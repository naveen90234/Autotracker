<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\UserController;
Use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\InAppController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\CarController;
use App\Http\Controllers\Api\V1\MaintenanceTaskController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\SellingTipController;
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


Route::group(['namespace' => 'Api'], function () {
    Route::post('get-auth-token', [HomeController::class, 'getAuthToken']);

    Route::group(['middleware' => ['verifyAuthToken']], function () {

        // ----------------- User Signup/Login -------------------

        //This api is used to verify user and then send/resend otp.
        Route::post('check-user', [UserController::class, 'checkUser']);
        Route::post('signup', [UserController::class, 'signup']);
        Route::post('signin', [UserController::class, 'signin']);

        Route::post('forgot-password', [UserController::class, 'forgotPasswordOtp']);
        Route::post('reset-password', [UserController::class, 'resetPassword']);
        Route::post('verify-otp', [UserController::class, 'verifyOtp']);

        Route::post('send-otp', [UserController::class, 'sendOtp']);
        Route::get("version-control", [HomeController::class, 'versionControl']);
        Route::post('home-page', [HomeController::class, 'homePage']);

        Route::post('pages', [PageController::class, 'getPage']);
        Route::get('welcome-text', [PageController::class, 'welcomeText']);

    });
});


Route::group(['namespace' => 'Api', 'middleware' => ['tokenExpiry:api', 'verifyAuthToken', 'userActive']], function () {



    Route::post('verify-two-factor-auth', [UserController::class, 'verifyTwoFactorAuth']);

    //----------------- Category Management -------------------
    Route::post('get-categories', [CategoryController::class, 'getCategories']);

    // ----------------- User Management -------------------
    Route::post('create-edit-profile', [UserController::class, 'createEditProfile']);
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::get('get-profile', [UserController::class, 'getProfile']);
    Route::post('get-other-profile', [UserController::class, 'getOtherProfile']);
    Route::post('logout', [UserController::class, 'userLogout']);
    Route::post('update-two-factor-status', [UserController::class, 'updateTwoFactorStatus']);

    // -----------------  User Account -------------------
    Route::post('delete-account', [UserController::class, 'delete_account']);


    // -----------------  Sync Contacts -------------------
    Route::post('sync-contacts', [UserController::class, 'syncContacts']);


    // ----------------- Notification Management -------------------
    Route::get('notification-list', [UserController::class, 'notificationList']);
    Route::post('clear-notification', [UserController::class, 'clearNotification']);
    Route::post('update-notification-status', [UserController::class, 'updateNotificationStatus']);
    Route::post('notification-count', [UserController::class, 'getNotificationCount']);



    // ----------------- User Management -------------------
    Route::post('block-unblock-user', [UserController::class, 'blockUnblockUser']);
    Route::get('blocked-users-list', [UserController::class, 'blockedUsersList']);


    //----------------- User Chat -------------------
    Route::post('chat-image-upload', [HomeController::class, 'chatImageUpload']);
    Route::post('create-garage', [HomeController::class, 'createGarage']);
    Route::post('nearby-service-shop', [HomeController::class, 'nearbyMechanics']);


    // ----------------- In App Purchase -------------------
    Route::post('purchase-plan', [InAppController::class, 'purchasePlan']);
    Route::post('purchase-restore', [InAppController::class, 'purchaseRestore']);
    Route::get('get-plan-list', [InAppController::class, 'getPlanList']);


    // ----------------- Report User -------------------
    Route::post('report-user', [UserController::class, 'reportUser']);
    // ----------------- Add Vehicle -------------------
    Route::post('add-edit-vehicle', [VehicleController::class, 'addEditVehicle']);
    Route::post('cars-list', [VehicleController::class, 'carsList']);
    Route::post('car-details', [VehicleController::class, 'carDetails']);
    Route::post('get-car-current-miles', [VehicleController::class, 'getCarCurrentMiles']);
    Route::post('add-update-current-miles', [VehicleController::class, 'addOrUpdateCurrentMiles']);
    // ----------------- Vehicle year list -------------------
    Route::post('/vehicle-year-list', [CarController::class, 'getCarYears']);
    // ----------------- Vehicle model list -------------------
    Route::post('/vehicle-model-list', [CarController::class, 'getCarModels']);
    // ----------------- Vehicle make list -------------------
    Route::post('/vehicle-make-list', [CarController::class, 'getCarMakes']);
    // ----------------- Vehicle parts list -------------------
    Route::post('/vehicle-parts-list', [CarController::class, 'getCarPartsList']);
    // ----------------- Vehicle Images list -------------------
    Route::post('/stock-vehicle-images', [VehicleController::class, 'stockVehicleImages']);

    // ----------------- Maintenance Task -------------------
    Route::post('add-update-maintenance-task', [MaintenanceTaskController::class, 'addOrUpdateMaintainanceTask']);
    Route::post('show-maintainance-task-list', [MaintenanceTaskController::class, 'showMaintenanceTaskList']);
    Route::post('show-maintainance-history', [MaintenanceTaskController::class, 'showMaintenanceHistory']);
    Route::post('delete-maintainance-task', [MaintenanceTaskController::class, 'deleteMaintenanceTask']);
    Route::post('update-maintainance-task-status', [MaintenanceTaskController::class, 'updateMaintenanceTaskStatus']);

    // ----------------- Service -------------------
    Route::post('add-service', [ServiceController::class, 'addService']);
    Route::post('show-service-list', [ServiceController::class, 'showServiceList']);
    Route::post('service-details', [ServiceController::class, 'serviceDetails']);
    Route::post('get-service-history', [ServiceController::class, 'getServiceHistory']);
    Route::post('get-car-last-service', [ServiceController::class, 'getCarLastService']);

    // ----------------- Selling Tips -------------------
    Route::get('tips-to-sell', [SellingTipController::class, 'index']);
});
