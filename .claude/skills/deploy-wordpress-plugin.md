# WordPress Plugin Deployment Skill

Automate the complete deployment workflow for the Product Recommendation Quiz for eCommerce WordPress plugin, including version updates, Git commits, and SVN deployment to WordPress.org.

## When to Use

- After completing code changes that are ready for release
- When user requests deployment or version bump
- Before releasing to WordPress.org plugin repository
- When triggered by `/deploy` command

## Prerequisites

Before starting, verify:
- [ ] All code changes are complete and tested
- [ ] Code review has been performed (run `/review-staged` if needed)
- [ ] User has confirmed readiness to deploy
- [ ] SVN credentials are available (username: `revenuehunt`, password in KeePassXC)

## Deployment Workflow

### Phase 1: Determine New Version Number

1. **Read current version** from `product-recommendation-quiz-for-ecommerce.php`:
   - Line 19: `Version:` header
   - Line 40: `PRQ_PLUGIN_VERSION` constant

2. **Calculate next version** using semantic versioning:
   - Bug fix → increment PATCH (e.g., `2.2.14` → `2.2.15`)
   - New feature → increment MINOR (e.g., `2.2.14` → `2.3.0`)
   - Breaking change → increment MAJOR (e.g., `2.2.14` → `3.0.0`)

3. **Ask user to confirm** the new version number if uncertain

### Phase 2: Update Version Numbers

Update version in these files (in order):

#### A. Main Plugin File
**File**: `product-recommendation-quiz-for-ecommerce.php`

- Line 19: Update `Version:` header
- Line 40: Update `PRQ_PLUGIN_VERSION` constant

#### B. README.txt
**File**: `README.txt`

- Line 8: Update `Stable tag:`
- Line 6: Update `Tested up to:` (if WordPress version changed)

#### C. Changelog
**File**: `changelog.txt`

**CRITICAL**: Add a NEW entry at the TOP of the file. DO NOT remove old entries.

Format:
```markdown
*** Product Recommendation Quiz for WooCommerce ***

YYYY-MM-DD - version X.X.X
* Description of changes

[Previous entries remain below...]
```

Example:
```markdown
2025-01-15 - version 2.2.15
* Dev - tested up to WP 6.9.0 and WooCommerce up to Version 10.3.0
```

#### D. README.md (if modified)
**File**: `README.md`

Check for any version references and update if present.

#### E. Language File (if modified)
**File**: `languages/product-recommendation-quiz-for-ecommerce.pot`

Update version in header if the file was modified.

### Phase 3: Verify Changes

Before proceeding, verify all version updates:

1. **Check version consistency**:
   ```bash
   grep -r "2.2.14" product-recommendation-quiz-for-ecommerce.php README.txt
   ```
   Should return no results (or only in comments/changelog history)

2. **Verify changelog entry**:
   - New entry is at the top
   - Old entries are preserved
   - Date format is correct (YYYY-MM-DD)

3. **Check for missed files**:
   - Review git status to see all modified files
   - Ensure no version references were missed

### Phase 4: Git Workflow

**IMPORTANT**: According to `.claude/rules/development.md`, agents should NOT perform git write operations. However, for deployment, we need to guide the user through the process.

#### Step 1: Show Git Status

```bash
git status
```

Display the output to the user showing what will be committed.

#### Step 2: Provide Git Commands

Provide the user with the exact commands to run:

```bash
# Stage all changes
git add -A

# Commit with descriptive message
git commit -m "dev: tested up to WP 6.9.0"

# Push to remote
git push
```

**Commit Message Guidelines**:
- Use conventional commit format: `fix:`, `feat:`, `dev:`, `chore:`
- Be descriptive: `fix: php error undefined array key host`
- Include context: `dev: tested up to WP 6.9.0`

#### Step 3: Wait for User Confirmation

Wait for the user to confirm:
- [ ] Git commit completed successfully
- [ ] Git push completed successfully

### Phase 5: SVN Deployment

**IMPORTANT**: SVN operations require user interaction for authentication. Guide the user through the process.

#### Step 1: Locate SVN Directory

The SVN working directory should be named `prq-wp-plugin` (typically in a parent directory or separate location).

If not checked out:
```bash
svn checkout https://plugins.svn.wordpress.org/product-recommendation-quiz-for-ecommerce prq-wp-plugin
```

#### Step 2: Copy Files to SVN Trunk

**CRITICAL**: Do NOT copy `.claude` or `.project` directories - these are development/configuration files and should NOT be deployed.

**Files to copy** from plugin directory to `prq-wp-plugin/trunk/`:

