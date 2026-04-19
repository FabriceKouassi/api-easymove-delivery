<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register');

    Route::prefix('/otp')->group(function () {
        Route::post('/send', 'sendOtp');
        Route::post('/check', 'checkOtpLogin')->middleware('throttle:5,1');
    });

});
