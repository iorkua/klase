@extends('layouts.app')
@section('page-title')
    {{ __('Migrate Data') }}
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
/* Custom styles for migrate page */
.upload-area {
  border: 2px dashed #d1d5db;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.upload-area.dragover {
  border-color: #3b82f6;
  background-color: #eff6ff;
}

.upload-area:hover {
  border-color: #6b7280;
  background-color: #f9fafb;
}

.progress-bar {
  transition: width 0.3s ease;
}

.step-indicator {
  display: flex;
  align-items: center;
  margin-bottom: 2rem;
}

.step {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #e5e7eb;
  color: #6b7280;
  font-weight: 600;
  margin-right: 1rem;
  position: relative;
}

.step.active {
  background-color: #3b82f6;
  color: white;
}

.step.completed {
  background-color: #10b981;
  color: white;
}

.step::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 100%;
  width: 60px;
  height: 2px;
  background-color: #e5e7eb;
  transform: translateY(-50%);
}

.step:last-child::after {
  display: none;
}

.step.completed::after {
  background-color: #10b981;
}

.file-info {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1rem;
}

.error-item {
  background-color: #fef2f2;
  border-left: 4px solid #ef4444;
  padding: 0.75rem;
  margin-bottom: 0.5rem;
  border-radius: 0 6px 6px 0;
}

.success-item {
  background-color: #f0fdf4;
  border-left: 4px solid #10b981;
  padding: 0.75rem;
  margin-bottom: 0.5rem;
  border-radius: 0 6px 6px 0;
}

.tab-button {
  padding: 0.75rem 1.5rem;
  border-bottom: 2px solid transparent;
  font-weight: 500;
  transition: all 0.3s ease;
  cursor: pointer;
}

.tab-button.active {
  color: #3b82f6;
  border-bottom-color: #3b82f6;
  background-color: #eff6ff;
}

.tab-button:hover:not(.active) {
  color: #6b7280;
  background-color: #f9fafb;
}

.tab-content {
  display: none;
}

