# GitHub Updater Documentation

## Overview

The Floating Action Button plugin includes a self-contained GitHub-based automatic update mechanism. This allows the plugin to check for new releases from your public GitHub repository and notify users directly in their WordPress dashboard.

---

## How It Works

### 1. **Update Checking**
- The updater checks the GitHub API for the latest release
- Uses the `pre_set_site_transient_update_plugins` filter hook
- Caches results for 2 hours to prevent API rate limiting
- Compares current version with latest GitHub release tag

### 2. **Update Notification**
- If a new version is found, displays update notification in WordPress admin
- Shows "update available" message on Plugins page
- Provides "View details" link with changelog modal

### 3. **One-Click Update**
- Users can update directly from WordPress dashboard
- Downloads the latest release ZIP from GitHub
- Automatically installs and activates the new version

---

## Setup Instructions

### Step 1: Configure GitHub Repository

1. **Update the constants** in `floating-action-button.php`:
   ```php
   define('FAB_GITHUB_USERNAME', 'amitjha329'); // Your GitHub username
   define('FAB_GITHUB_REPO', 'floating-action-button'); // Your repo name
   ```

2. **Make your repository public** (or use a GitHub token for private repos - see Advanced section)

### Step 2: Create GitHub Releases

1. Go to your GitHub repository
2. Click on "Releases" → "Create a new release"
3. **Tag version** format: `v1.0.0`, `v1.1.0`, etc.
   - Must use semantic versioning
   - The `v` prefix is optional (updater handles both)

4. **Release title**: Version name (e.g., "Version 1.1.0")

5. **Release description**: Changelog in Markdown format
   ```markdown
   ## What's New
   
   ### Added
   - New feature 1
   - New feature 2
   
   ### Fixed
   - Bug fix 1
   - Bug fix 2
   
   ### Changed
   - Updated feature 3
   ```

6. Click "Publish release"

### Step 3: Test the Updater

1. Install your plugin on a test WordPress site
2. Set plugin version to `1.0.0`
3. Create a `v1.1.0` release on GitHub
4. Go to WordPress admin → Plugins
5. Check if update notification appears
6. Click "View details" to see changelog
7. Click "Update now" to test automatic update

---

## File Structure

```
floating-action-button/
├── updater/
│   ├── class-fab-github-updater.php    # Main updater class
│   └── index.php                       # Security file
└── floating-action-button.php          # Modified to include updater
```

---

## Configuration Options

### Cache Duration

Default: **2 hours (7200 seconds)**

To modify, edit `class-fab-github-updater.php`:

```php
private $cache_expiration = 7200; // Change to desired seconds
```

Recommended values:
- **1 hour**: `3600`
- **2 hours**: `7200` (default)
- **6 hours**: `21600`
- **12 hours**: `43200`

### GitHub API Endpoint

The updater uses:
```
https://api.github.com/repos/{USERNAME}/{REPO}/releases/latest
```

This endpoint:
- Returns the most recent non-prerelease, non-draft release
- Does not require authentication for public repos
- Has rate limits (60 requests/hour unauthenticated)

---

## GitHub Release Guidelines

### Version Numbering

Use **Semantic Versioning** (semver.org):
- **MAJOR.MINOR.PATCH** (e.g., 1.2.3)
- **MAJOR**: Breaking changes
- **MINOR**: New features, backwards compatible
- **PATCH**: Bug fixes only

Examples:
- `1.0.0` → `1.0.1` (bug fix)
- `1.0.1` → `1.1.0` (new feature)
- `1.1.0` → `2.0.0` (breaking change)

### Tag Format

Both formats work:
- `v1.0.0` (with 'v' prefix) ✅
- `1.0.0` (without 'v' prefix) ✅

The updater automatically removes the 'v' prefix if present.

### Changelog Format

Use Markdown for best results:

