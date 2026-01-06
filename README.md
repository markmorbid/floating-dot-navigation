# Floating Dot Navigation

![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/License-GPLv2-green.svg)

**Automated floating dot navigation that automatically detects and navigates to page sections with class `.snap-section`**

Floating Dot Navigation is an elegant, fully customizable floating navigation menu that automatically detects sections on your pages. Perfect for one-page websites, long-form content, or any site that needs smooth section navigation.

The plugin creates a beautiful vertical dot navigation menu that appears on the right side of your page. Each dot represents a section, and clicking it smoothly scrolls to that section. The active section is automatically highlighted as users scroll through your content.

## ✨ Features

### Core Functionality
- **Automatic Section Detection** - Automatically finds all sections with the class `snap-section`
- **Smooth Scroll Navigation** - Click any dot to smoothly scroll to its corresponding section
- **Active Section Highlighting** - The current section is automatically highlighted as users scroll
- **Tooltip Labels** - Shows section names on hover or when active
- **Hide/Show Toggle** - Users can hide the navigation with a single click

### Customization
- **Fully Customizable** - Extensive customization options in WordPress Customizer
- **Smart Color System** - Auto-calculates text contrast for optimal readability
- **Theme Integration** - Seamlessly integrates with your theme's colors and CSS variables
- **Export/Import Settings** - Save and restore your customizations
- **Reset to Defaults** - One-click reset option

### Display Options
- **Flexible Display** - Show on homepage only, selected pages, or all pages
- **Page Selection** - Choose specific pages by ID
- **Header Offset Support** - Works perfectly with fixed headers

### Design
- **Responsive Design** - Adapts beautifully to all screen sizes
- **Smooth Animations** - Polished transitions and hover effects
- **Modern UI** - Clean, professional appearance

## 🎨 Customization Options

Customize every aspect of your navigation through the WordPress Customizer:

### Typography
- Font family (supports theme fonts and CSS variables)
- Tooltip font size

### Sizes
- Dot size
- Arrow width
- Spacing between dots
- Tooltip padding

### Colors
- Main accent color
- Text color (auto-calculated for contrast)
- Alternative color
- Border color
- Light and dark backgrounds
- Outline colors (active and hover states)

### Borders & Styling
- Border width
- Border style (solid, dashed, dotted, none)
- Border radius for dots
- Tooltip border radius

### Effects
- Outline size (inner shadow effect)
- Connecting trail (line between dots)
- Transition timing

### Advanced
- Full CSS override support for power users
- Custom CSS editor

## 🎯 Theme Compatibility

The plugin intelligently uses your theme's CSS variables when available, including:

- `--maincolor` - Theme primary color
- `--altcolor` - Theme secondary color
- `--titlefont` - Theme title font
- `--bodyfont` - Theme body font
- `--border-radius` - Theme border radius
- `--btn-border-radius` - Theme button border radius
- `--borders-color` - Theme border color
- `--lightbg2` - Theme light background
- `--maincolortext` - Theme primary text color
- `--wp-admin-theme-color` - WordPress admin theme color

If your theme doesn't provide these variables, the plugin uses sensible defaults that work with any theme.

## 📦 Installation

### Via WordPress Admin

1. Go to **Plugins > Add New**
2. Search for "Floating Dot Navigation"
3. Click **Install Now** and then **Activate**

### Manual Installation

1. Upload the plugin files to the `/wp-content/plugins/floating-dot-navigation` directory
2. Activate the plugin through the **Plugins** screen in WordPress
3. Go to **Appearance > Customize > Floating Dot Navigation** to configure

## 🚀 Quick Start

### 1. Add Sections to Your Pages

Add the class `snap-section` to any HTML element you want in the navigation. Each section must have a unique ID:

```html
<section id="about-us" class="snap-section">
    <!-- Your content here -->
</section>

<section id="services" class="snap-section">
    <!-- Your content here -->
</section>

<section id="contact" class="snap-section">
    <!-- Your content here -->
</section>
```

### 2. Customize Tooltip Text (Optional)

To customize the tooltip text, add an `aria-label` attribute:

```html
<section id="about-us" class="snap-section" aria-label="Learn About Us">
    <!-- Your content here -->
</section>
```

### 3. Configure in Customizer

1. Go to **Appearance > Customize > Floating Dot Navigation**
2. Choose where to display the navigation (Homepage, Selected Pages, or All Pages)
3. Customize colors, sizes, spacing, and other options
4. Click **Publish** to save your changes

## 📖 Usage Examples

### Basic Implementation

