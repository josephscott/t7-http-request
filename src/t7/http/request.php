<?php
declare( strict_types = 1 );

namespace T7\HTTP;

use CURLOPT_CUSTOMREQUEST;
use CURLOPT_ENCODING;
use CURLOPT_FOLLOWLOCATION;
use CURLOPT_HEADERFUNCTION;
use CURLOPT_HTTPHEADER;
use CURLOPT_POST;
use CURLOPT_POSTFIELDS;
use CURLOPT_PROTOCOLS;
use CURLOPT_RETURNTRANSFER;
use CURLOPT_TIMEOUT;
use CURLPROTO_HTTP;
use CURLPROTO_HTTPS;
use function array_merge;
use function array_shift;
use function count;
use function curl_close;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function curl_setopt_array;
use function explode;
use function file_get_contents;
use function floatval;
use function function_exists;
use function gzdecode;
use function http_build_query;
use function intval;
use function is_numeric;
use function microtime;
use function preg_match;
use function stream_context_create;
use function strlen;
use function strpos;
use function strtolower;
use function trim;

class Request {
	public array $default_options = [
		'using' => 'curl',
		'timeout' => 30,
		'encoding' => 'gzip',
	];

	public array $default_headers = [
		'Connection' => 'close',
		'Accept' => '*/*',
		'User-Agent' => 't7-http-request',
	];

	public function __construct() {}

	public function delete(
		string $url,
		array $headers = [],
		array $options = []
	) : Response {
		$out = $this->request(
			method: 'DELETE',
			url: $url,
			headers: $headers,
			options: $options
		);
		return $out;
	}

	public function get(
		string $url,
		array $headers = [],
		array $options = []
	) : Response {
		$out = $this->request(
			method: 'GET',
			url: $url,
			headers: $headers,
			options: $options
		);
		return $out;
	}

	public function head(
		string $url,
		array $headers = [],
		array $options = []
	) : Response {
		$out = $this->request(
			method: 'HEAD',
			url: $url,
			headers: $headers,
			options: $options
		);
		return $out;
	}

	public function options(
		string $url,
		array $headers = [],
		array $options = []
	) : Response {
		$out = $this->request(
			method: 'OPTIONS',
			url: $url,
			headers: $headers,
			options: $options
		);
		return $out;
	}

	public function patch(
		string $url,
		array $headers = [],
		string|array $data = [],
		array $options = []
	) : Response {
		$out = $this->request(
			method: 'PATCH',
			url: $url,
			headers: $headers,
			data: $data,
			options: $options
		);
		return $out;
	}

	public function post(
		string $url,
		array $headers = [],
		string|array $data = [],
		array $options = []
	) : Response {
		$out = $this->request(
			method: 'POST',
			url: $url,
			headers: $headers,
			data: $data,
			options: $options
		);
		return $out;
	}

	public function put(
		string $url,
		array $headers = [],
		string|array $data = [],
		array $options = []
	) : Response {
		$out = $this->request(
			method: 'PUT',
			url: $url,
			headers: $headers,
			data: $data,
			options: $options
		);
		return $out;
	}

	public function request(
		string $method,
		string $url,
		array $headers = [],
		string|array $data = [],
		array $options = []
	) : Response {
		$out = null;

		$merged_options = array_merge( $this->default_options, $options );

		if ( $merged_options['using'] === 'curl' ) {
			$out = $this->request_curl(
				method: $method,
				url: $url,
				headers: $headers,
				data: $data,
				options: $merged_options
			);
		} elseif ( $merged_options['using'] === 'php' ) {
			$out = $this->request_php(
				method: $method,
				url: $url,
				headers: $headers,
				data: $data,
				options: $merged_options
			);
		}

		return $out;
	}

