<script>
// Recertification Applications Table Management
let applicationsTable;
let applicationsData = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('Recertification table script loaded');
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Load applications data
    loadApplicationsData();
    
    // Setup search functionality
    setupSearch();
    
    // Setup modal handlers
    setupModalHandlers();
});

function loadApplicationsData() {
    console.log('Loading applications data...');
    
    // Show loading state
    const tableBody = document.getElementById('applications-table-body');
    const noResults = document.getElementById('no-results');
    const applicationsCount = document.getElementById('applications-count');
    
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-8">
                    <div class="loading-spinner mx-auto mb-2"></div>
                    <p class="text-gray-600">Loading applications...</p>
                </td>
            </tr>
        `;
    }
    
    // Fetch data from backend
    fetch('/recertification/data', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Applications data received:', data);
        applicationsData = data.data || [];
        
        // Update count
        if (applicationsCount) {
            applicationsCount.textContent = applicationsData.length;
        }
        
        // Render table
        renderApplicationsTable(applicationsData);
        
        // Hide no results initially
        if (noResults) {
            noResults.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error loading applications:', error);
        
        // Show error state
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-8">
                        <i data-lucide="alert-circle" class="h-8 w-8 text-red-500 mx-auto mb-2"></i>
                        <p class="text-red-600">Failed to load applications</p>
                        <button onclick="loadApplicationsData()" class="mt-2 text-blue-600 hover:text-blue-800">
                            Try Again
                        </button>
                    </td>
                </tr>
            `;
            
            // Reinitialize icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });
}

function renderApplicationsTable(data) {
    const tableBody = document.getElementById('applications-table-body');
    const noResults = document.getElementById('no-results');
    
    if (!tableBody) return;
    
    if (!data || data.length === 0) {
        tableBody.innerHTML = '';
        if (noResults) {
            noResults.classList.remove('hidden');
        }
        return;
    }
    
    // Hide no results
    if (noResults) {
        noResults.classList.add('hidden');
    }
    
    // Generate table rows
    const rows = data.map(app => {
        const actionMenuId = `action-menu-${app.id}`;
        
        return `
            <tr class="table-row border-b hover:bg-gray-50">
                <td class="p-4">
                    <div class="font-medium text-gray-900">${app.application_reference}</div>
                    <div class="text-sm text-gray-500">${app.applicant_type}</div>
                </td>
                <td class="p-4">
                    <div class="font-medium text-gray-900">${app.applicant_name}</div>
                </td>
                <td class="p-4">
                    <div class="text-gray-900">${app.plot_details}</div>
                </td>
                <td class="p-4">
                    <div class="text-gray-900">${app.lga_name}</div>
                </td>
                <td class="p-4">
                    <div class="text-gray-900">${app.created_at}</div>
                </td>
                <td class="p-4">
                    <div class="relative">
                        <button 
                            onclick="toggleActionMenu('${actionMenuId}')"
                            class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50"
                        >
                            <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                        </button>
                        
                        <div id="${actionMenuId}" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                            <div class="py-1">
                                <button onclick="viewApplication(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 gap-2">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                    View Application
                                </button>
                                <button onclick="editApplication(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 gap-2">
                                    <i data-lucide="edit" class="h-4 w-4"></i>
                                    Edit Application
                                </button>
                                <button onclick="deleteApplication(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 gap-2">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    Delete Application
                                </button>
                                <hr class="my-1">
                                <button onclick="captureExtantCofo(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 gap-2">
                                    <i data-lucide="camera" class="h-4 w-4"></i>
                                    Capture Extant CofO Details
                                </button>
                                <button onclick="generateAcknowledgement(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 gap-2">
                                    <i data-lucide="file-plus" class="h-4 w-4"></i>
                                    Generate Acknowledgement
                                </button>
                                <button onclick="viewAcknowledgement(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 gap-2">
                                    <i data-lucide="file-text" class="h-4 w-4"></i>
                                    View Acknowledgement
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    tableBody.innerHTML = rows;
    
    // Reinitialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function setupSearch() {
    const searchInput = document.getElementById('search-input');
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value.toLowerCase().trim();
            
            if (searchTerm === '') {
                renderApplicationsTable(applicationsData);
                return;
            }
            
            const filteredData = applicationsData.filter(app => {
                return (
                    app.application_reference.toLowerCase().includes(searchTerm) ||
                    app.applicant_name.toLowerCase().includes(searchTerm) ||
                    app.plot_details.toLowerCase().includes(searchTerm) ||
                    app.lga_name.toLowerCase().includes(searchTerm) ||
                    app.cofo_number.toLowerCase().includes(searchTerm)
                );
            });
            
            renderApplicationsTable(filteredData);
            
            // Update no results message
            const noResultsMessage = document.getElementById('no-results-message');
            if (noResultsMessage) {
                noResultsMessage.textContent = `No applications found matching "${searchTerm}"`;
            }
        }, 300);
    });
}

function setupModalHandlers() {
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        // Close action menus when clicking outside
        if (!event.target.closest('.relative')) {
            document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
        
        // Close details modal
        if (event.target.id === 'details-modal') {
            closeDetailsModal();
        }
    });
    
    // Close details modal button
    const closeDetailsBtn = document.getElementById('close-details-modal');
    if (closeDetailsBtn) {
        closeDetailsBtn.addEventListener('click', closeDetailsModal);
    }
    
    // ESC key to close modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDetailsModal();
            // Close all action menus
            document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
}

// Action Menu Functions
function toggleActionMenu(menuId) {
    const menu = document.getElementById(menuId);
    if (!menu) return;
    
    // Close all other menus
    document.querySelectorAll('[id^="action-menu-"]').forEach(otherMenu => {
        if (otherMenu.id !== menuId) {
            otherMenu.classList.add('hidden');
        }
    });
    
    // Toggle current menu
    menu.classList.toggle('hidden');
}

// Application Action Functions
function viewApplication(id) {
    console.log('Viewing application:', id);
    
    // Close action menu
    document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
        menu.classList.add('hidden');
    });
    
    // Fetch application details
    fetch(`/recertification/${id}/view`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showApplicationDetails(data.application, data.owners);
        } else {
            showToast('Failed to load application details', 'error');
        }
    })
    .catch(error => {
        console.error('Error viewing application:', error);
        showToast('Failed to load application details', 'error');
    });
}

function editApplication(id) {
    console.log('Editing application:', id);
    
    // Close action menu
    document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
        menu.classList.add('hidden');
    });
    
    // Navigate to edit page
    window.location.href = `/recertification/${id}/edit`;
}

function deleteApplication(id) {
    console.log('Deleting application:', id);
    
    // Close action menu
    document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
        menu.classList.add('hidden');
    });
    
    if (!confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
        return;
    }
    
    // Delete application
    fetch(`/recertification/${id}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Application deleted successfully', 'success');
            // Reload data
            loadApplicationsData();
        } else {
            showToast(data.message || 'Failed to delete application', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting application:', error);
        showToast('Failed to delete application', 'error');
    });
}

function captureExtantCofo(id) {
    console.log('Capturing Extant CofO for application:', id);
    
    // Close action menu
    document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
        menu.classList.add('hidden');
    });
    
    showToast('Capture Extant CofO Details feature coming soon', 'info');
}

