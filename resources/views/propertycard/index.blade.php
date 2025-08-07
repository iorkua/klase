@extends('layouts.app')
@section('page-title')
    {{ __('Property Record Assistant') }}
@endsection

@section('content')
@include('propertycard.css.style')
    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        @include('admin.header')
        <!-- Dashboard Content -->
        <div class="p-6">
            <div class="container mx-auto py-6 space-y-6">
                <!-- Page Header -->
                {{-- <div class="flex flex-col space-y-2">
                    <h1 class="text-3xl font-bold tracking-tight">Property Record Assistant</h1>
                    <p class="text-gray-500">Capture and manage property records</p>
                </div> --}}
        
                <div class="flex items-center justify-end mb-4">
                    <label for="assistant-toggle" class="flex items-center cursor-pointer">
                        <span class="mr-3 text-gray-600">Manual Assistant</span>
                        <div class="assistant-toggle">
                            <input type="checkbox" id="assistant-toggle">
                            <span class="slider round"></span>
                        </div>
                        <span class="ml-3 text-gray-600">AI Assistant</span>
                    </label>
                </div>

                <!-- Manual Property Details Content -->
                <div id="manual-assistant">
                    @include('propertycard.partials.property_details')
                </div>

                <!-- AI Property Details Content -->
                <div id="ai-assistant" style="display: none;">
                    @include('propertycard.partials.ai.ai_property_record_assistant')
                </div>
           
            </div>
        
            <!-- Property Modal Dialogs -->
            @include('propertycard.partials.add_property_record', ['is_ai_assistant' => false])
            @include('propertycard.partials.edit_property_record')
            @include('propertycard.partials.view_property_record')
        </div>
        <!-- Footer -->
        @include('admin.footer')
    </div>
    
    <!-- Include JavaScript after all DOM elements -->
    @include('propertycard.js.javascript')
@endsection
@include('propertycard.partials.property_form_sweetalert')
