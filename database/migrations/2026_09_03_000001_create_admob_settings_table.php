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
        Schema::create('admob_settings', function (Blueprint $table) {
            $table->id();

            // 1. Publisher Identifiers (Manual paste from Google AdMob Console)
            $table->string('publisher_id')->nullable(); // e.g. ca-pub-XXXXXXXXXXXXXXXX
            $table->string('contact_email')->nullable();
            $table->string('reporting_currency', 20)->default('INR (₹)');

            // 2. Mobile App Registrations
            $table->boolean('android_enabled')->default(false);
            $table->string('android_package_name')->nullable();
            $table->string('android_app_id')->nullable(); // ca-app-pub-XXXXXXXXXXXXXXXX~XXXXXXXXXX

            $table->boolean('ios_enabled')->default(false);
            $table->string('ios_bundle_id')->nullable();
            $table->string('ios_app_id')->nullable(); // ca-app-pub-XXXXXXXXXXXXXXXX~XXXXXXXXXX

            // 3. Background Sync & Reporting
            $table->boolean('enable_reporting')->default(true);
            $table->string('sync_frequency')->default('Every 6 hours');
            $table->string('default_report_range')->default('Last 30 days');
            $table->string('connection_status')->default('Not Connected'); // Connected, Not Connected, Pending
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_sync_message')->nullable();

            // 4. Test Ads & QA Devices
            $table->boolean('enable_test_ads')->default(true);
            $table->text('test_device_ids')->nullable();

            // 5. Google OAuth 2.0 Credentials & Tokens (Real OAuth Flow)
            $table->text('google_client_id')->nullable();
            $table->text('google_client_secret')->nullable();
            $table->boolean('is_connected')->default(false);
            $table->string('connected_email')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admob_settings');
    }
};
