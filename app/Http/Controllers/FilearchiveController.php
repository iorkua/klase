<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use App\Models\FileIndexing;
use App\Models\PageTyping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FilearchiveController extends Controller
{ 
    public function index(Request $request) {
        $PageTitle = 'File Digital Archive';
        $PageDescription = 'Access and manage digitally archived files';
        
        // Get completed page typed files
        $completedFiles = FileIndexing::whereHas('pagetypings')
            ->with(['pagetypings' => function($query) {
                $query->with('typedBy')->orderBy('page_number');
            }, 'scannings'])
            ->withCount(['pagetypings', 'scannings'])
            ->orderBy('updated_at', 'desc');
        
        // Apply search filters if provided
        if ($request->filled('search')) {
            $search = $request->get('search');
            $field = $request->get('field', 'all');
            
            $completedFiles->where(function($query) use ($search, $field) {
                if ($field === 'all' || $field === 'fileName') {
                    $query->orWhere('file_title', 'like', "%{$search}%");
                }
                if ($field === 'all' || $field === 'fileNumber') {
                    $query->orWhere('file_number', 'like', "%{$search}%");
                }
                if ($field === 'all' || $field === 'type') {
                    $query->orWhere('land_use_type', 'like', "%{$search}%");
                }
                if ($field === 'all' || $field === 'page') {
                    $query->orWhereHas('pagetypings', function($q) use ($search) {
                        $q->where('page_type', 'like', "%{$search}%")
                          ->orWhere('page_subtype', 'like', "%{$search}%");
                    });
                }
            });
        }
        
        // Apply category filter
        if ($request->filled('category') && $request->get('category') !== 'all') {
            $category = $request->get('category');
            switch ($category) {
                case 'land':
                    $completedFiles->whereIn('land_use_type', ['Residential', 'Commercial', 'Industrial']);
                    break;
                case 'legal':
                    $completedFiles->whereHas('pagetypings', function($q) {
                        $q->whereIn('page_type', ['Deed', 'Certificate', 'Legal Document']);
                    });
                    break;
                case 'admin':
                    $completedFiles->whereHas('pagetypings', function($q) {
                        $q->whereIn('page_type', ['Application Form', 'Letter', 'Administrative']);
                    });
                    break;
            }
        }
        
        $completedFiles = $completedFiles->paginate(12);
        
        // Calculate statistics
        $stats = [
            'total_archived' => FileIndexing::whereHas('pagetypings')->count(),
            'recently_added' => FileIndexing::whereHas('pagetypings')
                ->where('updated_at', '>=', now()->subDays(30))->count(),
            'total_pages' => PageTyping::count(),
            'storage_used' => $this->calculateStorageUsed(),
        ];
        
        // Get popular page types for filters
        $popularPageTypes = PageTyping::select('page_type', DB::raw('count(*) as count'))
            ->groupBy('page_type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        return view('filearchive.index', compact(
            'PageTitle', 
            'PageDescription', 
            'completedFiles', 
            'stats', 
            'popularPageTypes'
        ));
    }
    
    /**
     * Get file details for modal display
     */
    public function getFileDetails($id)
    {
        $file = FileIndexing::with(['pagetypings.typedBy', 'scannings'])
            ->withCount(['pagetypings', 'scannings'])
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'file' => $file->load(['pagetypings.typedBy:id,first_name,last_name'])
        ]);
    }
    
    /**
     * Search files with advanced filters
     */
    public function search(Request $request)
    {
        $query = FileIndexing::whereHas('pagetypings')
            ->with(['pagetypings.typedBy', 'scannings'])
            ->withCount(['pagetypings', 'scannings']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('file_title', 'like', "%{$search}%")
                  ->orWhere('file_number', 'like', "%{$search}%")
                  ->orWhereHas('pagetypings', function($subQ) use ($search) {
                      $subQ->where('page_type', 'like', "%{$search}%");
                  });
            });
        }
        
        $files = $query->paginate(12);
        
        return response()->json([
            'success' => true,
            'files' => $files,
            'html' => view('filearchive.partials.files_grid_content', compact('files'))->render()
        ]);
    }
    
    /**
     * Calculate storage used by archived files
     */
    private function calculateStorageUsed()
    {
        // This is a placeholder - you might want to implement actual file size calculation
        // based on your scanning files or implement a more sophisticated storage tracking
        return '4.2 GB';
    }
}


