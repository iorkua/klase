<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\FileIndexing;
use App\Models\Scanning;
use App\Models\PageTyping;
use Exception;

class Pagetypingcontroller extends Controller
{
    /**
     * Display the page typing dashboard or typing interface
     */
    public function index(Request $request)
    {
        try {
            $PageTitle = 'Page Typing';
            $PageDescription = 'Categorize and digitize file content';
            
            // Get file_indexing_id from request if provided
            $fileIndexingId = $request->get('file_indexing_id');
            
            if ($fileIndexingId) {
                // Load the page typing interface for specific file
                $selectedFileIndexing = FileIndexing::on('sqlsrv')
                    ->with(['mainApplication', 'scannings', 'pagetypings'])
                    ->find($fileIndexingId);
                
                if (!$selectedFileIndexing) {
                    return redirect()->route('pagetyping.index')
                        ->with('error', 'File not found');
                }
                
                // Check if file has scannings
                if ($selectedFileIndexing->scannings->isEmpty()) {
                    return redirect()->route('scanning.index', ['file_indexing_id' => $fileIndexingId])
                        ->with('error', 'Please upload scanned documents first before page typing');
                }
                
                // Return the page typing interface view
                $PageTitle = 'Page Typing - ' . $selectedFileIndexing->file_title;
                $PageDescription = 'Classify and label document pages';
                
                return view('pagetyping.typing', compact(
                    'PageTitle', 
                    'PageDescription', 
                    'selectedFileIndexing'
                ));
            }
            
            // Return the dashboard view (no file_indexing_id parameter)
            // Get statistics for dashboard
            $stats = [
                'pending_count' => $this->getPendingPageTypingCount(),
                'in_progress_count' => $this->getInProgressPageTypingCount(),
                'completed_count' => $this->getCompletedPageTypingCount(),
            ];
            
            // Get pending files (files with scannings but no page typings)
            $pendingFiles = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings'])
                ->whereHas('scannings')
                ->whereDoesntHave('pagetypings')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
            
            // Get in-progress files (files with some page typings but not all pages typed)
            $inProgressFiles = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->whereHas('pagetypings')
                ->whereHas('scannings', function ($query) {
                    $query->where('status', '!=', 'typed');
                })
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get();
            
            // Get completed files
            $completedFiles = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->whereHas('pagetypings')
                ->whereDoesntHave('scannings', function ($query) {
                    $query->where('status', '!=', 'typed');
                })
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get();
            
            return view('pagetyping.index', compact(
                'PageTitle', 
                'PageDescription', 
                'stats', 
                'pendingFiles',
                'inProgressFiles',
                'completedFiles'
            ));
        } catch (Exception $e) {
            Log::error('Error loading page typing dashboard', [
                'error' => $e->getMessage()
            ]);
            
            return view('pagetyping.index', [
                'PageTitle' => 'Page Typing',
                'PageDescription' => 'Categorize and digitize file content',
                'stats' => ['pending_count' => 0, 'in_progress_count' => 0, 'completed_count' => 0],
                'pendingFiles' => collect(),
                'inProgressFiles' => collect(),
                'completedFiles' => collect()
            ]);
        }
    }

