<style>
   .tab {
        overflow: hidden;
    }

    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 10px 16px;
        transition: 0.3s;
        font-size: 14px;
    }

    .tab button:hover {
        background-color: #ddd;
    }

    .tab button.active {
        background-color: #ccc;
    }

    /* Fix for tab content visibility */
    .tabcontent {
        display: none !important;
        width: 100%; /* Ensure full width */
        visibility: hidden;
    }

    .tabcontent.active {
        display: block !important;
        visibility: visible !important;
    }
</style>
<div class="bg-green-50 border border-green-100 rounded-md p-4 mb-6 items-center">
    <div class="flex items-center mb-2">
      <i data-lucide="file" class="w-5 h-5 mr-2 text-green-600"></i>
      <span class="font-medium">File Number Information</span>
    </div>
    <p class="text-sm text-gray-600 mb-4">Select file number type and enter the details</p>
    
    @php
        // Use provided prefix or default to empty string
        $prefix = $prefix ?? '';
    @endphp
    
    <!-- Add hidden input to track active tab -->
    <input type="hidden" id="{{ $prefix }}activeFileTab" name="activeFileTab" value="mlsFNo">
    
    <!-- Add hidden inputs for the actual database column names -->
    <input type="hidden" id="{{ $prefix }}mlsFNo" name="mlsFNo" value="">
    <input type="hidden" id="{{ $prefix }}kangisFileNo" name="kangisFileNo" value="">
    <input type="hidden" id="{{ $prefix }}NewKANGISFileno" name="NewKANGISFileno" value="">
    
    <div class="bg-white p-2 rounded-md mb-4 flex space-x-2">
      <button type="button" class="{{ $prefix }}tablinks active px-4 py-2 rounded-md hover:bg-gray-100" onclick="openFileTab('{{ $prefix }}', event, '{{ $prefix }}mlsFNoTab')">MLS</button>
      <button type="button" class="{{ $prefix }}tablinks px-4 py-2 rounded-md hover:bg-gray-100" onclick="openFileTab('{{ $prefix }}', event, '{{ $prefix }}kangisFileNoTab')">KANGIS</button>
      <button type="button" class="{{ $prefix }}tablinks px-4 py-2 rounded-md hover:bg-gray-100" onclick="openFileTab('{{ $prefix }}', event, '{{ $prefix }}NewKANGISFilenoTab')">New KANGIS</button>
    </div>
    
    <!-- Tab content divs -->
    <div id="{{ $prefix }}mlsFNoTab" class="tabcontent active">
      <p class="text-sm text-gray-600 mb-2">MLS File Number</p>
      <div class="grid grid-cols-3 gap-4 mb-3">
      <div>
        <label class="block text-sm mb-1">File Prefix</label>
        <div class="relative">
          <select class="w-full p-2 border border-gray-300 rounded-md appearance-none pr-8" id="{{ $prefix }}mlsFileNoPrefix" name="mlsFileNoPrefix">
            <option>Select prefix</option>
            @foreach (['COM', 'RES', 'CON-COM', 'CON-RES', 'CON-AG', 'CON-IND'] as $prefixOption)
            <option value="{{ $prefixOption }}">{{ $prefixOption }}</option>
            @endforeach
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
          </div>
        </div>
      </div>
      <div>
        <label class="block text-sm mb-1">Serial Number</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="{{ $prefix }}mlsFileNumber" name="mlsFileNumber" placeholder="e.g. 2022-572" value="{{ isset($result) ? ($result->mlsFileNumber ?: '') : '' }}">
      </div>
       <div>
        <label class="block text-sm mb-1">Full FileNo</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="{{ $prefix }}mlsPreviewFileNumber" name="mlsPreviewFileNumber"
        value="{{ isset($result) ? ($result->mlsFNo ?: '') : '' }}" readonly>
      </div>
    </div>
    </div>  

    <div id="{{ $prefix }}kangisFileNoTab" class="tabcontent">
      <p class="text-sm text-gray-600 mb-2">KANGIS File Number</p>
      <div class="grid grid-cols-3 gap-4 mb-3">
      <div>
        <label class="block text-sm mb-1">File Prefix</label>
        <div class="relative">
          <select class="w-full p-2 border border-gray-300 rounded-md appearance-none pr-8" id="{{ $prefix }}kangisFileNoPrefix" name="kangisFileNoPrefix">
            <option value="">Select Prefix</option>
                        @foreach (['KNML', 'MNKL', 'MLKN', 'KNGP'] as $prefixOption)
                            <option value="{{ $prefixOption }}">{{ $prefixOption }}</option>
                        @endforeach
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
          </div>
        </div>
      </div>
      <div>
        <label class="block text-sm mb-1">Serial Number</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="{{ $prefix }}kangisFileNumber" name="kangisFileNumber" placeholder="e.g. 0001 or 2500">
      </div>
       <div>
        <label class="block text-sm mb-1">Full FileNo</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="{{ $prefix }}kangisPreviewFileNumber" name="kangisPreviewFileNumber"
        value="{{ isset($result) ? ($result->kangisFileNo ?: '') : '' }}" readonly>
      </div>
    </div>
    </div> 

    <div id="{{ $prefix }}NewKANGISFilenoTab" class="tabcontent">
      <p class="text-sm text-gray-600 mb-2">
        New KANGIS File Number</p>
      <div class="grid grid-cols-3 gap-4 mb-3">
      <div>
        <label class="block text-sm mb-1">File Prefix</label>
        <div class="relative">
          <select class="w-full p-2 border border-gray-300 rounded-md appearance-none pr-8" id="{{ $prefix }}newKangisFileNoPrefix" name="newKangisFileNoPrefix">
        
            <option value="">Select Prefix</option>
            <option value="KN">KN</option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
          </div>
        </div>
      </div>
      <div>
        <label class="block text-sm mb-1">Serial Number</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="{{ $prefix }}newKangisFileNumber" name="newKangisFileNumber" 
        placeholder="e.g. 1586" value="{{ isset($result) ? ($result->newKangisFileNumber ?: '') : '' }}">
      </div>
       <div>
        <label class="block text-sm mb-1">Full FileNo</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="{{ $prefix }}newKangisPreviewFileNumber" name="newKangisPreviewFileNumber"
        value="{{ isset($result) ? ($result->NewKANGISFileno ?: '') : '' }}" readonly>
      </div>
    </div>
    </div>
