@extends('layouts.app')
@section('page-title')
    {{ __('Document Upload') }}
@endsection
@section('content')
 @include('printlabel.assets.style')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">
           
             
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Print File Labels</h1>
            <p class="text-muted-foreground">Generate and print labels for physical files</p>
        </div>
        <div class="flex gap-2">
            <div class="tooltip">
                <button id="history-btn" class="btn btn-outline btn-sm">
                    <i data-lucide="history" class="h-4 w-4"></i>
                </button>
                <div class="tooltip-content">Print History</div>
            </div>
            <div class="tooltip">
                <button id="reset-btn" class="btn btn-outline btn-sm">
                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                </button>
                <div class="tooltip-content">Reset Form</div>
            </div>
            <button id="print-labels-btn" class="btn btn-primary">Print Labels</button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card">
            <div class="p-6 pb-2">
                <h3 class="text-sm font-medium">Available Files</h3>
            </div>
            <div class="p-6">
                <div id="available-files-count" class="text-2xl font-bold">5</div>
                <p class="text-xs text-muted-foreground mt-1">Files available for label printing</p>
            </div>
        </div>

        <div class="card">
            <div class="p-6 pb-2">
                <h3 class="text-sm font-medium">Selected Files</h3>
            </div>
            <div class="p-6">
                <div id="selected-files-count" class="text-2xl font-bold">0</div>
                <p class="text-xs text-muted-foreground mt-1">Files selected for label printing</p>
            </div>
        </div>

        <div class="card">
            <div class="p-6 pb-2">
                <h3 class="text-sm font-medium">Printer Status</h3>
            </div>
            <div class="p-6">
                <div class="text-2xl font-bold flex items-center">
                    Ready
                    <span class="badge badge-green ml-2">Online</span>
                </div>
                <p class="text-xs text-muted-foreground mt-1">Label printer status</p>
            </div>
        </div>
    </div>

    <!-- Print History (Hidden by default) -->
    <div id="print-history" class="card mb-6 hidden">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold">Print History</h3>
                    <p class="text-sm text-muted-foreground">Recent label printing activity</p>
                </div>
                <button id="close-history-btn" class="btn btn-outline btn-sm">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="rounded-md border">
                <div class="p-3 bg-muted-50 grid grid-cols-5 gap-4">
                    <div class="text-sm font-medium">ID</div>
                    <div class="text-sm font-medium">Date</div>
                    <div class="text-sm font-medium">Files</div>
                    <div class="text-sm font-medium">Template</div>
                    <div class="text-sm font-medium">User</div>
                </div>
                <div class="divide-y">
                    <div class="p-3 grid grid-cols-5 gap-4">
                        <div class="text-sm">PRINT-001</div>
                        <div class="text-sm">2023-06-15</div>
                        <div class="text-sm">5</div>
                        <div class="text-sm">Standard</div>
                        <div class="text-sm">Admin</div>
                    </div>
                    <div class="p-3 grid grid-cols-5 gap-4">
                        <div class="text-sm">PRINT-002</div>
                        <div class="text-sm">2023-06-14</div>
                        <div class="text-sm">3</div>
                        <div class="text-sm">Compact</div>
                        <div class="text-sm">Admin</div>
                    </div>
                    <div class="p-3 grid grid-cols-5 gap-4">
                        <div class="text-sm">PRINT-003</div>
                        <div class="text-sm">2023-06-12</div>
                        <div class="text-sm">10</div>
                        <div class="text-sm">QR Code</div>
                        <div class="text-sm">Supervisor</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="w-full">
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-4 px-4 overflow-x-auto" aria-label="Tabs">
                <button data-tab="files"
                                class="tab-trigger flex items-center space-x-2 pb-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600 focus:outline-none">
                    <i data-lucide="file" class="h-5 w-5"></i>
                    <span>Select Files</span>
                </button>

                <button data-tab="settings"
                                class="tab-trigger flex items-center space-x-2 pb-3 text-sm font-medium text-gray-500 hover:text-blue-600 hover:border-blue-600 border-b-2 border-transparent focus:outline-none">
                    <i data-lucide="settings" class="h-5 w-5"></i>
                    <span>Settings</span>
                </button>

                <button data-tab="preview"
                                class="tab-trigger flex items-center space-x-2 pb-3 text-sm font-medium text-gray-500 hover:text-blue-600 hover:border-blue-600 border-b-2 border-transparent focus:outline-none">
                    <i data-lucide="eye" class="h-5 w-5"></i>
                    <span>Preview</span>
                </button>
            </nav>
        </div>

        <!-- Files Tab -->
        <div id="files-tab" class="tab-content active mt-6">
            <div class="card">
                <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold">Select Files for Labels</h3>
                            <p class="text-sm text-muted-foreground">Choose files to generate and print labels</p>
                        </div>
                        <div class="relative w-full md:w-64">
                            <i data-lucide="search" class="absolute left-2-5 top-2-5 h-4 w-4 text-muted-foreground"></i>
                            <input id="search-input" type="search" placeholder="Search files..." class="input w-full pl-8">
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4 flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="batch-mode" class="checkbox">
                            <label for="batch-mode" class="text-sm font-medium">Batch Mode</label>
                        </div>
                        <div id="batch-controls" class="flex items-center gap-2 hidden">
                            <label for="batch-start" class="text-sm whitespace-nowrap">Start Number:</label>
                            <input id="batch-start" type="number" min="1" class="input w-20" value="1">
                            <label for="batch-count" class="text-sm whitespace-nowrap">Count:</label>
                            <input id="batch-count" type="number" min="1" class="input w-20" value="10">
                            <button id="generate-batch-btn" class="btn btn-outline btn-sm">Generate</button>
                        </div>
                    </div>

                    <div id="files-container">
                        <div class="rounded-md border">
                            <div class="p-3 bg-muted-50 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="select-all" class="checkbox">
                                    <label for="select-all" class="text-sm font-medium">Select All</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span id="selection-count" class="text-sm text-muted-foreground">0 of 5 selected</span>
                                </div>
                            </div>
                            <div id="files-list" class="divide-y max-h-400 overflow-y-auto">
                                <!-- Files will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t flex justify-between">
                    <button class="btn btn-outline">Back to Indexing</button>
                    <div class="flex gap-2">
                        <div style="display: none">
                        <button id="duplicate-btn" class="btn btn-outline gap-2 hidden">
                            <i data-lucide="copy" class="h-4 w-4"></i>
                            Duplicate
                        </button>
                        </div>
                        <button id="continue-to-settings-btn"  class="bg-blue-600 hover:bg-blue-700 text-white shadow-sm flex items-center justify-center gap-2 px-6 py-2 transition-all duration-200" disabled>
                            <i data-lucide="settings" class="h-4 w-4"></i>
                            Continue to Label Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div id="settings-tab" class="tab-content mt-6">
            <div class="card">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold">Label Settings</h3>
                            <p class="text-sm text-muted-foreground">Configure label printing options</p>
                        </div>
                        <button id="advanced-options-btn" class="btn btn-outline">Show Advanced</button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="label-template" class="block text-sm font-medium text-gray-700 mb-1">Label Template</label>
                                <select id="label-template" class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                    <option value="standard">Standard - Standard label with barcode and text</option>
                                    <option value="compact">Compact - Compact label with minimal information</option>
                                    <option value="detailed">Detailed - Detailed label with all file information</option>
                                    <option value="qrcode">QR Code - Label with QR code for scanning</option>
                                    <option value="custom">Custom - Custom label template</option>
                                </select>
                            </div>
                            <div>
                                <label for="label-size" class="block text-sm font-medium text-gray-700 mb-1">Label Size</label>
                                <select id="label-size" class="w-full p-2 border border-gray-300 rounded-md text-sm"class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                    <option value="small">Small (2.5&quot; x 1&quot;)</option>
                                    <option value="standard" selected>Standard (3&quot; x 2&quot;)</option>
                                    <option value="large">Large (4&quot; x 3&quot;)</option>
                                    <option value="custom">Custom Size</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium">Label Format</label>
                                <div class="mt-1 grid grid-cols-2 gap-4">
                                    <div id="format-barcode" class="format-option selected">
                                        <i data-lucide="bar-chart-4" class="h-8 w-8 mb-2"></i>
                                        <span class="text-sm font-medium">Barcode</span>
                                    </div>
                                    <div id="format-qrcode" class="format-option">
                                        <i data-lucide="qr-code" class="h-8 w-8 mb-2"></i>
                                        <span class="text-sm font-medium">QR Code</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Orientation</label>
                                <div class="mt-1 grid grid-cols-2 gap-4">
                                    <div class="format-option selected">
                                        <input type="radio" name="orientation" value="portrait" class="radio mr-2" checked>
                                        <label class="cursor-pointer">Portrait</label>
                                    </div>
                                    <div class="format-option">
                                        <input type="radio" name="orientation" value="landscape" class="radio mr-2">
                                        <label class="cursor-pointer">Landscape</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="advanced-options" class="space-y-4 hidden">
                            <div>
                                <label for="margin" class="text-sm font-medium">Margin (inches)</label>
                                <input id="margin" type="number" value="0.25" step="0.01"class="w-full p-2 border border-gray-300 rounded-md text-sm">
                            </div>
                            <div>
                                <label for="dpi" class="text-sm font-medium">DPI</label>
                                <input id="dpi" type="number" value="300"class="w-full p-2 border border-gray-300 rounded-md text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="copies" class="text-sm font-medium">Number of Copies</label>
                            <input id="copies" type="number" min="1" value="1"class="w-full p-2 border border-gray-300 rounded-md text-sm">
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t flex flex-col sm:flex-row justify-between gap-3">
                    <button id="back-to-files-btn" class="btn btn-outline flex items-center gap-2 hover:bg-gray-100 transition-all duration-200">
                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                        <span>Back to File Selection</span>
                    </button>
                    <button id="continue-to-preview-btn" class="bg-blue-600 hover:bg-blue-700 text-white shadow-sm flex items-center justify-center gap-2 px-6 py-2 transition-all duration-200">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                        <span>Continue to Preview</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview Tab -->
        <div id="preview-tab" class="tab-content mt-6">
            <div class="card">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Preview and Print</h3>
                    <p class="text-sm text-muted-foreground">Preview labels before printing</p>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <p id="preview-description">You have selected 0 files to print labels for. Please review the label previews below before printing.</p>
                    </div>

                    <div class="space-y-6">
                        <div id="label-previews" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Label previews will be populated by JavaScript -->
                        </div>

                        <div class="border rounded-md p-4">
                            <h4 class="text-sm font-medium mb-2">Print Summary</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm">Labels:</span>
                                    <span id="summary-labels" class="text-sm font-medium">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm">Copies per label:</span>
                                    <span id="summary-copies" class="text-sm font-medium">1</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm">Total labels to print:</span>
                                    <span id="summary-total" class="text-sm font-medium">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm">Template:</span>
                                    <span id="summary-template" class="text-sm font-medium">Standard</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm">Size:</span>
                                    <span id="summary-size" class="text-sm font-medium">Standard (3" x 2")</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm">Format:</span>
                                    <span id="summary-format" class="text-sm font-medium">Barcode</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 mt-4">
                        <button id="export-pdf-btn" class="btn btn-outline gap-2">
                            <i data-lucide="upload" class="h-4 w-4"></i>
                            Export as PDF
                        </button>
                        <button id="save-template-btn" class="btn btn-outline gap-2">
                            <i data-lucide="save" class="h-4 w-4"></i>
                            Save as Template
                        </button>
                        <button id="import-template-btn" class="btn btn-outline gap-2">
                            <i data-lucide="file-text" class="h-4 w-4"></i>
                            Import Template
                        </button>
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex flex-col sm:flex-row justify-between gap-3">
                    <button id="back-to-settings-btn" class="btn btn-outline hover:bg-gray-100 transition-all duration-200 flex items-center gap-2">
                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                        Back to Settings
                    </button>
                    <button id="final-print-btn" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white shadow-sm flex items-center justify-center gap-2 px-6 py-2 transition-all duration-200">
                        <i data-lucide="printer" class="h-4 w-4"></i>
                        Print Labels
                    </button>
                </div>
            </div>
        </div>
    </div>

        </div>
        <!-- Footer -->
        @include('admin.footer')
        @include('printlabel.assets.js')
    </div>
@endsection
