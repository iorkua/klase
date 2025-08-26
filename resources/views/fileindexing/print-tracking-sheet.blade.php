@extends('layouts.print')

@section('page-title')
    {{ $PageTitle ?? 'Print Tracking Sheet' }}
@endsection
 
@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-xl rounded-lg p-8">
    <!-- Header Section -->
    <div class="header-section flex items-center justify-between mb-10 pb-6 border-b-2 border-green-600">
        <div class="flex-1">
            <h1 class="print-header text-2xl font-bold text-gray-800 mb-2 tracking-wide">KANO STATE LAND REGISTRY</h1>
            <p class="print-subheader text-lg text-green-700 font-semibold">FILE TRACKING SHEET</p>
        </div>
        
        <div class="flex-shrink-0 mx-8">
            <div class="govt-seal-container w-20 h-20 border-3 border-green-600 rounded-full flex items-center justify-center bg-white shadow-lg">
                <div class="government-seal w-16 h-16 rounded-full flex items-center justify-center text-white shadow-inner">
                    <div class="text-center">
                        <div class="text-xs font-bold">KANO</div>
                        <div class="text-xs">STATE</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex-1 text-right">
            <div class="tracking-section bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-800 mb-2">
                    <strong>Tracking ID:</strong> 
                    <span class="form-input ml-2">{{ $tracker->tracking_id }}</span>
                </p>
                <p class="text-xs text-gray-600">
                    Generated: <span class="form-input ml-1" style="min-width: 150px;">{{ $tracker->sheet_generated_at->format('n/j/Y, g:i:s A') }}</span>
                </p>
                @if($tracker->total_prints > 0)
                <p class="text-xs text-gray-500 mt-1 font-medium">Prints: {{ $tracker->total_prints }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- File Details Section -->
    <div class="file-details-section mb-10">
        <div class="flex items-start justify-between gap-8">
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-800 mb-6 text-green-700">File Details</h2>
                
                <!-- File Information Table -->
                <table class="file-details-table w-full border-2 border-gray-800 shadow-md rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-green-600 text-white">
                            <th class="border border-gray-800 px-4 py-3 text-left font-bold">File Information</th>
                            <th class="border border-gray-800 px-4 py-3 text-left font-bold">Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-400 px-4 py-4 font-medium">File Number</td>
                            <td class="border border-gray-400 px-4 py-4">{{ $fileIndexing->file_number }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-400 px-4 py-4 font-medium">File Title</td>
                            <td class="border border-gray-400 px-4 py-4">{{ $fileIndexing->file_title }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-400 px-4 py-4 font-medium">Plot Number</td>
                            <td class="border border-gray-400 px-4 py-4">{{ $fileIndexing->plot_number ?? 'N/A' }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-400 px-4 py-4 font-medium">Land Use</td>
                            <td class="border border-gray-400 px-4 py-4">{{ $fileIndexing->land_use_type ?? 'N/A' }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-400 px-4 py-4 font-medium">District</td>
                            <td class="border border-gray-400 px-4 py-4">{{ $fileIndexing->district ?? 'N/A' }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-400 px-4 py-4 font-medium">Date Created</td>
                            <td class="border border-gray-400 px-4 py-4">{{ $fileIndexing->created_at->format('Y-m-d') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- QR Code Section -->
            <div class="flex-shrink-0">
                <div class="qr-section bg-gray-50 p-6 rounded-lg border border-gray-200 text-center">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">QR Code</h3>
                    <div class="qr-code w-32 h-32 border-2 border-gray-800 bg-white flex items-center justify-center rounded-lg shadow-md">
                        @php
                            $qrData = json_encode([
                                'tracking_id' => $tracker->tracking_id,
                                'file_number' => $fileIndexing->file_number,
                                'file_title' => $fileIndexing->file_title,
                                'plot_number' => $fileIndexing->plot_number,
                                'district' => $fileIndexing->district,
                                'status' => 'Active',
                                'url' => route('fileindexing.show', $fileIndexing->id)
                            ]);
                            $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($qrData);
                        @endphp
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-28 h-28 rounded" />
                    </div>
                    <p class="text-xs mt-2">{{ $fileIndexing->file_number }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Signing Section -->
    <div class="signing-section mb-10">
        <h2 class="text-center text-xl font-bold text-green-700 mb-6 uppercase tracking-wide">Signing Section</h2>
        
        <table class="signing-table w-full border-2 border-gray-800 shadow-md rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="border border-gray-800 px-4 py-3 font-bold">Indexed By:</th>
                    <th class="border border-gray-800 px-4 py-3 font-bold">Scanned By:</th>
                    <th class="border border-gray-800 px-4 py-3 font-bold">Page Typed By:</th>
                    <th class="border border-gray-800 px-4 py-3 font-bold">Supervised By:</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <tr class="signing-row">
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                </tr>
                <tr class="signing-row">
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                </tr>
                <tr class="signing-row">
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                    <td class="border border-gray-400 px-4 py-12 align-top"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Authorization Section -->
    <div class="mb-8">
        <div class="auth-section bg-gray-50 p-6 rounded-lg border border-gray-200">
            <p class="text-base text-gray-800 flex items-center gap-4">
                <strong>Authorized Signature:</strong>
                <span class="form-input flex-1"></span>
                <strong>Date:</strong>
                <span class="form-input" style="min-width: 150px;"></span>
            </p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-section text-center mt-12 pt-6 border-t-2 border-green-600">
        <p class="text-lg font-bold text-green-700 mb-2">KANO STATE LAND REGISTRY</p>
        <p class="text-sm text-gray-600 italic">File Tracking System</p>
    </div>
</div>
@endsection