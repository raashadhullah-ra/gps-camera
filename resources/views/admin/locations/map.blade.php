@extends('layout')

@section('title', $location->city . ' Map View - GPS Camera Admin')
@section('page_title', $location->city . ' Map View')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Installations</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.locations.index') }}">Locations</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.locations.show', $location->id) }}">{{ $location->city }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active-crumb">Map View</span>
@endsection

@section('content')
<div class="locations-page">

    <!-- 1. Top Header with Action Buttons -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">{{ $location->city }} Map View</h1>
            <p class="text-secondary fs-13 mb-0">Aggregated GPS Camera activity across the city</p>
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
                    <li><a class="dropdown-item py-1" href="javascript:void(0)">Custom Range</a></li>
                </ul>
            </div>
            <a href="{{ route('admin.locations.export', ['search' => $location->city]) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-download"></i>
                <span>Export Map Data</span>
            </a>
        </div>
    </div>

    <!-- 2. Metric Stat Cards Row -->
    <div class="locations-stats-grid">
        <!-- 1. Users -->
        <div class="stat-card-location">
            <div class="stat-icon-circle purple">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Users</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->anonymous_users_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Devices -->
        <div class="stat-card-location">
            <div class="stat-icon-circle green">
                <i class="fa-solid fa-mobile-screen"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Devices</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->devices_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Photos -->
        <div class="stat-card-location">
            <div class="stat-icon-circle purple">
                <i class="fa-regular fa-image"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Photos</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->photos_captured_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 4. New Installs -->
        <div class="stat-card-location">
            <div class="stat-icon-circle blue">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">New Installs</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->new_installs_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 5. Updated -->
        <div class="stat-card-location">
            <div class="stat-icon-circle orange">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Updated</span>
                <div class="stat-value-group">
                    <span class="stat-number fs-15">{{ $location->last_activity_human }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Interactive Leaflet Cluster Map Shell -->
    <div class="map-view-shell position-relative">
        
        <!-- Top Floating Controls -->
        <div class="map-floating-header">
            <!-- Search Area Box -->
            <div class="map-search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="mapSearchAreaInput" placeholder="Search area...">
            </div>

            <!-- Map / Satellite Mode Buttons -->
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn-map-control active" id="btnModeMap">Map</button>
                <button type="button" class="btn-map-control" id="btnModeSat">Satellite</button>
            </div>

            <!-- Activity / Installations Mode -->
            <div class="btn-group btn-group-sm ms-2">
                <button type="button" class="btn-map-control active" id="btnModeActivity">Activity</button>
                <button type="button" class="btn-map-control" id="btnModeInstalls">Installations</button>
            </div>

            <!-- Fullscreen Action -->
            <button type="button" class="btn-map-control" onclick="toggleMapFullscreen()" title="Toggle Fullscreen">
                <i class="fa-solid fa-expand"></i>
            </button>
        </div>

        <!-- Map Container -->
        <div id="interactiveClusterMap" class="leaflet-map-canvas" style="height: 580px;"></div>

        <!-- Right Layers Control Panel -->
        <div class="map-layers-panel">
            <h6 class="layers-title">Map Layers</h6>

            <div class="layer-toggle-row">
                <span class="layer-label"><i class="fa-solid fa-fire text-danger"></i> Activity Heatmap</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="layerToggleHeatmap" checked>
                </div>
            </div>

            <div class="layer-toggle-row">
                <span class="layer-label"><i class="fa-solid fa-cubes text-primary"></i> Device Clusters</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="layerToggleClusters" checked>
                </div>
            </div>

            <div class="layer-toggle-row">
                <span class="layer-label"><i class="fa-solid fa-cloud-arrow-down text-info"></i> New Installs</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="layerToggleInstalls">
                </div>
            </div>

            <div class="layer-toggle-row">
                <span class="layer-label"><i class="fa-solid fa-camera text-success"></i> Photo Activity</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="layerTogglePhotos" checked>
                </div>
            </div>

            <!-- Intensity Gradient Legend -->
            <div class="intensity-legend-block">
                <div class="intensity-label-row">
                    <span>Low</span>
                    <span>High</span>
                </div>
                <div class="gradient-bar"></div>
            </div>

            <!-- Top Ranked Areas -->
            <div class="top-areas-list">
                <h6 class="top-areas-title">Top Areas</h6>
                @php $areas = $location->activity_areas ?? []; @endphp
                @foreach($areas as $index => $area)
                    <div class="area-rank-item">
                        <div class="area-name-group">
                            <span class="rank-badge">{{ $index + 1 }}</span>
                            <span class="area-text">{{ $area['name'] }}</span>
                        </div>
                        <span class="area-count">{{ number_format($area['raw_count'] ?? 1000) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Bottom Disclaimer Note -->
    <div class="table-footer-disclaimer">
        <i class="fa-solid fa-circle-info"></i>
        <span>Map clusters represent aggregated activity and do not expose individual device locations.</span>
    </div>

</div>

@endsection

@push('scripts')
<!-- Leaflet Cluster Hotspots Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cityLat = {{ $location->latitude }};
    const cityLng = {{ $location->longitude }};
    const areas = @json($location->activity_areas ?? []);

    if (window.L) {
        const map = L.map('interactiveClusterMap', {
            center: [cityLat, cityLng],
            zoom: 13,
            zoomControl: false,
        });

        // Add Zoom Control at bottom right
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Tile Layers
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri'
        });

        // Tile Toggle Events
        document.getElementById('btnModeMap')?.addEventListener('click', function() {
            map.removeLayer(satLayer);
            map.addLayer(osmLayer);
            this.classList.add('active');
            document.getElementById('btnModeSat')?.classList.remove('active');
        });

        document.getElementById('btnModeSat')?.addEventListener('click', function() {
            map.removeLayer(osmLayer);
            map.addLayer(satLayer);
            this.classList.add('active');
            document.getElementById('btnModeMap')?.classList.remove('active');
        });

        // Layer Groups
        const clusterLayerGroup = L.layerGroup().addTo(map);
        const circlesLayerGroup = L.layerGroup().addTo(map);

        // Render Hotspot Area Circles and Center Badges
        areas.forEach((area) => {
            let colorClass = 'bubble-red';
            let circleColor = '#ef4444';
            let radius = 1200;

            if (area.level === 'high') {
                colorClass = 'bubble-red';
                circleColor = '#ef4444';
                radius = 1400;
            } else if (area.level === 'medium-high') {
                colorClass = 'bubble-orange';
                circleColor = '#f97316';
                radius = 1100;
            } else if (area.level === 'medium') {
                colorClass = 'bubble-yellow';
                circleColor = '#eab308';
                radius = 900;
            } else {
                colorClass = 'bubble-green';
                circleColor = '#10b981';
                radius = 750;
            }

            // Radial Glow Outer Circle
            L.circle([area.lat, area.lng], {
                color: circleColor,
                fillColor: circleColor,
                fillOpacity: 0.18,
                radius: radius,
                weight: 2
            }).addTo(circlesLayerGroup);

            // Center Bubble Marker
            const markerHtml = `
                <div class="cluster-bubble-marker ${colorClass}" style="width: 80px; height: 80px;">
                    <div class="bubble-count">${area.count}</div>
                    <div class="bubble-name">${area.name}</div>
                </div>
            `;

            const icon = L.divIcon({
                className: 'custom-bubble-wrap',
                html: markerHtml,
                iconSize: [80, 80],
                iconAnchor: [40, 40]
            });

            L.marker([area.lat, area.lng], { icon: icon })
                .addTo(clusterLayerGroup)
                .bindPopup(`<strong>${area.name}</strong><br>Activity Count: ${area.raw_count.toLocaleString()}<br>Share: ${area.percentage}%`);
        });

        // Layer Switches
        document.getElementById('layerToggleClusters')?.addEventListener('change', function(e) {
            if (e.target.checked) {
                map.addLayer(clusterLayerGroup);
            } else {
                map.removeLayer(clusterLayerGroup);
            }
        });

        document.getElementById('layerToggleHeatmap')?.addEventListener('change', function(e) {
            if (e.target.checked) {
                map.addLayer(circlesLayerGroup);
            } else {
                map.removeLayer(circlesLayerGroup);
            }
        });

        // Area Search Autocomplete / Fly-to
        document.getElementById('mapSearchAreaInput')?.addEventListener('input', function(e) {
            const val = e.target.value.toLowerCase().trim();
            const match = areas.find(a => a.name.toLowerCase().includes(val));
            if (match) {
                map.flyTo([match.lat, match.lng], 15, { duration: 1.2 });
            }
        });
    }
});

function toggleMapFullscreen() {
    const el = document.getElementById('interactiveClusterMap');
    if (!document.fullscreenElement) {
        el.requestFullscreen().catch(err => alert(err.message));
    } else {
        document.exitFullscreen();
    }
}
</script>
@endpush
