<?php

namespace App\Http\Controllers;

use App\Models\FileIndexing;
use App\Models\PrintLabelBatch;
use App\Models\PrintLabelBatchItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrintLabelController extends Controller
{ 
    public function index() {
        $PageTitle = 'Print File Labels';
        $PageDescription = 'Generate and print labels for physical files';
        
        // Get statistics with error handling
        try {
            $availableFilesCount = FileIndexing::whereNotNull('batch_no')
                ->whereDoesntHave('printLabelBatchItems')
                ->count();
        } catch (\Exception $e) {
            // Fallback if tables don't exist yet or relationship issues
            Log::warning('Error counting available files for print labels', ['error' => $e->getMessage()]);
            $availableFilesCount = 0;
        }
        
        try {
            $recentBatches = PrintLabelBatch::with(['batchItems', 'creator'])
                ->recent(30)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // Fallback if tables don't exist yet
            Log::warning('Error fetching recent batches', ['error' => $e->getMessage()]);
            $recentBatches = collect();
        }
        
        Log::info('Print Label page accessed', ['user_id' => auth()->id()]);
        return view('printlabel.index', compact(
            'PageTitle', 
            'PageDescription', 
            'availableFilesCount',
            'recentBatches'
        ));
    }

    /**
     * Get available files for label printing
     */
    public function getAvailableFiles(Request $request)
    {
        try {
            $query = FileIndexing::whereNull('batch_no')
                ->whereDoesntHave('printLabelBatchItems');

            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('file_number', 'like', "%{$search}%")
                      ->orWhere('file_title', 'like', "%{$search}%")
                      ->orWhere('plot_number', 'like', "%{$search}%")
                      ->orWhere('district', 'like', "%{$search}%")
                      ->orWhere('lga', 'like', "%{$search}%");
                });
            }

            // Apply pagination
            $perPage = $request->get('per_page', 50);
            $files = $query->orderBy('file_number')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $files->items(),
                'pagination' => [
                    'current_page' => $files->currentPage(),
                    'last_page' => $files->lastPage(),
                    'per_page' => $files->perPage(),
                    'total' => $files->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching available files', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching files: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new label batch
     */
    public function createBatch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_ids' => 'required|array|min:1|max:30',
                'file_ids.*' => 'exists:file_indexings,id',
                'label_format' => 'required|in:standard,compact,qr_code,30-in-1',
                'orientation' => 'required|in:portrait,landscape',
                'batch_size' => 'integer|min:1|max:30'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Create the batch
            $batch = PrintLabelBatch::create([
                'batch_number' => PrintLabelBatch::generateBatchNumber(),
                'batch_size' => $request->get('batch_size', 30),
                'label_format' => $request->label_format,
                'orientation' => $request->orientation,
                'status' => PrintLabelBatch::STATUS_PENDING,
                'created_by' => auth()->id(),
            ]);

            // Get the files and create batch items
            $files = FileIndexing::whereIn('id', $request->file_ids)->get();
            
            foreach ($files as $index => $file) {
                PrintLabelBatchItem::create([
                    'batch_id' => $batch->id,
                    'file_indexing_id' => $file->id,
                    'file_number' => $file->file_number,
                    'file_title' => $file->file_title,
                    'plot_number' => $file->plot_number,
                    'district' => $file->district,
                    'lga' => $file->lga,
                    'land_use_type' => $file->land_use_type,
                    'shelf_location' => $file->shelf_location,
                    'label_position' => $index + 1,
                ]);
            }

            // Update batch status
            $batch->update(['status' => PrintLabelBatch::STATUS_GENERATED]);

            DB::commit();

            Log::info('Label batch created successfully', [
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'file_count' => count($files),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Label batch created successfully',
                'data' => [
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'file_count' => count($files)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating label batch', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get generated batches
     */
    public function getGeneratedBatches(Request $request)
    {
        try {
            $query = PrintLabelBatch::with(['batchItems', 'creator'])
                ->where('status', '!=', PrintLabelBatch::STATUS_PENDING);

            // Apply filters
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Apply pagination
            $perPage = $request->get('per_page', 20);
            $batches = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $batches->items(),
                'pagination' => [
                    'current_page' => $batches->currentPage(),
                    'last_page' => $batches->lastPage(),
                    'per_page' => $batches->perPage(),
                    'total' => $batches->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching generated batches', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching batches: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get batch details with items
     */
    public function getBatchDetails($batchId)
    {
        try {
            $batch = PrintLabelBatch::with(['batchItems.fileIndexing', 'creator'])
                ->findOrFail($batchId);

            return response()->json([
                'success' => true,
                'data' => $batch
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching batch details', [
                'batch_id' => $batchId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching batch details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark batch as printed
     */
    public function markBatchAsPrinted($batchId)
    {
        try {
            $batch = PrintLabelBatch::findOrFail($batchId);
            $batch->markAsPrinted();

            // Mark all items as printed
            $batch->batchItems()->update([
                'is_printed' => true,
                'printed_at' => now()
            ]);

            Log::info('Batch marked as printed', [
                'batch_id' => $batchId,
                'batch_number' => $batch->batch_number,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Batch marked as printed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking batch as printed', [
                'batch_id' => $batchId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error marking batch as printed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a batch
     */
    public function deleteBatch($batchId)
    {
        try {
            $batch = PrintLabelBatch::findOrFail($batchId);
            
            // Only allow deletion of pending or generated batches
            if (in_array($batch->status, [PrintLabelBatch::STATUS_PRINTED, PrintLabelBatch::STATUS_COMPLETED])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete printed or completed batches'
                ], 400);
            }

            $batchNumber = $batch->batch_number;
            $batch->delete();

            Log::info('Batch deleted', [
                'batch_id' => $batchId,
                'batch_number' => $batchNumber,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Batch deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting batch', [
                'batch_id' => $batchId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get print statistics
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'available_files' => FileIndexing::whereNotNull('batch_no')
                    ->whereDoesntHave('printLabelBatchItems')
                    ->count(),
                'total_batches' => PrintLabelBatch::count(),
                'pending_batches' => PrintLabelBatch::where('status', PrintLabelBatch::STATUS_PENDING)->count(),
                'generated_batches' => PrintLabelBatch::where('status', PrintLabelBatch::STATUS_GENERATED)->count(),
                'printed_batches' => PrintLabelBatch::where('status', PrintLabelBatch::STATUS_PRINTED)->count(),
                'completed_batches' => PrintLabelBatch::where('status', PrintLabelBatch::STATUS_COMPLETED)->count(),
                'total_labels_printed' => PrintLabelBatchItem::where('is_printed', true)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching statistics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
