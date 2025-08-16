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

<div>
<form id="property-record-form" action="{{ route('property-records.store') }}" method="POST">
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
                   <div class="space-y-1" x-data="{ showManualEntry: false }">
                    <div class="flex items-center justify-between mb-3">
                        <label for="fileno-select" class="block text-sm font-medium text-gray-700">Select File Number</label>
                        <button type="button" @click="showManualEntry = !showManualEntry" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span x-text="showManualEntry ? 'Use Smart Selector' : 'Enter Fileno manually'"></span>
                        </button>
                    </div>
                    
                    <!-- Smart File Number Selector (Default) -->
                    <div x-show="!showManualEntry" x-transition>
                        @include('propertycard.partials.smart_fileno_selector')
                    </div>
                    
                    <!-- Manual File Number Entry -->
                    <div x-show="showManualEntry" x-transition>
                        @include('propertycard.partials.manual_fileno')
                    </div>
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
                        @include('components.District')
                    
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
                     
                   <div>
                        <label for="state" class="text-xs text-gray-600">State</label>
                        <input id="state" name="state" type="text" class="form-input text-sm property-input" placeholder="Enter state" value="Kano">
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
                <select id="transactionType-record" name="transactionType" class="form-select text-sm transaction-type-select">
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
                <input type="date" id="transactionDate" name="transactionDate" class="form-input text-sm">
            </div>
        </div>

      
         <div class="space-y-1" x-data="{ 
            serialNo: '', 
            pageNo: '', 
            volumeNo: '', 
            regDate: getCurrentWorkingDate(),
            regTime: getCurrentWorkingTime(),
            showPreview: false, 
            get regNoDisplay() { return [this.serialNo, this.pageNo, this.volumeNo].filter(Boolean).join('/') || 'Not set'; },
            updatePageNo() {
                this.pageNo = this.serialNo;
                this.showPreview = this.serialNo || this.pageNo || this.volumeNo;
            },
validateRegDate(value) {
                const selectedDate = new Date(value);
                const dayOfWeek = selectedDate.getDay();
                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    const nextMonday = new Date(selectedDate);
                    const daysToAdd = dayOfWeek === 0 ? 1 : 2;
                    nextMonday.setDate(selectedDate.getDate() + daysToAdd);
                    const nextMondayStr = nextMonday.toISOString().split('T')[0];
                    this.regDate = nextMondayStr;
                    showToast('Weekend selected. Date moved to next Monday: ' + nextMondayStr, 'warning');
                } else {
                    this.regDate = value;
                }
            },
            validateRegTime(value) {
                const [hours, minutes] = value.split(':').map(Number);
                const timeInMinutes = hours * 60 + minutes;
                const startTime = 8 * 60;
                const endTime = 17 * 60;
                if (timeInMinutes < startTime || timeInMinutes > endTime) {
                    this.regTime = '09:00';
                    showToast('Time outside working hours (8:00 AM - 5:00 PM). Set to 9:00 AM.', 'warning');
                } else {
                    this.regTime = value;
                }
            }
        }">
            <label class="text-sm">Registration Number  </label>
            <div class="grid grid-cols-5 gap-2">
                <div>
                    <label for="serialNo" class="text-xs">Serial No.</label>
                    <input id="serialNo" name="serialNo" x-model="serialNo" @input="updatePageNo()" class="form-input text-xs py-1" placeholder="e.g. 1">
                </div>
                <div>
                    <label for="pageNo" class="text-xs text-gray-500">Page No. (Auto-filled)</label>
                    <input id="pageNo" name="pageNo" x-model="pageNo" readonly class="form-input text-xs py-1 bg-gray-100 text-gray-500 cursor-not-allowed" placeholder="Same as Serial No.">
                </div>
                <div>
                    <label for="volumeNo" class="text-xs">Volume No.</label>
                    <input id="volumeNo" name="volumeNo" x-model="volumeNo" @input="showPreview = serialNo || pageNo || volumeNo" class="form-input text-xs py-1" placeholder="e.g. 2">
                </div>
                <div>
                    <label for="regDate" class="text-xs">Reg Date</label>
                    <input id="regDate" name="regDate" type="date" x-model="regDate" @change="validateRegDate($event.target.value)" class="form-input text-xs py-1">
                </div>
                <div>
                    <label for="regTime" class="text-xs">Reg Time (8AM-5PM)</label>
                    <input id="regTime" name="regTime" type="time" x-model="regTime" @change="validateRegTime($event.target.value)" min="08:00" max="17:00" class="form-input text-xs py-1">
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
                    <input id="period" name="period" type="number" class="form-input text-sm" placeholder="Period">
                    <select id="periodUnit" name="periodUnit" class="form-select text-sm w-[90px]">
                        <option value="Days">Days</option>
                        <option value="Months">Months</option>
                        <option value="Years" selected>Years</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug info -->
