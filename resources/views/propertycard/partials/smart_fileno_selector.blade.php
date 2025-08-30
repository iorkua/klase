<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="smart-fileno-selector" x-data="smartFilenoSelector()">
    <!-- Hidden input for the main fileno field that gets submitted -->
    <input type="hidden" id="fileno" name="fileno" value="">
    <!-- Hidden inputs to mirror manual entry names so both modes are identical -->
    <input type="hidden" id="mlsFNo" name="mlsFNo" value="">
    <input type="hidden" id="kangisFileNo" name="kangisFileNo" value="">
    <input type="hidden" id="NewKANGISFileno" name="NewKANGISFileno" value="">
    <input type="hidden" id="activeFileTab" name="activeFileTab" value="">
    
    <!-- Dropdown Selection Mode -->
    <div id="dropdown-mode" class="fileno-mode">
        <select id="fileno-select" class="w-full p-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Select File Number</option>
            @php
                $fileNumbers = DB::connection('sqlsrv')
                    ->select("SELECT TOP (500)
                                [id],
                                [kangisFileNo],
                                [mlsfNo], 
                                [NewKANGISFileNo]
                              FROM [klas].[dbo].[fileNumber]
                              ORDER BY [id] DESC");
            @endphp
            @foreach($fileNumbers as $fileRecord)
                @php
                    // Determine which file number to display (priority: MLS -> KANGIS -> New KANGIS)
                    $displayFileNo = '';
                    
                    if (!empty($fileRecord->mlsfNo)) {
                        $displayFileNo = $fileRecord->mlsfNo;
                    } elseif (!empty($fileRecord->kangisFileNo)) {
                        $displayFileNo = $fileRecord->kangisFileNo;
                    } elseif (!empty($fileRecord->NewKANGISFileNo)) {
                        $displayFileNo = $fileRecord->NewKANGISFileNo;
                    }
                    
                    // Only show records that have at least one file number
                    if (empty($displayFileNo)) continue;
                @endphp
                
                <option value="{{ $fileRecord->id }}" 
                        data-id="{{ $fileRecord->id }}"
                        data-fileno="{{ $displayFileNo }}"
                        data-kangis-fileno="{{ $fileRecord->kangisFileNo ?? '' }}"
                        data-mls-fileno="{{ $fileRecord->mlsfNo ?? '' }}"
                        data-newkangis-fileno="{{ $fileRecord->NewKANGISFileNo ?? '' }}">
                    {{ $displayFileNo }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Search and select file numbers from fileNumber database</p>
        
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
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800" id="file-type-badge">
                                    ✓ Selected
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
</div>

<script>
function smartFilenoSelector() {
    return {
        selectedFileno: '',
        selectedApplication: null,
        
        init() {
            console.log('Smart CT Fileno Selector initialized');
            // Wait for DOM to be fully loaded and jQuery to be available
            this.$nextTick(() => {
                setTimeout(() => {
                    this.initializeSelect2();
                }, 100);
            });
        },
        
        initializeSelect2() {
            // Check if jQuery and Select2 are available
            if (typeof $ === 'undefined') {
                console.error('jQuery is not loaded');
                return;
            }
            
            if (typeof $.fn.select2 === 'undefined') {
                console.error('Select2 is not loaded');
                return;
            }
            
            const selectElement = $('#fileno-select');
            if (!selectElement.length) {
                console.error('Select element #fileno-select not found');
                return;
            }
            
            // Store reference to Alpine component
            const alpineComponent = this;
            
            // Destroy existing Select2 instance if it exists
            if (selectElement.hasClass('select2-hidden-accessible')) {
                selectElement.select2('destroy');
            }
            
            try {
                // Initialize Select2 with search functionality
                selectElement.select2({
                    placeholder: 'Search and select file number...',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 1,
                    // Remove dropdownParent to let it append to body automatically
                    templateResult: function(option) {
                        if (!option.id) {
                            return option.text;
                        }
                        
                        // Custom template for dropdown options
                        const fileno = option.element.dataset.fileno || '';
                        
                        var $option = $(
                            '<div class="select2-result-option">' +
                                '<div class="font-medium text-blue-800">' + fileno + '</div>' +
                            '</div>'
                        );
                        return $option;
                    },
                    templateSelection: function(option) {
                        if (!option.id) {
                            return option.text;
                        }
                        return option.element.dataset.fileno || option.text;
                    }
                });
                
                // Handle selection change - use stored reference
                selectElement.on('select2:select', function(e) {
                    const selectedOption = e.params.data.element;
                    if (selectedOption) {
                        console.log('Select2 selection triggered', selectedOption);
                        
                        const fileno = selectedOption.getAttribute('data-fileno') || '';
                        console.log('Selected fileno:', fileno);
                        
                        // Update Alpine component properties
                        alpineComponent.selectedFileno = fileno;
                        alpineComponent.selectedApplication = {
                            id: selectedOption.getAttribute('data-id'),
                            fileno: fileno,
                            kangisFileNo: selectedOption.getAttribute('data-kangis-fileno'),
                            mlsfNo: selectedOption.getAttribute('data-mls-fileno'),
                            NewKANGISFileNo: selectedOption.getAttribute('data-newkangis-fileno')
                        };
                        
                        console.log('Alpine component application set to:', alpineComponent.selectedApplication);
                        
                        // Call handleSelection directly
                        alpineComponent.handleSelection();
                    }
                });
                
                // Handle clear selection - use stored reference
                selectElement.on('select2:clear', function() {
                    console.log('Select2 clear triggered');
                    alpineComponent.clearSelection();
                });
                
                console.log('Select2 initialized successfully');
                
            } catch (error) {
                console.error('Error initializing Select2:', error);
            }
        },
        
        handleSelection() {
            console.log('handleSelection called with application:', this.selectedApplication);
            
            // Set main fileno input
            const filenoInput = document.getElementById('fileno');
            if (filenoInput) {
                filenoInput.value = this.selectedFileno;
                console.log('Set fileno to:', this.selectedFileno);
            }

            // Mirror fields used by manual entry so backend receives identical names
            const mlsHidden = document.getElementById('mlsFNo');
            const kangisHidden = document.getElementById('kangisFileNo');
            const newKangisHidden = document.getElementById('NewKANGISFileno');
            const activeTabHidden = document.getElementById('activeFileTab');
            
            // Clear all first
            if (mlsHidden) mlsHidden.value = '';
            if (kangisHidden) kangisHidden.value = '';
            if (newKangisHidden) newKangisHidden.value = '';
            
            // Populate the specific file number field based on what we have
            if (this.selectedApplication?.mlsfNo && this.selectedApplication.mlsfNo.trim() !== '') {
                if (mlsHidden) mlsHidden.value = this.selectedApplication.mlsfNo;
                if (activeTabHidden) activeTabHidden.value = 'mls';
                console.log('Set MLS file number:', this.selectedApplication.mlsfNo);
            } else if (this.selectedApplication?.kangisFileNo && this.selectedApplication.kangisFileNo.trim() !== '') {
                if (kangisHidden) kangisHidden.value = this.selectedApplication.kangisFileNo;
                if (activeTabHidden) activeTabHidden.value = 'kangis';
                console.log('Set KANGIS file number:', this.selectedApplication.kangisFileNo);
            } else if (this.selectedApplication?.NewKANGISFileNo && this.selectedApplication.NewKANGISFileNo.trim() !== '') {
                if (newKangisHidden) newKangisHidden.value = this.selectedApplication.NewKANGISFileNo;
                if (activeTabHidden) activeTabHidden.value = 'newkangis';
                console.log('Set New KANGIS file number:', this.selectedApplication.NewKANGISFileNo);
            }
            
            // Debug log all hidden field values
            console.log('Hidden field values after selection:');
            console.log('fileno:', filenoInput?.value);
            console.log('mlsFNo:', mlsHidden?.value);
            console.log('kangisFileNo:', kangisHidden?.value);
            console.log('NewKANGISFileno:', newKangisHidden?.value);
            console.log('activeFileTab:', activeTabHidden?.value);
            
            // Show selected display
            const selectedDisplay = document.getElementById('selected-fileno-display');
            const selectedText = document.getElementById('selected-fileno-text');
            const fileTypeBadge = document.getElementById('file-type-badge');
            
            if (selectedText) selectedText.textContent = this.selectedFileno;
            if (fileTypeBadge) fileTypeBadge.textContent = `✓ Selected`;
            if (selectedDisplay) selectedDisplay.classList.remove('hidden');
            
            // Dispatch event for other components
            this.$dispatch('ct-fileno-selected', {
                fileno: this.selectedFileno,
                application: this.selectedApplication
            });
            
            console.log('CT File selected:', this.selectedApplication);
        },
        
        clearSelection() {
            this.selectedFileno = '';
            this.selectedApplication = null;
            
            // Clear hidden input
            const filenoInput = document.getElementById('fileno');
            if (filenoInput) {
                filenoInput.value = '';
            }
            const mlsHidden = document.getElementById('mlsFNo');
            const kangisHidden = document.getElementById('kangisFileNo');
            const newKangisHidden = document.getElementById('NewKANGISFileno');
            const activeTabHidden = document.getElementById('activeFileTab');
            if (mlsHidden) mlsHidden.value = '';
            if (kangisHidden) kangisHidden.value = '';
            if (newKangisHidden) newKangisHidden.value = '';
            if (activeTabHidden) activeTabHidden.value = '';
            
            // Hide selected display
            const selectedDisplay = document.getElementById('selected-fileno-display');
            if (selectedDisplay) selectedDisplay.classList.add('hidden');
            
            // Clear dropdown
            const filenoSelect = document.getElementById('fileno-select');
            if (filenoSelect) {
                filenoSelect.value = '';
            }
            
            // Dispatch clear event
            this.$dispatch('ct-fileno-cleared');
        }
    }
}

// Initialize clear button functionality when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Delay the initialization to ensure all scripts are loaded
    setTimeout(() => {
        const clearBtn = document.getElementById('clear-selection');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                // Check if jQuery and Select2 are available
                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    // Clear Select2 selection
                    $('#fileno-select').val(null).trigger('change');
                }
                
                // Trigger Alpine.js clear method
                const component = document.querySelector('[x-data*="smartFilenoSelector"]');
                if (component && component._x_dataStack) {
                    component._x_dataStack[0].clearSelection();
                }
            });
        }
    }, 200);
});
</script>

<style>
/* Custom Select2 styling */
.select2-container--default .select2-selection--single {
    height: 42px;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px;
    padding-left: 12px;
    color: #374151;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 8px;
}

.select2-dropdown {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 8px 12px;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #3b82f6;
}

.select2-result-option {
    padding: 4px 0;
}

.select2-container--default .select2-selection--single:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>