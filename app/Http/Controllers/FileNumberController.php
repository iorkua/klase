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
                // Temporarily remove is_deleted filter to see all records

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
                    'mlsfNo',
                    'NewKANGISFileNo', 
                    'FileName',
                    'type',
                    'created_by',
                    'created_at'
                ])
                // Temporarily remove is_deleted filter to see all records
                ->when(!empty($searchValue), function($query) use ($searchValue) {
                    $query->where(function($q) use ($searchValue) {
                        $q->where('kangisFileNo', 'like', "%{$searchValue}%")
                          ->orWhere('NewKANGISFileNo', 'like', "%{$searchValue}%")
                          ->orWhere('FileName', 'like', "%{$searchValue}%")
                          ->orWhere('mlsfNo', 'like', "%{$searchValue}%");
                    });
                })
                ->orderBy('id', 'desc') // Order by ID since created_at might be null
                ->skip($start)
                ->take($length)
                ->get();

            // Format the data
            $formattedData = $data->map(function($row) {
                // Clean and format the data
                $kangisFileNo = trim($row->kangisFileNo ?? '');
                $newKangisFileNo = trim($row->NewKANGISFileNo ?? '');
                $fileName = trim($row->FileName ?? '');
                $mlsfNo = trim($row->mlsfNo ?? '');
                $createdBy = trim($row->created_by ?? '');
                
                // Display each field as-is, or show N/A if empty
                return [
                    'id' => $row->id,
                    'mlsfNo' => !empty($mlsfNo) ? $mlsfNo : 'N/A',
                    'kangisFileNo' => !empty($kangisFileNo) ? $kangisFileNo : 'N/A',
                    'NewKANGISFileNo' => !empty($newKangisFileNo) ? $newKangisFileNo : 'N/A',
                    'FileName' => !empty($fileName) ? $fileName : 'N/A',
                    'type' => trim($row->type ?? '') ?: 'N/A',
                    'created_by' => !empty($createdBy) ? $createdBy : 'System',
                    'created_at' => $row->created_at ? date('Y-m-d H:i:s', strtotime($row->created_at)) : 'N/A',
                    'action' => '<div class="flex justify-center space-x-2">
                        <button onclick="editRecord(' . $row->id . ')" 
                                class="text-blue-600 hover:text-blue-800 text-sm px-2 py-1 rounded hover:bg-blue-50" title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <button onclick="deleteRecord(' . $row->id . ')" 
                                class="text-red-600 hover:text-red-800 text-sm px-2 py-1 rounded hover:bg-red-50" title="Delete">
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
            // Get all records for the current year and extract serial numbers
            $records = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('mlsfNo', 'like', '%-' . $currentYear . '-%')
                ->whereNotNull('mlsfNo')
                ->where('mlsfNo', '!=', '')
                ->get();

            $maxSerial = 0;
            
            foreach ($records as $record) {
                if ($record->mlsfNo) {
                    // Extract serial number from patterns like: RES-2024-0001, CON-IND-42154, etc.
                    // Look for the last number in the string that could be a serial
                    if (preg_match('/-(\d+)(?:\(T\))?(?:\s+AND\s+EXTENSION)?$/', $record->mlsfNo, $matches)) {
                        $serial = (int) $matches[1];
                        if ($serial > $maxSerial) {
                            $maxSerial = $serial;
                        }
                    }
                }
            }
            
            $nextSerial = $maxSerial + 1;

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

            if ($fileOption === 'extension') {
                // For extensions, use the existing file number with "AND EXTENSION"
                $mlsfNo = $request->existing_file_no . ' AND EXTENSION';
            } else {
                // Generate new file number
                $serialNo = str_pad($request->serial_no, 4, '0', STR_PAD_LEFT);
                $mlsfNo = $request->land_use . '-' . $request->year . '-' . $serialNo;
                
                if ($fileOption === 'temporary') {
                    $mlsfNo .= '(T)';
                }
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

            // Insert new record - only populate mlsfNo field, leave others null
            $id = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->insertGetId([
                    'mlsfNo' => $mlsfNo,
                    'kangisFileNo' => null,  // Leave empty
                    'NewKANGISFileNo' => null,  // Leave empty
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
                    'kangisFileNo' => null,
                    'NewKANGISFileNo' => null,
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
     * Migrate data from CSV file (simple and efficient)
     */
    public function migrate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:csv,txt|max:20480' // Only CSV files, 20MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload a valid CSV file. Max size: 20MB',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Increase memory limit and execution time for large files
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', 300); // 5 minutes

            $file = $request->file('excel_file');
            $filePath = $file->getPathname();
            
            \Log::info("CSV Migration started for file: " . $file->getClientOriginalName());
            
            $imported = 0;
            $duplicates = 0;
            $errors = 0;
            $batchSize = 100;
            $batch = [];
            $rowNumber = 0;
            
            // Get existing records to check for duplicates
            $existingMlsfNos = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->whereNotNull('mlsfNo')
                ->where('mlsfNo', '!=', '')
                ->pluck('mlsfNo')
                ->toArray();
            
            $existingKangisNos = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->whereNotNull('kangisFileNo')
                ->where('kangisFileNo', '!=', '')
                ->pluck('kangisFileNo')
                ->toArray();
            
            $existingNewKangisNos = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->whereNotNull('NewKANGISFileNo')
                ->where('NewKANGISFileNo', '!=', '')
                ->pluck('NewKANGISFileNo')
                ->toArray();

            // Open and read CSV file
            if (($handle = fopen($filePath, 'r')) !== FALSE) {
                
                // Read header row to understand column structure
                $header = fgetcsv($handle, 1000, ',');
                
                if (!$header) {
                    throw new \Exception('Could not read CSV header row');
                }
                
                \Log::info("CSV Header: " . implode(', ', $header));
                
                // Find column indexes (case insensitive)
                $mlsfNoIndex = -1;
                $kangisFileIndex = -1;
                $newKangisFileNoIndex = -1;
                $fileNameIndex = -1;
                
                foreach ($header as $index => $column) {
                    $column = strtolower(trim($column));
                    if (in_array($column, ['mlsfno', 'mls_file_no', 'mlsfileno'])) {
                        $mlsfNoIndex = $index;
                    } elseif (in_array($column, ['kangisfile', 'kangis_file', 'kangisfileno'])) {
                        $kangisFileIndex = $index;
                    } elseif (in_array($column, ['newkangisfileno', 'new_kangis_file_no', 'newkangisfile'])) {
                        $newKangisFileNoIndex = $index;
                    } elseif (in_array($column, ['filename', 'file_name', 'name'])) {
                        $fileNameIndex = $index;
                    }
                }
                
                \Log::info("Column mapping - mlsfNo: {$mlsfNoIndex}, kangisFile: {$kangisFileIndex}, newKangisFileNo: {$newKangisFileNoIndex}, fileName: {$fileNameIndex}");
                
                // Process each data row
                while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $rowNumber++;
                    
                    try {
                        // Skip empty rows
                        if (empty(array_filter($row))) {
                            continue;
                        }
                        
                        // Extract data based on column indexes
                        $mlsfNo = trim($row[$mlsfNoIndex] ?? '');
                        $kangisFileNo = trim($row[$kangisFileIndex] ?? '');
                        $newKangisFileNo = trim($row[$newKangisFileNoIndex] ?? '');
                        $fileName = trim($row[$fileNameIndex] ?? '');
                        
                        // Skip if all essential data is missing
                        if (empty($mlsfNo) && empty($kangisFileNo) && empty($newKangisFileNo)) {
                            continue;
                        }
                        
                        // Check for duplicates
                        $isDuplicate = false;
                        if (!empty($mlsfNo) && in_array($mlsfNo, $existingMlsfNos)) {
                            $isDuplicate = true;
                        } elseif (!empty($kangisFileNo) && in_array($kangisFileNo, $existingKangisNos)) {
                            $isDuplicate = true;
                        } elseif (!empty($newKangisFileNo) && in_array($newKangisFileNo, $existingNewKangisNos)) {
                            $isDuplicate = true;
                        }
                        
                        if ($isDuplicate) {
                            $duplicates++;
                            continue;
                        }
                        
                        // Add to batch
                        $batch[] = [
                            'mlsfNo' => !empty($mlsfNo) ? $mlsfNo : null,
                            'kangisFileNo' => !empty($kangisFileNo) ? $kangisFileNo : null,
                            'NewKANGISFileNo' => !empty($newKangisFileNo) ? $newKangisFileNo : null,
                            'FileName' => !empty($fileName) ? $fileName : null,
                            'type' => 'Migrated',
                            'created_by' => 'Migrated',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        
                        // Update existing arrays to prevent duplicates within the same import
                        if (!empty($mlsfNo)) $existingMlsfNos[] = $mlsfNo;
                        if (!empty($kangisFileNo)) $existingKangisNos[] = $kangisFileNo;
                        if (!empty($newKangisFileNo)) $existingNewKangisNos[] = $newKangisFileNo;
                        
                        // Insert batch when it reaches the batch size
                        if (count($batch) >= $batchSize) {
                            DB::connection('sqlsrv')->table('fileNumber')->insert($batch);
                            $imported += count($batch);
                            $batch = [];
                            
                            // Log progress every 1000 records
                            if ($imported % 1000 == 0) {
                                \Log::info("Migration progress: {$imported} records imported");
                            }
                        }
                        
                    } catch (\Exception $e) {
                        \Log::error("Error importing CSV row {$rowNumber}: " . $e->getMessage());
                        $errors++;
                    }
                }
                
                // Insert remaining batch
                if (!empty($batch)) {
                    DB::connection('sqlsrv')->table('fileNumber')->insert($batch);
                    $imported += count($batch);
                }
                
                fclose($handle);
                
            } else {
                throw new \Exception('Could not open CSV file for reading');
            }
            
            // Clean up memory
            unset($existingMlsfNos, $existingKangisNos, $existingNewKangisNos, $batch);
            
            \Log::info("CSV Migration completed: {$imported} imported, {$duplicates} duplicates, {$errors} errors");
            
            return response()->json([
                'success' => true,
                'message' => "CSV migration completed successfully! Imported: {$imported}, Duplicates skipped: {$duplicates}, Errors: {$errors}",
                'data' => [
                    'imported' => $imported,
                    'duplicates' => $duplicates,
                    'errors' => $errors,
                    'total_processed' => $imported + $duplicates + $errors,
                    'rows_processed' => $rowNumber
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error during CSV migration: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error during CSV migration: ' . $e->getMessage()
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
     * Update a record (only file name can be updated)
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'file_name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

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

            // Update only the file name
            DB::connection('sqlsrv')
                ->table('fileNumber')
                ->where('id', $id)
                ->update([
                    'FileName' => $request->file_name,
                    'updated_by' => Auth::user()->name ?? Auth::user()->email ?? 'System',
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'File name updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a record (hard delete since we're not using soft delete filtering)
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

    /**
     * Test database connection and table structure
     */
    public function testDatabase()
    {
        try {
            // Test connection
            $connectionTest = DB::connection('sqlsrv')->getPdo();
            
            // Test table existence
            $tableExists = DB::connection('sqlsrv')
                ->select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'fileNumber'");
            
            // Get table structure
            $columns = DB::connection('sqlsrv')
                ->select("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'fileNumber'");
            
            // Get record count
            $recordCount = 0;
            $sampleRecords = [];
            
            if ($tableExists[0]->count > 0) {
                $recordCount = DB::connection('sqlsrv')->table('fileNumber')->count();
                $sampleRecords = DB::connection('sqlsrv')
                    ->table('fileNumber')
                    ->limit(5)
                    ->get()
                    ->toArray();
            }
            
            return response()->json([
                'success' => true,
                'connection' => 'Connected successfully',
                'table_exists' => $tableExists[0]->count > 0,
                'columns' => $columns,
                'record_count' => $recordCount,
                'sample_records' => $sampleRecords,
                'database_name' => DB::connection('sqlsrv')->getDatabaseName(),
                'server_info' => DB::connection('sqlsrv')->select('SELECT @@VERSION as version')[0]->version ?? 'Unknown'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}