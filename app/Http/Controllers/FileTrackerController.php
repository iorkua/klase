<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use App\Models\FileTracking;
use App\Models\FileIndexing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FileTrackerController extends Controller
{ 
    /**
     * Display the file tracker dashboard
     */
    public function index(Request $request) {
        $PageTitle = 'File Tracker';
        $PageDescription = 'Track and manage files within the system using RFID & Normal Modes';
        
        try {
            // Get summary statistics for the dashboard
            $stats = [
                'total_tracked_files' => FileTracking::count(),
                'active_files' => FileTracking::where('status', 'active')->count(),
                'overdue_files' => FileTracking::overdue()->count(),
                'checked_out_files' => FileTracking::where('status', 'checked_out')->count(),
                'recent_activities' => FileTracking::with('fileIndexing')
                    ->orderBy('updated_at', 'desc')
                    ->limit(10)
                    ->get()
            ];

            // Get file trackings for the table with pagination
            $fileTrackings = FileTracking::with(['fileIndexing', 'currentHandlerUser'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            // Add computed attributes
            $fileTrackings->getCollection()->transform(function ($tracking) {
                $tracking->is_overdue = $tracking->is_overdue;
                $tracking->days_until_due = $tracking->days_until_due;
                return $tracking;
            });

            // Get selected file for details sidebar
            $selectedFile = null;
            if ($request->has('selected')) {
                $selectedFile = FileTracking::with(['fileIndexing', 'currentHandlerUser'])
                    ->find($request->get('selected'));
                if ($selectedFile) {
                    $selectedFile->is_overdue = $selectedFile->is_overdue;
                    $selectedFile->days_until_due = $selectedFile->days_until_due;
                }
            }
            
            // If no specific file selected, get the first file (if any)
            if (!$selectedFile) {
                $selectedFile = $fileTrackings->first();
            }
            
            Log::info('File Tracker dashboard accessed', [
                'user_id' => auth()->id(),
                'stats' => $stats,
                'total_files' => $fileTrackings->total(),
                'selected_file_id' => $selectedFile ? $selectedFile->id : null
            ]);
            
            return view('filetracker.index', compact('PageTitle', 'PageDescription', 'stats', 'fileTrackings', 'selectedFile'));
            
        } catch (\Exception $e) {
            Log::error('Error loading File Tracker dashboard', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            // Fallback without stats
            $stats = [
                'total_tracked_files' => 0,
                'active_files' => 0,
                'overdue_files' => 0,
                'checked_out_files' => 0,
                'recent_activities' => collect()
            ];

            $fileTrackings = collect()->paginate(15);
            $selectedFile = null;
            
            return view('filetracker.index', compact('PageTitle', 'PageDescription', 'stats', 'fileTrackings', 'selectedFile'));
        }
    }

    /**
     * Display the print view for file tracker
     */
    public function print() {
        $PageTitle = 'File Tracker - Print View';
        $PageDescription = 'Print view for file tracking reports';
        
        try {
            // Get data for printing
            $trackings = FileTracking::with(['fileIndexing', 'currentHandlerUser'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            Log::info('File Tracker print view accessed', [
                'user_id' => auth()->id(),
                'total_records' => $trackings->count()
            ]);
            
            return view('filetracker.print', compact('PageTitle', 'PageDescription', 'trackings'));
            
        } catch (\Exception $e) {
            Log::error('Error loading File Tracker print view', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            $trackings = collect();
            return view('filetracker.print', compact('PageTitle', 'PageDescription', 'trackings'));
        }
    }

    /**
     * Display RFID scanner interface
     */
    public function rfidScanner() {
        $PageTitle = 'RFID Scanner';
        $PageDescription = 'Scan RFID tags to track file movements';
        
        Log::info('RFID Scanner accessed', ['user_id' => auth()->id()]);
        
        return view('filetracker.rfid-scanner', compact('PageTitle', 'PageDescription'));
    }

    /**
     * Display file tracking form
     */
    public function trackingForm($fileIndexingId = null) {
        $PageTitle = 'File Tracking Form';
        $PageDescription = 'Register or update file tracking information';
        
        $fileIndexing = null;
        $existingTracking = null;
        
        if ($fileIndexingId) {
            try {
                $fileIndexing = FileIndexing::find($fileIndexingId);
                if ($fileIndexing) {
                    $existingTracking = FileTracking::where('file_indexing_id', $fileIndexingId)->first();
                }
            } catch (\Exception $e) {
                Log::error('Error loading file for tracking form', [
                    'file_indexing_id' => $fileIndexingId,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        Log::info('File Tracking Form accessed', [
            'user_id' => auth()->id(),
            'file_indexing_id' => $fileIndexingId
        ]);
        
        return view('filetracker.tracking-form', compact(
            'PageTitle', 
            'PageDescription', 
            'fileIndexing', 
            'existingTracking'
        ));
    }

    /**
     * Display reports interface
     */
    public function reports() {
        $PageTitle = 'File Tracking Reports';
        $PageDescription = 'Generate and view file tracking reports';
        
        Log::info('File Tracking Reports accessed', ['user_id' => auth()->id()]);
        
        return view('filetracker.reports', compact('PageTitle', 'PageDescription'));
    }

    /**
     * Display overdue files
     */
    public function overdueFiles() {
        $PageTitle = 'Overdue Files';
        $PageDescription = 'Manage overdue file trackings';
        
        try {
            $overdueFiles = FileTracking::with(['fileIndexing', 'currentHandlerUser'])
                ->overdue()
                ->orderBy('due_date', 'asc')
                ->get();
                
            Log::info('Overdue Files view accessed', [
                'user_id' => auth()->id(),
                'overdue_count' => $overdueFiles->count()
            ]);
            
            return view('filetracker.overdue', compact('PageTitle', 'PageDescription', 'overdueFiles'));
            
        } catch (\Exception $e) {
            Log::error('Error loading overdue files', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            $overdueFiles = collect();
            return view('filetracker.overdue', compact('PageTitle', 'PageDescription', 'overdueFiles'));
        }
    }

    /**
     * Show the form for creating a new file tracking entry
     */
    public function create() {
        $PageTitle = 'Track New File';
        $PageDescription = 'Register a new file for tracking';
        
        Log::info('File Tracker create form accessed', ['user_id' => auth()->id()]);
        
        return view('filetracker.create', compact('PageTitle', 'PageDescription'));
    }

    /**
     * Store a newly created file tracking entry
     */
    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'file_indexing_id' => 'required|integer|exists:file_indexings,id',
                'rfid_tag' => 'nullable|string|max:100|unique:file_trackings,rfid_tag',
                'qr_code' => 'nullable|string|max:100|unique:file_trackings,qr_code',
                'current_location' => 'required|string|max:255',
                'current_holder' => 'nullable|string|max:255',
                'current_handler' => 'required|string|max:255',
                'date_received' => 'required|date',
                'due_date' => 'nullable|date|after:date_received',
                'status' => 'required|string|in:active,checked_out,overdue,returned,lost,archived',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Check if file is already being tracked
            $existingTracking = FileTracking::where('file_indexing_id', $validatedData['file_indexing_id'])->first();
            if ($existingTracking) {
                return redirect()->back()
                    ->withErrors(['file_indexing_id' => 'This file is already being tracked.'])
                    ->withInput();
            }

            // Create the tracking entry
            $tracking = FileTracking::create([
                'file_indexing_id' => $validatedData['file_indexing_id'],
                'rfid_tag' => $validatedData['rfid_tag'],
                'qr_code' => $validatedData['qr_code'],
                'current_location' => $validatedData['current_location'],
                'current_holder' => $validatedData['current_holder'],
                'current_handler' => $validatedData['current_handler'],
                'date_received' => $validatedData['date_received'],
                'due_date' => $validatedData['due_date'],
                'status' => $validatedData['status'],
            ]);

            // Add initial notes to movement history if provided
            if (!empty($validatedData['notes'])) {
                $tracking->addMovementEntry([
                    'action' => 'initial_notes',
                    'notes' => $validatedData['notes'],
                    'reason' => 'Initial tracking setup'
                ]);
            }

            Log::info('File tracking created successfully', [
                'tracking_id' => $tracking->id,
                'file_indexing_id' => $tracking->file_indexing_id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('filetracker.index', ['selected' => $tracking->id])
                ->with('success', 'File tracking created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating file tracking', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while creating the file tracking. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Search for files to track (AJAX endpoint)
     */
    public function searchFiles(Request $request) {
        try {
            $query = $request->get('query', '');
            
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Query must be at least 2 characters long'
                ]);
            }

            // Search in file_indexings table for files not already being tracked
            $files = FileIndexing::select('id', 'file_number', 'file_title', 'old_file_number', 'survey_plan_number')
                ->where(function($q) use ($query) {
                    $q->where('file_number', 'LIKE', "%{$query}%")
                      ->orWhere('file_title', 'LIKE', "%{$query}%")
                      ->orWhere('old_file_number', 'LIKE', "%{$query}%")
                      ->orWhere('survey_plan_number', 'LIKE', "%{$query}%");
                })
                ->whereNotIn('id', function($subQuery) {
                    $subQuery->select('file_indexing_id')
                             ->from('file_trackings')
                             ->whereNotNull('file_indexing_id');
                })
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $files->map(function($file) {
                    return [
                        'id' => $file->id,
                        'file_number' => $file->file_number,
                        'file_title' => $file->file_title,
                        'old_file_number' => $file->old_file_number,
                        'survey_plan_number' => $file->survey_plan_number,
                        'display_text' => $file->file_number . ' - ' . ($file->file_title ?? 'No Title')
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching files for tracking', [
                'error' => $e->getMessage(),
                'query' => $request->get('query'),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error searching files'
            ], 500);
        }
    }
}
