@extends('layouts.app')
@section('page-title')
{{ __('Secondary Application Form') }}
@endsection
@include('sectionaltitling.sub_app_css')
@include('sectionaltitling.partials.assets.css')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<!-- Animate.css for SweetAlert animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.swal-validation-popup {
    font-family: inherit;
}
.swal-validation-title {
    color: #dc2626 !important;
    font-weight: 600;
}
.swal-validation-content {
    color: #374151;
}
/* Select2 Custom Styling */
.select2-container--default .select2-selection--single {
    height: 42px;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px;
    padding-left: 8px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
}
.select2-dropdown {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #3b82f6;
}

/* Enhanced Step Circle Styles */
.step-circle {
    transition: all 0.2s ease;
}

.step-circle.cursor-pointer:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.step-circle.active-tab.cursor-pointer:hover {
    background-color: #059669;
    border-color: #059669;
}

.step-circle.inactive-tab.cursor-pointer:hover {
    background-color: #6b7280;
    border-color: #6b7280;
    color: white;
}
</style>
 
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 


<!-- Main Content -->
<div class="flex-1 overflow-auto">
<!-- Header -->
@include('admin.header')
<!-- Dashboard Content -->
<div class="p-6">

    

    @php
    $mainApplicationId = request()->get('application_id');
    // Fetch data from the mother_applications table
    $motherApplication = DB::connection('sqlsrv')->table('mother_applications')->where('id', $mainApplicationId)->first();
    $totalUnitsInMotherApp = $motherApplication ? $motherApplication->NoOfUnits : 0;

    // Count the number of sub-applications linked to the main application
    $totalSubApplications = DB::connection('sqlsrv')->table('subapplications')->where('main_application_id', $mainApplicationId)->count();

    // Calculate the remaining units
    $remainingUnits = $totalUnitsInMotherApp - $totalSubApplications;

    // Get property location
    $propertyLocation = '';
    if ($motherApplication) {
      $locationParts = array_filter([
        $motherApplication->property_plot_no ?? null,
        $motherApplication->property_street_name ?? null,
        $motherApplication->property_district ?? null
      ]);
      $propertyLocation = implode(', ', $locationParts);
    }

    // Fetch buyers and their unit measurements for this mother application
    $buyersWithUnits = [];
    if ($motherApplication) {
        $buyersWithUnits = DB::connection('sqlsrv')
            ->table('buyer_list as bl')
            ->leftJoin('st_unit_measurements as sum', function($join) use ($motherApplication) {
                $join->on('bl.unit_no', '=', 'sum.unit_no')
                     ->where('sum.application_id', '=', $motherApplication->id);
            })
            ->select(
                'bl.application_id',
                'bl.buyer_title',
                'bl.buyer_name', 
                'bl.unit_no',
                'sum.measurement',
                'sum.buyer_id'
            )
            ->where('bl.application_id', $motherApplication->id)
            ->get();
    }
  @endphp

    <!-- Primary Applications Table -->
    <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
        <div class="container py-4">
            <div class="modal-content">
                <!-- Step 1: Basic Information -->
                
                <div class="form-section active-tab" id="step1">
                  <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                      <h2 class="text-xl font-bold text-gray-800">MINISTRY OF LAND AND PHYSICAL PLANNING</h2>
                      <button   class="text-gray-500 hover:text-gray-700" onclick="window.history.back()">
                        <i data-lucide="x" class="w-5 h-5"></i>
                      </button>
                    </div>
                    
                    <div class="mb-6">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <i data-lucide="file-text" class="w-5 h-5 mr-2 text-green-600"></i>
                          <h3 class="text-lg font-bold items-center">Application for Sectional Titling - Unit Application (Secondary)</h3>
                        </div>
                        <div class="flex items-center">
                          <span class="text-gray-600 mr-2">Land Use:</span>
                          <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">{{ $motherApplication->land_use ?? 'N/A' }}</span>
                        </div>
                      </div>
                      <p class="text-gray-600 mt-1">Complete the form below to submit a new unit application for sectional titling</p>
                    </div>
            
                    <div class="flex items-center mb-6">
                    <div class="flex items-center mr-4">
                    <div class="step-circle active-tab flex items-center justify-center cursor-pointer" onclick="goToStep(1)">1</div>
                    </div>
                    <div class="flex items-center mr-4">
                    <div class="step-circle inactive-tab flex items-center justify-center cursor-pointer" onclick="goToStep(2)">2</div>
                    </div>
                    <div class="flex items-center mr-4">
                    <div class="step-circle inactive-tab flex items-center justify-center cursor-pointer" onclick="goToStep(3)">3</div>
                    </div>    
                    <div class="flex items-center mr-4">
                    <div class="step-circle inactive-tab flex items-center justify-center cursor-pointer" onclick="goToStep(4)">4</div>
                    </div>
                    <div class="ml-4">Step 1</div>
                    </div>
            
                    <div class="mb-6">
                      <div class="text-right text-sm text-gray-500">CODE: ST FORM - 1</div>
                      <hr class="my-4">
                      @php
                        $mainApplicationId = request()->get('application_id');
                        // Fetch data from the mother_applications table
                        $motherApplication = DB::connection('sqlsrv')->table('mother_applications')->where('id', $mainApplicationId)->first();
                        $totalUnitsInMotherApp = $motherApplication ? $motherApplication->NoOfUnits : 0;

                        // Count the number of sub-applications linked to the main application
                        $totalSubApplications = DB::connection('sqlsrv')->table('subapplications')->where('main_application_id', $mainApplicationId)->count();

                        // Calculate the remaining units
                        $remainingUnits = $totalUnitsInMotherApp - $totalSubApplications;

                        // Get property location
                        $propertyLocation = '';
                        if ($motherApplication) {
                          $locationParts = array_filter([
                            $motherApplication->property_plot_no ?? null,
                            $motherApplication->property_street_name ?? null,
                            $motherApplication->property_district ?? null
                          ]);
                          $propertyLocation = implode(', ', $locationParts);
                        }
                      @endphp
                      
                      <form id="subApplicationForm" method="POST" action="{{ route('secondaryform.save') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
                        @csrf
                        <input type="hidden" name="main_application_id" value="{{ $mainApplicationId ?? '' }}">
                        <input type="hidden" name="main_id" id="mainIdHidden" value="@php
             $mainYear = $motherApplication && $motherApplication->created_at ? date('Y', strtotime($motherApplication->created_at)) : date('Y');
            $mainAppId = $motherApplication->id ?? '';
             echo sprintf('ST-%s-%03d', $mainYear, $mainAppId);
              @endphp">
                        
                      <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-3">Main Application Reference</h2>
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                          <div class="flex items-center justify-between mb-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700 mb-1">Main Application ID</label>
                              <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-id-card-icon lucide-id-card"><path d="M16 10h2"/><path d="M16 14h2"/><path d="M6.17 15a3 3 0 0 1 5.66 0"/><circle cx="9" cy="11" r="2"/><rect x="2" y="5" width="20" height="14" rx="2"/></svg> 

                                  {{ $motherApplication->applicationID ?? 'N/A' }}
                                </span>
                              </div>            
                             
                            </div>
                            <div class="flex items-center">
                              <span class="px-3 py-1 text-sm rounded-full {{ $remainingUnits > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $remainingUnits }} units remaining
                              </span>
                            </div>
                          </div>

                          <!-- Main Application Details -->
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                            <!-- Applicant Information -->
                            <div class="bg-gray-50 p-4 rounded-md">
                              <h3 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Applicant Information
                              </h3>
                              <div class="space-y-2 text-sm">
                                <div class="flex">
                                  <span class="text-gray-500 w-36">Applicant Type:</span>
                                  <span class="font-medium">{{ $motherApplication->applicant_type ?? 'N/A' }}</span>
                                </div>
                                <div class="flex">
                                  <span class="text-gray-500 w-36">Name:</span>
                                  <span class="font-medium">
                                    {{ $motherApplication->applicant_title ?? '' }} 
                                    {{ $motherApplication->first_name ?? '' }} 
                                    {{ $motherApplication->surname ?? '' }}
                                  </span>
                                </div>
                                <div class="flex">
                                  <span class="text-gray-500 w-36">Form ID:</span>
                                  <span class="font-medium">{{ $motherApplication->id ?? 'N/A' }}</span>
                                </div>
                              </div>
                            </div>

                            <!-- Property Information -->
                            <div class="bg-gray-50 p-4 rounded-md">
                              <h3 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Property Information
                              </h3>
                              <div class="space-y-2 text-sm">
                                <div class="flex">
                                  <span class="text-gray-500 w-36">File Number:</span>
                                  <span class="font-medium">{{ $motherApplication->fileno ?? 'N/A' }}</span>
                                </div>
                                <div class="flex">
                                  <span class="text-gray-500 w-36">Land Use:</span>
                                  <span class="font-medium">{{ $motherApplication->land_use ?? 'N/A' }}</span>
                                </div>
                                <div class="flex">
                                  <span class="text-gray-500 w-36">Property Location:</span>
                                  <span class="font-medium">{{ $propertyLocation ?: 'N/A' }}</span>
                                </div>
                                <div class="flex">
                                  <span class="text-gray-500 w-36">Total Units:</span>
                                  <span class="font-medium">{{ $totalUnitsInMotherApp }}</span>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Progress indicator -->
                          <div class="mt-5 pt-4 border-t border-gray-200">
                            <div class="flex items-center">
                              <div class="w-full bg-gray-200 rounded-full h-2.5">
                                @php $progressPercent = $totalUnitsInMotherApp > 0 ? (($totalSubApplications / $totalUnitsInMotherApp) * 100) : 0; @endphp
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progressPercent }}%"></div>
                              </div>
                              <span class="ml-3 text-sm text-gray-600">{{ $totalSubApplications }}/{{ $totalUnitsInMotherApp }} units registered</span>
                            </div>
                          </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">This sub-application will be linked to the main application referenced above.</p>
                      </div>    
                    <div class="grid grid-cols-3 gap-6 mb-6">
                        
                        <!-- Left column (2/3 width) -->
                           <div>
                              <label class="block text-sm mb-1">NP FileNo (NPFN)</label>
                              <input type="text" class="w-full p-2 border border-gray-300 rounded-md bg-blue-100 text-blue-700 cursor-not-allowed" name="np_fileno" value="{{ $npFileNo ?? 'N/A' }}" readonly title="New Primary FileNo">
                            </div>
                           <div>
                              <label class="block text-sm mb-1">Unit FileNo</label>
                              <input type="text" class="w-full p-2 border border-gray-300 rounded-md bg-green-100 text-green-700 cursor-not-allowed" name="fileno" value="{{ $unitFileNo ?? 'N/A' }}" readonly title="Unit FileNo (NP FileNo + Serial)">
                            </div>            
                            <div>
                                <label class="block text-sm mb-1">Scheme No <span class="text-red-500">*</span></label>
                                <input type="text" id="schemeName" class="w-full p-2 border border-gray-300 rounded-md"    name="scheme_no" placeholder="enter scheme number. eg: ST/SP/0001" required>
                            </div>
                            
                        <div class="col-span-2">
                          <!-- Buyer Selection Section -->
                          <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-5">
                            <div class="flex items-center mb-3">
                              <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                              </div>
                              <div class="ml-3">
                                <h3 class="text-lg font-semibold text-gray-900">Select Existing Buyer</h3>
                                <p class="text-sm text-gray-600">Optional - Choose from registered buyers to auto-fill form</p>
                              </div>
                            </div>
                            
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                              <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-end">
                                <!-- Search Buyer Field -->
                                <div class="lg:col-span-2">
                                  <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Search Buyer
                                  </label>
                                  <select id="buyerSelect" class="w-full" name="selected_buyer_id">
                                    <option value="">Type to search buyer name...</option>
                                    @foreach($buyersWithUnits as $buyer)
                                      <option value="{{ $buyer->application_id }}_{{ $buyer->unit_no }}" 
                                              data-buyer-title="{{ $buyer->buyer_title }}"
                                              data-buyer-name="{{ $buyer->buyer_name }}"
                                              data-unit-no="{{ $buyer->unit_no }}"
                                              data-measurement="{{ $buyer->measurement }}">
                                        {{ $buyer->buyer_title }} {{ $buyer->buyer_name }} (Unit: {{ $buyer->unit_no }})
                                        @if($buyer->measurement) - {{ $buyer->measurement }} @endif
                                      </option>
                                    @endforeach
                                  </select>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                  <button type="button" id="clearBuyerSelection" class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:text-red-700 transition-colors duration-200" style="display: none;">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Clear
                                  </button>
                                </div>
                              </div>
                              
                              <!-- Selected Buyer Info Display -->
                              <div id="selectedBuyerInfo" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md" style="display: none;">
                                <div class="flex items-center">
                                  <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                  </svg>
                                  <div>
                                    <p class="text-sm font-medium text-green-800">Buyer Selected Successfully</p>
                                    <p class="text-xs text-green-600" id="selectedBuyerDetails"></p>
                                  </div>
                                </div>
                              </div>
                              
                              <!-- Available Buyers Count -->
                              @if(count($buyersWithUnits) > 0)
                                <div class="mt-3 flex items-center text-xs text-gray-500">
                                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                  </svg>
                                  {{ count($buyersWithUnits) }} registered buyer(s) available for this application
                                </div>
                              @else
                                <div class="mt-3 flex items-center text-xs text-amber-600 bg-amber-50 p-2 rounded">
                                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                  </svg>
                                  No registered buyers found for this application
                                </div>
                              @endif
                            </div>
                          </div>
                          
                          <div class="mb-6">
                            <label class="block mb-2 font-medium">Applicant Type</label>
                            <div class="flex space-x-6">
                                <label class="flex items-center">
                                    <input type="radio" name="applicantType" class="mr-2" value="individual"  onclick="setApplicantType('individual'); showIndividualFields()">
                                    <span>Individual</span>
                                  </label>
                                  <label class="flex items-center">
                                    <input type="radio" name="applicantType" class="mr-2" value="corporate" onclick="setApplicantType('corporate'); showCorporateFields()">
                                    <span>Corporate Body</span>
                                  </label>
                                  <label class="flex items-center">
                                    <input type="radio" name="applicantType" class="mr-2" value="multiple" onclick="setApplicantType('multiple'); showMultipleOwnersFields()">
                                    <span>Multiple Owners</span>
                                  </label>
                            </div>
                          </div>
                        </div>
                        
                        <! -- Right column (1/3 width) -->
                      </div>

                      @include('sectionaltitling.partials.subapplication.applicant')
                      

                      <div class="bg-gray-50 p-4 rounded-md mb-6">
            
                         
                       
                        <div class="grid grid-cols-2 gap-4 mb-4">
                         
                           
                            <div style="display: none">
                              <input type="text"   class="w-full p-2 border border-gray-300 rounded-md"  name="prefix" value="{{ $prefix }}" >
                              <input type="text"   class="w-full p-2 border border-gray-300 rounded-md"  name="year" value="{{ $currentYear }}"  >
                              <input type="text"   class="w-full p-2 border border-gray-300 rounded-md"  name="serial_number" value="{{ $formattedSerialNumber }}"  >
                               
                            </div> 
                            
                            
                         
                        </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                  
                        <div class="mb-4">
                        <p class="text-sm mb-1">Unit Owner's Address</p>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                          <div>
                            <label class="block text-sm mb-1">House No.  <span class="text-red-500">*</span></label>
                            <input type="text" id="ownerHouseNo" class="w-full p-2 border border-gray-300 rounded-md" placeholder="HOUSE NO." name="address_house_no" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase()">
                          </div>
                          <div>
                            <label class="block text-sm mb-1">Street Name  <span class="text-red-500">*</span></label>
                            <input type="text" id="ownerStreetName" class="w-full p-2 border border-gray-300 rounded-md" placeholder="STREET NAME" name="address_street_name" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase()">
                          </div>
                        </div>
              
                        <div class="grid grid-cols-3 gap-4 mb-4">
                          <div>
                            <label class="block text-sm mb-1">District  <span class="text-red-500">*</span></label>
                            <input type="text" id="ownerDistrict" class="w-full p-2 border border-gray-300 rounded-md" placeholder="DISTRICT" name="address_district" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase()">
                          </div>
                          <div>
                            <label class="block text-sm mb-1">LGA  <span class="text-red-500">*</span></label>
                            <select id="ownerLga" name="address_lga" class="w-full p-2 border border-gray-300 rounded-md" style="text-transform:uppercase" disabled>
                              <option value="">SELECT LGA</option>
                            </select>
                          </div>
                          <div>
                            <label class="block text-sm mb-1">State  <span class="text-red-500">*</span></label>
                            <select id="ownerState" name="address_state" class="w-full p-2 border border-gray-300 rounded-md" onchange="selectLGA(this)" style="text-transform:uppercase">
                              <option value="">SELECT STATE</option>
                            </select>
                          </div>
                        </div>
                             <input type="hidden" name="address" id="contactAddressHidden">    
                        <div class="mb-4">
                          <label class="block text-sm mb-1">Contact Address:  <span class="text-red-500">*</span></label>
                          <div id="contactAddressDisplay" class="p-2 bg-gray-50 border border-gray-200 rounded-md">
                            <span id="fullContactAddress" style="text-transform:uppercase"></span>
                          </div>
                        </div>
               
                          <div class="grid grid-cols-2 gap-4 mb-4">
                          <div>
                            <label class="block text-sm mb-1">Phone No. 1</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded-md" placeholder="ENTER PHONE NUMBER" name="phone_number[]" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase()">
                          </div>
                          <div>
                            <label class="block text-sm mb-1">Phone No. 2</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded-md" placeholder="ENTER ALTERNATE PHONE" name="phone_number[]" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase()">
                          </div>
                          </div>
                          
                          <div>
                          <label class="block text-sm mb-1">Email Address</label>
                          <input type="email" class="w-full p-2 border border-gray-300 rounded-md" placeholder="ENTER EMAIL ADDRESS" name="owner_email"   >
                          </div>
                        </div>
                        </div>
                    <div class="bg-gray-50 p-4 rounded-md grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" id="mainIdentificationSection">
                      <!-- Left column: Means of Identification options -->
                      <div id="meansOfIdentificationOptions">
                        <label class="block mb-2 font-medium">Means of Identification</label>
                        <div class="grid grid-cols-1 gap-2">
                          <label class="flex items-center">
                            <input type="radio" name="identification_type" class="mr-2" value="national id" checked>
                            <span>National ID</span>
                          </label>
                          <label class="flex items-center">
                            <input type="radio" name="identification_type" class="mr-2" value="drivers license">
                            <span>Driver's License</span>
                          </label>
                          <label class="flex items-center">
                            <input type="radio" name="identification_type" class="mr-2" value="voters card">
                            <span>Voter's Card</span>
                          </label>
                          <label class="flex items-center">
                            <input type="radio" name="identification_type" class="mr-2" value="international passport">
                            <span>International Passport</span>
                          </label>
                          <label class="flex items-center">
                            <input type="radio" name="identification_type" class="mr-2" value="others">
                            <span>Others</span>
                          </label>
                        </div>
                      </div>
                      <!-- Right column: Image upload and preview -->
                      <div class="flex flex-col justify-between">
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1" id="uploadIdentificationLabel">Upload Means of Identification <span class="text-red-500">*</span></label>
                          <input type="file" name="identification_image" id="identification_image" accept="image/*,.pdf" class="w-full p-2 border border-gray-300 rounded-md bg-white">
                          <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, PDF. Max size: 5MB.</p>
                        </div>
                        <div class="mt-4">
                          <label class="block text-sm font-medium text-gray-700 mb-1">Preview</label>
                          <div id="identification_preview" class="border border-gray-200 rounded-md bg-white flex items-center justify-center min-h-[120px]">
                            <span class="text-gray-400 text-xs">No file selected</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                      const input = document.getElementById('identification_image');
                      const preview = document.getElementById('identification_preview');
                      input.addEventListener('change', function (e) {
                        preview.innerHTML = '';
                        const file = e.target.files[0];
                        if (!file) {
                          preview.innerHTML = '<span class="text-gray-400 text-xs">No file selected</span>';
                          return;
                        }
                        if (file.type.startsWith('image/')) {
                          const img = document.createElement('img');
                          img.className = "max-h-32 mx-auto";
                          img.style.maxWidth = "100%";
                          img.alt = "Preview";
                          img.src = URL.createObjectURL(file);
                          preview.appendChild(img);
                        } else if (file.type === 'application/pdf') {
                          const icon = document.createElement('span');
                          icon.innerHTML = '<svg class="w-8 h-8 text-red-500 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><span class="block text-xs mt-2">PDF Selected</span>';
                          preview.appendChild(icon);
                        } else {
                          preview.innerHTML = '<span class="text-red-500 text-xs">Unsupported file type</span>';
                        }
                      });
                    });
                    </script>
            
                      <div class="bg-gray-50 p-4 rounded-md mb-6">
                        <h3 class="font-medium mb-4">Unit Details</h3>
                        @include('sectionaltitling.types.ownership')
                        @include('sectionaltitling.types.commercial')
                        @include('sectionaltitling.types.residential')
                        @include('sectionaltitling.types.industrial')
                        
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Block No <span class="text-red-500">*</span></label>
                                <input type="text" name="block_number" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter block number" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Section No (Floor) <span class="text-red-500">*</span></label>
                                <input type="text" name="floor_number" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter floor number" required>
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Unit No <span class="text-red-500">*</span></label>
                              <input type="text" name="unit_number" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100 text-gray-700 cursor-not-allowed" placeholder="Enter unit number" readonly>
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Unit Size</label>
                              <input type="text" name="unit_size" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100 text-gray-700 cursor-not-allowed" placeholder="Enter unit size (e.g. 120 sqm)" readonly>
                            </div>
                        </div>
                        
                       

                    
                       </div>
            
                       <div class="bg-gray-50 p-4 rounded-md mb-6">
                        <label for="application_comment" class="block text-sm font-medium text-gray-700 mb-2">. Write any comment that will assist in processing the application</label>
                        <textarea id="application_comment" name="application_comment" rows="3" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Add any comments or notes here..."></textarea>
                      </div>
            
                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                        @php
                            $landUse = $motherApplication->land_use ?? 'Residential';
                            
                            // Set fees based on land use type for Unit applications
                            if ($landUse === 'Commercial' || $landUse === 'Industrial') {
                                $applicationFee = '10000.00';
                                $processingFee = '20000.00';
                                $surveyFee = '100000.00';
                            } else {
                                // Residential rates - default to Block of Flat rate
                                $applicationFee = '10000.00';
                                $processingFee = '20000.00';
                                $surveyFee = '50000.00'; // Default to Block of Flat rate
                            }
                            
                            $totalFee = floatval($applicationFee) + floatval($processingFee) + floatval($surveyFee);
                        @endphp

                        <h3 class="font-medium text-center mb-4">INITIAL BILL</h3>
                        
                        <div class="grid grid-cols-3 gap-4 mb-4">
                          <div>
                          <label class="flex items-center text-sm mb-1">
                            <i data-lucide="file-text" class="w-4 h-4 mr-1 text-green-600"></i>
                            Application fee (â‚¦)
                          </label>
                          <input type="text" name="application_fee" class="w-full p-2 border border-gray-300 rounded-md fee-input bg-blue-50" placeholder="Enter application fee" value="{{ number_format($applicationFee, 2) }}" readonly>
                          </div>
                          <div>
                          <label class="flex items-center text-sm mb-1">
                            <i data-lucide="file-check" class="w-4 h-4 mr-1 text-green-600"></i>
                            Processing fee (â‚¦)
                          </label>
                          <input type="text" name="processing_fee" class="w-full p-2 border border-gray-300 rounded-md fee-input bg-blue-50" placeholder="Enter processing fee" value="{{ number_format($processingFee, 2) }}" readonly>
                          </div>
                          <div>
                          <label class="flex items-center text-sm mb-1">
                            <i data-lucide="map" class="w-4 h-4 mr-1 text-green-600"></i>
                           Survey Fee (â‚¦)
                          </label>
                          @if($landUse === 'Residential')
                            <select name="site_plan_fee" class="w-full p-2 border border-gray-300 rounded-md fee-input bg-blue-50" onchange="updateSurveyFee(this)">
                              <option value="50000.00">Block of Flat - â‚¦50,000.00</option>
                              <option value="70000.00">Apartment - â‚¦70,000.00</option>
                            </select>
                          @else
                            <input type="text" name="site_plan_fee" class="w-full p-2 border border-gray-300 rounded-md fee-input bg-blue-50" placeholder="Enter survey fee" value="{{ number_format($surveyFee, 2) }}" readonly>
                          @endif
                          </div>
                        </div>
                        
                        <div class="flex justify-between items-center mb-4">
                          <div class="flex items-center">
                          <i data-lucide="file-text" class="w-4 h-4 mr-1 text-green-600"></i>
                          <span>Total:</span>
                          </div>
                          <span class="font-bold" id="total-amount">â‚¦{{ number_format($totalFee, 2) }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                          <div>
                          <label class="flex items-center text-sm mb-1">
                            <i data-lucide="calendar" class="w-4 h-4 mr-1 text-green-600"></i>
                            has been paid on <span class="text-red-500">*</span>
                          </label>
                            <input type="date" name="payment_date" class="w-full p-2 border border-gray-300 rounded-md" value="{{ date('Y-m-d') }}" required>
                          </div>
                          <div>
                          <label class="flex items-center text-sm mb-1">
                            <i data-lucide="receipt" class="w-4 h-4 mr-1 text-green-600"></i>
                            with receipt No. <span class="text-red-500">*</span>
                          </label>
                          <input type="text"  name="receipt_number" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter receipt number" required>
                          </div>
                        </div>
                        </div> 
                      
                      <div class="flex justify-between mt-8">
                        <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md" onclick="window.history.back()">Cancel</button>
                        <div class="flex items-center">
                          <span class="text-sm text-gray-500 mr-4">Step 1 of 4</span>
                          <button class="px-4 py-2 bg-black text-white rounded-md" id="nextStep1">Next</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            
                <!-- Step 2:shared areas -->
                @include('sectionaltitling.partials.subapplication.sharedareas')


                 <!-- Step 3: Application documents -->
                @include('sectionaltitling.partials.subapplication.documents')

                <!-- Step 4: Application Summary -->
                @include('sectionaltitling.partials.subapplication.summary')
               

              </div>
            </form>
        </div>
    </div>
 

<!-- Footer -->
@include('admin.footer')
</div>
</div>
{{-- Move the navigation script here, after all HTML content --}}
<script>
function toggleOtherAreasTextarea() {
  const checkbox = document.getElementById('other_areas');
  const container = document.getElementById('other_areas_container');
  if (checkbox && container) {
    if (checkbox.checked) {
      container.style.display = 'block';
    } else {
      container.style.display = 'none';
      const detail = document.getElementById('other_areas_detail');
      if (detail) detail.value = '';
    }
  }
}
document.addEventListener('DOMContentLoaded', function() {
  toggleOtherAreasTextarea();
});

// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
  if (window.lucide) lucide.createIcons();
});

// Step navigation and validation
function goToStep(stepNumber) {
  // Get current active step
  const currentActiveStep = document.querySelector('.form-section.active-tab');
  let currentStepNumber = 1;
  if (currentActiveStep) {
    const stepId = currentActiveStep.id;
    currentStepNumber = parseInt(stepId.replace('step', ''));
  }
  if (currentStepNumber === stepNumber) return;

  // If going forward, validate current step
  if (stepNumber > currentStepNumber) {
    let errors = [];
    switch (currentStepNumber) {
      case 1: errors = validateStep1(); break;
      case 2: errors = validateStep2(); break;
      case 3: errors = validateStep3(); break;
    }
    if (errors.length > 0) {
      showValidationErrors(errors);
      return;
    } else {
      Swal.fire({
        icon: 'success',
        title: `Step ${currentStepNumber} Complete!`,
        text: 'Validated successfully.',
        timer: 1200,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
    }
  }

  // Hide all steps
  document.querySelectorAll('.form-section').forEach(step => step.classList.remove('active-tab'));
  // Show target step
  const targetStep = document.getElementById(`step${stepNumber}`);
  if (targetStep) targetStep.classList.add('active-tab');
  updateStepCircles(stepNumber);
  updateStepText(stepNumber);
}
window.goToStep = goToStep;

function updateStepCircles(currentStep) {
  document.querySelectorAll('.step-circle').forEach((circle, idx) => {
    circle.classList.remove('active-tab', 'inactive-tab');
    circle.classList.add(idx + 1 === currentStep ? 'active-tab' : 'inactive-tab');
  });
}
function updateStepText(currentStep) {
  document.querySelectorAll('.ml-4').forEach(el => {
    if (el.textContent.includes('Step')) el.textContent = `Step ${currentStep} of 4`;
  });
}

// Navigation buttons
document.addEventListener('DOMContentLoaded', function() {
  const navs = [
    { btn: 'nextStep1', step: 2 },
    { btn: 'nextStep2', step: 3 },
    { btn: 'nextStep3', step: 4 },
    { btn: 'backStep2', step: 1 },
    { btn: 'backStep3', step: 2 },
    { btn: 'backStep4', step: 3 }
  ];
  navs.forEach(({ btn, step }) => {
    const el = document.getElementById(btn);
    if (el) el.addEventListener('click', function(e) {
      e.preventDefault();
      goToStep(step);
    });
  });
});

// Validation functions
function validateStep1() {
  const errors = [];
  const applicantType = document.querySelector('input[name="applicantType"]:checked');
  if (!applicantType) {
    errors.push('Please select an applicant type');
  } else {
    const type = applicantType.value;
    if (type === 'individual') {
      if (!document.getElementById('applicantTitle')?.value) errors.push('Please select a title');
      if (!document.getElementById('applicantName')?.value?.trim()) errors.push('Please enter first name');
      if (!document.getElementById('applicantSurname')?.value?.trim()) errors.push('Please enter surname');
      if (!document.getElementById('photoUpload')?.files[0]) errors.push('Please upload a passport photo');
    } else if (type === 'corporate') {
      if (!document.getElementById('corporateName')?.value?.trim()) errors.push('Please enter corporate body name');
      if (!document.getElementById('rcNumber')?.value?.trim()) errors.push('Please enter RC number');
      if (!document.getElementById('subCorporateDocumentUpload')?.files[0]) errors.push('Please upload RC document');
    } else if (type === 'multiple') {
      const ownerRows = document.querySelectorAll('#ownersContainer > div');
      if (ownerRows.length === 0) errors.push('Please add at least one owner');
      ownerRows.forEach((row, idx) => {
        if (!row.querySelector('input[name="multiple_owners_names[]"]')?.value?.trim())
          errors.push(`Please enter name for owner ${idx + 1}`);
        if (!row.querySelector('textarea[name="multiple_owners_address[]"]')?.value?.trim())
          errors.push(`Please enter address for owner ${idx + 1}`);
        if (!row.querySelector('input[name="multiple_owners_identification_image[]"]')?.files[0])
          errors.push(`Please upload identification for owner ${idx + 1}`);
      });
    }
  }
  // Address validation (individual/corporate)
  if (applicantType && applicantType.value !== 'multiple') {
    if (!document.getElementById('ownerState')?.value) errors.push('Please select a state');
    if (!document.getElementById('ownerLga')?.value) errors.push('Please select an LGA');
    if (!document.getElementById('ownerDistrict')?.value?.trim()) errors.push('Please enter district');
    // Phone validation
    const phoneInputs = document.querySelectorAll('input[name="phone_number[]"]');
    let hasValidPhone = false;
    phoneInputs.forEach(input => {
      if (input.value?.trim()) {
        hasValidPhone = true;
        if (!/^[\d\s\-\+\(\)]{10,}$/.test(input.value.replace(/\s/g, '')))
          errors.push('Please enter a valid phone number');
      }
    });
    if (!hasValidPhone) errors.push('Please enter at least one phone number');
    // Email validation
    const email = document.querySelector('input[name="owner_email"]')?.value;
    if (email?.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('Please enter a valid email address');
  }
  // Unit details
  if (!document.querySelector('input[name="block_number"]')?.value?.trim()) errors.push('Please enter block number');
  if (!document.querySelector('input[name="floor_number"]')?.value?.trim()) errors.push('Please enter floor number');
  return errors;
}
function validateStep2() {
  const errors = [];
  if (document.querySelectorAll('input[name="shared_areas[]"]:checked').length === 0)
    errors.push('Please select at least one shared area');
  const otherCheckbox = document.getElementById('other_areas');
  if (otherCheckbox?.checked) {
    const otherDetails = document.getElementById('other_areas_detail')?.value;
    if (!otherDetails?.trim()) errors.push('Please specify other shared areas');
  }
  return errors;
}
function validateStep3() {
  const errors = [];
  const requiredDocs = [
    { name: 'application_letter', label: 'Application Letter' },
    { name: 'building_plan', label: 'Building Plan' },
    { name: 'architectural_design', label: 'Architectural Design' },
    { name: 'ownership_document', label: 'Ownership Document' }
  ];
  requiredDocs.forEach(doc => {
    const fileInput = document.getElementById(doc.name);
    if (!fileInput?.files[0]) {
      errors.push(`Please upload ${doc.label}`);
    } else {
      const file = fileInput.files[0];
      if (file.size > 5 * 1024 * 1024) errors.push(`${doc.label} file size must be less than 5MB`);
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
      if (!allowedTypes.includes(file.type)) errors.push(`${doc.label} must be a JPG, PNG, or PDF file`);
    }
  });
  return errors;
}
function showValidationErrors(errors) {
  if (errors.length > 0) {
    Swal.fire({
      icon: 'error',
      title: 'Please correct the following errors:',
      html: `<div style="text-align: left; font-size: 14px; line-height: 1.6;">${errors.map(e => `• ${e}`).join('<br>')}</div>`,
      confirmButtonText: 'OK',
      confirmButtonColor: '#dc2626',
      customClass: {
        popup: 'swal-validation-popup',
        title: 'swal-validation-title',
        htmlContainer: 'swal-validation-content'
      },
      showClass: {
        popup: 'animate__animated animate__fadeInDown animate__faster'
      },
      hideClass: {
        popup: 'animate__animated animate__fadeOutUp animate__faster'
      }
    });
    return false;
  }
  return true;
}
window.validateStep2 = validateStep2;
window.validateStep3 = validateStep3;
window.showValidationErrors = showValidationErrors;
</script>
@endsection
