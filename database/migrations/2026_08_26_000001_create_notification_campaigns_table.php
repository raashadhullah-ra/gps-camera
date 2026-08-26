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
        Schema::create('notification_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_id', 50)->unique();
            $table->string('name', 150);
            $table->string('title', 150);
            $table->text('message');
            $table->string('action', 50)->default('Open App');
            $table->string('action_url', 255)->nullable();
            
            // Audience Targeting
            $table->enum('audience_type', ['individual', 'segment', 'all'])->default('segment');
            $table->string('audience_label', 150)->nullable();
            $table->foreignId('segment_id')->nullable()->constrained('audience_segments')->nullOnDelete();
            $table->json('target_device_ids')->nullable();
            $table->unsignedInteger('android_count')->default(0);
            $table->unsignedInteger('ios_count')->default(0);
            $table->unsignedInteger('total_audience')->default(0);
            
            // Delivery Stats
            $table->unsignedInteger('delivered_count')->default(0);
            $table->unsignedInteger('open_count')->default(0);
            $table->decimal('open_rate', 5, 2)->nullable();
            
            // Scheduling & Status
            $table->enum('status', ['draft', 'scheduled', 'sent', 'failed', 'archived'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('time_zone', 50)->default('Asia/Kolkata');
            $table->boolean('quiet_hours_enabled')->default(true);
            $table->unsignedSmallInteger('expiry_hours')->default(24);
            
            // Workflow History
            $table->json('timeline_steps')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_campaigns');
    }
};
