// OCR Modal Functions
function closeOcrModal() {
    const modal = document.getElementById('ocr-modal');
    if (modal) modal.classList.add('hidden');
    
    // Reset form
    const form = document.getElementById('metadata-extraction-form');
    if (form) form.reset();
    
    // Hide form and show buttons
    const ocrForm = document.getElementById('ocr-extraction-form');
    const saveBtn = document.getElementById('save-extraction-btn');
    const continueBtn = document.getElementById('continue-processing-btn');
    if (ocrForm) ocrForm.classList.add('hidden');
    if (saveBtn) saveBtn.classList.add('hidden');
    if (continueBtn) continueBtn.classList.add('hidden');
}

function saveExtractionData() {
    // Get form data
    const fileNumber = document.getElementById('ocr-file-number').value;
    const plotNumber = document.getElementById('ocr-plot-number').value;
    const ownerName = document.getElementById('ocr-owner-name').value;
    const landUse = document.getElementById('ocr-land-use').value;
    const district = document.getElementById('ocr-district').value;
    const documentType = document.getElementById('ocr-document-type').value;
    const extractedText = document.getElementById('ocr-extracted-text').value;
    const notes = document.getElementById('ocr-notes').value;

    // Here you would normally send this data to the server
    console.log('Saving extraction data:', {
        fileNumber,
        plotNumber,
        ownerName,
        landUse,
        district,
        documentType,
        extractedText,
        notes
    });

    showNotification('Extraction data saved successfully!', 'success');
    closeOcrModal();
}

function continueProcessing() {
    // Continue with the processing workflow
    closeOcrModal();
    
    // Show AI processing section
    const aiDiv = document.getElementById('ai-processing');
    if (aiDiv) aiDiv.classList.remove('hidden');
    
    showNotification('Continuing with document processing...', 'info');
}

// Function to show extraction form after OCR completes
function showExtractionForm(extractedText = '') {
    const form = document.getElementById('ocr-extraction-form');
    const saveBtn = document.getElementById('save-extraction-btn');
    const continueBtn = document.getElementById('continue-processing-btn');
    const textArea = document.getElementById('ocr-extracted-text');
    
    if (form) form.classList.remove('hidden');
    if (saveBtn) saveBtn.classList.remove('hidden');
    if (continueBtn) continueBtn.classList.remove('hidden');
    if (textArea) textArea.value = extractedText;
    
    // Re-initialize icons for the form
    lucide.createIcons();
}

// Make functions globally available
window.closeOcrModal = closeOcrModal;
window.saveExtractionData = saveExtractionData;
window.continueProcessing = continueProcessing;
window.showExtractionForm = showExtractionForm;