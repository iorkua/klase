@extends('layouts.app')
@section('page-title')
    {{ __('Survey Plan Extraction') }}
@endsection

 
@section('content')
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
/* Custom styles */
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

.badge {
  display: inline-flex;
  align-items: center;
  border-radius: 9999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.badge-success {
  background-color: #dcfce7;
  color: #166534;
}

.badge-default {
  background-color: #f3f4f6;
  color: #374151;
}

/* Toggle switch styles */
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .toggle-slider {
  background-color: #3b82f6;
}

input:checked + .toggle-slider:before {
  transform: translateX(20px);
}

/* Table hover effects */
.table-row:hover {
  background-color: rgba(0, 0, 0, 0.025);
}

/* Loading spinner */
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

/* Fade in animation */
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
<div class="flex-1 overflow-auto">
    <!-- Header -->
   @include('admin.header')
    <!-- Dashboard Content -->
    <div class="p-6">
      @include('recertification.js.form_js')
 
<div class="container mx-auto py-6 space-y-6 max-w-7xl px-4 sm:px-6 lg:px-8">
  
  <!-- Header -->
  <div class="flex justify-between items-center">
    <div>
       
      <p class="text-gray-600 mt-1">
        Manage approved certificate recertification and re-issuance applications
      </p>
    </div>
    <div class="flex items-center gap-4">
      <div class="flex items-center space-x-2">
        <label class="toggle-switch">
          <input type="checkbox" id="ocr-mode-toggle">
          <span class="toggle-slider"></span>
        </label>
        <label for="ocr-mode-toggle" class="flex items-center gap-2 cursor-pointer">
          <i data-lucide="scan" class="h-4 w-4"></i>
          OCR Mode
        </label>
      </div>
      <div class="flex gap-2">
        <button id="new-application-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 gap-2">
          <i data-lucide="plus" class="h-4 w-4"></i>
          New Application (Modal)
        </button>
        <a href="{{ url('/recertification/application') }}" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border border-blue-600 text-blue-600 hover:bg-blue-50 gap-2">
          <i data-lucide="external-link" class="h-4 w-4"></i>
          New Application (Page)
        </a>
      </div>
    </div>
  </div>

  <!-- Statistics -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
      <div class="flex items-center">
        <div class="p-2 bg-green-100 rounded-lg">
          <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Total Approved</p>
          <p class="text-2xl font-bold text-gray-900">8</p>
        </div>
      </div>
    </div>
    
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
      <div class="flex items-center">
        <div class="p-2 bg-blue-100 rounded-lg">
          <i data-lucide="file-text" class="h-6 w-6 text-blue-600"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">This Month</p>
          <p class="text-2xl font-bold text-gray-900">8</p>
        </div>
      </div>
    </div>
    
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
      <div class="flex items-center">
        <div class="p-2 bg-purple-100 rounded-lg">
          <i data-lucide="users" class="h-6 w-6 text-purple-600"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Active Officers</p>
          <p class="text-2xl font-bold text-gray-900">4</p>
        </div>
      </div>
    </div>
    
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
      <div class="flex items-center">
        <div class="p-2 bg-orange-100 rounded-lg">
          <i data-lucide="clock" class="h-6 w-6 text-orange-600"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Avg. Processing</p>
          <p class="text-2xl font-bold text-gray-900">12 days</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Search and Filters -->
  <div class="bg-white rounded-lg shadow border border-gray-200">
    <div class="p-6">
      <div class="flex gap-4 items-center">
        <div class="relative flex-1">
          <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4"></i>
          <input
            id="search-input"
            type="text"
            placeholder="Search by applicant name, application number, plot number, or certificate number..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
          />
        </div>
        <button class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50">
          <i data-lucide="filter" class="h-4 w-4"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Applications Table -->
  <div class="bg-white rounded-lg shadow border border-gray-200">
    <div class="p-6 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
          <i data-lucide="check-circle" class="h-5 w-5 text-green-600"></i>
          Approved Recertification Applications (<span id="applications-count">8</span>)
        </h3>
        <span class="badge badge-success">
          All applications have been successfully processed
        </span>
      </div>
    </div>
    
    <div class="p-6">
      <div class="rounded-md border border-gray-200">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b bg-gray-50">
                
                <th class="text-left p-4 font-medium text-gray-700">Applicant Name</th>
                <th class="text-left p-4 font-medium text-gray-700">Plot Details</th>
           
                <th class="text-left p-4 font-medium text-gray-700">LGA</th>
                 
                <th class="text-left p-4 font-medium text-gray-700">Approval Date</th>
                <th class="text-left p-4 font-medium text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody id="applications-table-body">
              <!-- Applications will be inserted here -->
            </tbody>
          </table>
        </div>
        
        <!-- No results state -->
        <div id="no-results" class="hidden text-center py-12">
          <i data-lucide="file-text" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
          <h3 class="text-lg font-medium mb-2 text-gray-900">No applications found</h3>
          <p id="no-results-message" class="text-gray-600">
            No approved applications available
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- OCR Mode View -->
<div id="ocr-mode-view" class="hidden min-h-screen bg-gray-50">
  <div class="container mx-auto py-6 space-y-6 max-w-6xl px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
      <button id="back-from-ocr" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-2">
        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Back to Applications
      </button>
      <div>
        <h1 class="text-3xl font-bold text-gray-900">OCR Document Processing</h1>
        <p class="text-gray-600">Process recertification documents using OCR technology</p>
      </div>
    </div>
    
    <div class="bg-white rounded-lg shadow border border-gray-200 p-8">
      <div class="text-center">
        <i data-lucide="scan" class="h-16 w-16 text-blue-600 mx-auto mb-4"></i>
        <h3 class="text-xl font-semibold mb-2">OCR Processing Mode</h3>
        <p class="text-gray-600 mb-6">Upload documents to automatically extract recertification data</p>
        <button class="inline-flex items-center justify-center rounded-md font-medium text-sm px-6 py-3 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 gap-2">
          <i data-lucide="upload" class="h-4 w-4"></i>
          Upload Document
        </button>
      </div>
    </div>
  </div>
</div>

 
 

<!-- Application Details Modal -->
<div id="details-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
  <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
    <div class="p-6 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Application Details</h3>
        <button id="close-details-modal" class="text-gray-400 hover:text-gray-600">
          <i data-lucide="x" class="h-5 w-5"></i>
        </button>
      </div>
    </div>
    
    <div class="p-6">
      <div id="application-details-content">
        <!-- Application details will be inserted here -->
      </div>
    </div>
  </div>
</div>

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
  <!-- Toast messages will be inserted here -->
</div>

    
    </div>
    <!-- Include the New Application  form Modal -->
@include('recertification.application_form')
    <!-- Footer -->
    @include('admin.footer')
  </div>



@include('recertification.js.js')

@endsection

 