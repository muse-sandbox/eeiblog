<?php
/**
 * EEi Blog theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EEIBLOG_VERSION', '1.0.0' );

/* -------------------------------------------------------
   Theme Setup
   ------------------------------------------------------- */
function eeiblog_setup() {
    load_theme_textdomain( 'eeiblog', get_template_directory() . '/languages' );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'responsive-embeds' );

    // Custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array( 'site-title', 'site-description' ),
    ) );

    // Custom header image (hero banner)
    add_theme_support( 'custom-header', array(
        'default-text-color'     => 'ffffff',
        'width'                  => 1600,
        'height'                 => 500,
        'flex-height'            => true,
        'flex-width'             => true,
        'uploads'                => true,
        'wp-head-callback'       => 'eeiblog_header_style',
    ) );

    // Image sizes
    add_image_size( 'eeiblog-thumbnail', 480, 300, true );
    add_image_size( 'eeiblog-featured',  1200, 600, true );

    // Menus
    register_nav_menus( array(
        'primary' => __( 'Primary Navigation', 'eeiblog' ),
        'footer'  => __( 'Footer Links', 'eeiblog' ),
    ) );
}
add_action( 'after_setup_theme', 'eeiblog_setup' );

/* -------------------------------------------------------
   Register an extra "Light" block style for the core Button
   block, so editors can pick it from Button → Styles in the
   sidebar (alongside Default and Outline). Visual rules live
   in style.css under .wp-block-button.is-style-light. Use
   case: white pill on dark hero / banner backgrounds.
   ------------------------------------------------------- */
function eeiblog_register_block_styles() {
    if ( ! function_exists( 'register_block_style' ) ) {
        return;
    }
    register_block_style( 'core/button', array(
        'name'  => 'light',
        'label' => __( 'Light (on dark)', 'eeiblog' ),
    ) );
}
add_action( 'init', 'eeiblog_register_block_styles' );

/* -------------------------------------------------------
   Enqueue Styles & Scripts
   ------------------------------------------------------- */
function eeiblog_enqueue() {
    // Main stylesheet
    wp_enqueue_style(
        'eeiblog-style',
        get_stylesheet_uri(),
        array(),
        EEIBLOG_VERSION
    );

    // Google Fonts
    wp_enqueue_style(
        'eeiblog-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );

    // Navigation JS
    wp_enqueue_script(
        'eeiblog-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        array(),
        EEIBLOG_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'eeiblog_enqueue' );

/* Use Inter font in body */
function eeiblog_body_font_css() {
    echo '<style>body, .site-header, .main-navigation { font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }</style>';
}
add_action( 'wp_head', 'eeiblog_body_font_css' );

/* -------------------------------------------------------
   Custom Header Style Callback
   ------------------------------------------------------- */
function eeiblog_header_style() {
    $header_image = get_header_image();
    if ( ! $header_image ) return;
    ?>
    <style>
        .hero-banner { background-image: url(<?php echo esc_url( $header_image ); ?>); background-size: cover; background-position: center; }
    </style>
    <?php
}

/* -------------------------------------------------------
   Widgets / Sidebars
   ------------------------------------------------------- */
function eeiblog_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Blog Sidebar', 'eeiblog' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Widgets shown in the blog sidebar.', 'eeiblog' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer Widget Area', 'eeiblog' ),
        'id'            => 'footer-widgets',
        'description'   => __( 'Optional widgets above the footer.', 'eeiblog' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'eeiblog_widgets_init' );

/* -------------------------------------------------------
   Custom Walker: drop-down nav with ARIA
   ------------------------------------------------------- */
class EEiBlog_Nav_Walker extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= '<ul class="sub-menu" role="menu">';
    }

    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= '</ul>';
    }

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $has_children = in_array( 'menu-item-has-children', $classes );

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $output .= '<li' . $class_names . '>';

        $atts                 = array();
        $atts['title']        = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target']       = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']          = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['href']         = ! empty( $item->url ) ? $item->url : '';
        if ( $has_children ) {
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';
        }

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value       = esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title  = apply_filters( 'the_title', $item->title, $item->ID );
        $output .= '<a' . $attributes . '>' . $title . '</a>';
    }

    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= '</li>';
    }
}

