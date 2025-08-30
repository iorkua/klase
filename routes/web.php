<?php
// use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\AuthPageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubApplicationController;
use App\Http\Controllers\ApplicationMotherController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\LegalSearchController;
use App\Http\Controllers\ResidentialController;
use App\Http\Controllers\eRegistryController;
use App\Http\Controllers\DeedsController;
use App\Http\Controllers\ConveyanceController;
use App\Http\Controllers\SaveMainAppController;
use App\Http\Controllers\FileIndexingController;
use App\Http\Controllers\FileScanningController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\LandingController;

use App\Http\Controllers\GisController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\InstrumentRegistrationController;
use App\Http\Controllers\StInstrumentRegistrationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
require __DIR__ . '/auth.php';
require __DIR__ . '/recertification_routes.php';
require __DIR__ . '/file_numbers.php';
require __DIR__ . '/file_decommissioning.php';
Route::get('/', [HomeController::class, 'index'])->middleware(
    [
        'XSS',
    ]
);
Route::get('home', [HomeController::class, 'index'])->name('home')->middleware(
    [
        'XSS',
    ]
);
Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard')->middleware(
    [
        'XSS',
    ]
);
//-------------------------------User-------------------------------------------
Route::resource('users', UserController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
// User levels API endpoint
Route::get('users/get-levels/{userTypeId}', [UserController::class, 'getUserLevels'])->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------Subscription-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('subscriptions', SubscriptionController::class);
        Route::get('coupons/history', [CouponController::class, 'history'])->name('coupons.history');
        Route::delete('coupons/history/{id}/destroy', [CouponController::class, 'historyDestroy'])->name('coupons.history.destroy');
        Route::get('coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
        Route::resource('coupons', CouponController::class);
        Route::get('subscription/transaction', [SubscriptionController::class, 'transaction'])->name('subscription.transaction');
    Route::post('subscription/{id}/{user_id}/manual-assign-package', [PaymentController::class, 'subscriptionManualAssignPackage'])->name('subscription.manual_assign_package');
    }
);
//-------------------------------Subscription Payment-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::post('subscription/{id}/stripe/payment', [SubscriptionController::class, 'stripePayment'])->name('subscription.stripe.payment');
    }
);
//-------------------------------Settings-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('settings', [SettingController::class,'index'])->name('setting.index');
        Route::post('settings/account', [SettingController::class,'accountData'])->name('setting.account');
        Route::delete('settings/account/delete', [SettingController::class,'accountDelete'])->name('setting.account.delete');
        Route::post('settings/password', [SettingController::class,'passwordData'])->name('setting.password');
        Route::post('settings/general', [SettingController::class,'generalData'])->name('setting.general');
        Route::post('settings/smtp', [SettingController::class,'smtpData'])->name('setting.smtp');
        Route::get('settings/smtp-test', [SettingController::class, 'smtpTest'])->name('setting.smtp.test');
        Route::post('settings/smtp-test', [SettingController::class, 'smtpTestMailSend'])->name('setting.smtp.testing');
        Route::post('settings/payment', [SettingController::class,'paymentData'])->name('setting.payment');
        Route::post('settings/site-seo', [SettingController::class,'siteSEOData'])->name('setting.site.seo');
        Route::post('settings/google-recaptcha', [SettingController::class,'googleRecaptchaData'])->name('setting.google.recaptcha');
        Route::post('settings/company', [SettingController::class,'companyData'])->name('setting.company');
        Route::post('settings/2fa', [SettingController::class, 'twofaEnable'])->name('setting.twofa.enable');
        Route::get('footer-setting', [SettingController::class, 'footerSetting'])->name('footerSetting');
        Route::post('settings/footer', [SettingController::class,'footerData'])->name('setting.footer');
        Route::get('language/{lang}', [SettingController::class,'lanquageChange'])->name('language.change');
        Route::post('theme/settings', [SettingController::class,'themeSettings'])->name('theme.settings');
    }
);
Route::group(
    [
        'middleware' => [
            'auth',
        ],
    ],
    function () {
        Route::post('settings/payment', [SettingController::class, 'paymentData'])->name('setting.payment');
    }
);
//-------------------------------Role & Permissions-------------------------------------------
Route::resource('permission', PermissionController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::resource('role', RoleController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------Note-------------------------------------------
Route::resource('note', NoticeBoardController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------Contact-------------------------------------------
Route::resource('contact', ContactController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------logged History-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('logged/history', [UserController::class, 'loggedHistory'])->name('logged.history');
        Route::get('logged/{id}/history/show', [UserController::class, 'loggedHistoryShow'])->name('logged.history.show');
        Route::delete('logged/{id}/history', [UserController::class, 'loggedHistoryDestroy'])->name('logged.history.destroy');
    }
);

//-------------------------------User Activity Logs-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('user-activity-logs', [App\Http\Controllers\UserActivityLogController::class, 'index'])->name('user-activity-logs.index');
        Route::get('user-activity-logs/{id}', [App\Http\Controllers\UserActivityLogController::class, 'show'])->name('user-activity-logs.show');
        Route::delete('user-activity-logs/{id}', [App\Http\Controllers\UserActivityLogController::class, 'destroy'])->name('user-activity-logs.destroy');
        Route::post('user-activity-logs/logout-user/{userId}', [App\Http\Controllers\UserActivityLogController::class, 'logoutUser'])->name('user-activity-logs.logout-user');
        Route::get('user-activity-logs/stats/data', [App\Http\Controllers\UserActivityLogController::class, 'getStats'])->name('user-activity-logs.stats');
        Route::get('user-activity-logs/online/users', [App\Http\Controllers\UserActivityLogController::class, 'getOnlineUsers'])->name('user-activity-logs.online-users');
        Route::get('user-activity-logs/chart/data', [App\Http\Controllers\UserActivityLogController::class, 'getChartData'])->name('user-activity-logs.chart-data');
        Route::get('user-activity-logs/export/csv', [App\Http\Controllers\UserActivityLogController::class, 'export'])->name('user-activity-logs.export');
        Route::post('user-activity-logs/bulk-delete', [App\Http\Controllers\UserActivityLogController::class, 'bulkDelete'])->name('user-activity-logs.bulk-delete');
        Route::post('user-activity-logs/clean-old', [App\Http\Controllers\UserActivityLogController::class, 'cleanOldLogs'])->name('user-activity-logs.clean-old');
        Route::get('user-activity-logs/settings/get', [App\Http\Controllers\UserActivityLogController::class, 'getSettings'])->name('user-activity-logs.settings.get');
        Route::post('user-activity-logs/settings/save', [App\Http\Controllers\UserActivityLogController::class, 'saveSettings'])->name('user-activity-logs.settings.save');
        Route::get('user-activity-logs/settings/global', [App\Http\Controllers\UserActivityLogController::class, 'getGlobalSettings'])->name('user-activity-logs.settings.global');
        Route::post('user-activity-logs/settings/global', [App\Http\Controllers\UserActivityLogController::class, 'saveGlobalSettings'])->name('user-activity-logs.settings.global.save');
        Route::get('user-activity-logs/cleanup/status', [App\Http\Controllers\UserActivityLogController::class, 'getCleanupStatus'])->name('user-activity-logs.cleanup.status');
        Route::post('user-activity-logs/cleanup/auto', [App\Http\Controllers\UserActivityLogController::class, 'runAutomaticCleanup'])->name('user-activity-logs.cleanup.auto');
    }
);
//-------------------------------Document-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('document/history', [DocumentController::class, 'history'])->name('document.history');
        Route::resource('document', DocumentController::class);
        Route::get('my-document', [DocumentController::class, 'myDocument'])->name('document.my-document');
        Route::get('document/{id}/comment', [DocumentController::class, 'comment'])->name('document.comment');
        Route::post('document/{id}/comment', [DocumentController::class, 'commentData'])->name('document.comment.store');
        Route::get('document/{id}/reminder', [DocumentController::class, 'reminder'])->name('document.reminder');
        Route::get('document/{id}/add-reminder', [DocumentController::class, 'addReminder'])->name('document.add.reminder');
        Route::get('document/{id}/version-history', [DocumentController::class, 'versionHistory'])->name('document.version.history');
        Route::post('document/{id}/version-history', [DocumentController::class, 'newVersion'])->name('document.new.version');
        Route::get('document/{id}/share', [DocumentController::class, 'shareDocument'])->name('document.share');
        Route::post('document/{id}/share', [DocumentController::class, 'shareDocumentData'])->name('document.share.store');
        Route::get('document/{id}/add-share', [DocumentController::class, 'addshareDocumentData'])->name('document.add.share');
        Route::delete('document/{id}/share/destroy', [DocumentController::class, 'shareDocumentDelete'])->name('document.share.destroy');
        Route::get('document/{id}/send-email', [DocumentController::class, 'sendEmail'])->name('document.send.email');
        Route::post('document/{id}/send-email', [DocumentController::class, 'sendEmailData'])->name('document.send.email.store');
        Route::get('logged/history', [DocumentController::class, 'loggedHistory'])->name('logged.history');
        Route::get('logged/{id}/history/show', [DocumentController::class, 'loggedHistoryShow'])->name('logged.history.show');
        Route::delete('logged/{id}/history', [DocumentController::class, 'loggedHistoryDestroy'])->name('logged.history.destroy');
    }
);
//-------------------------------Reminder-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('reminder', ReminderController::class);
        Route::get('my-reminder', [ReminderController::class, 'myReminder'])->name('my-reminder');
    }
);
//-------------------------------Category, Sub Category & Tag-------------------------------------------
Route::get('category/{id}/sub-category', [CategoryController::class, 'getSubcategory'])->name('category.sub-category');
Route::resource('category', CategoryController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::resource('sub-category', SubCategoryController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::resource('tag', TagController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------Plan Payment-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::post('subscription/{id}/bank-transfer', [PaymentController::class, 'subscriptionBankTransfer'])->name('subscription.bank.transfer');
        Route::get('subscription/{id}/bank-transfer/action/{status}', [PaymentController::class, 'subscriptionBankTransferAction'])->name('subscription.bank.transfer.action');
        Route::post('subscription/{id}/paypal', [PaymentController::class, 'subscriptionPaypal'])->name('subscription.paypal');
        Route::get('subscription/{id}/paypal/{status}', [PaymentController::class, 'subscriptionPaypalStatus'])->name('subscription.paypal.status');
        Route::get('subscription/flutterwave/{sid}/{tx_ref}', [PaymentController::class, 'subscriptionFlutterwave'])->name('subscription.flutterwave');
    }
);
//-------------------------------Notification-------------------------------------------
Route::resource('notification', NotificationController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('email-verification/{token}', [VerifyEmailController::class, 'verifyEmail'])->name('email-verification')->middleware(
    [
        'XSS',
    ]
);
//-------------------------------FAQ-------------------------------------------
Route::resource('FAQ', FAQController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------Home Page-------------------------------------------
Route::resource('homepage', HomePageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------FAQ-------------------------------------------
Route::resource('pages', PageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------FAQ-------------------------------------------
Route::resource('authPage', AuthPageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('page/{slug}', [PageController::class, 'page'])->name('page');
//-------------------------------FAQ-------------------------------------------
 
// Application Mother routes
Route::get('/sectionaltitling', [ApplicationMotherController::class, 'index'])->name('sectionaltitling.index');
Route::get('/sectionaltitling/landuse', [ApplicationMotherController::class, 'landuse'])->name('sectionaltitling.landuse'); 
Route::get('/sectionaltitling/create', [ApplicationMotherController::class, 'create'])->name('sectionaltitling.create');
Route::get('/sectionaltitling/sub_application', [ApplicationMotherController::class, 'subApplication'])->name('sectionaltitling.sub_application');
Route:: get('/sectionaltitling/generate_bill/{id?}', [ApplicationMotherController::class, 'GenerateBill'])->name('sectionaltitling.generate_bill');
 
Route::get('/sectionaltitling/AcceptLetter', [ApplicationMotherController::class, 'AcceptLetter'])
    ->name('sectionaltitling.AcceptLetter');
Route::get('/sectionaltitling/sub_applications', [ApplicationMotherController::class, 'Subapplications'])->name('sectionaltitling.sub_applications');
Route::get('/sectionaltitling/sub_application', [ApplicationMotherController::class, 'Subapplication'])->name('sectionaltitling.sub_application');
// Add this new route for storing sub-applications
Route::post('/sectionaltitling/storesub', [ApplicationMotherController::class, 'storeSub'])->name('sectionaltitling.storesub');
Route::post('/sectionaltitling', [ApplicationMotherController::class, 'store'])->name('sectionaltitling.store');
Route::get('/sectionaltitling/{id}/edit', [ApplicationMotherController::class, 'edit']);
Route::post('sectionaltitling/approve-sub', [ApplicationMotherController::class, 'approveSubApplication'])
    ->name('sectionaltitling.approveSubApplication');
Route::post('sectionaltitling/decline-sub', [ApplicationMotherController::class, 'declineSubApplication'])
    ->name('sectionaltitling.declineSubApplication');
Route::post('sectionaltitling/decision-sub', [ApplicationMotherController::class, 'decisionSubApplication'])
    ->name('sectionaltitling.decisionSubApplication');
Route::post('sectionaltitling/decision-mother', [ApplicationMotherController::class, 'decisionMotherApplication'])
    ->name('sectionaltitling.decisionMotherApplication');
// Sectional Titling routes
Route::post('/sectional-titling/planning-recommendation', [App\Http\Controllers\ApplicationMotherController::class, 'planningRecommendation'])->name('sectionaltitling.planningRecommendation');
Route::post('/sectional-titling/department-approval', [App\Http\Controllers\ApplicationMotherController::class, 'departmentApproval'])->name('sectionaltitling.departmentApproval');
Route::get('sectionaltitling/getFinancialData', [App\Http\Controllers\ApplicationMotherController::class, 'getFinancialData'])->name('sectionaltitling.getFinancialData');
// Add these routes in the appropriate section of your web.php file
Route::get('sectionaltitling/get-billing-data/{id}', [App\Http\Controllers\ApplicationMotherController::class, 'getBillingData'])->name('sectionaltitling.getBillingData');
Route::get('sectionaltitling/get-billing-data2/{id}', [App\Http\Controllers\ApplicationMotherController::class, 'getBillingData2'])->name('sectionaltitling.getBillingData2');
Route::post('sectionaltitling/save-billing-data', [App\Http\Controllers\ApplicationMotherController::class, 'saveBillingData'])->name('sectionaltitling.saveBillingData');
Route::post('sectionaltitling/save-application-data', [App\Http\Controllers\ApplicationMotherController::class, 'saveApplicationData'])->name('sectionaltitling.saveApplicationData');
Route::get('sectionaltitling/viewrecorddetail',  [App\Http\Controllers\ApplicationMotherController::class, 'Veiwrecords'])->name('sectionaltitling.viewrecorddetail');
Route::get('sectionaltitling/edit/{id}', [App\Http\Controllers\ApplicationMotherController::class, 'edit'])->name('sectionaltitling.edit');
Route::put('sectionaltitling/update/{id}', [App\Http\Controllers\ApplicationMotherController::class, 'update'])->name('sectionaltitling.update');
Route::delete('sectionaltitling/delete/{id}', [App\Http\Controllers\ApplicationMotherController::class, 'delete'])->name('sectionaltitling.delete');
// Add this route in the appropriate section
Route::post('/sectionaltitling/save-eregistry', [eRegistryController::class, 'saveERegistry'])->name('sectionaltitling.saveERegistry');
 
// Add this fallback route for propertycard data
Route::get('/propertycard/data-fallback', function() {
    $sampleData = [
        [
            'id' => 1,
            'mlsfNo' => 'MLSF-001',
            'kangisFileNo' => 'KF-001',
            'currentAllottee' => 'Sample Allottee',
            'landUse' => 'Residential',
            'districtName' => 'Sample District',
        ],
        [
            'id' => 2,
            'mlsfNo' => 'MLSF-002',
            'kangisFileNo' => 'KF-002',
            'currentAllottee' => 'Another Allottee',
            'landUse' => 'Commercial',
            'districtName' => 'Another District',
        ]
    ];
    
    return response()->json([
        'draw' => 1,
        'recordsTotal' => count($sampleData),
        'recordsFiltered' => count($sampleData),
        'data' => $sampleData
    ]);
})->name('propertycard.data.fallback');
Route::group(['middleware' => 'web'], function () {
    Route::get('/legal_search', [LegalSearchController::class, 'index'])->name('legal_search.index');
    Route::post('/legal_search/search', [LegalSearchController::class, 'search'])->name('legal_search.search');
    Route::get('/legal_search/report', [LegalSearchController::class, 'report'])->name('legal_search.report');
    //Route::post('/legal_search', [LegalSearchController::class, 'store'])->name('legal_search.store');
    Route::get('/legal_search/legal_search_report', [LegalSearchController::class, 'legal_search_report'])->name('legal_search.legal_search_report');
    // Add alias for JS compatibility
    Route::post('/legal_search/search', [LegalSearchController::class, 'search'])->name('legalsearch.search');
});
 
Route::post('/deeds/insert', [DeedsController::class, 'insert'])->name('deeds.insert');
Route::get('/deeds/getdeedsdublicate', [DeedsController::class, 'getDeedsDublicate'])->name('deeds.getDeedsDublicate');
Route::post('/conveyance/update', [ConveyanceController::class, 'updateConveyance'])->name('conveyance.update');
Route::get('/sectionaltitling/generate-bill/{id?}', [SubApplicationController::class, 'GenerateBill'])->name('sectionaltitling.sub.generate_bill');
Route::get('/sectionaltitling/generate-bill', [SubApplicationController::class, 'GenerateBill'])->name('sectionaltitling.generate_bill_no_id');
Route::get('/subapplications/{id}', [SubApplicationController::class, 'getSubApplication']);
Route::get('sectionaltitling/viewrecorddetail_sub/{id?}',  [SubApplicationController::class, 'viewrecorddetail_sub'])->name('sectionaltitling.viewrecorddetail_sub');
// Fix the route definition - we have a duplicate route
Route::post('/sectionaltitling/store-mother-app', [SaveMainAppController::class, 'storeMotherApp'])
    ->name('sectionaltitling.storeMotherApp');
// Remove or comment out the duplicate route
// Route::post('/sectionaltitling', [SaveMainAppController::class, 'storeMotherApp'])->name('sectionaltitling.storeMotherApp');
// Instrument Registration routes
Route::group(['middleware' => ['auth'], 'prefix' => 'instrument_registration'], function () {
    Route::get('/', [InstrumentRegistrationController::class, 'InstrumentRegistration'])->name('instrument_registration.index');
    Route::get('get-batch-data', [InstrumentRegistrationController::class, 'getBatchData']);
    Route::get('get-next-serial', [InstrumentRegistrationController::class, 'getNextSerialNumber']);
    Route::post('register-batch', [InstrumentRegistrationController::class, 'registerBatch']);
    Route::post('register-single', [InstrumentRegistrationController::class, 'registerSingle']);
    Route::post('decline', [InstrumentRegistrationController::class, 'declineRegistration']);
    Route::get('check-registration-status', [InstrumentRegistrationController::class, 'checkRegistrationStatus']);
    Route::get('file-completion-status', [InstrumentRegistrationController::class, 'getFileCompletionStatus']);
    Route::get('overall-completion-status', [InstrumentRegistrationController::class, 'getOverallCompletionStatus']);
    Route::get('view/{id}', [InstrumentRegistrationController::class, 'view'])->name('instrument_registration.view');
    Route::get('edit/{id}', [InstrumentRegistrationController::class, 'edit'])->name('instrument_registration.edit');
    Route::put('update/{id}', [InstrumentRegistrationController::class, 'update'])->name('instrument_registration.update');
    Route::delete('delete/{id}', [InstrumentRegistrationController::class, 'destroy'])->name('instrument_registration.destroy');
});
Route::group(['middleware' => ['auth'], 'prefix' => 'st_deeds'], function () {
    Route::get('/', [StInstrumentRegistrationController::class, 'StInstrumentRegistration'])->name('st_deeds.index');
 
});
// Instrument routes
Route::group(['middleware' => ['auth'], 'prefix' => 'instruments'], function () {
    Route::get('/', [App\Http\Controllers\InstrumentController::class, 'index'])->name('instruments.index');
    Route::post('/store', [App\Http\Controllers\InstrumentController::class, 'store'])->name('instruments.store');
    Route::get('/create', [App\Http\Controllers\InstrumentController::class, 'create'])->name('instruments.create');
    Route::get('/generate-particulars', [App\Http\Controllers\InstrumentController::class, 'generateParticulars'])->name('instruments.generateParticulars');
});
// Add a fallback route for debugging
Route::fallback(function () {
    return response('Route not found. Please check the URL.', 404);
});
Route:: get('/sectionaltitling/generate_bill_sub/{id?}', [ApplicationMotherController::class, 'GenerateBill2'])->name('sectionaltitling.generate_bill_sub');
 
 
// FileIndexing routes
Route::impersonate();
Route::resource('fileindex', 'App\Http\Controllers\FileIndexingController');
Route::post('fileindex/save-cofo', 'App\Http\Controllers\FileIndexingController@saveCofO')->name('fileindex.save-cofo');
 
Route::post('fileindex/save-transaction', [FileIndexingController::class, 'savePropertyTransaction'])->name('fileindex.save-transaction');

// Check if a fileno has already been indexed
Route::get('/fileindex/check-indexed', [FileIndexingController::class, 'checkIndexed'])->name('fileindex.check-indexed');
 
// File Scanning
Route::get('/filescanning/index', [FileScanningController::class, 'index'])->name('filescanning.index');
Route::get('/filescanning/create', [FileScanningController::class, 'create'])->name('filescanning.create');
Route::get('/scanners', [ScannerController::class, 'getScanners'])->name('scanners.list');
Route::post('/scan', [ScannerController::class, 'scan'])->name('scanners.scan');
Route::post('/webcam-capture', [ScannerController::class, 'captureFromWebcam'])->name('scanners.webcam');
 
Route::get('/sectionaltitling', [\App\Http\Controllers\SectionalTitlingController::class, 'index'])->name('sectionaltitling.index');
Route::get('/sectionaltitling/primary', [\App\Http\Controllers\SectionalTitlingController::class, 'Primary'])->name('sectionaltitling.primary');
Route::get('/sectionaltitling/mother', [\App\Http\Controllers\SectionalTitlingController::class, 'mother'])->name('sectionaltitling.mother');
Route::get('/sectionaltitling/secondary', [\App\Http\Controllers\SectionalTitlingController::class, 'Secondary'])->name('sectionaltitling.secondary');
Route::get('/sectionaltitling/units', [\App\Http\Controllers\SectionalTitlingController::class, 'Units'])->name('sectionaltitling.units');
Route::get('/sectionaltitling/conveyance', [\App\Http\Controllers\SectionalTitlingController::class, 'conveyance'])->name('sectionaltitling.conveyance');
Route::get('/sectionaltitling/buyer-list/{id}', [\App\Http\Controllers\SectionalTitlingController::class, 'getBuyerList'])->name('sectionaltitling.buyerList');
Route::post('/sectionaltitling/save-cofo-details', [\App\Http\Controllers\SectionalTitlingController::class, 'saveCofoDetails'])->name('sectionaltitling.save-cofo-details');
// Soft delete sub-application
Route::delete('/sectionaltitling/sub/{id}', [\App\Http\Controllers\SectionalTitlingController::class, 'deleteSubapplication'])->name('sectionaltitling.sub.delete');
Route::get('/map', [\App\Http\Controllers\SectionalTitlingController::class, 'Map'])->name('map.index');
 
// Payment filtering route
Route::get('/programmes/payments/filter', [App\Http\Controllers\ProgrammesController::class, 'filterPayments'])->name('programmes.payments.filter');

// Payment action menu routes
Route::get('/programmes/payments/initial-bill-receipt/{fileNo}', [App\Http\Controllers\ProgrammesController::class, 'getInitialBillReceipt'])->name('programmes.payments.initial-bill-receipt');
Route::get('/programmes/payments/betterment-bill-reference/{fileNo}', [App\Http\Controllers\ProgrammesController::class, 'getBettermentBillReference'])->name('programmes.payments.betterment-bill-reference');
Route::post('/programmes/payments/save-betterment-bill-receipt', [App\Http\Controllers\ProgrammesController::class, 'saveBettermentBillReceipt'])->name('programmes.payments.save-betterment-bill-receipt');

// Unit application action menu routes
Route::get('/programmes/payments/bill-balance-reference/{fileNo}', [App\Http\Controllers\ProgrammesController::class, 'getBillBalanceReference'])->name('programmes.payments.bill-balance-reference');
Route::post('/programmes/payments/save-bill-balance-receipt', [App\Http\Controllers\ProgrammesController::class, 'saveBillBalanceReceipt'])->name('programmes.payments.save-bill-balance-receipt');

Route::get('/programmes/memo/{id}', 'App\Http\Controllers\ProgrammeController@viewMemo')->name('programmes.view_memo_detail');
//landing page
Route::get('/landing', [LandingController::class, 'index'])->name('landing.index');
Route::get('planning-recommendation/print/{id}', function($id) {
    $application = DB::connection('sqlsrv')->table('mother_applications')->where('id', $id)->first();
    if (!$application) {
        abort(404);
    }
    $surveyRecord = DB::connection('sqlsrv')->table('surveyCadastralRecord')
        ->where('application_id', $application->id)
        ->first();
    
    // Return view with print-specific data
    return view('actions.planning_recomm', [
        'application' => $application, 
        'surveyRecord' => $surveyRecord,
        'printMode' => true
    ]);
})->name('planning-recommendation.print');
// Route to mark welcome popup as shown
Route::post('/mark-welcome-popup-shown', function () {
    session(['show_welcome_popup' => false]);
    return response()->json(['success' => true]);
})->middleware('auth')->name('markWelcomePopupShown');
// Add this route wherever your other GIS routes are defined
Route::get('/gis/get-all-units', [GisController::class, 'getAllUnits'])->name('gis.get-all-units');
// COROI routes
Route::get('/coroi', [App\Http\Controllers\CoroiController::class, 'index'])->name('coroi.index');
Route::get('/coroi/search-by-fileno', [App\Http\Controllers\CoroiController::class, 'searchByFileno'])->name('coroi.search.fileno');
Route::get('/coroi/search', function() {
    return view('coroi.search');
})->name('coroi.search');
Route::get('/coroi/demo', function() {
    return view('coroi.demo');
})->name('coroi.demo');
Route::get('/coroi/debug', [App\Http\Controllers\CoroiController::class, 'debug'])->name('coroi.debug');
Route::get('/coroi/test', function() {
    return view('coroi.test');
})->name('coroi.test');
Route::get('/coroi/test-database', [App\Http\Controllers\CoroiController::class, 'testDatabase'])->name('coroi.test.database');
// User role routes for department-based filtering
Route::get('/user-roles/by-department', 'App\Http\Controllers\UserRoleController@getByDepartment')
    ->name('user-roles.by-department');
// Direct route for user roles by department - Fix for AJAX issue
Route::get('/get-roles-by-department/{departmentId}', function($departmentId) {
    try {
        // Get roles for the specific department
        $departmentRoles = \App\Models\UserRole::where('department_id', $departmentId)
                          ->where('is_active', 1)
                          ->get(['id', 'name', 'description']);
        
        // Also include general roles that don't have a specific department
        $generalRoles = \App\Models\UserRole::whereNull('department_id')
                       ->where('is_active', 1)
                       ->get(['id', 'name', 'description']);
        
        // Merge and return all roles
        $allRoles = $departmentRoles->merge($generalRoles);
        
        return response()->json($allRoles);
    } catch (\Exception $e) {
        \Log::error('Error fetching roles for department', [
            'department_id' => $departmentId,
            'error' => $e->getMessage()
        ]);
        
        return response()->json(['error' => 'Failed to load roles: ' . $e->getMessage()], 500);
    }
})->name('get.roles.by.department');
// Debug routes - only for development
if (app()->environment('local', 'development', 'staging')) {
    Route::get('/debug/roles-departments', [App\Http\Controllers\DebugController::class, 'rolesDepartments']);
}
// Debug route that directly returns roles for a department (bypass controller completely)
Route::get('/debug-roles/{departmentId}', function($departmentId) {
    try {
        // Log the request for debugging
        \Log::info('Debug route hit', ['department_id' => $departmentId]);
        
        // Get all user roles (with or without department_id)
        $roles = \App\Models\UserRole::where(function($query) use ($departmentId) {
            $query->where('department_id', $departmentId)
                  ->orWhereNull('department_id');
        })->where('is_active', 1)->get(['id', 'name']);
        
        // Log what we found
        \Log::info('Roles found', ['count' => $roles->count(), 'roles' => $roles->toArray()]);
        
        return response()->json($roles);
    } catch (\Exception $e) {
        \Log::error('Error in debug route', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
// Department and User Role Management Routes
Route::group(['middleware' => ['auth', 'XSS']], function () {
    // Department Routes
    Route::resource('departments', 'App\Http\Controllers\DepartmentController');
    
    // User Role Routes
    Route::resource('user-roles', 'App\Http\Controllers\UserRoleController');
});
// Debug routes for fixing user roles issue
Route::prefix('debug')->group(function() {
    Route::get('/check-roles', 'App\Http\Controllers\DebugController@checkUserRoles');
    Route::get('/add-sample-roles', 'App\Http\Controllers\DebugController@addSampleRoles');
    
    // Test user types and levels connection
    Route::get('/test-user-types', function() {
        try {
            // Test connection
            $userTypes = \App\Models\UserType::on('sqlsrv')->get();
            $userLevels = \App\Models\UserLevel::on('sqlsrv')->get();
            
            return response()->json([
                'status' => 'success',
                'user_types_count' => $userTypes->count(),
                'user_levels_count' => $userLevels->count(),
                'user_types' => $userTypes->toArray(),
                'user_levels' => $userLevels->toArray(),
                'operations_levels' => \App\Models\UserLevel::on('sqlsrv')
                    ->whereHas('userType', function($query) {
                        $query->where('name', 'Operations');
                    })->get()->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });
});
Route::get('/print_buyer_list', [ProgrammeController::class, 'printBuyerList']);
Route::get('/print_buyer_list/{applicationId}', [ProgrammeController::class, 'printBuyerList']);
// Final Conveyance Agreement
Route::get('actions/final-conveyance-agreement/{id}/{buyer_id?}', [\App\Http\Controllers\PrimaryActionsController::class, 'finalConveyanceAgreement'])->name('actions.final-conveyance-agreement');
Route::get('actions/final-conveyance/{id}', [\App\Http\Controllers\PrimaryActionsController::class, 'finalConveyance'])->name('actions.final-conveyance');
Route::post('actions/generate-final-conveyance', [\App\Http\Controllers\PrimaryActionsController::class, 'generateFinalConveyanceDocument'])->name('actions.generate-final-conveyance');
Route::get('actions/get-final-conveyance/{applicationId}', [\App\Http\Controllers\PrimaryActionsController::class, 'getFinalConveyance'])->name('actions.get-final-conveyance');
Route::get('actions/generate-final-conveyance-document/{id}', [\App\Http\Controllers\PrimaryActionsController::class, 'generateFinalConveyanceDocument'])->name('actions.generate-final-conveyance-document');
// Conveyance operations
Route::get('conveyance/{id}', [\App\Http\Controllers\PrimaryActionsController::class, 'getConveyance'])->name('conveyance.get');
Route::post('conveyance/update-buyer', [\App\Http\Controllers\PrimaryActionsController::class, 'updateSingleBuyer'])->name('conveyance.update.buyer');
Route::post('conveyance/delete-buyer', [\App\Http\Controllers\PrimaryActionsController::class, 'deleteBuyer'])->name('conveyance.delete.buyer');
// EDMS Workflow Routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'edms'], function () {
// Main EDMS workflow
Route::get('/{applicationId}', [\App\Http\Controllers\EdmsController::class, 'index'])->name('edms.index');
// Sub-application EDMS workflow
Route::get('/sub/{applicationId}', [\App\Http\Controllers\EdmsController::class, 'index'])->defaults('type', 'sub')->name('edms.sub.index');
// File Indexing
Route::get('/create-file-indexing/{applicationId}/{type?}', [\App\Http\Controllers\EdmsController::class, 'createFileIndexing'])->name('edms.create-file-indexing');
Route::get('/fileindexing/{fileIndexingId}', [\App\Http\Controllers\EdmsController::class, 'fileIndexing'])->name('edms.fileindexing');
Route::put('/fileindexing/{fileIndexingId}', [\App\Http\Controllers\EdmsController::class, 'updateFileIndexing'])->name('edms.update-file-indexing');
// Scanning
Route::get('/scanning/{fileIndexingId}', [\App\Http\Controllers\EdmsController::class, 'scanning'])->name('edms.scanning');
Route::post('/scanning/{fileIndexingId}/upload', [\App\Http\Controllers\EdmsController::class, 'uploadScannedDocuments'])->name('edms.upload-documents');
// Page Typing
Route::get('/pagetyping/{fileIndexingId}', [\App\Http\Controllers\EdmsController::class, 'pageTyping'])->name('edms.pagetyping');
Route::post('/pagetyping/{fileIndexingId}', [\App\Http\Controllers\EdmsController::class, 'savePageTyping'])->name('edms.save-page-typing');
Route::put('/edms/scanning/{scanningId}/update-details', [\App\Http\Controllers\EdmsController::class, 'updateDocumentDetails'])->name('edms.update-document-details');
// Status API
Route::get('/status/{applicationId}', [\App\Http\Controllers\EdmsController::class, 'getEdmsStatus'])->name('edms.status');
});
// Primary Form Routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'primaryform'], function () {
    Route::get('/', [\App\Http\Controllers\PrimaryFormController::class, 'index'])->name('primaryform.index');
    Route::post('/', [\App\Http\Controllers\PrimaryFormController::class, 'store'])->name('primaryform.store');
});
// Betterment Bill Routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'gisedms'], function () {
    Route::get('/betterment-bill/show/{id}', [App\Http\Controllers\BettermentBillController::class, 'show'])->name('gisedms.betterment-bill.show');
    Route::post('/betterment-bill/store', [App\Http\Controllers\BettermentBillController::class, 'store'])->name('gisedms.betterment-bill.store');
    Route::get('/betterment-bill/print/{id}', [App\Http\Controllers\BettermentBillController::class, 'printReceipt'])->name('gisedms.betterment-bill.print');
    Route::get('/sub-final-bill/show/{id}', [App\Http\Controllers\SubFinalBillController::class, 'showBill'])->name('gisedms.sub-final-bill.show');
    Route::post('/sub-final-bill/save', [App\Http\Controllers\SubFinalBillController::class, 'saveBill'])->name('gisedms.sub-final-bill.save');
    Route::get('/application-details/{fileId}/{fileType}', [App\Http\Controllers\ProgrammesController::class, 'getApplicationDetails'])->name('gisedms.application-details');
});
// Additional EDMS Page Typing Routes
Route::post('/edms/pagetyping/{fileIndexingId}/save-single', [\App\Http\Controllers\EdmsController::class, 'saveSinglePageTyping'])->name('edms.save-single-page-typing');
Route::post('/edms/pagetyping/{fileIndexingId}/finish', [\App\Http\Controllers\EdmsController::class, 'finishPageTyping'])->name('edms.finish-page-typing');
Route::post('/edms/pagetyping/{fileIndexingId}/batch-save', [\App\Http\Controllers\EdmsController::class, 'batchSavePageTyping'])->name('edms.batch-save-page-typing');
Route::post('/edms/pdf-thumbnail', [\App\Http\Controllers\EdmsController::class, 'getPdfPageThumbnail'])->name('edms.pdf-thumbnail');
// File Number Generation Routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'file-numbers'], function () {
    Route::get('/', [App\Http\Controllers\FileNumberController::class, 'index'])->name('file-numbers.index');
    Route::get('/data', [App\Http\Controllers\FileNumberController::class, 'getData'])->name('file-numbers.data');
    Route::get('/test-db', [App\Http\Controllers\FileNumberController::class, 'testDatabase'])->name('file-numbers.test-db');
    Route::get('/debug-data', function() {
        try {
            $data = DB::connection('sqlsrv')
                ->table('fileNumber')
                ->select(['id', 'kangisFileNo', 'NewKANGISFileNo', 'FileName', 'mlsfNo', 'created_by', 'created_at'])
                ->limit(5)
                ->get();
            
            return response()->json([
                'success' => true,
                'raw_data' => $data->toArray(),
                'formatted_data' => $data->map(function($row) {
                    return [
                        'id' => $row->id,
                        'kangisFileNo' => trim($row->kangisFileNo ?? '') ?: '-',
                        'NewKANGISFileNo' => trim($row->NewKANGISFileNo ?? '') ?: '-',
                        'FileName' => trim($row->FileName ?? '') ?: '-',
                        'mlsfNo' => trim($row->mlsfNo ?? '') ?: '-',
                        'created_by' => trim($row->created_by ?? '') ?: 'System',
                        'created_at' => $row->created_at ?: '-'
                    ];
                })->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    })->name('file-numbers.debug-data');
    Route::get('/next-serial', [App\Http\Controllers\FileNumberController::class, 'getNextSerial'])->name('file-numbers.next-serial');
    Route::get('/existing', [App\Http\Controllers\FileNumberController::class, 'getExistingFileNumbers'])->name('file-numbers.existing');
    Route::post('/store', [App\Http\Controllers\FileNumberController::class, 'store'])->name('file-numbers.store');
    Route::post('/migrate', [App\Http\Controllers\FileNumberController::class, 'migrate'])->name('file-numbers.migrate');
    Route::get('/{id}', [App\Http\Controllers\FileNumberController::class, 'show'])->name('file-numbers.show');
    Route::put('/{id}', [App\Http\Controllers\FileNumberController::class, 'update'])->name('file-numbers.update');
    Route::delete('/{id}', [App\Http\Controllers\FileNumberController::class, 'destroy'])->name('file-numbers.destroy');
    Route::get('/count/total', [App\Http\Controllers\FileNumberController::class, 'getCount'])->name('file-numbers.count');
});
// Page Typing Debug Routes (main routes are in apps2.php)
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'pagetyping'], function () {
    Route::get('/test-routes', function() {
        return view('pagetyping.test_routes');
    })->name('pagetyping.test');
    Route::get('/debug-database', function() {
        return view('pagetyping.debug_database');
    })->name('pagetyping.debug');
    Route::get('/test-file-urls', function() {
        return view('pagetyping.test_file_urls');
    })->name('pagetyping.test-urls');
    Route::get('/test-pdf-access', function() {
        return view('pagetyping.test_pdf_access');
    })->name('pagetyping.test-pdf');
    Route::get('/pdf-diagnostic', function() {
        return view('pagetyping.pdf_diagnostic');
    })->name('pagetyping.pdf-diagnostic');
    Route::post('/check-pdf-file', function(Request $request) {
        try {
            $filePath = $request->input('file_path');
            $fullPath = storage_path('app/public/' . $filePath);
            
            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File does not exist on server',
                    'details' => ['path' => $fullPath]
                ]);
            }
            
            $fileSize = filesize($fullPath);
            $mimeType = mime_content_type($fullPath);
            
            // Read first 100 bytes to check PDF header
            $handle = fopen($fullPath, 'rb');
            $header = fread($handle, 100);
            fclose($handle);
            
            $isPdf = strpos($header, '%PDF') === 0;
            $pdfVersion = null;
            if (preg_match('/%PDF-(\d\.\d)/', $header, $matches)) {
                $pdfVersion = $matches[1];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'File exists and analyzed',
                'details' => [
                    'path' => $fullPath,
                    'size' => $fileSize,
                    'mimeType' => $mimeType,
                    'isPdf' => $isPdf,
                    'pdfVersion' => $pdfVersion,
                    'header' => bin2hex(substr($header, 0, 20)),
                    'headerAscii' => substr($header, 0, 20),
                    'permissions' => substr(sprintf('%o', fileperms($fullPath)), -4)
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server-side check failed: ' . $e->getMessage()
            ]);
        }
    })->name('pagetyping.check-pdf');
});
// PropertyCard routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'propertycard'], function () {
    Route::get('/', [App\Http\Controllers\PropertyCardController::class, 'index'])->name('propertycard.index');
    Route::get('/data', [App\Http\Controllers\PropertyCardController::class, 'getData'])->name('propertycard.getData');
    Route::get('/create', [App\Http\Controllers\PropertyCardController::class, 'create'])->name('propertycard.create');
    Route::post('/store', [App\Http\Controllers\PropertyCardController::class, 'store'])->name('propertycard.store');
    Route::post('/search', [App\Http\Controllers\PropertyCardController::class, 'search'])->name('propertycard.search');
    Route::post('/save-record', [App\Http\Controllers\PropertyCardController::class, 'savePropertyRecord'])->name('propertycard.saveRecord');
    Route::post('/navigate', [App\Http\Controllers\PropertyCardController::class, 'navigateRecord'])->name('propertycard.navigate');
    Route::get('/record-details', [App\Http\Controllers\PropertyCardController::class, 'getRecordDetails'])->name('propertycard.getRecordDetails');
    Route::get('/capture', [App\Http\Controllers\PropertyCardController::class, 'capture'])->name('propertycard.capture');

    // AI Assistant routes
    Route::get('/ai', [App\Http\Controllers\PropertyCardController::class, 'aiIndex'])->name('propertycard.ai');
    Route::post('/ai/save', [App\Http\Controllers\PropertyCardController::class, 'saveAiPropertyRecord'])->name('propertycard.ai.save');
});

// File Indexing routes - Dynamic API endpoints
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'fileindexing'], function () {
    Route::get('/', [App\Http\Controllers\FileIndexController::class, 'index'])->name('fileindexing.index');
    Route::get('/create', [App\Http\Controllers\FileIndexController::class, 'create'])->name('fileindexing.create');
    Route::post('/store', [App\Http\Controllers\FileIndexController::class, 'store'])->name('fileindexing.store');
    
    // API endpoints for dynamic data (specific routes first)
    Route::get('/api/pending-files', [App\Http\Controllers\FileIndexController::class, 'getPendingFiles'])->name('fileindexing.api.pending-files');
    Route::get('/api/indexed-files', [App\Http\Controllers\FileIndexController::class, 'getIndexedFiles'])->name('fileindexing.api.indexed-files');
    Route::get('/api/selected-files-for-ai-insights', [App\Http\Controllers\FileIndexController::class, 'getSelectedFilesForAiInsights'])->name('fileindexing.api.selected-files-for-ai-insights');
    Route::get('/search-applications', [App\Http\Controllers\FileIndexController::class, 'searchApplications'])->name('fileindexing.search-applications');
    Route::get('/check-file-status', [App\Http\Controllers\FileIndexController::class, 'checkFileStatus'])->name('fileindexing.check-file-status');
    Route::get('/list', [App\Http\Controllers\FileIndexController::class, 'getFileIndexingList'])->name('fileindexing.list');
    
    // Tracking sheet generation routes (specific routes before parameterized routes)
    Route::get('/batch-tracking-sheet', [App\Http\Controllers\FileIndexController::class, 'generateBatchTrackingSheet'])->name('fileindexing.batch-tracking-sheet');
    Route::get('/tracking-sheet/{id}', [App\Http\Controllers\FileIndexController::class, 'generateTrackingSheet'])->name('fileindexing.tracking-sheet');
    Route::get('/print-tracking-sheet/{id}', [App\Http\Controllers\FileIndexController::class, 'printTrackingSheet'])->name('fileindexing.print-tracking-sheet');

    // Smart Batch Tracking Interface
    Route::get('/batch-tracking-interface', [App\Http\Controllers\FileIndexController::class, 'batchTrackingInterface'])->name('fileindexing.batch-tracking-interface');
    Route::post('/bulk-movement-update', [App\Http\Controllers\FileIndexController::class, 'bulkMovementUpdate'])->name('fileindexing.bulk-movement-update');
    Route::get('/movement-history', [App\Http\Controllers\FileIndexController::class, 'getMovementHistory'])->name('fileindexing.movement-history');
    Route::get('/export-movement-history', [App\Http\Controllers\FileIndexController::class, 'exportMovementHistory'])->name('fileindexing.export-movement-history');
    Route::post('/{id}/update-tracking', [App\Http\Controllers\FileIndexController::class, 'updateTrackingLocation'])->name('fileindexing.update-tracking');
    
    // Parameterized routes (must come last)
    Route::get('/{id}', [App\Http\Controllers\FileIndexController::class, 'show'])->name('fileindexing.show');
    Route::get('/{id}/edit', [App\Http\Controllers\FileIndexController::class, 'edit'])->name('fileindexing.edit');
    Route::put('/{id}', [App\Http\Controllers\FileIndexController::class, 'update'])->name('fileindexing.update');
    Route::delete('/{id}', [App\Http\Controllers\FileIndexController::class, 'destroy'])->name('fileindexing.destroy');
});