</div>
    
<script>
    // Updated function to maintain values across tabs and support multiple instances
    function openFileTab(prefix, evt, tabName) {
        console.log("Opening tab:", tabName, "for prefix:", prefix);
        
        try {
            // Save current values before switching tabs
            if (document.getElementById(prefix + 'activeFileTab').value === "mlsFNo") {
                updateMlsFileNumberPreview(prefix);
            } else if (document.getElementById(prefix + 'activeFileTab').value === "kangisFileNo") {
                updateKangisFileNumberPreview(prefix);
            } else if (document.getElementById(prefix + 'activeFileTab').value === "NewKANGISFileno") {
                updateNewKangisFileNumberPreview(prefix);
            }
            
            // Get all tab content for this form instance
            var tabcontent = document.querySelectorAll("[id^='" + prefix + "'][id$='Tab'][class*='tabcontent']");
            console.log("Found", tabcontent.length, "tab content elements for prefix", prefix);
            
            // Remove active class and hide all tab content first
            for (var i = 0; i < tabcontent.length; i++) {
                var tab = tabcontent[i];
                console.log("Processing tab:", tab.id, "Current display:", getComputedStyle(tab).display);
                tab.classList.remove("active");
                tab.style.display = "none";
            }

            // Remove active class from all tab buttons for this form instance
            var tablinks = document.getElementsByClassName(prefix + "tablinks");
            for (var i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }

            // Show the current tab and add active class to the button
            var currentTab = document.getElementById(tabName);
            if (currentTab) {
                console.log("Activating tab:", tabName);
                currentTab.classList.add("active");
                currentTab.style.display = "block";
                currentTab.style.visibility = "visible";
                // Log the actual computed style to confirm visibility
                console.log("Tab display after change:", getComputedStyle(currentTab).display);
            } else {
                console.error("Tab not found:", tabName);
            }
            
            evt.currentTarget.classList.add("active");
            
            // Set the active tab value based on the database field names
            if (tabName === prefix + "mlsFNoTab") {
                document.getElementById(prefix + 'activeFileTab').value = "mlsFNo";
            } else if (tabName === prefix + "kangisFileNoTab") {
                document.getElementById(prefix + 'activeFileTab').value = "kangisFileNo";
            } else if (tabName === prefix + "NewKANGISFilenoTab") {
                document.getElementById(prefix + 'activeFileTab').value = "NewKANGISFileno";
            }
        } catch (error) {
            console.error("Error in openFileTab:", error);
        }
    }

    // Format MLS file number preview with prefix
    function updateMlsFileNumberPreview(prefix) {
        const prefixEl = document.getElementById(prefix + 'mlsFileNoPrefix');
        const numberEl = document.getElementById(prefix + 'mlsFileNumber');
        const previewEl = document.getElementById(prefix + 'mlsPreviewFileNumber');
        const dbFieldEl = document.getElementById(prefix + 'mlsFNo');

        if (!prefixEl || !numberEl || !previewEl || !dbFieldEl) return;

        const selectedPrefix = prefixEl.value;
        let number = numberEl.value.trim();

        if (selectedPrefix && number) {
            const formatted = selectedPrefix + '-' + number;
            previewEl.value = formatted;
            dbFieldEl.value = formatted; // Set the database field
        } else if (selectedPrefix) {
            previewEl.value = selectedPrefix;
            dbFieldEl.value = selectedPrefix;
        } else if (number) {
            previewEl.value = number;
            dbFieldEl.value = number;
        } else {
            previewEl.value = '';
            dbFieldEl.value = '';
        }
    }

    // Format KANGIS file number preview with prefix
    function updateKangisFileNumberPreview(prefix) {
        const prefixEl = document.getElementById(prefix + 'kangisFileNoPrefix');
        const numberEl = document.getElementById(prefix + 'kangisFileNumber');
        const previewEl = document.getElementById(prefix + 'kangisPreviewFileNumber');
        const dbFieldEl = document.getElementById(prefix + 'kangisFileNo');

        if (!prefixEl || !numberEl || !previewEl || !dbFieldEl) return;

        const selectedPrefix = prefixEl.value;
        let number = numberEl.value.trim();

        if (selectedPrefix && number) {
            // Pad to 5 digits
            number = number.padStart(5, '0');
            numberEl.value = number;
            const formatted = selectedPrefix + ' ' + number;
            previewEl.value = formatted;
            dbFieldEl.value = formatted; // Set the database field
        } else if (selectedPrefix) {
            previewEl.value = selectedPrefix;
            dbFieldEl.value = selectedPrefix;
        } else if (number) {
            previewEl.value = number;
            dbFieldEl.value = number;
        } else {
            previewEl.value = '';
            dbFieldEl.value = '';
        }
    }

    // Format New KANGIS file number preview with prefix
    function updateNewKangisFileNumberPreview(prefix) {
        const prefixEl = document.getElementById(prefix + 'newKangisFileNoPrefix');
        const numberEl = document.getElementById(prefix + 'newKangisFileNumber');
        const previewEl = document.getElementById(prefix + 'newKangisPreviewFileNumber');
        const dbFieldEl = document.getElementById(prefix + 'NewKANGISFileno');

        if (!prefixEl || !numberEl || !previewEl || !dbFieldEl) return;

        const selectedPrefix = prefixEl.value;
        let number = numberEl.value.trim();

        if (selectedPrefix && number) {
            const formatted = selectedPrefix + number;
            previewEl.value = formatted;
            dbFieldEl.value = formatted; // Set the database field
        } else if (selectedPrefix) {
            previewEl.value = selectedPrefix;
            dbFieldEl.value = selectedPrefix;
        } else if (number) {
            previewEl.value = number;
            dbFieldEl.value = number;
        } else {
            previewEl.value = '';
            dbFieldEl.value = '';
        }
    }

    // Updates the form data for submission based on prefix
    function updateFormFileData(prefix) {
        // Ensure all file numbers are properly set in hidden fields
        updateMlsFileNumberPreview(prefix);
        updateKangisFileNumberPreview(prefix);
        updateNewKangisFileNumberPreview(prefix);
        
        // Get the active tab
        const activeTab = document.getElementById(prefix + 'activeFileTab').value;
        
        // Set the active file number based on the active tab
        if (activeTab === "mlsFNo") {
            document.getElementById(prefix + 'mlsFNo').value = document.getElementById(prefix + 'mlsPreviewFileNumber').value;
        } else if (activeTab === "kangisFileNo") {
            document.getElementById(prefix + 'kangisFileNo').value = document.getElementById(prefix + 'kangisPreviewFileNumber').value;
        } else if (activeTab === "NewKANGISFileno") {
            document.getElementById(prefix + 'NewKANGISFileno').value = document.getElementById(prefix + 'newKangisPreviewFileNumber').value;
        }
        
        return true;
    }

    // Initialize the file number component when it's added to the page
    function initFileNumberComponent(prefix) {
        // Initialize file number previews
        updateMlsFileNumberPreview(prefix);
        updateKangisFileNumberPreview(prefix);
        updateNewKangisFileNumberPreview(prefix);

        // Add event listeners for file number preview updates
        const mlsPrefix = document.getElementById(prefix + 'mlsFileNoPrefix');
        const mlsNumber = document.getElementById(prefix + 'mlsFileNumber');
        const kangisPrefix = document.getElementById(prefix + 'kangisFileNoPrefix');
        const kangisNumber = document.getElementById(prefix + 'kangisFileNumber');
        const newKangisPrefix = document.getElementById(prefix + 'newKangisFileNoPrefix');
        const newKangisNumber = document.getElementById(prefix + 'newKangisFileNumber');

        if (mlsPrefix) mlsPrefix.addEventListener('change', function() { updateMlsFileNumberPreview(prefix); });
        if (mlsNumber) mlsNumber.addEventListener('input', function() { updateMlsFileNumberPreview(prefix); });
        if (kangisPrefix) kangisPrefix.addEventListener('change', function() { updateKangisFileNumberPreview(prefix); });
        if (kangisNumber) kangisNumber.addEventListener('input', function() { updateKangisFileNumberPreview(prefix); });
        if (newKangisPrefix) newKangisPrefix.addEventListener('change', function() { updateNewKangisFileNumberPreview(prefix); });
        if (newKangisNumber) newKangisNumber.addEventListener('input', function() { updateNewKangisFileNumberPreview(prefix); });
            
        // Make sure the active tab is properly displayed on page load
        var activeTabName = document.getElementById(prefix + 'activeFileTab').value;
        var tabToShow = prefix + "mlsFNoTab"; // Default
        
        if (activeTabName === "kangisFileNo") {
            tabToShow = prefix + "kangisFileNoTab";
        } else if (activeTabName === "NewKANGISFileno") {
            tabToShow = prefix + "NewKANGISFilenoTab";
        }
        
        // Simulate a click on the appropriate tab button
        var tabButtons = document.getElementsByClassName(prefix + "tablinks");
        for (var i = 0; i < tabButtons.length; i++) {
            if (tabButtons[i].getAttribute("onclick").includes(tabToShow)) {
                var fakeEvent = { currentTarget: tabButtons[i] };
                openFileTab(prefix, fakeEvent, tabToShow);
                break;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // The actual initialization will happen in each form's own script
    });
</script>