/* -------------------------------------------------------
   Post excerpt length & more link
   ------------------------------------------------------- */
function eeiblog_excerpt_length( $length ) {
    return 28;
}
add_filter( 'excerpt_length', 'eeiblog_excerpt_length', 999 );

function eeiblog_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'eeiblog_excerpt_more' );

/* -------------------------------------------------------
   Helpers
   ------------------------------------------------------- */

/**
 * Returns the post thumbnail or a styled placeholder.
 */
function eeiblog_post_thumbnail( $size = 'eeiblog-thumbnail', $echo = true ) {
    $html = '';
    if ( has_post_thumbnail() ) {
        $html = get_the_post_thumbnail( null, $size, array( 'loading' => 'lazy' ) );
    } else {
        $html = '<div class="post-thumbnail-placeholder" aria-hidden="true">📝</div>';
    }
    if ( $echo ) {
        echo $html;
    }
    return $html;
}

/**
 * Posted-on meta line (category + date + optional author).
 *
 * Author is OFF by default. The blog runs under a single editorial
 * voice, so per-post bylines aren't useful in either the post header
 * or the listing cards. Pass $show_author=true explicitly if you ever
 * want it back on a specific surface.
 */
function eeiblog_posted_meta( $show_category = true, $show_date = true, $show_author = false ) {
    $category_html = '';
    if ( $show_category ) {
        $categories = get_the_category();
        if ( $categories ) {
            $cat           = $categories[0];
            $category_html = sprintf(
                '<span class="post-category"><a href="%s">%s</a></span>',
                esc_url( get_category_link( $cat->term_id ) ),
                esc_html( $cat->name )
            );
        }
    }

    $date_html = '';
    if ( $show_date ) {
        $date_html = sprintf(
            '<span class="post-date-label">%1$s</span> <time class="entry-date published" datetime="%2$s">%3$s</time>',
            esc_html__( 'Published:', 'eeiblog' ),
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date( 'F j, Y' ) )
        );
    }

    $author_html = '';
    if ( $show_author ) {
        $author_html = '<span class="post-author-by">' . esc_html__( 'by', 'eeiblog' ) . ' ' .
            sprintf( '<span class="post-author-name">%s</span>', esc_html( get_the_author() ) ) .
            '</span>';
    }

    if ( ! $category_html && ! $date_html && ! $author_html ) {
        return;
    }

    echo '<div class="post-meta">';
    echo $category_html;
    echo $date_html;
    echo $author_html;
    echo '</div>';
}

/**
 * Numeric pagination.
 */
function eeiblog_pagination() {
    global $wp_query;
    if ( $wp_query->max_num_pages <= 1 ) return;

    echo '<nav class="pagination" aria-label="' . esc_attr__( 'Posts pagination', 'eeiblog' ) . '">';
    echo paginate_links( array(
        'total'        => $wp_query->max_num_pages,
        'current'      => max( 1, get_query_var( 'paged' ) ),
        'mid_size'     => 2,
        'prev_text'    => '&larr;',
        'next_text'    => '&rarr;',
        'type'         => 'plain',
        'before_page_number' => '<span class="screen-reader-text">' . __( 'Page', 'eeiblog' ) . ' </span>',
    ) );
    echo '</nav>';
}

/* -------------------------------------------------------
   Body Classes
   ------------------------------------------------------- */
function eeiblog_body_classes( $classes ) {
    if ( is_singular() ) {
        $classes[] = 'singular';
    }
    if ( is_single() ) {
        // Single posts always get the content+sidebar grid (related posts live there).
        $classes[] = 'has-sidebar';
    } elseif ( is_active_sidebar( 'sidebar-1' ) && ! is_singular( 'page' ) ) {
        $classes[] = 'has-sidebar';
    }
    return $classes;
}
add_filter( 'body_class', 'eeiblog_body_classes' );

/* -------------------------------------------------------
   Related posts by shared tags. Renders a `.widget` block
   compatible with the existing sidebar widget styling.
   Falls back to most-recent same-category posts when the
   current post has no tags.
   ------------------------------------------------------- */
