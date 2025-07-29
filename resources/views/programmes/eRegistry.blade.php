@extends('layouts.app')

@section('page-title')
    {{ $PageTitle ?? __('KLAES') }}
@endsection

@section('styles')
 
@endsection

@section('content')
<style>
    .badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
      font-weight: 500;
    }
    .badge-approved {
      background-color: #d1fae5;
      color: #059669;
    }
    .badge-pending {
      background-color: #fef3c7;
      color: #d97706;
    }
    .badge-declined {
      background-color: #fee2e2;
      color: #dc2626;
    }
    .table-header {
      background-color: #f9fafb;
      font-weight: 500;
      color: rgb(13, 136, 13);
      text-align: left;
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #e5e7eb;
    }
    .table-cell {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #e5e7eb;
    }
    .tab-content {
      display: block;
    }
    .tab-content.hidden {
      display: none;
    }
    .status-completed {
      background-color: #d1fae5;
      color: #059669;
      padding: 0.25rem 0.75rem;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
    }
    .status-in-progress {
      background-color: #fef3c7;
      color: #d97706;
      padding: 0.25rem 0.75rem;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
    }
    .status-not-started {
      background-color: #fee2e2;
      color: #dc2626;
      padding: 0.25rem 0.75rem;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
    }
    .btn-disabled {
      background-color: #9ca3af !important;
      cursor: not-allowed !important;
      opacity: 0.6;
    }
    .btn-disabled:hover {
      background-color: #9ca3af !important;
      transform: none !important;
    }
</style>

<!-- Tab switching script -->
<script>
  function showTab(tabId) {
    // Hide all tab contents
    document.getElementById('primary-applications').classList.add('hidden');
    document.getElementById('unit-applications').classList.add('hidden');
    
    // Reset all tab buttons
    document.getElementById('primary-applications-tab').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('primary-applications-tab').classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
    document.getElementById('unit-applications-tab').classList.remove('bg-purple-600', 'text-white');
    document.getElementById('unit-applications-tab').classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
    
    // Show selected tab content
    document.getElementById(tabId).classList.remove('hidden');
    
    // Highlight active tab button
    if (tabId === 'primary-applications') {
      document.getElementById('primary-applications-tab').classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
      document.getElementById('primary-applications-tab').classList.add('bg-blue-600', 'text-white');
    } else {
      document.getElementById('unit-applications-tab').classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
      document.getElementById('unit-applications-tab').classList.add('bg-purple-600', 'text-white');
    }
  }
</script>

