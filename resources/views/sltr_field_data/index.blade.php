@extends('layouts.app')

@section('page-title')
    {{ $PageTitle ?? __('KLAES') }}
@endsection
@section('content')
 
 <!-- Papa Parse for CSV parsing -->
<script src="https://unpkg.com/papaparse@5.4.1/papaparse.min.js"></script>
<!-- SheetJS for Excel parsing -->
<script src="https://unpkg.com/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
// Tailwind config
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: '#3b82f6',
        'primary-foreground': '#ffffff',
        muted: '#f3f4f6',
        'muted-foreground': '#6b7280',
        border: '#e5e7eb',
        destructive: '#ef4444',
        'destructive-foreground': '#ffffff',
        secondary: '#f1f5f9',
        'secondary-foreground': '#0f172a',
      }
    }
  }
}
</script>

<style>
/* Minimal custom styles */
.loading-spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.file-drop-zone {
  border: 2px dashed #d1d5db;
  transition: all 0.3s ease;
}

.file-drop-zone:hover {
  border-color: #3b82f6;
  background-color: #f8fafc;
}

.file-drop-zone.dragover {
  border-color: #3b82f6;
  background-color: #eff6ff;
}

.survey123-iframe {
  width: 100%;
  height: 600px;
  border: none;
  border-radius: 0.5rem;
}
</style>


