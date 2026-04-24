<?php
/**
 * Blog index / posts archive template.
 */
get_header();
?>

    <div class="page-header">
        <div class="container">
            <h1 class="page-title">
                <?php
                if ( is_home() && ! is_front_page() ) {
                    single_post_title();
                } else {
                    esc_html_e( 'Blog', 'eeiblog' );
                }
                ?>
            </h1>
            <?php
            $blog_page_id = get_option( 'page_for_posts' );
            if ( $blog_page_id ) {
                $desc = get_post_field( 'post_content', $blog_page_id );
                if ( $desc ) {
                    echo '<p class="page-subtitle">' . wp_kses_post( wp_trim_words( $desc, 20 ) ) . '</p>';
                }
            }
            ?>
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
                        <p class="no-posts"><?php esc_html_e( 'No posts found.', 'eeiblog' ); ?></p>
                    <?php endif; ?>
                </div><!-- .content-area -->

                <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
                    <?php get_sidebar(); ?>
                <?php endif; ?>

            </div>
        </div>
    </main>

<?php get_footer(); ?>
