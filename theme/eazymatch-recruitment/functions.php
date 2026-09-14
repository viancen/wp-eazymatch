<?php
/**
 * EazyMatch Recruitment theme functions.
 *
 * @package EazyMatch_Recruitment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EMR_VERSION', '1.0.0' );
define( 'EMR_DIR', get_template_directory() );
define( 'EMR_URI', get_template_directory_uri() );

/**
 * Theme setup.
 */
function emr_setup() {
	load_theme_textdomain( 'eazymatch-recruitment', EMR_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Hoofdmenu', 'eazymatch-recruitment' ),
			'footer'  => __( 'Footermenu', 'eazymatch-recruitment' ),
			'legal'   => __( 'Juridisch menu (onderaan)', 'eazymatch-recruitment' ),
		)
	);

	add_image_size( 'emr-card', 720, 420, true );
}
add_action( 'after_setup_theme', 'emr_setup' );

/**
 * Content width for oEmbeds and large images.
 */
function emr_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'emr_content_width', 780 );
}
add_action( 'after_setup_theme', 'emr_content_width', 0 );

/**
 * Front-end assets.
 */
function emr_enqueue_assets() {
	$style_path = EMR_DIR . '/style.css';
	$version    = file_exists( $style_path ) ? (string) filemtime( $style_path ) : EMR_VERSION;

	wp_enqueue_style( 'emr-style', get_stylesheet_uri(), array(), $version );

	// Styling for the markup the EazyMatch plugin renders.
	$plugin_css = EMR_DIR . '/assets/css/eazymatch.css';
	wp_enqueue_style(
		'emr-eazymatch',
		EMR_URI . '/assets/css/eazymatch.css',
		array( 'emr-style' ),
		file_exists( $plugin_css ) ? (string) filemtime( $plugin_css ) : EMR_VERSION
	);

	wp_add_inline_style( 'emr-style', emr_customizer_css() );

	$script = EMR_DIR . '/assets/js/theme.js';
	wp_enqueue_script(
		'emr-theme',
		EMR_URI . '/assets/js/theme.js',
		array(),
		file_exists( $script ) ? (string) filemtime( $script ) : EMR_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'emr_enqueue_assets' );

/**
 * Widget areas.
 */
function emr_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Zijbalk', 'eazymatch-recruitment' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Verschijnt naast berichten en archieven.', 'eazymatch-recruitment' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number. */
				'name'          => sprintf( __( 'Footer kolom %d', 'eazymatch-recruitment' ), $i ),
				'id'            => 'footer-' . $i,
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'emr_widgets_init' );

/**
 * Is the EazyMatch plugin available?
 *
 * @return bool
 */
function emr_plugin_active() {
	return shortcode_exists( 'eazymatch' );
}

/**
 * Render an [eazymatch] shortcode, or a placeholder notice for administrators
 * when the plugin is not active.
 *
 * @param string $shortcode Complete shortcode string.
 *
 * @return string
 */
function emr_eazymatch( $shortcode ) {
	if ( emr_plugin_active() ) {
		return do_shortcode( $shortcode );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return '';
	}

	return '<div class="emol-no-results">' . sprintf(
		/* translators: %s: shortcode. */
		esc_html__( 'De EazyMatch-plugin is niet actief. Activeer de plugin om %s te tonen. Deze melding is alleen zichtbaar voor beheerders.', 'eazymatch-recruitment' ),
		'<code>' . esc_html( $shortcode ) . '</code>'
	) . '</div>';
}

/**
 * URL of a page that is configured in the EazyMatch settings.
 *
 * The plugin stores the page slug (post_name) in these options.
 *
 * @param string $option Option name, e.g. emol_job_search_page.
 *
 * @return string Empty string when the page is unknown.
 */
function emr_eazymatch_page_url( $option ) {
	$slug = get_option( $option );

	if ( empty( $slug ) || ! is_string( $slug ) ) {
		return '';
	}

	$page = get_page_by_path( $slug );

	if ( ! $page instanceof WP_Post ) {
		return '';
	}

	return (string) get_permalink( $page );
}

/**
 * URL of the job search page, with a sensible fallback.
 *
 * @return string
 */
function emr_jobs_url() {
	$url = emr_eazymatch_page_url( 'emol_job_search_page' );

	if ( '' !== $url ) {
		return $url;
	}

	$slug = get_option( 'emol_job_search_url' );

	if ( ! empty( $slug ) ) {
		return home_url( '/' . trim( (string) $slug, '/' ) . '/' );
	}

	return home_url( '/' );
}

/**
 * Body classes.
 *
 * @param string[] $classes Existing classes.
 *
 * @return string[]
 */
function emr_body_classes( $classes ) {
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'emr-no-sidebar';
	}

	if ( is_page() && emr_page_has_eazymatch_view( get_the_ID() ) ) {
		$classes[] = 'emr-eazymatch-page';
	}

	return $classes;
}
add_filter( 'body_class', 'emr_body_classes' );

/**
 * Does this page contain an [eazymatch] shortcode?
 *
 * @param int    $post_id Post id.
 * @param string $view    Optional specific view to look for.
 *
 * @return bool
 */
function emr_page_has_eazymatch_view( $post_id, $view = '' ) {
	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		return false;
	}

	if ( ! has_shortcode( $post->post_content, 'eazymatch' ) ) {
		return false;
	}

	if ( '' === $view ) {
		return true;
	}

	return (bool) preg_match( '/\[eazymatch[^\]]*view=["\']' . preg_quote( $view, '/' ) . '["\']/', $post->post_content );
}

/**
 * Pages holding a single job or the apply form get a wider, sidebar-free layout.
 *
 * @return bool
 */
function emr_is_job_detail_page() {
	if ( ! is_page() ) {
		return false;
	}

	$id = get_the_ID();

	return emr_page_has_eazymatch_view( $id, 'job' ) || emr_page_has_eazymatch_view( $id, 'apply' );
}

/**
 * Excerpt length.
 *
 * @param int $length Default length.
 *
 * @return int
 */
function emr_excerpt_length( $length ) {
	return is_admin() ? $length : 28;
}
add_filter( 'excerpt_length', 'emr_excerpt_length' );

/**
 * Excerpt suffix.
 *
 * @return string
 */
function emr_excerpt_more() {
	return is_admin() ? '[...]' : '&hellip;';
}
add_filter( 'excerpt_more', 'emr_excerpt_more' );

require_once EMR_DIR . '/inc/template-tags.php';
require_once EMR_DIR . '/inc/customizer.php';
require_once EMR_DIR . '/inc/setup-wizard.php';
