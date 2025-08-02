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
                        <button class="Button Button-variant-outline gap-2 bg-transparent" id="download-report">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Download Report
                        </button>
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
                                    File Title
                                    <svg class="ml-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m21 16-4 4-4-4"></path>
                                        <path d="M17 20V4"></path>
                                        <path d="m3 8 4-4 4 4"></path>
                                        <path d="M7 4v16"></path>
                                    </svg>
                                </th>
                                <th class="TableHead cursor-pointer min-w-[120px]" data-sort="created_at">
                                    Indexed Date
                                    <svg class="ml-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m21 16-4 4-4-4"></path>
                                        <path d="M17 20V4"></path>
                                        <path d="m3 8 4-4 4 4"></path>
                                        <path d="M7 4v16"></path>
                                    </svg>
                                </th>
                                <th class="TableHead min-w-[120px]">
                                    Status
                                </th>
                                <th class="TableHead cursor-pointer min-w-[120px]" data-sort="land_use_type">
                                    Land Use
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
                                    Plot Number
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
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
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
        // Dynamic data from database
        const indexedFiles = @json($recentIndexes ?? []);

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

        // Current sort state
        let currentSort = {
            field: 'file_number',
            direction: 'asc'
        };

        // Filtered and sorted files
        let filteredAndSortedIndexedFiles = [...indexedFiles];

        // Initialize the page
        function init() {
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
            // Always use green badge for status
            const statusText = file.pagetypings_count > 0
                ? 'Typed'
                : file.scannings_count > 0
                    ? 'Scanned'
                    : 'Indexed';
            return `<span class="Badge Badge-variant-green">${statusText}</span>`;
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
                
                row.innerHTML = `
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
                            ${file.scannings_count === 0 ? `
                                <a href="{{ route('scanning.index') }}?file_indexing_id=${file.id}" class="Button Button-variant-default Button-size-sm">
                                    Scan
                                </a>
                            ` : file.pagetypings_count === 0 ? `
                                <a href="{{ route('pagetyping.index') }}?file_indexing_id=${file.id}" class="Button Button-variant-default Button-size-sm">
                                    Type
                                </a>
                            ` : `
     `}
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

        // Initialize the page when loaded
        document.addEventListener('DOMContentLoaded', init);
    </script>