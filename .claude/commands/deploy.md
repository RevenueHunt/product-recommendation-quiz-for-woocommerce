---
description: Deploy WordPress plugin update - version bump, Git commit, SVN release
allowed-tools: ['Read', 'Grep', 'Search', 'Write', 'Bash']
---

# Deploy WordPress Plugin

Deploy the Product Recommendation Quiz for eCommerce plugin update to WordPress.org.

## Procedure

### Step 1: Invoke Deployment Skill

Use the `deploy-wordpress-plugin` skill to guide the deployment process.

**Skill Location**: `.claude/skills/deploy-wordpress-plugin.md`

### Step 2: Determine Version

1. Read current version from `product-recommendation-quiz-for-ecommerce.php`:
   ```bash
   grep "Version:" product-recommendation-quiz-for-ecommerce.php | head -1
   grep "PRQ_PLUGIN_VERSION" product-recommendation-quiz-for-ecommerce.php
   ```

2. Calculate next version:
   - Ask user: "What type of change is this? (bug fix / new feature / breaking change)"
   - Or infer from git changes if user doesn't specify
   - Default to patch increment if uncertain

3. Confirm version with user before proceeding

### Step 3: Update Version Numbers

Follow the skill's Phase 2 to update all version references:

1. Update `product-recommendation-quiz-for-ecommerce.php` (lines 19 and 40)
2. Update `README.txt` (line 8, and line 6 if WP version changed)
3. Add new entry to `changelog.txt` at the top
4. Check `README.md` for version references
5. Check language file if modified

### Step 4: Verify Changes

Before proceeding, verify:
- All version numbers are consistent
- Changelog entry is properly formatted
- No old version numbers remain (except in changelog history)

### Step 5: Guide Git Workflow

**IMPORTANT**: Do NOT execute git write commands. Guide the user.

1. Show git status:
   ```bash
   git status
   ```

2. Provide exact commands for user to run:
   ```bash
   git add -A
   git commit -m "[descriptive message]"
   git push
   ```

3. Wait for user confirmation that git operations completed

### Step 6: Guide SVN Deployment

**IMPORTANT**: SVN requires user authentication. Guide the user through the process.

**CRITICAL**: Do NOT copy `.claude` or `.project` directories - these are development files and should NOT be deployed.

1. Verify SVN directory exists (`prq-wp-plugin`)
2. Provide instructions for copying files to `prq-wp-plugin/trunk/`:
   - **EXCLUDE**: `.claude/`, `.project/`, `.git/`, `.gitignore`, `.DS_Store`
   - **INCLUDE**: All plugin directories (`admin/`, `includes/`, `public/`, `languages/`, `assets/`) and plugin files
   - Provide rsync command or manual copy instructions
3. Provide SVN commit command:
   ```bash
   cd prq-wp-plugin
   svn ci -m "Dev - tested up to WP X.X.X"
   ```
4. Provide tag creation commands:
   ```bash
   svn copy trunk tags/[VERSION]
   svn ci -m "Tag [VERSION]"
   ```
5. Wait for user confirmation

### Step 7: Verify Deployment

1. Check WordPress.org plugin page:
   - URL: https://wordpress.org/plugins/product-recommendation-quiz-for-ecommerce/
   - Verify version number
   - Verify "Last updated" timestamp
   - Note: May take 5-15 minutes to appear

2. Provide summary of deployment status

## Output Format

Provide a clear, step-by-step summary:

```markdown
## Deployment Workflow

### Current Status
- Current version: [X.X.X]
- New version: [X.X.X]
- Change type: [bug fix / feature / breaking]

### Step 1: Version Updates ✅
- [x] Updated product-recommendation-quiz-for-ecommerce.php
- [x] Updated README.txt
- [x] Added changelog entry

### Step 2: Git Operations
Please run these commands:
```bash
git add -A
git commit -m "[message]"
git push
```

### Step 3: SVN Deployment
[Instructions for SVN operations]

### Step 4: Verification
[Link to WordPress.org plugin page]
```

## Error Handling

If any step fails:
1. Identify the error
2. Check troubleshooting section in the skill
3. Provide specific fix instructions
4. Wait for user to resolve before continuing

## Notes

- **User executes commands**: Git and SVN write operations must be done by the user
- **Authentication**: SVN requires username `revenuehunt` and password from KeePassXC
- **Verification**: Always verify the update appears on WordPress.org
- **Timing**: WordPress.org may take 5-15 minutes to process updates
