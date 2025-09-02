<!-- New FileIndexDialog -->
<div class="dialog-overlay hidden" id="new-file-dialog-overlay">
    <div class="dialog">
        <div class="dialog-header">
            <div class="dialog-title">
                <i data-lucide="file-plus" class="h-5 w-5"></i>
                Create New File Index
            </div>
            <button id="close-dialog-btn" class="text-white" style="background: none; border: none; cursor: pointer;">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="dialog-description">
            Enter the details for the new file to be indexed
        </div>
        <div class="dialog-content">
            <form id="new-file-form">
                <!-- File Identification Section -->
                <div class="form-section">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="form-section-title" style="margin-bottom: 0;">File Identification</h3>
                        <div class="tracking-id-container" style="text-align: right;">
                            <label class="form-label" style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Tracking ID</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                  @php
                                function generateTrackingId() {
                                    $segment1 = '';
                                    $segment2 = '';
                                    $characters = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
                                    
                                    // Generate first segment (8 characters)
                                    for ($i = 0; $i < 8; $i++) {
                                        $segment1 .= $characters[rand(0, strlen($characters) - 1)];
                                    }
                                    
                                    // Generate second segment (5 characters)
                                    for ($i = 0; $i < 5; $i++) {
                                        $segment2 .= $characters[rand(0, strlen($characters) - 1)];
                                    }
                                    
                                    return "TRK-{$segment1}-{$segment2}";
                                }
                                $trackingId = generateTrackingId();
                                @endphp
                                <input type="text"   class="input" readonly 
                                       style="width: 180px; font-size: 0.75rem; background-color: #f9fafb; font-family: monospace; cursor: default;"
                                        value="{{ $trackingId }}">
                                 
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="file-number" class="form-label required">File Number</label>
                        
                        <!-- Use the Smart File Number Selector Component -->
                        @include('components.smart_fileno_selector_indexing')
                    </div>
                    
                    <div class="form-group">
                        <label for="file-title" class="form-label required">File Title</label>
                        <input type="text" id="file-title" class="input" placeholder="e.g. John Doe's Property">
                    </div>
                </div>
                
                <!-- Property Details Section -->
                <div class="form-section">
                    <h3 class="form-section-title">Property Details</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Land Use Type</label>
                            <select class="input" id="land-use-type">
                            <option value="‌RESIDENTIAL">‌RESIDENTIAL</option>
        <option value="AGRICULTURAL">AGRICULTURAL</option>
        <option value="COMMERCIAL">COMMERCIAL</option>
        <option value="COMMERCIAL ( WARE HOUSE)">COMMERCIAL ( WARE HOUSE)</option>
        <option value="COMMERCIAL (OFFICES)">COMMERCIAL (OFFICES)</option>
        <option value="COMMERCIAL (PETROL FILLING STATION)">COMMERCIAL (PETROL FILLING STATION)</option>
        <option value="COMMERCIAL (RICE PROCESSING)">COMMERCIAL (RICE PROCESSING)</option>
        <option value="COMMERCIAL (SCHOOL)">COMMERCIAL (SCHOOL)</option>
        <option value="COMMERCIAL (SHOPS & PUBLIC CONVINIENCE)">COMMERCIAL (SHOPS & PUBLIC CONVINIENCE)</option>
        <option value="COMMERCIAL (SHOPS AND OFFICES)">COMMERCIAL (SHOPS AND OFFICES)</option>
        <option value="COMMERCIAL (SHOPS)">COMMERCIAL (SHOPS)</option>
        <option value="COMMERCIAL (WAREHOUSE)">COMMERCIAL (WAREHOUSE)</option>
        <option value="COMMERCIAL (WORKSHOP AND OFFICES)">COMMERCIAL (WORKSHOP AND OFFICES)</option>
        <option value="COMMERCIAL AND RESIDENTIAL">COMMERCIAL AND RESIDENTIAL</option>
        <option value="INDUSTRIAL">INDUSTRIAL</option>
        <option value="INDUSTRIAL (SMALL SCALE)">INDUSTRIAL (SMALL SCALE)</option>
        <option value="RESIDENTIAL">RESIDENTIAL</option>
        <option value="RESIDENTIAL AND COMMERCIAL">RESIDENTIAL AND COMMERCIAL</option>
        <option value="RESIDENTIAL/COMMERCIAL">RESIDENTIAL/COMMERCIAL</option>
        <option value="RESIDENTIAL/COMMERCIAL LAYOUT">RESIDENTIAL/COMMERCIAL LAYOUT</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Plot Number</label>
                            <input type="text" id="plot-number" class="input" placeholder="e.g. PL-1234">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">District</label>
                            <select class="input" id="district-select">
                            <option value="DALA">DALA</option>
        <option value="DAWAKIN KUDU">DAWAKIN KUDU</option>
        <option value="FAGGE">FAGGE</option> 
        <option value="GWALE">GWALE</option>
        <option value="KUMBOTSO">KUMBOTSO</option>
        <option value="AJINGI">AJINGI</option>
        <option value="ALBASU">ALBASU</option>
        <option value="BAGWAI">BAGWAI</option>
        <option value="BEBEJI">BEBEJI</option>
        <option value="BICHI">BICHI</option>
        <option value="BUNKURE">BUNKURE</option>
        <option value="CITY">CITY</option>
        <option value="CITY DISTRICT">CITY DISTRICT</option>
        <option value="D/KUDU">D/KUDU</option>
        <option value="DAMBATTA">DAMBATTA</option>
        <option value="DAN DINSHE KOFAR DAWANAU">DAN DINSHE KOFAR DAWANAU</option>
        <option value="DANBATTA">DANBATTA</option>
        <option value="DAWAKIL KUDU">DAWAKIL KUDU</option>
        <option value="DAWAKIN KUDU DISTRICT">DAWAKIN KUDU DISTRICT</option>
        <option value="DAWAKIN TOFA">DAWAKIN TOFA</option>
        <option value="DAWAKIN-KUDU">DAWAKIN-KUDU</option>
        <option value="DAWAKIN-TOFA">DAWAKIN-TOFA</option>
        <option value="DAWANAU TOFA">DAWANAU TOFA</option>
        <option value="DOGUWA">DOGUWA</option>
        <option value="DORAYI KARAMA">DORAYI KARAMA</option>
        <option value="GABASAWA">GABASAWA</option>
        <option value="GARKO">GARKO</option>
        <option value="GARUN MALAM">GARUN MALAM</option>
        <option value="GARUN MALLAM">GARUN MALLAM</option>
        <option value="GAYA">GAYA</option>
        <option value="GEZAWA">GEZAWA</option>
        <option value="GWALA">GWALA</option>
        <option value="GWALE DISTRICT">GWALE DISTRICT</option>
        <option value="GWAMMAJA">GWAMMAJA</option>
        <option value="GWARZO">GWARZO</option>
        <option value="HAUSAWA">HAUSAWA</option>
        <option value="INUBAWA">INUBAWA</option>
        <option value="KABO">KABO</option>
        <option value="KANO CITY">KANO CITY</option>
        <option value="KANO MUNICIPAL">KANO MUNICIPAL</option>
        <option value="KANO MUNICIPAL CITY">KANO MUNICIPAL CITY</option>
        <option value="KANO STATE">KANO STATE</option>
        <option value="KANO-CITY">KANO-CITY</option>
        <option value="KARAYE">KARAYE</option>
        <option value="KIBIYA">KIBIYA</option>
        <option value="KIMBOTSO">KIMBOTSO</option>
        <option value="KIRU">KIRU</option>
        <option value="KOFAR DAWANAU">KOFAR DAWANAU</option>
        <option value="KUMBOSTO">KUMBOSTO</option>
        <option value="KUMBOTSO VILLAGE">KUMBOTSO VILLAGE</option>
        <option value="KUMBOTSOI">KUMBOTSOI</option>
        <option value="KUNCHI">KUNCHI</option>
        <option value="KURA">KURA</option>
        <option value="MADOBI">MADOBI</option>
        <option value="MAKODA">MAKODA</option>
        <option value="MINJIBIR">MINJIBIR</option>
        <option value="MUNICIPAL">MUNICIPAL</option>
        <option value="MUNICIPAL LOCAL GOVERNMENT">MUNICIPAL LOCAL GOVERNMENT</option>
        <option value="MUNNICIPAL">MUNNICIPAL</option>
        <option value="NASARAWA">NASARAWA</option>
        <option value="NASSARAWA">NASSARAWA</option>
        <option value="RANO">RANO</option>
        <option value="RIMIN GADO">RIMIN GADO</option>
        <option value="RIMIN ZAKARA">RIMIN ZAKARA</option>
        <option value="ROGO">ROGO</option>
        <option value="SUMAILA">SUMAILA</option>
        <option value="TAKAI">TAKAI</option>
        <option value="TARAUNI">TARAUNI</option>
        <option value="TARAUNI DISTRICT">TARAUNI DISTRICT</option>
        <option value="TOFA">TOFA</option>
        <option value="TSANTAWA">TSANTAWA</option>
        <option value="TSANYAWA">TSANYAWA</option>
        <option value="TUDUN WADA">TUDUN WADA</option>
        <option value="UNGOGGO">UNGOGGO</option>
        <option value="UNGOGO">UNGOGO</option>
        <option value="WAJE">WAJE</option>
        <option value="WARAWA">WARAWA</option>
        <option value="WUDIL">WUDIL</option>
        <option value="ZAWACHIKI">ZAWACHIKI</option>
   
                                <option value="other">Other</option>
                            </select>
                            <!-- Added custom district input field that shows when "Other" is selected -->
                            <div id="custom-district-container" class="hidden" style="margin-top: 0.5rem;">
                                <input type="text" id="custom-district-input" class="input" placeholder="Enter district name" style="font-size: 0.875rem;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">LGA</label>
                            <select id="lga-city" class="input">
                                <option value="Albasu">Albasu</option>
                                <option value="Bagwai">Bagwai</option>
                                <option value="Dala">Dala</option>
                                <option value="Danbatta">Danbatta</option>
                                <option value="D/Tofa">D/Tofa</option>
                                <option value="Gaya">Gaya</option>
                                <option value="Gwale">Gwale</option>
                                <option value="Doguwa">Doguwa</option>
                                <option value="Kibiya">Kibiya</option>
                                <option value="Kabo">Kabo</option>
                                <option value="Gezawa">Gezawa</option>
                                <option value="Kunchi">Kunchi</option>
                                <option value="Karaye">Karaye</option>
                                <option value="Garum Malan">Garum Malan</option>
                                <option value="Madobi">Madobi</option>
                                <option value="Gabasawa">Gabasawa</option>
                                <option value="Rimin Gado">Rimin Gado</option>
                                <option value="Rogo">Rogo</option>
                                <option value="Shanono">Shanono</option>
                                <option value="Municipal" selected>Municipal</option>
                                <option value="Sumaila">Sumaila</option>
                                <option value="Tarauni">Tarauni</option>
                                <option value="Tsanyawa">Tsanyawa</option>
                                <option value="Tudun Wada">Tudun Wada</option>
                                <option value="Tofa">Tofa</option>
                                <option value="Takai">Takai</option>
                                <option value="Kura">Kura</option>
                                <option value="Warawa">Warawa</option>
                                <option value="Garko">Garko</option>
                                <option value="Ajingi">Ajingi</option>
                                <option value="Bichi">Bichi</option>
                                <option value="Minjinbir">Minjinbir</option>
                                <option value="Rano">Rano</option>
                                <option value="Bunkure">Bunkure</option>
                                <option value="Kiru">Kiru</option>
                                <option value="Gwarzo">Gwarzo</option>
                                <option value="Ungogo">Ungogo</option>
                                <option value="Makoda">Makoda</option>
                                <option value="Wudil">Wudil</option>
                                <option value="Nassarawa">Nassarawa</option>
                                <option value="Bebeji">Bebeji</option>
                                <option value="Faffe">Faffe</option>
                                <option value="D/Kudu">D/Kudu</option>
                                <option value="Kumbotso">Kumbotso</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- File Properties Section -->
                <div class="form-section">
                    <!-- <h3 class="form-section-title">File Properties</h3> -->
                    
                    <div class="grid grid-cols-2 gap-4 hidden">
                        <div>
                            <div class="form-checkbox">
                                <input type="checkbox" id="has-cofo">
                                <label for="has-cofo">Has Certificate of Occupancy</label>
                            </div>
                            <div class="form-checkbox">
                                <input type="checkbox" id="has-transaction">
                                <label for="has-transaction">Has Transaction</label>
                            </div>
                            <!-- <div class="form-checkbox">
                                <input type="checkbox" id="is-problematic">
                                <label for="is-problematic">Problematic File</label>
                            </div> -->
                        </div>
                        <div>
                            <div class="form-checkbox">
                                <input type="checkbox" id="co-owned-plot">
                                <label for="co-owned-plot">Co-Owned Plot</label>
                            </div>
                            <div class="form-checkbox">
                                <input type="checkbox" id="merged-plot">
                                <label for="merged-plot">Merged Plot</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- File Archive Details Section -->
                <div class="form-section">
                    <h3 class="form-section-title">File Archive Details</h3>
                    
                    <div class="form-group">
                        <label for="serial-no" class="form-label">Serial No</label>
                        <input type="text" id="serial-no" class="input" placeholder="Enter serial number">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="batch-no" class="form-label">Batch No</label>
                            <select id="batch-no" class="input">
                                <option value="">Select batch number</option>
                                <option value="BATCH-001">BATCH-001</option>
                                <option value="BATCH-002">BATCH-002</option>
                                <option value="BATCH-003">BATCH-003</option>
                                <option value="BATCH-004">BATCH-004</option>
                                <option value="BATCH-005">BATCH-005</option>
                            </select>
                        </div>
                   <div class="form-group">
                            <label for="shelf-location" class="form-label">Shelf/Rack Location</label>
                            <select id="shelf-location" class="input">
                                <option value="">Select location</option>
                                <option value="A1-S1">A1-S1 (Aisle 1, Shelf 1)</option>
                                <option value="A1-S2">A1-S2 (Aisle 1, Shelf 2)</option>
                                <option value="A1-S3">A1-S3 (Aisle 1, Shelf 3)</option>
                                <option value="A2-S1">A2-S1 (Aisle 2, Shelf 1)</option>
                                <option value="A2-S2">A2-S2 (Aisle 2, Shelf 2)</option>
                                <option value="A2-S3">A2-S3 (Aisle 2, Shelf 3)</option>
                                <option value="B1-R1">B1-R1 (Block 1, Rack 1)</option>
                                <option value="B1-R2">B1-R2 (Block 1, Rack 2)</option>
                                <option value="B2-R1">B2-R1 (Block 2, Rack 1)</option>
                                <option value="B2-R2">B2-R2 (Block 2, Rack 2)</option>
                            </select>
                        </div>
    
 
                    </div>
                </div>
                
                <div class="flex justify-between mt-6">
                    <button type="button" class="btn" id="cancel-btn">Cancel</button>
                    <button type="button" class="btn btn-blue" id="create-file-btn">Create File Index</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Generate tracking ID functionality
