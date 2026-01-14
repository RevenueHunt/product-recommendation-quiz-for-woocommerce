# Standard Operating Procedure: Update WordPress Plugin

This SOP provides step-by-step instructions for updating the Product Recommendation Quiz for eCommerce WordPress plugin, including version number updates, Git commits, and SVN deployment to WordPress.org.

## Prerequisites

- Access to the Git repository: `https://github.com/RevenueHunt/product-recommendation-quiz-for-woocommerce`
- Access to the WordPress.org SVN repository: `prq-wp-plugin`
- SVN installed on your system (install with `brew install subversion` if needed)
- WordPress.org SVN credentials (username: `revenuehunt`, password stored in KeePassXC)
- Text editor with find-and-replace capabilities

## Overview

The update process consists of 6 main steps:
1. Make edits in the WordPress plugin
2. Update version numbers across all files
3. Commit and push changes to Git
4. Copy modified files to SVN trunk
5. Commit changes to SVN
6. Create and commit the SVN tag
7. Verify the update on WordPress.org

---

## Step 1: Make Your Edits

Make all necessary code changes, bug fixes, or feature additions to the WordPress plugin files in your local workspace.

**Location**: `/Users/libertas/Local Sites/productrecommendationquiz/app/public/wp-content/plugins/product-recommendation-quiz-for-ecommerce/`

---

## Step 2: Update Version Numbers

Update the version number in all relevant files. The version follows semantic versioning (e.g., `2.2.14` → `2.2.15`).

### 2.1 Determine the New Version Number

- If the current version is `2.2.14`, the next version should be `2.2.15`
- Follow semantic versioning: `MAJOR.MINOR.PATCH`
- For bug fixes, increment PATCH
- For new features, increment MINOR
- For breaking changes, increment MAJOR

### 2.2 Files to Update

Update the version number in the following files:

#### A. Main Plugin File
**File**: `product-recommendation-quiz-for-ecommerce.php`

Update these lines:
- Line 19: `Version:           2.2.14` → `Version:           2.2.15`
- Line 40: `define( 'PRQ_PLUGIN_VERSION', '2.2.14' );` → `define( 'PRQ_PLUGIN_VERSION', '2.2.15' );`

#### B. Core Plugin Class
**File**: `includes/class-product-recommendation-quiz-for-ecommerce.php`

No version number in this file (version is read from the constant defined in the main plugin file).

#### C. README.txt
**File**: `README.txt`

Update these lines:
- Line 6: `Tested up to: 6.8.3` (update to latest WordPress version if applicable)
- Line 8: `Stable tag: 2.2.14` → `Stable tag: 2.2.15`

#### D. README.md
**File**: `README.md`

Update version references if present (check for any version mentions).

#### E. Changelog File
**File**: `changelog.txt`

**IMPORTANT**: Do NOT replace the old version in the changelog. Instead, add a NEW entry at the TOP of the file.

Format:
```
*** Product Recommendation Quiz for WooCommerce ***

YYYY-MM-DD - version 2.2.15
* Description of changes (e.g., "Dev - tested up to WP 6.9.0")

[Previous entries remain below...]
```

Example entry:
```
2025-10-15 - version 2.2.15
* Dev - tested up to WP 6.9.0 and WooCommerce up to Version 10.3.0
```

#### F. Language File (if updated)
**File**: `languages/product-recommendation-quiz-for-ecommerce.pot`

Update version references if the language file was modified.

### 2.3 Version Update Checklist

- [ ] `product-recommendation-quiz-for-ecommerce.php` - Version header (line 19)
- [ ] `product-recommendation-quiz-for-ecommerce.php` - PRQ_PLUGIN_VERSION constant (line 40)
- [ ] `README.txt` - Stable tag (line 8)
- [ ] `README.txt` - Tested up to (line 6, if applicable)
- [ ] `changelog.txt` - New entry added at top (DO NOT remove old entries)
- [ ] `README.md` - Version references (if any)
- [ ] `languages/product-recommendation-quiz-for-ecommerce.pot` - Version (if modified)

---

## Step 3: Commit and Push to Git

### 3.1 Navigate to the Plugin Directory

```bash
cd "/Users/libertas/Local Sites/productrecommendationquiz/app/public/wp-content/plugins/product-recommendation-quiz-for-ecommerce"
```

### 3.2 Check Git Status

```bash
git status
```

Expected output should show modified files:
```
On branch master
Your branch is up to date with 'origin/master'.
Changes not staged for commit:
  modified: README.md
  modified: README.txt
  modified: changelog.txt
  modified: includes/class-product-recommendation-quiz-for-ecommerce.php
  modified: languages/product-recommendation-quiz-for-ecommerce.pot
  modified: product-recommendation-quiz-for-ecommerce.php
```

### 3.3 Stage and Commit Changes

```bash
git add -A
git commit -m "fix: [description of changes]"
```

**Commit Message Guidelines**:
- Use conventional commit format: `fix:`, `feat:`, `dev:`, `chore:`
- Be descriptive but concise
- Examples:
  - `fix: php error undefined array key host`
  - `feat: add new quiz template`
  - `dev: tested up to WP 6.9.0`

### 3.4 Push to Remote Repository

```bash
git push
```

Verify the push was successful:
```bash
git status
```

Expected output:
```
On branch master
Your branch is up to date with 'origin/master'.
nothing to commit, working tree clean
```

---

## Step 4: Copy Files to SVN Trunk

### 4.1 Locate SVN Directory

Navigate to the SVN working directory (typically named `prq-wp-plugin`).

**Note**: If you don't have the SVN directory checked out, you'll need to check it out first:
```bash
svn checkout https://plugins.svn.wordpress.org/product-recommendation-quiz-for-ecommerce prq-wp-plugin
```

### 4.2 Copy Modified Files

**CRITICAL**: Do NOT copy `.claude`, `.project` directories, or `CLAUDE.md` file - these are development/configuration files and should NOT be deployed to WordPress.org.

Copy the following files from your plugin directory to `prq-wp-plugin/trunk/`:

**Files/Directories to copy**:
- `product-recommendation-quiz-for-ecommerce.php`
- `README.txt`
- `README.md` (if modified)
- `changelog.txt`
- `LICENSE.txt`
- `uninstall.php`
- `index.php` (root and subdirectories)
- `includes/` directory (all files)
- `admin/` directory (all files)
- `public/` directory (all files)
- `languages/` directory (all files)
- `assets/` directory (all files)
- Any other plugin files

**Files/Directories to EXCLUDE** (DO NOT COPY):
- `.claude/` - Development configuration and skills
- `.project/` - Project documentation and SOPs
- `CLAUDE.md` - Development documentation
- `.git/` - Git repository
- `.gitignore` - Git configuration
- `.DS_Store` - macOS system files

**Method**: Use your file manager or command line to copy files. Ensure directory structure is preserved.

**Recommended command line method** (excludes development files automatically):
```bash
# From plugin directory, copy excluding .claude, .project, and CLAUDE.md
rsync -av --exclude='.claude' --exclude='.project' --exclude='CLAUDE.md' --exclude='.git' --exclude='.gitignore' --exclude='.DS_Store' \
  ./ prq-wp-plugin/trunk/
```

Or manually copy only the plugin directories:
```bash
cp -r admin/ includes/ public/ languages/ assets/ prq-wp-plugin/trunk/
cp product-recommendation-quiz-for-ecommerce.php README.txt README.md changelog.txt LICENSE.txt uninstall.php index.php prq-wp-plugin/trunk/
cp includes/index.php admin/index.php public/index.php prq-wp-plugin/trunk/includes/ prq-wp-plugin/trunk/admin/ prq-wp-plugin/trunk/public/
```

---

## Step 5: Commit to SVN Trunk

### 5.1 Navigate to SVN Directory

```bash
cd prq-wp-plugin
```

### 5.2 Check SVN Status

```bash
svn status
```

This will show which files have been modified or added.

### 5.3 Commit Changes

```bash
svn ci -m "Dev - tested up to WP 6.9.0"
```

**Commit Message Guidelines**:
- Use descriptive messages
- Include WordPress version tested if applicable
- Examples:
  - `Dev - tested up to WP 6.9.0`
  - `fix: php error undefined array key host`
  - `feat: add new quiz template`

### 5.4 Authentication

When prompted:
- **Username**: `revenuehunt`
- **Password**: Retrieve from KeePassXC (search for "svn")

Expected output:
```
Sending trunk/README.txt
Sending trunk/changelog.txt
Sending trunk/includes/class-product-recommendation-quiz-for-ecommerce.php
Sending trunk/languages/product-recommendation-quiz-for-ecommerce.pot
Sending trunk/product-recommendation-quiz-for-ecommerce.php
Transmitting file data ...done
Committing transaction...
Committed revision [REVISION_NUMBER].
```

---

## Step 6: Create and Commit SVN Tag

### 6.1 Create the Tag

Create a new tag by copying the trunk to the tags directory:

```bash
svn copy trunk tags/2.2.15
```

Replace `2.2.15` with your new version number.

Expected output:
```
A     tags/2.2.15
```

### 6.2 Verify Tag Creation

```bash
cd tags
ls
```

You should see your new tag listed along with previous versions.

### 6.3 Commit the Tag

```bash
cd ..
svn ci -m "Tag 2.2.15"
```

Replace `2.2.15` with your new version number.

