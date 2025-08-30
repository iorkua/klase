@php
   $isApproved = $PrimaryApplication->application_status === 'Approved' && 
              $PrimaryApplication->planning_recommendation_status === 'Approved';
@endphp

<div class="relative dropdown-container">
   <!-- Dropdown Toggle Button -->
   <button type="button" class="dropdown-toggle p-2 hover:bg-gray-100 focus:outline-none rounded-full" onclick="customToggleDropdown(this, event)">
     <i data-lucide="more-horizontal" class="w-5 h-5"></i>
   </button>
   <!-- Dropdown Menu -->
   <ul class="fixed action-menu z-50 bg-white border rounded-lg shadow-lg hidden w-56">

     @if($isApproved)
      @if(is_null($PrimaryApplication->final_conveyance_generated) || $PrimaryApplication->final_conveyance_generated == 0)
        <li>
         <a href="{{ route('actions.final-conveyance', ['id' => $PrimaryApplication->id]) }}" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
         <i data-lucide="file-text" class="w-4 h-4 text-orange-500"></i>
         <span>Generate Final Conveyance</span>
         </a>
        </li>
      @else
        <li class="opacity-50 cursor-not-allowed">
         <a href="#" class="w-full text-left px-4 py-2 flex items-center space-x-2" 
            title="Final Conveyance has already been generated">
         <i data-lucide="check-circle" class="w-4 h-4 text-gray-500"></i>
         <span>Generate Final Conveyance</span>
         </a>
        </li>
        
        <!-- View Final Conveyance option -->
        <li>
         <a href="{{ route('actions.final-conveyance', ['id' => $PrimaryApplication->id]) }}" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
         <i data-lucide="eye" class="w-4 h-4 text-blue-500"></i>
         <span>View Final Conveyance</span>
         </a>
        </li>
      @endif
     @else
      <li class="opacity-50 cursor-not-allowed">
        <a href="#" class="w-full text-left px-4 py-2 flex items-center space-x-2"
          title="Both Application Status and Planning Recommendation must be approved">
        <i data-lucide="file-text" class="w-4 h-4 text-gray-500"></i>
        <span>Generate Final Conveyance</span>
        </a>
      </li>
     @endif

   </ul>
 </div>
 <script>
   function customToggleDropdown(button, event) {
     event.stopPropagation();
     const currentDropdown = button.closest('.dropdown-container').querySelector('.action-menu');
     const isCurrentlyHidden = currentDropdown.classList.contains('hidden');
     
     // Close all other dropdowns first
     const allDropdowns = document.querySelectorAll('.action-menu');
     allDropdowns.forEach(dropdown => {
       dropdown.classList.add('hidden');
     });
     
     // If the current dropdown was hidden, show it
     if (isCurrentlyHidden) {
       currentDropdown.classList.remove('hidden');
       
       // Get button position
       const rect = button.getBoundingClientRect();
       
       // Position dropdown above the button
       currentDropdown.style.top = (rect.top - currentDropdown.offsetHeight - 5) + 'px';
       currentDropdown.style.left = (rect.left - currentDropdown.offsetWidth + rect.width) + 'px';
       
       // Check if dropdown would appear off the top of the screen
       if (rect.top - currentDropdown.offsetHeight < 0) {
         // If so, position it below the button instead
         currentDropdown.style.top = (rect.bottom + 5) + 'px';
       }
     }
   }
   
   // Close dropdown when clicking outside
   document.addEventListener('click', function (event) {
     const dropdowns = document.querySelectorAll('.action-menu');
     dropdowns.forEach(dropdown => {
       if (!dropdown.contains(event.target) && 
         !dropdown.closest('.dropdown-container').querySelector('.dropdown-toggle').contains(event.target)) {
         dropdown.classList.add('hidden');
       }
     });
   });
   </script>