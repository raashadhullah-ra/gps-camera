@extends('layout')

@section('title', 'Edit Administrator - GPS Camera Admin')
@section('page_title', 'Edit Administrator')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Admin Management</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1 text-muted"></i>
    <a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none">Admin List</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1 text-muted"></i>
    <span class="active-crumb">Edit Admin</span>
@endsection

@section('content')
<div class="devices-page roles-page add-admin-page-container">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="editAdminForm" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        {{-- 1. Top Header Row (Standard 18px Sizing & Actions Alignment) --}}
        <div class="page-header-row mb-3">
            <div class="header-title-group">
                <h1 class="page-main-title">Edit Administrator</h1>
                <p class="page-main-subtitle">Update administrator profile details, assigned role, and security access</p>
            </div>
            <div class="header-actions-group">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Admin List</span>
                </a>
                <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#guidelinesModal">
                    <i class="fa-solid fa-book-bookmark"></i>
                    <span>Admin Guidelines</span>
                </button>
            </div>
        </div>

        {{-- 2. Two-Column Layout --}}
        <div class="row g-3">
            {{-- Left Main Column --}}
            <div class="col-lg-8">

                {{-- CARD 1: Basic Information --}}
                <div class="role-card">
                    <div class="card-header-label">Basic Information</div>

                    <div class="row g-3">
                        {{-- Photo Upload Column with Cropper & 2MB Validation --}}
                        <div class="col-md-3 d-flex flex-column align-items-center justify-content-center border-end">
                            <div class="upload-photo-container text-center">
                                <div class="avatar-placeholder-circle cursor-pointer position-relative overflow-hidden mb-2" id="avatarPreviewBox" onclick="document.getElementById('adminPhotoInput').click()" title="Click to change & crop photo" style="cursor: pointer;">
                                    <span id="avatarTextInitials" style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    <img id="avatarImagePreview" src="" alt="Avatar Preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <input type="file" id="adminPhotoInput" accept="image/jpeg, image/png, image/jpg, image/webp" style="display: none;">
                                <input type="hidden" name="cropped_photo" id="croppedPhotoInput">
                                <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="document.getElementById('adminPhotoInput').click()">
                                    <i class="fa-solid fa-camera me-1"></i> Change Photo
                                </button>
                                <div class="fs-11 text-muted mt-2" style="font-size: 11px; line-height: 1.3;">
                                    Recommended: 500 × 500 px.<br>Max 2MB (JPG, PNG).
                                </div>
                            </div>
                        </div>

                        {{-- Form Inputs Column --}}
                        <div class="col-md-9">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark mb-1" for="adminFullName">
                                        Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="adminFullName" class="form-control" 
                                           placeholder="Enter full name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required oninput="handleNameInput(this)">
                                    <div class="invalid-feedback">
                                        Please enter the administrator's full name.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark mb-1" for="adminDisplayName">
                                        Display Name
                                    </label>
                                    <input type="text" name="displayname" id="adminDisplayName" class="form-control" 
                                           placeholder="Enter display name" 
                                           value="{{ old('displayname', $user->displayname) }}">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark mb-1" for="adminEmail">
                                        Email Address <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" id="adminEmail" class="form-control" 
                                           placeholder="admin@example.com" 
                                           value="{{ old('email', $user->email) }}" 
                                           required oninput="updateAdminSummary()">
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark mb-1" for="adminMobile">
                                        Mobile Number
                                    </label>
                                    <input type="text" name="mobilenumber" id="adminMobile" class="form-control" 
                                           placeholder="e.g. +91 90000 00000" 
                                           value="{{ old('mobilenumber', $user->mobilenumber) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Access & Role --}}
                <div class="role-card">
                    <div class="card-header-label">Access & Role</div>

                    @php
                        $userRoleId = $user->roles->first()?->id;
                    @endphp

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Primary Role <span class="text-danger">*</span></label>
                            <select name="role_id" id="adminPrimaryRoleSelect" class="form-select" onchange="handleRoleChange(this)" required>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}" 
                                            data-role-name="{{ $r->name }}" 
                                            data-role-code="{{ $r->code }}" 
                                            data-perm-count="{{ $r->permissions->count() }}"
                                            {{ old('role_id', $userRoleId) == $r->id ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please select a primary role.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Access Scope <span class="text-danger">*</span></label>
                            <select name="access_scope" id="adminAccessScope" class="form-select">
                                <option value="selected_modules" selected>Selected Modules</option>
                                <option value="full_org">Full Organization</option>
                                <option value="restricted">Restricted Scope (Assigned Regions)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Quick Module Badges Checkboxes --}}
                    <div class="row g-2 pt-2 border-top">
                        <div class="col-6 col-md-3">
                            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0" type="checkbox" checked disabled>
                                <span class="fs-12 fw-semibold text-dark">Dashboard</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0" type="checkbox" checked disabled>
                                <span class="fs-12 fw-semibold text-dark">Installed Devices</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0" type="checkbox" checked disabled>
                                <span class="fs-12 fw-semibold text-dark">Analytics</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0" type="checkbox" checked disabled>
                                <span class="fs-12 fw-semibold text-dark">Crash Reports</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0" type="checkbox" checked disabled>
                                <span class="fs-12 fw-semibold text-dark">Remote Config</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0" type="checkbox" checked disabled>
                                <span class="fs-12 fw-semibold text-dark">Ads Management</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2 opacity-75">
                                <input class="form-check-input mt-0" type="checkbox" disabled>
                                <span class="fs-12 text-muted">Admin Users</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2 opacity-75">
                                <input class="form-check-input mt-0" type="checkbox" disabled>
                                <span class="fs-12 text-muted">Settings</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.roles.permissions') }}" class="text-primary text-decoration-none fs-13 fw-semibold">
                            <i class="fa-solid fa-sliders me-1"></i> Customize Role Permissions
                        </a>
                    </div>
                </div>

                {{-- CARD 3: Account Security & Password Update (Clean Embedded Eye Toggle without Dividing Line) --}}
                <div class="role-card">
                    <div class="card-header-label">Account Security</div>

                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1" for="editPasswordInput">
                            Reset Password <span class="text-muted fw-normal fs-12">(Leave blank to keep existing password)</span>
                        </label>
                        <div class="input-icon-group position-relative">
                            <input type="password" name="password" id="editPasswordInput" class="form-control" placeholder="Enter new password (min 8 characters)" minlength="8">
                            <i class="fa-regular fa-eye password-toggle-icon" id="passwordToggleBtn" title="Toggle password visibility"></i>
                        </div>
                    </div>

                    <div class="row g-3 pt-2">
                        <div class="col-md-6 d-flex flex-column gap-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="two_factor_enabled" id="toggle2FA" value="1" {{ $user->two_factor_enabled ? 'checked' : '' }} onchange="updateAdminSummary()">
                                <label class="form-check-label fs-13 text-dark ms-1" for="toggle2FA">
                                    Require two-factor authentication (2FA)
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex flex-column gap-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-13 fw-semibold text-dark">Account Status</span>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input cursor-pointer" type="checkbox" name="account_status" id="toggleAccountStatus" value="1" {{ $user->status === 1 ? 'checked' : '' }} onchange="updateAdminSummary()">
                                    <label class="form-check-label fs-12 fw-semibold {{ $user->status === 1 ? 'text-success' : 'text-secondary' }} ms-1" id="accountStatusLabel" for="toggleAccountStatus">
                                        {{ $user->status === 1 ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Sticky Sidebar Column --}}
            <div class="col-lg-4">

                {{-- Administrator Summary Card --}}
                <div class="role-summary-card">
                    <div class="summary-header-badge">
                        <div class="admin-summary-avatar-circle" id="summaryAvatarCircle">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="role-meta-titles">
                            <span class="role-name-display" id="summaryAdminName">{{ $user->name }}</span>
                            <span class="role-code-display" id="summaryAdminEmail">{{ $user->email }}</span>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-primary-subtle text-primary fs-11 px-2 py-0" id="summaryRolePill">{{ $user->roles->first()->name ?? $user->role }}</span>
                                <span class="status-dot-active" id="summaryStatusText">{{ $user->status === 1 ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                    </div>

                    <table class="summary-meta-table">
                        <tbody>
                            <tr>
                                <td>Admin ID</td>
                                <td><code>{{ $user->admin_id ?? 'ADM-'.$user->id }}</code></td>
                            </tr>
                            <tr>
                                <td>2FA</td>
                                <td id="summary2FAVal">{{ $user->two_factor_enabled ? 'Required' : 'Disabled' }}</td>
                            </tr>
                            <tr>
                                <td>Last Activity</td>
                                <td>{{ $user->lastloginat ? $user->lastloginat->diffForHumans() : 'Never' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="role-alert-box valid">
                        <i class="fa-solid fa-circle-check fs-14 mt-1"></i>
                        <span>Administrator account is active and verified</span>
                    </div>
                </div>

                {{-- Permission Preview Card --}}
                <div class="role-summary-card">
                    <div class="card-header-label mb-2">Permission Scope</div>
                    
                    <ul class="access-preview-list">
                        <li>
                            <i class="fa-solid fa-circle-check icon-allow"></i>
                            <span>View Dashboard</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check icon-allow"></i>
                            <span>Manage Assigned Modules</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check icon-allow"></i>
                            <span>Telemetry and Diagnostics Access</span>
                        </li>
                    </ul>
                </div>

                {{-- Bottom Audit Info Box --}}
                <div class="p-3 bg-white rounded-3 border text-secondary fs-12 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-primary fs-14"></i>
                    <span>Account profile and security changes will be timestamped in the audit log.</span>
                </div>

            </div>
        </div>

        {{-- 3. Fixed Bottom Action Bar --}}
        <div class="role-sticky-footer">
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">
                    Cancel
                </a>
            </div>
            <div class="actions-btns-group">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Administrator
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Crop Photo Modal (Cropper.js 1:1 Aspect Ratio) --}}
<div class="modal fade" id="cropPhotoModal" tabindex="-1" aria-labelledby="cropPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold fs-15 text-dark" id="cropPhotoModalLabel">
                    <i class="fa-solid fa-crop-simple text-primary me-2"></i>Crop Administrator Photo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="avatar-cropper-container mb-3 shadow-inner rounded-3">
                    <img id="imageToCrop" src="" alt="Crop Source Image">
                </div>
                
                {{-- Toolbar Controls --}}
                <div class="d-flex align-items-center justify-content-between pt-2">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="cropZoomInBtn" title="Zoom In">
                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="cropZoomOutBtn" title="Zoom Out">
                            <i class="fa-solid fa-magnifying-glass-minus"></i>
                        </button>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="cropRotateLeftBtn" title="Rotate Left">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="cropRotateRightBtn" title="Rotate Right">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="cropResetBtn" title="Reset">
                        <i class="fa-solid fa-arrows-rotate me-1"></i> Reset
                    </button>
                </div>
                <div class="fs-12 text-muted text-center mt-2">
                    Recommended: 500 × 500 px. Drag to reposition and scroll to zoom.
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-3">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm px-4" id="applyCropBtn">
                    <i class="fa-solid fa-check me-1"></i> Crop & Apply
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Guidelines Modal --}}
<div class="modal fade" id="guidelinesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-book-bookmark text-primary me-2"></i>Admin Guidelines</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body fs-13 text-secondary">
                <p><strong>1. Least Privilege Principle:</strong> Assign only the minimum necessary module permissions required for the administrator's designated role.</p>
                <p><strong>2. Two-Factor Authentication:</strong> Required by default for all administrators accessing sensitive location telemetry.</p>
                <p><strong>3. Regional Scoping:</strong> Restrict regional operations staff to their assigned territory to prevent unauthorized data exports.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Understood</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // 1. Initialize Cropper.js & Image Picker
    document.addEventListener('DOMContentLoaded', function () {
        if (window.setupAvatarCropper) {
            window.setupAvatarCropper({
                fileInputId: 'adminPhotoInput',
                previewImgId: 'avatarImagePreview',
                previewIconId: 'avatarUploadIcon',
                summaryCircleId: 'summaryAvatarCircle',
                hiddenCroppedInputId: 'croppedPhotoInput'
            });
        }

        // Bootstrap Client Validation
        const form = document.getElementById('editAdminForm');
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // 2. Name & Avatar Initials
    function handleNameInput(input) {
        const val = input.value.trim();
        document.getElementById('summaryAdminName').textContent = val || 'Administrator';
        
        const summaryCircle = document.getElementById('summaryAvatarCircle');
        if (!summaryCircle.querySelector('img')) {
            if (val) {
                const parts = val.split(' ');
                const initials = parts.length > 1 ? (parts[0][0] + parts[1][0]).toUpperCase() : val.substring(0, 2).toUpperCase();
                summaryCircle.textContent = initials;
            } else {
                summaryCircle.textContent = 'AD';
            }
        }
    }

    // 3. Primary Role Selection
    function handleRoleChange(select) {
        const selectedOpt = select.options[select.selectedIndex];
        const roleName = selectedOpt.getAttribute('data-role-name') || 'Role';
        document.getElementById('summaryRolePill').textContent = roleName;
    }

    // 4. Summary Updates
    function updateAdminSummary() {
        document.getElementById('summaryAdminEmail').textContent = document.getElementById('adminEmail').value || 'email@example.com';
        
        const is2FA = document.getElementById('toggle2FA').checked;
        document.getElementById('summary2FAVal').textContent = is2FA ? 'Required' : 'Disabled';

        const isAct = document.getElementById('toggleAccountStatus').checked;
        document.getElementById('accountStatusLabel').textContent = isAct ? 'Active' : 'Inactive';
        document.getElementById('accountStatusLabel').className = isAct ? 'form-check-label fs-12 fw-semibold text-success ms-1' : 'form-check-label fs-12 fw-semibold text-secondary ms-1';
        document.getElementById('summaryStatusText').textContent = isAct ? 'Active' : 'Inactive';
    }
</script>
@endpush
@endsection
