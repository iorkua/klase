<script>
  // Mock data for the application
  const monthlyData = [
    { month: "Jan", searches: 18, revenue: 270000 },
    { month: "Feb", searches: 22, revenue: 330000 },
    { month: "Mar", searches: 25, revenue: 375000 },
    { month: "Apr", searches: 20, revenue: 300000 },
    { month: "May", searches: 28, revenue: 420000 },
    { month: "Jun", searches: 32, revenue: 480000 },
    { month: "Jul", searches: 35, revenue: 525000 },
    { month: "Aug", searches: 30, revenue: 450000 },
    { month: "Sep", searches: 26, revenue: 390000 },
    { month: "Oct", searches: 22, revenue: 330000 },
    { month: "Nov", searches: 20, revenue: 300000 },
    { month: "Dec", searches: 24, revenue: 360000 }
  ];

  // Helper to generate registration numbers in XX/XX/YYY format
  function generateRegNumber() {
    const prefix = Math.floor(Math.random() * 90) + 10; // 10-99
    const suffix = Math.floor(Math.random() * 300) + 1; // 1-300
    return `${prefix}/${prefix}/${suffix}`;
  }

  // DOM Elements
  const searchModal = document.getElementById('search-modal');
  const searchRecordsBtn = document.getElementById('search-records-btn');
  const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
  const filtersContainer = document.getElementById('filters-container');
  const collapsedFilters = document.getElementById('collapsed-filters');
  const resetSearchBtn = document.getElementById('reset-search-btn');
  const resetSearchCollapsedBtn = document.getElementById('reset-search-collapsed-btn');
  const addFilterBtn = document.getElementById('add-filter-btn');
  const filterDropdown = document.getElementById('filter-dropdown');
  const searchLoading = document.getElementById('search-loading');
  const noResultsMessage = document.getElementById('no-results-message');
  const tableResults = document.getElementById('table-results');
  const tableResultsBody = document.getElementById('table-results-body');
  const cardResults = document.getElementById('card-results');
  const fileDetailsView = document.getElementById('file-details-view');
  const resultsCount = document.getElementById('results-count');
  const viewTabs = document.querySelectorAll('.tab');
  const dashboardView = document.getElementById('dashboard-view');
  const fileHistoryView = document.getElementById('file-history-view');
  const reportsView = document.getElementById('reports-view');
   
  const deleteConfirmDialog = document.getElementById('delete-confirm-dialog');
  const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
  const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
  const newSearchFromDetailsBtn = document.getElementById('new-search-from-details-btn');
  const legalSearchReportView = document.getElementById('legal-search-report-view');
  const backToFileDetailsBtn = document.getElementById('back-to-file-details-btn');
  const printReportBtn = document.getElementById('print-report-btn');

  // Debug statements
  console.log("Search modal element:", searchModal);
  console.log("Search records button:", searchRecordsBtn);
  console.log("Search modal class list:", searchModal ? searchModal.classList : "Modal not found");

  // Add document ready event to ensure DOM is fully loaded
  document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded, initializing search elements");
    
    // Re-query elements to ensure they're available
    const searchModalRecheck = document.getElementById('search-modal');
    const searchRecordsBtnRecheck = document.getElementById('search-records-btn');
    
    console.log("Search modal element (recheck):", searchModalRecheck);
    console.log("Search records button (recheck):", searchRecordsBtnRecheck);
    
    // Add click handler directly to the button element
    if (searchRecordsBtnRecheck) {
      searchRecordsBtnRecheck.onclick = function() {
        console.log("Search records button clicked via direct onclick");
        if (searchModalRecheck) {
          searchModalRecheck.classList.remove('hidden');
          console.log("Modal hidden class removed, current classes:", searchModalRecheck.classList);
        } else {
          console.error("Search modal element not found when trying to show it");
        }
      };
    }
  });

  // State variables
  let currentView = 'table';
  let selectedFile = null;
  let transactionToDelete = null;
  let searchResults = [];
  let filtersCollapsed = false;

  // Initialize the search trends chart
  const initializeChart = () => {
    const ctx = document.getElementById('searchTrendsChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: monthlyData.map(d => d.month),
        datasets: [{
          label: 'Searches',
          data: monthlyData.map(d => d.searches),
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Monthly Search Volume'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Number of Searches'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Month'
            }
          }
        }
      }
    });
  };

  // Initialize the chart when the page loads
  document.addEventListener('DOMContentLoaded', initializeChart);

  // Event Listeners
  // Fix for the newSearchBtn reference - it doesn't exist, remove it
  // Instead, make sure searchRecordsBtn works properly
  if (searchRecordsBtn) {
    searchRecordsBtn.addEventListener('click', () => {
      console.log("Search records button clicked");
      searchModal.classList.remove('hidden');
    });
  } else {
    console.error("Search records button not found");
  }

  if (newSearchFromDetailsBtn) {
    newSearchFromDetailsBtn.addEventListener('click', () => {
      console.log("New search from details button clicked");
      searchModal.classList.remove('hidden');
    });
  }

  // Close modal when clicking outside
  searchModal.addEventListener('click', (e) => {
    if (e.target === searchModal) {
      searchModal.classList.add('hidden');
    }
  });

  // Toggle filters
  toggleFiltersBtn.addEventListener('click', () => {
    filtersCollapsed = !filtersCollapsed;
    if (filtersCollapsed) {
      filtersContainer.classList.add('hidden');
      collapsedFilters.classList.remove('hidden');
      toggleFiltersBtn.textContent = 'Expand Filters';
    } else {
      filtersContainer.classList.remove('hidden');
      collapsedFilters.classList.add('hidden');
      toggleFiltersBtn.textContent = 'Collapse Filters';
    }
  });

  // Reset search
  const resetSearch = () => {
    document.getElementById('fileNumber').value = '';
    document.getElementById('kangisFileNo').value = '';
    
    // Reset any other filters that might be added
    const additionalFilters = document.querySelectorAll('.additional-filter');
    additionalFilters.forEach(filter => {
      filter.remove();
    });
    
    // Reset results
    searchResults = [];
    resultsCount.textContent = '0';
    tableResultsBody.innerHTML = '';
    cardResults.innerHTML = '';
    
    // Hide results views
    tableResults.classList.add('hidden');
    cardResults.classList.add('hidden');
    fileDetailsView.classList.add('hidden');
    noResultsMessage.classList.add('hidden');
    
    // Reset selected file
    selectedFile = null;
  };

  resetSearchBtn.addEventListener('click', resetSearch);
  resetSearchCollapsedBtn.addEventListener('click', resetSearch);

  // Toggle filter dropdown
  addFilterBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    filterDropdown.classList.toggle('hidden');
  });

  // Close filter dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (!addFilterBtn.contains(e.target) && !filterDropdown.contains(e.target)) {
      filterDropdown.classList.add('hidden');
    }
  });

  // Add filter when clicking on dropdown item
  filterDropdown.addEventListener('click', (e) => {
    if (e.target.hasAttribute('data-filter')) {
      const filterId = e.target.getAttribute('data-filter');
      addFilter(filterId);
      filterDropdown.classList.add('hidden');
    }
  });

  // Add a new filter to the filters container
  const addFilter = (filterId) => {
    // Check if filter already exists
    if (document.getElementById(filterId)) {
      return;
    }

    const filterLabels = {
      newKangisFileNo: 'New KANGIS File No.',
      guarantorName: 'Guarantor Name',
      guaranteeName: 'Guarantee Name',
      lga: 'LGA',
      district: 'District',
      location: 'Location',
      plotNumber: 'Plot Number',
      planNumber: 'Plan Number',
      size: 'Size',
      caveat: 'Caveat'
    };

    const filterDiv = document.createElement('div');
    filterDiv.className = 'flex items-center gap-2 mb-2 additional-filter';
    filterDiv.id = filterId + '-filter';

    if (filterId === 'lga' || filterId === 'caveat') {
      // Create select for LGA or Caveat
      const options = filterId === 'lga' 
        ? ['Dala', 'Fagge', 'Gwale', 'Kano Municipal', 'Nassarawa', 'Tarauni', 'Ungogo']
        : ['Yes', 'No'];
      
      filterDiv.innerHTML = `
        <span class="badge badge-outline">${filterLabels[filterId]}</span>
        <div class="select-wrapper flex-grow">
          <select id="${filterId}" class="select">
            <option value="">Select ${filterLabels[filterId]}</option>
            ${options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
          </select>
          <div class="select-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </div>
      `;
    } else {
      // Create input for other filters
      filterDiv.innerHTML = `
        <span class="badge badge-outline">${filterLabels[filterId]}</span>
        <input type="text" id="${filterId}" placeholder="Enter ${filterLabels[filterId].toLowerCase()}" class="flex-grow px-3 py-2 border border-gray-300 rounded-md">
      `;
    }

    // Add remove button
    const removeBtn = document.createElement('button');
    removeBtn.className = 'h-8 w-8 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-100';
    removeBtn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    `;
    removeBtn.addEventListener('click', () => {
      filterDiv.remove();
      performSearch();
    });

    filterDiv.appendChild(removeBtn);
    filtersContainer.insertBefore(filterDiv, addFilterBtn.parentNode);

    // Add event listener to the new input/select
    const input = document.getElementById(filterId);
    if (input.tagName === 'SELECT') {
      input.addEventListener('change', performSearch);
    } else {
      input.addEventListener('input', performSearch);
    }
  };

  // Perform search based on filter values
  const performSearch = () => {
    // Get all filter values
    const filters = {
      fileNumber: document.getElementById('fileNumber').value,
      kangisFileNo: document.getElementById('kangisFileNo').value
    };

    // Add any additional filters
    const additionalFilters = document.querySelectorAll('.additional-filter');
    additionalFilters.forEach(filter => {
      const input = filter.querySelector('input, select');
      if (input && input.value) {
        filters[input.id] = input.value;
      }
    });

    // Check if any filter has a value
    const hasFilters = Object.values(filters).some(value => value);
    if (!hasFilters) {
      searchResults = [];
      resultsCount.textContent = '0';
      tableResults.classList.add('hidden');
      cardResults.classList.add('hidden');
      noResultsMessage.classList.add('hidden');
      return;
    }

    // Show loading
    searchLoading.classList.remove('hidden');
    tableResults.classList.add('hidden');
    cardResults.classList.add('hidden');
    noResultsMessage.classList.add('hidden');
    fileDetailsView.classList.add('hidden');

    // --- CHANGED LOGIC STARTS HERE ---
    // If File Number is filled, use only that as the query
    let query = '';
    if (filters.fileNumber) {
      query = filters.fileNumber;
    } else if (filters.kangisFileNo) {
      query = filters.kangisFileNo;
    } else if (filters.newKangisFileNo) {
      query = filters.newKangisFileNo;
    } else {
      // fallback: combine all filters as before
      query = Object.values(filters).filter(v => v).join(' ');
    }
    // --- CHANGED LOGIC ENDS HERE ---

    // AJAX call to the server
    $.ajax({
      url: '{{ route("legalsearch.search") }}',
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        query: query
      },
      success: function(data) {
        // Hide loading
        searchLoading.classList.add('hidden');

        // Combine results from all tables
        searchResults = [
          ...data.property_records,
          ...data.mother_applications,
          ...data.subapplications,
          ...data.registered_instruments,
          ...data.cofo
        ];

        // Update results count
        resultsCount.textContent = searchResults.length;

        // Show appropriate view
        if (searchResults.length === 0) {
          noResultsMessage.classList.remove('hidden');
        } else {
          // Automatically collapse filters when results are found
          if (searchResults.length > 0 && !filtersCollapsed) {
            filtersCollapsed = true;
            filtersContainer.classList.add('hidden');
            collapsedFilters.classList.remove('hidden');
            toggleFiltersBtn.textContent = 'Expand Filters';
            
            // Update active filters summary
            const activeFilters = Object.entries(filters)
              .filter(([_, value]) => value)
              .map(([key, value]) => {
                const filterLabels = {
                  fileNumber: 'File Number',
                  kangisFileNo: 'KANGIS File No.',
                  newKangisFileNo: 'New KANGIS File No.',
                  guarantorName: 'Guarantor Name',
                  guaranteeName: 'Guarantee Name',
                  lga: 'LGA',
                  district: 'District',
                  location: 'Location',
                  plotNumber: 'Plot Number',
                  planNumber: 'Plan Number',
                  size: 'Size',
                  caveat: 'Caveat'
                };
                return `${filterLabels[key]}: ${value}`;
              })
              .join(', ');
            
            document.getElementById('active-filters-summary').textContent = activeFilters;
          }
          
          renderSearchResults();
        }
      },
      error: function(error) {
        // Hide loading
        searchLoading.classList.add('hidden');
        noResultsMessage.classList.remove('hidden');
        console.error('Error performing search:', error);
      }
    });
  };

  // Render search results based on current view
  const renderSearchResults = () => {
    if (currentView === 'table') {
      renderTableResults();
      tableResults.classList.remove('hidden');
      cardResults.classList.add('hidden');
    } else {
      renderCardResults();
      cardResults.classList.remove('hidden');
      tableResults.classList.add('hidden');
    }
  };

  // Render table results
  const renderTableResults = () => {
    tableResultsBody.innerHTML = '';
    
    searchResults.forEach((file, index) => {
      const row = document.createElement('tr');
      row.className = 'hover:bg-gray-50 transition-colors';
      row.innerHTML = `
        <td class="p-2 text-sm">${file.mlsFNo || file.MLSFileNo || file.fileNo || file.fileno || 'N/A'}</td>
        <td class="p-2 text-sm">${file.kangisFileNo || file.KAGISFileNO || 'N/A'}</td>
        <td class="p-2 text-sm">${file.NewKANGISFileno || file.NewKANGISFileNo || 'N/A'}</td>
        <td class="p-2 text-sm">${getMappedValue(file, 'grantor')}</td>
        <td class="p-2 text-sm">${getMappedValue(file, 'grantee')}</td>
        <td class="p-2 text-sm">${getMappedValue(file, 'lga')}</td>
        <td class="p-2 text-sm">${file.property_house_no && file.property_plot_no && file.property_street_name && file.property_district && file.property_lga ? `${file.property_house_no},${file.property_plot_no},${file.property_street_name},${file.property_district},${file.property_lga}` : getMappedValue(file, 'location')}</td>
        <td class="p-2 text-sm">${getMappedValue(file, 'plotNo')}</td>
        <td class="p-2 text-sm">${getMappedValue(file, 'transactionType')}</td>
        <td class="p-2 text-sm">${getMappedValue(file, 'size')}</td>
        <td class="p-2 text-sm font-medium ${file.caveat === 'Yes' ? 'text-red-600' : ''}">${file.caveat || 'N/A'}</td>
        <td class="p-2 text-sm">
          <button class="view-file-btn inline-flex items-center px-2 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50" data-index="${index}">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0  5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
            </svg>
            View Records
          </button>
        </td>
      `;
      
      tableResultsBody.appendChild(row);
    });
    
    // Add event listeners to view buttons
    document.querySelectorAll('.view-file-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const index = parseInt(btn.getAttribute('data-index'));
        console.log('View button clicked for index:', index);
        
        selectedFile = searchResults[index];
        console.log('Selected file:', selectedFile);
        
        // Close search modal
        searchModal.classList.add('hidden');
        
        // Show file history view directly instead of file details
        dashboardView.classList.add('hidden');
        fileHistoryView.classList.remove('hidden');
        
        // Populate file details
        renderFileHistory();
      });
    });
  };

  // Render card results
  const renderCardResults = () => {
    cardResults.innerHTML = '';
    
    searchResults.forEach((file, index) => {
      const card = document.createElement('div');
      card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow cursor-pointer';
      card.setAttribute('data-index', index);
      card.innerHTML = `
        <div class="p-4">
          <div class="flex justify-between items-start mb-3">
            <div>
              <div class="font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                </svg>
                ${file.mlsFNo || file.MLSFileNo || file.fileNo || file.fileno || 'N/A'}
              </div>
              <div class="text-sm text-gray-500 mt-1">
                KANGIS: ${file.kangisFileNo || file.KAGISFileNO || 'N/A'} | New KANGIS: ${file.NewKANGISFileno || file.NewKANGISFileNo || 'N/A'}
              </div>
            </div>
            <div>
              <span class="text-gray-500">Guarantee:</span> ${getMappedValue(file, 'grantee')}
            </div>
            <div>
              <span class="text-gray-500">LGA:</span> ${getMappedValue(file, 'lga')}
            </div>
            <div>
              <span class="text-gray-500">Location:</span> ${file.property_house_no && file.property_plot_no && file.property_street_name && file.property_district && file.property_lga ? `${file.property_house_no},${file.property_plot_no},${file.property_street_name},${file.property_district},${file.property_lga}` : getMappedValue(file, 'location')}
            </div>
            <div>
              <span class="text-gray-500">Plot No:</span> ${getMappedValue(file, 'plotNo')}
            </div>
            <div>
              <span class="text-gray-500">Size:</span> ${getMappedValue(file, 'size')}
            </div>
            <div class="col-span-2">
              <span class="text-gray-500">Transaction Type:</span> ${getMappedValue(file, 'transactionType')}
            </div>
          </div>
        </div>
      `;
      
      card.addEventListener('click', () => {
        const cardIndex = parseInt(card.getAttribute('data-index'));
        console.log('Card clicked for index:', cardIndex);
        
        selectedFile = searchResults[cardIndex];
        console.log('Selected file from card:', selectedFile);
        
        // Close search modal
        searchModal.classList.add('hidden');
        
        // Show file history view directly
        dashboardView.classList.add('hidden');
        fileHistoryView.classList.remove('hidden');
        
        // Populate file details
        renderFileHistory();
      });
      
      cardResults.appendChild(card);
    });
  };
  
  // Render file history (the side-by-side layout shown in the screenshot)
  const renderFileHistory = () => {
    if (!selectedFile) {
      console.log('No selected file in renderFileHistory');
      return;
    }
    
    console.log('Rendering file history for:', selectedFile);
    
    // Update file reference in subtitle (with .0 fix)
    let fileRef = selectedFile.mlsFNo || selectedFile.MLSFileNo || selectedFile.fileNo || selectedFile.fileno || 'N/A';
    if (typeof fileRef === 'number' && fileRef % 1 === 0) {
      fileRef = Math.floor(fileRef).toString();
    } else if (typeof fileRef === 'string' && fileRef.endsWith('.0')) {
      fileRef = fileRef.replace('.0', '');
    }
    document.getElementById('file-reference').textContent = fileRef;
    
    // Update file information fields (with .0 fix and better field mapping)
    let fileNumber = selectedFile.mlsFNo || selectedFile.MLSFileNo || selectedFile.fileNo || selectedFile.fileno || 'N/A';
    if (typeof fileNumber === 'number' && fileNumber % 1 === 0) {
      fileNumber = Math.floor(fileNumber).toString();
    } else if (typeof fileNumber === 'string' && fileNumber.endsWith('.0')) {
      fileNumber = fileNumber.replace('.0', '');
    }
    document.getElementById('file-number-value').textContent = fileNumber;
    document.getElementById('kangis-file-number-value').textContent = selectedFile.kangisFileNo || selectedFile.KAGISFileNO || 'N/A';
    document.getElementById('new-kangis-file-number-value').textContent = selectedFile.NewKANGISFileno || selectedFile.NewKANGISFileNo || 'N/A';
    
    // Enhanced guarantor/guarantee mapping for mother and sub applications
    const guarantorValue = selectedFile.owner_fullname || selectedFile.mother_owner_fullname || 
                         selectedFile.first_name || selectedFile.corporate_name || 
                         selectedFile.multiple_owners_names || selectedFile.Assignor || 
                         selectedFile.Grantor || selectedFile.Mortgagor || selectedFile.Lessor || 
                         selectedFile.Surrenderor || selectedFile.originalAllottee || 'N/A';
    
    const guaranteeValue = selectedFile.sub_owner_fullname || selectedFile.multiple_owners_names || 
                         selectedFile.owner_fullname || selectedFile.Assignee || 
                         selectedFile.Grantee || selectedFile.Mortgagee || selectedFile.Lessee || 
                         selectedFile.Surrenderee || selectedFile.currentAllottee || 'N/A';
    
    document.getElementById('current-guarantor-value').textContent = guarantorValue;
    document.getElementById('current-guarantee-value').textContent = guaranteeValue;
    
    // Enhanced LGA mapping
    const lgaValue = selectedFile.property_lga || selectedFile.address_lga || 
                    selectedFile.lgsaOrCity || selectedFile.lga || selectedFile.lgaName || 'N/A';
    document.getElementById('lga-value').textContent = lgaValue;
    
    // Enhanced district mapping
    const districtValue = selectedFile.property_district || selectedFile.address_district || 
                         selectedFile.district || selectedFile.districtName || 'N/A';
    document.getElementById('district-value').textContent = districtValue;
    
    // Enhanced property type mapping - prioritize land_use
    const propertyTypeValue = selectedFile.land_use || selectedFile.landUse || 
                             selectedFile.landUseType || selectedFile.title_type || 
                             selectedFile.instrument_type || selectedFile.Type || 
                             selectedFile.residential_type || selectedFile.commercial_type || 
                             selectedFile.industrial_type || selectedFile.mixed_type || 'N/A';
    document.getElementById('property-type-value').textContent = propertyTypeValue;
    
    // Enhanced last transaction mapping
    const lastTransactionValue = selectedFile.transaction_type || selectedFile.instrument_type || 
                                selectedFile.application_status || selectedFile.deeds_status || 
                                selectedFile.planning_recommendation_status || 'N/A';
    document.getElementById('last-transaction-value').textContent = lastTransactionValue;
    
    // Render the transactions tables
    renderTransactionTables();
    
          
    // Default to property history tab
    switchTab('property-history');
  };
  
  // Get related transactions for a selected file
  const getRelatedTransactions = (file) => {
    console.log('=== getRelatedTransactions called ===');
    console.log('Selected file:', file);
    console.log('Search results available:', searchResults);
    console.log('Total search results count:', searchResults ? searchResults.length : 0);
    
    if (!searchResults || searchResults.length === 0 || !file) {
      console.log('No search results or file available, returning empty array');
      return [];
    }
    
    // Get the file identifiers from the selected file
    const selectedFileNumbers = [
      file.mlsFNo,
      file.MLSFileNo, 
      file.fileNo,
      file.fileno,
      file.kangisFileNo,
      file.KAGISFileNO,
      file.NewKANGISFileno,
      file.NewKANGISFileNo
    ].filter(num => num && num !== 'N/A' && num !== null && num !== undefined);
    
    console.log('Selected file numbers to match:', selectedFileNumbers);
    
    // Filter search results to only include records that match the selected file's identifiers
    const relatedRecords = searchResults.filter(result => {
      const resultFileNumbers = [
        result.mlsFNo,
        result.MLSFileNo,
        result.fileNo, 
        result.fileno,
        result.kangisFileNo,
        result.KAGISFileNO,
        result.NewKANGISFileno,
        result.NewKANGISFileNo
      ].filter(num => num && num !== 'N/A' && num !== null && num !== undefined);
      
      // Check if any of the selected file numbers match any of the result file numbers
      const hasMatch = selectedFileNumbers.some(selectedNum => 
        resultFileNumbers.some(resultNum => {
          // Convert both to strings and remove .0 for comparison
          const selectedStr = selectedNum.toString().replace('.0', '');
          const resultStr = resultNum.toString().replace('.0', '');
          return selectedStr === resultStr;
        })
      );
      
      if (hasMatch) {
        console.log('Found matching record:', result);
      }
      
      return hasMatch;
    });
    
    console.log('Filtered related records:', relatedRecords);
    console.log('Final return value count:', relatedRecords.length);
    return relatedRecords;
  };

  // Helper function to remove .0 from values
  const cleanNumericValue = (value) => {
    if (!value || value === 'N/A') return value;
    
    // Convert to string if it's a number
    let stringValue = value.toString();
    
    // Remove .0 from the end if present
    if (stringValue.endsWith('.0')) {
      stringValue = stringValue.replace('.0', '');
    }
    
    return stringValue;
  };

  // Helper function to get mapped field value
  const getMappedValue = (item, fieldType) => {
    const fieldMappings = {
      // Date fields - enhanced for applications
      date: [
        'transaction_date', 'deeds_date', 'deedsDate', 'certificateDate', 
        'instrumentDate', 'approval_date', 'planning_approval_date',
        'receipt_date', 'payment_date', 'accountant_signature_date',
        'created_at', 'updated_at'
      ],
      
      // Transaction type fields - enhanced for applications
      transactionType: [
        'transaction_type', 'instrument_type', 'title_type', 'typeForm', 
        'landUseType', 'application_status', 'deeds_status',
        'planning_recommendation_status', 'land_use', 'landUse'
      ],
      
      // Grantor/From party fields - enhanced for applications
      grantor: [
        'owner_fullname', 'mother_owner_fullname', 
        'first_name', 'corporate_name', 'multiple_owners_names',
        'Assignor', 'assignor', 'assignorName',
        'Grantor', 'Mortgagor', 'mortgagor', 
        'Lessor', 'lessor', 'Surrenderor', 'surrenderor',
        'originalAllottee', 'surrenderingPartyName',
        'applicant_title'
      ],
      
      // Grantee/To party fields - enhanced for applications
      grantee: [
        'sub_owner_fullname', 'multiple_owners_names', 'owner_fullname',
        'Assignee', 'assignee', 'Grantee', 
        'Mortgagee', 'mortgagee', 'Lessee', 'lessee',
        'Surrenderee', 'surrenderee', 'currentAllottee',
        'receivingPartyName', 'releaseeName',
        'first_name', 'corporate_name'
      ],
      
      // Registration number fields - enhanced
      serialNo: [
        'serialNo', 'serial_no', 'oldTitleSerialNo', 
        'rootRegistrationNumber', 'particularsRegistrationNumber',
        'volume_no', 'page_no'
      ],
      pageNo: ['pageNo', 'page_no', 'oldTitlePageNo'],
      volumeNo: ['volumeNo', 'volume_no', 'oldTitleVolumeNo'],
      
      // Size fields - enhanced
      size: ['size', 'plot_size', 'NoOfUnits', 'NoOfSections', 'NoOfBlocks'],
      
      // Comments fields - enhanced for applications
      comments: [
        'comments', 'additional_comments', 'recomm_comments', 
        'director_comments', 'application_comment', 'planning_recomm_comments'
      ],
      
      // Time fields
      time: ['deeds_time', 'deedsTime'],
      
      // Plot number fields - enhanced for applications
      plotNo: [
        'plot_no', 'plotNo', 'plotNumber', 'property_plot_no', 
        'address_plot_no', 'scheme_no'
      ],
      
      // LGA fields - enhanced for applications
      lga: [
        'property_lga', 'address_lga', 'lga', 'lgaName', 
        'lgsaOrCity'
      ],
      
      // District fields - enhanced for applications
      district: [
        'property_district', 'address_district', 'district', 
        'districtName'
      ],
      
      // Location/Address fields - enhanced for applications
      location: [
        'location', 'propertyAddress', 'propertyDescription', 
        'plotDescription', 'property_location', 'address',
        'property_street_name', 'address_street_name',
        'property_house_no', 'address_house_no'
      ],
      
      // Land use fields - enhanced for applications
      landUse: [
        'land_use', 'landUse', 'landUseType', 'residential_type',
        'commercial_type', 'industrial_type', 'mixed_type'
      ]
    };
    
    const fields = fieldMappings[fieldType] || [];
    for (const field of fields) {
      if (item[field] && item[field] !== null && item[field] !== '') {
        let value = item[field];
        // Remove .0 from numeric values that are actually integers
        if (typeof value === 'number' && value % 1 === 0) {
          value = Math.floor(value).toString();
        } else if (typeof value === 'string' && value.endsWith('.0')) {
          value = value.replace('.0', '');
        }
        return value;
      }
    }
    return 'N/A';
  };

  // Render all transaction tables
  const renderTransactionTables = () => {
    // Get related transactions for the selected file
    const relatedTransactions = getRelatedTransactions(selectedFile);
    
    console.log('Rendering transaction tables with:', relatedTransactions);
    
    // Property History (merged view of transactions)
    const propertyHistoryTable = document.getElementById('property-history-table');
    propertyHistoryTable.innerHTML = '';
    
    if (relatedTransactions.length > 0) {
      relatedTransactions.forEach(item => {
        console.log('Processing item:', item);
        
        const date = getMappedValue(item, 'date');
        const transactionType = getMappedValue(item, 'transactionType');
        const grantor = getMappedValue(item, 'grantor');
        const grantee = getMappedValue(item, 'grantee');
        const serialNo = getMappedValue(item, 'serialNo');
        const pageNo = getMappedValue(item, 'pageNo');
        const volumeNo = getMappedValue(item, 'volumeNo');
        const size = getMappedValue(item, 'size');
        const comments = getMappedValue(item, 'comments');
        
        console.log('Mapped values:', { date, transactionType, grantor, grantee, serialNo, pageNo, volumeNo, size, comments });
        
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>
            <div>${date}</div>
          </td>
          <td>${transactionType}</td>
          <td>${grantor}</td>
          <td>${grantee}</td>
          <td>${cleanNumericValue(serialNo)}/${cleanNumericValue(pageNo)}/${cleanNumericValue(volumeNo)}</td>
          <td>${size}</td>
          <td class="${item.caveat === 'Yes' ? 'text-red-600 font-medium' : ''}">${item.caveat || 'N/A'}</td>
          <td>${comments}</td>
          <td>
            <div class="flex space-x-2">
              <button class="edit-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <button class="delete-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 6h18"></path>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                  <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </div>
          </td>
        `;
        propertyHistoryTable.appendChild(row);
      });
    } else {
      propertyHistoryTable.innerHTML = `
        <tr>
          <td colspan="9" class="text-center py-4 text-gray-500">No property history records found.</td>
        </tr>
      `;
    }
    
    // Instrument Registration
    const instrumentRegistrationTable = document.getElementById('instrument-registration-table');
    instrumentRegistrationTable.innerHTML = '';
    
    // Filter for instrument records (registered_instruments table or records with instrument_type)
    const instrumentRecords = relatedTransactions.filter(item => 
      getMappedValue(item, 'transactionType') !== 'N/A' && 
      (item.instrument_type || item.MLSFileNo || item.KAGISFileNO || item.rootRegistrationNumber)
    );
    
    if (instrumentRecords.length > 0) {
      instrumentRecords.forEach(registration => {
        const date = getMappedValue(registration, 'date');
        const time = getMappedValue(registration, 'time');
        const transactionType = getMappedValue(registration, 'transactionType');
        const grantor = getMappedValue(registration, 'grantor');
        const grantee = getMappedValue(registration, 'grantee');
        const regNumber = getMappedValue(registration, 'serialNo');
        
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>
            <div>${date}</div>
            <div class="text-xs text-gray-600">${time}</div>
          </td>
          <td>${transactionType}</td>
          <td>${regNumber}</td>
          <td>${grantor} to ${grantee}</td>
          <td>${registration.created_by || registration.updated_by || 'N/A'}</td>
          <td>
            <div class="flex space-x-2">
              <button class="edit-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <button class="delete-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 6h18"></path>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                  <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </div>
          </td>
        `;
        instrumentRegistrationTable.appendChild(row);
      });
    } else {
      instrumentRegistrationTable.innerHTML = `
        <tr>
          <td colspan="6" class="text-center py-4 text-gray-500">No instrument registration records found.</td>
        </tr>
      `;
    }
    
    // Certificate of Occupancy
    const cofoTable = document.getElementById('cofo-table');
    cofoTable.innerHTML = '';
    
    // Filter for CofO records (cofo table or records with certificateDate)
    const cofoRecords = relatedTransactions.filter(item => 
      item.certificateDate || item.mlsfNo || item.kangisFileNo || item.currentAllottee || item.originalAllottee
    );
    
    if (cofoRecords.length > 0) {
      cofoRecords.forEach(cofo => {
        const serialNo = getMappedValue(cofo, 'serialNo');
        const pageNo = getMappedValue(cofo, 'pageNo');
        const volumeNo = getMappedValue(cofo, 'volumeNo');
        const date = getMappedValue(cofo, 'date');
        const grantee = getMappedValue(cofo, 'grantee');
        const landUse = getMappedValue(cofo, 'landUse');
        
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${cleanNumericValue(serialNo)}/${cleanNumericValue(pageNo)}/${cleanNumericValue(volumeNo)}</td>
          <td>
            <div>${date}</div>
          </td>
          <td>${grantee}</td>
          <td>${landUse}</td>
          <td>${cofo.term || cofo.occupancy || cofo.Period || 'N/A'}</td>
          <td>
            <div class="flex space-x-2">
              <button class="edit-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <button class="delete-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 6h18"></path>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                  <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </div>
          </td>
        `;
        cofoTable.appendChild(row);
      });
    } else {
      cofoTable.innerHTML = `
        <tr>
          <td colspan="6" class="text-center py-4 text-gray-500">No Certificate of Occupancy records found.</td>
        </tr>
      `;
    }
  };
  
  // Switch between tabs in the file details view
  const switchTab = (tabName) => {
    // Update active tab
    document.querySelectorAll('.tab').forEach(t => {
      if (t.getAttribute('data-tab') === tabName) {
        t.classList.add('active');
      } else {
        t.classList.remove('active');
      }
    });
    
    // Update visible content
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.remove('active');
    });
    document.getElementById(`${tabName}-tab`).classList.add('active');
  };

  // Default to property history tab instead of property transactions
  // Back to dashboard from file history view
  document.getElementById('back-to-dashboard-btn').addEventListener('click', () => {
    fileHistoryView.classList.add('hidden');
    dashboardView.classList.remove('hidden');
  });
  
  // Switch between table and card view
  document.querySelectorAll('[data-view]').forEach(tab => {
    tab.addEventListener('click', () => {
      // Remove active class from all tabs
      document.querySelectorAll('[data-view]').forEach(t => t.classList.remove('active'));
      // Add active class to clicked tab
      tab.classList.add('active');
      
      // Update current view
      currentView = tab.getAttribute('data-view');
      
      // Render search results
      renderSearchResults();
    });
  });

  

  // Add event delegation for delete action buttons and tabs
  document.addEventListener('click', (e) => {
    // Tab switching
    if (e.target.closest('.tab')) {
      const tabName = e.target.closest('.tab').getAttribute('data-tab');
      switchTab(tabName);
    }

    if (e.target.closest('.delete-action')) {
      // In a real app, you would show a confirmation dialog
      alert('Delete functionality would be implemented here.');
    }
    
    if (e.target.closest('.edit-action')) {
      // In a real app, you would open an edit form
      alert('Edit functionality would be implemented here.');
    }
    
    if (e.target.closest('#view-detailed-records-btn')) {
      // Show legal search report view
      fileHistoryView.classList.add('hidden');
      legalSearchReportView.classList.remove('hidden');
      
      // Render the legal search report
      renderLegalSearchReport();
    }
  });

  // Back to file details from legal search report view
  backToFileDetailsBtn.addEventListener('click', () => {
    legalSearchReportView.classList.add('hidden');
    fileHistoryView.classList.remove('hidden');
  });

  // Enhanced Print report with responsive handling
  printReportBtn.addEventListener('click', () => {
    // Optimize print layout based on data size
    optimizePrintLayout();
    
    // Add a small delay to ensure the report is fully rendered
    setTimeout(() => {
      window.print();
    }, 300);
  });

  // Function to optimize print layout based on data size
  const optimizePrintLayout = () => {
    const printDiv = document.querySelector('.print-div');
    const transactionRows = document.querySelectorAll('#report-transactions-table tbody tr');
    
    if (!printDiv) return;
    
    // Remove existing optimization classes
    printDiv.classList.remove('small-dataset', 'force-single-page');
    
    // Check if dataset is small (less than 5 rows)
    if (transactionRows.length <= 5) {
      printDiv.classList.add('small-dataset', 'force-single-page');
      
      // Adjust table layout for better single-page fit
      const table = document.querySelector('#report-transactions-table');
      if (table) {
        table.style.pageBreakInside = 'avoid';
        table.style.breakInside = 'avoid';
      }
      
      // Optimize header spacing for small datasets
      const headerSection = printDiv.querySelector('.mb-6');
      if (headerSection) {
        headerSection.style.marginBottom = '6px';
      }
      
      // Adjust property details section
      const propertySection = printDiv.querySelector('.space-y-6 > div:first-child');
      if (propertySection) {
        propertySection.style.marginBottom = '8px';
      }
    }
    
    // Ensure logos are properly positioned (left and right)
    const logoContainer = printDiv.querySelector('.flex-wrap');
    if (logoContainer) {
      logoContainer.style.display = 'flex';
      logoContainer.style.justifyContent = 'space-between';
      logoContainer.style.alignItems = 'center';
      logoContainer.style.marginBottom = '10px';
      
      const logos = logoContainer.querySelectorAll('img');
      if (logos.length >= 2) {
        logos[0].style.order = '1'; // Left logo
        logos[1].style.order = '3'; // Right logo
        
        const textContainer = logoContainer.querySelector('.text-center');
        if (textContainer) {
          textContainer.style.order = '2'; // Center text
          textContainer.style.flex = '1';
          textContainer.style.margin = '0 15px';
        }
      }
    }
    
    // Optimize table cell content for better printing
    const tableCells = printDiv.querySelectorAll('td, th');
    tableCells.forEach(cell => {
      cell.style.wordWrap = 'break-word';
      cell.style.overflowWrap = 'break-word';
      cell.style.hyphens = 'auto';
    });
    
    // Ensure watermark is properly positioned
    const watermark = document.querySelector('.watermark');
    if (watermark) {
      watermark.style.position = 'fixed';
      watermark.style.top = '50%';
      watermark.style.left = '50%';
      watermark.style.transform = 'translate(-50%, -50%) rotate(-45deg)';
      watermark.style.zIndex = '0';
      watermark.style.pointerEvents = 'none';
    }
  };

  // Function to handle print media queries and responsive adjustments
  const handlePrintMediaQuery = () => {
    const printMediaQuery = window.matchMedia('print');
    
    printMediaQuery.addListener((mq) => {
      if (mq.matches) {
        // Print mode activated
        optimizePrintLayout();
      }
    });
  };

  // Initialize print media query handler
  handlePrintMediaQuery();

  // Add CSS for better print handling
  const addPrintStyles = () => {
    const style = document.createElement('style');
    style.textContent = `
      @media print {
        /* Additional responsive print styles */
        .print-div {
          transform: none !important;
          zoom: 1 !important;
          -webkit-transform: none !important;
          -moz-transform: none !important;
        }
        
        /* Ensure proper A4 sizing */
        @page {
          size: A4;
          margin: 12mm 8mm;
        }
        
        /* Force single page for small datasets */
        .force-single-page {
          height: auto !important;
          max-height: none !important;
          page-break-after: avoid !important;
          page-break-inside: avoid !important;
          break-inside: avoid !important;
        }
        
        /* Optimize table for A4 width */
        .print-div table {
          table-layout: auto !important;
          width: 100% !important;
          max-width: 100% !important;
        }
        
        .print-div th,
        .print-div td {
          max-width: none !important;
          white-space: normal !important;
          word-break: break-word !important;
        }
        
        /* Logo positioning fix */
        .print-div .flex-wrap {
          display: flex !important;
          justify-content: space-between !important;
          align-items: center !important;
          width: 100% !important;
        }
        
        .print-div .flex-wrap > img:first-child {
          order: 1 !important;
          margin-right: auto !important;
        }
        
        .print-div .flex-wrap > div {
          order: 2 !important;
          flex: 1 !important;
          text-align: center !important;
          margin: 0 15px !important;
        }
        
        .print-div .flex-wrap > img:last-child {
          order: 3 !important;
          margin-left: auto !important;
        }
      }
    `;
    document.head.appendChild(style);
  };

  // Add the print styles when the page loads
  addPrintStyles();

  // Add this helper function to generate random time strings
