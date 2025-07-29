<div id="file-details-dialog" class="dialog-backdrop" style="display: none;" aria-hidden="true" tabindex="-1">
    <div class="dialog-content animate-fade-in">
        <div class="flex flex-col md:flex-row h-full">
            <!-- Left side - Document preview -->
            <div class="w-full md:w-2/5 bg-gray-50 p-6 flex flex-col border-r">
                <div class="flex items-center justify-between mb-4">
                    <span id="file-status-badge" class="badge badge-default">Active</span>
                    <button id="toggle-star" class="btn btn-ghost h-8 w-8">
                        <i data-lucide="star" class="h-4 w-4"></i>
                    </button>
                </div>

                <div id="file-preview" class="flex-1 flex flex-col items-center justify-center">
                    <!-- Real image preview instead of generic PDF placeholder -->
                    <div class="relative w-full max-w-[250px] aspect-[3/4] bg-white rounded-lg shadow-md border overflow-hidden mx-auto">
                        <!-- Image will be dynamically loaded based on file ID -->
                        <img src="{{ asset('storage/upload/dummy/1.jpg') }}" alt="Document Preview" class="w-full h-full object-cover" id="preview-image">
                        <div class="absolute top-0 left-0 w-full h-8 bg-gradient-to-b from-black/40 to-transparent">
                            <div class="flex items-center h-full px-3">
                                <div class="w-3 h-3 rounded-full bg-white/80 mr-1.5"></div>
                                <div class="w-3 h-3 rounded-full bg-white/80 mr-1.5"></div>
                                <div class="w-3 h-3 rounded-full bg-white/80"></div>
                            </div>
                        </div>
                        <div class="absolute bottom-3 right-3">
                            <svg class="w-10 h-10 text-white drop-shadow-md" viewBox="0 0 24 24" fill="currentColor" opacity="0.7">
                                <path d="M19.826 21.214H4.174A2.174 2.174 0 0 1 2 19.04V4.96c0-1.2.974-2.174 2.174-2.174h15.652A2.174 2.174 0 0 1 22 4.96v14.08a2.174 2.174 0 0 1-2.174 2.174M4.174 4.13a.83.83 0 0 0-.83.83v14.08c0 .458.372.83.83.83h15.652a.83.83 0 0 0 .83-.83V4.96a.83.83 0 0 0-.83-.83H4.174z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Format:</span>
                        <span id="file-format" class="badge badge-outline font-mono">PDF</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Size:</span>
                        <span id="file-size" class="font-medium">2.4 MB</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Pages:</span>
                        <span id="file-pages" class="font-medium">5</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-2">
                    <button id="close-details" class="btn btn-outline w-full" onclick="window.closeFileDetails()">Close</button>
                    <button id="view-document" class="btn btn-primary w-full">View Document</button>
                </div>
            </div>

            <!-- Right side - Document details -->
            <div class="w-full md:w-3/5 p-6">
                <div class="mb-6">
                    <h2 id="file-name" class="text-2xl font-bold">Alhaji Ibrahim Dantata</h2>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">File Number</h4>
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                                <p id="file-number" class="text-sm font-medium">RES-86-2244</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">KANGIS File No</h4>
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                                <p id="kangis-file-no" class="text-sm font-medium">KNGP 00338</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">New KANGIS File No</h4>
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full bg-purple-500 mr-2"></div>
                                <p id="new-kangis-file-no" class="text-sm font-medium">KNO001</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Upload Date</h4>
                            <p id="upload-date" class="text-sm">2023-01-15</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Last Accessed</h4>
                            <p id="last-accessed" class="text-sm">2023-06-10</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Uploaded By</h4>
                            <p id="uploaded-by" class="text-sm">Admin User</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Tags</h4>
                        <div id="file-tags" class="flex flex-wrap gap-1.5">
                            <span class="badge badge-secondary px-2 py-1">Residential</span>
                            <span class="badge badge-secondary px-2 py-1">Certificate</span>
                            <span class="badge badge-secondary px-2 py-1">Nasarawa</span>
                        </div>
                    </div>

                    <div id="document-pages-container">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Document Pages</h4>
                        <div class="border rounded-md overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b grid grid-cols-12 text-xs font-medium text-gray-500">
                                <div class="col-span-1">#</div>
                                <div class="col-span-5">Title</div>
                                <div class="col-span-3">Type</div>
                                <div class="col-span-3">Page Code</div>
                            </div>
                            <div id="document-pages" class="max-h-[200px] overflow-y-auto">
                                <!-- Example page rows -->
                                <div class="px-4 py-2 border-b grid grid-cols-12 text-sm hover:bg-gray-50 cursor-pointer">
                                    <div class="col-span-1 font-medium text-gray-500">1</div>
                                    <div class="col-span-5 truncate">Cover Page</div>
                                    <div class="col-span-3 truncate text-gray-600">File Cover</div>
                                    <div class="col-span-3 truncate font-mono text-xs text-gray-500">KNGP 00338-1-1-01</div>
                                </div>
                                <div class="px-4 py-2 border-b grid grid-cols-12 text-sm hover:bg-gray-50 cursor-pointer">
                                    <div class="col-span-1 font-medium text-gray-500">2</div>
                                    <div class="col-span-5 truncate">Application Form</div>
                                    <div class="col-span-3 truncate text-gray-600">Application</div>
                                    <div class="col-span-3 truncate font-mono text-xs text-gray-500">KNGP 00338-2-3-02</div>
                                </div>
                                <div class="px-4 py-2 border-b grid grid-cols-12 text-sm hover:bg-gray-50 cursor-pointer">
                                    <div class="col-span-1 font-medium text-gray-500">3</div>
                                    <div class="col-span-5 truncate">Personal Information</div>
                                    <div class="col-span-3 truncate text-gray-600">Application</div>
                                    <div class="col-span-3 truncate font-mono text-xs text-gray-500">KNGP 00338-2-3-03</div>
                                </div>
                                <div class="px-4 py-2 border-b grid grid-cols-12 text-sm hover:bg-gray-50 cursor-pointer">
                                    <div class="col-span-1 font-medium text-gray-500">4</div>
                                    <div class="col-span-5 truncate">Property Details</div>
                                    <div class="col-span-3 truncate text-gray-600">Land Title</div>
                                    <div class="col-span-3 truncate font-mono text-xs text-gray-500">KNGP 00338-5-5-04</div>
                                </div>
                                <div class="px-4 py-2 grid grid-cols-12 text-sm hover:bg-gray-50 cursor-pointer">
                                    <div class="col-span-1 font-medium text-gray-500">5</div>
                                    <div class="col-span-5 truncate">Survey Plan</div>
                                    <div class="col-span-3 truncate text-gray-600">Survey</div>
                                    <div class="col-span-3 truncate font-mono text-xs text-gray-500">KNGP 00338-9-25-05</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <button class="btn btn-outline btn-sm gap-1">
                        <i data-lucide="download" class="h-4 w-4"></i>
                        Download
                    </button>
                    <button class="btn btn-outline btn-sm gap-1">
                        <i data-lucide="printer" class="h-4 w-4"></i>
                        Print
                    </button>
                    <button class="btn btn-outline btn-sm gap-1">
                        <i data-lucide="share-2" class="h-4 w-4"></i>
                        Share
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Immediately executing function to ensure the modal is hidden
(function() {
    // Make absolutely sure the file details dialog is hidden on page load
    const hideFileDetails = function() {
        const dialog = document.getElementById('file-details-dialog');
        if (dialog) {
            dialog.classList.add('hidden');
            dialog.style.display = 'none';
            dialog.setAttribute('aria-hidden', 'true');
        }
    };
    
    // Hide immediately
    hideFileDetails();
    
    // Also hide after a tiny delay to override any other scripts
    setTimeout(hideFileDetails, 50);
    
    // And again after the page has fully loaded
    window.addEventListener('load', hideFileDetails);
    
    // Define a global function to close the dialog
    window.closeFileDetails = function() {
        const dialog = document.getElementById('file-details-dialog');
        if (dialog) {
            dialog.classList.add('hidden');
            dialog.style.display = 'none';
            dialog.setAttribute('aria-hidden', 'true');
        }
    };
    
    // Add a click handler to the backdrop to close when clicking outside
    const dialog = document.getElementById('file-details-dialog');
    if (dialog) {
        dialog.addEventListener('click', function(e) {
            if (e.target === dialog) {
                window.closeFileDetails();
            }
        });
    }
})();

