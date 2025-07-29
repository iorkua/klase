<div class="card mb-6">
    <div class="p-6 border-b">
        <h2 class="text-lg font-semibold">Search Archives</h2>
        <p class="text-sm text-muted-foreground">Find archived files by file number, title, or content</p>
    </div>
    <div class="p-6">
        <form id="search-form" action="{{ route('filearchive.index') }}" method="GET">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium mb-1">Search Term</label>
                    <div class="relative mt-1">
                        <i data-lucide="search" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input id="search" name="search" placeholder="Enter search term..." class="input pl-8">
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-2">
                    <div>
                        <label for="searchField" class="block text-sm font-medium mb-1">Search In</label>
                        <select id="searchField" name="field" class="select w-full md:w-[180px]">
                            <option value="all">All Fields</option>
                            <option value="page">Page Title</option>
                            <option value="type">Document Type</option>
                            <option value="fileName">File Name</option>
                            <option value="fileNumber">File Number</option>
                        </select>
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium mb-1">Category</label>
                        <select id="category" name="category" class="select w-full md:w-[180px]">
                            <option value="all">All Categories</option>
                            <option value="land">Land Records</option>
                            <option value="legal">Legal Documents</option>
                            <option value="admin">Administrative</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" id="search-button" class="btn btn-primary">Search</button>
                        <button type="reset" id="reset-search" class="btn btn-outline">Clear</button>
                    </div>
                </div>
            </div>
        </form>

        <div id="search-type-indicator" class="mt-2 flex items-center hidden">
            <span id="search-badge" class="badge badge-outline text-xs"></span>
            <span id="search-description" class="text-xs text-muted-foreground ml-2"></span>
        </div>
        
        <!-- Advanced Filters (Initially Hidden) -->
        <div id="advanced-filters" class="mt-4 pt-4 border-t hidden">
            <h3 class="text-sm font-medium mb-2">Advanced Filters</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Date Range</label>
                    <div class="flex items-center gap-2">
                        <input type="date" class="input py-1 text-sm" placeholder="From">
                        <span class="text-xs text-muted-foreground">to</span>
                        <input type="date" class="input py-1 text-sm" placeholder="To">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">File Type</label>
                    <select class="select text-sm">
                        <option value="">Any Type</option>
                        <option value="Certificate of Occupancy">Certificate of Occupancy</option>
                        <option value="Right of Occupancy">Right of Occupancy</option>
                        <option value="Deed of Assignment">Deed of Assignment</option>
                        <option value="Development Plan">Development Plan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">File Format</label>
                    <div class="flex flex-wrap gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" class="mr-1" checked> PDF
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="mr-1"> DOC
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="mr-1"> JPG
                        </label>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-sm font-medium mb-1">Tags</label>
                <div class="flex flex-wrap gap-2">
                    <span class="badge badge-outline px-2 py-1 cursor-pointer">Residential</span>
                    <span class="badge badge-outline px-2 py-1 cursor-pointer">Commercial</span>
                    <span class="badge badge-outline px-2 py-1 cursor-pointer">Condominium</span>
                    <span class="badge badge-outline px-2 py-1 cursor-pointer">Certificate</span>
                    <span class="badge badge-outline px-2 py-1 cursor-pointer">Nasarawa</span>
                    <span class="badge badge-outline px-2 py-1 cursor-pointer">Deed</span>
                    <span class="badge badge-outline px-2 py-1 cursor-pointer">Bompai</span>
                </div>
            </div>
        </div>
        
        <div class="mt-3 text-center">
            <button id="toggle-advanced-filters" class="btn btn-ghost btn-sm">
                <span id="advanced-filters-text">Show Advanced Filters</span>
                <i data-lucide="chevron-down" class="h-4 w-4 ml-1"></i>
            </button>
        </div>
    </div>
</div>
