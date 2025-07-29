<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // IMMEDIATE DIALOG HIDING - Force dialogs to be hidden as soon as script runs
    (function() {
        // These force-hide operations run immediately when the script is parsed
        const forceHideDialogs = function() {
            $('#preview-dialog, #file-selector-dialog, #document-details-dialog').each(function() {
                $(this).addClass('hidden').css('display', 'none');
            });
        };
        
        // Try to hide immediately
        forceHideDialogs();
        
        // Also hide when DOM is interactive
        $(document).ready(forceHideDialogs);
        
        // Extra safeguard - hide on window load too
        $(window).on('load', forceHideDialogs);
    })();

    // Initialize Lucide icons
    lucide.createIcons();

    // State management
    const state = {
      activeTab: 'upload',
      uploadStatus: 'idle', // 'idle', 'uploading', 'complete', 'error'
      uploadProgress: 0,
      previewOpen: false,
      selectedFile: null,
      zoomLevel: 100,
      rotation: 0,
      currentPreviewPage: 1,
      selectedIndexedFile: null,
      showFileSelector: false,
      selectedUploadFiles: [],
      showFolderView: false,
      selectedPageInFolder: null,
      showDocumentDetails: false,
      currentDocumentIndex: null,
      documentBatches: [],
      uploadDocuments: [],
      filterPaperSize: 'All'
    };

    // Sample data
    const documentTypes = ["Certificate", "Deed", "Letter", "Application Form", "Map", "Survey Plan", "Receipt", "Other"];

    const indexedFiles = [
      {
        id: "FILE-2023-004",
        fileNumber: "KNML 08146",
        name: "Musa Usman Bayero",
        type: "Right of Occupancy",
        landUseType: "Residential",
        district: "Nasarawa",
      },
      {
        id: "FILE-2023-005",
        fileNumber: "MLKN 03888",
        name: "Hajiya Fatima Mohammed",
        type: "Deed of Assignment",
        landUseType: "Industrial",
        district: "Bompai",
      },
      {
        id: "FILE-2023-006",
        fileNumber: "KNGP 00721",
        name: "Abdullahi Sani",
        type: "Site Plan",
        landUseType: "Commercial",
        district: "Fagge",
      },
    ];

    // Sample document previews
    const samplePages = [
      "{{ asset('storage/upload/dummy/1.jpg') }}",
      "{{ asset('storage/upload/dummy/2.jpg') }}",
      "{{ asset('storage/upload/dummy/3.jpg') }}"
    ];

    // Initialize with a sample batch
    state.documentBatches = [
      {
        id: "BATCH-EXAMPLE",
        fileNumber: "KNML 09846",
        name: "Alhaji Ibrahim Dantata",
        documents: [
          {
            fileName: "certificate.pdf",
            fileSize: 1024 * 1024, // 1MB
            paperSize: "A4",
            documentType: "Certificate",
          },
          {
            fileName: "deed.pdf",
            fileSize: 512 * 1024, // 0.5MB
            paperSize: "A5",
            documentType: "Deed",
          },
          {
            fileName: "letter.pdf",
            fileSize: 256 * 1024, // 0.25MB
            paperSize: "A4",
            documentType: "Letter",
          },
        ],
        date: "2023-06-18",
        status: "Ready for page typing",
      },
    ];

    // DOM Elements using jQuery
    const elements = {
      // Tabs
      tabs: $('[role="tab"]'),
      tabContents: $('[role="tabpanel"]'),
      
      // Upload tab
      selectedFileBadge: $('.selected-file-badge'),
      selectedFileNumber: $('#selected-file-number'),
      selectFileBtn: $('#select-file-btn'),
      changeFileText: $('#change-file-text'),
      uploadIdle: $('#upload-idle'),
      fileUpload: $('#file-upload'),
      browseFilesBtn: $('#browse-files-btn'),
      selectFileWarning: $('#select-file-warning'),
      selectedFilesContainer: $('#selected-files-container'),
      selectedFilesCount: $('#selected-files-count'),
      selectedFilesList: $('#selected-files-list'),
      clearAllBtn: $('#clear-all-btn'),
      uploadProgress: $('#upload-progress'),
      uploadingCount: $('#uploading-count'),
      uploadPercentage: $('#upload-percentage'),
      progressBar: $('#progress-bar'),
      uploadComplete: $('#upload-complete'),
      startUploadBtn: $('#start-upload-btn'),
      cancelUploadBtn: $('#cancel-upload-btn'),
      uploadMoreBtn: $('#upload-more-btn'),
      viewUploadedBtn: $('#view-uploaded-btn'),
      
      // Uploaded files tab
      uploadsCount: $('#uploads-count'),
      pendingCount: $('#pending-count'),
      paperSizeFilter: $('#paper-size-filter'),
      toggleViewBtn: $('#toggle-view-btn'),
      noDocuments: $('#no-documents'),
      listView: $('#list-view'),
      folderView: $('#folder-view'),
      batchActions: $('#batch-actions'),
      uploadMoreBtn2: $('#upload-more-btn-2'),
      proceedToTypingBtn: $('#proceed-to-typing-btn'),
      goToUploadBtn: $('#go-to-upload-btn'),
      
      // Preview dialog
      previewDialog: $('#preview-dialog'),
      previewTitle: $('#preview-title'),
      previewImage: $('#preview-image'),
      documentInfo: $('#document-info'),
      prevPageBtn: $('#prev-page-btn'),
      nextPageBtn: $('#next-page-btn'),
      zoomOutBtn: $('#zoom-out-btn'),
      zoomLevel: $('#zoom-level'),
      zoomInBtn: $('#zoom-in-btn'),
      rotateBtn: $('#rotate-btn'),
      proceedToTypingFromPreviewBtn: $('#proceed-to-typing-from-preview-btn'),
      
      // File selector dialog
      fileSelectorDialog: $('#file-selector-dialog'),
      indexedFilesList: $('#indexed-files-list'),
      cancelFileSelectBtn: $('#cancel-file-select-btn'),
      confirmFileSelectBtn: $('#confirm-file-select-btn'),
      
      // Document details dialog
      documentDetailsDialog: $('#document-details-dialog'),
      documentName: $('#document-name'),
      paperSizeRadios: $('input[name="paper-size"]'),
      documentType: $('#document-type'),
      documentNotes: $('#document-notes'),
      cancelDetailsBtn: $('#cancel-details-btn'),
      saveDetailsBtn: $('#save-details-btn')
    };

    // Helper functions
    function formatFileSize(bytes) {
      if (bytes < 1024) return bytes + " B";
      else if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + " KB";
      else return (bytes / (1024 * 1024)).toFixed(2) + " MB";
    }

    function getPaperSizeColor(size) {
      switch (size) {
        case "A4": return "bg-blue-500";
        case "A5": return "bg-green-500";
        case "A3": return "bg-purple-500";
        case "Letter": return "bg-amber-500";
        case "Legal": return "bg-rose-500";
        case "Custom": return "bg-gray-500";
        default: return "bg-gray-500";
      }
    }

    // UI update functions
    function updateUI() {
      // Update tabs
      elements.tabs.each(function() {
        const tabId = $(this).attr('data-tab');
        $(this).attr('aria-selected', tabId === state.activeTab);
      });
      
      elements.tabContents.each(function() {
        const contentId = $(this).attr('data-tab-content');
        $(this).attr('aria-hidden', contentId !== state.activeTab);
      });

      // Update selected file badge
      if (state.selectedIndexedFile) {
        const selectedFile = indexedFiles.find(f => f.id === state.selectedIndexedFile);
        elements.selectedFileBadge.removeClass('hidden');
        elements.selectedFileNumber.text(selectedFile ? selectedFile.fileNumber : 'No file selected');
        elements.changeFileText.text('Change File');
        elements.browseFilesBtn.prop('disabled', false);
        elements.selectFileWarning.addClass('hidden');
      } else {
        elements.selectedFileBadge.addClass('hidden');
        elements.changeFileText.text('Select File');
        elements.browseFilesBtn.prop('disabled', true);
        elements.selectFileWarning.removeClass('hidden');
      }

      // Update upload status
      elements.uploadIdle.toggleClass('hidden', state.uploadStatus !== 'idle');
      elements.uploadProgress.toggleClass('hidden', state.uploadStatus !== 'uploading');
      elements.uploadComplete.toggleClass('hidden', state.uploadStatus !== 'complete');
      
      // Update buttons based on upload status
      elements.startUploadBtn.toggleClass('hidden', 
        !(state.uploadStatus === 'idle' && state.uploadDocuments.length > 0));
      elements.cancelUploadBtn.toggleClass('hidden', state.uploadStatus !== 'uploading');
      elements.uploadMoreBtn.toggleClass('hidden', state.uploadStatus !== 'complete');
      elements.viewUploadedBtn.toggleClass('hidden', state.uploadStatus !== 'complete');

      // Update selected files
      elements.selectedFilesContainer.toggleClass('hidden', state.uploadDocuments.length === 0);
      elements.selectedFilesCount.text(state.uploadDocuments.length);
      
      if (state.uploadDocuments.length > 0) {
        renderSelectedFiles();
      }

      // Update upload progress
      if (state.uploadStatus === 'uploading') {
        elements.uploadingCount.text(state.uploadDocuments.length);
        elements.uploadPercentage.text(`${state.uploadProgress}%`);
        elements.progressBar.css('width', `${state.uploadProgress}%`);
      }

      // Update uploaded files tab
      elements.uploadsCount.text(state.documentBatches.length);
      elements.pendingCount.text(state.documentBatches.reduce(
        (total, batch) => total + batch.documents.length, 0
      ));

      elements.noDocuments.toggleClass('hidden', state.documentBatches.length > 0);
      elements.batchActions.toggleClass('hidden', state.documentBatches.length === 0);
      
      // Update view toggle
      elements.toggleViewBtn.text(state.showFolderView ? 'List View' : 'Folder View');
      elements.listView.toggleClass('hidden', state.showFolderView);
      elements.folderView.toggleClass('hidden', !state.showFolderView);

      if (state.documentBatches.length > 0) {
        renderBatches();
      }

      // Update dialogs - Force hide dialogs based on state
      if (!state.previewOpen) {
          elements.previewDialog.addClass('hidden').css('display', 'none');
      } else {
          elements.previewDialog.removeClass('hidden').css('display', '');
      }
      
      if (!state.showFileSelector) {
          elements.fileSelectorDialog.addClass('hidden').css('display', 'none');
      } else {
          elements.fileSelectorDialog.removeClass('hidden').css('display', '');
      }
      
      if (!state.showDocumentDetails) {
          elements.documentDetailsDialog.addClass('hidden').css('display', 'none');
      } else {
          elements.documentDetailsDialog.removeClass('hidden').css('display', '');
      }
    }

    function renderSelectedFiles() {
      elements.selectedFilesList.empty();
      
      $.each(state.uploadDocuments, function(index, doc) {
        const $fileItem = $('<div>').addClass('flex items-center justify-between p-3');
        $fileItem.html(`
          <div class="flex items-center gap-3">
            <i data-lucide="file" class="h-8 w-8 text-blue-500"></i>
            <div>
              <p class="font-medium">${doc.file.name}</p>
              <div class="flex items-center gap-2 mt-1">
                <span class="badge ${getPaperSizeColor(doc.paperSize)} text-white text-xs">${doc.paperSize}</span>
                <span class="badge badge-outline text-xs">${doc.documentType}</span>
                <span class="text-xs text-muted-foreground">${formatFileSize(doc.file.size)}</span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button class="btn btn-outline btn-sm edit-details" data-index="${index}">
              Edit Details
            </button>
            <button class="btn btn-ghost btn-sm remove-file" data-index="${index}">
              <i data-lucide="trash-2" class="h-4 w-4"></i>
            </button>
          </div>
        `);
        
        elements.selectedFilesList.append($fileItem);
      });
      
      // Initialize icons for the new elements
      lucide.createIcons();
      
      // Add event listeners
      $('.edit-details').on('click', function() {
        const index = parseInt($(this).data('index'));
        openDocumentDetails(index);
      });
      
      $('.remove-file').on('click', function() {
        const index = parseInt($(this).data('index'));
        removeFile(index);
      });
    }

    function renderBatches() {
      const filteredBatches = getFilteredBatches();
      
      if (state.showFolderView) {
        renderFolderView(filteredBatches);
      } else {
        renderListView(filteredBatches);
      }
    }

    function renderListView(batches) {
      elements.listView.empty();
      
      $.each(batches, function(_, batch) {
        // Get unique paper sizes
        const uniquePaperSizes = Array.from(new Set(batch.documents.map(d => d.paperSize)));
        
        const $batchItem = $('<div>').addClass('flex items-center justify-between p-4');
        
        $batchItem.html(`
          <div class="flex items-center gap-3">
            <i data-lucide="file-text" class="h-8 w-8 text-blue-500"></i>
            <div>
              <p class="font-medium text-blue-600">${batch.fileNumber}</p>
              <p class="text-sm text-gray-600">${batch.name}</p>
              <div class="flex items-center gap-2 mt-1">
                <span class="badge badge-outline text-xs">
                  ${batch.documents.length} ${batch.documents.length === 1 ? 'document' : 'documents'}
                </span>
                <span class="text-xs text-muted-foreground">${batch.date}</span>
                <div class="flex gap-1">
                  ${uniquePaperSizes.map(size => 
                    `<span class="badge ${getPaperSizeColor(size)} text-white text-xs">${size}</span>`
                  ).join('')}
                </div>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button class="btn btn-outline btn-sm preview-batch" data-id="${batch.id}">
              <i data-lucide="zoom-in" class="h-4 w-4 mr-1"></i>
              Preview
            </button>
            <a href="{{route('pagetyping.index')}}" class="btn btn-outline btn-sm start-typing" data-id="${batch.id}">
              Start Page Typing
            </a>
            <button class="btn btn-ghost btn-sm text-red-500 delete-batch" data-id="${batch.id}">
              <i data-lucide="trash-2" class="h-4 w-4"></i>
            </button>
          </div>
        `);
        
        elements.listView.append($batchItem);
      });
      
      // Initialize icons for the new elements
      lucide.createIcons();
      
      // Add event listeners
      $('.preview-batch').on('click', function() {
        const id = $(this).data('id');
        openPreview(id);
      });
      
      $('.start-typing').on('click', function() {
        const id = $(this).data('id');
        window.location.href = `/file-digital-registry/page-typing?fileId=${id}`;
      });
      
      $('.delete-batch').on('click', function() {
        const id = $(this).data('id');
        deleteBatch(id);
      });
    }

    function renderFolderView(batches) {
      elements.folderView.empty();
      
      $.each(batches, function(_, batch) {
        const $folderItem = $('<div>').addClass('border rounded-md overflow-hidden');
        
        $folderItem.html(`
          <div class="p-4 bg-muted/20 border-b">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <i data-lucide="folder-open" class="h-6 w-6 text-blue-500"></i>
                <div>
                  <p class="font-medium text-blue-600">${batch.fileNumber}</p>
                  <p class="text-sm">${batch.name}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span class="badge badge-outline text-xs">
                  ${batch.documents.length} ${batch.documents.length === 1 ? 'document' : 'documents'}
                </span>
                <a href="{{route('pagetyping.index')}}" class="btn btn-outline btn-sm start-typing" data-id="${batch.id}">
                  Start Page Typing
                </a>
              </div>
            </div>
          </div>
          <div class="p-4">
            <h4 class="text-sm font-medium mb-3">Documents</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 documents-grid" data-batch-id="${batch.id}">
              <!-- Documents will be added here -->
            </div>
          </div>
        `);
        
        elements.folderView.append($folderItem);
        
        // Add documents to the grid
        const $documentsGrid = $folderItem.find('.documents-grid');
        
        $.each(batch.documents, function(index, doc) {
          if (state.filterPaperSize === 'All' || doc.paperSize === state.filterPaperSize) {
            // Use sample pages for preview
            const pageImage = samplePages[index % samplePages.length];
            
            const $docItem = $('<div>')
              .addClass('border rounded-md overflow-hidden cursor-pointer hover:border-blue-500 transition-colors document-item')
              .attr({
                'data-batch-id': batch.id,
                'data-index': index
              })
              .html(`
                <div class="h-40 bg-muted flex items-center justify-center">
                  <img
                    src="${pageImage}"
                    alt="Document ${index + 1}"
                    class="max-h-full max-w-full object-contain"
                  >
                </div>
                <div class="p-2 bg-gray-50 border-t">
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Document ${index + 1}</span>
                    <span class="badge ${getPaperSizeColor(doc.paperSize)} text-white text-xs">${doc.paperSize}</span>
                  </div>
                  <div class="mt-1">
                    <span class="badge badge-outline text-xs w-full justify-center">${doc.documentType}</span>
                  </div>
                  <div class="mt-1">
                    <span class="badge bg-blue-500 text-white text-xs mt-1 w-full justify-center overflow-hidden text-ellipsis">
                      ${batch.fileNumber}-${(index + 1).toString().padStart(2, '0')}
                    </span>
                  </div>
                </div>
              `);
            
            $documentsGrid.append($docItem);
          }
        });
      });
      
      // Initialize icons for the new elements
      lucide.createIcons();
      
      // Add event listeners
      $('.start-typing').on('click', function() {
        const id = $(this).data('id');
        window.location.href = `/file-digital-registry/page-typing?fileId=${id}`;
      });
      
      $('.document-item').on('click', function() {
        const batchId = $(this).data('batch-id');
        const index = parseInt($(this).data('index'));
        openPreview(batchId, index);
      });
    }

    function updatePreview() {
      const batch = state.documentBatches.find(b => b.id === state.selectedFile);
      
      if (!batch) return;
      
      // Update title
      elements.previewTitle.text(`${batch.name} - Document ${state.currentPreviewPage} of ${batch.documents.length}`);
      
      // Update image
      const pageImage = samplePages[(state.currentPreviewPage - 1) % samplePages.length];
      elements.previewImage.attr('src', pageImage)
        .css('transform', `scale(${state.zoomLevel / 100}) rotate(${state.rotation}deg)`);
      
      // Update document info
      elements.documentInfo.empty();
      
      const $fileNumberBadge = $('<span>')
        .addClass('badge mr-2')
        .text(`${batch.fileNumber}-${state.currentPreviewPage.toString().padStart(2, '0')}`);
      elements.documentInfo.append($fileNumberBadge);
      
      if (batch.documents[state.currentPreviewPage - 1]) {
        const doc = batch.documents[state.currentPreviewPage - 1];
        
        const $paperSizeBadge = $('<span>')
          .addClass(`badge mr-2 ${getPaperSizeColor(doc.paperSize)}`)
          .text(doc.paperSize);
        elements.documentInfo.append($paperSizeBadge);
        
        const $typeBadge = $('<span>')
          .addClass('badge badge-outline')
          .text(doc.documentType);
        elements.documentInfo.append($typeBadge);
      }
      
      // Update navigation buttons
      elements.prevPageBtn.prop('disabled', state.currentPreviewPage <= 1);
      elements.nextPageBtn.prop('disabled', state.currentPreviewPage >= batch.documents.length);
      
      // Update zoom level
      elements.zoomLevel.text(`${state.zoomLevel}%`);
    }

    function renderIndexedFiles() {
      elements.indexedFilesList.empty();
      
      $.each(indexedFiles, function(_, file) {
        const $fileItem = $('<div>')
          .addClass(`flex items-center p-4 cursor-pointer hover:bg-muted/50 ${
            state.selectedIndexedFile === file.id ? 'bg-muted' : ''
          }`)
          .attr('data-id', file.id)
          .html(`
            <i data-lucide="folder" class="h-6 w-6 mr-3 ${
              state.selectedIndexedFile === file.id ? 'text-blue-500' : 'text-gray-400'
            }"></i>
            <div>
              <p class="font-medium text-blue-600">${file.fileNumber}</p>
              <p class="text-sm">${file.name}</p>
              <div class="flex items-center gap-2 mt-1">
                <span class="badge badge-secondary text-xs">${file.landUseType}</span>
                <span class="badge badge-outline text-xs">${file.district}</span>
              </div>
            </div>
          `);
        
        elements.indexedFilesList.append($fileItem);
      });
      
      // Initialize icons for the new elements
      lucide.createIcons();
      
      // Add event listeners
      $('#indexed-files-list > div').on('click', function() {
        const id = $(this).data('id');
        selectIndexedFileTemp(id);
      });
      
      // Update confirm button
      elements.confirmFileSelectBtn.prop('disabled', !state.selectedIndexedFile);
    }

    function updateDocumentDetails() {
      const doc = state.uploadDocuments[state.currentDocumentIndex];
      
      if (!doc) return;
      
      // Update file name
      elements.documentName.text(doc.file.name);
      
      // Update paper size
      elements.paperSizeRadios.each(function() {
        $(this).prop('checked', $(this).val() === doc.paperSize);
      });
      
      // Update document type
      elements.documentType.val(doc.documentType);
      
      // Update notes
      elements.documentNotes.val(doc.notes || '');
    }

    function getFilteredBatches() {
      if (state.filterPaperSize === 'All') {
        return state.documentBatches;
      }
      
      return state.documentBatches.filter(batch => 
        batch.documents.some(doc => doc.paperSize === state.filterPaperSize)
      );
    }

    // Event handlers
    function switchTab(tabId) {
      state.activeTab = tabId;
      updateUI();
    }

    function handleFileSelect(e) {
      if (e.target.files && e.target.files.length > 0) {
        const files = Array.from(e.target.files);
        state.selectedUploadFiles = files;
        
        // Initialize upload documents with default values
        state.uploadDocuments = files.map(file => ({
          file,
          paperSize: 'A4', // Default paper size
          documentType: 'Other', // Default document type
        }));
        
        updateUI();
      }
    }

    function openDocumentDetails(index) {
      state.currentDocumentIndex = index;
      state.showDocumentDetails = true;
      updateDocumentDetails();
      updateUI();
    }

    function updateDocumentDetails(index, updates) {
      state.uploadDocuments = state.uploadDocuments.map((doc, i) => 
        i === index ? { ...doc, ...updates } : doc
      );
      updateUI();
    }

    function startUpload() {
      if (!state.selectedIndexedFile) {
        state.showFileSelector = true;
        updateUI();
        return;
      }
      
      if (state.uploadDocuments.length === 0) {
        alert('Please select files to upload');
        return;
      }
      
      state.uploadStatus = 'uploading';
      state.uploadProgress = 0;
      updateUI();
      
      const interval = setInterval(() => {
        state.uploadProgress += 10;
        
        if (state.uploadProgress >= 100) {
          clearInterval(interval);
          state.uploadProgress = 100;
          state.uploadStatus = 'complete';
          
          // Find the selected indexed file
          const indexedFile = indexedFiles.find(f => f.id === state.selectedIndexedFile);
          
          // Add a new batch to the list
          if (indexedFile) {
            // Convert UploadDocument to BatchDocument
            const batchDocuments = state.uploadDocuments.map(doc => ({
              fileName: doc.file.name,
              fileSize: doc.file.size,
              paperSize: doc.paperSize,
              documentType: doc.documentType,
              notes: doc.notes,
            }));
            
            const newBatch = {
              id: `BATCH-${Date.now()}`,
              fileNumber: indexedFile.fileNumber,
              name: indexedFile.name,
              documents: batchDocuments,
              date: new Date().toLocaleDateString(),
              status: 'Ready for page typing',
            };
            
            state.documentBatches = [newBatch, ...state.documentBatches];
          }
        }
        
        updateUI();
      }, 500);
    }

    function resetUpload() {
      state.uploadStatus = 'idle';
      state.uploadProgress = 0;
      state.selectedUploadFiles = [];
      state.uploadDocuments = [];
      elements.fileUpload.val('');
      updateUI();
    }

    function sendToPageTyping() {
      if (state.documentBatches.length === 0) {
        alert('No files to send to page typing');
        return;
      }
      
      window.location.href = '/file-digital-registry/page-typing';
    }

    function openPreview(batchId, documentIndex = 0) {
      state.selectedFile = batchId;
      state.currentPreviewPage = documentIndex + 1;
      state.zoomLevel = 100;
      state.rotation = 0;
      state.previewOpen = true;
      updatePreview();
      updateUI();
    }

    function closePreview() {
      state.previewOpen = false;
      updateUI();
    }

    function nextPage() {
      const batch = state.documentBatches.find(b => b.id === state.selectedFile);
      if (batch && state.currentPreviewPage < batch.documents.length) {
        state.currentPreviewPage++;
        updatePreview();
      }
    }

    function prevPage() {
      if (state.currentPreviewPage > 1) {
        state.currentPreviewPage--;
        updatePreview();
      }
    }

    function zoomIn() {
      state.zoomLevel = Math.min(state.zoomLevel + 25, 200);
      updatePreview();
    }

    function zoomOut() {
      state.zoomLevel = Math.max(state.zoomLevel - 25, 50);
      updatePreview();
    }

    function rotate() {
      state.rotation = (state.rotation + 90) % 360;
      updatePreview();
    }

    function selectIndexedFileTemp(fileId) {
      // Update UI in the dialog
      $('#indexed-files-list > div').each(function() {
        const id = $(this).data('id');
        const $folderIcon = $(this).find('[data-lucide="folder"]');
        
        if (id === fileId) {
          $(this).addClass('bg-muted');
          $folderIcon.addClass('text-blue-500').removeClass('text-gray-400');
        } else {
          $(this).removeClass('bg-muted');
          $folderIcon.removeClass('text-blue-500').addClass('text-gray-400');
        }
      });
      
      // Update confirm button
      elements.confirmFileSelectBtn.prop('disabled', !fileId);
      
      // Store the selected ID temporarily
      state.selectedIndexedFile = fileId;
    }

    function selectIndexedFile() {
      state.showFileSelector = false;
      updateUI();
    }

    function removeFile(index) {
      state.selectedUploadFiles = state.selectedUploadFiles.filter((_, i) => i !== index);
      state.uploadDocuments = state.uploadDocuments.filter((_, i) => i !== index);
      updateUI();
    }

    function deleteBatch(id) {
      if (confirm('Are you sure you want to delete this batch?')) {
        state.documentBatches = state.documentBatches.filter(batch => batch.id !== id);
        updateUI();
      }
    }

    function saveDocumentDetails() {
      if (state.currentDocumentIndex === null) return;
      
      const paperSize = $('input[name="paper-size"]:checked').val() || 'A4';
      const documentType = elements.documentType.val();
      const notes = elements.documentNotes.val();
      
      updateDocumentDetails(state.currentDocumentIndex, {
        paperSize: paperSize,
        documentType: documentType,
        notes: notes
      });
      
      state.showDocumentDetails = false;
      updateUI();
    }

    // Initialize the page
    function init() {
      // Immediately force hide all dialogs again
      elements.previewDialog.addClass('hidden').css('display', 'none');
      elements.fileSelectorDialog.addClass('hidden').css('display', 'none');
      elements.documentDetailsDialog.addClass('hidden').css('display', 'none');
      
      // Set up event listeners
      
      // Tab switching
      elements.tabs.on('click', function() {
        const tabId = $(this).attr('data-tab');
        switchTab(tabId);
      });
      
      // File upload
      elements.fileUpload.on('change', handleFileSelect);
      elements.browseFilesBtn.on('click', function() {
        elements.fileUpload.trigger('click');
      });
      
      // Select file
      elements.selectFileBtn.on('click', function() {
        state.showFileSelector = true;
        renderIndexedFiles();
        updateUI();
      });
      
      // Upload actions
      elements.clearAllBtn.on('click', resetUpload);
      elements.startUploadBtn.on('click', startUpload);
      elements.cancelUploadBtn.on('click', resetUpload);
      elements.uploadMoreBtn.on('click', resetUpload);
      elements.viewUploadedBtn.on('click', function() {
        switchTab('uploaded-files');
      });
      elements.uploadMoreBtn2.on('click', function() {
        switchTab('upload');
      });
      elements.proceedToTypingBtn.on('click', sendToPageTyping);
      elements.goToUploadBtn.on('click', function() {
        switchTab('upload');
      });
      
      // Preview dialog
      elements.previewDialog.on('click', function(e) {
        if (e.target === this) {
          closePreview();
        }
      });
      elements.prevPageBtn.on('click', prevPage);
      elements.nextPageBtn.on('click', nextPage);
      elements.zoomInBtn.on('click', zoomIn);
      elements.zoomOutBtn.on('click', zoomOut);
      elements.rotateBtn.on('click', rotate);
      elements.proceedToTypingFromPreviewBtn.on('click', function() {
        if (state.selectedFile) {
          window.location.href = `/file-digital-registry/page-typing?fileId=${state.selectedFile}`;
        }
      });
      
      // File selector dialog
      elements.fileSelectorDialog.on('click', function(e) {
        if (e.target === this) {
          state.showFileSelector = false;
          updateUI();
        }
      });
      elements.cancelFileSelectBtn.on('click', function() {
        state.showFileSelector = false;
        updateUI();
      });
      elements.confirmFileSelectBtn.on('click', selectIndexedFile);
      
      // Document details dialog
      elements.documentDetailsDialog.on('click', function(e) {
        if (e.target === this) {
          state.showDocumentDetails = false;
          updateUI();
        }
      });
      elements.cancelDetailsBtn.on('click', function() {
        state.showDocumentDetails = false;
        updateUI();
      });
      elements.saveDetailsBtn.on('click', saveDocumentDetails);
      
      // Toggle view
      elements.toggleViewBtn.on('click', function() {
        state.showFolderView = !state.showFolderView;
        updateUI();
      });
      
      // Paper size filter
      elements.paperSizeFilter.on('change', function() {
        state.filterPaperSize = $(this).val();
        updateUI();
      });
      
      // Explicitly ensure all modals are hidden on page load
      state.previewOpen = false;
      state.showFileSelector = false;
      state.showDocumentDetails = false;
      
      // Initial UI update
      updateUI();
      
      // Render indexed files
      renderIndexedFiles();
      
      // Render batches
      renderBatches();
    }
    
    // Initialize the page when DOM is loaded
    $(document).ready(init);
  </script>