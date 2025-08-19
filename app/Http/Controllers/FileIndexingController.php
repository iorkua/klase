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

    /**
     * Display the specified file indexing record.
     */
    public function show($id)
    {
        try {
            $record = DB::connection('sqlsrv')->table('file_indexings')->where('id', $id)->first();
        } catch (\Throwable $e) {
            $record = null;
        }

        if (!$record) {
            return redirect()->route('fileindex.index')->with('error', 'File indexing record not found.');
        }

        // If a dedicated view exists, render it; otherwise, return to index with context
        if (view()->exists('fileindexing.show')) {
            return view('fileindexing.show', compact('record'));
        }

        return redirect()->route('fileindex.index')
            ->with('success', 'Opened file indexing record.')
            ->with('file_indexing_id', $id);
    }

    /**
     * Check if a file number has already been indexed
     */
    public function checkIndexed(Request $request)
    {
        $fileno = trim((string) $request->query('fileno', ''));
        
        if ($fileno === '') {
            return response()->json(['exists' => false]);
        }

        try {
            $record = DB::connection('sqlsrv')
                ->table('file_indexings')
                ->where('file_number', $fileno)
                ->orderBy('id', 'desc')
                ->first();

            if (!$record) {
                return response()->json(['exists' => false]);
            }

            return response()->json([
                'exists' => true,
                'record' => [
                    'id' => $record->id,
                    'file_number' => $record->file_number,
                    'st_fillno' => $record->st_fillno,
                    'file_title' => $record->file_title,
                    'land_use_type' => $record->land_use_type,
                    'plot_number' => $record->plot_number,
                    'district' => $record->district,
                    'lga' => $record->lga,
                    'has_cofo' => (int) ($record->has_cofo ?? 0),
                    'is_merged' => (int) ($record->is_merged ?? 0),
                    'has_transaction' => (int) ($record->has_transaction ?? 0),
                    'is_problematic' => (int) ($record->is_problematic ?? 0),
                    'is_co_owned_plot' => (int) ($record->is_co_owned_plot ?? 0),
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

 

}
