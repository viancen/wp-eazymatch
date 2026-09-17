<?php

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

$GLOBALS['emol_test_actions'] = array();
$GLOBALS['emol_test_options'] = array(
	'emol_sharing_links'  => 1,
	'emol_jquery_ui_skin' => '',
	'emol_form_theme'     => 'native',
	'emol_theme_brand_color' => '#1f5eff',
);

function is_admin() {
	return false;
}

function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['emol_test_options'] )
		? $GLOBALS['emol_test_options'][ $name ]
		: $default;
}

function add_action( $hook, $callback, $priority = 10 ) {
	$GLOBALS['emol_test_actions'][] = array( $hook, $callback, $priority );
}

function add_filter( $hook, $callback ) {
	$GLOBALS['emol_test_filters'][] = array( $hook, $callback );
}

function wp_upload_dir() {
	return array(
		'basedir' => sys_get_temp_dir() . '/emol-missing-stylesheet',
		'baseurl' => 'https://example.test/uploads',
	);
}

if ( ! defined( 'EMOL_DIR' ) ) {
	define( 'EMOL_DIR', dirname( __DIR__ ) );
}

require_once EMOL_DIR . '/lib/emol/form/theme.php';
require_once EMOL_DIR . '/lib/emol/require.php';

if ( emol_form_theme::sanitize( 'harbor' ) !== 'harbor' ) {
	throw new RuntimeException( 'Valid form themes must be kept.' );
}

if ( emol_form_theme::sanitize( '../smoothness' ) !== emol_form_theme::JQUERY ) {
	throw new RuntimeException( 'Unknown form themes must fall back to jQuery UI.' );
}

if ( ! emol_form_theme::isPluginTheme( 'native' ) || emol_form_theme::isPluginTheme( 'jquery' ) ) {
	throw new RuntimeException( 'Plugin theme detection is wrong.' );
}

if ( emol_form_theme::inlineCss() !== 'body.emol-form-theme{--emol-theme-brand:#1f5eff;}' ) {
	throw new RuntimeException( 'Brand colour must be exposed as a CSS custom property.' );
}

$classes = emol_form_theme::bodyClass( array( 'page' ) );
if ( $classes !== array( 'page', 'emol-form-theme', 'emol-form-theme-native' ) ) {
	throw new RuntimeException( 'Body classes for plugin form themes are wrong: ' . var_export( $classes, true ) );
}

emol_require::all();
$firstRegistration = $GLOBALS['emol_test_actions'];

emol_require::all();
emol_require::all();

if ( $firstRegistration !== $GLOBALS['emol_test_actions'] ) {
	throw new RuntimeException( 'Repeated form-theme registration added duplicate frontend asset callbacks.' );
}

$expectedCallbacks = array(
	'load_emol_css_basic',
	'load_emol_js_jquery',
	'load_emol_js_basic',
	'load_emol_js_jqueryui',
	'load_emol_css_form_theme',
);
$actualCallbacks = array_column( $GLOBALS['emol_test_actions'], 1 );

if ( $expectedCallbacks !== $actualCallbacks ) {
	throw new RuntimeException(
		"The registered frontend assets for a plugin form theme changed.\nExpected: "
		. var_export( $expectedCallbacks, true )
		. "\nActual: "
		. var_export( $actualCallbacks, true )
	);
}

echo "Form theme registration regression checks passed.\n";
