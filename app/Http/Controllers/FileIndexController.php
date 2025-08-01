<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\FileIndexing;
use App\Models\ApplicationMother;
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
            $validator = Validator::make($request->all(), [
                'file_number_type' => 'required|in:application,manual',
                'main_application_id' => 'nullable|integer|exists:sqlsrv.mother_applications,id',
                'file_number' => 'required|string|max:255',
                'file_title' => 'required|string|max:255',
                'land_use_type' => 'required|string|max:100',
                'plot_number' => 'nullable|string|max:100',
                'district' => 'nullable|string|max:100',
                'lga' => 'nullable|string|max:100',
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

            $validated = $validator->validated();

            // Check if file indexing already exists for this application
            if ($validated['file_number_type'] === 'application' && $validated['main_application_id']) {
                $existingIndex = FileIndexing::on('sqlsrv')
                    ->where('main_application_id', $validated['main_application_id'])
                    ->first();
                
                if ($existingIndex) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File indexing already exists for this application',
                        'redirect' => route('fileindexing.show', $existingIndex->id)
                    ], 409);
                }
            }

            // Create file indexing record
            $fileIndexing = FileIndexing::on('sqlsrv')->create([
                'main_application_id' => $validated['main_application_id'],
                'file_number' => $validated['file_number'],
                'file_title' => $validated['file_title'],
                'land_use_type' => $validated['land_use_type'],
                'plot_number' => $validated['plot_number'],
                'district' => $validated['district'],
                'lga' => $validated['lga'],
                'has_cofo' => $validated['has_cofo'] ?? false,
                'is_merged' => $validated['is_merged'] ?? false,
                'has_transaction' => $validated['has_transaction'] ?? false,
                'is_problematic' => $validated['is_problematic'] ?? false,
                'is_co_owned_plot' => $validated['is_co_owned_plot'] ?? false,
            ]);

            Log::info('File indexing created', [
                'file_indexing_id' => $fileIndexing->id,
                'file_number' => $fileIndexing->file_number,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File indexing created successfully!',
                'redirect' => route('scanning.index', ['file_indexing_id' => $fileIndexing->id])
            ]);

        } catch (Exception $e) {
            Log::error('Error creating file indexing', [
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
     */
    public function searchApplications(Request $request)
    {
        try {
            $search = $request->get('search', '');
            
            $applications = ApplicationMother::on('sqlsrv')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('file_indexings')
                        ->whereRaw('file_indexings.main_application_id = mother_applications.id');
                })
                ->where(function ($query) use ($search) {
                    $query->where('fileno', 'like', "%{$search}%")
                        ->orWhere('np_fileno', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%")
                        ->orWhere('corporate_name', 'like', "%{$search}%");
                })
                ->select('id', 'fileno', 'np_fileno', 'first_name', 'middle_name', 'surname', 'corporate_name', 'applicant_type', 'land_use', 'property_plot_no', 'property_district', 'property_lga')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'applications' => $applications->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'file_number' => $app->fileno ?? $app->np_fileno ?? "APP-{$app->id}",
                        'applicant_name' => $this->getApplicantName($app),
                        'land_use' => $app->land_use ?? 'Residential',
                        'plot_number' => $app->property_plot_no,
                        'district' => $app->property_district,
                        'lga' => $app->property_lga,
                    ];
                })
            ]);

        } catch (Exception $e) {
            Log::error('Error searching applications', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error searching applications'
            ], 500);
        }
    }

    /**
     * Get pending files count
     */
    private function getPendingFilesCount()
    {
        try {
            return ApplicationMother::on('sqlsrv')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('file_indexings')
                        ->whereRaw('file_indexings.main_application_id = mother_applications.id');
                })
                ->count();
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
     * Get file indexing list for other modules (AJAX)
     */
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
}