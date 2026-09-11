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
        // 1. Roles Table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                          // e.g. "Regional Operations Manager"
            $table->string('code', 100)->unique()->nullable();          // e.g. "REGIONAL_OPS_MANAGER"
            $table->enum('role_type', ['system', 'custom'])->default('custom'); // System Role vs Custom Role
            $table->enum('scope_type', ['full_org', 'restricted'])->default('full_org'); // Access Scope
            $table->json('assigned_regions')->nullable();               // ['Tamil Nadu', 'Kerala']
            $table->json('assigned_departments')->nullable();           // ['Operations', 'Support']
            $table->string('data_visibility', 50)->default('all');      // 'assigned_regions_only', 'all'
            $table->boolean('restrict_exports')->default(false);        // Restrict exports to assigned scope
            $table->boolean('is_active')->default(true);                // Active / Inactive
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Permissions Table (Spatie / Laravel Standard format: {module}.{action})
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                           // e.g. "devices.view", "notifications.create"
            $table->string('guard_name')->default('web');
            $table->string('module', 50);                               // e.g. "devices", "notifications", "locations"
            $table->string('action', 50);                               // e.g. "view", "create", "edit", "delete", "export", "manage"
            $table->string('display_name')->nullable();                 // Human readable label
            $table->string('description')->nullable();
            $table->boolean('is_locked')->default(false);               // Restricted by system policy
            $table->timestamps();

            $table->index(['module', 'action']);
        });

        // 3. Role-Permission Pivot Table
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        // 4. User-Role Pivot Table (Supports multiple roles or primary role)
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
