# GitHub Actions Workflows Documentation

## Overview

This plugin includes two automated GitHub Actions workflows to streamline the release process:

1. **Create Release** (Manual) - Interactive workflow for version bumping and release creation
2. **Auto Release on Tag** (Automatic) - Automatically creates releases when you push version tags

---

## Workflow 1: Create Release (Manual)

**File**: `.github/workflows/create-release.yml`

### Purpose
Provides an interactive way to create releases with automatic version bumping directly from the GitHub Actions UI.

### Features
- ✅ Automatic version bumping (major/minor/patch)
- ✅ Updates version in plugin files
- ✅ Generates or accepts custom changelog
- ✅ Updates CHANGELOG.md automatically
- ✅ Creates git commit and tag
- ✅ Creates GitHub release
- ✅ Professional release notes

### How to Use

#### Step 1: Navigate to Actions Tab
1. Go to your GitHub repository
2. Click on the **"Actions"** tab
3. Select **"Create Release"** workflow from the left sidebar

#### Step 2: Run Workflow
1. Click the **"Run workflow"** dropdown button
2. Select options:
   - **Branch**: `main` (or your default branch)
   - **Version bump type**: 
     - `patch` - Bug fixes (1.0.0 → 1.0.1)
     - `minor` - New features (1.0.0 → 1.1.0)
     - `major` - Breaking changes (1.0.0 → 2.0.0)
   - **Release notes** (optional): Custom changelog text

3. Click **"Run workflow"**

#### Step 3: Monitor Progress
- Watch the workflow execution in real-time
- Check each step's output
- Review the summary at the end

### What It Does

1. **Checks out your code**
2. **Gets current version** from `floating-action-button.php`
3. **Calculates new version** based on bump type
4. **Updates version** in:
   - `floating-action-button.php` (constant)
   - `floating-action-button.php` (header)
5. **Generates changelog** from commits or uses your custom notes
6. **Updates CHANGELOG.md** with new entry
7. **Commits changes** to main branch
8. **Creates git tag** (e.g., v1.1.0)
9. **Pushes tag** to GitHub
10. **Creates GitHub release** with changelog
11. **Shows summary** of what was created

### Examples

#### Example 1: Patch Release (Bug Fix)

```yaml
Version bump type: patch
Release notes: Fixed WhatsApp link encoding issue
```

**Result**:
- Version: 1.0.0 → 1.0.1
- Tag: v1.0.1
- Release title: "Version 1.0.1"

#### Example 2: Minor Release (New Feature)

```yaml
Version bump type: minor
Release notes: |
  - Added Instagram icon support
  - New custom animation options
  - Dark mode compatibility
```

**Result**:
- Version: 1.0.1 → 1.1.0
- Tag: v1.1.0
- Release title: "Version 1.1.0"

#### Example 3: Major Release (Breaking Changes)

```yaml
Version bump type: major
Release notes: |
  Breaking changes in this release:
  - New settings structure (requires reconfiguration)
  - Minimum WordPress version now 5.5
  - Removed deprecated features
```

**Result**:
- Version: 1.1.0 → 2.0.0
- Tag: v2.0.0
- Release title: "Version 2.0.0"

### Automation Features

**If no custom release notes provided**:
- Automatically generates changelog from git commits since last tag
- Groups commits by type
- Includes comparison link

**Automatic CHANGELOG.md update**:
- Inserts new version entry at the top
- Includes date and bump type
- Preserves existing changelog

---

## Workflow 2: Auto Release on Tag (Automatic)

**File**: `.github/workflows/auto-release-on-tag.yml`

### Purpose
Automatically creates a GitHub release when you push a version tag.

### Features
- ✅ Triggers on tag push (v*.*.*)
- ✅ Extracts version from tag
- ✅ Generates changelog from commits
- ✅ Groups changes by type (features, fixes, improvements)
- ✅ Creates professional GitHub release
- ✅ No manual intervention needed

### How to Use

#### Method 1: Command Line

```bash
# 1. Update version in files manually
# Edit floating-action-button.php:
# - Change FAB_VERSION constant
# - Change plugin header version

# 2. Update CHANGELOG.md manually

# 3. Commit changes
git add .
git commit -m "Prepare release 1.1.0"
git push origin main

# 4. Create and push tag
git tag -a v1.1.0 -m "Release version 1.1.0"
git push origin v1.1.0

# 5. Workflow runs automatically!
```

#### Method 2: GitHub UI

1. Go to repository **Releases** page
2. Click **"Draft a new release"**
3. Click **"Choose a tag"** → Type new tag (e.g., `v1.1.0`)
4. Click **"Create new tag: v1.1.0 on publish"**
5. Add title and description
6. Click **"Publish release"**
7. Workflow runs automatically!

### What It Does

