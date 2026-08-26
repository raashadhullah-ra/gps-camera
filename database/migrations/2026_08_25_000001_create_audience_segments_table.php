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
        Schema::create('audience_segments', function (Blueprint $table) {
            $table->id();
            $table->string('segment_id', 50)->unique();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->enum('type', ['dynamic', 'static'])->default('dynamic');
            $table->enum('status', ['active', 'draft', 'paused', 'archived'])->default('active');
            
            // Visual Rule Engine JSON
            $table->json('rule_groups');
            $table->json('platform_filters')->nullable();
            $table->json('exclusions')->nullable();

            // Pause / Archive metadata
            $table->enum('pause_duration_type', ['manual', 'until_date'])->nullable();
            $table->date('paused_until')->nullable();
            $table->string('pause_reason', 150)->nullable();
            $table->string('archive_reason', 150)->nullable();

            // Summary & Cached Counts
            $table->string('location_summary', 150)->default('All Locations');
            $table->unsignedInteger('audience_size')->default(0);
            $table->unsignedInteger('deliverable_count')->default(0);
            $table->unsignedInteger('excluded_count')->default(0);
            $table->json('platform_distribution')->nullable();

            $table->timestamp('last_synced_at')->nullable();
            $table->boolean('auto_refresh_enabled')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audience_segments');
    }
};
