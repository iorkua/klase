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

  <!-- Keyword Findings Display -->
  <div id="keyword-findings" class="hidden bg-white rounded-lg shadow border border-gray-200">
    <div class="p-6 border-b border-gray-200">
      <div class="flex items-center space-x-2">
        <i data-lucide="file-key-2" class="h-6 w-6 text-blue-600"></i>
        <h3 class="text-xl font-semibold text-gray-900">Key Document Types Found</h3>
      </div>
      <p id="keyword-findings-description" class="text-sm text-gray-600 mt-1"></p>
    </div>
    <div class="p-6">
      <ul id="keyword-findings-list" class="space-y-2">
        <!-- Keyword findings will be inserted here -->
      </ul>
    </div>
  </div>

  <!-- Raw Extracted Text -->
  <div id="raw-text-card" class="hidden bg-white rounded-lg shadow border border-gray-200">
    <div class="p-6 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold text-gray-900">Raw Extracted Text</h3>
        <button id="toggle-raw-text" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1 transition-all cursor-pointer bg-transparent text-gray-700 hover:bg-gray-100">
          <i data-lucide="chevron-down" class="h-4 w-4"></i>
          Show
        </button>
      </div>
    </div>
    <div id="raw-text-content" class="collapsible-content">
      <div class="p-6">
        <textarea id="raw-text-textarea" readonly rows="10" class="w-full text-xs bg-gray-50 font-mono border border-gray-300 rounded-md p-3"></textarea>
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
      <!-- Property Information Form -->
      <div class="space-y-4">
        <h4 class="text-lg font-medium text-gray-900 border-b pb-2">Property Information</h4>
        
        <div class="p-6 space-y-6">
          @include('propertycard.partials.add_property_record', ['is_ai_assistant' => true])
        </div>
      </div>

      <!-- Instruments Manager -->
      <div class="border-t pt-6">
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <h4 class="text-lg font-medium text-gray-900">Document Instruments</h4>
            <button id="add-instrument-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-2">
              <i data-lucide="plus" class="h-4 w-4"></i>
              Add Instrument
            </button>
          </div>
          
          <!-- No Instruments State -->
          <div id="no-instruments" class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
            <i data-lucide="file-key-2" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
            <p class="text-sm">No instruments added yet</p>
            <p class="text-xs text-gray-400">Click "Add Instrument" to get started</p>
          </div>
          
          <!-- Instruments List -->
          <div id="instruments-list" class="space-y-3">
            <!-- Instruments will be inserted here -->
          </div>
        </div>
      </div>

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

