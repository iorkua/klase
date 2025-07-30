<div class="form-section" id="step2">
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold text-center text-gray-800">MINISTRY OF LAND AND PHYSICAL PLANNING</h2>
        <button   class="text-gray-500 hover:text-gray-700" onclick="window.history.back()">
                        <i data-lucide="x" class="w-5 h-5"></i>
                      </button>
    </div>
    
    <div class="mb-6">
      <div class="flex items-center mb-2">
        <i data-lucide="file-text" class="w-5 h-5 mr-2 text-green-600"></i>
        <h3 class="text-lg font-bold">Application for Sectional Titling - Main Application</h3>
        <div class="ml-auto flex items-center">
          <span class="text-gray-600 mr-2">Land Use:</span>
          <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">{{ $motherApplication->land_use ?? 'N/A' }}</span>
        </div>
      </div>
      <p class="text-gray-600">Complete the form below to submit a new primary application for sectional titling</p>
    </div>

    <div class="flex items-center mb-8">
      <div class="flex items-center mr-4">
        <div class="step-circle inactive-tab cursor-pointer" onclick="goToStep(1)">1</div>
      </div>
      <div class="flex items-center mr-4">
        <div class="step-circle active-tab cursor-pointer" onclick="goToStep(2)">2</div>
      </div>
      <div class="flex items-center mr-4">
        <div class="step-circle inactive-tab cursor-pointer" onclick="goToStep(3)">3</div>
      </div> 
       <div class="flex items-center mr-4">
        <div class="step-circle inactive-tab cursor-pointer" onclick="goToStep(4)">4</div>
      </div>
      <div class="ml-4">Step 2</div>
    </div>

    <div class="mb-6">
      <div class="flex items-start mb-4">
        <i data-lucide="home" class="w-5 h-5 mr-2 text-green-600"></i>
        <span class="font-medium">Shared Areas</span>
      </div>
      
   
<div class="space-y-4">
  <p class="mb-2 text-gray-700">Select all shared areas that apply:</p>
    <!-- Check All / Uncheck All Buttons -->
  <div class="mb-2 flex gap-2">
    <button type="button" class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm border border-green-200 hover:bg-green-200" onclick="checkAllSharedAreas()">Check All</button>
    <button type="button" class="px-3 py-1 bg-red-100 text-red-800 rounded text-sm border border-red-200 hover:bg-red-200" onclick="uncheckAllSharedAreas()">Uncheck All</button>
  </div>
  <div class="grid grid-cols-3 gap-4">
    <div class="flex items-center">
      <input type="checkbox" id="hallways" name="shared_areas[]" value="hallways" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
      <label for="hallways" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="door-open" class="w-4 h-4 mr-1 text-gray-500"></i>
        Hallways
      </label>
    </div>
    
    <div class="flex items-center">
      <input type="checkbox" id="gardens" name="shared_areas[]" value="gardens" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
      <label for="gardens" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="flower" class="w-4 h-4 mr-1 text-gray-500"></i>
        Gardens
      </label>
    </div>
    
    <div class="flex items-center">
      <input type="checkbox" id="parking_lots" name="shared_areas[]" value="parking_lots" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
      <label for="parking_lots" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="car" class="w-4 h-4 mr-1 text-gray-500"></i>
        Parking Lots
      </label>
    </div>
    
    <div class="flex items-center">
      <input type="checkbox" id="swimming_pool" name="shared_areas[]" value="swimming_pool" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
      <label for="swimming_pool" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="droplets" class="w-4 h-4 mr-1 text-gray-500"></i>
        Swimming Pool
      </label>
    </div>
    
    <div class="flex items-center">
      <input type="checkbox" id="gym" name="shared_areas[]" value="gym" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
      <label for="gym" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="dumbbell" class="w-4 h-4 mr-1 text-gray-500"></i>
        Gym/Fitness Center
      </label>
    </div>
    
    <div class="flex items-center">
      <input type="checkbox" id="rooftop" name="shared_areas[]" value="rooftop" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
      <label for="rooftop" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="mountain" class="w-4 h-4 mr-1 text-gray-500"></i>
        Rooftop Terrace
      </label>
    </div>
    
    <div class="flex items-center">
      <input type="checkbox" id="lobby" name="shared_areas[]" value="lobby" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
      <label for="lobby" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="sofa" class="w-4 h-4 mr-1 text-gray-500"></i>
        Lobby
      </label>
    </div>
    
    <div class="flex items-center">
      <input type="checkbox" id="elevator" name="shared_areas[]" value="elevator" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
      <label for="elevator" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="arrow-up-down" class="w-4 h-4 mr-1 text-gray-500"></i>
        Elevator
      </label>
    </div>
    
    <div class="flex items-center">
      <input type="checkbox" id="storage" name="shared_areas[]" value="storage" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
      <label for="storage" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="package" class="w-4 h-4 mr-1 text-gray-500"></i>
        Storage Areas
      </label>
    </div>

       <div class="flex items-center">
      <input type="checkbox" id="other_areas" name="shared_areas[]" value="other" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500" onchange="toggleOtherAreasTextarea()">
      <label for="other_areas" class="ml-2 text-gray-700 flex items-center">
        <i data-lucide="plus-circle" class="w-4 h-4 mr-1 text-gray-500"></i>
        Other
      </label>
    </div>

  </div>

  <div id="other_areas_container" class="mt-4" style="display: none;">
    <label for="other_areas_detail" class="block text-sm font-medium text-gray-700 mb-1">Please specify other shared areas:</label>
    <textarea id="other_areas_detail" name="other_areas_detail" rows="3" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter other shared areas here..."></textarea>
  </div>
</div>
      
      <div class="flex justify-between mt-8">
        <button class="px-4 py-2 bg-white border border-gray-300 rounded-md" id="backStep2">Back</button>
        <div class="flex items-center">
          <span class="text-sm text-gray-500 mr-4">Step 2 of 4</span>
          <button class="px-4 py-2 bg-black text-white rounded-md" id="nextStep2">Next</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function toggleOtherAreasTextarea() {
  const checkbox = document.getElementById('other_areas');
  const container = document.getElementById('other_areas_container');
  
  if (checkbox.checked) {
    container.style.display = 'block';
  } else {
    container.style.display = 'none';
    // Clear the textarea when unchecked
    document.getElementById('other_areas_detail').value = '';
  }
}

// Check all shared areas except "Other"
function checkAllSharedAreas() {
  document.querySelectorAll('input[name="shared_areas[]"]').forEach(cb => {
      if (cb.value !== 'other') { 
          cb.checked = true;
      } else {
          cb.checked = false;
      }
  });
  toggleOtherAreasTextarea();
}

// Uncheck all shared areas
function uncheckAllSharedAreas() {
  document.querySelectorAll('input[name="shared_areas[]"]').forEach(cb => {
    cb.checked = false;
  });
  toggleOtherAreasTextarea();
}

// Initialize on page load to handle pre-filled forms
document.addEventListener('DOMContentLoaded', function() {
  toggleOtherAreasTextarea();
});
</script>