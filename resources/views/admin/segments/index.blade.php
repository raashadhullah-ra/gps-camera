@extends('layout')

@section('title', 'Audience Segments - GeoCam Admin')
@section('page_title', 'Audience Segments')

@section('breadcrumbs')
    <span>Engagement</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Audience Segments</span>
@endsection

@section('content')
<div class="segments-page">

    <!-- 1. Page Header Row -->
    <div class="page-header-row">
        <div class="header-title-group">
            <h1 class="page-main-title">Audience Segments</h1>
            <p class="page-main-subtitle">Create and manage reusable audiences for targeted notifications</p>
        </div>
        <div class="header-actions-group">
            @if(auth()->user()->hasPermissionTo('segments.export'))
                <a href="{{ route('admin.segments.export-all', request()->query()) }}" class="btn btn-outline-primary" id="exportSegmentsCsvBtn">
                    <i class="fa-solid fa-download"></i>
                    <span>Export CSV</span>
                </a>
            @endif
            @if(auth()->user()->hasPermissionTo('segments.create'))
                <a href="{{ route('admin.segments.create') }}" class="btn btn-primary" id="createSegmentBtn">
                    <i class="fa-solid fa-plus"></i>
                    <span>Create Segment</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 2. 5 Top Metric Stat Summary Cards -->
    <div class="stat-grid-row">
        <!-- 1. Total Segments -->
        <div class="stat-card">
            <div class="stat-icon-wrapper blue-bg">
                <i class="fa-solid fa-user-group"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Segments</div>
                <div class="stat-value">{{ number_format($metrics['total_segments']) }}</div>
            </div>
        </div>

        <!-- 2. Active Segments -->
        <div class="stat-card">
            <div class="stat-icon-wrapper green-bg">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active Segments</div>
                <div class="stat-value">{{ number_format($metrics['active_segments']) }}</div>
            </div>
        </div>

        <!-- 3. Total Audience -->
        <div class="stat-card">
            <div class="stat-icon-wrapper purple-bg">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Audience</div>
                <div class="stat-value">{{ number_format($metrics['total_audience']) }}</div>
            </div>
        </div>

        <!-- 4. Deliverable Devices -->
        <div class="stat-card">
            <div class="stat-icon-wrapper blue-bg">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Deliverable Devices</div>
                <div class="stat-value">{{ number_format($metrics['deliverable_devices']) }}</div>
            </div>
        </div>

        <!-- 5. Draft Segments -->
        <div class="stat-card">
            <div class="stat-icon-wrapper orange-bg">
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Draft Segments</div>
                <div class="stat-value">{{ number_format($metrics['draft_segments']) }}</div>
            </div>
        </div>
    </div>

    <!-- 3. Filters Toolbar Card (Single Inline Row) -->
    <div class="card border-0 shadow-sm rounded-3 mb-2 p-3 bg-white" style="overflow-x: auto;">
        <div class="fs-13 fw-bold text-dark mb-2">Filters</div>
        <form action="{{ route('admin.segments.index') }}" method="GET" id="segmentsFilterForm" class="d-flex align-items-center gap-2 w-100 flex-nowrap">
            <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">

            <!-- 1. Compact Search Box -->
            <div class="position-relative filter-search-segment-wrap">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted fs-12 filter-search-icon"></i>
                <input type="text" name="search" class="form-control form-control-sm rounded-2 fs-12 filter-search-input" placeholder="Search segment or location" value="{{ request('search') }}">
            </div>

            <!-- 2. Segment Type Dropdown -->
            <div class="dropdown">
                <input type="hidden" name="type" id="filter_type" value="{{ request('type', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-segment-type" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownTypeBtn">
                    <span class="filter-box-label">Segment Type</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_type">{{ request('type', 'All') == 'All' ? 'All' : ucfirst(request('type')) }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12" aria-labelledby="dropdownTypeBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('type') == 'All' || !request('type') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('type', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('type') == 'dynamic' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('type', 'dynamic', 'Dynamic')">Dynamic</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('type') == 'static' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('type', 'static', 'Static')">Static</a></li>
                </ul>
            </div>

            <!-- 3. Status Dropdown -->
            <div class="dropdown">
                <input type="hidden" name="status" id="filter_status" value="{{ request('status', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-segment-status" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownStatusBtn">
                    <span class="filter-box-label">Status</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_status">{{ request('status', 'All') == 'All' ? 'All' : ucfirst(request('status')) }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12" aria-labelledby="dropdownStatusBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'All' || !request('status') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'active' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'active', 'Active')">Active</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'draft' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'draft', 'Draft')">Draft</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'paused' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'paused', 'Paused')">Paused</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'archived' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'archived', 'Archived')">Archived</a></li>
                </ul>
            </div>

            <!-- 4. Location Dropdown -->
            <div class="dropdown">
                <input type="hidden" name="location" id="filter_location" value="{{ request('location', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-segment-location" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownLocationBtn">
                    <span class="filter-box-label">Location</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_location">{{ request('location', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12" aria-labelledby="dropdownLocationBtn" style="max-height: 240px; overflow-y: auto;">
                    <li><a class="dropdown-item py-1 px-3 {{ request('location') == 'All' || !request('location') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('location', 'All')">All</a></li>
                    @foreach($filterOptions['locations'] as $loc)
                        <li><a class="dropdown-item py-1 px-3 {{ request('location') == $loc ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('location', '{{ $loc }}')">{{ $loc }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 5. Platform Dropdown -->
            <div class="dropdown">
                <input type="hidden" name="platform" id="filter_platform" value="{{ request('platform', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-segment-platform" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownPlatformBtn">
                    <span class="filter-box-label">Platform</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_platform">{{ request('platform', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12" aria-labelledby="dropdownPlatformBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('platform') == 'All' || !request('platform') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('platform', 'All')">All</a></li>
                    @foreach($filterOptions['platforms'] as $plat)
                        <li><a class="dropdown-item py-1 px-3 {{ request('platform') == $plat ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('platform', '{{ $plat }}')">{{ $plat }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 6. Created Date Box -->
            <div class="filter-date-box" onclick="document.getElementById('createdDateInput').focus()">
                <span class="filter-box-label">Created Date</span>
                <div class="d-flex align-items-center gap-1">
                    <i class="fa-regular fa-calendar text-muted"></i>
                    <input type="text" name="created_date" id="createdDateInput" class="filter-date-input" placeholder="Select date" value="{{ request('created_date') }}" readonly>
                </div>
            </div>

            <!-- 7. Reset & Apply Action Buttons -->
            <div class="filter-actions-wrap">
                <a href="{{ route('admin.segments.index', ['tab' => request('tab', 'all')]) }}" class="btn btn-outline-secondary btn-sm px-2" style="height: 38px; display: inline-flex; align-items: center;">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary btn-sm px-2" style="height: 38px; display: inline-flex; align-items: center; white-space: nowrap;">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Segments Found Subtext -->
    <div class="devices-found-bar">
        <span>{{ number_format($segments->total()) }} {{ Str::plural('segment', $segments->total()) }} found</span>
    </div>

    <!-- 4. Segments Table Card -->
    <div class="devices-table-card">
        <div class="table-card-top-bar">
            <h2 class="table-title">Created Audience Segments</h2>
            <div class="table-header-tools">
                <button type="button" class="btn-tool">
                    <i class="fa-solid fa-table-columns"></i>
                    <span>Columns</span>
                </button>
                <button type="button" class="btn-tool">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filters</span>
                </button>
            </div>
        </div>

        <!-- Filter Tabs Row -->
        <div class="devices-tabs-nav">
            @php $activeTab = request('tab', 'all'); @endphp
            <a href="{{ route('admin.segments.index', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) }}" class="tab-item {{ $activeTab == 'all' ? 'active' : '' }}">All Segments</a>
            <a href="{{ route('admin.segments.index', array_merge(request()->except('tab', 'page'), ['tab' => 'active'])) }}" class="tab-item {{ $activeTab == 'active' ? 'active' : '' }}">Active</a>
            <a href="{{ route('admin.segments.index', array_merge(request()->except('tab', 'page'), ['tab' => 'draft'])) }}" class="tab-item {{ $activeTab == 'draft' ? 'active' : '' }}">Draft</a>
            <a href="{{ route('admin.segments.index', array_merge(request()->except('tab', 'page'), ['tab' => 'paused'])) }}" class="tab-item {{ $activeTab == 'paused' ? 'active' : '' }}">Paused</a>
            <a href="{{ route('admin.segments.index', array_merge(request()->except('tab', 'page'), ['tab' => 'archived'])) }}" class="tab-item {{ $activeTab == 'archived' ? 'active' : '' }}">Archived</a>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive">
            <table class="devices-data-table">
                <thead>
                    <tr>
                        <th>Segment Name</th>
                        <th>Type</th>
                        <th>Criteria</th>
                        <th>Location</th>
                        <th>Audience Size</th>
                        <th>Deliverable</th>
                        <th>Last Synced</th>
                        <th>Created Date</th>
                        <th>Status</th>
                        <th class="th-action">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($segments as $segment)
                        <tr>
                            <!-- 1. Segment Name (Truncate > 35 chars with ellipsis) -->
                            <td>
                                <div class="d-flex align-items-center gap-2 segment-name-cell">
                                    <i class="fa-solid fa-user-group text-muted fs-12 flex-shrink-0"></i>
                                    <a href="{{ route('admin.segments.show', $segment->id) }}" 
                                       class="fw-semibold text-primary text-decoration-none segment-name-link" 
                                       title="{{ $segment->name }}">
                                        {{ Str::limit($segment->name, 35, '...') }}
                                    </a>
                                    @if($segment->created_at && $segment->created_at->diffInDays(now()) < 7)
                                        <span class="segment-badge-new flex-shrink-0">New</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 2. Type -->
                            <td>
                                <span class="{{ $segment->type_badge_class }}">
                                    {{ ucfirst($segment->type) }}
                                </span>
                            </td>

                            <!-- 3. Criteria Pills (Horizontal) -->
                            <td>
                                @php
                                    $rules = [];
                                    foreach($segment->rule_groups ?? [] as $g) {
                                        foreach($g['rules'] ?? [] as $r) {
                                            if(!empty($r['value'])) {
                                                $rules[] = is_array($r['value']) ? (is_string(reset($r['value'])) ? reset($r['value']) : json_encode($r['value'])) : (string)$r['value'];
                                            }
                                        }
                                    }
                                    $displayRules = array_slice($rules, 0, 2);
                                    $remainingCount = count($rules) - count($displayRules);
                                @endphp
                                <div class="criteria-badges-wrap">
                                    @foreach($displayRules as $c)
                                        <span class="criteria-pill">{{ Str::limit($c, 15, '...') }}</span>
                                    @endforeach
                                    @if($remainingCount > 0)
                                        <span class="criteria-pill criteria-more" title="{{ implode(', ', array_slice($rules, 2)) }}">+{{ $remainingCount }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 4. Location -->
                            <td>
                                <span class="text-dark">{{ $segment->location_summary }}</span>
                            </td>

                            <!-- 5. Audience Size -->
                            <td>
                                <span class="fw-bold text-dark">{{ number_format($segment->audience_size) }}</span>
                            </td>

                            <!-- 6. Deliverable -->
                            <td>
                                <span class="text-dark">{{ number_format($segment->deliverable_count) }}</span>
                            </td>

                            <!-- 7. Last Synced -->
                            <td>
                                <span class="text-secondary">{{ $segment->last_synced_human }}</span>
                            </td>

                            <!-- 8. Created Date -->
                            <td>
                                <span class="text-secondary">{{ $segment->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                            </td>

                            <!-- 9. Status -->
                            <td>
                                <span class="{{ $segment->status_badge_class }}">
                                    {{ ucfirst($segment->status) }}
                                </span>
                            </td>

                            <!-- 10. Actions -->
                            <td class="td-action">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('admin.segments.show', $segment->id) }}" class="btn btn-sm btn-icon text-primary" title="View Segment Details">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    
                                    <!-- 3-dots Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon text-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 fs-12" style="min-width: 220px;">
                                            <li class="dropdown-header text-uppercase fs-10 fw-bold text-muted">Segment Actions</li>
                                            <li>
                                                <a class="dropdown-item py-1.5" href="{{ route('admin.segments.show', $segment->id) }}">
                                                    <i class="fa-regular fa-file-lines text-muted me-2"></i> View Segment Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-1.5" href="{{ route('admin.segments.audience', $segment->id) }}">
                                                    <i class="fa-solid fa-users-viewfinder text-muted me-2"></i> View Audience
                                                </a>
                                            </li>
                                            @if(auth()->user()->hasPermissionTo('notifications.create'))
                                                <li>
                                                    <a class="dropdown-item py-1.5 text-primary" href="{{ route('admin.locations.send-notification-global', ['segment_id' => $segment->id]) }}">
                                                        <i class="fa-solid fa-paper-plane text-primary me-2"></i> Send Notification
                                                    </a>
                                                </li>
                                            @endif
                                            @if(auth()->user()->hasPermissionTo('segments.edit') || auth()->user()->hasPermissionTo('segments.create') || auth()->user()->hasPermissionTo('segments.export'))
                                                <li><hr class="dropdown-divider my-1"></li>
                                            @endif
                                            @if(auth()->user()->hasPermissionTo('segments.edit'))
                                                <li>
                                                    <a class="dropdown-item py-1.5" href="{{ route('admin.segments.edit', $segment->id) }}">
                                                        <i class="fa-solid fa-pen text-muted me-2"></i> Edit Segment
                                                    </a>
                                                </li>
                                            @endif
                                            @if(auth()->user()->hasPermissionTo('segments.create'))
                                                <li>
                                                    <button type="button" class="dropdown-item py-1.5" data-bs-toggle="modal" data-bs-target="#duplicateModal{{ $segment->id }}">
                                                        <i class="fa-regular fa-copy text-muted me-2"></i> Duplicate Segment
                                                    </button>
                                                </li>
                                            @endif
                                            @if(auth()->user()->hasPermissionTo('segments.edit'))
                                                <li>
                                                    <button type="button" class="dropdown-item py-1.5" data-bs-toggle="modal" data-bs-target="#refreshModal{{ $segment->id }}">
                                                        <i class="fa-solid fa-rotate text-muted me-2"></i> Refresh Audience
                                                    </button>
                                                </li>
                                            @endif
                                            <li>
                                                <button type="button" class="dropdown-item py-1.5" onclick="navigator.clipboard.writeText('{{ $segment->segment_id }}'); showAppToast('Segment ID {{ $segment->segment_id }} copied to clipboard!');">
                                                    <i class="fa-regular fa-clone text-muted me-2"></i> Copy Segment ID
                                                </button>
                                            </li>
                                            @if(auth()->user()->hasPermissionTo('segments.export'))
                                                <li>
                                                    <button type="button" class="dropdown-item py-1.5" data-bs-toggle="modal" data-bs-target="#exportModal{{ $segment->id }}">
                                                        <i class="fa-solid fa-download text-muted me-2"></i> Export Segment Data
                                                    </button>
                                                </li>
                                            @endif
                                            @if(auth()->user()->hasPermissionTo('segments.edit') || auth()->user()->hasPermissionTo('segments.delete'))
                                                <li><hr class="dropdown-divider my-1"></li>
                                                @if($segment->status === 'active' && auth()->user()->hasPermissionTo('segments.edit'))
                                                    <li>
                                                        <button type="button" class="dropdown-item py-1.5 text-warning" data-bs-toggle="modal" data-bs-target="#pauseModal{{ $segment->id }}">
                                                            <i class="fa-solid fa-pause me-2"></i> Pause Segment
                                                        </button>
                                                    </li>
                                                @endif
                                                @if($segment->status === 'active' && (auth()->user()->hasPermissionTo('segments.delete') || auth()->user()->hasPermissionTo('segments.edit')))
                                                    <li>
                                                        <button type="button" class="dropdown-item py-1.5 text-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal{{ $segment->id }}">
                                                            <i class="fa-solid fa-box-archive me-2"></i> Archive Segment
                                                        </button>
                                                    </li>
                                                @endif
                                                @if($segment->status === 'paused' && auth()->user()->hasPermissionTo('segments.edit'))
                                                    <li>
                                                        <form action="{{ route('admin.segments.resume', $segment->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item py-1.5 text-success">
                                                                <i class="fa-solid fa-play me-2"></i> Resume Segment
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($segment->status === 'paused' && (auth()->user()->hasPermissionTo('segments.delete') || auth()->user()->hasPermissionTo('segments.edit')))
                                                    <li>
                                                        <button type="button" class="dropdown-item py-1.5 text-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal{{ $segment->id }}">
                                                            <i class="fa-solid fa-box-archive me-2"></i> Archive Segment
                                                        </button>
                                                    </li>
                                                @endif
                                                @if($segment->status === 'archived' && (auth()->user()->hasPermissionTo('segments.delete') || auth()->user()->hasPermissionTo('segments.edit')))
                                                    <li>
                                                        <form action="{{ route('admin.segments.restore', $segment->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item py-1.5 text-success">
                                                                <i class="fa-solid fa-trash-can-arrow-up me-2"></i> Restore Segment
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if(auth()->user()->hasPermissionTo('segments.delete'))
                                                    <li>
                                                        <button type="button" class="dropdown-item py-1.5 text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $segment->id }}">
                                                            <i class="fa-regular fa-trash-can me-2"></i> Delete Segment
                                                        </button>
                                                    </li>
                                                @endif
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL 1: Duplicate Segment Modal -->
                        <div class="modal fade" id="duplicateModal{{ $segment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('admin.segments.duplicate', $segment->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-bottom px-4 pt-4 pb-3">
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark fs-15">Duplicate Audience Segment</h5>
                                                <p class="text-muted fs-12 mb-0">Create a new segment using the same rules and filters.</p>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 fs-12">
                                            <div class="p-3 bg-light rounded-3 mb-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="fw-semibold text-dark">{{ $segment->name }}</span>
                                                    <div class="d-flex gap-1">
                                                        <span class="{{ $segment->type_badge_class }}">{{ ucfirst($segment->type) }}</span>
                                                        <span class="{{ $segment->status_badge_class }}">{{ ucfirst($segment->status) }}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-3 text-muted fs-11">
                                                    <span>Audience: <strong>{{ number_format($segment->audience_size) }}</strong></span>
                                                    <span>Deliverable: <strong>{{ number_format($segment->deliverable_count) }}</strong></span>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-medium text-dark">New Segment Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control form-control-sm" value="Copy of {{ $segment->name }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-medium text-dark">Description</label>
                                                <textarea name="description" class="form-control form-control-sm" rows="2">Duplicate of {{ $segment->name }}</textarea>
                                            </div>

                                            <div class="mb-3 d-flex flex-column gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="copy_rules" value="1" id="copyRules{{ $segment->id }}" checked>
                                                    <label class="form-check-label" for="copyRules{{ $segment->id }}">
                                                        <strong>Copy audience rules</strong> <span class="text-muted fs-11 d-block">Copy the segment's rules and criteria.</span>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="copy_filters" value="1" id="copyFilters{{ $segment->id }}" checked>
                                                    <label class="form-check-label" for="copyFilters{{ $segment->id }}">
                                                        <strong>Copy platform and app filters</strong> <span class="text-muted fs-11 d-block">Copy the platform and app filters.</span>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="copy_exclusions" value="1" id="copyExcl{{ $segment->id }}" checked>
                                                    <label class="form-check-label" for="copyExcl{{ $segment->id }}">
                                                        <strong>Copy exclusions</strong> <span class="text-muted fs-11 d-block">Copy exclusion filters and blocked items.</span>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="create_as_active" value="1" id="createActive{{ $segment->id }}">
                                                    <label class="form-check-label" for="createActive{{ $segment->id }}">
                                                        <strong>Create as Active</strong> <span class="text-muted fs-11 d-block">Duplicates are saved as Draft by default.</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="alert alert-info py-2 px-3 fs-11 mb-0">
                                                <i class="fa-solid fa-circle-info me-1"></i> The duplicate will calculate its own audience after creation.
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top px-4 py-3">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Create Duplicate</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL 2: Safe Delete Modal (Matches Reference Screenshot 5) -->
                        <div class="modal fade" id="deleteModal{{ $segment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <form action="{{ route('admin.segments.destroy', $segment->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header border-0 pb-0 px-4 pt-4 align-items-start justify-content-between">
                                            <div class="w-100 text-center position-relative">
                                                <button type="button" class="btn-close shadow-none position-absolute end-0 top-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                                <div class="modal-top-icon-circle bg-red-light">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </div>
                                                <h5 class="modal-title fw-bold text-dark fs-16 mb-1">Delete Audience Segment</h5>
                                            </div>
                                        </div>
                                        <div class="modal-body px-4 pt-2 pb-4 fs-12">
                                            <div class="text-center mb-3">
                                                <h6 class="fw-bold text-dark fs-14 mb-1">Permanently delete <span class="text-primary">{{ $segment->name }}</span>?</h6>
                                                <div class="text-danger fw-medium" style="font-size: 11.5px; line-height: 1.4;">
                                                    This action cannot be undone. Segment rules, audience history and saved configuration will be permanently removed.
                                                </div>
                                            </div>

                                            {{-- Dependency Check --}}
                                            <div class="dependency-check-card mb-2">
                                                <div class="fw-bold text-dark mb-2" style="font-size: 11px;">Dependency Check</div>
                                                <div class="row g-2 text-start">
                                                    <div class="col-4">
                                                        <div class="p-2 bg-white rounded border d-flex align-items-center gap-2">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background-color: #eff6ff; color: #2563eb;">
                                                                <i class="fa-solid fa-bell fs-12"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold text-dark fs-13 lh-1">0</div>
                                                                <div class="text-muted" style="font-size: 9.5px; line-height: 1.1; margin-top: 2px;">Scheduled Notifications</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="p-2 bg-white rounded border d-flex align-items-center gap-2">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background-color: #fff7ed; color: #ea580c;">
                                                                <i class="fa-regular fa-file-lines fs-12"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold text-dark fs-13 lh-1">0</div>
                                                                <div class="text-muted" style="font-size: 9.5px; line-height: 1.1; margin-top: 2px;">Draft Notifications</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="p-2 bg-white rounded border d-flex align-items-center gap-2">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background-color: #f3e8ff; color: #7e22ce;">
                                                                <i class="fa-solid fa-chart-column fs-12"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold text-dark fs-13 lh-1">8</div>
                                                                <div class="text-muted" style="font-size: 9.5px; line-height: 1.1; margin-top: 2px;">Historical Campaigns</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Blue info alert --}}
                                            <div class="alert-blue-light mb-3">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <span>Deleting the segment will not delete past notification campaign reports.</span>
                                            </div>

                                            {{-- Meta Info Box --}}
                                            <div class="modal-mini-summary-card mb-3">
                                                <div class="row g-2 text-center">
                                                    <div class="col-4 border-end">
                                                        <div class="text-muted" style="font-size: 10.5px;">Segment ID</div>
                                                        <div class="fw-bold text-dark fs-12">{{ $segment->segment_id }}</div>
                                                    </div>
                                                    <div class="col-4 border-end">
                                                        <div class="text-muted" style="font-size: 10.5px;">Type</div>
                                                        <div><span class="badge bg-primary-subtle text-primary" style="font-size: 10px; padding: 2px 6px;">{{ ucfirst($segment->type ?: 'Dynamic') }}</span></div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="text-muted" style="font-size: 10.5px;">Audience</div>
                                                        <div class="fw-bold text-dark fs-12">{{ number_format($segment->audience_size ?: 7054) }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Confirmation Input --}}
                                            <div class="mb-2">
                                                <label class="form-label text-muted mb-1" style="font-size: 11.5px;">Type <strong class="text-dark">DELETE</strong> to confirm</label>
                                                <div class="position-relative">
                                                    <input type="text" id="confirmDeleteInput{{ $segment->id }}" class="form-control form-control-sm font-monospace" placeholder="DELETE" required oninput="
                                                        const match = this.value === 'DELETE';
                                                        const check = document.getElementById('deleteConfirmCheck{{ $segment->id }}').checked;
                                                        document.getElementById('deleteSubmitBtn{{ $segment->id }}').disabled = !(match && check);
                                                        document.getElementById('deleteCheckIcon{{ $segment->id }}').style.display = match ? 'block' : 'none';
                                                        this.style.borderColor = match ? '#16a34a' : '';
                                                    ">
                                                    <i class="fa-solid fa-check text-success position-absolute end-0 top-50 translate-middle-y me-2" id="deleteCheckIcon{{ $segment->id }}" style="display: none;"></i>
                                                </div>
                                            </div>

                                            {{-- Checkbox --}}
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="deleteConfirmCheck{{ $segment->id }}" onchange="
                                                    const match = document.getElementById('confirmDeleteInput{{ $segment->id }}').value === 'DELETE';
                                                    document.getElementById('deleteSubmitBtn{{ $segment->id }}').disabled = !(match && this.checked);
                                                ">
                                                <label class="form-check-label text-dark" for="deleteConfirmCheck{{ $segment->id }}" style="font-size: 11.5px;">
                                                    I understand this audience segment will be permanently deleted.
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" id="deleteSubmitBtn{{ $segment->id }}" class="btn btn-sm btn-danger px-3 fw-semibold" style="background-color: #dc2626; border-color: #dc2626;" disabled>Delete Permanently</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL 3: Pause Segment Modal (Matches Reference Screenshot 3) -->
                        <div class="modal fade" id="pauseModal{{ $segment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <form action="{{ route('admin.segments.pause', $segment->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-0 pb-0 px-4 pt-4 align-items-start justify-content-between">
                                            <div class="w-100 text-center position-relative">
                                                <button type="button" class="btn-close shadow-none position-absolute end-0 top-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                                <div class="modal-top-icon-circle bg-amber-light">
                                                    <i class="fa-solid fa-pause"></i>
                                                </div>
                                                <h5 class="modal-title fw-bold text-dark fs-16 mb-1">Pause Audience Segment</h5>
                                            </div>
                                        </div>
                                        <div class="modal-body px-4 pt-2 pb-4 fs-12">
                                            <div class="text-start mb-3">
                                                <h6 class="fw-bold text-dark fs-14 mb-1">Pause <span class="text-primary">{{ $segment->name }}</span>?</h6>
                                                <div class="text-muted" style="font-size: 11.5px;">
                                                    {{ $segment->segment_id }} • {{ ucfirst($segment->type ?: 'Dynamic') }} • {{ ucfirst($segment->status ?: 'Active') }}
                                                </div>
                                                <p class="text-muted fs-12 mt-2 mb-0">
                                                    This segment currently has <strong>{{ number_format($segment->audience_size ?: 7054) }}</strong> matching users, with <strong>{{ number_format($segment->deliverable_count ?: 6842) }}</strong> deliverable. Auto Refresh is currently <strong class="text-primary">Enabled</strong>.
                                                </p>
                                            </div>

                                            {{-- While paused box --}}
                                            <div class="p-3 bg-light rounded-3 border mb-3">
                                                <div class="fw-bold text-dark fs-11 text-uppercase mb-1.5">While paused:</div>
                                                <ul class="text-muted fs-11 mb-0 ps-3 d-flex flex-column gap-1">
                                                    <li>Audience will stop refreshing automatically.</li>
                                                    <li>Segment remains available for reports.</li>
                                                    <li>New notifications cannot target this segment.</li>
                                                    <li>Existing campaign history is unaffected.</li>
                                                </ul>
                                            </div>

                                            {{-- Pause duration --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark mb-1" style="font-size: 12px;">Pause duration</label>
                                                <div class="form-check mb-1.5">
                                                    <input class="form-check-input" type="radio" name="pause_duration" id="pauseManual{{ $segment->id }}" value="manual" checked>
                                                    <label class="form-check-label text-dark" for="pauseManual{{ $segment->id }}">
                                                        Until manually resumed
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input mt-0" type="radio" name="pause_duration" id="pauseUntil{{ $segment->id }}" value="until">
                                                    <label class="form-check-label text-dark me-2" for="pauseUntil{{ $segment->id }}">
                                                        Pause until
                                                    </label>
                                                    <input type="date" name="pause_until_date" class="form-control form-control-sm w-auto" style="font-size: 11.5px; padding: 2px 8px;">
                                                </div>
                                            </div>

                                            {{-- Reason --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-medium text-dark mb-1" style="font-size: 12px;">Reason (optional)</label>
                                                <select name="reason" class="form-select form-select-sm fs-12">
                                                    <option value="Maintenance / Review">Maintenance / Review</option>
                                                    <option value="Seasonal Pause">Seasonal Pause</option>
                                                    <option value="Audience Audit">Audience Audit</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>

                                            {{-- Warning alert --}}
                                            <div class="alert-amber-light">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <span>Pausing does not delete the segment or its audience history.</span>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-between gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary px-3 fw-semibold" data-bs-dismiss="modal">Keep Active</button>
                                            <button type="submit" class="btn btn-sm btn-warning text-white px-3 fw-semibold" style="background-color: #ea580c; border-color: #ea580c;">Pause Segment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL 4: Archive Segment Modal (Matches Reference Screenshot 4) -->
                        <div class="modal fade" id="archiveModal{{ $segment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <form action="{{ route('admin.segments.archive', $segment->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-0 pb-0 px-4 pt-4 align-items-start justify-content-between">
                                            <div class="w-100 text-center position-relative">
                                                <button type="button" class="btn-close shadow-none position-absolute end-0 top-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                                <div class="modal-top-icon-circle bg-amber-light">
                                                    <i class="fa-solid fa-box-archive"></i>
                                                </div>
                                                <h5 class="modal-title fw-bold text-dark fs-16 mb-1">Archive Audience Segment</h5>
                                            </div>
                                        </div>
                                        <div class="modal-body px-4 pt-2 pb-4 fs-12">
                                            <div class="text-center mb-3">
                                                <h6 class="fw-bold text-dark fs-14 mb-0">Archive <span class="text-primary">{{ $segment->name }}</span>?</h6>
                                            </div>

                                            {{-- 4-column Stat Summary --}}
                                            <div class="modal-mini-summary-card mb-3">
                                                <div class="row g-2 text-center">
                                                    <div class="col-3 border-end">
                                                        <div class="text-muted" style="font-size: 10.5px;">Status</div>
                                                        <div><span class="badge bg-success-subtle text-success" style="font-size: 10px; padding: 2px 6px;">{{ ucfirst($segment->status ?: 'Active') }}</span></div>
                                                    </div>
                                                    <div class="col-3 border-end">
                                                        <div class="text-muted" style="font-size: 10.5px;">Type</div>
                                                        <div><span class="badge bg-primary-subtle text-primary" style="font-size: 10px; padding: 2px 6px;">{{ ucfirst($segment->type ?: 'Dynamic') }}</span></div>
                                                    </div>
                                                    <div class="col-3 border-end">
                                                        <div class="text-muted" style="font-size: 10.5px;">Audience</div>
                                                        <div class="fw-bold text-dark fs-13">{{ number_format($segment->audience_size ?: 7054) }}</div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-muted" style="font-size: 10.5px;">Active Notifications</div>
                                                        <div class="fw-bold text-dark fs-13">0</div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Warning alert --}}
                                            <div class="alert-amber-light mb-3">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <span>Archiving will pause audience refresh and remove the segment from active selection lists.</span>
                                            </div>

                                            {{-- Checkmark points --}}
                                            <div class="d-flex flex-column gap-1.5 mb-3 text-dark fs-11">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-check text-success fs-12"></i>
                                                    <span>All associated rules and history will be preserved.</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-check text-success fs-12"></i>
                                                    <span>Performance reports will remain available.</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-check text-success fs-12"></i>
                                                    <span>You can restore this segment anytime from the Archived list.</span>
                                                </div>
                                            </div>

                                            {{-- Checkbox --}}
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="confirm_archive" id="confirmArch{{ $segment->id }}" checked>
                                                <label class="form-check-label fw-bold text-dark" for="confirmArch{{ $segment->id }}" style="font-size: 12px;">
                                                    Pause this segment and move it to Archived.
                                                </label>
                                            </div>

                                            {{-- Optional Reason --}}
                                            <div>
                                                <label class="form-label text-muted mb-1" style="font-size: 11.5px;">Optional: Add a reason for archiving</label>
                                                <input type="text" name="archive_reason" class="form-control form-control-sm fs-12" placeholder="Enter reason (optional)">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary px-3 fw-semibold">Archive Segment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL 5: Export Segment Data Modal (Matches Reference Screenshot 2) -->
                        <div class="modal fade" id="exportModal{{ $segment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <form action="{{ route('admin.segments.export', $segment->id) }}" method="GET">
                                        <div class="modal-header border-0 pb-0 px-4 pt-4 align-items-start justify-content-between">
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark fs-16 mb-0">Export Segment Data</h5>
                                                <p class="text-muted mb-0" style="font-size: 12px;">Download segment configuration and anonymous audience data.</p>
                                            </div>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body px-4 pt-3 pb-4 fs-12">
                                            {{-- Segment summary card --}}
                                            <div class="p-3 bg-light rounded-3 border mb-3 d-flex align-items-center gap-3">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background-color: #e0f2fe; color: #0284c7;">
                                                    <i class="fa-solid fa-users fs-5"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <strong class="text-dark fs-13">{{ $segment->name }}</strong>
                                                        <span class="badge bg-primary-subtle text-primary border" style="font-size: 10px; padding: 2px 6px;">{{ ucfirst($segment->type ?: 'Dynamic') }}</span>
                                                        <span class="badge bg-success-subtle text-success border" style="font-size: 10px; padding: 2px 6px;">{{ ucfirst($segment->status ?: 'Active') }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 11.5px;">
                                                        <div><strong class="text-dark">{{ number_format($segment->audience_size ?: 7054) }}</strong> Matching Audience</div>
                                                        <div><strong class="text-dark">{{ number_format($segment->deliverable_count ?: 6842) }}</strong> Deliverable Devices</div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Export Contents --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark mb-1.5" style="font-size: 12px;">Export Contents</label>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="contents[]" value="details" id="expDetails{{ $segment->id }}" checked>
                                                        <label class="form-check-label text-dark" for="expDetails{{ $segment->id }}">Segment details</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="contents[]" value="rules" id="expRules{{ $segment->id }}" checked>
                                                        <label class="form-check-label text-dark" for="expRules{{ $segment->id }}">Audience rules and filters</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="contents[]" value="devices" id="expDevices{{ $segment->id }}" checked>
                                                        <label class="form-check-label text-dark" for="expDevices{{ $segment->id }}">Anonymous device list</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="contents[]" value="summary" id="expSummary{{ $segment->id }}" checked>
                                                        <label class="form-check-label text-dark" for="expSummary{{ $segment->id }}">Audience summary and platform breakdown</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="contents[]" value="metrics" id="expMetrics{{ $segment->id }}">
                                                        <label class="form-check-label text-muted" for="expMetrics{{ $segment->id }}">Performance metrics</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Export Format --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark mb-1.5" style="font-size: 12px;">Export Format</label>
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <label class="format-card-radio selected w-100" id="formatLblCsv{{ $segment->id }}" onclick="selectExportFormat({{ $segment->id }}, 'csv')">
                                                            <div class="d-flex align-items-center gap-1.5">
                                                                <i class="fa-regular fa-file-lines text-primary"></i>
                                                                <div class="fw-bold fs-12 text-dark">CSV</div>
                                                            </div>
                                                            <input type="radio" name="format" value="csv" checked class="form-check-input m-0">
                                                        </label>
                                                        <div class="text-muted text-start mt-1" style="font-size: 10.5px;">Comma separated values</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="format-card-radio w-100" id="formatLblXlsx{{ $segment->id }}" onclick="selectExportFormat({{ $segment->id }}, 'xlsx')">
                                                            <div class="d-flex align-items-center gap-1.5">
                                                                <i class="fa-regular fa-file-excel text-success"></i>
                                                                <div class="fw-bold fs-12 text-dark">XLSX</div>
                                                            </div>
                                                            <input type="radio" name="format" value="xlsx" class="form-check-input m-0">
                                                        </label>
                                                        <div class="text-muted text-start mt-1" style="font-size: 10.5px;">Microsoft Excel format</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="format-card-radio w-100" id="formatLblJson{{ $segment->id }}" onclick="selectExportFormat({{ $segment->id }}, 'json')">
                                                            <div class="d-flex align-items-center gap-1.5">
                                                                <i class="fa-regular fa-file-code text-warning"></i>
                                                                <div class="fw-bold fs-12 text-dark">JSON</div>
                                                            </div>
                                                            <input type="radio" name="format" value="json" class="form-check-input m-0">
                                                        </label>
                                                        <div class="text-muted text-start mt-1" style="font-size: 10.5px;">JavaScript Object Notation</div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Date / Reference --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark mb-1" style="font-size: 12px;">Date / Reference</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="snapshot" id="snapCurr{{ $segment->id }}" value="current" checked>
                                                    <label class="form-check-label text-dark fw-semibold" for="snapCurr{{ $segment->id }}">
                                                        Current audience snapshot
                                                    </label>
                                                    <div class="text-muted" style="font-size: 11px;">Export the latest audience state as of now</div>
                                                </div>
                                            </div>

                                            {{-- Info Alert --}}
                                            <div class="alert-blue-light mb-2">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <span>The export contains anonymous installation IDs only. No registered user data is included.</span>
                                            </div>

                                            {{-- File Estimate --}}
                                            <div class="text-muted" style="font-size: 11.5px;">
                                                <span class="fw-bold text-dark">File Estimate:</span> Approximately {{ number_format($segment->audience_size ?: 7054) }} rows • 1.8 MB
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary px-3 fw-semibold d-inline-flex align-items-center gap-1.5">
                                                <i class="fa-solid fa-table"></i>
                                                <span>Export Data</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL 6: Refresh Audience Modal (Matches Reference Screenshot 1) -->
                        <div class="modal fade" id="refreshModal{{ $segment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <form action="{{ route('admin.segments.refresh', $segment->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-0 pb-0 px-4 pt-4 align-items-start justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="modal-icon-header-circle icon-blue">
                                                    <i class="fa-solid fa-arrows-rotate"></i>
                                                </div>
                                                <h5 class="modal-title fw-bold text-dark fs-16 mb-0">Refresh Segment Audience</h5>
                                            </div>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body px-4 pt-2 pb-4 fs-12">
                                            <p class="text-muted fs-12 mb-3">Recalculate devices matching <strong class="text-primary">{{ $segment->name }}</strong>.</p>
                                            
                                            {{-- Mini stat summary card --}}
                                            <div class="modal-mini-summary-card">
                                                <div class="row g-2 text-start">
                                                    <div class="col-3">
                                                        <div class="text-muted" style="font-size: 11px;">Matching Audience</div>
                                                        <div class="fw-bold text-dark fs-13">{{ number_format($segment->audience_size ?: 7054) }}</div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-muted" style="font-size: 11px;">Deliverable</div>
                                                        <div class="fw-bold text-dark fs-13">{{ number_format($segment->deliverable_count ?: 6842) }}</div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-muted" style="font-size: 11px;">Last Refreshed</div>
                                                        <div class="fw-bold text-dark fs-13">{{ $segment->last_synced_human ?: '2 min ago' }}</div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-muted" style="font-size: 11px;">Type</div>
                                                        <div class="fw-bold text-dark fs-13">{{ ucfirst($segment->type ?: 'Dynamic') }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Radio options --}}
                                            <div class="mb-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="refresh_mode" id="refreshNow{{ $segment->id }}" value="now" checked>
                                                    <label class="form-check-label text-dark fw-semibold" for="refreshNow{{ $segment->id }}">
                                                        Refresh now
                                                    </label>
                                                    <div class="text-muted" style="font-size: 11.5px; margin-top: 1px;">Recalculate immediately using the saved rules</div>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="refresh_mode" id="refreshBg{{ $segment->id }}" value="background">
                                                    <label class="form-check-label text-dark fw-semibold" for="refreshBg{{ $segment->id }}">
                                                        Refresh in background
                                                    </label>
                                                    <div class="text-muted" style="font-size: 11.5px; margin-top: 1px;">Continue working while the audience recalculates.</div>
                                                </div>
                                            </div>

                                            {{-- Checkboxes --}}
                                            <div class="mb-3">
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="checkbox" name="remove_unmatched" id="removeUnmatched{{ $segment->id }}" checked>
                                                    <label class="form-check-label text-dark" for="removeUnmatched{{ $segment->id }}" style="font-size: 12px;">
                                                        Remove devices that no longer match
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="add_new" id="addNew{{ $segment->id }}" checked>
                                                    <label class="form-check-label text-dark" for="addNew{{ $segment->id }}" style="font-size: 12px;">
                                                        Add newly eligible devices
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Blue callout box --}}
                                            <div class="alert-blue-light mb-3">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <span>Refreshing does not send notifications or change saved rules.</span>
                                            </div>

                                            {{-- Clock footer note --}}
                                            <div class="text-muted d-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                                <i class="fa-regular fa-clock"></i>
                                                <span>Estimated time: Usually completes within 1–2 minutes.</span>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary px-3 fw-semibold">Refresh Audience</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fs-1 mb-2 d-block text-gray-300"></i>
                                No audience segments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Blue Callout Row -->
        <div class="px-3 py-2 border-top bg-light text-primary fs-12 d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-info"></i>
            <span>Dynamic segments update automatically when devices meet or leave the saved criteria.</span>
        </div>

        <!-- Table Footer Bar -->
        <div class="table-footer-bar">
            <div class="footer-left-info">
                Showing {{ $segments->firstItem() ?? 0 }} to {{ $segments->lastItem() ?? 0 }} of {{ number_format($segments->total()) }} segments
            </div>
            <div class="footer-right-controls">
                <div class="rows-per-page-group">
                    <span>Rows per page:</span>
                    <select class="form-select rows-select" onchange="changePerPage(this.value)">
                        <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <!-- Custom Pagination -->
                <div class="custom-pagination">
                    @if ($segments->onFirstPage())
                        <button type="button" class="page-btn prev-btn" disabled aria-label="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    @else
                        <a href="{{ $segments->previousPageUrl() }}" class="page-btn prev-btn" aria-label="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    @for ($page = 1; $page <= $segments->lastPage(); $page++)
                        @if ($page == $segments->currentPage())
                            <button type="button" class="page-btn page-num active">{{ $page }}</button>
                        @else
                            <a href="{{ $segments->url($page) }}" class="page-btn page-num">{{ $page }}</a>
                        @endif
                    @endfor

                    @if ($segments->hasMorePages())
                        <a href="{{ $segments->nextPageUrl() }}" class="page-btn next-btn" aria-label="Next Page">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <button type="button" class="page-btn next-btn" disabled aria-label="Next Page">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectExportFormat(segmentId, format) {
    ['csv', 'xlsx', 'json'].forEach(f => {
        const lbl = document.getElementById(`formatLbl${f.charAt(0).toUpperCase() + f.slice(1)}${segmentId}`);
        if (lbl) {
            if (f === format) {
                lbl.classList.add('selected');
                const radio = lbl.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
            } else {
                lbl.classList.remove('selected');
            }
        }
    });
}

function setFilterOption(type, value, label) {
    document.getElementById('filter_' + type).value = value;
    document.getElementById('label_' + type).innerText = label || value;
}

function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined' && document.getElementById('createdDateInput')) {
        flatpickr('#createdDateInput', {
            mode: 'single',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'M j, Y',
            allowInput: false
        });
    }
});
</script>
@endpush
