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
        Schema::create('segment_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('segment_id')->constrained('audience_segments')->cascadeOnDelete();
            $table->string('action', 50); // 'created', 'rules_updated', 'audience_refreshed', 'paused', 'resumed', 'archived', 'restored'
            $table->string('description', 255);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segment_activity_logs');
    }
};
