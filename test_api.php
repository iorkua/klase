<?php
// Simple test script to check if the API endpoint is working
header('Content-Type: application/json');

// Test the search-file-numbers endpoint
$url = 'http://localhost/kangi.com.ng/api/search-file-numbers';

$data = [
    'search' => '',
    'page' => 1
];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo json_encode([
        'error' => 'Failed to connect to API',
        'url' => $url
    ]);
} else {
    echo json_encode([
        'success' => true,
        'response' => json_decode($result, true),
        'raw_response' => $result
    ]);
}
?>