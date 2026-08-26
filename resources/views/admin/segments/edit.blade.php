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
                    <div class="section-header">
                        <span class="step-number">1</span>
                        <h2 class="section-title">Segment Details</h2>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium text-gray-700 mb-1">Segment Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="segmentNameInput" class="form-control form-control-sm" value="{{ $segment->name }}" maxlength="80" required oninput="triggerLiveEstimate()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-gray-700 mb-1">Segment ID</label>
                            <input type="text" class="form-control form-control-sm bg-light font-monospace" value="{{ $segment->segment_id }}" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-medium text-gray-700 mb-1">Description <span class="text-muted fs-xs">(Optional)</span></label>
                            <textarea name="description" class="form-control form-control-sm" rows="2" maxlength="200">{{ $segment->description }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-medium text-gray-700 mb-2">Segment Type</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="segment-type-card {{ $segment->type === 'dynamic' ? 'selected' : '' }}" id="typeDynamicLabel">
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
                                    <label class="segment-type-card {{ $segment->type === 'static' ? 'selected' : '' }}" id="typeStaticLabel">
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

                {{-- STEP 2: Audience Rules --}}
                <div class="builder-section-card">
                    <div class="section-header justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="step-number">2</span>
                            <h2 class="section-title">Audience Rules</h2>
                        </div>
                    </div>

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
                                        <div class="btn-group btn-group-sm" role="group">
                                            <input type="radio" class="btn-check" name="rule_groups[{{ $gIdx }}][match]" id="group{{ $gIdx + 1 }}MatchAll" value="ALL" {{ ($group['match'] ?? 'ALL') === 'ALL' ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                            <label class="btn btn-outline-primary px-3" for="group{{ $gIdx + 1 }}MatchAll">ALL</label>
                                            <input type="radio" class="btn-check" name="rule_groups[{{ $gIdx }}][match]" id="group{{ $gIdx + 1 }}MatchAny" value="ANY" {{ ($group['match'] ?? 'ALL') === 'ANY' ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                            <label class="btn btn-outline-primary px-3" for="group{{ $gIdx + 1 }}MatchAny">ANY</label>
                                        </div>
                                        @if($gIdx > 0)
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="this.closest('.rule-group-card').remove(); triggerLiveEstimate();">Remove Group</button>
                                        @endif
                                    </div>
                                </div>

                                <div class="rule-rows-list" id="group{{ $gIdx + 1 }}Rows">
                                    @foreach($group['rules'] ?? [] as $rIdx => $rule)
                                        <div class="rule-row">
                                            <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
                                            <span class="rule-index">{{ $rIdx + 1 }}</span>
                                            <select name="rule_groups[{{ $gIdx }}][rules][{{ $rIdx }}][attribute]" class="form-select form-select-sm" style="flex: 1.5;" onchange="updateRuleOperators(this); triggerLiveEstimate();">
                                                <option value="Country" {{ ($rule['attribute'] ?? '') === 'Country' ? 'selected' : '' }}>Country</option>
                                                <option value="State / Region" {{ ($rule['attribute'] ?? '') === 'State / Region' ? 'selected' : '' }}>State / Region</option>
                                                <option value="City" {{ ($rule['attribute'] ?? '') === 'City' ? 'selected' : '' }}>City</option>
                                                <option value="Platform" {{ ($rule['attribute'] ?? '') === 'Platform' ? 'selected' : '' }}>Platform</option>
                                                <option value="Activity Status" {{ ($rule['attribute'] ?? '') === 'Activity Status' ? 'selected' : '' }}>Activity Status</option>
                                                <option value="Last Active" {{ ($rule['attribute'] ?? '') === 'Last Active' ? 'selected' : '' }}>Last Active</option>
                                                <option value="Notification Permission" {{ ($rule['attribute'] ?? '') === 'Notification Permission' ? 'selected' : '' }}>Notification Permission</option>
                                            </select>
                                            <select name="rule_groups[{{ $gIdx }}][rules][{{ $rIdx }}][operator]" class="form-select form-select-sm" style="flex: 1;" onchange="triggerLiveEstimate()">
                                                <option value="is" {{ ($rule['operator'] ?? '') === 'is' ? 'selected' : '' }}>is</option>
                                                <option value="is not" {{ ($rule['operator'] ?? '') === 'is not' ? 'selected' : '' }}>is not</option>
                                                <option value="within" {{ ($rule['operator'] ?? '') === 'within' ? 'selected' : '' }}>within</option>
                                                <option value="above" {{ ($rule['operator'] ?? '') === 'above' ? 'selected' : '' }}>above</option>
                                                <option value="below" {{ ($rule['operator'] ?? '') === 'below' ? 'selected' : '' }}>below</option>
                                            </select>
                                            <input type="text" name="rule_groups[{{ $gIdx }}][rules][{{ $rIdx }}][value]" class="form-control form-control-sm" style="flex: 1.5;" value="{{ is_array($rule['value'] ?? '') ? implode(', ', $rule['value']) : ($rule['value'] ?? '') }}" oninput="triggerLiveEstimate()">
                                            <button type="button" class="delete-rule-btn" onclick="removeRuleRow(this)"><i class="fa-regular fa-trash-can"></i></button>
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
                                    <input class="form-check-input" type="checkbox" name="platform_filters[platforms][]" value="Android" id="platAndroid" {{ in_array('Android', $segment->platform_filters['platforms'] ?? ['Android', 'iOS']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                    <label class="form-check-label fs-sm" for="platAndroid"><i class="fa-brands fa-android text-success me-1"></i> Android</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="platform_filters[platforms][]" value="iOS" id="platiOS" {{ in_array('iOS', $segment->platform_filters['platforms'] ?? ['Android', 'iOS']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                    <label class="form-check-label fs-sm" for="platiOS"><i class="fa-brands fa-apple text-dark me-1"></i> iOS</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium text-gray-700 mb-1">App Version</label>
                            <select name="platform_filters[app_version]" class="form-select form-select-sm" onchange="triggerLiveEstimate()">
                                <option value="All Versions" {{ ($segment->platform_filters['app_version'] ?? '') === 'All Versions' ? 'selected' : '' }}>All Versions</option>
                                <option value="v1.4.2" {{ ($segment->platform_filters['app_version'] ?? '') === 'v1.4.2' ? 'selected' : '' }}>v1.4.2 (Latest)</option>
                                <option value="v1.4.1" {{ ($segment->platform_filters['app_version'] ?? '') === 'v1.4.1' ? 'selected' : '' }}>v1.4.1</option>
                                <option value="v1.4.0" {{ ($segment->platform_filters['app_version'] ?? '') === 'v1.4.0' ? 'selected' : '' }}>v1.4.0</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Exclusions --}}
                <div class="builder-section-card">
                    <div class="section-header">
                        <span class="step-number">4</span>
                        <h2 class="section-title">Exclusions (Optional)</h2>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="exclusions[exclude_inactive]" value="1" id="exclInactive" {{ !empty($segment->exclusions['exclude_inactive']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                <label class="form-check-label fs-sm" for="exclInactive">Exclude users who have uninstalled the app</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="exclusions[exclude_notification_denied]" value="1" id="exclDenied" {{ !empty($segment->exclusions['exclude_notification_denied']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                <label class="form-check-label fs-sm" for="exclDenied">Exclude users who have opted out of notifications</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="exclusions[exclude_test_devices]" value="1" id="exclTest" {{ !empty($segment->exclusions['exclude_test_devices']) ? 'checked' : '' }} onchange="triggerLiveEstimate()">
                                <label class="form-check-label fs-sm" for="exclTest">Exclude users in test devices or internal groups</label>
                            </div>
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
                            <div class="p-2 bg-light rounded">
                                <div class="stat-hero-number" id="statEligible">{{ number_format($segment->audience_size) }}</div>
                                <div class="text-muted fs-xs">Eligible Audience</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <div class="stat-sub-number deliverable" id="statDeliverable">{{ number_format($segment->deliverable_count) }}</div>
                                <div class="text-muted fs-xs">Deliverable</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <div class="stat-sub-number excluded" id="statExcluded">{{ number_format($segment->excluded_count) }}</div>
                                <div class="text-muted fs-xs">Excluded</div>
                            </div>
                        </div>
                    </div>

                    {{-- Criteria Summary --}}
                    <div class="border-top pt-3 mb-3">
                        <h4 class="h6 fw-bold text-gray-800 fs-xs text-uppercase mb-2">Criteria Summary</h4>
                        <div class="d-flex flex-wrap gap-1" id="criteriaSummaryContainer">
                            <span class="criteria-pill">Active</span>
                            <span class="criteria-pill">Notifications Enabled</span>
                            <span class="criteria-pill">Last 30 Days</span>
                            <span class="criteria-pill">{{ $segment->location_summary }}</span>
                            <span class="criteria-pill">Android {{ $segment->platform_distribution['android'] ?? 91 }}%</span>
                            <span class="criteria-pill">iOS {{ $segment->platform_distribution['ios'] ?? 9 }}%</span>
                        </div>
                    </div>

                    {{-- Segment Checklist --}}
                    <div class="border-top pt-3">
                        <h4 class="h6 fw-bold text-gray-800 fs-xs text-uppercase mb-2">Segment Checklist</h4>
                        <div class="d-flex flex-column gap-1">
                            <div class="segment-checklist-item"><i class="fa-solid fa-circle-check check-icon valid"></i><span>Segment name is valid</span></div>
                            <div class="segment-checklist-item"><i class="fa-solid fa-circle-check check-icon valid"></i><span>At least one rule is defined</span></div>
                            <div class="segment-checklist-item"><i class="fa-solid fa-circle-check check-icon valid"></i><span>Rules are properly configured</span></div>
                            <div class="segment-checklist-item"><i class="fa-solid fa-circle-check check-icon valid"></i><span>Platform filters are valid</span></div>
                            <div class="segment-checklist-item"><i class="fa-solid fa-circle-check check-icon valid"></i><span>Estimated audience calculated</span></div>
                            <div class="segment-checklist-item"><i class="fa-solid fa-circle-check check-icon valid"></i><span>Segment is ready to save</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Sticky Bar --}}
        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between">
                <div class="text-muted fs-xs mb-2 mb-md-0">
                    <i class="fa-solid fa-check text-success me-1"></i> <span id="estimateStatusText">Estimated audience updated just now</span>
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
@endsection
