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
    <p class="text-sm text-gray-600 mb-3">MLS File Number</p>
    
    <!-- Radio buttons for file type -->
    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">File Type</label>
      <div class="flex space-x-6">
        <label class="flex items-center">
          <input type="radio" name="mlsFileType" id="mlsRegular" value="regular" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" checked>
          <span class="ml-2 text-sm text-gray-700">Regular File</span>
        </label>
        <label class="flex items-center">
          <input type="radio" name="mlsFileType" id="mlsTemporary" value="temporary" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
          <span class="ml-2 text-sm text-gray-700">Temporary File</span>
        </label>
        <label class="flex items-center">
          <input type="radio" name="mlsFileType" id="mlsExtension" value="extension" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
          <span class="ml-2 text-sm text-gray-700">Extension</span>
        </label>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-3">
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
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="mlsYear" name="mlsYear" placeholder="e.g. 2024" maxlength="4">
      </div>
      <div>
        <label class="block text-sm mb-1">Serial No</label>
        <input type="text" class="w-full p-2 border border-gray-300 rounded-md" id="mlsFileNumber" name="mlsFileNumber" placeholder="e.g. 572">
      </div>
    </div>

    <!-- Full FileNo - Displayed prominently below -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
      <label class="block text-lg font-semibold text-gray-800 mb-2">Full File Number</label>
      <div class="text-lg font-semibold text-blue-700 p-3 bg-white border-2 border-blue-300 rounded-md min-h-[50px] flex items-center" id="mlsPreviewFileNumber">Enter file details above</div>
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
    // Updated function to maintain values across tabs
    function openFileTab(evt, tabName) {
        console.log("Opening tab:", tabName);
        
        // Save current values before switching tabs
        if (document.getElementById('activeFileTab').value === "mlsFNo") {
            updateMlsFileNumberPreview();
        } else if (document.getElementById('activeFileTab').value === "kangisFileNo") {
            updateKangisFileNumberPreview();
        } else if (document.getElementById('activeFileTab').value === "NewKANGISFileno") {
            updateNewKangisFileNumberPreview();
        }
        
        // Hide all tab content
        var tabcontent = document.getElementsByClassName("tabcontent");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
            tabcontent[i].style.display = "none";
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
            currentTab.style.display = "block";
        } else {
            console.error("Tab not found:", tabName);
        }
        
        evt.currentTarget.classList.add("active");
        
        // Set the active tab value based on the database field names
        if (tabName === "mlsFNoTab") {
            document.getElementById('activeFileTab').value = "mlsFNo";
        } else if (tabName === "kangisFileNoTab") {
            document.getElementById('activeFileTab').value = "kangisFileNo";
        } else if (tabName === "NewKANGISFilenoTab") {
            document.getElementById('activeFileTab').value = "NewKANGISFileno";
        }
    }

    // Get selected MLS file type
    function getMlsFileType() {
        const radioButtons = document.querySelectorAll('input[name="mlsFileType"]');
        for (const radioButton of radioButtons) {
            if (radioButton.checked) {
                return radioButton.value;
            }
        }
        return 'regular';
    }

    // Format MLS file number preview
    function updateMlsFileNumberPreview() {
        const prefixEl = document.getElementById('mlsFileNoPrefix');
        const yearEl = document.getElementById('mlsYear');
        const numberEl = document.getElementById('mlsFileNumber');
        const previewEl = document.getElementById('mlsPreviewFileNumber');
        const dbFieldEl = document.getElementById('mlsFNo');

        const prefix = prefixEl.value;
        const year = yearEl.value.trim();
        const number = numberEl.value.trim();
        const fileType = getMlsFileType();

        let baseFileNo = '';
        const parts = [];
        if (prefix) parts.push(prefix);
        if (year) parts.push(year);
        if (number) parts.push(number);
        
        baseFileNo = parts.join('-');

        let finalFileNo = baseFileNo;
        if (baseFileNo && fileType === 'temporary') {
            finalFileNo = baseFileNo + ' (T)';
        } else if (baseFileNo && fileType === 'extension') {
            finalFileNo = baseFileNo + ' AND EXTENSION';
        }

        if (finalFileNo) {
            previewEl.textContent = finalFileNo;
            dbFieldEl.value = finalFileNo;
        } else {
            previewEl.textContent = 'Enter file details above';
            dbFieldEl.value = '';
        }
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
            dbFieldEl.value = formatted; // Set the database field
        } else if (prefix) {
            previewEl.value = prefix;
            dbFieldEl.value = prefix;
        } else if (number) {
            previewEl.value = number;
            dbFieldEl.value = number;
        } else {
            previewEl.value = '';
            dbFieldEl.value = '';
        }
    }

    // Format New KANGIS file number preview
    function updateNewKangisFileNumberPreview() {
        const prefixEl = document.getElementById('newKangisFileNoPrefix');
        const numberEl = document.getElementById('newKangisFileNumber');
        const previewEl = document.getElementById('newKangisPreviewFileNumber');
        const dbFieldEl = document.getElementById('NewKANGISFileno');

        const prefix = prefixEl.value;
        let number = numberEl.value.trim();

        if (prefix && number) {
            const formatted = prefix + number;
            previewEl.value = formatted;
            dbFieldEl.value = formatted; // Set the database field
        } else if (prefix) {
            previewEl.value = prefix;
            dbFieldEl.value = prefix;
        } else if (number) {
            previewEl.value = number;
            dbFieldEl.value = number;
        } else {
            previewEl.value = '';
            dbFieldEl.value = '';
        }
    }

    // Updates the form data for submission
    function updateFormFileData() {
        // Ensure all file numbers are properly set in hidden fields
        updateMlsFileNumberPreview();
        updateKangisFileNumberPreview();
        updateNewKangisFileNumberPreview();
        
        // Get the active tab
        const activeTab = document.getElementById('activeFileTab').value;
        
        // Set the active file number based on the active tab
        if (activeTab === "mlsFNo") {
            document.getElementById('mlsFNo').value = document.getElementById('mlsPreviewFileNumber').textContent;
        } else if (activeTab === "kangisFileNo") {
            document.getElementById('kangisFileNo').value = document.getElementById('kangisPreviewFileNumber').value;
        } else if (activeTab === "NewKANGISFileno") {
            document.getElementById('NewKANGISFileno').value = document.getElementById('newKangisPreviewFileNumber').value;
        }
        
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize file number previews
        updateMlsFileNumberPreview();
        updateKangisFileNumberPreview();
        updateNewKangisFileNumberPreview();

        // Add event listeners for MLS file number preview updates
        document.getElementById('mlsFileNoPrefix').addEventListener('change', updateMlsFileNumberPreview);
        document.getElementById('mlsYear').addEventListener('input', updateMlsFileNumberPreview);
        document.getElementById('mlsFileNumber').addEventListener('input', updateMlsFileNumberPreview);
        
        // Add event listeners for radio buttons
        document.getElementById('mlsRegular').addEventListener('change', updateMlsFileNumberPreview);
        document.getElementById('mlsTemporary').addEventListener('change', updateMlsFileNumberPreview);
        document.getElementById('mlsExtension').addEventListener('change', updateMlsFileNumberPreview);

        document.getElementById('kangisFileNoPrefix').addEventListener('change', updateKangisFileNumberPreview);
        document.getElementById('kangisFileNumber').addEventListener('input', updateKangisFileNumberPreview);

        document.getElementById('newKangisFileNoPrefix').addEventListener('change', updateNewKangisFileNumberPreview);
        document.getElementById('newKangisFileNumber').addEventListener('input', updateNewKangisFileNumberPreview);
            
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
                var fakeEvent = { currentTarget: tabButtons[i] };
                openFileTab(fakeEvent, tabToShow);
                break;
            }
        }
        
        // Attach form submission handler to ensure all hidden fields are updated
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                updateFormFileData();
            });
        }
    });
</script>