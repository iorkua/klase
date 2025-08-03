<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\FileIndexing;
use App\Models\Scanning;
use Exception;

class ScanningController extends Controller
{ 
    /**
     * Display the scanning dashboard
     */
    public function index(Request $request) 
    {
        try {
            $PageTitle = 'Document Upload';
            $PageDescription = 'Upload scanned documents to their digital folders';
            
            // Get file_indexing_id from request if provided
            $fileIndexingId = $request->get('file_indexing_id');
            $selectedFileIndexing = null;
            
            if ($fileIndexingId) {
                $selectedFileIndexing = FileIndexing::on('sqlsrv')
                    ->with(['mainApplication', 'scannings'])
                    ->find($fileIndexingId);
            }
            
            // Get statistics for dashboard
            $stats = [
                'uploads_today' => $this->getUploadsTodayCount(),
                'pending_page_typing' => $this->getPendingPageTypingCount(),
                'total_scanned' => Scanning::on('sqlsrv')->count(),
            ];
            
            // Get recent scanning records
            $recentScans = Scanning::on('sqlsrv')
                ->with(['fileIndexing'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            return view('scanning.index', compact(
                'PageTitle', 
                'PageDescription', 
                'stats', 
                'recentScans', 
                'selectedFileIndexing'
            ));
        } catch (Exception $e) {
            Log::error('Error loading scanning dashboard', [
                'error' => $e->getMessage()
            ]);
            
            return view('scanning.index', [
                'PageTitle' => 'Document Upload',
                'PageDescription' => 'Upload scanned documents to their digital folders',
                'stats' => ['uploads_today' => 0, 'pending_page_typing' => 0, 'total_scanned' => 0],
                'recentScans' => collect(),
                'selectedFileIndexing' => null
            ]);
        }
    }

    /**
     * Upload scanned documents
     */
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_indexing_id' => 'required|integer|exists:sqlsrv.file_indexings,id',
                'documents' => 'required|array|min:1',
                'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png,tiff|max:20480', // 20MB max
                'custom_names' => 'nullable|array',
                'custom_names.*' => 'nullable|string|max:255',
                'paper_sizes' => 'nullable|array',
                'paper_sizes.*' => 'nullable|string|max:20',
                'document_types' => 'nullable|array',
                'document_types.*' => 'nullable|string|max:100',
                'notes' => 'nullable|array',
                'notes.*' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fileIndexingId = $request->file_indexing_id;
            $fileIndexing = FileIndexing::on('sqlsrv')->findOrFail($fileIndexingId);
            $customNames = $request->input('custom_names', []);
            $paperSizes = $request->input('paper_sizes', []);
            $documentTypes = $request->input('document_types', []);
            $notes = $request->input('notes', []);

            $uploadedDocuments = [];
            $errors = [];

            foreach ($request->file('documents') as $index => $document) {
                try {
                    // Use custom name if provided, otherwise use original name
                    $customName = $customNames[$index] ?? null;
                    $originalName = $customName ?: $document->getClientOriginalName();
                    
                    // Get paper size, document type, and notes from form or use defaults
                    $paperSize = $paperSizes[$index] ?? $this->detectPaperSize($document);
                    $documentType = $documentTypes[$index] ?? $this->detectDocumentType($originalName);
                    $documentNotes = $notes[$index] ?? null;
                    
                    // Generate unique filename for storage
                    $extension = $document->getClientOriginalExtension();
                    $filename = time() . '_' . $index . '_' . uniqid() . '.' . $extension;
                    
                    // Store file
                    $path = $document->storeAs(
                        'scanned_documents/' . $fileIndexingId, 
                        $filename, 
                        'public'
                    );

                    // Create scanning record
                    $scanning = Scanning::on('sqlsrv')->create([
                        'file_indexing_id' => $fileIndexingId,
                        'document_path' => $path,
                        'original_filename' => $originalName, // Use custom name as original filename
                        'paper_size' => $paperSize,
                        'document_type' => $documentType,
                        'uploaded_by' => Auth::id(),
                        'status' => 'pending',
                        'notes' => $documentNotes,
                    ]);

                    $uploadedDocuments[] = [
                        'id' => $scanning->id,
                        'filename' => $originalName,
                        'path' => $path,
                        'size' => $document->getSize(),
                        'type' => $extension,
                        'paper_size' => $paperSize,
                        'document_type' => $documentType,
                        'notes' => $documentNotes,
                    ];

                } catch (Exception $e) {
                    $errors[] = "Error uploading {$originalName}: " . $e->getMessage();
                    Log::error('Error uploading document', [
                        'file_indexing_id' => $fileIndexingId,
                        'filename' => $originalName ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Documents uploaded', [
                'file_indexing_id' => $fileIndexingId,
                'successful_uploads' => count($uploadedDocuments),
                'errors' => count($errors),
                'uploaded_by' => Auth::id()
            ]);

            $response = [
                'success' => count($uploadedDocuments) > 0,
                'message' => count($uploadedDocuments) . ' documents uploaded successfully!',
                'uploaded_documents' => $uploadedDocuments,
                'redirect' => route('pagetyping.index', ['file_indexing_id' => $fileIndexingId])
            ];

            if (count($errors) > 0) {
                $response['errors'] = $errors;
                $response['message'] .= ' ' . count($errors) . ' uploads failed.';
            }

            return response()->json($response);

        } catch (Exception $e) {
            Log::error('Error in document upload', [
                'error' => $e->getMessage(),
                'request_data' => $request->except('documents')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error uploading documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View a specific scanned document
     */
    public function view($id)
    {
        try {
            $scanning = Scanning::on('sqlsrv')
                ->with(['fileIndexing'])
                ->findOrFail($id);

            $PageTitle = 'View Scanned Document';
            $PageDescription = 'Preview and manage scanned document';

            return view('scanning.view', compact('PageTitle', 'PageDescription', 'scanning'));
        } catch (Exception $e) {
            Log::error('Error loading scanned document', [
                'scanning_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('scanning.index')
                ->with('error', 'Scanned document not found');
        }
    }

    /**
     * Get document details for editing
     */
    public function details($id)
    {
        try {
            $scanning = Scanning::on('sqlsrv')
                ->with(['fileIndexing'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'document' => [
                    'id' => $scanning->id,
                    'original_filename' => $scanning->original_filename,
                    'paper_size' => $scanning->paper_size,
                    'document_type' => $scanning->document_type,
                    'notes' => $scanning->notes,
                    'status' => $scanning->status,
                    'file_indexing' => [
                        'id' => $scanning->fileIndexing->id,
                        'file_number' => $scanning->fileIndexing->file_number,
                        'file_title' => $scanning->fileIndexing->file_title,
                    ],
                    'uploaded_at' => $scanning->created_at->format('M d, Y H:i'),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error getting document details', [
                'scanning_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }
    }

    /**
     * Delete a scanned document
     */
    public function delete($id)
    {
        try {
            $scanning = Scanning::on('sqlsrv')->findOrFail($id);
            
            // Check if document has page typings
            if ($scanning->fileIndexing->pagetypings()->where('file_path', $scanning->document_path)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete document that has been page typed'
                ], 409);
            }

            // Delete file from storage
            if (Storage::disk('public')->exists($scanning->document_path)) {
                Storage::disk('public')->delete($scanning->document_path);
            }

            // Delete database record
            $scanning->delete();

            Log::info('Scanned document deleted', [
                'scanning_id' => $id,
                'deleted_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully!'
            ]);

        } catch (Exception $e) {
            Log::error('Error deleting scanned document', [
                'scanning_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update document details
     */
    public function updateDetails(Request $request, $id)
    {
        try {
            $scanning = Scanning::on('sqlsrv')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'original_filename' => 'nullable|string|max:255',
                'paper_size' => 'nullable|string|max:20',
                'document_type' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $scanning->update($validator->validated());

            Log::info('Document details updated', [
                'scanning_id' => $id,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document details updated successfully!'
            ]);

        } catch (Exception $e) {
            Log::error('Error updating document details', [
                'scanning_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating document details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get scanned files list for a file indexing (AJAX)
     */
    public function getScannedFiles(Request $request)
    {
        try {
            $fileIndexingId = $request->get('file_indexing_id');
            $search = $request->get('search', '');

            $query = Scanning::on('sqlsrv')
                ->with(['fileIndexing']);

            if ($fileIndexingId) {
                $query->where('file_indexing_id', $fileIndexingId);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('original_filename', 'like', "%{$search}%")
                        ->orWhere('document_type', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            }

            $scannedFiles = $query->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();

            return response()->json([
                'success' => true,
                'scanned_files' => $scannedFiles->map(function ($scan) {
                    return [
                        'id' => $scan->id,
                        'filename' => $scan->original_filename,
                        'document_path' => $scan->document_path,
                        'paper_size' => $scan->paper_size,
                        'document_type' => $scan->document_type,
                        'status' => $scan->status,
                        'notes' => $scan->notes,
                        'file_indexing' => [
                            'id' => $scan->fileIndexing->id,
                            'file_number' => $scan->fileIndexing->file_number,
                            'file_title' => $scan->fileIndexing->file_title,
                        ],
                        'uploaded_at' => $scan->created_at->format('M d, Y H:i'),
                        'file_url' => url('storage/app/public/' . $scan->document_path),
                    ];
                })
            ]);

        } catch (Exception $e) {
            Log::error('Error getting scanned files', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading scanned files'
            ], 500);
        }
    }

    /**
     * Get uploads today count
     */
    private function getUploadsTodayCount()
    {
        try {
            return Scanning::on('sqlsrv')
                ->whereDate('created_at', today())
                ->count();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get pending page typing count
     */
    private function getPendingPageTypingCount()
    {
        try {
            return Scanning::on('sqlsrv')
                ->whereDoesntHave('fileIndexing.pagetypings')
                ->count();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Detect paper size from uploaded file
     */
    private function detectPaperSize($file)
    {
        // Basic paper size detection logic
        // This can be enhanced with actual image analysis
        $size = $file->getSize();
        
        if ($size > 5000000) { // > 5MB, likely A3 or larger
            return 'A3';
        } elseif ($size > 1000000) { // > 1MB, likely A4
            return 'A4';
        } else {
            return 'A5';
        }
    }

    /**
     * Detect document type from filename
     */
    private function detectDocumentType($filename)
    {
        $filename = strtolower($filename);
        
        if (strpos($filename, 'certificate') !== false || strpos($filename, 'cert') !== false) {
            return 'Certificate';
        } elseif (strpos($filename, 'deed') !== false) {
            return 'Deed';
        } elseif (strpos($filename, 'letter') !== false) {
            return 'Letter';
        } elseif (strpos($filename, 'application') !== false || strpos($filename, 'app') !== false) {
            return 'Application Form';
        } elseif (strpos($filename, 'map') !== false) {
            return 'Map';
        } elseif (strpos($filename, 'survey') !== false || strpos($filename, 'plan') !== false) {
            return 'Survey Plan';
        } elseif (strpos($filename, 'receipt') !== false) {
            return 'Receipt';
        } else {
            return 'Other';
        }
    }
}