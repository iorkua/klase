@extends('layouts.app')

@section('page-title')
    {{$PageTitle}}
@endsection

@include('sectionaltitling.partials.assets.css')

@section('content')
<div class="flex-1 overflow-auto bg-gray-100 min-h-screen">
    <!-- Header -->
    @include('admin.header')
    
    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-8">
            <div class="flex justify-between items-center mb-6 pb-4 border-b">
                <h1 class="text-2xl font-bold text-gray-800">{{ isset($existingSitePlan) ? 'Update' : 'Upload' }} Site Plan</h1>
                <a href="{{ route('stmemo.siteplan') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                    Back to Site Plans
                </a>
            </div>
            
            <div class="mb-8">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="info" class="w-5 h-5 text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Application Information</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>
                                    <span class="font-semibold">File No:</span> 
                                    {{ $isPrimary ? $application->fileno : $application->primary_fileno }} 
                                    {{ !$isPrimary ? ' / ' . $application->fileno : '' }}
                                </p>
                                <p>
                                    <span class="font-semibold">Applicant:</span> 
                                    @if($isPrimary)
                                        @if(!empty($application->corporate_name))
                                            {{ $application->corporate_name }}
                                        @else
                                            {{ $application->applicant_title }} {{ $application->first_name }} {{ $application->surname }}
                                        @endif
                                    @else
                                        @if(!empty($application->corporate_name))
                                            {{ $application->corporate_name }}
                                        @else
                                            {{ $application->applicant_title }} {{ $application->first_name }} {{ $application->surname }}
                                        @endif
                                    @endif
                                </p>
                                <p>
                                    <span class="font-semibold">Location:</span>
                                    {{ $application->property_plot_no }} {{ $application->property_street_name }},
                                    {{ $application->property_lga }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(isset($existingSitePlan))
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Site Plan Already Exists</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>You are updating an existing site plan. The current file will be replaced.</p>
                                <p class="mt-1">
                                    <a href="{{ asset('storage/app/public/' . $existingSitePlan->site_file) }}" target="_blank" class="text-yellow-800 font-medium underline">
                                        <i data-lucide="eye" class="w-4 h-4 inline"></i> View Current Site Plan
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <form action="{{ route('stmemo.saveSitePlan') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="application_id" value="{{ $application->id }}">
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="property_location" class="block text-sm font-medium text-gray-700 mb-1">Property Location</label>
                            <textarea 
                                id="property_location" 
                                name="property_location" 
                                rows="2" 
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ isset($existingSitePlan) ? $existingSitePlan->property_location : ($application->property_plot_no . ' ' . $application->property_street_name . ', ' . $application->property_lga) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Full address of the property</p>
                        </div>
                        
                        <div>
                            <label for="site_file" class="block text-sm font-medium text-gray-700 mb-1">Site Plan File</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="site_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PDF, PNG, JPG up to 10MB
                                    </p>
                                </div>
                            </div>
                            <div id="file-preview" class="mt-2 hidden">
                                <div class="p-2 bg-gray-50 rounded-md flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i data-lucide="file" class="w-4 h-4 text-gray-400 mr-2"></i>
                                        <span id="file-name" class="text-sm text-gray-700"></span>
                                    </div>
                                    <button type="button" id="remove-file" class="text-red-500 hover:text-red-700">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-5 border-t border-gray-200">
                        <a href="{{ route('stmemo.siteplan') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ isset($existingSitePlan) ? 'Update' : 'Upload' }} Site Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    @include('admin.footer')
</div>

 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file-upload');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        const removeFileBtn = document.getElementById('remove-file');
        
        fileInput.addEventListener('change', function(e) {
            if (fileInput.files.length > 0) {
                fileName.textContent = fileInput.files[0].name;
                filePreview.classList.remove('hidden');
            } else {
                filePreview.classList.add('hidden');
            }
        });
        
        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            filePreview.classList.add('hidden');
        });
        
        // File drag and drop support
        const dropZone = document.querySelector('.border-dashed');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropZone.classList.add('bg-blue-50');
            dropZone.classList.add('border-blue-300');
        }
        
        function unhighlight() {
            dropZone.classList.remove('bg-blue-50');
            dropZone.classList.remove('border-blue-300');
        }
        
        dropZone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                fileName.textContent = files[0].name;
                filePreview.classList.remove('hidden');
            }
        }
    });
</script>
@endsection