@extends('layouts.app')
@section('page-title')
    {{ __('GIS Data Management') }}
@endsection

@include('sectionaltitling.partials.assets.css')
@section('content')
<style>
    /* Required for proper z-index stacking and overflow */
    .dropdown-wrapper { 
        position: static; 
    }
    
    .dropdown-menu { 
        position: fixed !important;
        z-index: 10000 !important;
        min-width: 10rem;
        margin-top: 0.25rem;
        white-space: nowrap;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
    }
    
    /* Ensure table container allows overflow */
    .overflow-x-auto { 
        overflow-x: auto;
        overflow-y: visible !important;
        position: relative;
    }
    
    /* Table positioning */
    .table-container {
        position: relative;
        overflow: visible;
    }
    
    /* Prevent table cell from expanding */
    .action-cell {
        width: 80px;
        min-width: 80px;
        max-width: 80px;
        position: relative;
    }
    
    /* Responsive dropdown adjustments */
    @media (max-width: 768px) {
        .dropdown-menu {
            min-width: 8rem;
            font-size: 0.75rem;
        }
        .action-cell {
            width: 60px;
            min-width: 60px;
            max-width: 60px;
        }
    }
    /* Tab styling */
    .tab-nav { 
        border-bottom: 2px solid #e5e7eb; 
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 8px 8px 0 0;
    }
    .tab-nav button { 
        padding: 1rem 2rem; 
        margin-right: 0.25rem; 
        border-bottom: 3px solid transparent; 
        border-radius: 8px 8px 0 0;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #64748b;
    }
    .tab-nav button:hover {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .tab-nav button.active { 
        border-bottom-color: #10b981; 
        color: #10b981; 
        font-weight: 700;
        background-color: white;
        box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
    }
    .tab-content > div { display: none; }
    .tab-content > div.active { display: block; }
    
    /* Counter animations */
    .counter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
    }
    .counter-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .counter-card.primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .counter-card.secondary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }
    .counter-card.total {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    .counter-card.recent {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    /* Table improvements */
    .table-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
        padding: 1rem 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .table-row {
        transition: all 0.2s ease;
    }
    .table-row:hover {
        background-color: #f8fafc;
        transform: scale(1.001);
    }
    
    /* Search and filter styling */
    .search-container {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    
    /* Status badges */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }
    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }
</style>
<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include('admin.header')
    <!-- Dashboard Content -->
    <div class="p-6">
        <!-- Main Content Container -->
        <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
            <!-- Header with actions -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">GIS Data Records</h2>
                <!-- Primary GIS Create Button -->
                <a href="{{ route('gis.create', ['is' => 'primary']) }}" 
                   id="primary-gis-button"
                   class="flex items-center space-x-2 px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-800">
                    <i data-lucide="file-plus" class="w-4 h-4"></i>
                    <span>Create Primary GIS</span>
                </a>
                <!-- Unit GIS Create Button -->
                <a href="{{ route('gis.create', ['is' => 'secondary']) }}" 
                   id="unit-gis-button"
                   class="hidden flex items-center space-x-2 px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-800">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    <span>Create Unit GIS</span>
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Smart Counters Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Primary GIS Counter -->
                <div class="counter-card primary rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium">Primary GIS Records</p>
                            <p class="text-3xl font-bold mt-2" id="primary-counter">{{ count($primaryGisData) }}</p>
                            <p class="text-white/70 text-xs mt-1">Total registered</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i data-lucide="file-text" class="w-8 h-8"></i>
                        </div>
                    </div>
                </div>

                <!-- Unit GIS Counter -->
                <div class="counter-card secondary rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium">Unit GIS Records</p>
                            <p class="text-3xl font-bold mt-2" id="unit-counter">{{ count($unitGisData) }}</p>
                            <p class="text-white/70 text-xs mt-1">Total units</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i data-lucide="layers" class="w-8 h-8"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Records Counter -->
                <div class="counter-card total rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium">Total Records</p>
                            <p class="text-3xl font-bold mt-2" id="total-counter">{{ count($primaryGisData) + count($unitGisData) }}</p>
                            <p class="text-white/70 text-xs mt-1">All GIS data</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i data-lucide="database" class="w-8 h-8"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Records Counter -->
                <div class="counter-card recent rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium">This Month</p>
                            <p class="text-3xl font-bold mt-2" id="recent-counter">
                                {{ 
                                    collect($primaryGisData)->filter(function($item) {
                                        return $item->created_at && \Carbon\Carbon::parse($item->created_at)->isCurrentMonth();
                                    })->count() + 
                                    collect($unitGisData)->filter(function($item) {
                                        return $item->created_at && \Carbon\Carbon::parse($item->created_at)->isCurrentMonth();
                                    })->count()
                                }}
                            </p>
                            <p class="text-white/70 text-xs mt-1">New records</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i data-lucide="trending-up" class="w-8 h-8"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="search-container">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="search-input" placeholder="Search GIS records..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select id="filter-type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Types</option>
                            <option value="primary">Primary GIS</option>
                            <option value="unit">Unit GIS</option>
                        </select>
                        <button id="export-btn" class="flex items-center space-x-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Export</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-nav flex mb-4">
                <button class="tab-button active" data-tab="primary-gis">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                    Primary GIS
                </button>
                <button class="tab-button" data-tab="unit-gis">
                    <i data-lucide="layers" class="w-4 h-4 mr-2"></i>
                    Unit GIS
                </button>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Primary GIS Tab -->
                <div id="primary-gis" class="active">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-xs">
                                    <th class="table-header text-green-500">MLSF No</th>
                                    <th class="table-header text-green-500">KANGIS File No</th>
                                    <th class="table-header text-green-500">New KANGIS File No</th>
                                    <th class="table-header text-green-500">Plot No</th>
                                    <th class="table-header text-green-500">Block No</th>
                                    <th class="table-header text-green-500">Approved Plan No</th>
                                    <th class="table-header text-green-500">TP Plan No</th>
                                    <th class="table-header text-green-500">Old Title Serial No</th>
                                    <th class="table-header text-green-500">Old Title Page No</th>
                                    <th class="table-header text-green-500">Old Title Volume No</th>
                                    <th class="table-header text-green-500">Created At</th>
                                    <th class="table-header text-green-500">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($primaryGisData as $data)
                                <tr class="text-xs table-row">
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->mlsfNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->kangisFileNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->NewKANGISFileno ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->plotNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->blockNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->approvedPlanNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->tpPlanNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->oldTitleSerialNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->oldTitlePageNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->oldTitleVolumeNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->created_at ? date('d M, Y', strtotime($data->created_at)) : 'N/A' }}</td>
                                    <td class="table-cell action-cell px-1 py-1">
                                        <div class="dropdown-wrapper">
                                            <button class="flex items-center px-3 py-1 text-xs bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors dropdown-toggle" 
                                                    type="button" 
                                                    data-gis-id="{{ $data->id }}"
                                                    data-gis-type="primary"
                                                    onclick="toggleGisDropdown({{ $data->id }}, 'primary')">
                                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Unit GIS Tab -->
                <div id="unit-gis">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-xs">
                                    <th class="table-header text-green-500">ST File No</th>
                                    <th class="table-header text-green-500">Primary GIS ID</th>
                                    <th class="table-header text-green-500">Scheme No</th>
                                    <th class="table-header text-green-500">Section No</th>
                                    <th class="table-header text-green-500">Block No</th>
                                    <th class="table-header text-green-500">Unit No</th>
                                    <th class="table-header text-green-500">Land Use</th>
                                    <th class="table-header text-green-500">Unit Size</th>
                                    <th class="table-header text-green-500">Plot No</th>
                                    <th class="table-header text-green-500">Created At</th>
                                    <th class="table-header text-green-500">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($unitGisData as $data)
                                <tr class="text-xs table-row">
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->STFileNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->PrimaryGISID ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->scheme_no ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->section_no ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->block_no ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->unit_no ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->landuse ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->UnitSize ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->plotNo ?? 'N/A' }}</td>
                                    <td class="table-cell px-1 py-1 truncate">{{ $data->created_at ? date('d M, Y', strtotime($data->created_at)) : 'N/A' }}</td>
                                    <td class="table-cell action-cell px-1 py-1">
                                        <div class="dropdown-wrapper">
                                            <button class="flex items-center px-3 py-1 text-xs bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors dropdown-toggle" 
                                                    type="button" 
                                                    data-gis-id="{{ $data->id }}"
                                                    data-gis-type="unit"
                                                    onclick="toggleGisDropdown({{ $data->id }}, 'unit')">
                                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    @include('admin.footer')
