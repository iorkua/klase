@include('admin.head')

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        @include('admin.header')
        
        <!-- Main Content -->
        <div class="flex-1">
            <div class="container-fluid py-6">
                <!-- Breadcrumb Navigation -->
                <div class="flex items-center mb-6 text-sm text-gray-500">
                    <a href="{{ route('fileindexing.index') }}" class="text-blue-600 hover:text-blue-800">File Indexing</a>
                    <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                    <span>File Tracking Sheet</span>
                </div>

                <!-- Page Header -->
                <div class="card mb-6">
                    <div class="p-6 bg-gray-900 text-white" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%);">
                        <div class="flex justify-between items-center">
                            <div>
                                <h1 class="text-2xl font-bold mb-2 flex items-center">
                                    <i data-lucide="file-text" class="h-6 w-6 mr-3"></i>
                                    File Tracking Sheet
                                </h1>
                                <p class="text-gray-300">{{ $PageDescription ?? 'Generate tracking sheet for file indexing record' }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="window.print()" class="btn btn-primary">
                                    <i data-lucide="printer" class="h-4 w-4 mr-2"></i>
                                    Print Sheet
                                </button>
                                <a href="{{ route('fileindexing.index') }}" class="btn">
                                    <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i>
                                    Back to Index
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tracking Sheet Content -->
                <div class="card" id="tracking-sheet">
                    <div class="max-w-full mx-auto bg-white border border-black">
                        <!-- Header with tracking ID -->
                        <div class="p-2 border-b border-black">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h1 class="text-sm font-bold">KANO LAND STATE REGISTRY</h1>
                                    <h2 class="text-xs">FILE TRACKING SHEET</h2>
                                </div>
                                <div class="text-right text-xs">
                                    <p class="font-bold">Tracking ID: {{ $tracker->tracking_id }}</p>
                                    <p>Generated: {{ $tracker->sheet_generated_at->format('n/j/Y, g:i:s A') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-3">
                            <!-- File Icon and Details -->
                            <div class="grid grid-cols-12 gap-4 mb-4">
                                <!-- File Icon and Details -->
                                <div class="col-span-8">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-10 bg-gray-200 border border-gray-400 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-bold mb-1">File Details</h3>
                                            <p class="text-xs font-semibold mb-2">{{ $fileIndexing->file_title }}</p>
                                            
                                            <!-- Status buttons -->
                                            <div class="flex gap-2 mb-3">
                                                <span class="badge badge-blue">Status: Indexed</span>
                                                @if($fileIndexing->scannings->count() > 0)
                                                <span class="badge badge-green">Scanned</span>
                                                @endif
                                                @if($fileIndexing->pagetypings->count() > 0)
                                                <span class="badge badge-purple">Typed</span>
                                                @endif
                                            </div>

                                            <h4 class="text-xs font-bold mb-1">File Information</h4>
                                            <div class="grid grid-cols-2 gap-x-8 text-xs">
                                                <div>
                                                    <p><span class="font-semibold">File Number:</span></p>
                                                    <p><span class="font-semibold">Plot Number:</span></p>
                                                    <p><span class="font-semibold">Land Use:</span></p>
                                                    <p><span class="font-semibold">District:</span></p>
                                                    <p><span class="font-semibold">Date Created:</span></p>
                                                </div>
                                                <div>
                                                    <p>{{ $fileIndexing->file_number }}</p>
                                                    <p>{{ $fileIndexing->plot_number ?? 'N/A' }}</p>
                                                    <p>{{ $fileIndexing->land_use_type ?? 'N/A' }}</p>
                                                    <p>{{ $fileIndexing->district ?? 'N/A' }}</p>
                                                    <p>{{ $fileIndexing->created_at->format('Y-m-d') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- QR Code section -->
                                <div class="col-span-4">
                                    <h3 class="text-xs font-bold mb-1">QR Code</h3>
                                    <div class="border border-gray-400 p-2 text-center">
                                        <div class="w-20 h-20 mx-auto mb-2 border bg-gray-100 flex items-center justify-center">
                                            <span class="text-xs">QR Code</span>
                                        </div>
                                        <p class="text-xs">Contains file details</p>
                                        <p class="text-xs font-semibold">{{ $fileIndexing->file_number }}</p>
                                        <p class="text-xs">📱 File ID: {{ $fileIndexing->id }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Location section -->
                            <div class="mb-4">
                                <h3 class="text-xs font-bold mb-1">Current Location</h3>
                                <div class="grid grid-cols-4 gap-4 text-xs">
                                    <div>
                                        <p class="font-semibold">{{ $tracker->current_location }}</p>
                                        <p>Last updated: {{ $tracker->last_location_update->format('Y-m-d') }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $tracker->current_handler }}</p>
                                        <p>Current handler</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $tracker->last_location_update->format('Y-m-d g:i A') }}</p>
                                        <p>Last location update</p>
                                    </div>
                                    <div>
                                        <span class="badge badge-outline text-xs">{{ $tracker->status }}</span>
                                        <span class="badge badge-outline text-xs">{{ $tracker->priority }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Movement History -->
                            <div class="mb-4">
                                <h3 class="text-xs font-bold mb-2">Movement History</h3>
                                <table class="w-full border-collapse border border-black text-xs">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-black p-1 text-left font-bold">Date & Time</th>
                                            <th class="border border-black p-1 text-left font-bold">Location</th>
                                            <th class="border border-black p-1 text-left font-bold">Handler</th>
                                            <th class="border border-black p-1 text-left font-bold">Action</th>
                                            <th class="border border-black p-1 text-left font-bold">Method</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($tracker->movement_history && count($tracker->movement_history) > 0)
                                            @foreach($tracker->movement_history as $movement)
                                            <tr>
                                                <td class="border border-black p-1">{{ $movement['date'] }} {{ $movement['time'] }}</td>
                                                <td class="border border-black p-1">{{ $movement['location'] }}</td>
                                                <td class="border border-black p-1">{{ $movement['handler'] }}</td>
                                                <td class="border border-black p-1">{{ $movement['action'] }}</td>
                                                <td class="border border-black p-1">{{ $movement['method'] }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="border border-black p-1">{{ $fileIndexing->created_at->format('Y-m-d g:i A') }}</td>
                                                <td class="border border-black p-1">File Indexing System</td>
                                                <td class="border border-black p-1">System User</td>
                                                <td class="border border-black p-1">File indexed and registered</td>
                                                <td class="border border-black p-1">Digital</td>
                                            </tr>
                                        @endif
                                        <!-- Empty rows for manual tracking -->
                                        @for ($i = 1; $i <= 8; $i++)
                                        <tr>
                                            <td class="border border-black p-1 h-6"></td>
                                            <td class="border border-black p-1"></td>
                                            <td class="border border-black p-1"></td>
                                            <td class="border border-black p-1"></td>
                                            <td class="border border-black p-1"></td>
                                        </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>

                            <!-- Signature section -->
                            <div class="grid grid-cols-2 gap-8 mb-4">
                                <div>
                                    <h3 class="text-xs font-bold mb-2">Signature</h3>
                                    <div class="h-16 border-b border-black mb-1"></div>
                                    <p class="text-xs">Authorized Signature</p>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold mb-2">Notes</h3>
                                    <div class="h-16 mb-1"></div>
                                    <p class="text-xs text-center">File processing status updates</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <div class="border-b border-black mb-1"></div>
                                    <p class="text-xs">Date:</p>
                                </div>
                                <div></div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-black p-2 text-xs">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-bold">KANO STATE LAND REGISTRY</p>
                                    <p>File Tracking System</p>
                                </div>
                                <div class="text-right">
                                    <p>This tracking sheet should accompany the file at all times.</p>
                                    <p>For inquiries, contact File Management Office at ext.2145.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        @include('admin.footer')
    </div>

    <!-- Scripts and Styles -->
    @include('fileindexing.css.style')
    @include('fileindexing.js.javascript')

    <style>
    @media print {
        body * {
            visibility: hidden;
        }
        #tracking-sheet, #tracking-sheet * {
            visibility: visible;
        }
        #tracking-sheet {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn {
            display: none !important;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
    </script>
</body>
</html>