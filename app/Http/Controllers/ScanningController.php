<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScanningController extends Controller
{ 
    public function index() {
        $PageTitle = 'Document Upload';
        $PageDescription = 'Upload scanned documents to their digital folders';
        return view('scanning.index', compact('PageTitle', 'PageDescription'));
    }
}
