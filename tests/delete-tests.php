<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'delete-basic', function () {
	$response = $this->http->delete(
		url: 'http://localhost:17171/?method=delete'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body['method'] )->toBe( 'delete' );
} );

test( 'delete-with-query-parameters', function () {
	$response = $this->http->delete(
		url: 'http://localhost:17171/?method=delete&resource_id=123'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'get' );
	expect( $body['get']['resource_id'] )->toBe( '123' );
} );

test( 'delete-with-custom-headers', function () {
	$response = $this->http->delete(
		url: 'http://localhost:17171/?method=delete',
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

test( 'delete-with-status-code', function () {
	$response = $this->http->delete(
		url: 'http://localhost:17171/?method=delete&status=204'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 204 );
} );

test( 'delete-method-not-allowed', function () {
	$response = $this->http->delete(
		url: 'http://localhost:17171/?method=get'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 405 );
} );

test( 'delete-with-delay', function () {
	$response = $this->http->delete(
		url: 'http://localhost:17171/?method=delete&sleep=1'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'sleep' );
	expect( $body['sleep'] )->toBe( 1 );
} );

test( 'delete-invalid-url', function () {
	$response = $this->http->delete(
		url: 'http://invalid-domain-that-does-not-exist.test'
	);

	expect( $response->error )->toBe( true );
	expect( $response->code )->toBe( 0 );
} );

test( 'delete-with-basic-auth', function () {
	$response = $this->http->delete(
		url: 'http://localhost:17171/auth',
		headers: [
			'Authorization' => 'Basic ' . base64_encode( 'user:pass' ),
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );

test( 'delete-nonexistent-resource', function () {
	$response = $this->http->delete(
		url: 'http://localhost:17171/?method=delete&status=404'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 404 );
} );

test( 'delete-with-if-match', function () {
	$response = $this->http->delete(
		url: 'http://localhost:17171/?method=delete',
		headers: [
			'If-Match' => '"123456789"',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'headers' );
	expect( $body['headers']['If-Match'] )->toBe( '"123456789"' );
} );
