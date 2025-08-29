<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PTQController extends Controller
{
    /**
     * Display the PTQ Control interface
     */
    public function index()
    {
        try {
            // Get QC statistics using SQL Server connection
            $stats = [
                'pending_qc' => DB::connection('sqlsrv')->table('file_indexings')
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))
                              ->from('pagetypings')
                              ->whereRaw('pagetypings.file_indexing_id = file_indexings.id');
                    })
                    ->whereNotExists(function($query) {
                        $query->select(DB::raw(1))
                              ->from('pagetypings')
                              ->whereRaw('pagetypings.file_indexing_id = file_indexings.id')
                              ->whereNotNull('qc_reviewed_at');
                    })->count(),
                    
                'qc_in_progress' => DB::connection('sqlsrv')->table('file_indexings')
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))
                              ->from('pagetypings')
                              ->whereRaw('pagetypings.file_indexing_id = file_indexings.id')
                              ->whereNotNull('qc_reviewed_at');
                    })
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))
                              ->from('pagetypings')
                              ->whereRaw('pagetypings.file_indexing_id = file_indexings.id')
                              ->whereNull('qc_reviewed_at');
                    })->count(),
                    
                'qc_completed' => DB::connection('sqlsrv')->table('file_indexings')
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))
                              ->from('pagetypings')
                              ->whereRaw('pagetypings.file_indexing_id = file_indexings.id');
                    })
                    ->whereNotExists(function($query) {
                        $query->select(DB::raw(1))
                              ->from('pagetypings')
                              ->whereRaw('pagetypings.file_indexing_id = file_indexings.id')
                              ->whereNull('qc_reviewed_at');
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
            // Query using SQL Server connection
            $query = DB::connection('sqlsrv')->table('file_indexings')
                ->leftJoin('pagetypings', 'file_indexings.id', '=', 'pagetypings.file_indexing_id')
                ->leftJoin('users', 'pagetypings.typed_by', '=', 'users.id')
                ->select([
                    'file_indexings.id',
                    'file_indexings.file_number',
                    'file_indexings.file_title',
                    'file_indexings.land_use_type',
                    'file_indexings.created_at',
                    'file_indexings.updated_at',
                    DB::raw('COUNT(pagetypings.id) as total_pages_count'),
                    DB::raw('COUNT(CASE WHEN pagetypings.qc_reviewed_at IS NULL THEN 1 END) as pending_pages_count'),
                    DB::raw('MAX(pagetypings.updated_at) as last_pagetyped_at'),
                    DB::raw('COALESCE(users.first_name + \' \' + users.last_name, \'Unknown\') as pagetyped_by_name')
                ])
                ->whereExists(function($subquery) {
                    $subquery->select(DB::raw(1))
                            ->from('pagetypings')
                            ->whereRaw('pagetypings.file_indexing_id = file_indexings.id');
                })
                ->whereExists(function($subquery) {
                    $subquery->select(DB::raw(1))
                            ->from('pagetypings')
                            ->whereRaw('pagetypings.file_indexing_id = file_indexings.id')
                            ->whereNull('qc_reviewed_at');
                })
                ->groupBy([
                    'file_indexings.id',
                    'file_indexings.file_number',
                    'file_indexings.file_title',
                    'file_indexings.land_use_type',
                    'file_indexings.created_at',
                    'file_indexings.updated_at',
                    'users.first_name',
                    'users.last_name'
                ]);

            // Apply filters
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('file_indexings.file_number', 'like', "%{$search}%")
                      ->orWhere('file_indexings.file_title', 'like', "%{$search}%")
                      ->orWhere('file_indexings.batch_no', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'file_indexings.updated_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Get results
            $files = $query->get();

            return response()->json([
                'success' => true,
                'data' => $files->toArray(),
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $files->count(),
                    'total' => $files->count(),
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
            $query = DB::connection('sqlsrv')->table('file_indexings')
                ->leftJoin('pagetypings', 'file_indexings.id', '=', 'pagetypings.file_indexing_id')
                ->select([
                    'file_indexings.id',
                    'file_indexings.file_number',
                    'file_indexings.file_title',
                    'file_indexings.land_use_type',
                    'file_indexings.created_at',
                    'file_indexings.updated_at',
                    DB::raw('COUNT(pagetypings.id) as total_pages_count'),
                    DB::raw('COUNT(CASE WHEN pagetypings.qc_reviewed_at IS NULL THEN 1 END) as pending_pages_count'),
                    DB::raw('COUNT(CASE WHEN pagetypings.qc_reviewed_at IS NOT NULL THEN 1 END) as reviewed_pages_count'),
                    DB::raw('MAX(pagetypings.updated_at) as last_pagetyped_at')
                ])
                ->whereExists(function($subquery) {
                    $subquery->select(DB::raw(1))
                            ->from('pagetypings')
                            ->whereRaw('pagetypings.file_indexing_id = file_indexings.id')
                            ->whereNotNull('qc_reviewed_at');
                })
                ->whereExists(function($subquery) {
                    $subquery->select(DB::raw(1))
                            ->from('pagetypings')
                            ->whereRaw('pagetypings.file_indexing_id = file_indexings.id')
                            ->whereNull('qc_reviewed_at');
                })
                ->groupBy([
                    'file_indexings.id',
                    'file_indexings.file_number',
                    'file_indexings.file_title',
                    'file_indexings.land_use_type',
                    'file_indexings.created_at',
                    'file_indexings.updated_at'
                ]);

            // Apply filters
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('file_indexings.file_number', 'like', "%{$search}%")
                      ->orWhere('file_indexings.file_title', 'like', "%{$search}%")
                      ->orWhere('file_indexings.batch_no', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'file_indexings.updated_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $files = $query->get()->map(function($file) {
                $file->qc_progress = $file->total_pages_count > 0 
                    ? round(($file->reviewed_pages_count / $file->total_pages_count) * 100, 1) 
                    : 0;
                $file->pagetyped_by_name = 'Unknown';
                return $file;
            });

            return response()->json([
                'success' => true,
                'data' => $files->toArray(),
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $files->count(),
                    'total' => $files->count(),
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
            $query = DB::connection('sqlsrv')->table('file_indexings')
                ->leftJoin('pagetypings', 'file_indexings.id', '=', 'pagetypings.file_indexing_id')
                ->leftJoin('users', 'pagetypings.qc_reviewed_by', '=', 'users.id')
                ->select([
                    'file_indexings.id',
                    'file_indexings.file_number',
                    'file_indexings.file_title',
                    'file_indexings.land_use_type',
                    'file_indexings.created_at',
                    'file_indexings.updated_at',
                    DB::raw('COUNT(pagetypings.id) as total_pages_count'),
                    DB::raw('COUNT(CASE WHEN pagetypings.qc_status = \'passed\' THEN 1 END) as passed_pages_count'),
                    DB::raw('COUNT(CASE WHEN pagetypings.qc_status = \'failed\' THEN 1 END) as failed_pages_count'),
                    DB::raw('COUNT(CASE WHEN pagetypings.qc_overridden = 1 THEN 1 END) as overridden_pages_count'),
                    DB::raw('MAX(pagetypings.qc_reviewed_at) as qc_completed_at'),
                    DB::raw('COALESCE(users.first_name + \' \' + users.last_name, \'Unknown\') as qc_reviewed_by_name')
                ])
                ->whereExists(function($subquery) {
                    $subquery->select(DB::raw(1))
                            ->from('pagetypings')
                            ->whereRaw('pagetypings.file_indexing_id = file_indexings.id');
                })
                ->whereNotExists(function($subquery) {
                    $subquery->select(DB::raw(1))
                            ->from('pagetypings')
                            ->whereRaw('pagetypings.file_indexing_id = file_indexings.id')
                            ->whereNull('qc_reviewed_at');
                })
                ->groupBy([
                    'file_indexings.id',
                    'file_indexings.file_number',
                    'file_indexings.file_title',
                    'file_indexings.land_use_type',
                    'file_indexings.created_at',
                    'file_indexings.updated_at',
                    'users.first_name',
                    'users.last_name'
                ]);

            // Apply filters
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('file_indexings.file_number', 'like', "%{$search}%")
                      ->orWhere('file_indexings.file_title', 'like', "%{$search}%")
                      ->orWhere('file_indexings.batch_no', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'file_indexings.updated_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $files = $query->get()->map(function($file) {
                // Create processed pages array for frontend
                $file->processedPages = [];
                $file->pagetyped_by_name = 'Unknown';
                return $file;
            });

            return response()->json([
                'success' => true,
                'data' => $files->toArray(),
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $files->count(),
                    'total' => $files->count(),
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
            // Get file indexing details using SQL Server
            $fileIndexing = DB::connection('sqlsrv')->table('file_indexings')
                ->where('id', $fileIndexingId)
                ->first();

            if (!$fileIndexing) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Get page typing details with QC status
            $pageTypings = DB::connection('sqlsrv')->table('pagetypings')
                ->where('file_indexing_id', $fileIndexingId)
                ->orderBy('page_number')
                ->get()
                ->map(function ($pageTyping) {
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
                        'file_url' => $pageTyping->file_path ? url('storage/app/public/' . $pageTyping->file_path) : null,
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
            'page_typing_ids.*' => 'required|integer',
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

        DB::connection('sqlsrv')->beginTransaction();

        try {
            $pageTypingIds = $request->page_typing_ids;
            $qcStatus = $request->qc_status;
            $notes = $request->notes;

            // Update page typings using SQL Server
            $updatedCount = DB::connection('sqlsrv')->table('pagetypings')
                ->whereIn('id', $pageTypingIds)
                ->update([
                    'qc_status' => $qcStatus,
                    'qc_reviewed_by' => auth()->id(),
                    'qc_reviewed_at' => now(),
                    'has_qc_issues' => $qcStatus === 'failed' ? 1 : 0,
                    'qc_override_note' => $qcStatus === 'failed' ? $notes : null,
                ]);

            // Get affected file indexings
            $fileIndexingIds = DB::connection('sqlsrv')->table('pagetypings')
                ->whereIn('id', $pageTypingIds)
                ->distinct()
                ->pluck('file_indexing_id');

            // Update file indexing workflow status and QC issues flag
            foreach ($fileIndexingIds as $fileIndexingId) {
                $hasFailedPages = DB::connection('sqlsrv')->table('pagetypings')
                    ->where('file_indexing_id', $fileIndexingId)
                    ->where('qc_status', 'failed')
                    ->exists();
                
                $allPagesReviewed = !DB::connection('sqlsrv')->table('pagetypings')
                    ->where('file_indexing_id', $fileIndexingId)
                    ->whereNull('qc_reviewed_at')
                    ->exists();

                DB::connection('sqlsrv')->table('file_indexings')
                    ->where('id', $fileIndexingId)
                    ->update([
                        'has_qc_issues' => $hasFailedPages ? 1 : 0,
                        'workflow_status' => $allPagesReviewed && !$hasFailedPages ? 'qc_passed' : 'pagetyped',
                    ]);
            }

            // Log activity - Removed due to missing 'action' column in user_activity_logs table
            // DB::connection('sqlsrv')->table('user_activity_logs')->insert([...]);

            DB::connection('sqlsrv')->commit();

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} page(s) marked as {$qcStatus}",
                'data' => [
                    'updated_count' => $updatedCount,
                    'affected_files' => $fileIndexingIds->count(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            
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
            'page_typing_ids.*' => 'required|integer',
            'override_note' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::connection('sqlsrv')->beginTransaction();

        try {
            $pageTypingIds = $request->page_typing_ids;
            $overrideNote = $request->override_note;

            // Update page typings with override using SQL Server
            $updatedCount = DB::connection('sqlsrv')->table('pagetypings')
                ->whereIn('id', $pageTypingIds)
                ->update([
                    'qc_status' => 'passed',
                    'qc_overridden' => 1,
                    'qc_override_note' => $overrideNote,
                    'qc_reviewed_by' => auth()->id(),
                    'qc_reviewed_at' => now(),
                    'has_qc_issues' => 0,
                ]);

            // Get affected file indexings and update their status
            $fileIndexingIds = DB::connection('sqlsrv')->table('pagetypings')
                ->whereIn('id', $pageTypingIds)
                ->distinct()
                ->pluck('file_indexing_id');

            foreach ($fileIndexingIds as $fileIndexingId) {
                $allPagesReviewed = !DB::connection('sqlsrv')->table('pagetypings')
                    ->where('file_indexing_id', $fileIndexingId)
                    ->whereNull('qc_reviewed_at')
                    ->exists();

                DB::connection('sqlsrv')->table('file_indexings')
                    ->where('id', $fileIndexingId)
                    ->update([
                        'has_qc_issues' => 0,
                        'workflow_status' => $allPagesReviewed ? 'qc_passed' : 'pagetyped',
                    ]);
            }

            DB::connection('sqlsrv')->commit();

            return response()->json([
                'success' => true,
                'message' => "QC status overridden for {$updatedCount} page(s)",
                'data' => [
                    'updated_count' => $updatedCount,
                    'affected_files' => $fileIndexingIds->count(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            
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

            // Overall QC statistics using SQL Server
            $stats = [
                'total_pages_reviewed' => DB::connection('sqlsrv')->table('pagetypings')
                    ->whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                    ->whereNotNull('qc_reviewed_at')
                    ->count(),
                'passed_pages' => DB::connection('sqlsrv')->table('pagetypings')
                    ->whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                    ->where('qc_status', 'passed')
                    ->count(),
                'failed_pages' => DB::connection('sqlsrv')->table('pagetypings')
                    ->whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                    ->where('qc_status', 'failed')
                    ->count(),
                'overridden_pages' => DB::connection('sqlsrv')->table('pagetypings')
                    ->whereBetween('qc_reviewed_at', [$dateFrom, $dateTo])
                    ->where('qc_overridden', 1)
                    ->count(),
                'pending_pages' => DB::connection('sqlsrv')->table('pagetypings')
                    ->whereNull('qc_reviewed_at')
                    ->count(),
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