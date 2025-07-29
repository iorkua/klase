<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Add Auth facade for user tracking

class PropertyRecordController extends Controller
{
    /**
     * Store a newly created property record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

       public function index()
    {
        $PageTitle = 'Property Record Assistant
';
        $PageDescription = '';
        
        // Specify the table before using get()
        $Property_records = DB::connection('sqlsrv')->table('property_records')->get();

        $pageLength = 50; // set default page length
        return view('propertycard.index', compact('pageLength', 'PageTitle', 'PageDescription', 'Property_records'));
    } 
     

    
    public function store(Request $request)
    {
        // Updated validation rules for new field names
        $validator = Validator::make($request->all(), [
            'titleType' => 'required|string|in:Customary,Statutory',
            'mlsFNo' => 'nullable|string',
            'kangisFileNo' => 'nullable|string',
            'NewKANGISFileno' => 'nullable|string',
            'transactionType' => 'required|string',
            'transactionDate' => 'required|date',
            'serialNo' => 'required|string',
            'pageNo' => 'required|string',
            'volumeNo' => 'required|string',
            'instrumentType' => 'nullable|string',
            'period' => 'nullable|numeric',
            'periodUnit' => 'nullable',
            // Party fields based on transaction type
            'Assignor' => 'nullable|string',
            'Assignee' => 'nullable|string',
            'Mortgagor' => 'nullable|string',
            'Mortgagee' => 'nullable|string',
            'Surrenderor' => 'nullable|string',
            'Surrenderee' => 'nullable|string',
            'Lessor' => 'nullable|string',
            'Lessee' => 'nullable|string',
            'Grantor' => 'nullable|string',
            'Grantee' => 'nullable|string',
            // Property details
            'property_description' => 'nullable|string',
            'location' => 'nullable|string',
            'plot_no' => 'nullable|string',
            // New fields
            'lgsaOrCity' => 'nullable|string',
            'layout' => 'nullable|string',
            'schedule' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if at least one file number is provided
            if (empty($request->mlsFNo) && empty($request->kangisFileNo) && empty($request->NewKANGISFileno)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'At least one file number type is required'
                ], 422);
            }
            
            // Create registration number from components
            $regNo = $request->serialNo . '/' . $request->pageNo . '/' . $request->volumeNo;
            
            // Get transaction-specific party information
            $partyData = [];
            
            switch ($request->transactionType) {
                case 'assignment':
                    $partyData['Assignor'] = $request->input('trans-assignor-record');
                    $partyData['Assignee'] = $request->input('trans-assignee-record');
                    break;
                case 'mortgage':
                    $partyData['Mortgagor'] = $request->input('mortgagor-record');
                    $partyData['Mortgagee'] = $request->input('mortgagee-record');
                    break;
                case 'surrender':
                    $partyData['Surrenderor'] = $request->input('surrenderor-record');
                    $partyData['Surrenderee'] = $request->input('surrenderee-record');
                    break;
                case 'sublease':
                case 'lease':
                case 'sub-under-lease':
                    $partyData['Lessor'] = $request->input('lessor-record');
                    $partyData['Lessee'] = $request->input('lessee-record');
                    break;
                default:
                    $partyData['Grantor'] = $request->input('grantor-record');
                    $partyData['Grantee'] = $request->input('grantee-record');
            }

            // Prepare data for database insertion
            $data = [
                'mlsFNo' => $request->mlsFNo,
                'kangisFileNo' => $request->kangisFileNo,
                'NewKANGISFileno' => $request->NewKANGISFileno,
                'title_type' => $request->titleType,
                'transaction_type' => $request->transactionType,
                'transaction_date' => $request->transactionDate,
                'serialNo' => $request->serialNo,
                'pageNo' => $request->pageNo,
                'volumeNo' => $request->volumeNo,
                'regNo' => $regNo,
                'instrument_type' => $request->instrumentType,
                'period' => $request->period,
                'period_unit' => $request->periodUnit,
                'property_description' => $request->property_description,
                'location' => $request->location,
                'plot_no' => $request->plot_no,
                // New fields
                'lgsaOrCity' => $request->lgsaOrCity,
                'layout' => $request->layout,
                'schedule' => $request->schedule,
                // Add user tracking fields
                'created_by' => Auth::id(), // Record who created the record
                'updated_by' => Auth::id(), // Initial update is same as creator
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Merge party data
            $data = array_merge($data, $partyData);

            // Insert into database
            $id = DB::connection('sqlsrv')->table('property_records')->insertGetId($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Property record created successfully',
                'data' => ['id' => $id]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create property record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified property record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Modified validation rules for update - make file numbers optional
        $validator = Validator::make($request->all(), [
            'titleType' => 'required',
            'transactionType' => 'required|string',
            'transactionDate' => 'required|date',
            'serialNo' => 'required|string',
            'pageNo' => 'required|string',
            'volumeNo' => 'required|string',
            'instrumentType' => 'nullable|string',
            'period' => 'nullable|numeric',
            'periodUnit' => 'nullable',
            // Party fields
            'Assignor' => 'nullable|string',
            'Assignee' => 'nullable|string',
            'Mortgagor' => 'nullable|string',
            'Mortgagee' => 'nullable|string',
            'Surrenderor' => 'nullable|string',
            'Surrenderee' => 'nullable|string',
            'Lessor' => 'nullable|string',
            'Lessee' => 'nullable|string',
            'Grantor' => 'nullable|string',
            'Grantee' => 'nullable|string',
            // Property details
            'property_description' => 'nullable|string',
            'location' => 'nullable|string',
            'plot_no' => 'nullable|string',
            // New fields
            'lgsaOrCity' => 'nullable|string',
            'layout' => 'nullable|string',
            'schedule' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Do NOT check for file numbers here - they are read-only in edit form
            // Remove this block to avoid validation errors
            /*
            if (empty($request->mlsFNo) && empty($request->kangisFileNo) && empty($request->NewKANGISFileno)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'At least one file number type is required'
                ], 422);
            }
            */
            
