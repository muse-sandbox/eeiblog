# SEO metadata — EEi Overview

> Companion to `draft-post.md`. These fields go into Jetpack SEO Tools / WP post meta when we publish.

## Target keyword

**Primary:** `essential elements interactive`
**Secondary cluster:**
- `EEi`
- `essential elements method companion`
- `cloud-based music method`
- `band and strings classroom software`
- `music practice app for school`

Search intent: navigational + informational. People typing "essential elements interactive" are either looking for the platform itself or trying to figure out what it is. We want this post to satisfy both: it explains the product clearly and links to the live platform.

## Field-by-field

### `jetpack_seo_html_title` (SEO title — appears in browser tab and Google snippet)

**Recommended (62 chars):**
> `Essential Elements Interactive: Cloud Companion to EE Methods`

**Alternative options:**
- `Essential Elements Interactive Overview | Hal Leonard EEi` (56 chars)
- `What is EEi? Essential Elements Interactive — Overview` (54 chars)

### `advanced_seo_description` (meta description — appears in Google snippet)

**Recommended (157 chars):**
> `Essential Elements Interactive (EEi) is the cloud-based companion to the EE method — Music Studio, Video Assignments, Gradebook, free with every book.`

**Alternative (151 chars):**
> `Discover Essential Elements Interactive — the cloud-based digital companion built for band and strings classrooms, included free with every EE method book.`

### Open Graph (set via featured_media + meta — Jetpack picks them up)

- **OG title:** same as SEO title
- **OG description:** same as meta description (or shorter — 100 chars):
  > `The cloud-based digital companion to the Essential Elements method. Free with every EE book.`
- **OG image:** featured_media → `EEI_Banner_June2025.png` (1200×630 recommended; need to verify dimensions and crop if needed)

### Twitter Card

- **Card type:** `summary_large_image`
- **Title / description / image:** mirror OG values

### Canonical URL

`https://eeiblog.com/essential-elements-interactive-overview/` (assuming proposed slug + custom domain).
*(Currently the staging URL is `https://rkislenok-xwlsc.wpcomstaging.com/...`. Once the custom domain is mapped and site launched, the canonical updates automatically.)*

### `jetpack_seo_noindex`

`false` (this is a hub page — must be indexed).

### Robots meta

`index, follow` (default).

### Schema.org / JSON-LD

For this post, three schemas to embed:

1. **Article** — the post itself (headline, datePublished, author, image, publisher).
2. **Product** — Essential Elements Interactive (name, description, brand, offers, image).
3. **FAQPage** — the FAQ block at the bottom.

I'll generate the JSON-LD when we go to publish; for now noting the intent. Schema can be inserted into the post body via a Jetpack/Yoast feature if available, or via a custom HTML block.

## Internal links audit (per article-rule section 7)

Inside the post body, planned internal links:
- ✅ Teaching Tips link: `eei-video-assignments`, `recordingassignments`, `getting-started` (3 total — slightly over the "1 link" rule but justified for a hub page).
- ✅ Product surface link: `https://www.halleonard.com/ee/`, `https://www.halleonard.com/ee/band/`, `https://www.halleonard.com/ee/strings/`, `https://www.essentialelementsinteractive.com/signup/teacher/intake.asp` (multiple — also justified for a hub).
- ✅ Bottom CTA: get the books / create account — both included in "Get started" block.

**Note:** the 1-link rule applies to standard articles. For a hub/overview page, more cross-linking is warranted.

## External links

- `halleonard.com/ee/`, `halleonard.com/ee/band/`, `halleonard.com/ee/strings/` — `target=_blank`, no `rel=nofollow`
- `essentialelementsinteractive.com/...` — `target=_blank`, no `rel=nofollow`
- `support.essentialelementsinteractive.com` — `target=_blank`
- `youtube.com/@EssentialElementsforBand` and `@essentialelementsforstrings` — `target=_blank`

## Image alt-text checklist

Every image in the post needs an alt-text. Proposed (one line per image):

| Image (filename / what it shows) | Alt-text |
|---|---|
| EEI_Banner_June2025.png (hero) | Essential Elements Interactive banner — band and strings students using the EEi platform on laptops and tablets |
| EEi_Blog_1.png (Music Studio screenshot) | Music Studio screen on laptop — exercise notation with play, record, and tempo controls |
| EEi_SD_Card_Pic.png | Student playing along with EEi Music Studio in classroom |
| EEi_Blog_2.png | Music Studio accompaniment selection screen |
| EEi_Blog_3b.png | Music Studio recording interface |
| EEi_Blog_5.png (Video Assignments) | Student recording a video assignment in EEi Music Studio |
| EEi+Video+Assignments.png | Video Assignments submission flow |
| Resources_Videos_Still.png | EEi Resources section showing instructional video library |
| EEi_Blog_4.png | Resources library tile view |
| Flute_Preview.png | Flute instructional video preview |
| Customize+Curriculum.png | Teacher uploading custom materials to EEi classroom |
| iPad_Grading_Still.png | Teacher grading student recording on iPad |
| Overview-Assignments-6.png | Teacher Gradebook assignments overview |
| Assignments_Overview.png | EEi Assignments dashboard for teachers |

## Lighthouse / PSI follow-ups (post-publish)

After publishing the live URL:
- Run [Page Speed Insights](https://pagespeed.web.dev/) — verify LCP < 2.5s, CLS < 0.1, INP < 200ms.
- Verify hero image is properly compressed and serves WebP/AVIF.
- Verify lazy-loading on below-fold images.
- Verify proper heading hierarchy (one H1, sequential H2/H3).

## Pre-publish checklist

- [ ] Post body approved by Roman
- [ ] Title and slug confirmed
- [ ] Featured image uploaded to WP media library
- [ ] All images uploaded to WP and alt-texts set
- [ ] Internal links validated (no 404s)
- [ ] External links validated
- [ ] SEO title + meta description set
- [ ] Category + tags assigned
- [ ] noindex = false
- [ ] Permalink structure decision made (date-based → /%postname%/)
- [ ] Old `eei-overview-1` page → trashed or redirected after post is live
- [ ] Old `eei-overview` page (working copy) → trashed
- [ ] Old `eei-overview-1-1` page (working copy CB) → trashed
