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
      border: 1px solid transparent;
    }
    
    .badge-approved {
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      color: #065f46;
      border-color: #10b981;
    }
    
    .badge-pending {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      color: #92400e;
      border-color: #f59e0b;
    }
     .badge-pending2 {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      color: #920e41;
      border-color: #a50427;
    }
    
    .badge-declined {
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      color: #991b1b;
      border-color: #ef4444;
    }
    
    .badge-awaiting {
      background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
      color: #3730a3;
      border-color: #6366f1;
    }

    /* Enhanced Table Styles */
    .primary-table {
      border: 2px solid #3b82f6;
      border-radius: 0.75rem;
      overflow: hidden;
      box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1), 0 2px 4px -1px rgba(59, 130, 246, 0.06);
    }
    
    .unit-table {
      border: 2px solid #8b5cf6;
      border-radius: 0.75rem;
      overflow: hidden;
      box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.1), 0 2px 4px -1px rgba(139, 92, 246, 0.06);
    }

    .primary-table .table-header {
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
      color: white;
      font-weight: 600;
      text-align: left;
      padding: 1rem;
      border-bottom: 2px solid #1e40af;
      position: relative;
    }
    
    .unit-table .table-header {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
      color: white;
      font-weight: 600;
      text-align: left;
      padding: 1rem;
      border-bottom: 2px solid #6d28d9;
      position: relative;
    }

    .primary-table .table-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #60a5fa, #3b82f6, #1d4ed8);
    }
    
    .unit-table .table-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #a78bfa, #8b5cf6, #7c3aed);
    }

    .table-cell {
      padding: 0.875rem 1rem;
      border-bottom: 1px solid #e5e7eb;
      transition: background-color 0.2s ease;
    }
    
    .primary-table tbody tr:hover {
      background-color: #eff6ff;
    }
    
    .unit-table tbody tr:hover {
      background-color: #f3f4f6;
    }

    /* Enhanced Tab Styles */
    .tab-primary {
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
      border: 2px solid #1e40af;
      box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    
    .tab-primary:hover {
      background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.4);
    }
    
    .tab-unit {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
      border: 2px solid #6d28d9;
      box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3);
    }
    
    .tab-unit:hover {
      background: linear-gradient(135deg, #7c3aed 0%, #6b21a8 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 8px -1px rgba(139, 92, 246, 0.4);
    }

    /* Enhanced Stats Cards */
    .stat-card {
      background: white;
      border-radius: 0.75rem;
      padding: 1.5rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      border: 1px solid #e5e7eb;
      transition: all 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Section Headers */
    .section-header {
      position: relative;
      padding: 1rem 1.5rem;
      margin-bottom: 1rem;
      border-radius: 0.75rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .section-header-primary {
      background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
      border: 2px solid #3b82f6;
      color: #1e40af;
    }
    
    .section-header-unit {
      background: linear-gradient(135deg, #e9d5ff 0%, #ddd6fe 100%);
      border: 2px solid #8b5cf6;
      color: #6b21a8;
    }

    /* Filter Enhancements */
    .filter-container {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 0.5rem;
      padding: 1rem;
      margin-bottom: 1rem;
    }

    /* Action Button Enhancements */
    .action-menu {
      min-width: 200px;
      border-radius: 0.5rem;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .action-menu li a:hover {
      background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    }

    /* Responsive Improvements */
    @media (max-width: 768px) {
      .table-cell {
        padding: 0.5rem;
        font-size: 0.75rem;
      }
      
      .section-header {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
      }
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
    
    // Reset primary tab
    primaryTab.classList.remove('tab-primary', 'text-white');
    primaryTab.classList.add('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
    
    // Reset unit tab
    unitTab.classList.remove('tab-unit', 'text-white');
    unitTab.classList.add('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
    
    // Show selected tab content
    document.getElementById(tabId).classList.remove('hidden');
    
    // Highlight active tab button
    if (tabId === 'primary-survey') {
      primaryTab.classList.remove('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
      primaryTab.classList.add('tab-primary', 'text-white');
    } else if (tabId === 'unit-survey') {
      unitTab.classList.remove('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
      unitTab.classList.add('tab-unit', 'text-white');
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

    <!-- Enhanced Tab Navigation -->
    <div class="flex space-x-4 mb-6">
        <button 
        onclick="showTab('primary-survey')"
        id="primary-survey-tab"
        class="tab-primary flex items-center px-6 py-3 text-sm font-semibold rounded-xl shadow-lg transition-all duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-300 text-white"
        >
        <i data-lucide="home" class="w-5 h-5 mr-2"></i>
        <span>Primary Applications</span>
        <span class="ml-2 bg-white bg-opacity-20 text-white text-xs px-2 py-1 rounded-full">{{ $totalPrimaryApplications }}</span>
        </button>
        <button 
        onclick="showTab('unit-survey')"
        id="unit-survey-tab"
        class="tab-unit flex items-center px-6 py-3 text-sm font-semibold rounded-xl shadow-lg transition-all duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-purple-300 bg-white text-gray-700 hover:bg-gray-50 border-2 border-gray-200"
        >
        <i data-lucide="layers" class="w-5 h-5 mr-2"></i>
        <span>Unit Applications</span>
        <span class="ml-2 bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">{{ $totalUnitApplications }}</span>
        </button>
    </div>  

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-600 font-medium">Total Director's Approvals</h3>
            <i data-lucide="file-text" class="text-gray-400 w-5 h-5"></i>
        </div>
        <div class="text-3xl font-bold">{{ $totalPrimaryApplications + $totalUnitApplications }}</div>
        <div class="flex items-center mt-2 text-sm">
            <i data-lucide="info" class="text-blue-500 w-4 h-4 mr-1"></i>
            <span class="text-blue-500">All Applications in system </span>
        </div>
        <span class="text-xs italic">(Primary + Unit Applications)</span>
        </div>
        
        <div class="stat-card">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-600 font-medium">Primary Applications</h3>
        
            <i data-lucide="home" class="text-gray-400 w-5 h-5"></i>
        </div>
        <div class="text-3xl font-bold">{{ $totalPrimaryApplications }}</div>
        <div class="flex items-center mt-2 text-sm">
            <i data-lucide="check-circle" class="text-green-500 w-4 h-4 mr-1"></i>
            <span class="text-green-500">{{ $approvedPrimaryApplications }} Approved</span>
            <i data-lucide="x-circle" class="text-red-500 w-4 h-4 ml-3 mr-1"></i>
            <span class="text-red-500">{{ $rejectedPrimaryApplications }} Declined</span>
            <i data-lucide="clock" class="text-amber-500 w-4 h-4 ml-3 mr-1"></i>
            <span class="text-amber-500">{{ $pendingPrimaryApplications ?? ($totalPrimaryApplications - $approvedPrimaryApplications - $rejectedPrimaryApplications) }} Pending</span>
        </div>
        </div>

        <div class="stat-card">
            <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-600 font-medium">Unit Applications</h3>
            <i data-lucide="layers" class="text-gray-400 w-5 h-5"></i>
            </div>
            <div class="text-3xl font-bold">{{ $totalUnitApplications }}</div>
            <div class="flex items-center mt-2 text-sm">
            <i data-lucide="check-circle" class="text-green-500 w-4 h-4 mr-1"></i>
            <span class="text-green-500">{{ $approvedUnitApplications }} Approved</span>
            <i data-lucide="x-circle" class="text-red-500 w-4 h-4 ml-3 mr-1"></i>
            <span class="text-red-500">{{ $rejectedUnitApplications }} Declined</span>
            <i data-lucide="clock" class="text-amber-500 w-4 h-4 ml-3 mr-1"></i>
            <span class="text-amber-500">{{ $pendingUnitApplications ?? ($totalUnitApplications - $approvedUnitApplications - $rejectedUnitApplications) }} Pending</span>
            </div>
        </div>
    </div>

    <!-- Primary Application  -->
    <div id="primary-survey">
        @include('programmes.partials.director_report')
        
        <!-- Section Header for Primary Applications -->
        <div class="section-header section-header-primary">
            <i data-lucide="home" class="w-6 h-6"></i>
            <div>
                <h3 class="text-lg font-bold">Primary Applications - Director's Approval</h3>
                <p class="text-sm opacity-80">Original property applications requiring director approval</p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg border-2 border-blue-200 p-6">
            <div class="filter-container">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                        <span class="font-medium text-gray-700">Filter & Actions</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <select id="primaryStatusFilter" class="pl-4 pr-8 py-2 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none bg-white">
                                <option>All...</option>
                                <option>Approved</option>
                                <option>Pending</option>
                                <option>Awaiting Planning Rec</option>
                                <option>Declined</option>
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-400 w-4 h-4"></i>
                        </div>
                        
                        <button class="flex items-center space-x-2 px-4 py-2 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                            <i data-lucide="upload" class="w-4 h-4 text-blue-600"></i>
                            <span class="text-blue-600">Import</span>
                        </button>
                        
                        <button class="flex items-center space-x-2 px-4 py-2 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                            <i data-lucide="download" class="w-4 h-4 text-blue-600"></i>
                            <span class="text-blue-600">Export</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table id="primaryApplicationTable" class="primary-table min-w-full">
                  <thead>
                     <tr class="text-sm">
                        <th class="table-header">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                <span>File No</span>
                            </div>
                        </th>
                        <th class="table-header">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                <span>Owner</span>
                            </div>
                        </th>
                        <th class="table-header">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="activity" class="w-4 h-4"></i>
                                <span>Status</span>
                            </div>
                        </th>
                        <th class="table-header">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span>Approval Date</span>
                            </div>
                        </th>
                        <th class="table-header">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="message-square" class="w-4 h-4"></i>
                                <span>Comment</span>
                            </div>
                        </th>
                        <th class="table-header">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="settings" class="w-4 h-4"></i>
                                <span>Actions</span>
                            </div>
                        </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applications as $application)
                     <tr class="text-xs">
                        <td class="table-cell">{{ $application->fileno ?? 'N/A' }}</td>
                        <td class="table-cell">{{ $application->owner_name ?? 'N/A' }}</td>
                        <td class="table-cell">
                            @if($application->application_status == 'Approved')
                                <span class="badge badge-approved">Approved</span>
                            @elseif($application->application_status == 'Declined')
                                <span class="badge badge-declined">Declined</span>
                            @elseif($application->planning_recommendation_status != 'Approved')
                                <span class="badge badge-pending">Awaiting Planning Rec</span>
                            @else
                                <span class="badge" style=" background: linear-gradient(135deg, #fef3c7 0%, #b92323 100%);
      color: #920e41;
      border-color: #a50427;">Pending</span>
                            @endif
                        </td> 
                        <td class="table-cell">
                            
                                 
                           {{ $application->approval_date ?? 'N/A' }}
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
                          <li>
                            @if($application->planning_recommendation_status == 'Approved' && $application->application_status != 'Approved')
                              <a href="{{ route('actions.director-approval', ['id' => $application->id]) }}" 
                                 class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-blue-500"></i>
                                <span>Approve/Decline</span>
                              </a>
                            @else
                              <div class="block w-full text-left px-4 py-2 flex items-center space-x-2 cursor-not-allowed opacity-50">
                                <i data-lucide="check-circle" class="w-4 h-4 text-gray-400"></i>
                                <span>Approve/Decline</span>
                              </div>
                            @endif
                          </li>
                             <li>
                                <a href="{{ $application->application_status == 'Approved' ? route('actions.director-approval', ['id' => $application->id]) : '#' }}" 
                                   class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2 {{ $application->application_status != 'Approved' ? 'cursor-not-allowed opacity-50' : '' }}">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                                    <span>View approval</span>
                                </a>
                            </li>
                    
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
       
        @include('programmes.partials.unit_director_report')
        
        <!-- Section Header for Unit Applications -->
        <div class="section-header section-header-unit">
            <i data-lucide="layers" class="w-6 h-6"></i>
            <div>
                <h3 class="text-lg font-bold">Unit Applications - Director's Approval</h3>
                <p class="text-sm opacity-80">Sectional titling unit applications requiring director approval</p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg border-2 border-purple-200 p-6">
            <div class="filter-container">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="filter" class="w-5 h-5 text-purple-600"></i>
                        <span class="font-medium text-gray-700">Filter & Actions</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <select id="unitStatusFilter" class="pl-4 pr-8 py-2 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 appearance-none bg-white">
                                <option>All...</option>
                                <option>Approved</option>
                                <option>Pending</option>
                                <option>Awaiting Planning Rec</option>
                                <option>Declined</option>
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-purple-400 w-4 h-4"></i>
                        </div>
                        
                        <button class="flex items-center space-x-2 px-4 py-2 border border-purple-300 rounded-lg hover:bg-purple-50 transition-colors">
                            <i data-lucide="upload" class="w-4 h-4 text-purple-600"></i>
                            <span class="text-purple-600">Import</span>
                        </button>
                        
                        <button class="flex items-center space-x-2 px-4 py-2 border border-purple-300 rounded-lg hover:bg-purple-50 transition-colors">
                            <i data-lucide="download" class="w-4 h-4 text-purple-600"></i>
                            <span class="text-purple-600">Export</span>
                        </button>
                    </div>
                </div>
            </div>
        
        <div class="overflow-x-auto">
            <table id="unitApplicationTable" class="unit-table min-w-full">
              <thead>
                 <tr class="text-sm">
                  <th class="table-header">
                      <div class="flex items-center space-x-2">
                          <i data-lucide="file-text" class="w-4 h-4"></i>
                          <span>File No</span>
                      </div>
                  </th>
                  <th class="table-header">
                      <div class="flex items-center space-x-2">
                          <i data-lucide="user" class="w-4 h-4"></i>
                          <span>Owner</span>
                      </div>
                  </th>
                  <th class="table-header">
                      <div class="flex items-center space-x-2">
                          <i data-lucide="activity" class="w-4 h-4"></i>
                          <span>Status</span>
                      </div>
                  </th>
                  <th class="table-header">
                      <div class="flex items-center space-x-2">
                          <i data-lucide="calendar" class="w-4 h-4"></i>
                          <span>Approval Date</span>
                      </div>
                  </th>
                  <th class="table-header">
                      <div class="flex items-center space-x-2">
                          <i data-lucide="message-square" class="w-4 h-4"></i>
                          <span>Comment</span>
                      </div>
                  </th>
                  <th class="table-header">
                      <div class="flex items-center space-x-2">
                          <i data-lucide="settings" class="w-4 h-4"></i>
                          <span>Actions</span>
                      </div>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @forelse($unitApplications as $unitApplication)
                 <tr class="text-xs">
                  <td class="table-cell">{{ $unitApplication->fileno ?? 'N/A' }}</td>
                  <td class="table-cell">{{ $unitApplication->owner_name ?? 'N/A' }}</td>
                  <td class="table-cell">
                    @if($unitApplication->application_status == 'Approved')
                      <span class="badge badge-approved">Approved</span>
                    @elseif($unitApplication->application_status == 'Declined')
                      <span class="badge badge-declined">Declined</span>
                    @elseif($unitApplication->planning_recommendation_status != 'Approved')
                      <span class="badge badge-pending">Awaiting Planning Rec</span>
                    @else
                      <span class="badge" style=" background: linear-gradient(135deg, #fef3c7 0%, #b92323 100%);
      color: #920e41;
      border-color: #a50427;">Pending</span>
                    @endif
                  </td>
                <td class="table-cell">
                  {{ $unitApplication->approval_date ?? 'N/A' }}
                   
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
                      <li>
                        @if($unitApplication->planning_recommendation_status == 'Approved' && $unitApplication->application_status != 'Approved')
                          <a href="{{ route('sub-actions.director-approval', ['id' => $unitApplication->id]) }}" 
                             class="block w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-blue-500"></i>
                            <span>Approve/Decline</span>
                          </a>
                        @else
                          <div class="block w-full text-left px-4 py-2 flex items-center space-x-2 cursor-not-allowed opacity-50">
                            <i data-lucide="check-circle" class="w-4 h-4 text-gray-400"></i>
                            <span>Approve/Decline</span>
                          </div>
                        @endif
                      </li>
                      <li>
                        <a href="{{ $unitApplication->application_status == 'Approved' ? route('sub-actions.director-approval', ['id' => $unitApplication->id]) : '#' }}" 
                           class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2 {{ $unitApplication->application_status != 'Approved' ? 'cursor-not-allowed opacity-50' : '' }}">
                          <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                          <span>View approval</span>
                        </a>
                      </li>
                     
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


