<?php
/**
 * Plugin Name: Floating Dot Navigation
 * Plugin URI: https://satoshisea.io/
 * Description: Automated dot navigation that responds to page sections with class '.snap-section'
 * Version: 1.9.0
 * Requires at least: 6.9
 * Tested up to: 6.9
 * Author: Marcos Ribero
 * Author URI: https://satoshisea.io/
 * License: GPL2
 */


// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

define('FLOATING_DOTNAV_VERSION', '1.9.0');


class Floating_Dot_Navigation {
    
    public function __construct() {
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Customizer hooks
        add_action('customize_register', array($this, 'customize_register'));
        add_action('wp_head', array($this, 'customizer_css_output'), 999);
        add_action('customize_controls_enqueue_scripts', array($this, 'enqueue_customizer_assets'));
        
        // AJAX handlers for export/import/reset
        add_action('wp_ajax_floating_dotnav_export', array($this, 'ajax_export_settings'));
        add_action('wp_ajax_floating_dotnav_import', array($this, 'ajax_import_settings'));
        add_action('wp_ajax_floating_dotnav_reset', array($this, 'ajax_reset_settings'));
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Check if should display
        if (!$this->should_display()) {
            return;
        }

        $version = FLOATING_DOTNAV_VERSION;

        // Enqueue CSS
        wp_enqueue_style(
            'floating-dotnav-css',
            plugin_dir_url(__FILE__) . 'css/floating-dotnav.css',
            array(),
            $version
        );
        
        // Enqueue jQuery
        wp_enqueue_script('jquery');
        
        // Enqueue JS
        wp_enqueue_script(
            'floating-dotnav-js',
            plugin_dir_url(__FILE__) . 'js/floating-dotnav.js',
            array('jquery'),
            $version,
            true
        );
        
        // Pass header offset to JavaScript
        $header_offset = get_theme_mod('floating_dotnav_header_offset', 0);
        wp_localize_script('floating-dotnav-js', 'floatingDotNavSettings', array(
            'headerOffset' => intval($header_offset)
        ));
    }

   /**
     * Enqueue scripts and styles for the Customizer.
     */
    public function enqueue_customizer_assets() {
        // Enqueue our customizer JS
        wp_enqueue_script(
            'floating-dotnav-customizer-js',
            plugin_dir_url(__FILE__) . 'js/floating-dotnav-customizer.js',
            array('jquery', 'customize-controls'),
            FLOATING_DOTNAV_VERSION,
            true // Load in footer
        );
        
        // Localize script for AJAX - must be called after wp_enqueue_script
        wp_localize_script('floating-dotnav-customizer-js', 'floatingDotNavCustomizer', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('floating_dotnav_nonce'),
            'exportSuccess' => __('Settings exported successfully!', 'floating-dotnav'),
            'importSuccess' => __('Settings imported successfully!', 'floating-dotnav'),
            'resetSuccess' => __('Settings reset to defaults!', 'floating-dotnav'),
            'error' => __('An error occurred. Please try again.', 'floating-dotnav'),
        ));

