<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LegalSearchController extends Controller
{
    public function index()
    { 
        $PageTitle = 'Legal Search - Official (for filing purpose)';
        $PageDescription = '';
        return view('legal_search.index', compact('PageTitle', 'PageDescription'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query || trim($query) === '') {
            return response()->json([
                'property_records' => [],
                'mother_applications' => [],
                'subapplications' => [],
                'registered_instruments' => [],
                'cofo' => [],
            ]);
        }

        $fileNo = trim($query);
        
        // Require at least 5 characters for search
        if (strlen($fileNo) < 5) {
            return response()->json([
                'property_records' => [],
                'mother_applications' => [],
                'subapplications' => [],
                'registered_instruments' => [],
                'cofo' => [],
                'error' => 'Search query must be at least 5 characters long.',
                'message' => 'Please enter at least 5 characters to search.'
            ]);
        }

        $isFileNumber = $this->isFileNumberPattern($fileNo);

        // property_records table
        $property_records = DB::connection('sqlsrv')->table('property_records')
            ->where(function($q) use ($query, $fileNo, $isFileNumber) {
                if ($isFileNumber) {
                    // For file number searches, be very restrictive - only search file number fields
                    $q->where(function($qq) use ($fileNo) {
                        $qq->whereRaw("UPPER(mlsFNo) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(kangisFileNo) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(NewKANGISFileno) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(mlsFNo) LIKE UPPER(?)", ["{$fileNo}%"])
                           ->orWhereRaw("UPPER(kangisFileNo) LIKE UPPER(?)", ["{$fileNo}%"])
                           ->orWhereRaw("UPPER(NewKANGISFileno) LIKE UPPER(?)", ["{$fileNo}%"]);
                    });
                } else {
                    // For other searches, use broader matching
                    $q->where('mlsFNo', 'like', "%{$query}%")
                      ->orWhere('kangisFileNo', 'like', "%{$query}%")
                      ->orWhere('NewKANGISFileno', 'like', "%{$query}%")
                      ->orWhere('title_type', 'like', "%{$query}%")
                      ->orWhere('transaction_type', 'like', "%{$query}%")
                      ->orWhere('transaction_date', 'like', "%{$query}%")
                      ->orWhere('serialNo', 'like', "%{$query}%")
                      ->orWhere('pageNo', 'like', "%{$query}%")
                      ->orWhere('volumeNo', 'like', "%{$query}%")
                      ->orWhere('regNo', 'like', "%{$query}%")
                      ->orWhere('instrument_type', 'like', "%{$query}%")
                      ->orWhere('period', 'like', "%{$query}%")
                      ->orWhere('period_unit', 'like', "%{$query}%")
                      ->orWhere('Assignor', 'like', "%{$query}%")
                      ->orWhere('Assignee', 'like', "%{$query}%")
                      ->orWhere('Mortgagor', 'like', "%{$query}%")
                      ->orWhere('Mortgagee', 'like', "%{$query}%")
                      ->orWhere('Surrenderor', 'like', "%{$query}%")
                      ->orWhere('Surrenderee', 'like', "%{$query}%")
                      ->orWhere('Lessor', 'like', "%{$query}%")
                      ->orWhere('Lessee', 'like', "%{$query}%")
                      ->orWhere('Grantor', 'like', "%{$query}%")
                      ->orWhere('Grantee', 'like', "%{$query}%")
                      ->orWhere('property_description', 'like', "%{$query}%")
                      ->orWhere('location', 'like', "%{$query}%")
                      ->orWhere('plot_no', 'like', "%{$query}%")
                      ->orWhere('lgsaOrCity', 'like', "%{$query}%")
                      ->orWhere('layout', 'like', "%{$query}%")
                      ->orWhere('schedule', 'like', "%{$query}%")
                      ->orWhere('created_by', 'like', "%{$query}%")
                      ->orWhere('updated_by', 'like', "%{$query}%");
                }
            })
            ->get();

        $mother_applications = DB::connection('sqlsrv')->table('mother_applications')
            ->leftJoin('registered_instruments', function($join) {
                $join->on('mother_applications.fileno', '=', 'registered_instruments.MLSFileNo')
                     ->orOn('mother_applications.fileno', '=', 'registered_instruments.KAGISFileNO')
                     ->orOn('mother_applications.fileno', '=', 'registered_instruments.NewKANGISFileNo');
            })
            ->select('mother_applications.*', 'mother_applications.owner_fullname', 'mother_applications.property_lga', 'mother_applications.property_house_no', 'mother_applications.property_plot_no', 'mother_applications.property_street_name', 'mother_applications.property_district',
                     'registered_instruments.volume_no', 'registered_instruments.page_no', 'registered_instruments.serial_no')
            ->where(function($q) use ($query, $fileNo, $isFileNumber) {
                if ($isFileNumber) {
                    $q->where(function($qq) use ($fileNo) {
                        $qq->whereRaw("UPPER(mother_applications.fileno) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(mother_applications.fileno) LIKE UPPER(?)", ["{$fileNo}%"]);
                    });
                } else {
                    // For other searches, use broader matching
                    $q->Where('mother_applications.applicant_title', 'like', "%{$query}%")
                      ->orWhere('mother_applications.first_name', 'like', "%{$query}%")
                      ->orWhere('mother_applications.middle_name', 'like', "%{$query}%")
                      ->orWhere('mother_applications.surname', 'like', "%{$query}%")
                      ->orWhere('mother_applications.passport', 'like', "%{$query}%")
                      ->orWhere('mother_applications.fileno', 'like', "%{$query}%")
                      ->orWhere('mother_applications.corporate_name', 'like', "%{$query}%")
                      ->orWhere('mother_applications.rc_number', 'like', "%{$query}%")
                      ->orWhere('mother_applications.multiple_owners_names', 'like', "%{$query}%")
                      ->orWhere('mother_applications.multiple_owners_passport', 'like', "%{$query}%")
                      ->orWhere('mother_applications.address', 'like', "%{$query}%")
                      ->orWhere('mother_applications.phone_number', 'like', "%{$query}%")
                      ->orWhere('mother_applications.email', 'like', "%{$query}%")
                      ->orWhere('mother_applications.identification_type', 'like', "%{$query}%")
                      ->orWhere('mother_applications.additional_comments', 'like', "%{$query}%")
                      ->orWhere('mother_applications.receipt_date', 'like', "%{$query}%")
                      ->orWhere('mother_applications.revenue_accountant', 'like', "%{$query}%")
                      ->orWhere('mother_applications.accountant_signature_date', 'like', "%{$query}%")
                      ->orWhere('mother_applications.scheme_no', 'like', "%{$query}%")
                      ->orWhere('mother_applications.plot_size', 'like', "%{$query}%")
                      ->orWhere('mother_applications.land_use', 'like', "%{$query}%")
                      ->orWhere('mother_applications.NoOfUnits', 'like', "%{$query}%")
                      ->orWhere('mother_applications.address_house_no', 'like', "%{$query}%")
                      ->orWhere('mother_applications.address_plot_no', 'like', "%{$query}%")
                      ->orWhere('mother_applications.address_street_name', 'like', "%{$query}%")
                      ->orWhere('mother_applications.address_district', 'like', "%{$query}%")
                      ->orWhere('mother_applications.address_lga', 'like', "%{$query}%")
                      ->orWhere('mother_applications.address_state', 'like', "%{$query}%")
                      ->orWhere('mother_applications.property_house_no', 'like', "%{$query}%")
                      ->orWhere('mother_applications.property_plot_no', 'like', "%{$query}%")
                      ->orWhere('mother_applications.property_street_name', 'like', "%{$query}%")
                      ->orWhere('mother_applications.property_district', 'like', "%{$query}%")
                      ->orWhere('mother_applications.property_lga', 'like', "%{$query}%")
                      ->orWhere('mother_applications.property_state', 'like', "%{$query}%")
                      ->orWhere('mother_applications.residential_type', 'like', "%{$query}%")
                      ->orWhere('mother_applications.NoOfSections', 'like', "%{$query}%")
                      ->orWhere('mother_applications.NoOfBlocks', 'like', "%{$query}%")
                      ->orWhere('mother_applications.ownership_type', 'like', "%{$query}%")
                      ->orWhere('mother_applications.ownership_type_others_text', 'like', "%{$query}%")
                      ->orWhere('mother_applications.ownership', 'like', "%{$query}%")
                      ->orWhere('mother_applications.industrial_type', 'like', "%{$query}%")
                      ->orWhere('mother_applications.commercial_type', 'like', "%{$query}%")
                      ->orWhere('mother_applications.documents', 'like', "%{$query}%")
                      ->orWhere('mother_applications.owner_fullname', 'like', "%{$query}%")
                      ->orWhere('mother_applications.identification_others', 'like', "%{$query}%")
                      ->orWhere('mother_applications.ownershipType', 'like', "%{$query}%")
                      ->orWhere('mother_applications.mixed_type', 'like', "%{$query}%")
                      ->orWhere('mother_applications.applicationID', 'like', "%{$query}%")
                      ->orWhere('mother_applications.application_fee', 'like', "%{$query}%")
                      ->orWhere('mother_applications.processing_fee', 'like', "%{$query}%")
                      ->orWhere('mother_applications.site_plan_fee', 'like', "%{$query}%")
                      ->orWhere('mother_applications.payment_date', 'like', "%{$query}%")
                      ->orWhere('mother_applications.receipt_number', 'like', "%{$query}%")
                      ->orWhere('mother_applications.created_by', 'like', "%{$query}%")
                      ->orWhere('mother_applications.updated_by', 'like', "%{$query}%")
                      ->orWhere('mother_applications.planning_recommendation_status', 'like', "%{$query}%")
                      ->orWhere('mother_applications.recomm_comments', 'like', "%{$query}%")
                      ->orWhere('mother_applications.planning_approval_date', 'like', "%{$query}%")
                      ->orWhere('mother_applications.application_status', 'like', "%{$query}%")
                      ->orWhere('mother_applications.director_comments', 'like', "%{$query}%")
                      ->orWhere('mother_applications.approval_date', 'like', "%{$query}%")
                      ->orWhere('mother_applications.shared_areas', 'like', "%{$query}%")
                      ->orWhere('mother_applications.comments', 'like', "%{$query}%")
                      ->orWhere('mother_applications.deeds_status', 'like', "%{$query}%");
                }
            })
            ->get();

        // subapplications table with joins to mother_applications and registered_instruments
        $subapplications = DB::connection('sqlsrv')->table('subapplications')
            ->leftJoin('mother_applications', 'subapplications.main_application_id', '=', 'mother_applications.id')
            ->leftJoin('registered_instruments', function($join) {
                $join->on('subapplications.fileno', '=', 'registered_instruments.MLSFileNo')
                     ->orOn('subapplications.fileno', '=', 'registered_instruments.KAGISFileNO')
                     ->orOn('subapplications.fileno', '=', 'registered_instruments.NewKANGISFileNo');
            })
            ->select(
                'subapplications.*',
                'mother_applications.owner_fullname as mother_owner_fullname',
                'subapplications.multiple_owners_names as sub_owner_fullname',
                'mother_applications.property_lga',
                'mother_applications.property_house_no',
                'mother_applications.property_plot_no',
                'mother_applications.property_street_name',
                'mother_applications.property_district',
                'registered_instruments.volume_no',
                'registered_instruments.page_no', 
                'registered_instruments.serial_no'
            )
            ->where(function($q) use ($query, $fileNo, $isFileNumber) {
                if ($isFileNumber) {
                    $q->where(function($qq) use ($fileNo) {
                        $qq->whereRaw("UPPER(subapplications.fileno) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(subapplications.fileno) LIKE UPPER(?)", ["{$fileNo}%"]);
                    });
                } else {
                    // For other searches, use broader matching
                    $q->where('subapplications.main_id', 'like', "%{$query}%")
                      ->orWhere('subapplications.fileno', 'like', "%{$query}%")
                  ->orWhere('subapplications.applicant_title', 'like', "%{$query}%")
                  ->orWhere('subapplications.first_name', 'like', "%{$query}%")
                  ->orWhere('subapplications.middle_name', 'like', "%{$query}%")
                  ->orWhere('subapplications.surname', 'like', "%{$query}%")
                  ->orWhere('subapplications.passport', 'like', "%{$query}%")
                  ->orWhere('subapplications.corporate_name', 'like', "%{$query}%")
                  ->orWhere('subapplications.rc_number', 'like', "%{$query}%")
                  ->orWhere('subapplications.multiple_owners_names', 'like', "%{$query}%")
                  ->orWhere('subapplications.multiple_owners_passport', 'like', "%{$query}%")
                  ->orWhere('subapplications.multiple_owners_data', 'like', "%{$query}%")
                  ->orWhere('subapplications.address', 'like', "%{$query}%")
                  ->orWhere('subapplications.phone_number', 'like', "%{$query}%")
                  ->orWhere('subapplications.email', 'like', "%{$query}%")
                  ->orWhere('subapplications.identification_type', 'like', "%{$query}%")
                  ->orWhere('subapplications.identification_others', 'like', "%{$query}%")
                  ->orWhere('subapplications.block_number', 'like', "%{$query}%")
                  ->orWhere('subapplications.floor_number', 'like', "%{$query}%")
                  ->orWhere('subapplications.unit_number', 'like', "%{$query}%")
                  ->orWhere('subapplications.property_location', 'like', "%{$query}%")
                  ->orWhere('subapplications.ownership', 'like', "%{$query}%")
                  ->orWhere('subapplications.application_status', 'like', "%{$query}%")
                  ->orWhere('subapplications.planning_recomm_comments', 'like', "%{$query}%")
                  ->orWhere('subapplications.approval_date', 'like', "%{$query}%")
                  ->orWhere('subapplications.planning_recommendation_status', 'like', "%{$query}%")
                  ->orWhere('subapplications.application_fee', 'like', "%{$query}%")
                  ->orWhere('subapplications.processing_fee', 'like', "%{$query}%")
                  ->orWhere('subapplications.site_plan_fee', 'like', "%{$query}%")
                  ->orWhere('subapplications.payment_date', 'like', "%{$query}%")
                  ->orWhere('subapplications.receipt_number', 'like', "%{$query}%")
                  ->orWhere('subapplications.land_use', 'like', "%{$query}%")
                  ->orWhere('subapplications.plot_size', 'like', "%{$query}%")
                  ->orWhere('subapplications.commercial_type', 'like', "%{$query}%")
                  ->orWhere('subapplications.industrial_type', 'like', "%{$query}%")
                  ->orWhere('subapplications.ownership_type', 'like', "%{$query}%")
                  ->orWhere('subapplications.residence_type', 'like', "%{$query}%")
                  ->orWhere('subapplications.planning_approval_date', 'like', "%{$query}%")
                  ->orWhere('subapplications.director_comments', 'like', "%{$query}%")
                  ->orWhere('subapplications.address_street_name', 'like', "%{$query}%")
                  ->orWhere('subapplications.address_district', 'like', "%{$query}%")
                  ->orWhere('subapplications.address_lga', 'like', "%{$query}%")
                  ->orWhere('subapplications.address_state', 'like', "%{$query}%")
                  ->orWhere('subapplications.scheme_no', 'like', "%{$query}%")
                  ->orWhere('subapplications.shared_areas', 'like', "%{$query}%")
                  ->orWhere('subapplications.documents', 'like', "%{$query}%")
                  ->orWhere('subapplications.application_comment', 'like', "%{$query}%")
                  ->orWhere('subapplications.main_application_id', 'like', "%{$query}%")
                  ->orWhere('subapplications.memo_status', 'like', "%{$query}%")
                  ->orWhere('subapplications.updated_by', 'like', "%{$query}%")
                  ->orWhere('subapplications.created_by', 'like', "%{$query}%")
                  ->orWhere('subapplications.deeds_status', 'like', "%{$query}%");
                }
            })
            ->get();

        $registered_instruments = DB::connection('sqlsrv')->table('registered_instruments')
            ->where(function($q) use ($query, $fileNo, $isFileNumber) {
                if ($isFileNumber) {
                    $q->where(function($qq) use ($fileNo) {
                        $qq->whereRaw("UPPER(MLSFileNo) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(KAGISFileNO) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(NewKANGISFileNo) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(MLSFileNo) LIKE UPPER(?)", ["{$fileNo}%"])
                           ->orWhereRaw("UPPER(KAGISFileNO) LIKE UPPER(?)", ["{$fileNo}%"])
                           ->orWhereRaw("UPPER(NewKANGISFileNo) LIKE UPPER(?)", ["{$fileNo}%"]);
                    });
                } else {
                    // For other searches, use broader matching
                    $q->where('MLSFileNo', 'like', "%{$query}%")
                      ->orWhere('KAGISFileNO', 'like', "%{$query}%")
                      ->orWhere('NewKANGISFileNo', 'like', "%{$query}%")
            ->orWhere('rootRegistrationNumber', 'like', "%{$query}%")
            ->orWhere('particularsRegistrationNumber', 'like', "%{$query}%")
            ->orWhere('instrument_type', 'like', "%{$query}%")
            ->orWhere('Grantor', 'like', "%{$query}%")
            ->orWhere('GrantorAddress', 'like', "%{$query}%")
            ->orWhere('Grantee', 'like', "%{$query}%")
            ->orWhere('GranteeAddress', 'like', "%{$query}%")
            ->orWhere('mortgagor', 'like', "%{$query}%")
            ->orWhere('mortgagorAddress', 'like', "%{$query}%")
            ->orWhere('mortgagee', 'like', "%{$query}%")
            ->orWhere('mortgageeAddress', 'like', "%{$query}%")
            ->orWhere('loanAmount', 'like', "%{$query}%")
            ->orWhere('interestRate', 'like', "%{$query}%")
            ->orWhere('duration', 'like', "%{$query}%")
            ->orWhere('assignor', 'like', "%{$query}%")
            ->orWhere('assignorAddress', 'like', "%{$query}%")
            ->orWhere('assignee', 'like', "%{$query}%")
            ->orWhere('assigneeAddress', 'like', "%{$query}%")
            ->orWhere('lessor', 'like', "%{$query}%")
            ->orWhere('lessorAddress', 'like', "%{$query}%")
            ->orWhere('lessee', 'like', "%{$query}%")
            ->orWhere('lesseeAddress', 'like', "%{$query}%")
            ->orWhere('leasePeriod', 'like', "%{$query}%")
            ->orWhere('leaseTerms', 'like', "%{$query}%")
            ->orWhere('propertyDescription', 'like', "%{$query}%")
            ->orWhere('propertyAddress', 'like', "%{$query}%")
            ->orWhere('originalPlotDetails', 'like', "%{$query}%")
            ->orWhere('newSubDividedPlotDetails', 'like', "%{$query}%")
            ->orWhere('mergedPlotInformation', 'like', "%{$query}%")
            ->orWhere('surrenderingPartyName', 'like', "%{$query}%")
            ->orWhere('receivingPartyName', 'like', "%{$query}%")
            ->orWhere('propertyDetails', 'like', "%{$query}%")
            ->orWhere('considerationAmount', 'like', "%{$query}%")
            ->orWhere('changesVariations', 'like', "%{$query}%")
            ->orWhere('heirBeneficiaryDetails', 'like', "%{$query}%")
            ->orWhere('originalPropertyOwnerDetails', 'like', "%{$query}%")
            ->orWhere('assentTerms', 'like', "%{$query}%")
            ->orWhere('releasorName', 'like', "%{$query}%")
            ->orWhere('releaseeName', 'like', "%{$query}%")
            ->orWhere('releaseTerms', 'like', "%{$query}%")
            ->orWhere('instrumentDate', 'like', "%{$query}%")
            ->orWhere('solicitorName', 'like', "%{$query}%")
            ->orWhere('solicitorAddress', 'like', "%{$query}%")
            ->orWhere('surveyPlanNo', 'like', "%{$query}%")
            ->orWhere('lga', 'like', "%{$query}%")
            ->orWhere('district', 'like', "%{$query}%")
            ->orWhere('size', 'like', "%{$query}%")
            ->orWhere('plotNumber', 'like', "%{$query}%")
            ->orWhere('typeForm', 'like', "%{$query}%")
            ->orWhere('surrenderee', 'like', "%{$query}%")
            ->orWhere('surrenderor', 'like', "%{$query}%")
            ->orWhere('subLease', 'like', "%{$query}%")
            ->orWhere('thirdParty', 'like', "%{$query}%")
            ->orWhere('landUseType', 'like', "%{$query}%")
            ->orWhere('titleType', 'like', "%{$query}%")
            ->orWhere('assignment', 'like', "%{$query}%")
            ->orWhere('batchNumber', 'like', "%{$query}%")
            ->orWhere('grantLease', 'like', "%{$query}%")
            ->orWhere('statutory', 'like', "%{$query}%")
            ->orWhere('customer', 'like', "%{$query}%")
            ->orWhere('categoryCode', 'like', "%{$query}%")
            ->orWhere('mortgage', 'like', "%{$query}%")
            ->orWhere('assignorName', 'like', "%{$query}%")
            ->orWhere('batchNo', 'like', "%{$query}%")
            ->orWhere('plotNo', 'like', "%{$query}%")
            ->orWhere('Period', 'like', "%{$query}%")
            ->orWhere('status', 'like', "%{$query}%")
            ->orWhere('created_by', 'like', "%{$query}%")
            ->orWhere('updated_by', 'like', "%{$query}%")
            ->orWhere('volume_no', 'like', "%{$query}%")
            ->orWhere('page_no', 'like', "%{$query}%")
            ->orWhere('serial_no', 'like', "%{$query}%")
            ->orWhere('deeds_time', 'like', "%{$query}%")
            ->orWhere('deeds_date', 'like', "%{$query}%")
            ->orWhere('STM_Ref', 'like', "%{$query}%");
                }
            })
            ->get();

        // cofo table
        $cofo = DB::connection('sqlsrv')->table('cofo')
            ->where(function($q) use ($query, $fileNo, $isFileNumber) {
                if ($isFileNumber) {
                    $q->where(function($qq) use ($fileNo) {
                        $qq->whereRaw("UPPER(mlsfNo) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(kangisFileNo) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(NewKANGISFileno) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(fileNo) = UPPER(?)", [$fileNo])
                           ->orWhereRaw("UPPER(mlsfNo) LIKE UPPER(?)", ["{$fileNo}%"])
                           ->orWhereRaw("UPPER(kangisFileNo) LIKE UPPER(?)", ["{$fileNo}%"])
                           ->orWhereRaw("UPPER(NewKANGISFileno) LIKE UPPER(?)", ["{$fileNo}%"])
                           ->orWhereRaw("UPPER(fileNo) LIKE UPPER(?)", ["{$fileNo}%"]);
                    });
                } else {
                    // For other searches, use broader matching
                    $q->where('mlsfNo', 'like', "%{$query}%")
                      ->orWhere('kangisFileNo', 'like', "%{$query}%")
                      ->orWhere('NewKANGISFileno', 'like', "%{$query}%")
                      ->orWhere('fileNo', 'like', "%{$query}%")
            ->orWhere('plotNo', 'like', "%{$query}%")
            ->orWhere('blockNo', 'like', "%{$query}%")
            ->orWhere('approvedPlanNo', 'like', "%{$query}%")
            ->orWhere('tpPlanNo', 'like', "%{$query}%")
            ->orWhere('layoutName', 'like', "%{$query}%")
            ->orWhere('districtName', 'like', "%{$query}%")
            ->orWhere('lgaName', 'like', "%{$query}%")
            ->orWhere('oldTitleSerialNo', 'like', "%{$query}%")
            ->orWhere('oldTitlePageNo', 'like', "%{$query}%")
            ->orWhere('oldTitleVolumeNo', 'like', "%{$query}%")
            ->orWhere('deedsDate', 'like', "%{$query}%")
            ->orWhere('deedsTime', 'like', "%{$query}%")
            ->orWhere('certificateDate', 'like', "%{$query}%")
            ->orWhere('originalAllottee', 'like', "%{$query}%")
            ->orWhere('addressOfOriginalAllottee', 'like', "%{$query}%")
            ->orWhere('currentAllottee', 'like', "%{$query}%")
            ->orWhere('addressOfCurrentAllottee', 'like', "%{$query}%")
            ->orWhere('titleOfCurrentAllottee', 'like', "%{$query}%")
            ->orWhere('plotDescription', 'like', "%{$query}%")
            ->orWhere('landUse', 'like', "%{$query}%")
            ->orWhere('StateName', 'like', "%{$query}%")
            ->orWhere('fileNo', 'like', "{$query}%")
            ->orWhere('groundRent', 'like', "%{$query}%")
            ->orWhere('occupancy', 'like', "%{$query}%");
                }
            })
            ->get();

        return response()->json([
            'property_records' => $property_records,
            'mother_applications' => $mother_applications,
            'subapplications' => $subapplications,
            'registered_instruments' => $registered_instruments,
            'cofo' => $cofo,
        ]);
    }

    public function report()
    {
        return view('legal_search.report');
    }

    public function legal_search_report()
    {
        $PageTitle = 'Legal Search Report';
        return view('legal_search.legal_search_report');
    }
    
    /**
     * Check if the query looks like a file number pattern
     * 
     * @param string $query
     * @return bool
     */
    private function isFileNumberPattern($query)
    {
        $trimmedQuery = trim($query);
        
        // Only consider it a file number if it has more specific patterns
        $patterns = [
            '/^ST-\d+/i',        // ST- followed by numbers
            '/^RES-\d+/i',       // RES- followed by numbers  
            '/^COM-\d+/i',       // COM- followed by numbers
            '/^IND-\d+/i',       // IND- followed by numbers
            '/^MLS-\d+/i',       // MLS- followed by numbers
            '/^KAN-\d+/i',       // KAN- followed by numbers
            '/^KANGIS-\d+/i',    // KANGIS- followed by numbers
            '/^[A-Z]{2,4}-\d+/i', // Pattern like XX-123 or XXX-123
            '/^\d{4}\/\d+/i',    // Pattern like 2023/123
            '/^[A-Z]+\d{3,}/i',  // Pattern like ABC123 (at least 3 digits)
        ];
        
        // If it's just a prefix like "ST-" without numbers, treat it as file number search
        if (preg_match('/^(ST|RES|COM|IND|MLS|KAN|KANGIS)-$/i', $trimmedQuery)) {
            return true;
        }
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $trimmedQuery)) {
                return true;
            }
        }
        
        return false;
    }
}