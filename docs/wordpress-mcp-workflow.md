# WordPress MCP workflow

How to read, create, update, and delete content on `rkislenok-xwlsc.wpcomstaging.com` (Essential Elements Interactive blog) via the WordPress.com MCP connector.

This is the canonical reference for any LLM (or human) doing migration work in this repo. It assumes the MCP server `wpcom-mcp` is connected and the operator has access to the `rkislenok-xwlsc.wpcomstaging.com` site.

---

## Site identifiers

- **Site URL:** `https://rkislenok-xwlsc.wpcomstaging.com` (staging)
- **Future custom domain:** `eeiblog.com` (not yet mapped)
- **Blog ID:** `254376041`
- **Active theme:** `eeiblog-theme` (custom, source in `wordpress-theme/eeiblog-theme/`)
- **Site visibility:** Coming Soon (will launch after migration)

---

## MCP connector landscape

There are five top-level tools in the WordPress.com MCP. Use the one that matches the surface you're touching.

| Tool | Purpose |
|---|---|
| `wpcom-mcp-user-sites` | List sites the user has access to (rarely needed once we know the site ID). |
| `wpcom-mcp-account` | Account-level operations (profile, notifications). Out of scope for migration. |
| `wpcom-mcp-site` | Site-level config: settings, plugins, users, activity, manage-site (launch / visibility). |
| `wpcom-mcp-content-authoring` | The big one. Posts, pages, media, comments, categories, tags, patterns. |
| `wpcom-mcp-site-editor-context` | Theme presets, block types, style variations. Use when composing pages with blocks. |

Each tool has three actions: `list` (discover operations), `describe` (show schema), `execute` (run).

**Always run `describe` before the first call to a new operation** — schemas change and "the docs say X" is not a substitute for the actual schema.

---

## Operation availability (current state)

The following table reflects the configured MCP permissions as of 2026-04-30. Re-verify by running `wpcom-mcp-content-authoring action=list` if behaviour changes.

| Operation | Status | Notes |
|---|---|---|
| `posts.create` | ✅ | Always default `status: "draft"`. |
| `posts.update` | ✅ | |
| `posts.get` / `posts.list` | ✅ | |
| `posts.delete` | ❌ disabled | Use `posts.update {status: "trash"}` as workaround. |
| `pages.create` | ✅ | Same draft-default rule. |
| `pages.update` | ✅ | |
| `pages.get` / `pages.list` | ✅ | |
| `pages.delete` | ✅ | Moves to trash (recoverable for 30 days). |
| `categories.create/update/delete` | ✅ | Delete is permanent (no trash). |
| `tags.create/update` | ✅ | |
| `tags.delete` | ❌ disabled | Workaround: leave unused tags. |
| `media.update` | ✅ | Updates alt_text/caption only. No upload via MCP — must upload via WP admin. |
| `media.delete` | ❌ disabled | |
| `media.list` / `media.get` | ✅ | |
| `comments.*` | ❌ all disabled | |
| `settings.update` | ✅ | But limited — does NOT expose `permalink_structure`. |
| `manage-site.launch` | ✅ | Used to flip Coming Soon → live. |
| `theme.list/set` | 🚫 Automatticians only | Theme management goes via WP admin. |
| `plugins.list` | ❌ not exposed | Plugin management goes via WP admin. |

### Implications for the workflow

- **Image upload requires WP admin** (no MCP path). Plan to do image batches manually in `wp-admin → Media → Add New`.
- **Permalink structure change requires WP admin** (`Settings → Permalinks`).
- **Plugin install (e.g., Redirection) requires WP admin** (`Plugins → Add New`).
- **Comments management requires WP admin**.
- For deletes that aren't supported by MCP (`posts.delete`, `media.delete`, `tags.delete`), use the workaround of moving to trash via `update`, or do it from WP admin.

---

## Authoring a new post (the standard flow)

End-to-end recipe for "I have a new article ready in markdown; put it on the site as a draft."

### 0. Pre-requisites

