// Use server-provided data instead of sample data
let cofoData = [];

// Base URL for API endpoints defined in blade
const baseUrl = window.baseUrl || '';

// Helper function to capitalize first letter (moved to the top)
function capitalizeFirstLetter(string) {
  if (!string) return '';
  return string.charAt(0).toUpperCase() + string.slice(1);
}

// Initialize variables
// Show all instruments by default in main table
let activeTab = 'all';
let selectedUnitIndex = -1;
let selectedProperties = [];
let selectedBatchProperties = [];
let nextSerialData = null;

// Update the count of selected checkboxes in batch modal
function updateSelectedCount() {
  const count = document.querySelectorAll('.available-property-checkbox:checked:not([disabled])').length;
  const addSelectedBtn = document.getElementById('addSelectedBtn');
  
  if (addSelectedBtn) {
    addSelectedBtn.textContent = `Add Selected Instruments (${count})`;
    addSelectedBtn.disabled = count === 0;
    addSelectedBtn.classList.toggle('opacity-50', count === 0);
    addSelectedBtn.classList.toggle('cursor-not-allowed', count === 0);
  }

  // Update batch register button based on selectedBatchProperties array (actual added instruments)
  updateBatchRegisterButton();
}

// Separate function to update the batch register button
function updateBatchRegisterButton() {
  const batchRegisterButton = document.getElementById('batchRegisterButton');
  
  if (batchRegisterButton) {
    const selectedCount = selectedBatchProperties.length;
    batchRegisterButton.textContent = `Register ${selectedCount} Instruments`;
    
    // Only disable if there are truly no instruments
    const shouldDisable = selectedCount === 0;
    batchRegisterButton.disabled = shouldDisable;
    batchRegisterButton.classList.toggle('opacity-50', shouldDisable);
    batchRegisterButton.classList.toggle('cursor-not-allowed', shouldDisable);
    
    // Enable button styling when there are instruments
    if (selectedCount > 0) {
      batchRegisterButton.classList.remove('opacity-50', 'cursor-not-allowed');
      batchRegisterButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
    }
    
    // Debug logging
    console.log('updateBatchRegisterButton - selectedBatchProperties.length:', selectedCount);
    console.log('updateBatchRegisterButton - button disabled:', shouldDisable);
    console.log('updateBatchRegisterButton - selectedBatchProperties:', selectedBatchProperties);
  }
}

// Make sure critical functions are exposed to the global scope
window.populateAvailablePropertiesTable = function() {
  console.log("populateAvailablePropertiesTable called");
  
  // Get the table element
  const table = document.getElementById('availablePropertiesTable');
  if (!table) {
    console.error("Table element 'availablePropertiesTable' not found");
    return;
  }
  
  // Get search input value
  const searchInput = document.getElementById('batchSearchInput')?.value?.toLowerCase() || '';
  
  console.log("Search input:", searchInput);
  console.log("cofoData length:", cofoData ? cofoData.length : 0);
  
  // Validate cofoData
  if (!cofoData || !Array.isArray(cofoData) || cofoData.length === 0) {
    table.innerHTML = `
      <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
          No instrument data available.
        </td>
      </tr>
    `;
    return;
  }
  
  // Filter data by search input only (filter is already applied by the server)
  let filteredData = [...cofoData]; // Make a copy
  
  // Apply search filter if there's a search term
  if (searchInput) {
    filteredData = filteredData.filter(item => {
      const fileNo = (item.fileNo || '').toLowerCase();
      const grantor = (item.grantor || '').toLowerCase();
      const grantee = (item.grantee || '').toLowerCase();
      return fileNo.includes(searchInput) || 
             grantor.includes(searchInput) || 
             grantee.includes(searchInput);
    });
  }
  
  // Debug: Print sample of filtered data
  console.log("Sample data after filtering:", filteredData.slice(0, 2));
  // Clear the table first
  table.innerHTML = '';
  
  // Check if we have data after filtering
  if (filteredData.length === 0) {
    table.innerHTML = `
      <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
          No instruments found matching your criteria.
        </td>
      </tr>
    `;
    return;
  }
  
  // Build rows for each item
  filteredData.forEach((item) => {
    // Skip items without an id
    if (!item.id) {
      console.warn("Item without ID found:", item);
      return;
    }
    
    const isAlreadySelected = selectedBatchProperties.some(prop => String(prop.id) === String(item.id));
    
    // Check if this is an ST CofO instrument - disable checkbox if it is
    const isSTCofo = item.instrumentType === 'Sectional Titling CofO';
    const checkboxDisabled = isAlreadySelected || isSTCofo;
    const checkboxClass = isSTCofo ? 'rounded available-property-checkbox cursor-not-allowed' : 'rounded available-property-checkbox';
    
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    row.innerHTML = `
      <td class="px-6 py-4 whitespace-nowrap">
        <input type="checkbox" class="${checkboxClass}" 
          data-id="${item.id}" 
          data-instrument-type="${item.instrumentType || ''}"
          ${checkboxDisabled ? 'disabled' : ''} 
          ${isAlreadySelected ? 'checked' : ''}
          ${isSTCofo ? 'title="ST CofO instruments cannot be registered directly. Please register the corresponding ST Assignment first."' : ''}>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">${item.fileNo || 'N/A'}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">${item.grantor || 'N/A'}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">${item.grantee || 'N/A'}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">
        <span class="badge badge-${item.status || 'pending'}">
          ${capitalizeFirstLetter(item.status || 'pending')}
        </span>
      </td>
    `;
    
    table.appendChild(row);
  });
  
  // Add event listeners for checkboxes
  document.querySelectorAll('.available-property-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
  });
  
  // Update selected count
  updateSelectedCount();
};

