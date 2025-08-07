<script>
// Application state
let currentStep = 1;
const totalSteps = 6;

// Form data state
let formData = {};

// Development flags
let skipValidation = true; // Default to true for development
let autoFillEnabled = false;

// Sample data for auto-fill
const sampleData = {
    applicationDate: new Date().toISOString().split('T')[0],
    surname: 'IBRAHIM',
    firstName: 'MUHAMMAD',
    middleName: 'ALIYU',
    title: 'MR',
    occupation: 'ENGINEER',
    dateOfBirth: '1985-05-15',
    nationality: 'NIGERIAN',
    stateOfOrigin: 'KANO',
    lgaOfOrigin: 'KANO MUNICIPAL',
    nin: '12345678901',
    gender: 'male',
    maritalStatus: 'married',
    phoneNo: '08012345678',
    addressLine1: '123 MAIN STREET',
    cityTown: 'KANO',
    state: 'KANO',
    emailAddress: 'test@example.com',
    titleHolderSurname: 'IBRAHIM',
    titleHolderFirstName: 'MUHAMMAD',
    cofoNumber: 'KN/2023/001',
    isOriginalOwner: 'yes',
    isEncumbered: 'no',
    hasMortgage: 'no',
    plotNumber: 'PLOT 123',
    fileNumber: 'FILE/2023/001',
    plotSize: '0.5',
    layoutDistrict: 'GRA',
    lga: 'Kano Municipal',
    currentLandUse: 'residential',
    plotStatus: 'developed',
    modeOfAllocation: 'direct-allocation',
    paymentMethod: 'online',
    agreeTerms: true
};

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    console.log('Standalone Form - DOM Content Loaded');
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Set up event listeners
    setupEventListeners();
    
    // Set current date and update display
    setTimeout(() => {
        setCurrentDate();
        updateStepDisplay();
        setupDevelopmentControls();
    }, 100);
});

function setupEventListeners() {
    console.log('Setting up event listeners for standalone form...');
    
    // Navigation buttons
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', previousStep);
        console.log('Previous button event listener added');
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            console.log('Next button clicked!', e);
            nextStep(e);
        });
        console.log('Next button event listener added');
    }
    
    // Step indicator click navigation
    for (let i = 1; i <= totalSteps; i++) {
        const stepCircle = document.getElementById(`step-${i}`);
        if (stepCircle) {
            stepCircle.addEventListener('click', () => goToStep(i));
            stepCircle.style.cursor = 'pointer';
            stepCircle.title = `Go to Step ${i}`;
        }
    }
    
    // Form field updates
    const form = document.getElementById('recertification-form');
    if (form) {
        form.addEventListener('input', handleFormInput);
        form.addEventListener('change', handleFormChange);
        console.log('Form event listeners added');
    }
    
    // Conditional field displays
    setupConditionalFields();
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', handleKeyboardShortcuts);
    
    console.log('Event listeners setup complete');
}

function setupDevelopmentControls() {
    console.log('Setting up development controls...');
    
    // Skip validation checkbox
    const skipValidationCheckbox = document.getElementById('dev-skip-validation');
    if (skipValidationCheckbox) {
        skipValidationCheckbox.checked = skipValidation;
        skipValidationCheckbox.addEventListener('change', function() {
            skipValidation = this.checked;
            showToast(
                skipValidation ? 'Validation disabled for development' : 'Validation enabled',
                skipValidation ? 'warning' : 'info'
            );
        });
    }
    
    // Auto-fill checkbox
    const autoFillCheckbox = document.getElementById('dev-auto-fill');
    if (autoFillCheckbox) {
        autoFillCheckbox.addEventListener('change', function() {
            autoFillEnabled = this.checked;
            if (autoFillEnabled) {
                autoFillForm();
                showToast('Form auto-filled with sample data', 'info');
            } else {
                resetForm();
                showToast('Form reset', 'info');
            }
        });
    }
    
    // Debug button
    const debugBtn = document.getElementById('dev-debug-btn');
    if (debugBtn) {
        debugBtn.addEventListener('click', debugFormWizard);
    }
    
    // Reset button
    const resetBtn = document.getElementById('dev-reset-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            resetForm();
            showToast('Form reset to initial state', 'info');
        });
    }
}