- Article exists in `content/posts/<category>/<slug>.md` with valid frontmatter.
- Category referenced in frontmatter exists in WP and is recorded in `content/_meta/categories.json`. If not, run "Create category" recipe below first.

### 1. Read the frontmatter and body

Parse the file. Frontmatter is YAML; body is everything after the second `---`.

### 2. Verify category and tag IDs

Look up the IDs from `content/_meta/categories.json` and `tags.json`. If missing:

- For each missing category, call `categories.create` (see "Create category").
- For each missing tag, call `tags.create` (see "Create tag").
- Update the JSON files with the new IDs.

### 3. Call `posts.create`

Tool: `wpcom-mcp-content-authoring`, action `execute`, operation `posts.create`.

Required params:

```json
{
  "wpcom_site": "rkislenok-xwlsc.wpcomstaging.com",
  "operation": "posts.create",
  "params": {
    "title":   {"raw": "<title from frontmatter>"},
    "content": {"raw": "<body of the .md file>"},
    "excerpt": {"raw": "<excerpt from frontmatter>"},
    "slug":    "<slug from frontmatter>",
    "status":  "draft",
    "categories": [<id1>, <id2>],
    "tags":    [<id1>, <id2>],
    "meta": {
      "jetpack_seo_html_title":   "<seo.jetpack_seo_html_title>",
      "advanced_seo_description": "<seo.advanced_seo_description>",
      "jetpack_seo_noindex":      false
    },
    "user_confirmed": true
  }
}
```

**Response includes the post ID** (`data.id`). Capture it.

### 4. Update the frontmatter

Write the response data back into `content/posts/<category>/<slug>.md`:

- `wp_id: <data.id>`
- `date: <data.date>`
- `modified: <data.modified>`

Commit to git with a message like `feat(content): add eei-overview-1 post (draft, wp_id=139)`.

### 5. Show preview

Always surface these two URLs after a successful create:

- **Edit:** `https://rkislenok-xwlsc.wpcomstaging.com/wp-admin/post.php?post=<id>&action=edit`
- **Preview:** `https://rkislenok-xwlsc.wpcomstaging.com/?p=<id>&preview=true`

### 6. Publish (after review)

When Roman approves the draft:

```json
{
  "operation": "posts.update",
  "params": {
    "id": <wp_id>,
    "status": "publish",
    "user_confirmed": true
  }
}
```

Then update frontmatter `status: publish` and commit.

---

## Updating an existing post

When the `.md` file changes:

```json
{
  "operation": "posts.update",
  "params": {
    "id": <wp_id from frontmatter>,
    "title":   {"raw": "<title>"},
    "content": {"raw": "<body>"},
    "excerpt": {"raw": "<excerpt>"},
    "slug":    "<slug>",
    "categories": [...],
    "tags":    [...],
    "meta": {
      "jetpack_seo_html_title":   "...",
      "advanced_seo_description": "...",
      "jetpack_seo_noindex":      false
    },
    "user_confirmed": true
  }
}
```

**Pass only the fields that changed** — `posts.update` is partial. Sending unchanged fields is harmless but wastes tokens.

⚠️ If `status: publish`, the change goes LIVE IMMEDIATELY. For risky edits, switch the post back to draft first, then update, then re-publish.

---

## Pulling existing WP content into git

For initial seed of the 119 already-migrated pages and 4 posts:

1. **List**: `pages.list` with `status: "publish,draft,pending,private"`, paginate (`per_page: 100`), use `include_fields` to keep responses small.
2. **For each item, fetch full content**: `pages.get` with `id: <id>`, `include_fields: ["id","slug","title","status","date","modified","link","content","excerpt","categories","tags","featured_media","meta"]`.
3. **Write to disk**: build `content/posts/<category-guess>/<slug>.md` with frontmatter populated from the response and body = `data.content.raw` (or `.rendered` if raw missing).
4. **Commit**.

