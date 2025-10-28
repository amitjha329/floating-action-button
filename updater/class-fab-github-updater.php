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
        add_filter('upgrader_source_selection', array($this, 'fix_source_folder'), 10, 4);
        
        // Disable WordPress.org updates for this plugin (prevent conflicts)
        add_filter('http_request_args', array($this, 'disable_wporg_update'), 10, 2);
        
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
     * Fix the source folder name from GitHub zipball
     * 
     * GitHub creates a folder like "username-repo-commitsha", but WordPress
     * expects the plugin folder name. This renames it before installation.
     * 
     * @param string      $source        Source folder path
     * @param string      $remote_source Remote source path
     * @param WP_Upgrader $upgrader      Upgrader instance
     * @param array       $hook_extra    Extra hook data
     * @return string|WP_Error Modified source path or error
     */
    public function fix_source_folder($source, $remote_source, $upgrader, $hook_extra = array()) {
        global $wp_filesystem;

        // Check if this is our plugin
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_basename) {
            return $source;
        }

        // Get the expected folder name
        $expected_slug = dirname($this->plugin_basename);
        
        // Get the current folder name from source
        $source_files = $wp_filesystem->dirlist($remote_source);
        
        if (!is_array($source_files) || count($source_files) !== 1) {
            return $source;
        }

        // Get the actual folder name that GitHub created
        $actual_slug = key($source_files);
        
        // If they match, we're good
        if ($actual_slug === $expected_slug) {
            return $source;
        }

        // Rename the folder to expected name
        $new_source = trailingslashit($remote_source) . $expected_slug;
        
        if ($wp_filesystem->move($source, $new_source)) {
            return $new_source;
        }

        return new WP_Error('rename_failed', __('Unable to rename plugin folder.'));
    }

    /**
     * Post-install hook to rename the plugin folder
     * 
     * GitHub's zipball comes with a folder name like "username-repo-commit",
     * but we need it to be the plugin slug.
     * 
     * @param bool  $response   Installation response
     * @param array $hook_extra Extra hook data
     * @param array $result     Installation result
     * @return bool
     */
    public function post_install($response, $hook_extra, $result) {
        global $wp_filesystem;

        // Check if this is our plugin being updated
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_basename) {
            return $response;
        }

        // Get the expected plugin directory name
        $proper_destination = WP_PLUGIN_DIR . '/' . dirname($this->plugin_basename);
        
        // Get the current destination (where GitHub extracted to)
        $current_destination = isset($result['destination']) ? $result['destination'] : false;
        
        if (!$current_destination) {
            return $response;
        }

        // If they're different, we need to rename
        if ($current_destination !== $proper_destination) {
            // Initialize WP_Filesystem if not already done
            if (!isset($wp_filesystem) || !is_object($wp_filesystem)) {
                WP_Filesystem();
            }

            // Remove old plugin directory if it exists
            if ($wp_filesystem->is_dir($proper_destination)) {
                $wp_filesystem->delete($proper_destination, true);
            }

            // Move from GitHub's folder name to proper plugin folder name
            $moved = $wp_filesystem->move($current_destination, $proper_destination);
            
            if ($moved) {
                $result['destination'] = $proper_destination;
                $result['destination_name'] = dirname($this->plugin_basename);
            }
        }

        // Clear cache
        delete_transient($this->cache_key);

        return $response;
    }

    /**
     * Disable WordPress.org update checks for this plugin
     * 
     * Prevents conflicts with plugins that have similar names on WordPress.org
     * 
     * @param array  $args HTTP request args
     * @param string $url  Request URL
     * @return array Modified args
     */
    public function disable_wporg_update($args, $url) {
        // Check if this is a WordPress.org API request
        if (strpos($url, 'api.wordpress.org') !== false) {
            // Check if plugins are being checked
            if (isset($args['body']['plugins'])) {
                $plugins = json_decode($args['body']['plugins'], true);
                
                // Remove our plugin from the check
                if (isset($plugins['plugins'][$this->plugin_basename])) {
                    unset($plugins['plugins'][$this->plugin_basename]);
                    $args['body']['plugins'] = json_encode($plugins);
                }
            }
        }
        
        return $args;
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

    /**
     * Get debug information about update status
     * 
     * @return array Debug information
     */
    public function get_debug_info() {
        $current_version = $this->get_plugin_version();
        $release_data = $this->fetch_github_release();
        
        $debug = array(
            'plugin_file' => $this->plugin_file,
            'plugin_basename' => $this->plugin_basename,
            'plugin_slug' => $this->plugin_slug,
            'current_version' => $current_version,
            'github_api_url' => $this->github_api_url,
            'cache_key' => $this->cache_key,
            'cached_data' => get_transient($this->cache_key),
            'fresh_data' => $release_data,
        );
        
        if ($release_data && is_object($release_data)) {
            $new_version = isset($release_data->tag_name) ? $release_data->tag_name : false;
            if ($new_version && substr($new_version, 0, 1) === 'v') {
                $new_version = substr($new_version, 1);
            }
            $debug['github_version'] = $new_version;
            $debug['update_available'] = version_compare($current_version, $new_version, '<');
        } else {
            $debug['error'] = 'Could not fetch release data from GitHub';
        }
        
        return $debug;
    }
}
