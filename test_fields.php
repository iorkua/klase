<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get a sample record to see what fields are available
    $sample = DB::connection('sqlsrv')->table('recertification_applications')->first();
    
    if ($sample) {
        echo "Available fields in recertification_applications table:\n";
        echo "=================================================\n";
        
        foreach ($sample as $field => $value) {
            echo "$field: " . (is_null($value) ? 'NULL' : $value) . "\n";
        }
    } else {
        echo "No records found in recertification_applications table\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}