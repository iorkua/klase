<?php

use App\Http\Controllers\RecertificationController;
use App\Http\Controllers\CertificationController;
use Illuminate\Support\Facades\Route;

// Recertification Routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'recertification'], function () {
    Route::get('/', [RecertificationController::class, 'index'])->name('recertification.index');
    Route::get('/data', [RecertificationController::class, 'getApplicationsData'])->name('recertification.data');
    Route::get('/application', function() {
        return view('recertification.application_standalone_clean');
    })->name('recertification.application');
    Route::post('/application/store', [RecertificationController::class, 'store'])->name('recertification.application.store');
    Route::get('/migrate', [RecertificationController::class, 'migrate'])->name('recertification.migrate');
    Route::post('/migrate/upload', [RecertificationController::class, 'uploadMigration'])->name('recertification.migrate.upload');
    Route::get('/migrate/template', [RecertificationController::class, 'downloadTemplate'])->name('recertification.migrate.template');
    Route::get('/verification-sheet', [RecertificationController::class, 'verificationSheet'])->name('recertification.verification-sheet');
    Route::get('/verification-data', [RecertificationController::class, 'getVerificationData'])->name('recertification.verification-data');
    Route::get('/next-file-number', [RecertificationController::class, 'getNextFileNumber'])->name('recertification.nextFileNumber');
    Route::get('/{id}/view', [RecertificationController::class, 'view'])->name('recertification.view');
    Route::get('/{id}/details', [RecertificationController::class, 'details'])->name('recertification.details');
    Route::get('/{id}/edit', [RecertificationController::class, 'edit'])->name('recertification.edit');
    Route::put('/{id}', [RecertificationController::class, 'update'])->name('recertification.update');
    Route::delete('/{id}', [RecertificationController::class, 'destroy'])->name('recertification.destroy');
    
    // Certification Management Routes
    Route::get('/certification', [CertificationController::class, 'index'])->name('recertification.certification');
    Route::get('/certification-data', [CertificationController::class, 'getCertificationData'])->name('recertification.certification-data');
    Route::get('/{id}/cor', [CertificationController::class, 'viewCoR'])->name('recertification.cor');
    Route::post('/{id}/generate-cofo-front', [CertificationController::class, 'generateCofoFrontPage'])->name('recertification.generate-cofo-front');
    Route::get('/{id}/cofo-front-page', [CertificationController::class, 'viewCofoFrontPage'])->name('recertification.cofo-front-page');
    Route::get('/{id}/tdp', [CertificationController::class, 'viewTDP'])->name('recertification.tdp');
    Route::get('/{id}/cofo', [CertificationController::class, 'viewCofo'])->name('recertification.cofo');
});