<?php
declare( strict_types = 1 );

test( 'get-curl', function () {
	$http = new \T7\HTTP\Client();
	$response = $http->get( url: 'http://localhost:17171/' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );
