<!-- Dynamic Page Typing JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // State variables
    let selectedFileIndexing = @json($selectedFileIndexing ?? null);
    let currentDocuments = [];
    let currentDocumentIndex = 0;
    let currentPageNumber = 1;
    let savedPages = [];
    let totalPages = 0;
    
    // DOM Elements
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    const pageTypingModal = document.getElementById('page-typing-modal');
    const closeTypingModal = document.getElementById('close-typing-modal');
    const typingModalTitle = document.getElementById('typing-modal-title');
    const documentViewer = document.getElementById('document-viewer');
    const documentCounter = document.getElementById('document-counter');
    const prevDocumentBtn = document.getElementById('prev-document');
    const nextDocumentBtn = document.getElementById('next-document');
    
    // Form elements
    const pageTypingForm = document.getElementById('page-typing-form');
    const pageNumberInput = document.getElementById('page-number');
    const pageTypeSelect = document.getElementById('page-type');
    const pageSubtypeInput = document.getElementById('page-subtype');
    const serialNumberInput = document.getElementById('serial-number');
    const pageCodeInput = document.getElementById('page-code');
    const savePageBtn = document.getElementById('save-page');
    const saveAndNextBtn = document.getElementById('save-and-next');
    const completeTypingBtn = document.getElementById('complete-typing');
    const typingProgress = document.getElementById('typing-progress');
    const typingProgressBar = document.getElementById('typing-progress-bar');
    
    // Search elements
    const searchPendingFiles = document.getElementById('search-pending-files');
    const searchProgressFiles = document.getElementById('search-progress-files');
    const searchCompletedFiles = document.getElementById('search-completed-files');
    
    // Tab switching functionality
    function switchTab(tabName) {
        tabs.forEach(tab => {
            if (tab.getAttribute('data-tab') === tabName) {
                tab.classList.add('active');
                tab.setAttribute('aria-selected', 'true');
            } else {
                tab.classList.remove('active');
                tab.setAttribute('aria-selected', 'false');
            }
        });

        tabContents.forEach(content => {
            if (content.getAttribute('data-tab-content') === tabName) {
                content.classList.remove('hidden');
                content.classList.add('active');
                content.setAttribute('aria-hidden', 'false');
            } else {
                content.classList.add('hidden');
                content.classList.remove('active');
                content.setAttribute('aria-hidden', 'true');
            }
        });
    }
    
    // Start page typing for a file
    function startPageTyping(fileIndexingId) {
        loadFileForTyping(fileIndexingId);
    }
    
    // Continue page typing for a file
    function continuePageTyping(fileIndexingId) {
        loadFileForTyping(fileIndexingId);
    }
    
    // View completed page typing
    function viewPageTyping(fileIndexingId) {
        // Redirect to view page or show read-only modal
        window.open(`{{ route('fileindexing.show', '') }}/${fileIndexingId}`, '_blank');
    }
    
    // Load file for typing
    function loadFileForTyping(fileIndexingId) {
        // Show loading state
        showPageTypingModal();
        typingModalTitle.textContent = 'Loading...';
        documentViewer.innerHTML = '<div class="flex items-center justify-center h-full"><i data-lucide="loader" class="h-8 w-8 animate-spin text-gray-400"></i></div>';
        lucide.createIcons();
        
        // Fetch file data and documents
        fetch(`{{ route("scanning.list") }}?file_indexing_id=${fileIndexingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentDocuments = data.scanned_files || [];
                selectedFileIndexing = { id: fileIndexingId };
                
                if (currentDocuments.length === 0) {
                    documentViewer.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500"><p>No scanned documents found</p></div>';
                    return;
                }
                
                // Load existing page typings
                loadExistingPageTypings(fileIndexingId);
                
                // Initialize document viewer
                currentDocumentIndex = 0;
                loadDocument(0);
                updateDocumentCounter();
                updateTypingProgress();
                
                // Set title safely
                const fileNumber = currentDocuments[0]?.file_indexing?.file_number || 'Unknown File';
                typingModalTitle.textContent = `Page Typing - ${fileNumber}`;
            } else {
                throw new Error(data.message || 'Failed to load documents');
            }
        })
        .catch(error => {
            console.error('Error loading file for typing:', error);
            alert('Error loading file: ' + error.message);
            hidePageTypingModal();
        });
    }
    
    // Load existing page typings
    function loadExistingPageTypings(fileIndexingId) {
        fetch(`{{ route("pagetyping.list") }}?file_indexing_id=${fileIndexingId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.page_typings) {
                savedPages = data.page_typings || [];
                updateTypingProgress();
                
                // If there are existing typings, load the last one
                if (savedPages.length > 0) {
                    const lastPage = savedPages[savedPages.length - 1];
                    currentPageNumber = lastPage.page_number + 1;
                    pageNumberInput.value = currentPageNumber;
                    serialNumberInput.value = savedPages.length + 1;
                }
            } else {
                savedPages = [];
                updateTypingProgress();
            }
        })
        .catch(error => {
            console.error('Error loading existing page typings:', error);
            savedPages = [];
            updateTypingProgress();
        });
    }
    
    // Load document in viewer
    function loadDocument(index) {
        if (index < 0 || index >= currentDocuments.length) return;
        
        currentDocumentIndex = index;
        const document = currentDocuments[index];
        
        // Check if document and required properties exist
        if (!document || !document.file_url) {
            documentViewer.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500"><p>Document not available</p></div>';
            return;
        }
        
        // Display document
        const fileUrl = document.file_url;
        const filename = document.filename || document.file_name || 'Unknown file';
        const fileExtension = filename && typeof filename === 'string' ? filename.split('.').pop().toLowerCase() : '';
        
        if (['jpg', 'jpeg', 'png', 'gif', 'tiff'].includes(fileExtension)) {
            documentViewer.innerHTML = `
                <img src="${fileUrl}" alt="Document" class="max-w-full max-h-full object-contain">
            `;
        } else if (fileExtension === 'pdf') {
            documentViewer.innerHTML = `
                <iframe src="${fileUrl}" class="w-full h-full border-0"></iframe>
            `;
        } else {
            documentViewer.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                    <i data-lucide="file-text" class="h-12 w-12 mb-4"></i>
                    <p class="mb-2">${filename}</p>
                    <a href="${fileUrl}" target="_blank" class="btn btn-outline btn-sm">
                        <i data-lucide="external-link" class="h-4 w-4 mr-2"></i>
                        Open Document
                    </a>
                </div>
            `;
        }
        
        lucide.createIcons();
        updateDocumentCounter();
    }
    
    // Update document counter
    function updateDocumentCounter() {
        if (documentCounter) {
            documentCounter.textContent = `${currentDocumentIndex + 1} / ${currentDocuments.length}`;
        }
        
        // Update navigation buttons
        if (prevDocumentBtn) {
            prevDocumentBtn.disabled = currentDocumentIndex === 0;
        }
        if (nextDocumentBtn) {
            nextDocumentBtn.disabled = currentDocumentIndex === currentDocuments.length - 1;
        }
    }
    
    // Update typing progress
    function updateTypingProgress() {
        // Calculate total pages from all documents
        totalPages = currentDocuments.reduce((total, doc) => {
            // Estimate pages based on file size or use 1 as default
            return total + 1; // Simplified - each document = 1 page
        }, 0);
        
        const completedPages = savedPages.length;
        const progressPercentage = totalPages > 0 ? (completedPages / totalPages) * 100 : 0;
        
        if (typingProgress) {
            typingProgress.textContent = `${completedPages} / ${totalPages} pages`;
        }
        if (typingProgressBar) {
            typingProgressBar.style.width = progressPercentage + '%';
        }
        if (completeTypingBtn) {
            completeTypingBtn.disabled = completedPages < totalPages;
        }
    }
    
    // Save page typing
    function savePage(moveToNext = false) {
        if (!validatePageForm()) return;
        
        const currentDocument = currentDocuments[currentDocumentIndex];
        const pageData = {
            file_indexing_id: selectedFileIndexing.id,
            scanning_id: currentDocument.id,
            page_number: parseInt(pageNumberInput.value),
            page_type: pageTypeSelect.value,
            page_subtype: pageSubtypeInput.value,
            serial_number: parseInt(serialNumberInput.value),
            page_code: pageCodeInput.value,
            file_path: currentDocument.document_path
        };
        
        // Show loading state
        savePageBtn.disabled = true;
        saveAndNextBtn.disabled = true;
        savePageBtn.textContent = 'Saving...';
        
        fetch('{{ route("pagetyping.save-single") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify(pageData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add to saved pages
                savedPages.push({
                    ...pageData,
                    id: data.page_typing_id
                });
                
                updateTypingProgress();
                
                if (moveToNext) {
                    moveToNextPage();
                } else {
                    // Reset form for next page
                    resetFormForNextPage();
                }
                
                // Show success message briefly
                const originalText = savePageBtn.textContent;
                savePageBtn.textContent = 'Saved!';
                setTimeout(() => {
                    savePageBtn.textContent = originalText;
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to save page');
            }
        })
        .catch(error => {
            console.error('Error saving page:', error);
            alert('Error saving page: ' + error.message);
        })
        .finally(() => {
            savePageBtn.disabled = false;
            saveAndNextBtn.disabled = false;
            savePageBtn.textContent = 'Save Page';
        });
    }
    
    // Validate page form
    function validatePageForm() {
        if (!pageNumberInput.value || pageNumberInput.value < 1) {
            alert('Please enter a valid page number');
            pageNumberInput.focus();
            return false;
        }
        
        if (!pageTypeSelect.value) {
            alert('Please select a page type');
            pageTypeSelect.focus();
            return false;
        }
        
        if (!serialNumberInput.value || serialNumberInput.value < 1) {
            alert('Please enter a valid serial number');
            serialNumberInput.focus();
            return false;
        }
        
        return true;
    }
    
    // Reset form for next page
    function resetFormForNextPage() {
        currentPageNumber++;
        pageNumberInput.value = currentPageNumber;
        serialNumberInput.value = savedPages.length + 1;
        pageTypeSelect.value = '';
        pageSubtypeInput.value = '';
        pageCodeInput.value = '';
    }
    
    // Move to next page/document
    function moveToNextPage() {
        if (currentDocumentIndex < currentDocuments.length - 1) {
            loadDocument(currentDocumentIndex + 1);
        }
        resetFormForNextPage();
    }
    
    // Complete page typing
    function completePageTyping() {
        if (savedPages.length === 0) {
            alert('Please save at least one page before completing');
            return;
        }
        
        if (!confirm('Are you sure you want to complete page typing for this file?')) {
            return;
        }
        
        completeTypingBtn.disabled = true;
        completeTypingBtn.textContent = 'Completing...';
        
        fetch(`{{ route("pagetyping.store") }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                file_indexing_id: selectedFileIndexing.id,
                page_types: savedPages.map(page => ({
                    scanning_id: page.scanning_id,
                    page_number: page.page_number,
                    page_type: page.page_type,
                    page_subtype: page.page_subtype,
                    serial_number: page.serial_number,
                    page_code: page.page_code,
                    file_path: page.file_path
                }))
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                hidePageTypingModal();
                
                // Refresh the page to show updated data
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                throw new Error(data.message || 'Failed to complete page typing');
            }
        })
        .catch(error => {
            console.error('Error completing page typing:', error);
            alert('Error completing page typing: ' + error.message);
        })
        .finally(() => {
            completeTypingBtn.disabled = false;
            completeTypingBtn.textContent = 'Complete Page Typing';
        });
    }
    
    // Show page typing modal
    function showPageTypingModal() {
        if (pageTypingModal) {
            pageTypingModal.classList.remove('hidden');
            pageTypingModal.setAttribute('aria-hidden', 'false');
        }
    }
    
    // Hide page typing modal
    function hidePageTypingModal() {
        if (pageTypingModal) {
            pageTypingModal.classList.add('hidden');
            pageTypingModal.setAttribute('aria-hidden', 'true');
        }
        
        // Reset state
        currentDocuments = [];
        currentDocumentIndex = 0;
        currentPageNumber = 1;
        savedPages = [];
        selectedFileIndexing = null;
    }
    
    // Toggle page details for completed files
    function togglePageDetails(fileIndexingId) {
        const existingDetails = document.getElementById(`page-details-${fileIndexingId}`);
        
        if (existingDetails) {
            // Toggle visibility and update button
            const button = document.querySelector(`[onclick="togglePageDetails(${fileIndexingId})"]`);
            if (existingDetails.style.display === 'none') {
                existingDetails.style.display = 'table-row';
                button.innerHTML = '<i data-lucide="eye-off" class="h-4 w-4 mr-1"></i>Hide Pages';
            } else {
                existingDetails.style.display = 'none';
                button.innerHTML = '<i data-lucide="eye" class="h-4 w-4 mr-1"></i>View Pages';
            }
            lucide.createIcons();
            return;
        }
        
        // Find the table row element
        const tableRow = document.querySelector(`[onclick="togglePageDetails(${fileIndexingId})"]`).closest('tr');
        
        // Load page details and file information
        Promise.all([
            fetch(`{{ route("pagetyping.list") }}?file_indexing_id=${fileIndexingId}`),
            fetch(`{{ route("scanning.list") }}?file_indexing_id=${fileIndexingId}`)
        ])
        .then(responses => Promise.all(responses.map(r => {
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })))
        .then(([pageData, scanData]) => {
            if (pageData.success && pageData.page_typings && scanData.success) {
                const pages = pageData.page_typings;
                const scannedFiles = scanData.scanned_files || [];
                
                // Get file information
                const fileInfo = scannedFiles.length > 0 ? scannedFiles[0].file_indexing : null;
                const fileName = fileInfo?.file_number || 'Unknown File';
                const fileTitle = fileInfo?.file_title || 'Unknown Title';
                
                // Create horizontal card layout for documents
                const documentCards = scannedFiles.map((doc, index) => {
                    const docPages = pages.filter(page => page.file_path === doc.document_path);
                    const pageType = docPages.length > 0 ? docPages[0].page_type : 'Unknown';
                    const pageCode = docPages.length > 0 ? docPages[0].page_code : '';
                    const typedBy = docPages.length > 0 ? docPages[0].typed_by : 'Unknown';
                    
                    // Generate document preview
                    const fileExtension = doc.filename ? doc.filename.split('.').pop().toLowerCase() : '';
                    let documentPreview = '';
                    
                    if (['jpg', 'jpeg', 'png', 'gif', 'tiff'].includes(fileExtension)) {
                        documentPreview = `<img src="${doc.file_url}" alt="Document" class="w-full h-32 object-cover rounded" onerror="this.parentElement.innerHTML='<div class=\\'w-full h-32 bg-gray-100 rounded flex items-center justify-center\\'><i data-lucide=\\'image-off\\' class=\\'h-12 w-12 text-gray-400\\'></i></div>'">`;
                    } else if (fileExtension === 'pdf') {
                        documentPreview = `
                            <div class="w-full h-32 bg-red-100 rounded flex items-center justify-center">
                                <i data-lucide="file-text" class="h-12 w-12 text-red-500"></i>
                            </div>
                        `;
                    } else {
                        documentPreview = `
                            <div class="w-full h-32 bg-gray-100 rounded flex items-center justify-center">
                                <i data-lucide="file" class="h-12 w-12 text-gray-400"></i>
                            </div>
                        `;
                    }
                    
                    return `
                        <div class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                            <!-- Document Preview -->
                            <div class="mb-3">
                                ${documentPreview}
                            </div>
                            
                            <!-- Document Code Badge -->
                            ${pageCode ? `<div class="mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    ${pageCode}
                                </span>
                            </div>` : ''}
                            
                            <!-- Document Type -->
                            <div class="mb-2">
                                <span class="text-xs font-medium text-gray-600">${pageType}</span>
                            </div>
                            
                            <!-- Document Title -->
                            <h6 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2">
                                ${doc.filename || 'Untitled Document'}
                            </h6>
                            
                            <!-- Typed By Info -->
                            <div class="text-xs text-gray-500">
                                <span class="font-medium">Typed by:</span> ${typedBy}
                            </div>
                            
                            <!-- Page Count -->
                            <div class="text-xs text-gray-500 mt-1">
                                ${docPages.length} page${docPages.length !== 1 ? 's' : ''}
                            </div>
                        </div>
                    `;
                }).join('');
                
                // Create the expanded row content
                const detailsHtml = `
                    <tr id="page-details-${fileIndexingId}">
                        <td colspan="7" class="px-6 py-4 bg-gray-50">
                            <div class="space-y-4">
                                <!-- File Information Header -->
                                <div class="border-b pb-3">
                                    <h4 class="font-semibold text-lg text-gray-900 mb-2">Document Pages</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-600">File Name:</span>
                                            <span class="text-gray-900 ml-1">${fileName}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-600">File Title:</span>
                                            <span class="text-gray-900 ml-1">${fileTitle}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-600">Total Pages:</span>
                                            <span class="text-gray-900 ml-1">${pages.length}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Horizontal Document Cards -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                    ${documentCards}
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                
                // Insert the details row after the current row
                tableRow.insertAdjacentHTML('afterend', detailsHtml);
                
                // Update button text
                const button = tableRow.querySelector(`[onclick="togglePageDetails(${fileIndexingId})"]`);
                button.innerHTML = '<i data-lucide="eye-off" class="h-4 w-4 mr-1"></i>Hide Pages';
                lucide.createIcons();
            } else {
                alert('No page typing data found for this file');
            }
        })
        .catch(error => {
            console.error('Error loading page details:', error);
            alert('Error loading page details: ' + error.message);
        });
    }
    
    // Event listeners
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
    
    if (closeTypingModal) {
        closeTypingModal.addEventListener('click', hidePageTypingModal);
    }
    
    if (prevDocumentBtn) {
        prevDocumentBtn.addEventListener('click', () => {
            if (currentDocumentIndex > 0) {
                loadDocument(currentDocumentIndex - 1);
            }
        });
    }
    
    if (nextDocumentBtn) {
        nextDocumentBtn.addEventListener('click', () => {
            if (currentDocumentIndex < currentDocuments.length - 1) {
                loadDocument(currentDocumentIndex + 1);
            }
        });
    }
    
    if (savePageBtn) {
        savePageBtn.addEventListener('click', () => savePage(false));
    }
    
    if (saveAndNextBtn) {
        saveAndNextBtn.addEventListener('click', () => savePage(true));
    }
    
    if (completeTypingBtn) {
        completeTypingBtn.addEventListener('click', completePageTyping);
    }
    
    // Search functionality
    if (searchPendingFiles) {
        let searchTimeout;
        searchPendingFiles.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterFilesList('pending-files-list', this.value);
            }, 300);
        });
    }
    
    if (searchProgressFiles) {
        let searchTimeout;
        searchProgressFiles.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterFilesList('in-progress-files-list', this.value);
            }, 300);
        });
    }
    
    if (searchCompletedFiles) {
        let searchTimeout;
        searchCompletedFiles.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterCompletedFiles(this.value);
            }, 300);
        });
    }
    
    // Filter files list
    function filterFilesList(listId, searchTerm) {
        const list = document.getElementById(listId);
        if (!list) return;
        
        const items = list.querySelectorAll('.border.rounded-lg');
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm.toLowerCase())) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // Filter completed files table
    function filterCompletedFiles(searchTerm) {
        const tbody = document.getElementById('completed-files-list');
        if (!tbody) return;
        
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            // Skip the expanded detail rows
            if (row.id && row.id.startsWith('page-details-')) {
                return;
            }
            
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm.toLowerCase())) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
                // Also hide any expanded details for this row
                const fileId = row.querySelector('[onclick*="togglePageDetails"]')?.getAttribute('onclick')?.match(/\d+/)?.[0];
                if (fileId) {
                    const detailRow = document.getElementById(`page-details-${fileId}`);
                    if (detailRow) {
                        detailRow.style.display = 'none';
                    }
                }
            }
        });
    }
    
    // Auto-start typing if file is selected
    if (selectedFileIndexing) {
        startPageTyping(selectedFileIndexing.id);
    }
    
    // Make functions globally available
    window.startPageTyping = startPageTyping;
    window.continuePageTyping = continuePageTyping;
    window.viewPageTyping = viewPageTyping;
    window.togglePageDetails = togglePageDetails;
    
    console.log('Dynamic Page Typing module initialized');
});
</script>