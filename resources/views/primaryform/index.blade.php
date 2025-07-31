@extends('layouts.app')
@section('page-title')
    {{ __('Primary Application Form') }}
@endsection


@include('sectionaltitling.partials.assets.css')

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
<style>
  
    .step-circle {
      width: 2rem;
      height: 2rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    .step-circle.active {
      background-color: #10b981;
      color: white;
    }
    .step-circle.inactive {
      background-color: #f3f4f6;
      color: #6b7280;
    }
    .step-circle:hover {
      transform: scale(1.1);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    .step-circle.active:hover {
      background-color: #059669;
    }
    .step-circle.inactive:hover {
      background-color: #e5e7eb;
      color: #374151;
    }
    .form-section {
      display: none;
    }
    .form-section.active {
      display: block;
    }
    .upload-box {
      border: 2px dashed #e5e7eb;
      border-radius: 0.375rem;
      padding: 1.5rem;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.2s;
    }
    .upload-box:hover {
      border-color: #3b82f6;
    }

    
    /* Loading overlay styles */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        display: none;
    }
    
    .loader {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

</style>
<div class="flex-1 overflow-auto">
    <!-- Header -->
   @include('admin.header')
    <!-- Dashboard Content -->
    <div class="p-6">
 

      <!-- Stats Cards -->
        
 
      <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
        <div class="modal-content">
            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="loading-overlay">
                <div class="loader"></div>
                <div class="text-white ml-3">Processing your request...</div>
            </div>
            
            <form id="primaryForm" method="POST" action="{{ route('primaryform.store') }}" enctype="multipart/form-data">
                @csrf
                <!-- Step 1: Basic Information -->

              
                <div class="form-section active" id="step1">
                  <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                      <h2 class="text-xl font-bold text-gray-800">MINISTRY OF LAND AND PHYSICAL PLANNING</h2>
                      <button type="button"  onclick="window.history.back()"  class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="x" class="w-5 h-5"></i>
                      </button>
                    </div>
                    
                    <div class="mb-6">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <i data-lucide="file-text" class="w-5 h-5 mr-2 text-green-600"></i>
                          <h3 class="text-lg font-bold">Application for Sectional Titling - Main Application</h3>
                        </div>
                        <div class="flex items-center">
                          <span class="text-gray-600 mr-2">Land Use:</span>
                          <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                            
                            @if (request()->query('landuse') === 'Commercial')
                              Commercial
                            @elseif (request()->query('landuse') === 'Residential')
                                Residential
                            @elseif (request()->query('landuse') === 'Industrial')
                                Industrial
                            @else
                                Mixed Use
                            @endif 
                          </span>
                        </div>
                      </div>
                      <p class="text-gray-600 mt-1">Complete the form below to submit a new primary application for sectional titling</p>
                    </div>
            
                    <div class="flex items-center mb-6">
                      <div class="flex items-center mr-4">
                        <div class="step-circle active flex items-center justify-center cursor-pointer" onclick="goToStep(1)">1</div>
                      </div>
                      <div class="flex items-center mr-4">
                        <div class="step-circle inactive flex items-center justify-center cursor-pointer" onclick="goToStep(2)">2</div>
                      </div>
                      <div class="flex items-center mr-4">
                        <div class="step-circle inactive flex items-center justify-center cursor-pointer" onclick="goToStep(3)">3</div>
                      </div>
                      <div class="flex items-center mr-4">
                        <div class="step-circle inactive flex items-center justify-center cursor-pointer" onclick="goToStep(4)">4</div>
                      </div>
                       <div class="flex items-center">
                     <div class="step-circle inactive cursor-pointer" onclick="goToStep(5)">5</div>
                   </div>
                      <div class="ml-4">Step 1 0f 5</div>
                    </div>
            
                    <div class="mb-6">
                      <div class="text-right text-sm text-gray-500">CODE: ST FORM - 1</div>
                      <hr class="my-4">
                      
                      <div class="grid grid-cols-3 gap-6 mb-6">
                        <!-- Left column (2/3 width) -->
                        <div class="col-span-2">
                          <div class="mb-6">
                            <label class="block mb-2 font-medium">Applicant Type</label>
                            <div class="flex space-x-6">
                              <label class="flex items-center">
                                <input type="radio" name="applicantType" class="mr-2" value="individual" required onchange="handleApplicantTypeChange('individual')">
                                <span>Individual</span>
                              </label>
                              <label class="flex items-center">
                                <input type="radio" name="applicantType" class="mr-2" value="corporate" onchange="handleApplicantTypeChange('corporate')">
                                <span>Corporate Body</span>
                              </label>
                              <label class="flex items-center">
                                <input type="radio" name="applicantType" class="mr-2" value="multiple" onchange="handleApplicantTypeChange('multiple')">
                                <span>Multiple Owners</span>
                              </label>
                            </div>
                          </div>
                          <input type="hidden" name="land_use" value="{{ request()->query('landuse') === 'Commercial' ? 'Commercial' : (request()->query('landuse') === 'Residential' ? 'Residential' : (request()->query('landuse') === 'Industrial' ? 'Industrial' : 'Mixed Use')) }}">
                          
                          <!-- NP FileNo Display Section -->
                          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                              <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-full mr-4">
                                  <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                                </div>
                                <div>
                                  <h3 class="text-lg font-semibold text-gray-900">New Primary FileNo (NPFN)</h3>
                                  <p class="text-sm text-gray-600">Auto-generated file number for this application</p>
                                </div>
                              </div>
                              <div class="bg-green-100 px-3 py-1 rounded-full">
                                <span class="text-green-800 text-sm font-medium">Auto-Generated</span>
                              </div>
                            </div>
                            
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                  <label class="block text-sm font-medium text-gray-700 mb-2">NP FileNo (NPFN)</label>
                                  <div class="relative">
                                    <input type="text" 
                                           class="w-full px-4 py-3 bg-blue-50 border-2 border-blue-200 rounded-lg text-blue-900 font-mono text-lg font-semibold cursor-not-allowed" 
                                           value="{{ $npFileNo ?? 'ST-RES-'.date('Y').'-01' }}" 
                                           readonly
                                           title="New Primary FileNo - Auto Generated">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                      <i data-lucide="lock" class="w-5 h-5 text-blue-500"></i>
                                    </div>
                                  </div>
                                  <p class="text-xs text-gray-500 mt-1">Format: ST-LandUse-Year-SerialNo</p>
                                </div>
                                
                                <div>
                                  <label class="block text-sm font-medium text-gray-700 mb-2">File Number Components</label>
                                  <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                      <span class="text-gray-600">Prefix:</span>
                                      <span class="font-medium">ST (Sectional Title)</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                      <span class="text-gray-600">Land Use:</span>
                                      <span class="font-medium">
                                        @if (request()->query('landuse') === 'Commercial')
                                          COM (Commercial)
                                        @elseif (request()->query('landuse') === 'Residential')
                                          RES (Residential)
                                        @elseif (request()->query('landuse') === 'Industrial')
                                          IND (Industrial)
                                        @else
                                          RES (Residential)
                                        @endif
                                      </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                      <span class="text-gray-600">Year:</span>
                                      <span class="font-medium">{{ $currentYear ?? date('Y') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                      <span class="text-gray-600">Serial No:</span>
                                      <span class="font-medium">{{ $serialNo ?? '01' }}</span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              
                              <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                  <i data-lucide="info" class="w-4 h-4 text-blue-500 mr-2"></i>
                                  <p class="text-sm text-gray-700">
                                    This NP FileNo will be used as the primary identifier for your application and all associated unit applications.
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>

                          @include('primaryform.fileno')
                            
                        </div>
                        </div>
     
 
                   
                      </div>
            
                     @include('primaryform.applicant')
    
                      <div class="bg-gray-50 p-4 rounded-md mb-6" id="mainOwnerAddressSection">
                 
                        
                        <div class="mb-4">
                        <p class="text-sm mb-1">Owner's Address</p>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                          <div>
                            <label class="block text-sm mb-1">House No.</label>
                            <input type="text" id="ownerHouseNo" class="w-full p-2 border border-gray-300 rounded-md" placeholder="HOUSE NO." name="address_house_no" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase(); updateAddressDisplay();">
                          </div>
                          <div>
                            <label class="block text-sm mb-1">Street Name</label>
                            <input type="text" id="ownerStreetName" class="w-full p-2 border border-gray-300 rounded-md" placeholder="STREET NAME" name="owner_street_name" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase(); updateAddressDisplay();">
                          </div>
                        </div>
              
                        <div class="grid grid-cols-3 gap-4 mb-4">
                          <div>
                            <label class="block text-sm mb-1">District</label>
                            <input type="text" id="ownerDistrict" class="w-full p-2 border border-gray-300 rounded-md" placeholder="DISTRICT" name="owner_district" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase(); updateAddressDisplay();">
                          </div>
                          <div>
                            <label class="block text-sm mb-1">LGA <span class="text-red-500">*</span></label>
                            <select id="ownerLga" class="w-full p-2 border border-gray-300 rounded-md" name="owner_lga" required onchange="updateAddressDisplay();">
                              <option value="">SELECT LGA</option>
                            </select>
                          </div>
                          <div>
                            <label class="block text-sm mb-1">State <span class="text-red-500">*</span></label>
                            <select id="ownerState" class="w-full p-2 border border-gray-300 rounded-md" name="owner_state" required onchange="selectOwnerLGA(this); updateAddressDisplay();">
                              <option value="">SELECT STATE</option>
                            </select>
                          </div>
                        </div>
                        <input type="hidden" name="address" id="contactAddressDisplay">    
                        <div class="mb-4">
                          <label class="block text-sm mb-1">Contact Address:</label>
                            <div id="contactAddressPreview" class="p-2 bg-white border border-gray-300 rounded-md min-h-[40px]">
                            <span id="fullContactAddress" style="display: block; padding: 4px; text-transform: uppercase;"></span>
                            </div>
                          <input type="hidden" name="address" id="contactAddressDisplay">
                        </div>
               
                        <div class="grid grid-cols-2 gap-4 mb-4">
                          <div>
                            <label class="block text-sm mb-1">Phone No. 1 <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded-md" placeholder="ENTER PHONE NUMBER" name="phone_number[]" required style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase();">
                          </div>
                          <div>
                            <label class="block text-sm mb-1">Phone No. 2</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded-md" placeholder="ENTER ALTERNATE PHONE" name="phone_number[]" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase();">
                          </div>
                        </div>
                          
                          <div>
                            <label class="block text-sm mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter email address" name="owner_email" required>
                          </div>
                        </div>
                      </div>
    
                      <div class="grid grid-cols-2 gap-6 mb-6" id="mainOwnerIdentificationSection">
                        <!-- Left column -->
                        <div id="meansOfIdentificationSection">
                          <label class="block mb-2 font-medium">Means of identification <span class="text-red-500">*</span></label>
                          <div class="grid grid-cols-1 gap-2">
                            <label class="flex items-center">
                              <input type="radio" name="idType" class="mr-2" value="national_id" required checked>
                              <span>National ID</span>
                            </label>
                            <label class="flex items-center">
                              <input type="radio" name="idType" class="mr-2" value="drivers_license" required>
                              <span>Driver's License</span>
                            </label>
                            <label class="flex items-center">
                              <input type="radio" name="idType" class="mr-2" value="voters_card" required>
                              <span>Voter's Card</span>
                            </label>
                            <label class="flex items-center">
                              <input type="radio" name="idType" class="mr-2" value="international_passport" required>
                              <span>International Passport</span>
                            </label>
                            <label class="flex items-center">
                              <input type="radio" name="idType" class="mr-2" value="others" required>
                              <span>Others</span>
                            </label>
                          </div>
                        </div>
                        
                        <!-- Right column - ID Document Upload -->
                        <div>
                          <label class="block mb-2 font-medium" id="uploadDocumentLabel">Upload ID Document <span class="text-red-500">*</span></label>
                          <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div id="idDocumentPlaceholder" class="flex flex-col items-center justify-center">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                              </svg>
                              <p class="text-sm text-gray-600 mb-1" id="uploadDocumentText">Click to upload ID document</p>
                              <p class="text-xs text-gray-500">JPG, PNG, PDF (max. 5MB)</p>
                            </div>
                            <img id="idDocumentPreview" class="hidden w-full h-32 object-cover rounded-md mt-2" src="#" alt="ID Document Preview">
                            <div id="idDocumentInfo" class="hidden mt-2 text-sm text-gray-600"></div>
                            <input type="file" id="idDocumentUpload" name="id_document" accept="image/*,.pdf" class="hidden" required onchange="previewIdDocument(event)">
                            <button type="button" id="removeIdDocumentBtn" class="hidden mt-2 px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600" onclick="removeIdDocument()">Remove</button>
                          </div>
                          <div class="mt-2">
                            <button type="button" onclick="document.getElementById('idDocumentUpload').click()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                              Choose File
                            </button>
                          </div>
                        </div>
                      </div>
            
                     


                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                        <h3 class="font-medium mb-4">Property Details</h3>
                        
                        @include('primaryform.types.commercial')
                        @include('primaryform.types.residential')
                        @include('primaryform.types.industrial')
                        
                        <div class="grid grid-cols-4 gap-4 mb-4">
                          <div>
                          <label class="block text-sm mb-1">No. of Units <span class="text-red-500">*</span></label>
                          <input type="number" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter number of units" name="units_count" required min="1">
                          </div>
                          <div>
                          <label class="block text-sm mb-1">No. of Blocks <span class="text-red-500">*</span></label>
                          <input type="number" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter number of blocks" name="blocks_count" required min="1">
                          </div>
                          <div>
                          <label class="block text-sm mb-1">No. of Sections (Floors) <span class="text-red-500">*</span></label>
                          <input type="number" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter number of floors" name="sections_count" required min="1">
                          </div>
                          <div>
                          <label class="block text-sm mb-1">Plot Size</label>
                          <input type="text" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter plot size" name="plot_size" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase();">
                          </div>
                        </div>
                        
                        <h4 class="font-medium mb-2 mt-4">Property Address</h4>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                          <div>
                          <label class="block text-sm mb-1">House No. <span class="text-red-500">*</span></label>
                          <input type="text" id="propertyHouseNo" class="w-full p-2 border border-gray-300 rounded-md" placeholder="ENTER HOUSE NUMBER" name="property_house_no" required style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase(); updatePropertyAddressDisplay();">
                          </div>
                          <div>
                          <label class="block text-sm mb-1">Plot No.</label>
                          <input type="text" id="propertyPlotNo" class="w-full p-2 border border-gray-300 rounded-md" placeholder="ENTER PLOT NUMBER" name="property_plot_no" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase(); updatePropertyAddressDisplay();">
                          </div>
                          <div>
                          <label class="block text-sm mb-1">Street Name <span class="text-red-500">*</span></label>
                          <input type="text" id="propertyStreetName" class="w-full p-2 border border-gray-300 rounded-md" placeholder="ENTER STREET NAME" name="property_street_name" required style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase(); updatePropertyAddressDisplay();">
                          </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                          <div>
                          <label class="block text-sm mb-1">District</label>
                          <input type="text" id="propertyDistrict" class="w-full p-2 border border-gray-300 rounded-md" placeholder="ENTER DISTRICT" name="property_district" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase(); updatePropertyAddressDisplay();">
                          </div>
                          <div>
                            <label class="block text-sm mb-1">LGA <span class="text-red-500">*</span></label>
                            <select id="propertyLga" name="property_lga" class="w-full p-2 border border-gray-300 rounded-md" required onchange="updatePropertyAddressDisplay();">
                                <option value="">Select LGA</option>
                            </select>
                          </div>
                          <div>
                          <label class="block text-sm mb-1">State <span class="text-red-500">*</span></label>
                          <select id="propertyState" class="w-full p-2 border border-gray-300 rounded-md" name="property_state" required onchange="selectPropertyLGA(this); updatePropertyAddressDisplay();">
                              <option value="">Select State</option>
                          </select>
                          </div>
                        </div>
                        <!-- Property Address Preview and Hidden Input -->
                        <div class="mb-4">
                          <label class="block text-sm mb-1">Property Address:</label>
                          <div id="propertyAddressPreview" class="p-2 bg-white border border-gray-300 rounded-md min-h-[40px]">
                            <span id="fullPropertyAddress" style="display: block; padding: 4px; text-transform: uppercase;"></span>
                          </div>
                          <input type="hidden" name="property_address" id="propertyAddressDisplay">
                        </div>
                        </div>
                      @include('primaryform.types.ownership')
                      <div class="mb-4">
                        <label class="block text-sm mb-1">Write any comments that will assist in processing the application</label>
                        <textarea class="w-full p-2 border border-gray-300 rounded-md" rows="4" placeholder="Enter any additional comments or information" name="comments"></textarea>
                      </div>
                      @include('primaryform.initial_bill')
                   
                
                      <div class="flex justify-between mt-8">
                        <button type="button" onclick="window.history.back()" class="px-4 py-2 bg-white border border-gray-300 rounded-md">Cancel</button>
                        <div class="flex items-center">
                          <span class="text-sm text-gray-500 mr-4">Step 1 of 5</span>
                          <button class="px-4 py-2 bg-black text-white rounded-md" id="nextStep1">Next</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @include('primaryform.sharedareas')
              @include('primaryform.documents')
              @include('primaryform.buyer_list')
              @include('primaryform.summary')
            </form>
          </div>
      </div>
    </div>

    <!-- Footer -->
    @include('admin.footer')
  </div>

<!-- Print Template (Hidden) -->
@include('primaryform.print')
@include('primaryform.js')

<script>
    // Global function to update address display (accessible from other functions)
    function updateAddressDisplay() {
        const ownerHouseNo = document.getElementById('ownerHouseNo');
        const ownerStreetName = document.getElementById('ownerStreetName');
        const ownerDistrict = document.getElementById('ownerDistrict');
        const ownerLga = document.getElementById('ownerLga');
        const ownerState = document.getElementById('ownerState');
        
        const fullContactAddress = document.getElementById('fullContactAddress');
        const contactAddressDisplay = document.getElementById('contactAddressDisplay');
        
        if (!fullContactAddress || !contactAddressDisplay) {
            return;
        }
        
        const houseNo = ownerHouseNo?.value || '';
        const streetName = ownerStreetName?.value || '';
        const district = ownerDistrict?.value || '';
        const lga = ownerLga?.value || '';
        const state = ownerState?.value || '';
        
        const fullAddress = [houseNo, streetName, district, lga, state]
            .filter(part => part.trim() !== '')
            .join(', ');
        
        console.log('New address value:', fullAddress);
        
        // Update both elements
        if (fullContactAddress) {
            fullContactAddress.textContent = fullAddress || '';
        }
        
        if (contactAddressDisplay) {
            contactAddressDisplay.value = fullAddress;
        }
    }

    // Property address concatenation logic
    function updatePropertyAddressDisplay() {
        const houseNo = document.getElementById('propertyHouseNo')?.value.trim();
        const plotNo = document.getElementById('propertyPlotNo')?.value.trim();
        const streetName = document.getElementById('propertyStreetName')?.value.trim();
        const district = document.getElementById('propertyDistrict')?.value.trim();
        const lga = document.getElementById('propertyLga')?.value.trim();
        const state = document.getElementById('propertyState')?.value.trim();

        let addressParts = [];
        
        // Priority logic: Use House No if present (ignore Plot No), else use Plot No if present
        if (houseNo) {
            addressParts.push(houseNo);
        } else if (plotNo) {
            addressParts.push(plotNo);
        }
        
        // Add remaining address components in order: Streetname, District, LGA, State
        if (streetName) addressParts.push(streetName);
        if (district) addressParts.push(district);
        if (lga) addressParts.push(lga);
        if (state) addressParts.push(state);

        const fullAddress = addressParts.join(', ');

        const fullPropertyAddress = document.getElementById('fullPropertyAddress');
        const propertyAddressDisplay = document.getElementById('propertyAddressDisplay');
        
        if (fullPropertyAddress) {
            fullPropertyAddress.textContent = fullAddress || '';
        }
        if (propertyAddressDisplay) {
            propertyAddressDisplay.value = fullAddress;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('primaryForm');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show SweetAlert loading message
            Swal.fire({
                title: 'Submitting Application...',
                html: 'Please wait while we process your application.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Wait for 3 seconds before submitting the form
            setTimeout(function() {
                form.submit();
            }, 3000);
        });
    });

    // Direct script to handle contact address updates
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Setting up direct address handler');
        
        // Get address input fields
        const ownerHouseNo = document.getElementById('ownerHouseNo');
        const ownerStreetName = document.getElementById('ownerStreetName');
        const ownerDistrict = document.getElementById('ownerDistrict');
        const ownerLga = document.getElementById('ownerLga');
        const ownerState = document.getElementById('ownerState');
        
        console.log('Address elements found:', {
            'ownerHouseNo': !!ownerHouseNo,
            'ownerStreetName': !!ownerStreetName,
            'ownerLga': !!ownerLga,
            'ownerState': !!ownerState
        });
        
        // Add event listeners directly
        [ownerHouseNo, ownerStreetName, ownerDistrict, ownerLga, ownerState].forEach(field => {
            if (field) {
                field.addEventListener('input', updateAddressDisplay);
                field.addEventListener('change', updateAddressDisplay);
            }
        });
        
        // Run initial update
        updateAddressDisplay();
        
        // Initialize states and LGAs
        initializeStatesAndLGAs();
    });

    // Fetch all States and initialize dropdowns
    function initializeStatesAndLGAs() {
        console.log('Initializing states and LGAs...');
        
        fetch('https://nga-states-lga.onrender.com/fetch')
            .then((res) => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then((data) => {
                console.log('States data received:', data);
                
                // Populate owner state dropdown
                var ownerStateSelect = document.getElementById("ownerState");
                if (ownerStateSelect) {
                    ownerStateSelect.innerHTML = '<option value="">SELECT STATE</option>';
                    
                    if (Array.isArray(data)) {
                        data.forEach(function(state) {
                            var option = document.createElement("option");
                            option.text = state;
                            option.value = state;
                            ownerStateSelect.add(option);
                        });
                    } else {
                        for (let index = 0; index < Object.keys(data).length; index++) {
                            var option = document.createElement("option");
                            option.text = data[index];
                            option.value = data[index];
                            ownerStateSelect.add(option);
                        }
                    }
                }
                
                // Populate property state dropdown
                var propertyStateSelect = document.getElementById("propertyState");
                if (propertyStateSelect) {
                    propertyStateSelect.innerHTML = '<option value="">Select State</option>';
                    
                    if (Array.isArray(data)) {
                        data.forEach(function(state) {
                            var option = document.createElement("option");
                            option.text = state;
                            option.value = state;
                            propertyStateSelect.add(option);
                        });
                    } else {
                        for (let index = 0; index < Object.keys(data).length; index++) {
                            var option = document.createElement("option");
                            option.text = data[index];
                            option.value = data[index];
                            propertyStateSelect.add(option);
                        }
                    }
                    
                    // Set Kano as default for property state and load its LGAs
                    propertyStateSelect.value = "Kano";
                    selectPropertyLGA(propertyStateSelect);
                }
                
                console.log('States loaded successfully');
            })
            .catch((error) => {
                console.error('Error fetching states:', error);
                
                // Use backup data if API fails
                console.log('API failed, using backup states data...');
                const backupStates = ['Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'];
                
                // Populate owner state dropdown with backup data
                var ownerStateSelect = document.getElementById("ownerState");
                if (ownerStateSelect) {
                    ownerStateSelect.innerHTML = '<option value="">SELECT STATE</option>';
                    backupStates.forEach(function(state) {
                        var option = document.createElement("option");
                        option.text = state;
                        option.value = state;
                        ownerStateSelect.add(option);
                    });
                }
                
                // Populate property state dropdown with backup data
                var propertyStateSelect = document.getElementById("propertyState");
                if (propertyStateSelect) {
                    propertyStateSelect.innerHTML = '<option value="">Select State</option>';
                    backupStates.forEach(function(state) {
                        var option = document.createElement("option");
                        option.text = state;
                        option.value = state;
                        propertyStateSelect.add(option);
                    });
                    
                    // Set Kano as default for property state
                    propertyStateSelect.value = "Kano";
                    // Load backup LGAs for Kano
                    loadBackupLGAsForKano();
                }
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Using Offline Data',
                    text: 'Network connection issue detected. Using offline state data. LGA options may be limited.',
                    toast: true,
                    position: 'top-end',
                    timer: 5000,
                    showConfirmButton: false
                });
            });
    }

    // Function to load backup LGAs for Kano (most common LGAs)
    function loadBackupLGAsForKano() {
        const propertyLgaSelect = document.getElementById("propertyLga");
        if (propertyLgaSelect) {
            propertyLgaSelect.innerHTML = '<option value="">Select LGA</option>';
            const kanoLGAs = ['Dala', 'Fagge', 'Gwale', 'Kano Municipal', 'Kumbotso', 'Nasarawa', 'Tarauni', 'Ungogo'];
            kanoLGAs.forEach(function(lga) {
                var option = document.createElement("option");
                option.text = lga;
                option.value = lga;
                propertyLgaSelect.add(option);
            });
        }
    }

    // Fetch Local Governments based on selected state for owner address
    function selectOwnerLGA(target) {
        var state = target.value;
        if (!state) {
            // Clear LGA dropdown if no state selected
            var lgaSelect = document.getElementById("ownerLga");
            if (lgaSelect) {
                lgaSelect.innerHTML = '<option value="">SELECT LGA</option>';
                // Trigger address update
                updateAddressDisplay();
            }
            return;
        }
        
        // Show loading state
        var lgaSelect = document.getElementById("ownerLga");
        if (lgaSelect) {
            lgaSelect.innerHTML = '<option value="">Loading LGAs...</option>';
            lgaSelect.disabled = true;
        }
        
        fetch('https://nga-states-lga.onrender.com/?state=' + encodeURIComponent(state))
            .then((res) => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then((data) => {
                var lgaSelect = document.getElementById("ownerLga");
                if (!lgaSelect) return;
                
                // Clear existing options
                lgaSelect.innerHTML = '';
                lgaSelect.disabled = false;
                
                // Add default option
                var defaultOption = document.createElement("option");
                defaultOption.text = "SELECT LGA";
                defaultOption.value = "";
                lgaSelect.add(defaultOption);
                
                // Add LGA options
                if (Array.isArray(data)) {
                    data.forEach(function(lga) {
                        var option = document.createElement("option");
                        option.text = lga;
                        option.value = lga;
                        lgaSelect.add(option);
                    });
                } else {
                    // Handle object format
                    for (let index = 0; index < Object.keys(data).length; index++) {
                        var option = document.createElement("option");
                        option.text = data[index];
                        option.value = data[index];
                        lgaSelect.add(option);
                    }
                }
                
                // Trigger address update after LGAs are loaded
                updateAddressDisplay();
            })
            .catch((error) => {
                console.error('Error fetching LGAs:', error);
                var lgaSelect = document.getElementById("ownerLga");
                if (lgaSelect) {
                    lgaSelect.innerHTML = '<option value="">SELECT LGA</option>';
                    lgaSelect.disabled = false;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Failed to load LGAs for ' + state + '. Please check your internet connection and try again.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
    }

    // Fetch Local Governments based on selected state for property address
    function selectPropertyLGA(target) {
        var state = target.value;
        if (!state) {
            // Clear LGA dropdown if no state selected
            var lgaSelect = document.getElementById("propertyLga");
            if (lgaSelect) {
                lgaSelect.innerHTML = '<option value="">Select LGA</option>';
            }
            return;
        }
        
        // Show loading state
        var lgaSelect = document.getElementById("propertyLga");
        if (lgaSelect) {
            lgaSelect.innerHTML = '<option value="">Loading LGAs...</option>';
            lgaSelect.disabled = true;
        }
        
        fetch('https://nga-states-lga.onrender.com/?state=' + encodeURIComponent(state))
            .then((res) => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then((data) => {
                var lgaSelect = document.getElementById("propertyLga");
                if (!lgaSelect) return;
                
                // Clear existing options
                lgaSelect.innerHTML = '';
                lgaSelect.disabled = false;
                
                // Add default option
                var defaultOption = document.createElement("option");
                defaultOption.text = "Select LGA";
                defaultOption.value = "";
                lgaSelect.add(defaultOption);
                
                // Add LGA options
                if (Array.isArray(data)) {
                    data.forEach(function(lga) {
                        var option = document.createElement("option");
                        option.text = lga;
                        option.value = lga;
                        lgaSelect.add(option);
                    });
                } else {
                    // Handle object format
                    for (let index = 0; index < Object.keys(data).length; index++) {
                        var option = document.createElement("option");
                        option.text = data[index];
                        option.value = data[index];
                        lgaSelect.add(option);
                    }
                }
            })
            .catch((error) => {
                console.error('Error fetching LGAs:', error);
                var lgaSelect = document.getElementById("propertyLga");
                if (lgaSelect) {
                    lgaSelect.innerHTML = '<option value="">Select LGA</option>';
                    lgaSelect.disabled = false;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Failed to load LGAs for ' + state + '. Please check your internet connection and try again.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
    }

    // ID Document upload preview functionality
    function previewIdDocument(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            const placeholder = document.getElementById('idDocumentPlaceholder');
            const preview = document.getElementById('idDocumentPreview');
            const info = document.getElementById('idDocumentInfo');
            const removeBtn = document.getElementById('removeIdDocumentBtn');

            // Validate file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Please select a file smaller than 5MB.',
                    confirmButtonColor: '#dc3545'
                });
                event.target.value = '';
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select a JPG, PNG, or PDF file.',
                    confirmButtonColor: '#dc3545'
                });
                event.target.value = '';
                return;
            }

            if (file.type === 'application/pdf') {
                // For PDF files, show file info instead of preview
                placeholder.classList.add('hidden');
                preview.classList.add('hidden');
                info.classList.remove('hidden');
                info.innerHTML = `
                    <div class="flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-medium">${file.name}</p>
                            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                `;
                removeBtn.classList.remove('hidden');
            } else {
                // For image files, show preview
                reader.onload = function(e) {
                    placeholder.classList.add('hidden');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    info.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }

            // Show success toast
            Swal.fire({
                icon: 'success',
                title: 'File Uploaded',
                text: `${file.name} has been selected successfully.`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    }

    // Remove ID document functionality
    function removeIdDocument() {
        const upload = document.getElementById('idDocumentUpload');
        const placeholder = document.getElementById('idDocumentPlaceholder');
        const preview = document.getElementById('idDocumentPreview');
        const info = document.getElementById('idDocumentInfo');
        const removeBtn = document.getElementById('removeIdDocumentBtn');

        upload.value = '';
        preview.src = '#';
        preview.classList.add('hidden');
        info.classList.add('hidden');
        placeholder.classList.remove('hidden');
        removeBtn.classList.add('hidden');

        Swal.fire({
            icon: 'info',
            title: 'File Removed',
            text: 'ID document has been removed.',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    // Show/hide main owner address and identification based on applicant type
    function toggleMainOwnerSections() {
        const applicantType = document.querySelector('input[name="applicantType"]:checked')?.value;
        const addressSection = document.getElementById('mainOwnerAddressSection');
        const idSection = document.getElementById('mainOwnerIdentificationSection');

        // Remove required attributes if multiple owners
        const mainOwnerFields = [
            'ownerHouseNo', 'ownerStreetName', 'ownerDistrict', 'ownerLga', 'ownerState',
            'idDocumentUpload', 'owner_email'
        ];
        const phoneInputs = document.querySelectorAll('input[name="phone_number[]"]');
        if (applicantType === 'multiple') {
            if (addressSection) addressSection.style.display = 'none';
            if (idSection) idSection.style.display = 'none';
            mainOwnerFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.removeAttribute('required');
            });
            phoneInputs.forEach(el => el.removeAttribute('required'));
        } else {
            if (addressSection) addressSection.style.display = '';
            if (idSection) idSection.style.display = '';
            mainOwnerFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.setAttribute('required', 'required');
            });
            if (phoneInputs.length > 0) phoneInputs[0].setAttribute('required', 'required');
        }
    }

    // Function to update UI based on applicant type
    function updateIdentificationSection() {
        const applicantType = document.querySelector('input[name="applicantType"]:checked')?.value;
        const identificationSection = document.getElementById('mainOwnerIdentificationSection');

        if (applicantType === 'corporate') {
            // Hide the entire identification section for corporate body
            if (identificationSection) {
                identificationSection.style.display = 'none';
            }
        } else {
            // Show identification section for individual and multiple owners
            if (identificationSection) {
                identificationSection.style.display = 'grid';
            }
        }
    }

    // Main handler for applicant type changes
    function handleApplicantTypeChange(type) {
        // Set the applicant type in the hidden field
        if (typeof setApplicantType === 'function') {
            setApplicantType(type);
        }
        
        // Show the appropriate fields from applicant.blade.php
        if (type === 'individual' && typeof showIndividualFields === 'function') {
            showIndividualFields();
        } else if (type === 'corporate' && typeof showCorporateFields === 'function') {
            showCorporateFields();
        } else if (type === 'multiple' && typeof showMultipleOwnersFields === 'function') {
            showMultipleOwnersFields();
        }
        
        // Update main form sections
        toggleMainOwnerSections();
        updateIdentificationSection();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="applicantType"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                handleApplicantTypeChange(this.value);
            });
        });
        
        // Initial toggle on page load
        toggleMainOwnerSections();
        updateIdentificationSection();
        
        // Check if there's a pre-selected applicant type and trigger the change
        const selectedApplicantType = document.querySelector('input[name="applicantType"]:checked');
        if (selectedApplicantType) {
            handleApplicantTypeChange(selectedApplicantType.value);
        }
    });
</script>

@endsection