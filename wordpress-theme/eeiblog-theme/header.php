<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

    <header id="masthead" class="site-header" role="banner">
        <div class="container">

            <!-- Logo / Site Title -->
            <div class="site-branding">
                <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    </h1>
                <?php endif; ?>
            </div><!-- .site-branding -->

            <!-- Mobile menu toggle -->
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'eeiblog' ); ?>">
                ☰
            </button>

            <!-- Primary Navigation -->
            <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'eeiblog' ); ?>">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'walker'         => new EEiBlog_Nav_Walker(),
                    'fallback_cb'    => 'eeiblog_fallback_menu',
                ) );
                ?>
            </nav><!-- #site-navigation -->

            <!-- Site search trigger + form (toggled in navigation.js) -->
            <button class="header-search-toggle" type="button"
                    aria-expanded="false"
                    aria-controls="header-search-form"
                    aria-label="<?php esc_attr_e( 'Toggle search', 'eeiblog' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true" focusable="false">
                    <circle cx="11" cy="11" r="7"/>
                    <line x1="16.5" y1="16.5" x2="21" y2="21"/>
                </svg>
            </button>
            <div id="header-search-form" class="header-search-form" hidden>
                <?php get_search_form(); ?>
            </div>

        </div><!-- .container -->
    </header><!-- #masthead -->

    <div id="content" class="site-content">
<?php

/**
 * Fallback menu shown when no menu is assigned.
 */
function eeiblog_fallback_menu() {
    echo '<ul id="primary-menu">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'eeiblog' ) . '</a></li>';
    if ( is_user_logged_in() ) {
        echo '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Add Menu', 'eeiblog' ) . '</a></li>';
    }
    echo '</ul>';
}
