@extends('layout')

@section('title', 'Edit Role - GPS Camera Admin')
@section('page_title', 'Edit Role')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Settings</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1 text-muted"></i>
    <a href="{{ route('admin.roles.index') }}" class="text-muted text-decoration-none">Roles & Access</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1 text-muted"></i>
    <span class="active-crumb">Edit Role: {{ $role->name }}</span>
@endsection

@section('content')
<div class="devices-page roles-page roles-page-container">
    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" id="editRoleForm" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="role_type" id="inputRoleType" value="{{ $role->role_type }}">
        <input type="hidden" name="scope_type" id="inputScopeType" value="{{ $role->scope_type }}">
        <input type="hidden" name="is_active" id="inputIsActive" value="{{ $role->is_active ? '1' : '0' }}">

        {{-- 1. Top Header Row (Standardized 18px Sizing & Actions Alignment) --}}
        <div class="page-header-row mb-3">
            <div class="header-title-group">
                <h1 class="page-main-title">Edit Role</h1>
                <p class="page-main-subtitle">Update administrator role properties and data access boundaries</p>
            </div>
            <div class="header-actions-group">
                <a href="{{ route('admin.roles.permissions', ['role_id' => $role->id]) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-table-cells"></i>
                    <span>Configure Matrix Permissions</span>
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Roles</span>
                </a>
            </div>
        </div>

        {{-- 2. Two-Column Layout --}}
        <div class="row g-3">
            {{-- Left Main Column --}}
            <div class="col-lg-8">

                {{-- CARD 1: Role Information --}}
                <div class="role-card">
                    <div class="card-header-label">Role Information</div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1" for="inputRoleName">
                                Role Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="inputRoleName" class="form-control" 
                                   value="{{ old('name', $role->name) }}" 
                                   minlength="3" maxlength="25"
                                   required oninput="updateLiveSummary()" {{ $role->code === 'SUPER_ADMIN' ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1" for="inputRoleCode">
                                Role Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="code" id="inputRoleCode" class="form-control font-monospace" 
                                   value="{{ old('code', $role->code) }}" 
                                   required oninput="updateLiveSummary()" {{ $role->code === 'SUPER_ADMIN' ? 'readonly' : '' }}>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Role Type</label>
                            <div class="role-type-selector">
                                <button type="button" class="role-type-btn {{ $role->role_type === 'custom' ? 'active' : '' }}" id="btnTypeCustom" onclick="setRoleType('custom')">
                                    <i class="fa-solid fa-user-shield"></i>
                                    <span>Custom Role</span>
                                </button>
                                <button type="button" class="role-type-btn {{ $role->role_type === 'system' ? 'active' : '' }}" id="btnTypeSystem" onclick="setRoleType('system')">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    <span>System Role</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Description</label>
                            <textarea name="description" id="inputDescription" rows="2" class="form-control fs-13">{{ old('description', $role->description) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="toggleRoleStatus" {{ $role->is_active ? 'checked' : '' }} onchange="toggleStatus(this)">
                                <label class="form-check-label fs-13 fw-semibold text-dark ms-2" for="toggleRoleStatus">Role Status</label>
                            </div>
                            <span class="badge {{ $role->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} fs-12 px-2" id="statusBadgeLabel">
                                {{ $role->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Access Scope --}}
                <div class="role-card">
                    <div class="card-header-label">Access Scope</div>
                    
                    <div class="scope-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="scope_radio" id="scopeRestricted" value="restricted" {{ $role->scope_type === 'restricted' ? 'checked' : '' }} onchange="setScopeType('restricted')">
                            <label class="form-check-label" for="scopeRestricted">
                                <i class="fa-solid fa-lock text-primary me-1"></i> Restricted Scope
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="scope_radio" id="scopeFullOrg" value="full_org" {{ $role->scope_type === 'full_org' ? 'checked' : '' }} onchange="setScopeType('full_org')">
                            <label class="form-check-label" for="scopeFullOrg">
                                <i class="fa-solid fa-globe text-success me-1"></i> Full Organization
                            </label>
                        </div>
                    </div>

                    <div id="restrictedScopeFields" style="{{ $role->scope_type === 'restricted' ? '' : 'display: none;' }}">
                        <div class="row g-3 mb-3">
                            {{-- Region Access Multiselect --}}
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Region Access</label>
                                <div class="custom-multiselect dropdown" id="regionMultiselect">
                                    <div class="multiselect-trigger form-control" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <div class="multiselect-tags-container" id="regionTagsContainer">
                                            @php $currentRegions = $role->assigned_regions ?? []; @endphp
                                            @forelse($currentRegions as $reg)
                                                <span class="tag-badge">
                                                    <span>{{ $reg }}</span>
                                                    <button type="button" class="tag-remove-btn" onclick="removeMultiSelectTag('regionMultiselect', '{{ $reg }}', event)">&times;</button>
                                                    <input type="hidden" name="assigned_regions[]" value="{{ $reg }}">
                                                </span>
                                            @empty
                                                <span class="placeholder-text text-muted fs-13">Select regions...</span>
                                            @endforelse
                                        </div>
                                        <i class="fa-solid fa-chevron-down multiselect-chevron"></i>
                                    </div>
                                    <ul class="dropdown-menu w-100 p-2 shadow-sm border-light-subtle" style="max-height: 220px; overflow-y: auto;">
                                        @foreach($availableRegions as $reg)
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 cursor-pointer fs-13">
                                                    <input type="checkbox" class="form-check-input mt-0 region-checkbox" value="{{ $reg }}" {{ in_array($reg, $currentRegions) ? 'checked' : '' }} onchange="syncRegionTags()">
                                                    <span>{{ $reg }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            {{-- Department Access Multiselect --}}
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Department Access</label>
                                <div class="custom-multiselect dropdown" id="departmentMultiselect">
                                    <div class="multiselect-trigger form-control" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <div class="multiselect-tags-container" id="departmentTagsContainer">
                                            @php $currentDepts = $role->assigned_departments ?? []; @endphp
                                            @forelse($currentDepts as $dept)
                                                <span class="tag-badge">
                                                    <span>{{ $dept }}</span>
                                                    <button type="button" class="tag-remove-btn" onclick="removeMultiSelectTag('departmentMultiselect', '{{ $dept }}', event)">&times;</button>
                                                    <input type="hidden" name="assigned_departments[]" value="{{ $dept }}">
                                                </span>
                                            @empty
                                                <span class="placeholder-text text-muted fs-13">Select departments...</span>
                                            @endforelse
                                        </div>
                                        <i class="fa-solid fa-chevron-down multiselect-chevron"></i>
                                    </div>
                                    <ul class="dropdown-menu w-100 p-2 shadow-sm border-light-subtle" style="max-height: 220px; overflow-y: auto;">
                                        @foreach($availableDepartments as $dept)
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 cursor-pointer fs-13">
                                                    <input type="checkbox" class="form-check-input mt-0 dept-checkbox" value="{{ $dept }}" {{ in_array($dept, $currentDepts) ? 'checked' : '' }} onchange="syncDeptTags()">
                                                    <span>{{ $dept }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 align-items-center mt-1">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Data Visibility</label>
                                <select name="data_visibility" id="selectDataVisibility" class="form-select" onchange="updateLiveSummary()">
                                    <option value="assigned_regions_only" {{ $role->data_visibility === 'assigned_regions_only' ? 'selected' : '' }}>Assigned regions only</option>
                                    <option value="all" {{ $role->data_visibility === 'all' ? 'selected' : '' }}>All regions</option>
                                    <option value="country_only" {{ $role->data_visibility === 'country_only' ? 'selected' : '' }}>Country boundary only</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch pt-3">
                                    <input class="form-check-input cursor-pointer" type="checkbox" name="restrict_exports" id="toggleRestrictExports" value="1" {{ $role->restrict_exports ? 'checked' : '' }} onchange="updateLiveSummary()">
                                    <label class="form-check-label fs-13 fw-semibold text-dark ms-2" for="toggleRestrictExports">
                                        Restrict exports to assigned scope
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 3: Role Assignment --}}
                <div class="role-card">
                    <div class="card-header-label">Role Assignment</div>
                    
                    <div class="row g-3 align-items-start">
                        <div class="col-md-7">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Assigned Administrators</label>
                            @php
                                $assignedAdminIds = $role->users->pluck('id')->toArray();
                            @endphp
                            <div class="custom-multiselect dropdown" id="adminMultiselect">
                                <div class="multiselect-trigger form-control" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <div class="multiselect-tags-container" id="adminTagsContainer">
                                        @forelse($role->users as $u)
                                            <span class="tag-badge">
                                                <i class="fa-regular fa-user text-primary me-1"></i>
                                                <span>{{ $u->name }}</span>
                                                <button type="button" class="tag-remove-btn" onclick="removeMultiSelectTag('adminMultiselect', '{{ $u->id }}', event)">&times;</button>
                                                <input type="hidden" name="assigned_admin_ids[]" value="{{ $u->id }}">
                                            </span>
                                        @empty
                                            <span class="placeholder-text text-muted fs-13">Select administrators...</span>
                                        @endforelse
                                    </div>
                                    <i class="fa-solid fa-chevron-down multiselect-chevron"></i>
                                </div>
                                <ul class="dropdown-menu w-100 p-2 shadow-sm border-light-subtle" style="max-height: 220px; overflow-y: auto;">
                                    @foreach($adminUsers as $admin)
                                        @if($admin->id !== 1 && $admin->role !== 'Super Admin')
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 cursor-pointer">
                                                    <input type="checkbox" class="form-check-input mt-0 flex-shrink-0 admin-checkbox" value="{{ $admin->id }}" data-name="{{ $admin->name }}" data-email="{{ $admin->email }}" {{ in_array($admin->id, $assignedAdminIds) ? 'checked' : '' }} onchange="syncAdminTags()">
                                                    <div class="d-flex flex-column min-w-0 text-truncate">
                                                        <span class="fs-12 fw-semibold text-dark text-truncate">{{ $admin->name }}</span>
                                                        <span class="fs-11 text-muted text-truncate">{{ $admin->email }}</span>
                                                    </div>
                                                </label>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex flex-column gap-2 pt-md-4">
                                <span class="badge {{ $role->users->count() > 0 ? 'bg-primary-subtle text-primary' : 'bg-light text-secondary' }} border fs-12 py-2 px-3 align-self-start" id="adminSelectedCountBadge">
                                    <i class="fa-solid fa-users me-1"></i> {{ $role->users->count() }} administrator(s) assigned
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Sticky Sidebar Column --}}
            <div class="col-lg-4">
                <div class="role-summary-card">
                    <div class="summary-header-badge">
                        <div class="role-avatar-shield">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="role-meta-titles">
                            <span class="role-name-display" id="sideRoleNameDisplay">{{ $role->name }}</span>
                            <span class="role-code-display" id="sideRoleCodeDisplay">{{ $role->code }}</span>
                            <span class="status-dot-active" id="sideStatusDot">{{ $role->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>

                    <table class="summary-meta-table">
                        <tbody>
                            <tr>
                                <td>Type</td>
                                <td id="sideMetaType">{{ ucfirst($role->role_type) }} Role</td>
                            </tr>
                            <tr>
                                <td>Scope</td>
                                <td id="sideMetaScope">{{ $role->scope_type === 'restricted' ? 'Restricted' : 'Full Org' }}</td>
                            </tr>
                            <tr>
                                <td>Regions</td>
                                <td id="sideMetaRegions">{{ count($role->assigned_regions ?? []) }}</td>
                            </tr>
                            <tr>
                                <td>Departments</td>
                                <td id="sideMetaDepts">{{ count($role->assigned_departments ?? []) }}</td>
                            </tr>
                            <tr>
                                <td>Permissions</td>
                                <td>{{ $role->permissions->count() }}</td>
                            </tr>
                            <tr>
                                <td>Administrators</td>
                                <td id="sideMetaAdmins">{{ $role->users->count() }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="role-alert-box valid">
                        <i class="fa-solid fa-circle-check fs-14 mt-1"></i>
                        <span>Role configuration is active & valid</span>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.roles.permissions', ['role_id' => $role->id]) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fa-solid fa-table-cells me-1"></i> Configure Matrix Permissions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Fixed Bottom Action Bar --}}
        <div class="role-sticky-footer">
            <div>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary px-4">
                    Cancel
                </a>
            </div>
            <div class="actions-btns-group">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function setRoleType(type) {
        document.getElementById('inputRoleType').value = type;
        const btnCustom = document.getElementById('btnTypeCustom');
        const btnSystem = document.getElementById('btnTypeSystem');
        
        if (type === 'custom') {
            btnCustom.classList.add('active');
            btnSystem.classList.remove('active');
            document.getElementById('sideMetaType').textContent = 'Custom Role';
        } else {
            btnSystem.classList.add('active');
            btnCustom.classList.remove('active');
            document.getElementById('sideMetaType').textContent = 'System Role';
        }
        updateLiveSummary();
    }

    function setScopeType(scope) {
        document.getElementById('inputScopeType').value = scope;
        const fields = document.getElementById('restrictedScopeFields');
        if (scope === 'restricted') {
            fields.style.display = 'block';
            document.getElementById('sideMetaScope').textContent = 'Restricted';
        } else {
            fields.style.display = 'none';
            document.getElementById('sideMetaScope').textContent = 'Full Org';
        }
        updateLiveSummary();
    }

    function toggleStatus(checkbox) {
        const isActive = checkbox.checked;
        document.getElementById('inputIsActive').value = isActive ? '1' : '0';
        const badge = document.getElementById('statusBadgeLabel');
        const sideDot = document.getElementById('sideStatusDot');

        if (isActive) {
            badge.textContent = 'Active';
            badge.className = 'badge bg-success-subtle text-success fs-12 px-2';
            sideDot.textContent = 'Active';
            sideDot.className = 'status-dot-active';
        } else {
            badge.textContent = 'Inactive';
            badge.className = 'badge bg-secondary-subtle text-secondary fs-12 px-2';
            sideDot.textContent = 'Inactive';
            sideDot.className = 'text-secondary fs-12 fw-semibold';
        }
    }

    function syncRegionTags() {
        const container = document.getElementById('regionTagsContainer');
        const checkedBoxes = document.querySelectorAll('#regionMultiselect .region-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            container.innerHTML = `<span class="placeholder-text text-muted fs-13">Select regions...</span>`;
        } else {
            let html = '';
            checkedBoxes.forEach(cb => {
                const val = cb.value;
                html += `<span class="tag-badge"><span>${val}</span><button type="button" class="tag-remove-btn" onclick="removeMultiSelectTag('regionMultiselect', '${val}', event)">&times;</button><input type="hidden" name="assigned_regions[]" value="${val}"></span>`;
            });
            container.innerHTML = html;
        }
        updateLiveSummary();
    }

    function syncDeptTags() {
        const container = document.getElementById('departmentTagsContainer');
        const checkedBoxes = document.querySelectorAll('#departmentMultiselect .dept-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            container.innerHTML = `<span class="placeholder-text text-muted fs-13">Select departments...</span>`;
        } else {
            let html = '';
            checkedBoxes.forEach(cb => {
                const val = cb.value;
                html += `<span class="tag-badge"><span>${val}</span><button type="button" class="tag-remove-btn" onclick="removeMultiSelectTag('departmentMultiselect', '${val}', event)">&times;</button><input type="hidden" name="assigned_departments[]" value="${val}"></span>`;
            });
            container.innerHTML = html;
        }
        updateLiveSummary();
    }

    function syncAdminTags() {
        const container = document.getElementById('adminTagsContainer');
        const checkedBoxes = document.querySelectorAll('#adminMultiselect .admin-checkbox:checked');
        const badge = document.getElementById('adminSelectedCountBadge');
        
        if (checkedBoxes.length === 0) {
            container.innerHTML = `<span class="placeholder-text text-muted fs-13">Select administrators...</span>`;
            badge.innerHTML = `<i class="fa-solid fa-users me-1"></i> No administrators assigned`;
            badge.className = 'badge bg-light text-secondary border fs-12 py-2 px-3 align-self-start';
            document.getElementById('sideMetaAdmins').textContent = 0;
        } else {
            let html = '';
            checkedBoxes.forEach(cb => {
                const val = cb.value;
                const name = cb.getAttribute('data-name');
                html += `<span class="tag-badge"><i class="fa-regular fa-user text-primary me-1"></i><span>${name}</span><button type="button" class="tag-remove-btn" onclick="removeMultiSelectTag('adminMultiselect', '${val}', event)">&times;</button><input type="hidden" name="assigned_admin_ids[]" value="${val}"></span>`;
            });
            container.innerHTML = html;
            badge.innerHTML = `<i class="fa-solid fa-users text-primary me-1"></i> ${checkedBoxes.length} administrator(s) assigned`;
            badge.className = 'badge bg-primary-subtle text-primary border fs-12 py-2 px-3 align-self-start';
            document.getElementById('sideMetaAdmins').textContent = checkedBoxes.length;
        }
    }

    function removeMultiSelectTag(multiselectId, val, event) {
        if (event) {
            event.stopPropagation();
        }
        const multiselect = document.getElementById(multiselectId);
        if (!multiselect) return;
        
        const checkbox = multiselect.querySelector(`input[type="checkbox"][value="${val}"]`);
        if (checkbox) {
            checkbox.checked = false;
            if (multiselectId === 'regionMultiselect') syncRegionTags();
            else if (multiselectId === 'departmentMultiselect') syncDeptTags();
            else if (multiselectId === 'adminMultiselect') syncAdminTags();
        }
    }

    function updateLiveSummary() {
        const roleName = document.getElementById('inputRoleName').value || 'Role';
        const roleCode = document.getElementById('inputRoleCode').value || 'ROLE';
        
        document.getElementById('sideRoleNameDisplay').textContent = roleName;
        document.getElementById('sideRoleCodeDisplay').textContent = roleCode;

        const regCount = document.querySelectorAll('#regionMultiselect .region-checkbox:checked').length;
        const deptCount = document.querySelectorAll('#departmentMultiselect .dept-checkbox:checked').length;
        
        document.getElementById('sideMetaRegions').textContent = regCount;
        document.getElementById('sideMetaDepts').textContent = deptCount;
    }
</script>
@endpush
@endsection
