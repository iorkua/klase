<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\Log;

class SecondaryFormController extends Controller
{
    public function edit($id)
    {
        try {
            // Get the sub-application data
            $application = DB::connection('sqlsrv')
                ->table('subapplications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                return redirect()->route('sectionaltitling.index')
                    ->with('error', 'Sub-application not found.');
            }

            // Get the mother application data for context
            $motherApplication = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->where('id', $application->main_application_id)
                ->first();

            // Decode JSON fields
            if ($application->multiple_owners_names) {
                $application->multiple_owners_names_array = json_decode($application->multiple_owners_names, true);
            }
            if ($application->multiple_owners_address) {
                $application->multiple_owners_address_array = json_decode($application->multiple_owners_address, true);
            }
            if ($application->multiple_owners_email) {
                $application->multiple_owners_email_array = json_decode($application->multiple_owners_email, true);
            }
            if ($application->multiple_owners_phone) {
                $application->multiple_owners_phone_array = json_decode($application->multiple_owners_phone, true);
            }
            if ($application->multiple_owners_passport) {
                $application->multiple_owners_passport_array = json_decode($application->multiple_owners_passport, true);
            }
            if ($application->multiple_owners_identification_type) {
                $application->multiple_owners_identification_type_array = json_decode($application->multiple_owners_identification_type, true);
            }
            if ($application->multiple_owners_identification_image) {
                $application->multiple_owners_identification_image_array = json_decode($application->multiple_owners_identification_image, true);
            }
            if ($application->shared_areas) {
                $application->shared_areas_array = json_decode($application->shared_areas, true);
            }
            if ($application->documents) {
                $application->documents_array = json_decode($application->documents, true);
            }

            // Convert phone numbers back to array if stored as comma-separated string
            if ($application->phone_number && strpos($application->phone_number, ',') !== false) {
                $application->phone_number_array = explode(', ', $application->phone_number);
            } else {
                $application->phone_number_array = [$application->phone_number];
            }

            $PageTitle = 'Edit Unit Application';
            $PageDescription = 'Edit the unit application details';

            return view('sectionaltitling.edit_sub', compact('application', 'motherApplication', 'PageTitle', 'PageDescription'));

        } catch (Exception $e) {
            Log::error('Error loading sub-application for edit', [
                'error' => $e->getMessage(),
                'application_id' => $id
            ]);

            return redirect()->route('sectionaltitling.index')
                ->with('error', 'Error loading application for edit: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the form data
            $validated = $request->validate([
                'applicantType' => 'required',
                'applicant_title' => 'nullable',
                'first_name' => 'nullable',
                'middle_name' => 'nullable',
                'surname' => 'nullable',
                'corporate_name' => 'nullable',
                'rc_number' => 'nullable',
                'multiple_owners_names' => 'nullable|array',
                'multiple_owners_address' => 'nullable|array',
                'multiple_owners_email' => 'nullable|array',
                'multiple_owners_email.*' => 'nullable|email',
                'multiple_owners_phone' => 'nullable|array',
                'multiple_owners_passport' => 'nullable|array',
                'multiple_owners_passport.*' => 'nullable|image|max:5120',
                'multiple_owners_identification_image' => 'nullable|array',
                'multiple_owners_identification_image.*' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'address' => 'nullable',
                'address_street_name' => 'nullable',
                'address_district' => 'nullable',
                'address_lga' => 'nullable',
                'address_state' => 'nullable',
                'phone_number' => 'nullable',
                'owner_email' => 'nullable|email',
                'identification_type' => 'nullable',
                'id_document' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'residence_type' => 'nullable',
                'block_number' => 'nullable',
                'floor_number' => 'nullable',
                'unit_number' => 'nullable',
                'unit_size' => 'nullable|string|max:255',
                'application_fee' => 'nullable',
                'processing_fee' => 'nullable',
                'site_plan_fee' => 'nullable',
                'payment_date' => 'nullable',
                'receipt_number' => 'nullable',
                'application_comment' => 'nullable',
                'commercial_type' => 'nullable',
                'industrial_type' => 'nullable',
                'ownership_type' => 'nullable',
                'ownershipType' => 'nullable',
                'otherOwnership' => 'nullable',
                'shared_areas' => 'nullable|array',
                'scheme_no' => 'nullable',
                'application_letter' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'building_plan' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'architectural_design' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'ownership_document' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
            ]);

            // Get existing application data
            $existingApplication = DB::connection('sqlsrv')
                ->table('subapplications')
                ->where('id', $id)
                ->first();

            if (!$existingApplication) {
                return redirect()->route('sectionaltitling.index')
                    ->with('error', 'Sub-application not found.');
            }

            // Handle passport upload (keep existing if no new file)
            $passportPath = $existingApplication->passport;
            if ($request->hasFile('passport')) {
                // Delete old passport if exists
                if ($passportPath) {
                    Storage::disk('public')->delete($passportPath);
                }
                $passport = $request->file('passport');
                $passportPath = $passport->store('passports', 'public');
            }

            // Handle main identification image upload
            $identificationImagePath = $existingApplication->id_document;
            if ($request->hasFile('id_document')) {
                // Delete old identification image if exists
                if ($identificationImagePath) {
                    Storage::disk('public')->delete($identificationImagePath);
                }
                $identificationImage = $request->file('id_document');
                $identificationImagePath = $identificationImage->store('identification_images', 'public');
            }

            // Handle multiple owners passports upload
            $multipleOwnersPassportPaths = $existingApplication->multiple_owners_passport ? 
                json_decode($existingApplication->multiple_owners_passport, true) : [];
            
            if ($request->hasFile('multiple_owners_passport')) {
                // Delete old passports
                foreach ($multipleOwnersPassportPaths as $oldPath) {
                    if ($oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                
                $multipleOwnersPassportPaths = [];
                foreach ($request->file('multiple_owners_passport') as $passport) {
                    if ($passport && $passport->isValid()) {
                        $path = $passport->store('multiple_owners_passports', 'public');
                        $multipleOwnersPassportPaths[] = $path;
                    }
                }
            }

            // Handle multiple owners identification images upload
            $multipleOwnersIdentificationPaths = $existingApplication->multiple_owners_identification_image ? 
                json_decode($existingApplication->multiple_owners_identification_image, true) : [];
            
            if ($request->hasFile('multiple_owners_identification_image')) {
                // Delete old identification images
                foreach ($multipleOwnersIdentificationPaths as $oldPath) {
                    if ($oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                
                $multipleOwnersIdentificationPaths = [];
                foreach ($request->file('multiple_owners_identification_image') as $identificationImage) {
                    if ($identificationImage && $identificationImage->isValid()) {
                        $path = $identificationImage->store('multiple_owners_identification', 'public');
                        $multipleOwnersIdentificationPaths[] = $path;
                    }
                }
            }

            // Process multiple owners identification types
            $multipleOwnersIdentificationTypes = [];
            $allInputs = $request->all();
            foreach ($allInputs as $key => $value) {
                if (strpos($key, 'multiple_owners_identification_type_') === 0) {
                    $multipleOwnersIdentificationTypes[] = $value;
                }
            }

            // Process document uploads
            $documents = $existingApplication->documents ? json_decode($existingApplication->documents, true) : [];
            $documentTypes = ['application_letter', 'building_plan', 'architectural_design', 'ownership_document'];
            
            foreach ($documentTypes as $docType) {
                if ($request->hasFile($docType)) {
                    // Delete old document if exists
                    if (isset($documents[$docType]['path'])) {
                        Storage::disk('public')->delete($documents[$docType]['path']);
                    }
                    
                    $file = $request->file($docType);
                    if ($file && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $path = $file->store('documents', 'public');
                        
                        $documents[$docType] = [
                            'path' => $path,
                            'original_name' => $originalName,
                            'type' => $extension,
                            'uploaded_at' => now()->toDateTimeString()
                        ];
                    }
                }
            }

            // Format phone numbers - handle both string and array input
            $phoneNumber = null;
            if ($request->has('phone_number')) {
                $phoneInput = $request->input('phone_number');
                if (is_array($phoneInput)) {
                    $phoneNumber = implode(', ', array_filter($phoneInput));
                } else {
                    $phoneNumber = $phoneInput;
                }
            }
            
            // Process shared areas
            $sharedAreas = null;
            if ($request->has('shared_areas') && is_array($request->input('shared_areas'))) {
                $sharedAreas = json_encode($request->input('shared_areas'));
            }
            
            // Process ownership type
            $ownershipType = $request->input('ownershipType');
            if ($ownershipType === 'others' && $request->filled('otherOwnership')) {
                $ownershipType = $request->input('otherOwnership');
            }
            
            // Get the mother application to build main_id
            $motherApplication = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->where('id', $existingApplication->main_application_id)
                ->first();

            $mainId = null;
            if ($motherApplication) {
                $mainYear = date('Y', strtotime($motherApplication->created_at ?? now()));
                $mainAppId = $motherApplication->id ?? '';
                $mainId = sprintf('ST-%s-%03d', $mainYear, $mainAppId);
            }

            $updateData = [
                'applicant_type' => $request->input('applicantType'),
                'applicant_title' => $request->input('applicant_title'),
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'surname' => $request->input('surname'),
                'passport' => $passportPath,
                'corporate_name' => $request->input('corporate_name'),
                'rc_number' => $request->input('rc_number'),
                'multiple_owners_names' => $request->has('multiple_owners_names') ? json_encode($request->input('multiple_owners_names')) : null,
                'multiple_owners_address' => $request->has('multiple_owners_address') ? json_encode($request->input('multiple_owners_address')) : null,
                'multiple_owners_email' => $request->has('multiple_owners_email') ? json_encode($request->input('multiple_owners_email')) : null,
                'multiple_owners_phone' => $request->has('multiple_owners_phone') ? json_encode($request->input('multiple_owners_phone')) : null,
                'multiple_owners_passport' => !empty($multipleOwnersPassportPaths) ? json_encode($multipleOwnersPassportPaths) : null,
                'multiple_owners_identification_type' => !empty($multipleOwnersIdentificationTypes) ? json_encode($multipleOwnersIdentificationTypes) : null,
                'multiple_owners_identification_image' => !empty($multipleOwnersIdentificationPaths) ? json_encode($multipleOwnersIdentificationPaths) : null,
                'address' => $request->input('address'),
                'phone_number' => $phoneNumber,
                'email' => $request->input('owner_email'),
                'identification_type' => $request->input('identification_type'),
                'id_document' => $identificationImagePath,
                'block_number' => $request->input('block_number'),
                'floor_number' => $request->input('floor_number'),
                'unit_number' => $request->input('unit_number'),
                'unit_size' => $request->input('unit_size'),
                'main_id' => $mainId, // always set from server-side
                'application_fee' => $request->input('application_fee'),
                'processing_fee' => $request->input('processing_fee'),
                'site_plan_fee' => $request->input('site_plan_fee'),
                'payment_date' => $request->input('payment_date'),
                'receipt_number' => $request->input('receipt_number'),
                'commercial_type' => $request->input('commercial_type'),
                'industrial_type' => $request->input('industrial_type'),
                'residence_type' => $request->input('residence_type'),
                'ownership_type' => $ownershipType,
                'address_street_name' => $request->input('address_street_name'),
                'address_district' => $request->input('address_district'),
                'address_lga' => $request->input('address_lga'),
                'address_state' => $request->input('address_state'),
                'scheme_no' => $request->input('scheme_no'),
                'shared_areas' => $sharedAreas,
                'documents' => !empty($documents) ? json_encode($documents) : null,
                'application_comment' => $request->input('application_comment'),
                'updated_at' => now(),
                'updated_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            ];

            // Update the sub-application
            DB::connection('sqlsrv')
                ->table('subapplications')
                ->where('id', $id)
                ->update($updateData);

            Log::info('Sub-application updated successfully', [
                'application_id' => $id,
                'updated_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name
            ]);

            return redirect()->route('sectionaltitling.viewrecorddetail_sub', $id)
                ->with('success', 'Unit application updated successfully!');

        } catch (Exception $e) {
            Log::error('Error updating sub-application', [
                'error' => $e->getMessage(),
                'application_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating application: ' . $e->getMessage());
        }
    }

    public function save(Request $request)
    {
        try {
            // Validate the form data - making some fields optional to improve form submission success
            $validated = $request->validate([
                'applicantType' => 'required',
                'applicant_title' => 'nullable',
                'first_name' => 'nullable',
                'middle_name' => 'nullable',
                'surname' => 'nullable',
                'corporate_name' => 'nullable',
                'rc_number' => 'nullable',
                'multiple_owners_names' => 'nullable|array',
                'multiple_owners_address' => 'nullable|array',
                'multiple_owners_email' => 'nullable|array',
                'multiple_owners_email.*' => 'nullable|email',
                'multiple_owners_phone' => 'nullable|array',
                'multiple_owners_passport' => 'nullable|array',
                'multiple_owners_passport.*' => 'nullable|image|max:5120',
                'multiple_owners_identification_image' => 'nullable|array',
                'multiple_owners_identification_image.*' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'address_house_no' => 'nullable',
                'address_street_name' => 'nullable',
                'address_district' => 'nullable',
                'address_lga' => 'nullable',
                'address_state' => 'nullable',
                'phone_number' => 'nullable|array',
                'owner_email' => 'nullable|email',
                'identification_type' => 'nullable',
                'id_document' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'residence_type' => 'nullable',
                'block_number' => 'nullable',
                'floor_number' => 'nullable',
                'unit_number' => 'nullable',
                'unit_size' => 'nullable|string|max:255',
                'application_fee' => 'nullable',
                'processing_fee' => 'nullable',
                'site_plan_fee' => 'nullable',
                'payment_date' => 'nullable',
                'receipt_number' => 'nullable',
                'application_comment' => 'nullable',
                'commercial_type' => 'nullable',
                'industrial_type' => 'nullable',
                'ownership_type' => 'nullable',
                'ownershipType' => 'nullable',
                'otherOwnership' => 'nullable',
                'shared_areas' => 'nullable|array',
                'main_application_id' => 'required',
                'main_id' => 'nullable|string',
                'scheme_no' => 'nullable',
                'prefix' => 'required',
                'year' => 'required',
                'serial_number' => 'required',
                'fileno' => 'required',
                'application_letter' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'building_plan' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'architectural_design' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
                'ownership_document' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
            ]);

            // Debug log to check what's being received
            Log::info('Form data received', [
                'owner_fullname' => $request->input('fullname'),
                'multiple_owners_data' => [
                    'names' => $request->input('multiple_owners_names'),
                    'addresses' => $request->input('multiple_owners_address'),
                    'emails' => $request->input('multiple_owners_email'),
                    'phones' => $request->input('multiple_owners_phone'),
                    'identification_types' => $request->all()
                ],
                'all_data' => $request->all()
            ]);

            // Handle passport upload
            $passportPath = null;
            if ($request->hasFile('passport')) {
                $passport = $request->file('passport');
                $passportPath = $passport->store('passports', 'public');
            }

            // Handle main identification image upload
            $identificationImagePath = null;
            if ($request->hasFile('id_document')) {
                $identificationImage = $request->file('id_document');
                $identificationImagePath = $identificationImage->store('identification_images', 'public');
            }

            // Handle multiple owners passports upload
            $multipleOwnersPassportPaths = [];
            if ($request->hasFile('multiple_owners_passport')) {
                foreach ($request->file('multiple_owners_passport') as $passport) {
                    if ($passport && $passport->isValid()) {
                        $path = $passport->store('multiple_owners_passports', 'public');
                        $multipleOwnersPassportPaths[] = $path;
                    }
                }
            }

            // Handle multiple owners identification images upload
            $multipleOwnersIdentificationPaths = [];
            if ($request->hasFile('multiple_owners_identification_image')) {
                foreach ($request->file('multiple_owners_identification_image') as $identificationImage) {
                    if ($identificationImage && $identificationImage->isValid()) {
                        $path = $identificationImage->store('multiple_owners_identification', 'public');
                        $multipleOwnersIdentificationPaths[] = $path;
                    }
                }
            }

            // Process multiple owners identification types
            $multipleOwnersIdentificationTypes = [];
            $allInputs = $request->all();
            foreach ($allInputs as $key => $value) {
                if (strpos($key, 'multiple_owners_identification_type_') === 0) {
                    $multipleOwnersIdentificationTypes[] = $value;
                }
            }

            // Process document uploads
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

            // Format phone numbers
            $phoneNumber = null;
            if ($request->has('phone_number') && is_array($request->input('phone_number'))) {
                $phoneNumber = implode(', ', array_filter($request->input('phone_number')));
            } elseif ($request->has('phone_number')) {
                $phoneNumber = $request->input('phone_number');
            }
            
            // Process shared areas
            $sharedAreas = null;
            if ($request->has('shared_areas') && is_array($request->input('shared_areas'))) {
                $sharedAreas = json_encode($request->input('shared_areas'));
            }
            
            // Process ownership type
            $ownershipType = $request->input('ownershipType');
            if ($ownershipType === 'others' && $request->filled('otherOwnership')) {
                $ownershipType = $request->input('otherOwnership');
            }
            
            // Get the mother application to build main_id
            $motherApplication = DB::connection('sqlsrv')->table('mother_applications')
                ->where('id', $request->input('main_application_id'))
                ->first();

            $mainId = null;
            if ($motherApplication) {
                $mainYear = date('Y', strtotime($motherApplication->created_at ?? now()));
                $mainAppId = $motherApplication->id ?? '';
                $mainId = sprintf('ST-%s-%03d', $mainYear, $mainAppId);
                
                // Debug log to check main_id generation
                Log::info('Main ID generation', [
                    'mother_application_id' => $request->input('main_application_id'),
                    'mother_application_found' => $motherApplication ? 'yes' : 'no',
                    'main_year' => $mainYear,
                    'main_app_id' => $mainAppId,
                    'generated_main_id' => $mainId
                ]);
            } else {
                Log::warning('Mother application not found for main_application_id: ' . $request->input('main_application_id'));
            }
            
            // Create data array for subapplications table
            $subApplicationData = [
                'main_application_id' => $request->input('main_application_id'),
                'applicant_type' => $request->input('applicantType'),
                'fileno' => $request->input('fileno'), // Unit File Number
                'np_fileno' => $motherApplication->np_fileno ?? null, // NP FileNo from mother application
                'applicant_title' => $request->input('applicant_title'),
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'surname' => $request->input('surname'),
                'passport' => $passportPath,
                'corporate_name' => $request->input('corporate_name'),
                'rc_number' => $request->input('rc_number'),
                'multiple_owners_names' => $request->has('multiple_owners_names') ? json_encode($request->input('multiple_owners_names')) : null,
                'multiple_owners_address' => $request->has('multiple_owners_address') ? json_encode($request->input('multiple_owners_address')) : null,
                'multiple_owners_email' => $request->has('multiple_owners_email') ? json_encode($request->input('multiple_owners_email')) : null,
                'multiple_owners_phone' => $request->has('multiple_owners_phone') ? json_encode($request->input('multiple_owners_phone')) : null,
                'multiple_owners_passport' => !empty($multipleOwnersPassportPaths) ? json_encode($multipleOwnersPassportPaths) : null,
                'multiple_owners_identification_type' => !empty($multipleOwnersIdentificationTypes) ? json_encode($multipleOwnersIdentificationTypes) : null,
                'multiple_owners_identification_image' => !empty($multipleOwnersIdentificationPaths) ? json_encode($multipleOwnersIdentificationPaths) : null,
                'address' => $request->input('address'),
                'phone_number' => $phoneNumber,
                'email' => $request->input('owner_email'),
                'identification_type' => $request->input('identification_type'),
                'id_document' => $identificationImagePath,
                'block_number' => $request->input('block_number'),
                'floor_number' => $request->input('floor_number'),
                'unit_number' => $request->input('unit_number'),
                'unit_size' => $request->input('unit_size'),
                'main_id' => $mainId, // always set from server-side
                'application_status' => 'Pending',
                'planning_recommendation_status' => 'Pending',
                'application_fee' => $request->input('application_fee'),
                'processing_fee' => $request->input('processing_fee'),
                'site_plan_fee' => $request->input('site_plan_fee'),
                'payment_date' => $request->input('payment_date'),
                'receipt_number' => $request->input('receipt_number'),
                'commercial_type' => $request->input('commercial_type'),
                'industrial_type' => $request->input('industrial_type'),
                'residence_type' => $request->input('residence_type'),
                'ownership_type' => $ownershipType,
                'address_street_name' => $request->input('address_street_name'),
                'address_district' => $request->input('address_district'),
                'address_lga' => $request->input('address_lga'),
                'address_state' => $request->input('address_state'),
                'scheme_no' => $request->input('scheme_no'),
                'shared_areas' => $sharedAreas,
                'documents' => !empty($documents) ? json_encode($documents) : null,
                'application_comment' => $request->input('application_comment'),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            // Get the land use from the mother application (reuse the already fetched data)
            if ($motherApplication) {
                $subApplicationData['land_use'] = $motherApplication->land_use;
            }

            // Log the data being inserted into subapplications
            Log::info('Data being inserted into subapplications table', [
                'main_id' => $subApplicationData['main_id'],
                'main_application_id' => $subApplicationData['main_application_id'],
                'fileno' => $subApplicationData['fileno'],
                'documents' => $subApplicationData['documents'],
                'document_count' => count($documents),
                'multiple_owners_data' => [
                    'names' => $subApplicationData['multiple_owners_names'],
                    'addresses' => $subApplicationData['multiple_owners_address'],
                    'emails' => $subApplicationData['multiple_owners_email'],
                    'phones' => $subApplicationData['multiple_owners_phone'],
                    'identification_types' => $subApplicationData['multiple_owners_identification_type'],
                    'identification_images' => $subApplicationData['multiple_owners_identification_image']
                ]
            ]);

            // Log the exact SQL that would be executed
            Log::info('About to insert subapplication data', [
                'data_keys' => array_keys($subApplicationData),
                'main_id_value' => $subApplicationData['main_id'],
                'main_id_type' => gettype($subApplicationData['main_id']),
                'main_id_length' => strlen($subApplicationData['main_id'] ?? ''),
            ]);

            // Insert data into the subapplications table
            $subApplicationId = DB::connection('sqlsrv')->table('subapplications')->insertGetId($subApplicationData);
            
            // Verify the main_id was saved correctly
            $savedRecord = DB::connection('sqlsrv')->table('subapplications')->where('id', $subApplicationId)->first();
            Log::info('Verification after insert', [
                'subapplication_id' => $subApplicationId,
                'saved_main_id' => $savedRecord->main_id ?? 'NULL',
                'saved_st_fillno' => $savedRecord->st_fillno ?? 'NULL',
                'saved_fileno' => $savedRecord->fileno ?? 'NULL',
                'expected_main_id' => $mainId,
                'all_saved_fields' => array_keys((array)$savedRecord)
            ]);
            
            // If main_id is still null, try to update it directly
            if (empty($savedRecord->main_id) && !empty($mainId)) {
                Log::warning('Main ID was not saved during insert, attempting direct update');
                $updateResult = DB::connection('sqlsrv')
                    ->table('subapplications')
                    ->where('id', $subApplicationId)
                    ->update(['main_id' => $mainId]);
                    
                Log::info('Direct update result', [
                    'update_result' => $updateResult,
                    'main_id_to_update' => $mainId
                ]);
                
                // Verify the update worked
                $updatedRecord = DB::connection('sqlsrv')->table('subapplications')->where('id', $subApplicationId)->first();
                Log::info('After direct update verification', [
                    'updated_main_id' => $updatedRecord->main_id ?? 'NULL'
                ]);
            }
            

            // Add Duplicate Check for StFileNo table
            // Ensure that the file number is unique before inserting
            
            // Now insert into StFileNo table
            $stFileNoData = [
                'file_prefix' => $request->input('prefix'),
                'year' => $request->input('year'),
                'serial_number' => $request->input('serial_number'),
                'fileno' => $request->input('fileno'),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name
            ];

            // Insert data into the StFileNo table
            DB::connection('sqlsrv')->table('StFileNo')->insert($stFileNoData);

            // Generate a unique Sectional_Title_File_No using SQL to avoid duplicates
            $currentYear = date('Y');
            $lastNumberQuery = DB::connection('sqlsrv')
                ->table('eRegistry')
                ->where('Sectional_Title_File_No', 'like', "ST/{$currentYear}/%")
                ->orderByRaw("CAST(SUBSTRING(Sectional_Title_File_No, 9, 3) AS INT) DESC")
                ->value(DB::raw("SUBSTRING(Sectional_Title_File_No, 9, 3)"));
            
            $nextNumber = $lastNumberQuery ? (int)$lastNumberQuery + 1 : 1;
            $sectionalTitleFileNo = 'ST/' . $currentYear . '/' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Get the file number from the request
            $fileNo = $request->input('fileno');
            
            // Get authenticated user information
            $createdBy = Auth::user() ? Auth::user()->first_name : null;
            
            // Create eRegistry data array with required fields
            $eRegistryData = [
                'application_id' => $motherApplication->id ?? null, // Main application ID
                'sub_application_id' => $subApplicationId, // Unit application ID
                'MLSFileNo' => $motherApplication->fileno ?? null, // From mother application if MLS
                'KANGISFileNo' => $motherApplication->fileno ?? null, // From mother application if KANGIS
                'NEWKangisFileNo' => $motherApplication->fileno ?? null, // From mother application if New KANGIS
                'npFileno' => $motherApplication->np_fileno ?? null, // NP FileNo from mother application
                'ST_fileNO' => $request->input('fileno'), // Unit application file number
                'Sectional_Title_File_No' => $sectionalTitleFileNo,
                'Commissioning_Date' => now(), // Current date
                'Decommissioning_Date' => now(),
                'Site_Plan_Approval' => null,
                'Survey_Plan_Approval' => null,
                'Expected_Return_Date' => null,
                'Current_Office' => 'ST Registry', // Set to ST Registry
                'created_by' => $createdBy,
                'modify_by' => null,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Insert data into eRegistry table
            DB::connection('sqlsrv')->table('eRegistry')->insert($eRegistryData);
            
            Log::info('eRegistry data inserted for unit application', [
                'sub_application_id' => $subApplicationId,
                'application_id' => $motherApplication->id ?? null,
                'ST_fileNO' => $request->input('fileno'),
                'npFileno' => $motherApplication->np_fileno ?? null
            ]);

            // Insert billing record for unit application
            $billingData = [
                'Sectional_Title_File_No' => $sectionalTitleFileNo,
                'ref_id' => $request->input('fileno'),
                'application_id' => null,
                'sub_application_id' => $subApplicationId,
                'Scheme_Application_Fee' => $request->input('application_fee'),
                'Site_Plan_Fee' => null,
                'Processing_Fee' => $request->input('processing_fee'),
                'survey_fee' => $request->input('site_plan_fee'), // For unit, site_plan_fee maps to survey_fee
                'Betterment_Charges' => null,
                'Unit_Application_Fees' => $request->input('application_fee'),
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

            // Auto-create EDMS file indexing (always enabled)
            try {
                // Create file indexing record for sub-application
                $fileIndexing = \App\Models\FileIndexing::on('sqlsrv')->create([
                    'subapplication_id' => $subApplicationId,
                    'main_application_id' => $request->input('main_application_id'),
                    'file_number' => $request->input('fileno'), // Use the Unit File Number
                    'file_title' => $this->generateSubApplicationFileTitle($subApplicationData, $motherApplication),
                    'land_use_type' => $motherApplication->land_use ?? 'Residential',
                    'plot_number' => $motherApplication->property_plot_no,
                    'district' => $motherApplication->property_district,
                    'lga' => $motherApplication->property_lga,
                    'has_cofo' => false,
                    'is_merged' => false,
                    'has_transaction' => false,
                    'is_problematic' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                Log::info('EDMS file indexing created for sub-application', [
                    'subapplication_id' => $subApplicationId,
                    'file_indexing_id' => $fileIndexing->id
                ]);
            } catch (Exception $edmsError) {
                Log::warning('Failed to create EDMS file indexing', [
                    'subapplication_id' => $subApplicationId,
                    'error' => $edmsError->getMessage()
                ]);
            }

            // Always redirect to EDMS workflow after successful submission
            return redirect()->route('sectionaltitling.units', $subApplicationId)
                ->with('success', 'Unit application submitted successfully! EDMS workflow has been initialized.');
                
        } catch (Exception $e) {
            // Enhanced error logging for debugging
            Log::error('Error submitting sub-application form', [
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
     * Generate file title for sub-application
     */
    private function generateSubApplicationFileTitle($subApplicationData, $motherApplication)
    {
        $name = '';
        
        if ($subApplicationData['applicant_type'] === 'individual') {
            $name = trim(($subApplicationData['first_name'] ?? '') . ' ' . ($subApplicationData['middle_name'] ?? '') . ' ' . ($subApplicationData['surname'] ?? ''));
        } elseif ($subApplicationData['applicant_type'] === 'corporate') {
            $name = $subApplicationData['corporate_name'] ?? 'Corporate Applicant';
        } elseif ($subApplicationData['applicant_type'] === 'multiple') {
            $names = json_decode($subApplicationData['multiple_owners_names'] ?? '[]', true);
            if (is_array($names) && count($names) > 0) {
                $name = $names[0] . ' et al.';
            }
        }
        
        $unitInfo = '';
        if ($subApplicationData['unit_number']) {
            $unitInfo = "Unit {$subApplicationData['unit_number']}";
            if ($subApplicationData['block_number']) {
                $unitInfo .= ", Block {$subApplicationData['block_number']}";
            }
        }
        
        $landUse = $motherApplication->land_use ?? 'Property';
        
        if ($name && $unitInfo) {
            return "{$name}'s {$landUse} - {$unitInfo}";
        } elseif ($name) {
            return "{$name}'s Unit Application";
        } elseif ($unitInfo) {
            return "{$landUse} - {$unitInfo}";
        } else {
            return "Unit Application";
        }
    }
}