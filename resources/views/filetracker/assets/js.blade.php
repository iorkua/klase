<script>
  $(document).ready(function() {
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
    
    // Tab functionality
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
    $('.file-row, .file-view-btn').click(function() {
      // Get the file ID from the row
      const fileId = $(this).closest('tr').find('td:first').text();
      const fileNumber = $(this).closest('tr').find('td:nth-child(2) span').text();
      const status = $(this).closest('tr').data('status');
      
      // Highlight the selected row
      $('.file-row').removeClass('bg-gray-50');
      $(this).closest('tr').addClass('bg-gray-50');
      
      // Update file details sidebar (this is a simplified simulation)
      updateFileDetails(fileId, fileNumber, status);
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
    
    // Print tracking sheet
    // $('.print-tracking-btn').click(function() {
    //   // Create a print window with file details
    //   const fileId = $('.file-details h2 + p').text();
    //   const fileNumber = $('.file-details .text-xs.font-medium:contains("RES")').text();
      
    //   const printWindow = window.open('', '_blank');
    //   printWindow.document.write(`
    //     <html>
    //       <head>
    //         <title>File Tracking Sheet - ${fileId}</title>
    //         <style>
    //           body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
    //           h1 { font-size: 18px; margin-bottom: 10px; }
    //           .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 10px; }
    //           .details { margin: 20px 0; }
    //           .info-row { display: flex; margin-bottom: 5px; }
    //           .info-label { width: 150px; color: #666; }
    //           .info-value { font-weight: bold; }
    //           .qr-img { border: 1px solid #ddd; padding: 10px; text-align: center; }
    //           table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    //           th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    //           th { background-color: #f2f2f2; }
    //         </style>
    //       </head>
    //       <body>
    //         <div class="header">
    //           <h1>File Tracking Sheet</h1>
    //           <div>ID: ${fileId}</div>
    //         </div>
    //         <div class="details">
    //           <div class="info-row">
    //             <div class="info-label">File Number:</div>
    //             <div class="info-value">${fileNumber}</div>
    //           </div>
    //           <div class="info-row">
    //             <div class="info-label">Current Location:</div>
    //             <div class="info-value">Customer Care Unit</div>
    //           </div>
    //           <div class="info-row">
    //             <div class="info-label">Current Handler:</div>
    //             <div class="info-value">Aisha Mohammed</div>
    //           </div>
    //           <div class="info-row">
    //             <div class="info-label">RFID Tag:</div>
    //             <div class="info-value">RFID-00125478</div>
    //           </div>
    //         </div>
    //         <table>
    //           <tr>
    //             <th>Date</th>
    //             <th>Location</th>
    //             <th>Handler</th>
    //             <th>Comment</th>
    //             <th>Signature</th>
    //           </tr>
    //           <tr>
    //             <td>2023-06-15</td>
    //             <td>Reception</td>
    //             <td>Fatima Usman</td>
    //             <td>File received and registered</td>
    //             <td></td>
    //           </tr>
    //           <tr>
    //             <td>2023-06-15</td>
    //             <td>Customer Care Unit</td>
    //             <td>Aisha Mohammed</td>
    //             <td>File assigned for processing</td>
    //             <td></td>
    //           </tr>
    //           <tr>
    //             <td></td>
    //             <td></td>
    //             <td></td>
    //             <td></td>
    //             <td></td>
    //           </tr>
    //           <tr>
    //             <td></td>
    //             <td></td>
    //             <td></td>
    //             <td></td>
    //             <td></td>
    //           </tr>
    //         </table>
    //       </body>
    //     </html>
    //   `);
    //   printWindow.document.close();
    //   setTimeout(() => {
    //     printWindow.print();
    //   }, 500);
    // });
    
    // Search functionality
    $('#search-input').on('keyup', function() {
      const value = $(this).val().toLowerCase();
      $('.file-row').filter(function() {
        const rowText = $(this).text().toLowerCase();
        $(this).toggle(rowText.indexOf(value) > -1);
      });
    });
  });
</script>