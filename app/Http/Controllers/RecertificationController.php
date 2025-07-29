<?php

namespace App\Http\Controllers;

use App\Services\ScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecertificationController extends Controller
{ 
    public function index() {
        $PageTitle = 'Recertification Programme';
        $PageDescription = ' ';
        return view('recertification.index', compact('PageTitle', 'PageDescription'));
    }

    //new-application
    
    //   public function index() {
    //     $PageTitle = 'Recertification Programme';
    //     $PageDescription = ' ';
    //     return view('recertification.index', compact('PageTitle', 'PageDescription'));
    // }

}