function eeiblog_render_related_posts_widget( $count = 6 ) {
    if ( ! is_single() ) {
        return;
    }

    $post_id = get_the_ID();
    $tag_ids = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );

    // During migration most content is still in `draft`; show drafts/pending to
    // logged-in users so the related list isn't empty before launch. Anonymous
    // visitors see only published posts (the default).
    $statuses = array( 'publish' );
    if ( is_user_logged_in() ) {
        $statuses = array( 'publish', 'draft', 'pending', 'private', 'future' );
    }

    $args = array(
        'post__not_in'        => array( $post_id ),
        'posts_per_page'      => $count,
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'post_status'         => $statuses,
    );

    if ( ! empty( $tag_ids ) ) {
        $args['tag__in'] = $tag_ids;
    } else {
        $cat_ids = wp_get_post_categories( $post_id );
        if ( empty( $cat_ids ) ) {
            return;
        }
        $args['category__in'] = $cat_ids;
    }

    $related = new WP_Query( $args );

    if ( ! $related->have_posts() ) {
        wp_reset_postdata();
        return;
    }

    echo '<div class="widget widget-related-posts">';
    echo '<h3 class="widget-title">' . esc_html__( 'Related Posts', 'eeiblog' ) . '</h3>';
    echo '<ul>';
    while ( $related->have_posts() ) {
        $related->the_post();
        printf(
            '<li><a href="%s">%s</a></li>',
            esc_url( get_permalink() ),
            esc_html( get_the_title() )
        );
    }
    echo '</ul>';
    echo '</div>';

    wp_reset_postdata();
}

/* -------------------------------------------------------
   Jetpack Related Posts: disable auto-injection so we can
   place the [jetpack-related-posts] shortcode explicitly
   at the bottom of single.php instead of letting Jetpack
   append it to the_content automatically (which produces
   inconsistent placement and risks double-rendering when
   we call it ourselves).
   ------------------------------------------------------- */
add_filter( 'jetpack_relatedposts_filter_enabled_for_request', '__return_false' );

/* -------------------------------------------------------
   Theme-bundled vanilla-JS lightbox for content images on
   singular pages. See assets/js/lightbox.js for behavior.
   Version is pinned to the active theme version so cache
   busts automatically on each release.
   ------------------------------------------------------- */
