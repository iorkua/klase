<!-- JavaScript -->
<script>
  // Initialize Lucide icons safely
  if (typeof lucide !== 'undefined' && lucide.createIcons) {
    lucide.createIcons();
  }
  
  // State variables
  let selectedFiles = []; // Initialize empty - no pre-selected files
  let selectedIndexedFiles = []; // Track selected indexed files
  let pendingFiles = []; // Will be loaded from API
  let indexedFiles = []; // Start empty; will be loaded from API
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
    console.log("Starting AI indexing process for", selectedFiles.length, "files...");
    
    // Update the processing files count
    const aiProcessingFilesCount = document.getElementById('ai-processing-files-count');
    if (aiProcessingFilesCount) {
      aiProcessingFilesCount.textContent = selectedFiles.length;
    }
    
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
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
          lucide.createIcons();
        }
        
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
    console.log("Generating AI insights for selected files:", selectedFiles);
    
    // Get the actual selected file objects from pendingFiles
    const selectedFileObjects = pendingFiles.filter(file => selectedFiles.includes(file.id));
    
    if (selectedFileObjects.length === 0) {
      aiInsightsContainer.innerHTML = `
        <div class="text-center p-4">
          <p class="text-gray-500">No files selected for AI analysis.</p>
        </div>
      `;
      return;
    }
    
    // Generate insights for each selected file
    let insightsHTML = `
      <div class="flex items-center mb-2">
        <i data-lucide="zap" class="h-4 w-4 text-green-500 mr-2"></i>
        <h4 class="font-medium">Real-time AI Insights</h4>
      </div>
    `;
    
    selectedFileObjects.forEach((file, index) => {
      // Generate random confidence scores between 85-95%
      const mainConfidence = Math.floor(Math.random() * 11) + 85;
      const ownerConfidence = Math.floor(Math.random() * 11) + 85;
      const plotConfidence = Math.floor(Math.random() * 11) + 85;
      const landUseConfidence = Math.floor(Math.random() * 11) + 85;
      const textQuality = Math.floor(Math.random() * 11) + 85;
      
      // Determine document type based on file name or random selection
      const documentTypes = ['Certificate of Occupancy', 'Site Plan', 'Survey Plan', 'Deed of Assignment', 'Building Plan'];
      const documentType = documentTypes[Math.floor(Math.random() * documentTypes.length)];
      
      // Generate plot number if not available
      const plotNumber = `PL-${Math.floor(Math.random() * 9000) + 1000}`;
      
      // Generate suggested keywords based on file data
      const keywords = [
        file.landUseType || 'Residential',
        file.district || 'Fagge',
        documentType,
        'Land Document',
        'Property',
        'Kano State'
      ];
      
      insightsHTML += `
        <!-- File ${index + 1} insights -->
        <div class="insight-card">
          <div class="insight-header">
            <div>
              <h4 class="text-blue-600 font-medium">${file.fileNumber}</h4>
              <p class="text-gray-600">${file.name}</p>
            </div>
            <div class="flex flex-col items-end">
              <span class="insight-confidence">${mainConfidence}% Confidence</span>
              <span class="text-xs text-gray-500">AI Analysis</span>
            </div>
          </div>
          
          <div class="insight-analysis">
            <div>
              <h5 class="font-medium mb-2">Document Analysis:</h5>
              <div class="space-y-2">
                <div class="insight-field">
                  <span class="insight-field-label">Document Type:</span>
                  <span class="insight-field-value">${documentType}</span>
                </div>
                
                <div class="insight-field">
                  <span class="insight-field-label">Owner:</span>
                  <span class="insight-field-value">
                    ${file.name}
                    <span class="insight-confidence-pill">${ownerConfidence}%</span>
                  </span>
                </div>
                
                <div class="insight-field">
                  <span class="insight-field-label">Plot Number:</span>
                  <span class="insight-field-value">
                    ${plotNumber}
                    <span class="insight-confidence-pill">${plotConfidence}%</span>
                  </span>
                </div>
                
                <div class="insight-field">
                  <span class="insight-field-label">Land Use:</span>
                  <span class="insight-field-value">
                    ${file.landUseType || 'Residential'}
                    <span class="insight-confidence-pill">${landUseConfidence}%</span>
                  </span>
                </div>
              </div>
              
              <h5 class="font-medium mt-4 mb-2">AI Findings:</h5>
              <div class="space-y-2">
                <div class="insight-field">
                  <span class="insight-field-label">Text Quality:</span>
                  <span class="insight-field-value">
                    <span class="insight-confidence-pill">${textQuality}%</span>
                  </span>
                </div>
                
                <div class="insight-field">
                  <span class="insight-field-label">Document Structure:</span>
                  <span class="insight-field-value">Complete sections</span>
                </div>
                
                <div class="insight-field">
                  <span class="insight-field-label">Signature:</span>
                  <span class="insight-field-value">${Math.random() > 0.5 ? 'Detected' : 'Not detected'}</span>
                </div>
                
                <div class="insight-field">
                  <span class="insight-field-label">Stamp:</span>
                  <span class="insight-field-value">${Math.random() > 0.3 ? 'Official stamp detected' : 'Stamp not clear'}</span>
                </div>
                
                <div class="insight-field">
                  <span class="insight-field-label">GIS Verification:</span>
                  <span class="insight-field-value">${Math.random() > 0.4 ? 'Matched with parcel data' : 'Pending verification'}</span>
                </div>
              </div>
            </div>
            
            <div>
              <h5 class="font-medium mb-2">Suggested Keywords:</h5>
              <div class="insight-keywords">
                ${keywords.map(keyword => `<span class="insight-keyword">${keyword}</span>`).join('')}
              </div>
              
              <div class="insight-issues">
                <h6 class="insight-issues-title">Potential Issues:</h6>
                <ul class="insight-issues-list">
                  ${Math.random() > 0.5 ? '<li>Plot boundaries not specified</li>' : ''}
                  ${Math.random() > 0.6 ? '<li>Ownership information unclear</li>' : ''}
                  ${Math.random() > 0.7 ? '<li>Parcel data needs updating</li>' : ''}
                  ${Math.random() > 0.8 ? '<li>Document quality could be improved</li>' : ''}
                </ul>
              </div>
            </div>
          </div>
        </div>
      `;
    });
    
    aiInsightsContainer.innerHTML = insightsHTML;
    
    // Initialize Lucide icons for the new content
    if (typeof lucide !== 'undefined' && lucide.createIcons) {
      lucide.createIcons();
    }
  }
  
  // Function to complete the indexing process
  function completeIndexingProcess() {
    console.log("Completing indexing process and preparing for submission");
    
    // Show the confirm and save button
    confirmSaveResultsBtn.classList.remove('hidden');
  }
  
  // Confirm and save results
  async function confirmAndSaveResults() {
    console.log("Submitting indexed data to KLAES");
    
    try {
      // Get the actual selected file objects from pendingFiles
      const selectedFileObjects = pendingFiles.filter(file => selectedFiles.includes(file.id));
      
      if (selectedFileObjects.length === 0) {
        alert("No files selected for submission.");
        return;
      }
      
      // Prepare bulk entries data for the API
      const bulkEntries = selectedFileObjects.map(file => ({
        file_number: file.fileNumber,
        file_title: file.name,
        plot_number: file.plotNumber || `PL-${Math.floor(Math.random() * 9000) + 1000}`,
        land_use_type: file.landUseType || 'Residential',
        district: file.district || 'Fagge',
        source: 'AI_Indexing',
        extracted_metadata: {
          ai_confidence: Math.floor(Math.random() * 11) + 85,
          processing_date: new Date().toISOString(),
          document_type: ['Certificate of Occupancy', 'Site Plan', 'Survey Plan', 'Deed of Assignment', 'Building Plan'][Math.floor(Math.random() * 5)],
          processed_by_ai: true
        }
      }));
      
      // Submit to the backend API
      const response = await fetch('/fileindexing/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          bulk_entries: bulkEntries
        })
      });
      
      const result = await response.json();
      
      if (result.success) {
        let successMessage = `Files have been successfully indexed and submitted to KLAES!\n\n`;
        successMessage += `✅ ${result.created_count} files processed and saved to the database.\n`;
        
        if (result.created_files && result.created_files.length > 0) {
          successMessage += `\nIndexed Files:\n`;
          result.created_files.forEach(file => {
            successMessage += `• ${file.file_number}: ${file.file_title}\n`;
          });
        }
        
        if (result.errors && result.errors.length > 0) {
          successMessage += `\n⚠️ ${result.errors.length} errors occurred:\n`;
          result.errors.forEach(error => {
            successMessage += `• ${error}\n`;
          });
        }
        
        alert(successMessage);
        
        // Move selected files from pending to indexed
        selectedFiles.forEach(fileId => {
          const fileIndex = pendingFiles.findIndex(file => file.id === fileId);
          if (fileIndex !== -1) {
            const file = pendingFiles[fileIndex];
            // Update the source to indicate it's been indexed
            file.source = "Indexed";
            file.indexingDate = new Date().toLocaleDateString();
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
        
        // Refresh the pending files display
        renderPendingFiles();
        
        // Switch back to the pending tab to see the updated list
        switchTab('pending');
        
        // Reset the AI processing view
        resetAiProcessingView();
        
      } else {
        alert(`Error submitting files: ${result.message}`);
        console.error('API Error:', result);
      }
      
    } catch (error) {
      console.error('Error submitting indexed files:', error);
      alert('Error submitting files to the database. Please try again.');
    }
  }

  // Complete indexing process  // Function to reset AI processing view
  function resetAiProcessingView() {
    // Hide the processing view
    aiProcessingView.classList.add('hidden');
    
    // Show the initial indexing tab view
    const initialView = document.querySelector('#indexing-tab .card .p-6 .card');
    if (initialView) {
      initialView.parentElement.classList.remove('hidden');
    }
    
    // Reset progress bars
    progressBar.style.width = '0%';
    progressPercentage.textContent = '0%';
    pipelineProgressBar.style.width = '0%';
    pipelineProgressLine.style.width = '0%';
    pipelinePercentage.textContent = '0% Complete';
    
    // Reset pipeline stages
    const stages = ['init', 'analyze', 'extract', 'categorize', 'validate', 'complete'];
    stages.forEach(stage => {
      const dot = document.getElementById(`stage-${stage}`);
      const label = dot?.nextElementSibling;
      if (dot) {
        dot.className = stage === 'init' ? 'pipeline-dot active' : 'pipeline-dot pending';
      }
      if (label) {
        label.className = stage === 'init' ? 'pipeline-label active' : 'pipeline-label pending';
      }
    });
    
    // Reset current stage info
    if (currentStageInfo) {
      currentStageInfo.innerHTML = `
        <div class="p-2 bg-green-100 rounded-full">
          <i data-lucide="loader" class="h-5 w-5 text-green-500"></i>
        </div>
        <div>
          <p class="text-sm font-medium mb-1">Current Stage: Initialization</p>
          <p class="text-xs text-gray-600">Setting up AI processing environment and preparing documents for analysis...</p>
        </div>
      `;
    }
    
    // Reset AI insights
    aiInsightsContainer.innerHTML = '';
    
    // Hide the confirm save button
    confirmSaveResultsBtn.classList.add('hidden');
  }
  
  // Render indexed files
  function renderIndexedFiles() {
    const tableBody = document.getElementById('indexed-files-table-body');
    const emptyState = document.getElementById('indexed-empty-state');
    const tableContainer = document.getElementById('indexed-table-container');

    // Prefer table-based rendering if present
    if (tableBody) {
      // Toggle empty state visibility
      if (!indexedFiles || indexedFiles.length === 0) {
        if (tableContainer) tableContainer.style.display = 'none';
        if (emptyState) emptyState.style.display = 'block';
        // Update select-all checkbox and counts
        const selectAllCheckbox = document.getElementById('select-all-indexed-checkbox');
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        updateSelectedIndexedFilesCount();
        updateTrackingButton();
        return;
      } else {
        if (tableContainer) tableContainer.style.display = 'block';
        if (emptyState) emptyState.style.display = 'none';
      }

      tableBody.innerHTML = '';

      // Update "Select All" checkbox state
      const selectAllCheckbox = document.getElementById('select-all-indexed-checkbox');
      if (selectAllCheckbox) {
        selectAllCheckbox.checked = selectedIndexedFiles.length === indexedFiles.length && indexedFiles.length > 0;
      }

      indexedFiles.forEach(file => {
        const fileIdStr = String(file.id);
        const isSelected = selectedIndexedFiles.includes(fileIdStr);
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', fileIdStr);
        tr.className = `border-b hover:bg-gray-50 ${isSelected ? 'bg-blue-50' : ''}`;
        tr.innerHTML = `
          <td class="p-3 w-10">
            <input type="checkbox" class="row-indexed-checkbox" data-file-id="${fileIdStr}" ${isSelected ? 'checked' : ''} />
          </td>
          <td class="p-3">${file.fileNumber || '-'}</td>
          <td class="p-3">${file.name || '-'}</td>
          <td class="p-3">${file.registry || '-'}</td>
          <td class="p-3">${file.date || '-'}</td>
          <td class="p-3">
            <span class="badge badge-green">
              <i data-lucide="check" class="h-3 w-3 mr-1 inline"></i>
              Indexed
            </span>
          </td>
          <td class="p-3">${file.location || '-'}</td>
          <td class="p-3">${file.landUseType || '-'}</td>
          <td class="p-3">${file.district || '-'}</td>
          <td class="p-3 text-right">
            <div class="inline-flex gap-2">
              <button class="view-file-btn px-2 py-1 rounded hover:bg-gray-100" data-file-id="${fileIdStr}" title="View Details">
                <i data-lucide="eye" class="h-4 w-4"></i>
              </button>
              <button class="generate-tracking-btn px-2 py-1 rounded hover:bg-gray-100" data-file-id="${fileIdStr}" title="Generate Tracking Sheet">
                <i data-lucide="file-text" class="h-4 w-4"></i>
              </button>
              <button class="print-tracking-btn px-2 py-1 rounded hover:bg-gray-100" data-file-id="${fileIdStr}" title="Print Tracking Sheet">
                <i data-lucide="printer" class="h-4 w-4"></i>
              </button>
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
      });

      // Initialize Lucide icons for the new rows
      if (typeof lucide !== 'undefined' && lucide.createIcons) {
        lucide.createIcons();
      }

      // Row click toggles selection (ignore clicks on action buttons and checkboxes)
      tableBody.querySelectorAll('tr[data-id]').forEach(row => {
        row.addEventListener('click', function(e) {
          if (e.target.closest('button') || e.target.closest('input[type="checkbox"]')) return;
          const fileId = this.getAttribute('data-id');
          toggleIndexedFileSelection(fileId);
        });
      });

      // Row checkbox change handler
      tableBody.querySelectorAll('.row-indexed-checkbox').forEach(cb => {
        cb.addEventListener('click', e => e.stopPropagation());
        cb.addEventListener('change', function(e) {
          const id = this.dataset.fileId;
          toggleIndexedFileSelection(id);
        });
      });

      // Action buttons
      tableBody.querySelectorAll('.view-file-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.stopPropagation();
          const fileId = this.dataset.fileId;
          const file = indexedFiles.find(f => String(f.id) === fileId);
          if (file) {
            alert(`File Details:\n\nFile Number: ${file.fileNumber}\nName: ${file.name}\nType: ${file.type || '-'}\nDistrict: ${file.district || '-'}\nLand Use: ${file.landUseType || '-'}\nDate: ${file.date || '-'}`);
          }
        });
      });

      tableBody.querySelectorAll('.generate-tracking-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.stopPropagation();
          const fileId = this.dataset.fileId;
          if (!/^\d+$/.test(fileId)) {
            alert('This is a demo file. Tracking sheet generation is not available for demo files.');
            return;
          }
          const trackingUrl = `/fileindexing/tracking-sheet/${fileId}`;
          window.open(trackingUrl, '_blank');
        });
      });

      tableBody.querySelectorAll('.print-tracking-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.stopPropagation();
          const fileId = this.dataset.fileId;
          if (!/^\d+$/.test(fileId)) {
            alert('This is a demo file. Print tracking is not available for demo files.');
            return;
          }
          printTrackingSheet(fileId);
        });
      });

      // Update counts and button state
      updateSelectedIndexedFilesCount();
      updateTrackingButton();
      return; // Done with table-based rendering
    }

    // Fallback: do nothing if no known container is present to avoid runtime errors
    // (prevents breaking other UI like tabs)
  }
  
  // Switch between tabs
  function switchTab(tabName) {
    // Query tabs fresh in case DOM changed
    const tabElements = document.querySelectorAll('.tab');

    // Update active tab
    tabElements.forEach(t => {
      if (t.getAttribute('data-tab') === tabName) {
        t.classList.add('active');
      } else {
        t.classList.remove('active');
      }
    });

    // Enable/disable new file button based on active tab
    const newFileBtn = document.getElementById('new-file-index-btn');
    if (newFileBtn) {
      if (tabName === 'pending') {
        newFileBtn.removeAttribute('disabled');
        newFileBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      } else {
        newFileBtn.setAttribute('disabled', 'true');
        newFileBtn.classList.add('opacity-50', 'cursor-not-allowed');
      }
    }

    // Update visible content
    const tabContents = document.querySelectorAll('.tab-content');
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
    if (typeof lucide !== 'undefined' && lucide.createIcons) {
      lucide.createIcons();
    }
    
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
    const idStr = String(fileId);
    if (selectedIndexedFiles.includes(idStr)) {
      selectedIndexedFiles = selectedIndexedFiles.filter(id => id !== idStr);
    } else {
      selectedIndexedFiles.push(idStr);
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
      selectedIndexedFiles = indexedFiles.map(file => String(file.id));
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
    const trackingBtnIcon = trackingBtn ? trackingBtn.querySelector('i') : null;
    
    if (!trackingBtn || !trackingBtnText) return;

    // Helper to reset click handler
    function resetClick() {
      // Using property assignment avoids multiple listeners stacking
      trackingBtn.onclick = null;
    }

    if (selectedIndexedFiles.length >= 2) {
      // Enabled state for batch
      trackingBtnText.textContent = 'Generate Batch Tracking Sheets';
      trackingBtn.removeAttribute('disabled');
      trackingBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      trackingBtn.classList.add('btn-primary');
      if (trackingBtnIcon) trackingBtnIcon.setAttribute('data-lucide', 'file-check');

      resetClick();
      trackingBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        generateBatchTrackingSheets();
      };
    } else {
      // Disabled state for fewer than 2 selections
      trackingBtnText.textContent = 'Batch Tracking Sheets';
      trackingBtn.setAttribute('disabled', 'true');
      trackingBtn.classList.add('opacity-50', 'cursor-not-allowed');
      if (trackingBtnIcon) trackingBtnIcon.setAttribute('data-lucide', 'file-text');
      resetClick();
    }

    // Refresh lucide icons after attribute changes
    if (typeof lucide !== 'undefined' && lucide.createIcons) {
      lucide.createIcons();
    }
  }
  
  // Function to check if file has tracking record (simplified)
  function hasTrackingRecord(fileId) {
    // All indexed files can now be selected for tracking operations
    return true;
  }
  
  // Function to generate single tracking sheet
  function generateSingleTrackingSheet() {
    const selectedFile = indexedFiles.find(file => String(file.id) === selectedIndexedFiles[0]);
    if (selectedFile) {
      // Extract numeric ID if the file ID contains non-numeric characters
      let fileId = String(selectedFile.id);
      
      // If the ID is not purely numeric, try to extract a numeric part or use a timestamp
      if (!/^\d+$/.test(fileId)) {
        // For demo files with non-numeric IDs, use a fallback approach
        console.log('Non-numeric file ID detected:', fileId);
        alert('This is a demo file. Tracking sheet generation is not available for demo files.');
        return;
      }
      
      // Open tracking sheet in new tab using the blade template
      const trackingUrl = `/fileindexing/tracking-sheet/${fileId}`;
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
        
        // Check if this is a demo file (non-numeric ID)
        if (!/^\d+$/.test(fileId)) {
          alert('This is a demo file. Tracking sheet generation is not available for demo files.');
          this.closest('.action-dropdown').classList.add('hidden');
          return;
        }
        
        // Open tracking sheet in new tab
        const trackingUrl = `/fileindexing/tracking-sheet/${fileId}`;
        window.open(trackingUrl, '_blank');
        this.closest('.action-dropdown').classList.add('hidden');
      });
    });
    
    // Handle print tracking sheet
    document.querySelectorAll('.print-tracking-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const fileId = this.dataset.fileId;
        
        // Check if this is a demo file (non-numeric ID)
        if (!/^\d+$/.test(fileId)) {
          alert('This is a demo file. Print tracking is not available for demo files.');
          this.closest('.action-dropdown').classList.add('hidden');
          return;
        }
        
        printTrackingSheet(fileId);
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
  
  // Pagination state
  let currentPendingPage = 1;
  let currentIndexedPage = 1;
  const itemsPerPage = 10;
  
  // API Functions to load dynamic data with pagination
  async function loadPendingFiles(search = '', page = 1) {
    try {
      const response = await fetch(`/fileindexing/api/pending-files?search=${encodeURIComponent(search)}&page=${page}&per_page=${itemsPerPage}`);
      const data = await response.json();
      
      if (data.success) {
        pendingFiles = data.pending_files;
        currentPendingPage = page;
        renderPendingFiles();
        updateCounters();
        updatePendingPagination(data.pagination || {
          current_page: page,
          total: data.pending_files?.length || 0,
          per_page: itemsPerPage,
          last_page: Math.ceil((data.pending_files?.length || 0) / itemsPerPage)
        });
      } else {
        console.error('Error loading pending files:', data.message);
        // Fallback to empty array if API fails
        pendingFiles = [];
        renderPendingFiles();
        updateCounters();
        hidePendingPagination();
      }
    } catch (error) {
      console.error('Error loading pending files:', error);
      // Fallback to empty array if API fails
      pendingFiles = [];
      renderPendingFiles();
      updateCounters();
      hidePendingPagination();
    }
  }
  
  async function loadIndexedFiles(search = '', page = 1) {
    try {
      const response = await fetch(`/fileindexing/api/indexed-files?search=${encodeURIComponent(search)}&page=${page}&per_page=${itemsPerPage}`);
      const data = await response.json();
      
      if (data.success) {
        indexedFiles = data.indexed_files;
        currentIndexedPage = page;
        renderIndexedFiles();
        updateCounters();
        updateIndexedPagination(data.pagination || {
          current_page: page,
          total: data.indexed_files?.length || 0,
          per_page: itemsPerPage,
          last_page: Math.ceil((data.indexed_files?.length || 0) / itemsPerPage)
        });
      } else {
        console.error('Error loading indexed files:', data.message);
        // Fallback to empty array if API fails
        indexedFiles = [];
        renderIndexedFiles();
        updateCounters();
        hideIndexedPagination();
      }
    } catch (error) {
      console.error('Error loading indexed files:', error);
      // Fallback to empty array if API fails
      indexedFiles = [];
      renderIndexedFiles();
      updateCounters();
      hideIndexedPagination();
    }
  }
  
  // Pagination functions
  function updatePendingPagination(pagination) {
    const paginationContainer = document.getElementById('pending-pagination');
    const startElement = document.getElementById('pending-start');
    const endElement = document.getElementById('pending-end');
    const totalElement = document.getElementById('pending-total');
    const paginationNav = document.getElementById('pending-pagination-nav');
    
    if (!pagination || pagination.total === 0) {
      hidePendingPagination();
      return;
    }
    
    // Show pagination
    paginationContainer.style.display = 'flex';
    
    // Update counters
    const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
    
    startElement.textContent = start;
    endElement.textContent = end;
    totalElement.textContent = pagination.total;
    
    // Generate page numbers
    generatePageNumbers(paginationNav, pagination.current_page, pagination.last_page, 'pending');
  }
  
  function updateIndexedPagination(pagination) {
    const paginationContainer = document.getElementById('indexed-pagination');
    const startElement = document.getElementById('indexed-start');
    const endElement = document.getElementById('indexed-end');
    const totalElement = document.getElementById('indexed-total');
    const paginationNav = document.getElementById('indexed-pagination-nav');
    
    if (!pagination || pagination.total === 0) {
      hideIndexedPagination();
      return;
    }
    
    // Show pagination
    paginationContainer.style.display = 'flex';
    
    // Update counters
    const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
    
    startElement.textContent = start;
    endElement.textContent = end;
    totalElement.textContent = pagination.total;
    
    // Generate page numbers
    generatePageNumbers(paginationNav, pagination.current_page, pagination.last_page, 'indexed');
  }
  
  function hidePendingPagination() {
    const paginationContainer = document.getElementById('pending-pagination');
    if (paginationContainer) {
      paginationContainer.style.display = 'none';
    }
  }
  
  function hideIndexedPagination() {
    const paginationContainer = document.getElementById('indexed-pagination');
    if (paginationContainer) {
      paginationContainer.style.display = 'none';
    }
  }
  
  function generatePageNumbers(container, currentPage, lastPage, type) {
    // Clear existing page numbers (keep prev/next buttons)
    const existingNumbers = container.querySelectorAll('.page-number');
    existingNumbers.forEach(el => el.remove());
    
    const prevButton = container.querySelector(`#${type}-prev`);
    const nextButton = container.querySelector(`#${type}-next`);
    
    // Calculate page range to show
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(lastPage, startPage + maxVisiblePages - 1);
    
    // Adjust start if we're near the end
    if (endPage - startPage < maxVisiblePages - 1) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    // Create page number buttons
    for (let i = startPage; i <= endPage; i++) {
      const pageButton = document.createElement('button');
      pageButton.className = `page-number relative inline-flex items-center px-4 py-2 border text-sm font-medium ${
        i === currentPage 
          ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' 
          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
      }`;
      pageButton.textContent = i;
      pageButton.onclick = () => {
        if (type === 'pending') {
          loadPendingFiles(document.getElementById('search-pending-files')?.value || '', i);
        } else {
          loadIndexedFiles(document.getElementById('search-indexed-files')?.value || '', i);
        }
      };
      
      // Insert before next button
      container.insertBefore(pageButton, nextButton);
    }
    
    // Update prev/next button states
    prevButton.disabled = currentPage <= 1;
    nextButton.disabled = currentPage >= lastPage;
    
    prevButton.className = `relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 text-sm font-medium ${
      currentPage <= 1 
        ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
        : 'bg-white text-gray-500 hover:bg-gray-50'
    }`;
    
    nextButton.className = `relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 text-sm font-medium ${
      currentPage >= lastPage 
        ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
        : 'bg-white text-gray-500 hover:bg-gray-50'
    }`;
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
          currentPendingPage = 1; // Reset to first page on search
          loadPendingFiles(this.value, 1);
        }, 300);
      });
    }
    
    if (searchIndexedInput) {
      let searchTimeout;
      searchIndexedInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          currentIndexedPage = 1; // Reset to first page on search
          loadIndexedFiles(this.value, 1);
        }, 300);
      });
    }
  }
  
  // Setup pagination event listeners
  function setupPaginationListeners() {
    // Pending files pagination
    const pendingPrev = document.getElementById('pending-prev');
    const pendingNext = document.getElementById('pending-next');
    const pendingPrevMobile = document.getElementById('pending-prev-mobile');
    const pendingNextMobile = document.getElementById('pending-next-mobile');
    
    if (pendingPrev) {
      pendingPrev.addEventListener('click', () => {
        if (currentPendingPage > 1) {
          loadPendingFiles(document.getElementById('search-pending-files')?.value || '', currentPendingPage - 1);
        }
      });
    }
    
    if (pendingNext) {
      pendingNext.addEventListener('click', () => {
        loadPendingFiles(document.getElementById('search-pending-files')?.value || '', currentPendingPage + 1);
      });
    }
    
    if (pendingPrevMobile) {
      pendingPrevMobile.addEventListener('click', () => {
        if (currentPendingPage > 1) {
          loadPendingFiles(document.getElementById('search-pending-files')?.value || '', currentPendingPage - 1);
        }
      });
    }
    
    if (pendingNextMobile) {
      pendingNextMobile.addEventListener('click', () => {
        loadPendingFiles(document.getElementById('search-pending-files')?.value || '', currentPendingPage + 1);
      });
    }
    
    // Indexed files pagination
    const indexedPrev = document.getElementById('indexed-prev');
    const indexedNext = document.getElementById('indexed-next');
    const indexedPrevMobile = document.getElementById('indexed-prev-mobile');
    const indexedNextMobile = document.getElementById('indexed-next-mobile');
    
    if (indexedPrev) {
      indexedPrev.addEventListener('click', () => {
        if (currentIndexedPage > 1) {
          loadIndexedFiles(document.getElementById('search-indexed-files')?.value || '', currentIndexedPage - 1);
        }
      });
    }
    
    if (indexedNext) {
      indexedNext.addEventListener('click', () => {
        loadIndexedFiles(document.getElementById('search-indexed-files')?.value || '', currentIndexedPage + 1);
      });
    }
    
    if (indexedPrevMobile) {
      indexedPrevMobile.addEventListener('click', () => {
        if (currentIndexedPage > 1) {
          loadIndexedFiles(document.getElementById('search-indexed-files')?.value || '', currentIndexedPage - 1);
        }
      });
    }
    
    if (indexedNextMobile) {
      indexedNextMobile.addEventListener('click', () => {
        loadIndexedFiles(document.getElementById('search-indexed-files')?.value || '', currentIndexedPage + 1);
      });
    }
  }
  
  // Initialize the page when DOM is loaded
  document.addEventListener('DOMContentLoaded', function() {
    console.log("Initializing File Indexing Assistant");
    
    // Make sure File Index tab is active by default
    switchTab('pending');
    
    // Render lists and counters
    renderPendingFiles();
    renderIndexedFiles();
    updateCounters();
    
    // Tabs click handling (ignore disabled tabs)
    document.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => {
        if (tab.classList.contains('disabled')) return;
        const tabName = tab.getAttribute('data-tab');
        switchTab(tabName);
      });
    });

    // Empty-state button to go to Pending
    const goToPendingBtn = document.getElementById('go-to-pending');
    if (goToPendingBtn) {
      goToPendingBtn.addEventListener('click', () => switchTab('pending'));
    }

    if (beginIndexingBtn) {
      beginIndexingBtn.addEventListener('click', () => {
        // Only switch tabs if files are selected
        if (selectedFiles.length > 0) {
          // Update the AI Indexing file counts
          const aiIndexingFilesCount = document.getElementById('ai-indexing-files-count');
          const aiSelectedFilesCount = document.getElementById('ai-selected-files-count');
          const aiProcessingFilesCount = document.getElementById('ai-processing-files-count');
          
          if (aiIndexingFilesCount) {
            aiIndexingFilesCount.textContent = selectedFiles.length;
          }
          if (aiSelectedFilesCount) {
            aiSelectedFilesCount.textContent = selectedFiles.length;
          }
          if (aiProcessingFilesCount) {
            aiProcessingFilesCount.textContent = selectedFiles.length;
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
    
    // Setup pagination listeners
    setupPaginationListeners();
    
    // Load initial data
    loadPendingFiles();
    loadIndexedFiles();

    // Ensure tracking button reflects initial selection state
    updateTrackingButton();

    // Select All (Pending)
    const selectAllPending = document.getElementById('select-all-checkbox');
    if (selectAllPending) {
      selectAllPending.addEventListener('change', toggleSelectAll);
    }

    // Select All (Indexed)
    const selectAllIndexed = document.getElementById('select-all-indexed-checkbox');
    if (selectAllIndexed) {
      selectAllIndexed.addEventListener('change', toggleSelectAllIndexed);
    }
  });

 
</script>