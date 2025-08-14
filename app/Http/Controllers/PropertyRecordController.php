<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Add Auth facade for user tracking
use Illuminate\Support\Facades\Schema; // Schema facade to check columns safely

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
        $PageTitle = 'Property Record Assistant';
        $PageDescription = '';
        
        // Specify the table before using get()
        $Property_records = DB::connection('sqlsrv')->table('property_records')->get();

        $pageLength = 50; // set default page length
        return view('propertycard.index', compact('pageLength', 'PageTitle', 'PageDescription', 'Property_records'));
    } 
     

    
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Property Record Store Request:', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url(),
            'user_agent' => $request->userAgent()
        ]);

        // Updated validation rules for new field names
        $validator = Validator::make($request->all(), [
            'mlsFNo' => 'nullable|string|max:255',
            'kangisFileNo' => 'nullable|string|max:255',
            'NewKANGISFileno' => 'nullable|string|max:255',
            'fileno' => 'nullable|string|max:255',
            'title_type' => 'nullable|string|max:255', // Add title_type validation
            'transactionType' => 'required|string|max:255',
            'transactionDate' => 'required|date',
            'serialNo' => 'required|string|max:50',
            'pageNo' => 'required|string|max:50',
            'volumeNo' => 'required|string|max:50',
            'instrumentType' => 'nullable|string|max:255',
            'period' => 'nullable|numeric',
            'periodUnit' => 'nullable|string|max:50',
            // Party fields based on transaction type
            'Assignor' => 'nullable|string|max:500',
            'Assignee' => 'nullable|string|max:500',
            'Mortgagor' => 'nullable|string|max:500',
            'Mortgagee' => 'nullable|string|max:500',
            'Surrenderor' => 'nullable|string|max:500',
            'Surrenderee' => 'nullable|string|max:500',
            'Lessor' => 'nullable|string|max:500',
            'Lessee' => 'nullable|string|max:500',
            'Grantor' => 'nullable|string|max:500',
            'Grantee' => 'nullable|string|max:500',
            // Property details
            'property_description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'plot_no' => 'nullable|string|max:100',
            // New fields
            'lgsaOrCity' => 'nullable|string|max:255',
            'layout' => 'nullable|string|max:255',
            'schedule' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            \Log::error('Property Record Validation Failed:', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);

            // Check if request expects JSON (AJAX) or normal redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Start database transaction
        DB::connection('sqlsrv')->beginTransaction();

        try {
            // First, check if the property_records table exists
            $tableExists = Schema::connection('sqlsrv')->hasTable('property_records');
            if (!$tableExists) {
                throw new \Exception('Table property_records does not exist in the database');
            }

            // Log table columns for debugging
            $columns = Schema::connection('sqlsrv')->getColumnListing('property_records');
            \Log::info('Property Records Table Columns:', $columns);

            // Normalize file numbers coming from either manual fields or smart selector
            $mls = trim((string) $request->input('mlsFNo', ''));
            $kangis = trim((string) $request->input('kangisFileNo', ''));
            $newKangis = trim((string) $request->input('NewKANGISFileno', ''));
            $singleFileno = trim((string) $request->input('fileno', ''));

            // If only a single fileno is provided via smart selector, map it to MLS by default
            if ($mls === '' && $kangis === '' && $newKangis === '' && $singleFileno !== '') {
                $mls = $singleFileno;
            }

            // Check if at least one file number is provided
            if ($mls === '' && $kangis === '' && $newKangis === '') {
                throw new \Exception('At least one file number type is required');
            }
            
            // Create registration number from components
            $regNo = $request->serialNo . '/' . $request->pageNo . '/' . $request->volumeNo;
            
            // Get transaction-specific party information (robust to labels and field names)
            $partyData = [];
            $tx = strtolower((string)$request->transactionType);
            
            \Log::info('Processing transaction type:', ['type' => $tx]);
            
            if (strpos($tx, 'assignment') !== false) {
                $partyData['Assignor'] = $request->input('Assignor') ?? $request->input('trans-assignor-record');
                $partyData['Assignee'] = $request->input('Assignee') ?? $request->input('trans-assignee-record');
            } elseif (strpos($tx, 'mortgage') !== false) {
                $partyData['Mortgagor'] = $request->input('Mortgagor') ?? $request->input('mortgagor-record');
                $partyData['Mortgagee'] = $request->input('Mortgagee') ?? $request->input('mortgagee-record');
            } elseif (strpos($tx, 'surrender') !== false) {
                $partyData['Surrenderor'] = $request->input('Surrenderor') ?? $request->input('surrenderor-record');
                $partyData['Surrenderee'] = $request->input('Surrenderee') ?? $request->input('surrenderee-record');
            } elseif (strpos($tx, 'lease') !== false) {
                $partyData['Lessor'] = $request->input('Lessor') ?? $request->input('lessor-record');
                $partyData['Lessee'] = $request->input('Lessee') ?? $request->input('lessee-record');
            } else {
                $partyData['Grantor'] = $request->input('Grantor') ?? $request->input('grantor-record');
                $partyData['Grantee'] = $request->input('Grantee') ?? $request->input('grantee-record');
            }

            \Log::info('Party data extracted:', $partyData);

            // Determine title_type based on transaction type or use provided value
            $titleType = $request->input('title_type');
            if (!$titleType) {
                // Auto-determine title_type based on transaction_type
                $titleType = $this->determineTitleType($request->transactionType);
            }

            // Prepare base data for database insertion
            $data = [
                'mlsFNo' => $mls ?: null,
                'kangisFileNo' => $kangis ?: null,
                'NewKANGISFileno' => $newKangis ?: null,
                'title_type' => $titleType, // Add required title_type field
                'transaction_type' => $request->transactionType,
                'transaction_date' => $request->transactionDate,
                'serialNo' => $request->serialNo,
                'pageNo' => $request->pageNo,
                'volumeNo' => $request->volumeNo,
                'regNo' => $regNo,
                'instrument_type' => $request->instrumentType,
                'period' => $request->period ? (int)$request->period : null,
                'period_unit' => $request->periodUnit,
                'property_description' => $request->property_description,
                'plot_no' => $request->plot_no,
            ];

            // Conditionally include optional columns if they exist in the table
            $optionalFields = [
                'location' => $request->location,
                'lgsaOrCity' => $request->lgsaOrCity,
                'layout' => $request->layout,
                'schedule' => $request->schedule,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            foreach ($optionalFields as $field => $value) {
                if (Schema::connection('sqlsrv')->hasColumn('property_records', $field)) {
                    $data[$field] = $value;
                }
            }

            // Add party data only for non-null values
            foreach ($partyData as $key => $value) {
                if ($value !== null && $value !== '' && Schema::connection('sqlsrv')->hasColumn('property_records', $key)) {
                    $data[$key] = $value;
                }
            }

            \Log::info('Final data for insertion:', $data);

            // Insert into database
            $id = DB::connection('sqlsrv')->table('property_records')->insertGetId($data);

            // Commit the transaction
            DB::connection('sqlsrv')->commit();

            \Log::info('Property record created successfully:', ['id' => $id]);

            // Check if request expects JSON (AJAX) or normal redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Property record created successfully',
                    'data' => ['id' => $id]
                ], 201);
            }

            // For normal form submissions, redirect with success message
            return redirect()->route('propertycard.index')->with('success', 'Property record created successfully');

        } catch (\Exception $e) {
            // Rollback the transaction
            DB::connection('sqlsrv')->rollback();

            \Log::error('Property Record Creation Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            // Check if request expects JSON (AJAX) or normal redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create property record',
                    'error' => $e->getMessage(),
                    'debug_info' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ], 500);
            }

            // For normal form submissions, redirect with error message
            return redirect()->back()->with('error', 'Failed to create property record: ' . $e->getMessage())->withInput();
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

            // Prepare data for database update (keep file numbers; include optional columns only if present)
            $data = [
                'mlsFNo' => $existingProperty->mlsFNo,
                'kangisFileNo' => $existingProperty->kangisFileNo,
                'NewKANGISFileno' => $existingProperty->NewKANGISFileno,
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
            ];
            if (Schema::hasColumn('property_records', 'lgsaOrCity')) {
                $data['lgsaOrCity'] = $request->lgsaOrCity;
            }
            if (Schema::hasColumn('property_records', 'layout')) {
                $data['layout'] = $request->layout;
            }
            if (Schema::hasColumn('property_records', 'schedule')) {
                $data['schedule'] = $request->schedule;
            }
            if (Schema::hasColumn('property_records', 'updated_by')) {
                $data['updated_by'] = Auth::id();
            }
            if (Schema::hasColumn('property_records', 'updated_at')) {
                $data['updated_at'] = now();
            }

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

    /**
     * Search for file numbers for property records dropdown
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchFileNumbers(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $perPage = 10;
            $offset = ($page - 1) * $perPage;

            // Log the request for debugging
            \Log::info('File number search request:', [
                'search' => $search,
                'page' => $page,
                'method' => $request->method(),
                'all_input' => $request->all()
            ]);

            // Search across multiple sources for file numbers
            $fileNumbers = collect();

            // Search in existing property records
            if (!empty($search)) {
                $propertyRecords = DB::connection('sqlsrv')
                    ->table('property_records')
                    ->select('id', 'mlsFNo as fileno', 'property_description as description', 'plot_no', 'lgsaOrCity as lga', 'location')
                    ->where('mlsFNo', 'LIKE', "%{$search}%")
                    ->whereNotNull('mlsFNo')
                    ->where('mlsFNo', '!=', '')
                    ->limit($perPage)
                    ->get();

                foreach ($propertyRecords as $record) {
                    $fileNumbers->push([
                        'id' => 'property_' . $record->id,
                        'fileno' => $record->fileno,
                        'description' => $record->description,
                        'plot_no' => $record->plot_no,
                        'lga' => $record->lga,
                        'location' => $record->location,
                        'source' => 'property_records'
                    ]);
                }

                // Search KANGIS file numbers
                $kangisRecords = DB::connection('sqlsrv')
                    ->table('property_records')
                    ->select('id', 'kangisFileNo as fileno', 'property_description as description', 'plot_no', 'lgsaOrCity as lga', 'location')
                    ->where('kangisFileNo', 'LIKE', "%{$search}%")
                    ->whereNotNull('kangisFileNo')
                    ->where('kangisFileNo', '!=', '')
                    ->limit($perPage)
                    ->get();

                foreach ($kangisRecords as $record) {
                    $fileNumbers->push([
                        'id' => 'kangis_' . $record->id,
                        'fileno' => $record->fileno,
                        'description' => $record->description,
                        'plot_no' => $record->plot_no,
                        'lga' => $record->lga,
                        'location' => $record->location,
                        'source' => 'property_records'
                    ]);
                }

                // Search New KANGIS file numbers
                $newKangisRecords = DB::connection('sqlsrv')
                    ->table('property_records')
                    ->select('id', 'NewKANGISFileno as fileno', 'property_description as description', 'plot_no', 'lgsaOrCity as lga', 'location')
                    ->where('NewKANGISFileno', 'LIKE', "%{$search}%")
                    ->whereNotNull('NewKANGISFileno')
                    ->where('NewKANGISFileno', '!=', '')
                    ->limit($perPage)
                    ->get();

                foreach ($newKangisRecords as $record) {
                    $fileNumbers->push([
                        'id' => 'newkangis_' . $record->id,
                        'fileno' => $record->fileno,
                        'description' => $record->description,
                        'plot_no' => $record->plot_no,
                        'lga' => $record->lga,
                        'location' => $record->location,
                        'source' => 'property_records'
                    ]);
                }

                // Search in applications table if it exists
                try {
                    $applications = DB::connection('sqlsrv')
                        ->table('dbo.mother_applications')
                        ->select('id', 'fileno', 'plot_no', 'lga_name as lga', 'layout_name as location')
                        ->where('fileno', 'LIKE', "%{$search}%")
                        ->whereNotNull('fileno')
                        ->where('fileno', '!=', '')
                        ->limit($perPage)
                        ->get();

                    foreach ($applications as $app) {
                        $fileNumbers->push([
                            'id' => 'app_' . $app->id,
                            'fileno' => $app->fileno,
                            'description' => 'Application Record',
                            'plot_no' => $app->plot_no,
                            'lga' => $app->lga,
                            'location' => $app->location,
                            'source' => 'applications'
                        ]);
                    }
                } catch (\Exception $e) {
                    // Applications table might not exist or be accessible
                }
            } else {
                // If no search term, return recent file numbers or sample data
                try {
                    $recentRecords = DB::connection('sqlsrv')
                        ->table('property_records')
                        ->select('id', 'mlsFNo as fileno', 'property_description as description', 'plot_no', 'lgsaOrCity as lga', 'location')
                        ->whereNotNull('mlsFNo')
                        ->where('mlsFNo', '!=', '')
                        ->orderBy('created_at', 'desc')
                        ->limit($perPage)
                        ->get();

                    foreach ($recentRecords as $record) {
                        $fileNumbers->push([
                            'id' => 'recent_' . $record->id,
                            'fileno' => $record->fileno,
                            'description' => $record->description,
                            'plot_no' => $record->plot_no,
                            'lga' => $record->lga,
                            'location' => $record->location,
                            'source' => 'property_records'
                        ]);
                    }
                } catch (\Exception $e) {
                    // If database query fails, provide sample data for testing
                    $sampleData = [
                        [
                            'id' => 'sample_1',
                            'fileno' => 'COM-2023-001',
                            'description' => 'Commercial Property',
                            'plot_no' => '123',
                            'lga' => 'Kano Municipal',
                            'location' => 'Sabon Gari',
                            'source' => 'sample'
                        ],
                        [
                            'id' => 'sample_2',
                            'fileno' => 'RES-2023-002',
                            'description' => 'Residential Property',
                            'plot_no' => '456',
                            'lga' => 'Fagge',
                            'location' => 'Fagge Layout',
                            'source' => 'sample'
                        ],
                        [
                            'id' => 'sample_3',
                            'fileno' => 'KNML 00001',
                            'description' => 'KANGIS Property',
                            'plot_no' => '789',
                            'lga' => 'Gwale',
                            'location' => 'Gwale District',
                            'source' => 'sample'
                        ]
                    ];
                    
                    foreach ($sampleData as $sample) {
                        $fileNumbers->push($sample);
                    }
                }
            }

            // Remove duplicates based on fileno
            $uniqueFileNumbers = $fileNumbers->unique('fileno')->values();

            // Paginate results
            $total = $uniqueFileNumbers->count();
            $results = $uniqueFileNumbers->slice($offset, $perPage)->values();

            return response()->json([
                'success' => true,
                'file_numbers' => $results,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'more' => ($offset + $perPage) < $total
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching file numbers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determine title type based on transaction type
     *
     * @param string $transactionType
     * @return string
     */
    private function determineTitleType($transactionType)
    {
        $titleTypeMap = [
            'Certificate of Occupancy' => 'C of O',
            'ST Certificate of Occupancy' => 'ST C of O',
            'SLTR Certificate of Occupancy' => 'SLTR C of O',
            'Customary Right of Occupancy' => 'Customary R of O',
            'Deed of Assignment' => 'Assignment',
            'ST Assignment' => 'ST Assignment',
            'Deed of Mortgage' => 'Mortgage',
            'Tripartite Mortgage' => 'Tripartite Mortgage',
            'Deed of Lease' => 'Lease',
            'Deed of Sub Lease' => 'Sub Lease',
            'Deed of Sub Under Lease' => 'Sub Under Lease',
            'Indenture of Lease' => 'Indenture Lease',
            'Quarry Lease' => 'Quarry Lease',
            'Private Lease' => 'Private Lease',
            'Building Lease' => 'Building Lease',
            'Tenancy Agreement' => 'Tenancy',
            'Deed of Surrender' => 'Surrender',
            'Deed of Transfer' => 'Transfer',
            'Deed of Gift' => 'Gift',
            'Power of Attorney' => 'Power of Attorney',
            'Irrevocable Power of Attorney' => 'Irrevocable POA',
            'Deed of Release' => 'Release',
            'Letter of Administration' => 'Letter of Admin',
            'Certificate of Purchase' => 'Certificate of Purchase',
            'Deed of Variation' => 'Variation',
            'Vesting Assent' => 'Vesting Assent',
            'Court Judgement' => 'Court Judgement',
            'Exchange of Letters' => 'Exchange of Letters',
            'Revocation of Power of Attorney' => 'Revocation POA',
            'Deed of Convenyence' => 'Convenyence',
            'Memorandom of Agreement' => 'MOU',
            'Deed of Partition' => 'Partition',
            'Non-European Occupational Lease' => 'Non-European Lease',
            'Deed of Revocation' => 'Revocation',
            'Deed of Reconveyance' => 'Reconveyance',
            'Customary Inhertitance' => 'Customary Inheritance',
            'Deed of Rectification' => 'Rectification',
            'Memorandum of Loss' => 'Memorandum of Loss',
            'Vesting Deed' => 'Vesting Deed',
            'ST Fragmentation' => 'ST Fragmentation',
        ];

        return $titleTypeMap[$transactionType] ?? 'Other';
    }
}