@php
    $is_ai = $is_ai_assistant ?? false;
@endphp

@if(!$is_ai)
<div id="property-form-dialog" class="dialog-overlay hidden" >
    <div class="dialog-content property-form-content">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Add New Property</h2>
            <button id="close-property-form" class="text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
@endif

<form id="property-record-form" action="{{ route('property-records.store') }}" method="POST" x-data="propertyRecordForm()">
    @csrf
    <input type="hidden" name="property_id" id="property_id" value="">
    <input type="hidden" name="action" id="action" value="add">
    <div class="space-y-4 py-2 @if(!$is_ai) max-h-[75vh] overflow-y-auto pr-1 @endif">
        <!-- Top section with two columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Left column - Title Type Section -->
            <div class="form-section">
                <h4 class="form-section-title">Property Type Information</h4>
                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="text-sm">Title Type</label>
                        <div class="flex space-x-4">
                            <div class="flex items-center space-x-1">
                                <input type="radio" id="customary" name="titleType" value="Customary" checked>
                                <label for="customary" class="text-sm">Customary</label>
                            </div>
                            <div class="flex items-center space-x-1">
                                <input type="radio" id="statutory" name="titleType" value="Statutory">
                                <label for="statutory" class="text-sm">Statutory</label>
                            </div>
                        </div>
                    </div>

                    <!-- File Number -->
                   <div class="space-y-1">
                      @include('propertycard.partials.manual_fileno')
                    </div>
                </div>
            </div>
            
            <!-- Right column - Property Description -->
            <div class="form-section">
                <h4 class="form-section-title">Property Description</h4>
                <div class="space-y-3">
                    <!-- House No and Plot No -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="houseNo" class="text-xs text-gray-600">House No</label>
                            <input id="houseNo" name="house_no" x-model="house" type="text" class="form-input text-sm property-input">
                        </div>
                        <div>
                            <label for="plotNo" class="text-xs text-gray-600">Plot No.</label>
                            <input id="plotNo" name="plot_no" x-model="plot" type="text" class="form-input text-sm property-input" placeholder="Enter plot number">
                        </div>
                    </div>
                    <!-- Street Name and District/Neighbourhood -->
                    <div class="grid grid-cols-2 gap-3">
                        @include('components.StreetName2')
                        <div>
                            <label for="district" class="text-xs text-gray-600">District/Neighbourhood</label>
                            <input id="district" name="district" x-model="district" type="text" class="form-input text-sm property-input" placeholder="Enter district or neighbourhood">
                        </div>
                    
                    <div>
                        <label for="lga" class="text-xs text-gray-600">LGA</label>
                        <select id="lga" name="lgsaOrCity" x-model="lga" class="form-input text-sm property-input">
                            <option value="">Select LGA</option>
                            <option value="Ajingi">Ajingi</option>
                            <option value="Albasu">Albasu</option>
                            <option value="Bagwai">Bagwai</option>
                            <option value="Bebeji">Bebeji</option>
                            <option value="Bichi">Bichi</option>
                            <option value="Bunkure">Bunkure</option>
                            <option value="Dala">Dala</option>
                            <option value="Dambatta">Dambatta</option>
                            <option value="Dawakin Kudu">Dawakin Kudu</option>
                            <option value="Dawakin Tofa">Dawakin Tofa</option>
                            <option value="Doguwa">Doguwa</option>
                            <option value="Fagge">Fagge</option>
                            <option value="Gabasawa">Gabasawa</option>
                            <option value="Garko">Garko</option>
                            <option value="Garun Mallam">Garun Mallam</option>
                            <option value="Gaya">Gaya</option>
                            <option value="Gezawa">Gezawa</option>
                            <option value="Gwale">Gwale</option>
                            <option value="Gwarzo">Gwarzo</option>
                            <option value="Kabo">Kabo</option>
                            <option value="Kano Municipal">Kano Municipal</option>
                            <option value="Karaye">Karaye</option>
                            <option value="Kibiya">Kibiya</option>
                            <option value="Kiru">Kiru</option>
                            <option value="Kumbotso">Kumbotso</option>
                            <option value="Kunchi">Kunchi</option>
                            <option value="Kura">Kura</option>
                            <option value="Madobi">Madobi</option>
                            <option value="Makoda">Makoda</option>
                            <option value="Minjibir">Minjibir</option>
                            <option value="Nasarawa">Nasarawa</option>
                            <option value="Rano">Rano</option>
                            <option value="Rimin Gado">Rimin Gado</option>
                            <option value="Rogo">Rogo</option>
                            <option value="Shanono">Shanono</option>
                            <option value="Sumaila">Sumaila</option>
                            <option value="Takai">Takai</option>
                            <option value="Tarauni">Tarauni</option>
                            <option value="Tofa">Tofa</option>
                            <option value="Tsanyawa">Tsanyawa</option>
                            <option value="Tudun Wada">Tudun Wada</option>
                            <option value="Ungogo">Ungogo</option>
                            <option value="Warawa">Warawa</option>
                            <option value="Wudil">Wudil</option>
                        </select>
                    </div>
                     
                    <!-- State -->
                    <div>
                        <label for="state" class="text-xs text-gray-600">State</label>
                        <input id="state" name="state" x-model="state" type="text" class="form-input text-sm property-input" placeholder="Enter state">
                    </div>
                  
                </div>
            </div>

        </div>

      

         </div>
  <!-- Instrument Type Section -->
  <div class="form-section">
    <h4 class="form-section-title">Instrument Type</h4>
    <div class="space-y-3">
        <!-- Transaction Type and Date -->
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="transactionType-record" class="text-sm">Transaction Type</label>
                <select id="transactionType-record" x-model="selectedTransactionType" class="form-select text-sm transaction-type-select">
                    <option value="">Select type</option>
                    <option value="Deed of Transfer">Deed of Transfer</option>
                    <option value="Certificate of Occupancy">Certificate of Occupancy</option>
                    <option value="ST Certificate of Occupancy">ST Certificate of Occupancy</option>
                    <option value="SLTR Certificate of Occupancy">SLTR Certificate of Occupancy</option>
                    <option value="Irrevocable Power of Attorney">Irrevocable Power of Attorney</option>
                    <option value="Deed of Release">Deed of Release</option>
                    <option value="Deed of Assignment">Deed of Assignment</option>
                    <option value="ST Assignment">ST Assignment</option>
                    <option value="Deed of Mortgage">Deed of Mortgage</option>
                    <option value="Tripartite Mortgage">Tripartite Mortgage</option>
                    <option value="Deed of Sub Lease">Deed of Sub Lease</option>
                    <option value="Deed of Sub Under Lease">Deed of Sub Under Lease</option>
                    <option value="Power of Attorney">Power of Attorney</option>
                    <option value="Deed of Surrender">Deed of Surrender</option>
                    <option value="Indenture of Lease">Indenture of Lease</option>
                    <option value="Deed of Variation">Deed of Variation</option>
                    <option value="Customary Right of Occupancy">Customary Right of Occupancy</option>
                    <option value="Vesting Assent">Vesting Assent</option>
                    <option value="Court Judgement">Court Judgement</option>
                    <option value="Exchange of Letters">Exchange of Letters</option>
                    <option value="Tenancy Agreement">Tenancy Agreement</option>
                    <option value="Revocation of Power of Attorney">Revocation of Power of Attorney</option>
                    <option value="Deed of Convenyence">Deed of Convenyence</option>
                    <option value="Memorandom of Agreement">Memorandom of Agreement</option>
                    <option value="Quarry Lease">Quarry Lease</option>
                    <option value="Private Lease">Private Lease</option>
                    <option value="Deed of Gift">Deed of Gift</option>
                    <option value="Deed of Partition">Deed of Partition</option>
                    <option value="Non-European Occupational Lease">Non-European Occupational Lease</option>
                    <option value="Deed of Revocation">Deed of Revocation</option>
                    <option value="Deed of lease">Deed of lease</option>
                    <option value="Deed of Reconveyance">Deed of Reconveyance</option>
                    <option value="Letter of Administration">Letter of Administration</option>
                    <option value="Customary Inhertitance">Customary Inhertitance</option>
                    <option value="Certificate of Purchase">Certificate of Purchase</option>
                    <option value="Deed of Rectification">Deed of Rectification</option>
                    <option value="Building Lease">Building Lease</option>
                    <option value="Memorandum of Loss">Memorandum of Loss</option>
                    <option value="Vesting Deed">Vesting Deed</option>
                    <option value="ST Fragmentation">ST Fragmentation</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="space-y-1">
                <label for="transactionDate" class="text-sm">Transaction/Certificate Date</label>
                <input type="date" id="transactionDate" class="form-input text-sm">
            </div>
        </div>

        <!-- Registration Number Components -->
        <div class="space-y-1" x-data="{ serialNo: '', pageNo: '', volumeNo: '', showPreview: false, get regNoDisplay() { return [this.serialNo, this.pageNo, this.volumeNo].filter(Boolean).join('/') || 'Not set'; } }">
            <label class="text-sm">Registration Number  </label>
            <div class="grid grid-cols-5 gap-2">
                <div>
                    <label for="serialNo" class="text-xs">Serial No.</label>
                    <input id="serialNo" name="serialNo" x-model="serialNo" @input="showPreview = serialNo || pageNo || volumeNo" class="form-input text-xs py-1" placeholder="e.g. 1">
                </div>
                <div>
                    <label for="pageNo" class="text-xs">Page No.</label>
                    <input id="pageNo" name="pageNo" x-model="pageNo" @input="showPreview = serialNo || pageNo || volumeNo" class="form-input text-xs py-1" placeholder="e.g. 1">
                </div>
                <div>
                    <label for="volumeNo" class="text-xs">Volume No.</label>
                    <input id="volumeNo" name="volumeNo" x-model="volumeNo" @input="showPreview = serialNo || pageNo || volumeNo" class="form-input text-xs py-1" placeholder="e.g. 2">
                </div>
                <div>
                    <label for="regDate" class="text-xs">Reg Date</label>
                    <input id="regDate" name="regDate" type="date" class="form-input text-xs py-1">
                </div>
                <div>
                    <label for="regTime" class="text-xs">Reg Time</label>
                    <input id="regTime" name="regTime" type="time" class="form-input text-xs py-1">
                </div>
            </div>
            <div x-show="showPreview" x-transition class="mt-2 p-3 bg-blue-50 border-2 border-blue-200 rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-semibold text-blue-700">Registration Number:</span>
                    </div>
                    <span class="text-lg font-bold text-blue-800 tracking-wider" x-text="regNoDisplay"></span>
                </div>
                <div class="mt-1.5 flex justify-between items-center">
                    <div class="text-xs text-blue-600">Format: Serial No/Page No/Volume No</div>
                    <div x-show="serialNo && pageNo && volumeNo" class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">Complete</div>
                </div>
            </div>
        </div>

        <!-- Instrument Type and Period -->
        <div >
                   <!-- Land Use Type -->
            <div class="space-y-1">
                <label for="landUse" class="text-sm">Land Use</label>
                <select id="landUse" name="landUse" class="form-select text-sm">
                    <option value="">Select land use</option>
                    <option value="RESIDENTIAL">RESIDENTIAL</option>
                    <option value="AGRICULTURAL">AGRICULTURAL</option>
                    <option value="COMMERCIAL">COMMERCIAL</option>
                    <option value="COMMERCIAL ( WARE HOUSE)">COMMERCIAL ( WARE HOUSE)</option>
                    <option value="COMMERCIAL (OFFICES)">COMMERCIAL (OFFICES)</option>
                    <option value="COMMERCIAL (PETROL FILLING STATION)">COMMERCIAL (PETROL FILLING STATION)</option>
                    <option value="COMMERCIAL (RICE PROCESSING)">COMMERCIAL (RICE PROCESSING)</option>
                    <option value="COMMERCIAL (SCHOOL)">COMMERCIAL (SCHOOL)</option>
                    <option value="COMMERCIAL (SHOPS & PUBLIC CONVINIENCE)">COMMERCIAL (SHOPS & PUBLIC CONVINIENCE)</option>
                    <option value="COMMERCIAL (SHOPS AND OFFICES)">COMMERCIAL (SHOPS AND OFFICES)</option>
                    <option value="COMMERCIAL (SHOPS)">COMMERCIAL (SHOPS)</option>
                    <option value="COMMERCIAL (WAREHOUSE)">COMMERCIAL (WAREHOUSE)</option>
                    <option value="COMMERCIAL (WORKSHOP AND OFFICES)">COMMERCIAL (WORKSHOP AND OFFICES)</option>
                    <option value="COMMERCIAL AND RESIDENTIAL">COMMERCIAL AND RESIDENTIAL</option>
                    <option value="INDUSTRIAL">INDUSTRIAL</option>
                    <option value="INDUSTRIAL (SMALL SCALE)">INDUSTRIAL (SMALL SCALE)</option>
                    <option value="RESIDENTIAL AND COMMERCIAL">RESIDENTIAL AND COMMERCIAL</option>
                    <option value="RESIDENTIAL/COMMERCIAL">RESIDENTIAL/COMMERCIAL</option>
                    <option value="RESIDENTIAL/COMMERCIAL LAYOUT">RESIDENTIAL/COMMERCIAL LAYOUT</option>
                </select>
            </div>

            <div class="space-y-1">
                <label for="period" class="text-sm">Period/Tenancy</label>
                <div class="flex space-x-1">
                    <input id="period" type="number" class="form-input text-sm" placeholder="Period">
                    <select id="periodUnit" class="form-select text-sm w-[90px]">
                        <option value="Days">Days</option>
                        <option value="Months">Months</option>
                        <option value="Years" selected>Years</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Section -->
