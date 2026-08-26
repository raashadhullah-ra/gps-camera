<?php

use App\Http\Controllers\Api\DeviceApiController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    // Device Registration & Telemetry Sync on App Open
    Route::post('/devices/register', [DeviceApiController::class, 'register'])->name('api.v1.devices.register');
    Route::post('/devices/sync', [DeviceApiController::class, 'register'])->name('api.v1.devices.sync');
});
