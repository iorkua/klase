@extends('layouts.app')
@section('page-title')
    {{ __('File Tracker') }}
@endsection

@section('content')
    @include('filetracker.assets.style')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">
            <div class="flex flex-col min-h-screen">
                <!-- Page Header -->
                <header class="bg-white shadow-sm px-6 py-4 border-b">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold">File Tracker</h1>
                            <p class="text-sm text-gray-500">Track and manage the movement of physical files using RFID technology</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="relative w-64">
                                <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="search" placeholder="Search files..." class="w-full pl-8 border rounded-md px-3 py-2 text-sm" id="search-input">
                            </div>
                            <div class="flex items-center gap-2 mr-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" id="rfid-mode">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-2 text-sm font-medium text-gray-900 cursor-pointer">RFID Mode</span>
                                </label>
                            </div>
                            <button id="scan-rfid-btn" class="hidden bg-blue-600 text-white px-4 py-2 rounded-md text-sm flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Scan RFID Tags
                            </button>
                            <a href="{{ route('filetracker.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm flex items-center hover:bg-blue-700">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Track New File
                            </a>
                        </div>
                    </div>
                </header>

                <div class="flex flex-1 p-6 space-x-6">
                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 rounded-md p-4 shadow-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Main Content (2/3 width) -->
                    <div class="w-2/3 space-y-6">
                        @include('filetracker.partials.file-table')
                    </div>
                    
                    <!-- Sidebar (1/3 width) -->
                    <div class="w-1/3 space-y-6">
                        @include('filetracker.partials.file-details')
                        @include('filetracker.partials.rfid-management')
                        @include('filetracker.partials.quick-actions')
                    </div>
                </div>
            </div>

            <!-- RFID Scan Results Modal -->
            @include('filetracker.partials.rfid-modal')
        </div>
        
        <!-- Footer -->
        @include('admin.footer')
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    
    <!-- Scripts --> 
    @include('filetracker.assets.js')
@endsection
