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
            // Get QC statistics
            $stats = [
                'pending_qc' => PageTyping::where('qc_status', 'pending')->count(),
                'passed_qc' => PageTyping::where('qc_status', 'passed')->count(),
                'failed_qc' => PageTyping::where('qc_status', 'failed')->count(),
                'overridden_qc' => PageTyping::where('qc_overridden', true)->count(),
                'today_reviewed' => PageTyping::whereDate('qc_reviewed_at', today())->count(),
                'files_with_issues' => FileIndexing::where('has_qc_issues', true)->count(),
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
     * List files pending QC
     */
    public function listPending(Request $request)
    {
        try {
            $query = FileIndexing::with(['pagetypings' => function($q) {
                $q->where('qc_status', 'pending');
            }, 'scannings'])
            ->whereHas('pagetypings', function($q) {
                $q->where('qc_status', 'pending');
            })
            ->where('workflow_status', 'pagetyped');

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
                $file->pending_pages_count = $file->pagetypings->where('qc_status', 'pending')->count();
                $file->total_pages_count = $file->pagetypings->count();
                $file->last_pagetyped_at = $file->pagetypings->max('updated_at');
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
                        'pending_pages' => $pageTypings->where('qc_status', 'pending')->count(),
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
                        ->where('qc_status', 'pending')
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
        // Note: Role-based access control can be added here if needed
        // For now, all authenticated users can override QC

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
                        ->where('qc_status', 'pending')
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
                'pending_pages' => PageTyping::where('qc_status', 'pending')->count(),
            ];

            // QC performance by reviewer
            $reviewerStats = PageTyping::whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                ->whereNotNull('qc_reviewed_by')
                ->selectRaw('qc_reviewed_by, COUNT(*) as total_reviewed, 
                           SUM(CASE WHEN qc_status = "passed" THEN 1 ELSE 0 END) as passed,
                           SUM(CASE WHEN qc_status = "failed" THEN 1 ELSE 0 END) as failed,
                           SUM(CASE WHEN qc_overridden = 1 THEN 1 ELSE 0 END) as overridden')
                ->groupBy('qc_reviewed_by')
                ->with('qcReviewer:id,name')
                ->get();

            // Daily QC activity
            $dailyActivity = PageTyping::whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                ->whereNotNull('qc_reviewed_at')
                ->selectRaw('DATE(qc_reviewed_at) as date, COUNT(*) as total_reviewed,
                           SUM(CASE WHEN qc_status = "passed" THEN 1 ELSE 0 END) as passed,
                           SUM(CASE WHEN qc_status = "failed" THEN 1 ELSE 0 END) as failed')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'overall_stats' => $stats,
                    'reviewer_stats' => $reviewerStats,
                    'daily_activity' => $dailyActivity,
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