```markdown
# Version 1.1.0

Released: October 28, 2025

## ✨ New Features
- Added Instagram icon support
- Custom button shapes
- Animation library

## 🐛 Bug Fixes
- Fixed WhatsApp link encoding
- Resolved mobile responsiveness issue

## 🔧 Improvements
- Performance optimization
- Better error handling
- Updated Font Awesome to 6.5.0

## 📚 Documentation
- Added video tutorials
- Updated FAQ section
```

### Best Practices

1. **Always test releases** in staging before production
2. **Write clear changelogs** - users need to know what changed
3. **Use pre-releases** for beta versions
4. **Include upgrade notes** if manual steps are required
5. **Attach ZIP files** if you want to control the package

---

## How Users See Updates

### 1. Plugins Page Notification

```
Floating Action Button
Version 1.0.0 | by Your Name | View details
There is a new version of Floating Action Button available.
View version 1.1.0 details or update now.
```

### 2. Update Details Modal

When users click "View version 1.1.0 details", they see:
- Plugin name and version
- Release date
- Full changelog
- Download link
- "Install Update Now" button

### 3. Update Process

1. User clicks "Update now"
2. WordPress downloads ZIP from GitHub
3. Plugin is deactivated
4. Old files are replaced
5. Plugin is reactivated
6. Success message displayed

---

## API Rate Limits

### GitHub API Limits

**Unauthenticated requests:**
- 60 requests per hour per IP address

**With authentication:**
- 5,000 requests per hour

### Caching Strategy

The updater implements caching to stay within limits:

1. **First check**: API request made, result cached for 2 hours
2. **Subsequent checks**: Cached data used
3. **After 2 hours**: Cache expires, new API request made
4. **Failed requests**: Also cached to prevent hammering API

### Monitoring API Usage

Check your API rate limit status:
```bash
curl -I https://api.github.com/users/amitjha329
```

Response headers show:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 58
X-RateLimit-Reset: 1635438000
```

---

## Troubleshooting

### Plugin Deactivated After Update - "Plugin file does not exist"

**Problem**: After updating from GitHub, WordPress says "Plugin file does not exist" and deactivates the plugin.

**Cause**: GitHub's zipball creates a folder with a commit hash (e.g., `amitjha329-floating-action-button-abc123`), but WordPress expects the folder to be named `floating-action-button`.

**Solution (This is now fixed automatically):**

The updater now includes two methods to handle this:
1. `fix_source_folder()` - Renames the folder BEFORE installation
2. `post_install()` - Verifies correct folder name AFTER installation

**Manual Recovery (if you're currently experiencing this issue):**

Via FTP/File Manager:
1. Go to `/wp-content/plugins/`
2. Look for a folder like `amitjha329-floating-action-button-{hash}`
3. Rename it to `floating-action-button`
4. Go to WordPress admin → Plugins
5. Reactivate the plugin

Via WordPress admin:
1. Delete the broken plugin (don't worry, settings are saved in database)
2. Re-upload the plugin manually
3. Or wait for the fix and update again

**Prevention:**
Make sure you're using the latest version of the updater class which includes both:
- `upgrader_source_selection` filter (line ~103)
- `upgrader_post_install` filter (line ~104)

### Conflicts with WordPress.org Plugins

**Problem**: WordPress shows update from WordPress.org repository instead of GitHub (e.g., showing version from a different plugin with same/similar name)

**Solution:**

This is fixed in the updater class. The updater now automatically:
- Disables WordPress.org update checks for this plugin
- Prevents slug conflicts with plugins on WordPress.org
- Ensures only GitHub releases are shown for updates

**How it works:**
```php
// The updater filters HTTP requests to WordPress.org API
// and removes this plugin from WordPress.org update checks
add_filter('http_request_args', array($this, 'disable_wporg_update'), 10, 2);
```

**If the issue persists:**
1. Clear WordPress update cache:
   ```php
   delete_site_transient('update_plugins');
   ```

2. Check that the updater is properly initialized
3. Deactivate and reactivate the plugin
4. Verify you're on the latest version of the updater class

### Update Not Showing

**Problem**: New GitHub release exists, but no update notification

**Solutions:**

1. **Clear WordPress transients:**
   ```php
   // Add to wp-config.php temporarily
   delete_site_transient('update_plugins');
   ```

2. **Clear updater cache:**
   ```php
   // In WordPress admin or via plugin
   $updater = new FAB_Github_Updater(FAB_PLUGIN_FILE, FAB_GITHUB_USERNAME, FAB_GITHUB_REPO);
   $updater->clear_cache();
   ```

3. **Check plugin version:**
   - Ensure main plugin file has correct version
   - Version must be lower than GitHub release tag

4. **Verify GitHub release:**
   - Must not be a draft
   - Must not be marked as pre-release
   - Tag must be valid semver format

5. **Check API response:**
   - Visit: `https://api.github.com/repos/amitjha329/floating-action-button/releases/latest`
   - Should return JSON with latest release data