// Also expose the close modal functions to global scope
window.closeBatchRegisterModal = function() {
  const modal = document.getElementById('batchRegisterModal');
  if (modal) {
    modal.style.display = 'none';
  }
};

window.closeSingleRegisterModal = function() {
  const modal = document.getElementById('singleRegisterModal');
  if (modal) {
    modal.style.display = 'none';
  }
};

// Expose the real modal‐opening logic
window.openBatchRegisterModalImplementation = function() {
  const modal = document.getElementById('batchRegisterModal');
  if (!modal) {
    console.error("Batch modal element not found");
    return;
  }
  // show modal
  modal.style.display = 'block';

  // set date/time fields
  const today = new Date();
  document.getElementById('batchDeedsDate').value = today.toISOString().split('T')[0];
  const hours = today.getHours(), minutes = today.getMinutes();
  const ampm = hours >= 12 ? 'PM' : 'AM';
  const hh = hours % 12 || 12, mm = minutes < 10 ? '0'+minutes : minutes;
  document.getElementById('batchDeedsTime').value = `${hh}:${mm} ${ampm}`;

  // clear previous selection
  if (typeof clearSelectedProperties === 'function') {
    clearSelectedProperties();
  }

  // show loading state
  const table = document.getElementById('availablePropertiesTable');
  if (table) {
    table.innerHTML = `
      <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">
        <i class="fas fa-spinner fa-spin mr-2"></i> Loading instrument data...
      </td></tr>`;
  }

  // fetch batch data and populate table
  const baseUrl = window.location.origin;
  const filter = document.getElementById('batchStatusFilter').value;
  fetch(`${baseUrl}/instrument_registration/get-batch-data?filter=${filter}`, {
    method: 'GET',
    credentials: 'same-origin'
  })
  .then(response => {
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    return response.json();
  })
  .then(data => {
    console.log('Batch data received:', data);
    // Ensure data is an array
    if (Array.isArray(data)) {
      cofoData = data.map(item => ({
        id: item.id,
        fileNo: item.fileno,
        grantor: item.grantor,
        grantee: item.grantee,
        status: item.status || 'pending',
        instrumentType: item.instrument_type || '',
        source_type: item.source_type || ''
      }));
    } else if (data && data.error) {
      console.error('Server error:', data.error);
      cofoData = [];
      if (table) {
        table.innerHTML = `<tr><td colspan="5" class="px-6 py-10 text-center text-red-500">
          Server error: ${data.error}
        </td></tr>`;
      }
      return;
    } else {
      console.error('Expected array but got:', typeof data, data);
      cofoData = [];
    }
    populateAvailablePropertiesTable();
  })
  .catch(error => {
    console.error('Error fetching batch data:', error);
    if (table) {
      table.innerHTML = `<tr><td colspan="5" class="px-6 py-10 text-center text-red-500">
        Error loading instruments: ${error.message}
      </td></tr>`;
    }
  });

  // fetch next serial
  if (typeof fetchNextSerialNumber === 'function') {
    fetchNextSerialNumber();
  }
};

// Function to fetch batch data for a specific filter
function fetchBatchDataForFilter(filter) {
  console.log('Fetching data for filter:', filter);
  
  const table = document.getElementById('availablePropertiesTable');
  if (table) {
    table.innerHTML = `
      <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">
        <i class="fas fa-spinner fa-spin mr-2"></i> Loading ${filter} data...
      </td></tr>`;
  }

  const baseUrl = window.location.origin;
  fetch(`${baseUrl}/instrument_registration/get-batch-data?filter=${filter}`, {
    method: 'GET',
    credentials: 'same-origin'
  })
  .then(response => {
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    return response.json();
  })
  .then(data => {
    console.log('Filter data received:', data);
    // Ensure data is an array
    if (Array.isArray(data)) {
      cofoData = data.map(item => ({
        id: item.id,
        fileNo: item.fileno,
        grantor: item.grantor,
        grantee: item.grantee,
        status: item.status || 'pending',
        instrumentType: item.instrument_type || '',
        source_type: item.source_type || ''
      }));
    } else if (data && data.error) {
      console.error('Server error:', data.error);
      cofoData = [];
      if (table) {
        table.innerHTML = `<tr><td colspan="5" class="px-6 py-10 text-center text-red-500">
          Server error: ${data.error}
        </td></tr>`;
      }
      return;
    } else {
      console.error('Expected array but got:', typeof data, data);
      cofoData = [];
    }
    populateAvailablePropertiesTable();
  })
  .catch(error => {
    console.error('Error fetching filter data:', error);
    if (table) {
      table.innerHTML = `<tr><td colspan="5" class="px-6 py-10 text-center text-red-500">
        Error loading ${filter} data: ${error.message}
      </td></tr>`;
    }
  });
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
  console.log("DOMContentLoaded event fired - initializing instruments registration page");
  
  // Process server data if available
  if (typeof serverCofoData !== 'undefined') {
    console.log("Server data received:", serverCofoData.length, "records");
    cofoData = serverCofoData.map(item => {
      return {
        id: item.id,
        fileNo: item.fileno || item.MLSFileNo,
        grantor: item.Grantor || '',
        grantee: item.Grantee != null ? String(item.Grantee) : '',
        instrumentType: item.instrument_type || '',
        duration: item.duration || item.leasePeriod || '',
        lga: item.lga || '',
        district: item.district || '',
        plotNumber: item.plotNumber || '',
        plotSize: item.size || '',
        plotDescription: item.propertyDescription || '',
        deeds_date: item.deeds_date || item.instrumentDate || '',
        deeds_time: item.deeds_time || '',
        rootRegistrationNumber: item.rootRegistrationNumber || item.Deeds_Serial_No || '',
        status: item.status || 'pending',  // Ensure status is set with default
        solicitorName: item.solicitorName || '',
        solicitorAddress: item.solicitorAddress || '',
        landUseType: item.landUseType || item.land_use || ''
      };
    });
    console.log("Processed data:", cofoData.length, "records");
  } else {
    console.warn("No server data available");
  }
  
  updateTableVisibility();
  initializeCalendars();
  fetchNextSerialNumber();
  
  // Add event listener for batch search input
  const batchSearchInput = document.getElementById('batchSearchInput');
  if (batchSearchInput) {
    batchSearchInput.addEventListener('input', function() {
      populateAvailablePropertiesTable();
    });
  }
  
  // Add event listener for batch status filter
  const batchStatusFilter = document.getElementById('batchStatusFilter');
  if (batchStatusFilter) {
    batchStatusFilter.addEventListener('change', function() {
      console.log('Filter changed to:', this.value);
      fetchBatchDataForFilter(this.value);
    });
  }
  
  // Add event listener for the search input
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      filterTableByFileNo(this.value);
    });
  }
});

