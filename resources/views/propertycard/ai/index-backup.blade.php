@extends('layouts.app')
@section('page-title')
    {{ __('AI Property Record Assistant') }}
@endsection

@section('content')
@include('propertycard.css.style')
<div class="flex-1 overflow-auto">
    @include('admin.header')
    <div class="p-6">
        <div class="container mx-auto py-6 space-y-6">
            <div class="flex items-center justify-end mb-4">
                <label for="assistant-toggle" class="flex items-center cursor-pointer">
                    <a href="{{ route('propertycard.index') }}" class="mr-3 text-gray-600">Manual Assistant</a>
                    <div class="assistant-toggle">
                        <input type="checkbox" id="assistant-toggle" checked>
                        <span class="slider round"></span>
                    </div>
                    <span class="ml-3 text-gray-600">AI Assistant</span>
                </label>
            </div>

            <div id="ai-assistant-page">
                <!-- AI Property Record Assistant - Consolidated and Conflict-Free -->
                <div class="container mx-auto py-6 space-y-6 max-w-6xl px-4 sm:px-6 lg:px-8">
                  
                  <!-- Page Header -->
                  <div class="space-y-2">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">AI Property Record Assistant</h1>
                    <p class="text-lg text-gray-600">Upload property documents for automated data extraction and record creation</p>
                  </div>

                  <!-- File Upload Card -->
                  <div class="bg-white rounded-lg shadow border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                      <h2 class="text-xl font-semibold text-gray-900">Upload Property Record(s) for AI Extraction</h2>
                      <p class="text-sm text-gray-600 mt-1">Upload an image (JPEG, PNG) or PDF of the property document (e.g., Deed of Assignment, C of O).</p>
                    </div>
                    
                    <div class="p-6 space-y-4">
                      <!-- Error Alert -->
                      <div id="error-alert" class="hidden bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                          <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                          <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <div id="error-message" class="mt-2 text-sm text-red-700"></div>
                          </div>
                        </div>
                      </div>

                      <!-- File Upload Area -->
                      <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Document File</label>
                        <input
                          id="ai-file-input"
                          type="file"
                          accept="image/jpeg,image/png,application/pdf"
                          class="hidden"
                        />
                        <button
                          id="ai-file-upload-btn"
                          type="button"
                          class="w-full flex items-center justify-start px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-left font-normal hover:bg-gray-50 transition-colors"
                        >
                          <i data-lucide="file-up" class="mr-2 h-4 w-4"></i>
                          <span id="ai-file-upload-text">Click to select a file</span>
                        </button>
                      </div>

                      <!-- Image Preview -->
                      <div id="ai-image-preview" class="hidden border p-2 rounded-md">
                        <label class="text-xs text-gray-500">Image Preview</label>
                        <img id="ai-image-preview-img" class="max-w-full h-auto max-h-96 rounded-md mt-1" />
                      </div>

                      <!-- PDF Preview -->
                      <div id="ai-pdf-preview" class="hidden border p-2 rounded-md space-y-2">
                        <label id="ai-pdf-preview-label" class="text-xs text-gray-500">PDF Preview</label>
                        <div class="relative">
                          <img id="ai-pdf-preview-img" class="max-w-full h-auto max-h-[30rem] rounded-md mt-1 border mx-auto" />
                        </div>
                      </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="px-6 pb-6 flex flex-col sm:flex-row gap-2">
                      <button
                        id="ai-start-btn"
                        type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                      >
                        <i data-lucide="wand-2" class="mr-2 h-4 w-4"></i>
                        Extract Data with AI
                      </button>
                      <button
                        id="ai-reset-btn"
                        type="button"
                        class="hidden w-full sm:w-auto inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50"
                      >
                        Reset
                      </button>
                    </div>
                  </div>

                  <!-- Extracted Property Details -->
                  <div id="ai-extracted-details" class="hidden bg-white rounded-lg shadow border-l-4 border-l-green-500">
                    <div class="p-6 border-b border-gray-200">
                      <div class="flex items-center space-x-2">
                        <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
                        <h3 class="text-xl font-semibold text-gray-900">AI Extracted Property Details</h3>
                      </div>
                      <p id="ai-extraction-confidence" class="text-sm text-gray-600 mt-1">
                        Review the details extracted by the AI and save the record.
                      </p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                      <!-- Property Form -->
                      <form id="ai-property-record-form" action="{{ route('property-records.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                          <!-- File Number -->
                          <div class="grid grid-cols-2 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700">KANGIS File No</label>
                              <input type="text" id="ai-kangisFileNo" name="kangisFileNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">MLS File No</label>
                              <input type="text" id="ai-mlsFNo" name="mlsFNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                          </div>
                          
                          <!-- Plot and LGA -->
                          <div class="grid grid-cols-2 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Plot No</label>
                              <input type="text" id="ai-plotNo" name="plot_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">LGA/City</label>
                              <input type="text" id="ai-lga" name="lgsaOrCity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                          </div>
                          
                          <!-- Property Description -->
                          <div>
                            <label class="block text-sm font-medium text-gray-700">Property Description</label>
                            <textarea id="ai-property-description" name="property_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                          </div>
                          
                          <!-- Transaction Type -->
                          <div>
                            <label class="block text-sm font-medium text-gray-700">Transaction Type</label>
                            <select id="ai-transactionType-record" name="transactionType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                              <option value="">Select type</option>
                              <option value="Deed of Assignment">Deed of Assignment</option>
                              <option value="Certificate of Occupancy">Certificate of Occupancy</option>
                              <option value="Deed of Mortgage">Deed of Mortgage</option>
                              <option value="Power of Attorney">Power of Attorney</option>
                            </select>
                          </div>
                          
                          <!-- Registration Details -->
                          <div class="grid grid-cols-3 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Serial No</label>
                              <input type="text" id="ai-serialNo" name="serialNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Page No</label>
                              <input type="text" id="ai-pageNo" name="pageNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Volume No</label>
                              <input type="text" id="ai-volumeNo" name="volumeNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                          </div>
                          
                          <!-- Parties -->
                          <div class="grid grid-cols-2 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Grantor</label>
                              <input type="text" name="Grantor" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Grantee</label>
                              <input type="text" name="Grantee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                          </div>
                        </div>
                      </form>

                      <!-- Save Button -->
                      <div class="flex justify-end pt-4 border-t">
                        <button id="ai-save-record-btn" type="button" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-green-600 text-white hover:bg-green-700 gap-2">
                          <i data-lucide="check-circle" class="h-4 w-4"></i>
                          Save Property Record
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Toast Notifications -->
                <div id="ai-toast-container" class="fixed top-4 right-4 z-50 space-y-2">
                  <!-- Toast messages will be inserted here -->
                </div>

                <script>
                // AI Assistant JavaScript - Consolidated and Conflict-Free
                (function() {
                  'use strict';
                  
                  // Global state for AI assistant
                  let aiSelectedFile = null;
                  let aiPreviewUrl = null;
                  let aiExtractedPropertyData = null;

                  // Initialize when DOM is ready
                  document.addEventListener('DOMContentLoaded', function() {
                    console.log('AI Assistant: DOM Content Loaded');
                    
                    // Initialize Lucide icons
                    if (typeof lucide !== 'undefined') {
                      lucide.createIcons();
                    }
                    
                    // Set up AI assistant event listeners
                    setupAIEventListeners();
                  });

                  function setupAIEventListeners() {
                    console.log('AI Assistant: Setting up event listeners');
                    
                    // File input elements
                    const fileInput = document.getElementById('ai-file-input');
                    const fileUploadBtn = document.getElementById('ai-file-upload-btn');
                    
                    if (fileInput && fileUploadBtn) {
                      console.log('AI Assistant: File input elements found');
                      
                      // File upload button click
                      fileUploadBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('AI Assistant: File upload button clicked');
                        fileInput.click();
                      });
                      
                      // File input change
                      fileInput.addEventListener('change', function(e) {
                        console.log('AI Assistant: File input changed');
                        handleAIFileChange(e);
                      });
                    } else {
                      console.error('AI Assistant: File input elements not found!');
                    }
                    
                    // Action buttons
                    const startBtn = document.getElementById('ai-start-btn');
                    const resetBtn = document.getElementById('ai-reset-btn');
                    const saveBtn = document.getElementById('ai-save-record-btn');
                    
                    if (startBtn) {
                      startBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        startAIProcessing();
                      });
                    }
                    
                    if (resetBtn) {
                      resetBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        resetAIState();
                      });
                    }
                    
                    if (saveBtn) {
                      saveBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        handleAISaveRecord();
                      });
                    }
                    
                    console.log('AI Assistant: Event listeners setup complete');
                  }

                  function handleAIFileChange(event) {
                    console.log('AI Assistant: File change event triggered');
                    const file = event.target.files?.[0];
                    
                    if (file) {
                      console.log('AI Assistant: File selected:', file.name, file.type, file.size);
                      
                      if (file.type.startsWith('image/') || file.type === 'application/pdf') {
                        aiSelectedFile = file;
                        hideAIError();
                        
                        // Update file name display
                        const fileUploadText = document.getElementById('ai-file-upload-text');
                        if (fileUploadText) {
                          fileUploadText.textContent = file.name;
                        }
                        
                        // Show preview
                        if (file.type.startsWith('image/')) {
                          showAIImagePreview(file);
                        } else if (file.type === 'application/pdf') {
                          showAIPDFPreview(file);
                        }
                        
                        updateAIUI();
                      } else {
                        showAIError('Invalid file type. Please upload an image (JPEG, PNG) or PDF.');
                        resetAIFileState();
                      }
                    }
                  }

                  function showAIImagePreview(file) {
                    aiPreviewUrl = URL.createObjectURL(file);
                    const preview = document.getElementById('ai-image-preview');
                    const img = document.getElementById('ai-image-preview-img');
                    
                    if (preview && img) {
                      img.src = aiPreviewUrl;
                      preview.classList.remove('hidden');
                    }
                    
                    // Hide PDF preview
                    const pdfPreview = document.getElementById('ai-pdf-preview');
                    if (pdfPreview) {
                      pdfPreview.classList.add('hidden');
                    }
                  }

                  function showAIPDFPreview(file) {
                    // For now, just show a placeholder
                    const preview = document.getElementById('ai-pdf-preview');
                    const label = document.getElementById('ai-pdf-preview-label');
                    
                    if (preview && label) {
                      label.textContent = `PDF Preview: ${file.name}`;
                      preview.classList.remove('hidden');
                    }
                    
                    // Hide image preview
                    const imagePreview = document.getElementById('ai-image-preview');
                    if (imagePreview) {
                      imagePreview.classList.add('hidden');
                    }
                  }

                  function startAIProcessing() {
                    if (!aiSelectedFile) {
                      showAIToast('Please select a document file first.', 'error');
                      return;
                    }
                    
                    console.log('AI Assistant: Starting AI processing for:', aiSelectedFile.name);
                    showAIToast('AI processing started...', 'info');
                    
                    // Simulate AI processing
                    setTimeout(() => {
                      // Mock extracted data
                      aiExtractedPropertyData = {
                        kangisFileNo: 'LKN/COM/2024/001',
                        plotNo: 'Plot 123',
                        lgsaOrCity: 'Kano Municipal',
                        description: 'Sample property description extracted by AI',
                        instrument: 'DEED OF ASSIGNMENT',
                        serialNo: '001',
                        pageNo: '001',
                        volumeNo: '001'
                      };
                      
                      populateAIPropertyForm();
                      
                      const extractedDetails = document.getElementById('ai-extracted-details');
                      if (extractedDetails) {
                        extractedDetails.classList.remove('hidden');
                      }
                      
                      showAIToast('AI processing complete!', 'success');
                    }, 2000);
                  }

                  function populateAIPropertyForm() {
                    if (!aiExtractedPropertyData) return;
                    
                    const data = aiExtractedPropertyData;
                    
                    // Populate form fields
                    const fields = {
                      'ai-kangisFileNo': data.kangisFileNo,
                      'ai-plotNo': data.plotNo,
                      'ai-lga': data.lgsaOrCity,
                      'ai-property-description': data.description,
                      'ai-serialNo': data.serialNo,
                      'ai-pageNo': data.pageNo,
                      'ai-volumeNo': data.volumeNo
                    };
                    
                    Object.entries(fields).forEach(([fieldId, value]) => {
                      const field = document.getElementById(fieldId);
                      if (field && value) {
                        field.value = value;
                      }
                    });
                    
                    // Set transaction type
                    if (data.instrument) {
                      const transactionField = document.getElementById('ai-transactionType-record');
                      if (transactionField) {
                        const mapping = {
                          'DEED OF ASSIGNMENT': 'Deed of Assignment',
                          'CERTIFICATE OF OCCUPANCY': 'Certificate of Occupancy',
                          'DEED OF MORTGAGE': 'Deed of Mortgage',
                          'POWER OF ATTORNEY': 'Power of Attorney'
                        };
                        transactionField.value = mapping[data.instrument] || '';
                      }
                    }
                  }

                  function handleAISaveRecord() {
                    const form = document.getElementById('ai-property-record-form');
                    if (!form) {
                      showAIToast('Form not found. Cannot save record.', 'error');
                      return;
                    }

                    if (!aiExtractedPropertyData) {
                      showAIToast('No property data extracted. Please run AI extraction first.', 'error');
                      return;
                    }

                    showAIToast('Saving property record...', 'info');

                    // Create FormData from form
                    const formData = new FormData(form);
                    
                    // Add additional required fields
                    formData.append('title_type', 'Statutory');
                    formData.append('transactionDate', new Date().toISOString().split('T')[0]);
                    formData.append('regDate', new Date().toISOString().split('T')[0]);
                    formData.append('regTime', '09:00');

                    // Submit to backend
                    fetch(form.action, {
                      method: 'POST',
                      body: formData,
                      headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                      }
                    })
                    .then(response => {
                      if (response.redirected) {
                        window.location.href = response.url;
                        return null;
                      }
                      return response.json();
                    })
                    .then(data => {
                      if (!data) return;
                      
                      if (data.status === 'success' || data.success === true) {
                        showAIToast('Property record saved successfully!', 'success');
                        setTimeout(() => {
                          window.location.reload();
                        }, 1500);
                      } else {
                        showAIToast('Error: ' + (data.message || 'Failed to save record'), 'error');
                      }
                    })
                    .catch(error => {
                      console.error('AI Assistant: Error saving property record:', error);
                      showAIToast('Error: Failed to save property record', 'error');
                    });
                  }

                  function resetAIState() {
                    aiSelectedFile = null;
                    aiPreviewUrl = null;
                    aiExtractedPropertyData = null;

                    const fileInput = document.getElementById('ai-file-input');
                    const fileUploadText = document.getElementById('ai-file-upload-text');
                    const imagePreview = document.getElementById('ai-image-preview');
                    const pdfPreview = document.getElementById('ai-pdf-preview');
                    const extractedDetails = document.getElementById('ai-extracted-details');

                    if (fileInput) fileInput.value = '';
                    if (fileUploadText) fileUploadText.textContent = 'Click to select a file';
                    if (imagePreview) imagePreview.classList.add('hidden');
                    if (pdfPreview) pdfPreview.classList.add('hidden');
                    if (extractedDetails) extractedDetails.classList.add('hidden');
                    
                    hideAIError();
                    updateAIUI();
                  }

                  function resetAIFileState() {
                    aiSelectedFile = null;
                    aiPreviewUrl = null;
                    
                    const fileInput = document.getElementById('ai-file-input');
                    const fileUploadText = document.getElementById('ai-file-upload-text');
                    const imagePreview = document.getElementById('ai-image-preview');
                    const pdfPreview = document.getElementById('ai-pdf-preview');

                    if (fileInput) fileInput.value = '';
                    if (fileUploadText) fileUploadText.textContent = 'Click to select a file';
                    if (imagePreview) imagePreview.classList.add('hidden');
                    if (pdfPreview) pdfPreview.classList.add('hidden');
                  }

                  function updateAIUI() {
                    const startBtn = document.getElementById('ai-start-btn');
                    const resetBtn = document.getElementById('ai-reset-btn');

                    if (aiSelectedFile) {
                      if (startBtn) {
                        startBtn.disabled = false;
                        startBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                      }
                      if (resetBtn) resetBtn.classList.remove('hidden');
                    } else {
                      if (startBtn) {
                        startBtn.disabled = true;
                        startBtn.classList.add('opacity-50', 'cursor-not-allowed');
                      }
                      if (resetBtn) resetBtn.classList.add('hidden');
                    }
                  }

                  function showAIError(message) {
                    const errorAlert = document.getElementById('error-alert');
                    const errorMessage = document.getElementById('error-message');
                    if (errorAlert && errorMessage) {
                      errorMessage.textContent = message;
                      errorAlert.classList.remove('hidden');
                    }
                  }

                  function hideAIError() {
                    const errorAlert = document.getElementById('error-alert');
                    if (errorAlert) {
                      errorAlert.classList.add('hidden');
                    }
                  }

                  function showAIToast(message, type = 'info') {
                    const toastContainer = document.getElementById('ai-toast-container');
                    if (!toastContainer) return;

                    const toast = document.createElement('div');
                    toast.className = `max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto flex ring-1 ring-black ring-opacity-5 ${
                      type === 'success' ? 'border-l-4 border-green-400' :
                      type === 'error' ? 'border-l-4 border-red-400' :
                      type === 'warning' ? 'border-l-4 border-yellow-400' :
                      'border-l-4 border-blue-400'
                    }`;

                    toast.innerHTML = `
                      <div class="flex-1 w-0 p-4">
                        <div class="flex items-start">
                          <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">${message}</p>
                          </div>
                        </div>
                      </div>
                      <div class="flex border-l border-gray-200">
                        <button onclick="this.parentElement.parentElement.remove()" class="w-full border border-transparent rounded-none rounded-r-lg p-4 flex items-center justify-center text-sm font-medium text-gray-600 hover:text-gray-500 focus:outline-none">
                          ×
                        </button>
                      </div>
                    `;

                    toastContainer.appendChild(toast);

                    // Auto remove after 5 seconds
                    setTimeout(() => {
                      if (toast.parentElement) {
                        toast.remove();
                      }
                    }, 5000);
                  }

                })();
                </script>
            </div>
        </div>

        <!-- Include the same Add New Property modal used by Manual Assistant -->
        
        @include('propertycard.partials.edit_property_record')
        @include('propertycard.partials.view_property_record')
    </div>
    @include('admin.footer')
</div>

<!-- Shared JS and SweetAlert handlers -->
@include('propertycard.js.javascript')
@include('propertycard.partials.property_form_sweetalert')
@endsection