function generateTrackingId() {
    // Generate random alphanumeric segments
    const segment1 = generateRandomAlphanumeric(8); // 8 characters like MESALDX6
    const segment2 = generateRandomAlphanumeric(5); // 5 characters like QWB08
    return `TRK-${segment1}-${segment2}`;
}

// Generate random alphanumeric string
function generateRandomAlphanumeric(length) {
    const characters = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789'; // Exclude O, 0 for clarity
    let result = '';
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
}

// IMMEDIATE TRACKING ID GENERATION - runs as soon as script loads
(function immediateTrackingIdGeneration() {
    // Function to set tracking ID
    function setTrackingId() {
        const trackingIdInput = document.getElementById('tracking-id');
        if (trackingIdInput) {
            trackingIdInput.value = generateTrackingId();
            return true;
        }
        return false;
    }
    
    // Try immediately
    setTrackingId();
    
    // Keep trying until successful
    let attempts = 0;
    const maxAttempts = 50;
    const interval = setInterval(function() {
        if (setTrackingId() || attempts >= maxAttempts) {
            clearInterval(interval);
        }
        attempts++;
    }, 20); // Try every 20ms
})();

// Generate random alphanumeric string
function generateRandomAlphanumeric(length) {
    const characters = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789'; // Exclude O, 0 for clarity
    let result = '';
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
}

