<script>
        // Initialize Lucide icons
        lucide.createIcons();

        const generateTrackingId = () => {
            const timestamp = Date.now().toString(36)
            const random = Math.random().toString(36).substr(2, 5)
            return `TRK-${timestamp.toUpperCase()}-${random.toUpperCase()}`
        }

        const generateLogId = () => {
            const now = new Date()
            const timestamp = now.toISOString().replace(/[-:T]/g, "").split(".")[0]
            const random = Math.floor(Math.random() * 999)
                .toString()
                .padStart(3, "0")
            return `LOG-${timestamp}-${random}`
        }

        // DOM Elements
        const newFileDialogOverlay = document.getElementById('new-file-dialog-overlay');
        const closeDialogBtn = document.getElementById('close-dialog-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const createFileBtn = document.getElementById('create-file-btn');
        const trackingIdInput = document.getElementById('tracking-id');
        const generateTrackingBtn = document.getElementById('generate-tracking-btn');
        const districtSelect = document.getElementById('district-select');
        const customDistrictContainer = document.getElementById('custom-district-container');
        const customDistrictInput = document.getElementById('custom-district-input');

        // Tab functionality and file number preview
        const tabTriggers = document.querySelectorAll('.tab-trigger');
        const tabContents = document.querySelectorAll('.tab-content');
        const fileNumberPreview = document.getElementById('file-number-preview');
        const completeFileNumber = document.getElementById('complete-file-number');

        // Tab switching functionality
        tabTriggers.forEach(trigger => {
            trigger.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Update active tab trigger
                tabTriggers.forEach(t => {
                    t.classList.remove('active');
                    t.style.backgroundColor = 'white';
                    t.style.color = '#374151';
                });
                this.classList.add('active');
                this.style.backgroundColor = '#dbeafe';
                this.style.color = '#1d4ed8';
                
                // Update active tab content
                tabContents.forEach(content => {
                    content.style.display = 'none';
                    content.classList.remove('active');
                });
                const activeContent = document.getElementById(targetTab);
                activeContent.style.display = 'block';
                activeContent.classList.add('active');
                
                updateFileNumberPreview();
            });
        });

        // Update file number preview
        function updateFileNumberPreview() {
            const activeTab = document.querySelector('.tab-content.active');
            const activeTabId = activeTab.id;
            
            let prefix = '';
            let serial = '';
            
            if (activeTabId === 'mlsFileNo') {
                prefix = document.getElementById('mls-prefix').value;
                serial = document.getElementById('mls-serial').value;
            } else if (activeTabId === 'kangisFileNo') {
                prefix = document.getElementById('kangis-prefix').value;
                serial = document.getElementById('kangis-serial').value;
            } else if (activeTabId === 'newKangisFileNo') {
                prefix = document.getElementById('new-kangis-prefix').value;
                serial = document.getElementById('new-kangis-serial').value;
            }
            
            if (prefix && serial) {
                let completeNumber = '';
                if (activeTabId === 'mlsFileNo') {
                    // For MLS: prefix-serial (e.g., CON-COM-2019-296)
                    completeNumber = `${prefix}-${serial}`;
                } else if (activeTabId === 'kangisFileNo') {
                    // For KANGIS: prefix serial (e.g., KNML 09846)
                    completeNumber = `${prefix} ${serial}`;
                } else if (activeTabId === 'newKangisFileNo') {
                    // For New KANGIS: prefixserial (e.g., KN0001)
                    completeNumber = `${prefix}${serial}`;
                }
                
                completeFileNumber.textContent = completeNumber;
                fileNumberPreview.style.display = 'block';
            } else {
                fileNumberPreview.style.display = 'none';
            }
        }

        // Add event listeners for file number inputs
        document.getElementById('mls-prefix').addEventListener('change', updateFileNumberPreview);
        document.getElementById('mls-serial').addEventListener('input', updateFileNumberPreview);
        document.getElementById('kangis-prefix').addEventListener('change', updateFileNumberPreview);
        document.getElementById('kangis-serial').addEventListener('input', updateFileNumberPreview);
        document.getElementById('new-kangis-prefix').addEventListener('change', updateFileNumberPreview);
        document.getElementById('new-kangis-serial').addEventListener('input', updateFileNumberPreview);

        districtSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                customDistrictContainer.classList.remove('hidden');
                customDistrictInput.focus();
            } else {
                customDistrictContainer.classList.add('hidden');
                customDistrictInput.value = '';
            }
        });

        // Show new file dialog
        function showNewFileDialog() {
            newFileDialogOverlay.classList.remove('hidden');
            // Reset form fields
            document.getElementById('new-file-form').reset();
            // Reset to first tab
            tabTriggers.forEach(t => {
                t.classList.remove('active');
                t.style.backgroundColor = 'white';
                t.style.color = '#374151';
            });
            tabTriggers[0].classList.add('active');
            tabTriggers[0].style.backgroundColor = '#dbeafe';
            tabTriggers[0].style.color = '#1d4ed8';
            
            tabContents.forEach(content => {
                content.style.display = 'none';
                content.classList.remove('active');
            });
            tabContents[0].style.display = 'block';
            tabContents[0].classList.add('active');
            
            // Hide file number preview
            fileNumberPreview.style.display = 'none';
            
            // Generate initial tracking ID
            trackingIdInput.value = generateTrackingId();
        }

        // Close new file dialog
        function closeNewFileDialog() {
            newFileDialogOverlay.classList.add('hidden');
        }

        // Create new file
        function createNewFile() {
            // Get form values
            const fileTitle = document.getElementById('file-title').value;
            const activeTab = document.querySelector('.tab-content.active');
            const activeTabId = activeTab.id;
            const prefix = document.querySelector(`#${activeTabId} select`).value;
            const serial = document.querySelector(`#${activeTabId} input`).value;
            const serialNo = document.getElementById('serial-no').value;
            const batchNo = document.getElementById('batch-no').value;
            const shelfLocation = document.getElementById('shelf-location').value;
            const districtValue = districtSelect.value === 'other' ? customDistrictInput.value : districtSelect.value;
            
            if (!fileTitle.trim()) {
                alert('Please enter a file title.');
                return;
            }

            if (districtSelect.value === 'other' && !customDistrictInput.value.trim()) {
                alert('Please enter a district name.');
                customDistrictInput.focus();
                return;
            }

            // Create a new file object (for demo purposes)
            let fileNumber = '';
            if (activeTabId === 'mlsFileNo' && prefix && serial) {
                fileNumber = `${prefix}-${serial}`;
            } else if (activeTabId === 'kangisFileNo' && prefix && serial) {
                fileNumber = `${prefix} ${serial}`;
            } else if (activeTabId === 'newKangisFileNo' && prefix && serial) {
                fileNumber = `${prefix}${serial}`;
            }

            const newFile = {
                id: `FILE-${Date.now()}`,
                fileNumber: fileNumber,
                name: fileTitle,
                type: 'Certificate of Occupancy',
                source: 'Collated',
                date: new Date().toISOString().split('T')[0],
                landUseType: document.querySelector('select').value || 'Residential',
                district: districtValue || 'Nasarawa',
                hasCofo: document.getElementById('has-cofo').checked,
                serialNo: serialNo,
                batchNo: batchNo,
                shelfLocation: shelfLocation
            };

            // Close dialog
            closeNewFileDialog();
            
            // Show success message with file details
            alert(`New file index created successfully!\n\nFile Number: ${newFile.fileNumber}\nTitle: ${newFile.name}\nType: ${newFile.landUseType}\nDistrict: ${newFile.district}\nSerial No: ${newFile.serialNo}\nBatch No: ${newFile.batchNo}\nShelf Location: ${newFile.shelfLocation}`);
        }

        // Event listeners
        closeDialogBtn.addEventListener('click', closeNewFileDialog);
        cancelBtn.addEventListener('click', closeNewFileDialog);
        createFileBtn.addEventListener('click', createNewFile);
        generateTrackingBtn.addEventListener('click', function() {
            trackingIdInput.value = generateTrackingId();
        });

        // Close dialog when clicking outside
        newFileDialogOverlay.addEventListener('click', function(e) {
            if (e.target === newFileDialogOverlay) {
                closeNewFileDialog();
            }
        });

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !newFileDialogOverlay.classList.contains('hidden')) {
                closeNewFileDialog();
            }
        });
    </script>