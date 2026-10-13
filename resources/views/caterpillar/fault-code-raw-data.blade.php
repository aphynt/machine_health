@include('layout.head')
@include('layout.sidebar')
<style>
    .cat-fault-page {
        --mh-blue: #315f9f;
        --mh-blue-light: #eef4ff;
        --mh-border: #e5e7eb;
        --mh-muted: #64748b;
        --mh-bg: #f8fafc;
        --mh-red: #dc2626;
        --mh-orange: #d97706;
        --mh-green: #15803d;
    }
    .cat-fault-page .page-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 3px;
        color: #1e293b;
    }
    .cat-fault-page .page-subtitle {
        color: #64748b;
        font-size: 12px;
        margin-bottom: 0;
    }
    .cat-fault-page .filter-card {
        border: 1px solid var(--mh-border);
        box-shadow: none;
    }
    .cat-fault-page .filter-label {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 5px;
    }
    .cat-fault-page .form-control,
    .cat-fault-page .form-select {
        font-size: 12px;
        min-height: 38px;
    }
    .cat-fault-page .range-info {
        background: var(--mh-blue-light);
        border: 1px solid #dbeafe;
        color: #315f9f;
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 11px;
    }
    .cat-fault-page .report-tabs {
        border-bottom: 1px solid #e5e7eb;
        gap: 4px;
    }
    .cat-fault-page .report-tabs .nav-link {
        border: 0;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        padding: 11px 16px;
        border-radius: 7px 7px 0 0;
    }
    .cat-fault-page .report-tabs .nav-link.active {
        background: #eef4ff;
        color: #315f9f;
        border-bottom: 2px solid #315f9f;
    }
    .cat-fault-page .table {
        font-size: 11px;
        white-space: nowrap;
        margin-bottom: 0;
    }
    .cat-fault-page .table th {
        font-size: 10px;
        font-weight: 700;
        vertical-align: middle;
        background: #f1f5f9;
        color: #334155;
        padding: 8px 7px;
    }
    .cat-fault-page .table td {
        vertical-align: middle;
        padding: 7px;
    }
    .cat-fault-page .fault-message {
        min-width: 270px;
        max-width: 400px;
        white-space: normal;
        font-weight: 600;
        color: #1e293b;
    }
    .cat-fault-page .location-cell {
        min-width: 180px;
        max-width: 280px;
        white-space: normal;
    }
    .cat-fault-page .operator-cell {
        min-width: 150px;
    }
    .badge-severity {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 62px;
        border-radius: 999px;
        padding: 4px 7px;
        font-size: 10px;
        font-weight: 700;
    }
    .badge-low {
        background: #dcfce7;
        color: #15803d;
    }
    .badge-medium {
        background: #fef3c7;
        color: #b45309;
    }
    .badge-high {
        background: #fee2e2;
        color: #b91c1c;
    }
    .analytics-card {
        border: 1px solid #e5e7eb;
        box-shadow: none;
        border-radius: 10px;
        height: 100%;
    }
    .analytics-card .card-body {
        padding: 16px;
    }
    .metric-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .35px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 7px;
    }
    .metric-number {
        font-size: 27px;
        line-height: 1;
        font-weight: 700;
        color: #0f172a;
    }
    .metric-description {
        margin-top: 8px;
        font-size: 10px;
        color: #94a3b8;
    }
    .metric-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        border-radius: 10px;
        background: #eef4ff;
        color: #315f9f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .severity-box {
        border-radius: 9px;
        padding: 13px;
        height: 100%;
        border: 1px solid #e5e7eb;
    }
    .severity-box.low {
        background: #f0fdf4;
    }
    .severity-box.medium {
        background: #fffbeb;
    }
    .severity-box.high {
        background: #fef2f2;
    }
    .severity-title {
        font-size: 11px;
        color: #64748b;
        font-weight: 700;
    }
    .severity-value {
        font-size: 22px;
        font-weight: 700;
        margin-top: 4px;
    }
    .severity-low {
        color: #15803d;
    }
    .severity-medium {
        color: #d97706;
    }
    .severity-high {
        color: #dc2626;
    }
    .analytic-panel {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: white;
        height: 100%;
    }
    .analytic-panel-header {
        padding: 14px 15px;
        border-bottom: 1px solid #e5e7eb;
    }
    .analytic-panel-title {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }
    .analytic-panel-subtitle {
        color: #94a3b8;
        font-size: 10px;
        margin-top: 2px;
    }
    .analytic-panel-body {
        padding: 15px;
    }
    .bar-item {
        margin-bottom: 12px;
    }
    .bar-item:last-child {
        margin-bottom: 0;
    }
    .bar-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 5px;
    }
    .bar-label {
        font-size: 11px;
        color: #475569;
        font-weight: 600;
        line-height: 1.35;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .bar-total {
        font-size: 10px;
        color: #64748b;
        font-weight: 700;
        white-space: nowrap;
    }
    .bar-track {
        background: #f1f5f9;
        width: 100%;
        height: 7px;
        border-radius: 999px;
        overflow: hidden;
    }
    .bar-fill {
        height: 100%;
        background: #4f75b5;
        border-radius: 999px;
        min-width: 3px;
    }
    .bar-fill.orange {
        background: #d97706;
    }
    .bar-fill.green {
        background: #15803d;
    }
    .bar-fill.red {
        background: #dc2626;
    }
    .trend-wrapper {
        min-height: 220px;
        display: flex;
        align-items: flex-end;
        gap: 10px;
        overflow-x: auto;
        padding: 25px 5px 5px;
    }
    .trend-column {
        min-width: 60px;
        flex: 1;
        text-align: center;
    }
    .trend-value {
        font-size: 10px;
        color: #475569;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .trend-bar-area {
        height: 150px;
        display: flex;
        justify-content: center;
        align-items: flex-end;
    }
    .trend-bar {
        width: 34px;
        min-height: 3px;
        background: #4f75b5;
        border-radius: 5px 5px 0 0;
    }
    .trend-label {
        margin-top: 7px;
        font-size: 9px;
        color: #64748b;
        white-space: nowrap;
    }
    .pagination-info {
        font-size: 11px;
        color: #64748b;
    }
    .empty-state {
        padding: 30px;
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
    }
    @media (max-width: 768px) {
        .cat-fault-page .filter-action,
        .cat-fault-page .filter-action .btn {
            width: 100%;
        }
    }
</style>
<div class="page-content cat-fault-page">
    <div class="container-xxl">
        <div class="row mb-3">
            <div class="col-12">
                <div
                    class="d-flex
                           align-items-center
                           justify-content-between
                           flex-wrap
                           gap-2"
                >
                    <div>
                        <h4 class="page-title">
                            Caterpillar Fault Code Raw Data
                        </h4>
                        <p class="page-subtitle">
                            Fault Code Analysis
                        </p>
                    </div>
                    <div>
                        <span class="badge bg-light text-dark border">
                            CAT Machine Health
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card filter-card mb-3">
            <div class="card-body">
                <form
                    method="GET"
                    action="{{ route('caterpillar.fault-code.raw-data') }}"
                >
                    <input
                        type="hidden"
                        name="tab"
                        value="{{ $activeTab }}"
                    >
                    <div class="row g-3 align-items-end">
                        <div class="col-xl-2 col-md-4">
                            <label class="filter-label">
                                Start Date
                            </label>
                            <input
                                type="date"
                                name="start_date"
                                class="form-control"
                                value="{{ $startDate->format('Y-m-d') }}"
                            >
                        </div>
                        <div class="col-xl-2 col-md-4">
                            <label class="filter-label">
                                Stop Date
                            </label>
                            <input
                                type="date"
                                name="stop_date"
                                class="form-control"
                                value="{{ $stopDate->format('Y-m-d') }}"
                            >
                        </div>
                        <div class="col-xl-5 col-md-8">
                            <label class="filter-label">
                                Error Message
                            </label>
                            <select
                                name="error_message"
                                class="form-select"
                            >
                                <option value="">
                                    All Error Message
                                </option>
                                @foreach ($errorMessages as $errorMessage)
                                    <option
                                        value="{{ $errorMessage }}"
                                        {{ $selectedError === $errorMessage ? 'selected' : '' }}
                                    >
                                        {{ $errorMessage }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-4 filter-action">
                            <div class="d-flex gap-2">
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    <i class="fi fi-rr-search me-1"></i>
                                    View Report
                                </button>
                                <a
                                    href="{{ route('caterpillar.fault-code.raw-data') }}"
                                    class="btn btn-light border"
                                >
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="range-info mt-3">
                    <i class="fi fi-rr-calendar me-1"></i>
                    Data Range:
                    <strong>
                        {{ $startDate->format('d M Y H:i:s') }}
                    </strong>
                    s/d
                    <strong>
                        {{ $stopDate->format('d M Y H:i:s') }}
                    </strong>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header pb-0">
                <ul class="nav nav-tabs report-tabs mb-3">
                    <li class="nav-item">
                        <a
                            class="nav-link {{ $activeTab === 'raw' ? 'active' : '' }}"
                            href="{{ route('caterpillar.fault-code.raw-data', [ 'start_date' => $startDate->format('Y-m-d'), 'stop_date' => $stopDate->format('Y-m-d'), 'error_message' => $selectedError, 'tab' => 'raw' ]) }}"
                        >
                            <i class="fi fi-rr-table-list me-1"></i>
                            Raw Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link {{ $activeTab === 'analytics' ? 'active' : '' }}"
                            href="{{ route('caterpillar.fault-code.raw-data', [ 'start_date' => $startDate->format('Y-m-d'), 'stop_date' => $stopDate->format('Y-m-d'), 'error_message' => $selectedError, 'tab' => 'analytics' ]) }}"
                        >
                            <i class="fi fi-rr-chart-histogram me-1"></i>
                            Analytics Dashboard
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                @if ($activeTab === 'raw')
                    <div
                        class="d-flex
                               justify-content-between
                               align-items-center
                               flex-wrap
                               gap-2
                               mb-3"
                    >
                        <div>
                            <h5 class="mb-1 fw-bold">
                                Caterpillar Fault Code Raw Data
                            </h5>
                            <div class="text-muted small">
                                Menampilkan maksimal
                                <strong>
                                    {{ number_format($data?->perPage() ?? 1000) }}
                                </strong>
                                data per halaman.
                                @if ($data)
                                    Data pada halaman ini:
                                    <strong>
                                        {{ number_format($data->count()) }}
                                    </strong>
                                @endif
                            </div>
                        </div>
                        <div>
                            <a
                                href="{{ route('caterpillar.fault-code.raw-data.export', [ 'start_date' => $startDate->format('Y-m-d'), 'stop_date' => $stopDate->format('Y-m-d'), 'error_message' => $selectedError ]) }}"
                                class="btn btn-success"
                            >
                                <i class="fi fi-rr-file-excel me-1"></i>
                                Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-hover align-middle"
                            id="faultTable"
                        >
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Fault Code / Error Message</th>
                                    <th>Low</th>
                                    <th>Medium</th>
                                    <th>High</th>
                                    <th>Fault Date</th>
                                    <th>Unit</th>
                                    <th>Model</th>
                                    <th>Hours Meter</th>
                                    <th>Odometer (km)</th>
                                    <th>Source</th>
                                    <th>Type</th>
                                    <th>Occurrences</th>
                                    <th>Longitude</th>
                                    <th>Latitude</th>
                                    <th>Location</th>
                                    <th>Layer</th>
                                    <th>GPS Speed</th>
                                    <th>PLM Speed</th>
                                    <th>NIK Operator</th>
                                    <th>Fullname</th>
                                    <th>VSA Status</th>
                                    <th>VSA Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data ?? [] as $item)
                                    <tr>
                                        <td>
                                            {{ (($data->currentPage() - 1) * $data->perPage()) + $loop->iteration }}
                                        </td>
                                        <td class="fault-message">
                                            {{ $item->Fault_Code ?? '-' }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge-severity badge-low"
                                            >
                                                {{ (int) ($item->Severity_Level1 ?? 0) }}
                                                Low
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge-severity badge-medium"
                                            >
                                                {{ (int) ($item->Severity_Level2 ?? 0) }}
                                                Medium
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge-severity badge-high"
                                            >
                                                {{ (int) ($item->Severity_Level3 ?? 0) }}
                                                High
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->Fault_Date)
                                                {{ \Carbon\Carbon::parse( $item->Fault_Date )->format( 'Y-m-d H:i:s' ) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="fw-semibold">
                                            {{ $item->Name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $item->Model ?? '-' }}
                                        </td>
                                        <td class="text-end">
                                            {{ is_numeric( $item->Hours_Value ?? null ) ? number_format( (float) $item->Hours_Value, 2 ) : '-' }}
                                        </td>
                                        <td class="text-end">
                                            {{ is_numeric( $item->Odometer_Value ?? null ) ? number_format( (float) $item->Odometer_Value, 2 ) : '-' }}
                                        </td>
                                        <td>
                                            {{ $item->Source ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $item->Type ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $item->Occurrences ?? 0 }}
                                        </td>
                                        <td class="text-end">
                                            {{ is_numeric( $item->GPS_LON ?? null ) ? number_format( (float) $item->GPS_LON, 5 ) : '-' }}
                                        </td>
                                        <td class="text-end">
                                            {{ is_numeric( $item->GPS_LAT ?? null ) ? number_format( (float) $item->GPS_LAT, 5 ) : '-' }}
                                        </td>
                                        <td class="location-cell">
                                            {{ $item->LOC_NAME ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $item->LAYERNAME ?? '-' }}
                                        </td>
                                        <td class="text-end">
                                            {{ is_numeric( $item->GPS_SPEED ?? null ) ? number_format( (float) $item->GPS_SPEED, 2 ) : '-' }}
                                        </td>
                                        <td class="text-end">
                                            {{ is_numeric( $item->PLM_SPEED ?? null ) ? number_format( (float) $item->PLM_SPEED, 2 ) : '-' }}
                                        </td>
                                        <td>
                                            {{ $item->OPR_NRP ?? '-' }}
                                        </td>
                                        <td class="operator-cell">
                                            {{ $item->PERSONALNAME ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $item->VSA_STATUSDESC ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $item->VSA_ACTIVITYDESC ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="23"
                                            class="text-center text-muted py-5"
                                        >
                                            Tidak ada data Fault Code
                                            pada range yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($data && $data->hasPages())
                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-center
                                   flex-wrap
                                   gap-3
                                   mt-3"
                        >
                            <div class="pagination-info">
                                Halaman
                                <strong>
                                    {{ $data->currentPage() }}
                                </strong>
                                · Maksimum
                                <strong>
                                    {{ number_format($data->perPage()) }}
                                </strong>
                                record / halaman
                            </div>
                            <div>
                                {{ $data->links( 'pagination::simple-bootstrap-5' ) }}
                            </div>
                        </div>
                    @endif
                @elseif ($activeTab === 'analytics')
                    <div class="row g-3 mb-3">
                        <div class="col-xl-3 col-md-6">
                            <div class="card analytics-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="metric-label">
                                                Total Fault
                                            </div>
                                            <div class="metric-number">
                                                {{ number_format( $totalRecords ) }}
                                            </div>
                                            <div class="metric-description">
                                                Total fault record
                                            </div>
                                        </div>
                                        <div class="metric-icon">
                                            <i
                                                class="fi fi-rr-triangle-warning"
                                            ></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card analytics-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="metric-label">
                                                Unique Fault
                                            </div>
                                            <div class="metric-number">
                                                {{ number_format( $uniqueFaults ) }}
                                            </div>
                                            <div class="metric-description">
                                                Jenis fault berbeda
                                            </div>
                                        </div>
                                        <div class="metric-icon">
                                            <i class="fi fi-rr-list"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card analytics-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="metric-label">
                                                Units Affected
                                            </div>
                                            <div class="metric-number">
                                                {{ number_format( $uniqueUnits ) }}
                                            </div>
                                            <div class="metric-description">
                                                Unit terdeteksi fault
                                            </div>
                                        </div>
                                        <div class="metric-icon">
                                            <i
                                                class="fi fi-rr-truck-side"
                                            ></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card analytics-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="metric-label">
                                                Operators
                                            </div>
                                            <div class="metric-number">
                                                {{ number_format( $uniqueOperators ) }}
                                            </div>
                                            <div class="metric-description">
                                                Operator teridentifikasi
                                            </div>
                                        </div>
                                        <div class="metric-icon">
                                            <i class="fi fi-rr-user"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="severity-box low">
                                <div class="severity-title">
                                    Severity Low
                                </div>
                                <div
                                    class="severity-value severity-low"
                                >
                                    {{ number_format( $severityLow ) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="severity-box medium">
                                <div class="severity-title">
                                    Severity Medium
                                </div>
                                <div
                                    class="severity-value severity-medium"
                                >
                                    {{ number_format( $severityMedium ) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="severity-box high">
                                <div class="severity-title">
                                    Severity High
                                </div>
                                <div
                                    class="severity-value severity-high"
                                >
                                    {{ number_format( $severityHigh ) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="analytic-panel mb-3">
                        <div class="analytic-panel-header">
                            <h6 class="analytic-panel-title">
                                Daily Fault Trend
                            </h6>
                            <div class="analytic-panel-subtitle">
                                Jumlah fault yang tercatat per hari
                            </div>
                        </div>
                        <div class="analytic-panel-body">
                            @if ($dailyTrend->count())
                                <div class="trend-wrapper">
                                    @foreach ($dailyTrend as $trend)
                                        @php
                                            $trendPercent =
                                                ($trend->total / $maxDaily)
                                                * 100;
                                            $trendHeight =
                                                max(
                                                    5,
                                                    ($trendPercent / 100)
                                                    * 150
                                                );
                                        @endphp
                                        <div class="trend-column">
                                            <div class="trend-value">
                                                {{ number_format( $trend->total ) }}
                                            </div>
                                            <div class="trend-bar-area">
                                                <div
                                                    class="trend-bar"
                                                    style="height: {{ $trendHeight }}px"
                                                ></div>
                                            </div>
                                            <div class="trend-label">
                                                {{ \Carbon\Carbon::parse( $trend->date )->format( 'd M' ) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    Tidak ada data trend.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-xl-6">
                            <div class="analytic-panel">
                                <div class="analytic-panel-header">
                                    <h6 class="analytic-panel-title">
                                        Top 10 Fault Code
                                    </h6>
                                    <div class="analytic-panel-subtitle">
                                        Error message yang paling sering muncul
                                    </div>
                                </div>
                                <div class="analytic-panel-body">
                                    @forelse ($topFaults as $fault)
                                        @php
                                            $percentage =
                                                ($fault->total
                                                / $maxTopFault)
                                                * 100;
                                        @endphp
                                        <div class="bar-item">
                                            <div class="bar-header">
                                                <div
                                                    class="bar-label"
                                                    title="{{ $fault->label }}"
                                                >
                                                    {{ $fault->label }}
                                                </div>
                                                <div class="bar-total">
                                                    {{ number_format( $fault->total ) }}
                                                </div>
                                            </div>
                                            <div class="bar-track">
                                                <div
                                                    class="bar-fill red"
                                                    style="width: {{ $percentage }}%"
                                                ></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            Tidak ada data.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="analytic-panel">
                                <div class="analytic-panel-header">
                                    <h6 class="analytic-panel-title">
                                        Top Units
                                    </h6>
                                    <div class="analytic-panel-subtitle">
                                        Unit dengan fault terbanyak
                                    </div>
                                </div>
                                <div class="analytic-panel-body">
                                    @forelse ($topUnits as $unit)
                                        @php
                                            $percentage =
                                                ($unit->total
                                                / $maxUnit)
                                                * 100;
                                        @endphp
                                        <div class="bar-item">
                                            <div class="bar-header">
                                                <div class="bar-label">
                                                    {{ $unit->label }}
                                                </div>
                                                <div class="bar-total">
                                                    {{ number_format( $unit->total ) }}
                                                </div>
                                            </div>
                                            <div class="bar-track">
                                                <div
                                                    class="bar-fill"
                                                    style="width: {{ $percentage }}%"
                                                ></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            Tidak ada data.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-xl-6">
                            <div class="analytic-panel">
                                <div class="analytic-panel-header">
                                    <h6 class="analytic-panel-title">
                                        Fault by Source
                                    </h6>
                                    <div class="analytic-panel-subtitle">
                                        Distribusi berdasarkan source
                                    </div>
                                </div>
                                <div class="analytic-panel-body">
                                    @forelse ($topSources as $source)
                                        @php
                                            $percentage =
                                                ($source->total
                                                / $maxSource)
                                                * 100;
                                        @endphp
                                        <div class="bar-item">
                                            <div class="bar-header">
                                                <div class="bar-label">
                                                    {{ $source->label }}
                                                </div>
                                                <div class="bar-total">
                                                    {{ number_format( $source->total ) }}
                                                </div>
                                            </div>
                                            <div class="bar-track">
                                                <div
                                                    class="bar-fill orange"
                                                    style="width: {{ $percentage }}%"
                                                ></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            Tidak ada data.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="analytic-panel">
                                <div class="analytic-panel-header">
                                    <h6 class="analytic-panel-title">
                                        Top Locations
                                    </h6>
                                    <div class="analytic-panel-subtitle">
                                        Lokasi dengan fault terbanyak
                                    </div>
                                </div>
                                <div class="analytic-panel-body">
                                    @forelse ($topLocations as $location)
                                        @php
                                            $percentage =
                                                ($location->total
                                                / $maxLocation)
                                                * 100;
                                        @endphp
                                        <div class="bar-item">
                                            <div class="bar-header">
                                                <div
                                                    class="bar-label"
                                                    title="{{ $location->label }}"
                                                >
                                                    {{ $location->label }}
                                                </div>
                                                <div class="bar-total">
                                                    {{ number_format( $location->total ) }}
                                                </div>
                                            </div>
                                            <div class="bar-track">
                                                <div
                                                    class="bar-fill green"
                                                    style="width: {{ $percentage }}%"
                                                ></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            Tidak ada data.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="analytic-panel">
                        <div class="analytic-panel-header">
                            <h6 class="analytic-panel-title">
                                Fault Type Summary
                            </h6>
                        </div>
                        <div class="analytic-panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>
                                                Type
                                            </th>
                                            <th class="text-end">
                                                Total
                                            </th>
                                            <th class="text-end">
                                                Percentage
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($typeSummary as $type)
                                            @php
                                                $percentage =
                                                    $totalRecords > 0
                                                        ? (
                                                            $type->total
                                                            / $totalRecords
                                                        ) * 100
                                                        : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{ $type->label }}
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format( $type->total ) }}
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format( $percentage, 2 ) }}%
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td
                                                    colspan="3"
                                                    class="text-center text-muted py-4"
                                                >
                                                    Tidak ada data.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@include('layout.footer')
