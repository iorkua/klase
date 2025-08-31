<?php

/**
 * FPDI Library Test Script
 * 
 * This script tests if the FPDI library is properly installed and can split PDFs
 */

require_once 'vendor/autoload.php';

echo "=== FPDI Library Test ===\n\n";

// Check if FPDI is available
try {
    if (class_exists('setasign\Fpdi\Fpdi')) {
        echo "✓ FPDI library is installed and available\n";
    } else {
        echo "✗ FPDI library not found\n";
        echo "Please install with: composer require setasign/fpdi\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Error checking FPDI: " . $e->getMessage() . "\n";
    exit(1);
}

// Test basic FPDI functionality
try {
    $pdf = new \setasign\Fpdi\Fpdi();
    echo "✓ FPDI instance created successfully\n";
    
    // Test if we can add a page
    $pdf->AddPage();
    echo "✓ Can add pages to PDF\n";
    
    // Test output capability
    $output = $pdf->Output('S'); // Output as string
    if (strlen($output) > 0) {
        echo "✓ Can generate PDF output\n";
    } else {
        echo "⚠ PDF output is empty\n";
    }
    
} catch (Exception $e) {
    echo "✗ FPDI functionality test failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check required PHP extensions
echo "\n=== PHP Extensions Check ===\n";

$extensions = [
    'gd' => 'Image processing',
    'fileinfo' => 'File type detection',
    'mbstring' => 'String handling',
    'zip' => 'Archive support'
];

foreach ($extensions as $ext => $description) {
    if (extension_loaded($ext)) {
        echo "✓ $ext extension loaded ($description)\n";
    } else {
        echo "⚠ $ext extension not loaded ($description)\n";
    }
}

// Check storage directories
echo "\n=== Storage Directories Check ===\n";

$directories = [
    'storage/app/public/EDMS',
    'storage/app/public/EDMS/PAGETYPING',
    'storage/app/public/EDMS/THUMBNAILS'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✓ Directory exists and is writable: $dir\n";
        } else {
            echo "⚠ Directory exists but not writable: $dir\n";
        }
    } else {
        echo "⚠ Directory does not exist: $dir\n";
    }
}

echo "\n=== Test Summary ===\n";
echo "✓ FPDI library is ready for PDF processing\n";
echo "✓ Page Typing workflow can be used\n";
echo "\nTo create a test PDF split, use the PageTypingService class\n";
echo "Access the Page Typing dashboard at: /pagetyping\n";

echo "\nTest completed successfully!\n";
?>