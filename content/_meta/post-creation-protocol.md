# Post creation protocol

When creating a new post on eeiblog.com — whether via WP-admin or via Claude through MCP — every post MUST be tagged with two distinct dimensions before publish:

1. **Topic-category** — what the post is about. Pick one (or more if genuinely cross-topic) from:
   - Teaching Tips
   - Features
   - Method Books
   - News
   - Account Setup
   - Tutorials
   - Product Overview
   - Webinars
   - Lead Generation
   - Archive (deprecated, do not put new posts here)

2. **Audience-category** — who the post is for. Pick exactly one of:
   - **For Teachers** — content primarily for music educators (classroom mgmt, rehearsal pedagogy, teacher account workflows, method-book selection)
   - **For Students** — content primarily for students/parents (creating student accounts, submitting recordings, using Music Studio as a learner)
   - **News** — product announcements, feature releases, partnerships, holiday/seasonal content, events. (Note: News is also a topic-category — for news posts, use ONLY the News category and skip the audience dimension; news appears in its own homepage section.)
   - **Mixed** — content relevant to both audiences. Apply BOTH `For Teachers` AND `For Students` categories (no separate "Mixed" category exists; assigning both is the way to express "for everyone").

## Workflow when Claude is asked to create or edit a post

Claude must always:
1. Suggest a default topic-category and audience-category based on the title/content.
2. Ask the user to confirm or override before creating the post.
3. Never publish in `Uncategorized` — that's a smell from broken migration, not a valid state.
4. **Include a hand-crafted `excerpt` field in every `posts.create` and every `posts.update` payload** — not optional, not deferred. See "Excerpt rule" below for the spec. When EDITING an existing post (any reason — fix typo, replace image, restructure body), regenerate the excerpt from the new content if the body changed materially, and include it in the same `posts.update` call. Never let WP auto-generate or leave a stale excerpt across edits.
5. Apply the External link rule, Featured image rule, and Heading alignment rule (see below) to every post body before pushing.

## Why this matters

The home page composes 3 audience-driven sections (Teachers / Students / News). Posts without an audience tag don't appear in any of them. Posts with the wrong tag appear in the wrong section. The Squarespace migration left every post in tangled topic-buckets — we don't want to recreate that mess.

## CTA URLs cheatsheet (canonical EE Interactive endpoints)

When linking to EE Interactive signup flows, use these canonical URLs (NOT redirect aliases):

| Audience / Context | Canonical URL |
| ------------------ | ------------- |
| Universal — visitor picks Teacher/Student/Individual themselves | `https://www.essentialelementsinteractive.com/signup/type.asp` |
| Teacher signup (form) | `https://www.essentialelementsinteractive.com/signup/teacher/intake.asp` |
| Student in a school/class (uses school code) | `https://www.essentialelementsinteractive.com/signup/student/schoolcode.asp` |
| Independent learner (no school) | `https://www.essentialelementsinteractive.com/signup/student/student.asp` |

### Aliases that should NOT be used (they redirect, but `.html` aliases may not survive future platform migrations):
- ❌ `https://www.essentialelementsinteractive.com/createAccount.html` — use the canonical teacher form instead
- ❌ `https://www.essentialelementsinteractive.com/freeAccount.html` — does not exist on EE Interactive; use `/signup/type.asp` for "free account" CTAs
- ❌ `https://www.essentialelementsinteractive.com/intake.asp` — old root-level legacy (replaced site-wide in 2026-04 sweep)

### Rule of thumb when authoring a CTA

- If the surrounding paragraph is clearly addressed to TEACHERS (educators planning lessons, managing classroom workflows, choosing method books) → use `signup/teacher/intake.asp`.
- If the surrounding context is clearly addressed to a STUDENT (learning, recording assignments, using Music Studio as a learner) — and they have a school code → `signup/student/schoolcode.asp`. If they have no school code → `signup/student/student.asp`.
- If the page is generic (overview, marketing, broad-audience landing) → use `signup/type.asp` (the chooser).
- Never link to the bare domain `https://www.essentialelementsinteractive.com` from a button-style CTA — it leaves the visitor on the marketing home with no direct signup path. Image links and inline references can stay on bare domain when "go visit the site" is the intent.

