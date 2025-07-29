<div id="cofoDetailsModal" class="fixed inset-0 z-[1000] hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h5 class="text-xl font-semibold">Enter Certificate of Occupancy (CofO) Details</h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeCofoDetailsModal()">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <div class="px-6 py-4">
            <form id="cofoDetailsForm">
                @csrf
                <input type="hidden" id="cofoApplicationId" name="application_id" value="">
                
                <!-- Instrument Details Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">🧾 Instrument Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instrument Type</label>
                            <input type="text" id="cofoTransactionType" name="transaction_type" value="Certificate of Occupancy" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Certificate Date</label>
                            <input type="date" id="cofoCertificateDate" name="certificate_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Registration Number Components Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">🧮 Registration Number Components</h3>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Serial No.</label>
                            <input type="number" id="cofoSerialNo" name="serial_no" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 1" oninput="updateRegistrationPreview()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Page No.</label>
                            <input type="number" id="cofoPageNo" name="page_no" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 1" oninput="updateRegistrationPreview()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Volume No.</label>
                            <input type="number" id="cofoVolumeNo" name="volume_no" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 2" oninput="updateRegistrationPreview()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Date</label>
                            <input type="date" id="cofoTransactionDate" name="transaction_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Time</label>
                            <input type="time" id="cofoTransactionTime" name="transaction_time" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number Preview</label>
                            <input type="text" id="cofoRegNoPreview" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly placeholder="Serial/Page/Volume">
                        </div>
                    </div>
                </div>

                <!-- Land Use & Tenure Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">🏷 Land Use & Tenure</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Land Use</label>
                            <input type="text" id="cofoLandUse" name="land_use" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Period / Tenancy </label>
                            <input type="number" id="cofoPeriod" name="period" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 99">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                            <select id="cofoPeriodUnit" name="period_unit" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="years">Years</option>
                                <option value="months">Months</option>
                                <option value="weeks">Weeks</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Transaction Parties Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">🔁 Transaction Parties</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grantor</label>
                            <input type="text" id="cofoGrantor" name="grantor" value="KANO STATE GOVERNMENT" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grantee</label>
                            <input type="text" id="cofoGrantee" name="grantee" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 uppercase" readonly>
                        </div>
                    </div>
                </div>

                <!-- Property Description Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">✏️ Property Description</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PROPERTY DESCRIPTION</label>
                            <textarea id="cofoPropertyDescription" name="property_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100 uppercase" placeholder="FULL ADDRESS OF THE PROPERTY WILL BE AUTO-GENERATED..." readonly></textarea>
                            <small class="text-gray-500 text-xs mt-1">This will be auto-populated with the full address based on the property details.</small>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center bg-gray-50 px-6 py-4 rounded-b border-t">
                    <button type="button" onclick="closeCofoDetailsModal()" class="flex items-center space-x-2 px-6 py-2 bg-gray-500 text-white rounded-md shadow hover:bg-gray-600 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Cancel</span>
                    </button>
                    <button type="submit" id="submitCofoDetails" class="flex items-center space-x-2 px-6 py-2 bg-green-500 text-white rounded-md shadow hover:bg-green-600 transition-colors">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Submit CofO Details</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to open CofO Details modal
    function openCofoDetailsModal(applicationId, fileNo, npFileNo, applicantType, applicantData, propertyData) {
        // Set the application id
        document.getElementById('cofoApplicationId').value = applicationId;
        
        // Process applicant name based on type
        let applicantName = '';
        
        if(applicantType === 'individual' && applicantData) {
            const { applicant_title, first_name, middle_name, surname } = applicantData;
            applicantName = [applicant_title, first_name, middle_name, surname].filter(Boolean).join(' ');
        } 
        else if(applicantType === 'corporate' && applicantData) {
            applicantName = applicantData.corporate_name || '';
        }
        else if(applicantType === 'multiple' && applicantData) {
            try {
                if(typeof applicantData === 'string') {
                    const namesArray = JSON.parse(applicantData);
                    applicantName = Array.isArray(namesArray) ? namesArray.join(', ') : applicantData;
                } else if(Array.isArray(applicantData)) {
                    applicantName = applicantData.join(', ');
                }
            } catch(e) {
                applicantName = applicantData.toString();
            }
        }
        
        // Set grantee name
        document.getElementById('cofoGrantee').value = applicantName;
        
        // Set land use
        document.getElementById('cofoLandUse').value = propertyData.land_use || '';
        
        // Generate full property description based on available data
        let propertyDescription = '';
        // Format: Plot/House No, Streetname, District, LGA and State
        const addressParts = [
            propertyData.property_house_no,
            propertyData.property_street_name,
            propertyData.property_district, 
            propertyData.property_lga,
            (propertyData.property_state || 'Kano') + ' State'
        ].filter(Boolean);
        
        propertyDescription = addressParts.join(', ');
        
        // Set the property description
        document.getElementById('cofoPropertyDescription').value = propertyDescription;
        
        // Show the modal
        document.getElementById('cofoDetailsModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        }
    // Function to close CofO Details modal
    function closeCofoDetailsModal() {
        document.getElementById('cofoDetailsModal').classList.add('hidden');
        document.body.style.overflow = '';
        // Reset form
        document.getElementById('cofoDetailsForm').reset();
        document.getElementById('cofoRegNoPreview').value = '';
        // Reset transaction type to default
        document.getElementById('cofoTransactionType').value = 'Certificate of Occupancy';
    }
    
    // Function to update registration number preview
    function updateRegistrationPreview() {
        const serialNo = document.getElementById('cofoSerialNo').value;
        const pageNo = document.getElementById('cofoPageNo').value;
        const volumeNo = document.getElementById('cofoVolumeNo').value;
        
        if (serialNo && pageNo && volumeNo) {
            document.getElementById('cofoRegNoPreview').value = `${serialNo}/${pageNo}/${volumeNo}`;
        } else {
            document.getElementById('cofoRegNoPreview').value = '';
        }
    }
    
    // Close modal when clicking outside the modal content
    document.addEventListener('mousedown', function(event) {
        const modal = document.getElementById('cofoDetailsModal');
        if (!modal.classList.contains('hidden')) {
            const modalContent = modal.querySelector('div.bg-white');
            if (modal && !modalContent.contains(event.target)) {
                closeCofoDetailsModal();
            }
        }
    });
    
    // Handle form submission
    document.getElementById('cofoDetailsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        
        // Add the registration number to form data
        const regNo = document.getElementById('cofoRegNoPreview').value;
        if (regNo) {
            formData.append('reg_no', regNo);
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitCofoDetails');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Saving...</span>';
        submitBtn.disabled = true;
        
        // Submit via AJAX
        fetch('/sectionaltitling/save-cofo-details', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'CofO details saved successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    closeCofoDetailsModal();
                    // Optionally reload the page or update the UI
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to save CofO details',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while saving CofO details',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Initialize the form when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Set default transaction type
        document.getElementById('cofoTransactionType').value = 'Certificate of Occupancy';
    });
</script>