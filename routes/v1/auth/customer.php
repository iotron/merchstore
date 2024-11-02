<?php

use App\Http\Controllers\Api\Auth\AccountController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Auth\SocialController;
use App\Http\Controllers\Api\Auth\Verification\EmailOTPController;
use App\Http\Controllers\Api\Auth\Verification\PhoneFirebaseController;
use Illuminate\Support\Facades\Route;

/**
 * Login
 */
Route::post('/login', [LoginController::class, 'loginCustomer']);

/**
 * Auth Guarded
 */
Route::middleware(['auth:customer'])->group(function () {

    // Guarded Login
    Route::get('session', [LoginController::class, 'getSession']);
    Route::get('logout', [LoginController::class, 'logout']);

    /**
     * Customer Prefix
     */
    Route::prefix('customer')->group(function () {

        /**
         * Profile Account
         */
        Route::get('profile', [ProfileController::class, 'profile']);
        Route::post('profile-update', [ProfileController::class, 'updateProfile']);
        Route::post('set-password', [ProfileController::class, 'setPassword']);
        Route::post('change-password', [ProfileController::class, 'updatePassword']);

        /**
         * Social
         */
        Route::get('my-socials', [SocialController::class, 'viewSocials']);
        Route::delete('remove-social', [SocialController::class, 'removeSocial']);

    });

});

Route::post('token', [AccountController::class, 'checkTokenValidity']);

/**
 * OTP
 */
Route::post('sendotp', [EmailOTPController::class, 'sendOtp']);
Route::post('verifyotp', [EmailOTPController::class, 'verifyOtp']);

// Firebase
Route::post('check/contact', [PhoneFirebaseController::class, 'checkExistingContact']);

Route::post('save/contact', [PhoneFirebaseController::class, 'saveVerifiedContact']);

/**
 * reset
 */
Route::post('reset', [AccountController::class, 'reset']);

/**
 * Register
 */
Route::post('register', [AccountController::class, 'register']);
