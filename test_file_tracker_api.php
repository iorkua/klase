<?php
/**
 * File Tracker API Test Script
 * 
 * This script tests the File Tracker API endpoints to ensure they're working correctly.
 * Run this script from the command line or browser to test the implementation.
 */

// Set the base URL for your application
$baseUrl = 'http://klaes.com.ng/kangi.com.ng'; // Adjust this to your actual URL

// Test data
$testData = [
    'file_indexing_id' => 1, // Make sure this exists in your file_indexings table
    'rfid_tag' => 'TEST' . date('YmdHis'),
    'qr_code' => 'QR' . date('YmdHis'),
    'current_location' => 'Test Archive Room',
    'current_holder' => 'Test Holder',
    'current_handler' => 'Test Handler',
    'date_received' => date('Y-m-d H:i:s'),
    'due_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
    'status' => 'active'
];

/**
 * Make HTTP request
 */
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'response' => $response,
        'http_code' => $httpCode,
        'error' => $error
    ];
}

/**
 * Test function
 */
function runTest($testName, $url, $method = 'GET', $data = null, $expectedCode = 200) {
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "Testing: $testName\n";
    echo "URL: $url\n";
    echo "Method: $method\n";
    if ($data) {
        echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    echo str_repeat('-', 60) . "\n";
    
    $result = makeRequest($url, $method, $data);
    
    echo "HTTP Code: " . $result['http_code'] . "\n";
    
    if ($result['error']) {
        echo "CURL Error: " . $result['error'] . "\n";
        return false;
    }
    
    $responseData = json_decode($result['response'], true);
    
    if ($result['http_code'] === $expectedCode) {
        echo "✅ Test PASSED\n";
        if ($responseData) {
            echo "Response: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
        }
        return $responseData;
    } else {
        echo "❌ Test FAILED\n";
        echo "Expected HTTP Code: $expectedCode\n";
        echo "Actual HTTP Code: " . $result['http_code'] . "\n";
        echo "Response: " . $result['response'] . "\n";
        return false;
    }
}

// Start testing
echo "File Tracker API Test Suite\n";
echo "Base URL: $baseUrl\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n";

$createdTrackingId = null;
$testRfidTag = $testData['rfid_tag'];

// Test 1: List all file trackings (should work even if empty)
$result = runTest(
    'List All File Trackings',
    "$baseUrl/api/file-trackings",
    'GET'
);

// Test 2: Create new file tracking
$result = runTest(
    'Create New File Tracking',
    "$baseUrl/api/file-trackings",
    'POST',
    $testData,
    201
);

if ($result && isset($result['data']['id'])) {
    $createdTrackingId = $result['data']['id'];
    echo "Created tracking ID: $createdTrackingId\n";
}

// Test 3: Get specific file tracking (if we created one)
if ($createdTrackingId) {
    runTest(
        'Get Specific File Tracking',
        "$baseUrl/api/file-trackings/$createdTrackingId",
        'GET'
    );
}

// Test 4: Update file tracking (if we created one)
if ($createdTrackingId) {
    $updateData = [
        'current_location' => 'Updated Test Location',
        'status' => 'checked_out',
        'reason' => 'Testing update functionality'
    ];
    
    runTest(
        'Update File Tracking',
        "$baseUrl/api/file-trackings/$createdTrackingId",
        'PUT',
        $updateData
    );
}

// Test 5: Add movement entry (if we created one)
if ($createdTrackingId) {
    $movementData = [
        'action' => 'test_movement',
        'from_location' => 'Test Archive Room',
        'to_location' => 'Updated Test Location',
        'reason' => 'Testing movement functionality',
        'notes' => 'This is a test movement entry'
    ];
    
    runTest(
        'Add Movement Entry',
        "$baseUrl/api/file-trackings/$createdTrackingId/move",
        'POST',
        $movementData
    );
}

// Test 6: Register RFID tag
$rfidData = [
    'file_indexing_id' => $testData['file_indexing_id'],
    'rfid_tag' => 'RFID_TEST_' . date('YmdHis')
];

$result = runTest(
    'Register RFID Tag',
    "$baseUrl/api/rfid/register",
    'POST',
    $rfidData
);

if ($result && isset($result['data']['rfid_tag'])) {
    $testRfidTag = $result['data']['rfid_tag'];
}

// Test 7: Scan RFID tag
runTest(
    'Scan RFID Tag',
    "$baseUrl/api/rfid/scan/$testRfidTag",
    'GET'
);

// Test 8: Generate summary report
runTest(
    'Generate Summary Report',
    "$baseUrl/api/rfid/report?type=summary",
    'GET'
);

// Test 9: Generate overdue report
runTest(
    'Generate Overdue Report',
    "$baseUrl/api/rfid/report?type=overdue",
    'GET'
);

// Test 10: Test with filters
runTest(
    'List File Trackings with Filters',
    "$baseUrl/api/file-trackings?status=active&per_page=5",
    'GET'
);

// Test 11: Search functionality
runTest(
    'Search File Trackings',
    "$baseUrl/api/file-trackings?search=test",
    'GET'
);

// Test 12: Batch operation (if we have tracking IDs)
if ($createdTrackingId) {
    $batchData = [
        'action' => 'extend_due_date',
        'tracking_ids' => [$createdTrackingId],
        'new_due_date' => date('Y-m-d H:i:s', strtotime('+60 days')),
        'reason' => 'Testing batch operation'
    ];
    
    runTest(
        'Batch Update Operation',
        "$baseUrl/api/file-trackings/batch/overdue",
        'POST',
        $batchData
    );
}

// Test 13: Error handling - Invalid data
$invalidData = [
    'file_indexing_id' => 99999, // Non-existent ID
    'rfid_tag' => 'INVALID_TAG_WITH_SPECIAL_CHARS!@#',
    'status' => 'invalid_status'
];

runTest(
    'Error Handling - Invalid Data',
    "$baseUrl/api/file-trackings",
    'POST',
    $invalidData,
    422 // Expecting validation error
);

// Test 14: Error handling - Non-existent resource
runTest(
    'Error Handling - Non-existent Resource',
    "$baseUrl/api/file-trackings/99999",
    'GET',
    null,
    404 // Expecting not found
);

echo "\n" . str_repeat('=', 60) . "\n";
echo "Test Suite Completed at: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat('=', 60) . "\n";

// Cleanup instructions
if ($createdTrackingId) {
    echo "\nCleanup Instructions:\n";
    echo "A test file tracking was created with ID: $createdTrackingId\n";
    echo "You may want to delete it manually from the database if needed.\n";
    echo "SQL: DELETE FROM file_trackings WHERE id = $createdTrackingId;\n";
}

echo "\nNotes:\n";
echo "- Make sure your database is set up and the file_trackings table exists\n";
echo "- Ensure you have at least one record in the file_indexings table\n";
echo "- Update the \$baseUrl variable to match your application URL\n";
echo "- Some tests may fail if the database is not properly configured\n";
echo "- Check your Laravel logs for detailed error information\n";
?>