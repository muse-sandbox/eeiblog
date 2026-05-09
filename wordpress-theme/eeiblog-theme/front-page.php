<?php
/**
 * Front page template — EEi Interactive home.
 *
 * Layout:
 *   - Hero block (EE Interactive product banner + headline/sub + CTA)
 *   - Audience section: Teachers   (banner overlay + 3 posts + view-all)
 *   - Audience section: Students   (banner overlay + 3 posts + view-all)
 *   - Audience section: News       (banner overlay + 3 posts + view-all)
 *
 * The three audience sections are driven by category slugs. Posts in `for-teachers`
 * (cat 76611481) appear in the Teachers section; posts in `for-students`
 * (76611482) in Students; posts in `news` (76611470) in News. Posts tagged with
 * BOTH for-teachers and for-students appear in both sections.
 */

get_header();
?>

<!-- ── Hero: EE Interactive product banner ─────────────────────────── -->
<section class="ee-hero">
    <a class="ee-hero-image" href="https://www.essentialelementsinteractive.com/" target="_blank" rel="noopener noreferrer">
        <img src="https://eeiblog.com/wp-content/uploads/2026/05/hybrid-squarespace-background-header-2023.webp"
             alt="Essential Elements Interactive — cloud-based companion to the EE method book"
             loading="eager" />
    </a>

    <div class="ee-hero-text">
        <h1 class="ee-hero-headline">
            The Powerful Cloud-Based Companion to the Essential Elements Method Book
        </h1>
        <p class="ee-hero-sub">
            Access instructional videos, bonus songs, SoundCheck Performance Assessment, and more!
        </p>
        <a class="ee-hero-cta" href="<?php echo esc_url( home_url( '/eei-overview-1/' ) ); ?>">
            Discover the Power of EE Interactive
        </a>
    </div>
</section>

<?php
/**
 * Render an audience section: banner overlay with title/tagline + 3 latest posts +
 * "View all →" link.
 *
 * @param array $args Required keys:
 *   - title       (string, e.g. 'TEACHERS')
 *   - tagline     (string, e.g. 'PLAN • SHARE • CONNECT')
 *   - cta_text    (string, button label inside the overlay, e.g. 'Transform How You Teach with EEi')
 *   - banner_url  (string, full URL to background image)
 *   - banner_alt  (string)
 *   - category    (string, category slug)
 *   - view_all_url (string, archive URL — used by the banner anchor AND the in-overlay button text container)
 *   - section_class (string, e.g. 'teachers' — appended to .audience-section--)
 */
function eeiblog_render_audience_section( array $args ): void {
    $title         = $args['title'];
    $tagline       = $args['tagline'];
    $cta_text      = $args['cta_text'];
    $banner_url    = $args['banner_url'];
    $banner_alt    = $args['banner_alt'];
    $category      = $args['category'];
    $view_all_url  = $args['view_all_url'];
    $section_class = $args['section_class'];
    // Optional separate URL for the in-overlay button. Defaults to the
    // banner's view-all URL so existing call sites keep working.
    $cta_url       = ! empty( $args['cta_url'] ) ? $args['cta_url'] : $view_all_url;
    ?>
    <section class="audience-section audience-section--<?php echo esc_attr( $section_class ); ?>">
        <div class="audience-banner"
             style="background-image: url('<?php echo esc_url( $banner_url ); ?>');">
            <!-- Stretched link covers the banner area for whole-block click. The
                 overlay above it has pointer-events: none so clicks pass through
                 to this anchor; the CTA button inside the overlay re-enables
                 pointer-events on itself and routes clicks to its own URL. This
                 avoids invalid <a>-inside-<a> nesting. -->
            <a class="audience-banner-link"
               href="<?php echo esc_url( $view_all_url ); ?>"
               aria-label="<?php echo esc_attr( $banner_alt ); ?>"></a>
            <div class="audience-overlay">
                <h2 class="audience-title"><?php echo esc_html( $title ); ?></h2>
                <p class="audience-tagline"><?php echo esc_html( $tagline ); ?></p>
                <?php if ( ! empty( $cta_text ) ) : ?>
                    <a class="btn-light audience-cta-btn" href="<?php echo esc_url( $cta_url ); ?>">
                        <?php echo esc_html( $cta_text ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php
        $audience_query = new WP_Query( array(
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'category_name'  => $category,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( $audience_query->have_posts() ) : ?>
            <ul class="audience-posts">
                <?php while ( $audience_query->have_posts() ) : $audience_query->the_post(); ?>
                    <li class="audience-post-card">
                        <a class="audience-post-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                            <?php
                            if ( has_post_thumbnail() ) {
                                the_post_thumbnail( 'medium_large' );
                            } else {
                                eeiblog_post_thumbnail();
                            }
                            ?>
                        </a>
                        <div class="audience-post-content">
                            <h3 class="audience-post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <p class="audience-post-excerpt">
                                <?php echo esc_html( get_the_excerpt() ); ?>
                            </p>
                        </div>
                    </li>
                <?php endwhile; wp_reset_postdata(); ?>
            </ul>
        <?php endif; ?>

        <div class="audience-cta">
            <a class="audience-view-all" href="<?php echo esc_url( $view_all_url ); ?>">
                View all →
            </a>
        </div>
    </section>
    <?php
}
?>

<!-- ── Audience: Teachers ──────────────────────────────────────────── -->
<?php eeiblog_render_audience_section( array(
    'title'         => 'TEACHERS',
    'tagline'       => 'PLAN • SHARE • CONNECT',
    'cta_text'      => 'Transform How You Teach with EEi',
    'banner_url'    => 'https://eeiblog.com/wp-content/uploads/2026/05/misc-2026-05-ind-teacher-wide.webp',
    'banner_alt'    => 'For Teachers — view all posts',
    'category'      => 'for-teachers',
    'view_all_url'  => home_url( '/category/for-teachers/' ),
    'section_class' => 'teachers',
) ); ?>

<!-- ── Audience: Students ──────────────────────────────────────────── -->
<?php eeiblog_render_audience_section( array(
    'title'         => 'STUDENTS',
    'tagline'       => 'LEARN • PLAY & RECORD • HAVE FUN',
    'cta_text'      => 'Turn Practice into Progress',
    'banner_url'    => 'https://eeiblog.com/wp-content/uploads/2026/05/misc-2026-05-student-device-2.webp',
    'banner_alt'    => 'For Students — view all posts',
    'category'      => 'for-students',
    'view_all_url'  => home_url( '/category/for-students/' ),
    'section_class' => 'students',
) ); ?>

<!-- ── Audience: News ──────────────────────────────────────────────── -->
<?php eeiblog_render_audience_section( array(
    'title'         => 'NEWS',
    'tagline'       => 'UPDATES • RELEASES • EVENTS',
    'cta_text'      => "See What's New in EEi",
    'banner_url'    => 'https://eeiblog.com/wp-content/uploads/2026/05/misc-2026-05-news-updates-header.webp',
    'banner_alt'    => 'News — view all posts',
    'category'      => 'news',
    'view_all_url'  => home_url( '/category/news/' ),
    'section_class' => 'news',
) ); ?>

<?php get_footer(); ?>
