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
                  <div class="text-2xl font-bold" id="pending-count">{{ $stats['pending_count'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files waiting for page typing</p>
                </div>
              </div>
        
              <!-- In Progress -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">In Progress</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="in-progress-count">{{ $stats['in_progress_count'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files currently being typed</p>
                </div>
              </div>
        
              <!-- Completed -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">Completed</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="completed-count">{{ $stats['completed_count'] ?? 0 }}</div>
                  <p class="text-xs text-muted-foreground mt-1">Files completed typing</p>
                </div>
              </div>

              <!-- PageType More -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">PageType More</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold text-orange-600" id="pagetype-more-count">{{ $stats['pagetype_more_count'] ?? 0 }}</div>
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
                      <!-- Loading state -->
                      <div class="p-8 text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-sm text-gray-500">Loading pending files...</p>
                      </div>
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
                      <!-- Loading state -->
                      <div class="p-8 text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-sm text-gray-500">Loading in-progress files...</p>
                      </div>
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
                          <!-- Loading state -->
                          <tr>
                            <td colspan="7" class="text-center p-8">
                              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                              <p class="text-sm text-gray-500">Loading completed files...</p>
                            </td>
                          </tr>
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
                          <!-- Loading state -->
                          <tr>
                            <td colspan="8" class="text-center p-8">
                              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                              <p class="text-sm text-gray-500">Loading PageType More files...</p>
                            </td>
                          </tr>
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
                  <div class="p-8 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                      <i data-lucide="type" class="h-6 w-6"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-medium">Select a file to start typing</h3>
                    <p class="mb-4 text-sm text-muted-foreground">Choose a file from the pending or in-progress tabs</p>
                  </div>
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

    <!-- Enhanced PageTyping JavaScript with CoverType Integration -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Application state
        let state = {
          activeTab: 'pending',
          selectedFile: null,
          selectedFileData: null,
          pageTypeMoreMode: false,
          existingPageTypings: [],
          newScans: [],
          combinedPages: [],
          // Typing interface state
          typingState: null
        };

        // Cover types, page types and subtypes - will be loaded from backend
        let coverTypes = [];
        let pageTypes = [];
        let pageSubTypes = {};

        // Utility to safely get value from various casings/keys
        function pick(obj, keys, fallback = undefined) {
          for (const k of keys) {
            if (obj && obj[k] !== undefined && obj[k] !== null) return obj[k];
          }
          return fallback;
        }

        // Load data from backend
        async function loadPageTypingData() {
          try {
            const response = await fetch('{{ route("pagetyping.api.typing-data") }}');
            const data = await response.json();
            
            if (data.success) {
              // Normalize CoverTypes
              const rawCover = data.cover_types || [];
              coverTypes = rawCover.map(ct => {
                const id = pick(ct, ['id','Id']);
                const name = pick(ct, ['name','Name','title','Title'], 'Cover');
                let code = pick(ct, ['code','Code']);
                if (!code) {
                  const nm = (name || '').toLowerCase();
                  if (nm.includes('front')) code = 'FC';
                  else if (nm.includes('back')) code = 'BC';
                  else code = (name || 'CV').split(/\s+/).map(w => w[0]).join('').substring(0,3).toUpperCase();
                }
                return { id: id?.toString(), code, name };
              });

              // Normalize PageTypes
              const rawTypes = data.page_types || [];
              pageTypes = rawTypes.map(pt => {
                const id = pick(pt, ['id','Id']).toString();
                const code = pick(pt, ['code','Code','PageType'], 'PT');
                const name = pick(pt, ['name','Name'], code);
                return { id, code, name };
              });

              // Normalize and group PageSubTypes by PageTypeId
              const rawSubs = data.page_sub_types || [];
              const grouped = {};
              rawSubs.forEach(st => {
                const id = pick(st, ['id','Id']).toString();
                const code = pick(st, ['code','Code','PageSubType'], 'ST');
                const name = pick(st, ['name','Name'], code);
                let ptId = pick(st, ['page_type_id','PageTypeId','pageTypeId']);
                if (!ptId) {
                  // If only PageType code was provided, resolve to id via pageTypes
                  const ptCode = pick(st, ['PageType','page_type']);
                  if (ptCode) {
                    const found = pageTypes.find(t => (t.code || '').toString().toLowerCase() === ptCode.toString().toLowerCase());
                    if (found) ptId = found.id;
                  }
                }
                ptId = ptId?.toString();
                if (!ptId) return; // skip if cannot resolve mapping
                if (!grouped[ptId]) grouped[ptId] = [];
                grouped[ptId].push({ id, code, name });
              });
              pageSubTypes = grouped;

              console.log('Loaded page typing data:', { coverTypes, pageTypes, pageSubTypes });
            } else {
              console.error('Error loading page typing data:', data.message);
              // Fallback to default data
              setDefaultPageTypingData();
            }
          } catch (error) {
            console.error('Error loading page typing data:', error);
            // Fallback to default data
            setDefaultPageTypingData();
          }
        }

        // Fallback default data
        function setDefaultPageTypingData() {
          coverTypes = [
            { id: 1, code: "FC", name: "Front Cover" },
            { id: 2, code: "BC", name: "Back Cover" }
          ];
          
          pageTypes = [
            { id: 1, code: "FC", name: "File Cover" },
            { id: 2, code: "APP", name: "Application" },
            { id: 3, code: "BN", name: "Bill Notice" },
            { id: 4, code: "COR", name: "Correspondence" },
            { id: 5, code: "LT", name: "Land Title" },
            { id: 6, code: "LEG", name: "Legal" },
            { id: 7, code: "PE", name: "Payment Evidence" },
            { id: 8, code: "REP", name: "Report" },
            { id: 9, code: "SUR", name: "Survey" },
            { id: 10, code: "MISC", name: "Miscellaneous" }
          ];

          pageSubTypes = {
            1: [{ id: 1, code: "NFC", name: "New File Cover" }, { id: 2, code: "OFC", name: "Old File Cover" }],
            2: [{ id: 3, code: "CO", name: "Certificate of Occupancy" }, { id: 4, code: "REV", name: "Revalidation" }],
            3: [{ id: 7, code: "DGR", name: "Demand for Ground Rent" }, { id: 34, code: "DN", name: "Demand Notice" }],
            4: [{ id: 8, code: "AL", name: "Acknowledgment Letter" }, { id: 9, code: "ASR", name: "Application Submission" }],
            5: [{ id: 5, code: "CO", name: "Certificate of Occupancy" }, { id: 6, code: "SP", name: "Survey Plan" }],
            6: [{ id: 18, code: "AGR", name: "Agreement" }, { id: 44, code: "POA", name: "Power of Attorney" }],
            7: [{ id: 19, code: "AOF", name: "Assessment of Fees" }, { id: 20, code: "BT", name: "Bank Teller" }],
            8: [{ id: 23, code: "RR", name: "Reinspection Report" }, { id: 65, code: "IPVR", name: "Inspection Report" }],
            9: [{ id: 24, code: "TDP", name: "Title Deed Plan" }, { id: 25, code: "SP", name: "Survey Plan" }],
            10: [{ id: 27, code: "MISC", name: "Miscellaneous" }, { id: 43, code: "OC", name: "Other Certificates" }]
          };
        }

        // PageType More files will be loaded from backend
        let pageTypeMoreFiles = [];

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

        function getCoverTypeById(typeId) {
          return coverTypes.find(type => type.id.toString() === typeId.toString());
        }

        function getPageTypeById(typeId) {
          return pageTypes.find(type => type.id.toString() === typeId.toString());
        }

        function getPageSubTypeById(typeId, subTypeId) {
          return pageSubTypes[parseInt(typeId)]?.find(subType => subType.id.toString() === subTypeId.toString());
        }

        // Filetype helpers and preview rendering
        function isImageFile(filename) {
          if (!filename) return false;
          const exts = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.tif', '.tiff'];
          const lower = filename.toLowerCase();
          return exts.some(ext => lower.endsWith(ext));
        }

        function isPDFFile(filename) {
          if (!filename) return false;
          return filename.toLowerCase().endsWith('.pdf');
        }

        function getDocumentUrl(documentPath) {
          if (!documentPath) return null;
          const clean = documentPath.replace(/^\/+/, '').replace(/\\/g, '/');
          return `{{ asset('storage') }}/${clean}`;
        }

        function renderDocumentPreview(scanning, containerEl) {
          if (!scanning || !containerEl) return;
          const url = getDocumentUrl(scanning.document_path);
          const isImg = isImageFile(scanning.original_filename);
          const isPdf = isPDFFile(scanning.original_filename);

          if (!url) {
            containerEl.innerHTML = `
              <div class="h-full flex items-center justify-center">
                <div class="text-center">
                  <i data-lucide="file-x" class="h-12 w-12 mx-auto mb-3 text-muted-foreground"></i>
                  <p class="text-sm">No preview available</p>
                </div>
              </div>`;
            lucide.createIcons();
            return;
          }

          if (isImg) {
            containerEl.innerHTML = `
              <div class="w-full h-full flex flex-col">
                <div class="flex justify-between mb-2">
                  <span class="text-sm font-medium">${scanning.original_filename}</span>
                  <div class="flex items-center gap-2">
                    <button class="btn btn-ghost btn-icon zoom-out"><i data-lucide="zoom-out" class="h-4 w-4"></i></button>
                    <span class="text-xs zoom-level">${state.typingState.zoomLevel}%</span>
                    <button class="btn btn-ghost btn-icon zoom-in"><i data-lucide="zoom-in" class="h-4 w-4"></i></button>
                    <button class="btn btn-ghost btn-icon rotate"><i data-lucide="rotate-cw" class="h-4 w-4"></i></button>
                  </div>
                </div>
                <div class="flex-1 overflow-auto flex items-center justify-center bg-gray-50">
                  <img src="${url}" alt="${scanning.original_filename}" class="max-h-full max-w-full object-contain transition-transform document-image"
                       style="transform: scale(${state.typingState.zoomLevel / 100}) rotate(${state.typingState.rotation}deg);"
                       onerror="this.parentElement.innerHTML='<div class=\'text-center\'><i data-lucide=\'image-off\' class=\'h-12 w-12 mx-auto mb-3 text-red-500\'></i><p class=\'text-sm text-red-600\'>Failed to load image</p></div>'; lucide.createIcons();" />
                </div>
              </div>`;
          } else if (isPdf) {
            containerEl.innerHTML = `
              <div class="w-full h-full flex flex-col">
                <div class="flex justify-between mb-2">
                  <span class="text-sm font-medium">${scanning.original_filename}</span>
                  <div class="flex items-center gap-2">
                    <button class="btn btn-outline btn-sm" onclick="window.open('${url}', '_blank')">
                      <i data-lucide="external-link" class="h-4 w-4 mr-1"></i> Open PDF
                    </button>
                  </div>
                </div>
                <div class="flex-1 overflow-auto flex items-center justify-center bg-gray-50">
                  <div class="text-center">
                    <i data-lucide="file-text" class="h-16 w-16 mx-auto mb-3 text-blue-500"></i>
                    <p class="text-sm">PDF document</p>
                    <p class="text-xs text-muted-foreground mb-3">Preview opens in a new tab</p>
                    <button class="btn btn-primary btn-sm" onclick="window.open('${url}', '_blank')">
                      <i data-lucide="external-link" class="h-4 w-4 mr-1"></i> View PDF
                    </button>
                  </div>
                </div>
              </div>`;
          } else {
            containerEl.innerHTML = `
              <div class="h-full flex items-center justify-center">
                <div class="text-center">
                  <i data-lucide="file" class="h-12 w-12 mx-auto mb-3 text-muted-foreground"></i>
                  <p class="text-sm">Unsupported file: ${scanning.original_filename}</p>
                  <button class="btn btn-outline btn-sm mt-2" onclick="window.open('${url}', '_blank')">
                    <i data-lucide="external-link" class="h-4 w-4 mr-1"></i> Open
                  </button>
                </div>
              </div>`;
          }

          lucide.createIcons();
        }

        function updateDocumentZoom() {
          const span = document.querySelector('.zoom-level');
          const img = document.querySelector('.document-image');
          if (span) span.textContent = `${state.typingState.zoomLevel}%`;
          if (img) img.style.transform = `scale(${state.typingState.zoomLevel / 100}) rotate(${state.typingState.rotation}deg)`;
        }

        function updateDocumentRotation() {
          const img = document.querySelector('.document-image');
          if (img) img.style.transform = `scale(${state.typingState.zoomLevel / 100}) rotate(${state.typingState.rotation}deg)`;
        }

        // UI update functions
        function updateUI() {
          updateStats();
          
          // Only render the active tab to improve performance
          switch(state.activeTab) {
            case 'pending':
              renderPendingFiles();
              break;
            case 'in-progress':
              renderInProgressFiles();
              break;
            case 'completed':
              renderCompletedFilesTable();
              break;
            case 'pagetype-more':
              renderPageTypeMoreFiles();
              break;
            case 'typing':
              renderTypingView();
              break;
          }
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
        }

        // Start page typing for a file
        async function startPageTyping(fileId, options = {}) {
          try {
            state.pageTypeMoreMode = !!options.pageTypeMore;

            // Load page typing data first
            await loadPageTypingData();
            
            // Load file details
            const response = await fetch(`{{ route("pagetyping.api.file-details") }}?file_indexing_id=${fileId}`);
            const data = await response.json();
            
            if (data.success) {
              state.selectedFile = fileId;
              state.selectedFileData = data.file;
              state.activeTab = 'typing';

              // Pull initial serial number from backend if provided
              const initialSerial = (data.file && (data.file.next_serial || data.file.next_serial_no)) || data.next_serial || '01';
              const serialStr = initialSerial.toString().padStart(2, '0');
              
              // Initialize typing state
              state.typingState = {
                currentPage: 1,
                typedContent: '',
                typingProgress: 0,
                zoomLevel: 100,
                rotation: 0,
                showFolderView: true,
                selectedPageInFolder: null,
                coverType: (coverTypes[0]?.id || '1').toString(),
                pageType: (pageTypes[0]?.id || '1').toString(),
                pageSubType: '1',
                serialNo: serialStr,
                batchMode: false,
                batchTypedPages: {},
                batchSubmitReady: false,
                batchProgress: 0,
                batchProcessing: false,
                processedPages: {},
                bookletMode: false,
                currentBooklet: null,
                bookletStartPage: null,
                bookletPages: {},
                bookletCounter: 'a'
              };
              
              // Set initial page subtype based on page type
              if (pageSubTypes[parseInt(state.typingState.pageType)]) {
                state.typingState.pageSubType = pageSubTypes[parseInt(state.typingState.pageType)][0]?.id.toString() || '1';
              }

              // Pre-mark processed pages from backend (existing page typings)
              try {
                const scannings = state.selectedFileData.scannings || [];
                scannings.forEach((scan, idx) => {
                  const pts = (scan.page_typings || []);
                  if (Array.isArray(pts) && pts.length > 0) {
                    // Use the first typing for code display
                    const first = pts[0];
                    state.typingState.processedPages[idx] = {
                      page_code: first.page_code || null
                    };
                  }
                });
              } catch (e) {
                console.warn('Could not initialize processed pages from backend', e);
              }
              
              updateUI();
            } else {
              alert('Error loading file details: ' + data.message);
            }
          } catch (error) {
            console.error('Error starting page typing:', error);
            alert('Error loading file details');
          }
        }

        // Load and render pending files
        async function renderPendingFiles() {
          if (!elements.pendingFilesList) return;
          
          try {
            const response = await fetch('{{ route("pagetyping.api.files") }}?status=pending');
            const data = await response.json();
            
            if (data.success && data.files.length > 0) {
              elements.pendingFilesList.innerHTML = data.files.map(file => `
                <div class="p-4 border-b last:border-b-0 hover:bg-gray-50">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                          <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="file-text" class="h-5 w-5 text-yellow-600"></i>
                          </div>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-sm font-medium text-gray-900 truncate">${file.file_number}</p>
                          <p class="text-sm text-gray-500 truncate">${file.file_title}</p>
                          <div class="flex items-center gap-4 mt-1">
                            <span class="text-xs text-gray-400">${file.scannings_count} pages scanned</span>
                            <span class="text-xs text-gray-400">${file.created_at}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="badge bg-yellow-500 text-white">Pending</span>
                      <button class="btn btn-primary btn-sm start-typing" data-id="${file.id}">
                        <i data-lucide="type" class="h-4 w-4 mr-1"></i>
                        Start Typing
                      </button>
                    </div>
                  </div>
                </div>
              `).join('');
              
              // Add event listeners
              document.querySelectorAll('.start-typing').forEach(btn => {
                btn.addEventListener('click', () => {
                  const fileId = btn.getAttribute('data-id');
                  startPageTyping(fileId);
                });
              });
            } else {
              elements.pendingFilesList.innerHTML = `
                <div class="rounded-md border p-8 text-center">
                  <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <i data-lucide="file-text" class="h-6 w-6"></i>
                  </div>
                  <h3 class="mb-2 text-lg font-medium">No pending files</h3>
                  <p class="mb-4 text-sm text-muted-foreground">All files have been processed</p>
                </div>
              `;
            }
            lucide.createIcons();
          } catch (error) {
            console.error('Error loading pending files:', error);
            elements.pendingFilesList.innerHTML = `
              <div class="rounded-md border p-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                  <i data-lucide="alert-circle" class="h-6 w-6 text-red-600"></i>
                </div>
                <h3 class="mb-2 text-lg font-medium">Error loading files</h3>
                <p class="mb-4 text-sm text-muted-foreground">Please try refreshing the page</p>
              </div>
            `;
            lucide.createIcons();
          }
        }

        // Load and render in-progress files
        async function renderInProgressFiles() {
          if (!elements.inProgressFilesList) return;
          
          try {
            const response = await fetch('{{ route("pagetyping.api.files") }}?status=in_progress');
            const data = await response.json();
            
            if (data.success && data.files.length > 0) {
              elements.inProgressFilesList.innerHTML = data.files.map(file => `
                <div class="p-4 border-b last:border-b-0 hover:bg-gray-50">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                          <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="clock" class="h-5 w-5 text-orange-600"></i>
                          </div>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-sm font-medium text-gray-900 truncate">${file.file_number}</p>
                          <p class="text-sm text-gray-500 truncate">${file.file_title}</p>
                          <div class="flex items-center gap-4 mt-1">
                            <span class="text-xs text-gray-400">${file.page_typings_count}/${file.scannings_count} pages typed</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                              <div class="bg-orange-500 h-2 rounded-full" style="width: ${file.progress}%"></div>
                            </div>
                            <span class="text-xs text-gray-400">${file.progress}%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="badge bg-orange-500 text-white">In Progress</span>
                      <button class="btn btn-primary btn-sm continue-typing" data-id="${file.id}">
                        <i data-lucide="edit" class="h-4 w-4 mr-1"></i>
                        Continue
                      </button>
                    </div>
                  </div>
                </div>
              `).join('');
              
              // Add event listeners
              document.querySelectorAll('.continue-typing').forEach(btn => {
                btn.addEventListener('click', () => {
                  const fileId = btn.getAttribute('data-id');
                  startPageTyping(fileId);
                });
              });
            } else {
              elements.inProgressFilesList.innerHTML = `
                <div class="rounded-md border p-8 text-center">
                  <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <i data-lucide="clock" class="h-6 w-6"></i>
                  </div>
                  <h3 class="mb-2 text-lg font-medium">No files in progress</h3>
                  <p class="mb-4 text-sm text-muted-foreground">Start typing a file to see it here</p>
                </div>
              `;
            }
            lucide.createIcons();
          } catch (error) {
            console.error('Error loading in-progress files:', error);
            elements.inProgressFilesList.innerHTML = `
              <div class="rounded-md border p-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                  <i data-lucide="alert-circle" class="h-6 w-6 text-red-600"></i>
                </div>
                <h3 class="mb-2 text-lg font-medium">Error loading files</h3>
                <p class="mb-4 text-sm text-muted-foreground">Please try refreshing the page</p>
              </div>
            `;
            lucide.createIcons();
          }
        }

        // Load and render completed files
        async function renderCompletedFilesTable() {
          if (!elements.completedFilesTableBody) return;
          
          try {
            const response = await fetch('{{ route("pagetyping.api.files") }}?status=completed');
            const data = await response.json();
            
            if (data.success && data.files.length > 0) {
              elements.completedFilesTableBody.innerHTML = data.files.map(file => `
                <tr class="hover:bg-gray-50">
                  <td class="p-3">
                    <span class="text-blue-600 font-medium">${file.file_number}</span>
                  </td>
                  <td class="p-3">
                    <div class="font-medium">${file.file_title}</div>
                    ${file.district ? `<div class="text-xs text-gray-500">${file.district}, ${file.lga || ''}</div>` : ''}
                  </td>
                  <td class="p-3 text-sm text-gray-500">${file.updated_at}</td>
                  <td class="p-3 text-sm text-gray-500">
                    ${file.main_application?.applicant_name || 'Unknown'}
                  </td>
                  <td class="p-3">
                    <span class="badge bg-green-500 text-white">
                      <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                      Completed
                    </span>
                  </td>
                  <td class="p-3">
                    <span class="badge badge-secondary">${file.page_typings_count} pages</span>
                  </td>
                  <td class="p-3">
                    <div class="flex items-center gap-2">
                      <button class="btn btn-ghost btn-sm view-file" data-id="${file.id}" title="View File">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                      </button>
                      <button class="btn btn-outline btn-sm edit-file" data-id="${file.id}" title="Edit">
                        <i data-lucide="edit" class="h-4 w-4"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              `).join('');
              
              // Add event listeners
              document.querySelectorAll('.view-file').forEach(btn => {
                btn.addEventListener('click', () => {
                  const fileId = btn.getAttribute('data-id');
                  startPageTyping(fileId);
                });
              });
              
              document.querySelectorAll('.edit-file').forEach(btn => {
                btn.addEventListener('click', () => {
                  const fileId = btn.getAttribute('data-id');
                  startPageTyping(fileId);
                });
              });
            } else {
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
            }
            lucide.createIcons();
          } catch (error) {
            console.error('Error loading completed files:', error);
            elements.completedFilesTableBody.innerHTML = `
              <tr>
                <td colspan="7" class="text-center p-8">
                  <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <i data-lucide="alert-circle" class="h-6 w-6 text-red-600"></i>
                  </div>
                  <h3 class="mb-2 text-lg font-medium">Error loading files</h3>
                  <p class="mb-4 text-sm text-muted-foreground">Please try refreshing the page</p>
                </td>
              </tr>
            `;
            lucide.createIcons();
          }
        }

        // Load and render PageType More files
        async function renderPageTypeMoreFiles() {
          if (!elements.pageTypeMoreTableBody) return;
          
          try {
            const response = await fetch('{{ route("pagetyping.api.pagetype-more-files") }}');
            const data = await response.json();
            
            console.log('PageType More API Response:', data); // Debug log
            
            if (data.success && data.files && data.files.length > 0) {
              pageTypeMoreFiles = data.files; // Store the files

              // Optional filtering by search term
              const term = (elements.pageTypeMoreSearch?.value || '').trim().toLowerCase();
              const files = term
                ? data.files.filter(f =>
                    (f.file_number || '').toLowerCase().includes(term) ||
                    (f.file_title || '').toLowerCase().includes(term) ||
                    (f.district || '').toLowerCase().includes(term) ||
                    (f.lga || '').toLowerCase().includes(term)
                  )
                : data.files;
              
              elements.pageTypeMoreTableBody.innerHTML = files.map(file => `
                <tr class="border-b hover:bg-muted/10">
                  <td class="p-3">
                    <span class="text-blue-600 font-medium">${file.file_number}</span>
                  </td>
                  <td class="p-3">
                    <div class="flex items-center gap-2">
                      <i data-lucide="file-plus" class="h-4 w-4 text-orange-500"></i>
                      <span class="font-medium">${file.file_title}</span>
                    </div>
                    ${file.district ? `<div class="text-xs text-gray-500">${file.district}, ${file.lga || ''}</div>` : ''}
                  </td>
                  <td class="p-3">
                    <span class="badge bg-green-500 text-white">${file.existing_pages}</span>
                  </td>
                  <td class="p-3">
                    <span class="badge bg-orange-500 text-white">${file.new_scans}</span>
                  </td>
                  <td class="p-3">
                    <span class="badge badge-secondary">${file.total_pages}</span>
                  </td>
                  <td class="p-3 text-sm text-muted-foreground">${file.last_updated}</td>
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
                </tr>
              `).join('');
              
              // Add event listeners for PageType More actions
              document.querySelectorAll('.pagetype-more-action').forEach(btn => {
                btn.addEventListener('click', () => {
                  const fileId = btn.getAttribute('data-id');
                  // Open typing view in PageType More mode
                  startPageTyping(fileId, { pageTypeMore: true });
                });
              });
              
              document.querySelectorAll('.view-combined').forEach(btn => {
                btn.addEventListener('click', () => {
            `;
            lucide.createIcons();
          }
        }

        // Render typing view with full page typing interface including CoverType
        function renderTypingView() {
          if (!elements.typingCard || !state.selectedFileData) return;
          
          const file = state.selectedFileData;
          
          // Use actual scannings for pages; no placeholders
          // file.scannings already contains document_path and original_filename

          let content = '';

          // Header content
          const headerContent = `
            <div class="p-6 border-b">
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                  <h2 class="text-lg font-semibold">
                    <span class="text-blue-600">${file.file_number}</span> - ${file.file_title}
                  </h2>
                  <p class="text-sm text-muted-foreground">
                    ${state.typingState.showFolderView && state.typingState.selectedPageInFolder === null
                      ? state.typingState.batchMode
                        ? "Select pages to type in batch mode"
                        : "Select a page to type or categorize"
                      : state.typingState.selectedPageInFolder !== null
                        ? `Categorizing Page ${state.typingState.selectedPageInFolder + 1}`
                        : `Typing Page ${state.typingState.currentPage} of ${file.total_pages}`}
                  </p>
                </div>
                <div class="flex items-center gap-2">
                  ${state.typingState.showFolderView && state.typingState.selectedPageInFolder === null
                    ? `<button class="btn ${state.typingState.batchMode ? 'btn-primary' : 'btn-outline'} btn-sm toggle-batch-mode">
                        <i data-lucide="check-square" class="h-4 w-4 mr-1"></i>
                        ${state.typingState.batchMode ? 'Exit Batch Mode' : 'Batch Mode'}
                      </button>`
                    : ''}
                  <button class="btn btn-outline btn-sm back-button">
                    ${state.typingState.selectedPageInFolder !== null ? 'Back to Folder' : 'Back to Dashboard'}
                  </button>
                </div>
              </div>
            </div>
          `;

          if (state.typingState.showFolderView) {
            if (state.typingState.selectedPageInFolder !== null) {
              // Page categorization view with CoverType
              content = `
                ${headerContent}
                <div class="p-6">
                  <div class="space-y-6">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-medium">Categorize Page ${state.typingState.selectedPageInFolder + 1}</h3>
                      <span class="badge bg-blue-500 text-white">${file.file_number}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <div class="border rounded-md p-4 h-[400px] bg-white relative" id="document-preview-container">
                          <!-- Document preview rendered dynamically -->
                        </div>
                      </div>

                      <div class="space-y-6">
                        <div class="space-y-4">
                          <div>
                            <label for="cover-type" class="block text-sm font-medium mb-1.5">Cover Type</label>
                            <select id="cover-type" class="input">
                              ${coverTypes.map(type =>
                                `<option value="${type.id}" ${state.typingState.coverType == type.id ? 'selected' : ''}>
                                  ${type.name} (${type.code})
                                </option>`
                              ).join('')}
                            </select>
                            <p class="text-xs text-muted-foreground mt-1">
                              Front Cover: Main documents with pagination | Back Cover: Supporting documents without pagination
                            </p>
                          </div>

                          <div>
                            <label for="page-type" class="block text-sm font-medium mb-1.5">Page Type</label>
                            <select id="page-type" class="input">
                              ${pageTypes.map(type =>
                                `<option value="${type.id}" ${state.typingState.pageType == type.id ? 'selected' : ''}>
                                  ${type.name} (${type.code})
                                </option>`
                              ).join('')}
                            </select>
                          </div>

                          <div>
                            <label for="page-subtype" class="block text-sm font-medium mb-1.5">Page Subtype</label>
                            <select id="page-subtype" class="input">
                              ${pageSubTypes[parseInt(state.typingState.pageType)]?.map(subtype =>
                                `<option value="${subtype.id}" ${state.typingState.pageSubType == subtype.id ? 'selected' : ''}>
                                  ${subtype.name} (${subtype.code})
                                </option>`
                              ).join('') || '<option value="">Select page type first</option>'}
                            </select>
                          </div>

                            <div>
                            <label for="serial-no" class="block text-sm font-medium mb-1.5">Serial Number</label>
                            <input id="serial-no" value="${state.typingState.serialNo}" class="input bg-gray-100" maxlength="3" readonly>
                            <p class="text-xs text-muted-foreground mt-1">Two-digit serial number (from backend)</p>
                            </div>
                        </div>

                        <div class="p-4 border rounded-md bg-muted/30">
                          <h4 class="font-medium mb-2">Page Code Preview</h4>
                          <div class="flex items-center gap-2">
                            <span class="badge bg-blue-500 text-white text-base py-1 px-3">
                              ${getCoverTypeById(state.typingState.coverType)?.code || 'XX'}-${getPageTypeById(state.typingState.pageType)?.code || 'XX'}-${getPageSubTypeById(state.typingState.pageType, state.typingState.pageSubType)?.code || 'XX'}-${state.typingState.serialNo}
                            </span>
                          </div>
                          <p class="text-xs text-muted-foreground mt-2">
                            Format: CoverType-PageType-SubType-SerialNo<br>
                            This code will be assigned to the page for easy identification and retrieval.
                          </p>
                        </div>

                        <button class="btn btn-primary w-full process-page">
                          Process Page
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              `;
            } else {
              // Folder view
              content = `
                ${headerContent}
                <div class="p-6">
                  <div class="space-y-6">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-medium">File Pages</h3>
                      <span class="badge bg-blue-500 text-white">${file.file_number}</span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="folder-pages">
                      ${file.scannings.map((scanning, index) => {
                        const isProcessed = state.typingState.processedPages[index];
                        const url = getDocumentUrl(scanning.document_path);
                        const img = isImageFile(scanning.original_filename);
                        const pdf = isPDFFile(scanning.original_filename);
                        return `
                          <div class="border rounded-md overflow-hidden cursor-pointer hover:border-blue-500 transition-colors folder-page ${isProcessed ? 'border-green-500 bg-green-50' : ''}" data-index="${index}">
                            <div class="h-40 bg-muted flex items-center justify-center relative">
                              ${isProcessed ? `<div class=\"absolute top-2 right-2 z-10\"><span class=\"badge bg-green-500 text-white\"><i data-lucide=\"check-circle\" class=\"h-3 w-3 mr-1\"></i>Typed</span></div>` : ''}
                              ${img && url ? `<img src=\"${url}\" alt=\"Page ${index + 1}\" class=\"max-h-full max-w-full object-contain\" onerror=\"this.style.display='none'; this.nextElementSibling.style.display='flex';\" />
                                <div class=\"text-center hidden\"><i data-lucide=\"file-text\" class=\"h-8 w-8 text-gray-400 mb-2\"></i><p class=\"text-xs text-gray-500\">${scanning.original_filename}</p></div>`
                              : `<div class=\"text-center\"><i data-lucide=\"${pdf ? 'file-text' : 'file'}\" class=\"h-8 w-8 text-gray-400 mb-2\"></i><p class=\"text-xs text-gray-500\">${scanning.original_filename}</p></div>`}
                            </div>
                            <div class="p-2 bg-gray-50 border-t">
                              <div class="flex justify-between items-center">
                                <span class="text-sm font-medium">Page ${index + 1}</span>
                                <span class="badge badge-outline text-xs">${pdf ? 'PDF' : (img ? 'Image' : 'File')}</span>
                              </div>
                              <div class="mt-1 text-xs text-muted-foreground">${file.file_number}-${(index + 1).toString().padStart(2, '0')}</div>
                              ${isProcessed ? `<div class=\"mt-1\"><span class=\"badge bg-blue-500 text-white text-xs w-full justify-center\">${getCoverTypeById(isProcessed.coverType)?.code}-${getPageTypeById(isProcessed.pageType)?.code}-${getPageSubTypeById(isProcessed.pageType, isProcessed.pageSubType)?.code}-${isProcessed.serialNo}</span></div>` : ''}
                            </div>
                          </div>`;
                      }).join('')}
                    </div>
                  </div>
                </div>
              `;
            }
          }

          elements.typingCard.innerHTML = content;
          lucide.createIcons();

          // Render preview if on a selected page
          if (state.typingState.selectedPageInFolder !== null) {
            const scanning = file.scannings[state.typingState.selectedPageInFolder];
            const container = document.getElementById('document-preview-container');
            if (container && scanning) renderDocumentPreview(scanning, container);
          }
          
          // Add event listeners
          addTypingEventListeners(file);
        }

        // Add event listeners for typing interface
        function addTypingEventListeners(file) {
          // Back button
          document.querySelector('.back-button')?.addEventListener('click', () => {
            if (state.typingState.selectedPageInFolder !== null) {
              state.typingState.selectedPageInFolder = null;
            } else {
              state.selectedFile = null;
              state.selectedFileData = null;
              state.typingState = null;
              state.activeTab = 'pending';
            }
            updateUI();
          });

          // Folder page selection
          document.querySelectorAll('.folder-page').forEach(page => {
            page.addEventListener('click', () => {
              const index = parseInt(page.getAttribute('data-index'));
              state.typingState.selectedPageInFolder = index;
              updateUI();
            });
          });

          // Cover type change
          document.querySelector('#cover-type')?.addEventListener('change', (e) => {
            state.typingState.coverType = e.target.value;
            updateUI();
          });

          // Page type change
          document.querySelector('#page-type')?.addEventListener('change', (e) => {
            state.typingState.pageType = e.target.value;
            state.typingState.pageSubType = pageSubTypes[parseInt(e.target.value)]?.[0]?.id.toString() || '1';
            updateUI();
          });

          // Page subtype change
          document.querySelector('#page-subtype')?.addEventListener('change', (e) => {
            state.typingState.pageSubType = e.target.value;
            updateUI();
          });

          // Serial number change (keep editable but seeded from backend)
          document.querySelector('#serial-no')?.addEventListener('input', (e) => {
            state.typingState.serialNo = e.target.value.padStart(2, '0');
            updateUI();
          });

          // Process page
          document.querySelector('.process-page')?.addEventListener('click', async () => {
            if (state.typingState.selectedPageInFolder === null) return;

            // Save page typing to backend with CoverType
            const selected = file.scannings[state.typingState.selectedPageInFolder];
            const pageData = {
              file_indexing_id: file.id,
              scanning_id: selected?.id || null,
              page_number: state.typingState.selectedPageInFolder + 1,
              cover_type_id: parseInt(state.typingState.coverType),
              page_type: state.typingState.pageType,
              page_subtype: state.typingState.pageSubType,
              serial_number: parseInt(state.typingState.serialNo),
              page_code: `${getCoverTypeById(state.typingState.coverType)?.code}-${getPageTypeById(state.typingState.pageType)?.code}-${getPageSubTypeById(state.typingState.pageType, state.typingState.pageSubType)?.code}-${state.typingState.serialNo}`,
              file_path: selected?.document_path || null
            };

            try {
              const response = await fetch('{{ route("pagetyping.save-single") }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(pageData)
              });

              const result = await response.json();
              
              if (result.success) {
                // Mark page as processed
                state.typingState.processedPages[state.typingState.selectedPageInFolder] = {
                  coverType: state.typingState.coverType,
                  pageType: state.typingState.pageType,
                  pageSubType: state.typingState.pageSubType,
                  serialNo: state.typingState.serialNo
                };

                // Increment serial number
                const nextSerialNo = parseInt(state.typingState.serialNo) + 1;
                state.typingState.serialNo = nextSerialNo.toString().padStart(2, '0');

                // Go back to folder view
                state.typingState.selectedPageInFolder = null;
                
                alert('Page processed successfully!');
                updateUI();
              } else {
                alert('Error processing page: ' + result.message);
              }
            } catch (error) {
              console.error('Error processing page:', error);
              alert('Error processing page');
            }
          });

          // Zoom controls
          document.querySelector('.zoom-in')?.addEventListener('click', () => {
            if (state.typingState.zoomLevel < 200) {
              state.typingState.zoomLevel += 25;
              updateDocumentZoom();
            }
          });

          document.querySelector('.zoom-out')?.addEventListener('click', () => {
            if (state.typingState.zoomLevel > 50) {
              state.typingState.zoomLevel -= 25;
              updateDocumentZoom();
            }
          });

          document.querySelector('.rotate')?.addEventListener('click', () => {
            state.typingState.rotation = (state.typingState.rotation + 90) % 360;
            updateDocumentRotation();
          });
        }

        // Event handlers
        function switchTab(tabId) {
          state.activeTab = tabId;
          updateUI();
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
          // Load page typing data on startup
          loadPageTypingData();
          
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
        
        // Backend Integration Functions
        // Load real statistics from backend
        async function loadRealStats() {
            try {
                const response = await fetch('{{ route("pagetyping.api.stats") }}');
                const data = await response.json();
                
                if (data.success) {
                    // Update the stats in the UI
                    if (elements.pendingCount) elements.pendingCount.textContent = data.stats.pending_count || 0;
                    if (elements.inProgressCount) elements.inProgressCount.textContent = data.stats.in_progress_count || 0;
                    if (elements.completedCount) elements.completedCount.textContent = data.stats.completed_count || 0;
                    if (elements.pageTypeMoreCount) elements.pageTypeMoreCount.textContent = data.stats.pagetype_more_count || 0;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }
        
        // Initialize real data loading
        setTimeout(() => {
            loadRealStats();
            // Load PageType More files when that tab is active
            if (state.activeTab === 'pagetype-more') {
                renderPageTypeMoreFiles();
            }
        }, 1000);
        
        // Refresh data every 30 seconds
        setInterval(() => {
            loadRealStats();
            // Only refresh the active tab
            if (state.activeTab === 'pagetype-more') {
                renderPageTypeMoreFiles();
            }
        }, 30000);
    </script>

@endsection