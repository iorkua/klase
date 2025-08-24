<!-- JavaScript -->
<script>
  // Initialize Lucide icons
  lucide.createIcons();
  
  // State variables
  let selectedFiles = []; // Initialize empty - no pre-selected files
  let selectedIndexedFiles = []; // Track selected indexed files
  let pendingFiles = []; // Will be loaded from API
  let indexedFiles = [
    // Sample indexed files for testing
    {
      id: 'INDEXED-001',
      fileNumber: 'KNGP-12345',
      name: 'Alhaji Ibrahim Dantata Property',
      type: 'Certificate of Occupancy',
      source: 'Indexed',
      date: '2024-01-15',
      landUseType: 'Residential',
      district: 'Nasarawa'
    },
    {
      id: 'INDEXED-002',
      fileNumber: 'KNGP-12346',
      name: 'Hajiya Amina Yusuf Commercial Plot',
      type: 'Site Plan',
      source: 'Indexed',
      date: '2024-01-16',
      landUseType: 'Commercial',
      district: 'Fagge'
    }
  ]; // Sample data for testing
  let indexingProgress = 0; // Set to 0% initially
  let currentStage = "extract"; // Current stage in the AI pipeline
  
  // DOM Elements
  const tabs = document.querySelectorAll('.tab');
  const tabContents = document.querySelectorAll('.tab-content');
  const pendingFilesList = document.getElementById('pending-files-list');
  const selectedFilesCount = document.getElementById('selected-files-count');
  const beginIndexingBtn = document.getElementById('begin-indexing-btn');
  const newFileIndexBtn = document.getElementById('new-file-index-btn');
  const newFileDialogOverlay = document.getElementById('new-file-dialog-overlay');
  const confirmSaveResultsBtn = document.getElementById('confirm-save-results-btn');
  
  // DOM Elements for AI processing
  const startAiIndexingBtn = document.getElementById('start-ai-indexing-btn');
  const aiProcessingView = document.getElementById('ai-processing-view');
  const progressBar = document.getElementById('progress-bar');
  const progressPercentage = document.getElementById('progress-percentage');
  const pipelineProgressBar = document.getElementById('pipeline-progress-bar');
  const pipelineProgressLine = document.getElementById('pipeline-progress-line');
  const pipelinePercentage = document.getElementById('pipeline-percentage');
  const currentStageInfo = document.getElementById('current-stage-info');
  const aiInsightsContainer = document.getElementById('ai-insights-container');
  
  // DOM Elements for New File Dialog
  const closeDialogBtn = document.getElementById('close-dialog-btn');
  const cancelBtn = document.getElementById('cancel-btn');
  const createFileBtn = document.getElementById('create-file-btn');
  const fileNumberTypeRadios = document.querySelectorAll('input[name="file-number-type"]');

  // Function to toggle file selection
  function toggleFileSelection(fileId) {
    if (selectedFiles.includes(fileId)) {
      selectedFiles = selectedFiles.filter(id => id !== fileId);
    } else {
      selectedFiles.push(fileId);
    }
    
    renderPendingFiles();
    updateSelectedFilesCount();
  }
  
  // Make function globally accessible
  window.toggleFileSelection = toggleFileSelection;
  
  // Function to toggle select all
  function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    
    if (selectAllCheckbox.checked) {
      // Select all files
      selectedFiles = pendingFiles.map(file => file.id);
    } else {
      // Deselect all files
      selectedFiles = [];
    }
    
    renderPendingFiles();
    updateSelectedFilesCount();
  }
  
  // Function to start AI indexing
  function startAiIndexing() {
    console.log("Starting AI indexing process...");
    
    // Hide the initial view and show the processing view
    const initialView = document.querySelector('#indexing-tab .card .p-6 .card');
    if (initialView) {
      initialView.parentElement.classList.add('hidden');
    }
    aiProcessingView.classList.remove('hidden');
    
    // Start the indexing simulation
    simulateIndexingProcess();
  }
  
  // Function to simulate the indexing process
  function simulateIndexingProcess() {
    console.log("Starting AI indexing simulation");
    
    let progress = 0;
    const stages = ['init', 'analyze', 'extract', 'categorize', 'validate', 'complete'];
    let currentStageIndex = 0;
    
    // Stage descriptions
    const stageDescriptions = {
      init: "Setting up AI processing environment and preparing documents for analysis...",
      analyze: "Analyzing document structure and identifying key sections...",
      extract: "Extracting key information and metadata using form templates...",
      categorize: "Categorizing extracted information and applying relevant tags...",
      validate: "Validating extracted data against known patterns and rules...",
      complete: "Finalizing results and preparing data for submission to KLAES..."
    };
    
    // Stage icons
    const stageIcons = {
      init: "loader",
      analyze: "search",
      extract: "layers",
      categorize: "tag",
      validate: "check-circle",
      complete: "check-square"
    };
    
    // Update progress every 500ms
    const interval = setInterval(() => {
      progress += 2;
      
      // Update progress bar and percentage
      progressBar.style.width = `${progress}%`;
      progressPercentage.textContent = `${progress}%`;
      pipelineProgressBar.style.width = `${progress}%`;
      pipelineProgressLine.style.width = `${progress}%`;
      pipelinePercentage.textContent = `${progress}% Complete`;
      
      // Update stage if needed
      const stageThresholds = [0, 20, 40, 60, 80, 95];
      if (progress >= stageThresholds[currentStageIndex + 1] && currentStageIndex < stages.length - 1) {
        // Mark current stage as completed
        document.getElementById(`stage-${stages[currentStageIndex]}`).classList.remove('active');
        document.getElementById(`stage-${stages[currentStageIndex]}`).classList.add('completed');
        
        // Move to next stage
        currentStageIndex++;
        
        // Mark new stage as active
        document.getElementById(`stage-${stages[currentStageIndex]}`).classList.remove('pending');
        document.getElementById(`stage-${stages[currentStageIndex]}`).classList.add('active');
        
        // Update current stage info
        currentStageInfo.innerHTML = `
          <div class="p-2 bg-green-100 rounded-full">
            <i data-lucide="${stageIcons[stages[currentStageIndex]]}" class="h-5 w-5 text-green-500"></i>
          </div>
          <div>
            <p class="text-sm font-medium mb-1">Current Stage: ${stages[currentStageIndex].charAt(0).toUpperCase() + stages[currentStageIndex].slice(1)}</p>
            <p class="text-xs text-gray-600">${stageDescriptions[stages[currentStageIndex]]}</p>
          </div>
        `;
        
        // Initialize Lucide icons for the new content
        lucide.createIcons();
        
        // Log progress
        console.log(`AI Integration - Stage ${currentStageIndex + 1}/${stages.length}: ${stages[currentStageIndex]}`);
      }
      
      // Show AI insights at 50% progress
      if (progress === 50) {
        showAiInsights();
      }
      
      // Complete the process
      if (progress >= 100) {
        clearInterval(interval);
        completeIndexingProcess();
      }
    }, 200);
  }
  
  // Function to show AI insights
  function showAiInsights() {
    console.log("Generating AI insights");
    
    aiInsightsContainer.innerHTML = `
      <div class="flex items-center mb-2">
        <i data-lucide="zap" class="h-4 w-4 text-green-500 mr-2"></i>
        <h4 class="font-medium">Real-time AI Insights</h4>
      </div>
      
      <!-- First file insights -->
      <div class="insight-card">
        <div class="insight-header">
          <div>
            <h4 class="text-blue-600 font-medium">KNML 09846</h4>
            <p class="text-gray-600">Alhaji Ibrahim Dantata</p>
          </div>
          <div class="flex flex-col items-end">
            <span class="insight-confidence">92% Confidence</span>
            <span class="text-xs text-gray-500">AI Analysis</span>
          </div>
        </div>
        
        <div class="insight-analysis">
          <div>
            <h5 class="font-medium mb-2">Document Analysis:</h5>
            <div class="space-y-2">
              <div class="insight-field">
                <span class="insight-field-label">Document Type:</span>
                <span class="insight-field-value">Certificate of Occupancy</span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">Owner:</span>
                <span class="insight-field-value">
                  Alhaji Ibrahim Dantata
                  <span class="insight-confidence-pill">91%</span>
                </span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">Plot Number:</span>
                <span class="insight-field-value">
                  PL-4532
                  <span class="insight-confidence-pill">88%</span>
                </span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">Land Use:</span>
                <span class="insight-field-value">
                  Residential
                  <span class="insight-confidence-pill">87%</span>
                </span>
              </div>
            </div>
            
            <h5 class="font-medium mt-4 mb-2">AI Findings:</h5>
            <div class="space-y-2">
              <div class="insight-field">
                <span class="insight-field-label">Text Quality:</span>
                <span class="insight-field-value">
                  <span class="insight-confidence-pill">93%</span>
                </span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">Document Structure:</span>
                <span class="insight-field-value">Complete sections</span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">Signature:</span>
                <span class="insight-field-value">Not detected</span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">Stamp:</span>
                <span class="insight-field-value">Official stamp detected</span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">GIS Verification:</span>
                <span class="insight-field-value">Matched with parcel data</span>
              </div>
            </div>
          </div>
          
          <div>
            <h5 class="font-medium mb-2">Suggested Keywords:</h5>
            <div class="insight-keywords">
              <span class="insight-keyword">Residential</span>
              <span class="insight-keyword">Nasarawa</span>
              <span class="insight-keyword">Certificate of Occupancy</span>
              <span class="insight-keyword">Land Document</span>
              <span class="insight-keyword">Property</span>
              <span class="insight-keyword">Kano State</span>
              <span class="insight-keyword">Housing</span>
            </div>
            
            <div class="insight-issues">
              <h6 class="insight-issues-title">Potential Issues:</h6>
              <ul class="insight-issues-list">
                <li>Plot boundaries not specified</li>
                <li>Ownership information unclear</li>
                <li>Parcel data needs updating</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Second file insights -->
      <div class="insight-card">
        <div class="insight-header">
          <div>
            <h4 class="text-blue-600 font-medium">KNGP 00338</h4>
            <p class="text-gray-600">Hajiya Amina Yusuf</p>
          </div>
          <div class="flex flex-col items-end">
            <span class="insight-confidence">93% Confidence</span>
            <span class="text-xs text-gray-500">AI Analysis</span>
          </div>
        </div>
        
        <div class="insight-analysis">
          <div>
            <h5 class="font-medium mb-2">Document Analysis:</h5>
            <div class="space-y-2">
              <div class="insight-field">
                <span class="insight-field-label">Document Type:</span>
                <span class="insight-field-value">Site Plan</span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">Owner:</span>
                <span class="insight-field-value">
                  Hajiya Amina Yusuf
                  <span class="insight-confidence-pill">93%</span>
                </span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">Plot Number:</span>
                <span class="insight-field-value">
                  PL-1278
                  <span class="insight-confidence-pill">88%</span>
                </span>
              </div>
              
              <div class="insight-field">
                <span class="insight-field-label">Form Status:</span>
                <span class="insight-field-value">
                  Ready for submission
                  <span class="insight-confidence-pill">95%</span>
                </span>
              </div>
            </div>
          </div>
          
          <div>
            <h5 class="font-medium mb-2">Suggested Keywords:</h5>
            <div class="insight-keywords">
              <span class="insight-keyword">Commercial</span>
              <span class="insight-keyword">Fagge</span>
              <span class="insight-keyword">Site Plan</span>
              <span class="insight-keyword">Land Document</span>
              <span class="insight-keyword">Property</span>
              <span class="insight-keyword">Kano State</span>
              <span class="insight-keyword">Business</span>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Initialize Lucide icons for the new content
    lucide.createIcons();
  }
  
  // Function to complete the indexing process
  function completeIndexingProcess() {
    console.log("Completing indexing process and preparing for submission");
    
    // Show the confirm and save button
    confirmSaveResultsBtn.classList.remove('hidden');
  }
  
  // Confirm and save results
  function confirmAndSaveResults() {
    console.log("Submitting indexed data to KLAES");
    
    alert("Files have been successfully indexed and submitted to KLAES!");
    
    // Move selected files from pending to indexed
    selectedFiles.forEach(fileId => {
      const fileIndex = pendingFiles.findIndex(file => file.id === fileId);
      if (fileIndex !== -1) {
        const file = pendingFiles[fileIndex];
        // Update the source to indicate it's been indexed
        file.source = "Indexed";
        // Add to indexed files
        indexedFiles.push(file);
        // Remove from pending files
        pendingFiles.splice(fileIndex, 1);
      }
    });
    
    // Clear selected files
    selectedFiles = [];
    
    // Update counters
    updateCounters();
    
    // Reset the AI indexing view for next time
    const initialView = document.querySelector('#indexing-tab .card .p-6 .card');
    if (initialView) {
      initialView.parentElement.classList.remove('hidden');
    }
    aiProcessingView.classList.add('hidden');
    progressBar.style.width = '0%';
    progressPercentage.textContent = '0%';
    pipelineProgressBar.style.width = '0%';
    pipelineProgressLine.style.width = '0%';
    pipelinePercentage.textContent = '0% Complete';
    
    // Reset pipeline stages
    const stages = ['init', 'analyze', 'extract', 'categorize', 'validate', 'complete'];
    stages.forEach((stage, index) => {
      const element = document.getElementById(`stage-${stage}`);
      if (element) {
        element.classList.remove('active', 'completed');
        element.classList.add(index === 0 ? 'active' : 'pending');
      }
    });
    
    // Clear AI insights
    aiInsightsContainer.innerHTML = '';
    
    // Hide confirm button
    confirmSaveResultsBtn.classList.add('hidden');
    
    // Render pending files to update the list
    renderPendingFiles();
    
    // Render indexed files
    renderIndexedFiles();
    
    // Switch to indexed tab
    switchTab('indexed');
  }
  
  // Render indexed files
  function renderIndexedFiles() {
    const indexedFilesList = document.getElementById('indexed-files-list');
    indexedFilesList.innerHTML = '';
    
    if (indexedFiles.length === 0) {
      indexedFilesList.innerHTML = `
        <div class="p-8 text-center text-gray-500">
          <i data-lucide="file-question" class="h-12 w-12 mx-auto mb-4 text-gray-400"></i>
          <p>No indexed files yet. Start by indexing files from the File Index tab.</p>
        </div>
      `;
      lucide.createIcons();
      return;
    }
    
    // Update "Select All" checkbox state
    const selectAllCheckbox = document.getElementById('select-all-indexed-checkbox');
    if (selectAllCheckbox) {
      selectAllCheckbox.checked = selectedIndexedFiles.length === indexedFiles.length && indexedFiles.length > 0;
    }
    
    indexedFiles.forEach(file => {
      const isSelected = selectedIndexedFiles.includes(file.id);
      const fileItem = document.createElement('div');
      fileItem.className = 'p-4 border-b last:border-b-0';
      
      fileItem.innerHTML = `
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input type="checkbox" 
                   ${isSelected ? 'checked' : ''} 
                   data-id="${file.id}" 
                   class="indexed-file-checkbox mr-4"
                   title="Select for batch tracking operations">
            <div class="file-icon">
              <i data-lucide="file-check" class="h-6 w-6 text-green-500"></i>
            </div>
            <div class="file-details ml-4">
              <div class="file-number">${file.fileNumber}</div>
              <div class="file-name">${file.name}</div>
              <div class="file-tags">
                <span class="file-tag">${file.source}</span>
                <span class="file-tag">${file.landUseType}</span>
                <span class="file-tag">${file.district}</span>
                <span class="file-tag">${file.date}</span>
              </div>
            </div>
          </div>
          <div class="flex items-center">
            <span class="badge badge-green mr-3">
              <i data-lucide="check" class="h-3 w-3 mr-1"></i>
              Indexed
            </span>
            <div class="relative">
              <button class="action-menu-btn p-1 rounded-md hover:bg-gray-100" 
                      data-file-id="${file.id}" title="More Options">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"></path>
                </svg>
              </button>
              <div class="action-dropdown hidden absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg z-10 border">
                <div class="py-1">
                  <button class="view-file-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-file-id="${file.id}">
                    <i data-lucide="eye" class="h-4 w-4 mr-2 inline"></i>
                    View Details
                  </button>
                  <button class="generate-tracking-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-file-id="${file.id}">
                    <i data-lucide="file-text" class="h-4 w-4 mr-2 inline"></i>
                    Generate Tracking Sheet
                  </button>
                  <button class="print-tracking-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-file-id="${file.id}">
                    <i data-lucide="printer" class="h-4 w-4 mr-2 inline"></i>
                    Print Tracking Sheet
                  </button>
                  <button class="start-tracking-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-file-id="${file.id}">
                    <i data-lucide="play" class="h-4 w-4 mr-2 inline"></i>
                    Start Tracking
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      indexedFilesList.appendChild(fileItem);
    });
    
    // Initialize Lucide icons for the new rows
    lucide.createIcons();
    
    // Add event listeners for individual indexed file checkboxes
    document.querySelectorAll('.indexed-file-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const fileId = this.getAttribute('data-id');
        toggleIndexedFileSelection(fileId);
      });
    });
    
    // Update selected indexed files count
    updateSelectedIndexedFilesCount();
    
    // Add event listeners for action menus
    addActionMenuListeners();
  }
  
  // Switch between tabs
  function switchTab(tabName) {
    // Update active tab
    tabs.forEach(t => {
      if (t.getAttribute('data-tab') === tabName) {
        t.classList.add('active');
      } else {
        t.classList.remove('active');
      }
    });

    // Enable/disable new file button based on active tab
    const newFileBtn = document.getElementById('new-file-index-btn');
    if (tabName === 'pending') {
      newFileBtn.removeAttribute('disabled');
      newFileBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
      newFileBtn.setAttribute('disabled', 'true');
      newFileBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // Update visible content
    tabContents.forEach(content => {
      content.classList.add('hidden');
      content.classList.remove('active');
    });
    
    const activeContent = document.getElementById(`${tabName}-tab`);
    if (activeContent) {
      activeContent.classList.remove('hidden');
      activeContent.classList.add('active');
    }
    
    // If switching to indexed tab, render the indexed files
    if (tabName === 'indexed') {
      renderIndexedFiles();
    }
  }
  
  // Render pending files
  function renderPendingFiles() {
    pendingFilesList.innerHTML = '';
    
    // Update the "Select All" checkbox state
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (selectAllCheckbox) {
      selectAllCheckbox.checked = pendingFiles.length > 0 && selectedFiles.length === pendingFiles.length;
    }
    
    pendingFiles.forEach(file => {
      const isSelected = selectedFiles.includes(file.id);
      const fileItem = document.createElement('div');
      fileItem.className = 'p-4 border-b last:border-b-0';
      
      fileItem.innerHTML = `
        <div class="flex items-center">
          <input type="checkbox" ${isSelected ? 'checked' : ''} data-id="${file.id}" onclick="toggleFileSelection('${file.id}')" class="mr-4">
          <div class="file-icon">
            <i data-lucide="file-text" class="h-6 w-6"></i>
          </div>
          <div class="file-details ml-4">
            <div class="file-number">${file.fileNumber}</div>
            <div class="file-name">${file.name}</div>
            <div class="file-tags">
              <span class="file-tag">${file.source}</span>
              <span class="file-tag">${file.landUseType}</span>
              <span class="file-tag">${file.district}</span>
              <span class="file-tag">${file.date}</span>
            </div>
          </div>
          <div class="ml-auto">
            <span class="badge badge-yellow">
              <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
              Pending Digital Index
            </span>
          </div>
        </div>
      `;
      
      pendingFilesList.appendChild(fileItem);
    });
    
    // Initialize Lucide icons for the new rows
    lucide.createIcons();
    
    // Update selected files count
    updateSelectedFilesCount();
  }
  
  // Update selected files count
  function updateSelectedFilesCount() {
    selectedFilesCount.textContent = `${selectedFiles.length} of ${pendingFiles.length} selected`;
  }
  
  // Update counters
  function updateCounters() {
    document.getElementById('pending-files-count').textContent = pendingFiles.length;
    document.getElementById('indexed-files-count').textContent = indexedFiles.length;
  }
  
  // Show new file dialog
  function showNewFileDialog() {
    newFileDialogOverlay.classList.remove('hidden');
    // Reset form fields
    document.getElementById('new-file-form').reset();
  }
  
  // Close new file dialog
  function closeNewFileDialog() {
    newFileDialogOverlay.classList.add('hidden');
  }
  
  // Create new file
  function createNewFile() {
    // Get form values
    const fileTitle = document.getElementById('file-title').value;
    const fileNumberType = document.querySelector('input[name="file-number-type"]:checked').value;
    
    // Create a new file object
    const newFile = {
      id: `FILE-${Date.now()}`,
      fileNumber: fileNumberType === 'mls' ? 'MLS-' + Date.now().toString().slice(-5) : 'KNGP-' + Date.now().toString().slice(-5),
      name: fileTitle || 'New Property File',
      type: 'Certificate of Occupancy',
      source: 'Collated',
      date: new Date().toISOString().split('T')[0],
      landUseType: 'Residential',
      district: 'Nasarawa',
      hasCofo: document.getElementById('has-cofo').checked,
    };
    
    // Add to pending files
    pendingFiles.push(newFile);
    
    // Update counters
    updateCounters();
    
    // Render pending files
    renderPendingFiles();
    
    // Close dialog
    closeNewFileDialog();
    
    // Show success message
    alert('New file index created successfully!');
  }
  
  // Function to toggle indexed file selection
  function toggleIndexedFileSelection(fileId) {
    if (selectedIndexedFiles.includes(fileId)) {
      selectedIndexedFiles = selectedIndexedFiles.filter(id => id !== fileId);
    } else {
      selectedIndexedFiles.push(fileId);
    }
    
    renderIndexedFiles();
    updateSelectedIndexedFilesCount();
    updateTrackingButton();
  }
  
  // Make function globally accessible
  window.toggleIndexedFileSelection = toggleIndexedFileSelection;
  
  // Function to toggle select all indexed files - FIXED VERSION
  function toggleSelectAllIndexed() {
    const selectAllCheckbox = document.getElementById('select-all-indexed-checkbox');
    
    if (selectAllCheckbox.checked) {
      // Select all files
      selectedIndexedFiles = indexedFiles.map(file => file.id);
    } else {
      // Deselect all files
      selectedIndexedFiles = [];
    }
    
    renderIndexedFiles();
    updateSelectedIndexedFilesCount();
    updateTrackingButton();
  }
  
  // Function to update selected indexed files count
  function updateSelectedIndexedFilesCount() {
    const selectedCountElement = document.getElementById('selected-indexed-files-count');
    if (selectedCountElement) {
      selectedCountElement.textContent = `${selectedIndexedFiles.length} selected`;
    }
  }
  
  // Function to update tracking button behavior
  function updateTrackingButton() {
    const trackingBtn = document.getElementById('generate-tracking-sheets-btn');
    const trackingBtnText = document.getElementById('tracking-btn-text');
    
    if (trackingBtn && trackingBtnText) {
      if (selectedIndexedFiles.length === 0) {
        trackingBtnText.textContent = 'New File Index';
        trackingBtn.onclick = () => showNewFileDialog();
      } else if (selectedIndexedFiles.length === 1) {
        trackingBtnText.textContent = 'Generate Tracking Sheet';
        trackingBtn.onclick = () => generateSingleTrackingSheet();
      } else {
        trackingBtnText.textContent = 'Generate Batch Tracking Sheets';
        trackingBtn.onclick = () => openSmartBatchInterface();
      }
    }
  }
  
  // Function to check if file has tracking record (simplified)
  function hasTrackingRecord(fileId) {
    // All indexed files can now be selected for tracking operations
    return true;
  }
  
  // Function to generate single tracking sheet
  function generateSingleTrackingSheet() {
    const selectedFile = indexedFiles.find(file => file.id === selectedIndexedFiles[0]);
    if (selectedFile) {
      // Open tracking sheet in new tab using the blade template
      const trackingUrl = `/fileindexing/tracking-sheet/${selectedFile.id}`;
      window.open(trackingUrl, '_blank');
    }
  }
  
  // Function to open smart batch tracking interface
  function openSmartBatchInterface() {
    if (selectedIndexedFiles.length < 1) {
      alert('Please select at least one file for batch tracking operations.');
      return;
    }
    
    // Open smart batch tracking interface with selected files
    const fileIds = selectedIndexedFiles.join(',');
    const batchInterfaceUrl = `/fileindexing/batch-tracking-interface?files=${fileIds}`;
    window.open(batchInterfaceUrl, '_blank');
  }
  
  // Function to generate batch tracking sheets (legacy function, now redirects to smart interface)
  function generateBatchTrackingSheets() {
    openSmartBatchInterface();
  }
  
  // Function to print tracking sheet
  function printTrackingSheet(fileId) {
    const printUrl = `/fileindexing/print-tracking-sheet/${fileId}`;
    window.open(printUrl, '_blank');
  }
  
  // Function to add action menu event listeners
  function addActionMenuListeners() {
    // Handle action menu dropdown toggle
    document.querySelectorAll('.action-menu-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = this.nextElementSibling;
        
        // Close all other dropdowns
        document.querySelectorAll('.action-dropdown').forEach(d => {
          if (d !== dropdown) d.classList.add('hidden');
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('hidden');
      });
    });
    
    // Handle view file details
    document.querySelectorAll('.view-file-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const fileId = this.dataset.fileId;
        const file = indexedFiles.find(f => f.id === fileId);
        if (file) {
          alert(`File Details:\n\nFile Number: ${file.fileNumber}\nName: ${file.name}\nType: ${file.type}\nDistrict: ${file.district}\nLand Use: ${file.landUseType}\nDate: ${file.date}`);
        }
        this.closest('.action-dropdown').classList.add('hidden');
      });
    });
    
    // Handle generate tracking sheet
    document.querySelectorAll('.generate-tracking-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const fileId = this.dataset.fileId;
        generateSingleTrackingSheet(fileId);
        this.closest('.action-dropdown').classList.add('hidden');
      });
    });
    
    // Handle print tracking sheet
    document.querySelectorAll('.print-tracking-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const fileId = this.dataset.fileId;
        printTrackingSheet(fileId);
        this.closest('.action-dropdown').classList.add('hidden');
      });
    });
    
    // Handle start tracking
    document.querySelectorAll('.start-tracking-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const fileId = this.dataset.fileId;
        // Redirect to file tracker to start tracking for this file
        window.location.href = `/filetracker/create?file_indexing_id=${fileId}`;
        this.closest('.action-dropdown').classList.add('hidden');
      });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
      document.querySelectorAll('.action-dropdown').forEach(dropdown => {
        dropdown.classList.add('hidden');
      });
    });
  }
  
  // API Functions to load dynamic data
  async function loadPendingFiles(search = '') {
    try {
      const response = await fetch(`/fileindexing/api/pending-files?search=${encodeURIComponent(search)}`);
      const data = await response.json();
      
      if (data.success) {
        pendingFiles = data.pending_files;
        renderPendingFiles();
        updateCounters();
      } else {
        console.error('Error loading pending files:', data.message);
        // Fallback to empty array if API fails
        pendingFiles = [];
        renderPendingFiles();
        updateCounters();
      }
    } catch (error) {
      console.error('Error loading pending files:', error);
      // Fallback to empty array if API fails
      pendingFiles = [];
      renderPendingFiles();
      updateCounters();
    }
  }
  
  async function loadIndexedFiles(search = '') {
    try {
      const response = await fetch(`/fileindexing/api/indexed-files?search=${encodeURIComponent(search)}`);
      const data = await response.json();
      
      if (data.success) {
        indexedFiles = data.indexed_files;
        renderIndexedFiles();
        updateCounters();
      } else {
        console.error('Error loading indexed files:', data.message);
        // Fallback to empty array if API fails
        indexedFiles = [];
        renderIndexedFiles();
        updateCounters();
      }
    } catch (error) {
      console.error('Error loading indexed files:', error);
      // Fallback to empty array if API fails
      indexedFiles = [];
      renderIndexedFiles();
      updateCounters();
    }
  }
  
  // Search functionality
  function setupSearchListeners() {
    const searchPendingInput = document.getElementById('search-pending-files');
    const searchIndexedInput = document.getElementById('search-indexed-files');
    
    if (searchPendingInput) {
      let searchTimeout;
      searchPendingInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          loadPendingFiles(this.value);
        }, 300);
      });
    }
    
    if (searchIndexedInput) {
      let searchTimeout;
      searchIndexedInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          loadIndexedFiles(this.value);
        }, 300);
      });
    }
  }
  
  // Initialize the page when DOM is loaded
  document.addEventListener('DOMContentLoaded', function() {
    console.log("Initializing File Indexing Assistant");
    
    // Make sure File Index tab is active by default
    switchTab('pending');
    
    // Render the pending files list
    renderPendingFiles();
    
    // Render the indexed files list
    renderIndexedFiles();
    
    // Update counters
    updateCounters();
    
    // Add event listeners
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const tabName = tab.getAttribute('data-tab');
        switchTab(tabName);
      });
    });
    
    // Add event listener for select all checkbox
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (selectAllCheckbox) {
      selectAllCheckbox.addEventListener('click', toggleSelectAll);
    }
    
    // Add event listener for select all indexed files checkbox
    const selectAllIndexedCheckbox = document.getElementById('select-all-indexed-checkbox');
    if (selectAllIndexedCheckbox) {
      selectAllIndexedCheckbox.addEventListener('click', toggleSelectAllIndexed);
    }
    
    if (beginIndexingBtn) {
      beginIndexingBtn.addEventListener('click', () => {
        // Only switch tabs if files are selected
        if (selectedFiles.length > 0) {
          // Update the AI Indexing title to show the number of selected files
          const titleElement = document.querySelector('#indexing-tab .card h3');
          if (titleElement) {
            titleElement.textContent = `AI Indexing: ${selectedFiles.length} Files`;
          }
          
          // Update the ready message
          const messageElement = document.querySelector('#indexing-tab .card p.mb-6');
          if (messageElement) {
            messageElement.textContent = `Ready to begin AI-powered indexing for ${selectedFiles.length} selected files.`;
          }
          
          // Switch to the indexing tab
          switchTab('indexing');
        } else {
          alert("Please select at least one file to begin indexing.");
        }
      });
    }
    
    // New File Dialog event listeners
    if (newFileIndexBtn) {
      newFileIndexBtn.addEventListener('click', showNewFileDialog);
    }
    if (closeDialogBtn) {
      closeDialogBtn.addEventListener('click', closeNewFileDialog);
    }
    if (cancelBtn) {
      cancelBtn.addEventListener('click', closeNewFileDialog);
    }
    if (createFileBtn) {
      createFileBtn.addEventListener('click', createNewFile);
    }
    
    // File number type radio buttons
    fileNumberTypeRadios.forEach(radio => {
      radio.addEventListener('change', function() {
        document.querySelectorAll('.form-radio-item').forEach(item => {
          if (item.contains(this)) {
            item.classList.add('active');
          } else {
            item.classList.remove('active');
          }
        });
      });
    });
    
    if (startAiIndexingBtn) {
      startAiIndexingBtn.addEventListener('click', startAiIndexing);
    }
    if (confirmSaveResultsBtn) {
      confirmSaveResultsBtn.addEventListener('click', confirmAndSaveResults);
    }
    
    // Setup search listeners
    setupSearchListeners();
    
    // Load initial data
    loadPendingFiles();
    loadIndexedFiles();
  });
</script>