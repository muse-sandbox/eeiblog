<?php
/**
 * Main fallback template — used when no more specific template is found.
 */
get_header();
?>

    <main id="main" class="site-main">
        <div class="container">
            <?php if ( have_posts() ) : ?>

                <?php if ( is_home() && ! is_front_page() ) : ?>
                    <div class="page-header">
                        <h1 class="page-title"><?php esc_html_e( 'Blog', 'eeiblog' ); ?></h1>
                    </div>
                <?php endif; ?>

                <ul class="posts-list">
                    <?php while ( have_posts() ) : the_post(); ?>
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
                    <?php endwhile; ?>
                </ul>

                <?php eeiblog_pagination(); ?>

            <?php else : ?>
                <p class="no-posts"><?php esc_html_e( 'No content found.', 'eeiblog' ); ?></p>
            <?php endif; ?>
        </div>
    </main>

<?php get_footer(); ?>
