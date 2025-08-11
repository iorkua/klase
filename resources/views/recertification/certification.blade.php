@extends('layouts.app')
@section('page-title')
    {{ __('Certification Management') }}
@endsection

@section('content')
<script>
// Tailwind config
tailwind.config = { 
  theme: {
    extend: { 
      colors: {
        primary: '#3b82f6',
        'primary-foreground': '#ffffff',
        muted: '#f3f4f6',
        'muted-foreground': '#6b7280',
        border: '#e5e7eb',
        destructive: '#ef4444',
        'destructive-foreground': '#ffffff',
        secondary: '#f1f5f9',
        'secondary-foreground': '#0f172a',
      }
    }
  }
}
</script>

<style>
/* Custom styles */
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

.badge {
  display: inline-flex;
  align-items: center;
  border-radius: 9999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.badge-success {
  background-color: #dcfce7;
  color: #166534;
}

.badge-warning {
  background-color: #fef3c7;
  color: #92400e;
}

.badge-default {
  background-color: #f3f4f6;
  color: #374151;
}

/* Table hover effects */
.table-row:hover {
  background-color: rgba(0, 0, 0, 0.025);
}

/* Loading spinner */
.loading-spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Fade in animation */
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Tab styles */
.tab-button {
  position: relative;
  padding: 0.75rem 1.5rem;
  font-weight: 500;
  border-bottom: 2px solid transparent;
  transition: all 0.2s ease;
}

.tab-button.active {
  color: #3b82f6;
  border-bottom-color: #3b82f6;
  background-color: rgba(59, 130, 246, 0.05);
}

.tab-button:hover:not(.active) {
  color: #6b7280;
  background-color: rgba(0, 0, 0, 0.025);
}

.tab-content {
  display: none;
}

.tab-content.active {
  display: block;
}
</style>

<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include('admin.header')
    
    <!-- Main Content -->
    <div class="p-6">
        <div class="container mx-auto py-6 space-y-6 max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Certification Management</h1>
                    <p class="text-gray-600">Manage certificate generation and issuance for approved applications</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('recertification.index') }}" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-2">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Back to Applications
                    </a>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Generated Certificates</p>
                            <p class="text-2xl font-bold text-gray-900" id="generated-count">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i data-lucide="clock" class="h-6 w-6 text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending Generation</p>
                            <p class="text-2xl font-bold text-gray-900" id="pending-count">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i data-lucide="file-text" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Applications</p>
                            <p class="text-2xl font-bold text-gray-900" id="total-count">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i data-lucide="calendar" class="h-6 w-6 text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">This Month</p>
                            <p class="text-2xl font-bold text-gray-900" id="month-count">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="p-6">
                    <div class="flex gap-4 items-center">
                        <div class="relative flex-1">
                            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4"></i>
                            <input
                                id="search-input"
                                type="text"
                                placeholder="Search by applicant name, file number, plot number, or certificate number..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                            />
                        </div>
                        <button class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50">
                            <i data-lucide="filter" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Certification Table -->
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i data-lucide="award" class="h-5 w-5 text-blue-600"></i>
                            Certificate Management
                        </h3>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button id="tab-not-generated" class="tab-button active" onclick="switchTab('not-generated')">
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="h-4 w-4"></i>
                                Not Generated (<span id="not-generated-tab-count">0</span>)
                            </div>
                        </button>
                        <button id="tab-generated" class="tab-button" onclick="switchTab('generated')">
                            <div class="flex items-center gap-2">
                                <i data-lucide="check-circle" class="h-4 w-4"></i>
                                Generated (<span id="generated-tab-count">0</span>)
                            </div>
                        </button>
                    </nav>
                </div>
                
                <!-- Tab Content -->
                <div class="rounded-md border-t-0" id="certification-table-container">
                    <!-- Not Generated Tab -->
                    <div id="not-generated-content" class="tab-content active">
                        <div class="p-6">
                            <div class="table-container">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b bg-gray-50">
                                            <th class="text-left p-4 font-medium text-gray-700">File No</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Application Type</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Applicant Name</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Plot Details</th>
                                            <th class="text-left p-4 font-medium text-gray-700">LGA</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Application Date</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Status</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="not-generated-table-body">
                                        <!-- Applications will be loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- No results state -->
                            <div id="not-generated-no-results" class="hidden text-center py-12">
                                <i data-lucide="clock" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                                <h3 class="text-lg font-medium mb-2 text-gray-900">No pending certificates</h3>
                                <p class="text-gray-600">All certificates have been generated</p>
                            </div>
                        </div>
                    </div>

                    <!-- Generated Tab -->
                    <div id="generated-content" class="tab-content">
                        <div class="p-6">
                            <div class="table-container">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b bg-gray-50">
                                            <th class="text-left p-4 font-medium text-gray-700">File No</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Application Type</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Applicant Name</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Plot Details</th>
                                            <th class="text-left p-4 font-medium text-gray-700">LGA</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Generated Date</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Status</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="generated-table-body">
                                        <!-- Applications will be loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- No results state -->
                            <div id="generated-no-results" class="hidden text-center py-12">
                                <i data-lucide="file-text" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                                <h3 class="text-lg font-medium mb-2 text-gray-900">No generated certificates</h3>
                                <p class="text-gray-600">No certificates have been generated yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    @include('admin.footer')