.tab-content.active {
  display: block;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-recertified {
  background-color: #dcfce7;
  color: #166534;
}

.status-pending {
  background-color: #fef3c7;
  color: #92400e;
}

.status-processing {
  background-color: #dbeafe;
  color: #1e40af;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.uploading {
  animation: pulse 2s infinite;
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
</style>

<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include('admin.header')
    
    <!-- Main Content -->
    <div class="p-6">
        <div class="container mx-auto py-6 space-y-6 max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <!-- Header with Back Button -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ url('/recertification') }}" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-2">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Back to Applications
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Migrate Data</h1>
                        <p class="text-gray-600">View applications and import existing recertification data</p>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="border-b border-gray-200">
                    <nav class="flex">
                        <button onclick="switchTab('applications')" id="tab-applications" class="tab-button active">
                            <div class="flex items-center gap-2">
                                <i data-lucide="list" class="h-4 w-4"></i>
                                Applications
                            </div>
                        </button>
                        <button onclick="switchTab('migrate')" id="tab-migrate" class="tab-button">
                            <div class="flex items-center gap-2">
                                <i data-lucide="upload-cloud" class="h-4 w-4"></i>
                                Migrate Data
                            </div>
                        </button>
                    </nav>
                </div>

                <!-- Applications Tab Content -->
                <div id="content-applications" class="tab-content active">
                    <div class="p-6">
                        <!-- Search and Filters -->
                        <div class="mb-6">
                            <div class="flex gap-4 items-center">
                                <div class="relative flex-1">
                                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4"></i>
                                    <input
                                        id="applications-search"
                                        type="text"
                                        placeholder="Search by file number, applicant name, plot details..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                                    />
                                </div>
                                <button onclick="loadApplicationsData()" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Applications Table -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="text-left p-4 font-medium text-gray-700">NewFileNo</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Application Type</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Applicant Name</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Plot Details</th>
                                            <th class="text-left p-4 font-medium text-gray-700">LGA</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Application Status</th>
                                            <th class="text-left p-4 font-medium text-gray-700">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="applications-table-body">
                                        <!-- Applications will be loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- No results state -->
                            <div id="applications-no-results" class="hidden text-center py-12">
                                <i data-lucide="file-text" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                                <h3 class="text-lg font-medium mb-2 text-gray-900">No applications found</h3>
                                <p class="text-gray-600">No recertification applications available</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Migrate Tab Content -->
                <div id="content-migrate" class="tab-content">
                    <div class="p-6">
                        <!-- Download Template Button -->
                        <div class="flex justify-end mb-6">
                            <a href="{{ route('recertification.migrate.template') }}" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-green-600 text-white hover:bg-green-700 gap-2">
                                <i data-lucide="download" class="h-4 w-4"></i>
                                Download Template
                            </a>
                        </div>

                        <!-- Step Indicator -->
                        <div class="step-indicator">
                            <div class="step active" id="step-1">1</div>
                            <span class="text-gray-600 font-medium mr-16">Select File</span>
                            <div class="step" id="step-2">2</div>
                            <span class="text-gray-600 font-medium mr-16">Upload & Validate</span>
                            <div class="step" id="step-3">3</div>
                            <span class="text-gray-600 font-medium">Import Complete</span>
                        </div>

                        <!-- Instructions Card -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-blue-100 rounded-full">
                                    <i data-lucide="info" class="h-6 w-6 text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Migration Instructions</h3>
                                    <div class="text-blue-800 space-y-2">
                                        <p><strong>Purpose:</strong> This is a one-off exercise to migrate existing recertification data from CSV files.</p>
                                        <p><strong>1. Download Template:</strong> Click "Download Template" to get the CSV template with all required columns.</p>
                                        <p><strong>2. Prepare Data:</strong> Fill in your existing recertification data following the sample format.</p>
                                        <p><strong>3. Upload & Import:</strong> Upload your CSV file to migrate the data into the system.</p>
                                        <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                                            <p class="font-medium text-blue-900">Important Notes:</p>
                                            <ul class="list-disc list-inside text-sm text-blue-800 mt-2 space-y-1">
                                                <li>Migrated records will automatically have status "RECERTIFIED"</li>
                                                <li>Recertification date will be set to the current date</li>
                                                <li>Maximum file size: 10MB</li>
                                                <li>Supported formats: CSV (.csv), Text (.txt)</li>
                                                <li>Date format: YYYY-MM-DD (e.g., 2024-01-15)</li>
                                                <li>Boolean fields: Use 'yes'/'no' for checkboxes</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Section -->
                        <div class="bg-white rounded-lg border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                                    <i data-lucide="upload-cloud" class="h-5 w-5 text-blue-600"></i>
                                    Upload CSV File
                                </h3>
                                <p class="text-gray-600 mt-1">Select your CSV file containing existing recertification data</p>
                            </div>
                            
                            <div class="p-6">
                                <!-- File Upload Area -->
                                <div class="upload-area p-8 text-center" id="upload-area">
                                    <div class="mb-4">
                                        <i data-lucide="file-plus" class="h-16 w-16 text-gray-400 mx-auto mb-4"></i>
                                        <h4 class="text-lg font-medium text-gray-900 mb-2">Choose CSV File</h4>
                                        <p class="text-gray-600 mb-4">Drag and drop your CSV file here, or click to browse</p>
                                    </div>
                                    
                                    <input type="file" id="csv-file" accept=".csv,.txt" class="hidden">
                                    <button onclick="document.getElementById('csv-file').click()" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-6 py-3 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700 gap-2">
                                        <i data-lucide="folder-open" class="h-4 w-4"></i>
                                        Browse Files
                                    </button>
                                </div>

                                <!-- File Info Display -->
                                <div id="file-info" class="hidden file-info">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="file-text" class="h-8 w-8"></i>
                                            <div>
                                                <h4 class="font-semibold" id="file-name">filename.csv</h4>
                                                <p class="text-blue-100" id="file-size">0 KB</p>
                                            </div>
                                        </div>
                                        <button onclick="removeFile()" class="text-white hover:text-red-200 transition-colors">
                                            <i data-lucide="x" class="h-5 w-5"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Upload Button -->
                                <div id="upload-section" class="hidden mt-6">
                                    <button onclick="uploadFile()" id="upload-btn" class="w-full inline-flex items-center justify-center rounded-md font-medium text-sm px-6 py-3 transition-all cursor-pointer bg-green-600 text-white hover:bg-green-700 gap-2">
                                        <i data-lucide="upload" class="h-4 w-4"></i>
                                        Upload and Migrate Data
                                    </button>
                                </div>

                                <!-- Progress Bar -->
                                <div id="progress-section" class="hidden mt-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Migrating data...</span>
                                        <span class="text-sm text-gray-500" id="progress-text">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full progress-bar" id="progress-bar" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Results Section -->
                        <div id="results-section" class="hidden bg-white rounded-lg border border-gray-200 mt-6">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                                    <i data-lucide="check-circle" class="h-5 w-5 text-green-600"></i>
                                    Migration Results
                                </h3>
                            </div>
                            
                            <div class="p-6">
                                <!-- Summary -->
                                <div id="import-summary" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
                                            <div>
                                                <p class="text-sm text-green-600 font-medium">Migrated</p>
                                                <p class="text-2xl font-bold text-green-900" id="success-count">0</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="alert-circle" class="h-8 w-8 text-red-600"></i>
                                            <div>
                                                <p class="text-sm text-red-600 font-medium">Errors</p>
                                                <p class="text-2xl font-bold text-red-900" id="error-count">0</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="file-text" class="h-8 w-8 text-blue-600"></i>
                                            <div>
                                                <p class="text-sm text-blue-600 font-medium">Total Processed</p>
                                                <p class="text-2xl font-bold text-blue-900" id="total-count">0</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Success Message -->
                                <div id="success-message" class="hidden success-item">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="check-circle" class="h-5 w-5 text-green-600"></i>
                                        <span class="font-medium text-green-800" id="success-text">Migration completed successfully!</span>
                                    </div>
                                </div>

                                <!-- Error Details -->
                                <div id="error-details" class="hidden">
                                    <h4 class="font-semibold text-red-900 mb-3 flex items-center gap-2">
                                        <i data-lucide="alert-triangle" class="h-5 w-5"></i>
                                        Migration Errors
                                    </h4>
                                    <div id="error-list" class="space-y-2 max-h-64 overflow-y-auto">
                                        <!-- Error items will be inserted here -->
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3 mt-6">
                                    <button onclick="resetImport()" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700 gap-2">
                                        <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                        Migrate Another File
                                    </button>
                                    <button onclick="switchTab('applications')" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border border-gray-300 text-gray-700 hover:bg-gray-50 gap-2">
                                        <i data-lucide="list" class="h-4 w-4"></i>
                                        View Applications
                                    </button>
                                </div>
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
let applicationsData = [];

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Load applications data on page load
    loadApplicationsData();
    
    // Setup file upload
    setupFileUpload();
    
    // Setup search functionality
    setupApplicationsSearch();
});

