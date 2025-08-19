// Alpine.js File Index Modal Component with Enhanced Field Handling
function fileIndexModal() {
  return {
    showManualEntry: false,
    isFileIndexed: false,
    
    init() {
      console.log('File Index Modal Alpine component initialized');
    },
    
    handleFilenoSelected(detail) {
      console.log('Alpine.js: ct-fileno-selected received:', detail);
      
      const fileno = detail?.fileno;
      if (!fileno) {
        console.log('No fileno in event detail');
        return;
      }
      
      // Check if file is already indexed
      const indexedFile = window.indexedFilesData ? window.indexedFilesData[fileno] : null;
      console.log('Alpine.js: Indexed file lookup result:', indexedFile);
      
      if (indexedFile) {
        console.log('Alpine.js: File already indexed, auto-filling and locking fields...');
        this.isFileIndexed = true;
        this.autoFillFields(indexedFile);
        this.lockAllFields();
        this.showAlreadyIndexedAlert();
      } else {
        console.log('Alpine.js: File not indexed yet, fields remain editable');
        this.isFileIndexed = false;
        this.unlockAllFields();
      }
    },
    
    handleFilenoCleared() {
      console.log('Alpine.js: ct-fileno-cleared received, unlocking fields...');
      this.isFileIndexed = false;
      this.unlockAllFields();
      this.clearAllFields();
    },
    
    autoFillFields(record) {
      console.log('Alpine.js: Auto-filling fields with:', record);
      
      // Fill text inputs
      this.setFieldValue('#file-title', record.file_title || '');
      this.setFieldValue('input[placeholder*="PL-"]', record.plot_number || '');
      this.setFieldValue('input[name="lga"]', record.lga || '');
      
      // Fill select fields
      this.setSelectValue('#landUse', record.land_use_type || '');
      this.setAlpineSelectValue('#district', record.district || '');
      
      // Fill checkboxes
      this.setCheckboxValue('#has-cofo', record.has_cofo);
      this.setCheckboxValue('#has-transaction', record.has_transaction);
      this.setCheckboxValue('#co-owned-plot', record.is_co_owned_plot);
      this.setCheckboxValue('#merged-plot', record.is_merged);
    },
    
    setFieldValue(selector, value) {
      const element = document.querySelector(selector);
      if (element && value !== undefined && value !== null) {
        element.value = value;
        console.log(`Set ${selector} to: ${value}`);
      } else if (!element) {
        console.warn(`Field not found: ${selector}`);
      }
    },
    
    setSelectValue(selector, value) {
      const element = document.querySelector(selector);
      if (element && value) {
        element.value = value;
        console.log(`Set select ${selector} to: ${value}`);
      } else if (!element) {
        console.warn(`Select field not found: ${selector}`);
      }
    },
    
    setAlpineSelectValue(selector, value) {
      const element = document.querySelector(selector);
      if (element && value) {
        // Set the value directly
        element.value = value;
        // Trigger Alpine.js x-model update
        element.dispatchEvent(new Event('change'));
        // Also try to set the Alpine.js data directly
        const alpineComponent = element.closest('[x-data]');
        if (alpineComponent && alpineComponent._x_dataStack) {
          const data = alpineComponent._x_dataStack[0];
          if (data && typeof data.district !== 'undefined') {
            data.district = value;
          }
        }
        console.log(`Set Alpine select ${selector} to: ${value}`);
      } else if (!element) {
        console.warn(`Alpine select field not found: ${selector}`);
      }
    },
    
    setCheckboxValue(selector, value) {
      const element = document.querySelector(selector);
      if (element) {
        element.checked = !!value;
        console.log(`Set checkbox ${selector} to: ${!!value}`);
      } else {
        console.warn(`Checkbox not found: ${selector}`);
      }
    },
    
    getAllFieldSelectors() {
      return [
        '#file-title',
        'input[placeholder*="PL-"]',
        'input[name="lga"]',
        '#landUse',
        '#district',
        '#has-cofo',
        '#has-transaction',
        '#co-owned-plot',
        '#merged-plot'
      ];
    },
    
    lockAllFields() {
      console.log('Alpine.js: Locking all fields...');
      this.getAllFieldSelectors().forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
          element.disabled = true;
          if (element.type !== 'checkbox' && element.type !== 'radio') {
            element.readOnly = true;
          }
          element.classList.add('bg-gray-100', 'opacity-75', 'cursor-not-allowed');
          console.log(`Locked field: ${selector}`);
        } else {
          console.warn(`Field not found for locking: ${selector}`);
        }
      });
    },
    
    unlockAllFields() {
      console.log('Alpine.js: Unlocking all fields...');
      this.getAllFieldSelectors().forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
          element.disabled = false;
          element.readOnly = false;
          element.classList.remove('bg-gray-100', 'opacity-75', 'cursor-not-allowed');
          console.log(`Unlocked field: ${selector}`);
        } else {
          console.warn(`Field not found for unlocking: ${selector}`);
        }
      });
    },
    
    clearAllFields() {
      console.log('Alpine.js: Clearing all fields...');
      // Clear text inputs
      this.setFieldValue('#file-title', '');
      this.setFieldValue('input[placeholder*="PL-"]', '');
      
      // Reset select fields
      this.setSelectValue('#landUse', '');
      this.setAlpineSelectValue('#district', '');
      
      // Uncheck checkboxes
      this.setCheckboxValue('#has-cofo', false);
      this.setCheckboxValue('#has-transaction', false);
      this.setCheckboxValue('#co-owned-plot', false);
      this.setCheckboxValue('#merged-plot', false);
    },
    
    showAlreadyIndexedAlert() {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'info',
          title: 'Already Indexed',
          text: 'This file is already indexed. Fields have been auto-filled and locked.',
          confirmButtonColor: '#3085d6'
        });
      } else {
        alert('This file is already indexed. Fields have been auto-filled and locked.');
      }
    }
  }
}