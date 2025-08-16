<!-- Step 6: Document Uploads -->
<div id="step-content-6" class="step-content hidden">
    <div class="bg-white border border-gray-200 rounded-lg">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i data-lucide="upload" class="h-5 w-5"></i>
                SECTION D: DOCUMENT UPLOADS
            </h3>
        </div>
        <div class="p-4 space-y-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-900 mb-2">Required Documents</h4>
                <p class="text-sm text-blue-800">Please upload the following documents as applicable to your application. All documents should be clear, legible, and in PDF, JPG, or PNG format (Max: 5MB each).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Right of Occupancy -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="rightOfOccupancy" name="rightOfOccupancy" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(a) Right of Occupancy</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="rightOfOccupancy">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                </div>

                <!-- Certificate of Occupancy -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="certificateOfOccupancy" name="certificateOfOccupancy" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(b) Certificate of Occupancy</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="certificateOfOccupancy">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                </div>

                <!-- Deed of Assignment -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="deedOfAssignment" name="deedOfAssignment" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(c) Deed of Assignment</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="deedOfAssignment">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                </div>

                <!-- Deed of Sublease -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="deedOfSublease" name="deedOfSublease" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(d) Deed of Sublease</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="deedOfSublease">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                </div>

                <!-- Deed of Mortgage -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="deedOfMortgage" name="deedOfMortgage" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(e) Deed of Mortgage</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="deedOfMortgage">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                </div>

                <!-- Deed of Gift -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="deedOfGift" name="deedOfGift" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(f) Deed of Gift</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="deedOfGift">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                </div>

                <!-- Power of Attorney -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="powerOfAttorney" name="powerOfAttorney" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(g) Power of Attorney</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="powerOfAttorney">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                </div>

                <!-- Devolution Order -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="devolutionOrder" name="devolutionOrder" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(h) Devolution Order</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="devolutionOrder">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                </div>

                <!-- Letter of Administration -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="letterOfAdministration" name="letterOfAdministration" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(i) Letter of Administration</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="letterOfAdministration">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                </div>

                <!-- Others -->
                <div class="document-upload-section">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="otherDocuments" name="otherDocuments" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                        <i data-lucide="file-text" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                        <div class="text-sm font-semibold mb-2">(j) Others......</div>
                        <button type="button" class="upload-btn inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700" data-target="otherDocuments">
                            <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                            Choose File
                        </button>
                        <div class="file-info mt-2 text-sm text-gray-600 hidden">
                            <div class="file-name font-medium text-xs"></div>
                            <div class="file-size text-xs text-gray-500"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                    
                    <!-- Description field for other documents -->
                    <div class="mt-3">
                        <label for="otherDocumentsDescription" class="block text-sm font-medium text-gray-700 mb-1">
                            Description (if Others selected)
                        </label>
                        <input
                            type="text"
                            id="otherDocumentsDescription"
                            name="otherDocumentsDescription"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                            placeholder="Describe the document type"
                        />
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 p-4 rounded-lg">
                <h4 class="font-semibold text-yellow-900 mb-2 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="h-4 w-4"></i>
                    Important Notes
                </h4>
                <ul class="text-sm text-yellow-800 space-y-1">
                    <li>• All documents are optional but may be required based on your specific case</li>
                    <li>• Documents should be clear, legible, and properly scanned</li>
                    <li>• Accepted formats: PDF, JPG, PNG (Maximum file size: 5MB each)</li>
                    <li>• Original documents may be requested during verification</li>
                    <li>• Ensure all uploaded documents are relevant to your application</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup document upload handlers
    setupDocumentUploads();
});

function setupDocumentUploads() {
    console.log('Setting up document upload handlers...');
    
    // Get all upload buttons
    const uploadButtons = document.querySelectorAll('.upload-btn');
    
    uploadButtons.forEach(button => {
        const targetId = button.getAttribute('data-target');
        const fileInput = document.getElementById(targetId);
        const section = button.closest('.document-upload-section');
        const fileInfo = section.querySelector('.file-info');
        const fileName = section.querySelector('.file-name');
        const fileSize = section.querySelector('.file-size');
        
        if (fileInput) {
            // Button click handler
            button.addEventListener('click', () => {
                fileInput.click();
            });
            
            // File input change handler
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        showToast('Please select a PDF, JPG, or PNG file', 'error');
                        this.value = '';
                        return;
                    }
                    
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        showToast('File size must be less than 5MB', 'error');
                        this.value = '';
                        return;
                    }
                    
                    // Show file info
                    if (fileName) {
                        fileName.textContent = file.name;
                    }
                    if (fileSize) {
                        const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                        fileSize.textContent = `${sizeInMB} MB`;
                    }
                    if (fileInfo) {
                        fileInfo.classList.remove('hidden');
                    }
                    
                    // Update button text
                    button.innerHTML = `
                        <i data-lucide="check-circle" class="h-4 w-4 mr-1"></i>
                        File Selected
                    `;
                    button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    button.classList.add('bg-green-600', 'hover:bg-green-700');
                    
                    // Update border color
                    const uploadArea = section.querySelector('.border-dashed');
                    if (uploadArea) {
                        uploadArea.classList.remove('border-gray-300');
                        uploadArea.classList.add('border-green-400', 'bg-green-50');
                    }
                    
                    // Re-initialize Lucide icons
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                    
                    showToast(`${targetId} uploaded successfully`, 'success');
                } else {
                    // Reset if no file selected
                    if (fileInfo) {
                        fileInfo.classList.add('hidden');
                    }
                    
                    // Reset button text
                    button.innerHTML = `
                        <i data-lucide="upload" class="h-4 w-4 mr-1"></i>
                        Choose File
                    `;
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    
                    // Reset border color
                    const uploadArea = section.querySelector('.border-dashed');
                    if (uploadArea) {
                        uploadArea.classList.remove('border-green-400', 'bg-green-50');
                        uploadArea.classList.add('border-gray-300');
                    }
                    
                    // Re-initialize Lucide icons
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });
        }
    });
    
    console.log('Document upload handlers setup complete');
}

// Make showToast available if not already defined
if (typeof showToast === 'undefined') {
    function showToast(message, type = 'info') {
        console.log(`Toast: ${message} (${type})`);
    }
}
</script>