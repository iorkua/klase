<div id="files-container" class="card">
    <div class="p-6 border-b flex flex-row items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold">Archived Files</h2>
            <p class="text-sm text-muted-foreground">
                Completed page typed digital files ({{ $completedFiles->total() }} files)
            </p>
        </div>
        <div class="flex gap-2">
            <button id="filter-button" class="btn btn-outline btn-sm gap-1">
                <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                Filter
            </button>
            <button id="sort-button" class="btn btn-outline btn-sm gap-1">
                <i data-lucide="sort-asc" class="h-3.5 w-3.5"></i>
                Sort
            </button>
        </div>
    </div>

    <div class="p-6">
        @if($completedFiles->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="files-grid">
                @foreach($completedFiles as $file)
                    <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow cursor-pointer file-card" 
                         data-id="{{ $file->id }}" onclick="showFileDetails({{ $file->id }})">
                        <div class="aspect-[3/4] bg-gray-100 relative">
                            <!-- Document cover with document-style preview -->
                            <div class="absolute inset-0 flex flex-col bg-white">
                                <div class="h-8 bg-red-500 flex items-center justify-between px-3">
                                    <div class="flex space-x-1">
                                        <div class="w-3 h-3 rounded-full bg-gray-200 opacity-70"></div>
                                        <div class="w-3 h-3 rounded-full bg-gray-200 opacity-70"></div>
                                        <div class="w-3 h-3 rounded-full bg-gray-200 opacity-70"></div>
                                    </div>
                                    <span class="text-white font-medium text-xs">PDF</span>
                                </div>
                                <div class="flex-1 flex flex-col p-4 overflow-hidden">
                                    <!-- Document-style content preview -->
                                    <div class="w-full h-3 bg-gray-200 rounded mb-2"></div>
                                    <div class="w-3/4 h-3 bg-gray-200 rounded mb-3"></div>
                                    <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                                    <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                                    <div class="w-5/6 h-2 bg-gray-100 rounded mb-3"></div>
                                    <div class="w-full flex justify-center my-2">
                                        <div class="w-16 h-12 bg-gray-200 rounded"></div>
                                    </div>
                                    <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                                    <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                                    <div class="w-4/5 h-2 bg-gray-100 rounded"></div>
                                </div>
                            </div>

                            <!-- File type badge -->
                            <div class="absolute top-2 right-2">
                                <span class="badge badge-secondary text-xs font-medium">
                                    PDF
                                </span>
                            </div>
                        </div>

                        <div class="p-3">
                            <h3 class="font-medium text-sm line-clamp-1" title="{{ $file->file_title }}">
                                {{ $file->file_title }}
                            </h3>
                            <div class="mt-1 flex items-center text-xs text-muted-foreground">
                                <span class="line-clamp-1" title="{{ $file->file_number }}">
                                    {{ $file->file_number }}
                                </span>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                @php
                                    // Calculate estimated file size based on page count
                                    $pageCount = $file->pagetypings_count ?? 1;
                                    $estimatedSizeKB = $pageCount * 120; // Assume ~120KB per page
                                    if ($estimatedSizeKB >= 1024) {
                                        $fileSize = round($estimatedSizeKB / 1024, 1) . ' MB';
                                    } else {
                                        $fileSize = $estimatedSizeKB . ' KB';
                                    }
                                @endphp
                                <span class="text-xs text-muted-foreground">{{ $fileSize }}</span>
                                <span class="badge badge-secondary text-xs">
                                    Archived
                                </span>
                            </div>
                        </div>
                        <div class="p-2 pt-0 flex flex-wrap gap-1">
                            @if($file->land_use_type)
                                <span class="badge badge-secondary text-xs">{{ $file->land_use_type }}</span>
                            @endif
                            @if($file->district)
                                <span class="badge badge-secondary text-xs">{{ $file->district }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($completedFiles->hasPages())
                <div class="flex justify-center border-t pt-6 mt-6">
                    {{ $completedFiles->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <i data-lucide="archive" class="h-16 w-16 mx-auto text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Archived Files Found</h3>
                <p class="text-gray-600 mb-6">
                    @if(request()->filled('search'))
                        No files match your search criteria. Try adjusting your search terms.
                    @else
                        Complete page typing for files to see them in the archive.
                    @endif
                </p>
                @if(request()->filled('search'))
                    <a href="{{ route('filearchive.index') }}" class="btn btn-outline">
                        <i data-lucide="x" class="h-4 w-4 mr-2"></i>
                        Clear Search
                    </a>
                @else
                    <a href="{{ route('pagetyping.index') }}" class="btn btn-primary">
                        <i data-lucide="type" class="h-4 w-4 mr-2"></i>
                        Go to Page Typing
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>