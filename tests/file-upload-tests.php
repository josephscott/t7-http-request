<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'upload-single-file', function () {
	// Create a temporary file for testing
	$temp_file = tempnam( sys_get_temp_dir(), 'test_upload_' );
	file_put_contents( $temp_file, 'Test file content for upload' );

	// Use CURLFile to upload the file
	$data = [
		'file' => new \CURLFile( $temp_file, 'text/plain', 'test_file.txt' ),
		'name' => 'test_upload',
	];

	$response = $this->http->post(
		url: 'http://localhost:17171/?method=post',
		data: $data
	);

	// Clean up the temporary file
	unlink( $temp_file );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'test_upload' );
	// Server should receive the file
	expect( $body['post'] )->toHaveKey( 'file' );
} );

test( 'upload-multiple-files', function () {
	// Create temporary files for testing
	$temp_file1 = tempnam( sys_get_temp_dir(), 'test_upload1_' );
	$temp_file2 = tempnam( sys_get_temp_dir(), 'test_upload2_' );
	file_put_contents( $temp_file1, 'Test file 1 content' );
	file_put_contents( $temp_file2, 'Test file 2 content' );

	// Use CURLFile to upload multiple files
	$data = [
		'file1' => new \CURLFile( $temp_file1, 'text/plain', 'test_file1.txt' ),
		'file2' => new \CURLFile( $temp_file2, 'text/plain', 'test_file2.txt' ),
		'name' => 'multiple_files_test',
	];

	$response = $this->http->post(
		url: 'http://localhost:17171/?method=post',
		data: $data
	);

	// Clean up the temporary files
	unlink( $temp_file1 );
	unlink( $temp_file2 );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'multiple_files_test' );
	// Server should receive both files
	expect( $body['post'] )->toHaveKey( 'file1' );
	expect( $body['post'] )->toHaveKey( 'file2' );
} );

test( 'upload-file-with-special-characters-in-filename', function () {
	// Create a temporary file for testing
	$temp_file = tempnam( sys_get_temp_dir(), 'test_upload_' );
	file_put_contents( $temp_file, 'Test file content' );

	// Use a filename with special characters
	$data = [
		'file' => new \CURLFile( $temp_file, 'text/plain', 'test file & special-chars.txt' ),
		'name' => 'special_filename_test',
	];

	$response = $this->http->post(
		url: 'http://localhost:17171/?method=post',
		data: $data
	);

	// Clean up the temporary file
	unlink( $temp_file );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'special_filename_test' );
	expect( $body['post'] )->toHaveKey( 'file' );
} );

test( 'upload-large-file', function () {
	// Create a temporary file for testing (1MB)
	$temp_file = tempnam( sys_get_temp_dir(), 'test_large_upload_' );
	file_put_contents( $temp_file, str_repeat( 'A', 1024 * 1024 ) );

	// Use CURLFile to upload the file
	$data = [
		'file' => new \CURLFile( $temp_file, 'application/octet-stream', 'large_file.bin' ),
		'name' => 'large_file_test',
	];

	$response = $this->http->post(
		url: 'http://localhost:17171/?method=post',
		data: $data
	);

	// Clean up the temporary file
	unlink( $temp_file );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'large_file_test' );
	expect( $body['post'] )->toHaveKey( 'file' );
} );

test( 'upload-with-custom-mime-type', function () {
	// Create a temporary file for testing
	$temp_file = tempnam( sys_get_temp_dir(), 'test_upload_' );
	file_put_contents( $temp_file, '{"data": "JSON content"}' );

	// Use custom mime type
	$data = [
		'file' => new \CURLFile( $temp_file, 'application/json', 'data.json' ),
		'name' => 'json_file_test',
	];

	$response = $this->http->post(
		url: 'http://localhost:17171/?method=post',
		data: $data
	);

	// Clean up the temporary file
	unlink( $temp_file );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	$body = json_decode( $response->body, true );
	expect( $body )->toHaveKey( 'post' );
	expect( $body['post']['name'] )->toBe( 'json_file_test' );
	expect( $body['post'] )->toHaveKey( 'file' );
} );
