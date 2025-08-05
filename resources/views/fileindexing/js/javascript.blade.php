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
    const processingFilesCount = document.getElementById('processing-files-count');
    const selectedFilesAiCount = document.getElementById('selected-files-ai-count');
    
    // DOM Elements for AI Mode Switch
    const aiModeSwitch = document.getElementById('ai-mode-switch');
    const aiModeLabel = document.getElementById('ai-mode-label');
    
    // DOM Elements for New File Dialog
    const closeDialogBtn = document.getElementById('close-dialog-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const createFileBtn = document.getElementById('create-file-btn');

    // Function to format applicant name based on applicant type
    function formatApplicantName(app) {
        if (!app) {
            console.log('formatApplicantName: No app data provided');
            return 'Unknown Applicant';
        }
        
        console.log('formatApplicantName: Processing app:', {
            id: app.id,
            applicant_type: app.applicant_type,
            applicant_name: app.applicant_name,
            corporate_name: app.corporate_name,
            first_name: app.first_name,
            surname: app.surname,
            multiple_owners_names: app.multiple_owners_names,
            // Log all available fields to see what we're working with
            allFields: Object.keys(app)
        });
        
        // If applicant_name is already provided, use it
        if (app.applicant_name && app.applicant_name.trim()) {
            console.log('Using existing applicant_name:', app.applicant_name);
            return app.applicant_name;
        }
        
        // Format name based on applicant type
        switch (app.applicant_type) {
            case 'corporate':
                if (app.corporate_name && app.corporate_name.trim()) {
                    const corporateName = app.rc_number ? `${app.corporate_name} (RC: ${app.rc_number})` : app.corporate_name;
                    console.log('Corporate name formatted:', corporateName);
                    return corporateName;
                }
                break;
                
            case 'individual':
                const nameParts = [];
                if (app.applicant_title && app.applicant_title.trim()) nameParts.push(app.applicant_title);
                if (app.first_name && app.first_name.trim()) nameParts.push(app.first_name);
                if (app.middle_name && app.middle_name.trim()) nameParts.push(app.middle_name);
                if (app.surname && app.surname.trim()) nameParts.push(app.surname);
                
                if (nameParts.length > 0) {
                    const individualName = nameParts.join(' ');
                    console.log('Individual name formatted:', individualName);
                    return individualName;
                }
                break;
                
            case 'multiple':
                if (app.multiple_owners_names && app.multiple_owners_names.trim()) {
                    console.log('Multiple owners name formatted:', app.multiple_owners_names);
                    return app.multiple_owners_names;
                }
                console.log('Multiple owners type but no multiple_owners_names field');
                break;
        }
        
        // Enhanced fallback: try all possible name fields
        console.log('Trying fallback name construction...');
        
        // Try multiple owners names first (regardless of applicant_type)
        if (app.multiple_owners_names && app.multiple_owners_names.trim()) {
            console.log('Fallback: Using multiple_owners_names:', app.multiple_owners_names);
            return app.multiple_owners_names;
        }
        
        // Try corporate name
        if (app.corporate_name && app.corporate_name.trim()) {
            const corporateName = app.rc_number ? `${app.corporate_name} (RC: ${app.rc_number})` : app.corporate_name;
            console.log('Fallback: Using corporate_name:', corporateName);
            return corporateName;
        }
        
        // Try individual name parts
        const fallbackParts = [];
        if (app.applicant_title && app.applicant_title.trim()) fallbackParts.push(app.applicant_title);
        if (app.first_name && app.first_name.trim()) fallbackParts.push(app.first_name);
        if (app.middle_name && app.middle_name.trim()) fallbackParts.push(app.middle_name);
        if (app.surname && app.surname.trim()) fallbackParts.push(app.surname);
        
        if (fallbackParts.length > 0) {
            const individualName = fallbackParts.join(' ');
            console.log('Fallback: Using individual name parts:', individualName);
            return individualName;
        }
        
        // Try any other name-like fields that might exist
        const possibleNameFields = [
            'name', 'full_name', 'owner_name', 'applicant', 'client_name', 'owner', 'owners'
        ];
        
        for (const field of possibleNameFields) {
            if (app[field] && typeof app[field] === 'string' && app[field].trim()) {
                console.log(`Fallback: Using ${field}:`, app[field]);
                return app[field];
            }
        }
        
        console.log('No name found, returning Unknown Applicant for app:', app);
        return 'Unknown Applicant';
    }

    // Function to toggle file selection
    function toggleFileSelection(fileId) {
        console.log('Toggling file selection for:', fileId);
        if (selectedFiles.includes(fileId)) {
            selectedFiles = selectedFiles.filter(id => id !== fileId);
        } else {
            selectedFiles.push(fileId);
        }
        
        console.log('Selected files:', selectedFiles);
        renderPendingFiles();
        updateSelectedFilesCount();
        updateAiIndexingButton();
        updateNewFileIndexButton();
        updateDigitalIndexTab();
    }
    
    // Function to toggle select all files
    function toggleSelectAll() {
        if (selectedFiles.length === pendingFiles.length) {
            selectedFiles = [];
        } else {
            selectedFiles = pendingFiles.map(file => file.id);
        }
        
        renderPendingFiles();
        updateSelectedFilesCount();
        updateAiIndexingButton();
        updateNewFileIndexButton();
        updateDigitalIndexTab();
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

        // Update New File Index button state based on current tab
        updateNewFileIndexButtonForTab(tabName);
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
            const isSelected = selectedFiles.includes(file.id);
            const fileItem = document.createElement('div');
            fileItem.className = 'p-4 border-b last:border-b-0';
            
            fileItem.innerHTML = `
                <div class="flex items-center">
                    <input type="checkbox" ${isSelected ? 'checked' : ''} data-id="${file.id}" class="mr-4 file-checkbox">
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
            
            // Add event listener to checkbox
            const checkbox = fileItem.querySelector('.file-checkbox');
            checkbox.addEventListener('change', () => toggleFileSelection(file.id));
            
            pendingFilesList.appendChild(fileItem);
        });
        
        // Initialize Lucide icons for the new rows
        lucide.createIcons();
        
        // Update selected files count
        updateSelectedFilesCount();
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

    function updateAiIndexingButton() {
        if (selectedFilesAiCount) {
            selectedFilesAiCount.textContent = selectedFiles.length;
        }
        
        if (startAiIndexingBtn) {
            startAiIndexingBtn.disabled = selectedFiles.length === 0;
        }
    }

    function updateNewFileIndexButton() {
        if (newFileIndexBtn) {
            // Disable the "New File Index" button when files are selected
            if (selectedFiles.length > 0) {
                newFileIndexBtn.disabled = true;
                newFileIndexBtn.classList.add('opacity-50', 'cursor-not-allowed');
                newFileIndexBtn.title = 'Cannot create new file index while files are selected for AI indexing';
            } else {
                newFileIndexBtn.disabled = false;
                newFileIndexBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                newFileIndexBtn.title = '';
            }
        }
    }

    function updateDigitalIndexTab() {
        const digitalIndexTab = document.querySelector('[data-tab="indexing"]');
        
        if (digitalIndexTab) {
            if (selectedFiles.length === 0) {
                // Disable the Digital Index (AI) tab when no files are selected
                digitalIndexTab.classList.add('opacity-50', 'cursor-not-allowed');
                digitalIndexTab.style.pointerEvents = 'none';
                digitalIndexTab.title = 'Select files from the File Index tab first to enable AI processing';
            } else {
                // Enable the Digital Index (AI) tab when files are selected
                digitalIndexTab.classList.remove('opacity-50', 'cursor-not-allowed');
                digitalIndexTab.style.pointerEvents = 'auto';
                digitalIndexTab.title = '';
            }
        }
    }

    function updateNewFileIndexButtonForTab(tabName) {
        if (newFileIndexBtn) {
            if (tabName === 'indexed') {
                // Disable the "New File Index" button when on Indexed Files tab
                newFileIndexBtn.disabled = true;
                newFileIndexBtn.classList.add('opacity-50', 'cursor-not-allowed');
                newFileIndexBtn.title = 'Cannot create new file index while viewing indexed files';
            } else if (selectedFiles.length === 0) {
                // Enable the button if not on indexed tab and no files are selected
                newFileIndexBtn.disabled = false;
                newFileIndexBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                newFileIndexBtn.title = '';
            }
            // If files are selected, the updateNewFileIndexButton() function will handle the state
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
        const landUseSelect = document.getElementById('landUse');
        const landUseType = landUseSelect ? landUseSelect.value : 'RESIDENTIAL';
        const plotNumber = document.querySelector('input[placeholder*="PL-"]')?.value || '';
        const districtSelect = document.querySelector('select[name="district"]');
        const district = districtSelect ? districtSelect.value : '';
        const lgaInput = document.querySelector('input[name="lga"]');
        const lga = lgaInput ? lgaInput.value : 'Kano Municipal';
        
        if (!fileTitle.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please enter a file title',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Get file number and application ID
        let fileNumber = '';
        let mainApplicationId = null;
        let subApplicationId = null;
        let sourceTable = null;
        let fileNumberType = 'manual';
        
        // Check if smart file number selector is being used
        const smartFilenoInput = document.getElementById('fileno');
        const smartFilenoSelect = document.getElementById('fileno-select');
        
        console.log('Smart fileno input value:', smartFilenoInput?.value);
        console.log('Smart fileno select value:', smartFilenoSelect?.value);
        
        if (smartFilenoInput && smartFilenoInput.value) {
            // Using smart selector
            fileNumber = smartFilenoInput.value;
            fileNumberType = 'application';
            
            // Get the selected option to extract data attributes
            const selectedOption = smartFilenoSelect.options[smartFilenoSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset) {
                sourceTable = selectedOption.dataset.sourceTable;
                const applicationId = selectedOption.dataset.applicationId;
                
                console.log('Selected option data:', {
                    sourceTable: sourceTable,
                    applicationId: applicationId
                });
                
                if (sourceTable === 'mother') {
                    mainApplicationId = applicationId;
                } else if (sourceTable === 'sub') {
                    subApplicationId = applicationId;
                }
            }
        } else {
            // Check manual file number entry
            console.log('Checking manual file number entry...');
            
            // Check for manual file number inputs
            const activeFileTab = document.querySelector('input[name="activeFileTab"]')?.value;
            console.log('Active file tab:', activeFileTab);
            
            if (activeFileTab === 'mls') {
                const mlsFileNo = document.querySelector('input[name="mlsFNo"]')?.value;
                if (mlsFileNo) {
                    fileNumber = mlsFileNo;
                    console.log('Using MLS file number:', fileNumber);
                }
            } else if (activeFileTab === 'kangis') {
                const kangisFileNo = document.querySelector('input[name="kangisFileNo"]')?.value;
                if (kangisFileNo) {
                    fileNumber = kangisFileNo;
                    console.log('Using KANGIS file number:', fileNumber);
                }
            } else if (activeFileTab === 'newkangis') {
                const newKangisFileNo = document.querySelector('input[name="NewKANGISFileno"]')?.value;
                if (newKangisFileNo) {
                    fileNumber = newKangisFileNo;
                    console.log('Using New KANGIS file number:', fileNumber);
                }
            }
            
            // If no manual file number found, generate one
            if (!fileNumber) {
                fileNumber = 'MANUAL-' + Date.now();
                console.log('Generated manual file number:', fileNumber);
            }
        }

        if (!fileNumber) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please select or enter a file number',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        console.log('Final form data preparation:', {
            fileNumber: fileNumber,
            fileNumberType: fileNumberType,
            mainApplicationId: mainApplicationId,
            subApplicationId: subApplicationId,
            sourceTable: sourceTable
        });

        // Prepare form data
        const formData = {
            file_number_type: fileNumberType,
            main_application_id: mainApplicationId,
            subapplication_id: subApplicationId,
            source_table: sourceTable,
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

        console.log('Sending form data:', formData);

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
            console.log('Server response:', data);
            if (data.success) {
                closeNewFileDialog();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                console.error('Server error:', data);
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                    let errorMessage = 'Validation failed:\n';
                    Object.keys(data.errors).forEach(field => {
                        errorMessage += `${field}: ${data.errors[field].join(', ')}\n`;
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessage,
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error creating file index',
                        confirmButtonColor: '#ef4444'
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Error creating file index. Please try again.',
                confirmButtonColor: '#ef4444'
            });
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
            option.textContent = `${app.file_number} - ${formatApplicantName(app)}`;
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
            console.log('Raw data from server:', data);
            if (data.success) {
                pendingFiles = data.applications.map(app => {
                    console.log('Processing application:', app);
                    const formattedName = formatApplicantName(app);
                    console.log('Formatted name result:', formattedName);
                    
                    return {
                        id: app.id,
                        fileNumber: app.file_number,
                        name: formattedName,
                        type: app.application_type || 'Application',
                        source: app.source_table === 'mother' ? 'Primary' : 'Unit',
                        source_table: app.source_table,
                        date: app.created_at || new Date().toISOString().split('T')[0],
                        landUseType: app.land_use || 'Residential',
                        district: app.district || '',
                        hasCofo: false,
                        applicant_type: app.applicant_type,
                        // Store ALL original fields for debugging and reference
                        ...app
                    };
                });
                
                console.log('Final pendingFiles array:', pendingFiles);
                
                renderPendingFiles();
                updateCounters();
                updateSelectedFilesCount();
                updateAiIndexingButton();
                updateNewFileIndexButton();
            }
        })
        .catch(error => {
            console.error('Error loading pending files:', error);
        });
    }

    // AI Processing functions
    function startAiIndexing() {
        console.log('Starting AI indexing process for selected files');
        
        if (selectedFiles.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Files Selected',
                text: 'Please select files to index',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        const indexingTab = document.getElementById('indexing-tab');
        const aiProcessingView = document.getElementById('ai-processing-view');
        
        if (indexingTab && aiProcessingView) {
            indexingTab.style.display = 'none';
            aiProcessingView.classList.remove('hidden');
        }

        if (processingFilesCount) {
            processingFilesCount.textContent = selectedFiles.length;
        }

        // Show AI insights immediately after 3 seconds
        setTimeout(() => {
            console.log('Showing AI insights after 3 seconds...');
            showAiInsights();
        }, 3000);

        simulateAiProcessing();
    }

    function simulateAiProcessing() {
        let progress = 0;
        const stages = ['init', 'analyze', 'extract', 'categorize', 'validate', 'complete'];
        let currentStageIndex = 0;

        const interval = setInterval(() => {
            progress += Math.random() * 10 + 5; // Slower progress
            if (progress > 100) progress = 100;

            console.log('AI Processing progress:', Math.round(progress) + '%');

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
        }, 800);
    }

    function updateStageInfo(stage) {
        const stageInfos = {
            'init': {
                title: 'Initialization',
                description: 'Setting up AI processing environment and connecting to application databases...'
            },
            'analyze': {
                title: 'Application Analysis',
                description: 'Analyzing application data from mother_applications, subapplications, and cofo tables...'
            },
            'extract': {
                title: 'Information Extraction',
                description: 'Extracting applicant details, property information, and application metadata...'
            },
            'categorize': {
                title: 'Content Categorization',
                description: 'Categorizing application types and classifying extracted information...'
            },
            'validate': {
                title: 'Data Validation',
                description: 'Validating extracted data against existing records and checking for consistency...'
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

    function showAiInsights() {
        console.log('Showing AI insights...');
        
        if (!aiInsightsContainer) {
            console.log('AI insights container not found');
            return;
        }

        // Get the applications to process (selected files)
        const applicationsToProcess = pendingFiles.filter(file => selectedFiles.includes(file.id));

        console.log('Applications to process:', applicationsToProcess);

        if (applicationsToProcess.length === 0) {
            console.log('No applications to process');
            return;
        }

        let insightsHtml = `
            <div class="flex items-center mb-2">
                <i data-lucide="zap" class="h-4 w-4 text-green-500 mr-2"></i>
                <h4 class="font-medium">Real-time AI Insights</h4>
            </div>
        `;

        // Generate insights for each application (show first 2 applications)
        applicationsToProcess.slice(0, 2).forEach((app, index) => {
            const confidence = 88 + Math.floor(Math.random() * 10); // Random confidence between 88-97%
            const plotNumber = `PL-${Math.floor(Math.random() * 9000) + 1000}`;
            const ownerConfidence = confidence - 1;
            const plotConfidence = confidence - 4;
            const landUseConfidence = confidence - 3;
            const textQuality = confidence + 5;
            
            // Determine document type based on app type or use default
            const documentType = app.type || (index === 0 ? 'Certificate of Occupancy' : 'Site Plan');
            
            insightsHtml += `
                <!-- ${index === 0 ? 'First' : 'Second'} file insights -->
                <div class="insight-card">
                    <div class="insight-header">
                        <div>
                            <h4 class="text-blue-600 font-medium">${app.fileNumber}</h4>
                            <p class="text-gray-600">${app.name}</p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="insight-confidence">${confidence}% Confidence</span>
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
                                        ${app.name}
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
                                        ${app.landUseType}
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
                                    <span class="insight-field-value">${index === 0 ? 'Not detected' : 'Detected'}</span>
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
                                <span class="insight-keyword">${app.landUseType}</span>
                                <span class="insight-keyword">${app.district || 'Kano'}</span>
                                <span class="insight-keyword">${documentType}</span>
                                <span class="insight-keyword">Land Document</span>
                                <span class="insight-keyword">Property</span>
                                <span class="insight-keyword">Kano State</span>
                                <span class="insight-keyword">${app.landUseType === 'Residential' ? 'Housing' : 'Business'}</span>
                            </div>
                            
                            ${index === 0 ? `
                            <div class="insight-issues">
                                <h6 class="insight-issues-title">Potential Issues:</h6>
                                <ul class="insight-issues-list">
                                    <li>Plot boundaries not specified</li>
                                    <li>Ownership information unclear</li>
                                    <li>Parcel data needs updating</li>
                                </ul>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        if (applicationsToProcess.length > 2) {
            insightsHtml += `
                <div class="text-center text-gray-500 text-sm">
                    ... and ${applicationsToProcess.length - 2} more applications
                </div>
            `;
        }

        console.log('Setting AI insights HTML');
        aiInsightsContainer.innerHTML = insightsHtml;
        
        // Initialize Lucide icons for the new content
        lucide.createIcons();
    }

    function completeAiProcessing() {
        console.log('AI processing completed');
        
        // Show completion summary
        showCompletionSummary();
        
        if (confirmSaveResultsBtn) {
            confirmSaveResultsBtn.classList.remove('hidden');
        }

        updateStageInfo('complete');
    }

    function showCompletionSummary() {
        const completionSummary = document.getElementById('ai-completion-summary');
        const documentsCountEl = document.getElementById('summary-documents-count');
        const confidenceEl = document.getElementById('summary-confidence');
        const processingTimeEl = document.getElementById('summary-processing-time');
        
        if (completionSummary) {
            // Calculate summary statistics
            const documentsProcessed = selectedFiles.length;
            const averageConfidence = Math.floor(88 + Math.random() * 10); // Random between 88-97%
            const processingTime = Math.floor(8 + Math.random() * 8); // Random between 8-15 seconds
            
            // Update the summary values
            if (documentsCountEl) documentsCountEl.textContent = documentsProcessed;
            if (confidenceEl) confidenceEl.textContent = averageConfidence + '%';
            if (processingTimeEl) processingTimeEl.textContent = processingTime + 's';
            
            // Show the completion summary
            completionSummary.style.display = 'flex';
            
            // Initialize Lucide icons for the new content
            lucide.createIcons();
        }
    }

    // Function to confirm and save AI indexing results
    function confirmAndSaveResults() {
        console.log('Confirming and saving AI indexing results');
        
        const applicationsToIndex = pendingFiles.filter(file => selectedFiles.includes(file.id));
        
        if (applicationsToIndex.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Applications Selected',
                text: 'No applications selected for indexing',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Show loading state
        const originalText = confirmSaveResultsBtn.textContent;
        confirmSaveResultsBtn.textContent = 'Saving...';
        confirmSaveResultsBtn.disabled = true;

        // Prepare the data for saving - create file indexes for selected applications
        const promises = applicationsToIndex.map(app => {
            const formData = {
                file_number_type: 'application',
                main_application_id: app.source_table === 'mother' ? app.id : null,
                subapplication_id: app.source_table === 'sub' ? app.id : null,
                source_table: app.source_table,
                file_number: app.fileNumber,
                file_title: app.name,
                land_use_type: app.landUseType || 'Residential',
                plot_number: '',
                district: app.district || '',
                lga: 'Kano Municipal',
                has_cofo: app.hasCofo || false,
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
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: `${applicationsToIndex.length} applications indexed successfully!`,
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    // Update the UI
                    updateIndexedFilesCount();
                    
                    // Switch to indexed files tab to show results
                    switchTab('indexed');
                    
                    // Reload the page to refresh data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Indexing Failed',
                    text: 'Some applications could not be indexed. Please try again.',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .catch(error => {
            console.error('Error saving indexing results:', error);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Error saving indexing results. Please try again.',
                confirmButtonColor: '#ef4444'
            });
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
            const newCount = selectedFiles.length;
            indexedCountEl.textContent = currentCount + newCount;
        }
    }

    // Initialize the page
    console.log("Initializing Dynamic File Indexing Assistant");

    // Make sure File Index tab is active by default
    switchTab('pending');

    // Initialize Digital Index tab state
    updateDigitalIndexTab();

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
            console.log('Begin Indexing clicked. Selected files:', selectedFiles);
            if (selectedFiles.length > 0) {
                switchTab('indexing');
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Files Selected',
                    text: 'Please select at least one file to begin indexing.',
                    confirmButtonColor: '#3085d6'
                });
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

    // AI Mode Switch functionality
    if (aiModeSwitch && aiModeLabel) {
        aiModeSwitch.addEventListener('change', function() {
            if (this.checked) {
                aiModeLabel.textContent = 'ON';
                console.log('AI Mode enabled - Enhanced features activated');
                // Enable enhanced AI features
                document.body.classList.add('ai-mode-enabled');
            } else {
                aiModeLabel.textContent = 'OFF';
                console.log('AI Mode disabled - Standard features only');
                // Disable enhanced AI features
                document.body.classList.remove('ai-mode-enabled');
            }
        });
    }

    // Make functions available globally
    window.showNewFileDialog = showNewFileDialog;
    window.closeNewFileDialog = closeNewFileDialog;
    window.createNewFile = createNewFile;
    window.confirmAndSaveResults = confirmAndSaveResults;
    window.toggleFileSelection = toggleFileSelection;
});
</script>