### Update Download Fails

**Problem**: Update notification shows, but download fails

**Solutions:**

1. **Check ZIP availability:**
   - GitHub automatically creates ZIP files for releases
   - The `zipball_url` should be accessible

2. **Test download manually:**
   ```
   https://api.github.com/repos/amitjha329/floating-action-button/zipball/v1.1.0
   ```

3. **Check WordPress permissions:**
   - Server must allow external HTTP requests
   - `wp_remote_get()` must be enabled

4. **Firewall/Security:**
   - Some hosts block GitHub API
   - Whitelist GitHub domains

### Version Compare Issues

**Problem**: Wrong version comparison results

**Solutions:**

1. **Standardize format:**
   - Use `1.0.0` format consistently
   - Don't use `v1.0.0` in plugin header
   - The 'v' should only be in Git tags

2. **Check version locations:**
   - Plugin header version
   - Defined constant `FAB_VERSION`
   - Both must match

### API Rate Limit Exceeded

**Problem**: Too many API requests

**Solutions:**

1. **Increase cache duration:**
   ```php
   private $cache_expiration = 43200; // 12 hours
   ```

2. **Use GitHub token** (see Advanced section)

3. **Wait for limit reset:**
   - Limits reset every hour
   - Check `X-RateLimit-Reset` header

---

## Advanced Configuration

### Using GitHub Token (Private Repos)

To use with private repositories or increase rate limits:

1. **Generate GitHub Personal Access Token:**
   - Go to GitHub Settings → Developer settings → Personal access tokens
   - Generate new token with `repo` scope
   - Copy token

2. **Modify updater class:**

   In `class-fab-github-updater.php`, update `fetch_github_release()`:

   ```php
   private function fetch_github_release() {
       // Get token from WordPress config or options
       $github_token = defined('FAB_GITHUB_TOKEN') ? FAB_GITHUB_TOKEN : '';
       
       $headers = array(
           'Accept' => 'application/vnd.github.v3+json',
           'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
       );
       
       // Add authorization if token exists
       if (!empty($github_token)) {
           $headers['Authorization'] = 'token ' . $github_token;
       }
       
       $response = wp_remote_get(
           $this->github_api_url,
           array(
               'timeout' => 15,
               'headers' => $headers
           )
       );
       
       // Rest of the function...
   }
   ```

3. **Add token to wp-config.php:**
   ```php
   define('FAB_GITHUB_TOKEN', 'ghp_your_token_here');
   ```

### Custom Release Asset

If you want to provide a custom ZIP instead of auto-generated:

1. **Build your ZIP:**
   ```bash
   cd /path/to/plugin
   zip -r floating-action-button-1.1.0.zip floating-action-button/
   ```

2. **Attach to GitHub release:**
   - When creating release, attach your ZIP
   - Name it: `floating-action-button-1.1.0.zip`

3. **Modify updater to prefer assets:**

   In `check_for_update()` method:

   ```php
   // After getting release_data, check for assets
   if (isset($release_data->assets) && !empty($release_data->assets)) {
       foreach ($release_data->assets as $asset) {
           if (strpos($asset->name, '.zip') !== false) {
               $plugin_update->package = $asset->browser_download_url;
               break;
           }
       }
   } else {
       $plugin_update->package = $release_data->zipball_url;
   }
   ```

