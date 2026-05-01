---
wp_id: 139
wp_type: post
slug: eei-overview-1-draft        # currently a -draft suffix to avoid URL conflict with the existing /eei-overview-1/ page (ID 16). Will rename to eei-overview-1 once the old page is trashed.
status: draft
title: EEi Overview

date: 2026-04-30T11:45:38
modified: 2026-04-30T15:25:16

categories:
  - product-overview
tags: []

featured_media:
  wp_id: null
  source_url: https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/0d0001b5-29ab-4c7c-b3d1-9178f8a065eb/EEI_Banner_June2025.png?ssl=1

excerpt: |
  Essential Elements Interactive (EEi) is the cloud-based companion to the Essential Elements method — included free with every EE band and strings book. Music Studio, Video Assignments, Resources, Assignments, and Communication tools in one place.

seo:
  jetpack_seo_html_title: "Essential Elements Interactive: Cloud Companion to EE Methods"
  advanced_seo_description: "Essential Elements Interactive (EEi) is the cloud-based companion to the EE method — Music Studio, Video Assignments, Gradebook, free with every book."
  jetpack_seo_noindex: false
  og_image: null

source:
  origin: squarespace
  origin_url: https://www.eeiblog.com/eei-overview-1
  migration_pass: 1

notes: |
  Pilot — first post created via the new content tree.

  AS-IS port from page ID 16. Editorial rules NOT applied (per Roman's directive: don't editorialize old content).

  GALLERY ISSUE — fixed 2026-04-30T15:25:16:
  - The initial create used `<!-- wp:gallery -->` blocks with bare `<figure class="wp-block-image">` children. WordPress kses stripped the inner figures, leaving empty gallery shells (the "Drag and drop" placeholder Roman saw in the editor).
  - Resolution: replaced all galleries with stacked `<!-- wp:image -->` blocks (one block per image). Vertical layout, no media library required, reliable.
  - All 14 product images now render correctly via the Jetpack image proxy (i0.wp.com → Squarespace CDN).
  - Layout cost: images stack vertically instead of side-by-side. To restore multi-column layout, upload images to WP media library and switch to proper `<!-- wp:gallery -->` with `<!-- wp:image {"id":<media_id>} -->` inner blocks (or `<!-- wp:columns -->` with image inner blocks).

  Other open follow-ups:
  - Slug: currently `eei-overview-1-draft` to avoid URL conflict with the live page (ID 16). Once permalink structure is changed and old page trashed, rename slug back to `eei-overview-1`.
  - Permalink structure: requires manual change in wp-admin → Settings → Permalinks → "Post name". MCP doesn't expose this setting.
  - Featured image: set to local WP media ID after re-uploading EEI_Banner_June2025.png.
  - Tags: empty for now. Add `eei`, `music-studio`, `video-assignments`, `assignments`, `resources` when tag taxonomy is created.
  - Squarespace CDN images: 14 images currently served via Jetpack proxy (`i0.wp.com`). Re-upload to WP media library in a future pass.
  - Internal links: none yet. Add cross-links to /soundcheck/, /eei-google-classroom-integration/, /teacher-audio-feedback/ etc. when those are also migrated to posts.
  - Schema.org JSON-LD: not embedded yet (Jetpack handles basic Article schema automatically).
---

<!-- wp:image {"linkDestination":"custom"} -->
<figure class="wp-block-image"><a href="https://www.essentialelementsinteractive.com/" target="_blank" rel="noopener"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/0d0001b5-29ab-4c7c-b3d1-9178f8a065eb/EEI_Banner_June2025.png?ssl=1" alt="Essential Elements Interactive banner"/></a></figure>
<!-- /wp:image -->

