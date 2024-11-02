<?php
declare( strict_types = 1 );

namespace T7\HTTP;

class Response {
	public string $using = '';

	public bool $error = false;

	public int $code = 0;

	public float $http_version = 0.0;

	public array $headers = [];

	public array $timing = [];

	public string $body = '';

	public function __construct() {}
}
