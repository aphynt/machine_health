@include('layout.head')
@include('layout.sidebar')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.dataTables.min.css">
<style>
.caterpillar-vims-page{padding:24px 0 36px;color:#1e293b;background:#f8fafc;min-height:calc(100vh - 60px)}
.caterpillar-vims-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:20px}
.caterpillar-vims-title{font-size:23px;font-weight:750;letter-spacing:-.4px;margin:0;color:#172033}
.caterpillar-vims-subtitle{font-size:13px;color:#64748b;margin:6px 0 0}
.caterpillar-vims-actions{display:flex;gap:8px;flex-wrap:wrap}
.caterpillar-vims-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:9px 13px;border:1px solid #dbe2ea;border-radius:9px;background:#fff;color:#334155;font-size:12px;font-weight:650;text-decoration:none;cursor:pointer}
.caterpillar-vims-btn:hover{background:#f1f5f9;color:#0f172a}
.caterpillar-vims-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px;margin-bottom:18px}
.caterpillar-vims-stat{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:15px 17px;box-shadow:0 2px 8px rgba(15,23,42,.03);min-width:0}
.caterpillar-vims-stat-label{font-size:10px;font-weight:750;letter-spacing:.45px;text-transform:uppercase;color:#64748b}
.caterpillar-vims-stat-value{font-size:25px;line-height:1.2;font-weight:750;margin-top:8px;color:#172033;font-variant-numeric:tabular-nums}
.caterpillar-vims-stat-note{font-size:10px;color:#94a3b8;margin-top:5px}
.caterpillar-vims-indicator{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:6px;vertical-align:1px}
.caterpillar-vims-indicator-white{background:#cbd5e1;border:1px solid #94a3b8}
.caterpillar-vims-indicator-orange{background:#f4c47b}
.caterpillar-vims-indicator-red{background:#e9a0a0}
.caterpillar-vims-panel{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 2px 8px rgba(15,23,42,.035);overflow:hidden}
.caterpillar-vims-panel-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:16px 19px;border-bottom:1px solid #e2e8f0}
.caterpillar-vims-panel-title{font-size:15px;font-weight:700;margin:0;color:#1e293b}
.caterpillar-vims-panel-desc{font-size:11px;color:#94a3b8;margin:4px 0 0}
.caterpillar-vims-legend{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.caterpillar-vims-legend-item{display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#475569;font-weight:600}
.caterpillar-vims-legend-dot{width:9px;height:9px;border-radius:3px;display:inline-block}
.caterpillar-vims-dot-white{background:#fff;border:1px solid #94a3b8}
.caterpillar-vims-dot-orange{background:#f4c47b}
.caterpillar-vims-dot-red{background:#e9a0a0}
.caterpillar-vims-table-wrap{padding:14px 18px 18px}
#caterpillarVimsTable{width:100%!important;border-collapse:separate!important;border-spacing:0;font-size:11px}
#caterpillarVimsTable thead th{background:#f8fafc!important;color:#475569!important;font-size:10px!important;font-weight:750!important;text-transform:uppercase;letter-spacing:.3px;padding:12px 10px!important;border-bottom:1px solid #e2e8f0!important;white-space:nowrap}
#caterpillarVimsTable tbody td{padding:10px!important;border-bottom:1px solid #edf1f5!important;vertical-align:middle;white-space:nowrap}
#caterpillarVimsTable tbody tr.state-white td{background:#fff!important;color:#334155!important}
#caterpillarVimsTable tbody tr.state-orange td{background:#fffaf1!important;color:#80551b!important}
#caterpillarVimsTable tbody tr.state-red td{background:#fff7f7!important;color:#914747!important}
#caterpillarVimsTable tbody tr:hover td{filter:brightness(.975)}
.caterpillar-vims-equipment{font-weight:750;color:#1e293b}
.caterpillar-vims-model{font-weight:700;color:#475569}
.caterpillar-vims-status{display:inline-flex;align-items:center;border-radius:30px;padding:4px 9px;font-size:10px;font-weight:750}
.caterpillar-vims-status-white{background:#f1f5f9;color:#475569}
.caterpillar-vims-status-orange{background:#fff0d9;color:#96601c}
.caterpillar-vims-status-red{background:#fdeaea;color:#a34c4c}
.caterpillar-vims-diff{font-weight:750;font-variant-numeric:tabular-nums}
.caterpillar-vims-footnote{padding:0 19px 16px;color:#94a3b8;font-size:10px}
.caterpillar-vims-empty{padding:28px 12px;text-align:center;color:#94a3b8;font-size:12px}
.dt-container .dt-layout-row{margin:0 0 12px!important;align-items:center}
.dt-container .dt-search input,.dt-container .dt-length select{border:1px solid #dbe2ea!important;border-radius:7px!important;padding:6px 9px!important;font-size:11px!important;background:#fff!important}
.dt-container .dt-info{font-size:10px;color:#64748b}
.dt-container .dt-paging .dt-paging-button{font-size:10px!important;border-radius:6px!important;padding:5px 9px!important}
.dt-container .dt-paging .dt-paging-button.current{background:#eaf1ff!important;border:1px solid #c7d7fe!important;color:#1d4ed8!important}
@media(max-width:991px){.caterpillar-vims-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.caterpillar-vims-table-wrap{padding:12px}}
@media(max-width:575px){.caterpillar-vims-page{padding-top:16px}.caterpillar-vims-title{font-size:19px}.caterpillar-vims-stats{gap:9px}.caterpillar-vims-stat{padding:12px}.caterpillar-vims-stat-value{font-size:22px}.caterpillar-vims-panel-head{padding:14px}.caterpillar-vims-legend{gap:8px}.dt-container .dt-layout-row{gap:9px}.dt-container .dt-search,.dt-container .dt-length{width:100%;text-align:left}.dt-container .dt-search input{width:calc(100% - 55px)}}
</style>
<div class="page-content caterpillar-vims-page">
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
        <div class="caterpillar-vims-heading">
            <div>
                <h4 class="caterpillar-vims-title">Caterpillar VIMS Data Monitoring</h4>
                <p class="caterpillar-vims-subtitle">Monitoring pembaruan data VIMS equipment dalam 20 jam terakhir.</p>
            </div>
            <div class="caterpillar-vims-actions">
                <a href="{{ url()->current() }}" class="caterpillar-vims-btn"><i class="fi fi-rr-refresh"></i> Refresh Data</a>
            </div>
        </div>
        <div class="caterpillar-vims-stats">
            <div class="caterpillar-vims-stat">
                <div class="caterpillar-vims-stat-label">Total Equipment</div>
                <div class="caterpillar-vims-stat-value">{{ number_format($totalEquipment) }}</div>
                <div class="caterpillar-vims-stat-note">Seluruh equipment terpantau</div>
            </div>
            <div class="caterpillar-vims-stat">
                <div class="caterpillar-vims-stat-label"><span class="caterpillar-vims-indicator caterpillar-vims-indicator-white"></span>Normal</div>
                <div class="caterpillar-vims-stat-value">{{ number_format($whiteCount) }}</div>
                <div class="caterpillar-vims-stat-note">Data diperbarui kurang dari 48 jam</div>
            </div>
            <div class="caterpillar-vims-stat">
                <div class="caterpillar-vims-stat-label"><span class="caterpillar-vims-indicator caterpillar-vims-indicator-orange"></span>Warning</div>
                <div class="caterpillar-vims-stat-value">{{ number_format($orangeCount) }}</div>
                <div class="caterpillar-vims-stat-note">Data berusia 48 hingga kurang dari 168 jam</div>
            </div>
            <div class="caterpillar-vims-stat">
                <div class="caterpillar-vims-stat-label"><span class="caterpillar-vims-indicator caterpillar-vims-indicator-red"></span>Critical</div>
                <div class="caterpillar-vims-stat-value">{{ number_format($redCount) }}</div>
                <div class="caterpillar-vims-stat-note">Data berusia 168 jam atau lebih</div>
            </div>
        </div>
        <div class="caterpillar-vims-panel">
            <div class="caterpillar-vims-panel-head">
                <div>
                    <h5 class="caterpillar-vims-panel-title">Equipment Update Status</h5>
                    <p class="caterpillar-vims-panel-desc">Terakhir diperbarui: {{ $lastUpdated }}</p>
                </div>
                <div class="caterpillar-vims-legend">
                    @foreach($legend ?? [] as $item)
                        @php
                            $legendState = strtolower((string) ($item->ColorState ?? 'white'));
                            $legendState = in_array($legendState, ['white', 'orange', 'red'], true) ? $legendState : 'white';
                        @endphp
                        <span class="caterpillar-vims-legend-item"><span class="caterpillar-vims-legend-dot caterpillar-vims-dot-{{ $legendState }}"></span>{{ trim($item->Value ?? '') }}</span>
                    @endforeach
                </div>
            </div>
            <div class="caterpillar-vims-table-wrap">
                <div class="table-responsive">
                    <table id="caterpillarVimsTable" class="display responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Model</th>
                                <th>Equipment</th>
                                <th>From Date Time</th>
                                <th>From SMR</th>
                                <th>Hour Meter</th>
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
                                    $fromDateTime = !empty($row->From_Date_Time) ? \Carbon\Carbon::parse($row->From_Date_Time)->format('d M Y, H:i:s') : '-';
                                    $currentTime = !empty($row->CurrentTimeStamp) ? \Carbon\Carbon::parse($row->CurrentTimeStamp)->format('d M Y, H:i:s') : '-';
                                    $fromSmr = is_numeric($row->From_SMR ?? null) ? number_format((float) $row->From_SMR, 1) : '-';
                                    $hourMeter = is_numeric($row->LAST_LOGOUT_HOURMETER ?? null) ? number_format((float) $row->LAST_LOGOUT_HOURMETER, 1) : '-';
                                    $diffTime = is_numeric($row->Diff_TIime ?? null) ? number_format((float) $row->Diff_TIime, 1) : '-';
                                @endphp
                                <tr class="state-{{ $state }}">
                                    <td><span class="caterpillar-vims-model">{{ $row->EQU_GROUPID ?? '-' }}</span></td>
                                    <td><span class="caterpillar-vims-equipment">{{ $row->SN ?? $row->VHC_ID ?? '-' }}</span></td>
                                    <td data-order="{{ $row->From_Date_Time ?? '' }}">{{ $fromDateTime }}</td>
                                    <td>{{ $fromSmr }}</td>
                                    <td>{{ $hourMeter }}</td>
                                    <td data-order="{{ $row->CurrentTimeStamp ?? '' }}">{{ $currentTime }}</td>
                                    <td><span class="caterpillar-vims-diff">{{ $diffTime }}</span></td>
                                    <td><span class="caterpillar-vims-status caterpillar-vims-status-{{ $state }}">{{ $statusLabel }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($rows->isEmpty())
                        <div class="caterpillar-vims-empty">Tidak ada data equipment yang tersedia.</div>
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
    const table = document.querySelector('#caterpillarVimsTable');
    if (window.DataTable && table && table.querySelector('tbody tr')) {
        new DataTable(table, {
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            order: [[6, 'desc']],
            columnDefs: [{ targets: [7], orderable: false, searchable: false }],
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
