@extends('layout')

@section('title', 'Edit Audience Segment - GeoCam Admin')
@section('page_title', 'Edit Segment')

@section('breadcrumbs')
    <a href="{{ route('admin.segments.index') }}" class="text-decoration-none text-muted">Engagement</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.segments.index') }}" class="text-decoration-none text-muted">Audience Segments</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.segments.show', $segment->id) }}" class="text-decoration-none text-muted">{{ $segment->name }}</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Edit</span>
@endsection

@section('content')
<div class="segments-page">
    <form id="segmentEditForm" action="{{ route('admin.segments.update', $segment->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Top Breadcrumbs & Header Actions --}}
        <div class="page-header-row mb-4">
            <div class="header-title-group">
                <h1 class="page-main-title">Edit Audience Segment</h1>
                <p class="page-main-subtitle">Update saved audience criteria and delivery filters</p>
            </div>
            <div class="header-actions-group">
                <a href="{{ route('admin.segments.show', $segment->id) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" name="status" value="draft" class="btn btn-outline-primary">Save as Draft</button>
                <button type="submit" name="status" value="{{ $segment->status === 'draft' ? 'active' : $segment->status }}" class="btn btn-primary">Save Changes</button>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left Column: 4 Builder Steps --}}
            <div class="col-lg-8">
                {{-- STEP 1: Segment Details --}}
                <div class="builder-section-card">
                    <div class="section-header collapsible-trigger d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#collapseStep1" aria-expanded="true" aria-controls="collapseStep1">
                        <div class="d-flex align-items-center">
                            <span class="step-number">1</span>
                            <h2 class="section-title">Segment Details</h2>
                        </div>
                        <i class="fa-solid fa-chevron-up section-collapse-chevron"></i>
                    </div>

                    <div class="collapse show pt-3" id="collapseStep1">
                        <div class="row g-3">
                            {{-- Col 6: Segment Name --}}
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-medium text-gray-700 mb-0" for="segmentNameInput">Segment Name <span class="text-danger">*</span></label>
                                    <span class="text-muted fs-xs" id="nameCharCount">{{ strlen($segment->name) }} / 80</span>
                                </div>
                                <input type="text" name="name" id="segmentNameInput" class="form-control form-control-sm" value="{{ $segment->name }}" maxlength="80" required oninput="document.getElementById('nameCharCount').innerText = this.value.length + ' / 80'; updateChecklist(); triggerLiveEstimate();">
                            </div>

                            {{-- Col 6: Description (Side-by-side with Segment Name) --}}
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-medium text-gray-700 mb-0" for="segmentDescInput">Description <span class="text-muted fs-xs">(Optional)</span></label>
                                    <span class="text-muted fs-xs" id="descCharCount">{{ strlen($segment->description ?? '') }} / 200</span>
                                </div>
                                <textarea name="description" id="segmentDescInput" class="form-control form-control-sm" rows="1" maxlength="200" placeholder="Describe the targeting intent..." oninput="document.getElementById('descCharCount').innerText = this.value.length + ' / 200';">{{ $segment->description }}</textarea>
                            </div>

                            {{-- Segment Type Radio Selection --}}
                            <div class="col-md-12">
                                <label class="form-label fw-medium text-gray-700 mb-2">Segment Type</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="segment-type-card {{ $segment->type === 'dynamic' ? 'selected' : '' }}" id="typeDynamicLabel" onclick="selectSegmentType('dynamic')">
                                            <input type="radio" name="type" value="dynamic" {{ $segment->type === 'dynamic' ? 'checked' : '' }} class="form-check-input mt-1" onchange="selectSegmentType('dynamic')">
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
                                        <label class="segment-type-card {{ $segment->type === 'static' ? 'selected' : '' }}" id="typeStaticLabel" onclick="selectSegmentType('static')">
                                            <input type="radio" name="type" value="static" {{ $segment->type === 'static' ? 'checked' : '' }} class="form-check-input mt-1" onchange="selectSegmentType('static')">
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
                </div>

                {{-- STEP 2: Audience Rules --}}
                <div class="builder-section-card">
                    <div class="section-header collapsible-trigger d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#collapseStep2" aria-expanded="true" aria-controls="collapseStep2">
                        <div class="d-flex align-items-center">
                            <span class="step-number">2</span>
                            <h2 class="section-title">Audience Rules</h2>
                            <span class="badge bg-light text-secondary ms-2 fs-xs fw-normal" id="rulesCountBadge">Rules</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-link text-primary fs-xs text-decoration-none p-0 me-2" data-bs-toggle="modal" data-bs-target="#rulesDictionaryModal" onclick="event.stopPropagation()">
                                <i class="fa-solid fa-circle-question me-1"></i> Rule Dictionary & Operators
                            </button>
                            <i class="fa-solid fa-chevron-up section-collapse-chevron"></i>
                        </div>
                    </div>

                    <div class="collapse show pt-3" id="collapseStep2">
                        <div id="ruleGroupsContainer">
                            @php
                                $groups = $segment->rule_groups ?? [
                                    ['match' => 'ALL', 'rules' => [['attribute' => 'City', 'operator' => 'is', 'value' => 'Tirunelveli']]]
                                ];
                            @endphp

                            @foreach($groups as $gIdx => $group)
                                @if($gIdx > 0)
                                    <div class="rule-group-connector"><span class="connector-badge">AND</span></div>
                                @endif
                                <div class="rule-group-card" id="ruleGroup{{ $gIdx + 1 }}">
                                    <div class="rule-group-header">
                                        <span class="group-title">Rule Group {{ $gIdx + 1 }}</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fs-xs text-muted">Match</span>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <input type="radio" class="btn-check" name="rule_groups[{{ $gIdx }}][match]" id="group{{ $gIdx + 1 }}MatchAll" value="ALL" {{ ($group['match'] ?? 'ALL') === 'ALL' ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                                <label class="btn btn-outline-primary px-3" for="group{{ $gIdx + 1 }}MatchAll">ALL</label>
                                                <input type="radio" class="btn-check" name="rule_groups[{{ $gIdx }}][match]" id="group{{ $gIdx + 1 }}MatchAny" value="ANY" {{ ($group['match'] ?? 'ALL') === 'ANY' ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                                <label class="btn btn-outline-primary px-3" for="group{{ $gIdx + 1 }}MatchAny">ANY</label>
                                            </div>
                                            <span class="fs-xs text-muted">of the following rules</span>
                                            @if($gIdx > 0)
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="this.closest('.rule-group-card').remove(); renumberRuleRows(); triggerLiveEstimate();">Remove Group</button>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="rule-rows-list" id="group{{ $gIdx + 1 }}Rows">
                                        @foreach($group['rules'] ?? [] as $rIdx => $rule)
                                            @php
                                                $curAttr = $rule['attribute'] ?? 'Country';
                                                $curOp   = $rule['operator'] ?? 'is';
                                                $curVal  = is_array($rule['value'] ?? '') ? implode(', ', $rule['value']) : ($rule['value'] ?? '');
                                                $opts    = $ruleOptions[$curAttr] ?? null;
                                            @endphp
                                            <div class="rule-row" data-row-index="{{ $rIdx }}">
                                                <span class="rule-index">{{ $rIdx + 1 }}</span>
                                                <select name="rule_groups[{{ $gIdx }}][rules][{{ $rIdx }}][attribute]" class="form-select form-select-sm rule-attribute-select" style="flex: 1.5;" onchange="updateRuleRowControls(this); triggerLiveEstimate();">
                                                    <optgroup label="Device & Location">
                                                        <option value="Country" {{ $curAttr === 'Country' ? 'selected' : '' }}>Country</option>
                                                        <option value="State / Region" {{ $curAttr === 'State / Region' ? 'selected' : '' }}>State / Region</option>
                                                        <option value="City" {{ $curAttr === 'City' ? 'selected' : '' }}>City</option>
                                                        <option value="Platform" {{ $curAttr === 'Platform' ? 'selected' : '' }}>Platform</option>
                                                        <option value="Manufacturer" {{ $curAttr === 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                                        <option value="Device Model" {{ $curAttr === 'Device Model' ? 'selected' : '' }}>Device Model</option>
                                                    </optgroup>
                                                    <optgroup label="App Activity">
                                                        <option value="Activity Status" {{ $curAttr === 'Activity Status' ? 'selected' : '' }}>Activity Status</option>
                                                        <option value="Last Active" {{ $curAttr === 'Last Active' ? 'selected' : '' }}>Last Active</option>
                                                        <option value="First Open" {{ $curAttr === 'First Open' ? 'selected' : '' }}>First Open</option>
                                                        <option value="App Version" {{ $curAttr === 'App Version' ? 'selected' : '' }}>App Version</option>
                                                        <option value="Photos Captured" {{ $curAttr === 'Photos Captured' ? 'selected' : '' }}>Photos Captured</option>
                                                    </optgroup>
                                                    <optgroup label="Permissions">
                                                        <option value="Notification Permission" {{ $curAttr === 'Notification Permission' ? 'selected' : '' }}>Notification Permission</option>
                                                        <option value="Location Permission" {{ $curAttr === 'Location Permission' ? 'selected' : '' }}>Location Permission</option>
                                                        <option value="Camera Permission" {{ $curAttr === 'Camera Permission' ? 'selected' : '' }}>Camera Permission</option>
                                                    </optgroup>
                                                </select>
                                                <select name="rule_groups[{{ $gIdx }}][rules][{{ $rIdx }}][operator]" class="form-select form-select-sm rule-operator-select" style="flex: 1;" onchange="triggerLiveEstimate()">
                                                    <option value="is" {{ $curOp === 'is' ? 'selected' : '' }}>is</option>
                                                    <option value="is not" {{ $curOp === 'is not' ? 'selected' : '' }}>is not</option>
                                                    <option value="is any of" {{ $curOp === 'is any of' ? 'selected' : '' }}>is any of</option>
                                                    <option value="within" {{ $curOp === 'within' ? 'selected' : '' }}>within</option>
                                                    <option value="not within" {{ $curOp === 'not within' ? 'selected' : '' }}>not within</option>
                                                </select>
                                                <div class="rule-value-box" style="flex: 1.5;">
                                                    @if(!empty($opts) && is_array($opts))
                                                        <select name="rule_groups[{{ $gIdx }}][rules][{{ $rIdx }}][value]" class="form-select form-select-sm rule-value-input" onchange="triggerLiveEstimate()">
                                                            @foreach($opts as $opt)
                                                                <option value="{{ $opt }}" {{ (string)$curVal === (string)$opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="text" name="rule_groups[{{ $gIdx }}][rules][{{ $rIdx }}][value]" class="form-control form-control-sm rule-value-input" value="{{ $curVal }}" oninput="triggerLiveEstimate()">
                                                    @endif
                                                </div>
                                                <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)" title="Delete Rule"><i class="fa-regular fa-trash-can"></i></button>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addRuleRow({{ $gIdx }})">
                                        <i class="fa-solid fa-plus me-1"></i> Add Rule
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addRuleGroup()">
                                <i class="fa-solid fa-plus me-1"></i> Add Rule Group
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: Platform & App Filters --}}
                <div class="builder-section-card">
                    <div class="section-header collapsible-trigger d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#collapseStep3" aria-expanded="true" aria-controls="collapseStep3">
                        <div class="d-flex align-items-center">
                            <span class="step-number">3</span>
                            <h2 class="section-title">Platform & App Filters</h2>
                        </div>
                        <i class="fa-solid fa-chevron-up section-collapse-chevron"></i>
                    </div>

                    <div class="collapse show pt-3" id="collapseStep3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-medium text-gray-700 mb-2">Platforms</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="platform_filters[platforms][]" value="Android" id="platAndroid" {{ in_array('Android', $segment->platform_filters['platforms'] ?? ['Android', 'iOS']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                        <label class="form-check-label fs-sm" for="platAndroid"><i class="fa-brands fa-android text-success me-1"></i> Android</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="platform_filters[platforms][]" value="iOS" id="platiOS" {{ in_array('iOS', $segment->platform_filters['platforms'] ?? ['Android', 'iOS']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                        <label class="form-check-label fs-sm" for="platiOS"><i class="fa-brands fa-apple text-dark me-1"></i> iOS</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-medium text-gray-700 mb-1">App Version</label>
                                <select name="platform_filters[app_version]" class="form-select form-select-sm" onchange="triggerLiveEstimate()">
                                    <option value="All Versions" {{ ($segment->platform_filters['app_version'] ?? '') === 'All Versions' ? 'selected' : '' }}>All Versions</option>
                                    <option value="v1.4.2" {{ ($segment->platform_filters['app_version'] ?? '') === 'v1.4.2' ? 'selected' : '' }}>v1.4.2 (Latest)</option>
                                    <option value="v1.4.1" {{ ($segment->platform_filters['app_version'] ?? '') === 'v1.4.1' ? 'selected' : '' }}>v1.4.1</option>
                                    <option value="v1.4.0" {{ ($segment->platform_filters['app_version'] ?? '') === 'v1.4.0' ? 'selected' : '' }}>v1.4.0</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-medium text-gray-700 mb-1">Location Permission</label>
                                <select name="platform_filters[location_permission]" class="form-select form-select-sm" onchange="triggerLiveEstimate()">
                                    <option value="Any" {{ ($segment->platform_filters['location_permission'] ?? 'Any') === 'Any' ? 'selected' : '' }}>Any</option>
                                    <option value="Precise" {{ ($segment->platform_filters['location_permission'] ?? '') === 'Precise' ? 'selected' : '' }}>Precise</option>
                                    <option value="Approximate" {{ ($segment->platform_filters['location_permission'] ?? '') === 'Approximate' ? 'selected' : '' }}>Approximate</option>
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
                </div>

                {{-- STEP 4: Exclusions --}}
                <div class="builder-section-card">
                    <div class="section-header collapsible-trigger d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#collapseStep4" aria-expanded="true" aria-controls="collapseStep4">
                        <div class="d-flex align-items-center">
                            <span class="step-number">4</span>
                            <h2 class="section-title">Exclusions</h2>
                        </div>
                        <i class="fa-solid fa-chevron-up section-collapse-chevron"></i>
                    </div>

                    <div class="collapse show pt-3" id="collapseStep4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="exclusions[exclude_inactive]" value="1" id="exclInactive" {{ !empty($segment->exclusions['exclude_inactive']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                    <label class="form-check-label fs-sm" for="exclInactive">Exclude inactive devices</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="exclusions[exclude_invalid_fcm]" value="1" id="exclFcm" {{ !empty($segment->exclusions['exclude_invalid_fcm']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                    <label class="form-check-label fs-sm" for="exclFcm">Exclude invalid FCM tokens</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="exclusions[exclude_notification_denied]" value="1" id="exclDenied" {{ !empty($segment->exclusions['exclude_notification_denied']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                    <label class="form-check-label fs-sm" for="exclDenied">Exclude notification denied</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="exclusions[exclude_crash_prone]" value="1" id="exclCrash" {{ !empty($segment->exclusions['exclude_crash_prone']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                    <label class="form-check-label fs-sm" for="exclCrash">Exclude crash-prone app versions</label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light border d-flex align-items-center gap-2 fs-xs text-muted mt-3 mb-0 py-2">
                            <i class="fa-solid fa-circle-info text-primary"></i>
                            <span id="segmentTypeNotice">
                                {{ $segment->type === 'dynamic' ? 'Dynamic segments refresh automatically as devices meet or leave these conditions in real-time.' : 'Static segment: Captures a fixed snapshot of matching devices.' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Live Sidebar Estimator --}}
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
                            <div class="p-2 bg-light rounded h-100 d-flex flex-column justify-content-center">
                                <div class="stat-hero-number eligible" id="statEligible">{{ number_format($estimate['eligible_devices'] ?? $segment->audience_size) }}</div>
                                <div class="text-muted fs-xs">Eligible</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded h-100 d-flex flex-column justify-content-center">
                                <div class="stat-sub-number deliverable" id="statDeliverable">{{ number_format($estimate['deliverable_devices'] ?? $segment->deliverable_count) }}</div>
                                <div class="text-muted fs-xs">Deliverable</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded h-100 d-flex flex-column justify-content-center">
                                <div class="stat-sub-number excluded" id="statExcluded">{{ number_format($estimate['excluded_devices'] ?? 0) }}</div>
                                <div class="text-muted fs-xs">Excluded</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-4 py-2 border-top border-bottom my-3">
                        <div style="width: 70px; height: 70px; border-radius: 50%; background: conic-gradient(#3b82f6 0% {{ $estimate['android_pct'] ?? 100 }}%, #cbd5e1 {{ $estimate['android_pct'] ?? 100 }}% 100%);" id="platformDonut"></div>
                        <div class="fs-xs">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: #3b82f6;"></span>
                                <span class="text-muted">Android</span>
                                <strong id="pctAndroid">{{ $estimate['android_pct'] ?? 100 }}%</strong>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: #cbd5e1;"></span>
                                <span class="text-muted">iOS</span>
                                <strong id="pctiOS">{{ $estimate['ios_pct'] ?? 0 }}%</strong>
                            </div>
                        </div>
                    </div>

                    <div class="fs-xs d-flex flex-column gap-2 mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Anonymous Users</span>
                            <strong id="statAnon">{{ number_format($estimate['anonymous_users'] ?? 0) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Active Devices</span>
                            <strong id="statActive">{{ number_format($estimate['active_devices'] ?? 0) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Notifications Enabled</span>
                            <strong id="statNotif">{{ number_format($estimate['notifications_enabled'] ?? 0) }}</strong>
                        </div>
                    </div>

                    <div class="border-top pt-3 mb-3">
                        <h4 class="h6 fw-bold text-gray-800 fs-xs text-uppercase mb-2">Criteria Summary</h4>
                        <div class="d-flex flex-wrap gap-1" id="criteriaSummaryContainer">
                            @foreach($segment->criteria_pills ?? ['Active Devices'] as $pill)
                                <span class="criteria-pill">{{ $pill }}</span>
                            @endforeach
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
                                <span id="checkTypeText">{{ ucfirst($segment->type) }} type selected</span>
                            </div>
                            <div class="segment-checklist-item" id="checkRules">
                                <i class="fa-solid fa-circle-check check-icon valid"></i>
                                <span id="checkRulesText">Rules configured</span>
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
                    <i class="fa-solid fa-check text-success me-1"></i> <span id="estimateStatusText">Estimated audience synced</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.segments.show', $segment->id) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" name="status" value="draft" class="btn btn-outline-primary">Save as Draft</button>
                    <button type="submit" name="status" value="{{ $segment->status === 'draft' ? 'active' : $segment->status }}" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Rule Dictionary Modal --}}
<div class="modal fade" id="rulesDictionaryModal" tabindex="-1" aria-labelledby="rulesDictionaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fs-14 fw-bold text-gray-900" id="rulesDictionaryModalLabel">
                    <i class="fa-solid fa-book-open text-primary me-2"></i> Audience Rule Dictionary & Operators
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle fs-xs mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Category</th>
                                <th>Attribute</th>
                                <th>Available Operators</th>
                                <th>Description / Values</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="3" class="fw-semibold bg-light-subtle">Location</td>
                                <td><strong>Country</strong></td>
                                <td><code>is</code>, <code>is not</code>, <code>is any of</code></td>
                                <td>Filter by country of device (e.g. India, United States, UAE)</td>
                            </tr>
                            <tr>
                                <td><strong>State / Region</strong></td>
                                <td><code>is</code>, <code>is not</code>, <code>is any of</code></td>
                                <td>Filter by state / province (e.g. Tamil Nadu, California)</td>
                            </tr>
                            <tr>
                                <td><strong>City</strong></td>
                                <td><code>is</code>, <code>is not</code>, <code>is any of</code></td>
                                <td>Target specific cities (e.g. Tirunelveli, Chennai)</td>
                            </tr>
                            <tr>
                                <td rowspan="3" class="fw-semibold bg-light-subtle">App Activity</td>
                                <td><strong>Activity Status</strong></td>
                                <td><code>is</code>, <code>is not</code></td>
                                <td><code>Active</code> (used app within 30d), <code>Inactive</code></td>
                            </tr>
                            <tr>
                                <td><strong>Last Active</strong></td>
                                <td><code>within</code>, <code>not within</code>, <code>before</code>, <code>after</code></td>
                                <td>Relative timeframe (7 days, 30 days, 90 days)</td>
                            </tr>
                            <tr>
                                <td><strong>App Version</strong></td>
                                <td><code>is</code>, <code>is not</code>, <code>above</code>, <code>below</code></td>
                                <td>Installed app release (e.g. v1.4.2, v1.4.1)</td>
                            </tr>
                            <tr>
                                <td rowspan="3" class="fw-semibold bg-light-subtle">Permissions</td>
                                <td><strong>Notification Permission</strong></td>
                                <td><code>is</code>, <code>is not</code></td>
                                <td><code>Enabled</code> or <code>Disabled</code> push permission</td>
                            </tr>
                            <tr>
                                <td><strong>Location Permission</strong></td>
                                <td><code>is</code>, <code>is not</code></td>
                                <td><code>Precise</code>, <code>Approximate</code>, or <code>Denied</code></td>
                            </tr>
                            <tr>
                                <td><strong>Camera Permission</strong></td>
                                <td><code>is</code>, <code>is not</code></td>
                                <td><code>Granted</code> or <code>Denied</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Available Options & Location Hierarchy passed from server
window.availableRuleOptions = @json($ruleOptions ?? []);
window.locationHierarchy    = @json($locationHierarchy ?? []);

// Get selected Country and State context from active rules
function getSelectedLocationContext() {
    let selectedCountry = null;
    let selectedState = null;

    document.querySelectorAll('.rule-row').forEach(row => {
        const attr = row.querySelector('.rule-attribute-select')?.value;
        const val = row.querySelector('.rule-value-input')?.value;
        if (attr === 'Country' && val) selectedCountry = val;
        if (attr === 'State / Region' && val) selectedState = val;
    });

    return { selectedCountry, selectedState };
}

// Synchronize dependent State and City dropdowns based on selected Country/State
function syncCascadingLocations() {
    const { selectedCountry, selectedState } = getSelectedLocationContext();

    // 1. Filter State / Region dropdowns based on selected Country
    document.querySelectorAll('.rule-row').forEach(row => {
        const attr = row.querySelector('.rule-attribute-select')?.value;
        const valSelect = row.querySelector('.rule-value-input');

        if (attr === 'State / Region' && valSelect && valSelect.tagName === 'SELECT') {
            const curVal = valSelect.value;
            let availableStates = [];

            if (selectedCountry && window.locationHierarchy[selectedCountry]) {
                availableStates = Object.keys(window.locationHierarchy[selectedCountry]);
            } else {
                availableStates = window.availableRuleOptions['State / Region'] || [];
            }

            if (availableStates.length > 0) {
                const newHtml = availableStates.map(st => `<option value="${st}" ${st === curVal ? 'selected' : ''}>${st}</option>`).join('');
                if (valSelect.innerHTML !== newHtml) {
                    valSelect.innerHTML = newHtml;
                    if (!availableStates.includes(curVal)) {
                        valSelect.value = availableStates[0];
                    }
                }
            }
        }
    });

    // 2. Filter City dropdowns based on selected Country and State
    document.querySelectorAll('.rule-row').forEach(row => {
        const attr = row.querySelector('.rule-attribute-select')?.value;
        const valSelect = row.querySelector('.rule-value-input');

        if (attr === 'City' && valSelect && valSelect.tagName === 'SELECT') {
            const curVal = valSelect.value;
            let availableCities = [];

            if (selectedCountry && selectedState && window.locationHierarchy[selectedCountry] && window.locationHierarchy[selectedCountry][selectedState]) {
                availableCities = window.locationHierarchy[selectedCountry][selectedState];
            } else if (selectedCountry && window.locationHierarchy[selectedCountry]) {
                availableCities = Object.values(window.locationHierarchy[selectedCountry]).flat();
            } else {
                availableCities = window.availableRuleOptions['City'] || [];
            }

            // Deduplicate
            availableCities = [...new Set(availableCities)];

            if (availableCities.length > 0) {
                const newHtml = availableCities.map(ct => `<option value="${ct}" ${ct === curVal ? 'selected' : ''}>${ct}</option>`).join('');
                if (valSelect.innerHTML !== newHtml) {
                    valSelect.innerHTML = newHtml;
                    if (!availableCities.includes(curVal)) {
                        valSelect.value = availableCities[0];
                    }
                }
            }
        }
    });
}

function selectSegmentType(type) {
    const dynamicCard = document.getElementById('typeDynamicLabel');
    const staticCard = document.getElementById('typeStaticLabel');
    const dynamicRadio = document.querySelector('input[name="type"][value="dynamic"]');
    const staticRadio = document.querySelector('input[name="type"][value="static"]');
    const checkTypeText = document.getElementById('checkTypeText');
    const noticeText = document.getElementById('segmentTypeNotice');

    if (type === 'dynamic') {
        if (dynamicCard) dynamicCard.classList.add('selected');
        if (staticCard) staticCard.classList.remove('selected');
        if (dynamicRadio) dynamicRadio.checked = true;
        if (staticRadio) staticRadio.checked = false;
        if (checkTypeText) checkTypeText.textContent = 'Dynamic type selected';
        if (noticeText) noticeText.textContent = 'Dynamic segments refresh automatically as devices meet or leave these conditions in real-time.';
    } else {
        if (staticCard) staticCard.classList.add('selected');
        if (dynamicCard) dynamicCard.classList.remove('selected');
        if (staticRadio) staticRadio.checked = true;
        if (dynamicRadio) dynamicRadio.checked = false;
        if (checkTypeText) checkTypeText.textContent = 'Static type selected';
        if (noticeText) noticeText.textContent = 'Static segment: Captures a fixed snapshot of matching devices at creation time.';
    }
    updateChecklist();
    triggerLiveEstimate();
}

function updateRuleRowControls(attrSelect) {
    const row = attrSelect.closest('.rule-row');
    if (!row) return;

    const opSelect = row.querySelector('.rule-operator-select');
    const valBox = row.querySelector('.rule-value-box');
    const attr = attrSelect.value;

    if (opSelect) {
        if (attr === 'Last Active' || attr === 'First Open') {
            opSelect.innerHTML = `
                <option value="within" selected>within</option>
                <option value="not within">not within</option>
                <option value="before">before</option>
                <option value="after">after</option>
            `;
        } else if (attr === 'Photos Captured' || attr === 'App Opens') {
            opSelect.innerHTML = `
                <option value="greater than" selected>greater than</option>
                <option value="is">equals</option>
                <option value="less than">less than</option>
            `;
        } else {
            opSelect.innerHTML = `
                <option value="is" selected>is</option>
                <option value="is not">is not</option>
                <option value="is any of">is any of</option>
            `;
        }
    }

    if (valBox) {
        const inputName = attrSelect.name.replace('[attribute]', '[value]');
        let opts = window.availableRuleOptions[attr];
        const { selectedCountry, selectedState } = getSelectedLocationContext();

        // If State / Region, use states filtered by Country
        if (attr === 'State / Region' && selectedCountry && window.locationHierarchy[selectedCountry]) {
            opts = Object.keys(window.locationHierarchy[selectedCountry]);
        }
        // If City, use cities filtered by Country / State
        else if (attr === 'City') {
            if (selectedCountry && selectedState && window.locationHierarchy[selectedCountry] && window.locationHierarchy[selectedCountry][selectedState]) {
                opts = window.locationHierarchy[selectedCountry][selectedState];
            } else if (selectedCountry && window.locationHierarchy[selectedCountry]) {
                opts = Object.values(window.locationHierarchy[selectedCountry]).flat();
            }
        }

        if (opts && Array.isArray(opts) && opts.length > 0) {
            let optionsHtml = opts.map(opt => `<option value="${opt}">${opt}</option>`).join('');
            valBox.innerHTML = `
                <select name="${inputName}" class="form-select form-select-sm rule-value-input" onchange="handleRuleValueChange(this)">
                    ${optionsHtml}
                </select>
            `;
        } else {
            valBox.innerHTML = `
                <input type="text" name="${inputName}" class="form-control form-control-sm rule-value-input" placeholder="Enter value..." oninput="handleRuleValueChange(this)">
            `;
        }
    }

    syncCascadingLocations();
}

function handleRuleValueChange(el) {
    const row = el.closest('.rule-row');
    const attr = row?.querySelector('.rule-attribute-select')?.value;
    if (attr === 'Country' || attr === 'State / Region') {
        syncCascadingLocations();
    }
    triggerLiveEstimate();
}

function addRuleRow(groupIndex = 0) {
    const rowsList = document.getElementById(`group${groupIndex + 1}Rows`);
    if (!rowsList) return;

    const existingRows = rowsList.querySelectorAll('.rule-row');
    const newIdx = existingRows.length;
    const ruleNum = newIdx + 1;

    const rowDiv = document.createElement('div');
    rowDiv.className = 'rule-row';
    rowDiv.dataset.rowIndex = newIdx;

    rowDiv.innerHTML = `
        <span class="rule-index">${ruleNum}</span>
        <select name="rule_groups[${groupIndex}][rules][${newIdx}][attribute]" class="form-select form-select-sm rule-attribute-select" style="flex: 1.5;" onchange="updateRuleRowControls(this); triggerLiveEstimate();">
            <optgroup label="Device & Location">
                <option value="Country">Country</option>
                <option value="State / Region">State / Region</option>
                <option value="City" selected>City</option>
                <option value="Platform">Platform</option>
                <option value="Manufacturer">Manufacturer</option>
                <option value="Device Model">Device Model</option>
            </optgroup>
            <optgroup label="App Activity">
                <option value="Activity Status">Activity Status</option>
                <option value="Last Active">Last Active</option>
                <option value="First Open">First Open</option>
                <option value="App Version">App Version</option>
                <option value="Photos Captured">Photos Captured</option>
            </optgroup>
            <optgroup label="Permissions">
                <option value="Notification Permission">Notification Permission</option>
                <option value="Location Permission">Location Permission</option>
                <option value="Camera Permission">Camera Permission</option>
            </optgroup>
        </select>
        <select name="rule_groups[${groupIndex}][rules][${newIdx}][operator]" class="form-select form-select-sm rule-operator-select" style="flex: 1;" onchange="triggerLiveEstimate()">
            <option value="is" selected>is</option>
            <option value="is not">is not</option>
            <option value="is any of">is any of</option>
        </select>
        <div class="rule-value-box" style="flex: 1.5;">
            <select name="rule_groups[${groupIndex}][rules][${newIdx}][value]" class="form-select form-select-sm rule-value-input" onchange="triggerLiveEstimate()">
                ${(window.availableRuleOptions['City'] || ['Tirunelveli', 'Chennai', 'Bengaluru']).map(c => `<option value="${c}">${c}</option>`).join('')}
            </select>
        </div>
        <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)" title="Delete Rule"><i class="fa-regular fa-trash-can"></i></button>
    `;

    rowsList.appendChild(rowDiv);
    renumberRuleRows();
    updateChecklist();
    triggerLiveEstimate();
}

function removeRuleRow(btn) {
    const row = btn.closest('.rule-row');
    const rowsList = row?.closest('.rule-rows-list');
    if (row && rowsList) {
        if (rowsList.querySelectorAll('.rule-row').length <= 1) {
            alert('A rule group must have at least one rule.');
            return;
        }
        row.remove();
        renumberRuleRows();
        updateChecklist();
        triggerLiveEstimate();
    }
}

function addRuleGroup() {
    const container = document.getElementById('ruleGroupsContainer');
    const existingGroups = container.querySelectorAll('.rule-group-card');
    const gIdx = existingGroups.length;

    const groupDiv = document.createElement('div');
    groupDiv.className = 'rule-group-card mt-3';
    groupDiv.id = `ruleGroup${gIdx + 1}`;

    groupDiv.innerHTML = `
        <div class="rule-group-header">
            <span class="group-title">Rule Group ${gIdx + 1}</span>
            <div class="d-flex align-items-center gap-2">
                <span class="fs-xs text-muted">Match</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="rule_groups[${gIdx}][match]" id="group${gIdx + 1}MatchAll" value="ALL" checked onchange="triggerLiveEstimate()">
                    <label class="btn btn-outline-primary px-3" for="group${gIdx + 1}MatchAll">ALL</label>
                    <input type="radio" class="btn-check" name="rule_groups[${gIdx}][match]" id="group${gIdx + 1}MatchAny" value="ANY" onchange="triggerLiveEstimate()">
                    <label class="btn btn-outline-primary px-3" for="group${gIdx + 1}MatchAny">ANY</label>
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="this.closest('.rule-group-card').remove(); renumberRuleRows(); triggerLiveEstimate();">Remove Group</button>
            </div>
        </div>
        <div class="rule-rows-list" id="group${gIdx + 1}Rows">
            <div class="rule-row" data-row-index="0">
                <span class="rule-index">1</span>
                <select name="rule_groups[${gIdx}][rules][0][attribute]" class="form-select form-select-sm rule-attribute-select" style="flex: 1.5;" onchange="updateRuleRowControls(this); triggerLiveEstimate();">
                    <optgroup label="Device & Location">
                        <option value="Country">Country</option>
                        <option value="State / Region">State / Region</option>
                        <option value="City" selected>City</option>
                        <option value="Platform">Platform</option>
                    </optgroup>
                    <optgroup label="App Activity">
                        <option value="Activity Status">Activity Status</option>
                        <option value="Last Active">Last Active</option>
                    </optgroup>
                </select>
                <select name="rule_groups[${gIdx}][rules][0][operator]" class="form-select form-select-sm rule-operator-select" style="flex: 1;" onchange="triggerLiveEstimate()">
                    <option value="is" selected>is</option>
                    <option value="is not">is not</option>
                </select>
                <div class="rule-value-box" style="flex: 1.5;">
                    <select name="rule_groups[${gIdx}][rules][0][value]" class="form-select form-select-sm rule-value-input" onchange="triggerLiveEstimate()">
                        ${(window.availableRuleOptions['City'] || ['Tirunelveli', 'Chennai']).map(c => `<option value="${c}">${c}</option>`).join('')}
                    </select>
                </div>
                <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)"><i class="fa-regular fa-trash-can"></i></button>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addRuleRow(${gIdx})">
            <i class="fa-solid fa-plus me-1"></i> Add Rule
        </button>
    `;

    container.appendChild(groupDiv);
    renumberRuleRows();
    updateChecklist();
    triggerLiveEstimate();
}

function renumberRuleRows() {
    let totalCount = 0;
    document.querySelectorAll('.rule-group-card').forEach((group, gIdx) => {
        group.querySelectorAll('.rule-row').forEach((row, rIdx) => {
            totalCount++;
            const idxSpan = row.querySelector('.rule-index');
            if (idxSpan) idxSpan.textContent = totalCount;

            const attrSel = row.querySelector('.rule-attribute-select');
            const opSel = row.querySelector('.rule-operator-select');
            const valInp = row.querySelector('.rule-value-input');

            if (attrSel) attrSel.name = `rule_groups[${gIdx}][rules][${rIdx}][attribute]`;
            if (opSel) opSel.name = `rule_groups[${gIdx}][rules][${rIdx}][operator]`;
            if (valInp) valInp.name = `rule_groups[${gIdx}][rules][${rIdx}][value]`;
        });
    });

    const countBadge = document.getElementById('rulesCountBadge');
    if (countBadge) countBadge.textContent = `${totalCount} ${totalCount === 1 ? 'rule' : 'rules'}`;

    const checkRulesText = document.getElementById('checkRulesText');
    if (checkRulesText) checkRulesText.textContent = `${totalCount} rules configured`;
}

function updateChecklist() {
    const nameInput = document.getElementById('segmentNameInput');
    const checkName = document.getElementById('checkName');
    const isNameValid = nameInput && nameInput.value.trim().length > 0;

    if (checkName) {
        checkName.querySelector('.check-icon').className = isNameValid ? 'fa-solid fa-circle-check check-icon valid' : 'fa-solid fa-circle-xmark check-icon text-muted';
    }
}

let estimateDebounceTimer = null;
function triggerLiveEstimate() {
    clearTimeout(estimateDebounceTimer);
    estimateDebounceTimer = setTimeout(async () => {
        const form = document.getElementById('segmentEditForm');
        if (!form) return;

        const badge = document.getElementById('estimatingBadge');
        if (badge) badge.classList.remove('d-none');

        const formData = new FormData(form);
        updateCriteriaSummaryPills();

        try {
            const response = await fetch('{{ route("admin.segments.estimate-live") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (response.ok) {
                const res = await response.json();
                if (res.success && res.data) {
                    const data = res.data;
                    document.getElementById('statEligible').textContent = Number(data.eligible_devices || 0).toLocaleString();
                    document.getElementById('statDeliverable').textContent = Number(data.deliverable_devices || 0).toLocaleString();
                    document.getElementById('statExcluded').textContent = Number(data.excluded_devices || 0).toLocaleString();

                    document.getElementById('pctAndroid').textContent = (data.android_pct || 0) + '%';
                    document.getElementById('pctiOS').textContent = (data.ios_pct || 0) + '%';
                    document.getElementById('platformDonut').style.background = `conic-gradient(#3b82f6 0% ${data.android_pct || 0}%, #cbd5e1 ${data.android_pct || 0}% 100%)`;

                    document.getElementById('statAnon').textContent = Number(data.anonymous_users || 0).toLocaleString();
                    document.getElementById('statActive').textContent = Number(data.active_devices || 0).toLocaleString();
                    document.getElementById('statNotif').textContent = Number(data.notifications_enabled || 0).toLocaleString();

                    if (data.criteria_summary && Array.isArray(data.criteria_summary)) {
                        updateCriteriaSummaryPills(data.criteria_summary);
                    }

                    document.getElementById('estimateStatusText').textContent = 'Estimated audience updated just now';
                }
            }
        } catch (e) {
            console.error('Estimation update failed:', e);
        } finally {
            if (badge) badge.classList.add('d-none');
        }
    }, 250);
}

function updateCriteriaSummaryPills(backendPills = null) {
    const container = document.getElementById('criteriaSummaryContainer');
    if (!container) return;

    if (backendPills && Array.isArray(backendPills) && backendPills.length > 0) {
        container.innerHTML = backendPills.map(p => `<span class="criteria-pill">${escapeHtml(p)}</span>`).join('');
        return;
    }

    const pills = [];
    document.querySelectorAll('.rule-row').forEach(row => {
        const valInp = row.querySelector('.rule-value-input');
        if (valInp && valInp.value && valInp.value.trim() !== '') {
            pills.push(valInp.value.trim());
        }
    });

    const platAndroid = document.getElementById('platAndroid')?.checked;
    const platiOS = document.getElementById('platiOS')?.checked;
    if (platAndroid && platiOS) pills.push('Android + iOS');
    else if (platAndroid) pills.push('Android Only');
    else if (platiOS) pills.push('iOS Only');

    if (document.getElementById('exclInactive')?.checked) pills.push('Active Only');
    if (document.getElementById('exclFcm')?.checked) pills.push('Valid FCM Token');
    if (document.getElementById('exclDenied')?.checked) pills.push('Notifications Enabled');

    const uniquePills = Array.from(new Set(pills));
    if (uniquePills.length === 0) {
        container.innerHTML = '<span class="text-muted fs-xs">No criteria filters added</span>';
    } else {
        container.innerHTML = uniquePills.map(p => `<span class="criteria-pill">${escapeHtml(p)}</span>`).join('');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', () => {
    renumberRuleRows();
    syncCascadingLocations();
    updateChecklist();
    updateCriteriaSummaryPills();
    triggerLiveEstimate();
});
</script>
@endsection
