# Installation & Usage Guide

## Quick Start Guide

### Step 1: Install the Plugin

1. **Via WordPress Admin Panel:**
   - Go to `Plugins → Add New`
   - Click `Upload Plugin`
   - Choose the `floating-action-button.zip` file
   - Click `Install Now`
   - Click `Activate Plugin`

2. **Via FTP/File Manager:**
   - Upload the `floating-action-button` folder to `/wp-content/plugins/`
   - Go to `Plugins` in WordPress admin
   - Find "Floating Action Button" and click `Activate`

### Step 2: Configure Settings

1. Navigate to `Settings → Floating Action Button` in your WordPress admin
2. Configure your preferred settings (see detailed options below)
3. Click `Save Settings`

### Step 3: View Your Button

Visit your website's frontend to see the floating action button in action!

---

## Detailed Configuration Options

### 1. Icon Type Selection

Choose from six icon types:

#### WhatsApp
Best for: Customer support, sales inquiries, quick contact
- Displays WhatsApp icon
- Special auto-link generation available
- Can use custom wa.me links

#### Facebook
Best for: Social media engagement, page promotion
- Displays Facebook icon
- Link to your Facebook page or profile
- Example URL: `https://facebook.com/yourpage`

#### Phone
Best for: Direct phone calls, mobile-optimized contact
- Displays phone icon
- Uses `tel:` protocol for direct calling
- Example URL: `tel:+11234567890`

#### Email
Best for: Email inquiries, contact forms
- Displays email icon
- Uses `mailto:` protocol
- Example URL: `mailto:contact@example.com`
- Advanced: `mailto:contact@example.com?subject=Inquiry&body=Hello`

#### Telegram
Best for: Telegram community, channel promotion
- Displays Telegram icon
- Link to Telegram profile or group
- Example URL: `https://t.me/yourusername`

#### Custom
Best for: Unique icons, branded buttons
- Upload any PNG/JPG image
- Perfect for custom branding
- Link to any URL

### 2. Custom Icon Upload

When "Custom" is selected:

1. Click the `Upload Icon` button
2. Select an image from Media Library or upload new
3. Recommended specifications:
   - Format: PNG (with transparency)
   - Size: 100x100px to 200x200px
   - File size: Under 50KB for fast loading

### 3. Target URL Configuration

The destination link when users click the button.

**Standard Usage:**
- Enter any valid URL
- Examples:
  - Website: `https://example.com`
  - Phone: `tel:+11234567890`
  - Email: `mailto:email@example.com`
  - WhatsApp: `https://wa.me/11234567890`
  - Telegram: `https://t.me/username`

**Important Notes:**
- Always include `https://` for web URLs
- Use `tel:` prefix for phone numbers
- Use `mailto:` prefix for email addresses
- For WhatsApp, you can use manual links or enable auto-generation

### 4. Icon Position

Choose where the button appears on your site:

- **Bottom Right** (Default): Most common, doesn't obstruct content
- **Bottom Left**: Good for RTL languages or left-aligned designs
- **Top Right**: For headers or above-the-fold prominence
- **Top Left**: Alternative positioning for unique layouts

**Tips:**
- Test on both desktop and mobile
- Ensure it doesn't cover important content
- Consider your site's existing floating elements

### 5. WhatsApp Special Features

#### When to Use Auto-Generation
Enable "Generate wa.me Link" when:
- You want a simple WhatsApp contact setup
- You need a pre-filled message
- You want to avoid manual link formatting

#### When to Use Manual URL
Use the standard "Target URL" field when:
- Linking to a WhatsApp group
- Using WhatsApp Business API links
- You have a specific wa.me link structure

#### Auto-Generation Settings

**Phone Number Format:**
```
Correct: +11234567890
Correct: +44123456789
Incorrect: (123) 456-7890
Incorrect: 123-456-7890
```

**Pre-filled Message Tips:**
- Keep it friendly and professional
- State your purpose clearly
- Example: "Hi! I'm interested in learning more about your services."
- The message will be URL-encoded automatically

**Generated Link Format:**
```
https://wa.me/11234567890?text=Your%20encoded%20message
```

---

## Real-World Examples

### Example 1: Customer Support (WhatsApp)
```
Icon Type: WhatsApp
Generate wa.me Link: ON
Phone Number: +1234567890
Pre-filled Message: Hello! I need help with...
Icon Position: Bottom Right
```

### Example 2: Social Media Growth (Facebook)
```
Icon Type: Facebook
Target URL: https://facebook.com/mybusiness
Icon Position: Bottom Left
```

### Example 3: Quick Contact (Phone)
```
Icon Type: Phone
Target URL: tel:+1234567890
Icon Position: Bottom Right
```