// File number search API endpoint for Select2
Route::get('/api/search-file-numbers', [App\Http\Controllers\FileIndexController::class, 'searchFileNumbers'])->name('api.search-file-numbers');

// File Tracker Integration
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'filetracker'], function () {
    Route::get('/', [App\Http\Controllers\FileTrackerController::class, 'index'])->name('filetracker.index');
    Route::get('/create', [App\Http\Controllers\FileTrackerController::class, 'trackingForm'])->name('filetracker.create');
    Route::post('/store', [App\Http\Controllers\FileTrackerController::class, 'store'])->name('filetracker.store');
    Route::post('/store-batch', [App\Http\Controllers\FileTrackerController::class, 'storeBatch'])->name('filetracker.store-batch');
    Route::get('/get-indexed-files', [App\Http\Controllers\FileTrackerController::class, 'getIndexedFiles'])->name('filetracker.get-indexed-files');
});

// Scanning Integration
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'scanning'], function () {
    Route::get('/', [App\Http\Controllers\ScanningController::class, 'index'])->name('scanning.index');
    Route::post('/upload', [App\Http\Controllers\ScanningController::class, 'upload'])->name('scanning.upload');
    Route::post('/upload-unindexed', [App\Http\Controllers\ScanningController::class, 'uploadUnindexed'])->name('scanning.upload-unindexed');
    Route::get('/list', [App\Http\Controllers\ScanningController::class, 'list'])->name('scanning.list');
    Route::get('/{id}', [App\Http\Controllers\ScanningController::class, 'view'])->name('scanning.view');
    Route::get('/{id}/details', [App\Http\Controllers\ScanningController::class, 'details'])->name('scanning.details');
    Route::put('/{id}/update', [App\Http\Controllers\ScanningController::class, 'updateDetails'])->name('scanning.update');
    Route::delete('/{id}', [App\Http\Controllers\ScanningController::class, 'delete'])->name('scanning.delete');
});

