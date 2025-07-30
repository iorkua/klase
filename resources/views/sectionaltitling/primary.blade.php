@extends('layouts.app')
@section('page-title')
    {{$PageTitle}}  
@endsection

{{-- Add DataTables CSS --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endpush


@include('sectionaltitling.partials.assets.css')
@section('content')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">
          
            
            <!-- Primary Applications Overview - Screenshot 129 -->

            @if(!request()->has('survey') && (!request()->has('url') || (request()->get('url') !== 'phy_planning' && request()->get('url') !== 'recommendation')))
                @include('sectionaltitling.partials.statistic.PrimaryApplications')
            @endif

            <!-- Primary Applications Table -->
            <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Primary Applications</h2>
                     
                    <div class="flex items-center space-x-4">

                        @php
                            $specialUrls = ['phy_planning', 'recommendation'];
                            $isSurveyOrSpecialUrl = request()->has('survey') || (request()->has('url') && in_array(request()->get('url'), $specialUrls));
                            $urlParam = request()->get('url');
                            // Only add 'survey' if it exists in the request
                            $query = [];
                            if ($urlParam) {
                                $query['url'] = $urlParam;
                            }
                            if (request()->has('survey')) {
                                $query['survey'] = true;
                            }
                            $routeUrl = route('sectionaltitling.secondary', $query);
                        @endphp

                        @if($isSurveyOrSpecialUrl)
                            <a href="{{ $routeUrl }}" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                                <span>View Secondary Applications</span>
                            </a>
                        @endif

                                    <div class="relative">
                    <select id="statusFilter"
                        class="pl-4 pr-8 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                        <option value="All...">All...</option>
                        <option value="Approved">Approved</option>
                        <option value="Pending">Pending</option>
                        <option value="Declined">Declined</option>
                    </select>
                    <i data-lucide="chevron-down"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                    </div>
                                        {{-- <button style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background-color: #fff8f1; border: 2px solid #f97316; border-radius: 0.375rem; cursor: pointer; transition: background-color 0.2s ease;">
                            <i data-lucide="upload" style="width: 1rem; height: 1rem; color: #ea580c;"></i>
                            <span style="font-weight: 500; color: #ea580c;">Import Field Data</span>
                        </button> --}}

                        <style>
                            button:hover {
                                background-color: #fed7aa;
                            }
                        </style>

                        {{-- Replace the export button with export dropdown --}}
                        <div class="relative inline-block">
                            <button onclick="toggleExportDropdown(event)" class="flex items-center space-x-2 px-4 py-2 border border-gray-200 rounded-md">
                                <i data-lucide="download" class="w-4 h-4 text-gray-600"></i>
                                <span>Export</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 ml-2"></i>
                            </button>
                            <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                                <button onclick="exportTable('excel')" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-green-600"></i>
                                    Export to Excel
                                </button>
                                <button onclick="exportTable('csv')" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center">
                                    <i data-lucide="file" class="w-4 h-4 mr-2 text-blue-600"></i>
                                    Export to CSV
                                </button>
                                <button onclick="exportTable('pdf')" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-red-600"></i>
                                    Export to PDF
                                </button>
                            </div>
                        </div>
                            @if(
                                !request()->has('survey') && 
                                (
                                    !request()->has('url') || 
                                    (
                                        request()->get('url') !== 'phy_planning' && 
                                        request()->get('url') !== 'recommendation'
                                        
                                    )
                                    
                                )
                            )
                                @if(request()->has('url') && request()->get('url') === 'infopro')
                                    <div class="relative z-10">
                                        <button type="button" onclick="toggleDropdown(event)" class="flex items-center space-x-2 px-4 py-2 bg-gray-900 text-white rounded-md">
                                            <i data-lucide="file-plus" class="w-4 h-4"></i>
                                            <span>New Primary Application</span>
                                            <i data-lucide="chevron-down" class="w-4 h-4 ml-2"></i>
                                        </button>
                                        <div class="absolute mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg hidden dropdown-menu">
                                            <a href="{{ route('primaryform.index') }}?landuse=Residential" class="flex items-center px-4 py-2 hover:bg-gray-100">
                                                <i data-lucide="home" class="w-4 h-4 text-blue-500 mr-2"></i>
                                                Residential
                                            </a>
                                            <a href="{{ route('primaryform.index') }}?landuse=Commercial" class="flex items-center px-4 py-2 hover:bg-gray-100">
                                                <i data-lucide="briefcase" class="w-4 h-4 text-green-500 mr-2"></i>
                                                Commercial
                                            </a>
                                            <a href="{{ route('primaryform.index') }}?landuse=Industrial" class="flex items-center px-4 py-2 hover:bg-gray-100">
                                                <i data-lucide="factory" class="w-4 h-4 text-red-500 mr-2"></i>
                                                Industrial
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endif  
                  
                    </div>
                </div>

                <div class="overflow-x-auto">
                    {{-- Replace the existing table structure with DataTables --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead> 
                            <tr class="text-xs">
                                
                                <th class="table-header text-green-500">File No</th>
                                <th class="table-header text-green-500">Property</th>
                                <th class="table-header text-green-500">Type</th>
                                <th class="table-header text-green-500">Land Use</th>
                                <th class="table-header text-green-500">Owner</th>
                                <th class="table-header text-green-500">Units</th>
                                <th class="table-header text-green-500">Date</th>
                                {{-- <th class="table-header text-green-500">Planning Recommendation Approval</th> --}}

                                   @if(!request()->has('survey') && (!request()->has('url') || (request()->get('url') !== 'phy_planning' && request()->get('url') !== 'recommendation')))
                                <th class="table-header text-green-500">Director's Approval</th>
                                
                                    
                                @endif
                               
                                <th class="table-header text-green-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($PrimaryApplications as $PrimaryApplication)
                                 <tr class="text-xs">
                                   
                                    <td class="table-cell">
                                        <div class="truncate max-w-[120px]" title="{{ $PrimaryApplication->fileno }}">
                                            {{ $PrimaryApplication->fileno }}
                                        </div>
                                    </td>
                                     
                                    <td class="table-cell">
                                        <div class="truncate max-w-[150px]" title="{{ $PrimaryApplication->property_plot_no }} {{ $PrimaryApplication->property_street_name }}, {{ $PrimaryApplication->property_lga }}">
                                            {{ $PrimaryApplication->property_plot_no }} {{ $PrimaryApplication->property_street_name }}, {{ $PrimaryApplication->property_lga }}
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        @if ($PrimaryApplication->residential_type)
                                            {{ $PrimaryApplication->residential_type }}
                                        @elseif ($PrimaryApplication->commercial_type)
                                            {{ $PrimaryApplication->commercial_type }}
                                        @elseif ($PrimaryApplication->industrial_type)
                                            {{ $PrimaryApplication->industrial_type }}
                                        @elseif ($PrimaryApplication->mixed_type)
                                            {{ $PrimaryApplication->mixed_type }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        @if($PrimaryApplication->land_use)
                                            @php
                                                $landUseIcon = '';
                                                $landUseBadgeClass = '';
                                                switch(strtolower($PrimaryApplication->land_use)) {
                                                    case 'residential':
                                                        $landUseIcon = 'home';
                                                        $landUseBadgeClass = 'bg-blue-100 text-blue-800 border-blue-200';
                                                        break;
                                                    case 'commercial':
                                                        $landUseIcon = 'briefcase';
                                                        $landUseBadgeClass = 'bg-green-100 text-green-800 border-green-200';
                                                        break;
                                                    case 'industrial':
                                                        $landUseIcon = 'factory';
                                                        $landUseBadgeClass = 'bg-red-100 text-red-800 border-red-200';
                                                        break;
                                                    default:
                                                        $landUseIcon = 'map-pin';
                                                        $landUseBadgeClass = 'bg-gray-100 text-gray-800 border-gray-200';
                                                }
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $landUseBadgeClass }}">
                                                <i data-lucide="{{ $landUseIcon }}" class="w-3 h-3 mr-1"></i>
                                                {{ $PrimaryApplication->land_use }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-800 border-gray-200">
                                                <i data-lucide="map-pin" class="w-3 h-3 mr-1"></i>
                                                N/A
                                            </span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                                @if ($PrimaryApplication->passport)
                                                    <img src="{{ asset('storage/app/public/' . $PrimaryApplication->passport) }}" 
                                                         alt="Passport" 
                                                         class="w-full h-full rounded-full object-cover cursor-pointer"
                                                         onclick="showPassportPreview('{{ asset('storage/app/public/' . $PrimaryApplication->passport) }}', 'Owner Passport')">
                                                @elseif ($PrimaryApplication->multiple_owners_passport)
                                                    @php
                                                        $passports = json_decode($PrimaryApplication->multiple_owners_passport, true);
                                                        $firstPassport = $passports[0] ?? null;
                                                    @endphp
                                                    @if ($firstPassport)
                                                        <img src="{{ asset('storage/app/public/' . $firstPassport) }}" 
                                                             alt="Passport" 
                                                             class="w-full h-full rounded-full object-cover cursor-pointer"
                                                             onclick="showMultipleOwners({{ $PrimaryApplication->multiple_owners_names }}, {{ $PrimaryApplication->multiple_owners_passport }})">
                                                    @endif
                                                @endif
                                            </div>
                                            <span class="truncate max-w-[120px]">
                                                @if ($PrimaryApplication->corporate_name)
                                                    {{ $PrimaryApplication->corporate_name }}
                                                @elseif($PrimaryApplication->multiple_owners_names)
                                                    @php
                                                        $ownerNames = json_decode(
                                                            $PrimaryApplication->multiple_owners_names,
                                                            true,
                                                        );
                                                        $firstOwner = $ownerNames[0] ?? 'Unknown Owner';
                                                    @endphp
                                                    {{ $firstOwner }}
                                                    <span class="ml-1 cursor-pointer text-blue-500"
                                                        onclick="showMultipleOwners({{ $PrimaryApplication->multiple_owners_names }}, {{ $PrimaryApplication->multiple_owners_passport }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                @elseif($PrimaryApplication->first_name || $PrimaryApplication->surname)
                                                    {{ $PrimaryApplication->first_name }} {{ $PrimaryApplication->surname }}
                                                @else
                                                    Unknown Owner
                                                @endif
                                            </span>
                                        </div>
 
                                    </td>
                                    <td class="table-cell">
                                        @php
                                            $enteredUnitsCount = \DB::connection('sqlsrv')
                                                ->table('subapplications')
                                                ->where('main_application_id', $PrimaryApplication->id)
                                                ->count();
                                            $totalUnits = $PrimaryApplication->NoOfUnits ?? 0;
                                        @endphp
                                        @if($totalUnits > 0)
                                            <span class="badge badge-units">
                                                <i data-lucide="building" class="w-3 h-3 mr-1"></i>
                                                {{ $enteredUnitsCount }} of {{ $totalUnits }} Units
                                            </span>
                                        @else
                                            <span class="badge badge-primary">0 Units</span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        {{ \Carbon\Carbon::parse($PrimaryApplication->created_at)->format('Y-m-d') }}
                                    </td>
                                    {{-- <td class="table-cell uppercase" style="">
                                        <div class="flex items-center">
                                            @php
                                                $planningStatus = strtolower($PrimaryApplication->planning_recommendation_status ?? '');
                                                $planningBadgeClass = match($planningStatus) {
                                                    'approved' => 'bg-green-100 text-green-800 border-green-200',
                                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    'declined' => 'bg-red-100 text-red-800 border-red-200',
                                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                                    'in progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $planningBadgeClass }}">
                                                {{ $PrimaryApplication->planning_recommendation_status }}
                                            </span>
                                            @if($PrimaryApplication->planning_recommendation_status == 'Declined')
                                                <i data-lucide="info" class="w-4 h-4 ml-1 text-blue-500 cursor-pointer" 
                                                   onclick="showDeclinedInfo(event, 'Planning Recommendation', {{ json_encode($PrimaryApplication->recomm_comments) }}, {{ json_encode($PrimaryApplication->director_comments) }})"></i>
                                            @endif
                                        </div>
                                    </td>   --}}
                                    @if(!request()->has('survey') && (!request()->has('url') || (request()->get('url') !== 'phy_planning' && request()->get('url') !== 'recommendation')))
                                    <td class="table-cell">
                                        <div class="flex items-center">
                                            <span class="badge badge-{{ strtolower($PrimaryApplication->application_status) }}">
                                                {{ $PrimaryApplication->application_status }}
                                            </span>
                                            @if($PrimaryApplication->application_status == 'Declined')
                                                <i data-lucide="info" class="w-4 h-4 ml-1 text-blue-500 cursor-pointer" 
                                                   onclick="showDeclinedInfo(event, 'Application Status', {{ json_encode($PrimaryApplication->recomm_comments) }}, {{ json_encode($PrimaryApplication->director_comments) }})"></i>
                                            @endif
                                        </div>
                                    </td>
                                    @endif

                                    <td class="table-cell overflow-visible relative">
                                    @if(request()->has('url') && request()->get('url') === 'infopro')
                                        @include('sectionaltitling.action_menu.info_pro_action')
                                    @else
                                     @include('sectionaltitling.action_menu.info_pro_action')
                                       
                                        {{-- @include('sectionaltitling.action_menu.action') --}}
                                    @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
 
            </div>
        </div>

        <!-- Footer -->
        @include('admin.footer')
      </div>
   
@include('sectionaltitling.action_modals.eRegistry_modal')
@include('sectionaltitling.action_modals.cofo_details_modal') 

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add ID to the filter select if it doesn't have one
    const filterSelect = document.querySelector('select');
    if (filterSelect && !filterSelect.id) {
        filterSelect.id = 'statusFilter';
    }
    
    // Pagination variables
    window.primaryTablePagination = {
        currentPage: 1,
        rowsPerPage: 10,
        filteredRows: [],
        allRows: Array.from(document.querySelectorAll('tbody tr'))
    };

    function paginateTable(page = 1) {
        const { rowsPerPage, filteredRows } = window.primaryTablePagination;
        const totalRows = filteredRows.length;
        const startIdx = (page - 1) * rowsPerPage;
        const endIdx = startIdx + rowsPerPage;

        filteredRows.forEach((row, idx) => {
            row.style.display = (idx >= startIdx && idx < endIdx) ? '' : 'none';
        });

        // Update showing count
        const showingCount = document.getElementById('showingCount');
        showingCount.textContent = `Showing ${Math.min(endIdx, totalRows) - startIdx > 0 ? Math.min(endIdx, totalRows) - startIdx : 0} of ${totalRows} applications`;

        // Enable/disable buttons
        document.getElementById('prevPageBtn').disabled = page === 1;
        document.getElementById('nextPageBtn').disabled = endIdx >= totalRows;

        window.primaryTablePagination.currentPage = page;
    }

    function filterTable(selectedStatus) {
        const allRows = window.primaryTablePagination.allRows;
        let filteredRows = [];

        allRows.forEach(row => {
            let showRow = false;
            if (selectedStatus === 'All...') {
                showRow = true;
            } else {
                const planningStatusBadge = row.querySelector('td:nth-child(9) .badge');
                const directorStatusBadge = row.querySelector('td:nth-child(10) .badge');
                if (planningStatusBadge && planningStatusBadge.textContent.trim() === selectedStatus) showRow = true;
                if (directorStatusBadge && directorStatusBadge.textContent.trim() === selectedStatus) showRow = true;
            }
            row.style.display = showRow ? '' : 'none';
            if (showRow) filteredRows.push(row);
        });

        window.primaryTablePagination.filteredRows = filteredRows;
        paginateTable(1);
    }

    // Initial setup
    window.primaryTablePagination.filteredRows = window.primaryTablePagination.allRows.filter(row => row.style.display !== 'none');
    paginateTable(1);

    // Filter event
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterTable(this.value);
        });
    }

    // Pagination events
    document.getElementById('prevPageBtn').addEventListener('click', function() {
        const { currentPage } = window.primaryTablePagination;
        if (currentPage > 1) paginateTable(currentPage - 1);
    });
    document.getElementById('nextPageBtn').addEventListener('click', function() {
        const { currentPage, filteredRows, rowsPerPage } = window.primaryTablePagination;
        if (currentPage * rowsPerPage < filteredRows.length) paginateTable(currentPage + 1);
    });

    // Export to CSV
    document.querySelector('button.flex.items-center.space-x-2.px-4.py-2.border.border-gray-200.rounded-md').addEventListener('click', function() {
        exportVisibleTableToCSV();
    });

    function exportVisibleTableToCSV() {
        const table = document.querySelector('table');
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText.trim());
        const { filteredRows, currentPage, rowsPerPage } = window.primaryTablePagination;
        const startIdx = (currentPage - 1) * rowsPerPage;
        const endIdx = startIdx + rowsPerPage;
        const visibleRows = filteredRows.slice(startIdx, endIdx);

        let csvContent = '';
        csvContent += headers.join(',') + '\n';

        visibleRows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('td')).map(td => {
                // Remove commas and newlines from cell text
                return '"' + td.innerText.replace(/"/g, '""').replace(/\n/g, ' ').replace(/,/g, ' ') + '"';
            });
            csvContent += cells.join(',') + '\n';
        });

        // Download CSV
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'primary_applications.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    // Re-filter and paginate on load
    filterTable(statusFilter ? statusFilter.value : 'All...');
});

        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdownMenu = event.currentTarget.nextElementSibling;
            if (dropdownMenu) {
                dropdownMenu.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', () => {
            const dropdownMenus = document.querySelectorAll('.dropdown-menu');
            dropdownMenus.forEach(menu => menu.classList.add('hidden'));
        });



        function showPassportPreview(imageSrc, title) {
                                                Swal.fire({
                                                    title: title,
                                                    html: `<img src="${imageSrc}" class="img-fluid" style="max-height: 400px;">`,
                                                    width: 'auto',
                                                    showCloseButton: true,
                                                    showConfirmButton: false
                                                });
                                            }
                                              
                                            function showMultipleOwners(owners, passports) {
                                                if (Array.isArray(owners) && owners.length > 0) {
                                                    let htmlContent = '<div class="grid grid-cols-3 gap-4" style="max-width: 600px;">';
                                                    
                                                    owners.forEach((name, index) => {
                                                        const passport = Array.isArray(passports) && passports[index] 
                                                            ? `<img src="{{ asset('storage/app/public/') }}/${passports[index]}" 
                                                                 class="w-24 h-32 object-cover mx-auto border-2 border-gray-300" 
                                                                 style="object-position: center top;">` 
                                                            : '<div class="w-24 h-32 bg-gray-300 mx-auto flex items-center justify-center"><span>No Image</span></div>';
                                                        
                                                        htmlContent += `
                                                            <div class="flex flex-col items-center">
                                                                <div class="passport-container bg-blue-50 p-2 rounded">
                                                                    ${passport}
                                                                    <p class="text-center text-sm font-medium mt-1">${name}</p>
                                                                </div>
                                                            </div>
                                                        `;
                                                    });
                                                    
                                                    htmlContent += '</div>';
                                                    
                                                    Swal.fire({
                                                        title: 'Multiple Owners',
                                                        html: htmlContent,
                                                        width: 'auto',
                                                        showCloseButton: true,
                                                        showConfirmButton: false
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        title: 'Multiple Owners',
                                                        text: 'No owners available',
                                                        icon: 'info',
                                                        confirmButtonText: 'Close'
                                                    });
                                                }
                                            }
                                            
                                            function showDeclinedInfo(event, title, recommComments, directorComments) {
                                                event.stopPropagation();
                                                
                                                let htmlContent = '<div class="text-left">';
                                                if (recommComments) {
                                                    htmlContent += `
                                                        <div class="mb-3">
                                                            <h3 class="font-bold text-gray-700">Recommendation Comments:</h3>
                                                            <p class="text-gray-600 mt-1 p-2 bg-gray-100 rounded">${recommComments}</p>
                                                        </div>
                                                    `;
                                                }
                                                
                                                if (directorComments) {
                                                    htmlContent += `
                                                        <div>
                                                            <h3 class="font-bold text-gray-700">Director Comments:</h3>
                                                            <p class="text-gray-600 mt-1 p-2 bg-gray-100 rounded">${directorComments}</p>
                                                        </div>
                                                    `;
                                                }
                                                
                                                if (!recommComments && !directorComments) {
                                                    htmlContent += '<p>No comments available.</p>';
                                                }
                                                
                                                htmlContent += '</div>';
                                                
                                                Swal.fire({
                                                    title: `Declined: ${title}`,
                                                    html: htmlContent,
                                                    icon: 'info',
                                                    width: 'auto',
                                                    showCloseButton: true,
                                                    showConfirmButton: true,
                                                    confirmButtonText: 'Close'
                                                });
                                            }
    
                         </script>
    
    {{-- Add DataTables JS dependencies at the end of the file --}}
    
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize DataTable
                const table = $('table').DataTable({
                    dom: 'Bfrtip',
                    pageLength: 10,
                    responsive: true
                });

                // Handle status filter
                $('#statusFilter').on('change', function() {
                    const status = $(this).val();
                    table.column(8).search(status === 'All...' ? '' : status).draw();
                });
            });

            function toggleExportDropdown(event) {
                event.stopPropagation();
                const dropdown = document.getElementById('exportDropdown');
                dropdown.classList.toggle('hidden');
            }

            document.addEventListener('click', () => {
                document.getElementById('exportDropdown').classList.add('hidden');
            });

            function exportTable(type) {
                const table = $('table').DataTable();
                
                switch(type) {
                    case 'excel':
                        table.button('.buttons-excel').trigger();
                        break;
                    case 'csv':
                        table.button('.buttons-csv').trigger();
                        break;
                    case 'pdf':
                        table.button('.buttons-pdf').trigger();
                        break;
                }
            }
        </script>
   

@endsection
