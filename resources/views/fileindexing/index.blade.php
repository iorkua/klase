@extends('layouts.app')
@section('page-title')
    {{ __('File Indexing') }}
@endsection

 
@section('content')
  @include('fileindexing.css.style')
  {{-- Include new File Index Dialog CSS --}}
  @include('fileindexing.css.FileIndexDialog_css')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">

     {{-- updatig....  --}}
        

     <div class="container py-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-6 mb-6">
      <!-- File Index Card -->
      <div class="card p-6">
        <div class="card-title mb-2">File Index</div>
        <div class="text-3xl font-bold mb-2" id="pending-files-count">3</div>
        <div class="text-sm text-gray-500">Files waiting to be indexed</div>
      </div>

      <!-- Indexed Today Card -->
      <div class="card p-6">
        <div class="card-title mb-2">Indexed Today</div>
        <div class="text-3xl font-bold mb-2" id="indexed-files-count">2</div>
        <div class="text-sm text-gray-500">Files indexed today</div>
      </div>

      <!-- Next Steps Card -->
      <div class="card p-6">
        <div class="card-title mb-2">Next Steps</div>
        <div class="text-3xl font-bold mb-2 flex items-center">
          Scanning
          <span class="badge badge-blue ml-2 text-xs">Stage 2</span>
        </div>
        <div class="text-sm text-gray-500">After indexing, proceed to scanning</div>
      </div>
    </div>

    <!-- Tabs and New File Button -->
    <div class="flex justify-between items-center mb-6">
      <div class="tabs" id="main-tabs">
        <div class="tab active" data-tab="pending">File Index</div>
        <div class="tab disabled" data-tab="indexing">Digital Index (AI)</div>
        <div class="tab" data-tab="indexed">Indexed Files</div>
      </div>
      <div class="flex items-center gap-3">
        <a href="/unindexed-scanning" class="btn btn-outline">
          <i data-lucide="upload" class="h-4 w-4 mr-2"></i>
          Go to Unindexed Files
        </a>
        <button class="btn btn-primary" id="new-file-index-btn">
          <i data-lucide="folder-plus" class="h-4 w-4 mr-2"></i>
          New File Index
        </button>
      </div>
    </div>

    <!-- Pending Files Tab Content -->
    <div class="tab-content active" id="pending-tab">
      <div class="card">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <div>
              <h2 class="text-xl font-bold">File Index</h2>
              <p class="text-sm text-gray-500">Select files to begin the indexing process</p>
            </div>
            <div class="relative">
              <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-500"></i>
              <input type="search" placeholder="Search files..." class="input pl-10" id="search-pending-files">
            </div>
          </div>

          <div class="border rounded-md">
            <div class="flex justify-between items-center p-4 border-b bg-gray-50">
              <div class="flex items-center">
                <input type="checkbox" id="select-all-checkbox" class="mr-2">
                <label for="select-all-checkbox" class="text-sm font-medium">Select All</label>
              </div>
              <div class="flex items-center">
                <span class="text-sm text-gray-500" id="selected-files-count">1 of 3 selected</span>
                <button class="btn btn-primary ml-4" id="begin-indexing-btn">Begin Indexing</button>
              </div>
            </div>

            <div id="pending-files-list">
              <!-- File items will be populated here by JavaScript -->
            </div>
          </div>

          <!-- Pagination for File Index -->
          <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6 mt-4" id="pending-pagination" style="display: none;">
            <div class="flex-1 flex justify-between sm:hidden">
              <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" id="pending-prev-mobile">
                Previous
              </button>
              <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" id="pending-next-mobile">
                Next
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing <span class="font-medium" id="pending-start">1</span> to <span class="font-medium" id="pending-end">10</span> of <span class="font-medium" id="pending-total">0</span> results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination" id="pending-pagination-nav">
                  <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" id="pending-prev">
                    <span class="sr-only">Previous</span>
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                  </button>
                  <!-- Page numbers will be inserted here by JavaScript -->
                  <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" id="pending-next">
                    <span class="sr-only">Next</span>
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Digital Index (AI) Tab Content -->
    <div class="tab-content hidden" id="indexing-tab">
      <div class="card">
        <div class="p-6">
          <div class="flex items-center mb-2">
            <i data-lucide="brain" class="h-5 w-5 text-purple-600 mr-2"></i>
            <h2 class="text-xl font-bold">Digital Index (AI)</h2>
          </div>
          <p class="text-sm text-gray-500 mb-6">AI-powered document analysis and metadata extraction</p>
          
          <div class="card p-6 mb-4">
            <div class="flex items-center mb-4">
              <i data-lucide="brain" class="h-5 w-5 text-purple-600 mr-2"></i>
              <h3 class="text-lg font-medium">AI Indexing: <span id="ai-indexing-files-count">0</span> Files</h3>
            </div>
            
            <p class="mb-6">Ready to begin AI-powered indexing for <span id="ai-selected-files-count">0</span> selected files.</p>
            
            <div class="flex justify-center">
              <button class="btn btn-primary" id="start-ai-indexing-btn">
                <i data-lucide="brain" class="h-4 w-4 mr-2"></i>
                Start AI Indexing
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- AI Processing View (initially hidden) -->
    <div class="hidden" id="ai-processing-view">
      <div class="card p-6 mb-4">
        <div class="flex items-center mb-4">
          <i data-lucide="layers" class="h-5 w-5 text-green-500 mr-2"></i>
          <h3 class="text-lg font-medium">AI Indexing: <span id="ai-processing-files-count">0</span> Files</h3>
        </div>
        
        <div class="mb-4">
          <div class="flex justify-between mb-2">
            <div class="flex items-center">
              <i data-lucide="layers" class="h-4 w-4 text-green-500 mr-2"></i>
              <span class="text-sm">Extracting key information and metadata. Recognizing text, names, dates, and property details...</span>
            </div>
            <span class="text-sm" id="progress-percentage">0%</span>
          </div>
          <div class="progress">
            <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
          </div>
        </div>
        
        <div class="card p-4 mb-4">
          <div class="mb-2">
            <span class="text-sm font-medium">AI Processing Pipeline</span>
            <span class="text-sm float-right" id="pipeline-percentage">0% Complete</span>
          </div>
          
          <div class="progress mb-2">
            <div class="progress-bar" id="pipeline-progress-bar" style="width: 0%"></div>
          </div>
          
          <div class="pipeline">
            <div class="pipeline-line"></div>
            <div class="pipeline-progress" id="pipeline-progress-line" style="width: 0%"></div>
            
            <div class="pipeline-stage">
              <div class="pipeline-dot active" id="stage-init"></div>
              <span class="pipeline-label active">Init</span>
            </div>
            
            <div class="pipeline-stage">
              <div class="pipeline-dot pending" id="stage-analyze"></div>
              <span class="pipeline-label pending">Analyze</span>
            </div>
            
            <div class="pipeline-stage">
              <div class="pipeline-dot pending" id="stage-extract"></div>
              <span class="pipeline-label pending">Extract</span>
            </div>
            
            <div class="pipeline-stage">
              <div class="pipeline-dot pending" id="stage-categorize"></div>
              <span class="pipeline-label pending">Categorize</span>
            </div>
            
            <div class="pipeline-stage">
              <div class="pipeline-dot pending" id="stage-validate"></div>
              <span class="pipeline-label pending">Validate</span>
            </div>
            
            <div class="pipeline-stage">
              <div class="pipeline-dot pending" id="stage-complete"></div>
              <span class="pipeline-label pending">Complete</span>
            </div>
          </div>
          
          <div class="flex items-start gap-3 mt-4" id="current-stage-info">
            <div class="p-2 bg-green-100 rounded-full">
              <i data-lucide="loader" class="h-5 w-5 text-green-500"></i>
            </div>
            <div>
              <p class="text-sm font-medium mb-1">Current Stage: Initialization</p>
              <p class="text-xs text-gray-600">Setting up AI processing environment and preparing documents for analysis...</p>
            </div>
          </div>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-md border border-purple-100 mb-6">
          <p class="text-purple-700">
            Our AI is analyzing your documents, extracting metadata, and identifying key information. This process uses machine learning to understand document structure, recognize text, and categorize content.
          </p>
        </div>
        
        <div class="mb-4" id="ai-insights-container">
          <!-- AI insights will be populated here -->
        </div>
        
        <div class="flex justify-end">
          <button class="btn btn-primary hidden" id="confirm-save-results-btn">
            Confirm & Save Results
          </button>
        </div>
      </div>
    </div>

    <!-- Indexed Files Tab Content -->
    <div class="tab-content hidden" id="indexed-tab">
      <div class="card">
        <div class="card-header">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
              <h3 class="card-title">Indexed Files Report</h3>
              <p class="text-sm text-gray-500">Comprehensive report of all successfully indexed files.</p>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
              <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-500"></i>
                <input type="search" placeholder="Search indexed files..." class="input pl-10" id="search-indexed-files">
              </div>
              <button class="btn btn-primary opacity-50 cursor-not-allowed ml-4" id="generate-tracking-sheets-btn" disabled>
                <i data-lucide="file-text" class="h-4 w-4 mr-2"></i>
                <span id="tracking-btn-text">Batch Tracking Sheets</span>
              </button>
              <button class="btn btn-outline gap-2" id="download-report">
                <i data-lucide="download" class="h-4 w-4"></i>
                Download Report
              </button>
            </div>
          </div>
        </div>
        <div class="card-content">
          <div id="indexed-empty-state" class="rounded-md border p-8 text-center" style="display: none;">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
              <i data-lucide="file-text" class="h-6 w-6 text-gray-400"></i>
            </div>
            <h3 class="mb-2 text-lg font-medium">No indexed files yet</h3>
            <p class="mb-4 text-sm text-gray-500">
              Complete the indexing process to see files here
            </p>
            <button class="btn btn-primary gap-2" id="go-to-pending">
              Go to Pending Files
            </button>
          </div>
          
          <div id="indexed-table-container" class="rounded-md border overflow-x-auto">
            <div class="flex justify-between items-center p-4 border-b bg-gray-50">
              <div class="flex items-center">
                <input type="checkbox" id="select-all-indexed-checkbox" class="mr-2">
                <label for="select-all-indexed-checkbox" class="text-sm font-medium">Select All</label>
              </div>
              <div class="flex items-center">
                <span class="text-sm text-gray-500" id="selected-indexed-files-count">0 selected</span>
              </div>
            </div>
            
            <table class="w-full text-sm text-left border-collapse">
              <thead class="bg-gray-50">
                <tr class="border-b">
                  <th class="p-3 w-10">
                    <!-- Row checkbox column -->
                    <span class="sr-only">Select</span>
                  </th>
                  <th class="p-3 font-medium text-gray-600 uppercase text-xs tracking-wide cursor-pointer min-w-150" data-sort="fileNumber">
                    <div class="flex items-center">
                      File Number
                      <i data-lucide="arrow-up-down" class="ml-2 h-4 w-4"></i>
                    </div>
                  </th>
                  <th class="p-3 font-medium text-gray-600 uppercase text-xs tracking-wide cursor-pointer min-w-200" data-sort="name">
                    <div class="flex items-center">
                      Name
                      <i data-lucide="arrow-up-down" class="ml-2 h-4 w-4"></i>
                    </div>
                  </th>
                  <th class="p-3 font-medium text-gray-600 uppercase text-xs tracking-wide cursor-pointer min-w-150" data-sort="registry">
                    <div class="flex items-center">
                      Registry
                      <i data-lucide="arrow-up-down" class="ml-2 h-4 w-4"></i>
                    </div>
                  </th>
                  <th class="p-3 font-medium text-gray-600 uppercase text-xs tracking-wide cursor-pointer min-w-120" data-sort="date">
                    <div class="flex items-center">
                      Indexed Date
                      <i data-lucide="arrow-up-down" class="ml-2 h-4 w-4"></i>
                    </div>
                  </th>
                  <th class="p-3 font-medium text-gray-600 uppercase text-xs tracking-wide min-w-120">
                    Status
                  </th>
                  <th class="p-3 font-medium text-gray-600 uppercase text-xs tracking-wide cursor-pointer min-w-150" data-sort="location">
                    <div class="flex items-center">
                      Location
                      <i data-lucide="arrow-up-down" class="ml-2 h-4 w-4"></i>
                    </div>
                  </th>
                  <th class="p-3 font-medium text-gray-600 uppercase text-xs tracking-wide cursor-pointer min-w-120" data-sort="landUseType">
                    <div class="flex items-center">
                      Land Use
                      <i data-lucide="arrow-up-down" class="ml-2 h-4 w-4"></i>
                    </div>
                  </th>
                  <th class="p-3 font-medium text-gray-600 uppercase text-xs tracking-wide cursor-pointer min-w-120" data-sort="district">
                    <div class="flex items-center">
                      District
                      <i data-lucide="arrow-up-down" class="ml-2 h-4 w-4"></i>
                    </div>
                  </th>
                  <th class="p-3 font-medium text-gray-600 uppercase text-xs tracking-wide text-right min-w-100">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody id="indexed-files-table-body">
                <!-- Table rows will be inserted here by JavaScript -->
              </tbody>
            </table>
          </div>

          <!-- Pagination for Indexed Files -->
          <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6 mt-4" id="indexed-pagination" style="display: none;">
            <div class="flex-1 flex justify-between sm:hidden">
              <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" id="indexed-prev-mobile">
                Previous
              </button>
              <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" id="indexed-next-mobile">
                Next
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing <span class="font-medium" id="indexed-start">1</span> to <span class="font-medium" id="indexed-end">10</span> of <span class="font-medium" id="indexed-total">0</span> results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination" id="indexed-pagination-nav">
                  <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" id="indexed-prev">
                    <span class="sr-only">Previous</span>
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                  </button>
                  <!-- Page numbers will be inserted here by JavaScript -->
                  <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" id="indexed-next">
                    <span class="sr-only">Next</span>
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
        <div id="indexed-card-footer" class="flex justify-between items-center p-6 border-t" style="display: none;">
          <button class="btn btn-outline" id="index-more-files">
            Index More Files
          </button>
          <button class="btn btn-primary" id="print-labels">
            <i data-lucide="printer" class="h-4 w-4 mr-2"></i>
            Print Labels
          </button>
        </div>
      </div>
    </div>

    <!-- Modal for file details -->
    <div id="file-details-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-90vh overflow-y-auto m-4">
        <div class="flex justify-between items-center p-6 border-b">
          <h3 class="text-lg font-semibold" id="modal-title">File Details</h3>
          <button class="text-gray-400 hover:text-gray-600" id="close-modal" onclick="closeFileDetailsModal()">
            <i data-lucide="x" class="h-6 w-6"></i>
          </button>
        </div>
        <div id="modal-content" class="p-6">
          <!-- File details will be inserted here -->
        </div>
      </div>
    </div>

  {{-- Replace old inline dialog with the new partial --}}
  @include('fileindexing.partial.file_indexing_dialog')
 
        </div>

        <!-- Footer -->
        @include('admin.footer')
</div>
{{-- Debug JS for testing --}}
    @include('fileindexing.js.debug')
    
    {{-- Existing File Indexing JS --}}
    @include('fileindexing.js.javascript')
@endsection
