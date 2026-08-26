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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('displayname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('dob')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->text('address')->nullable();
            $table->string('mobilenumber')->nullable();
            $table->string('language')->default('en');
            $table->string('timezone')->default('UTC');
            $table->string('admin_id')->nullable()->unique();
            $table->string('role')->default('Super Admin');
            $table->string('department')->nullable();
            $table->boolean('two_factor_enabled')->default(true);
            $table->tinyInteger('status')->default(1); // 0 = Pending, 1 = Active, 2 = Suspended/Blocked
            $table->integer('active_sessions_count')->default(1);
            $table->date('doj')->nullable(); // Date of Joining
            $table->date('dor')->nullable(); // Date of Resignation / Renewal
            $table->boolean('isblocked')->default(false);
            $table->timestamp('lastloginat')->nullable();
            $table->timestamp('passwordchangedat')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
