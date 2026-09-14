<?php

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

$GLOBALS['emol_test_actions'] = array();
$GLOBALS['emol_test_options'] = array(
	'emol_sharing_links'  => 1,
	'emol_jquery_ui_skin' => 'smoothness',
);

function is_admin() {
	return false;
}

function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['emol_test_options'] )
		? $GLOBALS['emol_test_options'][ $name ]
		: $default;
}

function add_action( $hook, $callback ) {
	$GLOBALS['emol_test_actions'][] = array( $hook, $callback );
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

require_once EMOL_DIR . '/lib/emol/require.php';

emol_require::all();
$firstRegistration = $GLOBALS['emol_test_actions'];

emol_require::all();
emol_require::all();

if ( $firstRegistration !== $GLOBALS['emol_test_actions'] ) {
	throw new RuntimeException( 'Repeated shortcodes registered duplicate frontend asset callbacks.' );
}

$expectedCallbacks = array(
	'load_emol_css_basic',
	'load_emol_js_jquery',
	'load_emol_js_basic',
	'load_emol_js_jqueryui',
);
$actualCallbacks = array_column( $GLOBALS['emol_test_actions'], 1 );

if ( $expectedCallbacks !== $actualCallbacks ) {
	throw new RuntimeException(
		"The registered frontend assets changed.\nExpected: "
		. var_export( $expectedCallbacks, true )
		. "\nActual: "
		. var_export( $actualCallbacks, true )
	);
}

echo "Asset registration regression checks passed.\n";