Tip: do this in batches of 10–20 to avoid rate-limit hits. The MCP server returns `429`-style errors as `"The connector's server is rate-limiting requests"`. Sleep ~10s and retry.

---

## Renaming a slug (with redirect)

When a post slug changes, two operations:

1. `posts.update` with new `slug`.
2. Add a row to `content/_meta/redirects.csv`:
   ```
   /<old-slug>/,/<new-slug>/,301,Reason for rename,<YYYY-MM-DD>
   ```

**Warning:** redirects only take effect once the Redirection plugin (or equivalent) is installed in WP and configured to read from CSV / a redirect API. See "Manual WP-admin steps" below.

---

## Trashing / deleting

- **Pages:** `pages.delete` moves to trash. Recoverable from `wp-admin → Pages → Trash` for 30 days.
- **Posts:** `posts.delete` is disabled. Workaround:
  ```json
  {"operation": "posts.update", "params": {"id": <id>, "status": "trash", "user_confirmed": true}}
  ```
- **Media:** can't delete via MCP. Use WP admin.
- **Categories:** `categories.delete` is permanent (no trash). Posts in the category go to Uncategorized.
- **Tags:** `tags.delete` is disabled. Leave unused.

After delete, also update the `.md` file:

- Move it to `content/posts/archive/<slug>.md` if we want to keep history.
- Or delete it and let git history hold the record.

---

## Creating a category

```json
{
  "operation": "categories.create",
  "params": {
    "name":        "Teaching Tips",
    "slug":        "teaching-tips",
    "description": "Long-form articles for music educators teaching with Essential Elements.",
    "parent":      0,
    "user_confirmed": true
  }
}
```

After success, **append the result to `content/_meta/categories.json`**:

```json
{
  "id": <returned id>,
  "name": "Teaching Tips",
  "slug": "teaching-tips",
  "parent": 0,
  "description": "..."
}
```

---

## Creating a tag

```json
{
  "operation": "tags.create",
  "params": {
    "name":        "music-studio",
    "slug":        "music-studio",
    "description": "Posts about the EEi Music Studio feature.",
    "user_confirmed": true
  }
}
```

After success, append to `content/_meta/tags.json`.

---

## Updating image alt-text in WP media library

```json
{
  "operation": "media.update",
  "params": {
    "id":        <media_id>,
    "alt_text":  "Music Studio screen showing exercise notation with controls",
    "caption":   "Optional caption",
    "user_confirmed": true
  }
}
```

This updates the alt-text everywhere the image is used **immediately**. No re-publish needed.

---

## Site settings (limited)

Available fields via `settings.update`:

- `blogname` — site title
- `blogdescription` — tagline
- `blog_public` — visibility (1 public, 0 discourage search, -1 private)
- `timezone_string`, `date_format`, `time_format`, `start_of_week`
- `default_role`, `users_can_register`, `comment_registration`

**NOT** available via MCP:

- `permalink_structure` — change in `wp-admin → Settings → Permalinks`
- `siteurl` / `home` — domain mapping is a separate WP.com flow

---

## Site launch (Coming Soon → live)

```json
{
  "tool":      "wpcom-mcp-site",
  "operation": "manage-site.launch",
  "params":    {"action": "launch", "user_confirmed": true}
}
```

Pre-flight before calling launch:

- All draft posts that should be public are published.
- Junk / legacy pages trashed.
- Permalink structure set to `/%postname%/`.
- SEO metadata filled on the top 30 priority pages.
- Sitemap accessible at `/sitemap.xml` (Jetpack auto-generates).
- Tagline (`blogdescription`) set.
- Site icon / favicon uploaded.
- Custom domain mapping is configured (or we accept the staging URL going live first).

---

## Manual WP-admin steps

Some things the MCP can't do — Roman has to do these in `wp-admin`. URLs assume the staging hostname; replace once the custom domain is mapped.

