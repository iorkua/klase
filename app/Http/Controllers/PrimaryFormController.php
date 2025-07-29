<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Helpers\SectionalTitleHelper;
use App\Models\FileIndexing;

class PrimaryFormController extends Controller
{
    public function index()
    {
        $PageTitle = 'Application for Sectional Titling ';
        $PageDescription = 'Main Application';

        // Generate NP FileNo for the main application
        $landUse = request()->query('landuse', 'Residential');
        
        // Determine the land use code
        $landUseCode = match(strtoupper($landUse)) {
            'COMMERCIAL' => 'COM',
            'INDUSTRIAL' => 'IND', 
            'RESIDENTIAL' => 'RES',
            default => 'RES'
        };
        
        // Get the current year
        $currentYear = date('Y');
        
        // Get the next serial number based on existing applications
        $lastApplication = DB::connection('sqlsrv')
            ->table('mother_applications')
            ->whereYear('created_at', $currentYear)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextSerialNo = 1;
        if ($lastApplication) {
            $nextSerialNo = $lastApplication->id + 1;
        } else {
            // If no applications exist, get the next ID from the table
            $nextSerialNo = DB::connection('sqlsrv')->table('mother_applications')->count() + 1;
        }
        
        $serialNo = str_pad($nextSerialNo, 2, '0', STR_PAD_LEFT);
        
        // Generate NP FileNo (New Primary FileNo)
        $npFileNo = "ST-{$landUseCode}-{$currentYear}-{$serialNo}";

        return view('primaryform.index', compact(
            'PageTitle', 
            'PageDescription',
            'npFileNo',
            'landUse',
            'currentYear',
            'serialNo'
        )); 
    }
    
