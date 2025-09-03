<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

try {
    // Test database connection
    echo "Testing database connection...\n";
    
    // Check if table exists
    $tableExists = DB::connection('sqlsrv')->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'Rack_Shelf_Labels'");
    echo "Table exists check: " . json_encode($tableExists) . "\n";
    
    if (empty($tableExists)) {
        echo "Table 'Rack_Shelf_Labels' does not exist. Returning default batches.\n";
        $batches = [];
        for ($i = 1; $i <= 10; $i++) {
            $batches[] = [
                'id' => $i,
                'text' => $i
            ];
        }
        echo "Default batches: " . json_encode($batches) . "\n";
    } else {
        echo "Table 'Rack_Shelf_Labels' exists. Querying available batches...\n";
        
        // Get available batches (is_used = 0 or null)
        $availableBatches = DB::connection('sqlsrv')
            ->table('Rack_Shelf_Labels')
            ->where(function($query) {
                $query->where('is_used', 0)
                      ->orWhereNull('is_used');
            })
            ->orderBy('id')
            ->limit(10)
            ->get(['id', 'full_label']);
        
        echo "Available batches from DB: " . json_encode($availableBatches) . "\n";
        
        $batches = [];
        foreach ($availableBatches as $batch) {
            $batches[] = [
                'id' => $batch->id,
                'text' => $batch->id
            ];
        }
        
        echo "Formatted batches: " . json_encode($batches) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
