<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            $query = DB::connection('sqlsrv')->table('recertification_applications');
            $applications = $query->get();

            // Format data for the DG table
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
                    'submitted_to_dg_date' => $app->submitted_to_dg_date ? date('d M Y', strtotime($app->submitted_to_dg_date)) : 'N/A',
                    'dg_status' => $app->dg_status ?? 'pending'
                ];
            });

            // Calculate statistics
            $total = $data->count();
            $approved = $data->where('dg_status', 'approved')->count();
            $pending = $total - $approved;
            $thisMonth = $data->where('submitted_to_dg_date', '>=', now()->startOfMonth()->format('d M Y'))->count();

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
            Log::error('Error fetching DG data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'statistics' => ['total' => 0, 'approved' => 0, 'pending' => 0, 'thisMonth' => 0],
                'error' => 'Failed to fetch DG data'
            ]);
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
            $query = DB::connection('sqlsrv')->table('recertification_applications');
            $applications = $query->get();

            // Format data for the Governors table
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
                    'submitted_to_governor_date' => $app->submitted_to_governor_date ? date('d M Y', strtotime($app->submitted_to_governor_date)) : 'N/A',
                    'governor_status' => $app->governor_status ?? 'pending'
                ];
            });

            // Calculate statistics
            $total = $data->count();
            $approved = $data->where('governor_status', 'approved')->count();
            $pending = $total - $approved;
            $thisMonth = $data->where('submitted_to_governor_date', '>=', now()->startOfMonth()->format('d M Y'))->count();

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
                'error' => 'Failed to fetch Governors data'
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
}