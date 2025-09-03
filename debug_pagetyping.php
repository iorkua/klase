<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Test file for debugging pagetyping copy issue
$fileNumber = 'ST-RES-2025-46-001';

// Get scanning record for this file
$scanning = DB::connection('sqlsrv')->table('scannings')
    ->join('file_indexing', 'scannings.file_indexing_id', '=', 'file_indexing.id')
    ->where('file_indexing.file_number', $fileNumber)
    ->select('scannings.*')
    ->first();

if ($scanning) {
    echo "Found scanning record:\n";
    echo "ID: " . $scanning->id . "\n";
    echo "Document Path: " . ($scanning->document_path ?? 'NULL') . "\n";
    echo "Original Filename: " . ($scanning->original_filename ?? 'NULL') . "\n";
    
    // Test file extension extraction
    $fileExtension = pathinfo($scanning->original_filename, PATHINFO_EXTENSION);
    echo "Extracted Extension: '" . $fileExtension . "'\n";
    
    if (empty($fileExtension)) {
        echo "Extension is empty, trying document_path...\n";
        $pathExtension = pathinfo($scanning->document_path, PATHINFO_EXTENSION);
        echo "Path Extension: '" . $pathExtension . "'\n";
        $fileExtension = !empty($pathExtension) ? $pathExtension : 'pdf';
    }
    
    $fileName = $fileNumber . '.' . strtolower($fileExtension);
    echo "Constructed Filename: '" . $fileName . "'\n";
    
    // Check if filename ends with dot
    if (substr($fileName, -1) === '.') {
        echo "ERROR: Filename ends with dot!\n";
    }
    
    // Check source file
    $originalPath = storage_path('app/public/' . $scanning->document_path);
    echo "Source File Path: " . $originalPath . "\n";
    echo "Source File Exists: " . (file_exists($originalPath) ? 'YES' : 'NO') . "\n";
    
    if (file_exists($originalPath)) {
        echo "Source File Size: " . filesize($originalPath) . " bytes\n";
        echo "Source File Readable: " . (is_readable($originalPath) ? 'YES' : 'NO') . "\n";
    }
    
} else {
    echo "No scanning record found for file number: $fileNumber\n";
}
