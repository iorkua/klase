@extends('layouts.app')
@section('page-title')
    {{ __('Recertification Application Form') }}
@endsection

@section('content')
<script>
// Tailwind config
tailwind.config = {
  theme: { 
    extend: {
      colors: {
        primary: '#3b82f6',
        'primary-foreground': '#ffffff',
        muted: '#f3f4f6',
        'muted-foreground': '#6b7280',
        border: '#e5e7eb',
        destructive: '#ef4444',
        'destructive-foreground': '#ffffff',
        secondary: '#f1f5f9',
        'secondary-foreground': '#0f172a',
      }
    }
  }
}
</script>

@include('recertification.css.form_css')

<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include('admin.header')
    
    <!-- Main Content -->
    <div class="p-6">
        <div class="container mx-auto py-6 space-y-6 max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <!-- Header with Back Button -->
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ url('/recertification') }}" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-2">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Back to Applications
                </a>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900">New Recertification Application</h1>
                    <p class="text-gray-600">Complete the form below to submit a new recertification application</p>
                </div>
                <!-- File Number Display -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
                    <div class="text-xs font-medium text-blue-600 uppercase tracking-wide mb-1">Application File Number</div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="h-5 w-5 text-blue-600"></i>
                        <span id="file-number-display" class="text-lg font-bold text-blue-900 font-mono">Loading...</span>
                    </div>
                    <div class="text-xs text-blue-500 mt-1">Auto-generated</div>
                </div>
            </div>

            <!-- Development Controls -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="settings" class="h-5 w-5 text-yellow-600"></i>
                    <h3 class="font-semibold text-yellow-800">Development Controls</h3>
                </div>
                <div class="flex gap-4 items-center">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="dev-skip-validation" class="rounded" checked>
                        <span class="text-sm text-yellow-700">Skip Validation</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="dev-auto-fill" class="rounded">
                        <span class="text-sm text-yellow-700">Auto-fill Sample Data</span>
                    </label>
                    <button id="dev-debug-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-yellow-600 text-white hover:bg-yellow-700 gap-1">
                        <i data-lucide="bug" class="h-3 w-3"></i>
                        Debug
                    </button>
                    <button id="dev-reset-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-red-600 text-white hover:bg-red-700 gap-1">
                        <i data-lucide="refresh-cw" class="h-3 w-3"></i>
                        Reset
                    </button>
                </div>
            </div>

            <!-- Application Form -->
            <div class="bg-white rounded-lg shadow-xl border border-gray-200">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="text-center">
                        <div class="space-y-1">
                            <div class="font-bold text-lg">KANO STATE GEOGRAPHIC INFORMATION SYSTEMS (KANGIS)</div>
                            <div class="text-sm text-gray-600">MINISTRY OF LAND AND PHYSICAL PLANNING KANO STATE</div>
                            <div class="text-sm font-semibold">APPLICATION FOR RE-CERTIFICATION OR RE-ISSUANCE OF C-of-O</div>
                            <div class="text-xs text-gray-500">INDIVIDUAL FORM AR01-01</div>
                        </div>
                    </div>
                </div>
                
                <!-- Step Indicator -->
                <div class="p-6 pb-0">
                    <div class="step-indicator">
                        <div id="step-1" class="step-circle active">1</div>
                        <div id="line-1" class="step-line inactive"></div>
                        <div id="step-2" class="step-circle inactive">2</div>
                        <div id="line-2" class="step-line inactive"></div>
                        <div id="step-3" class="step-circle inactive">3</div>
                        <div id="line-3" class="step-line inactive"></div>
                        <div id="step-4" class="step-circle inactive">4</div>
                        <div id="line-4" class="step-line inactive"></div>
                        <div id="step-5" class="step-circle inactive">5</div>
                        <div id="line-5" class="step-line inactive"></div>
                        <div id="step-6" class="step-circle inactive">6</div>
                    </div>
                </div>
                 
                <div class="p-6">
                    <form id="recertification-form" method="POST" action="{{ route('recertification.application.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Include Step Partials -->
                        @include('recertification.steps.step1_personal_details')
                        @include('recertification.steps.step2_contact_details')
                        @include('recertification.steps.step3_title_holder')
                        @include('recertification.steps.step4_mortgage_encumbrance')
                        @include('recertification.steps.step5_plot_details')
                        @include('recertification.steps.step6_payment_terms')
                        
                    </form>

                    <!-- Form Navigation -->
                    <div class="flex justify-between pt-4 border-t">
                        <button
                            type="button"
                            id="prev-btn"
                            class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        
                        <button
                            type="button"
                            id="next-btn"
                            class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="next-text">Next</span>
                            <div class="loading-spinner hidden"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    @include('admin.footer')
</div>

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
    <!-- Toast messages will be inserted here -->
</div>

@include('recertification.js.standalone_form_js')

@endsection