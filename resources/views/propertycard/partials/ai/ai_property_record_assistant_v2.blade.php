<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enhanced AI Property Record Assistant - SLTR</title>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<!-- Enhanced Tesseract.js for better OCR -->
<script src="https://unpkg.com/tesseract.js@5/dist/tesseract.min.js"></script>
<!-- Alpine.js for reactive components -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
/* Enhanced loading spinner */
.loading-spinner {
  width: 1.5rem;
  height: 1.5rem;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Enhanced file drop zone */
.file-drop-zone {
  border: 2px dashed #d1d5db;
  transition: all 0.3s ease;
  min-height: 120px;
}

.file-drop-zone:hover {
  border-color: #3b82f6;
  background-color: #f8fafc;
}

.file-drop-zone.dragover {
  border-color: #3b82f6;
  background-color: #eff6ff;
  transform: scale(1.02);
}

/* Enhanced progress bar */
.progress-bar {
  transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* AI stage indicators with better animations */
.stage-indicator {
  transition: all 0.4s ease;
}

.stage-indicator.active {
  animation: pulse 2s infinite;
}

.stage-indicator.completed {
  animation: bounce 0.6s ease-in-out;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.05); }
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-10px); }
  60% { transform: translateY(-5px); }
}

/* Enhanced collapsible content */
.collapsible-content {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.collapsible-content.expanded {
  max-height: 2000px;
}

/* Enhanced toast notifications */
.toast-enter {
  transform: translateX(100%);
  opacity: 0;
}

.toast-enter-active {
  transform: translateX(0);
  opacity: 1;
  transition: all 0.3s ease-out;
}

.toast-exit {
  transform: translateX(0);
  opacity: 1;
}

.toast-exit-active {
  transform: translateX(100%);
  opacity: 0;
  transition: all 0.3s ease-in;
}

/* Enhanced form styling */
.form-input:focus {
  ring-2 ring-blue-500 ring-opacity-50;
  border-color: #3b82f6;
}

/* Enhanced confidence indicator */
.confidence-high { color: #059669; }
.confidence-medium { color: #d97706; }
.confidence-low { color: #dc2626; }
</style>
</head>
<body class="min-h-screen bg-gray-50">

<div class="container mx-auto py-6 space-y-6 max-w-6xl px-4 sm:px-6 lg:px-8" x-data="aiAssistant()">
  
  <!-- Enhanced Page Header -->
  <div class="space-y-2">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900 flex items-center">
      <i data-lucide="brain" class="mr-3 h-8 w-8 text-blue-600"></i>
      Enhanced AI Property Record Assistant
    </h1>
    <p class="text-lg text-gray-600">Upload property documents for automated data extraction with improved AI processing</p>
    <div class="flex items-center space-x-4 text-sm text-gray-500">
      <span class="flex items-center"><i data-lucide="check-circle" class="mr-1 h-4 w-4"></i>Enhanced OCR</span>
      <span class="flex items-center"><i data-lucide="zap" class="mr-1 h-4 w-4"></i>Faster Processing</span>
      <span class="flex items-center"><i data-lucide="target" class="mr-1 h-4 w-4"></i>Better Accuracy</span>
    </div>
  </div>

  <!-- Enhanced File Upload Card -->
  <div class="bg-white rounded-lg shadow-lg border border-gray-200">
    <div class="p-6 border-b border-gray-200">
      <h2 class="text-xl font-semibold text-gray-900 flex items-center">
        <i data-lucide="upload" class="mr-2 h-5 w-5 text-blue-600"></i>
        Upload Property Document
      </h2>
      <p class="text-sm text-gray-600 mt-1">Supports JPEG, PNG images and PDF documents. Maximum file size: 10MB</p>
    </div>
    
    <div class="p-6 space-y-4">
      <!-- Enhanced Error Alert -->
      <div x-show="error" x-transition class="bg-red-50 border border-red-200 rounded-md p-4">
        <div class="flex">
          <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Error</h3>
            <div x-text="error" class="mt-2 text-sm text-red-700"></div>
          </div>
        </div>
      </div>

      <!-- Enhanced File Upload Area with Drag & Drop -->
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Document File</label>
        <div 
          class="file-drop-zone flex flex-col items-center justify-center px-6 py-8 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50"
          @drop.prevent="handleDrop($event)"
          @dragover.prevent="$event.currentTarget.classList.add('dragover')"
          @dragleave.prevent="$event.currentTarget.classList.remove('dragover')"
          @click="$refs.fileInput.click()"
        >
          <input
            x-ref="fileInput"
            type="file"
            accept="image/jpeg,image/png,application/pdf"
            class="hidden"
            @change="handleFileChange($event)"
          />
          <div class="text-center">
            <i data-lucide="file-up" class="mx-auto h-12 w-12 text-gray-400"></i>
            <div class="mt-4">
              <p class="text-lg font-medium text-gray-900" x-text="selectedFile ? selectedFile.name : 'Drop files here or click to browse'"></p>
              <p class="text-sm text-gray-500">JPEG, PNG, PDF up to 10MB</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Preview Section -->
      <div x-show="previewUrl" x-transition class="border rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <label class="text-sm font-medium text-gray-700">Document Preview</label>
          <span x-text="fileInfo" class="text-xs text-gray-500"></span>
        </div>
        
        <!-- Image Preview -->
        <div x-show="fileType === 'image'" class="text-center">
          <img :src="previewUrl" class="max-w-full h-auto max-h-96 rounded-md mx-auto border" />
        </div>
        
        <!-- PDF Preview -->
        <div x-show="fileType === 'pdf'" class="text-center space-y-2">
          <img :src="currentPdfPage" class="max-w-full h-auto max-h-96 rounded-md mx-auto border" />
          <div x-show="pdfPages.length > 1" class="flex justify-center items-center space-x-2">
            <button @click="prevPdfPage()" :disabled="currentPdfPageIndex === 0" 
                    class="px-3 py-1 text-sm border rounded hover:bg-gray-50 disabled:opacity-50">
              Previous
            </button>
            <span class="text-sm text-gray-500" x-text="`Page ${currentPdfPageIndex + 1} of ${pdfPages.length}`"></span>
            <button @click="nextPdfPage()" :disabled="currentPdfPageIndex === pdfPages.length - 1"
                    class="px-3 py-1 text-sm border rounded hover:bg-gray-50 disabled:opacity-50">
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Action Buttons -->
    <div class="px-6 pb-6 flex flex-col sm:flex-row gap-3">
      <button
        @click="startAiProcessing()"
        :disabled="!selectedFile || processing"
        class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-md font-medium text-sm px-6 py-3 transition-all cursor-pointer border-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
      >
        <div x-show="!processing" class="flex items-center">
          <i data-lucide="wand-2" class="mr-2 h-4 w-4"></i>
          <span x-text="extractedData ? 'Re-process with AI' : 'Extract Data with AI'"></span>
        </div>
        <div x-show="processing" class="flex items-center">
          <div class="loading-spinner mr-2"></div>
          Processing...
        </div>
      </button>
      <button
        x-show="selectedFile || extractedData"
        @click="reset()"
        class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-3 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50"
      >
        <i data-lucide="refresh-cw" class="mr-2 h-4 w-4"></i>
        Reset
      </button>
    </div>
  </div>

  <!-- Enhanced AI Processing Visualizer -->
  <div x-show="processing" x-transition class="bg-white rounded-lg shadow-lg border border-gray-200">
    <div class="p-6">
      <div class="flex justify-between mb-4">
        <span class="text-lg font-semibold text-gray-900">AI Document Analysis</span>
        <span class="text-sm font-medium" x-text="`${Math.round(progress)}% Complete`"></span>
      </div>
      
      <!-- Enhanced Progress Bar -->
      <div class="relative mb-6">
        <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
          <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full progress-bar" 
               :style="`width: ${progress}%`"></div>
        </div>
      </div>
      
      <!-- Enhanced Stage Indicators -->
      <div class="flex justify-between mb-6">
        <template x-for="(stage, index) in stages" :key="index">
          <div class="flex flex-col items-center stage-indicator" 
               :class="{ 'active': currentStageIndex === index, 'completed': currentStageIndex > index }">
            <div class="w-5 h-5 rounded-full mb-2 transition-all duration-300"
                 :class="currentStageIndex > index ? 'bg-green-500' : (currentStageIndex === index ? 'bg-blue-500 ring-4 ring-blue-100' : 'bg-gray-300')">
            </div>
            <span class="text-xs font-medium" 
                  :class="currentStageIndex >= index ? 'text-blue-600' : 'text-gray-500'"
                  x-text="stage.name"></span>
          </div>
        </template>
      </div>
      
      <!-- Current Stage Info -->
      <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-lg">
        <div class="p-2 rounded-full bg-blue-100">
          <i :data-lucide="currentStage.icon" class="h-6 w-6 text-blue-600"></i>
        </div>
        <div>
          <p class="font-semibold text-blue-900" x-text="`Current Stage: ${currentStage.name}`"></p>
          <p class="text-sm text-blue-700" x-text="currentStage.description"></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Enhanced Results Section -->
  <div x-show="extractedData" x-transition class="space-y-6">
    
    <!-- Document Analysis Results -->
    <div class="bg-white rounded-lg shadow-lg border-l-4 border-l-green-500">
      <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2">
            <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
            <h3 class="text-xl font-semibold text-gray-900">AI Extraction Complete</h3>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Confidence:</span>
            <span class="font-semibold" 
                  :class="extractedData?.confidence > 70 ? 'confidence-high' : (extractedData?.confidence > 40 ? 'confidence-medium' : 'confidence-low')"
                  x-text="`${extractedData?.confidence || 0}%`"></span>
          </div>
        </div>
        <p class="text-sm text-gray-600 mt-2" x-text="extractionSummary"></p>
      </div>
     
      <div class="p-6 space-y-6">
        @include('propertycard.partials.add_property_record', ['is_ai_assistant' => true])
      </div>
    </div>

    <!-- Raw Text Display -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
      <div class="p-4 border-b border-gray-200">
        <button @click="showRawText = !showRawText" 
                class="flex items-center justify-between w-full text-left">
          <h3 class="text-lg font-semibold text-gray-900">Raw Extracted Text</h3>
          <i :data-lucide="showRawText ? 'chevron-up' : 'chevron-down'" class="h-4 w-4"></i>
        </button>
      </div>
      <div x-show="showRawText" x-transition class="p-4">
        <textarea x-model="rawText" readonly rows="10" 
                  class="w-full text-xs bg-gray-50 font-mono border border-gray-300 rounded-md p-3"></textarea>
      </div>
    </div>
  </div>

</div>

<!-- Enhanced Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
  <!-- Toast messages will be inserted here -->
</div>

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
    
    handleDrop(event) {
      event.currentTarget.classList.remove('dragover');
      const files = event.dataTransfer.files;
      if (files.length > 0) {
        this.processFile(files[0]);
      }
    },
    
    handleFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.processFile(file);
      }
    },
    
    async processFile(file) {
      // Validate file
      if (!this.validateFile(file)) return;
      
      this.selectedFile = file;
      this.error = null;
      this.extractedData = null;
      this.rawText = '';
      
      // Set file info
      this.fileInfo = `${this.formatFileSize(file.size)} • ${file.type}`;
      
      // Process based on file type
      if (file.type.startsWith('image/')) {
        this.fileType = 'image';
        this.previewUrl = URL.createObjectURL(file);
      } else if (file.type === 'application/pdf') {
        this.fileType = 'pdf';
        await this.processPDF(file);
      }
      
      this.$nextTick(() => {
        lucide.createIcons();
      });
    },
    
    validateFile(file) {
      const maxSize = 10 * 1024 * 1024; // 10MB
      const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
      
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
          const viewport = page.getViewport({ scale: 1.5 });
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
        this.showToast('PDF contains scanned images. Using OCR...', 'info');
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
          const { data: { text } } = await Tesseract.recognize(this.pdfPages[i], 'eng', {
            logger: (m) => {
              if (m.status === 'recognizing text') {
                const ocrProgress = m.progress * (40 / totalPages);
                this.progress = 10 + pageProgress + ocrProgress;
              }
            }
          });
          
          ocrText += `--- Page ${i + 1} (OCR) ---\n${text || 'No text found'}\n\n`;
        } catch (error) {
          console.error(`OCR error on page ${i + 1}:`, error);
          ocrText += `--- Page ${i + 1} (OCR) ---\nOCR failed for this page\n\n`;
        }
      }
      
      return ocrText;
    },
    
    async extractTextFromImage() {
      try {
        const { data: { text } } = await Tesseract.recognize(this.previewUrl, 'eng', {
          logger: (m) => {
            if (m.status === 'recognizing text') {
              this.progress = 10 + (m.progress * 40);
            }
          }
        });
        
        return text || '';
      } catch (error) {
        console.error('Image OCR error:', error);
        throw new Error('Failed to extract text from image');
      }
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
      const totalFields = 12;
      
      // Enhanced extraction patterns
      const patterns = {
        fileNumber: [
          /(?:NEW\s+)?FILE\s+(?:NO|NUMBER)[:\s]*([A-Z0-9/\s-]+?)(?:\s+PLOT|\s+TITLE|\s*$)/i,
          /(?:File\s*No\.?|FILE\s*NUMBER)\s*:?\s*([A-Z0-9/\s-]+)/i,
          /(LKN\/COM\/\d{4}\/\d{2,4})/i,
          /(COM\/\d{4}\/\d{2,4})/i,
          /([A-Z]{2,4}\/[A-Z]{2,4}\/\d{4}\/\d{3,4})/i,
          /(KN\d+)/i
        ],
        plotNumber: [
          /PLOT\s+(?:NO|NUMBER)[:\s]*([A-Z0-9\s-]+?)(?:\s+TITLE|\s+OLD|\s*$)/i,
          /Plot[:\s]+([A-Z0-9\s-]+)/i
        ],
        propertyHolder: [
          /TITLE[:\s]*([A-Z\s.,'-]+?)(?:\s+OLD\s+FILE|\s+TO|\s*$)/i,
          /(?:ASSIGNEE|GRANTEE|HOLDER)[:\s]*([A-Z\s.,'-]+)/i,
          /(?:MR\.?|MRS\.?|DR\.?|PROF\.?|ALH\.?|ALHAJI)\s+([A-Z\s.,'-]+)/i
        ],
        instrument: [
          /(DEED\s+OF\s+ASSIGNMENT)/i,
          /(CERTIFICATE\s+OF\s+OCCUPANCY)/i,
          /(RIGHT\s+OF\s+OCCUPANCY)/i,
          /(DEED\s+OF\s+MORTGAGE)/i,
          /(POWER\s+OF\s+ATTORNEY)/i,
          /(RECERTIFICATION)/i
        ],
        lga: [
          /(?:LGA|Local\s*Government)[:\s]*([A-Za-z\s]+?)(?:\s+State|\s*,|\s*\.|\n|$)/i,
          /(Kano|Lagos|Abuja|Kaduna|Port\s+Harcourt)/i
        ],
        registration: [
          /(?:Reg|Registration)\s*(?:No|Number)[:\s]*(\d+)[\/\s]*(\d+)[\/\s]*(\d+)/i,
          /Serial\s*No[:\s]*(\d+)\s*Page[:\s]*(\d+)\s*Volume[:\s]*(\d+)/i
        ]
      };
      
      // Extract file number
      for (const pattern of patterns.fileNumber) {
        const match = cleanText.match(pattern);
        if (match?.[1]) {
          data.fileNo = match[1].trim();
          foundFields++;
          break;
        }
      }
      
      // Extract plot number
      for (const pattern of patterns.plotNumber) {
        const match = cleanText.match(pattern);
        if (match?.[1]) {
          data.plotNo = match[1].trim();
          foundFields++;
          break;
        }
      }
      
      // Extract property holder
      for (const pattern of patterns.propertyHolder) {
        const match = cleanText.match(pattern);
        if (match?.[1]) {
          data.propertyHolder = match[1].trim();
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
          data.lgsaOrCity = match[1].trim();
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
      
      // Calculate confidence
      data.confidence = Math.min(100, Math.round((foundFields / totalFields) * 100));
      data.extractionStatus = data.confidence > 70 ? 'High Confidence' :
                             data.confidence > 40 ? 'Medium Confidence' : 
                             data.confidence > 15 ? 'Low Confidence' : 'Extraction Failed';
      
      return data;
    },
    
    validateExtractedData(data) {
      // Add validation logic here
      if (data.fileNo) {
        data.fileNo = data.fileNo.replace(/[_\s]+/g, '').trim();
      }
      
      if (data.plotNo) {
        data.plotNo = data.plotNo.replace(/[,.]$/, '').trim();
      }
      
      if (data.propertyHolder) {
        data.propertyHolder = data.propertyHolder.replace(/[,.]$/, '').trim();
      }
      
      return data;
    },
    
    populateForm() {
      if (!this.extractedData) return;
      
      const data = this.extractedData;
      
      // Populate basic fields
      if (data.plotNo) {
        const plotField = document.getElementById('plotNo');
        if (plotField) plotField.value = data.plotNo;
      }
      
      if (data.lgsaOrCity) {
        const lgaField = document.getElementById('lga');
        if (lgaField) lgaField.value = data.lgsaOrCity;
      }
      
      // Populate registration fields
      if (data.serialNo) {
        const serialField = document.getElementById('serialNo');
        if (serialField) serialField.value = data.serialNo;
      }
      
      if (data.page) {
        const pageField = document.getElementById('pageNo');
        if (pageField) pageField.value = data.page;
      }
      
      if (data.vol) {
        const volField = document.getElementById('volumeNo');
        if (volField) volField.value = data.vol;
      }
      
      // Populate transaction type
      if (data.instrument) {
        const transactionField = document.getElementById('transactionType-record');
        if (transactionField) {
          const mapping = {
            'DEED OF ASSIGNMENT': 'Deed of Assignment',
            'CERTIFICATE OF OCCUPANCY': 'Certificate of Occupancy',
            'RIGHT OF OCCUPANCY': 'Customary Right of Occupancy',
            'DEED OF MORTGAGE': 'Deed of Mortgage',
            'POWER OF ATTORNEY': 'Power of Attorney',
            'RECERTIFICATION': 'Other'
          };
          transactionField.value = mapping[data.instrument] || 'Other';
          transactionField.dispatchEvent(new Event('change'));
        }
      }
      
      // Update registration preview
      this.updateRegNoPreview();
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