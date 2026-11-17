@include('layout.head')
@include('layout.sidebar')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.dataTables.min.css">
<style>
.komatsu-page{padding:24px 0 36px;color:#1e293b;background:#f8fafc;min-height:calc(100vh - 60px)}
.komatsu-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:20px}
.komatsu-title{font-size:23px;font-weight:750;letter-spacing:-.4px;margin:0;color:#172033}
.komatsu-subtitle{font-size:13px;color:#64748b;margin:6px 0 0}
.komatsu-actions{display:flex;gap:8px;flex-wrap:wrap}
.komatsu-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:9px 13px;border:1px solid #dbe2ea;border-radius:9px;background:#fff;color:#334155;font-size:12px;font-weight:650;text-decoration:none;cursor:pointer}
.komatsu-btn:hover{background:#f1f5f9;color:#0f172a}
.komatsu-btn-primary{background:#2563eb;border-color:#2563eb;color:#fff}
.komatsu-btn-primary:hover{background:#1d4ed8;color:#fff}
.komatsu-btn-success{background:#15803d;border-color:#15803d;color:#fff}
.komatsu-btn-success:hover{background:#166534;color:#fff}
.komatsu-panel{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 2px 8px rgba(15,23,42,.035);overflow:hidden;margin-bottom:18px}
.komatsu-panel-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:16px 19px;border-bottom:1px solid #e2e8f0}
.komatsu-panel-title{font-size:15px;font-weight:700;margin:0;color:#1e293b}
.komatsu-panel-desc{font-size:11px;color:#94a3b8;margin:4px 0 0}
.komatsu-filter{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;padding:16px 19px}
.komatsu-field label{display:block;font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px}
.komatsu-field input,.komatsu-field select{width:100%;height:38px;border:1px solid #dbe2ea;border-radius:8px;padding:7px 10px;color:#334155;background:#fff;font-size:12px}
.komatsu-filter-actions{display:flex;align-items:end;gap:8px}
.komatsu-tabs{display:flex;gap:6px;padding:0 19px;border-bottom:1px solid #e2e8f0}
.komatsu-tab{padding:12px 14px;border-bottom:2px solid transparent;color:#64748b;font-size:12px;font-weight:700;text-decoration:none}
.komatsu-tab.active{color:#2563eb;border-color:#2563eb}
.komatsu-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:13px;margin-bottom:18px}
.komatsu-stat{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:15px 16px;box-shadow:0 2px 8px rgba(15,23,42,.03);min-width:0}
.komatsu-stat-label{font-size:10px;font-weight:750;letter-spacing:.45px;text-transform:uppercase;color:#64748b}
.komatsu-stat-value{font-size:25px;line-height:1.2;font-weight:750;margin-top:8px;color:#172033;font-variant-numeric:tabular-nums}
.komatsu-stat-note{font-size:10px;color:#94a3b8;margin-top:5px}
.komatsu-table-wrap{padding:14px 18px 18px}
#komatsuTable{width:100%!important;font-size:11px}
#komatsuTable thead th{background:#f8fafc!important;color:#475569!important;font-size:10px!important;font-weight:750!important;text-transform:uppercase;letter-spacing:.3px;white-space:nowrap;padding:12px 10px!important}
#komatsuTable tbody td{padding:10px!important;vertical-align:middle;white-space:nowrap}
.komatsu-equipment{font-weight:750;color:#1e293b}
.komatsu-code{font-weight:750;color:#2563eb}
.komatsu-message{white-space:normal!important;min-width:220px;max-width:360px}
.komatsu-muted{color:#94a3b8}
.komatsu-pagination{padding:0 18px 16px}
.komatsu-chart-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-bottom:18px}
.komatsu-chart-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;min-width:0}
.komatsu-chart-title{font-size:13px;font-weight:750;color:#1e293b;margin:0 0 14px}
.komatsu-bar-row{display:grid;grid-template-columns:minmax(90px,1fr) minmax(80px,2fr) 42px;gap:9px;align-items:center;margin:10px 0}
.komatsu-bar-label{font-size:10px;color:#475569;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.komatsu-bar-track{height:9px;background:#f1f5f9;border-radius:20px;overflow:hidden}
.komatsu-bar-fill{height:100%;background:#7c9bd4;border-radius:20px}
.komatsu-bar-value{font-size:10px;color:#64748b;text-align:right;font-variant-numeric:tabular-nums}
.komatsu-trend{display:flex;align-items:end;gap:7px;height:170px;padding:12px 4px 0;border-bottom:1px solid #e2e8f0}
.komatsu-trend-item{flex:1;min-width:0;display:flex;flex-direction:column;align-items:center;justify-content:end;height:100%;gap:6px}
.komatsu-trend-bar{width:min(100%,28px);min-height:3px;background:#8ba6d5;border-radius:5px 5px 0 0}
.komatsu-trend-value{font-size:9px;color:#64748b}
.komatsu-trend-label{font-size:9px;color:#94a3b8;white-space:nowrap;transform:rotate(-35deg);transform-origin:top center;margin-top:8px}
.komatsu-empty{padding:32px 16px;text-align:center;color:#94a3b8;font-size:12px}
.dt-container .dt-layout-row{margin:0 0 12px!important;align-items:center}
.dt-container .dt-search input,.dt-container .dt-length select{border:1px solid #dbe2ea!important;border-radius:7px!important;padding:6px 9px!important;font-size:11px!important;background:#fff!important}
.dt-container .dt-info{font-size:10px;color:#64748b}
.dt-container .dt-paging .dt-paging-button{font-size:10px!important;border-radius:6px!important;padding:5px 9px!important}
.dt-container .dt-paging .dt-paging-button.current{background:#eaf1ff!important;border:1px solid #c7d7fe!important;color:#1d4ed8!important}
@media(max-width:1100px){.komatsu-stats{grid-template-columns:repeat(3,minmax(0,1fr))}.komatsu-filter{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:767px){.komatsu-page{padding-top:16px}.komatsu-title{font-size:19px}.komatsu-stats{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.komatsu-stat{padding:12px}.komatsu-stat-value{font-size:22px}.komatsu-chart-grid{grid-template-columns:1fr}.komatsu-panel-head{padding:14px}.komatsu-table-wrap{padding:12px}.komatsu-filter{grid-template-columns:1fr;padding:14px}.komatsu-filter-actions{align-items:stretch}.komatsu-filter-actions .komatsu-btn{flex:1}.komatsu-tabs{padding:0 12px}.komatsu-tab{padding:11px 10px}}
</style>
<div class="page-content komatsu-page">
    <div class="container-fluid px-3 px-lg-4">
        <div class="komatsu-heading">
            <div>
                <h4 class="komatsu-title">Komatsu Fault Code Monitoring</h4>
                <p class="komatsu-subtitle">Monitoring fault code, frekuensi kejadian, equipment, dan aktivitas operator.</p>
            </div>
            <div class="komatsu-actions">
                <a href="{{ url()->current() . '?' . http_build_query(request()->except('page')) }}" class="komatsu-btn"><i class="fi fi-rr-refresh"></i> Refresh</a>
                @if($activeTab === 'raw')
                    <a href="{{ route('komatsu.fault-code.raw-data.export', request()->only(['start_date', 'stop_date', 'error_message'])) }}" class="komatsu-btn komatsu-btn-success"><i class="fi fi-rr-download"></i> Export CSV</a>
                @endif
            </div>
        </div>
        <div class="komatsu-panel">
            <div class="komatsu-panel-head">
                <div>
                    <h5 class="komatsu-panel-title">Filter Data</h5>
                    <p class="komatsu-panel-desc">Pilih rentang tanggal dan pesan error untuk menyaring data.</p>
                </div>
            </div>
            <form method="GET" action="{{ route('komatsu.fault-code.raw-data') }}" class="komatsu-filter">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <div class="komatsu-field">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                </div>
                <div class="komatsu-field">
                    <label for="stop_date">Tanggal Selesai</label>
                    <input type="date" id="stop_date" name="stop_date" value="{{ request('stop_date', $stopDate->format('Y-m-d')) }}">
                </div>
                <div class="komatsu-field">
                    <label for="error_message">Error Message</label>
                    <select id="error_message" name="error_message">
                        <option value="">Semua Error Message</option>
                        @foreach($errorMessages as $message)
                            <option value="{{ $message }}" {{ $selectedError === $message ? 'selected' : '' }}>{{ $message }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="komatsu-filter-actions">
                    <button type="submit" class="komatsu-btn komatsu-btn-primary"><i class="fi fi-rr-filter"></i> Terapkan</button>
                    <a href="{{ route('komatsu.fault-code.raw-data', ['tab' => $activeTab]) }}" class="komatsu-btn">Reset</a>
                </div>
            </form>
            <div class="komatsu-tabs">
                <a class="komatsu-tab {{ $activeTab === 'raw' ? 'active' : '' }}" href="{{ route('komatsu.fault-code.raw-data', array_merge(request()->except('page'), ['tab' => 'raw'])) }}">Raw Data</a>
                <a class="komatsu-tab {{ $activeTab === 'analytics' ? 'active' : '' }}" href="{{ route('komatsu.fault-code.raw-data', array_merge(request()->except('page'), ['tab' => 'analytics'])) }}">Analytics</a>
            </div>
        </div>
        @if($activeTab === 'raw')
            <div class="komatsu-panel">
                <div class="komatsu-panel-head">
                    <div>
                        <h5 class="komatsu-panel-title">Daftar Fault Code Komatsu</h5>
                        <p class="komatsu-panel-desc">Menampilkan data sesuai filter dengan pagination server-side.</p>
                    </div>
                    <span class="komatsu-panel-desc">Total halaman data: {{ $data ? $data->count() : 0 }} baris ditampilkan</span>
                </div>
                <div class="komatsu-table-wrap">
                    <div class="table-responsive">
                        <table id="komatsuTable" class="display responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Waktu Fault</th>
                                    <th>Equipment</th>
                                    <th>Serial Number</th>
                                    <th>Fault Code</th>
                                    <th>Error Message</th>
                                    <th>Count</th>
                                    <th>From SMR</th>
                                    <th>To SMR</th>
                                    <th>Hour Meter</th>
                                    <th>Odometer</th>
                                    <th>Operator</th>
                                    <th>Location</th>
                                    <th>VSA Status ID</th>
                                    <th>VSA Activity ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data ?? [] as $row)
                                    <tr>
                                        <td data-order="{{ $row->From_Date_Time ?? '' }}">{{ !empty($row->From_Date_Time) ? \Carbon\Carbon::parse($row->From_Date_Time)->format('d M Y H:i') : '-' }}</td>
                                        <td><span class="komatsu-equipment">{{ $row->NAME ?? '-' }}</span></td>
                                        <td>{{ $row->SN ?? '-' }}</td>
                                        <td><span class="komatsu-code">{{ $row->Code ?? '-' }}</span></td>
                                        <td class="komatsu-message">{{ $row->Error_Message ?? '-' }}</td>
                                        <td>{{ is_numeric($row->Count ?? null) ? number_format((float) $row->Count) : '-' }}</td>
                                        <td>{{ is_numeric($row->From_SMR ?? null) ? number_format((float) $row->From_SMR, 1) : '-' }}</td>
                                        <td>{{ is_numeric($row->To_SMR ?? null) ? number_format((float) $row->To_SMR, 1) : '-' }}</td>
                                        <td>{{ is_numeric($row->MTR_HOURMETER ?? null) ? number_format((float) $row->MTR_HOURMETER, 1) : '-' }}</td>
                                        <td>{{ is_numeric($row->MTR_ODOMETER ?? null) ? number_format((float) $row->MTR_ODOMETER, 1) : '-' }}</td>
                                        <td>{{ $row->OPR_NAME ?? $row->OPR_NRP ?? '-' }}</td>
                                        <td>{{ $row->LOC_NAME ?? '-' }}</td>
                                        <td>{{ $row->VSA_STATUSID ?? '-' }}</td>
                                        <td>{{ $row->VSA_ACTIVITYID ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($data)
                    <div class="komatsu-pagination">{{ $data->links() }}</div>
                @endif
            </div>
        @elseif($activeTab === 'analytics')
            <div class="komatsu-stats">
                <div class="komatsu-stat"><div class="komatsu-stat-label">Total Fault Records</div><div class="komatsu-stat-value">{{ number_format($totalRecords) }}</div><div class="komatsu-stat-note">Jumlah catatan fault</div></div>
                <div class="komatsu-stat"><div class="komatsu-stat-label">Total Occurrences</div><div class="komatsu-stat-value">{{ number_format($totalOccurrences) }}</div><div class="komatsu-stat-note">Akumulasi nilai Count</div></div>
                <div class="komatsu-stat"><div class="komatsu-stat-label">Unique Fault Codes</div><div class="komatsu-stat-value">{{ number_format($uniqueFaults) }}</div><div class="komatsu-stat-note">Kode fault berbeda</div></div>
                <div class="komatsu-stat"><div class="komatsu-stat-label">Units Affected</div><div class="komatsu-stat-value">{{ number_format($uniqueUnits) }}</div><div class="komatsu-stat-note">Equipment berdasarkan NAME</div></div>
                <div class="komatsu-stat"><div class="komatsu-stat-label">Unique Operators</div><div class="komatsu-stat-value">{{ number_format($uniqueOperators) }}</div><div class="komatsu-stat-note">Operator berdasarkan NRP</div></div>
            </div>
            <div class="komatsu-chart-grid">
                <div class="komatsu-chart-card">
                    <h6 class="komatsu-chart-title">Top 10 Fault Code</h6>
                    @forelse($topFaults as $item)
                        <div class="komatsu-bar-row"><div class="komatsu-bar-label" title="{{ $item->label }}">{{ $item->label }}</div><div class="komatsu-bar-track"><div class="komatsu-bar-fill" style="width:{{ min(100, (($item->total ?? 0) / max(1, $maxTopFault)) * 100) }}%"></div></div><div class="komatsu-bar-value">{{ number_format($item->total) }}</div></div>
                    @empty
                        <div class="komatsu-empty">Belum ada data.</div>
                    @endforelse
                </div>
                <div class="komatsu-chart-card">
                    <h6 class="komatsu-chart-title">Top 10 Error Message</h6>
                    @forelse($topErrors as $item)
                        <div class="komatsu-bar-row"><div class="komatsu-bar-label" title="{{ $item->label }}">{{ $item->label }}</div><div class="komatsu-bar-track"><div class="komatsu-bar-fill" style="width:{{ min(100, (($item->total ?? 0) / max(1, $maxError)) * 100) }}%"></div></div><div class="komatsu-bar-value">{{ number_format($item->total) }}</div></div>
                    @empty
                        <div class="komatsu-empty">Belum ada data.</div>
                    @endforelse
                </div>
                <div class="komatsu-chart-card">
                    <h6 class="komatsu-chart-title">Top 10 Equipment</h6>
                    @forelse($topUnits as $item)
                        <div class="komatsu-bar-row"><div class="komatsu-bar-label" title="{{ $item->label }}">{{ $item->label }}</div><div class="komatsu-bar-track"><div class="komatsu-bar-fill" style="width:{{ min(100, (($item->total ?? 0) / max(1, $maxUnit)) * 100) }}%"></div></div><div class="komatsu-bar-value">{{ number_format($item->total) }}</div></div>
                    @empty
                        <div class="komatsu-empty">Belum ada data.</div>
                    @endforelse
                </div>
                <div class="komatsu-chart-card">
                    <h6 class="komatsu-chart-title">Top 10 Locations</h6>
                    @forelse($topLocations as $item)
                        <div class="komatsu-bar-row"><div class="komatsu-bar-label" title="{{ $item->label }}">{{ $item->label }}</div><div class="komatsu-bar-track"><div class="komatsu-bar-fill" style="width:{{ min(100, (($item->total ?? 0) / max(1, $maxLocation)) * 100) }}%"></div></div><div class="komatsu-bar-value">{{ number_format($item->total) }}</div></div>
                    @empty
                        <div class="komatsu-empty">Belum ada data.</div>
                    @endforelse
                </div>
            </div>
            <div class="komatsu-chart-card">
                <h6 class="komatsu-chart-title">Daily Fault Trend</h6>
                @if($dailyTrend->isNotEmpty())
                    <div class="komatsu-trend">
                        @foreach($dailyTrend as $item)
                            <div class="komatsu-trend-item" title="{{ $item->date }}: {{ number_format($item->total) }} faults">
                                <span class="komatsu-trend-value">{{ number_format($item->total) }}</span>
                                <div class="komatsu-trend-bar" style="height:{{ max(3, (($item->total ?? 0) / max(1, $maxDaily)) * 125) }}px"></div>
                                <span class="komatsu-trend-label">{{ \Carbon\Carbon::parse($item->date)->format('d/m') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="komatsu-empty">Belum ada data tren harian.</div>
                @endif
            </div>
        @endif
    </div>
</div>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.querySelector('#komatsuTable');
    if (window.DataTable && table) {
        new DataTable(table, {
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[0, 'desc']],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data fault code',
                paginate: { first: 'Awal', last: 'Akhir', next: '›', previous: '‹' }
            }
        });
    }
});
</script>