</div>

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
    <!-- Toast messages will be inserted here -->
</div>

<script>
// Certification Management Table
let certificationData = [];
let currentTab = 'not-generated';

document.addEventListener('DOMContentLoaded', function() {
    console.log('Certification table script loaded');
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Load certification data
    loadCertificationData();
    
    // Setup search functionality
    setupSearch();
    
    // Setup modal handlers
    setupModalHandlers();
});

function loadCertificationData() {
    console.log('Loading certification data...');
    
    // Show loading state for both tabs
    showLoadingState('not-generated-table-body');
    showLoadingState('generated-table-body');
    
    // Fetch data from backend
    fetch('/recertification/certification-data', {
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
        console.log('Certification data received:', data);
        certificationData = data.data || [];
        
        // Update statistics
        updateStatistics(data.statistics || {});
        
        // Render tables
        renderCertificationTables();
    })
    .catch(error => {
        console.error('Error loading certification data:', error);
        showErrorState('not-generated-table-body');
        showErrorState('generated-table-body');
    });
}

function showLoadingState(tableBodyId) {
    const tableBody = document.getElementById(tableBodyId);
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-8">
                    <div class="loading-spinner mx-auto mb-2"></div>
                    <p class="text-gray-600">Loading certification data...</p>
                </td>
            </tr>
        `;
    }
}

function showErrorState(tableBodyId) {
    const tableBody = document.getElementById(tableBodyId);
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-8">
                    <i data-lucide="alert-circle" class="h-8 w-8 text-red-500 mx-auto mb-2"></i>
                    <p class="text-red-600">Failed to load certification data</p>
                    <button onclick="loadCertificationData()" class="mt-2 text-blue-600 hover:text-blue-800">
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
}

function updateStatistics(stats) {
    document.getElementById('generated-count').textContent = stats.generated || 0;
    document.getElementById('pending-count').textContent = stats.pending || 0;
    document.getElementById('total-count').textContent = stats.total || 0;
    document.getElementById('month-count').textContent = stats.thisMonth || 0;
}

function getApplicationTypeClass(type) {
    switch(type) {
        case 'Individual':
            return 'bg-blue-100 text-blue-800';
        case 'Corporate':
            return 'bg-purple-100 text-purple-800';
        case 'Government Body':
            return 'bg-green-100 text-green-800';
        case 'Multiple Owners':
            return 'bg-orange-100 text-orange-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function getStatusBadge(isGenerated) {
    if (isGenerated) {
        return '<span class="badge badge-success">Generated</span>';
    } else {
        return '<span class="badge badge-warning">Pending</span>';
    }
}

function renderCertificationTables() {
    const notGeneratedData = certificationData.filter(app => !app.certificate_generated);
    const generatedData = certificationData.filter(app => app.certificate_generated);
    
    // Update tab counts
    document.getElementById('not-generated-tab-count').textContent = notGeneratedData.length;
    document.getElementById('generated-tab-count').textContent = generatedData.length;
    
    // Render not generated table
    renderTable('not-generated-table-body', 'not-generated-no-results', notGeneratedData, false);
    
    // Render generated table
    renderTable('generated-table-body', 'generated-no-results', generatedData, true);
}

function renderTable(tableBodyId, noResultsId, data, isGenerated) {
    const tableBody = document.getElementById(tableBodyId);
    const noResults = document.getElementById(noResultsId);
    
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
        const dateField = isGenerated ? (app.certificate_generated_date || 'N/A') : (app.created_at || 'N/A');
        
        return `
            <tr class="table-row border-b hover:bg-gray-50">
                <td class="p-4">
                    <div class="font-medium text-blue-900 font-mono">${app.file_number || 'N/A'}</div>
                </td>
                <td class="p-4">
                    <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getApplicationTypeClass(app.applicant_type)}">
                        ${app.applicant_type || 'N/A'}
                    </div>
                </td>
                <td class="p-4">
                    <div class="font-medium text-gray-900">${app.applicant_name || 'N/A'}</div>
                </td>
                <td class="p-4">
                    <div class="text-gray-900">${app.plot_details || 'N/A'}</div>
                </td>
                <td class="p-4">
                    <div class="text-gray-900">${app.lga_name || 'N/A'}</div>
                </td>
                <td class="p-4">
                    <div class="text-gray-900">${dateField}</div>
                </td>
                <td class="p-4">
                    ${getStatusBadge(isGenerated)}
                </td>
                <td class="p-4">
                    <div class="relative">
                        <button 
                            onclick="toggleActionMenu('${actionMenuId}')"
                            class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50"
                        >
                            <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                        </button>
                        
                        <div id="${actionMenuId}" class="hidden absolute right-0 top-full mt-1 w-56 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                            <div class="py-1">
                                ${generateActionMenuItems(app, isGenerated)}
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

