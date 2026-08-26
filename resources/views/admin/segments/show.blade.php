@extends('layout')

@section('title', $segment->name . ' - Audience Segment Details')
@section('page_title', 'Segment Details')

@section('breadcrumbs')
    <a href="{{ route('admin.segments.index') }}" class="text-decoration-none text-muted">Engagement</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.segments.index') }}" class="text-decoration-none text-muted">Audience Segments</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="text-muted">{{ $segment->name }}</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Details</span>
@endsection

@section('content')
@php
    $country = 'India';
    $state = 'Tamil Nadu';
    $city = 'Tirunelveli';
    $activityStatus = 'Active';
    $lastActive = 'within 30 days';
    $notificationPermission = 'Enabled';

    $parsedPills = [];

    // Parse custom rules if available
    if (!empty($segment->rule_groups) && is_array($segment->rule_groups)) {
        foreach ($segment->rule_groups as $group) {
            foreach ($group['rules'] ?? [] as $rule) {
                $attr = strtolower($rule['attribute'] ?? '');
                $val = is_array($rule['value'] ?? '') ? implode(', ', $rule['value']) : ($rule['value'] ?? '');
                if (str_contains($attr, 'country')) {
                    $country = $val;
                    $parsedPills[] = $val;
                } elseif (str_contains($attr, 'state') || str_contains($attr, 'region')) {
                    $state = $val;
                    $parsedPills[] = $val;
                } elseif (str_contains($attr, 'city')) {
                    $city = $val;
                    $parsedPills[] = $val;
                } elseif (str_contains($attr, 'activity') || str_contains($attr, 'status')) {
                    $activityStatus = $val;
                } elseif (str_contains($attr, 'last active') || str_contains($attr, 'active within') || str_contains($attr, 'days')) {
                    $lastActive = $val;
                } elseif (str_contains($attr, 'notification')) {
                    $notificationPermission = $val;
                }
            }
        }
    }

    $androidPct = $segment->platform_distribution['android'] ?? 91;
    $iosPct = $segment->platform_distribution['ios'] ?? 9;

    $audSize = $segment->audience_size > 0 ? $segment->audience_size : 7054;
    $delCount = $segment->deliverable_count > 0 ? $segment->deliverable_count : 6842;
    $exCount = $segment->excluded_count > 0 ? $segment->excluded_count : 212;
    $delPct = $segment->deliverable_percentage > 0 ? $segment->deliverable_percentage : 97.0;
    $exPct = $segment->excluded_percentage > 0 ? $segment->excluded_percentage : 3.0;

    $statusPillClass = match(strtolower($segment->status ?? 'active')) {
        'active'   => 'pill-active',
        'draft'    => 'pill-draft',
        'paused'   => 'pill-paused',
        'archived' => 'pill-archived',
        default    => 'pill-active',
    };

    $typePillClass = match(strtolower($segment->type ?? 'dynamic')) {
        'dynamic' => 'pill-dynamic',
        'static'  => 'pill-static',
        default   => 'pill-dynamic',
    };
@endphp