| Task | Where | When |
|---|---|---|
| Change permalink structure to `/%postname%/` | `wp-admin/options-permalink.php` → "Post name" | Before publishing the first post |
| Install **Redirection** plugin (John Godley) | `wp-admin/plugin-install.php?s=redirection&tab=search&type=term` | Before any slug rename |
| Upload images to media library (batch) | `wp-admin/upload.php` → Add New | Before publishing posts that use them |
| Set site logo | `wp-admin/customize.php` → Site Identity | Before launch |
| Configure menus | `wp-admin/nav-menus.php` | Before launch |
| Verify Jetpack SEO Tools active | `wp-admin/admin.php?page=jetpack#/settings` → Traffic | Before publishing first post |
| Connect Google Analytics / Search Console | `wp-admin/admin.php?page=jetpack#/settings` → Traffic | After launch |

---

## Anti-patterns (don't do)

- ❌ **Don't `posts.create` without `user_confirmed: true`.** The safety policy rejects it.
- ❌ **Don't include `_crdt_document` in `meta` updates.** It's a CRDT internal blob — leave it alone.
- ❌ **Don't pass `categories` or `tags` as strings.** They must be arrays of integer IDs.
- ❌ **Don't publish without showing the preview link first.** Roman wants a chance to review.
- ❌ **Don't mass-delete without trashing first.** Build the trash first, verify, then commit to permanent.
- ❌ **Don't trust slug uniqueness assumptions.** Pages and posts share the same URL space if permalink is `/%postname%/`. A page slug `foo` and post slug `foo` will collide.
- ❌ **Don't use `<!-- wp:gallery -->` with bare `<figure class="wp-block-image">` children.** WP's block validator strips them and you get an empty gallery shell. Two paths that work:
  1. **Stack of `<!-- wp:image -->` blocks** (vertical layout, no media library required) — most reliable.
  2. **Proper gallery with media IDs** — upload images to WP media library first, then write `<!-- wp:gallery -->` with inner `<!-- wp:image {"id":<media_id>} -->` blocks. Until media IDs exist, the gallery block can't render.
- ❌ **Don't try to update large content payloads via MCP rapid-fire.** The connector sometimes times out / rate-limits on big `posts.update` calls. If it fails, wait 30+ seconds before retrying, or split the change into multiple smaller updates.

---

## Troubleshooting

### "The connector's server is rate-limiting requests"

Wait 8–15 seconds and retry. Don't retry immediately in a tight loop. For batch operations (e.g., creating 20 categories), space them by ~2s.

### "Permission denied: Operation 'X' is not supported"

Either the operation is disabled in the MCP settings (check the disabled list in `wpcom-mcp-content-authoring action=list`), or it's restricted to Automatticians (some `theme.*` operations).

### `_content_warnings` field in response

WordPress modified or stripped your content during save. Check the field — it lists exactly which blocks or HTML elements got dropped. Common cause: unsupported block markup or HTML the kses filter rejects. Retry with simpler markup.

### Featured image won't update

`featured_media` takes the **WP media ID** (integer), not a URL. If the image isn't in the media library yet, upload via WP admin first, then call `posts.update` with the resulting media ID.

### Slug shows up with `-2` or `-draft` suffix

WP auto-appends a suffix when the slug collides with an existing post or page. To take over a slug:

1. Trash the existing item (page or post) holding the slug.
2. `posts.update` with the desired clean slug. WP will re-assign without suffix once the conflict is gone.

---

## Quick reference: file → MCP call mapping

For a content file `content/posts/<category>/<slug>.md` with frontmatter `wp_id: <ID>`:

| Action | MCP call |
|---|---|
| Create new (no `wp_id` yet) | `posts.create` → capture ID, write back to frontmatter |
| Edit body or fields | `posts.update {id: <ID>, ...changed fields...}` |
| Move to trash | `posts.update {id: <ID>, status: "trash"}` |
| Permanent delete | Not supported via MCP — use wp-admin Trash → Delete Permanently |
| Read for diff | `posts.get {id: <ID>}` |

For pages, swap `posts.*` → `pages.*` (and `pages.delete` is supported, unlike `posts.delete`).
