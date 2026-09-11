<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definition of the 12 Canonical Modules and their supported actions
        $modules = [
            'dashboard' => [
                'name'    => 'Dashboard',
                'icon'    => 'fa-solid fa-table-cells-large',
                'actions' => ['view'],
            ],
            'devices' => [
                'name'    => 'Installed Devices',
                'icon'    => 'fa-solid fa-mobile-screen-button',
                'actions' => ['view', 'create', 'edit', 'delete', 'export', 'manage'],
            ],
            'locations' => [
                'name'    => 'Locations',
                'icon'    => 'fa-solid fa-location-dot',
                'actions' => ['view', 'export'],
            ],
            'notifications' => [
                'name'    => 'Notifications',
                'icon'    => 'fa-solid fa-bell',
                'actions' => ['view', 'create', 'edit', 'delete', 'export', 'manage'],
            ],
            'segments' => [
                'name'    => 'Audience Segments',
                'icon'    => 'fa-solid fa-users',
                'actions' => ['view', 'create', 'edit', 'delete', 'export', 'manage'],
            ],
            'analytics' => [
                'name'    => 'Analytics',
                'icon'    => 'fa-solid fa-chart-line',
                'actions' => ['view', 'export'],
            ],
            'crash_reports' => [
                'name'    => 'Crash Reports',
                'icon'    => 'fa-solid fa-triangle-exclamation',
                'actions' => ['view', 'export', 'manage'],
            ],
            'remote_config' => [
                'name'    => 'Remote Config',
                'icon'    => 'fa-solid fa-sliders',
                'actions' => ['view', 'create', 'edit', 'delete', 'manage'],
            ],
            'app_versions' => [
                'name'    => 'App Versions',
                'icon'    => 'fa-solid fa-cloud-arrow-up',
                'actions' => ['view', 'create', 'edit', 'delete', 'manage'],
            ],
            'ads' => [
                'name'    => 'Ads Management',
                'icon'    => 'fa-solid fa-rectangle-ad',
                'actions' => ['view', 'create', 'edit', 'delete', 'manage'],
            ],
            'firebase_settings' => [
                'name'    => 'Firebase Settings',
                'icon'    => 'fa-solid fa-fire',
                'actions' => ['view', 'create', 'edit', 'delete', 'manage'],
            ],
            'admins' => [
                'name'      => 'Admin Management',
                'icon'      => 'fa-solid fa-user-gear',
                'actions'   => ['view', 'create', 'edit', 'delete', 'manage'],
                'is_locked' => true, // Locked for system policy
            ],
            'settings' => [
                'name'      => 'Settings',
                'icon'      => 'fa-solid fa-gear',
                'actions'   => ['view', 'edit', 'manage'],
                'is_locked' => true, // Locked for system policy
            ],
        ];

        // Delete obsolete anonymous users permissions if any
        Permission::where('module', 'users')->delete();

        // 2. Insert all permissions into database
        $createdPermissions = [];
        foreach ($modules as $moduleKey => $meta) {
            $isLocked = $meta['is_locked'] ?? false;
            foreach ($meta['actions'] as $act) {
                $permName = "{$moduleKey}.{$act}";
                $displayName = ucfirst($act) . ' ' . $meta['name'];

                $perm = Permission::updateOrCreate(
                    ['name' => $permName],
                    [
                        'guard_name'   => 'web',
                        'module'       => $moduleKey,
                        'action'       => $act,
                        'display_name' => $displayName,
                        'description'  => "Allows user to {$act} {$meta['name']}",
                        'is_locked'    => $isLocked,
                    ]
                );

                $createdPermissions[$permName] = $perm->id;
            }
        }

        // 3. Seed Standard Roles

        // A. Super Admin (Protected System Role)
        $superAdminRole = Role::updateOrCreate(
            ['code' => 'SUPER_ADMIN'],
            [
                'name'                 => 'Super Admin',
                'role_type'            => 'system',
                'scope_type'           => 'full_org',
                'assigned_regions'     => null,
                'assigned_departments' => null,
                'data_visibility'      => 'all',
                'restrict_exports'     => false,
                'is_active'            => true,
                'description'          => 'Unrestricted full access to all system features, modules, security policies, and administrator management.',
            ]
        );
        $superAdminRole->permissions()->sync(array_values($createdPermissions));

        // B. Regional Operations Manager (Custom Role)
        $regOpsRole = Role::updateOrCreate(
            ['code' => 'REGIONAL_OPS_MANAGER'],
            [
                'name'                 => 'Regional Operations Manager',
                'role_type'            => 'custom',
                'scope_type'           => 'restricted',
                'assigned_regions'     => ['Tamil Nadu', 'Kerala'],
                'assigned_departments' => ['Operations', 'Support'],
                'data_visibility'      => 'assigned_regions_only',
                'restrict_exports'     => true,
                'is_active'            => true,
                'description'          => 'Manages device operations, analytics and support activities for assigned regions.',
            ]
        );

        $regOpsPerms = [
            'dashboard.view',
            'devices.view',
            'devices.create',
            'devices.edit',
            'devices.export',
            'devices.manage',
            'locations.view',
            'locations.export',
            'analytics.view',
            'analytics.export',
            'crash_reports.view',
            'crash_reports.export',
            'crash_reports.manage',
            'remote_config.view',
            'remote_config.create',
            'remote_config.edit',
            'remote_config.manage',
            'firebase_settings.view',
        ];

        $regOpsPermIds = [];
        foreach ($regOpsPerms as $pName) {
            if (isset($createdPermissions[$pName])) {
                $regOpsPermIds[] = $createdPermissions[$pName];
            }
        }
        $regOpsRole->permissions()->sync($regOpsPermIds);

        // C. Operations Admin (Template)
        $opsAdminRole = Role::updateOrCreate(
            ['code' => 'OPERATIONS_ADMIN'],
            [
                'name'                 => 'Operations Admin',
                'role_type'            => 'custom',
                'scope_type'           => 'full_org',
                'assigned_regions'     => null,
                'assigned_departments' => ['Operations'],
                'data_visibility'      => 'all',
                'restrict_exports'     => false,
                'is_active'            => true,
                'description'          => 'Standard operational administration covering devices, locations, campaigns and telemetry.',
            ]
        );

        $opsPerms = [
            'dashboard.view',
            'devices.view', 'devices.create', 'devices.edit', 'devices.export',
            'locations.view', 'locations.export',
            'notifications.view', 'notifications.create', 'notifications.edit', 'notifications.export',
            'segments.view', 'segments.create', 'segments.edit',
            'analytics.view', 'analytics.export',
            'crash_reports.view',
            'firebase_settings.view',
        ];
        $opsPermIds = [];
        foreach ($opsPerms as $pName) {
            if (isset($createdPermissions[$pName])) {
                $opsPermIds[] = $createdPermissions[$pName];
            }
        }
        $opsAdminRole->permissions()->sync($opsPermIds);

        // D. Viewer / Read Only
        $viewerRole = Role::updateOrCreate(
            ['code' => 'VIEWER'],
            [
                'name'                 => 'Viewer',
                'role_type'            => 'custom',
                'scope_type'           => 'full_org',
                'assigned_regions'     => null,
                'assigned_departments' => null,
                'data_visibility'      => 'all',
                'restrict_exports'     => false,
                'is_active'            => true,
                'description'          => 'Read-only visibility across dashboards, devices, locations, and analytics.',
            ]
        );

        $viewerPerms = [
            'dashboard.view',
            'devices.view',
            'locations.view',
            'notifications.view',
            'segments.view',
            'analytics.view',
            'crash_reports.view',
        ];
        $viewerPermIds = [];
        foreach ($viewerPerms as $pName) {
            if (isset($createdPermissions[$pName])) {
                $viewerPermIds[] = $createdPermissions[$pName];
            }
        }
        $viewerRole->permissions()->sync($viewerPermIds);

        // 4. Attach Super Admin Role to first user if exists
        $adminUser = User::first();
        if ($adminUser) {
            $adminUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }
    }
}