<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include($headerPartial ?? 'admin.header')
    
    <!-- Main Content -->
 
 
   <div class="container mx-auto py-6 space-y-6 max-w-7xl px-4 sm:px-6 lg:px-8">
  
  <!-- Page Header -->
  {{-- <div class="space-y-2">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Field Data Collection</h1>
    <p class="text-lg text-gray-600">Import, collect, and manage field data from Survey123</p>
  </div> --}}

  <!-- Tabs Container -->
  <div class="w-full">
    <!-- Tab Navigation -->
    <div class="grid grid-cols-4 bg-gray-100 rounded-lg p-1">
      <button id="tab-links" class="tab-trigger px-4 py-2 rounded-md bg-white text-gray-900 shadow-sm font-medium text-sm transition-all">
        Survey123 Links
      </button>
      <button id="tab-collect" class="tab-trigger px-4 py-2 rounded-md bg-transparent text-gray-600 hover:bg-gray-50 font-medium text-sm transition-all">
        Collect Data
      </button>
      <button id="tab-import" class="tab-trigger px-4 py-2 rounded-md bg-transparent text-gray-600 hover:bg-gray-50 font-medium text-sm transition-all">
        Import Data
      </button>
      <button id="tab-integration" class="tab-trigger px-4 py-2 rounded-md bg-transparent text-gray-600 hover:bg-gray-50 font-medium text-sm transition-all">
        API Integration
      </button>
    </div>

    <!-- Tab Content -->
    <div class="mt-6">
      <!-- Survey123 Links Tab -->
      <div id="content-links" class="tab-content">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Survey123 Link Card 1 -->
          <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">SLTR Field Survey</h3>
              <p class="text-sm text-gray-600 mt-1">Main field data collection form</p>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-center gap-2 text-sm">
                <i data-lucide="file-text" class="h-4 w-4 text-blue-600"></i>
                <span class="text-gray-700">Form ID: sltr-field-survey-2024</span>
              </div>
              <div class="flex items-center gap-2 text-sm">
                <i data-lucide="calendar" class="h-4 w-4 text-blue-600"></i>
                <span class="text-gray-700">Last Updated: 2024-01-15</span>
              </div>
              <div class="flex items-center gap-2 text-sm">
                <i data-lucide="database" class="h-4 w-4 text-blue-600"></i>
                <span class="text-gray-700">247 Submissions</span>
              </div>
              <div class="flex gap-2 mt-4">
                <a href="https://survey123.arcgis.com/share/sltr-field-survey-2024" target="_blank" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 flex-1 gap-1">
                  <i data-lucide="external-link" class="h-4 w-4"></i>
                  Open
                </a>
                <a href="https://survey123.arcgis.com/share/sltr-field-survey-2024?mode=edit" target="_blank" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 flex-1 gap-1">
                  <i data-lucide="edit" class="h-4 w-4"></i>
                  Edit
                </a>
              </div>
            </div>
          </div>

          <!-- Survey123 Link Card 2 -->
          <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Property Assessment</h3>
              <p class="text-sm text-gray-600 mt-1">Property valuation and assessment</p>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-center gap-2 text-sm">
                <i data-lucide="file-text" class="h-4 w-4 text-blue-600"></i>
                <span class="text-gray-700">Form ID: property-assessment-2024</span>
              </div>
              <div class="flex items-center gap-2 text-sm">
                <i data-lucide="calendar" class="h-4 w-4 text-blue-600"></i>
                <span class="text-gray-700">Last Updated: 2024-01-10</span>
              </div>
              <div class="flex items-center gap-2 text-sm">
                <i data-lucide="database" class="h-4 w-4 text-blue-600"></i>
                <span class="text-gray-700">183 Submissions</span>
              </div>
              <div class="flex gap-2 mt-4">
                <a href="https://survey123.arcgis.com/share/property-assessment-2024" target="_blank" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 flex-1 gap-1">
                  <i data-lucide="external-link" class="h-4 w-4"></i>
                  Open
                </a>
                <a href="https://survey123.arcgis.com/share/property-assessment-2024?mode=edit" target="_blank" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 flex-1 gap-1">
                  <i data-lucide="edit" class="h-4 w-4"></i>
                  Edit
                </a>
              </div>
            </div>
          </div>

          <!-- Survey123 Link Card 3 -->
          <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Land Use Survey</h3>
              <p class="text-sm text-gray-600 mt-1">Land use and zoning data collection</p>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-center gap-2 text-sm">
                <i data-lucide="file-text" class="h-4 w-4 text-blue-600"></i>
                <span class="text-gray-700">Form ID: land-use-survey-2024</span>
              </div>
              <div class="flex items-center gap-2 text-sm">
                <i data-lucide="calendar" class="h-4 w-4 text-blue-600"></i>
                <span class="text-gray-700">Last Updated: 2024-01-05</span>
              </div>
              <div class="flex items-center gap-2 text-sm">
                <i data-lucide="database" class="h-4 w-4 text-blue-600"></i>
                <span class="text-gray-700">129 Submissions</span>
              </div>
              <div class="flex gap-2 mt-4">
                <a href="https://survey123.arcgis.com/share/land-use-survey-2024" target="_blank" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 flex-1 gap-1">
                  <i data-lucide="external-link" class="h-4 w-4"></i>
                  Open
                </a>
                <a href="https://survey123.arcgis.com/share/land-use-survey-2024?mode=edit" target="_blank" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 flex-1 gap-1">
                  <i data-lucide="edit" class="h-4 w-4"></i>
                  Edit
                </a>
              </div>
            </div>
          </div>

          <!-- Add New Form Card -->
          <div class="bg-white rounded-lg shadow border border-dashed border-gray-300 overflow-hidden hover:border-blue-400 transition-colors">
            <div class="p-6 flex flex-col items-center justify-center h-full text-center cursor-pointer">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="plus" class="h-6 w-6 text-blue-600"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-900">Create New Form</h3>
              <p class="text-sm text-gray-600 mt-1">Add a new Survey123 form</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Collect Data Tab -->
      <div id="content-collect" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Collect Field Data</h3>
            <p class="text-sm text-gray-600 mt-1">Use Survey123 forms to collect field data</p>
          </div>
          <div class="p-0">
            <!-- Survey123 Form Component -->
            <div class="bg-gray-50 p-6">
              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <div>
                    <h4 class="text-base font-medium text-gray-900">SLTR Field Survey</h4>
                    <p class="text-sm text-gray-600">Complete the form to collect field data</p>
                  </div>
                  <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Form Ready</span>
                  </div>
                </div>
                
                <!-- Form Selection -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Select Survey Form</label>
                  <div class="flex gap-2">
                    <select id="survey-form-select" class="flex-1 w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white cursor-pointer focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10">
                      <option value="sltr-field-survey-2024">SLTR Field Survey</option>
                      <option value="property-assessment-2024">Property Assessment</option>
                      <option value="land-use-survey-2024">Land Use Survey</option>
                    </select>
                    <button id="load-form-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 gap-1">
                      <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                      Load Form
                    </button>
                  </div>
                </div>
                
                <!-- Survey123 Iframe Placeholder -->
                <div id="survey-form-container" class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                  <div class="space-y-4">
                    <i data-lucide="map" class="w-12 h-12 text-gray-400 mx-auto"></i>
                    <div>
                      <h5 class="text-lg font-medium text-gray-900">SLTR Field Survey</h5>
                      <p class="text-gray-600">Survey123 form will load here</p>
                    </div>
                    <button id="load-survey-iframe" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700">
                      <i data-lucide="play" class="w-4 h-4 mr-2"></i>
                      Load Survey Form
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Import Data Tab -->
      <div id="content-import" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Import Survey123 Data</h3>
            <p class="text-sm text-gray-600 mt-1">Import data from Survey123 forms</p>
          </div>
          <div class="p-6">
            <!-- Survey123 Data Importer Component -->
            <div class="space-y-4">
              <!-- Tab Navigation -->
              <div class="grid grid-cols-2 bg-gray-100 rounded-lg p-1">
                <button id="import-tab-api" class="import-tab-trigger px-4 py-2 rounded-md bg-white text-gray-900 shadow-sm font-medium text-sm transition-all">
                  API Import
                </button>
                <button id="import-tab-file" class="import-tab-trigger px-4 py-2 rounded-md bg-transparent text-gray-600 hover:bg-gray-50 font-medium text-sm transition-all">
                  File Import
                </button>
              </div>

              <!-- API Import Tab Content -->
              <div id="import-content-api" class="import-tab-content space-y-4">
                <div class="bg-white rounded-lg border border-gray-200">
                  <div class="p-6">
                    <div class="space-y-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label for="api-username" class="block text-sm font-medium text-gray-700 mb-1">
                            ArcGIS Username
                          </label>
                          <input
                            id="api-username"
                            type="text"
                            placeholder="Username (optional)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                          />
                        </div>
                        <div>
                          <label for="api-password" class="block text-sm font-medium text-gray-700 mb-1">
                            ArcGIS Password
                          </label>
                          <input
                            id="api-password"
                            type="password"
                            placeholder="Password (optional)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                          />
                        </div>
                      </div>
                      <div class="text-sm text-gray-500">
                        If credentials are not provided, the system will use environment variables or anonymous access.
                      </div>
                    </div>
                  </div>
                  
                  <div class="p-6 pt-0 flex justify-end">
                    <button id="load-api-data-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 gap-2">
                      <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                      <span>Load Data</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- File Import Tab Content -->
              <div id="import-content-file" class="import-tab-content space-y-4 hidden">
                <div class="bg-white rounded-lg border border-gray-200">
                  <div class="p-6">
                    <div class="space-y-4">
                      <!-- File Upload Area -->
                      <div id="file-drop-zone" class="file-drop-zone rounded-lg p-6 text-center cursor-pointer">
                        <input
                          id="file-upload"
                          type="file"
                          accept=".csv,.xlsx,.xls"
                          class="hidden"
                        />
                        <label for="file-upload" class="cursor-pointer flex flex-col items-center justify-center">
                          <i data-lucide="upload" class="h-10 w-10 text-gray-400 mb-2"></i>
                          <span id="file-name" class="text-sm font-medium">Click to upload CSV or Excel file</span>
                          <span id="file-size" class="text-xs text-gray-500 mt-1">Supports CSV, XLSX, XLS</span>
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="p-6 pt-0 flex justify-end">
                    <button id="process-file-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 gap-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                      <i data-lucide="database" class="h-4 w-4"></i>
                      <span>Process Data</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- API Integration Tab -->
      <div id="content-integration" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Survey123 API Integration</h3>
            <p class="text-sm text-gray-600 mt-1">Connect to Survey123 API</p>
          </div>
          <div class="p-6">
            <!-- Survey123 Integration Component -->
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <h4 class="text-base font-medium text-gray-900">Survey123 Integration</h4>
              </div>

              <!-- Integration Tabs -->
              <div class="grid grid-cols-2 bg-gray-100 rounded-lg p-1">
                <button id="integration-tab-direct" class="integration-tab-trigger px-4 py-2 rounded-md bg-white text-gray-900 shadow-sm font-medium text-sm transition-all">
                  Direct Access
                </button>
                <button id="integration-tab-api" class="integration-tab-trigger px-4 py-2 rounded-md bg-transparent text-gray-600 hover:bg-gray-50 font-medium text-sm transition-all">
                  API Access
                </button>
              </div>

              <!-- Direct Access Tab Content -->
              <div id="integration-content-direct" class="integration-tab-content space-y-4">
                <div class="bg-white rounded-lg border border-gray-200">
                  <div class="p-6 border-b border-gray-200 pb-3">
                    <h4 class="text-base font-medium text-gray-900">Access Survey123 Form Data</h4>
                    <p class="text-sm text-gray-600 mt-1">Access your form data directly through the Survey123 web interface</p>
                  </div>
                  
                  <div class="p-6 space-y-4">
                    <!-- Form ID Input -->
                    <div class="grid grid-cols-1 gap-4">
                      <div>
                        <label for="form-id" class="block text-sm font-medium text-gray-700 mb-1">
                          Survey123 Form ID
                        </label>
                        <div class="flex gap-2">
                          <input
                            id="form-id"
                            type="text"
                            placeholder="Enter Survey123 Form ID"
                            class="flex-1 w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                            value="sltr-field-survey-2024"
                          />
                          <select id="form-select" class="w-44 px-3 py-2 border border-gray-300 rounded-md text-sm bg-white cursor-pointer focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10">
                            <option value="">Select a form</option>
                            <option value="sltr-field-survey-2024">SLTR Field Survey 2024</option>
                            <option value="property-assessment-2024">Property Assessment 2024</option>
                            <option value="land-use-survey-2024">Land Use Survey 2024</option>
                          </select>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                          Enter a form ID directly or select from available forms
                        </p>
                      </div>

                      <!-- Action Buttons -->
                      <div class="flex gap-2">
                        <button id="copy-url-btn" class="flex-1 inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-1">
                          <i data-lucide="copy" class="h-4 w-4"></i>
                          <span id="copy-text">Copy URL</span>
                        </button>
                        <button id="open-browser-btn" class="flex-1 inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 gap-1">
                          <i data-lucide="external-link" class="h-4 w-4"></i>
                          Open in Browser
                        </button>
                      </div>
                    </div>

                    <!-- Generated URL -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Generated URL</label>
                      <div class="flex items-center">
                        <input
                          id="generated-url"
                          type="text"
                          readonly
                          value="https://survey123.arcgis.com/share/sltr-field-survey-2024"
                          class="w-full p-2 text-sm border border-gray-300 rounded-l-md bg-gray-50 focus:outline-none"
                        />
                        <button id="copy-generated-url" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-2 transition-all cursor-pointer bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-l-none border border-l-0 border-gray-300">
                          <i data-lucide="copy" class="h-4 w-4"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- API Access Tab Content -->
              <div id="integration-content-api" class="integration-tab-content hidden space-y-4">
                <!-- Authentication Alert -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <div class="flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0"></i>
                    <div>
                      <h4 class="font-medium text-yellow-800 text-sm">Authentication Required</h4>
                      <p class="text-yellow-700 text-sm mt-1">
                        API access requires proper authentication. Consider using Direct Access tab instead for easier access to your Survey123 data.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Form Selection Card -->
                <div class="bg-white rounded-lg border border-gray-200">
                  <div class="p-6 border-b border-gray-200 pb-3">
                    <h4 class="text-base font-medium text-gray-900">Select Survey123 Form</h4>
                    <p class="text-sm text-gray-600 mt-1">Choose a form to import data from</p>
                  </div>
                  
                  <div class="p-6">
                    <div class="flex gap-4">
                      <div class="flex-1">
                        <select id="api-form-select" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white cursor-pointer focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10">
                          <option value="">Select a form</option>
                          <option value="sltr-field-survey-2024">SLTR Field Survey 2024</option>
                          <option value="property-assessment-2024">Property Assessment 2024</option>
                          <option value="land-use-survey-2024">Land Use Survey 2024</option>
                        </select>
                      </div>
                      <button id="integration-load-data-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-2 whitespace-nowrap">
                        <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                        Load Data
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Loading Modal -->
<div id="loading-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full mx-4">
    <div class="text-center">
      <div class="loading-spinner mx-auto mb-4"></div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Processing</h3>
      <p id="loading-text" class="text-gray-600">Please wait...</p>
    </div>
  </div>
