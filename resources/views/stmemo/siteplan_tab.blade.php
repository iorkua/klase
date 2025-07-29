<div class="tab-content hidden" id="notes-tab">
    <div class="mb-6">
        @php
            $sitePlanExists = DB::connection('sqlsrv')->table('site_plans')->where('application_id', $application->id)->exists();
            $sitePlanStatus = $sitePlanExists ? 'Uploaded' : 'Not Uploaded';
            // Get the uploaded file info if exists
            $sitePlanFile = null;
            if ($sitePlanExists) {
                $sitePlanFile = DB::connection('sqlsrv')->table('site_plans')->where('application_id', $application->id)->first();
            }
        @endphp
        
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Attach Site Plan File</h3> 
            <span id="site-plan-status-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sitePlanExists ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                <span id="site-plan-status-text">{{ $sitePlanStatus }}</span>
            </span>
        </div>
        
        {{-- Preview uploaded file if exists --}}
        @if($sitePlanExists && $sitePlanFile && $sitePlanFile->site_file)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Uploaded Site Plan Preview:</label>
                @php
                    $fileUrl = asset('storage/site_plans/' . $sitePlanFile->site_file);
                    $extension = strtolower(pathinfo($sitePlanFile->site_file, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($extension, ['jpg','jpeg','png']))
                    <img src="{{ $fileUrl }}" alt="Site Plan Image" class="max-w-xs rounded border border-gray-300 mb-2">
                @elseif($extension === 'pdf')
                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> View PDF
                    </a>
                @else
                    <a href="{{ $fileUrl }}" target="_blank" class="text-blue-600 underline">Download File</a>
                @endif
            </div>
        @endif
        
        <form id="sitePlanForm" action="{{ route('stmemo.saveSitePlan') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                 @csrf
                 <input type="hidden" name="application_id" value="{{ $application->id }}">
                 
                 <div class="grid grid-cols-1 gap-6">
                     <div style="display: none;">
                         <label for="property_location" class="block text-sm font-medium text-gray-700 mb-1">Property Location</label>
                         <textarea 
                             id="property_location" 
                             name="property_location" 
                             rows="2" 
                             class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ isset($existingSitePlan) ? $existingSitePlan->property_location : ($application->property_plot_no . ' ' . $application->property_street_name . ', ' . $application->property_lga) }}</textarea>
                         <p class="mt-1 text-sm text-gray-500">Full address of the property</p>
                     </div>
                     
                     <div>
              
                         <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 {{ $sitePlanExists ? 'border-gray-200 bg-gray-50' : 'border-gray-300' }} border-dashed rounded-md">
                             <div class="space-y-1 text-center">
                                 @if($sitePlanExists)
                                     <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                     </svg>
                                     <div class="text-sm text-gray-600">
                                         <p class="font-medium text-green-600">Site Plan Already Uploaded</p>
                                         <p class="text-xs text-gray-500 mt-1">Upload is disabled once a site plan has been uploaded</p>
                                     </div>
                                 @else
                                     <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                         <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                     </svg>
                                     <div class="flex text-sm text-gray-600">
                                         <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                             <span>Upload a file</span>
                                             <input id="file-upload" name="site_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                         </label>
                                         <p class="pl-1">or drag and drop</p>
                                     </div>
                                     <p class="text-xs text-gray-500">
                                         PDF, PNG, JPG up to 10MB
                                     </p>
                                 @endif
                             </div>
                         </div>
                         <div id="file-preview" class="mt-2 hidden">
                             <div class="p-2 bg-gray-50 rounded-md flex items-center justify-between">
                                 <div class="flex items-center">
                                     <i data-lucide="file" class="w-4 h-4 text-gray-400 mr-2"></i>
                                     <span id="file-name" class="text-sm text-gray-700"></span>
                                 </div>
                                 <button type="button" id="remove-file" class="text-red-500 hover:text-red-700">
                                     <i data-lucide="x" class="w-4 h-4"></i>
                                 </button>
                             </div>
                         </div>
                     </div>
                 </div>
                 
                 <div class="flex justify-end pt-5 border-t border-gray-200">
                     <div id="loading-indicator" class="hidden mr-3">
                         <div class="flex items-center">
                             <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                             </svg>
                             <span class="text-sm text-gray-500">Uploading...</span>
                         </div>
                     </div>
                     <button id="submit-btn" type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white {{ $sitePlanExists ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" {{ $sitePlanExists ? 'disabled' : '' }}>
                         {{ $sitePlanExists ? 'Site Plan Already Uploaded' : (isset($existingSitePlan) ? 'Update' : 'Upload') }} Site Plan
                     </button>
                 </div>
             </form>
             
             <div id="status-message" class="mt-4 hidden">
                 <div id="success-message" class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg hidden" role="alert"></div>
                 <div id="error-message" class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg hidden" role="alert"></div>
             </div>
    </div>
</div>
<script>
 document.addEventListener('DOMContentLoaded', function() {
     const fileInput = document.getElementById('file-upload');
     const filePreview = document.getElementById('file-preview');
     const fileName = document.getElementById('file-name');
     const removeFileBtn = document.getElementById('remove-file');
     const sitePlanForm = document.getElementById('sitePlanForm');
     const loadingIndicator = document.getElementById('loading-indicator');
     const submitBtn = document.getElementById('submit-btn');
     const successMessage = document.getElementById('success-message');
     const errorMessage = document.getElementById('error-message');
     const statusMessage = document.getElementById('status-message');
     
     // File input handling
     fileInput.addEventListener('change', function(e) {
         if (fileInput.files.length > 0) {
             const file = fileInput.files[0];
             fileName.textContent = file.name;
             // Preview logic
             const previewContainer = filePreview.querySelector('.flex.items-center');
             // Remove previous preview if any
             const oldPreview = filePreview.querySelector('.file-preview-content');
             if (oldPreview) oldPreview.remove();

             const ext = file.name.split('.').pop().toLowerCase();
             let previewElem;
             if (['jpg','jpeg','png'].includes(ext)) {
                 previewElem = document.createElement('img');
                 previewElem.className = 'file-preview-content max-h-32 ml-2 rounded border border-gray-300';
                 previewElem.style.maxWidth = '120px';
                 previewElem.alt = 'Preview';
                 const reader = new FileReader();
                 reader.onload = function(e) {
                     previewElem.src = e.target.result;
                 };
                 reader.readAsDataURL(file);
                 previewContainer.appendChild(previewElem);
             } else if (ext === 'pdf') {
                 previewElem = document.createElement('a');
                 previewElem.className = 'file-preview-content ml-2 px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs';
                 previewElem.textContent = 'Preview PDF';
                 previewElem.target = '_blank';
                 // Create a blob URL for preview
                 const reader = new FileReader();
                 reader.onload = function(e) {
                     previewElem.href = e.target.result;
                 };
                 reader.readAsDataURL(file);
                 previewContainer.appendChild(previewElem);
             }
             filePreview.classList.remove('hidden');
         } else {
             filePreview.classList.add('hidden');
             // Remove preview if any
             const oldPreview = filePreview.querySelector('.file-preview-content');
             if (oldPreview) oldPreview.remove();
         }
     });

     removeFileBtn.addEventListener('click', function() {
         fileInput.value = '';
         filePreview.classList.add('hidden');
         // Remove preview if any
         const oldPreview = filePreview.querySelector('.file-preview-content');
         if (oldPreview) oldPreview.remove();
     });
     
     // File drag and drop support
     const dropZone = document.querySelector('.border-dashed');
     
     ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
         dropZone.addEventListener(eventName, preventDefaults, false);
     });
     
     function preventDefaults(e) {
         e.preventDefault();
         e.stopPropagation();
     }
     
     ['dragenter', 'dragover'].forEach(eventName => {
         dropZone.addEventListener(eventName, highlight, false);
     });
     
     ['dragleave', 'drop'].forEach(eventName => {
         dropZone.addEventListener(eventName, unhighlight, false);
     });
     
     function highlight() {
         dropZone.classList.add('bg-blue-50');
         dropZone.classList.add('border-blue-300');
     }
     
     function unhighlight() {
         dropZone.classList.remove('bg-blue-50');
         dropZone.classList.remove('border-blue-300');
     }
     
     dropZone.addEventListener('drop', handleDrop, false);
     
     function handleDrop(e) {
         const dt = e.dataTransfer;
         const files = dt.files;
         
         if (files.length > 0) {
             fileInput.files = files;
             fileName.textContent = files[0].name;
             filePreview.classList.remove('hidden');
         }
     }
     
     // Form submission with Ajax
     sitePlanForm.addEventListener('submit', function(e) {
         e.preventDefault();
         
         // Check if site plan already exists and prevent submission
         @if($sitePlanExists)
             statusMessage.classList.remove('hidden');
             errorMessage.classList.remove('hidden');
             errorMessage.textContent = 'Site plan has already been uploaded. Upload is disabled.';
             return;
         @endif
         
         // Validate file is selected
         if (fileInput.files.length === 0) {
             statusMessage.classList.remove('hidden');
             errorMessage.classList.remove('hidden');
             errorMessage.textContent = 'Please select a site plan file to upload';
             return;
         }
         
         // Show loading indicator
         loadingIndicator.classList.remove('hidden');
         submitBtn.disabled = true;
         successMessage.classList.add('hidden');
         errorMessage.classList.add('hidden');
         
         // Create FormData object
         const formData = new FormData(sitePlanForm);
         
         // Send Ajax request
         fetch(sitePlanForm.action, {
             method: 'POST',
             body: formData,
             headers: {
                 'X-Requested-With': 'XMLHttpRequest'
             }
         })
         .then(response => response.json())
         .then(data => {
             // Hide loading indicator
             loadingIndicator.classList.add('hidden');
             submitBtn.disabled = false;
             statusMessage.classList.remove('hidden');
             
             if (data.success) {
                 // Show success message
                 successMessage.classList.remove('hidden');
                 successMessage.textContent = data.message || 'Site plan uploaded successfully';
                 
                 // Reset form if needed
                 if (!data.update) {
                     fileInput.value = '';
                     filePreview.classList.add('hidden');
                 }
                 
                 // Update status badge with specific ID selectors
                 const statusBadge = document.getElementById('site-plan-status-badge');
                 const statusText = document.getElementById('site-plan-status-text');
                 
                 if (statusBadge) {
                     statusBadge.classList.remove('bg-red-100', 'text-red-800');
                     statusBadge.classList.add('bg-green-100', 'text-green-800');
                 }
                 
                 if (statusText) {
                     statusText.textContent = 'Uploaded';
                 }
                 
                 // Disable upload functionality after successful upload
                 const dropZone = document.querySelector('.border-dashed');
                 const submitButton = document.getElementById('submit-btn');
                 
                 if (dropZone) {
                     // Update drop zone to show "already uploaded" state
                     dropZone.classList.remove('border-gray-300');
                     dropZone.classList.add('border-gray-200', 'bg-gray-50');
                     
                     dropZone.innerHTML = `
                         <div class="space-y-1 text-center">
                             <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                             </svg>
                             <div class="text-sm text-gray-600">
                                 <p class="font-medium text-green-600">Site Plan Already Uploaded</p>
                                 <p class="text-xs text-gray-500 mt-1">Upload is disabled once a site plan has been uploaded</p>
                             </div>
                         </div>
                     `;
                 }
                 
                 if (submitButton) {
                     // Disable and update submit button
                     submitButton.disabled = true;
                     submitButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                     submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                     submitButton.textContent = 'Site Plan Already Uploaded';
                 }
                 
                 // Remove file input event listeners to prevent further uploads
                 const newFileInput = fileInput.cloneNode(true);
                 fileInput.parentNode.replaceChild(newFileInput, fileInput);
                 
                 // Update status in the parent table if we're in the generate form
                 try {
                     // Get application ID
                     const applicationId = document.querySelector('input[name="application_id"]').value;
                     
                     // Find the parent window table row for this application
                     if (window.opener && !window.opener.closed) {
                         const parentDoc = window.opener.document;
                         const appRow = parentDoc.querySelector(`tr[data-status="not-uploaded"][data-application-id="${applicationId}"]`);
                         if (appRow) {
                             // Update the status
                             appRow.setAttribute('data-status', 'uploaded');
                             
                             // Find and update the status cell
                             const statusCell = appRow.querySelector('td:nth-child(9)'); // Adjust index if needed
                             if (statusCell) {
                                 statusCell.innerHTML = '<span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Uploaded</span>';
                             }
                         }
                     } else {
                         // Try in the same window (if using tabs rather than popup)
                         const parentTable = window.parent.document.getElementById('applications-table');
                         if (parentTable) {
                             const rows = parentTable.querySelectorAll(`tr.application-row`);
                             rows.forEach(row => {
                                 // Find the row with application ID (might be in a data attribute or within a cell)
                                 // This is a simplified example - adjust based on your actual table structure
                                 if (row.innerHTML.includes(`ST-2025-0${applicationId}`)) {
                                     row.setAttribute('data-status', 'uploaded');
                                     const statusCell = row.querySelector('td:nth-child(9)'); // Adjust index if needed
                                     if (statusCell) {
                                         statusCell.innerHTML = '<span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Uploaded</span>';
                                     }
                                 }
                             });
                         }
                     }
                 } catch (err) {
                     console.error('Error updating parent table:', err);
                     // Non-critical error, don't show to user as upload was successful
                 }
             } else {
                 // Show error message
                 errorMessage.classList.remove('hidden');
                 errorMessage.textContent = data.message || 'Error uploading site plan';
             }
         })
         .catch(error => {
             // Hide loading indicator
             loadingIndicator.classList.add('hidden');
             submitBtn.disabled = false;
             statusMessage.classList.remove('hidden');
             
             // Show error message
             errorMessage.classList.remove('hidden');
             errorMessage.textContent = 'An unexpected error occurred. Please try again.';
             console.error('Error:', error);
         });
     });
 });
</script>