	public function request_curl(
		string $method,
		string $url,
		array $headers = [],
		string|array $data = [],
		array $options = []
	) : Response {
		$response = new Response();
		$response->using = 'curl';

		$curl = curl_init( $url );
		if ( $curl === false ) {
			$response->error = true;
			return $response;
		}

		curl_setopt_array( $curl, [
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_TIMEOUT => $options['timeout'],
			CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_HEADERFUNCTION => function ( $curl, $header ) use ( &$response ) {
				$length = strlen( $header );
				$parts = explode( ':', $header, 2 );

				if ( count( $parts ) < 2 ) {
					if ( preg_match( '/^HTTP\/([0-9\.]+)\s+([0-9]+)/', $header, $matches ) ) {
						$response->http_version = (int) $matches[1];
						$response->code = (int) $matches[2];
						return $length;
					} else {
						// Invalid header
						return $length;
					}
				}

				$key = strtolower( trim( $parts[0] ) );
				$value = trim( $parts[1] );
				if ( is_numeric( $value ) ) {
					$value = (int) $value;
				}

				// May need to reconsider this for duplicate headers
				$response->headers[$key] = $value;
				return $length;
			},
		] );

		if ( $method === 'PATCH' || $method === 'POST' ) {
			curl_setopt( $curl, CURLOPT_POST, true );
			if ( is_array( $data ) ) {
				curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $data ) );
			} else {
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
			}
		}

		if ( $method === 'PUT' ) {
			curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $data ) );
			$headers['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		if ( ! empty( $options['encoding'] ) ) {
			curl_setopt( $curl, CURLOPT_ENCODING, $options['encoding'] );
		}

		$headers = array_merge( $this->default_headers, $headers );
		$curl_headers = [];
		foreach ( $headers as $k => $v ) {
			$curl_headers[] = "$k: $v";
		}
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $curl_headers );

		$start = microtime( true );
		$body = curl_exec( $curl );
		$response->timing['done'] = intval(
			( microtime( true ) - $start ) * 1000000
		);

		if ( $body === false ) {
			$response->error = true;
		} else {
			$response->body = $body;
		}

		$info = curl_getinfo( $curl );
		curl_close( $curl );

		foreach ( $info as $k => $v ) {
			if ( strpos( $k, '_time_us' ) !== false ) {
				$response->timing['curl_' . $k] = $v;
			}
		}

		return $response;
	}

	public function request_php(
		string $method,
		string $url,
		array $headers = [],
		array $data = [],
		array $options = []
	) : Response {
		$response = new Response();
		$response->using = 'php';

		if (
			! empty( $options['encoding'] )
			&& function_exists( 'gzdecode' )
		) {
			$headers['Accept-Encoding'] = 'gzip';
		}

		$context = $this->php_build_context(
			method: $method,
			headers: $headers,
			data: $data,
			options: $options
		);

		// XXX: HACK
		// Make Pest happy by suppressing the warnings that can happen
		// I'd like to find a way to deal with warnings without using @
		$start = microtime( true );
		$body = @file_get_contents(
			filename: $url,
			use_include_path: false,
			context: $context
		);
		$response->timing['done'] = intval(
			( microtime( true ) - $start ) * 1000000
		);
		if ( $body === false ) {
			$response->error = true;
			return $response;
		}

		$response->body = $body;
		$response->headers = self::php_parse_headers(
			headers: $http_response_header
		);

		if (
			isset( $response->headers['content-encoding'] )
			&& $response->headers['content-encoding'] === 'gzip'
		) {
			$response->body = gzdecode( $body );
		}

		$response->http_version = $response->headers['http_version'];
		unset( $response->headers['http_version'] );

		$response->code = $response->headers['response_code'];
		unset( $response->headers['response_code'] );

		if ( $response->code > 399 ) {
			$response->error = true;
			return $response;
		}

		return $response;
	}

	private function php_build_context(
		string $method,
		array $headers = [],
		array $data = [],
		array $options = []
	) {
		$php_options = [];
		$php_options['http'] = [];
		$php_options['http']['method'] = $method;
		$php_options['http']['timeout'] = $options['timeout'];

		$headers = array_merge( $this->default_headers, $headers );

		if ( ! empty( $data ) ) {
			$php_options['http']['content'] = http_build_query( $data );
			$headers['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		foreach ( $headers as $header_name => $header_value ) {
			if ( ! isset( $php_options['http']['header'] ) ) {
				$php_options['http']['header'] = '';
			}

			$php_options['http']['header'] .= "$header_name: $header_value\r\n";
		}

		$context = stream_context_create( $php_options );
		return $context;
	}

	private function php_parse_headers( array $headers ) : array {
		$parsed = [];

		$response_code = array_shift( $headers );
		if ( preg_match( '#HTTP/([0-9\.]+)\s+([0-9]+)#', $response_code, $matches ) ) {
			$headers[] = 'http_version: ' . floatval( $matches[1] );
			$headers[] = 'response_code: ' . intval( $matches[2] );
		}

		foreach ( $headers as $header ) {
			$parts = explode( ':', $header, 2 );
			if ( count( $parts ) === 2 ) {
				$parts[1] = trim( $parts[1] );
				if ( is_numeric( $parts[1] ) ) {
					$parts[1] = (int) $parts[1];
				}

				$parsed[strtolower( trim( $parts[0] ) )] = $parts[1];
			}
		}

		return $parsed;
	}
}
