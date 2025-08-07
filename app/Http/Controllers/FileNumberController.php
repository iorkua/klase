<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\FileNumber;

class FileNumberController extends Controller
{
    /**
     * Display the MLSF number generation page
     */
    public function index()
    {
        $totalCount = DB::connection('sqlsrv')
            ->table('fileNumber')
            ->where('type', 'Generated')
            ->count();

        return view('generate_fileno.mlsfno', compact('totalCount'));
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        try {
            // Get DataTables parameters
            $draw = $request->input('draw');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = $request->input('search.value', '');

            // Base query for counting (without ORDER BY)
            $baseQuery = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('type', 'Generated');

            // Get total count
            $totalRecords = $baseQuery->count();

            // Apply search if provided
            if (!empty($searchValue)) {
                $baseQuery->where(function($query) use ($searchValue) {
                    $query->where('mlsfNo', 'like', "%{$searchValue}%")
                          ->orWhere('created_by', 'like', "%{$searchValue}%");
                });
            }

            // Get filtered count
            $filteredRecords = $baseQuery->count();

            // Get the actual data with ordering and pagination
            $data = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->select([
                    'id',
                    'mlsfNo',
                    'type',
                    'created_by',
                    'created_at'
                ])
                ->where('type', 'Generated')
                ->when(!empty($searchValue), function($query) use ($searchValue) {
                    $query->where(function($q) use ($searchValue) {
                        $q->where('mlsfNo', 'like', "%{$searchValue}%")
                          ->orWhere('created_by', 'like', "%{$searchValue}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->skip($start)
                ->take($length)
                ->get();

            // Format the data
            $formattedData = $data->map(function($row) {
                return [
                    'id' => $row->id,
                    'mlsfNo' => $row->mlsfNo,
                    'created_by' => $row->created_by,
                    'created_at' => date('Y-m-d H:i:s', strtotime($row->created_at)),
                    'action' => '<div class="flex justify-center">
                        <button onclick="deleteRecord(' . $row->id . ')" 
                                class="text-red-600 hover:text-red-800 text-sm">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>'
                ];
            });

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in FileNumberController getData: ' . $e->getMessage());
            
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get the next serial number
     */
    public function getNextSerial()
    {
        $currentYear = date('Y');
        
        // Get the highest serial number for the current year
        $lastRecord = DB::connection('sqlsrv')
            ->table('fileNumber')
            ->where('type', 'Generated')
            ->where('mlsfNo', 'like', '%-' . $currentYear . '-%')
            ->orderByRaw('CAST(RIGHT(mlsfNo, 4) AS INT) DESC')
            ->first();

        $nextSerial = 1;
        
        if ($lastRecord) {
            // Extract the serial number from the MLSF number (last 4 digits)
            $lastSerial = (int) substr($lastRecord->mlsfNo, -4);
            $nextSerial = $lastSerial + 1;
        }

        return response()->json(['nextSerial' => $nextSerial]);
    }

    /**
     * Store a new MLSF number
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serial_no' => 'required|integer|min:1',
            'year' => 'required|integer|min:2020|max:2050',
            'land_use' => 'required|string|max:10',
            'application_type' => 'required|in:new,conversion'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate MLSF number
            $serialNo = str_pad($request->serial_no, 4, '0', STR_PAD_LEFT);
            $mlsfNo = $request->land_use . '-' . $request->year . '-' . $serialNo;

            // Check if MLSF number already exists
            $exists = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('mlsfNo', $mlsfNo)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'MLSF number already exists: ' . $mlsfNo
                ], 409);
            }

            // Insert new record
            $id = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->insertGetId([
                    'mlsfNo' => $mlsfNo,
                    'type' => 'Generated',
                    'created_by' => Auth::user()->name ?? Auth::user()->email,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'MLSF number generated successfully: ' . $mlsfNo,
                'data' => [
                    'id' => $id,
                    'mlsfNo' => $mlsfNo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating MLSF number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific record
     */
    public function show($id)
    {
        try {
            $record = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('id', $id)
                ->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            return response()->json($record);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a record
     */
    public function update(Request $request, $id)
    {
        try {
            $record = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('id', $id)
                ->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            // Update the record
            DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('id', $id)
                ->update([
                    'updated_by' => Auth::user()->name ?? Auth::user()->email,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a record
     */
    public function destroy($id)
    {
        try {
            $record = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('id', $id)
                ->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            // Delete the record
            DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get total count of generated file numbers
     */
    public function getCount()
    {
        try {
            $count = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('type', 'Generated')
                ->count();

            return response()->json(['count' => $count]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting count: ' . $e->getMessage()
            ], 500);
        }
    }
}