<div id="debug-info" class="bg-yellow-100 p-2 text-xs mb-4" style="display: none;">
    <strong>Debug:</strong> Selected Transaction Type: <span id="debug-transaction-type"></span><br>
    <strong>Party Labels:</strong> <span id="debug-party-labels"></span><br>
    <strong>Show Default Fields:</strong> <span id="debug-show-default"></span><br>
    <strong>Is Assignment:</strong> <span id="debug-is-assignment"></span><br>
    <strong>Is Mortgage:</strong> <span id="debug-is-mortgage"></span><br>
    <strong>Is Lease:</strong> <span id="debug-is-lease"></span><br>
    <strong>Is Surrender:</strong> <span id="debug-is-surrender"></span>
</div>

<!-- Transaction Details Section -->
<div id="transaction-specific-fields-record" class="form-section" style="border: 2px solid limegreen; display: none;">
    <h4 class="form-section-title">Transaction Details</h4>
    
    <!-- Other Transaction Type Input -->
    <div id="other-transaction-type" class="space-y-1 mb-3" style="border: 1px solid red; display: none;">
        <label for="otherTransactionType" class="text-sm">Specify Other Transaction Type</label>
        <input type="text" id="otherTransactionType" name="otherTransactionType" class="form-input text-sm" placeholder="Enter transaction type">
    </div>
    
    <!-- Assignment fields -->
    <div id="assignment-fields-record" class="transaction-fields mb-4" style="border: 1px solid blue; display: none;">
        <h5>ASSIGNMENT FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="trans-assignor-record" class="text-sm font-medium">Assignor</label>
                <input id="trans-assignor-record" name="Assignor" class="form-input text-sm" placeholder="Enter assignor name">
            </div>
            <div class="space-y-1">
                <label for="trans-assignee-record" class="text-sm font-medium">Assignee</label>
                <input id="trans-assignee-record" name="Assignee" class="form-input text-sm" placeholder="Enter assignee name">
            </div>
        </div>
    </div>
    
    <!-- Mortgage fields -->
    <div id="mortgage-fields-record" class="transaction-fields mb-4" style="border: 1px solid purple; display: none;">
        <h5>MORTGAGE FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="mortgagor-record" class="text-sm font-medium">Mortgagor</label>
                <input id="mortgagor-record" name="Mortgagor" class="form-input text-sm" placeholder="Enter mortgagor name">
            </div>
            <div class="space-y-1">
                <label for="mortgagee-record" class="text-sm font-medium">Mortgagee</label>
                <input id="mortgagee-record" name="Mortgagee" class="form-input text-sm" placeholder="Enter mortgagee name">
            </div>
        </div>
    </div>
    
    <!-- Surrender fields -->
    <div id="surrender-fields-record" class="transaction-fields mb-4" style="border: 1px solid orange; display: none;">
        <h5>SURRENDER FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="surrenderor-record" class="text-sm font-medium">Surrenderor</label>
                <input id="surrenderor-record" name="Surrenderor" class="form-input text-sm" placeholder="Enter surrenderor name">
            </div>
            <div class="space-y-1">
                <label for="surrenderee-record" class="text-sm font-medium">Surrenderee</label>
                <input id="surrenderee-record" name="Surrenderee" class="form-input text-sm" placeholder="Enter surrenderee name">
            </div>
        </div>
    </div>
    
    <!-- Lease fields -->
    <div id="lease-fields-record" class="transaction-fields mb-4" style="border: 1px solid teal; display: none;">
        <h5>LEASE FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="lessor-record" class="text-sm font-medium">Lessor</label>
                <input id="lessor-record" name="Lessor" class="form-input text-sm" placeholder="Enter lessor name">
            </div>
            <div class="space-y-1">
                <label for="lessee-record" class="text-sm font-medium">Lessee</label>
                <input id="lessee-record" name="Lessee" class="form-input text-sm" placeholder="Enter lessee name">
            </div>
        </div>
    </div>
    
    <!-- Release fields -->
    <div id="release-fields-record" class="transaction-fields mb-4" style="border: 1px solid pink; display: none;">
        <h5>RELEASE FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="releasor-record" class="text-sm font-medium">Releasor</label>
                <input id="releasor-record" name="Releasor" class="form-input text-sm" placeholder="Enter releasor name">
            </div>
            <div class="space-y-1">
                <label for="releasee-record" class="text-sm font-medium">Releasee</label>
                <input id="releasee-record" name="Releasee" class="form-input text-sm" placeholder="Enter releasee name">
            </div>
        </div>
    </div>
    
    <!-- Transfer fields -->
    <div id="transfer-fields-record" class="transaction-fields mb-4" style="border: 1px solid brown; display: none;">
        <h5>TRANSFER FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="transferor-record" class="text-sm font-medium">Transferor</label>
                <input id="transferor-record" name="Transferor" class="form-input text-sm" placeholder="Enter transferor name">
            </div>
            <div class="space-y-1">
                <label for="transferee-record" class="text-sm font-medium">Transferee</label>
                <input id="transferee-record" name="Transferee" class="form-input text-sm" placeholder="Enter transferee name">
            </div>
        </div>
    </div>
    
    <!-- Gift fields -->
    <div id="gift-fields-record" class="transaction-fields mb-4" style="border: 1px solid gold; display: none;">
        <h5>GIFT FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="donor-record" class="text-sm font-medium">Donor</label>
                <input id="donor-record" name="Donor" class="form-input text-sm" placeholder="Enter donor name">
            </div>
            <div class="space-y-1">
                <label for="donee-record" class="text-sm font-medium">Donee</label>
                <input id="donee-record" name="Donee" class="form-input text-sm" placeholder="Enter donee name">
            </div>
        </div>
    </div>
    
    <!-- Purchase fields -->
    <div id="purchase-fields-record" class="transaction-fields mb-4" style="border: 1px solid cyan; display: none;">
        <h5>PURCHASE FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="vendor-record" class="text-sm font-medium">Vendor</label>
                <input id="vendor-record" name="Vendor" class="form-input text-sm" placeholder="Enter vendor name">
            </div>
            <div class="space-y-1">
                <label for="purchaser-record" class="text-sm font-medium">Purchaser</label>
                <input id="purchaser-record" name="Purchaser" class="form-input text-sm" placeholder="Enter purchaser name">
            </div>
        </div>
    </div>
    
    <!-- Administration fields -->
    <div id="administration-fields-record" class="transaction-fields mb-4" style="border: 1px solid navy; display: none;">
        <h5>ADMINISTRATION FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="administrator-record" class="text-sm font-medium">Administrator</label>
                <input id="administrator-record" name="Administrator" class="form-input text-sm" placeholder="Enter administrator name">
            </div>
            <div class="space-y-1">
                <label for="beneficiary-record" class="text-sm font-medium">Beneficiary</label>
                <input id="beneficiary-record" name="Beneficiary" class="form-input text-sm" placeholder="Enter beneficiary name">
            </div>
        </div>
    </div>
    
    <!-- Default/Grant fields -->
    <div id="default-fields-record" class="transaction-fields mb-4" style="border: 1px solid gray; display: none;">
        <h5>DEFAULT FIELDS</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label for="grantor-record" class="text-sm font-medium">Grantor</label>
                <input id="grantor-record" name="Grantor" class="form-input text-sm" placeholder="Enter grantor name">
            </div>
            <div class="space-y-1">
                <label for="grantee-record" class="text-sm font-medium">Grantee</label>
                <input id="grantee-record" name="Grantee" class="form-input text-sm" placeholder="Enter grantee name">
            </div>
        </div>
    </div>