## Excerpt rule

Every post MUST have an explicit `excerpt` field — a clean 1-2 sentence summary (30-60 words). Never rely on WordPress's auto-generated excerpt: it includes captions, button labels, image alt text, and leading H1s, which produces noisy previews on category archives, the home page audience sections, search results, and RSS.

When authoring OR updating a post, ALWAYS:
- Read the full body
- Distill the key value: what does this post help the reader do? What's actionable / specific?
- Write 1-2 sentences, 30-60 words, in plain prose
- Avoid: leading questions, "EEi" repetition, button labels, "Click here", lede-paragraph verbatim, the post title repeated, marketing puffery ("amazing", "powerful", "ultimate")
- Submit `excerpt` together with `content` in the same `posts.update`/`posts.create` call (no separate "I'll do excerpt next" — always atomic)

When editing an existing post: if the body changed materially (more than a typo fix), regenerate the excerpt — do not leave a stale one. If the body is unchanged or trivially edited, the existing excerpt can stay.

There's a planned `save_post` hook in the theme as a safety net for posts authored directly in WP-admin (not by Claude). It will fill empty excerpts with a simple first-paragraph heuristic. Don't rely on it for Claude-authored posts; hand-written excerpts always read better.

Tooling: see one-time regeneration task in `content/_meta/tasks.md` (top priority).

## External link rule

Every `<a>` whose `href` points outside `eeiblog.com` MUST have:
- `target="_blank"` — open in a new tab
- `rel="noopener noreferrer"` — security/privacy hygiene (prevents `window.opener` access; strips Referer header)

If the link already has a `rel` attribute (e.g. `rel="nofollow"`), append `noopener noreferrer` to the existing token list — don't replace.

Internal hrefs are exempt:
- starting with `/` (relative path)
- starting with `https://eeiblog.com` or `https://www.eeiblog.com`
- `mailto:` and `tel:` links
- `#` anchors

Apply this when authoring any new post, and verify before publish. Sweep tooling: `scripts/target-blank-patcher.py` (re-runnable across the whole catalog).

## Featured image rule

When a post has a `featured_media`, the post body MUST NOT begin with a `<figure>` containing the same image. The theme renders the featured image at the top of the page automatically — putting the same image first in body produces a visual duplicate.

If the original article opened with a hero image, choose one approach:
- Set it as `featured_media` and remove the `<figure>` from body, OR
- Leave it in body without a featured image (theme will skip the auto-render)

When writing new posts, prefer the featured-only approach — the theme controls layout consistently.

Same applies to the post title: the theme renders the post title as an H1 above the featured image. Body content MUST NOT start with an H1/H2 that duplicates (or near-duplicates) the post title — that produces a double heading on the rendered page. If the article has a "title-on-print" line that differs from the post title, that's fine; just don't repeat the post title.

## Heading alignment rule

Headings (`<h1>` through `<h6>`) MUST NOT carry the `has-text-align-center` class or `text-align: center` inline style — they should align with body text on the left. Designer-confirmed convention.

If a section heading absolutely needs centered emphasis, raise it as a layout exception with the designer first; don't sneak it into a post.

Paragraphs and other blocks may use center alignment when appropriate (e.g. captions, by-lines, button containers). The rule applies only to heading tags.

## Categories cheatsheet (WP IDs)

| name              | slug              | wp_id      |
| ----------------- | ----------------- | ---------- |
| For Teachers      | for-teachers      | 76611481   |
| For Students      | for-students      | 76611482   |
| News              | news              | 76611470   |
| Teaching Tips     | teaching-tips     | 76611468   |
| Features          | features          | 76611464   |
| Method Books      | method-books      | 76611465   |
| Account Setup     | account-setup     | 76611466   |
| Tutorials         | tutorials         | 76611467   |
| Product Overview  | product-overview  | 76611463   |
| Webinars          | webinars          | 76611469   |
| Lead Generation   | lead-gen          | 76611471   |
| Archive           | archive           | 76611472   |
| Giveaways         | giveaways         | 76611473   |