function generateActionMenuItems(app, isGenerated) {
    let menuItems = '';
    
    if (isGenerated) {
        // Actions for generated certificates - include View CoR
        menuItems = `
            <button onclick="viewCoR(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 gap-2">
                <i data-lucide="eye" class="h-4 w-4"></i>
                View CoR
            </button>
            <button onclick="viewCofoFrontPage(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 gap-2">
                <i data-lucide="file-text" class="h-4 w-4"></i>
                View CofO (Front Page)
            </button>
            <button onclick="viewTDP(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 gap-2">
                <i data-lucide="map" class="h-4 w-4"></i>
                View TDP
            </button>
            <button onclick="viewCofo(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 gap-2">
                <i data-lucide="file-check" class="h-4 w-4"></i>
                View CofO
            </button>
        `;
    } else {
        // Actions for not generated certificates - only Generate CofO (Front Page), no View CoR
        menuItems = `
            <button onclick="generateCofoFrontPage(${app.id})" class="flex items-center w-full px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 gap-2">
                <i data-lucide="file-plus" class="h-4 w-4"></i>
                Generate CofO (Front Page)
            </button>
        `;
    }
    
    return menuItems;
}

function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.getElementById(`tab-${tabName}`).classList.add('active');
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    document.getElementById(`${tabName}-content`).classList.add('active');
    
    currentTab = tabName;
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
                renderCertificationTables();
                return;
            }
            
            const filteredData = certificationData.filter(app => {
                return (
                    (app.file_number && app.file_number.toLowerCase().includes(searchTerm)) ||
                    (app.applicant_name && app.applicant_name.toLowerCase().includes(searchTerm)) ||
                    (app.plot_details && app.plot_details.toLowerCase().includes(searchTerm)) ||
                    (app.lga_name && app.lga_name.toLowerCase().includes(searchTerm)) ||
                    (app.cofo_number && app.cofo_number.toLowerCase().includes(searchTerm)) ||
                    (app.applicant_type && app.applicant_type.toLowerCase().includes(searchTerm))
                );
            });
            
            // Filter and render based on current tab
            const notGeneratedData = filteredData.filter(app => !app.certificate_generated);
            const generatedData = filteredData.filter(app => app.certificate_generated);
            
            renderTable('not-generated-table-body', 'not-generated-no-results', notGeneratedData, false);
            renderTable('generated-table-body', 'generated-no-results', generatedData, true);
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
    });
    
    // ESC key to close modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
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
    
    // Position menu correctly
    if (!menu.classList.contains('hidden')) {
        const button = menu.previousElementSibling;
        const buttonRect = button.getBoundingClientRect();
        const menuRect = menu.getBoundingClientRect();
        const viewportHeight = window.innerHeight;
        const viewportWidth = window.innerWidth;
        
        // Reset positioning
        menu.style.position = 'fixed';
        menu.style.top = '';
        menu.style.bottom = '';
        menu.style.left = '';
        menu.style.right = '';
        
        // Calculate position
        let top = buttonRect.bottom + 4;
        let left = buttonRect.right - 224; // 224px = w-56 (14rem * 16px)
        
        // Adjust if menu goes outside viewport
        if (top + menuRect.height > viewportHeight) {
            top = buttonRect.top - menuRect.height - 4;
        }
        
        if (left < 8) {
            left = buttonRect.left;
        }
        
        if (left + 224 > viewportWidth) {
            left = viewportWidth - 224 - 8;
        }
        
        menu.style.top = `${top}px`;
        menu.style.left = `${left}px`;
        menu.style.zIndex = '1000';
    }
}