// Tab switching functionality
function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById(`tab-${tabName}`).classList.add('active');
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`content-${tabName}`).classList.add('active');
    
    // Reinitialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Load applications data when switching to applications tab
    if (tabName === 'applications') {
        loadApplicationsData();
    }
}

// Applications functionality
function loadApplicationsData() {
    console.log('Loading applications data...');
    
    const tableBody = document.getElementById('applications-table-body');
    const noResults = document.getElementById('applications-no-results');
    
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-8">
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
        renderApplicationsTable(applicationsData);
        
        if (noResults) {
            noResults.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error loading applications:', error);
        
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-8">
                        <i data-lucide="alert-circle" class="h-8 w-8 text-red-500 mx-auto mb-2"></i>
                        <p class="text-red-600">Failed to load applications</p>
                        <button onclick="loadApplicationsData()" class="mt-2 text-blue-600 hover:text-blue-800">
                            Try Again
                        </button>
                    </td>
                </tr>
            `;
            
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });
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

function getStatusClass(status) {
    switch(status?.toLowerCase()) {
        case 'recertified':
            return 'status-recertified';
        case 'pending':
            return 'status-pending';
        case 'processing':
            return 'status-processing';
        default:
            return 'status-pending';
    }
}

function renderApplicationsTable(data) {
    const tableBody = document.getElementById('applications-table-body');
    const noResults = document.getElementById('applications-no-results');
    
    if (!tableBody) return;
    
    if (!data || data.length === 0) {
        tableBody.innerHTML = '';
        if (noResults) {
            noResults.classList.remove('hidden');
        }
        return;
    }
    
    if (noResults) {
        noResults.classList.add('hidden');
    }
    
    const rows = data.map(app => {
        return `
            <tr class="border-b hover:bg-gray-50">
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
                    <div class="status-badge ${getStatusClass(app.application_status)}">
                        ${app.application_status || 'Pending'}
                    </div>
                </td>
                <td class="p-4">
                    <div class="text-gray-900">${app.recertification_date ? new Date(app.recertification_date).toLocaleDateString() : (app.created_at ? new Date(app.created_at).toLocaleDateString() : 'N/A')}</div>
                </td>
            </tr>
        `;
    }).join('');
    
    tableBody.innerHTML = rows;
}

function setupApplicationsSearch() {
    const searchInput = document.getElementById('applications-search');
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
                    (app.file_number && app.file_number.toLowerCase().includes(searchTerm)) ||
                    (app.applicant_name && app.applicant_name.toLowerCase().includes(searchTerm)) ||
                    (app.plot_details && app.plot_details.toLowerCase().includes(searchTerm)) ||
                    (app.lga_name && app.lga_name.toLowerCase().includes(searchTerm)) ||
                    (app.applicant_type && app.applicant_type.toLowerCase().includes(searchTerm))
                );
            });
            
            renderApplicationsTable(filteredData);
        }, 300);
    });
}

// File upload functionality (existing code)
function setupFileUpload() {
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('csv-file');
    const fileInfo = document.getElementById('file-info');
    const uploadSection = document.getElementById('upload-section');
    
    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });
    
    // File input change
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });
}

function handleFileSelect(file) {
    // Validate file type
    const allowedTypes = ['text/csv', 'application/csv', 'text/plain'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    
    if (!allowedTypes.includes(file.type) && !['csv', 'txt'].includes(fileExtension)) {
        showToast('Please select a valid CSV file', 'error');
        return;
    }
    
    // Validate file size (10MB max)
    if (file.size > 10 * 1024 * 1024) {
        showToast('File size must be less than 10MB', 'error');
        return;
    }
    
    // Display file info
    document.getElementById('file-name').textContent = file.name;
    document.getElementById('file-size').textContent = formatFileSize(file.size);
    
    // Show file info and upload section
    document.getElementById('upload-area').style.display = 'none';
    document.getElementById('file-info').classList.remove('hidden');
    document.getElementById('upload-section').classList.remove('hidden');
    
    // Update step indicator
    updateStepIndicator(2);
}

function removeFile() {
    document.getElementById('csv-file').value = '';
    document.getElementById('upload-area').style.display = 'block';
    document.getElementById('file-info').classList.add('hidden');
    document.getElementById('upload-section').classList.add('hidden');
    
    // Reset step indicator
    updateStepIndicator(1);
}

function uploadFile() {
    const fileInput = document.getElementById('csv-file');
    const file = fileInput.files[0];
    
    if (!file) {
        showToast('Please select a file first', 'error');
        return;
    }
    
    // Show progress
    document.getElementById('upload-section').classList.add('hidden');
    document.getElementById('progress-section').classList.remove('hidden');
    
    // Create form data
    const formData = new FormData();
    formData.append('csv_file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
    
    // Simulate progress
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        updateProgress(progress);
    }, 200);
    
    // Upload file
    fetch('{{ route("recertification.migrate.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        updateProgress(100);
        
        setTimeout(() => {
            document.getElementById('progress-section').classList.add('hidden');
            showResults(data);
            updateStepIndicator(3);
            
            // Reload applications data to show newly migrated records
            if (data.success && data.success_count > 0) {
                setTimeout(() => {
                    loadApplicationsData();
                }, 1000);
            }
        }, 500);
    })
    .catch(error => {
        clearInterval(progressInterval);
        console.error('Upload error:', error);
        document.getElementById('progress-section').classList.add('hidden');
        document.getElementById('upload-section').classList.remove('hidden');
        showToast('Upload failed. Please try again.', 'error');
    });
}

function updateProgress(percent) {
    document.getElementById('progress-bar').style.width = percent + '%';
    document.getElementById('progress-text').textContent = Math.round(percent) + '%';
}

function showResults(data) {
    const resultsSection = document.getElementById('results-section');
    const successCount = document.getElementById('success-count');
    const errorCount = document.getElementById('error-count');
    const totalCount = document.getElementById('total-count');
    const successMessage = document.getElementById('success-message');
    const errorDetails = document.getElementById('error-details');
    const errorList = document.getElementById('error-list');
    const successText = document.getElementById('success-text');
    
    // Show results section
    resultsSection.classList.remove('hidden');
    
    // Update counts
    successCount.textContent = data.success_count || 0;
    errorCount.textContent = data.error_count || 0;
    totalCount.textContent = (data.success_count || 0) + (data.error_count || 0);
    
    if (data.success) {
        // Show success message
        successMessage.classList.remove('hidden');
        successText.textContent = data.message;
        showToast(data.message, 'success');
        
        // Show errors if any
        if (data.errors && data.errors.length > 0) {
            errorDetails.classList.remove('hidden');
            errorList.innerHTML = data.errors.map(error => 
                `<div class="error-item">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-circle" class="h-4 w-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                        <span class="text-red-800 text-sm">${error}</span>
                    </div>
                </div>`
            ).join('');
            
            // Reinitialize icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    } else {
        // Show error message
        showToast(data.message || 'Migration failed', 'error');
        
        if (data.errors && data.errors.length > 0) {
            errorDetails.classList.remove('hidden');
            errorList.innerHTML = data.errors.map(error => 
                `<div class="error-item">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-circle" class="h-4 w-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                        <span class="text-red-800 text-sm">${error}</span>
                    </div>
                </div>`
            ).join('');
            
            // Reinitialize icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }
    
    // Scroll to results
    resultsSection.scrollIntoView({ behavior: 'smooth' });
}

function resetImport() {
    // Reset all sections
    document.getElementById('results-section').classList.add('hidden');
    document.getElementById('progress-section').classList.add('hidden');
    document.getElementById('upload-section').classList.add('hidden');
    document.getElementById('file-info').classList.add('hidden');
    document.getElementById('upload-area').style.display = 'block';
    
    // Clear file input
    document.getElementById('csv-file').value = '';
    
    // Reset step indicator
    updateStepIndicator(1);
    
    // Clear results
    document.getElementById('success-message').classList.add('hidden');
    document.getElementById('error-details').classList.add('hidden');
}

function updateStepIndicator(activeStep) {
    for (let i = 1; i <= 3; i++) {
        const step = document.getElementById(`step-${i}`);
        step.classList.remove('active', 'completed');
        
        if (i < activeStep) {
            step.classList.add('completed');
            step.innerHTML = '<i data-lucide="check" class="h-4 w-4"></i>';
        } else if (i === activeStep) {
            step.classList.add('active');
            step.textContent = i;
        } else {
            step.textContent = i;
        }
    }
    
    // Reinitialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
window.removeToast = removeToast;
window.switchTab = switchTab;
window.loadApplicationsData = loadApplicationsData;
</script>

@endsection