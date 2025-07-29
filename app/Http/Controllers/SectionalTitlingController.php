<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class  SectionalTitlingController extends Controller
{

 

    

    public function index()
    {
        $PageTitle = 'Sectional Titling Module (STM)';
        $PageDescription = 'Process CofO for individually owned sections of multi-unit developments.';
        $Primary = DB::connection('sqlsrv')->table('dbo.mother_applications')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        
        $Secondary = DB::connection('sqlsrv')->table('dbo.subapplications')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();


        return view('sectionaltitling.index', compact(
            'Primary', 
            'Secondary',
            'PageTitle',
            'PageDescription'
        ));
    }

  public function Primary(Request $request)

    {
        if ($request->has('survey')) {
            $PageTitle = 'Sectional Titling Survey';
            $PageDescription = 'Process Survey Applications';
        } else {
            $PageTitle = $request->get('url') === 'phy_planning' ? 'Planning Recommendation Approval' : 
                    ($request->get('url') === 'recommendation' ? 'Planning Recommendation' : 'Primary Applications');
            $PageDescription = $request->get('url') === 'phy_planning' ? '' : 
                    ($request->get('url') === 'recommendation' ? 'Review and process planning recommendation for sectional titles' : 'Process CofO for individually owned sections of multi-unit developments.');
        }

        $PrimaryApplications = DB::connection('sqlsrv')->table('dbo.mother_applications')->get();
         

        return view('sectionaltitling.primary', compact('PrimaryApplications', 'PageTitle', 'PageDescription'));
    }

  public function Secondary(Request $request)
    {
        if ($request->has('survey')) {
            $PageTitle = 'Sectional Titling Survey';
            $PageDescription = 'Process Survey Applications';
        } else {
            $PageTitle = $request->get('url') === 'phy_planning' ? 'Planning Recommendation Approval' : 
                         ($request->get('url') === 'recommendation' ? 'Planning Recommendation' : 'Secondary  Applications');
            $PageDescription = $request->get('url') === 'phy_planning' ? '' : 
                               ($request->get('url') === 'recommendation' ? 'Review and process planning recommendation for sectional titles' : 'Process CofO for individually owned sections of multi-unit developments.');
        }

        $SecondaryApplications = DB::connection('sqlsrv')->table('dbo.subapplications')
            ->leftJoin('dbo.mother_applications', 'dbo.subapplications.main_application_id', '=', 'dbo.mother_applications.id')
            ->select(
            'dbo.subapplications.fileno', 
            'dbo.subapplications.applicant_type',
            'dbo.subapplications.scheme_no',
            'dbo.subapplications.id',
            'dbo.subapplications.main_application_id',
            'dbo.subapplications.applicant_title',
            'dbo.subapplications.first_name',
            'dbo.subapplications.surname',
            'dbo.subapplications.corporate_name',
            'dbo.subapplications.multiple_owners_names',
            'dbo.subapplications.phone_number',
            'dbo.subapplications.planning_recommendation_status',
            'dbo.subapplications.application_status',
            'dbo.subapplications.created_at',
            'dbo.subapplications.unit_number',
            'dbo.subapplications.main_id',

            'dbo.subapplications.passport',
            'dbo.subapplications.multiple_owners_passport',
            'dbo.mother_applications.fileno as mother_fileno',
           'dbo.mother_applications.passport as mother_passport',
            'dbo.mother_applications.multiple_owners_passport as mother_multiple_owners_passport',
            'dbo.mother_applications.applicant_title as mother_applicant_title',
            'dbo.mother_applications.first_name as mother_first_name',
            'dbo.mother_applications.surname as mother_surname',
            'dbo.mother_applications.corporate_name as mother_corporate_name',
            'dbo.mother_applications.multiple_owners_names as mother_multiple_owners_names',
            'dbo.mother_applications.land_use',
            'dbo.mother_applications.property_house_no',
            'dbo.mother_applications.property_plot_no',
            'dbo.mother_applications.property_street_name',
            'dbo.mother_applications.property_district',
            'dbo.mother_applications.property_lga',
             'dbo.mother_applications.np_fileno'
            )
            ->get();
         

        return view('sectionaltitling.secondary', compact('SecondaryApplications', 'PageTitle', 'PageDescription')); 
    }

  public function units(Request $request)
    {
        $PageTitle = 'Unit Applications';
        $PageDescription = 'Process CofO for individually owned sections of multi-unit developments.';

        $query = DB::connection('sqlsrv')->table('dbo.subapplications')
            ->leftJoin('dbo.mother_applications', 'dbo.subapplications.main_application_id', '=', 'dbo.mother_applications.id')
            ->select(
            'dbo.subapplications.fileno', 
            'dbo.subapplications.scheme_no',
            'dbo.subapplications.id',
            'dbo.subapplications.main_application_id',
            'dbo.subapplications.applicant_title',
            'dbo.subapplications.first_name',
            'dbo.subapplications.surname',
            'dbo.subapplications.corporate_name',
            'dbo.subapplications.multiple_owners_names',
            'dbo.subapplications.phone_number',
            'dbo.subapplications.planning_recommendation_status',
            'dbo.subapplications.application_status',
            'dbo.subapplications.created_at',
            'dbo.subapplications.unit_number',
            'dbo.subapplications.main_id',
            'dbo.subapplications.passport',
            'dbo.subapplications.multiple_owners_passport',
            'dbo.mother_applications.fileno as mother_fileno',
              'mother_applications.id as mother_id',
           'dbo.mother_applications.passport as mother_passport',
            'dbo.mother_applications.multiple_owners_passport as mother_multiple_owners_passport',
            'dbo.mother_applications.applicant_title as mother_applicant_title',
            'dbo.mother_applications.first_name as mother_first_name',
            'dbo.mother_applications.surname as mother_surname',
            'dbo.mother_applications.corporate_name as mother_corporate_name',
            'dbo.mother_applications.multiple_owners_names as mother_multiple_owners_names',
            'dbo.mother_applications.land_use',
            'dbo.mother_applications.property_house_no',
            'dbo.mother_applications.property_plot_no',
            'dbo.mother_applications.property_street_name',
            'dbo.mother_applications.property_district',
            'dbo.mother_applications.property_lga' ,  
            'dbo.mother_applications.np_fileno'
             
            );

        // Check if main_application_id parameter exists in URL
        if ($request->has('main_application_id')) {
            $mainApplicationId = $request->get('main_application_id');
            $query->where('dbo.subapplications.main_application_id', $mainApplicationId);
        }

        $SecondaryApplications = $query->get();
         

        return view('sectionaltitling.units', compact('SecondaryApplications', 'PageTitle', 'PageDescription')); 
    }



    public function Map()
    {
        $PageTitle = 'GIS Mapping - Sectional Titling';
        $PageDescription = 'Geospatial visualization of sectional title properties in Kano State.';
  
        return view('map.index', compact('PageTitle', 'PageDescription'));
    }

    public function mother(Request $request)
    {
        if ($request->has('survey')) {
            $PageTitle = 'Sectional Titling Survey - Mother Applications';
            $PageDescription = 'Process Survey Applications for Mother Applications';
        } else {
            $PageTitle = $request->get('url') === 'phy_planning' ? 'Planning Recommendation Approval - Mother Applications' : 
                    ($request->get('url') === 'recommendation' ? 'Planning Recommendation - Mother Applications' : 'Mother Applications');
            $PageDescription = $request->get('url') === 'phy_planning' ? 'Physical planning approval for mother applications' : 
                    ($request->get('url') === 'recommendation' ? 'Review and process planning recommendation for mother applications' : 'Manage and process mother applications for sectional titling.');
        }

        $PrimaryApplications = DB::connection('sqlsrv')->table('dbo.mother_applications')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sectionaltitling.mother', compact('PrimaryApplications', 'PageTitle', 'PageDescription'));
    }

    public function getBuyerList($applicationId)
    {
        try {
            // Query to get buyer list with unit measurements
            $buyers = DB::connection('sqlsrv')
                ->table('dbo.buyer_list as bl')
                ->leftJoin('dbo.st_unit_measurements as sum', function($join) {
                    $join->on('bl.application_id', '=', 'sum.application_id')
                         ->on('bl.unit_no', '=', 'sum.unit_no');
                })
                ->select(
                    'bl.application_id',
                    'bl.unit_measurement_id',
                    'bl.buyer_title',
                    'bl.buyer_name',
                    'bl.unit_no',
                    'sum.buyer_id',
                    'sum.measurement'
                )
                ->where('bl.application_id', $applicationId)
                ->get();

            return response()->json([
                'success' => true,
                'buyers' => $buyers,
                'message' => 'Buyer list retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving buyer list: ' . $e->getMessage(),
                'buyers' => []
            ]);
        }
    }

    public function saveCofoDetails(Request $request)
    {
        try {
            // Log the incoming request for debugging
            \Log::info('CofO Details Save Request', [
                'method' => $request->method(),
                'url' => $request->url(),
                'data' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Validate the request
            $request->validate([
                'application_id' => 'required|integer',
                'transaction_type' => 'required|string',
                'certificate_date' => 'required|date',
                'serial_no' => 'required|integer',
                'page_no' => 'required|integer',
                'volume_no' => 'required|integer',
                'transaction_date' => 'required|date',
                'period' => 'nullable|integer',
                'period_unit' => 'nullable|string',
                'grantor' => 'required|string',
                'grantee' => 'required|string',
                'property_description' => 'nullable|string'
            ]);

            // Get the mother application data
            $motherApplication = DB::connection('sqlsrv')
                ->table('dbo.mother_applications')
                ->where('id', $request->application_id)
                ->first();

            if (!$motherApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ]);
            }

            // Check for duplicates based on application ID
            $existingRecord = DB::connection('sqlsrv')
                ->table('dbo.Cofo_legacy')
                ->where('mlsFNo', $motherApplication->fileno)
                ->first();

            if ($existingRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'CofO details already exist for this application'
                ]);
            }

            // Prepare data for insertion
            $cofoData = [
                'mlsFNo' => $motherApplication->fileno,
                'kangisFileNo' => $motherApplication->fileno, // Using fileno as kangisFileNo
                'NewKANGISFileno' => $motherApplication->np_fileno ?? $motherApplication->fileno,
                'title_type' => 'Certificate of Occupancy', // Default title type
                'transaction_type' => $request->transaction_type,
                'transaction_date' => $request->transaction_date,
                'serialNo' => $request->serial_no,
                'pageNo' => $request->page_no,
                'volumeNo' => $request->volume_no,
                'regNo' => $request->reg_no, // This comes from the preview field
                'instrument_type' => 'Certificate of Occupancy', // Fixed as Certificate of Occupancy
                'period' => $request->period,
                'period_unit' => $request->period_unit,
                'Assignor' => null, // Will be set based on transaction type
                'Assignee' => null,
                'Mortgagor' => null,
                'Mortgagee' => null,
                'Surrenderor' => null,
                'Surrenderee' => null,
                'Lessor' => null,
                'Lessee' => null,
                'Grantor' => $request->grantor,
                'Grantee' => $request->grantee,
                'property_description' => $request->property_description ?: (isset($motherApplication->property_description) ? $motherApplication->property_description : ''),
                'location' => '', // Removed location field
                'plot_no' => $motherApplication->property_house_no ?: $motherApplication->property_plot_no,
                'lgsaOrCity' => $motherApplication->property_lga,
                'layout' => '', // Removed layout field
                'schedule' => '', // Removed schedule field
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Set transaction-specific parties based on transaction type
            switch (strtolower($request->transaction_type)) {
                case 'assignment':
                    $cofoData['Assignor'] = $request->grantor;
                    $cofoData['Assignee'] = $request->grantee;
                    break;
                case 'mortgage':
                    $cofoData['Mortgagor'] = $request->grantee; // The one giving the mortgage
                    $cofoData['Mortgagee'] = $request->grantor; // The one receiving the mortgage
                    break;
                case 'lease':
                    $cofoData['Lessor'] = $request->grantor;
                    $cofoData['Lessee'] = $request->grantee;
                    break;
                case 'surrender':
                    $cofoData['Surrenderor'] = $request->grantee;
                    $cofoData['Surrenderee'] = $request->grantor;
                    break;
                case 'certificate of occupancy':
                    // For Certificate of Occupancy, use Grantor and Grantee
                    $cofoData['Grantor'] = $request->grantor;
                    $cofoData['Grantee'] = $request->grantee;
                    break;
            }

            // Insert into Cofo_legacy table
            DB::connection('sqlsrv')->table('dbo.Cofo_legacy')->insert($cofoData);

            return response()->json([
                'success' => true,
                'message' => 'CofO details saved successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving CofO details: ' . $e->getMessage()
            ], 500);
        }
    }
  
}