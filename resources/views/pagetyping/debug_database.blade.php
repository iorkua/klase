<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EDMS Database Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .count { font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <h1>EDMS Database Debug Information</h1>
    
    <div class="section info">
        <h3>Database Connection Test</h3>
        <p>Testing connection to SQL Server database...</p>
    </div>

    <?php
    try {
        // Test database connection
        $connection = DB::connection('sqlsrv');
        $connection->getPdo();
        echo '<div class="section success"><h3>✅ Database Connection: SUCCESS</h3></div>';
        
        // Check File Indexings
        $fileIndexings = DB::connection('sqlsrv')->table('file_indexings')->get();
        echo '<div class="section">';
        echo '<h3>File Indexings Table</h3>';
        echo '<p class="count">Total Records: ' . $fileIndexings->count() . '</p>';
        
        if ($fileIndexings->count() > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>File Number</th><th>File Title</th><th>Main App ID</th><th>Sub App ID</th><th>Created</th></tr>';
            foreach ($fileIndexings->take(10) as $fi) {
                echo '<tr>';
                echo '<td>' . $fi->id . '</td>';
                echo '<td>' . ($fi->file_number ?? 'N/A') . '</td>';
                echo '<td>' . ($fi->file_title ?? 'N/A') . '</td>';
                echo '<td>' . ($fi->main_application_id ?? 'N/A') . '</td>';
                echo '<td>' . ($fi->subapplication_id ?? 'N/A') . '</td>';
                echo '<td>' . ($fi->created_at ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
        
        // Check Scannings
        $scannings = DB::connection('sqlsrv')->table('scannings')->get();
        echo '<div class="section">';
        echo '<h3>Scannings Table</h3>';
        echo '<p class="count">Total Records: ' . $scannings->count() . '</p>';
        
        if ($scannings->count() > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>File Indexing ID</th><th>Original Filename</th><th>Document Path</th><th>Status</th><th>Created</th></tr>';
            foreach ($scannings->take(10) as $scan) {
                echo '<tr>';
                echo '<td>' . $scan->id . '</td>';
                echo '<td>' . ($scan->file_indexing_id ?? 'N/A') . '</td>';
                echo '<td>' . ($scan->original_filename ?? 'N/A') . '</td>';
                echo '<td>' . ($scan->document_path ?? 'N/A') . '</td>';
                echo '<td>' . ($scan->status ?? 'N/A') . '</td>';
                echo '<td>' . ($scan->created_at ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
        
        // Check Page Typings
        $pageTypings = DB::connection('sqlsrv')->table('pagetypings')->get();
        echo '<div class="section">';
        echo '<h3>Page Typings Table</h3>';
        echo '<p class="count">Total Records: ' . $pageTypings->count() . '</p>';
        
        if ($pageTypings->count() > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>File Indexing ID</th><th>Page Type</th><th>Page Number</th><th>Serial Number</th><th>File Path</th><th>Created</th></tr>';
            foreach ($pageTypings->take(10) as $pt) {
                echo '<tr>';
                echo '<td>' . $pt->id . '</td>';
                echo '<td>' . ($pt->file_indexing_id ?? 'N/A') . '</td>';
                echo '<td>' . ($pt->page_type ?? 'N/A') . '</td>';
                echo '<td>' . ($pt->page_number ?? 'N/A') . '</td>';
                echo '<td>' . ($pt->serial_number ?? 'N/A') . '</td>';
                echo '<td>' . ($pt->file_path ?? 'N/A') . '</td>';
                echo '<td>' . ($pt->created_at ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
        
        // Check relationships
        echo '<div class="section">';
        echo '<h3>Relationship Analysis</h3>';
        
        // Files with scannings but no page typings
        $pendingFiles = DB::connection('sqlsrv')
            ->table('file_indexings as fi')
            ->leftJoin('scannings as s', 'fi.id', '=', 's.file_indexing_id')
            ->leftJoin('pagetypings as pt', 'fi.id', '=', 'pt.file_indexing_id')
            ->whereNotNull('s.id')
            ->whereNull('pt.id')
            ->select('fi.*')
            ->distinct()
            ->get();
        
        echo '<p><strong>Files with scannings but no page typings:</strong> <span class="count">' . $pendingFiles->count() . '</span></p>';
        
        // Files with both scannings and page typings
        $completedFiles = DB::connection('sqlsrv')
            ->table('file_indexings as fi')
            ->join('scannings as s', 'fi.id', '=', 's.file_indexing_id')
            ->join('pagetypings as pt', 'fi.id', '=', 'pt.file_indexing_id')
            ->select('fi.*')
            ->distinct()
            ->get();
        
        echo '<p><strong>Files with both scannings and page typings:</strong> <span class="count">' . $completedFiles->count() . '</span></p>';
        
        // Files with only file indexing
        $indexOnlyFiles = DB::connection('sqlsrv')
            ->table('file_indexings as fi')
            ->leftJoin('scannings as s', 'fi.id', '=', 's.file_indexing_id')
            ->whereNull('s.id')
            ->select('fi.*')
            ->get();
        
        echo '<p><strong>Files with only indexing (no scannings):</strong> <span class="count">' . $indexOnlyFiles->count() . '</span></p>';
        echo '</div>';
        
        // Sample data for testing
        if ($fileIndexings->count() > 0) {
            $sampleFile = $fileIndexings->first();
            echo '<div class="section info">';
            echo '<h3>Sample Test Data</h3>';
            echo '<p><strong>Sample File Indexing ID for testing:</strong> ' . $sampleFile->id . '</p>';
            echo '<p><strong>File Number:</strong> ' . ($sampleFile->file_number ?? 'N/A') . '</p>';
            echo '<p><strong>File Title:</strong> ' . ($sampleFile->file_title ?? 'N/A') . '</p>';
            echo '<p>Use this ID in the route testing page: <code>/pagetyping/test-routes</code></p>';
            echo '</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="section error">';
        echo '<h3>❌ Database Connection: FAILED</h3>';
        echo '<p>Error: ' . $e->getMessage() . '</p>';
        echo '</div>';
    }
    ?>

    <div class="section">
        <h3>Quick Actions</h3>
        <p><a href="{{ route('pagetyping.test') }}">🧪 Test Routes</a></p>
        <p><a href="{{ route('pagetyping.index') }}">📝 Page Typing Dashboard</a></p>
        <p><a href="{{ route('scanning.index') }}">📄 Scanning Dashboard</a></p>
    </div>
</body>
</html>