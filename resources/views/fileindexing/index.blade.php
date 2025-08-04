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
        <div class="card-title mb-2">Pending Files</div>
        <div class="text-3xl font-bold mb-2" id="pending-files-count">{{ $stats['pending_files'] ?? 0 }}</div>
        <div class="text-sm text-gray-500">Files waiting to be indexed</div>
      </div>

      <!-- Indexed Today Card -->
      <div class="card p-6">
        <div class="card-title mb-2">Indexed Today</div>
        <div class="text-3xl font-bold mb-2" id="indexed-files-count">{{ $stats['indexed_today'] ?? 0 }}</div>
        <div class="text-sm text-gray-500">Files indexed today</div>
      </div>

      <!-- Total Indexed Card -->
      <div class="card p-6">
        <div class="card-title mb-2">Total Indexed</div>
        <div class="text-3xl font-bold mb-2 flex items-center">
          {{ $stats['total_indexed'] ?? 0 }}
          <span class="badge badge-blue ml-2 text-xs">Total</span>
        </div>
        <div class="text-sm text-gray-500">All indexed files in system</div>
      </div>
    </div>

    <!-- Tabs and New File Button -->
    <div class="flex justify-between items-center mb-6">
      <div class="tabs bg-white rounded-lg shadow-sm border border-gray-200 p-1" id="main-tabs">
        <div class="tab active flex items-center px-6 py-3 text-sm font-medium rounded-md transition-all" data-tab="pending">
          <i data-lucide="file-text" class="h-4 w-4 mr-2"></i>
          File Index
          <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">New</span>
        </div>
        <div class="tab flex items-center px-6 py-3 text-sm font-medium rounded-md transition-all" data-tab="indexing">
          <i data-lucide="cpu" class="h-4 w-4 mr-2"></i>
          Digital Index (AI)
        </div>
        <div class="tab flex items-center px-6 py-3 text-sm font-medium rounded-md transition-all" data-tab="indexed">
          <i data-lucide="check-circle" class="h-4 w-4 mr-2"></i>
          Indexed Files
        </div>
      </div>
      
      <button class="btn btn-primary flex items-center px-5 py-2.5 shadow-lg hover:shadow-xl transition-all duration-200" id="new-file-index-btn">
        <i data-lucide="folder-plus" class="h-5 w-5 mr-2"></i>
        <span class="font-medium">New File Index</span>
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
          <p class="text-sm text-gray-500 mb-6">AI-powered document analysis and metadata extraction from selected files</p>
          
          <!-- AI Indexing Options -->
          <div class="flex justify-center mb-6">
            <!-- Selected Files AI Indexing -->
            <div class="card p-8 max-w-md w-full">
              <div class="flex items-center justify-center mb-4">
                <i data-lucide="file-search" class="h-8 w-8 text-blue-600 mr-3"></i>
                <h3 class="text-xl font-medium">AI Indexing</h3>
              </div>
              
              <p class="text-sm text-gray-600 mb-6 text-center">Start AI-powered indexing for <span id="selected-files-ai-count" class="font-semibold text-blue-600">0</span> selected files from the File Index tab.</p>
              
              <div class="flex justify-center">
                <button class="btn btn-primary btn-lg" id="start-ai-indexing-btn" disabled>
                  <i data-lucide="brain" class="h-5 w-5 mr-2"></i>
                  Start AI Indexing
                </button>
              </div>
              
              <div class="mt-4 text-xs text-gray-500 text-center">
                <p>Select files from the "File Index" tab first, then return here to start AI processing.</p>
              </div>
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
          <h3 class="text-lg font-medium">AI Indexing: <span id="processing-files-count">0</span> Files</h3>
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
            Our AI is analyzing your applications, extracting metadata, and identifying key information from the motheapplications, subapplication, and cofo tables. This process uses machine learning to understand application structure, recognize patterns, and categorize content.
          </p>
        </div>
        
        <div class="mb-4" id="ai-insights-container">
          <!-- AI insights will be populated here -->
        </div>
        
        <!-- AI Processing Complete Summary -->
        <div class="flex justify-center mb-6" id="ai-completion-summary" style="display: none;">
          <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 max-w-md w-full">
            <!-- Header with Success Icon -->
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                <i data-lucide="check" class="text-green-600 text-lg"></i>
              </div>
              <div>
                <h2 class="text-xl font-semibold text-gray-900">AI Processing Complete</h2>
                <p class="text-sm text-gray-600">All selected documents have been successfully processed</p>
              </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-4 mt-6">
              <!-- Documents Processed -->
              <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-700 mb-1" id="summary-documents-count">0</div>
                <div class="text-xs text-blue-600 font-medium uppercase tracking-wide">Documents Processed</div>
              </div>

              <!-- Average Confidence -->
              <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-700 mb-1" id="summary-confidence">0%</div>
                <div class="text-xs text-green-600 font-medium uppercase tracking-wide">Average Confidence</div>
              </div>

              <!-- Processing Time -->
              <div class="text-center p-4 bg-purple-50 rounded-lg">
                <div class="text-2xl font-bold text-purple-700 mb-1" id="summary-processing-time">0s</div>
                <div class="text-xs text-purple-600 font-medium uppercase tracking-wide">Processing Time</div>
              </div>
            </div>
          </div>
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
        @include('fileindexing.partial.IndexedFilesReport')
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
          
          <div class="form-group" x-data="{ showManualEntry: false }">
            <label for="file-number" class="form-label required">File Number</label>
            <div class="form-info">
              <i data-lucide="file-text" class="h-4 w-4 form-info-icon text-green-600"></i>
              <span class="form-info-text">File Number Information<br>Select file number type and enter the details</span>
            </div>
            
            <div class="flex items-center justify-between mb-3">
              <label class="block text-sm font-medium text-gray-700">Select File Number</label>
              <button type="button" @click="showManualEntry = !showManualEntry" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span x-text="showManualEntry ? 'Use Smart Selector' : 'Enter Fileno manually'"></span>
              </button>
            </div>
            
            <!-- Smart File Number Selector (Default) -->
            <div x-show="!showManualEntry" x-transition>
              @include('fileindexing.partial.smart_fileno_selector')
            </div>
            
            <!-- Manual File Number Entry -->
            <div x-show="showManualEntry" x-transition>
              @include('fileindexing.partial.manual_fileno')
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
              <select id="landUse" name="landUse" class="form-select text-sm">
                <option value="">Select land use</option>
                <option value="RESIDENTIAL">RESIDENTIAL</option>
                <option value="AGRICULTURAL">AGRICULTURAL</option>
                <option value="COMMERCIAL">COMMERCIAL</option>
                <option value="COMMERCIAL ( WARE HOUSE)">COMMERCIAL ( WARE HOUSE)</option>
                <option value="COMMERCIAL (OFFICES)">COMMERCIAL (OFFICES)</option>
                <option value="COMMERCIAL (PETROL FILLING STATION)">COMMERCIAL (PETROL FILLING STATION)</option>
                <option value="COMMERCIAL (RICE PROCESSING)">COMMERCIAL (RICE PROCESSING)</option>
                <option value="COMMERCIAL (SCHOOL)">COMMERCIAL (SCHOOL)</option>
                <option value="COMMERCIAL (SHOPS & PUBLIC CONVINIENCE)">COMMERCIAL (SHOPS & PUBLIC CONVINIENCE)</option>
                <option value="COMMERCIAL (SHOPS AND OFFICES)">COMMERCIAL (SHOPS AND OFFICES)</option>
                <option value="COMMERCIAL (SHOPS)">COMMERCIAL (SHOPS)</option>
                <option value="COMMERCIAL (WAREHOUSE)">COMMERCIAL (WAREHOUSE)</option>
                <option value="COMMERCIAL (WORKSHOP AND OFFICES)">COMMERCIAL (WORKSHOP AND OFFICES)</option>
                <option value="COMMERCIAL AND RESIDENTIAL">COMMERCIAL AND RESIDENTIAL</option>
                <option value="INDUSTRIAL">INDUSTRIAL</option>
                <option value="INDUSTRIAL (SMALL SCALE)">INDUSTRIAL (SMALL SCALE)</option>
                <option value="RESIDENTIAL AND COMMERCIAL">RESIDENTIAL AND COMMERCIAL</option>
                <option value="RESIDENTIAL/COMMERCIAL">RESIDENTIAL/COMMERCIAL</option>
                <option value="RESIDENTIAL/COMMERCIAL LAYOUT">RESIDENTIAL/COMMERCIAL LAYOUT</option>
            </select>
            </div>
            <div class="form-group">
              <label class="form-label">Plot Number</label>
              <input type="text" class="input" placeholder="e.g. PL-1234">
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
              @include('components.StreetName2')
             
            </div>
            <div class="form-group">
              @include('components.District')
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