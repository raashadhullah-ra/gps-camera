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
        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('campaign_id')->unique();
            $table->string('objective');
            $table->string('source')->default('Custom Campaign');
            $table->string('format');
            $table->boolean('is_active')->default(false);
            $table->string('conversion_goal')->nullable();
            $table->string('destination_type')->nullable();
            $table->string('destination')->nullable();
            $table->string('tracking_event')->nullable();
            $table->string('image_path')->nullable();
            $table->string('headline')->nullable();
            $table->text('description')->nullable();
            $table->string('call_to_action')->nullable();
            $table->string('destination_url')->nullable();
            $table->string('alt_text')->nullable();
            $table->json('placements')->nullable();
            $table->unsignedBigInteger('audience_segment_id')->nullable();
            $table->json('countries')->nullable();
            $table->json('platforms')->nullable();
            $table->string('min_app_version')->nullable();
            $table->json('device_languages')->nullable();
            $table->boolean('exclude_subscribed')->default(false);
            $table->string('consent_eligibility')->nullable();
            $table->integer('frequency_cap')->nullable();
            $table->string('frequency_per_user')->nullable();
            $table->integer('max_impressions')->nullable();
            $table->boolean('stop_at_limit')->default(false);
            $table->decimal('daily_budget', 10, 2)->nullable();
            $table->string('pacing')->nullable();
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->string('timezone')->nullable();
            $table->string('delivery_type')->nullable();
            $table->string('status')->default('Draft');
            $table->string('publish_option')->nullable();
            $table->boolean('notify_admins')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_campaigns');
    }
};
