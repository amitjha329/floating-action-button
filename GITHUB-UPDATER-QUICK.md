# GitHub Updater - Quick Reference

## 🚀 Creating a New Release (Step by Step)

### 1. Update Plugin Version

Edit `floating-action-button.php`:
```php
define('FAB_VERSION', '1.1.0'); // Update version number
```

Also update the plugin header:
```php
/**
 * Version: 1.1.0
 */
```

### 2. Update CHANGELOG.md

Add your changes:
```markdown
## [1.1.0] - 2025-11-15

### Added
- New Instagram icon support
- Custom button shapes

### Fixed
- Mobile display issue
- WhatsApp link encoding
```

### 3. Commit and Push

```bash
git add .
git commit -m "Release version 1.1.0"
git push origin main
```

### 4. Create GitHub Release

1. Go to: `https://github.com/amitjha329/floating-action-button/releases/new`

2. **Tag version**: `v1.1.0` (or `1.1.0`)

3. **Release title**: `Version 1.1.0`

4. **Description** (Markdown format):
   ```markdown
   ## 🎉 What's New in Version 1.1.0
   
   ### ✨ New Features
   - Added Instagram icon support
   - Custom button shapes now available
   - Animation library with 5 new effects
   
   ### 🐛 Bug Fixes
   - Fixed mobile display issue on iOS
   - Resolved WhatsApp link encoding problem
   - Corrected RTL language positioning
   
   ### 🔧 Improvements
   - Performance optimization (20% faster)
   - Better error handling
   - Updated dependencies
   
   ## 📥 Installation
   
   WordPress users will see an automatic update notification.
   Click "Update now" to install this version.
   
   ## ⚠️ Breaking Changes
   
   None - fully backwards compatible!
   
   ## 📝 Full Changelog
   
   See [CHANGELOG.md](https://github.com/amitjha329/floating-action-button/blob/main/CHANGELOG.md)
   ```

5. Click **"Publish release"**

### 5. Verify Update

1. Wait 5-10 minutes for WordPress sites to check for updates
2. Or force check: Go to WordPress admin → Plugins → Check Again
3. You should see: "There is a new version available. View version 1.1.0 details"

---

## 📋 Version Comparison Examples

| Current | GitHub | Update Shows? |
|---------|--------|---------------|
| 1.0.0 | 1.0.1 | ✅ Yes |
| 1.0.0 | 1.1.0 | ✅ Yes |
| 1.0.0 | 2.0.0 | ✅ Yes |
| 1.1.0 | 1.0.9 | ❌ No |
| 1.1.0 | 1.1.0 | ❌ No |
| 1.0.0 | 1.0.0-beta | ❌ No (pre-release) |

---

## 🔧 Configuration

### Current Settings

```php
// In floating-action-button.php
define('FAB_GITHUB_USERNAME', 'amitjha329');
define('FAB_GITHUB_REPO', 'floating-action-button');
```

### GitHub API Endpoint

```
https://api.github.com/repos/amitjha329/floating-action-button/releases/latest
```

### Cache Duration

Default: **2 hours**

To change, edit `updater/class-fab-github-updater.php`:
```php
private $cache_expiration = 7200; // seconds
```

---

## 🧪 Testing Updates

### Test Environment Setup

1. **Install plugin** with version 1.0.0
2. **Create test release** on GitHub (v1.0.1)
3. **Go to WordPress** admin → Plugins
4. **Check** if update notification appears
5. **Click** "View version 1.0.1 details"
6. **Verify** changelog displays correctly
7. **Click** "Update now"
8. **Confirm** update completes successfully
9. **Test** plugin still works

### Force Update Check

```php
// Clear all update caches
delete_site_transient('update_plugins');
delete_transient('fab_github_update_' . md5('https://api.github.com/repos/amitjha329/floating-action-button/releases/latest'));
```

Or visit: `wp-admin/update-core.php`

---

## 🐛 Troubleshooting

### No Update Showing

**Check:**
1. Is GitHub release published? (not draft)
2. Is version number higher than current?
3. Is plugin version correct in main file?
4. Cache cleared?

**Solution:**
```bash
# Visit this URL to check API:
https://api.github.com/repos/amitjha329/floating-action-button/releases/latest

# Should return JSON with:
{
  "tag_name": "v1.1.0",
  "name": "Version 1.1.0",
  "zipball_url": "https://api.github.com/repos/...",
  ...
}
```

### Update Download Fails

**Causes:**
- GitHub API rate limit exceeded
- Network/firewall blocking GitHub
- Invalid ZIP structure

**Solutions:**
1. Wait 1 hour (rate limit resets)
2. Check error logs: `wp-content/debug.log`
3. Try manual download and install

### Wrong Version Showing

**Issue:** Update shows wrong version number

**Fix:**
1. Clear cache (see above)
2. Verify tag format: `v1.1.0` or `1.1.0`
3. Don't use 'v' in plugin header version

