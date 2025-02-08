<?php
declare( strict_types = 1 );
$out = [];

// Parse PUT data
if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ) {
	parse_str( file_get_contents( 'php://input' ), $_POST );
}

// Special endpoints that don't require method parameter check
$special_endpoints = ['/auth', '/redirect', '/compressed', '/large'];
$is_special_endpoint = in_array( $_SERVER['REQUEST_URI'], $special_endpoints );

// Handle basic auth
if ( $_SERVER['REQUEST_URI'] === '/auth' ) {
	if ( ! isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
		header( 'HTTP/1.1 401 Unauthorized' );
		header( 'WWW-Authenticate: Basic realm="Test Realm"' );
		exit;
	}

	$auth_header = $_SERVER['HTTP_AUTHORIZATION'];
	$expected_auth = 'Basic ' . base64_encode( 'user:pass' );

	if ( $auth_header !== $expected_auth ) {
		header( 'HTTP/1.1 401 Unauthorized' );
		header( 'WWW-Authenticate: Basic realm="Test Realm"' );
		exit;
	}

	if ( $_SERVER['REQUEST_METHOD'] === 'HEAD' ) {
		header( 'HTTP/1.1 200 OK' );
		header( 'Content-Type: application/json' );
		exit;
	}

	// Auth successful, continue to normal processing
	$out['auth'] = 'success';
}

if ( $_SERVER['REQUEST_URI'] === '/redirect' ) {
	header( 'Location: http://localhost:17171/' );
	header( 'HTTP/1.1 302 Found' );
	exit;
}

if ( isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) && strpos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) !== false ) {
	if ( $_SERVER['REQUEST_URI'] === '/compressed' ) {
		header( 'Content-Encoding: gzip' );
		$out['compressed'] = true;
		send_body( $out, true );
		exit;
	}
}

if ( $_SERVER['REQUEST_URI'] === '/large' ) {
	$large_data = str_repeat( 'x', 2 * 1024 * 1024 ); // 2MB of data
	$out['data'] = $large_data;
	send_body( $out );
	exit;
}

// Only check method parameter for non-special endpoints
if ( ! $is_special_endpoint ) {
	$method = $_GET['method'] ?? 'get';
	$method = strtolower( $method );
	$out['method'] = $method;
	if ( strtolower( $_SERVER['REQUEST_METHOD'] ) !== $method ) {
		header( 'HTTP/1.0 405 Method Not Allowed' );
		send_body( $out );
		exit;
	}
}

$status = $_GET['status'] ?? 0;
$status = (int) $status;
$out['status'] = $status;
if ( $status > 0 ) {
	header( "HTTP/1.0 $status" );
	send_body( $out );
	exit;
}

$sleep = $_GET['sleep'] ?? 0;
$sleep = (int) $sleep;
$out['sleep'] = $sleep;
sleep( $sleep );
usleep( 100 ); // make sure we got longer than the sleep amount

foreach( apache_request_headers() as $k => $v ) {
	$out['headers'][$k] = $v;
}

foreach( $_GET as $k => $v ) {
	$out['get'][$k] = $v;
}

foreach( $_POST as $k => $v ) {
	$out['post'][$k] = $v;
}

send_body( $out );

/* *** */

function send_body( $out, $compress = false ) {
	$json = json_encode( $out, JSON_PRETTY_PRINT );
	header( 'Content-Type: application/json' );

	if ( $compress ) {
		echo gzencode( $json );
	} else {
		echo $json;
	}
}
