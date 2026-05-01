# Pilot draft — EEi Overview post (AS-IS approach)

> Source page: `https://rkislenok-xwlsc.wpcomstaging.com/eei-overview-1/` (WP page ID 16)
> Original on Squarespace: `https://www.eeiblog.com/eei-overview-1`
> Goal: convert page → post, preserve original copy AS-IS, clean only technical artifacts, fill SEO metadata.
> Status: **draft for Roman's review** — nothing pushed to WordPress yet.

---

## Approach

Per Roman's instruction: **don't editorialize old content, just port as-is.** Editorial rewrites apply only to NEW articles going forward.

### What I clean up (technical, non-editorial)

- Squarespace `<div class="sqs-html-content">` wrappers (rendering noise, no semantic meaning)
- Inline `style="white-space:pre-wrap"` on every `<p>` (legacy from Squarespace block editor)
- `data-recalc-dims` and other Jetpack image attributes get preserved
- Empty `class=""` attributes
- Convert HTML markup to native WordPress block markup (`<!-- wp:paragraph -->`, `<!-- wp:list -->`, etc.) so the Gutenberg editor handles it cleanly

### What I keep AS-IS (editorial — original wording preserved)

- Headlines (including ALL CAPS — they're stylistic choices made on the original site)
- Body copy verbatim
- Bullet lists verbatim
- Original CTAs ("Create your free EEi account", "BUY NOW") — kept as-is even though new content rules suggest "Get the books"
- All images at their current Squarespace CDN URLs (already Jetpack-proxied via `i0.wp.com`)

---

## URL strategy

Per your answer (#1) — preserving SEO traffic is critical. Two paths:

**Path A (safer, no redirect needed):** Keep slug `eei-overview-1` exactly as-is.
**Path B (better SEO long-term, requires 301):** Switch to `essential-elements-interactive-overview` + 301 from `/eei-overview-1/` to new URL.

**MCP constraint:** the WordPress.com MCP does NOT expose plugin management. I can't verify if a redirect plugin (Redirection / RankMath / Yoast) is installed. Without confirmed 301 capability, **Path A is safer for the pilot**. Path B can be applied later once a redirect mechanism is in place.

→ **Recommendation:** Go with Path A for the pilot. Migrate exactly the URL we have today.

---

## URL conflict handling (page vs. post with same slug)

Currently `/eei-overview-1/` is a **page** (ID 16). The plan is to:

1. Create a new **post** with slug `eei-overview-1` as a **draft** (no URL conflict because drafts aren't published).
2. You review the draft via preview link.
3. Once approved: **trash the old page first**, then **publish the new post**. The URL `/eei-overview-1/` continues to serve the same content, just from a post now. No SEO disruption.
4. If Path B is chosen later: rename slug + add 301 from old slug.

---

## Permalink structure note

Site currently uses `/%year%/%monthnum%/%day%/%postname%/` for posts. To get clean URLs (`/eei-overview-1/` instead of `/2026/04/30/eei-overview-1/`), we need to change permalink structure to `/%postname%/`.

**MCP constraint:** the WordPress.com MCP `settings.update` does NOT expose permalink_structure as a writable field. **You'll need to do this manually:**

1. Log in to wp-admin: `https://rkislenok-xwlsc.wpcomstaging.com/wp-admin/`
2. Navigate to **Settings → Permalinks**
3. Select **Post name** (or Custom: `/%postname%/`)
4. Save

Do this BEFORE we publish the post — otherwise it goes live at `/2026/04/30/eei-overview-1/` and we have an extra cleanup step.

---

## Post fields

### Title
**EEi Overview** *(unchanged from original page title)*

### Slug
`eei-overview-1` *(Path A — preserve as-is)*

### Excerpt (~25 words, taken from existing excerpt)
> THE POWERFUL CLOUD-BASED COMPANION TO THE ESSENTIAL ELEMENTS METHOD BOOKS. EEi is your all-in-one digital teaching companion — built for today's band and strings classrooms.

### Featured image
`EEI_Banner_June2025.png` (Squarespace CDN; later re-upload to WP media)

### Categories
**Product Overview** *(new — to be created)*

### Tags
`eei`, `essential-elements-interactive`, `music-studio`, `video-assignments`, `assignments`, `resources`

---

## Body (AS-IS content from original page, in clean WP block markup)

```html
<!-- wp:image {"linkDestination":"custom"} -->
<figure class="wp-block-image">
  <a href="https://www.essentialelementsinteractive.com/" target="_blank" rel="noopener">
    <img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/0d0001b5-29ab-4c7c-b3d1-9178f8a065eb/EEI_Banner_June2025.png?ssl=1"
         alt="Essential Elements Interactive banner" />
  </a>
</figure>
<!-- /wp:image -->

<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="has-text-align-center">THE POWERFUL CLOUD-BASED COMPANION TO THE ESSENTIAL ELEMENTS METHOD BOOKS</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>EEi is your all-in-one digital teaching companion—built for today's band and strings classrooms. From online learning and practice to assessment and communication, it's everything you need to keep students progressing in class and at home.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Best of all? It's included with every Essential Elements method book.</strong></p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:image {"linkDestination":"custom"} -->
<figure class="wp-block-image">
  <a href="https://www.halleonard.com/ee/" target="_blank" rel="noopener">
    <img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/84d5eee1-03be-47c3-b1d4-8551c351de7e/EE2025_800x800.jpg?ssl=1"
         alt="Essential Elements method book covers" />
  </a>
</figure>
<!-- /wp:image -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="has-text-align-center"><strong>Essential Elements Interactive offers the following powerful tools:</strong></h2>
<!-- /wp:heading -->

<!-- wp:image -->
<figure class="wp-block-image">
  <img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/b1fab22c-1f28-4565-8c4b-296e0a52c297/Frame%2B1.png?ssl=1"
       alt="EEi Music Studio feature graphic" />
</figure>
<!-- /wp:image -->

<!-- wp:gallery {"columns":4} -->
<figure class="wp-block-gallery columns-4">
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750725265354-1IJBY9HEMAXIBIRBWYDV/EEi_Blog_1.png?ssl=1" alt="Music Studio screen showing exercise notation with controls" /></figure>
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1680535986622-4N5TVE962EPX7W60NXUP/EEi%2BSD%2BCard%2BPic_0446-2.png?ssl=1" alt="Student playing along with EEi in classroom" /></figure>
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750725265301-WMVB3P9KFY6CBEV6Q6Y1/EEi_Blog_2.png?ssl=1" alt="Music Studio accompaniment selection" /></figure>
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750725700148-OWPM48WVFANGLCB43ZL0/EEi_Blog_3b.png?ssl=1" alt="Music Studio recording interface" /></figure>
</figure>
<!-- /wp:gallery -->

<!-- wp:paragraph -->
<p>The <strong><em>EEi Music Studio</em></strong> makes learning to play more effective and more fun… in class or at home!</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Features Include:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
  <li><strong>Professional Soloists</strong> demonstrating each exercise in the book</li>
  <li><strong>Multiple Accompaniment Styles</strong> for each exercise, performed by professional musicians</li>
  <li><strong>Recording Feature</strong> to share music with teachers, friends, and relatives</li>
  <li><strong>Tempo Control</strong> for more effective learning and practice</li>
  <li><strong>EEi Practice Tools</strong> including built-in metronome, tuner, and fingering charts</li>
  <li><strong>Easy-to-use Interface</strong> to make playing, recording, and sharing simple and fun</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":1} -->
<h1><strong>EEi Video Assignments</strong></h1>
<!-- /wp:heading -->

<!-- wp:gallery {"columns":2} -->
<figure class="wp-block-gallery columns-2">
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750726841157-YHJL2W1WV9S0Z97S0YYQ/EEi_Blog_5.png?ssl=1" alt="Student recording video assignment in EEi" /></figure>
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1680539006094-8AZD762XTITC3CVET9V5/EEi%2BVideo%2BAssignments.png?ssl=1" alt="EEi Video Assignments submission flow" /></figure>
</figure>
<!-- /wp:gallery -->

<!-- wp:paragraph -->
<p>Students can now record and share videos right in the EEi Music Studio. This allows teachers to provide more detailed feedback.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Video Assignments Features:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
  <li><strong>Video Recording in Music Studio</strong> to make a video directly from the music studio with EE Music on screen</li>
  <li><strong>Video in All-Music</strong> allows students to make recordings of any music being performed in class</li>
  <li><strong>Save to the Cloud</strong> means that videos are processed and stored in the cloud for easy use on the internet</li>
  <li><strong>Submit to Teacher</strong> so teachers can assess students performance with more information</li>
  <li><strong>Easy-to-use Interface</strong> to make playing, recording, and sharing simple and fun</li>
  <li><strong>Download Videos</strong> to share with friends and family</li>
</ul>
<!-- /wp:list -->

<!-- wp:image -->
<figure class="wp-block-image">
  <img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/e3596754-14f1-4c17-8ee2-f6c25397cc10/Frame%2B4.png?ssl=1"
       alt="EEi Resources feature graphic" />
</figure>
<!-- /wp:image -->

<!-- wp:gallery {"columns":3} -->
<figure class="wp-block-gallery columns-3">
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750772064271-R5KGD39I3CN9LM9KVOUJ/Resources_Videos_Still.png?ssl=1" alt="EEi Resources video library" /></figure>
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750726954835-AD4U5CK3XCE28CRNBSR3/EEi_Blog_4%2B%281%29.png?ssl=1" alt="Resources library tile view" /></figure>
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1539008197649-8T1ZL57JRUKNM8YYQEQL/Customize%2BCurriculum.png?ssl=1" alt="Teacher uploading custom curriculum materials" /></figure>
</figure>
<!-- /wp:gallery -->

<!-- wp:paragraph -->
<p>The <strong><em>EEi Resources Section</em></strong> is filled with flexible, high-quality content designed to support music educators and students at every level. And with regular updates, it continues to evolve alongside your classroom needs.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>EEi Resources include:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
  <li><strong>400+ Instructional Videos</strong> recorded by EE Ambassadors and professionals in the field of music education</li>
  <li><strong>Individual Studies</strong> for all instruments to reinforce key skills</li>
  <li><strong>Music Theory</strong> lessons and worksheets</li>
  <li><strong>Instrument Training Worksheets</strong> to develop tone and technique</li>
  <li><strong>Bonus Songs</strong> including songs, duets, trios, and 50+ new pop arrangements</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p><strong>Customizable Digital Classroom Experience</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>For teachers, EEi goes a step further—upload, organize, and share your own materials to create a personalized digital classroom experience your students can access anywhere.</p>
<!-- /wp:paragraph -->

<!-- wp:image -->
<figure class="wp-block-image">
  <img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/da007377-6813-46a5-84be-a753482662cf/Frame%2B3.png?ssl=1"
       alt="EEi Assignments feature graphic" />
</figure>
<!-- /wp:image -->

<!-- wp:gallery {"columns":3} -->
<figure class="wp-block-gallery columns-3">
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750772784913-BFSQOIFRGD6OL65XPJH0/iPad_Grading_Still.png?ssl=1" alt="Teacher grading student recording on iPad" /></figure>
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1680539774094-A946GZFXSOTD48TLQWS5/Overview-%2BAssignments%2B6.png?ssl=1" alt="Teacher Gradebook assignments overview" /></figure>
  <figure><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750773225655-9NULO74DDM1GUSCHGG7J/Assignments_Overview.png?ssl=1" alt="EEi Assignments dashboard" /></figure>
</figure>
<!-- /wp:gallery -->

<!-- wp:paragraph -->
<p>EEi Assignments allows teachers to guide students better in their home practice.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Features include:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
  <li><strong>Online Assignments</strong> to help students prepare for class</li>
  <li><strong>Recording Assignments</strong> for teacher feedback</li>
  <li><strong>All-Music Studio</strong> for students to record any music</li>
  <li><strong>Teacher Gradebook</strong> for quick access to student recordings</li>
  <li><strong>Self-Assessments</strong> to help student further improve their musical knowledge and playing ability</li>
  <li><strong>Practice Records</strong> to help students and teachers plan home practice while keeping parents informed of practice goals and progress</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>With EEi Assignments, teachers can provide more guidance for students in their home practice and empower them to become better students and musicians.</p>
<!-- /wp:paragraph -->

<!-- wp:image -->
<figure class="wp-block-image">
  <img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/51536bbb-2fb4-487e-a647-a891bb39e2c6/Frame%2B2.png?ssl=1"
       alt="EEi Communications feature graphic" />
</figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p><strong>Features include:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
  <li>Flexible Communication Tools that provide a variety of ways to communicate with students and parents</li>
  <li>In-app notifications to remind students about upcoming assignments and feedback</li>
</ul>
<!-- /wp:list -->
```

---

## SEO metadata to apply at publish time

(see `seo-meta.md` — values unchanged)

---

## Manual steps required from Roman

Before I publish:

1. ✅ **Change permalink structure to `/%postname%/`** in wp-admin → Settings → Permalinks. (MCP doesn't expose this setting.)
2. *(Optional, for slug renames in future)* **Install a redirect plugin** (Redirection by John Godley is the standard). Without it, slug renames mean SEO loss.

After my work:

3. Review the draft post in WP admin / via preview link.
4. Approve so I can trash the old page (ID 16) and publish the post.

---

## Rate-limit note

The WordPress.com MCP server intermittently rate-limits requests. I'll batch operations and retry with backoff.
