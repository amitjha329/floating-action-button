# GitHub Actions Quick Start

## 🚀 Quick Start: Create Your First Release

### Option 1: Using GitHub UI (Easiest)

**5 Simple Steps:**

1. **Go to Actions Tab**
   - Visit: https://github.com/amitjha329/floating-action-button/actions

2. **Select "Create Release"**
   - Click on "Create Release" in the left sidebar

3. **Run Workflow**
   - Click "Run workflow" dropdown
   - Branch: `main`
   - Version bump: `patch` (for first release after 1.0.0)
   - Release notes: `Bug fixes and improvements`
   - Click "Run workflow"

4. **Wait 1-2 Minutes**
   - Watch the workflow run
   - Green checkmark = Success! ✅

5. **Done!**
   - Version updated to 1.0.1
   - Tag created: v1.0.1
   - Release published
   - Users will see update!

---

### Option 2: Using Command Line (Advanced)

**Quick Commands:**

```bash
# 1. Make your changes
# ... edit files ...

# 2. Commit changes
git add .
git commit -m "Fix WhatsApp encoding bug"
git push origin main

# 3. Create and push tag
git tag -a v1.0.1 -m "Release 1.0.1"
git push origin v1.0.1

# Done! Release created automatically ✅
```

---

## 📋 Version Bump Guide

Choose the right bump type:

| Current | Change Type | Bump Type | Result |
|---------|-------------|-----------|--------|
| 1.0.0 | Bug fix | `patch` | 1.0.1 |
| 1.0.1 | New feature | `minor` | 1.1.0 |
| 1.1.0 | Breaking change | `major` | 2.0.0 |

**Examples:**

**Patch** (1.0.0 → 1.0.1):
- Fixed WhatsApp link bug
- Corrected CSS issue
- Security patch

**Minor** (1.0.0 → 1.1.0):
- Added Instagram icon
- New animation options
- Additional settings

**Major** (1.0.0 → 2.0.0):
- Removed old features
- Changed settings structure
- New WordPress minimum version

---

## ✅ First-Time Setup

### 1. Enable Workflow Permissions

1. Go to: https://github.com/amitjha329/floating-action-button/settings/actions
2. Scroll to **"Workflow permissions"**
3. Select **"Read and write permissions"**
4. Check **"Allow GitHub Actions to create and approve pull requests"**
5. Click **"Save"**

**That's it!** Workflows are ready to use.

---

## 🎯 Common Scenarios

### Scenario 1: Quick Bug Fix

```yaml
# GitHub UI:
Version bump: patch
Release notes: Fixed critical WhatsApp encoding bug
```

**Result**: 1.0.0 → 1.0.1

---

### Scenario 2: New Feature Release

```yaml
# GitHub UI:
Version bump: minor
Release notes: |
  - Added Instagram icon support
  - New custom animations
  - Dark mode compatible
```

**Result**: 1.0.0 → 1.1.0

---

### Scenario 3: Major Update

```yaml
# GitHub UI:
Version bump: major
Release notes: |
  Breaking changes:
  - New settings structure (backup your settings)
  - Minimum WordPress 5.5 required
  - Removed deprecated features
  
  Please read upgrade guide before updating.
```

**Result**: 1.0.0 → 2.0.0

---

## 📊 What Each Workflow Does

### Manual Workflow (create-release.yml)

```
You click "Run workflow"
         ↓
Calculates new version (1.0.0 → 1.0.1)
         ↓
Updates plugin files
         ↓
Updates CHANGELOG.md
         ↓
Commits to main branch
         ↓
Creates git tag (v1.0.1)
         ↓
Creates GitHub release
         ↓
Done! Users get update notification ✅
```

### Automatic Workflow (auto-release-on-tag.yml)

```
You push git tag
         ↓
Detects tag (v1.0.1)
         ↓
Analyzes commits since last version
         ↓
Generates changelog
         ↓
Creates GitHub release
         ↓
Done! Users get update notification ✅
```

---

## 🔍 Checking Release Status

### Via GitHub UI

1. Go to **Releases**: https://github.com/amitjha329/floating-action-button/releases
2. See all published releases
3. Latest release at the top

### Via API

```bash
curl https://api.github.com/repos/amitjha329/floating-action-button/releases/latest
```

### Via WordPress

1. Install plugin on test site
2. Set version to 1.0.0
3. Create release 1.0.1
4. Go to WordPress admin → Plugins
5. Wait 2 hours or clear cache
6. Update notification appears!

---

## 🐛 Troubleshooting

### No workflow in Actions tab?

**Solution:**
1. Check files are in `.github/workflows/`
2. Push to `main` branch
3. Refresh page

### Permission denied error?

**Solution:**
1. Go to Settings → Actions
2. Enable "Read and write permissions"
3. Save and retry

### Version not updating?

**Solution:**
1. Check workflow logs
2. Verify file format in `floating-action-button.php`
3. Ensure version follows X.Y.Z format

### Tag already exists?

**Solution:**
```bash
# Delete and recreate
git tag -d v1.0.1
git push origin :refs/tags/v1.0.1
git tag -a v1.0.1 -m "Release 1.0.1"
git push origin v1.0.1
```

---

## 💡 Pro Tips

1. **Test first**: Create test releases (v0.9.0) before official ones
2. **Use descriptions**: Add detailed release notes for users
3. **Check timing**: Release during business hours
4. **Monitor**: Watch workflow execution for errors
5. **Verify**: Check release page after workflow completes

---

## 🔗 Quick Links

**Your Repository:**
- Actions: https://github.com/amitjha329/floating-action-button/actions
- Releases: https://github.com/amitjha329/floating-action-button/releases
- Settings: https://github.com/amitjha329/floating-action-button/settings/actions

**Documentation:**
- Full Guide: `GITHUB-ACTIONS.md`
- Updater Guide: `GITHUB-UPDATER.md`
- Quick Reference: `GITHUB-UPDATER-QUICK.md`

---

## 📝 Checklist: Before First Release

- [ ] Workflows enabled (Settings → Actions → Permissions)
- [ ] Current version in plugin is 1.0.0
- [ ] CHANGELOG.md exists and is current
- [ ] All features tested
- [ ] Documentation updated
- [ ] Ready to release!

---

## 🎉 Ready to Go!

You're all set! Choose your preferred method:

**Prefer UI?** → Use "Create Release" workflow in GitHub Actions
**Prefer CLI?** → Use git tags and automatic workflow

**Either way, your releases are now automated!** 🚀

---

**Next Steps:**
1. Enable workflow permissions (if not done)
2. Create your first release
3. Wait 2 hours
4. Check WordPress for update notification
5. Celebrate! 🎊