        // Add the CSS inline
        $customizer_css = "
            .floating-dotnav-section-group {
                margin: 5px 0;
                padding: 15px;
                background: #fff;
                border: 1px solid #cdcdcd;
                border-radius: 6px;
                margin-top: 10px;
            }
            #sub-accordion-section-floating_dotnav_section select {
                min-height: 40px;
                max-width: unset;
        
            }
            #sub-accordion-section-floating_dotnav_section{
                input[type=date], input[type=datetime-local], input[type=datetime], input[type=email], input[type=month], input[type=number], input[type=password], input[type=search], input[type=tel], input[type=text], input[type=time], input[type=url], input[type=week] {
                    padding: 0 12px;
                    line-height: 2;
                    min-height: 40px;
                }
            }
            #sub-accordion-section-floating_dotnav_section .customize-control {
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                width: -webkit-fill-available;
                float: none;
                display: flex;
                flex-direction: column;
                flex-wrap: nowrap;
                align-items: stretch;
                justify-content: space-between;
                border-radius: 6px;
                position: relative;
                margin: 0 0 5px 0;
            }
           #sub-accordion-section-floating_dotnav_section .wp-picker-container .wp-color-result.button {
                min-height: 38px;
                margin: 0 0px 0px 0;
                padding: 0 0 0 40px;
                font-size: 12px;
                border-radius: 6px;
                line-height: 3.5;
                box-sizing: content-box;
                overflow: hidden;
            }
            #sub-accordion-section-floating_dotnav_section .wp-color-result-text {
                background: #f6f7f7;
                border-radius: 0 2px 2px 0;
                border-left: 1px solid #c3c4c7;
                color: #50575e;
                display: block;
                line-height: inherit;
                padding: 0 12px;
                text-align: center;
            }
            #sub-accordion-section-floating_dotnav_section .wp-picker-container input[type=text].wp-color-picker {
                    width: 6em;
                font-size: 13px;
                font-family: monospace;
                line-height: 3.5;
                margin: 0;
                padding: 0 8px;
                vertical-align: top;
                min-height: 44px;
                max-height: 44px;
                margin-left: 5px;
            }
            #sub-accordion-section-floating_dotnav_section .wp-customizer .wp-picker-input-wrap .button.wp-picker-clear, #sub-accordion-section-floating_dotnav_section .wp-customizer .wp-picker-input-wrap .button.wp-picker-default, #sub-accordion-section-floating_dotnav_section .wp-picker-input-wrap .button.wp-picker-clear, #sub-accordion-section-floating_dotnav_section .wp-picker-input-wrap .button.wp-picker-default {
                margin-left: 4px;
                padding: 0 10px;
                line-height: 3;
                min-height: 44px;
                max-height: 44px;
                font-size: 13px;
                border-radius: 5px;
                background: #575757;
                color: white;
                border-color: #575757;
            }
            #sub-accordion-section-floating_dotnav_section .wp-picker-container .iris-picker {
                border-radius: 5px;
                border-color: #bfbfbf;
                margin-top: 6px;
            }
            #sub-accordion-section-floating_dotnav_section .customize-control-title {
                display: block;
                font-size: 14px;
                line-height: 1.75;
                font-weight: 700;
                margin-bottom: 0px;
            }
            #customize-theme-controls ul#sub-accordion-section-floating_dotnav_section {
                padding: 12px;
                border-right: 1px solid #ccc;
            }
            .customize-control-textarea textarea#_customize-input-floating_dotnav_custom_css:focus-within {
                min-height: 60vh;
            }

            #customize-theme-controls ul#sub-accordion-section-floating_dotnav_section:has(textarea#_customize-input-floating_dotnav_custom_css:focus-within, textarea#_customize-input-floating_dotnav_custom_css:focus-visible, textarea#_customize-input-floating_dotnav_custom_css:focus, textarea#_customize-input-floating_dotnav_custom_css:active) .customize-control:not(:has(textarea#_customize-input-floating_dotnav_custom_css)) {
            pointer-events: none;
                opacity: 0.6;
                filter: grayscale(1);
            }
            .floating-dotnav-section-group .customize-control-title {
                margin-top: 0;
                font-weight: 600;
            }
            .floating-dotnav-actions {
                display: flex;
                gap: 6px!important;
                margin: 0!important;
                flex-wrap: wrap;
            }
            .floating-dotnav-actions button {
                padding: 6px 12px!important;
                cursor: pointer;
                flex: 1;
                border-radius: 5px!important;
            }
            .floating-dotnav-actions #floating-dotnav-reset-btn {
                color: white!important;
                border-color: #b32d2e;
                background: #b32d2e;
                vertical-align: top;
            }
            .floating-dotnav-import-wrapper {
                margin-top: 10px;
            }
            .floating-dotnav-import-wrapper input[type='file'] {
                margin-top: 5px;
            }
            
            /* Toggle functionality for controls */
            #sub-accordion-section-floating_dotnav_section .customize-control:not(:has(textarea#_customize-input-floating_dotnav_custom_css)):not(#customize-control-floating_dotnav_display):not(#customize-control-floating_dotnav_header_offset):not(#customize-control-floating_dotnav_settings_management):not(#customize-control-floating_dotnav_pages) *:not(.floating-dotnav-settings-control, .floating-dotnav-settings-control *, .customize-control-title, .customize-control-title *, label, label *) {
                display: none;
            }
            
            #sub-accordion-section-floating_dotnav_section .customize-control:has(input[type=\"checkbox\"]:checked) *:not(.floating-dotnav-settings-control, .floating-dotnav-settings-control *, .customize-control-title, .customize-control-title *, label, label *) {
                display: flex!important;
                flex-direction: row;
                flex-wrap: wrap;
            }
            
            /* Hide content container using class */
            #sub-accordion-section-floating_dotnav_section .customize-control.floating-dotnav-content-hidden > *:not(.customize-control-title, .customize-control-title *) {
                display: none!important;
            }
            
            #sub-accordion-section-floating_dotnav_section .customize-control-title input[type=\"checkbox\"] {
                margin-right: 8px;
                vertical-align: middle;
            }
            
            /* Selected Pages conditional visibility - handled by JS */
            #customize-control-floating_dotnav_pages {
                display: none;
            }
        ";
        
        wp_register_style('floating-dotnav-customizer-styles', false);
        wp_enqueue_style('floating-dotnav-customizer-styles');
        wp_add_inline_style('floating-dotnav-customizer-styles', $customizer_css);
    }
    /**
     * Get theme color value from theme mods or CSS variables
     */
    private function get_theme_color($theme_key, $css_var, $default) {
        // Try theme mod first
        $theme_mod = get_theme_mod($theme_key, '');
        if (!empty($theme_mod)) {
            return $theme_mod;
        }
        
        // Try CSS variable
        if (!empty($css_var)) {
            return 'var(' . $css_var . ', ' . $default . ')';
        }
        
        return $default;
    }
    
    /**
     * Get theme font value
     */
    private function get_theme_font($theme_key, $css_var, $default) {
        $theme_mod = get_theme_mod($theme_key, '');
        if (!empty($theme_mod)) {
            return $theme_mod;
        }
        
        if (!empty($css_var)) {
            return 'var(' . $css_var . ', ' . $default . ')';
        }
        
        return $default;
    }
    
    /**
     * Get theme CSS variable value suggestions for Customizer
     * This provides hints about available theme variables
     */
    public function get_theme_variable_suggestions() {
        // Common theme CSS variables that might be available
        $suggestions = array(
            '--maincolor' => __('Theme Primary Color', 'floating-dotnav'),
            '--altcolor' => __('Theme Secondary Color', 'floating-dotnav'),
            '--titlefont' => __('Theme Title Font', 'floating-dotnav'),
            '--bodyfont' => __('Theme Body Font', 'floating-dotnav'),
            '--border-radius' => __('Theme Border Radius', 'floating-dotnav'),
            '--btn-border-radius' => __('Theme Button Border Radius', 'floating-dotnav'),
            '--borders-color' => __('Theme Border Color', 'floating-dotnav'),
            '--lightbg2' => __('Theme Light Background', 'floating-dotnav'),
            '--maincolortext' => __('Theme Primary Text Color', 'floating-dotnav'),
            '--wp-admin-theme-color' => __('WordPress Admin Theme Color', 'floating-dotnav'),
        );
        
        return $suggestions;
    }
    
    /**
     * Calculate contrast color (black or white) for text readability
     */
    private function get_contrast_color($hex_color) {
        // Remove # if present
        $hex_color = ltrim($hex_color, '#');
        
        // Convert to RGB
        $r = hexdec(substr($hex_color, 0, 2));
        $g = hexdec(substr($hex_color, 2, 2));
        $b = hexdec(substr($hex_color, 4, 2));
        
        // Calculate relative luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        // Return black for light colors, white for dark colors
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
    
    /**
     * Check if should display on current page
     */
    private function should_display() {
        $display_location = get_theme_mod('floating_dotnav_display', 'homepage');
        
        if ($display_location === 'all') {
            return true;
        }
        
        if ($display_location === 'homepage' && is_front_page()) {
            return true;
        }
        
        if ($display_location === 'selected_pages') {
            $selected_pages = get_theme_mod('floating_dotnav_pages', '');
            if (empty($selected_pages)) {
                return false;
            }
            $page_ids = array_map('trim', explode(',', $selected_pages));
            return is_page($page_ids);
        }
        
        return false;
    }
    
    /**
     * Register Customizer settings
     */
    public function customize_register($wp_customize) {
        // Add Section
        $wp_customize->add_section('floating_dotnav_section', array(
            'title' => __('Floating Dot Navigation', 'floating-dotnav'),
            'priority' => 160,
        ));
        
        // ========== DISPLAY SETTINGS ==========
        // Display Location
        $wp_customize->add_setting('floating_dotnav_display', array(
            'default' => 'homepage',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_display', array(
            'label' => __('Display Location', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'select',
            'choices' => array(
                'homepage' => __('Homepage Only', 'floating-dotnav'),
                'selected_pages' => __('Selected Pages', 'floating-dotnav'),
                'all' => __('All Pages', 'floating-dotnav'),
            ),
        ));
        
        // Selected Pages
        $wp_customize->add_setting('floating_dotnav_pages', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_pages', array(
            'label' => __('Selected Page IDs', 'floating-dotnav'),
            'description' => __('Enter page IDs separated by commas (e.g., 10, 25, 42). Only used when "Selected Pages" is chosen.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // Header Offset
        $wp_customize->add_setting('floating_dotnav_header_offset', array(
            'default' => 0,
            'sanitize_callback' => 'absint',
        ));
        
        $wp_customize->add_control('floating_dotnav_header_offset', array(
            'label' => __('Header Offset (px)', 'floating-dotnav'),
            'description' => __('If your page has a fixed header, enter its height in pixels so that the scrolling is accurate. Leave it at 0 if you do not have a fixed header.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 0,
                'step' => 1,
            ),
        ));
        
        // ========== TYPOGRAPHY ==========
        // Font Family
        $wp_customize->add_setting('floating_dotnav_font', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $font_desc = __('Enter font family (e.g., "Arial", "Roboto", or CSS variable like "var(--titlefont)"). Leave empty to use theme default.', 'floating-dotnav');
        $theme_vars = $this->get_theme_variable_suggestions();
        if (isset($theme_vars['--titlefont'])) {
            $font_desc .= ' ' . sprintf(__('Theme variable available: %s', 'floating-dotnav'), '<code>var(--titlefont)</code>');
        }
        
        $wp_customize->add_control('floating_dotnav_font', array(
            'label' => __('Font Family', 'floating-dotnav'),
            'description' => $font_desc,
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // Font Size (Tooltip)
        $wp_customize->add_setting('floating_dotnav_size_font', array(
            'default' => '16px',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_size_font', array(
            'label' => __('Tooltip Font Size', 'floating-dotnav'),
            'description' => __('Font size for tooltip text (e.g., 16px, 1rem).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // ========== SIZES ==========
        // Dot Size
        $wp_customize->add_setting('floating_dotnav_size', array(
            'default' => '33px',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_size', array(
            'label' => __('Dot Size', 'floating-dotnav'),
            'description' => __('Size of the navigation dots (e.g., 33px, 2rem).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // Arrow Width
        $wp_customize->add_setting('floating_dotnav_arrow_width', array(
            'default' => '6px',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_arrow_width', array(
            'label' => __('Tooltip Arrow Width', 'floating-dotnav'),
            'description' => __('Width of the tooltip arrow triangle (e.g., 6px).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // Arrow Border
        $wp_customize->add_setting('floating_dotnav_arrow_border', array(
            'default' => '6px',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_arrow_border', array(
            'label' => __('Tooltip Arrow Border', 'floating-dotnav'),
            'description' => __('Border width for tooltip arrow (should match arrow width if tooltip has border).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // ========== SPACING ==========
        // Space Between Dots
        $wp_customize->add_setting('floating_dotnav_space', array(
            'default' => '7px',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_space', array(
            'label' => __('Space Between Dots', 'floating-dotnav'),
            'description' => __('Vertical spacing between navigation dots (e.g., 7px).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // Tooltip Padding
        $wp_customize->add_setting('floating_dotnav_padding', array(
            'default' => '12px',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_padding', array(
            'label' => __('Tooltip Padding', 'floating-dotnav'),
            'description' => __('Internal padding for tooltips (e.g., 12px).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // ========== COLORS ==========
        // Main Color
        $wp_customize->add_setting('floating_dotnav_maincolor', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        
        $maincolor_desc = __('Primary accent color for active dots and tooltips. Leave empty to use theme color.', 'floating-dotnav');
        $theme_vars = $this->get_theme_variable_suggestions();
        if (isset($theme_vars['--maincolor'])) {
            $maincolor_desc .= ' ' . sprintf(__('Theme variable available: %s', 'floating-dotnav'), '<code>var(--maincolor)</code>');
        }
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'floating_dotnav_maincolor', array(
            'label' => __('Main Accent Color', 'floating-dotnav'),
            'description' => $maincolor_desc,
            'section' => 'floating_dotnav_section',
        )));
        
        // Main Color Text (Auto-calculated contrast)
        $wp_customize->add_setting('floating_dotnav_maincolortext', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'floating_dotnav_maincolortext', array(
            'label' => __('Main Color Text', 'floating-dotnav'),
            'description' => __('Text color for tooltips. Auto-calculated for contrast if left empty.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
        )));
        
        // Alternative Color
        $wp_customize->add_setting('floating_dotnav_altcolor', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'floating_dotnav_altcolor', array(
            'label' => __('Alternative Color', 'floating-dotnav'),
            'description' => __('Alternative accent color. Uses main color if not set.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
        )));
        
        // Border Color
        $wp_customize->add_setting('floating_dotnav_border_color', array(
            'default' => '#2b2b2b',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'floating_dotnav_border_color', array(
            'label' => __('Border Color', 'floating-dotnav'),
            'description' => __('Color for borders on dots and tooltips.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
        )));
        
        // Light Background
        $wp_customize->add_setting('floating_dotnav_lightbg', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'floating_dotnav_lightbg', array(
            'label' => __('Light Background', 'floating-dotnav'),
            'description' => __('Light background color for general use.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
        )));
        
        // Dark Background
        $wp_customize->add_setting('floating_dotnav_darkbg', array(
            'default' => '#0f0f0f',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'floating_dotnav_darkbg', array(
            'label' => __('Dark Background', 'floating-dotnav'),
            'description' => __('Dark background color for dots and tooltips.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
        )));
        
        // Outline Color
        $wp_customize->add_setting('floating_dotnav_outline_color', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'floating_dotnav_outline_color', array(
            'label' => __('Outline Color (Active)', 'floating-dotnav'),
            'description' => __('Color for the inner outline on active dots. Uses main color if not set.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
        )));
        
        // Outline Color Hover
        $wp_customize->add_setting('floating_dotnav_outline_color_hover', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'floating_dotnav_outline_color_hover', array(
            'label' => __('Outline Color (Hover)', 'floating-dotnav'),
            'description' => __('Color for the inner outline on hover state.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
        )));
        
        // ========== BORDERS & STYLING ==========
        // Border Width
        $wp_customize->add_setting('floating_dotnav_border_width', array(
            'default' => '1px',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_border_width', array(
            'label' => __('Border Width', 'floating-dotnav'),
            'description' => __('Width of borders on dots and tooltips (e.g., 1px, 2px).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // Border Style
        $wp_customize->add_setting('floating_dotnav_border_style', array(
            'default' => 'solid',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_border_style', array(
            'label' => __('Border Style', 'floating-dotnav'),
            'description' => __('Border style (solid, dashed, dotted, none).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'select',
            'choices' => array(
                'solid' => __('Solid', 'floating-dotnav'),
                'dashed' => __('Dashed', 'floating-dotnav'),
                'dotted' => __('Dotted', 'floating-dotnav'),
                'none' => __('None', 'floating-dotnav'),
            ),
        ));
        
        // Border Radius
        $wp_customize->add_setting('floating_dotnav_border_radius', array(
            'default' => '100px',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $radius_desc = __('Border radius for dots (e.g., 100px for round, 5px for slight rounding, 0 for square).', 'floating-dotnav');
        $theme_vars = $this->get_theme_variable_suggestions();
        if (isset($theme_vars['--btn-border-radius'])) {
            $radius_desc .= ' ' . sprintf(__('Theme variable available: %s', 'floating-dotnav'), '<code>var(--btn-border-radius)</code>');
        }
        
        $wp_customize->add_control('floating_dotnav_border_radius', array(
            'label' => __('Border Radius (Dots)', 'floating-dotnav'),
            'description' => $radius_desc,
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // Tooltip Border Radius
        $wp_customize->add_setting('floating_dotnav_tooltip_border_radius', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_tooltip_border_radius', array(
            'label' => __('Tooltip Border Radius', 'floating-dotnav'),
            'description' => __('Border radius for tooltips. Leave empty to use dot border radius if it\'s below 10px, otherwise uses 5px.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // ========== EFFECTS ==========
        // Outline Size
        $wp_customize->add_setting('floating_dotnav_outline_size', array(
            'default' => '3',
            'sanitize_callback' => 'absint',
        ));
        
        $wp_customize->add_control('floating_dotnav_outline_size', array(
            'label' => __('Outline Size', 'floating-dotnav'),
            'description' => __('Size of the inner outline effect on dots (0 to disable, 1-10 for effect intensity).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 10,
                'step' => 1,
            ),
        ));
        
        // Trail (Connecting Line)
        $wp_customize->add_setting('floating_dotnav_trail', array(
            'default' => 'solid',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_trail', array(
            'label' => __('Show Connecting Line', 'floating-dotnav'),
            'description' => __('Display a line connecting all dots.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'select',
            'choices' => array(
                'solid' => __('Yes', 'floating-dotnav'),
                'none' => __('No', 'floating-dotnav'),
            ),
        ));
        
        // Transition Time
        $wp_customize->add_setting('floating_dotnav_transition_time', array(
            'default' => '500ms',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('floating_dotnav_transition_time', array(
            'label' => __('Transition Time', 'floating-dotnav'),
            'description' => __('Animation transition duration (e.g., 500ms, 0.5s).', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'text',
        ));
        
        // ========== EXPORT/IMPORT/RESET ==========
        // Export/Import/Reset controls (custom control class)
        require_once plugin_dir_path(__FILE__) . 'includes/class-settings-control.php';
        
        $wp_customize->add_setting('floating_dotnav_settings_management', array(
            'default' => '',
            'sanitize_callback' => '__return_empty_string',
        ));
        
        $wp_customize->add_control(new Floating_DotNav_Settings_Control($wp_customize, 'floating_dotnav_settings_management', array(
            'label' => __('Settings Management', 'floating-dotnav'),
            'description' => __('Export your current settings, import from a file, or reset to defaults.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
        )));
        
        // Keep Custom CSS for advanced users
        $wp_customize->add_setting('floating_dotnav_custom_css', array(
            'default' => '',
            'sanitize_callback' => array( $this, 'sanitize_custom_css' ), 
        ));
        
        $wp_customize->add_control('floating_dotnav_custom_css', array(
            'label' => __('Custom CSS (Advanced)', 'floating-dotnav'),
            'description' => __('Add custom CSS to override any styles. This will be applied after all other settings.', 'floating-dotnav'),
            'section' => 'floating_dotnav_section',
            'type' => 'textarea',
            'input_attrs' => array(
                'placeholder' => ':root #floating_menu {
    --dotnav-maincolor: #ff0000;
    --dotnav-size: 35px;
}',
                'rows' => 8,
            ),
        ));
    }
    /**
     * Sanitizes custom CSS.
     *
     * @param string $css The CSS code to sanitize.
     * @return string The sanitized CSS code.
     */
    public function sanitize_custom_css( $css ) {
        // Use wp_kses with an empty array of allowed tags.
        // This will strip all HTML tags but keep the content and its formatting.
        return wp_kses( $css, array() );
    }
    
    /**
     * Output custom CSS in head
     */
    public function customizer_css_output() {
        if (!$this->should_display()) {
            return;
        }
        
        echo '<style type="text/css" id="floating-dotnav-custom-css">' . "\n";
        echo ':root #floating_menu {' . "\n";
        
        // Font
        $font = get_theme_mod('floating_dotnav_font', '');
        if (!empty($font)) {
            // If it's not already a CSS variable, wrap it in quotes if it contains spaces
            if (strpos($font, 'var(') === false && strpos($font, ',') !== false) {
                $font = '"' . $font . '"';
            }
            echo '    --dotnav-font: ' . esc_html($font) . ';' . "\n";
        } else {
            echo '    --dotnav-font: var(--titlefont, inherit);' . "\n";
        }
        
        // Sizes
        $size = get_theme_mod('floating_dotnav_size', '33px');
        if (!empty($size)) {
            echo '    --dotnav-size: ' . esc_html($size) . ';' . "\n";
        }
        
        $size_font = get_theme_mod('floating_dotnav_size_font', '16px');
        if (!empty($size_font)) {
            echo '    --dotnav-size-font: ' . esc_html($size_font) . ';' . "\n";
        }
        
        $arrow_width = get_theme_mod('floating_dotnav_arrow_width', '6px');
        if (!empty($arrow_width)) {
            echo '    --dotnav-arrow-width: ' . esc_html($arrow_width) . ';' . "\n";
        }
        
        $arrow_border = get_theme_mod('floating_dotnav_arrow_border', '6px');
        if (!empty($arrow_border)) {
            echo '    --dotnav-arrow-border: ' . esc_html($arrow_border) . ';' . "\n";
        }
        
        // Spacing
        $space = get_theme_mod('floating_dotnav_space', '7px');
        if (!empty($space)) {
            echo '    --dotnav-space: ' . esc_html($space) . ';' . "\n";
        }
        
        $padding = get_theme_mod('floating_dotnav_padding', '12px');
        if (!empty($padding)) {
            echo '    --dotnav-padding: ' . esc_html($padding) . ';' . "\n";
        }
        
        // Colors - Main Color
        $maincolor = get_theme_mod('floating_dotnav_maincolor', '');
        if (!empty($maincolor)) {
            echo '    --dotnav-maincolor: ' . esc_html($maincolor) . ';' . "\n";
            
            // Auto-calculate text color if not set
            $maincolortext = get_theme_mod('floating_dotnav_maincolortext', '');
            if (empty($maincolortext)) {
                $maincolortext = $this->get_contrast_color($maincolor);
            }
            echo '    --dotnav-maincolortext: ' . esc_html($maincolortext) . ';' . "\n";
        } else {
            // Use theme fallback
            echo '    --dotnav-maincolor: var(--maincolor, var(--wp-admin-theme-color));' . "\n";
            $maincolortext = get_theme_mod('floating_dotnav_maincolortext', '');
            if (!empty($maincolortext)) {
                echo '    --dotnav-maincolortext: ' . esc_html($maincolortext) . ';' . "\n";
            } else {
                echo '    --dotnav-maincolortext: var(--maincolortext, black);' . "\n";
            }
        }
        
        // Alternative Color
        $altcolor = get_theme_mod('floating_dotnav_altcolor', '');
        if (!empty($altcolor)) {
            echo '    --dotnav-altcolor: ' . esc_html($altcolor) . ';' . "\n";
        } else {
            echo '    --dotnav-altcolor: var(--altcolor, var(--wp-adminbar-accent));' . "\n";
        }
        
        // Border Color
        $border_color = get_theme_mod('floating_dotnav_border_color', '#2b2b2b');
        if (!empty($border_color)) {
            echo '    --dotnav-border-color: ' . esc_html($border_color) . ';' . "\n";
        }
        
        // Background Colors
        $lightbg = get_theme_mod('floating_dotnav_lightbg', '#ffffff');
        if (!empty($lightbg)) {
            echo '    --dotnav-lightbg: ' . esc_html($lightbg) . ';' . "\n";
        }
        
        $darkbg = get_theme_mod('floating_dotnav_darkbg', '#0f0f0f');
        if (!empty($darkbg)) {
            echo '    --dotnav-darkbg: ' . esc_html($darkbg) . ';' . "\n";
        }
        
        // Outline Colors
        $outline_color = get_theme_mod('floating_dotnav_outline_color', '');
        if (!empty($outline_color)) {
            echo '    --dotnav-outline-color: ' . esc_html($outline_color) . ';' . "\n";
        } else {
            // Use main color if available
            if (!empty($maincolor)) {
                echo '    --dotnav-outline-color: ' . esc_html($maincolor) . ';' . "\n";
            } else {
                echo '    --dotnav-outline-color: var(--dotnav-maincolor, var(--wp-admin-theme-color));' . "\n";
            }
        }
        
        $outline_color_hover = get_theme_mod('floating_dotnav_outline_color_hover', '#ffffff');
        if (!empty($outline_color_hover)) {
            echo '    --dotnav-outline-color-hover: ' . esc_html($outline_color_hover) . ';' . "\n";
        }
        
        // Borders
        $border_width = get_theme_mod('floating_dotnav_border_width', '1px');
        if (!empty($border_width)) {
            echo '    --dotnav-border-width: ' . esc_html($border_width) . ';' . "\n";
        }
        
        $border_style = get_theme_mod('floating_dotnav_border_style', 'solid');
        if (!empty($border_style)) {
            echo '    --dotnav-border-style: ' . esc_html($border_style) . ';' . "\n";
            echo '    --dotnav-tooltip-border: ' . esc_html($border_style) . ';' . "\n";
        }
        
        // Border Radius
        $border_radius = get_theme_mod('floating_dotnav_border_radius', '100px');
        if (!empty($border_radius)) {
            echo '    --dotnav-border-radius: ' . esc_html($border_radius) . ';' . "\n";
        }
        
        $tooltip_border_radius = get_theme_mod('floating_dotnav_tooltip_border_radius', '');
        if (!empty($tooltip_border_radius)) {
            echo '    --dotnav-tooltip-border-radius: ' . esc_html($tooltip_border_radius) . ';' . "\n";
        } else {
            // Auto-calculate: if border radius is below 10px, use it, otherwise use 5px
            $radius_value = intval(preg_replace('/[^0-9]/', '', $border_radius));
            if ($radius_value < 10 && $radius_value > 0) {
                echo '    --dotnav-tooltip-border-radius: ' . esc_html($border_radius) . ';' . "\n";
            } else {
                echo '    --dotnav-tooltip-border-radius: var(--btn-border-radius, var(--border-radius), 5px);' . "\n";
            }
        }
        
        // Effects
        $outline_size = get_theme_mod('floating_dotnav_outline_size', '3');
        if ($outline_size !== '') {
            echo '    --dotnav-outline-size: ' . intval($outline_size) . ';' . "\n";
        }
        
        $trail = get_theme_mod('floating_dotnav_trail', 'solid');
        if (!empty($trail)) {
            echo '    --dotnav-trail: ' . esc_html($trail) . ';' . "\n";
        }
        
        $transition_time = get_theme_mod('floating_dotnav_transition_time', '500ms');
        if (!empty($transition_time)) {
            echo '    --dotnav-transition-time: ' . esc_html($transition_time) . ';' . "\n";
        }
        
        // Light background transparent (calculated from lightbg)
        $lightbg_transp = get_theme_mod('floating_dotnav_lightbg', '#ffffff');
        if (!empty($lightbg_transp)) {
            // Convert hex to rgba with 75% opacity
            $lightbg_transp = ltrim($lightbg_transp, '#');
            $r = hexdec(substr($lightbg_transp, 0, 2));
            $g = hexdec(substr($lightbg_transp, 2, 2));
            $b = hexdec(substr($lightbg_transp, 4, 2));
            echo '    --dotnav-lightbg-transp: rgba(' . $r . ', ' . $g . ', ' . $b . ', 0.75);' . "\n";
        }
        
        echo '}' . "\n";
        
        // Custom CSS (advanced users)
        $custom_css = get_theme_mod('floating_dotnav_custom_css', '');
        if (!empty($custom_css)) {
            echo "\n" . $custom_css . "\n";
        }
        
        echo '</style>' . "\n";
    }
    
    /**
     * AJAX handler for exporting settings
     */
    public function ajax_export_settings() {
        check_ajax_referer('floating_dotnav_nonce', 'nonce');
        
        if (!current_user_can('customize')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'floating-dotnav')));
        }
        
        $settings = array();
        $all_settings = array(
            'floating_dotnav_display',
            'floating_dotnav_pages',
            'floating_dotnav_header_offset',
            'floating_dotnav_font',
            'floating_dotnav_size_font',
            'floating_dotnav_size',
            'floating_dotnav_arrow_width',
            'floating_dotnav_arrow_border',
            'floating_dotnav_space',
            'floating_dotnav_padding',
            'floating_dotnav_maincolor',
            'floating_dotnav_maincolortext',
            'floating_dotnav_altcolor',
            'floating_dotnav_border_color',
            'floating_dotnav_lightbg',
            'floating_dotnav_darkbg',
            'floating_dotnav_outline_color',
            'floating_dotnav_outline_color_hover',
            'floating_dotnav_border_width',
            'floating_dotnav_border_style',
            'floating_dotnav_border_radius',
            'floating_dotnav_tooltip_border_radius',
            'floating_dotnav_outline_size',
            'floating_dotnav_trail',
            'floating_dotnav_transition_time',
            'floating_dotnav_custom_css',
        );
        
        foreach ($all_settings as $setting) {
            $value = get_theme_mod($setting, '');
            if ($value !== '') {
                $settings[$setting] = $value;
            }
        }
        
        wp_send_json_success(array(
            'settings' => $settings,
            'filename' => 'floating-dotnav-settings-' . date('Y-m-d') . '.txt'
        ));
    }
    
    /**
     * AJAX handler for importing settings
     */
    public function ajax_import_settings() {
        check_ajax_referer('floating_dotnav_nonce', 'nonce');
        
        if (!current_user_can('customize')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'floating-dotnav')));
        }
        
        $settings_json = isset($_POST['settings']) ? wp_unslash($_POST['settings']) : '';
        
        if (empty($settings_json)) {
            wp_send_json_error(array('message' => __('No settings data provided.', 'floating-dotnav')));
        }
        
        $settings = json_decode($settings_json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($settings)) {
            wp_send_json_error(array('message' => __('Invalid settings format.', 'floating-dotnav')));
        }
        
        $allowed_settings = array(
            'floating_dotnav_display',
            'floating_dotnav_pages',
            'floating_dotnav_header_offset',
            'floating_dotnav_font',
            'floating_dotnav_size_font',
            'floating_dotnav_size',
            'floating_dotnav_arrow_width',
            'floating_dotnav_arrow_border',
            'floating_dotnav_space',
            'floating_dotnav_padding',
            'floating_dotnav_maincolor',
            'floating_dotnav_maincolortext',
            'floating_dotnav_altcolor',
            'floating_dotnav_border_color',
            'floating_dotnav_lightbg',
            'floating_dotnav_darkbg',
            'floating_dotnav_outline_color',
            'floating_dotnav_outline_color_hover',
            'floating_dotnav_border_width',
            'floating_dotnav_border_style',
            'floating_dotnav_border_radius',
            'floating_dotnav_tooltip_border_radius',
            'floating_dotnav_outline_size',
            'floating_dotnav_trail',
            'floating_dotnav_transition_time',
            'floating_dotnav_custom_css',
        );
        
        foreach ($settings as $key => $value) {
            if (in_array($key, $allowed_settings)) {
                set_theme_mod($key, $value);
            }
        }
        
        wp_send_json_success(array('message' => __('Settings imported successfully!', 'floating-dotnav')));
    }
    
    /**
     * AJAX handler for resetting settings
     */
    public function ajax_reset_settings() {
        check_ajax_referer('floating_dotnav_nonce', 'nonce');
        
        if (!current_user_can('customize')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'floating-dotnav')));
        }
        
        $defaults = array(
            'floating_dotnav_display' => 'homepage',
            'floating_dotnav_pages' => '',
            'floating_dotnav_header_offset' => 0,
            'floating_dotnav_font' => '',
            'floating_dotnav_size_font' => '16px',
            'floating_dotnav_size' => '33px',
            'floating_dotnav_arrow_width' => '6px',
            'floating_dotnav_arrow_border' => '6px',
            'floating_dotnav_space' => '7px',
            'floating_dotnav_padding' => '12px',
            'floating_dotnav_maincolor' => '',
            'floating_dotnav_maincolortext' => '',
            'floating_dotnav_altcolor' => '',
            'floating_dotnav_border_color' => '#2b2b2b',
            'floating_dotnav_lightbg' => '#ffffff',
            'floating_dotnav_darkbg' => '#0f0f0f',
            'floating_dotnav_outline_color' => '',
            'floating_dotnav_outline_color_hover' => '#ffffff',
            'floating_dotnav_border_width' => '1px',
            'floating_dotnav_border_style' => 'solid',
            'floating_dotnav_border_radius' => '100px',
            'floating_dotnav_tooltip_border_radius' => '',
            'floating_dotnav_outline_size' => '3',
            'floating_dotnav_trail' => 'solid',
            'floating_dotnav_transition_time' => '500ms',
            'floating_dotnav_custom_css' => '',
        );
        
        foreach ($defaults as $key => $value) {
            set_theme_mod($key, $value);
        }
        
        wp_send_json_success(array('message' => __('Settings reset to defaults!', 'floating-dotnav')));
    }
}

// Initialize the plugin
new Floating_Dot_Navigation();