    /**
     * Show the form for creating a new page typing
     */
    public function create(Request $request)
    {
        try {
            $fileIndexingId = $request->get('file_indexing_id');
            
            if (!$fileIndexingId) {
                return redirect()->route('pagetyping.index')
                    ->with('error', 'File indexing ID is required');
            }
            
            $fileIndexing = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings'])
                ->findOrFail($fileIndexingId);
            
            // Check if file has scannings
            if ($fileIndexing->scannings->isEmpty()) {
                return redirect()->route('scanning.index', ['file_indexing_id' => $fileIndexingId])
                    ->with('error', 'Please upload scanned documents first before page typing');
            }
            
            $PageTitle = 'Page Typing - ' . $fileIndexing->file_title;
            $PageDescription = 'Classify and label document pages';
            
            return view('pagetyping.create', compact('PageTitle', 'PageDescription', 'fileIndexing'));
        } catch (Exception $e) {
            Log::error('Error loading page typing create form', [
                'file_indexing_id' => $request->get('file_indexing_id'),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('pagetyping.index')
                ->with('error', 'Error loading page typing form: ' . $e->getMessage());
        }
    }

    /**
     * Store page typing data
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_indexing_id' => 'required|integer|exists:sqlsrv.file_indexings,id',
                'page_types' => 'required|array|min:1',
                'page_types.*.scanning_id' => 'required|integer|exists:sqlsrv.scannings,id',
                'page_types.*.page_number' => 'required|integer|min:1',
                'page_types.*.page_type' => 'required|string|max:100',
                'page_types.*.page_subtype' => 'nullable|string|max:100',
                'page_types.*.serial_number' => 'required|integer|min:1',
                'page_types.*.page_code' => 'nullable|string|max:100',
                'page_types.*.file_path' => 'required|string|max:255',
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

            $savedCount = 0;
            $errors = [];

            foreach ($request->page_types as $pageTypeData) {
                try {
                    // Check if page typing already exists for this specific page
                    $existingPageTyping = PageTyping::on('sqlsrv')
                        ->where('file_indexing_id', $fileIndexingId)
                        ->where('file_path', $pageTypeData['file_path'])
                        ->where('page_number', $pageTypeData['page_number'])
                        ->first();

                    if ($existingPageTyping) {
                        // Update existing record
                        $existingPageTyping->update([
                            'page_type' => $pageTypeData['page_type'],
                            'page_subtype' => $pageTypeData['page_subtype'],
                            'serial_number' => $pageTypeData['serial_number'],
                            'page_code' => $pageTypeData['page_code'],
                            'typed_by' => Auth::id(),
                        ]);
                    } else {
                        // Create new record
                        PageTyping::on('sqlsrv')->create([
                            'file_indexing_id' => $fileIndexingId,
                            'scanning_id' => $pageTypeData['scanning_id'],
                            'page_number' => $pageTypeData['page_number'],
                            'page_type' => $pageTypeData['page_type'],
                            'page_subtype' => $pageTypeData['page_subtype'],
                            'serial_number' => $pageTypeData['serial_number'],
                            'page_code' => $pageTypeData['page_code'],
                            'file_path' => $pageTypeData['file_path'],
                            'typed_by' => Auth::id(),
                        ]);
                    }

                    $savedCount++;
                } catch (Exception $e) {
                    $errors[] = "Error saving page {$pageTypeData['page_number']}: " . $e->getMessage();
                    Log::error('Error saving page typing', [
                        'file_indexing_id' => $fileIndexingId,
                        'page_data' => $pageTypeData,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update scanning status to typed if all pages are typed
            $this->updateScanningStatus($fileIndexingId);

            Log::info('Page typing completed', [
                'file_indexing_id' => $fileIndexingId,
                'pages_saved' => $savedCount,
                'errors_count' => count($errors),
                'typed_by' => Auth::id()
            ]);

            $response = [
                'success' => $savedCount > 0,
                'message' => "{$savedCount} pages classified successfully!",
                'saved_count' => $savedCount,
                'total_count' => count($request->page_types),
                'redirect' => route('pagetyping.index')
            ];

            if (count($errors) > 0) {
                $response['errors'] = $errors;
                $response['message'] .= " " . count($errors) . " errors occurred.";
            }

            return response()->json($response);

        } catch (Exception $e) {
            Log::error('Error storing page typing', [
                'error' => $e->getMessage(),
                'request_data' => $request->except('page_types')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving page typing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified page typing
     */
    public function show($id)
    {
        try {
            $pageTyping = PageTyping::on('sqlsrv')
                ->with(['fileIndexing', 'typedBy'])
                ->findOrFail($id);

            $PageTitle = 'Page Typing Details';
            $PageDescription = 'View page typing information';

            return view('pagetyping.show', compact('PageTitle', 'PageDescription', 'pageTyping'));
        } catch (Exception $e) {
            Log::error('Error loading page typing details', [
                'page_typing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('pagetyping.index')
                ->with('error', 'Page typing record not found');
        }
    }

    /**
     * Show the form for editing the specified page typing
     */
    public function edit($id)
    {
        try {
            $pageTyping = PageTyping::on('sqlsrv')
                ->with(['fileIndexing'])
                ->findOrFail($id);
            
            $PageTitle = 'Edit Page Typing';
            $PageDescription = 'Update page classification';

            return view('pagetyping.edit', compact('PageTitle', 'PageDescription', 'pageTyping'));
        } catch (Exception $e) {
            Log::error('Error loading page typing edit form', [
                'page_typing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('pagetyping.index')
                ->with('error', 'Page typing record not found');
        }
    }

    /**
     * Update the specified page typing
     */
    public function update(Request $request, $id)
    {
        try {
            $pageTyping = PageTyping::on('sqlsrv')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'page_type' => 'required|string|max:100',
                'page_subtype' => 'nullable|string|max:100',
                'serial_number' => 'required|integer|min:1',
                'page_code' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pageTyping->update(array_merge($validator->validated(), [
                'typed_by' => Auth::id()
            ]));

            Log::info('Page typing updated', [
                'page_typing_id' => $id,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Page typing updated successfully!'
            ]);

        } catch (Exception $e) {
            Log::error('Error updating page typing', [
                'page_typing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating page typing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified page typing
     */
    public function destroy($id)
    {
        try {
            $pageTyping = PageTyping::on('sqlsrv')->findOrFail($id);
            $fileIndexingId = $pageTyping->file_indexing_id;
            
            $pageTyping->delete();

            // Update scanning status
            $this->updateScanningStatus($fileIndexingId);

            Log::info('Page typing deleted', [
                'page_typing_id' => $id,
                'deleted_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Page typing deleted successfully!'
            ]);

        } catch (Exception $e) {
            Log::error('Error deleting page typing', [
                'page_typing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting page typing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save single page typing (AJAX)
     */
    public function saveSingle(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_indexing_id' => 'required|integer|exists:sqlsrv.file_indexings,id',
                'scanning_id' => 'required|integer|exists:sqlsrv.scannings,id',
                'page_number' => 'required|integer|min:1',
                'page_type' => 'required|string|max:100',
                'page_subtype' => 'nullable|string|max:100',
                'serial_number' => 'required|integer|min:1',
                'page_code' => 'nullable|string|max:100',
                'file_path' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Check if page typing already exists
            $existingPageTyping = PageTyping::on('sqlsrv')
                ->where('file_indexing_id', $validated['file_indexing_id'])
                ->where('file_path', $validated['file_path'])
                ->where('page_number', $validated['page_number'])
                ->first();

            if ($existingPageTyping) {
                // Update existing record
                $existingPageTyping->update([
                    'page_type' => $validated['page_type'],
                    'page_subtype' => $validated['page_subtype'],
                    'serial_number' => $validated['serial_number'],
                    'page_code' => $validated['page_code'],
                    'typed_by' => Auth::id(),
                ]);
                $pageTyping = $existingPageTyping;
            } else {
                // Create new record
                $pageTyping = PageTyping::on('sqlsrv')->create(array_merge($validated, [
                    'typed_by' => Auth::id()
                ]));
            }

            Log::info('Single page typing saved', [
                'page_typing_id' => $pageTyping->id,
                'file_indexing_id' => $validated['file_indexing_id'],
                'page_number' => $validated['page_number'],
                'typed_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Page classification saved successfully!',
                'page_typing_id' => $pageTyping->id
            ]);

        } catch (Exception $e) {
            Log::error('Error saving single page typing', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving page classification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get page typings for a file indexing (AJAX)
     */
    public function getPageTypings(Request $request)
    {
        try {
            $fileIndexingId = $request->get('file_indexing_id');
            $search = $request->get('search', '');

            if (!$fileIndexingId) {
                return response()->json([
                    'success' => false,
                    'message' => 'File indexing ID is required'
                ], 400);
            }

            $query = PageTyping::on('sqlsrv')
                ->with(['fileIndexing', 'typedBy'])
                ->where('file_indexing_id', $fileIndexingId);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('page_type', 'like', "%{$search}%")
                        ->orWhere('page_subtype', 'like', "%{$search}%")
                        ->orWhere('page_code', 'like', "%{$search}%");
                });
            }

            $pageTypings = $query->orderBy('serial_number')
                ->orderBy('page_number')
                ->limit(100)
                ->get();

            return response()->json([
                'success' => true,
                'page_typings' => $pageTypings->map(function ($pt) {
                    return [
                        'id' => $pt->id,
                        'page_number' => $pt->page_number,
                        'page_type' => $pt->page_type,
                        'page_subtype' => $pt->page_subtype,
                        'serial_number' => $pt->serial_number,
                        'page_code' => $pt->page_code,
                        'file_path' => $pt->file_path,
                        'scanning_id' => $pt->scanning_id,
                        'file_indexing' => $pt->fileIndexing ? [
                            'id' => $pt->fileIndexing->id,
                            'file_number' => $pt->fileIndexing->file_number,
                            'file_title' => $pt->fileIndexing->file_title,
                        ] : null,
                        'typed_by' => $pt->typedBy ? $pt->typedBy->name : 'Unknown',
                        'created_at' => $pt->created_at ? $pt->created_at->format('M d, Y H:i') : 'Unknown',
                    ];
                })
            ]);

        } catch (Exception $e) {
            Log::error('Error getting page typings', [
                'file_indexing_id' => $fileIndexingId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading page typings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending page typing count
     */
    private function getPendingPageTypingCount()
    {
        try {
            return FileIndexing::on('sqlsrv')
                ->whereHas('scannings')
                ->whereDoesntHave('pagetypings')
                ->count();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get in-progress page typing count
     */
    private function getInProgressPageTypingCount()
    {
        try {
            return FileIndexing::on('sqlsrv')
                ->whereHas('pagetypings')
                ->whereHas('scannings', function ($query) {
                    $query->where('status', '!=', 'typed');
                })
                ->count();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get completed page typing count
     */
    private function getCompletedPageTypingCount()
    {
        try {
            return FileIndexing::on('sqlsrv')
                ->whereHas('pagetypings')
                ->whereDoesntHave('scannings', function ($query) {
                    $query->where('status', '!=', 'typed');
                })
                ->count();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Update scanning status based on page typing completion
     */
    private function updateScanningStatus($fileIndexingId)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')
                ->with(['scannings', 'pagetypings'])
                ->find($fileIndexingId);

            if (!$fileIndexing) {
                return;
            }

            // Check if all scanned documents have page typings
            $allTyped = true;
            foreach ($fileIndexing->scannings as $scanning) {
                $hasPageTyping = $fileIndexing->pagetypings()
                    ->where('file_path', $scanning->document_path)
                    ->exists();
                
                if (!$hasPageTyping) {
                    $allTyped = false;
                    break;
                }
            }

            // Update scanning status
            $status = $allTyped ? 'typed' : 'pending';
            Scanning::on('sqlsrv')
                ->where('file_indexing_id', $fileIndexingId)
                ->update(['status' => $status]);

        } catch (Exception $e) {
            Log::error('Error updating scanning status', [
                'file_indexing_id' => $fileIndexingId,
                'error' => $e->getMessage()
            ]);
        }
    }
}