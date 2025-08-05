@extends('layouts.app')

@section('page-title')
    {{ $PageTitle ?? __('KLAES') }}
@endsection

@section('styles')

@endsection

@section('content')
<style>
    /* Enhanced Badge Styles */
    .badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.375rem 0.75rem;
      border-radius: 0.5rem;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.025em;
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .badge-approved {
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      color: #047857;
      border: 1px solid #10b981;
    }
    
    
    .badge-approved2 {
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      color: #04397e;
      border: 1px solid #04397e;
    }
    .badge-pending {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      color: #b45309;
      border: 1px solid #f59e0b;
    }
    .badge-declined {
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      color: #b91c1c;
      border: 1px solid #ef4444;
    }

    /* Primary Application Styling */
    .primary-theme {
      --primary-color: #2563eb;
      --primary-light: #dbeafe;
      --primary-dark: #1d4ed8;
      --accent-color: #3b82f6;
    }

    .primary-card {
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      border: 2px solid #2563eb;
      border-radius: 12px;
      box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1), 0 2px 4px -1px rgba(37, 99, 235, 0.06);
    }

    .primary-header {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 10px 10px 0 0;
      border-bottom: 3px solid #1e40af;
    }

    .primary-table-header {
      background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
      font-weight: 600;
      color: #1e40af;
      text-align: left;
      padding: 1rem;
      border-bottom: 2px solid #3b82f6;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }

    /* Unit Application Styling */
    .unit-theme {
      --unit-color: #059669;
      --unit-light: #d1fae5;
      --unit-dark: #047857;
      --accent-color: #10b981;
    }

    .unit-card {
      background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
      border: 2px solid #059669;
      border-radius: 12px;
      box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.1), 0 2px 4px -1px rgba(5, 150, 105, 0.06);
    }

    .unit-header {
      background: linear-gradient(135deg, #059669 0%, #047857 100%);
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 10px 10px 0 0;
      border-bottom: 3px solid #065f46;
    }

    .unit-table-header {
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      font-weight: 600;
      color: #065f46;
      text-align: left;
      padding: 1rem;
      border-bottom: 2px solid #10b981;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }

    /* Enhanced Table Styling */
    .table-cell {
      padding: 1rem;
      border-bottom: 1px solid #e5e7eb;
      font-size: 0.875rem;
      transition: background-color 0.2s ease;
    }

    .table-row:hover .table-cell {
      background-color: #f8fafc;
    }

    /* Enhanced Tab Styling */
    .tab-primary {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: white;
      border: 2px solid #1e40af;
      box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
      transform: translateY(-2px);
    }

    .tab-primary:hover {
      background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
      box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.4);
    }

    .tab-unit {
      background: linear-gradient(135deg, #059669 0%, #047857 100%);
      color: white;
      border: 2px solid #065f46;
      box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.3);
      transform: translateY(-2px);
    }

    .tab-unit:hover {
      background: linear-gradient(135deg, #047857 0%, #064e3b 100%);
      box-shadow: 0 6px 8px -1px rgba(5, 150, 105, 0.4);
    }

    .tab-inactive {
      background: white;
      color: #6b7280;
      border: 2px solid #d1d5db;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .tab-inactive:hover {
      background: #f9fafb;
      border-color: #9ca3af;
    }

    /* Enhanced Stats Cards */
    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      border: 1px solid #e5e7eb;
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .primary-stat-card {
      border-left: 4px solid #2563eb;
    }

    .unit-stat-card {
      border-left: 4px solid #059669;
    }

    /* Enhanced Action Buttons */
    .action-button {
      transition: all 0.2s ease;
      border-radius: 8px;
      font-weight: 500;
    }

    .action-button:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Disabled States */
    .disabled-link {
        color: #9ca3af !important;
        cursor: not-allowed;
        pointer-events: none;
        opacity: 0.6;
    }
    .disabled-icon {
        color: #9ca3af !important;
    }

    /* Application Type Indicators */
    .app-type-indicator {
      position: absolute;
      top: -8px;
      right: -8px;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: bold;
      color: white;
    }

    .primary-indicator {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }

    .unit-indicator {
      background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    /* Enhanced Filter Styling */
    .filter-select {
      background: white;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      padding: 0.5rem 2.5rem 0.5rem 1rem;
      font-size: 0.875rem;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .filter-select:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<!-- Add the script at the beginning of the content section to ensure it's loaded before the buttons -->
<script>
  function showTab(tabId) {
    // Hide all tab contents
    document.getElementById('primary-survey').classList.add('hidden');
    document.getElementById('unit-survey').classList.add('hidden');
    
    // Reset all tab buttons to inactive state
    const primaryTab = document.getElementById('primary-survey-tab');
    const unitTab = document.getElementById('unit-survey-tab');
    
    // Remove all theme classes
    primaryTab.classList.remove('tab-primary', 'tab-unit');
    unitTab.classList.remove('tab-primary', 'tab-unit');
    
    // Add inactive class to both
    primaryTab.classList.add('tab-inactive');
    unitTab.classList.add('tab-inactive');
    
    // Update badge styling for inactive tabs
    const primaryBadge = primaryTab.querySelector('div:last-child');
    const unitBadge = unitTab.querySelector('div:last-child');
    
    primaryBadge.className = 'ml-2 bg-gray-200 px-2 py-1 rounded-full text-xs text-gray-600';
    unitBadge.className = 'ml-2 bg-gray-200 px-2 py-1 rounded-full text-xs text-gray-600';
    
    // Show selected tab content
    document.getElementById(tabId).classList.remove('hidden');
    
    // Highlight active tab button with appropriate theme
    const activeTab = document.getElementById(tabId + '-tab');
    activeTab.classList.remove('tab-inactive');
    
    if (tabId === 'primary-survey') {
      activeTab.classList.add('tab-primary');
      primaryBadge.className = 'ml-2 bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs';
    } else if (tabId === 'unit-survey') {
      activeTab.classList.add('tab-unit');
      unitBadge.className = 'ml-2 bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs';
    }
  }
  
  // Add dropdown toggle functionality
  function customToggleDropdown(button, event) {
    event.stopPropagation();
    
    // Close all other open dropdowns first
    const allMenus = document.querySelectorAll('.action-menu');
    allMenus.forEach(menu => {
      if (menu !== button.nextElementSibling && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
      }
    });
    
    // Toggle the clicked dropdown
    const menu = button.nextElementSibling;
    menu.classList.toggle('hidden');
    
    // Position the dropdown near the button
    if (!menu.classList.contains('hidden')) {
      const rect = button.getBoundingClientRect();
      menu.style.top = rect.bottom + 'px';
      menu.style.left = (rect.left - menu.offsetWidth + rect.width) + 'px';
    }
  }
  
  // Close dropdowns when clicking outside
  document.addEventListener('click', function(event) {
    const allMenus = document.querySelectorAll('.action-menu');
    allMenus.forEach(menu => {
      menu.classList.add('hidden');
    });
  });
  
  // Add table filtering functionality
  function filterTable(tableId, status) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    
    // Skip header row (index 0)
    for (let i = 1; i < rows.length; i++) {
      const statusCell = rows[i].getElementsByTagName('td')[2]; // Status is in the 3rd column
      
      if (statusCell) {
        const statusText = statusCell.textContent.trim();
        
        if (status === 'All...' || statusText.includes(status)) {
          rows[i].style.display = '';
        } else {
          rows[i].style.display = 'none';
        }
      }
    }
  }
  
  // Initialize filtering when the page loads
  document.addEventListener('DOMContentLoaded', function() {
    // Set up event listeners for the filter dropdowns
    const primaryFilter = document.getElementById('primaryStatusFilter');
    const unitFilter = document.getElementById('unitStatusFilter');
    
    if (primaryFilter) {
      primaryFilter.addEventListener('change', function() {
        filterTable('primaryApplicationTable', this.value);
      });
    }
    
    if (unitFilter) {
      unitFilter.addEventListener('change', function() {
        filterTable('unitApplicationTable', this.value);
      });
    }
  });
</script>

<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include($headerPartial ?? 'admin.header')
    
    <!-- Main Content -->
    <div class="p-6">
    <!-- Payments Overview -->
 
   <div class="grid grid-cols-3 gap-6 mb-8">
    <!-- Total Statistics Card -->
    <div class="stat-card relative overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-green-500"></div>
      <div class="flex justify-between items-start mb-4">
        <div>
          <h3 class="text-gray-700 font-semibold text-lg">Total Planning Recommendations</h3>
          <p class="text-gray-500 text-sm mt-1">Combined Primary & Unit Applications</p>
        </div>
        <div class="bg-gradient-to-br from-blue-100 to-green-100 p-3 rounded-full">
          <i data-lucide="file-text" class="text-blue-600 w-6 h-6"></i>
        </div>
      </div>
      <div class="text-4xl font-bold text-gray-800 mb-3">{{ $totalPrimaryApplications + $totalUnitApplications }}</div>
      <div class="flex items-center text-sm">
        <div class="flex items-center bg-blue-50 px-2 py-1 rounded-full mr-2">
          <i data-lucide="info" class="text-blue-600 w-4 h-4 mr-1"></i>
          <span class="text-blue-600 font-medium">System Overview</span>
        </div>
      </div>
    </div>
     
    <!-- Primary Applications Card -->
    <div class="stat-card primary-stat-card relative overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
      <div class="flex justify-between items-start mb-4">
        <div>
          <h3 class="text-gray-700 font-semibold text-lg">Primary Applications</h3>
          <p class="text-blue-600 text-sm mt-1 font-medium">Main Property Applications</p>
        </div>
        <div class="bg-blue-100 p-3 rounded-full relative">
          <i data-lucide="home" class="text-blue-600 w-6 h-6"></i>
          <div class="primary-indicator">P</div>
        </div>
      </div>
      <div class="text-4xl font-bold text-blue-700 mb-3">{{ $totalPrimaryApplications }}</div>
      <div class="grid grid-cols-3 gap-2 text-xs">
        <div class="flex items-center bg-green-50 px-2 py-1 rounded">
          <i data-lucide="check-circle" class="text-green-600 w-3 h-3 mr-1"></i>
          <span class="text-green-700 font-semibold">{{ $approvedPrimaryApplications }}</span>
        </div>
        <div class="flex items-center bg-red-50 px-2 py-1 rounded">
          <i data-lucide="x-circle" class="text-red-600 w-3 h-3 mr-1"></i>
          <span class="text-red-700 font-semibold">{{ $rejectedPrimaryApplications }}</span>
        </div>
        <div class="flex items-center bg-amber-50 px-2 py-1 rounded">
          <i data-lucide="clock" class="text-amber-600 w-3 h-3 mr-1"></i>
          <span class="text-amber-700 font-semibold">{{ $pendingPrimaryApplications ?? ($totalPrimaryApplications - $approvedPrimaryApplications - $rejectedPrimaryApplications) }}</span>
        </div>
      </div>
    </div>

    <!-- Unit Applications Card -->
    <div class="stat-card unit-stat-card relative overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
      <div class="flex justify-between items-start mb-4">
        <div>
          <h3 class="text-gray-700 font-semibold text-lg">Unit Applications</h3>
          <p class="text-green-600 text-sm mt-1 font-medium">Subdivision Unit Applications</p>
        </div>
        <div class="bg-green-100 p-3 rounded-full relative">
          <i data-lucide="layers" class="text-green-600 w-6 h-6"></i>
          <div class="unit-indicator">U</div>
        </div>
      </div>
      <div class="text-4xl font-bold text-green-700 mb-3">{{ $totalUnitApplications }}</div>
      <div class="grid grid-cols-3 gap-2 text-xs">
        <div class="flex items-center bg-green-50 px-2 py-1 rounded">
          <i data-lucide="check-circle" class="text-green-600 w-3 h-3 mr-1"></i>
          <span class="text-green-700 font-semibold">{{ $approvedUnitApplications }}</span>
        </div>
        <div class="flex items-center bg-red-50 px-2 py-1 rounded">
          <i data-lucide="x-circle" class="text-red-600 w-3 h-3 mr-1"></i>
          <span class="text-red-700 font-semibold">{{ $rejectedUnitApplications }}</span>
        </div>
        <div class="flex items-center bg-amber-50 px-2 py-1 rounded">
          <i data-lucide="clock" class="text-amber-600 w-3 h-3 mr-1"></i>
          <span class="text-amber-700 font-semibold">{{ $pendingUnitApplications ?? ($totalUnitApplications - $approvedUnitApplications - $rejectedUnitApplications) }}</span>
        </div>
      </div>
    </div>
   </div>
   
   <!-- Enhanced Tab Navigation -->
      <div class="flex space-x-4 mb-8">
        <button 
        onclick="showTab('primary-survey')"
        id="primary-survey-tab"
        class="flex items-center px-6 py-3 text-sm font-semibold rounded-xl transition-all duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-200 tab-primary"
        >
        <i data-lucide="home" class="w-5 h-5 mr-2"></i>
        <span>Primary Applications</span>
        <div class="ml-2 bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs">{{ $totalPrimaryApplications }}</div>
        </button>
        <button 
        onclick="showTab('unit-survey')"
        id="unit-survey-tab"
        class="flex items-center px-6 py-3 text-sm font-semibold rounded-xl transition-all duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-green-200 tab-inactive"
        >
        <i data-lucide="layers" class="w-5 h-5 mr-2"></i>
        <span>Unit Applications</span>
        <div class="ml-2 bg-gray-200 px-2 py-1 rounded-full text-xs text-gray-600">{{ $totalUnitApplications }}</div>
        </button>
    </div>  

      <!-- Primary Application  -->
      <div id="primary-survey">
        @include('programmes.partials.planning_report')
        <div  class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold">Planning Recommendation</h2>
                    <p class="text-sm text-gray-600 mt-1">Primary Application</p>
                  </div>
              <div class="flex items-center space-x-4">
                <div class="relative">
                  <select id="primaryStatusFilter" class="pl-4 pr-8 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                    <option>All...</option>
                    <option>Approved</option>
                    <option>Pending</option>
                    <option>Declined</option>
                  </select>
                  <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                </div>
                
                <button class="flex items-center space-x-2 px-4 py-2 border border-gray-200 rounded-md">
                  <i data-lucide="upload" class="w-4 h-4 text-gray-600"></i>
                  <span>Import</span>
                </button>
                
                <button class="flex items-center space-x-2 px-4 py-2 border border-gray-200 rounded-md">
                  <i data-lucide="download" class="w-4 h-4 text-gray-600"></i>
                  <span>Export</span>
                </button>
              </div>
            </div>
            
            <div class="overflow-x-auto">
                <table id="primaryApplicationTable" class="min-w-full divide-y divide-gray-200">
                  <thead>
                     <tr class="text-xs">
                        <th class="table-header">File No</th>
                        <th class="table-header">Owner</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Approval/Declined Date</th>
                        <th class="table-header">Comment</th>
                        <th class="table-header">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applications as $application)
                     <tr class="text-xs">
                        <td class="table-cell">
                          <div class="flex items-center space-x-2">
                             
                            
                              <span class="badge badge-approved2">{{ $application->fileno ?? 'N/A' }}</span>
                         
                          </div>
                         
                         
                        </td>
                        <td class="table-cell">{{ $application->owner_name ?? 'N/A' }}</td>
                        <td class="table-cell">
                            @if($application->planning_recommendation_status == 'Approved')
                                <span class="badge badge-approved">Approved</span>
                            @elseif($application->planning_recommendation_status == 'Declined')
                                <span class="badge badge-declined">Declined</span>
                            @else
                                <span class="badge badge-pending">Pending</span>
                            @endif
                        </td> 
                        <td class="table-cell">
                            @if($application->planning_approval_date)
                                {{ \Carbon\Carbon::parse($application->planning_approval_date)->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="table-cell">{{ $application->comments ?? 'N/A' }}</td>
                       
                    
                      <td class="table-cell relative">
                        <!-- Dropdown Toggle Button -->
                        <button type="button" class="p-2 hover:bg-gray-100 focus:outline-none rounded-full" onclick="customToggleDropdown(this, event)">
                          <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                        </button>
                        
                        <!-- Dropdown Menu Primary Application Surveys -->
                        <ul class="fixed action-menu z-50 bg-white border rounded-lg shadow-lg hidden w-56">
                          <li>
                            <a href="{{ route('sectionaltitling.viewrecorddetail')}}?id={{$application->id}}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                              <i data-lucide="eye" class="w-4 h-4 text-blue-600"></i>
                              <span>View Application</span>
                            </a>
                          </li>
                            @if(request()->query('url') != 'view')
                            <li>
                              @php
                              $fileExists = DB::connection('sqlsrv')
                                ->table('Cofo')
                                ->where('mlsFNo', $application->fileno)
                                ->orWhere('kangisFileNo', $application->fileno)
                                ->orWhere('NewKANGISFileno', $application->fileno)
                                ->exists();
                              @endphp

                              @if($application->planning_recommendation_status == 'Approved' || !$fileExists)
                              <div class="block w-full text-left px-4 py-2 flex items-center space-x-2 disabled-link">
                                <i data-lucide="check-circle" class="w-4 h-4 disabled-icon"></i>
                                <span>@if(!$fileExists) COFO Required @else Approve/Decline @endif</span>
                              </div>
                              @else
                              <a href="{{ route('actions.recommendation', ['id' => $application->id]) }}?url=phy_planning" class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-blue-600"></i>
                                <span>Approve/Decline</span>
                              </a>
                              @endif
                            </li>

                            <li>
                                @if($application->planning_recommendation_status == 'Approved')
                                <a href="{{ route('actions.recommendation', ['id' => $application->id]) }}?url=recommendation" class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                                    <span>View Planning Recommendation </span>
                                </a>
                                @else
                                <div class="block w-full text-left px-4 py-2 flex items-center space-x-2 disabled-link">
                                    <i data-lucide="check-circle" class="w-4 h-4 disabled-icon"></i>
                                    <span>View Planning Recommendation </span>
                                </div>
                                @endif
                            </li>
                          @endif
                            @if(request()->query('url') == 'view')
                          <li>
                            @if($application->planning_recommendation_status == 'Approved')
                            <a href="{{ route('actions.recommendation', ['id' => $application->id]) }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                              <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                              <span>View Planning Recommendation </span>
                            </a>
                            @else
                            <div class="block w-full text-left px-4 py-2 flex items-center space-x-2 disabled-link">
                              <i data-lucide="check-circle" class="w-4 h-4 disabled-icon"></i>
                              <span>View Planning Recommendation </span>
                            </div>
                            @endif
                          </li>
                               @endif
                        </ul>
                      </td>
                    </tr>
                    @empty
                     <tr class="text-xs">
                      <td colspan="11" class="table-cell text-center py-4 text-gray-500">No primary survey records found</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
          </div>
      </div>
     

      <!-- Unit Application  -->
    <div id="unit-survey" class="hidden">
        @include('programmes.partials.unit_planning_report')
      <div  class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h2 class="text-xl font-bold">Planning Recommendation</h2>
            <p class="text-sm text-gray-600 mt-1">Unit Application</p>
          </div>

          <div class="flex items-center space-x-4">
            <div class="relative">
              <select id="unitStatusFilter" class="pl-4 pr-8 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                <option>All...</option>
                <option>Approved</option>
                <option>Pending</option>
                <option>Declined</option>
              </select>
              <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
            </div>
            
         
            <button class="flex items-center space-x-2 px-4 py-2 border border-gray-200 rounded-md">
              <i data-lucide="download" class="w-4 h-4 text-gray-600"></i>
              <span>Export</span>
            </button>
          </div>
        </div>
        
        <div class="overflow-x-auto">
            <table id="unitApplicationTable" class="min-w-full divide-y divide-gray-200">
              <thead>
                 <tr class="text-xs">
                  <th class="table-header">File No</th>
                  <th class="table-header">Owner</th>
                  <th class="table-header">Status</th>
                  <th class="table-header">Approval/Declined Date</th>
                  <th class="table-header">Comment</th>
                  <th class="table-header">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @forelse($unitApplications as $unitApplication)
                 <tr class="text-xs">
                  <td class="table-cell">
                     <div class="flex items-center space-x-2">
                             
                            
                              <span class="badge badge-approved2"> {{ $unitApplication->fileno ?? 'N/A' }}</span>
                         
                          </div>
                          
                         </td>
                  <td class="table-cell">{{ $unitApplication->owner_name ?? 'N/A' }}</td>
                  <td class="table-cell">
                    @if($unitApplication->planning_recommendation_status == 'Approved')
                      <span class="badge badge-approved">Approved</span>
                    @elseif($unitApplication->planning_recommendation_status == 'Declined')
                      <span class="badge badge-declined">Declined</span>
                    @else
                      <span class="badge badge-pending">Pending</span>
                    @endif
                  </td>
                <td class="table-cell">
                    @if($unitApplication->planning_approval_date)
                        {{ \Carbon\Carbon::parse($unitApplication->planning_approval_date)->format('d/m/Y') }}
                    @else
                        N/A
                    @endif
                </td>
                  <td class="table-cell">{{ $unitApplication->comments ?? 'N/A' }}</td>
                  <td class="table-cell relative">
                    <!-- Dropdown Toggle Button -->
                    <button type="button" class="p-2 hover:bg-gray-100 focus:outline-none rounded-full" onclick="customToggleDropdown(this, event)">
                      <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                    </button>
                    
                    <!-- Dropdown Menu Unit Application Surveys -->
                    <ul class="fixed action-menu z-50 bg-white border rounded-lg shadow-lg hidden w-56">
                      <li>
                        <a href="{{ route('sectionaltitling.viewrecorddetail_sub', $unitApplication->id) }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                          <i data-lucide="eye" class="w-4 h-4 text-blue-600"></i>
                          <span>View Unit Application</span>
                        </a>
                      </li>
                      @if(request()->query('url') != 'view')
                      <li>
                        @if($unitApplication->planning_recommendation_status == 'Approved' || $unitApplication->planning_recommendation_status == 'Declined')
                        <div class="block w-full text-left px-4 py-2 flex items-center space-x-2 disabled-link">
                          <i data-lucide="check-circle" class="w-4 h-4 disabled-icon"></i>
                          <span>Approve/Decline</span>
                        </div>
                        @else
                        <a href="{{ route('sub-actions.recommendation', ['id' => $unitApplication->id]) }}?url=phy_planning" class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                          <i data-lucide="check-circle" class="w-4 h-4 text-blue-600"></i>
                          <span>Approve/Decline</span>
                        </a>
                        @endif
                      </li>

                      <li>  
                        @if($unitApplication->planning_recommendation_status == 'Approved')
                        <a href="{{ route('sub-actions.recommendation', ['id' => $unitApplication->id]) }}?url=recommendation" class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                          <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                          <span>View Planning Recommendation </span>
                        </a>
                        @else
                        <div class="block w-full text-left px-4 py-2 flex items-center space-x-2 disabled-link">
                          <i data-lucide="check-circle" class="w-4 h-4 disabled-icon"></i>
                          <span>View Planning Recommendation </span>
                        </div>
                        @endif
                      </li>
                      @endif
                       @if(request()->query('url') == 'view')
                      <li>  
                        @if($unitApplication->planning_recommendation_status == 'Approved')
                        <a href="{{ route('sub-actions.recommendation', ['id' => $unitApplication->id]) }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                          <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                          <span>View Planning Recommendation </span>
                        </a>
                        @else
                        <div class="block w-full text-left px-4 py-2 flex items-center space-x-2 disabled-link">
                          <i data-lucide="check-circle" class="w-4 h-4 disabled-icon"></i>
                          <span>View Planning Recommendation </span>
                        </div>
                        @endif
                      </li>
                      @endif
                    </ul>
                  </td>
                </tr>
                @empty
                 <tr class="text-xs">
                  <td colspan="6" class="table-cell text-center py-4 text-gray-500">No unit applications found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
      </div>
      </div>
    </div>
    
    <!-- Page Footer -->
    @include($footerPartial ?? 'admin.footer')
</div>
@endsection

@section('scripts')
@endsection