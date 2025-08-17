<div class="bg-white rounded-lg shadow-sm border">
  <div class="px-6 py-4 border-b">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">File Tracking</h2>
      <div class="flex items-center gap-2">
        <button class="border rounded-md px-3 py-1 text-sm flex items-center">
          <svg class="h-3.5 w-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
          </svg>
          Filter
        </button>
        <button class="border rounded-md px-3 py-1 text-sm flex items-center">
          <svg class="h-3.5 w-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
          Export
        </button>
      </div>
    </div>
    
    <!-- Tabs -->
    <div class="mt-4">
      <div class="grid grid-cols-6 gap-1 rounded-md bg-gray-100 p-1">
        <button class="tab-button active rounded-md px-3 py-1 text-sm font-medium bg-white shadow" data-tab="all">All</button>
        <button class="tab-button rounded-md px-3 py-1 text-sm font-medium" data-tab="in-process">In Process</button>
        <button class="tab-button rounded-md px-3 py-1 text-sm font-medium" data-tab="pending">Pending</button>
        <button class="tab-button rounded-md px-3 py-1 text-sm font-medium" data-tab="on-hold">On Hold</button>
        <button class="tab-button rounded-md px-3 py-1 text-sm font-medium" data-tab="awaiting">Awaiting Approval</button>
        <button class="tab-button rounded-md px-3 py-1 text-sm font-medium" data-tab="completed">Completed</button>
      </div>
    </div>
  </div>
  
  <!-- Table -->
  <div class="p-6">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b">
            <th class="text-left py-3 px-4 font-medium">ID</th>
            <th class="text-left py-3 px-4 font-medium">File Number</th>
            <th class="text-left py-3 px-4 font-medium">Current Location</th>
            <th class="text-left py-3 px-4 font-medium">Handler</th>
            <th class="text-left py-3 px-4 font-medium">Due Date</th>
            <th class="text-left py-3 px-4 font-medium">Status</th>
            <th class="text-right py-3 px-4 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($fileTrackings as $tracking)
            @php
              $statusClass = match($tracking->status) {
                'active' => 'in-process',
                'checked_out' => 'pending', 
                'overdue' => 'on-hold',
                'returned' => 'completed',
                'lost' => 'on-hold',
                'archived' => 'completed',
                default => 'pending'
              };
              
              $statusBadge = match($tracking->status) {
                'active' => ['badge-default', 'Active'],
                'checked_out' => ['badge-warning', 'Checked Out'],
                'overdue' => ['badge-destructive', 'Overdue'],
                'returned' => ['badge-outline', 'Returned'],
                'lost' => ['badge-destructive', 'Lost'],
                'archived' => ['badge-outline', 'Archived'],
                default => ['badge-secondary', ucfirst($tracking->status)]
              };
            @endphp
            <tr class="border-b hover:bg-gray-50 cursor-pointer file-row {{ $loop->first ? 'bg-gray-50' : '' }}" 
                data-status="{{ $statusClass }}" 
                data-tracking-id="{{ $tracking->id }}">
              <td class="py-3 px-4 font-medium">TRK-{{ str_pad($tracking->id, 6, '0', STR_PAD_LEFT) }}</td>
              <td class="py-3 px-4">
                <div class="flex items-center gap-2">
                  <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <span class="truncate max-w-[200px]">{{ $tracking->fileIndexing->file_number ?? 'N/A' }}</span>
                  @if($tracking->rfid_tag)
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">RFID</span>
                  @endif
                </div>
              </td>
              <td class="py-3 px-4">{{ $tracking->current_location ?? 'Not Set' }}</td>
              <td class="py-3 px-4">{{ $tracking->current_handler ?? 'Not Assigned' }}</td>
              <td class="py-3 px-4">
                @if($tracking->due_date)
                  {{ $tracking->due_date->format('Y-m-d') }}
                  @if($tracking->is_overdue)
                    <span class="text-red-600 text-xs ml-1">({{ abs($tracking->days_until_due) }} days overdue)</span>
                  @elseif($tracking->days_until_due <= 3 && $tracking->days_until_due > 0)
                    <span class="text-yellow-600 text-xs ml-1">({{ $tracking->days_until_due }} days left)</span>
                  @endif
                @else
                  <span class="text-gray-400">Not Set</span>
                @endif
              </td>
              <td class="py-3 px-4">
                <span class="badge {{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
              </td>
              <td class="py-3 px-4 text-right">
                <div class="flex justify-end gap-2">
                  <button class="file-view-btn p-1 rounded-md hover:bg-gray-100" data-tracking-id="{{ $tracking->id }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  <button class="p-1 rounded-md hover:bg-gray-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                <div class="flex flex-col items-center">
                  <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <p class="text-lg font-medium">No files are currently being tracked</p>
                  <p class="text-sm">Start tracking files by clicking "Track New File" above</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  
  <!-- Table Footer -->
  <div class="px-6 py-4 border-t flex items-center justify-between">
    <div class="text-sm text-gray-500">
      @if($fileTrackings->total() > 0)
        Showing {{ $fileTrackings->firstItem() }} to {{ $fileTrackings->lastItem() }} of {{ $fileTrackings->total() }} files
      @else
        No files to display
      @endif
    </div>
    <div class="flex items-center space-x-2">
      @if($fileTrackings->onFirstPage())
        <button class="border rounded-md px-3 py-1 text-sm flex items-center disabled:opacity-50" disabled>
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
          Previous
        </button>
      @else
        <a href="{{ $fileTrackings->previousPageUrl() }}" class="border rounded-md px-3 py-1 text-sm flex items-center hover:bg-gray-50">
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
          Previous
        </a>
      @endif
      
      @if($fileTrackings->hasMorePages())
        <a href="{{ $fileTrackings->nextPageUrl() }}" class="border rounded-md px-3 py-1 text-sm flex items-center hover:bg-gray-50">
          Next
          <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </a>
      @else
        <button class="border rounded-md px-3 py-1 text-sm flex items-center disabled:opacity-50" disabled>
          Next
          <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      @endif
    </div>
  </div>
</div>
