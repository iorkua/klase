@extends('layouts.app')

@section('page-title')
    {{ __('Smart Batch Tracking') }}
@endsection

@section('content')
@include('fileindexing.css.style')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        
        <!-- Dashboard Content -->
        <div class="p-6">
            <div class="container py-6">
                <!-- Page Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold flex items-center">
                                <i data-lucide="layers" class="h-6 w-6 mr-2 text-blue-600"></i>
                                Smart Batch Tracking Interface
                            </h1>
                            <p class="text-sm text-gray-500 mt-1">{{ $PageDescription ?? 'Manage and track multiple files efficiently' }}</p>
                        </div>
                        <div class="flex items-center">
                            <span class="badge badge-blue text-lg px-4 py-2">
                                {{ $selectedFiles->count() }} Files Selected
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Selected Files Summary -->
                <div class="card mb-6">
                    <div class="p-4 border-b bg-blue-50">
                        <h3 class="text-lg font-medium flex items-center">
                            <i data-lucide="list" class="h-5 w-5 mr-2 text-blue-600"></i>
                            Selected Files for Batch Operations
                        </h3>
                    </div>
                    <div class="p-0">
                        <div style="max-height: 300px; overflow-y: auto;">
                            <table class="w-full">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr class="border-b">
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">File Number</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">File Title</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Plot Number</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">District</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Land Use</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedFiles as $file)
                                        <tr class="border-b hover:bg-gray-50" data-file-id="{{ $file['id'] }}">
                                            <td class="px-4 py-3">
                                                <span class="font-medium text-blue-600">{{ $file['file_number'] }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="truncate max-w-xs" title="{{ $file['file_title'] }}">
                                                    {{ $file['file_title'] }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $file['plot_number'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $file['district'] ?? '-' }}</td>
                                            <td class="px-4 py-3">
                                                <span class="badge badge-gray text-xs">
                                                    {{ $file['land_use_type'] ?? 'Residential' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($file['page_typing_count'] > 0)
                                                    <span class="badge badge-green">Typed</span>
                                                @elseif($file['scanning_count'] > 0)
                                                    <span class="badge badge-yellow">Scanned</span>
                                                @else
                                                    <span class="badge badge-blue">Indexed</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Smart Batch Operations Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Bulk Movement Update -->
                    <div class="card">
                        <div class="p-4 border-b bg-green-50">
                            <h3 class="text-lg font-medium flex items-center">
                                <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-green-600"></i>
                                Bulk Movement Update
                            </h3>
                        </div>
                        <div class="p-6">
                            <form id="bulkMovementForm">
                                <div class="form-group">
                                    <label for="location" class="form-label required flex items-center">
                                        <i data-lucide="building" class="h-4 w-4 mr-2"></i>
                                        New Location
                                    </label>
                                    <select class="input" id="location" name="location" required>
                                        <option value="">Select Location...</option>
                                        <option value="Registry">Registry</option>
                                        <option value="Scanning Department">Scanning Department</option>
                                        <option value="Page Typing Unit">Page Typing Unit</option>
                                        <option value="Legal Section">Legal Section</option>
                                        <option value="Survey Section">Survey Section</option>
                                        <option value="Archive">Archive</option>
                                        <option value="Storage Room A">Storage Room A</option>
                                        <option value="Storage Room B">Storage Room B</option>
                                        <option value="Director's Office">Director's Office</option>
                                        <option value="Field Office">Field Office</option>
                                        <option value="External - Court">External - Court</option>
                                        <option value="External - Client">External - Client</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="handler" class="form-label required flex items-center">
                                        <i data-lucide="user" class="h-4 w-4 mr-2"></i>
                                        Handler
                                    </label>
                                    <input type="text" class="input" id="handler" name="handler" 
                                           placeholder="Enter handler name" required>
                                </div>

                                <div class="form-group">
                                    <label for="status" class="form-label required flex items-center">
                                        <i data-lucide="activity" class="h-4 w-4 mr-2"></i>
                                        Status
                                    </label>
                                    <select class="input" id="status" name="status" required>
                                        <option value="">Select Status...</option>
                                        <option value="In Process">In Process</option>
                                        <option value="Pending">Pending</option>
                                        <option value="On Hold">On Hold</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="priority" class="form-label required flex items-center">
                                        <i data-lucide="flag" class="h-4 w-4 mr-2"></i>
                                        Priority
                                    </label>
                                    <select class="input" id="priority" name="priority" required>
                                        <option value="">Select Priority...</option>
                                        <option value="Low">Low</option>
                                        <option value="Normal">Normal</option>
                                        <option value="High">High</option>
                                        <option value="Urgent">Urgent</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="reason" class="form-label flex items-center">
                                        <i data-lucide="message-circle" class="h-4 w-4 mr-2"></i>
                                        Reason for Movement
                                    </label>
                                    <input type="text" class="input" id="reason" name="reason" 
                                           placeholder="Brief reason for this movement">
                                </div>

                                <div class="form-group">
                                    <label for="notes" class="form-label flex items-center">
                                        <i data-lucide="sticky-note" class="h-4 w-4 mr-2"></i>
                                        Additional Notes
                                    </label>
                                    <textarea class="input" id="notes" name="notes" rows="3" 
                                              placeholder="Any additional notes or special instructions"></textarea>
                                </div>

                                <button type="submit" class="btn btn-green w-full">
                                    <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i>
                                    Update All Locations
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Movement History & Analytics -->
                    <div class="card">
                        <div class="p-4 border-b bg-blue-50">
                            <h3 class="text-lg font-medium flex items-center">
                                <i data-lucide="history" class="h-5 w-5 mr-2 text-blue-600"></i>
                                Movement History & Analytics
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3 mb-6">
                                <button type="button" class="btn btn-outline w-full" id="viewHistoryBtn">
                                    <i data-lucide="eye" class="h-4 w-4 mr-2"></i>
                                    View Movement History
                                </button>
                                <button type="button" class="btn btn-outline w-full" id="exportHistoryBtn">
                                    <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                                    Export to CSV
                                </button>
                                <button type="button" class="btn btn-outline w-full" id="generateReportBtn">
                                    <i data-lucide="bar-chart-2" class="h-4 w-4 mr-2"></i>
                                    Generate Analytics Report
                                </button>
                            </div>

                            <!-- Quick Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="card p-4 text-center">
                                    <div class="text-sm text-gray-500 mb-1">Total Files</div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $selectedFiles->count() }}</div>
                                </div>
                                <div class="card p-4 text-center">
                                    <div class="text-sm text-gray-500 mb-1">Avg. Processing Time</div>
                                    <div class="text-2xl font-bold text-green-600" id="avgProcessingTime">-</div>
                                </div>
                            </div>

                            <!-- Status Distribution -->
                            <div>
                                <h6 class="text-sm font-medium text-gray-700 mb-2">Status Distribution:</h6>
                                <div class="progress mb-2">
                                    @php
                                        $typedCount = $selectedFiles->where('page_typing_count', '>', 0)->count();
                                        $scannedCount = $selectedFiles->where('scanning_count', '>', 0)->where('page_typing_count', 0)->count();
                                        $indexedCount = $selectedFiles->where('scanning_count', 0)->where('page_typing_count', 0)->count();
                                        $total = $selectedFiles->count();
                                    @endphp
                                    
                                    @if($typedCount > 0)
                                        <div class="progress-bar bg-green-500" style="width: {{ ($typedCount / $total) * 100 }}%">
                                            <span class="text-xs text-white px-2">Typed ({{ $typedCount }})</span>
                                        </div>
                                    @endif
                                    @if($scannedCount > 0)
                                        <div class="progress-bar bg-yellow-500" style="width: {{ ($scannedCount / $total) * 100 }}%">
                                            <span class="text-xs text-white px-2">Scanned ({{ $scannedCount }})</span>
                                        </div>
                                    @endif
                                    @if($indexedCount > 0)
                                        <div class="progress-bar bg-blue-500" style="width: {{ ($indexedCount / $total) * 100 }}%">
                                            <span class="text-xs text-white px-2">Indexed ({{ $indexedCount }})</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Movement History Table (Hidden initially) -->
                <div class="card hidden" id="historySection">
                    <div class="p-4 border-b bg-purple-50">
                        <h3 class="text-lg font-medium flex items-center">
                            <i data-lucide="clock" class="h-5 w-5 mr-2 text-purple-600"></i>
                            Movement History Timeline
                        </h3>
                    </div>
                    <div class="p-0">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="w-full" id="historyTable">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr class="border-b">
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Date/Time</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">File Number</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Location</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Handler</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Action</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTableBody">
                                    <!-- History data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('fileindexing.index') }}" class="btn btn-outline">
                        <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i>
                        Back to File Indexing
                    </a>
                    <div class="flex space-x-3">
                        <button type="button" class="btn btn-outline" id="printTrackingSheetBtn">
                            <i data-lucide="printer" class="h-4 w-4 mr-2"></i>
                            Print Tracking Sheets
                        </button>
                        <button type="button" class="btn btn-primary" id="refreshDataBtn">
                            <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i>
                            Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>

    <!-- Loading Dialog -->
    <div class="dialog-overlay hidden" id="loadingModal">
        <div class="dialog">
            <div class="dialog-content text-center p-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <i data-lucide="loader" class="h-8 w-8 text-blue-600 animate-spin"></i>
                </div>
                <h3 class="text-lg font-medium mb-2">Processing Batch Operation</h3>
                <p class="text-sm text-gray-500">Please wait while we update the file locations...</p>
            </div>
        </div>
    </div>

    <!-- Success Dialog -->
    <div class="dialog-overlay hidden" id="successModal">
        <div class="dialog">
            <div class="dialog-content text-center p-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <i data-lucide="check" class="h-8 w-8 text-green-600"></i>
                </div>
                <h3 class="text-lg font-medium mb-2">Operation Successful</h3>
                <p class="text-sm text-gray-500 mb-4" id="successMessage">File locations have been updated successfully.</p>
                <button type="button" class="btn btn-primary" id="closeSuccessBtn">Close</button>
            </div>
        </div>
    </div>

    <!-- Error Dialog -->
    <div class="dialog-overlay hidden" id="errorModal">
        <div class="dialog">
            <div class="dialog-content text-center p-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                    <i data-lucide="x" class="h-8 w-8 text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium mb-2">Operation Failed</h3>
                <p class="text-sm text-gray-500 mb-4" id="errorMessage">An error occurred while processing the request.</p>
                <button type="button" class="btn btn-primary" id="closeErrorBtn">Close</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectedFileIds = @json($selectedFiles->pluck('id')->toArray());
        
        // Bulk Movement Form Submission
        document.getElementById('bulkMovementForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                files: selectedFileIds,
                location: document.getElementById('location').value,
                handler: document.getElementById('handler').value,
                status: document.getElementById('status').value,
                priority: document.getElementById('priority').value,
                reason: document.getElementById('reason').value,
                notes: document.getElementById('notes').value,
                _token: '{{ csrf_token() }}'
            };
            
            // Show loading modal
            document.getElementById('loadingModal').classList.remove('hidden');
            
            fetch('{{ route("fileindexing.bulk-movement-update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingModal').classList.add('hidden');
                
                if (data.success) {
                    document.getElementById('successMessage').textContent = data.message;
                    document.getElementById('successModal').classList.remove('hidden');
                    
                    // Reset form
                    document.getElementById('bulkMovementForm').reset();
                    
                    // Refresh history if visible
                    if (!document.getElementById('historySection').classList.contains('hidden')) {
                        loadMovementHistory();
                    }
                } else {
                    document.getElementById('errorMessage').textContent = data.message || 'Failed to update file locations';
                    document.getElementById('errorModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                document.getElementById('loadingModal').classList.add('hidden');
                document.getElementById('errorMessage').textContent = 'An error occurred while processing the request';
                document.getElementById('errorModal').classList.remove('hidden');
            });
        });
        
        // View Movement History
        document.getElementById('viewHistoryBtn').addEventListener('click', function() {
            const historySection = document.getElementById('historySection');
            if (!historySection.classList.contains('hidden')) {
                historySection.classList.add('hidden');
                this.innerHTML = '<i data-lucide="eye" class="h-4 w-4 mr-2"></i> View Movement History';
                lucide.createIcons(); // Re-initialize icons
            } else {
                loadMovementHistory();
                historySection.classList.remove('hidden');
                this.innerHTML = '<i data-lucide="eye-off" class="h-4 w-4 mr-2"></i> Hide Movement History';
                lucide.createIcons(); // Re-initialize icons
            }
        });
        
        // Load Movement History
        function loadMovementHistory() {
            fetch('{{ route("fileindexing.movement-history") }}?' + new URLSearchParams({
                files: selectedFileIds.join(',')
            }))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderMovementHistory(data.movement_history);
                } else {
                    console.error('Failed to load movement history');
                }
            })
            .catch(error => {
                console.error('Error loading movement history:', error);
            });
        }
        
        // Render Movement History
        function renderMovementHistory(history) {
            const tbody = document.getElementById('historyTableBody');
            tbody.innerHTML = '';
            
            if (history.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No movement history found for selected files
                        </td>
                    </tr>
                `;
                return;
            }
            
            history.forEach(function(movement) {
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="text-sm">${movement.date || ''} ${movement.time || ''}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-blue-600">${movement.file_number || ''}</div>
                        <div class="text-sm text-gray-500">${movement.file_title || ''}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge badge-blue">${movement.location || ''}</span>
                    </td>
                    <td class="px-4 py-3 text-sm">${movement.handler || ''}</td>
                    <td class="px-4 py-3">
                        <span class="badge badge-gray">${movement.action || ''}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-600">${movement.notes || ''}</div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Export History
        document.getElementById('exportHistoryBtn').addEventListener('click', function() {
            const url = '{{ route("fileindexing.export-movement-history") }}?files=' + selectedFileIds.join(',');
            window.open(url, '_blank');
        });
        
        // Generate Analytics Report
        document.getElementById('generateReportBtn').addEventListener('click', function() {
            document.getElementById('errorMessage').innerHTML = `
                <div class="text-center">
                    <div class="text-lg font-medium mb-2">Analytics Report</div>
                    <div class="text-sm">Analytics reporting feature coming soon!</div>
                    <div class="text-xs text-gray-500 mt-2">This will include detailed movement patterns and processing time analysis.</div>
                </div>
            `;
            document.getElementById('errorModal').classList.remove('hidden');
        });
        
        // Print Tracking Sheets
        document.getElementById('printTrackingSheetBtn').addEventListener('click', function() {
            const url = '{{ route("fileindexing.batch-tracking-sheet") }}?files=' + selectedFileIds.join(',');
            window.open(url, '_blank');
        });
        
        // Refresh Data
        document.getElementById('refreshDataBtn').addEventListener('click', function() {
            location.reload();
        });
        
        // Close modal handlers
        document.getElementById('closeSuccessBtn').addEventListener('click', function() {
            document.getElementById('successModal').classList.add('hidden');
        });
        
        document.getElementById('closeErrorBtn').addEventListener('click', function() {
            document.getElementById('errorModal').classList.add('hidden');
        });
        
        // Initialize Lucide icons
        lucide.createIcons();
    });
    </script>
@endsection