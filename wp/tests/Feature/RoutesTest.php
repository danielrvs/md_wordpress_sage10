<?php

declare(strict_types=1);

/**
 * Helper function to perform HTTP GET requests to the local server.
 */
function fetchUrlStatusCode(string $path): int
{
    $baseUrl = rtrim(getenv('WP_HOME') ?: 'http://localhost', '/');
    $url = $baseUrl . '/' . ltrim($path, '/');

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_NOBODY         => true,
        CURLOPT_TIMEOUT        => 10,
    ]);

    curl_exec($ch);
    $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $statusCode;
}

dataset('configured_routes', [
    'home page'                     => ['/'],
    'blog archive page'             => ['/blog/'],
    'single blog post (/blog/*)'   => ['/blog/hello-world/'],
    'doctors directory (/doctors/)' => ['/doctors/'],
    'pricing page (/pricing/)'      => ['/pricing/'],
]);

test('configured routes return HTTP 200 OK', function (string $route) {
    $statusCode = fetchUrlStatusCode($route);

    expect($statusCode)->toBe(200);
})->with('configured_routes');