// Ensure tracking ID is always available
function ensureTrackingIdExists() {
    const trackingIdInput = document.getElementById('tracking-id');
    if (trackingIdInput) {
        if (!trackingIdInput.value || trackingIdInput.value === 'Auto-generating...') {
            trackingIdInput.value = generateTrackingId();
        }
    }
    return trackingIdInput && trackingIdInput.value;
}

// Auto-generate tracking ID immediately when script loads
(function() {
    // Generate tracking ID as soon as this script runs
    const trackingIdInput = document.getElementById('tracking-id');
    if (trackingIdInput) {
        trackingIdInput.value = generateTrackingId();
    }
    
    // Fallback attempts with shorter intervals
    setTimeout(ensureTrackingIdExists, 10);
    setTimeout(ensureTrackingIdExists, 50);
    setTimeout(ensureTrackingIdExists, 100);
    
    // Use MutationObserver to watch for when the tracking ID input becomes available
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    const trackingIdInput = document.getElementById('tracking-id');
                    if (trackingIdInput && (!trackingIdInput.value || trackingIdInput.value === 'Auto-generating...')) {
                        trackingIdInput.value = generateTrackingId();
                        observer.disconnect(); // Stop observing once we've set the value
                    }
                }
            });
        });
        
        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Stop observing after 3 seconds to prevent memory leaks
        setTimeout(() => {
            observer.disconnect();
        }, 3000);
    }
})();