            // Create registration number from components
            $regNo = $request->serialNo . '/' . $request->pageNo . '/' . $request->volumeNo;
            
            // Get existing property record to preserve file numbers
            $existingProperty = DB::connection('sqlsrv')->table('property_records')
                ->where('id', $id)
                ->first();
                
            if (!$existingProperty) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property record not found'
                ], 404);
            }
            
            // Get transaction-specific party information from edit form
            $partyData = [];
            
            // Add debug output to help diagnose party field issues
            \Log::info('Transaction Type: ' . $request->transactionType);
            \Log::info('Request data: ', $request->all());
            
            switch (strtolower($request->transactionType)) {
                case 'assignment':
                    $partyData['Assignor'] = $request->input('Assignor');
                    $partyData['Assignee'] = $request->input('Assignee');
                    break;
                case 'mortgage':
                    $partyData['Mortgagor'] = $request->input('Mortgagor');
                    $partyData['Mortgagee'] = $request->input('Mortgagee');
                    break;
                case 'surrender':
                    $partyData['Surrenderor'] = $request->input('Surrenderor');
                    $partyData['Surrenderee'] = $request->input('Surrenderee');
                    break;
                case 'sub-lease':
                case 'lease':
                    $partyData['Lessor'] = $request->input('Lessor');
                    $partyData['Lessee'] = $request->input('Lessee');
                    break;
                default:
                    $partyData['Grantor'] = $request->input('Grantor');
                    $partyData['Grantee'] = $request->input('Grantee');
            }
            
            // Log party data for debugging
            \Log::info('Party data to be updated: ', $partyData);

            // Prepare data for database update
            $data = [
                // Keep existing file numbers
                'mlsFNo' => $existingProperty->mlsFNo,
                'kangisFileNo' => $existingProperty->kangisFileNo,
                'NewKANGISFileno' => $existingProperty->NewKANGISFileno,
                
                'title_type' => $request->titleType,
                'transaction_type' => $request->transactionType,
                'transaction_date' => $request->transactionDate,
                'serialNo' => $request->serialNo,
                'pageNo' => $request->pageNo,
                'volumeNo' => $request->volumeNo,
                'regNo' => $regNo,
                'instrument_type' => $request->instrumentType,
                'period' => $request->period,
                'period_unit' => $request->periodUnit,
                'property_description' => $request->property_description,
                'plot_no' => $request->plot_no,
                // New fields
                'lgsaOrCity' => $request->lgsaOrCity,
                'layout' => $request->layout,
                'schedule' => $request->schedule,
                // Update the user who modified the record
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ];

            // Merge party data only if values are not null
            foreach ($partyData as $key => $value) {
                if ($value !== null) {
                    $data[$key] = $value;
                }
            }

            // Update the database record
            DB::connection('sqlsrv')->table('property_records')
                ->where('id', $id)
                ->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Property record updated successfully'
            ]);
        } catch (\Exception $e) {
            // Add more detailed error logging
            \Log::error('Error updating property record: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update property record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified property record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::connection('sqlsrv')->table('property_records')
                ->where('id', $id)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Property record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete property record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified property record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $property = DB::connection('sqlsrv')->table('property_records')
                ->where('id', $id)
                ->first();

            if (!$property) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property record not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $property
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve property record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
