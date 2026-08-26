<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $location->city }} - Location Export Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            font-size: 12px;
            line-height: 1.5;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 12px;
            margin-bottom: 20px;
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
            padding: 16px;
            margin-bottom: 20px;
        }
        .city-name {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px 0;
        }
        .region-text {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }
        .metrics-table {
            width: 100%;
            margin-top: 14px;
            border-collapse: collapse;
        }
        .metric-cell {
            padding: 8px 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            width: 25%;
            text-align: center;
        }
        .metric-val {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .metric-lbl {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-top: 20px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            border: 1px solid #e2e8f0;
        }
        .data-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 11.5px;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .status-pill {
            background-color: #dcfce7;
            color: #15803d;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10.5px;
        }
        .footer-note {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px dashed #cbd5e1;
            font-size: 10px;
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
                <div class="app-sub">Aggregated Location Analytics & Device Intelligence</div>
            </td>
            <td style="text-align: right;">
                <div class="report-badge">CONFIDENTIAL REPORT</div>
                <div class="app-sub" style="margin-top: 4px;">Generated: {{ date('F j, Y - H:i T') }}</div>
            </td>
        </tr>
    </table>

    <div class="hero-box">
        <div class="city-name">{{ $location->city }}</div>
        <div class="region-text">{{ $location->state }}, {{ $location->country }} &bull; Coordinates: {{ $location->latitude }}, {{ $location->longitude }}</div>

        <table class="metrics-table">
            <tr>
                <td class="metric-cell">
                    <div class="metric-val">{{ number_format($location->anonymous_users_count) }}</div>
                    <div class="metric-lbl">Anonymous Users</div>
                </td>
                <td class="metric-cell">
                    <div class="metric-val">{{ number_format($location->devices_count) }}</div>
                    <div class="metric-lbl">Total Devices</div>
                </td>
                <td class="metric-cell">
                    <div class="metric-val">{{ number_format($location->photos_captured_count) }}</div>
                    <div class="metric-lbl">Photos Captured</div>
                </td>
                <td class="metric-cell">
                    <div class="metric-val">{{ number_format($location->new_installs_count) }}</div>
                    <div class="metric-lbl">New Installs</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Location Overview & Specifications</div>
    <table class="data-table">
        <tr>
            <th style="width: 35%;">Attribute</th>
            <th>Value</th>
        </tr>
        <tr>
            <td><strong>Location ID</strong></td>
            <td>#LOC-{{ str_pad($location->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td><strong>City / District</strong></td>
            <td>{{ $location->city }}</td>
        </tr>
        <tr>
            <td><strong>State / Region</strong></td>
            <td>{{ $location->state }}</td>
        </tr>
        <tr>
            <td><strong>Country</strong></td>
            <td>{{ $location->country }} ({{ $location->country_code }})</td>
        </tr>
        <tr>
            <td><strong>Location Source</strong></td>
            <td>{{ $location->location_source }}</td>
        </tr>
        <tr>
            <td><strong>Activity Status</strong></td>
            <td><span class="status-pill">{{ $location->status }}</span></td>
        </tr>
        <tr>
            <td><strong>Last Activity Timestamp</strong></td>
            <td>{{ $location->last_activity?->format('Y-m-d H:i:s') ?? 'Recently active' }}</td>
        </tr>
    </table>

    <div class="section-title">Platform & Device Breakdown</div>
    <table class="data-table">
        <tr>
            <th>Platform</th>
            <th>Distribution Percentage</th>
            <th>Estimated Reach</th>
        </tr>
        <tr>
            <td><strong>Android</strong></td>
            <td>{{ $location->platform_distribution['android'] ?? 91 }}%</td>
            <td>{{ number_format(round($location->devices_count * (($location->platform_distribution['android'] ?? 91) / 100))) }} devices</td>
        </tr>
        <tr>
            <td><strong>iOS (Apple)</strong></td>
            <td>{{ $location->platform_distribution['ios'] ?? 9 }}%</td>
            <td>{{ number_format(round($location->devices_count * (($location->platform_distribution['ios'] ?? 9) / 100))) }} devices</td>
        </tr>
    </table>

    <div class="section-title">Export Audit Trail</div>
    <table class="data-table">
        <tr>
            <th style="width: 35%;">Metadata</th>
            <th>Details</th>
        </tr>
        <tr>
            <td><strong>Export Format</strong></td>
            <td>PDF Summary Document (ISO 32000-1 compliant)</td>
        </tr>
        <tr>
            <td><strong>Date Range Selected</strong></td>
            <td>{{ $dateRangeLabel ?? 'Last 30 Days' }}</td>
        </tr>
        <tr>
            <td><strong>Privacy & Anonymization</strong></td>
            <td>Small result groups aggregated; No raw PII exposed</td>
        </tr>
    </table>

    <div class="footer-note">
        This document contains aggregated location statistics exported from GPS Camera Admin.
        All telemetry data complies with anonymization standards. Audit reference: EXP-LOC-{{ $location->id }}-{{ time() }}
    </div>

</body>
</html>
