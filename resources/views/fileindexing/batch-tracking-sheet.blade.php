@extends('layouts.app')

@section('page-title')
    {{ $PageTitle ?? 'Batch Tracking Sheets' }}
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/print-tracking-sheet.css') }}">
<style>
@media screen {
    .tracking-sheet { 
        margin-bottom: 2rem; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
}
</style>

<div class="flex-1 overflow-auto">
    <!-- Header -->
    <div class="no-print">
        @include('admin.header')
    </div>
    
    <!-- Main Content -->
    <div class="p-6 no-print">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Batch Tracking Sheets</h1>
                        <p class="text-gray-600">{{ $PageDescription ?? 'Generate tracking sheets for multiple file indexing records' }}</p>
                        <p class="text-sm text-gray-500 mt-1">Total Files: {{ $fileIndexings->count() }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="window.print()" class="btn btn-primary">
                            <i data-lucide="printer" class="h-4 w-4 mr-2"></i>
                            Print All Sheets
                        </button>
                        <a href="{{ route('fileindexing.index') }}" class="btn btn-secondary">
                            <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i>
                            Back to Index
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Tracking Sheets -->
    <div id="batch-tracking-sheets">
        @foreach($fileIndexings as $index => $fileIndexing)
        @php $tracker = $trackersData[$fileIndexing->id] @endphp
        <div class="bg-white font-sans text-xs tracking-sheet">
            <div class="max-w-full mx-auto bg-white border border-black">
                <!-- Header with tracking ID -->
                <div class="p-2 border-b border-black">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h1 class="text-sm font-bold">KANO STATE LAND REGISTRY</h1>
                            <h2 class="text-xs">FILE TRACKING SHEET</h2>
                        </div>
                        <div class="text-right text-xs">
                            <p class="font-bold">Tracking ID: {{ $tracker->tracking_id }}</p>
                            <p>Generated: {{ $tracker->sheet_generated_at->format('n/j/Y, g:i:s A') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Sheet {{ $index + 1 }} of {{ $fileIndexings->count() }}</p>
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
                                    
                                    <!-- Status buttons with updated status system -->
                                    <div class="flex gap-2 mb-3">
                                        @php
                                            $status = $tracker->status ?? 'Active';
                                            $statusClass = match($status) {
                                                'Active' => 'bg-green-600 text-white',
                                                'Normal' => 'bg-blue-600 text-white', 
                                                'Scanned' => 'bg-purple-600 text-white',
                                                'Typed' => 'bg-indigo-600 text-white',
                                                'Pending' => 'bg-yellow-600 text-white',
                                                'Completed' => 'bg-green-700 text-white',
                                                'In Progress' => 'bg-blue-500 text-white',
                                                default => 'bg-gray-600 text-white'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">{{ $status }}</span>
                                        
                                        @if($tracker->priority ?? false)
                                        @php
                                            $priority = $tracker->priority;
                                            $priorityClass = match($priority) {
                                                'High' => 'bg-red-600 text-white',
                                                'Medium' => 'bg-orange-600 text-white', 
                                                'Low' => 'bg-green-600 text-white',
                                                default => 'bg-gray-600 text-white'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded {{ $priorityClass }}">{{ $priority }}</span>
                                        @endif
                                        
                                        @if($fileIndexing->scannings->count() > 0)
                                        <span class="bg-green-600 text-white px-2 py-1 text-xs rounded">Scanned</span>
                                        @endif
                                        @if($fileIndexing->pagetypings->count() > 0)
                                        <span class="bg-purple-600 text-white px-2 py-1 text-xs rounded">Typed</span>
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

                        <!-- QR Code section with real QR code -->
                        <div class="col-span-4">
                            <h3 class="text-xs font-bold mb-1">QR Code</h3>
                            <div class="border border-gray-400 p-2 text-center">
                                @php
                                    $qrData = json_encode([
                                        'tracking_id' => $tracker->tracking_id,
                                        'file_number' => $fileIndexing->file_number,
                                        'file_title' => $fileIndexing->file_title,
                                        'plot_number' => $fileIndexing->plot_number,
                                        'district' => $fileIndexing->district,
                                        'status' => $status,
                                        'url' => route('fileindexing.show', $fileIndexing->id)
                                    ]);
                                    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' . urlencode($qrData);
                                @endphp
                                <div class="w-20 h-20 mx-auto mb-2 border bg-gray-100 flex items-center justify-center">
                                    <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-full h-full object-contain" />
                                </div>
                                <p class="text-xs">Contains file details</p>
                                <p class="text-xs font-semibold">{{ $fileIndexing->file_number }}</p>
                                <p class="text-xs">📱 {{ $tracker->tracking_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Location section -->
                    <div class="mb-4">
                        <h3 class="text-xs font-bold mb-1">Current Location</h3>
                        <div class="grid grid-cols-4 gap-4 text-xs">
                            <div>
                                <p class="font-semibold">{{ $tracker->current_location ?? 'File Indexing System' }}</p>
                                <p>Last updated: {{ ($tracker->last_location_update ?? $fileIndexing->created_at)->format('Y-m-d') }}</p>
                            </div>
                            <div>
                                <p class="font-semibold">{{ $tracker->current_handler ?? 'System User' }}</p>
                                <p>Current handler</p>
                            </div>
                            <div>
                                <p class="font-semibold">{{ ($tracker->last_location_update ?? $fileIndexing->created_at)->format('Y-m-d g:i A') }}</p>
                                <p>Last location update</p>
                            </div>
                            <div>
                                @if(($tracker->total_prints ?? 0) > 0)
                                <p class="text-xs">Prints: {{ $tracker->total_prints }}</p>
                                @endif
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
                                @if(isset($tracker->movement_history) && count($tracker->movement_history) > 0)
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
                                @for ($i = 1; $i <= 6; $i++)
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
        @endforeach
    </div>
</div>
@endsection