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
        display: none;
    }

    .tabcontent.active {
        display: block;
    }
</style>
<div class="bg-green-50 border border-green-100 rounded-md p-4 mb-6 items-center">
    <div class="flex items-center mb-2">
      <i data-lucide="file" class="w-5 h-5 mr-2 text-green-600"></i>
      <span class="font-medium">File Number Information</span>
    </div>
    <p class="text-sm text-gray-600 mb-4">Select file number type and enter the details</p>
    
    <!-- Add hidden input to track active tab -->
    <input type="hidden" id="activeFileTab" name="activeFileTab" value="mlsFNo">
    
    <!-- Add hidden inputs for the actual database column names -->
    <input type="hidden" id="mlsFNo" name="mlsFNo" value="">
    <input type="hidden" id="kangisFileNo" name="kangisFileNo" value="">
    <input type="hidden" id="NewKANGISFileno" name="NewKANGISFileno" value="">
    
    <div class="bg-white p-2 rounded-md mb-4 flex space-x-2">
      <button type="button" class="tablinks active px-4 py-2 rounded-md hover:bg-gray-100" onclick="openFileTab(event, 'mlsFNoTab')">MLS</button>
      <button type="button" class="tablinks px-4 py-2 rounded-md hover:bg-gray-100" onclick="openFileTab(event, 'kangisFileNoTab')">KANGIS</button>
      <button type="button" class="tablinks px-4 py-2 rounded-md hover:bg-gray-100" onclick="openFileTab(event, 'NewKANGISFilenoTab')">New KANGIS</button>
    </div>
    
  
   <div id="mlsFNoTab" class="tabcontent active">
    <p class="text-sm text-gray-600 mb-2">MLS File Number</p>
    
    <!-- Radio buttons for file type selection -->
    <div class="mb-4">
      <label class="block text-sm mb-2">File Type</label>
      <div class="flex space-x-4">
        <label class="flex items-center">
          <input type="radio" name="mlsFileType" value="regular" id="mlsRegularFile" class="mr-2" checked>
          <span class="text-sm">Regular File</span>
        </label>
        <label class="flex items-center">
          <input type="radio" name="mlsFileType" value="temporary" id="mlsTemporaryFile" class="mr-2">
          <span class="text-sm">Temporary File</span>
        </label>
        <label class="flex items-center">
          <input type="radio" name="mlsFileType" value="extension" id="mlsExtensionFile" class="mr-2">
          <span class="text-sm">Extension</span>
        </label>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-4">
      <div>
        <label class="block text-sm mb-1">File Prefix</label>
        <div class="relative">
          <select class="w-full p-2 border border-gray-300 rounded-md appearance-none pr-8" id="mlsFileNoPrefix" name="mlsFileNoPrefix">
            <option>Select prefix</option>
            @foreach (['COM', 'RES', 'CON-COM', 'CON-RES', 'CON-AG', 'CON-IND'] as $prefix)
            <option value="{{ $prefix }}">{{ $prefix }}</option>
        @endforeach
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
          </div>
        </div>
      </div>
      <div>
        <label class="block text-sm mb-1">Year</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="mlsFileYear" name="mlsFileYear" placeholder="e.g. 2024" maxlength="4" value="{{ isset($result) ? (date('Y')) : date('Y') }}">
      </div>
      <div>
        <label class="block text-sm mb-1">Serial No</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="mlsFileSerial" name="mlsFileSerial" placeholder="e.g. 572" value="{{ isset($result) ? ($result->mlsFileNumber ? explode('-', $result->mlsFileNumber)[1] ?? '' : '') : '' }}">
      </div>
    </div>

    <!-- Enhanced Full File Number Display -->
    <div class="mb-3">
      <label class="block text-sm mb-1">Full File Number</label>
      <div class="bg-blue-50 border-2 border-blue-200 rounded-md p-3">
        <div id="mlsPreviewFileNumber" class="text-lg font-semibold text-blue-800">
          {{ isset($result) && $result->mlsFNo ? $result->mlsFNo : 'Enter details above to see preview' }}
        </div>
      </div>
    </div>
  </div>  

  <div id="kangisFileNoTab" class="tabcontent">
    <p class="text-sm text-gray-600 mb-2">KANGIS File Number</p>
    <div class="grid grid-cols-3 gap-4 mb-3">
      <div>
        <label class="block text-sm mb-1">File Prefix</label>
        <div class="relative">
          <select class="w-full p-2 border border-gray-300 rounded-md appearance-none pr-8"    id="kangisFileNoPrefix" name="kangisFileNoPrefix">
            <option value="">Select Prefix</option>
                        @foreach (['KNML', 'MNKL', 'MLKN', 'KNGP'] as $prefix)
                            <option value="{{ $prefix }}">{{ $prefix }}</option>
                        @endforeach
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
          </div>
        </div>
      </div>
      <div>
        <label class="block text-sm mb-1">Serial Number</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="kangisFileNumber" name="kangisFileNumber" placeholder="e.g. 0001 or 2500">
      </div>
       <div>
        <label class="block text-sm mb-1">Full FileNo</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md"  id="kangisPreviewFileNumber" name="kangisPreviewFileNumber"
        value="{{ isset($result) ? ($result->kangisFileNo ?: '') : '' }}" readonly>
      </div>
    </div>
  </div> 

  <div id="NewKANGISFilenoTab" class="tabcontent">
    <p class="text-sm text-gray-600 mb-2">
        New KANGIS File Number</p>
    <div class="grid grid-cols-3 gap-4 mb-3">
      <div>
        <label class="block text-sm mb-1">File Prefix</label>
        <div class="relative">
          <select class="w-full p-2 border border-gray-300 rounded-md appearance-none pr-8"  id="newKangisFileNoPrefix" name="newKangisFileNoPrefix">
        
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
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md"  id="newKangisFileNumber" name="newKangisFileNumber" 
        placeholder="e.g. 1586" value="{{ isset($result) ? ($result->newKangisFileNumber ?: '') : '' }}">
      </div>
       <div>
        <label class="block text-sm mb-1">Full FileNo</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md"  id="newKangisPreviewFileNumber" name="newKangisPreviewFileNumber"
        value="{{ isset($result) ? ($result->NewKANGISFileno ?: '') : '' }}" readonly>
      </div>
    </div>
