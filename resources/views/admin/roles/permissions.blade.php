@extends('layout')

@section('title', 'Assign Permissions - GPS Camera Admin')
@section('page_title', 'Assign Permissions')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Settings</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1 text-muted"></i>
    <a href="{{ route('admin.roles.index') }}" class="text-muted text-decoration-none">Roles & Access</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1 text-muted"></i>
    <span class="active-crumb">Assign Permissions</span>
@endsection

@section('content')
<div class="devices-page roles-page">
    <form action="{{ route('admin.roles.permissions.update', $selectedRole->id ?? 1) }}" method="POST" id="permissionsMatrixForm">
        @csrf

        {{-- 1. Top Header Row (Exact Match to Devices Heading Sizing & Style) --}}
        <div class="page-header-row mb-3">
            <div class="header-title-group">
                <h1 class="page-main-title">Assign Permissions</h1>
                <p class="page-main-subtitle">Configure module-level access for administrator roles</p>
            </div>
            <div class="header-actions-group">
                <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#templateModal">
                    <i class="fa-solid fa-shapes"></i>
                    <span>Permission Templates</span>
                </button>
                <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#copyRoleModal">
                    <i class="fa-solid fa-copy"></i>
                    <span>Copy From Role</span>
                </button>
            </div>
        </div>

        {{-- 2. Role Selector & Counters Header (Clean and Single Line, Marked Badges Removed) --}}
        <div class="permission-role-selector-card">
            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                {{-- Left Side: Select Role + Compact Dropdown + Clean Badges --}}
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="fs-11 fw-bold text-muted text-uppercase text-nowrap me-1">Select Role <span class="text-danger">*</span></span>

                    <select class="form-select form-select-sm role-select-box" id="roleSelectorDropdown" onchange="changeRole(this)" style="width: 170px; max-width: 175px; font-weight: 600;">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ $selectedRole && $selectedRole->id === $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <span class="badge {{ ($selectedRole->is_active ?? true) ? 'bg-success-subtle text-success border-success-subtle' : 'bg-secondary-subtle text-secondary' }} border fs-12 px-2 py-1">
                        <i class="fa-solid fa-circle fs-8 me-1 {{ ($selectedRole->is_active ?? true) ? 'text-success' : 'text-secondary' }}"></i>
                        {{ ($selectedRole->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>

                    <span class="badge-scope-pill">
                        <i class="fa-solid fa-shield-halved"></i>
                        {{ ($selectedRole->scope_type ?? 'full_org') === 'restricted' ? 'Restricted Scope' : 'Full Organization' }}
                    </span>

                    @if(!empty($selectedRole->assigned_regions))
                        <span class="badge-meta-pill">
                            <i class="fa-solid fa-location-dot text-primary"></i>
                            {{ count($selectedRole->assigned_regions) }} Regions
                        </span>
                    @endif
                </div>

                {{-- Right Side: The 3 Counter Boxes (In the same line) --}}
                <div class="role-counters-row ms-auto d-flex align-items-center gap-2">
                    <div class="pill-stat-box blue py-1 px-3">
                        <span class="num" id="statSelectedPerms">{{ count($assignedPermissionIds) }}</span>
                        <span class="txt">Selected Permissions</span>
                    </div>
                    <div class="pill-stat-box green py-1 px-3">
                        <span class="num" id="statEnabledModules">{{ $selectedRole ? $selectedRole->permissions->pluck('module')->unique()->count() : 0 }}</span>
                        <span class="txt">Enabled Modules</span>
                    </div>
                    <div class="pill-stat-box amber py-1 px-3">
                        <span class="num" id="statUnsavedChanges">0</span>
                        <span class="txt">Unsaved Changes</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Two-Column Matrix + Summary Layout --}}
        <div class="row g-3">
            {{-- Left Column: Permission Matrix Table --}}
            <div class="col-lg-8">
                <div class="matrix-card-container">
                    
                    {{-- Matrix Toolbar --}}
                    <div class="matrix-toolbar">
                        <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 320px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="matrixSearchInput" placeholder="Search module or permission..." oninput="filterMatrixRows()">
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <select class="form-select form-select-sm" id="matrixGroupFilter" style="width: 170px;" onchange="filterMatrixRows()">
                                <option value="all">All Module Groups</option>
                                <option value="Overview">Overview</option>
                                <option value="Installations">Installations</option>
                                <option value="Engagement">Engagement</option>
                                <option value="App Control">App Control</option>
                                <option value="System">System</option>
                            </select>

                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input cursor-pointer" type="checkbox" id="toggleShowSelectedOnly" onchange="filterMatrixRows()">
                                <label class="form-check-label fs-12 fw-medium text-dark ms-1" for="toggleShowSelectedOnly">Show selected only</label>
                            </div>

                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="expandAllModules()">Expand All</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="resetMatrixChanges()">Reset Changes</button>
                        </div>
                    </div>

                    {{-- Super Admin Protected Alert --}}
                    @if(($selectedRole->code ?? '') === 'SUPER_ADMIN')
                        <div class="alert alert-purple border-0 bg-purple-subtle text-purple-emphasis p-3 rounded-3 d-flex align-items-center gap-3 mb-3 mx-3 mt-3">
                            <i class="fa-solid fa-shield-halved fs-3 text-purple"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Protected System Role (Super Admin)</h6>
                                <p class="fs-12 mb-0">Super Admin holds permanent full unrestricted access across all 12 modules and actions. Permissions are protected by system policy and cannot be altered.</p>
                            </div>
                        </div>
                    @endif

                    {{-- Tabs Header --}}
                    <div class="matrix-tabs-header">
                        <span class="matrix-tab-link active" onclick="switchMatrixTab('permissions')">Module Permissions</span>
                        <span class="matrix-tab-link" onclick="switchMatrixTab('scope')">Data Scope</span>
                        <span class="matrix-tab-link" onclick="switchMatrixTab('advanced')">Advanced Controls</span>
                    </div>

                    {{-- Matrix Table --}}
                    <div class="table-responsive">
                        <table class="permission-matrix-table" id="matrixTable">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>View</th>
                                    <th>Create</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                    <th>Export</th>
                                    <th>Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modules as $modKey => $modMeta)
                                    @php
                                        $modPerms = $permissionsByModule->get($modKey, collect());
                                        $isLockedModule = $modMeta['is_locked'] ?? false;
                                        $isSuperAdmin = ($selectedRole->code ?? '') === 'SUPER_ADMIN';
                                    @endphp
                                    <tr data-module-key="{{ $modKey }}" data-module-group="{{ $modMeta['group'] }}">
                                        {{-- 1. Module Name Column --}}
                                        <td>
                                            <div class="module-title-row">
                                                <i class="{{ $modMeta['icon'] }}"></i>
                                                <span>{{ $modMeta['name'] }}</span>
                                            </div>
                                        </td>

                                        {{-- Actions: View, Create, Edit, Delete, Export, Manage --}}
                                        @foreach(['view', 'create', 'edit', 'delete', 'export', 'manage'] as $act)
                                            @php
                                                $permObj = $modPerms->firstWhere('action', $act);
                                                $isAssigned = $permObj && ($isSuperAdmin || in_array($permObj->id, $assignedPermissionIds));
                                            @endphp
                                            <td>
                                                @if($permObj)
                                                    @if($isSuperAdmin)
                                                        <input type="checkbox" 
                                                               class="matrix-checkbox" 
                                                               value="{{ $permObj->id }}" 
                                                               data-module="{{ $modKey }}" 
                                                               data-module-name="{{ $modMeta['name'] }}" 
                                                               data-action="{{ ucfirst($act) }}" 
                                                               checked disabled 
                                                               title="Super Admin has permanent full access">
                                                    @elseif($isLockedModule)
                                                        <i class="fa-solid fa-lock locked-lock-icon" title="Locked by system policy"></i>
                                                    @else
                                                        <input type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $permObj->id }}" 
                                                               class="matrix-checkbox"
                                                               data-module="{{ $modKey }}"
                                                               data-module-name="{{ $modMeta['name'] }}"
                                                               data-action="{{ ucfirst($act) }}"
                                                               {{ $isAssigned ? 'checked' : '' }}
                                                               onchange="handleCheckboxChange(this)">
                                                    @endif
                                                @else
                                                    <span class="dash-not-applicable">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Matrix Legend Footer --}}
                    <div class="matrix-legend-footer">
                        <div class="legend-item">
                            <span class="box-checked"></span>
                            <span>Checked = Allowed</span>
                        </div>
                        <div class="legend-item">
                            <span class="box-empty"></span>
                            <span>Empty = Not allowed</span>
                        </div>
                        <div class="legend-item">
                            <i class="fa-solid fa-lock text-muted"></i>
                            <span>Locked = Restricted by system policy</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Right Column: Permission Summary & Access Restrictions --}}
            <div class="col-lg-4">
                
                {{-- Permission Summary Card --}}
                <div class="role-summary-card">
                    <div class="card-header-label mb-1">Permission Summary</div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fs-13 fw-bold text-dark">{{ $selectedRole->name ?? 'Role Name' }}</span>
                        <span class="badge bg-primary-subtle text-primary fs-11" id="sideSummaryPermCountBadge">
                            {{ count($assignedPermissionIds) }} permissions selected
                        </span>
                    </div>

                    {{-- Dynamic List of Module Breakdown --}}
                    <div class="border-top pt-2 mb-3" id="permissionSummaryModuleList" style="max-height: 260px; overflow-y: auto;">
                        {{-- Filled dynamically by JS --}}
                    </div>

                    {{-- Status box --}}
                    <div class="role-alert-box valid mb-0">
                        <i class="fa-solid fa-circle-check fs-14 mt-1"></i>
                        <span>No permission conflicts detected</span>
                    </div>
                </div>

                {{-- Access Restrictions Card --}}
                <div class="role-summary-card">
                    <div class="card-header-label mb-2">Access Restrictions</div>

                    <table class="summary-meta-table mb-3">
                        <tbody>
                            <tr>
                                <td>Data Scope</td>
                                <td>{{ ($selectedRole->scope_type ?? 'full_org') === 'restricted' ? 'Assigned regions only' : 'Full Organization' }}</td>
                            </tr>
                            @if(!empty($selectedRole->assigned_regions))
                                <tr>
                                    <td>Regions</td>
                                    <td>{{ implode(', ', $selectedRole->assigned_regions) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td>Export Restriction</td>
                                <td>{{ ($selectedRole->restrict_exports ?? false) ? 'Enabled' : 'Disabled' }}</td>
                            </tr>
                            <tr>
                                <td>Admin Management</td>
                                <td class="text-danger">Denied</td>
                            </tr>
                            <tr>
                                <td>Settings</td>
                                <td class="text-danger">Denied</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mb-3">
                        <a href="{{ route('admin.roles.edit', $selectedRole->id ?? 1) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Access Scope
                        </a>
                    </div>

                    <div class="role-alert-box warning mb-0">
                        <i class="fa-solid fa-circle-exclamation fs-14 mt-1"></i>
                        <span>Permission changes affect all administrators assigned to this role.</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- 4. Bottom Sticky Action Footer --}}
        <div class="role-sticky-footer">
            <div class="audit-note">
                <i class="fa-solid fa-circle-check"></i>
                <span>Permission changes are versioned and recorded in the audit log.</span>
            </div>
            <div class="actions-btns-group">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary px-3">
                    Cancel
                </a>
                <button type="submit" name="save_draft" value="1" class="btn btn-outline-secondary px-3">
                    Save as Draft
                </button>
                <button type="button" class="btn btn-outline-primary px-3" onclick="reviewChangesAlert()">
                    <span id="reviewChangesBtnLabel">Review 0 Changes</span>
                </button>
                @if(($selectedRole->code ?? '') === 'SUPER_ADMIN')
                    <button type="button" class="btn btn-secondary px-4" disabled>
                        <i class="fa-solid fa-lock me-1"></i> Super Admin is Protected
                    </button>
                @else
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Permissions
                    </button>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- MODAL 1: Copy Permissions From Another Role --}}
