@extends('layouts.app')
@section('page-title')
    {{ __('Welcome to KLAES - Kano State Land Administration Enterprise 
System') }}
@endsection

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-card {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hover-scale {
            transition: transform 0.2s ease-in-out;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-badge-approved {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-badge-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .status-badge-in-progress {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .status-badge-rejected {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .module-badge-dashboard { background-color: #3b82f6; color: white; }
        .module-badge-customer { background-color: #10b981; color: white; }
        .module-badge-programmes { background-color: #8b5cf6; color: white; }
        .module-badge-info-products { background-color: #f59e0b; color: white; }
        .module-badge-instrument { background-color: #ef4444; color: white; }
        .module-badge-file-registry { background-color: #06b6d4; color: white; }

        .module-icon-dashboard { color: #3b82f6; }
        .module-icon-customer { color: #10b981; }
        .module-icon-programmes { color: #8b5cf6; }
        .module-icon-info-products { color: #f59e0b; }
        .module-icon-instrument { color: #ef4444; }
        .module-icon-file-registry { color: #06b6d4; }
        .module-icon-systems { color: #f97316; }
        .module-icon-legacy { color: #6b7280; }
        .module-icon-admin { color: #6366f1; }

        .text-success { color: #10b981; }
        .text-warning { color: #f59e0b; }
        .text-pending { color: #8b5cf6; }
        .text-destructive { color: #ef4444; }
        .text-info { color: #06b6d4; }

        .bg-success { background-color: #10b981; }
        .bg-warning { background-color: #f59e0b; }
        .bg-pending { background-color: #8b5cf6; }
        .bg-destructive { background-color: #ef4444; }
        .bg-info { background-color: #06b6d4; }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 50;
            min-width: 12rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 0.25rem 0;
        }

        .dropdown-menu.show {
            display: block;
        }

        .tooltip {
            position: relative;
        }

        .tooltip .tooltip-content {
            visibility: hidden;
            position: absolute;
            z-index: 50;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1f2937;
            color: white;
            text-align: center;
            border-radius: 0.375rem;
            padding: 0.5rem;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        .tooltip:hover .tooltip-content {
            visibility: visible;
        }

        .progress-bar {
            background-color: #e5e7eb;
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: #3b82f6;
            transition: width 0.3s ease;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-trigger {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }

        .tab-trigger.active {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced Chart styles */
        .chart-bar {
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .chart-bar:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .chart-tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 100;
            min-width: 200px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .chart-tooltip.show {
            opacity: 1;
        }

        /* Priority indicators */
        .priority-high { background-color: #ef4444; }
        .priority-medium { background-color: #f59e0b; }
        .priority-low { background-color: #10b981; }

        /* Enhanced shadows */
        .shadow-enhanced {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Decorative elements */
        .decorative-circle {
            position: absolute;
            top: 0;
            right: 0;
            width: 5rem;
            height: 5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(2.5rem, -2.5rem);
        }

        /* Enhanced Analytics Chart Styles */
        .analytics-chart-container {
            position: relative;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .chart-grid-line {
            stroke: #e2e8f0;
            stroke-width: 1;
            stroke-dasharray: 2,2;
        }

        .chart-axis-line {
            stroke: #64748b;
            stroke-width: 1.5;
        }

        .chart-data-point {
            fill: #3b82f6;
            stroke: #ffffff;
            stroke-width: 2;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chart-data-point:hover {
            fill: #1d4ed8;
            r: 6;
            stroke-width: 3;
        }

        .chart-trend-line {
            fill: none;
            stroke: url(#chartGradient);
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .chart-area-fill {
            fill: url(#chartAreaGradient);
            opacity: 0.3;
        }

        .chart-legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chart-legend-item:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .chart-legend-item.inactive {
            opacity: 0.4;
        }

        .chart-stats-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            transition: all 0.2s ease;
        }

        .chart-stats-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .trend-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .trend-up {
            background-color: #dcfce7;
            color: #166534;
        }

        .trend-down {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .trend-neutral {
            background-color: #f3f4f6;
            color: #374151;
        }

        /* Animated chart bars */
        .animated-bar {
            animation: growBar 1s ease-out forwards;
            transform-origin: bottom;
        }

        @keyframes growBar {
            from {
                transform: scaleY(0);
            }
            to {
                transform: scaleY(1);
            }
        }

        /* Chart controls */
        .chart-control-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background: white;
            color: #374151;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chart-control-button:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background-color: #f8fafc;
        }

        .chart-control-button.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        /* Performance metrics */
        .performance-metric {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .performance-metric:hover {
            background-color: #f8fafc;
        }

        .metric-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }

        .metric-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .metric-change.positive {
            color: #059669;
        }

        .metric-change.negative {
            color: #dc2626;
        }
    </style>
@include('sectionaltitling.partials.assets.css')
@section('content')
@if (auth()->check() && is_null(auth()->user()->is_password_change))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          title: 'Password Change Required',
          text: 'You must change your password before accessing the dashboard.',
          icon: 'warning',
          confirmButtonText: 'Go to Profile'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "{{ route('profile.index') }}";
          }
        });
      });
    </script>
@endif
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">
          <div class="flex-1 space-y-6 p-6 animate-fade-in">
        <!-- System Status Banner -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-100 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div id="system-status-indicator" class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="text-sm font-medium text-gray-700">System Status:</span>
                        <span id="system-status-text" class="text-sm font-semibold text-green-600">All Systems Operational</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div id="db-status-indicator" class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="text-sm text-gray-600">Database:</span>
                        <span id="db-status-text" class="text-sm font-medium text-green-600">Connected</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div id="api-status-indicator" class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="text-sm text-gray-600">API Services:</span>
                        <span id="api-status-text" class="text-sm font-medium text-green-600">Online</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2 text-xs text-gray-500">
                    <i data-lucide="clock" class="h-3 w-3"></i>
                    <span>Last updated: <span id="last-updated">{{ date('H:i:s') }}</span></span>
                    <button onclick="refreshSystemStatus()" class="ml-2 p-1 hover:bg-blue-100 rounded">
                        <i data-lucide="refresh-cw" class="h-3 w-3"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
            <div class="gradient-card hover-scale rounded-lg p-6 shadow-enhanced" style="--gradient-start: hsl(221.2, 83.2%, 53.3%); --gradient-end: hsl(262, 83.3%, 57.8%);">
                <div class="decorative-circle"></div>
                <div class="flex items-center justify-between mb-2 relative z-10">
                    <h3 class="text-sm font-medium text-white">Total Applications</h3>
                    <i data-lucide="file-text" class="h-4 w-4 text-white/80"></i>
                </div>
                <div class="text-2xl font-bold text-white relative z-10" id="total-applications-count">
                    <span class="loading-shimmer">Initializing...</span>
                </div>
                <p class="text-xs text-white/80 flex items-center mt-1 relative z-10">
                    <span class="text-emerald-300 flex items-center mr-1">
                        <i data-lucide="trending-up" class="h-3 w-3 mr-1"></i>
                        <span id="applications-trend">--</span>%
                    </span>
                    from last month
                </p>
            </div>

            <div class="gradient-card hover-scale rounded-lg p-6 shadow-enhanced" style="--gradient-start: hsl(38, 92%, 50%); --gradient-end: hsl(262, 83.3%, 57.8%);">
                <div class="decorative-circle"></div>
                <div class="flex items-center justify-between mb-2 relative z-10">
                    <h3 class="text-sm font-medium text-white">Pending Approvals</h3>
                    <i data-lucide="clock" class="h-4 w-4 text-white/80"></i>
                </div>
                <div class="text-2xl font-bold text-white relative z-10" id="pending-approvals-count">
                    <span class="loading-shimmer">Loading...</span>
                </div>
                <p class="text-xs text-white/80 flex items-center mt-1 relative z-10">
                    <span class="text-red-300 flex items-center mr-1">
                        <i data-lucide="trending-down" class="h-3 w-3 mr-1"></i>
                        <span id="pending-trend">--</span>%
                    </span>
                    from last month
                </p>
            </div>

            <div class="gradient-card hover-scale rounded-lg p-6 shadow-enhanced" style="--gradient-start: hsl(142.1, 76.2%, 36.3%); --gradient-end: hsl(198, 93%, 60%);">
                <div class="decorative-circle"></div>
                <div class="flex items-center justify-between mb-2 relative z-10">
                    <h3 class="text-sm font-medium text-white">Registered Properties</h3>
                    <i data-lucide="building-2" class="h-4 w-4 text-white/80"></i>
                </div>
                <div class="text-2xl font-bold text-white relative z-10" id="registered-properties-count">
                    <span class="loading-shimmer">Connecting...</span>
                </div>
                <p class="text-xs text-white/80 flex items-center mt-1 relative z-10">
                    <span class="text-emerald-300 flex items-center mr-1">
                        <i data-lucide="trending-up" class="h-3 w-3 mr-1"></i>
                        <span id="properties-trend">--</span>%
                    </span>
                    from last month
                </p>
            </div>

            <div class="gradient-card hover-scale rounded-lg p-6 shadow-enhanced" style="--gradient-start: hsl(262, 83.3%, 57.8%); --gradient-end: hsl(339.6, 82.2%, 51.6%);">
                <div class="decorative-circle"></div>
                <div class="flex items-center justify-between mb-2 relative z-10">
                    <h3 class="text-sm font-medium text-white">Active Users</h3>
                    <i data-lucide="users" class="h-4 w-4 text-white/80"></i>
                </div>
                <div class="text-2xl font-bold text-white relative z-10" id="active-users-count">
                    <span class="loading-shimmer">Syncing...</span>
                </div>
                <p class="text-xs text-white/80 flex items-center mt-1 relative z-10">
                    <span class="text-emerald-300 flex items-center mr-1">
                        <i data-lucide="trending-up" class="h-3 w-3 mr-1"></i>
                        <span id="users-trend">--</span>%
                    </span>
                    from last month
                </p>
            </div>

            <div class="gradient-card hover-scale rounded-lg p-6 shadow-enhanced" style="--gradient-start: hsl(339.6, 82.2%, 51.6%); --gradient-end: hsl(198, 93%, 60%);">
                <div class="decorative-circle"></div>
                <div class="flex items-center justify-between mb-2 relative z-10">
                    <h3 class="text-sm font-medium text-white">System Health</h3>
                    <i data-lucide="activity" class="h-4 w-4 text-white/80"></i>
                </div>
                <div class="text-2xl font-bold text-white relative z-10" id="system-health-score">
                    <span class="loading-shimmer">Checking...</span>
                </div>
                <p class="text-xs text-white/80 flex items-center mt-1 relative z-10">
                    <span class="text-emerald-300 flex items-center mr-1">
                        <i data-lucide="shield-check" class="h-3 w-3 mr-1"></i>
                        <span id="health-status">Excellent</span>
                    </span>
                    performance
                </p>
            </div>
        </div>

        <!-- Real-time Notifications -->
        <div id="notification-banner" class="hidden bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="info" class="h-5 w-5 text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700" id="notification-text">
                        System notification will appear here...
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button onclick="dismissNotification()" class="inline-flex bg-blue-50 rounded-md p-1.5 text-blue-500 hover:bg-blue-100">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="space-y-6">
            <div class="bg-gray-100 p-1 rounded-lg inline-flex">
                <button class="tab-trigger active" data-tab="overview">Overview</button>
                <button class="tab-trigger" data-tab="applications">Applications</button>
                <button class="tab-trigger" data-tab="documents">Information Products</button>
                <button class="tab-trigger" data-tab="analytics">Analytics</button>
                <button class="tab-trigger" data-tab="modules">Modules</button>
            </div>

            <!-- Overview Tab -->
            <div id="overview" class="tab-content active space-y-6">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                    <!-- Enhanced Applications Overview Chart -->
                    <div class="lg:col-span-4 bg-white rounded-lg border-0 shadow-enhanced overflow-hidden">
                        <!-- Chart Header -->
                        <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900">Applications Analytics</h3>
                                    <p class="text-gray-600">Comprehensive application tracking and performance metrics</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <select id="chart-period" class="px-3 py-2 border rounded-md text-sm bg-white">
                                        <option value="week">Last 7 Days</option>
                                        <option value="month">Last 30 Days</option>
                                        <option value="quarter">Last Quarter</option>
                                        <option value="year">Last Year</option>
                                    </select>
                                    <div class="relative">
                                        <button class="chart-control-button" onclick="toggleDropdown('filter-dropdown')">
                                            <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                                            Filter
                                        </button>
                                        <div id="filter-dropdown" class="dropdown-menu">
                                            <div class="px-3 py-2 text-sm font-medium text-gray-700 border-b">Filter by Status</div>
                                            <button class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 flex items-center" onclick="filterChart('approved')">
                                                <i data-lucide="check-circle-2" class="mr-2 h-4 w-4 text-success"></i>
                                                Approved Only
                                            </button>
                                            <button class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 flex items-center" onclick="filterChart('pending')">
                                                <i data-lucide="clock" class="mr-2 h-4 w-4 text-warning"></i>
                                                Pending Only
                                            </button>
                                            <button class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 flex items-center" onclick="filterChart('rejected')">
                                                <i data-lucide="x-circle" class="mr-2 h-4 w-4 text-destructive"></i>
                                                Rejected Only
                                            </button>
                                            <button class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 flex items-center" onclick="filterChart('all')">
                                                <i data-lucide="eye" class="mr-2 h-4 w-4 text-gray-600"></i>
                                                Show All
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats Row -->
                            <div class="grid grid-cols-4 gap-4">
                                <div class="chart-stats-card">
                                    <div class="text-2xl font-bold text-blue-600" id="total-applications">720</div>
                                    <div class="text-xs text-gray-600">Total Applications</div>
                                    <div class="trend-indicator trend-up mt-1">
                                        <i data-lucide="trending-up" class="h-3 w-3"></i>
                                        +12.5%
                                    </div>
                                </div>
                                <div class="chart-stats-card">
                                    <div class="text-2xl font-bold text-green-600" id="avg-processing">2.8</div>
                                    <div class="text-xs text-gray-600">Avg. Days</div>
                                    <div class="trend-indicator trend-up mt-1">
                                        <i data-lucide="trending-down" class="h-3 w-3"></i>
                                        -0.4 days
                                    </div>
                                </div>
                                <div class="chart-stats-card">
                                    <div class="text-2xl font-bold text-purple-600" id="success-rate">94.2%</div>
                                    <div class="text-xs text-gray-600">Success Rate</div>
                                    <div class="trend-indicator trend-up mt-1">
                                        <i data-lucide="trending-up" class="h-3 w-3"></i>
                                        +2.1%
                                    </div>
                                </div>
                                <div class="chart-stats-card">
                                    <div class="text-2xl font-bold text-orange-600" id="peak-day">Thu</div>
                                    <div class="text-xs text-gray-600">Peak Day</div>
                                    <div class="trend-indicator trend-neutral mt-1">
                                        <i data-lucide="calendar" class="h-3 w-3"></i>
                                        136 apps
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced SVG Chart Container -->
                        <div class="p-6">
                            <div class="analytics-chart-container h-80 p-4">
                                <svg id="applications-chart" width="100%" height="100%" viewBox="0 0 800 300">
                                    <!-- Gradient Definitions -->
                                    <defs>
                                        <linearGradient id="chartGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                                            <stop offset="50%" style="stop-color:#8b5cf6;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#06b6d4;stop-opacity:1" />
                                        </linearGradient>
                                        <linearGradient id="chartAreaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0.3" />
                                            <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0.05" />
                                        </linearGradient>
                                        <filter id="dropShadow" x="-20%" y="-20%" width="140%" height="140%">
                                            <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.1"/>
                                        </filter>
                                    </defs>
                                    
                                    <!-- Grid Lines -->
                                    <g id="grid-lines">
                                        <!-- Horizontal grid lines will be generated by JavaScript -->
                                    </g>
                                    
                                    <!-- Chart Area -->
                                    <g id="chart-area">
                                        <!-- Chart elements will be generated by JavaScript -->
                                    </g>
                                    
                                    <!-- Axes -->
                                    <g id="axes">
                                        <!-- X and Y axes will be generated by JavaScript -->
                                    </g>
                                    
                                    <!-- Data visualization -->
                                    <g id="data-visualization">
                                        <!-- Bars, lines, and points will be generated by JavaScript -->
                                    </g>
                                </svg>
                            </div>

                            <!-- Interactive Legend -->
                            <div class="flex items-center justify-center gap-6 mt-6 pt-4 border-t bg-gray-50 rounded-b-lg -mx-6 -mb-6 px-6 py-4">
                                <div class="chart-legend-item" onclick="toggleChartSeries('sectional')" id="legend-sectional">
                                    <div class="h-3 w-3 rounded-full bg-gradient-to-r from-blue-500 to-blue-400"></div>
                                    <span class="text-sm font-medium">Sectional Titling</span>
                                    <span class="text-sm text-gray-500" id="total-sectional">(318)</span>
                                    <div class="trend-indicator trend-up ml-2">
                                        <i data-lucide="trending-up" class="h-3 w-3"></i>
                                        +8%
                                    </div>
                                </div>
                                <div class="chart-legend-item" onclick="toggleChartSeries('recertification')" id="legend-recertification">
                                    <div class="h-3 w-3 rounded-full bg-gradient-to-r from-green-500 to-green-400"></div>
                                    <span class="text-sm font-medium">Recertification</span>
                                    <span class="text-sm text-gray-500" id="total-recertification">(226)</span>
                                    <div class="trend-indicator trend-up ml-2">
                                        <i data-lucide="trending-up" class="h-3 w-3"></i>
                                        +15%
                                    </div>
                                </div>
                                <div class="chart-legend-item" onclick="toggleChartSeries('allocation')" id="legend-allocation">
                                    <div class="h-3 w-3 rounded-full bg-gradient-to-r from-yellow-500 to-yellow-400"></div>
                                    <span class="text-sm font-medium">Allocation</span>
                                    <span class="text-sm text-gray-500" id="total-allocation">(176)</span>
                                    <div class="trend-indicator trend-down ml-2">
                                        <i data-lucide="trending-down" class="h-3 w-3"></i>
                                        -3%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart Controls -->
                        <div class="flex justify-between items-center p-6 pt-0">
                            <div class="flex items-center gap-2">
                                <button class="chart-control-button" onclick="downloadChart()">
                                    <i data-lucide="download" class="h-3.5 w-3.5"></i>
                                    Export Data
                                </button>
                                <button class="chart-control-button" onclick="refreshChart()">
                                    <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                                    Refresh
                                </button>
                                <button class="chart-control-button" onclick="showInsights()">
                                    <i data-lucide="lightbulb" class="h-3.5 w-3.5"></i>
                                    Insights
                                </button>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="chart-control-button" onclick="toggleChartView()" id="chart-view-toggle">
                                    <i data-lucide="bar-chart-3" class="h-3.5 w-3.5"></i>
                                    <span id="chart-view-text">Line View</span>
                                </button>
                                <button class="chart-control-button" onclick="showChartDetails()">
                                    <i data-lucide="info" class="h-3.5 w-3.5"></i>
                                    Details
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Chart Tooltip -->
                    <div id="enhanced-chart-tooltip" class="chart-tooltip">
                        <div class="font-semibold text-white mb-2" id="tooltip-day"></div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                    <span class="text-xs">Sectional Titling:</span>
                                </div>
                                <span id="tooltip-sectional" class="font-medium text-xs">0</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                                    <span class="text-xs">Recertification:</span>
                                </div>
                                <span id="tooltip-recertification" class="font-medium text-xs">0</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                                    <span class="text-xs">Allocation:</span>
                                </div>
                                <span id="tooltip-allocation" class="font-medium text-xs">0</span>
                            </div>
                            <div class="border-t border-gray-600 pt-2 mt-2">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-xs font-medium">Total Applications:</span>
                                    <span id="tooltip-total" class="font-bold text-sm">0</span>
                                </div>
                                <div class="flex items-center justify-between gap-4 mt-1">
                                    <span class="text-xs">Success Rate:</span>
                                    <span id="tooltip-success-rate" class="text-xs font-medium text-green-400">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Upcoming Appointments -->
                    <div class="lg:col-span-3 bg-white rounded-lg border-0 shadow-enhanced p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-semibold">Upcoming Appointments</h3>
                                <p class="text-gray-600">Your schedule for today</p>
                            </div>
                            <button class="text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-2 py-1 rounded">View All</button>
                        </div>
                        <div class="space-y-4 max-h-[300px] overflow-y-auto">
                            <div class="flex items-start gap-4 rounded-lg border p-3 hover:bg-gray-50 transition-all duration-200 hover:shadow-sm">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border bg-gradient-to-br from-blue-100 to-blue-50">
                                    <i data-lucide="calendar" class="h-5 w-5 text-blue-600"></i>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium">Property Inspection</p>
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-2 rounded-full priority-high"></div>
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Upcoming</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i data-lucide="clock" class="mr-1 h-3 w-3"></i>
                                        10:00 AM with John Smith
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i data-lucide="map-pin" class="mr-1 h-3 w-3"></i>
                                        Riverside Apartments
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 rounded-lg border p-3 hover:bg-gray-50 transition-all duration-200 hover:shadow-sm">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border bg-gradient-to-br from-blue-100 to-blue-50">
                                    <i data-lucide="calendar" class="h-5 w-5 text-blue-600"></i>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium">Document Verification</p>
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-2 rounded-full priority-medium"></div>
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Upcoming</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i data-lucide="clock" class="mr-1 h-3 w-3"></i>
                                        11:30 AM with Sarah Johnson
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i data-lucide="map-pin" class="mr-1 h-3 w-3"></i>
                                        KLAS Office
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 rounded-lg border p-3 hover:bg-gray-50 transition-all duration-200 hover:shadow-sm">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border bg-gradient-to-br from-blue-100 to-blue-50">
                                    <i data-lucide="calendar" class="h-5 w-5 text-blue-600"></i>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium">Title Deed Handover</p>
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-2 rounded-full priority-high"></div>
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full border">Confirmed</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i data-lucide="clock" class="mr-1 h-3 w-3"></i>
                                        1:00 PM with Michael Brown
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i data-lucide="map-pin" class="mr-1 h-3 w-3"></i>
                                        Central Plaza
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 rounded-lg border p-3 hover:bg-gray-50 transition-all duration-200 hover:shadow-sm">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border bg-gradient-to-br from-blue-100 to-blue-50">
                                    <i data-lucide="calendar" class="h-5 w-5 text-blue-600"></i>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium">Land Survey Review</p>
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-2 rounded-full priority-low"></div>
                                            <span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded-full">Pending</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i data-lucide="clock" class="mr-1 h-3 w-3"></i>
                                        2:30 PM with Emily Davis
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i data-lucide="map-pin" class="mr-1 h-3 w-3"></i>
                                        Field Office
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Recent Activities -->
                    <div class="col-span-2 bg-white rounded-lg border-0 shadow-enhanced p-6">
                        <div class="mb-4">
                            <h3 class="text-xl font-semibold">Recent Activities</h3>
                            <p class="text-gray-600">Latest system activities</p>
                        </div>
                        <div class="space-y-4 max-h-[200px] overflow-y-auto">
                            <div class="flex items-start space-x-4 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="p-2 rounded-full bg-gray-100">
                                    <i data-lucide="file-text" class="h-4 w-4 text-blue-600"></i>
                                </div>
                                <div class="space-y-1 flex-1">
                                    <p class="text-sm font-medium">New application submitted</p>
                                    <p class="text-xs text-gray-500">John Smith • 10 minutes ago</p>
                                </div>
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full border">Sectional Titling</span>
                            </div>

                            <div class="flex items-start space-x-4 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="p-2 rounded-full bg-gray-100">
                                    <i data-lucide="check-circle-2" class="h-4 w-4 text-green-600"></i>
                                </div>
                                <div class="space-y-1 flex-1">
                                    <p class="text-sm font-medium">Document approved</p>
                                    <p class="text-xs text-gray-500">Admin User • 30 minutes ago</p>
                                </div>
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full border">Certificate of Occupancy</span>
                            </div>

                            <div class="flex items-start space-x-4 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="p-2 rounded-full bg-gray-100">
                                    <i data-lucide="user-circle" class="h-4 w-4 text-purple-600"></i>
                                </div>
                                <div class="space-y-1 flex-1">
                                    <p class="text-sm font-medium">User account created</p>
                                    <p class="text-xs text-gray-500">System • 1 hour ago</p>
                                </div>
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full border">System Admin</span>
                            </div>

                            <div class="flex items-start space-x-4 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="p-2 rounded-full bg-gray-100">
                                    <i data-lucide="file-digit" class="h-4 w-4 text-cyan-600"></i>
                                </div>
                                <div class="space-y-1 flex-1">
                                    <p class="text-sm font-medium">File uploaded</p>
                                    <p class="text-xs text-gray-500">Sarah Johnson • 2 hours ago</p>
                                </div>
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full border">File Digital Registry</span>
                            </div>

                            <div class="flex items-start space-x-4 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="p-2 rounded-full bg-gray-100">
                                    <i data-lucide="calendar-days" class="h-4 w-4 text-yellow-600"></i>
                                </div>
                                <div class="space-y-1 flex-1">
                                    <p class="text-sm font-medium">Appointment scheduled</p>
                                    <p class="text-xs text-gray-500">Michael Brown • 3 hours ago</p>
                                </div>
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full border">Customer Management</span>
                            </div>
                        </div>
                    </div>

                    <!-- Module Usage -->
                    <div class="bg-white rounded-lg border-0 shadow-enhanced p-6">
                        <div class="mb-4">
                            <h3 class="text-xl font-semibold">Module Usage</h3>
                            <p class="text-gray-600">Most active modules</p>
                        </div>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="building-2" class="h-4 w-4 text-blue-600"></i>
                                        <span class="text-sm">Sectional Titling</span>
                                    </div>
                                    <span class="text-sm font-medium">42%</span>
                                </div>
                                <div class="progress-bar h-2">
                                    <div class="progress-fill" style="width: 42%"></div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="file-check" class="h-4 w-4 text-green-600"></i>
                                        <span class="text-sm">Recertification</span>
                                    </div>
                                    <span class="text-sm font-medium">28%</span>
                                </div>
                                <div class="progress-bar h-2">
                                    <div class="progress-fill bg-green-500" style="width: 28%"></div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="file-digit" class="h-4 w-4 text-yellow-600"></i>
                                        <span class="text-sm">File Digital Registry</span>
                                    </div>
                                    <span class="text-sm font-medium">15%</span>
                                </div>
                                <div class="progress-bar h-2">
                                    <div class="progress-fill bg-yellow-500" style="width: 15%"></div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="users" class="h-4 w-4 text-purple-600"></i>
                                        <span class="text-sm">Customer Management</span>
                                    </div>
                                    <span class="text-sm font-medium">15%</span>
                                </div>
                                <div class="progress-bar h-2">
                                    <div class="progress-fill bg-purple-500" style="width: 15%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white rounded-lg border-0 shadow-enhanced p-6">
                        <div class="mb-4">
                            <h3 class="text-xl font-semibold">Quick Stats</h3>
                            <p class="text-gray-600">System performance</p>
                        </div>
                        <div class="space-y-4">
                            <div class="performance-metric">
                                <div class="flex items-center gap-2">
                                    <div class="p-1 rounded-full bg-gray-100">
                                        <i data-lucide="check-circle-2" class="h-4 w-4 text-green-600"></i>
                                    </div>
                                    <span class="text-sm">Approved Applications</span>
                                </div>
                                <div class="text-right">
                                    <div class="metric-value">432</div>
                                    <div class="metric-change positive">
                                        <i data-lucide="trending-up" class="h-3 w-3"></i>
                                        +12%
                                    </div>
                                </div>
                            </div>

                            <div class="performance-metric">
                                <div class="flex items-center gap-2">
                                    <div class="p-1 rounded-full bg-gray-100">
                                        <i data-lucide="x-circle" class="h-4 w-4 text-red-600"></i>
                                    </div>
                                    <span class="text-sm">Rejected Applications</span>
                                </div>
                                <div class="text-right">
                                    <div class="metric-value">67</div>
                                    <div class="metric-change positive">
                                        <i data-lucide="trending-down" class="h-3 w-3"></i>
                                        -5%
                                    </div>
                                </div>
                            </div>

                            <div class="performance-metric">
                                <div class="flex items-center gap-2">
                                    <div class="p-1 rounded-full bg-gray-100">
                                        <i data-lucide="clock" class="h-4 w-4 text-yellow-600"></i>
                                    </div>
                                    <span class="text-sm">Average Processing Time</span>
                                </div>
                                <div class="text-right">
                                    <div class="metric-value">3.2 days</div>
                                    <div class="metric-change positive">
                                        <i data-lucide="trending-down" class="h-3 w-3"></i>
                                        -0.5 days
                                    </div>
                                </div>
                            </div>

                            <div class="performance-metric">
                                <div class="flex items-center gap-2">
                                    <div class="p-1 rounded-full bg-gray-100">
                                        <i data-lucide="help-circle" class="h-4 w-4 text-blue-600"></i>
                                    </div>
                                    <span class="text-sm">User Satisfaction</span>
                                </div>
                                <div class="text-right">
                                    <div class="metric-value">92%</div>
                                    <div class="metric-change positive">
                                        <i data-lucide="trending-up" class="h-3 w-3"></i>
                                        +4%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications Tab -->
            <div id="applications" class="tab-content space-y-6">
                <div class="bg-white rounded-lg border-0 shadow-enhanced">
                    <div class="p-6 border-b">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold">Applications</h3>
                                <p class="text-gray-600">Manage your land applications</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <select class="px-3 py-2 border rounded-md text-sm">
                                    <option>All Applications</option>
                                    <option>Pending</option>
                                    <option>Approved</option>
                                    <option>Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b bg-gray-50">
                                <tr>
                                    <th class="text-left p-4 font-medium text-gray-700">Application ID</th>
                                    <th class="text-left p-4 font-medium text-gray-700">Type</th>
                                    <th class="text-left p-4 font-medium text-gray-700">Applicant</th>
                                    <th class="text-left p-4 font-medium text-gray-700">Date</th>
                                    <th class="text-left p-4 font-medium text-gray-700">Status</th>
                                    <th class="text-left p-4 font-medium text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4 font-medium">APP-2023-0042</td>
                                    <td class="p-4">Sectional Titling</td>
                                    <td class="p-4">John Smith</td>
                                    <td class="p-4">2023-06-15</td>
                                    <td class="p-4">
                                        <span class="status-badge-in-progress px-2 py-1 text-xs rounded-full">In Progress</span>
                                    </td>
                                    <td class="p-4">
                                        <button class="text-blue-600 hover:text-blue-700 text-sm">View</button>
                                    </td>
                                </tr>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4 font-medium">APP-2023-0041</td>
                                    <td class="p-4">CofO</td>
                                    <td class="p-4">Sarah Johnson</td>
                                    <td class="p-4">2023-06-12</td>
                                    <td class="p-4">
                                        <span class="status-badge-approved px-2 py-1 text-xs rounded-full">Approved</span>
                                    </td>
                                    <td class="p-4">
                                        <button class="text-blue-600 hover:text-blue-700 text-sm">View</button>
                                    </td>
                                </tr>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4 font-medium">APP-2023-0040</td>
                                    <td class="p-4">Right of Occupancy</td>
                                    <td class="p-4">Michael Brown</td>
                                    <td class="p-4">2023-06-10</td>
                                    <td class="p-4">
                                        <span class="status-badge-pending px-2 py-1 text-xs rounded-full">Pending</span>
                                    </td>
                                    <td class="p-4">
                                        <button class="text-blue-600 hover:text-blue-700 text-sm">View</button>
                                    </td>
                                </tr>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4 font-medium">APP-2023-0039</td>
                                    <td class="p-4">Recertification</td>
                                    <td class="p-4">Emily Davis</td>
                                    <td class="p-4">2023-06-05</td>
                                    <td class="p-4">
                                        <span class="status-badge-approved px-2 py-1 text-xs rounded-full">Approved</span>
                                    </td>
                                    <td class="p-4">
                                        <button class="text-blue-600 hover:text-blue-700 text-sm">View</button>
                                    </td>
                                </tr>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4 font-medium">APP-2023-0038</td>
                                    <td class="p-4">Land Property Enumeration</td>
                                    <td class="p-4">Robert Wilson</td>
                                    <td class="p-4">2023-06-01</td>
                                    <td class="p-4">
                                        <span class="status-badge-rejected px-2 py-1 text-xs rounded-full">Rejected</span>
                                    </td>
                                    <td class="p-4">
                                        <button class="text-blue-600 hover:text-blue-700 text-sm">View</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t flex items-center justify-between">
                        <div class="text-sm text-gray-600">Showing 5 of 42 applications</div>
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-1 border rounded text-sm" disabled>Previous</button>
                            <button class="px-3 py-1 border rounded text-sm hover:bg-gray-50">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Products Tab -->
            <div id="documents" class="tab-content space-y-6">
                <div class="bg-white rounded-lg border-0 shadow-enhanced">
                    <div class="p-6 border-b">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold">Information Products</h3>
                                <p class="text-gray-600">Manage your land documents</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="flex items-center gap-2 px-4 py-2 border rounded-md hover:bg-gray-50">
                                    <i data-lucide="download" class="h-4 w-4"></i>
                                    Export
                                </button>
                                <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <i data-lucide="plus" class="h-4 w-4"></i>
                                    New Document
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="hover-scale bg-gradient-to-br from-blue-50 to-blue-100 border-0 shadow-md rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium">CofO</h3>
                                        <p class="text-2xl font-bold mt-2">1,284</p>
                                    </div>
                                    <i data-lucide="file-check" class="h-10 w-10 text-blue-600 opacity-80"></i>
                                </div>
                            </div>

                            <div class="hover-scale bg-gradient-to-br from-green-50 to-green-100 border-0 shadow-md rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium">Right of Occupancy</h3>
                                        <p class="text-2xl font-bold mt-2">856</p>
                                    </div>
                                    <i data-lucide="file-text" class="h-10 w-10 text-green-600 opacity-80"></i>
                                </div>
                            </div>

                            <div class="hover-scale bg-gradient-to-br from-yellow-50 to-yellow-100 border-0 shadow-md rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium">Letter of Administration</h3>
                                        <p class="text-2xl font-bold mt-2">432</p>
                                    </div>
                                    <i data-lucide="file-text" class="h-10 w-10 text-yellow-600 opacity-80"></i>
                                </div>
                            </div>

                            <div class="hover-scale bg-gradient-to-br from-purple-50 to-purple-100 border-0 shadow-md rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium">Occupancy Permit</h3>
                                        <p class="text-2xl font-bold mt-2">621</p>
                                    </div>
                                    <i data-lucide="file-text" class="h-10 w-10 text-purple-600 opacity-80"></i>
                                </div>
                            </div>

                            <div class="hover-scale bg-gradient-to-br from-red-50 to-red-100 border-0 shadow-md rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium">Site Plan / Parcel Plan</h3>
                                        <p class="text-2xl font-bold mt-2">1,842</p>
                                    </div>
                                    <i data-lucide="file-text" class="h-10 w-10 text-red-600 opacity-80"></i>
                                </div>
                            </div>

                            <div class="hover-scale bg-gradient-to-br from-cyan-50 to-cyan-100 border-0 shadow-md rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium">Sectional Title Deeds</h3>
                                        <p class="text-2xl font-bold mt-2">756</p>
                                    </div>
                                    <i data-lucide="building-2" class="h-10 w-10 text-cyan-600 opacity-80"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div id="analytics" class="tab-content space-y-6">
                <div class="bg-white rounded-lg border-0 shadow-enhanced">
                    <div class="p-6 border-b">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold">Analytics</h3>
                                <p class="text-gray-600">View system analytics and reports</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <select class="px-3 py-2 border rounded-md text-sm">
                                    <option>This Month</option>
                                    <option>This Week</option>
                                    <option>This Quarter</option>
                                    <option>This Year</option>
                                </select>
                                <button class="flex items-center gap-2 px-4 py-2 border rounded-md hover:bg-gray-50">
                                    <i data-lucide="download" class="h-4 w-4"></i>
                                    Export
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-6 mb-6">
                            <!-- Analytics Summary Cards -->
                            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-blue-600 font-medium">Total Revenue</p>
                                            <p class="text-2xl font-bold text-blue-900">₦2.4M</p>
                                            <p class="text-xs text-blue-600 flex items-center mt-1">
                                                <i data-lucide="trending-up" class="h-3 w-3 mr-1"></i>
                                                +15% from last month
                                            </p>
                                        </div>
                                        <i data-lucide="dollar-sign" class="h-8 w-8 text-blue-600 opacity-60"></i>
                                    </div>
                                </div>

                                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-green-600 font-medium">Processing Time</p>
                                            <p class="text-2xl font-bold text-green-900">2.8 days</p>
                                            <p class="text-xs text-green-600 flex items-center mt-1">
                                                <i data-lucide="trending-down" class="h-3 w-3 mr-1"></i>
                                                -0.4 days improved
                                            </p>
                                        </div>
                                        <i data-lucide="clock" class="h-8 w-8 text-green-600 opacity-60"></i>
                                    </div>
                                </div>

                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-purple-600 font-medium">Success Rate</p>
                                            <p class="text-2xl font-bold text-purple-900">94.2%</p>
                                            <p class="text-xs text-purple-600 flex items-center mt-1">
                                                <i data-lucide="trending-up" class="h-3 w-3 mr-1"></i>
                                                +2.1% improvement
                                            </p>
                                        </div>
                                        <i data-lucide="target" class="h-8 w-8 text-purple-600 opacity-60"></i>
                                    </div>
                                </div>

                                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 border">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-orange-600 font-medium">User Satisfaction</p>
                                            <p class="text-2xl font-bold text-orange-900">4.7/5</p>
                                            <p class="text-xs text-orange-600 flex items-center mt-1">
                                                <i data-lucide="trending-up" class="h-3 w-3 mr-1"></i>
                                                +0.3 rating increase
                                            </p>
                                        </div>
                                        <i data-lucide="star" class="h-8 w-8 text-orange-600 opacity-60"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Analytics Charts -->
                            <div class="grid gap-6 lg:grid-cols-2">
                                <!-- Monthly Trends Chart -->
                                <div class="bg-white rounded-lg border p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold">Monthly Application Trends</h3>
                                            <p class="text-gray-600 text-sm">Applications submitted over the last 6 months</p>
                                        </div>
                                        <button class="text-blue-600 hover:text-blue-700 text-sm">View Details</button>
                                    </div>
                                    <div class="h-64 relative">
                                        <!-- Y-axis labels -->
                                        <div class="absolute left-0 top-0 bottom-8 flex flex-col justify-between text-xs text-gray-500 w-8">
                                            <span>300</span>
                                            <span>250</span>
                                            <span>200</span>
                                            <span>150</span>
                                            <span>100</span>
                                            <span>50</span>
                                            <span>0</span>
                                        </div>
                                        
                                        <!-- Chart area -->
                                        <div class="ml-10 mr-4 h-56 relative">
                                            <!-- Grid lines -->
                                            <div class="absolute inset-0 flex flex-col justify-between">
                                                <div class="border-t border-gray-200"></div>
                                                <div class="border-t border-gray-200"></div>
                                                <div class="border-t border-gray-200"></div>
                                                <div class="border-t border-gray-200"></div>
                                                <div class="border-t border-gray-200"></div>
                                                <div class="border-t border-gray-300"></div>
                                            </div>
                                            
                                            <!-- Line chart -->
                                            <div class="flex items-end justify-between h-full" id="monthly-trends-chart">
                                                <!-- Chart points will be generated by JavaScript -->
                                            </div>
                                        </div>
                                        
                                        <!-- X-axis labels -->
                                        <div class="ml-10 mr-4 flex justify-between text-xs text-gray-500 mt-2">
                                            <span>Jan</span>
                                            <span>Feb</span>
                                            <span>Mar</span>
                                            <span>Apr</span>
                                            <span>May</span>
                                            <span>Jun</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Application Types Distribution -->
                                <div class="bg-white rounded-lg border p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold">Application Types Distribution</h3>
                                            <p class="text-gray-600 text-sm">Breakdown by application type</p>
                                        </div>
                                        <button class="text-blue-600 hover:text-blue-700 text-sm">View All</button>
                                    </div>
                                    <div class="h-64">
                                        <!-- Donut chart representation -->
                                        <div class="flex items-center justify-center h-full">
                                            <div class="relative">
                                                <!-- Donut chart -->
                                                <div class="w-40 h-40 rounded-full border-8 border-gray-200 relative">
                                                    <div class="absolute inset-0 rounded-full border-8 border-blue-500" style="border-right-color: transparent; border-bottom-color: transparent; transform: rotate(0deg);"></div>
                                                    <div class="absolute inset-0 rounded-full border-8 border-green-500" style="border-left-color: transparent; border-bottom-color: transparent; transform: rotate(120deg);"></div>
                                                    <div class="absolute inset-0 rounded-full border-8 border-yellow-500" style="border-left-color: transparent; border-top-color: transparent; transform: rotate(240deg);"></div>
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <div class="text-center">
                                                            <div class="text-2xl font-bold">1,284</div>
                                                            <div class="text-xs text-gray-600">Total</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Legend -->
                                            <div class="ml-8 space-y-3">
                                                <div class="flex items-center">
                                                    <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                                                    <div>
                                                        <div class="text-sm font-medium">Sectional Titling</div>
                                                        <div class="text-xs text-gray-600">542 (42%)</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                                    <div>
                                                        <div class="text-sm font-medium">Recertification</div>
                                                        <div class="text-xs text-gray-600">385 (30%)</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                                                    <div>
                                                        <div class="text-sm font-medium">CofO</div>
                                                        <div class="text-xs text-gray-600">357 (28%)</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Metrics Table -->
                            <div class="bg-white rounded-lg border">
                                <div class="p-6 border-b">
                                    <h3 class="text-lg font-semibold">Performance Metrics</h3>
                                    <p class="text-gray-600 text-sm">Detailed breakdown of system performance</p>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50 border-b">
                                            <tr>
                                                <th class="text-left p-4 font-medium text-gray-700">Metric</th>
                                                <th class="text-left p-4 font-medium text-gray-700">Current Value</th>
                                                <th class="text-left p-4 font-medium text-gray-700">Previous Period</th>
                                                <th class="text-left p-4 font-medium text-gray-700">Change</th>
                                                <th class="text-left p-4 font-medium text-gray-700">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-4 font-medium">Average Processing Time</td>
                                                <td class="p-4">2.8 days</td>
                                                <td class="p-4">3.2 days</td>
                                                <td class="p-4 text-green-600">-0.4 days</td>
                                                <td class="p-4">
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Improved</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-4 font-medium">Application Success Rate</td>
                                                <td class="p-4">94.2%</td>
                                                <td class="p-4">92.1%</td>
                                                <td class="p-4 text-green-600">+2.1%</td>
                                                <td class="p-4">
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Excellent</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-4 font-medium">User Satisfaction Score</td>
                                                <td class="p-4">4.7/5</td>
                                                <td class="p-4">4.4/5</td>
                                                <td class="p-4 text-green-600">+0.3</td>
                                                <td class="p-4">
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">High</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-4 font-medium">System Uptime</td>
                                                <td class="p-4">99.8%</td>
                                                <td class="p-4">99.5%</td>
                                                <td class="p-4 text-green-600">+0.3%</td>
                                                <td class="p-4">
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Excellent</span>
                                                </td>
                                            </tr>
                                            <tr class="hover:bg-gray-50">
                                                <td class="p-4 font-medium">Revenue Growth</td>
                                                <td class="p-4">₦2.4M</td>
                                                <td class="p-4">₦2.1M</td>
                                                <td class="p-4 text-green-600">+15%</td>
                                                <td class="p-4">
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Growing</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modules Tab -->
            <div id="modules" class="tab-content space-y-6">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div class="hover-scale bg-gradient-to-br from-blue-50 to-blue-100 border-0 shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <i data-lucide="home" class="h-8 w-8 module-icon-dashboard"></i>
                                <span class="module-badge-dashboard px-2 py-1 text-xs rounded-full">Module</span>
                            </div>
                            <h3 class="text-lg font-semibold mt-4">Dashboard</h3>
                            <p class="text-gray-600">System overview and statistics</p>
                        </div>
                        <div class="p-6 pt-0">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 border rounded-md hover:bg-blue-600 hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                Access Module
                            </button>
                        </div>
                    </div>

                    <div class="hover-scale bg-gradient-to-br from-green-50 to-green-100 border-0 shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <i data-lucide="users" class="h-8 w-8 module-icon-customer"></i>
                                <span class="module-badge-customer px-2 py-1 text-xs rounded-full">Module</span>
                            </div>
                            <h3 class="text-lg font-semibold mt-4">Customer Management</h3>
                            <p class="text-gray-600">Manage individuals, groups, and appointments</p>
                        </div>
                        <div class="p-6 pt-0">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 border rounded-md hover:bg-green-600 hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                Access Module
                            </button>
                        </div>
                    </div>

                    <div class="hover-scale bg-gradient-to-br from-purple-50 to-purple-100 border-0 shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <i data-lucide="layers" class="h-8 w-8 module-icon-programmes"></i>
                                <span class="module-badge-programmes px-2 py-1 text-xs rounded-full">Module</span>
                            </div>
                            <h3 class="text-lg font-semibold mt-4">Programmes</h3>
                            <p class="text-gray-600">Land allocation, resettlement, and titling</p>
                        </div>
                        <div class="p-6 pt-0">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 border rounded-md hover:bg-purple-600 hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                Access Module
                            </button>
                        </div>
                    </div>

                    <div class="hover-scale bg-gradient-to-br from-yellow-50 to-yellow-100 border-0 shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <i data-lucide="file-text" class="h-8 w-8 module-icon-info-products"></i>
                                <span class="module-badge-info-products px-2 py-1 text-xs rounded-full">Module</span>
                            </div>
                            <h3 class="text-lg font-semibold mt-4">Information Products</h3>
                            <p class="text-gray-600">Certificates, permits, and plans</p>
                        </div>
                        <div class="p-6 pt-0">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 border rounded-md hover:bg-yellow-600 hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                Access Module
                            </button>
                        </div>
                    </div>

                    <div class="hover-scale bg-gradient-to-br from-red-50 to-red-100 border-0 shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <i data-lucide="book-open" class="h-8 w-8 module-icon-instrument"></i>
                                <span class="module-badge-instrument px-2 py-1 text-xs rounded-full">Module</span>
                            </div>
                            <h3 class="text-lg font-semibold mt-4">Instrument Registration</h3>
                            <p class="text-gray-600">Register land instruments</p>
                        </div>
                        <div class="p-6 pt-0">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 border rounded-md hover:bg-red-600 hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                Access Module
                            </button>
                        </div>
                    </div>

                    <div class="hover-scale bg-gradient-to-br from-cyan-50 to-cyan-100 border-0 shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <i data-lucide="file-digit" class="h-8 w-8 module-icon-file-registry"></i>
                                <span class="module-badge-file-registry px-2 py-1 text-xs rounded-full">Module</span>
                            </div>
                            <h3 class="text-lg font-semibold mt-4">File Digital Registry</h3>
                            <p class="text-gray-600">Archive, track, and index files</p>
                        </div>
                        <div class="p-6 pt-0">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 border rounded-md hover:bg-cyan-600 hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                Access Module
                            </button>
                        </div>
                    </div>

                    <div class="hover-scale bg-gradient-to-br from-orange-50 to-orange-100 border-0 shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <i data-lucide="alert-circle" class="h-8 w-8 module-icon-systems"></i>
                                <span class="module-badge-dashboard px-2 py-1 text-xs rounded-full">Module</span>
                            </div>
                            <h3 class="text-lg font-semibold mt-4">Systems</h3>
                            <p class="text-gray-600">Caveat and encumbrance management</p>
                        </div>
                        <div class="p-6 pt-0">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 border rounded-md hover:bg-orange-600 hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                Access Module
                            </button>
                        </div>
                    </div>

                    <div class="hover-scale bg-gradient-to-br from-gray-50 to-gray-100 border-0 shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <i data-lucide="history" class="h-8 w-8 module-icon-legacy"></i>
                                <span class="module-badge-info-products px-2 py-1 text-xs rounded-full">Module</span>
                            </div>
                            <h3 class="text-lg font-semibold mt-4">Legacy Systems</h3>
                            <p class="text-gray-600">Access to legacy data</p>
                        </div>
                        <div class="p-6 pt-0">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 border rounded-md hover:bg-gray-600 hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                Access Module
                            </button>
                        </div>
                    </div>

                    <div class="hover-scale bg-gradient-to-br from-indigo-50 to-indigo-100 border-0 shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <i data-lucide="settings" class="h-8 w-8 module-icon-admin"></i>
                                <span class="module-badge-instrument px-2 py-1 text-xs rounded-full">Module</span>
                            </div>
                            <h3 class="text-lg font-semibold mt-4">System Admin</h3>
                            <p class="text-gray-600">User accounts and system settings</p>
                        </div>
                        <div class="p-6 pt-0">
                            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 border rounded-md hover:bg-indigo-600 hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                Access Module
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Enhanced Chart Variables
        let chartView = 'bar'; // 'bar' or 'line'
        let hiddenSeries = new Set();
        let currentFilter = 'all';

        // Enhanced Chart Data with more realistic values and analytics
        const enhancedChartData = [
            { 
                day: "Mon", 
                sectional: 45, 
                recertification: 32, 
                allocation: 28, 
                total: 105,
                successRate: 92.4,
                avgProcessingTime: 2.8,
                rejectionRate: 7.6
            },
            { 
                day: "Tue", 
                sectional: 52, 
                recertification: 28, 
                allocation: 35, 
                total: 115,
                successRate: 94.8,
                avgProcessingTime: 2.6,
                rejectionRate: 5.2
            },
            { 
                day: "Wed", 
                sectional: 38, 
                recertification: 45, 
                allocation: 22, 
                total: 105,
                successRate: 89.5,
                avgProcessingTime: 3.1,
                rejectionRate: 10.5
            },
            { 
                day: "Thu", 
                sectional: 61, 
                recertification: 35, 
                allocation: 40, 
                total: 136,
                successRate: 96.3,
                avgProcessingTime: 2.4,
                rejectionRate: 3.7
            },
            { 
                day: "Fri", 
                sectional: 48, 
                recertification: 38, 
                allocation: 32, 
                total: 118,
                successRate: 93.2,
                avgProcessingTime: 2.7,
                rejectionRate: 6.8
            },
            { 
                day: "Sat", 
                sectional: 35, 
                recertification: 25, 
                allocation: 18, 
                total: 78,
                successRate: 87.2,
                avgProcessingTime: 3.4,
                rejectionRate: 12.8
            },
            { 
                day: "Sun", 
                sectional: 28, 
                recertification: 20, 
                allocation: 15, 
                total: 63,
                successRate: 85.7,
                avgProcessingTime: 3.6,
                rejectionRate: 14.3
            }
        ];

        // Analytics chart data
        const monthlyTrendsData = [
            { month: "Jan", value: 180 },
            { month: "Feb", value: 220 },
            { month: "Mar", value: 195 },
            { month: "Apr", value: 240 },
            { month: "May", value: 280 },
            { month: "Jun", value: 320 }
        ];

        const maxChartValue = 150; // Fixed max for consistent scaling
        const maxAnalyticsValue = 350; // Max for analytics chart

        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabTriggers = document.querySelectorAll('.tab-trigger');
            const tabContents = document.querySelectorAll('.tab-content');

            tabTriggers.forEach(trigger => {
                trigger.addEventListener('click', () => {
                    const targetTab = trigger.getAttribute('data-tab');
                    
                    // Remove active class from all triggers and contents
                    tabTriggers.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked trigger and corresponding content
                    trigger.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });

            // Initialize charts
            generateEnhancedSVGChart();
            generateAnalyticsChart();
        });

        // Generate Enhanced SVG Chart with professional analytics
        function generateEnhancedSVGChart() {
            const svg = document.getElementById('applications-chart');
            const tooltip = document.getElementById('enhanced-chart-tooltip');
            if (!svg) return;

            // Clear existing content
            svg.innerHTML = `
                <defs>
                    <linearGradient id="chartGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                        <stop offset="50%" style="stop-color:#8b5cf6;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#06b6d4;stop-opacity:1" />
                    </linearGradient>
                    <linearGradient id="chartAreaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0.3" />
                        <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0.05" />
                    </linearGradient>
                    <filter id="dropShadow" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.1"/>
                    </filter>
                </defs>
            `;

            const chartWidth = 800;
            const chartHeight = 300;
            const margin = { top: 20, right: 40, bottom: 40, left: 60 };
            const innerWidth = chartWidth - margin.left - margin.right;
            const innerHeight = chartHeight - margin.top - margin.bottom;

            // Create grid lines
            const gridGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            gridGroup.setAttribute('id', 'grid-lines');
            
            // Horizontal grid lines
            for (let i = 0; i <= 5; i++) {
                const y = margin.top + (innerHeight / 5) * i;
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', margin.left);
                line.setAttribute('y1', y);
                line.setAttribute('x2', margin.left + innerWidth);
                line.setAttribute('y2', y);
                line.setAttribute('class', 'chart-grid-line');
                gridGroup.appendChild(line);
            }

            // Vertical grid lines
            for (let i = 0; i <= 7; i++) {
                const x = margin.left + (innerWidth / 7) * i;
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', x);
                line.setAttribute('y1', margin.top);
                line.setAttribute('x2', x);
                line.setAttribute('y2', margin.top + innerHeight);
                line.setAttribute('class', 'chart-grid-line');
                gridGroup.appendChild(line);
            }

            svg.appendChild(gridGroup);

            // Create axes
            const axesGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            axesGroup.setAttribute('id', 'axes');

            // Y-axis
            const yAxis = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            yAxis.setAttribute('x1', margin.left);
            yAxis.setAttribute('y1', margin.top);
            yAxis.setAttribute('x2', margin.left);
            yAxis.setAttribute('y2', margin.top + innerHeight);
            yAxis.setAttribute('class', 'chart-axis-line');
            axesGroup.appendChild(yAxis);

            // X-axis
            const xAxis = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            xAxis.setAttribute('x1', margin.left);
            xAxis.setAttribute('y1', margin.top + innerHeight);
            xAxis.setAttribute('x2', margin.left + innerWidth);
            xAxis.setAttribute('y2', margin.top + innerHeight);
            xAxis.setAttribute('class', 'chart-axis-line');
            axesGroup.appendChild(xAxis);

            // Y-axis labels
            for (let i = 0; i <= 5; i++) {
                const value = (maxChartValue / 5) * (5 - i);
                const y = margin.top + (innerHeight / 5) * i;
                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', margin.left - 10);
                text.setAttribute('y', y + 4);
                text.setAttribute('text-anchor', 'end');
                text.setAttribute('font-size', '12');
                text.setAttribute('fill', '#6b7280');
                text.textContent = value;
                axesGroup.appendChild(text);
            }

            // X-axis labels
            enhancedChartData.forEach((data, index) => {
                const x = margin.left + (innerWidth / 7) * index + (innerWidth / 14);
                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', x);
                text.setAttribute('y', margin.top + innerHeight + 20);
                text.setAttribute('text-anchor', 'middle');
                text.setAttribute('font-size', '12');
                text.setAttribute('fill', '#6b7280');
                text.textContent = data.day;
                axesGroup.appendChild(text);
            });

            svg.appendChild(axesGroup);

            // Create data visualization
            const dataGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            dataGroup.setAttribute('id', 'data-visualization');

            if (chartView === 'bar') {
                // Create stacked bars
                enhancedChartData.forEach((data, index) => {
                    const barWidth = innerWidth / 7 * 0.6;
                    const x = margin.left + (innerWidth / 7) * index + (innerWidth / 14) - barWidth / 2;
                    
                    let currentY = margin.top + innerHeight;
                    
                    // Sectional Titling bar
                    if (!hiddenSeries.has('sectional')) {
                        const height = (data.sectional / maxChartValue) * innerHeight;
                        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        rect.setAttribute('x', x);
                        rect.setAttribute('y', currentY - height);
                        rect.setAttribute('width', barWidth);
                        rect.setAttribute('height', height);
                        rect.setAttribute('fill', '#3b82f6');
                        rect.setAttribute('class', 'chart-bar animated-bar');
                        rect.setAttribute('filter', 'url(#dropShadow)');
                        
                        // Add hover events
                        rect.addEventListener('mouseenter', (e) => showTooltip(e, data, index));
                        rect.addEventListener('mouseleave', hideTooltip);
                        
                        dataGroup.appendChild(rect);
                        currentY -= height;
                    }
                    
                    // Recertification bar
                    if (!hiddenSeries.has('recertification')) {
                        const height = (data.recertification / maxChartValue) * innerHeight;
                        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        rect.setAttribute('x', x);
                        rect.setAttribute('y', currentY - height);
                        rect.setAttribute('width', barWidth);
                        rect.setAttribute('height', height);
                        rect.setAttribute('fill', '#10b981');
                        rect.setAttribute('class', 'chart-bar animated-bar');
                        rect.setAttribute('filter', 'url(#dropShadow)');
                        
                        rect.addEventListener('mouseenter', (e) => showTooltip(e, data, index));
                        rect.addEventListener('mouseleave', hideTooltip);
                        
                        dataGroup.appendChild(rect);
                        currentY -= height;
                    }
                    
                    // Allocation bar
                    if (!hiddenSeries.has('allocation')) {
                        const height = (data.allocation / maxChartValue) * innerHeight;
                        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        rect.setAttribute('x', x);
                        rect.setAttribute('y', currentY - height);
                        rect.setAttribute('width', barWidth);
                        rect.setAttribute('height', height);
                        rect.setAttribute('fill', '#f59e0b');
                        rect.setAttribute('class', 'chart-bar animated-bar');
                        rect.setAttribute('filter', 'url(#dropShadow)');
                        
                        rect.addEventListener('mouseenter', (e) => showTooltip(e, data, index));
                        rect.addEventListener('mouseleave', hideTooltip);
                        
                        dataGroup.appendChild(rect);
                    }
                });
            } else {
                // Create line chart
                const points = enhancedChartData.map((data, index) => {
                    const x = margin.left + (innerWidth / 7) * index + (innerWidth / 14);
                    const y = margin.top + innerHeight - (data.total / maxChartValue) * innerHeight;
                    return `${x},${y}`;
                }).join(' ');

                // Area fill
                const areaPoints = `${margin.left + innerWidth / 14},${margin.top + innerHeight} ${points} ${margin.left + innerWidth - innerWidth / 14},${margin.top + innerHeight}`;
                const area = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
                area.setAttribute('points', areaPoints);
                area.setAttribute('class', 'chart-area-fill');
                dataGroup.appendChild(area);

                // Trend line
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
                line.setAttribute('points', points);
                line.setAttribute('class', 'chart-trend-line');
                dataGroup.appendChild(line);

                // Data points
                enhancedChartData.forEach((data, index) => {
                    const x = margin.left + (innerWidth / 7) * index + (innerWidth / 14);
                    const y = margin.top + innerHeight - (data.total / maxChartValue) * innerHeight;
                    
                    const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    circle.setAttribute('cx', x);
                    circle.setAttribute('cy', y);
                    circle.setAttribute('r', 4);
                    circle.setAttribute('class', 'chart-data-point');
                    
                    circle.addEventListener('mouseenter', (e) => showTooltip(e, data, index));
                    circle.addEventListener('mouseleave', hideTooltip);
                    
                    dataGroup.appendChild(circle);
                });
            }

            svg.appendChild(dataGroup);
            updateChartTotals();
        }

        // Show enhanced tooltip
        function showTooltip(event, data, index) {
            const tooltip = document.getElementById('enhanced-chart-tooltip');
            if (!tooltip) return;

            // Update tooltip content
            const tooltipDay = document.getElementById('tooltip-day');
            const tooltipSectional = document.getElementById('tooltip-sectional');
            const tooltipRecertification = document.getElementById('tooltip-recertification');
            const tooltipAllocation = document.getElementById('tooltip-allocation');
            const tooltipTotal = document.getElementById('tooltip-total');
            const tooltipSuccessRate = document.getElementById('tooltip-success-rate');

            if (tooltipDay) tooltipDay.textContent = `${data.day} - Applications Overview`;
            if (tooltipSectional) tooltipSectional.textContent = data.sectional;
            if (tooltipRecertification) tooltipRecertification.textContent = data.recertification;
            if (tooltipAllocation) tooltipAllocation.textContent = data.allocation;
            if (tooltipTotal) tooltipTotal.textContent = data.total;
            if (tooltipSuccessRate) tooltipSuccessRate.textContent = `${data.successRate}%`;

            // Position tooltip
            const rect = event.target.getBoundingClientRect();
            tooltip.style.left = `${rect.left + rect.width / 2}px`;
            tooltip.style.top = `${rect.top - 10}px`;
            tooltip.style.transform = 'translateX(-50%) translateY(-100%)';
            tooltip.classList.remove('opacity-0');
            tooltip.classList.add('opacity-100');
        }

        // Hide tooltip
        function hideTooltip() {
            const tooltip = document.getElementById('enhanced-chart-tooltip');
            if (!tooltip) return;
            
            tooltip.classList.remove('opacity-100');
            tooltip.classList.add('opacity-0');
        }

        // Generate Analytics Monthly Trends Chart
        function generateAnalyticsChart() {
            const container = document.getElementById('monthly-trends-chart');
            if (!container) return;
            
            container.innerHTML = '';

            monthlyTrendsData.forEach((data, index) => {
                const pointContainer = document.createElement('div');
                pointContainer.className = 'flex flex-col items-center flex-1 relative';

                // Calculate position
                const heightPercent = (data.value / maxAnalyticsValue) * 100;
                const point = document.createElement('div');
                point.className = 'analytics-chart-point';
                point.style.bottom = `${heightPercent}%`;
                point.title = `${data.month}: ${data.value} applications`;

                // Add line to next point
                if (index < monthlyTrendsData.length - 1) {
                    const nextData = monthlyTrendsData[index + 1];
                    const nextHeightPercent = (nextData.value / maxAnalyticsValue) * 100;
                    const line = document.createElement('div');
                    line.className = 'analytics-line';
                    line.style.bottom = `${Math.min(heightPercent, nextHeightPercent)}%`;
                    line.style.height = `${Math.abs(nextHeightPercent - heightPercent)}%`;
                    line.style.width = '100%';
                    line.style.left = '50%';
                    pointContainer.appendChild(line);
                }

                pointContainer.appendChild(point);
                container.appendChild(pointContainer);
            });
        }

        // Update chart totals in legend
        function updateChartTotals() {
            const totals = enhancedChartData.reduce((acc, day) => {
                if (!hiddenSeries.has('sectional')) acc.sectional += day.sectional;
                if (!hiddenSeries.has('recertification')) acc.recertification += day.recertification;
                if (!hiddenSeries.has('allocation')) acc.allocation += day.allocation;
                return acc;
            }, { sectional: 0, recertification: 0, allocation: 0 });

            const totalSectional = document.getElementById('total-sectional');
            const totalRecertification = document.getElementById('total-recertification');
            const totalAllocation = document.getElementById('total-allocation');
            const totalApplications = document.getElementById('total-applications');

            if (totalSectional) totalSectional.textContent = `(${totals.sectional})`;
            if (totalRecertification) totalRecertification.textContent = `(${totals.recertification})`;
            if (totalAllocation) totalAllocation.textContent = `(${totals.allocation})`;
            if (totalApplications) totalApplications.textContent = totals.sectional + totals.recertification + totals.allocation;
        }

        // Toggle chart series visibility
        function toggleChartSeries(series) {
            const legendElement = document.getElementById(`legend-${series}`);
            
            if (hiddenSeries.has(series)) {
                hiddenSeries.delete(series);
                if (legendElement) legendElement.classList.remove('inactive');
            } else {
                hiddenSeries.add(series);
                if (legendElement) legendElement.classList.add('inactive');
            }
            
            generateEnhancedSVGChart();
        }

        // Toggle dropdown
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Filter chart data
        function filterChart(filter) {
            currentFilter = filter;
            console.log(`Filtering chart by: ${filter}`);
            const dropdown = document.getElementById('filter-dropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
            
            // Here you would typically filter the data based on the selected filter
            // For demo purposes, we'll just show an alert
            if (filter !== 'all') {
                alert(`Chart filtered to show only ${filter} applications`);
            }
        }

        // Toggle chart view between bar and line
        function toggleChartView() {
            chartView = chartView === 'bar' ? 'line' : 'bar';
            const viewText = document.getElementById('chart-view-text');
            
            if (chartView === 'line') {
                if (viewText) viewText.textContent = 'Bar View';
            } else {
                if (viewText) viewText.textContent = 'Line View';
            }
            
            generateEnhancedSVGChart();
        }

        // Show insights
        function showInsights() {
            const insights = `
📊 Key Insights from Applications Data:

🔹 Peak Performance: Thursday shows highest application volume (136 apps) with 96.3% success rate
🔹 Processing Efficiency: Average processing time improved by 0.4 days this week
🔹 Success Trends: Overall success rate of 94.2% exceeds target of 90%
🔹 Weekend Pattern: Lower volumes on weekends but higher rejection rates (12.8% Sat, 14.3% Sun)
🔹 Sectional Titling: Leading application type with consistent high performance
🔹 Opportunity: Recertification applications show 15% growth trend

💡 Recommendations:
• Investigate weekend processing quality issues
• Optimize Thursday capacity to handle peak loads
• Consider staff training for recertification processes
            `;
            alert(insights);
        }

        // Download chart
        function downloadChart() {
            alert('Exporting comprehensive analytics report with charts, trends, and insights...');
        }

        // Refresh chart
        function refreshChart() {
            generateEnhancedSVGChart();
            generateAnalyticsChart();
            alert('Chart data refreshed with latest analytics');
        }

        // Show chart details
        function showChartDetails() {
            const details = `
📈 Applications Analytics Dashboard

📊 Data Overview:
• Total Applications: 720 (Last 7 days)
• Average Daily Volume: 103 applications
• Peak Day: Thursday (136 applications)
• Success Rate: 94.2% (Above target)
• Processing Time: 2.8 days average

🎯 Performance Metrics:
• Sectional Titling: 318 applications (44.2%)
• Recertification: 226 applications (31.4%)
• Allocation: 176 applications (24.4%)

📈 Trends:
• Week-over-week growth: +12.5%
• Processing time improvement: -0.4 days
• Success rate improvement: +2.1%

🔍 Quality Indicators:
• User Satisfaction: 4.7/5
• System Uptime: 99.8%
• Error Rate: <1%
            `;
            alert(details);
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target) && !event.target.closest('button')) {
                    dropdown.classList.remove('show');
                }
            });
        });
    </script>
           
          </div>

        <!-- Footer -->
        @include('admin.footer')
      </div>
 
<script>
        function showFullNames(owners) {
            if (Array.isArray(owners) && owners.length > 0) {
                Swal.fire({
                    title: 'Full Names of Multiple Owners',
                    text: 'The following names are associated with this application:',
                    html: '<ul>' + owners.map(name => `<li>${name}</li>`).join('') + '</ul>',
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            } else {
                Swal.fire({
                    title: 'Full Names of Multiple Owners',
                    text: 'No owners available',
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            }
        }

        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdownMenu = event.currentTarget.nextElementSibling;
            if (dropdownMenu) {
                dropdownMenu.classList.toggle('hidden');
            }
        }

        // Enhanced Dashboard JavaScript
        // System status simulation and management
        let systemStatus = {
            system: 'operational',
            database: 'connected',
            api: 'online',
            lastUpdated: new Date()
        };

        // Initialize dashboard enhancements
        document.addEventListener('DOMContentLoaded', function() {
            initializeDashboard();
            startSystemMonitoring();
            setupInteractiveFeatures();
        });

        function initializeDashboard() {
            // Show loading states initially
            showLoadingStates();
            
            // Simulate system initialization
            setTimeout(() => {
                updateSystemStatus('initializing', 'Initializing system components...');
                
                setTimeout(() => {
                    updateSystemStatus('connecting', 'Establishing database connection...');
                    
                    setTimeout(() => {
                        updateSystemStatus('loading', 'Loading dashboard data...');
                        
                        setTimeout(() => {
                            updateSystemStatus('operational', 'All systems operational');
                            loadRealData();
                        }, 1500);
                    }, 1000);
                }, 800);
            }, 500);
        }

        function showLoadingStates() {
            const loadingTexts = [
                'Initializing...',
                'Loading...',
                'Connecting...',
                'Syncing...',
                'Checking...'
            ];

            const elements = [
                'total-applications-count',
                'pending-approvals-count',
                'registered-properties-count',
                'active-users-count',
                'system-health-score'
            ];

            elements.forEach((id, index) => {
                const element = document.getElementById(id);
                if (element) {
                    element.innerHTML = `<span class="loading-shimmer">${loadingTexts[index]}</span>`;
                }
            });
        }

        function updateSystemStatus(status, message) {
            const statusIndicator = document.getElementById('system-status-indicator');
            const statusText = document.getElementById('system-status-text');
            
            if (statusIndicator && statusText) {
                statusText.textContent = message;
                
                // Update indicator color based on status
                statusIndicator.className = 'w-3 h-3 rounded-full';
                switch(status) {
                    case 'operational':
                        statusIndicator.classList.add('bg-green-500');
                        break;
                    case 'initializing':
                    case 'connecting':
                    case 'loading':
                        statusIndicator.classList.add('bg-yellow-500', 'animate-pulse');
                        break;
                    case 'error':
                        statusIndicator.classList.add('bg-red-500', 'animate-pulse');
                        break;
                    default:
                        statusIndicator.classList.add('bg-gray-500');
                }
            }
        }

        function loadRealData() {
            // Simulate loading real data with animation
            const stats = {
                totalApplications: 1284,
                pendingApprovals: 145,
                registeredProperties: 8549,
                activeUsers: 3672,
                systemHealth: 98
            };

            // Animate counters
            animateCounter('total-applications-count', stats.totalApplications, 2000);
            animateCounter('pending-approvals-count', stats.pendingApprovals, 1800);
            animateCounter('registered-properties-count', stats.registeredProperties, 2200);
            animateCounter('active-users-count', stats.activeUsers, 1900);
            animateCounter('system-health-score', stats.systemHealth, 1500, '%');

            // Update trends
            setTimeout(() => {
                const applicationsElement = document.getElementById('applications-trend');
                const pendingElement = document.getElementById('pending-trend');
                const propertiesElement = document.getElementById('properties-trend');
                const usersElement = document.getElementById('users-trend');
                const healthElement = document.getElementById('health-status');
                
                if (applicationsElement) applicationsElement.textContent = '12';
                if (pendingElement) pendingElement.textContent = '8';
                if (propertiesElement) propertiesElement.textContent = '4';
                if (usersElement) usersElement.textContent = '9';
                if (healthElement) healthElement.textContent = 'Excellent';
            }, 2500);

            // Show notification
            setTimeout(() => {
                showNotification('Dashboard data loaded successfully! All systems are running optimally.', 'success');
            }, 3000);
        }

        function animateCounter(elementId, targetValue, duration, suffix = '') {
            const element = document.getElementById(elementId);
            if (!element) return;

            const startValue = 0;
            const increment = targetValue / (duration / 16);
            let currentValue = startValue;

            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= targetValue) {
                    currentValue = targetValue;
                    clearInterval(timer);
                }
                
                element.textContent = Math.floor(currentValue).toLocaleString() + suffix;
            }, 16);
        }

        function startSystemMonitoring() {
            // Update system status every 30 seconds
            setInterval(() => {
                updateLastUpdatedTime();
                
                // Simulate occasional status changes
                if (Math.random() < 0.1) {
                    simulateSystemEvent();
                }
            }, 30000);
        }

        function updateLastUpdatedTime() {
            const lastUpdatedElement = document.getElementById('last-updated');
            if (lastUpdatedElement) {
                lastUpdatedElement.textContent = new Date().toLocaleTimeString();
            }
        }

        function simulateSystemEvent() {
            const events = [
                'Database optimization completed',
                'New user registration detected',
                'System backup completed successfully',
                'Performance monitoring active',
                'Security scan completed - no issues found'
            ];
            
            const randomEvent = events[Math.floor(Math.random() * events.length)];
            showNotification(randomEvent, 'info');
        }

        function refreshSystemStatus() {
            updateSystemStatus('loading', 'Refreshing system status...');
            
            // Simulate refresh delay
            setTimeout(() => {
                updateSystemStatus('operational', 'All systems operational');
                updateLastUpdatedTime();
                showNotification('System status refreshed successfully', 'success');
            }, 1500);
        }

        function showNotification(message, type = 'info') {
            const banner = document.getElementById('notification-banner');
            const text = document.getElementById('notification-text');
            
            if (banner && text) {
                text.textContent = message;
                banner.classList.remove('hidden');
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    dismissNotification();
                }, 5000);
            }
        }

        function dismissNotification() {
            const banner = document.getElementById('notification-banner');
            if (banner) {
                banner.classList.add('hidden');
            }
        }

        function setupInteractiveFeatures() {
            // Chart interaction functions
            window.filterChart = function(filter) {
                console.log('Filtering chart by:', filter);
                showNotification(`Chart filtered by ${filter} status`, 'info');
            };

            window.downloadChart = function() {
                showNotification('Chart data export initiated', 'success');
            };

            window.refreshChart = function() {
                showNotification('Chart data refreshed', 'success');
            };

            window.showInsights = function() {
                showNotification('AI-powered insights: Peak application times are between 10 AM - 2 PM', 'info');
            };

            window.toggleChartView = function() {
                const text = document.getElementById('chart-view-text');
                if (text && text.textContent === 'Line View') {
                    text.textContent = 'Bar View';
                    showNotification('Switched to line chart view', 'info');
                } else if (text) {
                    text.textContent = 'Line View';
                    showNotification('Switched to bar chart view', 'info');
                }
            };

            window.showChartDetails = function() {
                showNotification('Detailed analytics: 94.2% success rate, 2.8 days avg processing time', 'info');
            };

            window.toggleChartSeries = function(series) {
                const legend = document.getElementById(`legend-${series}`);
                if (legend) {
                    legend.classList.toggle('inactive');
                    showNotification(`${series} series toggled`, 'info');
                }
            };
        }

        // Add CSS for loading shimmer effect
        const style = document.createElement('style');
        style.textContent = `
            .loading-shimmer {
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: shimmer 1.5s infinite;
                border-radius: 4px;
                display: inline-block;
                padding: 2px 8px;
            }
            
            @keyframes shimmer {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
        `;
        document.head.appendChild(style);

        // Real-time clock update
        setInterval(() => {
            updateLastUpdatedTime();
        }, 1000);

        document.addEventListener('click', () => {
            const dropdownMenus = document.querySelectorAll('.dropdown-menu');
            dropdownMenus.forEach(menu => menu.classList.add('hidden'));
        });
</script>
@endsection