// Page Typing Integration
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'pagetyping'], function () {
    Route::get('/', [App\Http\Controllers\PageTypingController::class, 'index'])->name('pagetyping.index');
    Route::post('/save', [App\Http\Controllers\PageTypingController::class, 'save'])->name('pagetyping.save');
});

require __DIR__ . '/instrument_batch_fix.php';

Route::get('/api/survey-plan/{applicationId}', [App\Http\Controllers\PrimaryFormController::class, 'getSurveyPlan'])->name('api.survey-plan');

// Blind Scanning Routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'blind-scanning'], function () {
    Route::get('/', [App\Http\Controllers\BlindScanningController::class, 'index'])->name('blind-scanning.index');
    Route::post('/store', [App\Http\Controllers\BlindScanningController::class, 'store'])->name('blind-scanning.store');
    Route::get('/list', [App\Http\Controllers\BlindScanningController::class, 'list'])->name('blind-scanning.list');
    Route::post('/convert-to-upload', [App\Http\Controllers\BlindScanningController::class, 'convertToUpload'])->name('blind-scanning.convert-to-upload');
    Route::get('/{id}', [App\Http\Controllers\BlindScanningController::class, 'show'])->name('blind-scanning.show');
    Route::delete('/{id}', [App\Http\Controllers\BlindScanningController::class, 'destroy'])->name('blind-scanning.destroy');
});

