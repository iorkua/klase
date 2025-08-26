<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\FileIndexing;
use App\Models\ApplicationMother;
use App\Models\IndexedFileTracker;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FileIndexController extends Controller
{
    /**
     * Display the file indexing dashboard
     */
    public function index()
    {
        try {
            $PageTitle = 'File Indexing';
            $PageDescription = 'Digital File Index Management System';
            
            // Get statistics for dashboard
            $stats = [
                'pending_files' => $this->getPendingFilesCount(),
                'indexed_today' => $this->getIndexedTodayCount(),
                'total_indexed' => FileIndexing::on('sqlsrv')->count(),
            ];
            
            // Get recent file indexing records
            $recentIndexes = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            return view('fileindexing.index', compact('PageTitle', 'PageDescription', 'stats', 'recentIndexes'));
        } catch (Exception $e) {
            Log::error('Error loading file indexing dashboard', [
                'error' => $e->getMessage()
            ]);
            
            return view('fileindexing.index', [
                'PageTitle' => 'File Indexing',
                'PageDescription' => 'Digital File Index Management System',
                'stats' => ['pending_files' => 0, 'indexed_today' => 0, 'total_indexed' => 0],
                'recentIndexes' => collect()
            ]);
        }
    }

    /**
     * Show the form for creating a new file index
     */
    public function create()
    {
        try {
            $PageTitle = 'Create File Index';
            $PageDescription = 'Create a new file index record';
            
            // Get available applications for file number selection
            $availableApplications = $this->getAvailableApplications();
            
            return view('fileindexing.create', compact('PageTitle', 'PageDescription', 'availableApplications'));
        } catch (Exception $e) {
            Log::error('Error loading file indexing create form', [
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('fileindexing.index')
                ->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created file index
     */
    public function store(Request $request)
    {
        try {
            // Handle bulk entries from scanning interface
            if ($request->has('bulk_entries')) {
                return $this->storeBulkEntries($request);
            }

            // Handle single entry creation with smart file number selector
            $validator = Validator::make($request->all(), [
                'main_application_id' => 'nullable|integer',
                'subapplication_id' => 'nullable|integer',
                'file_number' => 'required|string|max:255',
                'file_number_id' => 'nullable|integer', // ID from fileNumber table when selected
                'file_title' => 'required|string|max:255',
                'st_fillno' => 'nullable|string|max:100',
                'serial_no' => 'nullable|string|max:100',
                'batch_no' => 'nullable|string|max:100',
                'shelf_location' => 'nullable|string|max:100',
                'land_use_type' => 'nullable|string|max:100',
                'plot_number' => 'nullable|string|max:100',
                'district' => 'nullable|string|max:100',
                'lga' => 'nullable|string|max:100',
                'has_cofo' => 'boolean',
                'is_merged' => 'boolean',
                'has_transaction' => 'boolean',
                'is_problematic' => 'boolean',
                'is_co_owned_plot' => 'boolean',
                'source' => 'nullable|string',
                'scanning_id' => 'nullable|integer',
                'extracted_metadata' => 'nullable|array',
                // Smart file number selector fields
                'source_file_id' => 'nullable|string',
                'file_number_source' => 'nullable|in:existing,manual',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Process file number ID from smart selector
            $fileNumberId = $this->processFileNumberId($validated);

            // Check for existing file indexing
            $existingIndex = $this->checkForExistingFileIndex($validated, $fileNumberId);
            
            if ($existingIndex) {
                return response()->json([
                    'success' => false,
                    'message' => 'File indexing already exists for this file number',
                    'redirect' => route('fileindexing.show', $existingIndex->id)
                ], 409);
            }

            // Create file indexing record
            $fileIndexingData = [
                'main_application_id' => $validated['main_application_id'] ?? null,
                'subapplication_id' => $validated['subapplication_id'] ?? null,
                'file_number' => $validated['file_number'],
                'file_number_id' => $fileNumberId,
                'file_title' => $validated['file_title'],
                'st_fillno' => $validated['st_fillno'] ?? null,
                'serial_no' => $validated['serial_no'] ?? null,
                'batch_no' => $validated['batch_no'] ?? null,
                'shelf_location' => $validated['shelf_location'] ?? null,
                'land_use_type' => $validated['land_use_type'] ?? 'Residential',
                'plot_number' => $validated['plot_number'],
                'district' => $validated['district'],
                'lga' => $validated['lga'],
                'has_cofo' => $validated['has_cofo'] ?? false,
                'is_merged' => $validated['is_merged'] ?? false,
                'has_transaction' => $validated['has_transaction'] ?? false,
                'is_problematic' => $validated['is_problematic'] ?? false,
                'is_co_owned_plot' => $validated['is_co_owned_plot'] ?? false,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $fileIndexing = FileIndexing::on('sqlsrv')->create($fileIndexingData);

            Log::info('File indexing created via smart selector', [
                'file_indexing_id' => $fileIndexing->id,
                'file_number' => $fileIndexing->file_number,
                'file_number_id' => $fileNumberId,
                'file_title' => $fileIndexing->file_title,
                'file_number_source' => $validated['file_number_source'] ?? 'unknown',
                'source_file_id' => $validated['source_file_id'] ?? null,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File indexing created successfully!',
                'file_indexing_id' => $fileIndexing->id,
                'redirect' => route('fileindexing.index')
            ]);

        } catch (Exception $e) {
            Log::error('Error creating file indexing via smart selector', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating file indexing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process file number ID from smart selector
     */
    private function processFileNumberId(array $validated)
    {
        // If file number was selected from dropdown, extract the ID
        if (!empty($validated['source_file_id']) && $validated['file_number_source'] === 'existing') {
            // Remove 'manual_' prefix if it exists (for manual entries)
            $sourceFileId = str_replace('manual_', '', $validated['source_file_id']);
            
            if (is_numeric($sourceFileId)) {
                return (int) $sourceFileId;
            }
        }

        // For manual entries, file_number_id remains null
        return null;
    }

    /**
     * Check for existing file indexing
     */
    private function checkForExistingFileIndex(array $validated, $fileNumberId = null)
    {
        // Check by file_number_id first if available (for selected files)
        if ($fileNumberId) {
            $existing = FileIndexing::on('sqlsrv')
                ->where('file_number_id', $fileNumberId)
                ->first();
            if ($existing) return $existing;
        }

        // Check by exact file number match
        $existing = FileIndexing::on('sqlsrv')
            ->where('file_number', $validated['file_number'])
            ->first();
        if ($existing) return $existing;

        // Check by application IDs if provided
        if (!empty($validated['main_application_id'])) {
            $existing = FileIndexing::on('sqlsrv')
                ->where('main_application_id', $validated['main_application_id'])
                ->first();
            if ($existing) return $existing;
        }

        if (!empty($validated['subapplication_id'])) {
            $existing = FileIndexing::on('sqlsrv')
                ->where('subapplication_id', $validated['subapplication_id'])
                ->first();
            if ($existing) return $existing;
        }

        return null;
    }

    /**
     * Store bulk file indexing entries from scanning interface
     */
    private function storeBulkEntries(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bulk_entries' => 'required|array|min:1',
                'bulk_entries.*.scanning_id' => 'required|integer',
                'bulk_entries.*.file_number' => 'required|string|max:255',
                'bulk_entries.*.file_title' => 'required|string|max:255',
                'bulk_entries.*.plot_number' => 'nullable|string|max:100',
                'bulk_entries.*.land_use_type' => 'nullable|string|max:100',
                'bulk_entries.*.district' => 'nullable|string|max:100',
                'bulk_entries.*.source' => 'nullable|string',
                'bulk_entries.*.extracted_metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $entries = $request->input('bulk_entries');
            $createdCount = 0;
            $errors = [];

            foreach ($entries as $entry) {
                try {
                    $fileIndexing = FileIndexing::on('sqlsrv')->create([
                        'file_number' => $entry['file_number'],
                        'file_title' => $entry['file_title'],
                        'plot_number' => $entry['plot_number'] ?? null,
                        'land_use_type' => $entry['land_use_type'] ?? 'Residential',
                        'district' => $entry['district'] ?? null,
                        'lga' => null,
                        'has_cofo' => false,
                        'is_merged' => false,
                        'has_transaction' => false,
                        'is_problematic' => false,
                        'is_co_owned_plot' => false,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);

                    $createdCount++;

                    Log::info('Bulk file indexing created', [
                        'file_indexing_id' => $fileIndexing->id,
                        'file_number' => $fileIndexing->file_number,
                        'scanning_id' => $entry['scanning_id'],
                        'source' => $entry['source'] ?? 'bulk_scanning_upload',
                        'created_by' => Auth::id()
                    ]);

                } catch (Exception $e) {
                    $errors[] = "Error creating entry for {$entry['file_title']}: " . $e->getMessage();
                    Log::error('Error creating bulk file indexing entry', [
                        'entry' => $entry,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => $createdCount > 0,
                'message' => "Successfully created {$createdCount} file indexing entries!",
                'created_count' => $createdCount,
                'errors' => $errors
            ]);

        } catch (Exception $e) {
            Log::error('Error creating bulk file indexing entries', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating bulk file indexing entries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified file index
     */
    public function show($id)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->findOrFail($id);

            $PageTitle = 'File Index Details';
            $PageDescription = 'View file index information and workflow status';

            return view('fileindexing.show', compact('PageTitle', 'PageDescription', 'fileIndexing'));
        } catch (Exception $e) {
            Log::error('Error loading file indexing details', [
                'file_indexing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fileindexing.index')
                ->with('error', 'File indexing record not found');
        }
    }

    /**
     * Show the form for editing the specified file index
     */
    public function edit($id)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')->findOrFail($id);
            
            $PageTitle = 'Edit File Index';
            $PageDescription = 'Update file index information';

            return view('fileindexing.edit', compact('PageTitle', 'PageDescription', 'fileIndexing'));
        } catch (Exception $e) {
            Log::error('Error loading file indexing edit form', [
                'file_indexing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fileindexing.index')
                ->with('error', 'File indexing record not found');
        }
    }

    /**
     * Update the specified file index
     */
    public function update(Request $request, $id)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'file_title' => 'required|string|max:255',
                'land_use_type' => 'required|string|max:100',
                'plot_number' => 'nullable|string|max:100',
                'district' => 'nullable|string|max:100',
                'lga' => 'nullable|string|max:100',
                'st_fillno' => 'nullable|string|max:100',
                'serial_no' => 'nullable|string|max:100',
                'batch_no' => 'nullable|string|max:100',
                'shelf_location' => 'nullable|string|max:100',
                'has_cofo' => 'boolean',
                'is_merged' => 'boolean',
                'has_transaction' => 'boolean',
                'is_problematic' => 'boolean',
                'is_co_owned_plot' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fileIndexing->update($validator->validated());

            Log::info('File indexing updated', [
                'file_indexing_id' => $id,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File indexing updated successfully!',
                'redirect' => route('scanning.index', ['file_indexing_id' => $fileIndexing->id])
            ]);

        } catch (Exception $e) {
            Log::error('Error updating file indexing', [
                'file_indexing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating file indexing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified file index
     */
    public function destroy($id)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')->findOrFail($id);
            
            // Check if there are related scannings or page typings
            if ($fileIndexing->scannings()->exists() || $fileIndexing->pagetypings()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete file indexing with associated documents or page typings'
                ], 409);
            }

            $fileIndexing->delete();

            Log::info('File indexing deleted', [
                'file_indexing_id' => $id,
                'deleted_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File indexing deleted successfully!'
            ]);

        } catch (Exception $e) {
            Log::error('Error deleting file indexing', [
                'file_indexing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting file indexing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available applications for file number selection
     */
    public function getAvailableApplications()
    {
        try {
            // Get applications that don't have file indexing yet
            $applications = ApplicationMother::on('sqlsrv')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('file_indexings')
                        ->whereRaw('file_indexings.main_application_id = mother_applications.id');
                })
                ->select('id', 'fileno', 'np_fileno', 'first_name', 'middle_name', 'surname', 'corporate_name', 'applicant_type')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();

            return $applications;
        } catch (Exception $e) {
            Log::error('Error getting available applications', [
                'error' => $e->getMessage()
            ]);

            return collect();
        }
    }

    /**
     * Search applications for file number selection (AJAX)
     * Searches both mother_applications and subapplications tables
     */
    public function searchApplications(Request $request)
    {
        try {
            $search = $request->get('search', '');
            
            // Search mother_applications table
            $motherApplications = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('file_indexings')
                        ->whereRaw('file_indexings.main_application_id = mother_applications.id');
                })
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('fileno', 'like', "%{$search}%")
                            ->orWhere('np_fileno', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                            ->orWhere('corporate_name', 'like', "%{$search}%")
                            ->orWhere('multiple_owners_names', 'like', "%{$search}%");
                    }
                })
                ->select(
                    'id',
                    'fileno',
                    'np_fileno', 
                    'first_name',
                    'middle_name',
                    'surname',
                    'applicant_title',
                    'corporate_name',
                    'rc_number',
                    'multiple_owners_names',
                    'applicant_type',
                    'land_use',
                    'property_plot_no',
                    'property_district',
                    'property_lga',
                    'property_state',
                    'created_at',
                    DB::raw("'mother' as source_table")
                )
                ->orderBy('created_at', 'desc')
                ->limit(25)
                ->get();

            // Search subapplications table with mother application data for land use
            $subApplications = DB::connection('sqlsrv')
                ->table('subapplications')
                ->leftJoin('mother_applications', 'subapplications.main_application_id', '=', 'mother_applications.id')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('file_indexings')
                        ->whereRaw('file_indexings.subapplication_id = subapplications.id');
                })
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('subapplications.fileno', 'like', "%{$search}%")
                            ->orWhere('subapplications.first_name', 'like', "%{$search}%")
                            ->orWhere('subapplications.surname', 'like', "%{$search}%")
                            ->orWhere('subapplications.corporate_name', 'like', "%{$search}%")
                            ->orWhere('subapplications.multiple_owners_names', 'like', "%{$search}%");
                    }
                })
                ->select(
                    'subapplications.id',
                    'subapplications.fileno',
                    DB::raw('NULL as np_fileno'),
                    'subapplications.first_name',
                    'subapplications.middle_name', 
                    'subapplications.surname',
                    'subapplications.applicant_title',
                    'subapplications.corporate_name',
                    'subapplications.rc_number',
                    'subapplications.multiple_owners_names',
                    'subapplications.applicant_type',
                    'mother_applications.land_use', // Get land use from mother application
                    'subapplications.unit_number',
                    'subapplications.block_number',
                    'subapplications.floor_number',
                    'mother_applications.property_district',
                    'mother_applications.property_lga',
                    'mother_applications.property_state',
                    'subapplications.created_at',
                    DB::raw("'sub' as source_table")
                )
                ->orderBy('subapplications.created_at', 'desc')
                ->limit(25)
                ->get();

            // Combine and format results
            $allApplications = collect($motherApplications)->merge($subApplications);

            return response()->json([
                'success' => true,
                'applications' => $allApplications->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'source_table' => $app->source_table,
                        'file_number' => $app->fileno ?? $app->np_fileno ?? "APP-{$app->id}",
                        'applicant_name' => $this->getApplicantNameFromRecord($app),
                        'application_type' => $app->source_table === 'mother' ? 'Primary ' : 'Unit',
                        'land_use' => $app->land_use ?? 'Residential',
                        'plot_number' => $app->property_plot_no ?? (isset($app->unit_number) && $app->unit_number ? "Unit {$app->unit_number}" : ''),
                        'district' => $app->property_district ?? '',
                        'lga' => $app->property_lga ?? '',
                        'status' => 'Pending Index',
                        'created_at' => $app->created_at ? date('M d, Y', strtotime($app->created_at)) : '',
                        // Include all original fields for debugging
                        'applicant_type' => $app->applicant_type,
                        'first_name' => $app->first_name,
                        'middle_name' => $app->middle_name,
                        'surname' => $app->surname,
                        'applicant_title' => $app->applicant_title,
                        'corporate_name' => $app->corporate_name,
                        'rc_number' => $app->rc_number,
                        'multiple_owners_names' => $app->multiple_owners_names,
                    ];
                })->sortByDesc('created_at')->values()
            ]);

        } catch (Exception $e) {
            Log::error('Error searching applications', [
                'error' => $e->getMessage(),
                'search' => $search
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error searching applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search file numbers for Select2 dropdown (AJAX endpoint)
     */
    public function searchFileNumbers(Request $request)
    {
        try {
            $search = trim($request->get('search', ''));
            $page = (int) $request->get('page', 1);
            $limit = min((int) $request->get('limit', 20), 50); // Max 50 results per page
            $offset = ($page - 1) * $limit;

            if (strlen($search) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search term must be at least 2 characters',
                    'files' => [],
                    'has_more' => false
                ]);
            }

            // Search in fileNumber table
            $query = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->select([
                    'id',
                    'kangisFileNo',
                    'mlsfNo', 
                    'NewKANGISFileNo'
                ])
                ->where(function ($q) use ($search) {
                    $q->where('kangisFileNo', 'like', "%{$search}%")
                      ->orWhere('mlsfNo', 'like', "%{$search}%")
                      ->orWhere('NewKANGISFileNo', 'like', "%{$search}%");
                })
                ->where(function ($q) {
                    $q->whereNotNull('kangisFileNo')
                      ->orWhereNotNull('mlsfNo')
                      ->orWhereNotNull('NewKANGISFileNo');
                })
                ->orderBy('id', 'desc')
                ->offset($offset)
                ->limit($limit + 1); // Get one extra to check if there are more

            $results = $query->get();
            $hasMore = $results->count() > $limit;
            
            if ($hasMore) {
                $results = $results->take($limit); // Remove the extra record
            }

            $files = $results->map(function ($record) {
                // Determine primary file number and type
                $fileNumber = '';
                $fileType = '';
                
                if (!empty($record->mlsfNo)) {
                    $fileNumber = $record->mlsfNo;
                    $fileType = 'MLS';
                } elseif (!empty($record->kangisFileNo)) {
                    $fileNumber = $record->kangisFileNo;
                    $fileType = 'KANGIS';
                } elseif (!empty($record->NewKANGISFileNo)) {
                    $fileNumber = $record->NewKANGISFileNo;
                    $fileType = 'New KANGIS';
                }

                return [
                    'id' => $record->id,
                    'file_number' => $fileNumber,
                    'kangis_file_no' => $record->kangisFileNo ?? '',
                    'mls_file_no' => $record->mlsfNo ?? '',
                    'new_kangis_file_no' => $record->NewKANGISFileNo ?? '',
                    'file_type' => $fileType
                ];
            })->filter(function ($file) {
                return !empty($file['file_number']); // Only include records with valid file numbers
            })->values();

            return response()->json([
                'success' => true,
                'files' => $files,
                'has_more' => $hasMore,
                'total_found' => $files->count(),
                'page' => $page
            ]);

        } catch (Exception $e) {
            Log::error('Error searching file numbers', [
                'search' => $search,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error searching file numbers: ' . $e->getMessage(),
                'files' => [],
                'has_more' => false
            ], 500);
        }
    }

    /**
     * Get pending files count
     */
    private function getPendingFilesCount()
    {
        try {
            $motherCount = ApplicationMother::on('sqlsrv')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('file_indexings')
                        ->whereRaw('file_indexings.main_application_id = mother_applications.id');
                })
                ->count();

            $subCount = DB::connection('sqlsrv')
                ->table('subapplications')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('file_indexings')
                        ->whereRaw('file_indexings.subapplication_id = subapplications.id');
                })
                ->count();

            return $motherCount + $subCount;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get indexed today count
     */
    private function getIndexedTodayCount()
    {
        try {
            return FileIndexing::on('sqlsrv')
                ->whereDate('created_at', today())
                ->count();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get applicant name from application
     */
    private function getApplicantName($application)
    {
        if ($application->applicant_type === 'individual') {
            return trim($application->first_name . ' ' . $application->middle_name . ' ' . $application->surname);
        } elseif ($application->applicant_type === 'corporate') {
            return $application->corporate_name;
        } else {
            return 'Multiple Applicants';
        }
    }

    /**
     * Get applicant name from database record (for both mother and unit applications)
     */
    private function getApplicantNameFromRecord($record)
    {
        if ($record->applicant_type === 'individual') {
            $nameParts = [];
            if (!empty($record->applicant_title)) $nameParts[] = $record->applicant_title;
            if (!empty($record->first_name)) $nameParts[] = $record->first_name;
            if (!empty($record->middle_name)) $nameParts[] = $record->middle_name;
            if (!empty($record->surname)) $nameParts[] = $record->surname;
            
            $name = implode(' ', $nameParts);
            return $name ?: 'Unknown Individual';
        } elseif ($record->applicant_type === 'corporate') {
            $corporateName = $record->corporate_name ?? 'Unknown Corporate';
            if (!empty($record->rc_number)) {
                $corporateName .= " (RC: {$record->rc_number})";
            }
            return $corporateName;
        } elseif ($record->applicant_type === 'multiple') {
            // Handle multiple owners
            if (!empty($record->multiple_owners_names)) {
                // Check if it's JSON encoded
                $decoded = json_decode($record->multiple_owners_names, true);
                if (is_array($decoded) && count($decoded) > 0) {
                    // If it's an array, join the first few names
                    if (count($decoded) > 2) {
                        return $decoded[0] . ' & ' . $decoded[1] . ' et al.';
                    } else {
                        return implode(' & ', $decoded);
                    }
                } else {
                    // If it's a plain string, return as is
                    return $record->multiple_owners_names;
                }
            }
            return 'Multiple Owners';
        } else {
            // Handle unknown types - try all possible name fields
            if (!empty($record->multiple_owners_names)) {
                $decoded = json_decode($record->multiple_owners_names, true);
                if (is_array($decoded) && count($decoded) > 0) {
                    return count($decoded) > 1 ? $decoded[0] . ' et al.' : $decoded[0];
                } else {
                    return $record->multiple_owners_names;
                }
            } elseif (!empty($record->corporate_name)) {
                return $record->corporate_name;
            } elseif (!empty($record->first_name) || !empty($record->surname)) {
                $nameParts = [];
                if (!empty($record->applicant_title)) $nameParts[] = $record->applicant_title;
                if (!empty($record->first_name)) $nameParts[] = $record->first_name;
                if (!empty($record->middle_name)) $nameParts[] = $record->middle_name;
                if (!empty($record->surname)) $nameParts[] = $record->surname;
                return implode(' ', $nameParts) ?: 'Unknown Applicant';
            } else {
                return 'Unknown Applicant';
            }
        }
    }

    /**
     * Get file indexing list for other modules (AJAX endpoint)
     */
    public function checkFileStatus(Request $request)
    {
        try {
            $fileno = trim($request->get('fileno', ''));
            if ($fileno === '') {
                return response()->json(['success' => false, 'message' => 'Missing fileno'], 422);
            }

            // Find file_indexings by file_number
            $fileIndex = FileIndexing::on('sqlsrv')
                ->with(['scannings', 'pagetypings'])
                ->where('file_number', $fileno)
                ->first();

            if (!$fileIndex) {
                // Try to resolve fileno from mother_applications or subapplications
                $mother = DB::connection('sqlsrv')->table('mother_applications')
                    ->where('fileno', $fileno)
                    ->orWhere('np_fileno', $fileno)
                    ->first();

                $sub = null;
                if (!$mother) {
                    $sub = DB::connection('sqlsrv')->table('subapplications')
                        ->where('fileno', $fileno)
                        ->first();
                }

                if ($mother) {
                    $fileIndex = FileIndexing::on('sqlsrv')
                        ->with(['scannings', 'pagetypings'])
                        ->where('main_application_id', $mother->id)
                        ->first();
                } elseif ($sub) {
                    $fileIndex = FileIndexing::on('sqlsrv')
                        ->with(['scannings', 'pagetypings'])
                        ->where('subapplication_id', $sub->id)
                        ->first();
                }
            }

            if (!$fileIndex) {
                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'message' => 'No file indexing record found for the provided file number'
                ]);
            }

            $typedCount = $fileIndex->pagetypings ? $fileIndex->pagetypings->count() : 0;
            $scannedCount = $fileIndex->scannings ? $fileIndex->scannings->count() : 0;
            $status = 'indexed';
            if ($typedCount > 0) {
                $status = 'typed';
            } elseif ($scannedCount > 0) {
                $status = 'scanned';
            }

            return response()->json([
                'success' => true,
                'exists' => true,
                'status' => $status,
                'file_indexing' => [
                    'id' => $fileIndex->id,
                    'file_number' => $fileIndex->file_number,
                    'file_title' => $fileIndex->file_title,
                    'plot_number' => $fileIndex->plot_number,
                    'district' => $fileIndex->district,
                    'lga' => $fileIndex->lga,
                    'land_use_type' => $fileIndex->land_use_type,
                    'has_cofo' => (bool) $fileIndex->has_cofo,
                    'is_merged' => (bool) $fileIndex->is_merged,
                    'has_transaction' => (bool) $fileIndex->has_transaction,
                    'is_co_owned_plot' => (bool) $fileIndex->is_co_owned_plot,
                    'scanning_count' => $scannedCount,
                    'page_typing_count' => $typedCount,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Error checking file status', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error checking file status'
            ], 500);
        }
    }

    public function getFileIndexingList(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $status = $request->get('status', 'all'); // all, indexed, scanned, typed
            
            $query = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('file_number', 'like', "%{$search}%")
                        ->orWhere('file_title', 'like', "%{$search}%")
                        ->orWhere('plot_number', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($status === 'indexed') {
                $query->whereDoesntHave('scannings');
            } elseif ($status === 'scanned') {
                $query->whereHas('scannings')
                    ->whereDoesntHave('pagetypings');
            } elseif ($status === 'typed') {
                $query->whereHas('pagetypings');
            }

            $fileIndexings = $query->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();

            return response()->json([
                'success' => true,
                'file_indexings' => $fileIndexings->map(function ($fi) {
                    return [
                        'id' => $fi->id,
                        'file_number' => $fi->file_number,
                        'file_title' => $fi->file_title,
                        'plot_number' => $fi->plot_number,
                        'district' => $fi->district,
                        'lga' => $fi->lga,
                        'status' => $fi->status,
                        'scanning_count' => $fi->scannings->count(),
                        'page_typing_count' => $fi->pagetypings->count(),
                        'created_at' => $fi->created_at->format('M d, Y H:i'),
                    ];
                })
            ]);

        } catch (Exception $e) {
            Log::error('Error getting file indexing list', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading file indexing list'
            ], 500);
        }
    }

    /**
     * Get pending files (applications without file indexing) - API endpoint
     */
    public function getPendingFiles(Request $request)
    {
        try {
            $search = $request->get('search', '');
            
            // Get mother applications without file indexing
            $motherApplications = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('file_indexings')
                        ->whereRaw('file_indexings.main_application_id = mother_applications.id');
                })
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('fileno', 'like', "%{$search}%")
                            ->orWhere('np_fileno', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                            ->orWhere('corporate_name', 'like', "%{$search}%");
                    }
                })
                ->select(
                    'id',
                    'fileno',
                    'np_fileno',
                    'first_name',
                    'middle_name',
                    'surname',
                    'applicant_title',
                    'corporate_name',
                    'applicant_type',
                    'land_use',
                    'property_plot_no',
                    'property_district',
                    'property_lga',
                    'created_at',
                    DB::raw("'mother' as source_table")
                )
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            // Get sub applications without file indexing
            $subApplications = DB::connection('sqlsrv')
                ->table('subapplications')
                ->leftJoin('mother_applications', 'subapplications.main_application_id', '=', 'mother_applications.id')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('file_indexings')
                        ->whereRaw('file_indexings.subapplication_id = subapplications.id');
                })
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('subapplications.fileno', 'like', "%{$search}%")
                            ->orWhere('subapplications.first_name', 'like', "%{$search}%")
                            ->orWhere('subapplications.surname', 'like', "%{$search}%")
                            ->orWhere('subapplications.corporate_name', 'like', "%{$search}%");
                    }
                })
                ->select(
                    'subapplications.id',
                    'subapplications.fileno',
                    DB::raw('NULL as np_fileno'),
                    'subapplications.first_name',
                    'subapplications.middle_name',
                    'subapplications.surname',
                    'subapplications.applicant_title',
                    'subapplications.corporate_name',
                    'subapplications.applicant_type',
                    'mother_applications.land_use',
                    'subapplications.unit_number',
                    'mother_applications.property_district',
                    'mother_applications.property_lga',
                    'subapplications.created_at',
                    DB::raw("'sub' as source_table")
                )
                ->orderBy('subapplications.created_at', 'desc')
                ->limit(50)
                ->get();

            // Combine and format results
            $allApplications = collect($motherApplications)->merge($subApplications);

            $pendingFiles = $allApplications->map(function ($app) {
                return [
                    'id' => $app->source_table . '-' . $app->id, // Prefix with table to avoid conflicts
                    'application_id' => $app->id,
                    'source_table' => $app->source_table,
                    'fileNumber' => $app->fileno ?? $app->np_fileno ?? "APP-{$app->id}",
                    'name' => $this->getApplicantNameFromRecord($app),
                    'type' => $app->source_table === 'mother' ? 'Primary Application' : 'Unit Application',
                    'source' => 'Application',
                    'date' => $app->created_at ? date('Y-m-d', strtotime($app->created_at)) : date('Y-m-d'),
                    'landUseType' => $app->land_use ?? 'Residential',
                    'district' => $app->property_district ?? 'Unknown',
                    'lga' => $app->property_lga ?? 'Kano Municipal',
                    'hasCofo' => false,
                ];
            })->sortByDesc('date')->values();

            return response()->json([
                'success' => true,
                'pending_files' => $pendingFiles
            ]);

        } catch (Exception $e) {
            Log::error('Error getting pending files', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading pending files'
            ], 500);
        }
    }

    /**
     * Get indexed files - API endpoint
     */
    public function getIndexedFiles(Request $request)
    {
        try {
            $search = $request->get('search', '');
            
            $query = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('file_number', 'like', "%{$search}%")
                        ->orWhere('file_title', 'like', "%{$search}%")
                        ->orWhere('plot_number', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%");
                });
            }

            $fileIndexings = $query->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();

            $indexedFiles = $fileIndexings->map(function ($fi) {
                $scannedCount = $fi->scannings->count();
                $typedCount = $fi->pagetypings->count();
                
                $source = 'Indexed';
                if ($typedCount > 0) {
                    $source = 'Indexed & Typed';
                } elseif ($scannedCount > 0) {
                    $source = 'Indexed & Scanned';
                }

                return [
                    'id' => $fi->id,
                    'fileNumber' => $fi->file_number,
                    'name' => $fi->file_title,
                    'type' => $this->getDocumentType($fi),
                    'source' => $source,
                    'date' => $fi->created_at->format('Y-m-d'),
                    'landUseType' => $fi->land_use_type ?? 'Residential',
                    'district' => $fi->district ?? 'Unknown',
                    'lga' => $fi->lga ?? 'Kano Municipal',
                    'hasCofo' => (bool) $fi->has_cofo,
                    'plot_number' => $fi->plot_number,
                    'scanning_count' => $scannedCount,
                    'page_typing_count' => $typedCount,
                    'is_merged' => (bool) $fi->is_merged,
                    'has_transaction' => (bool) $fi->has_transaction,
                    'is_problematic' => (bool) $fi->is_problematic,
                    'is_co_owned_plot' => (bool) $fi->is_co_owned_plot,
                ];
            });

            return response()->json([
                'success' => true,
                'indexed_files' => $indexedFiles
            ]);

        } catch (Exception $e) {
            Log::error('Error getting indexed files', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading indexed files'
            ], 500);
        }
    }

    /**
     * Get document type based on file indexing data
     */
    private function getDocumentType($fileIndexing)
    {
        if ($fileIndexing->has_cofo) {
            return 'Certificate of Occupancy';
        } elseif ($fileIndexing->land_use_type === 'Commercial') {
            return 'Commercial Document';
        } elseif ($fileIndexing->land_use_type === 'Industrial') {
            return 'Industrial Document';
        } else {
            return 'Property Document';
        }
    }

    /**
     * Generate tracking sheet for a single file
     */
    public function generateTrackingSheet($id)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->findOrFail($id);

            // Get or create tracking record
            $tracker = $this->getOrCreateTracker($fileIndexing);

            $PageTitle = 'File Tracking Sheet';
            $PageDescription = 'Generate tracking sheet for file indexing record';
            $settings = settings(); // Add missing settings variable

            return view('fileindexing.tracking-sheet', compact('PageTitle', 'PageDescription', 'fileIndexing', 'tracker', 'settings'));
        } catch (Exception $e) {
            Log::error('Error generating tracking sheet', [
                'file_indexing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fileindexing.index')
                ->with('error', 'Error generating tracking sheet: ' . $e->getMessage());
        }
    }

    /**
     * Print tracking sheet for a single file
     */
    public function printTrackingSheet($id)
    {
        try {
            $fileIndexing = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->findOrFail($id);

            // Get or create tracking record
            $tracker = $this->getOrCreateTracker($fileIndexing);
            
            // Update print count and timestamp
            $tracker->incrementPrintCount();

            $PageTitle = 'Print Tracking Sheet';
            $PageDescription = 'Print tracking sheet for file indexing record';

            return view('fileindexing.print-tracking-sheet', compact('PageTitle', 'PageDescription', 'fileIndexing', 'tracker'));
        } catch (Exception $e) {
            Log::error('Error printing tracking sheet', [
                'file_indexing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fileindexing.index')
                ->with('error', 'Error printing tracking sheet: ' . $e->getMessage());
        }
    }

    /**
     * Generate batch tracking sheets for multiple files
     */
    public function generateBatchTrackingSheet(Request $request)
    {
        try {
            $fileIds = $request->get('files', '');
            
            if (empty($fileIds)) {
                return redirect()->route('fileindexing.index')
                    ->with('error', 'No files selected for batch tracking sheet generation');
            }

            $fileIdsArray = explode(',', $fileIds);
            
            $fileIndexings = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->whereIn('id', $fileIdsArray)
                ->get();

            if ($fileIndexings->isEmpty()) {
                return redirect()->route('fileindexing.index')
                    ->with('error', 'No valid files found for tracking sheet generation');
            }

            // Create or get trackers for all files
            $trackersData = [];
            foreach ($fileIndexings as $fileIndexing) {
                $tracker = $this->getOrCreateTracker($fileIndexing);
                $tracker->incrementPrintCount(); // Count batch print
                $trackersData[$fileIndexing->id] = $tracker;
            }

            $PageTitle = 'Batch Tracking Sheets';
            $PageDescription = 'Generate tracking sheets for multiple file indexing records';

            return view('fileindexing.batch-tracking-sheet', compact('PageTitle', 'PageDescription', 'fileIndexings', 'trackersData'));
        } catch (Exception $e) {
            Log::error('Error generating batch tracking sheets', [
                'file_ids' => $request->get('files', ''),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fileindexing.index')
                ->with('error', 'Error generating batch tracking sheets: ' . $e->getMessage());
        }
    }

    /**
     * Get or create tracking record for file indexing
     */
    private function getOrCreateTracker($fileIndexing)
    {
        $tracker = IndexedFileTracker::on('sqlsrv')
            ->where('file_indexing_id', $fileIndexing->id)
            ->first();

        if (!$tracker) {
            // Create new tracking record
            $tracker = IndexedFileTracker::on('sqlsrv')->create([
                'file_indexing_id' => $fileIndexing->id,
                'tracking_id' => $this->generateUniqueTrackingId($fileIndexing->id),
                'current_location' => 'File Indexing Department',
                'current_handler' => Auth::user()->name ?? 'System User',
                'current_department' => 'File Indexing Department',
                'status' => 'Active',
                'priority' => 'Normal',
                'sheet_generated_at' => now(),
                'movement_history' => $this->createInitialMovementHistory($fileIndexing),
            ]);

            Log::info('Created new tracking record', [
                'file_indexing_id' => $fileIndexing->id,
                'tracking_id' => $tracker->tracking_id,
                'created_by' => Auth::id()
            ]);
        }

        return $tracker;
    }

    /**
     * Generate unique tracking ID with format TRK-XXXXXXXX-XXXXX
     */
    private function generateUniqueTrackingId($fileIndexingId)
    {
        // Generate random alphanumeric segments
        $segment1 = $this->generateRandomAlphanumeric(8); // 8 characters like MESALDX6
        $segment2 = $this->generateRandomAlphanumeric(5); // 5 characters like QWB08
        
        $baseId = "TRK-{$segment1}-{$segment2}";
        
        // Check if ID already exists and regenerate if needed
        $counter = 0;
        $trackingId = $baseId;
        
        while (IndexedFileTracker::on('sqlsrv')->where('tracking_id', $trackingId)->exists()) {
            $counter++;
            // If collision occurs, generate new segments
            $segment1 = $this->generateRandomAlphanumeric(8);
            $segment2 = $this->generateRandomAlphanumeric(5);
            $trackingId = "TRK-{$segment1}-{$segment2}";
            
            // Prevent infinite loop
            if ($counter > 100) {
                break;
            }
        }
        
        return $trackingId;
    }

    /**
     * Generate random alphanumeric string
     */
    private function generateRandomAlphanumeric($length)
    {
        $characters = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789'; // Exclude O, 0 for clarity
        $result = '';
        $charactersLength = strlen($characters);
        
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $result;
    }

    /**
     * Create initial movement history for new tracking record
     */
    private function createInitialMovementHistory($fileIndexing)
    {
        $history = [];
        
        // Add file indexing entry
        $history[] = [
            'date' => $fileIndexing->created_at->format('Y-m-d'),
            'time' => $fileIndexing->created_at->format('g:i A'),
            'location' => 'File Indexing System',
            'handler' => 'System User',
            'action' => 'File indexed and registered',
            'method' => 'Digital',
            'notes' => 'File information captured in EDMS',
            'timestamp' => $fileIndexing->created_at->toISOString(),
        ];

        // Add scanning entries if exist
        if ($fileIndexing->scannings && $fileIndexing->scannings->count() > 0) {
            $latestScanning = $fileIndexing->scannings->sortBy('created_at')->last();
            $history[] = [
                'date' => $latestScanning->created_at->format('Y-m-d'),
                'time' => $latestScanning->created_at->format('g:i A'),
                'location' => 'Scanning Department',
                'handler' => 'Scanner Operator',
                'action' => 'Document scanning completed',
                'method' => 'Digital Scan',
                'notes' => $fileIndexing->scannings->count() . ' documents scanned',
                'timestamp' => $latestScanning->created_at->toISOString(),
            ];
        }

        // Add page typing entries if exist
        if ($fileIndexing->pagetypings && $fileIndexing->pagetypings->count() > 0) {
            $latestPageTyping = $fileIndexing->pagetypings->sortBy('created_at')->last();
            $history[] = [
                'date' => $latestPageTyping->created_at->format('Y-m-d'),
                'time' => $latestPageTyping->created_at->format('g:i A'),
                'location' => 'Page Typing Department',
                'handler' => 'Data Entry Operator',
                'action' => 'Page typing completed',
                'method' => 'Manual Input',
                'notes' => $fileIndexing->pagetypings->count() . ' pages typed',
                'timestamp' => $latestPageTyping->created_at->toISOString(),
            ];
        }

        // Sort by timestamp (newest first)
        usort($history, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return $history;
    }

    /**
     * Update file tracking location (AJAX endpoint)
     */
    public function updateTrackingLocation(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'location' => 'required|string|max:255',
                'handler' => 'required|string|max:255',
                'action' => 'required|string|max:255',
                'method' => 'nullable|string|max:50',
                'notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fileIndexing = FileIndexing::on('sqlsrv')->findOrFail($id);
            $tracker = $this->getOrCreateTracker($fileIndexing);

            // Add movement record
            $tracker->addMovementRecord(
                $request->location,
                $request->handler,
                $request->action,
                $request->method ?? 'Manual',
                $request->notes ?? ''
            );

            return response()->json([
                'success' => true,
                'message' => 'Tracking location updated successfully',
                'tracker' => [
                    'current_location' => $tracker->current_location,
                    'current_handler' => $tracker->current_handler,
                    'last_location_update' => $tracker->last_location_update->format('M d, Y g:i A'),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error updating tracking location', [
                'file_indexing_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating tracking location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show smart batch tracking interface
     */
    public function batchTrackingInterface(Request $request)
    {
        try {
            $fileIds = $request->get('files', '');
            
            if (empty($fileIds)) {
                return redirect()->route('fileindexing.index')
                    ->with('error', 'No files selected for batch tracking operations');
            }

            $fileIdsArray = explode(',', $fileIds);
            
            $selectedFiles = FileIndexing::on('sqlsrv')
                ->with(['mainApplication', 'scannings', 'pagetypings'])
                ->whereIn('id', $fileIdsArray)
                ->get()
                ->map(function ($fi) {
                    return [
                        'id' => $fi->id,
                        'file_number' => $fi->file_number,
                        'file_title' => $fi->file_title,
                        'plot_number' => $fi->plot_number,
                        'district' => $fi->district,
                        'land_use_type' => $fi->land_use_type,
                        'created_at' => $fi->created_at,
                        'updated_at' => $fi->updated_at,
                        'scanning_count' => $fi->scannings->count(),
                        'page_typing_count' => $fi->pagetypings->count(),
                    ];
                });

            if ($selectedFiles->isEmpty()) {
                return redirect()->route('fileindexing.index')
                    ->with('error', 'No valid files found for batch tracking operations');
            }

            $PageTitle = 'Smart Batch Tracking Interface';
            $PageDescription = 'Manage batch tracking operations and movement history';

            return view('fileindexing.batch-tracking-interface', compact(
                'PageTitle', 
                'PageDescription', 
                'selectedFiles'
            ));

        } catch (Exception $e) {
            Log::error('Error loading batch tracking interface', [
                'file_ids' => $request->get('files', ''),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fileindexing.index')
                ->with('error', 'Error loading batch tracking interface: ' . $e->getMessage());
        }
    }

    /**
     * Process bulk movement update (AJAX endpoint)
     */
    public function bulkMovementUpdate(Request $request)
    {
        try {
            // ...existing code...
            
            $files = $request->input('files', []);
            $location = $request->input('location');
            $handler = $request->input('handler');
            $status = $request->input('status');
            $priority = $request->input('priority');
            $reason = $request->input('reason');
            $notes = $request->input('notes');

            if (empty($files) || !$location || !$handler || !$status || !$priority) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields: location, handler, status, and priority are required'
                ]);
            }

            $updated = 0;
            $errors = [];

            foreach ($files as $fileId) {
                try {
                    $file = FileIndex::find($fileId);
                    if (!$file) {
                        $errors[] = "File ID {$fileId} not found";
                        continue;
                    }

                    // Update file location and tracking info
                    $file->current_location = $location;
                    $file->handler = $handler;
                    $file->status = $status;
                    $file->priority = $priority;
                    $file->movement_reason = $reason;
                    $file->last_movement_date = now();
                    $file->save();

                    // Create movement log entry
                    FileMovementLog::create([
                        'file_index_id' => $file->id,
                        'file_number' => $file->file_number,
                        'previous_location' => $file->getOriginal('current_location') ?? 'Unknown',
                        'new_location' => $location,
                        'handler' => $handler,
                        'status' => $status,
                        'priority' => $priority,
                        'reason' => $reason,
                        'notes' => $notes,
                        'moved_by' => Auth::id(),
                        'moved_at' => now()
                    ]);

                    $updated++;
                } catch (Exception $e) {
                    $errors[] = "Error updating file {$fileId}: " . $e->getMessage();
                }
            }

            if ($updated > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully updated {$updated} file(s)" . 
                                ($errors ? ". Errors: " . implode(', ', $errors) : '')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No files were updated. Errors: ' . implode(', ', $errors)
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get movement history for files (AJAX endpoint)
     */
    public function getMovementHistory(Request $request)
    {
        try {
            $fileIds = $request->get('files', []);
            
            if (empty($fileIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file IDs provided'
                ], 422);
            }

            if (is_string($fileIds)) {
                $fileIds = explode(',', $fileIds);
            }

            $trackers = IndexedFileTracker::on('sqlsrv')
                ->with('fileIndexing')
                ->whereIn('file_indexing_id', $fileIds)
                ->get();

            $movementHistory = [];

            foreach ($trackers as $tracker) {
                $fileIndexing = $tracker->fileIndexing;
                $history = $tracker->movement_history ?? [];

                foreach ($history as $movement) {
                    $movementHistory[] = [
                        'file_id' => $fileIndexing->id,
                        'file_number' => $fileIndexing->file_number,
                        'file_title' => $fileIndexing->file_title,
                        'tracking_id' => $tracker->tracking_id,
                        'date' => $movement['date'] ?? '',
                        'time' => $movement['time'] ?? '',
                        'location' => $movement['location'] ?? '',
                        'handler' => $movement['handler'] ?? '',
                        'action' => $movement['action'] ?? '',
                        'method' => $movement['method'] ?? '',
                        'notes' => $movement['notes'] ?? '',
                        'timestamp' => $movement['timestamp'] ?? '',
                        'current_location' => $tracker->current_location,
                        'current_handler' => $tracker->current_handler,
                        'status' => $tracker->status,
                        'priority' => $tracker->priority,
                    ];
                }
            }

            // Sort by timestamp (newest first)
            usort($movementHistory, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });

            return response()->json([
                'success' => true,
                'movement_history' => $movementHistory
            ]);

        } catch (Exception $e) {
            Log::error('Error getting movement history', [
                'file_ids' => $fileIds,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading movement history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export movement history (AJAX endpoint)
     */
    public function exportMovementHistory(Request $request)
    {
        try {
            $fileIds = $request->get('files', []);
            $format = $request->get('format', 'csv'); // csv, excel, pdf
            
            if (empty($fileIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file IDs provided'
                ], 422);
            }

            if (is_string($fileIds)) {
                $fileIds = explode(',', $fileIds);
            }

            // Get movement history data
            $historyResponse = $this->getMovementHistory($request);
            $historyData = json_decode($historyResponse->getContent(), true);

            if (!$historyData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading movement history for export'
                ], 500);
            }

            $movements = $historyData['movement_history'];

            // For now, return CSV format
            $csvData = "File Number,File Title,Tracking ID,Date,Time,Location,Handler,Action,Method,Notes,Current Location,Status,Priority\n";
            
            foreach ($movements as $movement) {
                $csvData .= implode(',', [
                    '"' . ($movement['file_number'] ?? '') . '"',
                    '"' . ($movement['file_title'] ?? '') . '"',
                    '"' . ($movement['tracking_id'] ?? '') . '"',
                    '"' . ($movement['date'] ?? '') . '"',
                    '"' . ($movement['time'] ?? '') . '"',
                    '"' . ($movement['location'] ?? '') . '"',
                    '"' . ($movement['handler'] ?? '') . '"',
                    '"' . ($movement['action'] ?? '') . '"',
                    '"' . ($movement['method'] ?? '') . '"',
                    '"' . ($movement['notes'] ?? '') . '"',
                    '"' . ($movement['current_location'] ?? '') . '"',
                    '"' . ($movement['status'] ?? '') . '"',
                    '"' . ($movement['priority'] ?? '') . '"',
                ]) . "\n";
            }

            $filename = 'movement_history_' . date('Y-m-d_H-i-s') . '.csv';

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (Exception $e) {
            Log::error('Error exporting movement history', [
                'file_ids' => $fileIds,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error exporting movement history: ' . $e->getMessage()
            ], 500);
        }
    }
}