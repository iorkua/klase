@extends('layouts.app')
@section('page-title')
    {{ __('Planning Recommendation') }}
@endsection



@section('content')
@include('sectionaltitling.partials.assets.css')
@include('actions.parts.recomm_css')
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
                        <h2 class="text-lg font-medium">
                            Planning Recommendation 
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusClass }}">
                                <i data-lucide="{{ $statusIcon }}" class="w-3 h-3 mr-1"></i>
                                {{ $application->planning_recommendation_status }}
                            </span>
                        </h2>
                        <button onclick="window.history.back()" class="text-gray-500 hover:text-gray-700">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <div class="text-sm mb-4">
                        Approval Date:
                        <span class="font-medium">
                            {{ $application->planning_approval_date }}
                        </span>
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
                        <div class="grid grid-cols-5 gap-2 mb-4">
                            <button class="tab-button active" data-tab="detterment">
                                <i data-lucide="calculator" class="w-3.5 h-3.5 mr-1.5"></i>
                                Architectural Design
                            </button>

                            <button class="tab-button" data-tab="survey-plan">
                                <i data-lucide="map" class="w-3.5 h-3.5 mr-1.5"></i>
                                View Survey Plan
                            </button>

                            <button class="tab-button" data-tab="planning-form">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5 mr-1.5"></i>
                            Complete survey data<br>
                            for Planning Recommendation Report
                            </button>

                            @if (request()->query('url') == 'phy_planning')
                                <button class="tab-button" data-tab="initial">
                                    <i data-lucide="banknote" class="w-3.5 h-3.5 mr-1.5"></i>
                                    Planning Recommendation Approval
                                </button>
                            @endif

                            <button class="tab-button" data-tab="final" id="planningRecommendationTab" disabled>
                                <i data-lucide="file-check" class="w-3.5 h-3.5 mr-1.5"></i>
                                Planning Recommendation Report
                            </button>
                        </div>


                        @include('actions.architecturaldesign')

                        <!-- View Survey Plan Tab -->
                        <div id="survey-plan-tab" class="tab-content">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="p-4 border-b bg-green-50">
                                    <h3 class="text-lg font-medium text-green-800">🗺️ View Survey Plan</h3>
                                    <p class="text-sm text-green-600 mt-1">View the uploaded survey plan document for this application.</p>
                                </div>
                                
                                <div class="p-6">
                                    @php
                                        $documents = null;
                                        $surveyPlan = null;
                                        
                                        if (!empty($application->documents)) {
                                            $documents = json_decode($application->documents, true);
                                            if (is_array($documents) && isset($documents['survey_plan'])) {
                                                $surveyPlan = $documents['survey_plan'];
                                            }
                                        }
                                    @endphp
                                    
                                    @if ($surveyPlan && isset($surveyPlan['path']))
                                        <div class="bg-gray-50 rounded-lg p-4 border">
                                            <div class="flex items-center justify-between mb-4">
                                                <div>
                                                    <h4 class="font-semibold text-gray-800 flex items-center">
                                                        <i data-lucide="file-image" class="w-4 h-4 mr-2"></i>
                                                        Survey Plan Document
                                                    </h4>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        <strong>Original Name:</strong> {{ $surveyPlan['original_name'] ?? 'N/A' }}<br>
                                                        <strong>File Type:</strong> {{ strtoupper($surveyPlan['type'] ?? 'Unknown') }}<br>
                                                        <strong>Uploaded:</strong> {{ $surveyPlan['uploaded_at'] ?? 'N/A' }}
                                                    </p>
                                                </div>
                                                <div class="flex gap-2">
                                                    <a href="{{ asset('storage/' . $surveyPlan['path']) }}" 
                                                       target="_blank"
                                                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                                                        <i data-lucide="external-link" class="w-4 h-4 mr-1"></i>
                                                        Open in New Tab
                                                    </a>
                                                    <a href="{{ asset('storage/' . $surveyPlan['path']) }}" 
                                                       download="{{ $surveyPlan['original_name'] ?? 'survey_plan' }}"
                                                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                                                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            <!-- Document Preview -->
                                            <div class="bg-white rounded-lg border p-4">
                                                <h5 class="font-medium text-gray-800 mb-3">Document Preview</h5>
                                                <div class="flex justify-center">
                                                    @if (in_array(strtolower($surveyPlan['type'] ?? ''), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                        <img src="{{ asset('storage/' . $surveyPlan['path']) }}" 
                                                             alt="Survey Plan" 
                                                             class="max-w-full max-h-96 object-contain rounded-lg shadow-md border">
                                                    @elseif (strtolower($surveyPlan['type'] ?? '') === 'pdf')
                                                        <div class="w-full">
                                                            <iframe src="{{ asset('storage/' . $surveyPlan['path']) }}" 
                                                                    class="w-full h-96 border rounded-lg"
                                                                    frameborder="0">
                                                                <p>Your browser does not support PDFs. 
                                                                   <a href="{{ asset('storage/' . $surveyPlan['path']) }}" target="_blank">Click here to view the PDF</a>
                                                                </p>
                                                            </iframe>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-8">
                                                            <i data-lucide="file" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                                                            <p class="text-gray-600">Preview not available for this file type.</p>
                                                            <p class="text-sm text-gray-500 mt-2">Please download the file to view it.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-12">
                                            <i data-lucide="map-pin-off" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                                            <h4 class="text-lg font-medium text-gray-800 mb-2">No Survey Plan Available</h4>
                                            <p class="text-gray-600 mb-4">No survey plan document has been uploaded for this application.</p>
                                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-md mx-auto">
                                                <p class="text-sm text-yellow-800">
                                                    <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                                    Please ensure the survey plan is uploaded in the application documents.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Complete Application Data Tab -->
                        <div id="planning-form-tab" class="tab-content">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="p-4 border-b bg-blue-50">
                                    <h3 class="text-lg font-medium text-blue-800">📋 Complete Application Data</h3>
                                    <p class="text-sm text-blue-600 mt-1">Fill in all required information before generating the Physical Planning Report. Fields marked with N/A need to be completed.</p>
                                </div>
                                
                                <form id="applicationDataForm" class="p-6 space-y-6">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="application_id" value="{{ $application->id }}">
                                    
                                    <!-- Application Information Section -->
                                    <div class="bg-gray-50 rounded-lg p-4 border">
                                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                                            <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                            Application Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    LKN Number <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" name="lkn_number" id="lkn_number" 
                                                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                       value="{{ $surveyRecord->tp_plan_no ?? '' }}" 
                                                       placeholder="Enter LKN Number">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    TP Plan Number <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" name="tp_plan_number" id="tp_plan_number" 
                                                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                       value="{{ $surveyRecord->tp_plan_no ?? '' }}" 
                                                       placeholder="Enter TP Plan Number">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Approved Plan Number <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" name="approved_plan_number" id="approved_plan_number" 
                                                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                       value="{{ $surveyRecord->approved_plan_no ?? '' }}" 
                                                       placeholder="Enter Approved Plan Number">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Validation Status Section -->
                                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                        <h4 class="font-semibold text-yellow-800 mb-4 flex items-center">
                                            <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                                            Completion Status
                                        </h4>
                                        <div class="bg-white rounded-lg p-4 border">
                                            <div id="validationStatus" class="space-y-2">
                                                <div class="flex items-center text-sm">
                                                    <span id="lknStatus" class="w-4 h-4 mr-2">❌</span>
                                                    <span>LKN Number</span>
                                                </div>
                                                <div class="flex items-center text-sm">
                                                    <span id="tpStatus" class="w-4 h-4 mr-2">❌</span>
                                                    <span>TP Plan Number</span>
                                                </div>
                                                <div class="flex items-center text-sm">
                                                    <span id="approvedStatus" class="w-4 h-4 mr-2">❌</span>
                                                    <span>Approved Plan Number</span>
                                                </div>
                                            </div>
                                            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg" id="completionMessage">
                                                <p class="text-sm text-red-700">
                                                    <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                                    Complete all required fields above to unlock the Planning Recommendation Report tab.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="flex justify-between items-center pt-4 border-t">
                                        <div class="text-sm text-gray-600">
                                            <span class="text-red-500">*</span> Required fields
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button" onclick="window.history.back()" 
                                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center">
                                                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                                                Back
                                            </button>
                                            <button type="submit" id="saveApplicationDataBtn" 
                                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                                                <i data-lucide="save" class="w-4 h-4 mr-1"></i>
                                                Save Application Data
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="initial-tab" class="tab-content">
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
                                                            value="approve"
                                                            class="form-radio"
                                                            onchange="toggleObservationsAndReasonContainers(this)"
                                                            @if(strtolower($application->planning_recommendation_status ?? '') === 'approve' || strtolower($application->planning_recommendation_status ?? '') === 'approved') checked @endif
                                                            @if(strtolower($application->planning_recommendation_status ?? '') === 'approve' || strtolower($application->planning_recommendation_status ?? '') === 'approved') disabled @endif
                                                        >
                                                        <span class="ml-2 text-sm @if(strtolower($application->planning_recommendation_status ?? '') === 'approve' || strtolower($application->planning_recommendation_status ?? '') === 'approved') text-gray-400 @endif">Approve</span>
                                                    </label>
                                                    <label class="inline-flex items-center">
                                                        <input 
                                                            type="radio" 
                                                            name="decision" 
                                                            value="decline"
                                                            class="form-radio"
                                                            onchange="toggleObservationsAndReasonContainers(this)"
                                                            @if(strtolower($application->planning_recommendation_status ?? '') === 'decline' || strtolower($application->planning_recommendation_status ?? '') === 'declined') checked @endif
                                                            @if(strtolower($application->planning_recommendation_status ?? '') === 'approve' || strtolower($application->planning_recommendation_status ?? '') === 'approved') disabled @endif
                                                        >
                                                        <span class="ml-2 text-sm @if(strtolower($application->planning_recommendation_status ?? '') === 'approve' || strtolower($application->planning_recommendation_status ?? '') === 'approved') text-gray-400 @endif">Decline</span>
                                                    </label>

                                                    <script>
                                                        function toggleObservationsAndReasonContainers(radio) {
                                                            const reasonContainer = document.getElementById('reasonContainer');
                                                            const observationsContainer = document.getElementById('observationsContainer');
                                                            
                                                            // Only show reason container when declining
                                                            reasonContainer.style.display = (radio.value === 'decline') ? 'block' : 'none';
                                                            
                                                            // Only show observations container when approving
                                                            if (observationsContainer) {
                                                                observationsContainer.style.display = (radio.value === 'approve') ? 'block' : 'none';
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

                                        <!-- Additional Observations Section -->
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
                                                <button type="button" onclick="window.history.back()"
                                                    class="flex items-center px-3 py-1 text-xs border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                                                    <i data-lucide="undo-2" class="w-3.5 h-3.5 mr-1.5"></i>
                                                    Back
                                                </button>
                                                <button id="planningRecommendationSubmitBtn" type="submit"
                                                    class="flex items-center px-3 py-1 text-xs rounded-md
                                                        @if(strtolower($application->planning_recommendation_status ?? '') === 'approve' || strtolower($application->planning_recommendation_status ?? '') === 'approved')
                                                            bg-gray-400 text-gray-200 cursor-not-allowed
                                                        @else
                                                            bg-green-700 text-white hover:bg-gray-800
                                                        @endif"
                                                    @if(strtolower($application->planning_recommendation_status ?? '') === 'approve' || strtolower($application->planning_recommendation_status ?? '') === 'approved')
                                                        disabled
                                                    @endif
                                                >
                                                    <i data-lucide="send-horizontal" class="w-3.5 h-3.5 mr-1.5"></i>
                                                    Submit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>


                        </div>

                        <!-- Final Bill Tab -->
                        <div id="final-tab" class="tab-content">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="p-4 border-b">
                                    <h3 class="text-sm font-medium">Planning Recommendation Report</h3>
                                    <p class="text-xs text-gray-500"></p>
                                </div>
                                <input type="hidden" id="application_id" value="{{ $application->id }}">
                                <input type="hidden" name="fileno" value="{{ $application->fileno }}">
                                <div class="p-4 space-y-4">

                                    @include('actions.planning_recomm')
                                    <hr class="my-4">

                                    <div class="flex justify-between items-center">
                                        <div class="flex gap-2">
                                            <button onclick="window.history.back()"
                                                class="flex items-center px-3 py-1 text-xs border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                                                <i data-lucide="undo-2" class="w-3.5 h-3.5 mr-1.5"></i>
                                                Back
                                            </button>

                                            <!-- Fallback Print Link -->
                                             @if(request()->query('url') == 'recommendation')
                                            @if(strtolower($application->planning_recommendation_status ?? '') == 'approve' || 
                                                strtolower($application->planning_recommendation_status ?? '') == 'approved')
                                                <a href="{{ url('planning-recommendation/print') }}/{{ $application->id }}?url=print"
                                                    target="_blank"
                                                    class="flex items-center px-3 py-1 text-xs bg-blue-700 text-white rounded-md hover:bg-blue-800">
                                                    <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1.5"></i>
                                                    Print
                                                </a>
                                            @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            @include('admin.footer')
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

@include('actions.parts.recomm_js')

<script>
// Application Data Form JavaScript with Validation
document.addEventListener('DOMContentLoaded', function() {
    // Get approval status from PHP
    const isApproved = '{{ strtolower($application->planning_recommendation_status ?? '') }}' === 'approved' || '{{ strtolower($application->planning_recommendation_status ?? '') }}' === 'approve';
    
    // Validation function
    function validateRequiredFields() {
        const lknNumber = document.getElementById('lkn_number').value.trim();
        const tpNumber = document.getElementById('tp_plan_number').value.trim();
        const approvedNumber = document.getElementById('approved_plan_number').value.trim();

        // Update status indicators
        document.getElementById('lknStatus').textContent = lknNumber ? '✅' : '❌';
        document.getElementById('tpStatus').textContent = tpNumber ? '✅' : '❌';
        document.getElementById('approvedStatus').textContent = approvedNumber ? '✅' : '❌';

        const allComplete = lknNumber && tpNumber && approvedNumber;
        
        // Enable Planning Recommendation Report tab only if approved
        const shouldEnableReportTab = isApproved;
        
        // Update completion message
        const completionMessage = document.getElementById('completionMessage');
        if (isApproved) {
            completionMessage.className = 'mt-4 p-3 bg-green-50 border border-green-200 rounded-lg';
            completionMessage.innerHTML = `
                <p class="text-sm text-green-700">
                    <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                    Application is approved! You can now access the Planning Recommendation Report tab.
                </p>
            `;
        } else {
            completionMessage.className = 'mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg';
            completionMessage.innerHTML = `
                <p class="text-sm text-yellow-700">
                    <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                    The Planning Recommendation Report tab will be enabled once the application is approved.
                </p>
            `;
        }

        // Enable/disable Planning Recommendation Report tab
        const planningTab = document.getElementById('planningRecommendationTab');
        if (planningTab) {
            if (shouldEnableReportTab) {
                planningTab.disabled = false;
                planningTab.classList.remove('opacity-50', 'cursor-not-allowed');
                planningTab.classList.add('cursor-pointer');
            } else {
                planningTab.disabled = true;
                planningTab.classList.add('opacity-50', 'cursor-not-allowed');
                planningTab.classList.remove('cursor-pointer');
            }
        }

        return allComplete;
    }

    // Add event listeners to required fields
    const requiredFields = ['lkn_number', 'tp_plan_number', 'approved_plan_number'];
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', validateRequiredFields);
            field.addEventListener('blur', validateRequiredFields);
        }
    });

    // Initial validation
    validateRequiredFields();

    // Prevent clicking on disabled Planning Recommendation Report tab
    document.addEventListener('click', function(e) {
        if (e.target.closest('#planningRecommendationTab') && e.target.closest('#planningRecommendationTab').disabled) {
            e.preventDefault();
            e.stopPropagation();
            
            // Show appropriate alert message
            if (isApproved) {
                alert('The Planning Recommendation Report tab should be enabled for approved applications. Please refresh the page.');
            } else {
                alert('The Planning Recommendation Report tab is only available for approved applications.');
            }
            
            return false;
        }
    });

    // Handle form submission
    document.getElementById('applicationDataForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // If application is approved, allow saving even with incomplete fields
        if (!isApproved && !validateRequiredFields()) {
            alert('Please fill in all required fields before saving.');
            return;
        }
        
        // Collect form data
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = document.getElementById('saveApplicationDataBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-1 animate-spin"></i>Saving...';
        submitBtn.disabled = true;
        
        // Send data to backend
        fetch('{{ route("sectionaltitling.saveApplicationData") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Application data saved successfully!');
                validateRequiredFields();
            } else {
                alert('Error saving data: ' + (data.error || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving data: ' + error.message);
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>

@endsection