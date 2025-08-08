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
                            <span>Generate New  FileNO</span>
                        </button>
                         <button 
                            onclick="openMigrationModal()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            <span>Migrate Data</span>
                        </button>  
                        <!-- <button 
                            onclick="testDatabaseConnection()"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                            <i data-lucide="database" class="w-4 h-4"></i>
                            <span>Test Database</span>
                        </button> -->
                        <!-- <button 
                            onclick="debugTableData()"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                            <i data-lucide="bug" class="w-4 h-4"></i>
                            <span>Debug Data</span>
                        </button> -->
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
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MLS File No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KANGIS File No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New KANGIS File No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
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
        <div class="relative top-5 mx-auto p-6 border w-[800px] max-w-4xl shadow-xl rounded-lg bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                    <div>
                        <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Generate New Application</h3>
                        <p class="text-sm text-gray-500 mt-1">Fill in the details to generate a new MLS file number</p>
                    </div>
                    <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <!-- Modal Form -->
                <form id="generateForm" onsubmit="submitForm(event)" class="space-y-6">
                    @csrf
                    
                    <!-- Application Type Selection -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Application Type</label>
                        <div class="flex space-x-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="application_type" value="new" class="mr-3 text-blue-600" onchange="updateApplicationType('new')" checked>
                                <span class="text-sm font-medium">New Application</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="application_type" value="conversion" class="mr-3 text-blue-600" onchange="updateApplicationType('conversion')">
                                <span class="text-sm font-medium">Conversion</span>
                            </label>
                        </div>
                    </div>

                    <!-- Main Form Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- File Name -->
                            <div>
                                <label for="fileName" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i data-lucide="file-text" class="w-4 h-4 inline mr-1"></i>
                                    File Name
                                </label>
                                <input type="text" id="fileName" name="file_name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter file name">
                            </div>
                            
                            <!-- Land Use -->
                            <div>
                                <label for="landUse" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i data-lucide="map" class="w-4 h-4 inline mr-1"></i>
                                    Land Use
                                </label>
                                <select id="landUse" name="land_use" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    <i data-lucide="settings" class="w-4 h-4 inline mr-1"></i>
                                    File Options
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center cursor-pointer p-2 rounded hover:bg-gray-50">
                                        <input type="radio" name="file_option" value="normal" class="mr-3 text-blue-600" onchange="updatePreview()" checked>
                                        <span class="text-sm">Normal File</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer p-2 rounded hover:bg-gray-50">
                                        <input type="radio" name="file_option" value="temporary" class="mr-3 text-blue-600" onchange="updatePreview()">
                                        <span class="text-sm">Temporary File</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer p-2 rounded hover:bg-gray-50">
                                        <input type="radio" name="file_option" value="extension" class="mr-3 text-blue-600" onchange="updatePreview()">
                                        <span class="text-sm">Extension</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Extension File Selection (shown only when Extension is selected) -->
                            <div id="extensionFileSection" class="hidden">
                                <label for="existingFileNo" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i data-lucide="link" class="w-4 h-4 inline mr-1"></i>
                                    Select Existing MLS File Number
                                </label>
                                <select id="existingFileNo" name="existing_file_no" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        onchange="updatePreview()">
                                    <option value="">Select existing file number...</option>
                                    <!-- Options will be populated via AJAX -->
                                </select>
                            </div>

                            <!-- Year and Serial Number Grid -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Year -->
                                <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                        Year
                                    </label>
                                    <input type="number" id="year" name="year" 
                                           value="{{ date('Y') }}"
                                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600"
                                           min="2020" max="2050" readonly>
                                    <p class="text-xs text-gray-500 mt-1">Auto-filled</p>
                                </div>

                                <!-- Serial Number -->
                                <div>
                                    <label for="serialNo" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="hash" class="w-4 h-4 inline mr-1"></i>
                                        Serial No.
                                    </label>
                                    <input type="number" id="serialNo" name="serial_no" 
                                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600"
                                           readonly>
                                    <p class="text-xs text-gray-500 mt-1">Auto-generated</p>
                                </div>
                            </div>

                            <!-- Full File Number Preview -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                    Generated File Number Preview
                                </label>
                                <div id="mlsfPreview" class="w-full px-4 py-3 bg-white border border-blue-300 rounded-md text-lg font-mono text-center text-blue-800 font-bold shadow-sm">
                                    -
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <button type="button" onclick="showOverrideModal()" 
                                class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors flex items-center space-x-2">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            <span>Override</span>
                        </button>
                        <div class="flex space-x-3">
                            <button type="button" onclick="closeGenerateModal()" 
                                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center space-x-2">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Generate</span>
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
                    <div class="mb-4" style="display:none;">
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
                        <label for="excelFile" class="block text-sm font-medium text-gray-700 mb-2">CSV File</label>
                        <input type="file" id="excelFile" name="excel_file" 
                               accept=".csv,.txt"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Upload CSV file with columns: mlsfNo, kangisFile, NewKANGISFileNo, FileName (ignore SN column)</p>
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
                    <h3 class="text-lg font-medium text-gray-900">Edit File Name</h3>
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

                    <!-- File Name (Editable) -->
                    <div class="mb-4">
                        <label for="editFileName" class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-lucide="file-text" class="w-4 h-4 inline mr-1"></i>
                            File Name
                        </label>
                        <input type="text" id="editFileName" name="file_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter file name" required>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update File Name
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

        // Loading utility functions
        function showLoadingButton(buttonElement, originalText) {
            if (buttonElement) {
                buttonElement.disabled = true;
                buttonElement.innerHTML = `
                    <i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>
                    Loading...
                `;
                lucide.createIcons();
            }
        }

        function hideLoadingButton(buttonElement, originalText) {
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.innerHTML = originalText;
                lucide.createIcons();
            }
        }

        function showGlobalLoading(message = 'Processing...') {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function hideGlobalLoading() {
            Swal.close();
        }

        $(document).ready(function() {
            // Initialize DataTable
            table = $('#mlsfTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("file-numbers.data") }}',
                    type: 'GET',
                    data: function(d) {
                        console.log('DataTables request:', d);
                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('DataTables response:', json);
                        if (json.error) {
                            console.error('Server error:', json.error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: json.error,
                                confirmButtonColor: '#ef4444'
                            });
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTables AJAX error:', error);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        
                        let errorMessage = 'Failed to load file numbers. Please check your connection and try again.';
                        
                        if (xhr.status === 500) {
                            errorMessage = 'Server error occurred. Please contact the administrator.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Data endpoint not found. Please contact the administrator.';
                        } else if (xhr.status === 0) {
                            errorMessage = 'Network connection error. Please check your internet connection.';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Loading Data',
                            text: errorMessage,
                            confirmButtonColor: '#ef4444',
                            footer: `<small>Error Code: ${xhr.status} - ${error}</small>`
                        });
                    }
                },
                columns: [
                    { 
                        data: 'mlsfNo', 
                        name: 'mlsfNo',
                        title: 'MLS File No',
                        defaultContent: 'N/A',
                        render: function(data, type, row) {
                            if (data && data !== 'N/A' && data.trim() !== '') {
                                return data;
                            }
                            return 'N/A';
                        }
                    },
                    { 
                        data: 'kangisFileNo', 
                        name: 'kangisFileNo',
                        title: 'KANGIS File No',
                        defaultContent: 'N/A',
                        render: function(data, type, row) {
                            if (data && data !== 'N/A' && data.trim() !== '') {
                                return data;
                            }
                            return 'N/A';
                        }
                    },
                    { 
                        data: 'NewKANGISFileNo', 
                        name: 'NewKANGISFileNo',
                        title: 'New KANGIS File No',
                        defaultContent: 'N/A',
                        render: function(data, type, row) {
                            if (data && data !== 'N/A' && data.trim() !== '') {
                                return data;
                            }
                            return 'N/A';
                        }
                    },
                    { 
                        data: 'FileName', 
                        name: 'FileName',
                        title: 'File Name',
                        defaultContent: 'N/A',
                        render: function(data, type, row) {
                            if (data && data !== 'N/A' && data.trim() !== '') {
                                return data;
                            }
                            return 'N/A';
                        }
                    },
                    { 
                        data: 'created_by', 
                        name: 'created_by',
                        title: 'Created By',
                        defaultContent: 'System',
                        render: function(data, type, row) {
                            if (data && data.trim() !== '') {
                                return data;
                            }
                            return 'System';
                        }
                    },
                    { 
                        data: 'created_at', 
                        name: 'created_at',
                        title: 'Created Date',
                        defaultContent: 'N/A',
                        render: function(data, type, row) {
                            if (data && data.trim() !== '') {
                                const date = new Date(data);
                                return date.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                    hour12: true
                                });
                            }
                            return 'N/A';
                        }
                    },
                    { 
                        data: 'action', 
                        name: 'action', 
                        title: 'Actions',
                        orderable: false, 
                        searchable: false,
                        defaultContent: '<span class="text-gray-400">No actions</span>'
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: '<div class="flex items-center justify-center"><i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>Loading file numbers...</div>',
                    emptyTable: '<div class="text-center py-8"><div class="text-gray-400 mb-2"><i data-lucide="database" class="w-12 h-12 mx-auto mb-2"></i></div><h3 class="text-lg font-medium text-gray-900 mb-1">No file numbers found</h3><p class="text-gray-500">Start by generating your first MLS file number using the button above.</p></div>',
                    zeroRecords: '<div class="text-center py-8"><div class="text-gray-400 mb-2"><i data-lucide="search" class="w-12 h-12 mx-auto mb-2"></i></div><h3 class="text-lg font-medium text-gray-900 mb-1">No matching records found</h3><p class="text-gray-500">Try adjusting your search criteria.</p></div>',
                    info: "Showing _START_ to _END_ of _TOTAL_ file numbers",
                    infoEmpty: "No file numbers available",
                    infoFiltered: "(filtered from _MAX_ total file numbers)",
                    lengthMenu: "Show _MENU_ file numbers per page",
                    search: "Search file numbers:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                drawCallback: function(settings) {
                    // Reinitialize Lucide icons after table redraw
                    setTimeout(function() {
                        lucide.createIcons();
                    }, 100);
                    
                    // Log draw information for debugging
                    console.log('DataTable draw completed:', {
                        recordsTotal: settings.json?.recordsTotal || 0,
                        recordsFiltered: settings.json?.recordsFiltered || 0,
                        dataLength: settings.json?.data?.length || 0
                    });
                },
                initComplete: function(settings, json) {
                    console.log('DataTable initialized:', {
                        recordsTotal: json?.recordsTotal || 0,
                        recordsFiltered: json?.recordsFiltered || 0,
                        dataLength: json?.data?.length || 0
                    });
                    
                    // Show a message if no data is available
                    if (json && json.recordsTotal === 0) {
                        console.log('No records found in database');
                    }
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
            
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading on button
            showLoadingButton(submitBtn, originalText);
            
            // Show global loading
            showGlobalLoading('Migrating data... Please wait.');
            
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
                hideGlobalLoading();
                hideLoadingButton(submitBtn, originalText);
                
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
                hideGlobalLoading();
                hideLoadingButton(submitBtn, originalText);
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
            
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading on button
            showLoadingButton(submitBtn, originalText);
            
            // Show global loading
            showGlobalLoading('Generating file number...');
            
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
                hideGlobalLoading();
                hideLoadingButton(submitBtn, originalText);
                
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
                hideGlobalLoading();
                hideLoadingButton(submitBtn, originalText);
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
            // Show loading while fetching record details
            showGlobalLoading('Loading record details...');
            
            fetch(`{{ route("file-numbers.show", ":id") }}`.replace(':id', id))
                .then(response => response.json())
                .then(data => {
                    hideGlobalLoading();
                    document.getElementById('editId').value = data.id;
                    document.getElementById('editMlsfNo').value = data.mlsfNo || data.kangisFileNo;
                    document.getElementById('editFileName').value = data.FileName || '';
                    document.getElementById('editModal').classList.remove('hidden');
                })
                .catch(error => {
                    hideGlobalLoading();
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
            
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading on button
            showLoadingButton(submitBtn, originalText);
            
            // Show global loading
            showGlobalLoading('Updating record...');
            
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
                hideGlobalLoading();
                hideLoadingButton(submitBtn, originalText);
                
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
                hideGlobalLoading();
                hideLoadingButton(submitBtn, originalText);
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
                confirmButtonText: 'Yes, delete it!',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`{{ route("file-numbers.destroy", ":id") }}`.replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        );
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const data = result.value;
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

        function testDatabaseConnection() {
            // Show loading for database test
            showGlobalLoading('Testing database connection...');
            
            fetch('{{ route("file-numbers.test-db") }}')
                .then(response => response.json())
                .then(data => {
                    hideGlobalLoading();
                    
                    if (data.success) {
                        let message = `Database Connection Test Results:\n\n`;
                        message += `✅ Connection: ${data.connection}\n`;
                        message += `✅ Database: ${data.database_name}\n`;
                        message += `✅ Table Exists: ${data.table_exists ? 'Yes' : 'No'}\n`;
                        message += `✅ Record Count: ${data.record_count}\n`;
                        message += `✅ Server: ${data.server_info.substring(0, 50)}...\n\n`;
                        
                        if (data.columns && data.columns.length > 0) {
                            message += `Table Columns:\n`;
                            data.columns.forEach(col => {
                                message += `- ${col.COLUMN_NAME} (${col.DATA_TYPE})\n`;
                            });
                        }
                        
                        if (data.sample_records && data.sample_records.length > 0) {
                            message += `\nSample Records:\n`;
                            data.sample_records.forEach((record, index) => {
                                message += `${index + 1}. ${record.mlsfNo || record.kangisFileNo || 'No ID'}\n`;
                            });
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Database Test Successful',
                            text: message,
                            confirmButtonColor: '#10b981',
                            customClass: {
                                content: 'text-left'
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Database Test Failed',
                            text: data.error || 'Unknown error occurred',
                            confirmButtonColor: '#ef4444',
                            footer: '<small>Check the browser console for more details</small>'
                        });
                        console.error('Database test error:', data);
                    }
                })
                .catch(error => {
                    hideGlobalLoading();
                    console.error('Database test error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Test Failed',
                        text: 'Failed to connect to test endpoint: ' + error.message,
                        confirmButtonColor: '#ef4444'
                    });
                });
        }

        function debugTableData() {
            // Show loading for debug data
            showGlobalLoading('Debugging table data...');
            
            fetch('{{ route("file-numbers.debug-data") }}')
                .then(response => response.json())
                .then(data => {
                    hideGlobalLoading();
                    
                    if (data.success) {
                        console.log('Raw Data:', data.raw_data);
                        console.log('Formatted Data:', data.formatted_data);
                        
                        let message = `Debug Data Results:\n\n`;
                        message += `Raw Records Found: ${data.raw_data.length}\n`;
                        message += `Formatted Records: ${data.formatted_data.length}\n\n`;
                        
                        if (data.raw_data.length > 0) {
                            message += `Raw Data Sample:\n`;
                            data.raw_data.slice(0, 3).forEach((record, index) => {
                                message += `${index + 1}. ID: ${record.id}\n`;
                                message += `   kangisFileNo: "${record.kangisFileNo}"\n`;
                                message += `   NewKANGISFileNo: "${record.NewKANGISFileNo}"\n`;
                                message += `   FileName: "${record.FileName}"\n`;
                                message += `   mlsfNo: "${record.mlsfNo}"\n\n`;
                            });
                        }
                        
                        if (data.formatted_data.length > 0) {
                            message += `Formatted Data Sample:\n`;
                            data.formatted_data.slice(0, 3).forEach((record, index) => {
                                message += `${index + 1}. ID: ${record.id}\n`;
                                message += `   kangisFileNo: "${record.kangisFileNo}"\n`;
                                message += `   NewKANGISFileNo: "${record.NewKANGISFileNo}"\n`;
                                message += `   FileName: "${record.FileName}"\n`;
                                message += `   mlsfNo: "${record.mlsfNo}"\n\n`;
                            });
                        }
                        
                        Swal.fire({
                            icon: 'info',
                            title: 'Debug Data Results',
                            text: message,
                            confirmButtonColor: '#8b5cf6',
                            customClass: {
                                content: 'text-left'
                            },
                            width: '600px'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Debug Failed',
                            text: data.error || 'Unknown error occurred',
                            confirmButtonColor: '#ef4444'
                        });
                        console.error('Debug error:', data);
                    }
                })
                .catch(error => {
                    hideGlobalLoading();
                    console.error('Debug error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Debug Failed',
                        text: 'Failed to connect to debug endpoint: ' + error.message,
                        confirmButtonColor: '#ef4444'
                    });
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