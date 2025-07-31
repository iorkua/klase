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
    });
  </script>
