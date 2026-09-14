<?php

if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

// add the update checker


/*
global $MyUpdateChecker;
$MyUpdateChecker = new emol_updater( 'https://wordpress.eazymatch.cloud/info.json',
	EMOL_DIR . '/eazymatch.php'
);
*/

add_action( 'admin_menu', 'eazymatch_admin_menu' );

/**
 * Add the eazyMatch Admin menu
 *
 */
function eazymatch_admin_menu() {
	add_menu_page(
		'EazyMatch',
		EMOL_ADMIN_GLOBAL,
		'manage_options',
		'emol-admin',
		'eazymatch_plugin_options',
		plugins_url( 'assets/img/eazymatch-menu-icon.png', __FILE__ )
	);

	add_submenu_page( 'emol-admin', EMOL_ADMIN_JOB, EMOL_ADMIN_JOB, 'manage_options', 'emol-job', 'eazymatch_plugin_job' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_MANAGER, EMOL_ADMIN_MANAGER, 'manage_options', 'emol-manager', 'eazymatch_plugin_manager' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_CV, EMOL_ADMIN_CV, 'manage_options', 'emol-cv', 'eazymatch_plugin_cv' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_FORM, EMOL_ADMIN_FORM, 'manage_options', 'emol-forms', 'eazymatch_plugin_form' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_FORM_AVG, EMOL_ADMIN_FORM_AVG, 'manage_options', 'emol-avg', 'eazymatch_plugin_avg' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_ACC_FORM, EMOL_ADMIN_ACC_FORM, 'manage_options', 'emol-applicant-account', 'eazymatch_formmanager_applicant_account' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_STYLESHEET, EMOL_ADMIN_STYLESHEET, 'manage_options', 'emol-stylesheet', 'eazymatch_plugin_stylesheet' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_SHARING, EMOL_ADMIN_SHARING, 'manage_options', 'emol-sharing', 'eazymatch_plugin_sharing' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_SHORTCODES, EMOL_ADMIN_SHORTCODES, 'manage_options', 'emol-shortcodes', 'eazymatch_plugin_shortcodes' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_THEME, EMOL_ADMIN_THEME, 'manage_options', 'emol-theme', 'eazymatch_plugin_theme' );

	// add_submenu_page( 'emol-admin', EMOL_ADMIN_SHARING, EMOL_ADMIN_SHARING, 'manage_options', 'emol-sharing', 'eazymatch_plugin_sharing');
	// add_submenu_page( 'emol-admin', EMOL_ADMIN_ACCOUNT, EMOL_ADMIN_ACCOUNT, 'manage_options', 'emol-cv', 'eazymatch_plugin_account');
}

/**
 * Load the EazyMatch admin stylesheet on the plugin's own screens only.
 *
 * Presentation only: this restyles the existing markup of every EazyMatch menu
 * item into one consistent look, without changing any field or saved value.
 *
 * @param string $hook Current admin page hook suffix.
 */
function eazymatch_admin_assets( $hook ) {
	if ( ! eazymatch_is_admin_screen() ) {
		return;
	}

	$css_path = EMOL_DIR . '/assets/css/admin.css';
	$version  = file_exists( $css_path ) ? filemtime( $css_path ) : EMOL_VERSION;

	wp_enqueue_style( 'emol-admin-ui', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), $version );
	wp_enqueue_style( 'dashicons' );

	if ( isset( $_GET['page'] ) && 'emol-theme' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}
}

add_action( 'admin_enqueue_scripts', 'eazymatch_admin_assets' );

/**
 * Size the sidebar icon to the WordPress 20×20 menu slot on every admin screen.
 */
function eazymatch_admin_menu_icon_css() {
	wp_add_inline_style(
		'common',
		'#adminmenu #toplevel_page_emol-admin .wp-menu-image img{padding:7px 0 0;width:20px;height:20px;max-width:20px;max-height:20px;object-fit:contain;}'
	);
}

add_action( 'admin_enqueue_scripts', 'eazymatch_admin_menu_icon_css' );

/**
 * Add a body class on the EazyMatch admin screens so the stylesheet can scope
 * itself and win from the inline styles some screens still print.
 *
 * @param string $classes Space separated body classes.
 *
 * @return string
 */
function eazymatch_admin_body_class( $classes ) {
	if ( eazymatch_is_admin_screen() ) {
		$classes .= ' emol-admin-page';
	}

	return $classes;
}

add_filter( 'admin_body_class', 'eazymatch_admin_body_class' );

/**
 * Are we on one of the EazyMatch admin screens?
 *
 * @return bool
 */
function eazymatch_is_admin_screen() {
	if ( ! isset( $_GET['page'] ) ) {
		return false;
	}

	return strpos( sanitize_key( wp_unslash( $_GET['page'] ) ), 'emol-' ) === 0;
}

/**
 * include admin javascript
 */
emol_require::admin();

/**
 * Handle all vars that are configurable
 *
 */
include( EMOL_DIR . '/admin/forminstance.php' );
include( EMOL_DIR . '/admin/job.php' );
include( EMOL_DIR . '/admin/managers.php' );
include( EMOL_DIR . '/admin/cv.php' );
include( EMOL_DIR . '/admin/global.php' );
include( EMOL_DIR . '/admin/forms.php' );
include( EMOL_DIR . '/admin/avg.php' );
include( EMOL_DIR . '/admin/stylesheet.php' );
include( EMOL_DIR . '/admin/sharing.php' );
include( EMOL_DIR . '/admin/applicant-account.php' );
include( EMOL_DIR . '/admin/shortcodes.php' );
include( EMOL_DIR . '/admin/theme-download.php' );
include( EMOL_DIR . '/admin/theme.php' );
?>
