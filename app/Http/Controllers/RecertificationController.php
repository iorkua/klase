<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecertificationController extends Controller
{  
    public function index() {
        $PageTitle = 'Recertification Programme';
        $PageDescription = 'Manage approved certificate recertification and re-issuance applications';
        return view('recertification.index', compact('PageTitle', 'PageDescription'));
    }

    /**
     * Get applications data for DataTables
     */
    public function getApplicationsData(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv')->table('recertification_applications');

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('application_reference', 'like', "%{$searchValue}%")
                      ->orWhere('surname', 'like', "%{$searchValue}%")
                      ->orWhere('first_name', 'like', "%{$searchValue}%")
                      ->orWhere('organisation_name', 'like', "%{$searchValue}%")
                      ->orWhere('plot_number', 'like', "%{$searchValue}%")
                      ->orWhere('cofo_number', 'like', "%{$searchValue}%");
                });
            }

            // Get total count before pagination
            $totalRecords = $query->count();

            // Apply ordering
            if ($request->has('order')) {
                $orderColumn = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];
                
                $columns = ['id', 'application_reference', 'applicant_name', 'plot_details', 'lga_name', 'created_at'];
                if (isset($columns[$orderColumn])) {
                    if ($orderColumn == 2) { // applicant_name
                        $query->orderBy('surname', $orderDir)->orderBy('first_name', $orderDir);
                    } else {
                        $query->orderBy($columns[$orderColumn], $orderDir);
                    }
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Apply pagination
            if ($request->has('start') && $request->has('length')) {
                $query->skip($request->start)->take($request->length);
            }

            $applications = $query->get();

            // Format data for DataTables
            $data = $applications->map(function($app) {
                // Determine applicant name based on type
                $applicantName = '';
                if ($app->applicant_type === 'Corporate') {
                    $applicantName = $app->organisation_name ?? 'N/A';
                } else {
                    $applicantName = trim(($app->surname ?? '') . ' ' . ($app->first_name ?? ''));
                    if (empty($applicantName)) {
                        $applicantName = 'N/A';
                    }
                }

                // Format plot details
                $plotDetails = '';
                if ($app->plot_number) {
                    $plotDetails .= 'Plot: ' . $app->plot_number;
                }
                if ($app->layout_district) {
                    $plotDetails .= ($plotDetails ? ', ' : '') . $app->layout_district;
                }
                if (empty($plotDetails)) {
                    $plotDetails = 'N/A';
                }

                return [
                    'id' => $app->id,
                    'application_reference' => $app->application_reference ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'applicant_type' => $app->applicant_type ?? 'N/A',
                    'plot_details' => $plotDetails,
                    'lga_name' => $app->lga_name ?? 'N/A',
                    'created_at' => $app->created_at ? date('d M Y', strtotime($app->created_at)) : 'N/A',
                    'cofo_number' => $app->cofo_number ?? 'N/A'
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching applications data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to fetch applications data'
            ]);
        }
    }

    /**
     * View application details
     */
    public function view($id)
    {
        try {
            $application = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                return response()->json(['error' => 'Application not found'], 404);
            }

            // Get owners if Multiple Owners type
            $owners = [];
            if ($application->applicant_type === 'Multiple Owners') {
                $owners = DB::connection('sqlsrv')
                    ->table('recertification_owners')
                    ->where('application_id', $id)
                    ->get();
            }

            return response()->json([
                'success' => true,
                'application' => $application,
                'owners' => $owners
            ]);

        } catch (\Exception $e) {
            Log::error('Error viewing application', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to load application'], 500);
        }
    }

    /**
     * Delete application
     */
    public function destroy($id)
    {
        try {
            $application = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                return response()->json(['error' => 'Application not found'], 404);
            }

            // Delete owners first (cascade should handle this, but being explicit)
            DB::connection('sqlsrv')
                ->table('recertification_owners')
                ->where('application_id', $id)
                ->delete();

            // Delete application
            DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting application', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to delete application'], 500);
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $application = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('id', $id)
                ->first();

            if (!$application) {
                abort(404, 'Application not found');
            }

            // Get owners if Multiple Owners type
            $owners = [];
            if ($application->applicant_type === 'Multiple Owners') {
                $owners = DB::connection('sqlsrv')
                    ->table('recertification_owners')
                    ->where('application_id', $id)
                    ->get();
            }

            $PageTitle = 'Edit Recertification Application';
            $PageDescription = 'Update application details';

            return view('recertification.edit', compact('PageTitle', 'PageDescription', 'application', 'owners'));

        } catch (\Exception $e) {
            Log::error('Error loading edit form', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return redirect()->route('recertification.index')
                ->with('error', 'Failed to load application for editing');
        }
    }

    /**
     * Store a newly created recertification application.
     * Persists to SQL Server connection (sqlsrv).
     */
    public function store(Request $request)
    {
        try {
            // Normalize applicant type
            $type = $request->input('applicantType');

            // Base validation
            $rules = [
                'applicationDate' => 'nullable|date',
                'applicantType' => 'required|in:Individual,Corporate,Government Body,Multiple Owners',
            ];

            if ($type === 'Corporate') {
                $rules = array_merge($rules, [
                    'organisationName' => 'required|string|max:255',
                    'cacRegistrationNo' => 'required|string|max:100',
                    'typeOfOrganisation' => 'required|string|max:255',
                    'typeOfBusiness' => 'required|string|max:255',
                ]);
            } elseif ($type === 'Multiple Owners') {
                $rules = array_merge($rules, [
                    'owners' => 'required|array|min:1',
                    'owners.*.surname' => 'required|string|max:255',
                    'owners.*.firstName' => 'required|string|max:255',
                    'owners.*.occupation' => 'required|string|max:255',
                    'owners.*.dateOfBirth' => 'required|date',
                    'owners.*.nationality' => 'required|string|max:255',
                    'owners.*.stateOfOrigin' => 'required|string|max:255',
                    'owners.*.gender' => 'required|string|in:male,female',
                    'owners.*.maritalStatus' => 'required|string|in:single,married,divorced,widowed',
                    'owners.*.passportPhoto' => 'nullable|file|image|max:2048',
                ]);
            } else { // Individual or Government Body
                $rules = array_merge($rules, [
                    'surname' => 'required|string|max:255',
                    'firstName' => 'required|string|max:255',
                    'occupation' => 'required|string|max:255',
                    'dateOfBirth' => 'required|date',
                    'nationality' => 'required|string|max:255',
                    'stateOfOrigin' => 'required|string|max:255',
                    'gender' => 'required|string|in:male,female',
                    'maritalStatus' => 'required|string|in:single,married,divorced,widowed',
                ]);
            }

            $validated = $request->validate($rules);

            // Generate application reference
            $reference = 'RC-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Prepare payload (exclude files)
            $payload = $request->except(['owners', '_token']);

            // Include owners meta (without files) if present
            if ($type === 'Multiple Owners') {
                $ownersMeta = [];
                foreach ((array)$request->input('owners', []) as $idx => $owner) {
                    $ownerCopy = $owner;
                    unset($ownerCopy['passportPhoto']);
                    $ownersMeta[$idx] = $ownerCopy;
                }
                $payload['owners'] = $ownersMeta;
            }

            // Map documents checkboxes (Step 6)
            $documents = $request->input('documents', []);

            // Insert application with all structured fields
            $appId = DB::connection('sqlsrv')->table('recertification_applications')->insertGetId([
                // Meta
                'application_reference' => $reference,
                'application_date' => $request->input('applicationDate'),
                'applicant_type' => $type,
                'organisation_name' => $request->input('organisationName'),
                'cac_registration_no' => $request->input('cacRegistrationNo'),
                'type_of_organisation' => $request->input('typeOfOrganisation'),
                'type_of_business' => $request->input('typeOfBusiness'),

                // Step 1
                'surname' => $request->input('surname'),
                'first_name' => $request->input('firstName'),
                'middle_name' => $request->input('middleName'),
                'title' => $request->input('title'),
                'occupation' => $request->input('occupation'),
                'date_of_birth' => $request->input('dateOfBirth'),
                'nationality' => $request->input('nationality'),
                'state_of_origin' => $request->input('stateOfOrigin'),
                'lga_of_origin' => $request->input('lgaOfOrigin'),
                'nin' => $request->input('nin'),
                'gender' => $request->input('gender'),
                'marital_status' => $request->input('maritalStatus'),
                'maiden_name' => $request->input('maidenName'),

                // Step 2 - Applicant contact
                'phone_no' => $request->input('phoneNo'),
                'whatsapp_phone_no' => $request->input('whatsappPhoneNo'),
                'alternate_phone_no' => $request->input('alternatePhoneNo'),
                'address_line1' => $request->input('addressLine1'),
                'address_line2' => $request->input('addressLine2'),
                'city_town' => $request->input('cityTown'),
                'state_name' => $request->input('state'),
                'email_address' => $request->input('emailAddress'),

                // Step 2 - Representative
                'rep_surname' => $request->input('repSurname'),
                'rep_first_name' => $request->input('repFirstName'),
                'rep_middle_name' => $request->input('repMiddleName'),
                'rep_title' => $request->input('repTitle'),
                'rep_relationship' => $request->input('repRelationship'),
                'rep_phone_no' => $request->input('repPhoneNo'),

                // Step 3 - Title Holder
                'title_holder_surname' => $request->input('titleHolderSurname'),
                'title_holder_first_name' => $request->input('titleHolderFirstName'),
                'title_holder_middle_name' => $request->input('titleHolderMiddleName'),
                'title_holder_title' => $request->input('titleHolderTitle'),
                'cofo_number' => $request->input('cofoNumber'),
                'reg_no' => $request->input('registrationNo'),
                'reg_volume' => $request->input('registrationVolume'),
                'reg_page' => $request->input('registrationPage'),
                'reg_number' => $request->input('registrationNumber'),
                'is_original_owner' => $request->input('isOriginalOwner') === 'yes' ? 1 : ($request->has('isOriginalOwner') ? 0 : null),
                'instrument_type' => $request->input('instrumentType'),
                'acquired_title_holder_name' => $request->input('titleHolderName'),
                'commencement_date' => $request->input('commencementDate'),
                'grant_term' => $request->input('grantTerm'),

                // Step 4 - Mortgage & Encumbrance
                'is_encumbered' => $request->input('isEncumbered') === 'yes' ? 1 : ($request->has('isEncumbered') ? 0 : null),
                'encumbrance_reason' => $request->input('encumbranceReason'),
                'has_mortgage' => $request->input('hasMortgage') === 'yes' ? 1 : ($request->has('hasMortgage') ? 0 : null),
                'mortgagee_name' => $request->input('mortgageeName'),
                'mortgage_registration_no' => $request->input('mortgageRegistrationNo'),
                'mortgage_volume' => $request->input('mortgageVolume'),
                'mortgage_page' => $request->input('mortgagePage'),
                'mortgage_number' => $request->input('mortgageNumber'),
                'mortgage_released' => $request->input('mortgageReleased') === 'yes' ? 1 : ($request->has('mortgageReleased') ? 0 : null),

                // Step 5 - Plot Details
                'plot_number' => $request->input('plotNumber'),
                'file_number' => $request->input('fileNumber'),
                'plot_size' => $request->input('plotSize'),
                'layout_district' => $request->input('layoutDistrict'),
                'lga_name' => $request->input('lga'),
                'current_land_use' => $request->input('currentLandUse'),
                'plot_status' => $request->input('plotStatus'),
                'mode_of_allocation' => $request->input('modeOfAllocation'),
                'start_date' => $request->input('startDate'),
                'expiry_date' => $request->input('expiryDate'),
                'plot_description' => $request->input('plotDescription'),

                // Step 6 - Payment & Terms
                'application_type' => $request->input('applicationType'),
                'application_reason' => $request->input('applicationReason'),
                'other_reason' => $request->input('otherReason'),
                'payment_method' => $request->input('paymentMethod'),
                'receipt_no' => $request->input('receiptNo'),
                'bank_name' => $request->input('bankName'),
                'payment_amount' => $request->input('paymentAmount'),
                'payment_date' => $request->input('paymentDate'),
                'documents_json' => json_encode($documents),
                'agree_terms' => $request->boolean('agreeTerms'),
                'confirm_accuracy' => $request->boolean('confirmAccuracy'),

                // Raw payload
                'payload' => json_encode($payload),

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Handle owners if Multiple Owners
            if ($type === 'Multiple Owners') {
                foreach ((array)$request->input('owners', []) as $idx => $owner) {
                    $photoPath = null;
                    if ($request->hasFile("owners.$idx.passportPhoto")) {
                        $photoPath = $request->file("owners.$idx.passportPhoto")->store('recertification/passports', 'public');
                    }

                    DB::connection('sqlsrv')->table('recertification_owners')->insert([
                        'application_id' => $appId,
                        'surname' => $owner['surname'] ?? null,
                        'first_name' => $owner['firstName'] ?? null,
                        'middle_name' => $owner['middleName'] ?? null,
                        'title' => $owner['title'] ?? null,
                        'occupation' => $owner['occupation'] ?? null,
                        'date_of_birth' => $owner['dateOfBirth'] ?? null,
                        'nationality' => $owner['nationality'] ?? null,
                        'state_of_origin' => $owner['stateOfOrigin'] ?? null,
                        'lga_of_origin' => $owner['lgaOfOrigin'] ?? null,
                        'nin' => $owner['nin'] ?? null,
                        'gender' => $owner['gender'] ?? null,
                        'marital_status' => $owner['maritalStatus'] ?? null,
                        'maiden_name' => $owner['maidenName'] ?? null,
                        'passport_photo_path' => $photoPath,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'id' => $appId,
                'reference' => $reference,
                'message' => 'Recertification application stored successfully.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Recertification store error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store application',
                'error' => app()->environment('local') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    //new-application
    
    //   public function index() {
    //     $PageTitle = 'Recertification Programme';
    //     $PageDescription = ' ';
    //     return view('recertification.index', compact('PageTitle', 'PageDescription'));
    // }


    /**
     * Get the next file number for new applications
     */
    public function getNextFileNumber()
    {
        try {
            $lastFileNumber = DB::connection('sqlsrv')
                ->table('recertification_applications')
                ->where('file_number', 'like', 'KN%')
                ->orderBy('file_number', 'desc')
                ->value('file_number');
            
            if ($lastFileNumber) {
                // Extract the numeric part and increment
                $lastNumber = intval(substr($lastFileNumber, 2));
                $newNumber = $lastNumber + 1;
            } else {
                // Start from KN3000 if no previous records
                $newNumber = 3000;
            }
            
            $nextFileNumber = 'KN' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            
            return response()->json([
                'success' => true,
                'file_number' => $nextFileNumber
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting next file number', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'file_number' => 'KN3000' // Default fallback
            ]);
        }
    }
}