1. **Detects tag push** (e.g., v1.1.0)
2. **Extracts version** from tag name
3. **Finds previous tag** for comparison
4. **Generates changelog** by analyzing commits:
   - Groups by type (features, fixes, improvements)
   - Filters by commit message prefixes
   - Includes "Other Changes" section
5. **Creates GitHub release** with:
   - Tag name
   - Version title
   - Auto-generated changelog
   - Comparison link
6. **Shows summary** of release

### Commit Message Conventions

For best changelog generation, use these prefixes:

```bash
# Features
feat: Add Instagram icon support
add: Custom animation options

# Bug Fixes
fix: WhatsApp link encoding
bug: Mobile display issue

# Improvements
improve: Performance optimization
update: Font Awesome to 6.5.0
refactor: Code cleanup

# Other (will be grouped separately)
docs: Update README
test: Add unit tests
chore: Update dependencies
```

### Example Output

When you push tag `v1.1.0`, the workflow creates:

```markdown
## What's Changed in v1.1.0

### 🚀 Features
- feat: Add Instagram icon support
- add: Custom animation options
- add: Dark mode compatibility

### 🐛 Bug Fixes
- fix: WhatsApp link encoding issue
- fix: Mobile Safari scrolling

### 🔧 Improvements
- improve: Performance optimization (20% faster)
- update: Font Awesome to 6.5.0
- refactor: Clean up CSS structure

### 📝 Other Changes
- docs: Update installation guide
- chore: Update dependencies

**Full Changelog**: https://github.com/amitjha329/floating-action-button/compare/v1.0.0...v1.1.0
```

---

## Comparison: Manual vs Automatic

### Create Release Workflow (Manual)

**Best for:**
- ✅ Quick releases without leaving GitHub
- ✅ When you want automation to handle everything
- ✅ Team members who prefer UI over CLI
- ✅ Ensuring consistent versioning

**Pros:**
- No local git commands needed
- Automatic version calculation
- Updates all files automatically
- Can't forget to update CHANGELOG.md
- Interactive and user-friendly

**Cons:**
- Requires GitHub Actions UI access
- Less control over commit messages
- Must be online

### Auto Release on Tag (Automatic)

**Best for:**
- ✅ Developers who prefer command line
- ✅ CI/CD pipelines
- ✅ When you want full control
- ✅ Scripted release processes

**Pros:**
- Works with existing git workflow
- Full control over commits and tags
- Can be scripted/automated
- Works offline (until push)

**Cons:**
- Must manually update versions
- Must manually update CHANGELOG.md
- More steps to remember

---

## File Structure

```
.github/
└── workflows/
    ├── create-release.yml          # Manual release workflow
    └── auto-release-on-tag.yml     # Automatic tag-based workflow
```

---

## Configuration

### Permissions Required

Both workflows need:
```yaml
permissions:
  contents: write  # To create releases and push commits/tags
```

This is automatically configured in the workflow files.

### Secrets Required

Both workflows use:
```yaml
GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

This token is **automatically provided** by GitHub Actions. No configuration needed!

---

## Troubleshooting

### Issue: Workflow not appearing in Actions tab

**Solution:**
1. Ensure files are in `.github/workflows/` directory
2. Ensure files have `.yml` or `.yaml` extension
3. Push files to `main` branch
4. Check workflow syntax is valid

### Issue: Permission denied

**Solution:**
1. Go to repository **Settings** → **Actions** → **General**
2. Under "Workflow permissions", select:
   - **"Read and write permissions"**
3. Check **"Allow GitHub Actions to create and approve pull requests"**
4. Click **"Save"**

### Issue: Version not updating

**Solution:**
1. Check `floating-action-button.php` format:
   ```php
   define('FAB_VERSION', '1.0.0');  // Correct format
   * Version: 1.0.0                  // Correct format
   ```
2. Ensure version uses semantic versioning (X.Y.Z)
3. Check workflow logs for sed command errors

### Issue: Tag already exists

**Solution:**
```bash
# Delete local tag
git tag -d v1.1.0

# Delete remote tag
git push origin :refs/tags/v1.1.0

# Create new tag
git tag -a v1.1.0 -m "Release 1.1.0"
git push origin v1.1.0
```

### Issue: Changelog not generated

**Solution:**
1. Ensure previous tags exist in repository
2. Check commit messages follow conventions
3. Review workflow logs for errors
4. Manually add release notes if needed

---

## Best Practices

### Versioning Strategy

Follow **Semantic Versioning**:

```
MAJOR.MINOR.PATCH

Example: 2.3.1
         │ │ └─ PATCH: Bug fixes (2.3.1 → 2.3.2)
         │ └─── MINOR: New features (2.3.2 → 2.4.0)
         └───── MAJOR: Breaking changes (2.4.0 → 3.0.0)
