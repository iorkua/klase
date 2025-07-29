<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileTrackerController extends Controller
{ 
    public function index() {
        $PageTitle = 'File Tracker';
        $PageDescription = 'Track and manage files within the system';
        Log::info('File Tracker accessed', ['user_id' => auth()->id()]);
        return view('filetracker.index', compact('PageTitle', 'PageDescription'));
    }

    //print
  public function print() {
        $PageTitle = 'File Tracker';
        $PageDescription = 'Track and manage files within the system';
        Log::info('File Tracker accessed', ['user_id' => auth()->id()]);
        return view('filetracker.print', compact('PageTitle', 'PageDescription'));
    }

}
