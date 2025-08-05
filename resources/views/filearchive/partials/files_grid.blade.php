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
                        <div class="aspect-3/4 bg-gray-100 relative">
                            <!-- Document cover with document-style preview -->
                            <div class="absolute inset-0 flex flex-col bg-white">
                                <div class="h-8 bg-blue-500 flex items-center justify-between px-3">
                                    <div class="flex space-x-1">
                                        <div class="w-3 h-3 rounded-full bg-gray-200 opacity-70"></div>
                                        <div class="w-3 h-3 rounded-full bg-gray-200 opacity-70"></div>
                                        <div class="w-3 h-3 rounded-full bg-gray-200 opacity-70"></div>
                                    </div>
                                    <span class="text-white font-medium text-xs">
                                        {{ $file->pagetypings_count }} Pages
                                    </span>
                                </div>
                                <div class="flex-1 flex flex-col p-4 overflow-hidden">
                                    <!-- Document-style content preview based on page types -->
                                    @php
                                        $pageTypes = $file->pagetypings->pluck('page_type')->unique();
                                        $hasDeeds = $pageTypes->contains('Deed');
                                        $hasCertificates = $pageTypes->contains('Certificate');
                                        $hasApplications = $pageTypes->contains('Application Form');
                                        $hasMaps = $pageTypes->contains('Map') || $pageTypes->contains('Survey Plan');
                                    @endphp
                                    
                                    @if($hasCertificates)
                                        <!-- Certificate-style preview -->
                                        <div class="w-full h-3 bg-blue-200 rounded mb-2"></div>
                                        <div class="w-3/4 h-3 bg-blue-200 rounded mb-3"></div>
                                        <div class="w-full flex justify-center my-2">
                                            <div class="w-16 h-12 bg-blue-100 rounded border-2 border-blue-300"></div>
                                        </div>
                                        <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                                        <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                                        <div class="w-4/5 h-2 bg-gray-100 rounded"></div>
                                    @elseif($hasMaps)
                                        <!-- Map-style preview -->
                                        <div class="w-full h-3 bg-green-200 rounded mb-2"></div>
                                        <div class="w-4/5 h-3 bg-green-200 rounded mb-3"></div>
                                        <div class="w-full bg-gray-100 rounded-sm mb-3 p-1 flex-1 flex items-center justify-center relative">
                                            <div class="w-full h-full bg-gray-50">
                                                <div class="absolute w-1/2 h-px bg-gray-300 top-1/2 left-1/4"></div>
                                                <div class="absolute w-px h-1/2 bg-gray-300 top-1/4 left-1/2"></div>
                                                <div class="absolute w-4 h-4 rounded-full bg-green-100 border border-green-300 top-1/3 left-1/3"></div>
                                                <div class="absolute w-3 h-3 rounded-full bg-blue-100 border border-blue-300 bottom-1/4 right-1/4"></div>
                                            </div>
                                        </div>
                                    @elseif($hasApplications)
                                        <!-- Form-style preview -->
                                        <div class="w-full h-3 bg-orange-200 rounded mb-3"></div>
                                        <div class="mb-2">
                                            <div class="w-1/4 h-2 bg-gray-200 mb-1"></div>
                                            <div class="w-full h-3 bg-gray-100 rounded border border-gray-200"></div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="w-1/3 h-2 bg-gray-200 mb-1"></div>
                                            <div class="w-full h-3 bg-gray-100 rounded border border-gray-200"></div>
                                        </div>
                                        <div class="flex justify-end">
                                            <div class="w-1/4 h-4 bg-orange-500 rounded"></div>
                                        </div>
                                    @else
                                        <!-- Default document preview -->
                                        <div class="w-full h-3 bg-gray-200 rounded mb-2"></div>
                                        <div class="w-3/4 h-3 bg-gray-200 rounded mb-3"></div>
                                        <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                                        <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                                        <div class="w-5/6 h-2 bg-gray-100 rounded mb-3"></div>
                                        <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                                        <div class="w-4/5 h-2 bg-gray-100 rounded"></div>
                                    @endif
                                </div>
                            </div>

                            <!-- File type badge -->
                            <div class="absolute top-2 right-2">
                                <span class="badge badge-success text-xs font-medium">
                                    Archived
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
                                <span class="text-xs text-muted-foreground">
                                    {{ $file->pagetypings_count }} pages
                                </span>
                                <span class="badge badge-success text-xs">
                                    Completed
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
                            @foreach($file->pagetypings->pluck('page_type')->unique()->take(2) as $pageType)
                                <span class="badge badge-outline text-xs">{{ $pageType }}</span>
                            @endforeach
                            @if($file->pagetypings->pluck('page_type')->unique()->count() > 2)
                                <span class="badge badge-outline text-xs">+{{ $file->pagetypings->pluck('page_type')->unique()->count() - 2 }} more</span>
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