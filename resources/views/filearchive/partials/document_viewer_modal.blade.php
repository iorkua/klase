<!-- Ensure this starts completely hidden -->
<div id="document-viewer-dialog" class="dialog-backdrop" style="display: none;" aria-hidden="true" tabindex="-1">
    <div class="dialog-content animate-fade-in max-w-[1200px] h-[90vh] flex flex-col">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold" id="viewer-title">Document Viewer</h2>
            <button class="btn btn-ghost btn-sm" id="close-viewer" type="button">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>

        <div class="flex-1 flex flex-col md:flex-row overflow-hidden">
            <!-- Document pages sidebar -->
            <div class="w-full md:w-64 border-r bg-gray-50 overflow-y-auto">
                <div class="p-4">
                    <h3 class="text-sm font-medium mb-2">Pages</h3>
                    <div class="space-y-2" id="document-pages-list">
                        <!-- Static page items for demonstration -->
                        <div class="p-2 border rounded-md cursor-pointer hover:bg-gray-50 bg-blue-50 border-blue-300 page-item" data-page="1" data-image="storage/upload/dummy/1.jpg">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('storage/upload/dummy/1.jpg') }}" alt="Page 1" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium truncate">Cover Page</div>
                                    <div class="text-xs text-gray-500 truncate">File Cover</div>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 border rounded-md cursor-pointer hover:bg-gray-50 page-item" data-page="2" data-image="storage/upload/dummy/2.jpg">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('storage/upload/dummy/2.jpg') }}" alt="Page 2" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium truncate">Application Form</div>
                                    <div class="text-xs text-gray-500 truncate">Application</div>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 border rounded-md cursor-pointer hover:bg-gray-50 page-item" data-page="3" data-image="storage/upload/dummy/3.jpg">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('storage/upload/dummy/3.jpg') }}" alt="Page 3" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium truncate">Personal Information</div>
                                    <div class="text-xs text-gray-500 truncate">Application</div>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 border rounded-md cursor-pointer hover:bg-gray-50 page-item" data-page="4" data-image="storage/upload/dummy/5.jpg">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('storage/upload/dummy/5.jpg') }}" alt="Page 4" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium truncate">Property Details</div>
                                    <div class="text-xs text-gray-500 truncate">Land Title</div>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 border rounded-md cursor-pointer hover:bg-gray-50 page-item" data-page="5" data-image="storage/upload/dummy/7.jpg">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('storage/upload/dummy/7.jpg') }}" alt="Page 5" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium truncate">Survey Plan</div>
                                    <div class="text-xs text-gray-500 truncate">Survey</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document content area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <div class="p-2 border-b flex items-center justify-between bg-gray-50">
                    <div class="flex items-center gap-2">
                        <button class="btn btn-ghost btn-sm" id="prev-page">
                            <i data-lucide="chevron-left" class="h-4 w-4"></i>
                        </button>
                        <span class="text-sm" id="page-indicator">Page 1 of 5</span>
                        <button class="btn btn-ghost btn-sm" id="next-page">
                            <i data-lucide="chevron-right" class="h-4 w-4"></i>
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="btn btn-ghost btn-sm" id="zoom-out">
                            <i data-lucide="zoom-out" class="h-4 w-4"></i>
                        </button>
                        <span class="text-sm" id="zoom-level">100%</span>
                        <button class="btn btn-ghost btn-sm" id="zoom-in">
                            <i data-lucide="zoom-in" class="h-4 w-4"></i>
                        </button>
                        <button class="btn btn-ghost btn-sm" id="rotate">
                            <i data-lucide="rotate-cw" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-auto flex items-center justify-center bg-gray-100 p-4">
                    <div id="current-page-content" class="bg-white shadow-md rounded-md max-w-[800px] w-full mx-auto transition-transform" style="transform: scale(1) rotate(0deg);">
                        <!-- Display actual image instead of text content -->
                        <div class="h-full">
                            <img src="{{ asset('storage/upload/dummy/1.jpg') }}" alt="Document page" id="document-image" class="w-full object-contain">
                        </div>
                    </div>
                </div>

                <div class="p-2 border-t bg-gray-50">
                    <div class="text-sm text-gray-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-medium">File Cover</span>
                                <span> - Cover Page</span>
                            </div>
                            <div class="font-mono text-xs">KNGP 00338-1-1-01</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Immediately executing function to ensure the modal is hidden
(function() {
    // Make absolutely sure the document viewer is hidden on page load
    const hideViewer = function() {
        const viewer = document.getElementById('document-viewer-dialog');
        if (viewer) {
            viewer.classList.add('hidden');
            viewer.style.display = 'none';
            viewer.setAttribute('aria-hidden', 'true');
        }
    };
    
    // Hide immediately
    hideViewer();
    
    // Also hide after a tiny delay to override any other scripts
    setTimeout(hideViewer, 50);
    
    // And again after the page has fully loaded
    window.addEventListener('load', hideViewer);
    
    // Define a global function to close the viewer
    window.closeDocumentViewer = function() {
        const viewer = document.getElementById('document-viewer-dialog');
        if (viewer) {
            viewer.classList.add('hidden');
            viewer.style.display = 'none';
            viewer.setAttribute('aria-hidden', 'true');
        }
    };
    
    // Add a click handler to the backdrop to close when clicking outside
    const viewer = document.getElementById('document-viewer-dialog');
    if (viewer) {
        viewer.addEventListener('click', function(e) {
            if (e.target === viewer) {
                window.closeDocumentViewer();
            }
        });
    }
})();

// Make sure clicking outside the dialog and the close button works
(function() {
    function setupListeners() {
        const dialog = document.getElementById('document-viewer-dialog');
        const closeBtn = document.getElementById('close-viewer');
        
        // Ensure click outside closes modal
        if (dialog) {
            dialog.addEventListener('click', function(e) {
                if (e.target === this && window.closeDocumentViewer) {
                    window.closeDocumentViewer();
                }
            });
        }
        
        // Ensure close button works
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                if (window.closeDocumentViewer) {
                    window.closeDocumentViewer();
                }
            });
        }
    }
    
    // Run on load and with a small delay for good measure
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupListeners);
    } else {
        setupListeners();
        setTimeout(setupListeners, 100);
    }
})();

// Enhanced document viewer functionality
$(document).ready(function() {
    // Page navigation with actual images
    $('.page-item').on('click', function() {
        // Update active state
        $('.page-item').removeClass('bg-blue-50 border-blue-300').addClass('bg-white');
        $(this).addClass('bg-blue-50 border-blue-300').removeClass('bg-white');
        
        // Get page info
        const pageNum = $(this).data('page');
        const imagePath = $(this).data('image');
        
        // Update page indicator
        $('#page-indicator').text(`Page ${pageNum} of 5`);
        
        // Update image
        $('#document-image').attr('src', imagePath);
    });
    
    // View document button handler
    $('#view-document').on('click', function() {
        // Get the current file preview image and set it as the first document page
        const currentPreviewSrc = $('#preview-image').attr('src');
        $('#document-image').attr('src', currentPreviewSrc);
        
        // Make sure the first page is selected
        $('.page-item').removeClass('bg-blue-50 border-blue-300').addClass('bg-white');
        $('.page-item[data-page="1"]').addClass('bg-blue-50 border-blue-300').removeClass('bg-white');
        
        // Update page indicator
        $('#page-indicator').text('Page 1 of 5');
    });
});
</script>