```html
<!DOCTYPE html>
<html>
<head>
    <!-- Your head content -->
</head>
<body>
    <section id="home" class="snap-section">
        <h1>Home Section</h1>
    </section>
    
    <section id="about" class="snap-section">
        <h1>About Section</h1>
    </section>
    
    <section id="services" class="snap-section">
        <h1>Services Section</h1>
    </section>
    
    <section id="contact" class="snap-section">
        <h1>Contact Section</h1>
    </section>
</body>
</html>
```

### With Custom Tooltips

```html
<section id="about-us" class="snap-section" aria-label="About Our Company">
    <h2>About Us</h2>
    <p>Content here...</p>
</section>

<section id="our-team" class="snap-section" aria-label="Meet Our Team">
    <h2>Our Team</h2>
    <p>Content here...</p>
</section>
```

## ⚙️ Configuration

### Display Location

Choose where the navigation appears:

- **Homepage Only** (default) - Only shows on your homepage
- **Selected Pages** - Enter specific page IDs separated by commas (e.g., `10, 25, 42`)
- **All Pages** - Shows on every page of your site

### Header Offset

If your site has a fixed header, enter its height in pixels in the "Header Offset" setting. This ensures the navigation scrolls to the correct position, accounting for your fixed header.

### Export/Import Settings

1. Click **Export Settings** to download your current configuration
2. Click **Import Settings** to upload and apply a previously exported configuration
3. Click **Reset to Defaults** to restore all settings to their original values

## 🎨 Customization Guide

### Using Theme Colors

The plugin automatically detects and uses your theme's CSS variables. You can also manually enter CSS variables:

- `var(--maincolor)` - Use theme primary color
- `var(--titlefont)` - Use theme title font
- `var(--border-radius)` - Use theme border radius

### Custom CSS

For advanced customization, use the Custom CSS editor in the Customizer. You can override any CSS variable:

```css
:root #floating_menu {
    --dotnav-maincolor: #ff0000;
    --dotnav-size: 35px;
    --dotnav-border-radius: 10px;
}
```

## ❓ Frequently Asked Questions

### How do I add sections to the navigation?

Simply add the class `snap-section` to any HTML element you want to appear in the navigation. Make sure each section has a unique ID attribute. You can also add an `aria-label` attribute to customize the tooltip text.

### Where can I customize the navigation?

All customization options are available in the WordPress Customizer. Go to **Appearance > Customize > Floating Dot Navigation**.

### Can I use my theme's colors?

Yes! The plugin automatically detects and uses your theme's CSS variables when available. You can also manually enter CSS variables like `var(--maincolor)` or use the color pickers to set custom colors.

### Where does the navigation appear?

You can choose to display it on:
- **Homepage Only** (default)
- **Selected Pages** (enter page IDs)
- **All Pages**

### How do I customize the tooltip text?

The plugin automatically generates tooltip text from the section ID (replacing hyphens with spaces). To customize it, add an `aria-label` attribute to your section:

```html
<section id="about-us" class="snap-section" aria-label="Learn About Us">
```

### Can I export my settings?

Yes! In the Customizer, you'll find Export, Import, and Reset buttons. You can export your settings to a file and import them later, or reset everything to defaults.

### Does it work with fixed headers?

Yes! Enter your header height in pixels in the "Header Offset" setting. This ensures the navigation scrolls to the correct position, accounting for your fixed header.

### Is it mobile-friendly?

Yes! The plugin is fully responsive and adapts to all screen sizes. On mobile devices, the navigation automatically adjusts spacing and sizes for optimal usability.

### Can I hide the navigation?

Yes! Users can click the X button at the bottom of the navigation to hide it. The button remains visible so they can show it again.

## 🔧 Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **Browser**: Modern browsers with CSS Grid and Flexbox support

## 📝 Changelog

### 1.4.0
- Major update: Complete Customizer overhaul with comprehensive customization options
- Added controls for all CSS variables (colors, sizes, spacing, fonts, borders, effects)
- Implemented export/import/reset functionality for settings
- Added automatic text contrast calculation for optimal readability
- Enhanced theme integration with CSS variable detection
- Improved responsive design for mobile devices
- Added custom control class for better UI in Customizer
- Fixed header offset support for accurate scrolling
- Added tooltip customization via aria-label attribute
- Performance improvements and code optimization

### 1.3.9
- Previous version with basic customization

### 1.0.0
- Initial release

## 🤝 Support

For support, feature requests, or bug reports, please visit:
- **Plugin URI**: https://satoshisea.io/
- **Author**: Marcos Ribero

## 📄 License

This plugin is licensed under the GPLv2 or later.

```
Copyright (C) 2024 Marcos Ribero

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## 👨‍💻 Author

**Marcos Ribero**
- Website: https://satoshisea.io/
- Author URI: https://satoshisea.io/

## 🙏 Credits

Built with ❤️ for the WordPress community.

---

**Made with WordPress** | **GPLv2 Licensed**

