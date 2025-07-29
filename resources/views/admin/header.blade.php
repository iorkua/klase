<div class="p-6 bg-white border-b border-gray-200">
  <div class="flex justify-between items-center">
    <div>
      <h1 class="text-2xl font-bold">{{ $PageTitle ?? 'Confirmation Of Instrument Registration' }}</h1>
      <p class="text-gray-500">{{ $PageDescription ?? 'Default page description' }}</p>
    </div>
    <div class="flex items-center space-x-4">
      <!-- Back Button -->
      <button type="button"
        onclick="window.history.back()"
        class="flex items-center px-3 py-2 border border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 text-gray-700"
        title="Go Back">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Back
      </button>
      <div class="relative">
        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
        <input
        type="text"
        placeholder="Search applications..."
        class="pl-10 pr-4 py-2 border border-gray-200 rounded-md w-64 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div class="relative">
        <i data-lucide="bell" class="w-5 h-5"></i>
        <span class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
        2
        </span>
      </div>
      
      <!-- User Profile Dropdown -->
      <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center focus:outline-none" type="button">
          <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-200 flex items-center justify-center bg-gray-100">
            @if(Auth::user()->profile)
              <img src="{{ asset('storage/app/public/'.auth()->user()->profile) }}" alt="Profile" class="w-full h-full object-cover">
            @else
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            @endif
          </div>
          <span class="ml-2 text-gray-700 hidden md:block">{{ Auth::user()->first_name ?? Auth::user()->name }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             x-cloak
             class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 z-50">
          
          <div class="px-4 py-3">
            <p class="text-sm leading-5">Signed in as</p>
            <p class="text-sm font-medium leading-5 text-gray-900 truncate">{{ Auth::user()->email }}</p>
          </div>
          
          <div class="py-1">
            <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
              </svg>
              My Profile
            </a>
          </div>
          
          <div class="py-1">
            <form method="POST" action="{{ route('logout') }}" id="autoLogoutForm">
              @csrf
              <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V7.414a1 1 0 00-.293-.707L11.414 2.414A1 1 0 0010.707 2H4a1 1 0 00-1 1zm9 2.5V5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5V5.5zm0 7V10a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2.5z" clip-rule="evenodd" />
                </svg>
                Logout
              </button>
            </form>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>

<!-- Include Alpine.js for dropdown functionality -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>

<!-- Tailwind CDN must come BEFORE our configuration -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
  // Simple flag to force popup to show - for testing
  const FORCE_SHOW_POPUP = false;
  
  // Configure Tailwind - this must come AFTER the CDN import
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: {
            red: '#D42E12',
            green: '#107C41',
            yellow: '#FFBA08',
            black: '#212121'
          }
        }
      }
    }
  }
</script>
<style>
  /* Custom colors as direct CSS variables for fallback */
  :root {
    --brand-red: #D42E12;
    --brand-green: #107C41;
    --brand-yellow: #FFBA08;
    --brand-black: #212121;
  }
  
  /* Apply fallback styles using the CSS variables */
  .bg-brand-green-fallback {
    background-color: var(--brand-green) !important;
  }
  .text-brand-green-fallback {
    color: var(--brand-green) !important;
  }
  .hover-brand-green-fallback:hover {
    background-color: rgba(16, 124, 65, 0.9) !important;
  }
  
  @keyframes flash {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
  }
  .flash-text {
    animation: flash 1.8s infinite;
  }
  .popup-overlay {
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.4s ease, visibility 0.4s ease;
  }
  .popup-overlay.active {
    opacity: 1;
    visibility: visible;
  }
  .popup-content {
    transform: scale(0.9);
    transition: transform 0.4s ease;
  }
  .popup-overlay.active .popup-content {
    transform: scale(1);
  }
  .brand-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
    width: 60px;
    height: 60px;
  }
