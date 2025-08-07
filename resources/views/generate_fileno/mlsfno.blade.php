@extends('layouts.app')
@section('page-title')
    {{ __('MLS File Number Generator') }}
@endsection

@section('content')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header', [
            'PageTitle' => 'MLS File Number Generator',
            'PageDescription' => 'Generate and manage MLS file numbers'
        ])
        
        <!-- Dashboard Content -->
        <div class="p-6">
            <div class="container mx-auto py-6 space-y-6">
                
                <!-- Action Buttons -->
                <div class="flex justify-between items-center mb-6">
                    <div class="flex space-x-4">
                        <button 
                            onclick="openGenerateModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Generate New Application MLS File Number</span>
                        </button>
                        <button 
                            onclick="openMigrationModal()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            <span>Migrate Data</span>
                        </button>
                    </div>
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 shadow-sm">
    <div class="flex items-center space-x-2">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <div class="text-sm text-gray-700 font-medium">
            Total Generated: 
            <span id="totalCount" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-blue-100 text-blue-800 ml-1">
                {{ $totalCount ?? 0 }}
            </span>
        </div>
    </div>
</div>
                </div>

                <!-- DataTable -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table id="mlsfTable" class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KANGIS File No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New KANGIS File No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Data will be loaded via DataTables AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        @include('admin.footer')
    </div>

    <!-- Generate Modal -->
    <div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Generate New Application MLS File Number</h3>
                    <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Form -->
                <form id="generateForm" onsubmit="submitForm(event)">
                    @csrf
                    
                    <!-- Application Type Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Type</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="application_type" value="new" class="mr-2" onchange="updateApplicationType('new')" checked>
                                <span>New Application MLS File Number</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="application_type" value="conversion" class="mr-2" onchange="updateApplicationType('conversion')">
                                <span>Conversion</span>
                            </label>
                        </div>
                    </div>

                    <!-- File Name -->
                    <div class="mb-4">
                        <label for="fileName" class="block text-sm font-medium text-gray-700 mb-2">File Name</label>
                        <input type="text" id="fileName" name="file_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter file name">
                    </div>
                    
                    <!-- Land Use -->
                    <div class="mb-4">
                        <label for="landUse" class="block text-sm font-medium text-gray-700 mb-2">Land Use</label>
                        <select id="landUse" name="land_use" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="updatePreview()">
                            <option value="">Select Land Use</option>
                            <!-- New Application Options -->
                            <optgroup id="newOptions" label="New Application">
                                <option value="RES">RES - Residential</option>
                                <option value="COM">COM - Commercial</option>
                                <option value="IND">IND - Industrial</option>
                                <option value="AGR">AGR - Agricultural</option>
                                <option value="INS">INS - Institutional</option>
                            </optgroup> 
                            <!-- Conversion Options -->
                            <optgroup id="conversionOptions" label="Conversion" style="display: none;">
                                <option value="CON-RES">CON-RES - Conversion to Residential</option>
                                <option value="CON-COM">CON-COM - Conversion to Commercial</option>
                                <option value="CON-IND">CON-IND - Conversion to Industrial</option>
                                <option value="CON-AGR">CON-AGR - Conversion to Agricultural</option>
                                <option value="CON-INS">CON-INS - Conversion to Institutional</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- File Options -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Options</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="file_option" value="normal" class="mr-2" onchange="updatePreview()" checked>
                                <span>Normal File</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="file_option" value="temporary" class="mr-2" onchange="updatePreview()">
                                <span>Temporary File</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="file_option" value="extension" class="mr-2" onchange="updatePreview()">
                                <span>Extension</span>
                            </label>
                        </div>
                    </div>

                    <!-- Extension File Selection (shown only when Extension is selected) -->
                    <div id="extensionFileSection" class="mb-4 hidden">
                        <label for="existingFileNo" class="block text-sm font-medium text-gray-700 mb-2">Select Existing MLS File Number</label>
                        <select id="existingFileNo" name="existing_file_no" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="updatePreview()">
                            <option value="">Select existing file number...</option>
                            <!-- Options will be populated via AJAX -->
                        </select>
                    </div>

                    <!-- Year -->
                    <div class="mb-4">
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                        <input type="number" id="year" name="year" 
                               value="{{ date('Y') }}"
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600"
                               min="2020" max="2050" readonly>
                        <p class="text-xs text-gray-500 mt-1">Current year (auto-filled)</p>
                    </div>

                    <!-- Serial Number -->
                    <div class="mb-4">
                        <label for="serialNo" class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                        <input type="number" id="serialNo" name="serial_no" 
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600"
                               readonly>
                        <p class="text-xs text-gray-500 mt-1">Auto-generated based on last serial number</p>
                    </div>

                    <!-- Full File Number Preview -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full File Number</label>
                        <div id="mlsfPreview" class="w-full px-3 py-2 bg-blue-50 border border-blue-300 rounded-md text-lg font-mono text-center text-blue-800 font-semibold">
                            -
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between">
                        <button type="button" onclick="showOverrideModal()" 
                                class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                            Override
                        </button>
                        <div class="flex space-x-3">
                            <button type="button" onclick="closeGenerateModal()" 
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Generate
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Override Modal -->
    <div id="overrideModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Override File Number</h3>
                    <button onclick="closeOverrideModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Override Form -->
                <form id="overrideForm" onsubmit="submitOverrideForm(event)">
                    @csrf
                    
                    <!-- Manual Year -->
                    <div class="mb-4">
                        <label for="overrideYear" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                        <input type="number" id="overrideYear" name="override_year" 
                               value="{{ date('Y') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               min="2020" max="2050">
                    </div>

                    <!-- Manual Serial Number -->
                    <div class="mb-4">
                        <label for="overrideSerialNo" class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                        <input type="number" id="overrideSerialNo" name="override_serial_no" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               min="1" max="9999">
                    </div>

                    <!-- Extension Option -->
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="overrideExtension" name="override_extension" class="mr-2">
                            <span>File Extension</span>
                        </label>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeOverrideModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                            Apply Override
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Migration Modal -->
    <div id="migrationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Migrate Data from Excel</h3>
                    <button onclick="closeMigrationModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Migration Form -->
                <form id="migrationForm" onsubmit="submitMigrationForm(event)" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- File Upload -->
                    <div class="mb-4">
                        <label for="excelFile" class="block text-sm font-medium text-gray-700 mb-2">Excel File</label>
                        <input type="file" id="excelFile" name="excel_file" 
                               accept=".xlsx,.xls,.csv"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Upload Excel file with columns: mlsfNo, kangisFile, NewKANGISFileNo, FileName</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeMigrationModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Migrate Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit MLSF Number</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Edit Form -->
                <form id="editForm" onsubmit="submitEditForm(event)">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editId" name="id">
                    
                    <!-- MLSF Number (Read-only) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">MLSF Number</label>
                        <input type="text" id="editMlsfNo" 
                               class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md"
                               readonly>
                    </div>

                    <!-- Type (Read-only) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <input type="text" id="editType" 
                               class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md"
                               readonly>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        let table;
        let nextSerialNo = 1;
        let isOverrideMode = false;

        $(document).ready(function() {
            // Initialize DataTable
            table = $('#mlsfTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("file-numbers.data") }}',
                    type: 'GET',
                    error: function(xhr, error, code) {
                        console.error('DataTables error:', error);
                        console.error('Response:', xhr.responseText);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Loading Data',
                            text: 'Failed to load file numbers. Please refresh the page.',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                },
                columns: [
                    { 
                        data: 'kangisFileNo', 
                        name: 'kangisFileNo',
                        title: 'KANGIS File No'
                    },
                    { 
                        data: 'NewKANGISFileNo', 
                        name: 'NewKANGISFileNo',
                        title: 'New KANGIS File No'
                    },
                    { 
                        data: 'FileName', 
                        name: 'FileName',
                        title: 'File Name'
                    },
                    { 
                        data: 'action', 
                        name: 'action', 
                        title: 'Actions',
                        orderable: false, 
                        searchable: false
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: "Loading file numbers...",
                    emptyTable: "No file numbers found",
                    zeroRecords: "No matching file numbers found"
                },
                drawCallback: function() {
                    // Reinitialize Lucide icons after table redraw
                    setTimeout(function() {
                        lucide.createIcons();
                    }, 100);
                }
            });

            // Get next serial number
            getNextSerialNumber();
            
            // Load existing file numbers for extension dropdown
            loadExistingFileNumbers();
        });

        function openGenerateModal() {
            document.getElementById('generateModal').classList.remove('hidden');
            
            // Reset form
            document.getElementById('generateForm').reset();
            document.getElementById('year').value = new Date().getFullYear();
            document.getElementById('serialNo').value = nextSerialNo;
            
            // Reset to default state
            isOverrideMode = false;
            document.getElementById('year').readOnly = true;
            document.getElementById('serialNo').readOnly = true;
            document.getElementById('year').classList.add('bg-gray-100', 'text-gray-600');
            document.getElementById('serialNo').classList.add('bg-gray-100', 'text-gray-600');
            
            // Set default application type
            document.querySelector('input[name="application_type"][value="new"]').checked = true;
            updateApplicationType('new');
            
            updatePreview();
        }

        function closeGenerateModal() {
            document.getElementById('generateModal').classList.add('hidden');
        }

        function updateApplicationType(type) {
            const newOptions = document.getElementById('newOptions');
            const conversionOptions = document.getElementById('conversionOptions');
            const landUseSelect = document.getElementById('landUse');
            
            if (type === 'new') {
                newOptions.style.display = 'block';
                conversionOptions.style.display = 'none';
            } else {
                newOptions.style.display = 'none';
                conversionOptions.style.display = 'block';
            }
            
            // Reset land use selection
            landUseSelect.value = '';
            updatePreview();
        }

        function updatePreview() {
            const serialNo = document.getElementById('serialNo').value;
            const year = document.getElementById('year').value;
            const landUse = document.getElementById('landUse').value;
            const fileOption = document.querySelector('input[name="file_option"]:checked')?.value;
            const existingFileNo = document.getElementById('existingFileNo').value;
            const preview = document.getElementById('mlsfPreview');
            
            let previewText = '-';
            
            if (fileOption === 'extension' && existingFileNo) {
                previewText = existingFileNo + ' AND EXTENSION';
            } else if (serialNo && year && landUse) {
                const paddedSerial = serialNo.toString().padStart(4, '0');
                previewText = `${landUse}-${year}-${paddedSerial}`;
                
                if (fileOption === 'temporary') {
                    previewText += '(T)';
                }
            }
            
            preview.textContent = previewText;
            
            if (previewText !== '-') {
                preview.classList.remove('text-gray-400');
                preview.classList.add('text-green-600');
            } else {
                preview.classList.remove('text-green-600');
                preview.classList.add('text-gray-400');
            }
            
            // Show/hide extension file selection
            const extensionSection = document.getElementById('extensionFileSection');
            if (fileOption === 'extension') {
                extensionSection.classList.remove('hidden');
            } else {
                extensionSection.classList.add('hidden');
            }
        }

        function loadExistingFileNumbers() {
            fetch('{{ route("file-numbers.existing") }}')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('existingFileNo');
                    select.innerHTML = '<option value="">Select existing file number...</option>';
                    
                    data.forEach(fileNo => {
                        const option = document.createElement('option');
                        option.value = fileNo.mlsfNo;
                        option.textContent = fileNo.mlsfNo;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading existing file numbers:', error);
                });
        }

        function getNextSerialNumber() {
            const currentYear = new Date().getFullYear();
            
            fetch(`{{ route("file-numbers.next-serial") }}?year=${currentYear}`)
                .then(response => response.json())
                .then(data => {
                    nextSerialNo = data.nextSerial;
                    if (!isOverrideMode) {
                        document.getElementById('serialNo').value = nextSerialNo;
                        updatePreview();
                    }
                })
                .catch(error => {
                    console.error('Error getting next serial number:', error);
                });
        }

        function showOverrideModal() {
            document.getElementById('overrideModal').classList.remove('hidden');
            document.getElementById('overrideYear').value = document.getElementById('year').value;
            document.getElementById('overrideSerialNo').value = document.getElementById('serialNo').value;
        }

        function closeOverrideModal() {
            document.getElementById('overrideModal').classList.add('hidden');
        }

        function submitOverrideForm(event) {
            event.preventDefault();
            
            const overrideYear = document.getElementById('overrideYear').value;
            const overrideSerialNo = document.getElementById('overrideSerialNo').value;
            const overrideExtension = document.getElementById('overrideExtension').checked;
            
            // Apply override values to main form
            document.getElementById('year').value = overrideYear;
            document.getElementById('serialNo').value = overrideSerialNo;
            
            // Enable manual editing
            isOverrideMode = true;
            document.getElementById('year').readOnly = false;
            document.getElementById('serialNo').readOnly = false;
            document.getElementById('year').classList.remove('bg-gray-100', 'text-gray-600');
            document.getElementById('serialNo').classList.remove('bg-gray-100', 'text-gray-600');
            document.getElementById('year').classList.add('bg-white', 'text-gray-900');
            document.getElementById('serialNo').classList.add('bg-white', 'text-gray-900');
            
            if (overrideExtension) {
                document.querySelector('input[name="file_option"][value="extension"]').checked = true;
            }
            
            updatePreview();
            closeOverrideModal();
        }

        function openMigrationModal() {
            document.getElementById('migrationModal').classList.remove('hidden');
        }

        function closeMigrationModal() {
            document.getElementById('migrationModal').classList.add('hidden');
        }

        function submitMigrationForm(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('migrationForm'));
            
            fetch('{{ route("file-numbers.migrate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#10b981'
                    });
                    closeMigrationModal();
                    table.ajax.reload();
                    updateTotalCount();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'An error occurred during migration',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while migrating data',
                    confirmButtonColor: '#ef4444'
                });
            });
        }

        function submitForm(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('generateForm'));
            
            fetch('{{ route("file-numbers.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#10b981'
                    });
                    closeGenerateModal();
                    table.ajax.reload();
                    getNextSerialNumber();
                    updateTotalCount();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'An error occurred',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while generating the file number',
                    confirmButtonColor: '#ef4444'
                });
            });
        }

        function editRecord(id) {
            fetch(`{{ route("file-numbers.show", ":id") }}`.replace(':id', id))
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editId').value = data.id;
                    document.getElementById('editMlsfNo').value = data.mlsfNo || data.kangisFileNo;
                    document.getElementById('editType').value = data.type || 'Generated';
                    document.getElementById('editModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to load record details',
                        confirmButtonColor: '#ef4444'
                    });
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function submitEditForm(event) {
            event.preventDefault();
            
            const id = document.getElementById('editId').value;
            const formData = new FormData(document.getElementById('editForm'));
            
            fetch(`{{ route("file-numbers.update", ":id") }}`.replace(':id', id), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#10b981'
                    });
                    closeEditModal();
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'An error occurred',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while updating the record',
                    confirmButtonColor: '#ef4444'
                });
            });
        }

        function deleteRecord(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route("file-numbers.destroy", ":id") }}`.replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                confirmButtonColor: '#10b981'
                            });
                            table.ajax.reload();
                            updateTotalCount();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message || 'An error occurred',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while deleting the record',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        }

        function updateTotalCount() {
            fetch('{{ route("file-numbers.count") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalCount').textContent = data.count;
                })
                .catch(error => {
                    console.error('Error updating count:', error);
                });
        }

        // Add event listeners for form inputs
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('serialNo').addEventListener('input', updatePreview);
            document.getElementById('year').addEventListener('input', updatePreview);
            document.getElementById('landUse').addEventListener('change', updatePreview);
            
            // Add event listeners for file option radio buttons
            document.querySelectorAll('input[name="file_option"]').forEach(radio => {
                radio.addEventListener('change', updatePreview);
            });
            
            // Add event listener for existing file number dropdown
            document.getElementById('existingFileNo').addEventListener('change', updatePreview);
        });
    </script>
@endsection