<!-- wp:heading {"style":{"typography":{"textAlign":"center"}}} -->
<h2 class="wp-block-heading has-text-align-center">THE POWERFUL CLOUD-BASED COMPANION TO THE ESSENTIAL ELEMENTS METHOD BOOKS</h2>
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
<figure class="wp-block-image"><a href="https://www.halleonard.com/ee/" target="_blank" rel="noopener"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/84d5eee1-03be-47c3-b1d4-8551c351de7e/EE2025_800x800.jpg?ssl=1" alt="Essential Elements method book covers"/></a></figure>
<!-- /wp:image -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:heading {"style":{"typography":{"textAlign":"center"}}} -->
<h2 class="wp-block-heading has-text-align-center"><strong>Essential Elements Interactive offers the following powerful tools:</strong></h2>
<!-- /wp:heading -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/b1fab22c-1f28-4565-8c4b-296e0a52c297/Frame%2B1.png?ssl=1" alt="EEi Music Studio feature graphic"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750725265354-1IJBY9HEMAXIBIRBWYDV/EEi_Blog_1.png?ssl=1" alt="Music Studio screen showing exercise notation with controls"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1680535986622-4N5TVE962EPX7W60NXUP/EEi%2BSD%2BCard%2BPic_0446-2.png?ssl=1" alt="Student playing along with EEi in classroom"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750725265301-WMVB3P9KFY6CBEV6Q6Y1/EEi_Blog_2.png?ssl=1" alt="Music Studio accompaniment selection"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750725700148-OWPM48WVFANGLCB43ZL0/EEi_Blog_3b.png?ssl=1" alt="Music Studio recording interface"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>The <strong><em>EEi Music Studio</em></strong> makes learning to play more effective and more fun… in class or at home!</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Features Include:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item --><li><strong>Professional Soloists</strong> demonstrating each exercise in the book</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Multiple Accompaniment Styles</strong> for each exercise, performed by professional musicians</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Recording Feature</strong> to share music with teachers, friends, and relatives</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Tempo Control</strong> for more effective learning and practice</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>EEi Practice Tools</strong> including built-in metronome, tuner, and fingering charts</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Easy-to-use Interface</strong> to make playing, recording, and sharing simple and fun</li><!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><strong>EEi Video Assignments</strong></h2>
<!-- /wp:heading -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750726841157-YHJL2W1WV9S0Z97S0YYQ/EEi_Blog_5.png?ssl=1" alt="Student recording video assignment in EEi"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1680539006094-8AZD762XTITC3CVET9V5/EEi%2BVideo%2BAssignments.png?ssl=1" alt="EEi Video Assignments submission flow"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Students can now record and share videos right in the EEi Music Studio. This allows teachers to provide more detailed feedback.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Video Assignments Features:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item --><li><strong>Video Recording in Music Studio</strong> to make a video directly from the music studio with EE Music on screen</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Video in All-Music</strong> allows students to make recordings of any music being performed in class</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Save to the Cloud</strong> means that videos are processed and stored in the cloud for easy use on the internet</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Submit to Teacher</strong> so teachers can assess students performance with more information</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Easy-to-use Interface</strong> to make playing, recording, and sharing simple and fun</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Download Videos</strong> to share with friends and family</li><!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/e3596754-14f1-4c17-8ee2-f6c25397cc10/Frame%2B4.png?ssl=1" alt="EEi Resources feature graphic"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750772064271-R5KGD39I3CN9LM9KVOUJ/Resources_Videos_Still.png?ssl=1" alt="EEi Resources video library"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750726954835-AD4U5CK3XCE28CRNBSR3/EEi_Blog_4%2B%281%29.png?ssl=1" alt="Resources library tile view"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1539008197649-8T1ZL57JRUKNM8YYQEQL/Customize%2BCurriculum.png?ssl=1" alt="Teacher uploading custom curriculum materials"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>The <strong><em>EEi Resources Section</em></strong> is filled with flexible, high-quality content designed to support music educators and students at every level. And with regular updates, it continues to evolve alongside your classroom needs.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>EEi Resources include:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item --><li><strong>400+ Instructional Videos</strong> recorded by EE Ambassadors and professionals in the field of music education</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Individual Studies</strong> for all instruments to reinforce key skills</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Music Theory</strong> lessons and worksheets</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Instrument Training Worksheets</strong> to develop tone and technique</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Bonus Songs</strong> including songs, duets, trios, and 50+ new pop arrangements</li><!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p><strong>Customizable Digital Classroom Experience</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>For teachers, EEi goes a step further—upload, organize, and share your own materials to create a personalized digital classroom experience your students can access anywhere.</p>
<!-- /wp:paragraph -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/da007377-6813-46a5-84be-a753482662cf/Frame%2B3.png?ssl=1" alt="EEi Assignments feature graphic"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750772784913-BFSQOIFRGD6OL65XPJH0/iPad_Grading_Still.png?ssl=1" alt="Teacher grading student recording on iPad"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1680539774094-A946GZFXSOTD48TLQWS5/Overview-%2BAssignments%2B6.png?ssl=1" alt="Teacher Gradebook assignments overview"/></figure>
<!-- /wp:image -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/1750773225655-9NULO74DDM1GUSCHGG7J/Assignments_Overview.png?ssl=1" alt="EEi Assignments dashboard"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>EEi Assignments allows teachers to guide students better in their home practice.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Features include:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item --><li><strong>Online Assignments</strong> to help students prepare for class</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Recording Assignments</strong> for teacher feedback</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>All-Music Studio</strong> for students to record any music</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Teacher Gradebook</strong> for quick access to student recordings</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Self-Assessments</strong> to help student further improve their musical knowledge and playing ability</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Practice Records</strong> to help students and teachers plan home practice while keeping parents informed of practice goals and progress</li><!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>With EEi Assignments, teachers can provide more guidance for students in their home practice and empower them to become better students and musicians.</p>
<!-- /wp:paragraph -->

<!-- wp:image -->
<figure class="wp-block-image"><img src="https://i0.wp.com/images.squarespace-cdn.com/content/v1/5b928a1e75f9ee72cbaf8c6f/51536bbb-2fb4-487e-a647-a891bb39e2c6/Frame%2B2.png?ssl=1" alt="EEi Communications feature graphic"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p><strong>Features include:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item --><li>Flexible Communication Tools that provide a variety of ways to communicate with students and parents</li><!-- /wp:list-item --><!-- wp:list-item --><li>In-app notifications to remind students about upcoming assignments and feedback</li><!-- /wp:list-item --></ul>
<!-- /wp:list -->
