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
        $PageDescription = ' ';
        return view('recertification.index', compact('PageTitle', 'PageDescription'));
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

}