function autoFillForm() {
    console.log('Auto-filling form with sample data...');
    
    // Fill text inputs
    Object.keys(sampleData).forEach(key => {
        const element = document.getElementById(key) || document.querySelector(`[name="${key}"]`);
        if (element) {
            if (element.type === 'checkbox') {
                element.checked = sampleData[key];
            } else if (element.type === 'radio') {
                const radioGroup = document.querySelectorAll(`[name="${key}"]`);
                radioGroup.forEach(radio => {
                    if (radio.value === sampleData[key]) {
                        radio.checked = true;
                    }
                });
            } else {
                element.value = sampleData[key];
            }
            
            // Update form data
            formData[key] = sampleData[key];
        }
    });
    
    // Trigger change events for conditional fields
    document.querySelectorAll('input[name="isOriginalOwner"]').forEach(radio => {
        if (radio.checked) radio.dispatchEvent(new Event('change'));
    });
    
    document.querySelectorAll('input[name="isEncumbered"]').forEach(radio => {
        if (radio.checked) radio.dispatchEvent(new Event('change'));
    });
    
    document.querySelectorAll('input[name="hasMortgage"]').forEach(radio => {
        if (radio.checked) radio.dispatchEvent(new Event('change'));
    });
}

function setupConditionalFields() {
    // Original owner conditional fields
    document.querySelectorAll('input[name="isOriginalOwner"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const ownershipDetails = document.getElementById('ownership-details');
            if (ownershipDetails) {
                if (this.value === 'no') {
                    ownershipDetails.classList.remove('hidden');
                } else {
                    ownershipDetails.classList.add('hidden');
                }
            }
        });
    });
    
    // Encumbrance conditional fields
    document.querySelectorAll('input[name="isEncumbered"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const encumbranceReason = document.getElementById('encumbrance-reason');
            if (encumbranceReason) {
                if (this.value === 'yes') {
                    encumbranceReason.classList.remove('hidden');
                } else {
                    encumbranceReason.classList.add('hidden');
                }
            }
        });
    });
    
    // Mortgage conditional fields
    document.querySelectorAll('input[name="hasMortgage"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const mortgageDetails = document.getElementById('mortgage-details');
            if (mortgageDetails) {
                if (this.value === 'yes') {
                    mortgageDetails.classList.remove('hidden');
                } else {
                    mortgageDetails.classList.add('hidden');
                }
            }
        });
    });
}

function handleFormInput(event) {
    const { name, value } = event.target;
    if (name) {
        formData[name] = value;
        clearFieldError(name);
    }
}

function handleFormChange(event) {
    const { name, value, type, checked } = event.target;
    if (name) {
        if (type === 'checkbox') {
            formData[name] = checked;
        } else {
            formData[name] = value;
        }
        clearFieldError(name);
    }
}

function setCurrentDate() {
    const today = new Date().toISOString().split('T')[0];
    const applicationDateField = document.getElementById('applicationDate');
    if (applicationDateField) {
        applicationDateField.value = today;
        formData.applicationDate = today;
    }
}

