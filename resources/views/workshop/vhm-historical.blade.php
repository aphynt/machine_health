@include('layout.head')
@include('layout.sidebar')
<style>
.vhm-page{min-height:calc(100vh - 60px);padding:22px 0 36px;background:#f6f8fc;color:#1e293b}
.vhm-header{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap;margin-bottom:18px}
.vhm-title{margin:0;font-size:23px;font-weight:750;letter-spacing:-.4px;color:#172033}
.vhm-subtitle{margin:5px 0 0;color:#718096;font-size:12px}
.vhm-filter,.vhm-panel,.vhm-kpi,.vhm-side{background:#fff;border:1px solid #e4eaf2;border-radius:13px;box-shadow:0 3px 12px rgba(15,23,42,.035)}
.vhm-filter{display:grid;grid-template-columns:minmax(180px,1fr) minmax(180px,1fr) auto;gap:12px;align-items:end;padding:15px;margin-bottom:16px}
.vhm-field label{display:block;margin:0 0 5px;color:#64748b;font-size:10px;font-weight:750;text-transform:uppercase;letter-spacing:.4px}
.vhm-field input,.vhm-field select{width:100%;height:37px;padding:7px 10px;border:1px solid #dbe3ed;border-radius:8px;background:#fff;color:#334155;font-size:12px;outline:none}
.vhm-field input:focus,.vhm-field select:focus{border-color:#7c9ac7;box-shadow:0 0 0 3px rgba(91,130,190,.12)}
.vhm-btn{height:37px;padding:0 16px;border:0;border-radius:8px;background:#315b9a;color:#fff;font-size:12px;font-weight:700;cursor:pointer}
.vhm-layout{display:grid;grid-template-columns:minmax(0,1fr) 260px;gap:15px;align-items:start}
.vhm-main{min-width:0}
.vhm-sidebar{display:grid;gap:14px}
.vhm-panel{overflow:hidden;margin-bottom:15px}
.vhm-panel-head{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid #edf1f6}
.vhm-panel-title{margin:0;color:#26364d;font-size:13px;font-weight:750}
.vhm-panel-note{margin:4px 0 0;color:#94a3b8;font-size:10px}
.vhm-chart{height:340px;padding:14px 16px}
.vhm-chart canvas{width:100%!important;height:100%!important}
.vhm-kpis{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:15px}
.vhm-kpi{padding:14px 15px}
.vhm-kpi-label{color:#748198;font-size:10px;font-weight:750;text-transform:uppercase;letter-spacing:.45px}
.vhm-kpi-value{margin-top:7px;color:#1e293b;font-size:25px;font-weight:800;font-variant-numeric:tabular-nums}
.vhm-kpi-unit{margin-top:3px;color:#94a3b8;font-size:10px}
.vhm-side{padding:14px}
.vhm-side-title{margin:0 0 11px;color:#334155;font-size:12px;font-weight:750}
.vhm-side-list{display:grid;gap:8px}
.vhm-side-item{display:flex;justify-content:space-between;gap:8px;padding:8px 9px;border-radius:8px;background:#f7f9fc;font-size:10px}
.vhm-side-item span:first-child{color:#64748b;overflow:hidden;text-overflow:ellipsis}
.vhm-side-item strong{color:#334155}
.vhm-location{height:250px;padding:12px}
.vhm-empty{display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;font-size:12px}
.vhm-alert{padding:11px 13px;margin-bottom:14px;border:1px solid #f4dfb2;border-radius:9px;background:#fff9eb;color:#946b20;font-size:11px}
.vhm-footer{margin-top:2px;color:#94a3b8;font-size:10px}
@media(max-width:1100px){.vhm-layout{grid-template-columns:1fr}.vhm-sidebar{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:767px){.vhm-page{padding-top:14px}.vhm-title{font-size:19px}.vhm-filter{grid-template-columns:repeat(2,minmax(0,1fr))}.vhm-filter .vhm-btn{width:100%}.vhm-kpis{gap:8px}.vhm-kpi{padding:11px}.vhm-kpi-value{font-size:20px}.vhm-chart{height:290px;padding:10px}.vhm-sidebar{grid-template-columns:1fr}}
</style>
<div class="page-content vhm-page">
    <div class="container-fluid px-3 px-lg-4">
        <div class="vhm-header">
            <div>
                <h4 class="vhm-title">VHM Historical Monitoring</h4>
                <p class="vhm-subtitle">Pemantauan parameter equipment berdasarkan rentang tanggal dan waktu yang dipilih.</p>
            </div>
        </div>
        <div id="vhmAlert" class="vhm-alert" style="display:none"></div>
        <div id="vhmLoading" class="vhm-alert" style="display:block">Memuat data VHM Historical...</div>
        <form id="vhmFilterForm" class="vhm-filter">
            <div class="vhm-field">
                <label for="vhc_id">Equipment ID</label>
                <select id="vhc_id" name="vhc_id" required>
                    <option value="">Memuat equipment...</option>
                </select>
            </div>
            <div class="vhm-field">
                <label for="start">Tanggal & Waktu Mulai</label>
                <input type="datetime-local" id="start" name="start" value="" required>
            </div>
            <div class="vhm-field">
                <label for="stop">Tanggal & Waktu Selesai</label>
                <input type="datetime-local" id="stop" name="stop" value="" required>
            </div>
            <div class="vhm-field">
                <label for="operatorFilter">Operator</label>
                <select id="operatorFilter">
                    <option value="">Semua Operator</option>
                </select>
            </div>
            <button class="vhm-btn" id="loadDataBtn" type="submit">Tampilkan Data</button>
        </form>
        <div class="vhm-layout">
            <main class="vhm-main">
                <div class="vhm-kpis">
                    <div class="vhm-kpi">
                        <div class="vhm-kpi-label">Maximum</div>
                        <div class="vhm-kpi-value" id="maxValue">-</div>
                        <div class="vhm-kpi-unit" id="maxUnit">Nilai tertinggi</div>
                    </div>
                    <div class="vhm-kpi">
                        <div class="vhm-kpi-label">Average</div>
                        <div class="vhm-kpi-value" id="avgValue">-</div>
                        <div class="vhm-kpi-unit" id="avgUnit">Nilai rata-rata</div>
                    </div>
                    <div class="vhm-kpi">
                        <div class="vhm-kpi-label">Minimum</div>
                        <div class="vhm-kpi-value" id="minValue">-</div>
                        <div class="vhm-kpi-unit" id="minUnit">Nilai terendah</div>
                    </div>
                </div>
                <section class="vhm-panel">
                    <div class="vhm-panel-head">
                        <div>
                            <h5 class="vhm-panel-title">Trend Data Historical</h5>
                            <p class="vhm-panel-note">Pergerakan nilai parameter terhadap waktu.</p>
                        </div>
                        <div class="vhm-field" style="min-width:190px">
                            <select id="parameterFilter">
                                <option value="">Memuat parameter...</option>
                            </select>
                        </div>
                    </div>
                    <div class="vhm-chart">
                        <canvas id="historicalChart"></canvas>
                        <div id="chartEmpty" class="vhm-empty" style="display:none">Tidak ada data untuk filter yang dipilih.</div>
                    </div>
                </section>
                <section class="vhm-panel">
                    <div class="vhm-panel-head">
                        <div>
                            <h5 class="vhm-panel-title">Treemap Location</h5>
                            <p class="vhm-panel-note">Sebaran jumlah rekaman berdasarkan lokasi.</p>
                        </div>
                    </div>
                    <div class="vhm-location"><canvas id="locationChart"></canvas></div>
                </section>
            </main>
            <aside class="vhm-sidebar">
                <section class="vhm-side">
                    <h5 class="vhm-side-title">Equipment Information</h5>
                    <div class="vhm-side-list">
                        <div class="vhm-side-item"><span>Equipment</span><strong id="infoEquipment">-</strong></div>
                        <div class="vhm-side-item"><span>Data Records</span><strong id="infoRecords">-</strong></div>
                        <div class="vhm-side-item"><span>Operators</span><strong id="infoOperators">-</strong></div>
                        <div class="vhm-side-item"><span>Parameters</span><strong id="infoParameters">-</strong></div>
                    </div>
                </section>
                <section class="vhm-side">
                    <h5 class="vhm-side-title">Parameter Threshold</h5>
                    <div class="vhm-side-list">
                        <div class="vhm-side-item"><span>Maximum Caution</span><strong id="maxThreshold">-</strong></div>
                        <div class="vhm-side-item"><span>Minimum Normal</span><strong id="minThreshold">-</strong></div>
                        <div class="vhm-side-item"><span>Selected Parameter</span><strong id="selectedParameter">-</strong></div>
                    </div>
                </section>
                <section class="vhm-side">
                    <h5 class="vhm-side-title">Data Information</h5>
                    <div class="vhm-side-list">
                        <div class="vhm-side-item"><span>Start</span><strong id="infoStart">-</strong></div>
                        <div class="vhm-side-item"><span>Stop</span><strong id="infoStop">-</strong></div>
                        <div class="vhm-side-item"><span>Source</span><strong>Tracking Log</strong></div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const apiUrl = @json(route('workshop.vhm-historical.data'));
    const form = document.getElementById('vhmFilterForm');
    const equipment = document.getElementById('vhc_id');
    const startInput = document.getElementById('start');
    const stopInput = document.getElementById('stop');
    const operator = document.getElementById('operatorFilter');
    const parameter = document.getElementById('parameterFilter');
    const loading = document.getElementById('vhmLoading');
    const alertBox = document.getElementById('vhmAlert');
    const button = document.getElementById('loadDataBtn');
    const empty = document.getElementById('chartEmpty');
    const chartCanvas = document.getElementById('historicalChart');
    const locationCanvas = document.getElementById('locationChart');

    let rows = [];
    let historicalChart = null;
    let locationChart = null;

    function fmt(value) {
        return value === null || !Number.isFinite(Number(value))
            ? '-'
            : new Intl.NumberFormat('id-ID', {maximumFractionDigits: 2}).format(Number(value));
    }

    function showError(message) {
        alertBox.textContent = message;
        alertBox.style.display = 'block';
    }

    function clearError() {
        alertBox.textContent = '';
        alertBox.style.display = 'none';
    }

    function setLoading(active) {
        loading.style.display = active ? 'block' : 'none';
        button.disabled = active;
        button.textContent = active ? 'Memuat...' : 'Tampilkan Data';
    }

    function fillSelect(select, values, firstLabel) {
        select.innerHTML = '';
        if (firstLabel !== null) {
            const first = document.createElement('option');
            first.value = '';
            first.textContent = firstLabel;
            select.appendChild(first);
        }
        values.forEach(value => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            select.appendChild(option);
        });
    }

    function selectedRows() {
        return rows.filter(row =>
            row.remark === parameter.value &&
            (!operator.value || row.operator === operator.value) &&
            row.value !== null
        );
    }

    function updateDashboard() {
        const filtered = selectedRows().sort((a, b) => new Date(a.time) - new Date(b.time));
        const values = filtered.map(row => Number(row.value));
        const max = values.length ? Math.max(...values) : null;
        const min = values.length ? Math.min(...values) : null;
        const avg = values.length ? values.reduce((sum, value) => sum + value, 0) / values.length : null;

        document.getElementById('maxValue').textContent = fmt(max);
        document.getElementById('avgValue').textContent = fmt(avg);
        document.getElementById('minValue').textContent = fmt(min);
        document.getElementById('selectedParameter').textContent = parameter.value || '-';
        document.getElementById('maxThreshold').textContent = fmt(filtered.length ? filtered[0].max : null);
        document.getElementById('minThreshold').textContent = fmt(filtered.length ? filtered[0].min : null);

        empty.style.display = filtered.length ? 'none' : 'flex';
        chartCanvas.style.display = filtered.length ? 'block' : 'none';

        if (historicalChart) historicalChart.destroy();

        if (filtered.length) {
            const labels = filtered.map(row => row.time);
            const datasets = [{
                label: parameter.value,
                data: values,
                borderColor: '#4778b8',
                backgroundColor: 'rgba(71,120,184,.12)',
                borderWidth: 2,
                pointRadius: filtered.length > 500 ? 0 : 2,
                pointHoverRadius: 4,
                tension: .22,
                fill: true
            }];

            historicalChart = new Chart(chartCanvas, {
                type: 'line',
                data: {labels, datasets},
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {mode: 'index', intersect: false},
                    plugins: {legend: {position: 'bottom', labels: {usePointStyle: true, boxWidth: 7, font: {size: 10}}}},
                    scales: {
                        x: {ticks: {maxTicksLimit: 9, font: {size: 9}}, grid: {display: false}},
                        y: {beginAtZero: false, ticks: {font: {size: 10}}}
                    }
                }
            });
        }

        const locations = {};
        rows.filter(row => row.remark === parameter.value && (!operator.value || row.operator === operator.value))
            .forEach(row => locations[row.location] = (locations[row.location] || 0) + 1);

        const entries = Object.entries(locations).sort((a, b) => b[1] - a[1]).slice(0, 12);
        if (locationChart) locationChart.destroy();

        locationChart = new Chart(locationCanvas, {
            type: 'bar',
            data: {
                labels: entries.map(item => item[0]),
                datasets: [{label: 'Jumlah Data', data: entries.map(item => item[1]), backgroundColor: '#8ba8cf', borderRadius: 5}]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {legend: {display: false}},
                scales: {
                    x: {beginAtZero: true, ticks: {precision: 0, font: {size: 9}}},
                    y: {grid: {display: false}, ticks: {font: {size: 9}}}
                }
            }
        });
    }

    async function loadData() {
        clearError();
        setLoading(true);

        const params = new URLSearchParams();
        if (equipment.value) params.set('vhc_id', equipment.value);
        if (startInput.value) params.set('start', startInput.value);
        if (stopInput.value) params.set('stop', stopInput.value);

        try {
            const response = await fetch(`${apiUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Gagal mengambil data VHM.');
            }

            fillSelect(equipment, result.equipmentIds || [], null);
            equipment.value = result.vhcId || '';

            startInput.value = result.start || '';
            stopInput.value = result.stop || '';

            fillSelect(operator, result.operators || [], 'Semua Operator');
            fillSelect(parameter, result.parameters || [], null);

            rows = result.chartData || [];

            document.getElementById('infoEquipment').textContent = result.vhcId || '-';
            document.getElementById('infoRecords').textContent = fmt(result.recordsCount || 0);
            document.getElementById('infoOperators').textContent = fmt((result.operators || []).length);
            document.getElementById('infoParameters').textContent = fmt((result.parameters || []).length);
            document.getElementById('infoStart').textContent = result.start || '-';
            document.getElementById('infoStop').textContent = result.stop || '-';

            if (parameter.options.length) {
                parameter.selectedIndex = 0;
                updateDashboard();
            } else {
                updateDashboard();
                empty.style.display = 'flex';
                chartCanvas.style.display = 'none';
            }
        } catch (error) {
            showError(error.message || 'Terjadi kesalahan saat mengambil data.');
            rows = [];
            updateDashboard();
        } finally {
            setLoading(false);
        }
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        loadData();
    });

    equipment.addEventListener('change', loadData);
    parameter.addEventListener('change', updateDashboard);
    operator.addEventListener('change', updateDashboard);

    // Data baru diminta otomatis ketika halaman fitur ini selesai dibuka.
    const now = new Date();
    const twoHoursAgo = new Date(now.getTime() - (2 * 60 * 60 * 1000));
    const toLocalInput = date => {
        const local = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
        return local.toISOString().slice(0, 16);
    };

    startInput.value = toLocalInput(twoHoursAgo);
    stopInput.value = toLocalInput(now);
    loadData();
});
</script>
