<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'options-basic', function () {
	$response = $this->http->options(
		url: 'http://localhost:17171/?method=options'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers )->toHaveKey( 'allow' );
	expect( $response->headers['allow'] )->toContain( 'GET' );
	expect( $response->headers['allow'] )->toContain( 'POST' );
	expect( $response->headers['allow'] )->toContain( 'PUT' );
	expect( $response->headers['allow'] )->toContain( 'DELETE' );
	expect( $response->headers['allow'] )->toContain( 'OPTIONS' );
} );

test( 'options-with-cors-headers', function () {
	$response = $this->http->options(
		url: 'http://localhost:17171/?method=options',
		headers: [
			'Origin' => 'http://example.com',
			'Access-Control-Request-Method' => 'POST',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers )->toHaveKey( 'access-control-allow-origin' );
	expect( $response->headers )->toHaveKey( 'access-control-allow-methods' );
	expect( $response->headers )->toHaveKey( 'access-control-allow-headers' );
} );

test( 'options-with-custom-headers', function () {
	$response = $this->http->options(
		url: 'http://localhost:17171/?method=options',
		headers: [
			'X-Custom-Header' => 'test-value',
			'User-Agent' => 'T7-HTTP-Test',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'headers' );
	expect( $body['headers']['X-Custom-Header'] )->toBe( 'test-value' );
	expect( $body['headers']['User-Agent'] )->toBe( 'T7-HTTP-Test' );
} );

test( 'options-with-auth', function () {
	$response = $this->http->options(
		url: 'http://localhost:17171/auth',
		headers: [
			'Authorization' => 'Basic ' . base64_encode( 'user:pass' ),
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers )->toHaveKey( 'allow' );
	expect( $response->headers['allow'] )->toContain( 'GET' );
} );

test( 'options-method-not-allowed', function () {
	$response = $this->http->options(
		url: 'http://localhost:17171/?method=get'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 405 );
} );

test( 'options-with-delay', function () {
	$response = $this->http->options(
		url: 'http://localhost:17171/?method=options&sleep=1'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'sleep' );
	expect( $body['sleep'] )->toBe( 1 );
} );

test( 'options-invalid-url', function () {
	$response = $this->http->options(
		url: 'http://invalid-domain-that-does-not-exist.test'
	);

	expect( $response->error )->toBe( true );
	expect( $response->code )->toBe( 0 );
} );

test( 'options-with-access-control-request-headers', function () {
	$response = $this->http->options(
		url: 'http://localhost:17171/?method=options',
		headers: [
			'Origin' => 'http://example.com',
			'Access-Control-Request-Headers' => 'X-Custom-Header, Content-Type',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers )->toHaveKey( 'access-control-allow-headers' );
	expect( $response->headers['access-control-allow-headers'] )->toContain( 'X-Custom-Header' );
	expect( $response->headers['access-control-allow-headers'] )->toContain( 'Content-Type' );
} );

test( 'options-with-max-age', function () {
	$response = $this->http->options(
		url: 'http://localhost:17171/?method=options',
		headers: [
			'Origin' => 'http://example.com',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers )->toHaveKey( 'access-control-max-age' );
} );
