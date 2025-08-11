<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CertificationController extends Controller
{
    /**
     * Show certification management page
     */
    public function index()
    {
        return view('recertification.certification');
    }

    /**
     * Get certification data for the certification management page
     */
    public function getCertificationData(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv')->table('recertification_applications');
            $applications = $query->get();

            // Format data for the certification table
            $data = $applications->map(function($app) {
                // Determine applicant name based on type
                $applicantName = '';
                if ($app->applicant_type === 'Corporate') {
                    $applicantName = $app->organisation_name ?? 'N/A';
                } else {
                    $applicantName = trim(($app->surname ?? '') . ' ' . ($app->first_name ?? ''));
                    if (empty($applicantName)) {
                        $applicantName = 'N/A';
                    }
                }

                // Format plot details
                $plotDetails = '';
                if ($app->plot_number) {
                    $plotDetails .= 'Plot: ' . $app->plot_number;
                }
                if ($app->layout_district) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . $app->layout_district;
                }
                if ($app->plot_size) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . 'Size: ' . $app->plot_size;
                }
                if (empty($plotDetails)) {
                    $plotDetails = 'N/A';
                }

                // Check if certificate is generated
                $certificateGenerated = $app->certificate_generated ?? false;

                return [
                    'id' => $app->id,
                    'file_number' => $app->file_number ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'lga_name' => $app->lga_name ?? 'N/A',
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A',
                    'certificate_generated' => $certificateGenerated,
                    'certificate_generated_date' => $app->certificate_generated_date ? date('d M Y', strtotime($app->certificate_generated_date)) : null,
                    'cofo_number' => $app->cofo_number ?? 'N/A'
                ];
            });

            // Calculate statistics
            $total = $data->count();
            $generated = $data->where('certificate_generated', true)->count();
            $pending = $total - $generated;
            $thisMonth = $data->where('created_at', '>=', now()->startOfMonth()->format('d M Y'))->count();

            $statistics = [
                'total' => $total,
                'generated' => $generated,
                'pending' => $pending,
                'thisMonth' => $thisMonth
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching certification data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'generated' => 0, 'pending' => 0, 'thisMonth' => 0],
                'error' => 'Failed to fetch certification data'
            ]);
        }
    }

    /**
     * View Certificate of Recognition (CoR)
     */
    public function viewCoR($id)
    {
        try {
            $application = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                abort(404, 'Application not found');
            }

            $PageTitle = 'Certificate of Recognition';
            $PageDescription = 'View Certificate of Recognition for Application ' . ($application->file_number ?? 'N/A');

            return view('recertification.cor', compact('PageTitle', 'PageDescription', 'application'));

        } catch (\Exception $e) {
            Log::error('Error viewing CoR', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('recertification.certification')->with('error', 'Failed to load Certificate of Recognition');
        }
    }

    /**
     * Generate Certificate of Occupancy Front Page
     */
    public function generateCofoFrontPage($id)
    {
        try {
            $application = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                return response()->json(['error' => 'Application not found'], 404);
            }

            // Update the application to mark certificate as generated
            DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->update([
                    'certificate_generated' => true,
                    'certificate_generated_date' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificate of Occupancy Front Page generated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating CofO Front Page', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to generate CofO Front Page'], 500);
        }
    }

    /**
     * View Certificate of Occupancy Front Page
     */
    public function viewCofoFrontPage($id)
    {
        try {
            $application = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                abort(404, 'Application not found');
            }

            $PageTitle = 'Certificate of Occupancy - Front Page';
            $PageDescription = 'View CofO Front Page for Application ' . ($application->file_number ?? 'N/A');

            return view('recertification.cofo-front-page', compact('PageTitle', 'PageDescription', 'application'));

        } catch (\Exception $e) {
            Log::error('Error viewing CofO Front Page', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('recertification.certification')->with('error', 'Failed to load CofO Front Page');
        }
    }

    /**
     * View Title Development Plan (TDP)
     */
    public function viewTDP($id)
    {
        try {
            $application = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                abort(404, 'Application not found');
            }

            $PageTitle = 'Title Development Plan';
            $PageDescription = 'View TDP for Application ' . ($application->file_number ?? 'N/A');

            return view('recertification.tdp', compact('PageTitle', 'PageDescription', 'application'));

        } catch (\Exception $e) {
            Log::error('Error viewing TDP', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('recertification.certification')->with('error', 'Failed to load Title Development Plan');
        }
    }

    /**
     * View Certificate of Occupancy
     */
    public function viewCofo($id)
    {
        try {
            $application = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                abort(404, 'Application not found');
            }

            $PageTitle = 'Certificate of Occupancy';
            $PageDescription = 'View CofO for Application ' . ($application->file_number ?? 'N/A');

            return view('recertification.cofo', compact('PageTitle', 'PageDescription', 'application'));

        } catch (\Exception $e) {
            Log::error('Error viewing CofO', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('recertification.certification')->with('error', 'Failed to load Certificate of Occupancy');
        }
    }
 

    /**
     * Show vetting sheet page
     */
    public function vettingSheet()
    {
        return view('recertification.vetting_sheet');
    }

    /**
     * Get vetting data for the vetting sheet page
     */
    public function getVettingData(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv')->table('recertification_applications');
            $applications = $query->get();

            // Format data for the vetting table
            $data = $applications->map(function($app) {
                // Determine applicant name based on type
                $applicantName = '';
                if ($app->applicant_type === 'Corporate') {
                    $applicantName = $app->organisation_name ?? 'N/A';
                } else {
                    $applicantName = trim(($app->surname ?? '') . ' ' . ($app->first_name ?? ''));
                    if (empty($applicantName)) {
                        $applicantName = 'N/A';
                    }
                }

                // Format plot details
                $plotDetails = '';
                if ($app->plot_number) {
                    $plotDetails .= 'Plot: ' . $app->plot_number;
                }
                if ($app->layout_district) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . $app->layout_district;
                }
                if ($app->plot_size) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . 'Size: ' . $app->plot_size;
                }
                if (empty($plotDetails)) {
                    $plotDetails = 'N/A';
                }

                return [
                    'id' => $app->id,
                    'file_number' => $app->file_number ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'lga_name' => $app->lga_name ?? 'N/A',
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A',
                    'vetting_status' => $app->vetting_status ?? 'pending'
                ];
            });

            // Calculate statistics
            $total = $data->count();
            $vetted = $data->where('vetting_status', 'vetted')->count();
            $pending = $total - $vetted;
            $thisMonth = $data->where('created_at', '>=', now()->startOfMonth()->format('d M Y'))->count();

            $statistics = [
                'total' => $total,
                'vetted' => $vetted,
                'pending' => $pending,
                'thisMonth' => $thisMonth
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching vetting data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'vetted' => 0, 'pending' => 0, 'thisMonth' => 0],
                'error' => 'Failed to fetch vetting data'
            ]);
        }
    }

    /**
     * Show DG's list page
     */
    public function dgList()
    {
        return view('recertification.dg_list');
    }

    /**
     * Get DG data for the DG's list page
     */
    public function getDGData(Request $request)
    {
        try {
            Log::info('DG Data Request - Simple Version Started');
            
            // Test database connection
            try {
                DB::connection('sqlsrv')->getPdo();
                Log::info('Database connection test passed');
            } catch (\Exception $e) {
                Log::error('Database connection failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'statistics' => ['total' => 0, 'approved' => 0, 'pending' => 0, 'thisMonth' => 0],
                    'error' => 'Database connection failed'
                ], 500);
            }
            
            // Fetch applications
            $applications = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->get();
                
            Log::info('Applications fetched', ['count' => $applications->count()]);
            
            // Simple data mapping - no complex logic
            $data = [];
            foreach ($applications as $app) {
                try {
                    // Basic applicant name
                    $applicantName = 'N/A';
                    if ($app->applicant_type === 'Corporate' && !empty($app->organisation_name)) {
                        $applicantName = $app->organisation_name;
                    } elseif (!empty($app->surname) || !empty($app->first_name)) {
                        $applicantName = trim(($app->surname ?? '') . ' ' . ($app->first_name ?? ''));
                    }
                    
                    // Basic plot details
                    $plotDetails = 'N/A';
                    if (!empty($app->plot_number)) {
                        $plotDetails = 'Plot: ' . $app->plot_number;
                        if (!empty($app->layout_district)) {
                            $plotDetails .= ', ' . $app->layout_district;
                        }
                    }
                    
                    // Simple date formatting
                    $createdDate = 'N/A';
                    if (!empty($app->created_at)) {
                        try {
                            $createdDate = date('d M Y', strtotime($app->created_at));
                        } catch (\Exception $e) {
                            $createdDate = 'N/A';
                        }
                    }
                    
                    // DG approval status
                    $dgApproval = !empty($app->dg_approval) && $app->dg_approval == 1;
                    
                    $data[] = [
                        'id' => $app->id ?? 0,
                        'file_number' => $app->file_number ?? 'N/A',
                        'applicant_name' => $applicantName,
                        'applicant_type' => $app->applicant_type ?? 'N/A',
                        'plot_details' => $plotDetails,
                        'lga_name' => $app->lga_name ?? 'N/A',
                        'submitted_to_dg_date' => $createdDate,
                        'dg_status' => $dgApproval ? 'approved' : 'pending',
                        'dg_approval' => $dgApproval,
                        'created_at' => $createdDate,
                        // All prerequisites set to true for development
                        'acknowledgement_generated' => true,
                        'verification_generated' => true,
                        'gis_captured' => true,
                        'vetting_generated' => true,
                        'edms_captured' => true,
                        'cofo_front_generated' => true
                    ];
                    
                } catch (\Exception $e) {
                    Log::warning('Error processing application', ['id' => $app->id ?? 'unknown', 'error' => $e->getMessage()]);
                    // Skip this record and continue
                    continue;
                }
            }
            
            Log::info('Data processing completed', ['processed_count' => count($data)]);
            
            // Simple statistics
            $total = count($data);
            $approved = 0;
            $thisMonth = 0;
            
            foreach ($data as $item) {
                if ($item['dg_approval']) {
                    $approved++;
                }
                // Simple this month calculation
                if ($item['created_at'] !== 'N/A') {
                    try {
                        $itemDate = \DateTime::createFromFormat('d M Y', $item['created_at']);
                        $startOfMonth = new \DateTime('first day of this month');
                        if ($itemDate && $itemDate >= $startOfMonth) {
                            $thisMonth++;
                        }
                    } catch (\Exception $e) {
                        // Skip this calculation if date parsing fails
                    }
                }
            }
            
            $statistics = [
                'total' => $total,
                'approved' => $approved,
                'pending' => $total - $approved,
                'thisMonth' => $thisMonth
            ];
            
            Log::info('DG Data Response prepared', ['statistics' => $statistics]);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'statistics' => $statistics
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getDGData - Simple Version', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'approved' => 0, 'pending' => 0, 'thisMonth' => 0],
                'error' => 'Failed to load DG data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Governors list page
     */
    public function governorsList()
    {
        return view('recertification.governors_list');
    }

    /**
     * Get Governors data for the Governors list page
     */
    public function getGovernorsData(Request $request)
    {
        try {
            // Development bypass setting - can be controlled via query parameter
            $bypassPrerequisites = $request->get('bypass', 'true') === 'true'; // Default to true for testing
            
            $query = DB::connection('sqlsrv')->table('recertification_applications');
            $applications = $query->get();

            // Format data for the Governors table
            $data = $applications->map(function($app) use ($bypassPrerequisites, $request) {
                // Parse payload to get additional fields
                $payload = json_decode($app->payload ?? '{}', true);
                
                // Determine applicant name based on type
                $applicantName = '';
                if ($app->applicant_type === 'Corporate') {
                    $applicantName = $app->organisation_name ?? 'N/A';
                } else {
                    // Try to get name from payload first, then from direct columns
                    $surname = $payload['surname'] ?? $app->surname ?? '';
                    $firstName = $payload['first_name'] ?? $app->first_name ?? '';
                    $applicantName = trim($surname . ' ' . $firstName);
                    if (empty($applicantName)) {
                        $applicantName = 'N/A';
                    }
                }

                // Format plot details
                $plotDetails = '';
                $plotNumber = $payload['plot_number'] ?? $app->plot_number ?? '';
                $layoutDistrict = $payload['layout_district'] ?? $app->layout_district ?? '';
                $plotSize = $payload['plot_size'] ?? $app->plot_size ?? '';
                
                if ($plotNumber) {
                    $plotDetails .= 'Plot: ' . $plotNumber;
                }
                if ($layoutDistrict) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . $layoutDistrict;
                }
                if ($plotSize) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . 'Size: ' . $plotSize;
                }
                if (empty($plotDetails)) {
                    $plotDetails = 'N/A';
                }

                // Get LGA name from payload
                $lgaName = $payload['lga_name'] ?? $app->lga_name ?? 'N/A';

                if ($bypassPrerequisites) {
                    // Development mode - all prerequisites are automatically complete
                    $acknowledgementGenerated = true;
                    $verificationGenerated = true;
                    $gisCaptured = true;
                    $vettingGenerated = true;
                    $edmsCaptured = true;
                    $cofoFrontGenerated = true;
                    $dgApproval = true; // DG Approval is also bypassed in dev mode
                } else {
                    // Production mode - check actual database values
                    $fileNo = $app->file_number ?? null;
                    
                    // 1. Check certificate_generated (CofO Front Page)
                    $cofoFrontGenerated = ($app->certificate_generated ?? 0) == 1;
                    
                    // 2. Check acknowledgement
                    $acknowledgementGenerated = ($app->acknowledgement ?? '') === 'Generated';
                    
                    // 3. Check verification status
                    $verificationGenerated = ($app->verification ?? '') === 'Verified';
                    
                    // 4. Check EDMS captured (file_indexings + pagetypings)
                    $edmsCaptured = false;
                    try {
                        $fileIndexing = DB::connection('sqlsrv')->table('file_indexings')
                            ->where('recertification_application_id', $app->id)
                            ->first();
                            
                        if ($fileIndexing) {
                            $edmsCaptured = DB::connection('sqlsrv')->table('pagetypings')
                                ->where('file_indexing_id', $fileIndexing->id)
                                ->exists();
                        }
                    } catch (\Throwable $e) {
                        $edmsCaptured = false;
                    }
                    
                    // 5. Check GIS captured
                    $gisCaptured = ($app->gis_captured ?? 0) == 1;
                    
                    // 6. Check vetting generated
                    $vettingGenerated = ($app->vetting_generated ?? 0) == 1;
                    
                    // 7. Check DG Approval (required for Governor's List)
                    $dgApproval = ($app->dg_approval ?? 0) == 1;
                }
                
                $governorApproval = ($app->governor_approval ?? 0) == 1;

                return [
                    'id' => $app->id,
                    'file_number' => $app->file_number ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'lga_name' => $lgaName,
                    'submitted_to_governor_date' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : date('d M Y'),
                    'governor_status' => $governorApproval ? 'approved' : 'pending',
                    'governor_approval' => $governorApproval,
                    // Prerequisites status (hidden from frontend but available for processing logic)
                    'acknowledgement_generated' => $acknowledgementGenerated,
                    'verification_generated' => $verificationGenerated,
                    'gis_captured' => $gisCaptured,
                    'vetting_generated' => $vettingGenerated,
                    'edms_captured' => $edmsCaptured,
                    'cofo_front_generated' => $cofoFrontGenerated,
                    'dg_approval' => $dgApproval, // DG Approval as prerequisite
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A'
                ];
            });

            // Apply filtering only if not bypassing prerequisites
            if (!$bypassPrerequisites) {
                $data = $data->filter(function($app) {
                    // Filter to show only applications with all prerequisites completed (including DG Approval)
                    return $app['acknowledgement_generated'] && 
                           $app['verification_generated'] && 
                           $app['gis_captured'] && 
                           $app['vetting_generated'] && 
                           $app['edms_captured'] && 
                           $app['cofo_front_generated'] &&
                           $app['dg_approval']; // DG Approval is required for Governor's List
                })->values(); // Reset array keys after filtering
            }

            // Calculate statistics based on filtered data
            $total = $data->count();
            $approved = $data->where('governor_approval', true)->count();
            $pending = $total - $approved;
            $thisMonth = $data->filter(function($app) {
                if ($app['created_at'] === 'N/A') return false;
                try {
                    $createdDate = \DateTime::createFromFormat('d M Y', $app['created_at']);
                    if (!$createdDate) return false;
                    $startOfMonth = new \DateTime('first day of this month');
                    return $createdDate >= $startOfMonth;
                } catch (\Exception $e) {
                    return false;
                }
            })->count();

            $statistics = [
                'total' => $total,
                'approved' => $approved,
                'pending' => $pending,
                'thisMonth' => $thisMonth
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching Governors data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'approved' => 0, 'pending' => 0, 'thisMonth' => 0],
                'error' => 'Failed to fetch Governors data: ' . $e->getMessage()
            ]);
        }
    }
 


    /**
     * Show EDMS page
     */
    public function edms()
    {
        return view('recertification.edms');
    }

    /**
     * Get EDMS data for the EDMS page
     */
    public function getEDMSData(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv')->table('recertification_applications');
            $applications = $query->get();

            // Format data for the EDMS table
            $data = $applications->map(function($app) {
                // Determine applicant name based on type
                $applicantName = '';
                if ($app->applicant_type === 'Corporate') {
                    $applicantName = $app->organisation_name ?? 'N/A';
                } else {
                    $applicantName = trim(($app->surname ?? '') . ' ' . ($app->first_name ?? ''));
                    if (empty($applicantName)) {
                        $applicantName = 'N/A';
                    }
                }

                // Format plot details
                $plotDetails = '';
                if ($app->plot_number) {
                    $plotDetails .= 'Plot: ' . $app->plot_number;
                }
                if ($app->layout_district) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . $app->layout_district;
                }
                if ($app->plot_size) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . 'Size: ' . $app->plot_size;
                }
                if (empty($plotDetails)) {
                    $plotDetails = 'N/A';
                }

                return [
                    'id' => $app->id,
                    'file_number' => $app->file_number ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'document_count' => $app->document_count ?? 0,
                    'last_updated' => $app->updated_at ? date('d M Y', strtotime($app->updated_at)) : 'N/A',
                    'edms_status' => $app->edms_status ?? 'pending'
                ];
            });

            // Calculate statistics
            $total = $data->count();
            $digitized = $data->where('edms_status', 'digitized')->count();
            $pending = $total - $digitized;
            $thisMonth = $data->where('last_updated', '>=', now()->startOfMonth()->format('d M Y'))->count();

            $statistics = [
                'total' => $total,
                'digitized' => $digitized,
                'pending' => $pending,
                'thisMonth' => $thisMonth
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching EDMS data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'digitized' => 0, 'pending' => 0, 'thisMonth' => 0],
                'error' => 'Failed to fetch EDMS data'
            ]);
        }
    }

    /**
     * Show GIS Data Capture page
     */
    public function gisDataCapture()
    {
        return view('recertification.gis_data_capture');
    }

    /**
     * Get GIS data for the GIS Data Capture page
     */
    public function getGISData(Request $request)
    {
        try {
            // Get all recertification applications
            $applications = DB::connection('sqlsrv')->table('recertification_applications')->get();

            // Get all GIS capture records for recertification (if the table exists)
            $gisRecords = collect(); // Empty collection for now
            try {
                $gisRecords = DB::connection('sqlsrv')
                    ->table('gisCapture')
                    ->where('gis_type', 'recertification')
                    ->get()
                    ->keyBy('mlsfNo');
            } catch (\Exception $e) {
                // GIS table might not exist, continue with empty collection
            }

            // Format data for the GIS table
            $data = $applications->map(function($app) use ($gisRecords) {
                // Determine applicant name based on type
                $applicantName = '';
                if ($app->applicant_type === 'Corporate') {
                    $applicantName = $app->organisation_name ?? 'N/A';
                } else {
                    $applicantName = trim(($app->surname ?? '') . ' ' . ($app->first_name ?? ''));
                    if (empty($applicantName)) {
                        $applicantName = 'N/A';
                    }
                }

                // Format plot details
                $plotDetails = '';
                if ($app->plot_number) {
                    $plotDetails .= 'Plot: ' . $app->plot_number;
                }
                if ($app->layout_district) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . $app->layout_district;
                }
                if ($app->plot_size) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . 'Size: ' . $app->plot_size . ' Ha';
                }
                if (empty($plotDetails)) {
                    $plotDetails = 'N/A';
                }

                // Check if GIS data exists for this application
                $gisRecord = $gisRecords->get($app->file_number);
                $gisStatus = $gisRecord ? 'captured' : 'pending';
                $lastCaptured = $gisRecord ? date('d M Y', strtotime($gisRecord->created_at)) : 'N/A';

                return [
                    'id' => $app->id,
                    'file_number' => $app->file_number ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'lga_name' => $app->lga_name ?? 'N/A',
                    'application_date' => $app->application_date ? date('d M Y', strtotime($app->application_date)) : ($app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A'),
                    'gis_status' => $gisStatus,
                    'last_captured' => $lastCaptured,
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A'
                ];
            });

            // Calculate statistics
            $total = $data->count();
            $captured = $data->where('gis_status', 'captured')->count();
            $pending = $total - $captured;
            
            // Count applications captured this month
            $thisMonth = $data->filter(function($app) {
                if ($app['last_captured'] === 'N/A') return false;
                try {
                    $capturedDate = \DateTime::createFromFormat('d M Y', $app['last_captured']);
                    if (!$capturedDate) return false;
                    $startOfMonth = new \DateTime('first day of this month');
                    return $capturedDate >= $startOfMonth;
                } catch (\Exception $e) {
                    return false;
                }
            })->count();

            $statistics = [
                'total' => $total,
                'captured' => $captured,
                'pending' => $pending,
                'thisMonth' => $thisMonth
            ];

            return response()->json([
                'success' => true,
                'data' => $data->values(),
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching GIS data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'captured' => 0, 'pending' => 0, 'thisMonth' => 0],
                'error' => 'Failed to fetch GIS data'
            ]);
        }
    }

    /**
     * Browse vetting sheet directory
     */
    public function browseVettingSheetDirectory(Request $request)
    {
        try {
            // Define the vetting sheet directory path
            $directoryPath = 'C:\\Users\\admin\\Documents';
            
            // Get the requested path from query parameter, default to base directory
            $requestedPath = $request->get('path', $directoryPath);
            
            // Security check: ensure the requested path is within the allowed directory
            $realBasePath = realpath($directoryPath);
            $realRequestedPath = realpath($requestedPath);
            
            if (!$realRequestedPath || !str_starts_with($realRequestedPath, $realBasePath)) {
                $requestedPath = $directoryPath;
                $realRequestedPath = $realBasePath;
            }

            // Check if directory exists
            if (!is_dir($realRequestedPath)) {
                throw new \Exception('Directory not found');
            }

            // Get directory contents
            $items = [];
            $files = scandir($realRequestedPath);
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $fullPath = $realRequestedPath . DIRECTORY_SEPARATOR . $file;
                $isDirectory = is_dir($fullPath);
                $size = $isDirectory ? null : filesize($fullPath);
                $modified = filemtime($fullPath);
                
                $items[] = [
                    'name' => $file,
                    'type' => $isDirectory ? 'directory' : 'file',
                    'size' => $size,
                    'modified' => date('Y-m-d H:i:s', $modified),
                    'path' => $fullPath
                ];
            }

            // Sort items: directories first, then files, both alphabetically
            usort($items, function($a, $b) {
                if ($a['type'] !== $b['type']) {
                    return $a['type'] === 'directory' ? -1 : 1;
                }
                return strcasecmp($a['name'], $b['name']);
            });

            // Prepare breadcrumb navigation
            $breadcrumbs = [];
            $currentPath = $realRequestedPath;
            while ($currentPath && str_starts_with($currentPath, $realBasePath)) {
                $breadcrumbs[] = [
                    'name' => basename($currentPath),
                    'path' => $currentPath
                ];
                $parentPath = dirname($currentPath);
                if ($parentPath === $currentPath) break;
                $currentPath = $parentPath;
            }
            $breadcrumbs = array_reverse($breadcrumbs);

            return view('recertification.vetting_sheet_browser', [
                'items' => $items,
                'currentPath' => $realRequestedPath,
                'breadcrumbs' => $breadcrumbs,
                'parentPath' => dirname($realRequestedPath) !== $realRequestedPath ? dirname($realRequestedPath) : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error browsing vetting sheet directory', [
                'message' => $e->getMessage(),
                'path' => $requestedPath ?? 'unknown'
            ]);

            return view('recertification.vetting_sheet_browser', [
                'items' => [],
                'currentPath' => $directoryPath,
                'breadcrumbs' => [['name' => 'Documents', 'path' => $directoryPath]],
                'parentPath' => null,
                'error' => 'Unable to access directory: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process batch of applications for DG approval
     */
    public function batchProcess(Request $request)
    {
        try {
            $request->validate([
                'application_ids' => 'required|array',
                'application_ids.*' => 'integer|exists:sqlsrv.recertification_applications,id'
            ]);

            $applicationIds = $request->input('application_ids');
            $processedCount = 0;

            foreach ($applicationIds as $id) {
                // Update the application to mark as DG approved
                $updated = DB::connection('sqlsrv')
                    ->table('recertification_applications')
                    ->where('id', $id)
                    ->update([
                        'dg_approval' => true,
                        'dg_approval_date' => now(),
                        'updated_at' => now()
                    ]);

                if ($updated) {
                    $processedCount++;
                }
            }

            Log::info('Batch processing completed', [
                'requested_count' => count($applicationIds),
                'processed_count' => $processedCount,
                'application_ids' => $applicationIds
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully processed {$processedCount} application(s) for DG approval",
                'processed_count' => $processedCount,
                'total_requested' => count($applicationIds)
            ]);

        } catch (\Exception $e) {
            Log::error('Error in batch processing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process batch of applications for Governor approval
     */
    public function batchProcessGovernor(Request $request)
    {
        try {
            $request->validate([
                'application_ids' => 'required|array',
                'application_ids.*' => 'integer|exists:sqlsrv.recertification_applications,id'
            ]);

            $applicationIds = $request->input('application_ids');
            $processedCount = 0;

            foreach ($applicationIds as $id) {
                // Update the application to mark as Governor approved
                $updated = DB::connection('sqlsrv')
                    ->table('recertification_applications')
                    ->where('id', $id)
                    ->update([
                        'governor_approval' => true,
                        'governor_approval_date' => now(),
                        'updated_at' => now()
                    ]);

                if ($updated) {
                    $processedCount++;
                }
            }

            Log::info('Governor batch processing completed', [
                'requested_count' => count($applicationIds),
                'processed_count' => $processedCount,
                'application_ids' => $applicationIds
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully processed {$processedCount} application(s) for Governor approval",
                'processed_count' => $processedCount,
                'total_requested' => count($applicationIds)
            ]);

        } catch (\Exception $e) {
            Log::error('Error in Governor batch processing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process Governor batch: ' . $e->getMessage()
            ], 500);
        }
    }
}