```

### Release Timing

**Good times to release:**
- ✅ During business hours (for support)
- ✅ Early in the week (Monday-Wednesday)
- ✅ After thorough testing
- ✅ When team is available

**Avoid releasing:**
- ❌ Friday afternoons
- ❌ Before holidays
- ❌ During peak traffic times
- ❌ Without testing

### Commit Message Guidelines

```bash
# Good commit messages
feat: Add Instagram icon support
fix: Resolve WhatsApp encoding bug
improve: Optimize button rendering performance
docs: Update installation instructions

# Bad commit messages
fixed stuff
updates
WIP
.
```

### Testing Before Release

**Pre-release checklist:**
- [ ] All tests passing
- [ ] Tested on staging site
- [ ] Tested on multiple WordPress versions
- [ ] Tested on multiple themes
- [ ] No console errors
- [ ] Documentation updated
- [ ] CHANGELOG.md updated (if manual)
- [ ] Breaking changes documented

---

## Advanced Usage

### Custom Release Notes Template

Edit workflow to use a template:

```yaml
- name: Generate changelog
  run: |
    cat > /tmp/release_notes.md << 'EOF'
    ## 🎉 What's New

    ${{ github.event.inputs.release_notes }}

    ## 📥 Installation

    WordPress users will see an update notification automatically.

    ## 🔗 Links

    - [Documentation](https://github.com/${{ github.repository }}/wiki)
    - [Support](https://github.com/${{ github.repository }}/issues)
    EOF
```

### Notify on Release

Add notification step:

```yaml
- name: Send notification
  uses: actions/github-script@v6
  with:
    script: |
      github.rest.issues.create({
        owner: context.repo.owner,
        repo: context.repo.repo,
        title: '🎉 Version ${{ steps.new_version.outputs.version }} Released!',
        body: 'A new version has been released. Check it out!',
        labels: ['release']
      })
```

### Build Assets

Add build step before release:

```yaml
- name: Build plugin ZIP
  run: |
    mkdir -p build
    zip -r build/floating-action-button-${{ steps.new_version.outputs.version }}.zip \
      floating-action-button/ \
      -x "*.git*" -x "*node_modules*" -x "*.github*"

- name: Upload Release Asset
  uses: actions/upload-release-asset@v1
  env:
    GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
  with:
    upload_url: ${{ steps.create_release.outputs.upload_url }}
    asset_path: ./build/floating-action-button-${{ steps.new_version.outputs.version }}.zip
    asset_name: floating-action-button-${{ steps.new_version.outputs.version }}.zip
    asset_content_type: application/zip
```

---

## Integration with Plugin Updater

These workflows work seamlessly with your plugin updater:

1. **Workflow creates release** on GitHub
2. **Plugin updater queries** GitHub API
3. **Finds new version** from release tag
4. **Shows notification** in WordPress admin
5. **Downloads ZIP** from GitHub
6. **Installs update** automatically

### Timeline

```
You create release (Manual workflow)
         ↓
GitHub release published (v1.1.0)
         ↓
WordPress sites check for updates (2 hours max)
         ↓
Update notification appears in admin
         ↓
User clicks "Update now"
         ↓
Download and install from GitHub
         ↓
Complete! ✅
```

---

## Quick Reference Commands

### Manual Workflow (GitHub UI)
1. Go to **Actions** tab
2. Select **"Create Release"**
3. Click **"Run workflow"**
4. Choose bump type and add notes
5. Click **"Run workflow"**
6. Done! ✅

### Automatic Workflow (Command Line)

```bash
# Update versions in files
# Update CHANGELOG.md
git add .
git commit -m "Prepare release X.Y.Z"
git push origin main

# Create and push tag
git tag -a vX.Y.Z -m "Release X.Y.Z"
git push origin vX.Y.Z

# Workflow runs automatically! ✅
```

### Check Workflow Status

```bash
# View workflow runs
gh run list --workflow=create-release.yml

# View specific run
gh run view <run-id>

# View logs
gh run view <run-id> --log
```

---

## Summary

You now have **two powerful workflows** for creating releases:

### Choose Manual Workflow When:
- ✅ You want everything automated
- ✅ You prefer GitHub UI
- ✅ You want guaranteed consistency
- ✅ Team members need easy access

### Choose Automatic Workflow When:
- ✅ You prefer command line
- ✅ You want full control
- ✅ You have scripted processes
- ✅ You're comfortable with git

**Both workflows result in the same outcome**: Professional GitHub releases that automatically trigger plugin updates for WordPress users!

---

## Resources

- **GitHub Actions Documentation**: https://docs.github.com/en/actions
- **Semantic Versioning**: https://semver.org/
- **Conventional Commits**: https://www.conventionalcommits.org/
- **GitHub CLI**: https://cli.github.com/

---

**Your release process is now fully automated!** 🚀
