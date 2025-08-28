<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PageTyping;
use App\Models\FileIndexing;
use App\Models\FileTracking;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PTQController extends Controller
{
    /**
     * Display the PTQ Control interface
     */
    public function index()
    {
        try {
            // Get QC statistics - count files that have page typings but no QC review
            $stats = [
                'pending_qc' => FileIndexing::whereHas('pagetypings')
                    ->whereDoesntHave('pagetypings', function($q) {
                        $q->whereNotNull('qc_reviewed_at');
                    })->count(),
                'qc_in_progress' => FileIndexing::whereHas('pagetypings', function($q) {
                        $q->whereNotNull('qc_reviewed_at');
                    })
                    ->whereHas('pagetypings', function($q) {
                        $q->whereNull('qc_reviewed_at');
                    })->count(),
                'qc_completed' => FileIndexing::whereHas('pagetypings')
                    ->whereDoesntHave('pagetypings', function($q) {
                        $q->whereNull('qc_reviewed_at');
                    })->count(),
            ];

            return view('qc.ptq_control', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Error loading PTQ control interface', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return back()->with('error', 'Failed to load PTQ control interface: ' . $e->getMessage());
        }
    }

    /**
     * List files pending QC - Files that are pagetyped but not QC reviewed
     */
    public function listPending(Request $request)
    {
        try {
            // Query for files that have been pagetyped but not QC reviewed
            $query = FileIndexing::with(['pagetypings' => function($q) {
                $q->orderBy('page_number');
            }, 'pagetypings.typedBy:id,name'])
            ->whereHas('pagetypings') // Must have page typings (pagetyped files)
            ->whereDoesntHave('pagetypings', function($q) {
                $q->whereNotNull('qc_reviewed_at'); // No QC review done yet
            });

            // Apply filters
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('file_number', 'like', "%{$search}%")
                      ->orWhere('file_title', 'like', "%{$search}%")
                      ->orWhere('batch_no', 'like', "%{$search}%");
                });
            }

            if ($request->has('batch_no') && $request->batch_no !== '') {
                $query->where('batch_no', $request->batch_no);
            }

            if ($request->has('date_from') && $request->date_from !== '') {
                $query->whereHas('pagetypings', function($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                });
            }

            if ($request->has('date_to') && $request->date_to !== '') {
                $query->whereHas('pagetypings', function($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'updated_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->get('per_page', 15);
            $files = $query->paginate($perPage);

            // Add additional data for each file
            $files->getCollection()->transform(function ($file) {
                $file->pending_pages_count = $file->pagetypings->whereNull('qc_reviewed_at')->count();
                $file->total_pages_count = $file->pagetypings->count();
                $file->last_pagetyped_at = $file->pagetypings->max('updated_at');
                $file->pagetyped_by_name = $file->pagetypings->first()?->typedBy?->name ?? 'Unknown';
                return $file;
            });

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
            Log::error('Error listing pending QC files', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load pending QC files: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List files in QC progress - Files with some pages QC reviewed but not all
     */
    public function listInProgress(Request $request)
    {
        try {
            $query = FileIndexing::with(['pagetypings' => function($q) {
                $q->orderBy('page_number');
            }])
            ->whereHas('pagetypings', function($q) {
                $q->whereNotNull('qc_reviewed_at'); // Some pages reviewed
            })
            ->whereHas('pagetypings', function($q) {
                $q->whereNull('qc_reviewed_at'); // Some pages not reviewed
            });

            // Apply filters
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('file_number', 'like', "%{$search}%")
                      ->orWhere('file_title', 'like', "%{$search}%")
                      ->orWhere('batch_no', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'updated_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->get('per_page', 15);
            $files = $query->paginate($perPage);

            // Add additional data for each file
            $files->getCollection()->transform(function ($file) {
                $file->pending_pages_count = $file->pagetypings->whereNull('qc_reviewed_at')->count();
                $file->reviewed_pages_count = $file->pagetypings->whereNotNull('qc_reviewed_at')->count();
                $file->total_pages_count = $file->pagetypings->count();
                $file->qc_progress = $file->total_pages_count > 0 
                    ? round(($file->reviewed_pages_count / $file->total_pages_count) * 100, 1) 
                    : 0;
                return $file;
            });

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
            Log::error('Error listing QC in-progress files', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load QC in-progress files: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List completed QC files - Files where all pages have been QC reviewed
     */
    public function listCompleted(Request $request)
    {
        try {
            $query = FileIndexing::with(['pagetypings' => function($q) {
                $q->orderBy('page_number');
            }, 'pagetypings.qcReviewer:id,name'])
            ->whereHas('pagetypings') // Must have page typings
            ->whereDoesntHave('pagetypings', function($q) {
                $q->whereNull('qc_reviewed_at'); // All pages must be QC reviewed
            });

            // Apply filters
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('file_number', 'like', "%{$search}%")
                      ->orWhere('file_title', 'like', "%{$search}%")
                      ->orWhere('batch_no', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'updated_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->get('per_page', 15);
            $files = $query->paginate($perPage);

            // Add additional data for each file
            $files->getCollection()->transform(function ($file) {
                $file->total_pages_count = $file->pagetypings->count();
                $file->passed_pages_count = $file->pagetypings->where('qc_status', 'passed')->count();
                $file->failed_pages_count = $file->pagetypings->where('qc_status', 'failed')->count();
                $file->overridden_pages_count = $file->pagetypings->where('qc_overridden', true)->count();
                $file->qc_completed_at = $file->pagetypings->max('qc_reviewed_at');
                $file->qc_reviewed_by_name = $file->pagetypings->whereNotNull('qc_reviewed_by')->first()?->qcReviewer?->name;
                
                // Create processed pages array for frontend
                $file->processedPages = $file->pagetypings->map(function($page, $index) {
                    return [
                        'pageNumber' => $index + 1,
                        'pageCode' => $page->page_code,
                        'pageType' => $page->page_type,
                        'pageSubType' => $page->page_subtype,
                        'qcStatus' => $page->qc_overridden ? 'overridden' : ($page->qc_status === 'passed' ? 'passed' : 'rejected'),
                        'overrideNote' => $page->qc_override_note
                    ];
                })->toArray();
                
                return $file;
            });

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
            Log::error('Error listing completed QC files', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load completed QC files: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get QC details for a specific file
     */
    public function getQCDetails($fileIndexingId)
    {
        try {
            $fileIndexing = FileIndexing::with([
                'pagetypings' => function($q) {
                    $q->orderBy('page_number');
                },
                'scannings'
            ])->findOrFail($fileIndexingId);

            // Get page typing details with QC status
            $pageTypings = $fileIndexing->pagetypings->map(function ($pageTyping) {
                return [
                    'id' => $pageTyping->id,
                    'page_number' => $pageTyping->page_number,
                    'page_type' => $pageTyping->page_type,
                    'page_subtype' => $pageTyping->page_subtype,
                    'serial_number' => $pageTyping->serial_number,
                    'page_code' => $pageTyping->page_code,
                    'file_path' => $pageTyping->file_path,
                    'qc_status' => $pageTyping->qc_status,
                    'qc_reviewed_by' => $pageTyping->qc_reviewed_by,
                    'qc_reviewed_at' => $pageTyping->qc_reviewed_at,
                    'qc_overridden' => $pageTyping->qc_overridden,
                    'qc_override_note' => $pageTyping->qc_override_note,
                    'has_qc_issues' => $pageTyping->has_qc_issues,
                    'file_url' => asset('storage/' . $pageTyping->file_path),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'file_indexing' => $fileIndexing,
                    'page_typings' => $pageTypings,
                    'qc_summary' => [
                        'total_pages' => $pageTypings->count(),
                        'pending_pages' => $pageTypings->whereNull('qc_reviewed_at')->count(),
                        'passed_pages' => $pageTypings->where('qc_status', 'passed')->count(),
                        'failed_pages' => $pageTypings->where('qc_status', 'failed')->count(),
                        'overridden_pages' => $pageTypings->where('qc_overridden', true)->count(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting QC details', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'file_indexing_id' => $fileIndexingId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load QC details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark QC status for page typings
     */
    public function markQCStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_typing_ids' => 'required|array|min:1',
            'page_typing_ids.*' => 'required|exists:pagetypings,id',
            'qc_status' => 'required|in:passed,failed',
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
            $pageTypingIds = $request->page_typing_ids;
            $qcStatus = $request->qc_status;
            $notes = $request->notes;

            // Update page typings
            $updatedCount = PageTyping::whereIn('id', $pageTypingIds)
                ->update([
                    'qc_status' => $qcStatus,
                    'qc_reviewed_by' => auth()->id(),
                    'qc_reviewed_at' => now(),
                    'has_qc_issues' => $qcStatus === 'failed',
                    'qc_override_note' => $qcStatus === 'failed' ? $notes : null,
                ]);

            // Get affected file indexings
            $fileIndexingIds = PageTyping::whereIn('id', $pageTypingIds)
                ->distinct()
                ->pluck('file_indexing_id');

            // Update file indexing workflow status and QC issues flag
            foreach ($fileIndexingIds as $fileIndexingId) {
                $fileIndexing = FileIndexing::find($fileIndexingId);
                if ($fileIndexing) {
                    $hasFailedPages = $fileIndexing->pagetypings()
                        ->where('qc_status', 'failed')
                        ->exists();
                    
                    $allPagesReviewed = !$fileIndexing->pagetypings()
                        ->whereNull('qc_reviewed_at')
                        ->exists();

                    $fileIndexing->update([
                        'has_qc_issues' => $hasFailedPages,
                        'workflow_status' => $allPagesReviewed && !$hasFailedPages ? 'qc_passed' : 'pagetyped',
                    ]);

                    // Update file tracking
                    $tracking = FileTracking::where('file_indexing_id', $fileIndexingId)->first();
                    if ($tracking) {
                        if ($allPagesReviewed && !$hasFailedPages) {
                            $tracking->updateStatus('qc_passed', 'All pages passed QC review');
                        } elseif ($hasFailedPages) {
                            $tracking->updateStatus('qc_failed', 'Some pages failed QC review');
                        }
                        
                        $tracking->addMovementEntry([
                            'action' => 'qc_review',
                            'qc_status' => $qcStatus,
                            'pages_reviewed' => count($pageTypingIds),
                            'notes' => $notes,
                        ]);
                    }
                }
            }

            // Log activity
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'qc_review',
                'description' => "Marked {$updatedCount} pages as {$qcStatus} in QC review",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'additional_info' => json_encode([
                    'page_typing_ids' => $pageTypingIds,
                    'qc_status' => $qcStatus,
                    'file_indexing_ids' => $fileIndexingIds->toArray(),
                    'notes' => $notes,
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} page(s) marked as {$qcStatus}",
                'data' => [
                    'updated_count' => $updatedCount,
                    'affected_files' => $fileIndexingIds->count(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error marking QC status', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'QC status update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Override QC status
     */
    public function overrideQC(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_typing_ids' => 'required|array|min:1',
            'page_typing_ids.*' => 'required|exists:pagetypings,id',
            'override_note' => 'required|string|max:1000',
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
            $pageTypingIds = $request->page_typing_ids;
            $overrideNote = $request->override_note;

            // Update page typings with override
            $updatedCount = PageTyping::whereIn('id', $pageTypingIds)
                ->update([
                    'qc_status' => 'passed',
                    'qc_overridden' => true,
                    'qc_override_note' => $overrideNote,
                    'qc_reviewed_by' => auth()->id(),
                    'qc_reviewed_at' => now(),
                    'has_qc_issues' => false,
                ]);

            // Get affected file indexings and update their status
            $fileIndexingIds = PageTyping::whereIn('id', $pageTypingIds)
                ->distinct()
                ->pluck('file_indexing_id');

            foreach ($fileIndexingIds as $fileIndexingId) {
                $fileIndexing = FileIndexing::find($fileIndexingId);
                if ($fileIndexing) {
                    $allPagesReviewed = !$fileIndexing->pagetypings()
                        ->whereNull('qc_reviewed_at')
                        ->exists();

                    $fileIndexing->update([
                        'has_qc_issues' => false,
                        'workflow_status' => $allPagesReviewed ? 'qc_passed' : 'pagetyped',
                    ]);

                    // Update file tracking
                    $tracking = FileTracking::where('file_indexing_id', $fileIndexingId)->first();
                    if ($tracking) {
                        if ($allPagesReviewed) {
                            $tracking->updateStatus('qc_passed', 'QC completed with override');
                        }
                        
                        $tracking->addMovementEntry([
                            'action' => 'qc_override',
                            'pages_overridden' => count($pageTypingIds),
                            'override_note' => $overrideNote,
                        ]);
                    }
                }
            }

            // Log activity
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'qc_override',
                'description' => "Overrode QC status for {$updatedCount} pages",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'additional_info' => json_encode([
                    'page_typing_ids' => $pageTypingIds,
                    'file_indexing_ids' => $fileIndexingIds->toArray(),
                    'override_note' => $overrideNote,
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "QC status overridden for {$updatedCount} page(s)",
                'data' => [
                    'updated_count' => $updatedCount,
                    'affected_files' => $fileIndexingIds->count(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error overriding QC status', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'QC override failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get QC statistics and reports
     */
    public function getQCStats(Request $request)
    {
        try {
            $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
            $dateTo = $request->get('date_to', now()->format('Y-m-d'));

            // Overall QC statistics
            $stats = [
                'total_pages_reviewed' => PageTyping::whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                    ->whereNotNull('qc_reviewed_at')
                    ->count(),
                'passed_pages' => PageTyping::whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                    ->where('qc_status', 'passed')
                    ->count(),
                'failed_pages' => PageTyping::whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                    ->where('qc_status', 'failed')
                    ->count(),
                'overridden_pages' => PageTyping::whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                    ->where('qc_overridden', true)
                    ->count(),
                'pending_pages' => PageTyping::whereNull('qc_reviewed_at')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'overall_stats' => $stats,
                    'date_range' => [
                        'from' => $dateFrom,
                        'to' => $dateTo,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting QC statistics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load QC statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}