@extends('layout')

@section('title', 'Edit Firebase Project - ' . $setting->label)
@section('page_title', 'Edit Firebase Project')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Settings</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.settings.firebase.index') }}">Firebase</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active-crumb">Edit Project</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Top Action Bar -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="fs-4 fw-bold text-dark mb-0">Edit Firebase Project</h1>
                @if($setting->is_active)
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fs-11 fw-semibold">
                        <i class="fa-solid fa-bolt me-1"></i> Active Routing Project
                    </span>
                @endif
            </div>
            <p class="fs-13 text-secondary mb-0">Update credentials, switch routing status, or upload a replacement Service Account JSON for <strong>{{ $setting->label }}</strong>.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.settings.firebase.index') }}" class="btn btn-outline-secondary px-3 py-2 fs-13 rounded-2 fw-medium">Cancel</a>
            <button type="button" class="btn btn-primary px-3 py-2 fs-13 rounded-2 fw-medium d-inline-flex align-items-center gap-2" onclick="submitFirebaseForm()">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Update Configuration</span>
            </button>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 rounded-3 p-3 mb-4 fs-13">
            <div class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Please check the following errors:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.firebase.update', $setting->id) }}" enctype="multipart/form-data" id="firebaseConfigForm" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="config_mode" id="config_mode" value="upload">

        <div class="row g-4">
            <!-- Left Column: Primary Config Form -->
            <div class="col-lg-8">
                <!-- 1. General Project Details -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="fa-solid fa-gear text-primary fs-5"></i>
                        <h2 class="fs-6 fw-bold text-dark mb-0">Project Details</h2>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">Configuration Label / Name <span class="text-danger">*</span></label>
                        <div class="input-counter-wrap">
                            <input type="text" name="label" id="config_label" class="form-control rounded-2 fs-13" placeholder="e.g. GPS Camera Production" value="{{ old('label', $setting->label) }}" maxlength="100" required oninput="updateCharCount(this, 'labelCounter', 100)">
                            <span class="input-counter-badge" id="labelCounter">{{ strlen(old('label', $setting->label)) }} / 100</span>
                        </div>
                        <div class="invalid-feedback fs-11">Please provide a descriptive configuration label.</div>
                        <div class="form-text fs-11">A friendly name to identify this Firebase project in the admin console.</div>
                    </div>

                    <div class="form-check form-switch pt-2 border-top">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active', $setting->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label fs-13 fw-semibold text-dark" for="is_active">
                            Set as active project immediately upon saving
                        </label>
                        <div class="form-text fs-11">When enabled, outgoing push notifications will immediately route through this Firebase project.</div>
                    </div>
                </div>

                <!-- 2. Credentials Configuration Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="fa-solid fa-key text-primary fs-5"></i>
                        <h2 class="fs-6 fw-bold text-dark mb-0">Credentials & Service Account</h2>
                    </div>

                    @if(!empty($setting->service_account_json))
                        <div class="alert alert-success border-0 rounded-3 p-3 mb-3 fs-13 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                                <div>
                                    <strong>Service Account Loaded:</strong> Private key and credentials currently active.
                                    <div class="fs-11 text-muted">Upload a new <code>.json</code> file below only if you wish to overwrite existing credentials.</div>
                                </div>
                            </div>
                            <span class="badge bg-success text-white px-2 py-1 fs-11">Active Key</span>
                        </div>
                    @endif

                    <!-- Mode Selector Nav Pills -->
                    <ul class="nav nav-pills mb-3 gap-2" id="configTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-3 py-1 fs-12 fw-semibold" id="tab-upload-btn" data-bs-toggle="pill" data-bs-target="#tab-upload" type="button" role="tab" onclick="setConfigMode('upload')">
                                <i class="fa-solid fa-file-arrow-up me-1"></i> Upload Service Account JSON (Recommended)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-1 fs-12 fw-semibold" id="tab-manual-btn" data-bs-toggle="pill" data-bs-target="#tab-manual" type="button" role="tab" onclick="setConfigMode('manual')">
                                <i class="fa-solid fa-keyboard me-1"></i> Manual Fields & Web SDK
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content border rounded-3 p-3 bg-light" id="configTabContent">
                        <!-- Tab 1: Upload JSON -->
                        <div class="tab-pane fade show active" id="tab-upload" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label fs-13 fw-semibold text-dark">Upload New Firebase Service Account JSON (Optional)</label>
                                <input type="file" name="service_account_file" id="service_account_file" class="form-control fs-13" accept=".json,application/json">
                                <div class="form-text fs-11">Leave blank to retain current active Service Account key. Upload a new <code>.json</code> file to replace.</div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fs-13 fw-semibold text-dark">Or Paste Service Account JSON Content</label>
                                <textarea name="service_account_raw" id="service_account_raw" rows="4" class="form-control font-monospace fs-12" placeholder='{ "type": "service_account", "project_id": "...", "private_key": "..." }'>{{ old('service_account_raw') }}</textarea>
                                <div class="form-text fs-11">Paste new JSON content to replace current credentials.</div>
                            </div>
                        </div>

                        <!-- Tab 2: Manual Fields -->
                        <div class="tab-pane fade" id="tab-manual" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Firebase Project ID <span class="text-danger">*</span></label>
                                    <input type="text" name="project_id" id="project_id" class="form-control fs-13" placeholder="e.g. geocam-production-app" value="{{ old('project_id', $setting->project_id) }}">
                                    <div class="invalid-feedback fs-11">Firebase Project ID is mandatory.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Web API Key <span class="text-danger">*</span></label>
                                    <input type="text" name="api_key" id="api_key" class="form-control fs-13" placeholder="e.g. AIzaSyD..." value="{{ old('api_key', $setting->api_key) }}">
                                    <div class="invalid-feedback fs-11">Web API Key is mandatory.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">App ID (Mobile / Web) <span class="text-danger">*</span></label>
                                    <input type="text" name="app_id" id="app_id" class="form-control fs-13" placeholder="e.g. 1:123456789:android:abcdef" value="{{ old('app_id', $setting->app_id) }}">
                                    <div class="invalid-feedback fs-11">App ID is mandatory.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Messaging Sender ID <span class="text-danger">*</span></label>
                                    <input type="text" name="messaging_sender_id" id="messaging_sender_id" class="form-control fs-13" placeholder="e.g. 123456789012" value="{{ old('messaging_sender_id', $setting->messaging_sender_id) }}">
                                    <div class="invalid-feedback fs-11">Messaging Sender ID is mandatory.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Auth Domain <span class="text-danger">*</span></label>
                                    <input type="text" name="auth_domain" id="auth_domain" class="form-control fs-13" placeholder="e.g. geocam.firebaseapp.com" value="{{ old('auth_domain', $setting->auth_domain) }}">
                                    <div class="invalid-feedback fs-11">Auth Domain is mandatory.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Storage Bucket <span class="text-danger">*</span></label>
                                    <input type="text" name="storage_bucket" id="storage_bucket" class="form-control fs-13" placeholder="e.g. geocam.appspot.com" value="{{ old('storage_bucket', $setting->storage_bucket) }}">
                                    <div class="invalid-feedback fs-11">Storage Bucket is mandatory.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Measurement ID <span class="text-danger">*</span></label>
                                    <input type="text" name="measurement_id" id="measurement_id" class="form-control fs-13" placeholder="e.g. G-XXXXXXX" value="{{ old('measurement_id', $setting->measurement_id) }}">
                                    <div class="invalid-feedback fs-11">Measurement ID is mandatory.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">VAPID Key (Web Push) <span class="text-danger">*</span></label>
                                    <input type="text" name="vapid_key" id="vapid_key" class="form-control fs-13" placeholder="e.g. BElZ..." value="{{ old('vapid_key', $setting->vapid_key) }}">
                                    <div class="invalid-feedback fs-11">VAPID Key is mandatory.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Controls -->
                <div class="d-flex align-items-center justify-content-end gap-2 pt-2">
                    <a href="{{ route('admin.settings.firebase.index') }}" class="btn btn-outline-secondary px-4 py-2 fs-13 rounded-2 fw-medium">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fs-13 rounded-2 fw-medium d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Update Configuration</span>
                    </button>
                </div>
            </div>

            <!-- Right Column: Quick Guidance & Instructions -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="fa-solid fa-circle-question text-primary fs-5"></i>
                        <h3 class="fs-6 fw-bold text-dark mb-0">Project Status</h3>
                    </div>

                    <div class="d-flex flex-column gap-3 fs-13 text-secondary">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-12">Connection Health</span>
                                @if($setting->connection_status === 'connected')
                                    <span class="status-indicator active">
                                        <span class="status-dot"></span> Connected
                                    </span>
                                @elseif($setting->connection_status === 'failed')
                                    <span class="badge-status badge-danger">
                                        <span class="status-dot"></span> Failed
                                    </span>
                                @else
                                    <span class="badge-status badge-secondary">
                                        <span class="status-dot"></span> Untested
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-12">Routing State</span>
                                <span class="fw-bold text-dark">{{ $setting->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted fs-12">Last Verified</span>
                                <span class="fw-bold text-dark">{{ $setting->last_tested_at ? $setting->last_tested_at->diffForHumans() : 'Never' }}</span>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-shield-halved text-primary fs-14"></i>
                                <span>Automated Handshake</span>
                            </div>
                            <p class="mb-0 text-muted fs-12">
                                When updated, our system validates the private key against Google Cloud OAuth2 endpoints before applying changes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let currentConfigMode = 'upload';

function updateCharCount(input, counterId, max) {
    const counter = document.getElementById(counterId);
    if (counter) {
        counter.textContent = input.value.length + ' / ' + max;
    }
}

function setConfigMode(mode) {
    currentConfigMode = mode;
    document.getElementById('config_mode').value = mode;

    const manualFields = [
        'project_id',
        'api_key',
        'app_id',
        'messaging_sender_id',
        'auth_domain',
        'storage_bucket',
        'measurement_id',
        'vapid_key'
    ];

    if (mode === 'upload') {
        manualFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.removeAttribute('required');
        });
    } else {
        manualFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.setAttribute('required', 'required');
        });
    }
}

function submitFirebaseForm() {
    const form = document.getElementById('firebaseConfigForm');

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    form.submit();
}
</script>
@endpush
