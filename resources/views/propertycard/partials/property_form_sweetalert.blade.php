<script>
// Direct form submission handler with SweetAlert
function submitPropertyForm() {
    const form = document.getElementById('property-record-form');
    const formData = new FormData(form);

    // Show loading
    Swal.fire({
        title: 'Submitting...',
        text: 'Please wait while we save your property record',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Submit form via fetch
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message || 'Property record created successfully',
                confirmButtonText: 'OK'
            }).then(() => {
                // Reset form and close dialog
                form.reset();
                
                // Close dialog if it exists
                const dialog = document.getElementById('property-form-dialog');
                if (dialog) {
                    dialog.classList.add('hidden');
                }
                
                // Reload page to show new record
                window.location.reload();
            });
        } else {
            // Handle validation errors
            let errorMessage = data.message || 'An error occurred';
            if (data.errors) {
                const errorList = Object.values(data.errors).flat();
                errorMessage = errorList.join('\n');
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An unexpected error occurred. Please try again.',
            confirmButtonText: 'OK'
        });
    });
}

// Override the Alpine.js form submission when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('property-record-form');
    const submitBtn = document.getElementById('property-submit-btn');
    
    if (form && submitBtn) {
        // Change button type to prevent default form submission
        submitBtn.type = 'button';
        
        // Remove any existing event listeners and add our custom handler
        submitBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            submitPropertyForm();
            return false;
        };
        
        // Also prevent form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            submitPropertyForm();
            return false;
        }, true);
    }
});
</script>