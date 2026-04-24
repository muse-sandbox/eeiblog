<?php
/**
 * 404 Error page template.
 */
get_header();
?>

    <main id="main" class="site-main">
        <div class="container">
            <section class="error-404 not-found">
                <div class="error-code" aria-hidden="true">404</div>
                <h1><?php esc_html_e( 'Page Not Found', 'eeiblog' ); ?></h1>
                <p><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'eeiblog' ); ?></p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn">
                    <?php esc_html_e( '← Back to Home', 'eeiblog' ); ?>
                </a>

                <div style="margin-top:48px; max-width:400px; margin-left:auto; margin-right:auto;">
                    <?php get_search_form(); ?>
                </div>
            </section>
        </div>
    </main>

<?php get_footer(); ?>