- `product-recommendation-quiz-for-ecommerce.php`
- `README.txt`
- `README.md` (if modified)
- `changelog.txt`
- `includes/` directory (all files)
- `admin/` directory (all files)
- `public/` directory (all files)
- `languages/` directory (all files)
- `assets/` directory (all files)
- Any other plugin files

**Files/Directories to EXCLUDE**:
- `.claude/` - Development configuration (DO NOT COPY)
- `.project/` - Project documentation (DO NOT COPY)
- `.git/` - Git repository (DO NOT COPY)
- `.gitignore` - Git configuration (DO NOT COPY)
- `.DS_Store` - macOS system files (DO NOT COPY)

**Method**: Use file manager or command line. Preserve directory structure.

**Command line method** (recommended to avoid copying excluded files):
```bash
# From plugin directory, copy excluding .claude and .project
rsync -av --exclude='.claude' --exclude='.project' --exclude='.git' --exclude='.gitignore' --exclude='.DS_Store' \
  ./ prq-wp-plugin/trunk/
```

Or manually copy only the plugin directories:
```bash
cp -r admin/ includes/ public/ languages/ assets/ prq-wp-plugin/trunk/
cp product-recommendation-quiz-for-ecommerce.php README.txt README.md changelog.txt LICENSE.txt uninstall.php index.php prq-wp-plugin/trunk/
```

#### Step 3: Commit to SVN Trunk

Provide commands for user to run:

```bash
cd prq-wp-plugin
svn status
svn ci -m "Dev - tested up to WP 6.9.0"
```

**Authentication**:
- Username: `revenuehunt`
- Password: Retrieve from KeePassXC (search for "svn")

**Expected Output**:
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

#### Step 4: Create SVN Tag

Provide commands:

```bash
# Create tag
svn copy trunk tags/2.2.15

# Commit tag
svn ci -m "Tag 2.2.15"
```

Replace `2.2.15` with the new version number.

**Expected Output**:
```
Adding     tags/2.2.15
Replacing tags/2.2.15/README.txt
...
Committing transaction...
Committed revision [REVISION_NUMBER].
```

### Phase 6: Verification

#### Step 1: Verify on WordPress.org

Visit: https://wordpress.org/plugins/product-recommendation-quiz-for-ecommerce/

Check:
- [ ] Version number matches new version
- [ ] "Last updated" timestamp is recent (may take 5-15 minutes)
- [ ] Changelog displays new entry

#### Step 2: Wait for Propagation

WordPress.org may take 5-15 minutes to process updates. If version doesn't appear immediately:
- Wait 5-10 minutes
- Refresh the page
- Check SVN tags: `svn list tags/` to verify tag exists

## Error Handling

### SVN Command Not Found

If user gets `command not found: svn`:
```bash
brew install subversion
```

### Authentication Issues

- Verify username: `revenuehunt`
- Check password in KeePassXC
- Ensure SVN credentials are correct

### Version Not Appearing

- Wait 5-15 minutes for WordPress.org processing
- Verify tag was created: `svn list tags/`
- Check `Stable tag` in `README.txt` matches tag name
- Ensure all files were committed to SVN

### Git Issues

- Check git credentials
- Verify push access to repository
- Check for merge conflicts: `git pull` before pushing

## Output Format

When deployment is complete, provide a summary:

```markdown
## Deployment Complete ✅

### Version Updated
- From: 2.2.14
- To: 2.2.15

### Files Updated
- ✅ product-recommendation-quiz-for-ecommerce.php
- ✅ README.txt
- ✅ changelog.txt
- ✅ [other files]

### Git Status
- ✅ Committed: [commit message]
- ✅ Pushed to: origin/master

### SVN Status
- ✅ Trunk committed: revision [NUMBER]
- ✅ Tag created: tags/2.2.15
- ✅ Tag committed: revision [NUMBER]

### WordPress.org
- 🔗 Plugin page: https://wordpress.org/plugins/product-recommendation-quiz-for-ecommerce/
- ⏳ Update visible: [Yes/No - may take 5-15 minutes]
```

## Notes

- **Changelog**: Always add new entries at TOP, never remove old entries
- **Version Format**: Use semantic versioning (MAJOR.MINOR.PATCH)
- **User Interaction**: Git and SVN write operations require user to execute commands
- **Verification**: Always verify version appears on WordPress.org after deployment
- **Timing**: Allow 5-15 minutes for WordPress.org to process updates

## Related Resources

- SOP Document: `SOP_Update-WordPress-Plugin.md`
- Git Repository: https://github.com/RevenueHunt/product-recommendation-quiz-for-woocommerce
- WordPress.org Plugin: https://wordpress.org/plugins/product-recommendation-quiz-for-ecommerce/
- SVN Repository: https://plugins.svn.wordpress.org/product-recommendation-quiz-for-ecommerce/