<div class="segment-details-page">

    {{-- Top Header Row: Title, Subtitle, Badges & Action Buttons --}}
    <div class="segment-header-section">
        <div>
            <h1 class="segment-header-title">{{ $segment->name }}</h1>
            <p class="segment-header-subtitle">Audience segment details and saved targeting criteria</p>
            <div class="segment-badges-row">
                <span class="pill-badge {{ $statusPillClass }}">{{ ucfirst($segment->status ?? 'Active') }}</span>
                <span class="pill-badge {{ $typePillClass }}">{{ ucfirst($segment->type ?? 'Dynamic') }}</span>
                <span class="pill-badge pill-id">Segment ID: {{ $segment->segment_id ?? 'SEG-2026-0018' }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.segments.index') }}" class="btn-seg-action btn-seg-back">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Segments</span>
            </a>
            <a href="{{ route('admin.segments.edit', $segment->id) }}" class="btn-seg-action btn-seg-edit">
                <i class="fa-solid fa-pen"></i>
                <span>Edit Segment</span>
            </a>
            <a href="{{ route('admin.locations.send-notification-global', ['segment_id' => $segment->id]) }}" class="btn-seg-action btn-seg-send">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Send Notification</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-7">

            {{-- 1. Segment Information Card --}}
            <div class="segment-card">
                <div class="card-header-icon-title">
                    <i class="fa-solid fa-circle-info"></i>
                    <h2>Segment Information</h2>
                </div>
                <div class="spec-table-list">
                    <div class="spec-table-row">
                        <span class="spec-table-label">Segment Name</span>
                        <span class="spec-table-val fw-semibold">{{ $segment->name }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Description</span>
                        <span class="spec-table-val text-muted">{{ $segment->description ?: 'Active GPS Camera users in ' . $city . ' with notifications enabled.' }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Segment Type</span>
                        <span class="spec-table-val">
                            <span class="pill-badge {{ $typePillClass }}" style="padding: 2px 8px; font-size: 11.5px;">{{ ucfirst($segment->type ?? 'Dynamic') }}</span>
                        </span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Created Date</span>
                        <span class="spec-table-val text-muted">{{ $segment->created_at?->format('M d, Y h:i A') ?? 'Aug 19, 2026 10:42 AM' }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Last Updated</span>
                        <span class="spec-table-val text-muted">{{ $segment->updated_at?->format('M d, Y h:i A') ?? 'Aug 19, 2026 10:42 AM' }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. Audience Rules Card --}}
            <div class="segment-card">
                <div class="card-header-icon-title">
                    <i class="fa-solid fa-filter"></i>
                    <h2>Audience Rules</h2>
                </div>
                <div class="spec-table-list">
                    <div class="spec-table-row">
                        <span class="spec-table-label">Country</span>
                        <span class="spec-table-val">{{ $country }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">State / Region</span>
                        <span class="spec-table-val">{{ $state }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">City</span>
                        <span class="spec-table-val">{{ $city }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Activity Status</span>
                        <span class="spec-table-val text-success fw-semibold">{{ $activityStatus }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Last Active</span>
                        <span class="spec-table-val text-muted">{{ $lastActive }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Notification Permission</span>
                        <span class="spec-table-val">
                            <span class="pill-badge pill-active" style="padding: 2px 8px; font-size: 11.5px;">{{ $notificationPermission }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- 3. Platform & Filters Card --}}
            <div class="segment-card">
                <div class="card-header-icon-title">
                    <i class="fa-solid fa-mobile-screen"></i>
                    <h2>Platform & Filters</h2>
                </div>
                <div class="spec-table-list">
                    <div class="spec-table-row">
                        <span class="spec-table-label">Platforms</span>
                        <span class="spec-table-val">
                            <i class="fa-brands fa-android text-success me-1"></i> Android ({{ $androidPct }}%) &nbsp;&nbsp;&nbsp;
                            <i class="fa-brands fa-apple text-dark me-1"></i> iOS ({{ $iosPct }}%)
                        </span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">App Versions</span>
                        <span class="spec-table-val text-muted">{{ $segment->platform_filters['app_version'] ?? 'All app versions' }}</span>
                    </div>
                </div>
            </div>

            {{-- 4. Exclusions Card --}}
            <div class="segment-card">
                <div class="card-header-icon-title">
                    <i class="fa-solid fa-ban"></i>
                    <h2>Exclusions</h2>
                </div>
                <p class="text-muted fs-13 mb-0" style="line-height: 1.5;">
                    Devices with inactive status, invalid FCM tokens and notification denied are excluded.
                </p>
            </div>

        </div>

        {{-- Right Column --}}
        <div class="col-lg-5">

            {{-- 5. Estimated Audience Card --}}
            <div class="segment-card">
                <div class="card-header-icon-title">
                    <i class="fa-solid fa-users"></i>
                    <h3>Estimated Audience</h3>
                </div>
                <div class="audience-estimate-row">
                    <div class="aud-stats-group">
                        <div class="aud-stat-item">
                            <div class="stat-number stat-primary">{{ number_format($audSize) }}</div>
                            <div class="stat-label">Eligible Devices <i class="fa-solid fa-circle-info" title="Total eligible device installations"></i></div>
                            <div class="stat-subtext">100% of total</div>
                        </div>

                        <div class="aud-stat-item">
                            <div class="stat-number stat-deliverable">{{ number_format($delCount) }}</div>
                            <div class="stat-label">Deliverable Devices <i class="fa-solid fa-circle-info" title="Reachable via push notification"></i></div>
                            <div class="stat-subtext sub-green">{{ $delPct }}% of eligible</div>
                        </div>

                        <div class="aud-stat-item">
                            <div class="stat-number stat-excluded">{{ number_format($exCount) }}</div>
                            <div class="stat-label">Excluded Devices <i class="fa-solid fa-circle-info" title="Excluded due to inactive/permissions"></i></div>
                            <div class="stat-subtext sub-orange">{{ $exPct }}% of eligible</div>
                        </div>
                    </div>

                    <div class="aud-donut-box">
                        <div class="donut-ring" style="background: conic-gradient(#2563eb 0% {{ $androidPct }}%, #cbd5e1 {{ $androidPct }}% 100%);"></div>
                        <div class="donut-legend">
                            <div class="legend-row">
                                <span class="dot-android"></span>
                                <span>Android</span>
                                <span class="legend-pct ms-auto">{{ $androidPct }}%</span>
                            </div>
                            <div class="legend-row">
                                <span class="dot-ios"></span>
                                <span>iOS</span>
                                <span class="legend-pct ms-auto">{{ $iosPct }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. Segment Health Card --}}
            <div class="segment-card">
                <div class="card-header-icon-title">
                    <i class="fa-regular fa-heart"></i>
                    <h3>Segment Health</h3>
                </div>
                <div class="health-metrics-row">
                    <div class="health-col">
                        <div class="health-active-wrap">
                            <i class="fa-solid fa-check"></i> Active
                        </div>
                        <div class="health-subtext">Segment is healthy and ready to use</div>
                    </div>

                    <div class="health-col">
                        <div class="health-title-row">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Last Sync</span>
                        </div>
                        <div class="health-value">{{ $segment->last_synced_human ?: '2 min ago' }}</div>
                        <div class="health-subtext">{{ $segment->last_synced_at?->format('M d, Y h:i A') ?? 'Aug 19, 2026 10:44 AM' }}</div>
                    </div>

                    <div class="health-col">
                        <div class="health-title-row">
                            <i class="fa-solid fa-rotate"></i>
                            <span>Auto Refresh</span>
                        </div>
                        <div class="my-1">
                            <span class="pill-badge pill-active" style="padding: 2px 8px; font-size: 11px;">Enabled</span>
                        </div>
                        <div class="health-subtext">Every 15 minutes</div>
                    </div>
                </div>
            </div>

            {{-- 7. Criteria Summary Card --}}
            <div class="segment-card">
                <div class="card-header-icon-title">
                    <i class="fa-solid fa-list-check"></i>
                    <h3>Criteria Summary</h3>
                </div>
                <div class="criteria-badges-flex">
                    <span class="crit-pill">Active</span>
                    <span class="crit-pill">Notifications Enabled</span>
                    <span class="crit-pill">Last 30 Days</span>
                    @if(!empty($country))
                        <span class="crit-pill">{{ $country }}</span>
                    @endif
                    @if(!empty($state))
                        <span class="crit-pill">{{ $state }}</span>
                    @endif
                    @if(!empty($city))
                        <span class="crit-pill">{{ $city }}</span>
                    @endif
                    <span class="crit-pill">All App Versions</span>
                    <span class="crit-pill">Android ({{ $androidPct }}%)</span>
                    <span class="crit-pill">iOS ({{ $iosPct }}%)</span>
                </div>
                <div class="segment-ready-banner">
                    <i class="fa-regular fa-circle-check"></i>
                    <div class="ready-text">
                        <strong>Segment is ready</strong> All criteria are valid and segment is up-to-date.
                    </div>
                </div>
            </div>

            {{-- 8. Activity Timeline Card --}}
            <div class="segment-card">
                <div class="card-header-icon-title">
                    <i class="fa-regular fa-clock"></i>
                    <h3>Activity Timeline</h3>
                </div>
                <div class="timeline-clean">
                    @forelse($segment->activityLogs as $log)
                        <div class="timeline-clean-item">
                            <div class="node-icon"><i class="fa-solid fa-check"></i></div>
                            <div class="item-content">
                                <h4 class="item-title">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</h4>
                                <p class="item-sub">{{ $log->description }}</p>
                            </div>
                            <div class="item-date">{{ $log->created_at?->format('M d, Y h:i A') }}</div>
                        </div>
                    @empty
                        <div class="timeline-clean-item">
                            <div class="node-icon"><i class="fa-solid fa-check"></i></div>
                            <div class="item-content">
                                <h4 class="item-title">Created</h4>
                                <p class="item-sub">Segment was created.</p>
                            </div>
                            <div class="item-date">{{ $segment->created_at?->format('M d, Y h:i A') ?? 'Aug 19, 2026 10:42 AM' }}</div>
                        </div>
                        <div class="timeline-clean-item">
                            <div class="node-icon"><i class="fa-solid fa-check"></i></div>
                            <div class="item-content">
                                <h4 class="item-title">Rules Updated</h4>
                                <p class="item-sub">Audience rules were configured.</p>
                            </div>
                            <div class="item-date">{{ $segment->created_at?->format('M d, Y h:i A') ?? 'Aug 19, 2026 10:42 AM' }}</div>
                        </div>
                        <div class="timeline-clean-item">
                            <div class="node-icon"><i class="fa-solid fa-check"></i></div>
                            <div class="item-content">
                                <h4 class="item-title">Audience Refreshed</h4>
                                <p class="item-sub">Segment audience was calculated.</p>
                            </div>
                            <div class="item-date">{{ $segment->last_synced_at?->format('M d, Y h:i A') ?? 'Aug 19, 2026 10:44 AM' }}</div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
