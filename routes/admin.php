<?php

use App\Http\Controllers\Admin\AudienceSegmentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\FirebaseSettingController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('admin.forgot-password');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (Dashboard, Installations, Users & Profile)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Installed Devices Management
    Route::prefix('admin/devices')->group(function () {
        Route::get('/', [DeviceController::class, 'index'])->name('admin.devices.index');
        Route::post('/send-campaign', [DeviceController::class, 'sendCampaign'])->name('admin.devices.send-campaign');
        Route::get('/export', [DeviceController::class, 'exportCsv'])->name('admin.devices.export');
        Route::get('/{id}', [DeviceController::class, 'show'])->name('admin.devices.show');
        Route::post('/{id}/send-push', [DeviceController::class, 'sendPush'])->name('admin.devices.send-push');
        Route::post('/{id}/mark-inactive', [DeviceController::class, 'markInactive'])->name('admin.devices.mark-inactive');
        Route::post('/{id}/toggle-status', [DeviceController::class, 'toggleStatus'])->name('admin.devices.toggle-status');
        Route::delete('/{id}', [DeviceController::class, 'destroy'])->name('admin.devices.destroy');
    });

    // Locations Analytics & Interactive Maps
    Route::prefix('admin/locations')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('admin.locations.index');
        Route::get('/export', [LocationController::class, 'exportCsv'])->name('admin.locations.export');
        Route::post('/export-all', [LocationController::class, 'exportAllModal'])->name('admin.locations.export-all');
        Route::get('/send-notification', [LocationController::class, 'sendNotificationGlobal'])->name('admin.locations.send-notification-global');
        Route::post('/send-notification', [LocationController::class, 'submitNotificationGlobal'])->name('admin.locations.submit-notification-global');
        Route::get('/create-segment', [LocationController::class, 'createSegmentGlobal'])->name('admin.locations.create-segment-global');
        Route::post('/create-segment', [LocationController::class, 'storeSegmentGlobal'])->name('admin.locations.store-segment-global');

        Route::get('/{id}', [LocationController::class, 'show'])->name('admin.locations.show');
        Route::get('/{id}/map', [LocationController::class, 'map'])->name('admin.locations.map');
        Route::get('/{id}/heatmap', [LocationController::class, 'heatmap'])->name('admin.locations.heatmap');
        Route::get('/{id}/create-segment', [LocationController::class, 'createSegment'])->name('admin.locations.create-segment');
        Route::post('/{id}/create-segment', [LocationController::class, 'storeSegment'])->name('admin.locations.store-segment');
        Route::get('/{id}/send-notification', [LocationController::class, 'sendNotification'])->name('admin.locations.send-notification');
        Route::post('/{id}/send-notification', [LocationController::class, 'submitNotification'])->name('admin.locations.submit-notification');
        Route::post('/{id}/export-modal', [LocationController::class, 'exportModal'])->name('admin.locations.export-modal');
    });

    // Audience Segments Management
    Route::prefix('admin/segments')->group(function () {
        Route::get('/', [AudienceSegmentController::class, 'index'])->name('admin.segments.index');
        Route::get('/export-all', [AudienceSegmentController::class, 'exportAll'])->name('admin.segments.export-all');
        Route::get('/create', [AudienceSegmentController::class, 'create'])->name('admin.segments.create');
        Route::post('/', [AudienceSegmentController::class, 'store'])->name('admin.segments.store');
        Route::post('/estimate-live', [AudienceSegmentController::class, 'estimateLive'])->name('admin.segments.estimate-live');
        
        Route::get('/{id}', [AudienceSegmentController::class, 'show'])->name('admin.segments.show');
        Route::get('/{id}/edit', [AudienceSegmentController::class, 'edit'])->name('admin.segments.edit');
        Route::put('/{id}', [AudienceSegmentController::class, 'update'])->name('admin.segments.update');
        Route::delete('/{id}', [AudienceSegmentController::class, 'destroy'])->name('admin.segments.destroy');
        
        Route::get('/{id}/audience', [AudienceSegmentController::class, 'audience'])->name('admin.segments.audience');
        Route::post('/{id}/duplicate', [AudienceSegmentController::class, 'duplicate'])->name('admin.segments.duplicate');
        Route::post('/{id}/refresh', [AudienceSegmentController::class, 'refreshAudience'])->name('admin.segments.refresh');
        Route::post('/{id}/pause', [AudienceSegmentController::class, 'pause'])->name('admin.segments.pause');
        Route::post('/{id}/resume', [AudienceSegmentController::class, 'resume'])->name('admin.segments.resume');
        Route::post('/{id}/archive', [AudienceSegmentController::class, 'archive'])->name('admin.segments.archive');
        Route::post('/{id}/restore', [AudienceSegmentController::class, 'restore'])->name('admin.segments.restore');
        Route::get('/{id}/export', [AudienceSegmentController::class, 'export'])->name('admin.segments.export');
    });

    // Push Notifications & Campaigns Management
    Route::prefix('admin/notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::get('/create', [NotificationController::class, 'create'])->name('admin.notifications.create');
        Route::post('/', [NotificationController::class, 'store'])->name('admin.notifications.store');
        Route::get('/export', [NotificationController::class, 'export'])->name('admin.notifications.export');
        Route::get('/{id}', [NotificationController::class, 'show'])->name('admin.notifications.show');
        Route::post('/{id}/duplicate', [NotificationController::class, 'duplicate'])->name('admin.notifications.duplicate');
        Route::post('/{id}/reschedule', [NotificationController::class, 'reschedule'])->name('admin.notifications.reschedule');
        Route::post('/{id}/send-now', [NotificationController::class, 'sendNow'])->name('admin.notifications.send-now');
        Route::post('/{id}/cancel-schedule', [NotificationController::class, 'cancelSchedule'])->name('admin.notifications.cancel-schedule');
        Route::post('/{id}/archive', [NotificationController::class, 'archive'])->name('admin.notifications.archive');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    });

    // Admin Users Management
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');

    // Firebase Settings & Multi-Project Configuration
    Route::prefix('admin/settings/firebase')->group(function () {
        Route::get('/', [FirebaseSettingController::class, 'index'])->name('admin.settings.firebase.index');
        Route::get('/create', [FirebaseSettingController::class, 'create'])->name('admin.settings.firebase.create');
        Route::post('/', [FirebaseSettingController::class, 'store'])->name('admin.settings.firebase.store');
        Route::get('/{id}/edit', [FirebaseSettingController::class, 'edit'])->name('admin.settings.firebase.edit');
        Route::put('/{id}', [FirebaseSettingController::class, 'update'])->name('admin.settings.firebase.update');
        Route::delete('/{id}', [FirebaseSettingController::class, 'destroy'])->name('admin.settings.firebase.destroy');
        Route::post('/{id}/activate', [FirebaseSettingController::class, 'activate'])->name('admin.settings.firebase.activate');
        Route::post('/{id}/test-connection', [FirebaseSettingController::class, 'testConnection'])->name('admin.settings.firebase.test');
    });

    // Super Admin Profile & Password Management
    Route::prefix('admin/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('admin.profile');
        Route::post('/update', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('admin.profile.change-password');
        Route::post('/change-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');
    });
});

