<?php
/**
 * Plugin Name: Floating Action Button
 * Plugin URI: https://example.com/floating-action-button
 * Description: A customizable floating action button for your WordPress site with support for WhatsApp, social media, and custom icons.
 * Version: 1.1.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: floating-action-button
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('FAB_VERSION', '1.1.0');
define('FAB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FAB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FAB_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('FAB_PLUGIN_FILE', __FILE__);

// GitHub updater configuration
define('FAB_GITHUB_USERNAME', 'amitjha329'); // Your GitHub username
define('FAB_GITHUB_REPO', 'floating-action-button'); // Your repository name

// Include the GitHub updater
require_once FAB_PLUGIN_DIR . 'updater/class-fab-github-updater.php';

/**
 * Main Floating Action Button Class
 */
class Floating_Action_Button {

    /**
     * Constructor
     */
    public function __construct() {
        // Load plugin text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
    }

    /**
     * Load plugin text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain('floating-action-button', false, dirname(FAB_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * Add admin menu item under Settings
     */
    public function add_admin_menu() {
        add_options_page(
            __('Floating Action Button Settings', 'floating-action-button'),
            __('Floating Action Button', 'floating-action-button'),
            'manage_options',
            'floating-action-button',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        // Register settings group
        register_setting('fab_settings_group', 'fab_settings', array($this, 'sanitize_settings'));

        // General Settings Section
        add_settings_section(
            'fab_general_section',
            __('General Settings', 'floating-action-button'),
            array($this, 'general_section_callback'),
            'floating-action-button'
        );

        // Icon Type Field
        add_settings_field(
            'fab_icon_type',
            __('Icon Type', 'floating-action-button'),
            array($this, 'icon_type_callback'),
            'floating-action-button',
            'fab_general_section'
        );

        // Custom Icon Field
        add_settings_field(
            'fab_custom_icon',
            __('Custom Icon', 'floating-action-button'),
            array($this, 'custom_icon_callback'),
            'floating-action-button',
            'fab_general_section'
        );

        // Target URL Field
        add_settings_field(
            'fab_target_url',
            __('Target URL', 'floating-action-button'),
            array($this, 'target_url_callback'),
            'floating-action-button',
            'fab_general_section'
        );

        // Icon Position Field
        add_settings_field(
            'fab_icon_position',
            __('Icon Position', 'floating-action-button'),
            array($this, 'icon_position_callback'),
            'floating-action-button',
            'fab_general_section'
        );

        // WhatsApp Settings Section
        add_settings_section(
            'fab_whatsapp_section',
            __('WhatsApp Settings', 'floating-action-button'),
            array($this, 'whatsapp_section_callback'),
            'floating-action-button'
        );

        // Generate WhatsApp Link Toggle
        add_settings_field(
            'fab_generate_whatsapp',
            __('Generate wa.me Link', 'floating-action-button'),
            array($this, 'generate_whatsapp_callback'),
            'floating-action-button',
            'fab_whatsapp_section'
        );

        // Phone Number Field
        add_settings_field(
            'fab_phone_number',
            __('Phone Number', 'floating-action-button'),
            array($this, 'phone_number_callback'),
            'floating-action-button',
            'fab_whatsapp_section'
        );

        // Pre-filled Message Field
        add_settings_field(
            'fab_prefilled_message',
            __('Pre-filled Message', 'floating-action-button'),
            array($this, 'prefilled_message_callback'),
            'floating-action-button',
            'fab_whatsapp_section'
        );
    }

    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        // Sanitize icon type
        if (isset($input['icon_type'])) {
            $allowed_types = array('whatsapp', 'facebook', 'phone', 'email', 'telegram', 'custom');
            $sanitized['icon_type'] = in_array($input['icon_type'], $allowed_types) ? $input['icon_type'] : 'whatsapp';
        }

        // Sanitize custom icon URL
        if (isset($input['custom_icon'])) {
            $sanitized['custom_icon'] = esc_url_raw($input['custom_icon']);
        }

        // Sanitize target URL
        if (isset($input['target_url'])) {
            $sanitized['target_url'] = esc_url_raw($input['target_url']);
        }

        // Sanitize icon position
        if (isset($input['icon_position'])) {
            $allowed_positions = array('bottom-right', 'bottom-left', 'top-right', 'top-left');
            $sanitized['icon_position'] = in_array($input['icon_position'], $allowed_positions) ? $input['icon_position'] : 'bottom-right';
        }

        // Sanitize generate WhatsApp link
        $sanitized['generate_whatsapp'] = isset($input['generate_whatsapp']) ? 1 : 0;

        // Sanitize phone number
        if (isset($input['phone_number'])) {
            $sanitized['phone_number'] = sanitize_text_field($input['phone_number']);
        }

        // Sanitize pre-filled message
        if (isset($input['prefilled_message'])) {
            $sanitized['prefilled_message'] = sanitize_textarea_field($input['prefilled_message']);
        }

        return $sanitized;
    }

