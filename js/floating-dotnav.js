jQuery(document).ready(function($) {
    // Get header offset from localized script
	var headerHeight = parseInt(floatingDotNavSettings.headerOffset || 0, 10);
	//console.log('Header Height:', headerHeight, '| Type is:', typeof headerHeight);
	//var headerHeight = 80; // Adjust this according to your fixed header height
    
   // Create the floating menu and dot navigation
     $('body').append('<div id="floating_menu"></div>');
    $('.snap-section').each(function() {
        var $section = $(this); // Cache the jQuery object for efficiency
        var sectionId = $section.attr('id');

        // Skip this section if it doesn't have an ID, as we can't link to it.
        if (!sectionId) {
            return; // This is like 'continue' in a .each() loop
        }

        var tooltipText;
        var ariaLabel = $section.attr('aria-label');

        // Check if aria-label exists and is not empty.
        if (ariaLabel && ariaLabel.trim() !== '') {
            // If it exists, use it as the tooltip text directly.
            tooltipText = ariaLabel;
        } else {
            // Otherwise, fall back to the section ID and sanitize it.
            tooltipText = sectionId.replace(/-/g, ' ');
        }
        
        // Build the HTML for the dot and its tooltip, then append it.
        var dotHtml = '<a href="#' + sectionId + '" class="dots"><span class="tooltip">' + tooltipText + '</span></a>';
        $('#floating_menu').append(dotHtml);
    });

    // Update active menu item on scroll with offset
    var offset = headerHeight; // Adjust as needed
    var timeout;

    function handleScroll() {
        //var scrollPos = jQuery(window).scrollTop() + offset;
        var scrollPos = jQuery(window).scrollTop() + headerHeight; // Add header height to scroll position

        var activeSection;

        jQuery('.snap-section').each(function() {
            var offsetTop = jQuery(this).offset().top;
            var sectionId = jQuery(this).attr('id');
            //if (scrollPos >= offsetTop && scrollPos < offsetTop + jQuery(this).height()) {
            if (scrollPos >= offsetTop - offset && scrollPos < offsetTop + jQuery(this).height() - headerHeight) {

                activeSection = sectionId;
                return false; // Break loop once active section found
            }
        });

        // Update active class based on activeSection
        if (activeSection) {
            jQuery('#floating_menu a').removeClass('active');
            jQuery('#floating_menu a[href="#' + activeSection + '"]').addClass('active');
        } else {
            // No section in view, remove "active" class
            jQuery('#floating_menu a').removeClass('active');
        }

        // Show/hide menu based on scroll position
        if (scrollPos <= offset) {
            jQuery('#floating_menu').addClass("hidetop");
        } else {
            jQuery('#floating_menu').removeClass("hidetop");
        }
    }

    jQuery(window).scroll(handleScroll); // Bind scroll event handler

    // Smooth scrolling for menu links
    jQuery('#floating_menu a').on('click', function(event) {
        event.preventDefault();

        var targetSectionId = jQuery(this).attr('href');
        var targetOffset = jQuery(targetSectionId).offset().top - headerHeight;

            jQuery('html, body').animate({
                scrollTop: targetOffset
            }, 500); // Adjust duration as needed
        });

        // Trigger initial scroll event immediately
        handleScroll(); // Call to ensure active class on page load
        // Create the hide button
        var hideButton = $('<button class="hide-button"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>');

        // Append the hide button to the floating_menu element
        $('#floating_menu').append(hideButton);

        // Toggle the "hidenav" class when the button is clicked
        hideButton.click(function() {
            $('#floating_menu').toggleClass('hidenav');
            $(this).toggleClass('active');
        });
});