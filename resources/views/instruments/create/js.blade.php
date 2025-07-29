<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // State management
    let currentInstrumentType = null;
    let tempFileCounter = 1;

    // Complete instrument type definitions for all 18 types
    const instrumentTypes = {
        'power-of-attorney': {
            id: 'power-of-attorney',
            name: 'Power of Attorney',
            firstParty: 'Grantor',
            secondParty: 'Grantee',
            needsRootReg: true
        },
        'irrevocable-power-of-attorney': {
            id: 'irrevocable-power-of-attorney',
            name: 'Irrevocable Power of Attorney',
            firstParty: 'Grantor',
            secondParty: 'Grantee',
            needsRootReg: true
        },
        'deed-of-mortgage': {
            id: 'deed-of-mortgage',
            name: 'Deed of Mortgage',
            firstParty: 'Mortgagor',
            secondParty: 'Mortgagee',
            needsRootReg: true
        },
        'tripartite-mortgage': {
            id: 'tripartite-mortgage',
            name: 'Tripartite Mortgage',
            firstParty: 'Mortgagor',
            secondParty: 'Mortgagee',
            needsRootReg: true
        },
        'deed-of-assignment': {
            id: 'deed-of-assignment',
            name: 'Deed of Assignment',
            firstParty: 'Assignor',
            secondParty: 'Assignee',
            needsRootReg: true
        },
        'deed-of-lease': {
            id: 'deed-of-lease',
            name: 'Deed of Lease',
            firstParty: 'Lessor',
            secondParty: 'Lessee',
            needsRootReg: true
        },
        'deed-of-sub-lease': {
            id: 'deed-of-sub-lease',
            name: 'Deed of Sub-Lease',
            firstParty: 'Sub-Lessor',
            secondParty: 'Sub-Lessee',
            needsRootReg: true
        },
        'deed-of-sub-under-lease': {
            id: 'deed-of-sub-under-lease',
            name: 'Deed of Sub-Under-Lease',
            firstParty: 'Sub-Under-Lessor',
            secondParty: 'Sub-Under-Lessee',
            needsRootReg: true
        },
        'deed-of-sub-division': {
            id: 'deed-of-sub-division',
            name: 'Deed of Sub-Division',
            firstParty: 'Subdivider',
            secondParty: 'Beneficiary',
            needsRootReg: true
        },
        'deed-of-merger': {
            id: 'deed-of-merger',
            name: 'Deed of Merger',
            firstParty: 'Merging Party',
            secondParty: 'Receiving Party',
            needsRootReg: true
        },
        'deed-of-surrender': {
            id: 'deed-of-surrender',
            name: 'Deed of Surrender',
            firstParty: 'Surrenderer',
            secondParty: 'Surrenderor',
            needsRootReg: true
        },
        'deed-of-variation': {
            id: 'deed-of-variation',
            name: 'Deed of Variation',
            firstParty: 'Party',
            secondParty: 'Counterparty',
            needsRootReg: true
        },
        'deed-of-assent': {
            id: 'deed-of-assent',
            name: 'Deed of Assent',
            firstParty: 'Executor/Administrator',
            secondParty: 'Beneficiary',
            needsRootReg: true
        },
        'deed-of-release': {
            id: 'deed-of-release',
            name: 'Deed of Release',
            firstParty: 'Releasor',
            secondParty: 'Releasee',
            needsRootReg: true
        },
        'right-of-occupancy': {
            id: 'right-of-occupancy',
            name: 'Right of Occupancy (R of O)',
            firstParty: 'Holder',
            secondParty: 'Authority',
            needsRootReg: false
        },
        'certificate-of-occupancy': {
            id: 'certificate-of-occupancy',
            name: 'Certificate of Occupancy (C of O)',
            firstParty: 'Holder',
            secondParty: 'Authority',
            needsRootReg: false
        },
        'sectional-titling-c-of-o': {
            id: 'sectional-titling-c-of-o',
            name: 'Sectional Titling Certificate of Occupancy',
            firstParty: 'Grantor',
            secondParty: 'Grantee',
            needsRootReg: false,
            autoSetGrantor: true
        },
        'sltr-c-of-o': {
            id: 'sltr-c-of-o',
            name: 'Systematic Land Titling and Registration (SLTR) Certificate of Occupancy',
            firstParty: 'Holder',
            secondParty: 'Authority',
            needsRootReg: false
        },
        'st-assignment': {
            id: 'st-assignment',
            name: 'ST Assignment (Transfer of Title)',
            firstParty: 'Grantor',
            secondParty: 'Grantee',
            needsRootReg: true,
            autoSetGrantor: true
        }
    };

    // DOM elements
    const elements = {
        registrationDialog: document.getElementById('registration-dialog'),
        dialogTitle: document.getElementById('dialog-title'),
        registrationForm: document.getElementById('registration-form'),
        cancelBtn: document.getElementById('cancel-btn'),
        submitBtn: document.getElementById('submit-btn'),
        isTemporaryFileNo: document.getElementById('isTemporaryFileNo'),
        isTemporaryRegNo: document.getElementById('isTemporaryRegNo'),
        temporaryFileNo: document.getElementById('temporaryFileNo'),
        regenerateTempBtn: document.getElementById('regenerate-temp-btn'),
        temporaryFileSection: document.getElementById('temporary-file-section'),
        regularFileSection: document.getElementById('regular-file-section'),
        regNoSection: document.getElementById('reg-no-section'),
        rootRegNoSection: document.getElementById('rootRegNoSection'),
        firstPartyTitle: document.getElementById('first-party-title'),
        firstPartyLabel: document.getElementById('first-party-label'),
        secondPartyTitle: document.getElementById('second-party-title'),
        secondPartyLabel: document.getElementById('second-party-label'),
        surveyInfo: document.getElementById('surveyInfo'),
        surveyInfoSection: document.getElementById('survey-info-section'),
        instrumentFields: document.getElementById('instrument-fields')
    };

    // Helper functions
    function generateTemporaryFileNo() {
        const paddedCounter = String(tempFileCounter).padStart(4, '0');
        tempFileCounter++;
        return `TEMP-${paddedCounter}`;
    }

    
    function updatePartyLabels(instrumentType) {
        const type = instrumentTypes[instrumentType];
        if (!type) return;

        elements.firstPartyTitle.textContent = `${type.firstParty} Information`;
        elements.firstPartyLabel.textContent = `${type.firstParty} Name`;
        elements.secondPartyTitle.textContent = `${type.secondParty} Information`;
        elements.secondPartyLabel.textContent = `${type.secondParty} Name`;

        // Update address labels
        const firstPartyAddressTitle = document.getElementById('first-party-address-title');
        const secondPartyAddressTitle = document.getElementById('second-party-address-title');
        
        if (firstPartyAddressTitle) {
            firstPartyAddressTitle.textContent = `${type.firstParty} Address`;
        }
        if (secondPartyAddressTitle) {
            secondPartyAddressTitle.textContent = `${type.secondParty} Address`;
        }

        // Update placeholders
        document.getElementById('firstPartyName').placeholder = `Enter ${type.firstParty.toLowerCase()}'s full name`;
        document.getElementById('secondPartyName').placeholder = `Enter ${type.secondParty.toLowerCase()}'s full name`;
    }

    function renderInstrumentSpecificFields(instrumentType) {
        const fieldsContainer = elements.instrumentFields;
        fieldsContainer.innerHTML = '';

        switch (instrumentType) {
            case 'power-of-attorney':
            case 'irrevocable-power-of-attorney':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="duration" class="label">Duration</label>
                        <input id="duration" name="duration" class="input" placeholder="Enter duration (e.g., 5 years)">
                    </div>
                `;
                break;
            case 'deed-of-mortgage':
            case 'tripartite-mortgage':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="bankName" class="label">Bank Name</label>
                        <input id="bankName" name="bankName" class="input" placeholder="Enter bank name">
                    </div>
                    <div class="space-y-2">
                        <label for="mortgageDate" class="label">Mortgage Date</label>
                        <input id="mortgageDate" name="mortgageDate" type="date" class="input">
                    </div>
                    <div class="space-y-2">
                        <label for="governorSignDate" class="label">Governor Sign Date</label>
                        <input id="governorSignDate" name="governorSignDate" type="date" class="input">
                    </div>
                `;
                break;
            case 'deed-of-assignment':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="assignmentTerm" class="label">Assignment Term</label>
                        <input id="assignmentTerm" name="assignmentTerm" class="input" placeholder="Enter assignment term">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoDate" class="label">CofO Date</label>
                        <input id="cofoDate" name="cofoDate" type="date" class="input">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoRegParticulars" class="label">CofO Reg Particulars</label>
                        <input id="cofoRegParticulars" name="cofoRegParticulars" class="input" placeholder="Enter CofO registration particulars">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoTerm" class="label">CofO Term</label>
                        <input id="cofoTerm" name="cofoTerm" class="input" placeholder="Enter CofO term">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoTermStartDate" class="label">CofO Term Start Date</label>
                        <input id="cofoTermStartDate" name="cofoTermStartDate" type="date" class="input">
                    </div>
                `;
                break;
            case 'deed-of-lease':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="leaseTerm" class="label">Lease Term</label>
                        <input id="leaseTerm" name="leaseTerm" class="input" placeholder="Enter lease term (e.g., 99 years)">
                    </div>
                    <div class="space-y-2">
                        <label for="annualRent" class="label">Annual Rent</label>
                        <input id="annualRent" name="annualRent" class="input" placeholder="Enter annual rent">
                    </div>
                    <div class="space-y-2">
                        <label for="chiefMagistrateSignDate" class="label">Chief Magistrate Sign Date</label>
                        <input id="chiefMagistrateSignDate" name="chiefMagistrateSignDate" type="date" class="input">
                    </div>
                `;
                break;
            case 'deed-of-sub-lease':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="leaseTerm" class="label">Lease Term</label>
                        <input id="leaseTerm" name="leaseTerm" class="input" placeholder="Enter lease term (e.g., 99 years)">
                    </div>
                    <div class="space-y-2">
                        <label for="subLeaseAmount" class="label">Sub-Lease Amount</label>
                        <input id="subLeaseAmount" name="subLeaseAmount" class="input" placeholder="Enter sub-lease amount">
                    </div>
                    <div class="space-y-2">
                        <label for="assignmentTerm" class="label">Assignment Term</label>
                        <input id="assignmentTerm" name="assignmentTerm" class="input" placeholder="Enter assignment term">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoDate" class="label">CofO Date</label>
                        <input id="cofoDate" name="cofoDate" type="date" class="input">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoRegParticulars" class="label">CofO Reg Particulars</label>
                        <input id="cofoRegParticulars" name="cofoRegParticulars" class="input" placeholder="Enter CofO registration particulars">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoTerm" class="label">CofO Term</label>
                        <input id="cofoTerm" name="cofoTerm" class="input" placeholder="Enter CofO term">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoTermStartDate" class="label">CofO Term Start Date</label>
                        <input id="cofoTermStartDate" name="cofoTermStartDate" type="date" class="input">
                    </div>
                `;
                break;
            case 'deed-of-sub-under-lease':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="leaseTerm" class="label">Lease Term</label>
                        <input id="leaseTerm" name="leaseTerm" class="input" placeholder="Enter lease term (e.g., 99 years)">
                    </div>
                    <div class="space-y-2">
                        <label for="leaseAmount" class="label">Lease Amount</label>
                        <input id="leaseAmount" name="leaseAmount" class="input" placeholder="Enter lease amount">
                    </div>
                `;
                break;
            case 'deed-of-sub-division':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="numberOfPlots" class="label">Number of Plots</label>
                        <input id="numberOfPlots" name="numberOfPlots" type="number" class="input" placeholder="Enter number of plots">
                    </div>
                    <div class="space-y-2">
                        <label for="originalPlotSize" class="label">Original Plot Size</label>
                        <input id="originalPlotSize" name="originalPlotSize" class="input" placeholder="Enter original plot size">
                    </div>
                `;
                break;
            case 'deed-of-merger':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="mergingProperties" class="label">Merging Properties</label>
                        <input id="mergingProperties" name="mergingProperties" class="input" placeholder="Enter merging properties">
                    </div>
                    <div class="space-y-2">
                        <label for="resultingProperty" class="label">Resulting Property</label>
                        <input id="resultingProperty" name="resultingProperty" class="input" placeholder="Enter resulting property">
                    </div>
                `;
                break;
            case 'deed-of-surrender':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="surrenderReason" class="label">Reason for Surrender</label>
                        <input id="surrenderReason" name="surrenderReason" class="input" placeholder="Enter reason for surrender">
                    </div>
                    <div class="space-y-2">
                        <label for="compensationAmount" class="label">Compensation Amount</label>
                        <input id="compensationAmount" name="compensationAmount" class="input" placeholder="Enter compensation amount (if any)">
                    </div>
                `;
                break;
            case 'deed-of-assent':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="deceasedName" class="label">Deceased Name</label>
                        <input id="deceasedName" name="deceasedName" class="input" placeholder="Enter deceased's full name">
                    </div>
                    <div class="space-y-2">
                        <label for="dateOfDeath" class="label">Date of Death</label>
                        <input id="dateOfDeath" name="dateOfDeath" type="date" class="input">
                    </div>
                    <div class="space-y-2">
                        <label for="willReference" class="label">Will Reference</label>
                        <input id="willReference" name="willReference" class="input" placeholder="Enter will reference number">
                    </div>
                `;
                break;
            case 'deed-of-release':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="bankName" class="label">Bank Name</label>
                        <input id="bankName" name="bankName" class="input" placeholder="Enter bank name">
                    </div>
                    <div class="space-y-2">
                        <label for="releaseRegParticulars" class="label">Release Reg Particulars</label>
                        <input id="releaseRegParticulars" name="releaseRegParticulars" class="input" placeholder="Enter release registration particulars">
                    </div>
                    <div class="space-y-2">
                        <label for="originalInstrumentRegParticulars" class="label">Original Instrument Reg Particulars</label>
                        <input id="originalInstrumentRegParticulars" name="originalInstrumentRegParticulars" class="input" placeholder="Enter original instrument registration particulars">
                    </div>
                    <div class="space-y-2">
                        <label for="releaseAmount" class="label">Release Amount</label>
                        <input id="releaseAmount" name="releaseAmount" class="input" placeholder="Enter release amount (if applicable)">
                    </div>
                `;
                break;
            case 'deed-of-variation':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="variationDetails" class="label">Variation Details</label>
                        <textarea id="variationDetails" name="variationDetails" class="textarea" placeholder="Describe the variation"></textarea>
                    </div>
                `;
                break;
            case 'right-of-occupancy':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="rOfONumber" class="label">R of O Number</label>
                        <input id="rOfONumber" name="rOfONumber" class="input" placeholder="Enter Right of Occupancy number">
                    </div>
                `;
                break;
            case 'certificate-of-occupancy':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="cOfONumber" class="label">C of O Number</label>
                        <input id="cOfONumber" name="cOfONumber" class="input" placeholder="Enter Certificate of Occupancy number">
                    </div>
                `;
                break;
            case 'sectional-titling-c-of-o':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="unitNumber" class="label">Unit Number</label>
                        <input id="unitNumber" name="unitNumber" class="input" placeholder="Enter unit number">
                    </div>
                    <div class="space-y-2">
                        <label for="sectionalCofONumber" class="label">Sectional C of O Number</label>
                        <input id="sectionalCofONumber" name="sectionalCofONumber" class="input" placeholder="Enter Sectional C of O number">
                    </div>
                `;
                break;
            case 'sltr-c-of-o':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="sltrCofONumber" class="label">SLTR C of O Number</label>
                        <input id="sltrCofONumber" name="sltrCofONumber" class="input" placeholder="Enter SLTR C of O number">
                    </div>
                `;
                break;
            case 'st-assignment':
                fieldsContainer.innerHTML = `
                    <div class="space-y-2">
                        <label for="assignmentTerm" class="label">Assignment Term</label>
                        <input id="assignmentTerm" name="assignmentTerm" class="input" placeholder="Enter assignment term">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoDate" class="label">CofO Date</label>
                        <input id="cofoDate" name="cofoDate" type="date" class="input">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoRegParticulars" class="label">CofO Reg Particulars</label>
                        <input id="cofoRegParticulars" name="cofoRegParticulars" class="input" placeholder="Enter CofO registration particulars">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoTerm" class="label">CofO Term</label>
                        <input id="cofoTerm" name="cofoTerm" class="input" placeholder="Enter CofO term">
                    </div>
                    <div class="space-y-2">
                        <label for="cofoTermStartDate" class="label">CofO Term Start Date</label>
                        <input id="cofoTermStartDate" name="cofoTermStartDate" type="date" class="input">
                    </div>
                    <div class="space-y-2">
                        <label for="stFileNo" class="label">ST File Number</label>
                        <input id="stFileNo" name="stFileNo" class="input" placeholder="Enter ST file number">
                    </div>
                `;
                break;
            default:
                // For any new types not handled above, leave blank or add a comment
                break;
        }
    }

    function openRegistrationDialog(instrumentType) {
        currentInstrumentType = instrumentType;
        const type = instrumentTypes[instrumentType];
        
        elements.dialogTitle.textContent = `Register ${type.name}`;
        updatePartyLabels(instrumentType);
        renderInstrumentSpecificFields(instrumentType);
        
        // Auto-set grantor for ST Assignment and Sectional Titling CofO
        if (type.autoSetGrantor) {
            const firstPartyNameField = document.getElementById('firstPartyName');
            if (firstPartyNameField) {
                firstPartyNameField.value = 'Kano State Government';
                firstPartyNameField.readOnly = true;
                firstPartyNameField.style.backgroundColor = '#f3f4f6'; // Light gray background
                firstPartyNameField.style.cursor = 'not-allowed';
            }
            
            // Also set the address fields for Kano State Government
            const addressFields = {
                'firstPartyStreet': 'Government House',
                'firstPartyCity': 'Kano',
                'firstPartyState': 'Kano State',
                'firstPartyPostalCode': '700001',
                'firstPartyCountry': 'Nigeria'
            };
            
            Object.entries(addressFields).forEach(([fieldId, value]) => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.value = value;
                    field.readOnly = true;
                    field.style.backgroundColor = '#f3f4f6';
                    field.style.cursor = 'not-allowed';
                }
            });
        } else {
            // Reset fields for other instrument types
            const firstPartyNameField = document.getElementById('firstPartyName');
            if (firstPartyNameField) {
                firstPartyNameField.value = '';
                firstPartyNameField.readOnly = false;
                firstPartyNameField.style.backgroundColor = '';
                firstPartyNameField.style.cursor = '';
            }
            
            const addressFieldIds = ['firstPartyStreet', 'firstPartyCity', 'firstPartyState', 'firstPartyPostalCode', 'firstPartyCountry'];
            addressFieldIds.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.value = '';
                    field.readOnly = false;
                    field.style.backgroundColor = '';
                    field.style.cursor = '';
                }
            });
        }
        
        // Show/hide registration number sections based on whether instrument needs Root Reg
        const registrationDetailsSection = document.getElementById('registration-details-section');
        if (type.needsRootReg) {
            // For instruments that need Root Reg, show the registration details section
            if (registrationDetailsSection) {
                registrationDetailsSection.classList.remove('hidden');
            }
            
            // Initialize the temporary reg number section visibility
            handleTemporaryRegNoChange();
        } else {
            // For instruments that don't need Root Reg, hide the entire registration details section
            if (registrationDetailsSection) {
                registrationDetailsSection.classList.add('hidden');
            }
        }

        // Always update survey info section visibility based on checkbox state
        handleSurveyInfoChange();

        elements.registrationDialog.classList.remove('hidden');
    }

    function closeRegistrationDialog() {
        elements.registrationDialog.classList.add('hidden');
        currentInstrumentType = null;
        elements.registrationForm.reset();
        
        // Reset registration details section visibility
        const registrationDetailsSection = document.getElementById('registration-details-section');
        if (registrationDetailsSection) {
            registrationDetailsSection.classList.remove('hidden');
        }
        
        // Reset checkboxes to unchecked state
        if (elements.isTemporaryFileNo) {
            elements.isTemporaryFileNo.checked = false;
        }
        if (elements.isTemporaryRegNo) {
            elements.isTemporaryRegNo.checked = false;
        }
        
        // Reset section visibility
        handleTemporaryFileNoChange();
        handleTemporaryRegNoChange();
    }

    function handleTemporaryFileNoChange() {
        const isChecked = elements.isTemporaryFileNo.checked;
        if (isChecked) {
            elements.temporaryFileSection.classList.remove('hidden');
            elements.regularFileSection.classList.add('hidden');
            if (!elements.temporaryFileNo.value) {
                elements.temporaryFileNo.value = generateTemporaryFileNo();
            }
        } else {
            elements.temporaryFileSection.classList.add('hidden');
            elements.regularFileSection.classList.remove('hidden');
            elements.temporaryFileNo.value = '';
        }
    }

    function handleTemporaryRegNoChange() {
        const isChecked = elements.isTemporaryRegNo.checked;
        if (isChecked) {
            elements.regNoSection.classList.remove('hidden');
            elements.rootRegNoSection.classList.add('hidden');
        } else {
            elements.regNoSection.classList.add('hidden');
            elements.rootRegNoSection.classList.remove('hidden');
        }
    }

    function handleSurveyInfoChange() {
        console.log('Survey info changed'); // Debug log
        const surveyCheckbox = document.getElementById('surveyInfo');
        const surveySection = document.getElementById('survey-info-section');
        
        if (!surveyCheckbox || !surveySection) {
            console.error('Survey elements not found'); // Debug log
            return;
        }

        if (surveyCheckbox.checked) {
            surveySection.classList.remove('hidden');
        } else {
            surveySection.classList.add('hidden');
            // Clear survey fields
            document.getElementById('lga').value = '';
            document.getElementById('district').value = '';
            document.getElementById('plotNumber').value = '';
        }
    }

    function collectFormData() {
        const formData = new FormData(elements.registrationForm);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        // Add instrument type
        data.instrumentType = currentInstrumentType;
        
        // Add final file number (temporary or regular)
        if (elements.isTemporaryFileNo.checked) {
            data.finalFileNo = elements.temporaryFileNo.value;
            data.isTemporary = true;
        } else {
            // Get the active file number from the file number tabs
            const activeTab = document.getElementById('activeFileTab')?.value;
            if (activeTab === 'mlsFNo') {
                data.finalFileNo = document.getElementById('mlsFNo')?.value || '';
            } else if (activeTab === 'kangisFileNo') {
                data.finalFileNo = document.getElementById('kangisFileNo')?.value || '';
            } else if (activeTab === 'NewKANGISFileno') {
                data.finalFileNo = document.getElementById('NewKANGISFileno')?.value || '';
            }
            data.isTemporary = false;
        }
        
        return data;
    }

    function handleSubmit() {
        const formData = collectFormData();
        console.log('Form submitted:', formData);
        
        // Here you would typically send the data to a server
        alert('Instrument registration submitted successfully!');
        closeRegistrationDialog();
    }

    // Event listeners
    document.querySelectorAll('.instrument-type-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.getAttribute('data-type');
            openRegistrationDialog(type);
        });
    });

    elements.cancelBtn.addEventListener('click', closeRegistrationDialog);
    elements.submitBtn.addEventListener('click', handleSubmit);

    elements.isTemporaryFileNo.addEventListener('change', handleTemporaryFileNoChange);
    if (elements.isTemporaryRegNo) {
        elements.isTemporaryRegNo.addEventListener('change', handleTemporaryRegNoChange);
    }
    
    if (elements.regenerateTempBtn) {
        elements.regenerateTempBtn.addEventListener('click', () => {
            elements.temporaryFileNo.value = generateTemporaryFileNo();
        });
    }

    // Close dialog when clicking outside
    elements.registrationDialog.addEventListener('click', (e) => {
        if (e.target === elements.registrationDialog) {
            closeRegistrationDialog();
        }
    });

    // Set default dates to today
    function setDefaultDates() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('registrationDate').value = today;
        document.getElementById('entryDate').value = today;
    }

    // Initialize the page
    function init() {
        setDefaultDates();
        lucide.createIcons();

        // Add event listener for survey info checkbox
        const surveyCheckbox = document.getElementById('surveyInfo');
        if (surveyCheckbox) {
            surveyCheckbox.addEventListener('change', handleSurveyInfoChange);
        }

        // Initialize survey info section to be hidden
        handleSurveyInfoChange();

        // Initialize registration number section visibility
        handleTemporaryRegNoChange();
        // Initialize file number section visibility
        handleTemporaryFileNoChange();
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', init);
</script>