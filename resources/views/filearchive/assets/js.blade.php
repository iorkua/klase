<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
  $(document).ready(function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Hide modals on page load
    $('.dialog-backdrop').hide();
    
    // File card click handler
    $('.file-card').on('click', function() {
      const fileId = $(this).data('id');
      openFileDetails(fileId);
    });
    
    // Modal close handlers
    $('.dialog-backdrop').on('click', function(e) {
      if ($(e.target).hasClass('dialog-backdrop')) {
        $(this).fadeOut('fast');
      }
    });
    
    $('#close-details, #close-viewer').on('click', function() {
      $(this).closest('.dialog-backdrop').fadeOut('fast');
    });
    
    // View document button
    $('#view-document').on('click', function() {
      $('#file-details-dialog').fadeOut('fast');
      setTimeout(function() {
        $('#document-viewer-dialog').fadeIn('fast');
      }, 300);
    });
    
    // Zoom and page navigation
    let zoomLevel = 100;
    let rotation = 0;
    
    $('#zoom-in').on('click', function() {
      zoomLevel = Math.min(zoomLevel + 25, 200);
      updateTransform();
    });
    
    $('#zoom-out').on('click', function() {
      zoomLevel = Math.max(zoomLevel - 25, 50);
      updateTransform();
    });
    
    $('#rotate').on('click', function() {
      rotation = (rotation + 90) % 360;
      updateTransform();
    });
    
    function updateTransform() {
      $('#zoom-level').text(zoomLevel + '%');
      $('#current-page-content').css('transform', 
        `scale(${zoomLevel / 100}) rotate(${rotation}deg)`);
    }
    
    // Page navigation
    $('#prev-page').on('click', function() {
      navigatePages('prev');
    });
    
    $('#next-page').on('click', function() {
      navigatePages('next');
    });
    
    // Document page items click
    $(document).on('click', '.page-item', function() {
      $('.page-item').removeClass('bg-blue-50 border-blue-300').addClass('bg-white');
      $(this).addClass('bg-blue-50 border-blue-300').removeClass('bg-white');
      
      const pageNumber = $(this).data('page');
      updatePageIndicator(pageNumber);
    });
    
    // Star toggle
    $('#toggle-star').on('click', function() {
      $(this).find('i').toggleClass('fill-yellow-400 text-yellow-400');
    });
    
    // Helper functions
    function openFileDetails(fileId) {
      // Set active file
      $('#file-details-dialog').fadeIn('fast');
    }
    
    function navigatePages(direction) {
      const currentPage = parseInt($('#page-indicator').text().split(' ')[1]);
      const totalPages = parseInt($('#page-indicator').text().split(' ')[3]);
      
      let newPage = currentPage;
      if (direction === 'prev' && currentPage > 1) {
        newPage = currentPage - 1;
      } else if (direction === 'next' && currentPage < totalPages) {
        newPage = currentPage + 1;
      }
      
      if (newPage !== currentPage) {
        updatePageIndicator(newPage);
        $(`.page-item[data-page="${newPage}"]`).click();
      }
    }
    
    function updatePageIndicator(page) {
      const totalPages = $('#document-pages-list .page-item').length;
      $('#page-indicator').text(`Page ${page} of ${totalPages}`);
    }
  });
</script>