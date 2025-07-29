@extends('layouts.app')
@section('page-title')
    {{ __('File Indexing') }}
@endsection


@section('content')
  @include('fileindexing.css.style')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">

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
        <div id="indexed-files-list">
          <!-- Indexed file items will be populated here by JavaScript -->
        </div>
      </div>
    </div>
  </div>
</div>
  </div>

  <!-- New File Index Dialog -->
<div class="dialog-overlay hidden" id="new-file-dialog-overlay">
  <div class="dialog">
    <div class="dialog-header">
      <div class="dialog-title">
        <i data-lucide="file-plus" class="h-5 w-5"></i>
        Create New File Index
      </div>
      <button id="close-dialog-btn" class="text-white">
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>
    <div class="dialog-description px-4 py-2 bg-gray-100">
      Enter the details for the new file to be indexed
    </div>
    <div class="dialog-content">
      <form id="new-file-form">
        <!-- File Identification Section -->
        <div class="form-section">
          <h3 class="form-section-title">File Identification</h3>
          
          <div class="form-group">
            <label for="file-number" class="form-label required">File Number</label>
            <div class="form-info">
              <i data-lucide="file-text" class="h-4 w-4 form-info-icon text-green-600"></i>
              <span class="form-info-text">File Number Information<br>Select file number type and enter the details</span>
            </div>
            
            <div class="form-radio-group">
              <label class="form-radio-item active">
                <input type="radio" name="file-number-type" value="mls" checked>
                MLS
              </label>
              <label class="form-radio-item">
                <input type="radio" name="file-number-type" value="kangis">
                KANGIS
              </label>
              <label class="form-radio-item">
                <input type="radio" name="file-number-type" value="new-kangis">
                New KANGIS
              </label>
            </div>
            
            <div id="mls-fields">
              <div class="form-group">
                <label class="form-label">Legacy File Number (MLS)</label>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                  <label class="form-label">File Prefix</label>
                  <select class="input">
                    <option value="">Select prefix</option>
                    <option value="CON">CON</option>
                    <option value="RES">RES</option>
                    <option value="COM">COM</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Serial Number</label>
                  <input type="text" class="input" placeholder="e.g. 2019-296 or 91-249">
                </div>
              </div>
              
              <div class="form-help-text">Format example: CON-COM-2019-296, RES-2015-4859, COM-91-249</div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="file-title" class="form-label required">File Title</label>
            <input type="text" id="file-title" class="input" placeholder="e.g. John Doe's Property">
          </div>
        </div>
        
        <!-- Property Details Section -->
        <div class="form-section">
          <h3 class="form-section-title">Property Details</h3>
          
          <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
              <label class="form-label">Land Use Type</label>
              <select class="input">
                <option value="residential">Residential</option>
                <option value="commercial">Commercial</option>
                <option value="industrial">Industrial</option>
                <option value="agricultural">Agricultural</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Plot Number</label>
              <input type="text" class="input" placeholder="e.g. PL-1234">
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
              <label class="form-label">District</label>
              <select class="input">
                <option value="nasarawa">Nasarawa</option>
                <option value="fagge">Fagge</option>
                <option value="bompai">Bompai</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">LGA/City</label>
              <input type="text" class="input" value="Kano Municipal">
            </div>
          </div>
        </div>
        
        <!-- File Properties Section -->
        <div class="form-section">
          <h3 class="form-section-title">File Properties</h3>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <div class="form-checkbox">
                <input type="checkbox" id="has-cofo">
                <label for="has-cofo">Has Certificate of Occupancy</label>
              </div>
              <div class="form-checkbox">
                <input type="checkbox" id="has-transaction">
                <label for="has-transaction">Has Transaction</label>
              </div>
            </div>
            <div>
              <div class="form-checkbox">
                <input type="checkbox" id="co-owned-plot">
                <label for="co-owned-plot">Co-Owned Plot</label>
              </div>
              <div class="form-checkbox">
                <input type="checkbox" id="merged-plot">
                <label for="merged-plot">Merged Plot</label>
              </div>
            </div>
          </div>
        </div>
        
        <div class="flex justify-between mt-6">
          <button type="button" class="btn" id="cancel-btn">Cancel</button>
          <button type="button" class="btn btn-blue" id="create-file-btn">Create File Index</button>
        </div>
      </form>
    </div>
  </div>
</div>
 
        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>
    @include('fileindexing.js.javascript')
@endsection
