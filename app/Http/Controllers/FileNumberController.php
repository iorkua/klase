<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\FileNumber;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FileNumberController extends Controller
{
    /**
     * Display the MLS File number generation page
     */
    public function index()
    {
        $totalCount = DB::connection('sqlsrv')
            ->table('fileNumber')
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
                ->table('fileNumber');

            // Get total count
            $totalRecords = $baseQuery->count();

            // Apply search if provided
            if (!empty($searchValue)) {
                $baseQuery->where(function($query) use ($searchValue) {
                    $query->where('kangisFileNo', 'like', "%{$searchValue}%")
                          ->orWhere('NewKANGISFileNo', 'like', "%{$searchValue}%")
                          ->orWhere('FileName', 'like', "%{$searchValue}%")
                          ->orWhere('mlsfNo', 'like', "%{$searchValue}%");
                });
            }

            // Get filtered count
            $filteredRecords = $baseQuery->count();

            // Get the actual data with ordering and pagination
            $data = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->select([
                    'id',
                    'kangisFileNo',
                    'NewKANGISFileNo', 
                    'FileName',
                    'mlsfNo',
                    'created_by',
                    'created_at'
                ])
                ->when(!empty($searchValue), function($query) use ($searchValue) {
                    $query->where(function($q) use ($searchValue) {
                        $q->where('kangisFileNo', 'like', "%{$searchValue}%")
                          ->orWhere('NewKANGISFileNo', 'like', "%{$searchValue}%")
                          ->orWhere('FileName', 'like', "%{$searchValue}%")
                          ->orWhere('mlsfNo', 'like', "%{$searchValue}%");
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
                    'kangisFileNo' => $row->kangisFileNo ?? '-',
                    'NewKANGISFileNo' => $row->NewKANGISFileNo ?? '-',
                    'FileName' => $row->FileName ?? '-',
                    'mlsfNo' => $row->mlsfNo ?? '-',
                    'created_by' => $row->created_by ?? '-',
                    'created_at' => $row->created_at ? date('Y-m-d H:i:s', strtotime($row->created_at)) : '-',
                    'action' => '<div class="flex justify-center space-x-2">
                        <button onclick="editRecord(' . $row->id . ')" 
                                class="text-blue-600 hover:text-blue-800 text-sm" title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <button onclick="deleteRecord(' . $row->id . ')" 
                                class="text-red-600 hover:text-red-800 text-sm" title="Delete">
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
     * Get the next serial number for the current year
     */
    public function getNextSerial(Request $request)
    {
        $currentYear = $request->get('year', date('Y'));
        
        try {
            // Get the highest serial number for the current year
            $lastRecord = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('mlsfNo', 'like', '%-' . $currentYear . '-%')
                ->orderByRaw('CAST(SUBSTRING(mlsfNo, LEN(mlsfNo) - 3, 4) AS INT) DESC')
                ->first();

            $nextSerial = 1;
            
            if ($lastRecord && $lastRecord->mlsfNo) {
                // Extract the serial number from the MLS File number (last 4 digits)
                preg_match('/-(\d{4})(?:\(T\))?(?:\s+AND\s+EXTENSION)?$/', $lastRecord->mlsfNo, $matches);
                if (isset($matches[1])) {
                    $lastSerial = (int) $matches[1];
                    $nextSerial = $lastSerial + 1;
                }
            }

            return response()->json(['nextSerial' => $nextSerial]);

        } catch (\Exception $e) {
            \Log::error('Error getting next serial number: ' . $e->getMessage());
            return response()->json(['nextSerial' => 1]);
        }
    }

    /**
     * Get existing file numbers for extension dropdown
     */
    public function getExistingFileNumbers()
    {
        try {
            $fileNumbers = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->select('mlsfNo')
                ->whereNotNull('mlsfNo')
                ->where('mlsfNo', '!=', '')
                ->orderBy('mlsfNo', 'desc')
                ->limit(100)
                ->get();

            return response()->json($fileNumbers);

        } catch (\Exception $e) {
            \Log::error('Error getting existing file numbers: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Store a new MLS File number
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_name' => 'required|string|max:255',
            'application_type' => 'required|in:new,conversion',
            'land_use' => 'required|string|max:10',
            'year' => 'required|integer|min:2020|max:2050',
            'serial_no' => 'required|integer|min:1',
            'file_option' => 'required|in:normal,temporary,extension',
            'existing_file_no' => 'required_if:file_option,extension'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fileOption = $request->file_option;
            $mlsfNo = '';
            $kangisFileNo = '';
            $newKangisFileNo = '';

            if ($fileOption === 'extension') {
                // For extensions, use the existing file number with "AND EXTENSION"
                $mlsfNo = $request->existing_file_no . ' AND EXTENSION';
                $kangisFileNo = $request->existing_file_no;
                $newKangisFileNo = $mlsfNo;
            } else {
                // Generate new file number
                $serialNo = str_pad($request->serial_no, 4, '0', STR_PAD_LEFT);
                $mlsfNo = $request->land_use . '-' . $request->year . '-' . $serialNo;
                
                if ($fileOption === 'temporary') {
                    $mlsfNo .= '(T)';
                }
                
                $kangisFileNo = $mlsfNo;
                $newKangisFileNo = $mlsfNo;
            }

            // Check if file number already exists (only for non-extension files)
            if ($fileOption !== 'extension') {
                $exists = DB::connection('sqlsrv')
                    ->table('fileNumber')
                    ->where('mlsfNo', $mlsfNo)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File number already exists: ' . $mlsfNo
                    ], 409);
                }
            }

            // Insert new record
            $id = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->insertGetId([
                    'mlsfNo' => $mlsfNo,
                    'kangisFileNo' => $kangisFileNo,
                    'NewKANGISFileNo' => $newKangisFileNo,
                    'FileName' => $request->file_name,
                    'type' => 'Generated',
                    'location' => $request->land_use,
                    'created_by' => Auth::user()->name ?? Auth::user()->email ?? 'System',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'MLS File number generated successfully: ' . $mlsfNo,
                'data' => [
                    'id' => $id,
                    'mlsfNo' => $mlsfNo,
                    'kangisFileNo' => $kangisFileNo,
                    'NewKANGISFileNo' => $newKangisFileNo,
                    'FileName' => $request->file_name
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating MLS File number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating MLS File number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Migrate data from Excel file
     */
    public function migrate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload a valid Excel file (xlsx, xls, or csv)',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            $header = array_shift($rows);
            
            $imported = 0;
            $duplicates = 0;
            $errors = 0;

            foreach ($rows as $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map columns: mlsfNo, kangisFile, NewKANGISFileNo, FileName
                    $mlsfNo = trim($row[0] ?? '');
                    $kangisFileNo = trim($row[1] ?? '');
                    $newKangisFileNo = trim($row[2] ?? '');
                    $fileName = trim($row[3] ?? '');

                    // Skip if essential data is missing
                    if (empty($mlsfNo) && empty($kangisFileNo) && empty($newKangisFileNo)) {
                        continue;
                    }

                    // Check for duplicates
                    $exists = DB::connection('sqlsrv')
                        ->table('fileNumber')
                        ->where(function($query) use ($mlsfNo, $kangisFileNo, $newKangisFileNo) {
                            if (!empty($mlsfNo)) {
                                $query->orWhere('mlsfNo', $mlsfNo);
                            }
                            if (!empty($kangisFileNo)) {
                                $query->orWhere('kangisFileNo', $kangisFileNo);
                            }
                            if (!empty($newKangisFileNo)) {
                                $query->orWhere('NewKANGISFileNo', $newKangisFileNo);
                            }
                        })
                        ->exists();

                    if ($exists) {
                        $duplicates++;
                        continue;
                    }

                    // Insert record
                    DB::connection('sqlsrv')
                        ->table('fileNumber')
                        ->insert([
                            'mlsfNo' => $mlsfNo ?: null,
                            'kangisFileNo' => $kangisFileNo ?: null,
                            'NewKANGISFileNo' => $newKangisFileNo ?: null,
                            'FileName' => $fileName ?: null,
                            'type' => 'Migrated',
                            'created_by' => 'Migrated',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                    $imported++;

                } catch (\Exception $e) {
                    \Log::error('Error importing row: ' . $e->getMessage(), ['row' => $row]);
                    $errors++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Migration completed. Imported: {$imported}, Duplicates skipped: {$duplicates}, Errors: {$errors}",
                'data' => [
                    'imported' => $imported,
                    'duplicates' => $duplicates,
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error during migration: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error during migration: ' . $e->getMessage()
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
                    'updated_by' => Auth::user()->name ?? Auth::user()->email ?? 'System',
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
     * Get total count of file numbers
     */
    public function getCount()
    {
        try {
            $count = DB::connection('sqlsrv')
                ->table('fileNumber')
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