function updateStepDisplay() {
    console.log('Updating step display, current step:', currentStep);
    
    // Hide all step contents
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show current step content
    const currentStepContent = document.getElementById(`step-content-${currentStep}`);
    if (currentStepContent) {
        currentStepContent.classList.remove('hidden');
    }
    
    // Update step indicators
    for (let i = 1; i <= totalSteps; i++) {
        const stepCircle = document.getElementById(`step-${i}`);
        const stepLine = document.getElementById(`line-${i}`);
        
        if (stepCircle) {
            if (i <= currentStep) {
                stepCircle.classList.remove('inactive');
                stepCircle.classList.add('active');
            } else {
                stepCircle.classList.remove('active');
                stepCircle.classList.add('inactive');
            }
        }
        
        if (stepLine) {
            if (i < currentStep) {
                stepLine.classList.remove('inactive');
                stepLine.classList.add('active');
            } else {
                stepLine.classList.remove('active');
                stepLine.classList.add('inactive');
            }
        }
    }
    
    // Update navigation buttons
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const nextText = nextBtn?.querySelector('.next-text');
    
    if (prevBtn) {
        prevBtn.disabled = currentStep === 1;
    }
    
    if (nextText) {
        if (currentStep === totalSteps) {
            nextText.textContent = 'Submit Application';
        } else {
            nextText.textContent = 'Next';
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
        showToast(`Moved to Step ${currentStep}`, 'info');
    }
}

async function nextStep(event) {
    console.log('nextStep called, currentStep:', currentStep);
    
    if (currentStep < totalSteps) {
        // Check for development bypass
        const forceSkip = event && (event.ctrlKey || event.metaKey);
        
        if (skipValidation || forceSkip || validateCurrentStep()) {
            console.log('Moving to next step...');
            currentStep++;
            updateStepDisplay();
            
            if (forceSkip) {
                showToast('Validation bypassed with Ctrl+Click', 'warning');
            } else if (skipValidation) {
                showToast(`Step ${currentStep - 1} completed (validation skipped)`, 'info');
            } else {
                showToast(`Step ${currentStep - 1} completed`, 'success');
            }
        } else {
            console.log('Validation failed, staying on current step');
        }
    } else {
        console.log('On final step, submitting form...');
        await submitForm();
    }
}

function validateCurrentStep() {
    if (skipValidation) {
        return true;
    }
    
    const currentStepElement = document.getElementById(`step-content-${currentStep}`);
    if (!currentStepElement) {
        console.warn('Current step element not found');
        return true; // Allow progression if step element is missing
    }
    
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;
    
    // Clear previous errors
    currentStepElement.querySelectorAll('.form-field').forEach(field => {
        field.classList.remove('error');
    });
    
    // Validate required fields
    requiredFields.forEach(field => {
        const value = field.type === 'checkbox' ? field.checked : field.value;
        const isRadioGroup = field.type === 'radio';
        
        if (isRadioGroup) {
            const radioGroup = currentStepElement.querySelectorAll(`input[name="${field.name}"]`);
            const isChecked = Array.from(radioGroup).some(radio => radio.checked);
            if (!isChecked) {
                showFieldError(field.name);
                isValid = false;
            }
        } else if (!value || (typeof value === 'string' && value.trim() === '')) {
            showFieldError(field.name);
            isValid = false;
        }
    });
    
    // Additional validation for step 6 (terms agreement)
    if (currentStep === 6) {
        const agreeTerms = document.getElementById('agreeTerms');
        if (agreeTerms && !agreeTerms.checked) {
            showFieldError('agreeTerms');
            isValid = false;
        }
    }
    
    if (!isValid) {
        showToast('Please fill in all required fields correctly', 'error');
        // Scroll to first error field
        const firstErrorField = currentStepElement.querySelector('.form-field.error');
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    return isValid;
}

function showFieldError(fieldName) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (field) {
        const formField = field.closest('.form-field');
        if (formField) {
            formField.classList.add('error');
        }
    }
}

function clearFieldError(fieldName) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (field) {
        const formField = field.closest('.form-field');
        if (formField) {
            formField.classList.remove('error');
        }
    }
}

async function submitForm() {
    const nextBtn = document.getElementById('next-btn');
    const nextText = nextBtn?.querySelector('.next-text');
    const loadingSpinner = nextBtn?.querySelector('.loading-spinner');
    
    // Show loading state
    if (nextBtn) nextBtn.disabled = true;
    if (nextText) nextText.textContent = 'Submitting...';
    if (loadingSpinner) loadingSpinner.classList.remove('hidden');
    
    try {
        // Collect all form data
        const form = document.getElementById('recertification-form');
        const currentFormData = new FormData(form);
        const applicationData = Object.fromEntries(currentFormData.entries());
        
        // Merge with tracked form data
        const finalData = { ...formData, ...applicationData };
        
        // Simulate API call
        console.log('Submitting application data:', finalData);
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Show success message
        showToast('Application submitted successfully!', 'success');
        
        // Redirect after delay
        setTimeout(() => {
            window.location.href = '/recertification';
        }, 2000);
        
    } catch (error) {
        console.error('Error submitting application:', error);
        showToast('Failed to submit application. Please try again.', 'error');
    } finally {
        // Reset loading state
        if (nextBtn) nextBtn.disabled = false;
        if (nextText) nextText.textContent = 'Submit Application';
        if (loadingSpinner) loadingSpinner.classList.add('hidden');
    }
}

