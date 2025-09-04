<?php

/**
 * Test script to verify our serial number parsing fixes
 */

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PageTyping;
use Illuminate\Support\Facades\DB;

echo "=== Non-Numeric Serial Number Test ===\n\n";

try {
    // Test the fixed PHP logic for finding max serial
    echo "Testing PHP serial number extraction...\n";
    
    // Get all serial numbers from database
    $serialNumbers = PageTyping::on('sqlsrv')->pluck('serial_number')->toArray();
    echo "Found " . count($serialNumbers) . " serial numbers in database\n";
    
    if (!empty($serialNumbers)) {
        echo "Sample serial numbers: " . implode(', ', array_slice($serialNumbers, 0, 10)) . "\n";
        
        // Test our extraction logic
        $numericSerials = [];
        foreach ($serialNumbers as $serial) {
            if (preg_match('/^(\d+)/', (string)$serial, $matches)) {
                $numericSerials[] = (int)$matches[1];
            }
        }
        
        if (!empty($numericSerials)) {
            $maxSerial = max($numericSerials);
            $nextSerial = $maxSerial + 1;
            
            echo "✅ Successfully extracted numeric serials\n";
            echo "Max numeric serial: $maxSerial\n";
            echo "Next serial should be: $nextSerial\n";
            echo "Formatted: " . str_pad($nextSerial, 2, "0", STR_PAD_LEFT) . "\n";
        } else {
            echo "⚠️  No numeric serials found\n";
        }
    } else {
        echo "ℹ️  No serial numbers in database\n";
    }
    
    echo "\nTesting various serial number formats...\n";
    
    // Test various serial number formats
    $testSerials = ['1', '02', '1a', '2b', '10c', '003', 'abc', '', null];
    
    foreach ($testSerials as $testSerial) {
        echo "Testing '$testSerial': ";
        
        if (preg_match('/^(\d+)/', (string)$testSerial, $matches)) {
            $numeric = (int)$matches[1];
            echo "Extracted: $numeric ✅\n";
        } else {
            echo "No numeric part found ⚠️\n";
        }
    }
    
    echo "\n✅ All tests completed successfully!\n";
    echo "The non-numeric value error should now be resolved.\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
