@extends('layouts.app')
@section('page-title')
    {{ __('Blind Scannings') }}
@endsection

@section('content')
    @include('pagetyping.css.style')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">
          
          <div class="container mx-auto py-6 space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col space-y-2">
              <h1 class="text-2xl font-bold tracking-tight">Blind Scannings</h1>
              <p class="text-muted-foreground">Upload raw scanned documents without indexing</p>
            </div>
        
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
              <!-- Total Blind Scans -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">Total Blind Scans</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="total-count">{{ $stats['total_blind_scans'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">All uploaded blind scans</p>
                </div>
              </div>

              <!-- Pending Scans -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">Pending Scans</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="pending-count">{{ $stats['pending_scans'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Awaiting conversion</p>
                </div>
              </div>

              <!-- Converted Scans -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">Converted Scans</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="converted-count">{{ $stats['converted_scans'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Successfully converted</p>
                </div>
              </div>

              <!-- Today's Uploads -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">Today's Uploads</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="today-count">{{ $stats['today_uploads'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Uploaded today</p>
                </div>
              </div>
            </div>
        
            <!-- Tabs -->
            <div class="tabs">
              <div class="tabs-list grid w-full md:w-auto grid-cols-4">
                <!-- <button class="tab" role="tab" aria-selected="true" data-tab="pending">Pending Scans</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="converted">Converted</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="archived">Archived</button> -->
                <button class="tab" role="tab" aria-selected="false" data-tab="upload" id="upload-tab">Upload Scans</button>
              </div>
        
              <!-- Pending Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="false" data-tab-content="pending">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Pending Blind Scans</h2>
                        <p class="text-sm text-muted-foreground">Raw scans awaiting conversion to upload workflow</p>
                      </div>
                      <div class="flex items-center gap-2">
                        <button class="btn btn-outline btn-sm" id="refresh-pending-btn">
                          <i data-lucide="refresh-cw" class="h-4 w-4 mr-1"></i>
                          Refresh
                        </button>
                        <button class="btn btn-primary btn-sm" id="create-folder-btn">
                          <i data-lucide="folder-plus" class="h-4 w-4 mr-1"></i>
                          Create Folder
                        </button>
                        <button class="btn btn-primary btn-sm" id="browse-folder-btn">
                          <i data-lucide="folder-open" class="h-4 w-4 mr-1"></i>
                          Browse Folder
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div id="pending-scans-list" class="rounded-md border divide-y">
                      <!-- Pending scans will be added here dynamically -->
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- Converted Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="converted">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Converted Scans</h2>
                        <p class="text-sm text-muted-foreground">Scans that have been converted to upload workflow</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search converted..." class="input w-full pl-8" id="search-converted">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div id="converted-scans-list" class="rounded-md border divide-y">
                      <!-- Converted scans will be added here dynamically -->
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- Archived Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="archived">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Archived Scans</h2>
                        <p class="text-sm text-muted-foreground">Scans that have been archived</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search archived..." class="input w-full pl-8" id="search-archived">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div id="archived-scans-list" class="rounded-md border divide-y">
                      <!-- Archived scans will be added here dynamically -->
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- Upload Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="upload">
                <div class="card" id="upload-card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Upload Blind Scans</h2>
                        <p class="text-sm text-muted-foreground">Upload raw scanned documents without indexing</p>
                      </div>
                      <button class="btn btn-outline btn-sm" id="cancel-upload-btn">
                        <i data-lucide="x" class="h-4 w-4 mr-1"></i>
                        Cancel
                      </button>
                    </div>
                  </div>
                  <div class="p-6">
                    <form id="upload-blind-scan-form" enctype="multipart/form-data" class="space-y-6">
                      <!-- File Upload Area -->
                      <div>
                        <label class="block text-sm font-medium mb-2">Select Files</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition-colors cursor-pointer" id="file-drop-zone">
                          <i data-lucide="upload" class="h-12 w-12 mx-auto text-gray-400 mb-4"></i>
                          <p class="text-lg font-medium text-gray-600 mb-2">Drag and drop files here, or click to browse</p>
                          <p class="text-sm text-gray-500">Supports PDF, JPG, PNG, TIFF (max 10MB each)</p>
                          <input type="file" id="blind-scan-files" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.tiff" class="hidden">
                        </div>
                        <div id="selected-files-list" class="mt-4 space-y-2 hidden">
                          <!-- Selected files will be shown here -->
                        </div>
                      </div>

                      <!-- Document Details -->
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                          <label class="block text-sm font-medium mb-2">Paper Size</label>
                          <select name="paper_size" class="input">
                            <option value="">Select Paper Size</option>
                            <option value="A4">A4</option>
                            <option value="A5">A5</option>
                            <option value="A3">A3</option>
                            <option value="Letter">Letter</option>
                            <option value="Legal">Legal</option>
                            <option value="Custom">Custom</option>
                          </select>
                        </div>
                        <div>
                          <label class="block text-sm font-medium mb-2">Document Type</label>
                          <select name="document_type" class="input">
                            <option value="">Select Document Type</option>
                            <option value="Certificate">Certificate</option>
                            <option value="Deed">Deed</option>
                            <option value="Letter">Letter</option>
                            <option value="Application Form">Application Form</option>
                            <option value="Map">Map</option>
                            <option value="Survey Plan">Survey Plan</option>
                            <option value="Receipt">Receipt</option>
                            <option value="Other">Other</option>
                          </select>
                        </div>
                      </div>

                      <!-- Notes -->
                      <div>
                        <label class="block text-sm font-medium mb-2">Notes (Optional)</label>
                        <textarea name="notes" class="textarea" rows="4" placeholder="Add any notes about these scans..."></textarea>
                      </div>

                      <!-- Upload Progress -->
                      <div id="upload-progress" class="hidden">
                        <div class="flex justify-between text-sm mb-2">
                          <span>Uploading files...</span>
                          <span id="upload-percentage">0%</span>
                        </div>
                        <div class="progress">
                          <div class="progress-bar" id="upload-progress-bar" style="width: 0%"></div>
                        </div>
                      </div>

                      <!-- Submit Button -->
                      <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary btn-lg" id="submit-upload-btn">
                          <i data-lucide="upload" class="h-4 w-4 mr-2"></i>
                          Upload Files
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        
        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>

    <!-- Convert to Upload Modal -->
    <div id="convert-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="convert-modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="convert-modal-backdrop"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="convert-modal-title">
                  Convert to Upload Workflow
                </h3>
                <p class="text-sm text-gray-500 mt-1">Link this blind scan to an indexed file</p>
                
                <form id="convert-to-upload-form" class="mt-4">
                  <div class="space-y-4">
                    <input type="hidden" id="convert-blind-scan-id" name="blind_scan_id">
                    
                    <!-- File Selection -->
                    <div>
                      <label class="block text-sm font-medium mb-2">Select Indexed File</label>
                      <select id="file-indexing-select" name="file_indexing_id" class="input" required>
                        <option value="">Choose an indexed file...</option>
                      </select>
                    </div>

                    <!-- Blind Scan Details -->
                    <div id="blind-scan-details" class="bg-gray-50 p-4 rounded-lg">
                      <h4 class="font-medium mb-2">Blind Scan Details:</h4>
                      <div class="text-sm space-y-1">
                        <p><strong>Temp ID:</strong> <span id="detail-temp-id">-</span></p>
                        <p><strong>Filename:</strong> <span id="detail-filename">-</span></p>
                        <p><strong>Document Type:</strong> <span id="detail-doc-type">-</span></p>
                        <p><strong>Paper Size:</strong> <span id="detail-paper-size">-</span></p>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="submit" form="convert-to-upload-form" class="btn btn-primary">
              <i data-lucide="arrow-right" class="h-4 w-4 mr-2"></i>
              Convert to Upload
            </button>
            <button type="button" class="btn btn-outline mr-3" id="cancel-convert-btn">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="preview-modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="preview-modal-backdrop"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="preview-modal-title">
                  Preview Scans
                </h3>
                <div id="preview-content" class="mt-4">
                  <!-- Preview content will be loaded here -->
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" class="btn btn-outline" id="close-preview-btn">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Application state
        let state = {
          activeTab: 'pending',
          selectedFiles: [],
          currentPage: 1,
          perPage: 15,
          filters: {}
        };

        // Sample data for demonstration
        const sampleBlindScans = {
          pending: [
            {
              id: 1,
              temp_file_id: 'BLIND-2024-001',
              original_filename: 'certificate_scan_001.pdf',
              document_type: 'Certificate',
              paper_size: 'A4',
              status: 'pending',
              created_at: '2024-01-15T10:30:00Z',
              uploader: { name: 'John Doe' }
            },
            {
              id: 2,
              temp_file_id: 'BLIND-2024-002',
              original_filename: 'deed_documents.pdf',
              document_type: 'Deed',
              paper_size: 'A4',
              status: 'pending',
              created_at: '2024-01-15T11:15:00Z',
              uploader: { name: 'Jane Smith' }
            }
          ],
          converted: [
            {
              id: 3,
              temp_file_id: 'BLIND-2024-003',
              original_filename: 'survey_plan.pdf',
              document_type: 'Survey Plan',
              paper_size: 'A3',
              status: 'converted',
              created_at: '2024-01-14T14:20:00Z',
              uploader: { name: 'Mike Johnson' },
              converted_to: 'KNML 12345'
            }
          ],
          archived: []
        };

        // DOM Elements
        const elements = {
          // Tabs
          tabs: document.querySelectorAll('[role="tab"]'),
          tabContents: document.querySelectorAll('[role="tabpanel"]'),
          uploadTab: document.getElementById('upload-tab'),
          
          // Lists
          pendingScansList: document.getElementById('pending-scans-list'),
          convertedScansList: document.getElementById('converted-scans-list'),
          archivedScansList: document.getElementById('archived-scans-list'),
          
          // Counters
          totalCount: document.getElementById('total-count'),
          pendingCount: document.getElementById('pending-count'),
          convertedCount: document.getElementById('converted-count'),
          todayCount: document.getElementById('today-count')
        };

        // Helper functions
        function formatDate(dateString) {
          return new Date(dateString).toLocaleDateString();
        }

        function formatFileSize(bytes) {
          if (bytes === 0) return '0 Bytes';
          const k = 1024;
          const sizes = ['Bytes', 'KB', 'MB', 'GB'];
          const i = Math.floor(Math.log(bytes) / Math.log(k));
          return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // UI update functions
        function updateUI() {
          updateStats();
          renderPendingScans();
          renderConvertedScans();
          renderArchivedScans();
        }

        function updateStats() {
          // Update tabs
          elements.tabs.forEach(tab => {
            const tabId = tab.getAttribute('data-tab');
            tab.setAttribute('aria-selected', tabId === state.activeTab);
          });
          
          elements.tabContents.forEach(content => {
            const contentId = content.getAttribute('data-tab-content');
            content.setAttribute('aria-hidden', contentId !== state.activeTab);
          });

          // Update counters
          elements.totalCount.textContent = sampleBlindScans.pending.length + sampleBlindScans.converted.length + sampleBlindScans.archived.length;
          elements.pendingCount.textContent = sampleBlindScans.pending.length;
          elements.convertedCount.textContent = sampleBlindScans.converted.length;
          elements.todayCount.textContent = sampleBlindScans.pending.filter(scan => 
            new Date(scan.created_at).toDateString() === new Date().toDateString()
          ).length;
        }

        function renderPendingScans() {
          elements.pendingScansList.innerHTML = '';
          
          if (sampleBlindScans.pending.length === 0) {
            elements.pendingScansList.innerHTML = `
              <div class="rounded-md border p-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                  <i data-lucide="file-text" class="h-6 w-6"></i>
                </div>
                <h3 class="mb-2 text-lg font-medium">No pending blind scans</h3>
                <p class="mb-4 text-sm text-muted-foreground">Upload some scans to get started</p>
              </div>
            `;
            lucide.createIcons();
            return;
          }
          
          sampleBlindScans.pending.forEach(scan => {
            const scanItem = document.createElement('div');
            scanItem.className = 'flex items-center justify-between p-4';
            scanItem.innerHTML = `
              <div class="flex items-center gap-3">
                <i data-lucide="file-text" class="h-8 w-8 text-orange-500"></i>
                <div>
                  <p class="text-blue-600 font-medium">${scan.temp_file_id}</p>
                  <p class="text-sm text-gray-700 mt-0.5">${scan.original_filename}</p>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="badge badge-secondary text-xs">${scan.document_type || 'Unknown'}</span>
                    <span class="badge badge-secondary text-xs">${scan.paper_size || 'Unknown'}</span>
                    <span class="text-xs text-muted-foreground">${formatDate(scan.created_at)}</span>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button class="btn btn-ghost btn-sm preview-scan" data-id="${scan.id}" title="Preview Scans">
                  <i data-lucide="eye" class="h-4 w-4"></i>
                </button>
                <button class="btn btn-outline btn-sm convert-scan" data-id="${scan.id}">
                  <i data-lucide="arrow-right" class="h-3.5 w-3.5 mr-1"></i>
                  Convert
                </button>
              </div>
            `;
            
            elements.pendingScansList.appendChild(scanItem);
          });
          
          // Initialize icons for the new elements
          lucide.createIcons();
          
          // Add event listeners
          document.querySelectorAll('.convert-scan').forEach(btn => {
            btn.addEventListener('click', () => {
              const scanId = btn.getAttribute('data-id');
              showConvertModal(scanId);
            });
          });

          document.querySelectorAll('.preview-scan').forEach(btn => {
            btn.addEventListener('click', () => {
              const scanId = btn.getAttribute('data-id');
              showPreviewModal(scanId);
            });
          });
        }

        function renderConvertedScans() {
          elements.convertedScansList.innerHTML = '';
          
          if (sampleBlindScans.converted.length === 0) {
            elements.convertedScansList.innerHTML = `
              <div class="rounded-md border p-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                  <i data-lucide="check-circle" class="h-6 w-6"></i>
                </div>
                <h3 class="mb-2 text-lg font-medium">No converted scans</h3>
                <p class="mb-4 text-sm text-muted-foreground">Convert pending scans to see them here</p>
              </div>
            `;
            lucide.createIcons();
            return;
          }
          
          sampleBlindScans.converted.forEach(scan => {
            const scanItem = document.createElement('div');
            scanItem.className = 'flex items-center justify-between p-4';
            scanItem.innerHTML = `
              <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="h-8 w-8 text-green-500"></i>
                <div>
                  <p class="text-blue-600 font-medium">${scan.temp_file_id}</p>
                  <p class="text-sm text-gray-700 mt-0.5">${scan.original_filename}</p>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="badge bg-green-500 text-white text-xs">Converted to ${scan.converted_to}</span>
                    <span class="text-xs text-muted-foreground">${formatDate(scan.created_at)}</span>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button class="btn btn-ghost btn-sm" title="View Details">
                  <i data-lucide="eye" class="h-4 w-4"></i>
                </button>
              </div>
            `;
            
            elements.convertedScansList.appendChild(scanItem);
          });
          
          lucide.createIcons();
        }

        function renderArchivedScans() {
          elements.archivedScansList.innerHTML = `
            <div class="rounded-md border p-8 text-center">
              <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                <i data-lucide="archive" class="h-6 w-6"></i>
              </div>
              <h3 class="mb-2 text-lg font-medium">No archived scans</h3>
              <p class="mb-4 text-sm text-muted-foreground">Archived scans will appear here</p>
            </div>
          `;
          lucide.createIcons();
        }

        // Event handlers
        function switchTab(tabId) {
          state.activeTab = tabId;
          updateUI();
        }

        function showConvertModal(scanId) {
          const scan = sampleBlindScans.pending.find(s => s.id == scanId);
          if (!scan) return;

          // Populate modal
          document.getElementById('convert-blind-scan-id').value = scanId;
          document.getElementById('detail-temp-id').textContent = scan.temp_file_id;
          document.getElementById('detail-filename').textContent = scan.original_filename;
          document.getElementById('detail-doc-type').textContent = scan.document_type || '-';
          document.getElementById('detail-paper-size').textContent = scan.paper_size || '-';

          // Show modal
          document.getElementById('convert-modal').classList.remove('hidden');
          document.getElementById('convert-modal').setAttribute('aria-hidden', 'false');
        }

        function hideConvertModal() {
          document.getElementById('convert-modal').classList.add('hidden');
          document.getElementById('convert-modal').setAttribute('aria-hidden', 'true');
        }

        function showPreviewModal(scanId) {
          const scan = sampleBlindScans.pending.find(s => s.id == scanId);
          if (!scan) return;

          // Show preview content
          document.getElementById('preview-content').innerHTML = `
            <div class="text-center p-8">
              <i data-lucide="file-text" class="h-16 w-16 mx-auto text-gray-400 mb-4"></i>
              <h3 class="text-lg font-medium mb-2">${scan.original_filename}</h3>
              <p class="text-sm text-gray-500">Preview functionality will be implemented with actual file paths</p>
            </div>
          `;

          // Show modal
          document.getElementById('preview-modal').classList.remove('hidden');
          document.getElementById('preview-modal').setAttribute('aria-hidden', 'false');
          lucide.createIcons();
        }

        function hidePreviewModal() {
          document.getElementById('preview-modal').classList.add('hidden');
          document.getElementById('preview-modal').setAttribute('aria-hidden', 'true');
        }

        // File upload functions
        function handleFileSelection(files) {
          state.selectedFiles = Array.from(files);
          displaySelectedFiles();
        }

        function displaySelectedFiles() {
          const container = document.getElementById('selected-files-list');
          
          if (state.selectedFiles.length === 0) {
            container.classList.add('hidden');
            return;
          }

          container.classList.remove('hidden');
          container.innerHTML = '';

          state.selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 border rounded-md';
            fileItem.innerHTML = `
              <div class="flex items-center gap-3">
                <i data-lucide="file-text" class="h-5 w-5 text-gray-400"></i>
                <div>
                  <div class="font-medium text-sm">${file.name}</div>
                  <div class="text-xs text-gray-500">${formatFileSize(file.size)}</div>
                </div>
              </div>
              <button type="button" class="text-red-500 hover:text-red-700 p-1" onclick="removeFile(${index})">
                <i data-lucide="x" class="h-4 w-4"></i>
              </button>
            `;
            container.appendChild(fileItem);
          });

          lucide.createIcons();
        }

        function removeFile(index) {
          state.selectedFiles.splice(index, 1);
          displaySelectedFiles();
        }

        async function uploadBlindScans() {
          if (state.selectedFiles.length === 0) {
            alert('Please select at least one file to upload.');
            return;
          }

          const formData = new FormData();
          const form = document.getElementById('upload-blind-scan-form');
          
          // Add files
          state.selectedFiles.forEach(file => {
            formData.append('files[]', file);
          });

          // Add other form data
          formData.append('paper_size', form.paper_size.value);
          formData.append('document_type', form.document_type.value);
          formData.append('notes', form.notes.value);

          // Show progress
          document.getElementById('upload-progress').classList.remove('hidden');
          document.getElementById('submit-upload-btn').disabled = true;

          try {
            // Simulate upload progress
            for (let i = 0; i <= 100; i += 10) {
              document.getElementById('upload-percentage').textContent = i + '%';
              document.getElementById('upload-progress-bar').style.width = i + '%';
              await new Promise(resolve => setTimeout(resolve, 100));
            }

            // Simulate successful upload
            alert('Files uploaded successfully!');
            
            // Reset form
            form.reset();
            state.selectedFiles = [];
            displaySelectedFiles();
            
            // Switch back to pending tab
            switchTab('pending');
            
          } catch (error) {
            console.error('Upload error:', error);
            alert('Upload failed. Please try again.');
          } finally {
            document.getElementById('upload-progress').classList.add('hidden');
            document.getElementById('submit-upload-btn').disabled = false;
          }
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
          // Add tab event listeners
          elements.tabs.forEach(tab => {
            tab.addEventListener('click', () => {
              const tabId = tab.getAttribute('data-tab');
              switchTab(tabId);
            });
          });

          // File upload event listeners
          const dropZone = document.getElementById('file-drop-zone');
          const fileInput = document.getElementById('blind-scan-files');

          dropZone.addEventListener('click', () => fileInput.click());
          dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
          });
          dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
          });
          dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
            handleFileSelection(e.dataTransfer.files);
          });

          fileInput.addEventListener('change', (e) => {
            handleFileSelection(e.target.files);
          });

          // Form submit
          document.getElementById('upload-blind-scan-form').addEventListener('submit', (e) => {
            e.preventDefault();
            uploadBlindScans();
          });

          // Modal event listeners
          document.getElementById('cancel-convert-btn').addEventListener('click', hideConvertModal);
          document.getElementById('convert-modal-backdrop').addEventListener('click', hideConvertModal);
          document.getElementById('close-preview-btn').addEventListener('click', hidePreviewModal);
          document.getElementById('preview-modal-backdrop').addEventListener('click', hidePreviewModal);

          // Other button listeners
          document.getElementById('cancel-upload-btn').addEventListener('click', () => {
            switchTab('pending');
          });

          document.getElementById('refresh-pending-btn').addEventListener('click', () => {
            updateUI();
          });

          document.getElementById('create-folder-btn').addEventListener('click', () => {
            alert('Create Folder functionality will be implemented');
          });

          document.getElementById('browse-folder-btn').addEventListener('click', () => {
            alert('Browse Folder functionality will be implemented');
          });

          // Convert form submit
          document.getElementById('convert-to-upload-form').addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Convert functionality will be implemented');
            hideConvertModal();
          });

          // Initial UI update
          updateUI();
        });
    </script>
@endsection