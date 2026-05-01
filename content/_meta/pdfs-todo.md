# PDFs pending download + WP Media upload

PDFs referenced in posts that still point to Squarespace `/s/...` URLs (which won't work after migration) or to external hosts. Roman to download from source, upload to WP Media library, then update post URLs.

## Naming convention

Match the image convention: `<post-slug>-<descriptive-name>.pdf`. Lowercase, hyphenated, no spaces. Examples:

- `eeidistancelearning2-eei-lesson-plans.pdf`
- `ee-dealer-resource-page-eei-advantage-flyer.pdf`
- `ee-dealer-resource-page-eei-brochure.pdf`

If a PDF is shared across multiple posts, use the most logical "owner" post slug. Then in posts that link to it, use the same WP URL — not the slug-prefixed name per post.

## Upload location

WP Media library uploads to `https://rkislenok-xwlsc.wpcomstaging.com/wp-content/uploads/<year>/<month>/<filename>.pdf`. As of 2026-05 the convention is `2026/05/`.

## Inventory

| Source URL | New WP filename | Used in | Notes |
|---|---|---|---|
| `https://www.eeiblog.com/s/EEi-Lesson-Plans.pdf` | `eei-lesson-plans.pdf` | `/eeidistancelearning2/` (page kept), future blog posts | COVID-era distance learning lesson plans |
| `https://www.eeiblog.com/s/EEi-Advantage-Flyer.pdf` | `eei-advantage-flyer.pdf` | `/ee-dealer-resource-page/`, `/ee-clinician-resource-page/` | "Take Advantage of EEi" flyer |
| `https://www.eeiblog.com/s/EEi-Brochure-sm.pdf` | `eei-brochure.pdf` | `/ee-dealer-resource-page/`, `/ee-clinician-resource-page/` | EE overview brochure |

## Lead-gen / gating decision

Roman raised: should we gate these PDFs behind a Constant Contact form (download in exchange for email)? My take:

- **EEi-Lesson-Plans.pdf** — yes, gate it. High-intent download, valuable for teachers, fits the lead-gen pattern we already have for perusal books.
- **EEi-Advantage-Flyer.pdf** and **EEi-Brochure-sm.pdf** — both are dealer/clinician sales tools. Keep open (no gate). They need to be easy to share with prospects.
- **PromoBandOrchestraToolkit2025-2026.pdf** (already on HL S3, referenced in `musiciseessential` archive) — leave on S3, not our concern.

If we gate `EEi-Lesson-Plans.pdf`, the workflow is:
1. Make the PDF non-public (private upload, or .htaccess gate)
2. Add a CC form on `/eeidistancelearning2/` (or new lead-gen post)
3. CC delivers download link via email
4. Track conversion in CC

Open question: which CC list? Roman to decide.

## Process to upload

1. `curl -L -o ~/Downloads/eei-lesson-plans.pdf https://www.eeiblog.com/s/EEi-Lesson-Plans.pdf` (run on Mac with full network)
2. WP admin → Media → Add New → drag-drop
3. After upload, copy the URL from the media item
4. Update the relevant post(s) in WP and the matching `.md` files in this repo
