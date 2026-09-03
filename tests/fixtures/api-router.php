<?php

if ( 'cli-server' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

$stateFile = getenv( 'EMOL_TEST_STATE_FILE' );
$requests  = array();

if ( $stateFile && file_exists( $stateFile ) ) {
	$requests = json_decode( file_get_contents( $stateFile ), true );
	if ( ! is_array( $requests ) ) {
		$requests = array();
	}
}

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$body = file_get_contents( 'php://input' );
$fields = array();
parse_str( $body, $fields );

$requests[] = array(
	'path'   => $path,
	'body'   => $body,
	'fields' => $fields,
);

if ( $stateFile ) {
	file_put_contents( $stateFile, json_encode( $requests ) );
}

header( 'Content-Type: application/json' );

if ( '/v1/session/getToken.json' === $path ) {
	echo json_encode(
		array(
			'success' => true,
			'result'  => 'fresh-token',
		)
	);
	return;
}

if ( '/v1/job/recover.json' === $path ) {
	$recoveryAttempts = 0;
	foreach ( $requests as $request ) {
		if ( '/v1/job/recover.json' === $request['path'] ) {
			$recoveryAttempts++;
		}
	}

	if ( 1 === $recoveryAttempts ) {
		echo json_encode( array( 'status' => 'error' ) );
		return;
	}

	echo json_encode(
		array(
			'success' => true,
			'result'  => array(
				'recovered' => true,
				'key'       => isset( $fields['key'] ) ? $fields['key'] : null,
			),
		)
	);
	return;
}

if ( '/v1/job/emptyResult.json' === $path ) {
	echo json_encode( array( 'success' => true ) );
	return;
}

if ( '/v1/tool/trunk.json' === $path ) {
	$calls = isset( $fields['argument'][0] )
		? json_decode( $fields['argument'][0], true )
		: array();
	$responses = array();

	foreach ( $calls as $call ) {
		$responses[] = array(
			'class'     => $call['class'],
			'method'    => $call['method'],
			'arguments' => $call['arguments'],
		);
	}

	echo json_encode(
		array(
			'success' => true,
			'result'  => $responses,
		)
	);
	return;
}

$arguments = array();
if ( isset( $fields['argument'] ) && is_array( $fields['argument'] ) ) {
	foreach ( $fields['argument'] as $argument ) {
		$arguments[] = json_decode( $argument, true );
	}
}

echo json_encode(
	array(
		'success' => true,
		'result'  => array(
			'path'      => $path,
			'instance'  => isset( $fields['instance'] ) ? $fields['instance'] : null,
			'key'       => isset( $fields['key'] ) ? $fields['key'] : null,
			'arguments' => $arguments,
		),
	)
);