</div>

<!-- Dynamic Dropdown Container (will be positioned absolutely) -->
<div id="dynamicGisDropdown" class="dropdown-menu hidden bg-white border border-gray-200 rounded-md shadow-lg py-1" style="position: fixed; z-index: 10000;">
    <!-- Content will be dynamically populated -->
</div>

<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        // Counter animation function
        function animateCounter(element, target, duration = 1000) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 16);
        }
        
        // Animate counters on page load
        setTimeout(() => {
            const primaryCounter = document.getElementById('primary-counter');
            const unitCounter = document.getElementById('unit-counter');
            const totalCounter = document.getElementById('total-counter');
            const recentCounter = document.getElementById('recent-counter');
            
            if (primaryCounter) animateCounter(primaryCounter, parseInt(primaryCounter.textContent));
            if (unitCounter) animateCounter(unitCounter, parseInt(unitCounter.textContent));
            if (totalCounter) animateCounter(totalCounter, parseInt(totalCounter.textContent));
            if (recentCounter) animateCounter(recentCounter, parseInt(recentCounter.textContent));
        }, 300);
        
        // Search functionality
        const searchInput = document.getElementById('search-input');
        const filterType = document.getElementById('filter-type');
        
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase();
            const filterValue = filterType.value;
            
            // Get all table rows
            const primaryRows = document.querySelectorAll('#primary-gis tbody tr');
            const unitRows = document.querySelectorAll('#unit-gis tbody tr');
            
            // Filter primary GIS rows
            let visiblePrimary = 0;
            primaryRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let textContent = '';
                cells.forEach(cell => textContent += cell.textContent.toLowerCase() + ' ');
                
                const matchesSearch = textContent.includes(searchTerm);
                const matchesFilter = filterValue === '' || filterValue === 'primary';
                
                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                    visiblePrimary++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Filter unit GIS rows
            let visibleUnit = 0;
            unitRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let textContent = '';
                cells.forEach(cell => textContent += cell.textContent.toLowerCase() + ' ');
                
                const matchesSearch = textContent.includes(searchTerm);
                const matchesFilter = filterValue === '' || filterValue === 'unit';
                
                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                    visibleUnit++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update counters based on visible rows
            if (searchTerm || filterValue) {
                document.getElementById('primary-counter').textContent = visiblePrimary;
                document.getElementById('unit-counter').textContent = visibleUnit;
                document.getElementById('total-counter').textContent = visiblePrimary + visibleUnit;
            }
        }
        
        // Add search event listeners
        searchInput.addEventListener('input', performSearch);
        filterType.addEventListener('change', performSearch);
        
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content > div');
        const primaryButton = document.getElementById('primary-gis-button');
        const unitButton = document.getElementById('unit-gis-button');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                
                // Remove active class from all buttons and content
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to current button and content
                button.classList.add('active');
                document.getElementById(tabId).classList.add('active');
                
                // Toggle create buttons visibility
                if (tabId === 'primary-gis') {
                    primaryButton.classList.remove('hidden');
                    unitButton.classList.add('hidden');
                } else {
                    primaryButton.classList.add('hidden');
                    unitButton.classList.remove('hidden');
                }
                
                // Update filter dropdown based on active tab
                if (tabId === 'primary-gis') {
                    filterType.value = 'primary';
                } else {
                    filterType.value = 'unit';
                }
                performSearch();
            });
        });
        
        // Export functionality
        const exportBtn = document.getElementById('export-btn');
        exportBtn.addEventListener('click', function() {
            const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
            const table = document.querySelector(`#${activeTab} table`);
            
            if (!table) return;
            
            // Get visible rows only
            const rows = Array.from(table.querySelectorAll('tr')).filter(row => 
                row.style.display !== 'none'
            );
            
            let csv = '';
            rows.forEach(row => {
                const cells = row.querySelectorAll('th, td');
                const rowData = Array.from(cells).slice(0, -1).map(cell => 
                    `"${cell.textContent.trim().replace(/"/g, '""')}"`
                ).join(',');
                csv += rowData + '\n';
            });
            
            // Download CSV
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${activeTab}-data-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        });
        
        // Reset counters when search is cleared
        searchInput.addEventListener('input', function() {
            if (!this.value && !filterType.value) {
                // Reset to original values
                setTimeout(() => {
                    const originalPrimary = {{ count($primaryGisData) }};
                    const originalUnit = {{ count($unitGisData) }};
                    const originalTotal = originalPrimary + originalUnit;
                    const originalRecent = {{ 
                        collect($primaryGisData)->filter(function($item) {
                            return $item->created_at && \Carbon\Carbon::parse($item->created_at)->isCurrentMonth();
                        })->count() + 
                        collect($unitGisData)->filter(function($item) {
                            return $item->created_at && \Carbon\Carbon::parse($item->created_at)->isCurrentMonth();
                        })->count()
                    }};
                    
                    document.getElementById('primary-counter').textContent = originalPrimary;
                    document.getElementById('unit-counter').textContent = originalUnit;
                    document.getElementById('total-counter').textContent = originalTotal;
                    document.getElementById('recent-counter').textContent = originalRecent;
                }, 100);
            }
        });
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
            
            // Escape to clear search
            if (e.key === 'Escape' && document.activeElement === searchInput) {
                searchInput.value = '';
                filterType.value = '';
                performSearch();
                searchInput.blur();
            }
        });
        
        // Add loading states for better UX
        const originalCounters = {
            primary: {{ count($primaryGisData) }},
            unit: {{ count($unitGisData) }},
            total: {{ count($primaryGisData) + count($unitGisData) }},
            recent: {{ 
                collect($primaryGisData)->filter(function($item) {
                    return $item->created_at && \Carbon\Carbon::parse($item->created_at)->isCurrentMonth();
                })->count() + 
                collect($unitGisData)->filter(function($item) {
                    return $item->created_at && \Carbon\Carbon::parse($item->created_at)->isCurrentMonth();
                })->count()
            }}
        };
        
        // Store original values for reset
        window.originalCounters = originalCounters;
    });

    // New GIS Dropdown functionality using external dropdown
    let currentDropdownGisId = null;
    let currentDropdownGisType = null;
    
    window.toggleGisDropdown = function(gisId, gisType) {
        const dropdown = document.getElementById('dynamicGisDropdown');
        const button = document.querySelector(`[data-gis-id="${gisId}"][data-gis-type="${gisType}"]`);
        
        // If clicking the same button, close dropdown
        if (currentDropdownGisId === gisId && currentDropdownGisType === gisType && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
            currentDropdownGisId = null;
            currentDropdownGisType = null;
            return;
        }
        
        // Populate dropdown content
        dropdown.innerHTML = `
            <a href="{{ url('gis/edit') }}/${gisId}" 
               class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit
            </a>
            <a href="{{ url('gis/view') }}/${gisId}" 
               class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors">
                <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                View
            </a>
            <button onclick="confirmGisDelete(${gisId})" 
                    class="flex items-center w-full px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                Delete
            </button>
        `;
        
        // Position dropdown
        const rect = button.getBoundingClientRect();
        const dropdownWidth = 160;
        const dropdownHeight = 120;
        
        let left = rect.right - dropdownWidth;
        let top = rect.bottom + window.scrollY;
        
        // Ensure dropdown doesn't go off screen
        if (left < 10) {
            left = rect.left;
        }
        if (left + dropdownWidth > window.innerWidth - 10) {
            left = window.innerWidth - dropdownWidth - 10;
        }
        
        // Check if dropdown would go below viewport
        if (rect.bottom + dropdownHeight > window.innerHeight) {
            top = rect.top + window.scrollY - dropdownHeight;
        }
        
        // Apply positioning and show
        dropdown.style.left = `${left}px`;
        dropdown.style.top = `${top}px`;
        dropdown.classList.remove('hidden');
        
        currentDropdownGisId = gisId;
        currentDropdownGisType = gisType;
        
        // Recreate icons
        setTimeout(() => {
            lucide.createIcons();
        }, 10);
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dynamicGisDropdown');
        if (!event.target.closest('.dropdown-wrapper') && !event.target.closest('#dynamicGisDropdown')) {
            dropdown.classList.add('hidden');
            currentDropdownGisId = null;
            currentDropdownGisType = null;
        }
    });

    // Delete confirmation function
    window.confirmGisDelete = function(gisId) {
        // Close dropdown first
        document.getElementById('dynamicGisDropdown').classList.add('hidden');
        currentDropdownGisId = null;
        currentDropdownGisType = null;
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a form and submit it for deletion
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('gis') }}/${gisId}`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add method spoofing for DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Close dropdown with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            // Close dropdown
            const dropdown = document.getElementById('dynamicGisDropdown');
            if (!dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                currentDropdownGisId = null;
                currentDropdownGisType = null;
            }
        }
    });
</script>
@endsection
