@extends('layouts.app')
@section('page-title')
    {{ __('File Indexing') }}
@endsection

@include('sectionaltitling.partials.assets.css')

@section('content')
<style>
    /* Modern File Indexing UI Styles */
    .main-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1.5rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.25);
        position: relative;
        overflow: hidden;
    }
    
    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }
    
    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .page-subtitle {
        font-size: 1.125rem;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }
    
    .breadcrumb-nav {
        background: white;
        border-radius: 1rem;
        padding: 1rem 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }
    
    .breadcrumb-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .breadcrumb-link:hover {
        color: #5a67d8;
        text-decoration: underline;
    }
    
    .app-info-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .app-info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #48bb78, #38a169, #2f855a);
    }
    
    .app-info-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .app-info-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }
    
    .step-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .form-card {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
        padding: 2rem;
    }
    
    .form-section {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e0;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .section-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem;
        border-radius: 0.75rem;
        margin-right: 1rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .required {
        color: #ef4444;
        margin-left: 0.25rem;
    }
    
    .form-input, .form-select {
        width: 100%;
        padding: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        font-size: 0.875rem;
        background: white;
        text-transform: uppercase;
    }
    
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }
    
    .form-input[readonly] {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        color: #6b7280;
        cursor: not-allowed;
        border-color: #d1d5db;
    }
    
    .form-help {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.5rem;
        font-style: italic;
    }
    
    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .checkbox-item {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .checkbox-item:hover {
        border-color: #667eea;
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
    }
    
    .checkbox-item.checked {
        border-color: #667eea;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    
    .checkbox-item input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    
    .checkbox-custom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.5rem;
        border: 2px solid #d1d5db;
        border-radius: 0.375rem;
        margin-right: 0.75rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .checkbox-custom .check-icon {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .checkbox-item.checked .checkbox-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
    }
    
    .checkbox-item.checked .checkbox-custom .check-icon {
        opacity: 1;
    }
    
    .checkbox-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    
    .properties-section {
        background: linear-gradient(135deg, #f0fff4 0%, #dcfce7 100%);
        border: 2px solid #bbf7d0;
        border-radius: 1rem;
        padding: 2rem;
        margin-top: 2rem;
    }
    
    .properties-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .properties-icon {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 0.75rem;
        border-radius: 0.75rem;
        margin-right: 1rem;
        box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
    }
    
    .properties-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #065f46;
        margin: 0;
    }
    
    .action-bar {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .btn {
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
    }
    
    .btn-outline {
        border: 2px solid #e5e7eb;
        color: #374151;
        background-color: white;
    }
    
    .btn-outline:hover {
        background-color: #f9fafb;
        border-color: #667eea;
        color: #667eea;
    }
    
    .help-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 2px solid #bfdbfe;
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .help-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .help-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 0.75rem;
        border-radius: 0.75rem;
        margin-right: 1rem;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }
    
    .help-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e40af;
        margin: 0;
    }
    
    .help-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .help-list li {
        color: #1e40af;
        margin-bottom: 0.5rem;
        padding-left: 1.5rem;
        position: relative;
    }
    
    .help-list li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #059669;
        font-weight: bold;
    }
    
    .error-message {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border: 2px solid #fecaca;
        color: #dc2626;
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        margin-top: 0.5rem;
        font-weight: 600;
    }
    
    .loading {
        opacity: 0.6;
        pointer-events: none;
        position: relative;
    }
    
    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 24px;
        height: 24px;
        margin: -12px 0 0 -12px;
        border: 3px solid #667eea;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .main-container {
            padding: 1rem;
        }
        
        .page-header {
            padding: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            padding: 1.5rem;
        }
        
        .checkbox-grid {
            grid-template-columns: 1fr;
        }
        
        .action-bar {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        
        .app-info-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
    }
</style>

 @php 
    // Get property description from various sources
    $propertyDescription = '';

    // Try to get from existing property_description field
    if (!empty($fileIndexing->property_description)) {
        $propertyDescription = $fileIndexing->property_description;
    } else {
        // Build property description from address components
        $addressParts = [];
        $mainApplication = null;

        // Check if this is a unit (subapplication) or main application
        if (!empty($fileIndexing->subapplication_id)) {
            // This is a unit - get subapplication first, then get main application
            try {
                $subApplication = DB::connection('sqlsrv')
                    ->table('subapplications')
                    ->where('id', $fileIndexing->subapplication_id)
                    ->first();

                if ($subApplication && !empty($subApplication->main_application_id)) {
                    $mainApplication = DB::connection('sqlsrv')
                        ->table('applications')
                        ->where('id', $subApplication->main_application_id)
                        ->first();
                }
            } catch (Exception $e) {
                // If subapplication lookup fails, try using main_application_id directly
                try {
                    $mainApplication = DB::connection('sqlsrv')
                        ->table('applications')
                        ->where('id', $fileIndexing->main_application_id)
                        ->first();
                } catch (Exception $e2) {
                    // Final fallback - do nothing
                }
            }
        } else {
            // This is a main application - get directly from applications table
            try {
                $mainApplication = DB::connection('sqlsrv')
                    ->table('applications')
                    ->where('id', $fileIndexing->main_application_id)
                    ->first();
            } catch (Exception $e) {
                // Fallback - do nothing
            }
        }

        // Build address from main application data
        if ($mainApplication) {
            if (!empty($mainApplication->property_plot_no)) $addressParts[] = $mainApplication->property_plot_no;
            if (!empty($mainApplication->property_street_name)) $addressParts[] = $mainApplication->property_street_name;
            if (!empty($mainApplication->property_district)) $addressParts[] = $mainApplication->property_district;
            if (!empty($mainApplication->property_lga)) $addressParts[] = $mainApplication->property_lga;
            if (!empty($mainApplication->property_state)) $addressParts[] = $mainApplication->property_state;
        }

        // If no main application data found, use file indexing data as fallback
        if (empty($addressParts)) {
            if (!empty($fileIndexing->plot_number)) $addressParts[] = 'Plot ' . $fileIndexing->plot_number;
            if (!empty($fileIndexing->district)) $addressParts[] = $fileIndexing->district;
            if (!empty($fileIndexing->lga)) $addressParts[] = $fileIndexing->lga;
        }

        // Join address parts with commas
        if (!empty($addressParts)) {
            $propertyDescription = implode(', ', array_filter($addressParts));
        } else {
            $propertyDescription = 'Property description will be auto-generated from application data';
        }
    }
@endphp


