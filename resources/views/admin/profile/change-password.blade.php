@extends('layout')

@section('page_title', 'Change Password')

@section('breadcrumbs')
    <span>Account</span>
    <span class="mx-1 text-muted">/</span>
    <span>Security</span>
    <span class="mx-1 text-muted">/</span>
    <span class="active-crumb">Change Password</span>
@endsection

@section('content')
<div class="change-password-page">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div class="header-title-group">
            <h1 class="page-main-title">Change Password</h1>
            <p class="page-main-subtitle">Update your Super Admin password and protect active sessions</p>
        </div>
        <div class="header-actions-group">
            <button type="button" class="btn btn-outline-primary btn-sm px-3 py-2" style="border-radius: 8px;">
                <i class="fa-solid fa-shield-halved me-1"></i> Security Guidelines
            </button>
        </div>
    </div>

    <!-- Main 2-Column Split Grid -->
    <div class="password-split-grid">
        
        <!-- Left Column: Password Update Form -->
        <div class="password-form-card">
            <h2 class="form-card-title">Update Password</h2>

            <!-- User Info Pill Strip -->
            <div class="user-pill-strip">
                <div class="user-avatar-sm">
                    {{ strtoupper(substr($user->displayname ?? $user->name ?? 'SA', 0, 2)) }}
                </div>
                <div>
                    <div class="user-name-text">{{ $user->name }}</div>
                    <div class="user-email-text">{{ $user->email }}</div>
                </div>
            </div>

            <form action="{{ route('admin.profile.update-password') }}" method="POST" class="needs-validation" novalidate id="changePasswordForm">
                @csrf

                <!-- Current Password -->
                <div class="form-field-item">
                    <div class="field-label-row">
                        <label class="field-label" for="currentPasswordInput">
                            Current Password <span class="req-star">*</span>
                        </label>
                        <a href="{{ route('admin.forgot-password') }}" class="forgot-pass-link">Forgot current password?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon-left"></i>
                        <input type="password" id="currentPasswordInput" name="current_password" class="form-control" placeholder="••••••••••••••••" required>
                        <i class="fa-regular fa-eye password-toggle-btn" data-target="currentPasswordInput"></i>
                    </div>
                </div>

                <!-- New Password -->
                <div class="form-field-item">
                    <div class="field-label-row">
                        <label class="field-label" for="newPasswordInput">
                            New Password <span class="req-star">*</span>
                        </label>
                    </div>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon-left"></i>
                        <input type="password" id="newPasswordInput" name="new_password" class="form-control" placeholder="••••••••••••••••" required>
                        <i class="fa-regular fa-eye password-toggle-btn" data-target="newPasswordInput"></i>
                    </div>

                    <!-- Strength Indicator Track -->
                    <div class="strength-indicator-box">
                        <div class="strength-bars-track">
                            <div class="bar-segment" id="bar1"></div>
                            <div class="bar-segment" id="bar2"></div>
                            <div class="bar-segment" id="bar3"></div>
                            <div class="bar-segment" id="bar4"></div>
                        </div>
                        <span class="strength-text" id="strengthLabel">Enter password</span>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div class="form-field-item">
                    <div class="field-label-row">
                        <label class="field-label" for="confirmPasswordInput">
                            Confirm New Password <span class="req-star">*</span>
                        </label>
                    </div>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon-left"></i>
                        <input type="password" id="confirmPasswordInput" name="new_password_confirmation" class="form-control" placeholder="••••••••••••••••" required>
                        <i class="fa-regular fa-eye password-toggle-btn" data-target="confirmPasswordInput"></i>
                        <i class="fa-solid fa-circle-check valid-check-icon" id="confirmCheckIcon"></i>
                    </div>
                </div>

                <!-- Password Requirements Checklist (Live JS Evaluated) -->
                <div class="password-reqs-section">
                    <div class="reqs-title">Password requirements</div>
                    <div class="reqs-grid">
                        <div class="req-item" id="reqLength">
                            <i class="fa-regular fa-circle-check"></i>
                            <span>At least 8 characters</span>
                        </div>
                        <div class="req-item" id="reqSpecial">
                            <i class="fa-regular fa-circle-check"></i>
                            <span>At least one special character</span>
                        </div>
                        <div class="req-item" id="reqCase">
                            <i class="fa-regular fa-circle-check"></i>
                            <span>Uppercase and lowercase letters</span>
                        </div>
                        <div class="req-item" id="reqNumber">
                            <i class="fa-regular fa-circle-check"></i>
                            <span>At least one number</span>
                        </div>
                        <div class="req-item" id="reqEmail">
                            <i class="fa-regular fa-circle-check"></i>
                            <span>Does not contain your email</span>
                        </div>
                    </div>
                </div>

                <!-- Sign out active devices checkbox -->
                <div class="signout-sessions-row">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="signoutOtherSessions" name="sign_out_other_devices" value="1" checked>
                        <label class="form-check-label" for="signoutOtherSessions">
                            Sign out all other active sessions
                            <span class="subtext">Your current browser session will remain active.</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-btn-actions">
                    <a href="{{ route('admin.profile') }}" class="btn btn-outline-secondary px-4 py-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2" >
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column: Sidebar Info Cards -->
        <div class="password-sidebar-cards">
            
            <!-- Card 1: Security Summary -->
            <div class="sidebar-info-card">
                <div class="card-header-row">
                    <span class="header-text">Security Summary</span>
                    <span class="badge-protected">
                        <i class="fa-solid fa-check"></i> Account protected
                    </span>
                </div>

                <div class="summary-meta-list">
                    <div class="summary-row">
                        <div class="label-group">
                            <i class="fa-solid fa-lock"></i>
                            <span>Two-Factor Authentication</span>
                        </div>
                        <span class="val-text positive">Enabled</span>
                    </div>

                    <div class="summary-row">
                        <div class="label-group">
                            <i class="fa-solid fa-key"></i>
                            <span>Last Password Change</span>
                        </div>
                        <span class="val-text">
                            {{ $user->passwordchangedat ? $user->passwordchangedat->format('M d, Y') : 'Aug 20, 2026' }}
                        </span>
                    </div>

                    <div class="summary-row">
                        <div class="label-group">
                            <i class="fa-regular fa-clock"></i>
                            <span>Password Age</span>
                        </div>
                        <span class="val-text">
                            {{ $user->passwordchangedat ? max(0, (int) $user->passwordchangedat->diffInDays(now())) . ' days' : '0 days' }}
                        </span>
                    </div>

                    <div class="summary-row">
                        <div class="label-group">
                            <i class="fa-solid fa-desktop"></i>
                            <span>Active Sessions</span>
                        </div>
                        <span class="val-text">2 devices</span>
                    </div>

                    <div class="summary-row">
                        <div class="label-group">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>Failed Login Attempts</span>
                        </div>
                        <span class="val-text">0</span>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-primary w-100 py-2 fs-13" style="border-radius: 8px;">
                    <i class="fa-solid fa-shield-halved me-1"></i> View Active Sessions
                </button>
            </div>

            <!-- Card 2: Password Security Best Practices -->
            <div class="sidebar-info-card">
                <div class="card-header-row">
                    <span class="header-text">Password Security</span>
                </div>

                <div class="best-practices-list">
                    <div class="practice-item">
                        <div class="step-num">1</div>
                        <div class="step-info">
                            <div class="step-title">Use a unique password</div>
                            <div class="step-desc">Avoid using personal information or common passwords.</div>
                        </div>
                    </div>

                    <div class="practice-item">
                        <div class="step-num">2</div>
                        <div class="step-info">
                            <div class="step-title">Never share administrator credentials</div>
                            <div class="step-desc">Keep your credentials confidential and secure.</div>
                        </div>
                    </div>

                    <div class="practice-item">
                        <div class="step-num">3</div>
                        <div class="step-info">
                            <div class="step-title">Change immediately after suspicious activity</div>
                            <div class="step-desc">Update your password if you suspect any unauthorized access.</div>
                        </div>
                    </div>
                </div>

                <div class="warning-box-amber">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>Changing the password signs out API sessions using your administrator credentials.</div>
                </div>
            </div>

        </div>

    </div>

    <!-- Bottom Audit Log Alert Banner -->
    <div class="alert-audit-note">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Password changes and session revocations are recorded in the audit log.</span>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const userEmail = "{{ strtolower($user->email ?? 'admin@gpscamera.app') }}";
    const userEmailPrefix = userEmail.split('@')[0];

    // 1. Password Visibility Toggle
    const toggleBtns = document.querySelectorAll('.password-toggle-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            }
        });
    });

    // 2. Real-time Password Strength & Requirements Checklist
    const newPassInput = document.getElementById('newPasswordInput');
    const confirmPassInput = document.getElementById('confirmPasswordInput');
    const confirmCheckIcon = document.getElementById('confirmCheckIcon');

    const bar1 = document.getElementById('bar1');
    const bar2 = document.getElementById('bar2');
    const bar3 = document.getElementById('bar3');
    const bar4 = document.getElementById('bar4');
    const strengthLabel = document.getElementById('strengthLabel');

    const reqLength = document.getElementById('reqLength');
    const reqSpecial = document.getElementById('reqSpecial');
    const reqCase = document.getElementById('reqCase');
    const reqNumber = document.getElementById('reqNumber');
    const reqEmail = document.getElementById('reqEmail');

    function updateRequirements() {
        const val = newPassInput.value || '';
        
        // Checklist checks
        const hasLength = val.length >= 8;
        const hasSpecial = /[@$!%*#?&_\-+=~^]/.test(val);
        const hasCase = /[a-z]/.test(val) && /[A-Z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasNoEmail = val.length > 0 && !val.toLowerCase().includes(userEmailPrefix);

        toggleReq(reqLength, hasLength);
        toggleReq(reqSpecial, hasSpecial);
        toggleReq(reqCase, hasCase);
        toggleReq(reqNumber, hasNumber);
        toggleReq(reqEmail, hasNoEmail);

        // Strength Calculation
        let score = 0;
        if (hasLength) score++;
        if (hasSpecial) score++;
        if (hasCase) score++;
        if (hasNumber) score++;
        if (hasNoEmail) score++;

        // Reset bars
        [bar1, bar2, bar3, bar4].forEach(b => {
            b.className = 'bar-segment';
        });

        if (val.length === 0) {
            strengthLabel.textContent = 'Enter password';
            strengthLabel.className = 'strength-text';
        } else if (score <= 2) {
            bar1.classList.add('active-weak');
            strengthLabel.textContent = 'Weak password';
            strengthLabel.className = 'strength-text text-weak';
        } else if (score === 3) {
            bar1.classList.add('active-fair');
            bar2.classList.add('active-fair');
            strengthLabel.textContent = 'Fair password';
            strengthLabel.className = 'strength-text text-fair';
        } else if (score === 4) {
            bar1.classList.add('active-good');
            bar2.classList.add('active-good');
            bar3.classList.add('active-good');
            strengthLabel.textContent = 'Good password';
            strengthLabel.className = 'strength-text text-good';
        } else {
            bar1.classList.add('active-strong');
            bar2.classList.add('active-strong');
            bar3.classList.add('active-strong');
            bar4.classList.add('active-strong');
            strengthLabel.textContent = 'Strong password';
            strengthLabel.className = 'strength-text text-strong';
        }

        checkConfirmation();
    }

    function toggleReq(element, isMet) {
        if (!element) return;
        if (isMet) {
            element.classList.add('met');
            const icon = element.querySelector('i');
            if (icon) {
                icon.className = 'fa-solid fa-circle-check';
            }
        } else {
            element.classList.remove('met');
            const icon = element.querySelector('i');
            if (icon) {
                icon.className = 'fa-regular fa-circle-check';
            }
        }
    }

    function checkConfirmation() {
        const p1 = newPassInput.value || '';
        const p2 = confirmPassInput.value || '';
        if (p1.length >= 8 && p1 === p2) {
            confirmCheckIcon.classList.add('show');
        } else {
            confirmCheckIcon.classList.remove('show');
        }
    }

    if (newPassInput) {
        newPassInput.addEventListener('input', updateRequirements);
    }
    if (confirmPassInput) {
        confirmPassInput.addEventListener('input', checkConfirmation);
    }

    // 3. Form Validation Submission
    const form = document.getElementById('changePasswordForm');
    if (form) {
        form.addEventListener('submit', function (event) {
            const p1 = newPassInput.value || '';
            const p2 = confirmPassInput.value || '';
            const isValidLength = p1.length >= 8;
            const isMatch = p1 === p2;

            if (!form.checkValidity() || !isValidLength || !isMatch) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    }
});
</script>
@endpush
@endsection
