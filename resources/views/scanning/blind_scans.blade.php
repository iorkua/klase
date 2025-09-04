@extends('layouts.app')
@section('page-title')
{{ __('Blind Scannings') }}
@endsection

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
// Configure PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>
@include('scanning.blind_scan.css')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include('admin.header')
    <!-- Dashboard Content -->
    <div class="p-4 sm:p-6">

        <div class="bg-white rounded-md shadow-sm border border-gray-200 p-4 sm:p-6">
            <!-- Page Header -->
            <!-- <div class="flex flex-col space-y-2">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Blind Scannings</h1>
                <p class="text-muted-foreground text-sm sm:text-base">Upload raw scanned documents </p>
            </div> -->



            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-8">
                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">

                    <!-- Left Panel - File Operations -->
                    <div class="space-y-4 sm:space-y-6">

                        <!-- File Number Information Section -->
                        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-200">
                            <div class="flex items-center space-x-2 mb-4">
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-800">File Number Information
                                </h3>
                            </div>

                            <!-- Include the reusable File Number Information component -->
                            @include('components.file_number_info')

                            <!-- Instructions Panel -->
                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-sm">
                                        <h4 class="font-medium text-blue-900 mb-1">Blind Scanning Workflow</h4>
                                        <ol class="text-blue-800 space-y-1 text-xs">
                                            <li>1. Select file number above and click "Create Folder"</li>
                                            <li>2. Folders created in both storage and C:\BLIND_SCAN\[FileNo]\</li>
                                            <li>3. Scan documents directly to C: drive folders (A4/A3)</li>
                                            <li>4. Use "Browse Files" to select and upload scanned files</li>
                                            <li>5. Files will be organized and stored in the system</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="createBlindScanFolder()"
                                class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 text-sm sm:text-base">
                                Create Folder
                            </button>
                        </div>


                    </div>

                    <!-- Right Panel - Preview and File Management -->
                    <div class="space-y-4 sm:space-y-6">

                        <!-- Preview Section -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 sm:p-6 border-b border-gray-200 space-y-3 sm:space-y-0">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-purple-100 p-2 rounded-lg">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Preview Scans</h3>
                                </div>

                                <!-- Folder Type Selection Dropdown -->
                                <div
                                    class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                    <select id="folderTypeSelect"
                                        class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-0"
                                        onchange="updateFolderSelection()">
                                        <option value="">Select Document Type</option>
                                        <option value="A4">📄 A4 Documents</option>
                                        <option value="A3">📋 A3 Documents</option>

                                    </select>

                                    <button id="browseFolderBtn" onclick="browseForFiles()" disabled
                                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center justify-center space-x-2 whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <span>Browse Files</span>
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 sm:p-6">
                                <!-- File Input for browsing (hidden) -->
                                <input type="file" id="folderInput" multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif,.bmp,.gif" style="display: none;"
                                    onchange="handleFileSelection(event)">

                                <div id="previewArea"
                                    class="border-2 border-dashed border-gray-300 rounded-lg p-4 sm:p-8 text-center file-preview">
                                    <div id="defaultMessage" class="text-gray-500">
                                        <svg class="w-8 h-8 sm:w-12 sm:h-12 mx-auto mb-4 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <p class="text-base sm:text-lg font-medium mb-2">Select Files for Processing (A4
                                            & A3)</p>
                                        <p class="text-xs sm:text-sm text-gray-400">1. Select document type from
                                            dropdown above</p>
                                        <p class="text-xs sm:text-sm text-gray-400">2. Click "Browse Files" to select
                                            multiple files from your PC</p>
                                    </div>

                                    <!-- File List Area (initially hidden) -->
                                    <div id="fileListArea" class="hidden">
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                                            <h4 class="text-base sm:text-lg font-semibold text-gray-800">Selected Files
                                            </h4>
                                            <div
                                                class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-2">
                                                <span id="selectedFolderType"
                                                    class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full"></span>
                                                <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                                    💡 Click file to preview • Click ⭕ to select
                                                </div>
                                            </div>
                                        </div>

                                        <!-- File Management Actions -->
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 p-3 bg-gray-50 rounded-lg space-y-3 sm:space-y-0">
                                            <div
                                                class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                                <button id="selectAllBtn" onclick="selectAllFiles()"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-2 rounded-lg transition duration-200 flex items-center justify-center space-x-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Select All</span>
                                                </button>

                                                <button id="deselectAllBtn" onclick="deselectAllFiles()"
                                                    class="bg-gray-500 hover:bg-gray-600 text-white text-sm px-3 py-2 rounded-lg transition duration-200 flex items-center justify-center space-x-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    <span>Deselect All</span>
                                                </button>
                                            </div>

                                            <div
                                                class="flex items-center justify-center sm:justify-end space-x-2 text-sm text-gray-600">
                                                <span id="selectionSummary">0 of 0 files selected</span>
                                            </div>
                                        </div>

                                        <!-- File Grid -->
                                        <div id="fileGrid"
                                            class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4 max-h-96 overflow-y-auto">
                                            <!-- Files will be populated here -->
                                        </div>

                                        <!-- Summary -->
                                        <div
                                            class="mt-4 flex flex-col sm:flex-row sm:justify-between sm:items-center text-sm text-gray-600 bg-gray-50 rounded-lg p-3 space-y-2 sm:space-y-0">
                                            <span>Total Files: <span id="totalFileCount"
                                                    class="font-semibold">0</span></span>
                                            <span>Selected Files: <span id="selectedFileCount"
                                                    class="font-semibold">0</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Folder Paths Display Panel -->
                        <div id="folderPathsPanel"
                            class="bg-white rounded-xl shadow-lg border border-gray-200 hidden mt-4 sm:mt-6">
                            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-green-100 p-2 rounded-lg">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 5a2 2 0 012-2h2a2 2 0 012 2v0H8v0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Created Folder Paths
                                    </h3>
                                </div>
                                <button onclick="hideFolderPaths()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4 sm:p-6 space-y-4">
                                <div id="fileNumberDisplay" class="text-center">
                                    <span
                                        class="text-lg font-semibold text-blue-900 bg-blue-50 px-4 py-2 rounded-lg">File
                                        Number: <span id="displayedFileNumber">-</span></span>
                                </div>

                                <!-- Local Folder Instructions -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <h4 class="font-semibold text-blue-800">� Local Folder (Create on Your PC)
                                        </h4>
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        <div class="font-mono bg-white p-2 rounded border">
                                            <strong>Create This Path:</strong> <span id="localRootPath"
                                                class="text-blue-700">-</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <div class="font-mono bg-white p-2 rounded border">
                                                <strong>A4 Subfolder:</strong> <span id="localA4Path"
                                                    class="text-blue-700">-</span>
                                            </div>
                                            <div class="font-mono bg-white p-2 rounded border">
                                                <strong>A3 Subfolder:</strong> <span id="localA3Path"
                                                    class="text-blue-700">-</span>
                                            </div>
                                        </div>
                                        <div class="text-xs text-blue-600 bg-blue-100 p-2 rounded">
                                            💡 Manual creation required: Use Windows Explorer to create these folders on your local PC
                                        </div>
                                    </div>
                                </div>

                                <!-- Server Storage Path -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01">
                                            </path>
                                        </svg>
                                        <h4 class="font-semibold text-blue-800">🏢 Server Storage Directory</h4>
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        <div class="font-mono bg-white p-2 rounded border">
                                            <strong>Root:</strong> <span id="storageRootPath"
                                                class="text-blue-700">-</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <div class="font-mono bg-white p-2 rounded border">
                                                <strong>A4:</strong> <span id="storageA4Path"
                                                    class="text-blue-700">-</span>
                                            </div>
                                            <div class="font-mono bg-white p-2 rounded border">
                                                <strong>A3:</strong> <span id="storageA3Path"
                                                    class="text-blue-700">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row gap-2 pt-4">
                                    <button onclick="openLocalFolder()"
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        <span>Show Instructions</span>
                                    </button>
                                    <button onclick="copyLocalPath()"
                                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>Copy Path</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 1v6"></path>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Created Folder Paths</h3>
                <div class="flex-1"></div>
                <span id="folderFileNumber"
                    class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full font-medium"></span>
            </div>

            <div class="space-y-4 " >
                <!-- Local C: Drive Path -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <div class="bg-blue-600 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-blue-900 mb-2">📁 Local Scanning Location (C: Drive)</h4>
                            <div class="space-y-2 text-sm">
                                <div class="bg-white rounded p-2 border border-blue-200">
                                    <div class="font-medium text-gray-700 mb-1">Main Folder:</div>
                                    <div id="localMainPath"
                                        class="font-mono text-blue-800 text-xs sm:text-sm break-all"></div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="bg-white rounded p-2 border border-blue-200">
                                        <div class="font-medium text-gray-700 mb-1">A4 Documents:</div>
                                        <div id="localA4Path" class="font-mono text-blue-800 text-xs break-all"></div>
                                    </div>
                                    <div class="bg-white rounded p-2 border border-blue-200">
                                        <div class="font-medium text-gray-700 mb-1">A3 Documents:</div>
                                        <div id="localA3Path" class="font-mono text-blue-800 text-xs break-all"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-blue-700 bg-blue-100 p-2 rounded">
                                💡 Scan your documents directly to these folders using your scanner software
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Server Storage Path -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <div class="bg-green-600 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-green-900 mb-2">🗄️ Server Storage Location</h4>
                            <div class="space-y-2 text-sm">
                                <div class="bg-white rounded p-2 border border-green-200">
                                    <div class="font-medium text-gray-700 mb-1">Storage Path:</div>
                                    <div id="storageMainPath"
                                        class="font-mono text-green-800 text-xs sm:text-sm break-all"></div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="bg-white rounded p-2 border border-green-200">
                                        <div class="font-medium text-gray-700 mb-1">A4 Storage:</div>
                                        <div id="storageA4Path" class="font-mono text-green-800 text-xs break-all">
                                        </div>
                                    </div>
                                    <div class="bg-white rounded p-2 border border-green-200">
                                        <div class="font-medium text-gray-700 mb-1">A3 Storage:</div>
                                        <div id="storageA3Path" class="font-mono text-green-800 text-xs break-all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-green-700 bg-green-100 p-2 rounded">
                                📤 Files uploaded through this interface will be stored here automatically
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
@include('scanning.blind_scan.js_file_function')
@endsection