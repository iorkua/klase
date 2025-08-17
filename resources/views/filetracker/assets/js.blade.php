<script>
  $(document).ready(function() {
    // API Base URL
    const API_BASE = '/api';
    
    // Current page and filters
    let currentPage = 1;
    let currentFilters = {};
    
    // Try to load data from API, but keep original UI functionality
    tryLoadApiData();
    
    // Toggle RFID mode
    $('#rfid-mode').change(function() {
      if ($(this).is(':checked')) {
        $('#scan-rfid-btn').removeClass('hidden');
      } else {
        $('#scan-rfid-btn').addClass('hidden');
      }
    });
    
    // RFID scan button
    $('#scan-rfid-btn').click(function() {
      const $button = $(this);
      $button.html(`
        <svg class="animate-spin h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Scanning...
      `);
      $button.addClass('bg-yellow-100 text-yellow-800 border-yellow-300');
      $button.prop('disabled', true);
      
      // Simulate scanning process
      setTimeout(() => {
        $button.html(`
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
          Scan RFID Tags
        `);
        $button.removeClass('bg-yellow-100 text-yellow-800 border-yellow-300');
        $button.prop('disabled', false);
        
        // Show results modal
        $('#rfid-modal').removeClass('hidden');
      }, 2000);
    });
    
    // Close RFID modal
    $('#close-rfid-modal').click(function() {
      $('#rfid-modal').addClass('hidden');
    });
    
    // Tab functionality (original)
    $('.tab-button').click(function() {
      const tabId = $(this).data('tab');
      
      // Update active tab button
      $('.tab-button').removeClass('active bg-white shadow');
      $(this).addClass('active bg-white shadow');
      
      // Show/hide rows based on tab
      if (tabId === 'all') {
        $('.file-row').show();
      } else {
        $('.file-row').hide();
        $('.file-row[data-status="' + tabId + '"]').show();
      }
    });
    
    // File view functionality (clicking on file rows or view buttons)
    $('.file-row, .file-view-btn').click(function(e) {
      e.preventDefault();
      
      // Get the tracking ID from the row
      const trackingId = $(this).closest('tr').data('tracking-id') || $(this).data('tracking-id');
      
      if (!trackingId) {
        console.error('No tracking ID found');
        return;
      }
      
      // Highlight the selected row
      $('.file-row').removeClass('bg-gray-50');
      $(this).closest('tr').addClass('bg-gray-50');
      
      // Load file details from API
      loadFileDetails(trackingId);
    });

    // View buttons from RFID modal
    $('.view-file-btn').click(function() {
      const fileId = $(this).closest('tr').find('td:nth-child(2)').text();
      const fileNumber = $(this).closest('tr').find('td:nth-child(3)').text();
      
      // Close the modal
      $('#rfid-modal').addClass('hidden');
      
      // Update file details
      updateFileDetails(fileId, fileNumber, 'in-process');
      
      // Find and highlight the corresponding row
      $('.file-row').removeClass('bg-gray-50');
      $('.file-row').each(function() {
        if($(this).find('td:first').text() === fileId) {
          $(this).addClass('bg-gray-50');
        }
      });
    });
    
    // Function to update file details sidebar
    function updateFileDetails(fileId, fileNumber, status) {
      // This is simulated - in a real app you'd fetch data from the server
      
      // Update file ID and number
      $('.file-details h2 + p').text(fileId);
      
      // Update status badge
      let statusText = 'In Process';
      let badgeClass = 'badge-default';
      
      if (status === 'pending') {
        statusText = 'Pending';
        badgeClass = 'badge-warning';
      } else if (status === 'on-hold') {
        statusText = 'On Hold';
        badgeClass = 'badge-destructive';
      } else if (status === 'awaiting') {
        statusText = 'Awaiting Approval';
        badgeClass = 'badge-secondary';
      } else if (status === 'completed') {
        statusText = 'Completed';
        badgeClass = 'badge-outline';
      }
      
      $('.file-details .badge').attr('class', 'badge ' + badgeClass).text(statusText);
      
      // Update file number in details
      $('.file-details .text-xs.font-medium:contains("RES")').text(fileNumber);
    }
    
    // Generate QR code (if qrcodejs library is available)
    if (typeof QRCode !== 'undefined') {
      const qrElement = document.getElementById('qr-code');
      if (qrElement) {
        const qrData = JSON.stringify({
          id: "TRK-2023-001",
          fileNumber: "RES-2015-4859",
          kangisFileNo: "KNGP 00338",
          newKangisFileNo: "KNO001",
          dateReceived: "2023-06-15",
          dueDate: "2023-06-30"
        });
        
        // Clear previous content
        qrElement.innerHTML = '';
        
        // Create new QR code
        const qr = new QRCode(qrElement, {
          text: qrData,
          width: 96,
          height: 96,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H
        });
      }
    }
    
    // Search functionality (original behavior)
    $('#search-input').on('keyup', function() {
      const value = $(this).val().toLowerCase();
      $('.file-row').filter(function() {
        const rowText = $(this).text().toLowerCase();
        $(this).toggle(rowText.indexOf(value) > -1);
      });
    });
    
    // API Integration Functions (background functionality)
    
    // Try to load data from API
    function tryLoadApiData() {
      $.ajax({
        url: `${API_BASE}/file-trackings?per_page=10`,
        method: 'GET',
        success: function(response) {
          if (response.success && response.data.data && response.data.data.length > 0) {
            console.log('API data available:', response.data.data.length, 'records');
            // Optionally enhance the existing table with API data
            enhanceTableWithApiData(response.data.data);
          }
        },
        error: function(xhr) {
          console.log('API not available, using static data');
        }
      });
    }
    
    // Enhance existing table with API data
    function enhanceTableWithApiData(apiData) {
      // Add RFID indicators to existing rows if they have RFID tags
      $('.file-row').each(function(index) {
        if (apiData[index] && apiData[index].rfid_tag) {
          const fileNumberCell = $(this).find('td:nth-child(2) .flex');
          fileNumberCell.append('<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">RFID</span>');
        }
      });
    }
    
    // RFID scan functionality (enhanced with API)
    function scanRfidTag(rfidTag) {
      $.ajax({
        url: `${API_BASE}/rfid/scan/${rfidTag}`,
        method: 'GET',
        success: function(response) {
          if (response.success) {
            showRfidScanResult(response.data);
          } else {
            showError('RFID tag not found');
          }
        },
        error: function(xhr) {
          console.error('Error scanning RFID tag:', xhr);
          // Fallback to showing the modal with static data
          $('#rfid-modal').removeClass('hidden');
        }
      });
    }
    
    // Show RFID scan result
    function showRfidScanResult(tracking) {
      // Update modal content with tracking data
      const modalContent = `
        <div class="p-6">
          <h3 class="text-lg font-semibold mb-4">RFID Scan Result</h3>
          <div class="space-y-3">
            <div><strong>File Number:</strong> ${tracking.file_indexing?.file_number || 'N/A'}</div>
            <div><strong>Current Location:</strong> ${tracking.current_location || 'N/A'}</div>
            <div><strong>Current Handler:</strong> ${tracking.current_handler || 'N/A'}</div>
            <div><strong>Status:</strong> ${getStatusBadge(tracking.status)}</div>
            <div><strong>RFID Tag:</strong> ${tracking.rfid_tag}</div>
            ${tracking.is_overdue ? '<div class="text-red-600 font-semibold">⚠️ This file is overdue!</div>' : ''}
          </div>
          <div class="mt-6 flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm" onclick="viewFileDetails(${tracking.id})">View Details</button>
            <button class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm" onclick="updateFileLocation(${tracking.id})">Update Location</button>
            <button class="border rounded-md px-4 py-2 text-sm" onclick="closeRfidModal()">Close</button>
          </div>
        </div>
      `;
      
      // Update the modal if it exists, otherwise show the original modal
      const modalElement = $('#rfid-modal .modal-content');
      if (modalElement.length) {
        modalElement.html(modalContent);
      }
      $('#rfid-modal').removeClass('hidden');
    }
    
    // Get status badge HTML
    function getStatusBadge(status) {
      const badges = {
        'active': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>',
        'checked_out': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Checked Out</span>',
        'overdue': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Overdue</span>',
        'returned': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Returned</span>',
        'lost': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Lost</span>',
        'archived': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Archived</span>'
      };
      return badges[status] || '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unknown</span>';
    }
    
    // View file details (enhanced with API)
    function viewFileDetails(trackingId) {
      $.ajax({
        url: `${API_BASE}/file-trackings/${trackingId}`,
        method: 'GET',
        success: function(response) {
          if (response.success) {
            updateFileDetailsFromApi(response.data);
          }
        },
        error: function(xhr) {
          console.error('Error loading file details:', xhr);
        }
      });
    }
    
    // Load file details from API
    function loadFileDetails(trackingId) {
      $.ajax({
        url: `${API_BASE}/file-trackings/${trackingId}`,
        method: 'GET',
        success: function(response) {
          if (response.success) {
            // Reload the page to show the selected file details
            // In a more sophisticated implementation, you would update the sidebar dynamically
            window.location.href = window.location.pathname + '?selected=' + trackingId;
          } else {
            showError('Failed to load file details');
          }
        },
        error: function(xhr) {
          console.error('Error loading file details:', xhr);
          showError('Error loading file details');
        }
      });
    }
    
    // Update file details from API data
    function updateFileDetailsFromApi(tracking) {
      updateFileDetails(tracking.id, tracking.file_indexing?.file_number, tracking.status);
      closeRfidModal();
    }
    
    // Show error message
    function showError(message) {
      console.error('Error:', message);
      // You can implement a better notification system here
    }
    
    // Show success message
    function showSuccess(message) {
      console.log('Success:', message);
      // You can implement a better notification system here
    }
    
    // Close RFID modal
    function closeRfidModal() {
      $('#rfid-modal').addClass('hidden');
    }
    
    // Update file location (enhanced with API)
    function updateFileLocation(trackingId) {
      const newLocation = prompt('Enter new location:');
      if (newLocation) {
        $.ajax({
          url: `${API_BASE}/file-trackings/${trackingId}`,
          method: 'PUT',
          data: JSON.stringify({
            current_location: newLocation,
            reason: 'Location updated via RFID scan'
          }),
          contentType: 'application/json',
          success: function(response) {
            if (response.success) {
              showSuccess('Location updated successfully');
              closeRfidModal();
            } else {
              showError('Failed to update location');
            }
          },
          error: function(xhr) {
            console.error('Error updating location:', xhr);
            showError('Error updating location');
          }
        });
      }
    }
    
    // Make functions globally available for onclick handlers
    window.viewFileDetails = viewFileDetails;
    window.updateFileLocation = updateFileLocation;
    window.closeRfidModal = closeRfidModal;
    window.scanRfidTag = scanRfidTag;
  });
</script>