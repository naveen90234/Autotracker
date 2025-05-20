<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\CustomPageViewController;

//Admin
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\DrivingStyleController;
use App\Http\Controllers\Admin\SellingTipController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AppPageController;
use App\Http\Controllers\Admin\CarPartController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Api\V1\InAppController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\MaintenanceTaskTypeController;
use App\Http\Controllers\Admin\SupportController;


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

//Clear Cache facade value:
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Reoptimized class loader:
Route::get('/optimize-clear', function () {
    $exitCode = Artisan::call('optimize:clear');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function () {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});


Route::get('/', function(){
    return view('home');
    // return redirect('/admin/login');
});

Route::group(['prefix' => 'cron'], function(){
    Route::any('check-subscription-expiry', [InAppController::class, 'subscriptionStatusCron']);
});


Route::get('page/{slug}', [CustomPageViewController::class,'showPages'])->name('show-custom-page');

//Support page route
Route::get('/support', [CustomPageViewController::class,'support']);
Route::post('submit-support-form', [CustomPageViewController::class, 'submitSupportForm'])->name('submit_support-form');


//Account deletion
Route::get('/request-account-deletion', [CustomPageViewController::class,'accountDeletion']);
Route::post('submit-account-deletion-request', [CustomPageViewController::class, 'submitAccountDeletionRequest'])->name('submit-account-deletion-request');


Route::group(['prefix' => 'admin', 'middleware' => 'guest:admin'], function(){
    Route::any('login', [AuthController::class,'login'])->name('admin.login');
    Route::get('forgot-password', [AuthController::class, 'forgotPasswordView'])->name('admin.forgetpassword.view');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('admin.forgotpassword');
    Route::any('reset-password/{token}', [AuthController::class,'resetPassword'])->name('admin.resetpassword');
});

//Admin Panel Route
Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function () {

    Route::get('home', [HomeController::class, 'index'])->name('admin.dashboard');
    Route::any('profile', [HomeController::class, 'profile'])->name('admin.profile');
    Route::any('change-password', [HomeController::class, 'changePassword'])->name('admin.changepassword');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');


    //******************* CMS Pages Management ********************
    Route::get('/cms-page', [AppPageController::class, 'appPageList'])->name('appPage');
    Route::get('/cms-page/list', [AppPageController::class, 'showAppPageList'])->name('appPage.list');
    Route::post('/cms-page/detail', [AppPageController::class, 'viewAppPageDetail'])->name('appPage.detail');
    Route::post('/cms-page/edit', [AppPageController::class, 'editAppPage'])->name('appPage.edit');


    //******************* Category Management ********************
    Route::any('add/category', [CategoryController::class, 'addcategory'])->name('admin.addcategory');
    Route::get('categories', [CategoryController::class, 'categorylist'])->name('admin.categories');
    Route::get('category/edit/{id}', [CategoryController::class, 'editCategory']);
    Route::post('category/update/{id}', [CategoryController::class, 'updateCategory']);
    Route::get('category/delete/{id}', [CategoryController::class, 'deleteCategory']);

    //stock images
    Route::get('stock-images', [HomeController::class, 'stockimageslist'])->name('admin.stock_images');
    Route::get('stock-images/add', [HomeController::class, 'stockimagesadd'])->name('admin.stock_images.add');
    Route::post('stock-images/store', [HomeController::class, 'stockimagesstore'])->name('admin.stock_images.store');
    Route::delete('stock-images/delete/{id}', [HomeController::class, 'stockimagesdelete'])->name('admin.stock_images.delete');
    Route::post('stock-images/status/{id}', [HomeController::class, 'stockimagestatus'])->name('admin.stock_images.status');

    
    //******************* Driving Styles Management ********************
    Route::any('add/driving', [DrivingStyleController::class, 'addDrivingStyle'])->name('admin.adddrivingstyle');
    Route::get('driving_styles', [DrivingStyleController::class, 'drivingstylelist'])->name('admin.driving_styles');
    Route::get('driving_styles/edit/{id}', [DrivingStyleController::class, 'editDrivingStyle']);
    Route::post('driving_styles/update/{id}', [DrivingStyleController::class, 'updateDrivingStyle']);
    Route::get('/driving_styles/delete-style/{id}', [DrivingStyleController::class, 'deleteStyle'])->name('admin.driving_styles.delete');
    Route::any('/driving_styles/status', [DrivingStyleController::class, 'status'])->name('admin.driving_styles.status');

    //******************* Selling Tips Management ********************
    Route::get('selling_tips', [SellingTipController::class, 'index'])->name('admin.selling_tips.index');
    Route::get('selling_tips/create', [SellingTipController::class, 'create'])->name('admin.selling_tips.create');
    Route::post('selling_tips/store', [SellingTipController::class, 'store'])->name('admin.selling_tips.store');
    Route::get('selling_tips/edit/{sellingTip}', [SellingTipController::class, 'edit'])->name('admin.selling_tips.edit');
    Route::post('selling_tips/update/{sellingTip}', [SellingTipController::class, 'update'])->name('admin.selling_tips.update');
    Route::get('selling_tips/delete-article/{sellingTip}', [SellingTipController::class, 'deleteArticle'])->name('admin.selling_tips.delete');
    Route::post('selling_tips/status', [SellingTipController::class, 'status'])->name('admin.selling_tips.status');

    //******************* Car Management ********************
    Route::get('/cars', [CarController::class, 'index'])->name('admin.cars.index');
    Route::get('/cars/create', [CarController::class, 'create'])->name('admin.cars.create');
    Route::post('/cars', [CarController::class, 'store'])->name('cars.store');
    Route::get('/cars/{car}/edit', [CarController::class, 'edit'])->name('admin.cars.edit');
    Route::put('/cars/{car}', [CarController::class, 'update'])->name('admin.cars.update');
    Route::get('/cars/delete-car/{id}', [CarController::class, 'deleteCar'])->name('admin.cars.delete');
    Route::any('/cars/status', [CarController::class, 'status'])->name('admin.cars.status');
    Route::get('/cars/upload', [CarController::class, 'showUploadForm'])->name('admin.cars.upload');
    Route::post('/cars/upload', [CarController::class, 'uploadCSV'])->name('cars.uploadCSV');

    //******************* Car Parts Management ********************
    Route::get('/car-parts', [CarPartController::class, 'index'])->name('admin.car_parts.index');
    Route::get('/car-parts/create', [CarPartController::class, 'create'])->name('admin.car_parts.create');
    Route::post('/car-parts', [CarPartController::class, 'store'])->name('car_parts.store');
    Route::get('/car-parts/{carPart}/edit', [CarPartController::class, 'edit'])->name('admin.car_parts.edit');
    Route::put('/car-parts/{carPart}', [CarPartController::class, 'update'])->name('admin.car_parts.update');
    Route::any('/car-parts/status', [CarPartController::class, 'status'])->name('admin.car_parts.status');
    Route::get('/car-parts/delete-car-part/{id}', [CarPartController::class, 'deleteCarPart'])->name('admin.car_parts.delete');
    Route::get('/car-parts/upload', [CarPartController::class, 'showUploadForm'])->name('admin.car_parts.upload');
    Route::post('/car-parts/upload', [CarPartController::class, 'uploadCSV'])->name('car_parts.uploadCSV');

    //******************* Maintenance Task Type Management ********************
    Route::get('/maintenance-task-types', [MaintenanceTaskTypeController::class, 'index'])->name('admin.maintenance_task_types.index');
    Route::get('/maintenance-task-types/create', [MaintenanceTaskTypeController::class, 'create'])->name('admin.maintenance_task_types.create');
    Route::post('/maintenance-task-types', [MaintenanceTaskTypeController::class, 'store'])->name('admin.maintenance_task_types.store');
    Route::get('/maintenance-task-types/{maintenanceTaskType}/edit', [MaintenanceTaskTypeController::class, 'edit'])->name('admin.maintenance_task_types.edit');
    Route::put('/maintenance-task-types/{maintenanceTaskType}', [MaintenanceTaskTypeController::class, 'update'])->name('admin.maintenance_task_types.update');
    Route::get('/maintenance-task-types/delete-task-type/{id}', [MaintenanceTaskTypeController::class, 'deleteMaintenanceTasktype'])->name('admin.maintenance_task_types.delete');
    Route::any('/maintenance-task-types/status', [MaintenanceTaskTypeController::class, 'status'])->name('admin.maintenance_task_types.status');
    Route::get('/maintenance-task-types/upload', [MaintenanceTaskTypeController::class, 'showUploadForm'])->name('admin.maintenance_task_types.upload');
    Route::post('/maintenance-task-types/upload', [MaintenanceTaskTypeController::class, 'uploadCSV'])->name('maintenance_task_types.uploadCSV');


    // Settings Route
    Route::any('/editSetting', [SettingController::class, 'editSetting'])->name('edit_version');
    // Route::any('/settings', [SettingController::class, 'manageSetting'])->name('manage_setting');

    //**************** User management *****************
    Route::get('/users-list', [UserController::class, 'userList'])->name('admin.users-list');
    Route::any('/users/status', [UserController::class, 'status'])->name('admin.users.status');
    Route::get('/users/view/{id}', [UserController::class, 'viewUser']);
    Route::get('/users/edit/{id}', [UserController::class, 'edit']);
    Route::post('/users/update/{id}', [UserController::class, 'update']);
    Route::get('/users/delete-account/{id}', [UserController::class, 'deleteAccount']);

    //**************** User report management *****************
    Route::get('/user/reported-user-list', [UserController::class, 'reportUserList'])->name('admin.reported-user-list');
    Route::get('/user/reported-info/{id}', [UserController::class, 'reportedInfo']);

    //***************** Welcome content management *****************
    Route::get('welcome-page-content', [AppPageController::class, 'welcomePage'])->name('welcomePage');
    Route::post('welcome-page-update', [AppPageController::class, 'welcomePageUpdate'])->name('welcomePageUpdate');


    //************* Subscription Management ***************
    Route::get('subscription/plans', [SubscriptionController::class, 'subscriptionPlans'])->name('admin.subscription');
    Route::get('subscription/edit/{id}', [SubscriptionController::class, 'editSubscription']);
    Route::post('subscription/update/{id}', [SubscriptionController::class, 'updateSubscription']);
    Route::any('/subscrition/status', [SubscriptionController::class, 'status'])->name('admin.subscrition.status');


    //*************** Broadcast Management *****************
    Route::any('user-broadcasting', [HomeController::class, 'broadcasting_user'])->name('admin.broadcasting');


    //**************** Account deletion request management *****************
    Route::get('account-deletion-requests', [HomeController::class, 'accountDeletionRequests'])->name('admin.account-deletion-requests');
    Route::get('delete/user-account/{id}', [HomeController::class, 'deleteUserAccount']);

    //Email templates routes
    // Route::resource('email-templates', EmailTemplateController::class);

    Route::any('email-templates', [EmailTemplateController::class,'index'])->name('admin.email-templates');
    Route::any('email-templates-edit/{id}', [EmailTemplateController::class,'edit'])->name('admin.edit-email-templates');
    Route::any('email-templates-update/{id}', [EmailTemplateController::class,'update'])->name('admin.update-email-templates');


    //***************Support Controller ***************************
    Route::get('support-list',[SupportController::class,'index'])->name('admin.support_list');
    Route::post('delete-message/{id}',[SupportController::class,'destroy_message'])->name('delete_support_msg');
    Route::post('send-message-response',[SupportController::class,'add_response'])->name('add_response');


});