// Initialize tracking ID generation
document.addEventListener('DOMContentLoaded', function() {
    const generateTrackingBtn = document.getElementById('generate-tracking-btn');
    const trackingIdInput = document.getElementById('tracking-id');
    
    if (trackingIdInput) {
        // Always generate initial tracking ID on DOM ready
        trackingIdInput.value = generateTrackingId();
    }
    
    if (generateTrackingBtn && trackingIdInput) {
        generateTrackingBtn.addEventListener('click', function() {
            const newTrackingId = generateTrackingId();
            trackingIdInput.value = newTrackingId;
            
            // Add visual feedback
            trackingIdInput.style.backgroundColor = '#dcfce7';
            setTimeout(() => {
                trackingIdInput.style.backgroundColor = '#f9fafb';
            }, 1000);
        });
    }
    
    // Handle district selection
    const districtSelect = document.getElementById('district-select');
    const customDistrictContainer = document.getElementById('custom-district-container');
    const customDistrictInput = document.getElementById('custom-district-input');
    
    if (districtSelect && customDistrictContainer && customDistrictInput) {
        districtSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                customDistrictContainer.classList.remove('hidden');
                customDistrictInput.focus();
            } else {
                customDistrictContainer.classList.add('hidden');
                customDistrictInput.value = '';
            }
        });
    }
    
    // Initialize file indexing form submission
    initializeFileIndexingForm();
});

