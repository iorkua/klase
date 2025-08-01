<!-- Dynamic File Indexing JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Dynamic data from database
    let pendingFiles = [];
    let indexedFiles = @json($recentIndexes ?? []);
    let availableApplications = [];
    
    // State variables
    let selectedFiles = [];
    let indexingProgress = 0;
    let currentStage = "extract";
    
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
            selectedFiles = [];
        } else {
            selectedFiles = pendingFiles.map(file => file.id);
        }
        
        renderPendingFiles();
        updateSelectedFilesCount();
    }
    
    // Switch between tabs
    function switchTab(tabName) {
        tabs.forEach(t => {
            if (t.getAttribute('data-tab') === tabName) {
                t.classList.add('active');
            } else {
                t.classList.remove('active');
            }
        });

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
        if (!pendingFilesList) return;
        
        pendingFilesList.innerHTML = '';
        
        if (pendingFiles.length === 0) {
            pendingFilesList.innerHTML = `
                <div class="p-8 text-center text-gray-500">
                    <i data-lucide="inbox" class="h-12 w-12 mx-auto mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium mb-2">No pending files</p>
                    <p class="text-sm">All applications have been indexed or there are no applications available.</p>
                </div>
            `;
            lucide.createIcons();
            return;
        }
        
        pendingFiles.forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex justify-between items-center p-4 border-b hover:bg-gray-50';
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
        const pendingCountEl = document.getElementById('pending-files-count');
        const indexedCountEl = document.getElementById('indexed-files-count');
        
        if (pendingCountEl) pendingCountEl.textContent = pendingFiles.length;
        if (indexedCountEl) indexedCountEl.textContent = indexedFiles.length;
    }

    function updateSelectedFilesCount() {
        if (selectedFilesCount) {
            selectedFilesCount.textContent = `${selectedFiles.length} of ${pendingFiles.length} selected`;
        }
    }

    // Functions for New File Index Dialog
    function showNewFileDialog() {
        console.log('Opening New File Index Dialog');
        if (newFileDialogOverlay) {
            newFileDialogOverlay.classList.remove('hidden');
            loadAvailableApplications();
            lucide.createIcons();
        }
    }

    function closeNewFileDialog() {
        console.log('Closing New File Index Dialog');
        if (newFileDialogOverlay) {
            newFileDialogOverlay.classList.add('hidden');
        }
        const form = document.getElementById('new-file-form');
        if (form) {
            form.reset();
        }
    }

    function createNewFile() {
        console.log('Creating new file index');
        
        // Get form data
        const fileTitle = document.getElementById('file-title').value;
        const landUseSelect = document.querySelector('select');
        const landUseType = landUseSelect ? landUseSelect.value : 'residential';
        const plotNumber = document.querySelector('input[placeholder*="PL-"]')?.value || '';
        const districtSelect = document.querySelectorAll('select')[1];
        const district = districtSelect ? districtSelect.value : 'nasarawa';
        const lgaInput = document.querySelector('input[value="Kano Municipal"]');
        const lga = lgaInput ? lgaInput.value : 'Kano Municipal';
        
        if (!fileTitle.trim()) {
            alert('Please enter a file title');
            return;
        }

        // Get file number and application ID
        let fileNumber = '';
        let mainApplicationId = null;
        let fileNumberType = 'manual';
        
        const smartFilenoInput = document.getElementById('fileno');
        if (smartFilenoInput && smartFilenoInput.value) {
            fileNumber = smartFilenoInput.value;
            mainApplicationId = smartFilenoInput.dataset.applicationId;
            fileNumberType = 'application';
        } else {
            // Get from manual entry
            const manualFilenoInput = document.querySelector('input[name="manual_file_number"]');
            if (manualFilenoInput && manualFilenoInput.value) {
                fileNumber = manualFilenoInput.value;
            } else {
                fileNumber = 'MANUAL-' + Date.now();
            }
        }

        if (!fileNumber) {
            alert('Please select or enter a file number');
            return;
        }

        // Prepare form data
        const formData = {
            file_number_type: fileNumberType,
            main_application_id: mainApplicationId,
            file_number: fileNumber,
            file_title: fileTitle,
            land_use_type: landUseType,
            plot_number: plotNumber,
            district: district,
            lga: lga,
            has_cofo: document.getElementById('has-cofo')?.checked || false,
            is_merged: document.getElementById('merged-plot')?.checked || false,
            has_transaction: document.getElementById('has-transaction')?.checked || false,
            is_problematic: false,
            is_co_owned_plot: document.getElementById('co-owned-plot')?.checked || false,
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        };

        // Show loading state
        const createBtn = document.getElementById('create-file-btn');
        const originalText = createBtn.textContent;
        createBtn.textContent = 'Creating...';
        createBtn.disabled = true;

        // Send AJAX request
        fetch('{{ route("fileindexing.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': formData._token
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeNewFileDialog();
                alert(data.message);
                
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Error creating file index');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating file index. Please try again.');
        })
        .finally(() => {
            createBtn.textContent = originalText;
            createBtn.disabled = false;
        });
    }

    // Load available applications for file number selection
    function loadAvailableApplications(search = '') {
        fetch(`{{ route("fileindexing.search-applications") }}?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                availableApplications = data.applications;
                populateApplicationDropdown();
            }
        })
        .catch(error => {
            console.error('Error loading applications:', error);
        });
    }

    // Populate application dropdown
    function populateApplicationDropdown() {
        const dropdown = document.getElementById('application-dropdown');
        if (!dropdown) return;

        dropdown.innerHTML = '<option value="">Select an application...</option>';
        
        availableApplications.forEach(app => {
            const option = document.createElement('option');
            option.value = app.id;
            option.textContent = `${app.file_number} - ${app.applicant_name}`;
            option.dataset.fileNumber = app.file_number;
            option.dataset.landUse = app.land_use;
            option.dataset.plotNumber = app.plot_number;
            option.dataset.district = app.district;
            option.dataset.lga = app.lga;
            dropdown.appendChild(option);
        });
    }

    // Load pending files (applications without file indexing)
    function loadPendingFiles() {
        fetch('{{ route("fileindexing.search-applications") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pendingFiles = data.applications.map(app => ({
                    id: app.id,
                    fileNumber: app.file_number,
                    name: app.applicant_name,
                    type: '',
                    source: '',
                    date: new Date().toISOString().split('T')[0],
                    landUseType: app.land_use || 'Residential',
                    district: app.district || '',
                    hasCofo: false
                }));
                
                renderPendingFiles();
                updateCounters();
                updateSelectedFilesCount();
            }
        })
        .catch(error => {
            console.error('Error loading pending files:', error);
        });
    }

    // AI Processing functions
    function startAiIndexing() {
        console.log('Starting AI indexing process');
        
        const indexingTab = document.getElementById('indexing-tab');
        const aiProcessingView = document.getElementById('ai-processing-view');
        
        if (indexingTab && aiProcessingView) {
            indexingTab.style.display = 'none';
            aiProcessingView.classList.remove('hidden');
        }

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
        
        if (confirmSaveResultsBtn) {
            confirmSaveResultsBtn.classList.remove('hidden');
        }

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

        updateStageInfo('complete');
    }

    // Function to confirm and save AI indexing results
    function confirmAndSaveResults() {
        console.log('Confirming and saving AI indexing results');
        
        if (selectedFiles.length === 0) {
            alert('No files selected for indexing');
            return;
        }

        // Show loading state
        const originalText = confirmSaveResultsBtn.textContent;
        confirmSaveResultsBtn.textContent = 'Saving...';
        confirmSaveResultsBtn.disabled = true;

        // Prepare the data for saving - create file indexes for selected applications
        const promises = selectedFiles.map(fileId => {
            const file = pendingFiles.find(f => f.id === fileId);
            if (!file) return Promise.resolve();

            const formData = {
                file_number_type: 'application',
                main_application_id: file.id,
                file_number: file.fileNumber,
                file_title: file.name,
                land_use_type: file.landUseType,
                plot_number: '',
                district: file.district,
                lga: 'Kano Municipal',
                has_cofo: file.hasCofo,
                is_merged: false,
                has_transaction: false,
                is_problematic: false,
                is_co_owned_plot: false,
                _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            };

            return fetch('{{ route("fileindexing.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formData._token
                },
                body: JSON.stringify(formData)
            });
        });

        // Execute all file creation requests
        Promise.all(promises)
        .then(responses => {
            // Check if all requests were successful
            const allSuccessful = responses.every(response => response && response.ok);
            
            if (allSuccessful) {
                // Show success message
                alert(`${selectedFiles.length} files indexed successfully!`);
                
                // Update the UI
                updateIndexedFilesCount();
                
                // Switch to indexed files tab to show results
                switchTab('indexed');
                
                // Reload the page to refresh data
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert('Some files could not be indexed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error saving indexing results:', error);
            alert('Error saving indexing results. Please try again.');
        })
        .finally(() => {
            confirmSaveResultsBtn.textContent = originalText;
            confirmSaveResultsBtn.disabled = false;
        });
    }

    // Function to update indexed files count
    function updateIndexedFilesCount() {
        const indexedCountEl = document.getElementById('indexed-files-count');
        if (indexedCountEl) {
            const currentCount = parseInt(indexedCountEl.textContent) || 0;
            indexedCountEl.textContent = currentCount + selectedFiles.length;
        }
    }

    // Initialize the page
    console.log("Initializing Dynamic File Indexing Assistant");

    // Make sure File Index tab is active by default
    switchTab('pending');

    // Load pending files on page load
    loadPendingFiles();

    // Add event listeners
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.getAttribute('data-tab');
            switchTab(tabName);
        });
    });

    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('click', toggleSelectAll);
    }

    if (beginIndexingBtn) {
        beginIndexingBtn.addEventListener('click', () => {
            if (selectedFiles.length > 0) {
                const titleElement = document.querySelector('#indexing-tab .card h3');
                if (titleElement) {
                    titleElement.textContent = `AI Indexing: ${selectedFiles.length} Files`;
                }

                const messageElement = document.querySelector('#indexing-tab .card p.mb-6');
                if (messageElement) {
                    messageElement.textContent = `Ready to begin AI-powered indexing for ${selectedFiles.length} selected files.`;
                }

                switchTab('indexing');
            } else {
                alert("Please select at least one file to begin indexing.");
            }
        });
    }

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

    if (startAiIndexingBtn) {
        startAiIndexingBtn.addEventListener('click', startAiIndexing);
    }

    if (confirmSaveResultsBtn) {
        confirmSaveResultsBtn.addEventListener('click', confirmAndSaveResults);
    }

    // Make functions available globally
    window.showNewFileDialog = showNewFileDialog;
    window.closeNewFileDialog = closeNewFileDialog;
    window.createNewFile = createNewFile;
    window.confirmAndSaveResults = confirmAndSaveResults;
});
</script>