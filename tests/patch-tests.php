<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'patch-basic', function () {
	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=patch',
		data: ['name' => 'test_value']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'test_value' );
} );

test( 'patch-json-patch', function () {
	$patch_operations = [
		[
			'op' => 'replace',
			'path' => '/name',
			'value' => 'new_value',
		],
	];

	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=patch',
		data: json_encode( $patch_operations ),
		headers: ['Content-Type' => 'application/json-patch+json']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body['headers']['Content-Type'] )->toBe( 'application/json-patch+json' );
} );

test( 'patch-merge-patch', function () {
	$merge_patch = [
		'name' => 'updated_name',
		'settings' => [
			'enabled' => true,
		],
	];

	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=patch',
		data: json_encode( $merge_patch ),
		headers: ['Content-Type' => 'application/merge-patch+json']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body['headers']['Content-Type'] )->toBe( 'application/merge-patch+json' );
} );

test( 'patch-with-query-parameters', function () {
	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=patch&resource_id=123',
		data: ['name' => 'test_value']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'get' );
	expect( $body['get']['resource_id'] )->toBe( '123' );
} );

test( 'patch-with-custom-headers', function () {
	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=patch',
		data: ['name' => 'test'],
		headers: [
			'X-Custom-Header' => 'test-value',
			'User-Agent' => 'T7-HTTP-Test',
			'If-Match' => '"123456789"',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'headers' );
	expect( $body['headers']['X-Custom-Header'] )->toBe( 'test-value' );
	expect( $body['headers']['User-Agent'] )->toBe( 'T7-HTTP-Test' );
	expect( $body['headers']['If-Match'] )->toBe( '"123456789"' );
} );

test( 'patch-with-status-code', function () {
	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=patch&status=204',
		data: ['name' => 'test']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 204 );
} );

test( 'patch-method-not-allowed', function () {
	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=get',
		data: ['name' => 'test']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 405 );
} );

test( 'patch-with-delay', function () {
	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=patch&sleep=1',
		data: ['name' => 'test']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'sleep' );
	expect( $body['sleep'] )->toBe( 1 );
} );

test( 'patch-invalid-url', function () {
	$response = $this->http->patch(
		url: 'http://invalid-domain-that-does-not-exist.test',
		data: ['name' => 'test']
	);

	expect( $response->error )->toBe( true );
	expect( $response->code )->toBe( 0 );
} );

test( 'patch-with-basic-auth', function () {
	$response = $this->http->patch(
		url: 'http://localhost:17171/auth',
		data: ['name' => 'test'],
		headers: [
			'Authorization' => 'Basic ' . base64_encode( 'user:pass' ),
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );

test( 'patch-precondition-required', function () {
	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=patch&status=428',
		data: ['name' => 'test']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 428 );
} );

test( 'patch-precondition-failed', function () {
	$response = $this->http->patch(
		url: 'http://localhost:17171/?method=patch&status=412',
		data: ['name' => 'test'],
		headers: [
			'If-Match' => '"invalid-etag"',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 412 );
} );
