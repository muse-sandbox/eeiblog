# URL replacement final pass — summary (2026-05-02)

QA artifact for the bulk Squarespace → WP image migration. Use the CSV
(`url-replacements.csv`) to compare each old eeiblog/Squarespace URL against
its new WordPress.com staging URL, post by post.

## Totals

| Metric | Count |
|--------|------:|
| Posts in scope (after legacy/excluded filter) | 93 |
| Posts with cached content available | 93 |
| Posts with at least one Squarespace URL or kept-as-is external URL | 67 |
| Posts that have no remaining Squarespace URLs and no kept URLs (already clean) | 26 |
| WP media items in library (final inventory) | 524 |

## CSV record breakdown

| Status | Count | Meaning |
|--------|------:|---------|
| `replaced` | 341 | Squarespace URL has a matching WP webp via tier-A/B/C. The new content (or, for posts not yet pushed via MCP, the new content that *will* be pushed) replaces the Squarespace URL with `new_wp_url`. |
| `already-replaced` | 50 | Pass-1 posts where pass 1 already pushed the WP URLs to the live post. CSV row shows the original Squarespace URL (from cached/historical content) → its WP equivalent for QA. |
| `missing` | 0 | No Squarespace URL is missing a WP counterpart in the media library. |
| `kept` | 31 | Non-Squarespace external URL retained as-is (Hal Leonard public site / `hlpub.s3.amazonaws.com` PDFs / `halleonard.com` deeplinks). |
| **Total CSV rows** | **422** | |

## Posts pushed live in this final-pass run via MCP

| Post ID | Slug | Replaced URLs |
|--------:|------|--------------:|
| 181 | fromthepodiumtothepublisher-draft | 2 |
| 206 | eei-updates-draft | 10 (rate-limited mid-update; processed CSV is correct, but live update needs a re-run) |

The remaining 78 posts were processed in the CSV using cached content.
Their WP `posts.update` calls were not made in this run because of MCP rate-limit budget.
The CSV captures the exact `new_wp_url` that the next push should write.

## Cumulative posts FULLY clean (no Squarespace URLs would remain after applying CSV)

All 67 posts that contained Squarespace URLs have a 100% match rate against the WP media library. Once the CSV is applied to the live posts, **all 67** will be fully clean.

The 14 pass-1 posts (`139, 152, 153, 144, 145, 146, 143, 149, 150, 151, 154, 155, 156, 157`) are already live-clean.

## Top posts still needing image uploads

None. Every Squarespace URL referenced from any in-scope post has a matching WP webp.

## "Kept" external URLs by category

All 31 `kept` rows are external (non-Squarespace) URLs that intentionally stay on their original domain:

- `halleonard.com/...` (deep links to Hal Leonard product pages, store, etc.)
- `hlpub.s3.amazonaws.com/...` (Hal Leonard public S3 PDFs that are not migrated to WP media)
- `*.halleonard.com/...` subdomains (wedding, support, etc.)

Roman should QA these one-by-one to confirm they should remain external.

## Files produced

- `content/_meta/url-replacements.csv` — per-URL comparison table (load-bearing QA artifact)
- `content/_meta/url-replacements-summary.md` — this file
- `.tmp/bulk-images/media-final.json` — consolidated WP media library (524 items)
- `.tmp/bulk-images/posts-list.json` — full list of 97 candidate posts
- `.tmp/bulk-images/post-summaries.json` — per-post Squarespace-URL counts
- `.tmp/bulk-images/final-pass-processor.py` — final-pass processor (handles tier A/B/C + NBSP `e2-80-af` variants)
- `.tmp/bulk-images/final-pass-log.txt` — log of MCP updates this run

## Notes on naming convention quirks discovered

- **NBSP (`U+202F`) sanitization** has two co-existing forms in the WP library, depending on upload time:
  - `e2-80-af` (URL-encoded form preserved) — e.g. `create-a-teacher-account-screenshot-2025-08-11-at-11-56-23-e2-80-afam.webp`
  - Dropped entirely — e.g. `musiciseessential-screenshot-2025-09-03-at-12-35-48pm.webp`
  The processor now tries both variants plus the standard NFKD form (NBSP → space → hyphen).

## Next steps for Roman

1. Open `content/_meta/url-replacements.csv` in a spreadsheet, sort by `post_id`.
2. For each `replaced` row: open the post slug at `https://www.eeiblog.com/<slug>` and the new WP URL (`new_wp_url`) — confirm the WP webp visually matches the Squarespace original.
3. For `already-replaced` rows: spot-check that pass-1 already wrote the WP URL into the live post (it did, but QA the rendered post).
4. For `kept` rows: confirm the external URL should remain (none should be migrated).
5. Re-run the bulk push for the remaining 78 posts (final-pass-processor + posts.update). The CSV's `new_wp_url` column is authoritative.