// PTQ Control (Quality Control) Routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'ptq-control'], function () {
    Route::get('/', [App\Http\Controllers\PTQController::class, 'index'])->name('ptq-control.index');
    Route::get('/list-pending', [App\Http\Controllers\PTQController::class, 'listPending'])->name('ptq-control.list-pending');
    Route::get('/list-in-progress', [App\Http\Controllers\PTQController::class, 'listInProgress'])->name('ptq-control.list-in-progress');
    Route::get('/list-completed', [App\Http\Controllers\PTQController::class, 'listCompleted'])->name('ptq-control.list-completed');
    Route::get('/qc-details/{fileIndexingId}', [App\Http\Controllers\PTQController::class, 'getQCDetails'])->name('ptq-control.qc-details');
    Route::post('/mark-qc-status', [App\Http\Controllers\PTQController::class, 'markQCStatus'])->name('ptq-control.mark-qc-status');
    Route::post('/override-qc', [App\Http\Controllers\PTQController::class, 'overrideQC'])->name('ptq-control.override-qc');
    Route::post('/batch-qc-operation', [App\Http\Controllers\PTQController::class, 'batchQCOperation'])->name('ptq-control.batch-qc-operation');
    Route::post('/approve-for-archiving', [App\Http\Controllers\PTQController::class, 'approveForArchiving'])->name('ptq-control.approve-for-archiving');
    Route::post('/archive-file', [App\Http\Controllers\PTQController::class, 'archiveFile'])->name('ptq-control.archive-file');
    Route::get('/qc-audit-trail/{fileIndexingId}', [App\Http\Controllers\PTQController::class, 'getQCAuditTrail'])->name('ptq-control.qc-audit-trail');
    Route::get('/qc-stats', [App\Http\Controllers\PTQController::class, 'getQCStats'])->name('ptq-control.qc-stats');
});