### Example 4: Email Inquiries
```
Icon Type: Email
Target URL: mailto:sales@example.com?subject=Product Inquiry
Icon Position: Bottom Right
```

### Example 5: Telegram Community
```
Icon Type: Telegram
Target URL: https://t.me/yourcommunity
Icon Position: Bottom Left
```

### Example 6: Custom Branded Button
```
Icon Type: Custom
Custom Icon: [Upload your logo/icon]
Target URL: https://yourapp.com/download
Icon Position: Bottom Right
```

---

## Advanced Tips

### Multiple Buttons
To add multiple floating buttons, you can:
1. Use this plugin for one primary action
2. Add custom CSS for additional buttons
3. Consider using a different plugin for multi-button menus

### Custom Styling
Add to your theme's custom CSS:

```css
/* Larger button */
.fab-button {
    width: 70px !important;
    height: 70px !important;
}

/* Custom color */
.fab-button.fab-icon-whatsapp {
    background: #075E54 !important;
}

/* Add shadow effect */
.fab-button {
    box-shadow: 0 8px 16px rgba(0,0,0,0.3) !important;
}
```

### Hide on Specific Pages
Add to your theme's functions.php:

```php
add_action('wp_enqueue_scripts', 'hide_fab_on_pages', 100);
function hide_fab_on_pages() {
    if (is_page('contact')) {
        wp_dequeue_style('fab-frontend-style');
        wp_dequeue_script('fab-frontend-script');
    }
}
```

### Analytics Tracking
The button opens in a new tab by default. To track clicks:

1. Use Google Tag Manager to track link clicks
2. Add onclick attributes via JavaScript
3. Use WordPress action hooks to modify the output

---

## Testing Checklist

Before going live, test:

- [ ] Button appears on frontend
- [ ] Button is visible on mobile devices
- [ ] Link opens correctly
- [ ] WhatsApp link works (if using WhatsApp)
- [ ] Button doesn't cover important content
- [ ] Button is visible on all pages
- [ ] Button works in different browsers
- [ ] Custom icon displays correctly (if using custom)
- [ ] Settings save properly in admin
- [ ] No JavaScript console errors

---

## Performance Optimization

The plugin is optimized for performance:

- **Minimal HTTP Requests**: Only 2-3 external resources loaded
- **Small File Sizes**: CSS ~3KB, JS ~2KB
- **CDN Usage**: Font Awesome loaded from CDN
- **No Database Overhead**: Settings stored efficiently
- **Conditional Loading**: Scripts only load when needed

---

## Compatibility

### WordPress Themes
Compatible with all standard WordPress themes including:
- Twenty Twenty-Four
- Astra
- GeneratePress
- OceanWP
- Kadence
- Custom themes

### Page Builders
Works seamlessly with:
- Elementor
- WPBakery
- Divi Builder
- Beaver Builder
- Gutenberg (Block Editor)

### Plugins
Compatible with:
- WooCommerce
- Contact Form 7
- Yoast SEO
- All standard WordPress plugins

---

## Troubleshooting

### Issue: Button not visible
**Solutions:**
1. Clear browser cache
2. Check if icon type is selected
3. Verify settings are saved
4. Check for JavaScript conflicts
5. Disable other plugins temporarily

### Issue: WhatsApp link doesn't work
**Solutions:**
1. Verify phone number format (+countrycode)
2. Remove spaces and special characters
3. Test the generated link manually
4. Enable "Generate wa.me Link" toggle

### Issue: Custom icon not showing
**Solutions:**
1. Re-upload the icon
2. Verify image URL is accessible
3. Check image file format (use PNG)
4. Clear WordPress cache
5. Check file permissions

### Issue: Button covers content
**Solutions:**
1. Change icon position
2. Adjust position in CSS
3. Reduce button size
4. Use bottom positioning instead of top

---

## Support & Updates

For support, please:
1. Check this documentation first
2. Review the README.md file
3. Check WordPress.org support forums
4. Contact the plugin developer

Keep your plugin updated for:
- Security patches
- New features
- Bug fixes
- WordPress compatibility

---

## Best Practices

1. **Choose the Right Icon**: Match your primary call-to-action
2. **Test on Mobile**: Most traffic is mobile
3. **Keep It Simple**: One clear action is better than multiple
4. **Update Regularly**: Keep contact info current
5. **Monitor Performance**: Track click-through rates
6. **Accessibility**: Ensure keyboard navigation works
7. **Legal Compliance**: Add privacy notices if collecting data

---

## Need Help?

If you need assistance:
- Review this guide thoroughly
- Check the README.md for technical details
- Test in a staging environment first
- Document any errors you encounter
- Contact support with specific details

Happy floating! 🚀