function eeiblog_enqueue_lightbox() {
    if ( ! is_singular() ) {
        return;
    }
    wp_enqueue_script(
        'eeiblog-lightbox',
        get_theme_file_uri( 'assets/js/lightbox.js' ),
        array(),
        wp_get_theme()->get( 'Version' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'eeiblog_enqueue_lightbox' );

/* -------------------------------------------------------
   Theme-bundled vanilla-JS gallery carousel — converts
   multi-image WordPress galleries into Squarespace-style
   "big preview + thumbnails" widgets on singular pages.
   Depends on the lightbox script (loaded first) so that
   clicking the carousel main image still opens the lightbox.
   See assets/js/gallery-carousel.js for behavior.
   ------------------------------------------------------- */
function eeiblog_enqueue_gallery_carousel() {
    if ( ! is_singular() ) {
        return;
    }
    wp_enqueue_script(
        'eeiblog-gallery-carousel',
        get_theme_file_uri( 'assets/js/gallery-carousel.js' ),
        array( 'eeiblog-lightbox' ),
        wp_get_theme()->get( 'Version' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'eeiblog_enqueue_gallery_carousel' );

/* -------------------------------------------------------
   Resolve the canonical "blog index" URL with a robust
   fallback chain. Used by front-page.php's "View All Posts"
   button and feature box #3 default URL.

   Falls back through:
     1. page_for_posts setting (if that page is published).
     2. home_url('/?post_type=post') — shows the full post
        archive: with a static front page, hitting the home
        URL with a query string makes is_home() true and WP
        renders home.php with the default main query (all
        posts, paginated). This is the most idiomatic "all
        posts" URL when no Posts page is configured.

   Background: page_for_posts had pointed at a page that was
   later trashed, and get_permalink( 0 ) silently falls back
   to the most recent post — sending users to a random article.
   ------------------------------------------------------- */
function eeiblog_blog_index_url() {
    $pfp_id = (int) get_option( 'page_for_posts' );
    if ( $pfp_id && get_post_status( $pfp_id ) === 'publish' ) {
        return get_permalink( $pfp_id );
    }
    return home_url( '/?post_type=post' );
}

/* -------------------------------------------------------
   Suppress WordPress.com default "by Author" byline on
   single posts. Our theme handles meta via
   eeiblog_posted_meta() in single.php.
   ------------------------------------------------------- */
add_filter( 'the_content', function( $content ) {
    if ( ! is_single() ) {
        return $content;
    }
    return preg_replace( '/<p[^>]*class="[^"]*post-author[^"]*"[^>]*>.*?<\/p>/is', '', $content );
}, 99 );

add_filter( 'author_link', '__return_empty_string', 99 );

/* -------------------------------------------------------
   Bootstrap: create the default Primary + Footer navigation
   menus, then assign them to this theme's locations.

   Two separate gates:
     1. `eeiblog_default_menu_created` — global option, set
        once when the menus are first created. Prevents the
        seeder from clobbering menus Roman edits later in
        Appearance → Menus.
     2. `eeiblog_default_menu_assigned_<theme_slug>` — per-
        theme-dir option, set once per fresh theme directory.
        WP stores menu→location assignments in
        theme_mods_<slug>; when the theme directory changes
        (zoo cleanup, dir rename), the new slug has empty
        theme_mods and primary/footer end up unassigned.
        This gate ensures the locations get re-pointed at
        the existing menus on first activation under the new
        slug, without overwriting later edits.

   Hooked twice (after_switch_theme + admin_init) to seed
   regardless of activation method; the gates short-circuit
   the redundant pass.
   ------------------------------------------------------- */
function eeiblog_bootstrap_default_menus() {
    $needs_create = ! get_option( 'eeiblog_default_menu_created' );
    $assign_flag  = 'eeiblog_default_menu_assigned_' . get_stylesheet();
    $needs_assign = ! get_option( $assign_flag );

    if ( ! $needs_create && ! $needs_assign ) {
        return;
    }

    // wp_update_nav_menu_item lives in wp-admin/includes/nav-menu.php,
    // which is not loaded for non-admin requests (e.g., after_switch_theme
    // can fire from WP-CLI or REST). Pull it in defensively.
    if ( ! function_exists( 'wp_update_nav_menu_item' ) ) {
        require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
    }
    if ( ! function_exists( 'wp_update_nav_menu_item' ) ) {
        return; // give up cleanly if the include is unavailable
    }

    // ─── Primary menu ─────────────────────────────────────────────
    $primary_obj = wp_get_nav_menu_object( 'Primary' );
    $primary_id  = $primary_obj ? (int) $primary_obj->term_id : wp_create_nav_menu( 'Primary' );
    if ( is_wp_error( $primary_id ) ) {
        return;
    }

    // Item creation only on the very first run — re-running would
    // duplicate every entry on every theme dir change.
    if ( $needs_create ) {

    // Each top-level entry: [ title, url, target ('' or '_blank'), children[] ].
    $primary_items = array(
        array( 'Home',           home_url( '/' ),                        '',       array() ),
        array( 'Teaching Tips',  home_url( '/category/teaching-tips/' ), '',       array() ),
        array( 'EE Interactive', home_url( '/eei-overview-1/' ),         '',       array(
            array( 'EE Overview',                      home_url( '/eei-overview-1/' ),                   '' ),
            array( 'Soundcheck',                       home_url( '/soundcheck/' ),                       '' ),
            array( 'Five Ways to Get Started',         home_url( '/getting-started/' ),                  '' ),
            array( 'Teacher Audio Feedback',           home_url( '/teacher-audio-feedback/' ),           '' ),
            array( 'EEi Webinars',                     home_url( '/eei-webinars/' ),                     '' ),
            array( 'EEi Google Classroom Integration', home_url( '/eei-google-classroom-integration/' ), '' ),
        ) ),
        array( 'EE Method',      'https://www.halleonard.com/ee/',       '_blank', array(
            array( 'EE Method Books',           'https://www.halleonard.com/ee/',         '_blank' ),
            array( 'EE Band Methods',           'https://www.halleonard.com/ee/band/',    '_blank' ),
            array( 'EE String Methods',         'https://www.halleonard.com/ee/strings/', '_blank' ),
            array( 'EE Correlated Collections', home_url( '/correlatedcollections/' ),    ''       ),
            array( 'EE Digital Books',          home_url( '/ee-digital-books/' ),         ''       ),
        ) ),
        array( 'Subscribe',      home_url( '/subscribe/' ),              '',       array() ),
    );

    foreach ( $primary_items as $i => $item ) {
        list( $title, $url, $target, $children ) = $item;
        $parent_id = wp_update_nav_menu_item( $primary_id, 0, array(
            'menu-item-title'      => $title,
            'menu-item-url'        => $url,
            'menu-item-target'     => $target,
            'menu-item-attr-title' => '',
            'menu-item-status'     => 'publish',
            'menu-item-position'   => $i + 1,
            'menu-item-type'       => 'custom',
        ) );
        if ( is_wp_error( $parent_id ) ) {
            continue;
        }
        foreach ( $children as $j => $child ) {
            list( $c_title, $c_url, $c_target ) = $child;
            wp_update_nav_menu_item( $primary_id, 0, array(
                'menu-item-title'      => $c_title,
                'menu-item-url'        => $c_url,
                'menu-item-target'     => $c_target,
                'menu-item-attr-title' => '',
                'menu-item-status'     => 'publish',
                'menu-item-parent-id'  => $parent_id,
                'menu-item-position'   => $j + 1,
                'menu-item-type'       => 'custom',
            ) );
        }
    }

    } // end if ( $needs_create ) for Primary items

    // ─── Footer menu ──────────────────────────────────────────────
    $footer_obj = wp_get_nav_menu_object( 'Footer' );
    $footer_id  = $footer_obj ? (int) $footer_obj->term_id : wp_create_nav_menu( 'Footer' );
    if ( $needs_create && ! is_wp_error( $footer_id ) ) {
        // EEi Lessons / EEi Tutorials / Hal Leonard Website moved out of
        // the bottom legal-style strip — Lessons + Tutorials are already
        // reachable via the Footer Categories block above, and the Hal
        // Leonard link belongs alongside the new "Our Products" block.
        $footer_items = array(
            array( 'Home',                 home_url( '/' ),                ''       ),
            array( 'Subscribe',            home_url( '/subscribe/' ),      ''       ),
            array( 'Terms & Conditions',   home_url( '/terms-of-use/' ),   ''       ),
            array( 'Privacy Policy',       home_url( '/privacy-policy/' ), ''       ),
        );
        foreach ( $footer_items as $i => $f ) {
            list( $title, $url, $target ) = $f;
            wp_update_nav_menu_item( $footer_id, 0, array(
                'menu-item-title'      => $title,
                'menu-item-url'        => $url,
                'menu-item-target'     => $target,
                'menu-item-attr-title' => '',
                'menu-item-status'     => 'publish',
                'menu-item-position'   => $i + 1,
                'menu-item-type'       => 'custom',
            ) );
        }
    }

    // Assign both menus to this theme's locations. Runs once per
    // theme directory (theme_mods are stored as theme_mods_<slug>,
    // so a fresh install under a new slug starts with empty mods —
    // we re-point primary/footer at the existing menu term IDs
    // instead of orphaning them).
    if ( $needs_assign ) {
        $locations = (array) get_theme_mod( 'nav_menu_locations' );
        $locations['primary'] = $primary_id;
        if ( ! is_wp_error( $footer_id ) ) {
            $locations['footer'] = $footer_id;
        }
        set_theme_mod( 'nav_menu_locations', $locations );
        update_option( $assign_flag, 1 );
    }

    if ( $needs_create ) {
        update_option( 'eeiblog_default_menu_created', 1 );
    }
}
add_action( 'after_switch_theme', 'eeiblog_bootstrap_default_menus' );
add_action( 'admin_init',         'eeiblog_bootstrap_default_menus' );