### Debug Mode

Enable detailed logging:

1. **Enable WordPress debugging:**
   ```php
   // wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

2. **Add logging to updater:**

   In `class-fab-github-updater.php`, add after fetching release:

   ```php
   private function fetch_github_release() {
       // ... existing code ...
       
       if ($release_data) {
           error_log('FAB Updater: Successfully fetched release data');
           error_log('FAB Updater: Tag name - ' . $release_data->tag_name);
           error_log('FAB Updater: Package URL - ' . $release_data->zipball_url);
       }
       
       return $release_data;
   }
   ```

3. **Check logs:**
   ```
   wp-content/debug.log
   ```

---

## Security Considerations

### Plugin Integrity

1. **Verify sources:**
   - Only updates from your GitHub repository
   - GitHub serves as trusted source

2. **ZIP validation:**
   - WordPress validates ZIP structure
   - Updater renames folder after extraction

3. **User permissions:**
   - Only admins can see updates
   - Requires `update_plugins` capability

### Best Practices

1. **Use HTTPS** (already implemented)
2. **Validate version format** (already implemented)
3. **Sanitize API responses** (already implemented)
4. **Error handling** (already implemented)
5. **Cache API responses** (already implemented)

---

## Testing Checklist

Before releasing an update:

- [ ] Create GitHub release with proper tag
- [ ] Include changelog in release description
- [ ] Test on staging site first
- [ ] Verify version numbers are correct
- [ ] Check update notification appears
- [ ] Test "View details" modal
- [ ] Perform actual update
- [ ] Verify plugin still works after update
- [ ] Check no fatal errors
- [ ] Test on multiple WordPress versions
- [ ] Verify cache clearing works

---

## Comparison with WordPress.org

### GitHub Updater (This Plugin)

**Pros:**
- Full control over releases
- Instant updates (no approval process)
- Private repositories possible
- Custom changelog format
- Free hosting on GitHub

**Cons:**
- Must maintain GitHub repository
- API rate limits
- No plugin stats/downloads tracking
- Users must trust your repository

### WordPress.org Plugin Directory

**Pros:**
- Central trusted source
- No rate limits
- Detailed stats and downloads
- Better discovery (search)
- Established trust

**Cons:**
- Review process required
- Guidelines to follow
- Public only
- Less control over timing

---

## Maintenance

### Regular Tasks

1. **Monitor API usage:**
   - Check rate limits monthly
   - Adjust cache duration if needed

2. **Test updates:**
   - Test each release before publishing
   - Maintain staging site

3. **Update documentation:**
   - Keep changelog current
   - Document breaking changes

4. **Version management:**
   - Follow semantic versioning
   - Plan major releases carefully

---

## Support & Resources

### Helpful Links

- **GitHub Releases API:** https://docs.github.com/en/rest/releases/releases
- **WordPress Plugin API:** https://developer.wordpress.org/plugins/
- **Semantic Versioning:** https://semver.org/
- **GitHub Rate Limits:** https://docs.github.com/en/rest/overview/resources-in-the-rest-api#rate-limiting

### Quick Reference

**Clear update cache:**
```php
delete_site_transient('update_plugins');
delete_transient('fab_github_update_' . md5($api_url));
```

**Force update check:**
```
wp-admin/update-core.php
```

**Check current version:**
```php
$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/floating-action-button/floating-action-button.php');
echo $plugin_data['Version'];
```

---

## Summary

The GitHub updater provides:
- ✅ Automatic update notifications
- ✅ One-click updates from GitHub
- ✅ Changelog display
- ✅ Rate limit protection
- ✅ Error handling
- ✅ WordPress standard compliance
- ✅ Easy configuration
- ✅ No external dependencies

**You're ready to ship updates!** 🚀
