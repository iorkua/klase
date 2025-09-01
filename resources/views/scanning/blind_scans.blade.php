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
              <p class="text-muted-foreground">Upload raw scanned documents </p>
            </div>
        
           
       
            </div>
          </div>
        
        </div>

        <!-- Footer -->
        @include('admin.footer')
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