function resetForm() {
    // Reset step
    currentStep = 1;
    updateStepDisplay();
    
    // Reset form
    const form = document.getElementById('recertification-form');
    if (form) {
        form.reset();
    }
    
    // Reset form data
    formData = {};
    
    // Clear all errors
    document.querySelectorAll('.form-field').forEach(field => {
        field.classList.remove('error');
    });
    
    // Hide conditional fields
    const ownershipDetails = document.getElementById('ownership-details');
    const encumbranceReason = document.getElementById('encumbrance-reason');
    const mortgageDetails = document.getElementById('mortgage-details');
    
    if (ownershipDetails) ownershipDetails.classList.add('hidden');
    if (encumbranceReason) encumbranceReason.classList.add('hidden');
    if (mortgageDetails) mortgageDetails.classList.add('hidden');
    
    // Reset development controls
    const skipValidationCheckbox = document.getElementById('dev-skip-validation');
    const autoFillCheckbox = document.getElementById('dev-auto-fill');
    
    if (skipValidationCheckbox) skipValidationCheckbox.checked = true;
    if (autoFillCheckbox) autoFillCheckbox.checked = false;
    
    skipValidation = true;
    autoFillEnabled = false;
    
    // Set current date again
    setCurrentDate();
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toastId = `toast-${Date.now()}`;
    
    const typeClasses = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-600 text-white',
        warning: 'bg-yellow-600 text-white',
        info: 'bg-blue-600 text-white'
    };
    
    const typeIcons = {
        success: 'check-circle',
        error: 'alert-circle',
        warning: 'alert-triangle',
        info: 'info'
    };
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${typeClasses[type]} px-4 py-2 rounded-md shadow-lg flex items-center gap-2 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
        <i data-lucide="${typeIcons[type]}" class="h-4 w-4"></i>
        <span>${message}</span>
        <button onclick="removeToast('${toastId}')" class="ml-2 hover:bg-black/20 rounded p-1">
            <i data-lucide="x" class="h-3 w-3"></i>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        removeToast(toastId);
    }, 5000);
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}

// Step navigation functions
function goToStep(stepNumber) {
    if (stepNumber >= 1 && stepNumber <= totalSteps) {
        currentStep = stepNumber;
        updateStepDisplay();
        showToast(`Navigated to Step ${stepNumber}`, 'info');
    }
}

function handleKeyboardShortcuts(event) {
    // Ctrl/Cmd + Arrow keys for navigation
    if (event.ctrlKey || event.metaKey) {
        switch(event.key) {
            case 'ArrowLeft':
                event.preventDefault();
                previousStep();
                break;
            case 'ArrowRight':
                event.preventDefault();
                nextStep(event);
                break;
        }
    }
    
    // Number keys to jump to steps (Ctrl/Cmd + 1-6)
    if (event.key >= '1' && event.key <= '6' && (event.ctrlKey || event.metaKey)) {
        event.preventDefault();
        goToStep(parseInt(event.key));
    }
    
    // Escape to reset form
    if (event.key === 'Escape' && (event.ctrlKey || event.metaKey)) {
        event.preventDefault();
        resetForm();
        showToast('Form reset via keyboard shortcut', 'info');
    }
}

// Debug function for development
function debugFormWizard() {
    console.log('=== Standalone Form Wizard Debug Info ===');
    console.log('Current Step:', currentStep);
    console.log('Total Steps:', totalSteps);
    console.log('Form Data:', formData);
    console.log('Skip Validation:', skipValidation);
    console.log('Auto Fill Enabled:', autoFillEnabled);
    
    // Check if all step elements exist
    for (let i = 1; i <= totalSteps; i++) {
        const stepContent = document.getElementById(`step-content-${i}`);
        const stepCircle = document.getElementById(`step-${i}`);
        console.log(`Step ${i}:`, {
            content: stepContent ? 'exists' : 'missing',
            circle: stepCircle ? 'exists' : 'missing',
            visible: stepContent && !stepContent.classList.contains('hidden')
        });
    }
    
    // Check navigation buttons
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    console.log('Navigation buttons:', {
        prev: prevBtn ? 'exists' : 'missing',
        next: nextBtn ? 'exists' : 'missing'
    });
    
    // Show debug info in toast
    showToast('Debug info logged to console', 'info');
}

// Make functions available globally for debugging
window.debugFormWizard = debugFormWizard;
window.goToStep = goToStep;
window.autoFillForm = autoFillForm;
window.resetForm = resetForm;

// Quick test functions
window.testNextStep = function() {
    console.log('Testing next step...');
    nextStep();
};

window.testValidation = function() {
    skipValidation = !skipValidation;
    const checkbox = document.getElementById('dev-skip-validation');
    if (checkbox) checkbox.checked = skipValidation;
    showToast(`Validation ${skipValidation ? 'disabled' : 'enabled'}`, 'info');
};

console.log('Standalone form wizard initialized with development features');
</script>