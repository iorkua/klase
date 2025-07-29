<!-- JavaScript -->
<script>
  // Initialize Lucide icons
  lucide.createIcons();
  
  // Sample data for pending indexing files
  let pendingFiles = [
    {
      id: "FILE-2023-001",
      fileNumber: "KNML 09846",
      name: "Alhaji Ibrahim Dantata",
      type: "Certificate of Occupancy",
      source: "Collated",
      date: "2023-06-15",
      landUseType: "Residential",
      district: "Nasarawa",
      hasCofo: true,
    },
    {
      id: "FILE-2023-002",
      fileNumber: "KNGP 00338",
      name: "Hajiya Amina Yusuf",
      type: "Site Plan",
      source: "Collated",
      date: "2023-06-14",
      landUseType: "Commercial",
      district: "Fagge",
      hasCofo: false,
    },
    {
      id: "FILE-2023-003",
      fileNumber: "MLKN 03051",
      name: "Kano Traders Association",
      type: "Letter of Administration",
      source: "Collated",
      date: "2023-06-13",
      landUseType: "Industrial",
      district: "Bompai",
      hasCofo: false,
    },
  ];
  
  // Sample data for indexed files
  let indexedFiles = [
    {
      id: "FILE-2023-004",
      fileNumber: "KNML 08146",
      name: "Musa Usman Bayero",
      type: "Right of Occupancy",
      source: "Indexed",
      date: "2023-06-12",
      landUseType: "Residential",
      district: "Nasarawa",
      hasCofo: true,
    },
    {
      id: "FILE-2023-005",
      fileNumber: "MLKN 03888",
      name: "Hajiya Fatima Mohammed",
      type: "Deed of Assignment",
      source: "Indexed & Scanned",
      date: "2023-06-10",
      landUseType: "Industrial",
      district: "Bompai",
      hasCofo: true,
    },
  ];
  
  // State variables
  let selectedFiles = []; // Initialize empty - no pre-selected files
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
  
  // Function to toggle select all
  function toggleSelectAll() {
    if (selectedFiles.length === pendingFiles.length) {
      // If all files are already selected, deselect all
      selectedFiles = [];
    } else {
      // Otherwise, select all files
      selectedFiles = pendingFiles.map(file => file.id);
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
    
    indexedFiles.forEach(file => {
      const fileItem = document.createElement('div');
      fileItem.className = 'p-4 border-b last:border-b-0';
      
      fileItem.innerHTML = `
        <div class="flex items-center">
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
          <div class="ml-auto">
            <span class="badge badge-green">
              <i data-lucide="check" class="h-3 w-3 mr-1"></i>
              Indexed
            </span>
          </div>
        </div>
      `;
      
      indexedFilesList.appendChild(fileItem);
    });
    
    // Initialize Lucide icons for the new rows
    lucide.createIcons();
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
    selectAllCheckbox.checked = pendingFiles.length > 0 && selectedFiles.length === pendingFiles.length;
    
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
    document.getElementById('select-all-checkbox').addEventListener('click', toggleSelectAll);
    
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
    
    // New File Dialog event listeners
    newFileIndexBtn.addEventListener('click', showNewFileDialog);
    closeDialogBtn.addEventListener('click', closeNewFileDialog);
    cancelBtn.addEventListener('click', closeNewFileDialog);
    createFileBtn.addEventListener('click', createNewFile);
    
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
    
    startAiIndexingBtn.addEventListener('click', startAiIndexing);
    confirmSaveResultsBtn.addEventListener('click', confirmAndSaveResults);
  });
</script>
