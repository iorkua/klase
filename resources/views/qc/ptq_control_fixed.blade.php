@extends('layouts.app')
@section('page-title')
    {{ __('PTQ Control Dashboard') }}
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
              <h1 class="text-2xl font-bold tracking-tight">PTQ Control</h1>
              <p class="text-muted-foreground">Quality Control for Page Typing - Review and correct typed pages</p>
            </div>
        
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
              <!-- Pending QC -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">Pending QC</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="pending-count">{{ $stats['pending_qc'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files waiting for QC review</p>
                </div>
              </div>
        
              <!-- QC In Progress -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">QC In Progress</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="in-progress-count">{{ $stats['qc_in_progress'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files currently under QC review</p>
                </div>
              </div>
        
              <!-- QC Completed -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">QC Completed</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="completed-count">{{ $stats['qc_completed'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files completed QC review</p>
                </div>
              </div>
            </div>
        
            <!-- Tabs -->
            <div class="tabs">
              <div class="tabs-list grid w-full md:w-auto grid-cols-4">
                <button class="tab" role="tab" aria-selected="true" data-tab="pending">Pending QC</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="in-progress">QC In Progress</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="completed">QC Completed</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="qc-review" aria-disabled="true" id="qc-review-tab">QC Review</button>
              </div>
        
              <!-- Pending QC Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="false" data-tab-content="pending">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Files Pending QC Review</h2>
                        <p class="text-sm text-muted-foreground">Select a file to begin QC review of its typed pages</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search files..." class="input w-full pl-8">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div id="pending-files-list" class="rounded-md border divide-y">
                      <div class="p-8 text-center">
                        <div class="animate-spin h-8 w-8 border-2 border-blue-500 border-t-transparent rounded-full mx-auto mb-4"></div>
                        <p class="text-sm text-muted-foreground">Loading pending QC files...</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- QC In Progress Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="in-progress">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Files QC In Progress</h2>
                        <p class="text-sm text-muted-foreground">Files that are partially reviewed</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search files..." class="input w-full pl-8">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div id="in-progress-files-list" class="rounded-md border divide-y">
                      <!-- QC in progress files will be added here dynamically -->
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- QC Completed Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="completed">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">QC Completed Files</h2>
                        <p class="text-sm text-muted-foreground">Files that have been fully QC reviewed</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search files..." class="input w-full pl-8">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div class="overflow-x-auto">
                      <table class="w-full border-collapse">
                        <thead>
                          <tr class="border-b bg-muted/20">
                            <th class="text-left p-3 font-medium">File Number</th>
                            <th class="text-left p-3 font-medium">File Name</th>
                            <th class="text-left p-3 font-medium">Date QC Reviewed</th>
                            <th class="text-left p-3 font-medium">QC Reviewed By</th>
                            <th class="text-left p-3 font-medium">QC Status</th>
                            <th class="text-left p-3 font-medium">Pages</th>
                            <th class="text-left p-3 font-medium">Actions</th>
                          </tr>
                        </thead>
                        <tbody id="completed-files-table-body">
                          <!-- Table rows will be added here dynamically -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- QC Review Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="qc-review">
                <div class="card" id="qc-review-card">
                  <!-- QC Review content will be added here dynamically -->
                </div>
              </div>
            </div>
          </div>
        
        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>

    <!-- QC Override Modal -->
    <div id="qc-override-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="override-modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="override-modal-backdrop"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                <i data-lucide="alert-triangle" class="h-6 w-6 text-yellow-600"></i>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="override-modal-title">
                  QC Override
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    You are about to override the page typing for the selected page. Please provide a reason for this correction.
                  </p>
                </div>
                
                <form id="qc-override-form" class="mt-4">
                  <div class="space-y-4">
                    <input type="hidden" id="override-page-id" name="page_typing_id">
                    
                    <!-- Override Reason -->
                    <div>
                      <label class="block text-sm font-medium mb-2">Override Reason</label>
                      <textarea id="override-note" name="override_note" class="textarea" rows="4" placeholder="Explain why you are overriding this page typing..." required></textarea>
                    </div>

                    <!-- Current Page Info -->
                    <div id="override-page-info" class="bg-gray-50 p-4 rounded-lg">
                      <h4 class="font-medium mb-2">Current Page Details:</h4>
                      <div id="override-page-details" class="text-sm space-y-1">
                        <!-- Current page details will be listed here -->
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="submit" form="qc-override-form" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
              <i data-lucide="shield-check" class="h-4 w-4 mr-2"></i>
              Override & Correct
            </button>
            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" id="cancel-override-btn">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- PTQ Control Dashboard JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <!-- PTQ Control JavaScript with Real Backend Integration -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Application state
        let state = {
          activeTab: 'pending',
          selectedFile: null,
          currentPage: 1,
          qcProgress: 0,
          zoomLevel: 100,
          rotation: 0,
          showFolderView: false,
          selectedPageInFolder: null,
          pageType: '1',
          pageSubType: '1',
          serialNo: '01',
          expandedFiles: [],
          batchMode: false,
          batchQCPages: {},
          currentBatchPageIndex: null,
          batchSubmitReady: false,
          batchProgress: 0,
          batchProcessing: false,
          processedPages: {},
          bookletMode: false,
          currentBooklet: null,
          bookletStartPage: null,
          bookletPages: {},
          bookletCounter: 'a',
          expandedFileId: null
        };

        // Data arrays for QC files - populated from backend
        let pendingQCFiles = [];
        let inProgressQCFiles = [];
        let completedQCFiles = [];
        const samplePages = {};

        // DOM Elements
        const elements = {
          tabs: document.querySelectorAll('[role="tab"]'),
          tabContents: document.querySelectorAll('[role="tabpanel"]'),
          qcReviewTab: document.getElementById('qc-review-tab'),
          pendingFilesList: document.getElementById('pending-files-list'),
          inProgressFilesList: document.getElementById('in-progress-files-list'),
          completedFilesList: document.getElementById('completed-files-list'),
          qcReviewCard: document.getElementById('qc-review-card'),
          pendingCount: document.getElementById('pending-count'),
          inProgressCount: document.getElementById('in-progress-count'),
          completedCount: document.getElementById('completed-count')
        };

        // API functions for backend integration
        async function loadPendingFiles() {
          try {
            const response = await fetch('/ptq-control/list-pending');
            const data = await response.json();
            
            if (data.success) {
              pendingQCFiles = data.data.map(file => ({
                id: file.id,
                fileNumber: file.file_number,
                name: file.file_title || 'Untitled File',
                type: file.land_use_type || 'Unknown',
                pages: file.total_pages_count || 0,
                completed: file.total_pages_count || 0,
                qc_reviewed: 0,
                date: new Date(file.created_at).toISOString().split('T')[0],
                status: 'Pending QC',
                pagetyped_by: file.pagetyped_by_name || 'Unknown',
                pagetyped_at: file.last_pagetyped_at
              }));
              
              renderPendingQCFiles();
              elements.pendingCount.textContent = pendingQCFiles.length;
            } else {
              console.error('Failed to load pending files:', data.message);
              showError('Failed to load pending QC files');
            }
          } catch (error) {
            console.error('Error loading pending files:', error);
            showError('Error loading pending QC files');
          }
        }

        async function loadInProgressFiles() {
          try {
            const response = await fetch('/ptq-control/list-in-progress');
            const data = await response.json();
            
            if (data.success) {
              inProgressQCFiles = data.data.map(file => ({
                id: file.id,
                fileNumber: file.file_number,
                name: file.file_title || 'Untitled File',
                type: file.land_use_type || 'Unknown',
                pages: file.total_pages_count || 0,
                completed: file.total_pages_count || 0,
                qc_reviewed: file.reviewed_pages_count || 0,
                date: new Date(file.created_at).toISOString().split('T')[0],
                status: 'QC In Progress',
                pagetyped_by: file.pagetyped_by_name || 'Unknown',
                pagetyped_at: file.last_pagetyped_at,
                qc_progress: file.qc_progress || 0
              }));
              
              renderInProgressQCFiles();
              elements.inProgressCount.textContent = inProgressQCFiles.length;
            }
          } catch (error) {
            console.error('Error loading in-progress files:', error);
          }
        }

        async function loadCompletedFiles() {
          try {
            const response = await fetch('/ptq-control/list-completed');
            const data = await response.json();
            
            if (data.success) {
              completedQCFiles = data.data.map(file => ({
                id: file.id,
                fileNumber: file.file_number,
                name: file.file_title || 'Untitled File',
                type: file.land_use_type || 'Unknown',
                pages: file.total_pages_count || 0,
                completed: file.total_pages_count || 0,
                qc_reviewed: file.total_pages_count || 0,
                date: new Date(file.created_at).toISOString().split('T')[0],
                status: 'QC Completed',
                pagetyped_by: file.pagetyped_by_name || 'Unknown',
                pagetyped_at: file.last_pagetyped_at,
                qc_reviewed_by: file.qc_reviewed_by_name || 'QC Team',
                qc_reviewed_at: file.qc_completed_at,
                processedPages: file.processedPages || []
              }));
              
              renderCompletedQCFilesTable();
              elements.completedCount.textContent = completedQCFiles.length;
            }
          } catch (error) {
            console.error('Error loading completed files:', error);
          }
        }

        function showError(message) {
          elements.pendingFilesList.innerHTML = `
            <div class="rounded-md border p-8 text-center">
              <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <i data-lucide="alert-circle" class="h-6 w-6 text-red-600"></i>
              </div>
              <h3 class="mb-2 text-lg font-medium text-red-800">Error Loading Files</h3>
              <p class="mb-4 text-sm text-red-600">${message}</p>
              <button class="btn btn-outline btn-sm" onclick="loadPendingFiles()">
                <i data-lucide="refresh-cw" class="h-4 w-4 mr-1"></i>
                Retry
              </button>
            </div>
          `;
          lucide.createIcons();
        }

        function renderPendingQCFiles() {
          elements.pendingFilesList.innerHTML = '';
          
          if (pendingQCFiles.length === 0) {
            elements.pendingFilesList.innerHTML = `
              <div class="rounded-md border p-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                  <i data-lucide="clipboard-check" class="h-6 w-6"></i>
                </div>
                <h3 class="mb-2 text-lg font-medium">No files pending QC</h3>
                <p class="mb-4 text-sm text-muted-foreground">All pagetyped files have been QC reviewed</p>
              </div>
            `;
            lucide.createIcons();
            return;
          }
          
          pendingQCFiles.forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-4';
            fileItem.innerHTML = `
              <div class="flex items-center gap-3">
                <i data-lucide="clipboard-list" class="h-8 w-8 text-orange-500"></i>
                <div>
                  <p class="text-blue-600 font-medium">${file.fileNumber}</p>
                  <p class="text-sm text-gray-700 mt-0.5">
                    ${file.name.includes(" - ") ? file.name.split(" - ")[1] : file.name}
                  </p>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="badge badge-secondary text-xs">
                      ${file.pages} ${file.pages === 1 ? "page" : "pages"} pagetyped
                    </span>
                    <span class="text-xs text-muted-foreground">by ${file.pagetyped_by}</span>
                    <span class="text-xs text-muted-foreground">${file.date}</span>
                  </div>
                </div>
              </div>
              <button class="btn btn-outline btn-sm start-qc-review" data-id="${file.id}">
                <i data-lucide="clipboard-check" class="h-3.5 w-3.5 mr-1"></i>
                Start QC Review
              </button>
            `;
            
            elements.pendingFilesList.appendChild(fileItem);
          });
          
          lucide.createIcons();
          
          // Add event listeners
          document.querySelectorAll('.start-qc-review').forEach(btn => {
            btn.addEventListener('click', () => {
              const fileId = btn.getAttribute('data-id');
              selectFileForQCReview(fileId);
            });
          });
        }

        function renderInProgressQCFiles() {
          elements.inProgressFilesList.innerHTML = '';
          
          if (inProgressQCFiles.length === 0) {
            elements.inProgressFilesList.innerHTML = `
              <div class="rounded-md border p-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                  <i data-lucide="clock" class="h-6 w-6"></i>
                </div>
                <h3 class="mb-2 text-lg font-medium">No files in QC progress</h3>
                <p class="mb-4 text-sm text-muted-foreground">Start QC review on a file to see it here</p>
              </div>
            `;
            lucide.createIcons();
            return;
          }
          
          inProgressQCFiles.forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-4';
            fileItem.innerHTML = `
              <div class="flex items-center gap-3">
                <i data-lucide="clock" class="h-8 w-8 text-blue-500"></i>
                <div class="flex-1">
                  <p class="text-blue-600 font-medium">${file.fileNumber}</p>
                  <p class="text-sm text-gray-700 mt-0.5">
                    ${file.name.includes(" - ") ? file.name.split(" - ")[1] : file.name}
                  </p>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="badge badge-secondary text-xs">
                      ${file.qc_reviewed}/${file.pages} pages QC reviewed
                    </span>
                    <span class="text-xs text-muted-foreground">${file.date}</span>
                  </div>
                  <div class="mt-2 w-full">
                    <div class="progress">
                      <div class="progress-bar" style="width: ${file.qc_progress}%"></div>
                    </div>
                  </div>
                </div>
              </div>
              <button class="btn btn-outline btn-sm continue-qc-review" data-id="${file.id}">
                <i data-lucide="play" class="h-3.5 w-3.5 mr-1"></i>
                Continue QC
              </button>
            `;
            
            elements.inProgressFilesList.appendChild(fileItem);
          });
          
          lucide.createIcons();
          
          // Add event listeners
          document.querySelectorAll('.continue-qc-review').forEach(btn => {
            btn.addEventListener('click', () => {
              const fileId = btn.getAttribute('data-id');
              selectFileForQCReview(fileId);
            });
          });
        }

        function renderCompletedQCFilesTable() {
          const tableBody = document.getElementById('completed-files-table-body');
          if (!tableBody) return;
          
          tableBody.innerHTML = '';
          
          if (completedQCFiles.length === 0) {
            tableBody.innerHTML = `
              <tr>
                <td colspan="7" class="text-center p-8">
                  <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <i data-lucide="check-circle" class="h-6 w-6"></i>
                  </div>
                  <h3 class="mb-2 text-lg font-medium">No completed QC files</h3>
                  <p class="mb-4 text-sm text-muted-foreground">Complete QC reviews to see them here</p>
                </td>
              </tr>
            `;
            lucide.createIcons();
            return;
          }
          
          completedQCFiles.forEach(file => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-muted/10';
            row.innerHTML = `
              <td class="p-3">
                <span class="text-blue-600 font-medium">${file.fileNumber}</span>
              </td>
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <i data-lucide="check-circle" class="h-4 w-4 text-green-500"></i>
                  <span class="font-medium">${file.name}</span>
                </div>
              </td>
              <td class="p-3 text-sm text-muted-foreground">${file.qc_reviewed_at ? new Date(file.qc_reviewed_at).toLocaleDateString() : file.date}</td>
              <td class="p-3 text-sm">${file.qc_reviewed_by || 'QC Team'}</td>
              <td class="p-3">
                <span class="badge bg-green-500 text-white">
                  <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                  QC Completed
                </span>
              </td>
              <td class="p-3">
                <span class="badge badge-secondary">${file.pages} ${file.pages === 1 ? "page" : "pages"}</span>
              </td>
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <button class="btn btn-ghost btn-sm">
                    <i data-lucide="eye" class="h-4 w-4"></i>
                  </button>
                  <button class="btn btn-ghost btn-sm">
                    <i data-lucide="download" class="h-4 w-4"></i>
                  </button>
                </div>
              </td>
            `;
            tableBody.appendChild(row);
          });
          
          lucide.createIcons();
        }

        // Helper functions
        function getFileById(fileId) {
          return [...pendingQCFiles, ...inProgressQCFiles, ...completedQCFiles].find(file => file.id == fileId);
        }

        function selectFileForQCReview(fileId) {
          state.selectedFile = fileId;
          state.activeTab = "qc-review";
          state.showFolderView = true;
          state.selectedPageInFolder = null;
          updateUI();
        }

        function switchTab(tabId) {
          state.activeTab = tabId;
          updateUI();
        }

        function updateUI() {
          // Update tabs
          elements.tabs.forEach(tab => {
            const tabId = tab.getAttribute('data-tab');
            tab.setAttribute('aria-selected', tabId === state.activeTab);
          });
          
          elements.tabContents.forEach(content => {
            const contentId = content.getAttribute('data-tab-content');
            content.setAttribute('aria-hidden', contentId !== state.activeTab);
          });

          // Update QC review tab state
          elements.qcReviewTab.setAttribute('aria-disabled', state.selectedFile ? 'false' : 'true');
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', async () => {
          // Add CSRF token to meta tag if not present
          if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
          }

          // Add tab event listeners
          elements.tabs.forEach(tab => {
            tab.addEventListener('click', async () => {
              const tabId = tab.getAttribute('data-tab');
              if (tabId !== 'qc-review' || state.selectedFile) {
                switchTab(tabId);
                
                // Load data for the selected tab
                switch (tabId) {
                  case 'pending':
                    await loadPendingFiles();
                    break;
                  case 'in-progress':
                    await loadInProgressFiles();
                    break;
                  case 'completed':
                    await loadCompletedFiles();
                    break;
                }
              }
            });
          });

          // Load initial data
          await loadPendingFiles();
          updateUI();
        });
    </script>
@endsection