<script>
        console.log("JS loaded successfully"); // Debug log

        document.addEventListener('DOMContentLoaded', function() {
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
            document.getElementById('closeModal').addEventListener('click', function() {
                // In a real application, this would close the modal
                alert('Modal closed');
            });

            // Print Planning Recommendation using a new window
            document.getElementById('print-planning-recommendation').addEventListener('click', function(e) {
                e.preventDefault();
                try {
                    console.log('Print button clicked'); // Debug log

                    // Create a new window with just the planning recommendation content
                    const printWindow = window.open('', '_blank', 'height=800,width=800');

                    // Get the direct URL to the planning recommendation with the print parameter
                    const applicationId = document.getElementById('application_id').value;
                    const printUrl =
                        `{{ url('planning-recommendation/print') }}/${applicationId}?url=print`;

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

            // Toggle reason field based on decision
            // const decisionRadios = document.querySelectorAll('input[name="decision"]');
            // const reasonContainer = document.getElementById('reasonContainer');
            // decisionRadios.forEach(radio => {
            //     radio.addEventListener('change', function() {
            //         reasonContainer.style.display = (this.value === 'decline') ? 'block' : 'none';
            //     });
            // });

            // DEBUG: Check if modal elements exist
            console.log("Modal button exists:", !!document.getElementById('openDeclineReasonModal'));
            console.log("Modal container exists:", !!document.getElementById('declineReasonModal'));

            // Ensure the modal functionality is properly initialized
            const initializeDeclineModal = function() {
                const declineReasonModal = document.getElementById('declineReasonModal');
                const openDeclineReasonModal = document.getElementById('openDeclineReasonModal');
                const closeDeclineModal = document.getElementById('closeDeclineModal');
                const cancelDeclineReasons = document.getElementById('cancelDeclineReasons');
                const saveDeclineReasons = document.getElementById('saveDeclineReasons');
                const commentsField = document.getElementById('comments');
                
                if (!openDeclineReasonModal || !declineReasonModal) {
                    console.error("Modal elements not found:", {
                        button: openDeclineReasonModal,
                        modal: declineReasonModal
                    });
                    return;
                }
                
                // Show modal when button is clicked - add direct click handler
                openDeclineReasonModal.onclick = function() {
                    console.log("Opening modal");
                    declineReasonModal.classList.remove('hidden');
                    declineReasonModal.style.display = 'flex';
                };
                
                // Close modal functions
                if (closeDeclineModal) {
                    closeDeclineModal.onclick = function() {
                        console.log("Closing modal");
                        declineReasonModal.classList.add('hidden');
                        declineReasonModal.style.display = 'none';
                    };
                }
                
                if (cancelDeclineReasons) {
                    cancelDeclineReasons.onclick = function() {
                        console.log("Canceling reasons");
                        declineReasonModal.classList.add('hidden');
                        declineReasonModal.style.display = 'none';
                    };
                }
                
                // Handle checkbox toggles for showing/hiding details sections
                const reasonChecks = document.querySelectorAll('.decline-reason-check');
                reasonChecks.forEach(check => {
                    check.addEventListener('change', function() {
                        const detailsId = this.id.replace('Check', 'Details');
                        const detailsSection = document.getElementById(detailsId);
                        if (detailsSection) {
                            detailsSection.style.display = this.checked ? 'block' : 'none';
                        }
                    });
                });

                // Handle sub-reason checkboxes
                const subReasonChecks = document.querySelectorAll('.sub-reason-check');
                subReasonChecks.forEach(check => {
                    check.addEventListener('change', function() {
                        const inputsId = this.id.replace('Check', 'Inputs');
                        const inputsSection = document.getElementById(inputsId);
                        if (inputsSection) {
                            inputsSection.style.display = this.checked ? 'block' : 'none';
                        }
                    });
                });
                
                // Save decline reasons
                if (saveDeclineReasons) {
                    saveDeclineReasons.addEventListener('click', function() {
                        let reasonText = "In view of the following deficiencies, the application cannot be recommended for approval at this time:\n\n";
                        let hasReasons = false;
                        let reasonCount = 1;
                        
                        // 1. Accessibility
                        if (document.getElementById('accessibilityCheck').checked) {
                            hasReasons = true;
                            reasonText += reasonCount + ". Accessibility Issues\n";
                            reasonText += "• Condition: The property/site must have adequate accessibility to ensure ease of movement and compliance with urban planning standards.\n";
                            reasonText += "• Findings:\n";
                            
                            // Check for sub-reasons
                            if (document.getElementById('accessRoadCheck').checked) {
                                const location = document.getElementById('accessRoadLocation').value;
                                const type = document.getElementById('accessRoadType').value;
                                const measurement = document.getElementById('accessRoadMeasurement').value;
                                const details = document.getElementById('accessRoadDetails').value;
                                
                                reasonText += "  - Road Access Issues: " + (location ? location + ". " : "") + 
                                             (type ? "Issue type: " + type + ". " : "") + 
                                             (measurement ? "Measurements: " + measurement + ". " : "") + 
                                             (details ? details : "") + "\n";
                            }
                            
                            if (document.getElementById('pedestrianCheck').checked) {
                                const location = document.getElementById('pedestrianLocation').value;
                                const type = document.getElementById('pedestrianIssueType').value;
                                const measurement = document.getElementById('pedestrianMeasurement').value;
                                const details = document.getElementById('pedestrianDetails').value;
                                
                                reasonText += "  - Pedestrian Movement Issues: " + (location ? location + ". " : "") + 
                                             (type ? "Issue type: " + type + ". " : "") + 
                                             (measurement ? "Measurements: " + measurement + ". " : "") + 
                                             (details ? details : "") + "\n";
                            }
                            
                            if (!document.getElementById('accessRoadCheck').checked && 
                                !document.getElementById('pedestrianCheck').checked) {
                                reasonText += "  - Inadequate accessibility.\n";
                            }
                            
                            reasonText += "• Conclusion: The property/site does not satisfy the accessibility requirement.\n\n";
                            reasonCount++;
                        }
                        
                        // 2. Land Use Conformity
                        if (document.getElementById('conformityCheck').checked) {
                            hasReasons = true;
                            reasonText += reasonCount + ". Land Use Conformity Issues\n";
                            reasonText += "• Condition: The property/site must conform to the existing land use designation of the area as per the Kano State Physical Development Plan.\n";
                            reasonText += "• Findings:\n";
                            
                            if (document.getElementById('zoningCheck').checked) {
                                const current = document.getElementById('currentZoning').value;
                                const proposed = document.getElementById('proposedUse').value;
                                const details = document.getElementById('zoningDetails').value;
                                
                                reasonText += "  - Zoning Violation: " + 
                                             (current ? "Current zoning: " + current + ". " : "") + 
                                             (proposed ? "Proposed use: " + proposed + ". " : "") + 
                                             (details ? details : "") + "\n";
                            }
                            
                            if (document.getElementById('densityCheck').checked) {
                                const allowed = document.getElementById('allowedDensity').value;
                                const proposed = document.getElementById('proposedDensity').value;
                                const details = document.getElementById('densityDetails').value;
                                
                                reasonText += "  - Density/Intensity Violation: " + 
                                             (allowed ? "Allowed density: " + allowed + ". " : "") + 
                                             (proposed ? "Proposed density: " + proposed + ". " : "") + 
                                             (details ? details : "") + "\n";
                            }
                            
                            if (!document.getElementById('zoningCheck').checked && 
                                !document.getElementById('densityCheck').checked) {
                                reasonText += "  - Non-conformity with existing land use regulations.\n";
                            }
                            
                            reasonText += "• Conclusion: The property/site does not conform to the existing land use regulations.\n\n";
                            reasonCount++;
                        }
                        
                        // 3. Utility Line Interference
                        if (document.getElementById('utilityCheck').checked) {
                            hasReasons = true;
                            reasonText += reasonCount + ". Utility Line Interference\n";
                            reasonText += "• Condition: The property/site must not transverse or interfere with existing utility lines (e.g., electricity, water, sewage).\n";
                            reasonText += "• Findings:\n";
                            
                            if (document.getElementById('overheadCheck').checked) {
                                const type = document.getElementById('overheadUtilityType').value;
                                const distance = document.getElementById('overheadDistance').value;
                                const details = document.getElementById('overheadDetails').value;
                                
                                reasonText += "  - Overhead Utility Interference: " + 
                                             (type ? "Utility type: " + type + ". " : "") + 
                                             (distance ? "Distance: " + distance + "m. " : "") + 
                                             (details ? details : "") + "\n";
                            }
                            
                            if (document.getElementById('undergroundCheck').checked) {
                                const type = document.getElementById('undergroundUtilityType').value;
                                const depth = document.getElementById('undergroundDepth').value;
                                const details = document.getElementById('undergroundDetails').value;
                                
                                reasonText += "  - Underground Utility Interference: " + 
                                             (type ? "Utility type: " + type + ". " : "") + 
                                             (depth ? "Depth: " + depth + "m. " : "") + 
                                             (details ? details : "") + "\n";
                            }
                            
                            if (!document.getElementById('overheadCheck').checked && 
                                !document.getElementById('undergroundCheck').checked) {
                                reasonText += "  - Interference with utility lines detected.\n";
                            }
                            
                            reasonText += "• Conclusion: The property/site violates the no-transverse utility line condition.\n\n";
                            reasonCount++;
                        }
                        
                        // 4. Road Reservation Issues
                        if (document.getElementById('roadReservationCheck').checked) {
                            hasReasons = true;
                            reasonText += reasonCount + ". Road Reservation Issues\n";
                            reasonText += "• Condition: The property/site must have an adequate access road or comply with minimum road reservation standards as stipulated in KNUPDA guidelines.\n";
                            reasonText += "• Findings:\n";
                            
                            if (document.getElementById('rightOfWayCheck').checked) {
                                const required = document.getElementById('requiredSetback').value;
                                const actual = document.getElementById('actualSetback').value;
                                const details = document.getElementById('rightOfWayDetails').value;
                                
                                reasonText += "  - Right-of-Way Encroachment: " + 
                                             (required ? "Required setback: " + required + "m. " : "") + 
                                             (actual ? "Actual/proposed setback: " + actual + "m. " : "") + 
                                             (details ? details : "") + "\n";
                            }
                            
                            if (document.getElementById('roadWidthCheck').checked) {
                                const required = document.getElementById('requiredWidth').value;
                                const actual = document.getElementById('actualWidth').value;
                                const details = document.getElementById('roadWidthDetails').value;
                                
                                reasonText += "  - Inadequate Road Width: " + 
                                             (required ? "Required width: " + required + "m. " : "") + 
                                             (actual ? "Actual/available width: " + actual + "m. " : "") + 
                                             (details ? details : "") + "\n";
                            }
                            
                            if (!document.getElementById('rightOfWayCheck').checked && 
                                !document.getElementById('roadWidthCheck').checked) {
                                reasonText += "  - Inadequate access road/road reservation.\n";
                            }
                            
                            reasonText += "• Conclusion: The property/site does not meet the requirements for adequate access road/road reservation.\n\n";
                        }
                        
                        reasonText += "We advise the applicant to address the identified issues and resubmit the application for reconsideration.";
                        
                        if (!hasReasons) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Reasons Selected',
                                text: 'Please select at least one reason for declining this application.',
                                confirmButtonColor: '#10B981'
                            });
                            return;
                        }
                        
                        // Set the formatted text to the hidden field
                        commentsField.value = reasonText;
                        
                        // Update the button text to show reasons are provided
                        openDeclineReasonModal.textContent = 'Decline reasons provided ✓';
                        openDeclineReasonModal.classList.add('bg-red-50');
                        
                        // Hide the modal
                        declineReasonModal.classList.add('hidden');
                        declineReasonModal.style.display = 'none';
                    });
                }
            };

            // Initialize modal functionality
            initializeDeclineModal();

            // Toggle reason field based on decision
            const decisionRadios = document.querySelectorAll('input[name="decision"]');
            const reasonContainer = document.getElementById('reasonContainer');
            decisionRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    reasonContainer.style.display = (this.value === 'decline') ? 'block' : 'none';
                });
            });
        });

        // Ensure buttons work outside DOMContentLoaded too
        window.toggleModal = function(show) {
            const modal = document.getElementById('declineReasonModal');
            if (!modal) return console.error("Modal not found");
            
            if (show) {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            } else {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }
        };
        
        // Toggle sub-reason inputs visibility - improved version with !important flag
        window.toggleSubReason = function(checkbox, inputsId) {
            console.log("toggleSubReason called for", inputsId);
            const inputsSection = document.getElementById(inputsId);
            if (inputsSection) {
                // Use !important to override any conflicting styles
                inputsSection.style.cssText = checkbox.checked ? 'display: block !important' : 'display: none !important';
                console.log("Set display to", inputsSection.style.display, "for", inputsId);
                
                // Double-check after a short delay to ensure it took effect
                setTimeout(() => {
                    if ((checkbox.checked && inputsSection.style.display !== 'block') || 
                        (!checkbox.checked && inputsSection.style.display !== 'none')) {
                        console.warn("Display state didn't match checkbox state, forcing update");
                        inputsSection.style.cssText = checkbox.checked ? 'display: block !important' : 'display: none !important';
                    }
                }, 50);
            } else {
                console.error("Could not find inputs section:", inputsId);
                
                // Try to find by alternative ID patterns
                const alternativeId = inputsId.replace('Inputs', '-inputs');
                const altSection = document.getElementById(alternativeId);
                if (altSection) {
                    console.log("Found alternative section:", alternativeId);
                    altSection.style.cssText = checkbox.checked ? 'display: block !important' : 'display: none !important';
                }
            }
        };

        // Direct check for all sub-reason inputs on modal open
        window.refreshSubReasonInputs = function() {
            document.querySelectorAll('.sub-reason-check').forEach(check => {
                const inputsId = check.id.replace('Check', 'Inputs');
                const inputsSection = document.getElementById(inputsId);
                if (inputsSection) {
                    inputsSection.style.display = check.checked ? 'block' : 'none';
                }
            });
        };

        // Enhanced modal toggle that also refreshes inputs
        window.toggleModalEnhanced = function(show) {
            toggleModal(show);
            if (show) {
                // Initialize all sub-reason visibility states when the modal opens
                setTimeout(initializeAllSubReasonVisibility, 100);
                
                // Add event listeners to all checkboxes in the modal
                document.querySelectorAll('.sub-reason-check').forEach(check => {
                    // Add direct onclick handler for immediate effect
                    check.onclick = function() {
                        const inputsId = this.id.replace('Check', 'Inputs');
                        const inputsSection = document.getElementById(inputsId);
                        if (inputsSection) {
                            // Apply style directly with !important for maximum reliability
                            inputsSection.style.cssText = this.checked ? 'display: block !important' : 'display: none !important';
                        }
                    };
                });
                
                // Force check current states after a slight delay
                setTimeout(function() {
                    document.querySelectorAll('.sub-reason-check').forEach(check => {
                        if (check.checked) {
                            const inputsId = check.id.replace('Check', 'Inputs');
                            const inputsSection = document.getElementById(inputsId);
                            if (inputsSection) {
                                inputsSection.style.cssText = 'display: block !important';
                            }
                        }
                    });
                }, 300);
            }
        };

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

            fetch('{{ url('planning-recommendation/update') }}', {
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
                            // Redirect based on decision
                            if (decision === 'approve') {
                                // Redirect to approval memo
                                window.location.href = `{{ url('pr_memos/approval') }}?id=${applicationId}`;
                            } else if (decision === 'decline') {
                                // Redirect to declination memo
                                window.location.href = `{{ url('pr_memos/declination') }}?id=${applicationId}`;
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

        // Save decline reasons to database
        function saveDeclineReasons(viewMemo = false) {
            // Gather all reason data
            const applicationId = document.getElementById('application_id').value;
            
            // Main reason flags
            const accessibilitySelected = document.getElementById('accessibilityCheck').checked ? 1 : 0;
            const landUseSelected = document.getElementById('conformityCheck').checked ? 1 : 0;
            const utilitySelected = document.getElementById('utilityCheck').checked ? 1 : 0;
            const roadReservationSelected = document.getElementById('roadReservationCheck').checked ? 1 : 0;
            
            // Simplified form values
            const accessibilitySpecificDetails = document.getElementById('accessibilitySpecificDetails')?.value || '';
            const accessibilityObstructions = document.getElementById('accessibilityObstructions')?.value || '';
            
            const landUseDetails = document.getElementById('landUseDetails')?.value || '';
            const landUseDeviations = document.getElementById('landUseDeviations')?.value || '';
            
            const utilityIssueDetails = document.getElementById('utilityIssueDetails')?.value || '';
            const utilityTypeDetails = document.getElementById('utilityTypeDetails')?.value || '';
            
            const roadReservationIssues = document.getElementById('roadReservationIssues')?.value || '';
            const roadMeasurements = document.getElementById('roadMeasurements')?.value || '';
            
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
            
            // Generate the formatted reason text
            let reasonText = "In view of the following deficiencies, the application cannot be recommended for approval at this time:\n\n";
            let reasonCount = 1;
            
            if (accessibilitySelected) {
                reasonText += reasonCount + ". Accessibility Issues\n";
                reasonText += "• Condition: The property/site must have adequate accessibility to ensure ease of movement and compliance with urban planning standards.\n";
                reasonText += "• Findings:\n";
                
                if (accessibilitySpecificDetails) {
                    reasonText += "  - " + accessibilitySpecificDetails + "\n";
                }
                
                if (accessibilityObstructions) {
                    reasonText += "  - Obstructions/barriers: " + accessibilityObstructions + "\n";
                }
                
                reasonText += "• Conclusion: The property/site does not satisfy the accessibility requirement.\n\n";
                reasonCount++;
            }
            
            if (landUseSelected) {
                reasonText += reasonCount + ". Land Use Conformity Issues\n";
                reasonText += "• Condition: The property/site must conform to the existing land use designation of the area as per the Kano State Physical Development Plan.\n";
                reasonText += "• Findings:\n";
                
                if (landUseDetails) {
                    reasonText += "  - " + landUseDetails + "\n";
                }
                
                if (landUseDeviations) {
                    reasonText += "  - " + landUseDeviations + "\n";
                }
                
                reasonText += "• Conclusion: The property/site does not conform to the existing land use regulations.\n\n";
                reasonCount++;
            }
            
            if (utilitySelected) {
                reasonText += reasonCount + ". Utility Line Interference\n";
                reasonText += "• Condition: The property/site must not transverse or interfere with existing utility lines (e.g., electricity, water, sewage).\n";
                reasonText += "• Findings:\n";
                
                if (utilityIssueDetails) {
                    reasonText += "  - " + utilityIssueDetails + "\n";
                }
                
                if (utilityTypeDetails) {
                    reasonText += "  - " + utilityTypeDetails + "\n";
                }
                
                reasonText += "• Conclusion: The property/site violates the no-transverse utility line condition.\n\n";
                reasonCount++;
            }
            
            if (roadReservationSelected) {
                reasonText += reasonCount + ". Road Reservation Issues\n";
                reasonText += "• Condition: The property/site must have an adequate access road or comply with minimum road reservation standards as stipulated in KNUPDA guidelines.\n";
                reasonText += "• Findings:\n";
                
                if (roadReservationIssues) {
                    reasonText += "  - " + roadReservationIssues + "\n";
                }
                
                if (roadMeasurements) {
                    reasonText += "  - Measurements: " + roadMeasurements + "\n";
                }
                
                reasonText += "• Conclusion: The property/site does not meet the requirements for adequate access road/road reservation.\n\n";
            }
            
            reasonText += "We advise the applicant to address the identified issues and resubmit the application for reconsideration.";
            
            // Set the formatted text to the hidden field
            document.getElementById('comments').value = reasonText;
            
            // Show preloader
            showPreloader();
            
            // Save to database with simplified fields
            fetch('{{ route("pr_memos.declination.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    application_id: applicationId,
                    submitted_by: {{ auth()->user()->id ?? 1 }},
                    approval_date: document.getElementById('approval-date').value,
                    
                    // Main reason flags
                    accessibility_selected: accessibilitySelected,
                    land_use_selected: landUseSelected,
                    utility_selected: utilitySelected,
                    road_reservation_selected: roadReservationSelected,
                    
                    // Simplified fields - store in existing database columns for compatibility
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
                })
            })
            .then(response => response.json())
            .then(data => {
                hidePreloader();
                
                if (data.success) {
                    // Update UI to show success
                    const openDeclineReasonModal = document.getElementById('openDeclineReasonModal');
                    if (openDeclineReasonModal) {
                        openDeclineReasonModal.textContent = 'Decline reasons provided ✓';
                        openDeclineReasonModal.classList.add('bg-red-50');
                    }
                    
                    // Hide the modal
                    const declineReasonModal = document.getElementById('declineReasonModal');
                    if (declineReasonModal) {
                        declineReasonModal.classList.add('hidden');
                        declineReasonModal.style.display = 'none';
                    }
                    
                    // Show success message and redirect
                    if (viewMemo) {
                        // Redirect immediately to the declination memo
                        window.location.href = `{{ url('pr_memos/declination') }}?id=${applicationId}`;
                    } else {
                        // Show success message first, then redirect when confirmed
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Decline reasons saved successfully! View the declination memo?',
                            showCancelButton: true,
                            confirmButtonColor: '#10B981',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'View Memo',
                            cancelButtonText: 'Stay Here'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to declination memo if confirmed
                                window.location.href = `{{ url('pr_memos/declination') }}?id=${applicationId}`;
                            }
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
                console.error('Error saving decline reasons:', error);
                hidePreloader();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while saving decline reasons.',
                    confirmButtonColor: '#EF4444'
                });
            });
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

        // Run this function when page loads to initialize everything properly
        window.initializeAllSubReasonVisibility = function() {
            console.log("Initializing all sub-reason visibility states");
            // First ensure all main reason details are correctly displayed
            document.querySelectorAll('.decline-reason-check').forEach(check => {
                const detailsId = check.id.replace('Check', 'Details');
                const detailsSection = document.getElementById(detailsId);
                if (detailsSection) {
                    detailsSection.style.cssText = check.checked ? 'display: block !important' : 'display: none !important';
                }
            });
            
            // Then ensure all sub-reason inputs are correctly displayed
            document.querySelectorAll('.sub-reason-check').forEach(check => {
                const inputsId = check.id.replace('Check', 'Inputs');
                const inputsSection = document.getElementById(inputsId);
                if (inputsSection) {
                    inputsSection.style.cssText = check.checked ? 'display: block !important' : 'display: none !important';
                }
            });
        };

        // Enhanced modal toggle that also refreshes inputs
        window.toggleModalEnhanced = function(show) {
            toggleModal(show);
            if (show) {
                // Initialize all sub-reason visibility states when the modal opens
                setTimeout(initializeAllSubReasonVisibility, 100);
                
                // Add event listeners to all checkboxes in the modal
                document.querySelectorAll('.sub-reason-check').forEach(check => {
                    // Remove any existing listeners to avoid duplicates
                    const newCheck = check.cloneNode(true);
                    check.parentNode.replaceChild(newCheck, check);
                    
                    // Add the event listener
                    newCheck.addEventListener('change', function() {
                        const inputsId = this.id.replace('Check', 'Inputs');
                        toggleSubReason(this, inputsId);
                    });
                    
                    // Also add onclick for redundancy
                    newCheck.onclick = function() {
                        const inputsId = this.id.replace('Check', 'Inputs');
                        toggleSubReason(this, inputsId);
                    };
                });
            }
        };

        // Add an automatic initialization call
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sub-reason visibility when page loads
            setTimeout(initializeAllSubReasonVisibility, 500);
        });

        // Add event listeners for the new buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Save button - Save decline reasons
            const saveDeclineReasonsBtn = document.getElementById('saveDeclineReasons');
            if (saveDeclineReasonsBtn) {
                saveDeclineReasonsBtn.addEventListener('click', function() {
                    saveDeclineReasons(false);
                });
            }
            
            // Save & View button - Save decline reasons and view memo
            const saveAndViewBtn = document.getElementById('saveAndViewDeclineReasons');
            if (saveAndViewBtn) {
                saveAndViewBtn.addEventListener('click', function() {
                    saveDeclineReasons(true);
                });
            }
        });

        // Simple direct toggle function that works without relying on complex event handlers
        window.toggleDetails = function(checkbox, detailsId) {
            const detailsSection = document.getElementById(detailsId);
            if (detailsSection) {
                detailsSection.style.display = checkbox.checked ? 'block' : 'none';
            }
        };

        // Add event listener for saving observations
        document.addEventListener('DOMContentLoaded', function() {
            const saveObservationsBtn = document.getElementById('saveObservations');
            if (saveObservationsBtn) {
                saveObservationsBtn.addEventListener('click', function() {
                    const applicationId = document.getElementById('application_id').value;
                    const additionalObservations = document.getElementById('additionalObservations').value;
                    
                    if (!applicationId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Application ID not found'
                        });
                        return;
                    }
                    
                    // Show loading indicator
                    saveObservationsBtn.disabled = true;
                    saveObservationsBtn.textContent = 'Saving...';
                    
                    // Save the observations
                    fetch('{{ route("pr_memos.save-observations") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            application_id: applicationId,
                            additional_observations: additionalObservations
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        saveObservationsBtn.disabled = false;
                        saveObservationsBtn.textContent = 'Save Observations';
                        
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
                        saveObservationsBtn.disabled = false;
                        saveObservationsBtn.textContent = 'Save Observations';
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while saving observations'
                        });
                    });
                });
            }
        });
    </script>