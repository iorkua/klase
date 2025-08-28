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

class PageTypingController extends Controller
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
            $selectedFileIndexing = null;
            
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
                
                // Update the page title to reflect we're in typing mode
                $PageTitle = 'Page Typing - ' . $selectedFileIndexing->file_title;
                $PageDescription = 'Classify and label document pages';
            }
            
            // Load dashboard data
            $stats = [
                'pending_count' => $this->getPendingPageTypingCount(),
                'in_progress_count' => $this->getInProgressPageTypingCount(),
                'completed_count' => $this->getCompletedPageTypingCount(),
            ];

            // Load pending files (files with scannings but no page typings)
            $pendingFiles = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings'])
                ->whereHas('scannings')
                ->whereDoesntHave('pagetypings')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            // Load in-progress files (files with some page typings but not all pages typed)
            $inProgressFiles = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->whereHas('pagetypings')
                ->whereHas('scannings', function ($query) {
                    $query->where('status', '!=', 'typed');
                })
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get();

            // Load completed files (files where all pages are typed)
            $completedFiles = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings.typedBy'])
                ->whereHas('pagetypings')
                ->whereDoesntHave('scannings', function ($query) {
                    $query->where('status', '!=', 'typed');
                })
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get();

            // Return the dashboard view with selectedFileIndexing if provided
            return view('pagetyping.index', compact(
                'PageTitle', 
                'PageDescription', 
                'stats', 
                'pendingFiles', 
                'inProgressFiles', 
                'completedFiles',
                'selectedFileIndexing'
            ));
        } catch (Exception $e) {
            Log::error('Error loading page typing dashboard', [
                'error' => $e->getMessage()
            ]);
            
            return view('pagetyping.index', [
                'PageTitle' => 'Page Typing',
                'PageDescription' => 'Categorize and digitize file content',
                'selectedFileIndexing' => null
            ]);
        }
    }

    /**
     * Get dashboard statistics (AJAX)
     */
    public function getStats(Request $request)
    {
        try {
            $stats = [
                'pending_count' => $this->getPendingPageTypingCount(),
                'in_progress_count' => $this->getInProgressPageTypingCount(),
                'completed_count' => $this->getCompletedPageTypingCount(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            Log::error('Error getting page typing stats', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics',
                'stats' => ['pending_count' => 0, 'in_progress_count' => 0, 'completed_count' => 0]
            ], 500);
        }
    }

    /**
     * Get files by status (AJAX)
     */
    public function getFilesByStatus(Request $request)
    {
        try {
            $status = $request->get('status', 'pending');
            $search = $request->get('search', '');
            $limit = $request->get('limit', 20);

            $query = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings']);

            // Apply status filters
            switch ($status) {
                case 'pending':
                    $query->whereHas('scannings')
                          ->whereDoesntHave('pagetypings');
                    break;
                case 'in_progress':
                    $query->whereHas('pagetypings')
                          ->whereHas('scannings', function ($q) {
                              $q->where('status', '!=', 'typed');
                          });
                    break;
                case 'completed':
                    $query->whereHas('pagetypings')
                          ->whereDoesntHave('scannings', function ($q) {
                              $q->where('status', '!=', 'typed');
                          });
                    break;
            }

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('file_number', 'like', "%{$search}%")
                      ->orWhere('file_title', 'like', "%{$search}%")
                      ->orWhere('district', 'like', "%{$search}%")
                      ->orWhere('lga', 'like', "%{$search}%");
                });
            }

            $files = $query->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();

            $formattedFiles = $files->map(function ($file) {
                $scanningsCount = $file->scannings->count();
                $pageTypingsCount = $file->pagetypings->count();
                
                // Calculate progress for in-progress files
                $progress = $scanningsCount > 0 ? ($pageTypingsCount / $scanningsCount) * 100 : 0;

                return [
                    'id' => $file->id,
                    'file_number' => $file->file_number,
                    'file_title' => $file->file_title,
                    'district' => $file->district,
                    'lga' => $file->lga,
                    'scannings_count' => $scanningsCount,
                    'page_typings_count' => $pageTypingsCount,
                    'progress' => round($progress, 1),
                    'created_at' => $file->created_at ? $file->created_at->format('M d, Y') : 'Unknown',
                    'updated_at' => $file->updated_at ? $file->updated_at->format('M d, Y H:i') : 'Unknown',
                    'status' => $file->status,
                    'main_application' => $file->mainApplication ? [
                        'id' => $file->mainApplication->id,
                        'applicant_name' => $file->mainApplication->applicant_name ?? 'Unknown'
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'files' => $formattedFiles
            ]);
        } catch (Exception $e) {
            Log::error('Error getting files by status', [
                'status' => $status,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading files',
                'files' => []
            ], 500);
        }
    }

    /**
     * Get file details with scannings for typing interface (AJAX)
     */
    public function getFileDetails(Request $request)
    {
        try {
            $fileIndexingId = $request->get('file_indexing_id');
            
            if (!$fileIndexingId) {
                return response()->json([
                    'success' => false,
                    'message' => 'File indexing ID is required'
                ], 400);
            }

            $fileIndexing = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->find($fileIndexingId);

            if (!$fileIndexing) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Format scannings with page information
            $scannings = $fileIndexing->scannings->map(function ($scanning) use ($fileIndexing) {
                $pageTypings = $fileIndexing->pagetypings()
                    ->where('scanning_id', $scanning->id)
                    ->get();

                // For PDF files, get individual pages
                $pages = [];
                $fileExtension = strtolower(pathinfo($scanning->document_path, PATHINFO_EXTENSION));
                
                if ($fileExtension === 'pdf') {
                    // For PDFs, we need to determine the number of pages
                    $pdfPageCount = $this->getPdfPageCount($scanning->document_path);
                    
                    for ($i = 1; $i <= $pdfPageCount; $i++) {
                        $pageTyping = $pageTypings->where('page_number', $i)->first();
                        $pages[] = [
                            'page_number' => $i,
                            'file_path' => $scanning->document_path . '#page=' . $i,
                            'is_typed' => $pageTyping !== null,
                            'page_typing' => $pageTyping ? [
                                'id' => $pageTyping->id,
                                'page_type' => $pageTyping->page_type,
                                'page_subtype' => $pageTyping->page_subtype,
                                'page_code' => $pageTyping->page_code,
                                'serial_number' => $pageTyping->serial_number
                            ] : null
                        ];
                    }
                } else {
                    // For image files, treat as single page
                    $pageTyping = $pageTypings->first();
                    $pages[] = [
                        'page_number' => 1,
                        'file_path' => $scanning->document_path,
                        'is_typed' => $pageTyping !== null,
                        'page_typing' => $pageTyping ? [
                            'id' => $pageTyping->id,
                            'page_type' => $pageTyping->page_type,
                            'page_subtype' => $pageTyping->page_subtype,
                            'page_code' => $pageTyping->page_code,
                            'serial_number' => $pageTyping->serial_number
                        ] : null
                    ];
                }

                return [
                    'id' => $scanning->id,
                    'document_path' => $scanning->document_path,
                    'original_filename' => $scanning->original_filename,
                    'document_type' => $scanning->document_type,
                    'paper_size' => $scanning->paper_size,
                    'status' => $scanning->status,
                    'pages' => $pages,
                    'total_pages' => count($pages),
                    'typed_pages' => count(array_filter($pages, fn($page) => $page['is_typed']))
                ];
            });

            return response()->json([
                'success' => true,
                'file' => [
                    'id' => $fileIndexing->id,
                    'file_number' => $fileIndexing->file_number,
                    'file_title' => $fileIndexing->file_title,
                    'district' => $fileIndexing->district,
                    'lga' => $fileIndexing->lga,
                    'main_application' => $fileIndexing->mainApplication ? [
                        'id' => $fileIndexing->mainApplication->id,
                        'applicant_name' => $fileIndexing->mainApplication->applicant_name ?? 'Unknown'
                    ] : null,
                    'scannings' => $scannings,
                    'total_scannings' => $scannings->count(),
                    'total_pages' => $scannings->sum('total_pages'),
                    'typed_pages' => $scannings->sum('typed_pages')
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error getting file details', [
                'file_indexing_id' => $fileIndexingId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading file details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new page typing
     */
    public function create(Request $request)
    {
        $PageTitle = 'Page Typing';
        $PageDescription = 'Create new page typing';
        
        return view('pagetyping.create', compact('PageTitle', 'PageDescription'));
    }

    /**
     * Store page typing data
     */
    public function store(Request $request)
    {
        try {
            // Check if this is a completion request
            if ($request->has('action') && $request->action === 'complete') {
                $fileIndexingId = $request->file_indexing_id;
                
                // Update all scannings for this file to 'typed' status
                $fileIndexing = FileIndexing::on('sqlsrv')->find($fileIndexingId);
                if ($fileIndexing) {
                    $fileIndexing->scannings()->update(['status' => 'typed']);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Page typing completed successfully!'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'File not found'
                    ], 404);
                }
            }
            
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

            // Mark file_indexings.is_updated = 1 if column exists
            try {
                FileIndexing::on('sqlsrv')->where('id', $fileIndexingId)->update(['is_updated' => 1]);
            } catch (Exception $e) {
                Log::warning('Could not update file_indexings.is_updated after page typing (column may be missing). Generate and run EDMS schema SQL.', [
                    'file_indexing_id' => $fileIndexingId,
                    'error' => $e->getMessage()
                ]);
            }

            $response = [
                'success' => $savedCount > 0,
                'message' => "{$savedCount} pages classified successfully!",
                'saved_count' => $savedCount,
                'total_count' => count($request->page_types),
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
     * Alias for store() to match pagetyping.save route
     */
    public function save(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Display the specified page typing
     */
    public function show($id)
    {
        $PageTitle = 'Page Typing Details';
        $PageDescription = 'View page typing information';
        
        return view('pagetyping.show', compact('PageTitle', 'PageDescription'));
    }

    /**
     * Show the form for editing the specified page typing
     */
    public function edit($id)
    {
        $PageTitle = 'Edit Page Typing';
        $PageDescription = 'Update page classification';
        
        return view('pagetyping.edit', compact('PageTitle', 'PageDescription'));
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
            $pageTyping->delete();

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

            // Mark file_indexings.is_updated = 1 if column exists
            try {
                FileIndexing::on('sqlsrv')->where('id', $validated['file_indexing_id'])->update(['is_updated' => 1]);
            } catch (Exception $e) {
                Log::warning('Could not update file_indexings.is_updated after single page typing (column may be missing). Generate and run EDMS schema SQL.', [
                    'file_indexing_id' => $validated['file_indexing_id'],
                    'error' => $e->getMessage()
                ]);
            }

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
     * PageType More - Load previously pagetyped pages plus new scans for continued typing
     */
    public function pageTypeMore(Request $request)
    {
        try {
            $fileIndexingId = $request->get('file_indexing_id');
            
            if (!$fileIndexingId) {
                return response()->json([
                    'success' => false,
                    'message' => 'File indexing ID is required'
                ], 400);
            }

            $fileIndexing = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->find($fileIndexingId);

            if (!$fileIndexing) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Get existing page typings
            $existingPageTypings = $fileIndexing->pagetypings()
                ->orderBy('page_number')
                ->get();

            // Get all scannings (including new ones)
            $allScannings = $fileIndexing->scannings()
                ->orderBy('created_at')
                ->get();

            // Combine existing page typings with new scan pages
            $combinedPages = [];
            $nextSerialNumber = $existingPageTypings->max('serial_number') + 1;

            // Add existing page typings first
            foreach ($existingPageTypings as $pageTyping) {
                $combinedPages[] = [
                    'id' => $pageTyping->id,
                    'page_number' => $pageTyping->page_number,
                    'page_type' => $pageTyping->page_type,
                    'page_subtype' => $pageTyping->page_subtype,
                    'serial_number' => $pageTyping->serial_number,
                    'page_code' => $pageTyping->page_code,
                    'file_path' => $pageTyping->file_path,
                    'scanning_id' => $pageTyping->scanning_id,
                    'is_existing' => true,
                    'is_typed' => true,
                    'source' => $pageTyping->source ?? 'initial',
                    'typed_by' => $pageTyping->typedBy ? $pageTyping->typedBy->name : 'Unknown',
                    'created_at' => $pageTyping->created_at ? $pageTyping->created_at->format('M d, Y H:i') : 'Unknown'
                ];
            }

            // Add new scan pages that haven't been typed yet
            foreach ($allScannings as $scanning) {
                $fileExtension = strtolower(pathinfo($scanning->document_path, PATHINFO_EXTENSION));
                
                if ($fileExtension === 'pdf') {
                    $pdfPageCount = $this->getPdfPageCount($scanning->document_path);
                    
                    for ($i = 1; $i <= $pdfPageCount; $i++) {
                        // Check if this page is already typed
                        $existingTyping = $existingPageTypings
                            ->where('scanning_id', $scanning->id)
                            ->where('page_number', $i)
                            ->first();
                        
                        if (!$existingTyping) {
                            $combinedPages[] = [
                                'id' => null,
                                'page_number' => $i,
                                'page_type' => null,
                                'page_subtype' => null,
                                'serial_number' => $nextSerialNumber++,
                                'page_code' => null,
                                'file_path' => $scanning->document_path . '#page=' . $i,
                                'scanning_id' => $scanning->id,
                                'is_existing' => false,
                                'is_typed' => false,
                                'source' => 'additional',
                                'scanning_info' => [
                                    'original_filename' => $scanning->original_filename,
                                    'document_type' => $scanning->document_type,
                                    'paper_size' => $scanning->paper_size,
                                    'created_at' => $scanning->created_at ? $scanning->created_at->format('M d, Y H:i') : 'Unknown'
                                ]
                            ];
                        }
                    }
                } else {
                    // For image files, treat as single page
                    $existingTyping = $existingPageTypings
                        ->where('scanning_id', $scanning->id)
                        ->where('page_number', 1)
                        ->first();
                    
                    if (!$existingTyping) {
                        $combinedPages[] = [
                            'id' => null,
                            'page_number' => 1,
                            'page_type' => null,
                            'page_subtype' => null,
                            'serial_number' => $nextSerialNumber++,
                            'page_code' => null,
                            'file_path' => $scanning->document_path,
                            'scanning_id' => $scanning->id,
                            'is_existing' => false,
                            'is_typed' => false,
                            'source' => 'additional',
                            'scanning_info' => [
                                'original_filename' => $scanning->original_filename,
                                'document_type' => $scanning->document_type,
                                'paper_size' => $scanning->paper_size,
                                'created_at' => $scanning->created_at ? $scanning->created_at->format('M d, Y H:i') : 'Unknown'
                            ]
                        ];
                    }
                }
            }

            // Mark file as updated since we're adding more pages
            try {
                $fileIndexing->update(['is_updated' => 1]);
            } catch (Exception $e) {
                Log::warning('Could not update file_indexings.is_updated in pageTypeMore (column may be missing)', [
                    'file_indexing_id' => $fileIndexingId,
                    'error' => $e->getMessage()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'PageType More data loaded successfully',
                'file' => [
                    'id' => $fileIndexing->id,
                    'file_number' => $fileIndexing->file_number,
                    'file_title' => $fileIndexing->file_title,
                    'district' => $fileIndexing->district,
                    'lga' => $fileIndexing->lga,
                    'main_application' => $fileIndexing->mainApplication ? [
                        'id' => $fileIndexing->mainApplication->id,
                        'applicant_name' => $fileIndexing->mainApplication->applicant_name ?? 'Unknown'
                    ] : null
                ],
                'pages' => $combinedPages,
                'summary' => [
                    'total_pages' => count($combinedPages),
                    'existing_typed_pages' => count(array_filter($combinedPages, fn($page) => $page['is_existing'])),
                    'new_pages_to_type' => count(array_filter($combinedPages, fn($page) => !$page['is_existing'])),
                    'total_scannings' => $allScannings->count(),
                    'next_serial_number' => $nextSerialNumber
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error in pageTypeMore', [
                'file_indexing_id' => $fileIndexingId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading PageType More data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store additional page typings from PageType More workflow
     */
    public function storePageTypeMore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_indexing_id' => 'required|integer|exists:sqlsrv.file_indexings,id',
                'additional_pages' => 'required|array|min:1',
                'additional_pages.*.scanning_id' => 'required|integer|exists:sqlsrv.scannings,id',
                'additional_pages.*.page_number' => 'required|integer|min:1',
                'additional_pages.*.page_type' => 'required|string|max:100',
                'additional_pages.*.page_subtype' => 'nullable|string|max:100',
                'additional_pages.*.serial_number' => 'required|integer|min:1',
                'additional_pages.*.page_code' => 'nullable|string|max:100',
                'additional_pages.*.file_path' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fileIndexingId = $request->file_indexing_id;
            $savedCount = 0;
            $errors = [];

            foreach ($request->additional_pages as $pageData) {
                try {
                    // Check if page typing already exists for this specific page
                    $existingPageTyping = PageTyping::on('sqlsrv')
                        ->where('file_indexing_id', $fileIndexingId)
                        ->where('scanning_id', $pageData['scanning_id'])
                        ->where('page_number', $pageData['page_number'])
                        ->first();

                    if ($existingPageTyping) {
                        // Update existing record
                        $existingPageTyping->update([
                            'page_type' => $pageData['page_type'],
                            'page_subtype' => $pageData['page_subtype'],
                            'serial_number' => $pageData['serial_number'],
                            'page_code' => $pageData['page_code'],
                            'typed_by' => Auth::id(),
                            'source' => 'additional', // Mark as additional typing
                        ]);
                    } else {
                        // Create new record
                        PageTyping::on('sqlsrv')->create([
                            'file_indexing_id' => $fileIndexingId,
                            'scanning_id' => $pageData['scanning_id'],
                            'page_number' => $pageData['page_number'],
                            'page_type' => $pageData['page_type'],
                            'page_subtype' => $pageData['page_subtype'],
                            'serial_number' => $pageData['serial_number'],
                            'page_code' => $pageData['page_code'],
                            'file_path' => $pageData['file_path'],
                            'typed_by' => Auth::id(),
                            'source' => 'additional', // Mark as additional typing
                        ]);
                    }

                    $savedCount++;
                } catch (Exception $e) {
                    $errors[] = "Error saving additional page {$pageData['page_number']}: " . $e->getMessage();
                    Log::error('Error saving additional page typing', [
                        'file_indexing_id' => $fileIndexingId,
                        'page_data' => $pageData,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update file_indexings.is_updated = 1 to indicate more pages were added
            try {
                FileIndexing::on('sqlsrv')->where('id', $fileIndexingId)->update(['is_updated' => 1]);
            } catch (Exception $e) {
                Log::warning('Could not update file_indexings.is_updated after PageType More (column may be missing)', [
                    'file_indexing_id' => $fileIndexingId,
                    'error' => $e->getMessage()
                ]);
            }

            $response = [
                'success' => $savedCount > 0,
                'message' => "{$savedCount} additional pages classified successfully!",
                'saved_count' => $savedCount,
                'total_count' => count($request->additional_pages),
            ];

            if (count($errors) > 0) {
                $response['errors'] = $errors;
                $response['message'] .= " " . count($errors) . " errors occurred.";
            }

            return response()->json($response);

        } catch (Exception $e) {
            Log::error('Error storing PageType More data', [
                'error' => $e->getMessage(),
                'request_data' => $request->except('additional_pages')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving additional page typings: ' . $e->getMessage()
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
     * Get PDF page count
     */
    private function getPdfPageCount($filePath)
    {
        try {
            // Resolve to storage path where uploads are saved
            $fullPath = storage_path('app/public/' . ltrim($filePath, '/'));
            if (!file_exists($fullPath)) {
                return 1; // Default to 1 page if file not found
            }

            // Try to get page count using different methods
            if (extension_loaded('imagick')) {
                try {
                    $imagick = new \Imagick();
                    $imagick->readImage($fullPath);
                    $pageCount = $imagick->getNumberImages();
                    $imagick->clear();
                    return $pageCount;
                } catch (Exception $e) {
                    Log::warning('Imagick failed to read PDF', ['file' => $filePath, 'error' => $e->getMessage()]);
                }
            }

            // Fallback: try to parse PDF manually (basic method)
            $content = file_get_contents($fullPath);
            if ($content) {
                preg_match_all('/\/Count\s+(\d+)/', $content, $matches);
                if (!empty($matches[1])) {
                    return max($matches[1]);
                }
                
                // Alternative method: count page objects
                preg_match_all('/\/Type\s*\/Page[^s]/', $content, $matches);
                if (!empty($matches[0])) {
                    return count($matches[0]);
                }
            }

            // Default fallback
            return 3; // Assume 3 pages for PDFs when we can't determine
        } catch (Exception $e) {
            Log::error('Error getting PDF page count', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    /**
     * Get PageType More count (files with is_updated = 1)
     */
    private function getPageTypeMoreCount()
    {
        try {
            return FileIndexing::on('sqlsrv')
                ->whereHas('pagetypings') // Must have existing page typings
                ->where('is_updated', 1) // And be marked as updated
                ->count();
        } catch (Exception $e) {
            Log::warning('Could not get PageType More count (is_updated column may be missing)', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get files for PageType More (AJAX)
     */
    public function getPageTypeMoreFiles(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $limit = $request->get('limit', 20);

            $query = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->whereHas('pagetypings') // Must have existing page typings
                ->where('is_updated', 1); // And be marked as updated

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('file_number', 'like', "%{$search}%")
                      ->orWhere('file_title', 'like', "%{$search}%")
                      ->orWhere('district', 'like', "%{$search}%")
                      ->orWhere('lga', 'like', "%{$search}%");
                });
            }

            $files = $query->orderBy('updated_at', 'desc')
                          ->limit($limit)
                          ->get();

            $formattedFiles = $files->map(function ($file) {
                $scanningsCount = $file->scannings->count();
                $pageTypingsCount = $file->pagetypings->count();
                
                // Calculate existing vs new pages
                $existingPages = $pageTypingsCount;
                $newScans = max(0, $scanningsCount - $pageTypingsCount);
                $totalPages = $scanningsCount;

                return [
                    'id' => $file->id,
                    'file_number' => $file->file_number,
                    'file_title' => $file->file_title,
                    'district' => $file->district,
                    'lga' => $file->lga,
                    'existing_pages' => $existingPages,
                    'new_scans' => $newScans,
                    'total_pages' => $totalPages,
                    'last_updated' => $file->updated_at ? $file->updated_at->format('M d, Y') : 'Unknown',
                    'status' => 'Updated',
                    'is_updated' => $file->is_updated,
                    'main_application' => $file->mainApplication ? [
                        'id' => $file->mainApplication->id,
                        'applicant_name' => $file->mainApplication->applicant_name ?? 'Unknown'
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'files' => $formattedFiles
            ]);
        } catch (Exception $e) {
            Log::error('Error getting PageType More files', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading PageType More files: ' . $e->getMessage(),
                'files' => []
            ], 500);
        }
    }
}
