<?php

use App\Http\Controllers\Api\DeviceApiController;
use App\Http\Controllers\Api\AdCampaignApiController;
use App\Http\Controllers\Api\AppVersionApiController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    // Device Registration & Telemetry Sync on App Open
    Route::post('/devices/register', [DeviceApiController::class, 'register'])->name('api.v1.devices.register');
    Route::post('/devices/sync', [DeviceApiController::class, 'register'])->name('api.v1.devices.sync');

    // Ads
    Route::get('/ads/custom', [AdCampaignApiController::class, 'getAds'])->name('api.v1.ads.custom');

    // App Version & Force Update Config
    Route::get('/app-version', [AppVersionApiController::class, 'check'])->name('api.v1.app-version');
    // Route::post('/app-version/check', [AppVersionApiController::class, 'check'])->name('api.v1.app-version.check');
});