</div>
         <div class="space-y-1">
            <label class="text-sm"> </label>
            <textarea id="property-description" name="property_description" rows="4" class="form-input text-sm" readonly x-text="description"></textarea>
            <div class="text-xs text-gray-500 italic">This field is auto-populated based on property details</div>
        </div>
                    
    </div>
    
    <div class="flex justify-end space-x-3 pt-2 border-t mt-4">
        
        <button id="property-submit-btn" type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
</div>
<script>
// Vanilla JavaScript Property Record Form Handler
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 Vanilla JS Property Record Form loaded');
    
    // Check if elements exist
    const transactionSelect = document.getElementById('transactionType-record');
    const transactionSection = document.getElementById('transaction-specific-fields-record');
    const debugInfo = document.getElementById('debug-info');
    
    console.log('🔍 Elements found:');
    console.log('- Transaction Select:', transactionSelect);
    console.log('- Transaction Section:', transactionSection);
    console.log('- Debug Info:', debugInfo);
    
    if (!transactionSelect) {
        console.error('❌ Transaction select element not found!');
        return;
    }
    
    // Transaction types mapping
    const transactionTypes = {
        'Power of Attorney': { firstParty: 'Grantor', secondParty: 'Grantee' },
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
    };
    
    // Function to show/hide elements
    function showElement(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.style.display = 'block';
            console.log('✅ Showing:', elementId);
        } else {
            console.log('❌ Element not found:', elementId);
        }
    }
    
    function hideElement(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.style.display = 'none';
        }
    }
    
    // Function to show transaction section
    function showTransactionSection() {
        showElement('transaction-specific-fields-record');
        showElement('debug-info');
    }
    
    // Function to hide all transaction fields
    function hideAllTransactionFields() {
        const fieldIds = [
            'assignment-fields-record',
            'mortgage-fields-record', 
            'surrender-fields-record',
            'lease-fields-record',
            'release-fields-record',
            'transfer-fields-record',
            'gift-fields-record',
            'purchase-fields-record',
            'administration-fields-record',
            'default-fields-record',
            'other-transaction-type'
        ];
        
        fieldIds.forEach(hideElement);
    }
    
    // Function to check if transaction type is special
    function isSpecialType(type) {
        const specialTypes = [
            'Deed of Assignment', 'ST Assignment',
            'Deed of Mortgage', 'Tripartite Mortgage',
            'Deed of Surrender',
            'Deed of Sub Lease', 'Deed of Sub Under Lease', 'Indenture of Lease',
            'Quarry Lease', 'Private Lease', 'Non-European Occupational Lease',
            'Deed of lease', 'Building Lease', 'Tenancy Agreement',
            'Deed of Release',
            'Deed of Transfer',
            'Deed of Gift',
            'Certificate of Purchase',
            'Letter of Administration',
            'Other'
        ];
        return specialTypes.includes(type);
    }
    
    // Function to update party labels
    function updatePartyLabels(selectedType) {
        const partyLabels = transactionTypes[selectedType] || { firstParty: 'Grantor', secondParty: 'Grantee' };
        
        // Update debug info
        document.getElementById('debug-transaction-type').textContent = selectedType;
        document.getElementById('debug-party-labels').textContent = JSON.stringify(partyLabels);
        document.getElementById('debug-show-default').textContent = !isSpecialType(selectedType);
        
        console.log('🏷️ Updated party labels:', partyLabels);
    }
    
    // Main function to update transaction fields
    function updateTransactionFields(selectedType) {
        console.log('🔄 Updating transaction fields for:', selectedType);
        
        if (!selectedType || selectedType === '') {
            hideElement('transaction-specific-fields-record');
            hideElement('debug-info');
            return;
        }
        
        // Show main Transaction Details section
        showTransactionSection();
        
        // Hide all fields first
        hideAllTransactionFields();
        
        // Update party labels
        updatePartyLabels(selectedType);
        
        // Show appropriate fields based on transaction type
        if (selectedType === 'Other') {
            showElement('other-transaction-type');
        } else if (selectedType === 'Deed of Assignment' || selectedType === 'ST Assignment') {
            showElement('assignment-fields-record');
        } else if (selectedType === 'Deed of Mortgage' || selectedType === 'Tripartite Mortgage') {
            showElement('mortgage-fields-record');
        } else if (selectedType === 'Deed of Surrender') {
            showElement('surrender-fields-record');
        } else if (['Deed of Sub Lease', 'Deed of Sub Under Lease', 'Indenture of Lease', 'Quarry Lease', 'Private Lease', 'Non-European Occupational Lease', 'Deed of lease', 'Building Lease', 'Tenancy Agreement'].includes(selectedType)) {
            showElement('lease-fields-record');
        } else if (selectedType === 'Deed of Release') {
            showElement('release-fields-record');
        } else if (selectedType === 'Deed of Transfer') {
            showElement('transfer-fields-record');
        } else if (selectedType === 'Deed of Gift') {
            showElement('gift-fields-record');
        } else if (selectedType === 'Certificate of Purchase') {
            showElement('purchase-fields-record');
        } else if (selectedType === 'Letter of Administration') {
            showElement('administration-fields-record');
        } else {
            // Show default fields for Power of Attorney and other types
            showElement('default-fields-record');
            
            // Handle auto-filled grantor for government types
            const grantorInput = document.getElementById('grantor-record');
            if (grantorInput) {
                const autoFillTypes = ['Certificate of Occupancy', 'ST Certificate of Occupancy', 'SLTR Certificate of Occupancy', 'Customary Right of Occupancy'];
                if (autoFillTypes.includes(selectedType)) {
                    grantorInput.value = 'KANO STATE GOVERNMENT';
                    grantorInput.readOnly = true;
                    grantorInput.classList.add('bg-gray-100');
                } else {
                    grantorInput.value = '';
                    grantorInput.readOnly = false;
                    grantorInput.classList.remove('bg-gray-100');
                }
            }
        }
        
        console.log('✅ Transaction fields updated successfully');
    }
    
    // Add event listener to transaction type dropdown
    transactionSelect.addEventListener('change', function() {
        const selectedType = this.value;
        console.log('📝 Transaction type changed to:', selectedType);
        updateTransactionFields(selectedType);
    });
    
    console.log('✅ Event listener attached to transaction select');
    
    // Test the function immediately
    console.log('🧪 Testing with Power of Attorney...');
    updateTransactionFields('Power of Attorney');
});
</script>
@if(!$is_ai)
    </div>
</div>
@endif