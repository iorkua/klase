<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GisDataController;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\SurveyCadastralAttributionController;
use App\Http\Controllers\SurveyAttributionController;
use App\Http\Controllers\GisController;
use App\Http\Controllers\PlanningRecommendationController;
use App\Http\Controllers\SubPlanningRecommendationController;
use App\Http\Controllers\FileIndexController;
use App\Http\Controllers\PageTypingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyRecordController;
use App\Http\Controllers\ScanningController;
use App\Http\Controllers\FilearchiveController;
use App\Http\Controllers\FileTrackerController;
use App\Http\Controllers\SltroverviewController;
use App\Http\Controllers\LegalsearchreportsController;
use App\Http\Controllers\SltrApplicationController;
use App\Http\Controllers\PrintLabelController;
use App\Http\Controllers\InstrumentRegistrationController;
use App\Http\Controllers\OnPremiseController;
use App\Http\Controllers\SLTRApprovalController;
use App\Http\Controllers\SLTRDeedsRegController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\ApplicationFormController;
use App\Http\Controllers\SltrFieldDataController;
use App\Http\Controllers\SurveyPlanExtractionController;
use App\Http\Controllers\RecertificationController;

// Public routes
Route::get('/custom-public', function () {
    return 'This is a public custom route';
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // GIS related routes
    Route::prefix('gis')->name('gis.')->group(function () {
        Route::get('/', [GisDataController::class, 'index'])->name('index');
        Route::get('/create', [GisDataController::class, 'create'])->name('create');
        Route::post('/store', [GisDataController::class, 'store'])->name('store');

        // Add this new route for fetching unit file numbers
        Route::get('/get-units', [GisDataController::class, 'getUnits'])->name('get-units');

        // Important: This specific route must come BEFORE the wildcard {id} route
        Route::get('/search-files', [GisDataController::class, 'searchFiles'])->name('search-files');
        // Wildcard routes should come after more specific routes
        Route::get('/{id}', [GisDataController::class, 'show'])->name('view');
        Route::get('/{id}/edit', [GisDataController::class, 'edit'])->name('edit');
        Route::put('/{id}', [GisDataController::class, 'update'])->name('update');
        Route::delete('/{id}', [GisDataController::class, 'destroy'])->name('destroy');
    });  
    
    
    Route::prefix('gis_record')->name('gis_record.')->group(function () {
        Route::get('/index', [GisController::class, 'index'])->name('index');
        Route::get('/create', [GisController::class, 'create'])->name('create');
        Route::post('/store', [GisController::class, 'store'])->name('store');

        // Add this new route for fetching unit file numbers
        Route::get('/get-units', [GisController::class, 'getUnits'])->name('get-units');

        // Important: This specific route must come BEFORE the wildcard {id} route
        Route::get('/search-files', [GisController::class, 'searchFiles'])->name('search-files');
        // Wildcard routes should come after more specific routes
        Route::get('/{id}', [GisController::class, 'show'])->name('view');
        Route::get('/{id}/edit', [GisController::class, 'edit'])->name('edit');
        Route::put('/{id}', [GisController::class, 'update'])->name('update');
        Route::delete('/{id}', [GisController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('attribution')->group(function () {
        // Routes for AttributionController
        Route::get('/', [AttributionController::class, 'Attributions'])->name('attribution.index');
        Route::get('/create', [AttributionController::class, 'create'])->name('attribution.create');
        Route::post('/store', [AttributionController::class, 'store'])->name('attribution.store');
        Route::get('/update-survey/{id}', [AttributionController::class, 'editSurvey'])->name('attribution.update-survey');
        Route::post('/update-survey', [AttributionController::class, 'updateSurvey'])->name('attribution.update-survey');
        Route::post('/search-fileno', [AttributionController::class, 'searchFileNo'])->name('attribution.search-fileno');

        // New routes for Primary Survey selection
        Route::post('/fetch-primary-surveys', [AttributionController::class, 'fetchPrimarySurveys'])->name('attribution.fetch-primary-surveys');
        Route::get('/primary-survey-details/{id}', [AttributionController::class, 'getPrimarySurveyDetails'])->name('attribution.primary-survey-details');
    });

    Route::prefix('survey_cadastral')->group(function () {
        // Routes for SurveyCadastralAttributionController
        Route::get('/', [SurveyCadastralAttributionController::class, 'Attributions'])->name('survey_cadastral.index');
        Route::get('/create', [SurveyCadastralAttributionController::class, 'create'])->name('survey_cadastral.create');
        Route::post('/store', [SurveyCadastralAttributionController::class, 'store'])->name('survey_cadastral.store');
        Route::get('/update-survey/{id}', [SurveyCadastralAttributionController::class, 'editSurvey'])->name('survey_cadastral.update-survey');
        Route::post('/update-survey', [SurveyCadastralAttributionController::class, 'updateSurvey'])->name('survey_cadastral.update-survey');
        Route::post('/search-fileno', [SurveyCadastralAttributionController::class, 'searchFileNo'])->name('survey_cadastral.search-fileno');

        // New routes for Primary Survey selection
        Route::post('/fetch-primary-surveys', [SurveyCadastralAttributionController::class, 'fetchPrimarySurveys'])->name('survey_cadastral.fetch-primary-surveys');
        Route::get('/primary-survey-details/{id}', [SurveyCadastralAttributionController::class, 'getPrimarySurveyDetails'])->name('survey_cadastral.primary-survey-details');
        
        // Additional action routes
        Route::get('/edit/{id}', [SurveyCadastralAttributionController::class, 'edit'])->name('survey_cadastral.edit');
        Route::get('/view-plan/{id}', [SurveyCadastralAttributionController::class, 'viewPlan'])->name('survey_cadastral.view-plan');
        Route::delete('/delete/{id}', [SurveyCadastralAttributionController::class, 'destroy'])->name('survey_cadastral.delete');
    });  
    
    Route::prefix('survey_record')->group(function () {
        // Routes for SurveyAttributionController
        Route::get('/', [SurveyAttributionController::class, 'Attributions'])->name('survey_record.index');
        Route::get('/create', [SurveyAttributionController::class, 'create'])->name('survey_record.create');
        Route::post('/store', [SurveyAttributionController::class, 'store'])->name('survey_record.store');
        Route::get('/update-survey/{id}', [SurveyAttributionController::class, 'editSurvey'])->name('survey_record.update-survey');
        Route::post('/update-survey', [SurveyAttributionController::class, 'updateSurvey'])->name('survey_record.update-survey');
        Route::post('/search-fileno', [SurveyAttributionController::class, 'searchFileNo'])->name('survey_record.search-fileno');

        // New routes for Primary Survey selection
        Route::post('/fetch-primary-surveys', [SurveyAttributionController::class, 'fetchPrimarySurveys'])->name('survey_record.fetch-primary-surveys');
        Route::get('/primary-survey-details/{id}', [SurveyAttributionController::class, 'getPrimarySurveyDetails'])->name('survey_record.primary-survey-details');
        
        // Additional action routes
        Route::get('/edit/{id}', [SurveyAttributionController::class, 'edit'])->name('survey_record.edit');
        Route::get('/view-plan/{id}', [SurveyAttributionController::class, 'viewPlan'])->name('survey_record.view-plan');
        Route::delete('/delete/{id}', [SurveyAttributionController::class, 'destroy'])->name('survey_record.delete');
    });
    

    Route::prefix('pr_memos')->group(function () {
        // Routes for PlanningRecommendation memo
        Route::get('/approval', [PlanningRecommendationController::class, 'ApprovalMome'])->name('pr_memos.approval');
        Route::post('/store', [PlanningRecommendationController::class, 'ApprovalMomeSave'])->name('pr_memos.store'); 
        Route::post('/save-observations', [PlanningRecommendationController::class, 'saveAdditionalObservations'])->name('pr_memos.save-observations');
        
        Route::get('/declination', [PlanningRecommendationController::class, 'Declination'])->name('pr_memos.declination');
        Route::post('/declination/store', [PlanningRecommendationController::class, 'DeclinationSave'])->name('pr_memos.declination.store');
    });  
    
    
    Route::prefix('sub_pr_memos')->group(function () {
        // Routes for SubPlanningRecommendation memo
        Route::get('/approval', [SubPlanningRecommendationController::class, 'ApprovalMome'])->name('sub_pr_memos.approval');
        Route::post('/store', [SubPlanningRecommendationController::class, 'ApprovalMomeSave'])->name('sub_pr_memos.store'); 
        Route::post('/save-observations', [SubPlanningRecommendationController::class, 'saveAdditionalObservations'])->name('sub_pr_memos.save-observations');
        
        Route::get('/declination', [SubPlanningRecommendationController::class, 'Declination'])->name('sub_pr_memos.declination');
        Route::post('/declination/store', [SubPlanningRecommendationController::class, 'DeclinationSave'])->name('sub_pr_memos.declination.store');
    });

    Route::prefix('fileindexing')->group(function () {
        Route::get('/', [FileIndexController::class, 'index'])->name('fileindexing.index');
        Route::get('/create', [FileIndexController::class, 'create'])->name('fileindexing.create');
        Route::post('/store', [FileIndexController::class, 'store'])->name('fileindexing.store');
        Route::get('/{id}', [FileIndexController::class, 'show'])->name('fileindexing.show');
        Route::get('/{id}/edit', [FileIndexController::class, 'edit'])->name('fileindexing.edit');
        Route::put('/{id}', [FileIndexController::class, 'update'])->name('fileindexing.update');
        Route::delete('/{id}', [FileIndexController::class, 'destroy'])->name('fileindexing.destroy');
        
        // AJAX routes
        Route::get('/search/applications', [FileIndexController::class, 'searchApplications'])->name('fileindexing.search-applications');
        Route::get('/list/file-indexings', [FileIndexController::class, 'getFileIndexingList'])->name('fileindexing.list');
    });
   
    Route::prefix('pagetyping')->group(function () {
        Route::get('/', [PageTypingController::class, 'index'])->name('pagetyping.index');
        Route::get('/create', [PageTypingController::class, 'create'])->name('pagetyping.create');
        Route::post('/store', [PageTypingController::class, 'store'])->name('pagetyping.store');
        Route::get('/{id}', [PageTypingController::class, 'show'])->name('pagetyping.show');
        Route::get('/{id}/edit', [PageTypingController::class, 'edit'])->name('pagetyping.edit');
        Route::put('/{id}', [PageTypingController::class, 'update'])->name('pagetyping.update');
        Route::delete('/{id}', [PageTypingController::class, 'destroy'])->name('pagetyping.destroy');
        
        // AJAX routes
        Route::post('/save-single', [PageTypingController::class, 'saveSingle'])->name('pagetyping.save-single');
        Route::get('/list/page-typings', [PageTypingController::class, 'getPageTypings'])->name('pagetyping.list');
    });
    
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Property Record Routes - Keep only these relevant routes
        Route::prefix('propertycard')->group(function () {
        Route::get('/', [PropertyRecordController::class, 'index'])->name('propertycard.index');
    });

    Route::prefix('property-records')->name('property-records.')->group(function () {
        Route::get('/{id}', [PropertyRecordController::class, 'show'])->name('show');
        Route::post('/', [PropertyRecordController::class, 'store'])->name('store');
        Route::put('/{id}', [PropertyRecordController::class, 'update'])->name('update');
        Route::delete('/{id}', [PropertyRecordController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('scanning')->group(function () {
        Route::get('/', [ScanningController::class, 'index'])->name('scanning.index');
        Route::post('/upload', [ScanningController::class, 'upload'])->name('scanning.upload');
        Route::get('/view/{id}', [ScanningController::class, 'view'])->name('scanning.view');
        Route::delete('/delete/{id}', [ScanningController::class, 'delete'])->name('scanning.delete');
        Route::put('/update-details/{id}', [ScanningController::class, 'updateDetails'])->name('scanning.update-details');
        
        // AJAX routes
        Route::get('/list/scanned-files', [ScanningController::class, 'getScannedFiles'])->name('scanning.list');
    });

    Route::prefix('filearchive')->group(function () {
        Route::get('/', [FilearchiveController::class, 'index'])->name('filearchive.index');
        Route::post('/upload', [FilearchiveController::class, 'upload'])->name('filearchive.upload');
        Route::get('/view/{id}', [FilearchiveController::class, 'view'])->name('filearchive.view');
        Route::delete('/delete/{id}', [FilearchiveController::class, 'delete'])->name('filearchive.delete');
    });

    Route::prefix('filetracker')->group(function () {
        Route::get('/', [FileTrackerController::class, 'index'])->name('filetracker.index');
        Route::get('/print', [FileTrackerController::class, 'print'])->name('filetracker.print');
        Route::post('/upload', [FileTrackerController::class, 'upload'])->name('filetracker.upload');
        Route::get('/view/{id}', [FileTrackerController::class, 'view'])->name('filetracker.view');
        Route::delete('/delete/{id}', [FileTrackerController::class, 'delete'])->name('filetracker.delete');
    });

    Route::prefix('sltroverview')->group(function () {
        Route::get('/', [SltroverviewController::class, 'index'])->name('sltroverview.index');
   
    });

    Route::prefix('legalsearchreports')->group(function () {
        Route::get('/', [LegalsearchreportsController::class, 'index'])->name('legalsearchreports.index');
    });

    Route::prefix('sltrapplication')->group(function () {
        Route::get('/', [SltrApplicationController::class, 'index'])->name('sltrapplication.index');
    });

    Route::prefix('printlabel')->group(function () {
        Route::get('/', [PrintLabelController::class, 'index'])->name('printlabel.index');
    });


    
    Route::prefix('instrument_registration')->group(function () {
        Route::get('/', [InstrumentRegistrationController::class, 'InstrumentRegistration'])->name('instrument_registration.index');
        
        // Fix generate route to point to generate method, not save
        Route::get('/generate/{id}', [InstrumentRegistrationController::class, 'generate'])->name('instrument_registration.generate');
        
        // Save route (POST) 
        Route::post('/save', [InstrumentRegistrationController::class, 'save'])->name('instrument_registration.save');
        
        // View route is correct but make sure it's accessible
        Route::get('/view/{id}', [InstrumentRegistrationController::class, 'view'])->name('instrument_registration.view');

        // New endpoints for direct Instrument registration
        Route::get('/get-batch-data', [InstrumentRegistrationController::class, 'getBatchData'])->name('instrument_registration.get-batch-data');
        Route::get('/get-next-serial', [InstrumentRegistrationController::class, 'getNextSerialNumber'])->name('instrument_registration.get-next-serial');
        Route::post('/register-single', [InstrumentRegistrationController::class, 'registerSingle'])->name('instrument_registration.register-single');
        Route::post('/register-batch', [InstrumentRegistrationController::class, 'registerBatch'])->name('instrument_registration.register-batch');
        Route::post('/decline', [InstrumentRegistrationController::class, 'declineRegistration'])->name('instrument_registration.decline');
        
        // New route for checking registration status
        Route::get('/check-registration-status', [InstrumentRegistrationController::class, 'checkRegistrationStatus'])->name('instrument_registration.check-registration-status');
        
        // New route for checking overall completion status
        Route::get('/overall-completion-status', [InstrumentRegistrationController::class, 'getOverallCompletionStatus'])->name('instrument_registration.overall-completion-status');
        
        // Debug route
        Route::get('/debug', [InstrumentRegistrationController::class, 'debug'])->name('instrument_registration.debug');
    });

    Route::prefix('onpremise')->group(function () {
        Route::get('/', [OnPremiseController::class, 'index'])->name('onpremise.index');
    });


    Route::prefix('sltrapproval')->group(function () {
        Route::get('/deeds', [SLTRApprovalController::class, 'deeds'])->name('sltrapproval.deeds');
    }); 


    Route::prefix('sltrdeedsreg')->group(function () {
        Route::get('/', [SLTRDeedsRegController::class, 'index'])->name('sltrdeedsreg.index');
    });
    
    
    Route::prefix('coroi')->group(function () {
        Route::get('/', [InstrumentController::class, 'Coroi'])->name('coroi.index');
    });

    // Add any additional routes here
    Route::prefix('sltr_application_form')->group(function () {
        Route::get('/residential', [ApplicationFormController::class, 'residential'])->name('sltr_application_form.residential');
    });
    Route::prefix('sltr_field_data')->group(function () {
        Route::get('/', [SltrFieldDataController::class, 'FieldData'])->name('sltr_field_data.index');
    });

    Route::prefix('survey_plan_extraction')->group(function () {
        Route::get('/', [SurveyPlanExtractionController::class, 'index'])->name('survey_plan_extraction.index');
    });

    Route::prefix('recertification')->group(function () {
        Route::get('/', [RecertificationController::class, 'index'])->name('recertification.index');
    });

});
// Additional attribution routes
Route::get('/attribution/edit/{id}', [App\Http\Controllers\AttributionController::class, 'edit'])->name('attribution.edit');
Route::put('/attribution/update/{id}', [App\Http\Controllers\AttributionController::class, 'update'])->name('attribution.update');
Route::get('/attribution/view-plan/{id}', [App\Http\Controllers\AttributionController::class, 'viewPlan'])->name('attribution.view-plan');
Route::delete('/attribution/{id}', [App\Http\Controllers\AttributionController::class, 'destroy'])->name('attribution.destroy');
