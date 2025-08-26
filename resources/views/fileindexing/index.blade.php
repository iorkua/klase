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
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
          <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <div>
            <h4 class="text-blue-800 font-medium">System Update in Progress</h4>
            <p class="text-blue-600 text-sm">Please wait while we update the file indexing system...</p>
          </div>
        </div>
      </div>

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
        <div class="tab" data-tab="indexing">Digital Index (AI)</div>
        <div class="tab" data-tab="indexed">Indexed Files</div>
      </div>
      <button class="btn btn-primary" id="new-file-index-btn">
        <i data-lucide="folder-plus" class="h-4 w-4 mr-2"></i>
        New File Index
      </button>
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
              <h3 class="text-lg font-medium">AI Indexing: 2 Files</h3>
            </div>
            
            <p class="mb-6">Ready to begin AI-powered indexing for 2 selected files.</p>
            
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
          <h3 class="text-lg font-medium">AI Indexing: 2 Files</h3>
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
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
        <div>
          <h2 class="text-xl font-bold">Indexed Files</h2>
          <p class="text-sm text-gray-500">Files that have been digitally indexed</p>
        </div>
        <div class="relative">
          <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-500"></i>
          <input type="search" placeholder="Search indexed files..." class="input pl-10" id="search-indexed-files">
        </div>
      </div>

      <div class="border rounded-md">
        <div class="flex justify-between items-center p-4 border-b bg-gray-50">
          <div class="flex items-center">
            <input type="checkbox" id="select-all-indexed-checkbox" class="mr-2">
            <label for="select-all-indexed-checkbox" class="text-sm font-medium">Select All</label>
          </div>
          <div class="flex items-center">
            <span class="text-sm text-gray-500" id="selected-indexed-files-count">0 selected</span>
            <button class="btn btn-primary ml-4" id="generate-tracking-sheets-btn">
              <i data-lucide="file-plus" class="h-4 w-4 mr-2"></i>
              <span id="tracking-btn-text">New File Index</span>
            </button>
          </div>
        </div>

        <div id="indexed-files-list">
          <!-- Indexed file items will be populated here by JavaScript -->
        </div>
      </div>
    </div>
  </div>
</div>
  </div>

  {{-- Replace old inline dialog with the new partial --}}
  @include('fileindexing.partial.file_indexing_dialog')
 
        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>
    {{-- Existing File Indexing JS --}}
    @include('fileindexing.js.javascript')
    {{-- Include new File Index Dialog JS (after the existing JS so it overrides dialog handlers) --}}
    @include('fileindexing.js.FileIndexDialog_js')
@endsection
