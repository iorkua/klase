<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubActionsController extends Controller
{
    private function getApplication($id)
    {
        // Modified to join subapplications with mother_applications to get primary application details
        $application = DB::connection('sqlsrv')->table('subapplications')
            ->select(
                'subapplications.*', 
                'subapplications.id as applicationID', 
                'subapplications.main_application_id as main_application_id',  
                'mother_applications.fileno as primary_fileno',
                'mother_applications.applicant_type as primary_applicant_type',
                'mother_applications.first_name as primary_first_name',
                'mother_applications.surname as primary_surname',
                'mother_applications.applicant_title as primary_applicant_title',
                'mother_applications.id as primary_id',
                'mother_applications.application_status as primary_application_status',
                'mother_applications.land_use as primary_land_use',
                'mother_applications.id as main_application_id',

                // Property fields with proper aliases
                'mother_applications.property_house_no as property_house_no',
                'mother_applications.property_plot_no as property_plot_no',
                'mother_applications.property_street_name as property_street_name',
                'mother_applications.property_lga as property_lga',
                  'mother_applications.property_state as property_state',

                'mother_applications.np_fileno as np_fileno'  
            )
            ->leftJoin('mother_applications', 'subapplications.main_application_id', '=', 'mother_applications.id')
            ->where('subapplications.id', $id)
            ->first();

        if (!$application) {
            return response()->json(['error' => 'Sub application not found'], 404);
        }

        return $application;
    }

    public function OtherDepartments(Request $request, $id)
    {
        if ($request->query('is') === 'survey') {
            $PageTitle = 'SECTIONAL TITLE SURVEY';
            $PageDescription = 'Manage Survey Department Actions (Add/Update Survey Records)';
        } else {
            $PageTitle = 'OTHER DEPARTMENTS';
            $PageDescription = 'Sub-Application Departmental Actions';
        }
        
        $application = $this->getApplication($id);
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return $application;
        }

        return view('sub_actions.other_departments', compact('application', 'PageTitle', 'PageDescription'));
    }

    public function Bill($id)
    {
        $PageTitle = 'Bill';
        $PageDescription = 'Sub-Application Billing Details';
        
        $application = $this->getApplication($id);
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return $application;
        }

        return view('sub_actions.bill', compact('application', 'PageTitle', 'PageDescription'));
    }

    public function Payment($id)
    {
        $PageTitle = 'Payment';
        $PageDescription = 'Sub-Application Bills Management';
        
        $application = $this->getApplication($id);
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return $application;
        }

        return view('sub_actions.payments', compact('application', 'PageTitle', 'PageDescription'));
    }
    
    public function Recommendation($id)
    {
        $PageTitle = 'PLANNING RECOMMENDATION';
        $PageDescription = 'Sub-Application Planning Recommendation';
        
        $application = $this->getApplication($id);
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return $application;
        }

        return view('sub_actions.recommendation', compact('application', 'PageTitle', 'PageDescription'));
    }
 

    public function DirectorApproval($id)
    {
        $PageTitle = 'Director\'s Approval';
        $PageDescription = 'Sub-Application Director Approval';
        
        $application = $this->getApplication($id);
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return $application;
        }

        return view('sub_actions.director_approval', compact('application', 'PageTitle', 'PageDescription'));
    }
 
    public function updateArchitecturalDesign(Request $request, $applicationId)
    {
        $request->validate([
            'architectural_design' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);
    
        try {
            // Get the current application from the SQL Server database
            $application = DB::connection('sqlsrv')
                ->table('subapplications')
                ->where('id', $applicationId)
                ->first();
                
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub-application not found.'
                ], 404);
            }
            
            // Parse the existing documents JSON
            $documents = json_decode($application->documents, true) ?? [];
            
            // Upload the new file
            $file = $request->file('architectural_design');
            $path = $file->store('documents/subapplications', 'public');
            
            // Update only the architectural_design portion of the JSON
            $documents['architectural_design'] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'type' => $file->getClientOriginalExtension(),
                'uploaded_at' => now()->format('Y-m-d H:i:s')
            ];
            
            // Update the application in the SQL Server database
            DB::connection('sqlsrv')
                ->table('subapplications')
                ->where('id', $applicationId)
                ->update([
                    'documents' => json_encode($documents),
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Architectural design has been updated successfully.',
                'design' => [
                    'path' => $documents['architectural_design']['path'],
                    'uploaded_at' => $documents['architectural_design']['uploaded_at'],
                    'full_path' => asset('storage/app/public/' . $documents['architectural_design']['path'])
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating architectural design: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating architectural design. Please try again.'
            ], 500);
        }
    }

    // New method to update planning recommendation via AJAX
    public function updatePlanningRecommendation(Request $request)
    {
        try {
            $validated = $request->validate([
                'application_id' => 'required|integer',
                'status' => 'required|string|in:Approved,Declined',
                'approval_date' => 'required|date',
                'comments' => 'nullable|string'
            ]);
             
            DB::connection('sqlsrv')->table('subapplications')
                ->where('id', $validated['application_id'])
                ->update([
                    'planning_recommendation_status' => $validated['status'],
                    'planning_approval_date' => $validated['approval_date'],
                    'planning_recomm_comments' => $validated['comments'],
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Planning recommendation has been updated successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating planning recommendation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating planning recommendation. Please try again.'
            ], 500);
        }
    }

    // New method to update director approval via AJAX
    public function updateDirectorApproval(Request $request)
    {
        try {
            $validated = $request->validate([
                'application_id' => 'required|integer',
                'status' => 'required|string|in:Approved,Declined',
                'approval_date' => 'required|date',
                'comments' => 'nullable|string'
            ]);

            DB::connection('sqlsrv')->table('subapplications')
                ->where('id', $validated['application_id'])
                ->update([
                    'application_status' => $validated['status'],
                    'approval_date' => $validated['approval_date'],
                  
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Director approval has been updated successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating director approval: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating director approval. Please try again.'
            ], 500);
        }
    }

    // New method to store survey info via AJAX
    public function storeSurvey(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'sub_application_id' => 'required|integer',
            'fileno' => 'required|string|max:255',
            // Survey personnel information
            'survey_by' => 'required|string|max:255',
            'survey_by_date' => 'required|date',
            'drawn_by' => 'required|string|max:255',
            'drawn_by_date' => 'required|date',
            'checked_by' => 'required|string|max:255',
            'checked_by_date' => 'required|date',
            'approved_by' => 'required|string|max:255',
            'approved_by_date' => 'required|date',
            // Property Identification
            'plot_no' => 'nullable|string|max:255',
            'block_no' => 'nullable|string|max:255',
            'approved_plan_no' => 'nullable|string|max:255',
            'tp_plan_no' => 'nullable|string|max:255',
            // Beacon Control Information
            'beacon_control_name' => 'nullable|string|max:255',
            'Control_Beacon_Coordinate_X' => 'nullable|string|max:255',
            'Control_Beacon_Coordinate_Y' => 'nullable|string|max:255',
            // Sheet Information
            'Metric_Sheet_Index' => 'nullable|string|max:255',
            'Metric_Sheet_No' => 'nullable|string|max:255',
            'Imperial_Sheet' => 'nullable|string|max:255',
            'Imperial_Sheet_No' => 'nullable|string|max:255',
            // Location Information
            'layout_name' => 'nullable|string|max:255',
            'district_name' => 'nullable|string|max:255',
            'lga_name' => 'nullable|string|max:255',
        ]);

        // Insert the data into the database
        DB::connection('sqlsrv')->table('surveyCadastralRecord')->insert($validatedData);
 
        return redirect()->back()->with('success', 'Survey submitted successfully!');
    }
    // New method to store deeds info via AJAX
    public function storeDeeds(Request $request)
    {
        try {
            $validated = $request->validate([
                'application_id' => 'required|integer',
                'serial_no' => 'required|string|max:255',
                'page_no' => 'required|string|max:255',
                'volume_no' => 'required|string|max:255',
                'deeds_time' => 'required|string',
                'deeds_date' => 'required|date'
            ]);

            $deedsData = [
                'serial_no' => $validated['serial_no'],
                'page_no' => $validated['page_no'],
                'volume_no' => $validated['volume_no'],
                'deeds_time' => $validated['deeds_time'],
                'deeds_date' => $validated['deeds_date'],
                'updated_at' => now()
            ];

            DB::connection('sqlsrv')->table('subapplications')
                ->where('id', $validated['application_id'])
                ->update($deedsData);

            return response()->json([
                'success' => true,
                'message' => 'Deeds information has been saved successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving deeds information: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving deeds information. Please try again.'
            ], 500);
        }
    }

    // Method to get related subapplications for a primary application
    public function getRelatedSubApplications($primaryId)
    {
        try {
            $subapplications = DB::connection('sqlsrv')->table('subapplications')
                ->where('main_application_id', $primaryId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subapplications
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching related subapplications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching related subapplications. Please try again.'
            ], 500);
        }
    }

    public function FinalConveyance($id)
    {
        $PageTitle = 'Final Conveyance';
        $PageDescription = 'Sub-Application Final Conveyance Agreement';
        
        $application = $this->getApplication($id);
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return $application;
        }

        return view('sub_actions.final_conveyance', compact('application', 'PageTitle', 'PageDescription'));
    }
    
    // Method to get conveyance data
    public function getConveyance($applicationId)
    {
        try {
            $application = DB::connection('sqlsrv')->table('subapplications')
                ->where('id', $applicationId)
                ->first();
                
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub-application not found'
                ], 404);
            }
            
            $conveyanceData = json_decode($application->conveyance, true) ?? [];
            $records = $conveyanceData['records'] ?? [];
            
            return response()->json([
                'success' => true,
                'records' => $records
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving conveyance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving conveyance data. Please try again.'
            ], 500);
        }
    }
    
    // New method to update final conveyance via AJAX
    public function updateFinalConveyance(Request $request)
    {
        try {
            $validated = $request->validate([
                'application_id' => 'required|integer',
                'records' => 'required|array',
                'records.*.buyerTitle' => 'nullable|string',
                'records.*.buyerName' => 'required|string',
                'records.*.sectionNo' => 'required|string'
            ]);

            $conveyanceData = [
                'records' => $validated['records'],
                'updated_at' => now()->format('Y-m-d H:i:s')
            ];

            DB::connection('sqlsrv')->table('subapplications')
                ->where('id', $validated['application_id'])
                ->update([
                    'conveyance' => json_encode($conveyanceData),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Final conveyance has been updated successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating final conveyance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating final conveyance. Please try again.'
            ], 500);
        }
    }

    // New method to finalize conveyance via AJAX
    public function finalizeFinalConveyance(Request $request)
    {
        try {
            $validated = $request->validate([
                'application_id' => 'required|integer',
                'status' => 'required|string|in:completed,pending'
            ]);

            DB::connection('sqlsrv')->table('subapplications')
                ->where('id', $validated['application_id'])
                ->update([
                    'conveyance_status' => $validated['status'],
                    'conveyance_completed_at' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Final conveyance has been marked as ' . $validated['status'] . '.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error finalizing conveyance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error finalizing conveyance. Please try again.'
            ], 500);
        }
    }

    public function printPlanningRecommendation($id)
    {
        $PageTitle = 'PLANNING RECOMMENDATION';
        $PageDescription = 'Print Planning Recommendation for Sub-Application';
        
        $application = $this->getApplication($id);
        if ($application instanceof \Illuminate\Http\JsonResponse) {
            return $application;
        }

        return view('sub_actions.print_planning_recommendation', compact('application', 'PageTitle', 'PageDescription'));
    }
}
