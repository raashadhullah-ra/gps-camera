@extends('layout')

@section('title', 'Dashboard Overview - GPS Camera Admin')
@section('page_title', 'Dashboard')

@section('content')
<div class="dashboard-page">
    
    <!-- Header Titles & Filter Controls -->
    <div class="dashboard-header">
        <div class="header-titles">
            <h1 class="page-title">Dashboard Overview</h1>
            <div class="page-subtitle">Monitor installations, engagement, app health and remote controls</div>
        </div>

        <div class="header-actions">
            <button class="date-filter-btn">
                <i class="fa-regular fa-calendar-days"></i>
                <span>Last 30 days</span>
                <i class="fa-solid fa-chevron-down ms-1 fs-11"></i>
            </button>
            <button class="send-notif-btn">
                <i class="fa-solid fa-plus"></i>
                <span>Send Notification</span>
            </button>
        </div>
    </div>

    <!-- Top 5 Stat Cards Grid (5 Cards Strictly in Single Line) -->
    <div class="stat-grid-row">
        <div class="stat-card">
            <div class="stat-icon-wrapper blue-bg">
                <i class="fa-solid fa-download"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Installations</div>
                <div class="stat-value">128,450</div>
                <div class="stat-trend"><i class="fa-solid fa-arrow-up"></i> +12.8%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper green-bg">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active Devices</div>
                <div class="stat-value">94,280</div>
                <div class="stat-trend"><i class="fa-solid fa-arrow-up"></i> +8.4%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper purple-bg">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">New Installs</div>
                <div class="stat-value">6,842</div>
                <div class="stat-trend"><i class="fa-solid fa-arrow-up"></i> +15.2%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper orange-bg">
                <i class="fa-regular fa-bell"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Notification Enabled</div>
                <div class="stat-value">81.6%</div>
                <div class="stat-trend"><i class="fa-solid fa-arrow-up"></i> +5.6%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper teal-bg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Crash-Free Users</div>
                <div class="stat-value">99.42%</div>
                <div class="stat-trend"><i class="fa-solid fa-arrow-up"></i> +0.38%</div>
            </div>
        </div>
    </div>

    <!-- Middle Row: Chart.js Line Chart + Donut Chart + Quick Controls -->
    <div class="charts-grid-row">
        <!-- Installations Chart.js Line Chart -->
        <div class="chart-card">
            <div class="card-header-bar">
                <div class="card-title">Installations & Active Devices</div>
                <select class="header-select">
                    <option>Daily</option>
                    <option>Weekly</option>
                </select>
            </div>
            <div class="chart-legend-row">
                <div class="legend-item">
                    <span class="legend-dot blue"></span>
                    <span>Installations</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot teal"></span>
                    <span>Active Devices</span>
                </div>
            </div>
            <div class="position-relative" style="height: 240px; width: 100%;">
                <canvas id="installationsChart"></canvas>
            </div>
        </div>

        <!-- Platform Distribution Chart.js Donut Chart -->
        <div class="chart-card">
            <div class="card-header-bar">
                <div class="card-title">Platform Distribution</div>
            </div>
            <div class="d-flex align-items-center justify-content-between h-100">
                <div style="width: 140px; height: 140px; position: relative;">
                    <canvas id="platformDonutChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                        <div class="fw-bold fs-14 text-dark">128,450</div>
                        <div class="fs-10 text-muted">Total</div>
                    </div>
                </div>
                <div class="ps-3 flex-grow-1">
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #0d6efd;"></span>
                            <span class="fs-13 fw-semibold">Android</span>
                            <span class="fs-13 fw-bold ms-auto">86%</span>
                        </div>
                        <div class="fs-11 text-muted ms-3">110,087</div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #0d9488;"></span>
                            <span class="fs-13 fw-semibold">iOS</span>
                            <span class="fs-13 fw-bold ms-auto">14%</span>
                        </div>
                        <div class="fs-11 text-muted ms-3">18,363</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Controls Card & Crash Alert -->
        <div>
            <div class="chart-card quick-controls-card mb-0">
                <div class="card-header-bar mb-2">
                    <div class="card-title">Quick Controls</div>
                </div>

                <div class="control-item">
                    <div class="control-label">Force App Update <i class="fa-regular fa-circle-question ms-1"></i></div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox">
                    </div>
                </div>

                <div class="control-item">
                    <div class="control-label">Firebase Notifications <i class="fa-regular fa-circle-question ms-1"></i></div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>

                <div class="control-item">
                    <div class="control-label">Show AdMob Ads <i class="fa-regular fa-circle-question ms-1"></i></div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>

                <button class="open-config-btn">
                    <i class="fa-solid fa-gear"></i> Open Remote Config
                </button>
            </div>

            <!-- Crash Warning Box -->
            <div class="crash-alert-card">
                <div class="crash-alert-header">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>2 crash issues require attention</span>
                </div>
                <a href="#" class="crash-report-link">
                    <span>View Crash Reports</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Mid Row: Top Locations & Device Breakdown -->
    <div class="location-device-row">
        <!-- Top Locations Card -->
        <div class="chart-card">
            <div class="card-header-bar">
                <div class="card-title"><i class="fa-solid fa-globe me-2 text-muted"></i> Top Locations by Devices</div>
            </div>
            <div class="progress-bar-list">
                <div class="bar-item">
                    <span class="item-name">India</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 41.2%;"></div></div>
                    <span class="item-value">41.2%</span>
                </div>
                <div class="bar-item">
                    <span class="item-name">United States</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 12.6%;"></div></div>
                    <span class="item-value">12.6%</span>
                </div>
                <div class="bar-item">
                    <span class="item-name">Brazil</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 7.8%;"></div></div>
                    <span class="item-value">7.8%</span>
                </div>
                <div class="bar-item">
                    <span class="item-name">Indonesia</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 5.6%;"></div></div>
                    <span class="item-value">5.6%</span>
                </div>
                <div class="bar-item">
                    <span class="item-name">United Kingdom</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 4.9%;"></div></div>
                    <span class="item-value">4.9%</span>
                </div>
            </div>
        </div>

        <!-- Device & OS Card -->
        <div class="chart-card">
            <div class="card-header-bar">
                <div class="card-title"><i class="fa-solid fa-mobile-screen-button me-2 text-muted"></i> Device & OS</div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="progress-bar-list">
                        <div class="bar-item">
                            <span class="item-name" style="width: 70px;">Samsung</span>
                            <div class="bar-track"><div class="bar-fill teal" style="width: 34%;"></div></div>
                            <span class="item-value">34%</span>
                        </div>
                        <div class="bar-item">
                            <span class="item-name" style="width: 70px;">Xiaomi</span>
                            <div class="bar-track"><div class="bar-fill teal" style="width: 22%;"></div></div>
                            <span class="item-value">22%</span>
                        </div>
                        <div class="bar-item">
                            <span class="item-name" style="width: 70px;">Vivo</span>
                            <div class="bar-track"><div class="bar-fill teal" style="width: 16%;"></div></div>
                            <span class="item-value">16%</span>
                        </div>
                        <div class="bar-item">
                            <span class="item-name" style="width: 70px;">OnePlus</span>
                            <div class="bar-track"><div class="bar-fill teal" style="width: 10%;"></div></div>
                            <span class="item-value">10%</span>
                        </div>
                        <div class="bar-item">
                            <span class="item-name" style="width: 70px;">Others</span>
                            <div class="bar-track"><div class="bar-fill teal" style="width: 18%;"></div></div>
                            <span class="item-value">18%</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="progress-bar-list">
                        <div class="bar-item">
                            <span class="item-name" style="width: 80px;">Android 15</span>
                            <div class="bar-track"><div class="bar-fill" style="width: 41%;"></div></div>
                            <span class="item-value">41%</span>
                        </div>
                        <div class="bar-item">
                            <span class="item-name" style="width: 80px;">Android 14</span>
                            <div class="bar-track"><div class="bar-fill" style="width: 33%;"></div></div>
                            <span class="item-value">33%</span>
                        </div>
                        <div class="bar-item">
                            <span class="item-name" style="width: 80px;">Android 13</span>
                            <div class="bar-track"><div class="bar-fill" style="width: 17%;"></div></div>
                            <span class="item-value">17%</span>
                        </div>
                        <div class="bar-item">
                            <span class="item-name" style="width: 80px;">iOS 17</span>
                            <div class="bar-track"><div class="bar-fill" style="width: 8%;"></div></div>
                            <span class="item-value">8%</span>
                        </div>
                        <div class="bar-item">
                            <span class="item-name" style="width: 80px;">iOS 16</span>
                            <div class="bar-track"><div class="bar-fill" style="width: 1%;"></div></div>
                            <span class="item-value">1%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Installations Table (Left-Aligned Filter Tabs) -->
    <div class="table-card-wrapper">
        <div class="table-card-header">
            <div class="table-title">Recent Installations</div>
            <div class="table-nav-tabs ms-3">
                <a href="#" class="tab-link active">All</a>
                <a href="#" class="tab-link">Active</a>
                <a href="#" class="tab-link">Inactive</a>
                <a href="#" class="tab-link">Notifications Off</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Installation ID</th>
                        <th>Device</th>
                        <th>Platform / OS</th>
                        <th>Location</th>
                        <th>App Version</th>
                        <th>Last Active</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="install-id">INS-8F29A1</span></td>
                        <td><span class="device-info">Samsung Galaxy S24</span></td>
                        <td><i class="fa-brands fa-android text-success me-1"></i> Android 15</td>
                        <td>Tirunelveli, India</td>
                        <td>v1.4.2</td>
                        <td>2 min ago</td>
                        <td><span class="badge-status badge-success"><span class="status-dot"></span> Active</span></td>
                        <td>
                            <a href="{{ route('admin.devices.index') }}" class="btn-action-icon me-1" title="View Device"><i class="fa-regular fa-eye"></i></a>
                            <button type="button" class="btn-action-dots" title="More Actions"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="install-id">INS-A7C3D9</span></td>
                        <td><span class="device-info">iPhone 16</span></td>
                        <td><i class="fa-brands fa-apple text-dark me-1"></i> iOS 17.4.1</td>
                        <td>New York, United States</td>
                        <td>v1.4.2</td>
                        <td>5 min ago</td>
                        <td><span class="badge-status badge-success"><span class="status-dot"></span> Active</span></td>
                        <td>
                            <a href="{{ route('admin.devices.index') }}" class="btn-action-icon me-1" title="View Device"><i class="fa-regular fa-eye"></i></a>
                            <button type="button" class="btn-action-dots" title="More Actions"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="install-id">INS-B1D8F4</span></td>
                        <td><span class="device-info">Xiaomi 14</span></td>
                        <td><i class="fa-brands fa-android text-success me-1"></i> Android 14</td>
                        <td>Sao Paulo, Brazil</td>
                        <td>v1.4.1</td>
                        <td>12 min ago</td>
                        <td><span class="badge-status badge-success"><span class="status-dot"></span> Active</span></td>
                        <td>
                            <a href="{{ route('admin.devices.index') }}" class="btn-action-icon me-1" title="View Device"><i class="fa-regular fa-eye"></i></a>
                            <button type="button" class="btn-action-dots" title="More Actions"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="install-id">INS-C9E2B7</span></td>
                        <td><span class="device-info">Vivo V40</span></td>
                        <td><i class="fa-brands fa-android text-success me-1"></i> Android 14</td>
                        <td>Jakarta, Indonesia</td>
                        <td>v1.4.1</td>
                        <td>18 min ago</td>
                        <td><span class="badge-status badge-success"><span class="status-dot"></span> Active</span></td>
                        <td>
                            <a href="{{ route('admin.devices.index') }}" class="btn-action-icon me-1" title="View Device"><i class="fa-regular fa-eye"></i></a>
                            <button type="button" class="btn-action-dots" title="More Actions"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="install-id">INS-D4F6G2</span></td>
                        <td><span class="device-info">OnePlus 13</span></td>
                        <td><i class="fa-brands fa-android text-success me-1"></i> Android 15</td>
                        <td>London, United Kingdom</td>
                        <td>v1.4.2</td>
                        <td>25 min ago</td>
                        <td><span class="badge-status badge-success"><span class="status-dot"></span> Active</span></td>
                        <td>
                            <a href="{{ route('admin.devices.index') }}" class="btn-action-icon me-1" title="View Device"><i class="fa-regular fa-eye"></i></a>
                            <button type="button" class="btn-action-dots" title="More Actions"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Chart.js Installations & Active Devices Line Chart
    const lineCanvas = document.getElementById('installationsChart');
    if (lineCanvas && window.Chart) {
        const ctx = lineCanvas.getContext('2d');
        const blueGradient = ctx.createLinearGradient(0, 0, 0, 240);
        blueGradient.addColorStop(0, 'rgba(37, 99, 235, 0.16)');
        blueGradient.addColorStop(1, 'rgba(37, 99, 235, 0.00)');

        new Chart(lineCanvas, {
            type: 'line',
            data: {
                labels: [
                    'Apr 27', '', '',
                    'Apr 30', '', '',
                    'May 3', '', '',
                    'May 6', '', '',
                    'May 9', '', '',
                    'May 12', '', '',
                    'May 15', '', '',
                    'May 18', '', '',
                    'May 21', '', '',
                    'May 24', '', '',
                    'May 27', ''
                ],
                datasets: [
                    {
                        label: 'Installations',
                        data: [
                            7000, 6400, 7300, 7000, 8900, 8200, 7600, 7300, 9400, 7800, 6400,
                            6900, 6700, 8100, 9200, 7400, 7800, 6800, 8800, 8300, 7300, 8900,
                            8100, 6800, 8200, 7400, 7800, 8600, 7800, 6700, 7400, 7500
                        ],
                        borderColor: '#2563eb',
                        backgroundColor: blueGradient,
                        borderWidth: 2,
                        tension: 0,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                        fill: true
                    },
                    {
                        label: 'Active Devices',
                        data: [
                            3700, 3300, 3900, 3800, 5100, 4500, 4000, 3800, 5000, 4700, 3900,
                            4100, 3600, 4300, 5100, 4200, 4300, 3900, 4900, 4600, 4100, 5100,
                            4700, 3900, 4800, 4400, 4500, 5100, 4700, 4400, 4800, 5200
                        ],
                        borderColor: '#0d9488',
                        borderWidth: 2,
                        tension: 0,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#0d9488',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, family: 'Inter', weight: '600' },
                        bodyFont: { size: 12, family: 'Inter' },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ` ${context.dataset.label}: ${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11, family: 'Inter' },
                            color: '#64748b',
                            autoSkip: false
                        }
                    },
                    y: {
                        min: 0,
                        max: 10000,
                        grid: {
                            color: '#f1f5f9',
                            borderDash: [3, 3]
                        },
                        ticks: {
                            stepSize: 2000,
                            font: { size: 11, family: 'Inter' },
                            color: '#64748b',
                            callback: function(value) {
                                return value === 0 ? '0' : (value / 1000) + 'K';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Chart.js Platform Distribution Donut Chart
    const donutCtx = document.getElementById('platformDonutChart');
    if (donutCtx && window.Chart) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Android', 'iOS'],
                datasets: [{
                    data: [86, 14],
                    backgroundColor: ['#0d6efd', '#0d9488'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        bodyFont: { size: 12, family: 'Inter' },
                        padding: 8,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return ` ${context.label}: ${context.parsed}%`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