// Make sure clicking outside the dialog and the close button works
(function() {
    function setupListeners() {
        const dialog = document.getElementById('file-details-dialog');
        const closeBtn = document.getElementById('close-details');
        
        // Ensure click outside closes modal
        if (dialog) {
            dialog.addEventListener('click', function(e) {
                if (e.target === this && window.closeFileDetails) {
                    window.closeFileDetails();
                }
            });
        }
        
        // Ensure close button works
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                if (window.closeFileDetails) {
                    window.closeFileDetails();
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

// Update preview image based on file ID
$(document).ready(function() {
    // Map of file IDs to image paths
    const fileImages = {
        'FILE-2023-001': 'storage/upload/dummy/1.jpg',
        'FILE-2023-002': 'storage/upload/dummy/2.jpg',
        'FILE-2023-003': 'storage/upload/dummy/3.jpg',
        'FILE-2023-004': 'storage/upload/dummy/5.jpg',
        'FILE-2023-005': 'storage/upload/dummy/7.jpg',
        'FILE-2023-006': 'storage/upload/dummy/9.jpg'
    };
    
    // When file card is clicked
    $('.file-card').on('click', function() {
        const fileId = $(this).data('id');
        // Update preview image if available
        if (fileImages[fileId]) {
            $('#preview-image').attr('src', fileImages[fileId]);
        }
    });
});
</script>
