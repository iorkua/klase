<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\ScannedDocument;
// use App\Models\FileIndexing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UnindexedScanningController extends Controller
{
    public function index()
    {
        // Mock statistics for now (until models are ready)
        $stats = [
            'uploads_today' => 0,
            'pending_indexing' => 0,
            'total_unindexed' => 0
        ];

        // Mock recent uploads (empty collection for now)
        $recentUploads = collect([]);

        return view('scanning.unindexed', compact('stats', 'recentUploads'));
    }

    // Commented out methods that depend on models for now
    /*
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,gif,bmp,tiff,webp|max:20480', // 20MB max
        ]);

        $uploadedFiles = [];
        
        try {
            DB::beginTransaction();

            foreach ($request->file('files') as $file) {
                // Store the file
                $path = $file->store('unindexed_scans', 'public');
                
                // Create scanned document record without file_indexing_id
                $scannedDoc = ScannedDocument::create([
                    'file_indexing_id' => null, // No indexing record yet
                    'original_filename' => $file->getClientOriginalName(),
                    'stored_filename' => basename($path),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'paper_size' => 'Unknown', // Will be determined later
                    'document_type' => 'Unindexed',
                    'notes' => 'Uploaded as unindexed document',
                    'status' => 'uploaded',
                    'uploaded_by' => Auth::id(),
                    'upload_batch_id' => uniqid('BATCH_')
                ]);

                $uploadedFiles[] = [
                    'id' => $scannedDoc->id,
                    'name' => $file->getClientOriginalName(),
                    'size' => $this->formatFileSize($file->getSize()),
                    'type' => $file->getMimeType(),
                    'status' => 'Ready for analysis',
                    'date' => $scannedDoc->created_at->format('M d, Y'),
                    'path' => $path
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'files' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error uploading files: ' . $e->getMessage()
            ], 500);
        }
    }
    */

    // Helper methods (these work without models)
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}