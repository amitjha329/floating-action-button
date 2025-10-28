# Floating Action Button WordPress Plugin

A complete, customizable WordPress plugin that adds a floating action button to your website with support for WhatsApp, social media, phone, email, and custom icons.

## Features

- **Multiple Icon Types**: WhatsApp, Facebook, Phone, Email, Telegram, or Custom
- **Custom Icon Upload**: Upload your own PNG icons via WordPress Media Library
- **WhatsApp Auto-Link Generation**: Automatically create wa.me links with phone number and pre-filled message
- **Flexible Positioning**: Choose from Bottom Right, Bottom Left, Top Right, or Top Left
- **Fully Responsive**: Optimized for desktop, tablet, and mobile devices
- **Accessibility Ready**: Includes focus states and reduced-motion support
- **Translation Ready**: Full i18n support with text domain
- **WordPress Standards**: Built using WordPress Settings API and coding standards
- **Secure**: Proper sanitization, escaping, and security measures

## Installation

1. Upload the `floating-action-button` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings → Floating Action Button to configure the plugin

## Configuration

### General Settings

1. **Icon Type**: Select from predefined icons (WhatsApp, Facebook, Phone, Email, Telegram) or choose Custom to upload your own
2. **Custom Icon**: When "Custom" is selected, use the media uploader to select your icon image
3. **Target URL**: Enter the URL where the button should link to
4. **Icon Position**: Choose where the button appears on your site (Bottom Right, Bottom Left, Top Right, Top Left)

### WhatsApp Settings

When WhatsApp is selected as the icon type:

1. **Generate wa.me Link**: Toggle this ON to automatically generate WhatsApp links
   - When ON: Enter phone number and optional pre-filled message
   - When OFF: Enter a custom WhatsApp link in the Target URL field

2. **Phone Number**: Enter with country code (e.g., +11234567890)
3. **Pre-filled Message**: Optional message that appears in the WhatsApp chat

## Usage Examples

### WhatsApp Contact
- Icon Type: WhatsApp
- Generate wa.me Link: ON
- Phone Number: +11234567890
- Pre-filled Message: Hello! I'd like to learn more about your services.

### Facebook Page
- Icon Type: Facebook
- Target URL: https://facebook.com/yourpage

### Phone Call
- Icon Type: Phone
- Target URL: tel:+1234567890

### Email
- Icon Type: Email
- Target URL: mailto:contact@example.com

### Telegram
- Icon Type: Telegram
- Target URL: https://t.me/yourusername

### Custom Icon
- Icon Type: Custom
- Upload your own icon using the media uploader
- Target URL: Any URL you want

## File Structure

```
floating-action-button/
├── floating-action-button.php    # Main plugin file
├── admin/
│   ├── settings-page.php         # Admin settings page template
│   ├── admin-script.js           # Admin JavaScript (media uploader)
│   └── admin-style.css           # Admin styles
├── public/
│   ├── frontend-logic.js         # Frontend JavaScript
│   └── frontend-style.css        # Frontend styles
└── README.md                     # This file
```

## Technical Details

### Dependencies
- WordPress 5.0 or higher
- PHP 7.0 or higher
- jQuery (bundled with WordPress)
- Font Awesome 6.4.0 (loaded from CDN)

### Hooks Used
- `plugins_loaded`: Load text domain
- `admin_menu`: Register settings page
- `admin_init`: Register settings
- `admin_enqueue_scripts`: Load admin assets
- `wp_enqueue_scripts`: Load frontend assets

### Security Features
- Nonce verification for form submissions
- Input sanitization using WordPress functions
- Output escaping in templates
- URL validation
- XSS prevention

### Accessibility Features
- Keyboard navigation support
- Focus indicators
- ARIA-compliant markup
- Reduced motion support for users with vestibular disorders
- High contrast mode support

### Responsive Breakpoints
- Desktop: Default sizing (60px button)
- Tablet (≤768px): Slightly smaller (55px button)
- Mobile (≤480px): Compact size (50px button)

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Customization

### CSS Customization
You can override the default styles by adding custom CSS to your theme:

```css
/* Change button size */
.fab-button {
    width: 70px;
    height: 70px;
}

/* Change WhatsApp color */
.fab-button.fab-icon-whatsapp {
    background: #25D366;
}

/* Adjust positioning */
.fab-container.fab-bottom-right {
    bottom: 40px;
    right: 40px;
}
```

### JavaScript Customization
Use the `fabSettings` object to access plugin settings in your own scripts:

```javascript
jQuery(document).ready(function($) {
    if (typeof fabSettings !== 'undefined') {
        console.log('Icon Type:', fabSettings.iconType);
        console.log('Position:', fabSettings.iconPosition);
    }
});
```

## Troubleshooting

### Button Not Showing
1. Check that you've saved settings in the admin panel
2. Verify an icon type is selected
3. Clear your browser cache
4. Check for JavaScript errors in browser console

### WhatsApp Link Not Working
1. Ensure phone number includes country code
2. Verify "Generate wa.me Link" toggle is enabled
3. Check that phone number has no spaces or special characters

### Custom Icon Not Displaying
1. Verify the icon file is a valid image format (PNG recommended)
2. Check that the image URL is accessible
3. Try re-uploading the icon

## Support

For issues, questions, or contributions, please contact the plugin developer.

## Changelog

### Version 1.0.0
- Initial release
- Multiple icon type support
- WhatsApp auto-link generation
- Custom icon upload
- Responsive design
- Accessibility features

## License

This plugin is licensed under GPL v2 or later.

## Credits

- Font Awesome icons by Fonticons, Inc.
- Developed using WordPress Plugin Development best practices
