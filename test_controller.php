<?php
// Load autoloader first
require_once __DIR__ . '/vendor/autoload.php';

echo "Testing PageTypingController...\n";

// Check if class exists
if (class_exists('App\Http\Controllers\PageTypingController')) {
    echo "✓ PageTypingController class exists\n";
    
    // Check if getStats method exists
    if (method_exists('App\Http\Controllers\PageTypingController', 'getStats')) {
        echo "✓ getStats method exists\n";
    } else {
        echo "✗ getStats method does not exist\n";
    }
} else {
    echo "✗ PageTypingController class does not exist\n";
    echo "Checking if file exists...\n";
    if (file_exists(__DIR__ . '/app/Http/Controllers/PageTypingController.php')) {
        echo "✓ File exists\n";
    } else {
        echo "✗ File does not exist\n";
    }
}

echo "Done.\n";
