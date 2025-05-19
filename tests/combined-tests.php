<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'upload-file-with-cookies', function () {
	// Create a temporary file for testing
	$temp_file = tempnam( sys_get_temp_dir(), 'test_upload_' );
	file_put_contents( $temp_file, 'Test file content for upload' );

	// Use CURLFile to upload the file
	$data = [
		'file' => new \CURLFile( $temp_file, 'text/plain', 'test_file.txt' ),
		'name' => 'test_upload_with_cookie'
	];

	$response = $this->http->post(
		url: 'http://localhost:17171/?method=post',
		headers: [
			'Cookie' => 'session=test_session_id; user=test_user'
		],
		data: $data
	);

	// Clean up the temporary file
	unlink( $temp_file );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	
	// Verify file upload
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'test_upload_with_cookie' );
	expect( $body['post'] )->toHaveKey( 'file' );
	
	// Verify cookies were sent
	expect( $body )->toHaveKey( 'headers' );
	expect( $body['headers'] )->toHaveKey( 'Cookie' );
	expect( $body['headers']['Cookie'] )->toBe( 'session=test_session_id; user=test_user' );
} );

test( 'upload-file-and-receive-cookie', function () {
	// Create a temporary file for testing
	$temp_file = tempnam( sys_get_temp_dir(), 'test_upload_' );
	file_put_contents( $temp_file, 'Test file content for upload' );

	// Use CURLFile to upload the file
	$data = [
		'file' => new \CURLFile( $temp_file, 'text/plain', 'test_file.txt' ),
		'name' => 'test_upload_receive_cookie'
	];

	$response = $this->http->post(
		url: 'http://localhost:17171/?method=post&set_cookie=session_id:new_session_123',
		data: $data
	);

	// Clean up the temporary file
	unlink( $temp_file );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	
	// Verify file upload
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'test_upload_receive_cookie' );
	expect( $body['post'] )->toHaveKey( 'file' );
	
	// Verify cookie was received
	expect( $response->headers )->toHaveKey( 'set-cookie' );
	expect( $response->headers['set-cookie'] )->toContain( 'session_id=new_session_123' );
} ); 