<!-- Main Content -->
<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include('admin.header')
    
    <!-- Dashboard Content -->
    <div class="main-container">
        <!-- Page Header -->
          <div class="page-header">
            <h1 class="page-title">File Indexing</h1>
            <p class="page-subtitle">Create and manage digital file index with comprehensive metadata</p>
        </div>  

        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="flex items-center space-x-2">
                <a href="{{ route('edms.index', ['applicationId' => $fileIndexing->main_application_id]) }}" class="breadcrumb-link">
                    <i data-lucide="workflow" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                    EDMS Workflow
                </a>
                <i data-lucide="chevron-right" style="width: 1rem; height: 1rem; color: #9ca3af;"></i>
                <span style="color: #6b7280; font-weight: 600;">File Indexing</span>
            </div>
        </nav>

        <!-- Application Info -->
        <div class="app-info-card">
            <div class="app-info-header">
                <div>
                    <h3 class="app-info-title">File Number</h3>
                    <p style="color: #6b7280; margin: 0.5rem 0 0 0; font-weight: 500;">{{ $fileIndexing->file_number }}</p>
                </div>
                <span class="step-badge">Step 1 of 3</span>
            </div>
        </div>

        <!-- File Indexing Form -->
        <div class="form-card">
            <form action="{{ route('edms.update-file-indexing', $fileIndexing->id) }}" method="POST" id="fileIndexingForm">
                @csrf
                @method('PUT')
                
                <div class="form-grid">
                    <!-- File Identification Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i data-lucide="file-text" style="width: 1.25rem; height: 1.25rem;"></i>
                            </div>
                            <h3 class="section-title">File Identification</h3>
                        </div>
                        
                        <div class="form-group">
                            <label for="file_number" class="form-label">File Number</label>
                            <input type="text" id="file_number" name="file_number" class="form-input" 
                                   value="{{ old('file_number', $fileIndexing->file_number) }}" readonly>
                            <p class="form-help">Auto-generated from application data</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="file_title" class="form-label">File Title</label>
                            <input type="text" id="file_title" name="file_title" class="form-input" 
                                   value="{{ old('file_title', $fileIndexing->file_title) }}" readonly>
                            <p class="form-help">Auto-populated from application data</p>
                            @error('file_title')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="land_use_type" class="form-label">Land Use Type</label>
                            <input type="text" id="land_use_type" name="land_use_type" class="form-input" 
                                   value="{{ old('land_use_type', $fileIndexing->land_use_type) }}" readonly>
                            <p class="form-help">Auto-populated from application data</p>
                            @error('land_use_type')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Property Details Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i data-lucide="map-pin" style="width: 1.25rem; height: 1.25rem;"></i>
                            </div>
                            <h3 class="section-title">Property Details</h3>
                        </div>
                        
                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" id="plot_number" name="plot_number" value="{{ old('plot_number', $fileIndexing->plot_number) }}">
                        <input type="hidden" id="district" name="district" value="{{ old('district', $fileIndexing->district) }}">
                        <input type="hidden" id="lga" name="lga" value="{{ old('lga', $fileIndexing->lga) }}">
                        
                        <div class="form-group">
                            <label for="property_description" class="form-label">Property Description</label>
                            <textarea id="property_description" name="property_description" class="form-input" 
                                      rows="4" readonly style="resize: none; height: auto; min-height: 120px;">{{ old('property_description', $propertyDescription) }}</textarea>
                            <p class="form-help">Auto-generated from Plot No, Street Name, District, LGA, and State (same for units and main applications)</p>
                            @error('property_description')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- File Properties Section -->
                <div class="properties-section">
                    <div class="properties-header">
                        <div class="properties-icon">
                            <i data-lucide="settings" style="width: 1.25rem; height: 1.25rem;"></i>
                        </div>
                        <h3 class="properties-title">File Properties & Attributes</h3>
                    </div>
                    
                    <div class="checkbox-grid">
                        <div class="checkbox-item" onclick="toggleCheckbox('has_cofo')">
                            <input type="checkbox" id="has_cofo" name="has_cofo" value="1" 
                                   {{ old('has_cofo', $fileIndexing->has_cofo) == 1 ? 'checked' : '' }}>
                            <label for="has_cofo" class="checkbox-label">
                                <div class="checkbox-custom">
                                    <i data-lucide="check" class="check-icon" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                Has Certificate of Occupancy
                            </label>
                        </div>
                        
                        <div class="checkbox-item" onclick="toggleCheckbox('is_merged')">
                            <input type="checkbox" id="is_merged" name="is_merged" value="1" 
                                   {{ old('is_merged', $fileIndexing->is_merged) == 1 ? 'checked' : '' }}>
                            <label for="is_merged" class="checkbox-label">
                                <div class="checkbox-custom">
                                    <i data-lucide="check" class="check-icon" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                Merged Plot
                            </label>
                        </div>
                        
                        <div class="checkbox-item" onclick="toggleCheckbox('has_transaction')">
                            <input type="checkbox" id="has_transaction" name="has_transaction" value="1" 
                                   {{ old('has_transaction', $fileIndexing->has_transaction) == 1 ? 'checked' : '' }}>
                            <label for="has_transaction" class="checkbox-label">
                                <div class="checkbox-custom">
                                    <i data-lucide="check" class="check-icon" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                Has Transaction History
                            </label>
                        </div>
                        
                        <div class="checkbox-item" onclick="toggleCheckbox('is_problematic')">
                            <input type="checkbox" id="is_problematic" name="is_problematic" value="1" 
                                   {{ old('is_problematic', $fileIndexing->is_problematic) == 1 ? 'checked' : '' }}>
                            <label for="is_problematic" class="checkbox-label">
                                <div class="checkbox-custom">
                                    <i data-lucide="check" class="check-icon" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                Problematic File
                            </label>
                        </div>
                        
                        <div class="checkbox-item" onclick="toggleCheckbox('is_co_owned_plot')">
                            <input type="checkbox" id="is_co_owned_plot" name="is_co_owned_plot" value="1" 
                                   {{ old('is_co_owned_plot', $fileIndexing->is_co_owned_plot ?? false) == 1 ? 'checked' : '' }}>
                            <label for="is_co_owned_plot" class="checkbox-label">
                                <div class="checkbox-custom">
                                    <i data-lucide="check" class="check-icon" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                Co-Owned Plot
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            @if($fileIndexing->is_unit_own ?? false)
                <a href="{{ route('edms.sub', $fileIndexing->main_application_id) }}" class="btn btn-outline">
                    <i data-lucide="arrow-left" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                    Back to Workflow
                </a>
            @else
                <a href="{{ route('edms.index', ['applicationId' => $fileIndexing->main_application_id]) }}" class="btn btn-outline">
                    <i data-lucide="arrow-left" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                    Back to Workflow
                </a>
            @endif
            
            <button type="submit" form="fileIndexingForm" class="btn btn-primary" id="submitBtn">
                <i data-lucide="save" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                Save & Continue to Scanning
            </button>
        </div>

        <!-- Help Section -->
        <div class="help-card">
            <div class="help-header">
                <div class="help-icon">
                    <i data-lucide="help-circle" style="width: 1.25rem; height: 1.25rem;"></i>
                </div>
                <h4 class="help-title">File Indexing Guidelines</h4>
            </div>
            <ul class="help-list">
                <li>Ensure the file title is descriptive and includes the applicant's name</li>
                <li>Select the correct land use type as it affects processing requirements</li>
                <li>Check all applicable file properties to ensure proper categorization</li>
                <li>Verify plot number and location details for accuracy</li>
                <li>All required fields must be completed before proceeding</li>
                <li>Use clear and consistent naming conventions for better organization</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    @include('admin.footer')
