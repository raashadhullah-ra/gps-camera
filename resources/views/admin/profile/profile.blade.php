@extends('layout')

@section('page_title', 'My Profile')

@section('breadcrumbs')
    <span>Account</span>
    <span class="mx-1 text-muted">/</span>
    <span class="active-crumb">Profile</span>
@endsection

@section('content')
<div class="profile-page">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div class="header-title-group">
            <h1 class="page-main-title">My Profile</h1>
            <p class="page-main-subtitle">Manage your Super Admin account information and preferences</p>
        </div>
        <div class="header-actions-group">
            <button type="button" class="btn btn-outline-primary btn-sm px-3 py-2" style="border-radius: 8px;">
                <i class="fa-regular fa-clock me-1"></i> View Login Activity
            </button>
            <button type="button" class="btn btn-primary btn-sm px-3 py-2" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="fa-solid fa-pen me-1"></i> Edit Profile
            </button>
        </div>
    </div>

    <!-- Top Profile Banner Card -->
    <div class="profile-banner-card">
        <div class="banner-user-left">
            <div class="profile-avatar-lg">
                {{ strtoupper(substr($user->name ?? 'SA', 0, 2)) }}
            </div>
            <div class="user-info-text">
                <div class="user-full-name">{{ $user->name ?? 'Super Admin' }}</div>
                <div class="badges-row">
                    <span class="badge-role-pill">Super Admin</span>
                    <span class="badge-active-pill">
                        <span class="status-dot"></span>
                        Active
                    </span>
                </div>
            </div>
        </div>

        <div class="banner-meta-items">
            <div class="meta-item">
                <i class="fa-regular fa-envelope meta-icon"></i>
                <div class="meta-content">
                    <div class="meta-label">Email Address</div>
                    <div class="meta-value">{{ $user->email }}</div>
                </div>
            </div>

            <div class="meta-item">
                <i class="fa-solid fa-phone meta-icon"></i>
                <div class="meta-content">
                    <div class="meta-label">Mobile Number</div>
                    <div class="meta-value">{{ $user->mobilenumber ?: '—' }}</div>
                </div>
            </div>

            <div class="meta-item">
                <i class="fa-regular fa-calendar meta-icon"></i>
                <div class="meta-content">
                    <div class="meta-label">Last Login</div>
                    <div class="meta-value">
                        {{ $user->lastloginat ? $user->lastloginat->format('M d, Y · h:i A') : '—' }}
                    </div>
                </div>
            </div>

            <div class="meta-item">
                <i class="fa-solid fa-location-dot meta-icon"></i>
                <div class="meta-content">
                    <div class="meta-label">Location</div>
                    <div class="meta-value">
                        {{ collect([$user->district, $user->state])->filter()->unique()->implode(', ') ?: '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2x2 Grid Section -->
    <div class="profile-grid-2x2">
        
        <!-- Card 1: Personal Information -->
        <div class="profile-card">
            <div>
                <div class="card-head-row">
                    <div class="head-title-group">
                        <i class="fa-regular fa-user"></i>
                        <h2 class="head-title">Personal Information</h2>
                    </div>
                </div>

                <div class="details-grid">
                    <div class="detail-entry">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">{{ $user->name }}</div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Display Name</div>
                        <div class="detail-value">{{ $user->displayname ?: $user->name }}</div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Email Address</div>
                        <div class="detail-value">
                            <span>{{ $user->email }}</span>
                            <span class="badge-verified">Verified</span>
                        </div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Mobile Number</div>
                        <div class="detail-value">
                            <span>{{ $user->mobilenumber ?: '—' }}</span>
                            @if($user->mobilenumber)
                                <span class="badge-verified">Verified</span>
                            @endif
                        </div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Time Zone</div>
                        <div class="detail-value">{{ $user->timezone ?: 'UTC' }}</div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Language</div>
                        <div class="detail-value">{{ $user->language ?: 'English' }}</div>
                    </div>
                </div>
            </div>

            <div class="card-action-row">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fa-solid fa-pen me-1"></i> Edit Information
                </button>
            </div>
        </div>

        <!-- Card 2: Security Status -->
        <!-- <div class="profile-card">
            <div>
                <div class="card-head-row">
                    <div class="head-title-group">
                        <i class="fa-solid fa-shield-halved"></i>
                        <h2 class="head-title">Security Status</h2>
                    </div>
                    <span class="badge-account-protected">
                        <i class="fa-solid fa-check"></i> Account protected
                    </span>
                </div>

                <div class="security-status-list">
                    <div class="sec-item">
                        <div class="sec-label">
                            <i class="fa-solid fa-lock"></i>
                            <span>Two-Factor Authentication</span>
                        </div>
                        <span class="sec-value status-positive">Enabled</span>
                    </div>

                    <div class="sec-item">
                        <div class="sec-label">
                            <i class="fa-solid fa-key"></i>
                            <span>Password Strength</span>
                        </div>
                        <span class="sec-value status-positive">Strong</span>
                    </div>

                    <div class="sec-item">
                        <div class="sec-label">
                            <i class="fa-regular fa-bell"></i>
                            <span>Login Alerts</span>
                        </div>
                        <span class="sec-value status-positive">Enabled</span>
                    </div>

                    <div class="sec-item">
                        <div class="sec-label">
                            <i class="fa-regular fa-envelope"></i>
                            <span>Recovery Email</span>
                        </div>
                        <span class="sec-value status-positive">Configured</span>
                    </div>
                </div>
            </div>

            <div class="card-action-row">
                <a href="{{ route('admin.profile.change-password') }}" class="btn btn-outline-primary">
                    <i class="fa-solid fa-lock me-1"></i> Change Password
                </a>
                <button type="button" class="btn btn-outline-primary">
                    <i class="fa-solid fa-shield-halved me-1"></i> Security Settings
                </button>
            </div>
        </div> -->

        <!-- Card 3: Account & Access -->
        <div class="profile-card">
            <div>
                <div class="card-head-row">
                    <div class="head-title-group">
                        <i class="fa-solid fa-lock"></i>
                        <h2 class="head-title">Account & Access</h2>
                    </div>
                </div>

                <div class="details-grid">
                    <div class="detail-entry">
                        <div class="detail-label">Account ID</div>
                        <div class="detail-value">ADM-{{ str_pad($user->id ?? 1, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Primary Role</div>
                        <div class="detail-value">Super Admin</div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Access Level</div>
                        <div class="detail-value">Full System Access</div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Last Password Change</div>
                        <div class="detail-value">
                            {{ $user->passwordchangedat ? $user->passwordchangedat->format('M d, Y') : 'Aug 02, 2026' }}
                        </div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Account Created</div>
                        <div class="detail-value">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : ($user->doj ? $user->doj->format('M d, Y') : 'Jan 12, 2026') }}
                        </div>
                    </div>
                    <div class="detail-entry">
                        <div class="detail-label">Active Sessions</div>
                        <div class="detail-value">2 devices</div>
                    </div>
                </div>
            </div>

            <div class="card-action-row">
                <a href="#" class="btn btn-link text-primary p-0 text-decoration-none fw-semibold fs-13 me-3">
                    <i class="fa-solid fa-shield-halved me-1"></i> View Permissions
                </a>
                <a href="#" class="btn btn-link text-primary p-0 text-decoration-none fw-semibold fs-13">
                    <i class="fa-solid fa-desktop me-1"></i> Manage Sessions
                </a>
            </div>
        </div>

        <!-- Card 4: Preferences -->
        <div class="profile-card">
            <div>
                <div class="card-head-row">
                    <div class="head-title-group">
                        <i class="fa-solid fa-gear"></i>
                        <h2 class="head-title">Preferences</h2>
                    </div>
                </div>

                <div class="pref-setting-list">
                    <div class="pref-row">
                        <span class="pref-title">Email Notifications</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="emailNotif" checked>
                        </div>
                    </div>

                    <div class="pref-row">
                        <span class="pref-title">Push Notifications</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="pushNotif" checked>
                        </div>
                    </div>

                    <div class="pref-row">
                        <span class="pref-title">Critical Security Alerts</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="secAlerts" checked>
                        </div>
                    </div>

                    <div class="pref-row">
                        <span class="pref-title">Date Format</span>
                        <select class="form-select form-select-sm" aria-label="Date Format">
                            <option selected>DD MMM YYYY</option>
                            <option value="1">YYYY-MM-DD</option>
                            <option value="2">MM/DD/YYYY</option>
                        </select>
                    </div>

                    <div class="pref-row">
                        <span class="pref-title">Theme</span>
                        <select class="form-select form-select-sm" aria-label="Theme">
                            <option selected>Light</option>
                            <option value="1">Dark</option>
                            <option value="2">System</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-action-row">
                <button type="button" class="btn btn-primary w-100">
                    Save Preferences
                </button>
            </div>
        </div>

    </div>

    <!-- Bottom Audit Log Alert Banner -->
    <div class="alert-audit-note">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Profile and access changes are recorded in the audit log.</span>
    </div>

</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow-lg rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="modal-title fw-bold text-dark mb-0" id="editProfileModalLabel">Edit Profile Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold text-dark mb-1">Full Name</label>
                    <input type="text" class="form-control rounded-3 bg-light text-muted" value="{{ $user->name }}" readonly style="cursor: not-allowed;">
                    <!-- <div class="fs-12 text-muted mt-1">Full name is permanent for Super Admin and cannot be edited.</div> -->
                </div>

                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold text-dark mb-1">Display Name</label>
                    <input type="text" name="displayname" class="form-control rounded-3" value="{{ old('displayname', $user->displayname) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold text-dark mb-1">Mobile Number</label>
                    <input type="text" name="mobilenumber" class="form-control rounded-3" value="{{ old('mobilenumber', $user->mobilenumber) }}">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">Time Zone</label>
                        <input type="text" name="timezone" class="form-control rounded-3" value="{{ old('timezone', $user->timezone ?? 'Asia/Kolkata') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">Language</label>
                        <input type="text" name="language" class="form-control rounded-3" value="{{ old('language', $user->language ?? 'English') }}">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">City</label>
                        <input type="text" name="city" class="form-control rounded-3" value="{{ old('city', $user->city) }}" placeholder="e.g. Tirunelveli">
                    </div>
                    <div class="col-6">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">District</label>
                        <input type="text" name="district" class="form-control rounded-3" value="{{ old('district', $user->district) }}" placeholder="e.g. Tirunelveli">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">State</label>
                        <input type="text" name="state" class="form-control rounded-3" value="{{ old('state', $user->state) }}" placeholder="e.g. Tamil Nadu">
                    </div>
                    <div class="col-6">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">Country</label>
                        <input type="text" name="country" class="form-control rounded-3" value="{{ old('country', $user->country) }}" placeholder="e.g. India">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
