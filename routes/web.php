<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BloodInventoryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\CrossMatchController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransfusionController;
use App\Http\Controllers\BloodBagController;
use App\Http\Controllers\SmsCampaignController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
   return view('auth.login');
   
});

// Super Admin Routes
Route::middleware(['auth'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/hospitals', [App\Http\Controllers\SuperAdminController::class, 'hospitals'])->name('hospitals');
    Route::get('/hospitals/create', [App\Http\Controllers\SuperAdminController::class, 'createHospital'])->name('hospitals.create');
    Route::post('/hospitals', [App\Http\Controllers\SuperAdminController::class, 'storeHospital'])->name('hospitals.store');
    Route::get('/hospitals/{hospital}/edit', [App\Http\Controllers\SuperAdminController::class, 'editHospital'])->name('hospitals.edit');
    Route::put('/hospitals/{hospital}', [App\Http\Controllers\SuperAdminController::class, 'updateHospital'])->name('hospitals.update');
    Route::post('/hospitals/{hospital}/reset-admin', [App\Http\Controllers\SuperAdminController::class, 'resetHospitalAdmin'])->name('hospitals.reset-admin');
    Route::post('/switch-tenant', [App\Http\Controllers\SuperAdminController::class, 'switchTenant'])->name('switch-tenant');
    Route::post('/clear-tenant-context', [App\Http\Controllers\SuperAdminController::class, 'clearTenantContext'])->name('clear-tenant-context');
});

// Main Dashboard Route - handles both Super Admin and regular users
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role && auth()->user()->role->name === 'super_admin') {
            return app(App\Http\Controllers\SuperAdminController::class)->dashboard();
        } else {
            return app(DashboardController::class)->index();
        }
    })->name('dashboard');
});


// Remove the general blood-bags resource route
// Route::resource('blood-bags', BloodBagController::class);

// Blood bag management
Route::get('blood-bags', [BloodBagController::class, 'index'])->name('blood-bags.index');

// Add specific routes for blood bags that require a donor
Route::prefix('donors/{donor}')->group(function(){
    Route::get('bags/create',    [BloodBagController::class, 'create'])
         ->name('blood-bags.create');
    Route::post('bags',          [BloodBagController::class, 'store'])
         ->name('blood-bags.store');
    Route::get('bags/{bloodBag}', [BloodBagController::class, 'show'])
         ->name('blood-bags.show')
         ->where('bloodBag', '[0-9]+');
    Route::get('bags/{bloodBag}/edit', [BloodBagController::class, 'edit'])
         ->name('blood-bags.edit');
    Route::put('bags/{bloodBag}', [BloodBagController::class, 'update'])
         ->name('blood-bags.update');
    Route::delete('bags/{bloodBag}', [BloodBagController::class, 'destroy'])
         ->name('blood-bags.destroy');
});


// show the "Add Bag" form
// Route::get('donors/{donor}/bags/create', [BloodBagController::class, 'create'])
//      ->name('blood-bags.create');

// // handle form submission
// Route::post('donors/{donor}/bags', [BloodBagController::class, 'store'])
//      ->name('blood-bags.store');



// index
Route::get('donors/lab-results', [DonorController::class, 'labResultsIndex'])
     ->name('donors.lab-results.index');

// show — only donor ID; controller will pick the latest or all tests
Route::get('donors/lab-results/{donor}', [DonorController::class, 'showLabResult'])
     ->name('donors.lab-results.show');

// Transfusion Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('transfusions', TransfusionController::class);
    Route::get('transfusions/compatible-blood', [TransfusionController::class, 'getCompatibleBlood'])
        ->name('transfusions.compatible-blood');
});
// Donors with lab results (new index)
Route::get('donors/lab-results', [DonorController::class, 'labResultsIndex'])
     ->name('donors.lab-results.index');

// Blood bag management
// Route::resource('blood-bags', BloodBagController::class);



