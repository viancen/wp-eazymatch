<?php

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

if ( ! extension_loaded( 'curl' ) ) {
	fwrite( STDERR, "The cURL extension is required for this test.\n" );
	exit( 1 );
}

function emol_test_assert_same( $expected, $actual, $message ) {
	if ( $expected === $actual ) {
		return;
	}

	throw new RuntimeException(
		$message . "\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true )
	);
}

function emol_test_find_port() {
	$socket = stream_socket_server( 'tcp://127.0.0.1:0', $errorNumber, $errorMessage );
	if ( false === $socket ) {
		throw new RuntimeException( 'Unable to reserve a test port: ' . $errorMessage, $errorNumber );
	}

	$address = stream_socket_get_name( $socket, false );
	fclose( $socket );

	return (int) substr( strrchr( $address, ':' ), 1 );
}

function emol_test_wait_for_server( $port ) {
	for ( $attempt = 0; $attempt < 100; $attempt++ ) {
		$socket = @fsockopen( '127.0.0.1', $port );
		if ( false !== $socket ) {
			fclose( $socket );
			return;
		}

		usleep( 20000 );
	}

	throw new RuntimeException( 'The local API test server did not start.' );
}

$testRoot   = dirname( __DIR__ );
$stateFile  = tempnam( sys_get_temp_dir(), 'emol-api-state-' );
$serverLog  = tempnam( sys_get_temp_dir(), 'emol-api-log-' );
$serverPort = emol_test_find_port();
$serverEnv  = array_merge( $_ENV, array( 'EMOL_TEST_STATE_FILE' => $stateFile ) );
$server     = proc_open(
	array( PHP_BINARY, '-S', '127.0.0.1:' . $serverPort, 'tests/fixtures/api-router.php' ),
	array(
		0 => array( 'pipe', 'r' ),
		1 => array( 'file', $serverLog, 'a' ),
		2 => array( 'file', $serverLog, 'a' ),
	),
	$pipes,
	$testRoot,
	$serverEnv
);

if ( ! is_resource( $server ) ) {
	throw new RuntimeException( 'Unable to start the local API test server.' );
}

fclose( $pipes[0] );

register_shutdown_function(
	function () use ( $server, $stateFile, $serverLog ) {
		proc_terminate( $server );
		proc_close( $server );
		@unlink( $stateFile );
		@unlink( $serverLog );
	}
);

emol_test_wait_for_server( $serverPort );

$GLOBALS['emol_test_options'] = array(
	'emol_service_url' => 'http://127.0.0.1:' . $serverPort,
	'emol_instance'    => 'client instance',
	'emol_key'         => 'client-key',
	'emol_secret'      => 'client-secret',
	'emol_apihash'     => 'initial-api-key',
);
$GLOBALS['emol_Core']    = $GLOBALS['emol_test_options']['emol_service_url'];
$GLOBALS['emol_isDebug'] = false;

function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['emol_test_options'] )
		? $GLOBALS['emol_test_options'][ $name ]
		: $default;
}

function update_option( $name, $value ) {
	$GLOBALS['emol_test_options'][ $name ] = $value;
	return true;
}

function delete_option( $name ) {
	unset( $GLOBALS['emol_test_options'][ $name ] );
	return true;
}

function is_admin() {
	return false;
}

function eazymatch_trow_error( $message = '' ) {
	throw new RuntimeException( $message );
}

class emol_session {
	private static $values = array();

	public static function exists( $name ) {
		return array_key_exists( $name, self::$values );
	}

	public static function get( $name ) {
		return self::exists( $name ) ? self::$values[ $name ] : '';
	}

	public static function set( $name, $value = '' ) {
		self::$values[ $name ] = $value;
	}

	public static function remove( $name ) {
		unset( self::$values[ $name ] );
	}
}

if ( ! defined( 'EMOL_DIR' ) ) {
	define( 'EMOL_DIR', $testRoot );
}

require_once EMOL_DIR . '/lib/emol/connectproxy/rest.php';
require_once EMOL_DIR . '/lib/emol/connectproxy/json.php';
require_once EMOL_DIR . '/lib/emol/connectManager.php';

function eazymatch_connect() {
	return emol_connectManager::getInstance()->getConnection();
}

$connection = eazymatch_connect();
emol_test_assert_same( $connection, eazymatch_connect(), 'The connection must be reused within one request.' );

$jobService = $connection->get( 'job' );
emol_test_assert_same( $jobService, $connection->get( 'job' ), 'A service proxy must be created only once.' );

$lookup = $jobService->lookup( array( 'active' ), 5 );
emol_test_assert_same(
	array(
		'path'      => '/v1/job/lookup.json',
		'instance'  => 'client+instance',
		'key'       => 'initial-api-key',
		'arguments' => array( array( 'active' ), 5 ),
	),
	$lookup,
	'The API response must be decoded without changing its values.'
);

$curlHandleProperty = new ReflectionProperty( emol_connectproxy_rest::class, 'curlHandle' );
$curlHandleProperty->setAccessible( true );
$curlHandle = $curlHandleProperty->getValue();

emol_test_assert_same( null, $jobService->emptyResult(), 'A successful response without a result must return null.' );
emol_test_assert_same(
	$curlHandle,
	$curlHandleProperty->getValue(),
	'Consecutive calls to one service must reuse the same cURL handle.'
);

$recovered = $jobService->recover( 'payload' );
$freshKey  = hash( 'sha256', 'fresh-token' . 'client-secret' );
emol_test_assert_same(
	array(
		'recovered' => true,
		'key'       => $freshKey,
	),
	$recovered,
	'An expired API session must retry with the refreshed key.'
);
emol_test_assert_same( $freshKey, get_option( 'emol_apihash' ), 'The refreshed application key must be persisted.' );

$trunk = new emol_trunk();
$first = &$trunk->request( 'job', 'first', array( 1 ) );
$second = &$trunk->request( 'company', 'second', array( 'value' ) );
$trunk->execute();

emol_test_assert_same(
	$curlHandle,
	$curlHandleProperty->getValue(),
	'Different API services must share the same cURL handle.'
);

emol_test_assert_same(
	array( 'class' => 'job', 'method' => 'first', 'arguments' => array( 1 ) ),
	$first,
	'Trunk responses must keep the request order and update returned references.'
);
emol_test_assert_same(
	array( 'class' => 'company', 'method' => 'second', 'arguments' => array( 'value' ) ),
	$second,
	'Trunk responses must keep every original argument unchanged.'
);

$requests = json_decode( file_get_contents( $stateFile ), true );
emol_test_assert_same(
	'instance=client%2Binstance&key=initial-api-key&argument%5B0%5D=%5B%22active%22%5D&argument%5B1%5D=5',
	$requests[0]['body'],
	'The raw API request payload must remain byte-for-byte compatible.'
);

$recoveryRequests = array_values(
	array_filter(
		$requests,
		function ( $request ) {
			return '/v1/job/recover.json' === $request['path'];
		}
	)
);
emol_test_assert_same( 2, count( $recoveryRequests ), 'A failed API session must perform exactly one successful retry.' );
emol_test_assert_same( 'initial-api-key', $recoveryRequests[0]['fields']['key'], 'The first request must use the existing key.' );
emol_test_assert_same( $freshKey, $recoveryRequests[1]['fields']['key'], 'The retry must use the refreshed key.' );

echo "API connection regression checks passed.\n";