</div>
    
<script>
    function updateFileNumberPreview() {
        const prefixEl = document.getElementById('fileNoPrefix');
        const numberEl = document.getElementById('fileNumber');
        const previewEl = document.getElementById('Previewflenumber');

        const prefix = prefixEl.value;
        let number = numberEl.value.trim();

        // Set placeholder based on selected prefix
        if (prefix) {
            if (['KNML', 'MNKL', 'MLKN', 'KNGP'].includes(prefix)) {
                numberEl.placeholder = "e.g. 00001";
            } else if (prefix === "KN") {
                numberEl.placeholder = "e.g. 0001";
            } else if (['CON-COM', 'CON-RES', 'CON-AG', 'CON-IND', 'RES'].includes(prefix)) {
                numberEl.placeholder = "e.g. 01";
            } else {
                numberEl.placeholder = "Format example";
            }
        }

        // Format the number based on the prefix
        if (prefix && number) {
            if (['KNML', 'MNKL', 'MLKN', 'KNGP'].includes(prefix)) {
                // Ensure 5-digit format with leading zeros
                number = number.padStart(5, '0');
                numberEl.value = number;
                previewEl.value = prefix + ' ' + number;
            } else if (prefix === "KN") {
                previewEl.value = prefix + number;
            } else if (['CON-COM', 'CON-RES', 'CON-AG', 'CON-IND', 'RES'].includes(prefix)) {
                previewEl.value = prefix + '-' + number;
            } else {
                previewEl.value = prefix + '/' + number;
            }
        } else if (prefix) {
            previewEl.value = prefix;
        } else if (number) {
            previewEl.value = number;
        } else {
            previewEl.value = '';
        }

        // Validation based on prefix
        let isValid = true;
        if (prefix === "KN") {
            isValid = /^\d+$/.test(number);
        } else if (["KNML", "MNKL", "MLKN", "KNGP"].includes(prefix)) {
            isValid = /^\d{5}$/.test(number);
        } else if (['CON-COM', 'CON-RES', 'CON-AG', 'CON-IND', 'RES'].includes(prefix)) {
            isValid = /^\d+$/.test(number);
        }

        if (prefix && number && isValid) {
            prefixEl.style.color = 'red';
            numberEl.style.color = 'red';
            previewEl.style.color = 'red';
        } else {
            prefixEl.style.color = '';
            numberEl.style.color = '';
            previewEl.style.color = '';
        }
    }

    // Fixed tab switching function
    function openFileTab(evt, tabName) {
        console.log("Opening tab:", tabName); // Debug logging
        
        // Hide all tab content
        var tabcontent = document.getElementsByClassName("tabcontent");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
            tabcontent[i].style.display = "none"; // Explicitly hide
        }

        // Remove active class from all tab buttons
        var tablinks = document.getElementsByClassName("tablinks");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }

        // Show the current tab and add active class to the button
        var currentTab = document.getElementById(tabName);
        if (currentTab) {
            currentTab.classList.add("active");
            currentTab.style.display = "block"; // Explicitly show
        } else {
            console.error("Tab not found:", tabName);
        }
        
        evt.currentTarget.classList.add("active");
        
        // Set the active tab value based on the database field names, not tab IDs
        if (tabName === "mlsFNoTab") {
            document.getElementById('activeFileTab').value = "mlsFNo";
        updateMainFilenoField();
        } else if (tabName === "kangisFileNoTab") {
            document.getElementById('activeFileTab').value = "kangisFileNo";
        updateMainFilenoField();
        } else if (tabName === "NewKANGISFilenoTab") {
            document.getElementById('activeFileTab').value = "NewKANGISFileno";
        updateMainFilenoField();
        }
    }


    // Function to update the main fileno field based on active tab
    function updateMainFilenoField() {
        const activeTab = document.getElementById('activeFileTab').value;
        const mainFilenoField = document.getElementById('fileno');
        
        if (!mainFilenoField) return; // Exit if main fileno field doesn't exist
        
        let fileNumber = '';
        
        // Get the file number based on active tab
        if (activeTab === 'mlsFNo') {
            fileNumber = document.getElementById('mlsFNo').value;
        } else if (activeTab === 'kangisFileNo') {
            fileNumber = document.getElementById('kangisFileNo').value;
        } else if (activeTab === 'NewKANGISFileno') {
            fileNumber = document.getElementById('NewKANGISFileno').value;
        }
        
        // Update the main fileno field
        mainFilenoField.value = fileNumber;
        
        // Trigger validation if the function exists
        if (typeof validateSurveyForm === 'function') {
            validateSurveyForm();
        }
    }
    // Enhanced MLS file number preview with new format and file types
    function updateMlsFileNumberPreview() {
        const prefixEl = document.getElementById('mlsFileNoPrefix');
        const yearEl = document.getElementById('mlsFileYear');
        const serialEl = document.getElementById('mlsFileSerial');
        const previewEl = document.getElementById('mlsPreviewFileNumber');
        const dbFieldEl = document.getElementById('mlsFNo');
        
        // Get file type from radio buttons
        const fileTypeRadios = document.querySelectorAll('input[name="mlsFileType"]');
        let fileType = 'regular';
        for (const radio of fileTypeRadios) {
            if (radio.checked) {
                fileType = radio.value;
                break;
            }
        }

        const prefix = prefixEl.value;
        const year = yearEl.value.trim();
        const serial = serialEl.value.trim();

        let formatted = '';
        let displayText = '';

        if (prefix && year && serial) {
            // Base format: PREFIX-YEAR-SERIAL
            formatted = `${prefix}-${year}-${serial}`;
            
            // Add suffix based on file type
            if (fileType === 'temporary') {
                formatted += ' (T)';
            } else if (fileType === 'extension') {
                formatted += ' AND EXTENSION';
            }
            
            displayText = formatted;
        } else if (prefix || year || serial) {
            // Show partial preview
            const parts = [];
            if (prefix) parts.push(prefix);
            if (year) parts.push(year);
            if (serial) parts.push(serial);
            
            displayText = parts.join('-');
            if (parts.length > 0) {
                if (fileType === 'temporary') {
                    displayText += ' (T)';
                } else if (fileType === 'extension') {
                    displayText += ' AND EXTENSION';
                }
            }
        } else {
            displayText = 'Enter details above to see preview';
        }

        // Update preview display
        previewEl.textContent = displayText;
        
        // Update database field only with complete format
        if (prefix && year && serial) {
            dbFieldEl.value = formatted;
        } else {
            dbFieldEl.value = '';
        }
        
        updateMainFilenoField(); // Update main fileno field
    }

    // Format KANGIS file number preview
    function updateKangisFileNumberPreview() {
        const prefixEl = document.getElementById('kangisFileNoPrefix');
        const numberEl = document.getElementById('kangisFileNumber');
        const previewEl = document.getElementById('kangisPreviewFileNumber');
        const dbFieldEl = document.getElementById('kangisFileNo');

        const prefix = prefixEl.value;
        let number = numberEl.value.trim();

        if (prefix && number) {
            // Pad to 5 digits
            number = number.padStart(5, '0');
            numberEl.value = number;
            const formatted = prefix + ' ' + number;
            previewEl.value = formatted;
            dbFieldEl.value = formatted; // Set the database field directly
            updateMainFilenoField(); // Update main fileno field
        } else if (prefix) {
            previewEl.value = prefix;
            dbFieldEl.value = prefix;
            updateMainFilenoField();
        } else if (number) {
            previewEl.value = number;
            dbFieldEl.value = number;
            updateMainFilenoField();
        } else {
            previewEl.value = '';
            dbFieldEl.value = '';
            updateMainFilenoField();
        }
    }

    // Format New KANGIS file number preview
    function updateNewKangisFileNumberPreview() {
        const prefixEl = document.getElementById('newKangisFileNoPrefix');
        const numberEl = document.getElementById('newKangisFileNumber');
        const previewEl = document.getElementById('newKangisPreviewFileNumber');
        const dbFieldEl = document.getElementById('NewKANGISFileno'); // Important: This must match DB column name

        const prefix = prefixEl.value;
        let number = numberEl.value.trim();

        if (prefix && number) {
            const formatted = prefix + number;
            previewEl.value = formatted;
            dbFieldEl.value = formatted; // Set the database field directly
            updateMainFilenoField(); // Update main fileno field
        } else if (prefix) {
            previewEl.value = prefix;
            dbFieldEl.value = prefix;
            updateMainFilenoField();
        } else if (number) {
            previewEl.value = number;
            dbFieldEl.value = number;
            updateMainFilenoField();
        } else {
            previewEl.value = '';
            dbFieldEl.value = '';
            updateMainFilenoField();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize file number previews
        updateMlsFileNumberPreview();
        updateKangisFileNumberPreview();
        updateNewKangisFileNumberPreview();

        // Add event listeners for MLS file number preview updates
        document.getElementById('mlsFileNoPrefix').addEventListener('change', updateMlsFileNumberPreview);
        document.getElementById('mlsFileYear').addEventListener('input', updateMlsFileNumberPreview);
        document.getElementById('mlsFileSerial').addEventListener('input', updateMlsFileNumberPreview);
        
        // Add event listeners for radio buttons
        const mlsFileTypeRadios = document.querySelectorAll('input[name="mlsFileType"]');
        mlsFileTypeRadios.forEach(radio => {
            radio.addEventListener('change', updateMlsFileNumberPreview);
        });

        document.getElementById('kangisFileNoPrefix').addEventListener('change', updateKangisFileNumberPreview);
        document.getElementById('kangisFileNumber').addEventListener('input', updateKangisFileNumberPreview);

        document.getElementById('newKangisFileNoPrefix').addEventListener('change',
            updateNewKangisFileNumberPreview);
        document.getElementById('newKangisFileNumber').addEventListener('input',
            updateNewKangisFileNumberPreview);
            
        // Make sure the active tab is properly displayed on page load
        var activeTabName = document.getElementById('activeFileTab').value;
        var tabToShow = "mlsFNoTab"; // Default
        
        if (activeTabName === "kangisFileNo") {
            tabToShow = "kangisFileNoTab";
        } else if (activeTabName === "NewKANGISFileno") {
            tabToShow = "NewKANGISFilenoTab";
        }
        
        // Simulate a click on the appropriate tab button
        var tabButtons = document.getElementsByClassName("tablinks");
        for (var i = 0; i < tabButtons.length; i++) {
            if (tabButtons[i].getAttribute("onclick").includes(tabToShow)) {
                // Create a fake event object
                var fakeEvent = { currentTarget: tabButtons[i] };
                openFileTab(fakeEvent, tabToShow);
                break;
            }
        }
    });
</script>
