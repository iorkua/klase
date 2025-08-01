<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class FileIndexingController extends Controller
{
  
    public function index()
    {
     return view('fileindex.index');
    }

 

}
