    </div><!-- #content -->

    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="container">

            <!-- Footer Logo -->
            <div class="footer-brand">
                <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-site-name">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Copyright -->
            <div class="footer-info">
                <p class="copyright">
                    &copy; <?php echo esc_html( date( 'Y' ) ); ?>
                    <?php bloginfo( 'name' ); ?>.
                    <?php esc_html_e( 'All rights reserved.', 'eeiblog' ); ?>
                </p>
            </div>

            <!-- Footer Navigation -->
            <?php if ( has_nav_menu( 'footer' ) ) : ?>
                <nav class="footer-links" aria-label="<?php esc_attr_e( 'Footer Navigation', 'eeiblog' ); ?>">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'depth'          => 1,
                        'fallback_cb'    => false,
                        'items_wrap'     => '%3$s',
                        'walker'         => new class extends Walker_Nav_Menu {
                            public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
                                $output .= '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
                            }
                            public function end_el( &$output, $item, $depth = 0, $args = array() ) {}
                            public function start_lvl( &$output, $depth = 0, $args = array() ) {}
                            public function end_lvl( &$output, $depth = 0, $args = array() ) {}
                        },
                    ) );
                    ?>
                </nav>
            <?php else : ?>
                <nav class="footer-links" aria-label="<?php esc_attr_e( 'Footer Navigation', 'eeiblog' ); ?>">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'eeiblog' ); ?></a>
                    <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy Policy', 'eeiblog' ); ?></a>
                </nav>
            <?php endif; ?>

        </div><!-- .container -->
    </footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
