 <script>
// Immediately declare and initialize global variables at the very top
(function() {
    // Force global scope
    window.selectedFiles = [];
    window.currentPreviewFile = null;
})();

// Global variables - declared at the very top to ensure availability
var selectedFiles = [];
var currentPreviewFile = null;

// Also assign to window object for global access
window.selectedFiles = window.selectedFiles || [];
window.currentPreviewFile = window.currentPreviewFile || null;

// Fallback declarations with explicit global scope
if (typeof selectedFiles === 'undefined') {
    selectedFiles = [];
}
if (typeof currentPreviewFile === 'undefined') {
    currentPreviewFile = null;
}

// Ensure variables are accessible globally
this.selectedFiles = selectedFiles;
this.currentPreviewFile = currentPreviewFile;

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Set current date if element exists
    const currentDateEl = document.getElementById('currentDate');
    if (currentDateEl) {
        currentDateEl.textContent = new Date().toLocaleDateString();
    }
    
    // Initialize global variables again as backup
    if (!window.selectedFiles) {
        window.selectedFiles = [];
    }
    if (!window.currentPreviewFile) {
        window.currentPreviewFile = null;
    }
    
    // Sync with global scope
    selectedFiles = window.selectedFiles;
    currentPreviewFile = window.currentPreviewFile;
});
    
    // Browse to BLIND SCANS folder functionality
    function browseToBlindscanFolder() {
      // This would typically interface with the file system
      // For demo purposes, we'll show an alert
      alert('Opening BLIND SCANS folder...\nPath: /BLIND SCANS/');
      
      // In a real implementation, this might:
      // window.electronAPI?.openFolder('/BLIND SCANS/');
    }
    
    // Upload modal functions
    function closeUploadModal() {
      document.getElementById('uploadModal').classList.add('hidden');
    }
    
    function showUploadModal() {
      document.getElementById('uploadModal').classList.remove('hidden');
    }
    
    // Folder selection functionality
    function updateFolderSelection() {
      const folderSelect = document.getElementById('folderTypeSelect');
      const browseBtn = document.getElementById('browseFolderBtn');
      
      if (folderSelect.value) {
        browseBtn.disabled = false;
        browseBtn.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
        browseBtn.classList.add('hover:bg-blue-700');
      } else {
        browseBtn.disabled = true;
        browseBtn.classList.add('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
        browseBtn.classList.remove('hover:bg-blue-700');
      }
    }
    
    // Browse for folder functionality
    function browseForFolder() {
      const folderSelect = document.getElementById('folderTypeSelect');
      const selectedType = folderSelect.value;
      
      if (!selectedType) {
        alert('Please select a folder type first (A4, A3, or All Documents)');
        return;
      }
      
      // Trigger file input for folder selection
      const folderInput = document.getElementById('folderInput');
      folderInput.click();
    }

    // Alias for browseForFiles to match HTML onclick
    function browseForFiles() {
      browseForFolder();
    }
    
    // Handle file selection
    function handleFileSelection(event) {
      // Add console logging for debugging
      console.log('handleFileSelection called');
      
      // Ensure selectedFiles is properly initialized with multiple fallback strategies
      try {
        if (typeof selectedFiles === 'undefined' || selectedFiles === null) {
          console.log('Initializing selectedFiles from undefined/null');
          selectedFiles = [];
        }
        if (typeof window.selectedFiles === 'undefined' || window.selectedFiles === null) {
          console.log('Initializing window.selectedFiles from undefined/null');
          window.selectedFiles = [];
        }
        
        // Sync the variables
        if (Array.isArray(window.selectedFiles) && !Array.isArray(selectedFiles)) {
          selectedFiles = window.selectedFiles;
        } else if (Array.isArray(selectedFiles) && !Array.isArray(window.selectedFiles)) {
          window.selectedFiles = selectedFiles;
        }
        
        console.log('selectedFiles after initialization:', selectedFiles);
      } catch (error) {
        console.error('Error initializing selectedFiles:', error);
        selectedFiles = [];
        window.selectedFiles = [];
      }
      
      const files = event.target.files;
      const folderSelect = document.getElementById('folderTypeSelect');
      
      if (!files || files.length === 0) return;
      if (!folderSelect) {
        console.error('Folder select element not found');
        return;
      }
      
      const selectedType = folderSelect.value;
      
      // Convert FileList to Array
      let filteredFiles = Array.from(files);
      
      // Filter files based on selected type (if user wants to filter by name patterns)
      if (selectedType === 'A4') {
        // For A4, we'll accept all files but user can manually filter by naming convention
        // No automatic filtering since we're selecting individual files
      } else if (selectedType === 'A3') {
        // For A3, we'll accept all files but user can manually filter by naming convention
        // No automatic filtering since we're selecting individual files
      }
      
      // Filter for common document formats
      filteredFiles = filteredFiles.filter(file => {
        const ext = file.name.toLowerCase().split('.').pop();
        return ['pdf', 'jpg', 'jpeg', 'png', 'tiff', 'tif', 'bmp', 'gif'].includes(ext);
      });
      
      // Sort files by name for better organization
      filteredFiles.sort((a, b) => a.name.localeCompare(b.name));
      
      // Store files globally
      console.log('Setting selectedFiles to:', filteredFiles);
      selectedFiles = filteredFiles;
      window.selectedFiles = filteredFiles; // Also set on window for safety
      
      console.log('Selected files count:', selectedFiles.length);
      displaySelectedFiles(filteredFiles, selectedType);
    }
    
    // Display selected files
    function displaySelectedFiles(files, folderType) {
      const defaultMessage = document.getElementById('defaultMessage');
      const fileListArea = document.getElementById('fileListArea');
      const fileGrid = document.getElementById('fileGrid');
      const selectedFolderType = document.getElementById('selectedFolderType');
      const totalFileCount = document.getElementById('totalFileCount');
      const selectedFileCount = document.getElementById('selectedFileCount');
      
      // Hide default message and show file list
      defaultMessage.classList.add('hidden');
      fileListArea.classList.remove('hidden');
      
      // Update folder type badge
      const typeColors = {
        'A4': 'bg-blue-100 text-blue-800',
        'A3': 'bg-green-100 text-green-800',
        'ALL': 'bg-purple-100 text-purple-800'
      };
      
      selectedFolderType.className = `text-sm px-3 py-1 rounded-full ${typeColors[folderType] || typeColors['ALL']}`;
      selectedFolderType.textContent = `${folderType} Documents`;
      
      // Update file count
      totalFileCount.textContent = files.length;
      selectedFileCount.textContent = '0'; // Reset selected count
      
      // Clear and populate file grid
      fileGrid.innerHTML = '';
      
      if (files.length === 0) {
        fileGrid.innerHTML = `
          <div class="col-span-full text-center py-8">
            <div class="text-gray-400 mb-2">
              <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <p class="text-gray-500">No valid document files selected</p>
            <p class="text-sm text-gray-400 mt-1">Please select files with supported formats: PDF, JPG, PNG, TIFF</p>
          </div>
        `;
      } else {
        files.forEach((file, index) => {
          const fileCard = createFileCard(file, index);
          fileGrid.appendChild(fileCard);
        });
      }
      
      // Add clear button to preview section
      addClearButton();
      
      // Show success message
      if (files.length > 0) {
        showNotification(`Successfully loaded ${files.length} files for ${folderType} processing`, 'success');
      }
    }
    
    // Show notification function
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
      
      const colors = {
        'success': 'bg-green-500 text-white',
        'error': 'bg-red-500 text-white',
        'info': 'bg-blue-500 text-white',
        'warning': 'bg-yellow-500 text-black'
      };
      
      notification.className += ` ${colors[type] || colors['info']}`;
      notification.innerHTML = `
        <div class="flex items-center space-x-2">
          <span>${message}</span>
          <button onclick="this.parentElement.parentElement.remove()" class="ml-2 opacity-75 hover:opacity-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      `;
      
      document.body.appendChild(notification);
      
      // Animate in
      setTimeout(() => {
        notification.classList.remove('translate-x-full');
      }, 100);
      
      // Auto remove after 3 seconds
      setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
          if (notification.parentElement) {
            notification.remove();
          }
        }, 300);
      }, 3000);
    }
    
    // Create file card element
    function createFileCard(file, index) {
      const fileCard = document.createElement('div');
      fileCard.className = 'file-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-md transition-all duration-200 cursor-pointer';
      
      const fileSize = (file.size / (1024 * 1024)).toFixed(2);
      const fileExt = file.name.toLowerCase().split('.').pop();
      const lastModified = new Date(file.lastModified).toLocaleDateString();
      
      // Determine file type icon and color
      const fileTypeInfo = getFileTypeInfo(fileExt);
      
      fileCard.innerHTML = `
        <div class="relative">
          <!-- Thumbnail Container -->
          <div class="file-preview-area w-full h-32 bg-gray-100 rounded-lg mb-3 flex items-center justify-center overflow-hidden hover:bg-gray-200 transition-colors duration-200" onclick="previewFile(${index})" title="Click to preview ${file.name}">
            <div id="thumbnail-${index}" class="w-full h-full flex items-center justify-center">
              <div class="${fileTypeInfo.bgColor} p-3 rounded-lg">
                ${fileTypeInfo.icon}
              </div>
            </div>
            <!-- Preview overlay on hover -->
            <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center opacity-0 hover:opacity-100 rounded-lg">
              <div class="bg-white bg-opacity-90 rounded-full p-2">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </div>
            </div>
          </div>
          
          <!-- File Info -->
          <div class="space-y-1 file-preview-area hover:bg-gray-50 p-1 rounded transition-colors duration-200" onclick="previewFile(${index})" title="Click to preview ${file.name}">
            <p class="text-sm font-medium text-gray-900 truncate" title="${file.name}">
              ${file.name}
            </p>
            <p class="text-xs text-gray-500">${fileSize} MB • ${fileExt.toUpperCase()}</p>
            <p class="text-xs text-gray-400">Modified: ${lastModified}</p>
          </div>
          
          <!-- Selection Indicator -->
          <div class="selection-indicator absolute top-2 right-2 w-6 h-6 rounded-full border-2 border-white shadow-lg bg-gray-300 opacity-0 transition-all duration-200 flex items-center justify-center hover:bg-gray-400" onclick="event.stopPropagation(); toggleFileSelection(${index})" title="Click to select/deselect file">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
          </div>
          
          <!-- Remove File Button -->
          <div class="remove-file-btn absolute top-2 left-2 w-6 h-6 rounded-full border-2 border-white shadow-lg bg-red-500 opacity-0 hover:opacity-100 transition-all duration-200 flex items-center justify-center hover:bg-red-600 cursor-pointer" onclick="event.stopPropagation(); removeFile(${index})" title="Remove file from list">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
        </div>
      `;
      
      // Generate thumbnail after creating the card
      setTimeout(() => generateThumbnail(file, index), 100);
      
      return fileCard;
    }
    
    // Get file type information for styling
    function getFileTypeInfo(extension) {
      const fileTypes = {
        'pdf': {
          icon: '<svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12a2 2 0 002-2V8a2 2 0 00-2-2h-3l-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
          bgColor: 'bg-red-100'
        },
        'jpg': {
          icon: '<svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg>',
          bgColor: 'bg-green-100'
        },
        'jpeg': {
          icon: '<svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg>',
          bgColor: 'bg-green-100'
        },
        'png': {
          icon: '<svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg>',
          bgColor: 'bg-blue-100'
        },
        'tiff': {
          icon: '<svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg>',
          bgColor: 'bg-purple-100'
        },
        'tif': {
          icon: '<svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg>',
          bgColor: 'bg-purple-100'
        }
      };
      
      return fileTypes[extension] || {
        icon: '<svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4z"/></svg>',
        bgColor: 'bg-gray-100'
      };
    }
    
    // Add event listeners for upload button and keyboard navigation
    document.addEventListener('DOMContentLoaded', function() {
      const uploadButton = document.querySelector('.bg-gray-600.hover\\:bg-gray-700');
      if (uploadButton) {
        uploadButton.addEventListener('click', showUploadModal);
      }
      
      // Keyboard navigation for preview modal
      document.addEventListener('keydown', function(e) {
        const previewModal = document.getElementById('previewModal');
        if (!previewModal.classList.contains('hidden')) {
          switch(e.key) {
            case 'Escape':
              closePreviewModal();
              break;
            case 'ArrowLeft':
              navigatePreview(-1);
              break;
            case 'ArrowRight':
              navigatePreview(1);
              break;
            case 'Enter':
            case ' ':
              e.preventDefault();
              selectForProcessing();
              break;
          }
        }
      });
    });
    
    // Enhanced file browsing with file selection
    function browseForFiles() {
      const folderSelect = document.getElementById('folderTypeSelect');
      const selectedType = folderSelect.value;
      
      if (!selectedType) {
        alert('Please select a document type first:\n\n📄 A4 Documents - For standard letter-size documents\n📋 A3 Documents - For larger format documents\n📁 All Documents - For mixed document sizes');
        return;
      }
      
      // Trigger file input for file selection
      const folderInput = document.getElementById('folderInput');
      folderInput.click();
    }
    
    // Clear selection functionality
    function clearFileSelection() {
      const defaultMessage = document.getElementById('defaultMessage');
      const fileListArea = document.getElementById('fileListArea');
      
      // Show default message and hide file list
      defaultMessage.classList.remove('hidden');
      fileListArea.classList.add('hidden');
      
      // Clear selected files
      selectedFiles = [];
      currentPreviewFile = null;
      
      // Reset folder select
      document.getElementById('folderTypeSelect').value = '';
      updateFolderSelection();
      
      // Reset file input
      const folderInput = document.getElementById('folderInput');
      if (folderInput) {
        folderInput.value = '';
      }
      
      // Update counts
      updateSelectedCount();
      
      showNotification('File selection cleared', 'info');
    }
    
    // Add clear button functionality to the preview section
    function addClearButton() {
      const previewHeader = document.querySelector('#previewArea').closest('.bg-white').querySelector('.flex.items-center.justify-between');
      if (previewHeader && !previewHeader.querySelector('#clearSelectionBtn')) {
        const clearBtn = document.createElement('button');
        clearBtn.id = 'clearSelectionBtn';
        clearBtn.className = 'bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition duration-200 ml-2';
        clearBtn.innerHTML = `
          <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
          Clear Selection
        `;
        clearBtn.onclick = clearFileSelection;
        
        const buttonContainer = previewHeader.querySelector('div:last-child');
        buttonContainer.appendChild(clearBtn);
      }
    }
    
    // Process selected files
    function processSelectedFiles() {
      const selectedCards = document.querySelectorAll('#fileGrid > div.ring-green-500');
      
      if (selectedCards.length === 0) {
        alert('No files selected for processing');
        return;
      }
      
      // Get selected file information
      const selectedFilesList = [];
      selectedCards.forEach((card, index) => {
        const fileIndex = Array.from(card.parentNode.children).indexOf(card);
        if (selectedFiles[fileIndex]) {
          selectedFilesList.push(selectedFiles[fileIndex]);
        }
      });
      
      // Show processing confirmation
      const fileNames = selectedFilesList.map(file => file.name).join('\n• ');
      const confirmMessage = `Process ${selectedFilesList.length} selected files?\n\nFiles to process:\n• ${fileNames}\n\nThis will start the document processing workflow.`;
      
      if (confirm(confirmMessage)) {
        // Start processing
        startFileProcessing(selectedFilesList);
      }
    }
    
    // Start file processing workflow
    function startFileProcessing(filesList) {
      // Show processing notification
      showNotification(`Starting processing of ${filesList.length} files...`, 'info');
      
      // Simulate processing workflow
      const processingSteps = [
        'Initializing document scanner...',
        'Validating file formats...',
        'Creating batch folder...',
        'Processing documents...',
        'Generating thumbnails...',
        'Updating file registry...',
        'Processing complete!'
      ];
      
      let currentStep = 0;
      
      const processStep = () => {
        if (currentStep < processingSteps.length) {
          showNotification(processingSteps[currentStep], currentStep === processingSteps.length - 1 ? 'success' : 'info');
          currentStep++;
          
          if (currentStep < processingSteps.length) {
            setTimeout(processStep, 1500); // Wait 1.5 seconds between steps
          } else {
            // Processing complete
            setTimeout(() => {
              showProcessingComplete(filesList);
            }, 2000);
          }
        }
      };
      
      // Start the processing simulation
      setTimeout(processStep, 500);
    }
    
    // Show processing complete dialog
    function showProcessingComplete(filesList) {
      const message = `✅ Processing Complete!\n\n${filesList.length} files have been successfully processed and are ready for the next stage.\n\nFiles processed:\n${filesList.map(f => '• ' + f.name).join('\n')}\n\nNext: Files are now available in the PageTyping workflow.`;
      
      alert(message);
      
      // Optionally clear the selection after processing
      if (confirm('Clear file selection and reset for next batch?')) {
        clearFileSelection();
      }
    }
    
    // File preview functionality
    function previewFile(fileIndex) {
      if (!selectedFiles || selectedFiles.length === 0) {
        alert('No files available for preview');
        return;
      }
      
      const file = selectedFiles[fileIndex];
      if (!file) {
        alert('File not found');
        return;
      }
      
      currentPreviewFile = { file, index: fileIndex };
      
      // Update modal content
      document.getElementById('previewFileName').textContent = file.name;
      document.getElementById('fileNavigation').textContent = `${fileIndex + 1} / ${selectedFiles.length}`;
      
      // Update navigation buttons
      document.getElementById('prevFileBtn').disabled = fileIndex === 0;
      document.getElementById('nextFileBtn').disabled = fileIndex === selectedFiles.length - 1;
      
      // Update file info
      const fileSize = (file.size / (1024 * 1024)).toFixed(2);
      const fileExt = file.name.toLowerCase().split('.').pop();
      const lastModified = new Date(file.lastModified).toLocaleDateString();
      
      document.getElementById('fileInfo').innerHTML = `
        <div class="space-y-1">
          <div><strong>Size:</strong> ${fileSize} MB</div>
          <div><strong>Type:</strong> ${fileExt.toUpperCase()}</div>
          <div><strong>Modified:</strong> ${lastModified}</div>
        </div>
      `;
      
      // Load and display file content
      loadFilePreview(file);
      
      // Show modal
      document.getElementById('previewModal').classList.remove('hidden');
    }
    
    // Load file preview content
    function loadFilePreview(file) {
      const previewContent = document.getElementById('previewContent');
      const fileExt = file.name.toLowerCase().split('.').pop();
      
      if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(fileExt)) {
        // Image preview
        const reader = new FileReader();
        reader.onload = function(e) {
          previewContent.innerHTML = `
            <div class="w-full max-w-2xl mx-auto">
              <img src="${e.target.result}" alt="${file.name}" 
                   class="w-full h-auto max-h-80 object-contain rounded-lg shadow-md">
            </div>
          `;
        };
        reader.readAsDataURL(file);
      } else if (fileExt === 'pdf') {
        // PDF preview placeholder
        previewContent.innerHTML = `
          <div class="text-center p-8">
            <div class="bg-red-100 p-4 rounded-lg inline-block mb-4">
              <svg class="w-16 h-16 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 18h12a2 2 0 002-2V8a2 2 0 00-2-2h-3l-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">PDF Document</h3>
            <p class="text-gray-600 mb-4">${file.name}</p>
            <p class="text-sm text-gray-500">PDF preview requires a PDF viewer.<br>File is ready for processing.</p>
          </div>
        `;
      } else if (['tiff', 'tif'].includes(fileExt)) {
        // TIFF preview placeholder
        previewContent.innerHTML = `
          <div class="text-center p-8">
            <div class="bg-purple-100 p-4 rounded-lg inline-block mb-4">
              <svg class="w-16 h-16 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">TIFF Document</h3>
            <p class="text-gray-600 mb-4">${file.name}</p>
            <p class="text-sm text-gray-500">High-quality scan document.<br>File is ready for processing.</p>
          </div>
        `;
      } else {
        // Generic file preview
        previewContent.innerHTML = `
          <div class="text-center p-8">
            <div class="bg-gray-100 p-4 rounded-lg inline-block mb-4">
              <svg class="w-16 h-16 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Document File</h3>
            <p class="text-gray-600 mb-4">${file.name}</p>
            <p class="text-sm text-gray-500">File type: ${fileExt.toUpperCase()}<br>File is ready for processing.</p>
          </div>
        `;
      }
    }
    
    // Navigate preview (previous/next)
    function navigatePreview(direction) {
      if (!currentPreviewFile) return;
      
      const newIndex = currentPreviewFile.index + direction;
      if (newIndex >= 0 && newIndex < selectedFiles.length) {
        previewFile(newIndex);
      }
    }
    
    // Close preview modal
    function closePreviewModal() {
      document.getElementById('previewModal').classList.add('hidden');
      currentPreviewFile = null;
    }
    
    // Select file for processing
    function selectForProcessing() {
      if (!currentPreviewFile) return;
      
      const file = currentPreviewFile.file;
      
      // Add visual feedback to the selected file
      const fileCards = document.querySelectorAll('#fileGrid > div');
      if (fileCards[currentPreviewFile.index]) {
        const fileCard = fileCards[currentPreviewFile.index];
        fileCard.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
        
        // Add a selected badge
        const badge = document.createElement('div');
        badge.className = 'absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full';
        badge.textContent = '✓ Selected';
        fileCard.style.position = 'relative';
        fileCard.appendChild(badge);
      }
      
      alert(`File "${file.name}" has been selected for processing!\n\nThis file will be processed in the current batch.`);
      closePreviewModal();
    }
    
    // Generate thumbnail for file
    function generateThumbnail(file, index) {
      const thumbnailContainer = document.getElementById(`thumbnail-${index}`);
      if (!thumbnailContainer) return;
      
      const fileExt = file.name.toLowerCase().split('.').pop();
      
      if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(fileExt)) {
        // Generate image thumbnail
        const reader = new FileReader();
        reader.onload = function(e) {
          thumbnailContainer.innerHTML = `
            <img src="${e.target.result}" alt="${file.name}" 
                 class="thumbnail-image w-full h-full object-cover rounded">
          `;
        };
        reader.readAsDataURL(file);
      } else if (fileExt === 'pdf') {
        // Generate PDF thumbnail from first page
        generatePDFThumbnail(file, thumbnailContainer);
      } else if (['tiff', 'tif'].includes(fileExt)) {
        // TIFF thumbnail placeholder
        thumbnailContainer.innerHTML = `
          <div class="text-center">
            <div class="bg-purple-100 p-2 rounded-lg inline-block mb-1">
              <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
              </svg>
            </div>
            <p class="text-xs text-gray-600 font-medium">TIFF</p>
          </div>
        `;
      }
    }
    
    // Toggle file selection (replaces separate select function)
    function toggleFileSelection(fileIndex) {
      if (!selectedFiles || !selectedFiles[fileIndex]) {
        console.log('File not found at index:', fileIndex); // Debug log
        return;
      }
      
      const file = selectedFiles[fileIndex];
      const fileCards = document.querySelectorAll('#fileGrid .file-card');
      
      console.log('Toggling selection for file:', file.name, 'at index:', fileIndex); // Debug log
      
      if (fileCards[fileIndex]) {
        const fileCard = fileCards[fileIndex];
        const selectionIndicator = fileCard.querySelector('.selection-indicator');
        
        // Check if already selected
        if (fileCard.classList.contains('ring-2')) {
          // Deselect
          fileCard.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
          if (selectionIndicator) {
            selectionIndicator.classList.remove('opacity-100', 'bg-green-500');
            selectionIndicator.classList.add('opacity-0', 'bg-gray-300');
          }
          console.log('File deselected:', file.name); // Debug log
        } else {
          // Select
          fileCard.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
          if (selectionIndicator) {
            selectionIndicator.classList.remove('opacity-0', 'bg-gray-300');
            selectionIndicator.classList.add('opacity-100', 'bg-green-500');
          }
          console.log('File selected:', file.name); // Debug log
        }
        
        // Update selected count
        updateSelectedCount();
      } else {
        console.log('File card not found at index:', fileIndex); // Debug log
      }
    }
    
    // Update selected file count
    function updateSelectedCount() {
      const selectedCards = document.querySelectorAll('#fileGrid .file-card.ring-green-500');
      const totalCards = document.querySelectorAll('#fileGrid .file-card');
      const selectedFileCount = document.getElementById('selectedFileCount');
      const processFilesBtn = document.getElementById('processFilesBtn');
      const processDisplayCount = document.getElementById('processDisplayCount');
      const processStatusText = document.getElementById('processStatusText');
      const selectionSummary = document.getElementById('selectionSummary');
      const selectAllBtn = document.getElementById('selectAllBtn');
      const deselectAllBtn = document.getElementById('deselectAllBtn');
      
      const count = selectedCards.length;
      const total = totalCards.length;
      
      console.log('Selected cards count:', count); // Debug log
      
      if (selectedFileCount) {
        selectedFileCount.textContent = count;
      }
      
      // Update selection summary
      if (selectionSummary) {
        selectionSummary.textContent = `${count} of ${total} files selected`;
      }
      
      // Update select/deselect all buttons
      if (selectAllBtn && deselectAllBtn) {
        if (count === 0) {
          selectAllBtn.disabled = false;
          deselectAllBtn.disabled = true;
          selectAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
          deselectAllBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else if (count === total) {
          selectAllBtn.disabled = true;
          deselectAllBtn.disabled = false;
          selectAllBtn.classList.add('opacity-50', 'cursor-not-allowed');
          deselectAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
          selectAllBtn.disabled = false;
          deselectAllBtn.disabled = false;
          selectAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
          deselectAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
      }
      
      // Update process display count
      if (processDisplayCount) {
        processDisplayCount.textContent = count;
      }
      
      // Update process button state
      if (processFilesBtn) {
        if (count > 0) {
          processFilesBtn.disabled = false;
          processFilesBtn.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
          processFilesBtn.classList.add('hover:bg-green-700');
          
          // Update status text
          if (processStatusText) {
            processStatusText.textContent = `${count} file${count === 1 ? '' : 's'} ready for processing`;
          }
        } else {
          processFilesBtn.disabled = true;
          processFilesBtn.classList.add('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
          processFilesBtn.classList.remove('hover:bg-green-700');
          
          // Update status text
          if (processStatusText) {
            processStatusText.textContent = 'No files selected';
          }
        }
      }
    }
    
    // Select all files
    function selectAllFiles() {
      const fileCards = document.querySelectorAll('#fileGrid .file-card');
      
      fileCards.forEach((fileCard, index) => {
        if (!fileCard.classList.contains('ring-2')) {
          const selectionIndicator = fileCard.querySelector('.selection-indicator');
          
          // Select the file
          fileCard.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
          if (selectionIndicator) {
            selectionIndicator.classList.remove('opacity-0', 'bg-gray-300');
            selectionIndicator.classList.add('opacity-100', 'bg-green-500');
          }
        }
      });
      
      updateSelectedCount();
      showNotification('All files selected', 'success');
    }
    
    // Deselect all files
    function deselectAllFiles() {
      const fileCards = document.querySelectorAll('#fileGrid .file-card');
      
      fileCards.forEach((fileCard, index) => {
        if (fileCard.classList.contains('ring-2')) {
          const selectionIndicator = fileCard.querySelector('.selection-indicator');
          
          // Deselect the file
          fileCard.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
          if (selectionIndicator) {
            selectionIndicator.classList.remove('opacity-100', 'bg-green-500');
            selectionIndicator.classList.add('opacity-0', 'bg-gray-300');
          }
        }
      });
      
      updateSelectedCount();
      showNotification('All files deselected', 'info');
    }
    
    // Remove file from list
    function removeFile(fileIndex) {
      if (!selectedFiles || !selectedFiles[fileIndex]) {
        console.log('File not found at index:', fileIndex);
        return;
      }
      
      const file = selectedFiles[fileIndex];
      const fileName = file.name;
      
      if (confirm(`Remove "${fileName}" from the file list?`)) {
        // Remove from selectedFiles array
        selectedFiles.splice(fileIndex, 1);
        
        // Regenerate the file grid
        const folderSelect = document.getElementById('folderTypeSelect');
        const selectedType = folderSelect.value;
        displaySelectedFiles(selectedFiles, selectedType);
        
        showNotification(`File "${fileName}" removed from list`, 'info');
      }
    }
    
    // Move file to PageTyping functionality
    function moveToPageTyping(fileName) {
      if (confirm(`Move ${fileName} to PageTyping folder?`)) {
        alert(`File ${fileName} moved to PageTyping successfully!`);
        // Refresh the file list
      }
    }
    
    // Generate PDF thumbnail from first page
    function generatePDFThumbnail(file, thumbnailContainer) {
      const loadingHtml = `
        <div class="text-center">
          <div class="bg-red-100 p-2 rounded-lg inline-block mb-1">
            <div class="animate-pulse w-8 h-8 bg-red-200 rounded"></div>
          </div>
          <p class="text-xs text-gray-600 font-medium">Loading PDF...</p>
        </div>
      `;
      
      thumbnailContainer.innerHTML = loadingHtml;
      
      const fileReader = new FileReader();
      
      fileReader.onload = function(e) {
        const typedarray = new Uint8Array(e.target.result);
        
        // Load PDF
        pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
          // Get first page
          pdf.getPage(1).then(function(page) {
            const scale = 0.5;
            const viewport = page.getViewport({scale: scale});
            
            // Create canvas
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            // Set max dimensions
            const maxWidth = 80;
            const maxHeight = 80;
            
            if (canvas.width > maxWidth || canvas.height > maxHeight) {
              const scaleX = maxWidth / canvas.width;
              const scaleY = maxHeight / canvas.height;
              const finalScale = Math.min(scaleX, scaleY);
              
              canvas.width = canvas.width * finalScale;
              canvas.height = canvas.height * finalScale;
              
              const scaledViewport = page.getViewport({scale: scale * finalScale});
              canvas.height = scaledViewport.height;
              canvas.width = scaledViewport.width;
            }
            
            // Render PDF page
            const renderContext = {
              canvasContext: context,
              viewport: page.getViewport({scale: scale * (canvas.width / viewport.width)})
            };
            
            page.render(renderContext).promise.then(function() {
              // Clear loading and show canvas
              thumbnailContainer.innerHTML = '';
              canvas.className = 'rounded shadow-sm';
              thumbnailContainer.appendChild(canvas);
            });
          });
        }).catch(function(error) {
          console.error('Error loading PDF:', error);
          // Fallback to icon
          thumbnailContainer.innerHTML = `
            <div class="text-center">
              <div class="bg-red-100 p-2 rounded-lg inline-block mb-1">
                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4 18h12a2 2 0 002-2V8a2 2 0 00-2-2h-3l-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <p class="text-xs text-gray-600 font-medium">PDF</p>
            </div>
          `;
        });
      };
      
      fileReader.readAsArrayBuffer(file);
    }
  
 </script>