</div>
    
    <!-- Page Footer -->
    @include($footerPartial ?? 'admin.footer')
</div>

 <script>
// State management
let activeMainTab = 'links';
let activeImportTab = 'api';
let activeIntegrationTab = 'direct';

// DOM elements
const elements = {
  // Main tabs
  tabLinks: document.getElementById('tab-links'),
  tabCollect: document.getElementById('tab-collect'),
  tabImport: document.getElementById('tab-import'),
  tabIntegration: document.getElementById('tab-integration'),
  contentLinks: document.getElementById('content-links'),
  contentCollect: document.getElementById('content-collect'),
  contentImport: document.getElementById('content-import'),
  contentIntegration: document.getElementById('content-integration'),
  
  // Collect tab elements
  surveyFormSelect: document.getElementById('survey-form-select'),
  loadFormBtn: document.getElementById('load-form-btn'),
  surveyFormContainer: document.getElementById('survey-form-container'),
  loadSurveyIframe: document.getElementById('load-survey-iframe'),
  
  // Import tab elements
  importTabApi: document.getElementById('import-tab-api'),
  importTabFile: document.getElementById('import-tab-file'),
  importContentApi: document.getElementById('import-content-api'),
  importContentFile: document.getElementById('import-content-file'),
  
  // Integration tab elements
  integrationTabDirect: document.getElementById('integration-tab-direct'),
  integrationTabApi: document.getElementById('integration-tab-api'),
  integrationContentDirect: document.getElementById('integration-content-direct'),
  integrationContentApi: document.getElementById('integration-content-api'),
  
  // Loading modal
  loadingModal: document.getElementById('loading-modal'),
  loadingText: document.getElementById('loading-text')
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  // Initialize Lucide icons
  lucide.createIcons();
  
  // Set up event listeners
  setupEventListeners();
});