const generateRandomTime = () => {
const hours = Math.floor(Math.random() * 12) + 1; // 1-12
const minutes = Math.floor(Math.random() * 60); // 0-59
const ampm = Math.random() > 0.5 ? 'AM' : 'PM';
return `${hours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
};

// Generate Reg. No in format X/X/Y where first two are same
const generateRegNo = () => {
const prefix = Math.floor(Math.random() * 90) + 10;
// Limit suffix to range 100-300
const suffix = Math.floor(Math.random() * 201) + 100;
return `${prefix}/${prefix}/${suffix}`;
};

  // Render legal search report
  const renderLegalSearchReport = () => {
    if (!selectedFile) return;

    // Set current date and time
    const currentDate = new Date().toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    
    const currentTime = new Date().toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    });
    
    // Update report header with timestamp info (with .0 fix)
    let reportFileRef = selectedFile.mlsFNo || selectedFile.MLSFileNo || selectedFile.fileNo || selectedFile.fileno || 'N/A';
    if (typeof reportFileRef === 'number' && reportFileRef % 1 === 0) {
      reportFileRef = Math.floor(reportFileRef).toString();
    } else if (typeof reportFileRef === 'string' && reportFileRef.endsWith('.0')) {
      reportFileRef = reportFileRef.replace('.0', '');
    }
    document.getElementById('report-file-reference').textContent = reportFileRef;
    document.getElementById('report-timestamp').textContent = `These details are as at ${currentDate} ${currentTime}`;
    document.getElementById('report-date').textContent = `Date: ${currentDate}`;
    document.getElementById('report-time').textContent = `Time: ${currentTime}`;
    
    // Update property details
    document.getElementById('report-file-numbers').textContent = `mlsfNo: ${selectedFile.mlsFNo || selectedFile.MLSFileNo || selectedFile.fileNo || 'N/A'} | kangisFileNo: ${selectedFile.kangisFileNo || selectedFile.KAGISFileNO || 'N/A'} | NewKANGISFileNo: ${selectedFile.NewKANGISFileno || selectedFile.NewKANGISFileNo || 'N/A'}`;
    document.getElementById('report-plot-number').textContent = selectedFile.plot_no || selectedFile.plotNo || "GP No. 1067/1 & 1067/2";
    document.getElementById('report-plan-number').textContent = selectedFile.planNumber || "LKN/RES/2021/3006";
    document.getElementById('report-plot-description').textContent = `${selectedFile.district || selectedFile.districtName || "Niger Street Nassarawa District"}, ${selectedFile.lgsaOrCity || selectedFile.lga || selectedFile.lgaName || "Nassarawa"} LGA`;
    
    // Generate QR code URL
    const qrCodeData = `File Number: MLSF: ${selectedFile.mlsFNo || selectedFile.MLSFileNo || selectedFile.fileNo || 'N/A'} | KANGIS: ${selectedFile.kangisFileNo || selectedFile.KAGISFileNO || 'N/A'} | New KANGIS: ${selectedFile.NewKANGISFileno || selectedFile.NewKANGISFileNo || 'N/A'}`;
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qrCodeData)}`;
    document.getElementById('report-qr-code').src = qrCodeUrl;
    
    // Get related transactions for the selected file
    const relatedTransactions = getRelatedTransactions(selectedFile);

    // Helper to find reg instrument by file number
    function findRegInstrumentByFileNo(fileNo) {
      if (!searchResults) return null;
      // Search in registered_instruments only
      const regInstruments = searchResults.filter(
        r =>
          r.instrument_type ||
          r.MLSFileNo ||
          r.KAGISFileNO ||
          r.rootRegistrationNumber
      );
      return regInstruments.find(
        ri =>
          ri.MLSFileNo == fileNo ||
          ri.KAGISFileNO == fileNo ||
          ri.NewKANGISFileNo == fileNo
      );
    }

    // Helper to get Registration Particulars for each transaction
    function getRegistrationParticulars(transaction) {
      // property_records table
      if (
        transaction.hasOwnProperty('serialNo') &&
        transaction.hasOwnProperty('pageNo') &&
        transaction.hasOwnProperty('volumeNo')
      ) {
        return `${cleanNumericValue(transaction.serialNo)}/${cleanNumericValue(transaction.pageNo)}/${cleanNumericValue(transaction.volumeNo)}`;
      }
      // mother_applications table
      if (
        transaction.hasOwnProperty('owner_fullname') &&
        transaction.hasOwnProperty('fileno')
      ) {
        let regInst = findRegInstrumentByFileNo(transaction.fileno);
        if (regInst) {
          return `${cleanNumericValue(regInst.volume_no)}/${cleanNumericValue(regInst.page_no)}/${cleanNumericValue(regInst.serial_no)}`;
        }
        return 'N/A/N/A/N/A';
      }
      // subapplications table
      if (
        transaction.hasOwnProperty('sub_owner_fullname') &&
        transaction.hasOwnProperty('fileno')
      ) {
        let regInst = findRegInstrumentByFileNo(transaction.fileno);
        if (regInst) {
          return `${cleanNumericValue(regInst.volume_no)}/${cleanNumericValue(regInst.page_no)}/${cleanNumericValue(regInst.serial_no)}`;
        }
        return 'N/A/N/A/N/A';
      }
      // cofo table
      if (
        transaction.hasOwnProperty('oldTitleSerialNo') &&
        transaction.hasOwnProperty('oldTitlePageNo') &&
        transaction.hasOwnProperty('oldTitleVolumeNo')
      ) {
        return `${cleanNumericValue(transaction.oldTitleSerialNo)}/${cleanNumericValue(transaction.oldTitlePageNo)}/${cleanNumericValue(transaction.oldTitleVolumeNo)}`;
      }
      // registered_instruments table
      if (
        transaction.hasOwnProperty('instrument_type') ||
        transaction.hasOwnProperty('rootRegistrationNumber')
      ) {
        return `${cleanNumericValue(transaction.volume_no)}/${cleanNumericValue(transaction.page_no)}/${cleanNumericValue(transaction.serial_no)}`;
      }
      return 'N/A/N/A/N/A';
    }

    // Create combined array of all transactions
    const allTransactions = [];

    relatedTransactions.forEach(transaction => {
      let regNo = getRegistrationParticulars(transaction);
      let category = 'Property Record';
      if (
        transaction.hasOwnProperty('instrument_type') ||
        transaction.hasOwnProperty('rootRegistrationNumber')
      ) {
        category = 'Instrument Registration';
      } else if (
        transaction.hasOwnProperty('oldTitleSerialNo') &&
        transaction.hasOwnProperty('oldTitlePageNo') &&
        transaction.hasOwnProperty('oldTitleVolumeNo')
      ) {
        category = 'Certificate of Occupancy';
      } else if (
        transaction.hasOwnProperty('owner_fullname') &&
        transaction.hasOwnProperty('fileno')
      ) {
        category = 'Sectional Title';
      } else if (
        transaction.hasOwnProperty('sub_owner_fullname') &&
        transaction.hasOwnProperty('fileno')
      ) {
        category = 'Sectional Title';
      }

      allTransactions.push({
        type: transaction.transaction_type || transaction.instrument_type || transaction.title_type || 'Record',
        date: transaction.transaction_date || transaction.deeds_date || transaction.certificateDate || transaction.approval_date || 'N/A',
        time: transaction.deeds_time || generateRandomTime(),
        transactionType: transaction.transaction_type || transaction.instrument_type || transaction.title_type || 'Record',
        grantor: transaction.owner_fullname || transaction.mother_owner_fullname || transaction.Assignor || transaction.Grantor || transaction.originalAllottee || transaction.first_name || transaction.corporate_name || 'N/A',
        grantee: transaction.sub_owner_fullname || transaction.multiple_owners_names || transaction.Assignee || transaction.Grantee || transaction.currentAllottee || 'N/A',
        regNo: regNo,
        size: transaction.size || transaction.plot_size || 'N/A',
        caveat: transaction.caveat || 'N/A',
        comments: transaction.comments || transaction.additional_comments || 'N/A',
        category: category,
        originalRecord: transaction
      });
    });

    // If we still don't have enough transactions, try to get them directly from the rendered tabs
    if (allTransactions.length <= 1) {
      console.log('=== BACKUP APPROACH: Getting data from rendered tabs ===');
      
      // Get data from Property History tab
      const propertyHistoryTable = document.getElementById('property-history-table');
      if (propertyHistoryTable && propertyHistoryTable.children.length > 0) {
        console.log(`Found ${propertyHistoryTable.children.length} rows in Property History tab`);
        for (let i = 0; i < propertyHistoryTable.children.length; i++) {
          const row = propertyHistoryTable.children[i];
          if (row.children.length >= 8) {
            const backupTransaction = {
              type: 'Property History',
              date: cleanNumericValue(row.children[0].textContent.trim()),
              time: generateRandomTime(),
              transactionType: cleanNumericValue(row.children[1].textContent.trim()),
              grantor: cleanNumericValue(row.children[2].textContent.trim()),
              grantee: cleanNumericValue(row.children[3].textContent.trim()),
              regNo: cleanNumericValue(row.children[4].textContent.trim()),
              size: cleanNumericValue(row.children[5].textContent.trim()),
              caveat: cleanNumericValue(row.children[6].textContent.trim()),
              comments: cleanNumericValue(row.children[7].textContent.trim()),
              category: 'Property History'
            };
            allTransactions.push(backupTransaction);
            console.log(`Added backup transaction from Property History:`, backupTransaction);
          }
        }
      }
      
      // Get data from Instrument Registration tab
      const instrumentTable = document.getElementById('instrument-registration-table');
      if (instrumentTable && instrumentTable.children.length > 0) {
        console.log(`Found ${instrumentTable.children.length} rows in Instrument Registration tab`);
        for (let i = 0; i < instrumentTable.children.length; i++) {
          const row = instrumentTable.children[i];
          if (row.children.length >= 5) {
            const backupTransaction = {
              type: 'Instrument Registration',
              date: cleanNumericValue(row.children[0].textContent.trim()),
              time: generateRandomTime(),
              transactionType: cleanNumericValue(row.children[1].textContent.trim()),
              grantor: cleanNumericValue(row.children[3].textContent.split(' to ')[0]) || 'N/A',
              grantee: cleanNumericValue(row.children[3].textContent.split(' to ')[1]) || 'N/A',
              regNo: cleanNumericValue(row.children[2].textContent.trim()),
              size: 'N/A',
              caveat: 'N/A',
              comments: 'N/A',
              category: 'Instrument Registration'
            };
            allTransactions.push(backupTransaction);
            console.log(`Added backup transaction from Instrument Registration:`, backupTransaction);
          }
        }
      }
      
      // Get data from CofO tab
      const cofoTable = document.getElementById('cofo-table');
      if (cofoTable && cofoTable.children.length > 0) {
        console.log(`Found ${cofoTable.children.length} rows in CofO tab`);
        for (let i = 0; i < cofoTable.children.length; i++) {
          const row = cofoTable.children[i];
          if (row.children.length >= 5) {
            const backupTransaction = {
              type: 'Certificate of Occupancy',
              date: cleanNumericValue(row.children[1].textContent.trim()),
              time: generateRandomTime(),
              transactionType: 'Certificate of Occupancy',
              grantor: 'Government',
              grantee: cleanNumericValue(row.children[2].textContent.trim()),
              regNo: cleanNumericValue(row.children[0].textContent.trim()),
              size: 'N/A',
              caveat: 'N/A',
              comments: cleanNumericValue(row.children[3].textContent.trim()),
              category: 'Certificate of Occupancy'
            };
            allTransactions.push(backupTransaction);
            console.log(`Added backup transaction from CofO:`, backupTransaction);
          }
        }
      }
      
      console.log(`After backup approach, total transactions: ${allTransactions.length}`);
    }
    
    console.log('=== FINAL TRANSACTIONS ARRAY ===');
    console.log('Total transactions processed:', allTransactions.length);
    console.log('All transactions for report:', allTransactions);

    
    // Map to report row format
    const mappedTransactions = allTransactions.map(transaction => {
      const date = getMappedValue(transaction, 'date');
      const time = getMappedValue(transaction, 'time');
      const transactionType = getMappedValue(transaction, 'transactionType');
      const grantor = getMappedValue(transaction, 'grantor');
      const grantee = getMappedValue(transaction, 'grantee');
      const serialNo = getMappedValue(transaction, 'serialNo');
      const pageNo = getMappedValue(transaction, 'pageNo');
      const volumeNo = getMappedValue(transaction, 'volumeNo');
      const size = getMappedValue(transaction, 'size');
      const comments = getMappedValue(transaction, 'comments');
      let category = 'Property Record';
      if (
        transaction.instrument_type ||
        transaction.rootRegistrationNumber ||
        transaction.MLSFileNo ||
        transaction.KAGISFileNO
      ) {
        category = 'Instrument Registration';
      } else if (
        transaction.certificateDate ||
        transaction.currentAllottee ||
        transaction.originalAllottee
      ) {
        category = 'Certificate of Occupancy';
      }
      return {
        type: transactionType,
        date: date,
        time: time !== 'N/A' ? time : generateRandomTime(),
        transactionType: transactionType,
        grantor: grantor,
        grantee: grantee,
        regNo: `${serialNo}/${pageNo}/${volumeNo}`,
        size: size,
        caveat: transaction.caveat || 'N/A',
        comments: comments,
        category: category
      };
    });

    // Sort by date (newest first)
    allTransactions.sort((a, b) => new Date(b.date) - new Date(a.date));

    // Render the combined transactions table
    const transactionsTable = document.getElementById('report-transactions-table');
    transactionsTable.innerHTML = '';

    if (allTransactions.length > 0) {
      allTransactions.forEach((transaction, index) => {
        // Use the correct registration particulars for each row
        const regParticulars = getRegistrationParticulars(transaction.originalRecord);
        const row = document.createElement('tr');
        row.innerHTML = `
          <td class="border border-gray-300 px-3 py-2">${index + 1}</td>
          <td class="border border-gray-300 px-3 py-2">${transaction.grantor}</td>
          <td class="border border-gray-300 px-3 py-2">${transaction.grantee}</td>
          <td class="border border-gray-300 px-3 py-2">
            ${transaction.transactionType}
            <div class="text-xs text-gray-500">${transaction.category}</div>
          </td>
          <td class="border border-gray-300 px-3 py-2">
            <div>${transaction.date}</div>
            <div class="text-xs text-gray-600">${transaction.time}</div>
          </td>
          <td class="border border-gray-300 px-3 py-2">${cleanNumericValue(regParticulars)}</td>
          <td class="border border-gray-300 px-3 py-2">${transaction.size}</td>
          <td class="border border-gray-300 px-3 py-2 ${transaction.caveat === 'Yes' ? 'text-red-600' : ''}">${transaction.caveat}</td>
          <td class="border border-gray-300 px-3 py-2">${transaction.comments}</td>
        `;
        transactionsTable.appendChild(row);
      });
    } else {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td colspan="9" class="border border-gray-300 px-3 py-2 text-center">No transaction history found</td>
      `;
      transactionsTable.appendChild(row);
    }
  };

  // Add input event listeners for search fields
  document.getElementById('fileNumber').addEventListener('input', performSearch);
  document.getElementById('kangisFileNo').addEventListener('input', performSearch);

  // Close modal when pressing Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      searchModal.classList.add('hidden');
      deleteConfirmDialog.classList.add('hidden');
    }
  });
</script>

