<?php

use App\Http\Controllers\RecertificationController;

// Additional Recertification Routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'recertification'], function () {
    Route::get('/data', [RecertificationController::class, 'getApplicationsData'])->name('recertification.data');
    Route::get('/next-file-number', [RecertificationController::class, 'getNextFileNumber'])->name('recertification.nextFileNumber');
    Route::get('/{id}/view', [RecertificationController::class, 'view'])->name('recertification.view');
    Route::get('/{id}/details', [RecertificationController::class, 'details'])->name('recertification.details');
    Route::get('/{id}/edit', [RecertificationController::class, 'edit'])->name('recertification.edit');
    Route::put('/{id}', [RecertificationController::class, 'update'])->name('recertification.update');
    Route::delete('/{id}', [RecertificationController::class, 'destroy'])->name('recertification.destroy');
});