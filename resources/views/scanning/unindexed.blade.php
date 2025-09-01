@extends('layouts.app')
@section('page-title')
    {{ __('Document Upload') }}
@endsection
@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://unpkg.com/tesseract.js@4/dist/tesseract.min.js"></script>
    <script src="/scanning/upload_handler.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header --> 
        @include('admin.header') 
        <!-- Dashboard Content -->
        <div class="p-6">
            <div class="container mx-auto py-6 space-y-6">
                <style>
                    .lucide {
                        width: 1em;
                        height: 1em;
                        display: inline-block;
                        vertical-align: middle;
                    }
                    .document-preview {
                        max-height: 400px;
                        overflow-y: auto;
                    }
                    .pdf-page {
                        margin-bottom: 10px;
                        border: 1px solid #e5e7eb;
                    }
                </style>
 
                <div class="container mx-auto py-6 space-y-6 px-4">
                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900">Unindexed File Upload</h1>
                        <p class="text-gray-600 mt-2">Upload and automatically index digital files</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white rounded-lg border shadow-sm">
                            <div class="p-4 pb-2">
                                <h3 class="text-sm font-medium text-gray-600">Today's Uploads</h3>
                            </div>
                            <div class="p-4 pt-0">
                                <div class="text-2xl font-bold" id="todaysUploads">{{ $stats['uploads_today'] ?? 0 }}</div>
                                <p class="text-xs text-gray-500 mt-1">Files uploaded today</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg border shadow-sm">
                            <div class="p-4 pb-2">
                                <h3 class="text-sm font-medium text-gray-600">Pending Indexing</h3>
                            </div>
                            <div class="p-4 pt-0">
                                <div class="text-2xl font-bold" id="pendingIndexing">{{ $stats['pending_indexing'] ?? 0 }}</div>
                                <p class="text-xs text-gray-500 mt-1">Files waiting to be indexed</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg border shadow-sm">
                            <div class="p-4 pb-2">
                                <h3 class="text-sm font-medium text-gray-600">Upload Status</h3>
                            </div>
                            <div class="p-4 pt-0">
                                <div class="text-2xl font-bold flex items-center">
                                    <span id="uploadStatusText">Ready</span>
                                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800" id="uploadStatusBadge">Ready</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Current upload status</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="w-full">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8">
                                <button class="tab-button active py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600" data-tab="upload">
                                    Upload Files
                                </button>
                                <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="uploaded-files">
                                    Uploaded Files
                                </button>
                            </nav>
                        </div>

                        <!-- Upload Tab Content -->
                        <div id="upload-tab" class="tab-content mt-6">
                            <div class="bg-white rounded-lg border shadow-sm">
                                <div class="p-6 border-b">
                                    <h3 class="text-lg font-semibold">Upload Unindexed Files</h3>
                                    <p class="text-gray-600 text-sm mt-1">Upload files that will be automatically analyzed and indexed</p>
                                </div>
                                <div class="p-6">
                                    <form id="unindexed-upload-form" enctype="multipart/form-data">
                                        @csrf
                                        <div class="space-y-6">
                                            <!-- Upload Area -->
                                            <div id="upload-area" class="rounded-md border-2 border-dashed border-gray-300 p-8 text-center hover:border-blue-400 transition-colors cursor-pointer">
                                                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                                                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="mb-2 text-lg font-medium">Drag and drop files here</h3>
                                                <p class="mb-4 text-sm text-gray-500">or click to browse files on your computer</p>
                                                <input type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.gif,.bmp,.tiff,.webp" class="hidden" id="file-upload" name="documents[]">
                                                <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors" onclick="document.getElementById('file-upload').click()">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                    </svg>
                                                    Browse Files
                                                </button>
                                                <p class="text-xs text-gray-500 mt-2">
                                                    Supported formats: PDF, JPG, PNG, GIF, BMP, TIFF, WebP (OCR enabled for scanned documents)
                                                </p>
                                            </div>

                                            <!-- Selected Files -->
                                            <div id="selected-files" class="hidden rounded-md border divide-y">
                                                <div class="p-3 bg-gray-50 flex justify-between items-center">
                                                    <span class="font-medium" id="selected-count">0 files selected</span>
                                                    <button type="button" class="text-sm text-gray-600 hover:text-gray-800" onclick="clearAllFiles()">Clear All</button>
                                                </div>
                                                <div id="selected-files-list"></div>
                                            </div>

                                            <!-- Upload Progress -->
                                            <div id="upload-progress" class="hidden space-y-2">
                                                <div class="flex justify-between text-sm">
                                                    <span id="upload-progress-text">Uploading files...</span>
                                                    <span id="upload-progress-percent">0%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="upload-progress-bar" style="width: 0%"></div>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex flex-col md:flex-row gap-4 justify-center">
                                                <button type="button" id="start-upload-btn" class="hidden inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                    </svg>
                                                    Start Upload & Analysis
                                                </button>
                                                <button type="button" id="cancel-upload-btn" class="hidden inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors" onclick="cancelUpload()">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Cancel
                                                </button>
                                                <button type="button" id="upload-more-btn" class="hidden inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors" onclick="resetUpload()">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                    Upload More
                                                </button>
                                                <button type="button" id="view-files-btn" class="hidden inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors" onclick="switchToUploadedFiles()">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    View Uploaded Files
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Uploaded Files Tab Content -->
                        <div id="uploaded-files-tab" class="tab-content mt-6 hidden">
                            <div class="bg-white rounded-lg border shadow-sm">
                                <div class="p-6 border-b">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div>
                                            <h3 class="text-lg font-semibold">Uploaded Files</h3>
                                            <p class="text-gray-600 text-sm mt-1">Recently uploaded files ready for processing</p>
                                        </div>
                                        <div class="relative w-full md:w-64">
                                            <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <input type="search" placeholder="Search files..." class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="file-search">
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <div id="uploaded-files-list">
                                        <!-- Content will be populated by JavaScript -->
                                    </div>
                                </div>
                                <div id="uploaded-files-footer" class="hidden border-t p-6 flex justify-between">
                                    <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors" onclick="switchToUpload()">Upload More</button>
                                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors" onclick="sendToIndexing()">Send All to Indexing</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Processing Section -->
                    <div id="ai-processing" class="hidden mt-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 bg-blue-100 rounded-full">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-blue-900">AI Document Analysis</h3>
                                <p class="text-sm text-blue-700">Extracting metadata for File Indexing Assistant</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium">Processing Progress</span>
                                <span class="text-sm font-medium" id="ai-progress-percent">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="ai-progress-bar" style="width: 0%"></div>
                            </div>
                            <div class="grid grid-cols-4 gap-2 mt-4" id="ai-stages">
                                <div class="text-center p-2 rounded bg-gray-100 text-gray-500" data-stage="analyzing">
                                    <svg class="h-4 w-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <div class="text-xs font-medium">Analyzing</div>
                                </div>
                                <div class="text-center p-2 rounded bg-gray-100 text-gray-500" data-stage="extracting">
                                    <svg class="h-4 w-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0112.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <div class="text-xs font-medium">Extracting</div>
                                </div>
                                <div class="text-center p-2 rounded bg-gray-100 text-gray-500" data-stage="creating">
                                    <svg class="h-4 w-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                    </svg>
                                    <div class="text-xs font-medium">Creating</div>
                                </div>
                                <div class="text-center p-2 rounded bg-gray-100 text-gray-500" data-stage="complete">
                                    <svg class="h-4 w-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-xs font-medium">Complete</div>
                                </div>
                            </div>
                            
                            <!-- Analysis Results -->
                            <div id="analysis-results" class="hidden mt-4 p-4 bg-white rounded-lg border shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-lg font-semibold text-gray-900">Document Analysis Results</h4>
                                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800 border border-green-200" id="files-processed">0 files processed</span>
                                </div>
                                <div id="metadata-results" class="space-y-6"></div>
                                
                                <!-- Summary and Actions -->
                                <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h5 class="font-semibold text-gray-900 mb-1">Analysis Complete</h5>
                                            <p class="text-sm text-gray-600">
                                                All documents have been processed and indexed successfully.
                                            </p>
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors" onclick="resetUpload()">
                                                Upload More
                                            </button>
                                            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors" onclick="goToPageTyping()">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Continue to Page Typing
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OCR Processing Modal -->
                <div id="ocr-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                        <h3 class="text-lg font-semibold mb-4">Document Text Extraction</h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-blue-100 rounded-full">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium">Extracting Text from Documents</h4>
                                    <p class="text-sm text-gray-600" id="ocr-current-file">Processing documents...</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Extraction Progress</span>
                                    <span id="ocr-progress-percent">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="ocr-progress-bar" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="p-3 bg-gray-100 rounded text-sm">
                                <p class="font-medium mb-1">Processing:</p>
                                <ul class="space-y-1 text-gray-600">
                                    <li>• Reading PDF pages</li>
                                    <li>• Converting pages to images</li>
                                    <li>• Running OCR on each page</li>
                                    <li>• Analyzing document structure</li>
                                    <li>• Searching for metadata patterns</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                @include('scanning.ocr_functions')
            </div>
        </div>
        <!-- Footer -->
        @include('admin.footer')
    </div>

    <script>
        // Initialize global variables in window scope
        window.selectedFiles = [];
        window.uploadStatus = 'idle';
        window.uploadProgress = 0;
        window.extractedMetadata = {};
        window.uploadedDocuments = [];
        window.aiProcessingStage = 'idle';
        window.aiProgress = 0;
        window.currentEditingFile = null;
        window.ocrProgress = 0;
        window.filteredFiles = [];
        window.currentPDFDocument = null;
        window.currentPageNumber = 1;
        window.uploadedFiles = [];

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeUploadArea();
            initializeTabs();
            loadUploadedFiles();
        });

        // Initialize upload area
        function initializeUploadArea() {
            const uploadArea = document.getElementById('upload-area');
            const fileInput = document.getElementById('file-upload');

            // Drag and drop events
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('border-blue-400', 'bg-blue-50');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
                
                const files = Array.from(e.dataTransfer.files);
                handleFileSelection(files);
            });

            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                handleFileSelection(files);
            });
        }

        // Initialize tabs
        function initializeTabs() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Update button states
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });
                    
                    this.classList.add('active', 'border-blue-500', 'text-blue-600');
                    this.classList.remove('border-transparent', 'text-gray-500');
                    
                    // Update content visibility
                    tabContents.forEach(content => {
                        if (content.id === targetTab + '-tab') {
                            content.classList.remove('hidden');
                        } else {
                            content.classList.add('hidden');
                        }
                    });

                    // Load data for specific tabs
                    if (targetTab === 'uploaded-files') {
                        loadUploadedFiles();
                    }
                });
            });
        }

        // Handle file selection
        function handleFileSelection(files) {
            const validFiles = files.filter(file => {
                const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp'];
                return validTypes.includes(file.type) && file.size <= 20 * 1024 * 1024; // 20MB
            });

            if (validFiles.length !== files.length) {
                showNotification('Some files were skipped due to invalid format or size (max 20MB)', 'warning');
            }

            selectedFiles = [...selectedFiles, ...validFiles];
            displaySelectedFiles();
            updateUploadButtons();
        }

        // Display selected files
        function displaySelectedFiles() {
            const container = document.getElementById('selected-files');
            const list = document.getElementById('selected-files-list');
            const count = document.getElementById('selected-count');

            if (selectedFiles.length === 0) {
                container.classList.add('hidden');
                return;
            }

            container.classList.remove('hidden');
            count.textContent = `${selectedFiles.length} files selected`;

            list.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'p-3 flex items-center justify-between hover:bg-gray-50';
                fileItem.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-sm">${file.name}</div>
                            <div class="text-xs text-gray-500">${formatFileSize(file.size)}</div>
                        </div>
                    </div>
                    <button type="button" class="text-red-500 hover:text-red-700 p-1" onclick="removeFile(${index})">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                list.appendChild(fileItem);
            });
        }

        // Remove file from selection
        function removeFile(index) {
            selectedFiles.splice(index, 1);
            displaySelectedFiles();
            updateUploadButtons();
        }

        // Clear all files
        function clearAllFiles() {
            selectedFiles = [];
            document.getElementById('file-upload').value = '';
            displaySelectedFiles();
            updateUploadButtons();
        }

        // Update upload buttons based on state
        function updateUploadButtons() {
            const startBtn = document.getElementById('start-upload-btn');
            const cancelBtn = document.getElementById('cancel-upload-btn');
            const uploadMoreBtn = document.getElementById('upload-more-btn');
            const viewFilesBtn = document.getElementById('view-files-btn');

            // Hide all buttons first
            [startBtn, cancelBtn, uploadMoreBtn, viewFilesBtn].forEach(btn => {
                if (btn) btn.classList.add('hidden');
            });

            if (uploadStatus === 'idle' && selectedFiles.length > 0) {
                if (startBtn) startBtn.classList.remove('hidden');
            } else if (uploadStatus === 'uploading') {
                if (cancelBtn) cancelBtn.classList.remove('hidden');
            } else if (uploadStatus === 'complete') {
                if (uploadMoreBtn) uploadMoreBtn.classList.remove('hidden');
                if (viewFilesBtn) viewFilesBtn.classList.remove('hidden');
            }
        }

        // Handle form submission
        document.getElementById('unindexed-upload-form').addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            startUpload();
            return false;
        });

        // Also add direct click handler to the button as backup
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'start-upload-btn') {
                e.preventDefault();
                e.stopPropagation();
                startUpload();
                return false;
            }
        });

        // Start upload process
        async function startUpload() {
            console.log('Starting upload process...'); // Debug log
            
            if (selectedFiles.length === 0) {
                showNotification('Please select files to upload', 'warning');
                return;
            }

            // Prevent multiple simultaneous uploads
            if (uploadStatus === 'uploading') {
                console.log('Upload already in progress, ignoring...');
                return;
            }

            uploadStatus = 'uploading';
            uploadProgress = 0;
            updateUploadStatus();
            updateUploadButtons();

            // Show progress bar
            const progressDiv = document.getElementById('upload-progress');
            if (progressDiv) progressDiv.classList.remove('hidden');

            // Show OCR modal
            document.getElementById('ocr-modal').classList.remove('hidden');

            try {
                // Process OCR and metadata extraction first
                await processOCRAndMetadata();

                // Hide OCR modal
                document.getElementById('ocr-modal').classList.add('hidden');

                // Show AI processing
                document.getElementById('ai-processing').classList.remove('hidden');

                // Update progress
                updateProgress(25, 'Preparing upload...');

                // Create FormData
                const formData = new FormData();
                
                // Add files
                selectedFiles.forEach((file, index) => {
                    formData.append(`documents[${index}]`, file);
                });
                
                // Add extracted metadata
                if (Object.keys(extractedMetadata).length > 0) {
                    formData.append('extracted_metadata', JSON.stringify(extractedMetadata));
                }

                // Update progress
                updateProgress(50, 'Uploading files...');

                console.log('Sending request to backend...'); // Debug log

                // Send to backend
                const response = await fetch('{{ route("scanning.upload-unindexed") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                console.log('Response received:', response.status); // Debug log

                const result = await response.json();
                console.log('Result:', result); // Debug log

                if (result.success) {
                    // Update progress
                    updateProgress(75, 'Creating index records...');
                    
                    uploadedDocuments = result.uploaded_documents || [];
                    
                    // Update progress
                    updateProgress(100, 'Upload complete!');
                    
                    // Show results
                    showAnalysisResults(result);
                    
                    uploadStatus = 'complete';
                    updateUploadStatus();
                    updateUploadButtons();
                    
                    showNotification(result.message, 'success');
                    
                    // Update stats
                    updateStats();
                    
                } else {
                    throw new Error(result.message || 'Upload failed');
                }

            } catch (error) {
                console.error('Upload error:', error);
                showNotification('Upload failed: ' + error.message, 'error');
                uploadStatus = 'idle';
                updateUploadStatus();
                updateUploadButtons();
                
                // Hide modals
                document.getElementById('ocr-modal').classList.add('hidden');
                document.getElementById('ai-processing').classList.add('hidden');
            }

            // Hide progress bar
            setTimeout(() => {
                if (progressDiv) progressDiv.classList.add('hidden');
            }, 2000);
        }

        // Process OCR and metadata extraction
        async function processOCRAndMetadata() {
            extractedMetadata = {};
            
            for (let i = 0; i < selectedFiles.length; i++) {
                const file = selectedFiles[i];
                
                // Update OCR modal
                document.getElementById('ocr-current-file').textContent = `Processing ${file.name}...`;
                updateOCRProgress((i / selectedFiles.length) * 100);
                
                try {
                    let extractedText = '';
                    
                    if (file.type === 'application/pdf') {
                        extractedText = await extractTextFromPDF(file);
                    } else if (file.type.startsWith('image/')) {
                        extractedText = await extractTextFromImage(file);
                    }
                    
                    // Extract metadata from text
                    const metadata = extractMetadataFromText(extractedText, file.name);
                    extractedMetadata[i] = metadata;
                    
                } catch (error) {
                    console.error(`Error processing ${file.name}:`, error);
                    extractedMetadata[i] = {
                        extractedFileNumber: '',
                        detectedOwner: file.name.replace(/\.[^/.]+$/, ''),
                        plotNumber: '',
                        landUseType: 'Unknown',
                        district: 'Unknown',
                        documentType: 'Document'
                    };
                }
            }
            
            updateOCRProgress(100);
        }

        // Extract text from PDF
        async function extractTextFromPDF(file) {
            return new Promise((resolve, reject) => {
                const fileReader = new FileReader();
                fileReader.onload = async function() {
                    try {
                        const pdf = await pdfjsLib.getDocument(this.result).promise;
                        let fullText = '';
                        
                        for (let i = 1; i <= pdf.numPages; i++) {
                            const page = await pdf.getPage(i);
                            const textContent = await page.getTextContent();
                            const pageText = textContent.items.map(item => item.str).join(' ');
                            fullText += pageText + ' ';
                        }
                        
                        resolve(fullText);
                    } catch (error) {
                        reject(error);
                    }
                };
                fileReader.readAsArrayBuffer(file);
            });
        }

        // Extract text from image using OCR
        async function extractTextFromImage(file) {
            try {
                const result = await Tesseract.recognize(file, 'eng');
                return result.data.text;
            } catch (error) {
                console.error('OCR Error:', error);
                return '';
            }
        }

        // Extract metadata from text
        function extractMetadataFromText(text, filename) {
            const metadata = {
                extractedFileNumber: '',
                detectedOwner: '',
                plotNumber: '',
                landUseType: 'Unknown',
                district: 'Unknown',
                documentType: 'Document'
            };

            if (!text) {
                metadata.detectedOwner = filename.replace(/\.[^/.]+$/, '');
                return metadata;
            }

            // Extract file number patterns
            const fileNumberPatterns = [
                /(?:File\s*No\.?\s*:?\s*)([A-Z0-9\-\/]+)/i,
                /(?:Reference\s*No\.?\s*:?\s*)([A-Z0-9\-\/]+)/i,
                /([A-Z]{2,4}\/\d{4,}\/[A-Z0-9]+)/i
            ];

            for (const pattern of fileNumberPatterns) {
                const match = text.match(pattern);
                if (match) {
                    metadata.extractedFileNumber = match[1].trim();
                    break;
                }
            }

            // Extract owner/grantee
            const ownerPatterns = [
                /(?:Grantee\s*:?\s*)([A-Z\s\.]+)/i,
                /(?:Name\s*:?\s*)([A-Z\s\.]+)/i,
                /(?:Owner\s*:?\s*)([A-Z\s\.]+)/i
            ];

            for (const pattern of ownerPatterns) {
                const match = text.match(pattern);
                if (match && match[1].length > 3) {
                    metadata.detectedOwner = match[1].trim();
                    break;
                }
            }

            // Extract plot number
            const plotPatterns = [
                /(?:Plot\s*No\.?\s*:?\s*)([A-Z0-9\-\/]+)/i,
                /(?:Plot\s*:?\s*)([A-Z0-9\-\/]+)/i
            ];

            for (const pattern of plotPatterns) {
                const match = text.match(pattern);
                if (match) {
                    metadata.plotNumber = match[1].trim();
                    break;
                }
            }

            // Extract location/district
            const locationPatterns = [
                /(?:Location\s*:?\s*)([A-Z\s]+)/i,
                /(?:District\s*:?\s*)([A-Z\s]+)/i,
                /(?:LGA\s*:?\s*)([A-Z\s]+)/i
            ];

            for (const pattern of locationPatterns) {
                const match = text.match(pattern);
                if (match && match[1].length > 2) {
                    metadata.district = match[1].trim();
                    break;
                }
            }

            // Determine document type
            const lowerText = text.toLowerCase();
            if (lowerText.includes('certificate of occupancy') || lowerText.includes('c of o')) {
                metadata.documentType = 'Certificate of Occupancy';
                metadata.landUseType = 'Residential';
            } else if (lowerText.includes('deed') || lowerText.includes('conveyance')) {
                metadata.documentType = 'Deed';
            } else if (lowerText.includes('survey') || lowerText.includes('plan')) {
                metadata.documentType = 'Survey Plan';
            }

            // Use filename as fallback for owner
            if (!metadata.detectedOwner) {
                metadata.detectedOwner = filename.replace(/\.[^/.]+$/, '');
            }

            return metadata;
        }

        // Update OCR progress
        function updateOCRProgress(percentage) {
            document.getElementById('ocr-progress-percent').textContent = Math.round(percentage) + '%';
            document.getElementById('ocr-progress-bar').style.width = percentage + '%';
        }

        // Update upload progress
        function updateProgress(percentage, text) {
            uploadProgress = percentage;
            document.getElementById('upload-progress-percent').textContent = percentage + '%';
            document.getElementById('upload-progress-bar').style.width = percentage + '%';
            document.getElementById('upload-progress-text').textContent = text;
            
            document.getElementById('ai-progress-percent').textContent = percentage + '%';
            document.getElementById('ai-progress-bar').style.width = percentage + '%';
            
            // Update AI stages
            const stages = document.querySelectorAll('#ai-stages [data-stage]');
            stages.forEach((stage, index) => {
                const stagePercentage = (index + 1) * 25;
                if (percentage >= stagePercentage) {
                    stage.classList.remove('bg-gray-100', 'text-gray-500');
                    stage.classList.add('bg-blue-100', 'text-blue-600');
                }
            });
        }

        // Show analysis results
        function showAnalysisResults(result) {
            const resultsDiv = document.getElementById('analysis-results');
            const metadataDiv = document.getElementById('metadata-results');
            const filesProcessedSpan = document.getElementById('files-processed');
            
            filesProcessedSpan.textContent = `${result.uploaded_documents.length} files processed`;
            
            metadataDiv.innerHTML = '';
            
            result.uploaded_documents.forEach((doc, index) => {
                const metadata = extractedMetadata[index] || {};
                
                const resultItem = document.createElement('div');
                resultItem.className = 'p-4 border rounded-lg bg-gray-50';
                resultItem.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <h5 class="font-medium text-gray-900">${doc.filename}</h5>
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">Indexed</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">File Number:</span>
                            <span class="font-medium ml-1">${doc.file_number}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Owner:</span>
                            <span class="font-medium ml-1">${metadata.detectedOwner || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Plot Number:</span>
                            <span class="font-medium ml-1">${metadata.plotNumber || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">District:</span>
                            <span class="font-medium ml-1">${metadata.district || 'N/A'}</span>
                        </div>
                    </div>
                `;
                metadataDiv.appendChild(resultItem);
            });
            
            resultsDiv.classList.remove('hidden');
        }

        // Update upload status
        function updateUploadStatus() {
            const statusText = document.getElementById('uploadStatusText');
            const statusBadge = document.getElementById('uploadStatusBadge');
            
            switch(uploadStatus) {
                case 'idle':
                    statusText.textContent = 'Ready';
                    statusBadge.textContent = 'Ready';
                    statusBadge.className = 'ml-2 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800';
                    break;
                case 'uploading':
                    statusText.textContent = 'Processing';
                    statusBadge.textContent = 'Processing';
                    statusBadge.className = 'ml-2 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800';
                    break;
                case 'complete':
                    statusText.textContent = 'Complete';
                    statusBadge.textContent = 'Complete';
                    statusBadge.className = 'ml-2 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800';
                    break;
            }
        }

        // Update statistics
        function updateStats() {
            // Update today's uploads count
            const todaysUploads = document.getElementById('todaysUploads');
            if (todaysUploads) {
                const currentCount = parseInt(todaysUploads.textContent) || 0;
                todaysUploads.textContent = currentCount + uploadedDocuments.length;
            }
        }

        // Cancel upload
        function cancelUpload() {
            uploadStatus = 'idle';
            updateUploadStatus();
            updateUploadButtons();
            
            // Hide modals and progress
            document.getElementById('ocr-modal').classList.add('hidden');
            document.getElementById('ai-processing').classList.add('hidden');
            document.getElementById('upload-progress').classList.add('hidden');
        }

        // Reset upload form
        function resetUpload() {
            selectedFiles = [];
            uploadedDocuments = [];
            extractedMetadata = {};
            uploadStatus = 'idle';
            
            clearAllFiles();
            updateUploadStatus();
            updateUploadButtons();
            
            // Hide modals and progress
            document.getElementById('ocr-modal').classList.add('hidden');
            document.getElementById('ai-processing').classList.add('hidden');
            document.getElementById('upload-progress').classList.add('hidden');
            document.getElementById('analysis-results').classList.add('hidden');
        }

        // Switch to uploaded files tab
        function switchToUploadedFiles() {
            document.querySelector('[data-tab="uploaded-files"]').click();
        }

        // Switch to upload tab
        function switchToUpload() {
            document.querySelector('[data-tab="upload"]').click();
        }

        // Go to page typing
        function goToPageTyping() {
            if (uploadedDocuments.length > 0) {
                const fileIndexingId = uploadedDocuments[0].file_indexing_id;
                window.location.href = `/pagetyping?file_indexing_id=${fileIndexingId}`;
            }
        }

        // Load uploaded files
        function loadUploadedFiles() {
            const uploadedFilesList = document.getElementById('uploaded-files-list');
            
            if (uploadedDocuments.length === 0) {
                uploadedFilesList.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-lg font-medium mb-2">No files uploaded yet</p>
                        <p class="text-sm">Upload some files to see them here.</p>
                    </div>
                `;
                return;
            }

            let tableHTML = `
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
            `;

            uploadedDocuments.forEach(doc => {
                tableHTML += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${doc.filename}</div>
                                    <div class="text-sm text-gray-500">${doc.type}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${doc.file_number}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatFileSize(doc.size)}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Indexed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button type="button" class="text-blue-600 hover:text-blue-900 mr-3" onclick="viewDocument('${doc.id}')">View</button>
                            <button type="button" class="text-green-600 hover:text-green-900" onclick="goToPageTyping()">Page Type</button>
                        </td>
                    </tr>
                `;
            });

            tableHTML += `
                    </tbody>
                </table>
            `;

            uploadedFilesList.innerHTML = tableHTML;
            document.getElementById('uploaded-files-footer').classList.remove('hidden');
        }

        // View document
        function viewDocument(documentId) {
            window.open(`/scanning/${documentId}`, '_blank');
        }

        // Send all to indexing
        function sendToIndexing() {
            if (uploadedDocuments.length > 0) {
                goToPageTyping();
            } else {
                showNotification('No files to send to indexing', 'warning');
            }
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' :
                type === 'error' ? 'bg-red-50 border border-red-200 text-red-800' :
                type === 'warning' ? 'bg-yellow-50 border border-yellow-200 text-yellow-800' :
                'bg-blue-50 border border-blue-200 text-blue-800'
            }`;
            
            notification.innerHTML = `
                <div class="flex">
                    <div class="flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <button type="button" class="ml-3 text-gray-400 hover:text-gray-600" onclick="this.parentElement.parentElement.remove()">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
@endsection