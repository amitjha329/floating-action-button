<?php
/**
 * GitHub Updater Class for Floating Action Button Plugin
 * 
 * Handles automatic updates from a public GitHub repository.
 * Checks for new releases and displays update notifications in WordPress admin.
 * 
 * @package FloatingActionButton
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class FAB_Github_Updater
 * 
 * Self-contained updater class that integrates with WordPress plugin update system.
 */
class FAB_Github_Updater {

    /**
     * Plugin file path
     * 
     * @var string
     */
    private $plugin_file;

    /**
     * Plugin basename (e.g., floating-action-button/floating-action-button.php)
     * 
     * @var string
     */
    private $plugin_basename;

    /**
     * Plugin slug (e.g., floating-action-button)
     * 
     * @var string
     */
    private $plugin_slug;

    /**
     * GitHub username
     * 
     * @var string
     */
    private $github_username;

    /**
     * GitHub repository name
     * 
     * @var string
     */
    private $github_repo;

    /**
     * GitHub API URL
     * 
     * @var string
     */
    private $github_api_url;

    /**
     * Transient cache key
     * 
     * @var string
     */
    private $cache_key;

    /**
     * Cache expiration time in seconds (2 hours)
     * 
     * @var int
     */
    private $cache_expiration = 7200;

    /**
     * Constructor
     * 
     * @param string $plugin_file     Full path to the main plugin file
     * @param string $github_username GitHub username
     * @param string $github_repo     GitHub repository name
     */
    public function __construct($plugin_file, $github_username, $github_repo) {
        // Set properties
        $this->plugin_file = $plugin_file;
        $this->plugin_basename = plugin_basename($plugin_file);
        $this->plugin_slug = dirname($this->plugin_basename);
        $this->github_username = $github_username;
        $this->github_repo = $github_repo;
        $this->github_api_url = sprintf(
            'https://api.github.com/repos/%s/%s/releases/latest',
            $github_username,
            $github_repo
        );
        $this->cache_key = 'fab_github_update_' . md5($this->github_api_url);

        // Register hooks
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_filter('upgrader_post_install', array($this, 'post_install'), 10, 3);
        
        // Add custom update message
        add_action('in_plugin_update_message-' . $this->plugin_basename, array($this, 'update_message'), 10, 2);
    }

    /**
     * Check for plugin updates
     * 
     * @param object $transient The WordPress update transient object
     * @return object Modified transient object
     */
    public function check_for_update($transient) {
        // If no transient, return
        if (empty($transient->checked)) {
            return $transient;
        }

        // Get cached release data
        $release_data = get_transient($this->cache_key);

        // If no cache, fetch from GitHub
        if (false === $release_data) {
            $release_data = $this->fetch_github_release();
            
            // Cache the result (even if it's an error, to prevent hammering the API)
            if ($release_data !== false) {
                set_transient($this->cache_key, $release_data, $this->cache_expiration);
            }
        }

        // If we have valid release data
        if ($release_data && is_object($release_data)) {
            // Get current plugin version
            $current_version = $this->get_plugin_version();
            
            // Get new version from GitHub
            $new_version = isset($release_data->tag_name) ? $release_data->tag_name : false;
            
            // Remove 'v' prefix if present (e.g., v1.0.0 -> 1.0.0)
            if ($new_version && substr($new_version, 0, 1) === 'v') {
                $new_version = substr($new_version, 1);
            }

            // Compare versions
            if ($new_version && version_compare($current_version, $new_version, '<')) {
                // Create update object
                $plugin_update = new stdClass();
                $plugin_update->slug = $this->plugin_slug;
                $plugin_update->plugin = $this->plugin_basename;
                $plugin_update->new_version = $new_version;
                $plugin_update->url = isset($release_data->html_url) ? $release_data->html_url : '';
                $plugin_update->package = isset($release_data->zipball_url) ? $release_data->zipball_url : '';
                $plugin_update->tested = get_bloginfo('version'); // Current WordPress version
                $plugin_update->compatibility = new stdClass();

                // Add to transient
                $transient->response[$this->plugin_basename] = $plugin_update;
            }
        }

        return $transient;
    }

    /**
     * Fetch latest release data from GitHub API
     * 
     * @return object|false Release data object or false on failure
     */
    private function fetch_github_release() {
        // Make API request
        $response = wp_remote_get(
            $this->github_api_url,
            array(
                'timeout' => 15,
                'headers' => array(
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
                )
            )
        );

        // Check for errors
        if (is_wp_error($response)) {
            error_log('FAB GitHub Updater Error: ' . $response->get_error_message());
            return false;
        }

        // Check response code
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            error_log('FAB GitHub Updater Error: API returned status code ' . $response_code);
            return false;
        }

