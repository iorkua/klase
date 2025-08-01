@extends('layouts.app')
@section('page-title')
    {{ __('View Scanned Document') }}
@endsection

@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include('admin.header')
    
    <!-- Dashboard Content -->
    <div class="p-6">
        <div class="container mx-auto py-6 space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">View Scanned Document</h1>
                        <p class="text-muted-foreground">{{ $scanning->original_filename }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('scanning.index') }}" class="btn btn-outline">
                            <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i>
                            Back to Scanning
                        </a>
                        <a href="{{ Storage::url($scanning->document_path) }}" target="_blank" class="btn btn-primary">
                            <i data-lucide="external-link" class="h-4 w-4 mr-2"></i>
                            Open in New Tab
                        </a>
                    </div>
                </div>
            </div>

            <!-- Document Info Card -->
            <div class="card">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">Document Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium mb-4">File Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">File Number:</span>
                                    <span class="font-medium">{{ $scanning->fileIndexing->file_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">File Title:</span>
                                    <span class="font-medium">{{ $scanning->fileIndexing->file_title }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Original Filename:</span>
                                    <span class="font-medium">{{ $scanning->original_filename }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Document Type:</span>
                                    <span class="badge bg-blue-500 text-white">{{ $scanning->document_type ?? 'Unknown' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Paper Size:</span>
                                    <span class="font-medium">{{ $scanning->paper_size ?? 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-medium mb-4">Upload Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Uploaded:</span>
                                    <span class="font-medium">{{ $scanning->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="badge {{ $scanning->status === 'typed' ? 'bg-green-500' : ($scanning->status === 'scanned' ? 'bg-blue-500' : 'bg-yellow-500') }} text-white">
                                        {{ ucfirst($scanning->status) }}
                                    </span>
                                </div>
                                @if($scanning->notes)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Notes:</span>
                                    <span class="font-medium">{{ $scanning->notes }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Viewer -->
            <div class="card">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">Document Preview</h2>
                </div>
                <div class="p-6">
                    <div class="border rounded-lg overflow-hidden bg-gray-50">
                        @php
                            $fileExtension = pathinfo($scanning->original_filename, PATHINFO_EXTENSION);
                            $fileUrl = Storage::url($scanning->document_path);
                        @endphp
                        
                        @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'tiff']))
                            <!-- Image viewer -->
                            <div class="flex items-center justify-center min-h-[500px]">
                                <img src="{{ $fileUrl }}" alt="Document" class="max-w-full max-h-[600px] object-contain">
                            </div>
                        @elseif(strtolower($fileExtension) === 'pdf')
                            <!-- PDF viewer -->
                            <iframe src="{{ $fileUrl }}" class="w-full h-[600px] border-0"></iframe>
                        @else
                            <!-- Unsupported file type -->
                            <div class="flex flex-col items-center justify-center min-h-[300px] text-gray-500">
                                <i data-lucide="file-text" class="h-16 w-16 mb-4"></i>
                                <p class="text-lg font-medium mb-2">{{ $scanning->original_filename }}</p>
                                <p class="text-sm mb-4">Preview not available for this file type</p>
                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary">
                                    <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                                    Download File
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <button class="btn btn-outline" onclick="editDocument()">
                        <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                        Edit Details
                    </button>
                    <button class="btn btn-destructive" onclick="deleteDocument()">
                        <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                        Delete Document
                    </button>
                </div>
                
                @if($scanning->status !== 'typed')
                <a href="{{ route('pagetyping.index', ['file_indexing_id' => $scanning->file_indexing_id]) }}" class="btn btn-primary">
                    <i data-lucide="type" class="h-4 w-4 mr-2"></i>
                    Start Page Typing
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('admin.footer')
</div>

<script>
function editDocument() {
    alert('Edit document functionality - to be implemented');
}

function deleteDocument() {
    if (!confirm('Are you sure you want to delete this document?')) {
        return;
    }
    
    fetch(`{{ route('scanning.delete', $scanning->id) }}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("scanning.index") }}';
        } else {
            alert(data.message || 'Error deleting document');
        }
    })
    .catch(error => {
        console.error('Error deleting document:', error);
        alert('Error deleting document');
    });
}

// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection