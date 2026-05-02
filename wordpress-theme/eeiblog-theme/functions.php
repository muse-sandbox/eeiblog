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
function eeiblog_posted_meta( $show_category = true, $show_date = true, $show_author = true ) {
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