    /**
     * Section callbacks
     */
    public function general_section_callback() {
        echo '<p>' . esc_html__('Configure your floating action button appearance and behavior.', 'floating-action-button') . '</p>';
    }

    public function whatsapp_section_callback() {
        echo '<p>' . esc_html__('These settings only apply when WhatsApp is selected as the icon type.', 'floating-action-button') . '</p>';
    }

    /**
     * Field callbacks
     */
    public function icon_type_callback() {
        $options = get_option('fab_settings');
        $value = isset($options['icon_type']) ? $options['icon_type'] : 'whatsapp';
        ?>
        <select name="fab_settings[icon_type]" id="fab_icon_type">
            <option value="whatsapp" <?php selected($value, 'whatsapp'); ?>><?php esc_html_e('WhatsApp', 'floating-action-button'); ?></option>
            <option value="facebook" <?php selected($value, 'facebook'); ?>><?php esc_html_e('Facebook', 'floating-action-button'); ?></option>
            <option value="phone" <?php selected($value, 'phone'); ?>><?php esc_html_e('Phone', 'floating-action-button'); ?></option>
            <option value="email" <?php selected($value, 'email'); ?>><?php esc_html_e('Email', 'floating-action-button'); ?></option>
            <option value="telegram" <?php selected($value, 'telegram'); ?>><?php esc_html_e('Telegram', 'floating-action-button'); ?></option>
            <option value="custom" <?php selected($value, 'custom'); ?>><?php esc_html_e('Custom', 'floating-action-button'); ?></option>
        </select>
        <?php
    }

    public function custom_icon_callback() {
        $options = get_option('fab_settings');
        $value = isset($options['custom_icon']) ? $options['custom_icon'] : '';
        ?>
        <div id="fab_custom_icon_wrapper">
            <input type="text" name="fab_settings[custom_icon]" id="fab_custom_icon" value="<?php echo esc_attr($value); ?>" class="regular-text" />
            <button type="button" class="button" id="fab_upload_icon_button"><?php esc_html_e('Upload Icon', 'floating-action-button'); ?></button>
            <div id="fab_icon_preview" style="margin-top: 10px;">
                <?php if ($value) : ?>
                    <img src="<?php echo esc_url($value); ?>" style="max-width: 50px; max-height: 50px;" />
                <?php endif; ?>
            </div>
            <p class="description"><?php esc_html_e('Upload a custom icon image (PNG recommended).', 'floating-action-button'); ?></p>
        </div>
        <?php
    }

    public function target_url_callback() {
        $options = get_option('fab_settings');
        $value = isset($options['target_url']) ? $options['target_url'] : '';
        ?>
        <input type="url" name="fab_settings[target_url]" id="fab_target_url" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="https://example.com" />
        <p class="description"><?php esc_html_e('The URL the button will link to. For WhatsApp, this can be overridden by the "Generate wa.me Link" option.', 'floating-action-button'); ?></p>
        <?php
    }

    public function icon_position_callback() {
        $options = get_option('fab_settings');
        $value = isset($options['icon_position']) ? $options['icon_position'] : 'bottom-right';
        ?>
        <fieldset>
            <label>
                <input type="radio" name="fab_settings[icon_position]" value="bottom-right" <?php checked($value, 'bottom-right'); ?> />
                <?php esc_html_e('Bottom Right', 'floating-action-button'); ?>
            </label><br>
            <label>
                <input type="radio" name="fab_settings[icon_position]" value="bottom-left" <?php checked($value, 'bottom-left'); ?> />
                <?php esc_html_e('Bottom Left', 'floating-action-button'); ?>
            </label><br>
            <label>
                <input type="radio" name="fab_settings[icon_position]" value="top-right" <?php checked($value, 'top-right'); ?> />
                <?php esc_html_e('Top Right', 'floating-action-button'); ?>
            </label><br>
            <label>
                <input type="radio" name="fab_settings[icon_position]" value="top-left" <?php checked($value, 'top-left'); ?> />
                <?php esc_html_e('Top Left', 'floating-action-button'); ?>
            </label>
        </fieldset>
        <?php
    }

