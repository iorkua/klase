<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlindScanning;
use App\Models\FileIndexing;
use App\Models\FileTracking;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BlindScanningController extends Controller
{
    /**
     * Display the blind scanning interface
     */
    public function index()
    {
           $PageTitle = 'Blind Scans';
            $PageDescription = ''; 

        try {
            // Get statistics
            $stats = [
                'total_blind_scans' => BlindScanning::count(),
                'pending_scans' => BlindScanning::where('status', BlindScanning::STATUS_PENDING)->count(),
                'converted_scans' => BlindScanning::where('status', BlindScanning::STATUS_CONVERTED)->count(),
                'today_uploads' => BlindScanning::whereDate('created_at', today())->count(),
            ];

            // Get recent blind scans


            $recentScans = BlindScanning::with(['uploader'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('scanning.blind_scans', compact('stats', 'recentScans', 'PageTitle', 'PageDescription'));
        } catch (\Exception $e) {
            Log::error('Error loading blind scanning interface', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return back()->with('error', 'Failed to load blind scanning interface: ' . $e->getMessage());
        }
    }

    /**
     * Store uploaded blind scans
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,tiff|max:10240', // 10MB max
            'paper_size' => 'nullable|string|in:A4,A5,A3,Letter,Legal,Custom',
            'document_type' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            $uploadedFiles = [];
            $files = $request->file('files');

            foreach ($files as $file) {
                // Generate unique temp file ID
                $tempFileId = BlindScanning::generateTempFileId();
                
                // Store file
                $filename = $tempFileId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('blind_scans', $filename, 'public');

                // Create blind scanning record
                $blindScan = BlindScanning::create([
                    'temp_file_id' => $tempFileId,
                    'original_filename' => $file->getClientOriginalName(),
                    'document_path' => $path,
                    'paper_size' => $request->paper_size,
                    'document_type' => $request->document_type,
                    'notes' => $request->notes,
                    'status' => BlindScanning::STATUS_PENDING,
                    'uploaded_by' => auth()->id(),
                ]);

                $uploadedFiles[] = [
                    'id' => $blindScan->id,
                    'temp_file_id' => $tempFileId,
                    'filename' => $file->getClientOriginalName(),
                    'size' => $blindScan->file_size,
                    'url' => $blindScan->file_url,
                ];

                // Log activity
                UserActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'blind_scan_upload',
                    'description' => "Uploaded blind scan: {$file->getClientOriginalName()}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'additional_info' => json_encode([
                        'temp_file_id' => $tempFileId,
                        'file_size' => filesize($blindScan->full_path),
                        'document_type' => $request->document_type,
                    ])
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
                'files' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error uploading blind scans', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->except('files')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List blind scans with pagination and filtering
     */
    public function list(Request $request)
    {
        try {
            $query = BlindScanning::with(['uploader', 'fileIndexing']);

            // Apply filters
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('temp_file_id', 'like', "%{$search}%")
                      ->orWhere('original_filename', 'like', "%{$search}%")
                      ->orWhere('document_type', 'like', "%{$search}%");
                });
            }

            if ($request->has('date_from') && $request->date_from !== '') {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to !== '') {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->get('per_page', 15);
            $blindScans = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $blindScans->items(),
                'pagination' => [
                    'current_page' => $blindScans->currentPage(),
                    'last_page' => $blindScans->lastPage(),
                    'per_page' => $blindScans->perPage(),
                    'total' => $blindScans->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error listing blind scans', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load blind scans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert blind scan to upload workflow
     */
    public function convertToUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blind_scan_id' => 'required|exists:blind_scannings,id',
            'file_indexing_id' => 'required|exists:file_indexings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $blindScan = BlindScanning::findOrFail($request->blind_scan_id);
            $fileIndexing = FileIndexing::findOrFail($request->file_indexing_id);

            // Check if blind scan is still pending
            if ($blindScan->status !== BlindScanning::STATUS_PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blind scan has already been processed'
                ], 400);
            }

            // Convert to regular scanning
            $scanning = $blindScan->convertToUpload($request->file_indexing_id);

            // Update file indexing workflow status
            $fileIndexing->update([
                'workflow_status' => 'uploaded',
                'is_updated' => true,
            ]);

            // Update file tracking if exists
            $tracking = FileTracking::where('file_indexing_id', $fileIndexing->id)->first();
            if ($tracking) {
                $tracking->updateStatus('uploaded', 'Document uploaded from blind scan');
                $tracking->addMovementEntry([
                    'action' => 'blind_scan_converted',
                    'from_temp_id' => $blindScan->temp_file_id,
                    'to_file_number' => $fileIndexing->file_number,
                    'scanning_id' => $scanning->id,
                ]);
            }

            // Log activity
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'blind_scan_converted',
                'description' => "Converted blind scan {$blindScan->temp_file_id} to file {$fileIndexing->file_number}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'additional_info' => json_encode([
                    'blind_scan_id' => $blindScan->id,
                    'file_indexing_id' => $fileIndexing->id,
                    'scanning_id' => $scanning->id,
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Blind scan successfully converted to upload workflow',
                'data' => [
                    'scanning_id' => $scanning->id,
                    'file_number' => $fileIndexing->file_number,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error converting blind scan', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'blind_scan_id' => $request->blind_scan_id,
                'file_indexing_id' => $request->file_indexing_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Conversion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a blind scan
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $blindScan = BlindScanning::findOrFail($id);

            // Check if user can delete (only uploader or admin)
            if ($blindScan->uploaded_by !== auth()->id() && !auth()->user()->hasRole('Admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this scan'
                ], 403);
            }

            // Delete file from storage
            if (Storage::disk('public')->exists($blindScan->document_path)) {
                Storage::disk('public')->delete($blindScan->document_path);
            }

            // Log activity before deletion
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'blind_scan_deleted',
                'description' => "Deleted blind scan: {$blindScan->temp_file_id}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'additional_info' => json_encode([
                    'temp_file_id' => $blindScan->temp_file_id,
                    'original_filename' => $blindScan->original_filename,
                ])
            ]);

            // Delete record
            $blindScan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Blind scan deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting blind scan', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'blind_scan_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create folder structure for blind scanning
     */
    public function createFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_no' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fileNo = $request->file_no;
            $basePath = "EDMS/BLIND_SCAN/{$fileNo}";
            
            // Define local path for client-side creation
            $localPath = "EDMS/BLIND_SCAN/{$fileNo}";

            // Check if folder already exists in storage
            if (Storage::disk('public')->exists($basePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder already exists for this File Number'
                ], 409);
            }

            // Create directories in server storage only
            $a4Created = Storage::disk('public')->makeDirectory("{$basePath}/A4");
            $a3Created = Storage::disk('public')->makeDirectory("{$basePath}/A3");
            
            // Define local paths for client instructions
            $localA4Path = "{$localPath}\\A4";
            $localA3Path = "{$localPath}\\A3";

            if ($a4Created && $a3Created) {
                // Create a blind scanning record
                BlindScanning::create([
                    'temp_file_id' => 'FOLDER_' . time() . '_' . str_replace(['/', '\\', ' ', '-'], '_', $fileNo),
                    'original_filename' => $fileNo,
                    'document_path' => $basePath,
                    'paper_size' => 'FOLDER',
                    'document_type' => 'BLIND_SCAN_FOLDER',
                    'notes' => 'Auto-created folders for blind scanning: Storage=' . $basePath . ', LocalInstructions=' . $localPath,
                    'status' => BlindScanning::STATUS_PENDING,
                    'uploaded_by' => auth()->id(),
                ]);

                // Log the folder creation
                Log::info('Blind scan server folders created', [
                    'file_no' => $fileNo,
                    'user_id' => auth()->id(),
                    'storage_path' => $basePath,
                    'local_instructions' => $localPath
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Server folders created successfully for FileNo: {$fileNo}. Please create local folder structure manually.",
                    'data' => [
                        'file_no' => $fileNo,
                        'storage_path' => storage_path("app/public/{$basePath}"),
                        'storage_a4_path' => storage_path("app/public/{$basePath}/A4"),
                        'storage_a3_path' => storage_path("app/public/{$basePath}/A3"),
                        'local_path' => $localPath,
                        'local_a4_path' => $localA4Path,
                        'local_a3_path' => $localA3Path,
                        'create_local_instructions' => true
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create server folders'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error creating blind scan folder', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'file_no' => $request->file_no ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create folder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get blind scan details
     */
    public function show($id)
    {
        try {
            $blindScan = BlindScanning::with(['uploader', 'fileIndexing'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $blindScan
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting blind scan details', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'blind_scan_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load blind scan details: ' . $e->getMessage()
            ], 500);
        }
    }
}