function initializeFileIndexingForm() {
    const form = document.getElementById('new-file-form');
    const createBtn = document.getElementById('create-file-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const closeBtn = document.getElementById('close-dialog-btn');
    const overlay = document.getElementById('new-file-dialog-overlay');
    
    if (createBtn) {
        createBtn.addEventListener('click', function(e) {
            e.preventDefault();
            submitFileIndexingForm();
        });
    }
    
    // Close dialog handlers
    [cancelBtn, closeBtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                closeFileIndexingDialog();
            });
        }
    });
    
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeFileIndexingDialog();
            }
        });
    }
}

function submitFileIndexingForm() {
    const form = document.getElementById('new-file-form');
    if (!form) return;
    
    // Get form data
    const formData = {
        file_number: document.getElementById('fileno')?.value || '',
        file_title: document.getElementById('file-title')?.value || '',
        land_use_type: document.getElementById('land-use-type')?.value || 'residential',
        plot_number: document.getElementById('plot-number')?.value || '',
        district: getDistrictValue(),
        lga: document.getElementById('lga-city')?.value || 'Kano Municipal',
        has_cofo: document.getElementById('has-cofo')?.checked || false,
        has_transaction: document.getElementById('has-transaction')?.checked || false,
        is_problematic: document.getElementById('is-problematic')?.checked || false,
        is_co_owned_plot: document.getElementById('co-owned-plot')?.checked || false,
        is_merged: document.getElementById('merged-plot')?.checked || false,
        serial_no: document.getElementById('serial-no')?.value || '',
        batch_no: document.getElementById('batch-no')?.value || '',
        shelf_location: document.getElementById('shelf-location')?.value || '',
        tracking_id: document.getElementById('tracking-id')?.value || '',
        // Include smart file selector data
        main_application_id: document.getElementById('application_id')?.value || null,
        subapplication_id: document.getElementById('sub_application_id')?.value || null,
        file_number_source: window.selectedApplication?.isManual ? 'manual' : 'existing',
        source_file_id: window.selectedApplication?.id || null
    };
    
    // Validation
    if (!formData.file_number || !formData.file_title) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Validation Error',
                text: 'File number and file title are required.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            alert('File number and file title are required.');
        }
        return;
    }
    
    // Show loading state
    const createBtn = document.getElementById('create-file-btn');
    if (createBtn) {
        createBtn.disabled = true;
        createBtn.textContent = 'Creating...';
    }
    
    // Submit to server
    fetch('/fileindexing/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Success!',
                    text: data.message || 'File indexing created successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    closeFileIndexingDialog();
                    // Refresh the file list if available
                    if (typeof refreshFileList === 'function') {
                        refreshFileList();
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                alert(data.message || 'File indexing created successfully!');
                closeFileIndexingDialog();
                window.location.reload();
            }
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        console.error('Error creating file indexing:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error',
                text: error.message || 'Failed to create file indexing. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            alert(error.message || 'Failed to create file indexing. Please try again.');
        }
    })
    .finally(() => {
        // Reset button state
        if (createBtn) {
            createBtn.disabled = false;
            createBtn.textContent = 'Create File Index';
        }
    });
}