// Fetch next available serial number
function fetchNextSerialNumber() {
  // Create headers
  const headers = {
    'Content-Type': 'application/json'
  };
  
  // Try to get CSRF token if available
  const csrfToken = document.querySelector('meta[name="csrf-token"]');
  if (csrfToken) {
    headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
  } else {
    console.warn('CSRF token meta tag not found! CSRF protection may cause request to fail.');
  }
  
  // Use the correct route for the instrument_registration controller
  return fetch(`${baseUrl}/instrument_registration/get-next-serial`, {
    method: 'GET',
    headers: headers,
    credentials: 'same-origin' // Include cookies in the request
  })
  .then(response => {
    if (!response.ok) {
      console.error(`Server returned ${response.status}: ${response.statusText}`);
      // For debugging - log the actual response content
      return response.text().then(text => {
        console.error('Response content:', text);
        throw new Error(`Server returned ${response.status}: ${response.statusText}`);
      });
    }
    return response.json();
  })
  .then(data => {
    console.log('Serial number data:', data); // Debug log
    
    // Update single registration form
    if (document.getElementById('serialNo')) {
      document.getElementById('serialNo').value = data.serial_no;
      document.getElementById('pageNo').value = data.page_no;
      document.getElementById('volumeNo').value = data.volume_no;
      document.getElementById('deedsSerialNo').value = data.deeds_serial_no;
    }
    
    // Update batch registration form
    const batchNextSerialNo = document.getElementById('batchNextSerialNo');
    if (batchNextSerialNo) {
      batchNextSerialNo.textContent = data.deeds_serial_no;
    }
    
    // Store the data for later use
    nextSerialData = data;
    
    return data;
  })
  .catch(error => {
    console.error('Error fetching next serial number:', error);
    showToast('Error', 'Failed to get next serial number: ' + error.message, 'error');
    
    // Set default values in case of error
    nextSerialData = {
      serial_no: 1,
      page_no: 1,
      volume_no: 1,
      deeds_serial_no: '1/1/1'
    };
    
    // Update UI with default values
    if (document.getElementById('batchNextSerialNo')) {
      document.getElementById('batchNextSerialNo').textContent = '1/1/1 (default)';
    }
    
    return nextSerialData;
  });
}

// Function to manually retry fetching the serial number
function retryFetchSerialNumber() {
  const debugElem = document.getElementById('serialNumberDebug');
  const debugMsg = document.getElementById('serialNumberDebugMsg');
  
  if (debugElem) debugElem.classList.remove('hidden');
  if (debugMsg) debugMsg.textContent = 'Retrying...';
  
  fetch('http://klas.com.ng/st_registration/get-next-serial', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'same-origin'
  })
  .then(response => {
    if (!response.ok) {
      if (debugMsg) debugMsg.textContent = `Server error: ${response.status} ${response.statusText}`;
      throw new Error(`Server returned ${response.status}: ${response.statusText}`);
    }
    return response.json();
  })
  .then(data => {
    if (debugMsg) debugMsg.textContent = `Success! Data: ${JSON.stringify(data)}`;
    
    // Update batch registration form
    const batchNextSerialNo = document.getElementById('batchNextSerialNo');
    if (batchNextSerialNo) {
      batchNextSerialNo.textContent = data.deeds_serial_no;
    }
    
    // Store the data
    nextSerialData = data;
    
    // Hide debug info after success
    setTimeout(() => {
      if (debugElem) debugElem.classList.add('hidden');
    }, 3000);
  })
  .catch(error => {
    if (debugMsg) debugMsg.textContent = `Error: ${error.message}`;
    console.error('Retry failed:', error);
  });
}

// Helper function to show/hide no results message
function updateNoResultsMessage(hasVisibleRows) {
  const noResultsRow = document.getElementById('noResultsRow');
  if (!hasVisibleRows) {
    if (!noResultsRow) {
      const tableBody = document.getElementById('cofoTableBody');
      const newNoResultsRow = document.createElement('tr');
      newNoResultsRow.id = 'noResultsRow';
      newNoResultsRow.innerHTML = `
        <td colspan="13" class="px-6 py-10 text-center text-gray-500">
          No results found.
        </td>
      `;
      tableBody.appendChild(newNoResultsRow);
    }
  } else if (noResultsRow) {
    // Remove the "No results" row if we have visible rows
    noResultsRow.remove();
  }
}

