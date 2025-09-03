<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME IN ('temp_fileNo', 'fileNumber', 'mother_applications')");
    echo "Found tables:\n";
    foreach($tables as $table) {
        echo "- " . $table->TABLE_NAME . "\n";
    }
    
    // Check structure of temp_fileNo table if it exists
    try {
        $columns = DB::select("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'temp_fileNo' ORDER BY ORDINAL_POSITION");
        if (count($columns) > 0) {
            echo "\ntemp_fileNo table structure:\n";
            foreach($columns as $column) {
                echo "- " . $column->COLUMN_NAME . " (" . $column->DATA_TYPE . ")\n";
            }
        }
    } catch(Exception $e) {
        echo "temp_fileNo table doesn't exist or error: " . $e->getMessage() . "\n";
    }
    
    // Check structure of fileNumber table if it exists
    try {
        $columns = DB::select("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'fileNumber' ORDER BY ORDINAL_POSITION");
        if (count($columns) > 0) {
            echo "\nfileNumber table structure:\n";
            foreach($columns as $column) {
                echo "- " . $column->COLUMN_NAME . " (" . $column->DATA_TYPE . ")\n";
            }
        }
    } catch(Exception $e) {
        echo "fileNumber table doesn't exist or error: " . $e->getMessage() . "\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
