@include('layout.head')
@include('layout.sidebar')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.dataTables.min.css">
<style>
.komatsu-vhms-page{padding:24px 0 36px;color:#1e293b;background:#f8fafc;min-height:calc(100vh - 60px)}
.komatsu-vhms-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:20px}
.komatsu-vhms-title{font-size:23px;font-weight:750;letter-spacing:-.4px;margin:0;color:#172033}
.komatsu-vhms-subtitle{font-size:13px;color:#64748b;margin:6px 0 0}
.komatsu-vhms-refresh{display:inline-flex;align-items:center;gap:7px;padding:9px 13px;border:1px solid #dbe2ea;border-radius:9px;background:#fff;color:#334155;font-size:12px;font-weight:650;text-decoration:none}
.komatsu-vhms-refresh:hover{background:#f1f5f9;color:#0f172a}
.komatsu-vhms-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px;margin-bottom:18px}
.komatsu-vhms-stat{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:15px 17px;box-shadow:0 2px 8px rgba(15,23,42,.03);min-width:0}
.komatsu-vhms-stat-label{font-size:10px;font-weight:750;letter-spacing:.45px;text-transform:uppercase;color:#64748b}
.komatsu-vhms-stat-value{font-size:25px;line-height:1.2;font-weight:750;margin-top:8px;color:#172033;font-variant-numeric:tabular-nums}
.komatsu-vhms-stat-note{font-size:10px;color:#94a3b8;margin-top:5px}
.komatsu-vhms-indicator{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:6px;vertical-align:1px}
.komatsu-vhms-indicator-white{background:#cbd5e1;border:1px solid #94a3b8}
.komatsu-vhms-indicator-orange{background:#f4c47b}
.komatsu-vhms-indicator-red{background:#e9a0a0}
.komatsu-vhms-panel{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 2px 8px rgba(15,23,42,.035);overflow:hidden}
.komatsu-vhms-panel-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:16px 19px;border-bottom:1px solid #e2e8f0}
.komatsu-vhms-panel-title{font-size:15px;font-weight:700;margin:0;color:#1e293b}
.komatsu-vhms-panel-desc{font-size:11px;color:#94a3b8;margin:4px 0 0}
.komatsu-vhms-legend{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.komatsu-vhms-legend-item{display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#475569;font-weight:600}
.komatsu-vhms-legend-dot{width:9px;height:9px;border-radius:3px;display:inline-block}
.komatsu-vhms-dot-white{background:#fff;border:1px solid #94a3b8}
.komatsu-vhms-dot-orange{background:#f4c47b}
.komatsu-vhms-dot-red{background:#e9a0a0}
.komatsu-vhms-table-wrap{padding:14px 18px 18px}
#komatsuVhmsTable{width:100%!important;border-collapse:separate!important;border-spacing:0;font-size:11px}
#komatsuVhmsTable thead th{background:#f8fafc!important;color:#475569!important;font-size:10px!important;font-weight:750!important;text-transform:uppercase;letter-spacing:.3px;padding:12px 10px!important;border-bottom:1px solid #e2e8f0!important;white-space:nowrap}
#komatsuVhmsTable tbody td{padding:10px!important;border-bottom:1px solid #edf1f5!important;vertical-align:middle;white-space:nowrap}
#komatsuVhmsTable tbody tr.state-white td{background:#fff!important;color:#334155!important}
#komatsuVhmsTable tbody tr.state-orange td{background:#fffaf1!important;color:#80551b!important}
#komatsuVhmsTable tbody tr.state-red td{background:#fff7f7!important;color:#914747!important}
#komatsuVhmsTable tbody tr:hover td{filter:brightness(.975)}
.komatsu-vhms-equipment{font-weight:750;color:#1e293b}
.komatsu-vhms-sn{font-weight:700;color:#475569}
.komatsu-vhms-status{display:inline-flex;align-items:center;border-radius:30px;padding:4px 9px;font-size:10px;font-weight:750}
.komatsu-vhms-status-white{background:#f1f5f9;color:#475569}
.komatsu-vhms-status-orange{background:#fff0d9;color:#96601c}
.komatsu-vhms-status-red{background:#fdeaea;color:#a34c4c}
.komatsu-vhms-diff{font-weight:750;font-variant-numeric:tabular-nums}
.komatsu-vhms-footnote{padding:0 19px 16px;color:#94a3b8;font-size:10px}
.dt-container .dt-layout-row{margin:0 0 12px!important;align-items:center}
.dt-container .dt-search input,.dt-container .dt-length select{border:1px solid #dbe2ea!important;border-radius:7px!important;padding:6px 9px!important;font-size:11px!important;background:#fff!important}
.dt-container .dt-info{font-size:10px;color:#64748b}
.dt-container .dt-paging .dt-paging-button{font-size:10px!important;border-radius:6px!important;padding:5px 9px!important}
.dt-container .dt-paging .dt-paging-button.current{background:#eaf1ff!important;border:1px solid #c7d7fe!important;color:#1d4ed8!important}
@media(max-width:991px){.komatsu-vhms-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.komatsu-vhms-table-wrap{padding:12px}}
@media(max-width:575px){.komatsu-vhms-page{padding-top:16px}.komatsu-vhms-title{font-size:19px}.komatsu-vhms-stats{gap:9px}.komatsu-vhms-stat{padding:12px}.komatsu-vhms-stat-value{font-size:22px}.komatsu-vhms-panel-head{padding:14px}.komatsu-vhms-legend{gap:8px}.dt-container .dt-layout-row{gap:9px}.dt-container .dt-search,.dt-container .dt-length{width:100%;text-align:left}.dt-container .dt-search input{width:calc(100% - 55px)}}
</style>
<div class="page-content komatsu-vhms-page">
    <div class="container-fluid px-3 px-lg-4">
        @php
            $rows = collect($data ?? []);
            $totalEquipment = $rows->count();
            $whiteCount = $rows->filter(fn($row) => strtolower((string) ($row->ColorState ?? '')) === 'white')->count();
            $orangeCount = $rows->filter(fn($row) => strtolower((string) ($row->ColorState ?? '')) === 'orange')->count();
            $redCount = $rows->filter(fn($row) => strtolower((string) ($row->ColorState ?? '')) === 'red')->count();
            $lastUpdatedValue = $rows->first()->CurrentTimeStamp ?? null;
            $lastUpdated = $lastUpdatedValue ? \Carbon\Carbon::parse($lastUpdatedValue)->format('d M Y, H:i:s') : '-';
        @endphp
        <div class="komatsu-vhms-heading">
            <div>
                <h4 class="komatsu-vhms-title">Komatsu VHMS Data Monitoring</h4>
                <p class="komatsu-vhms-subtitle">Monitoring pembaruan data equipment dalam 20 jam terakhir.</p>
            </div>
            <a href="{{ url()->current() }}" class="komatsu-vhms-refresh"><i class="fi fi-rr-refresh"></i> Refresh Data</a>
        </div>
        <div class="komatsu-vhms-stats">
            <div class="komatsu-vhms-stat">
                <div class="komatsu-vhms-stat-label">Total Equipment</div>
                <div class="komatsu-vhms-stat-value">{{ number_format($totalEquipment) }}</div>
                <div class="komatsu-vhms-stat-note">Seluruh equipment terpantau</div>
            </div>
            <div class="komatsu-vhms-stat">
                <div class="komatsu-vhms-stat-label"><span class="komatsu-vhms-indicator komatsu-vhms-indicator-white"></span>Normal</div>
                <div class="komatsu-vhms-stat-value">{{ number_format($whiteCount) }}</div>
                <div class="komatsu-vhms-stat-note">Data diperbarui kurang dari 48 jam</div>
            </div>
            <div class="komatsu-vhms-stat">
                <div class="komatsu-vhms-stat-label"><span class="komatsu-vhms-indicator komatsu-vhms-indicator-orange"></span>Warning</div>
                <div class="komatsu-vhms-stat-value">{{ number_format($orangeCount) }}</div>
                <div class="komatsu-vhms-stat-note">Data berusia 48 hingga kurang dari 168 jam</div>
            </div>
            <div class="komatsu-vhms-stat">
                <div class="komatsu-vhms-stat-label"><span class="komatsu-vhms-indicator komatsu-vhms-indicator-red"></span>Critical</div>
                <div class="komatsu-vhms-stat-value">{{ number_format($redCount) }}</div>
                <div class="komatsu-vhms-stat-note">Data berusia 168 jam atau lebih</div>
            </div>
        </div>
        <div class="komatsu-vhms-panel">
            <div class="komatsu-vhms-panel-head">
                <div>
                    <h5 class="komatsu-vhms-panel-title">Equipment Update Status</h5>
                    <p class="komatsu-vhms-panel-desc">Terakhir diperbarui: {{ $lastUpdated }}</p>
                </div>
                <div class="komatsu-vhms-legend">
                    <span class="komatsu-vhms-legend-item"><span class="komatsu-vhms-legend-dot komatsu-vhms-dot-white"></span>Normal &lt; 48 jam</span>
                    <span class="komatsu-vhms-legend-item"><span class="komatsu-vhms-legend-dot komatsu-vhms-dot-orange"></span>Warning 48–&lt;168 jam</span>
                    <span class="komatsu-vhms-legend-item"><span class="komatsu-vhms-legend-dot komatsu-vhms-dot-red"></span>Critical ≥168 jam</span>
                </div>
            </div>
            <div class="komatsu-vhms-table-wrap">
                <div class="table-responsive">
                    <table id="komatsuVhmsTable" class="display responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Equipment</th>
                                <th>From Date Time</th>
                                <th>From SMR</th>
                                <th>Hour Meter</th>
                                <th>Realtime HM</th>
                                <th>Last Total HM</th>
                                <th>Reset</th>
                                <th>Current Time</th>
                                <th>Diff Time Hour</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                @php
                                    $state = strtolower((string) ($row->ColorState ?? 'white'));
                                    $state = in_array($state, ['white', 'orange', 'red'], true) ? $state : 'white';
                                    $statusLabel = ['white' => 'Normal', 'orange' => 'Warning', 'red' => 'Critical'][$state];
                                    $fromDate = !empty($row->From_Date_Time) ? \Carbon\Carbon::parse($row->From_Date_Time)->format('d M Y, H:i:s') : '-';
                                    $currentDate = !empty($row->CurrentTimeStamp) ? \Carbon\Carbon::parse($row->CurrentTimeStamp)->format('d M Y, H:i:s') : '-';
                                    $fromSmr = is_numeric($row->From_SMR ?? null) ? number_format((float) $row->From_SMR, 1) : '-';
                                    $hourMeter = is_numeric($row->LAST_LOGOUT_HOURMETER ?? null) ? number_format((float) $row->LAST_LOGOUT_HOURMETER, 1) : '-';
                                    $realtimeHm = is_numeric($row->REALTIME_HM ?? null) ? number_format((float) $row->REALTIME_HM, 1) : '-';
                                    $lastTotalHm = is_numeric($row->Last_Total_HM ?? null) ? number_format((float) $row->Last_Total_HM, 1) : '-';
                                    $diff = is_numeric($row->Diff_TIime ?? null) ? number_format((float) $row->Diff_TIime, 1) : '-';
                                @endphp
                                <tr class="state-{{ $state }}">
                                    <td data-order="{{ $row->SN ?? '' }}"><span class="komatsu-vhms-sn">{{ $row->SN ?? '-' }}</span></td>
                                    <td><span class="komatsu-vhms-equipment">{{ $row->VHC_ID ?? '-' }}</span></td>
                                    <td data-order="{{ $row->From_Date_Time ?? '' }}">{{ $fromDate }}</td>
                                    <td>{{ $fromSmr }}</td>
                                    <td>{{ $hourMeter }}</td>
                                    <td>{{ $realtimeHm }}</td>
                                    <td>{{ $lastTotalHm }}</td>
                                    <td>{{ isset($row->is_Reset) ? ((int) $row->is_Reset === 1 ? 'Yes' : 'No') : '-' }}</td>
                                    <td data-order="{{ $row->CurrentTimeStamp ?? '' }}">{{ $currentDate }}</td>
                                    <td><span class="komatsu-vhms-diff">{{ $diff }}</span></td>
                                    <td><span class="komatsu-vhms-status komatsu-vhms-status-{{ $state }}">{{ $statusLabel }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($rows->isEmpty())
                        <div class="komatsu-vhms-empty">Belum ada data equipment.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.querySelector('#komatsuVhmsTable');
    if (window.DataTable && table && table.querySelector('tbody tr')) {
        new DataTable(table, {
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            order: [[9, 'desc']],
            columnDefs: [{ targets: [10], orderable: false, searchable: false }],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ equipment',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data equipment',
                paginate: { first: 'Awal', last: 'Akhir', next: '›', previous: '‹' }
            }
        });
    }
});
</script>
