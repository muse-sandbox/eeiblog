---
# WordPress identifiers — fill after first push
wp_id: null
wp_type: post                # post | page
slug: my-new-post
status: draft                # draft | publish | private | pending
title: Replace with the post title

# Timestamps — auto-updated on push
date: null
modified: null

# Taxonomy — slugs; resolved to IDs via _meta/categories.json and _meta/tags.json
categories:
  - product-overview
tags: []

# Featured image — set wp_id after upload to media library
featured_media:
  wp_id: null
  source_url: null

# Short excerpt for archive views and OG fallback
excerpt: |
  One-paragraph excerpt, ~25 words.

# SEO metadata (Jetpack SEO Tools fields)
seo:
  jetpack_seo_html_title: ""           # <= 65 chars
  advanced_seo_description: ""         # 150–160 chars
  jetpack_seo_noindex: false
  og_image: null                       # optional override; falls back to featured_media

# Provenance
source:
  origin: written                      # squarespace | written | imported
  origin_url: null
  migration_pass: 1                    # 1 = AS-IS port, 2 = SEO pass, 3 = editorial rewrite

# Free-form notes — migration decisions, deviations from rules, follow-ups
notes: |
  ...
---

<!-- wp:paragraph -->
<p>Body content here in WordPress block markup. Replace this paragraph.</p>
<!-- /wp:paragraph -->
