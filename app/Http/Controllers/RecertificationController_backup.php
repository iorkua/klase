<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RecertificationController extends Controller
{  
    public function index() {
        $PageTitle = 'Recertification Programme';
        $PageDescription = 'Manage approved certificate recertification and re-issuance applications';
        return view('recertification.index', compact('PageTitle', 'PageDescription'));
    }

    /**
     * Get applications data for DataTables
     */
    public function getApplicationsData(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv')->table('recertification_applications');

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('application_reference', 'like', "%{$searchValue}%")
                      ->orWhere('surname', 'like', "%{$searchValue}%")
                      ->orWhere('first_name', 'like', "%{$searchValue}%")
                      ->orWhere('organisation_name', 'like', "%{$searchValue}%")
                      ->orWhere('plot_number', 'like', "%{$searchValue}%")
                      ->orWhere('cofo_number', 'like', "%{$searchValue}%")
                      ->orWhere('file_number', 'like', "%{$searchValue}%");
                });
            }

            // Get total count before pagination
            $totalRecords = $query->count();

            // Apply ordering
            if ($request->has('order')) {
                $orderColumn = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];
                
                $columns = ['id', 'file_number', 'applicant_type', 'applicant_name', 'plot_details', 'lga_name', 'created_at'];
                if (isset($columns[$orderColumn])) {
                    if ($orderColumn == 3) { // applicant_name
                        $query->orderBy('surname', $orderDir)->orderBy('first_name', $orderDir);
                    } else {
                        $query->orderBy($columns[$orderColumn], $orderDir);
                    }
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Apply pagination
            if ($request->has('start') && $request->has('length')) {
                $query->skip($request->start)->take($request->length);
            }

            $applications = $query->get();

            // Format data for DataTables
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
                if (empty($plotDetails)) {
                    $plotDetails = 'N/A';
                }

                // Check if CofO already captured (exists in legacy CofO table) using available file number
                $fileNo = $app->file_number ?? null;
                $cofoExists = false;
                if ($fileNo) {
                    try {
                        if (Schema::connection('sqlsrv')->hasTable('Cofo')) {
                            $cofoExists = DB::connection('sqlsrv')->table('Cofo')
                                ->where('fileNo', $fileNo)
                                ->orWhere('mlsFNo', $fileNo)
                                ->orWhere('kangisFileNo', $fileNo)
                                ->orWhere('NewKANGISFileno', $fileNo)
                                ->exists();
                        }
                    } catch (\Throwable $e) {
                        $cofoExists = false;
                    }
                }

                return [
                    'id' => $app->id,
                    'application_reference' => $app->application_reference ?? 'N/A',
                    'file_number' => $fileNo ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'lga_name' => $app->lga_name ?? 'N/A',
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A',
                    'cofo_number' => $app->cofo_number ?? 'N/A',
                    'acknowledgement' => $app->acknowledgement ?? null,
                    'cofo_exists' => $cofoExists,
                    // Add the missing fields
                    'cofO_serialNo' => $app->cofO_serialNo ?? $app->CofOSerialNo ?? 'N/A',
                    'NewKANGISFileno' => $app->NewKANGISFileno ?? $app->newkangisfileno ?? 'N/A',
                    'kangisFileNo' => $app->kangisFileNo ?? $app->kangis_file_no ?? 'N/A',
                    'mlsfNo' => $app->mlsfNo ?? $app->mlsf_no ?? 'N/A',
                    'reg_no' => $app->reg_no ?? $app->registration_no ?? 'N/A',
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching applications data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to fetch applications data'
            ]);
        }
    }

    /**
     * Get verification sheet data for DataTables
     */
    public function getVerificationData(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv')->table('recertification_applications');

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('file_number', 'like', "%{$searchValue}%")
                      ->orWhere('surname', 'like', "%{$searchValue}%")
                      ->orWhere('first_name', 'like', "%{$searchValue}%")
                      ->orWhere('organisation_name', 'like', "%{$searchValue}%")
                      ->orWhere('plot_number', 'like', "%{$searchValue}%")
                      ->orWhere('lga_name', 'like', "%{$searchValue}%")
                      ->orWhere('application_type', 'like', "%{$searchValue}%");
                });
            }

            // Get total count before pagination
            $totalRecords = $query->count();

            // Apply ordering
            if ($request->has('order')) {
                $orderColumn = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];
                
                $columns = ['file_number', 'application_type', 'applicant_name', 'plot_details', 'lga_name', 'application_date'];
                if (isset($columns[$orderColumn])) {
                    if ($orderColumn == 2) { // applicant_name
                        $query->orderBy('surname', $orderDir)->orderBy('first_name', $orderDir);
                    } else {
                        $query->orderBy($columns[$orderColumn], $orderDir);
                    }
                }
            } else {
                $query->orderBy('application_date', 'desc');
            }

            // Apply pagination
            if ($request->has('start') && $request->has('length')) {
                $query->skip($request->start)->take($request->length);
            }

            $applications = $query->get();

            // Format data for DataTables
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
                    'application_reference' => $app->application_reference ?? 'N/A',
                    'file_number' => $app->file_number ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'lga_name' => $app->lga_name ?? 'N/A',
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A',
                    'verification' => $app->verification ?? null,
                    // Add the missing fields
                    'cofO_serialNo' => $app->cofO_serialNo ?? $app->CofOSerialNo ?? 'N/A',
                    'NewKANGISFileno' => $app->NewKANGISFileno ?? $app->newkangisfileno ?? 'N/A',
                    'kangisFileNo' => $app->kangisFileNo ?? $app->kangis_file_no ?? 'N/A',
                    'mlsfNo' => $app->mlsfNo ?? $app->mlsf_no ?? 'N/A',
                    'reg_no' => $app->reg_no ?? $app->registration_no ?? 'N/A',
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching verification data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to fetch verification data'
            ]);
        }
    }
}