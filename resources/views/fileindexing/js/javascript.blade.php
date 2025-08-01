<!-- JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
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
          newFileBtn.removeAttribute('disabled');
          newFileBtn.classList.remove('opacity-50', 'cursor-not-allowed');

          // Update visible content
          tabContents.forEach(content => {
            if (content.id === `${tabName}-tab`) {
              content.classList.remove('hidden');
              content.classList.add('active');
            } else {
              content.classList.add('hidden');
              content.classList.remove('active');
            }
          });
        }
        
                
                
                
                
                
        
        function renderPendingFiles() {
            pendingFilesList.innerHTML = '';
            pendingFiles.forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex justify-between items-center p-4 border-b';
                fileItem.innerHTML = `
                    <div class="flex items-center">
                        <input type="checkbox" class="mr-4" data-file-id="${file.id}" ${selectedFiles.includes(file.id) ? 'checked' : ''}>
                        <div>
                            <div class="font-medium">${file.name}</div>
                            <div class="text-sm text-gray-500">${file.fileNumber}</div>
                        </div>
                    </div>
                    <div class="text-sm">${file.type}</div>
                    <div class="text-sm">${file.source}</div>
                    <div class="text-sm">${file.date}</div>
                    <div class="text-sm">${file.landUseType}</div>
                    <div class="text-sm">${file.district}</div>
                    <div>
                        <span class="badge ${file.hasCofo ? 'badge-green' : 'badge-red'}">${file.hasCofo ? 'C of O' : 'No C of O'}</span>
                    </div>
                `;
                fileItem.querySelector('input[type="checkbox"]').addEventListener('change', () => toggleFileSelection(file.id));
                pendingFilesList.appendChild(fileItem);
            });
        }

        function updateCounters() {
            document.getElementById('pending-files-count').textContent = pendingFiles.length;
            document.getElementById('indexed-files-count').textContent = indexedFiles.length;
        }

        function updateSelectedFilesCount() {
            const countElement = document.getElementById('selected-files-count');
            countElement.textContent = `${selectedFiles.length} of ${pendingFiles.length} selected`;
        }

        console.log("Initializing File Indexing Assistant");

        // Make sure File Index tab is active by default
        switchTab('pending');

        // Render the pending files list
        renderPendingFiles();

        // Update counters
        updateCounters();

        // Add event listeners
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabName = tab.getAttribute('data-tab');
                switchTab(tabName);
            });
        });

        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        if(selectAllCheckbox){
            selectAllCheckbox.addEventListener('click', toggleSelectAll);
        }

        if(beginIndexingBtn){
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

        if(newFileIndexBtn){
            newFileIndexBtn.addEventListener('click', showNewFileDialog);
        }
        if(closeDialogBtn){
            closeDialogBtn.addEventListener('click', closeNewFileDialog);
        }
        if(cancelBtn){
            cancelBtn.addEventListener('click', closeNewFileDialog);
        }
        if(createFileBtn){
            createFileBtn.addEventListener('click', createNewFile);
        }

        if(fileNumberTypeRadios){
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
        }

        // Functions for New File Index Dialog
        function showNewFileDialog() {
            console.log('Opening New File Index Dialog');
            if (newFileDialogOverlay) {
                newFileDialogOverlay.classList.remove('hidden');
                // Initialize Lucide icons for the dialog
                lucide.createIcons();
            }
        }

        function closeNewFileDialog() {
            console.log('Closing New File Index Dialog');
            if (newFileDialogOverlay) {
                newFileDialogOverlay.classList.add('hidden');
            }
            // Reset form
            const form = document.getElementById('new-file-form');
            if (form) {
                form.reset();
            }
        }

        function createNewFile() {
            console.log('Creating new file index');
            
            // Get form data
            const fileTitle = document.getElementById('file-title').value;
            
            if (!fileTitle.trim()) {
                alert('Please enter a file title');
                return;
            }

            // Get file number from either smart selector or manual entry
            let fileNumber = '';
            const smartFilenoInput = document.getElementById('fileno');
            if (smartFilenoInput && smartFilenoInput.value) {
                fileNumber = smartFilenoInput.value;
            } else {
                // Get from manual entry (Alpine.js data)
                const manualFilenoContainer = document.querySelector('[x-data*="mlsPrefix"]');
                if (manualFilenoContainer) {
                    // This would need to be implemented based on the active tab
                    fileNumber = 'MANUAL-' + Date.now(); // Placeholder
                }
            }

            if (!fileNumber) {
                alert('Please select or enter a file number');
                return;
            }

            // Create new file object
            const newFile = {
                id: `FILE-${Date.now()}`,
                fileNumber: fileNumber,
                name: fileTitle,
                type: 'New File',
                source: 'Manual Entry',
                date: new Date().toISOString().split('T')[0],
                landUseType: document.querySelector('select').value || 'Residential',
                district: document.querySelectorAll('select')[1]?.value || 'Nasarawa',
                hasCofo: document.getElementById('has-cofo').checked,
            };

            // Add to pending files
            pendingFiles.push(newFile);

            // Re-render the list
            renderPendingFiles();
            updateCounters();
            updateSelectedFilesCount();

            // Close dialog
            closeNewFileDialog();

            // Show success message
            alert('File index created successfully!');
        }

        // Add AI indexing functionality
        if(startAiIndexingBtn){
            startAiIndexingBtn.addEventListener('click', startAiIndexing);
        }

        function startAiIndexing() {
            console.log('Starting AI indexing process');
            
            // Hide the start button and show processing view
            const indexingTab = document.getElementById('indexing-tab');
            const aiProcessingView = document.getElementById('ai-processing-view');
            
            if (indexingTab && aiProcessingView) {
                indexingTab.style.display = 'none';
                aiProcessingView.classList.remove('hidden');
            }

            // Start the AI processing simulation
            simulateAiProcessing();
        }

        function simulateAiProcessing() {
            let progress = 0;
            const stages = ['init', 'analyze', 'extract', 'categorize', 'validate', 'complete'];
            let currentStageIndex = 0;

            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 100) progress = 100;

                // Update progress bars
                if (progressBar) progressBar.style.width = progress + '%';
                if (progressPercentage) progressPercentage.textContent = Math.round(progress) + '%';
                if (pipelineProgressBar) pipelineProgressBar.style.width = progress + '%';
                if (pipelineProgressLine) pipelineProgressLine.style.width = progress + '%';
                if (pipelinePercentage) pipelinePercentage.textContent = Math.round(progress) + '% Complete';

                // Update pipeline stages
                const stageProgress = progress / 100 * stages.length;
                const newStageIndex = Math.floor(stageProgress);
                
                if (newStageIndex > currentStageIndex && newStageIndex < stages.length) {
                    // Mark previous stage as complete
                    const prevStage = document.getElementById(`stage-${stages[currentStageIndex]}`);
                    const prevLabel = prevStage?.nextElementSibling;
                    if (prevStage) {
                        prevStage.classList.remove('active');
                        prevStage.classList.add('complete');
                    }
                    if (prevLabel) {
                        prevLabel.classList.remove('active');
                        prevLabel.classList.add('complete');
                    }

                    // Mark current stage as active
                    currentStageIndex = newStageIndex;
                    const currentStage = document.getElementById(`stage-${stages[currentStageIndex]}`);
                    const currentLabel = currentStage?.nextElementSibling;
                    if (currentStage) {
                        currentStage.classList.remove('pending');
                        currentStage.classList.add('active');
                    }
                    if (currentLabel) {
                        currentLabel.classList.remove('pending');
                        currentLabel.classList.add('active');
                    }

                    // Update stage info
                    updateStageInfo(stages[currentStageIndex]);
                }

                if (progress >= 100) {
                    clearInterval(interval);
                    completeAiProcessing();
                }
            }, 500);
        }

        function updateStageInfo(stage) {
            const stageInfos = {
                'init': {
                    title: 'Initialization',
                    description: 'Setting up AI processing environment and preparing documents for analysis...'
                },
                'analyze': {
                    title: 'Document Analysis',
                    description: 'Analyzing document structure, layout, and identifying key sections...'
                },
                'extract': {
                    title: 'Information Extraction',
                    description: 'Extracting text, names, dates, and property details using OCR and NLP...'
                },
                'categorize': {
                    title: 'Content Categorization',
                    description: 'Categorizing document types and classifying extracted information...'
                },
                'validate': {
                    title: 'Data Validation',
                    description: 'Validating extracted data and checking for consistency...'
                },
                'complete': {
                    title: 'Processing Complete',
                    description: 'AI indexing completed successfully. Review and confirm results.'
                }
            };

            const info = stageInfos[stage];
            if (info && currentStageInfo) {
                currentStageInfo.innerHTML = `
                    <div class="p-2 bg-green-100 rounded-full">
                        <i data-lucide="loader" class="h-5 w-5 text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium mb-1">Current Stage: ${info.title}</p>
                        <p class="text-xs text-gray-600">${info.description}</p>
                    </div>
                `;
                lucide.createIcons();
            }
        }

        function completeAiProcessing() {
            console.log('AI processing completed');
            
            // Show the confirm button
            if (confirmSaveResultsBtn) {
                confirmSaveResultsBtn.classList.remove('hidden');
            }

            // Add AI insights
            if (aiInsightsContainer) {
                aiInsightsContainer.innerHTML = `
                    <div class="bg-green-50 p-4 rounded-md border border-green-100">
                        <h4 class="font-medium text-green-800 mb-2">AI Processing Results</h4>
                        <ul class="text-sm text-green-700 space-y-1">
                            <li>✓ ${selectedFiles.length} files processed successfully</li>
                            <li>✓ Metadata extracted and validated</li>
                            <li>✓ Document types identified</li>
                            <li>✓ Key information categorized</li>
                        </ul>
                    </div>
                `;
            }

            // Update final stage
            updateStageInfo('complete');
        }

        // Make functions available globally
        window.showNewFileDialog = showNewFileDialog;
        window.closeNewFileDialog = closeNewFileDialog;
        window.createNewFile = createNewFile;
    });
  </script>
