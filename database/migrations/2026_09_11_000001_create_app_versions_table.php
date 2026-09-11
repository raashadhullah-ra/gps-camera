<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('GPS Camera');
            $table->string('platform')->default('all'); // all, android, ios
            $table->string('current_version')->default('1.0.0'); // Latest available version
            $table->string('minimum_version')->default('1.0.0'); // Below this requires force update
            $table->boolean('force_update')->default(false); // Master toggle for forcing update
            $table->string('update_title')->nullable()->default('New Update Available');
            $table->text('update_message')->nullable()->default('A new version of GPS Camera is available. Please update the app to continue enjoying all features.');
            $table->string('play_store_url')->nullable();
            $table->string('app_store_url')->nullable();
            $table->boolean('is_under_maintenance')->default(false);
            $table->text('maintenance_message')->nullable()->default('GPS Camera is currently undergoing scheduled maintenance. Please try again shortly.');
            $table->timestamps();
        });

        // Insert default initial row
        DB::table('app_versions')->insert([
            'app_name' => 'GPS Camera',
            'platform' => 'all',
            'current_version' => '1.0.0',
            'minimum_version' => '1.0.0',
            'force_update' => false,
            'update_title' => 'New Update Available',
            'update_message' => 'A new version of GPS Camera is available with performance improvements and bug fixes. Please update to continue.',
            'play_store_url' => 'https://play.google.com/store/apps/details?id=com.geocam.app',
            'app_store_url' => 'https://apps.apple.com/app/gps-camera',
            'is_under_maintenance' => false,
            'maintenance_message' => 'GPS Camera is currently undergoing scheduled maintenance. Please check back shortly.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