Expected output:
```
Adding     tags/2.2.15
Replacing tags/2.2.15/README.txt
Replacing tags/2.2.15/admin/class-product-recommendation-quiz-for-ecommerce-admin.php
Replacing tags/2.2.15/admin/css/product-recommendation-quiz-for-ecommerce-admin.css
Replacing tags/2.2.15/changelog.txt
Replacing tags/2.2.15/includes/class-product-recommendation-quiz-for-ecommerce.php
Replacing tags/2.2.15/languages/product-recommendation-quiz-for-ecommerce.pot
Replacing tags/2.2.15/product-recommendation-quiz-for-ecommerce.php
Committing transaction...
Committed revision [REVISION_NUMBER].
```

---

## Step 7: Verify the Update

### 7.1 Check WordPress.org Plugin Page

Visit: https://wordpress.org/plugins/product-recommendation-quiz-for-ecommerce/

Verify:
- [ ] Version number matches your new version (e.g., `2.2.15`)
- [ ] "Last updated" timestamp shows recent update (may take a few minutes to appear)
- [ ] Changelog displays your new entry

### 7.2 Wait for Propagation

WordPress.org may take 5-15 minutes to process and display the update. If the version doesn't appear immediately, wait and refresh the page.

---

## Troubleshooting

### SVN Command Not Found

If you get `command not found: svn`, install Subversion:

```bash
brew install subversion
```

### Authentication Issues

- Ensure you're using the correct username: `revenuehunt`
- Retrieve password from KeePassXC (search for "svn")
- If authentication fails, verify credentials are correct

### Version Not Appearing on WordPress.org

- Wait 5-15 minutes for WordPress.org to process the update
- Verify the tag was created correctly: `svn list tags/`
- Check that the `Stable tag` in `README.txt` matches your tag name
- Ensure all files were committed to SVN

### Git Push Fails

- Check your Git credentials
- Verify you have push access to the repository
- Check for merge conflicts: `git pull` before pushing

### Files Not Updating in SVN

- Verify files were copied to the correct location (`prq-wp-plugin/trunk/`)
- Check SVN status: `svn status`
- Ensure you're committing from the correct directory

---

## Quick Reference: Version Update Locations

| File | Line/Location | What to Update |
|------|--------------|----------------|
| `product-recommendation-quiz-for-ecommerce.php` | Line 19 | `Version:` header |
| `product-recommendation-quiz-for-ecommerce.php` | Line 40 | `PRQ_PLUGIN_VERSION` constant |
| `README.txt` | Line 8 | `Stable tag:` |
| `README.txt` | Line 6 | `Tested up to:` (if applicable) |
| `changelog.txt` | Top of file | Add new entry (DO NOT remove old) |
| `README.md` | Various | Version references (if any) |
| `languages/*.pot` | Header | Version (if modified) |

---

## Complete Workflow Example

Here's a complete example workflow for updating from version `2.2.14` to `2.2.15`:

```bash
# 1. Make your edits (in your editor)

# 2. Update version numbers (in your editor)

# 3. Git workflow
cd "/Users/libertas/Local Sites/productrecommendationquiz/app/public/wp-content/plugins/product-recommendation-quiz-for-ecommerce"
git status
git add -A
git commit -m "dev: tested up to WP 6.9.0"
git push

# 4. Copy files to SVN trunk (EXCLUDE .claude and .project directories)

# 5. SVN trunk commit
cd prq-wp-plugin
svn ci -m "Dev - tested up to WP 6.9.0"

# 6. Create and commit tag
svn copy trunk tags/2.2.15
svn ci -m "Tag 2.2.15"

# 7. Verify at https://wordpress.org/plugins/product-recommendation-quiz-for-ecommerce/
```

---

## Notes

- **Changelog**: Always add new entries at the TOP of `changelog.txt`. Never remove old entries as they provide version history.
- **Version Format**: Use semantic versioning: `MAJOR.MINOR.PATCH` (e.g., `2.2.15`)
- **Commit Messages**: Use clear, descriptive commit messages that explain what changed
- **Testing**: Test your changes locally before committing
- **Backup**: Consider creating a backup or branch before making changes
- **WordPress.org Processing**: Allow 5-15 minutes for WordPress.org to process and display updates

---

## Related Resources

- Git Repository: https://github.com/RevenueHunt/product-recommendation-quiz-for-woocommerce
- WordPress.org Plugin Page: https://wordpress.org/plugins/product-recommendation-quiz-for-ecommerce/
- SVN Repository: https://plugins.svn.wordpress.org/product-recommendation-quiz-for-ecommerce/

---

**Last Updated**: 2025-01-XX  
**Current Plugin Version**: 2.2.14  
**SOP Version**: 1.0