<div class="modal fade" id="copyRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.roles.copy-permissions', $selectedRole->id ?? 1) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-copy text-primary me-2"></i>Copy From Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary fs-13 mb-3">
                    Select a source role. Its assigned permissions will be copied to <strong>{{ $selectedRole->name ?? 'Current Role' }}</strong>.
                </p>
                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold">Source Role</label>
                    <select name="source_role_id" class="form-select" required>
                        @foreach($roles as $r)
                            @if(!$selectedRole || $r->id !== $selectedRole->id)
                                <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->permissions->count() }} permissions)</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Apply & Copy Permissions</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: Permission Templates --}}
<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-shapes text-primary me-2"></i>Permission Templates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary fs-13 mb-3">Choose a pre-defined enterprise template to apply to this role:</p>
                <div class="list-group">
                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="applyQuickTemplate('view_only')">
                        <div>
                            <div class="fw-bold">View Only (Read-only)</div>
                            <small class="text-muted">Grants View actions across all operational modules</small>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">7 perms</span>
                    </button>
                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="applyQuickTemplate('operations_lead')">
                        <div>
                            <div class="fw-bold">Operations Lead</div>
                            <small class="text-muted">Devices, Locations, Notifications, Segments full access</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">18 perms</span>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let initialSelected = [];
    let unsavedChangesCount = 0;

    function changeRole(select) {
        window.location.href = "{{ route('admin.roles.permissions') }}?role_id=" + select.value;
    }

    function recordInitialState() {
        initialSelected = Array.from(document.querySelectorAll('.matrix-checkbox:checked')).map(cb => cb.value);
        updateLiveStats();
    }

    function handleCheckboxChange(cb) {
        updateLiveStats();
    }

    function updateLiveStats() {
        const checkedBoxes = Array.from(document.querySelectorAll('.matrix-checkbox:checked'));
        const totalChecked = checkedBoxes.length;

        // Enabled Modules count
        const modulesSet = new Set(checkedBoxes.map(cb => cb.getAttribute('data-module')));
        const totalModules = modulesSet.size;

        // Unsaved changes
        const currentSelected = checkedBoxes.map(cb => cb.value);
        const added = currentSelected.filter(x => !initialSelected.includes(x));
        const removed = initialSelected.filter(x => !currentSelected.includes(x));
        unsavedChangesCount = added.length + removed.length;

        document.getElementById('statSelectedPerms').textContent = totalChecked;
        document.getElementById('statEnabledModules').textContent = totalModules;
        document.getElementById('statUnsavedChanges').textContent = unsavedChangesCount;
        document.getElementById('sideSummaryPermCountBadge').textContent = `${totalChecked} permissions selected`;
        document.getElementById('reviewChangesBtnLabel').textContent = `Review ${unsavedChangesCount} Changes`;

        // Update Right Side Summary List
        const summaryList = document.getElementById('permissionSummaryModuleList');
        summaryList.innerHTML = '';

        const moduleGroups = {};
        checkedBoxes.forEach(cb => {
            const mName = cb.getAttribute('data-module-name');
            const act = cb.getAttribute('data-action');
            if (mName && act) {
                if (!moduleGroups[mName]) {
                    moduleGroups[mName] = [];
                }
                moduleGroups[mName].push(act);
            }
        });

        if (Object.keys(moduleGroups).length === 0) {
            summaryList.innerHTML = '<div class="text-muted fs-12 p-2">No permissions selected</div>';
        } else {
            for (const [mName, acts] of Object.entries(moduleGroups)) {
                const item = document.createElement('div');
                item.className = 'd-flex justify-content-between align-items-start py-1 fs-12 border-bottom border-light';
                item.innerHTML = `
                    <span class="fw-semibold text-dark">${mName}</span>
                    <span class="text-secondary text-end">${acts.join(', ')}</span>
                `;
                summaryList.appendChild(item);
            }
        }
    }

    function filterMatrixRows() {
        const query = document.getElementById('matrixSearchInput').value.toLowerCase().trim();
        const group = document.getElementById('matrixGroupFilter').value;
        const selectedOnly = document.getElementById('toggleShowSelectedOnly').checked;

        const rows = document.querySelectorAll('#matrixTable tbody tr');
        rows.forEach(tr => {
            const modKey = tr.getAttribute('data-module-key').toLowerCase();
            const modName = tr.querySelector('.module-title-row span').textContent.toLowerCase();
            const modGroup = tr.getAttribute('data-module-group');
            const hasChecked = tr.querySelectorAll('.matrix-checkbox:checked').length > 0;

            let matchesQuery = modKey.includes(query) || modName.includes(query);
            let matchesGroup = group === 'all' || modGroup === group;
            let matchesSelected = !selectedOnly || hasChecked;

            if (matchesQuery && matchesGroup && matchesSelected) {
                tr.style.display = '';
            } else {
                tr.style.display = 'none';
            }
        });
    }

    function resetMatrixChanges() {
        document.querySelectorAll('.matrix-checkbox').forEach(cb => {
            cb.checked = initialSelected.includes(cb.value);
        });
        updateLiveStats();
    }

    function expandAllModules() {
        document.getElementById('matrixSearchInput').value = '';
        document.getElementById('matrixGroupFilter').value = 'all';
        document.getElementById('toggleShowSelectedOnly').checked = false;
        filterMatrixRows();
    }

    function switchMatrixTab(tab) {
        document.querySelectorAll('.matrix-tab-link').forEach(el => el.classList.remove('active'));
        event.target.classList.add('active');
        // If needed in future, tab panels can be toggled here
    }

    function reviewChangesAlert() {
        alert(`You have ${unsavedChangesCount} unsaved permission modification(s). Click "Save Permissions" below to persist changes.`);
    }

    function applyQuickTemplate(type) {
        if (type === 'view_only') {
            document.querySelectorAll('.matrix-checkbox').forEach(cb => {
                cb.checked = cb.getAttribute('data-action') === 'View';
            });
        }
        updateLiveStats();
        bootstrap.Modal.getInstance(document.getElementById('templateModal')).hide();
    }

    document.addEventListener('DOMContentLoaded', function() {
        recordInitialState();
    });
</script>
@endpush
@endsection
