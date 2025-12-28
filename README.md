# CTA Slider - WordPress Plugin

A professional WordPress plugin for creating beautiful Call-to-Action banner sliders using Bootstrap 5.3 carousel component.

**Version:** 1.0.0
**Requires at least:** WordPress 5.8
**Tested up to:** WordPress 6.4
**Requires PHP:** 7.4+
**License:** GPLv2 or later

## Description

CTA Slider allows you to create stunning, responsive call-to-action banner sliders for your WordPress site. Built on Bootstrap 5.3, it provides a powerful yet user-friendly interface for managing multiple sliders with customizable slides, captions, and call-to-action buttons.

### Key Features

- 📱 **Fully Responsive** - Works beautifully on all devices
- 🎨 **Bootstrap 5.3 Powered** - Leverages Bootstrap's robust carousel component
- 🖼️ **Media Library Integration** - Use WordPress's native media uploader
- ✏️ **Rich Caption Support** - Add titles and text overlays to slides
- 🔘 **Customizable CTA Buttons** - 8 Bootstrap button styles with custom text and links
- 🔄 **Drag-and-Drop Reordering** - Easily reorder slides with intuitive UI
- 🎛️ **Enable/Disable Slides** - One-click toggle with zero performance impact for disabled slides
- ⚙️ **Extensive Configuration** - Control every aspect of carousel behavior
- 🔒 **Security First** - Built following WordPress security best practices
- 🌐 **Translation Ready** - Fully internationalized

## Installation

### Manual Installation

1. Download the plugin zip file
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin" and select the zip file
4. Click "Install Now"
5. Activate the plugin

### From Source

1. Clone or download this repository
2. Copy the `cta-slider` directory to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress

### Requirements

**Important:** This plugin requires Bootstrap 5.3 to be loaded by your WordPress theme. The plugin does NOT include Bootstrap files to avoid conflicts. If your theme doesn't include Bootstrap, you can add it by uncommenting the Bootstrap CDN lines in `/public/class-cta-slider-public.php`.

## Quick Start Guide

### 1. Create Your First Slider

1. Navigate to **CTA Sliders** in your WordPress admin menu
2. Click **Add New**
3. Configure your slider settings:
   - Enter a unique **Slider ID** (e.g., "homepage-banner")
   - Set your **Slider Name** (e.g., "Homepage Banner")
   - Choose your display options (indicators, controls, transition type)
   - Configure autoplay settings
4. Click **Save Settings**

### 2. Add Slides

1. After saving, click **Manage Slides**
2. Click **Add New Slide**
3. Select an image from the Media Library
4. Optionally add a caption (title and text)
5. Optionally add a CTA button (text, URL, style, new tab option)
6. Click **Save Slide**
7. Repeat to add more slides
8. Drag and drop to reorder slides
9. Use **Enable/Disable** buttons to control which slides appear (disabled slides are NOT downloaded to the browser)

### 3. Display the Slider

Copy the shortcode from the admin interface and paste it into any page, post, or widget:

```
[cta_slider id="homepage-banner"]
```

## Configuration Options

### Slider Settings

#### Basic Information
- **Slider Name** - Display name for admin identification
- **Slider ID** - Unique identifier (used in shortcode)
- **Status** - Enable/disable slider

#### Display Options
- **Indicators** - Show dot indicators at bottom
- **Controls** - Show previous/next arrows
- **Transition Type** - Slide or Crossfade animation

#### Autoplay Settings
- **Autoplay** - Automatically cycle through slides
- **Interval** - Time between slides (1000-30000ms)
- **Pause on Hover** - Pause when mouse hovers

#### Navigation Options
- **Keyboard Navigation** - Arrow keys control slides
- **Touch/Swipe Support** - Swipe gestures on mobile
- **Continuous Loop** - Loop back to first slide

#### Image Display Options
- **Image Height** - Maximum height for carousel images (auto, 300px-800px)
  - Auto: Uses original image height
  - Fixed heights: Sets consistent height across all slides
  - Responsive: Automatically adjusts on mobile devices
- **Image Fit** - How images fill the container
  - **Cover (Recommended)**: Image zooms/scales to fill space while maintaining aspect ratio. No stretching or distortion. Excess is cropped.
  - Contain: Entire image visible, maintains aspect ratio, may show empty space
  - None: Image at original size without scaling

### Slide Settings

#### Image
- **Image** - Main slide image (required)
- **Alt Text** - Alternative text for accessibility

#### Caption
- **Enable Caption** - Show text overlay
- **Caption Title** - Main heading
- **Caption Text** - Supporting text

#### CTA Button
- **Enable Button** - Show call-to-action button
- **Button Text** - Button label
- **Button URL** - Destination link
- **Button Style** - 8 Bootstrap colors (Primary, Secondary, Success, Danger, Warning, Info, Light, Dark)
- **Open in New Tab** - Open link in new window

#### Slide Settings
- **Status** - Enable/disable individual slide

## Usage Examples

### Basic Slider

```
[cta_slider id="simple-banner"]
```

### Multiple Sliders

