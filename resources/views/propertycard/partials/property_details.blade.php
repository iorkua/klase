<div id="property-details-content" class="tab-content active" style="display: block;">
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-medium">Property Records</h2>
            <div class="flex items-center gap-2">
                <input type="text" id="property-search" class="form-input w-64" placeholder="Search properties...">
                <button id="reset-cards-view" class="btn btn-secondary" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-2">
                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                        <path d="M21 3v5h-5"></path>
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                        <path d="M3 21v-5h5"></path>
                    </svg>
                    Reset View
                </button>
                <!-- Improved Add New Property Card Button -->
                <button id="add-property-btn" class="btn btn-primary flex items-center whitespace-nowrap shadow-lg border-2 border-blue-400 bg-gradient-to-r from-blue-500 to-blue-700 text-white hover:from-blue-600 hover:to-blue-800 transition-all scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 mr-2">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Add New Property Record
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Property Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6" id="property-cards-container">
                <!-- Improved Add New Property Card -->
                <div class="border-2 border-dashed border-blue-400 rounded-lg shadow-lg cursor-pointer hover:bg-blue-50 transition-all flex flex-col items-center justify-center p-8 bg-gradient-to-br from-blue-50 to-white" id="add-property-card">
                    <div class="h-16 w-16 rounded-full bg-blue-200 flex items-center justify-center mb-4 shadow">
                        <span class="text-blue-700 text-3xl font-bold">+</span>
                    </div>
                    <h3 class="text-xl font-semibold text-center text-blue-800">Add New Property Record</h3>
                    <p class="text-base text-blue-600 text-center mt-2 font-medium">Click here to create a new property record</p>
                </div>
                <!-- Selected Property Detail Card will be injected here by JS -->
                <div id="selected-property-detail-card" class="col-span-2">
                    @if($Property_records->count())
                        @php $property = $Property_records->first(); @endphp
                        <div class="border rounded-lg shadow-lg overflow-hidden bg-blue-50 border-blue-200">
                            <div class="bg-blue-100 p-4 border-b border-blue-200">
                                <div class="flex justify-between items-center">
                                    <span class="bg-blue-200 text-blue-800 border-blue-300 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $property->title_type ?? 'N/A' }} - Selected Record
                                    </span>
                                    <button class="text-blue-600 hover:text-blue-800 property-options" data-id="{{ $property->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="12" cy="5" r="1"></circle>
                                            <circle cx="12" cy="19" r="1"></circle>
                                        </svg>
                                    </button>
                                </div>
                                <h3 class="mt-2 font-bold text-lg text-blue-900">
                                    @if($property->kangisFileNo)
                                        {{ $property->kangisFileNo }}
                                    @elseif($property->mlsFNo)
                                        {{ $property->mlsFNo }}
                                    @elseif($property->NewKANGISFileno)
                                        {{ $property->NewKANGISFileno }}
                                    @else
                                        No File Number
                                    @endif
                                </h3>
                            </div>
                            <div class="p-4">
                                <div class="space-y-4">
                                    <div class="text-sm">
                                        <strong>Description:</strong> {{ $property->property_description ?? 'No description available' }}
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <strong>LGA/City:</strong> {{ $property->lgsaOrCity ?? 'N/A' }}
                                        </div>
                                        <div>
                                            <strong>Plot Number:</strong> {{ $property->plot_no ?? 'N/A' }}
                                        </div>
                                        <div>
                                            <strong>Layout:</strong> {{ $property->layout ?? 'N/A' }}
                                        </div>
                                        <div>
                                            <strong>Location:</strong> {{ $property->location ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="border-t pt-3">
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <strong>Transaction Type:</strong> {{ $property->transaction_type ?? 'N/A' }}
                                            </div>
                                            <div>
                                                <strong>Transaction Date:</strong> {{ $property->transaction_date ? \Carbon\Carbon::parse($property->transaction_date)->toFormattedDateString() : 'N/A' }}
                                            </div>
                                            <div>
                                                <strong>Registration No:</strong> {{ $property->regNo ?? 'N/A' }}
                                            </div>
                                            <div>
                                                <strong>Instrument Type:</strong> {{ $property->instrument_type ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $fromParty = $toParty = $fromLabel = $toLabel = '';
                                        switch(strtolower($property->transaction_type ?? '')) {
                                            case 'assignment':
                                                $fromParty = $property->Assignor ?? '';
                                                $toParty = $property->Assignee ?? '';
                                                $fromLabel = 'Assignor';
                                                $toLabel = 'Assignee';
                                                break;
                                            case 'mortgage':
                                                $fromParty = $property->Mortgagor ?? '';
                                                $toParty = $property->Mortgagee ?? '';
                                                $fromLabel = 'Mortgagor';
                                                $toLabel = 'Mortgagee';
                                                break;
                                            case 'surrender':
                                                $fromParty = $property->Surrenderor ?? '';
                                                $toParty = $property->Surrenderee ?? '';
                                                $fromLabel = 'Surrenderor';
                                                $toLabel = 'Surrenderee';
                                                break;
                                            case 'sub-lease':
                                            case 'lease':
                                                $fromParty = $property->Lessor ?? '';
                                                $toParty = $property->Lessee ?? '';
                                                $fromLabel = 'Lessor';
                                                $toLabel = 'Lessee';
                                                break;
                                            default:
                                                $fromParty = $property->Grantor ?? '';
                                                $toParty = $property->Grantee ?? '';
                                                $fromLabel = 'Grantor';
                                                $toLabel = 'Grantee';
                                        }
                                    @endphp
                                    @if($fromParty || $toParty)
                                    <div class="border-t pt-3">
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            @if($fromParty)
                                                <div><strong>{{ $fromLabel }}:</strong> {{ $fromParty }}</div>
                                            @endif
                                            @if($toParty)
                                                <div><strong>{{ $toLabel }}:</strong> {{ $toParty }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="p-4 pt-0 flex justify-between border-t bg-white">
                                <div class="text-xs text-gray-500">
                                    <div>File Numbers:</div>
                                    @if($property->mlsFNo)
                                        <div>MLS: {{ $property->mlsFNo }}</div>
                                    @endif
                                    @if($property->kangisFileNo)
                                        <div>KANGIS: {{ $property->kangisFileNo }}</div>
                                    @endif
                                    @if($property->NewKANGISFileno)
                                        <div>New KANGIS: {{ $property->NewKANGISFileno }}</div>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <button class="px-3 py-1 border rounded-md text-sm flex items-center view-property-details bg-blue-600 text-white hover:bg-blue-700" data-id="{{ $property->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-1">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        View Full Details
                                    </button>
                                  
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Property Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>File Number</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Registration Particulars</th>
                            <th>Transaction Type</th>
                            <th>Instrument Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($Property_records as $property)
                        <tr>
                            <td class="font-medium">
                                @if($property->kangisFileNo)
                                    {{ $property->kangisFileNo }}
                                @elseif($property->mlsFNo)
                                    {{ $property->mlsFNo }}
                                @elseif($property->NewKANGISFileno)
                                    {{ $property->NewKANGISFileno }}
                                @else
                                    No File Number
                                @endif
                            </td>
                            <td>{{ Str::limit($property->property_description, 30) ?: 'No description' }}</td>
                            <td>{{ $property->location ?: 'N/A' }}</td>
                            <td>{{ $property->regNo ?: 'N/A' }}</td>
                            <td>{{ $property->transaction_type ?: 'N/A' }}</td>
                            <td>{{ $property->instrument_type ?: 'N/A' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button class="text-blue-500 hover:text-blue-700 transition-colors view-property" data-id="{{ $property->id }}">
                                        <i data-lucide="eye" class="h-4 w-4 text-blue-500"></i>
                                    </button>
                                    <button class="text-green-500 hover:text-green-700 transition-colors edit-property" data-id="{{ $property->id }}">
                                        <i data-lucide="pencil" class="h-4 w-4 text-green-500"></i>
                                    </button>
                                    <button class="text-red-500 hover:text-red-700 transition-colors delete-property" data-id="{{ $property->id }}">
                                        <i data-lucide="trash-2" class="h-4 w-4 text-red-500"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">No property records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide all property cards except the selected detail card and the add card
        const cardsContainer = document.getElementById('property-cards-container');
        if (cardsContainer) {
            // Only hide direct children .border cards, not nested ones
            cardsContainer.querySelectorAll(':scope > .border:not(#add-property-card):not(#selected-property-detail-card)').forEach(card => {
                card.style.display = 'none';
            });
        }
        // Do NOT auto-load the first property via JS, since it's rendered server-side now.
    });
</script>