<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstrumentController extends Controller
{
    public function index()
    {
        $PageTitle = 'Instrument Capture';
        $PageDescription = '';
        
        $instruments = DB::connection('sqlsrv')->table('instrument_registration')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('instruments.index', compact('PageTitle', 'PageDescription', 'instruments'));
    }

    public function create()
    {
      $PageTitle = 'Instrument Capture';
      $PageDescription = 'Capture a new instrument ';

        return view('instruments.create', compact('PageTitle', 'PageDescription'));
    }
 
    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'instrument_type' => 'required|string',
                'Grantor' => 'required|string',
                'GrantorAddress' => 'required|string',
                'Grantee' => 'required|string',
                'GranteeAddress' => 'required|string',
                'instrumentDate' => 'required|date',
                'propertyDescription' => 'nullable|string',
                'solicitorName' => 'nullable|string',
                'solicitorAddress' => 'nullable|string',
            ]);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            
            // Format date for SQL Server
            $instrumentDate = $request->instrumentDate ? date('Y-m-d', strtotime($request->instrumentDate)) : null;
            
            // Create instrument record using DB facade
            $now = now()->format('Y-m-d H:i:s');
            
            $data = [
                'instrument_type' => $request->instrument_type,
                'MLSFileNo' => $request->mlsFNo,
                'KAGISFileNO' => $request->kangisFileNo,
                'NewKANGISFileNo' => $request->NewKANGISFileno,
                'Grantor' => $request->Grantor,
                'GrantorAddress' => $request->GrantorAddress,
                'Grantee' => $request->Grantee,
                'GranteeAddress' => $request->GranteeAddress,
                'instrumentDate' => $instrumentDate,
                'propertyDescription' => $request->propertyDescription,
                'solicitorName' => $request->solicitorName,
                'solicitorAddress' => $request->solicitorAddress,
                'lga' => $request->lga,
                'district' => $request->district,
                'size' => $request->size,
                'plotNumber' => $request->plotNumber,
                'rootRegistrationNumber' => $request->rootRegistrationNumber,
                'particularsRegistrationNumber' => $request->particularsRegistrationNumber,
                'duration' => $request->duration,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            // Check if table exists
            $tableExists = DB::connection('sqlsrv')->select("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'instrument_registration'");
            
            if (empty($tableExists)) {
                return redirect()->back()->with('error', 'Database table not found. Please run migrations.')->withInput();
            }
            
            try {
                // Begin transaction to ensure data integrity
                DB::connection('sqlsrv')->beginTransaction();
                
                // Use the direct insert method with explicit commit
                $success = DB::connection('sqlsrv')->table('instrument_registration')->insert($data);
                
                if ($success) {
                    // Get the last inserted ID
                    $idResult = DB::connection('sqlsrv')->select('SELECT SCOPE_IDENTITY() as id');
                    
                    if (!empty($idResult)) {
                        $instrument_id = $idResult[0]->id;
                        
                        // Parse the registration number
                        $regParts = explode('/', $request->rootRegistrationNumber);
                        $serial_no = $regParts[0] ?? 0;
                        $page_no = $regParts[1] ?? 0;
                        $volume_no = $regParts[2] ?? 0;
                        
                        // Log the generated number with the instrument ID
                        DB::connection('sqlsrv')->table('instrument_particulars_log')->insert([
                            'instrument_id' => $instrument_id,
                            'serial_no' => $serial_no,
                            'page_no' => $page_no,
                            'volume_no' => $volume_no,
                            'generated_root_particulars_number' => $request->rootRegistrationNumber,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                        
                        // Explicitly commit the transaction
                        DB::connection('sqlsrv')->commit();
                        
                        // Redirect to index with success message
                        return redirect()->route('instruments.index')->with('success', 'Instrument registered successfully');
                    } else {
                        throw new \Exception('Failed to retrieve inserted record ID');
                    }
                } else {
                    throw new \Exception('Failed to insert record');
                }
            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::connection('sqlsrv')->rollBack();
                
                return redirect()->back()->with('error', 'Database error occurred: ' . $e->getMessage())->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function generateParticulars()
    {
        try {
            // Get the last record from the log table to determine the next sequence
            $lastRecord = DB::connection('sqlsrv')
                ->table('instrument_particulars_log')
                ->orderBy('id', 'desc')
                ->first();
            
            if (!$lastRecord) {
                // Initialize with starting values if no records exist
                $serial_no = 1;
                $page_no = 1;
                $volume_no = 1;
            } else {
                // Calculate the next values based on the last record
                $serial_no = $lastRecord->serial_no + 1;
                $page_no = $lastRecord->page_no + 1;
                $volume_no = $lastRecord->volume_no;
                
                // If we've reached the maximum, reset and increment volume
                if ($serial_no > 300) {
                    $serial_no = 1;
                    $page_no = 1;
                    $volume_no++;
                }
            }
            
            // Format the particulars registration number
            $formatted = "{$serial_no}/{$page_no}/{$volume_no}";
            
            // We no longer insert into the log here - we'll do that on form submission
            
            return response()->json([
                'success' => true,
                'rootRegistrationNumber' => $formatted,
                'serial_no' => $serial_no,
                'page_no' => $page_no,
                'volume_no' => $volume_no
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate particulars registration number: ' . $e->getMessage()
            ], 500);
        }
    }

    public function Coroi()
    {
        $title = 'Confirmation Of Instrument Registration';
        return view('coroi.index', compact('title'));
    }

    public function update(Request $request, $id)
    {
        // Here you would implement the update logic using DB::connection('sqlsrv')
        return redirect('/instruments')->with('success', 'Instrument updated successfully');
    }

    public function destroy($id)
    {
        // Here you would implement the delete logic using DB::connection('sqlsrv')
        return redirect('/instruments')->with('success', 'Instrument deleted successfully');
    }
}
