@extends('layouts.app')
@section('page-title')
    {{ __('Page Typing Dashboard') }}
@endsection

@section('content')
  @include('pagetyping.css.style')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">
 <div class="container mx-auto py-6 space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col space-y-2">
      <h1 class="text-2xl font-bold tracking-tight">Page Typing Dashboard</h1>
      <p class="text-muted-foreground">Categorize and digitize file content</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <!-- Pending Page Typing -->
      <div class="card">
        <div class="p-4 pb-2">
          <h3 class="text-sm font-medium">Pending Page Typing</h3>
        </div>
        <div class="p-4 pt-0">
          <div class="text-2xl font-bold" id="pending-count">{{ $stats['pending_count'] ?? 0 }}</div>
          <p class="text-xs text-muted-foreground mt-1">Files waiting for page typing</p>
        </div>
      </div>

      <!-- In Progress -->
      <div class="card">
        <div class="p-4 pb-2">
          <h3 class="text-sm font-medium">In Progress</h3>
        </div>
        <div class="p-4 pt-0">
          <div class="text-2xl font-bold" id="in-progress-count">{{ $stats['in_progress_count'] ?? 0 }}</div>
          <p class="text-xs text-muted-foreground mt-1">Files currently being typed</p>
        </div>
      </div>

      <!-- Completed -->
      <div class="card">
        <div class="p-4 pb-2">
          <h3 class="text-sm font-medium">Completed</h3>
        </div>
        <div class="p-4 pt-0">
          <div class="text-2xl font-bold" id="completed-count">{{ $stats['completed_count'] ?? 0 }}</div>
          <p class="text-xs text-muted-foreground mt-1">Files completed typing</p>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <div class="tabs-list grid w-full md:w-auto grid-cols-3">
        <button class="tab active" role="tab" aria-selected="true" data-tab="pending">Pending Page Typing</button>
        <button class="tab" role="tab" aria-selected="false" data-tab="in-progress">In Progress</button>
        <button class="tab" role="tab" aria-selected="false" data-tab="completed">Completed</button>
      </div>

      <!-- Pending Tab -->
      <div class="tab-content mt-6 active" role="tabpanel" aria-hidden="false" data-tab-content="pending">
        <div class="card">
          <div class="p-6 border-b">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div>
                <h2 class="text-lg font-semibold">Files Pending Page Typing</h2>
                <p class="text-sm text-muted-foreground">Select a file to begin typing its content</p>
              </div>
              <div class="relative w-full md:w-64">
                <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                <input type="search" placeholder="Search files..." class="input w-full pl-8" id="search-pending-files">
              </div>
            </div>
          </div>
          <div class="p-6">
            <div id="pending-files-list" class="space-y-4">
              @if($pendingFiles && $pendingFiles->count() > 0)
                @foreach($pendingFiles as $file)
                  <div class="border rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                          <i data-lucide="folder" class="h-8 w-8 text-blue-500"></i>
                        </div>
                        <div>
                          <h3 class="text-sm font-medium">{{ $file->file_number }}</h3>
                          <p class="text-sm text-gray-600">{{ $file->file_title }}</p>
                          <p class="text-xs text-gray-500">{{ $file->scannings->count() }} documents • {{ $file->district }}</p>
                        </div>
                      </div>
                      <div class="flex items-center space-x-2">
                        <span class="badge bg-yellow-500 text-white">Pending</span>
                        <a href="{{ route('pagetyping.index', ['file_indexing_id' => $file->id]) }}" class="btn btn-primary btn-sm">
                          <i data-lucide="type" class="h-4 w-4 mr-1"></i>
                          Start Typing
                        </a>
                      </div>
                    </div>
                  </div>
                @endforeach
              @else
                <div class="text-center py-8">
                  <i data-lucide="inbox" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                  <p class="text-gray-500">No files pending page typing</p>
                  <p class="text-sm text-gray-400">Upload scanned documents first to begin page typing</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- In Progress Tab -->
      <div class="tab-content mt-6 hidden" role="tabpanel" aria-hidden="true" data-tab-content="in-progress">
        <div class="card">
          <div class="p-6 border-b">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div>
                <h2 class="text-lg font-semibold">Files In Progress</h2>
                <p class="text-sm text-muted-foreground">Files that are partially typed</p>
              </div>
              <div class="relative w-full md:w-64">
                <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                <input type="search" placeholder="Search files..." class="input w-full pl-8" id="search-progress-files">
              </div>
            </div>
          </div>
          <div class="p-6">
            <div id="in-progress-files-list" class="space-y-4">
              @if($inProgressFiles && $inProgressFiles->count() > 0)
                @foreach($inProgressFiles as $file)
                  <div class="border rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                          <i data-lucide="folder-open" class="h-8 w-8 text-orange-500"></i>
                        </div>
                        <div>
                          <h3 class="text-sm font-medium">{{ $file->file_number }}</h3>
                          <p class="text-sm text-gray-600">{{ $file->file_title }}</p>
                          <p class="text-xs text-gray-500">{{ $file->pagetypings->count() }}/{{ $file->scannings->count() }} pages typed</p>
                        </div>
                      </div>
                      <div class="flex items-center space-x-2">
                        <span class="badge bg-orange-500 text-white">In Progress</span>
                        <a href="{{ route('pagetyping.index', ['file_indexing_id' => $file->id]) }}" class="btn btn-primary btn-sm">
                          <i data-lucide="edit" class="h-4 w-4 mr-1"></i>
                          Continue
                        </a>
                      </div>
                    </div>
                  </div>
                @endforeach
              @else
                <div class="text-center py-8">
                  <i data-lucide="inbox" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                  <p class="text-gray-500">No files in progress</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Completed Tab -->
      <div class="tab-content mt-6 hidden" role="tabpanel" aria-hidden="true" data-tab-content="completed">
        <div class="card">
          <div class="p-6 border-b">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div>
                <h2 class="text-lg font-semibold">Completed Files</h2>
                <p class="text-sm text-muted-foreground">Files with completed page typing</p>
              </div>
              <div class="relative w-full md:w-64">
                <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                <input type="search" placeholder="Search files..." class="input w-full pl-8" id="search-completed-files">
              </div>
            </div>
          </div>
          <div class="p-6">
            @if($completedFiles && $completedFiles->count() > 0)
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Number</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Typed</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typed By</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pages</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="completed-files-list" class="bg-white divide-y divide-gray-200">
                    @foreach($completedFiles as $file)
                      <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                          {{ $file->file_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ $file->file_title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ $file->updated_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          @if($file->pagetypings->count() > 0)
                            {{ $file->pagetypings->first()->typedBy->name ?? 'Unknown' }}
                          @else
                            Unknown
                          @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Completed
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ $file->pagetypings->count() }} pages
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <div class="flex items-center space-x-2">
                            <button class="text-indigo-600 hover:text-indigo-900" onclick="togglePageDetails({{ $file->id }})">
                              <i data-lucide="eye" class="h-4 w-4 mr-1"></i>
                              View Pages
                            </button>
                            <a href="{{ route('pagetyping.index', ['file_indexing_id' => $file->id]) }}" class="text-gray-600 hover:text-gray-900">
                              <i data-lucide="external-link" class="h-4 w-4 mr-1"></i>
                              Open
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
                <i data-lucide="inbox" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                <p class="text-gray-500">No completed files</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>
    
    <!-- Simple Dashboard JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Tab switching functionality
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        function switchTab(tabName) {
            tabs.forEach(tab => {
                if (tab.getAttribute('data-tab') === tabName) {
                    tab.classList.add('active');
                    tab.setAttribute('aria-selected', 'true');
                } else {
                    tab.classList.remove('active');
                    tab.setAttribute('aria-selected', 'false');
                }
            });

            tabContents.forEach(content => {
                if (content.getAttribute('data-tab-content') === tabName) {
                    content.classList.remove('hidden');
                    content.classList.add('active');
                    content.setAttribute('aria-hidden', 'false');
                } else {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                    content.setAttribute('aria-hidden', 'true');
                }
            });
        }
        
        // Add event listeners to tabs
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabName = tab.getAttribute('data-tab');
                switchTab(tabName);
            });
        });
        
        // Search functionality
        const searchInputs = document.querySelectorAll('input[type="search"]');
        searchInputs.forEach(input => {
            input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const listId = this.id.replace('search-', '') + '-list';
                const list = document.getElementById(listId);
                
                if (list) {
                    const items = list.querySelectorAll('.border.rounded-lg, tr');
                    items.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }
            });
        });
        
        console.log('Page Typing Dashboard initialized');
    });
    
    // Toggle page details for completed files
    function togglePageDetails(fileIndexingId) {
        console.log('Toggle page details for file:', fileIndexingId);
        // This function can be implemented later for viewing page details
        alert('Page details view will be implemented soon');
    }
    </script>
@endsection