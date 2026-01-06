=== Floating Dot Navigation ===
Contributors: satoshisea
Tags: navigation, scroll, dots, sections, menu, one-page, floating, customizer, responsive
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automated floating dot navigation that automatically detects and navigates to page sections with class '.snap-section'

== Description ==

Floating Dot Navigation is an elegant, fully customizable floating navigation menu that automatically detects sections on your pages. Perfect for one-page websites, long-form content, or any site that needs smooth section navigation.

The plugin creates a beautiful vertical dot navigation menu that appears on the right side of your page. Each dot represents a section, and clicking it smoothly scrolls to that section. The active section is automatically highlighted as users scroll through your content.

**Key Features:**

* **Automatic Section Detection** - Automatically finds all sections with the class 'snap-section'
* **Fully Customizable** - Extensive customization options in WordPress Customizer
* **Smart Color System** - Auto-calculates text contrast for optimal readability
* **Theme Integration** - Seamlessly integrates with your theme's colors and CSS variables
* **Flexible Display Options** - Show on homepage only, selected pages, or all pages
* **Responsive Design** - Adapts beautifully to all screen sizes
* **Hide/Show Toggle** - Users can hide the navigation with a single click
* **Smooth Animations** - Polished transitions and hover effects
* **Export/Import Settings** - Save and restore your customizations
* **Tooltip Labels** - Shows section names on hover or when active

**Customization Options:**

Customize every aspect of your navigation through the WordPress Customizer:

* **Typography** - Font family and tooltip font size
* **Sizes** - Dot size, arrow width, and spacing
* **Colors** - Main accent, text, borders, backgrounds, and outline colors
* **Borders** - Width, style, and radius for dots and tooltips
* **Effects** - Outline size, connecting trail, and transition timing
* **Advanced CSS** - Full CSS override support for power users

**Theme Compatibility:**

The plugin intelligently uses your theme's CSS variables when available, including:
* Theme primary and secondary colors
* Theme fonts
* Theme border radius settings
* Theme border colors

If your theme doesn't provide these variables, the plugin uses sensible defaults that work with any theme.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/floating-dot-navigation` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to **Appearance > Customize > Floating Dot Navigation** to configure the plugin
4. Add the class `snap-section` to any HTML elements you want in the navigation
5. Make sure each section has a unique ID attribute

**Adding Sections:**

To add sections to your navigation, simply add the class `snap-section` to any HTML element and give it a unique ID. For example:

`<section id="about-us" class="snap-section">...</section>`

The plugin will automatically:
* Create a dot for each section
* Use the section's ID to generate the tooltip label (or use the `aria-label` attribute if present)
* Enable smooth scrolling to that section

== Frequently Asked Questions ==

= How do I add sections to the navigation? =

Simply add the class `snap-section` to any HTML element you want to appear in the navigation. Make sure each section has a unique ID attribute. You can also add an `aria-label` attribute to customize the tooltip text.

Example:
`<section id="about-us" class="snap-section" aria-label="About Us">...</section>`

= Where can I customize the navigation? =

All customization options are available in the WordPress Customizer. Go to **Appearance > Customize > Floating Dot Navigation**. You'll find comprehensive options for colors, sizes, spacing, fonts, borders, and more.

= Can I use my theme's colors? =

Yes! The plugin automatically detects and uses your theme's CSS variables when available. You can also manually enter CSS variables like `var(--maincolor)` or use the color pickers to set custom colors.

= Where does the navigation appear? =

You can choose to display it on:
* **Homepage Only** (default) - Only shows on your homepage
* **Selected Pages** - Enter specific page IDs separated by commas
* **All Pages** - Shows on every page of your site

= How do I customize the tooltip text? =

The plugin automatically generates tooltip text from the section ID (replacing hyphens with spaces). To customize it, add an `aria-label` attribute to your section:

`<section id="about-us" class="snap-section" aria-label="Learn About Us">...</section>`

= Can I export my settings? =

Yes! In the Customizer, you'll find Export, Import, and Reset buttons. You can export your settings to a file and import them later, or reset everything to defaults.

= Does it work with fixed headers? =

Yes! Enter your header height in pixels in the "Header Offset" setting. This ensures the navigation scrolls to the correct position, accounting for your fixed header.

= Is it mobile-friendly? =

Yes! The plugin is fully responsive and adapts to all screen sizes. On mobile devices, the navigation automatically adjusts spacing and sizes for optimal usability.

= Can I hide the navigation? =

Yes! Users can click the X button at the bottom of the navigation to hide it. The button remains visible so they can show it again.

== Screenshots ==

1. Floating dot navigation on the right side of a page
2. Customizer options showing all customization controls
3. Navigation with tooltips visible
4. Mobile responsive view

== Changelog ==

= 1.4.0 =
* Major update: Complete Customizer overhaul with comprehensive customization options
* Added controls for all CSS variables (colors, sizes, spacing, fonts, borders, effects)
* Implemented export/import/reset functionality for settings
* Added automatic text contrast calculation for optimal readability
* Enhanced theme integration with CSS variable detection
* Improved responsive design for mobile devices
* Added custom control class for better UI in Customizer
* Fixed header offset support for accurate scrolling
* Added tooltip customization via aria-label attribute
* Performance improvements and code optimization

= 1.3.9 =
* Previous version with basic customization

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.4.0 =
Major update with comprehensive Customizer controls. All your existing settings will be preserved. New customization options are available in Appearance > Customize > Floating Dot Navigation.

= 1.0.0 =
Initial release of Floating Dot Navigation. Install and activate to get started!