    public function generate_whatsapp_callback() {
        $options = get_option('fab_settings');
        $value = isset($options['generate_whatsapp']) ? $options['generate_whatsapp'] : 0;
        ?>
        <label for="fab_generate_whatsapp">
            <input type="checkbox" name="fab_settings[generate_whatsapp]" id="fab_generate_whatsapp" value="1" <?php checked($value, 1); ?> />
            <?php esc_html_e('Automatically generate WhatsApp link from phone number and message', 'floating-action-button'); ?>
        </label>
        <?php
    }

    public function phone_number_callback() {
        $options = get_option('fab_settings');
        $value = isset($options['phone_number']) ? $options['phone_number'] : '';
        ?>
        <input type="text" name="fab_settings[phone_number]" id="fab_phone_number" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="+11234567890" />
        <p class="description"><?php esc_html_e('Enter phone number with country code (e.g., +11234567890)', 'floating-action-button'); ?></p>
        <?php
    }

    public function prefilled_message_callback() {
        $options = get_option('fab_settings');
        $value = isset($options['prefilled_message']) ? $options['prefilled_message'] : '';
        ?>
        <textarea name="fab_settings[prefilled_message]" id="fab_prefilled_message" rows="4" class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <p class="description"><?php esc_html_e('Optional pre-filled message for WhatsApp chat', 'floating-action-button'); ?></p>
        <?php
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Include the settings page template
        include FAB_PLUGIN_DIR . 'admin/settings-page.php';
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our settings page
        if ($hook !== 'settings_page_floating-action-button') {
            return;
        }

        // Enqueue WordPress media uploader
        wp_enqueue_media();

        // Enqueue admin script
        wp_enqueue_script(
            'fab-admin-script',
            FAB_PLUGIN_URL . 'admin/admin-script.js',
            array('jquery'),
            FAB_VERSION,
            true
        );

        // Enqueue admin styles
        wp_enqueue_style(
            'fab-admin-style',
            FAB_PLUGIN_URL . 'admin/admin-style.css',
            array(),
            FAB_VERSION
        );
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        // Get plugin settings
        $options = get_option('fab_settings');

        // Don't load if no icon type is set
        if (empty($options['icon_type'])) {
            return;
        }

        // Enqueue FontAwesome for icons
        wp_enqueue_style(
            'fab-fontawesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0'
        );

        // Enqueue frontend CSS
        wp_enqueue_style(
            'fab-frontend-style',
            FAB_PLUGIN_URL . 'public/frontend-style.css',
            array(),
            FAB_VERSION
        );

        // Enqueue frontend JavaScript
        wp_enqueue_script(
            'fab-frontend-script',
            FAB_PLUGIN_URL . 'public/frontend-logic.js',
            array('jquery'),
            FAB_VERSION,
            true
        );

        // Localize script with settings
        wp_localize_script(
            'fab-frontend-script',
            'fabSettings',
            array(
                'iconType' => isset($options['icon_type']) ? $options['icon_type'] : '',
                'customIcon' => isset($options['custom_icon']) ? $options['custom_icon'] : '',
                'targetUrl' => isset($options['target_url']) ? $options['target_url'] : '',
                'iconPosition' => isset($options['icon_position']) ? $options['icon_position'] : 'bottom-right',
                'generateWhatsapp' => isset($options['generate_whatsapp']) ? $options['generate_whatsapp'] : 0,
                'phoneNumber' => isset($options['phone_number']) ? $options['phone_number'] : '',
                'prefilledMessage' => isset($options['prefilled_message']) ? $options['prefilled_message'] : ''
            )
        );
    }
}

// Initialize the plugin
function fab_init() {
    new Floating_Action_Button();
}
add_action('init', 'fab_init');

// Initialize the GitHub updater
if (is_admin()) {
    new FAB_Github_Updater(FAB_PLUGIN_FILE, FAB_GITHUB_USERNAME, FAB_GITHUB_REPO);
}
