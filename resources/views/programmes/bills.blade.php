@extends('layouts.app')

@section('page-title')
    {{ $PageTitle ?? __('Bills Management') }}
@endsection

@section('content')
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include($headerPartial ?? 'admin.header')

        <!-- Main Content -->
        <div class="p-6">
            <div class="bg-white rounded-lg shadow-sm" style="width: 60%; margin: 0 auto;">
                <!-- Page Header -->
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $PageTitle }}</h1>
                            <p class="text-sm text-gray-600 mt-1">{{ $PageDescription }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button id="exportAllBills" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                Export All Bills
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Section -->
                <div class="px-6 py-6 bg-gray-50 border-b border-gray-200">
                    <div class="max-w-md mx-auto">
                        <label for="filenoSelect" class="block text-sm font-medium text-gray-700 mb-3 text-center">
                            <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                            Search by File Number
                        </label>
                        <select id="filenoSelect" class="w-full" style="width: 100%;">
                            <option value="">Select a file number to view bills...</option>
                            @foreach($allFiles as $file)
                                <option value="{{ $file['id'] }}" data-type="{{ $file['type'] }}" data-fileno="{{ $file['fileno'] }}" data-owner="{{ $file['owner_name'] }}">
                                    {{ $file['fileno'] }} - {{ $file['owner_name'] }} ({{ ucfirst($file['type']) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Bills Content -->
                <div class="px-6 py-6">
                    <!-- Default State - No File Selected -->
                    <div id="no-selection" class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="file-search" class="w-12 h-12 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Select a File Number</h3>
                        <p class="text-gray-500 max-w-md mx-auto">Choose a file number from the dropdown above to view and manage bills for that application.</p>
                    </div>

                    <!-- Bills Display Area -->
                    <div id="bills-container" class="hidden">
                        <!-- Application Info Header -->
                        <div id="application-info" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900" id="app-owner-name">-</h3>
                                    <p class="text-sm text-gray-600">
                                        Application ID: <span class="font-medium" id="app-id">-</span> | 
                                        File No: <span class="font-medium" id="app-fileno">-</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" id="app-type">
                                        <i data-lucide="file-text" class="w-4 h-4 mr-1"></i>
                                        -
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs Navigation -->
                        <div class="mb-6">
                            <nav class="flex space-x-8" aria-label="Tabs">
                                <button class="tab-button active py-3 px-1 border-b-2 font-medium text-sm focus:outline-none" data-tab="initial">
                                    <div class="flex items-center">
                                        <i data-lucide="banknote" class="w-4 h-4 mr-2"></i>
                                        INITIAL BILL
                                    </div>
                                </button>
                                <button class="tab-button py-3 px-1 border-b-2 font-medium text-sm focus:outline-none" data-tab="betterment" id="betterment-tab-btn">
                                    <div class="flex items-center">
                                        <i data-lucide="calculator" class="w-4 h-4 mr-2"></i>
                                        BETTERMENT BILL
                                    </div>
                                </button>
                                <button class="tab-button py-3 px-1 border-b-2 font-medium text-sm focus:outline-none" data-tab="balance" id="balance-tab-btn">
                                    <div class="flex items-center">
                                        <i data-lucide="file-check" class="w-4 h-4 mr-2"></i>
                                        BILL BALANCE
                                    </div>
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content-container">
                            <!-- Initial Bills Tab -->
                            <div id="initial-tab" class="tab-content">
                                <div id="initial-bills-content">
                                    <!-- Bills will be loaded here -->
                                </div>
                            </div>

                            <!-- Betterment Bills Tab -->
                            <div id="betterment-tab" class="tab-content hidden">
                                <div id="betterment-bills-content">
                                    <!-- Bills will be loaded here -->
                                </div>
                            </div>

                            <!-- Bill Balance Tab -->
                            <div id="balance-tab" class="tab-content hidden">
                                <div id="balance-bills-content">
                                    <!-- Bills will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page Footer -->
            @include($footerPartial ?? 'admin.footer')
        </div>
    </div>

    <!-- Include Select2 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .tab-button {
            color: #6b7280;
            border-bottom-color: transparent;
            transition: all 0.2s;
        }
        
        .tab-button.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }
        
        .tab-button:hover:not(.disabled) {
            color: #4b5563;
        }
        
        .tab-button.disabled {
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.5;
        }
        
        .tab-button.disabled:hover {
            color: #9ca3af;
        }
        
        .tab-content {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 48px;
            border: 2px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.75rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
            color: #374151;
            font-size: 0.875rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
        }
        
        .select2-dropdown {
            border: 2px solid #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6;
        }

        /* Bill Card Styles */
        .bill-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .bill-card:hover {
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Betterment and Balance Tab Button Styles */
        .betterment-tab-button, .balance-tab-button {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: background-color 0.2s;
            border: 1px solid #d1d5db;
            background-color: white;
            color: #6b7280;
        }

        .betterment-tab-button.active, .balance-tab-button.active {
            background-color: #f3f4f6;
            font-weight: 500;
            color: #374151;
            border-color: #9ca3af;
        }

        .betterment-tab-button:hover:not(.active), .balance-tab-button:hover:not(.active) {
            background-color: #f9fafb;
        }

        /* Tab Content Styles */
        .betterment-tab-content, .balance-tab-content {
            display: none;
        }

        .betterment-tab-content.active, .balance-tab-content.active {
            display: block;
        }

        /* Enhanced Print Styles */
        @media print {
            @page {
                margin: 0.5in;
                size: A4;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                font-family: 'Times New Roman', serif !important;
                font-size: 12pt !important;
                line-height: 1.4 !important;
                color: black !important;
            }
            
            body * {
                visibility: hidden !important;
            }
            
            .print-area, .print-area * {
                visibility: visible !important;
            }
            
            .print-area {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                background: white !important;
                padding: 20px !important;
                margin: 0 !important;
                font-family: 'Times New Roman', serif !important;
                color: black !important;
            }
            
            .print-header {
                width: 100% !important;
                margin-bottom: 30px !important;
                page-break-inside: avoid !important;
            }
            
            .print-logos {
                display: table !important;
                width: 100% !important;
                margin-bottom: 20px !important;
                border-collapse: separate !important;
                table-layout: fixed !important;
            }
            
            .print-logo-left {
                display: table-cell !important;
                width: 100px !important;
                vertical-align: middle !important;
                text-align: left !important;
            }
            
            .print-logo-right {
                display: table-cell !important;
                width: 100px !important;
                vertical-align: middle !important;
                text-align: right !important;
            }
            
            .print-title {
                display: table-cell !important;
                vertical-align: middle !important;
                text-align: center !important;
                padding: 0 20px !important;
            }
            
            .print-logo {
                width: 80px !important;
                height: 80px !important;
                max-width: 80px !important;
                max-height: 80px !important;
                display: block !important;
            }
            
            .print-title h1 {
                font-size: 16pt !important;
                font-weight: bold !important;
                color: black !important;
                margin: 0 !important;
                line-height: 1.2 !important;
                text-align: center !important;
            }
            
            .print-title h2 {
                font-size: 14pt !important;
                font-weight: bold !important;
                color: black !important;
                margin: 5px 0 0 0 !important;
                text-align: center !important;
            }
            
            .print-content {
                font-size: 12pt !important;
                line-height: 1.4 !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .print-content p {
                margin: 8px 0 !important;
                color: black !important;
            }
            
            .print-content strong,
            .print-content b {
                color: black !important;
                font-weight: bold !important;
            }
            
            .print-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin: 20px 0 !important;
                font-size: 11pt !important;
                color: black !important;
            }
            
            .print-table th,
            .print-table td {
                border: 1px solid black !important;
                padding: 8px !important;
                text-align: left !important;
                color: black !important;
                background: white !important;
            }
            
            .print-table th {
                background-color: #f5f5f5 !important;
                font-weight: bold !important;
                color: black !important;
            }
            
            .print-table .total-row {
                background-color: #f0f0f0 !important;
                font-weight: bold !important;
                color: black !important;
            }
            
            .print-table .total-row td {
                background-color: #f0f0f0 !important;
                color: black !important;
                font-weight: bold !important;
            }
            
            .print-footer {
                margin-top: 30px !important;
                font-size: 11pt !important;
                color: black !important;
            }
            
            .print-footer p {
                color: black !important;
                margin: 8px 0 !important;
            }
            
            .no-print {
                display: none !important;
                visibility: hidden !important;
            }
            
            /* Date and reference styling */
            .print-date-ref {
                text-align: right !important;
                margin-bottom: 20px !important;
                color: black !important;
            }
            
            .print-date-ref p {
                color: black !important;
                margin: 4px 0 !important;
            }
            
            .print-date-ref .ref-highlight {
                color: black !important;
                font-weight: bold !important;
            }
        }
    </style>

    <script>
        // Store all bills data
        let allBillsData = {
            initial: @json($initialBills),
            betterment: @json($bettermentBills),
            balance: @json($finalBills)
        };

        // Store current application data for export
        let currentApplication = {
            fileId: null,
            fileType: null,
            fileno: null,
            owner: null
        };

        // Store generated bills for persistence
        let generatedBills = {
            betterment: null,
            balance: null
        };

        $(document).ready(function() {
            // Initialize Select2
            $('#filenoSelect').select2({
                placeholder: 'Search by file number or owner name...',
                allowClear: true,
                width: '100%',
                templateResult: function(option) {
                    if (!option.id) return option.text;
                    
                    var parts = option.text.split(' - ');
                    var fileno = parts[0];
                    var ownerInfo = parts[1];
                    var type = $(option.element).data('type');
                    
                    var $option = $(
                        '<div class="flex items-center justify-between p-2">' +
                            '<div>' +
                                '<div class="font-medium text-gray-900">' + fileno + '</div>' +
                                '<div class="text-sm text-gray-500">' + ownerInfo + '</div>' +
                            '</div>' +
                            '<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">' + type.charAt(0).toUpperCase() + type.slice(1) + '</span>' +
                        '</div>'
                    );
                    return $option;
                },
                templateSelection: function(option) {
                    if (!option.id) return option.text;
                    return $(option.element).data('fileno') + ' - ' + $(option.element).data('owner');
                }
            });

            // File number selection handler
            $('#filenoSelect').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var fileId = $(selectedOption).val();
                var fileType = $(selectedOption).data('type');
                var fileno = $(selectedOption).data('fileno');
                var owner = $(selectedOption).data('owner');
                
                loadBillsForFile(fileId, fileType, fileno, owner);
            });

            // Clear selection handler
            $('#filenoSelect').on('select2:clear', function(e) {
                showNoSelection();
            });

            // Tab functionality
            $('.tab-button').click(function() {
                // Check if tab is disabled
                if ($(this).hasClass('disabled')) {
                    return false;
                }
                
                var tabId = $(this).data('tab');
                
                // Update active tab button
                $('.tab-button').removeClass('active');
                $(this).addClass('active');
                
                // Show corresponding tab content
                $('.tab-content').addClass('hidden');
                $('#' + tabId + '-tab').removeClass('hidden');
            });

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function showNoSelection() {
            $('#no-selection').removeClass('hidden');
            $('#bills-container').addClass('hidden');
            
            currentApplication = {
                fileId: null,
                fileType: null,
                fileno: null,
                owner: null
            };
        }

        function loadBillsForFile(fileId, fileType, fileno, owner) {
            // Hide no selection state
            $('#no-selection').addClass('hidden');
            $('#bills-container').removeClass('hidden');

            // Store current application data
            currentApplication = {
                fileId: fileId,
                fileType: fileType,
                fileno: fileno,
                owner: owner
            };

            // Update application info
            $('#app-owner-name').text(owner);
            $('#app-id').text('ST-2025-' + String(fileId).padStart(4, '0'));
            $('#app-fileno').text(fileno);
            $('#app-type').html('<i data-lucide="file-text" class="w-4 h-4 mr-1"></i>' + fileType.charAt(0).toUpperCase() + fileType.slice(1) + ' Application');

            // Filter bills for this file
            var initialBills = filterBillsByFile(allBillsData.initial, fileId, fileType);
            var bettermentBills = filterBillsByFile(allBillsData.betterment, fileId, fileType);
            var balanceBills = filterBillsByFile(allBillsData.balance, fileId, fileType);

            // Conditionally disable tabs based on application type
            manageTabsBasedOnApplicationType(fileType);

            // Load bill cards
            loadInitialBills(initialBills);
            loadBettermentBills(bettermentBills);
            loadBalanceBills(balanceBills);

            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function filterBillsByFile(bills, fileId, fileType) {
            return bills.filter(function(bill) {
                if (fileType === 'primary') {
                    return bill.application_id == fileId;
                } else {
                    return bill.sub_application_id == fileId;
                }
            });
        }

        function manageTabsBasedOnApplicationType(fileType) {
            // Reset all tabs to enabled state first
            $('.tab-button').removeClass('disabled');
            
            if (fileType === 'primary') {
                // For primary applications: disable BILL BALANCE tab
                $('#balance-tab-btn').addClass('disabled');
                
                // If the currently active tab is the disabled one, switch to initial
                if ($('#balance-tab-btn').hasClass('active')) {
                    $('#balance-tab-btn').removeClass('active');
                    $('[data-tab="initial"]').addClass('active');
                    $('.tab-content').addClass('hidden');
                    $('#initial-tab').removeClass('hidden');
                }
            } else if (fileType === 'unit' || fileType === 'secondary') {
                // For unit applications: disable BETTERMENT BILL tab only
                $('#betterment-tab-btn').addClass('disabled');
                
                // If the currently active tab is the disabled one, switch to initial
                if ($('#betterment-tab-btn').hasClass('active')) {
                    $('.tab-button').removeClass('active');
                    $('[data-tab="initial"]').addClass('active');
                    $('.tab-content').addClass('hidden');
                    $('#initial-tab').removeClass('hidden');
                }
            }
        }

        function loadInitialBills(bills) {
            var container = $('#initial-bills-content');
            
            if (bills.length === 0) {
                container.html(getEmptyState('initial', 'No initial bills found for this application.'));
                return;
            }

            var html = '';
            bills.forEach(function(bill) {
                // Check for primary or unit application fields
                var applicationFee, processingFee, sitePlanFee, paymentDate, receiptNumber;
                
                if (bill.primary_application_fee !== null && bill.primary_application_fee !== undefined) {
                    // Primary application
                    applicationFee = parseFloat(bill.primary_application_fee || 0);
                    processingFee = parseFloat(bill.primary_processing_fee || 0);
                    sitePlanFee = parseFloat(bill.primary_site_plan_fee || 0);
                    paymentDate = bill.primary_payment_date;
                    receiptNumber = bill.primary_receipt_number;
                } else if (bill.unit_application_fee !== null && bill.unit_application_fee !== undefined) {
                    // Unit application
                    applicationFee = parseFloat(bill.unit_application_fee || 0);
                    processingFee = parseFloat(bill.unit_processing_fee || 0);
                    sitePlanFee = parseFloat(bill.unit_site_plan_fee || 0);
                    paymentDate = bill.unit_payment_date;
                    receiptNumber = bill.unit_receipt_number;
                } else {
                    // Fallback to original field names
                    applicationFee = parseFloat(bill.Scheme_Application_Fee || bill.Unit_Application_Fees || 0);
                    processingFee = parseFloat(bill.Processing_Fee || 0);
                    sitePlanFee = parseFloat(bill.Site_Plan_Fee || 0);
                    paymentDate = bill.payment_date;
                    receiptNumber = bill.receipt_number;
                }
                
                var total = applicationFee + processingFee + sitePlanFee;
                
                var billId = bill.id || bill.billing_id || bill.bill_id || bill.application_id || bill.sub_application_id || 'unknown';
                
                html += createBillCard({
                    id: billId,
                    title: 'Initial Application Bill',
                    subtitle: 'Application Fee, Processing Fee, Site Plan Fee',
                    items: [
                        { label: 'Application Fee', amount: applicationFee },
                        { label: 'Processing Fee', amount: processingFee },
                        { label: 'Site Plan Fee', amount: sitePlanFee }
                    ],
                    total: total,
                    status: bill.Payment_Status,
                    date: paymentDate || bill.created_at,
                    receipt_number: receiptNumber,
                    type: 'initial'
                });
            });
            
            container.html(html);
        }

        function loadBettermentBills(bills) {
            var container = $('#betterment-bills-content');
            
            // Always show the betterment bill generation form for primary applications
            if (currentApplication.fileType === 'primary') {
                container.html(getBettermentBillForm());
                return;
            }
            
            // For non-primary applications, show empty state
            container.html(getEmptyState('betterment', 'Betterment bills are only available for primary applications.'));
        }

        function loadBalanceBills(bills) {
            var container = $('#balance-bills-content');
            
            // Always show the balance bill generation form for unit applications
            if (currentApplication.fileType === 'unit' || currentApplication.fileType === 'secondary') {
                container.html(getBalanceBillForm());
                return;
            }
            
            // For non-unit applications, show empty state
            container.html(getEmptyState('balance', 'Bill balance is only available for unit applications.'));
        }

        function createBillCard(data) {
            var statusClass = getStatusClass(data.status, data.type);
            var statusText = getStatusText(data.status, data.type);
            var formattedDate = data.date ? new Date(data.date).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            }) : 'N/A';

            var itemsHtml = '';
            data.items.forEach(function(item) {
                itemsHtml += `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">${item.label}</span>
                        <span class="font-medium">₦${parseFloat(item.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                    </div>
                `;
            });

            return `
                <div class="bill-card p-4 mb-3">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">${data.title}</h3>
                            <p class="text-xs text-gray-500">${data.subtitle}</p>
                        </div>
                        <span class="status-badge ${statusClass}">
                            <i data-lucide="${getStatusIcon(data.status, data.type)}" class="w-3 h-3 mr-1"></i>
                            ${statusText}
                        </span>
                    </div>

                    <div class="space-y-1 mb-3">
                        ${itemsHtml}
                    </div>

                    <hr class="my-3">

                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <p class="text-xs text-gray-500">Total Amount</p>
                            <p class="text-lg font-bold text-gray-900">₦${parseFloat(data.total).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Date</p>
                            <p class="text-xs font-medium">${formattedDate}</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="printBill('${data.type}', '${data.id}')" class="flex items-center px-2 py-1 text-xs border border-gray-300 rounded bg-white hover:bg-gray-50 transition-colors">
                            <i data-lucide="printer" class="w-3 h-3 mr-1"></i>
                            Print
                        </button>
                    </div>
                </div>
            `;
        }

        function getEmptyState(type, message) {
            var icons = {
                initial: 'banknote',
                betterment: 'calculator', 
                balance: 'file-check'
            };

            return `
                <div class="text-center py-12">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="${icons[type]}" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Bills Found</h3>
                    <p class="text-gray-500">${message}</p>
                </div>
            `;
        }

        function getStatusClass(status, type) {
            if (type === 'balance') {
                switch(status) {
                    case 'paid': return 'status-paid';
                    case 'generated':
                    case 'sent': return 'status-pending';
                    default: return 'status-overdue';
                }
            } else {
                switch(status) {
                    case 'Complete': return 'status-paid';
                    case 'Incomplete': return 'status-pending';
                    default: return 'status-overdue';
                }
            }
        }

        function getStatusText(status, type) {
            if (type === 'balance') {
                switch(status) {
                    case 'paid': return 'Paid';
                    case 'generated': return 'Generated';
                    case 'sent': return 'Sent';
                    case 'cancelled': return 'Cancelled';
                    default: return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown';
                }
            } else {
                switch(status) {
                    case 'Complete': return 'Paid';
                    case 'Incomplete': return 'Pending';
                    default: return status || 'Paid';
                }
            }
        }

        function getStatusIcon(status, type) {
            if (type === 'balance') {
                switch(status) {
                    case 'paid': return 'check-circle';
                    case 'generated':
                    case 'sent': return 'clock';
                    case 'cancelled': return 'x-circle';
                    default: return 'help-circle';
                }
            } else {
                switch(status) {
                    case 'Complete': return 'check-circle';
                    case 'Incomplete': return 'clock';
                    default: return 'alert-circle';
                }
            }
        }

        function printBill(billType, billId) {
            if (!billId || billId === 'undefined') {
                alert('Bill ID is missing. Cannot print bill.');
                return;
            }

            console.log('Printing bill:', billType, billId);
            
            // Open print view in new window
            const baseUrl = window.location.origin;
            const printUrl = `${baseUrl}/programmes/bill/print/${billType}/${billId}`;
            window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
        }

        // Generate Betterment Bill Form
        function getBettermentBillForm() {
            // Get application data for the current file
            var landSize = '1,200'; // Default value
            var unitsCount = '12'; // Default value
            
            // Try to get actual values from the current application
            if (currentApplication && currentApplication.fileId) {
                // Fetch application details to get plot_size and NoOfUnits
                fetchApplicationDetails(currentApplication.fileId, currentApplication.fileType);
            }
            
            return `
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="p-4 border-b">
                        <h3 class="text-sm font-medium">Generate Betterment Bill</h3>
                        <p class="text-xs text-gray-500">Generate and manage betterment charges for primary applications</p>
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <button class="betterment-tab-button active" data-tab="generate">
                                <i data-lucide="calculator" class="w-3.5 h-3.5 mr-1.5"></i>
                                GENERATE BETTERMENT BILL
                            </button>
                            <button class="betterment-tab-button" data-tab="receipt">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 mr-1.5"></i>
                                BETTERMENT BILL RECEIPT
                            </button>
                        </div>

                        <!-- Generate Tab -->
                        <div id="betterment-generate-tab" class="betterment-tab-content active">
                            <div class="p-4 border border-gray-200 rounded-md">
                                <h4 class="text-sm font-medium mb-3">Calculate Betterment Charges</h4>
                                <form id="betterment-form" class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label for="betterment-property-value" class="text-xs font-medium block">
                                                Property Value (₦)
                                            </label>
                                            <input id="betterment-property-value" name="property_value" type="text" value="0.00"
                                                class="w-full p-2 border border-gray-300 rounded-md text-sm" required>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="betterment-rate" class="text-xs font-medium block">
                                                Betterment Rate (%)
                                            </label>
                                            <input id="betterment-rate" name="betterment_rate" type="text" value="2.5"
                                                class="w-full p-2 border border-gray-300 rounded-md text-sm" required>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label for="betterment-land-size" class="text-xs font-medium block">
                                                Land Size (sqm)
                                            </label>
                                            <input id="betterment-land-size" name="land_size" type="text" value="${landSize}"
                                                class="w-full p-2 border border-gray-300 rounded-md text-sm bg-gray-50" readonly>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="betterment-units-count" class="text-xs font-medium block">
                                                Number of Units
                                            </label>
                                            <input id="betterment-units-count" name="units_count" type="text" value="${unitsCount}"
                                                class="w-full p-2 border border-gray-300 rounded-md text-sm bg-gray-50" readonly>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 p-3 rounded-md">
                                        <h4 class="text-xs font-medium">Calculation Formula</h4>
                                        <p class="text-xs text-gray-500">Betterment Fee = Property Value × Betterment Rate × Land Size Factor</p>
                                        <p class="text-xs text-gray-400 mt-1">Land Size Factor is automatically calculated based on the property size.</p>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-xs text-gray-500">Estimated Amount</p>
                                            <p class="text-lg font-bold" id="betterment-amount">₦0.00</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" id="calculate-betterment-btn" class="px-3 py-1 text-xs bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                                <i data-lucide="calculator" class="w-3.5 h-3.5 mr-1.5 inline-block"></i>
                                                Calculate
                                            </button>
                                            <button type="button" id="generate-betterment-btn" class="px-3 py-1 text-xs bg-green-600 text-white rounded-md hover:bg-green-700">
                                                <i data-lucide="save" class="w-3.5 h-3.5 mr-1.5 inline-block"></i>
                                                Generate Bill
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Receipt Tab -->
                        <div id="betterment-receipt-tab" class="betterment-tab-content hidden">
                            <div class="p-4 border border-gray-200 rounded-md">
                                <h4 class="text-sm font-medium mb-3">Betterment Bill Receipt</h4>
                                <div id="betterment-receipt-container">
                                    <div class="text-center p-8">
                                        <p class="text-sm text-gray-500">No betterment bill has been generated yet.</p>
                                        <p class="text-xs text-gray-400 mt-2">Please generate a bill first.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Generate Balance Bill Form
        function getBalanceBillForm() {
            return `
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="p-4 border-b">
                        <h3 class="text-sm font-medium">Unit Application Final Bill Balance</h3>
                        <p class="text-xs text-gray-500">Calculate, generate and preview final bill balance for unit applications</p>
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <button class="balance-tab-button active" data-tab="calculate">
                                <i data-lucide="calculator" class="w-3.5 h-3.5 mr-1.5"></i>
                                CALCULATE & GENERATE BILL
                            </button>
                            <button class="balance-tab-button" data-tab="preview">
                                <i data-lucide="eye" class="w-3.5 h-3.5 mr-1.5"></i>
                                PREVIEW BILL
                            </button>
                        </div>

                        <!-- Calculate Tab -->
                        <div id="balance-calculate-tab" class="balance-tab-content active">
                            <div class="p-4 border border-gray-200 rounded-md">
                                <h4 class="text-sm font-medium mb-3">Generate Bill Balance</h4>
                                <form id="balance-form" class="space-y-4">
                                    <!-- Owner Details (Disabled) -->
                                    <div class="mb-3">
                                        <div class="space-y-2">
                                            <label for="balance-bill-ref-id" class="text-xs font-medium text-green-600">Bill Reference ID</label>
                                            <input id="balance-bill-ref-id" name="bill_ref_id" type="text" 
                                                value="ST-BILL-${currentApplication.fileId}-${new Date().toISOString().slice(0,10).replace(/-/g,'')}-${Math.floor(Math.random() * 9000) + 1000}"
                                                class="w-full p-2 border border-gray-300 rounded-md text-sm bg-gray-100" readonly>
                                        </div>
                                        <br>
                                        <h5 class="text-xs font-semibold mb-2">Owner Details</h5>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label for="balance-file-no" class="text-xs font-medium">File Number</label>
                                                <input id="balance-file-no" type="text" value="${currentApplication.fileno}" 
                                                    class="w-full p-2 border border-gray-300 rounded-md text-sm bg-gray-100" disabled>
                                            </div>
                                            <div class="space-y-2">
                                                <label for="balance-owner-name" class="text-xs font-medium">Owner Name</label>
                                                <input id="balance-owner-name" type="text" value="${currentApplication.owner}" 
                                                    class="w-full p-2 border border-gray-300 rounded-md text-sm bg-gray-100" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fee Customization -->
                                    <div class="mb-3">
                                        <h5 class="text-xs font-semibold mb-2">Charges & Fees</h5>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label for="balance-assignment-fee" class="text-xs font-medium">Assignment Fee (₦)</label>
                                                <input id="balance-assignment-fee" name="assignment_fee" type="text" value="0.00" 
                                                    class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                            </div>
                                            <div class="space-y-2">
                                                <label for="balance-bill-balance" class="text-xs font-medium">Bill Balance (₦)</label>
                                                <input id="balance-bill-balance" name="bill_balance" type="text" value="0.00" 
                                                    class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                            </div>
                                            <div class="space-y-2">
                                                <label for="balance-recertification-fee" class="text-xs font-medium">Recertification Fee (₦)</label>
                                                <input id="balance-recertification-fee" name="recertification_fee" type="text" value="0.00" 
                                                    class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                            </div>
                                            <div class="space-y-2">
                                                <label for="balance-bill-date" class="text-xs font-medium">Bill Date</label>
                                                <input id="balance-bill-date" name="bill_date" type="date" value="${new Date().toISOString().slice(0,10)}" 
                                                    class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Development Charges -->
                                    <div class="space-y-2">
                                        <label for="balance-dev-charges" class="text-xs font-medium">Development Charges (₦)</label>
                                        <input id="balance-dev-charges" name="dev_charges" type="text" value="0.00" 
                                            class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                    </div>

                                    <!-- Total Amount (Calculated) -->
                                    <div class="mt-4 p-3 bg-gray-100 rounded-md">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-xs text-gray-600">Total Amount:</p>
                                                <p class="text-lg font-bold" id="balance-calculated-total">₦85,525.00</p>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="button" id="calculate-balance-total-btn" class="px-3 py-1 text-xs bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                                    <i data-lucide="calculator" class="w-3.5 h-3.5 mr-1.5 inline-block"></i>
                                                    Calculate
                                                </button>
                                                <button type="button" id="save-balance-bill-btn" class="px-3 py-1 text-xs bg-green-600 text-white rounded-md hover:bg-green-700">
                                                    <i data-lucide="save" class="w-3.5 h-3.5 mr-1.5 inline-block"></i>
                                                    Generate Bill
                                                </button>
                                                <button type="button" id="preview-balance-bill-btn" class="px-3 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                    <i data-lucide="eye" class="w-3.5 h-3.5 mr-1.5 inline-block"></i>
                                                    Preview Bill
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Preview Tab -->
                        <div id="balance-preview-tab" class="balance-tab-content hidden">
                            <div id="balance-preview-container">
                                <div class="text-center p-8">
                                    <p class="text-sm text-gray-500">No bill preview available yet.</p>
                                    <p class="text-xs text-gray-400 mt-2">Please calculate and generate a bill first.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Event delegation for dynamically created elements
        $(document).on('click', '.betterment-tab-button', function() {
            var tab = $(this).data('tab');
            
            // Update active tab button
            $('.betterment-tab-button').removeClass('active');
            $(this).addClass('active');
            
            // Hide all tab contents
            $('.betterment-tab-content').removeClass('active').addClass('hidden');
            
            // Show selected tab
            $('#betterment-' + tab + '-tab').removeClass('hidden').addClass('active');
            
            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        $(document).on('click', '.balance-tab-button', function() {
            var tab = $(this).data('tab');
            
            // Update active tab button
            $('.balance-tab-button').removeClass('active');
            $(this).addClass('active');
            
            // Hide all tab contents
            $('.balance-tab-content').removeClass('active').addClass('hidden');
            
            // Show selected tab
            $('#balance-' + tab + '-tab').removeClass('hidden').addClass('active');
            
            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Betterment bill calculation
        $(document).on('click', '#calculate-betterment-btn', function() {
            var propertyValue = parseFloat($('#betterment-property-value').val().replace(/,/g, '')) || 0;
            var bettermentRate = parseFloat($('#betterment-rate').val()) || 0;
            
            if (propertyValue === 0 || bettermentRate === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Input',
                    text: 'Please enter valid property value and betterment rate.',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Calculating...',
                text: 'Please wait while we calculate the betterment charges.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Simulate calculation delay
            setTimeout(() => {
                // Simple calculation: Property Value × Betterment Rate / 100
                var bettermentAmount = (propertyValue * bettermentRate) / 100;
                
                $('#betterment-amount').text('₦' + bettermentAmount.toLocaleString('en-US', {minimumFractionDigits: 2}));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Calculation Complete!',
                    text: `Betterment charges calculated: ₦${bettermentAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}`,
                    confirmButtonColor: '#10b981'
                });
            }, 1500);
        });

        // Generate betterment bill with saving functionality
        $(document).on('click', '#generate-betterment-btn', function() {
            var propertyValue = parseFloat($('#betterment-property-value').val().replace(/,/g, '')) || 0;
            var bettermentRate = parseFloat($('#betterment-rate').val()) || 0;
            
            if (propertyValue === 0 || bettermentRate === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Calculation',
                    text: 'Please calculate the betterment amount first.',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            
            var bettermentAmount = (propertyValue * bettermentRate) / 100;
            
            // Show loading
            Swal.fire({
                title: 'Generating Bill...',
                text: 'Please wait while we generate your betterment bill.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Save betterment bill data to localStorage
            var billData = {
                application_id: currentApplication.fileId,
                property_value: propertyValue,
                betterment_rate: bettermentRate,
                betterment_amount: bettermentAmount,
                land_size: $('#betterment-land-size').val(),
                units_count: $('#betterment-units-count').val(),
                bill_reference: `BB-${currentApplication.fileId}-${new Date().toISOString().slice(0,10).replace(/-/g,'')}`,
                generated_date: new Date().toISOString().slice(0,10),
                file_no: currentApplication.fileno,
                owner_name: currentApplication.owner
            };
            
            // Store in localStorage for persistence
            generatedBills.betterment = billData;
            localStorage.setItem('betterment_bill_' + currentApplication.fileId, JSON.stringify(billData));
            
            // Simulate generation delay
            setTimeout(() => {
                // Update receipt tab with generated bill
                var receiptHtml = getBettermentReceiptHtml(propertyValue, bettermentRate, bettermentAmount);
                
                $('#betterment-receipt-container').html(receiptHtml);
                $('.betterment-tab-button[data-tab="receipt"]').click();
                
                // Reinitialize Lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Bill Generated & Saved Successfully!',
                    text: `Betterment bill generated for ₦${bettermentAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}`,
                    confirmButtonColor: '#10b981'
                });
            }, 2000);
        });

        // Function to generate betterment receipt HTML with proper print structure
        function getBettermentReceiptHtml(propertyValue, bettermentRate, bettermentAmount) {
            return `
                <div class="print-area">
                    <!-- Header with logos -->
                    <div class="print-header">
                        <div class="print-logos">
                            <div class="print-logo-left">
                                <img src="/assets/logo/logo1.jpg" alt="Kano State Logo" class="print-logo">
                            </div>
                            <div class="print-title">
                                <h1>KANO STATE MINISTRY OF LAND AND PHYSICAL PLANNING</h1>
                                <h2>BETTERMENT CHARGES BILL</h2>
                            </div>
                            <div class="print-logo-right">
                                <img src="/assets/logo/logo3.jpeg" alt="Ministry Logo" class="print-logo">
                            </div>
                        </div>
                    </div>
                    
                    <div class="print-content">
                        <!-- Date and Reference -->
                        <div class="print-date-ref">
                            <p><strong>Date:</strong> ${new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            <p><strong>Bill Reference:</strong> <span class="ref-highlight">BB-${currentApplication.fileId}-${new Date().toISOString().slice(0,10).replace(/-/g,'')}</span></p>
                        </div>
                        
                        <!-- Introduction -->
                        <div style="margin-bottom: 20px;">
                            <p>Dear Sir/Madam,</p>
                            <p>I am directed to inform you that the betterment charges for your primary application with the following particulars:</p>
                        </div>
                        
                        <!-- Property Details -->
                        <div style="margin-bottom: 20px;">
                            <p><strong>File No:</strong> ${currentApplication.fileno}</p>
                            <p><strong>Name of Applicant:</strong> ${currentApplication.owner}</p>
                        </div>
                        
                        <!-- Calculation Table -->
                        <table class="print-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th style="text-align: right;">Amount (₦)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Property Value</td>
                                    <td style="text-align: right;">${propertyValue.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                </tr>
                                <tr>
                                    <td>Betterment Rate</td>
                                    <td style="text-align: right;">${bettermentRate}%</td>
                                </tr>
                                <tr class="total-row">
                                    <td><strong>Total Betterment Charges</strong></td>
                                    <td style="text-align: right;"><strong>${bettermentAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Footer Text -->
                        <div class="print-footer">
                            <p>You are hereby directed to settle this bill promptly in order to accelerate the processing of your application.</p>
                            <p><strong>Note:</strong> Documentary Payments can be made at the Checkout-Point and KANGIS Cashier's Office.</p>
                            <p>Thank you.</p>
                        </div>
                    </div>
                    
                    <!-- Action Buttons (no-print) -->
                    <div class="no-print mt-6 flex gap-2">
                        <button onclick="printBettermentBill()" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                            Print Bill
                        </button>
                    </div>
                </div>
            `;
        }

        // Balance bill calculation
        $(document).on('click', '#calculate-balance-total-btn', function() {
            calculateBalanceTotal();
        });

        // Real-time calculation for balance bill
        $(document).on('input', '#balance-assignment-fee, #balance-bill-balance, #balance-recertification-fee, #balance-dev-charges', function() {
            calculateBalanceTotal();
        });

        function calculateBalanceTotal() {
            var assignmentFee = parseFloat($('#balance-assignment-fee').val()) || 0;
            var billBalance = parseFloat($('#balance-bill-balance').val()) || 0;
            var recertificationFee = parseFloat($('#balance-recertification-fee').val()) || 0;
            var devCharges = parseFloat($('#balance-dev-charges').val()) || 0;
            
            var totalAmount = assignmentFee + billBalance + recertificationFee + devCharges;
            
            $('#balance-calculated-total').text('₦' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2}));
        }

        // Generate balance bill with saving functionality
        $(document).on('click', '#save-balance-bill-btn', function() {
            var assignmentFee = parseFloat($('#balance-assignment-fee').val()) || 0;
            var billBalance = parseFloat($('#balance-bill-balance').val()) || 0;
            var recertificationFee = parseFloat($('#balance-recertification-fee').val()) || 0;
            var devCharges = parseFloat($('#balance-dev-charges').val()) || 0;
            var totalAmount = assignmentFee + billBalance + recertificationFee + devCharges;
            var billDate = $('#balance-bill-date').val() || new Date().toISOString().slice(0,10);
            
            if (totalAmount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Amounts',
                    text: 'Please enter valid amounts for the fees.',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Generating Balance Bill...',
                text: 'Please wait while we generate your balance bill.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Save balance bill data to localStorage
            var billData = {
                application_id: currentApplication.fileId,
                assignment_fee: assignmentFee,
                bill_balance: billBalance,
                recertification_fee: recertificationFee,
                dev_charges: devCharges,
                total_amount: totalAmount,
                bill_date: billDate,
                bill_reference: $('#balance-bill-ref-id').val(),
                generated_date: new Date().toISOString().slice(0,10),
                file_no: currentApplication.fileno,
                owner_name: currentApplication.owner
            };
            
            // Store in localStorage for persistence
            generatedBills.balance = billData;
            localStorage.setItem('balance_bill_' + currentApplication.fileId, JSON.stringify(billData));
            
            // Simulate generation delay
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Balance Bill Generated & Saved!',
                    text: `Balance bill generated successfully! Total Amount: ₦${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}`,
                    confirmButtonColor: '#10b981'
                });
            }, 2000);
        });

        // Preview balance bill with SweetAlert
        $(document).on('click', '#preview-balance-bill-btn', function() {
            var assignmentFee = parseFloat($('#balance-assignment-fee').val()) || 0;
            var billBalance = parseFloat($('#balance-bill-balance').val()) || 0;
            var recertificationFee = parseFloat($('#balance-recertification-fee').val()) || 0;
            var devCharges = parseFloat($('#balance-dev-charges').val()) || 0;
            var totalAmount = assignmentFee + billBalance + recertificationFee + devCharges;
            var billDate = $('#balance-bill-date').val() || new Date().toISOString().slice(0,10);
            
            if (totalAmount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Calculation Found',
                    text: 'Please calculate the bill first.',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Generating Preview...',
                text: 'Please wait while we prepare your bill preview.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Simulate preview generation delay
            setTimeout(() => {
                // Generate preview HTML
                var previewHtml = getBalancePreviewHtml(assignmentFee, billBalance, recertificationFee, devCharges, totalAmount, billDate);
                
                $('#balance-preview-container').html(previewHtml);
                $('.balance-tab-button[data-tab="preview"]').click();
                
                // Reinitialize Lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Preview Ready!',
                    text: 'Your bill preview has been generated successfully.',
                    confirmButtonColor: '#10b981'
                });
            }, 1500);
        });

        // Function to generate balance preview HTML with proper print structure
        function getBalancePreviewHtml(assignmentFee, billBalance, recertificationFee, devCharges, totalAmount, billDate) {
            return `
                <div class="print-area">
                    <!-- Header with logos -->
                    <div class="print-header">
                        <div class="print-logos">
                            <div class="print-logo-left">
                                <img src="/assets/logo/logo1.jpg" alt="Kano State Logo" class="print-logo">
                            </div>
                            <div class="print-title">
                                <h1>KANO STATE MINISTRY OF LAND AND PHYSICAL PLANNING</h1>
                                <h2>SECTIONAL TITLE BILL BALANCE</h2>
                            </div>
                            <div class="print-logo-right">
                                <img src="/assets/logo/logo3.jpeg" alt="Ministry Logo" class="print-logo">
                            </div>
                        </div>
                    </div>
                    
                    <div class="print-content">
                        <!-- Date and Reference -->
                        <div class="print-date-ref">
                            <p><strong>Date:</strong> ${new Date(billDate).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            <p><strong>Bill Reference ID:</strong> <span class="ref-highlight">${$('#balance-bill-ref-id').val()}</span></p>
                        </div>
                        
                        <!-- Introduction -->
                        <div style="margin-bottom: 20px;">
                            <p>Dear Sir/Madam,</p>
                            <p>I am directed to inform you that the total cost of processing of your application for sectional title with the following particulars:</p>
                        </div>
                        
                        <!-- Property Details -->
                        <div style="margin-bottom: 20px;">
                            <p><strong>File No:</strong> ${currentApplication.fileno}</p>
                            <p><strong>Name of Section Owner:</strong> ${currentApplication.owner}</p>
                        </div>
                        
                        <!-- Fee Table -->
                        <table class="print-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th style="text-align: right;">Amount (₦)</th>
                                    <th style="text-align: right;">Dev. Charges (₦)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Assignment Fee</td>
                                    <td style="text-align: right;">${assignmentFee.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td style="text-align: right;">${devCharges.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                </tr>
                                <tr>
                                    <td>Bill Balance</td>
                                    <td style="text-align: right;">${billBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td style="text-align: right;">-</td>
                                </tr>
                                <tr>
                                    <td>Recertification Fee</td>
                                    <td style="text-align: right;">${recertificationFee.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td style="text-align: right;">-</td>
                                </tr>
                                <tr class="total-row">
                                    <td><strong>TOTAL</strong></td>
                                    <td style="text-align: right;"><strong>${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                                    <td style="text-align: right;"><strong>-</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Footer Text -->
                        <div class="print-footer">
                            <p>You are hereby directed to settle this bill promptly in order to accelerate the processing of your application.</p>
                            <p><strong>Note:</strong> Documentary Payments can be made at the Checkout-Point and KANGIS Cashier's Office.</p>
                            <p>Thank you.</p>
                        </div>
                    </div>
                    
                    <!-- Action Buttons (no-print) -->
                    <div class="no-print mt-6 flex gap-2">
                        <button onclick="printBalanceBill()" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                            Print Bill
                        </button>
                    </div>
                </div>
            `;
        }

        // Print functions
        function printBettermentBill() {
            Swal.fire({
                title: 'Preparing Print...',
                text: 'Please wait while we prepare your bill for printing.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.close();
                window.print();
            }, 1000);
        }

        function printBalanceBill() {
            Swal.fire({
                title: 'Preparing Print...',
                text: 'Please wait while we prepare your bill for printing.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.close();
                window.print();
            }, 1000);
        }

        // Currency input formatting functionality
        function initializeCurrencyInputs() {
            // Target all bill amount inputs
            const currencyInputs = [
                '#betterment-property-value',
                '#balance-assignment-fee', 
                '#balance-bill-balance',
                '#balance-recertification-fee',
                '#balance-dev-charges'
            ];

            currencyInputs.forEach(selector => {
                $(document).off('focus input keydown', selector);
                
                // Initialize with 0.00 if empty
                $(document).on('focus', selector, function() {
                    const $input = $(this);
                    let value = $input.val().replace(/[^\d]/g, '');
                    
                    if (!value || value === '0') {
                        $input.val('0.00');
                        $input.data('raw-value', '0');
                    } else {
                        // Store the raw numeric value
                        $input.data('raw-value', value);
                    }
                    
                    // Select all text for easy replacement
                    this.select();
                });

                // Handle input formatting
                $(document).on('input', selector, function(e) {
                    const $input = $(this);
                    let value = $input.val().replace(/[^\d]/g, '');
                    
                    // If empty, set to 0
                    if (!value) {
                        value = '0';
                    }
                    
                    // Store raw value
                    $input.data('raw-value', value);
                    
                    // Format as currency (divide by 100 to get decimal places)
                    const numericValue = parseInt(value) / 100;
                    const formattedValue = numericValue.toFixed(2);
                    
                    $input.val(formattedValue);
                });

                // Handle keydown for special keys
                $(document).on('keydown', selector, function(e) {
                    const $input = $(this);
                    
                    // Allow: backspace, delete, tab, escape, enter
                    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
                        // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                        (e.keyCode === 65 && e.ctrlKey === true) ||
                        (e.keyCode === 67 && e.ctrlKey === true) ||
                        (e.keyCode === 86 && e.ctrlKey === true) ||
                        (e.keyCode === 88 && e.ctrlKey === true)) {
                        return;
                    }
                    
                    // Ensure that it is a number and stop the keypress
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });

                // Handle blur to ensure proper formatting
                $(document).on('blur', selector, function() {
                    const $input = $(this);
                    let value = $input.val().replace(/[^\d.]/g, '');
                    
                    if (!value || value === '0' || value === '0.00') {
                        $input.val('0.00');
                        $input.data('raw-value', '0');
                    } else {
                        // Ensure proper decimal formatting
                        const numericValue = parseFloat(value);
                        if (!isNaN(numericValue)) {
                            $input.val(numericValue.toFixed(2));
                            $input.data('raw-value', (numericValue * 100).toString());
                        }
                    }
                });
            });
        }

        // Initialize currency inputs when document is ready
        $(document).ready(function() {
            initializeCurrencyInputs();
        });

        // Re-initialize currency inputs when new content is loaded
        $(document).on('DOMNodeInserted', function() {
            setTimeout(initializeCurrencyInputs, 100);
        });
    </script>
    <script src="{{ asset('js/betterment-bill-fix.js') }}"></script>
    <script src="{{ asset('js/application-details-fetcher.js') }}"></script>  
     <script src="{{ asset('js/bill-form-populator.js') }}"></script>
 <script src="{{ asset('js/test-routes.js') }}"></script>
@endsection
{{--  
    // Additional function to load saved bills when application is selected
    function loadSavedBillsForApplication() {
        if (typeof checkForSavedBettermentBill === 'function') {
            checkForSavedBettermentBill();
        }
        if (typeof checkForSavedBalanceBill === 'function') {
            checkForSavedBalanceBill();
        }
    } --}}
