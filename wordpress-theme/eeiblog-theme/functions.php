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
        'default-image'          => get_template_directory_uri() . '/assets/images/hero-default.jpg',
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
 * Posted-on meta line (date + author).
 */
function eeiblog_posted_meta( $show_category = true ) {
    $date_html = sprintf(
        '<time class="entry-date published" datetime="%1$s">%2$s</time>',
        esc_attr( get_the_date( DATE_W3C ) ),
        esc_html( get_the_date( 'F j, Y' ) )
    );

    $author_html = sprintf(
        '<span class="post-author-name">%s</span>',
        esc_html( get_the_author() )
    );

    $category_html = '';
    if ( $show_category ) {
        $categories = get_the_category();
        if ( $categories ) {
            $cat         = $categories[0];
            $category_html = sprintf(
                '<span class="post-category"><a href="%s">%s</a></span>',
                esc_url( get_category_link( $cat->term_id ) ),
                esc_html( $cat->name )
            );
        }
    }

    echo '<div class="post-meta">';
    if ( $category_html ) echo $category_html;
    echo $date_html;
    echo '<span class="post-author-by">' . esc_html__( 'by', 'eeiblog' ) . ' ' . $author_html . '</span>';
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
    if ( is_active_sidebar( 'sidebar-1' ) && ! is_singular( 'page' ) ) {
        $classes[] = 'has-sidebar';
    }
    return $classes;
}
add_filter( 'body_class', 'eeiblog_body_classes' );
