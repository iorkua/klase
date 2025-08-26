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
                                    <input type="text" id="tracking-id" class="input" readonly style="width: 180px; font-size: 0.75rem; background-color: #f9fafb; font-family: monospace;">
                                    <button type="button" id="generate-tracking-btn" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" title="Generate new tracking ID">
                                        <i data-lucide="refresh-cw" class="h-3 w-3"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="file-number" class="form-label required">File Number</label>
                            
                            <!-- Tabbed interface for file number types -->
                            <div class="file-number-card" style="border: 1px solid #d1fae5; border-radius: 0.5rem; background-color: #f0fdf4;">
                                <div class="card-header" style="background-color: #ecfdf5; padding: 0.75rem; border-bottom: 1px solid #d1fae5; border-radius: 0.5rem 0.5rem 0 0;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i data-lucide="file-code" class="h-4 w-4" style="color: #059669;"></i>
                                        <span style="font-size: 0.875rem; font-weight: 500; color: #065f46;">File Number Information</span>
                                    </div>
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Select file number type and enter the details</div>
                                </div>
                                
                                <div class="card-content" style="padding: 0.75rem;">
                                    <!-- Tab Navigation -->
                                    <div class="tab-list" style="display: grid; grid-template-columns: repeat(3, 1fr); border: 1px solid #e5e7eb; border-radius: 0.375rem; overflow: hidden; margin-bottom: 1rem;">
                                        <button type="button" class="tab-trigger active" data-tab="mlsFileNo" style="padding: 0.5rem; background-color: #dbeafe; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 500; color: #1d4ed8; border-right: 1px solid #e5e7eb;">
                                            MLS
                                        </button>
                                        <button type="button" class="tab-trigger" data-tab="kangisFileNo" style="padding: 0.5rem; background-color: white; border: none; cursor: pointer; font-size: 0.75rem; color: #374151; border-right: 1px solid #e5e7eb;">
                                            KANGIS
                                        </button>
                                        <button type="button" class="tab-trigger" data-tab="newKangisFileNo" style="padding: 0.5rem; background-color: white; border: none; cursor: pointer; font-size: 0.75rem; color: #374151;">
                                            New KANGIS
                                        </button>
                                    </div>
                                    
                                    <!-- Tab Content -->
                                    <div class="tab-content active" id="mlsFileNo">
                                        <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.75rem;">Legacy File Number (MLS)</div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="form-group">
                                                <label class="form-label" style="font-size: 0.75rem;">File Prefix</label>
                                                <select class="input" id="mls-prefix" style="height: 2rem; font-size: 0.75rem;">
                                                    <option value="">Select prefix</option>
                                                    <option value="CON-COM">CON-COM (Conversion Commercial)</option>
                                                    <option value="CON-RES">CON-RES (Conversion Residential)</option>
                                                    <option value="RES">RES (Residential)</option>
                                                    <option value="COM">COM (Commercial)</option>
                                                    <option value="IND">IND (Industrial)</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" style="font-size: 0.75rem;">Serial Number</label>
                                                <input type="text" class="input" id="mls-serial" placeholder="e.g. 2019-296 or 91-249" style="height: 2rem; font-size: 0.75rem;">
                                            </div>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">Format example: CON-COM-2019-296, RES-2015-4859, COM-91-249</div>
                                    </div>
                                    
                                    <div class="tab-content" id="kangisFileNo" style="display: none;">
                                        <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.75rem;">KANGIS File Number</div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="form-group">
                                                <label class="form-label" style="font-size: 0.75rem;">File Prefix</label>
                                                <select class="input" id="kangis-prefix" style="height: 2rem; font-size: 0.75rem;">
                                                    <option value="">Select prefix</option>
                                                    <option value="KNML">KNML (Kano Municipal)</option>
                                                    <option value="MLKN">MLKN (Municipal Kano)</option>
                                                    <option value="KNGP">KNGP (Kano Government Property)</option>
                                                    <option value="KNRS">KNRS (Kano Residential)</option>
                                                    <option value="KNCO">KNCO (Kano Commercial)</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" style="font-size: 0.75rem;">Serial Number</label>
                                                <input type="text" class="input" id="kangis-serial" placeholder="e.g. 09846 or 03051" style="height: 2rem; font-size: 0.75rem;">
                                            </div>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">Format example: KNML 09846, MLKN 01251, KNGP 00338</div>
                                    </div>
                                    
                                    <div class="tab-content" id="newKangisFileNo" style="display: none;">
                                        <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.75rem;">New KANGIS File Number</div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="form-group">
                                                <label class="form-label" style="font-size: 0.75rem;">File Prefix</label>
                                                <select class="input" id="new-kangis-prefix" style="height: 2rem; font-size: 0.75rem;">
                                                    <option value="">Select prefix</option>
                                                    <option value="KN">KN (Kano New System)</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" style="font-size: 0.75rem;">Serial Number</label>
                                                <input type="text" class="input" id="new-kangis-serial" placeholder="e.g. 0001 or 2500" style="height: 2rem; font-size: 0.75rem;">
                                            </div>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">Format example: KN0001, KN2500, KN0131</div>
                                    </div>
                                    
                                    <!-- File number preview -->
                                    <div id="file-number-preview" style="margin-top: 0.75rem; padding: 0.5rem; background-color: #ecfdf5; border: 1px solid #d1fae5; border-radius: 0.375rem; display: none;">
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            <label style="font-size: 0.75rem; color: #065f46; font-weight: 500;">Complete File Number:</label>
                                            <span id="complete-file-number" style="font-size: 0.875rem; font-weight: 600; color: #065f46; font-family: monospace;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                <select class="input">
                                    <option value="residential">Residential</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="industrial">Industrial</option>
                                    <option value="agricultural">Agricultural</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Plot Number</label>
                                <input type="text" class="input" placeholder="e.g. PL-1234">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">District</label>
                                <select class="input" id="district-select">
                                    <option value="nasarawa">Nasarawa</option>
                                    <option value="fagge">Fagge</option>
                                    <option value="bompai">Bompai</option>
                                    <option value="other">Other</option>
                                </select>
                                <!-- Added custom district input field that shows when "Other" is selected -->
                                <div id="custom-district-container" class="hidden" style="margin-top: 0.5rem;">
                                    <input type="text" id="custom-district-input" class="input" placeholder="Enter district name" style="font-size: 0.875rem;">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">LGA/City</label>
                                <input type="text" class="input" value="Kano Municipal">
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Properties Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">File Properties</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="form-checkbox">
                                    <input type="checkbox" id="has-cofo">
                                    <label for="has-cofo">Has Certificate of Occupancy</label>
                                </div>
                                <div class="form-checkbox">
                                    <input type="checkbox" id="has-transaction">
                                    <label for="has-transaction">Has Transaction</label>
                                </div>
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