// Certificate Action Functions
function viewCoR(id) {
    console.log('Viewing CoR for application:', id);
    closeActionMenus();
    window.location.href = `/recertification/${id}/cor`;
}

function generateCofoFrontPage(id) {
    console.log('Generating CofO Front Page for application:', id);
    closeActionMenus();
    
    if (!confirm('Are you sure you want to generate the Certificate of Occupancy Front Page for this application?')) {
        return;
    }
    
    showToast('Generating CofO Front Page...', 'info');
    
    fetch(`/recertification/${id}/generate-cofo-front`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('CofO Front Page generated successfully', 'success');
            loadCertificationData(); // Reload data
        } else {
            showToast(data.message || 'Failed to generate CofO Front Page', 'error');
        }
    })
    .catch(error => {
        console.error('Error generating CofO Front Page:', error);
        showToast('Failed to generate CofO Front Page', 'error');
    });
}

function viewCofoFrontPage(id) {
    console.log('Viewing CofO Front Page for application:', id);
    closeActionMenus();
    window.location.href = `/recertification/${id}/cofo-front-page`;
}

function viewTDP(id) {
    console.log('Viewing TDP for application:', id);
    closeActionMenus();
    window.location.href = `/recertification/${id}/tdp`;
}

function viewCofo(id) {
    console.log('Viewing CofO for application:', id);
    closeActionMenus();
    window.location.href = `/recertification/${id}/cofo`;
}

function closeActionMenus() {
    document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
        menu.classList.add('hidden');
    });
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
window.switchTab = switchTab;
window.viewCoR = viewCoR;
window.generateCofoFrontPage = generateCofoFrontPage;
window.viewCofoFrontPage = viewCofoFrontPage;
window.viewTDP = viewTDP;
window.viewCofo = viewCofo;
window.removeToast = removeToast;
window.loadCertificationData = loadCertificationData;

console.log('Certification table script initialized');
</script>

@endsection