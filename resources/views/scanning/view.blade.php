@extends('layouts.app')
@section('page-title')
    {{ __('View Scanned Document') }}
@endsection

@section('content')
<div class="flex-1 overflow-auto bg-gray-50">
    <!-- Header -->
    @include('admin.header')
    
    <!-- Dashboard Content -->
    <div class="p-6">
        <div class="container mx-auto py-6 space-y-6">
            <!-- Enhanced Page Header -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @php
                                $fileExtension = pathinfo($scanning->original_filename, PATHINFO_EXTENSION);
                            @endphp
                            @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'tiff']))
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="image" class="h-6 w-6 text-green-600"></i>
                                </div>
                            @elseif(strtolower($fileExtension) === 'pdf')
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="file-text" class="h-6 w-6 text-red-600"></i>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="file" class="h-6 w-6 text-blue-600"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $scanning->original_filename }}</h1>
                            <p class="text-gray-600">{{ $scanning->fileIndexing->file_number }} • {{ $scanning->fileIndexing->file_title }}</p>
                            <div class="flex items-center space-x-2 mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $scanning->status === 'typed' ? 'bg-green-100 text-green-800' : 
                                       ($scanning->status === 'scanned' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($scanning->status) }}
                                </span>
                                @if($scanning->document_type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $scanning->document_type }}
                                    </span>
                                @endif
                                @if($scanning->paper_size)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $scanning->paper_size }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('scanning.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i>
                            Back
                        </a>
                        
                        <a href="{{ Storage::url($scanning->document_path) }}" target="_blank" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <i data-lucide="external-link" class="h-4 w-4 mr-2"></i>
                            Open in New Tab
                        </a>

                        <div class="relative">
                            <button onclick="toggleActionsMenu()"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                            </button>
                            <div id="actions-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-10 border border-gray-200 overflow-hidden">
                                <div class="py-1">
                                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150 flex items-center" onclick="editDocument()">
                                        <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                                        Edit Details
                                    </button>
                                    <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150 flex items-center" onclick="deleteDocument()">
                                        <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                                        Delete Document
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Document Viewer - Takes up 2/3 of the space -->
                <div class="xl:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900">Document Preview</h2>
                                <div class="flex items-center space-x-2">
                                    <button class="btn btn-outline btn-sm" onclick="zoomOut()">
                                        <i data-lucide="zoom-out" class="h-4 w-4"></i>
                                    </button>
                                    <span id="zoom-level" class="text-sm text-gray-600">100%</span>
                                    <button class="btn btn-outline btn-sm" onclick="zoomIn()">
                                        <i data-lucide="zoom-in" class="h-4 w-4"></i>
                                    </button>
                                    <button class="btn btn-outline btn-sm" onclick="resetZoom()">
                                        <i data-lucide="maximize" class="h-4 w-4"></i>
                                    </button>
                                    <button class="btn btn-outline btn-sm" onclick="toggleFullscreen()">
                                        <i data-lucide="expand" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div id="document-container" class="border rounded-lg overflow-auto bg-gray-100 min-h-[600px] max-h-[800px] relative">
                                @php
                                    $fileUrl = Storage::url($scanning->document_path);
                                @endphp
                                
                                @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'tiff']))
                                    <!-- Enhanced Image viewer -->
                                    <div class="flex items-center justify-center min-h-[600px] p-4">
                                        <img id="document-image" src="{{ $fileUrl }}" alt="Document" 
                                             class="max-w-full max-h-full object-contain transition-transform duration-200 cursor-zoom-in"
                                             onclick="toggleImageZoom(this)">
                                    </div>
                                @elseif(strtolower($fileExtension) === 'pdf')
                                    <!-- Enhanced PDF viewer -->
                                    <iframe id="document-pdf" src="{{ $fileUrl }}" class="w-full h-[600px] border-0 rounded"></iframe>
                                @else
                                    <!-- Unsupported file type with better styling -->
                                    <div class="flex flex-col items-center justify-center min-h-[600px] text-gray-500">
                                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-6">
                                            <i data-lucide="file-text" class="h-12 w-12 text-gray-400"></i>
                                        </div>
                                        <h3 class="text-xl font-semibold mb-2 text-gray-700">{{ $scanning->original_filename }}</h3>
                                        <p class="text-gray-500 mb-6 text-center max-w-md">
                                            Preview is not available for this file type. You can download the file to view it.
                                        </p>
                                        <div class="flex space-x-3">
                                            <a href="{{ $fileUrl }}" download class="btn btn-primary">
                                                <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                                                Download File
                                            </a>
                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline">
                                                <i data-lucide="external-link" class="h-4 w-4 mr-2"></i>
                                                Open in Browser
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Information Sidebar - Takes up 1/3 of the space -->
                <div class="xl:col-span-1 space-y-6">
                    <!-- File Information Card -->
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                            <h3 class="font-semibold text-gray-900">File Information</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600">File Number</span>
                                    <span class="text-sm font-medium text-gray-900 text-right">{{ $scanning->fileIndexing->file_number }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600">File Title</span>
                                    <span class="text-sm font-medium text-gray-900 text-right max-w-[60%]">{{ $scanning->fileIndexing->file_title }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600">Original Name</span>
                                    <span class="text-sm font-medium text-gray-900 text-right max-w-[60%] break-words">{{ $scanning->original_filename }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">File Size</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        @if(Storage::exists('public/' . $scanning->document_path))
                                            {{ number_format(Storage::size('public/' . $scanning->document_path) / 1024, 1) }} KB
                                        @else
                                            Unknown
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">File Type</span>
                                    <span class="text-sm font-medium text-gray-900 uppercase">{{ $fileExtension }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Properties Card -->
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                            <h3 class="font-semibold text-gray-900">Document Properties</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Document Type</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $scanning->document_type ?? 'Unknown' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Paper Size</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $scanning->paper_size ?? 'Unknown' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Status</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $scanning->status === 'typed' ? 'bg-green-100 text-green-800' : 
                                           ($scanning->status === 'scanned' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($scanning->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600">Uploaded</span>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $scanning->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $scanning->created_at->format('H:i') }}</div>
                                    </div>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600">Uploaded By</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $scanning->uploader->name ?? 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Card -->
                    @if($scanning->notes)
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                            <h3 class="font-semibold text-gray-900">Notes</h3>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-700">{{ $scanning->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                            <h3 class="font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-4 space-y-3">
                            @if($scanning->status !== 'typed')
                                <a href="{{ route('pagetyping.index', ['file_indexing_id' => $scanning->file_indexing_id]) }}" 
                                   class="w-full btn btn-primary">
                                    <i data-lucide="type" class="h-4 w-4 mr-2"></i>
                                    Start Page Typing
                                </a>
                            @endif
                            <button class="w-full btn btn-outline" onclick="editDocument()">
                                <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                                Edit Document Details
                            </button>
                            <button class="w-full btn btn-outline" onclick="shareDocument()">
                                <i data-lucide="share-2" class="h-4 w-4 mr-2"></i>
                                Share Document
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('admin.footer')
</div>

<!-- Edit Document Modal -->
<div id="edit-modal" class="dialog-backdrop hidden" aria-hidden="true">
    <div class="dialog-content animate-fade-in">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Edit Document Details</h2>
        </div>
        <form id="edit-form" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Document Type</label>
                <select id="edit-document-type" class="input">
                    <option value="Certificate" {{ $scanning->document_type === 'Certificate' ? 'selected' : '' }}>Certificate</option>
                    <option value="Deed" {{ $scanning->document_type === 'Deed' ? 'selected' : '' }}>Deed</option>
                    <option value="Letter" {{ $scanning->document_type === 'Letter' ? 'selected' : '' }}>Letter</option>
                    <option value="Application Form" {{ $scanning->document_type === 'Application Form' ? 'selected' : '' }}>Application Form</option>
                    <option value="Map" {{ $scanning->document_type === 'Map' ? 'selected' : '' }}>Map</option>
                    <option value="Survey Plan" {{ $scanning->document_type === 'Survey Plan' ? 'selected' : '' }}>Survey Plan</option>
                    <option value="Receipt" {{ $scanning->document_type === 'Receipt' ? 'selected' : '' }}>Receipt</option>
                    <option value="Other" {{ $scanning->document_type === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Paper Size</label>
                <select id="edit-paper-size" class="input">
                    <option value="A4" {{ $scanning->paper_size === 'A4' ? 'selected' : '' }}>A4</option>
                    <option value="A3" {{ $scanning->paper_size === 'A3' ? 'selected' : '' }}>A3</option>
                    <option value="A5" {{ $scanning->paper_size === 'A5' ? 'selected' : '' }}>A5</option>
                    <option value="Letter" {{ $scanning->paper_size === 'Letter' ? 'selected' : '' }}>Letter</option>
                    <option value="Legal" {{ $scanning->paper_size === 'Legal' ? 'selected' : '' }}>Legal</option>
                    <option value="Custom" {{ $scanning->paper_size === 'Custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Notes</label>
                <textarea id="edit-notes" class="input" rows="3" placeholder="Add any notes about this document...">{{ $scanning->notes }}</textarea>
            </div>
        </form>
        <div class="flex justify-end gap-2 p-4 border-t">
            <button class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
            <button class="btn btn-primary" onclick="saveDocumentDetails()">Save Changes</button>
        </div>
    </div>
</div>

<style>
.zoom-in { transform: scale(1.5); }
.zoom-out { transform: scale(0.75); }
.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 9999;
    background: white;
}
</style>

<script>
let currentZoom = 100;
let isFullscreen = false;

function toggleActionsMenu() {
    const menu = document.getElementById('actions-menu');
    menu.classList.toggle('hidden');
}

function zoomIn() {
    currentZoom = Math.min(currentZoom + 25, 200);
    updateZoom();
}

function zoomOut() {
    currentZoom = Math.max(currentZoom - 25, 50);
    updateZoom();
}

function resetZoom() {
    currentZoom = 100;
    updateZoom();
}

function updateZoom() {
    const image = document.getElementById('document-image');
    const pdf = document.getElementById('document-pdf');
    const zoomLevel = document.getElementById('zoom-level');
    
    if (image) {
        image.style.transform = `scale(${currentZoom / 100})`;
    }
    if (pdf) {
        pdf.style.transform = `scale(${currentZoom / 100})`;
        pdf.style.transformOrigin = 'top left';
    }
    
    zoomLevel.textContent = currentZoom + '%';
}

function toggleImageZoom(img) {
    if (img.classList.contains('zoom-in')) {
        img.classList.remove('zoom-in');
        img.classList.add('cursor-zoom-in');
    } else {
        img.classList.add('zoom-in');
        img.classList.remove('cursor-zoom-in');
        img.classList.add('cursor-zoom-out');
    }
}

function toggleFullscreen() {
    const container = document.getElementById('document-container');
    if (!isFullscreen) {
        container.classList.add('fullscreen');
        container.style.maxHeight = '100vh';
        isFullscreen = true;
    } else {
        container.classList.remove('fullscreen');
        container.style.maxHeight = '800px';
        isFullscreen = false;
    }
}

function downloadDocument() {
    const link = document.createElement('a');
    link.href = '{{ Storage::url($scanning->document_path) }}';
    link.download = '{{ $scanning->original_filename }}';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function editDocument() {
    document.getElementById('edit-modal').classList.remove('hidden');
    toggleActionsMenu();
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

function saveDocumentDetails() {
    const formData = {
        document_type: document.getElementById('edit-document-type').value,
        paper_size: document.getElementById('edit-paper-size').value,
        notes: document.getElementById('edit-notes').value
    };
    
    fetch(`{{ route('scanning.update-details', $scanning->id) }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Document details updated successfully!');
            location.reload();
        } else {
            alert(data.message || 'Error updating document details');
        }
    })
    .catch(error => {
        console.error('Error updating document details:', error);
        alert('Error updating document details');
    });
}

function shareDocument() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $scanning->original_filename }}',
            text: 'Scanned document from {{ $scanning->fileIndexing->file_number }}',
            url: '{{ Storage::url($scanning->document_path) }}'
        });
    } else {
        // Fallback: copy link to clipboard
        navigator.clipboard.writeText('{{ Storage::url($scanning->document_path) }}').then(() => {
            alert('Document link copied to clipboard!');
        });
    }
}

function deleteDocument() {
    if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
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

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('actions-menu');
    const button = event.target.closest('button');
    
    if (!button || !button.onclick || button.onclick.toString().indexOf('toggleActionsMenu') === -1) {
        menu.classList.add('hidden');
    }
});

// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(event) {
    if (event.ctrlKey || event.metaKey) {
        switch(event.key) {
            case '=':
            case '+':
                event.preventDefault();
                zoomIn();
                break;
            case '-':
                event.preventDefault();
                zoomOut();
                break;
            case '0':
                event.preventDefault();
                resetZoom();
                break;
        }
    }
    
    if (event.key === 'Escape' && isFullscreen) {
        toggleFullscreen();
    }
});
</script>
@endsection