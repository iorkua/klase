@extends('layouts.app')
@section('page-title')
    {{ __('Page Typing') }}
@endsection

@include('sectionaltitling.partials.assets.css')

@section('content')
<style>
    /* Modern Card System */
    .main-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 1.5rem;
    }
    
    .workflow-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1.5rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        position: relative;
        overflow: hidden;
    }
    
    .workflow-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }
    
    .workflow-title {
        font-size: 2.25rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .workflow-subtitle {
        font-size: 1.2rem;
        opacity: 0.95;
        position: relative;
        z-index: 1;
        font-weight: 400;
    }
    
    .progress-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }
    
    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .progress-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
    }
    
    .progress-counter {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .progress-bar-container {
        background: #e2e8f0;
        height: 8px;
        border-radius: 1rem;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 100%;
        border-radius: 1rem;
        transition: width 0.5s ease;
        position: relative;
    }
    
    .progress-bar-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    /* Main Content Layout */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    @media (max-width: 1200px) {
        .content-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }
    
    /* Document Viewer Card */
    .viewer-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    
    .viewer-header {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .viewer-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .viewer-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
    }
    
    .nav-controls {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .nav-btn {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .nav-btn:hover:not(:disabled) {
        border-color: #667eea;
        background: #f7fafc;
        transform: translateY(-1px);
    }
    
    .nav-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .doc-counter {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .document-viewer {
        padding: 2rem;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
    }
    
    .document-viewer img {
        max-width: 100%;
        max-height: 500px;
        object-fit: contain;
        border-radius: 0.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .document-viewer iframe {
        width: 100%;
        height: 500px;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .viewer-placeholder {
        text-align: center;
        color: #718096;
    }
    
    .viewer-placeholder i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    /* Sidebar */
    .sidebar {
        gap: 1.5rem;
    }
    
    /* Document Thumbnails Card */
    .thumbnails-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        min-height: 400px;
        max-height: 600px;
        overflow-y: auto;
    }
    
    .thumbnails-header {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .thumbnails-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .thumbnails-grid {
        padding: 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
    }
    
    @media (max-width: 480px) {
        .thumbnails-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .document-thumbnail {
        aspect-ratio: 3/4;
        border: 3px solid #e2e8f0;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    
    .document-thumbnail:hover {
        border-color: #667eea;
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.25);
    }
    
    .document-thumbnail.active {
        border-color: #4f46e5;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        box-shadow: 0 12px 30px rgba(79, 70, 229, 0.3);
        transform: translateY(-2px);
    }
    
    .document-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0.5rem;
    }
    
    .thumbnail-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.6), transparent);
        color: white;
        padding: 1rem 0.75rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }
    
    .thumbnail-icon {
        font-size: 2.5rem;
        color: #667eea;
        margin-bottom: 0.75rem;
        opacity: 0.8;
    }
    
    /* PDF Thumbnail Specific Styles */
    .pdf-thumbnail-container {
        width: 100%;
        height: 100%;
        position: relative;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
    }
    
    .pdf-thumbnail-canvas {
        max-width: 100%;
        max-height: 100%;
        border-radius: 0.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: opacity 0.3s ease;
    }
    
    .pdf-thumbnail-fallback {
        text-align: center;
        color: #64748b;
        padding: 1rem;
    }
    
    .pdf-thumbnail-fallback i {
        width: 2.5rem;
        height: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.7;
        color: #667eea;
    }
    
    .pdf-thumbnail-loading {
        text-align: center;
        color: #667eea;
        padding: 1rem;
    }
    
    .pdf-thumbnail-loading .spinner {
        width: 1.5rem;
        height: 1.5rem;
        margin: 0 auto 0.75rem;
        border: 2px solid #e2e8f0;
        border-top: 2px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    /* Thumbnail Quality Indicator */
    .thumbnail-quality {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.625rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .thumbnail-quality.pdf {
        background: rgba(239, 68, 68, 0.9);
    }
    
    .thumbnail-quality.image {
        background: rgba(34, 197, 94, 0.9);
    }
    
    /* Classification Form Card */
    .classification-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 200px);
        max-height: 800px;
        min-height: 700px;
        position: sticky;
        top: 1rem;
        margin-bottom: 1rem;
    }
    
    .form-container {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        height: 100%;
        min-height: 400px;
    }
    
    .form-footer {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 1rem;
        border-top: 1px solid #e2e8f0;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 10;
        margin-top: auto;
    }
    
    .classification-header {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .classification-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }
    
    .classification-subtitle {
        font-size: 0.875rem;
        color: #718096;
        margin-top: 0.25rem;
    }

    .form-container {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 200px;
        max-height: 100%;
        padding: 1.5rem;
    }
    
    .page-form {
        transition: all 0.3s ease;
    }
    
    .page-form.active {
        opacity: 1;
        transform: translateY(0);
    }
    
    .page-form.hidden {
        display: none;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .required {
        color: #e53e3e;
    }
    
    .form-input, .form-select {
        width: 100%;
        padding: 0.875rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: white;
    }
    
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-input.error, .form-select.error {
        border-color: #e53e3e;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
    }
    
    .form-help {
        font-size: 0.75rem;
        color: #718096;
        margin-top: 0.25rem;
    }
    
    .form-footer {
        padding: 1.5rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
    
    .btn-save {
        width: 100%;
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        border: none;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .btn-save:hover:not(:disabled) {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
    }
    
    .btn-save:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }
    
    .btn-back {
        background: white;
        border: 2px solid #e2e8f0;
        color: #4a5568;
        border-radius: 0.75rem;
        padding: 0.875rem 1.5rem;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .btn-back:hover {
        border-color: #cbd5e0;
        background: #f7fafc;
        transform: translateY(-1px);
    }
    
    .status-text {
        color: #718096;
        font-size: 0.875rem;
    }
    
    /* Help Section */
    .help-card {
        background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
        border: 1px solid #90cdf4;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .help-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .help-icon {
        color: #3182ce;
        font-size: 1.25rem;
        margin-top: 0.125rem;
    }
    
    .help-content h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #2c5282;
        margin: 0 0 0.75rem 0;
    }
    
    .help-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .help-list li {
        color: #2c5282;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        padding-left: 1rem;
        position: relative;
    }
    
    .help-list li::before {
        content: '•';
        color: #3182ce;
        font-weight: bold;
        position: absolute;
        left: 0;
    }
    
    /* No Documents State */
    .no-documents {
        background: white;
        border-radius: 1rem;
        padding: 3rem;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }
    
    .no-documents-icon {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 1.5rem;
    }
    
    .no-documents h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
    }
    
    .no-documents p {
        color: #718096;
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 0.75rem;
        padding: 1rem 2rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    /* Breadcrumb */
    .breadcrumb {
        background: white;
        border-radius: 1rem;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }
    
    .breadcrumb-list {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .breadcrumb-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .breadcrumb-link {
        color: #4a5568;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }
    
    .breadcrumb-link:hover {
        color: #667eea;
    }
    
    .breadcrumb-separator {
        color: #cbd5e0;
    }
    
    .breadcrumb-current {
        color: #718096;
        font-weight: 500;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .main-container {
            padding: 1rem;
        }
        
        .workflow-header {
            padding: 1.5rem;
        }
        
        .workflow-title {
            font-size: 1.5rem;
        }
        
        .thumbnails-grid {
            grid-template-columns: 1fr;
        }
        
        .nav-controls {
            gap: 0.5rem;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
    }
    
    /* Loading States */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Page Status Indicators */
    .page-status {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .page-status.completed {
        background: #48bb78;
        color: white;
    }
    
    .page-status.in-progress {
        background: #ed8936;
        color: white;
    }
</style>

<!-- Main Content -->
<div class="flex-1 overflow-auto">
    <!-- Header -->
    @include('admin.header')
    
    <!-- Dashboard Content -->
    <div class="main-container">
        <!-- Workflow Header -->
        <div class="workflow-header">
            <h1 class="workflow-title">Page Typing & Classification</h1>
            <p class="workflow-subtitle">Classify each page of your scanned documents for efficient retrieval</p>
        </div>

        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item">
                    <a href="{{ route('edms.index', $fileIndexing->main_application_id) }}" class="breadcrumb-link">
                        EDMS Workflow
                    </a>
                </li>
                <li class="breadcrumb-separator">
                    <i data-lucide="chevron-right" style="width: 1rem; height: 1rem;"></i>
                </li>
                <li class="breadcrumb-item">
                    <span class="breadcrumb-current">Page Typing</span>
                </li>
            </ol>
        </nav>

        @php
            // Get all pages from all documents
            $allPages = [];
            $pageIndex = 0;
            
            foreach($fileIndexing->scannings as $docIndex => $scanning) {
                if(str_ends_with($scanning->document_path, '.pdf')) {
                    // For PDFs, try to get actual page count
                    $pdfInfo = app('App\Http\Controllers\EdmsController')->getPdfPageInfo($scanning->document_path);
                    $pageCount = $pdfInfo['page_count'] ?? 1;
                    
                    for($page = 1; $page <= $pageCount; $page++) {
                        $allPages[] = [
                            'type' => 'pdf_page',
                            'document_index' => $docIndex,
                            'page_number' => $page,
                            'file_path' => $scanning->document_path,
                            'display_name' => "Document " . ($docIndex + 1) . " - Page " . $page,
                            'page_index' => $pageIndex++,
                            'scanning_id' => $scanning->id
                        ];
                    }
                } else {
                    // For images, treat as single page
                    $allPages[] = [
                        'type' => 'image',
                        'document_index' => $docIndex,
                        'page_number' => 1,
                        'file_path' => $scanning->document_path,
                        'display_name' => "Document " . ($docIndex + 1),
                        'page_index' => $pageIndex++,
                        'scanning_id' => $scanning->id
                    ];
                }
            }
            
            $totalPages = count($allPages);
        @endphp
        
        @if($totalPages > 0)
        <!-- Progress Card -->
        <div class="progress-card">
            <div class="progress-header">
                <div class="progress-title">Page Classification Progress</div>
                <div class="progress-counter" id="progress-text">0 of {{ $totalPages }} pages completed</div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progress-fill" style="width: 0%"></div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Document Viewer -->
            <div class="viewer-card">
                <div class="viewer-header">
                    <div class="viewer-controls">
                        <h3 class="viewer-title">Page Viewer</h3>
                        <div class="nav-controls">
                            <button id="prev-page" class="nav-btn">
                                <i data-lucide="chevron-left" style="width: 1.25rem; height: 1.25rem;"></i>
                            </button>
                            <div class="doc-counter" id="page-counter">1 of {{ $totalPages }}</div>
                            <button id="next-page" class="nav-btn">
                                <i data-lucide="chevron-right" style="width: 1.25rem; height: 1.25rem;"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="document-viewer" class="document-viewer">
                    <div class="viewer-placeholder">
                        <i data-lucide="file-text" style="width: 4rem; height: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p>Loading page...</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Page Thumbnails -->
                <div class="thumbnails-card">
                    <div class="thumbnails-header">
                        <h3 class="thumbnails-title">Pages ({{ $totalPages }})</h3>
                    </div>
                    <div class="thumbnails-grid">
                        @foreach($allPages as $pageData)
                        <div class="document-thumbnail {{ $pageData['page_index'] === 0 ? 'active' : '' }}" 
                             data-page-index="{{ $pageData['page_index'] }}" 
                             data-file-path="{{ $pageData['file_path'] }}"
                             data-page-number="{{ $pageData['page_number'] }}"
                             data-type="{{ $pageData['type'] }}"
                             data-scanning-id="{{ $pageData['scanning_id'] }}">
                            
                            <!-- Quality Indicator -->
                            <div class="thumbnail-quality {{ $pageData['type'] === 'pdf_page' ? 'pdf' : 'image' }}">
                                {{ $pageData['type'] === 'pdf_page' ? 'PDF' : 'IMG' }}
                            </div>
                            
                            @if($pageData['type'] === 'pdf_page')
                                <div class="pdf-thumbnail-container">
                                    <canvas class="pdf-thumbnail-canvas" 
                                            data-pdf-path="{{ asset('storage/app/public/' . $pageData['file_path']) }}"
                                            data-page-number="{{ $pageData['page_number'] }}">
                                    </canvas>
                                    <div class="pdf-thumbnail-fallback" style="display: none;">
                                        <i data-lucide="file-text"></i>
                                        <div style="font-size: 0.75rem; font-weight: 500; margin-top: 0.5rem;">
                                            PDF Page {{ $pageData['page_number'] }}
                                        </div>
                                    </div>
                                    <div class="pdf-thumbnail-loading">
                                        <div class="spinner"></div>
                                        <div style="font-size: 0.75rem; margin-top: 0.5rem;">Loading...</div>
                                    </div>
                                </div>
                            @else
                                <img src="{{ asset('storage/app/public/' . $pageData['file_path']) }}" 
                                     alt="{{ $pageData['display_name'] }}"
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem;"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="thumbnail-icon" style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center;">
                                    <i data-lucide="image" style="width: 2.5rem; height: 2.5rem; color: #667eea; margin-bottom: 0.5rem;"></i>
                                    <div style="font-size: 0.75rem; color: #64748b; text-align: center;">Image Error</div>
                                </div>
                            @endif
                            
                            <div class="thumbnail-label">{{ $pageData['display_name'] }}</div>
                            <div class="page-status" data-page-index="{{ $pageData['page_index'] }}">
                                <i data-lucide="circle" style="width: 0.75rem; height: 0.75rem;"></i>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Classification Form -->
                <div class="classification-card">
                    <div class="classification-header">
                        <h3 class="classification-title">Page Classification</h3>
                        <p class="classification-subtitle" id="current-page-title">Classify Page 1</p>
                    </div>
                    
                    <form id="page-typing-form" style="display: flex; flex-direction: column; flex: 1; overflow: hidden;">
                        @csrf
                        
                        <div class="form-container">
                            @foreach($allPages as $pageData)
                            @php
                                // Find existing page typing data for this specific page
                                $existingPageTyping = $fileIndexing->pagetypings
                                    ->where('file_path', $pageData['file_path'])
                                    ->where('page_number', $pageData['page_number'])
                                    ->first();
                            @endphp
                            <div class="page-form {{ $pageData['page_index'] === 0 ? 'active' : 'hidden' }}" 
                                 data-page-index="{{ $pageData['page_index'] }}" 
                                 data-file-path="{{ $pageData['file_path'] }}"
                                 data-page-number="{{ $pageData['page_number'] }}"
                                 data-scanning-id="{{ $pageData['scanning_id'] }}">
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        Page Type <span class="required">*</span>
                                    </label>
                                    <select class="form-select page-type-select" required data-page-index="{{ $pageData['page_index'] }}">
                                        <option value="">Select page type</option>
                                        <option value="1" {{ $existingPageTyping && $existingPageTyping->page_type == '1' ? 'selected' : '' }}>File Cover (FC)</option>
                                        <option value="2" {{ $existingPageTyping && $existingPageTyping->page_type == '2' ? 'selected' : '' }}>Application (APP)</option>
                                        <option value="3" {{ $existingPageTyping && $existingPageTyping->page_type == '3' ? 'selected' : '' }}>Bill Notice (BN)</option>
                                        <option value="4" {{ $existingPageTyping && $existingPageTyping->page_type == '4' ? 'selected' : '' }}>Correspondence (COR)</option>
                                        <option value="5" {{ $existingPageTyping && $existingPageTyping->page_type == '5' ? 'selected' : '' }}>Land Title (LT)</option>
                                        <option value="6" {{ $existingPageTyping && $existingPageTyping->page_type == '6' ? 'selected' : '' }}>Legal (LEG)</option>
                                        <option value="7" {{ $existingPageTyping && $existingPageTyping->page_type == '7' ? 'selected' : '' }}>Payment Evidence (PE)</option>
                                        <option value="8" {{ $existingPageTyping && $existingPageTyping->page_type == '8' ? 'selected' : '' }}>Report (REP)</option>
                                        <option value="9" {{ $existingPageTyping && $existingPageTyping->page_type == '9' ? 'selected' : '' }}>Survey (SUR)</option>
                                        <option value="10" {{ $existingPageTyping && $existingPageTyping->page_type == '10' ? 'selected' : '' }}>Miscellaneous (MISC)</option>
                                        <option value="11" {{ $existingPageTyping && $existingPageTyping->page_type == '11' ? 'selected' : '' }}>Image (IMG)</option>
                                        <option value="12" {{ $existingPageTyping && $existingPageTyping->page_type == '12' ? 'selected' : '' }}>Town Planning (TP)</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Page Subtype</label>
                                    <select class="form-select page-subtype-select" data-page-index="{{ $pageData['page_index'] }}">
                                        <option value="">Select page subtype</option>
                                        @if($existingPageTyping && $existingPageTyping->page_subtype)
                                            <option value="{{ $existingPageTyping->page_subtype }}" selected>{{ $existingPageTyping->page_subtype }}</option>
                                        @endif
                                    </select>
                                    <div class="form-help">Specific classification based on page type</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        Serial Number <span class="required">*</span>
                                    </label>
                                    <input type="number" class="form-input serial-input" 
                                           value="{{ $existingPageTyping ? $existingPageTyping->serial_number : $pageData['page_index'] + 1 }}" 
                                           required min="1" data-page-index="{{ $pageData['page_index'] }}">
                                    <div class="form-help">Sequential page number for ordering</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Reference Code</label>
                                    <input type="text" class="form-input page-code-input" 
                                           value="{{ $existingPageTyping ? $existingPageTyping->page_code : '' }}"
                                           placeholder="e.g., FC-001, APP-002" readonly data-page-index="{{ $pageData['page_index'] }}">
                                    <div class="form-help">Auto-generated reference code for quick identification</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="form-footer">
                            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                                <button type="button" class="btn-save" id="save-current-btn" style="flex: 1; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i data-lucide="save" style="width: 1.25rem; height: 1.25rem;"></i>
                                    Save & Next Page
                                </button>
                                {{-- <button type="button" class="btn-save" id="batch-save-btn" style="flex: 1; background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);">
                                    <i data-lucide="layers" style="width: 1.25rem; height: 1.25rem;"></i>
                                    Batch Save All
                                </button> --}}
                            </div>
                            {{-- <div style="display: flex; gap: 1rem;">
                                <button type="button" class="btn-save" id="finish-btn" style="flex: 1;">
                                    <i data-lucide="check" style="width: 1.25rem; height: 1.25rem;"></i>
                                    Finish Classification
                                </button>
                            </div> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('edms.scanning', $fileIndexing->id) }}" class="btn-back">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                Back to Document Scanning
            </a>
            
            <div class="status-text">
                Complete all page classifications to finish the EDMS workflow
            </div>
        </div>

        @else
        <!-- No Documents State -->
        <div class="no-documents">
            <i data-lucide="file-x" class="no-documents-icon"></i>
            <h3>No Documents Available</h3>
            <p>You need to upload documents before you can classify them. Please go back to the scanning step to upload your documents.</p>
            <a href="{{ route('edms.scanning', $fileIndexing->id) }}" class="btn-primary">
                <i data-lucide="upload" style="width: 1.25rem; height: 1.25rem;"></i>
                Go to Document Scanning
            </a>
        </div>
        @endif

        <!-- Help Section -->
        <div class="help-card">
            <div class="help-header">
                <i data-lucide="help-circle" class="help-icon"></i>
                <div class="help-content">
                    <h4>Page Classification Guidelines</h4>
                    <ul class="help-list">
                        <li>Each page of your PDF documents needs to be classified individually</li>
                        <li>For example: Page 1 might be "File Cover", Page 2 might be "Land Title", etc.</li>
                        <li>Select the appropriate page type first, then choose a specific subtype</li>
                        <li>Assign sequential serial numbers for proper document ordering</li>
                        <li>Reference codes are auto-generated based on page type and serial number</li>
                        <li>Use "Save & Next Page" to continue to the next page, or "Finish" when done</li>
                        <li>You can return anytime to continue where you left off</li>
                        <li>Navigate between pages using the arrow buttons or thumbnails</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('admin.footer')
</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    lucide.createIcons();
    
    // Configure PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    
    // Page types and subtypes data structure
    const pageTypes = [
        { id: 1, code: "FC", name: "File Cover" },
        { id: 2, code: "APP", name: "Application" },
        { id: 3, code: "BN", name: "Bill Notice" },
        { id: 4, code: "COR", name: "Correspondence" },
        { id: 5, code: "LT", name: "Land Title" },
        { id: 6, code: "LEG", name: "Legal" },
        { id: 7, code: "PE", name: "Payment Evidence" },
        { id: 8, code: "REP", name: "Report" },
        { id: 9, code: "SUR", name: "Survey" },
        { id: 10, code: "MISC", name: "Miscellaneous" },
        { id: 11, code: "IMG", name: "Image" },
        { id: 12, code: "TP", name: "Town Planning" }
    ];

    // Page subtypes organized by page type ID
    const pageSubTypes = {
        1: [ // File Cover
            { id: 1, code: "NFC", name: "New File Cover" },
            { id: 2, code: "OFC", name: "Old File Cover" }
        ],
        2: [ // Application
            { id: 3, code: "CO", name: "Certificate of Occupancy" },
            { id: 4, code: "REV", name: "Revalidation" },
            { id: 42, code: "OTH", name: "Others" },
            { id: 96, code: "ASI", name: "Application for Surrender/Issuance of CofO" },
            { id: 97, code: "ATF", name: "Application for Temporary Files" },
            { id: 128, code: "REC", name: "Recertification" },
            { id: 136, code: "INS", name: "Inspection" },
            { id: 137, code: "CF", name: "Computer Form" }
        ],
        3: [ // Bill Notice
            { id: 7, code: "DGR", name: "Demand for Ground Rent" },
            { id: 34, code: "DN", name: "Demand Notice" },
            { id: 35, code: "MISC", name: "Miscellaneous" },
            { id: 77, code: "NOA", name: "Notice of Assessment" },
            { id: 92, code: "AUC", name: "Auction Notice" },
            { id: 133, code: "FRP", name: "First Registration of Plot Bill" }
        ],
        4: [ // Correspondence
            { id: 8, code: "AL", name: "Acknowledgment Letter" },
            { id: 9, code: "ASR", name: "Application Submission for Recommendation" },
            { id: 10, code: "ACO", name: "Approval of Certificate of Occupancy" },
            { id: 11, code: "AUL", name: "Authority Letter" },
            { id: 12, code: "BIR", name: "Board of Internal Revenue" },
            { id: 13, code: "CL", name: "Conveyance Letter" },
            { id: 14, code: "DTP", name: "Director Town Planning" },
            { id: 15, code: "SD", name: "Survey Description" },
            { id: 16, code: "SP", name: "Survey Plan" },
            { id: 17, code: "SG", name: "Surveyor General" },
            { id: 29, code: "MISC", name: "Miscellaneous" },
            { id: 30, code: "IM", name: "Internal Memo" },
            { id: 31, code: "EM", name: "External Memo" }
        ],
        5: [ // Land Title
            { id: 5, code: "CO", name: "Certificate of Occupancy" },
            { id: 6, code: "SP", name: "Survey Plan" },
            { id: 32, code: "MISC", name: "Miscellaneous" },
            { id: 130, code: "LFR", name: "Letter of First Registration" },
            { id: 131, code: "CR", name: "Confirmation of Registration" },
            { id: 140, code: "PRO", name: "Provisional Right of Occupancy" }
        ],
        6: [ // Legal
            { id: 18, code: "AGR", name: "Agreement" },
            { id: 39, code: "REP", name: "Report" },
            { id: 44, code: "POA", name: "Power of Attorney" },
            { id: 45, code: "DOS", name: "Deed of Surrender" },
            { id: 46, code: "WCC", name: "Withdrawal of Clients CofO" },
            { id: 48, code: "CACC", name: "CAC Certificate" },
            { id: 49, code: "CI", name: "Certificate of Incorporation" },
            { id: 50, code: "LA", name: "Letter of Administration" },
            { id: 51, code: "MISC", name: "Miscellaneous" },
            { id: 53, code: "DOA", name: "Deed of Assignment" }
        ],
        7: [ // Payment Evidence
            { id: 19, code: "AOF", name: "Assessment of Fees" },
            { id: 20, code: "BT", name: "Bank Teller" },
            { id: 21, code: "ITCC", name: "Income Tax Clearance Certificate" },
            { id: 22, code: "RCR", name: "Revenue Collector's Receipt" },
            { id: 36, code: "MISC", name: "Miscellaneous" },
            { id: 41, code: "REP", name: "Report" },
            { id: 70, code: "ITPR", name: "Income TAX P.A.Y.E Receipt" },
            { id: 78, code: "REC", name: "Receipts" }
        ],
        8: [ // Report
            { id: 23, code: "RR", name: "Reinspection Report" },
            { id: 37, code: "MISC", name: "Miscellaneous" },
            { id: 65, code: "IPVR", name: "Inspection and Property Valuation Report" },
            { id: 101, code: "PSR", name: "Property Search Report" },
            { id: 110, code: "LITR", name: "Low Income TAX Report" },
            { id: 111, code: "RVR", name: "Reconciliation of Valuation Report" },
            { id: 116, code: "PVR", name: "Property Valuation Report" }
        ],
        9: [ // Survey
            { id: 24, code: "TDP", name: "Title Deed Plan" },
            { id: 25, code: "SP", name: "Survey Plan" },
            { id: 26, code: "SD", name: "Survey Description" },
            { id: 33, code: "MISC", name: "Miscellaneous" },
            { id: 38, code: "REP", name: "Report" }
        ],
        10: [ // Miscellaneous
            { id: 27, code: "MISC", name: "Miscellaneous" },
            { id: 43, code: "OC", name: "Other Certificates" },
            { id: 59, code: "CP", name: "Company Profile" },
            { id: 132, code: "LRAT", name: "Land Registration Acknowledgment Ticket" }
        ],
        11: [ // Image
            { id: 28, code: "PP", name: "Passport" }
        ],
        12: [ // Town Planning
            { id: 135, code: "SKT", name: "Sketch" },
            { id: 141, code: "LP", name: "Location Plan" }
        ]
    };

    // Store page data for easy access
    const pageData = @json($allPages ?? []);
    const totalPages = pageData.length;
    let currentPageIndex = 0;
    
    // Store page classifications in memory
    const pageClassifications = {};
    
    document.addEventListener('DOMContentLoaded', function() {
        const documentViewer = document.getElementById('document-viewer');
        const pageCounter = document.getElementById('page-counter');
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        const thumbnails = document.querySelectorAll('.document-thumbnail');
        const classificationForms = document.querySelectorAll('.page-form');
        const progressFill = document.getElementById('progress-fill');
        const progressText = document.getElementById('progress-text');
        const currentPageTitle = document.getElementById('current-page-title');
        
        // Initialize page type change handlers
        initializePageTypeHandlers();
        
        // Initialize
        if (totalPages > 0) {
            showPage(0);
            updateProgress();
        }
        
        // Thumbnail clicks
        thumbnails.forEach((thumbnail, index) => {
            thumbnail.addEventListener('click', () => {
                showPage(index);
            });
        });
        
        // Navigation buttons
        prevBtn.addEventListener('click', () => {
            if (currentPageIndex > 0) {
                showPage(currentPageIndex - 1);
            }
        });
        
        nextBtn.addEventListener('click', () => {
            if (currentPageIndex < totalPages - 1) {
                showPage(currentPageIndex + 1);
            }
        });
        
        // Save current page and move to next
        document.getElementById('save-current-btn').addEventListener('click', function() {
            if (saveCurrentPage()) {
                if (currentPageIndex < totalPages - 1) {
                    showPage(currentPageIndex + 1);
                } else {
                    alert('All pages have been classified!');
                }
            }
        });
        
        // Batch save all pages
        document.getElementById('batch-save-btn').addEventListener('click', function() {
            batchSaveAllPages();
        });
        
        // Finish classification
        document.getElementById('finish-btn').addEventListener('click', function() {
            if (saveCurrentPage()) {
                finishClassification();
            }
        });
        
        // Initialize page type change handlers
        function initializePageTypeHandlers() {
            document.querySelectorAll('.page-type-select').forEach(select => {
                select.addEventListener('change', function() {
                    const pageIndex = this.dataset.pageIndex;
                    const pageTypeId = parseInt(this.value);
                    const subtypeSelect = document.querySelector(`select.page-subtype-select[data-page-index="${pageIndex}"]`);
                    
                    // Clear existing subtypes
                    subtypeSelect.innerHTML = '<option value="">Select page subtype</option>';
                    
                    // Populate subtypes if page type is selected
                    if (pageTypeId && pageSubTypes[pageTypeId]) {
                        pageSubTypes[pageTypeId].forEach(subtype => {
                            const option = document.createElement('option');
                            option.value = subtype.id;
                            option.textContent = `${subtype.name} (${subtype.code})`;
                            subtypeSelect.appendChild(option);
                        });
                    }
                    
                    // Update reference code
                    updateReferenceCode(pageIndex);
                    updatePageStatus(pageIndex);
                });
                
                // Trigger change event for existing data
                if (select.value) {
                    select.dispatchEvent(new Event('change'));
                }
            });
            
            // Serial number change handlers
            document.querySelectorAll('.serial-input').forEach(input => {
                input.addEventListener('input', function() {
                    const pageIndex = this.dataset.pageIndex;
                    updateReferenceCode(pageIndex);
                });
            });
        }
        
        // Update reference code
        function updateReferenceCode(pageIndex) {
            const pageTypeSelect = document.querySelector(`select.page-type-select[data-page-index="${pageIndex}"]`);
            const serialInput = document.querySelector(`input.serial-input[data-page-index="${pageIndex}"]`);
            const codeInput = document.querySelector(`input.page-code-input[data-page-index="${pageIndex}"]`);
            
            if (pageTypeSelect.value && serialInput.value) {
                const pageType = pageTypes.find(pt => pt.id == pageTypeSelect.value);
                if (pageType) {
                    const serial = serialInput.value.padStart(3, '0');
                    codeInput.value = `${pageType.code}-${serial}`;
                }
            } else {
                codeInput.value = '';
            }
        }
        
        // Update page status indicator
        function updatePageStatus(pageIndex) {
            const pageTypeSelect = document.querySelector(`select.page-type-select[data-page-index="${pageIndex}"]`);
            const serialInput = document.querySelector(`input.serial-input[data-page-index="${pageIndex}"]`);
            const statusIndicator = document.querySelector(`.page-status[data-page-index="${pageIndex}"]`);
            
            if (pageTypeSelect.value && serialInput.value) {
                statusIndicator.classList.add('completed');
                statusIndicator.classList.remove('in-progress');
                statusIndicator.innerHTML = '<i data-lucide="check" style="width: 0.75rem; height: 0.75rem;"></i>';
            } else if (pageTypeSelect.value || serialInput.value) {
                statusIndicator.classList.add('in-progress');
                statusIndicator.classList.remove('completed');
                statusIndicator.innerHTML = '<i data-lucide="clock" style="width: 0.75rem; height: 0.75rem;"></i>';
            } else {
                statusIndicator.classList.remove('completed', 'in-progress');
                statusIndicator.innerHTML = '<i data-lucide="circle" style="width: 0.75rem; height: 0.75rem;"></i>';
            }
            
            lucide.createIcons();
            updateProgress();
        }
        
        // Show page function
        function showPage(index) {
            currentPageIndex = index;
            
            // Update thumbnails
            thumbnails.forEach((thumb, i) => {
                thumb.classList.toggle('active', i === index);
            });
            
            // Update forms
            classificationForms.forEach((form, i) => {
                if (i === index) {
                    form.classList.remove('hidden');
                    form.classList.add('active');
                } else {
                    form.classList.add('hidden');
                    form.classList.remove('active');
                }
            });
            
            // Update counter and title
            pageCounter.textContent = `${index + 1} of ${totalPages}`;
            currentPageTitle.textContent = `Classify ${pageData[index].display_name}`;
            
            // Update navigation buttons
            prevBtn.disabled = index === 0;
            nextBtn.disabled = index === totalPages - 1;
            
            // Load page in viewer
            const currentPage = pageData[index];
            
            if (currentPage.type === 'pdf_page') {
                // For PDF pages, use PDF.js for better rendering
                const pdfPath = `{{ asset('storage/app/public/') }}/${currentPage.file_path}`;
                documentViewer.innerHTML = `
                    <div style="width: 100%; height: 500px; position: relative; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
                        <canvas id="main-pdf-canvas" 
                                style="max-width: 100%; max-height: 100%; border-radius: 0.5rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
                        </canvas>
                        <div id="main-pdf-loading" style="text-align: center; color: #667eea;">
                            <div class="spinner" style="width: 2rem; height: 2rem; margin: 0 auto 1rem;"></div>
                            <div style="font-size: 1rem; font-weight: 500;">Loading ${currentPage.display_name}...</div>
                        </div>
                        <div id="main-pdf-fallback" style="display: none; text-align: center; color: #718096; padding: 2rem;">
                            <i data-lucide="file-text" style="width: 4rem; height: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <h3 style="margin-bottom: 0.5rem;">${currentPage.display_name}</h3>
                            <p>PDF Page ${currentPage.page_number}</p>
                            <p style="font-size: 0.875rem; opacity: 0.7;">Click <a href="${pdfPath}" target="_blank" style="color: #667eea;">here</a> to open PDF in new tab</p>
                        </div>
                        <div style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem;">
                            ${currentPage.display_name}
                        </div>
                    </div>
                `;
                
                // Render the PDF page
                renderMainPdfPage(pdfPath, currentPage.page_number);
            } else {
                // For images
                const imagePath = `{{ asset('storage/app/public/') }}/${currentPage.file_path}`;
                documentViewer.innerHTML = `
                    <img src="${imagePath}" 
                         alt="${currentPage.display_name}" 
                         style="max-width: 100%; max-height: 500px; object-fit: contain; border-radius: 0.5rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div style="display: none; text-align: center; color: #718096;">
                        <i data-lucide="image-off" style="width: 4rem; height: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p>Unable to load image</p>
                    </div>
                `;
            }
            
            lucide.createIcons();
        }
        
        // Save current page data
        function saveCurrentPage() {
            const currentForm = document.querySelector(`.page-form[data-page-index="${currentPageIndex}"]`);
            const pageTypeSelect = currentForm.querySelector('.page-type-select');
            const pageSubtypeSelect = currentForm.querySelector('.page-subtype-select');
            const serialInput = currentForm.querySelector('.serial-input');
            const pageCodeInput = currentForm.querySelector('.page-code-input');
            
            // Validate required fields
            if (!pageTypeSelect.value || !serialInput.value) {
                alert('Please fill in all required fields (Page Type and Serial Number).');
                return false;
            }
            
            // Store classification data
            pageClassifications[currentPageIndex] = {
                file_path: currentForm.dataset.filePath,
                page_number: currentForm.dataset.pageNumber,
                scanning_id: currentForm.dataset.scanningId,
                page_type: pageTypeSelect.value,
                page_subtype: pageSubtypeSelect.value,
                serial_number: serialInput.value,
                page_code: pageCodeInput.value
            };
            
            // Update page status
            updatePageStatus(currentPageIndex);
            
            // Auto-save to server (optional)
            saveToServer(currentPageIndex);
            
            return true;
        }
        
        // Save to server
        function saveToServer(pageIndex) {
            const data = pageClassifications[pageIndex];
            if (!data) return;
            
            // Get CSRF token from multiple sources
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                             document.querySelector('input[name="_token"]')?.value ||
                             '{{ csrf_token() }}';
            
            fetch(`{{ route('edms.save-single-page-typing', $fileIndexing->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('Page saved:', result);
                if (result.success) {
                    // Show success indicator
                    showNotification('Page saved successfully!', 'success');
                } else {
                    showNotification(result.message || 'Error saving page', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving page:', error);
                showNotification('Error saving page: ' + error.message, 'error');
            });
        }
        
        // Finish classification
        function finishClassification() {
            // Save all classifications
            const allData = Object.values(pageClassifications);
            
            if (allData.length === 0) {
                alert('Please classify at least one page before finishing.');
                return;
            }
            
            // Get CSRF token from multiple sources
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                             document.querySelector('input[name="_token"]')?.value ||
                             '{{ csrf_token() }}';
            
            fetch(`{{ route('edms.finish-page-typing', $fileIndexing->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ classifications: allData })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    showNotification('Page classification completed successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = `{{ route('edms.index', $fileIndexing->main_application_id) }}`;
                    }, 1500);
                } else {
                    showNotification(result.message || 'Error completing classification', 'error');
                }
            })
            .catch(error => {
                console.error('Error finishing classification:', error);
                showNotification('Error completing classification: ' + error.message, 'error');
            });
        }
        
        // Show notification function
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => notification.remove());
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 0.5rem;
                color: white;
                font-weight: 600;
                z-index: 9999;
                max-width: 400px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                transform: translateX(100%);
                transition: transform 0.3s ease;
            `;
            
            // Set background color based on type
            switch (type) {
                case 'success':
                    notification.style.background = 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)';
                    break;
                case 'error':
                    notification.style.background = 'linear-gradient(135deg, #e53e3e 0%, #c53030 100%)';
                    break;
                case 'warning':
                    notification.style.background = 'linear-gradient(135deg, #ed8936 0%, #dd6b20 100%)';
                    break;
                default:
                    notification.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            }
            
            notification.textContent = message;
            
            // Add to document
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }
        
        // Batch save all pages function
        function batchSaveAllPages() {
            const allPages = [];
            let validPages = 0;
            let invalidPages = [];
            
            // Collect all page data
            for (let i = 0; i < totalPages; i++) {
                const form = document.querySelector(`.page-form[data-page-index="${i}"]`);
                const pageTypeSelect = form.querySelector('.page-type-select');
                const pageSubtypeSelect = form.querySelector('.page-subtype-select');
                const serialInput = form.querySelector('.serial-input');
                const pageCodeInput = form.querySelector('.page-code-input');
                
                // Check if required fields are filled
                if (pageTypeSelect.value && serialInput.value) {
                    allPages.push({
                        file_path: form.dataset.filePath,
                        page_number: form.dataset.pageNumber,
                        scanning_id: form.dataset.scanningId,
                        page_type: pageTypeSelect.value,
                        page_subtype: pageSubtypeSelect.value,
                        serial_number: serialInput.value,
                        page_code: pageCodeInput.value
                    });
                    validPages++;
                } else {
                    invalidPages.push(i + 1);
                }
            }
            
            if (validPages === 0) {
                showNotification('No pages have been classified yet. Please classify at least one page.', 'warning');
                return;
            }
            
            if (invalidPages.length > 0) {
                const proceed = confirm(`${invalidPages.length} pages are incomplete (pages: ${invalidPages.join(', ')}). Do you want to save only the ${validPages} completed pages?`);
                if (!proceed) {
                    return;
                }
            }
            
            // Show loading state
            const batchBtn = document.getElementById('batch-save-btn');
            const originalText = batchBtn.innerHTML;
            batchBtn.disabled = true;
            batchBtn.innerHTML = '<div class="spinner"></div> Saving...';
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                             document.querySelector('input[name="_token"]')?.value ||
                             '{{ csrf_token() }}';
            
            fetch(`{{ route('edms.batch-save-page-typing', $fileIndexing->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ pages: allPages })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    showNotification(`Batch save completed! ${result.saved_count} of ${result.total_count} pages saved successfully.`, 'success');
                    
                    // Update page status indicators for saved pages
                    allPages.forEach((pageData, index) => {
                        const pageIndex = pageData.page_number - 1; // Assuming page numbers start from 1
                        updatePageStatus(pageIndex);
                    });
                    
                    updateProgress();
                } else {
                    showNotification(result.message || 'Error in batch save operation', 'error');
                }
                
                if (result.errors && result.errors.length > 0) {
                    console.error('Batch save errors:', result.errors);
                    showNotification(`Some pages had errors: ${result.errors.length} errors occurred`, 'warning');
                }
            })
            .catch(error => {
                console.error('Error in batch save:', error);
                showNotification('Error in batch save operation: ' + error.message, 'error');
            })
            .finally(() => {
                // Restore button state
                batchBtn.disabled = false;
                batchBtn.innerHTML = originalText;
                lucide.createIcons();
            });
        }
        
        // Update progress function
        function updateProgress() {
            let completed = 0;
            
            for (let i = 0; i < totalPages; i++) {
                const pageTypeSelect = document.querySelector(`select.page-type-select[data-page-index="${i}"]`);
                const serialInput = document.querySelector(`input.serial-input[data-page-index="${i}"]`);
                
                if (pageTypeSelect && pageTypeSelect.value && serialInput && serialInput.value) {
                    completed++;
                }
            }
            
            const percentage = totalPages > 0 ? (completed / totalPages) * 100 : 0;
            progressFill.style.width = percentage + '%';
            progressText.textContent = `${completed} of ${totalPages} pages completed`;
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && currentPageIndex > 0) {
                showPage(currentPageIndex - 1);
            } else if (e.key === 'ArrowRight' && currentPageIndex < totalPages - 1) {
                showPage(currentPageIndex + 1);
            } else if (e.key === 'Enter' && e.ctrlKey) {
                // Ctrl+Enter to save and next
                document.getElementById('save-current-btn').click();
            }
        }); 
        
        // Initialize page status indicators
        for (let i = 0; i < totalPages; i++) {
            updatePageStatus(i);
        }
        
        // Initialize PDF thumbnails
        initializePdfThumbnails();
    });
    
    // PDF Thumbnail Rendering Functions
    async function initializePdfThumbnails() {
        console.log('Initializing PDF thumbnails...');
        const pdfCanvases = document.querySelectorAll('.pdf-thumbnail-canvas');
        console.log(`Found ${pdfCanvases.length} PDF canvases to render`);
        
        // Render thumbnails with a small delay between each to avoid overwhelming the browser
        for (let i = 0; i < pdfCanvases.length; i++) {
            const canvas = pdfCanvases[i];
            const pdfPath = canvas.dataset.pdfPath;
            const pageNumber = parseInt(canvas.dataset.pageNumber);
            
            console.log(`Processing thumbnail ${i + 1}/${pdfCanvases.length}: ${pdfPath}, page ${pageNumber}`);
            
            try {
                // Add a small delay between renders to prevent browser lockup
                if (i > 0) {
                    await new Promise(resolve => setTimeout(resolve, 100));
                }
                await renderPdfThumbnail(canvas, pdfPath, pageNumber);
            } catch (error) {
                console.error(`Error rendering PDF thumbnail ${i + 1}:`, error);
                showPdfThumbnailFallback(canvas);
            }
        }
        
        console.log('PDF thumbnail initialization complete');
    }
    
    async function renderPdfThumbnail(canvas, pdfPath, pageNumber) {
        const container = canvas.parentElement;
        const loadingElement = container.querySelector('.pdf-thumbnail-loading');
        const fallbackElement = container.querySelector('.pdf-thumbnail-fallback');
        
        try {
            console.log(`Starting PDF thumbnail render for: ${pdfPath}, page: ${pageNumber}`);
            
            // Show loading state
            if (loadingElement) {
                loadingElement.style.display = 'flex';
                loadingElement.style.flexDirection = 'column';
                loadingElement.style.alignItems = 'center';
                loadingElement.style.justifyContent = 'center';
            }
            canvas.style.display = 'none';
            if (fallbackElement) {
                fallbackElement.style.display = 'none';
            }
            
            // Load PDF with simplified options for thumbnails
            const loadingTask = pdfjsLib.getDocument({
                url: pdfPath,
                disableAutoFetch: true,
                disableStream: true,
                disableFontFace: true, // Disable font loading for thumbnails
                verbosity: 0 // Reduce verbosity to minimize warnings
            });
            
            // Set a timeout for PDF loading
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('PDF loading timeout')), 10000);
            });
            
            const pdf = await Promise.race([loadingTask.promise, timeoutPromise]);
            console.log(`PDF loaded successfully for thumbnail, total pages: ${pdf.numPages}`);
            
            // Validate page number
            if (pageNumber > pdf.numPages || pageNumber < 1) {
                throw new Error(`Invalid page number: ${pageNumber}. PDF has ${pdf.numPages} pages.`);
            }
            
            // Get the specific page
            const page = await pdf.getPage(pageNumber);
            console.log(`Page ${pageNumber} loaded successfully for thumbnail`);
            
            // Wait for container to have dimensions with timeout
            let attempts = 0;
            while ((container.clientWidth === 0 || container.clientHeight === 0) && attempts < 20) {
                await new Promise(resolve => setTimeout(resolve, 50));
                attempts++;
            }
            
            // Use fixed dimensions if container dimensions are still not available
            const containerWidth = container.clientWidth > 0 ? container.clientWidth - 20 : 140;
            const containerHeight = container.clientHeight > 0 ? container.clientHeight - 20 : 180;
            
            const viewport = page.getViewport({ scale: 1 });
            const scaleX = containerWidth / viewport.width;
            const scaleY = containerHeight / viewport.height;
            const scale = Math.min(scaleX, scaleY, 1.5); // Reduced max scale for thumbnails
            
            const scaledViewport = page.getViewport({ scale });
            
            console.log(`Rendering thumbnail at scale: ${scale}, dimensions: ${scaledViewport.width}x${scaledViewport.height}`);
            
            // Set canvas dimensions
            canvas.width = scaledViewport.width;
            canvas.height = scaledViewport.height;
            canvas.style.width = Math.min(scaledViewport.width, containerWidth) + 'px';
            canvas.style.height = Math.min(scaledViewport.height, containerHeight) + 'px';
            canvas.style.maxWidth = '100%';
            canvas.style.maxHeight = '100%';
            canvas.style.objectFit = 'contain';
            
            // Render page to canvas with simplified options
            const context = canvas.getContext('2d');
            
            // Clear canvas with white background
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            
            const renderContext = {
                canvasContext: context,
                viewport: scaledViewport,
                enableWebGL: false,
                renderTextLayer: false, // Disable text layer for thumbnails
                renderAnnotationLayer: false // Disable annotations for thumbnails
            };
            
            // Set timeout for rendering
            const renderTimeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('PDF rendering timeout')), 15000);
            });
            
            const renderTask = page.render(renderContext);
            await Promise.race([renderTask.promise, renderTimeoutPromise]);
            
            console.log(`PDF thumbnail page ${pageNumber} rendered successfully`);
            
            // Hide loading and show canvas
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
            canvas.style.display = 'block';
            
            // Add subtle animation
            canvas.style.opacity = '0';
            canvas.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                canvas.style.opacity = '1';
            }, 50);
            
            // Clean up
            page.cleanup();
            
        } catch (error) {
            console.error('Error rendering PDF thumbnail:', error);
            showPdfThumbnailFallback(canvas);
        }
    }
    
    function showPdfThumbnailFallback(canvas) {
        const container = canvas.parentElement;
        const loadingElement = container.querySelector('.pdf-thumbnail-loading');
        const fallbackElement = container.querySelector('.pdf-thumbnail-fallback');
        
        console.log('Showing PDF thumbnail fallback');
        
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
        canvas.style.display = 'none';
        if (fallbackElement) {
            fallbackElement.style.display = 'flex';
            fallbackElement.style.flexDirection = 'column';
            fallbackElement.style.alignItems = 'center';
            fallbackElement.style.justifyContent = 'center';
        }
        
        // Re-initialize lucide icons for fallback
        lucide.createIcons();
    }
    
    // Main PDF Page Rendering Function
    async function renderMainPdfPage(pdfPath, pageNumber) {
        const canvas = document.getElementById('main-pdf-canvas');
        const loadingElement = document.getElementById('main-pdf-loading');
        const fallbackElement = document.getElementById('main-pdf-fallback');
        
        try {
            // Show loading state
            loadingElement.style.display = 'block';
            canvas.style.display = 'none';
            fallbackElement.style.display = 'none';
            
            // Load PDF
            const pdf = await pdfjsLib.getDocument(pdfPath).promise;
            
            // Get the specific page
            const page = await pdf.getPage(pageNumber);
            
            // Calculate scale to fit viewer container
            const viewerContainer = document.getElementById('document-viewer');
            const containerWidth = viewerContainer.clientWidth - 40; // Account for padding
            const containerHeight = 460; // Fixed height minus padding
            
            const viewport = page.getViewport({ scale: 1 });
            const scaleX = containerWidth / viewport.width;
            const scaleY = containerHeight / viewport.height;
            const scale = Math.min(scaleX, scaleY, 2.0); // Max scale of 2.0 for quality
            
            const scaledViewport = page.getViewport({ scale });
            
            // Set canvas dimensions
            canvas.width = scaledViewport.width;
            canvas.height = scaledViewport.height;
            canvas.style.width = scaledViewport.width + 'px';
            canvas.style.height = scaledViewport.height + 'px';
            
            // Render page to canvas
            const context = canvas.getContext('2d');
            const renderContext = {
                canvasContext: context,
                viewport: scaledViewport
            };
            
            await page.render(renderContext).promise;
            
            // Hide loading and show canvas
            loadingElement.style.display = 'none';
            canvas.style.display = 'block';
            
            // Add subtle animation
            canvas.style.opacity = '0';
            canvas.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                canvas.style.opacity = '1';
            }, 50);
            
        } catch (error) {
            console.error('Error rendering main PDF page:', error);
            
            // Show fallback
            loadingElement.style.display = 'none';
            canvas.style.display = 'none';
            fallbackElement.style.display = 'block';
            
            // Re-initialize lucide icons for fallback
            lucide.createIcons();
        }
    }
</script>
@endsection