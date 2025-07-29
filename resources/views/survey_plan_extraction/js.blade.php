
<script>
  // Global state
  let selectedFiles = [];
  let extractedMetadata = {};
  let rawOcrText = {};
  let uploadStatus = 'idle';
  let uploadProgress = 0;
  let aiStage = 'idle';
  let aiProgress = 0;
  let currentEditingFile = null;
  let currentPreviewFile = null;
  let pdfPages = {};
  let currentEditPreviewPage = 0;
  let processingCancelled = false;
  
  // Document rendering cache
  let renderedDocuments = {};
  let fileObjectUrls = {};
  
  // Initialize PDF.js
  if (window.pdfjsLib) {
    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
  }
  
  // Initialize the application
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Set up event listeners
    setupEventListeners();
    
    // Update UI
    updateUI();
  });
  
  function setupEventListeners() {
    // File input and upload area
    const fileInput = document.getElementById('file-input');
    const uploadArea = document.getElementById('upload-area');
    const browseBtn = document.getElementById('browse-btn');
    
    if (fileInput) {
      fileInput.addEventListener('change', handleFileSelect);
    }
    
    if (browseBtn) {
      browseBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (fileInput) fileInput.click();
      });
    }
    
    if (uploadArea) {
      // uploadArea.addEventListener('click', (e) => {
      //   // Don't trigger if the click was on the browse button
      //   if (e.target.closest('#browse-btn')) return;
      //   e.preventDefault();
      //   e.stopPropagation();
      //   if (fileInput) fileInput.click();
      // });
      
      // Drag and drop
      uploadArea.addEventListener('dragover', handleDragOver);
      uploadArea.addEventListener('drop', handleDrop);
      uploadArea.addEventListener('dragleave', handleDragLeave);
    }
  
    // Tab switching
    const tabUpload = document.getElementById('tab-upload');
    const tabExtracted = document.getElementById('tab-extracted');
    
    if (tabUpload) {
      tabUpload.addEventListener('click', () => switchTab('upload'));
    }
    if (tabExtracted) {
      tabExtracted.addEventListener('click', () => switchTab('extracted'));
    }
  
    // Action buttons
    const clearAllBtn = document.getElementById('clear-all-btn');
    const startUploadBtn = document.getElementById('start-upload-btn');
    const cancelUploadBtn = document.getElementById('cancel-upload-btn');
    const newBatchBtn = document.getElementById('new-batch-btn');
    const saveToDbBtn = document.getElementById('save-to-db-btn');
  
    if (clearAllBtn) clearAllBtn.addEventListener('click', clearAllFiles);
    if (startUploadBtn) startUploadBtn.addEventListener('click', startUpload);
    if (cancelUploadBtn) cancelUploadBtn.addEventListener('click', cancelUpload);
    if (newBatchBtn) newBatchBtn.addEventListener('click', startNewBatch);
    if (saveToDbBtn) saveToDbBtn.addEventListener('click', saveToDatabase);
  }
  
  function handleFileSelect(event) {
    const files = Array.from(event.target.files);
    
    // Validate file types
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/tiff', 'image/tif', 'image/webp'];
    const validFiles = files.filter(file => {
      return allowedTypes.includes(file.type) || file.name.toLowerCase().match(/\.(pdf|jpg|jpeg|png|tiff|tif|webp)$/);
    });
  
    if (validFiles.length !== files.length) {
      showToast(`${files.length - validFiles.length} file(s) skipped due to unsupported format`, 'warning');
    }
  
    if (validFiles.length > 5) {
      showToast('Maximum 5 files allowed', 'error');
      selectedFiles = validFiles.slice(0, 5);
    } else {
      selectedFiles = validFiles;
    }
    
    // Create object URLs for the files
    selectedFiles.forEach((file, index) => {
      const fileId = `file-${Date.now()}-${index}`;
      fileObjectUrls[fileId] = URL.createObjectURL(file);
    });
    
    updateUI();
    if (selectedFiles.length > 0) {
      showToast(`${selectedFiles.length} file(s) selected successfully`, 'success');
    }
  }
  
  function handleDragOver(event) {
    event.preventDefault();
    event.stopPropagation();
    const uploadArea = document.getElementById('upload-area');
    if (uploadArea) {
      uploadArea.classList.add('dragover');
    }
  }
  
  function handleDrop(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const uploadArea = document.getElementById('upload-area');
    if (uploadArea) {
      uploadArea.classList.remove('dragover');
    }
    
    const files = Array.from(event.dataTransfer.files);
    
    // Validate file types
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/tiff', 'image/tif', 'image/webp'];
    const validFiles = files.filter(file => {
      return allowedTypes.includes(file.type) || file.name.toLowerCase().match(/\.(pdf|jpg|jpeg|png|tiff|tif|webp)$/);
    });
  
    if (validFiles.length !== files.length) {
      showToast(`${files.length - validFiles.length} file(s) skipped due to unsupported format`, 'warning');
    }
  
    if (validFiles.length > 5) {
      showToast('Maximum 5 files allowed', 'error');
      selectedFiles = validFiles.slice(0, 5);
    } else {
      selectedFiles = validFiles;
    }
    
    // Create object URLs for the files
    selectedFiles.forEach((file, index) => {
      const fileId = `file-${Date.now()}-${index}`;
      fileObjectUrls[fileId] = URL.createObjectURL(file);
    });
    
    updateUI();
    if (selectedFiles.length > 0) {
      showToast(`${selectedFiles.length} file(s) added successfully`, 'success');
    }
  }
  
  function handleDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();
    const uploadArea = document.getElementById('upload-area');
    if (uploadArea) {
      uploadArea.classList.remove('dragover');
    }
  }
  
  function clearAllFiles() {
    // Clean up object URLs
    Object.values(fileObjectUrls).forEach(url => URL.revokeObjectURL(url));
    
    selectedFiles = [];
    extractedMetadata = {};
    rawOcrText = {};
    pdfPages = {};
    renderedDocuments = {};
    fileObjectUrls = {};
    uploadStatus = 'idle';
    uploadProgress = 0;
    aiStage = 'idle';
    aiProgress = 0;
    processingCancelled = false;
    
    const fileInput = document.getElementById('file-input');
    if (fileInput) {
      fileInput.value = '';
    }
    
    updateUI();
    showToast('All files cleared', 'info');
  }
  
  function removeFile(index) {
    // Clean up object URL for removed file
    const fileId = `file-${Date.now()}-${index}`;
    if (fileObjectUrls[fileId]) {
      URL.revokeObjectURL(fileObjectUrls[fileId]);
      delete fileObjectUrls[fileId];
    }
    
    selectedFiles.splice(index, 1);
    updateUI();
    showToast('File removed', 'info');
  }
  
  // Document rendering functions
  async function renderPDFPages(file) {
    try {
      if (!window.pdfjsLib) {
        throw new Error('PDF.js not loaded');
      }
      
      const arrayBuffer = await file.arrayBuffer();
      const pdf = await window.pdfjsLib.getDocument({ data: arrayBuffer }).promise;
      const pageImages = [];
      
      for (let i = 1; i <= pdf.numPages; i++) {
        const page = await pdf.getPage(i);
        const viewport = page.getViewport({ scale: 1.5 });
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        
        await page.render({ canvasContext: context, viewport: viewport }).promise;
        pageImages.push(canvas.toDataURL('image/png'));
      }
      
      return pageImages;
    } catch (error) {
      console.error('Error rendering PDF:', error);
      return [`data:image/svg+xml;base64,${btoa('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="800" viewBox="0 0 600 800"><rect width="600" height="800" fill="#f3f4f6"/><text x="300" y="400" text-anchor="middle" font-family="Arial" font-size="16" fill="#6b7280">PDF Render Failed</text></svg>')}`];
    }
  }
  
  async function renderImageFile(file) {
    return new Promise((resolve) => {
      const reader = new FileReader();
      reader.onload = (e) => resolve([e.target.result]);
      reader.onerror = () => resolve([`data:image/svg+xml;base64,${btoa('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="800" viewBox="0 0 600 800"><rect width="600" height="800" fill="#f3f4f6"/><text x="300" y="400" text-anchor="middle" font-family="Arial" font-size="16" fill="#6b7280">Image Load Failed</text></svg>')}`]);
      reader.readAsDataURL(file);
    });
  }
  
  async function ensureDocumentRendered(fileId) {
    if (renderedDocuments[fileId]) {
      return renderedDocuments[fileId];
    }
    
    const metadata = extractedMetadata[fileId];
    if (!metadata) {
      return [`data:image/svg+xml;base64,${btoa('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="800" viewBox="0 0 600 800"><rect width="600" height="800" fill="#f3f4f6"/><text x="300" y="400" text-anchor="middle" font-family="Arial" font-size="16" fill="#6b7280">File Not Found</text></svg>')}`];
    }
    
    const originalFile = selectedFiles[metadata.originalFileIndex];
    if (!originalFile) {
      return [`data:image/svg+xml;base64,${btoa('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="800" viewBox="0 0 600 800"><rect width="600" height="800" fill="#f3f4f6"/><text x="300" y="400" text-anchor="middle" font-family="Arial" font-size="16" fill="#6b7280">Original File Missing</text></svg>')}`];
    }
    
    try {
      let pages;
      if (originalFile.type === 'application/pdf') {
        pages = await renderPDFPages(originalFile);
      } else if (originalFile.type.startsWith('image/')) {
        pages = await renderImageFile(originalFile);
      } else {
        pages = [`data:image/svg+xml;base64,${btoa('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="800" viewBox="0 0 600 800"><rect width="600" height="800" fill="#f3f4f6"/><text x="300" y="400" text-anchor="middle" font-family="Arial" font-size="16" fill="#6b7280">Unsupported File Type</text></svg>')}`];
      }
      
      renderedDocuments[fileId] = pages;
      return pages;
    } catch (error) {
      console.error('Error rendering document:', error);
      return [`data:image/svg+xml;base64,${btoa('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="800" viewBox="0 0 600 800"><rect width="600" height="800" fill="#f3f4f6"/><text x="300" y="400" text-anchor="middle" font-family="Arial" font-size="16" fill="#6b7280">Render Error</text></svg>')}`];
    }
  }
  
  // OCR and Data Extraction Functions
  async function performOCROnImage(imageSource, fileName) {
    try {
      const result = await Tesseract.recognize(imageSource, 'eng', {
        logger: m => {
          if (m.status === 'recognizing text') {
            const progress = Math.round(m.progress * 100);
            updateCurrentFileProcessing(`${fileName} - OCR: ${progress}%`);
          }
        }
      });
      
      return {
        text: result.data.text,
        confidence: result.data.confidence
      };
    } catch (error) {
      console.error('OCR Error:', error);
      return {
        text: '',
        confidence: 0
      };
    }
  }
  
  function extractSurveyPlanData(ocrText, fileName) {
    const data = {
      originalFileName: fileName,
      fileNo: null,
      fileNoFound: false,
      applicantName: null,
      applicantNameFound: false,
      approvedPlanNo: null,
      approvedPlanNoFound: false,
      startingBeaconNo: null,
      startingBeaconNoFound: false,
      area: null,
      areaFound: false,
      location: null,
      locationFound: false,
      beaconCoordinates: [],
      beaconCoordinatesFound: false,
      courses: [],
      coursesFound: false,
      extractedText: ocrText,
      confidence: 0,
      extractionStatus: 'No Data Found',
      quality: 'Poor'
    };
  
    // Clean up OCR text for better pattern matching
    const cleanText = ocrText.replace(/\s+/g, ' ').trim();
  
    // Extract File Number patterns - Enhanced for Nigerian formats with OCR errors
    const fileNoPatterns = [
      // Handle OCR errors like "LKNICONICOM" -> "LKN/CON/COM"
      /\b(LKNICONICOM\/\d{4}\/\d+)\b/i,
      /\b(LKN[\/\s\-]?CON[\/\s\-]?COM[\/\s\-]?\d{4}[\/\s\-]?\d+)\b/i,
      // Full LKN format: LKN/CON/COM/2021/672
      /\b(LKN\/[A-Z]+\/[A-Z]+\/\d{4}\/\d+)\b/i,
      // Partial format: CON/COM/2021/672
      /\b([A-Z]{2,4}\/[A-Z]{2,4}\/\d{4}\/\d+)\b/i,
      // With spaces or dashes
      /\b(LKN[\/\s\-][A-Z]+[\/\s\-][A-Z]+[\/\s\-]\d{4}[\/\s\-]\d+)\b/i,
      // General file number patterns
      /(?:FILE\s*NO\.?\s*:?\s*)([A-Z0-9\/\-\s]+)/i,
      /(?:PLAN\s*NO\.?\s*:?\s*)([A-Z0-9\/\-\s]+)/i,
      // Plan No at end of text
      /Plan\s*No\s*[)]\s*([A-Z0-9\/\-]+)/i
    ];
  
    for (const pattern of fileNoPatterns) {
      const match = cleanText.match(pattern);
      if (match) {
        let fileNo = match[1].trim();
        // Fix OCR errors
        fileNo = fileNo.replace(/LKNICONICOM/i, 'LKN/CON/COM');
        fileNo = fileNo.replace(/([A-Z]+)([A-Z]+)([A-Z]+)(\d{4})(\d+)/i, '$1/$2/$3/$4/$5');
        fileNo = fileNo.replace(/\s+/g, '/');
        data.fileNo = fileNo;
        data.fileNoFound = true;
        break;
      }
    }
  
    // Extract Applicant Name patterns - Enhanced for "LAND GRANTED TO" format
    const applicantPatterns = [
      // Nigerian format: "LAND GRANTED TO LAWAN MUHAMMAD SHAKKA"
      /LAND\s+GRANTED\s+TO\s+([A-Z\s\.]+?)(?:\s+AT\s+|\s+$|\n)/i,
      // Alternative formats
      /(?:APPLICANT\s*:?\s*)([A-Z\s\.]+?)(?:\s+AT\s+|\n|$)/i,
      /(?:GRANTEE\s*:?\s*)([A-Z\s\.]+?)(?:\s+AT\s+|\n|$)/i,
      /(?:NAME\s*:?\s*)([A-Z\s\.]+?)(?:\s+AT\s+|\n|$)/i,
      /(?:OWNER\s*:?\s*)([A-Z\s\.]+?)(?:\s+AT\s+|\n|$)/i,
      // Title patterns
      /(?:MR\.?\s*|MRS\.?\s*|DR\.?\s*|PROF\.?\s*|ALHAJI\s*|HAJIYA\s*)([A-Z\s\.]+?)(?:\s+AT\s+|\n|$)/i
    ];
  
    for (const pattern of applicantPatterns) {
      const match = cleanText.match(pattern);
      if (match) {
        data.applicantName = match[1].trim().replace(/\s+/g, ' ');
        data.applicantNameFound = true;
        break;
      }
    }
  
    // Extract Location information
    const locationPatterns = [
      // Nigerian format: "AT KABI VILLAGE DAWAKIN TOFA DISTRICT"
      /AT\s+([A-Z\s]+VILLAGE[A-Z\s]+DISTRICT[A-Z\s]*)/i,
      /AT\s+([A-Z\s]+WARD[A-Z\s]+)/i,
      /AT\s+([A-Z\s]+AREA[A-Z\s]+)/i,
      /(?:LOCATION\s*:?\s*)([A-Z\s,]+)/i
    ];
  
    for (const pattern of locationPatterns) {
      const match = cleanText.match(pattern);
      if (match) {
        data.location = match[1].trim().replace(/\s+/g, ' ');
        data.locationFound = true;
        break;
      }
    }
  
    // Extract Area information
    const areaPatterns = [
      /AREA\s*[=:]\s*(\d+\.?\d*\s*ha)/i,
      /(\d+\.?\d*)\s*ha/i,
      /(\d+\.?\d*)\s*hectares?/i,
      /AREA\s*[=:]\s*(\d+\.?\d*)/i
    ];
  
    for (const pattern of areaPatterns) {
      const match = cleanText.match(pattern);
      if (match) {
        data.area = match[1].trim();
        data.areaFound = true;
        break;
      }
    }
  
    // Extract Approved Plan Number patterns
    const planNoPatterns = [
      /(?:APPROVED\s*PLAN\s*NO\.?\s*:?\s*)([A-Z0-9\/\-]+)/i,
      /(?:PLAN\s*NO\.?\s*:?\s*)([A-Z0-9\/\-]+)/i,
      /(?:APP\.?\s*NO\.?\s*:?\s*)([A-Z0-9\/\-]+)/i,
      /(?:APPROVAL\s*NO\.?\s*:?\s*)([A-Z0-9\/\-]+)/i,
      // SUB-DIVISION pattern
      /SUB\s*-?\s*DIVISION\s+OF\s+([A-Z0-9\/\-]+)/i
    ];
  
    for (const pattern of planNoPatterns) {
      const match = cleanText.match(pattern);
      if (match) {
        data.approvedPlanNo = match[1].trim();
        data.approvedPlanNoFound = true;
        break;
      }
    }
  
    // Extract Starting Beacon Number
    const beaconPatterns = [
      /(?:STARTING\s*BEACON\s*:?\s*)([A-Z0-9]+)/i,
      /(?:START\s*BEACON\s*:?\s*)([A-Z0-9]+)/i,
      /(?:BEACON\s*:?\s*)([A-Z0-9]+)/i,
      /\b([A-Z]\d+)\b/i,
      // UTM coordinates pattern to extract beacon
      /UTM\s+Co-ordinates\s+of\s+([A-Z0-9]+)/i
    ];
  
    for (const pattern of beaconPatterns) {
      const match = cleanText.match(pattern);
      if (match) {
        data.startingBeaconNo = match[1].trim();
        data.startingBeaconNoFound = true;
        break;
      }
    }
  
    // Extract Beacon Coordinates - Enhanced for UTM format
    const coordinatePatterns = [
      // UTM format: X = 1337 267.420m, Y = 437 052.310m
      /X\s*=\s*(\d+\s+\d+\.?\d*)\s*m.*?[Yv]\s*=\s*(\d+\s+\d+\.?\d*)\s*m/gi,
      // Standard format
      /([A-Z]\d+)\s*[:\-]?\s*X?\s*[=:]?\s*(\d+\.?\d*)\s*[,\s]\s*Y?\s*[=:]?\s*(\d+\.?\d*)/gi,
      /BEACON\s*([A-Z0-9]+)\s*[:\-]?\s*(\d+\.?\d*)\s*[,\s]\s*(\d+\.?\d*)/gi,
      /([A-Z0-9]+)\s*[:\-]?\s*E\s*(\d+\.?\d*)\s*N\s*(\d+\.?\d*)/gi
    ];
  
    for (const pattern of coordinatePatterns) {
      let match;
      while ((match = pattern.exec(cleanText)) !== null) {
        if (match.length >= 3) {
          // Check if this is UTM format with beacon name from previous extraction
          if (match[1].includes(' ') && match[2].includes(' ')) {
            // UTM format: X = 1337 267.420m, Y = 437 052.310m
            data.beaconCoordinates.push({
              beaconNo: data.startingBeaconNo || 'A4929J',
              x: match[1].replace(/\s+/g, ''),
              y: match[2].replace(/\s+/g, ''),
              zone: '32N',
              origin: 'UTM (MINNA DATUM)'
            });
          } else {
            // Standard format
            data.beaconCoordinates.push({
              beaconNo: match[1],
              x: match[2],
              y: match[3],
              zone: '32N',
              origin: 'K2'
            });
          }
          data.beaconCoordinatesFound = true;
        }
      }
    }
  
    // Extract Survey Courses - Enhanced for bearing/distance format
    const coursePatterns = [
      // Format: Ad917J - AAS1E] = 20.55m @ 152° 33
      /([A-Z0-9]+)\s*-\s*([A-Z0-9]+)[^\d]*=\s*(\d+\.?\d*)\s*m\s*@\s*(\d+)°\s*(\d+)/gi,
      // Standard bearing format
      /([A-Z]\d+)\s*TO\s*([A-Z]\d+)\s*[:\-]?\s*([NS]\d+[°]\d+[']\d+["]?[EW])\s*(\d+\.?\d*m?)/gi,
      /BEARING\s*([NS]\d+[°]\d+[']\d+["]?[EW])\s*DISTANCE\s*(\d+\.?\d*m?)/gi,
      /([NS]\d+[°]\d+[']\d+["]?[EW])\s*(\d+\.?\d*m?)/gi
    ];
  
    let courseIndex = 0;
    for (const pattern of coursePatterns) {
      let match;
      while ((match = pattern.exec(cleanText)) !== null) {
        if (match.length >= 5) {
          // Format: Ad917J - AAS1E] = 20.55m @ 152° 33
          data.courses.push({
            id: `course-${Date.now()}-${courseIndex++}`,
            type: 'DD',
            fromBeacon: match[1],
            toBeacon: match[2],
            direction: `${match[4]}° ${match[5]}'`,
            distance: `${match[3]}m`
          });
        } else if (match.length >= 4) {
          // Direction-Distance format with beacons
          data.courses.push({
            id: `course-${Date.now()}-${courseIndex++}`,
            type: 'DD',
            fromBeacon: match[1],
            toBeacon: match[2],
            direction: match[3],
            distance: match[4]
          });
        } else if (match.length >= 2) {
          // Simple direction-distance format
          data.courses.push({
            id: `course-${Date.now()}-${courseIndex++}`,
            type: 'DD',
            fromBeacon: '',
            toBeacon: '',
            direction: match[1],
            distance: match[2]
          });
        }
        data.coursesFound = true;
      }
    }
  
    // Calculate overall confidence and status
    let foundFields = 0;
    let totalFields = 7; // fileNo, applicant, planNo, beacon, coordinates, courses, area
  
    if (data.fileNoFound) foundFields++;
    if (data.applicantNameFound) foundFields++;
    if (data.approvedPlanNoFound) foundFields++;
    if (data.startingBeaconNoFound) foundFields++;
    if (data.beaconCoordinatesFound) foundFields++;
    if (data.coursesFound) foundFields++;
    if (data.areaFound) foundFields++;
  
    data.confidence = Math.round((foundFields / totalFields) * 100);
  
    if (data.confidence >= 80) {
      data.extractionStatus = 'Fully Extracted';
      data.quality = 'Excellent';
    } else if (data.confidence >= 60) {
      data.extractionStatus = 'Mostly Extracted';
      data.quality = 'Good';
    } else if (data.confidence >= 40) {
      data.extractionStatus = 'Partially Extracted';
      data.quality = 'Fair';
    } else if (data.confidence >= 20) {
      data.extractionStatus = 'Minimal Extraction';
      data.quality = 'Poor';
    } else {
      data.extractionStatus = 'No Data Found';
      data.quality = 'Very Poor';
    }
  
    return data;
  }
  
  function updateCurrentFileProcessing(message) {
    const element = document.getElementById('current-file-processing');
    if (element) {
      element.textContent = `Processing: ${message}`;
      element.classList.remove('hidden');
    }
  }
  
  function switchTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.tab-trigger').forEach(btn => {
      btn.classList.remove('active');
      btn.classList.add('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
      btn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    });
    
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.remove('active');
    });
    
    // Activate selected tab
    const tabBtn = document.getElementById(`tab-${tab}`);
    const tabContent = document.getElementById(`content-${tab}`);
    
    if (tabBtn && tabContent) {
      tabBtn.classList.add('active');
      tabBtn.classList.remove('bg-transparent', 'text-gray-600', 'hover:bg-gray-50');
      tabBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
      
      tabContent.classList.add('active');
    }
  }
  
  function updateUI() {
    // Update stats
    const selectedCountEl = document.getElementById('selected-count');
    const processedCountEl = document.getElementById('processed-count');
    
    if (selectedCountEl) selectedCountEl.textContent = selectedFiles.length;
    if (processedCountEl) processedCountEl.textContent = Object.keys(extractedMetadata).length;
    
    // Update AI status
    updateAIStatus();
    
    // Update file list
    updateFilesList();
    
    // Update upload area visibility
    updateUploadArea();
    
    // Update action buttons
    updateActionButtons();
    
    // Update extracted data tab
    updateExtractedDataTab();
    
    // Update tab states
    updateTabStates();
  }
  
  function updateAIStatus() {
    const statusText = document.getElementById('ai-status-text');
    const statusBadge = document.getElementById('ai-status-badge');
    
    if (!statusText || !statusBadge) return;
    
    if (aiStage === 'idle') {
      statusText.textContent = 'Ready';
      statusBadge.textContent = 'Idle';
      statusBadge.className = 'badge badge-default ml-2';
    } else if (aiStage === 'complete') {
      statusText.textContent = Object.keys(extractedMetadata).length > 0 ? 'Complete' : 'Ready';
      statusBadge.textContent = Object.keys(extractedMetadata).length > 0 ? 'Done' : 'Idle';
      statusBadge.className = Object.keys(extractedMetadata).length > 0 ? 'badge badge-success ml-2' : 'badge badge-default ml-2';
    } else {
      statusText.textContent = 'Processing...';
      statusBadge.textContent = 'Active';
      statusBadge.className = 'badge ml-2 bg-blue-500 text-white animate-pulse';
    }
  }
  
  function updateFilesList() {
    const selectedFilesDiv = document.getElementById('selected-files');
    const filesCountSpan = document.getElementById('files-count');
    const filesList = document.getElementById('files-list');
    
    if (!selectedFilesDiv || !filesCountSpan || !filesList) return;
    
    if (selectedFiles.length === 0) {
      selectedFilesDiv.classList.add('hidden');
      return;
    }
    
    selectedFilesDiv.classList.remove('hidden');
    filesCountSpan.textContent = `${selectedFiles.length} survey plan(s) selected`;
    
    filesList.innerHTML = selectedFiles.map((file, index) => `
      <div class="flex items-center justify-between p-3">
        <div class="flex items-center gap-3">
          ${getFileIcon(file.type)}
          <div>
            <p class="font-medium">${file.name}</p>
            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
          </div>
        </div>
        <button onclick="removeFile(${index})" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-2 py-2 transition-all cursor-pointer bg-transparent text-gray-700 hover:bg-gray-100 h-8 w-8 p-0" ${uploadStatus === 'uploading' || (aiStage !== 'idle' && aiStage !== 'complete') ? 'disabled' : ''}>
          <i data-lucide="x" class="h-4 w-4"></i>
        </button>
      </div>
    `).join('');
    
    // Re-initialize Lucide icons
    lucide.createIcons();
  }
  
  function updateUploadArea() {
    const uploadArea = document.getElementById('upload-area');
    if (!uploadArea) return;
    
    if (selectedFiles.length > 0) {
      uploadArea.classList.add('hidden');
    } else {
      uploadArea.classList.remove('hidden');
    }
  }
  
  function updateActionButtons() {
    const startBtn = document.getElementById('start-upload-btn');
    const cancelBtn = document.getElementById('cancel-upload-btn');
    const newBatchBtn = document.getElementById('new-batch-btn');
    
    if (!startBtn || !cancelBtn || !newBatchBtn) return;
    
    // Hide all buttons first
    startBtn.classList.add('hidden');
    cancelBtn.classList.add('hidden');
    newBatchBtn.classList.add('hidden');
    
    if (uploadStatus === 'idle' && selectedFiles.length > 0 && aiStage === 'idle') {
      startBtn.classList.remove('hidden');
    } else if (uploadStatus === 'uploading' || (aiStage !== 'idle' && aiStage !== 'complete')) {
      cancelBtn.classList.remove('hidden');
    } else if (aiStage === 'complete') {
      newBatchBtn.classList.remove('hidden');
    }
  }
  
  function updateExtractedDataTab() {
    const tabBtn = document.getElementById('tab-extracted');
    if (!tabBtn) return;
    
    if (Object.keys(extractedMetadata).length === 0 && aiStage !== 'complete') {
      tabBtn.disabled = true;
      tabBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
      tabBtn.disabled = false;
      tabBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    
    // Update extracted data content
    updateExtractedDataContent();
  }
  
  function updateExtractedDataContent() {
    const aiProcessingExtracted = document.getElementById('ai-processing-extracted');
    const extractedResults = document.getElementById('extracted-results');
    const noDataState = document.getElementById('no-data-state');
    const extractedDataList = document.getElementById('extracted-data-list');
    
    if (!aiProcessingExtracted || !extractedResults || !noDataState || !extractedDataList) return;
    
    if (aiStage !== 'idle' && aiStage !== 'complete') {
      // Show AI processing
      aiProcessingExtracted.classList.remove('hidden');
      extractedResults.classList.add('hidden');
      noDataState.classList.add('hidden');
    } else if (aiStage === 'complete' && Object.keys(extractedMetadata).length > 0) {
      // Show extracted results
      aiProcessingExtracted.classList.add('hidden');
      extractedResults.classList.remove('hidden');
      noDataState.classList.add('hidden');
      
      // Populate extracted data
      extractedDataList.innerHTML = Object.entries(extractedMetadata).map(([fileId, data]) => 
        createExtractedDataCard(fileId, data)
      ).join('');
      
      // Re-initialize Lucide icons
      lucide.createIcons();
    } else {
      // Show no data state
      aiProcessingExtracted.classList.add('hidden');
      extractedResults.classList.add('hidden');
      noDataState.classList.remove('hidden');
    }
  }
  
  function updateTabStates() {
    // Auto-switch to extracted data tab when processing is complete
    if (aiStage === 'complete' && Object.keys(extractedMetadata).length > 0) {
      switchTab('extracted');
    }
  }
  
  function getFileIcon(fileType) {
    if (fileType && fileType.includes('pdf')) {
      return '<i data-lucide="file-text" class="h-8 w-8 text-red-500"></i>';
    } else if (fileType && fileType.includes('image')) {
      return '<i data-lucide="image" class="h-8 w-8 text-purple-500"></i>';
    } else {
      return '<i data-lucide="file" class="h-8 w-8 text-gray-500"></i>';
    }
  }
  
  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }
  
  function startUpload() {
    if (selectedFiles.length === 0) {
      showToast('Please select files to upload', 'error');
      return;
    }
    
    uploadStatus = 'uploading';
    uploadProgress = 0;
    aiStage = 'idle';
    aiProgress = 0;
    processingCancelled = false;
    
    updateUI();
    showUploadProgress();
    
    // Simulate upload progress
    const uploadInterval = setInterval(() => {
      uploadProgress += 20;
      updateUploadProgress();
      
      if (uploadProgress >= 100) {
        clearInterval(uploadInterval);
        uploadStatus = 'complete';
        hideUploadProgress();
        setTimeout(() => startAIProcessing(), 300);
      }
    }, 100);
  }
  
  function cancelUpload() {
    processingCancelled = true;
    uploadStatus = 'idle';
    uploadProgress = 0;
    aiStage = 'idle';
    aiProgress = 0;
    hideUploadProgress();
    hideAIProcessing();
    updateUI();
    showToast('Processing cancelled', 'info');
  }
  
  function startNewBatch() {
    clearAllFiles();
    showToast('Ready for new batch', 'success');
  }
  
  function showUploadProgress() {
    const uploadProgress = document.getElementById('upload-progress');
    if (uploadProgress) {
      uploadProgress.classList.remove('hidden');
    }
  }
  
  function hideUploadProgress() {
    const uploadProgress = document.getElementById('upload-progress');
    if (uploadProgress) {
      uploadProgress.classList.add('hidden');
    }
  }
  
  function updateUploadProgress() {
    const uploadPercentage = document.getElementById('upload-percentage');
    const uploadProgressBar = document.getElementById('upload-progress-bar');
    
    if (uploadPercentage) {
      uploadPercentage.textContent = `${uploadProgress}%`;
    }
    if (uploadProgressBar) {
      uploadProgressBar.style.width = `${uploadProgress}%`;
    }
  }
  
  async function startAIProcessing() {
    if (selectedFiles.length === 0 || processingCancelled) {
      showToast('No files to process', 'info');
      return;
    }
    
    aiStage = 'initializing';
    aiProgress = 5;
    
    showAIProcessing();
    updateAIProcessingUI();
    
    try {
      // Initialize stage
      await new Promise(resolve => setTimeout(resolve, 1000));
      if (processingCancelled) return;
      
      // OCR Stage
      aiStage = 'ocr';
      aiProgress = 20;
      updateAIProcessingUI();
      
      // Process each file with real OCR
      for (let index = 0; index < selectedFiles.length; index++) {
        if (processingCancelled) return;
        
        const file = selectedFiles[index];
        const fileId = `SURVEYPLAN-${Date.now()}-${index}`;
        
        updateCurrentFileProcessing(`${file.name} - Preparing...`);
        
        // Render document pages for OCR
        let pages;
        if (file.type === 'application/pdf') {
          pages = await renderPDFPages(file);
        } else if (file.type.startsWith('image/')) {
          pages = await renderImageFile(file);
        } else {
          continue; // Skip unsupported files
        }
        
        // Perform OCR on each page
        let combinedText = '';
        let totalConfidence = 0;
        
        for (let pageIndex = 0; pageIndex < pages.length; pageIndex++) {
          if (processingCancelled) return;
          
          updateCurrentFileProcessing(`${file.name} - Page ${pageIndex + 1}/${pages.length}`);
          
          const ocrResult = await performOCROnImage(pages[pageIndex], file.name);
          combinedText += ocrResult.text + '\n';
          totalConfidence += ocrResult.confidence;
        }
        
        const averageConfidence = pages.length > 0 ? totalConfidence / pages.length : 0;
        
        // Store raw OCR text
        rawOcrText[fileId] = combinedText;
        
        // Layout Analysis Stage
        aiStage = 'layoutAnalysis';
        aiProgress = 40 + (index / selectedFiles.length) * 20;
        updateAIProcessingUI();
        updateCurrentFileProcessing(`${file.name} - Analyzing layout...`);
        
        await new Promise(resolve => setTimeout(resolve, 500));
        if (processingCancelled) return;
        
        // Data Extraction Stage
        aiStage = 'dataExtraction';
        aiProgress = 60 + (index / selectedFiles.length) * 20;
        updateAIProcessingUI();
        updateCurrentFileProcessing(`${file.name} - Extracting data...`);
        
        // Extract survey plan data from OCR text
        const extractedData = extractSurveyPlanData(combinedText, file.name);
        extractedData.originalFileIndex = index;
        extractedData.fileSize = formatFileSize(file.size);
        extractedData.fileType = file.type;
        extractedData.pageCount = pages.length;
        extractedData.confidence = Math.round((extractedData.confidence + averageConfidence) / 2);
        
        // Store extracted metadata
        extractedMetadata[fileId] = extractedData;
        
        // Pre-render the document for faster preview
        renderedDocuments[fileId] = pages;
        
        await new Promise(resolve => setTimeout(resolve, 500));
        if (processingCancelled) return;
      }
      
      // Data Assembly Stage
      aiStage = 'dataAssembly';
      aiProgress = 90;
      updateAIProcessingUI();
      updateCurrentFileProcessing('Finalizing extraction results...');
      
      await new Promise(resolve => setTimeout(resolve, 1000));
      if (processingCancelled) return;
      
      // Complete
      aiStage = 'complete';
      aiProgress = 100;
      updateAIProcessingUI();
      
      // Hide current file processing indicator
      const element = document.getElementById('current-file-processing');
      if (element) {
        element.classList.add('hidden');
      }
      
      updateUI();
      showToast(`Successfully processed ${selectedFiles.length} survey plan(s)!`, 'success');
      
    } catch (error) {
      console.error('Error during AI processing:', error);
      showToast('Error occurred during processing', 'error');
      aiStage = 'idle';
      updateUI();
    }
  }
  
  function showAIProcessing() {
    const aiProcessing = document.getElementById('ai-processing');
    if (aiProcessing) {
      aiProcessing.classList.remove('hidden');
    }
  }
  
  function hideAIProcessing() {
    const aiProcessing = document.getElementById('ai-processing');
    if (aiProcessing) {
      aiProcessing.classList.add('hidden');
    }
  }
  
  function updateAIProcessingUI() {
    // Update progress bar
    const aiProgressText = document.getElementById('ai-progress-text');
    const aiProgressBar = document.getElementById('ai-progress-bar');
    
    if (aiProgressText) {
      aiProgressText.textContent = `${aiProgress}% Complete`;
    }
    if (aiProgressBar) {
      aiProgressBar.style.width = `${aiProgress}%`;
    }
    
    // Update stage indicators
    const stages = ['initializing', 'ocr', 'layoutAnalysis', 'dataExtraction', 'dataAssembly', 'complete'];
    const currentStageIndex = stages.indexOf(aiStage);
    
    document.querySelectorAll('.stage-indicator').forEach((indicator, index) => {
      const circle = indicator.querySelector('.w-4');
      const text = indicator.querySelector('.text-xs');
      
      if (!circle || !text) return;
      
      if (index < currentStageIndex) {
        // Completed stage
        circle.className = 'w-4 h-4 rounded-full bg-blue-500 mb-1';
        text.className = 'text-xs font-medium text-blue-600';
      } else if (index === currentStageIndex) {
        // Current stage
        circle.className = 'w-4 h-4 rounded-full bg-blue-500 ring-4 ring-blue-100 animate-pulse mb-1';
        text.className = 'text-xs font-bold text-blue-700';
      } else {
        // Future stage
        circle.className = 'w-4 h-4 rounded-full bg-gray-300 mb-1';
        text.className = 'text-xs text-gray-500';
      }
    });
    
    // Update stage description
    updateStageDescription();
    updateUI();
  }
  
  function updateStageDescription() {
    const stageTitle = document.getElementById('ai-stage-title');
    const stageDescription = document.getElementById('ai-stage-description');
    const stageIcon = document.getElementById('ai-stage-icon');
    
    if (!stageTitle || !stageDescription || !stageIcon) return;
    
    const stageInfo = {
      'initializing': {
        title: 'Initializing',
        description: 'Initializing OCR engine and preparing for survey plan analysis...',
        icon: 'brain'
      },
      'ocr': {
        title: 'OCR Processing',
        description: 'Performing Optical Character Recognition on document pages to extract text...',
        icon: 'file-digit'
      },
      'layoutAnalysis': {
        title: 'Layout Analysis',
        description: 'Analyzing document structure and identifying data field locations...',
        icon: 'file-search'
      },
      'dataExtraction': {
        title: 'Data Extraction',
        description: 'Extracting survey details: File No, Applicant, Plan No, Beacons, Courses...',
        icon: 'layers'
      },
      'dataAssembly': {
        title: 'Data Assembly',
        description: 'Assembling and structuring extracted information for review...',
        icon: 'zap'
      },
      'complete': {
        title: 'Complete',
        description: 'Survey plan analysis complete! Data is ready for review and editing.',
        icon: 'sparkles'
      }
    };
    
    const info = stageInfo[aiStage] || stageInfo['initializing'];
    
    stageTitle.textContent = `Current Stage: ${info.title}`;
    stageDescription.textContent = info.description;
    stageIcon.setAttribute('data-lucide', info.icon);
    
    // Re-initialize Lucide icons
    lucide.createIcons();
  }
  
  function createExtractedDataCard(fileId, data) {
    const confidenceClass = data.confidence > 75 ? 'badge-success' : 
                           data.confidence > 40 ? 'badge-warning' : 'badge-error';
    
    return `
      <div class="border rounded-lg p-4 shadow-sm">
        <div class="flex justify-between items-start mb-3">
          <div>
            <h4 class="font-semibold text-lg">${data.originalFileName}</h4>
            <div class="flex gap-2 mt-1">
              <span class="badge ${confidenceClass}">Confidence: ${data.confidence}%</span>
              <span class="badge badge-default">${data.extractionStatus}</span>
            </div>
          </div>
          <div class="flex gap-2 flex-wrap">
            <button onclick="showDocumentPreview('${fileId}')" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-1">
              <i data-lucide="eye" class="h-4 w-4"></i>
              Preview
            </button>
            <button onclick="editMetadata('${fileId}')" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-1">
              <i data-lucide="edit" class="h-4 w-4"></i>
              Edit
            </button>
            <button onclick="exportCogo('${fileId}')" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1 transition-all cursor-pointer bg-gray-200 text-gray-700 hover:bg-gray-300 gap-1">
              <i data-lucide="share" class="h-4 w-4"></i>
              Export COGO
            </button>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div><strong>File No:</strong> ${data.fileNo || '<span class="text-red-500">Not found</span>'}</div>
          <div><strong>Applicant:</strong> ${data.applicantName || '<span class="text-red-500">Not found</span>'}</div>
          <div><strong>Approved Plan No:</strong> ${data.approvedPlanNo || '<span class="text-red-500">Not found</span>'}</div>
          <div><strong>Start Beacon:</strong> ${data.startingBeaconNo || '<span class="text-red-500">Not found</span>'}</div>
          ${data.area ? `<div><strong>Area:</strong> ${data.area}</div>` : ''}
          ${data.location ? `<div><strong>Location:</strong> ${data.location}</div>` : ''}
        </div>
        
        ${data.beaconCoordinates.length > 0 ? `
          <div class="mt-3">
            <h5 class="font-medium text-sm mb-1">Beacon Coordinates (${data.beaconCoordinates.length}):</h5>
            <ul class="list-disc list-inside pl-1 text-xs space-y-0.5">
              ${data.beaconCoordinates.slice(0, 3).map(bc => `
                <li>${bc.beaconNo}: X=${bc.x}, Y=${bc.y}</li>
              `).join('')}
              ${data.beaconCoordinates.length > 3 ? `<li class="text-gray-500">... and ${data.beaconCoordinates.length - 3} more</li>` : ''}
            </ul>
          </div>
        ` : ''}
        
        ${data.courses && data.courses.length > 0 ? `
          <div class="mt-3">
            <h5 class="font-medium text-sm mb-1">Survey Courses (${data.courses.length}):</h5>
            <div class="space-y-2">
              ${data.courses.slice(0, 2).map((course, idx) => `
                <div class="p-2 border rounded-md text-xs bg-gray-50">
                  <p class="font-semibold">Course #${idx + 1} (${course.type})</p>
                  ${course.type === 'DD' ? `
                    <p>From: ${course.fromBeacon || 'N/A'} ${course.toBeacon ? ` to ${course.toBeacon}` : ''} | Dir: ${course.direction}, Dist: ${course.distance}</p>
                  ` : ''}
                  ${course.type === 'AD' ? `
                    <p>Angle: ${course.angle}, Dist: ${course.distance}</p>
                  ` : ''}
                </div>
              `).join('')}
              ${data.courses.length > 2 ? `<p class="text-xs text-gray-500">... and ${data.courses.length - 2} more courses</p>` : ''}
            </div>
          </div>
        ` : ''}
        
        <div class="mt-3 text-xs text-gray-500">
          Quality: ${data.quality} | Pages: ${data.pageCount} | Size: ${data.fileSize}
        </div>
      </div>
    `;
  }
  
  // Modal functions
  async function showDocumentPreview(fileId) {
    currentPreviewFile = fileId;
    const data = extractedMetadata[fileId];
    
    if (!data) {
      showToast('Metadata not found for preview', 'error');
      return;
    }
    
    // Show preview modal
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
      previewModal.classList.remove('hidden');
    }
    
    // Show loading state
    const previewContent = document.getElementById('preview-content');
    if (previewContent) {
      previewContent.innerHTML = `
        <div class="flex items-center justify-center py-8">
          <div class="loading-spinner mr-3"></div>
          <span>Rendering document...</span>
        </div>
      `;
    }
    
    try {
      // Render the document
      const pages = await ensureDocumentRendered(fileId);
      
      // Generate preview content
      if (previewContent) {
        previewContent.innerHTML = `
          <div class="space-y-6">
            <div class="text-sm text-gray-500 text-center">
              Showing ${pages.length} page(s) from ${data.originalFileName}
            </div>
            ${pages.map((pageImage, index) => `
              <div class="border rounded-lg overflow-hidden bg-white shadow-sm">
                <div class="bg-gray-50 px-4 py-2 border-b">
                  <span class="text-sm font-medium text-gray-700">Page ${index + 1}</span>
                </div>
                <div class="p-4">
                  <img
                    src="${pageImage}"
                    alt="Page ${index + 1}"
                    class="w-full object-contain border shadow-sm rounded"
                    style="max-height: 600px"
                    onerror="this.src='data:image/svg+xml;base64,${btoa('<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"600\" height=\"800\" viewBox=\"0 0 600 800\"><rect width=\"600\" height=\"800\" fill=\"#f3f4f6\"/><text x=\"300\" y=\"400\" text-anchor=\"middle\" font-family=\"Arial\" font-size=\"16\" fill=\"#6b7280\">Image Load Error</text></svg>')}'"
                  />
                </div>
              </div>
            `).join('')}
          </div>
        `;
      }
    } catch (error) {
      console.error('Error showing document preview:', error);
      if (previewContent) {
        previewContent.innerHTML = `
          <div class="text-center py-8 text-red-600">
            <i data-lucide="alert-circle" class="h-8 w-8 mx-auto mb-2"></i>
            <p>Error loading document preview</p>
          </div>
        `;
        lucide.createIcons();
      }
    }
    
    // Set raw OCR text
    const rawText = document.getElementById('raw-text');
    if (rawText) {
      rawText.textContent = rawOcrText[fileId] || 'No OCR text available.';
    }
  }
  
  function closePreviewModal() {
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
      previewModal.classList.add('hidden');
    }
    currentPreviewFile = null;
  }
  
  function toggleRawText() {
    const content = document.getElementById('raw-text-content');
    const icon = document.querySelector('#toggle-raw-text i');
    
    if (!content || !icon) return;
    
    if (content.classList.contains('expanded')) {
      content.classList.remove('expanded');
      icon.setAttribute('data-lucide', 'chevron-down');
    } else {
      content.classList.add('expanded');
      icon.setAttribute('data-lucide', 'chevron-up');
    }
    
    lucide.createIcons();
  }
  
  async function editMetadata(fileId) {
    currentEditingFile = fileId;
    const data = extractedMetadata[fileId];
    
    if (!data) {
      showToast('Metadata not found for editing', 'error');
      return;
    }
    
    // Show edit modal
    const editModal = document.getElementById('edit-modal');
    if (editModal) {
      editModal.classList.remove('hidden');
    }
    
    // Populate form fields
    const editFileName = document.getElementById('edit-file-name');
    const editFileNo = document.getElementById('edit-file-no');
    const editApplicantName = document.getElementById('edit-applicant-name');
    const editApprovedPlanNo = document.getElementById('edit-approved-plan-no');
    const editStartingBeaconNo = document.getElementById('edit-starting-beacon-no');
    
    if (editFileName) editFileName.textContent = `File: ${data.originalFileName}`;
    if (editFileNo) editFileNo.value = data.fileNo || '';
    if (editApplicantName) editApplicantName.value = data.applicantName || '';
    if (editApprovedPlanNo) editApprovedPlanNo.value = data.approvedPlanNo || '';
    if (editStartingBeaconNo) editStartingBeaconNo.value = data.startingBeaconNo || '';
    
    // Populate beacon coordinates
    populateBeaconCoordinates(data.beaconCoordinates || []);
    
    // Populate courses
    populateCourses(data.courses || []);
    
    // Show preview with loading state
    await showEditPreview(fileId);
  }
  
  function closeEditModal() {
    const editModal = document.getElementById('edit-modal');
    if (editModal) {
      editModal.classList.add('hidden');
    }
    currentEditingFile = null;
    currentEditPreviewPage = 0;
  }
  
  function saveEditChanges() {
    if (!currentEditingFile) return;
    
    // Update metadata with form values
    const data = extractedMetadata[currentEditingFile];
    const editFileNo = document.getElementById('edit-file-no');
    const editApplicantName = document.getElementById('edit-applicant-name');
    const editApprovedPlanNo = document.getElementById('edit-approved-plan-no');
    const editStartingBeaconNo = document.getElementById('edit-starting-beacon-no');
    
    if (editFileNo) data.fileNo = editFileNo.value;
    if (editApplicantName) data.applicantName = editApplicantName.value;
    if (editApprovedPlanNo) data.approvedPlanNo = editApprovedPlanNo.value;
    if (editStartingBeaconNo) data.startingBeaconNo = editStartingBeaconNo.value;
    
    // Update beacon coordinates and courses from form
    // (Implementation would collect data from dynamic form elements)
    
    showToast('Changes saved locally. Save to DB via main button.', 'success');
    closeEditModal();
    updateUI();
  }
  
  async function showEditPreview(fileId) {
    const previewContent = document.getElementById('edit-preview-content');
    const pageNav = document.getElementById('edit-page-nav');
    
    if (!previewContent || !pageNav) return;
    
    // Show loading state
    previewContent.innerHTML = `
      <div class="flex items-center justify-center h-full">
        <div class="loading-spinner mr-3"></div>
        <span>Loading preview...</span>
      </div>
    `;
    
    try {
      // Render the document
      const pages = await ensureDocumentRendered(fileId);
      
      // Store pages for navigation
      renderedDocuments[fileId] = pages;
      currentEditPreviewPage = 0;
      
      // Show first page
      updateEditPreview(fileId, pages);
      
      // Show/hide page navigation
      if (pages.length > 1) {
        pageNav.classList.remove('hidden');
        updateEditPageNavigation(pages.length);
      } else {
        pageNav.classList.add('hidden');
      }
    } catch (error) {
      console.error('Error showing edit preview:', error);
      previewContent.innerHTML = `
        <div class="flex items-center justify-center h-full text-red-600">
          <div class="text-center">
            <i data-lucide="alert-circle" class="h-8 w-8 mx-auto mb-2"></i>
            <p>Error loading preview</p>
          </div>
        </div>
      `;
      lucide.createIcons();
    }
  }
  
  function updateEditPreview(fileId, pages) {
    const previewContent = document.getElementById('edit-preview-content');
    if (!previewContent || !pages || pages.length === 0) return;
    
    const currentPage = pages[currentEditPreviewPage];
    
    previewContent.innerHTML = `
      <img
        src="${currentPage}"
        alt="Page ${currentEditPreviewPage + 1}"
        class="w-full h-auto object-contain p-2"
        onerror="this.src='data:image/svg+xml;base64,${btoa('<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"400\" height=\"600\" viewBox=\"0 0 400 600\"><rect width=\"400\" height=\"600\" fill=\"#f3f4f6\"/><text x=\"200\" y=\"300\" text-anchor=\"middle\" font-family=\"Arial\" font-size=\"16\" fill=\"#6b7280\">Image Load Error</text></svg>')}'"
      />
    `;
  }
  
  function updateEditPageNavigation(totalPages) {
    const pageInfo = document.getElementById('edit-page-info');
    const prevBtn = document.getElementById('edit-prev-page');
    const nextBtn = document.getElementById('edit-next-page');
    
    if (!pageInfo || !prevBtn || !nextBtn) return;
    
    pageInfo.textContent = `Page ${currentEditPreviewPage + 1} of ${totalPages}`;
    
    prevBtn.disabled = currentEditPreviewPage === 0;
    nextBtn.disabled = currentEditPreviewPage === totalPages - 1;
    
    if (prevBtn.disabled) {
      prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
      prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    
    if (nextBtn.disabled) {
      nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
      nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
  }
  
  function changeEditPreviewPage(direction) {
    if (!currentEditingFile || !renderedDocuments[currentEditingFile]) return;
    
    const pages = renderedDocuments[currentEditingFile];
    const newPage = currentEditPreviewPage + direction;
    
    if (newPage >= 0 && newPage < pages.length) {
      currentEditPreviewPage = newPage;
      updateEditPreview(currentEditingFile, pages);
      updateEditPageNavigation(pages.length);
    }
  }
  
  function populateBeaconCoordinates(coordinates) {
    const container = document.getElementById('beacon-coordinates-list');
    if (!container) return;
    
    container.innerHTML = coordinates.map((coord, index) => `
      <div class="data-card">
        <div class="flex justify-between items-center mb-2">
          <span class="text-sm font-medium">Beacon #${index + 1}</span>
          <button onclick="removeBeaconCoordinate(${index})" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-2 py-1 transition-all cursor-pointer bg-transparent text-red-500 hover:bg-red-50">
            <i data-lucide="x" class="h-4 w-4"></i>
          </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
          <div>
            <label class="text-xs text-gray-700">Beacon No.</label>
            <input type="text" value="${coord.beaconNo}" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
          </div>
          <div>
            <label class="text-xs text-gray-700">X</label>
            <input type="text" value="${coord.x}" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
          </div>
          <div>
            <label class="text-xs text-gray-700">Y</label>
            <input type="text" value="${coord.y}" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div>
            <label class="text-xs text-gray-700">Zone</label>
            <input type="text" value="${coord.zone || ''}" placeholder="e.g., 32N" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
          </div>
          <div>
            <label class="text-xs text-gray-700">Origin</label>
            <input type="text" value="${coord.origin || ''}" placeholder="e.g., K2" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
          </div>
        </div>
      </div>
    `).join('');
    
    lucide.createIcons();
  }
  
  function populateCourses(courses) {
    const container = document.getElementById('courses-list');
    if (!container) return;
    
    container.innerHTML = courses.map((course, index) => `
      <div class="data-card">
        <div class="flex justify-between items-center mb-2">
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium">Course #${index + 1}</span>
            <select onchange="changeCourseType(${index}, this.value)" class="p-1 border rounded text-xs bg-white">
              <option value="DD" ${course.type === 'DD' ? 'selected' : ''}>Direction-Distance</option>
              <option value="AD" ${course.type === 'AD' ? 'selected' : ''}>Angle-Distance</option>
            </select>
          </div>
          <button onclick="removeCourse(${index})" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-2 py-1 transition-all cursor-pointer bg-transparent text-red-500 hover:bg-red-50">
            <i data-lucide="x" class="h-4 w-4"></i>
          </button>
        </div>
        
        ${course.type === 'DD' ? `
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-2">
            <div>
              <label class="text-xs text-gray-700">From Beacon</label>
              <input type="text" value="${course.fromBeacon || ''}" placeholder="e.g., B1" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
            </div>
            <div>
              <label class="text-xs text-gray-700">To Beacon</label>
              <input type="text" value="${course.toBeacon || ''}" placeholder="e.g., B2" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div>
              <label class="text-xs text-gray-700">Direction</label>
              <input type="text" value="${course.direction}" placeholder="e.g., N45-30-15E" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
            </div>
            <div>
              <label class="text-xs text-gray-700">Distance</label>
              <input type="text" value="${course.distance}" placeholder="e.g., 105.50m" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
            </div>
          </div>
        ` : `
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div>
              <label class="text-xs text-gray-700">Angle</label>
              <input type="text" value="${course.angle || ''}" placeholder="e.g., 45-00-00" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
            </div>
            <div>
              <label class="text-xs text-gray-700">Distance</label>
              <input type="text" value="${course.distance}" placeholder="e.g., 100.00m" class="w-full px-2 py-1 border border-gray-300 rounded text-xs" />
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-1">Note: 'From Beacon' for AD courses is implied.</p>
        `}
      </div>
    `).join('');
    
    lucide.createIcons();
  }
  
  function addBeaconCoordinate() {
    if (!currentEditingFile) return;
    
    const data = extractedMetadata[currentEditingFile];
    data.beaconCoordinates.push({ beaconNo: '', x: '', y: '', zone: '', origin: '' });
    populateBeaconCoordinates(data.beaconCoordinates);
  }
  
  function removeBeaconCoordinate(index) {
    if (!currentEditingFile) return;
    
    const data = extractedMetadata[currentEditingFile];
    data.beaconCoordinates.splice(index, 1);
    populateBeaconCoordinates(data.beaconCoordinates);
  }
  
  function addCourse() {
    if (!currentEditingFile) return;
    
    const data = extractedMetadata[currentEditingFile];
    const newCourse = {
      id: `course-${Date.now()}-${Math.random().toString(36).substring(2, 7)}`,
      type: 'DD',
      fromBeacon: '',
      toBeacon: '',
      direction: '',
      distance: ''
    };
    data.courses.push(newCourse);
    populateCourses(data.courses);
  }
  
  function removeCourse(index) {
    if (!currentEditingFile) return;
    
    const data = extractedMetadata[currentEditingFile];
    data.courses.splice(index, 1);
    populateCourses(data.courses);
  }
  
  function changeCourseType(index, newType) {
    if (!currentEditingFile) return;
    
    const data = extractedMetadata[currentEditingFile];
    const course = data.courses[index];
    
    if (newType === 'DD') {
      course.type = 'DD';
      course.fromBeacon = course.fromBeacon || '';
      course.toBeacon = course.toBeacon || '';
      course.direction = course.direction || '';
      delete course.angle;
    } else if (newType === 'AD') {
      course.type = 'AD';
      course.angle = course.angle || '';
      delete course.fromBeacon;
      delete course.toBeacon;
      delete course.direction;
    }
    
    populateCourses(data.courses);
  }
  
  function exportCogo(fileId) {
    const data = extractedMetadata[fileId];
    if (!data) {
      showToast('Metadata not found for COGO export', 'error');
      return;
    }
    
    // Generate COGO format data
    const cogoData = generateCogoData(data);
    
    // Show COGO modal
    const cogoOutput = document.getElementById('cogo-output');
    const cogoModal = document.getElementById('cogo-modal');
    
    if (cogoOutput) cogoOutput.value = cogoData;
    if (cogoModal) cogoModal.classList.remove('hidden');
  }
  
  function closeCogoModal() {
    const cogoModal = document.getElementById('cogo-modal');
    if (cogoModal) {
      cogoModal.classList.add('hidden');
    }
  }
  
  function generateCogoData(data) {
    let cogo = `; COGO Data Export for ${data.originalFileName}\n`;
    cogo += `; File No: ${data.fileNo}\n`;
    cogo += `; Applicant: ${data.applicantName}\n`;
    cogo += `; Generated: ${new Date().toISOString()}\n\n`;
    
    // Add beacon coordinates
    if (data.beaconCoordinates && data.beaconCoordinates.length > 0) {
      cogo += "; Beacon Coordinates\n";
      data.beaconCoordinates.forEach(coord => {
        cogo += `PT ${coord.beaconNo} ${coord.x} ${coord.y}\n`;
      });
      cogo += "\n";
    }
    
    // Add courses
    if (data.courses && data.courses.length > 0) {
      cogo += "; Survey Courses\n";
      data.courses.forEach((course, index) => {
        if (course.type === 'DD') {
          cogo += `; Course ${index + 1}: ${course.fromBeacon} to ${course.toBeacon}\n`;
          cogo += `BD ${course.direction} ${course.distance}\n`;
        } else if (course.type === 'AD') {
          cogo += `; Course ${index + 1}: Angle-Distance\n`;
          cogo += `AD ${course.angle} ${course.distance}\n`;
        }
      });
    }
    
    return cogo;
  }
  
  function copyCogoData() {
    const cogoOutput = document.getElementById('cogo-output');
    if (!cogoOutput) return;
    
    cogoOutput.select();
    try {
      document.execCommand('copy');
      showToast('COGO data copied to clipboard!', 'success');
    } catch (err) {
      // Fallback for modern browsers
      navigator.clipboard.writeText(cogoOutput.value).then(() => {
        showToast('COGO data copied to clipboard!', 'success');
      }).catch(() => {
        showToast('Failed to copy COGO data', 'error');
      });
    }
  }
  
  function downloadCogoFile() {
    const cogoOutput = document.getElementById('cogo-output');
    if (!cogoOutput || !cogoOutput.value) {
      showToast('No COGO data to download', 'error');
      return;
    }
    
    const blob = new Blob([cogoOutput.value], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    
    const fileName = currentEditingFile 
      ? `cogo-data-${extractedMetadata[currentEditingFile]?.originalFileName.split('.')[0] || 'survey'}.txt`
      : 'cogo-data.txt';
    
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    showToast('COGO data downloaded!', 'success');
  }
  
  function saveToDatabase() {
    const entriesCount = Object.keys(extractedMetadata).length;
    if (entriesCount === 0) {
      showToast('No extracted data to save', 'info');
      return;
    }
    
    showLoading('Saving extracted data to cadastral records...');
    
    // Simulate database save
    setTimeout(() => {
      hideLoading();
      showToast(`${entriesCount} record(s) saved to cadastral database!`, 'success');
    }, 2000);
  }
  
  function showLoading(message) {
    const loadingMessage = document.getElementById('loading-message');
    const loadingOverlay = document.getElementById('loading-overlay');
    
    if (loadingMessage) loadingMessage.textContent = message;
    if (loadingOverlay) loadingOverlay.classList.remove('hidden');
  }
  
  function hideLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
      loadingOverlay.classList.add('hidden');
    }
  }
  
  function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toastId = `toast-${Date.now()}`;
    
    const typeClasses = {
      success: 'bg-green-600 text-white',
      error: 'bg-red-600 text-white',
      warning: 'bg-yellow-600 text-white',
      info: 'bg-blue-600 text-white'
    };
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${typeClasses[type]} px-4 py-2 rounded-md shadow-lg flex items-center gap-2 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
      <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : type === 'warning' ? 'alert-triangle' : 'info'}" class="h-4 w-4"></i>
      <span>${message}</span>
      <button onclick="removeToast('${toastId}')" class="ml-2 hover:bg-black/20 rounded p-1">
        <i data-lucide="x" class="h-3 w-3"></i>
      </button>
    `;
    
    toastContainer.appendChild(toast);
    lucide.createIcons();
    
    // Animate in
    setTimeout(() => {
      toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      removeToast(toastId);
    }, 5000);
  }
  
  function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
      toast.classList.add('translate-x-full');
      setTimeout(() => {
        toast.remove();
      }, 300);
    }
  }
  </script>