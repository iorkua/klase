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

    // Sample land records data
    const landRecords = [
      {
        id: 1,
        fileNumber: "COM-RES-2021-078",
        kangisFileNo: "KNML 12453",
        newKangisFileNo: "KN0004",
        guarantor: "Kano Market Development Authority",
        guarantee: "Suleiman Abubakar Trading Co.",
        lga: "Kano Municipal",
        district: "Sabon Gari Market",
        location: "Sabon Gari",
        propertyType: "Commercial",
        registrationParticulars: "Lease Agreement dated 05/04/2021",
        lastTransaction: "Lease Agreement",
        caveat: "No",
        size: "1000 Ha",
        plotNumber: "Plot 123",
        planNumber: "KN/PL/2021/123",
        history: [
          {
            id: 1,
            date: "2021-04-05",
            time: "10:30 AM",
            transactionType: "Lease Agreement",
            guarantor: "Kano Market Development Authority",
            guarantee: "Suleiman Abubakar Trading Co.",
            amount: "₦5,000,000",
            caveat: "No",
            size: "1000 Ha",
            comments: "Initial market stall lease"
          }
        ],
        propertyHistory: [
          {
            id: 1,
            date: "2021-04-05",
            time: "10:30 AM",
            event: "Initial Registration",
            authority: "Kano Land Registry",
            recipient: "Suleiman Abubakar Trading Co.",
            documentNo: "DOC/2021/001",
            size: "1000 Ha",
            comments: "First registration of property"
          }
        ],
        instrumentRegistrations: [
          {
            id: 1,
            registrationDate: "2021-04-10",
            registrationTime: "11:45 AM",
            instrumentType: "Lease Agreement",
            registrationNumber: "REG/2021/001",
            parties: "Kano Market Development Authority and Suleiman Abubakar Trading Co.",
            propertyDescription: "Commercial stall at Sabon Gari Market",
            registeredBy: "Land Registry Officer",
            caveat: "No"
          }
        ],
        cofoRecords: [
          {
            id: 1,
            cofoNumber: "C of O/KN/2021/001",
            issueDate: "2021-05-01",
            holderName: "Suleiman Abubakar Trading Co.",
            propertyDescription: "Commercial stall at Sabon Gari Market",
            landUse: "Commercial",
            term: "25 years",
            commencementDate: "2021-04-05",
            annualRent: "₦200,000",
            caveat: "No"
          }
        ]
      },
      {
        id: 2,
        fileNumber: "MLSF/KN/2023/002",
        kangisFileNo: "KG-002-2023",
        newKangisFileNo: "KNG-2023-002",
        guarantor: "Michael Johnson",
        guarantee: "Sarah Williams",
        lga: "Fagge",
        district: "North",
        location: "Fagge",
        propertyType: "Residential",
        registrationParticulars: "Deed of Assignment dated 20/06/2023",
        lastTransaction: "Lease",
        caveat: "No",
        size: "750 Ha",
        plotNumber: "Plot 456",
        planNumber: "KN/PL/2023/456",
        history: [
          {
            id: 2,
            date: "2023-06-20",
            time: "02:15 PM",
            transactionType: "Lease",
            guarantor: "Michael Johnson",
            guarantee: "Sarah Williams",
            amount: "₦3,500,000",
            caveat: "No",
            size: "750 Ha",
            comments: "Property leased for 25 years"
          },
          {
            id: 3,
            date: "2023-08-15",
            time: "11:30 AM",
            transactionType: "Deed of Assignment",
            guarantor: "Sarah Williams",
            guarantee: "Ahmed Abdullahi",
            amount: "₦4,200,000",
            caveat: "No",
            size: "750 Ha",
            comments: "Transfer of ownership rights"
          },
          {
            id: 4,
            date: "2023-10-10",
            time: "09:45 AM",
            transactionType: "Power of Attorney",
            guarantor: "Ahmed Abdullahi",
            guarantee: "Fatima Hassan",
            amount: "₦500,000",
            caveat: "No",
            size: "750 Ha",
            comments: "Legal representation granted"
          },
          {
            id: 5,
            date: "2023-12-05",
            time: "03:20 PM",
            transactionType: "Deed of Mortgage",
            guarantor: "Fatima Hassan",
            guarantee: "First Bank of Nigeria",
            amount: "₦8,000,000",
            caveat: "No",
            size: "750 Ha",
            comments: "Property mortgaged for loan facility"
          },
          {
            id: 6,
            date: "2024-02-18",
            time: "01:15 PM",
            transactionType: "Deed of Mortgage",
            guarantor: "Fatima Hassan",
            guarantee: "Musa Ibrahim Kano",
            amount: "₦12,500,000",
            caveat: "No",
            size: "750 Ha",
            comments: "Property sold with building plan approval"
          },
          {
            id: 7,
            date: "2024-05-22",
            time: "10:00 AM",
            transactionType: "Transfer of Title",
            guarantor: "Musa Ibrahim Kano",
            guarantee: "Aisha Bello Muhammad",
            amount: "₦15,000,000",
            caveat: "No",
            size: "750 Ha",
            comments: "Final transfer of ownership completed"
          }
        ],
        propertyHistory: [
          {
            id: 2,
            date: "2023-06-25",
            time: "02:30 PM",
            event: "Lease Registration",
            authority: "Kano Land Registry",
            recipient: "Sarah Williams",
            documentNo: "DOC/2023/002",
            size: "750 Ha",
            comments: "Lease agreement registered"
          },
          {
            id: 3,
            date: "2023-08-20",
            time: "12:00 PM",
            event: "Ownership Transfer",
            authority: "Kano Land Registry",
            recipient: "Ahmed Abdullahi",
            documentNo: "DOC/2023/003",
            size: "750 Ha",
            comments: "Deed of assignment registered"
          },
          {
            id: 4,
            date: "2024-02-25",
            time: "02:45 PM",
            event: "Sale Agreement Filing",
            authority: "Kano Land Registry",
            recipient: "Musa Ibrahim Kano",
            documentNo: "DOC/2024/001",
            size: "750 Ha",
            comments: "Deed of Mortgage filed and approved"
          }
        ],
        instrumentRegistrations: [
          {
            id: 2,
            registrationDate: "2023-06-25",
            registrationTime: "02:45 PM",
            instrumentType: "Lease Agreement",
            registrationNumber: "REG/2023/002",
            parties: "Michael Johnson and Sarah Williams",
            propertyDescription: "Residential property at Fagge North District",
            registeredBy: "Land Registry Officer",
            caveat: "No"
          },
          {
            id: 3,
            registrationDate: "2023-08-25",
            registrationTime: "01:30 PM",
            instrumentType: "Deed of Assignment",
            registrationNumber: "REG/2023/003",
            parties: "Sarah Williams and Ahmed Abdullahi",
            propertyDescription: "Residential property at Fagge North District",
            registeredBy: "Senior Registry Officer",
            caveat: "No"
          },
          {
            id: 4,
            registrationDate: "2024-05-27",
            registrationTime: "11:15 AM",
            instrumentType: "Transfer of Title",
            registrationNumber: "REG/2024/001",
            parties: "Musa Ibrahim Kano and Aisha Bello Muhammad",
            propertyDescription: "Residential property at Fagge North District",
            registeredBy: "Chief Registry Officer",
            caveat: "No"
          } 
        ],
        cofoRecords: [
          {
            id: 2,
            cofoNumber: "C of O/KN/2024/001",
            issueDate: "2023-07-01",
            holderName: "Sarah Williams",
            propertyDescription: "Residential property at Fagge North District",
            landUse: "Residential",
            term: "99 years",
            commencementDate: "2023-06-20",
            annualRent: "₦150,000",
            caveat: "No"
          }
        ]
      }
    ];

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

      // Simulate API call with timeout
      setTimeout(() => {
        // Filter land records based on filters
        searchResults = landRecords.filter(record => {
          return Object.entries(filters).every(([key, value]) => {
            if (!value) return true; // Skip empty filters
            
            const searchValue = value.toLowerCase();
            
            switch(key) {
              case 'fileNumber':
                return record.fileNumber.toLowerCase().includes(searchValue);
              case 'kangisFileNo':
                return record.kangisFileNo.toLowerCase().includes(searchValue);
              case 'newKangisFileNo':
                return record.newKangisFileNo.toLowerCase().includes(searchValue);
              case 'guarantorName':
                return record.guarantor.toLowerCase().includes(searchValue);
              case 'guaranteeName':
                return record.guarantee.toLowerCase().includes(searchValue);
              case 'lga':
                return record.lga === value;
              case 'district':
                return record.district.toLowerCase().includes(searchValue);
              case 'location':
                return record.location.toLowerCase().includes(searchValue);
              case 'plotNumber':
                return record.plotNumber.toLowerCase().includes(searchValue);
              case 'planNumber':
                return record.planNumber.toLowerCase().includes(searchValue);
              case 'size':
                return record.size.toLowerCase().includes(searchValue);
              case 'caveat':
                return record.caveat === value;
              default:
                return true;
            }
          });
        });

        // Update results count
        resultsCount.textContent = searchResults.length;

        // Hide loading
        searchLoading.classList.add('hidden');

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
      }, 500);
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
      
      searchResults.forEach(file => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition-colors';
        row.innerHTML = `
          <td class="p-2 text-sm">${file.fileNumber}</td>
          <td class="p-2 text-sm">${file.kangisFileNo}</td>
          <td class="p-2 text-sm">${file.newKangisFileNo}</td>
          <td class="p-2 text-sm">${file.guarantor}</td>
          <td class="p-2 text-sm">${file.guarantee}</td>
          <td class="p-2 text-sm">${file.lga}</td>
          <td class="p-2 text-sm">${file.location}</td>
          <td class="p-2 text-sm">${file.plotNumber}</td>
          <td class="p-2 text-sm">${file.registrationParticulars}</td>
          <td class="p-2 text-sm">${file.size}</td>
          <td class="p-2 text-sm font-medium ${file.caveat === 'Yes' ? 'text-red-600' : ''}">${file.caveat}</td>
          <td class="p-2 text-sm">
            <button class="view-file-btn inline-flex items-center px-2 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50" data-id="${file.id}">
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
          const fileId = parseInt(btn.getAttribute('data-id'));
          selectedFile = searchResults.find(file => file.id === fileId);
          
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
      
      searchResults.forEach(file => {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow cursor-pointer';
        card.innerHTML = `
          <div class="p-4">
            <div class="flex justify-between items-start mb-3">
              <div>
                <div class="font-medium flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                  </svg>
                  ${file.fileNumber}
                </div>
                <div class="text-sm text-gray-500 mt-1">
                  KANGIS: ${file.kangisFileNo} | New KANGIS: ${file.newKangisFileNo}
                </div>
              </div>
              <div>
                <span class="text-gray-500">Guarantee:</span> ${file.guarantee}
              </div>
              <div>
                <span class="text-gray-500">LGA:</span> ${file.lga}
              </div>
              <div>
                <span class="text-gray-500">Location:</span> ${file.location}
              </div>
              <div>
                <span class="text-gray-500">Plot No:</span> ${file.plotNumber}
              </div>
              <div>
                <span class="text-gray-500">Size:</span> ${file.size}
              </div>
              <div class="col-span-2">
                <span class="text-gray-500">Registration Particulars:</span> ${file.registrationParticulars}
              </div>
            </div>
          </div>
        `;
        
        card.addEventListener('click', () => {
          selectedFile = file;
          
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
      if (!selectedFile) return;
      
      // Update file reference in subtitle
      document.getElementById('file-reference').textContent = selectedFile.fileNumber;
      
      // Update file information fields
      document.getElementById('file-number-value').textContent = selectedFile.fileNumber;
      document.getElementById('kangis-file-number-value').textContent = selectedFile.kangisFileNo;
      document.getElementById('new-kangis-file-number-value').textContent = selectedFile.newKangisFileNo;
      document.getElementById('current-guarantor-value').textContent = selectedFile.guarantor;
      document.getElementById('current-guarantee-value').textContent = selectedFile.guarantee;
      document.getElementById('lga-value').textContent = selectedFile.lga;
      document.getElementById('district-value').textContent = selectedFile.district;
      document.getElementById('property-type-value').textContent = selectedFile.propertyType || 'N/A';
      document.getElementById('last-transaction-value').textContent = selectedFile.lastTransaction;
      
      // Render the transactions tables
      renderTransactionTables();
      
      // Set up tab switching
      document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
          const tabName = tab.getAttribute('data-tab');
          switchTab(tabName);
        });
      });
      
      // Default to property history tab
      switchTab('property-history');
    };
    
    // Render all transaction tables
    const renderTransactionTables = () => {
      // Property History (merged view of transactions)
      const propertyHistoryTable = document.getElementById('property-history-table');
      propertyHistoryTable.innerHTML = '';
      
      // Create a combined array of history items
      const combinedHistory = [];
      
      // Add items from history array (previously property transactions)
      if (selectedFile.history && selectedFile.history.length > 0) {
        selectedFile.history.forEach(transaction => {
          combinedHistory.push({
            date: transaction.date,
            time: transaction.time || generateRandomTime(), // Use existing time or generate random
            transactionType: transaction.transactionType,
            grantor: transaction.guarantor,
            grantee: transaction.guarantee,
            documentNo: '-',
            size: transaction.size || 'N/A',
            caveat: transaction.caveat,
            comments: transaction.comments || 'N/A',
            isTransaction: true
          });
        });
      }
      
      // Add items from propertyHistory array
      if (selectedFile.propertyHistory && selectedFile.propertyHistory.length > 0) {
        selectedFile.propertyHistory.forEach(history => {
          combinedHistory.push({
            date: history.date,
            time: history.time || generateRandomTime(), // Use existing time or generate random
            transactionType: history.event,
            grantor: history.authority,
            grantee: history.recipient,
            documentNo: history.documentNo,
            size: history.size || 'N/A',
            caveat: '-',
            comments: history.comments || 'N/A',
            isTransaction: false
          });
        });
      }
      
      // Sort the combined history by date (newest first)
      combinedHistory.sort((a, b) => new Date(b.date) - new Date(a.date));
      
      if (combinedHistory.length > 0) {
        combinedHistory.forEach(item => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>
              <div>${item.date}</div>
              <div class="text-xs text-gray-600">${item.time}</div>
            </td>
            <td>${item.transactionType}</td>
            <td>${item.grantor}</td>
            <td>${item.grantee}</td>
            <td>${item.documentNo}</td>
            <td>${item.size}</td>
            <td class="${item.caveat === 'Yes' ? 'text-red-600 font-medium' : ''}">${item.caveat}</td>
            <td>${item.comments}</td>
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
      
      if (selectedFile.instrumentRegistrations && selectedFile.instrumentRegistrations.length > 0) {
        selectedFile.instrumentRegistrations.forEach(registration => {
          // Ensure we have a consistent time field
          registration.registrationTime = registration.registrationTime || generateRandomTime();
          
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>
              <div>${registration.registrationDate}</div>
              <div class="text-xs text-gray-600">${registration.registrationTime}</div>
            </td>
            <td>${registration.instrumentType}</td>
            <td>${registration.registrationNumber}</td>
            <td>${registration.parties}</td>
            <td>${registration.registeredBy}</td>
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
      
      if (selectedFile.cofoRecords && selectedFile.cofoRecords.length > 0) {
        selectedFile.cofoRecords.forEach(cofo => {
          // Add issue time if not present
          cofo.issueTime = cofo.issueTime || generateRandomTime();
          
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${cofo.cofoNumber}</td>
            <td>
              <div>${cofo.issueDate}</div>
              <div class="text-xs text-gray-600">${cofo.issueTime}</div>
            </td>
            <td>${cofo.holderName}</td>
            <td>${cofo.landUse}</td>
            <td>${cofo.term}</td>
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

    

    // Add event delegation for delete action buttons
    document.addEventListener('click', (e) => {
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

    // Print report
    printReportBtn.addEventListener('click', () => {
      // Add a small delay to ensure the report is fully rendered
      setTimeout(() => {
        window.print();
      }, 200);
    });

    // Add this helper function to generate random time strings
const generateRandomTime = () => {
  const hours = Math.floor(Math.random() * 12) + 1; // 1-12
  const minutes = Math.floor(Math.random() * 60); // 0-59
  const ampm = Math.random() > 0.5 ? 'AM' : 'PM';
  return `${hours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
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
      
      // Update report header with timestamp info
      document.getElementById('report-file-reference').textContent = selectedFile.fileNumber;
      document.getElementById('report-timestamp').textContent = `These details are as at ${currentDate} ${currentTime}`;
      document.getElementById('report-date').textContent = `Date: ${currentDate}`;
      document.getElementById('report-time').textContent = `Time: ${currentTime}`;
      
      // Update property details
      document.getElementById('report-file-numbers').textContent = `mlsfNo: ${selectedFile.fileNumber} | kangisFileNo: ${selectedFile.kangisFileNo} | NewKANGISFileNo: ${selectedFile.newKangisFileNo}`;
      document.getElementById('report-plot-number').textContent = selectedFile.plotNumber || "GP No. 1067/1 & 1067/2";
      document.getElementById('report-plan-number').textContent = selectedFile.planNumber || "LKN/RES/2021/3006";
      document.getElementById('report-plot-description').textContent = `${selectedFile.district || "Niger Street Nassarawa District"}, ${selectedFile.lga || "Nassarawa"} LGA`;
      
      // Generate QR code URL
      const qrCodeData = `File Number: MLSF: ${selectedFile.fileNumber} | KANGIS: ${selectedFile.kangisFileNo} | New KANGIS: ${selectedFile.newKangisFileNo}`;
      const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qrCodeData)}`;
      document.getElementById('report-qr-code').src = qrCodeUrl;
      
      // Render transaction history table
      const transactionsTable = document.getElementById('report-transactions-table');
      transactionsTable.innerHTML = '';
      
      if (selectedFile.history && selectedFile.history.length > 0) {
        selectedFile.history.forEach((transaction, index) => {
          // Ensure time consistency - use the existing time or generate one if needed
          transaction.time = transaction.time || generateRandomTime();
          
          const row = document.createElement('tr');
          row.innerHTML = `
            <td class="border border-gray-300 px-3 py-2">${index + 1}</td>
            <td class="border border-gray-300 px-3 py-2">${transaction.guarantor}</td>
            <td class="border border-gray-300 px-3 py-2">${transaction.guarantee}</td>
            <td class="border border-gray-300 px-3 py-2">${transaction.transactionType}</td>
            <td class="border border-gray-300 px-3 py-2">
              <div>${transaction.date}</div>
              <div class="text-xs text-gray-600">${transaction.time}</div>
            </td>
            <td class="border border-gray-300 px-3 py-2">${index + 1}/${index + 1}/1</td>
            <td class="border border-gray-300 px-3 py-2">${transaction.size || "0.0192ha"}</td>
            <td class="border border-gray-300 px-3 py-2 ${transaction.caveat === 'Yes' ? 'text-red-600' : ''}">${transaction.caveat}</td>
            <td class="border border-gray-300 px-3 py-2">${transaction.comments || "Transfer registered"}</td>
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
