@extends('layouts.app')
@section('page-title')
    {{ __($PageTitle) }}
@endsection

@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include('admin.header')
    
    <!-- Main Content -->
    <div class="p-6">
        <div class="container mx-auto py-6 space-y-6 max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $PageTitle }}</h1>
                    <p class="text-gray-600">{{ $PageDescription }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('recertification.index') }}" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-2">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Back to Applications
                    </a>
                </div>
            </div>

            <!-- Verification Sheet Table -->
            <div class="bg-white rounded-lg shadow-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <i data-lucide="clipboard-check" class="h-5 w-5"></i>
                        Verification Sheet
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table id="verification-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        New File No
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Application Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Applicant Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Plot Details
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        LGA
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Application Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    @include('admin.footer')
</div>

<!-- Application Details Modal -->
<div id="application-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Application Details</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="py-4" id="modal-content">
                <!-- Content will be loaded here -->
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-end pt-4 border-t gap-3">
                <button type="button" onclick="closeModal()" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
    <!-- Toast messages will be inserted here -->
</div>

<script>
$(document).ready(function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Initialize DataTable
    const table = $('#verification-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("recertification.verification-data") }}',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error, thrown);
                showToast('Failed to load verification data', 'error');
            }
        },
        columns: [
            { 
                data: 'file_number',
                name: 'file_number',
                render: function(data, type, row) {
                    return `<span class="font-mono text-sm">${data}</span>`;
                }
            },
            { 
                data: 'application_type',
                name: 'application_type',
                render: function(data, type, row) {
                    const badges = {
                        'Recertification': 'bg-blue-100 text-blue-800',
                        'Re-issuance': 'bg-green-100 text-green-800',
                        'Replacement': 'bg-yellow-100 text-yellow-800'
                    };
                    const badgeClass = badges[data] || 'bg-gray-100 text-gray-800';
                    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: 'applicant_name',
                name: 'applicant_name',
                render: function(data, type, row) {
                    return `<div class="text-sm font-medium text-gray-900">${data}</div>`;
                }
            },
            { 
                data: 'plot_details',
                name: 'plot_details',
                render: function(data, type, row) {
                    return `<div class="text-sm text-gray-600">${data}</div>`;
                }
            },
            { 
                data: 'lga_name',
                name: 'lga_name',
                render: function(data, type, row) {
                    return `<div class="text-sm text-gray-600">${data}</div>`;
                }
            },
            { 
                data: 'application_date',
                name: 'application_date',
                render: function(data, type, row) {
                    return `<div class="text-sm text-gray-600">${data}</div>`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <div class="flex items-center space-x-2">
                            <button onclick="viewApplication(${row.id})" class="inline-flex items-center justify-center rounded-md font-medium text-xs px-2.5 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700 gap-1">
                                <i data-lucide="eye" class="h-3 w-3"></i>
                                View
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[5, 'desc']], // Sort by application date descending
        pageLength: 25,
        responsive: true,
        language: {
            processing: "Loading verification data...",
            emptyTable: "No applications found for verification",
            zeroRecords: "No matching applications found"
        },
        drawCallback: function() {
            // Re-initialize Lucide icons after table redraw
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });
    
    // Refresh table every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});

// View application details
function viewApplication(id) {
    console.log('Viewing application:', id);
    
    // Show loading state
    document.getElementById('modal-title').textContent = 'Loading...';
    document.getElementById('modal-content').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading application details...</span>
        </div>
    `;
    document.getElementById('application-modal').classList.remove('hidden');
    
    // Fetch application details
    fetch(`/recertification/${id}/view`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayApplicationDetails(data.application, data.owners || []);
            } else {
                throw new Error(data.error || 'Failed to load application');
            }
        })
        .catch(error => {
            console.error('Error loading application:', error);
            document.getElementById('modal-content').innerHTML = `
                <div class="text-center py-8">
                    <i data-lucide="alert-circle" class="h-12 w-12 text-red-500 mx-auto mb-4"></i>
                    <p class="text-red-600">Failed to load application details</p>
                    <p class="text-sm text-gray-500 mt-2">${error.message}</p>
                </div>
            `;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
}

// Display application details in modal
function displayApplicationDetails(application, owners) {
    document.getElementById('modal-title').textContent = `Application Details - ${application.file_number || 'N/A'}`;
    
    // Determine applicant name
    let applicantName = 'N/A';
    if (application.applicant_type === 'Corporate') {
        applicantName = application.organisation_name || 'N/A';
    } else {
        applicantName = `${application.surname || ''} ${application.first_name || ''}`.trim() || 'N/A';
    }
    
    let content = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <h4 class="font-semibold text-gray-900 border-b pb-2">Basic Information</h4>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">File Number:</span> ${application.file_number || 'N/A'}</div>
                    <div><span class="font-medium">Application Type:</span> ${application.application_type || 'N/A'}</div>
                    <div><span class="font-medium">Applicant Type:</span> ${application.applicant_type || 'N/A'}</div>
                    <div><span class="font-medium">Applicant Name:</span> ${applicantName}</div>
                    <div><span class="font-medium">Application Date:</span> ${application.application_date ? new Date(application.application_date).toLocaleDateString() : 'N/A'}</div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="space-y-4">
                <h4 class="font-semibold text-gray-900 border-b pb-2">Contact Information</h4>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Phone:</span> ${application.phone_no || 'N/A'}</div>
                    <div><span class="font-medium">Email:</span> ${application.email_address || 'N/A'}</div>
                    <div><span class="font-medium">Address:</span> ${application.address_line1 || 'N/A'}</div>
                    <div><span class="font-medium">City/Town:</span> ${application.city_town || 'N/A'}</div>
                    <div><span class="font-medium">State:</span> ${application.state_name || 'N/A'}</div>
                </div>
            </div>
            
            <!-- Plot Information -->
            <div class="space-y-4">
                <h4 class="font-semibold text-gray-900 border-b pb-2">Plot Information</h4>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Plot Number:</span> ${application.plot_number || 'N/A'}</div>
                    <div><span class="font-medium">Plot Size:</span> ${application.plot_size || 'N/A'}</div>
                    <div><span class="font-medium">Layout/District:</span> ${application.layout_district || 'N/A'}</div>
                    <div><span class="font-medium">LGA:</span> ${application.lga_name || 'N/A'}</div>
                    <div><span class="font-medium">Land Use:</span> ${application.current_land_use || 'N/A'}</div>
                </div>
            </div>
            
            <!-- Title Information -->
            <div class="space-y-4">
                <h4 class="font-semibold text-gray-900 border-b pb-2">Title Information</h4>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">C-of-O Number:</span> ${application.cofo_number || 'N/A'}</div>
                    <div><span class="font-medium">Title Holder:</span> ${application.title_holder_surname || ''} ${application.title_holder_first_name || ''}</div>
                    <div><span class="font-medium">Registration No:</span> ${application.reg_no || 'N/A'}</div>
                    <div><span class="font-medium">Original Owner:</span> ${application.is_original_owner ? 'Yes' : 'No'}</div>
                </div>
            </div>
        </div>
    `;
    
    // Add owners information if Multiple Owners
    if (application.applicant_type === 'Multiple Owners' && owners.length > 0) {
        content += `
            <div class="mt-6 space-y-4">
                <h4 class="font-semibold text-gray-900 border-b pb-2">Owners Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        `;
        
        owners.forEach((owner, index) => {
            content += `
                <div class="border rounded-lg p-4 bg-gray-50">
                    <h5 class="font-medium text-gray-900 mb-2">Owner ${index + 1}</h5>
                    <div class="space-y-1 text-sm">
                        <div><span class="font-medium">Name:</span> ${owner.surname || ''} ${owner.first_name || ''}</div>
                        <div><span class="font-medium">Occupation:</span> ${owner.occupation || 'N/A'}</div>
                        <div><span class="font-medium">Gender:</span> ${owner.gender || 'N/A'}</div>
                        <div><span class="font-medium">Nationality:</span> ${owner.nationality || 'N/A'}</div>
                    </div>
                </div>
            `;
        });
        
        content += `
                </div>
            </div>
        `;
    }
    
    document.getElementById('modal-content').innerHTML = content;
}

// Close modal
function closeModal() {
    document.getElementById('application-modal').classList.add('hidden');
}

// Show toast notification
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toastId = `toast-${Date.now()}`;
    
    const typeClasses = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-600 text-white',
        warning: 'bg-yellow-600 text-white',
        info: 'bg-blue-600 text-white'
    };
    
    const typeIcons = {
        success: 'check-circle',
        error: 'alert-circle',
        warning: 'alert-triangle',
        info: 'info'
    };
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${typeClasses[type]} px-4 py-2 rounded-md shadow-lg flex items-center gap-2 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
        <i data-lucide="${typeIcons[type]}" class="h-4 w-4"></i>
        <span>${message}</span>
        <button onclick="removeToast('${toastId}')" class="ml-2 hover:bg-black/20 rounded p-1">
            <i data-lucide="x" class="h-3 w-3"></i>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        removeToast(toastId);
    }, 5000);
}

// Remove toast
function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}

// Close modal when clicking outside
document.getElementById('application-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

@endsection