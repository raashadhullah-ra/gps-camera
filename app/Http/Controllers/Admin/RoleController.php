<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display listing of all roles with summary metrics.
     */
    public function index(Request $request): View
    {
        $query = Role::with(['permissions', 'users']);

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('code', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('role_type') && $request->role_type !== 'all') {
            $query->where('role_type', $request->role_type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $roles = $query->orderByRaw("CASE WHEN role_type = 'system' THEN 1 ELSE 2 END")
                       ->orderBy('name')
                       ->paginate(10)
                       ->withQueryString();

        $metrics = [
            'total_roles'  => Role::count(),
            'active_roles' => Role::where('is_active', true)->count(),
            'system_roles' => Role::where('role_type', 'system')->count(),
            'custom_roles' => Role::where('role_type', 'custom')->count(),
        ];

        return view('admin.roles.index', compact('roles', 'metrics'));
    }

    /**
     * Show the Add Role form screen (matching reference image 2).
     */
    public function create(): View
    {
        $existingRoles = Role::with('permissions')->orderBy('name')->get();
        $adminUsers = User::orderBy('name')->get();

        // Sample default regions and departments for quick selection
        $availableRegions = ['Tamil Nadu', 'Kerala', 'Karnataka', 'Maharashtra', 'Delhi NCR', 'Gujarat', 'Andhra Pradesh', 'Telangana'];
        $availableDepartments = ['Operations', 'Support', 'Analytics', 'Marketing', 'Executive', 'Product Management'];

        $modules = $this->getModuleDefinitions();

        return view('admin.roles.create', compact('existingRoles', 'adminUsers', 'availableRegions', 'availableDepartments', 'modules'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                 => 'required|string|min:3|max:25|unique:roles,name',
            'code'                 => 'required|string|max:100|unique:roles,code',
            'role_type'            => 'required|in:custom,system',
            'scope_type'           => 'required|in:full_org,restricted',
            'assigned_regions'     => 'nullable|array',
            'assigned_departments' => 'nullable|array',
            'data_visibility'      => 'nullable|string|max:50',
            'restrict_exports'     => 'nullable|boolean',
            'is_active'            => 'nullable|boolean',
            'description'          => 'nullable|string|max:500',
            'template_role_id'     => 'nullable|exists:roles,id',
            'assigned_admin_ids'   => 'nullable|array',
            'assigned_admin_ids.*' => 'exists:users,id',
        ]);

        $role = new Role();
        $role->name = $validated['name'];
        $role->code = strtoupper(str_replace([' ', '-'], '_', $validated['code']));
        $role->role_type = $validated['role_type'];
        $role->scope_type = $validated['scope_type'];
        $role->assigned_regions = $validated['scope_type'] === 'restricted' ? ($validated['assigned_regions'] ?? []) : null;
        $role->assigned_departments = $validated['scope_type'] === 'restricted' ? ($validated['assigned_departments'] ?? []) : null;
        $role->data_visibility = $validated['data_visibility'] ?? ($validated['scope_type'] === 'restricted' ? 'assigned_regions_only' : 'all');
        $role->restrict_exports = $request->boolean('restrict_exports');
        $role->is_active = $request->boolean('is_active', true);
        $role->description = $validated['description'] ?? null;
        $role->created_by = auth()->id();
        $role->save();

        // If a template role was selected, clone its permissions
        if (!empty($validated['template_role_id'])) {
            $templateRole = Role::find($validated['template_role_id']);
            if ($templateRole) {
                $role->permissions()->sync($templateRole->permissions->pluck('id')->toArray());
            }
        }

        // Attach assigned administrators
        if (!empty($validated['assigned_admin_ids'])) {
            $role->users()->sync($validated['assigned_admin_ids']);
        }

        if ($request->input('action') === 'configure_permissions') {
            return redirect()->route('admin.roles.permissions', ['role_id' => $role->id])
                ->with('success', "Role '{$role->name}' created successfully. Now customize its permission matrix.");
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Show the Edit Role form screen.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $role = Role::with(['permissions', 'users'])->findOrFail($id);

        // Protect Super Admin from editing
        if ($role->code === 'SUPER_ADMIN') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin is a protected system role with permanent full access and cannot be modified.');
        }

        $existingRoles = Role::where('id', '!=', $id)->with('permissions')->orderBy('name')->get();
        $adminUsers = User::orderBy('name')->get();

        $availableRegions = ['Tamil Nadu', 'Kerala', 'Karnataka', 'Maharashtra', 'Delhi NCR', 'Gujarat', 'Andhra Pradesh', 'Telangana'];
        $availableDepartments = ['Operations', 'Support', 'Analytics', 'Marketing', 'Executive', 'Product Management'];

        $modules = $this->getModuleDefinitions();

        return view('admin.roles.edit', compact('role', 'existingRoles', 'adminUsers', 'availableRegions', 'availableDepartments', 'modules'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        // Protect Super Admin
        if ($role->code === 'SUPER_ADMIN') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin is a protected system role and cannot be modified.');
        }

        $validated = $request->validate([
            'name'                 => "required|string|min:3|max:25|unique:roles,name,{$id}",
            'code'                 => "required|string|max:100|unique:roles,code,{$id}",
            'role_type'            => 'required|in:custom,system',
            'scope_type'           => 'required|in:full_org,restricted',
            'assigned_regions'     => 'nullable|array',
            'assigned_departments' => 'nullable|array',
            'data_visibility'      => 'nullable|string|max:50',
            'restrict_exports'     => 'nullable|boolean',
            'is_active'            => 'nullable|boolean',
            'description'          => 'nullable|string|max:500',
            'assigned_admin_ids'   => 'nullable|array',
            'assigned_admin_ids.*' => 'exists:users,id',
        ]);

        $role->name = $validated['name'];
        $role->code = strtoupper(str_replace([' ', '-'], '_', $validated['code']));
        $role->role_type = $validated['role_type'];
        $role->scope_type = $validated['scope_type'];
        $role->assigned_regions = $validated['scope_type'] === 'restricted' ? ($validated['assigned_regions'] ?? []) : null;
        $role->assigned_departments = $validated['scope_type'] === 'restricted' ? ($validated['assigned_departments'] ?? []) : null;
        $role->data_visibility = $validated['data_visibility'] ?? ($validated['scope_type'] === 'restricted' ? 'assigned_regions_only' : 'all');
        $role->restrict_exports = $request->boolean('restrict_exports');
        $role->is_active = $request->boolean('is_active', true);
        $role->description = $validated['description'] ?? null;
        $role->save();

        if (isset($validated['assigned_admin_ids'])) {
            $role->users()->sync($validated['assigned_admin_ids']);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        if ($role->role_type === 'system' || $role->code === 'SUPER_ADMIN') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'System roles cannot be deleted as they are protected by system policy.');
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' deleted successfully.");
    }

    /**
     * Display the Assign Permissions matrix screen (matching reference image 1).
     */
    public function permissions(Request $request): View
    {
        $roles = Role::with(['permissions', 'users'])->orderBy('name')->get();

        // Selected role from query param or default to Regional Operations Manager / first role
        $selectedRoleId = (int) $request->input('role_id', 0);
        $selectedRole = $roles->firstWhere('id', $selectedRoleId) 
            ?? $roles->firstWhere('code', 'REGIONAL_OPS_MANAGER') 
            ?? $roles->first();

        $allPermissions = Permission::orderBy('module')->orderBy('action')->get();
        $modules = $this->getModuleDefinitions();

        // Group permissions by module
        $permissionsByModule = $allPermissions->groupBy('module');

        // Current assigned permission IDs for the selected role
        $assignedPermissionIds = $selectedRole ? $selectedRole->permissions->pluck('id')->toArray() : [];
        $assignedPermissionNames = $selectedRole ? $selectedRole->permissions->pluck('name')->toArray() : [];

        return view('admin.roles.permissions', compact(
            'roles',
            'selectedRole',
            'modules',
            'permissionsByModule',
            'assignedPermissionIds',
            'assignedPermissionNames'
        ));
    }

    /**
     * Save updated permissions matrix for a role.
     */
    public function updatePermissions(Request $request, int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        // Protect Super Admin
        if ($role->code === 'SUPER_ADMIN') {
            return redirect()->route('admin.roles.permissions', ['role_id' => $role->id])
                ->with('error', 'Super Admin is a protected system role. All permissions are permanently granted and cannot be modified.');
        }

        $permissionIds = $request->input('permissions', []);
        $role->permissions()->sync($permissionIds);

        return redirect()->route('admin.roles.permissions', ['role_id' => $role->id])
            ->with('success', "Permissions updated successfully for '{$role->name}'.");
    }

    /**
     * Copy permissions from another role (AJAX or form POST).
     */
    public function copyFromRole(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $targetRole = Role::findOrFail($id);

        if ($targetRole->code === 'SUPER_ADMIN') {
            return redirect()->route('admin.roles.permissions', ['role_id' => $targetRole->id])
                ->with('error', 'Super Admin is a protected system role and cannot be modified.');
        }

        $sourceRoleId = (int) $request->input('source_role_id');
        $sourceRole = Role::with('permissions')->findOrFail($sourceRoleId);

        $permIds = $sourceRole->permissions->pluck('id')->toArray();
        $targetRole->permissions()->sync($permIds);

        if ($request->wantsJson()) {
            return response()->json([
                'success'          => true,
                'message'          => "Copied {$sourceRole->permissions->count()} permissions from {$sourceRole->name}.",
                'permission_ids'   => $permIds,
                'permission_names' => $sourceRole->permissions->pluck('name')->toArray(),
            ]);
        }

        return redirect()->route('admin.roles.permissions', ['role_id' => $targetRole->id])
            ->with('success', "Copied permissions from {$sourceRole->name} successfully.");
    }

    /**
     * Helper returning canonical definition of all 12 modules, icons, groups, and available actions.
     */
    protected function getModuleDefinitions(): array
    {
        return [
            'dashboard' => [
                'name'     => 'Dashboard',
                'group'    => 'Overview',
                'icon'     => 'fa-solid fa-table-cells-large',
                'actions'  => ['view'],
            ],
            'devices' => [
                'name'     => 'Installed Devices',
                'group'    => 'Installations',
                'icon'     => 'fa-solid fa-mobile-screen-button',
                'actions'  => ['view', 'create', 'edit', 'delete', 'export', 'manage'],
            ],
            'locations' => [
                'name'     => 'Locations',
                'group'    => 'Installations',
                'icon'     => 'fa-solid fa-location-dot',
                'actions'  => ['view', 'export'],
            ],
            'notifications' => [
                'name'     => 'Notifications',
                'group'    => 'Engagement',
                'icon'     => 'fa-solid fa-bell',
                'actions'  => ['view', 'create', 'edit', 'delete', 'export', 'manage'],
            ],
            'segments' => [
                'name'     => 'Audience Segments',
                'group'    => 'Engagement',
                'icon'     => 'fa-solid fa-users',
                'actions'  => ['view', 'create', 'edit', 'delete', 'export', 'manage'],
            ],
            'analytics' => [
                'name'     => 'Analytics',
                'group'    => 'Engagement',
                'icon'     => 'fa-solid fa-chart-line',
                'actions'  => ['view', 'export'],
            ],
            'crash_reports' => [
                'name'     => 'Crash Reports',
                'group'    => 'Engagement',
                'icon'     => 'fa-solid fa-triangle-exclamation',
                'actions'  => ['view', 'export', 'manage'],
            ],
            'remote_config' => [
                'name'     => 'Remote Config',
                'group'    => 'App Control',
                'icon'     => 'fa-solid fa-sliders',
                'actions'  => ['view', 'create', 'edit', 'delete', 'manage'],
            ],
            'app_versions' => [
                'name'     => 'App Versions',
                'group'    => 'App Control',
                'icon'     => 'fa-solid fa-cloud-arrow-up',
                'actions'  => ['view', 'create', 'edit', 'delete', 'manage'],
            ],
            'ads' => [
                'name'     => 'Ads Management',
                'group'    => 'App Control',
                'icon'     => 'fa-solid fa-rectangle-ad',
                'actions'  => ['view', 'create', 'edit', 'delete', 'manage'],
            ],
            'firebase_settings' => [
                'name'     => 'Firebase Settings',
                'group'    => 'System',
                'icon'     => 'fa-solid fa-fire',
                'actions'  => ['view', 'create', 'edit', 'delete', 'manage'],
            ],
            'admins' => [
                'name'      => 'Admin Management',
                'group'     => 'System',
                'icon'      => 'fa-solid fa-user-gear',
                'actions'   => ['view', 'create', 'edit', 'delete', 'manage'],
                'is_locked' => true,
            ],
            'settings' => [
                'name'      => 'Settings',
                'group'     => 'System',
                'icon'      => 'fa-solid fa-gear',
                'actions'   => ['view', 'edit', 'manage'],
                'is_locked' => true,
            ],
        ];
    }
}