function setupEventListeners() {
  // Main tab switching
  elements.tabLinks.addEventListener('click', () => switchMainTab('links'));
  elements.tabCollect.addEventListener('click', () => switchMainTab('collect'));
  elements.tabImport.addEventListener('click', () => switchMainTab('import'));
  elements.tabIntegration.addEventListener('click', () => switchMainTab('integration'));
  
  // Collect tab
  elements.loadSurveyIframe.addEventListener('click', loadSurveyForm);
  elements.loadFormBtn.addEventListener('click', loadSurveyForm);
  
  // Import tab
  elements.importTabApi.addEventListener('click', () => switchImportTab('api'));
  elements.importTabFile.addEventListener('click', () => switchImportTab('file'));
  
  // Integration tab
  elements.integrationTabDirect.addEventListener('click', () => switchIntegrationTab('direct'));
  elements.integrationTabApi.addEventListener('click', () => switchIntegrationTab('api'));
}

function switchMainTab(tabName) {
  activeMainTab = tabName;
  
  // Hide all content
  elements.contentLinks.classList.add('hidden');
  elements.contentCollect.classList.add('hidden');
  elements.contentImport.classList.add('hidden');
  elements.contentIntegration.classList.add('hidden');
  
  // Reset all tab buttons
  elements.tabLinks.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
  elements.tabLinks.classList.add('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
  elements.tabCollect.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
  elements.tabCollect.classList.add('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
  elements.tabImport.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
  elements.tabImport.classList.add('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
  elements.tabIntegration.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
  elements.tabIntegration.classList.add('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
  
  // Show selected content and activate tab
  if (tabName === 'links') {
    elements.contentLinks.classList.remove('hidden');
    elements.tabLinks.classList.remove('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    elements.tabLinks.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
  } else if (tabName === 'collect') {
    elements.contentCollect.classList.remove('hidden');
    elements.tabCollect.classList.remove('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    elements.tabCollect.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
  } else if (tabName === 'import') {
    elements.contentImport.classList.remove('hidden');
    elements.tabImport.classList.remove('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    elements.tabImport.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
  } else if (tabName === 'integration') {
    elements.contentIntegration.classList.remove('hidden');
    elements.tabIntegration.classList.remove('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    elements.tabIntegration.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
  }
}

function switchImportTab(tabName) {
  activeImportTab = tabName;
  
  if (tabName === 'api') {
    elements.importTabApi.classList.remove('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    elements.importTabApi.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    elements.importTabFile.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    elements.importTabFile.classList.add('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    
    elements.importContentApi.classList.remove('hidden');
    elements.importContentFile.classList.add('hidden');
  } else {
    elements.importTabFile.classList.remove('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    elements.importTabFile.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    elements.importTabApi.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    elements.importTabApi.classList.add('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    
    elements.importContentFile.classList.remove('hidden');
    elements.importContentApi.classList.add('hidden');
  }
}

function switchIntegrationTab(tabName) {
  activeIntegrationTab = tabName;
  
  if (tabName === 'direct') {
    elements.integrationTabDirect.classList.remove('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    elements.integrationTabDirect.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    elements.integrationTabApi.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    elements.integrationTabApi.classList.add('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    
    elements.integrationContentDirect.classList.remove('hidden');
    elements.integrationContentApi.classList.add('hidden');
  } else {
    elements.integrationTabApi.classList.remove('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    elements.integrationTabApi.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    elements.integrationTabDirect.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    elements.integrationTabDirect.classList.add('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
    
    elements.integrationContentApi.classList.remove('hidden');
    elements.integrationContentDirect.classList.add('hidden');
  }
}

function showLoading(message) {
  elements.loadingText.textContent = message;
  elements.loadingModal.classList.remove('hidden');
}

function hideLoading() {
  elements.loadingModal.classList.add('hidden');
}

function loadSurveyForm() {
  const formId = elements.surveyFormSelect.value;
  showLoading('Loading Survey123 form...');
  
  setTimeout(() => {
    hideLoading();
    
    // Replace the placeholder with actual survey iframe
    elements.surveyFormContainer.innerHTML = `
      <iframe 
        src="https://survey123.arcgis.com/share/${formId}" 
        class="survey123-iframe"
        title="SLTR Field Survey">
      </iframe>
    `;
  }, 2000);
}
</script>
@endsection


