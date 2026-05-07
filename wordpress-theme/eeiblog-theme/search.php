<?php
/**
 * Search results template — mirrors archive.php layout but with a
 * "Search results for: <query>" page header and a "no posts found"
 * message that surfaces the search form again so the visitor can
 * retry without leaving the page.
 */
get_header();
?>

    <div class="page-header">
        <div class="container">
            <h1 class="page-title">
                <?php
                printf(
                    /* translators: %s: search query */
                    esc_html__( 'Search results for: %s', 'eeiblog' ),
                    '<span>' . esc_html( get_search_query() ) . '</span>'
                );
                ?>
            </h1>
        </div>
    </div>

    <main id="main" class="site-main">
        <div class="container">
            <div class="<?php echo is_active_sidebar( 'sidebar-1' ) ? 'content-sidebar-wrap' : ''; ?>">

                <div class="content-area">
                    <?php if ( have_posts() ) : ?>
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
                        <p class="no-posts">
                            <?php esc_html_e( 'No posts matched your search. Try different keywords:', 'eeiblog' ); ?>
                        </p>
                        <?php get_search_form(); ?>
                    <?php endif; ?>
                </div>

                <?php if ( is_active_sidebar( 'sidebar-1' ) ) get_sidebar(); ?>

            </div>
        </div>
    </main>

<?php get_footer(); ?>
