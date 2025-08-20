<style>
        
        
        .mt-6 { margin-top: 1.5rem; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .flex-row { flex-direction: row; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .gap-4 { gap: 1rem; }
        .w-full { width: 100%; }
        .w-auto { width: auto; }
        .relative { position: relative; }
        .absolute { position: absolute; }
        .left-2\.5 { left: 0.625rem; }
        .top-2\.5 { top: 0.625rem; }
        .h-4 { height: 1rem; }
        .w-4 { width: 1rem; }
        .text-muted-foreground { color: #6b7280; }
        .pl-8 { padding-left: 2rem; }
        .bg-transparent { background-color: transparent; }
        .rounded-md { border-radius: 0.375rem; }
        .border { border: 1px solid #e5e7eb; }
        .p-8 { padding: 2rem; }
        .text-center { text-align: center; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .mb-4 { margin-bottom: 1rem; }
        .h-12 { height: 3rem; }
        .w-12 { width: 3rem; }
        .bg-muted { background-color: #f3f4f6; }
        .rounded-full { border-radius: 9999px; }
        .h-6 { height: 1.5rem; }
        .w-6 { width: 1.5rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
        .font-medium { font-weight: 500; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .overflow-x-auto { overflow-x: auto; }
        .min-w-\[150px\] { min-width: 150px; }
        .min-w-\[200px\] { min-width: 200px; }
        .min-w-\[120px\] { min-width: 120px; }
        .min-w-\[100px\] { min-width: 100px; }
        .cursor-pointer { cursor: pointer; }
        .ml-2 { margin-left: 0.5rem; }
        .inline { display: inline; }
        .text-right { text-align: right; }
        .justify-end { justify-content: flex-end; }
        .border-t { border-top: 1px solid #e5e7eb; }
        .pt-4 { padding-top: 1rem; }
        .mr-2 { margin-right: 0.5rem; }

        /* Card styles */
        .Card {
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .CardHeader {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .CardTitle {
            font-size: 1.25rem;
            font-weight: 600;
            line-height: 1.75rem;
            margin-bottom: 0.25rem;
        }
        
        .CardDescription {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .CardContent {
            padding: 1.5rem;
        }
        
        .CardFooter {
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e5e7eb;
        }
        
        /* Table styles */
        .Table {
            width: 100%;
            font-size: 0.875rem;
            text-align: left;
            border-collapse: collapse;
        }
        
        .TableHeader {
            background-color: #f9fafb;
        }
        
        .TableRow {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .TableHead {
            padding: 0.75rem 1rem;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .TableCell {
            padding: 1rem;
            vertical-align: middle;
        }
        
        /* Button styles */
        .Button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            line-height: 1.25rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .Button:disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        
        .Button-variant-outline {
            background-color: white;
            border: 1px solid #e5e7eb;
            color: #374151;
        }
        
        .Button-variant-outline:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }
        
        .Button-variant-default {
            background-color: #3b82f6;
            border: 1px solid #3b82f6;
            color: white;
        }
        
        .Button-variant-default:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        
        .Button-size-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1rem;
        }
        
        /* Badge styles */
        .Badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1rem;
        }
        
        .Badge-variant-default {
            background-color: #3b82f6;
            color: white;
        }
        
        .Badge-variant-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .Badge-variant-destructive {
            background-color: #ef4444;
            color: white;
        }
        
        .Badge-variant-outline {
            background-color: transparent;
            border: 1px solid #e5e7eb;
            color: #374151;
        }
        
        .Badge-variant-black {
            background-color: #000;
            color: white;
        }

        .Badge-variant-green {
            background-color: #10b981;
            color: white;
        }

        .Badge-variant-blue {
            background-color: #3b82f6;
            color: white;
        }

        .Badge-variant-purple {
            background-color: #8b5cf6;
            color: white;
        }

        /* Input styles */
        .Input {
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            width: 100%;
            transition: border-color 0.2s;
        }
        
        .Input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }
        
        /* Responsive styles */
        @media (min-width: 768px) {
            .md\:flex-row {
                flex-direction: row;
            }
            
            .md\:items-center {
                align-items: center;
            }
            
            .md\:w-auto {
                width: auto;
            }
        }

        /* Initially hide empty state and footer */
        #empty-state {
            display: none;
        }
        
        #card-footer {
            display: none;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 1rem;
        }
        
        .detail-label {
            font-weight: 500;
            width: 150px;
            color: #6b7280;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .keywords-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .keyword-badge {
            background-color: #e5e7eb;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
        }
        
        .problematic-badge {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>
    
    <div class="TabsContent mt-6">
        <div class="Card">
            <div class="CardHeader">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="CardTitle">Indexed Files Report</h3>
                        <p class="CardDescription">Comprehensive report of all successfully indexed files.</p>
                    </div>
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <div class="relative flex-1">
                            <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                            <input type="search" placeholder="Search indexed files..." class="Input w-full pl-8" id="search-input">
                        </div>
                        <div class="relative">
                            <button class="Button Button-variant-outline gap-2 bg-transparent" id="actions-menu-btn">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="12" cy="5" r="1"></circle>
                                    <circle cx="12" cy="19" r="1"></circle>
                                </svg>
                                Actions
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-10 hidden" id="actions-menu">
                                <div class="py-1">
                                    <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" id="generate-tracking-sheet">
                                        <svg class="h-4 w-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        Generate Tracking Sheet
                                    </button>
                                    <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" id="print-tracking-sheet">
                                        <svg class="h-4 w-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2 2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                            <rect width="12" height="8" x="6" y="14"></rect>
                                        </svg>
                                        Print Tracking Sheet
                                    </button>
                                    <hr class="my-1">
                                    <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" id="download-report">
                                        <svg class="h-4 w-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        Download Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="CardContent">
                <div id="empty-state" class="rounded-md border p-8 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <line x1="10" y1="9" x2="8" y2="9"></line>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-medium">No indexed files yet</h3>
                    <p class="mb-4 text-sm text-muted-foreground">
                        Complete the indexing process to see files here
                    </p>
                    <button class="Button Button-variant-default gap-2" id="go-to-pending">
                        Go to Pending Files
                    </button>
                </div>
                <div id="table-container" class="rounded-md border overflow-x-auto">
                    <table class="Table">
                        <thead class="TableHeader">
                            <tr class="TableRow">
                                <th class="TableHead min-w-[50px]">
                                    <input type="checkbox" id="select-all-indexed" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </th>
                                <th class="TableHead cursor-pointer min-w-[150px]" data-sort="file_number">
                                    File&nbsp;Number
                                    <svg class="ml-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m21 16-4 4-4-4"></path>
                                        <path d="M17 20V4"></path>
                                        <path d="m3 8 4-4 4 4"></path>
                                        <path d="M7 4v16"></path>
                                    </svg>
                                </th>
                                <th class="TableHead cursor-pointer min-w-[200px]" data-sort="file_title">
                                    File&nbsp;Title
                                    <svg class="ml-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m21 16-4 4-4-4"></path>
                                        <path d="M17 20V4"></path>
                                        <path d="m3 8 4-4 4 4"></path>
                                        <path d="M7 4v16"></path>
                                    </svg>
                                </th>
                                <th class="TableHead cursor-pointer min-w-[120px]" data-sort="created_at">
                                    <span class="flex items-center">
                                        Indexed&nbsp;Date
                                        <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m21 16-4 4-4-4"></path>
                                            <path d="M17 20V4"></path>
                                            <path d="m3 8 4-4 4 4"></path>
                                            <path d="M7 4v16"></path>
                                        </svg>
                                    </span>
                                </th>
                                <th class="TableHead min-w-[120px]">
                                    Status
                                </th>
                                <th class="TableHead cursor-pointer min-w-[120px]" data-sort="land_use_type">
                                    Land&nbsp;Use
                                    <svg class="ml-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m21 16-4 4-4-4"></path>
                                        <path d="M17 20V4"></path>
                                        <path d="m3 8 4-4 4 4"></path>
                                        <path d="M7 4v16"></path>
                                    </svg>
                                </th>
                                <th class="TableHead cursor-pointer min-w-[120px]" data-sort="district">
                                    District
                                    <svg class="ml-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m21 16-4 4-4-4"></path>
                                        <path d="M17 20V4"></path>
                                        <path d="m3 8 4-4 4 4"></path>
                                        <path d="M7 4v16"></path>
                                    </svg>
                                </th>
                                <th class="TableHead min-w-[100px]">
                                    Plot&nbsp;Number
                                </th>
                                <th class="TableHead text-right min-w-[100px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="TableBody">
                            <!-- Table rows will be inserted here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="card-footer" class="CardFooter">
                <button class="Button Button-variant-outline" id="index-more-files">
                    Index More Files
                </button>
                <div class="flex gap-2">
                    <button class="Button Button-variant-outline" id="export-csv">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        Export CSV
                    </button>
                    <button class="Button Button-variant-default" id="print-labels">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2 2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect width="12" height="8" x="6" y="14"></rect>
                        </svg>
                        Print Labels
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for file details -->
    <div id="file-details-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">File Details</h3>
                <button class="close-btn" id="close-modal">&times;</button>
            </div>
            <div id="modal-content">
                <!-- File details will be inserted here -->
            </div>
        </div>
    </div>

    <script>
        // Dynamic data from database with tracking status
        const indexedFiles = @json($recentIndexes ?? []);
        
        // Add tracking status to indexed files
        let fileTrackingStatus = {};
        
        // Fetch tracking status for all indexed files
        async function loadTrackingStatus() {
            try {
                const response = await fetch('/api/file-trackings');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        // Create a map of file_indexing_id to tracking status
                        data.data.forEach(tracking => {
                            if (tracking.file_indexing_id) {
                                fileTrackingStatus[tracking.file_indexing_id] = {
                                    id: tracking.id,
                                    status: tracking.status,
                                    rfid_tag: tracking.rfid_tag,
                                    qr_code: tracking.qr_code,
                                    current_location: tracking.current_location,
                                    current_handler: tracking.current_handler,
                                    date_received: tracking.date_received,
                                    due_date: tracking.due_date,
                                    created_at: tracking.created_at,
                                    updated_at: tracking.updated_at
                                };
                            }
                        });
                        console.log('Loaded tracking status for', Object.keys(fileTrackingStatus).length, 'files');
                        // Re-render table with tracking status
                        renderTable();
                    }
                }
            } catch (error) {
                console.error('Error loading tracking status:', error);
            }
        }

        // DOM elements
        const emptyState = document.getElementById('empty-state');
        const tableContainer = document.getElementById('table-container');
        const tableBody = document.getElementById('table-body');
        const cardFooter = document.getElementById('card-footer');
        const searchInput = document.getElementById('search-input');
        const goToPendingBtn = document.getElementById('go-to-pending');
        const indexMoreFilesBtn = document.getElementById('index-more-files');
        const printLabelsBtn = document.getElementById('print-labels');
        const downloadReportBtn = document.getElementById('download-report');
        const exportCsvBtn = document.getElementById('export-csv');
        const sortableHeaders = document.querySelectorAll('[data-sort]');
        const modal = document.getElementById('file-details-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const modalContent = document.getElementById('modal-content');
        const modalTitle = document.getElementById('modal-title');
        const selectAllCheckbox = document.getElementById('select-all-indexed');
        const actionsMenuBtn = document.getElementById('actions-menu-btn');
        const actionsMenu = document.getElementById('actions-menu');
        const generateTrackingSheetBtn = document.getElementById('generate-tracking-sheet');
        const printTrackingSheetBtn = document.getElementById('print-tracking-sheet');

        // Selected files tracking
        let selectedFiles = new Set();

        // Current sort state
        let currentSort = {
            field: 'file_number',
            direction: 'asc'
        };

        // Filtered and sorted files
        let filteredAndSortedIndexedFiles = [...indexedFiles];

        // Initialize the page
        function init() {
            loadTrackingStatus(); // Load tracking status first
            renderTable();
            updateView();
            
            // Set up event listeners
            searchInput.addEventListener('input', handleSearch);
            goToPendingBtn.addEventListener('click', () => {
                // Switch to pending tab
                const pendingTab = document.querySelector('[data-tab="pending"]');
                if (pendingTab) {
                    pendingTab.click();
                }
            });
            
            indexMoreFilesBtn.addEventListener('click', () => {
                // Switch to pending tab
                const pendingTab = document.querySelector('[data-tab="pending"]');
                if (pendingTab) {
                    pendingTab.click();
                }
            });
            
            printLabelsBtn.addEventListener('click', sendToLabelPrinting);
            downloadReportBtn.addEventListener('click', downloadReport);
            exportCsvBtn.addEventListener('click', exportToCsv);
            
            sortableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const field = header.getAttribute('data-sort');
                    handleSort(field);
                });
            });

            // Modal close button
            closeModalBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            // Close modal when clicking outside
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Update view based on data
        function updateView() {
            if (filteredAndSortedIndexedFiles.length === 0) {
                emptyState.style.display = 'block';
                tableContainer.style.display = 'none';
                cardFooter.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
                tableContainer.style.display = 'block';
                cardFooter.style.display = 'flex';
            }
        }

        // Get status badge based on file status
        function getStatusBadge(file) {
            // Check if file has tracking information
            const trackingInfo = fileTrackingStatus[file.id];
            if (trackingInfo) {
                // File is being tracked - show tracking status
                const statusColors = {
                    'active': 'Badge-variant-green',
                    'checked_out': 'Badge-variant-blue',
                    'overdue': 'Badge-variant-destructive',
                    'returned': 'Badge-variant-outline',
                    'lost': 'Badge-variant-destructive',
                    'archived': 'Badge-variant-secondary',
                    'in_process': 'Badge-variant-blue',
                    'pending': 'Badge-variant-outline',
                    'on_hold': 'Badge-variant-destructive',
                    'completed': 'Badge-variant-green'
                };
                const statusText = trackingInfo.status.replace('_', ' ').toUpperCase();
                const statusClass = statusColors[trackingInfo.status] || 'Badge-variant-secondary';
                return `<span class="Badge ${statusClass}">TRACKED: ${statusText}</span>`;
            } else {
                // File is not being tracked - show indexing status
                const statusText = file.pagetypings_count > 0
                    ? 'Typed'
                    : file.scannings_count > 0
                        ? 'Scanned'
                        : 'Indexed';
                return `<span class="Badge Badge-variant-green">${statusText}</span>`;
            }
        }

        // Check if file can be selected for tracking (i.e., doesn't have tracking info)
        function canSelectForTracking(file) {
            return !fileTrackingStatus[file.id];
        }

        // Format date for display
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Render the table with current data
        function renderTable() {
            tableBody.innerHTML = '';
            
            filteredAndSortedIndexedFiles.forEach(file => {
                const row = document.createElement('tr');
                row.className = 'TableRow';
                
                // Add problematic file styling
                if (file.is_problematic) {
                    row.style.backgroundColor = '#fff1f1';
                }
                
                // Check if file can be selected for tracking
                const canSelect = canSelectForTracking(file);
                const checkboxDisabled = !canSelect;
                const checkboxTitle = canSelect ? 'Select for tracking sheet generation' : 'File is already being tracked';
                
                row.innerHTML = `
                    <td class="TableCell">
                        <input type="checkbox" 
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 file-checkbox" 
                               data-file-id="${file.id}" 
                               ${selectedFiles.has(file.id) ? 'checked' : ''}
                               ${checkboxDisabled ? 'disabled' : ''}
                               title="${checkboxTitle}"
                               style="${checkboxDisabled ? 'opacity: 0.5; cursor: not-allowed;' : ''}">
                    </td>
                    <td class="TableCell font-medium">${file.file_number || 'N/A'}</td>
                    <td class="TableCell">${file.file_title || 'Untitled'}</td>
                    <td class="TableCell">${formatDate(file.created_at)}</td>
                    <td class="TableCell">
                        ${getStatusBadge(file)}
                    </td>
                    <td class="TableCell">
                        <span class="Badge Badge-variant-outline text-xs">
                            ${file.land_use_type || 'N/A'}
                        </span>
                    </td>
                    <td class="TableCell">${file.district || 'N/A'}</td>
                    <td class="TableCell">${file.plot_number || 'N/A'}</td>
                    <td class="TableCell text-right">
                        <div class="flex justify-end gap-2">
                            <button class="Button Button-variant-outline Button-size-sm view-btn" data-id="${file.id}">
                                View
                            </button>
                            ${!canSelect ? `
                                <a href="{{ route('filetracker.index') }}?selected=${fileTrackingStatus[file.id] && fileTrackingStatus[file.id].id ? fileTrackingStatus[file.id].id : ''}" 
                                   class="Button Button-variant-blue Button-size-sm"
                                   title="View in File Tracker">
                                    Track
                                </a>
                            ` : file.scannings_count === 0 ? `
                                <a href="{{ route('scanning.index') }}?file_indexing_id=${file.id}" class="Button Button-variant-default Button-size-sm">
                                    Scan
                                </a>
                            ` : file.pagetypings_count === 0 ? `
                                <a href="{{ route('pagetyping.index') }}?file_indexing_id=${file.id}" class="Button Button-variant-default Button-size-sm">
                                    Type
                                </a>
                            ` : ''}
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
            
            // Add event listeners to view buttons
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const fileId = e.target.getAttribute('data-id');
                    showFileDetails(fileId);
                });
            });

            // Add event listeners to checkboxes (only enabled ones)
            document.querySelectorAll('.file-checkbox:not([disabled])').forEach(checkbox => {
                checkbox.addEventListener('change', handleFileSelection);
            });

            updateSelectAllCheckbox();
        }

        // Show file details in modal
        function showFileDetails(fileId) {
            const file = indexedFiles.find(f => f.id == fileId);
            if (!file) return;

            modalTitle.textContent = `File Details: ${file.file_number}`;
            
            let detailsHTML = `
                <div class="detail-row">
                    <div class="detail-label">File Number:</div>
                    <div class="detail-value">${file.file_number || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">File Title:</div>
                    <div class="detail-value">${file.file_title || 'Untitled'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Indexed Date:</div>
                    <div class="detail-value">${formatDate(file.created_at)}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        ${getStatusBadge(file)}
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Land Use Type:</div>
                    <div class="detail-value">${file.land_use_type || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Plot Number:</div>
                    <div class="detail-value">${file.plot_number || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">District:</div>
                    <div class="detail-value">${file.district || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">LGA:</div>
                    <div class="detail-value">${file.lga || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Has COFO:</div>
                    <div class="detail-value">${file.has_cofo ? 'Yes' : 'No'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Has Transaction:</div>
                    <div class="detail-value">${file.has_transaction ? 'Yes' : 'No'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Is Merged:</div>
                    <div class="detail-value">${file.is_merged ? 'Yes' : 'No'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Co-Owned Plot:</div>
                    <div class="detail-value">${file.is_co_owned_plot ? 'Yes' : 'No'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Scanned Documents:</div>
                    <div class="detail-value">${file.scannings_count || 0}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Typed Pages:</div>
                    <div class="detail-value">${file.pagetypings_count || 0}</div>
                </div>
                ${file.is_problematic ? `
                <div class="detail-row">
                    <div class="detail-label">Problematic:</div>
                    <div class="detail-value">
                        <span class="problematic-badge">Yes</span>
                    </div>
                </div>
                ` : ''}
                
                <!-- Add tracking status if available -->
                ${fileTrackingStatus[file.id] ? `
                <div class="detail-row">
                    <div class="detail-label">Tracking Status:</div>
                    <div class="detail-value">
                        <span class="Badge Badge-variant-blue">TRACKED: ${fileTrackingStatus[file.id].status.replace('_', ' ').toUpperCase()}</span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Current Location:</div>
                    <div class="detail-value">${fileTrackingStatus[file.id].current_location || 'Not specified'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Current Handler:</div>
                    <div class="detail-value">${fileTrackingStatus[file.id].current_handler || 'Not specified'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">RFID Tag:</div>
                    <div class="detail-value">${fileTrackingStatus[file.id].rfid_tag || 'Not assigned'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">QR Code:</div>
                    <div class="detail-value">${fileTrackingStatus[file.id].qr_code || 'Not assigned'}</div>
                </div>
                ` : `
                <div class="detail-row">
                    <div class="detail-label">Tracking Status:</div>
                    <div class="detail-value">
                        <span class="Badge Badge-variant-outline">Not being tracked</span>
                        <br><small class="text-gray-500">This file can be selected for tracking sheet generation</small>
                    </div>
                </div>
                `}
            `;

            modalContent.innerHTML = detailsHTML;
            modal.style.display = 'flex';
        }

        // Handle search input
        function handleSearch() {
            const searchTerm = searchInput.value.toLowerCase();
            
            if (searchTerm === '') {
                filteredAndSortedIndexedFiles = [...indexedFiles];
            } else {
                filteredAndSortedIndexedFiles = indexedFiles.filter(file => 
                    (file.file_number && file.file_number.toLowerCase().includes(searchTerm)) ||
                    (file.file_title && file.file_title.toLowerCase().includes(searchTerm)) ||
                    (file.land_use_type && file.land_use_type.toLowerCase().includes(searchTerm)) ||
                    (file.district && file.district.toLowerCase().includes(searchTerm)) ||
                    (file.lga && file.lga.toLowerCase().includes(searchTerm)) ||
                    (file.plot_number && file.plot_number.toLowerCase().includes(searchTerm))
                );
            }
            
            // Re-apply sorting
            sortFiles(currentSort.field, currentSort.direction);
            renderTable();
            updateView();
        }

        // Handle sorting
        function handleSort(field) {
            // Toggle direction if same field is clicked
            const direction = currentSort.field === field && currentSort.direction === 'asc' ? 'desc' : 'asc';
            
            currentSort = { field, direction };
            sortFiles(field, direction);
            renderTable();
        }

        // Sort files by field and direction
        function sortFiles(field, direction) {
            filteredAndSortedIndexedFiles.sort((a, b) => {
                // Handle different field types
                if (field === 'created_at') {
                    const dateA = new Date(a[field]);
                    const dateB = new Date(b[field]);
                    return direction === 'asc' ? dateA - dateB : dateB - dateA;
                } else {
                    // Default string comparison
                    const valueA = String(a[field] || '').toLowerCase();
                    const valueB = String(b[field] || '').toLowerCase();
                    return direction === 'asc' 
                        ? valueA.localeCompare(valueB) 
                        : valueB.localeCompare(valueA);
                }
            });
        }

        // Send to label printing
        function sendToLabelPrinting() {
            if (filteredAndSortedIndexedFiles.length === 0) {
                alert('No files to print labels for');
                return;
            }
            
            const fileNumbers = filteredAndSortedIndexedFiles.map(f => f.file_number).join(', ');
            alert(`Printing labels for ${filteredAndSortedIndexedFiles.length} files`);
            
            // Here you would typically send the data to a label printing service
            // window.open('/print-labels?files=' + encodeURIComponent(JSON.stringify(filteredAndSortedIndexedFiles)));
        }

        // Download report
        function downloadReport() {
            if (filteredAndSortedIndexedFiles.length === 0) {
                alert('No files to download');
                return;
            }
            
            alert('Downloading report as PDF');
            // Here you would typically generate and download a PDF report
            // window.open('/download-report?files=' + encodeURIComponent(JSON.stringify(filteredAndSortedIndexedFiles)));
        }

        // Export to CSV
        function exportToCsv() {
            if (filteredAndSortedIndexedFiles.length === 0) {
                alert('No files to export');
                return;
            }
            
            // Create CSV content
            const headers = ['File Number', 'File Title', 'Indexed Date', 'Land Use Type', 'District', 'LGA', 'Plot Number', 'Has COFO', 'Has Transaction', 'Status'];
            const csvContent = [
                headers.join(','),
                ...filteredAndSortedIndexedFiles.map(file => [
                    `"${file.file_number || ''}"`,
                    `"${file.file_title || ''}"`,
                    `"${formatDate(file.created_at)}"`,
                    `"${file.land_use_type || ''}"`,
                    `"${file.district || ''}"`,
                    `"${file.lga || ''}"`,
                    `"${file.plot_number || ''}"`,
                    `"${file.has_cofo ? 'Yes' : 'No'}"`,
                    `"${file.has_transaction ? 'Yes' : 'No'}"`,
                    `"${file.pagetypings_count > 0 ? 'Typed' : file.scannings_count > 0 ? 'Scanned' : 'Indexed'}"`
                ].join(','))
            ].join('\n');
            
            // Create and download file
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `indexed_files_report_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Handle file selection
        function handleFileSelection(event) {
            const fileId = parseInt(event.target.getAttribute('data-file-id'));
            
            if (event.target.checked) {
                selectedFiles.add(fileId);
            } else {
                selectedFiles.delete(fileId);
            }
            
            updateSelectAllCheckbox();
            updateNewFileIndexButton();
        }

        // Handle select all checkbox
        function handleSelectAll(event) {
            const isChecked = event.target.checked;
            
            if (isChecked) {
                // Select all visible files
                filteredAndSortedIndexedFiles.forEach(file => {
                    selectedFiles.add(file.id);
                });
            } else {
                // Deselect all files
                selectedFiles.clear();
            }
            
            // Update individual checkboxes
            document.querySelectorAll('.file-checkbox').forEach(checkbox => {
                const fileId = parseInt(checkbox.getAttribute('data-file-id'));
                checkbox.checked = selectedFiles.has(fileId);
            });
            
            updateNewFileIndexButton();
        }

        // Update select all checkbox state
        function updateSelectAllCheckbox() {
            const visibleFileIds = filteredAndSortedIndexedFiles.map(f => f.id);
            const selectedVisibleFiles = visibleFileIds.filter(id => selectedFiles.has(id));
            
            if (selectedVisibleFiles.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (selectedVisibleFiles.length === visibleFileIds.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        // Update New File Index button based on selection
        function updateNewFileIndexButton() {
            const newFileIndexBtn = document.getElementById('new-file-index-btn');
            if (!newFileIndexBtn) return;

            const selectedCount = selectedFiles.size;
            
            if (selectedCount === 0) {
                newFileIndexBtn.innerHTML = `
                    <i data-lucide="folder-plus" class="h-5 w-5 mr-2"></i>
                    <span class="font-medium">New File Index</span>
                `;
            } else if (selectedCount === 1) {
                newFileIndexBtn.innerHTML = `
                    <i data-lucide="file-text" class="h-5 w-5 mr-2"></i>
                    <span class="font-medium">Generate Tracking Sheet</span>
                `;
            } else {
                newFileIndexBtn.innerHTML = `
                    <i data-lucide="files" class="h-5 w-5 mr-2"></i>
                    <span class="font-medium">Generate Batch Tracking Sheets</span>
                `;
            }
            
            // Re-initialize lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Toggle actions menu
        function toggleActionsMenu() {
            actionsMenu.classList.toggle('hidden');
        }

        // Generate tracking sheet with official template
        function generateTrackingSheet() {
            if (selectedFiles.size === 0) {
                alert('Please select at least one file to generate tracking sheet.');
                return;
            }

            const selectedFileData = Array.from(selectedFiles).map(fileId => {
                return indexedFiles.find(f => f.id === fileId);
            }).filter(Boolean);

            // Build printable tracking sheet content directly
            let trackingSheetContent = `
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Kano Land State Registry - File Tracking Sheet</title>
                    <script src="https://cdn.tailwindcss.com"></script>
                    <style>
                        @media print {
                            @page {
                                size: landscape;
                                margin: 0.5in;
                            }
                            body {
                                print-color-adjust: exact;
                            }
                        }
                    </style>
                </head>
                <body class="bg-white font-sans text-xs">
            `;

            selectedFileData.forEach((file, index) => {
                trackingSheetContent += `
                    <div class="max-w-full mx-auto bg-white border border-black" ${index > 0 ? 'style="page-break-before: always;"' : ''}>
                        <!-- Header -->
                        <div class="p-2 border-b border-black">
                            <!-- Two logos side by side -->
                            <div class="flex justify-center items-center gap-8 mb-3">
                                <div class="w-15 h-15 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-xs">LOGO</span>
                                </div>
                                <div class="w-15 h-15 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-xs">LOGO</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h1 class="text-sm font-bold">KANO LAND STATE REGISTRY</h1>
                                    <h2 class="text-xs">FILE TRACKING SHEET</h2>
                                </div>
                                <div class="text-right text-xs">
                                    <p class="font-bold">Tracking ID: TRK-${new Date().getFullYear()}-${String(index + 1).padStart(3, '0')}</p>
                                    <p>Generated: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-3">
                            <!-- File Details -->
                            <div class="grid grid-cols-12 gap-4 mb-4">
                                <div class="col-span-8">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-10 bg-gray-200 border border-gray-400 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-bold mb-1">File Details</h3>
                                            <p class="text-xs font-semibold mb-2">${file.file_title || 'Untitled'}</p>
                                            
                                            <!-- Status buttons -->
                                            <div class="flex gap-2 mb-3">
                                                <span class="bg-blue-600 text-white px-2 py-1 text-xs rounded">Status: Ready for Tracking</span>
                                                <span class="bg-gray-500 text-white px-2 py-1 text-xs rounded">Priority: Normal</span>
                                            </div>

                                            <h4 class="text-xs font-bold mb-1">File Information</h4>
                                            <div class="grid grid-cols-2 gap-x-8 text-xs">
                                                <div>
                                                    <p><span class="font-semibold">File Number:</span></p>
                                                    <p><span class="font-semibold">Land Use:</span></p>
                                                    <p><span class="font-semibold">District:</span></p>
                                                    <p><span class="font-semibold">Plot Number:</span></p>
                                                    <p><span class="font-semibold">Indexed Date:</span></p>
                                                </div>
                                                <div>
                                                    <p>${file.file_number || 'N/A'}</p>
                                                    <p>${file.land_use_type || 'N/A'}</p>
                                                    <p>${file.district || 'N/A'}</p>
                                                    <p>${file.plot_number || 'N/A'}</p>
                                                    <p>${formatDate(file.created_at)}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- QR Code section -->
                                <div class="col-span-4">
                                    <h3 class="text-xs font-bold mb-1">QR Code</h3>
                                    <div class="border border-gray-400 p-2 text-center">
                                        <div class="w-20 h-20 mx-auto mb-2 border bg-gray-100 flex items-center justify-center">
                                            <span class="text-xs">QR</span>
                                        </div>
                                        <p class="text-xs">Contains file details</p>
                                        <p class="text-xs font-semibold">${file.file_number || 'N/A'}</p>
                                        <p class="text-xs">📱 RFID: Pending</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Location -->
                            <div class="mb-4">
                                <h3 class="text-xs font-bold mb-1">Current Location</h3>
                                <div class="grid grid-cols-4 gap-4 text-xs">
                                    <div>
                                        <p class="font-semibold">Pending Assignment</p>
                                        <p>Last updated: ${new Date().toLocaleDateString()}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Unassigned</p>
                                        <p>Current handler</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</p>
                                        <p>Sheet generated</p>
                                    </div>
                                    <div></div>
                                </div>
                            </div>

                            <!-- Movement History -->
                            <div class="mb-4">
                                <h3 class="text-xs font-bold mb-2">Movement History</h3>
                                <table class="w-full border-collapse border border-black text-xs">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-black p-1 text-left font-bold">Date & Time</th>
                                            <th class="border border-black p-1 text-left font-bold">Location</th>
                                            <th class="border border-black p-1 text-left font-bold">Handler</th>
                                            <th class="border border-black p-1 text-left font-bold">Action</th>
                                            <th class="border border-black p-1 text-left font-bold">Method</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-black p-1">${formatDate(file.created_at)}</td>
                                            <td class="border border-black p-1">File Indexing</td>
                                            <td class="border border-black p-1">System</td>
                                            <td class="border border-black p-1">File indexed and tracking sheet generated</td>
                                            <td class="border border-black p-1">System</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-black p-1">&nbsp;</td>
                                            <td class="border border-black p-1">&nbsp;</td>
                                            <td class="border border-black p-1">&nbsp;</td>
                                            <td class="border border-black p-1">&nbsp;</td>
                                            <td class="border border-black p-1">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-black p-1">&nbsp;</td>
                                            <td class="border border-black p-1">&nbsp;</td>
                                            <td class="border border-black p-1">&nbsp;</td>
                                            <td class="border border-black p-1">&nbsp;</td>
                                            <td class="border border-black p-1">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Signature section -->
                            <div class="grid grid-cols-2 gap-8 mb-4">
                                <div>
                                    <h3 class="text-xs font-bold mb-2">Signature</h3>
                                    <div class="h-16 border-b border-black mb-1"></div>
                                    <p class="text-xs">Authorized Signature</p>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold mb-2">Notes</h3>
                                    <div class="h-16 mb-1"></div>
                                    <p class="text-xs text-center">File ready for physical tracking</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <div class="border-b border-black mb-1"></div>
                                    <p class="text-xs">Date:</p>
                                </div>
                                <div></div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-black p-2 text-xs">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-bold">KANO STATE LAND REGISTRY</p>
                                    <p>File Tracking System</p>
                                </div>
                                <div class="text-right">
                                    <p>This tracking sheet should accompany the file at all times.</p>
                                    <p>For inquiries, contact File Management Office at ext.2145.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            trackingSheetContent += `
                </body>
                </html>
            `;

            // Open in new window for PDF generation
            const printWindow = window.open('', '_blank');
            printWindow.document.write(trackingSheetContent);
            printWindow.document.close();
            printWindow.focus();
            
            // Wait for content to load then trigger print dialog for PDF save
            setTimeout(() => {
                printWindow.print();
            }, 500);

            actionsMenu.classList.add('hidden');
        }

        // Print tracking sheet function
        function printTrackingSheet() {
            if (selectedFiles.size === 0) {
                alert('Please select at least one file to print tracking sheets.');
                return;
            }

            // Generate the tracking sheet and trigger print
            generateTrackingSheet();
            actionsMenu.classList.add('hidden');
        }

        // Initialize the page when loaded
        document.addEventListener('DOMContentLoaded', function() {
            init();
            
            // Add event listeners for new functionality
            selectAllCheckbox.addEventListener('change', handleSelectAll);
            actionsMenuBtn.addEventListener('click', toggleActionsMenu);
            generateTrackingSheetBtn.addEventListener('click', generateTrackingSheet);
            printTrackingSheetBtn.addEventListener('click', printTrackingSheet);
            
            // Close actions menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!actionsMenuBtn.contains(event.target) && !actionsMenu.contains(event.target)) {
                    actionsMenu.classList.add('hidden');
                }
            });
        });
    </script>

<script>
// Fix for Generate Batch Tracking Sheets button - ensure it's always enabled when files are selected
document.addEventListener('DOMContentLoaded', function() {
    // Wait for the page to fully load
    setTimeout(function() {
        // Add a mutation observer to watch for button text changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'characterData') {
                    const newFileIndexBtn = document.getElementById('new-file-index-btn');
                    if (newFileIndexBtn && (
                        newFileIndexBtn.textContent.includes('Generate Tracking Sheet') || 
                        newFileIndexBtn.textContent.includes('Generate Batch Tracking Sheets')
                    )) {
                        newFileIndexBtn.disabled = false;
                        newFileIndexBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        console.log('Button enabled for tracking sheet generation');
                    }
                }
            });
        });
        
        // Start observing the button for changes
        const newFileIndexBtn = document.getElementById('new-file-index-btn');
        if (newFileIndexBtn) {
            observer.observe(newFileIndexBtn, {
                childList: true,
                subtree: true,
                characterData: true
            });
            
            // Also enable it immediately if it's already showing tracking sheet text
            if (newFileIndexBtn.textContent.includes('Generate Tracking Sheet') || 
                newFileIndexBtn.textContent.includes('Generate Batch Tracking Sheets')) {
                newFileIndexBtn.disabled = false;
                newFileIndexBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
        
        // Ensure action menu buttons work properly
        const generateBtn = document.getElementById('generate-tracking-sheet');
        const printBtn = document.getElementById('print-tracking-sheet');
        
        if (generateBtn) {
            generateBtn.addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('#indexed-tab .file-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one file to generate tracking sheets.');
                    return;
                }
                
                // Call the existing function if available
                if (typeof generateTrackingSheet === 'function') {
                    generateTrackingSheet();
                } else {
                    console.log('generateTrackingSheet function not found');
                    alert('Generating tracking sheets for ' + selectedCheckboxes.length + ' selected files...');
                }
            });
        }
        
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('#indexed-tab .file-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one file to print tracking sheets.');
                    return;
                }
                
                // Call the existing function if available
                if (typeof generateTrackingSheet === 'function') {
                    generateTrackingSheet();
                } else {
                    console.log('generateTrackingSheet function not found');
                    alert('Generating tracking sheets for ' + selectedCheckboxes.length + ' selected files...');
                }
            });
        }
    }, 500);
});
</script>
