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
    // 1. Dashboard
    Route::middleware(['permission:dashboard.view'])->get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // 2. Installed Devices Management
    Route::prefix('admin/devices')->group(function () {
        Route::middleware(['permission:devices.view'])->get('/', [DeviceController::class, 'index'])->name('admin.devices.index');
        Route::middleware(['permission:devices.export'])->get('/export', [DeviceController::class, 'exportCsv'])->name('admin.devices.export');
        Route::middleware(['permission:devices.edit'])->post('/send-campaign', [DeviceController::class, 'sendCampaign'])->name('admin.devices.send-campaign');
        Route::middleware(['permission:devices.view'])->get('/{id}', [DeviceController::class, 'show'])->name('admin.devices.show');
        Route::middleware(['permission:devices.edit'])->post('/{id}/send-push', [DeviceController::class, 'sendPush'])->name('admin.devices.send-push');
        Route::middleware(['permission:devices.edit'])->post('/{id}/mark-inactive', [DeviceController::class, 'markInactive'])->name('admin.devices.mark-inactive');
        Route::middleware(['permission:devices.edit'])->post('/{id}/toggle-status', [DeviceController::class, 'toggleStatus'])->name('admin.devices.toggle-status');
        Route::middleware(['permission:devices.delete'])->delete('/{id}', [DeviceController::class, 'destroy'])->name('admin.devices.destroy');
    });

    // 3. Locations Analytics & Interactive Maps
    Route::prefix('admin/locations')->group(function () {
        Route::middleware(['permission:locations.view'])->get('/', [LocationController::class, 'index'])->name('admin.locations.index');
        Route::middleware(['permission:locations.export'])->get('/export', [LocationController::class, 'exportCsv'])->name('admin.locations.export');
        Route::middleware(['permission:locations.export'])->post('/export-all', [LocationController::class, 'exportAllModal'])->name('admin.locations.export-all');
        Route::middleware(['permission:notifications.create'])->get('/send-notification', [LocationController::class, 'sendNotificationGlobal'])->name('admin.locations.send-notification-global');
        Route::middleware(['permission:notifications.create'])->post('/send-notification', [LocationController::class, 'submitNotificationGlobal'])->name('admin.locations.submit-notification-global');
        Route::middleware(['permission:segments.create'])->get('/create-segment', [LocationController::class, 'createSegmentGlobal'])->name('admin.locations.create-segment-global');
        Route::middleware(['permission:segments.create'])->post('/create-segment', [LocationController::class, 'storeSegmentGlobal'])->name('admin.locations.store-segment-global');

        Route::middleware(['permission:locations.view'])->get('/{id}', [LocationController::class, 'show'])->name('admin.locations.show');
        Route::middleware(['permission:locations.view'])->get('/{id}/map', [LocationController::class, 'map'])->name('admin.locations.map');
        Route::middleware(['permission:locations.view'])->get('/{id}/heatmap', [LocationController::class, 'heatmap'])->name('admin.locations.heatmap');
        Route::middleware(['permission:segments.create'])->get('/{id}/create-segment', [LocationController::class, 'createSegment'])->name('admin.locations.create-segment');
        Route::middleware(['permission:segments.create'])->post('/{id}/create-segment', [LocationController::class, 'storeSegment'])->name('admin.locations.store-segment');
        Route::middleware(['permission:notifications.create'])->get('/{id}/send-notification', [LocationController::class, 'sendNotification'])->name('admin.locations.send-notification');
        Route::middleware(['permission:notifications.create'])->post('/{id}/send-notification', [LocationController::class, 'submitNotification'])->name('admin.locations.submit-notification');
        Route::middleware(['permission:locations.export'])->post('/{id}/export-modal', [LocationController::class, 'exportModal'])->name('admin.locations.export-modal');
    });

    // 4. Audience Segments Management
    Route::prefix('admin/segments')->group(function () {
        Route::middleware(['permission:segments.view'])->get('/', [AudienceSegmentController::class, 'index'])->name('admin.segments.index');
        Route::middleware(['permission:segments.export'])->get('/export-all', [AudienceSegmentController::class, 'exportAll'])->name('admin.segments.export-all');
        Route::middleware(['permission:segments.create'])->get('/create', [AudienceSegmentController::class, 'create'])->name('admin.segments.create');
        Route::middleware(['permission:segments.create'])->post('/', [AudienceSegmentController::class, 'store'])->name('admin.segments.store');
        Route::middleware(['permission:segments.view'])->post('/estimate-live', [AudienceSegmentController::class, 'estimateLive'])->name('admin.segments.estimate-live');
        
        Route::middleware(['permission:segments.view'])->get('/{id}', [AudienceSegmentController::class, 'show'])->name('admin.segments.show');
        Route::middleware(['permission:segments.edit'])->get('/{id}/edit', [AudienceSegmentController::class, 'edit'])->name('admin.segments.edit');
        Route::middleware(['permission:segments.edit'])->put('/{id}', [AudienceSegmentController::class, 'update'])->name('admin.segments.update');
        Route::middleware(['permission:segments.delete'])->delete('/{id}', [AudienceSegmentController::class, 'destroy'])->name('admin.segments.destroy');
        
        Route::middleware(['permission:segments.view'])->get('/{id}/audience', [AudienceSegmentController::class, 'audience'])->name('admin.segments.audience');
        Route::middleware(['permission:segments.create'])->post('/{id}/duplicate', [AudienceSegmentController::class, 'duplicate'])->name('admin.segments.duplicate');
        Route::middleware(['permission:segments.edit'])->post('/{id}/refresh', [AudienceSegmentController::class, 'refreshAudience'])->name('admin.segments.refresh');
        Route::middleware(['permission:segments.edit'])->post('/{id}/pause', [AudienceSegmentController::class, 'pause'])->name('admin.segments.pause');
        Route::middleware(['permission:segments.edit'])->post('/{id}/resume', [AudienceSegmentController::class, 'resume'])->name('admin.segments.resume');
        Route::middleware(['permission:segments.delete'])->post('/{id}/archive', [AudienceSegmentController::class, 'archive'])->name('admin.segments.archive');
        Route::middleware(['permission:segments.delete'])->post('/{id}/restore', [AudienceSegmentController::class, 'restore'])->name('admin.segments.restore');
        Route::middleware(['permission:segments.export'])->get('/{id}/export', [AudienceSegmentController::class, 'export'])->name('admin.segments.export');
    });

    // 5. Push Notifications & Campaigns Management
    Route::prefix('admin/notifications')->group(function () {
        Route::middleware(['permission:notifications.view'])->get('/', [NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::middleware(['permission:notifications.create'])->get('/create', [NotificationController::class, 'create'])->name('admin.notifications.create');
        Route::middleware(['permission:notifications.create'])->post('/', [NotificationController::class, 'store'])->name('admin.notifications.store');
        Route::middleware(['permission:notifications.export'])->get('/export', [NotificationController::class, 'export'])->name('admin.notifications.export');
        Route::middleware(['permission:notifications.view'])->get('/{id}', [NotificationController::class, 'show'])->name('admin.notifications.show');
        Route::middleware(['permission:notifications.edit'])->get('/{id}/edit', [NotificationController::class, 'edit'])->name('admin.notifications.edit');
        Route::middleware(['permission:notifications.edit'])->put('/{id}', [NotificationController::class, 'update'])->name('admin.notifications.update');
        Route::middleware(['permission:notifications.create'])->post('/{id}/duplicate', [NotificationController::class, 'duplicate'])->name('admin.notifications.duplicate');
        Route::middleware(['permission:notifications.edit'])->post('/{id}/reschedule', [NotificationController::class, 'reschedule'])->name('admin.notifications.reschedule');
        Route::middleware(['permission:notifications.edit,notifications.manage'])->post('/{id}/send-now', [NotificationController::class, 'sendNow'])->name('admin.notifications.send-now');
        Route::middleware(['permission:notifications.edit,notifications.manage'])->post('/{id}/cancel-schedule', [NotificationController::class, 'cancelSchedule'])->name('admin.notifications.cancel-schedule');
        Route::middleware(['permission:notifications.edit,notifications.manage'])->post('/{id}/archive', [NotificationController::class, 'archive'])->name('admin.notifications.archive');
        Route::middleware(['permission:notifications.delete'])->delete('/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    });

    // 6. Admin Users Management
    Route::prefix('admin/users')->group(function () {
        Route::middleware(['permission:admins.view'])->get('/', [UserController::class, 'index'])->name('admin.users.index');
        Route::middleware(['permission:admins.create'])->get('/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::middleware(['permission:admins.create'])->post('/', [UserController::class, 'store'])->name('admin.users.store');
        Route::middleware(['permission:admins.edit'])->get('/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::middleware(['permission:admins.edit'])->put('/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::middleware(['permission:admins.delete'])->delete('/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // 7. Firebase Settings & Multi-Project Configuration
    Route::prefix('admin/settings/firebase')->group(function () {
        Route::middleware(['permission:firebase_settings.view'])->get('/', [FirebaseSettingController::class, 'index'])->name('admin.settings.firebase.index');
        Route::middleware(['permission:firebase_settings.create'])->get('/create', [FirebaseSettingController::class, 'create'])->name('admin.settings.firebase.create');
        Route::middleware(['permission:firebase_settings.create'])->post('/', [FirebaseSettingController::class, 'store'])->name('admin.settings.firebase.store');
        Route::middleware(['permission:firebase_settings.edit'])->get('/{id}/edit', [FirebaseSettingController::class, 'edit'])->name('admin.settings.firebase.edit');
        Route::middleware(['permission:firebase_settings.edit'])->put('/{id}', [FirebaseSettingController::class, 'update'])->name('admin.settings.firebase.update');
        Route::middleware(['permission:firebase_settings.delete'])->delete('/{id}', [FirebaseSettingController::class, 'destroy'])->name('admin.settings.firebase.destroy');
        Route::middleware(['permission:firebase_settings.edit'])->post('/{id}/activate', [FirebaseSettingController::class, 'activate'])->name('admin.settings.firebase.activate');
        Route::middleware(['permission:firebase_settings.edit'])->post('/{id}/test-connection', [FirebaseSettingController::class, 'testConnection'])->name('admin.settings.firebase.test');
    });

    // 8. Roles & Access Permissions Management
    Route::prefix('admin/roles')->group(function () {
        Route::middleware(['permission:settings.view'])->get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles.index');
        Route::middleware(['permission:settings.edit'])->get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('admin.roles.create');
        Route::middleware(['permission:settings.edit'])->post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('admin.roles.store');
        Route::middleware(['permission:settings.view'])->get('/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'permissions'])->name('admin.roles.permissions');
        Route::middleware(['permission:settings.edit'])->post('/{id}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->name('admin.roles.permissions.update');
        Route::middleware(['permission:settings.edit'])->post('/{id}/copy-permissions', [\App\Http\Controllers\Admin\RoleController::class, 'copyFromRole'])->name('admin.roles.copy-permissions');
        Route::middleware(['permission:settings.edit'])->get('/{id}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::middleware(['permission:settings.edit'])->put('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('admin.roles.update');
        Route::middleware(['permission:settings.edit'])->delete('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('admin.roles.destroy');
    });

    // 9. Ads Management (Supported via both /admin/ad-management and /ad-management)
    Route::prefix('admin/ad-management')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdManagementController::class, 'index'])->name('admin.ads.index');
        Route::get('/configuration', [\App\Http\Controllers\Admin\AdManagementController::class, 'configuration'])->name('admin.ads.configuration');
        Route::post('/configuration', [\App\Http\Controllers\Admin\AdManagementController::class, 'saveConfiguration'])->name('admin.ads.configuration.save');
        Route::post('/test-connection', [\App\Http\Controllers\Admin\AdManagementController::class, 'testConnection'])->name('admin.ads.test-connection');
        Route::get('/oauth/redirect', [\App\Http\Controllers\Admin\AdManagementController::class, 'redirectToGoogle'])->name('admin.ads.oauth.redirect');
        Route::get('/oauth/callback', [\App\Http\Controllers\Admin\AdManagementController::class, 'handleGoogleCallback'])->name('admin.ads.oauth.callback');
        Route::post('/disconnect-google', [\App\Http\Controllers\Admin\AdManagementController::class, 'disconnectGoogle'])->name('admin.ads.disconnect-google');
        
        // Custom Ads
        Route::get('/custom/create', [\App\Http\Controllers\Admin\AdManagementController::class, 'createCustom'])->name('admin.ads.custom.create');
        Route::post('/custom/store', [\App\Http\Controllers\Admin\AdManagementController::class, 'storeCustom'])->name('admin.ads.custom.store');
    });
 
    Route::prefix('ad-management')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdManagementController::class, 'index'])->name('admin.ads.alias-index');
        Route::get('/configuration', [\App\Http\Controllers\Admin\AdManagementController::class, 'configuration'])->name('admin.ads.alias-configuration');
        Route::post('/configuration', [\App\Http\Controllers\Admin\AdManagementController::class, 'saveConfiguration'])->name('admin.ads.alias-configuration.save');
        Route::post('/test-connection', [\App\Http\Controllers\Admin\AdManagementController::class, 'testConnection'])->name('admin.ads.alias-test-connection');
        Route::get('/oauth/redirect', [\App\Http\Controllers\Admin\AdManagementController::class, 'redirectToGoogle'])->name('admin.ads.alias-oauth.redirect');
        Route::get('/oauth/callback', [\App\Http\Controllers\Admin\AdManagementController::class, 'handleGoogleCallback'])->name('admin.ads.alias-oauth.callback');
        Route::post('/disconnect-google', [\App\Http\Controllers\Admin\AdManagementController::class, 'disconnectGoogle'])->name('admin.ads.alias-disconnect-google');
    });
 

    // 10. Profile & Password Management (Available to all authenticated admins)
    Route::prefix('admin/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('admin.profile');
        Route::post('/update', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('admin.profile.change-password');
        Route::post('/change-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');
    });
});

