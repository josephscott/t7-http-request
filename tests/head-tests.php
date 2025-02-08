<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'head-basic', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/?method=head'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'application/json' );
	expect( $response->body )->toBe( '' );
} );

test( 'head-with-query-parameters', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/?method=head&param1=value1'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'application/json' );
	expect( $response->body )->toBe( '' );
} );

test( 'head-with-custom-headers', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/?method=head',
		headers: [
			'X-Custom-Header' => 'test-value',
			'User-Agent' => 'T7-HTTP-Test',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'application/json' );
	expect( $response->body )->toBe( '' );
} );

test( 'head-with-status-code', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/?method=head&status=201'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 201 );
	expect( $response->body )->toBe( '' );
} );

test( 'head-method-not-allowed', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/?method=post'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 405 );
	expect( $response->body )->toBe( '' );
} );

test( 'head-with-delay', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/?method=head&sleep=1'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->body )->toBe( '' );
} );

test( 'head-invalid-url', function () {
	$response = $this->http->head(
		url: 'http://invalid-domain-that-does-not-exist.test'
	);

	expect( $response->error )->toBe( true );
	expect( $response->code )->toBe( 0 );
	expect( $response->body )->toBe( '' );
} );

test( 'head-redirect', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/redirect'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 302 );
	expect( $response->headers['location'] )->toBe( 'http://localhost:17171/' );
	expect( $response->body )->toBe( '' );
} );

test( 'head-with-basic-auth', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/auth',
		headers: [
			'Authorization' => 'Basic ' . base64_encode( 'user:pass' ),
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->body )->toBe( '' );
} );

test( 'head-with-compression', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/compressed',
		headers: ['Accept-Encoding' => 'gzip, deflate']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-encoding'] ?? '' )->toContain( 'gzip' );
	expect( $response->body )->toBe( '' );
} );

test( 'head-large-response', function () {
	$response = $this->http->head(
		url: 'http://localhost:17171/large'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'application/json' );
	expect( $response->body )->toBe( '' );
} );
