<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FilearchiveController extends Controller
{ 
    public function index() {
        $PageTitle = 'File Digital Archive';
        $PageDescription = 'Access and manage digitally archived files';
        return view('filearchive.index', compact('PageTitle', 'PageDescription'));
    }
}