// Unindexed Scanning Routes
Route::group(['middleware' => ['auth', 'XSS'], 'prefix' => 'unindexed-scanning'], function () {
    Route::get('/', [App\Http\Controllers\UnindexedScanningController::class, 'index'])->name('unindexed-scanning.index');
    Route::post('/upload', [App\Http\Controllers\UnindexedScanningController::class, 'upload'])->name('unindexed-scanning.upload');
    Route::post('/process-ocr', [App\Http\Controllers\UnindexedScanningController::class, 'processOcr'])->name('unindexed-scanning.process-ocr');
    Route::post('/create-indexing-entry', [App\Http\Controllers\UnindexedScanningController::class, 'createIndexingEntry'])->name('unindexed-scanning.create-indexing-entry');
    Route::get('/files', [App\Http\Controllers\UnindexedScanningController::class, 'getUnindexedFiles'])->name('unindexed-scanning.files');
    
    // Document preview and metadata routes
    Route::get('/preview/{id}', [App\Http\Controllers\UnindexedScanningController::class, 'getFilePreview'])->name('unindexed-scanning.preview');
    Route::get('/metadata/{id}', [App\Http\Controllers\UnindexedScanningController::class, 'getFileMetadata'])->name('unindexed-scanning.metadata');
    Route::put('/metadata/{id}', [App\Http\Controllers\UnindexedScanningController::class, 'updateFileMetadata'])->name('unindexed-scanning.update-metadata');
    
    Route::delete('/files/{id}', [App\Http\Controllers\UnindexedScanningController::class, 'deleteUnindexedFile'])->name('unindexed-scanning.delete-file');
});

Route::get('/test-serial-api', function() {
    return view('pagetyping.test_serial_api');
})->name('pagetyping.test-serial-api');
