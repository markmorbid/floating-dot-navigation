(function($) {
    'use strict';
    
    // Check if floatingDotNavCustomizer is defined
    if (typeof floatingDotNavCustomizer === 'undefined') {
        console.error('Floating DotNav Customizer: Localized script data not found!');
        return;
    }
    
    // Wait for both jQuery and Customizer to be ready
    $(document).ready(function() {
        // Use multiple approaches to ensure handlers are bound
        if (typeof wp !== 'undefined' && wp.customize) {
            wp.customize.bind('ready', function() {
                initCustomizerHandlers();
            });
        } else {
            // Fallback: wait a bit and then initialize
            setTimeout(function() {
                initCustomizerHandlers();
            }, 500);
        }
        
        // Also try to bind immediately (in case control is already rendered)
        setTimeout(function() {
            initCustomizerHandlers();
        }, 100);
        
        // Also try after a longer delay to catch dynamically loaded controls
        setTimeout(function() {
            initCustomizerHandlers();
        }, 1000);
    });
    
    /**
     * Initialize all Customizer handlers
     */
    function initCustomizerHandlers() {
        bindSettingsHandlers();
        addToggleCheckboxes();
        handleSelectedPagesVisibility();
        
        // Auto-calculate text color when main color changes
        if (typeof wp !== 'undefined' && wp.customize) {
            wp.customize('floating_dotnav_maincolor', function(setting) {
                if (setting) {
                    setting.bind(function(value) {
                        if (value && !wp.customize('floating_dotnav_maincolortext').get()) {
                            var contrastColor = calculateContrastColor(value);
                            wp.customize('floating_dotnav_maincolortext').set(contrastColor);
                        }
                    });
                }
            });
            
            // Handle Selected Pages visibility
            wp.customize('floating_dotnav_display', function(setting) {
                if (setting) {
                    setting.bind(function(value) {
                        handleSelectedPagesVisibility();
                    });
                }
            });
        }
    }
    
    /**
     * Add toggle checkboxes to control titles
     */
    function addToggleCheckboxes() {
        // Excluded control IDs (exact matches)
        var excludedIds = [
            'customize-control-floating_dotnav_display',
            'customize-control-floating_dotnav_header_offset',
            'customize-control-floating_dotnav_settings_management',
            'customize-control-floating_dotnav_custom_css',
            'customize-control-floating_dotnav_pages'
        ];
        
        // Find all customize controls in our section
        $('#sub-accordion-section-floating_dotnav_section .customize-control').each(function() {
            var $control = $(this);
            var controlId = $control.attr('id');
            
            // Skip excluded controls
            if (excludedIds.indexOf(controlId) !== -1) {
                return;
            }
            
            // Check if checkbox already exists
            var $title = $control.find('.customize-control-title');
            if ($title.length && !$title.find('input[type="checkbox"]').length) {
                // Check if control has a value (to determine initial state)
                var hasValue = false;
                var $input = $control.find('input, select, textarea').not('input[type="checkbox"]').first();
                
                if ($input.length) {
                    var inputValue = $input.val();
                    // Check if it's a meaningful value (not empty, not default)
                    if (inputValue && 
                        inputValue !== '' && 
                        inputValue !== '0' && 
                        inputValue !== 'inherit' && 
                        inputValue !== 'var(--titlefont, inherit)' &&
                        inputValue !== 'solid' && // border style default
                        inputValue !== 'none' && // trail default when not set
                        inputValue !== '100px') { // border radius default
                        hasValue = true;
                    }
                    
                    // For color controls, check if it's not the default
                    if ($control.find('.wp-color-picker').length) {
                        var colorValue = $input.val();
                        if (colorValue && colorValue !== '' && colorValue !== '#ffffff' && colorValue !== '#2b2b2b' && colorValue !== '#0f0f0f') {
                            hasValue = true;
                        }
                    }
                }
                
                // Create checkbox
                var $checkbox = $('<input type="checkbox" name="checkbox" value="yes">');
                
                // Set initial state based on whether control has value
                if (hasValue) {
                    $checkbox.prop('checked', true);
                } else {
                    // Add class to hide content if no value
                    $control.addClass('floating-dotnav-content-hidden');
                }
                
                // Prepend checkbox to title (inside the label if it exists)
                if ($title.is('label')) {
                    $title.prepend($checkbox);
                } else {
                    $title.prepend($checkbox);
                }
                
                // Handle checkbox change - toggle class on container
                $checkbox.on('change', function() {
                    var isChecked = $(this).is(':checked');
                    if (isChecked) {
                        $control.removeClass('floating-dotnav-content-hidden');
                    } else {
                        $control.addClass('floating-dotnav-content-hidden');
                    }
                });
            }
        });
    }
    
    /**
     * Handle Selected Pages visibility based on Display Location
     */
    function handleSelectedPagesVisibility() {
        if (typeof wp !== 'undefined' && wp.customize) {
            var displayValue = wp.customize('floating_dotnav_display').get();
            var $pagesControl = $('#customize-control-floating_dotnav_pages');
            
            if (displayValue === 'selected_pages') {
                $pagesControl.show();
            } else {
                $pagesControl.hide();
            }
        } else {
            // Fallback: check the select value directly
            var $displaySelect = $('#_customize-input-floating_dotnav_display');
            var $pagesControl = $('#customize-control-floating_dotnav_pages');
            
            if ($displaySelect.length) {
                $displaySelect.on('change', function() {
                    if ($(this).val() === 'selected_pages') {
                        $pagesControl.show();
                    } else {
                        $pagesControl.hide();
                    }
                });
                
                // Set initial state
                if ($displaySelect.val() === 'selected_pages') {
                    $pagesControl.show();
                } else {
                    $pagesControl.hide();
                }
            }
        }
    }
    
    /**
     * Bind handlers for export/import/reset buttons
     */
    function bindSettingsHandlers() {
        // Remove any existing handlers first
        $(document).off('click', '#floating-dotnav-export-btn');
        $(document).off('click', '#floating-dotnav-import-btn');
        $(document).off('change', '#floating-dotnav-file-input');
        $(document).off('click', '#floating-dotnav-reset-btn');
        
        // Bind export button
        $(document).on('click', '#floating-dotnav-export-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Export button clicked');
            exportSettings();
            return false;
        });
        
        // Bind import button
        $(document).on('click', '#floating-dotnav-import-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Import button clicked');
            $('#floating-dotnav-import-wrapper').toggle();
            if ($('#floating-dotnav-import-wrapper').is(':visible')) {
                setTimeout(function() {
                    $('#floating-dotnav-file-input').focus();
                }, 100);
            }
            return false;
        });
        
        // Bind file input change
        $(document).on('change', '#floating-dotnav-file-input', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var file = e.target.files[0];
            if (file) {
                console.log('File selected:', file.name);
                importSettings(file);
            }
            return false;
        });
        
        // Bind reset button
        $(document).on('click', '#floating-dotnav-reset-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Reset button clicked');
            if (confirm('Are you sure you want to reset all settings to defaults? This cannot be undone.')) {
                resetSettings();
            }
            return false;
        });
        
        // Also try direct binding if elements exist
        var $exportBtn = $('#floating-dotnav-export-btn');
        var $importBtn = $('#floating-dotnav-import-btn');
        var $resetBtn = $('#floating-dotnav-reset-btn');
        var $fileInput = $('#floating-dotnav-file-input');
        
        if ($exportBtn.length) {
            $exportBtn.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Export button clicked (direct)');
                exportSettings();
                return false;
            });
        }
        
        if ($importBtn.length) {
            $importBtn.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Import button clicked (direct)');
                $('#floating-dotnav-import-wrapper').toggle();
                return false;
            });
        }
        
        if ($resetBtn.length) {
            $resetBtn.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Reset button clicked (direct)');
                if (confirm('Are you sure you want to reset all settings to defaults? This cannot be undone.')) {
                    resetSettings();
                }
                return false;
            });
        }
        
        if ($fileInput.length) {
            $fileInput.off('change').on('change', function(e) {
                var file = e.target.files[0];
                if (file) {
                    console.log('File selected (direct):', file.name);
                    importSettings(file);
                }
            });
        }
    }
    
    /**
     * Export settings
     */
    function exportSettings() {
        console.log('Exporting settings...');
        console.log('AJAX URL:', floatingDotNavCustomizer.ajaxUrl);
        console.log('Nonce:', floatingDotNavCustomizer.nonce);
        
        $.ajax({
            url: floatingDotNavCustomizer.ajaxUrl,
            type: 'POST',
            data: {
                action: 'floating_dotnav_export',
                nonce: floatingDotNavCustomizer.nonce
            },
            success: function(response) {
                console.log('Export response:', response);
                if (response.success) {
                    // Create download
                    var dataStr = JSON.stringify(response.data.settings, null, 2);
                    var dataBlob = new Blob([dataStr], {type: 'text/plain'});
                    var url = URL.createObjectURL(dataBlob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = response.data.filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                    
                    // Show success message
                    showNotice(floatingDotNavCustomizer.exportSuccess, 'success');
                } else {
                    console.error('Export failed:', response.data);
                    showNotice(response.data.message || floatingDotNavCustomizer.error, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Export AJAX error:', status, error);
                console.error('Response:', xhr.responseText);
                showNotice(floatingDotNavCustomizer.error + ' (' + error + ')', 'error');
            }
        });
    }
    
    /**
     * Import settings from file
     */
    function importSettings(file) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            try {
                var settings = JSON.parse(e.target.result);
                
                $.ajax({
                    url: floatingDotNavCustomizer.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'floating_dotnav_import',
                        nonce: floatingDotNavCustomizer.nonce,
                        settings: JSON.stringify(settings)
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotice(floatingDotNavCustomizer.importSuccess, 'success');
                            // Reload Customizer to reflect changes
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            showNotice(response.data.message || floatingDotNavCustomizer.error, 'error');
                        }
                    },
                    error: function() {
                        showNotice(floatingDotNavCustomizer.error, 'error');
                    }
                });
            } catch (error) {
                showNotice('Invalid file format. Please select a valid settings file.', 'error');
            }
        };
        
        reader.readAsText(file);
    }
    
    /**
     * Reset settings to defaults
     */
    function resetSettings() {
        console.log('Resetting settings...');
        $.ajax({
            url: floatingDotNavCustomizer.ajaxUrl,
            type: 'POST',
            data: {
                action: 'floating_dotnav_reset',
                nonce: floatingDotNavCustomizer.nonce
            },
            success: function(response) {
                console.log('Reset response:', response);
                if (response.success) {
                    showNotice(floatingDotNavCustomizer.resetSuccess, 'success');
                    // Reload Customizer to reflect changes
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    console.error('Reset failed:', response.data);
                    showNotice(response.data.message || floatingDotNavCustomizer.error, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Reset AJAX error:', status, error);
                console.error('Response:', xhr.responseText);
                showNotice(floatingDotNavCustomizer.error + ' (' + error + ')', 'error');
            }
        });
    }
    
    /**
     * Show notice message
     */
    function showNotice(message, type) {
        // Remove existing notices
        $('.floating-dotnav-notice').remove();
        
        var noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        var $notice = $('<div class="notice ' + noticeClass + ' is-dismissible floating-dotnav-notice" style="margin: 10px 0; padding: 10px;"><p>' + message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
        
        var $noticeArea = $('#floating-dotnav-notice-area');
        if ($noticeArea.length) {
            $noticeArea.html($notice);
        } else {
            // Fallback: try to find the control
            var $control = $('#customize-control-floating_dotnav_settings_management');
            if ($control.length) {
                $control.find('.floating-dotnav-settings-control').append($notice);
            }
        }
        
        // Handle dismiss button
        $notice.find('.notice-dismiss').on('click', function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        });
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * Calculate contrast color (black or white)
     */
    function calculateContrastColor(hex) {
        // Remove # if present
        hex = hex.replace('#', '');
        
        // Convert to RGB
        var r = parseInt(hex.substr(0, 2), 16);
        var g = parseInt(hex.substr(2, 2), 16);
        var b = parseInt(hex.substr(4, 2), 16);
        
        // Calculate relative luminance
        var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        
        // Return black for light colors, white for dark colors
        return luminance > 0.5 ? '#000000' : '#ffffff';
    }
    
})(jQuery);