<div id="transaction-specific-fields-record" class="form-section" x-show="selectedTransactionType" x-transition  >
    <h4 class="form-section-title">Transaction Details</h4>
    
    <!-- Other Transaction Type Input -->
    <div id="other-transaction-type" class="space-y-1 mb-3" x-show="selectedTransactionType === 'Other'" x-transition>
        <label for="otherTransactionType" class="text-sm">Specify Other Transaction Type</label>
        <input type="text" id="otherTransactionType" name="otherTransactionType" class="form-input text-sm" placeholder="Enter transaction type">
    </div>
    
    <!-- Assignment fields -->
    <div id="assignment-fields-record" class="transaction-fields" x-show="selectedTransactionType === 'Assignment'" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="trans-assignor-record" class="text-sm" x-text="partyLabels.firstParty"></label>
                <input id="trans-assignor-record" name="Assignor" class="form-input text-sm" :placeholder="`Enter ${partyLabels.firstParty.toLowerCase()} name`">
            </div>
            <div class="space-y-1">
                <label for="trans-assignee-record" class="text-sm" x-text="partyLabels.secondParty"></label>
                <input id="trans-assignee-record" name="Assignee" class="form-input text-sm" :placeholder="`Enter ${partyLabels.secondParty.toLowerCase()} name`">
            </div>
        </div>
    </div>
    
    <!-- Mortgage fields -->
    <div id="mortgage-fields-record" class="transaction-fields" x-show="selectedTransactionType === 'Mortgage'" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="mortgagor-record" class="text-sm" x-text="partyLabels.firstParty"></label>
                <input id="mortgagor-record" name="Mortgagor" class="form-input text-sm" :placeholder="`Enter ${partyLabels.firstParty.toLowerCase()} name`">
            </div>
            <div class="space-y-1">
                <label for="mortgagee-record" class="text-sm" x-text="partyLabels.secondParty"></label>
                <input id="mortgagee-record" name="Mortgagee" class="form-input text-sm" :placeholder="`Enter ${partyLabels.secondParty.toLowerCase()} name`">
            </div>
        </div>
    </div>
    
    <!-- Surrender fields -->
    <div id="surrender-fields-record" class="transaction-fields" x-show="selectedTransactionType === 'Surrender'" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="surrenderor-record" class="text-sm" x-text="partyLabels.firstParty"></label>
                <input id="surrenderor-record" name="Surrenderor" class="form-input text-sm" :placeholder="`Enter ${partyLabels.firstParty.toLowerCase()} name`">
            </div>
            <div class="space-y-1">
                <label for="surrenderee-record" class="text-sm" x-text="partyLabels.secondParty"></label>
                <input id="surrenderee-record" name="Surrenderee" class="form-input text-sm" :placeholder="`Enter ${partyLabels.secondParty.toLowerCase()} name`">
            </div>
        </div>
    </div>
    
    <!-- Lease fields -->
    <div id="lease-fields-record" class="transaction-fields" x-show="selectedTransactionType === 'Sub-Lease'" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="lessor-record" class="text-sm" x-text="partyLabels.firstParty"></label>
                <input id="lessor-record" name="Lessor" class="form-input text-sm" :placeholder="`Enter ${partyLabels.firstParty.toLowerCase()} name`">
            </div>
            <div class="space-y-1">
                <label for="lessee-record" class="text-sm" x-text="partyLabels.secondParty"></label>
                <input id="lessee-record" name="Lessee" class="form-input text-sm" :placeholder="`Enter ${partyLabels.secondParty.toLowerCase()} name`">
            </div>
        </div>
    </div>
    
    <!-- Default/Grant fields -->
    <div id="default-fields-record" class="transaction-fields" x-show="shouldShowDefaultFields" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="grantor-record" class="text-sm" x-text="partyLabels.firstParty"></label>
                <input id="grantor-record" name="Grantor" class="form-input text-sm" :placeholder="`Enter ${partyLabels.firstParty.toLowerCase()} name`" :value="autoFilledGrantor" :readonly="isGrantorReadonly" :class="isGrantorReadonly ? 'bg-gray-100' : ''">
            </div>
            <div class="space-y-1">
                <label for="grantee-record" class="text-sm" x-text="partyLabels.secondParty"></label>
                <input id="grantee-record" name="Grantee" class="form-input text-sm" :placeholder="`Enter ${partyLabels.secondParty.toLowerCase()} name`">
            </div>
        </div>
    </div>

