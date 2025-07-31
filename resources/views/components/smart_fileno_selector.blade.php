<!-- Smart File Number Selector Component -->
<div class="smart-fileno-selector">
    <!-- Hidden input for the main fileno field that gets submitted -->
    <input type="hidden" id="fileno" name="fileno" value="">
    
    <div class="flex items-center justify-between mb-3">
        <label for="fileno-select" class="block text-sm font-medium text-gray-700">Select File Number</label>
        <button type="button" id="toggle-manual-entry" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Enter Fileno manually
        </button>
    </div>
    
    <!-- Dropdown Selection Mode -->
    <div id="dropdown-mode" class="fileno-mode">
        <select id="fileno-select" class="w-full p-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">-- Select File Number --</option>
            @php
                $ctApplications = DB::connection('sqlsrv')
                    ->select("SELECT [fileno], [applicant_title], [first_name], [surname], [corporate_name], [rc_number], [multiple_owners_names] FROM [klas].[dbo].[mother_applications]");
            @endphp
            @foreach($ctApplications as $application)
                <option value="{{ $application->fileno }}" 
                        data-fileno="{{ $application->fileno }}"
                        data-applicant-title="{{ $application->applicant_title ?? '' }}"
                        data-first-name="{{ $application->first_name ?? '' }}"
                        data-surname="{{ $application->surname ?? '' }}"
                        data-corporate-name="{{ $application->corporate_name ?? '' }}"
                        data-rc-number="{{ $application->rc_number ?? '' }}"
                        data-multiple-owners="{{ $application->multiple_owners_names ?? '' }}">
                    {{ $application->fileno }} - 
                    @if($application->corporate_name)
                        {{ $application->corporate_name }}
                    @else
                        {{ $application->applicant_title ?? '' }} {{ $application->first_name ?? '' }} {{ $application->surname ?? '' }}
                    @endif
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Can't find your file number? <button type="button" class="text-blue-600 hover:underline" onclick="toggleFilenoMode()">Enter it manually</button></p>
        
        <!-- Selected File Number Display (in dropdown mode) -->
        <div id="selected-fileno-display" class="hidden mt-3">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-green-800 mb-1">Selected File Number</h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-bold text-green-900 font-mono bg-white px-3 py-1 rounded border border-green-200" id="selected-fileno-text"></span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Ready to use
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" id="clear-selection" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Manual Entry Mode -->
    <div id="manual-mode" class="fileno-mode hidden" style="display: none;">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 w-full">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-blue-800">Enter File Number Information</h4>
                </div>
                <button type="button" id="back-to-dropdown" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-white border border-blue-300 rounded-md hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to dropdown
                </button>
            </div>
            
            <!-- Include the File Number Information component -->
            <div class="bg-white rounded-lg p-4 border border-blue-100">
                @include('primaryform.gis_fileno')
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" id="confirm-manual-entry" class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Use This File Number
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.smart-fileno-selector .fileno-mode {
    transition: all 0.3s ease;
}

.smart-fileno-selector .fileno-mode.hidden {
    display: none !important;
}

/* Ensure manual mode is completely hidden by default */
.smart-fileno-selector #manual-mode {
    display: none !important;
}

.smart-fileno-selector #manual-mode:not(.hidden) {
    display: block !important;
}

/* Override any conflicting styles from the included component */
.smart-fileno-selector #manual-mode.hidden,
.smart-fileno-selector #manual-mode.hidden * {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeSmartFilenoSelector();
});

