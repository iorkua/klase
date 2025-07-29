@extends('layouts.app')
@section('page-title')
    {{ __('Planning Recommendation') }}
@endsection

<style>
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .tab-button {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .tab-button.active {
        background-color: #f3f4f6;
        font-weight: 500;
    }

    .tab-button:hover:not(.active) {
        background-color: #f9fafb;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #final-tab,
        #final-tab * {
            visibility: visible;
        }

        #final-tab {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print,
        button,
        .tab-button,
        footer,
        nav {
            display: none !important;
        }
    }
</style>
@include('sectionaltitling.partials.assets.css')
@section('content')
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">

            <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">

                @php
                    $surveyRecord = DB::connection('sqlsrv')
                        ->table('surveyCadastralRecord')
                        ->where('application_id', $application->id)
                        ->first();

                    $statusClass = match (strtolower($application->planning_recommendation_status ?? '')) {
                        'approve' => 'bg-green-100 text-green-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'decline' => 'bg-red-100 text-red-800',
                        'declined' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800',
                    };

                    $statusIcon = match (strtolower($application->planning_recommendation_status ?? '')) {
                        'approve' => 'check-circle',
                        'approved' => 'check-circle',
                        'pending' => 'clock',
                        'decline' => 'x-circle',
                        'declined' => 'x-circle',
                        default => 'help-circle',
                    };
                @endphp


                <div class="modal-content8 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium">Planning Recommendation <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusClass }}">
                                <i data-lucide="{{ $statusIcon }}" class="w-3 h-3 mr-1"></i>
                                {{ $application->planning_recommendation_status }}
                            </span></h2>
                        <button onclick="window.history.back()" class="text-gray-500 hover:text-gray-700">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="py-2">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-medium">{{ $application->land_use }} Property</h3>
                                <p class="text-xs text-gray-500">
                                    Application ID: {{ $application->applicationID }} | File No: {{ $application->fileno }}
                                </p>

                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusClass }}">
                                    <i data-lucide="{{ $statusIcon }}" class="w-3 h-3 mr-1"></i>
                                    {{ $application->planning_recommendation_status }}
                                </span>
                            </div>
                            <div class="text-right">
                                <h3 class="text-sm font-medium">
                                    @if ($application->applicant_type == 'individual')
                                        {{ $application->applicant_title }} {{ $application->first_name }}
                                        {{ $application->surname }}
                                    @elseif($application->applicant_type == 'corporate')
                                        {{ $application->rc_number }} {{ $application->corporate_name }}
                                    @elseif($application->applicant_type == 'multiple')
                                        @php
                                            $names = @json_decode($application->multiple_owners_names, true);
                                            if (is_array($names) && count($names) > 0) {
                                                echo implode(', ', $names);
                                            } else {
                                                echo $application->multiple_owners_names;
                                            }
                                        @endphp
                                    @endif


                                </h3>
                                <p class="text-xs text-gray-500">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        {{ $application->land_use }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Tabs Navigation -->



                        <div class="grid grid-cols-3 gap-2 mb-4">
                            @if (request()->query('url') !== 'phy_planning' || request()->query('url') == 'recommendation')
                                <button class="tab-button active" data-tab="detterment">
                                    <i data-lucide="calculator" class="w-3.5 h-3.5 mr-1.5"></i>
                                    Architectural Design
                                </button>
                            @endif

                            @if (request()->query('url') == 'phy_planning')
                                <button class="tab-button" data-tab="initial">
                                    <i data-lucide="banknote" class="w-3.5 h-3.5 mr-1.5"></i>
                                    Planning Recommendation Approval
                                </button>
                            @endif


                            @if (request()->query('url') !== 'phy_planning')
                                <button class="tab-button" data-tab="final">
                                    <i data-lucide="file-check" class="w-3.5 h-3.5 mr-1.5"></i>
                                    Planning Recommendation
                                </button>
                            @endif
                        </div>


                        @include('sub_actions.architecturaldesign')

                        <div id="initial-tab"
                            class="tab-content {{ request()->query('url') == 'phy_planning' ? 'active' : '' }}">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="p-4 border-b">
                                    <h3 class="text-sm font-medium">Planning Recommendation Approval</h3>
                                </div>
                                <form id="planningRecommendationForm" method="post" action="javascript:void(0);"
                                    onsubmit="handlePlanningRecommendation(event)">
                                    <!-- CSRF token for Laravel -->
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="p-4 space-y-4">
                                        <input type="hidden" id="application_id" value="{{ $application->id }}">
                                        <input type="hidden" name="fileno" value="{{ $application->fileno }}">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="text-xs font-medium block">
                                                    Decision
                                                </label>
                                                <div class="flex items-center space-x-4">
                                                <label class="inline-flex items-center">
                                                    <input 
                                                        type="radio" 
                                                        name="decision" 
                                                        value="Approved"
                                                        class="form-radio"
                                                        onchange="toggleObservationsAndReasonContainers(this)"
                                                        {{ strtolower($application->planning_recommendation_status) === 'approved' ? 'checked disabled' : '' }}
                                                    >
                                                    <span class="ml-2 text-sm {{ strtolower($application->planning_recommendation_status) === 'approved' ? 'text-gray-400' : '' }}">Approve</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input 
                                                        type="radio" 
                                                        name="decision" 
                                                        value="Declined"
                                                        class="form-radio"
                                                        onchange="toggleObservationsAndReasonContainers(this)"
                                                        {{ strtolower($application->planning_recommendation_status) === 'declined' ? 'checked disabled' : (strtolower($application->planning_recommendation_status) === 'approved' ? 'disabled' : '') }}
                                                    >
                                                    <span class="ml-2 text-sm {{ strtolower($application->planning_recommendation_status) === 'approved' ? 'text-gray-400' : '' }}">Decline</span>
                                                </label>

                                                    <script>
                                                        function toggleObservationsAndReasonContainers(radio) {
                                                            const reasonContainer = document.getElementById('reasonContainer');
                                                            const observationsContainer = document.getElementById('observationsContainer');
                                                            
                                                            // Only show reason container when declining
                                                            reasonContainer.style.display = (radio.value === 'Declined') ? 'block' : 'none';
                                                            
                                                            // Only show observations container when approving
                                                            if (observationsContainer) {
                                                                observationsContainer.style.display = (radio.value === 'Approved') ? 'block' : 'none';
                                                            }
                                                        }
                                                    </script>
                                                </div>
                                            </div>
                                           <div class="space-y-2">
                                                <label for="approval-date" class="text-xs font-medium block">
                                                    Approval/Decline Date
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <input id="approval-date" type="datetime-local" name="planning_approval_date"
                                                        value="{{ old('planning_approval_date') ?? now()->format('Y-m-d\TH:i') }}"
                                                        class="w-full p-2 border border-gray-300 rounded-md text-sm"
                                                        max="{{ now()->format('Y-m-d\TH:i') }}"
                                                    >
                                                    <button type="button" onclick="document.getElementById('approval-date').value = '{{ now()->format('Y-m-d\TH:i') }}';"
                                                        class="px-2 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300">
                                                        Use Current Date/Time
                                                    </button>
                                                </div>
                                                <span class="text-xs text-gray-500">You cannot select a future date.</span>
                                            </div>
                                        </div>
                                     <div id="observationsContainer" class="grid grid-cols-1 gap-4" style="display: none;">
                                            <div class="space-y-2">
                                                <label for="additionalObservations" class="text-xs font-medium block">
                                                    Additional Observations (If applicable)
                                                </label>
                                                <div class="border border-gray-300 rounded-md p-2">
                                                    <textarea id="additionalObservations" name="additionalObservations" rows="4" 
                                                        class="w-full p-2 border-none focus:outline-none focus:ring-0"
                                                        placeholder="Enter any additional observations or special considerations here...">{{ $additionalObservations ?? '' }}</textarea>
                                                    <div class="flex justify-end mt-2">
                                                        <button type="button" id="saveObservations" 
                                                            class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                                            Save Observations
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       <div id="reasonContainer" class="space-y-2" style="display: none;">
                                            <label for="comments" class="text-xs font-medium block">
                                                Reason <span class="text-red-500">*</span>
                                            </label>
                                            <button type="button" id="openDeclineReasonModal" 
                                                class="w-full p-2 border border-gray-300 rounded-md text-sm bg-white text-left text-gray-500 hover:bg-gray-50"
                                                onclick="toggleModalEnhanced(true)">
                                                Click to specify decline reasons...
                                            </button>
                                            <input type="hidden" id="comments" name="comments">
                                            <p class="text-xs text-red-500 mt-1">Please provide detailed reasons for declining this application</p>
                                        </div>


                                        <hr class="my-4">

                                        <div class="flex justify-between items-center">
                                            <div class="flex gap-2">
                                                <a href="{{ route('sectionaltitling.primary') }}"
                                                    class="flex items-center px-3 py-1 text-xs border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                                                    <i data-lucide="undo-2" class="w-3.5 h-3.5 mr-1.5"></i>
                                                    Back
                                                </a>
                                                <button id="planningRecommendationSubmitBtn" type="submit"
                                                    class="flex items-center px-3 py-1 text-xs bg-green-700 text-white rounded-md hover:bg-gray-800">
                                                    <i data-lucide="send-horizontal" class="w-3.5 h-3.5 mr-1.5"></i>
                                                    Submit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
 
                        </div>
                    </div>






                    <!-- Final Bill Tab -->
                    <div id="final-tab" class="tab-content">


                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                            <div class="p-4 border-b">
                                <h3 class="text-sm font-medium">Planning Recommendation</h3>
                                <p class="text-xs text-gray-500"></p>
                            </div>
                            <input type="hidden" id="application_id" value="{{ $application->id }}">
                            <input type="hidden" name="fileno" value="{{ $application->fileno }}">
                            <div class="p-4 space-y-4">

                                @include('sub_actions.planning_recomm')
                                <hr class="my-4">

                                <div class="flex justify-between items-center">
                                    <div class="flex gap-2">
                                        <button onclick="window.history.back()"
                                            class="flex items-center px-3 py-1 text-xs border border-gray-300 rounded-md bg-white hover:bg-gray-50"
                                            onclick="window.history.back()">
                                            <i data-lucide="undo-2" class="w-3.5 h-3.5 mr-1.5"></i>
                                            Back
                                        </button>

                                       
                                        <!-- Fallback Print Link -->
                                         @if(request()->query('url') == 'recommendation')
                                        <a href="{{ url('sub-actions/planning-recommendation/print') }}/{{ $application->id }}?url=print"
                                            target="_blank"
                                            class="flex items-center px-3 py-1 text-xs bg-blue-700 text-white rounded-md hover:bg-blue-800">
                                            <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1.5"></i>
                                            Print 
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Decline Reason Modal -->
<div id="declineReasonModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 items-center justify-center z-50 hidden" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b flex justify-between items-center bg-red-50">
            <h3 class="text-lg font-medium text-red-800">Specify Decline Reasons</h3>
            <button id="closeDeclineModal" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="p-6 space-y-6">
            <div class="text-sm text-gray-600 mb-4 bg-yellow-50 p-4 rounded-md border border-yellow-200">
                <p class="font-medium text-yellow-800">Instructions:</p>
                <p>Please select applicable reasons for declining this application and provide specific details for each selected reason.</p>
            </div>
            
             <!-- 1. Accessibility Category - Simplified -->
            <div class="border rounded-md p-4 bg-gray-50 shadow-sm">
                <div class="flex items-start mb-3">
                    <input type="checkbox" id="accessibilityCheck" class="mt-1 decline-reason-check h-4 w-4" onclick="toggleDetails(this, 'accessibilityDetails')">
                    <div class="ml-3">
                        <label for="accessibilityCheck" class="font-medium text-gray-800 text-base">1. Accessibility Issues</label>
                        <p class="text-sm text-gray-600">The property/site must have adequate accessibility to ensure ease of movement and compliance with urban planning standards.</p>
                    </div>
                </div>
                
                <div class="ml-8 mt-3 decline-reason-details bg-white p-4 rounded-md border" id="accessibilityDetails" style="display: none;">
                    <div class="mb-4">
                        <label for="accessibilitySpecificDetails" class="block text-sm font-medium text-gray-700 mb-1">Specific details about accessibility issues:</label>
                        <textarea id="accessibilitySpecificDetails" rows="3" placeholder="E.g., The property lacks direct access to an approved road network..." class="w-full p-2 border border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                    
                    <div>
                        <label for="accessibilityObstructions" class="block text-sm font-medium text-gray-700 mb-1">Obstructions or barriers to access (if any):</label>
                        <textarea id="accessibilityObstructions" rows="2" placeholder="Describe any physical barriers or obstructions..." class="w-full p-2 border border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                </div>
            </div>
            
            <!-- 2. Land Use Conformity Category - Simplified -->
            <div class="border rounded-md p-4 bg-gray-50 shadow-sm">
                <div class="flex items-start mb-3">
                    <input type="checkbox" id="conformityCheck" class="mt-1 decline-reason-check h-4 w-4" onclick="toggleDetails(this, 'conformityDetails')">
                    <div class="ml-3">
                        <label for="conformityCheck" class="font-medium text-gray-800 text-base">2. Land Use Conformity Issues</label>
                        <p class="text-sm text-gray-600">The property/site must conform to the existing land use designation of the area as per the Kano State Physical Development Plan.</p>
                    </div>
                </div>
                
                <div class="ml-8 mt-3 decline-reason-details bg-white p-4 rounded-md border" id="conformityDetails" style="display: none;">
                    <div class="mb-4">
                        <label for="landUseDetails" class="block text-sm font-medium text-gray-700 mb-1">Specific details about non-conformity:</label>
                        <textarea id="landUseDetails" rows="3" placeholder="E.g., The proposed use of the property conflicts with the designated residential zoning of the area..." class="w-full p-2 border border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                    
                    <div>
                        <label for="landUseDeviations" class="block text-sm font-medium text-gray-700 mb-1">Deviations from the approved land use plan:</label>
                        <textarea id="landUseDeviations" rows="2" placeholder="Describe any specific deviations from zoning or land use plans..." class="w-full p-2 border border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                </div>
            </div>
            
            <!-- 3. Utility Lines Category - Simplified -->
            <div class="border rounded-md p-4 bg-gray-50 shadow-sm">
                <div class="flex items-start mb-3">
                    <input type="checkbox" id="utilityCheck" class="mt-1 decline-reason-check h-4 w-4" onclick="toggleDetails(this, 'utilityDetails')">
                    <div class="ml-3">
                        <label for="utilityCheck" class="font-medium text-gray-800 text-base">3. Utility Line Interference</label>
                        <p class="text-sm text-gray-600">The property/site must not transverse or interfere with existing utility lines (e.g., electricity, water, sewage).</p>
                    </div>
                </div>
                
                <div class="ml-8 mt-3 decline-reason-details bg-white p-4 rounded-md border" id="utilityDetails" style="display: none;">
                    <div class="mb-4">
                        <label for="utilityIssueDetails" class="block text-sm font-medium text-gray-700 mb-1">Specific details about utility line issues:</label>
                        <textarea id="utilityIssueDetails" rows="3" placeholder="E.g., The property boundary overlaps with an existing high-voltage power line corridor..." class="w-full p-2 border border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                    
                    <div>
                        <label for="utilityTypeDetails" class="block text-sm font-medium text-gray-700 mb-1">Type of utility line affected and implications:</label>
                        <textarea id="utilityTypeDetails" rows="2" placeholder="Specify the utility type (electricity, water, sewage) and safety/access implications..." class="w-full p-2 border border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                </div>
            </div>
            
            <!-- 4. Road Reservation Category - Simplified -->
            <div class="border rounded-md p-4 bg-gray-50 shadow-sm">
                <div class="flex items-start mb-3">
                    <input type="checkbox" id="roadReservationCheck" class="mt-1 decline-reason-check h-4 w-4" onclick="toggleDetails(this, 'roadReservationDetails')">
                    <div class="ml-3">
                        <label for="roadReservationCheck" class="font-medium text-gray-800 text-base">4. Road Reservation Issues</label>
                        <p class="text-sm text-gray-600">The property/site must have an adequate access road or comply with minimum road reservation standards as stipulated in KNUPDA guidelines.</p>
                    </div>
                </div>
                
                <div class="ml-8 mt-3 decline-reason-details bg-white p-4 rounded-md border" id="roadReservationDetails" style="display: none;">
                    <div class="mb-4">
                        <label for="roadReservationIssues" class="block text-sm font-medium text-gray-700 mb-1">Specific details about road/reservation issues:</label>
                        <textarea id="roadReservationIssues" rows="3" placeholder="E.g., The property lacks a defined access road, and the surrounding road network is below the required width..." class="w-full p-2 border border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                    
                    <div>
                        <label for="roadMeasurements" class="block text-sm font-medium text-gray-700 mb-1">Measurements or observations related to deficiencies:</label>
                        <textarea id="roadMeasurements" rows="2" placeholder="Provide relevant measurements (required vs. actual) and observations..." class="w-full p-2 border border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-4 border-t flex justify-end bg-gray-50">
            <button type="button" id="cancelDeclineReasons" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 mr-2" onclick="toggleModal(false)">
                Cancel
            </button>
            <button type="button" id="saveDeclineReasons" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                Save Reasons
            </button>
            <button type="button" id="saveAndViewDeclineReasons" class="ml-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                Save & View Memo
            </button>
        </div>
    </div>
</div>
            <!-- Footer -->
            @include('admin.footer')
        </div>
      @include('sub_actions.parts.sub_recomm_js')
    @endsection
