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
        Schema::create('firebase_settings', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable(); // friendly name, e.g. "Client A - prod"
            $table->string('project_id')->nullable();
            $table->string('api_key')->nullable();
            $table->string('auth_domain')->nullable();
            $table->string('storage_bucket')->nullable();
            $table->string('messaging_sender_id')->nullable();
            $table->string('app_id')->nullable();
            $table->string('measurement_id')->nullable();
            $table->string('vapid_key')->nullable();
            $table->longText('service_account_json')->nullable(); // store encrypted via model cast
            $table->boolean('is_active')->default(false); // only ONE row should be true, enforce in app layer

            // Admin UI feedback fields
            $table->string('connection_status')->default('untested'); // 'connected', 'failed', 'untested'
            $table->text('last_error_message')->nullable(); // short human-readable message, not a full stack trace
            $table->timestamp('last_tested_at')->nullable(); // e.g. "Verified 5 mins ago"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firebase_settings');
    }
};
