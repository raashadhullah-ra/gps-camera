<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('installation_id', 64)->unique()->index();       // e.g. INS-8F29A1
            $table->string('firebase_installation_id', 64)->nullable();    // e.g. c7F9...K2mP (Firebase FID)
            $table->string('hardware_id', 128)->nullable()->index();        // Android ID / iOS IDFV
            $table->text('fcm_token')->nullable();                          // Firebase Push Notification Token
            
            // Device Hardware Specs (from Device Details screen)
            $table->string('device_manufacturer', 50)->nullable();         // Samsung, Apple, Xiaomi
            $table->string('device_brand', 50)->nullable();                // Samsung, Apple, Xiaomi
            $table->string('device_model', 100)->nullable();               // Galaxy S24, iPhone 16
            $table->string('device_code', 50)->nullable();                // SM-S921B, iPhone16,1
            $table->string('cpu_architecture', 30)->default('arm64-v8a');  // arm64-v8a, armeabi-v7a, x86_64
            $table->enum('platform', ['Android', 'iOS'])->default('Android')->index();
            $table->string('os_version', 30)->nullable();                  // Android 15, iOS 17.4.1
            $table->integer('sdk_version')->nullable()->default(35);       // Android SDK 35 / iOS SDK
            $table->string('app_version', 20)->default('1.4.2')->index();  // 1.4.2
            $table->integer('app_build_number')->default(42);              // Build 42
            $table->string('screen_resolution', 30)->nullable();           // 1080 x 2340
            
            // Geo Location & Network
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 100)->nullable();                    // India, United States, Brazil
            $table->string('country_code', 5)->nullable();                 // IN, US, BR, ID, GB, AE
            $table->string('state', 100)->nullable();                      // Tamil Nadu, New York
            $table->string('city', 100)->nullable();                       // Tirunelveli, New York
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('timezone', 50)->default('Asia/Kolkata');
            $table->string('language', 30)->default('English');
            
            // Granular Permissions JSON (Camera, Location, Notifications, Photos & Media)
            $table->json('permissions')->nullable();                       // {"camera":"granted","location":"precise","notifications":"granted","photos_media":"granted"}
            $table->string('permissions_status', 100)->default('Camera + Location granted'); // Table overview display summary
            
            // Notification Status & Telemetry
            $table->string('notification_status', 30)->default('Enabled'); // Enabled, Disabled, Invalid Token
            $table->timestamp('fcm_token_updated_at')->nullable();
            $table->timestamp('last_notification_delivered_at')->nullable();
            $table->timestamp('last_notification_opened_at')->nullable();
            
            // Usage, Deactivation & Lifecycle
            $table->unsignedBigInteger('total_photos_taken')->default(0);
            $table->unsignedBigInteger('total_sessions_count')->default(1);
            $table->boolean('is_active')->default(true)->index();
            $table->string('inactive_reason')->nullable();                  // User Uninstalled App, Device Inactive, Duplicate
            $table->text('admin_notes')->nullable();                       // Optional internal admin note
            $table->boolean('is_blacklisted')->default(false);
            
            $table->timestamp('first_installed_at')->useCurrent();
            $table->timestamp('last_active_at')->useCurrent()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
