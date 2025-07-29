<script>


    // Sample data
    const indexedFiles = [
        {
            id: "FILE-2023-001",
            name: "Certificate of Occupancy - Alhaji Ibrahim Dantata",
            fileNumber: "RES-2015-4859",
            kangisFileNo: "KNGP 00338",
            newKangisFileNo: "KNO001",
            type: "Certificate of Occupancy",
            location: "Cabinet A-12",
            date: "2023-06-15",
            status: "Indexed",
        },
        {
            id: "FILE-2023-002",
            name: "Site Plan - Hajiya Amina Yusuf",
            fileNumber: "RES-86-2244",
            kangisFileNo: "RES-86-2244",
            newKangisFileNo: "KNO069",
            type: "Site Plan",
            location: "Cabinet B-05",
            date: "2023-06-14",
            status: "Indexed",
        },
        {
            id: "FILE-2023-003",
            name: "Letter of Administration - Kano Traders Association",
            fileNumber: "COM-91-249",
            kangisFileNo: "MLKN 03051",
            newKangisFileNo: "KNO082",
            type: "Letter of Administration",
            location: "Cabinet C-08",
            date: "2023-06-13",
            status: "Indexed",
        },
        {
            id: "FILE-2023-004",
            name: "Right of Occupancy - Musa Usman Bayero",
            fileNumber: "RES-2000-1904",
            kangisFileNo: "KNML 08146",
            newKangisFileNo: "KNO131",
            type: "Right of Occupancy",
            location: "Cabinet A-08",
            date: "2023-06-12",
            status: "Indexed",
        },
        {
            id: "FILE-2023-005",
            name: "Deed of Assignment - Hajiya Fatima Mohammed",
            fileNumber: "CON-IND-2021-37",
            kangisFileNo: "MLKN 03888",
            newKangisFileNo: "KNO132",
            type: "Deed of Assignment",
            location: "Cabinet B-12",
            date: "2023-06-10",
            status: "Indexed",
        },
    ];

    // State management
    let selectedFiles = [];
    let labelFormat = 'qrcode';
    let activeTab = 'files';
    let batchMode = false;
    let batchStartNumber = 1;
    let batchCount = 10;
    let copies = 1;
    let showAdvancedOptions = false;
    let searchTerm = '';

    // DOM elements
    const elements = {
        historyBtn: document.getElementById('history-btn'),
        resetBtn: document.getElementById('reset-btn'),
        printLabelsBtn: document.getElementById('print-labels-btn'),
        printHistory: document.getElementById('print-history'),
        closeHistoryBtn: document.getElementById('close-history-btn'),
        searchInput: document.getElementById('search-input'),
        batchModeCheckbox: document.getElementById('batch-mode'),
        batchControls: document.getElementById('batch-controls'),
        batchStart: document.getElementById('batch-start'),
        batchCount: document.getElementById('batch-count'),
        generateBatchBtn: document.getElementById('generate-batch-btn'),
        selectAllCheckbox: document.getElementById('select-all'),
        filesList: document.getElementById('files-list'),
        selectionCount: document.getElementById('selection-count'),
        selectedFilesCount: document.getElementById('selected-files-count'),
        duplicateBtn: document.getElementById('duplicate-btn'),
        continueToSettingsBtn: document.getElementById('continue-to-settings-btn'),
        advancedOptionsBtn: document.getElementById('advanced-options-btn'),
        advancedOptions: document.getElementById('advanced-options'),
        formatBarcode: document.getElementById('format-barcode'),
        formatQrcode: document.getElementById('format-qrcode'),
        copiesInput: document.getElementById('copies'),
        backToFilesBtn: document.getElementById('back-to-files-btn'),
        continueToPreviewBtn: document.getElementById('continue-to-preview-btn'),
        backToSettingsBtn: document.getElementById('back-to-settings-btn'),
        finalPrintBtn: document.getElementById('final-print-btn'),
        previewDescription: document.getElementById('preview-description'),
        labelPreviews: document.getElementById('label-previews'),
        summaryLabels: document.getElementById('summary-labels'),
        summaryCopies: document.getElementById('summary-copies'),
        summaryTotal: document.getElementById('summary-total'),
        summaryTemplate: document.getElementById('summary-template'),
        summarySize: document.getElementById('summary-size'),
        summaryFormat: document.getElementById('summary-format'),
        exportPdfBtn: document.getElementById('export-pdf-btn'),
        saveTemplateBtn: document.getElementById('save-template-btn'),
        importTemplateBtn: document.getElementById('import-template-btn'),
        tabTriggers: document.querySelectorAll('.tab-trigger'),
        tabContents: document.querySelectorAll('.tab-content')
    };

    // Helper functions
    function updateCounts() {
        elements.selectedFilesCount.textContent = selectedFiles.length;
        elements.selectionCount.textContent = `${selectedFiles.length} of ${getFilteredFiles().length} selected`;
        
        // Show/hide duplicate button
        if (selectedFiles.length > 0) {
            elements.duplicateBtn.classList.remove('hidden');
        } else {
            elements.duplicateBtn.classList.add('hidden');
        }
        
        // Enable/disable continue button
        if (selectedFiles.length > 0 || batchMode) {
            elements.continueToSettingsBtn.disabled = false;
        } else {
            elements.continueToSettingsBtn.disabled = true;
        }
    }

    function getFilteredFiles() {
        return indexedFiles.filter(file =>
            file.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            file.fileNumber.toLowerCase().includes(searchTerm.toLowerCase()) ||
            file.type.toLowerCase().includes(searchTerm.toLowerCase()) ||
            file.location.toLowerCase().includes(searchTerm.toLowerCase())
        );
    }

    function toggleFileSelection(fileId) {
        if (selectedFiles.includes(fileId)) {
            selectedFiles = selectedFiles.filter(id => id !== fileId);
        } else {
            selectedFiles.push(fileId);
        }
        updateCounts();
        renderFilesList();
    }

    function selectAllFiles() {
        const filteredFiles = getFilteredFiles();
        if (selectedFiles.length === filteredFiles.length) {
            selectedFiles = [];
        } else {
            selectedFiles = filteredFiles.map(file => file.id);
        }
        updateCounts();
        renderFilesList();
    }

    function renderFilesList() {
        const filteredFiles = getFilteredFiles();
        elements.filesList.innerHTML = '';

        filteredFiles.forEach(file => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'flex items-center p-4';
            fileDiv.innerHTML = `
                <input type="checkbox" class="checkbox mr-4" ${selectedFiles.includes(file.id) ? 'checked' : ''} 
                       onchange="toggleFileSelection('${file.id}')">
                <div class="flex flex-1 items-center gap-3">
                    <i data-lucide="file-text" class="h-8 w-8 text-blue-500"></i>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-medium text-blue-600">${file.fileNumber}</p>
                            <span class="badge badge-outline text-xs">${file.type}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">${file.name}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="badge badge-secondary text-xs">${file.location}</span>
                            <span class="text-xs text-muted-foreground">${file.date}</span>
                            ${file.kangisFileNo ? `<span class="text-xs text-muted-foreground">KANGIS: ${file.kangisFileNo}</span>` : ''}
                            ${file.newKangisFileNo ? `<span class="text-xs text-muted-foreground">New KANGIS: ${file.newKangisFileNo}</span>` : ''}
                        </div>
                    </div>
                    <span class="badge badge-green">
                        <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                        Indexed
                    </span>
                </div>
            `;
            elements.filesList.appendChild(fileDiv);
        });

        // Update select all checkbox
        elements.selectAllCheckbox.checked = selectedFiles.length === filteredFiles.length && filteredFiles.length > 0;
        
        // Re-initialize Lucide icons
        lucide.createIcons();
    }

    function switchTab(tabName) {
        activeTab = tabName;
        
        // Update tab triggers
        elements.tabTriggers.forEach(trigger => {
            trigger.classList.remove('active');
            if (trigger.dataset.tab === tabName) {
                trigger.classList.add('active');
            }
        });

        // Update tab contents
        elements.tabContents.forEach(content => {
            content.classList.remove('active');
            if (content.id === `${tabName}-tab`) {
                content.classList.add('active');
            }
        });

        // Update preview when switching to preview tab
        if (tabName === 'preview') {
            updatePreview();
        }
    }

    function generateBarcode(text, width = 200, height = 100) {
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        
        try {
            JsBarcode(canvas, text, {
                format: "CODE128",
                width: 2,
                height: height * 0.6,
                displayValue: true,
                fontSize: 12,
                margin: 10
            });
        } catch (error) {
            // Fallback if barcode generation fails
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#000';
            ctx.fillRect(0, height * 0.2, width, height * 0.6);
            ctx.fillStyle = '#fff';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(text, width / 2, height * 0.9);
        }
        
        return canvas;
    }

    function generateQRCode(text, size = 150) {
        // Create an image element instead of canvas
        const img = document.createElement('img');
        
        // Use the QR code API URL
        const encodedText = encodeURIComponent(text);
        img.src = `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodedText}`;
        
        // Set size attributes
        img.width = size;
        img.height = size;
        
        // Add styling
        img.style.maxWidth = '100%';
        img.style.height = 'auto';
        
        // Add alt text for accessibility
        img.alt = `QR Code for ${text}`;
        
        return img;
    }

    function updatePreview() {
        const labelCount = batchMode ? batchCount : selectedFiles.length;
        const totalLabels = labelCount * copies;
        
        elements.previewDescription.textContent = `You have selected ${labelCount} ${batchMode ? 'batch' : ''} labels to print. Please review the label previews below before printing.`;
        
        // Update summary
        elements.summaryLabels.textContent = labelCount;
        elements.summaryCopies.textContent = copies;
        elements.summaryTotal.textContent = totalLabels;
        elements.summaryFormat.textContent = labelFormat === 'barcode' ? 'Barcode' : 'QR Code';
        
        // Generate previews
        elements.labelPreviews.innerHTML = '';
        
        if (batchMode) {
            // Batch mode previews
            const previewCount = Math.min(batchCount, 6);
            for (let i = 0; i < previewCount; i++) {
                const labelDiv = document.createElement('div');
                labelDiv.className = 'border p-6 rounded-md flex flex-col items-center';
                
                const labelText = `BATCH-${(batchStartNumber + i).toString().padStart(4, '0')}`;
                
                let codeElement;
                if (labelFormat === 'barcode') {
                    codeElement = generateBarcode(labelText);
                } else {
                    codeElement = generateQRCode(labelText);
                }
                
                labelDiv.innerHTML = `
                    <div class="mb-4 w-full flex justify-center"></div>
                    <div class="text-center text-sm mt-2">
                        <p class="font-bold">Batch Label #${batchStartNumber + i}</p>
                        <p class="text-muted-foreground">Generated: ${new Date().toLocaleDateString()}</p>
                    </div>
                `;
                
                labelDiv.querySelector('div').appendChild(codeElement);
                elements.labelPreviews.appendChild(labelDiv);
            }
            
            if (batchCount > 6) {
                const moreDiv = document.createElement('div');
                moreDiv.className = 'border p-6 rounded-md flex items-center justify-center';
                moreDiv.innerHTML = `<p class="text-sm text-muted-foreground">+${batchCount - 6} more labels</p>`;
                elements.labelPreviews.appendChild(moreDiv);
            }
        } else {
            // File mode previews
            const previewCount = Math.min(selectedFiles.length, 6);
            for (let i = 0; i < previewCount; i++) {
                const fileId = selectedFiles[i];
                const file = indexedFiles.find(f => f.id === fileId);
                if (!file) continue;
                
                const labelDiv = document.createElement('div');
                labelDiv.className = 'border p-6 rounded-md flex flex-col items-center';
                
                let codeElement;
                if (labelFormat === 'barcode') {
                    codeElement = generateBarcode(file.fileNumber);
                } else {
                    codeElement = generateQRCode(file.fileNumber);
                }
                
                labelDiv.innerHTML = `
                    <div class="mb-4 w-full flex justify-center"></div>
                    <div class="text-center text-sm mt-2">
                        <p class="font-bold">${file.type}</p>
                        <p class="text-muted-foreground">${file.location}</p>
                        ${file.kangisFileNo ? `<p class="text-muted-foreground">KANGIS: ${file.kangisFileNo}</p>` : ''}
                    </div>
                `;
                
                labelDiv.querySelector('div').appendChild(codeElement);
                elements.labelPreviews.appendChild(labelDiv);
            }
            
            if (selectedFiles.length > 6) {
                const moreDiv = document.createElement('div');
                moreDiv.className = 'border p-6 rounded-md flex items-center justify-center';
                moreDiv.innerHTML = `<p class="text-sm text-muted-foreground">+${selectedFiles.length - 6} more labels</p>`;
                elements.labelPreviews.appendChild(moreDiv);
            }
        }
    }

    function resetForm() {
        selectedFiles = [];
        labelFormat = 'qrcode';
        batchMode = false;
        batchStartNumber = 1;
        batchCount = 10;
        copies = 1;
        showAdvancedOptions = false;
        searchTerm = '';
        
        // Reset UI elements
        elements.batchModeCheckbox.checked = false;
        elements.batchControls.classList.add('hidden');
        elements.batchStart.value = '1';
        elements.batchCount.value = '10';
        elements.copiesInput.value = '1';
        elements.searchInput.value = '';
        elements.formatBarcode.classList.remove('selected');
        elements.formatQrcode.classList.add('selected');
        elements.advancedOptions.classList.add('hidden');
        elements.advancedOptionsBtn.textContent = 'Show Advanced';
        
        updateCounts();
        renderFilesList();
        switchTab('files');
    }

    function printLabels() {
        if (selectedFiles.length === 0 && !batchMode) {
            alert('Please select files to print labels for');
            return;
        }

        if (batchMode) {
            alert(`Printing ${batchCount} batch labels starting from ${batchStartNumber}`);
        } else {
            alert(`Printing ${selectedFiles.length} labels with ${copies} copies each`);
        }
    }

    // Event listeners
    elements.historyBtn.addEventListener('click', () => {
        elements.printHistory.classList.toggle('hidden');
    });

    elements.closeHistoryBtn.addEventListener('click', () => {
        elements.printHistory.classList.add('hidden');
    });

    elements.resetBtn.addEventListener('click', resetForm);
    elements.printLabelsBtn.addEventListener('click', printLabels);
    elements.finalPrintBtn.addEventListener('click', printLabels);

    elements.searchInput.addEventListener('input', (e) => {
        searchTerm = e.target.value;
        renderFilesList();
        updateCounts();
    });

    elements.batchModeCheckbox.addEventListener('change', (e) => {
        batchMode = e.target.checked;
        if (batchMode) {
            elements.batchControls.classList.remove('hidden');
        } else {
            elements.batchControls.classList.add('hidden');
        }
        updateCounts();
    });

    elements.batchStart.addEventListener('input', (e) => {
        batchStartNumber = parseInt(e.target.value) || 1;
    });

    elements.batchCount.addEventListener('input', (e) => {
        batchCount = parseInt(e.target.value) || 10;
        updateCounts();
    });

    elements.generateBatchBtn.addEventListener('click', () => {
        alert(`Generated ${batchCount} batch labels starting from ${batchStartNumber}`);
    });

    elements.selectAllCheckbox.addEventListener('change', selectAllFiles);

    elements.duplicateBtn.addEventListener('click', () => {
        copies++;
        elements.copiesInput.value = copies;
        alert(`Duplicated selected labels. Now printing ${copies} copies of each.`);
    });

    elements.continueToSettingsBtn.addEventListener('click', () => switchTab('settings'));
    elements.backToFilesBtn.addEventListener('click', () => switchTab('files'));
    elements.continueToPreviewBtn.addEventListener('click', () => switchTab('preview'));
    elements.backToSettingsBtn.addEventListener('click', () => switchTab('settings'));

    elements.advancedOptionsBtn.addEventListener('click', () => {
        showAdvancedOptions = !showAdvancedOptions;
        if (showAdvancedOptions) {
            elements.advancedOptions.classList.remove('hidden');
            elements.advancedOptionsBtn.textContent = 'Hide Advanced';
        } else {
            elements.advancedOptions.classList.add('hidden');
            elements.advancedOptionsBtn.textContent = 'Show Advanced';
        }
    });

    elements.formatBarcode.addEventListener('click', () => {
        labelFormat = 'barcode';
        elements.formatBarcode.classList.add('selected');
        elements.formatQrcode.classList.remove('selected');
    });

    elements.formatQrcode.addEventListener('click', () => {
        labelFormat = 'qrcode';
        elements.formatQrcode.classList.add('selected');
        elements.formatBarcode.classList.remove('selected');
    });

    elements.copiesInput.addEventListener('input', (e) => {
        copies = parseInt(e.target.value) || 1;
    });

    elements.exportPdfBtn.addEventListener('click', () => {
        alert('Exporting labels as PDF...');
    });

    elements.saveTemplateBtn.addEventListener('click', () => {
        const templateName = prompt('Enter a name for this template:');
        if (templateName) {
            alert(`Template "${templateName}" saved successfully!`);
        }
    });

    elements.importTemplateBtn.addEventListener('click', () => {
        alert('Import template functionality would open a file dialog here');
    });

    // Tab triggers
    elements.tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            switchTab(trigger.dataset.tab);
        });
    });

    // Global function for file selection (called from HTML)
    window.toggleFileSelection = toggleFileSelection;

    // Initialize the page - simplify since we don't need QR library check
    function init() {
        renderFilesList();
        updateCounts();
        lucide.createIcons();
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', init);

    // Additional initialization when window is fully loaded
    window.addEventListener('load', function() {
        // If we're on the preview tab, update it
        if (activeTab === 'preview') {
            updatePreview();
        }
    });
</script>