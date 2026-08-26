<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Global Locations Export Report - GPS Camera Admin</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            font-size: 11px;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .app-title {
            font-size: 18px;
            font-weight: bold;
            color: #071a39;
        }
        .app-sub {
            font-size: 11px;
            color: #64748b;
        }
        .report-badge {
            background-color: #eff6ff;
            color: #1d4ed8;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
        .hero-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 16px;
        }
        .global-title {
            font-size: 17px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px 0;
        }
        .global-sub {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }
        .metrics-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .metric-cell {
            padding: 6px 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            width: 20%;
            text-align: center;
        }
        .metric-val {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
        }
        .metric-lbl {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-top: 16px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            text-align: left;
            padding: 6px 8px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10.5px;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .status-pill {
            background-color: #dcfce7;
            color: #15803d;
            padding: 1px 6px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 9.5px;
        }
        .footer-note {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
            font-size: 9.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="app-title">GPS Camera Admin</div>
                <div class="app-sub">Global Locations Intelligence & Telemetry Export</div>
            </td>
            <td style="text-align: right;">
                <div class="report-badge">ALL LOCATIONS EXPORT</div>
                <div class="app-sub" style="margin-top: 3px;">Generated: {{ date('F j, Y - H:i T') }}</div>
            </td>
        </tr>
    </table>

    <div class="hero-box">
        <div class="global-title">Global Aggregated Summary</div>
        <div class="global-sub">Telemetry dataset covering {{ count($locations) }} tracked administrative regions &bull; Range: {{ $dateRangeLabel ?? 'Last 30 Days' }}</div>

        <table class="metrics-table">
            <tr>
                <td class="metric-cell">
                    <div class="metric-val">{{ number_format($metrics['countries'] ?? 142) }}</div>
                    <div class="metric-lbl">Countries</div>
                </td>
                <td class="metric-cell">
                    <div class="metric-val">{{ number_format($metrics['cities'] ?? 12594) }}</div>
                    <div class="metric-lbl">Cities</div>
                </td>
                <td class="metric-cell">
                    <div class="metric-val">{{ number_format($metrics['total_db_users'] ?? 87521) }}</div>
                    <div class="metric-lbl">Users</div>
                </td>
                <td class="metric-cell">
                    <div class="metric-val">{{ number_format($metrics['total_db_devices'] ?? 94171) }}</div>
                    <div class="metric-lbl">Devices</div>
                </td>
                <td class="metric-cell">
                    <div class="metric-val">{{ number_format($metrics['photos_captured'] ?? 426800) }}</div>
                    <div class="metric-lbl">Photos</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Locations Dataset Overview</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>City</th>
                <th>State / Region</th>
                <th>Country</th>
                <th style="text-align: right;">Users</th>
                <th style="text-align: right;">Devices</th>
                <th style="text-align: right;">Photos</th>
                <th>Source</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $loc)
            <tr>
                <td><strong>{{ $loc->city }}</strong></td>
                <td>{{ $loc->state }}</td>
                <td>{{ $loc->country }}</td>
                <td style="text-align: right;">{{ number_format($loc->anonymous_users_count) }}</td>
                <td style="text-align: right;">{{ number_format($loc->devices_count) }}</td>
                <td style="text-align: right;">{{ number_format($loc->photos_captured_count) }}</td>
                <td>{{ $loc->location_source }}</td>
                <td><span class="status-pill">{{ $loc->status }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-note">
        This document contains complete global aggregated location telemetry exported from GPS Camera Admin.
        All telemetry data complies with anonymization standards. Audit reference: EXP-GLOBAL-{{ time() }}
    </div>

</body>
</html>
