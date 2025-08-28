@extends('layouts.app')
@section('page-title')
    {{ __('Page Typing Dashboard') }}
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
              <h1 class="text-2xl font-bold tracking-tight">Page Typing</h1>
              <p class="text-muted-foreground">Categorize and digitize file content</p>
            </div>
        
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
              <!-- Pending Page Typing -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">Pending Page Typing</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="pending-count">{{ $stats['pending_count'] ?? 4 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files waiting for page typing</p>
                </div>
              </div>
        
              <!-- In Progress -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">In Progress</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="in-progress-count">{{ $stats['in_progress_count'] ?? 1 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files currently being typed</p>
                </div>
              </div>
        
              <!-- Completed -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">Completed</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="completed-count">{{ $stats['completed_count'] ?? 2 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files completed typing</p>
                </div>
              </div>

              <!-- PageType More -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">PageType More</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold text-orange-600" id="pagetype-more-count">{{ $stats['pagetype_more_count'] ?? 3 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files with new scans added</p>
                </div>
              </div>
            </div>
        
            <!-- Tabs -->
            <div class="tabs">
              <div class="tabs-list grid w-full md:w-auto grid-cols-5">
                <button class="tab" role="tab" aria-selected="true" data-tab="pending">Pending Page Typing</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="in-progress">In Progress</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="completed">Completed</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="pagetype-more">PageType More</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="typing" aria-disabled="true" id="typing-tab">Typing</button>
              </div>
        
              <!-- Pending Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="false" data-tab-content="pending">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Files Pending Page Typing</h2>
                        <p class="text-sm text-muted-foreground">Select a file to begin typing its content</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search files..." class="input w-full pl-8">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div id="pending-files-list" class="rounded-md border divide-y">
                      <!-- Pending files will be added here dynamically -->
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- In Progress Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="in-progress">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Files In Progress</h2>
                        <p class="text-sm text-muted-foreground">Files that are partially typed</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search files..." class="input w-full pl-8">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div id="in-progress-files-list" class="rounded-md border divide-y">
                      <!-- In progress files will be added here dynamically -->
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- Completed Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="completed">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Completed Files</h2>
                        <p class="text-sm text-muted-foreground">Files that have been fully typed</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search files..." class="input w-full pl-8">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <!-- Replaced card-based layout with proper HTML table -->
                    <div class="overflow-x-auto">
                      <table class="w-full border-collapse">
                        <thead>
                          <tr class="border-b bg-muted/20">
                            <th class="text-left p-3 font-medium">File Number</th>
                            <th class="text-left p-3 font-medium">File Name</th>
                            <th class="text-left p-3 font-medium">Date Typed</th>
                            <th class="text-left p-3 font-medium">Typed By</th>
                            <th class="text-left p-3 font-medium">Status</th>
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

              <!-- PageType More Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="pagetype-more">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">PageType More Files</h2>
                        <p class="text-sm text-muted-foreground">Files with new scans that need additional page typing (IsUpdated = 1)</p>
                      </div>
                      <div class="flex items-center gap-4">
                        <div class="relative w-full md:w-64">
                          <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                          <input type="search" placeholder="Search files..." class="input w-full pl-8" id="pagetype-more-search">
                        </div>
                        <button class="btn btn-outline btn-sm" id="refresh-pagetype-more">
                          <i data-lucide="refresh-cw" class="h-4 w-4 mr-1"></i>
                          Refresh
                        </button>
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
                            <th class="text-left p-3 font-medium">Existing Pages</th>
                            <th class="text-left p-3 font-medium">New Scans</th>
                            <th class="text-left p-3 font-medium">Total Pages</th>
                            <th class="text-left p-3 font-medium">Last Updated</th>
                            <th class="text-left p-3 font-medium">Status</th>
                            <th class="text-left p-3 font-medium">Actions</th>
                          </tr>
                        </thead>
                        <tbody id="pagetype-more-table-body">
                          <!-- PageType More files will be added here dynamically -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- Typing Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="typing">
                <div class="card" id="typing-card">
                  <!-- Typing content will be added here dynamically -->
                </div>
              </div>
            </div>
          </div>
        
        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>

    <!-- Page Typing Dashboard JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <!-- Enhanced PageTyping JavaScript with PageType More functionality -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Application state - Extended for PageType More
        let state = {
          activeTab: 'pending',
          selectedFile: null,
          currentPage: 1,
          typedContent: '',
          typingProgress: 0,
          zoomLevel: 100,
          rotation: 0,
          showFolderView: false,
          selectedPageInFolder: null,
          pageType: '1',
          pageSubType: '1',
          serialNo: '01',
          expandedFiles: [],
          batchMode: false,
          batchTypedPages: {},
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
          expandedFileId: null,
          // PageType More specific state
          pageTypeMoreMode: false,
          existingPageTypings: [],
          newScans: [],
          combinedPages: []
        };

        // Sample data for PageType More files (files with IsUpdated = 1)
        const pageTypeMoreFiles = [
          {
            id: "FILE-2024-007",
            fileNumber: "KNML 45123",
            name: "Certificate of Occupancy - Malam Garba Shehu",
            type: "Certificate of Occupancy",
            existingPages: 4,
            newScans: 2,
            totalPages: 6,
            lastUpdated: "2024-01-20",
            status: "Updated",
            isUpdated: true,
            typedBy: "John Doe",
            updatedBy: "Jane Smith"
          },
          {
            id: "FILE-2024-008", 
            fileNumber: "KNGP 78456",
            name: "Site Plan - Hajiya Khadija Umar",
            type: "Site Plan",
            existingPages: 3,
            newScans: 3,
            totalPages: 6,
            lastUpdated: "2024-01-19",
            status: "Updated",
            isUpdated: true,
            typedBy: "Mike Johnson",
            updatedBy: "Alice Brown"
          },
          {
            id: "FILE-2024-009",
            fileNumber: "KNML 98765",
            name: "Right of Occupancy - Alhaji Sani Danladi",
            type: "Right of Occupancy", 
            existingPages: 5,
            newScans: 1,
            totalPages: 6,
            lastUpdated: "2024-01-18",
            status: "Updated",
            isUpdated: true,
            typedBy: "Sarah Wilson",
            updatedBy: "Tom Davis"
          }
        ];

        // Sample pages for PageType More files
        const samplePages = {
          "FILE-2024-007": [
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+1",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+2", 
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+3",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+4",
            "https://via.placeholder.com/800x1000/e3f2fd/1565c0?text=NEW+Scan+1",
            "https://via.placeholder.com/800x1000/e3f2fd/1565c0?text=NEW+Scan+2"
          ],
          "FILE-2024-008": [
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+1",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+2",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+3", 
            "https://via.placeholder.com/800x1000/e3f2fd/1565c0?text=NEW+Scan+1",
            "https://via.placeholder.com/800x1000/e3f2fd/1565c0?text=NEW+Scan+2",
            "https://via.placeholder.com/800x1000/e3f2fd/1565c0?text=NEW+Scan+3"
          ],
          "FILE-2024-009": [
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+1",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+2",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+3",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+4", 
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Existing+Page+5",
            "https://via.placeholder.com/800x1000/e3f2fd/1565c0?text=NEW+Scan+1"
          ]
        };

        // DOM Elements
        const elements = {
          // Tabs
          tabs: document.querySelectorAll('[role="tab"]'),
          tabContents: document.querySelectorAll('[role="tabpanel"]'),
          typingTab: document.getElementById('typing-tab'),
          
          // File lists
          pendingFilesList: document.getElementById('pending-files-list'),
          inProgressFilesList: document.getElementById('in-progress-files-list'),
          completedFilesTableBody: document.getElementById('completed-files-table-body'),
          pageTypeMoreTableBody: document.getElementById('pagetype-more-table-body'),
          
          // Typing card
          typingCard: document.getElementById('typing-card'),
          
          // Counters
          pendingCount: document.getElementById('pending-count'),
          inProgressCount: document.getElementById('in-progress-count'),
          completedCount: document.getElementById('completed-count'),
          pageTypeMoreCount: document.getElementById('pagetype-more-count'),
          
          // PageType More specific
          pageTypeMoreSearch: document.getElementById('pagetype-more-search'),
          refreshPageTypeMore: document.getElementById('refresh-pagetype-more')
        };

        // Helper functions
        function getFileById(fileId) {
          return [...pageTypeMoreFiles].find(file => file.id === fileId);
        }

        function formatDate(dateString) {
          return new Date(dateString).toLocaleDateString();
        }

        // UI update functions
        function updateUI() {
          updateStats();
          renderPendingFiles();
          renderInProgressFiles();
          renderCompletedFilesTable();
          renderPageTypeMoreFiles();
          renderTypingView();
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

          // Update typing tab state
          elements.typingTab.setAttribute('aria-disabled', state.selectedFile ? 'false' : 'true');

          // Update counters
          elements.pageTypeMoreCount.textContent = pageTypeMoreFiles.length;
        }

        function renderPendingFiles() {
          if (!elements.pendingFilesList) return;
          
          elements.pendingFilesList.innerHTML = `
            <div class="rounded-md border p-8 text-center">
              <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                <i data-lucide="file-text" class="h-6 w-6"></i>
              </div>
              <h3 class="mb-2 text-lg font-medium">No pending files</h3>
              <p class="mb-4 text-sm text-muted-foreground">All files have been processed</p>
            </div>
          `;
          lucide.createIcons();
        }

        function renderInProgressFiles() {
          if (!elements.inProgressFilesList) return;
          
          elements.inProgressFilesList.innerHTML = `
            <div class="rounded-md border p-8 text-center">
              <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                <i data-lucide="clock" class="h-6 w-6"></i>
              </div>
              <h3 class="mb-2 text-lg font-medium">No files in progress</h3>
              <p class="mb-4 text-sm text-muted-foreground">Start typing a file to see it here</p>
            </div>
          `;
          lucide.createIcons();
        }

        function renderCompletedFilesTable() {
          if (!elements.completedFilesTableBody) return;
          
          elements.completedFilesTableBody.innerHTML = `
            <tr>
              <td colspan="7" class="text-center p-8">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                  <i data-lucide="check-circle" class="h-6 w-6"></i>
                </div>
                <h3 class="mb-2 text-lg font-medium">No completed files</h3>
                <p class="mb-4 text-sm text-muted-foreground">Complete page typing to see files here</p>
              </td>
            </tr>
          `;
          lucide.createIcons();
        }

        function renderPageTypeMoreFiles() {
          if (!elements.pageTypeMoreTableBody) return;
          
          elements.pageTypeMoreTableBody.innerHTML = '';
          
          if (pageTypeMoreFiles.length === 0) {
            elements.pageTypeMoreTableBody.innerHTML = `
              <tr>
                <td colspan="8" class="text-center p-8">
                  <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <i data-lucide="file-plus" class="h-6 w-6"></i>
                  </div>
                  <h3 class="mb-2 text-lg font-medium">No files need additional page typing</h3>
                  <p class="mb-4 text-sm text-muted-foreground">Files with new scans (IsUpdated = 1) will appear here</p>
                </td>
              </tr>
            `;
            lucide.createIcons();
            return;
          }
          
          pageTypeMoreFiles.forEach(file => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-muted/10';
            row.innerHTML = `
              <td class="p-3">
                <span class="text-blue-600 font-medium">${file.fileNumber}</span>
              </td>
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <i data-lucide="file-plus" class="h-4 w-4 text-orange-500"></i>
                  <span class="font-medium">${file.name.includes(" - ") ? file.name.split(" - ")[1] : file.name}</span>
                </div>
              </td>
              <td class="p-3">
                <span class="badge bg-green-500 text-white">${file.existingPages}</span>
              </td>
              <td class="p-3">
                <span class="badge bg-orange-500 text-white">${file.newScans}</span>
              </td>
              <td class="p-3">
                <span class="badge badge-secondary">${file.totalPages}</span>
              </td>
              <td class="p-3 text-sm text-muted-foreground">${formatDate(file.lastUpdated)}</td>
              <td class="p-3">
                <span class="badge bg-orange-500 text-white">
                  <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                  ${file.status}
                </span>
              </td>
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <button class="btn btn-ghost btn-sm view-combined" data-id="${file.id}" title="View Combined File">
                    <i data-lucide="eye" class="h-4 w-4"></i>
                  </button>
                  <button class="btn btn-primary btn-sm pagetype-more-action" data-id="${file.id}" title="PageType More">
                    <i data-lucide="edit" class="h-4 w-4 mr-1"></i>
                    PageType More
                  </button>
                </div>
              </td>
            `;
            
            elements.pageTypeMoreTableBody.appendChild(row);
          });
          
          // Initialize icons for the new elements
          lucide.createIcons();
          
          // Add event listeners for PageType More actions
          document.querySelectorAll('.pagetype-more-action').forEach(btn => {
            btn.addEventListener('click', () => {
              const fileId = btn.getAttribute('data-id');
              startPageTypeMore(fileId);
            });
          });
          
          document.querySelectorAll('.view-combined').forEach(btn => {
            btn.addEventListener('click', () => {
              const fileId = btn.getAttribute('data-id');
              viewCombinedFile(fileId);
            });
          });
        }

        function renderTypingView() {
          const file = getFileById(state.selectedFile);
          if (!file || !elements.typingCard) return;
          
          // Render PageType More typing interface
          if (state.pageTypeMoreMode) {
            renderPageTypeMoreInterface(file);
          }
        }

        function renderPageTypeMoreInterface(file) {
          const headerContent = `
            <div class="p-6 border-b">
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                  <h2 class="text-lg font-semibold">
                    <span class="text-blue-600">${file.fileNumber}</span> - PageType More
                    <span class="badge bg-orange-500 text-white ml-2">
                      <i data-lucide="plus-circle" class="h-3 w-3 mr-1"></i>
                      ${file.newScans} New Scans
                    </span>
                  </h2>
                  <p class="text-sm text-muted-foreground">
                    Continue page typing with existing ${file.existingPages} pages + ${file.newScans} new scans
                  </p>
                </div>
                <div class="flex items-center gap-2">
                  <button class="btn btn-outline btn-sm back-to-pagetype-more">
                    <i data-lucide="arrow-left" class="h-4 w-4 mr-1"></i>
                    Back to PageType More
                  </button>
                </div>
              </div>
            </div>
          `;

          // Combined folder view showing existing + new pages
          const content = `
            ${headerContent}
            <div class="p-6">
              <div class="space-y-6">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-medium">Combined File Pages</h3>
                  <div class="flex items-center gap-4">
                    <span class="text-sm text-muted-foreground">
                      ${file.existingPages} existing + ${file.newScans} new = ${file.totalPages} total pages
                    </span>
                    <span class="badge bg-blue-500 text-white">${file.fileNumber}</span>
                  </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="combined-pages">
                  ${samplePages[state.selectedFile] 
                    ? samplePages[state.selectedFile].map((page, index) => {
                        const isExisting = index < file.existingPages;
                        const isNew = index >= file.existingPages;
                        
                        return `
                          <div class="border rounded-md overflow-hidden cursor-pointer hover:border-blue-500 transition-colors combined-page ${
                            isExisting ? 'border-green-500 bg-green-50' : 
                            isNew ? 'border-orange-500 bg-orange-50' : ''
                          }" data-index="${index}">
                            <div class="h-40 bg-muted flex items-center justify-center relative">
                              <div class="absolute top-2 right-2 z-10">
                                <span class="badge ${
                                  isExisting ? 'bg-green-500' : 
                                  isNew ? 'bg-orange-500' : 'bg-gray-500'
                                } text-white text-xs">
                                  ${isExisting ? 'TYPED' : isNew ? 'NEW' : 'UNKNOWN'}
                                </span>
                              </div>
                              <img
                                src="${page}"
                                alt="Page ${index + 1}"
                                class="max-h-full max-w-full object-contain"
                              />
                            </div>
                            <div class="p-2 bg-gray-50 border-t">
                              <div class="flex justify-between items-center">
                                <span class="text-sm font-medium">Page ${index + 1}</span>
                                <span class="badge badge-outline text-xs">
                                  ${isExisting ? 'Existing' : isNew ? 'New Scan' : 'Unknown'}
                                </span>
                              </div>
                              ${isExisting ? `
                                <div class="mt-1">
                                  <span class="badge bg-blue-500 text-white text-xs w-full justify-center">
                                    ${file.fileNumber}-${(index + 1).toString().padStart(2, '0')}
                                  </span>
                                </div>
                              ` : isNew ? `
                                <div class="mt-1 text-xs text-orange-600">
                                  Needs page typing
                                </div>
                              ` : ''}
                            </div>
                          </div>
                        `;
                      }).join('')
                    : `<div class="col-span-4 text-center p-8 border rounded-md">
                        <i data-lucide="file-digit" class="h-12 w-12 mx-auto mb-4 text-muted-foreground"></i>
                        <p class="text-sm font-medium">No pages available for this file</p>
                      </div>`
                  }
                </div>

                <div class="mt-6 flex justify-center">
                  <button class="btn btn-primary btn-lg continue-pagetype-more">
                    <i data-lucide="play" class="h-4 w-4 mr-2"></i>
                    Continue Page Typing
                  </button>
                </div>
              </div>
            </div>
          `;
          
          elements.typingCard.innerHTML = content;
          lucide.createIcons();
          
          // Add event listeners
          document.querySelector('.back-to-pagetype-more')?.addEventListener('click', () => {
            state.pageTypeMoreMode = false;
            state.selectedFile = null;
            state.activeTab = 'pagetype-more';
            updateUI();
          });

          document.querySelector('.continue-pagetype-more')?.addEventListener('click', () => {
            alert('Continue Page Typing functionality will load the existing pagetyped pages plus new scans for continued typing.');
          });
        }

        // Event handlers
        function switchTab(tabId) {
          state.activeTab = tabId;
          updateUI();
        }

        function startPageTypeMore(fileId) {
          state.selectedFile = fileId;
          state.pageTypeMoreMode = true;
          state.activeTab = "typing";
          state.showFolderView = true;
          state.zoomLevel = 100;
          state.rotation = 0;
          
          updateUI();
        }

        function viewCombinedFile(fileId) {
          const file = getFileById(fileId);
          if (file) {
            alert(`Viewing combined file: ${file.fileNumber}\nThis would show existing ${file.existingPages} pages + ${file.newScans} new scans in a preview mode.`);
          }
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
          // Add tab event listeners
          elements.tabs.forEach(tab => {
            tab.addEventListener('click', () => {
              const tabId = tab.getAttribute('data-tab');
              if (tabId !== 'typing' || state.selectedFile) {
                switchTab(tabId);
              }
            });
          });

          // PageType More specific event listeners
          if (elements.refreshPageTypeMore) {
            elements.refreshPageTypeMore.addEventListener('click', () => {
              renderPageTypeMoreFiles();
            });
          }

          if (elements.pageTypeMoreSearch) {
            elements.pageTypeMoreSearch.addEventListener('input', (e) => {
              const searchTerm = e.target.value.toLowerCase();
              // Filter PageType More files based on search
              // Implementation would filter the displayed files
            });
          }

          // Initial UI update
          updateUI();
        });
    </script>

    @include('pagetyping.js.javascript')  
@endsection