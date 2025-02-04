<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'get-curl', function () {
	$response = $this->http->get( url: 'http://localhost:17171/' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'application/json' );
} );

test( 'get-404-response', function () {
	$response = $this->http->get( url: 'http://localhost:17171/not-found' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );

test( 'get-with-query-parameters', function () {
	$response = $this->http->get(
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
	$response = $this->http->get(
		url: 'http://localhost:17171/headers',
		headers: ['X-Custom-Header' => 'test-value']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->body )->toContain( 'X-Custom-Header' );
} );

test( 'get-invalid-url', function () {
	$response = $this->http->get( url: 'http://invalid-domain-that-does-not-exist.test' );

	expect( $response->error )->toBe( true );
	expect( $response->code )->toBe( 0 );
} );

test( 'get-timeout', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/delay',
		options: ['timeout' => 1]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );

test( 'get-redirect-handling', function () {
	$response = $this->http->get( url: 'http://localhost:17171/redirect' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 302 );
	expect( $response->headers['location'] )->not->toBe( 'http://localhost:17171/redirect' );
} );

test( 'get-with-compression', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/compressed',
		headers: ['Accept-Encoding' => 'gzip, deflate']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-encoding'] ?? '' )->toContain( 'gzip' );
} );

test( 'get-large-response', function () {
	$response = $this->http->get( url: 'http://localhost:17171/large' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( strlen( $response->body ) )->toBeGreaterThan( 1024 * 1024 ); // At least 1MB
} );

test( 'get-with-connection-options', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/',
		options: [
			'connect_timeout' => 5,
			'max_redirects' => 3,
			'verify_ssl' => true,
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );

test( 'get-with-basic-auth', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/auth',
		headers: [
			'Authorization' => 'Basic ' . base64_encode( 'user:pass' ),
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );
