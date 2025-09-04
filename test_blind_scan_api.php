<?php
// Simple test script to verify the blind scanning folder creation API

require 'vendor/autoload.php';
require 'bootstrap/app.php';

// Set up a basic request
$_POST['file_no'] = 'TEST-API-' . date('YmdHis');
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

try {
    echo "Testing Blind Scan Folder Creation API\n";
    echo "======================================\n";
    
    $request = new \Illuminate\Http\Request();
    $request->merge(['file_no' => $_POST['file_no']]);
    
    $controller = new App\Http\Controllers\BlindScanningController();
    $response = $controller->createFolder($request);
    
    $data = $response->getData(true);
    
    echo "Request File No: " . $_POST['file_no'] . "\n";
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Success: " . ($data['success'] ? 'YES' : 'NO') . "\n";
    echo "Message: " . $data['message'] . "\n";
    
    if ($data['success']) {
        echo "\nFolder Details:\n";
        echo "- File Number: " . $data['data']['file_no'] . "\n";
        echo "- Server Storage: " . $data['data']['storage_path'] . "\n";
        echo "- Local Path: " . $data['data']['local_path'] . "\n";
        echo "- Create Local Instructions: " . ($data['data']['create_local_instructions'] ? 'YES' : 'NO') . "\n";
        
        // Check if folders actually exist
        $storagePath = storage_path('app/public/' . str_replace('storage/app/public/', '', $data['data']['storage_path']));
        $a4Path = $data['data']['storage_a4_path'];
        $a3Path = $data['data']['storage_a3_path'];
        
        echo "\nFolder Creation Verification:\n";
        echo "- Storage Root: " . (file_exists($storagePath) ? 'EXISTS' : 'NOT FOUND') . "\n";
        echo "- A4 Subfolder: " . (file_exists($a4Path) ? 'EXISTS' : 'NOT FOUND') . "\n";
        echo "- A3 Subfolder: " . (file_exists($a3Path) ? 'EXISTS' : 'NOT FOUND') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\nTest completed.\n";
?>
