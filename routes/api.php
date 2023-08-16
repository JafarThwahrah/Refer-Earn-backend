<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller(App\Http\Controllers\Api\AuthController::class)->prefix('auth')->name('auth.')->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});

Route::controller(App\Http\Controllers\Api\ReferralClickController::class)->prefix('clicks')->name('clicks.')->group(function () {
    Route::get('add-clicks/{user_id}', 'handle_referral_click')->name('handle_referral_click');
});
//protected routes
Route::middleware('auth:api')->group(function () {
});
