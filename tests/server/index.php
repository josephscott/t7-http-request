<?php
declare( strict_types = 1 );
$out = [];

$method = $_GET['method'] ?? 'get';
$method = strtolower( $method );
$out['method'] = $method;
if ( strtolower( $_SERVER['REQUEST_METHOD'] ) !== $method ) {
	header( 'HTTP/1.0 405 Method Not Allowed' );
	send_body( $out );
	exit;
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

function send_body( $out ) {
	$json = json_encode( $out, JSON_PRETTY_PRINT );
	header( 'Content-Type: application/json' );
	echo $json;
}
