<?php
/**
 * Front page template — mirrors the EEi Blog homepage with hero banner and feature boxes.
 */
get_header();
?>

    <!-- ── Hero Banner ─────────────────────────────────────── -->
    <section class="hero-banner" role="banner">
        <?php if ( get_header_image() ) : ?>
            <img class="hero-img"
                 src="<?php header_image(); ?>"
                 width="<?php echo esc_attr( get_custom_header()->width ); ?>"
                 height="<?php echo esc_attr( get_custom_header()->height ); ?>"
                 alt=""
                 aria-hidden="true"
                 loading="eager">
        <?php endif; ?>

        <div class="hero-content">
            <?php
            // Allow the hero headline to be customised via the Customizer (stored as a theme mod).
            $hero_title = get_theme_mod(
                'eeiblog_hero_title',
                strtoupper( get_bloginfo( 'description' ) ) ?: 'WELCOME TO ' . strtoupper( get_bloginfo( 'name' ) )
            );
            $hero_text  = get_theme_mod( 'eeiblog_hero_text', '' );
            $hero_btn_label = get_theme_mod( 'eeiblog_hero_btn_label', __( 'Learn More', 'eeiblog' ) );
            $hero_btn_url   = get_theme_mod( 'eeiblog_hero_btn_url', '' );
            ?>
            <h1><?php echo esc_html( $hero_title ); ?></h1>
            <?php if ( $hero_text ) : ?>
                <p><?php echo esc_html( $hero_text ); ?></p>
            <?php endif; ?>
            <?php if ( $hero_btn_url && $hero_btn_label ) : ?>
                <a href="<?php echo esc_url( $hero_btn_url ); ?>" class="btn"><?php echo esc_html( $hero_btn_label ); ?></a>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── Feature Boxes ───────────────────────────────────── -->
    <?php
    // Feature boxes are defined as theme mods (3 boxes, each with heading/tagline/text/link).
    $boxes = array(
        array(
            'heading' => get_theme_mod( 'eeiblog_box1_heading', __( 'Teachers', 'eeiblog' ) ),
            'tagline' => get_theme_mod( 'eeiblog_box1_tagline', 'PLAN • SHARE • CONNECT' ),
            'text'    => get_theme_mod( 'eeiblog_box1_text', __( 'Access lesson plans, assignment tools, and classroom resources.', 'eeiblog' ) ),
            'url'     => get_theme_mod( 'eeiblog_box1_url', '' ),
            'label'   => get_theme_mod( 'eeiblog_box1_label', __( 'Get Started', 'eeiblog' ) ),
        ),
        array(
            'heading' => get_theme_mod( 'eeiblog_box2_heading', __( 'Students', 'eeiblog' ) ),
            'tagline' => get_theme_mod( 'eeiblog_box2_tagline', 'LEARN • PLAY & RECORD • HAVE FUN' ),
            'text'    => get_theme_mod( 'eeiblog_box2_text', __( 'Watch instructional videos, record yourself and get feedback.', 'eeiblog' ) ),
            'url'     => get_theme_mod( 'eeiblog_box2_url', '' ),
            'label'   => get_theme_mod( 'eeiblog_box2_label', __( 'Start Learning', 'eeiblog' ) ),
        ),
        array(
            'heading' => get_theme_mod( 'eeiblog_box3_heading', __( 'Latest Posts', 'eeiblog' ) ),
            'tagline' => get_theme_mod( 'eeiblog_box3_tagline', 'TIPS • TUTORIALS • NEWS' ),
            'text'    => get_theme_mod( 'eeiblog_box3_text', __( 'Browse our teaching tips, tutorials, and updates.', 'eeiblog' ) ),
            'url'     => get_theme_mod( 'eeiblog_box3_url', eeiblog_blog_index_url() ),
            'label'   => get_theme_mod( 'eeiblog_box3_label', __( 'Read Blog', 'eeiblog' ) ),
        ),
    );

    $has_any_box = array_filter( $boxes, fn( $b ) => ! empty( $b['heading'] ) );
    ?>
    <?php if ( $has_any_box ) : ?>
    <section class="feature-boxes">
        <div class="container">
            <?php foreach ( $boxes as $box ) :
                if ( empty( $box['heading'] ) ) continue; ?>
                <div class="feature-box">
                    <h2><?php echo esc_html( $box['heading'] ); ?></h2>
                    <?php if ( $box['tagline'] ) : ?>
                        <p class="tagline"><?php echo esc_html( $box['tagline'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( $box['text'] ) : ?>
                        <p><?php echo esc_html( $box['text'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( $box['url'] && $box['label'] ) : ?>
                        <a href="<?php echo esc_url( $box['url'] ); ?>" class="btn"><?php echo esc_html( $box['label'] ); ?></a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Recent Posts ────────────────────────────────────── -->
    <main id="main" class="site-main">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title"><?php esc_html_e( 'Recent Posts', 'eeiblog' ); ?></h2>
            </div>

            <?php
            $recent = new WP_Query( array(
                'posts_per_page' => 5,
                'post_status'    => 'publish',
            ) );

            if ( $recent->have_posts() ) : ?>
                <ul class="posts-list">
                    <?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
                        <li class="post-item">
                            <a class="post-thumbnail" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                                <?php eeiblog_post_thumbnail(); ?>
                            </a>
                            <div class="post-content">
                                <?php eeiblog_posted_meta(); ?>
                                <h2 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <p class="post-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                                <a href="<?php the_permalink(); ?>" class="read-more">
                                    <?php esc_html_e( 'Read More →', 'eeiblog' ); ?>
                                </a>
                            </div>
                        </li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>

                <div style="text-align:center; margin-top:32px;">
                    <a href="<?php echo esc_url( eeiblog_blog_index_url() ); ?>" class="btn" style="display:inline-block; padding:12px 28px; background:var(--color-accent); color:#fff; border-radius:4px; font-weight:700; text-decoration:none;">
                        <?php esc_html_e( 'View All Posts →', 'eeiblog' ); ?>
                    </a>
                </div>

            <?php else : ?>
                <p class="no-posts"><?php esc_html_e( 'No posts yet.', 'eeiblog' ); ?></p>
            <?php endif; ?>

        </div>
    </main>

<?php get_footer(); ?>
