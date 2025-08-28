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
                  <div class="text-2xl font-bold" id="pending-count">{{ $stats['pending_qc'] ?? 9 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files waiting for QC review</p>
                </div>
              </div>
        
              <!-- QC In Progress -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">QC In Progress</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="in-progress-count">{{ $stats['qc_in_progress'] ?? 2 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files currently under QC review</p>
                </div>
              </div>
        
              <!-- QC Completed -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">QC Completed</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="completed-count">{{ $stats['qc_completed'] ?? 15 }}</div>
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
                      <!-- Pending QC files will be added here dynamically -->
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
                    <!-- Replaced card-based layout with proper HTML table -->
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

    <!-- PTQ Control JavaScript (Modified from PageTyping) -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Application state (same as PageTyping but for QC)
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

        // Sample data for QC files (files that have been pagetyped and need QC)
        const samplePages = {
          "FILE-2023-001": [
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+1",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+2",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+3",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+4",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+5"
          ],
          "FILE-2023-002": [
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+1",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+2",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+3"
          ],
          "FILE-2023-003": [
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+1",
            "https://via.placeholder.com/800x1000/f8fafc/1e293b?text=Document+Page+2"
          ]
        };

        // Sample data for pending QC files (files that have been pagetyped)
        const pendingQCFiles = [
          {
            id: "FILE-2023-001",
            fileNumber: "KNML 34591",
            name: "Certificate of Occupancy - Alhaji Ibrahim Dantata",
            type: "Certificate of Occupancy",
            pages: 5,
            completed: 5,
            qc_reviewed: 0,
            date: "2023-06-15",
            status: "Pending QC",
            pagetyped_by: "John Doe",
            pagetyped_at: "2023-06-15T10:30:00Z"
          },
          {
            id: "FILE-2023-002",
            fileNumber: "KNGP 00892",
            name: "Site Plan - Hajiya Amina Yusuf",
            type: "Site Plan",
            pages: 3,
            completed: 3,
            qc_reviewed: 0,
            date: "2023-06-14",
            status: "Pending QC",
            pagetyped_by: "Jane Smith",
            pagetyped_at: "2023-06-14T14:20:00Z"
          }
        ];

        // Sample data for QC in-progress files
        const inProgressQCFiles = [
          {
            id: "FILE-2023-003",
            fileNumber: "KNML 42786",
            name: "Letter of Administration - Kano Traders Association",
            type: "Letter of Administration",
            pages: 2,
            completed: 2,
            qc_reviewed: 1,
            date: "2023-06-13",
            status: "QC In Progress",
            pagetyped_by: "Mike Johnson",
            pagetyped_at: "2023-06-13T09:15:00Z"
          }
        ];

        // Sample data for completed QC files
        const completedQCFiles = [
          {
            id: "FILE-2023-004",
            fileNumber: "KNGP 01478",
            name: "Right of Occupancy - Musa Usman Bayero",
            type: "Right of Occupancy",
            pages: 4,
            completed: 4,
            qc_reviewed: 4,
            date: "2023-06-12",
            status: "QC Completed",
            pagetyped_by: "Alice Brown",
            pagetyped_at: "2023-06-12T11:30:00Z",
            qc_reviewed_by: "QC Supervisor",
            qc_reviewed_at: "2023-06-12T16:45:00Z",
            processedPages: [
              { pageNumber: 1, pageCode: "KNGP 01478-1-1-01", pageType: "File Cover", pageSubType: "New File Cover", qcStatus: "passed" },
              { pageNumber: 2, pageCode: "KNGP 01478-5-5-02", pageType: "Land Title", pageSubType: "Certificate of Occupancy", qcStatus: "passed" },
              { pageNumber: 3, pageCode: "KNGP 01478-9-25-03", pageType: "Survey", pageSubType: "Survey Plan", qcStatus: "passed" },
              { pageNumber: 4, pageCode: "KNGP 01478-4-8-04", pageType: "Correspondence", pageSubType: "Acknowledgment Letter", qcStatus: "overridden", overrideNote: "Corrected page type classification" }
            ]
          }
        ];

        // Page types and subtypes (same as PageTyping)
        const pageTypes = [
          { id: 1, code: "FC", name: "File Cover" },
          { id: 2, code: "APP", name: "Application" },
          { id: 3, code: "BN", name: "Bill Notice" },
          { id: 4, code: "COR", name: "Correspondence" },
          { id: 5, code: "LT", name: "Land Title" },
          { id: 6, code: "LEG", name: "Legal" },
          { id: 7, code: "PE", name: "Payment Evidence" },
          { id: 8, code: "REP", name: "Report" },
          { id: 9, code: "SUR", name: "Survey" },
          { id: 10, code: "MISC", name: "Miscellaneous" },
          { id: 11, code: "IMG", name: "Image" },
          { id: 12, code: "TP", name: "Town Planning" }
        ];

        // Page subtypes organized by page type ID (same as PageTyping)
        const pageSubTypes = {
          1: [
            { id: 1, code: "NFC", name: "New File Cover" },
            { id: 2, code: "OFC", name: "Old File Cover" }
          ],
          2: [
            { id: 3, code: "CO", name: "Certificate of Occupancy" },
            { id: 4, code: "REV", name: "Revalidation" },
            { id: 42, code: "OTH", name: "Others" }
          ],
          4: [
            { id: 8, code: "AL", name: "Acknowledgment Letter" },
            { id: 9, code: "ASR", name: "Application Submission for Recommendation" },
            { id: 10, code: "ACO", name: "Approval of Certificate of Occupancy" }
          ],
          5: [
            { id: 5, code: "CO", name: "Certificate of Occupancy" },
            { id: 6, code: "SP", name: "Survey Plan" }
          ],
          9: [
            { id: 24, code: "TDP", name: "Title Deed Plan" },
            { id: 25, code: "SP", name: "Survey Plan" },
            { id: 26, code: "SD", name: "Survey Description" }
          ]
        };

        // Get all files
        const allQCFiles = [...pendingQCFiles, ...inProgressQCFiles, ...completedQCFiles];

        // DOM Elements
        const elements = {
          // Tabs
          tabs: document.querySelectorAll('[role="tab"]'),
          tabContents: document.querySelectorAll('[role="tabpanel"]'),
          qcReviewTab: document.getElementById('qc-review-tab'),
          
          // File lists
          pendingFilesList: document.getElementById('pending-files-list'),
          inProgressFilesList: document.getElementById('in-progress-files-list'),
          completedFilesList: document.getElementById('completed-files-list'),
          
          // QC Review card
          qcReviewCard: document.getElementById('qc-review-card'),
          
          // Counters
          pendingCount: document.getElementById('pending-count'),
          inProgressCount: document.getElementById('in-progress-count'),
          completedCount: document.getElementById('completed-count')
        };

        // Helper functions (same as PageTyping)
        function getFileById(fileId) {
          return allQCFiles.find(file => file.id === fileId);
        }

        function getPageTypeById(typeId) {
          return pageTypes.find(type => type.id.toString() === typeId);
        }

        function getPageSubTypeById(typeId, subTypeId) {
          return pageSubTypes[parseInt(typeId)]?.find(subType => subType.id.toString() === subTypeId);
        }

        function getCurrentPageImage() {
          if (!state.selectedFile || !samplePages[state.selectedFile]) {
            return null;
          }

          const pageIndex = state.currentPage - 1;
          return pageIndex >= 0 && pageIndex < samplePages[state.selectedFile].length 
            ? samplePages[state.selectedFile][pageIndex] 
            : null;
        }

        // UI update functions (modified for QC)
        function updateUI() {
          updateStats();
          renderPendingQCFiles();
          renderInProgressQCFiles();
          renderCompletedQCFilesTable();
          renderQCReviewView();
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

          // Update QC review tab state
          elements.qcReviewTab.setAttribute('aria-disabled', state.selectedFile ? 'false' : 'true');

          // Update counters
          elements.pendingCount.textContent = pendingQCFiles.length;
          elements.inProgressCount.textContent = inProgressQCFiles.length;
          elements.completedCount.textContent = completedQCFiles.length;
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
          
          // Initialize icons for the new elements
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
                      <div class="progress-bar" style="width: ${(file.qc_reviewed / file.pages) * 100}%"></div>
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
          
          // Initialize icons for the new elements
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
            // Main row
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-muted/10 cursor-pointer';
            row.setAttribute('data-file-id', file.id);
            
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
                  <button class="btn btn-ghost btn-sm" onclick="toggleQCFileExpansion('${file.id}')">
                    <i data-lucide="${state.expandedFileId === file.id ? 'chevron-up' : 'chevron-down'}" class="h-4 w-4"></i>
                    ${state.expandedFileId === file.id ? 'Hide' : 'Show'} Pages
                  </button>
                  <button class="btn btn-ghost btn-sm">
                    <i data-lucide="download" class="h-4 w-4"></i>
                  </button>
                </div>
              </td>
            `;
            
            tableBody.appendChild(row);
            
            // Expanded content row
            if (state.expandedFileId === file.id && file.processedPages) {
              const expandedRow = document.createElement('tr');
              expandedRow.className = 'bg-muted/5';
              expandedRow.innerHTML = `
                <td colspan="7" class="p-4">
                  <div class="border rounded-lg p-4 bg-white">
                    <h4 class="text-sm font-medium mb-3">QC Reviewed Pages (${file.processedPages.length})</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                      ${file.processedPages.map((page, index) => `
                        <div class="border rounded-md overflow-hidden bg-white shadow-sm">
                          <div class="h-32 bg-muted flex items-center justify-center">
                            ${samplePages[file.id] && samplePages[file.id][index] 
                              ? `<img src="${samplePages[file.id][index]}" alt="Page ${page.pageNumber}" class="max-h-full max-w-full object-contain">`
                              : `<i data-lucide="file-text" class="h-10 w-10 text-muted-foreground"></i>`
                            }
                          </div>
                          <div class="p-3">
                            <div class="flex justify-between items-center mb-2">
                              <span class="text-sm font-medium">Page ${page.pageNumber}</span>
                              <span class="badge ${page.qcStatus === 'overridden' ? 'bg-yellow-500' : 'bg-green-500'} text-white text-xs">
                                ${page.qcStatus === 'overridden' ? 'Override' : 'Passed'}
                              </span>
                            </div>
                            <div class="space-y-1">
                              <div class="badge bg-blue-500 text-white text-xs w-full justify-center">
                                ${page.pageCode}
                              </div>
                              <p class="text-xs text-muted-foreground truncate" title="${page.pageSubType}">
                                ${page.pageSubType}
                              </p>
                              ${page.qcStatus === 'overridden' && page.overrideNote ? `
                                <p class="text-xs text-yellow-600 italic truncate" title="${page.overrideNote}">
                                  Override: ${page.overrideNote}
                                </p>
                              ` : ''}
                            </div>
                          </div>
                        </div>
                      `).join('')}
                    </div>
                  </div>
                </td>
              `;
              tableBody.appendChild(expandedRow);
            }
          });
          
          lucide.createIcons();
        }

        function toggleQCFileExpansion(fileId) {
          if (state.expandedFileId === fileId) {
            state.expandedFileId = null;
          } else {
            state.expandedFileId = fileId;
          }
          renderCompletedQCFilesTable();
        }

        function renderQCReviewView() {
          const file = getFileById(state.selectedFile);
          if (!file) return;
          
          // Determine what to show based on state (same structure as PageTyping but for QC)
          let content = '';
          
          // Header content
          const headerContent = `
            <div class="p-6 border-b">
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                  <h2 class="text-lg font-semibold">
                    <span class="text-blue-600">${file.fileNumber}</span> - 
                    ${file.name.split(" - ").length > 1 ? file.name.split(" - ")[1] : file.name}
                  </h2>
                  <p class="text-sm text-muted-foreground">
                    ${state.showFolderView && state.selectedPageInFolder === null
                      ? state.batchMode
                        ? "Select pages to QC review in batch mode"
                        : "Select a page to QC review or override"
                      : state.selectedPageInFolder !== null
                        ? `QC Reviewing Page ${state.selectedPageInFolder + 1}`
                        : `QC Review Page ${state.currentPage} of ${file.pages}`}
                  </p>
                </div>
                <div class="flex items-center gap-2">
                  ${state.showFolderView && state.selectedPageInFolder === null 
                    ? `<button class="btn ${state.batchMode ? 'btn-primary' : 'btn-outline'} btn-sm toggle-batch-mode">
                        <i data-lucide="check-square" class="h-4 w-4 mr-1"></i>
                        ${state.batchMode ? 'Exit Batch Mode' : 'Batch QC Mode'}
                      </button>` 
                    : ''}
                  <button class="btn btn-outline btn-sm back-button">
                    ${state.selectedPageInFolder !== null ? 'Back to Folder' : 'Cancel'}
                  </button>
                  ${!state.showFolderView 
                    ? `<button class="btn btn-primary btn-sm save-page">
                        <i data-lucide="check" class="h-4 w-4 mr-1"></i>
                        Approve Page
                      </button>` 
                    : ''}
                </div>
              </div>
            </div>
          `;
          
          // Main content based on view mode (same as PageTyping but for QC)
          if (state.showFolderView) {
            if (state.selectedPageInFolder !== null) {
              // Page QC review view (similar to page categorization but for QC)
              content = `
                ${headerContent}
                <div class="p-6">
                  <div class="space-y-6">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-medium">QC Review Page ${state.selectedPageInFolder + 1}</h3>
                      <span class="badge bg-blue-500 text-white">${file.fileNumber}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <div class="border rounded-md p-4 h-[400px] bg-white relative">
                          ${samplePages[state.selectedFile] && samplePages[state.selectedFile][state.selectedPageInFolder] 
                            ? `<div class="w-full h-full flex flex-col">
                                <div class="flex justify-between mb-2">
                                  <span class="text-sm font-medium">Document Preview - Page ${state.selectedPageInFolder + 1}</span>
                                  <div class="flex items-center gap-2">
                                    <button class="btn btn-ghost btn-icon zoom-out">
                                      <i data-lucide="zoom-out" class="h-4 w-4"></i>
                                    </button>
                                    <span class="text-xs">${state.zoomLevel}%</span>
                                    <button class="btn btn-ghost btn-icon zoom-in">
                                      <i data-lucide="zoom-in" class="h-4 w-4"></i>
                                    </button>
                                    <button class="btn btn-ghost btn-icon rotate">
                                      <i data-lucide="rotate-cw" class="h-4 w-4"></i>
                                    </button>
                                  </div>
                                </div>
                                <div class="flex-1 overflow-auto flex items-center justify-center">
                                  <img
                                    src="${samplePages[state.selectedFile][state.selectedPageInFolder]}"
                                    alt="Page ${state.selectedPageInFolder + 1}"
                                    class="max-h-full max-w-full object-contain transition-transform"
                                    style="transform: scale(${state.zoomLevel / 100}) rotate(${state.rotation}deg);"
                                  />
                                </div>
                              </div>`
                            : `<div class="h-full flex items-center justify-center">
                                <div class="text-center">
                                  <i data-lucide="file-text" class="h-12 w-12 mx-auto mb-4 text-muted-foreground"></i>
                                  <p class="text-sm font-medium">Document preview not available</p>
                                </div>
                              </div>`
                          }
                        </div>
                      </div>
                      <div class="space-y-6">
                        <!-- Current Page Typing Info (Read-only for QC) -->
                        <div class="p-4 border rounded-md bg-gray-50">
                          <h4 class="font-medium mb-3">Current Page Typing</h4>
                          <div class="space-y-2 text-sm">
                            <div><strong>Page Type:</strong> File Cover (FC)</div>
                            <div><strong>Page Subtype:</strong> New File Cover (NFC)</div>
                            <div><strong>Serial Number:</strong> 01</div>
                            <div><strong>Page Code:</strong> ${file.fileNumber}-FC-NFC-01</div>
                            <div><strong>Typed By:</strong> ${file.pagetyped_by}</div>
                          </div>
                        </div>

                        <!-- QC Actions -->
                        <div class="space-y-3">
                          <button class="btn btn-primary w-full approve-page">
                            <i data-lucide="check" class="h-4 w-4 mr-2"></i>
                            Approve Page Typing
                          </button>
                          <button class="btn btn-outline w-full reject-page">
                            <i data-lucide="x" class="h-4 w-4 mr-2"></i>
                            Reject Page Typing
                          </button>
                          <button class="btn btn-outline w-full override-page" style="border-color: #f59e0b; color: #f59e0b;">
                            <i data-lucide="shield-check" class="h-4 w-4 mr-2"></i>
                            Override & Correct
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              `;
            } else {
              // QC Folder view (same as PageTyping folder view)
              content = `
                ${headerContent}
                <div class="p-6">
                  <div class="space-y-6">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-medium">Pages for QC Review</h3>
                      <span class="badge bg-blue-500 text-white">${file.fileNumber}</span>
                    </div>

                    ${state.batchMode 
                      ? `<div class="space-y-2">
                          <div class="flex justify-between text-sm">
                            <span>QC Progress</span>
                            <span>${Math.round(state.batchProgress)}%</span>
                          </div>
                          <div class="progress">
                            <div class="progress-bar" style="width: ${state.batchProgress}%"></div>
                          </div>
                          <div class="flex justify-between text-xs text-muted-foreground">
                            <span>Pages QC reviewed: ${Object.keys(state.batchQCPages).length}</span>
                            <span>Total pages: ${samplePages[state.selectedFile]?.length || 0}</span>
                          </div>
                        </div>`
                      : ''}

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="qc-folder-pages">
                      ${samplePages[state.selectedFile] 
                        ? samplePages[state.selectedFile].map((page, index) => {
                            const isProcessed = (state.batchMode && state.batchQCPages[index]) || (!state.batchMode && state.processedPages[index]);
                            const processedInfo = state.batchQCPages[index] || state.processedPages[index];
                            
                            return `
                              <div class="border rounded-md overflow-hidden cursor-pointer hover:border-blue-500 transition-colors qc-folder-page ${
                                isProcessed
                                  ? processedInfo?.status === 'approved' 
                                    ? 'border-green-500 bg-green-50'
                                    : processedInfo?.status === 'rejected'
                                    ? 'border-red-500 bg-red-50'
                                    : 'border-yellow-500 bg-yellow-50'
                                  : ''
                              }" data-index="${index}">
                                <div class="h-40 bg-muted flex items-center justify-center relative">
                                  ${isProcessed 
                                    ? `<div class="absolute top-2 right-2 z-10">
                                        <span class="badge ${
                                          processedInfo?.status === 'approved' ? 'bg-green-500' : 
                                          processedInfo?.status === 'rejected' ? 'bg-red-500' : 'bg-yellow-500'
                                        } text-white">
                                          <i data-lucide="${
                                            processedInfo?.status === 'approved' ? 'check' : 
                                            processedInfo?.status === 'rejected' ? 'x' : 'shield-check'
                                          }" class="h-3 w-3 mr-1"></i>
                                          ${processedInfo?.status === 'approved' ? 'Approved' : 
                                            processedInfo?.status === 'rejected' ? 'Rejected' : 'Override'}
                                        </span>
                                      </div>`
                                    : ''}
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
                                      ${index === 0 ? 'Cover' : 'Content'}
                                    </span>
                                  </div>
                                  <div class="mt-1 text-xs text-muted-foreground">
                                    ${file.fileNumber}-${(index + 1).toString().padStart(2, '0')}
                                  </div>
                                  ${isProcessed 
                                    ? `<div class="mt-1">
                                        <span class="badge ${
                                          processedInfo?.status === 'approved' ? 'bg-green-500' : 
                                          processedInfo?.status === 'rejected' ? 'bg-red-500' : 'bg-yellow-500'
                                        } text-white text-xs w-full justify-center">
                                          QC ${processedInfo?.status === 'approved' ? 'Approved' : 
                                               processedInfo?.status === 'rejected' ? 'Rejected' : 'Override'}
                                        </span>
                                      </div>`
                                    : ''}
                                </div>
                              </div>
                            `;
                          }).join('')
                        : `<div class="col-span-4 text-center p-8 border rounded-md">
                            <i data-lucide="file-digit" class="h-12 w-12 mx-auto mb-4 text-muted-foreground"></i>
                            <p class="text-sm font-medium">No pages available for QC review</p>
                          </div>`
                      }
                    </div>

                    ${state.batchMode && state.batchSubmitReady 
                      ? `<div class="mt-6 flex justify-center">
                          <button class="btn btn-primary btn-lg submit-batch ${state.batchProcessing ? 'disabled' : ''}" ${state.batchProcessing ? 'disabled' : ''}>
                            ${state.batchProcessing 
                              ? `<div class="animate-spin h-4 w-4 border-2 border-current border-t-transparent rounded-full mr-2"></div>
                                 Processing QC Batch...`
                              : `<i data-lucide="upload" class="h-4 w-4 mr-2"></i>
                                 Submit QC Batch`
                            }
                          </button>
                        </div>`
                      : ''}
                  </div>
                </div>
              `;
            }
          }
          
          // Update the QC review card
          elements.qcReviewCard.innerHTML = content;
          
          // Initialize icons for the new elements
          lucide.createIcons();
          
          // Add event listeners (same as PageTyping but for QC actions)
          if (state.showFolderView) {
            if (state.selectedPageInFolder !== null) {
              // Page QC review listeners
              document.querySelector('.approve-page')?.addEventListener('click', approvePage);
              document.querySelector('.reject-page')?.addEventListener('click', rejectPage);
              document.querySelector('.override-page')?.addEventListener('click', showOverrideModal);
              
              // Image controls
              document.querySelector('.zoom-in')?.addEventListener('click', zoomIn);
              document.querySelector('.zoom-out')?.addEventListener('click', zoomOut);
              document.querySelector('.rotate')?.addEventListener('click', rotate);
            } else {
              // QC Folder view listeners
              document.querySelector('.toggle-batch-mode')?.addEventListener('click', toggleBatchMode);
              
              document.querySelectorAll('.qc-folder-page').forEach(page => {
                page.addEventListener('click', () => {
                  const index = parseInt(page.getAttribute('data-index'));
                  selectPageFromFolder(index);
                });
              });
              
              document.querySelector('.submit-batch')?.addEventListener('click', submitBatch);
            }
            
            document.querySelector('.back-button')?.addEventListener('click', () => {
              if (state.selectedPageInFolder !== null) {
                state.selectedPageInFolder = null;
              } else if (state.showFolderView) {
                state.showFolderView = false;
                state.selectedFile = null;
                state.activeTab = 'pending';
              } else {
                state.selectedFile = null;
                state.activeTab = 'pending';
              }
              updateUI();
            });
          }
        }

        // Event handlers (modified for QC)
        function switchTab(tabId) {
          state.activeTab = tabId;
          updateUI();
        }

        function selectFileForQCReview(fileId) {
          state.selectedFile = fileId;
          state.currentPage = 1;
          state.activeTab = "qc-review";
          state.zoomLevel = 100;
          state.rotation = 0;
          state.showFolderView = true;
          state.selectedPageInFolder = null;
          state.batchMode = false;
          state.batchQCPages = {};
          state.currentBatchPageIndex = null;
          state.batchSubmitReady = false;
          state.processedPages = {};

          // Calculate QC progress based on reviewed pages
          const file = getFileById(fileId);
          if (file) {
            state.qcProgress = (file.qc_reviewed / file.pages) * 100;
          }
          
          updateUI();
        }

        function selectPageFromFolder(index) {
          state.selectedPageInFolder = index;
          updateUI();
        }

        function toggleBatchMode() {
          state.batchMode = !state.batchMode;
          if (!state.batchMode) {
            state.batchQCPages = {};
            state.batchProgress = 0;
          }
          updateUI();
        }

        function approvePage() {
          if (state.selectedPageInFolder === null) return;
          
          if (state.batchMode) {
            state.batchQCPages[state.selectedPageInFolder] = { status: 'approved' };
            updateBatchProgress();
          } else {
            state.processedPages[state.selectedPageInFolder] = { status: 'approved' };
            alert('Page approved successfully!');
          }
          
          state.selectedPageInFolder = null;
          updateUI();
        }

        function rejectPage() {
          if (state.selectedPageInFolder === null) return;
          
          const reason = prompt('Please provide a reason for rejecting this page:');
          if (reason === null) return; // User cancelled
          
          if (state.batchMode) {
            state.batchQCPages[state.selectedPageInFolder] = { status: 'rejected', reason: reason };
            updateBatchProgress();
          } else {
            state.processedPages[state.selectedPageInFolder] = { status: 'rejected', reason: reason };
            alert('Page rejected successfully!');
          }
          
          state.selectedPageInFolder = null;
          updateUI();
        }

        function showOverrideModal() {
          if (state.selectedPageInFolder === null) return;
          
          // Populate modal with current page info
          document.getElementById('override-page-id').value = state.selectedPageInFolder;
          document.getElementById('override-page-details').innerHTML = `
            <p><strong>Page Number:</strong> ${state.selectedPageInFolder + 1}</p>
            <p><strong>Current Page Type:</strong> File Cover (FC)</p>
            <p><strong>Current Page Subtype:</strong> New File Cover (NFC)</p>
            <p><strong>Current Page Code:</strong> ${getFileById(state.selectedFile).fileNumber}-FC-NFC-01</p>
          `;
          
          // Show modal
          document.getElementById('qc-override-modal').classList.remove('hidden');
          document.getElementById('qc-override-modal').setAttribute('aria-hidden', 'false');
        }

        function hideOverrideModal() {
          document.getElementById('qc-override-modal').classList.add('hidden');
          document.getElementById('qc-override-modal').setAttribute('aria-hidden', 'true');
          document.getElementById('override-note').value = '';
        }

        function submitOverride() {
          const note = document.getElementById('override-note').value.trim();
          if (!note) {
            alert('Please provide a reason for the override.');
            return;
          }
          
          if (state.batchMode) {
            state.batchQCPages[state.selectedPageInFolder] = { status: 'overridden', note: note };
            updateBatchProgress();
          } else {
            state.processedPages[state.selectedPageInFolder] = { status: 'overridden', note: note };
            alert('Page override submitted successfully!');
          }
          
          state.selectedPageInFolder = null;
          hideOverrideModal();
          updateUI();
        }

        function updateBatchProgress() {
          const totalPages = samplePages[state.selectedFile]?.length || 0;
          const reviewedCount = Object.keys(state.batchQCPages).length;
          state.batchProgress = totalPages > 0 ? (reviewedCount / totalPages) * 100 : 0;
          
          if (reviewedCount >= totalPages) {
            state.batchSubmitReady = true;
          }
        }

        function submitBatch() {
          if (Object.keys(state.batchQCPages).length === 0) {
            alert('No QC decisions made.');
            return;
          }
          
          state.batchProcessing = true;
          updateUI();
          
          // Simulate batch processing
          setTimeout(() => {
            alert(`QC batch submitted successfully! ${Object.keys(state.batchQCPages).length} pages reviewed.`);
            
            // Reset state and go back to list
            state.selectedFile = null;
            state.activeTab = 'pending';
            state.batchQCPages = {};
            state.batchMode = false;
            state.batchProcessing = false;
            updateUI();
          }, 2000);
        }

        // Image control functions (same as PageTyping)
        function zoomIn() {
          if (state.zoomLevel < 200) {
            state.zoomLevel += 25;
            updateUI();
          }
        }

        function zoomOut() {
          if (state.zoomLevel > 50) {
            state.zoomLevel -= 25;
            updateUI();
          }
        }

        function rotate() {
          state.rotation = (state.rotation + 90) % 360;
          updateUI();
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
          // Add tab event listeners
          elements.tabs.forEach(tab => {
            tab.addEventListener('click', () => {
              const tabId = tab.getAttribute('data-tab');
              if (tabId !== 'qc-review' || state.selectedFile) {
                switchTab(tabId);
              }
            });
          });

          // Override modal event listeners
          document.getElementById('cancel-override-btn').addEventListener('click', hideOverrideModal);
          document.getElementById('override-modal-backdrop').addEventListener('click', hideOverrideModal);
          
          // Override form submit
          document.getElementById('qc-override-form').addEventListener('submit', (e) => {
            e.preventDefault();
            submitOverride();
          });

          // Initial UI update
          updateUI();
        });
    </script>
@endsection