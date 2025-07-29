<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlanningRecommendationController extends Controller
{
    public function getSitePlanDimensions($applicationId)
    {
        $dimensions = DB::connection('sqlsrv')
            ->table('site_plan_dimensions')
            ->where('application_id', $applicationId)
            ->orderBy('order')
            ->get();

        return response()->json($dimensions);
    }

    public function getSharedUtilities($applicationId)
    {
        // First get existing shared utilities from the database
        $existingUtilities = DB::connection('sqlsrv')
            ->table('shared_utilities')
            ->where('application_id', $applicationId)
            ->orderBy('order')
            ->get();

        // Get shared areas from mother_applications
        $application = DB::connection('sqlsrv')
            ->table('mother_applications')
            ->where('id', $applicationId)
            ->first();

        if (!$application) {
            return response()->json([]);
        }

        $sharedAreasJson = $application->shared_areas;

        $sharedAreas = [];
        if (!empty($sharedAreasJson)) {
            if (is_string($sharedAreasJson)) {
                // Try to decode JSON string
                $sharedAreas = json_decode($sharedAreasJson, true);
            } elseif (is_array($sharedAreasJson)) {
                // Already an array
                $sharedAreas = $sharedAreasJson;
            }

            // If not array (could be a string or null), convert to array 
            if (!is_array($sharedAreas)) {
                // Try to convert comma-separated string to array if needed
                if (is_string($sharedAreasJson) && strpos($sharedAreasJson, ',') !== false) {
                    $sharedAreas = array_map('trim', explode(',', $sharedAreasJson));
                } else {
                    $sharedAreas = [];
                }
            }
        }

        // Get physical planning data
        $physicalPlanningData = DB::connection('sqlsrv')
            ->table('physicalPlanning')
            ->where('application_id', $applicationId)
            ->get();

        // Convert to collection and key by utility type
        $physicalPlanningDataKeyed = collect($physicalPlanningData)->keyBy('Shared_Utilities_List');

        // Prepare result - start with existing utilities
        $result = collect($existingUtilities);

        // Add entries from shared_areas that don't exist in utilities yet
        foreach ($sharedAreas as $area) {
            if (empty($area)) continue; // Skip empty entries

            // Skip if already exists in the result
            $exists = $result->where('utility_type', $area)->first();
            if ($exists) {
                continue;
            }

            // Create new utility entry from shared_area
            $newUtility = (object)[
                'id' => null,
                'application_id' => $applicationId,
                'utility_type' => $area,
                'dimension' => 0,
                'count' => 1,  // Default to 1 count
                'order' => $result->count() + 1
            ];

            // Check if we have data in physicalPlanning
            if ($physicalPlanningDataKeyed->has($area)) {
                $ppData = $physicalPlanningDataKeyed[$area];
                $newUtility->dimension = $ppData->Recommended_Size ?? 0;
                $newUtility->count = $ppData->count ?? 1;
            }

            $result->push($newUtility);
        }

        // If we still have no utilities, create at least one with common default
        if ($result->isEmpty() && !empty($sharedAreas)) {
            $result->push((object)[
                'id' => null,
                'application_id' => $applicationId,
                'utility_type' => $sharedAreas[0] ?? 'Common Area',
                'dimension' => 0,
                'count' => 1,
                'order' => 1
            ]);
        }

        return response()->json($result->values());
    }

    public function saveSitePlanDimension(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer',
            'description' => 'required|string|max:255',
            'dimension' => 'required|numeric',
            'order' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = $request->input('id');
        $data = [
            'application_id' => $request->input('application_id'),
            'description' => $request->input('description'),
            'dimension' => $request->input('dimension'),
            'order' => $request->input('order', 0)
        ];

        if ($id) {
            // Update existing record
            DB::connection('sqlsrv')
                ->table('site_plan_dimensions')
                ->where('id', $id)
                ->update($data);

            $dimension = DB::connection('sqlsrv')
                ->table('site_plan_dimensions')
                ->where('id', $id)
                ->first();
        } else {
            // Create new record
            $id = DB::connection('sqlsrv')
                ->table('site_plan_dimensions')
                ->insertGetId($data);

            $dimension = DB::connection('sqlsrv')
                ->table('site_plan_dimensions')
                ->where('id', $id)
                ->first();
        }

        return response()->json(['success' => true, 'dimension' => $dimension]);
    }

    public function saveSharedUtility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer',
            'utility_type' => 'required|string|max:255',
            'dimension' => 'required|numeric',
            'count' => 'required|integer',
            'order' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = $request->input('id');
        $applicationId = $request->input('application_id');
        $utilityType = $request->input('utility_type');
        $dimension = $request->input('dimension');
        $count = $request->input('count');
        $order = $request->input('order', 0);

        $data = [
            'application_id' => $applicationId,
            'utility_type' => $utilityType,
            'dimension' => $dimension,
            'count' => $count,
            'order' => $order
        ];

        // Update or create in shared_utilities table
        if ($id) {
            // Update existing record
            DB::connection('sqlsrv')
                ->table('shared_utilities')
                ->where('id', $id)
                ->update($data);

            $utility = DB::connection('sqlsrv')
                ->table('shared_utilities')
                ->where('id', $id)
                ->first();
        } else {
            // Create new record
            $id = DB::connection('sqlsrv')
                ->table('shared_utilities')
                ->insertGetId($data);

            $utility = DB::connection('sqlsrv')
                ->table('shared_utilities')
                ->where('id', $id)
                ->first();
        }

        // Now also update or create in physicalPlanning table - with improved error handling
        try {
            $existingPP = DB::connection('sqlsrv')
                ->table('physicalPlanning')
                ->where('application_id', $applicationId)
                ->where('Shared_Utilities_List', $utilityType)
                ->first();

            $ppData = [
                'Recommended_Size' => $dimension,
                'count' => $count,
            ];

            if ($existingPP) {
                // Update existing record
                DB::connection('sqlsrv')
                    ->table('physicalPlanning')
                    ->where('id', $existingPP->id)
                    ->update($ppData);
            } else {
                // Create new record in physicalPlanning
                DB::connection('sqlsrv')
                    ->table('physicalPlanning')
                    ->insertGetId([
                        'application_id' => $applicationId,
                        'Shared_Utilities_List' => $utilityType,
                        'Recommended_Size' => $dimension,
                        'count' => $count,
                        'Area_Under_Application' => 'Auto-generated from planning recommendation'
                    ]);
            }
        } catch (\Exception $e) {
            // Continue without failing the whole operation
        }

        return response()->json(['success' => true, 'utility' => $utility]);
    }

    /**
     * Batch update utilities and sync with physicalPlanning table
     */
    public function batchUpdateUtilities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer',
            'utilities' => 'required|array',
            'utilities.*.utility_type' => 'required|string|max:255',
            'utilities.*.dimension' => 'required|numeric',
            'utilities.*.count' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $applicationId = $request->input('application_id');
        $utilities = $request->input('utilities');
        $updatedUtilities = [];

        foreach ($utilities as $utilityData) {
            $id = $utilityData['id'] ?? null;
            $utilityType = $utilityData['utility_type'];
            $dimension = $utilityData['dimension'];
            $count = $utilityData['count'];

            $data = [
                'application_id' => $applicationId,
                'utility_type' => $utilityType,
                'dimension' => $dimension,
                'count' => $count,
                'order' => $utilityData['order'] ?? 0
            ];

            // Update or create in shared_utilities table
            if ($id) {
                DB::connection('sqlsrv')
                    ->table('shared_utilities')
                    ->where('id', $id)
                    ->update($data);

                $utility = DB::connection('sqlsrv')
                    ->table('shared_utilities')
                    ->where('id', $id)
                    ->first();
            } else {
                $id = DB::connection('sqlsrv')
                    ->table('shared_utilities')
                    ->insertGetId($data);

                $utility = DB::connection('sqlsrv')
                    ->table('shared_utilities')
                    ->where('id', $id)
                    ->first();
            }

            // Update or create in physicalPlanning table
            try {
                $existingPP = DB::connection('sqlsrv')
                    ->table('physicalPlanning')
                    ->where('application_id', $applicationId)
                    ->where('Shared_Utilities_List', $utilityType)
                    ->first();

                if ($existingPP) {
                    DB::connection('sqlsrv')
                        ->table('physicalPlanning')
                        ->where('id', $existingPP->id)
                        ->update([
                            'Recommended_Size' => $dimension,
                            'count' => $count,
                        ]);
                } else {
                    DB::connection('sqlsrv')
                        ->table('physicalPlanning')
                        ->insertGetId([
                            'application_id' => $applicationId,
                            'Shared_Utilities_List' => $utilityType,
                            'Recommended_Size' => $dimension,
                            'count' => $count,
                            'Area_Under_Application' => 'Auto-generated from batch update'
                        ]);
                }
            } catch (\Exception $e) {
                // Continue without failing
            }

            $updatedUtilities[] = $utility;
        }

        return response()->json([
            'success' => true,
            'message' => 'All utilities updated successfully',
            'utilities' => $updatedUtilities
        ]);
    }

    public function deleteSitePlanDimension(Request $request)
    {
        $id = $request->input('id');

        DB::connection('sqlsrv')
            ->table('site_plan_dimensions')
            ->where('id', $id)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function deleteSharedUtility(Request $request)
    {
        $id = $request->input('id');

        DB::connection('sqlsrv')
            ->table('shared_utilities')
            ->where('id', $id)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Debug endpoint to view shared areas data
     */
    public function debugSharedAreas($applicationId)
    {
        // Get application data
        $application = DB::connection('sqlsrv')
            ->table('mother_applications')
            ->where('id', $applicationId)
            ->first();

        if (!$application) {
            return response()->json([
                'error' => 'Application not found',
                'application_id' => $applicationId
            ]);
        }

        // Get shared areas
        $sharedAreasRaw = $application->shared_areas;
        $sharedAreasParsed = null;

        if (is_string($sharedAreasRaw)) {
            // Try to parse as JSON
            $sharedAreasParsed = json_decode($sharedAreasRaw, true);
        }

        // Get all utilities for this application
        $utilities = DB::connection('sqlsrv')
            ->table('shared_utilities')
            ->where('application_id', $applicationId)
            ->get();

        // Get physical planning data
        $physicalPlanning = DB::connection('sqlsrv')
            ->table('physicalPlanning')
            ->where('application_id', $applicationId)
            ->get();

        return response()->json([
            'application_id' => $applicationId,
            'shared_areas_raw' => $sharedAreasRaw,
            'shared_areas_parsed' => $sharedAreasParsed,
            'utilities_count' => count($utilities),
            'utilities' => $utilities,
            'physical_planning_count' => count($physicalPlanning),
            'physical_planning' => $physicalPlanning
        ]);
    }

    public function ApprovalMome(Request $request)
    {
        $PageTitle = 'Application for planning recommendation approval';
        $PageDescription = '';
        
        // Load application data if ID is provided
        $application = null;
        $surveyRecord = null;
        $additionalObservations = null;
        
        if ($request->has('id')) {
            $application = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->where('id', $request->get('id'))
                ->first();
                
            if ($application) {
                $surveyRecord = DB::connection('sqlsrv')
                    ->table('surveyCadastralRecord')
                    ->where('application_id', $application->id)
                    ->first();
                    
                // Retrieve additional observations
                $additionalObservations = DB::connection('sqlsrv')
                    ->table('planning_approval_details')
                    ->where('application_id', $application->id)
                    ->value('additional_observations');
            }
        }
        
        return view('pr_memos.approval', compact('PageTitle', 'PageDescription', 'application', 'surveyRecord', 'additionalObservations'));
    }

    // New method to save additional observations
    public function saveAdditionalObservations(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer',
            'additional_observations' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Check if record exists
            $exists = DB::connection('sqlsrv')
                ->table('planning_approval_details')
                ->where('application_id', $request->application_id)
                ->exists();
                
            if ($exists) {
                // Update existing record
                DB::connection('sqlsrv')
                    ->table('planning_approval_details')
                    ->where('application_id', $request->application_id)
                    ->update([
                        'additional_observations' => $request->additional_observations,
                        'updated_at' => now(),
                        'updated_by' => auth()->user()->name ?? 'system'
                    ]);
            } else {
                // Create new record
                DB::connection('sqlsrv')
                    ->table('planning_approval_details')
                    ->insert([
                        'application_id' => $request->application_id,
                        'additional_observations' => $request->additional_observations,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'created_by' => auth()->user()->name ?? 'system',
                        'updated_by' => auth()->user()->name ?? 'system'
                    ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Additional observations saved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving additional observations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save additional observations: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ApprovalMomeSave(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer',
            'memo_date' => 'required|date',
            'director_name' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Update the application status if not already approved
            $updated = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->where('id', $request->application_id)
                ->whereNotIn('planning_recommendation_status', ['approve', 'approved'])
                ->update([
                    'planning_recommendation_status' => 'approved',
                    'planning_approval_date' => $request->memo_date,
                    'updated_by' => auth()->user()->name ?? 'system',
                    'updated_at' => now()
                ]);
                
            return response()->json([
                'success' => true,
                'message' => 'Approval memo saved successfully',
                'updated' => $updated > 0
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving approval memo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save approval memo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function Declination(Request $request)
    {
        $PageTitle = 'Declination of Planning Recommendation for Sectional Titling';
        $PageDescription = '';
        
        // Load application data if ID is provided
        $application = null;
        $surveyRecord = null;
        $declineReasons = null;
        
        if ($request->has('id')) {
            $application = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->where('id', $request->get('id'))
                ->first();
                
            if ($application) {
                $surveyRecord = DB::connection('sqlsrv')
                    ->table('surveyCadastralRecord')
                    ->where('application_id', $application->id)
                    ->first();
                    
                $declineReasons = DB::connection('sqlsrv')
                    ->table('planning_decline_reasons')
                    ->where('application_id', $application->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }
        
        return view('pr_memos.declination', compact('PageTitle', 'PageDescription', 'application', 'surveyRecord', 'declineReasons'));
    }

    public function DeclinationSave(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer',
            'approval_date' => 'required|date',
            
            // Main reason flags
            'accessibility_selected' => 'nullable|boolean',
            'land_use_selected' => 'nullable|boolean',
            'utility_selected' => 'nullable|boolean',
            'road_reservation_selected' => 'nullable|boolean',
            
            // Simplified form fields for major reasons
            'access_road_details' => 'nullable|string',
            'pedestrian_details' => 'nullable|string',
            'zoning_details' => 'nullable|string',
            'density_details' => 'nullable|string',
            'overhead_details' => 'nullable|string',
            'underground_details' => 'nullable|string',
            'right_of_way_details' => 'nullable|string',
            'road_width_details' => 'nullable|string',
            
            // Complete formatted reason summary
            'reason_summary' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            DB::connection('sqlsrv')->beginTransaction();
            
            // Save the decline reasons
            $declineId = DB::connection('sqlsrv')
                ->table('planning_decline_reasons')
                ->insertGetId([
                    'application_id' => $request->application_id,
                    'submitted_by' => $request->submitted_by ?? auth()->id() ?? 1,
                    'approval_date' => $request->approval_date,
                    
                    // Main reason flags
                    'accessibility_selected' => $request->accessibility_selected ?? 0,
                    'land_use_selected' => $request->land_use_selected ?? 0,
                    'utility_selected' => $request->utility_selected ?? 0,
                    'road_reservation_selected' => $request->road_reservation_selected ?? 0,
                    
                    // Simplified form fields mapped to database columns
                    'access_road_details' => $request->access_road_details,
                    'pedestrian_details' => $request->pedestrian_details,
                    'zoning_details' => $request->zoning_details,
                    'density_details' => $request->density_details,
                    'overhead_details' => $request->overhead_details,
                    'underground_details' => $request->underground_details,
                    'right_of_way_details' => $request->right_of_way_details,
                    'road_width_details' => $request->road_width_details,
                    
                    // Complete formatted reason text
                    'reason_summary' => $request->reason_summary,
                    
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => auth()->user()->name ?? 'system',
                    'updated_by' => auth()->user()->name ?? 'system'
                ]);
                
            // Update the application status
            DB::connection('sqlsrv')
                ->table('mother_applications')
                ->where('id', $request->application_id)
                ->update([
                    'planning_recommendation_status' => 'declined',
                    'planning_approval_date' => $request->approval_date,
                    'recomm_comments' => $request->reason_summary,
                    'updated_by' => auth()->user()->name ?? 'system',
                    'updated_at' => now()
                ]);
                
            DB::connection('sqlsrv')->commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Declination memo saved successfully',
                'decline_id' => $declineId
            ]);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            \Log::error('Error saving declination memo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save declination memo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show recommendation page
     */
    public function showRecommendation(Request $request, $id)
    {
        // ...existing code...
        
        // Add retrieving additional observations
        $additionalObservations = DB::connection('sqlsrv')
            ->table('planning_approval_details')
            ->where('application_id', $id)
            ->value('additional_observations');
            
        return view('actions.recommendation', compact(
            'application', 
            'additionalObservations'
            // ...other variables...
        ));
    }
}