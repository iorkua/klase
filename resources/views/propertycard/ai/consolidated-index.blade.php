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
                <!-- AI Property Record Assistant - Consolidated -->
                <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <title>AI Property Record Assistant - SLTR</title>
                <!-- Tailwind CSS -->
                <script src="https://cdn.tailwindcss.com"></script>
                <!-- Lucide Icons -->
                <script src="https://unpkg.com/lucide@latest"></script>
                <!-- PDF.js -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
                <!-- Tesseract.js for OCR -->
                <script src="https://unpkg.com/tesseract.js@4/dist/tesseract.min.js"></script>

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
                /* Loading spinner animation */
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

                /* File drop zone styles */
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

                /* Progress bar animation */
                .progress-bar {
                  transition: width 0.5s ease-in-out;
                }

                /* AI stage indicator animations */
                .stage-indicator {
                  transition: all 0.3s ease;
                }

                .stage-indicator.active {
                  animation: pulse 2s infinite;
                }

                @keyframes pulse {
                  0%, 100% { opacity: 1; }
                  50% { opacity: 0.5; }
                }

                /* Modal backdrop */
                .modal-backdrop {
                  background-color: rgba(0, 0, 0, 0.5);
                  backdrop-filter: blur(4px);
                }

                /* Badge styles */
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

                .badge-warning {
                  background-color: #fef3c7;
                  color: #92400e;
                }

                .badge-error {
                  background-color: #fee2e2;
                  color: #991b1b;
                }

                .badge-default {
                  background-color: #f3f4f6;
                  color: #374151;
                }

                /* Collapsible content */
                .collapsible-content {
                  max-height: 0;
                  overflow: hidden;
                  transition: max-height 0.3s ease;
                }

                .collapsible-content.expanded {
                  max-height: 2000px;
                }

                /* Instrument card styles */
                .instrument-card {
                  border-left: 4px solid #3b82f6;
                }

                .instrument-card.editing {
                  border-left-color: #10b981;
                }
                </style>
                </head>
                <body class="min-h-screen bg-gray-50">

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
                          id="file-input"
                          type="file"
                          accept="image/jpeg,image/png,application/pdf"
                          class="hidden"
                        />
                        <button
                          id="file-upload-btn"
                          class="w-full flex items-center justify-start px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-left font-normal hover:bg-gray-50 transition-colors"
                        >
                          <i data-lucide="file-up" class="mr-2 h-4 w-4"></i>
                          <span id="file-upload-text">Click to select a file</span>
                        </button>
                      </div>

                      <!-- Image Preview -->
                      <div id="image-preview" class="hidden border p-2 rounded-md">
                        <label class="text-xs text-gray-500">Image Preview</label>
                        <img id="image-preview-img" class="max-w-full h-auto max-h-96 rounded-md mt-1" />
                      </div>

                      <!-- PDF Preview -->
                      <div id="pdf-preview" class="hidden border p-2 rounded-md space-y-2">
                        <label id="pdf-preview-label" class="text-xs text-gray-500">PDF Preview</label>
                        <div class="relative">
                          <img id="pdf-preview-img" class="max-w-full h-auto max-h-[30rem] rounded-md mt-1 border mx-auto" />
                        </div>
                        <div id="pdf-navigation" class="hidden flex justify-center items-center space-x-2 mt-2">
                          <button id="pdf-prev-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Previous
                          </button>
                          <span id="pdf-page-info" class="text-sm text-gray-500">Page 1 / 1</span>
                          <button id="pdf-next-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Next
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="px-6 pb-6 flex flex-col sm:flex-row gap-2">
                      <button
                        id="start-ai-btn"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                      >
                        <i data-lucide="wand-2" class="mr-2 h-4 w-4"></i>
                        Extract Data with AI
                      </button>
                      <button
                        id="reset-btn"
                        class="hidden w-full sm:w-auto inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50"
                      >
                        Reset
                      </button>
                    </div>
                  </div>

                  <!-- AI Processing Visualizer -->
                  <div id="ai-processing" class="hidden bg-white rounded-lg shadow border border-gray-200">
                    <div class="p-6">
                      <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium">Property Document AI Analysis</span>
                        <span id="ai-progress-text" class="text-sm">0% Complete</span>
                      </div>
                      <div class="relative">
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                          <div id="ai-progress-bar" class="h-full bg-blue-500 rounded-full transition-all duration-500 ease-in-out" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between mt-2">
                          <div class="flex flex-col items-center stage-indicator" data-stage="0">
                            <div class="w-4 h-4 rounded-full bg-gray-300 mb-1"></div>
                            <span class="text-xs text-gray-500">Init</span>
                          </div>
                          <div class="flex flex-col items-center stage-indicator" data-stage="1">
                            <div class="w-4 h-4 rounded-full bg-gray-300 mb-1"></div>
                            <span class="text-xs text-gray-500">OCR</span>
                          </div>
                          <div class="flex flex-col items-center stage-indicator" data-stage="2">
                            <div class="w-4 h-4 rounded-full bg-gray-300 mb-1"></div>
                            <span class="text-xs text-gray-500">Layout</span>
                          </div>
                          <div class="flex flex-col items-center stage-indicator" data-stage="3">
                            <div class="w-4 h-4 rounded-full bg-gray-300 mb-1"></div>
                            <span class="text-xs text-gray-500">Extract</span>
                          </div>
                          <div class="flex flex-col items-center stage-indicator" data-stage="4">
                            <div class="w-4 h-4 rounded-full bg-gray-300 mb-1"></div>
                            <span class="text-xs text-gray-500">Assemble</span>
                          </div>
                          <div class="flex flex-col items-center stage-indicator" data-stage="5">
                            <div class="w-4 h-4 rounded-full bg-gray-300 mb-1"></div>
                            <span class="text-xs text-gray-500">Done</span>
                          </div>
                        </div>
                      </div>
                      <div class="mt-4 flex items-start gap-3">
                        <div class="p-2 rounded-full bg-blue-100">
                          <i id="ai-stage-icon" data-lucide="brain" class="h-5 w-5 text-blue-600"></i>
                        </div>
                        <div>
                          <p id="ai-stage-title" class="text-sm font-medium mb-1">Current Stage: Initializing</p>
                          <p id="ai-stage-description" class="text-xs text-gray-600">Preparing for AI analysis...</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Extracted Property Details -->
                  <div id="extracted-details" class="hidden bg-white rounded-lg shadow border-l-4 border-l-green-500">
                    <div class="p-6 border-b border-gray-200">
                      <div class="flex items-center space-x-2">
                        <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
                        <h3 class="text-xl font-semibold text-gray-900">AI Extracted Property Details</h3>
                      </div>
                      <p id="extraction-confidence" class="text-sm text-gray-600 mt-1">
                        Review the details extracted by the AI. Add or modify instruments as needed, then save the record.
                      </p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                      <!-- Property Form -->
                      <form id="property-record-form" action="{{ route('property-records.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                          <!-- File Number -->
                          <div class="grid grid-cols-2 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700">KANGIS File No</label>
                              <input type="text" id="kangisFileNo" name="kangisFileNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">MLS File No</label>
                              <input type="text" id="mlsFNo" name="mlsFNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                          </div>
                          
                          <!-- Plot and LGA -->
                          <div class="grid grid-cols-2 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Plot No</label>
                              <input type="text" id="plotNo" name="plot_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">LGA/City</label>
                              <input type="text" id="lga" name="lgsaOrCity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                          </div>
                          
                          <!-- Property Description -->
                          <div>
                            <label class="block text-sm font-medium text-gray-700">Property Description</label>
                            <textarea id="property-description" name="property_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                          </div>
                          
                          <!-- Transaction Type -->
                          <div>
                            <label class="block text-sm font-medium text-gray-700">Transaction Type</label>
                            <select id="transactionType-record" name="transactionType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
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
                              <input type="text" id="serialNo" name="serialNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Page No</label>
                              <input type="text" id="pageNo" name="pageNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Volume No</label>
                              <input type="text" id="volumeNo" name="volumeNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                          </div>
                          
                          <!-- Parties -->
                          <div class="grid grid-cols-2 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Grantor</label>
                              <input type="text" name="Grantor" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Grantee</label>
                              <input type="text" name="Grantee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                          </div>
                        </div>
                      </form>

                      <!-- Save Button -->
                      <div class="flex justify-end pt-4 border-t">
                        <button id="save-record-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-green-600 text-white hover:bg-green-700 gap-2">
                          <i data-lucide="check-circle" class="h-4 w-4"></i>
                          Save Property Record
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Toast Notifications -->
                <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
                  <!-- Toast messages will be inserted here -->
                </div>

                <script>
                // Global state
                let selectedFile = null;
                let previewUrl = null;
                let pdfPagePreviews = [];
                let currentPdfPreviewPageIdx = 0;
                let rawExtractedText = '';
                let extractedPropertyData = null;
                let keywordFindings = {};
                let currentAiStage = 'idle';
                let aiProgress = 0;
                let instruments = [];
                let editingInstrumentId = null;

                // Initialize PDF.js
                if (window.pdfjsLib) {
                  window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                }

                // Initialize the application
                document.addEventListener('DOMContentLoaded', function() {
                  console.log('DOM Content Loaded - Setting up AI Assistant');
                  
                  // Initialize Lucide icons
                  lucide.createIcons();
                  
                  // Set up event listeners
                  setupEventListeners();
                  
                  // Update UI
                  updateUI();
                });

                function setupEventListeners() {
                  console.log('Setting up event listeners');
                  
                  // File input
                  const fileInput = document.getElementById('file-input');
                  const fileUploadBtn = document.getElementById('file-upload-btn');
                  
                  if (fileInput && fileUploadBtn) {
                    console.log('File input elements found, adding listeners');
                    
                    fileInput.addEventListener('change', handleFileChange);
                    fileUploadBtn.addEventListener('click', function() {
                      console.log('File upload button clicked');
                      fileInput.click();
                    });
                  } else {
                    console.error('File input elements not found!');
                  }
                  
                  // Action buttons
                  const startBtn = document.getElementById('start-ai-btn');
                  const resetBtn = document.getElementById('reset-btn');
                  const saveBtn = document.getElementById('save-record-btn');
                  
                  if (startBtn) startBtn.addEventListener('click', startAiPropertyProcessing);
                  if (resetBtn) resetBtn.addEventListener('click', resetState);
                  if (saveBtn) saveBtn.addEventListener('click', handleSaveRecord);
                  
                  console.log('Event listeners setup complete');
                }

                function handleFileChange(event) {
                  console.log('File change event triggered');
                  const file = event.target.files?.[0];
                  if (file) {
                    console.log('File selected:', file.name, file.type, file.size);
                    if (file.type.startsWith('image/') || file.type === 'application/pdf') {
                      selectedFile = file;
                      hideError();
                      
                      document.getElementById('file-upload-text').textContent = file.name;
                      
                      if (file.type === 'application/pdf') {
                        document.getElementById('image-preview').classList.add('hidden');
                        renderPDFPagesToImages(file).then(pages => {
                          pdfPagePreviews = pages;
                          currentPdfPreviewPageIdx = 0;
                          if (pages.length > 0) {
                            showPdfPreview();
                          }
                        });
                      } else {
                        document.getElementById('pdf-preview').classList.add('hidden');
                        previewUrl = URL.createObjectURL(file);
                        showImagePreview();
                      }
                      
                      updateUI();
                    } else {
                      showError('Invalid file type. Please upload an image (JPEG, PNG) or PDF.');
                      resetFileState();
                    }
                  }
                }

                function showImagePreview() {
                  const preview = document.getElementById('image-preview');
                  const img = document.getElementById('image-preview-img');
                  img.src = previewUrl;
                  preview.classList.remove('hidden');
                }

                function showPdfPreview() {
                  const preview = document.getElementById('pdf-preview');
                  const img = document.getElementById('pdf-preview-img');
                  const label = document.getElementById('pdf-preview-label');
                  
                  if (pdfPagePreviews.length > 0) {
                    img.src = pdfPagePreviews[currentPdfPreviewPageIdx];
                    label.textContent = `PDF Preview (Page ${currentPdfPreviewPageIdx + 1} of ${pdfPagePreviews.length})`;
                    preview.classList.remove('hidden');
                  }
                }

                async function renderPDFPagesToImages(file) {
                  try {
                    const arrayBuffer = await file.arrayBuffer();
                    const pdf = await window.pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                    const pageImages = [];
                    
                    for (let i = 1; i <= pdf.numPages; i++) {
                      const page = await pdf.getPage(i);
                      const viewport = page.getViewport({ scale: 1.5 });
                      const canvas = document.createElement('canvas');
                      const context = canvas.getContext('2d');
                      
                      if (!context) throw new Error('Could not get canvas context');
                      
                      canvas.height = viewport.height;
                      canvas.width = viewport.width;
                      
                      await page.render({ canvasContext: context, viewport: viewport }).promise;
                      pageImages.push(canvas.toDataURL('image/png'));
                    }
                    
                    return pageImages;
                  } catch (error) {
                    console.error('Error rendering PDF pages:', error);
                    showToast('Failed to render PDF for preview.', 'error');
                    return [];
                  }
                }

                function startAiPropertyProcessing() {
                  if (!selectedFile) {
                    showToast('Please select a document file first.', 'error');
                    return;
                  }
                  
                  console.log('Starting AI processing for:', selectedFile.name);
                  showToast('AI processing started...', 'info');
                  
                  // Simulate AI processing
                  setTimeout(() => {
                    // Mock extracted data
                    extractedPropertyData = {
                      kangisFileNo: 'LKN/COM/2024/001',
                      plotNo: 'Plot 123',
                      lgsaOrCity: 'Kano Municipal',
                      description: 'Sample property description',
                      instrument: 'DEED OF ASSIGNMENT'
                    };
                    
                    populatePropertyForm();
                    document.getElementById('extracted-details').classList.remove('hidden');
                    showToast('AI processing complete!', 'success');
                  }, 2000);
                }

                function populatePropertyForm() {
                  if (!extractedPropertyData) return;
                  
                  const data = extractedPropertyData;
                  
                  // Populate form fields
                  if (data.kangisFileNo) {
                    const field = document.getElementById('kangisFileNo');
                    if (field) field.value = data.kangisFileNo;
                  }
                  
                  if (data.plotNo) {
                    const field = document.getElementById('plotNo');
                    if (field) field.value = data.plotNo;
                  }
                  
                  if (data.lgsaOrCity) {
                    const field = document.getElementById('lga');
                    if (field) field.value = data.lgsaOrCity;
                  }
                  
                  if (data.description) {
                    const field = document.getElementById('property-description');
                    if (field) field.value = data.description;
                  }
                  
                  if (data.instrument) {
                    const field = document.getElementById('transactionType-record');
                    if (field) {
                      // Map instrument to transaction type
                      const mapping = {
                        'DEED OF ASSIGNMENT': 'Deed of Assignment',
                        'CERTIFICATE OF OCCUPANCY': 'Certificate of Occupancy',
                        'DEED OF MORTGAGE': 'Deed of Mortgage',
                        'POWER OF ATTORNEY': 'Power of Attorney'
                      };
                      field.value = mapping[data.instrument] || '';
                    }
                  }
                }

                function handleSaveRecord() {
                  const form = document.getElementById('property-record-form');
                  if (!form) {
                    showToast('Form not found. Cannot save record.', 'error');
                    return;
                  }

                  if (!extractedPropertyData) {
                    showToast('No property data extracted. Please run AI extraction first.', 'error');
                    return;
                  }

                  showToast('Saving property record...', 'info');

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
                      showToast('Property record saved successfully!', 'success');
                      setTimeout(() => {
                        window.location.reload();
                      }, 1500);
                    } else {
                      showToast('Error: ' + (data.message || 'Failed to save record'), 'error');
                    }
                  })
                  .catch(error => {
                    console.error('Error saving property record:', error);
                    showToast('Error: Failed to save property record', 'error');
                  });
                }

                function resetState() {
                  selectedFile = null;
                  previewUrl = null;
                  pdfPagePreviews = [];
                  currentPdfPreviewPageIdx = 0;
                  extractedPropertyData = null;

                  document.getElementById('file-input').value = '';
                  document.getElementById('file-upload-text').textContent = 'Click to select a file';
                  document.getElementById('image-preview').classList.add('hidden');
                  document.getElementById('pdf-preview').classList.add('hidden');
                  document.getElementById('extracted-details').classList.add('hidden');
                  hideError();
                  updateUI();
                }

                function resetFileState() {
                  selectedFile = null;
                  previewUrl = null;
                  document.getElementById('file-input').value = '';
                  document.getElementById('file-upload-text').textContent = 'Click to select a file';
                  document.getElementById('image-preview').classList.add('hidden');
                  document.getElementById('pdf-preview').classList.add('hidden');
                }

                function updateUI() {
                  const startBtn = document.getElementById('start-ai-btn');
                  const resetBtn = document.getElementById('reset-btn');

                  if (selectedFile) {
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

                function showError(message) {
                  const errorAlert = document.getElementById('error-alert');
                  const errorMessage = document.getElementById('error-message');
                  if (errorAlert && errorMessage) {
                    errorMessage.textContent = message;
                    errorAlert.classList.remove('hidden');
                  }
                }

                function hideError() {
                  const errorAlert = document.getElementById('error-alert');
                  if (errorAlert) {
                    errorAlert.classList.add('hidden');
                  }
                }

                function showToast(message, type = 'info') {
                  const toastContainer = document.getElementById('toast-container');
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
                </script>

                </body>
                </html>
            </div>
        </div>
    </div>
    @include('admin.footer')
</div>
@endsection