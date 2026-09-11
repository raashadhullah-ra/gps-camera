@extends('layout')

@section('title', 'Google AdMob Configuration')

@section('breadcrumbs')
    <span>App Control</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.ads.index') }}" class="text-decoration-none text-gray-800">Ads Management</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Google AdMob Configuration</span>
@endsection

@section('content')
    <div class="admob-config-page">

        <!-- Page Header -->
        <div class="page-header-row">
            <div>
                <h1 class="page-main-title">Google AdMob Configuration</h1>
                <p class="page-main-subtitle">Connect your AdMob account, register mobile apps and configure
                    reporting</p>
            </div>
            <div class="header-actions-group">
                <a href="{{ route('admin.ads.index') }}" class="btn btn-outline-primary">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Ads</span>
                </a>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#placementManagerModal">
                    <i class="fa-regular fa-clone"></i>
                    <span>Placement Manager</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAdModal">
                    <i class="fa-solid fa-plus me-1"></i> Create Ad
                </button>
            </div>
        </div>


        <!-- Top OAuth Integration Banner Card -->
        <div class="admob-oauth-card">
            <div class="oauth-left">
                <div class="admob-logo-box">
                    <i class="fa-brands fa-google"></i>
                </div>
                <div class="oauth-meta">
                    <div class="oauth-title-row">
                        <span class="oauth-title">Google AdMob</span>
                        <span
                            class="badge {{ ($config['connection_status'] ?? '') === 'Token Expired' ? 'bg-warning text-dark' : (!empty($config['is_google_connected']) ? 'bg-success' : 'bg-danger-subtle text-danger border border-danger-subtle') }}"
                            id="oauthStatusBadge">
                            @if(($config['connection_status'] ?? '') === 'Token Expired')
                                Token Expired ({{ $config['connected_email'] ?? 'Google Account' }})
                            @elseif(!empty($config['is_google_connected']))
                                Connected ({{ $config['connected_email'] ?? 'Google Account' }})
                            @else
                                Not Connected
                            @endif
                        </span>
                    </div>
                    <p class="oauth-desc">
                        Connect a Google account with access to your AdMob publisher account.
                    </p>
                </div>
            </div>

            <div class="oauth-right">
                @if(!empty($config['is_google_connected']))
                    <a href="{{ route('admin.ads.oauth.redirect') }}" class="btn btn-warning btn-sm px-3 fw-semibold text-dark text-decoration-none">
                        <i class="fa-solid fa-arrows-rotate me-1"></i> Reconnect Google Account
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="disconnectGoogleAccount()">
                        <i class="fa-solid fa-link-slash me-1"></i> Disconnect
                    </button>
                @else
                    <button type="button" class="btn btn-primary btn-sm px-3" onclick="openConnectModal()">
                        <i class="fa-brands fa-google me-1"></i> Connect Google Account
                    </button>
                @endif

                <a href="https://support.google.com/admob/answer/7356219" target="_blank"
                    class="btn btn-outline-secondary btn-sm px-3 text-decoration-none">
                    View Setup Guide <i class="fa-solid fa-arrow-up-right-from-square ms-1 fs-11"></i>
                </a>

                <div class="oauth-security-note">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Authentication uses secure Google OAuth. Passwords are never stored.</span>
                </div>
            </div>
        </div>

        <!-- Main Configuration Form -->
        <form action="{{ route('admin.ads.configuration.save') }}" method="POST" id="admobConfigForm">
            @csrf

            <div class="row g-4">
                <!-- Left Column: Configuration Forms (8 Cols) -->
                <div class="col-lg-8">

                    <!-- 1. Account & Publisher Section -->
                    <div class="config-section-card">
                        <div class="config-section-header">
                            <div class="config-section-num">1</div>
                            <h2 class="config-section-title">Account & Publisher</h2>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="publisher_id" class="form-label fs-12 fw-semibold text-dark">
                                    Publisher ID
                                </label>
                                <input type="text" name="publisher_id" id="publisher_id"
                                    class="form-control form-control-sm fs-12 font-monospace" readonly
                                    placeholder="Connect your Google account to fetch this automatically"
                                    value="{{ $config['publisher_id'] ?? '' }}">
                                <div class="form-text fs-11 text-muted">Fetched automatically from AdMob once you connect
                                    a Google account that has a real AdMob account — this can't be typed manually.</div>
                            </div>

                            <div class="col-md-3">
                                <label for="reporting_currency" class="form-label fs-12 fw-semibold text-dark">
                                    Dashboard Currency
                                </label>
                                <select name="reporting_currency" id="reporting_currency"
                                    class="form-select form-select-sm fs-12">
                                    <option value="INR (₹)" {{ ($config['reporting_currency'] ?? '') === 'INR (₹)' ? 'selected' : '' }}>INR (₹)</option>
                                    <option value="USD ($)" {{ ($config['reporting_currency'] ?? '') === 'USD ($)' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR (€)" {{ ($config['reporting_currency'] ?? '') === 'EUR (€)' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP (£)" {{ ($config['reporting_currency'] ?? '') === 'GBP (£)' ? 'selected' : '' }}>GBP (£)</option>
                                </select>
                                <div class="form-text fs-11 text-muted">Format for dashboard display</div>
                            </div>

                            <div class="col-md-3">
                                <label for="contact_email" class="form-label fs-12 fw-semibold text-dark">
                                    Contact Email
                                </label>
                                <input type="email" name="contact_email" id="contact_email"
                                    class="form-control form-control-sm fs-12" placeholder="e.g. ads@company.com"
                                    value="{{ old('contact_email', $config['contact_email'] ?? '') }}">
                                <div class="form-text fs-11 text-muted">Admin contact address</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Mobile App Registration Section -->
                    <div class="config-section-card">
                        <div class="config-section-header">
                            <div class="config-section-num">2</div>
                            <h2 class="config-section-title">Mobile App Registration</h2>
                        </div>

                        <div class="row g-3">
                            <!-- Android Card -->
                            <div class="col-md-6">
                                <div class="config-platform-card">
                                    <div class="platform-header">
                                        <div class="platform-title-group">
                                            <i class="fa-brands fa-android platform-icon-android"></i>
                                            <span>Android</span>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="android_enabled" value="1"
                                                id="android_enabled" {{ !empty($config['android_enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label fs-12 text-secondary"
                                                for="android_enabled">Enabled</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fs-11 fw-semibold text-dark">Package Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="android_package_name"
                                            class="form-control form-control-sm fs-12 bg-white"
                                            placeholder="e.g. com.company.gpscamera"
                                            value="{{ old('android_package_name', $config['android_package_name'] ?? '') }}">
                                    </div>

                                    <div>
                                        <label class="form-label fs-11 fw-semibold text-dark">AdMob App ID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="android_app_id"
                                            class="form-control form-control-sm fs-12 bg-white font-monospace"
                                            placeholder="ca-app-pub-XXXXXXXXXXXXXXXX~XXXXXXXXXX"
                                            value="{{ old('android_app_id', $config['android_app_id'] ?? '') }}">
                                        <div class="form-text fs-11 text-muted">Created inside Google AdMob console &gt;
                                            Apps &gt; App Settings</div>
                                    </div>
                                </div>
                            </div>

                            <!-- iOS Card -->
                            <div class="col-md-6">
                                <div class="config-platform-card">
                                    <div class="platform-header">
                                        <div class="platform-title-group">
                                            <i class="fa-brands fa-apple platform-icon-ios"></i>
                                            <span>iOS</span>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="ios_enabled" value="1"
                                                id="ios_enabled" {{ !empty($config['ios_enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label fs-12 text-secondary"
                                                for="ios_enabled">Enabled</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fs-11 fw-semibold text-dark">Bundle ID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="ios_bundle_id"
                                            class="form-control form-control-sm fs-12 bg-white"
                                            placeholder="e.g. com.company.gpscamera"
                                            value="{{ old('ios_bundle_id', $config['ios_bundle_id'] ?? '') }}">
                                    </div>

                                    <div>
                                        <label class="form-label fs-11 fw-semibold text-dark">AdMob App ID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="ios_app_id"
                                            class="form-control form-control-sm fs-12 bg-white font-monospace"
                                            placeholder="ca-app-pub-XXXXXXXXXXXXXXXX~XXXXXXXXXX"
                                            value="{{ old('ios_app_id', $config['ios_app_id'] ?? '') }}">
                                        <div class="form-text fs-11 text-muted">Created inside Google AdMob console &gt;
                                            Apps &gt; App Settings</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Reporting & Synchronization Section (Backend Sync) -->
                    <div class="config-section-card">
                        <div class="config-section-header">
                            <div class="config-section-num">3</div>
                            <h2 class="config-section-title">Backend Reporting & Synchronization</h2>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <label class="form-label fs-12 fw-semibold text-dark mb-0">Automatic Metrics
                                        Sync</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="enable_reporting" value="1"
                                            id="enable_reporting" {{ !empty($config['enable_reporting']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted fs-11">Reporting API Status:</span>
                                    <span
                                        class="badge {{ ($config['connection_status'] ?? '') === 'Connected' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }}"
                                        id="apiStatusBadge">
                                        {{ $config['connection_status'] ?? 'Pending Connection' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-semibold text-dark">Sync Schedule Frequency</label>
                                <select name="sync_frequency" class="form-select form-select-sm fs-12">
                                    <option value="Every 6 hours" {{ ($config['sync_frequency'] ?? '') === 'Every 6 hours' ? 'selected' : '' }}>Every 6 hours</option>
                                    <option value="Every 12 hours" {{ ($config['sync_frequency'] ?? '') === 'Every 12 hours' ? 'selected' : '' }}>Every 12 hours</option>
                                    <option value="Daily" {{ ($config['sync_frequency'] ?? '') === 'Daily' ? 'selected' : '' }}>Daily at midnight</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-semibold text-dark">Default Dashboard Range</label>
                                <select name="default_report_range" class="form-select form-select-sm fs-12">
                                    <option value="Last 7 days" {{ ($config['default_report_range'] ?? '') === 'Last 7 days' ? 'selected' : '' }}>Last 7 days</option>
                                    <option value="Last 30 days" {{ ($config['default_report_range'] ?? '') === 'Last 30 days' ? 'selected' : '' }}>Last 30 days</option>
                                    <option value="This Month" {{ ($config['default_report_range'] ?? '') === 'This Month' ? 'selected' : '' }}>This Month</option>
                                </select>
                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label fs-12 fw-semibold text-dark">Last Synced Timestamp</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="text" class="form-control form-control-sm fs-12 bg-light text-muted"
                                        id="lastSyncInput" value="{{ $config['last_sync'] ?? 'Not synced' }}" readonly>
                                    <button type="button" class="btn btn-outline-primary btn-sm text-nowrap"
                                        id="testSyncBtn" onclick="testReportingSync()">
                                        <i class="fa-solid fa-bolt me-1"></i> Verify & Test Connection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Test Device Mode Section -->
                    <div class="config-section-card">
                        <div class="config-section-header">
                            <div class="config-section-num">4</div>
                            <h2 class="config-section-title">Test Device Mode & QA</h2>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <label class="toggle-label-wrap" for="enable_test_ads">Enable Test Ads Mode</label>
                                        <div class="toggle-desc">Serve Google test ads on registered QA devices to prevent
                                            invalid click penalties</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="enable_test_ads" value="1"
                                            id="enable_test_ads" {{ !empty($config['enable_test_ads']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-semibold text-dark">Registered Test Device IDs</label>
                                <input type="text" name="default_test_device_ids"
                                    class="form-control form-control-sm fs-12 font-monospace"
                                    placeholder="e.g. TEST-DEVICE-12345, 33BE2250B43518CCDA7DE426D04EE231"
                                    value="{{ old('default_test_device_ids', $config['default_test_device_ids'] ?? '') }}">
                                <div class="form-text fs-11 text-muted">Comma-separated Android Advertising IDs or iOS test
                                    device hashes.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary & Checklist (4 Cols) -->
                <div class="col-lg-4">

                    <!-- 1. Configuration Summary Card -->
                    <div class="config-summary-card">
                        <h3 class="card-heading">Configuration Summary</h3>

                        <div class="summary-row">
                            <span class="summary-label">Google Account:</span>
                            <span
                                class="badge {{ $summary['account_status'] === 'Connected' ? 'bg-success' : 'bg-danger-subtle text-danger border border-danger-subtle' }}"
                                id="summaryAccountBadge">
                                {{ $summary['account_status'] }}
                            </span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Publisher ID:</span>
                            <span
                                class="badge {{ $summary['publisher_id_status'] === 'Valid' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $summary['publisher_id_status'] }}
                            </span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Android App:</span>
                            <span
                                class="badge {{ $summary['android_app_status'] === 'Configured' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }}">
                                {{ $summary['android_app_status'] }}
                            </span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">iOS App:</span>
                            <span
                                class="badge {{ $summary['ios_app_status'] === 'Configured' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }}">
                                {{ $summary['ios_app_status'] }}
                            </span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Reporting API:</span>
                            <span
                                class="badge {{ $summary['reporting_api_status'] === 'Active Connection' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }}"
                                id="summaryApiBadge">
                                {{ $summary['reporting_api_status'] }}
                            </span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Test Mode:</span>
                            <span
                                class="badge {{ $summary['test_mode_status'] === 'Enabled' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $summary['test_mode_status'] }}
                            </span>
                        </div>
                    </div>

                    <!-- 2. Setup Checklist Card -->
                    <div class="setup-checklist-card">
                        <h3 class="card-heading">Setup Checklist</h3>

                        <div class="checklist-item {{ $checklist['google_connected'] ? 'completed' : '' }}" id="chkGoogle">
                            <i
                                class="fa-solid fa-circle-check check-icon {{ $checklist['google_connected'] ? 'checked' : '' }}"></i>
                            <span>Google account connected</span>
                        </div>

                        <div class="checklist-item {{ $checklist['publisher_id_added'] ? 'completed' : '' }}"
                            id="chkPublisher">
                            <i
                                class="fa-solid fa-circle-check check-icon {{ $checklist['publisher_id_added'] ? 'checked' : '' }}"></i>
                            <span>Publisher ID added & verified</span>
                        </div>

                        <div class="checklist-item {{ $checklist['android_app_configured'] ? 'completed' : '' }}"
                            id="chkAndroid">
                            <i
                                class="fa-solid fa-circle-check check-icon {{ $checklist['android_app_configured'] ? 'checked' : '' }}"></i>
                            <span>Android App ID configured</span>
                        </div>

                        <div class="checklist-item {{ $checklist['ios_app_configured'] ? 'completed' : '' }}" id="chkIos">
                            <i
                                class="fa-solid fa-circle-check check-icon {{ $checklist['ios_app_configured'] ? 'checked' : '' }}"></i>
                            <span>iOS App ID configured</span>
                        </div>

                        <div class="checklist-item {{ $checklist['test_ad_mode'] ? 'completed' : '' }}" id="chkTest">
                            <i
                                class="fa-solid fa-circle-check check-icon {{ $checklist['test_ad_mode'] ? 'checked' : '' }}"></i>
                            <span>Test ad mode enabled</span>
                        </div>

                        <div class="checklist-item {{ $checklist['reporting_sync_verified'] ? 'completed' : '' }}"
                            id="chkSync">
                            <i
                                class="fa-solid fa-circle-check check-icon {{ $checklist['reporting_sync_verified'] ? 'checked' : '' }}"></i>
                            <span>Reporting sync verified</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Bottom Form Action Bar -->
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white mt-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('admin.ads.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="testReportingSync()">
                            <i class="fa-solid fa-circle-check me-1"></i> Validate Configuration
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Configuration
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal: Connect Google Account Options -->
    <div class="modal fade" id="connectGoogleModal" tabindex="-1" aria-labelledby="connectGoogleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-brands fa-google text-primary fs-18"></i>
                        <h5 class="modal-title fs-15 fw-bold" id="connectGoogleModalLabel">Connect Google AdMob Account</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="fs-13 text-secondary mb-3">
                        You'll be redirected to Google to sign in and authorize access to your AdMob Reporting API data. No
                        passwords are entered here.
                    </p>

                    <div class="d-grid gap-2 mb-2">
                        <a href="{{ route('admin.ads.oauth.redirect') }}"
                            class="btn btn-primary d-flex align-items-center justify-content-center gap-2 py-2">
                            <i class="fa-brands fa-google"></i>
                            <span>Sign in with Google</span>
                        </a>
                    </div>
                    <div class="form-text fs-11 text-muted">
                        This requires a Google Cloud OAuth Client ID/Secret to be configured on the server. If it isn't set
                        up yet, you'll see an error explaining what's missing.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript helpers -->
    <script>
        function openConnectModal() {
            const modal = new bootstrap.Modal(document.getElementById('connectGoogleModal'));
            modal.show();
        }

        function disconnectGoogleAccount() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Disconnect Account?',
                    text: 'Are you sure you want to disconnect this Google AdMob account?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, disconnect',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performDisconnect();
                    }
                });
            } else if (confirm('Are you sure you want to disconnect this Google account?')) {
                performDisconnect();
            }
        }

        function performDisconnect() {
            fetch("{{ route('admin.ads.disconnect-google') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Disconnected',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        window.location.reload();
                    }
                })
                .catch(err => {
                    alert('Disconnect failed: ' + err.message);
                });
        }

        function testReportingSync() {
            const btn = document.getElementById('testSyncBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Testing...';
            }

            const pubInput = document.getElementById('publisher_id');
            const androidInput = document.getElementById('android_app_id');
            const iosInput = document.getElementById('ios_app_id');
            const androidEnabled = document.getElementById('android_enabled');
            const iosEnabled = document.getElementById('ios_enabled');

            fetch("{{ route('admin.ads.test-connection') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    publisher_id: pubInput ? pubInput.value : '',
                    android_app_id: androidInput ? androidInput.value : '',
                    ios_app_id: iosInput ? iosInput.value : '',
                    android_enabled: androidEnabled ? (androidEnabled.checked ? 1 : 0) : 0,
                    ios_enabled: iosEnabled ? (iosEnabled.checked ? 1 : 0) : 0
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-bolt me-1"></i> Verify & Test Connection';
                    }

                    if (data.success) {
                        if (data.publisher_id && pubInput) {
                            pubInput.value = data.publisher_id;
                        }
                        if (data.last_sync) {
                            const lastSyncEl = document.getElementById('lastSyncInput');
                            if (lastSyncEl) lastSyncEl.value = data.last_sync;
                        }

                        const badge = document.getElementById('apiStatusBadge');
                        if (badge) {
                            badge.className = 'badge bg-success-subtle text-success border border-success-subtle';
                            badge.textContent = 'Connected';
                        }
                        const summaryBadge = document.getElementById('summaryApiBadge');
                        if (summaryBadge) {
                            summaryBadge.className = 'badge bg-success-subtle text-success border border-success-subtle';
                            summaryBadge.textContent = 'Active Connection';
                        }

                        // Also update summary Publisher ID badge
                        const summaryRows = document.querySelectorAll('.config-summary-card .summary-row');
                        summaryRows.forEach(row => {
                            const label = row.querySelector('.summary-label');
                            if (label && label.textContent.includes('Publisher ID:')) {
                                const badgeSpan = row.querySelector('.badge');
                                if (badgeSpan) {
                                    badgeSpan.className = 'badge bg-success-subtle text-success border border-success-subtle';
                                    badgeSpan.textContent = 'Valid';
                                }
                            }
                        });

                        // Update checklist
                        const chkPublisher = document.getElementById('chkPublisher');
                        if (chkPublisher) {
                            chkPublisher.classList.add('completed');
                            const icon = chkPublisher.querySelector('.check-icon');
                            if (icon) icon.classList.add('checked');
                        }
                        const chkSync = document.getElementById('chkSync');
                        if (chkSync) {
                            chkSync.classList.add('completed');
                            const icon = chkSync.querySelector('.check-icon');
                            if (icon) icon.classList.add('checked');
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 3500
                            });
                        } else {
                            alert(data.message);
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Connection Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#0d6efd'
                            });
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(err => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-bolt me-1"></i> Verify & Test Connection';
                    }
                    alert('Failed to test connection: ' + err.message);
                });
        }
    </script>
@endsection