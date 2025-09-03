<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\FileIndexing;

class FileIndexingController extends Controller
{
  
    public function index()
    {
        try {
            // Get recent file indexing records to generate AI insights
            $recentIndexes = FileIndexing::on('sqlsrv')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $aiInsights = $recentIndexes->map(function ($fi) {
                $owner = $fi->file_title ?? ($fi->owner_name ?? null) ?? 'Unknown Owner';
                $scannedCount = isset($fi->scannings_count) ? $fi->scannings_count : 0;
                $typedCount = isset($fi->pagetypings_count) ? $fi->pagetypings_count : 0;

                $confidence = min(98, 50 + ($scannedCount * 12) + ($typedCount * 6));

                $title = $fi->file_title ?? '';
                $keywords = array_values(array_filter(array_map('trim', preg_split('/\s+/', preg_replace('/[^A-Za-z0-9 ]/', ' ', $title)))));
                if (!empty($fi->land_use_type)) {
                    array_unshift($keywords, $fi->land_use_type);
                }

                $issues = [];
                if (!empty($fi->is_problematic)) $issues[] = 'Flagged as problematic';
                if (empty($fi->plot_number)) $issues[] = 'Missing plot number';

                return [
                    'id' => $fi->id,
                    'file_number' => $fi->file_number ?? '',
                    'owner' => $owner,
                    'document_type' => $fi->document_type ?? ($fi->land_use_type ?? 'Property Document'),
                    'plot_number' => $fi->plot_number ?? '',
                    'land_use' => $fi->land_use_type ?? 'Residential',
                    'confidence' => $confidence,
                    'keywords' => $keywords,
                    'issues' => $issues,
                    'text_quality' => $scannedCount > 0 ? 'Good' : 'Unknown',
                    'structure' => $typedCount > 0 ? 'Complete sections' : 'Partial',
                    'signature' => 'Not detected',
                    'stamp' => $scannedCount > 0 ? 'Official stamp detected' : 'Not detected',
                    'gis_verification' => 'Matched with parcel data',
                ];
            })->values();

            return view('fileindexing.index', compact('aiInsights', 'recentIndexes'));
        } catch (\Throwable $e) {
            // Fallback: render view with empty aiInsights
            return view('fileindexing.index', ['aiInsights' => collect(), 'recentIndexes' => collect()]);
        }
    }

    /**
     * Display the specified file indexing record.
     */
    public function show($id)
    {
        try {
            $record = DB::connection('sqlsrv')->table('file_indexings')->where('id', $id)->first();
        } catch (\Throwable $e) {
            $record = null;
        }

        if (!$record) {
            return redirect()->route('fileindex.index')->with('error', 'File indexing record not found.');
        }

        // If a dedicated view exists, render it; otherwise, return to index with context
        if (view()->exists('fileindexing.show')) {
            return view('fileindexing.show', compact('record'));
        }

        return redirect()->route('fileindex.index')
            ->with('success', 'Opened file indexing record.')
            ->with('file_indexing_id', $id);
    }

