<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\FileIndexing;

echo "Checking files with scannings and pagetypings...\n";

$files = FileIndexing::on('sqlsrv')
    ->with(['scannings', 'pagetypings'])
    ->whereHas('scannings')
    ->whereHas('pagetypings')
    ->get();

echo "Total files with both scannings and pagetypings: " . $files->count() . "\n\n";

foreach ($files as $file) {
    $scanningCount = $file->scannings->count();
    $pageTypingCount = $file->pagetypings->count();

    echo "File: {$file->file_number} - {$file->file_title}\n";
    echo "  Scannings: {$scanningCount}\n";
    echo "  PageTypings: {$pageTypingCount}\n";
    echo "  Status: " . ($pageTypingCount >= $scanningCount ? 'COMPLETED' : 'IN PROGRESS') . "\n";
    echo "\n";
}
