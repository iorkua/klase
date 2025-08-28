@extends('layouts.app')
@section('page-title')
    {{ __('Unindexed File Upload') }}
@endsection
@section('content')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header --> 
        @include('admin.header') 
        <!-- Dashboard Content -->
        <div class="p-6">
            @include('scanning.assets.style')
            
            <div class="container mx-auto py-6 space-y-6">
                <!-- Page Header -->
                <div class="flex flex-col space-y-2">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('scanning.index') }}" class="btn btn-ghost btn-sm">
                            <i data-lucide="arrow-left" class="h-4 w-4 mr-1"></i>
                            Back to Scanning
                        </a>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight">Upload Unindexed Scanned Files</h1>
                    <p class="text-muted-foreground">Upload scanned documents without existing indexing records. The system will extract metadata and create indexing records automatically.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Today's Unindexed Uploads -->
                    <div class="card">
                        <div class="p-4 pb-2">
                            <h3 class="text-sm font-medium">Today's Unindexed Uploads</h3>
                        </div>
                        <div class="p-4 pt-0">
                            <div class="text-2xl font-bold" id="unindexed-uploads-count">{{ $stats['unindexed_uploads_today'] ?? 0 }}</div>
                            <p class="text-xs text-muted-foreground mt-1">Files processed today</p>
                        </div>
                    </div>

                    <!-- Pending Processing -->
                    <div class="card">
                        <div class="p-4 pb-2">
                            <h3 class="text-sm font-medium">Pending Processing</h3>
                        </div>
                        <div class="p-4 pt-0">
                            <div class="text-2xl font-bold" id="pending-processing-count">{{ $stats['pending_processing'] ?? 0 }}</div>
                            <p class="text-xs text-muted-foreground mt-1">Files awaiting metadata extraction</p>
                        </div>
                    </div>

                    <!-- Total Processed -->
                    <div class="card">
                        <div class="p-4 pb-2">
                            <h3 class="text-sm font-medium">Total Processed</h3>
                        </div>
                        <div class="p-4 pt-0">
                            <div class="text-2xl font-bold flex items-center">
                                {{ $stats['total_processed'] ?? 0 }}
                                <span class="badge ml-2 bg-green-500 text-white">Total</span>
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">All processed unindexed files</p>
                        </div>
                    </div>
                </div>

                <!-- Main Upload Card -->
                <div class="card">
                    <div class="p-6 border-b">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold">Upload Unindexed Documents</h2>
                                <p class="text-sm text-muted-foreground">Upload files and let the system create indexing records automatically</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge bg-blue-500 text-white">
                                    <i data-lucide="zap" class="h-3 w-3 mr-1"></i>
                                    Auto-Processing
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Upload area -->
                            <div class="border rounded-md p-4">
                                <h3 class="text-sm font-medium mb-4">Select Files to Process</h3>

                                <!-- Idle state -->
                                <div id="upload-idle" class="rounded-md border-2 border-dashed p-8 text-center">
                                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                                        <i data-lucide="file-plus" class="h-6 w-6"></i>
                                    </div>
                                    <h3 class="mb-2 text-lg font-medium">Drag and drop unindexed documents here</h3>
                                    <p class="mb-4 text-sm text-muted-foreground">System will extract metadata and create indexing records automatically</p>
                                    <input type="file" multiple class="hidden" id="file-upload" accept=".pdf,.jpg,.jpeg,.png,.tiff">
                                    <button class="btn btn-primary gap-2" id="browse-files-btn">
                                        <i data-lucide="upload" class="h-4 w-4"></i>
                                        Browse Files
                                    </button>
                                    <p class="mt-2 text-xs text-muted-foreground">Supported formats: PDF, JPG, PNG, TIFF (max 10MB each)</p>
                                </div>

                                <!-- Selected files list -->
                                <div id="selected-files-container" class="rounded-md border divide-y mt-4 hidden">
                                    <div class="p-3 bg-muted/50 flex justify-between items-center">
                                        <span class="font-medium"><span id="selected-files-count">0</span> files selected</span>
                                        <button class="btn btn-ghost btn-sm" id="clear-all-btn">Clear All</button>
                                    </div>
                                    <div id="selected-files-list">
                                        <!-- Files will be added here dynamically -->
                                    </div>
                                </div>

                                <!-- Processing state -->
                                <div id="processing-state" class="space-y-4 mt-4 hidden">
                                    <div class="flex justify-between text-sm">
                                        <span>Processing <span id="processing-count">0</span> files...</span>
                                        <span id="processing-percentage">0%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
                                    </div>
                                    <div class="text-xs text-muted-foreground space-y-1">
                                        <p>• Extracting metadata from documents</p>
                                        <p>• Creating indexing records</p>
                                        <p>• Organizing files by type and date</p>
                                    </div>
                                </div>

                                <!-- Complete state -->
                                <div id="processing-complete" class="mt-4 p-4 bg-green-50 border border-green-100 rounded-md hidden">
                                    <div class="flex items-center gap-2 text-green-700">
                                        <i data-lucide="check-circle" class="h-5 w-5"></i>
                                        <span class="font-medium">Processing Complete!</span>
                                    </div>
                                    <p class="text-sm text-green-700 mt-1">
                                        Files have been processed and indexing records created successfully.
                                    </p>
                                    <div class="mt-3 space-y-2">
                                        <div class="text-sm">
                                            <strong>Created Records:</strong>
                                            <ul class="list-disc list-inside mt-1 space-y-1" id="created-records-list">
                                                <!-- Will be populated dynamically -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action buttons -->
                            <div class="flex flex-col md:flex-row gap-4 justify-center">
                                <!-- Start processing button (idle state) -->
                                <button class="btn btn-primary gap-2 hidden" id="start-processing-btn">
                                    <i data-lucide="play" class="h-4 w-4"></i>
                                    Start Processing
                                </button>

                                <!-- Cancel button (processing state) -->
                                <button class="btn btn-destructive gap-2 hidden" id="cancel-processing-btn">
                                    <i data-lucide="alert-circle" class="h-4 w-4"></i>
                                    Cancel Processing
                                </button>

                                <!-- Complete state buttons -->
                                <button class="btn btn-outline gap-2 hidden" id="process-more-btn">
                                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                    Process More Files
                                </button>
                                <button class="btn btn-primary gap-2 hidden" id="view-created-files-btn">
                                    <i data-lucide="folder-open" class="h-4 w-4"></i>
                                    View Created Files
                                </button>
                                <a href="{{ route('pagetyping.index') }}" class="btn btn-primary gap-2 hidden" id="proceed-page-typing-btn">
                                    <i data-lucide="type" class="h-4 w-4"></i>
                                    Proceed to Page Typing
                                </a>
                                <!-- SCAN UPLOAD to Scanned Files action -->
                                <button class="btn btn-success gap-2 hidden" id="scan-upload-btn">
                                    <i data-lucide="scan" class="h-4 w-4"></i>
                                    SCAN UPLOAD to Scanned Files
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Analysis Results Section -->
                <div id="analysis-results-section" class="card hidden">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-semibold">Document Analysis Results</h2>
                                <p class="text-sm text-muted-foreground">Extracted metadata and document information</p>
                            </div>
                            <span class="badge bg-green-500 text-white" id="files-processed-badge">0 files processed</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="metadata-results" class="space-y-6">
                            <!-- Analysis results will be populated here -->
                        </div>
                        
                        <!-- Summary and Actions -->
                        <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="font-semibold text-gray-900 mb-1">Analysis Complete</h5>
                                    <p class="text-sm text-gray-600">
                                        All documents have been processed and are ready to be added to the File Indexing Assistant.
                                    </p>
                                </div>
                                <div class="flex gap-3">
                                    <button class="btn btn-outline" onclick="resetAnalysis()">
                                        Cancel
                                    </button>
                                    <button class="btn btn-primary gap-2" onclick="createIndexingEntries()">
                                        <i data-lucide="database" class="h-4 w-4"></i>
                                        Create in File Indexing Assistant
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Processed Files -->
                <div class="card">
                    <div class="p-6 border-b">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold">Recently Processed Files</h2>
                                <p class="text-sm text-muted-foreground">Files that have been processed and indexed</p>
                            </div>
                            <div class="relative w-full md:w-64">
                                <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                                <input type="search" placeholder="Search processed files..." class="input w-full pl-8" id="search-processed-files">
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if(isset($recentProcessed) && $recentProcessed->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated File No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Original Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Process Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed By</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="processed-files-list" class="bg-white divide-y divide-gray-200">
                                        @foreach($recentProcessed as $processed)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $processed->file_number ?? 'AUTO-' . str_pad($processed->id, 6, '0', STR_PAD_LEFT) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $processed->original_filename ?? 'Document' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $processed->document_type ?? 'Unknown' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $processed->created_at->format('M d, Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                                                        Processed
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $processed->uploader->name ?? 'System' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex items-center space-x-2">
                                                        <button class="text-indigo-600 hover:text-indigo-900" onclick="viewProcessedDocument({{ $processed->id }})">
                                                            <i data-lucide="eye" class="h-4 w-4 mr-1"></i>
                                                            View
                                                        </button>
                                                        <a href="{{ route('pagetyping.index', ['file_indexing_id' => $processed->id]) }}" class="text-green-600 hover:text-green-900">
                                                            <i data-lucide="type" class="h-4 w-4 mr-1"></i>
                                                            Page Type
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i data-lucide="inbox" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No processed files found</p>
                                <p class="text-sm text-gray-400 mt-1">Upload and process some unindexed files to see them here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('admin.footer')
        
        <!-- Document Text Extraction Modal -->
        <div id="text-extraction-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Document Text Extraction</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-100 rounded-full">
                            <i data-lucide="search" class="h-5 w-5 text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Extracting Text from Documents</h4>
                            <p class="text-sm text-muted-foreground" id="extraction-current-file">Processing documents...</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Extraction Progress</span>
                            <span id="extraction-progress-percent">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="extraction-progress-bar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="p-3 bg-muted/50 rounded text-sm">
                        <p class="font-medium mb-1">Processing:</p>
                        <ul class="space-y-1 text-muted-foreground">
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

        <!-- Unindexed Files JavaScript -->
        <script>
            // Initialize Lucide icons
            lucide.createIcons();

            // Application state
            let processingState = {
                selectedFiles: [],
                isProcessing: false,
                processedFiles: []
            };

            // File upload handlers
            document.getElementById('browse-files-btn').addEventListener('click', function() {
                document.getElementById('file-upload').click();
            });

            document.getElementById('file-upload').addEventListener('change', function(e) {
                handleFileSelection(e.target.files);
            });

            // Drag and drop handlers
            const uploadArea = document.getElementById('upload-idle');
            
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-blue-400', 'bg-blue-50');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-400', 'bg-blue-50');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-400', 'bg-blue-50');
                handleFileSelection(e.dataTransfer.files);
            });

            function handleFileSelection(files) {
                processingState.selectedFiles = Array.from(files);
                displaySelectedFiles();
            }

            function displaySelectedFiles() {
                const container = document.getElementById('selected-files-container');
                const list = document.getElementById('selected-files-list');
                const count = document.getElementById('selected-files-count');
                const startBtn = document.getElementById('start-processing-btn');

                if (processingState.selectedFiles.length === 0) {
                    container.classList.add('hidden');
                    startBtn.classList.add('hidden');
                    return;
                }

                container.classList.remove('hidden');
                startBtn.classList.remove('hidden');
                count.textContent = processingState.selectedFiles.length;

                list.innerHTML = '';
                processingState.selectedFiles.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'p-3 flex items-center justify-between';
                    fileItem.innerHTML = `
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="h-5 w-5 text-gray-400"></i>
                            <div>
                                <div class="font-medium text-sm">${file.name}</div>
                                <div class="text-xs text-gray-500">${formatFileSize(file.size)} • ${getFileType(file.name)}</div>
                            </div>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 p-1" onclick="removeFile(${index})">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    `;
                    list.appendChild(fileItem);
                });

                lucide.createIcons();
            }

            function removeFile(index) {
                processingState.selectedFiles.splice(index, 1);
                displaySelectedFiles();
            }

            // Start processing
            document.getElementById('start-processing-btn').addEventListener('click', function() {
                startProcessing();
            });

            function startProcessing() {
                if (processingState.selectedFiles.length === 0) return;

                processingState.isProcessing = true;

                // Hide file selection
                document.getElementById('selected-files-container').classList.add('hidden');
                document.getElementById('start-processing-btn').classList.add('hidden');

                // Show processing state
                document.getElementById('processing-state').classList.remove('hidden');
                document.getElementById('cancel-processing-btn').classList.remove('hidden');

                // Show text extraction modal
                showTextExtractionModal();

                // Simulate processing with real-like steps
                simulateProcessing();
            }

            function simulateProcessing() {
                let progress = 0;
                const totalFiles = processingState.selectedFiles.length;
                const steps = ['Uploading files...', 'Extracting metadata...', 'Creating indexing records...', 'Organizing files...', 'Finalizing...'];
                let currentStep = 0;

                const interval = setInterval(() => {
                    progress += Math.random() * 15 + 5; // Random progress between 5-20%
                    
                    if (progress > 100) progress = 100;

                    // Update progress display
                    document.getElementById('processing-percentage').textContent = Math.round(progress) + '%';
                    document.getElementById('progress-bar').style.width = progress + '%';
                    document.getElementById('processing-count').textContent = Math.ceil((progress / 100) * totalFiles);

                    // Update step description
                    const stepIndex = Math.floor((progress / 100) * steps.length);
                    if (stepIndex < steps.length && stepIndex !== currentStep) {
                        currentStep = stepIndex;
                        // Could update step description here if needed
                    }

                    if (progress >= 100) {
                        clearInterval(interval);
                        completeProcessing();
                    }
                }, 800);
            }

            function completeProcessing() {
                processingState.isProcessing = false;

                // Hide processing state
                document.getElementById('processing-state').classList.add('hidden');
                document.getElementById('cancel-processing-btn').classList.add('hidden');

                // Generate processed files data
                processingState.processedFiles = processingState.selectedFiles.map((file, index) => ({
                    id: Date.now() + index,
                    originalName: file.name,
                    generatedFileNo: `AUTO-${String(Date.now() + index).slice(-6)}`,
                    documentType: getDocumentType(file.name),
                    status: 'Processed'
                }));

                // Show complete state
                document.getElementById('processing-complete').classList.remove('hidden');
                document.getElementById('process-more-btn').classList.remove('hidden');
                document.getElementById('view-created-files-btn').classList.remove('hidden');
                document.getElementById('proceed-page-typing-btn').classList.remove('hidden');

                // Populate created records list
                const recordsList = document.getElementById('created-records-list');
                recordsList.innerHTML = '';
                processingState.processedFiles.forEach(file => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${file.generatedFileNo} - ${file.originalName} (${file.documentType})`;
                    recordsList.appendChild(listItem);
                });

                showNotification('Files processed successfully! Indexing records have been created.', 'success');
            }

            // Cancel processing
            document.getElementById('cancel-processing-btn').addEventListener('click', function() {
                if (confirm('Are you sure you want to cancel processing?')) {
                    cancelProcessing();
                }
            });

            function cancelProcessing() {
                processingState.isProcessing = false;

                // Hide processing state
                document.getElementById('processing-state').classList.add('hidden');
                document.getElementById('cancel-processing-btn').classList.add('hidden');

                // Show file selection again
                displaySelectedFiles();

                showNotification('Processing cancelled.', 'info');
            }

            // Process more files
            document.getElementById('process-more-btn').addEventListener('click', function() {
                // Reset to initial state
                document.getElementById('processing-complete').classList.add('hidden');
                document.getElementById('process-more-btn').classList.add('hidden');
                document.getElementById('view-created-files-btn').classList.add('hidden');
                document.getElementById('proceed-page-typing-btn').classList.add('hidden');

                processingState.selectedFiles = [];
                processingState.processedFiles = [];
                displaySelectedFiles();
            });

            // Clear all files
            document.getElementById('clear-all-btn').addEventListener('click', function() {
                processingState.selectedFiles = [];
                displaySelectedFiles();
            });

            // Utility functions
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function getFileType(filename) {
                const extension = filename.split('.').pop().toLowerCase();
                const types = {
                    'pdf': 'PDF Document',
                    'jpg': 'JPEG Image',
                    'jpeg': 'JPEG Image',
                    'png': 'PNG Image',
                    'tiff': 'TIFF Image',
                    'tif': 'TIFF Image'
                };
                return types[extension] || 'Unknown';
            }

            function getDocumentType(filename) {
                const name = filename.toLowerCase();
                if (name.includes('certificate')) return 'Certificate';
                if (name.includes('deed')) return 'Deed';
                if (name.includes('letter')) return 'Letter';
                if (name.includes('application')) return 'Application Form';
                if (name.includes('map')) return 'Map';
                if (name.includes('survey')) return 'Survey Plan';
                if (name.includes('receipt')) return 'Receipt';
                return 'Document';
            }

            function showNotification(message, type = 'info') {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-sm ${
                    type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' :
                    type === 'error' ? 'bg-red-50 border border-red-200 text-red-800' :
                    'bg-blue-50 border border-blue-200 text-blue-800'
                }`;
                
                notification.innerHTML = `
                    <div class="flex items-center gap-2">
                        <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'}" class="h-5 w-5"></i>
                        <span class="text-sm font-medium">${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>
                `;
                
                document.body.appendChild(notification);
                lucide.createIcons();
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            }

            // View processed document function
            function viewProcessedDocument(id) {
                showNotification('Document viewer will be implemented.', 'info');
            }

            // Text extraction modal functions
            function showTextExtractionModal() {
                const modal = document.getElementById('text-extraction-modal');
                if (modal) {
                    modal.classList.remove('hidden');
                    simulateTextExtraction();
                }
            }

            function hideTextExtractionModal() {
                const modal = document.getElementById('text-extraction-modal');
                if (modal) {
                    modal.classList.add('hidden');
                }
            }

            function simulateTextExtraction() {
                let extractionProgress = 0;
                const totalFiles = processingState.selectedFiles.length;
                let currentFileIndex = 0;

                const interval = setInterval(() => {
                    extractionProgress += Math.random() * 20 + 10; // Random progress between 10-30%
                    
                    if (extractionProgress > 100) extractionProgress = 100;

                    // Update extraction progress
                    document.getElementById('extraction-progress-percent').textContent = Math.round(extractionProgress) + '%';
                    document.getElementById('extraction-progress-bar').style.width = extractionProgress + '%';

                    // Update current file being processed
                    if (currentFileIndex < totalFiles) {
                        const currentFile = processingState.selectedFiles[currentFileIndex];
                        document.getElementById('extraction-current-file').textContent = `Processing: ${currentFile.name}`;
                        
                        if (extractionProgress > (currentFileIndex + 1) * (100 / totalFiles)) {
                            currentFileIndex++;
                        }
                    }

                    if (extractionProgress >= 100) {
                        clearInterval(interval);
                        setTimeout(() => {
                            hideTextExtractionModal();
                            showAnalysisResults();
                        }, 1000);
                    }
                }, 600);
            }

            // Analysis results functions
            function showAnalysisResults() {
                const analysisSection = document.getElementById('analysis-results-section');
                const metadataResults = document.getElementById('metadata-results');
                const filesProcessedBadge = document.getElementById('files-processed-badge');

                if (!analysisSection || !metadataResults || !filesProcessedBadge) return;

                analysisSection.classList.remove('hidden');
                filesProcessedBadge.textContent = `${processingState.selectedFiles.length} files processed`;

                // Generate mock analysis results
                const resultsHTML = processingState.selectedFiles.map((file, index) => 
                    generateMetadataResultHTML(file, index)
                ).join('');

                metadataResults.innerHTML = resultsHTML;
                
                // Show SCAN UPLOAD button
                document.getElementById('scan-upload-btn').classList.remove('hidden');
                
                lucide.createIcons();
            }

            function generateMetadataResultHTML(file, index) {
                const mockMetadata = generateMockMetadata(file.name);
                
                return `
                    <div class="border rounded-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h5 class="font-semibold text-gray-900">${file.name}</h5>
                                    <p class="text-sm text-gray-600 mt-1">Document successfully analyzed and processed</p>
                                </div>
                                <span class="badge ${mockMetadata.confidence > 70 ? 'bg-green-500' : 'bg-amber-500'} text-white">
                                    ${mockMetadata.confidence}% confidence
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h6 class="font-medium text-gray-900 border-b pb-2">File Numbers</h6>
                                    <div class="bg-muted/50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700">New File Number (KANGIS)</span>
                                            <span class="badge ${mockMetadata.fileNumberFound ? 'bg-green-500' : 'bg-red-500'} text-white text-xs">
                                                ${mockMetadata.fileNumberFound ? '✓ Detected' : '⚠ Not Found'}
                                            </span>
                                        </div>
                                        <div class="text-lg font-mono bg-white p-2 rounded border">
                                            ${mockMetadata.extractedFileNumber || 'No file number detected'}
                                        </div>
                                    </div>
                                    <div class="bg-muted/50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700">Property Owner</span>
                                            <span class="badge ${mockMetadata.ownerFound ? 'bg-green-500' : 'bg-red-500'} text-white text-xs">
                                                ${mockMetadata.ownerFound ? '✓ Detected' : '⚠ Not Found'}
                                            </span>
                                        </div>
                                        <div class="text-lg font-semibold bg-white p-2 rounded border">
                                            ${mockMetadata.detectedOwner || 'No owner detected'}
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <h6 class="font-medium text-gray-900 border-b pb-2">Property Information</h6>
                                    <div class="bg-muted/50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700">Plot No:</span>
                                            <span class="badge ${mockMetadata.plotNumberFound ? 'bg-green-500' : 'bg-red-500'} text-white text-xs">
                                                ${mockMetadata.plotNumberFound ? '✓ Detected' : '⚠ Not Found'}
                                            </span>
                                        </div>
                                        <div class="text-lg bg-white p-2 rounded border">
                                            ${mockMetadata.plotNumber || 'No plot number detected'}
                                        </div>
                                    </div>
                                    <div class="bg-muted/50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700">Land Use Type</span>
                                            <span class="badge ${mockMetadata.landUseFound ? 'bg-green-500' : 'bg-red-500'} text-white text-xs">
                                                ${mockMetadata.landUseFound ? '✓ Detected' : '⚠ Not Found'}
                                            </span>
                                        </div>
                                        <div class="text-lg bg-white p-2 rounded border">
                                            ${mockMetadata.landUseType ? `<span class="badge bg-blue-500 text-white">${mockMetadata.landUseType}</span>` : 'No land use detected'}
                                        </div>
                                    </div>
                                    <div class="bg-muted/50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700">District/Location</span>
                                            <span class="badge ${mockMetadata.districtFound ? 'bg-green-500' : 'bg-red-500'} text-white text-xs">
                                                ${mockMetadata.districtFound ? '✓ Detected' : '⚠ Not Found'}
                                            </span>
                                        </div>
                                        <div class="text-lg bg-white p-2 rounded border">
                                            ${mockMetadata.district || 'No district detected'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end mt-6 gap-3">
                                <button class="btn btn-outline btn-sm gap-2" onclick="previewDocument(${index})">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                    Preview Document
                                </button>
                                <button class="btn btn-outline btn-sm gap-2" onclick="editMetadata(${index})">
                                    <i data-lucide="edit" class="h-4 w-4"></i>
                                    Edit Metadata
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }

            function generateMockMetadata(filename) {
                // Generate realistic mock metadata based on filename
                const hasFileNumber = Math.random() > 0.3;
                const hasOwner = Math.random() > 0.2;
                const hasPlotNumber = Math.random() > 0.4;
                const hasLandUse = Math.random() > 0.5;
                const hasDistrict = Math.random() > 0.6;

                return {
                    extractedFileNumber: hasFileNumber ? `MLKN ${String(Math.floor(Math.random() * 999999)).padStart(6, '0')}` : '',
                    fileNumberFound: hasFileNumber,
                    detectedOwner: hasOwner ? `ALH. ${['IBRAHIM MOHAMMED', 'FATIMA HASSAN', 'AHMED SULEIMAN', 'KHADIJA USMAN'][Math.floor(Math.random() * 4)]}` : '',
                    ownerFound: hasOwner,
                    plotNumber: hasPlotNumber ? `${Math.floor(Math.random() * 999) + 1}/${Math.floor(Math.random() * 99) + 1}` : '',
                    plotNumberFound: hasPlotNumber,
                    landUseType: hasLandUse ? ['Commercial', 'Residential', 'Industrial'][Math.floor(Math.random() * 3)] : '',
                    landUseFound: hasLandUse,
                    district: hasDistrict ? ['FAGGE', 'NASARAWA', 'BOMPAI', 'KANO MUNICIPAL'][Math.floor(Math.random() * 4)] : '',
                    districtFound: hasDistrict,
                    confidence: Math.floor(Math.random() * 40) + 60 // 60-100%
                };
            }

            // Additional functions
            function previewDocument(index) {
                showNotification('Document preview will be implemented.', 'info');
            }

            function editMetadata(index) {
                showNotification('Metadata editor will be implemented.', 'info');
            }

            function resetAnalysis() {
                document.getElementById('analysis-results-section').classList.add('hidden');
                document.getElementById('scan-upload-btn').classList.add('hidden');
                showNotification('Analysis reset.', 'info');
            }

            function createIndexingEntries() {
                showNotification('Creating indexing entries...', 'success');
                setTimeout(() => {
                    showNotification('Indexing entries created successfully!', 'success');
                    resetAnalysis();
                }, 2000);
            }

            // SCAN UPLOAD button functionality
            document.getElementById('scan-upload-btn').addEventListener('click', function() {
                if (confirm('Upload processed files to Scanned Files section?')) {
                    showNotification('Files uploaded to Scanned Files successfully!', 'success');
                    // Reset the form after successful upload
                    setTimeout(() => {
                        resetAnalysis();
                        document.getElementById('processing-complete').classList.add('hidden');
                        document.getElementById('process-more-btn').classList.add('hidden');
                        document.getElementById('view-created-files-btn').classList.add('hidden');
                        document.getElementById('proceed-page-typing-btn').classList.add('hidden');
                        document.getElementById('scan-upload-btn').classList.add('hidden');
                        
                        processingState.selectedFiles = [];
                        processingState.processedFiles = [];
                        displaySelectedFiles();
                    }, 1500);
                }
            });

            // View Created Files button functionality
            document.getElementById('view-created-files-btn').addEventListener('click', function() {
                // Switch to the processed files table
                document.querySelector('html').scrollTo({
                    top: document.querySelector('#processed-files-list').offsetTop - 100,
                    behavior: 'smooth'
                });
                showNotification('Showing recently processed files below.', 'info');
            });
        </script>
    </div>
@endsection