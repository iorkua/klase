<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class STMemoController extends Controller
{
    private function getApplication($id)
    {
        // Modified to join subapplications with mother_applications to get primary application details
        $application = DB::connection('sqlsrv')->table('subapplications')
            ->select(
                'subapplications.*', 
                'subapplications.id as applicationID', // Add alias for applicationID
                'subapplications.main_application_id as main_application_id', // Add main_application_id if it exists
                'mother_applications.fileno as primary_fileno',
                'mother_applications.first_name as primary_first_name',
                'mother_applications.surname as primary_surname',
                'mother_applications.applicant_title as primary_applicant_title',
                'mother_applications.application_status as primary_application_status',
                'mother_applications.land_use as primary_land_use',
                'mother_applications.id as main_application_id',

                // Property fields with proper aliases
                'mother_applications.property_house_no as property_house_no',
                'mother_applications.property_plot_no as property_plot_no',
                'mother_applications.property_street_name as property_street_name',
                'mother_applications.property_lga as property_lga',
                 
                'mother_applications.applicationID as primary_applicationID' // Get primary app's applicationID
            )
            ->leftJoin('mother_applications', 'subapplications.main_application_id', '=', 'mother_applications.id')
            ->where('subapplications.id', $id)
            ->first();

        if (!$application) {
            return response()->json(['error' => 'Sub application not found'], 404);
        }

        return $application;
    }

    private function getSecondaryApplication($id)
    {
        $application = DB::connection('sqlsrv')->table('dbo.subapplications')
            ->leftJoin('dbo.mother_applications', 'dbo.subapplications.main_application_id', '=', 'dbo.mother_applications.id')
            ->select(
                'dbo.subapplications.*',
                'dbo.subapplications.id as id',
                'dbo.mother_applications.fileno as primary_fileno', // Changed alias to primary_fileno
                'dbo.mother_applications.passport as mother_passport',
                'dbo.mother_applications.multiple_owners_passport as mother_multiple_owners_passport',
                'dbo.mother_applications.applicant_title as primary_applicant_title', // Changed alias to primary_applicant_title
                'dbo.mother_applications.first_name as primary_first_name', // Changed alias to primary_first_name
                'dbo.mother_applications.surname as primary_surname', // Changed alias to primary_surname
                'dbo.mother_applications.corporate_name as mother_corporate_name',
                'dbo.mother_applications.multiple_owners_names as mother_multiple_owners_names',
                'dbo.mother_applications.land_use',
                'dbo.mother_applications.property_house_no',
                'dbo.mother_applications.property_plot_no',
                'dbo.mother_applications.property_street_name',
                'dbo.mother_applications.property_district',
                'dbo.mother_applications.property_lga',
                'dbo.mother_applications.id as main_application_id' // Add main_application_id for reference
            )
            ->where('dbo.subapplications.id', $id)
            ->first();

        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        return $application;
    }

    private function getPrimaryApplication($id)
    {
        $application = DB::connection('sqlsrv')->table('mother_applications')
            ->select(
                'mother_applications.*',
                'mother_applications.id as main_application_id',
                'mother_applications.id as applicationID'
            )
            ->where('mother_applications.id', $id)
            ->first();

        if (!$application) {
            return response()->json(['error' => 'Primary application not found'], 404);
        }

        return $application;
    }
  
    public function STmemo(Request $request)
    {
        $PageTitle = 'SECTIONAL TITLING MEMO';
        $PageDescription = 'processing of sectional titling memo';

        if ($request->has('id')) {
            $application = $this->getSecondaryApplication($request->get('id'));
            if ($application instanceof \Illuminate\Http\JsonResponse) {
                return $application;
            }
            
            return view('stmemo.view_application', compact('application', 'PageTitle', 'PageDescription'));
        }

        // Get count of applications with generated memos
        $generatedCount = DB::connection('sqlsrv')
            ->table('mother_applications')
            ->join('memos', 'mother_applications.id', '=', 'memos.application_id')
            ->where('memos.memo_status', 'GENERATED')
            ->count();

        // Get count of applications without generated memos
        $notGeneratedCount = DB::connection('sqlsrv')
            ->table('mother_applications')
            ->leftJoin('memos', function($join) {
                $join->on('mother_applications.id', '=', 'memos.application_id')
                     ->where('memos.memo_status', 'GENERATED');
            })
            ->whereNull('memos.id')
            ->count();

        // Filter applications based on selected status
        $status = $request->input('status', 'not_generated');
        
        if ($status == 'generated') {
            $PrimaryApplications = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->select('mother_applications.*')
                ->join('memos', 'mother_applications.id', '=', 'memos.application_id')
                ->where('memos.memo_status', 'GENERATED')
                ->orderBy('mother_applications.created_at', 'desc')
                ->get();
        } else {
            $PrimaryApplications = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->select('mother_applications.*')
                ->leftJoin('memos', function($join) {
                    $join->on('mother_applications.id', '=', 'memos.application_id')
                         ->where('memos.memo_status', 'GENERATED');
                })
                ->whereNull('memos.id')
                ->orderBy('mother_applications.created_at', 'desc')
                ->get();
        }

        return view('stmemo.stmemo', compact('PrimaryApplications', 'PageTitle', 'PageDescription', 'generatedCount', 'notGeneratedCount'));
    }

    

    public function SitePlan(Request $request)
    {
        $PageTitle = 'ST Applications';
        $PageDescription = '';

        // Fetch primary applications only from mother_applications
        $PrimaryApplications = DB::connection('sqlsrv')->table('mother_applications')
            ->select(
                'mother_applications.*',
                'mother_applications.id as id'
            )
            ->orderBy('mother_applications.created_at', 'desc')
            ->get();

        // Check which applications have site plans
        $sitePlans = DB::connection('sqlsrv')->table('site_plans')
            ->pluck('application_id')
            ->toArray();

        // Add site_plan_status to each application
        $PrimaryApplications = $PrimaryApplications->map(function($app) use ($sitePlans) {
            $app->site_plan_status = in_array($app->id, $sitePlans) ? 'Uploaded' : 'Not Uploaded';
            return $app;
        });

        return view('stmemo.siteplan', compact('PrimaryApplications', 'PageTitle', 'PageDescription'));
    }
    
    public function uploadSitePlan($id)
    {
        $PageTitle = 'UPLOAD SITE PLAN';
        $PageDescription = 'Upload a site plan for a sectional titling application';
        
        // Check if primary or secondary application
        $isPrimary = DB::connection('sqlsrv')->table('mother_applications')->where('id', $id)->exists();
        
        if ($isPrimary) {
            $application = $this->getPrimaryApplication($id);
        } else {
            $application = $this->getSecondaryApplication($id);
        }
        
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return redirect()->route('stmemo.siteplan')->with('error', 'Application not found');
        }
        
        // Check if site plan already exists
        $existingSitePlan = DB::connection('sqlsrv')->table('site_plans')
            ->where('application_id', $id)
            ->first();
        
        return view('stmemo.upload_siteplan', compact('application', 'existingSitePlan', 'PageTitle', 'PageDescription', 'isPrimary'));
    }
    
    public function saveSitePlan(Request $request)
    {
        $request->validate([
            'application_id' => 'required',
            'property_location' => 'required',
            'site_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        
        $applicationId = $request->application_id;
        
        // Get application details for naming the file
        $isPrimary = DB::connection('sqlsrv')->table('mother_applications')->where('id', $applicationId)->exists();
        
        if ($isPrimary) {
            $application = $this->getPrimaryApplication($applicationId);
        } else {
            $application = $this->getSecondaryApplication($applicationId);
        }
        
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Application not found']);
            }
            return redirect()->route('stmemo.siteplan')->with('error', 'Application not found');
        }
        
        // Generate applicant name for the file
        $applicantName = '';
        if ($isPrimary) {
            if (!empty($application->corporate_name)) {
                $applicantName = $application->corporate_name;
            } else {
                $applicantName = $application->applicant_title . ' ' . $application->first_name . ' ' . $application->surname;
            }
        } else {
            if (!empty($application->corporate_name)) {
                $applicantName = $application->corporate_name;
            } else {
                $applicantName = $application->applicant_title . ' ' . $application->first_name . ' ' . $application->surname;
            }
        }
        
        $applicantName = preg_replace('/[^a-zA-Z0-9]/', '_', $applicantName);
        
        // Create directory if it doesn't exist
        $uploadDir = 'site_plans/' . $applicationId;
        if (!Storage::disk('public')->exists($uploadDir)) {
            Storage::disk('public')->makeDirectory($uploadDir);
        }
        
        // Upload the file
        $file = $request->file('site_file');
        $extension = $file->getClientOriginalExtension();
        $fileName = $applicantName . '_site_plan_' . time() . '.' . $extension;
        $filePath = $file->storeAs($uploadDir, $fileName, 'public');
        
        // Check if record already exists
        $existingSitePlan = DB::connection('sqlsrv')->table('site_plans')
            ->where('application_id', $applicationId)
            ->first();
            
        $isUpdate = false;
        if ($existingSitePlan) {
            // Delete old file if exists
            if (Storage::disk('public')->exists($existingSitePlan->site_file)) {
                Storage::disk('public')->delete($existingSitePlan->site_file);
            }
            
            // Update record
            DB::connection('sqlsrv')->table('site_plans')
                ->where('application_id', $applicationId)
                ->update([
                    'property_location' => $request->property_location,
                    'site_file' => $filePath,
                     'uploaded_by' => Auth::id(),
                    'updated_at' => now()
                ]);
                
            $message = 'Site plan has been successfully updated';
            $isUpdate = true;
        } else {
            // Create new record
            DB::connection('sqlsrv')->table('site_plans')->insert([
                'application_id' => $applicationId,
                'property_location' => $request->property_location,
                'status' => 'Uploaded',
                'created_by' => Auth::id(),
                'site_file' => $filePath,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $message = 'Site plan has been successfully uploaded';
        }
        
        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'update' => $isUpdate,
                'file_path' => Storage::url($filePath)
            ]);
        }
        
        // If not AJAX, redirect as before
        return redirect()->route('stmemo.viewSitePlan', $applicationId)->with('success', $message);
    }
    
    public function viewSitePlan($id)
    {
        $PageTitle = 'VIEW SITE PLAN';
        $PageDescription = 'View site plan for a sectional titling application';
        
        // Check if primary or secondary application
        $isPrimary = DB::connection('sqlsrv')->table('mother_applications')->where('id', $id)->exists();
        
        if ($isPrimary) {
            $application = $this->getPrimaryApplication($id);
        } else {
            $application = $this->getSecondaryApplication($id);
        }
        
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return redirect()->route('stmemo.siteplan')->with('error', 'Application not found');
        }
        
        // Get site plan details
        $sitePlan = DB::connection('sqlsrv')->table('site_plans')
            ->where('application_id', $id)
            ->first();
            
        if (!$sitePlan) {
            return redirect()->route('stmemo.uploadSitePlan', $id)->with('error', 'No site plan found for this application');
        }
        
        return view('stmemo.view_siteplan', compact('application', 'sitePlan', 'PageTitle', 'PageDescription', 'isPrimary'));
    }
    
    public function deleteSitePlan($id)
    {
        // Get site plan details
        $sitePlan = DB::connection('sqlsrv')->table('site_plans')
            ->where('application_id', $id)
            ->first();
            
        if (!$sitePlan) {
            return redirect()->route('stmemo.siteplan')->with('error', 'No site plan found for this application');
        }
        
        // Delete file
        if (Storage::disk('public')->exists($sitePlan->site_file)) {
            Storage::disk('public')->delete($sitePlan->site_file);
        }
        
        // Delete record
        DB::connection('sqlsrv')->table('site_plans')
            ->where('application_id', $id)
            ->delete();
            
        return redirect()->route('stmemo.siteplan')->with('success', 'Site plan has been successfully deleted');
    }

    
    public function MemoTemplate()
    {
        $PageTitle = 'ST MEMO TEMPLATE';
        $PageDescription = '';
         
        return view('stmemo.temotemplate', compact('PageTitle', 'PageDescription'));
    }   
    
    public function SecondarySurveyView($d)
    {
        $PageTitle = 'SECTIONAL TITLING SURVEY';
        $PageDescription = 'processing of sectional title survey applications for secondary applications';
        
        $application = $this->getSecondaryApplication($d);
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return $application;
        }

        return view('other_departments.secondary_survey_view', compact('application', 'PageTitle', 'PageDescription'));
    }

    private function getUnitMeasurements($applicationId) 
    {
        // Get all measurements from the st_unit_measurements table based on application_id
        $measurements = DB::connection('sqlsrv')->table('st_unit_measurements')
            ->select(
                'application_id',
                'buyer_id',
                'unit_no',
                'measurement',
                'created_at',
                'updated_at'
            )
            ->where('application_id', $applicationId)
            ->get();
            
        return $measurements;
    }

    public function generateSTMemo($id)
    {
        $PageTitle = 'Generate Physical Planning Memo';
        $PageDescription = 'Create a new sectional titling memo';
        
        // Check if this is a primary application
        $isPrimary = DB::connection('sqlsrv')->table('mother_applications')->where('id', $id)->exists();
        
        if ($isPrimary) {
            // Get the primary application details
            $application = $this->getPrimaryApplication($id);
            if ($application instanceof \Illuminate\Http\JsonResponse) {
                return $application;
            }
            
            // Get conveyance data from the buyer_list table
            $conveyanceData = DB::connection('sqlsrv')->table('buyer_list')
                ->select('*')
                ->where('application_id', $id)
                ->get();
            
            // Get unit measurements if they exist
            $unitMeasurements = $this->getUnitMeasurements($id);
            
            return view('stmemo.generate', compact('application', 'conveyanceData', 'unitMeasurements', 'PageTitle', 'PageDescription', 'isPrimary'));
        } else {
            // Existing code for secondary application
            $application = $this->getSecondaryApplication($id);
            if ($application instanceof \Illuminate\Http\JsonResponse) {
                return $application;
            }
            
            // Get conveyance data from the buyer_list table for the primary application
            $conveyanceData = DB::connection('sqlsrv')->table('buyer_list')
                ->select('*')
                ->where('application_id', $application->main_application_id)
                ->get();
            
            // Get unit measurements if they exist
            $unitMeasurements = $this->getUnitMeasurements($id);
            
            return view('stmemo.generate', compact('application', 'conveyanceData', 'unitMeasurements', 'PageTitle', 'PageDescription', 'isPrimary'));
        }
    }
    
    public function saveSTMemo(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'application_id' => 'required',
            'property_location' => 'required',
            'applicant_name' => 'required',
            'sections' => 'required|array',
            'shared_facilities' => 'required'
        ], [
            'sections.required' => 'Buyers list and Unit Measurements are Required'
        ]);
        
        // Generate next memo number
        $lastMemo = DB::connection('sqlsrv')->table('memos')
            ->orderBy('id', 'desc')
            ->first();
            
        $currentYear = date('Y');
        $memoNo = 'MEMO/' . $currentYear . '/01';
        
        if ($lastMemo) {
            $lastMemoNo = $lastMemo->memo_no;
            $parts = explode('/', $lastMemoNo);
            if (count($parts) == 3 && $parts[1] == $currentYear) {
                $nextNumber = intval($parts[2]) + 1;
                $memoNo = 'MEMO/' . $currentYear . '/' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
            }
        }
        
        // Create the memo record
        $memoId = DB::connection('sqlsrv')->table('memos')->insertGetId([
            'memo_no' => $memoNo,
            'application_id' => $request->application_id,
            'memo_type' => 'physical_planning',
            'memo_status' => 'GENERATED',
            'applicant_name' => $request->applicant_name,
            'property_location' => $request->property_location,
            'created_by' => Auth::id(),
            'created_at' => now(),
            'shared_facilities' => $request->shared_facilities
        ]);
        
     
        
        // Update the application status
        if (isset($request->is_primary) && $request->is_primary == '1') {
            DB::connection('sqlsrv')->table('memos')
                ->where('id', $memoId)
                ->update(['memo_status' => 'GENERATED']);
        } else {
            
        }
        
        return redirect()->route('stmemo.view', $request->application_id)->with('success', 'Physical Planning Memo has been successfully generated');
    }
    
    public function viewSTMemo($id)
    {
        $PageTitle = 'View Physical Planning Memo';
        $PageDescription = 'View sectional titling memo details';
        
        // Get the memo details
        $memo = DB::connection('sqlsrv')->table('memos')
            ->where('application_id', $id)
            ->where('memo_type', 'physical_planning')
            ->first();
        if (!$memo) {
            return redirect()->route('stmemo.siteplan')->with('error', 'Memo not found');
        }
        
        // Get the unit measurements
        $measurements = DB::connection('sqlsrv')->table('st_unit_measurements')
            ->where('application_id', $id)
            ->get();
        
        // Check if primary or secondary application
        $isPrimary = false;
        $application = null;
        
        // Try to get application from mother_applications first
        $primaryApp = DB::connection('sqlsrv')->table('mother_applications')
            ->where('id', $memo->application_id)
            ->first();
            
        if ($primaryApp) {
            $isPrimary = true;
            $application = $this->getPrimaryApplication($memo->application_id);
        } else {
            // If not found in primary, try secondary
            $application = $this->getSecondaryApplication($memo->application_id);
        }
        
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return redirect()->route('stmemo.stmemo')->with('error', 'Application not found');
        }
        
        return view('stmemo.view', compact('memo', 'measurements', 'application', 'PageTitle', 'PageDescription', 'isPrimary'));
    }
    
    private function getConveyanceData($mainApplicationId)
    {
        // Get all conveyance data from the buyer_list table based on application_id
        $buyerRecords = DB::connection('sqlsrv')->table('buyer_list')
            ->select(
                'application_id',
                'buyer_title',
                'buyer_name',
                'unit_no',
                'created_at',
                'updated_at'
            )
            ->where('application_id', $mainApplicationId)
            ->get();
            
        // Each item in the collection is an object, access properties with ->
        // For example: $buyerRecord->buyer_name, not $buyerRecord['buyer_name']
        return $buyerRecords;
    }
}