Route::middleware('auth')->group(function(){
    Route::resource('patients', PatientController::class);
    Route::resource('requests', BloodRequestController::class);
    Route::resource('matches', CrossMatchController::class)->only(['create','store','index']);
    Route::resource('hospitals', HospitalController::class);
    Route::resource('departments', DepartmentController::class);
    Route::get('reports', [ReportController::class,'index'])->name('reports.index');
  });
  
Route::middleware(['auth', 'can:manage-users'])->group(function () {
    // Roles CRUD
    Route::resource('roles', RoleController::class);

    // Users CRUD
    Route::resource('users', UserController::class)->except(['show']);

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])
         ->name('settings.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::get('donors/search', [DonorController::class, 'search'])->name('donors.search');
    Route::get('donors/with-lab-results', [DonorController::class, 'withLabResults'])->name('donors.with-lab-results');
    Route::get('donors/{donor}/assign-lab-test', [DonorController::class, 'assignLabTest'])->name('donors.assign-lab-test');
    Route::post('donors/{donor}/store-lab-test', [DonorController::class, 'storeLabTest'])->name('donors.store-lab-test');
    Route::get('donors/{donor}/lab-test/select', [DonorController::class, 'showLabTestSelection'])->name('donors.lab-test.select');
    Route::post('donors/{donor}/lab-test/select', [DonorController::class, 'postLabTestSelection'])->name('donors.lab-test.select.post');
    Route::get('donors/{donor}/lab-test/results', [DonorController::class, 'showLabTestResults'])->name('donors.lab-test.results');
    Route::post('donors/{donor}/lab-test/results', [DonorController::class, 'postLabTestResults'])->name('donors.lab-test.results.post');
    Route::resource('donors', DonorController::class);
});

Route::resource('sms-campaigns', SmsCampaignController::class);
Route::post('sms-campaigns/{smsCampaign}/send', [SmsCampaignController::class, 'send'])->name('sms-campaigns.send');
Route::get('/sms-campaigns/reports', [SmsCampaignController::class, 'reports'])->name('sms-campaigns.reports');
Route::get('/sms-campaigns/test-connection', [SmsCampaignController::class, 'testConnection'])->name('sms-campaigns.test-connection');
Route::post('/sms-campaigns/send-test', [SmsCampaignController::class, 'sendTestSms'])->name('sms-campaigns.send-test');

// Blood Requests
Route::resource('blood-requests', BloodRequestController::class);

// Departments
Route::resource('departments', DepartmentController::class);

// Reports Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/blood-requests', [ReportController::class, 'bloodRequests'])->name('blood-requests');
    Route::get('/blood-type-distribution', [ReportController::class, 'bloodTypeDistribution'])->name('blood-type-distribution');
    Route::get('/hospital-statistics', [ReportController::class, 'hospitalStatistics'])->name('hospital-statistics');
    Route::get('/department-statistics', [ReportController::class, 'departmentStatistics'])->name('department-statistics');
    Route::get('/export-blood-requests', [ReportController::class, 'exportBloodRequests'])->name('export-blood-requests');
});



// Blood Inventory Management Routes
Route::middleware('auth')->group(function () {
    Route::get('/inventory', [BloodInventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [BloodInventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [BloodInventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{inventory}', [BloodInventoryController::class, 'show'])->name('inventory.show');
    Route::get('/inventory/{inventory}/edit', [BloodInventoryController::class, 'edit'])->name('inventory.edit');
    Route::put('/inventory/{inventory}', [BloodInventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventory}', [BloodInventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/inventory/expiry-alerts', [BloodInventoryController::class, 'expiryAlerts'])->name('inventory.expiry-alerts');
});

// Blood Bag Processing Routes
Route::post('/blood-bags/process', [BloodBagController::class, 'process'])->name('blood-bags.process');
Route::get('/blood-bags/transfuse', [BloodBagController::class, 'transfuse'])->name('blood-bags.transfuse');
Route::post('/blood-bags/complete-transfusion', [BloodBagController::class, 'completeTransfusion'])->name('blood-bags.complete-transfusion');

require __DIR__.'/auth.php';
