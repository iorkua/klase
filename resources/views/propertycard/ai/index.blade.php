@extends('layouts.app')
@section('page-title')
    {{ __('AI Property Record Assistant') }}
@endsection

@section('content')
@include('propertycard.css.style')
<div class="flex-1 overflow-auto">
    @include('admin.header')
    <div class="p-6">
        <div class="container mx-auto py-6 space-y-6">
            <div class="flex items-center justify-end mb-4">
                <label for="assistant-toggle" class="flex items-center cursor-pointer">
                    <a href="{{ route('propertycard.index') }}" class="mr-3 text-gray-600">Manual Assistant</a>
                    <div class="assistant-toggle">
                        <input type="checkbox" id="assistant-toggle" checked>
                        <span class="slider round"></span>
                    </div>
                    <span class="ml-3 text-gray-600">AI Assistant</span>
                </label>
            </div>

            <div id="ai-assistant-page">
                @includeIf('propertycard.partials.ai.ai-property-record-assistant')
            </div>
        </div>

        <!-- Include the same Add New Property modal used by Manual Assistant -->
        
        @include('propertycard.partials.edit_property_record')
        @include('propertycard.partials.view_property_record')
    </div>
    @include('admin.footer')
</div>

<!-- Shared JS and SweetAlert handlers -->
@include('propertycard.js.javascript')
@include('propertycard.partials.property_form_sweetalert')
@endsection