function initializeSmartFilenoSelector() {
    const dropdownMode = document.getElementById('dropdown-mode');
    const manualMode = document.getElementById('manual-mode');
    const toggleManualBtn = document.getElementById('toggle-manual-entry');
    const backToDropdownBtn = document.getElementById('back-to-dropdown');
    const confirmManualBtn = document.getElementById('confirm-manual-entry');
    const clearSelectionBtn = document.getElementById('clear-selection');
    const selectedDisplay = document.getElementById('selected-fileno-display');
    const selectedText = document.getElementById('selected-fileno-text');
    const filenoSelect = document.getElementById('fileno-select');
    const filenoInput = document.getElementById('fileno'); // Main fileno hidden input
    
    // Ensure manual mode is hidden on initialization
    if (manualMode) {
        manualMode.style.display = 'none';
        manualMode.classList.add('hidden');
    }
    
    // Toggle between dropdown and manual modes
    function toggleFilenoMode() {
        if (dropdownMode.classList.contains('hidden')) {
            // Switch to dropdown mode
            dropdownMode.classList.remove('hidden');
            dropdownMode.style.display = 'block';
            manualMode.classList.add('hidden');
            manualMode.style.display = 'none';
            toggleManualBtn.innerHTML = `
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Enter Fileno manually
            `;
        } else {
            // Switch to manual mode
            dropdownMode.classList.add('hidden');
            dropdownMode.style.display = 'none';
            manualMode.classList.remove('hidden');
            manualMode.style.display = 'block';
            toggleManualBtn.innerHTML = `
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                Use dropdown
            `;
            
            // Enable file number inputs when switching to manual mode
            enableFileNumberInputs();
        }
    }
    
    // Event listeners
    if (toggleManualBtn) toggleManualBtn.addEventListener('click', toggleFilenoMode);
    if (backToDropdownBtn) backToDropdownBtn.addEventListener('click', toggleFilenoMode);
    
    // Confirm manual entry
    if (confirmManualBtn) {
        confirmManualBtn.addEventListener('click', function() {
            const activeTabEl = document.getElementById('activeFileTab');
            if (!activeTabEl) return;
            
            const activeTab = activeTabEl.value;
            let fileNumber = '';
            
            // Get the file number based on active tab
            if (activeTab === 'mlsFNo') {
                const mlsEl = document.getElementById('mlsFNo');
                fileNumber = mlsEl ? mlsEl.value : '';
            } else if (activeTab === 'kangisFileNo') {
                const kangisEl = document.getElementById('kangisFileNo');
                fileNumber = kangisEl ? kangisEl.value : '';
            } else if (activeTab === 'NewKANGISFileno') {
                const newKangisEl = document.getElementById('NewKANGISFileno');
                fileNumber = newKangisEl ? newKangisEl.value : '';
            }
            
            if (fileNumber.trim()) {
                // Set the main fileno field
                if (filenoInput) filenoInput.value = fileNumber;
                
                // Create a mock application object for manual entry
                const manualApplication = {
                    id: 'manual_' + Date.now(),
                    fileno: fileNumber,
                    applicant_type: 'manual',
                    first_name: 'Manual',
                    surname: 'Entry',
                    isManual: true
                };
                
                // Show selected file number
                if (selectedText) selectedText.textContent = fileNumber;
                if (selectedDisplay) selectedDisplay.classList.remove('hidden');
                
                // Switch back to dropdown mode
                toggleFilenoMode();
                
                // Trigger the same logic as dropdown selection
                handleFilenoSelection(manualApplication);
                
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'File Number Set',
                        text: `File number "${fileNumber}" has been set. You can now enter GIS data.`,
                        icon: 'success',
                        confirmButtonText: 'Continue'
                    });
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Invalid File Number',
                        text: 'Please enter a valid file number.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    }
    
    // Clear selection
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            if (selectedDisplay) selectedDisplay.classList.add('hidden');
            if (selectedText) selectedText.textContent = '';
            if (filenoInput) filenoInput.value = ''; // Clear main fileno field
            
            // Clear form and disable inputs
            clearFormAndDisableInputs();
            
            // Clear dropdown selection
            if (typeof $ !== 'undefined' && filenoSelect) {
                $(filenoSelect).val(null).trigger('change');
            }
            
            // Reset manual entry form
            resetManualEntryForm();
        });
    }
    
    // Function to handle file number selection (both dropdown and manual)
    function handleFilenoSelection(application) {
        // Store the selected application globally
        window.selectedApplication = application;
        
        // Set the main fileno field
        if (filenoInput) filenoInput.value = application.fileno;
        
        // Populate hidden fields based on survey type
        const isSecondary = '{{ request()->query('is') }}' === 'secondary';
        
        const appIdEl = document.getElementById('application_id');
        const subAppIdEl = document.getElementById('sub_application_id');
        
        if (isSecondary) {
            if (subAppIdEl) subAppIdEl.value = application.id;
            if (appIdEl) appIdEl.value = '';
            application.isSecondary = true;
            
            // Auto-populate unit information fields if available
            if (typeof populateUnitInformation === 'function') {
                populateUnitInformation(application);
            }
        } else {
            if (appIdEl) appIdEl.value = application.id;
            if (subAppIdEl) subAppIdEl.value = '';
            application.isSecondary = false;
        }
        
        // Enable all form inputs
        enableFormInputs();
        
        // Render application header if function exists
        if (typeof renderApplicationHeader === 'function') {
            renderApplicationHeader(application);
        }
    }
    
    // Function to enable form inputs
    function enableFormInputs() {
        // Try different form selectors since we're in GIS record form
        const formSelectors = [
            '#update-survey-form input:not([type="hidden"]):not([type="submit"])',
            'form input:not([type="hidden"]):not([type="submit"])',
            'input:not([type="hidden"]):not([type="submit"])'
        ];
        
        let formInputs = [];
        for (const selector of formSelectors) {
            formInputs = document.querySelectorAll(selector);
            if (formInputs.length > 0) break;
        }
        
        const controlledFields = [
            'Imperial_Sheet',
            'Imperial_Sheet_No',
            'Metric_Sheet_No',
            'Metric_Sheet_Index',
            'lga_name',
            'plotNo',
            'blockNo',
            'approvedPlanNo',
            'tpPlanNo',
            'layoutName',
            'districtName'
        ];
        
        formInputs.forEach(input => {
            if (input.id !== 'fileno') { // Don't disable the main fileno input
                input.disabled = false;
            }
        });
        
        controlledFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.disabled = false;
        });
        
        // Enable save button
        const saveButton = document.getElementById('saveButton');
        if (saveButton) saveButton.disabled = false;
    }
    
    // Function to clear form and disable inputs
    function clearFormAndDisableInputs() {
        // Try different form selectors
        const formSelectors = [
            '#update-survey-form input:not([type="hidden"]):not([type="submit"])',
            'form input:not([type="hidden"]):not([type="submit"])',
            'input:not([type="hidden"]):not([type="submit"])'
        ];
        
        let formInputs = [];
        for (const selector of formSelectors) {
            formInputs = document.querySelectorAll(selector);
            if (formInputs.length > 0) break;
        }
        
        const controlledFields = [
            'Imperial_Sheet',
            'Imperial_Sheet_No',
            'Metric_Sheet_No',
            'Metric_Sheet_Index',
            'lga_name',
            'plotNo',
            'blockNo',
            'approvedPlanNo',
            'tpPlanNo',
            'layoutName',
            'districtName'
        ];
        
        formInputs.forEach(input => {
            if (input.id !== 'fileno') { // Don't disable the main fileno input
                input.disabled = true;
            }
        });
        
        controlledFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.disabled = true;
        });
        
        // Disable save button
        const saveButton = document.getElementById('saveButton');
        if (saveButton) saveButton.disabled = true;
        
        // Hide application info
        const applicationInfo = document.getElementById('application-info');
        if (applicationInfo) applicationInfo.classList.add('hidden');
        
        // Clear hidden fields
        const appIdEl = document.getElementById('application_id');
        const subAppIdEl = document.getElementById('sub_application_id');
        if (appIdEl) appIdEl.value = '';
        if (subAppIdEl) subAppIdEl.value = '';
        
        window.selectedApplication = null;
    }
    
    // Function to enable file number inputs specifically
    function enableFileNumberInputs() {
        // Enable all file number related inputs
        const fileNumberInputs = [
            'mlsFileNoPrefix', 'mlsFileNumber', 'mlsPreviewFileNumber',
            'kangisFileNoPrefix', 'kangisFileNumber', 'kangisPreviewFileNumber',
            'newKangisFileNoPrefix', 'newKangisFileNumber', 'newKangisPreviewFileNumber'
        ];
        
        fileNumberInputs.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.disabled = false;
            }
        });
    }
    
    // Function to reset manual entry form
    function resetManualEntryForm() {
        // Reset all file number inputs
        const resetFields = [
            'mlsFNo', 'kangisFileNo', 'NewKANGISFileno',
            'mlsPreviewFileNumber', 'kangisPreviewFileNumber', 'newKangisPreviewFileNumber',
            'mlsFileNumber', 'kangisFileNumber', 'newKangisFileNumber',
            'mlsFileNoPrefix', 'kangisFileNoPrefix', 'newKangisFileNoPrefix'
        ];
        
        resetFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        
        // Reset to first tab
        const activeTabEl = document.getElementById('activeFileTab');
        if (activeTabEl) activeTabEl.value = 'mlsFNo';
        
        // Trigger tab switch to first tab
        const firstTabButton = document.querySelector('.tablinks');
        if (firstTabButton && typeof openFileTab === 'function') {
            const fakeEvent = { currentTarget: firstTabButton };
            openFileTab(fakeEvent, 'mlsFNoTab');
        }
    }
    
    // Make toggleFilenoMode globally accessible
    window.toggleFilenoMode = toggleFilenoMode;
    
    // Handle dropdown selection from Select2
    window.handleDropdownSelection = function(application) {
        // Set the main fileno field
        if (filenoInput) filenoInput.value = application.fileno;
        
        // Show selected file number
        if (selectedText) selectedText.textContent = application.fileno;
        if (selectedDisplay) selectedDisplay.classList.remove('hidden');
        
        // Handle the selection
        handleFilenoSelection(application);
    };
}
</script>