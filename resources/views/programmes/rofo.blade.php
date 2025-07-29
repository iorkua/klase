@extends('layouts.app')

@section('page-title')
    {{ $PageTitle ?? __('KLAES') }}
@endsection

@section('content')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<style>
    /* Modern Design System Variables */
    :root {
        --primary-50: #f0fdf4;
        --primary-100: #dcfce7;
        --primary-500: #22c55e;
        --primary-600: #16a34a;
        --primary-700: #15803d;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;
    }

    /* Reset and Base Styles */
    * {
        box-sizing: border-box;
    }

    /* Modern Container System */
    .rofo-container {
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .rofo-header {
        background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
        padding: 2rem;
        color: white;
    }

    .rofo-content {
        padding: 2rem;
    }

    /* Modern Filter System */
    .filter-panel {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateY(-10px);
        opacity: 0;
        visibility: hidden;
        max-height: 0;
        overflow: hidden;
    }

    .filter-panel.active {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
        max-height: 500px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        align-items: end;
    }

    /* Modern Form Controls */
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.25rem;
    }

    .form-input, .form-select {
        padding: 0.75rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: white;
        width: 100%;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgb(34 197 94 / 0.1);
    }

    .form-input::placeholder {
        color: var(--gray-400);
    }

    /* Modern Button System */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-700) 0%, var(--primary-600) 100%);
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: white;
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }

    .btn-secondary:hover {
        background: var(--gray-50);
        border-color: var(--gray-400);
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.8125rem;
    }

    /* Modern Tab System */
    .tab-container {
        margin-bottom: 2rem;
    }

    .tab-list {
        display: flex;
        background: var(--gray-100);
        border-radius: var(--radius-lg);
        padding: 0.25rem;
        gap: 0.25rem;
    }

    .tab-button {
        flex: 1;
        padding: 1rem 1.5rem;
        border: none;
        background: transparent;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--gray-600);
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .tab-button.active {
        background: white;
        color: var(--primary-700);
        box-shadow: var(--shadow-sm);
    }

    .tab-button:hover:not(.active) {
        color: var(--gray-700);
        background: var(--gray-50);
    }

    /* Clean Table System - Matching Primary Applications */
    .table-wrapper {
        background: white;
        border-radius: 0.375rem;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }

    .table-header {
        background-color: #f9fafb;
        font-weight: 500;
        color: #4b5563;
        text-align: left;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .table-cell {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .table-row {
        background: white;
    }

    .table-row:hover {
        background-color: #f9fafb;
    }

    /* Enhanced cell styling */
    .table-cell.font-medium {
        font-weight: 600;
        color: #111827;
    }

    .table-cell .text-primary-600 {
        color: var(--primary-600);
        font-weight: 600;
    }

    /* Empty state styling */
    .table-empty-state {
        padding: 3rem 2rem;
        text-align: center;
        background: #f9fafb;
    }

    .table-empty-state .empty-icon {
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1rem;
        color: #9ca3af;
    }

    .table-empty-state .empty-text {
        color: #6b7280;
        font-size: 1rem;
        font-weight: 500;
    }

    /* Clean Badge System - Matching Primary Applications */
    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        border: 1px solid transparent;
        transition: all 0.2s ease-in-out;
    }

    .badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .badge-success {
        background-color: #d1fae5;
        color: #059669;
        border-color: #86efac;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #d97706;
        border-color: #fcd34d;
    }

    .badge-error {
        background-color: #fee2e2;
        color: #dc2626;
        border-color: #fca5a5;
    }

    .badge-primary {
        background-color: #f3f4f6;
        color: #4b5563;
        border-color: #d1d5db;
    }

    /* Land Use Badge Colors - Matching Primary Applications */
    .badge-residential {
        background-color: #dbeafe;
        color: #2563eb;
        border-color: #93c5fd;
    }

    .badge-commercial {
        background-color: #d1fae5;
        color: #059669;
        border-color: #86efac;
    }

    .badge-industrial {
        background-color: #fee2e2;
        color: #dc2626;
        border-color: #fca5a5;
    }

    /* Responsive Dropdown System */
    .dropdown-container {
        position: relative;
        display: inline-block;
    }

    .dropdown-trigger {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border: 1px solid var(--gray-300);
        background: white;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s ease;
        color: var(--gray-500);
    }

    .dropdown-trigger:hover {
        background: var(--primary-600);
        border-color: var(--primary-600);
        color: white;
        transform: scale(1.05);
        box-shadow: var(--shadow-md);
    }

    .dropdown-menu {
        position: fixed;
        z-index: 9999;
        min-width: 12rem;
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--gray-200);
        padding: 0.5rem;
        display: none;
        max-height: 300px;
        overflow-y: auto;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-menu::before {
        content: '';
        position: absolute;
        top: -6px;
        right: 12px;
        width: 12px;
        height: 12px;
        background: white;
        border: 1px solid var(--gray-200);
        border-bottom: none;
        border-right: none;
        transform: rotate(45deg);
        z-index: 1;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--gray-700);
        text-decoration: none;
        border-radius: var(--radius-md);
        transition: all 0.15s ease;
        font-size: 0.875rem;
        font-weight: 500;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
        text-align: left;
        margin-bottom: 0.25rem;
        white-space: nowrap;
    }

    .dropdown-item:last-child {
        margin-bottom: 0;
    }

    .dropdown-item:hover:not(.disabled) {
        background: var(--primary-50);
        color: var(--primary-700);
        transform: translateX(0.125rem);
    }

    .dropdown-item.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        color: var(--gray-400);
    }

    .dropdown-item.disabled:hover {
        background: none;
        transform: none;
    }

    .dropdown-item i {
        width: 1rem;
        height: 1rem;
        flex-shrink: 0;
    }

    /* Ensure table cells don't clip dropdowns */
    .table-cell.overflow-visible {
        overflow: visible !important;
    }

    .table-wrapper {
        overflow: visible;
    }

    .table-wrapper .overflow-x-auto {
        overflow: visible;
    }

    /* Modern Info Icon */
    .info-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.25rem;
        height: 1.25rem;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        border-radius: 50%;
        font-size: 0.6875rem;
        font-weight: 700;
        margin-left: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid white;
        box-shadow: var(--shadow-sm);
    }

    .info-icon:hover {
        transform: scale(1.1);
        box-shadow: var(--shadow-md);
    }

    /* Modern Search System */
    .search-container {
        position: relative;
        flex: 1;
        max-width: 20rem;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: white;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgb(34 197 94 / 0.1);
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
        width: 1rem;
        height: 1rem;
    }

    /* Modern DataTables Integration */
    .dataTables_wrapper {
        font-family: inherit;
    }

    .dataTables_info {
        color: var(--gray-600);
        font-size: 0.875rem;
        font-weight: 500;
        margin-top: 1rem;
    }

    .dataTables_paginate {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
        gap: 0.25rem;
    }

    .dataTables_paginate .paginate_button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0 0.75rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        background: white;
        color: var(--gray-700);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.15s ease;
    }

    .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: var(--primary-600);
        border-color: var(--primary-600);
        color: white;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .dataTables_paginate .paginate_button.current {
        background: var(--primary-600);
        border-color: var(--primary-600);
        color: white;
        box-shadow: var(--shadow-md);
    }

    .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .dataTables_paginate .paginate_button.disabled:hover {
        background: white;
        border-color: var(--gray-300);
        color: var(--gray-700);
        transform: none;
        box-shadow: none;
    }

    /* Hide default DataTables elements */
    .dataTables_filter,
    .dataTables_length {
        display: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .rofo-content {
            padding: 1rem;
        }

        .filter-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .table-cell {
            padding: 0.75rem 0.5rem;
            font-size: 0.8125rem;
        }

        .table-header {
            padding: 0.75rem 0.5rem;
            font-size: 0.75rem;
        }

        .dropdown-menu {
            min-width: 10rem;
            right: 0;
        }

        .search-container {
            max-width: none;
        }
    }

    /* Loading States */
    .loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .loading-spinner {
        width: 2rem;
        height: 2rem;
        border: 3px solid var(--gray-200);
        border-top: 3px solid var(--primary-600);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Animation Classes */
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(0.5rem);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-up {
        animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(1rem);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
 


<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include($headerPartial ?? 'admin.header')
    
    <!-- Main Content -->
    <div class="p-6">
        <div class="rofo-container fade-in">
            <!-- Header Section -->
            

            <!-- Content Section -->
            <div class="rofo-content">
                <!-- Controls Bar -->
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between mb-6">
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                        <button onclick="toggleFilters()" class="btn btn-primary btn-sm">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                            <span>Filters</span>
                        </button>
                        
                        <div class="search-container">
                            <input 
                                type="text" 
                                id="searchInput"
                                placeholder="Search records..." 
                                class="search-input"
                            >
                            <i data-lucide="search" class="search-icon"></i>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button class="btn btn-secondary btn-sm">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Export
                        </button>
                    </div>
                </div>

                <!-- Filter Panel -->
                <div id="filterPanel" class="filter-panel">
                    <div class="filter-grid">
                        <div class="form-group">
                            <label class="form-label">Land Use</label>
                            <select id="landUseFilter" class="form-select">
                                <option value="">All</option>
                                <option value="Residential">Residential</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Industrial">Industrial</option>
                                <option value="Mixed Use">Mixed Use</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Date From</label>
                            <input type="date" id="dateFromFilter" class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Date To</label>
                            <input type="date" id="dateToFilter" class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <button onclick="resetFilters()" class="btn btn-secondary">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="tab-container">
                    <div class="tab-list">
                        <button 
                            onclick="setActiveTab('not-generated')" 
                            id="tab-not-generated"
                            class="tab-button active"
                        >
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            <span>Not Generated RoFO</span>
                        </button>
                        <button 
                            onclick="setActiveTab('generated')" 
                            id="tab-generated"
                            class="tab-button"
                        >
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Generated RoFO</span>
                        </button>
                    </div>
                </div>

                <!-- Not Generated RoFO Table -->
                <div id="not-generated-table" class="table-wrapper slide-up">
                    <table id="notGeneratedTable" class="modern-table">
                        <thead>
                            <tr>
                                <th class="table-header">ST FileNo</th>
                                <th class="table-header">Scheme No</th>
                                <th class="table-header">Unit Owner</th>
                                <th class="table-header">LGA</th>
                                <th class="table-header">Block/Floor/Unit</th>
                                <th class="table-header">Land Use</th>
                                <th class="table-header">ST Memo Status</th>
                                <th class="table-header">Date Created</th>
                                <th class="table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subapplications->filter(function($app) { return empty($app->rofo_no); }) as $unitApplication)
                            <tr class="table-row" 
                                data-land-use="{{ strtolower($unitApplication->land_use ?? '') }}" 
                                data-date="{{ $unitApplication->created_at ? date('Y-m-d', strtotime($unitApplication->created_at)) : '' }}">
                                <td class="table-cell font-medium">{{ $unitApplication->fileno ?? 'N/A' }}</td>
                                <td class="table-cell">{{ $unitApplication->scheme_no ?? 'N/A' }}</td>
                                <td class="table-cell">
                                    @if(!empty($unitApplication->multiple_owners_names) && json_decode($unitApplication->multiple_owners_names))
                                        @php
                                            $owners = json_decode($unitApplication->multiple_owners_names);
                                            $firstOwner = isset($owners[0]) ? $owners[0] : 'N/A';
                                            $allOwners = json_encode($owners);
                                        @endphp
                                        {{ $firstOwner }}
                                        <span class="info-icon" onclick="showOwners({{ $allOwners }})">i</span>
                                    @else
                                        {{ $unitApplication->owner_name ?? 'N/A' }}
                                    @endif
                                </td>
                                <td class="table-cell">{{ $unitApplication->property_lga ?? 'N/A' }}</td>
                                <td class="table-cell">{{ $unitApplication->block_number ?? '' }}-{{ $unitApplication->floor_number ?? '' }}-{{ $unitApplication->unit_number ?? '' }}</td>
                                <td class="table-cell">{{ $unitApplication->land_use ?? 'N/A' }}</td>
                                <td class="table-cell">
                                    @if($unitApplication->has_st_memo ?? false)
                                        <span class="badge badge-success">
                                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                                            Generated
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                            Not Generated
                                        </span>
                                    @endif
                                </td>
                                <td class="table-cell">{{ $unitApplication->created_at ? date('d-m-Y', strtotime($unitApplication->created_at)) : 'N/A' }}</td>
                                <td class="table-cell">
                                    <div class="dropdown-container">
                                        <button onclick="toggleDropdown(this)" class="dropdown-trigger" type="button">
                                            <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                                        </button>
                                        
                                        <div class="dropdown-menu">
                                            <a href="{{ route('sectionaltitling.viewrecorddetail_sub', $unitApplication->id) }}" class="dropdown-item">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                                <span>View Record</span>
                                            </a>
                                            @if($unitApplication->has_st_memo ?? false)
                                                <a href="{{ route('programmes.generate_rofo', $unitApplication->id) }}" class="dropdown-item">
                                                    <i data-lucide="file-plus" class="w-4 h-4"></i>
                                                    <span>Generate RoFO</span>
                                                </a>
                                            @else
                                                <div class="dropdown-item disabled" title="ST Memo prerequisite required">
                                                    <i data-lucide="file-plus" class="w-4 h-4"></i>
                                                    <span>Generate RoFO</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="table-empty-state">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="empty-icon">
                                            <i data-lucide="inbox" class="w-full h-full"></i>
                                        </div>
                                        <div class="empty-text">No records pending RoFO generation</div>
                                        <p class="text-sm text-gray-400 mt-1">All applications have been processed or no applications exist yet.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Generated RoFO Table -->
                <div id="generated-table" class="table-wrapper slide-up" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-xs">
                                    <th class="table-header text-green-500">ST FileNo</th>
                                    <th class="table-header text-green-500">RoFO No</th>
                                    <th class="table-header text-green-500">Scheme No</th>
                                    <th class="table-header text-green-500">Unit Owner</th>
                                    <th class="table-header text-green-500">LGA</th>
                                    <th class="table-header text-green-500">Block/Floor/Unit</th>
                                    <th class="table-header text-green-500">Land Use</th>
                                    <th class="table-header text-green-500">Date Created</th>
                                    <th class="table-header text-green-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($subapplications->filter(function($app) { return !empty($app->rofo_no); }) as $unitApplication)
                                <tr class="text-xs table-row" 
                                    data-land-use="{{ strtolower($unitApplication->land_use ?? '') }}" 
                                    data-date="{{ $unitApplication->created_at ? date('Y-m-d', strtotime($unitApplication->created_at)) : '' }}">
                                    <td class="table-cell">
                                        <div class="truncate max-w-[120px]" title="{{ $unitApplication->fileno }}">
                                            {{ $unitApplication->fileno ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="truncate max-w-[120px] font-medium text-green-600" title="{{ $unitApplication->rofo_no }}">
                                            {{ $unitApplication->rofo_no ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="truncate max-w-[120px]" title="{{ $unitApplication->scheme_no }}">
                                            {{ $unitApplication->scheme_no ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex items-center">
                                            <span class="truncate max-w-[120px]">
                                                @if(!empty($unitApplication->multiple_owners_names) && json_decode($unitApplication->multiple_owners_names))
                                                    @php
                                                        $owners = json_decode($unitApplication->multiple_owners_names);
                                                        $firstOwner = isset($owners[0]) ? $owners[0] : 'N/A';
                                                        $allOwners = json_encode($owners);
                                                    @endphp
                                                    {{ $firstOwner }}
                                                    <span class="ml-1 cursor-pointer text-blue-500"
                                                        onclick="showOwners({{ $allOwners }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                @else
                                                    {{ $unitApplication->owner_name ?? 'N/A' }}
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td class="table-cell">{{ $unitApplication->property_lga ?? 'N/A' }}</td>
                                    <td class="table-cell">{{ $unitApplication->block_number ?? '' }}-{{ $unitApplication->floor_number ?? '' }}-{{ $unitApplication->unit_number ?? '' }}</td>
                                    <td class="table-cell">
                                        @if($unitApplication->land_use)
                                            @php
                                                $landUseClass = '';
                                                switch(strtolower($unitApplication->land_use)) {
                                                    case 'residential':
                                                        $landUseClass = 'badge-residential';
                                                        break;
                                                    case 'commercial':
                                                        $landUseClass = 'badge-commercial';
                                                        break;
                                                    case 'industrial':
                                                        $landUseClass = 'badge-industrial';
                                                        break;
                                                    default:
                                                        $landUseClass = 'badge-primary';
                                                }
                                            @endphp
                                            <span class="badge {{ $landUseClass }}">
                                                <i data-lucide="map-pin" class="w-3 h-3 mr-1"></i>
                                                {{ $unitApplication->land_use }}
                                            </span>
                                        @else
                                            <span class="badge badge-primary">N/A</span>
                                        @endif
                                    </td>
                                    <td class="table-cell">{{ $unitApplication->created_at ? date('d-m-Y', strtotime($unitApplication->created_at)) : 'N/A' }}</td>
                                    <td class="table-cell overflow-visible relative">
                                        <div class="dropdown-container">
                                            <button onclick="toggleDropdown(this)" class="dropdown-trigger" type="button">
                                                <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                                            </button>
                                            
                                            <div class="dropdown-menu">
                                                <a href="{{ route('sectionaltitling.viewrecorddetail_sub', $unitApplication->id) }}" class="dropdown-item">
                                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                                    <span>View Application</span>
                                                </a>
                                                <a href="{{ route('programmes.view_rofo', $unitApplication->id) }}" class="dropdown-item">
                                                    <i data-lucide="clipboard" class="w-4 h-4"></i>
                                                    <span>View RoFO</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="table-empty-state">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="empty-icon">
                                                <i data-lucide="file-check" class="w-full h-full"></i>
                                            </div>
                                            <div class="empty-text">No generated RoFO applications found</div>
                                            <p class="text-sm text-gray-400 mt-1">RoFO documents will appear here once generated from applications.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Page Footer -->
    @include($footerPartial ?? 'admin.footer')
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
// Global variables
let notGeneratedTable = null;
let generatedTable = null;
let activeTab = 'not-generated';
let showFilters = false;

// Responsive Dropdown Toggle Function
function toggleDropdown(button) {
    console.log('Dropdown button clicked!');
    
    // Close all other dropdowns first
    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
        menu.classList.remove('show');
    });
    
    // Find the dropdown menu for this button
    const dropdownMenu = button.nextElementSibling;
    
    if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
        if (dropdownMenu.classList.contains('show')) {
            dropdownMenu.classList.remove('show');
        } else {
            // Position the dropdown menu
            positionDropdown(button, dropdownMenu);
            dropdownMenu.classList.add('show');
        }
        console.log('Dropdown toggled, now showing:', dropdownMenu.classList.contains('show'));
    }
}

// Position dropdown menu responsively
function positionDropdown(trigger, menu) {
    const triggerRect = trigger.getBoundingClientRect();
    const menuWidth = 192; // 12rem = 192px
    const menuHeight = menu.scrollHeight || 200;
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
    
    let top = triggerRect.bottom + scrollTop + 8; // 8px margin
    let left = triggerRect.right + scrollLeft - menuWidth;
    
    // Adjust if dropdown would go off the right edge
    if (left < scrollLeft + 16) {
        left = triggerRect.left + scrollLeft;
    }
    
    // Adjust if dropdown would go off the bottom edge
    if (top + menuHeight > viewportHeight + scrollTop - 16) {
        top = triggerRect.top + scrollTop - menuHeight - 8;
    }
    
    // Ensure dropdown doesn't go off the top edge
    if (top < scrollTop + 16) {
        top = triggerRect.bottom + scrollTop + 8;
    }
    
    menu.style.top = `${top}px`;
    menu.style.left = `${left}px`;
    
    // Adjust arrow position based on menu position
    const arrow = menu.querySelector('::before');
    if (left === triggerRect.left + scrollLeft) {
        // Menu is left-aligned with trigger, move arrow to the right
        menu.style.setProperty('--arrow-right', '12px');
    } else {
        // Menu is right-aligned with trigger, keep arrow at default position
        menu.style.setProperty('--arrow-right', '12px');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown-container')) {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Filter Functions
function toggleFilters() {
    showFilters = !showFilters;
    const filterPanel = document.getElementById('filterPanel');
    if (showFilters) {
        filterPanel.classList.add('active');
    } else {
        filterPanel.classList.remove('active');
    }
}

function resetFilters() {
    document.getElementById('landUseFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';
    document.getElementById('searchInput').value = '';
    
    if (notGeneratedTable) {
        notGeneratedTable.search('').draw();
    }
    if (generatedTable) {
        generatedTable.search('').draw();
    }
}

// Tab Functions
function setActiveTab(tab) {
    activeTab = tab;
    
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById('tab-' + tab).classList.add('active');
    
    // Show/hide tables
    if (tab === 'not-generated') {
        document.getElementById('not-generated-table').style.display = 'block';
        document.getElementById('generated-table').style.display = 'none';
    } else {
        document.getElementById('not-generated-table').style.display = 'none';
        document.getElementById('generated-table').style.display = 'block';
    }
    
    // Refresh icons
    setTimeout(() => {
        lucide.createIcons();
    }, 100);
}

// Show Owners Function
function showOwners(owners) {
    let ownersList = '';
    owners.forEach(owner => {
        ownersList += `<li class="py-1">${owner}</li>`;
    });
    
    Swal.fire({
        title: 'All Owners',
        html: `<ul class="text-left list-disc list-inside space-y-1">${ownersList}</ul>`,
        icon: 'info',
        confirmButtonText: 'Close',
        confirmButtonColor: '#16a34a',
        customClass: {
            popup: 'rounded-lg',
            title: 'text-lg font-semibold',
            content: 'text-sm'
        }
    });
}

// Initialize DataTables
function initializeTables() {
    // Initialize Not Generated Table
    if ($.fn.DataTable.isDataTable('#notGeneratedTable')) {
        $('#notGeneratedTable').DataTable().destroy();
    }
    
    notGeneratedTable = $('#notGeneratedTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>Excel',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i data-lucide="file-text" class="w-4 h-4 mr-2"></i>PDF',
                className: 'btn btn-secondary btn-sm'
            }
        ],
        columnDefs: [
            { orderable: false, targets: -1 },
            { className: 'text-center', targets: -1 }
        ],
        language: {
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        initComplete: function() {
            $('.dataTables_filter').hide();
            lucide.createIcons();
        }
    });

    // Initialize Generated Table
    if ($.fn.DataTable.isDataTable('#generatedTable')) {
        $('#generatedTable').DataTable().destroy();
    }
    
    generatedTable = $('#generatedTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>Excel',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i data-lucide="file-text" class="w-4 h-4 mr-2"></i>PDF',
                className: 'btn btn-secondary btn-sm'
            }
        ],
        columnDefs: [
            { orderable: false, targets: -1 },
            { className: 'text-center', targets: -1 }
        ],
        language: {
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        initComplete: function() {
            $('.dataTables_filter').hide();
            lucide.createIcons();
        }
    });
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value;
    if (activeTab === 'not-generated' && notGeneratedTable) {
        notGeneratedTable.search(searchTerm).draw();
    } else if (activeTab === 'generated' && generatedTable) {
        generatedTable.search(searchTerm).draw();
    }
});

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    
    // Initialize DataTables
    initializeTables();
    
    // Initialize Lucide icons
    lucide.createIcons();
    
    console.log('Initialization complete');
});
</script>
@endsection