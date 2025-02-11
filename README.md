# T7\HTTP\Request

<a href="https://github.com/josephscott/t7-http-request/actions"><img src="https://github.com/josephscott/t7-http-request/actions/workflows/tests.yml/badge.svg"></a>

A lightweight, flexible HTTP client library for PHP that supports both cURL and native PHP streams. This library provides a simple, consistent interface for making HTTP requests while maintaining powerful features like custom headers, authentication, and various request methods.

## Installation

```bash
composer require t7/http-request
```

## Basic Usage

```php
use T7\HTTP\Request;

$http = new Request();

// Simple GET request
$response = $http->get(url: 'https://api.example.com/data');

// POST request with data
$response = $http->post(
    url: 'https://api.example.com/create',
    data: ['name' => 'test', 'value' => 123]
);

// PUT request with custom headers
$response = $http->put(
    url: 'https://api.example.com/update',
    data: ['status' => 'active'],
    headers: ['X-Custom-Header' => 'value']
);

// PATCH request with JSON data
$response = $http->patch(
    url: 'https://api.example.com/update',
    data: ['name' => 'updated_name'],
    headers: ['Content-Type' => 'application/merge-patch+json']
);

// DELETE request
$response = $http->delete(url: 'https://api.example.com/remove/123');
```

## Response Object

The response object contains:
- `error`: Boolean indicating if the request failed
- `code`: HTTP status code
- `body`: Response body
- `headers`: Array of response headers

```php
$response = $http->get(url: 'https://api.example.com/data');
if (!$response->error) {
    $status_code = $response->code;
    $body = $response->body;
    $content_type = $response->headers['content-type'];
}
```

## Advanced Options

### Custom Connection Options

```php
$response = $http->get(
    url: 'https://api.example.com',
    options: [
        'timeout' => 30,
        'connect_timeout' => 5,
        'max_redirects' => 3,
        'verify_ssl' => true,
    ]
);
```

### Basic Authentication

```php
$response = $http->get(
    url: 'https://api.example.com/protected',
    headers: [
        'Authorization' => 'Basic ' . base64_encode('username:password')
    ]
);
```

### Handling Compressed Responses

```php
$response = $http->get(
    url: 'https://api.example.com/data',
    headers: ['Accept-Encoding' => 'gzip, deflate']
);
```

### Choosing the HTTP Client Implementation

```php
$http = new Request();
$http->default_options['using'] = 'curl';    // Use cURL
// or
$http->default_options['using'] = 'php';     // Use PHP streams
```

## Error Handling

The library uses a simple error handling approach, setting the `error` property on the response object.

```php
$response = $http->get(url: 'http://invalid-domain.test');
if ($response->error) {
    // Handle error
    $error_code = $response->code;
}
```

## License

MIT License
