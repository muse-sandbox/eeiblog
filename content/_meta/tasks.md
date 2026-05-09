# EEi Migration — Task Tracker

_Last updated: 2026-05-07_

This file is the source of truth for project tasks. Update after every session — the live Cowork TaskList can reset between sessions, but this file is in git.

## In progress

_(none — pick from Pending below)_

## TOP PRIORITY

_(none — pick from Pending below)_

## Pending

### Quick polish
- **Single-CTA convention sweep** — go through articles, leave one primary CTA per post (was #47).
- **target="_blank" for any new posts** — initial sweep done; revisit when drafts get published (was #57 follow-up).
- **[manual] Cleanup duplicate media library uploads (Type B "-1 suffix")** — 19 verified-unreferenced files in WP Media Library need permanent delete via WP-admin (MCP can't force-delete). Low priority — see `.tmp/dupes-type-b-delete-list.md`.
- **Bulk-replace cross-post media duplicates (Type A)** — 56 groups, ~6 MB. Same pattern as the staging-URL bulk-replace. See `.tmp/dupes-type-a-bulk-replace.md`.
- **Local image dedup** — 92 groups, 24.7 MB wasted in `assets/images/`. Migrate dupes into `assets/_shared/`, update `media-inventory.csv`.

### Content
- **Featured images: оставшиеся посты без preview** (was #62). ~70 posts still without featured_media (mostly archive, news, EE Books overview pages).
- **Update post 159 + restore school-account + individual-account pages** (was #68). Buttons in 159 currently point to /school-account/ and /individual-account/ which don't exist as published pages yet — drafts 160 and 161 need slug rename + publish.
- **Re-categorize Mixed posts** (was #67). 13 posts currently dual-tagged for-teachers + for-students. Over time, edit content + split into one specific audience.
- **Rewrite school-account flow — посты 159-166** (was #35). Full rewrite of student onboarding flow. Related to "Update post 159…" above.
- **Restore tutorials 171, 172 (submit audio/video)** (was #36) — связаны с 170.
- **Restore archived webinar posts 194-198** (was #46) — split per-webinar from 148.
- **Decide which archive posts to keep / rewrite / delete** (was #34).
- **Переосмыслить контентные страницы 201/202/203/204 + 209/210** (was #55).
- **Переписать 214 First Performance — история про Hal Leonard в COVID** (was #56).
- **Editorial pass: PURCHASE → GET, EEMC removal, freshening stale copy** (was #31).
- **Normalize все авторские статьи — общий формат** (was #40).
- **Author bylines + author pages — Erin Cole Steele, Chase Banks, Matt Wolf, Soo Han** (was #37).

### Layout / theme
- **Theme tweaks via Claude Code (HR / buttons / date / gallery / author / video bottom-margin)** (was #33). Накопленный список мелочей — отдельный заход.
- **Переделать галереи как в оригинале (Squarespace layout)** (was #50).

### Big projects
- **Vimeo → YouTube migration for /eei-webinars/** (was #25).
- **Per-episode EE Band Talk blogs (16 posts) + audio.com player** (was #26).
- **Constant Contact unified form for lead-gen + code purchase** (was #28).
- **Import profiles.csv (2326 contacts) into Constant Contact** (was #29).
- **Competitor analysis + content strategy** (was #23).
- **Spanish Edition EE — write launch article** (was #24).

### Maintenance
- **PDF assets — постоянные permalink'и** (was #51).
- **Обновить устаревшие скриншоты** (was #52).
- **FREE vs TEACHER signup CTA URL audit follow-up** — base sweep done (was #54), revisit if new posts add CTAs.

## Completed (recent — last 2 weeks)

### 2026-05-09 (this session, continued)
- Webinar transcription pipeline built — `scripts/transcribe-video.sh` (whisper.cpp on Mac) + `assets/videos/{audio,transcripts}/` layout. 5 videos transcribed by Roman locally (large-v3).
- Post 194 (`/on-demand-starting-band/`) — full webinar restoration: title cleaned, video re-embedded, "Inside the webinar" 3-paragraph summary added, full collapsible transcript injected (`<details>` block, 173 timestamped paragraphs).
- Webinar post structure rule added to `post-creation-protocol.md`.

### 2026-05-08 (this session, continued)
- Theme: stop rendering featured image on single post pages. Featured is now preview-only (category archives, search, recent posts, OG cards). Body controls the lead image position via "intro → H2 → paragraph → figure" convention. Featured-image rule in protocol updated to match.
- Hub post 170 — added `is-vertical` class to button group, gap reuses existing CSS fix; "staircase" gone.
- Restore tutorials 171, 172 (Submit Audio/Video Recording) — both published with clean slugs, For Students + Tutorials categories, featured images set (= same as body's lead figure), full step-by-step bodies replacing the gallery-only drafts. CTA changed from teacher signup to /eei-overview-1/ (audience mismatch fix).
- Excerpt regeneration Part 1 — 50 posts updated, 38 skipped (37 already-good + post 189 pre-approved). Hand-crafted 30-60 word excerpts.
- Fix broken YouTube embed on /music-studio/ (post 168) — `<a href="...">URL</a>` inside embed wrapper replaced with plain URL for oEmbed.
- Audited 13 other posts with embed wrappers — all already correct (iframes from Jetpack); 168 was only broken case.
- target="_blank" sweep extended to drafts — 8 drafts updated (10 attrs added). Publish posts already correct.
- Protocol doc gained 4 new rules: External link, Featured image, Heading alignment, Excerpt — all hand-crafted, not auto-generated.

### 2026-05-07 (this session)
- Add CTA buttons to home audience sections (Teachers / Students / News) — deployed via Claude Code.
- Add site-wide search to header + Categories list to footer — deployed.
- Bulk-fix center-aligned headings → left align — 17 posts updated, 51 headings unaligned.
- Fix vertical button group spacing in theme (was "black staircase" on /create-an-eei-student-account/) — deployed.
- Post 209 (eei-holiday-album) — 3 H1 → H2/H3 hierarchy + sentence case + center removed.
- Post 185 (rhythm-writing-system) — set featured to media 753, removed leading H1, removed body figure dup, sentence-case section heads.
- Post 181 (fromthepodiumtothepublisher) — set featured to media 1559 (orchestra-performance), removed H2 dup, fixed `By: Chase Banks` → `by Chase Banks`, repositioned EE2025 banner under "Want to explore Essential Elements?", "FORMER" → "former".

### 2026-05-05 (this session, continued)
- Fix duplicate headings + featured-image dups — 18 posts patched (15 heading-only, 3 figure-only, 2 both). Log at `.tmp/dup-fix-log.txt`.
- Audit Type A cross-post media dups — 56 groups, ~6 MB. Pending decision on bulk-replace.
- Audit Type B duplicate uploads — 19 unreferenced files, MCP can't force-delete; manual cleanup task.
- Local image dedup audit — 92 groups, 24.7 MB in assets/images/.
- Restore project task tracker to `content/_meta/tasks.md` (was lost in Cowork TaskList session reset).

### 2026-05-05 (this session)
- Replace deprecated EEi banners (was #70) — 7 posts cleaned, canonical id 271 everywhere.
- CTA URL audit + bulk-fix (was #54) — 14 posts updated; canonical `signup/teacher/intake.asp` + per-post overrides for 139/189/204; protocol doc updated.
- Featured images: extract from eei-lessons hub + apply via MCP (was #60) — 21 posts got featured.
- Trash duplicate pages from accidental WXR re-import (was #58) — 117 pages trashed.
- Apply redirects.csv via Redirection plugin (was #44) — initial 20 + 4 incremental = 24 redirects live.
- Update WP favicon + Site Identity (was #48) — done.
- Add 173→191 redirect (was #59) — done.
- Audience categorization for 38 posts — `for-teachers` / `for-students` / Mixed (both) / News.
- Trash legacy duplicate posts 4, 8, 201, 202.
- Post 174 — removed duplicate H1 in body.
- Post 159 — restored 3-button router with correct URLs + ported text/diagram from old post 4.
- Rewrite home page front-page.php with 3 audience sections (was #63) — Teachers + Students + News + hero EE Interactive banner.
- target="_blank" sweep on external links (was #57) — 6 posts updated.
- Поправить даты публикации для активных постов (was #49) — 91 posts.
- Online QA walkthrough (was #22) — done across 88 posts.
- Merge "EEi Video Assignments Strings" (173) into "EEi Video Assignments" (191) (was #45).
- Slug renames: -draft → clean slugs (was #30) — 46 posts.
- Bulk-remove "BACK TO TEACHING TIPS" CTA (was #43).

### 2026-05-04 and before
- Re-import пост 187 holiday-practice-tips (was #39).
- Reorg media папок: assets → images, media/* migration (was #41).
- Бульк-replace signup/teacher/intake.asp (was #38) — superseded by full URL audit (#54).
- Generate "teachers shaking hands" image (was #27).
- Bulk media inventory + image migrations (was #14, #15, #16, #17).
- Original audit, plan, content rules, dochistka, страницы → посты, junk decisions (was #1-13).

## Deleted / superseded

- **#61** "Rewrite main landing pages" — split into #63/64/65, then #64+#65 dropped after Roman picked categories+redirects approach.
- **#64** "/teachers/ landing" — replaced by category archive `/category/for-teachers/` with redirect.
- **#65** "/students/ landing" — replaced by category archive `/category/for-students/` with redirect.
- **#66** "Workflow protocol: ask audience category on post create" — duplicate of #69, merged.
- **#69** "Categorize new posts strictly: post-creation-protocol" — task-style item dropped after protocol documented in `content/_meta/post-creation-protocol.md`.
- **#18-21** initial scratch tasks during onboarding.

## Notes

- After every session, update this file with what changed.
- Cowork TaskList is **non-persistent** between sessions — this markdown is the persistent record.
- IDs from the live Cowork TaskList are scoped to one session and renumber after reset; here we use descriptive names instead.
- For the school-account flow rewrite (#35 + restore school-account/individual-account), see related notes in `content/posts/account-setup/*.md` frontmatter.
