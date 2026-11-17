@include('layout.head')
@include('layout.sidebar')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@2.3.4/css/dataTables.dataTables.min.css">
<style>
.poi-page{min-height:calc(100vh - 60px);padding:24px 0 36px;background:#f8fafc;color:#1e293b}
.poi-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:20px}
.poi-title{margin:0;color:#172033;font-size:23px;font-weight:750;letter-spacing:-.4px}
.poi-subtitle{margin:6px 0 0;color:#64748b;font-size:13px}
.poi-filter{display:flex;align-items:end;gap:9px;flex-wrap:wrap;padding:14px 16px;margin-bottom:17px;background:#fff;border:1px solid #e2e8f0;border-radius:12px}
.poi-filter label{display:block;margin-bottom:5px;color:#64748b;font-size:11px;font-weight:700}
.poi-filter input{height:37px;width:115px;padding:7px 10px;border:1px solid #dbe2ea;border-radius:8px;color:#334155}
.poi-btn{height:37px;padding:0 14px;border:1px solid #dbe2ea;border-radius:8px;background:#f8fafc;color:#334155;font-size:12px;font-weight:700;cursor:pointer}
.poi-btn-primary{background:#315b9a;border-color:#315b9a;color:#fff}
.poi-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px;margin-bottom:17px}
.poi-stat,.poi-panel{background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 2px 8px rgba(15,23,42,.03)}
.poi-stat{padding:15px 17px}
.poi-stat-label{font-size:10px;font-weight:750;letter-spacing:.4px;text-transform:uppercase;color:#64748b}
.poi-stat-value{margin-top:7px;color:#172033;font-size:25px;font-weight:750;font-variant-numeric:tabular-nums}
.poi-stat-note{margin-top:4px;color:#94a3b8;font-size:10px}
.poi-panel{overflow:hidden;margin-bottom:17px}
.poi-panel-head{padding:16px 18px;border-bottom:1px solid #e2e8f0}
.poi-panel-title{margin:0;color:#1e293b;font-size:15px;font-weight:750}
.poi-panel-subtitle{margin:4px 0 0;color:#94a3b8;font-size:11px}
.poi-table-wrap{padding:14px 16px 17px;overflow-x:auto}
.poi-table{width:100%;border-collapse:separate;border-spacing:0;font-size:11px;white-space:nowrap}
.poi-table th,.poi-table td{padding:9px 10px;border:1px solid #e5eaf0;text-align:center;vertical-align:middle}
.poi-table thead th{background:#f1f5f9;color:#475569;font-size:10px;font-weight:750;text-transform:uppercase}
.poi-table thead th.poi-brand-head{background:#eaf0fa;color:#334155}
.poi-table td.poi-description{text-align:left;white-space:normal;min-width:220px}
.poi-table td.poi-brand{text-align:left;font-weight:800;color:#334155;vertical-align:top}
.poi-table td.poi-total{background:#eef2ff;color:#334155;font-weight:800}
.poi-table tr.poi-brand-total td{background:#e7f5e9;color:#285c36;font-weight:800}
.poi-table tr.poi-grand-total td{background:#e2e8f0;color:#1e293b;font-weight:800}
.poi-table tr.poi-brand-total td:first-child,.poi-table tr.poi-grand-total td:first-child{text-align:left}
.poi-chart-grid{display:grid;grid-template-columns:1fr;gap:17px}
.poi-chart-area{padding:16px 18px;height:360px}
.poi-chart-area canvas{width:100%!important;height:100%!important}
.poi-empty{padding:28px;text-align:center;color:#94a3b8;font-size:12px}
@media(max-width:767px){.poi-page{padding-top:16px}.poi-title{font-size:19px}.poi-stats{grid-template-columns:1fr}.poi-chart-area{height:300px;padding:12px}.poi-table-wrap{padding:10px}.poi-table{font-size:10px}}
</style>
<div class="page-content poi-page">
    <div class="container-fluid px-3 px-lg-4">
        @php
            $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $records = collect($data ?? []);
            $brands = ['CAT', 'KOMATSU'];
            $grouped = [];
            foreach ($brands as $brand) {
                $brandRows = $records->where('BRAND', $brand);
                $descriptions = $brandRows->groupBy('ERROR_MESSAGE');
                foreach ($descriptions as $description => $items) {
                    $monthValues = array_fill(1, 12, 0);
                    foreach ($items as $item) {
                        $monthNumber = (int) substr((string) $item->MONTH, -2);
                        if ($monthNumber >= 1 && $monthNumber <= 12) {
                            $monthValues[$monthNumber] += (int) $item->TOTAL_INDUCED;
                        }
                    }
                    $grouped[$brand][] = ['description' => $description, 'total' => array_sum($monthValues), 'months' => $monthValues];
                }
                $grouped[$brand] = $grouped[$brand] ?? [];
            }
            $brandMonthly = [];
            $brandYearly = [];
            foreach ($brands as $brand) {
                $brandMonthly[$brand] = array_fill(1, 12, 0);
                foreach ($grouped[$brand] as $item) {
                    foreach ($item['months'] as $month => $value) {
                        $brandMonthly[$brand][$month] += $value;
                    }
                }
                $brandYearly[$brand] = array_sum($brandMonthly[$brand]);
            }
            $grandMonthly = array_fill(1, 12, 0);
            foreach ($brands as $brand) {
                foreach ($brandMonthly[$brand] as $month => $value) {
                    $grandMonthly[$month] += $value;
                }
            }
            $grandTotal = array_sum($grandMonthly);
            $chartLabels = array_values($months);
            $catChart = array_values($brandMonthly['CAT']);
            $komatsuChart = array_values($brandMonthly['KOMATSU']);
            $totalChart = array_values($grandMonthly);
            $catErrorNames = $records->where('BRAND', 'CAT')->pluck('ERROR_MESSAGE')->unique()->values();
            $komatsuErrorNames = $records->where('BRAND', 'KOMATSU')->pluck('ERROR_MESSAGE')->unique()->values();
            $chartSource = $records->map(function ($row) {
                return [
                    'brand' => $row->BRAND,
                    'month' => (int) substr((string) $row->MONTH, -2),
                    'name' => $row->ERROR_MESSAGE,
                    'value' => (int) $row->TOTAL_INDUCED,
                ];
            })->values();
        @endphp
        <div class="poi-heading">
            <div>
                <h4 class="poi-title">Periodic Operator Induced Year to Date Resume</h4>
                <p class="poi-subtitle">Rekapitulasi operator induced berdasarkan brand dan bulan pada tahun {{ $year }}.</p>
            </div>
        </div>
        <form method="GET" action="{{ url()->current() }}" class="poi-filter">
            <div>
                <label for="year">Tahun</label>
                <input type="number" id="year" name="year" min="2000" max="2100" value="{{ $year }}" required>
            </div>
            <button type="submit" class="poi-btn poi-btn-primary">Tampilkan Report</button>
            <a href="{{ url()->current() }}?year={{ date('Y') }}" class="poi-btn">Tahun Ini</a>
        </form>
        <div class="poi-stats">
            <div class="poi-stat">
                <div class="poi-stat-label">Total Operator Induced</div>
                <div class="poi-stat-value">{{ number_format($grandTotal) }}</div>
                <div class="poi-stat-note">Akumulasi CAT dan Komatsu tahun {{ $year }}</div>
            </div>
            <div class="poi-stat">
                <div class="poi-stat-label">Caterpillar</div>
                <div class="poi-stat-value">{{ number_format($brandYearly['CAT']) }}</div>
                <div class="poi-stat-note">Total induced Caterpillar</div>
            </div>
            <div class="poi-stat">
                <div class="poi-stat-label">Komatsu</div>
                <div class="poi-stat-value">{{ number_format($brandYearly['KOMATSU']) }}</div>
                <div class="poi-stat-note">Total induced Komatsu</div>
            </div>
        </div>
        <div class="poi-panel">
            <div class="poi-panel-head">
                <h5 class="poi-panel-title">Operator Induced Detail</h5>
                <p class="poi-panel-subtitle">Jumlah kejadian berdasarkan deskripsi dan bulan.</p>
            </div>
            <div class="poi-table-wrap">
                <table class="poi-table">
                    <thead>
                        <tr>
                            <th class="poi-brand-head">Brand</th>
                            <th class="poi-brand-head">Description</th>
                            <th class="poi-brand-head">Total</th>
                            @foreach($months as $month)
                                <th class="poi-brand-head">{{ $month }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if($records->isEmpty())
                            <tr><td colspan="15" class="poi-empty">Tidak ada data untuk tahun {{ $year }}.</td></tr>
                        @else
                            @foreach($brands as $brand)
                                @foreach($grouped[$brand] as $index => $item)
                                    <tr>
                                        @if($index === 0)
                                            <td class="poi-brand" rowspan="{{ count($grouped[$brand]) + 1 }}">{{ $brand }}</td>
                                        @endif
                                        <td class="poi-description">{{ $item['description'] }}</td>
                                        <td class="poi-total">{{ number_format($item['total']) }}</td>
                                        @foreach($item['months'] as $value)
                                            <td>{{ $value ? number_format($value) : '' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr class="poi-brand-total">
                                    <td>Total {{ $brand }}</td>
                                    <td>{{ number_format($brandYearly[$brand]) }}</td>
                                    @foreach($brandMonthly[$brand] as $value)
                                        <td>{{ $value ? number_format($value) : '' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif
                        <tr class="poi-grand-total">
                            <td colspan="2">Grand Total</td>
                            <td>{{ number_format($grandTotal) }}</td>
                            @foreach($grandMonthly as $value)
                                <td>{{ $value ? number_format($value) : '' }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="poi-chart-grid">
            <div class="poi-panel">
                <div class="poi-panel-head">
                    <h5 class="poi-panel-title">Caterpillar</h5>
                    <p class="poi-panel-subtitle">Tren operator induced Caterpillar per bulan.</p>
                </div>
                <div class="poi-chart-area"><canvas id="catChart"></canvas></div>
            </div>
            <div class="poi-panel">
                <div class="poi-panel-head">
                    <h5 class="poi-panel-title">Komatsu</h5>
                    <p class="poi-panel-subtitle">Tren operator induced Komatsu per bulan.</p>
                </div>
                <div class="poi-chart-area"><canvas id="komatsuChart"></canvas></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = @json($chartLabels);
    const catData = @json($catChart);
    const komatsuData = @json($komatsuChart);
    const totalData = @json($totalChart);
    const descriptions = @json($catErrorNames);
    const komatsuDescriptions = @json($komatsuErrorNames);
    const colors = ['#315b9a','#e6b94f','#65a879','#d97878','#7a91c7','#64748b','#a78bca','#5caaa4','#d68a62'];
    function renderBrandChart(canvasId, brand, errorNames) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !window.Chart) return;
        const source = @json($chartSource);
        const datasets = errorNames.map((name, index) => ({
            label: name,
            data: labels.map((label, i) => source.filter(row => row.brand === brand && row.name === name && row.month === i + 1).reduce((sum, row) => sum + row.value, 0)),
            backgroundColor: colors[index % colors.length],
            borderRadius: 2,
            maxBarThickness: 18
        }));
        const monthlyTotals = brand === 'CAT' ? catData : komatsuData;
        datasets.push({
            type: 'line',
            label: 'Total ' + brand,
            data: monthlyTotals,
            borderColor: '#8bbdc8',
            backgroundColor: '#8bbdc8',
            borderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 5,
            tension: .25,
            yAxisID: 'y1'
        });
        new Chart(canvas, {
            type: 'bar',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'top', labels: { boxWidth: 10, boxHeight: 10, font: { size: 10 } } } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { beginAtZero: true, title: { display: true, text: 'Total Per Error Message' }, ticks: { precision: 0 } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Total ' + brand }, ticks: { precision: 0 } }
                }
            }
        });
    }
    renderBrandChart('catChart', 'CAT', descriptions);
    renderBrandChart('komatsuChart', 'KOMATSU', komatsuDescriptions);
});
</script>
