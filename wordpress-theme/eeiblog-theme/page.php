<?php
/**
 * Static page template.
 */
get_header();
?>

    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><?php the_title(); ?></h1>
        </div>
    </div>

    <main id="main" class="site-main">
        <div class="container">

            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content' ); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="post-featured-image" style="margin-bottom:32px;">
                            <?php the_post_thumbnail( 'eeiblog-featured' ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-content-body entry-content">
                        <?php
                        the_content();
                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'eeiblog' ),
                            'after'  => '</div>',
                        ) );
                        ?>
                    </div>
                </article>
            <?php endwhile; ?>

        </div>
    </main>

<?php get_footer(); ?>
