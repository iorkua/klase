<script>
    console.log("JS loaded successfully"); // Debug log

    // First let's clean up the repeated code by creating one DOMContentLoaded handler
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM Content Loaded");
        
        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                // Deactivate all tabs
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });

        // Close modal button
        const closeModalBtn = document.getElementById('closeModal');
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                // In a real application, this would close the modal
                alert('Modal closed');
            });
        }

        // Print Planning Recommendation using a new window
        const printBtn = document.getElementById('print-planning-recommendation');
        if (printBtn) {
            printBtn.addEventListener('click', function(e) {
                e.preventDefault();
                try {
                    console.log('Print button clicked'); // Debug log

                    // Create a new window with just the planning recommendation content
                    const printWindow = window.open('', '_blank', 'height=800,width=800');

                    // Get the direct URL to the planning recommendation with the print parameter
                    const applicationId = document.getElementById('application_id').value;
                    const printUrl =
                        `{{ url('sub-actions/planning-recommendation/print') }}/${applicationId}?url=print`;

                    // Navigate the new window to this URL
                    printWindow.location.href = printUrl;

                    // Set up listener for when content is loaded
                    printWindow.onload = function() {
                        setTimeout(function() {
                            printWindow.focus();
                            printWindow.print();
                        }, 1000); // Short delay to ensure content is fully loaded
                    };
                } catch (error) {
                    console.error('Error printing:', error);
                    alert('There was an error during printing. See console for details.');
                }
            });
        }

        // Toggle reason field based on decision and observations field
        const decisionRadios = document.querySelectorAll('input[name="decision"]');
        const reasonContainer = document.getElementById('reasonContainer');
        const observationsContainer = document.getElementById('observationsContainer');
        
        decisionRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (reasonContainer) {
                    reasonContainer.style.display = (this.value === 'Declined') ? 'block' : 'none';
                }
                if (observationsContainer) {
                    observationsContainer.style.display = (this.value === 'Approved') ? 'block' : 'none';
                }
            });
        });

        // FIX: Add event listener for saving observations - with improved debugging
        const saveObservationsBtn = document.getElementById('saveObservations');
        if (saveObservationsBtn) {
            console.log("Found save observations button", saveObservationsBtn);
            
            // IMPORTANT: Remove any existing listeners to avoid conflicts
            const newBtn = saveObservationsBtn.cloneNode(true);
            saveObservationsBtn.parentNode.replaceChild(newBtn, saveObservationsBtn);
            
            // Add the click handler to the new button
            newBtn.addEventListener('click', function() {
                console.log("Save Observations button clicked");
                
                const applicationId = document.getElementById('application_id').value;
                const additionalObservations = document.getElementById('additionalObservations').value;
                
                console.log("Application ID:", applicationId);
                console.log("Observations:", additionalObservations);
                
                if (!applicationId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Application ID not found'
                    });
                    return;
                }
                
                // Show loading indicator
                newBtn.disabled = true;
                newBtn.textContent = 'Saving...';
                
                // Define the CSRF token explicitly from meta tag
                const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const formToken = document.querySelector('input[name="_token"]')?.value;
                const csrfToken = formToken || metaToken || '{{ csrf_token() }}';
                
                console.log("Using CSRF token:", csrfToken ? "Present" : "Missing");
                
                // Direct fetch with debug logs
                fetch('{{ route("sub_pr_memos.save-observations") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        sub_application_id: applicationId,
                        additional_observations: additionalObservations
                    })
                })
                .then(response => {
                    console.log("Response status:", response.status);
                    // Don't throw on non-200, let the json parser try
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error("Error parsing JSON:", e);
                            console.log("Raw response:", text);
                            return { success: false, message: "Invalid server response" };
                        }
                    });
                })
                .then(data => {
                    console.log("Response data:", data);
                    newBtn.disabled = false;
                    newBtn.textContent = 'Save Observations';
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Additional observations saved successfully',
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to save observations'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error saving observations:', error);
                    newBtn.disabled = false;
                    newBtn.textContent = 'Save Observations';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving observations. See console for details.'
                    });
                });
            });
            
            // Also add direct onclick handler as backup
            newBtn.onclick = function() {
                console.log("Save Observations clicked via onclick");
            };
        } else {
            console.error('Save Observations button not found in the DOM');
        }
        
        // Direct onclick handlers for the decline reason buttons
        const saveDeclineReasonsBtn = document.getElementById('saveDeclineReasons');
        if (saveDeclineReasonsBtn) {
            console.log("Adding click handler to Save Reasons button");
            saveDeclineReasonsBtn.onclick = function() {
                console.log("Save Reasons button clicked");
                saveDeclineReasons(false);
            };
        }
        
        const saveAndViewBtn = document.getElementById('saveAndViewDeclineReasons');
        if (saveAndViewBtn) {
            console.log("Adding click handler to Save & View Memo button");
            saveAndViewBtn.onclick = function() {
                console.log("Save & View Memo button clicked");
                saveDeclineReasons(true);
            };
        }
    });

    // Separate the form handling function
    function handlePlanningRecommendation(e) {
        e.preventDefault();
        showPreloader();
        const submitBtn = document.getElementById('planningRecommendationSubmitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
        }
        const applicationId = document.getElementById('application_id').value;
        const decision = document.querySelector('input[name="decision"]:checked').value;
        const approvalDate = document.getElementById('approval-date').value;
        const comments = document.getElementById('comments')?.value || '';

        fetch('{{ url('sub-actions/planning-recommendation/update') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    application_id: applicationId,
                    status: decision,
                    approval_date: approvalDate,
                    comments: comments
                })
            })
            .then(response => response.json())
            .then(data => {
                hidePreloader();
                submitBtn.disabled = false;
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Planning recommendation updated successfully!',
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        // Redirect based on decision type to the sub-application specific memos
                        console.log("Decision was:", decision);
                        if (decision === 'Approved') {
                            // Redirect to sub-application approval memo
                            window.location.href = `{{ url('sub_pr_memos/approval') }}?id=${applicationId}`;
                        } else if (decision === 'Declined') {
                            // Redirect to sub-application declination memo
                            window.location.href = `{{ url('sub_pr_memos/declination') }}?id=${applicationId}`;
                        } else {
                            // Fallback - just reload the page
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Something went wrong!',
                        confirmButtonColor: '#EF4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                hidePreloader();
                submitBtn.disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating planning recommendation.',
                    confirmButtonColor: '#EF4444'
                });
            });
        return false;
    }

    function showPreloader() {
        Swal.fire({
            title: 'Processing...',
            html: 'Approval',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    function hidePreloader() {
        Swal.close();
    }
    
    // Add the missing toggleModal and toggleModalEnhanced functions
    function toggleModal(show) {
        const modal = document.getElementById('declineReasonModal');
        if (!modal) return console.error("Modal not found");
        
        if (show) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        } else {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }
    
    // Enhanced modal toggle that also refreshes inputs
    function toggleModalEnhanced(show) {
        toggleModal(show);
        if (show) {
            // Initialize details sections visibility based on checkbox state
            document.querySelectorAll('.decline-reason-check').forEach(check => {
                const detailsId = check.id.replace('Check', 'Details');
                const detailsSection = document.getElementById(detailsId);
                if (detailsSection) {
                    detailsSection.style.display = check.checked ? 'block' : 'none';
                }
            });
            
            // Make sure close buttons work
            const closeButtons = document.querySelectorAll('#closeDeclineModal, #cancelDeclineReasons');
            closeButtons.forEach(button => {
                button.onclick = function() {
                    toggleModal(false);
                };
            });
        }
    }
    
    // Add toggleDetails function for checkbox toggles
    function toggleDetails(checkbox, detailsId) {
        const detailsSection = document.getElementById(detailsId);
        if (detailsSection) {
            detailsSection.style.display = checkbox.checked ? 'block' : 'none';
        }
    }

    // Add the missing saveDeclineReasons function for sub-applications
    function saveDeclineReasons(viewMemo = false) {
        console.log("saveDeclineReasons called with viewMemo =", viewMemo); // Debug log
        
        // Gather all reason data
        const applicationId = document.getElementById('application_id').value;
        console.log("Sub-Application ID:", applicationId); // Debug log
        
        // Main reason flags
        const accessibilitySelected = document.getElementById('accessibilityCheck')?.checked ? 1 : 0;
        const landUseSelected = document.getElementById('conformityCheck')?.checked ? 1 : 0;
        const utilitySelected = document.getElementById('utilityCheck')?.checked ? 1 : 0;
        const roadReservationSelected = document.getElementById('roadReservationCheck')?.checked ? 1 : 0;
        
        // Check if at least one reason is selected
        if (!accessibilitySelected && !landUseSelected && !utilitySelected && !roadReservationSelected) {
            Swal.fire({
                icon: 'warning',
                title: 'No Reasons Selected',
                text: 'Please select at least one reason for declining this application.',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        // Get form fields - use optional chaining to prevent errors
        const accessibilitySpecificDetails = document.getElementById('accessibilitySpecificDetails')?.value || '';
        const accessibilityObstructions = document.getElementById('accessibilityObstructions')?.value || '';
        
        const landUseDetails = document.getElementById('landUseDetails')?.value || '';
        const landUseDeviations = document.getElementById('landUseDeviations')?.value || '';
        
        const utilityIssueDetails = document.getElementById('utilityIssueDetails')?.value || '';
        const utilityTypeDetails = document.getElementById('utilityTypeDetails')?.value || '';
        
        const roadReservationIssues = document.getElementById('roadReservationIssues')?.value || '';
        const roadMeasurements = document.getElementById('roadMeasurements')?.value || '';
        
        // Generate the formatted reason text
        let reasonText = "In view of the following deficiencies, the application for Sectional Titling cannot be recommended for approval at this time:\n\n";
        let reasonCount = 1;
        
        if (accessibilitySelected) {
            reasonText += reasonCount + ". Accessibility Issues\n";
            reasonText += "• Condition: The property/site must have adequate accessibility to ensure ease of movement and compliance with urban planning standards.\n";
            reasonText += "• Findings:\n";
            
            if (accessibilitySpecificDetails) {
                reasonText += "  • " + accessibilitySpecificDetails + "\n";
            }
            
            if (accessibilityObstructions) {
                reasonText += "  • Obstructions/barriers: " + accessibilityObstructions + "\n";
            }
            
            if (!accessibilitySpecificDetails && !accessibilityObstructions) {
                reasonText += "  • The property lacks adequate accessibility features required by planning standards.\n";
            }
            
            reasonText += "• Conclusion: The property/site does not satisfy the accessibility requirement.\n\n";
            reasonCount++;
        }
        
        if (landUseSelected) {
            // ...similar pattern for land use...
            reasonText += reasonCount + ". Land Use Conformity Issues\n";
            reasonText += "• Condition: The property/site must conform to the existing land use designation of the area as per the Kano State Physical Development Plan.\n";
            reasonText += "• Findings:\n";
            
            if (landUseDetails) {
                reasonText += "  • " + landUseDetails + "\n";
            }
            
            if (landUseDeviations) {
                reasonText += "  • " + landUseDeviations + "\n";
            }
            
            if (!landUseDetails && !landUseDeviations) {
                reasonText += "  • The property does not conform to the approved land use plan for the area.\n";
            }
            
            reasonText += "• Conclusion: The property/site does not conform to the existing land use regulations.\n\n";
            reasonCount++;
        }
        
        if (utilitySelected) {
            // ...similar pattern for utility...
            reasonText += reasonCount + ". Utility Line Interference\n";
            reasonText += "• Condition: The property/site must not transverse or interfere with existing utility lines (e.g., electricity, water, sewage).\n";
            reasonText += "• Findings:\n";
            
            if (utilityIssueDetails) {
                reasonText += "  • " + utilityIssueDetails + "\n";
            }
            
            if (utilityTypeDetails) {
                reasonText += "  • " + utilityTypeDetails + "\n";
            }
            
            if (!utilityIssueDetails && !utilityTypeDetails) {
                reasonText += "  • The property interferes with existing utility infrastructure in the area.\n";
            }
            
            reasonText += "• Conclusion: The property/site violates the no-transverse utility line condition.\n\n";
            reasonCount++;
        }
        
        if (roadReservationSelected) {
            // ...similar pattern for road reservation...
            reasonText += reasonCount + ". Road Reservation Issues\n";
            reasonText += "• Condition: The property/site must have an adequate access road or comply with minimum road reservation standards as stipulated in KNUPDA guidelines.\n";
            reasonText += "• Findings:\n";
            
            if (roadReservationIssues) {
                reasonText += "  • " + roadReservationIssues + "\n";
            }
            
            if (roadMeasurements) {
                reasonText += "  • Measurements: " + roadMeasurements + "\n";
            }
            
            if (!roadReservationIssues && !roadMeasurements) {
                reasonText += "  • The property lacks an adequate access road network as required by planning standards.\n";
            }
            
            reasonText += "• Conclusion: The property/site does not meet the requirements for adequate access road/road reservation.\n\n";
        }
        
        reasonText += "We advise the applicant to address the identified issues and resubmit the application for reconsideration.";
        
        // Set the formatted text to the hidden field
        const commentsField = document.getElementById('comments');
        if (commentsField) {
            commentsField.value = reasonText;
        }
        
        // Update the button text to show reasons are provided
        const openDeclineReasonModal = document.getElementById('openDeclineReasonModal');
        if (openDeclineReasonModal) {
            openDeclineReasonModal.textContent = 'Decline reasons provided ✓';
            openDeclineReasonModal.classList.add('bg-red-50');
        }
        
        // Show preloader
        showPreloader();
        console.log("Sending data to server..."); // Debug log
        
        // Get approval date - with fallback to today's date
        const approvalDate = document.getElementById('approval-date')?.value || new Date().toISOString().split('T')[0];
        
        // Prepare the data for sending
        const postData = {
            sub_application_id: applicationId, // Use sub_application_id for sub-applications
            submitted_by: {{ auth()->id() ?? 1 }},
            approval_date: approvalDate,
            
            // Main reason flags
            accessibility_selected: accessibilitySelected,
            land_use_selected: landUseSelected,
            utility_selected: utilitySelected,
            road_reservation_selected: roadReservationSelected,
            
            // Simplified fields
            access_road_details: accessibilitySpecificDetails,
            pedestrian_details: accessibilityObstructions,
            zoning_details: landUseDetails,
            density_details: landUseDeviations,
            overhead_details: utilityIssueDetails,
            underground_details: utilityTypeDetails,
            right_of_way_details: roadReservationIssues,
            road_width_details: roadMeasurements,
            
            // Complete reason summary
            reason_summary: reasonText
        };
        
        console.log("Posting data:", postData); // Debug log
        
        // Save to database - use sub-specific route
        fetch('{{ route("sub_pr_memos.declination.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(postData)
        })
        .then(response => {
            console.log("Response status:", response.status); // Debug log
            return response.json();
        })
        .then(data => {
            console.log("Response data:", data); // Debug log
            hidePreloader();
            
            if (data.success) {
                // Hide the modal
                const declineReasonModal = document.getElementById('declineReasonModal');
                if (declineReasonModal) {
                    declineReasonModal.classList.add('hidden');
                    declineReasonModal.style.display = 'none';
                }
                
                // Handle redirect based on viewMemo parameter
                if (viewMemo) {
                    console.log("Redirecting to declination memo..."); // Debug log
                    // Redirect immediately to the declination memo for sub-application
                    window.location.href = `{{ url('sub_pr_memos/declination') }}?id=${applicationId}`;
                } else {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Decline reasons saved successfully!',
                        confirmButtonColor: '#10B981'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to save decline reasons.',
                    confirmButtonColor: '#EF4444'
                });
            }
        })
        .catch(error => {
            console.error('Error saving decline reasons:', error); // Debug log
            hidePreloader();
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while saving decline reasons.',
                confirmButtonColor: '#EF4444'
            });
        });
    }

    // Add direct event handlers when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Direct onclick handlers for the decline reason buttons
        const saveDeclineReasonsBtn = document.getElementById('saveDeclineReasons');
        if (saveDeclineReasonsBtn) {
            console.log("Adding click handler to Save Reasons button");
            saveDeclineReasonsBtn.onclick = function() {
                console.log("Save Reasons button clicked");
                saveDeclineReasons(false);
            };
        }
        
        const saveAndViewBtn = document.getElementById('saveAndViewDeclineReasons');
        if (saveAndViewBtn) {
            console.log("Adding click handler to Save & View Memo button");
            saveAndViewBtn.onclick = function() {
                console.log("Save & View Memo button clicked");
                saveDeclineReasons(true);
            };
        }
    });

    // Create global saveObservations function for direct onclick usage
    function saveObservations() {
        console.log("saveObservations global function called");
        const applicationId = document.getElementById('application_id').value;
        const additionalObservations = document.getElementById('additionalObservations').value;
        const saveObservationsBtn = document.getElementById('saveObservations');
        
        if (!applicationId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Application ID not found'
            });
            return;
        }
        
        // Show loading indicator
        if (saveObservationsBtn) {
            saveObservationsBtn.disabled = true;
            saveObservationsBtn.textContent = 'Saving...';
        }
        
        // Define the CSRF token explicitly
        const csrfToken = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';
        
        fetch('{{ route("sub_pr_memos.save-observations") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sub_application_id: applicationId,
                additional_observations: additionalObservations
            })
        })
        .then(response => response.json())
        .then(data => {
            if (saveObservationsBtn) {
                saveObservationsBtn.disabled = false;
                saveObservationsBtn.textContent = 'Save Observations';
            }
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Additional observations saved successfully',
                    timer: 1500
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to save observations'
                });
            }
        })
        .catch(error => {
            console.error('Error saving observations:', error);
            if (saveObservationsBtn) {
                saveObservationsBtn.disabled = false;
                saveObservationsBtn.textContent = 'Save Observations';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while saving observations'
            });
        });
    }
</script>
