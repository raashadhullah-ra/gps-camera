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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('city', 100)->index();
            $table->string('state', 100)->index();
            $table->string('country', 100)->index();
            $table->string('country_code', 5)->default('IN');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('timezone', 50)->default('Asia/Kolkata (IST)');
            $table->string('location_level', 50)->default('City');
            $table->string('location_source', 50)->default('GPS + Network');
            $table->string('status', 50)->default('Active')->index(); // High Activity, Active, Inactive
            
            // Core Aggregated Metrics
            $table->unsignedBigInteger('anonymous_users_count')->default(0);
            $table->unsignedBigInteger('devices_count')->default(0);
            $table->unsignedBigInteger('photos_captured_count')->default(0);
            $table->unsignedBigInteger('new_installs_count')->default(0);
            $table->decimal('notification_enabled_percentage', 5, 2)->default(75.00);
            
            // Analytics Breakdowns (JSON)
            $table->json('location_sources')->nullable();       // {"gps": 68, "network": 24, "approximate": 8}
            $table->json('permission_breakdown')->nullable();   // {"precise": 62, "approximate": 21, "denied": 17}
            $table->json('platform_distribution')->nullable();  // {"android": 91, "ios": 9}
            $table->json('top_app_versions')->nullable();       // {"v1.4.2": 48, "v1.4.1": 31, "v1.4.0": 14, "older": 7}
            $table->json('activity_areas')->nullable();          // Sub-areas with coords, counts and heat levels
            $table->json('activity_by_hour_matrix')->nullable(); // 7x24 hour matrix data
            $table->json('activity_types')->nullable();          // photo capture, save, location stamp, share
            $table->json('daily_pattern')->nullable();           // 7 days trend
            
            // Heatmap & Peak Activity Highlights
            $table->string('peak_hour', 20)->default('7 PM');
            $table->string('peak_day', 20)->default('Sunday');
            $table->string('top_area_name', 100)->default('Palayamkottai');
            
            $table->timestamp('last_activity_at')->useCurrent()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