You can create multiple sliders and display them on different pages:

```
[cta_slider id="homepage-hero"]
[cta_slider id="product-showcase"]
[cta_slider id="testimonials"]
```

Note: Only one slider should be displayed per page for optimal performance.

## Database Structure

### Slider Configuration
Stored in `wp_options` table with key pattern: `cta_slider_config_{slider_id}`

### Slide Entries
Stored in custom table: `{$prefix}cta_slider_slides`

Fields include:
- Slider association
- Slide order
- Image data (ID, URL, alt text)
- Caption data (enabled, title, text)
- Button data (enabled, text, URL, style, new tab)
- Active status
- Timestamps

## Security Features

The plugin implements WordPress security best practices:

- ✅ **ABSPATH checks** - Prevent direct file access
- ✅ **Nonce verification** - CSRF protection for all forms
- ✅ **Capability checks** - Administrator-only access (`manage_options`)
- ✅ **Input sanitization** - All user input cleaned
- ✅ **Output escaping** - All output properly escaped
- ✅ **Prepared statements** - SQL injection prevention
- ✅ **Validation** - Data type and format validation

## File Structure

```
cta-slider/
├── cta-slider.php                      # Main plugin file
├── uninstall.php                       # Uninstall cleanup
├── README.md                           # This file
├── index.php                           # Directory protection
│
├── includes/                           # Core classes
│   ├── class-cta-slider-core.php      # Main orchestrator
│   ├── class-cta-slider-database.php  # Database operations
│   ├── class-cta-slider-shortcode.php # Shortcode rendering
│   ├── class-cta-slider-security.php  # Security utilities
│   └── class-cta-slider-activator.php # Activation hooks
│
├── admin/                              # Admin functionality
│   ├── class-cta-slider-admin.php
│   ├── class-cta-slider-settings.php
│   ├── class-cta-slider-slide-manager.php
│   ├── partials/                       # Admin templates
│   │   ├── admin-main.php
│   │   ├── slider-edit.php
│   │   ├── slider-slides.php
│   │   └── slide-form.php
│   ├── css/
│   │   └── cta-slider-admin.css
│   └── js/
│       └── cta-slider-admin.js
│
└── public/                             # Frontend
    ├── class-cta-slider-public.php
    ├── css/
    │   └── cta-slider-public.css
    └── js/
        └── cta-slider-public.js
```

## Uninstallation

When you delete the plugin (not just deactivate), all data is automatically removed:

- Custom database table is dropped
- All slider configurations are deleted from wp_options
- Slider list index is removed
- Works with multisite installations

**Note:** Deactivation does NOT delete data. Only complete uninstallation removes data.

## Developer Documentation

### Hooks and Filters

The plugin is built with extensibility in mind. Future versions will include action and filter hooks for customization.

### Database Schema

See the plan document at `/home/choskins/.claude/plans/bright-orbiting-kahan.md` for complete database schema documentation.

### Security

All functions follow WordPress coding standards and security practices. See `includes/class-cta-slider-security.php` for security implementation details.

## Troubleshooting

### Slider Not Displaying

1. **Check Bootstrap** - Ensure your theme loads Bootstrap 5.3
2. **Check Shortcode** - Verify the slider ID matches exactly
3. **Check Status** - Ensure slider and slides are set to "Active"
4. **Check Slides** - Slider needs at least one active slide

### Carousel Not Working

1. **Bootstrap JS** - Confirm Bootstrap JavaScript is loaded
2. **jQuery** - Ensure jQuery is enqueued
3. **Console Errors** - Check browser console for JavaScript errors
4. **Version Conflict** - Check for Bootstrap version conflicts

### Admin Interface Issues

1. **Permissions** - Only administrators can manage sliders
2. **JavaScript** - Ensure JavaScript is enabled
3. **Browser Cache** - Clear browser cache after updates

## Frequently Asked Questions

**Q: Does this plugin include Bootstrap?**
A: No, it expects Bootstrap 5.3 to be loaded by your theme to avoid conflicts.

**Q: Can I have multiple sliders on one page?**
A: Technically yes, but we recommend one slider per page for optimal performance.

**Q: Can I customize the slider appearance?**
A: Yes, you can add custom CSS to `public/css/cta-slider-public.css`.

**Q: Is it compatible with page builders?**
A: Yes, use the shortcode in any page builder that supports WordPress shortcodes.

**Q: Can I export/import sliders?**
A: Not currently, but this feature may be added in future versions.

## Changelog

### Version 1.0.0 - 2025-12-27
- Initial release
- Bootstrap 5.3 carousel integration
- WordPress Media Library integration
- Drag-and-drop slide reordering
- Comprehensive configuration options
- Security-first implementation
- Full WordPress coding standards compliance

## Support

For bug reports, feature requests, or questions:
- Create an issue on GitHub

## Credits

- Built with ❤️ following WordPress best practices
- Bootstrap 5.3 by the Bootstrap team
- Developed by: Ant Forager (and my pal Claude)

## License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025 Your Name

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

**Made with Claude Code** 🤖
