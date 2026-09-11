@extends('layout')

@section('title', 'Roles & Access - GeoCam Admin')
@section('page_title', 'Roles & Access')

@section('breadcrumbs')
    <span class="text-muted">Settings</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Roles & Access</span>
@endsection

@section('content')
<div class="roles-page-view devices-page roles-page">

    {{-- 1. Page Header Row --}}
    <div class="page-header-row">
        <div class="header-title-group">
            <h1 class="page-main-title">Roles & Access</h1>
            <p class="page-main-subtitle">Manage administrator roles, scoping rules, and permission matrices</p>
        </div>
        <div class="header-actions-group">
            <a href="{{ route('admin.roles.permissions') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-table-cells"></i>
                <span>Assign Permissions</span>
            </a>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>
                <span>Add Role</span>
            </a>
        </div>
    </div>

    {{-- 2. Top 4 Metric Stat Summary Cards (Full 100% Width Grid) --}}
    <div class="stat-grid-row">
        <!-- 1. Total Roles -->
        <div class="stat-card">
            <div class="stat-icon-wrapper blue-bg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Roles</div>
                <div class="stat-value">{{ $metrics['total_roles'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-layer-group fs-10"></i> Configured
                </div>
            </div>
        </div>

        <!-- 2. Active Roles -->
        <div class="stat-card">
            <div class="stat-icon-wrapper green-bg">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active Roles</div>
                <div class="stat-value">{{ $metrics['active_roles'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-circle-dot fs-10 text-success"></i> Operational
                </div>
            </div>
        </div>

        <!-- 3. System Roles -->
        <div class="stat-card">
            <div class="stat-icon-wrapper purple-bg">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">System Roles</div>
                <div class="stat-value">{{ $metrics['system_roles'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-shield fs-10 text-purple"></i> Protected
                </div>
            </div>
        </div>

        <!-- 4. Custom Roles -->
        <div class="stat-card">
            <div class="stat-icon-wrapper orange-bg">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Custom Roles</div>
                <div class="stat-value">{{ $metrics['custom_roles'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-sliders fs-10 text-warning"></i> Editable
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Filters Toolbar Card (Exact Devices Structure & Search Input) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-2 p-3 bg-white">
        <div class="fs-13 fw-bold text-dark mb-2">Filters</div>
        <form action="{{ route('admin.roles.index') }}" method="GET" id="rolesFilterForm" class="d-flex align-items-center gap-2 w-100 flex-nowrap">
            <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">

            <!-- 1. Exact Compact Search Box from Devices -->
            <div class="position-relative filter-search-wrap" style="flex: 0 0 260px; width: 260px; max-width: 260px;">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted fs-12 filter-search-icon"></i>
                <input type="text" name="search" class="form-control form-control-sm rounded-2 fs-12 filter-search-input" placeholder="Search by role or code" value="{{ request('search') }}">
            </div>

            <!-- 2. Role Type Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="role_type" id="filter_role_type" value="{{ request('role_type', 'all') }}">
                <button class="btn filter-dropdown-btn w-dropdown-status" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownRoleTypeBtn" style="min-width: 120px;">
                    <span class="filter-box-label">Role Type</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_role_type">{{ request('role_type') && request('role_type') != 'all' ? ucfirst(request('role_type')) : 'All' }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownRoleTypeBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ !request('role_type') || request('role_type') == 'all' ? 'active' : '' }}" href="javascript:void(0)" onclick="setRoleFilter('role_type', 'all', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('role_type') == 'custom' ? 'active' : '' }}" href="javascript:void(0)" onclick="setRoleFilter('role_type', 'custom', 'Custom')">Custom Roles</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('role_type') == 'system' ? 'active' : '' }}" href="javascript:void(0)" onclick="setRoleFilter('role_type', 'system', 'System')">System Roles</a></li>
                </ul>
            </div>

            <!-- 3. Status Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="status" id="filter_status" value="{{ request('status', 'all') }}">
                <button class="btn filter-dropdown-btn w-dropdown-status" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownStatusBtn" style="min-width: 105px;">
                    <span class="filter-box-label">Status</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_status">{{ request('status') && request('status') != 'all' ? ucfirst(request('status')) : 'All' }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownStatusBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ !request('status') || request('status') == 'all' ? 'active' : '' }}" href="javascript:void(0)" onclick="setRoleFilter('status', 'all', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'active' ? 'active' : '' }}" href="javascript:void(0)" onclick="setRoleFilter('status', 'active', 'Active')">Active</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'inactive' ? 'active' : '' }}" href="javascript:void(0)" onclick="setRoleFilter('status', 'inactive', 'Inactive')">Inactive</a></li>
                </ul>
            </div>

            <!-- 4. Reset & Apply Action Buttons -->
            <div class="d-flex align-items-center gap-2 flex-nowrap ms-auto">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Roles Found Subtext -->
    <div class="devices-found-bar">
        <span>{{ number_format($roles->total()) }} {{ Str::plural('role', $roles->total()) }} found</span>
    </div>

    {{-- 4. Device Installations Table Card for Roles --}}
    <div class="devices-table-card">
        <div class="table-card-top-bar">
            <h2 class="table-title">Administrator Roles</h2>
            <div class="table-header-tools">
                <a href="{{ route('admin.roles.permissions') }}" class="btn-tool text-decoration-none">
                    <i class="fa-solid fa-table-columns"></i>
                    <span>Matrix View</span>
                </a>
                <a href="{{ route('admin.roles.create') }}" class="btn-tool text-decoration-none">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add Role</span>
                </a>
            </div>
        </div>

        <!-- Filter Tabs Row -->
        <div class="devices-tabs-nav">
            @php
                $activeTab = request('tab', 'all');
            @endphp
            <a href="{{ route('admin.roles.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}" class="tab-item {{ $activeTab == 'all' ? 'active' : '' }}">
                All Roles
            </a>
            <a href="{{ route('admin.roles.index', array_merge(request()->except('tab'), ['tab' => 'custom', 'role_type' => 'custom'])) }}" class="tab-item {{ $activeTab == 'custom' ? 'active' : '' }}">
                Custom Roles
            </a>
            <a href="{{ route('admin.roles.index', array_merge(request()->except('tab'), ['tab' => 'system', 'role_type' => 'system'])) }}" class="tab-item {{ $activeTab == 'system' ? 'active' : '' }}">
                System Roles
            </a>
            <a href="{{ route('admin.roles.index', array_merge(request()->except('tab'), ['tab' => 'active', 'status' => 'active'])) }}" class="tab-item {{ $activeTab == 'active' ? 'active' : '' }}">
                Active
            </a>
        </div>

        <!-- Roles Table with 3-Dots Action Button & Normal Typography -->
        <div class="table-responsive">
            <table class="devices-data-table">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Code</th>
                        <th>Type & Scope</th>
                        <th>Permissions</th>
                        <th>Assigned Admins</th>
                        <th>Data Scope</th>
                        <th>Status</th>
                        <th class="th-action text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <!-- 1. Role Name (Clean link, normal weight) -->
                            <td>
                                <a href="{{ route('admin.roles.permissions', ['role_id' => $role->id]) }}" class="installation-id-link">
                                    {{ $role->name }}
                                </a>
                            </td>

                            <!-- 2. Code -->
                            <td>
                                <span class="text-secondary font-monospace" style="font-size: 11.5px;">{{ $role->code }}</span>
                            </td>

                            <!-- 3. Type & Scope -->
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @if($role->role_type === 'system')
                                        <span class="badge bg-dark fw-normal text-white fs-11 px-2">System</span>
                                    @else
                                        <span class="badge bg-info-subtle fw-normal text-info-emphasis fs-11 px-2">Custom</span>
                                    @endif
                                    <span class="text-secondary fs-12 ms-1">
                                        @if($role->scope_type === 'restricted')
                                            <i class="fa-solid fa-lock text-primary me-1 fs-10"></i> Restricted
                                        @else
                                            <i class="fa-solid fa-globe text-success me-1 fs-10"></i> Full Org
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <!-- 4. Permissions -->
                            <td>
                                <span class="badge bg-primary-subtle fw-normal text-primary fs-12 px-2 py-1">
                                    <i class="fa-solid fa-key me-1 fs-10"></i> {{ $role->permissions->count() }} permissions
                                </span>
                            </td>

                            <!-- 5. Assigned Admins -->
                            <td>
                                <span class="text-secondary fs-12">
                                    <i class="fa-solid fa-user-group me-1 fs-11"></i> {{ $role->users->count() }} {{ Str::plural('admin', $role->users->count()) }}
                                </span>
                            </td>

                            <!-- 6. Data Scope -->
                            <td>
                                <span class="text-secondary fs-12">
                                    @if($role->scope_type === 'restricted')
                                        {{ implode(', ', array_slice($role->assigned_regions ?? ['Assigned regions'], 0, 2)) }}
                                        @if(count($role->assigned_regions ?? []) > 2)
                                            <span class="badge bg-light text-muted fw-normal">+{{ count($role->assigned_regions) - 2 }}</span>
                                        @endif
                                    @else
                                        Global Access
                                    @endif
                                </span>
                            </td>

                            <!-- 7. Status -->
                            <td>
                                <span class="badge {{ $role->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} fw-normal fs-11 px-2 py-1">
                                    <i class="fa-solid fa-circle fs-8 me-1 {{ $role->is_active ? 'text-success' : 'text-secondary' }}"></i>
                                    {{ $role->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <!-- 8. Actions (3-Dots Button) -->
                            <td class="td-action text-end pe-3">
                                @if($role->code === 'SUPER_ADMIN')
                                    <span class="badge bg-light text-muted border fw-normal py-1 px-2 fs-11" title="Protected System Role">
                                        <i class="fa-solid fa-lock text-purple me-1 fs-10"></i> Protected
                                    </span>
                                @else
                                    <div class="dropdown d-inline-block">
                                        <button class="btn-action-dots-clean" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-2 py-1 fs-12">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.roles.permissions', ['role_id' => $role->id]) }}">
                                                    <i class="fa-solid fa-table-columns text-primary fs-12" style="width: 16px;"></i> Assign Permissions
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.roles.edit', $role->id) }}">
                                                    <i class="fa-solid fa-pen-to-square text-secondary fs-12" style="width: 16px;"></i> Edit Scope
                                                </a>
                                            </li>
                                            @if($role->role_type !== 'system')
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete role {{ $role->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger">
                                                            <i class="fa-solid fa-trash fs-12" style="width: 16px;"></i> Delete Role
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-shield-slash fs-2 mb-2 d-block text-secondary"></i>
                                No roles found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($roles->hasPages())
            <div class="d-flex align-items-center justify-content-between p-3 border-top bg-white fs-12 text-muted">
                <span>Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} roles</span>
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function setRoleFilter(filterName, value, label) {
        document.getElementById('filter_' + filterName).value = value;
        document.getElementById('label_' + filterName).textContent = label;
        document.getElementById('rolesFilterForm').submit();
    }
</script>
@endpush
@endsection