</div>
         <div class="space-y-1">
            <label class="text-sm">Description</label>
            <textarea id="property-description" name="property_description" rows="4" class="form-input text-sm" readonly x-text="description"></textarea>
            <div class="text-xs text-gray-500 italic">This field is auto-populated based on property details</div>
        </div>
                    
    </div>
    
    <div class="flex justify-end space-x-3 pt-2 border-t mt-4">
        
        <button id="property-submit-btn" type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>

@if(!$is_ai)
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handler
    const propertyForm = document.getElementById('property-record-form');
    if (propertyForm) {
        propertyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // File numbers are automatically handled by Alpine.js in the manual_fileno partial
            
            // Update registration number preview and hidden field
            const serialNo = document.getElementById('serialNo').value || '';
            const pageNo = document.getElementById('pageNo').value || '';
            const volumeNo = document.getElementById('volumeNo').value || '';
            
            if (document.getElementById('regNo')) {
                document.getElementById('regNo').textContent = `${serialNo}/${pageNo}/${volumeNo}`;
            }
            
            // Create a hidden input for the reg number if it doesn't exist
            if (!document.getElementById('regNoField')) {
                const regNoInput = document.createElement('input');
                regNoInput.type = 'hidden';
                regNoInput.id = 'regNoField';
                regNoInput.name = 'regNo';
                regNoInput.value = `${serialNo}/${pageNo}/${volumeNo}`;
                propertyForm.appendChild(regNoInput);
            } else {
                document.getElementById('regNoField').value = `${serialNo}/${pageNo}/${volumeNo}`;
            }
            
            // Get transaction-specific fields based on the active transaction type
            const transactionType = document.getElementById('transactionType-record').value;
            if (transactionType) {
                // Update party field names based on transaction type
                updatePartyFields(transactionType);
            }
            
            // Now actually submit the form
            console.log('Submitting form...');
            
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Saving property record',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Get the form action URL as a string
            const actionUrl = propertyForm.getAttribute('action');
            
            // Use fetch API for AJAX submission
            fetch(actionUrl, {
                method: 'POST',
                body: new FormData(propertyForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Handle validation errors specifically
                    if (response.status === 422) {
                        return response.json().then(data => {
                            throw new Error('Validation failed');
                        });
                    }
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error message with SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Validation failed',
                    confirmButtonColor: '#3085d6'
                });
            });
        });
    }
    
    // Function to update party fields based on transaction type
    function updatePartyFields(transactionType) {
        // Check which fields are visible based on transaction type
        switch(transactionType) {
            case 'assignment':
                if (document.getElementById('trans-assignor-record')) {
                    const assignorField = document.createElement('input');
                    assignorField.type = 'hidden';
                    assignorField.name = 'Assignor';
                    assignorField.value = document.getElementById('trans-assignor-record').value;
                    propertyForm.appendChild(assignorField);
                    
                    const assigneeField = document.createElement('input');
                    assigneeField.type = 'hidden';
                    assigneeField.name = 'Assignee';
                    assigneeField.value = document.getElementById('trans-assignee-record').value;
                    propertyForm.appendChild(assigneeField);
                }
                break;
            case 'mortgage':
                if (document.getElementById('mortgagor-record')) {
                    const mortgagorField = document.createElement('input');
                    mortgagorField.type = 'hidden';
                    mortgagorField.name = 'Mortgagor';
                    mortgagorField.value = document.getElementById('mortgagor-record').value;
                    propertyForm.appendChild(mortgagorField);
                    
                    const mortgageeField = document.createElement('input');
                    mortgageeField.type = 'hidden';
                    mortgageeField.name = 'Mortgagee';
                    mortgageeField.value = document.getElementById('mortgagee-record').value;
                    propertyForm.appendChild(mortgageeField);
                }
                break;
                // Add other transaction types as needed
        }
    }
    
    // Initialize registration number preview
    const serialNo = document.getElementById('serialNo');
    const pageNo = document.getElementById('pageNo');
    const volumeNo = document.getElementById('volumeNo');
    
    // Function to update registration number preview
    function updateRegNoPreview() {
        const serialNo = document.getElementById('serialNo');
        const pageNo = document.getElementById('pageNo');
        const volumeNo = document.getElementById('volumeNo');
        
        if ((serialNo && serialNo.value) || (pageNo && pageNo.value) || (volumeNo && volumeNo.value)) {
            const regNoDisplay = [
                serialNo ? serialNo.value : '',
                pageNo ? pageNo.value : '',
                volumeNo ? volumeNo.value : ''
            ].filter(Boolean).join('/') || 'Not set';
            
            // Update any preview elements if they exist
            const previewElement = document.querySelector('[x-text="regNoDisplay"]');
            if (previewElement) {
                previewElement.textContent = regNoDisplay;
            }
        }
    }
    
    if (serialNo) serialNo.addEventListener('input', updateRegNoPreview);
    if (pageNo) pageNo.addEventListener('input', updateRegNoPreview);
    if (volumeNo) volumeNo.addEventListener('input', updateRegNoPreview);
    
    // Fix name of transaction type field to match controller expected name
    const transactionTypeField = document.getElementById('transactionType-record');
    if (transactionTypeField) {
        transactionTypeField.name = 'transactionType';
    }
    
    // Fix other field names to match expected controller names
    const instrumentTypeField = document.getElementById('instrumentType');
    if (instrumentTypeField) {
        instrumentTypeField.name = 'instrumentType';
    }
    
    const periodField = document.getElementById('period');
    if (periodField) {
        periodField.name = 'period';
    }
    
    const periodUnitField = document.getElementById('periodUnit');
    if (periodUnitField) {
        periodUnitField.name = 'periodUnit';
    }
    
    const propertyDescriptionField = document.getElementById('property-description');
    if (propertyDescriptionField) {
        propertyDescriptionField.name = 'property_description';
    }
    
    const locationField = document.getElementById('property-location');
    if (locationField) {
        locationField.name = 'location';
    }
    
    const plotNoField = document.getElementById('plotNo');
    if (plotNoField) {
        plotNoField.name = 'plotNo';
    }
    
    // Also fix date field name
    const transactionDateField = document.getElementById('transactionDate');
    if (transactionDateField) {
        transactionDateField.name = 'transactionDate';
    }
});