<script>
function aiAssistant() {
  return {
    // State
    selectedFile: null,
    previewUrl: null,
    fileType: null,
    fileInfo: '',
    pdfPages: [],
    currentPdfPageIndex: 0,
    processing: false,
    progress: 0,
    currentStageIndex: 0,
    extractedData: null,
    rawText: '',
    error: null,
    showRawText: false,
    
    // Stages configuration
    stages: [
      { name: 'Init', icon: 'brain', description: 'Initializing AI processing...' },
      { name: 'OCR', icon: 'scan-text', description: 'Extracting text from document...' },
      { name: 'Parse', icon: 'file-search', description: 'Analyzing document structure...' },
      { name: 'Extract', icon: 'layers', description: 'Extracting property details...' },
      { name: 'Validate', icon: 'check-circle', description: 'Validating extracted data...' },
      { name: 'Complete', icon: 'sparkles', description: 'Processing complete!' }
    ],
    
    // Computed properties
    get currentStage() {
      return this.stages[this.currentStageIndex] || this.stages[0];
    },
    
    get currentPdfPage() {
      return this.pdfPages[this.currentPdfPageIndex] || null;
    },
    
    get extractionSummary() {
      if (!this.extractedData) return '';
      
      const data = this.extractedData;
      let summary = `Extracted ${data.confidence}% of available data. `;
      
      if (data.fileNo) summary += `File No: ${data.fileNo}. `;
      if (data.plotNo) summary += `Plot: ${data.plotNo}. `;
      if (data.propertyHolder) summary += `Holder: ${data.propertyHolder}. `;
      if (data.instrument) summary += `Type: ${data.instrument}.`;
      
      return summary;
    },
    
    // Methods
    init() {
      console.log('🚀 Enhanced AI Assistant initialized');
      this.$nextTick(() => {
        lucide.createIcons();
      });
    },
    
    triggerFileInput() {
      console.log('🔄 Triggering file input...');
      const fileInput = this.$refs.fileInput;
      if (fileInput) {
        console.log('✅ File input found, triggering click...');
        // Clear any previous value to ensure change event fires
        fileInput.value = '';
        
        // Add a temporary event listener to ensure we catch the change
        const tempHandler = (e) => {
          console.log('🎯 Temporary handler caught file change');
          this.handleFileSelection(e.target.files[0]);
          fileInput.removeEventListener('change', tempHandler);
        };
        
        fileInput.addEventListener('change', tempHandler);
        
        try {
          fileInput.click();
          console.log('✅ File input clicked successfully');
        } catch (error) {
          console.error('❌ Error clicking file input:', error);
          fileInput.removeEventListener('change', tempHandler);
        }
      } else {
        console.error('❌ File input not found!');
      }
    },
    
    handleDrop(event) {
      console.log('🎯 File drop event triggered');
      event.currentTarget.classList.remove('dragover');
      const files = event.dataTransfer.files;
      if (files.length > 0) {
        console.log('✅ File dropped:', files[0].name);
        this.handleFileSelection(files[0]);
      }
    },
    
    handleFileChange(event) {
      console.log('🎯 File change event triggered');
      const file = event.target.files[0];
      console.log('📁 Selected file:', file ? file.name : 'No file');
      console.log('📊 Event target files length:', event.target.files.length);
      
      this.handleFileSelection(file);
    },
    
    handleFileSelection(file) {
      console.log('🔍 Processing file selection...');
      
      if (!file) {
        console.log('❌ No file provided, resetting...');
        this.reset();
        return;
      }
      
      console.log('📋 File details:', {
        name: file.name,
        size: file.size,
        type: file.type,
        lastModified: file.lastModified
      });
      
      // Clear any previous errors immediately
      this.error = null;
      
      // Set the selected file immediately to enable the button
      this.selectedFile = file;
      console.log('✅ selectedFile set to:', this.selectedFile ? this.selectedFile.name : 'null');
      
      // Force Alpine.js reactivity update
      this.$nextTick(() => {
        console.log('🔄 After nextTick - selectedFile:', this.selectedFile ? this.selectedFile.name : 'null');
        console.log('🔄 Button should be enabled now');
      });
      
      // Process the file for preview
      this.processFileForPreview(file);
    },
    
    async processFileForPreview(file) {
      try {
        console.log('Starting file preview processing for:', file.name);
        
        // Validate file first
        if (!this.validateFile(file)) {
          return false;
        }
        
        // Clear previous state
        this.error = null;
        this.extractedData = null;
        this.rawText = '';
        this.previewUrl = null;
        this.pdfPages = [];
        this.currentPdfPageIndex = 0;
        
        // Set the selected file
        this.selectedFile = file;
        
        // Set file info
        this.fileInfo = `${this.formatFileSize(file.size)} • ${file.type}`;
        
        // Process based on file type
        if (file.type.startsWith('image/')) {
          this.fileType = 'image';
          this.previewUrl = URL.createObjectURL(file);
          console.log('Image file processed successfully for preview:', file.name);
        } else if (file.type === 'application/pdf') {
          this.fileType = 'pdf';
          await this.processPDF(file);
          console.log('PDF file processed successfully for preview:', file.name);
        }
        
        // Update UI
        this.$nextTick(() => {
          lucide.createIcons();
        });
        
        console.log('File preview processing completed successfully');
        return true;
      } catch (error) {
        console.error('Error processing file for preview:', error);
        this.error = `Failed to process file: ${error.message}`;
        return false;
      }
    },
    
    async processFile(file) {
      try {
        console.log('Starting file processing for:', file.name);
        
        // Prevent duplicate processing
        if (this.selectedFile && this.selectedFile.name === file.name && this.selectedFile.size === file.size) {
          console.log('File already processed, skipping...');
          return true;
        }
        
        // Validate file first
        if (!this.validateFile(file)) {
          return false;
        }
        
        // Clear previous state
        this.error = null;
        this.extractedData = null;
        this.rawText = '';
        this.previewUrl = null;
        this.pdfPages = [];
        this.currentPdfPageIndex = 0;
        
        // Set the selected file
        this.selectedFile = file;
        
        // Set file info
        this.fileInfo = `${this.formatFileSize(file.size)} • ${file.type}`;
        
        // Process based on file type
        if (file.type.startsWith('image/')) {
          this.fileType = 'image';
          this.previewUrl = URL.createObjectURL(file);
          console.log('Image file processed successfully:', file.name);
        } else if (file.type === 'application/pdf') {
          this.fileType = 'pdf';
          await this.processPDF(file);
          console.log('PDF file processed successfully:', file.name);
        }
        
        // Update UI
        this.$nextTick(() => {
          lucide.createIcons();
        });
        
        console.log('File processing completed successfully');
        return true;
      } catch (error) {
        console.error('Error processing file:', error);
        this.error = `Failed to process file: ${error.message}`;
        return false;
      }
    },
    
    validateFile(file) {
      const maxSize = 10 * 1024 * 1024; // 10MB
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'application/pdf'];
      
      if (!allowedTypes.includes(file.type)) {
        this.error = 'Invalid file type. Please upload JPEG, PNG, or PDF files only.';
        return false;
      }
      
      if (file.size > maxSize) {
        this.error = 'File size too large. Please upload files smaller than 10MB.';
        return false;
      }
      
      return true;
    },
    
    async processPDF(file) {
      try {
        const arrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
        this.pdfPages = [];
        
        for (let i = 1; i <= pdf.numPages; i++) {
          const page = await pdf.getPage(i);
          const viewport = page.getViewport({ scale: 2.0 }); // Higher scale for better OCR
          const canvas = document.createElement('canvas');
          const context = canvas.getContext('2d');
          
          canvas.height = viewport.height;
          canvas.width = viewport.width;
          
          await page.render({ canvasContext: context, viewport: viewport }).promise;
          this.pdfPages.push(canvas.toDataURL('image/png'));
        }
        
        this.currentPdfPageIndex = 0;
      } catch (error) {
        console.error('PDF processing error:', error);
        this.error = 'Failed to process PDF file.';
      }
    },
    
    prevPdfPage() {
      if (this.currentPdfPageIndex > 0) {
        this.currentPdfPageIndex--;
      }
    },
    
    nextPdfPage() {
      if (this.currentPdfPageIndex < this.pdfPages.length - 1) {
        this.currentPdfPageIndex++;
      }
    },
    
    async startAiProcessing() {
      if (!this.selectedFile) return;
      
      this.processing = true;
      this.progress = 0;
      this.currentStageIndex = 0;
      this.error = null;
      
      try {
        // Stage 1: Initialize
        await this.updateProgress(0, 10);
        
        // Stage 2: OCR
        this.currentStageIndex = 1;
        const text = await this.extractText();
        await this.updateProgress(1, 50);
        
        // Stage 3: Parse
        this.currentStageIndex = 2;
        await this.updateProgress(2, 70);
        
        // Stage 4: Extract
        this.currentStageIndex = 3;
        const extractedData = this.extractPropertyDetails(text);
        await this.updateProgress(3, 85);
        
        // Stage 5: Validate
        this.currentStageIndex = 4;
        const validatedData = this.validateExtractedData(extractedData);
        await this.updateProgress(4, 95);
        
        // Stage 6: Complete
        this.currentStageIndex = 5;
        await this.updateProgress(5, 100);
        
        this.extractedData = validatedData;
        this.rawText = text;
        this.populateForm();
        
        this.showToast('AI processing completed successfully!', 'success');
        
      } catch (error) {
        console.error('AI processing error:', error);
        this.error = `AI Processing failed: ${error.message}`;
        this.showToast('AI processing failed. Please try again.', 'error');
      } finally {
        this.processing = false;
      }
    },
    
    async updateProgress(stageIndex, targetProgress) {
      return new Promise(resolve => {
        const duration = 500;
        const startProgress = this.progress;
        const progressDiff = targetProgress - startProgress;
        const startTime = Date.now();
        
        const animate = () => {
          const elapsed = Date.now() - startTime;
          const progress = Math.min(elapsed / duration, 1);
          
          this.progress = startProgress + (progressDiff * progress);
          
          if (progress < 1) {
            requestAnimationFrame(animate);
          } else {
            resolve();
          }
        };
        
        animate();
      });
    },
    
    async extractText() {
      if (this.fileType === 'pdf') {
        return await this.extractTextFromPDF();
      } else {
        return await this.extractTextFromImage();
      }
    },
    
    async extractTextFromPDF() {
      try {
        const arrayBuffer = await this.selectedFile.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
        let fullText = '';
        let hasSelectableText = false;
        
        // First try to extract selectable text
        for (let i = 1; i <= pdf.numPages; i++) {
          const page = await pdf.getPage(i);
          const textContent = await page.getTextContent();
          const pageText = textContent.items.map(item => item.str).join(' ');
          
          if (pageText.trim().length > 0) {
            fullText += `--- Page ${i} ---\n${pageText}\n\n`;
            hasSelectableText = true;
          }
        }
        
        if (hasSelectableText && fullText.trim().length > 50) {
          return fullText;
        }
        
        // Fallback to OCR if no selectable text
        this.showToast('PDF contains scanned images. Using enhanced OCR...', 'info');
        return await this.performOCROnPDF();
        
      } catch (error) {
        console.error('PDF text extraction error:', error);
        throw new Error('Failed to extract text from PDF');
      }
    },
    
    async performOCROnPDF() {
      let ocrText = '';
      const totalPages = this.pdfPages.length;
      
      for (let i = 0; i < totalPages; i++) {
        const pageProgress = (i / totalPages) * 40; // OCR takes 40% of total progress
        this.progress = 10 + pageProgress;
        
        try {
          // Preprocess image for better OCR
          const preprocessedImage = await this.preprocessImageForOCR(this.pdfPages[i]);
          
          const { data: { text, confidence } } = await Tesseract.recognize(preprocessedImage, 'eng', {
            logger: (m) => {
              if (m.status === 'recognizing text') {
                const ocrProgress = m.progress * (40 / totalPages);
                this.progress = 10 + pageProgress + ocrProgress;
              }
            },
            tessedit_pageseg_mode: Tesseract.PSM.AUTO,
            tessedit_ocr_engine_mode: Tesseract.OEM.LSTM_ONLY,
            tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,/:-()&\'"',
            preserve_interword_spaces: '1',
            user_defined_dpi: '300',
            tessedit_do_invert: '0',
            tessedit_create_hocr: '1',
            tessedit_create_tsv: '1'
          });
          
          console.log(`Page ${i + 1} OCR confidence: ${confidence}%`);
          ocrText += `--- Page ${i + 1} (OCR - ${Math.round(confidence)}% confidence) ---\n${text || 'No text found'}\n\n`;
        } catch (error) {
          console.error(`OCR error on page ${i + 1}:`, error);
          ocrText += `--- Page ${i + 1} (OCR) ---\nOCR failed for this page: ${error.message}\n\n`;
        }
      }
      
      return ocrText;
    },
    
    async extractTextFromImage() {
      try {
        // Preprocess image for better OCR
        const preprocessedImage = await this.preprocessImageForOCR(this.previewUrl);
        
        const { data: { text, confidence } } = await Tesseract.recognize(preprocessedImage, 'eng', {
          logger: (m) => {
            if (m.status === 'recognizing text') {
              this.progress = 10 + (m.progress * 40);
            }
          },
          tessedit_pageseg_mode: Tesseract.PSM.AUTO,
          tessedit_ocr_engine_mode: Tesseract.OEM.LSTM_ONLY,
          tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,/:-() ',
          preserve_interword_spaces: '1',
          user_defined_dpi: '300'
        });
        
        console.log(`Image OCR confidence: ${confidence}%`);
        return text || '';
      } catch (error) {
        console.error('Image OCR error:', error);
        throw new Error('Failed to extract text from image');
      }
    },
    
    async preprocessImageForOCR(imageUrl) {
      return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => {
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');
          
          // Scale up small images for better OCR (up to 2000px on the longest edge, max 2x)
          const maxEdge = Math.max(img.width, img.height);
          const scale = Math.max(1, Math.min(2, 2000 / maxEdge));
          const targetWidth = Math.round(img.width * scale);
          const targetHeight = Math.round(img.height * scale);
          canvas.width = targetWidth;
          canvas.height = targetHeight;

          // Draw image with disabled smoothing to keep edges sharp
          ctx.imageSmoothingEnabled = false;
          ctx.drawImage(img, 0, 0, targetWidth, targetHeight);
          
          // Apply image preprocessing for better OCR
          const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
          const data = imageData.data;

          // Convert to grayscale, enhance contrast, and binarize
          const threshold = 180;
          for (let i = 0; i < data.length; i += 4) {
            const r = data[i], g = data[i + 1], b = data[i + 2];
            const gray = r * 0.299 + g * 0.587 + b * 0.114;
            // Contrast stretch
            let val = (gray - 128) * 1.2 + 128;
            val = Math.max(0, Math.min(255, val));
            // Binarize
            const bin = val > threshold ? 255 : 0;
            data[i] = bin;     // Red
            data[i + 1] = bin; // Green
            data[i + 2] = bin; // Blue
            // Alpha unchanged
          }

          ctx.putImageData(imageData, 0, 0);
          resolve(canvas.toDataURL('image/png'));
        };
        img.src = imageUrl;
      });
    },
    
    extractPropertyDetails(text) {
      const cleanText = text.replace(/(\r\n|\n|\r)/gm, ' ').replace(/\s+/g, ' ').trim();
      
      const data = {
        originalFileName: this.selectedFile.name,
        extractedText: text,
        confidence: 0,
        fileSize: this.formatFileSize(this.selectedFile.size),
        fileType: this.selectedFile.type,
        pageCount: this.fileType === 'pdf' ? this.pdfPages.length : 1,
      };
      
      let foundFields = 0;
      const totalFields = 15;
      
      // Enhanced extraction patterns with more variations and better accuracy
      const patterns = {
        fileNumber: [
          // Standard file number patterns with better boundaries
          /(?:NEW\s+)?FILE\s+(?:NO|NUMBER)[:\s]*([A-Z0-9/\s-]+?)(?:\s+(?:PLOT|TITLE|OLD|DATED|FOR|TO|BEING|SITUATE)|\s*$)/i,
          /(?:File\s*No\.?|FILE\s*NUMBER)\s*:?\s*([A-Z0-9/\s-]+?)(?:\s+(?:PLOT|TITLE|OLD|DATED|FOR|TO|BEING|SITUATE)|\s*$)/i,
          
          // Specific Nigerian patterns with better matching
          /(LKN\/COM\/\d{4}\/\d{2,4})/i,
          /(COM\/\d{4}\/\d{2,4})/i,
          /(KAN\/[A-Z]{2,4}\/\d{4}\/\d{2,4})/i,
          /(KANO\/[A-Z]{2,4}\/\d{4}\/\d{2,4})/i,
          /([A-Z]{2,4}\/[A-Z]{2,4}\/\d{4}\/\d{3,4})/i,
          /(KN\d{3,6})/i,
          /([A-Z]{3,4}\s*\/\s*[A-Z]{3,4}\s*\/\s*\d{4}\s*\/\s*\d{3,4})/i,
          
          // More specific patterns for Nigerian land records
          /(SLTR\/[A-Z]{2,4}\/\d{4}\/\d{2,4})/i,
          /(KANO\/SLTR\/\d{4}\/\d{2,4})/i,
          /(MUN\/[A-Z]{2,4}\/\d{4}\/\d{2,4})/i,
          /(MISC\/[A-Z]{2,4}\/\d{4}\/\d{2,4})/i,
          /(KANMUN\/[A-Z]{2,4}\/\d{4}\/\d{2,4})/i,
          
          // Enhanced patterns for various formats
          /(?:FILE|F)\s*(?:NO|NUMBER|#)\.?\s*:?\s*([A-Z]{2,5}\/[A-Z]{2,5}\/\d{4}\/\d{2,5})/i,
          /(?:REF|REFERENCE)\s*(?:NO|NUMBER)\.?\s*:?\s*([A-Z]{2,5}\/[A-Z]{2,5}\/\d{4}\/\d{2,5})/i,
          
          // Generic patterns with better boundaries
          /FILE\s*NO\s*[:\-]?\s*([A-Z0-9\/\s-]+?)(?:\s+(?:PLOT|TITLE|OLD|DATED|FOR|TO|BEING|SITUATE)|\s*$)/i,
          /F\.?\s*NO\.?\s*[:\-]?\s*([A-Z0-9\/\s-]+?)(?:\s+(?:PLOT|TITLE|OLD|DATED|FOR|TO|BEING|SITUATE)|\s*$)/i,
          /REF\s*NO\s*[:\-]?\s*([A-Z0-9\/\s-]+?)(?:\s+(?:PLOT|TITLE|OLD|DATED|FOR|TO|BEING|SITUATE)|\s*$)/i,
          
          // Pattern for file numbers in parentheses or brackets
          /\(([A-Z]{2,4}\/[A-Z]{2,4}\/\d{4}\/\d{2,4})\)/i,
          /\[([A-Z]{2,4}\/[A-Z]{2,4}\/\d{4}\/\d{2,4})\]/i,
          
          // Additional patterns for common variations
          /APPLICATION\s*(?:NO|NUMBER)\.?\s*:?\s*([A-Z0-9\/\s-]+?)(?:\s|$)/i,
          /APPL\.?\s*(?:NO|NUMBER)\.?\s*:?\s*([A-Z0-9\/\s-]+?)(?:\s|$)/i
        ],
        
        plotNumber: [
          // Standard plot patterns
          /PLOT\s+(?:NO|NUMBER)[:\s]*([A-Z0-9\s-]+?)(?:\s+TITLE|\s+OLD|\s+LAYOUT|\s*$)/i,
          /Plot[:\s]+([A-Z0-9\s-]+?)(?:\s|$)/i,
          /PLOT[:\s]*([A-Z0-9\s-]+?)(?:\s|$)/i,
          /(?:PLOT|PLT)\s*[:\-]?\s*([A-Z0-9\s-]+?)(?:\s|$)/i,
          
          // Block and plot patterns
          /BLOCK\s*([A-Z0-9]+)\s*PLOT\s*([A-Z0-9]+)/i,
          /BLK\s*([A-Z0-9]+)\s*PLT\s*([A-Z0-9]+)/i,
          
          // Layout patterns
          /LAYOUT\s*([A-Z0-9\s-]+?)(?:\s|$)/i,
          /PLOT\s*(?:NO\.?|NUMBER)?\s*[:\-]?\s*([A-Z0-9\-\/ ]+?)(?:\s+(?:BLOCK|TITLE|LAYOUT|DISTRICT|AREA)|\s|$)/i
        ],
        
        propertyHolder: [
          // Title holder patterns
          /(?:TITLE\s+TO|TITLE)[:\s]*([A-Z\s.,'-]+?)(?:\s+OLD\s+FILE|\s+TO|\s+PLOT|\s*$)/i,
          /(?:ASSIGNEE|GRANTEE|HOLDER)[:\s]*([A-Z\s.,'-]+?)(?:\s|$)/i,
          /(?:PROPERTY\s+HOLDER|OWNER)[:\s]*([A-Z\s.,'-]+?)(?:\s|$)/i,
          
          // Name with titles
          /(?:MR\.?|MRS\.?|MS\.?|DR\.?|PROF\.?|ALH\.?|ALHAJI|ALHAJA|CHIEF|HON\.?)\s+([A-Z\s.,'-]+?)(?:\s|$)/i,
          
          // Applicant patterns
          /(?:APPLICANT|OWNER|BENEFICIARY)[:\s]*([A-Z\s.,'-]+?)(?:\s|$)/i,
          /NAME[:\s]*([A-Z\s.,'-]+?)(?:\s|$)/i,
          
          // Company patterns
          /([A-Z\s&.,'-]+(?:LIMITED|LTD|PLC|COMPANY|CORP|ENTERPRISE|VENTURES))/i
        ],
        
        instrument: [
          // Deed types
          /(DEED\s+OF\s+ASSIGNMENT)/i,
          /(DEED\s+OF\s+MORTGAGE)/i,
          /(DEED\s+OF\s+TRANSFER)/i,
          /(DEED\s+OF\s+CONVEYANCE)/i,
          /(DEED\s+OF\s+GIFT)/i,
          
          // Certificate types
          /(CERTIFICATE\s+OF\s+OCCUPANCY)/i,
          /(STATUTORY\s+CERTIFICATE\s+OF\s+OCCUPANCY)/i,
          /(CUSTOMARY\s+CERTIFICATE\s+OF\s+OCCUPANCY)/i,
          /(RIGHT\s+OF\s+OCCUPANCY)/i,
          /(CUSTOMARY\s+RIGHT\s+OF\s+OCCUPANCY)/i,
          
          // Other instruments
          /(POWER\s+OF\s+ATTORNEY)/i,
          /(IRREVOCABLE\s+POWER\s+OF\s+ATTORNEY)/i,
          /(RECERTIFICATION)/i,
          /(SURVEY\s+PLAN)/i,
          /(BUILDING\s+PLAN)/i,
          /(DEVELOPMENT\s+PERMIT)/i
        ],
        
        lga: [
          // LGA patterns
          /(?:LGA|Local\s*Government\s*Area)[:\s]*([A-Za-z\s]+?)(?:\s+State|\s*,|\s*\.|\n|$)/i,
          /(?:WITHIN|IN)\s+([A-Z\s]+)\s+(?:LGA|LOCAL\s+GOVERNMENT)/i,
          
          // Specific Nigerian LGAs
          /(Kano\s+Municipal|Lagos\s+Island|Lagos\s+Mainland|Abuja\s+Municipal|Kaduna\s+North|Kaduna\s+South)/i,
          /(Municipal|Metropolitan)/i,
          
          // State patterns
          /(Kano|Lagos|Abuja|Kaduna|Rivers|Ogun|Oyo|Anambra|Enugu|Delta)\s*(?:State)?/i
        ],
        
        registration: [
          // Registration number patterns
          /(?:Reg|Registration)\s*(?:No|Number)[:\s]*(\d+)[\/\s]*(\d+)[\/\s]*(\d+)/i,
          /Serial\s*No[:\s]*(\d+)\s*Page[:\s]*(\d+)\s*Volume[:\s]*(\d+)/i,
          /(?:S\/N|SERIAL)[:\s]*(\d+)\s*(?:P\/N|PAGE)[:\s]*(\d+)\s*(?:V\/N|VOL)[:\s]*(\d+)/i,
          
          // Simple number patterns
          /(\d{1,4})\s*\/\s*(\d{1,4})\s*\/\s*(\d{1,4})/i,
          /NO\.\s*(\d+)\s*PAGE\s*(\d+)\s*VOL\.\s*(\d+)/i
        ],
        
        location: [
          // Location patterns
          /(?:SITUATED|LOCATED|SITUATE)\s+(?:AT|IN)[:\s]*([A-Z\s,.-]+?)(?:\s+LGA|\s+STATE|\s*$)/i,
          /LOCATION[:\s]*([A-Z\s,.-]+?)(?:\s+LGA|\s+STATE|\s*$)/i,
          /ADDRESS[:\s]*([A-Z\s,.-]+?)(?:\s+LGA|\s+STATE|\s*$)/i,
          /(?:AT|IN)\s+([A-Z\s,.-]+?)(?:\s+LGA|\s+STATE|\s*$)/i,
          
          // Area/District patterns
          /(?:AREA|DISTRICT|WARD)[:\s]*([A-Z\s,.-]+?)(?:\s|$)/i
        ],
        
        area: [
          // Area/size patterns
          /(?:AREA|SIZE|MEASURING)[:\s]*([0-9.,]+)\s*(HECTARES?|HA|SQ\.?\s*M|ACRES?|SQM)/i,
          /([0-9.,]+)\s*(HECTARES?|HA|SQ\.?\s*M|ACRES?|SQM)/i,
          /APPROX\.?\s*([0-9.,]+)\s*(HECTARES?|HA|SQ\.?\s*M|ACRES?|SQM)/i,
          /APPROXIMATELY\s*([0-9.,]+)\s*(HECTARES?|HA|SQ\.?\s*M|ACRES?|SQM)/i
        ],
        
        // Additional patterns for better extraction
        date: [
          /(?:DATE|DATED)[:\s]*(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i,
          /(\d{1,2}(?:st|nd|rd|th)?\s+(?:January|February|March|April|May|June|July|August|September|October|November|December)\s+\d{2,4})/i,
          /(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i
        ],
        
        term: [
          /(?:TERM|PERIOD)[:\s]*(\d+)\s*(YEARS?)/i,
          /FOR\s+A\s+TERM\s+OF\s+(\d+)\s*(YEARS?)/i,
          /(\d+)\s*YEARS?\s*TERM/i
        ],
        
        consideration: [
          /(?:CONSIDERATION|SUM|AMOUNT)[:\s]*(?:NGN|N|₦)?\s*([0-9,]+(?:\.\d{2})?)/i,
          /(?:NGN|N|₦)\s*([0-9,]+(?:\.\d{2})?)/i,
          /NAIRA\s*([0-9,]+(?:\.\d{2})?)/i
        ]
      };
      
      // Extract file number
      for (const pattern of patterns.fileNumber) {
        const match = cleanText.match(pattern);
        if (match?.[1]) {
          data.fileNo = match[1].trim().replace(/\s+/g, ' ');
          foundFields++;
          break;
        }
      }
      
      // Extract plot number
      for (const pattern of patterns.plotNumber) {
        const match = cleanText.match(pattern);
        if (match?.[1]) {
          data.plotNo = match[1].trim().replace(/\s+/g, ' ');
          foundFields++;
          break;
        }
      }
      
      // Extract property holder
      for (const pattern of patterns.propertyHolder) {
        const match = cleanText.match(pattern);
        if (match?.[1]) {
          data.propertyHolder = match[1].trim().replace(/\s+/g, ' ');
          foundFields++;
          break;
        }
      }
      
      // Extract instrument type
      for (const pattern of patterns.instrument) {
        const match = cleanText.match(pattern);
        if (match?.[1]) {
          data.instrument = match[1].trim().toUpperCase();
          foundFields++;
          break;
        }
      }
      
      // Extract LGA
      for (const pattern of patterns.lga) {
        const match = cleanText.match(pattern);
        if (match?.[1]) {
          data.lgsaOrCity = match[1].trim().replace(/\s+/g, ' ');
          foundFields++;
          break;
        }
      }
      
      // Extract location
      for (const pattern of patterns.location) {
        const match = cleanText.match(pattern);
        if (match?.[1]) {
          data.location = match[1].trim().replace(/\s+/g, ' ');
          foundFields++;
          break;
        }
      }
      
      // Extract area
      for (const pattern of patterns.area) {
        const match = cleanText.match(pattern);
        if (match?.[1] && match?.[2]) {
          data.area = `${match[1]} ${match[2]}`;
          foundFields++;
          break;
        }
      }
      
      // Extract registration details
      for (const pattern of patterns.registration) {
        const match = cleanText.match(pattern);
        if (match && match.length >= 4) {
          data.serialNo = match[1];
          data.page = match[2];
          data.vol = match[3];
          data.regNo = `${match[1]}/${match[2]}/${match[3]}`;
          foundFields += 3;
          break;
        }
      }
      
      // Calculate confidence based on found fields and text quality
      const baseConfidence = (foundFields / totalFields) * 100;
      const textQualityBonus = Math.min(20, cleanText.length / 100); // Bonus for longer text
      
      data.confidence = Math.min(100, Math.round(baseConfidence + textQualityBonus));
      data.extractionStatus = data.confidence > 70 ? 'High Confidence' :
                             data.confidence > 40 ? 'Medium Confidence' : 
                             data.confidence > 15 ? 'Low Confidence' : 'Extraction Failed';
      
      return data;
    },
    
    validateExtractedData(data) {
      // Clean and validate file number
      if (data.fileNo) {
        data.fileNo = data.fileNo.replace(/[_\s]+/g, ' ').trim();
        // Remove trailing punctuation
        data.fileNo = data.fileNo.replace(/[,.]$/, '');
      }
      
      // Clean and validate plot number
      if (data.plotNo) {
        data.plotNo = data.plotNo.replace(/[,.]$/, '').trim();
      }
      
      // Clean and validate property holder
      if (data.propertyHolder) {
        data.propertyHolder = data.propertyHolder.replace(/[,.]$/, '').trim();
        // Capitalize properly
        data.propertyHolder = data.propertyHolder.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
      }
      
      // Clean and validate location
      if (data.location) {
        data.location = data.location.replace(/[,.]$/, '').trim();
      }
      
      // Clean and validate LGA
      if (data.lgsaOrCity) {
        data.lgsaOrCity = data.lgsaOrCity.replace(/[,.]$/, '').trim();
      }
      
      return data;
    },
    
    populateForm() {
      if (!this.extractedData) return;
      
      const data = this.extractedData;
      let populatedFields = 0;
      
      try {
        // Helper function to safely populate field
        const populateField = (fieldId, value, eventType = 'input') => {
          if (!value) return false;
          
          const field = document.getElementById(fieldId);
          if (field && field.value !== value) {
            field.value = value;
            field.dispatchEvent(new Event(eventType, { bubbles: true }));
            return true;
          }
          return false;
        };
        
        // Populate basic property fields
        if (populateField('plotNo', data.plotNo)) {
          populatedFields++;
          console.log('Populated plot number:', data.plotNo);
        }
        
        if (populateField('houseNo', data.houseNo)) {
          populatedFields++;
          console.log('Populated house number:', data.houseNo);
        }
        
        // Populate location fields
        if (populateField('lga', data.lgsaOrCity, 'change')) {
          populatedFields++;
          console.log('Populated LGA:', data.lgsaOrCity);
        }
        
        if (populateField('state', data.state || 'Kano State')) {
          populatedFields++;
          console.log('Populated state:', data.state || 'Kano State');
        }
        
        // Populate registration fields
        if (populateField('serialNo', data.serialNo)) {
          populatedFields++;
          console.log('Populated serial number:', data.serialNo);
        }
        
        if (populateField('pageNo', data.page)) {
          populatedFields++;
          console.log('Populated page number:', data.page);
        }
        
        if (populateField('volumeNo', data.vol)) {
          populatedFields++;
          console.log('Populated volume number:', data.vol);
        }
        
        // Populate transaction type with enhanced mapping
        if (data.instrument) {
          const transactionField = document.getElementById('transactionType-record');
          if (transactionField) {
            const mapping = {
              'DEED OF ASSIGNMENT': 'Deed of Assignment',
              'CERTIFICATE OF OCCUPANCY': 'Certificate of Occupancy',
              'STATUTORY CERTIFICATE OF OCCUPANCY': 'ST Certificate of Occupancy',
              'CUSTOMARY CERTIFICATE OF OCCUPANCY': 'Customary Right of Occupancy',
              'RIGHT OF OCCUPANCY': 'Customary Right of Occupancy',
              'CUSTOMARY RIGHT OF OCCUPANCY': 'Customary Right of Occupancy',
              'DEED OF MORTGAGE': 'Deed of Mortgage',
              'POWER OF ATTORNEY': 'Power of Attorney',
              'IRREVOCABLE POWER OF ATTORNEY': 'Irrevocable Power of Attorney',
              'DEED OF TRANSFER': 'Deed of Transfer',
              'DEED OF CONVEYANCE': 'Deed of Conveyance',
              'DEED OF GIFT': 'Deed of Gift',
              'RECERTIFICATION': 'Other',
              'SURVEY PLAN': 'Other',
              'BUILDING PLAN': 'Other'
            };
            
            const mappedValue = mapping[data.instrument] || 'Other';
            if (transactionField.value !== mappedValue) {
              transactionField.value = mappedValue;
              transactionField.dispatchEvent(new Event('change', { bubbles: true }));
              populatedFields++;
              console.log('Populated transaction type:', mappedValue);
            }
          }
        }
        
        // Update registration preview
        this.updateRegNoPreview();
        
        // Show success message with details
        const successMessage = populatedFields > 0 
          ? `Successfully populated ${populatedFields} fields with ${data.confidence}% confidence`
          : `AI extraction completed with ${data.confidence}% confidence, but no form fields were populated`;
          
        this.showToast(successMessage, populatedFields > 0 ? 'success' : 'warning');
        
        console.log(`Form population completed. Populated ${populatedFields} fields.`);
        
      } catch (error) {
        console.error('Error populating form:', error);
        this.showToast('Error populating form fields. Please check the extracted data manually.', 'error');
      }
    },
    
    updateRegNoPreview() {
      const serialNo = document.getElementById('serialNo')?.value || '';
      const pageNo = document.getElementById('pageNo')?.value || '';
      const volumeNo = document.getElementById('volumeNo')?.value || '';
      
      const regNoDisplay = [serialNo, pageNo, volumeNo].filter(Boolean).join('/') || 'Not set';
      
      // Update Alpine.js reactive elements
      const alpineElements = document.querySelectorAll('[x-text*="regNoDisplay"]');
      alpineElements.forEach(element => {
        if (element._x_dataStack && element._x_dataStack[0]) {
          element._x_dataStack[0].serialNo = serialNo;
          element._x_dataStack[0].pageNo = pageNo;
          element._x_dataStack[0].volumeNo = volumeNo;
        }
      });
    },
    
    reset() {
      this.selectedFile = null;
      this.previewUrl = null;
      this.fileType = null;
      this.fileInfo = '';
      this.pdfPages = [];
      this.currentPdfPageIndex = 0;
      this.processing = false;
      this.progress = 0;
      this.currentStageIndex = 0;
      this.extractedData = null;
      this.rawText = '';
      this.error = null;
      this.showRawText = false;
      
      // Clear file input
      this.$refs.fileInput.value = '';
      
      this.$nextTick(() => {
        lucide.createIcons();
      });
    },
    
    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    showToast(message, type = 'info') {
      const toastContainer = document.getElementById('toast-container');
      const toastId = `toast-${Date.now()}`;
      
      const typeClasses = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-600 text-white',
        warning: 'bg-yellow-600 text-white',
        info: 'bg-blue-600 text-white'
      };
      
      const toast = document.createElement('div');
      toast.id = toastId;
      toast.className = `${typeClasses[type]} px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 transform translate-x-full transition-transform duration-300 max-w-sm`;
      toast.innerHTML = `
        <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : type === 'warning' ? 'alert-triangle' : 'info'}" class="h-5 w-5 flex-shrink-0"></i>
        <span class="text-sm font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto hover:bg-black/20 rounded p-1">
          <i data-lucide="x" class="h-4 w-4"></i>
        </button>
      `;
      
      toastContainer.appendChild(toast);
      lucide.createIcons();
      
      setTimeout(() => {
        toast.classList.remove('translate-x-full');
      }, 100);
      
      setTimeout(() => {
        if (toast.parentElement) {
          toast.classList.add('translate-x-full');
          setTimeout(() => toast.remove(), 300);
        }
      }, 5000);
    }
  }
}

// Initialize PDF.js
if (window.pdfjsLib) {
  window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
}

console.log('🚀 Enhanced AI Property Record Assistant v2 loaded successfully');
</script>
</body>
</html>