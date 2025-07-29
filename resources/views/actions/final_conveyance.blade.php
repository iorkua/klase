@extends('layouts.app')
@section('page-title')
    {{ __('SECTIONAL TITLING  MODULE') }}
@endsection

@section('content')
<style>
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    .tab-button {
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      padding: 0.5rem 1rem;
      border-radius: 0.25rem;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    .tab-button.active {
      background-color: #f3f4f6;
      font-weight: 500;
    }
    .tab-button:hover:not(.active) {
      background-color: #f9fafb;
    }
</style>
@include('sectionaltitling.partials.assets.css')
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">
            <!-- Display session messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            
            <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
                <div class="modal-content p-6">
                    <div class="flex justify-between items-center mb-4">
                        <button onclick="window.history.back()" class="text-gray-500 hover:text-gray-700">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <div class="py-2">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-medium">{{$application->land_use }} Property</h3>
                                <p class="text-xs text-gray-500">
                                    Application ID: {{$application->applicationID}} | File No: {{$application->fileno }}  
                                </p>
                            </div>
                            <div class="text-right">
                     <h3 class="text-sm font-medium">
                    @if($application->applicant_type == 'individual')
                    {{$application->applicant_title }} {{$application->first_name }} {{$application->surname }}
                    @elseif($application->applicant_type == 'corporate')
                    {{$application->rc_number }} {{$application->corporate_name }}
                    @elseif($application->applicant_type == 'multiple')
                    {{$application->multiple_owners_names }}
                    @endif
                      </h3>
                                <p class="text-xs text-gray-500">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        {{$application->land_use }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    
                        <!-- Tabs Navigation -->
                        @php
                            $currentRoute = request()->route()->getName();
                            $showAddBuyers = !str_contains($currentRoute, 'final-conveyance-agreement');
                            $gridCols = $showAddBuyers ? 'grid-cols-3' : 'grid-cols-2';
                            $defaultActiveTab = $showAddBuyers ? 'initial' : 'detterment';
                        @endphp
                        
                        <div class="grid {{ $gridCols }} gap-2 mb-4">
                         
                            <button class="tab-button active"  data-tab="final">
                                <i data-lucide="file-check" class="w-3.5 h-3.5 mr-1.5"></i>
                                Final Conveyance Agreement
                            </button>
                            <input type="hidden" name="application_fileno" value="{{$application->fileno}}">
                        </div>
                    
                        <!-- Add Buyers Tab -->
                        @if($showAddBuyers)
                        <div id="initial-tab" class="tab-content " x-data="{ buyers: [{}] }">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="p-4 border-b">
                                    <h3 class="text-sm font-medium">Add Buyers</h3>
                                </div>
                                <!-- Form with event handling for SweetAlert response -->
                                <form id="add-buyers-form" method="POST" action="{{ route('conveyance.update') }}" class="p-4 space-y-4">
                                    @csrf
                                    <input type="hidden" name="application_id" value="{{$application->id}}">
                                    
                                    <div>
                                        <template x-for="(buyer, index) in buyers" :key="index">
                                            <div class="flex items-start space-x-2 mb-4">
                                                <div class="grid grid-cols-4 gap-4 flex-grow">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Title <span class="text-red-500">*</span>
                                                        </label>
                                                        <select :name="'records['+index+'][buyerTitle]'"
                                                            class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                                                            <option value="" disabled selected>Select title</option>
                                                            <option value="Mr.">Mr.</option>
                                                            <option value="Mrs.">Mrs.</option>
                                                            <option value="Chief">Chief</option>
                                                            <option value="Master">Master</option>
                                                            <option value="Capt">Capt</option>
                                                            <option value="Coln">Coln</option>
                                                            <option value="Pastor">Pastor</option>
                                                            <option value="King">King</option>
                                                            <option value="Prof">Prof</option>
                                                            <option value="Dr.">Dr.</option>
                                                            <option value="Alhaji">Alhaji</option>
                                                            <option value="Alhaja">Alhaja</option>
                                                            <option value="High Chief">High Chief</option>
                                                            <option value="Lady">Lady</option>
                                                            <option value="Bishop">Bishop</option>
                                                            <option value="Senator">Senator</option>
                                                            <option value="Messr">Messr</option>
                                                            <option value="Honorable">Honorable</option>
                                                            <option value="Miss">Miss</option>
                                                            <option value="Rev.">Rev.</option>
                                                            <option value="Barr.">Barr.</option>
                                                            <option value="Arc.">Arc.</option>
                                                            <option value="Sister">Sister</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Buyer Name <span class="text-red-500">*</span></label>
                                                        <input type="text" :name="'records['+index+'][buyerName]'" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm" placeholder="Enter Buyer Name" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit No <span class="text-red-500">*</span></label>
                                                        <input type="text" :name="'records['+index+'][sectionNo]'" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm" placeholder="Enter Unit No" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Measurement (sqm)</label>
                                                        <input type="number" step="0.01" :name="'records['+index+'][measurement]'" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm" placeholder="Enter Measurement">
                                                    </div>
                                                </div>
                                                <button type="button" @click="buyers.splice(index, 1)" x-show="buyers.length > 1" class="bg-red-500 text-white p-1.5 rounded-md hover:bg-red-600 flex items-center justify-center mt-8">
                                                    <i data-lucide="x" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <button type="button" @click="buyers.push({})" class="flex items-center px-3 py-1.5 text-xs bg-blue-500 text-white rounded-md hover:bg-blue-600 mt-2">
                                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Add Buyer
                                    </button>
                                    
                                    <hr class="my-4">
                                    
                                    <div class="flex justify-between items-center">
                                        <div class="flex gap-2">
                                            <a href="{{route('sectionaltitling.primary')}}" class="flex items-center px-3 py-1 text-xs border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                                                <i data-lucide="undo-2" class="w-3.5 h-3.5 mr-1.5"></i>
                                                Back
                                            </a>
                                         
                                            <!-- Submit button -->
                                            <button type="submit" class="flex items-center px-3 py-1.5 text-xs bg-green-700 text-white rounded-md hover:bg-green-800">
                                                <i data-lucide="save" class="w-3.5 h-3.5 mr-1.5"></i>
                                                Save Buyers
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                    
                        <!-- Buyers List Tab -->
                        <div id="detterment-tab" class="tab-content ">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="p-4 border-b">
                                    <h3 class="text-sm font-medium">Buyers List</h3>
                                    <p class="text-xs text-gray-500">View and manage the list of buyers</p>
                                </div>
                                <input type="hidden" id="application_id" value="{{$application->id}}">
                                <input type="hidden" name="fileno" value="{{$application->fileno}}">
                                <div class="p-4 space-y-4">
                                    <div class="overflow-x-auto" id="buyers-list-container">
                                        <!-- Dynamic content will be loaded here -->
                                        <div class="text-center text-gray-500 py-4">Loading buyers list...</div>
                                    </div>
                                    
                                    <hr class="my-4">
                                    
                                    <div class="flex justify-between items-center">
                                        <div class="flex gap-2">
                                            <a href="javascript:window.history.back()" class="flex items-center px-3 py-1 text-xs border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                                                <i data-lucide="undo-2" class="w-3.5 h-3.5 mr-1.5"></i>
                                                Back
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Final Conveyance Agreement Tab -->
                        @include('actions.FinalConveyanceAgreement')
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('admin.footer')
    </div>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            // Check if we should load buyers list on page load (when Add Buyers tab is hidden)
            const showAddBuyers = {{ $showAddBuyers ? 'true' : 'false' }};
            if (!showAddBuyers) {
                // Load buyers list immediately when Add Buyers tab is hidden
                loadBuyersList();
            }
            
            // Handle form submission to show SweetAlert with response data
            const buyersForm = document.getElementById('add-buyers-form');
            if (buyersForm) {
                buyersForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Show loading state
                    Swal.fire({
                        title: 'Saving...',
                        html: 'Please wait while we process your request',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Get the form data
                    const formData = new FormData(this);
                    
                    // Submit the form using fetch
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Generate HTML for the records table
                            let recordsHtml = '<div class="overflow-x-auto">';
                            recordsHtml += '<table class="w-full min-w-full text-sm text-left">';
                            recordsHtml += '<thead class="text-xs uppercase bg-gray-50">';
                            recordsHtml += '<tr><th class="py-2 px-4">Buyer Name</th><th class="py-2 px-4">Unit No</th><th class="py-2 px-4">Measurement</th></tr>';
                            recordsHtml += '</thead><tbody>';
                            
                            data.records.forEach(record => {
                                recordsHtml += '<tr class="border-b">';
                                recordsHtml += `<td class="py-2 px-4">${record.buyerTitle ? record.buyerTitle + ' ' : ''}${record.buyerName}</td>`;
                                recordsHtml += `<td class="py-2 px-4">${record.sectionNo}</td>`;
                                recordsHtml += `<td class="py-2 px-4">${record.measurement || 'N/A'}</td>`;
                                recordsHtml += '</tr>';
                            });
                            
                            recordsHtml += '</tbody></table></div>';
                            
                            // Show success message with the records
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                html: 
                                    '<div class="mb-4">' + data.message + '</div>' +
                                    '<div class="text-left">' + recordsHtml + '</div>',
                                confirmButtonText: 'View Buyers List',
                                showCancelButton: true,
                                cancelButtonText: 'Add More Buyers'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Switch to the buyers list tab
                                    document.querySelector('[data-tab="detterment"]').click();
                                } else {
                                    // Reset the form for adding more buyers
                                    buyersForm.reset();
                                }
                            });
                            
                            // Refresh buyers list data in background
                            loadBuyersList();
                        } else {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to save buyers information'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred. Please try again later.'
                        });
                    });
                });
            }

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Deactivate all tabs
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Activate selected tab
                    this.classList.add('active');
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                    
                    // Refresh Lucide icons after tab switch
                    lucide.createIcons();
                    
                    // If switching to final tab, make sure the buttons are initialized
                    if (tabId === 'final') {
                        // Re-initialize print and submit buttons
                        const printEvent = new Event('DOMContentLoaded');
                        document.dispatchEvent(printEvent);
                    }
                    
                    // If switching to buyers list tab, load the buyers list
                    if (tabId === 'detterment') {
                        loadBuyersList();
                    }
                });
            });
            
            // Function to load buyers list
            function loadBuyersList() {
                const applicationId = document.getElementById('application_id').value;
                
                fetch(`{{ url('conveyance') }}/${applicationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            renderBuyersList(data.records);
                        } else {
                            console.error('Error:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
            
            // Function to render buyers list
            function renderBuyersList(records) {
                const buyersListContainer = document.getElementById('buyers-list-container');
                if (!buyersListContainer) return;
                
                if (records.length === 0) {
                    buyersListContainer.innerHTML = '<div class="p-4 text-center text-gray-500">No buyers added yet.</div>';
                    return;
                }
                
                let html = `
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SN</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buyer Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit No.</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Measurement (sqm)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                `;
                
                records.forEach((record, index) => {
                    html += `
                    <tr class="hover:bg-gray-50" data-index="${index}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${index + 1}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${record.buyer_title || ''} ${record.buyer_name || ''}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${record.unit_no || ''}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${record.measurement || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="edit-buyer text-blue-600 hover:text-blue-900 mr-2" data-id="${record.id}">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button class="delete-buyer text-red-600 hover:text-red-900" data-id="${record.id}">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                });
                
                html += `
                    </tbody>
                    </table>
                `;
                
                buyersListContainer.innerHTML = html;
                lucide.createIcons(); // Reinitialize icons
                
                // Add event listeners for edit and delete buttons
                attachBuyerActionsListeners(records);
            }
            
            // Function to attach event listeners to edit and delete buttons
            function attachBuyerActionsListeners(records) {
                // Edit buyer
                document.querySelectorAll('.edit-buyer').forEach(button => {
                    button.addEventListener('click', function() {
                        const buyerId = this.getAttribute('data-id');
                        const record = records.find(r => r.id == buyerId);
                        
                        if (!record) {
                            console.error('Record not found for ID:', buyerId);
                            return;
                        }
                        
                        Swal.fire({
                            title: 'Edit Buyer',
                            html: `
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                <select id="buyer-title" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm">
                                <option value="Mr." ${record.buyer_title === 'Mr.' ? 'selected' : ''}>Mr.</option>
                                <option value="Mrs." ${record.buyer_title === 'Mrs.' ? 'selected' : ''}>Mrs.</option>
                                <option value="Chief" ${record.buyer_title === 'Chief' ? 'selected' : ''}>Chief</option>
                                <option value="Miss" ${record.buyer_title === 'Miss' ? 'selected' : ''}>Miss</option>
                                <option value="Dr." ${record.buyer_title === 'Dr.' ? 'selected' : ''}>Dr.</option>
                                <option value="Prof" ${record.buyer_title === 'Prof' ? 'selected' : ''}>Prof</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Buyer Name</label>
                                <input id="buyer-name" type="text" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm" value="${record.buyer_name || ''}">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit No</label>
                                <input id="unit-no" type="text" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm" value="${record.unit_no || ''}">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Measurement (sqm)</label>
                                <input id="measurement" type="number" step="0.01" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm" value="${record.measurement || ''}">
                            </div>
                            <input id="buyer-id" type="hidden" value="${record.id}">
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Update',
                            confirmButtonColor: '#10B981',
                            preConfirm: () => {
                                const buyerTitle = document.getElementById('buyer-title').value;
                                const buyerName = document.getElementById('buyer-name').value;
                                const unitNo = document.getElementById('unit-no').value;
                                const measurement = document.getElementById('measurement').value;
                                const buyerId = document.getElementById('buyer-id').value;
                                
                                if (!buyerName || !unitNo) {
                                    Swal.showValidationMessage('Buyer name and unit number are required');
                                    return false;
                                }
                                
                                return { buyerTitle, buyerName, unitNo, measurement, buyerId };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Send update request to server
                                updateBuyer(result.value);
                            }
                        });
                    });
                });
                
                // Delete buyer
                document.querySelectorAll('.delete-buyer').forEach(button => {
                    button.addEventListener('click', function() {
                        const buyerId = this.getAttribute('data-id');
                        
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This buyer will be removed from the list.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#EF4444',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                deleteBuyer(buyerId);
                            }
                        });
                    });
                });
            }
            
            // Function to update a buyer
            function updateBuyer(data) {
                const applicationId = document.getElementById('application_id').value;
                
                // Show loading state
                Swal.fire({
                    title: 'Updating...',
                    html: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request
                fetch('{{ route("conveyance.update.buyer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        application_id: applicationId,
                        buyer_id: data.buyerId,
                        buyer_title: data.buyerTitle,
                        buyer_name: data.buyerName,
                        unit_no: data.unitNo,
                        measurement: data.measurement
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Buyer information updated successfully'
                        });
                        // Refresh the buyers list
                        loadBuyersList();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update buyer information'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred'
                    });
                });
            }
            
            // Function to delete a buyer
            function deleteBuyer(buyerId) {
                const applicationId = document.getElementById('application_id').value;
                
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    html: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request
                fetch('{{ route("conveyance.delete.buyer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        application_id: applicationId,
                        buyer_id: buyerId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Buyer deleted successfully'
                        });
                        // Refresh the buyers list
                        loadBuyersList();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to delete buyer'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred'
                    });
                });
            }
            
            // Load buyers list when detterment tab is clicked
            document.querySelector('[data-tab="detterment"]').addEventListener('click', loadBuyersList);
            
            // Add functionality to refresh buyers list button
            const refreshButton = document.getElementById('refresh-buyers-list');
            if (refreshButton) {
                refreshButton.addEventListener('click', loadBuyersList);
            }
        });
    </script>
@endsection