</style>
<!-- Welcome Popup -->
<div id="welcomePopup" class="popup-overlay fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" style="display: none; background: linear-gradient(to bottom, rgba(0, 120, 212, 0.658), rgba(0, 104, 55, 0.8));">
  <div class="popup-content bg-white rounded-xl shadow-2xl w-11/12 max-w-md mx-auto overflow-hidden">
    <!-- Brand Logos Header -->
    <div class="flex justify-center items-center space-x-6 pt-4">
      <img src="{{ asset('storage/upload/logo/logo.png') }}" alt="KLAES Logo" class="h-12">
      <img src="http://klas.com.ng/storage/uploads/logo.jpeg" alt="LAAD-Sys Logo" class="h-12">
    </div>
    
     
    <!-- Popup Content -->
    <div class="p-6">
      <div class="mb-8 text-center">
        <div class="flex justify-center mb-4">
          <div class="w-16 h-1 rounded-full" style="background: linear-gradient(to right, #D42E12, #FFBA08, #107C41);"></div>
        </div>
        
        <p class="text-gray-600 mb-4">We're excited to have you here!</p>
        
        <div class="flash-text py-3 px-4 rounded-lg" style="background: linear-gradient(to right, rgba(212,46,18,0.1), rgba(255,186,8,0.1), rgba(16,124,65,0.1));">
          <h3 class="text-2xl md:text-3xl font-extrabold" style="color: #212121;">
            WELCOME TO KLAES
          </h3>
          <p class="text-xl md:text-2xl font-bold mt-2">
            Dear <span id="username" class="text-brand-green-fallback" style="color: #107C41;">USERNAME</span>
          </p>
        </div>
      </div>
      
      <div class="space-y-3">
        <button id="continueBtn" class="w-full bg-brand-green bg-brand-green-fallback hover-brand-green-fallback text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-opacity-50 transition" style="background-color: #107C41;">
          Continue to Site
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // Simple flag to control popup visibility for testing purposes
  const TEST_POPUP_ENABLED = false; // Change to false to disable the popup for testing
  
  // Add this line to force execution - ensures the script runs
  console.log('Welcome popup script loaded');
  
  // Function to toggle welcome popup on/off for testing
  function toggleWelcomePopup(show) {
    const popup = document.getElementById('welcomePopup');
    if (!popup) return;
    
    if (show) {
      popup.style.display = 'flex';
      setTimeout(() => {
        popup.classList.add('active');
      }, 10);
    } else {
      popup.classList.remove('active');
      setTimeout(() => {
        popup.style.display = 'none';
      }, 400);
    }
    console.log('Welcome popup toggled:', show ? 'ON' : 'OFF');
  }
  
  // Wait for DOM to be fully loaded
  window.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    // Get elements
    const popup = document.getElementById('welcomePopup');
    const closeBtn = document.getElementById('closePopup');
    const continueBtn = document.getElementById('continueBtn');
    const learnMoreBtn = document.getElementById('learnMoreBtn');
    const usernameSpan = document.getElementById('username');
    
    // Get the username from PHP
    const username = "{{ Auth::user()->first_name ?? Auth::user()->name ?? Auth::user()->email ?? 'User' }}";
    console.log('Current user:', username);
    
    // Set the username in the popup
    if (usernameSpan) {
      usernameSpan.textContent = username;
    }
    
    // Function to show popup
    function showPopup() {
      console.log('Showing popup');
      popup.style.display = 'flex';
      // Add active class after a small delay to trigger animation
      setTimeout(() => {
        popup.classList.add('active');
      }, 10);
      
      // Mark as shown in server-side session via AJAX
      fetch("{{ route('markWelcomePopupShown') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      }).then(response => {
        console.log('Marked popup as shown in session');
      });
    }
    
    // Function to hide popup
    function hidePopup() {
      console.log('Hiding popup');
      popup.classList.remove('active');
      // Remove from DOM after animation completes
      setTimeout(() => {
        popup.style.display = 'none';
      }, 400);
    }
    
    // Add event listeners
    if (closeBtn) closeBtn.addEventListener('click', hidePopup);
    if (continueBtn) continueBtn.addEventListener('click', hidePopup);
    
    // Handle learn more button
    if (learnMoreBtn) {
      learnMoreBtn.addEventListener('click', function() {
        alert('This would take you to more information about KLAES.');
      });
    }
    
    // Close when clicking outside
    popup.addEventListener('click', function(e) {
      if (e.target === popup) hidePopup();
    });
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && popup.classList.contains('active')) {
        hidePopup();
      }
    });
    
    // Check if we should show popup - based on server-side session or testing flag
    const shouldShowPopup = TEST_POPUP_ENABLED || {{ session('show_welcome_popup', true) ? 'true' : 'false' }};
    console.log('Should show popup:', shouldShowPopup);
    
    if (FORCE_SHOW_POPUP || shouldShowPopup) {
      console.log('Will show popup');
      // Small delay to ensure everything is loaded
      setTimeout(showPopup, 500);
    } else {
      console.log('Popup will not be shown');
    }
  });
  
  // Reset popup state on logout
  const logoutForm = document.getElementById('autoLogoutForm');
  if (logoutForm) {
    logoutForm.addEventListener('submit', function() {
      console.log('Logout detected, clearing popup state');
      sessionStorage.removeItem('welcomePopupShown');
    });
  }
</script>

{{-- Removed Auto Logout functionality:
     - Auto Logout Configuration and associated timers.
     - Auto Logout Toast markup and related scripts.
--}}

<!-- Set last login time in session -->
@php
    if (!session()->has('last_login_time')) {
        session(['last_login_time' => time()]);
    }
@endphp

<!-- Set welcome popup flag in session if not set -->
@php
    // Set welcome popup flag to true for first-time visits in a login session
    if (!session()->has('show_welcome_popup')) {
        session(['show_welcome_popup' => true]);
    }
@endphp
