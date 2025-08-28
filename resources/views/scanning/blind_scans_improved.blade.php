@extends('layouts.app')
@section('page-title')
    {{ __('Blind Scannings') }}
@endsection
@section('content')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header --> 
        @include('admin.header') 
        <!-- Dashboard Content -->
        <div class="p-6">
            <div class="container mx-auto py-6 space-y-6">
                <!-- Page Header -->
                <div class="flex flex-col space-y-2">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Blind Scannings</h1>
                            <p class="text-gray-600">Upload raw scanned documents without indexing</p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-4">
                            <button class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="refresh-list-btn">
                                <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i>
                                Refresh
                            </button>
                            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="upload-blind-scan-btn">
                                <i data-lucide="upload" class="h-4 w-4 mr-2"></i>
                                Upload Blind Scan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <!-- Total Blind Scans -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i data-lucide="file-text" class="h-6 w-6 text-gray-400"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Blind Scans</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_blind_scans'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Scans -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i data-lucide="clock" class="h-6 w-6 text-orange-400"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Scans</dt>
                                        <dd class="text-lg font-medium text-orange-600">{{ $stats['pending_scans'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Converted Scans -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i data-lucide="check-circle" class="h-6 w-6 text-green-400"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Converted Scans</dt>
                                        <dd class="text-lg font-medium text-green-600">{{ $stats['converted_scans'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Uploads -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i data-lucide="calendar" class="h-6 w-6 text-blue-400"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Today's Uploads</dt>
                                        <dd class="text-lg font-medium text-blue-600">{{ $stats['today_uploads'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Filter Blind Scans</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="search-input" placeholder="Search by temp ID, filename...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="status-filter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="converted">Converted</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                                <input type="date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="date-from-filter">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                                <input type="date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="date-to-filter">
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="apply-filters-btn">Apply Filters</button>
                            <button class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="clear-filters-btn">Clear Filters</button>
                        </div>
                    </div>
                </div>

                <!-- Blind Scans List -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-medium text-gray-900">Blind Scans List</h2>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-500">Show:</span>
                                <select class="mt-1 block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="per-page-select">
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Loading State -->
                        <div id="loading-state" class="text-center py-8">
                            <div class="inline-flex items-center gap-2">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                                <span class="text-gray-600">Loading blind scans...</span>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div id="empty-state" class="text-center py-8 hidden">
                            <i data-lucide="inbox" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No blind scans found</p>
                        </div>

                        <!-- Blind Scans Table -->
                        <div id="blind-scans-table" class="hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <input type="checkbox" id="select-all-checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temp ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper Size</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded By</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="blind-scans-tbody" class="bg-white divide-y divide-gray-200">
                                        <!-- Dynamic content will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="pagination-container" class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing <span id="showing-from">0</span> to <span id="showing-to">0</span> of <span id="total-records">0</span> results
                                </div>
                                <div id="pagination-buttons" class="flex gap-2">
                                    <!-- Pagination buttons will be generated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Blind Scan Modal -->
        <div id="upload-blind-scan-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="modal-backdrop"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Upload Blind Scan
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">Upload scanned documents without indexing</p>
                                
                                <form id="upload-blind-scan-form" enctype="multipart/form-data" class="mt-4">
                                    <div class="space-y-4">
                                        <!-- File Upload Area -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Files</label>
                                            <div class="mt-2 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors cursor-pointer" id="file-drop-zone">
                                                <i data-lucide="upload" class="h-8 w-8 mx-auto text-gray-400 mb-2"></i>
                                                <p class="text-sm text-gray-600">Drag and drop files here, or click to browse</p>
                                                <p class="text-xs text-gray-500 mt-1">Supports PDF, JPG, PNG, TIFF (max 10MB each)</p>
                                                <input type="file" id="blind-scan-files" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.tiff" class="hidden">
                                            </div>
                                            <div id="selected-files-list" class="mt-4 space-y-2 hidden">
                                                <!-- Selected files will be shown here -->
                                            </div>
                                        </div>

                                        <!-- Document Details -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Paper Size</label>
                                                <select name="paper_size" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    <option value="">Select Paper Size</option>
                                                    <option value="A4">A4</option>
                                                    <option value="A5">A5</option>
                                                    <option value="A3">A3</option>
                                                    <option value="Letter">Letter</option>
                                                    <option value="Legal">Legal</option>
                                                    <option value="Custom">Custom</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Document Type</label>
                                                <select name="document_type" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    <option value="">Select Document Type</option>
                                                    <option value="Certificate">Certificate</option>
                                                    <option value="Deed">Deed</option>
                                                    <option value="Letter">Letter</option>
                                                    <option value="Application Form">Application Form</option>
                                                    <option value="Map">Map</option>
                                                    <option value="Survey Plan">Survey Plan</option>
                                                    <option value="Receipt">Receipt</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Notes -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                                            <textarea name="notes" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" rows="3" placeholder="Add any notes about these scans..."></textarea>
                                        </div>

                                        <!-- Upload Progress -->
                                        <div id="upload-progress" class="hidden">
                                            <div class="flex justify-between text-sm mb-2">
                                                <span>Uploading files...</span>
                                                <span id="upload-percentage">0%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="upload-progress-bar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" form="upload-blind-scan-form" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" id="submit-upload-btn">
                            <i data-lucide="upload" class="h-4 w-4 mr-2"></i>
                            Upload Files
                        </button>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" id="cancel-upload-btn">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Convert to Upload Modal -->
        <div id="convert-to-upload-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="convert-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="convert-modal-backdrop"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="convert-modal-title">
                                    Convert to Upload Workflow
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">Link this blind scan to an indexed file</p>
                                
                                <form id="convert-to-upload-form" class="mt-4">
                                    <div class="space-y-4">
                                        <input type="hidden" id="convert-blind-scan-id" name="blind_scan_id">
                                        
                                        <!-- File Selection -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Indexed File</label>
                                            <select id="file-indexing-select" name="file_indexing_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                                <option value="">Choose an indexed file...</option>
                                            </select>
                                        </div>

                                        <!-- Blind Scan Details -->
                                        <div id="blind-scan-details" class="bg-gray-50 p-4 rounded-lg">
                                            <h4 class="font-medium mb-2 text-gray-900">Blind Scan Details:</h4>
                                            <div class="text-sm space-y-1 text-gray-600">
                                                <p><strong>Temp ID:</strong> <span id="detail-temp-id">-</span></p>
                                                <p><strong>Filename:</strong> <span id="detail-filename">-</span></p>
                                                <p><strong>Document Type:</strong> <span id="detail-doc-type">-</span></p>
                                                <p><strong>Paper Size:</strong> <span id="detail-paper-size">-</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" form="convert-to-upload-form" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <i data-lucide="arrow-right" class="h-4 w-4 mr-2"></i>
                            Convert to Upload
                        </button>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" id="cancel-convert-btn">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>

    <style>
        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }

        .file-item .file-info {
            flex: 1;
        }

        .file-item .file-name {
            font-weight: 500;
            font-size: 14px;
            color: #374151;
        }

        .file-item .file-size {
            font-size: 12px;
            color: #6b7280;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-converted {
            background-color: #d1fae5;
            color: #059669;
        }

        .status-archived {
            background-color: #e5e7eb;
            color: #6b7280;
        }

        /* Modal animation */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 200ms ease-out, transform 200ms ease-out;
        }

        .modal-exit {
            opacity: 1;
            transform: scale(1);
        }

        .modal-exit-active {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 150ms ease-in, transform 150ms ease-in;
        }
    </style>

    <script>
        // Blind Scanning functionality
        class BlindScanningManager {
            constructor() {
                this.currentPage = 1;
                this.perPage = 15;
                this.filters = {};
                this.selectedFiles = [];
                this.init();
            }

            init() {
                this.bindEvents();
                this.loadBlindScans();
                this.loadIndexedFiles();
            }

            bindEvents() {
                // Upload modal
                document.getElementById('upload-blind-scan-btn').addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.showUploadModal();
                });

                document.getElementById('cancel-upload-btn').addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.hideUploadModal();
                });

                // Modal backdrop clicks
                document.getElementById('modal-backdrop').addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.hideUploadModal();
                });

                document.getElementById('convert-modal-backdrop').addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.hideConvertModal();
                });

                // File drop zone
                const dropZone = document.getElementById('file-drop-zone');
                const fileInput = document.getElementById('blind-scan-files');

                dropZone.addEventListener('click', (e) => {
                    e.preventDefault();
                    fileInput.click();
                });

                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('border-blue-400', 'bg-blue-50');
                });

                dropZone.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
                });

                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
                    this.handleFileSelection(e.dataTransfer.files);
                });

                fileInput.addEventListener('change', (e) => {
                    this.handleFileSelection(e.target.files);
                });

                // Upload form
                document.getElementById('upload-blind-scan-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.uploadBlindScans();
                });

                // Filters
                document.getElementById('apply-filters-btn').addEventListener('click', () => {
                    this.applyFilters();
                });

                document.getElementById('clear-filters-btn').addEventListener('click', () => {
                    this.clearFilters();
                });

                // Refresh
                document.getElementById('refresh-list-btn').addEventListener('click', () => {
                    this.loadBlindScans();
                });

                // Per page change
                document.getElementById('per-page-select').addEventListener('change', (e) => {
                    this.perPage = parseInt(e.target.value);
                    this.currentPage = 1;
                    this.loadBlindScans();
                });

                // Convert modal
                document.getElementById('cancel-convert-btn').addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.hideConvertModal();
                });

                document.getElementById('convert-to-upload-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.convertToUpload();
                });

                // Prevent modal auto-close on form interactions
                document.getElementById('upload-blind-scan-modal').addEventListener('click', (e) => {
                    e.stopPropagation();
                });

                document.getElementById('convert-to-upload-modal').addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }

            showUploadModal() {
                const modal = document.getElementById('upload-blind-scan-modal');
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                
                // Focus management
                setTimeout(() => {
                    const firstInput = modal.querySelector('input, select, textarea, button');
                    if (firstInput) firstInput.focus();
                }, 100);
            }

            hideUploadModal() {
                const modal = document.getElementById('upload-blind-scan-modal');
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                this.resetUploadForm();
            }

            resetUploadForm() {
                document.getElementById('upload-blind-scan-form').reset();
                document.getElementById('selected-files-list').classList.add('hidden');
                document.getElementById('upload-progress').classList.add('hidden');
                this.selectedFiles = [];
            }

            handleFileSelection(files) {
                this.selectedFiles = Array.from(files);
                this.displaySelectedFiles();
            }

            displaySelectedFiles() {
                const container = document.getElementById('selected-files-list');
                
                if (this.selectedFiles.length === 0) {
                    container.classList.add('hidden');
                    return;
                }

                container.classList.remove('hidden');
                container.innerHTML = '';

                this.selectedFiles.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.innerHTML = `
                        <div class="file-info">
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${this.formatFileSize(file.size)}</div>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 p-1" onclick="blindScanningManager.removeFile(${index})">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    `;
                    container.appendChild(fileItem);
                });

                // Re-initialize Lucide icons
                lucide.createIcons();
            }

            removeFile(index) {
                this.selectedFiles.splice(index, 1);
                this.displaySelectedFiles();
            }

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            async uploadBlindScans() {
                if (this.selectedFiles.length === 0) {
                    this.showAlert('Please select at least one file to upload.', 'error');
                    return;
                }

                const formData = new FormData();
                const form = document.getElementById('upload-blind-scan-form');
                
                // Add files
                this.selectedFiles.forEach(file => {
                    formData.append('files[]', file);
                });

                // Add other form data
                formData.append('paper_size', form.paper_size.value);
                formData.append('document_type', form.document_type.value);
                formData.append('notes', form.notes.value);

                // Show progress
                document.getElementById('upload-progress').classList.remove('hidden');
                document.getElementById('submit-upload-btn').disabled = true;

                try {
                    const response = await fetch('/blind-scanning/store', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showAlert(result.message, 'success');
                        this.hideUploadModal();
                        this.loadBlindScans();
                    } else {
                        this.showAlert(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    this.showAlert('Upload failed. Please try again.', 'error');
                } finally {
                    document.getElementById('upload-progress').classList.add('hidden');
                    document.getElementById('submit-upload-btn').disabled = false;
                }
            }

            async loadBlindScans() {
                this.showLoading();

                try {
                    const params = new URLSearchParams({
                        page: this.currentPage,
                        per_page: this.perPage,
                        ...this.filters
                    });

                    const response = await fetch(`/blind-scanning/list?${params}`);
                    const result = await response.json();

                    if (result.success) {
                        this.displayBlindScans(result.data);
                        this.updatePagination(result.pagination);
                    } else {
                        this.showAlert(result.message, 'error');
                        this.showEmpty();
                    }
                } catch (error) {
                    console.error('Load error:', error);
                    this.showAlert('Failed to load blind scans.', 'error');
                    this.showEmpty();
                }
            }

            displayBlindScans(blindScans) {
                const tbody = document.getElementById('blind-scans-tbody');
                
                if (blindScans.length === 0) {
                    this.showEmpty();
                    return;
                }

                this.hideLoading();
                this.hideEmpty();
                document.getElementById('blind-scans-table').classList.remove('hidden');

                tbody.innerHTML = '';

                blindScans.forEach(scan => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded scan-checkbox" value="${scan.id}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${scan.temp_file_id}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${scan.original_filename}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${scan.document_type || '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${scan.paper_size || '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-badge status-${scan.status}">
                                ${scan.status.charAt(0).toUpperCase() + scan.status.slice(1)}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${new Date(scan.created_at).toLocaleDateString()}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${scan.uploader ? scan.uploader.name : 'Unknown'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-900 p-1" onclick="blindScanningManager.viewBlindScan(${scan.id})" title="View">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </button>
                                ${scan.status === 'pending' ? `
                                    <button class="text-green-600 hover:text-green-900 p-1" onclick="blindScanningManager.showConvertModal(${scan.id})" title="Convert">
                                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                                    </button>
                                ` : ''}
                                <button class="text-red-600 hover:text-red-900 p-1" onclick="blindScanningManager.deleteBlindScan(${scan.id})" title="Delete">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                // Re-initialize Lucide icons
                lucide.createIcons();
            }

            showLoading() {
                document.getElementById('loading-state').classList.remove('hidden');
                document.getElementById('empty-state').classList.add('hidden');
                document.getElementById('blind-scans-table').classList.add('hidden');
            }

            hideLoading() {
                document.getElementById('loading-state').classList.add('hidden');
            }

            showEmpty() {
                this.hideLoading();
                document.getElementById('empty-state').classList.remove('hidden');
                document.getElementById('blind-scans-table').classList.add('hidden');
            }

            hideEmpty() {
                document.getElementById('empty-state').classList.add('hidden');
            }

            updatePagination(pagination) {
                document.getElementById('showing-from').textContent = ((pagination.current_page - 1) * pagination.per_page) + 1;
                document.getElementById('showing-to').textContent = Math.min(pagination.current_page * pagination.per_page, pagination.total);
                document.getElementById('total-records').textContent = pagination.total;

                // Generate pagination buttons
                const container = document.getElementById('pagination-buttons');
                container.innerHTML = '';

                if (pagination.current_page > 1) {
                    const prevBtn = document.createElement('button');
                    prevBtn.className = 'inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
                    prevBtn.innerHTML = '<i data-lucide="chevron-left" class="h-4 w-4"></i>';
                    prevBtn.onclick = () => this.goToPage(pagination.current_page - 1);
                    container.appendChild(prevBtn);
                }

                if (pagination.current_page < pagination.last_page) {
                    const nextBtn = document.createElement('button');
                    nextBtn.className = 'inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
                    nextBtn.innerHTML = '<i data-lucide="chevron-right" class="h-4 w-4"></i>';
                    nextBtn.onclick = () => this.goToPage(pagination.current_page + 1);
                    container.appendChild(nextBtn);
                }

                // Re-initialize Lucide icons
                lucide.createIcons();
            }

            goToPage(page) {
                this.currentPage = page;
                this.loadBlindScans();
            }

            applyFilters() {
                this.filters = {
                    search: document.getElementById('search-input').value,
                    status: document.getElementById('status-filter').value,
                    date_from: document.getElementById('date-from-filter').value,
                    date_to: document.getElementById('date-to-filter').value,
                };
                this.currentPage = 1;
                this.loadBlindScans();
            }

            clearFilters() {
                document.getElementById('search-input').value = '';
                document.getElementById('status-filter').value = '';
                document.getElementById('date-from-filter').value = '';
                document.getElementById('date-to-filter').value = '';
                this.filters = {};
                this.currentPage = 1;
                this.loadBlindScans();
            }

            async showConvertModal(blindScanId) {
                try {
                    // Get blind scan details
                    const response = await fetch(`/blind-scanning/${blindScanId}`);
                    const result = await response.json();

                    if (result.success) {
                        const scan = result.data;
                        
                        // Populate modal
                        document.getElementById('convert-blind-scan-id').value = blindScanId;
                        document.getElementById('detail-temp-id').textContent = scan.temp_file_id;
                        document.getElementById('detail-filename').textContent = scan.original_filename;
                        document.getElementById('detail-doc-type').textContent = scan.document_type || '-';
                        document.getElementById('detail-paper-size').textContent = scan.paper_size || '-';

                        // Show modal
                        const modal = document.getElementById('convert-to-upload-modal');
                        modal.classList.remove('hidden');
                        modal.setAttribute('aria-hidden', 'false');
                        
                        // Focus management
                        setTimeout(() => {
                            const firstInput = modal.querySelector('select');
                            if (firstInput) firstInput.focus();
                        }, 100);
                    } else {
                        this.showAlert(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Error loading blind scan details:', error);
                    this.showAlert('Failed to load blind scan details.', 'error');
                }
            }

            hideConvertModal() {
                const modal = document.getElementById('convert-to-upload-modal');
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
            }

            async loadIndexedFiles() {
                try {
                    const response = await fetch('/fileindexing/list');
                    const result = await response.json();

                    if (result.success) {
                        const select = document.getElementById('file-indexing-select');
                        select.innerHTML = '<option value="">Choose an indexed file...</option>';

                        result.data.forEach(file => {
                            const option = document.createElement('option');
                            option.value = file.id;
                            option.textContent = `${file.file_number} - ${file.file_title}`;
                            select.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Error loading indexed files:', error);
                }
            }

            async convertToUpload() {
                const form = document.getElementById('convert-to-upload-form');
                const formData = new FormData(form);

                try {
                    const response = await fetch('/blind-scanning/convert-to-upload', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showAlert(result.message, 'success');
                        this.hideConvertModal();
                        this.loadBlindScans();
                    } else {
                        this.showAlert(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Convert error:', error);
                    this.showAlert('Conversion failed. Please try again.', 'error');
                }
            }

            async deleteBlindScan(id) {
                if (!confirm('Are you sure you want to delete this blind scan? This action cannot be undone.')) {
                    return;
                }

                try {
                    const response = await fetch(`/blind-scanning/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showAlert(result.message, 'success');
                        this.loadBlindScans();
                    } else {
                        this.showAlert(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    this.showAlert('Delete failed. Please try again.', 'error');
                }
            }

            viewBlindScan(id) {
                // Open blind scan in new tab/window for viewing
                window.open(`/blind-scanning/${id}/view`, '_blank');
            }

            showAlert(message, type = 'info') {
                // Create and show alert notification
                const alertDiv = document.createElement('div');
                alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
                    type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
                    type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
                    'bg-blue-100 text-blue-800 border border-blue-200'
                }`;
                alertDiv.innerHTML = `
                    <div class="flex items-center">
                        <span class="flex-1">${message}</span>
                        <button class="ml-4 text-current opacity-70 hover:opacity-100 p-1" onclick="this.parentElement.parentElement.remove()">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>
                `;
                document.body.appendChild(alertDiv);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentElement) {
                        alertDiv.remove();
                    }
                }, 5000);

                // Re-initialize Lucide icons
                lucide.createIcons();
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.blindScanningManager = new BlindScanningManager();
        });
    </script>
@endsection