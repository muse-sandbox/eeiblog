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

## Workflow when Claude is asked to create a new post

Claude must always:
1. Suggest a default topic-category and audience-category based on the title/content.
2. Ask the user to confirm or override before creating the post.
3. Never publish in `Uncategorized` — that's a smell from broken migration, not a valid state.

## Why this matters

The home page composes 3 audience-driven sections (Teachers / Students / News). Posts without an audience tag don't appear in any of them. Posts with the wrong tag appear in the wrong section. The Squarespace migration left every post in tangled topic-buckets — we don't want to recreate that mess.

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
