# Content tree

Source-of-truth markdown copies of every post and page on the WordPress site.

This directory is the **canonical version** of all content. WordPress is the render target; git is the version history. When something changes, you change it here first, then push to WordPress via the MCP workflow (see `docs/wordpress-mcp-workflow.md`).

## Directory layout

```
content/
├── README.md                 ← this file
├── _meta/                    ← cross-content data (taxonomy maps, redirect tables, image inventories)
│   ├── categories.json       ← WP category IDs ↔ slugs
│   ├── tags.json             ← WP tag IDs ↔ slugs
│   ├── redirects.csv         ← old slug → new slug 301 mapping
│   └── images-todo.csv       ← Squarespace CDN images pending re-upload to WP
├── _template.md              ← starter file for any new post
├── posts/                    ← all WP posts, grouped by category
│   ├── product-overview/     ← marquee product hub posts (EEi Overview, EE Books Overview…)
│   ├── method-books/         ← per-book pages (EE Band Book 1/2/3, Strings 1/2/3)
│   ├── features/             ← feature posts (SoundCheck, Music Studio, Video Assignments…)
│   ├── teaching-tips/        ← editorial / Teaching Tips long-form articles
│   ├── tutorials/            ← short how-to posts
│   ├── webinars/             ← webinar landing posts
│   ├── account-setup/        ← teacher / student account walk-throughs
│   ├── lead-gen/             ← perusal request + thank-you (set noindex)
│   └── archive/              ← retired / event-based content (giveaways, COVID-era pages)
└── pages/                    ← any actual WP `page` content type that survives (e.g. homepage, /faq/)
```

## File naming

One file per post/page. Filename = WP slug, extension = `.md`.

Example: post with slug `eei-overview-1` → `content/posts/product-overview/eei-overview-1.md`.

If a post is renamed, **rename the file too** and add a redirect entry in `_meta/redirects.csv`.

## File format

Every content file is **YAML frontmatter + body**. Frontmatter holds metadata, body is the WordPress block markup that goes into the post content.

```markdown
---
wp_id: 139                          # post/page ID in WordPress (set after first push)
wp_type: post                       # post | page
slug: eei-overview-1
status: draft                       # draft | publish | private
title: EEi Overview
date: 2026-04-30T11:45:38           # WP-side publish date
modified: 2026-04-30T11:45:38       # last sync timestamp

categories:
  - product-overview                # category slug; mapped to ID via _meta/categories.json
tags:
  - eei
  - music-studio

featured_media:
  wp_id: null                       # WP media ID once uploaded
  source_url: https://...           # local or remote source

excerpt: |
  Short excerpt text shown in archives and as fallback for OG description.

seo:
  jetpack_seo_html_title: "Essential Elements Interactive: Cloud Companion to EE Methods"
  advanced_seo_description: "..."   # 150–160 chars
  jetpack_seo_noindex: false
  og_image: https://...             # optional; falls back to featured_media

source:
  origin: squarespace               # squarespace | written | imported
  origin_url: https://www.eeiblog.com/eei-overview-1
  migration_pass: 1                 # 1 = AS-IS port, 2 = SEO pass, 3 = editorial rewrite

notes: |
  Free-text notes about migration decisions, deviations from rules, things to revisit.
---

<!-- wp:image -->
<figure class="wp-block-image">...</figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Body content as WordPress block markup.</p>
<!-- /wp:paragraph -->
```

## Why WordPress block markup in the body?

We store the body in the same form WordPress uses internally — `<!-- wp:paragraph -->`-style block comments. Three reasons:

1. **Round-trip safety.** When we pull a post from WP, the response IS this format. Storing it back in the same shape means no information loss between WP and git.
2. **Block-level metadata.** Things like `{"columns":4}` on a gallery, or `linkDestination:custom` on an image — pure markdown can't represent these. Block markup can.
3. **Theme compatibility.** Our custom `eeiblog-theme` may have block patterns or styles that depend on block attributes. Stripping to plain markdown loses them.

If a post is plain prose with no fancy blocks, the markup is barely more verbose than markdown. For complex pages it's a lot more, but it's faithful.

## Pull / push lifecycle

Standard flow when changing content:

1. **Edit the `.md` file** locally.
2. **Run the push** (manual MCP call — see `docs/wordpress-mcp-workflow.md`) to apply the change to WordPress as a draft (or publish, if explicitly approved).
3. **Verify** in WP admin / preview link.
4. **Commit** the `.md` change to git.

For initial seed of existing WP content into git: see the "Pull existing posts" section in the workflow doc.