        // Get response body
        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            error_log('FAB GitHub Updater Error: Empty response body');
            return false;
        }

        // Decode JSON
        $release_data = json_decode($body);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('FAB GitHub Updater Error: Invalid JSON response');
            return false;
        }

        return $release_data;
    }

    /**
     * Get current plugin version from plugin header
     * 
     * @return string Plugin version
     */
    private function get_plugin_version() {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugin_data = get_plugin_data($this->plugin_file);
        return isset($plugin_data['Version']) ? $plugin_data['Version'] : '0.0.0';
    }

    /**
     * Provide plugin information for the "View details" modal
     * 
     * @param false|object|array $result The result object or array
     * @param string             $action The type of information being requested
     * @param object             $args   Plugin API arguments
     * @return false|object Modified result object
     */
    public function plugin_info($result, $action = '', $args = null) {
        // Check if this is a request for our plugin
        if ($action !== 'plugin_information') {
            return $result;
        }

        if (!isset($args->slug) || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        // Get cached release data
        $release_data = get_transient($this->cache_key);

        // If no cache, fetch from GitHub
        if (false === $release_data) {
            $release_data = $this->fetch_github_release();
            
            if ($release_data !== false) {
                set_transient($this->cache_key, $release_data, $this->cache_expiration);
            }
        }

        // If we have valid release data
        if ($release_data && is_object($release_data)) {
            // Get plugin data
            if (!function_exists('get_plugin_data')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $plugin_data = get_plugin_data($this->plugin_file);

            // Get new version
            $new_version = isset($release_data->tag_name) ? $release_data->tag_name : '';
            if ($new_version && substr($new_version, 0, 1) === 'v') {
                $new_version = substr($new_version, 1);
            }

            // Create plugin info object
            $plugin_info = new stdClass();
            $plugin_info->name = isset($plugin_data['Name']) ? $plugin_data['Name'] : 'Floating Action Button';
            $plugin_info->slug = $this->plugin_slug;
            $plugin_info->version = $new_version;
            $plugin_info->author = isset($plugin_data['Author']) ? $plugin_data['Author'] : '';
            $plugin_info->homepage = isset($release_data->html_url) ? $release_data->html_url : '';
            $plugin_info->requires = '5.0';
            $plugin_info->tested = get_bloginfo('version');
            $plugin_info->downloaded = 0;
            $plugin_info->last_updated = isset($release_data->published_at) ? $release_data->published_at : '';
            $plugin_info->download_link = isset($release_data->zipball_url) ? $release_data->zipball_url : '';

            // Sections
            $plugin_info->sections = array(
                'description' => isset($plugin_data['Description']) ? $plugin_data['Description'] : '',
                'changelog' => $this->format_changelog($release_data)
            );

            // Add installation section if available
            if (isset($release_data->body) && !empty($release_data->body)) {
                $plugin_info->sections['installation'] = '<p>Download and install like any other WordPress plugin.</p>';
            }

            return $plugin_info;
        }

        return $result;
    }

    /**
     * Format changelog from GitHub release data
     * 
     * @param object $release_data GitHub release data
     * @return string Formatted changelog HTML
     */
    private function format_changelog($release_data) {
        $changelog = '';

        if (isset($release_data->name) && !empty($release_data->name)) {
            $changelog .= '<h3>' . esc_html($release_data->name) . '</h3>';
        }

        if (isset($release_data->published_at) && !empty($release_data->published_at)) {
            $date = date('F j, Y', strtotime($release_data->published_at));
            $changelog .= '<p><strong>Released:</strong> ' . esc_html($date) . '</p>';
        }

        if (isset($release_data->body) && !empty($release_data->body)) {
            // Convert Markdown to basic HTML
            $body = $release_data->body;
            
            // Convert Markdown headings
            $body = preg_replace('/^### (.+)$/m', '<h4>$1</h4>', $body);
            $body = preg_replace('/^## (.+)$/m', '<h3>$1</h3>', $body);
            $body = preg_replace('/^# (.+)$/m', '<h2>$1</h2>', $body);
            
            // Convert Markdown lists
            $body = preg_replace('/^\* (.+)$/m', '<li>$1</li>', $body);
            $body = preg_replace('/^- (.+)$/m', '<li>$1</li>', $body);
            
            // Wrap list items in ul tags
            $body = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $body);
            
            // Convert line breaks
            $body = nl2br($body);
            
            $changelog .= '<div class="fab-changelog-content">' . wp_kses_post($body) . '</div>';
        } else {
            $changelog .= '<p>No changelog available.</p>';
        }

        return $changelog;
    }

    /**
     * Post-install hook to rename the plugin folder
     * 
     * GitHub's zipball comes with a folder name like "username-repo-commit",
     * but we need it to be the plugin slug.
     * 
     * @param bool  $true       Always true
     * @param array $hook_extra Extra hook data
     * @param array $result     Installation result
     * @return bool
     */
    public function post_install($true, $hook_extra, $result) {
        global $wp_filesystem;

        // Check if this is our plugin
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_basename) {
            return $true;
        }

        // Get the plugin directory
        $plugin_folder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname($this->plugin_basename);
        
        // Move to correct location if needed
        if (isset($result['destination']) && $result['destination'] !== $plugin_folder) {
            // Remove old directory if exists
            if ($wp_filesystem->is_dir($plugin_folder)) {
                $wp_filesystem->delete($plugin_folder, true);
            }

            // Move to correct location
            $wp_filesystem->move($result['destination'], $plugin_folder);
            $result['destination'] = $plugin_folder;
        }

        // Clear cache
        delete_transient($this->cache_key);

        return $true;
    }

    /**
     * Display custom update message
     * 
     * @param array  $plugin_data Plugin data array
     * @param object $response    Update response object
     */
    public function update_message($plugin_data, $response) {
        if (isset($response->package) && !empty($response->package)) {
            echo '<br /><strong>Note:</strong> This update will be downloaded from GitHub.';
        }
    }

    /**
     * Clear update cache
     * 
     * Public method to allow manual cache clearing if needed
     */
    public function clear_cache() {
        delete_transient($this->cache_key);
    }
}