// Alpine.js component for Property Record Form
function propertyRecordForm() {
    return {
        selectedTransactionType: '',
        
        // Property description variables
        house: '',
        plot: '',
        street: '',
        district: '',
        lga: '',
        state: 'Kano',
        
        // Computed property for description
        get description() {
            let desc = '';
            if (this.house) desc += `House ${this.house}`;
            if (this.plot) desc += (desc ? ', ' : '') + `Plot ${this.plot}`;
            if (this.street) desc += (desc ? ', ' : '') + this.street;
            if (this.district) desc += (desc ? ', ' : '') + this.district;
            if (this.lga) desc += (desc ? ', ' : '') + `${this.lga} LGA`;
            if (this.state) desc += (desc ? ', ' : '') + this.state;
            return desc;
        },
        
        // Define transaction types with their corresponding party labels
        transactionTypes: {
            'Deed of Transfer': { firstParty: 'Transferor', secondParty: 'Transferee' },
            'Certificate of Occupancy': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'ST Certificate of Occupancy': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'SLTR Certificate of Occupancy': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Irrevocable Power of Attorney': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Deed of Release': { firstParty: 'Releasor', secondParty: 'Releasee' },
            'Deed of Assignment': { firstParty: 'Assignor', secondParty: 'Assignee' },
            'ST Assignment': { firstParty: 'Assignor', secondParty: 'Assignee' },
            'Deed of Mortgage': { firstParty: 'Mortgagor', secondParty: 'Mortgagee' },
            'Tripartite Mortgage': { firstParty: 'Mortgagor', secondParty: 'Mortgagee' },
            'Deed of Sub Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Sub Under Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Power of Attorney': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Deed of Surrender': { firstParty: 'Surrenderor', secondParty: 'Surrenderee' },
            'Indenture of Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Variation': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Customary Right of Occupancy': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Vesting Assent': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Court Judgement': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Exchange of Letters': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Tenancy Agreement': { firstParty: 'Landlord', secondParty: 'Tenant' },
            'Revocation of Power of Attorney': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Deed of Convenyence': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Memorandom of Agreement': { firstParty: 'First Party', secondParty: 'Second Party' },
            'Quarry Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Private Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Gift': { firstParty: 'Donor', secondParty: 'Donee' },
            'Deed of Partition': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Non-European Occupational Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Revocation': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Deed of lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Reconveyance': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Letter of Administration': { firstParty: 'Administrator', secondParty: 'Beneficiary' },
            'Customary Inhertitance': { firstParty: 'Grantor', secondParty: 'Heir' },
            'Certificate of Purchase': { firstParty: 'Vendor', secondParty: 'Purchaser' },
            'Deed of Rectification': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Building Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Memorandum of Loss': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Vesting Deed': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'ST Fragmentation': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Other': { firstParty: 'Grantor', secondParty: 'Grantee' }
        },
        
        // Computed property for party labels
        get partyLabels() {
            if (this.selectedTransactionType && this.transactionTypes[this.selectedTransactionType]) {
                return this.transactionTypes[this.selectedTransactionType];
            }
            return { firstParty: 'Grantor', secondParty: 'Grantee' };
        },
        
        // Computed property to determine if default fields should be shown
        get shouldShowDefaultFields() {
            const specificTypes = ['Assignment', 'Mortgage', 'Surrender', 'Sub-Lease'];
            return this.selectedTransactionType && !specificTypes.includes(this.selectedTransactionType);
        },
        
        // Computed property for auto-filled grantor
        get autoFilledGrantor() {
            if (this.selectedTransactionType === 'Certificate of Occupancy' || this.selectedTransactionType === 'ST Certificate of Occupancy' || this.selectedTransactionType === 'SLTR Certificate of Occupancy' || this.selectedTransactionType === 'Customary Right of Occupancy') {
                return 'KANO STATE GOVERNMENT';
            }
            return '';
        },
        
        // Computed property for grantor readonly state
        get isGrantorReadonly() {
            return this.selectedTransactionType === 'Certificate of Occupancy' || this.selectedTransactionType === 'ST Certificate of Occupancy' || this.selectedTransactionType === 'SLTR Certificate of Occupancy' || this.selectedTransactionType === 'Customary Right of Occupancy';
        },
        
        // Initialize the component
        init() {
            console.log('🚀 Alpine.js Property Record Form initialized');
            
            // Watch for changes in selectedTransactionType
            this.$watch('selectedTransactionType', (value) => {
                console.log('📝 Transaction type changed to:', value);
                console.log('🏷️ Party labels updated to:', this.partyLabels);
                console.log('🔍 Should show default fields:', this.shouldShowDefaultFields);
                console.log('🏛️ Auto-filled grantor:', this.autoFilledGrantor);
            });
        }
    }
}

console.log('🎉 Alpine.js Property Record Form script loaded');

// Function to toggle other street name input
function toggleOtherStreetName() {
    const select = document.getElementById('streetName');
    const otherInput = document.getElementById('otherStreetName');
    
    if (select && otherInput) {
        if (select.value === 'other') {
            otherInput.classList.remove('hidden');
            otherInput.name = 'streetName';
            select.name = '';
        } else {
            otherInput.classList.add('hidden');
            otherInput.name = '';
            select.name = 'streetName';
        }
    }
}

// Function to toggle other street name input (alternative name for compatibility)
function toggleotherStreetName() {
    toggleOtherStreetName();
}

// Initialize the toggle function on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleOtherStreetName();
});
</script>