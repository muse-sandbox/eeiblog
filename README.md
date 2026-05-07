# eeiblog.com

Migration of [eeiblog.com](https://eeiblog.com) from Squarespace to WordPress, with SEO cleanup and a content rewrite layer for new articles.

WordPress site lives at `https://rkislenok-xwlsc.wpcomstaging.com` (staging) and will move to `eeiblog.com` once mapped.

## Repo layout

```
eeiblog.com/
├── README.md                       ← this file
├── MIGRATION-SEO-PLAN.md           ← audit + 5-phase migration plan
├── wordpress-inventory.json        ← snapshot of all 119 pages + 4 posts in WP
│
├── content/                        ← canonical markdown source-of-truth
│   ├── README.md                   ← directory layout, file format, frontmatter schema
│   ├── _template.md                ← starter for new posts
│   ├── _meta/
│   │   ├── categories.json         ← WP category IDs ↔ slugs
│   │   ├── tags.json               ← WP tag IDs ↔ slugs
│   │   ├── redirects.csv           ← old slug → new slug 301 mapping
│   │   └── images-todo.csv         ← Squarespace CDN images pending re-upload
│   ├── posts/
│   │   ├── product-overview/       ← marquee product hub posts
│   │   ├── method-books/
│   │   ├── features/
│   │   ├── teaching-tips/
│   │   ├── tutorials/
│   │   ├── webinars/
│   │   ├── account-setup/
│   │   ├── lead-gen/               ← perusal-request + thank-you (noindex)
│   │   └── archive/                ← retired / event-based content
│   └── pages/                      ← any WP `page`-type content kept (homepage, /faq/)
│
├── content-rules/                  ← writing rulebook for new articles
│   ├── README.md
│   ├── eei-rules.md                ← structure, voice, CTAs, internal-link rules
│   ├── eei-abbreviations.md        ← curated acronyms for EE / EEi copy
│   ├── eei-communications-problems.md  ← AI hallucinations / corrections to avoid
│   └── source/                     ← original Notion exports (reference only)
│
├── docs/
│   └── wordpress-mcp-workflow.md   ← how to read/create/update content via MCP
│
├── migrations/                     ← per-page working drafts and notes
│   └── eei-overview/               ← pilot
│
├── wordpress-theme/                ← custom theme matching the original eeiblog.com
│   └── eeiblog-theme/
│
├── EEI_Banner_June2025.webp        ← hero banner asset
└── EE_i_WHITE.webp                 ← logo asset
```

## How to read this repo

If you want to:

- **Understand the migration plan** → start with `MIGRATION-SEO-PLAN.md`.
- **Write a new article** → read `content-rules/README.md`, then `eei-rules.md`.
- **Add or change content** → read `content/README.md` for the file format, then `docs/wordpress-mcp-workflow.md` for how to push it to WordPress.
- **Touch the theme** → see `wordpress-theme/eeiblog-theme/`.
- **See current WP state** → `wordpress-inventory.json` (run a fresh pull when stale).

## Workflow in one sentence

Edit `.md` in `content/`, push to WordPress as a draft via the MCP workflow, review in WP admin, publish, commit.

## Status

- Site: Coming Soon (not launched).
- Pages: 119 migrated from Squarespace as `page` type, mostly clean slugs.
- Posts: 4 (Hello World + 3 EE pilot posts with date-prefixed slugs to clean up).
- SEO metadata: empty across the board — being filled in incrementally.
- First pilot post: `eei-overview-1` (WP ID 139, draft) — created via the new content tree.

## Installing the theme

1. Grab the latest pre-built archive from `wordpress-theme/eeiblog-theme-<version>.zip` (currently `1.0.30`). To rebuild from source: `cd wordpress-theme && zip -r eeiblog-theme-<version>.zip eeiblog-theme/ -x '*.DS_Store' '*/CLAUDE-CODE-TASKS.md'`. **The zip must contain a top-level `eeiblog-theme/` folder** — otherwise WP unzips into a directory named after the zip filename and every release becomes a new theme instead of an update.
2. **Switch off the active theme first.** WordPress.com refuses to overwrite the active theme on upload — instead it appends `-1`, `-2`, etc. to the directory name and you end up with a zoo. Workflow per release: **Appearance → Themes** → activate any default (Twenty Twenty-Four etc.) → hover the old `eeiblog-theme` and **Theme Details → Delete** → **Add New → Upload Theme** → upload the new zip → **Activate**. (On Atomic plans you can skip this dance via SFTP/SSH; Simple plans don't have that option.)
3. The first activation seeds Primary + Footer nav menus automatically (idempotent — won't overwrite later edits).
4. Configure menus (**Appearance → Menus**): create *Primary* and *Footer* menus.
5. Upload site logo via **Appearance → Customize → Site Identity**.
6. Set hero image via **Appearance → Customize → Header Image**.

## Theme structure

```
wordpress-theme/eeiblog-theme/
├── style.css          # theme metadata + all styles
├── functions.php      # setup, menus, widgets, helpers
├── header.php         # dark site header + primary nav (with dropdowns)
├── footer.php         # footer with logo, copyright, footer nav
├── front-page.php     # homepage: hero + feature boxes + recent posts
├── home.php           # blog listing (list layout)
├── single.php         # single post: title, featured image, content, tags, prev/next
├── archive.php        # category/tag/author/date archives
├── page.php           # static pages
├── 404.php
├── sidebar.php
├── searchform.php
├── index.php          # fallback template
├── screenshot.png
└── assets/assets/images/js/navigation.js  # mobile menu + accessible dropdowns
```
