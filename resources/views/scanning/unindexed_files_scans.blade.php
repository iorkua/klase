@extends('layouts.app')
@section('page-title')
    {{ __('Page Typing Dashboard') }}
@endsection

@section('content')
    @include('scanning.assets.unindexed_files_scans_css')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">
          
            <div class="container mx-auto py-6 space-y-6 px-4">
                Page Header 
               <div class="mb-8">
                   <h1 class="text-3xl font-bold text-gray-900">File Upload</h1>
                   <p class="text-gray-600 mt-2">Upload digital files to the registry</p>
               </div>
       
                <!-- Stats Cards  -->
               <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                   <div class="bg-white rounded-lg border shadow-sm">
                       <div class="p-4 pb-2">
                           <h3 class="text-sm font-medium text-gray-600">Today's Uploads</h3>
                       </div>
                       <div class="p-4 pt-0">
                           <div class="text-2xl font-bold" id="todaysUploads">0</div>
                           <p class="text-xs text-gray-500 mt-1">Files uploaded today</p>
                       </div>
                   </div>
                   <div class="bg-white rounded-lg border shadow-sm">
                       <div class="p-4 pb-2">
                           <h3 class="text-sm font-medium text-gray-600">Pending Indexing</h3>
                       </div>
                       <div class="p-4 pt-0">
                           <div class="text-2xl font-bold" id="pendingIndexing">0</div>
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
       
                <!-- Tabs  -->
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
       
                    <!-- Upload Tab Content  -->
                   <div id="upload-tab" class="tab-content mt-6">
                       <div class="bg-white rounded-lg border shadow-sm">
                           <div class="p-6 border-b">
                               <h3 class="text-lg font-semibold">Upload Files</h3>
                               <p class="text-gray-600 text-sm mt-1">Upload digital files to the registry</p>
                           </div>
                           <div class="p-6">
                               <div class="space-y-6">
                                    Upload Area 
                                   <div id="upload-area" class="rounded-md border-2 border-dashed border-gray-300 p-8 text-center hover:border-blue-400 transition-colors cursor-pointer">
                                       <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                                           <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                           </svg>
                                       </div>
                                       <h3 class="mb-2 text-lg font-medium">Drag and drop files here</h3>
                                       <p class="mb-4 text-sm text-gray-500">or click to browse files on your computer</p>
                                       <input type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.gif,.bmp,.tiff,.webp" class="hidden" id="file-upload">
                                       <button class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors" onclick="document.getElementById('file-upload').click()">
                                           <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                           </svg>
                                           Browse Files
                                       </button>
                                       <p class="text-xs text-gray-500 mt-2">
                                           Supported formats: PDF, JPG, PNG, GIF, BMP, TIFF, WebP (OCR enabled for scanned documents)
                                       </p>
                                   </div>
       
                                    <!-- Selected Files  -->
                                   <div id="selected-files" class="hidden rounded-md border divide-y">
                                       <div class="p-3 bg-gray-50 flex justify-between items-center">
                                           <span class="font-medium" id="selected-count">0 files selected</span>
                                           <button class="text-sm text-gray-600 hover:text-gray-800" onclick="clearAllFiles()">Clear All</button>
                                       </div>
                                       <div id="selected-files-list"></div>
                                   </div>
       
                                    <!-- Upload Progress  -->
                                   <div id="upload-progress" class="hidden space-y-2">
                                       <div class="flex justify-between text-sm">
                                           <span id="upload-progress-text">Uploading files...</span>
                                           <span id="upload-progress-percent">0%</span>
                                       </div>
                                       <div class="w-full bg-gray-200 rounded-full h-2">
                                           <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="upload-progress-bar" style="width: 0%"></div>
                                       </div>
                                   </div>
       
                                    <!-- Action Buttons  -->
                                   <div class="flex flex-col md:flex-row gap-4 justify-center">
                                       <button id="start-upload-btn" class="hidden inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors" onclick="startUpload()">
                                           <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                           </svg>
                                           Start Upload & Analysis
                                       </button>
                                       <button id="cancel-upload-btn" class="hidden inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors" onclick="cancelUpload()">
                                           <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                           </svg>
                                           Cancel
                                       </button>
                                       <button id="upload-more-btn" class="hidden inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors" onclick="resetUpload()">
                                           <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                           </svg>
                                           Upload More
                                       </button>
                                       <button id="view-files-btn" class="hidden inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors" onclick="switchToUploadedFiles()">
                                           <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                           </svg>
                                           View Uploaded Files
                                       </button>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
       
                    <!-- Uploaded Files Tab Content  -->
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
                                    Content will be populated by JavaScript 
                               </div>
                           </div>
                           <div id="uploaded-files-footer" class="hidden border-t p-6 flex justify-between">
                               <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors" onclick="switchToUpload()">Upload More</button>
                               <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors" onclick="sendToIndexing()">Send All to Indexing</button>
                           </div>
                       </div>
                   </div>
               </div>
       
                <!-- AI Processing Section  -->
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
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
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
                       
                        <!-- Analysis Results  -->
                       <div id="analysis-results" class="hidden mt-4 p-4 bg-white rounded-lg border shadow-sm">
                           <div class="flex justify-between items-center mb-4">
                               <h4 class="text-lg font-semibold text-gray-900">Document Analysis Results</h4>
                               <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800 border border-green-200" id="files-processed">0 files processed</span>
                           </div>
                           <div id="metadata-results" class="space-y-6"></div>
                           
                            Summary and Actions 
                           <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200">
                               <div class="flex items-center justify-between">
                                   <div>
                                       <h5 class="font-semibold text-gray-900 mb-1">Analysis Complete</h5>
                                       <p class="text-sm text-gray-600">
                                           All documents have been processed and are ready to be added to the File Indexing Assistant.
                                       </p>
                                   </div>
                                   <div class="flex gap-3">
                                       <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors" onclick="resetUpload()">
                                           Cancel
                                       </button>
                                       <button class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors" onclick="createIndexingEntries()">
                                           <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                           </svg>
                                           Create in File Indexing Assistant
                                       </button>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       
            <!-- OCR Processing Modal  -->
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
       
            <!-- Edit Metadata Modal  -->
           <div id="metadata-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
               <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                   <div class="flex justify-between items-center mb-4">
                       <h3 class="text-lg font-semibold" id="metadata-modal-title">Edit Document Metadata</h3>
                       <button class="text-gray-400 hover:text-gray-600" onclick="closeMetadataModal()">
                           <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                           </svg>
                       </button>
                   </div>
                   <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                       <div>
                           <h4 class="font-medium mb-2">Metadata Fields</h4>
                           <div id="metadata-form" class="space-y-4"></div>
                       </div>
                       <div>
                           <h4 class="font-medium mb-2">Document Preview</h4>
                           <div id="metadata-preview-content" class="document-preview border rounded-lg p-4 bg-gray-50">
                               <div id="pdf-preview-wrapper" class="relative hidden">
                                   <canvas id="pdf-preview-canvas" class="w-full h-auto border rounded-lg"></canvas>
                                   <p id="pdf-loading-placeholder" class="text-gray-500 text-center py-8 hidden">Loading PDF preview...</p>
                                   <div id="pdf-navigation-controls" class="flex justify-between items-center mt-4 hidden">
                                       <button id="prev-page-btn" class="px-3 py-1 bg-gray-200 rounded text-gray-700 hover:bg-gray-300 disabled:opacity-50" onclick="goToPreviousPage()" disabled>Previous</button>
                                       <span id="page-info" class="text-sm font-medium text-gray-700"></span>
                                       <button id="next-page-btn" class="px-3 py-1 bg-gray-200 rounded text-gray-700 hover:bg-gray-300 disabled:opacity-50" onclick="goToNextPage()" disabled>Next</button>
                                   </div>
                               </div>
                               <div id="image-preview-wrapper" class="hidden">
                                   <img id="image-preview-img" src="/placeholder.svg" alt="Document preview" class="max-w-full h-auto border rounded">
                                   <p id="image-loading-placeholder" class="text-gray-500 text-center py-8 hidden">Loading image preview...</p>
                               </div>
                               <p id="unsupported-preview-message" class="text-gray-500 hidden">Preview not available for this file type</p>
                           </div>
                           <h4 class="font-medium mb-2 mt-4">Extracted Text</h4>
                           <div class="document-preview border rounded-lg p-4 bg-white">
                               <pre id="metadata-extracted-text-preview" class="text-xs whitespace-pre-wrap text-gray-700"></pre>
                           </div>
                       </div>
                   </div>
                   <div class="flex justify-end gap-3 mt-6">
                       <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors" onclick="closeMetadataModal()">
                           Cancel
                       </button>
                       <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors" onclick="saveMetadata()">
                           Save Changes
                       </button>
                   </div>
               </div>
           </div>
       
        
        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>
    @include('scanning.assets.unindexed_files_scans_js')


 
   



  
@endsection
