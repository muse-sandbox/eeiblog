<?php
/**
 * Single post template.
 */
get_header();
?>

    <main id="main" class="site-main">
        <div class="container">
            <div class="<?php echo is_active_sidebar( 'sidebar-1' ) ? 'content-sidebar-wrap' : ''; ?>">

                <div class="content-area">
                    <?php while ( have_posts() ) : the_post(); ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                            <!-- Post Header -->
                            <header class="single-post-header">
                                <?php eeiblog_posted_meta(); ?>

                                <h1 class="post-title entry-title"><?php the_title(); ?></h1>

                                <p class="post-author">
                                    <?php
                                    printf(
                                        /* translators: %s: author name */
                                        esc_html__( 'By %s', 'eeiblog' ),
                                        '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
                                    );
                                    ?>
                                </p>
                            </header>

                            <!-- Featured Image -->
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="post-featured-image">
                                    <?php the_post_thumbnail( 'eeiblog-featured', array( 'loading' => 'eager' ) ); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Post Body -->
                            <div class="post-content-body entry-content">
                                <?php
                                the_content( sprintf(
                                    wp_kses(
                                        /* translators: %s: Post title */
                                        __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'eeiblog' ),
                                        array( 'span' => array( 'class' => array() ) )
                                    ),
                                    wp_kses_post( get_the_title() )
                                ) );

                                wp_link_pages( array(
                                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'eeiblog' ),
                                    'after'  => '</div>',
                                ) );
                                ?>
                            </div><!-- .post-content-body -->

                            <!-- Tags -->
                            <?php
                            $tags = get_the_tags();
                            if ( $tags ) :
                            ?>
                                <div class="post-tags">
                                    <span class="label"><?php esc_html_e( 'Tags:', 'eeiblog' ); ?></span>
                                    <?php foreach ( $tags as $tag ) : ?>
                                        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
                                            <?php echo esc_html( $tag->name ); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </article><!-- #post-<?php the_ID(); ?> -->

                        <!-- Prev / Next Post Navigation -->
                        <nav class="post-navigation" aria-label="<?php esc_attr_e( 'Post Navigation', 'eeiblog' ); ?>">
                            <div class="nav-previous">
                                <?php
                                $prev = get_previous_post();
                                if ( $prev ) :
                                ?>
                                    <span class="nav-label"><?php esc_html_e( '← Previous Post', 'eeiblog' ); ?></span>
                                    <a class="nav-title" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
                                        <?php echo esc_html( get_the_title( $prev ) ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="nav-next">
                                <?php
                                $next = get_next_post();
                                if ( $next ) :
                                ?>
                                    <span class="nav-label"><?php esc_html_e( 'Next Post →', 'eeiblog' ); ?></span>
                                    <a class="nav-title" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
                                        <?php echo esc_html( get_the_title( $next ) ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </nav>

                        <!-- Comments -->
                        <?php
                        if ( comments_open() || get_comments_number() ) {
                            comments_template();
                        }
                        ?>

                    <?php endwhile; ?>
                </div><!-- .content-area -->

                <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
                    <?php get_sidebar(); ?>
                <?php endif; ?>

            </div>
        </div>
    </main>

<?php get_footer(); ?>
