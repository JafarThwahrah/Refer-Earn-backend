<?php

use App\Http\Controllers\api\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReferralController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->prefix('auth')->name('auth.')->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});

Route::controller(ReferralController::class)->prefix('clicks')->name('clicks.')->group(function () {
    //handle clicking on the referral link(count views)
    Route::get('add-clicks/{user_id}', 'handle_referral_click')->name('handle_referral_click');
});


//protected routes
Route::middleware('auth:api')->group(function () {
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        //get profilePage data
        Route::get('user-profile', 'get_user_data')->name('get_user_data');
        //get refferals tree
        Route::get('get-referrals-tree', 'get_referrals_tree')->name('get_referrals_tree');
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::controller(AdminController::class)->prefix('admin')->name('admin.')->group(function () {
            Route::get('get-users', 'get_users')->name('get_users');
            Route::get('get-overview', 'get_overview')->name('get_overview');
        });
    });
});