</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
    lucide.createIcons();
    
    // Checkbox toggle functionality
    function toggleCheckbox(checkboxId) {
        const checkbox = document.getElementById(checkboxId);
        const container = checkbox.closest('.checkbox-item');
        
        checkbox.checked = !checkbox.checked;
        
        if (checkbox.checked) {
            container.classList.add('checked');
        } else {
            container.classList.remove('checked');
        }
    }
    
    // Initialize checkbox states
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.checkbox-item input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            const container = checkbox.closest('.checkbox-item');
            if (checkbox.checked) {
                container.classList.add('checked');
            }
        });
    });
    
    // Form submission handling
    document.getElementById('fileIndexingForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        const form = this;
        
        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = '#ef4444';
                field.focus();
            } else {
                field.style.borderColor = '#e5e7eb';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" style="width: 1rem; height: 1rem; margin-right: 0.5rem; animation: spin 1s linear infinite;"></i>Saving...';
        
        // Re-enable button after 5 seconds to prevent permanent disable
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i data-lucide="save" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>Save & Continue to Scanning';
                lucide.createIcons();
            }
        }, 5000);
    });
    
    // Auto-save functionality
    let autoSaveTimeout;
    const formInputs = document.querySelectorAll('#fileIndexingForm input, #fileIndexingForm select');
    
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                console.log('Auto-saving form data...');
                // Auto-save logic can be implemented here
            }, 2000);
        });
    });
    
    // Enhanced form interactions
    const formElements = document.querySelectorAll('.form-input, .form-select');
    formElements.forEach(element => {
        element.addEventListener('focus', function() {
            this.closest('.form-section').style.transform = 'translateY(-4px)';
            this.closest('.form-section').style.boxShadow = '0 12px 35px rgba(0, 0, 0, 0.15)';
        });
        
        element.addEventListener('blur', function() {
            this.closest('.form-section').style.transform = 'translateY(-2px)';
            this.closest('.form-section').style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
        });
    });
</script>
@endsection 