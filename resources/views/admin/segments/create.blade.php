@extends('layout')

@section('title', 'Create Audience Segment - GeoCam Admin')
@section('page_title', 'Create Segment')

@section('breadcrumbs')
    <a href="{{ route('admin.segments.index') }}" class="text-decoration-none text-muted">Engagement</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.segments.index') }}" class="text-decoration-none text-muted">Audience Segments</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Create Segment</span>
@endsection

@section('content')
<div class="segments-page">
    <form id="segmentCreateForm" action="{{ route('admin.segments.store') }}" method="POST">
        @csrf

        {{-- Top Breadcrumbs & Header Actions --}}
        <div class="page-header-row mb-4">
            <div class="header-title-group">
                <h1 class="page-main-title">Create Audience Segment</h1>
                <p class="page-main-subtitle">Build a reusable audience using anonymous device and engagement criteria</p>
            </div>
            <div class="header-actions-group">
                <a href="{{ route('admin.segments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" name="status" value="draft" class="btn btn-outline-primary">Save as Draft</button>
                <button type="submit" name="status" value="active" class="btn btn-primary">Create Segment</button>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left Column: 4 Builder Steps --}}
            <div class="col-lg-8">
                {{-- STEP 1: Segment Details --}}
                <div class="builder-section-card">
                    <div class="section-header">
                        <span class="step-number">1</span>
                        <h2 class="section-title">Segment Details</h2>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-medium text-gray-700 mb-0">Segment Name <span class="text-danger">*</span></label>
                                <span class="text-muted fs-xs" id="nameCharCount">23 / 80</span>
                            </div>
                            <input type="text" name="name" id="segmentNameInput" class="form-control form-control-sm" placeholder="e.g. Tirunelveli Active Users" value="Tirunelveli Active Users" maxlength="80" required oninput="document.getElementById('nameCharCount').innerText = this.value.length + ' / 80'; updateChecklist();">
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-medium text-gray-700 mb-0">Description <span class="text-muted fs-xs">(Optional)</span></label>
                                <span class="text-muted fs-xs" id="descCharCount">73 / 200</span>
                            </div>
                            <textarea name="description" id="segmentDescInput" class="form-control form-control-sm" rows="2" maxlength="200" placeholder="Describe the targeting intent..." oninput="document.getElementById('descCharCount').innerText = this.value.length + ' / 200';">Active GPS Camera users in Tirunelveli with notifications enabled.</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-medium text-gray-700 mb-2">Segment Type</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="segment-type-card selected" id="typeDynamicLabel">
                                        <input type="radio" name="type" value="dynamic" checked class="form-check-input mt-1" onchange="selectSegmentType('dynamic')">
                                        <div>
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="fa-solid fa-arrows-rotate text-primary"></i>
                                                <span class="type-title">Dynamic</span>
                                            </div>
                                            <p class="type-desc">Updates automatically when devices match criteria</p>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="segment-type-card" id="typeStaticLabel">
                                        <input type="radio" name="type" value="static" class="form-check-input mt-1" onchange="selectSegmentType('static')">
                                        <div>
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="fa-solid fa-lock text-secondary"></i>
                                                <span class="type-title">Static</span>
                                            </div>
                                            <p class="type-desc">Keeps the current device list until manually changed</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Audience Rules --}}
                <div class="builder-section-card">
                    <div class="section-header justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="step-number">2</span>
                            <h2 class="section-title">Audience Rules</h2>
                        </div>
                        <button type="button" class="btn btn-link text-primary fs-xs text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#rulesDictionaryModal">
                            <i class="fa-solid fa-circle-question me-1"></i> Rule Dictionary & Operators
                        </button>
                    </div>

                    <div id="ruleGroupsContainer">
                        {{-- Rule Group 1 --}}
                        <div class="rule-group-card" id="ruleGroup1">
                            <div class="rule-group-header">
                                <span class="group-title">Rule Group 1</span>
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="rule_groups[0][match]" id="group1MatchAll" value="ALL" checked onchange="triggerLiveEstimate()">
                                    <label class="btn btn-outline-primary px-3" for="group1MatchAll">ALL</label>
                                    <input type="radio" class="btn-check" name="rule_groups[0][match]" id="group1MatchAny" value="ANY" onchange="triggerLiveEstimate()">
                                    <label class="btn btn-outline-primary px-3" for="group1MatchAny">ANY</label>
                                </div>
                            </div>

                            <div class="rule-rows-list" id="group1Rows">
                                {{-- Row 1: Country --}}
                                <div class="rule-row">
                                    <span class="rule-index">1</span>
                                    <select name="rule_groups[0][rules][0][attribute]" class="form-select form-select-sm" style="flex: 1.5;" onchange="updateRuleOperators(this); triggerLiveEstimate();">
                                        <optgroup label="Device & Location">
                                            <option value="Country" selected>Country</option>
                                            <option value="State / Region">State / Region</option>
                                            <option value="City">City</option>
                                            <option value="Platform">Platform</option>
                                            <option value="Manufacturer">Manufacturer</option>
                                            <option value="Device Model">Device Model</option>
                                        </optgroup>
                                        <optgroup label="App Activity">
                                            <option value="Activity Status">Activity Status</option>
                                            <option value="Last Active">Last Active</option>
                                            <option value="App Version">App Version</option>
                                            <option value="Photos Captured">Photos Captured</option>
                                        </optgroup>
                                        <optgroup label="Permissions">
                                            <option value="Notification Permission">Notification Permission</option>
                                            <option value="Location Permission">Location Permission</option>
                                            <option value="Camera Permission">Camera Permission</option>
                                        </optgroup>
                                    </select>
                                    <select name="rule_groups[0][rules][0][operator]" class="form-select form-select-sm" style="flex: 1;" onchange="triggerLiveEstimate()">
                                        <option value="is" selected>is</option>
                                        <option value="is not">is not</option>
                                        <option value="is any of">is any of</option>
                                    </select>
                                    <input type="text" name="rule_groups[0][rules][0][value]" class="form-control form-control-sm" style="flex: 1.5;" value="{{ $prefill['country'] ?: 'India' }}" oninput="triggerLiveEstimate()">
                                    <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)"><i class="fa-regular fa-trash-can"></i></button>
                                </div>

                                {{-- Row 2: State --}}
                                <div class="rule-row">
                                    <span class="rule-index">2</span>
                                    <select name="rule_groups[0][rules][1][attribute]" class="form-select form-select-sm" style="flex: 1.5;" onchange="updateRuleOperators(this); triggerLiveEstimate();">
                                        <option value="State / Region" selected>State / Region</option>
                                        <option value="Country">Country</option>
                                        <option value="City">City</option>
                                    </select>
                                    <select name="rule_groups[0][rules][1][operator]" class="form-select form-select-sm" style="flex: 1;" onchange="triggerLiveEstimate()">
                                        <option value="is" selected>is</option>
                                        <option value="is not">is not</option>
                                    </select>
                                    <input type="text" name="rule_groups[0][rules][1][value]" class="form-control form-control-sm" style="flex: 1.5;" value="{{ $prefill['state'] ?: 'Tamil Nadu' }}" oninput="triggerLiveEstimate()">
                                    <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)"><i class="fa-regular fa-trash-can"></i></button>
                                </div>

                                {{-- Row 3: City --}}
                                <div class="rule-row">
                                    <span class="rule-index">3</span>
                                    <select name="rule_groups[0][rules][2][attribute]" class="form-select form-select-sm" style="flex: 1.5;" onchange="updateRuleOperators(this); triggerLiveEstimate();">
                                        <option value="City" selected>City</option>
                                        <option value="Country">Country</option>
                                        <option value="State / Region">State / Region</option>
                                    </select>
                                    <select name="rule_groups[0][rules][2][operator]" class="form-select form-select-sm" style="flex: 1;" onchange="triggerLiveEstimate()">
                                        <option value="is" selected>is</option>
                                        <option value="is not">is not</option>
                                    </select>
                                    <input type="text" name="rule_groups[0][rules][2][value]" class="form-control form-control-sm" style="flex: 1.5;" value="{{ $prefill['city'] ?: 'Tirunelveli' }}" oninput="triggerLiveEstimate()">
                                    <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)"><i class="fa-regular fa-trash-can"></i></button>
                                </div>

                                {{-- Row 4: Activity Status --}}
                                <div class="rule-row">
                                    <span class="rule-index">4</span>
                                    <select name="rule_groups[0][rules][3][attribute]" class="form-select form-select-sm" style="flex: 1.5;" onchange="updateRuleOperators(this); triggerLiveEstimate();">
                                        <option value="Activity Status" selected>Activity Status</option>
                                        <option value="City">City</option>
                                    </select>
                                    <select name="rule_groups[0][rules][3][operator]" class="form-select form-select-sm" style="flex: 1;" onchange="triggerLiveEstimate()">
                                        <option value="is" selected>is</option>
                                        <option value="is not">is not</option>
                                    </select>
                                    <select name="rule_groups[0][rules][3][value]" class="form-select form-select-sm" style="flex: 1.5;" onchange="triggerLiveEstimate()">
                                        <option value="Active" selected>Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                    <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)"><i class="fa-regular fa-trash-can"></i></button>
                                </div>

                                {{-- Row 5: Last Active --}}
                                <div class="rule-row">
                                    <span class="rule-index">5</span>
                                    <select name="rule_groups[0][rules][4][attribute]" class="form-select form-select-sm" style="flex: 1.5;" onchange="updateRuleOperators(this); triggerLiveEstimate();">
                                        <option value="Last Active" selected>Last Active</option>
                                        <option value="First Open">First Open</option>
                                    </select>
                                    <select name="rule_groups[0][rules][4][operator]" class="form-select form-select-sm" style="flex: 1;" onchange="triggerLiveEstimate()">
                                        <option value="within" selected>within</option>
                                        <option value="not within">not within</option>
                                        <option value="before">before</option>
                                        <option value="after">after</option>
                                    </select>
                                    <select name="rule_groups[0][rules][4][value]" class="form-select form-select-sm" style="flex: 1.5;" onchange="triggerLiveEstimate()">
                                        <option value="7 days">7 days</option>
                                        <option value="30 days" selected>30 days</option>
                                        <option value="60 days">60 days</option>
                                        <option value="90 days">90 days</option>
                                    </select>
                                    <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)"><i class="fa-regular fa-trash-can"></i></button>
                                </div>

                                {{-- Row 6: Notification Permission --}}
                                <div class="rule-row">
                                    <span class="rule-index">6</span>
                                    <select name="rule_groups[0][rules][5][attribute]" class="form-select form-select-sm" style="flex: 1.5;" onchange="updateRuleOperators(this); triggerLiveEstimate();">
                                        <option value="Notification Permission" selected>Notification Permission</option>
                                        <option value="Location Permission">Location Permission</option>
                                        <option value="Camera Permission">Camera Permission</option>
                                    </select>
                                    <select name="rule_groups[0][rules][5][operator]" class="form-select form-select-sm" style="flex: 1;" onchange="triggerLiveEstimate()">
                                        <option value="is" selected>is</option>
                                        <option value="is not">is not</option>
                                    </select>
                                    <select name="rule_groups[0][rules][5][value]" class="form-select form-select-sm" style="flex: 1.5;" onchange="triggerLiveEstimate()">
                                        <option value="Enabled" selected>Enabled</option>
                                        <option value="Disabled">Disabled</option>
                                    </select>
                                    <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)"><i class="fa-regular fa-trash-can"></i></button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addRuleRow(0)">
                                <i class="fa-solid fa-plus me-1"></i> Add Rule
                            </button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mt-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addRuleGroup()">
                            <i class="fa-solid fa-plus me-1"></i> Add Rule Group
                        </button>
                    </div>
                </div>

                {{-- STEP 3: Platform & App Filters --}}
                <div class="builder-section-card">
                    <div class="section-header">
                        <span class="step-number">3</span>
                        <h2 class="section-title">Platform & App Filters</h2>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-medium text-gray-700 mb-2">Platforms</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="platform_filters[platforms][]" value="Android" id="platAndroid" checked onchange="triggerLiveEstimate()">
                                    <label class="form-check-label fs-sm" for="platAndroid"><i class="fa-brands fa-android text-success me-1"></i> Android</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="platform_filters[platforms][]" value="iOS" id="platiOS" checked onchange="triggerLiveEstimate()">
                                    <label class="form-check-label fs-sm" for="platiOS"><i class="fa-brands fa-apple text-dark me-1"></i> iOS</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-medium text-gray-700 mb-1">App Version</label>
                            <select name="platform_filters[app_version]" class="form-select form-select-sm" onchange="triggerLiveEstimate()">
                                <option value="All Versions" selected>All Versions</option>
                                <option value="v1.4.2">v1.4.2 (Latest)</option>
                                <option value="v1.4.1">v1.4.1</option>
                                <option value="v1.4.0">v1.4.0</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-medium text-gray-700 mb-1">Location Permission</label>
                            <select name="platform_filters[location_permission]" class="form-select form-select-sm" onchange="triggerLiveEstimate()">
                                <option value="Any" selected>Any</option>
                                <option value="Precise">Precise</option>
                                <option value="Approximate">Approximate</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-medium text-gray-700 mb-1">Analytics Consent</label>
                            <select name="platform_filters[analytics_consent]" class="form-select form-select-sm" onchange="triggerLiveEstimate()">
                                <option value="Any" selected>Any</option>
                                <option value="Opted In">Opted In</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Exclusions --}}
                <div class="builder-section-card">
                    <div class="section-header">
                        <span class="step-number">4</span>
                        <h2 class="section-title">Exclusions</h2>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="exclusions[exclude_inactive]" value="1" id="exclInactive" checked onchange="triggerLiveEstimate()">
                                <label class="form-check-label fs-sm" for="exclInactive">Exclude inactive devices</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="exclusions[exclude_invalid_fcm]" value="1" id="exclFcm" checked onchange="triggerLiveEstimate()">
                                <label class="form-check-label fs-sm" for="exclFcm">Exclude invalid FCM tokens</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="exclusions[exclude_notification_denied]" value="1" id="exclDenied" checked onchange="triggerLiveEstimate()">
                                <label class="form-check-label fs-sm" for="exclDenied">Exclude notification denied</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="exclusions[exclude_crash_prone]" value="1" id="exclCrash" onchange="triggerLiveEstimate()">
                                <label class="form-check-label fs-sm" for="exclCrash">Exclude crash-prone app versions</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Live Sidebar Estimator & Checklist --}}
            <div class="col-lg-4">
                <div class="audience-estimate-card position-sticky" style="top: 80px;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h6 fw-bold text-gray-900 mb-0">Estimated Audience</h3>
                        <span id="estimatingBadge" class="badge bg-light text-muted fs-xs fw-normal d-none">
                            <i class="fa-solid fa-rotate fa-spin me-1 text-primary"></i> Updating...
                        </span>
                    </div>

                    <div class="row g-2 text-center mb-3">
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <div class="stat-hero-number" id="statEligible">{{ number_format($initialEstimate['eligible_devices']) }}</div>
                                <div class="text-muted fs-xs">Eligible Devices</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <div class="stat-sub-number deliverable" id="statDeliverable">{{ number_format($initialEstimate['deliverable_devices']) }}</div>
                                <div class="text-muted fs-xs">Deliverable</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <div class="stat-sub-number excluded" id="statExcluded">{{ number_format($initialEstimate['excluded_devices']) }}</div>
                                <div class="text-muted fs-xs">Excluded</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-4 py-2 border-top border-bottom my-3">
                        <div style="width: 70px; height: 70px; border-radius: 50%; background: conic-gradient(#3b82f6 0% 91%, #cbd5e1 91% 100%);" id="platformDonut"></div>
                        <div class="fs-xs">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: #3b82f6;"></span>
                                <span class="text-muted">Android</span>
                                <strong id="pctAndroid">{{ $initialEstimate['android_pct'] }}%</strong>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: #cbd5e1;"></span>
                                <span class="text-muted">iOS</span>
                                <strong id="pctiOS">{{ $initialEstimate['ios_pct'] }}%</strong>
                            </div>
                        </div>
                    </div>

                    <div class="fs-xs d-flex flex-column gap-2 mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Anonymous Users</span>
                            <strong id="statAnon">{{ number_format($initialEstimate['anonymous_users']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Active Devices</span>
                            <strong id="statActive">{{ number_format($initialEstimate['active_devices']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Notifications Enabled</span>
                            <strong id="statNotif">{{ number_format($initialEstimate['notifications_enabled']) }}</strong>
                        </div>
                    </div>

                    <div class="border-top pt-3 mb-3">
                        <h4 class="h6 fw-bold text-gray-800 fs-xs text-uppercase mb-2">Criteria Summary</h4>
                        <div class="d-flex flex-wrap gap-1" id="criteriaSummaryContainer">
                            <span class="criteria-pill">India</span>
                            <span class="criteria-pill">Tamil Nadu</span>
                            <span class="criteria-pill">Tirunelveli</span>
                            <span class="criteria-pill">Active</span>
                            <span class="criteria-pill">Last 30 Days</span>
                            <span class="criteria-pill">Notifications Enabled</span>
                            <span class="criteria-pill">Android + iOS</span>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <h4 class="h6 fw-bold text-gray-800 fs-xs text-uppercase mb-2">Segment Checklist</h4>
                        <div class="d-flex flex-column gap-1">
                            <div class="segment-checklist-item" id="checkName">
                                <i class="fa-solid fa-circle-check check-icon valid"></i>
                                <span>Name added</span>
                            </div>
                            <div class="segment-checklist-item" id="checkType">
                                <i class="fa-solid fa-circle-check check-icon valid"></i>
                                <span>Dynamic type selected</span>
                            </div>
                            <div class="segment-checklist-item" id="checkRules">
                                <i class="fa-solid fa-circle-check check-icon valid"></i>
                                <span>Rules properly configured</span>
                            </div>
                            <div class="segment-checklist-item" id="checkAudience">
                                <i class="fa-solid fa-circle-check check-icon valid"></i>
                                <span>Audience is deliverable</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between">
                <div class="text-muted fs-xs mb-2 mb-md-0">
                    <i class="fa-solid fa-check text-success me-1"></i> <span id="estimateStatusText">Estimated audience updated just now</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.segments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" name="status" value="draft" class="btn btn-outline-primary">Save as Draft</button>
                    <button type="submit" name="status" value="active" class="btn btn-primary">Create Segment</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
