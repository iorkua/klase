<div id="files-container" class="card">
    <div class="p-6 border-b flex flex-row items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold">Archived Files</h2>
            <p class="text-sm text-muted-foreground">
                Recently archived digital files
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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
           

            <!-- File Card 3 - with document-style preview -->
            <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow cursor-pointer file-card" data-id="FILE-2023-003">
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
                    <h3 class="font-medium text-sm line-clamp-1" title="Kano Traders Association">
                        Kano Traders Association
                    </h3>
                    <div class="mt-1 flex items-center text-xs text-muted-foreground">
                        <span class="line-clamp-1" title="COM-91-249">
                            COM-91-249
                        </span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-muted-foreground">1.2 MB</span>
                        <span class="badge badge-secondary text-xs">
                            Archived
                        </span>
                    </div>
                </div>
                <div class="p-2 pt-0 flex flex-wrap gap-1">
                    <span class="badge badge-secondary text-xs">Commercial</span>
                    <span class="badge badge-secondary text-xs">Sabon Gari</span>
                </div>
            </div>

            <!-- File Card 4 - with document-style preview -->
            <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow cursor-pointer file-card" data-id="FILE-2023-004">
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
                            <!-- Document-style content preview with table -->
                            <div class="w-full h-3 bg-gray-200 rounded mb-2"></div>
                            <div class="w-2/3 h-3 bg-gray-200 rounded mb-4"></div>
                            
                            <!-- Table-like structure -->
                            <div class="w-full flex mb-1">
                                <div class="w-1/3 h-2 bg-gray-200 mr-1"></div>
                                <div class="w-1/3 h-2 bg-gray-200 mr-1"></div>
                                <div class="w-1/3 h-2 bg-gray-200"></div>
                            </div>
                            <div class="w-full h-px bg-gray-300 my-1"></div>
                            <div class="w-full flex mb-1">
                                <div class="w-1/3 h-2 bg-gray-100 mr-1"></div>
                                <div class="w-1/3 h-2 bg-gray-100 mr-1"></div>
                                <div class="w-1/3 h-2 bg-gray-100"></div>
                            </div>
                            <div class="w-full flex mb-1">
                                <div class="w-1/3 h-2 bg-gray-100 mr-1"></div>
                                <div class="w-1/3 h-2 bg-gray-100 mr-1"></div>
                                <div class="w-1/3 h-2 bg-gray-100"></div>
                            </div>
                            <div class="w-full flex mb-4">
                                <div class="w-1/3 h-2 bg-gray-100 mr-1"></div>
                                <div class="w-1/3 h-2 bg-gray-100 mr-1"></div>
                                <div class="w-1/3 h-2 bg-gray-100"></div>
                            </div>
                            
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
                    <h3 class="font-medium text-sm line-clamp-1" title="Musa Usman Bayero">
                        Musa Usman Bayero
                    </h3>
                    <div class="mt-1 flex items-center text-xs text-muted-foreground">
                        <span class="line-clamp-1" title="RES-2000-1904">
                            RES-2000-1904
                        </span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-muted-foreground">3.1 MB</span>
                        <span class="badge badge-outline text-xs">
                            Active
                        </span>
                    </div>
                </div>
                <div class="p-2 pt-0 flex flex-wrap gap-1">
                    <span class="badge badge-secondary text-xs">Residential</span>
                    <span class="badge badge-secondary text-xs">Right of Occupancy</span>
                </div>
            </div>

            <!-- File Card 5 - with document-style preview -->
            <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow cursor-pointer file-card" data-id="FILE-2023-005">
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
                            <!-- Document-style content preview with form -->
                            <div class="w-full h-3 bg-gray-200 rounded mb-3"></div>
                            
                            <div class="mb-2">
                                <div class="w-1/4 h-2 bg-gray-200 mb-1"></div>
                                <div class="w-full h-3 bg-gray-100 rounded border border-gray-200"></div>
                            </div>
                            
                            <div class="mb-2">
                                <div class="w-1/3 h-2 bg-gray-200 mb-1"></div>
                                <div class="w-full h-3 bg-gray-100 rounded border border-gray-200"></div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="w-1/4 h-2 bg-gray-200 mb-1"></div>
                                <div class="w-full h-3 bg-gray-100 rounded border border-gray-200"></div>
                            </div>
                            
                            <div class="flex justify-end">
                                <div class="w-1/4 h-4 bg-red-500 rounded"></div>
                            </div>
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
                    <h3 class="font-medium text-sm line-clamp-1" title="Hajiya Fatima Mohammed">
                        Hajiya Fatima Mohammed
                    </h3>
                    <div class="mt-1 flex items-center text-xs text-muted-foreground">
                        <span class="line-clamp-1" title="CON-IND-2021-37">
                            CON-IND-2021-37
                        </span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-muted-foreground">4.5 MB</span>
                        <span class="badge badge-destructive text-xs">
                            Inactive
                        </span>
                    </div>
                </div>
                <div class="p-2 pt-0 flex flex-wrap gap-1">
                    <span class="badge badge-secondary text-xs">Industrial</span>
                    <span class="badge badge-secondary text-xs">Deed</span>
                </div>
            </div>

            <!-- File Card 6 - with document-style preview -->
            <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow cursor-pointer file-card" data-id="FILE-2023-006">
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
                            <!-- Document-style content preview with map-like image -->
                            <div class="w-full h-3 bg-gray-200 rounded mb-2"></div>
                            <div class="w-4/5 h-3 bg-gray-200 rounded mb-3"></div>
                            
                            <div class="w-full bg-gray-100 rounded-sm mb-3 p-1 flex-1 flex items-center justify-center relative">
                                <!-- Simulated map elements -->
                                <div class="w-full h-full bg-gray-50">
                                    <div class="absolute w-1/2 h-px bg-gray-300 top-1/2 left-1/4"></div>
                                    <div class="absolute w-px h-1/2 bg-gray-300 top-1/4 left-1/2"></div>
                                    <div class="absolute w-4 h-4 rounded-full bg-red-100 border border-red-300 top-1/3 left-1/3"></div>
                                    <div class="absolute w-3 h-3 rounded-full bg-blue-100 border border-blue-300 bottom-1/4 right-1/4"></div>
                                    <div class="absolute w-12 h-1 bg-gray-300 bottom-1/3 left-1/4"></div>
                                </div>
                            </div>
                            
                            <div class="w-full h-2 bg-gray-100 rounded mb-2"></div>
                            <div class="w-3/4 h-2 bg-gray-100 rounded"></div>
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
                    <h3 class="font-medium text-sm line-clamp-1" title="Nasarawa Development Plan">
                        Nasarawa Development Plan
                    </h3>
                    <div class="mt-1 flex items-center text-xs text-muted-foreground">
                        <span class="line-clamp-1" title="RES-2019-746">
                            RES-2019-746
                        </span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-muted-foreground">8.7 MB</span>
                        <span class="badge badge-outline text-xs">
                            Active
                        </span>
                    </div>
                </div>
                <div class="p-2 pt-0 flex flex-wrap gap-1">
                    <span class="badge badge-secondary text-xs">Residential</span>
                    <span class="badge badge-secondary text-xs">Development Plan</span>
                    <span class="badge badge-secondary text-xs">Nasarawa</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center border-t pt-4 p-6">
        <button class="btn btn-outline">View All Archives</button>
    </div>
</div>
