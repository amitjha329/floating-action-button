<?php
/**
 * Admin Settings Page Template
 * 
 * @package FloatingActionButton
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php settings_errors('fab_settings_group'); ?>

    <form method="post" action="options.php">
        <?php
        // Output security fields
        settings_fields('fab_settings_group');
        
        // Output settings sections
        do_settings_sections('floating-action-button');
        
        // Output save button
        submit_button(__('Save Settings', 'floating-action-button'));
        ?>
    </form>

    <div class="fab-info-box" style="margin-top: 30px; padding: 20px; background: #fff; border-left: 4px solid #2271b1; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <h2><?php esc_html_e('How to Use', 'floating-action-button'); ?></h2>
        <ol>
            <li><?php esc_html_e('Select your preferred icon type from the dropdown.', 'floating-action-button'); ?></li>
            <li><?php esc_html_e('If you choose "Custom", upload your own icon image.', 'floating-action-button'); ?></li>
            <li><?php esc_html_e('Enter the target URL where the button should link to.', 'floating-action-button'); ?></li>
            <li><?php esc_html_e('Choose the position where the button should appear on your site.', 'floating-action-button'); ?></li>
            <li><?php esc_html_e('For WhatsApp: Enable "Generate wa.me Link" to automatically create WhatsApp links from phone number and message.', 'floating-action-button'); ?></li>
        </ol>

        <h3><?php esc_html_e('Icon Types & Suggested URLs', 'floating-action-button'); ?></h3>
        <ul>
            <li><strong><?php esc_html_e('WhatsApp:', 'floating-action-button'); ?></strong> <?php esc_html_e('Use the WhatsApp settings below or enter a custom wa.me link', 'floating-action-button'); ?></li>
            <li><strong><?php esc_html_e('Facebook:', 'floating-action-button'); ?></strong> <?php esc_html_e('https://facebook.com/yourpage', 'floating-action-button'); ?></li>
            <li><strong><?php esc_html_e('Phone:', 'floating-action-button'); ?></strong> <?php esc_html_e('tel:+1234567890', 'floating-action-button'); ?></li>
            <li><strong><?php esc_html_e('Email:', 'floating-action-button'); ?></strong> <?php esc_html_e('mailto:email@example.com', 'floating-action-button'); ?></li>
            <li><strong><?php esc_html_e('Telegram:', 'floating-action-button'); ?></strong> <?php esc_html_e('https://t.me/username', 'floating-action-button'); ?></li>
        </ul>
    </div>
</div>