function getDistrictValue() {
    const districtSelect = document.getElementById('district-select');
    const customDistrictInput = document.getElementById('custom-district-input');
    
    if (!districtSelect) return '';
    
    if (districtSelect.value === 'other' && customDistrictInput?.value) {
        return customDistrictInput.value;
    }
    
    return districtSelect.value;
}

function closeFileIndexingDialog() {
    const overlay = document.getElementById('new-file-dialog-overlay');
    if (overlay) {
        overlay.classList.add('hidden');
        
        // Reset form
        const form = document.getElementById('new-file-form');
        if (form) {
            form.reset();
        }
        
        // Always regenerate tracking ID when closing
        const trackingIdInput = document.getElementById('tracking-id');
        if (trackingIdInput) {
            trackingIdInput.value = generateTrackingId();
        }
        
        // Reset smart file selector
        if (typeof resetSmartFileSelector === 'function') {
            resetSmartFileSelector();
        }
        
        // Hide custom district input
        const customDistrictContainer = document.getElementById('custom-district-container');
        if (customDistrictContainer) {
            customDistrictContainer.classList.add('hidden');
        }
    }
}

// Function to open the file indexing dialog
function openFileIndexingDialog() {
    const overlay = document.getElementById('new-file-dialog-overlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        
        // Always ensure tracking ID is present when dialog opens
        const trackingIdInput = document.getElementById('tracking-id');
        if (trackingIdInput) {
            // If no tracking ID exists or it's still showing placeholder, generate one
            if (!trackingIdInput.value || trackingIdInput.value === 'Auto-generating...') {
                const newTrackingId = generateTrackingId();
                trackingIdInput.value = newTrackingId;
            } else {
                // Generate a fresh one each time dialog opens
                const newTrackingId = generateTrackingId();
                trackingIdInput.value = newTrackingId;
            }
            
            // Add visual feedback to show it's been generated
            trackingIdInput.style.backgroundColor = '#dcfce7';
            setTimeout(() => {
                trackingIdInput.style.backgroundColor = '#f9fafb';
            }, 1000);
        }
        
        // Focus on first input
        setTimeout(() => {
            const firstInput = overlay.querySelector('input:not([readonly])');
            if (firstInput) {
                firstInput.focus();
            }
        }, 100);
    }
}
</script>