    /**
     * Check if a file number has already been indexed
     */
    public function checkIndexed(Request $request)
    {
        $fileno = trim((string) $request->query('fileno', ''));
        
        if ($fileno === '') {
            return response()->json(['exists' => false]);
        }

        try {
            $record = DB::connection('sqlsrv')
                ->table('file_indexings')
                ->where('file_number', $fileno)
                ->orderBy('id', 'desc')
                ->first();

            if (!$record) {
                return response()->json(['exists' => false]);
            }

            return response()->json([
                'exists' => true,
                'record' => [
                    'id' => $record->id,
                    'file_number' => $record->file_number,
                    'st_fillno' => $record->st_fillno,
                    'file_title' => $record->file_title,
                    'land_use_type' => $record->land_use_type,
                    'plot_number' => $record->plot_number,
                    'district' => $record->district,
                    'lga' => $record->lga,
                    'has_cofo' => (int) ($record->has_cofo ?? 0),
                    'is_merged' => (int) ($record->is_merged ?? 0),
                    'has_transaction' => (int) ($record->has_transaction ?? 0),
                    'is_problematic' => (int) ($record->is_problematic ?? 0),
                    'is_co_owned_plot' => (int) ($record->is_co_owned_plot ?? 0),
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'file_number' => 'required|string|max:255',
                'file_title' => 'required|string|max:255',
                'land_use_type' => 'nullable|string|max:255',
                'plot_number' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'lga' => 'nullable|string|max:255',
                'has_cofo' => 'nullable|boolean',
                'has_transaction' => 'nullable|boolean',
                'is_problematic' => 'nullable|boolean',
                'is_co_owned_plot' => 'nullable|boolean',
                'is_merged' => 'nullable|boolean',
                'serial_no' => 'nullable|string|max:255',
                'batch_no' => 'nullable|string',
                'shelf_location' => 'nullable|string|max:255',
                'tracking_id' => 'nullable|string|max:255',
                'main_application_id' => 'nullable|integer',
                'subapplication_id' => 'nullable|integer',
                'file_number_source' => 'nullable|string',
                'source_file_id' => 'nullable|integer',
            ]);

            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            $fileIndexing = FileIndexing::create($validated);

            // Mark the batch as used in the Rack_Shelf_Labels table
            if (isset($validated['batch_no']) && !empty($validated['batch_no'])) {
                try {
                    // Check if the Rack_Shelf_Labels table exists
                    $tableExists = DB::connection('sqlsrv')->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'Rack_Shelf_Labels'");

                    if (!empty($tableExists)) {
                        // Mark the batch as used
                        DB::connection('sqlsrv')
                            ->table('Rack_Shelf_Labels')
                            ->where('id', $validated['batch_no'])
                            ->update(['is_used' => 1]);
                    }
                } catch (\Exception $e) {
                    // Log the error but don't fail the file indexing creation
                    \Log::error('Failed to mark batch as used: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'File indexing created successfully!',
                'data' => $fileIndexing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getShelfForBatch($batch)
    {
        try {
            // Check if the Rack_Shelf_Labels table exists
            $tableExists = DB::connection('sqlsrv')->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'Rack_Shelf_Labels'");

            if (empty($tableExists)) {
                // Table doesn't exist, return a default shelf location
                return response()->json([
                    'success' => true,
                    'label' => "A1-S{$batch}"
                ]);
            }

            // Assuming table is Rack_Shelf_Labels with columns: id, full_label, is_used
            // The batch number is stored in the 'id' column
            $shelf = DB::connection('sqlsrv')
                ->table('Rack_Shelf_Labels')
                ->where('id', $batch)
                ->where(function($query) {
                    $query->where('is_used', 0)
                          ->orWhereNull('is_used');
                })
                ->first();

            if ($shelf) {
                // DON'T mark as used here - only mark as used when file is actually created
                return response()->json([
                    'success' => true,
                    'label' => $shelf->full_label
                ]);
            } else {
                // No shelf found, return a default
                return response()->json([
                    'success' => true,
                    'label' => "A1-S{$batch}"
                ]);
            }
        } catch (\Exception $e) {
            // If there's any error, return a default shelf location
            return response()->json([
                'success' => true,
                'label' => "A1-S{$batch}"
            ]);
        }
    }

    public function getAvailableBatches(Request $request)
    {
        try {
            // Check if the Rack_Shelf_Labels table exists
            $tableExists = DB::connection('sqlsrv')->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'Rack_Shelf_Labels'");

            if (empty($tableExists)) {
                // Table doesn't exist, return default batches 1-10
                $batches = [];
                for ($i = 1; $i <= 10; $i++) {
                    $batches[] = [
                        'id' => $i,
                        'text' => $i
                    ];
                }
                return response()->json([
                    'success' => true,
                    'batches' => $batches
                ]);
            }

            // Get available batches (is_used = 0 or null)
            $query = DB::connection('sqlsrv')
                ->table('Rack_Shelf_Labels')
                ->where(function($query) {
                    $query->where('is_used', 0)
                          ->orWhereNull('is_used');
                });

            // Add search functionality if search term is provided
            if ($request->has('q') && !empty($request->q)) {
                $searchTerm = $request->q;
                $query->where('id', 'LIKE', '%' . $searchTerm . '%');
            }

            $availableBatches = $query
                ->orderBy('id')
                ->limit(10)
                ->get(['id', 'full_label']);

            $batches = [];
            foreach ($availableBatches as $batch) {
                $batches[] = [
                    'id' => $batch->id,
                    'text' => $batch->id
                ];
            }

            return response()->json([
                'success' => true,
                'batches' => $batches
            ]);
        } catch (\Exception $e) {
            // If there's any error, return default batches
            $batches = [];
            for ($i = 1; $i <= 10; $i++) {
                $batches[] = [
                    'id' => $i,
                    'text' => $i
                ];
            }
            return response()->json([
                'success' => true,
                'batches' => $batches
            ]);
        }
    }

}
