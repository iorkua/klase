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
              <h1 class="text-2xl font-bold tracking-tight">Page Typing</h1>
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
                  <div class="text-2xl font-bold" id="pending-count">4</div>
                  <p class="text-xs text-muted-foreground mt-1">Files waiting for page typing</p>
                </div>
              </div>
        
              <!-- In Progress -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">In Progress</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="in-progress-count">1</div>
                  <p class="text-xs text-muted-foreground mt-1">Files currently being typed</p>
                </div>
              </div>
        
              <!-- Completed -->
              <div class="card">
                <div class="p-4 pb-2">
                  <h3 class="text-sm font-medium">Completed</h3>
                </div>
                <div class="p-4 pt-0">
                  <div class="text-2xl font-bold" id="completed-count">2</div>
                  <p class="text-xs text-muted-foreground mt-1">Files completed typing</p>
                </div>
              </div>
            </div>
        
            <!-- Tabs -->
            <div class="tabs">
              <div class="tabs-list grid w-full md:w-auto grid-cols-4">
                <button class="tab" role="tab" aria-selected="true" data-tab="pending">Pending Page Typing</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="in-progress">In Progress</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="completed">Completed</button>
                <button class="tab" role="tab" aria-selected="false" data-tab="typing" aria-disabled="true" id="typing-tab">Typing</button>
              </div>
        
              <!-- Pending Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="false" data-tab-content="pending">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Files Pending Page Typing</h2>
                        <p class="text-sm text-muted-foreground">Select a file to begin typing its content</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search files..." class="input w-full pl-8">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div id="pending-files-list" class="rounded-md border divide-y">
                      <!-- Pending files will be added here dynamically -->
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- In Progress Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="in-progress">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Files In Progress</h2>
                        <p class="text-sm text-muted-foreground">Files that are partially typed</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search files..." class="input w-full pl-8">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div id="in-progress-files-list" class="rounded-md border divide-y">
                      <!-- In progress files will be added here dynamically -->
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- Completed Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="completed">
                <div class="card">
                  <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                      <div>
                        <h2 class="text-lg font-semibold">Completed Files</h2>
                        <p class="text-sm text-muted-foreground">Files that have been fully typed</p>
                      </div>
                      <div class="relative w-full md:w-64">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="search" placeholder="Search files..." class="input w-full pl-8">
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <!-- Replaced card-based layout with proper HTML table -->
                    <div class="overflow-x-auto">
                      <table class="w-full border-collapse">
                        <thead>
                          <tr class="border-b bg-muted/20">
                            <th class="text-left p-3 font-medium">File Number</th>
                            <th class="text-left p-3 font-medium">File Name</th>
                            <th class="text-left p-3 font-medium">Date Typed</th>
                            <th class="text-left p-3 font-medium">Typed By</th>
                            <th class="text-left p-3 font-medium">Status</th>
                            <th class="text-left p-3 font-medium">Pages</th>
                            <th class="text-left p-3 font-medium">Actions</th>
                          </tr>
                        </thead>
                        <tbody id="completed-files-table-body">
                          <!-- Table rows will be added here dynamically -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
        
              <!-- Typing Tab -->
              <div class="tab-content mt-6" role="tabpanel" aria-hidden="true" data-tab-content="typing">
                <div class="card" id="typing-card">
                  <!-- Typing content will be added here dynamically -->
                </div>
              </div>
            </div>
          </div>
        
        </div>

        <!-- Footer -->
        @include('admin.footer')
    </div>

    <!-- Page Typing Dashboard JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>


 
    @include('pagetyping.js.javascript')  



  
@endsection
