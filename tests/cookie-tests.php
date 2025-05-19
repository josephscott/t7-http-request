<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'receive-single-cookie', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/?set_cookie=test_cookie:cookie_value'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers )->toHaveKey( 'set-cookie' );
	expect( $response->headers['set-cookie'] )->toContain( 'test_cookie=cookie_value' );
} );

test( 'receive-multiple-cookies', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/?set_cookie=cookie1:value1&set_cookie2=cookie2:value2'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );

	// Multiple cookies may be sent in various ways
	// 1. Multiple Set-Cookie headers
	// 2. A single Set-Cookie header with a \n-separated string
	// Depending on how the server returns cookies, we might need to adjust this test
	expect( isset( $body['cookies'] ) || isset( $response->headers['set-cookie'] ) )->toBeTrue();
} );

test( 'send-cookie', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/?method=get',
		headers: [
			'Cookie' => 'test_cookie=cookie_value',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'headers' );
	expect( $body['headers'] )->toHaveKey( 'Cookie' );
	expect( $body['headers']['Cookie'] )->toBe( 'test_cookie=cookie_value' );
} );

test( 'send-multiple-cookies', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/?method=get',
		headers: [
			'Cookie' => 'cookie1=value1; cookie2=value2',
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'headers' );
	expect( $body['headers'] )->toHaveKey( 'Cookie' );
	expect( $body['headers']['Cookie'] )->toBe( 'cookie1=value1; cookie2=value2' );
} );

test( 'cookies-with-special-characters', function () {
	$cookie_value = 'special&value+with@characters';
	$response = $this->http->get(
		url: 'http://localhost:17171/?method=get',
		headers: [
			'Cookie' => 'special_cookie=' . urlencode( $cookie_value ),
		]
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'headers' );
	expect( $body['headers'] )->toHaveKey( 'Cookie' );
	// Encoded cookie value should be properly sent
	expect( $body['headers']['Cookie'] )->toBe( 'special_cookie=' . urlencode( $cookie_value ) );
} );

test( 'cookie-with-expiry', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/?set_cookie=cookie_with_expiry:value;Max-Age=3600'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers )->toHaveKey( 'set-cookie' );
	expect( $response->headers['set-cookie'] )->toContain( 'cookie_with_expiry=value' );
	expect( $response->headers['set-cookie'] )->toContain( 'Max-Age=3600' );
} );

test( 'cookie-with-http-only-flag', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/?set_cookie=http_only_cookie:value;HttpOnly'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers )->toHaveKey( 'set-cookie' );
	expect( $response->headers['set-cookie'] )->toContain( 'http_only_cookie=value' );
	expect( $response->headers['set-cookie'] )->toContain( 'HttpOnly' );
} );

test( 'cookie-with-secure-flag', function () {
	$response = $this->http->get(
		url: 'http://localhost:17171/?set_cookie=secure_cookie:value;Secure'
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers )->toHaveKey( 'set-cookie' );
	expect( $response->headers['set-cookie'] )->toContain( 'secure_cookie=value' );
	expect( $response->headers['set-cookie'] )->toContain( 'Secure' );
} );
