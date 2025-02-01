<?php
declare( strict_types = 1 );

test( 'get-curl', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get( url: 'http://localhost:17171/' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'application/json' );
} );

test( 'get-404-response', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get( url: 'http://localhost:17171/not-found' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );

test( 'get-with-query-parameters', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get(
		url: 'http://localhost:17171/echo?name=test&value=123'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'get' );
	expect( $body['get']['name'] )->toBe( 'test' );
	expect( $body['get']['value'] )->toBe( '123' );
} );

test( 'get-with-custom-headers', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get(
		url: 'http://localhost:17171/headers',
		headers: ['X-Custom-Header' => 'test-value']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->body )->toContain( 'X-Custom-Header' );
} );

test( 'get-invalid-url', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get( url: 'http://invalid-domain-that-does-not-exist.test' );

	expect( $response->error )->toBe( true );
	expect( $response->code )->toBe( 0 );
} );

test( 'get-timeout', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get(
		url: 'http://localhost:17171/delay',
		options: ['timeout' => 1]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );
