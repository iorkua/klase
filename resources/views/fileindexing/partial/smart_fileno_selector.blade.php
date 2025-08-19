<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="smart-fileno-selector" x-data="smartFilenoSelector()">
    <!-- Hidden input for the main fileno field that gets submitted -->
    <input type="hidden" id="fileno" name="fileno" value="">
    
    <!-- Dropdown Selection Mode -->
    <div id="dropdown-mode" class="fileno-mode">
        <select id="fileno-select" class="w-full p-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Select File Number</option>
            @php
                // Collect filenos from fileNumber, subapplications, and mother_applications
                $entries = [];

                // 1) fileNumber table (keep rich attributes)
                $fileNumbers = DB::connection('sqlsrv')->select("\n                    SELECT [id], [kangisFileNo], [mlsfNo], [NewKANGISFileNo]\n                    FROM [klas].[dbo].[fileNumber]\n                    ORDER BY [id] DESC\n                ");
                foreach ($fileNumbers as $fr) {
                    $displayFileNo = '';
                    if (!empty($fr->mlsfNo)) {
                        $displayFileNo = $fr->mlsfNo;
                    } elseif (!empty($fr->kangisFileNo)) {
                        $displayFileNo = $fr->kangisFileNo;
                    } elseif (!empty($fr->NewKANGISFileNo)) {
                        $displayFileNo = $fr->NewKANGISFileNo;
                    }
                    $displayFileNo = trim((string) $displayFileNo);
                    if ($displayFileNo === '') continue;

                    $key = strtoupper($displayFileNo);
                    if (!isset($entries[$key])) {
                        $entries[$key] = [
                            'source' => 'fileNumber',
                            'id' => $fr->id,
                            'fileno' => $displayFileNo,
                            'kangisFileNo' => $fr->kangisFileNo ?? '',
                            'mlsfNo' => $fr->mlsfNo ?? '',
                            'NewKANGISFileNo' => $fr->NewKANGISFileNo ?? '',
                        ];
                    }
                }

                // 2) subapplications table
                $subapps = DB::connection('sqlsrv')->select("\n                    SELECT [id], [fileno]\n                    FROM [klas].[dbo].[subapplications]\n                    WHERE [fileno] IS NOT NULL AND LTRIM(RTRIM([fileno])) <> ''\n                    ORDER BY [id] DESC\n                ");
                foreach ($subapps as $sa) {
                    $fileno = trim((string) $sa->fileno);
                    if ($fileno === '') continue;
                    $key = strtoupper($fileno);
                    if (!isset($entries[$key])) {
                        $entries[$key] = [
                            'source' => 'subapplications',
                            'id' => $sa->id,
                            'fileno' => $fileno,
                            'kangisFileNo' => '',
                            'mlsfNo' => '',
                            'NewKANGISFileNo' => '',
                        ];
                    }
                }

                // 3) mother_applications table
                $mothers = DB::connection('sqlsrv')->select("\n                    SELECT [id], [fileno]\n                    FROM [klas].[dbo].[mother_applications]\n                    WHERE [fileno] IS NOT NULL AND LTRIM(RTRIM([fileno])) <> ''\n                    ORDER BY [id] DESC\n                ");
                foreach ($mothers as $ma) {
                    $fileno = trim((string) $ma->fileno);
                    if ($fileno === '') continue;
                    $key = strtoupper($fileno);
                    if (!isset($entries[$key])) {
                        $entries[$key] = [
                            'source' => 'mother_applications',
                            'id' => $ma->id,
                            'fileno' => $fileno,
                            'kangisFileNo' => '',
                            'mlsfNo' => '',
                            'NewKANGISFileNo' => '',
                        ];
                    }
                }
            @endphp
            @foreach($entries as $entry)
                <option value="{{ $entry['source'] }}:{{ $entry['id'] }}" 
                        data-id="{{ $entry['source'] }}:{{ $entry['id'] }}"
                        data-fileno="{{ $entry['fileno'] }}"
                        data-kangis-fileno="{{ $entry['kangisFileNo'] }}"
                        data-mls-fileno="{{ $entry['mlsfNo'] }}"
                        data-newkangis-fileno="{{ $entry['NewKANGISFileNo'] }}"
                        data-source="{{ $entry['source'] }}">
                    {{ $entry['fileno'] }}
                    @if($entry['fileno'] === 'RES-455')
                        <!-- Debug: RES-455 found in dropdown -->
                    @endif
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Search and select file numbers from fileNumber, subapplications, and mother_applications tables</p>
        
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
                                    ✓ CT Ready
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
                
                <!-- Show all available file numbers for this record -->
                <!-- <div class="mt-3 pt-3 border-t border-green-200">
                    <h4 class="text-xs font-medium text-green-700 mb-2">Available File Numbers:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs">
                        <div id="mls-file-display" class="hidden">
                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded font-mono">MLS: <span id="mls-file-text"></span></span>
                        </div>
                        <div id="kangis-file-display" class="hidden">
                            <span class="inline-block bg-purple-100 text-purple-800 px-2 py-1 rounded font-mono">KANGIS: <span id="kangis-file-text"></span></span>
                        </div>
                        <div id="newkangis-file-display" class="hidden">
                            <span class="inline-block bg-orange-100 text-orange-800 px-2 py-1 rounded font-mono">New KANGIS: <span id="newkangis-file-text"></span></span>
                        </div>
                    </div>
                </div> -->
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
            
            // Debug: Log all available options
            const selectElement = document.getElementById('fileno-select');
            if (selectElement) {
                const options = Array.from(selectElement.options);
                console.log('Available file numbers in dropdown:', options.map(opt => opt.dataset.fileno || opt.text).filter(text => text && text !== 'Select File Number'));
                
                // Check specifically for RES-455
                const res455Option = options.find(opt => opt.dataset.fileno === 'RES-455');
                if (res455Option) {
                    console.log('RES-455 found in dropdown:', res455Option);
                } else {
                    console.log('RES-455 NOT found in dropdown');
                }
            }
            
            this.initializeSelect2();
        },
        
        initializeSelect2() {
            // Initialize Select2 with search functionality
            $('#fileno-select').select2({
                placeholder: 'Search and select file number...',
                allowClear: true,
                width: '100%',
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
            
            // Handle selection change
            $('#fileno-select').on('select2:select', (e) => {
                const selectedOption = e.params.data.element;
                if (selectedOption) {
                    this.selectedFileno = selectedOption.getAttribute('data-fileno') || '';
                    
                    // Create application object from data attributes
                    this.selectedApplication = {
                        id: selectedOption.getAttribute('data-id'),
                        fileno: selectedOption.getAttribute('data-fileno'),
                        kangisFileNo: selectedOption.getAttribute('data-kangis-fileno'),
                        mlsfNo: selectedOption.getAttribute('data-mls-fileno'),
                        NewKANGISFileNo: selectedOption.getAttribute('data-newkangis-fileno')
                    };
                    
                    this.handleSelection();
                }
            });
            
            // Handle clear selection
            $('#fileno-select').on('select2:clear', () => {
                this.clearSelection();
            });
        },
        
        handleSelection() {
            // Set hidden input
            const filenoInput = document.getElementById('fileno');
            if (filenoInput) {
                filenoInput.value = this.selectedFileno;
            }
            
            // Show selected display
            const selectedDisplay = document.getElementById('selected-fileno-display');
            const selectedText = document.getElementById('selected-fileno-text');
            const fileTypeBadge = document.getElementById('file-type-badge');
            
            if (selectedText) selectedText.textContent = this.selectedFileno;
            if (fileTypeBadge) fileTypeBadge.textContent = `✓ Selected`;
            if (selectedDisplay) selectedDisplay.classList.remove('hidden');
            
            // Show individual file numbers
            this.displayAllFileNumbers();
            
            // Dispatch event for other components
            console.log('Dispatching ct-fileno-selected event with fileno:', this.selectedFileno);
            this.$dispatch('ct-fileno-selected', {
                fileno: this.selectedFileno,
                application: this.selectedApplication
            });
            
            console.log('CT File selected:', this.selectedApplication);
        },
        
        displayAllFileNumbers() {
            const app = this.selectedApplication;
            
            // MLS File Number
            const mlsDisplay = document.getElementById('mls-file-display');
            const mlsText = document.getElementById('mls-file-text');
            if (app.mlsfNo) {
                if (mlsText) mlsText.textContent = app.mlsfNo;
                if (mlsDisplay) mlsDisplay.classList.remove('hidden');
            } else {
                if (mlsDisplay) mlsDisplay.classList.add('hidden');
            }
            
            // KANGIS File Number
            const kangisDisplay = document.getElementById('kangis-file-display');
            const kangisText = document.getElementById('kangis-file-text');
            if (app.kangisFileNo) {
                if (kangisText) kangisText.textContent = app.kangisFileNo;
                if (kangisDisplay) kangisDisplay.classList.remove('hidden');
            } else {
                if (kangisDisplay) kangisDisplay.classList.add('hidden');
            }
            
            // New KANGIS File Number
            const newKangisDisplay = document.getElementById('newkangis-file-display');
            const newKangisText = document.getElementById('newkangis-file-text');
            if (app.NewKANGISFileNo) {
                if (newKangisText) newKangisText.textContent = app.NewKANGISFileNo;
                if (newKangisDisplay) newKangisDisplay.classList.remove('hidden');
            } else {
                if (newKangisDisplay) newKangisDisplay.classList.add('hidden');
            }
        },
        
        clearSelection() {
            this.selectedFileno = '';
            this.selectedApplication = null;
            
            // Clear hidden input
            const filenoInput = document.getElementById('fileno');
            if (filenoInput) {
                filenoInput.value = '';
            }
            
            // Hide selected display
            const selectedDisplay = document.getElementById('selected-fileno-display');
            if (selectedDisplay) selectedDisplay.classList.add('hidden');
            
            // Hide all file number displays
            document.getElementById('mls-file-display')?.classList.add('hidden');
            document.getElementById('kangis-file-display')?.classList.add('hidden');
            document.getElementById('newkangis-file-display')?.classList.add('hidden');
            
            // Clear dropdown
            const filenoSelect = document.getElementById('fileno-select');
            if (filenoSelect) {
                filenoSelect.value = '';
            }
            
            // Dispatch clear event
            console.log('Dispatching ct-fileno-cleared event');
            this.$dispatch('ct-fileno-cleared');
        }
    }
}

// Initialize clear button functionality
document.addEventListener('DOMContentLoaded', function() {
    const clearBtn = document.getElementById('clear-selection');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            // Clear Select2 selection
            $('#fileno-select').val(null).trigger('change');
            
            // Trigger Alpine.js clear method
            const component = document.querySelector('[x-data*="smartFilenoSelector"]');
            if (component && component._x_dataStack) {
                component._x_dataStack[0].clearSelection();
            }
        });
    }
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