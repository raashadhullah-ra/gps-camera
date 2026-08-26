@extends('layout')

@section('title', $location->city . ' Activity Heatmap - GPS Camera Admin')
@section('page_title', $location->city . ' Activity Heatmap')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Installations</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.locations.index') }}">Locations</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.locations.show', $location->id) }}">{{ $location->city }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active-crumb">Activity Heatmap</span>
@endsection

@section('content')
<div class="locations-page">

    <!-- 1. Top Header with Export Action -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Activity Heatmap</h1>
            <p class="text-secondary fs-13 mb-0">Aggregated GPS Camera usage patterns across {{ $location->city }}</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <i class="fa-regular fa-calendar"></i>
                    <span>Last 30 days</span>
                </button>
                <ul class="dropdown-menu shadow-sm fs-12">
                    <li><a class="dropdown-item py-1" href="javascript:void(0)">Today</a></li>
                    <li><a class="dropdown-item py-1" href="javascript:void(0)">Last 7 days</a></li>
                    <li><a class="dropdown-item py-1 active" href="javascript:void(0)">Last 30 days</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-2" onclick="showInfoToast('Exporting Activity Heatmap report')">
                <i class="fa-solid fa-download"></i>
                <span>Export Heatmap</span>
            </button>
        </div>
    </div>

    <!-- 2. Metric Stat Cards (5 Cards Row) -->
    <div class="locations-stats-grid">
        <!-- 1. Active Users -->
        <div class="stat-card-location">
            <div class="stat-icon-circle purple">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Active Users</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->anonymous_users_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Photo Events -->
        <div class="stat-card-location">
            <div class="stat-icon-circle green">
                <i class="fa-regular fa-image"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Photo Events</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->photos_captured_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Peak Hour -->
        <div class="stat-card-location">
            <div class="stat-icon-circle purple">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Peak Hour</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ $location->peak_hour }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Peak Day -->
        <div class="stat-card-location">
            <div class="stat-icon-circle orange">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Peak Day</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ $location->peak_day }}</span>
                </div>
            </div>
        </div>

        <!-- 5. Top Area -->
        <div class="stat-card-location">
            <div class="stat-icon-circle cyan">
                <i class="fa-solid fa-location-crosshairs"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Top Area</span>
                <div class="stat-value-group">
                    <span class="stat-number fs-15">{{ $location->top_area_name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Middle Row: Density Heatmap Map (Left) & Activity by Hour Matrix (Right) -->
    <div class="row g-3 mb-3">
        
        <!-- Left: Leaflet Density Heatmap Canvas -->
        <div class="col-lg-6">
            <div class="card border rounded-3 h-100 shadow-sm overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0">Activity Heatmap</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active" id="btnHeatOsm">Map</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnHeatSat">Satellite</button>
                    </div>
                </div>
                <div class="card-body p-0 position-relative" style="min-height: 380px;">
                    <div id="densityLeafletHeatmap" style="height: 380px; width: 100%;"></div>

                    <!-- Bottom Intensity Legend on Map -->
                    <div class="position-absolute bottom-0 start-0 m-3 p-2 bg-white rounded-2 border shadow-sm" style="z-index: 1000; width: 180px;">
                        <div class="d-flex justify-content-between fs-10 text-secondary mb-1">
                            <span>Low</span>
                            <span>High</span>
                        </div>
                        <div style="height: 6px; border-radius: 9999px; background: linear-gradient(to right, #3b82f6, #06b6d4, #eab308, #f97316, #ef4444);"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: 7x24 Hour x Day Activity Heatmap Matrix -->
        <div class="col-lg-6">
            <div class="activity-matrix-card">
                <div class="matrix-header">
                    <h6 class="matrix-title">Activity by Hour</h6>
                    <span class="peak-pill">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        <span>Peak: {{ $location->peak_day }}, {{ $location->peak_hour }}</span>
                    </span>
                </div>

                @php
                    $matrix = $location->activity_by_hour_matrix ?? [
                        'Mon' => [0,0,0,0,1,1,2,3,4,4,5,5,4,4,5,6,7,7,8,7,6,4,2,1],
                        'Tue' => [0,0,0,0,1,1,2,3,4,4,5,5,4,4,5,6,7,7,8,7,6,4,2,1],
                        'Wed' => [0,0,0,0,1,1,2,3,4,5,5,5,5,4,5,6,7,8,8,7,6,4,2,1],
                        'Thu' => [0,0,0,0,1,1,2,3,4,5,5,5,5,5,6,6,7,8,8,8,6,4,2,1],
                        'Fri' => [0,0,0,0,1,1,2,3,4,5,6,6,5,5,6,7,8,8,9,8,7,5,3,1],
                        'Sat' => [0,0,0,0,1,2,3,4,5,6,7,7,6,6,7,8,8,9,9,9,8,6,4,2],
                        'Sun' => [0,0,0,0,1,2,3,4,5,6,7,8,7,7,8,9,9,10,10,9,8,6,4,2],
                    ];
                    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                @endphp

                <div class="matrix-table-wrap">
                    <div class="matrix-grid">
                        <!-- Hour Header Labels -->
                        <div></div>
                        <div class="hour-header-cell">12 AM</div>
                        <div class="hour-header-cell">4 AM</div>
                        <div class="hour-header-cell">8 AM</div>
                        <div class="hour-header-cell">12 PM</div>
                        <div class="hour-header-cell">4 PM</div>
                        <div class="hour-header-cell">8 PM</div>

                        <!-- 7 Days x 24 Hours Heatmap Blocks -->
                        @foreach($days as $day)
                            <div class="day-label-cell">{{ $day }}</div>
                            @for($h = 0; $h < 24; $h++)
                                @php $level = $matrix[$day][$h] ?? 0; @endphp
                                <div class="heat-box h-{{ $level }}" title="{{ $day }} {{ $h }}:00 - Intensity {{ $level }}/10"></div>
                            @endfor
                        @endforeach
                    </div>
                </div>

                <!-- Footer Gradient Legend -->
                <div class="matrix-legend-footer">
                    <span>Low Activity</span>
                    <div class="gradient-line"></div>
                    <span>High Activity</span>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. Bottom Row: 3 Analytics Breakdown Cards -->
    <div class="row g-3">
        
        <!-- 1. Top Activity Areas (Bars) -->
        <div class="col-lg-4">
            <div class="location-analytics-card h-100">
                <h6 class="card-top-title">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Top Activity Areas</span>
                </h6>

                @php $areas = $location->activity_areas ?? []; @endphp
                @foreach($areas as $area)
                    <div class="progress-stat-row mb-3">
                        <div class="stat-label-row">
                            <span class="fw-semibold text-dark">{{ $area['name'] }}</span>
                            <span class="stat-pct">{{ $area['percentage'] }}%</span>
                        </div>
                        <div class="custom-progress-track">
                            <div class="progress-fill blue" style="width: {{ $area['percentage'] * 3.5 }}%;"></div>
                        </div>
                    </div>
                @endforeach
                
                <span class="fs-11 text-muted">Percentage of total photo events</span>
            </div>
        </div>

        <!-- 2. Activity Type (Donut Chart) -->
        <div class="col-lg-4">
            <div class="location-analytics-card h-100 text-center">
                <h6 class="card-top-title text-start">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Activity Type</span>
                </h6>

                <div class="position-relative d-inline-block mx-auto my-2" style="width: 140px; height: 140px;">
                    <canvas id="activityTypeDonut"></canvas>
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <span class="fw-bold fs-14 text-dark">{{ number_format($location->photos_captured_count) }}</span>
                        <div class="fs-9 text-muted">Total Events</div>
                    </div>
                </div>

                @php $types = $location->activity_types ?? ['photo_capture' => 54, 'photo_save' => 23, 'location_stamp' => 15, 'share' => 8]; @endphp
                <div class="d-flex flex-column gap-2 text-start fs-12 mt-2 px-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #2563eb;"></span>
                            <span class="text-dark">Photo Capture</span>
                        </div>
                        <span class="fw-semibold text-dark">{{ $types['photo_capture'] ?? 54 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                            <span class="text-dark">Photo Save</span>
                        </div>
                        <span class="fw-semibold text-dark">{{ $types['photo_save'] ?? 23 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #a855f7;"></span>
                            <span class="text-dark">Location Stamp</span>
                        </div>
                        <span class="fw-semibold text-dark">{{ $types['location_stamp'] ?? 15 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #f97316;"></span>
                            <span class="text-dark">Share</span>
                        </div>
                        <span class="fw-semibold text-dark">{{ $types['share'] ?? 8 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Daily Pattern Line Chart -->
        <div class="col-lg-4">
            <div class="location-analytics-card h-100">
                <h6 class="card-top-title">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Daily Pattern (Last 7 Days)</span>
                </h6>

                <div style="height: 180px; width: 100%;">
                    <canvas id="dailyPatternChart"></canvas>
                </div>

                <div class="d-flex justify-content-between fs-11 text-muted mt-2">
                    <span>Total photo events</span>
                    <span class="fw-semibold text-success">↑ 18.2% vs last week</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Disclaimer Note -->
    <div class="table-footer-disclaimer mt-3">
        <i class="fa-solid fa-circle-info"></i>
        <span>Heatmap data is aggregated and cannot identify an individual device or user.</span>
    </div>

</div>

@endsection

@push('scripts')
<!-- Leaflet Heat & Chart.js Integration -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cityLat = {{ $location->latitude }};
    const cityLng = {{ $location->longitude }};
    const areas = @json($location->activity_areas ?? []);

    // 1. Initialize Leaflet Density Heatmap
    if (window.L) {
        const map = L.map('densityLeafletHeatmap', {
            center: [cityLat, cityLng],
            zoom: 12,
            zoomControl: true,
        });

        // OSM Layer
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Satellite Layer
        const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri'
        });

        document.getElementById('btnHeatOsm')?.addEventListener('click', function() {
            map.removeLayer(satLayer);
            map.addLayer(osmLayer);
            this.classList.add('active');
            document.getElementById('btnHeatSat')?.classList.remove('active');
        });

        document.getElementById('btnHeatSat')?.addEventListener('click', function() {
            map.removeLayer(osmLayer);
            map.addLayer(satLayer);
            this.classList.add('active');
            document.getElementById('btnHeatOsm')?.classList.remove('active');
        });

        // Heatmap Points
        const heatPoints = [];
        areas.forEach(a => {
            const count = a.raw_count || 1000;
            const intensity = count > 2000 ? 1.0 : (count > 1500 ? 0.8 : 0.5);
            // Multi-point jitter for realistic density spread
            for (let i = 0; i < 20; i++) {
                const jLat = a.lat + (Math.random() - 0.5) * 0.015;
                const jLng = a.lng + (Math.random() - 0.5) * 0.015;
                heatPoints.push([jLat, jLng, intensity]);
            }
        });

        // Render Leaflet.heat layer if available
        if (typeof L.heatLayer === 'function') {
            L.heatLayer(heatPoints, {
                radius: 35,
                blur: 25,
                maxZoom: 15,
                gradient: {
                    0.2: '#3b82f6',
                    0.4: '#06b6d4',
                    0.6: '#eab308',
                    0.8: '#f97316',
                    1.0: '#ef4444'
                }
            }).addTo(map);
        } else {
            // Fallback circles if heatLayer is not enabled
            areas.forEach(a => {
                L.circle([a.lat, a.lng], {
                    color: '#ef4444',
                    fillColor: '#f97316',
                    fillOpacity: 0.35,
                    radius: 1200,
                    weight: 1
                }).addTo(map);
            });
        }
    }

    // 2. Activity Type Donut Chart
    if (window.Chart) {
        const ctxDonut = document.getElementById('activityTypeDonut');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Photo Capture', 'Photo Save', 'Location Stamp', 'Share'],
                    datasets: [{
                        data: [54, 23, 15, 8],
                        backgroundColor: ['#2563eb', '#10b981', '#a855f7', '#f97316'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        // 3. Daily Pattern Line Chart
        const ctxLine = document.getElementById('dailyPatternChart');
        if (ctxLine) {
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Photo Events',
                        data: [9800, 11200, 12400, 13800, 14900, 16500, 18900],
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                callback: (val) => val >= 1000 ? (val / 1000) + 'K' : val,
                                font: { size: 10 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
