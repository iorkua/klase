<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrintLabelController extends Controller
{ 
    public function index() {
        $PageTitle = 'Print File Labels';
        $PageDescription = 'Generate and print labels for physical files';
        //Log::info('File Tracker accessed', ['user_id' => auth()->id()]);
        return view('printlabel.index', compact('PageTitle', 'PageDescription'));
    }
}