    public function store(Request $request)
    {
        try {
            $rules = [
                'applicantType' => 'required',
                'applicant_title' => 'nullable',
                'first_name' => 'nullable',
                'middle_name' => 'nullable',
                'surname' => 'nullable',
                'corporate_name' => 'nullable',
                'rc_number' => 'nullable',
                'multiple_owners_names' => 'nullable|array',
                'multiple_owners_address' => 'nullable|array',
                'multiple_owners_passport' => 'nullable|array',
                'multiple_owners_passport.*' => 'nullable|image|max:5120',
                'multiple_owners_email' => 'nullable|array',
                'multiple_owners_email.*' => 'nullable|email',
                'multiple_owners_phone' => 'nullable|array',
                'multiple_owners_phone.*' => 'nullable|string',
                'multiple_owners_identification_type' => 'nullable|array',
                'multiple_owners_identification_type.*' => 'nullable|string',
                'multiple_owners_identification_image' => 'nullable|array',
                'multiple_owners_identification_image.*' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'address_house_no' => 'nullable',
                'owner_street_name' => 'nullable',
                'owner_district' => 'nullable',
                'owner_lga' => 'nullable',
                'owner_state' => 'nullable',
                'phone_number' => 'nullable',
                'owner_email' => 'nullable|email',
                'idType' => 'nullable',
                'id_document' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'residenceType' => 'nullable',
                'units_count' => 'nullable',
                'blocks_count' => 'nullable',
                'sections_count' => 'nullable',
                'plot_size' => 'nullable|string|max:255',
                'application_fee' => 'nullable',
                'processing_fee' => 'nullable',
                'site_plan_fee' => 'nullable',
                'payment_date' => 'nullable',
                'receipt_number' => 'nullable',
                'comments' => 'nullable',
                'commercial_type' => 'nullable',
                'passportInput' => 'nullable',
                'application_letter' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'building_plan' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'architectural_design' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'ownership_document' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'shared_areas' => 'nullable|array',
                'shared_areas.*' => 'nullable|string',
                'other_areas_detail' => 'nullable|string|max:500',
            ];

            // Conditional validation
            if ($request->input('applicantType') === 'multiple') {
                $rules['multiple_owners_names'] = 'required|array|min:1';
                $rules['multiple_owners_names.*'] = 'required|string';
                $rules['multiple_owners_address'] = 'required|array|min:1';
                $rules['multiple_owners_address.*'] = 'required|string';
                $rules['multiple_owners_email'] = 'required|array|min:1';
                $rules['multiple_owners_email.*'] = 'required|email';
                $rules['multiple_owners_phone'] = 'required|array|min:1';
                $rules['multiple_owners_phone.*'] = 'required|string';
                $rules['multiple_owners_identification_type'] = 'required|array|min:1';
                $rules['multiple_owners_identification_type.*'] = 'required|string';
                $rules['multiple_owners_identification_image'] = 'required|array|min:1';
                $rules['multiple_owners_identification_image.*'] = 'required|file|max:5120|mimes:pdf,jpg,jpeg,png';

                // Main owner fields must be nullable (not required)
                $rules['address_house_no'] = 'nullable';
                $rules['owner_street_name'] = 'nullable';
                $rules['owner_district'] = 'nullable';
                $rules['owner_lga'] = 'nullable';
                $rules['owner_state'] = 'nullable';
                $rules['phone_number'] = 'nullable';
                $rules['owner_email'] = 'nullable|email';
                $rules['idType'] = 'nullable';
                $rules['id_document'] = 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png';
            } else {
                // Main owner required fields
                $rules['address_house_no'] = 'required';
                $rules['owner_street_name'] = 'required';
                $rules['owner_district'] = 'required';
                $rules['owner_lga'] = 'required';
                $rules['owner_state'] = 'required';
                $rules['phone_number'] = 'required';
                $rules['owner_email'] = 'required|email';
                $rules['idType'] = 'required';
                $rules['id_document'] = 'required|file|max:5120|mimes:pdf,jpg,jpeg,png';
            }

            $validated = $request->validate($rules);

            // Debug log to check what's being received
            Log::info('Form data received', [
                'owner_fullname' => $request->input('fullname'),
                'all_data' => $request->all()
            ]);

            // Process the file number based on active tab
            $fileNo = null;
            $mlsFileNo = null;
            $kangisFileNo = null;
            $newKangisFileNo = null;
            
            if ($request->filled('mlsPreviewFileNumber')) {
                $fileNo = $request->input('mlsPreviewFileNumber');
                $mlsFileNo = $request->input('mlsPreviewFileNumber');
            } elseif ($request->filled('kangisPreviewFileNumber')) {
                $fileNo = $request->input('kangisPreviewFileNumber');
                $kangisFileNo = $request->input('kangisPreviewFileNumber');
            } elseif ($request->filled('newKangisPreviewFileNumber')) {
                $fileNo = $request->input('newKangisPreviewFileNumber');
                $newKangisFileNo = $request->input('newKangisPreviewFileNumber');
            }

            // Handle passport upload
            $passportPath = null;
            if ($request->hasFile('passport')) {
                $passport = $request->file('passport');
                $passportPath = $passport->store('passports', 'public');
            }

            // Handle ID document upload
            $idDocumentPath = null;
            if ($request->hasFile('id_document')) {
                $idDocument = $request->file('id_document');
                $originalName = $idDocument->getClientOriginalName();
                $extension = $idDocument->getClientOriginalExtension();
                $idDocumentPath = $idDocument->store('id_documents', 'public');
                
                Log::info('ID Document uploaded', [
                    'path' => $idDocumentPath,
                    'original_name' => $originalName,
                    'type' => $extension
                ]);
            }

            // Handle multiple owners passports upload
            $multipleOwnersPassportPaths = [];
            if ($request->hasFile('multiple_owners_passport')) {
                foreach ($request->file('multiple_owners_passport') as $passport) {
                    if ($passport && $passport->isValid()) {
                        $path = $passport->store('multiple_owners_passports', 'public');
                        $multipleOwnersPassportPaths[] = $path;
                    } else {
                        $multipleOwnersPassportPaths[] = null;
                    }
                }
            }

            // Handle multiple owners identification images upload
            $multipleOwnersIdImagePaths = [];
            if ($request->hasFile('multiple_owners_identification_image')) {
                foreach ($request->file('multiple_owners_identification_image') as $idimg) {
                    if ($idimg && $idimg->isValid()) {
                        $path = $idimg->store('multiple_owners_id_images', 'public');
                        $multipleOwnersIdImagePaths[] = $path;
                    } else {
                        $multipleOwnersIdImagePaths[] = null;
                    }
                }
            }

            // Process document uploads - using direct file access
            $documents = [];
            $documentTypes = ['application_letter', 'building_plan', 'architectural_design', 'ownership_document'];
            
            foreach ($documentTypes as $docType) {
                if ($request->hasFile($docType)) {
                    $file = $request->file($docType);
                    if ($file && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $path = $file->store('documents', 'public');
                        
                        // Add detailed info about each document
                        $documents[$docType] = [
                            'path' => $path,
                            'original_name' => $originalName,
                            'type' => $extension,
                            'uploaded_at' => now()->toDateTimeString()
                        ];
                        
                        Log::info('Document uploaded', [
                            'docType' => $docType,
                            'path' => $path,
                            'original_name' => $originalName
                        ]);
                    }
                }
            }

            // Process shared areas
            $sharedAreas = null;
            if ($request->has('shared_areas') && is_array($request->input('shared_areas'))) {
                $sharedAreas = $request->input('shared_areas');
                
                // If "other" is selected and other_areas_detail is provided, add it to the array
                if (in_array('other', $sharedAreas) && $request->filled('other_areas_detail')) {
                    $sharedAreas['other_details'] = $request->input('other_areas_detail');
                }
                
                // Convert to JSON for storage
                $sharedAreas = json_encode($sharedAreas);
            }

            // Format phone numbers
            $phoneNumber = null;
            if ($request->has('phone_number') && is_array($request->input('phone_number'))) {
                $phoneNumber = implode(', ', array_filter($request->input('phone_number')));
            } elseif ($request->has('phone_number')) {
                $phoneNumber = $request->input('phone_number');
            }

            // Generate NP FileNo for this application
            $landUse = $request->input('land_use', 'Residential');
            $landUseCode = match(strtoupper($landUse)) {
                'COMMERCIAL' => 'COM',
                'INDUSTRIAL' => 'IND', 
                'RESIDENTIAL' => 'RES',
                default => 'RES'
            };
            
            $currentYear = date('Y');
            
            // Insert basic data first to get the application ID
            $tempData = [
                'applicant_type' => $request->input('applicantType'),
                'land_use' => $request->input('land_use'),
                'application_status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Get the application ID first
            $applicationId = DB::connection('sqlsrv')->table('mother_applications')->insertGetId($tempData);
            
            // Now generate the NP FileNo using the actual application ID
            $serialNo = str_pad($applicationId, 2, '0', STR_PAD_LEFT);
            $npFileNo = "ST-{$landUseCode}-{$currentYear}-{$serialNo}";

            // Create complete data array for update
            $data = [
                'applicant_type' => $request->input('applicantType'),
                'applicant_title' => $request->input('applicant_title'),
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'surname' => $request->input('surname'),
                'corporate_name' => $request->input('corporate_name'),
                'rc_number' => $request->input('rc_number'),
                'multiple_owners_names' => $request->has('multiple_owners_names') ? json_encode($request->input('multiple_owners_names')) : null,
                'multiple_owners_address' => $request->has('multiple_owners_address') ? json_encode($request->input('multiple_owners_address')) : null,
                'multiple_owners_passport' => !empty($multipleOwnersPassportPaths) ? json_encode($multipleOwnersPassportPaths) : null,
                'multiple_owners_email' => $request->has('multiple_owners_email') ? json_encode($request->input('multiple_owners_email')) : null,
                'multiple_owners_phone' => $request->has('multiple_owners_phone') ? json_encode($request->input('multiple_owners_phone')) : null,
                'multiple_owners_identification_type' => $request->has('multiple_owners_identification_type') ? json_encode($request->input('multiple_owners_identification_type')) : null,
                'multiple_owners_identification_image' => !empty($multipleOwnersIdImagePaths) ? json_encode($multipleOwnersIdImagePaths) : null,
                'passport' => $passportPath,
                'id_document' => $idDocumentPath,
                'fileno' => $fileNo,
                'np_fileno' => $npFileNo, // Add NP FileNo
                'address' =>$request->input('address'),
                'address_house_no' => $request->input('address_house_no'),
                'address_street_name' => $request->input('owner_street_name'),
                'address_district' => $request->input('owner_district'),
                'address_lga' => $request->input('owner_lga'),
                'address_state' => $request->input('owner_state'),
                'phone_number' => $phoneNumber,
                'email' => $request->input('owner_email'),
                'identification_type' => $request->input('idType'),
               
                'property_plot_no' => $request->input('property_plot_no'),
                'property_street_name' => $request->input('property_street_name'),
                'property_district' => $request->input('property_district'),
                'property_lga' => $request->input('property_lga'),
                'property_state' => $request->input('property_state'),
                'plot_size' => $request->input('plot_size'),
                'NoOfUnits' => $request->input('units_count'),
                'application_fee' => $request->input('application_fee'),
                'processing_fee' => $request->input('processing_fee'),
                'site_plan_fee' => $request->input('site_plan_fee'),
                'payment_date' => $request->input('payment_date'),
                'receipt_number' => $request->input('receipt_number'),
                'comments' => $request->input('comments'),
                'commercial_type' => $request->input('commercial_type'),
                'residential_type' => $request->input('residenceType'),
                'industrial_type' => $request->input('industrial_type'),
                'land_use' => $request->input('land_use'),
                'application_status' => 'Pending',
                'applicationID' => date('Y').'-'.str_pad($applicationId, 2, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
                // Store documents as a proper JSON string
                'documents' => !empty($documents) ? json_encode($documents) : null,
                'shared_areas' => $sharedAreas,
            ];

            // Log the data being inserted
            Log::info('Data being inserted into DB', [
                'documents' => $data['documents'],
                'document_count' => count($documents),
                'np_fileno' => $npFileNo
            ]);

            // Update the application with complete data
            DB::connection('sqlsrv')->table('mother_applications')->where('id', $applicationId)->update($data);

            // Get authenticated user information
            $createdBy = Auth::user() ? Auth::user()->first_name : null;
            
            // Use helper class to generate and insert file numbers
            $fileData = [
                'mlsFileNo' => $mlsFileNo,
                'kangisFileNo' => $kangisFileNo,
                'newKangisFileNo' => $newKangisFileNo
            ];
            
            $sectionalTitleFileNo = SectionalTitleHelper::generateAndInsertFileNumber(
                $applicationId, 
                $createdBy, 
                $fileData
            );
            
            // Insert additional data into eRegistry table after successful application creation
            try {
                $eRegistryUpdateData = [
                    'application_id' => $applicationId,
                    'sub_application_id' => null, // This is for main application only
                    'MLSFileNo' => $mlsFileNo,
                    'KANGISFileNo' => $kangisFileNo,
                    'NEWKangisFileNo' => $newKangisFileNo,
                    'npFileno' => $npFileNo,
                    'ST_fileNO' => null, // For fileno unit application only
                    'Commissioning_Date' => now(), // Current date
                    'Current_Office' => 'ST Registry',
                    'updated_at' => now()
                ];
                
                // Update the existing eRegistry record with additional fields
                DB::connection('sqlsrv')->table('eRegistry')
                    ->where('application_id', $applicationId)
                    ->update($eRegistryUpdateData);
                    
                Log::info('eRegistry updated with additional fields for primary application', [
                    'application_id' => $applicationId,
                    'npFileno' => $npFileNo
                ]);
            } catch (Exception $eRegistryError) {
                Log::error('Error updating eRegistry with additional fields', [
                    'application_id' => $applicationId,
                    'error' => $eRegistryError->getMessage()
                ]);
            }
            
            // Process records/buyers if they exist in the request
            if ($request->has('records') && is_array($request->input('records'))) {
                SectionalTitleHelper::insertBuyers($applicationId, $request->input('records'));
            }
            
            // Insert billing record for primary application
            $billingData = [
                'Sectional_Title_File_No' => $sectionalTitleFileNo,
                'ref_id' => $data['applicationID'],
                'application_id' => $applicationId,
                'sub_application_id' => null,
                'Scheme_Application_Fee' => $request->input('application_fee'),
                'Site_Plan_Fee' => $request->input('site_plan_fee'),
                'Processing_Fee' => $request->input('processing_fee'),
                'survey_fee' => null,
                'Betterment_Charges' => null,
                'Unit_Application_Fees' => null,
                'Land_Use_Charge' => null,
                'property_value' => null,
                'Penalty_Fees' => null,
                'Payment_Status' => 'Paid',
                'created_at' => now(),
                'updated_at' => now(),
                'betterment_rate' => null
            ];

            // Insert billing record
            DB::connection('sqlsrv')->table('billing')->insert($billingData);

            // Log successful submission
            Log::info('Application submitted successfully', [
                'application_id' => $applicationId,
                'sectional_title_file_no' => $sectionalTitleFileNo,
                'billing_inserted' => true
            ]);

            // Auto-create file indexing record for EDMS workflow
            $this->createFileIndexingRecord($applicationId, $data);

            // Return response with success message and redirect to EDMS workflow
            return redirect()->route('edms.index', $applicationId)
                ->with('success', 'Application submitted successfully! Please proceed with the EDMS workflow.')
                ->with('application_id', $applicationId);
        } catch (Exception $e) {
            // Enhanced error logging for debugging
            Log::error('Error submitting application form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'form_data' => $request->all()
            ]);

            // Return with error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error submitting application: ' . $e->getMessage());
        }
    }

    /**
     * Create file indexing record for EDMS workflow
     */
    private function createFileIndexingRecord($applicationId, $applicationData)
    {
        try {
            // Verify the application exists before creating file indexing
            $applicationExists = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->where('id', $applicationId)
                ->exists();
                
            if (!$applicationExists) {
                Log::warning('Cannot create file indexing - application does not exist', [
                    'application_id' => $applicationId
                ]);
                return null;
            }

            // Check if file indexing already exists
            $existingFileIndexing = FileIndexing::on('sqlsrv')
                ->where('main_application_id', $applicationId)
                ->first();
                
            if ($existingFileIndexing) {
                Log::info('File indexing already exists for application', [
                    'application_id' => $applicationId,
                    'file_indexing_id' => $existingFileIndexing->id
                ]);
                return $existingFileIndexing;
            }

            // Generate file title from application data
            $name = '';
            if ($applicationData['applicant_type'] === 'individual') {
                $name = trim(($applicationData['first_name'] ?? '') . ' ' . ($applicationData['middle_name'] ?? '') . ' ' . ($applicationData['surname'] ?? ''));
            } elseif ($applicationData['applicant_type'] === 'corporate') {
                $name = $applicationData['corporate_name'] ?? '';
            } elseif ($applicationData['applicant_type'] === 'multiple') {
                $names = json_decode($applicationData['multiple_owners_names'] ?? '[]', true);
                if (is_array($names) && count($names) > 0) {
                    $name = $names[0] . ' et al.';
                }
            }
            
            $landUse = $applicationData['land_use'] ?? 'Property';
            $fileTitle = $name ? "{$name}'s {$landUse}" : "Application {$applicationId}";

            // Create file indexing record with proper database connection
            $fileIndexing = FileIndexing::on('sqlsrv')->create([
                'main_application_id' => $applicationId,
                'file_number' => $applicationData['fileno'] ?? $applicationData['np_fileno'] ?? 'APP-' . $applicationId,
                'file_title' => $fileTitle,
                'land_use_type' => $applicationData['land_use'] ?? 'Residential',
                'plot_number' => $applicationData['property_plot_no'] ?? null,
                'district' => $applicationData['property_district'] ?? null,
                'lga' => $applicationData['property_lga'] ?? null,
                'has_cofo' => false,
                'is_merged' => false,
                'has_transaction' => false,
                'is_problematic' => false,
            ]);

            Log::info('File indexing auto-created', [
                'application_id' => $applicationId,
                'file_indexing_id' => $fileIndexing->id,
                'file_title' => $fileTitle
            ]);

            return $fileIndexing;

        } catch (Exception $e) {
            Log::error('Error creating file indexing record', [
                'application_id' => $applicationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't throw exception to avoid breaking the main flow
            return null;
        }
    }
}
