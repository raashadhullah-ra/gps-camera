@extends('layout')

@section('title', 'Add Role - GPS Camera Admin')
@section('page_title', 'Add Role')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Settings</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1 text-muted"></i>
    <a href="{{ route('admin.roles.index') }}" class="text-muted text-decoration-none">Roles & Access</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1 text-muted"></i>
    <span class="active-crumb">Add Role</span>
@endsection

@section('content')
<div class="devices-page roles-page roles-page-container">
    <form action="{{ route('admin.roles.store') }}" method="POST" id="addRoleForm" novalidate>
        @csrf
        <input type="hidden" name="role_type" id="inputRoleType" value="custom">
        <input type="hidden" name="scope_type" id="inputScopeType" value="restricted">
        <input type="hidden" name="is_active" id="inputIsActive" value="1">
        <input type="hidden" name="action" id="formSubmitAction" value="create">

        {{-- 1. Top Header Row (Standardized 18px Sizing & Actions Alignment) --}}
        <div class="page-header-row mb-3">
            <div class="header-title-group">
                <h1 class="page-main-title">Add Role</h1>
                <p class="page-main-subtitle">Create a reusable administrator role and define its access scope</p>
            </div>
            <div class="header-actions-group">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-list-check"></i>
                    <span>View Existing Roles</span>
                </a>
            </div>
        </div>

        {{-- 2. Two-Column Layout --}}
        <div class="row g-3">
            {{-- Left Main Column (Cards 1 to 4) --}}
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
                                   placeholder="e.g. Operations Manager" 
                                   value="{{ old('name') }}" 
                                   minlength="3" maxlength="25"
                                   required oninput="handleRoleNameInput(this)">
                            <div class="invalid-feedback fs-12" id="roleNameFeedback">Please provide a valid unique role name.</div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fs-13 fw-semibold text-dark mb-0" for="inputRoleCode">
                                    Role Code <span class="text-danger">*</span>
                                </label>
                                <span class="badge fs-11 px-2 py-0" id="codeAvailableBadge" style="display: none;">
                                </span>
                            </div>
                            <input type="text" name="code" id="inputRoleCode" class="form-control font-monospace" 
                                   placeholder="e.g. OPERATIONS_MANAGER" 
                                   value="{{ old('code') }}" 
                                   required oninput="handleManualCodeInput(this)">
                            <div class="invalid-feedback fs-12" id="roleCodeFeedback">This role code is already taken.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Role Type <span class="text-danger">*</span></label>
                            <div class="role-type-selector">
                                <button type="button" class="role-type-btn active" id="btnTypeCustom" onclick="setRoleType('custom')">
                                    <i class="fa-solid fa-user-shield"></i>
                                    <span>Custom Role</span>
                                </button>
                                <button type="button" class="role-type-btn" id="btnTypeSystem" onclick="setRoleType('system')">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    <span>System Role</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Description</label>
                            <textarea name="description" id="inputDescription" rows="2" class="form-control fs-13" 
                                      placeholder="Brief description of role responsibilities and scope..." 
                                      oninput="updateLiveSummary()">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="toggleRoleStatus" checked onchange="toggleStatus(this)">
                                <label class="form-check-label fs-13 fw-semibold text-dark ms-2" for="toggleRoleStatus">Role Status</label>
                            </div>
                            <span class="badge bg-success-subtle text-success fs-12 px-2" id="statusBadgeLabel">Active</span>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Access Scope --}}
                <div class="role-card">
                    <div class="card-header-label">Access Scope</div>
                    
                    <div class="scope-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="scope_radio" id="scopeRestricted" value="restricted" checked onchange="setScopeType('restricted')">
                            <label class="form-check-label" for="scopeRestricted">
                                <i class="fa-solid fa-lock text-primary me-1"></i> Restricted Scope
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="scope_radio" id="scopeFullOrg" value="full_org" onchange="setScopeType('full_org')">
                            <label class="form-check-label" for="scopeFullOrg">
                                <i class="fa-solid fa-globe text-success me-1"></i> Full Organization
                            </label>
                        </div>
                    </div>

                    <div id="restrictedScopeFields">
                        <div class="row g-3 mb-3">
                            {{-- Region Access Multiselect --}}
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Region Access</label>
                                <div class="custom-multiselect dropdown" id="regionMultiselect">
                                    <div class="multiselect-trigger form-control" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <div class="multiselect-tags-container" id="regionTagsContainer">
                                            <span class="placeholder-text text-muted fs-13">Select regions...</span>
                                        </div>
                                        <i class="fa-solid fa-chevron-down multiselect-chevron"></i>
                                    </div>
                                    <ul class="dropdown-menu w-100 p-2 shadow-sm border-light-subtle" style="max-height: 220px; overflow-y: auto;">
                                        @foreach($availableRegions as $reg)
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 cursor-pointer fs-13">
                                                    <input type="checkbox" class="form-check-input mt-0 region-checkbox" value="{{ $reg }}" onchange="syncRegionTags()">
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
                                            <span class="placeholder-text text-muted fs-13">Select departments...</span>
                                        </div>
                                        <i class="fa-solid fa-chevron-down multiselect-chevron"></i>
                                    </div>
                                    <ul class="dropdown-menu w-100 p-2 shadow-sm border-light-subtle" style="max-height: 220px; overflow-y: auto;">
                                        @foreach($availableDepartments as $dept)
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 cursor-pointer fs-13">
                                                    <input type="checkbox" class="form-check-input mt-0 dept-checkbox" value="{{ $dept }}" onchange="syncDeptTags()">
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
                                    <option value="assigned_regions_only" selected>Assigned regions only</option>
                                    <option value="all">All regions</option>
                                    <option value="country_only">Country boundary only</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch pt-3">
                                    <input class="form-check-input cursor-pointer" type="checkbox" name="restrict_exports" id="toggleRestrictExports" value="1" checked onchange="updateLiveSummary()">
                                    <label class="form-check-label fs-13 fw-semibold text-dark ms-2" for="toggleRestrictExports">
                                        Restrict exports to assigned scope
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 3: Permission Template --}}
                <div class="role-card">
                    <div class="card-header-label">
                        <span>Permission Template</span>
                        <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="submitForPermissionMatrix()">
                            <i class="fa-solid fa-table-cells me-1"></i> Configure Permissions
                        </button>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Start From Template (Optional)</label>
                            <select name="template_role_id" id="selectTemplateRole" class="form-select" onchange="handleTemplateChange(this)">
                                <option value="" selected>Custom (Blank - 0 permissions)</option>
                                @foreach($existingRoles as $r)
                                    <option value="{{ $r->id }}" data-perm-count="{{ $r->permissions->count() }}">
                                        {{ $r->name }} ({{ $r->permissions->count() }} permissions)
                                    </option>
                                @endforeach
                            </select>
                            <div class="fs-12 text-muted mt-1">
                                Choose an existing role to duplicate its permission set or start with a blank role.
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="alert alert-info py-2 px-3 mb-0 w-100 fs-12 d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-emphasis">
                                <i class="fa-solid fa-circle-info"></i>
                                <span id="templateNoticeText">Starting with a blank template (0 permissions). Permissions can be configured immediately after creation.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 4: Role Assignment --}}
                <div class="role-card">
                    <div class="card-header-label">Role Assignment</div>
                    
                    <div class="row g-3 align-items-start">
                        <div class="col-md-7">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Assign Administrators (Optional)</label>
                            <div class="custom-multiselect dropdown" id="adminMultiselect">
                                <div class="multiselect-trigger form-control" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <div class="multiselect-tags-container" id="adminTagsContainer">
                                        <span class="placeholder-text text-muted fs-13">Select administrators...</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down multiselect-chevron"></i>
                                </div>
                                <ul class="dropdown-menu w-100 p-2 shadow-sm border-light-subtle" style="max-height: 220px; overflow-y: auto;">
                                    @foreach($adminUsers as $admin)
                                        @if($admin->id !== 1 && $admin->role !== 'Super Admin')
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 cursor-pointer">
                                                    <input type="checkbox" class="form-check-input mt-0 flex-shrink-0 admin-checkbox" value="{{ $admin->id }}" data-name="{{ $admin->name }}" data-email="{{ $admin->email }}" onchange="syncAdminTags()">
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
                                <span class="badge bg-light text-secondary border fs-12 py-2 px-3 align-self-start" id="adminSelectedCountBadge">
                                    <i class="fa-solid fa-users me-1"></i> No administrators assigned
                                </span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input cursor-pointer" type="checkbox" id="toggleFutureAssignment" checked>
                                    <label class="form-check-label fs-12 fw-medium text-muted ms-2" for="toggleFutureAssignment">
                                        Allow future assignment
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Sticky Sidebar Column (Role Summary & Access Preview) --}}
            <div class="col-lg-4">

                {{-- Role Summary Card --}}
                <div class="role-summary-card">
                    <div class="summary-header-badge">
                        <div class="role-avatar-shield">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="role-meta-titles">
                            <span class="role-name-display" id="sideRoleNameDisplay">New Role</span>
                            <span class="role-code-display" id="sideRoleCodeDisplay">ROLE_CODE</span>
                            <span class="status-dot-active" id="sideStatusDot">Active</span>
                        </div>
                    </div>

                    <table class="summary-meta-table">
                        <tbody>
                            <tr>
                                <td>Type</td>
                                <td id="sideMetaType">Custom Role</td>
                            </tr>
                            <tr>
                                <td>Scope</td>
                                <td id="sideMetaScope">Restricted</td>
                            </tr>
                            <tr>
                                <td>Regions</td>
                                <td id="sideMetaRegions">0</td>
                            </tr>
                            <tr>
                                <td>Departments</td>
                                <td id="sideMetaDepts">0</td>
                            </tr>
                            <tr>
                                <td>Permissions</td>
                                <td id="sideMetaPerms">0</td>
                            </tr>
                            <tr>
                                <td>Administrators</td>
                                <td id="sideMetaAdmins">0</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Dynamic Status Alerts --}}
                    <div class="role-alert-box valid">
                        <i class="fa-solid fa-circle-check fs-14 mt-1"></i>
                        <span>Role configuration is ready</span>
                    </div>

                    <div class="role-alert-box warning">
                        <i class="fa-solid fa-circle-exclamation fs-14 mt-1"></i>
                        <span>Creating this role will not affect existing admins until it is assigned.</span>
                    </div>
                </div>

                {{-- Access Preview Card --}}
                <div class="role-summary-card">
                    <div class="card-header-label mb-2">Access Preview</div>
                    
                    <ul class="access-preview-list" id="accessPreviewList">
                        <li id="accessPreviewNotice">
                            <i class="fa-solid fa-circle-info text-muted"></i>
                            <span class="text-muted">Start with 0 permissions or select a template to copy initial permissions.</span>
                        </li>
                    </ul>
                </div>

                {{-- Bottom Audit Info Box --}}
                <div class="p-3 bg-white rounded-3 border text-secondary fs-12 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-shield-heart text-primary fs-14"></i>
                    <span>Role creation and future changes are recorded in the audit log.</span>
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
                <button type="submit" name="save_draft" value="1" class="btn btn-outline-secondary px-4">
                    Save as Draft
                </button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-plus me-1"></i> Create Role
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const existingRoleNames = @json($existingRoles->pluck('name')->map(fn($n) => strtolower(trim($n))));
    const existingRoleCodes = @json($existingRoles->pluck('code')->map(fn($c) => strtoupper(trim($c))));

    function validateRoleUniqueness() {
        const nameInput = document.getElementById('inputRoleName');
        const codeInput = document.getElementById('inputRoleCode');
        const badge = document.getElementById('codeAvailableBadge');
        const nameFeedback = document.getElementById('roleNameFeedback');
        const codeFeedback = document.getElementById('roleCodeFeedback');

        const nameVal = nameInput.value.trim().toLowerCase();
        const codeVal = codeInput.value.trim().toUpperCase();

        let isNameValid = true;
        let isCodeValid = true;

        if (nameVal) {
            if (nameVal.length < 3) {
                nameInput.classList.add('is-invalid');
                nameInput.classList.remove('is-valid');
                nameFeedback.textContent = 'Role name must be at least 3 characters.';
                isNameValid = false;
            } else if (nameVal.length > 25) {
                nameInput.classList.add('is-invalid');
                nameInput.classList.remove('is-valid');
                nameFeedback.textContent = 'Role name cannot exceed 25 characters.';
                isNameValid = false;
            } else if (existingRoleNames.includes(nameVal)) {
                nameInput.classList.add('is-invalid');
                nameInput.classList.remove('is-valid');
                nameFeedback.textContent = `A role named "${nameInput.value.trim()}" already exists.`;
                isNameValid = false;
            } else {
                nameInput.classList.remove('is-invalid');
                nameInput.classList.add('is-valid');
            }
        } else {
            nameInput.classList.remove('is-invalid', 'is-valid');
            isNameValid = false;
        }

        if (codeVal) {
            if (codeVal.length < 3) {
                codeInput.classList.add('is-invalid');
                codeInput.classList.remove('is-valid');
                codeFeedback.textContent = 'Role code must be at least 3 characters.';
                isCodeValid = false;
            } else if (existingRoleCodes.includes(codeVal)) {
                codeInput.classList.add('is-invalid');
                codeInput.classList.remove('is-valid');
                codeFeedback.textContent = `Role code "${codeVal}" is already in use.`;
                isCodeValid = false;
            } else {
                codeInput.classList.remove('is-invalid');
                codeInput.classList.add('is-valid');
            }
        } else {
            codeInput.classList.remove('is-invalid', 'is-valid');
            isCodeValid = false;
        }

        if (codeVal && nameVal) {
            badge.style.display = 'inline-block';
            if (!isCodeValid || !isNameValid) {
                badge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle fs-11 px-2 py-0';
                badge.innerHTML = '<i class="fa-solid fa-xmark me-1"></i>Invalid / Taken';
            } else {
                badge.className = 'badge bg-success-subtle text-success border border-success-subtle fs-11 px-2 py-0';
                badge.innerHTML = '<i class="fa-solid fa-check me-1"></i>Available';
            }
        } else {
            badge.style.display = 'none';
        }

        return isNameValid && isCodeValid;
    }

    function handleRoleNameInput(input) {
        const nameVal = input.value.trim();
        const codeInput = document.getElementById('inputRoleCode');
        if (nameVal) {
            const autoCode = nameVal.toUpperCase().replace(/[^A-Z0-9]+/g, '_');
            codeInput.value = autoCode;
        } else {
            codeInput.value = '';
        }
        validateRoleUniqueness();
        updateLiveSummary();
    }

    function handleManualCodeInput(input) {
        input.value = input.value.toUpperCase().replace(/[^A-Z0-9_]+/g, '');
        validateRoleUniqueness();
        updateLiveSummary();
    }

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

    function handleTemplateChange(select) {
        const selectedOpt = select.options[select.selectedIndex];
        const permCount = selectedOpt.getAttribute('data-perm-count') || 0;
        const text = selectedOpt.text;
        
        const notice = document.getElementById('templateNoticeText');
        if (select.value) {
            notice.textContent = `${permCount} permissions copied from ${text.split('(')[0].trim()}`;
            document.getElementById('sideMetaPerms').textContent = permCount;
        } else {
            notice.textContent = "Starting with a blank template (0 permissions). Permissions can be configured immediately after creation.";
            document.getElementById('sideMetaPerms').textContent = 0;
        }
    }

    function submitForPermissionMatrix() {
        document.getElementById('formSubmitAction').value = 'configure_permissions';
        document.getElementById('addRoleForm').submit();
    }

    function updateLiveSummary() {
        const roleName = document.getElementById('inputRoleName').value || 'New Role';
        const roleCode = document.getElementById('inputRoleCode').value || 'NEW_ROLE';
        
        document.getElementById('sideRoleNameDisplay').textContent = roleName;
        document.getElementById('sideRoleCodeDisplay').textContent = roleCode;

        const regCount = document.querySelectorAll('#regionMultiselect .region-checkbox:checked').length;
        const deptCount = document.querySelectorAll('#departmentMultiselect .dept-checkbox:checked').length;
        
        document.getElementById('sideMetaRegions').textContent = regCount;
        document.getElementById('sideMetaDepts').textContent = deptCount;
    }

    document.getElementById('addRoleForm').addEventListener('submit', function(e) {
        const isValid = validateRoleUniqueness();
        if (!this.checkValidity() || !isValid) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('was-validated');
            if (!isValid) {
                if (window.showToast) {
                    window.showToast('error', 'Duplicate Role', 'A role with this name or code already exists in the system.');
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        updateLiveSummary();
        if (document.getElementById('inputRoleName').value) {
            validateRoleUniqueness();
        }
    });
</script>
@endpush
@endsection
