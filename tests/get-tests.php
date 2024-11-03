<?php
declare( strict_types = 1 );

test( 'get-curl', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get( url: 'http://localhost:17171/' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );
