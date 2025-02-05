<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'post-basic', function () {
	$response = $this->http->post(
		url: 'http://localhost:17171/?method=post',
		data: ['name' => 'test_value']
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'test_value' );
} );
