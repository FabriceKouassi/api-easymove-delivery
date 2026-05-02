<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermisController;
use App\Http\Controllers\VehiculeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');

        Route::prefix('/otp')->group(function () {
            Route::post('/send', 'sendOtp');
            Route::post('/check', 'checkOtpLogin')->middleware('throttle:5,1');
        });

        Route::prefix('/admin')->group(function () {
            Route::post('/login', 'adminLogin');
            // Route::post('/create', 'adminCreate')->middleware(['auth:sanctum']);
            Route::get('/logout', 'adminLogout')->middleware(['auth:sanctum']);
        });
    });

    Route::prefix('/register/conducteur')->group(function () {
        Route::controller(PermisController::class)->group(function () {
            Route::post('permis/create', 'create');
        });
        Route::controller(VehiculeController::class)->group(function () {
            Route::post('vehicule/create', 'create');
        });
    });


});