// Update table rows visibility based on active tab
function updateTableVisibility() {
  const rows = document.querySelectorAll('.cofo-row');
  
  // Track if we have visible rows
  let hasVisibleRows = false;
  
  rows.forEach(row => {
    const status = row.getAttribute('data-status');
    if (activeTab === 'all' || status === activeTab) {
      row.style.display = '';
      hasVisibleRows = true;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Update "No results" message
  updateNoResultsMessage(hasVisibleRows);
  
  // Also apply any active search filter
  const searchInput = document.getElementById('searchInput');
  if (searchInput && searchInput.value.trim() !== '') {
    filterTableByFileNo(searchInput.value);
  }
}

// Filter table by ST FileNO
function filterTableByFileNo(searchTerm) {
  const rows = document.querySelectorAll('.cofo-row');
  const normalizedSearchTerm = searchTerm.toLowerCase().trim();
  
  // If empty search, just revert to tab filtering
  if (normalizedSearchTerm === '') {
    updateTableVisibility();
    return;
  }
  
  // Track if we have visible rows
  let hasVisibleRows = false;
  
  rows.forEach(row => {
    const status = row.getAttribute('data-status');
    // Skip rows that don't match the active tab
    if (activeTab !== 'all' && status !== activeTab) {
      row.style.display = 'none';
      return;
    }
    
    // Get the ST FileNO from the row (3rd column)
    const fileNo = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
    
    if (fileNo.includes(normalizedSearchTerm)) {
      row.style.display = '';
      hasVisibleRows = true;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Update "No results" message
  updateNoResultsMessage(hasVisibleRows);
}

// Switch between tabs
function switchTab(tab, element) {
  activeTab = tab;
  
  // Update active tab styling
  document.querySelectorAll('.tab-active').forEach(el => {
    el.classList.remove('tab-active');
  });
  element.classList.add('tab-active');
  
  // Update table visibility
  updateTableVisibility();
}

// Toggle dropdown menu
function toggleDropdown() {
  document.getElementById("registerDropdown").classList.toggle("show");
}

// Close dropdown when clicking outside
window.onclick = function(event) {
  if (!event.target.matches('.dropdown button')) {
    const dropdowns = document.getElementsByClassName("dropdown-content");
    for (let i = 0; i < dropdowns.length; i++) {
      const openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}

// Open single register modal with data
function openSingleRegisterModalWithData(id) {
  // Show loading state
  Swal.fire({
    title: 'Loading',
    text: 'Fetching instrument details...',
    icon: 'info',
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    }
  });
  
  // Convert id to string to ensure consistent comparison
  id = String(id);
  
  // Find the application by id - first try cofoData, then serverCofoData
  let application = cofoData.find(item => String(item.id) === id);
  
  // If not found in cofoData, try serverCofoData (main table data)
  if (!application && typeof serverCofoData !== 'undefined') {
    const serverItem = serverCofoData.find(item => String(item.id) === id);
    if (serverItem) {
      application = {
        id: serverItem.id,
        fileNo: serverItem.fileno || serverItem.MLSFileNo,
        grantor: serverItem.Grantor || '',
        grantee: serverItem.Grantee != null ? String(serverItem.Grantee) : '',
        instrumentType: serverItem.instrument_type || '',
        duration: serverItem.duration || serverItem.leasePeriod || '',
        lga: serverItem.lga || '',
        district: serverItem.district || '',
        plotNumber: serverItem.plotNumber || '',
        plotSize: serverItem.size || '',
        plotDescription: serverItem.propertyDescription || '',
        deeds_date: serverItem.deeds_date || serverItem.instrumentDate || '',
        deeds_time: serverItem.deeds_time || '',
        rootRegistrationNumber: serverItem.rootRegistrationNumber || serverItem.Deeds_Serial_No || '',
        status: serverItem.status || 'pending',
        solicitorName: serverItem.solicitorName || '',
        solicitorAddress: serverItem.solicitorAddress || '',
        landUseType: serverItem.landUseType || serverItem.land_use || ''
      };
    }
  }
  
  if (!application) {
    console.error('Instrument not found with ID:', id);
    console.log('Available cofoData IDs:', cofoData.map(item => item.id));
    if (typeof serverCofoData !== 'undefined') {
      console.log('Available serverCofoData IDs:', serverCofoData.map(item => item.id));
    }
    Swal.fire({
      title: 'Error',
      text: 'Instrument not found. Please try again.',
      icon: 'error'
    });
    return;
  }
  
  // Show the modal
  document.getElementById('singleRegisterModal').style.display = 'block';
  document.getElementById('unitSearchSection').style.display = 'none';
  document.getElementById('unitDetailsSection').style.display = 'block';
  
  // Close loading dialog
  Swal.close();
  
  // Ensure the status is set to 'pending'
  application.status = 'pending';
  
  // Set application data
  document.getElementById('selectedFileNo').textContent = application.fileNo;
  document.getElementById('selectedProperty').textContent = application.plotDescription || application.propertyDescription || 'No description available';
  
  // Populate form fields
  document.getElementById('formInstrumentId').value = application.id;
  document.getElementById('instrumentType').value = application.instrumentType || '';
  document.getElementById('duration').value = application.duration || '';
  document.getElementById('grantor').value = application.grantor || '';
  document.getElementById('grantee').value = application.grantee || '';
  document.getElementById('lga').value = application.lga || '';
  document.getElementById('district').value = application.district || '';
  document.getElementById('plotNumber').value = application.plotNumber || '';
  document.getElementById('plotSize').value = application.plotSize || '';
  document.getElementById('plotDescription').value = application.plotDescription || '';
  
  // Set current date and time
  const today = new Date();
  document.getElementById('deedsDate').value = today.toISOString().split('T')[0];
  
  // Set current time (formatted as HH:MM AM/PM)
  const hours = today.getHours();
  const minutes = today.getMinutes();
  const ampm = hours >= 12 ? 'PM' : 'AM';
  const formattedHours = hours % 12 || 12;
  const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
  document.getElementById('deedsTime').value = `${formattedHours}:${formattedMinutes} ${ampm}`;
  
  // Fetch the next serial number
  fetchNextSerialNumber();
}

// Open single register modal
function openSingleRegisterModal() {
  document.getElementById('singleRegisterModal').style.display = 'block';
  document.getElementById('unitSearchSection').style.display = 'block';
  document.getElementById('unitDetailsSection').style.display = 'none';
  
  // Populate unit search results with real data
  const unitSearchResults = document.getElementById('unitSearchResults');
  unitSearchResults.innerHTML = '';
  
  const pendingApplications = cofoData.filter(item => item.status === 'pending');
  
  if (pendingApplications.length === 0) {
    unitSearchResults.innerHTML = `
      <tr>
        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
          No pending applications found.
        </td>
      </tr>
    `;
    return;
  }
  
  pendingApplications.forEach((item, index) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${item.stmRef}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">${item.unitNo}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">${item.blockNo || 'N/A'}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">${item.owner}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">
        <span class="badge badge-pending">Pending</span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
        <button class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm" onclick="openSingleRegisterModalWithData(${item.mother_id})">Select</button>
      </td>
    `;
    unitSearchResults.appendChild(row);
  });
}

// Close single register modal - Fix close modal function
function closeSingleRegisterModal() {
  const modal = document.getElementById('singleRegisterModal');
  if (modal) {
    modal.style.display = 'none';
  }
}

// Close batch register modal - Fix close modal function
function closeBatchRegisterModal() {
  const modal = document.getElementById('batchRegisterModal');
  if (modal) {
    modal.style.display = 'none';
  }
}

// Add selected properties to batch - Fix function to correctly add selected instruments
function addSelectedToBatch() {
  console.log("addSelectedToBatch called");
  const selectedCheckboxes = document.querySelectorAll('.available-property-checkbox:checked:not([disabled])');
  console.log("Selected checkboxes:", selectedCheckboxes.length);
  
  if (selectedCheckboxes.length === 0) {
    showToast('Warning', 'No properties selected', 'info');
    return;
  }
   
  try {
    // Disable the button while processing
    const addSelectedBtn = document.getElementById('addSelectedBtn');
    if (addSelectedBtn) {
      addSelectedBtn.disabled = true;
      addSelectedBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
    }
    
    // Get next serial number if not already fetched
    if (!nextSerialData) {
      fetchNextSerialNumber()
        .then(() => {
          processSelectedProperties(selectedCheckboxes);
          // Re-enable the button
          if (addSelectedBtn) {
            addSelectedBtn.disabled = false;
            addSelectedBtn.textContent = `Add Selected Instruments (${selectedCheckboxes.length})`;
          }
        })
        .catch((error) => {
          console.error("Error fetching serial number:", error);
          // Re-enable the button even if there's an error
          if (addSelectedBtn) {
            addSelectedBtn.disabled = false;
            addSelectedBtn.textContent = `Add Selected Instruments (${selectedCheckboxes.length})`;
          }
          // Show error
          Swal.fire({
            title: 'Error',
            text: `Failed to get serial numbers: ${error.message}`,
            icon: 'error'
          });
        });
    } else {
      processSelectedProperties(selectedCheckboxes);
      // Re-enable the button
      if (addSelectedBtn) {
        addSelectedBtn.disabled = false;
        addSelectedBtn.textContent = `Add Selected Instruments (${selectedCheckboxes.length})`;
      }
    }
  } catch (error) {
    console.error("Error in addSelectedToBatch:", error);
    Swal.fire({
      title: 'Error',
      text: `An error occurred: ${error.message}`,
      icon: 'error'
    });
  }
}

// Process selected properties - Fix to properly handle selected instruments
function processSelectedProperties(selectedCheckboxes) {
  try {
    const newProperties = Array.from(selectedCheckboxes).map(checkbox => {
      const id = checkbox.getAttribute('data-id');
      console.log("Processing checkbox with ID:", id);
      const item = cofoData.find(item => String(item.id) === String(id));
      if (!item) {
        console.warn("Could not find data for ID:", id);
      }
      return item;
    }).filter(item => item); // Filter out any undefined items
    
    console.log("New properties to add:", newProperties.length);
    
    if (newProperties.length === 0) return;
    
    // Calculate serial numbers for new properties
    let currentSerialNo = nextSerialData ? nextSerialData.serial_no : 1;
    let currentPageNo = nextSerialData ? nextSerialData.page_no : 1;
    let currentVolumeNo = nextSerialData ? nextSerialData.volume_no : 1;
    
    // If we already have properties, start from the last one's next serial number
    if (selectedBatchProperties.length > 0) {
      const lastProperty = selectedBatchProperties[selectedBatchProperties.length - 1];
      currentSerialNo = lastProperty.serialData.serial_no + 1;
      currentPageNo = lastProperty.serialData.page_no + 1;
      currentVolumeNo = lastProperty.serialData.volume_no;
      
      // Check if we need to start a new volume
      if (currentPageNo > 100) {
        currentVolumeNo++;
        currentPageNo = 1;
        currentSerialNo = 1;
      }
    }
    
    // Add serial data to new properties
    const propertiesWithSerial = newProperties.map((property, index) => {
      let serialNo = currentSerialNo + index;
      let pageNo = currentPageNo + index;
      let volumeNo = currentVolumeNo;
      
      // Check if we need to move to next volume
      if (pageNo > 100) {
        volumeNo++;
        pageNo = (pageNo - 1) % 100 + 1; // 1-100 range
        serialNo = pageNo; // Reset serial to match page within new volume
      }
      
      const serialData = {
        serial_no: serialNo,
        page_no: pageNo,
        volume_no: volumeNo,
        deeds_serial_no: `${serialNo}/${pageNo}/${volumeNo}`
      };
      
      return {
        ...property,
        serialData
      };
    });
    
    // Log and add to selectedBatchProperties
    console.log("Adding properties with serial:", propertiesWithSerial);
    selectedBatchProperties = [...selectedBatchProperties, ...propertiesWithSerial];
    console.log("Updated selectedBatchProperties:", selectedBatchProperties.length);
    
    // Update next serial data for future additions
    if (propertiesWithSerial.length > 0) {
      const lastProperty = propertiesWithSerial[propertiesWithSerial.length - 1];
      nextSerialData = {
        serial_no: lastProperty.serialData.serial_no + 1,
        page_no: lastProperty.serialData.page_no + 1,
        volume_no: lastProperty.serialData.volume_no,
        deeds_serial_no: `${lastProperty.serialData.serial_no + 1}/${lastProperty.serialData.page_no + 1}/${lastProperty.serialData.volume_no}`
      };
      
      // Check if we need to start a new volume
      if (nextSerialData.page_no > 100) {
        nextSerialData.volume_no++;
        nextSerialData.page_no = 1;
        nextSerialData.serial_no = 1;
        nextSerialData.deeds_serial_no = `1/1/${nextSerialData.volume_no}`;
      }
    }
    
    // Update the UI - Critical part that updates the selected properties table
    updateSelectedPropertiesTable();
    
    // Refresh available properties to show disabled checkboxes
    populateAvailablePropertiesTable();
    
    // Show success message
    Swal.fire({
      title: 'Success!',
      text: `${newProperties.length} instruments added to batch`,
      icon: 'success',
      toast: true,
      position: 'bottom-end',
      showConfirmButton: false,
      timer: 3000
    });
  } catch (error) {
    console.error("Error in processSelectedProperties:", error);
    Swal.fire({
      title: 'Error',
      text: `An error occurred: ${error.message}`,
      icon: 'error'
    });
  }
}

// Fix updateSelectedPropertiesTable function to properly display selected instruments
function updateSelectedPropertiesTable() {
  console.log("updateSelectedPropertiesTable called with", selectedBatchProperties.length, "properties");
  const table = document.getElementById('selectedPropertiesTable');
  if (!table) {
    console.error("Selected properties table not found");
    return;
  }

  // Clear the table
  table.innerHTML = '';
  
  // Update register button state using the centralized function
  updateBatchRegisterButton();

  // Show/hide no selected properties message
  if (selectedBatchProperties.length === 0) {
    table.innerHTML = `
      <tr id="noSelectedPropertiesRow">
        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
          No instruments selected for registration. Use the table above to select instruments.
        </td>
      </tr>
    `;
    return;
  }
  
  // Populate table with selected properties
  selectedBatchProperties.forEach((property, index) => {
    console.log("Adding property to table:", property.fileNo);
    const row = document.createElement('tr');
    row.setAttribute('data-index', index);
    row.className = 'hover:bg-gray-50';
    
    row.innerHTML = `
      <td class="px-6 py-4 whitespace-nowrap text-sm">${property.fileNo || 'N/A'}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">${property.grantor || 'N/A'}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">${property.grantee || 'N/A'}</td>
      <td class="px-6 py-4 whitespace-nowrap text-sm">
        <input type="text" class="w-full px-3 py-1 border rounded-md bg-gray-100" value="${property.instrumentType || 'N/A'}" readonly>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${property.serialData.deeds_serial_no}</td>
      <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
        <button class="text-red-600 hover:text-red-800" onclick="removePropertyFromBatch(${index})">
          <i class="fas fa-times"></i>
        </button>
      </td>
    `;
    
    table.appendChild(row);
  });
  
  // Instrument types are now automatically determined, no event listeners needed
}

// Remove property from batch - Make sure it's properly defined
function removePropertyFromBatch(index) {
  console.log("Removing property at index:", index);
  // Remove the property from the array
  if (index >= 0 && index < selectedBatchProperties.length) {
    selectedBatchProperties.splice(index, 1);
    // Update the table
    updateSelectedPropertiesTable();
    // Refresh available properties
    populateAvailablePropertiesTable();
  } else {
    console.error("Invalid index for removal:", index);
  }
}

// Clear all selected properties with confirmation
function clearSelectedProperties() {
  if (selectedBatchProperties.length === 0) return;
  
  Swal.fire({
    title: 'Clear Selection?',
    text: 'Are you sure you want to clear all selected instruments?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, clear them'
  }).then((result) => {
    if (result.isConfirmed) {
      selectedBatchProperties = [];
      updateSelectedPropertiesTable();
      populateAvailablePropertiesTable();
      Swal.fire('Cleared!', 'Your selected instruments have been cleared.', 'success');
    }
  });
}

// Hide toast is not needed with SweetAlert
function hideToast() {
  // Not needed, as SweetAlert handles this automatically
}

// Setup the server data from the global variable when it exists
document.addEventListener('DOMContentLoaded', function() {
  // Check if the serverCofoData variable exists globally
  if (typeof serverCofoData !== 'undefined') {
    // Process the data
    cofoData = serverCofoData.map(item => {
      return {
        id: item.id,
        fileNo: item.fileno || item.MLSFileNo,
        grantor: item.Grantor || '',
        grantee: item.Grantee != null ? String(item.Grantee) : '',
        instrumentType: item.instrument_type || '',
        duration: item.duration || item.leasePeriod || '',
        lga: item.lga || '',
        district: item.district || '',
        plotNumber: item.plotNumber || '',
        plotSize: item.size || '',
        plotDescription: item.propertyDescription || '',
        deeds_date: item.deeds_date || item.instrumentDate || '',
        deeds_time: item.deeds_time || '',
        rootRegistrationNumber: item.rootRegistrationNumber || item.Deeds_Serial_No || '',
        status: item.status,
        solicitorName: item.solicitorName || '',
        solicitorAddress: item.solicitorAddress || '',
        landUseType: item.landUseType || item.land_use || ''
      };
    });
  }
});

// Add a dummy initializeCalendars function to prevent ReferenceError
function initializeCalendars() {
  // No-op: Placeholder for calendar initialization if needed in the future
  // You can integrate a date picker here if required
  console.log("initializeCalendars called (no-op)");
}

// Submit single registration
function submitSingleRegistration() {
  const instrumentType = document.getElementById('instrumentType').value;
  const grantor = document.getElementById('grantor').value;
  const grantee = document.getElementById('grantee').value;
  const deedsTime = document.getElementById('deedsTime').value;
  const deedsDate = document.getElementById('deedsDate').value;
  
  const data = {
    mother_application_id: document.getElementById('formInstrumentId').value,
    file_no: document.getElementById('selectedFileNo').textContent,
    instrument_type: instrumentType,
    Grantor: grantor,
    GrantorAddress: "",
    Grantee: grantee,
    GranteeAddress: "",
    duration: document.getElementById('duration').value,
    propertyDescription: document.getElementById('plotDescription').value,
    lga: document.getElementById('lga').value,
    district: document.getElementById('district').value,
    plotNumber: document.getElementById('plotNumber').value,
    plotSize: document.getElementById('plotSize').value,
    deeds_time: deedsTime,
    deeds_date: deedsDate
  };

  // Use the application's base URL instead of a Blade route
  const baseUrl = window.location.origin ;
  fetch(`${baseUrl}/instrument_registration/register-single`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => {
    if(res.success) {
      Swal.fire('Success', res.message, 'success');
      closeSingleRegisterModal();
      window.location.reload();
    } else {
      Swal.fire('Error', res.error || res.message, 'error');
    }
  })
  .catch(e => {
    console.error(e);
    Swal.fire('Error', 'Request failed', 'error');
  });
}

// Submit batch registration
function submitBatchRegistration() {
  // Debug logging
  console.log('submitBatchRegistration called');
  console.log('selectedBatchProperties:', selectedBatchProperties);
  console.log('selectedBatchProperties.length:', selectedBatchProperties.length);
  
  // Use fallbacks for missing data
  const deedsTime = document.getElementById('batchDeedsTime')?.value || new Date().toLocaleTimeString();
  const deedsDate = document.getElementById('batchDeedsDate')?.value || new Date().toISOString().split('T')[0];
  
  // Process batch entries with proper fallbacks for missing data
  const batchEntries = selectedBatchProperties.map(p => ({
    application_id: p.id || 'N/A',
    file_no: p.fileNo || 'N/A',
    instrument_type: p.instrumentType || 'N/A',
    grantor: p.grantor || 'N/A',
    grantorAddress: p.grantorAddress || '',
    grantee: p.grantee || 'N/A',
    granteeAddress: p.granteeAddress || '',
    duration: p.duration || 'N/A',
    propertyDescription: p.plotDescription || p.propertyDescription || 'N/A',
    lga: p.lga || 'N/A',
    district: p.district || 'N/A',
    plotNumber: p.plotNumber || 'N/A',
    size: p.plotSize || p.size || 'N/A',
    serial_no: (p.serialData && p.serialData.serial_no) || 1,
    page_no: (p.serialData && p.serialData.page_no) || 1,
    volume_no: (p.serialData && p.serialData.volume_no) || 1
  }));
  
  console.log('batchEntries:', batchEntries);
  
  // Show loading state
  Swal.fire({
    title: 'Processing Registration',
    text: `Registering ${batchEntries.length} instruments...`,
    icon: 'info',
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    }
  });
  
  // Use the application's base URL instead of a Blade route
  const baseUrl = window.location.origin;
  fetch(`${baseUrl}/instrument_registration/register-batch`, {
    method: 'POST',
    headers: {
      'Content-Type':'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ 
      batch_entries: batchEntries, 
      deeds_time: deedsTime, 
      deeds_date: deedsDate 
    })
  })
  .then(r => r.json())
  .then(res => {
    Swal.close();
    if(res.success) {
      Swal.fire('Success', res.message, 'success');
      closeBatchRegisterModal();
      window.location.reload();
    } else {
      Swal.fire('Error', res.error || res.message, 'error');
    }
  })
  .catch(e => {
    Swal.close();
    console.error(e);
    Swal.fire('Error', 'Batch request failed: ' + e.message, 'error');
  });
}

// Decline registration
function declineRegistration(id) {
  Swal.fire({
    title: 'Decline Registration',
    text: 'Are you sure you want to decline this registration?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, decline it',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      const baseUrl = window.location.origin;
      fetch(`${baseUrl}/instrument_registration/decline`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ id: id })
      })
      .then(r => r.json())
      .then(res => {
        if(res.success) {
          Swal.fire('Success', res.message, 'success');
          window.location.reload();
        } else {
          Swal.fire('Error', res.error || res.message, 'error');
        }
      })
      .catch(e => {
        console.error(e);
        Swal.fire('Error', 'Decline failed', 'error');
      });
    }
  });
}

// Back to unit search function
function backToUnitSearch() {
  document.getElementById('unitSearchSection').style.display = 'block';
  document.getElementById('unitDetailsSection').style.display = 'none';
  
  // Clear form data
  document.getElementById('singleRegistrationForm').reset();
  selectedUnitIndex = -1;
}

// Toggle select all available properties
function toggleSelectAllAvailable(checkbox) {
  const availableCheckboxes = document.querySelectorAll('.available-property-checkbox:not([disabled])');
  availableCheckboxes.forEach(cb => {
    // Only check/uncheck if it's not an ST CofO instrument
    const instrumentType = cb.getAttribute('data-instrument-type');
    if (instrumentType !== 'Sectional Titling CofO') {
      cb.checked = checkbox.checked;
    }
  });
  updateSelectedCount();
}

// Toggle select all function for main table
function toggleSelectAll(checkbox) {
  const checkboxes = document.querySelectorAll('.cofo-row:not([style*="display: none"]) input[type="checkbox"]');
  checkboxes.forEach(cb => {
    cb.checked = checkbox.checked;
  });
}

// Select unit function for single registration
function selectUnit(index) {
  selectedUnitIndex = index;
  const pendingApplications = cofoData.filter(item => item.status === 'pending');
  
  if (index < 0 || index >= pendingApplications.length) {
    console.error('Invalid unit index:', index);
    return;
  }
  
  const selectedUnit = pendingApplications[index];
  
  // Show unit details section
  document.getElementById('unitSearchSection').style.display = 'none';
  document.getElementById('unitDetailsSection').style.display = 'block';
  
  // Populate selected unit details
  document.getElementById('selectedFileNo').textContent = selectedUnit.fileNo || 'N/A';
  document.getElementById('selectedProperty').textContent = selectedUnit.plotDescription || selectedUnit.propertyDescription || 'No description available';
  
  // Populate form fields
  document.getElementById('formInstrumentId').value = selectedUnit.id;
  document.getElementById('instrumentType').value = selectedUnit.instrumentType || '';
  document.getElementById('duration').value = selectedUnit.duration || '';
  document.getElementById('grantor').value = selectedUnit.grantor || '';
  document.getElementById('grantee').value = selectedUnit.grantee || '';
  document.getElementById('lga').value = selectedUnit.lga || '';
  document.getElementById('district').value = selectedUnit.district || '';
  document.getElementById('plotNumber').value = selectedUnit.plotNumber || '';
  document.getElementById('plotSize').value = selectedUnit.plotSize || '';
  document.getElementById('plotDescription').value = selectedUnit.plotDescription || '';
  
  // Set current date and time
  const today = new Date();
  document.getElementById('deedsDate').value = today.toISOString().split('T')[0];
  
  const hours = today.getHours();
  const minutes = today.getMinutes();
  const ampm = hours >= 12 ? 'PM' : 'AM';
  const formattedHours = hours % 12 || 12;
  const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
  document.getElementById('deedsTime').value = `${formattedHours}:${formattedMinutes} ${ampm}`;
  
  // Fetch next serial number
  fetchNextSerialNumber();
}

// Remove from batch (alias for removePropertyFromBatch)
function removeFromBatch(button) {
  const row = button.closest('tr');
  const index = parseInt(row.getAttribute('data-index'), 10);
  removePropertyFromBatch(index);
}

// Show toast function for better user feedback
function showToast(title, message, type = 'success') {
  // Using SweetAlert2 for consistent UI
  const icon = type === 'error' ? 'error' : type === 'warning' ? 'warning' : type === 'info' ? 'info' : 'success';
  
  Swal.fire({
    title: title,
    text: message,
    icon: icon,
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  });
}

// Enhanced batch registration validation function
function submitBatchRegistrationWithValidation() {
    // Debug logging
    console.log('submitBatchRegistrationWithValidation called');
    console.log('selectedBatchProperties:', selectedBatchProperties);
    console.log('selectedBatchProperties.length:', selectedBatchProperties.length);
    
    // Check if we have any selected properties
    if (selectedBatchProperties.length === 0) {
        console.error('No batch properties selected!');
        
        // Check if user has selected checkboxes but not added them to batch
        const checkedCount = document.querySelectorAll('.available-property-checkbox:checked:not([disabled])').length;
        if (checkedCount > 0) {
            Swal.fire({
                title: 'Instruments Not Added to Batch',
                html: `
                    <p>You have selected <strong>${checkedCount} instruments</strong> but haven't added them to the batch.</p>
                    <p class="mt-2">Please click the <strong>"Add Selected Instruments"</strong> button first to add them to the batch for registration.</p>
                `,
                icon: 'warning',
                confirmButtonText: 'OK, I understand',
                confirmButtonColor: '#3085d6'
            });
        } else {
            Swal.fire('Error', 'No instruments selected for batch registration. Please select instruments first.', 'error');
        }
        return;
    }
    
    // If we have selected properties, proceed with the original function
    submitBatchRegistration();
}

// Ensure modal functions are available globally
function closeBatchRegisterModal() {
  const modal = document.getElementById('batchRegisterModal');
  if (modal) {
    modal.style.display = 'none';
  }
}

function closeSingleRegisterModal() {
  const modal = document.getElementById('singleRegisterModal');
  if (modal) {
    modal.style.display = 'none';
  }
}