---

## 📊 API Rate Limits

### GitHub Limits

**Without authentication:**
- 60 requests per hour

**With GitHub token:**
- 5,000 requests per hour

### Current Usage

With 2-hour cache:
- Maximum 12 checks per day per user
- Well within 60/hour limit

### Check Your Rate Limit

```bash
curl -I https://api.github.com/users/amitjha329
```

Look for:
```
X-RateLimit-Remaining: 57
X-RateLimit-Reset: 1635438000
```

---

## 📝 Changelog Best Practices

### Good Changelog

```markdown
## Version 1.1.0

Released: November 15, 2025

### ✨ New Features
- Instagram icon support (#12)
- Custom animation options
- Dark mode compatibility

### 🐛 Bug Fixes
- Fixed mobile Safari scrolling issue (#45)
- Resolved WhatsApp link encoding
- Corrected positioning on RTL sites

### 🔧 Improvements
- 20% performance improvement
- Better error messages
- Updated Font Awesome to 6.5.0

### 📚 Documentation
- Added video tutorials
- Updated FAQ section
```

### Bad Changelog

```markdown
## v1.1.0

- Fixed stuff
- Added things
- Updated code
```

**Why bad?**
- Not descriptive
- No dates
- No categorization
- No details

---

## 🔐 Security Checklist

Before releasing:

- [ ] Sanitized all new inputs
- [ ] Escaped all new outputs
- [ ] No SQL injection vulnerabilities
- [ ] No XSS vulnerabilities
- [ ] Tested with WP_DEBUG enabled
- [ ] No PHP errors or warnings
- [ ] Updated version numbers everywhere
- [ ] Tested update process
- [ ] Backup created
- [ ] Changelog updated

---

## 📦 Release Checklist

**Pre-Release:**
- [ ] Code tested thoroughly
- [ ] Version numbers updated (plugin file + header)
- [ ] CHANGELOG.md updated
- [ ] Documentation updated if needed
- [ ] No debug code left in
- [ ] All files committed to git
- [ ] Tagged correctly in git

**Release:**
- [ ] GitHub release created
- [ ] Tag format correct (v1.x.x)
- [ ] Release title descriptive
- [ ] Changelog clear and detailed
- [ ] Not marked as pre-release
- [ ] Not saved as draft

**Post-Release:**
- [ ] Update notification appears
- [ ] "View details" shows correct info
- [ ] Update process works
- [ ] Plugin functions after update
- [ ] No PHP errors
- [ ] Monitor for user issues

---

## 🎯 Common Scenarios

### Hotfix Release

```bash
# 1. Fix the bug
# 2. Update version: 1.1.0 → 1.1.1
# 3. Update CHANGELOG.md
# 4. Commit and push
git commit -m "Hotfix: Critical WhatsApp link bug"
git push

# 5. Create release immediately
# Tag: v1.1.1
# Title: "Hotfix 1.1.1 - Critical Bug Fix"
```

### Major Version Release

```bash
# 1. Complete all breaking changes
# 2. Update version: 1.9.0 → 2.0.0
# 3. Document breaking changes
# 4. Update migration guide
# 5. Test extensively
# 6. Create release with warnings
```

### Beta Release

```bash
# 1. Update version: 1.1.0-beta.1
# 2. Create pre-release on GitHub
# 3. Check "This is a pre-release" ✓
# 4. Beta users can test
# 5. Not shown to regular users
```

---

## 💡 Pro Tips

1. **Version Format**: Always use semantic versioning (x.y.z)
2. **Release Timing**: Release during business hours for support
3. **Testing**: Test updates on staging before production
4. **Communication**: Announce major updates to users
5. **Rollback Plan**: Keep previous version accessible
6. **Documentation**: Update docs before releasing
7. **Changelog**: Write for users, not developers
8. **Tags**: Use consistent tag format (always v1.x.x or always 1.x.x)

---

## 🔗 Quick Links

**Your Plugin:**
- Repository: `https://github.com/amitjha329/floating-action-button`
- Releases: `https://github.com/amitjha329/floating-action-button/releases`
- New Release: `https://github.com/amitjha329/floating-action-button/releases/new`

**API:**
- Latest Release: `https://api.github.com/repos/amitjha329/floating-action-button/releases/latest`
- All Releases: `https://api.github.com/repos/amitjha329/floating-action-button/releases`

**Documentation:**
- Full Guide: `GITHUB-UPDATER.md`
- Changelog: `CHANGELOG.md`
- This File: `GITHUB-UPDATER-QUICK.md`

---

## ❓ Need Help?

1. Check `GITHUB-UPDATER.md` for detailed documentation
2. Review error logs: `wp-content/debug.log`
3. Test API endpoint manually
4. Verify GitHub release settings
5. Clear all caches and try again

---

**Happy Releasing!** 🚀
