<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FileIndexingController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\InstrumentTypeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for application data
Route::get('/get-application-data', function (Request $request) {
    $applicationId = $request->input('application_id');
    
    if (!$applicationId) {
        return response()->json(['error' => 'No application ID provided'], 400);
    }
    
    try {
        $applicationData = DB::connection('sqlsrv')
            ->table('dbo.mother_applications')
            ->where('id', $applicationId)
            ->first();
            
        if ($applicationData) {
            return response()->json([
                'success' => true,
                'data' => $applicationData
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data found for this application ID'
            ], 404);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving application data',
            'error' => $e->getMessage()
        ], 500);
    }
})->name('api.get-application-data');

// File Indexing API Endpoints
Route::get('/file-records', [FileIndexingController::class, 'getAllRecords']);
Route::get('/file-records/{id}', [FileIndexingController::class, 'getRecord']);
Route::post('/file-records/search', [FileIndexingController::class, 'searchRecords']);

// New API endpoints for CofO and Property Transaction data
Route::get('/cofo-record/{fileNo}', [FileIndexingController::class, 'getCofORecord']);
Route::get('/property-transaction', [FileIndexingController::class, 'getPropertyTransactionRecord']);

// Instruments API Routes - Not requiring authentication for now to fix the immediate issue
Route::post('/instruments/generate-particulars', [InstrumentController::class, 'generateParticulars']);

// Instrument Types API Routes
Route::get('/instrument-types', [InstrumentTypeController::class, 'getAll']);

// Route for fetching sub-final-bill details
Route::get('/sub-final-bill/show/{id}', [App\Http\Controllers\SubFinalBillController::class, 'show']);

// Route for fetching application details
Route::get('/application-details/{fileId}/{fileType}', [App\Http\Controllers\ProgrammesController::class, 'getApplicationDetails']);

// Route for fetching existing MLS file numbers for extensions
Route::get('/get-existing-mls-files', function (Request $request) {
    try {
        // Query the fileNumber table to get existing MLS file numbers
        $mlsFiles = DB::connection('sqlsrv')
            ->table('dbo.fileNumber')
            ->whereNotNull('mlsfNo')
            ->where('mlsfNo', '!=', '')
            ->select('id', 'mlsfNo')
            ->orderBy('id', 'desc')
            ->limit(500) // Limit to prevent overwhelming the dropdown
            ->get();

        if ($mlsFiles->count() > 0) {
            return response()->json([
                'success' => true,
                'files' => $mlsFiles->map(function($file) {
                    return [
                        'id' => $file->id,
                        'mlsFNo' => $file->mlsfNo,
                        'file_number' => $file->mlsfNo
                    ];
                })
            ]);
        } else {
            return response()->json([
                'success' => true,
                'files' => [],
                'message' => 'No existing MLS file numbers found'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving MLS file numbers',
            'error' => $e->getMessage()
        ], 500);
    }
})->name('api.get-existing-mls-files');

// Route for searching file numbers for property records
Route::post('/search-file-numbers', [App\Http\Controllers\PropertyRecordController::class, 'searchFileNumbers']);

// Test route to debug file number search
Route::get('/test-file-numbers', function() {
    return response()->json([
        'success' => true,
        'message' => 'API endpoint is working',
        'sample_data' => [
            [
                'id' => 'test_1',
                'fileno' => 'TEST-001',
                'description' => 'Test File Number 1',
                'plot_no' => '123',
                'lga' => 'Test LGA',
                'location' => 'Test Location',
                'source' => 'test'
            ],
            [
                'id' => 'test_2', 
                'fileno' => 'TEST-002',
                'description' => 'Test File Number 2',
                'plot_no' => '456',
                'lga' => 'Test LGA 2',
                'location' => 'Test Location 2',
                'source' => 'test'
            ]
        ]
    ]);
});

// File Tracking API Routes - KLAES Lands Module File Tracker
use App\Http\Controllers\FileTrackingController;

// Main file tracking endpoints
Route::get('/file-trackings', [FileTrackingController::class, 'index']);
Route::post('/file-trackings', [FileTrackingController::class, 'store']);
Route::get('/file-trackings/{id}', [FileTrackingController::class, 'show']);
Route::put('/file-trackings/{id}', [FileTrackingController::class, 'update']);
Route::delete('/file-trackings/{id}', [FileTrackingController::class, 'destroy']);

// Movement tracking
Route::post('/file-trackings/{id}/move', [FileTrackingController::class, 'addMovement']);

// RFID integration endpoints
Route::post('/rfid/register', [FileTrackingController::class, 'registerRfid']);
Route::get('/rfid/scan/{tag}', [FileTrackingController::class, 'scanRfid']);
Route::get('/rfid/report', [FileTrackingController::class, 'generateReport']);

// Batch operations
Route::post('/file-trackings/batch/overdue', [FileTrackingController::class, 'batchUpdateOverdue']);