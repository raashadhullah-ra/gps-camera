@extends('layout')

@section('title', 'App Versions & Force Update - GPS Camera Admin')
@section('page_title', 'App Versions')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Overview</a>
    <span class="breadcrumb-separator">/</span>
    <span>App Control</span>
    <span class="breadcrumb-separator">/</span>
    <span class="active-crumb">App Versions</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Top Header Bar -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="fs-4 fw-bold text-dark mb-1">App Versions & Force Update</h1>
            <p class="fs-13 text-secondary mb-0">Control the minimum required version, manage mandatory updates, and configure store URLs for mobile users.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge {{ $settings->force_update ? 'bg-danger text-white' : 'bg-success-subtle text-success' }} px-3 py-2 rounded-pill fs-12 fw-semibold">
                <i class="fa-solid {{ $settings->force_update ? 'fa-shield-halved' : 'fa-circle-check' }} me-1"></i>
                Force Update: {{ $settings->force_update ? 'ENABLED' : 'DISABLED' }}
            </span>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa-solid fa-circle-check fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation fs-5 text-danger"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                <strong>Please fix the following validation errors:</strong>
            </div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Settings Configuration Form -->
        <div class="col-lg-7 col-xl-8">
            <form action="{{ route('admin.app-versions.update') }}" method="POST" id="appVersionForm">
                @csrf

                <!-- 1. Version Rules Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fa-solid fa-code-branch fs-6"></i>
                            </div>
                            <div>
                                <h5 class="fs-6 fw-bold mb-0">Version Control Rules</h5>
                                <span class="fs-12 text-muted">Define the current live release and the minimum acceptable version</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="app_name" class="form-label fs-13 fw-semibold text-dark">App Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('app_name') is-invalid @enderror" id="app_name" name="app_name" value="{{ old('app_name', $settings->app_name) }}" required placeholder="GPS Camera">
                                @error('app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="platform" class="form-label fs-13 fw-semibold text-dark">Target Platform <span class="text-danger">*</span></label>
                                <select class="form-select @error('platform') is-invalid @enderror" id="platform" name="platform" required>
                                    <option value="all" {{ old('platform', $settings->platform) === 'all' ? 'selected' : '' }}>All Platforms (Android & iOS)</option>
                                    <option value="android" {{ old('platform', $settings->platform) === 'android' ? 'selected' : '' }}>Android Only</option>
                                    <option value="ios" {{ old('platform', $settings->platform) === 'ios' ? 'selected' : '' }}>iOS Only</option>
                                </select>
                                @error('platform')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="current_version" class="form-label fs-13 fw-semibold text-dark">
                                    Latest (Current) Version <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-tag"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('current_version') is-invalid @enderror" id="current_version" name="current_version" value="{{ old('current_version', $settings->current_version) }}" required placeholder="1.5.0">
                                </div>
                                <small class="text-muted fs-11 mt-1 d-block">The latest release available on app stores (e.g. 1.5.0).</small>
                                @error('current_version')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="minimum_version" class="form-label fs-13 fw-semibold text-dark">
                                    Minimum Required Version <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-shield-halved"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('minimum_version') is-invalid @enderror" id="minimum_version" name="minimum_version" value="{{ old('minimum_version', $settings->minimum_version) }}" required placeholder="1.4.0">
                                </div>
                                <small class="text-muted fs-11 mt-1 d-block">Users on versions <strong>below this</strong> will be blocked with a force update dialog.</small>
                                @error('minimum_version')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-3">
                                <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="fa-solid fa-bolt"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-13">Enable Mandatory Force Update</div>
                                            <div class="text-muted fs-11">When enabled, any client version strictly lower than the minimum version cannot dismiss the update popup.</div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-5 m-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="force_update" name="force_update" value="1" {{ old('force_update', $settings->force_update) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Dialog Customization Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm bg-info-subtle text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fa-solid fa-message fs-6"></i>
                            </div>
                            <div>
                                <h5 class="fs-6 fw-bold mb-0">Update Dialog Content</h5>
                                <span class="fs-12 text-muted">Customize the modal title and description shown inside Flutter</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="update_title" class="form-label fs-13 fw-semibold text-dark">Dialog Title</label>
                                <input type="text" class="form-control @error('update_title') is-invalid @enderror" id="update_title" name="update_title" value="{{ old('update_title', $settings->update_title) }}" placeholder="New Update Available">
                                @error('update_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="update_message" class="form-label fs-13 fw-semibold text-dark">Dialog Description / Release Notes</label>
                                <textarea class="form-control @error('update_message') is-invalid @enderror" id="update_message" name="update_message" rows="3" placeholder="Please update to the latest version of GPS Camera to continue using all features.">{{ old('update_message', $settings->update_message) }}</textarea>
                                @error('update_message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. App Store URLs Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fa-brands fa-google-play fs-6"></i>
                            </div>
                            <div>
                                <h5 class="fs-6 fw-bold mb-0">Store Redirect Links</h5>
                                <span class="fs-12 text-muted">Users will be redirected to these links when clicking "Update Now"</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="play_store_url" class="form-label fs-13 fw-semibold text-dark">
                                    <i class="fa-brands fa-google-play text-success me-1"></i> Google Play Store URL
                                </label>
                                <div class="input-group">
                                    <input type="url" class="form-control @error('play_store_url') is-invalid @enderror" id="play_store_url" name="play_store_url" value="{{ old('play_store_url', $settings->play_store_url) }}" placeholder="https://play.google.com/store/apps/details?id=com.geocam.app">
                                    @if($settings->play_store_url)
                                        <a href="{{ $settings->play_store_url }}" target="_blank" class="btn btn-outline-secondary" title="Test Play Store Link">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    @endif
                                </div>
                                @error('play_store_url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="app_store_url" class="form-label fs-13 fw-semibold text-dark">
                                    <i class="fa-brands fa-apple text-dark me-1"></i> Apple App Store URL (Optional)
                                </label>
                                <div class="input-group">
                                    <input type="url" class="form-control @error('app_store_url') is-invalid @enderror" id="app_store_url" name="app_store_url" value="{{ old('app_store_url', $settings->app_store_url) }}" placeholder="https://apps.apple.com/app/gps-camera">
                                    @if($settings->app_store_url)
                                        <a href="{{ $settings->app_store_url }}" target="_blank" class="btn btn-outline-secondary" title="Test App Store Link">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    @endif
                                </div>
                                @error('app_store_url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Action Button -->
                <div class="d-flex align-items-center justify-content-end gap-3 mb-4">
                    <button type="reset" class="btn btn-light px-4 py-2 text-secondary fw-semibold">Reset</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm fw-semibold">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save App Version Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column: Live Mockup Preview & Quick Version Tester -->
        <div class="col-lg-5 col-xl-4">
            <div class="sticky-top" style="top: 90px; z-index: 10;">
                
                <!-- 1. Live Flutter Dialog Preview Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-mobile-screen-button text-primary"></i>
                            <h6 class="fs-6 fw-bold mb-0">Live Mobile Preview</h6>
                        </div>
                        <span class="badge bg-light text-secondary border fs-11">Flutter Dialog</span>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <!-- Mobile Device Mockup Container -->
                        <div class="p-4 rounded-4 text-center position-relative shadow-inner" style="background: linear-gradient(145deg, #1e293b, #0f172a); min-height: 310px; display: flex; align-items: center; justify-content: center;">
                            <!-- Mobile Dialog Popup Box -->
                            <div class="card border-0 shadow-lg rounded-4 p-3 bg-white text-dark w-100" style="max-width: 290px; animation: popIn 0.3s ease;">
                                <div class="avatar-md mx-auto mb-2 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                                    <i class="fa-solid fa-cloud-arrow-down fs-5"></i>
                                </div>
                                <h6 class="fw-bold mb-1 fs-14" id="previewTitle">{{ $settings->update_title ?? 'New Update Available' }}</h6>
                                <p class="text-muted fs-11 mb-3 px-1" id="previewMessage" style="line-height: 1.4;">
                                    {{ $settings->update_message ?? 'A new version of GPS Camera is available. Please update to continue.' }}
                                </p>
                                
                                <div class="d-flex flex-column gap-2">
                                    <button type="button" class="btn btn-primary btn-sm py-2 rounded-3 fw-semibold w-100 shadow-sm">
                                        <i class="fa-solid fa-arrow-up-from-bracket me-1"></i> Update Now
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm py-1 rounded-3 text-muted fs-11 border-0" id="previewLaterBtn" style="{{ $settings->force_update ? 'display: none;' : '' }}">
                                        Maybe Later
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted fs-11"><i class="fa-solid fa-eye me-1"></i> Changes above update this preview in real time.</small>
                        </div>
                    </div>
                </div>

                <!-- 2. Live Semantic Version Tester Tool -->
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calculator text-primary"></i>
                            <h6 class="fs-6 fw-bold mb-0">Test Version Logic</h6>
                        </div>
                        <span class="fs-12 text-muted">Simulate what a user app version receives</span>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="mb-3">
                            <label class="form-label fs-12 fw-semibold text-dark">Simulate Installed App Version</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="testVersionInput" value="1.2.0" placeholder="e.g. 1.2.0">
                                <button type="button" class="btn btn-outline-primary" id="runTestBtn">
                                    <i class="fa-solid fa-play me-1"></i> Evaluate
                                </button>
                            </div>
                        </div>

                        <!-- Result Display Box -->
                        <div id="testResultBox" class="p-3 rounded-3 bg-light border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fs-12 fw-semibold text-muted">Evaluation Result:</span>
                                <span class="badge" id="testResultBadge"></span>
                            </div>
                            <div class="fs-12 text-dark" id="testResultDetails"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@keyframes popIn {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('update_title');
    const msgInput = document.getElementById('update_message');
    const forceToggle = document.getElementById('force_update');
    
    const previewTitle = document.getElementById('previewTitle');
    const previewMessage = document.getElementById('previewMessage');
    const previewLaterBtn = document.getElementById('previewLaterBtn');

    // Live sync inputs to preview mockup
    if (titleInput && previewTitle) {
        titleInput.addEventListener('input', function() {
            previewTitle.textContent = this.value || 'New Update Available';
        });
    }

    if (msgInput && previewMessage) {
        msgInput.addEventListener('input', function() {
            previewMessage.textContent = this.value || 'A new version of GPS Camera is available. Please update to continue.';
        });
    }

    if (forceToggle && previewLaterBtn) {
        forceToggle.addEventListener('change', function() {
            previewLaterBtn.style.display = this.checked ? 'none' : 'block';
        });
    }

    // Version Evaluation Tester
    const testInput = document.getElementById('testVersionInput');
    const runTestBtn = document.getElementById('runTestBtn');
    const resultBadge = document.getElementById('testResultBadge');
    const resultDetails = document.getElementById('testResultDetails');

    function evaluateVersionClient() {
        const clientVer = (testInput.value || '1.0.0').trim().replace(/^v/i, '');
        const currentVer = (document.getElementById('current_version').value || '1.0.0').trim();
        const minVer = (document.getElementById('minimum_version').value || '1.0.0').trim();
        const isForceEnabled = document.getElementById('force_update').checked;

        // Perform semantic comparison
        function compareSemver(v1, v2) {
            const p1 = v1.split('.').map(Number);
            const p2 = v2.split('.').map(Number);
            for (let i = 0; i < Math.max(p1.length, p2.length); i++) {
                const n1 = p1[i] || 0;
                const n2 = p2[i] || 0;
                if (n1 > n2) return 1;
                if (n1 < n2) return -1;
            }
            return 0;
        }

        const isBelowMin = compareSemver(clientVer, minVer) < 0;
        const isBelowCurrent = compareSemver(clientVer, currentVer) < 0;
        const forceUpdate = isBelowMin || (isForceEnabled && isBelowCurrent);

        if (forceUpdate) {
            resultBadge.className = 'badge bg-danger text-white px-2 py-1';
            resultBadge.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> FORCE UPDATE';
            resultDetails.innerHTML = `Version <strong>${clientVer}</strong> is below minimum required <strong>${minVer}</strong>. Flutter app will block user and force update.`;
        } else if (isBelowCurrent) {
            resultBadge.className = 'badge bg-warning text-dark px-2 py-1';
            resultBadge.innerHTML = '<i class="fa-solid fa-circle-info me-1"></i> OPTIONAL UPDATE';
            resultDetails.innerHTML = `Version <strong>${clientVer}</strong> meets minimum (<strong>${minVer}</strong>) but is older than latest (<strong>${currentVer}</strong>). Optional dialog shown.`;
        } else {
            resultBadge.className = 'badge bg-success text-white px-2 py-1';
            resultBadge.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> UP TO DATE';
            resultDetails.innerHTML = `Version <strong>${clientVer}</strong> is on or ahead of latest version (<strong>${currentVer}</strong>). No update dialog shown.`;
        }
    }

    if (runTestBtn) {
        runTestBtn.addEventListener('click', evaluateVersionClient);
    }
    if (testInput) {
        testInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') evaluateVersionClient();
        });
    }

    // Run initial evaluation
    evaluateVersionClient();
});
</script>
@endpush
