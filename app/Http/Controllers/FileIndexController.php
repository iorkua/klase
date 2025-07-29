<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class FileIndexController extends Controller
{
  
    public function index()
    {
        $PageTitle = 'File Indexing';
        $PageDescription = '';
        
        // Fetch all records from the file_indexing table
        // $fileIndexRecords = DB::connection('sqlsrv')->table('file_indexing')
        //     ->orderBy('created_at', 'desc')
        //     ->get();
        
        return view('fileindexing.index', compact('PageTitle', 'PageDescription'));
    
    }

  
    public function create()
    {
        return view('fileindex.create');
    }
}