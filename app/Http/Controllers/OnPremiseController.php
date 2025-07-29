<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OnPremiseController extends Controller
{ 
    public function index() {
        $PageTitle = 'On-Premise - Pay-per-Search';
        $PageDescription = '';
        //Log::info('File Tracker accessed', ['user_id' => auth()->id()]);
        return view('onpremise.index', compact('PageTitle', 'PageDescription'));
    }


    
}