function generateAcknowledgement(id) {
    console.log('Generating acknowledgement for application:', id);
    
    // Close action menu
    document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
        menu.classList.add('hidden');
    });
    
    showToast('Generate Acknowledgement feature coming soon', 'info');
}

function viewAcknowledgement(id) {
    console.log('Viewing acknowledgement for application:', id);
    
    // Close action menu
    document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
        menu.classList.add('hidden');
    });
    
    showToast('View Acknowledgement feature coming soon', 'info');
}

// Modal Functions
function showApplicationDetails(application, owners) {
    const modal = document.getElementById('details-modal');
    const content = document.getElementById('application-details-content');
    
    if (!modal || !content) return;
    
    // Format application details
    let detailsHtml = `
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Application Information</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Reference:</span> ${application.application_reference || 'N/A'}</div>
                        <div><span class="font-medium">Date:</span> ${application.application_date || 'N/A'}</div>
                        <div><span class="font-medium">Type:</span> ${application.applicant_type || 'N/A'}</div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Plot Information</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Plot Number:</span> ${application.plot_number || 'N/A'}</div>
                        <div><span class="font-medium">File Number:</span> ${application.file_number || 'N/A'}</div>
                        <div><span class="font-medium">LGA:</span> ${application.lga_name || 'N/A'}</div>
                    </div>
                </div>
            </div>
    `;
    
    // Add applicant details based on type
    if (application.applicant_type === 'Corporate') {
        detailsHtml += `
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Corporate Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">Organisation:</span> ${application.organisation_name || 'N/A'}</div>
                    <div><span class="font-medium">CAC No:</span> ${application.cac_registration_no || 'N/A'}</div>
                    <div><span class="font-medium">Type:</span> ${application.type_of_organisation || 'N/A'}</div>
                    <div><span class="font-medium">Business:</span> ${application.type_of_business || 'N/A'}</div>
                </div>
            </div>
        `;
    } else if (application.applicant_type === 'Multiple Owners' && owners && owners.length > 0) {
        detailsHtml += `
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Owners Information</h4>
                <div class="space-y-3">
        `;
        
        owners.forEach((owner, index) => {
            detailsHtml += `
                <div class="border border-gray-200 rounded p-3">
                    <h5 class="font-medium mb-2">Owner ${index + 1}</h5>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div><span class="font-medium">Name:</span> ${(owner.surname || '') + ' ' + (owner.first_name || '')}</div>
                        <div><span class="font-medium">Occupation:</span> ${owner.occupation || 'N/A'}</div>
                        <div><span class="font-medium">Nationality:</span> ${owner.nationality || 'N/A'}</div>
                        <div><span class="font-medium">State:</span> ${owner.state_of_origin || 'N/A'}</div>
                    </div>
                </div>
            `;
        });
        
        detailsHtml += `
                </div>
            </div>
        `;
    } else {
        detailsHtml += `
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Applicant Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">Name:</span> ${(application.surname || '') + ' ' + (application.first_name || '')}</div>
                    <div><span class="font-medium">Occupation:</span> ${application.occupation || 'N/A'}</div>
                    <div><span class="font-medium">Nationality:</span> ${application.nationality || 'N/A'}</div>
                    <div><span class="font-medium">State:</span> ${application.state_of_origin || 'N/A'}</div>
                </div>
            </div>
        `;
    }
    
    detailsHtml += `
        </div>
    `;
    
    content.innerHTML = detailsHtml;
    modal.classList.remove('hidden');
}

function closeDetailsModal() {
    const modal = document.getElementById('details-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Toast notification function
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

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}

// Make functions available globally
window.toggleActionMenu = toggleActionMenu;
window.viewApplication = viewApplication;
window.editApplication = editApplication;
window.deleteApplication = deleteApplication;
window.captureExtantCofo = captureExtantCofo;
window.generateAcknowledgement = generateAcknowledgement;
window.viewAcknowledgement = viewAcknowledgement;
window.removeToast = removeToast;
window.loadApplicationsData = loadApplicationsData;

console.log('Recertification table script initialized');
</script>