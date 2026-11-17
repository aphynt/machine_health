@include('layout.head')
@include('layout.sidebar')
<style>
    .mhr-page{padding:24px 0 36px;background:#f8fafc;color:#263449;min-height:calc(100vh - 60px)}
    .mhr-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap}
    .mhr-title{font-size:21px;font-weight:700;margin:0;color:#243b5a}
    .mhr-subtitle{font-size:12px;color:#7b8798;margin-top:4px}
    .mhr-filter{display:flex;gap:10px;align-items:end;flex-wrap:wrap;background:#fff;border:1px solid #e5eaf1;border-radius:12px;padding:12px;margin-bottom:14px}
    .mhr-field{display:flex;flex-direction:column;gap:5px;min-width:150px}
    .mhr-field label{font-size:11px;font-weight:700;color:#64748b}
    .mhr-field input,.mhr-field select{height:36px;border:1px solid #dce3ed;border-radius:7px;padding:0 10px;background:#fff;color:#334155;font-size:12px}
    .mhr-btn{height:36px;padding:0 17px;border:0;border-radius:7px;background:#2563a6;color:#fff;font-size:12px;font-weight:700;cursor:pointer}
    .mhr-btn:disabled{opacity:.6;cursor:wait}
    .mhr-grid{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(0,1fr);gap:12px}
    .mhr-left,.mhr-right{display:flex;flex-direction:column;gap:12px;min-width:0}
    .mhr-card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;min-width:0}
    .mhr-card-head{height:39px;display:flex;align-items:center;justify-content:space-between;padding:0 12px;border-bottom:1px solid #e8edf3;font-size:12px;font-weight:700;color:#315f96}
    .mhr-card-body{padding:12px}
    .mhr-filter-boxes{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .mhr-chart{height:280px;position:relative}
    .mhr-chart.large{height:320px}
    .mhr-chart.small{height:220px}
    .mhr-tabs{display:flex;gap:5px;overflow-x:auto;padding:8px;border-bottom:1px solid #e8edf3}
    .mhr-tab{white-space:nowrap;border:1px solid #dce5ef;background:#f8fafc;border-radius:6px;padding:7px 10px;font-size:11px;color:#53657d;cursor:pointer}
    .mhr-tab.active{background:#e8f1fb;border-color:#8bb4df;color:#245c94;font-weight:700}
    .mhr-table-wrap{overflow:auto;max-height:290px}
    .mhr-table{width:100%;border-collapse:collapse;font-size:11px;white-space:nowrap}
    .mhr-table th{position:sticky;top:0;background:#f1f5f9;color:#52657d;text-align:left;padding:9px;border-bottom:1px solid #dfe6ef}
    .mhr-table td{padding:8px 9px;border-bottom:1px solid #edf1f5;color:#3c4b60}
    .mhr-pill{display:inline-block;padding:3px 7px;border-radius:20px;font-size:10px;font-weight:700}
    .mhr-normal{background:#e7f5ec;color:#25834c}.mhr-caution{background:#fff4d8;color:#a76b00}.mhr-critical{background:#fde8e7;color:#c33b35}
    .mhr-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-bottom:12px}
    .mhr-kpi{padding:10px;border:1px solid #e5eaf1;border-radius:8px;background:#fff}
    .mhr-kpi span{display:block;font-size:10px;color:#7b8798;margin-bottom:5px}
    .mhr-kpi strong{font-size:18px;color:#263b55}
    .mhr-status{font-size:12px;color:#64748b;margin-left:auto}
    .mhr-alert{display:none;background:#fff1f0;border:1px solid #ffd2ce;color:#a8322a;padding:10px 12px;border-radius:8px;font-size:12px;margin-bottom:12px}
    .mhr-loading{display:none;color:#426b99;font-size:12px;padding:10px 0}
    @media(max-width:1000px){.mhr-grid{grid-template-columns:1fr}.mhr-chart.large{height:280px}}
    @media(max-width:600px){.mhr-page{padding-top:16px}.mhr-kpis{grid-template-columns:repeat(2,1fr)}.mhr-filter-boxes{grid-template-columns:1fr}}
</style>

<div class="page-content mhr-page">
    <div class="container-fluid px-3 px-lg-4">
    <div class="mhr-head">
        <div>
            <h2 class="mhr-title">Machine Health Ranking Equipment</h2>
            <div class="mhr-subtitle">Monitoring vehicle score dan component health berdasarkan data VHM</div>
        </div>
        <div class="mhr-status" id="mhrStatus">Menunggu data...</div>
    </div>

    <div id="mhrAlert" class="mhr-alert"></div>

    <form id="mhrFilter" class="mhr-filter">
        <div class="mhr-field">
            <label for="mhrStart">Start Date</label>
            <input type="date" id="mhrStart" name="start" required>
        </div>
        <div class="mhr-field">
            <label for="mhrStop">Stop Date</label>
            <input type="date" id="mhrStop" name="stop" required>
        </div>
        <button type="submit" class="mhr-btn" id="mhrLoadBtn">Load Dashboard</button>
        <div class="mhr-loading" id="mhrLoading">Mengambil data dari database...</div>
    </form>

    <div class="mhr-kpis">
        <div class="mhr-kpi"><span>Total Equipment</span><strong id="kpiEquipment">0</strong></div>
        <div class="mhr-kpi"><span>Normal Score</span><strong id="kpiNormal">0</strong></div>
        <div class="mhr-kpi"><span>Caution Score</span><strong id="kpiCaution">0</strong></div>
        <div class="mhr-kpi"><span>Critical Score</span><strong id="kpiCritical">0</strong></div>
    </div>

    <div class="mhr-grid">
        <div class="mhr-left">
            <div class="mhr-card">
                <div class="mhr-card-head">Equipment Filter</div>
                <div class="mhr-card-body mhr-filter-boxes">
                    <div class="mhr-field">
                        <label for="mhrGroup">Equipment Group</label>
                        <select id="mhrGroup"><option value="">All Equipment Group</option></select>
                    </div>
                    <div class="mhr-field">
                        <label for="mhrEquipment">Equipment ID</label>
                        <select id="mhrEquipment"><option value="">All Equipment</option></select>
                    </div>
                </div>
            </div>

            <div class="mhr-card">
                <div class="mhr-card-head">Vehicle Score</div>
                <div class="mhr-card-body"><div class="mhr-chart large"><canvas id="vehicleScoreChart"></canvas></div></div>
            </div>

            <div class="mhr-card">
                <div class="mhr-card-head">Vehicle Score Table</div>
                <div class="mhr-table-wrap">
                    <table class="mhr-table">
                        <thead><tr><th>Equipment ID</th><th>Equipment Group</th><th>Normal</th><th>Caution</th><th>Critical</th><th>Total Score</th></tr></thead>
                        <tbody id="vehicleTable"><tr><td colspan="6">Belum ada data</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mhr-right">
            <div class="mhr-card">
                <div class="mhr-tabs" id="componentTabs">
                    <button type="button" class="mhr-tab active" data-component="">Component Score</button>
                </div>
                <div class="mhr-card-head" id="componentTitle">Component Score</div>
                <div class="mhr-card-body"><div class="mhr-chart large"><canvas id="componentScoreChart"></canvas></div></div>
            </div>

            <div class="mhr-card">
                <div class="mhr-card-head">Component Score By Group</div>
                <div class="mhr-card-body"><div class="mhr-chart small"><canvas id="componentGroupChart"></canvas></div></div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = @json(route('workshop.machine-health-ranking.data'));
    const form = document.getElementById('mhrFilter');
    const startInput = document.getElementById('mhrStart');
    const stopInput = document.getElementById('mhrStop');
    const groupSelect = document.getElementById('mhrGroup');
    const equipmentSelect = document.getElementById('mhrEquipment');
    const alertBox = document.getElementById('mhrAlert');
    const loading = document.getElementById('mhrLoading');
    const loadBtn = document.getElementById('mhrLoadBtn');

    let rawData = [];
    let selectedComponent = '';
    let vehicleChart = null;
    let componentChart = null;
    let groupChart = null;

    const number = value => Number(value || 0);
    const fmt = value => new Intl.NumberFormat('id-ID', {maximumFractionDigits: 2}).format(number(value));
    const val = (row, key) => row[key] ?? row[key.toUpperCase()] ?? row[key.toLowerCase()];
    const score = (row, key) => number(val(row, key));
    const unique = values => [...new Set(values.filter(v => v !== null && v !== undefined && v !== ''))].sort();

    function showError(message) {
        alertBox.textContent = message;
        alertBox.style.display = 'block';
    }

    function filteredData() {
        return rawData.filter(row =>
            (!groupSelect.value || val(row, 'EQU_TYPEID') === groupSelect.value) &&
            (!equipmentSelect.value || val(row, 'VHC_ID') === equipmentSelect.value)
        );
    }

    function summarizeVehicles(rows) {
        const map = new Map();
        rows.forEach(row => {
            const id = val(row, 'VHC_ID') || '-';
            if (!map.has(id)) map.set(id, {
                id,
                group: val(row, 'EQU_TYPEID') || '-',
                normal: 0, caution: 0, critical: 0
            });
            const item = map.get(id);
            item.normal += score(row, 'SCORE_NORMAL');
            item.caution += score(row, 'SCORE_CAUTION');
            item.critical += score(row, 'SCORE_CRITICAL');
        });
        return [...map.values()].map(item => ({...item, total: item.normal + item.caution + item.critical}))
            .sort((a, b) => b.critical - a.critical || b.total - a.total);
    }

    function renderVehicle(rows) {
        const summary = summarizeVehicles(rows);
        document.getElementById('kpiEquipment').textContent = fmt(summary.length);
        document.getElementById('kpiNormal').textContent = fmt(summary.reduce((s, r) => s + r.normal, 0));
        document.getElementById('kpiCaution').textContent = fmt(summary.reduce((s, r) => s + r.caution, 0));
        document.getElementById('kpiCritical').textContent = fmt(summary.reduce((s, r) => s + r.critical, 0));

        const tbody = document.getElementById('vehicleTable');
        tbody.innerHTML = summary.length ? summary.map(item => `
            <tr>
                <td><b>${item.id}</b></td><td>${item.group}</td>
                <td>${fmt(item.normal)}</td><td>${fmt(item.caution)}</td>
                <td><span class="mhr-pill ${item.critical > 0 ? 'mhr-critical' : 'mhr-normal'}">${fmt(item.critical)}</span></td>
                <td><b>${fmt(item.total)}</b></td>
            </tr>`).join('') : '<tr><td colspan="6">Tidak ada data pada filter ini</td></tr>';

        if (vehicleChart) vehicleChart.destroy();
        vehicleChart = new Chart(document.getElementById('vehicleScoreChart'), {
            type: 'bar',
            data: {
                labels: summary.map(r => r.id),
                datasets: [
                    {label: 'Normal', data: summary.map(r => r.normal), backgroundColor: '#65b887'},
                    {label: 'Caution', data: summary.map(r => r.caution), backgroundColor: '#f0bd58'},
                    {label: 'Critical', data: summary.map(r => r.critical), backgroundColor: '#dc7771'}
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {legend: {position: 'bottom'}},
                scales: {x: {stacked: true, ticks: {autoSkip: true, maxRotation: 60}}, y: {stacked: true, beginAtZero: true}}
            }
        });
    }

    function renderComponent(rows) {
        const selectedRows = rows.filter(row => !selectedComponent || val(row, 'NAME_ITEM') === selectedComponent);
        const byDate = new Map();
        selectedRows.forEach(row => {
            const date = String(val(row, 'DATE') || '').slice(0, 10);
            if (!date) return;
            if (!byDate.has(date)) byDate.set(date, {normal: 0, caution: 0, critical: 0});
            const item = byDate.get(date);
            item.normal += score(row, 'SCORE_NORMAL');
            item.caution += score(row, 'SCORE_CAUTION');
            item.critical += score(row, 'SCORE_CRITICAL');
        });
        const dates = [...byDate.keys()].sort();
        const totals = key => dates.map(date => byDate.get(date)[key]);

        if (componentChart) componentChart.destroy();
        componentChart = new Chart(document.getElementById('componentScoreChart'), {
            type: 'bar',
            data: {labels: dates, datasets: [
                {label: 'Normal', data: totals('normal'), backgroundColor: '#65b887'},
                {label: 'Caution', data: totals('caution'), backgroundColor: '#f0bd58'},
                {label: 'Critical', data: totals('critical'), backgroundColor: '#dc7771'}
            ]},
            options: {responsive: true, maintainAspectRatio: false, plugins: {legend: {position: 'bottom'}}, scales: {x: {stacked: true}, y: {stacked: true, beginAtZero: true}}}
        });

        const byGroup = new Map();
        selectedRows.forEach(row => {
            const group = val(row, 'GROUP_ITEM') || val(row, 'EQU_TYPEID') || 'Other';
            byGroup.set(group, (byGroup.get(group) || 0) + score(row, 'SCORE_CRITICAL') + score(row, 'SCORE_CAUTION'));
        });
        const groups = [...byGroup.entries()].sort((a,b) => b[1]-a[1]);

        if (groupChart) groupChart.destroy();
        groupChart = new Chart(document.getElementById('componentGroupChart'), {
            type: 'bar',
            data: {labels: groups.map(x => x[0]), datasets: [{label: 'Caution + Critical Score', data: groups.map(x => x[1]), backgroundColor: '#8aa9d1', borderRadius: 4}]},
            options: {indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: {legend: {display: false}}, scales: {x: {beginAtZero: true}, y: {grid: {display: false}}}}
        });
        document.getElementById('componentTitle').textContent = selectedComponent ? `Component Score - ${selectedComponent}` : 'Component Score';
    }

    function render() {
        const rows = filteredData();
        renderVehicle(rows);
        renderComponent(rows);
    }

    function fillFilter(select, values, firstText) {
        select.innerHTML = '';
        const first = document.createElement('option');
        first.value = '';
        first.textContent = firstText;
        select.appendChild(first);
        values.forEach(value => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            select.appendChild(option);
        });
    }

    function renderTabs() {
        const components = unique(rawData.map(row => val(row, 'NAME_ITEM')));
        const tabs = document.getElementById('componentTabs');
        tabs.innerHTML = '';
        const all = document.createElement('button');
        all.type = 'button'; all.className = 'mhr-tab' + (!selectedComponent ? ' active' : '');
        all.textContent = 'Component Score'; all.dataset.component = '';
        tabs.appendChild(all);
        components.forEach(component => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'mhr-tab' + (selectedComponent === component ? ' active' : '');
            btn.textContent = component;
            btn.dataset.component = component;
            tabs.appendChild(btn);
        });
    }

    async function loadData() {
        alertBox.style.display = 'none';
        loading.style.display = 'block';
        loadBtn.disabled = true;

        try {
            const params = new URLSearchParams({start: startInput.value, stop: stopInput.value});
            const response = await fetch(`${apiUrl}?${params.toString()}`, {
                headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Gagal mengambil data.');

            rawData = result.data || [];
            selectedComponent = '';
            fillFilter(groupSelect, unique(rawData.map(row => val(row, 'EQU_TYPEID'))), 'All Equipment Group');
            fillFilter(equipmentSelect, unique(rawData.map(row => val(row, 'VHC_ID'))), 'All Equipment');
            renderTabs();
            render();
            document.getElementById('mhrStatus').textContent = `${fmt(result.totalRows)} records • ${result.start} s/d ${result.stop}`;
        } catch (error) {
            rawData = [];
            renderTabs();
            render();
            showError(error.message || 'Terjadi kesalahan saat mengambil data.');
            document.getElementById('mhrStatus').textContent = 'Gagal memuat data';
        } finally {
            loading.style.display = 'none';
            loadBtn.disabled = false;
        }
    }

    form.addEventListener('submit', event => {
        event.preventDefault();
        loadData();
    });
    groupSelect.addEventListener('change', () => {
        const group = groupSelect.value;
        const equipment = unique(rawData.filter(row => !group || val(row, 'EQU_TYPEID') === group).map(row => val(row, 'VHC_ID')));
        fillFilter(equipmentSelect, equipment, 'All Equipment');
        render();
    });
    equipmentSelect.addEventListener('change', render);
    document.getElementById('componentTabs').addEventListener('click', event => {
        const button = event.target.closest('[data-component]');
        if (!button) return;
        selectedComponent = button.dataset.component;
        renderTabs();
        render();
    });

    const today = new Date();
    const first = new Date(today.getFullYear(), today.getMonth(), 1);
    const toDate = date => `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
    startInput.value = toDate(first);
    stopInput.value = toDate(today);
    loadData();
});
</script>
