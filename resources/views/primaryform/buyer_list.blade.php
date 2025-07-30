<div class="form-section" id="step4">
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold text-center text-gray-800">MINISTRY OF LAND AND PHYSICAL PLANNING</h2>
      <button id="closeModal3" class="text-gray-500 hover:text-gray-700">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <div class="mb-6">
      <div class="flex items-center mb-2">
        <i data-lucide="file-text" class="w-5 h-5 mr-2 text-green-600"></i>
        <h3 class="text-lg font-bold">Application for Sectional Titling - Main Application</h3>
        <div class="ml-auto flex items-center">
          <span class="text-gray-600 mr-2">Land Use:</span>
          <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
            @if (request()->query('landuse') === 'Commercial')
              Commercial
            @elseif (request()->query('landuse') === 'Residential')
              Residential
            @elseif (request()->query('landuse') === 'Industrial')
              Industrial
            @else
              Mixed Use
            @endif
          </span>
        </div>
      </div>
      <p class="text-gray-600">Complete the form below to submit a new primary application for sectional titling
      </p>
    </div>

    <div class="flex items-center mb-8">
      <div class="flex items-center mr-4">
        <div class="step-circle inactive cursor-pointer" onclick="goToStep(1)">1</div>
      </div>
      <div class="flex items-center mr-4">
        <div class="step-circle inactive cursor-pointer" onclick="goToStep(2)">2</div>
      </div>
      <div class="flex items-center mr-4">
        <div class="step-circle inactive cursor-pointer" onclick="goToStep(3)">3</div>
      </div>
      <div class="flex items-center mr-4">
        <div class="step-circle active cursor-pointer" onclick="goToStep(4)">4</div>
      </div>
                  <div class="flex items-center">
          <div class="step-circle inactive cursor-pointer" onclick="goToStep(5)">5</div>
        </div>
      <div class="ml-4">Step 4 - Buyers List</div>
    </div>

    <div class="mb-6">
      <div class="flex items-start mb-4">
        <i data-lucide="layout" class="w-5 h-5 mr-2 text-green-600"></i>
        <span class="font-medium">Buyers List</span>
      </div>

      <!-- Unit Details Form Section -->
      <div class="bg-gray-50 p-4 rounded-md mb-6">
        <h3 class="font-medium mb-4">Add list of buyer</h3>

        <div x-data="{ buyers: {{ old('records') ? json_encode(old('records')) : '[{}]' }} }">
              <div>
                <template x-for="(buyer, index) in buyers" :key="index">
                  <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white">
                    <div class="flex justify-between items-start mb-4">
                      <h4 class="text-sm font-medium text-gray-700">Buyer <span x-text="index + 1"></span></h4>
                      <button type="button" @click="buyers.splice(index, 1)"
                        x-show="buyers.length > 1"
                        class="bg-red-500 text-white p-1.5 rounded-md hover:bg-red-600 flex items-center justify-center">
                        <i data-lucide="x" class="w-4 h-4"></i>
                      </button>
                    </div>
                    
                    <!-- Buyer Name Fields -->
                    <div class="grid grid-cols-4 gap-4 mb-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Title <span class="text-red-500">*</span>
                        </label>
                        <select :name="'records[' + index + '][buyerTitle]'"
                          class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm uppercase"
                          x-bind:value="buyer.buyerTitle || ''" required>
                          <option value="">Select title</option>
                          <option value="Mr." x-bind:selected="buyer.buyerTitle == 'Mr.'">Mr.</option>
                          <option value="Mrs." x-bind:selected="buyer.buyerTitle == 'Mrs.'">Mrs.</option>
                          <option value="Chief" x-bind:selected="buyer.buyerTitle == 'Chief'">Chief</option>
                          <option value="Master" x-bind:selected="buyer.buyerTitle == 'Master'">Master</option>
                          <option value="Capt" x-bind:selected="buyer.buyerTitle == 'Capt'">Capt</option>
                          <option value="Coln" x-bind:selected="buyer.buyerTitle == 'Coln'">Coln</option>
                          <option value="Pastor" x-bind:selected="buyer.buyerTitle == 'Pastor'">Pastor</option>
                          <option value="King" x-bind:selected="buyer.buyerTitle == 'King'">King</option>
                          <option value="Prof" x-bind:selected="buyer.buyerTitle == 'Prof'">Prof</option>
                          <option value="Dr." x-bind:selected="buyer.buyerTitle == 'Dr.'">Dr.</option>
                          <option value="Alhaji" x-bind:selected="buyer.buyerTitle == 'Alhaji'">Alhaji</option>
                          <option value="Alhaja" x-bind:selected="buyer.buyerTitle == 'Alhaja'">Alhaja</option>
                          <option value="High Chief" x-bind:selected="buyer.buyerTitle == 'High Chief'">High Chief</option>
                          <option value="Lady" x-bind:selected="buyer.buyerTitle == 'Lady'">Lady</option>
                          <option value="Bishop" x-bind:selected="buyer.buyerTitle == 'Bishop'">Bishop</option>
                          <option value="Senator" x-bind:selected="buyer.buyerTitle == 'Senator'">Senator</option>
                          <option value="Messr" x-bind:selected="buyer.buyerTitle == 'Messr'">Messr</option>
                          <option value="Honorable" x-bind:selected="buyer.buyerTitle == 'Honorable'">Honorable</option>
                          <option value="Miss" x-bind:selected="buyer.buyerTitle == 'Miss'">Miss</option>
                          <option value="Rev." x-bind:selected="buyer.buyerTitle == 'Rev.'">Rev.</option>
                          <option value="Barr." x-bind:selected="buyer.buyerTitle == 'Barr.'">Barr.</option>
                          <option value="Arc." x-bind:selected="buyer.buyerTitle == 'Arc.'">Arc.</option>
                          <option value="Sister" x-bind:selected="buyer.buyerTitle == 'Sister'">Sister</option>
                          <option value="Other" x-bind:selected="buyer.buyerTitle == 'Other'">Other</option>
                        </select>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" :name="'records[' + index + '][firstName]'"
                          class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm uppercase"
                          placeholder="Enter First Name" required
                          x-bind:value="buyer.firstName || ''"
                          oninput="this.value = this.value.toUpperCase()">
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Middle Name (Optional)
                        </label>
                        <input type="text" :name="'records[' + index + '][middleName]'"
                          class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm uppercase"
                          placeholder="Enter Middle Name"
                          x-bind:value="buyer.middleName || ''"
                          oninput="this.value = this.value.toUpperCase()">
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Surname <span class="text-red-500">*</span>
                        </label>
                        <input type="text" :name="'records[' + index + '][surname]'"
                          class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm uppercase"
                          placeholder="Enter Surname" required
                          x-bind:value="buyer.surname || ''"
                          oninput="this.value = this.value.toUpperCase()">
                      </div>
                    </div>
                    
                    <!-- Unit Details -->
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Unit No <span class="text-red-500">*</span>
                        </label>
                        <input type="text" :name="'records[' + index + '][unit_no]'"
                          class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm uppercase"
                          placeholder="Enter Unit No" required
                          x-bind:value="buyer.unit_no || ''"
                          oninput="this.value = this.value.toUpperCase()">
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Unit Measurement (m²) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" :name="'records[' + index + '][unitMeasurement]'"
                          class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                          placeholder="Enter Measurement" required min="0"
                          x-bind:value="buyer.unitMeasurement || ''">
                      </div>
                    </div>
                    
                    <!-- Hidden field for full buyer name -->
                    <input type="hidden" :name="'records[' + index + '][buyerName]'" 
                           :value="[buyer.buyerTitle, buyer.firstName, buyer.middleName, buyer.surname].filter(name => name && name.trim()).join(' ')">
                  </div>
                </template>
              </div>

              <button type="button" @click="buyers.push({}); reinitializeIcons()"
                class="flex items-center px-3 py-1.5 text-xs bg-blue-500 text-white rounded-md hover:bg-blue-600 mt-2">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Add More
              </button>

              <hr class="my-4">
        </div>


        <div class="flex justify-between mt-8">
          <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md"
            id="backStep4">Back</button>
          <div class="flex items-center">
            <span class="text-sm text-gray-500 mr-4">Step 4 of 5</span>
            <button type="button" class="px-4 py-2 bg-black text-white rounded-md"
              id="nextStep4">Next</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
      
      // Watch for Alpine.js changes and reinitialize Lucide icons
      document.addEventListener('alpine:initialized', () => {
        // Get the Alpine data component for buyers
        const buyersComponent = Alpine.data('{ buyers: [{}] }');
        
        // Create a mutation observer to watch for DOM changes
        const observer = new MutationObserver(() => {
          if (typeof lucide !== 'undefined') {
            lucide.createIcons();
          }
        });
        
        // Start observing the container with the buyers list
        observer.observe(document.querySelector('[x-data]'), { 
          childList: true, 
          subtree: true 
        });
      });
    });
    
    // Function to reinitialize icons after adding new buyer
    function reinitializeIcons() {
      if (typeof lucide !== 'undefined') {
        setTimeout(() => lucide.createIcons(), 10);
      }
    }
  </script>
</div>