@extends('layouts.app')
@section('page-title')
    {{ $PageTitle }}
@endsection
@section('content')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header --> 
        @include('admin.header') 
        <!-- Dashboard Content -->
        <div class="p-6">
            @include('scanning.assets.style')
            
            <style>
                /* Additional styles for view page */
                .scan-card {
                    transition: all 0.2s ease;
                }

                .scan-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                }

                .tab.active {
                    border-bottom: 2px solid #3b82f6;
                    color: #3b82f6;
                }

                .tab-content.active {
                    display: block;
                }

                .scans-grid {
                    display: grid;
                    gap: 1.5rem;
                }

                @media (max-width: 768px) {
                    .scans-grid {
                        grid-template-columns: 1fr;
                    }
                }

                @media (min-width: 768px) {
                    .scans-grid {
                        grid-template-columns: repeat(2, 1fr);
                    }
                }

                @media (min-width: 1024px) {
                    .scans-grid {
                        grid-template-columns: repeat(3, 1fr);
                    }
                }
            </style>
            
            <div class="container mx-auto py-6 space-y-6">
                <!-- Page Header -->
                <div class="flex flex-col space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight">{{ $PageTitle }}</h1>
                            <p class="text-muted-foreground">{{ $PageDescription }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('scanning.index') }}" class="btn btn-outline gap-2">
                                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                                Back to Scanning
                            </a>
                            <button class="btn btn-primary gap-2" onclick="uploadMoreScans()">
                                <i data-lucide="plus-circle" class="h-4 w-4"></i>
                                Upload More
                            </button>
                        </div>
                    </div>
                    
                    <!-- File Information Card -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i data-lucide="folder-open" class="h-5 w-5 text-blue-600 mr-3"></i>
                                <div>
                                    <p class="font-medium text-blue-900">{{ $fileIndexing->file_number }}</p>
                                    <p class="text-sm text-blue-700">{{ $fileIndexing->file_title }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-blue-700">{{ $allScans->count() }} {{ $allScans->count() == 1 ? 'scan' : 'scans' }}</p>
                                <p class="text-xs text-blue-600">Storage: {{ $folderPath }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="tabs">
                    <div class="tabs-list grid w-full md:w-auto grid-cols-2">
                        <button class="tab active" role="tab" aria-selected="true" data-tab="scans-list">Scanned Documents</button>
                        <button class="tab" role="tab" aria-selected="false" data-tab="file-manager">File Manager</button>
                    </div>

                    <!-- Scanned Documents Tab -->
                    <div class="tab-content mt-6 active" role="tabpanel" aria-hidden="false" data-tab-content="scans-list">
                        <div class="card">
                            <div class="p-6 border-b">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div>
                                        <h2 class="text-lg font-semibold">Scanned Documents</h2>
                                        <p class="text-sm text-muted-foreground">All scanned files for {{ $fileIndexing->file_number }}</p>
                                    </div>
                                    <div class="relative w-full md:w-64">
                                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                                        <input type="search" placeholder="Search scans..." class="input w-full pl-8" id="search-scans">
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                @if($allScans && $allScans->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="scans-grid">
                                        @foreach($allScans as $scan)
                                            <div class="scan-card border rounded-lg overflow-hidden hover:shadow-md transition-shadow" data-scan-id="{{ $scan->id }}">
                                                <!-- Document Preview -->
                                                <div class="aspect-[3/4] bg-gray-100 relative">
                                                    @if(in_array(strtolower(pathinfo($scan->document_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                        <img src="{{ Storage::disk('public')->url($scan->document_path) }}" 
                                                             alt="{{ $scan->original_filename }}" 
                                                             class="w-full h-full object-cover">
                                                    @elseif(strtolower(pathinfo($scan->document_path, PATHINFO_EXTENSION)) === 'pdf')
                                                        <div class="w-full h-full flex items-center justify-center bg-red-50">
                                                            <div class="text-center">
                                                                <i data-lucide="file-text" class="h-12 w-12 text-red-500 mx-auto mb-2"></i>
                                                                <p class="text-sm text-red-700 font-medium">PDF Document</p>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-gray-50">
                                                            <div class="text-center">
                                                                <i data-lucide="file" class="h-12 w-12 text-gray-400 mx-auto mb-2"></i>
                                                                <p class="text-sm text-gray-600">{{ strtoupper(pathinfo($scan->document_path, PATHINFO_EXTENSION)) }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Status Badge -->
                                                    <div class="absolute top-2 right-2">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                            {{ $scan->status === 'typed' ? 'bg-green-100 text-green-800' : 
                                                               ($scan->status === 'scanned' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                            {{ ucfirst($scan->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Document Info -->
                                                <div class="p-4">
                                                    <h3 class="font-medium text-sm mb-2 truncate" title="{{ $scan->original_filename }}">
                                                        {{ $scan->original_filename }}
                                                    </h3>
                                                    <div class="space-y-1 text-xs text-gray-500">
                                                        <div class="flex justify-between">
                                                            <span>Type:</span>
                                                            <span>{{ $scan->document_type }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span>Paper Size:</span>
                                                            <span>{{ $scan->paper_size }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span>Uploaded:</span>
                                                            <span>{{ $scan->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span>By:</span>
                                                            <span>{{ $scan->uploader->name ?? 'Unknown' }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($scan->notes)
                                                        <div class="mt-2 p-2 bg-gray-50 rounded text-xs">
                                                            <strong>Notes:</strong> {{ $scan->notes }}
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Actions -->
                                                    <div class="mt-3 flex items-center justify-between">
                                                        <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium" 
                                                                onclick="viewDocument('{{ Storage::disk('public')->url($scan->document_path) }}', '{{ $scan->original_filename }}')">
                                                            <i data-lucide="eye" class="h-4 w-4 inline mr-1"></i>
                                                            View
                                                        </button>
                                                        <div class="flex items-center gap-2">
                                                            <button class="text-gray-600 hover:text-gray-900 text-sm" 
                                                                    onclick="editScan({{ $scan->id }})">
                                                                <i data-lucide="edit" class="h-4 w-4"></i>
                                                            </button>
                                                            <button class="text-red-600 hover:text-red-900 text-sm" 
                                                                    onclick="deleteScan({{ $scan->id }})">
                                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <i data-lucide="inbox" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                                        <p class="text-gray-500">No scanned documents found for this file</p>
                                        <button class="btn btn-primary mt-4 gap-2" onclick="uploadMoreScans()">
                                            <i data-lucide="plus-circle" class="h-4 w-4"></i>
                                            Upload First Scan
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- File Manager Tab -->
                    <div class="tab-content mt-6 hidden" role="tabpanel" aria-hidden="true" data-tab-content="file-manager">
                        <div class="card">
                            <div class="p-6 border-b">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div>
                                        <h2 class="text-lg font-semibold">File Manager</h2>
                                        <p class="text-sm text-muted-foreground">Browse and manage files in {{ $folderPath }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button class="btn btn-outline btn-sm gap-2" onclick="refreshFileManager()">
                                            <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                            Refresh
                                        </button>
                                        <button class="btn btn-primary btn-sm gap-2" onclick="uploadMoreScans()">
                                            <i data-lucide="upload" class="h-4 w-4"></i>
                                            Upload
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                @if(count($folderFiles) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modified</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($folderFiles as $file)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                @if(in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                                    <i data-lucide="image" class="h-5 w-5 text-green-500 mr-3"></i>
                                                                @elseif(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) === 'pdf')
                                                                    <i data-lucide="file-text" class="h-5 w-5 text-red-500 mr-3"></i>
                                                                @else
                                                                    <i data-lucide="file" class="h-5 w-5 text-gray-400 mr-3"></i>
                                                                @endif
                                                                <span class="text-sm font-medium text-gray-900">{{ $file['name'] }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ formatBytes($file['size']) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ date('M d, Y H:i', $file['modified']) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <div class="flex items-center space-x-2">
                                                                <button class="text-indigo-600 hover:text-indigo-900" 
                                                                        onclick="viewDocument('{{ $file['url'] }}', '{{ $file['name'] }}')">
                                                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                                                </button>
                                                                <a href="{{ $file['url'] }}" download class="text-green-600 hover:text-green-900">
                                                                    <i data-lucide="download" class="h-4 w-4"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <i data-lucide="folder-x" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                                        <p class="text-gray-500">No files found in this folder</p>
                                        <p class="text-sm text-gray-400 mt-1">{{ $folderPath }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Viewer Modal -->
        <div id="document-viewer-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b">
                        <h3 class="text-lg font-semibold" id="document-title">Document Viewer</h3>
                        <button onclick="closeDocumentViewer()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="h-6 w-6"></i>
                        </button>
                    </div>
                    <div class="p-4 max-h-[80vh] overflow-auto">
                        <div id="document-content" class="text-center">
                            <!-- Document content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('admin.footer')
        
        <script>
            // Initialize Lucide icons
            lucide.createIcons();

            // Tab functionality
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Update tab states
                    document.querySelectorAll('.tab').forEach(t => {
                        t.classList.remove('active');
                        t.setAttribute('aria-selected', 'false');
                    });
                    this.classList.add('active');
                    this.setAttribute('aria-selected', 'true');
                    
                    // Update content states
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.add('hidden');
                        content.setAttribute('aria-hidden', 'true');
                    });
                    const targetContent = document.querySelector(`[data-tab-content="${tabName}"]`);
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                        targetContent.setAttribute('aria-hidden', 'false');
                    }
                });
            });

            // Search functionality
            document.getElementById('search-scans').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const scanCards = document.querySelectorAll('.scan-card');
                
                scanCards.forEach(card => {
                    const filename = card.querySelector('h3').textContent.toLowerCase();
                    const documentType = card.querySelector('.space-y-1 span:nth-child(2)').textContent.toLowerCase();
                    
                    if (filename.includes(searchTerm) || documentType.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            // Document viewer functions
            function viewDocument(url, filename) {
                const modal = document.getElementById('document-viewer-modal');
                const title = document.getElementById('document-title');
                const content = document.getElementById('document-content');
                
                title.textContent = filename;
                
                const extension = filename.split('.').pop().toLowerCase();
                
                if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                    content.innerHTML = `<img src="${url}" alt="${filename}" class="max-w-full h-auto">`;
                } else if (extension === 'pdf') {
                    content.innerHTML = `<iframe src="${url}" width="100%" height="600px" frameborder="0"></iframe>`;
                } else {
                    content.innerHTML = `
                        <div class="text-center py-8">
                            <i data-lucide="file" class="h-16 w-16 mx-auto text-gray-400 mb-4"></i>
                            <p class="text-gray-600 mb-4">Cannot preview this file type</p>
                            <a href="${url}" download class="btn btn-primary gap-2">
                                <i data-lucide="download" class="h-4 w-4"></i>
                                Download File
                            </a>
                        </div>
                    `;
                }
                
                modal.classList.remove('hidden');
                lucide.createIcons();
            }

            function closeDocumentViewer() {
                document.getElementById('document-viewer-modal').classList.add('hidden');
            }

            // Upload more scans
            function uploadMoreScans() {
                window.location.href = `/scanning?file_indexing_id={{ $fileIndexing->id }}`;
            }

            // Refresh file manager
            function refreshFileManager() {
                window.location.reload();
            }

            // Edit scan (placeholder)
            function editScan(scanId) {
                alert('Edit functionality will be implemented');
            }

            // Delete scan (placeholder)
            function deleteScan(scanId) {
                if (confirm('Are you sure you want to delete this scan?')) {
                    // Implementation for delete
                    alert('Delete functionality will be implemented');
                }
            }

            // Close modal on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDocumentViewer();
                }
            });

            // Close modal on backdrop click
            document.getElementById('document-viewer-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDocumentViewer();
                }
            });
        </script>
    </div>
@endsection

@php
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
@endphp