<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include($headerPartial ?? 'admin.header')
    
    <!-- Main Content -->
    <div class="p-6">
        <!-- Tab Navigation -->
        <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold">eRegistry</h2>
                    <p class="text-sm text-gray-600 mt-1">Electronic Registry Management System</p>
                </div>
                
                <!-- Summary Cards -->
                <div class="flex gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-center min-w-[120px]">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ isset($primaryApplications) ? count($primaryApplications) : 0 }}
                        </div>
                        <div class="text-xs text-blue-700 font-medium">Primary Apps</div>
                        <div class="text-xs text-blue-600">
                            @if(isset($primaryApplications))
                                @php
                                    $completedPrimary = collect($primaryApplications)->where('processing_status', 'Completed')->count();
                                    $inProgressPrimary = collect($primaryApplications)->where('processing_status', 'In Progress')->count();
                                @endphp
                                {{ $completedPrimary }} completed, {{ $inProgressPrimary }} in progress
                            @else
                                0 completed, 0 in progress
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 border border-purple-200 rounded-lg px-4 py-3 text-center min-w-[120px]">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ isset($unitApplications) ? count($unitApplications) : 0 }}
                        </div>
                        <div class="text-xs text-purple-700 font-medium">Unit Apps</div>
                        <div class="text-xs text-purple-600">
                            @if(isset($unitApplications))
                                @php
                                    $completedUnit = collect($unitApplications)->where('processing_status', 'Completed')->count();
                                    $inProgressUnit = collect($unitApplications)->where('processing_status', 'In Progress')->count();
                                @endphp
                                {{ $completedUnit }} completed, {{ $inProgressUnit }} in progress
                            @else
                                0 completed, 0 in progress
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-center min-w-[120px]">
                        <div class="text-2xl font-bold text-green-600">
                            @php
                                $totalPrimary = isset($primaryApplications) ? count($primaryApplications) : 0;
                                $totalUnit = isset($unitApplications) ? count($unitApplications) : 0;
                                $totalApps = $totalPrimary + $totalUnit;
                            @endphp
                            {{ $totalApps }}
                        </div>
                        <div class="text-xs text-green-700 font-medium">Total Apps</div>
                        <div class="text-xs text-green-600">
                            @php
                                $totalCompleted = 0;
                                $totalInProgress = 0;
                                if(isset($primaryApplications)) {
                                    $totalCompleted += collect($primaryApplications)->where('processing_status', 'Completed')->count();
                                    $totalInProgress += collect($primaryApplications)->where('processing_status', 'In Progress')->count();
                                }
                                if(isset($unitApplications)) {
                                    $totalCompleted += collect($unitApplications)->where('processing_status', 'Completed')->count();
                                    $totalInProgress += collect($unitApplications)->where('processing_status', 'In Progress')->count();
                                }
                            @endphp
                            {{ $totalCompleted }} completed, {{ $totalInProgress }} in progress
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Buttons -->
            <div class="flex space-x-1 mb-6">
                <button id="primary-applications-tab" onclick="showTab('primary-applications')" 
                        class="px-6 py-3 rounded-lg font-medium transition-all duration-200 bg-blue-600 text-white">
                    <i data-lucide="building" class="w-4 h-4 mr-2 inline"></i>
                    Primary Applications
                    <span class="ml-2 bg-white bg-opacity-20 px-2 py-1 rounded text-xs">
                        {{ isset($primaryApplications) ? count($primaryApplications) : 0 }}
                    </span>
                </button>
                <button id="unit-applications-tab" onclick="showTab('unit-applications')" 
                        class="px-6 py-3 rounded-lg font-medium transition-all duration-200 bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
                    <i data-lucide="home" class="w-4 h-4 mr-2 inline"></i>
                    Unit Applications
                    <span class="ml-2 bg-gray-100 px-2 py-1 rounded text-xs">
                        {{ isset($unitApplications) ? count($unitApplications) : 0 }}
                    </span>
                </button>
            </div>
            
            <!-- Primary Applications Tab Content -->
            <div id="primary-applications" class="tab-content">
                <div class="overflow-x-auto">
                    <table id="primary-eRegistry-table" class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="text-xs">
                                <th class="table-header">#</th>
                                <th class="table-header">Ministry FileNo</th>
                                <th class="table-header">ST Fileno</th>
                                <th class="table-header">Applicant Name</th>
                                <th class="table-header">Status</th>
                                <th class="table-header">Commissioning Date</th>
                                <th class="table-header">Decommissioning Date</th>
                                <th class="table-header">Expected Return Date</th>
                                <th class="table-header">Registry</th>
                                <th class="table-header">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if(isset($primaryApplications) && count($primaryApplications) > 0)
                                @foreach($primaryApplications as $index => $application)
                                    <tr class="text-xs hover:bg-gray-50">
                                        <td class="table-cell font-medium text-gray-900">{{ $index + 1 }}</td>
                                        <td class="table-cell">{{ $application->fileno ?? 'N/A' }}</td>
                                        <td class="table-cell">{{ $application->np_fileno ?? 'N/A' }}</td>
                                        <td class="table-cell">
                                            @if(!empty($application->corporate_name))
                                                {{ $application->corporate_name }}
                                            @elseif(!empty($application->multiple_owners_names))
                                                @php
                                                    $owners = json_decode($application->multiple_owners_names, true);
                                                    echo is_array($owners) ? implode(', ', array_slice($owners, 0, 2)) . (count($owners) > 2 ? '...' : '') : 'Multiple Owners';
                                                @endphp
                                            @else
                                                {{ trim(($application->first_name ?? '') . ' ' . ($application->surname ?? '')) }}
                                            @endif
                                        </td>
                                        <td class="table-cell">
                                            @if($application->processing_status == 'Completed')
                                                <span class="status-completed">
                                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                    Completed
                                                </span>
                                                @if($application->file_count > 0)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $application->file_count }} page(s)</div>
                                                @endif
                                            @elseif($application->processing_status == 'In Progress')
                                                <span class="status-in-progress">
                                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                                    In Progress
                                                </span>
                                                @if($application->file_count > 0)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $application->file_count }} page(s)</div>
                                                @endif
                                            @else
                                                <span class="status-not-started">
                                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                    Not Started
                                                </span>
                                            @endif
                                        </td>
                                        <td class="table-cell">{{ $application->Commissioning_Date ?? 'N/A' }}</td>
                                        <td class="table-cell">{{ $application->Decommissioning_Date ?? 'N/A' }}</td>
                                        <td class="table-cell">{{ $application->Expected_Return_Date ?? 'N/A' }}</td>
                                        <td class="table-cell">{{ $application->Current_Office ?? 'N/A' }}</td>
                                        <td class="table-cell">
                                            @if($application->processing_status == 'Completed')
                                                <a href="{{ route('file-viewer.primary', $application->application_id) }}" 
                                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                                    <i data-lucide="folder-open" class="w-3 h-3 mr-1"></i>
                                                    View File
                                                </a>
                                            @else
                                                <button disabled 
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white btn-disabled transition-colors"
                                                        title="Files not available - Processing {{ strtolower($application->processing_status) }}">
                                                    <i data-lucide="folder-x" class="w-3 h-3 mr-1"></i>
                                                    @if($application->processing_status == 'In Progress')
                                                        Processing...
                                                    @else
                                                        No File
                                                    @endif
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-xs">
                                    <td colspan="10" class="table-cell text-center py-4">No primary applications found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Unit Applications Tab Content -->
            <div id="unit-applications" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table id="unit-eRegistry-table" class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="text-xs">
                                <th class="table-header">#</th>
                                <th class="table-header">Parent Fileno</th>
                                <th class="table-header">ST Fileno</th>
                                <th class="table-header">Applicant Name</th>
                                <th class="table-header">Status</th>
                                <th class="table-header">Commissioning Date</th>
                                <th class="table-header">Decommissioning Date</th>
                                <th class="table-header">Expected Return Date</th>
                                <th class="table-header">Registry</th>
                                <th class="table-header">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if(isset($unitApplications) && count($unitApplications) > 0)
                                @foreach($unitApplications as $index => $application)
                                    <tr class="text-xs hover:bg-gray-50">
                                        <td class="table-cell font-medium text-gray-900">{{ $index + 1 }}</td>
                                        <td class="table-cell">{{ $application->parent_fileno ?? 'N/A' }}</td>
                                        <td class="table-cell">{{ $application->fileno ?? 'N/A' }}</td>
                                        <td class="table-cell">
                                            @if(!empty($application->corporate_name))
                                                {{ $application->corporate_name }}
                                            @elseif(!empty($application->multiple_owners_names))
                                                @php
                                                    $owners = json_decode($application->multiple_owners_names, true);
                                                    echo is_array($owners) ? implode(', ', array_slice($owners, 0, 2)) . (count($owners) > 2 ? '...' : '') : 'Multiple Owners';
                                                @endphp
                                            @else
                                                {{ trim(($application->first_name ?? '') . ' ' . ($application->surname ?? '')) }}
                                            @endif
                                        </td>
                                        <td class="table-cell">
                                            @if($application->processing_status == 'Completed')
                                                <span class="status-completed">
                                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                    Completed
                                                </span>
                                                @if($application->file_count > 0)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $application->file_count }} page(s)</div>
                                                @endif
                                            @elseif($application->processing_status == 'In Progress')
                                                <span class="status-in-progress">
                                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                                    In Progress
                                                </span>
                                                @if($application->file_count > 0)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $application->file_count }} page(s)</div>
                                                @endif
                                            @else
                                                <span class="status-not-started">
                                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                    Not Started
                                                </span>
                                            @endif
                                        </td>
                                        <td class="table-cell">{{ $application->Commissioning_Date ?? 'N/A' }}</td>
                                        <td class="table-cell">{{ $application->Decommissioning_Date ?? 'N/A' }}</td>
                                        <td class="table-cell">{{ $application->Expected_Return_Date ?? 'N/A' }}</td>
                                        <td class="table-cell">{{ $application->Current_Office ?? 'N/A' }}</td>
                                        <td class="table-cell">
                                            @if($application->processing_status == 'Completed')
                                                <a href="{{ route('file-viewer.unit', $application->sub_application_id) }}" 
                                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                                    <i data-lucide="folder-open" class="w-3 h-3 mr-1"></i>
                                                    View File
                                                </a>
                                            @else
                                                <button disabled 
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white btn-disabled transition-colors"
                                                        title="Files not available - Processing {{ strtolower($application->processing_status) }}">
                                                    <i data-lucide="folder-x" class="w-3 h-3 mr-1"></i>
                                                    @if($application->processing_status == 'In Progress')
                                                        Processing...
                                                    @else
                                                        No File
                                                    @endif
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-xs">
                                    <td colspan="10" class="table-cell text-center py-4">No unit applications found</td>
                                </tr>
                            @endif
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