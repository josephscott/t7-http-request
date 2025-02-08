<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'put-basic', function () {
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=put',
		data: ['name' => 'test_value']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'test_value' );
} );

test( 'put-with-query-parameters', function () {
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=put&param1=value1',
		data: ['name' => 'test_value']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'get' );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['get']['param1'] )->toBe( 'value1' );
	expect( $body['post']['name'] )->toBe( 'test_value' );
} );

test( 'put-multiple-values', function () {
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=put',
		data: [
			'name' => 'test_value',
			'number' => '42',
			'boolean' => 'true',
			'array' => ['a', 'b', 'c'],
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'test_value' );
	expect( $body['post']['number'] )->toBe( '42' );
	expect( $body['post']['boolean'] )->toBe( 'true' );
} );

test( 'put-with-special-characters', function () {
	$special_value = 'Test & Value + More @ Special ! Characters #';
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=put',
		data: ['special' => $special_value]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['special'] )->toBe( $special_value );
} );

test( 'put-with-unicode-characters', function () {
	$unicode_value = '测试 🌟 Unicode';
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=put',
		data: ['unicode' => $unicode_value]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['unicode'] )->toBe( $unicode_value );
} );

test( 'put-with-custom-headers', function () {
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=put',
		data: ['name' => 'test'],
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

test( 'put-empty-data', function () {
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=put',
		data: []
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body['get']['method'] )->toBe( 'put' );
} );

test( 'put-with-status-code', function () {
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=put&status=201',
		data: ['name' => 'test']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 201 );
} );

test( 'put-method-not-allowed', function () {
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=get',
		data: ['name' => 'test']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 405 );
} );

test( 'put-with-delay', function () {
	$response = $this->http->put(
		url: 'http://localhost:17171/?method=put&sleep=1',
		data: ['name' => 'test']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'sleep' );
	expect( $body['sleep'] )->toBe( 1 );
} );

test( 'put-invalid-url', function () {
	$response = $this->http->put(
		url: 'http://invalid-domain-that-does-not-exist.test',
		data: ['name' => 'test']
	);

	expect( $response->error )->toBe( true );
	expect( $response->code )->toBe( 0 );
} );

test( 'put-with-basic-auth', function () {
	$response = $this->http->put(
		url: 'http://localhost:17171/auth',
		data: ['name' => 'test'],
		headers: [
			'Authorization' => 'Basic ' . base64_encode( 'user:pass' ),
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );
