@include('layout.head')
@include('layout.sidebar')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
    .rts-page {
        --rts-blue: #2865ab;
        --rts-ink: #172b4d;
        --rts-muted: #75849b;
        --rts-line: #e4eaf2;
        --rts-bg: #f3f6fb;
        --rts-card: #fff;
        background: var(--rts-bg);
        min-height: calc(100vh - 70px);
        color: var(--rts-ink);
        /* font-family: inherit; */
        padding: 20px 22px 26px;
    }
    .rts-page * { box-sizing: border-box; }
    .rts-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:7px; }
    .rts-title { font-size:21px; font-weight:800; letter-spacing:-.5px; margin:0 0 3px; color:#162943; }
    .rts-subtitle { margin:0; color:#77879d; font-size:12px; }
    .rts-status { display:inline-flex; align-items:center; gap:8px; color:#6a7a90; font-size:11px; background:#fff; padding:8px 12px; border:1px solid var(--rts-line); border-radius:99px; font-weight:700; }
    .rts-dot { display:inline-block; width:8px; height:8px; flex:none; border-radius:50%; background:#a1aec0; }
    .rts-dot.is-live { background:#17a66c; box-shadow:0 0 0 4px rgba(23,166,108,.12); }
    .rts-dot.is-old { background:#d18b27; box-shadow:0 0 0 4px rgba(209,139,39,.12); }
    .rts-surface { background:var(--rts-card); border:1px solid var(--rts-line); border-radius:5px; box-shadow:0 3px 14px rgba(36,67,107,.035); }
    .rts-toolbar { padding:17px; margin-bottom:16px; }
    .rts-filter-grid { display:grid; grid-template-columns:minmax(130px,.9fr) minmax(140px,.9fr) minmax(235px,1.8fr) 100px 85px auto; gap:12px; align-items:end; }
    .rts-field { min-width:0; position:relative; }
    .rts-field > label, .rts-mini-label { display:block; color:#65768f; font-size:10px; letter-spacing:.3px; font-weight:800; margin:0 0 7px; text-transform:uppercase; }
    .rts-input { width:100%; display:block; height:39px; font-size:12px; line-height:1.3; padding:8px 11px; border:1px solid #d9e3ee; color:#30415b; background:#fff; border-radius:8px; outline:none; }
    .rts-input:focus { border-color:#72a5e4; box-shadow:0 0 0 3px rgba(40,101,171,.10); }
    .rts-button { border:1px solid #dce4f0; background:#fff; color:#53647b; border-radius:8px; height:39px; display:inline-flex; align-items:center; justify-content:center; gap:7px; cursor:pointer; padding:0 13px; font-size:12px; font-weight:800; white-space:nowrap; }
    .rts-button:hover { background:#f4f7fc; }
    .rts-button-primary { border-color:var(--rts-blue); background:var(--rts-blue); color:#fff; }
    .rts-button-primary:hover { background:#1d528f; }
    .rts-button:disabled { cursor:not-allowed; opacity:.55; }
    .rts-actions { display:flex; gap:8px; }
    .rts-choose { text-align:left; width:100%; justify-content:space-between; font-weight:650; overflow:hidden; }
    .rts-choose span { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .rts-parameter-menu { position:absolute; top:calc(100% + 5px); left:0; right:0; z-index:1100; min-width:280px; background:white; border:1px solid #dbe5ee; box-shadow:0 15px 45px rgba(19,45,79,.15); border-radius:10px; padding:10px; }
    .rts-parameter-menu[hidden] { display:none!important; }
    .rts-parameter-search { font-size:12px; height:35px; margin-bottom:8px; }
    .rts-parameter-list { max-height:230px; overflow-y:auto; }
    .rts-parameter-option { display:flex; align-items:center; gap:9px; border-radius:6px; font-size:11px; padding:8px 7px; cursor:pointer; color:#334b69; }
    .rts-parameter-option:hover { background:#f2f6fc; }
    .rts-parameter-option[hidden] { display:none!important; }
    .rts-parameter-option input { accent-color:var(--rts-blue); width:14px; height:14px; margin:0; }
    .rts-parameter-code { margin-left:auto; font-size:9px; color:#8796aa; white-space:nowrap; }
    .rts-menu-note { border-top:1px solid #edf1f6; padding:9px 4px 1px; margin-top:6px; font-size:10px; color:#8a98aa; }
    .rts-meta { margin-top:12px; display:flex; align-items:center; flex-wrap:wrap; gap:9px 18px; color:#8b99ab; font-size:11px; }
    .rts-meta strong { color:#536c8c; }
    .rts-meta .rts-inline-dot { width:4px; height:4px; border-radius:50%; background:#c6d0dc; }
    .rts-main { display:grid; grid-template-columns:minmax(0, .93fr) minmax(0, 1.07fr); gap:15px; align-items:start; }
    .rts-column { min-width:0; }
    .rts-section-title { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:15px 17px 13px; }
    .rts-section-title h2 { font-size:13px; font-weight:800; color:#253c57; margin:0; }
    .rts-section-title small { font-size:10px; color:#8696aa; }
    .rts-chart-stack { display:flex; flex-direction:column; gap:12px; padding:0 12px 12px; }
    .rts-chart-card { background:#fff; border:1px solid var(--rts-line); border-radius:10px; padding:13px 14px 8px; min-width:0; }
    .rts-chart-header { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin-bottom:7px; }
    .rts-chart-name { color:#334d6e; font-size:11px; font-weight:800; margin:0; }
    .rts-chart-code { color:#9aa7b8; font-size:9px; margin-top:2px; }
    .rts-reading { font-size:19px; font-weight:800; letter-spacing:-.5px; color:#2b6bb1; font-variant-numeric:tabular-nums; }
    .rts-chart-card svg { display:block; width:100%; height:145px; }
    .rts-chart-foot { font-size:10px; color:#8a9aae; margin-top:4px; display:flex; justify-content:space-between; gap:8px; }
    .rts-chart-warning { color:#b4772a; }
    .rts-top-area { display:grid; grid-template-columns:minmax(0,.78fr) minmax(0,1.22fr); gap:12px; }
    .rts-gauges { padding:11px; display:flex; flex-direction:column; gap:9px; }
    .rts-gauge { border:1px solid #e2eaf3; background:linear-gradient(120deg,#f7faff 0%,#fff 90%); border-radius:10px; padding:11px 12px; min-height:78px; display:flex; align-items:center; justify-content:space-between; gap:10px; }
    .rts-gauge-name { display:block; color:#596d89; font-size:10px; font-weight:800; max-width:145px; line-height:1.45; }
    .rts-gauge-time { display:block; color:#9baabd; font-size:9px; margin-top:5px; }
    .rts-gauge-value { font-size:29px; line-height:1; color:#213e61; font-weight:800; letter-spacing:-1.5px; font-family:ui-monospace,SFMono-Regular,Consolas,monospace; font-variant-numeric:tabular-nums; white-space:nowrap; }
    .rts-gauge.gauge-empty .rts-gauge-value { color:#b5c1cf; }
    .rts-info-head { display:flex; justify-content:space-between; align-items:center; padding:14px 15px; border-bottom:1px solid var(--rts-line); gap:8px; }
    .rts-info-head h2 { margin:0; font-size:13px; font-weight:800; }
    .rts-chip { border-radius:99px; background:#eaf2fb; color:#2964aa; font-size:10px; font-weight:800; padding:5px 9px; }
    .rts-detail { padding:12px 15px 15px; }
    .rts-detail-row { display:grid; grid-template-columns:39% minmax(0,1fr); gap:8px; padding:6px 0; border-bottom:1px dashed #e8edf3; font-size:11px; }
    .rts-detail-row:last-child { border-bottom:none; }
    .rts-detail-row span { color:#8391a4; }
    .rts-detail-row strong { color:#3c4d66; font-weight:700; overflow-wrap:anywhere; text-align:right; }
    .rts-location { color:#2865ab!important; }
    .rts-map-section { margin-top:12px; overflow:hidden; }
    .rts-map-actions { display:flex; gap:7px; }
    .rts-map-button { height:30px; padding:0 9px; font-size:10px; }
    .rts-map-canvas { width:100%; height:365px; background:repeating-linear-gradient(0deg,#e6edf3,#e6edf3 1px,transparent 1px,transparent 30px),repeating-linear-gradient(90deg,#e6edf3,#e6edf3 1px,transparent 1px,transparent 30px),#f4f7fb; }
    .rts-map-foot { padding:10px 14px; background:#fff; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px 15px; color:#8797aa; font-size:10px; }
    .rts-map-foot strong { color:#4a617e; }
    .rts-map-foot a { color:#2865ab; font-weight:800; text-decoration:none; }
    .rts-alert { margin:0 0 14px; background:#fff4ec; border:1px solid #ffd9c2; border-radius:9px; padding:12px 14px; color:#9e5528; font-size:12px; }
    .rts-alert[hidden] { display:none!important; }
    .rts-empty { display:flex; min-height:160px; align-items:center; justify-content:center; flex-direction:column; gap:10px; color:#98a6b7; font-size:12px; text-align:center; padding:20px; }
    .rts-chart-card .rts-empty { min-height:120px; }
    .rts-map-placeholder { position:absolute; display:flex; flex-direction:column; text-align:center; align-items:center; justify-content:center; gap:7px; color:#7185a1; font-size:12px; inset:0; padding:20px; pointer-events:none; }
    .rts-map-wrap { position:relative; }
    .rts-map-placeholder[hidden] { display:none!important; }
    .rts-unit-marker { display:flex; align-items:center; justify-content:center; background:#2865ab; color:white; font-weight:900; width:31px; height:31px; border:3px solid white; border-radius:50%; box-shadow:0 2px 12px rgba(10,44,83,.4); }
    .rts-page .leaflet-container { z-index:0; font-family:inherit; }
    .rts-page .leaflet-popup-content { font-size:12px; }
    @media(max-width:1450px) { .rts-filter-grid { grid-template-columns:1fr 1fr 1.6fr 100px auto; } .rts-actions { grid-column:span 5; } .rts-actions .rts-button { flex:1; } }
    @media(max-width:1199px) { .rts-main { grid-template-columns:1fr; } .rts-top-area { grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr); } .rts-chart-stack { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:767px) {
        .rts-page { padding:13px 11px 24px; }
        .rts-title { font-size:18px; }
        .rts-filter-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
        .rts-parameter-field { grid-column:span 2; }
        .rts-actions { grid-column:span 2; }
        .rts-top-area { grid-template-columns:1fr; }
        .rts-gauges { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); }
        .rts-gauge { align-items:flex-start; flex-direction:column; }
        .rts-gauge-value { font-size:25px; }
        .rts-chart-stack { display:flex; flex-direction:column; }
        .rts-map-canvas { height:315px; }
    }
    @media(max-width:400px) { .rts-gauges { grid-template-columns:1fr; } .rts-gauge { align-items:center; flex-direction:row; } }
</style>

<div class="page-content rts-page">
    <div class="rts-heading">
        <div>
            <h1 class="rts-title">Realtime Machine Health Series</h1>
        </div>
        <div class="rts-status"><span id="rtsStatusDot" class="rts-dot"></span><span id="rtsStatusText">Menunggu data</span></div>
    </div>

    <div class="rts-surface rts-toolbar">
        <div class="rts-filter-grid">
            <div class="rts-field">
                <label for="rtsModel">Model</label>
                <select id="rtsModel" class="rts-input">
                    @forelse($models as $model)
                        <option value="{{ $model }}" {{ $selectedModel === $model ? 'selected' : '' }}>{{ $model }}</option>
                    @empty
                        <option value="">Tidak ada model</option>
                    @endforelse
                </select>
            </div>
            <div class="rts-field">
                <label for="rtsEquipment">Equipment</label>
                <select id="rtsEquipment" class="rts-input">
                    @forelse($equipment as $unit)
                        <option value="{{ $unit }}" {{ $selectedEquipment === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                    @empty
                        <option value="">Tidak ada equipment</option>
                    @endforelse
                </select>
            </div>
            <div class="rts-field rts-parameter-field">
                <label for="rtsParameterToggle">Parameter (maksimal 4)</label>
                <button class="rts-button rts-input rts-choose" type="button" id="rtsParameterToggle" aria-expanded="false">
                    <span id="rtsParameterSummary">Pilih parameter</span><span>⌄</span>
                </button>
                <div id="rtsParameterMenu" class="rts-parameter-menu" hidden>
                    <input type="search" id="rtsParameterSearch" class="rts-input rts-parameter-search" placeholder="Cari nama parameter atau kode VHM...">
                    <div class="rts-parameter-list" id="rtsParameterList">
                        @foreach($parameterOptions as $code => $label)
                            <label class="rts-parameter-option" data-search="{{ strtolower($code . ' ' . $label) }}">
                                <input type="checkbox" class="rts-param-check" value="{{ $code }}" {{ in_array($code, $defaultParameters, true) ? 'checked' : '' }}>
                                <span>{{ $label }}</span><span class="rts-parameter-code">{{ $code }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="rts-menu-note">Maksimal 4 sensor. Grafik dimulai sejak Apply, tidak membaca histori SQL.</div>
                </div>
            </div>
            <div class="rts-field">
                <label for="rtsPoints">Buffer grafik</label>
                <select id="rtsPoints" class="rts-input">
                    <option value="30">30</option><option value="60" selected>60</option>
                    <option value="120">120</option><option value="180">180</option>
                </select>
            </div>
            <div class="rts-field">
                <label for="rtsInterval">Refresh</label>
                <select id="rtsInterval" class="rts-input">
                    <option value="5" selected>5 s</option><option value="10">10 s</option><option value="15">15 s</option><option value="30">30 s</option>
                </select>
            </div>
            <div class="rts-actions">
                <button type="button" class="rts-button rts-button-primary" id="rtsApply"><i class="fi fi-rr-check"></i> Apply</button>
                <button type="button" class="rts-button" id="rtsStop"><i class="fi fi-rr-stop"></i> Stop</button>
            </div>
        </div>
        <div class="rts-meta">
            <span><strong>Last report:</strong> <span id="rtsLastReport">–</span></span>
            <span class="rts-inline-dot"></span>
            <span><strong>Chart points (browser):</strong> <span id="rtsRecordCount">0</span></span>
            <span class="rts-inline-dot"></span>
            <span><strong>Last poll:</strong> <span id="rtsUpdated">–</span></span>
        </div>
    </div>

    <div id="rtsError" class="rts-alert" hidden></div>

    <div class="rts-main">
        <div class="rts-column rts-surface">
            <div class="rts-section-title"><h2><i class="fi fi-rr-chart-line-up me-1"></i> Realtime Sensor Trends</h2><small id="rtsChartCount">0 parameters</small></div>
            <div id="rtsChartStack" class="rts-chart-stack">
                <div class="rts-empty">Pilih equipment dan parameter untuk menampilkan grafik.</div>
            </div>
        </div>

        <div class="rts-column">
            <div class="rts-top-area">
                <div class="rts-surface">
                    <div class="rts-section-title"><h2>Digital Readings</h2><small>Current DB snapshot</small></div>
                    <div class="rts-gauges" id="rtsGauges"><div class="rts-empty">Belum ada data sensor.</div></div>
                </div>
                <div class="rts-surface">
                    <div class="rts-info-head"><h2><i class="fi fi-rr-truck-side me-1"></i> Equipment Information</h2><span class="rts-chip" id="rtsUnitBadge">–</span></div>
                    <div class="rts-detail">
                        <div class="rts-detail-row"><span>Equipment Group</span><strong id="rtsInfoModel">–</strong></div>
                        <div class="rts-detail-row"><span>Equipment</span><strong id="rtsInfoEquipment">–</strong></div>
                        <div class="rts-detail-row"><span>Operator</span><strong id="rtsInfoOperator">–</strong></div>
                        <div class="rts-detail-row"><span>Location</span><strong class="rts-location" id="rtsInfoLocation">–</strong></div>
                        <div class="rts-detail-row"><span>Group / Status / Activity</span><strong id="rtsInfoActivity">–</strong></div>
                        <div class="rts-detail-row"><span>Assignment (From → To)</span><strong id="rtsInfoAssignment">–</strong></div>
                        <div class="rts-detail-row"><span>Shift / Shift Date</span><strong id="rtsInfoShift">–</strong></div>
                        <div class="rts-detail-row"><span>Last Connected *</span><strong id="rtsInfoLast">–</strong></div>
                        <div class="rts-detail-row"><span>VHM Timestamp</span><strong id="rtsInfoVhm">–</strong></div>
                        <div class="rts-detail-row"><span>GPS Timestamp</span><strong id="rtsInfoGpsTime">–</strong></div>
                        <div class="rts-detail-row"><span>Longitude / Latitude</span><strong id="rtsInfoCoordinates">–</strong></div>
                    </div>
                </div>
            </div>

            <div class="rts-surface rts-map-section">
                <div class="rts-section-title">
                    <div><h2><i class="fi fi-rr-marker me-1"></i> Live Equipment Position</h2></div>
                    <div class="rts-map-actions"><button id="rtsMapCenter" type="button" class="rts-button rts-map-button">⌖ Center</button></div>
                </div>
                <div class="rts-map-wrap">
                    <div class="rts-map-canvas" id="rtsMap"></div>
                    <div class="rts-map-placeholder" id="rtsMapPlaceholder"><span>⌖</span><span>Menunggu koordinat GPS...</span></div>
                </div>
                <div class="rts-map-foot">
                    <span id="rtsMapStatus">Belum ada posisi GPS.</span>
                    <a id="rtsMapsLink" href="#" target="_blank" rel="noopener noreferrer" hidden>Buka di Google Maps ↗</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
<script>
(() => {
    'use strict';
    const endpoints = {
        equipment: @json(route('realtime-series.equipment')),
        data: @json(route('realtime-series.data')),
    };
    const labels = @json($parameterOptions);
    const $ = id => document.getElementById(id);
    const model = $('rtsModel'), equipment = $('rtsEquipment');
    const checks = () => [...document.querySelectorAll('.rts-param-check')];
    const selected = () => checks().filter(e => e.checked).map(e => e.value);
    const state = { timer: null, running: false, loading: false, requestToken: 0, abort: null,
        map: null, marker: null, latlon: null, parameters: [], points: 60,
        history: [], lastVhmTime: null };
    const emptyValue = v => v === undefined || v === null || v === '' ? '–' : String(v);
    const number = v => {
        if (v === undefined || v === null || !Number.isFinite(Number(v))) return '–';
        const n = Number(v), decimals = n !== 0 && Math.abs(n) < 1 ? 4 : 2;
        return n.toLocaleString('en-US', { maximumFractionDigits: decimals, minimumFractionDigits: 0 });
    };
    const shortTime = v => v ? String(v).slice(11, 19) : '–';
    const timeString = v => v ? String(v).replace('T', ' ') : '–';
    const setText = (id, content) => { $(id).textContent = emptyValue(content); };
    const showError = message => { $('rtsError').textContent = message; $('rtsError').hidden = false; };
    const hideError = () => { $('rtsError').hidden = true; };
    function status(message, style = '') {
        setText('rtsStatusText', message);
        $('rtsStatusDot').className = 'rts-dot' + (style ? ' is-' + style : '');
    }
    function summary() {
        const p = selected();
        $('rtsParameterSummary').textContent = p.length
            ? p.map(x => labels[x] || x).join(', ')
            : 'Pilih parameter';
    }
    function menuToggle(open) {
        $('rtsParameterMenu').hidden = !open;
        $('rtsParameterToggle').setAttribute('aria-expanded', String(open));
    }
    $('rtsParameterToggle').addEventListener('click', e => {
        e.stopPropagation(); menuToggle($('rtsParameterMenu').hidden);
    });
    document.addEventListener('click', e => {
        if (!e.target.closest('.rts-parameter-field')) menuToggle(false);
    });
    checks().forEach(input => input.addEventListener('change', () => {
        if (selected().length > 4) {
            input.checked = false;
            showError('Maksimal 4 parameter dalam satu tampilan.');
        } else { hideError(); }
        summary();
    }));
    $('rtsParameterSearch').addEventListener('input', e => {
        const q = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.rts-parameter-option').forEach(label => {
            label.hidden = !label.dataset.search.includes(q);
        });
    });
    summary();

    async function fetchJson(url, signal) {
        const response = await fetch(url, { method: 'GET', credentials: 'same-origin', headers: { Accept: 'application/json' }, signal });
        const result = await response.json().catch(() => null);
        if (!response.ok) throw new Error(result?.message || `HTTP ${response.status}`);
        return result;
    }
    function stop(silent = false) {
        state.running = false;
        if (state.timer) clearInterval(state.timer);
        state.timer = null;
        if (state.abort) state.abort.abort();
        state.abort = null;
        state.loading = false;
        state.requestToken += 1;
        $('rtsApply').disabled = false;
        if (!silent) status('Paused · buffer grafik tersimpan');
    }
    function clearDashboard() {
        state.history = [];
        state.lastVhmTime = null;
        $('rtsChartStack').innerHTML = '<div class="rts-empty">Grafik belum memiliki pembacaan VHM baru.</div>';
        $('rtsGauges').innerHTML = '<div class="rts-empty">Tidak ada bacaan sensor.</div>';
        setText('rtsChartCount', '0 parameters');
        ['rtsLastReport','rtsUpdated','rtsInfoModel','rtsInfoEquipment','rtsInfoOperator','rtsInfoLocation',
         'rtsInfoActivity','rtsInfoAssignment','rtsInfoShift','rtsInfoLast','rtsInfoVhm','rtsInfoGpsTime','rtsInfoCoordinates',
         'rtsUnitBadge'].forEach(id => setText(id, '–'));
        setText('rtsRecordCount', '0');
        setText('rtsMapStatus', 'Belum ada posisi GPS.');
        $('rtsMapsLink').hidden = true;
        if (state.marker && state.map) { state.map.removeLayer(state.marker); state.marker = null; }
        state.latlon = null;
        $('rtsMapPlaceholder').hidden = false;
    }
    async function updateEquipment() {
        stop(true);
        equipment.disabled = true;
        equipment.innerHTML = '<option value="">Memuat...</option>';
        clearDashboard();
        const requestId = ++state.requestToken;
        try {
            if (!model.value) { equipment.innerHTML = '<option value="">Pilih model</option>'; return; }
            const result = await fetchJson(endpoints.equipment + '?' + new URLSearchParams({ model: model.value }));
            if (requestId !== state.requestToken) return;
            equipment.replaceChildren();
            for (const id of result.equipment || []) {
                const opt = new Option(id, id);
                equipment.add(opt);
            }
            if (!equipment.options.length) equipment.add(new Option('Tidak ada equipment', ''));
            hideError();
        } catch (err) {
            if (requestId !== state.requestToken) return;
            equipment.innerHTML = '<option value="">Gagal memuat</option>';
            showError('Equipment tidak dapat dimuat: ' + err.message);
        } finally {
            if (requestId === state.requestToken) equipment.disabled = false;
        }
    }
    model.addEventListener('change', updateEquipment);
    equipment.addEventListener('change', () => { stop(true); clearDashboard(); status('Filter berubah, klik Apply'); });
    $('rtsStop').addEventListener('click', () => stop());
    $('rtsApply').addEventListener('click', apply);
    $('rtsInterval').addEventListener('change', () => { if (state.running) startTimer(); });
    $('rtsMapCenter').addEventListener('click', () => {
        if (state.map && state.latlon) state.map.setView(state.latlon, Math.max(15, state.map.getZoom()));
    });

    function startTimer() {
        if (state.timer) clearInterval(state.timer);
        const interval = Math.max(5, Number($('rtsInterval').value) || 5);
        state.timer = setInterval(() => { if (!document.hidden) poll(); }, interval * 1000);
    }

    function apply() {
        if (!model.value || !equipment.value) { showError('Pilih model dan equipment terlebih dahulu.'); return; }
        if (selected().length === 0) { showError('Pilih minimal 1 parameter.'); return; }
        stop(true);
        hideError(); menuToggle(false);
        state.parameters = selected();
        state.points = Number($('rtsPoints').value) || 60;
        state.history = [];
        state.lastVhmTime = null;
        setText('rtsRecordCount', '0');
        state.running = true;
        status('Mengambil data...');
        poll();
        startTimer();
    }

    async function poll() {
        if (!state.running || state.loading) return;
        state.loading = true;
        $('rtsApply').disabled = true;
        const token = ++state.requestToken;
        const controller = new AbortController();
        state.abort = controller;
        const params = new URLSearchParams({ model: model.value, equipment: equipment.value });
        state.parameters.forEach(p => params.append('parameters[]', p));
        try {
            const data = await fetchJson(endpoints.data + '?' + params.toString(), controller.signal);
            if (!state.running || token !== state.requestToken) return;
            render(data);
            hideError();
        } catch (err) {
            if (err.name === 'AbortError' || token !== state.requestToken) return;
            showError('Gagal membaca FLT_VEHICLE: ' + err.message);
            status('Koneksi data terganggu', 'old');
        } finally {
            if (token === state.requestToken) {
                state.loading = false;
                state.abort = null;
                $('rtsApply').disabled = false;
            }
        }
    }

    function render(data) {
        setText('rtsUpdated', new Date().toLocaleTimeString('id-ID', { hour12: false }));
        setText('rtsLastReport', timeString(data.latest?.report_time));
        setText('rtsInfoModel', data.model);
        setText('rtsInfoEquipment', data.equipment);
        setText('rtsUnitBadge', data.equipment);

        if (!data.latest) {
            clearDashboard();
            setText('rtsInfoModel', data.model);
            setText('rtsInfoEquipment', data.equipment);
            setText('rtsUnitBadge', data.equipment);
            status('Unit tidak ditemukan / model berubah', 'old');
            return;
        }
        const latest = data.latest;
        const age = latest.data_age_seconds;
        const vhmAge = latest.vhm_age_seconds;
        if (!latest.active) status('Unit tidak aktif', 'old');
        else if (!latest.vhm_valid) status('Tidak ada timestamp VHM valid', 'old');
        else if (typeof age === 'number' && age < -120) status('Periksa waktu SQL Server', 'old');
        else if (typeof age === 'number' && age <= 120 && age >= -120 &&
                 typeof vhmAge === 'number' && vhmAge <= 120 && vhmAge >= -120)
            status('Live', 'live');
        else status('Pembacaan/laporan lama', 'old');

        const sample = data.sample;
        if (sample && sample.time && (!state.lastVhmTime || sample.time > state.lastVhmTime)) {
            state.history.push(sample);
            state.lastVhmTime = sample.time;
            if (state.history.length > state.points) {
                state.history.splice(0, state.history.length - state.points);
            }
        }
        setText('rtsRecordCount', String(state.history.length));
        setText('rtsInfoOperator', [latest.operator, latest.nrp ? '(' + latest.nrp + ')' : ''].filter(Boolean).join(' ') || '–');
        setText('rtsInfoLocation', latest.location);
        setText('rtsInfoActivity', [latest.vsa_group, latest.vsa_status, latest.vsa_activity].map(emptyValue).join(' / '));
        setText('rtsInfoAssignment', latest.assignment || '–');
        setText('rtsInfoShift', [Number(latest.shift_no) === 6 ? 'Siang (6)' : Number(latest.shift_no) === 7 ? 'Malam (7)' : emptyValue(latest.shift_no), latest.shift_date].filter(Boolean).join(' / '));
        setText('rtsInfoLast', timeString(latest.report_time));
        setText('rtsInfoVhm', timeString(latest.vhm_time));
        renderCharts(state.history, data.sensors || {});
        renderGauges(data.sensors || {});
        renderGps(data.gps);
    }

    function safeNumericText(value) {
        const n = Number(value);
        if (!Number.isFinite(n)) return '0';
        if (Math.abs(n) >= 10000) return Math.round(n).toLocaleString('en-US');
        if (n !== 0 && Math.abs(n) < 1) return String(Number(n.toFixed(4)));
        return String(Number(n.toFixed(2)));
    }
    function chartMarkup(rows, key) {
        const W = 680, H = 160, left = 52, right = 14, top = 12, bottom = 26;
        const pw = W - left - right, ph = H - top - bottom;
        const points = rows.map((s, i) => ({ i, y: s?.values?.[key] === null || s?.values?.[key] === undefined ? null : Number(s.values[key]), t: shortTime(s.time) }));
        const valid = points.filter(p => Number.isFinite(p.y));
        if (!valid.length) return '<div class="rts-empty">Parameter ini belum memiliki data numerik.</div>';
        let min = Math.min(...valid.map(p => p.y)), max = Math.max(...valid.map(p => p.y));
        if (min === max) { if (min === 0) { max = 1; } else { const padding = Math.max(Math.abs(min) * 0.07, .01); min -= padding; max += padding; } }
        else { const padding = (max - min) * .12; min -= padding; max += padding; }
        const cx = i => left + (rows.length <= 1 ? pw / 2 : i * pw / (rows.length - 1));
        const cy = v => top + (max - v) * ph / (max - min);
        let svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${W} ${H}" role="img" aria-label="Trend ${key}">`;
        for (let i = 0; i <= 4; i++) {
            const y = top + i * ph / 4;
            svg += `<line x1="${left}" y1="${y}" x2="${W - right}" y2="${y}" stroke="#e7edf5" stroke-width="1" />`;
            svg += `<text x="${left - 8}" y="${y + 4}" text-anchor="end" font-size="10" fill="#96a5b7">${safeNumericText(max - i * (max - min) / 4)}</text>`;
        }
        let path = '', active = false;
        for (const point of points) {
            if (!Number.isFinite(point.y)) { active = false; continue; }
            const x = cx(point.i).toFixed(2), y = cy(point.y).toFixed(2);
            path += `${active ? ' L' : ' M'}${x} ${y}`;
            active = true;
        }
        svg += `<path d="${path}" fill="none" stroke="#30a2d1" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke" />`;
        points.forEach(point => {
            if (!Number.isFinite(point.y)) return;
            svg += `<circle cx="${cx(point.i).toFixed(2)}" cy="${cy(point.y).toFixed(2)}" r="5" fill="#30a2d1" fill-opacity="0" stroke="none"><title>${point.t}: ${safeNumericText(point.y)}</title></circle>`;
        });
        const last = valid[valid.length - 1];
        svg += `<circle cx="${cx(last.i).toFixed(2)}" cy="${cy(last.y).toFixed(2)}" r="3.5" fill="#30a2d1" stroke="white" stroke-width="1.4" />`;
        const mid = rows[Math.floor((rows.length - 1) / 2)];
        for (const item of [ {x:left, time:rows[0]?.time, anchor:'start'}, {x:left+pw/2,time:mid?.time,anchor:'middle'}, {x:W-right,time:rows[rows.length-1]?.time,anchor:'end'} ]) {
            svg += `<text x="${item.x}" y="${H-4}" text-anchor="${item.anchor}" font-size="10" fill="#92a2b6">${shortTime(item.time)}</text>`;
        }
        return svg + '</svg>';
    }
    function renderCharts(rows, sensors) {
        const host = $('rtsChartStack');
        host.replaceChildren();
        const keys = state.parameters;
        setText('rtsChartCount', keys.length + ' parameters');
        for (const key of keys) {
            const info = sensors[key] || {};
            const card = document.createElement('div'); card.className = 'rts-chart-card';
            const header = document.createElement('div'); header.className = 'rts-chart-header';
            const titleWrap = document.createElement('div');
            const title = document.createElement('div'); title.className = 'rts-chart-name'; title.textContent = labels[key] || key;
            const code = document.createElement('div'); code.className = 'rts-chart-code'; code.textContent = key;
            titleWrap.append(title, code);
            const reading = document.createElement('div'); reading.className = 'rts-reading'; reading.textContent = number(info.value);
            header.append(titleWrap, reading);
            const graph = document.createElement('div'); graph.innerHTML = chartMarkup(rows, key);
            const foot = document.createElement('div'); foot.className = 'rts-chart-foot';
            const time = document.createElement('span'); time.textContent = 'Last sensor: ' + timeString(info.measured_at);
            const indicator = document.createElement('span');
            indicator.className = info.all_zero ? 'rts-chart-warning' : '';
            indicator.textContent = !info.valid_time ? 'Timestamp VHM tidak valid' : (info.all_zero ? 'Bacaan saat ini = 0 · ' : '') + rows.length + ' titik';
            foot.append(time, indicator);
            card.append(header, graph, foot); host.append(card);
        }
    }
    function renderGauges(sensors) {
        const host = $('rtsGauges'); host.replaceChildren();
        state.parameters.forEach(key => {
            const sensor = sensors[key] || {};
            const wrap = document.createElement('div');
            wrap.className = 'rts-gauge' + (sensor.value === null || sensor.value === undefined ? ' gauge-empty' : '');
            const description = document.createElement('div');
            const label = document.createElement('span'); label.className = 'rts-gauge-name'; label.textContent = labels[key] || key;
            const time = document.createElement('span'); time.className = 'rts-gauge-time'; time.textContent = sensor.valid_time ? shortTime(sensor.measured_at) : 'VHM belum tersedia';
            description.append(label,time);
            const value = document.createElement('strong'); value.className = 'rts-gauge-value'; value.textContent = number(sensor.value);
            wrap.append(description, value); host.append(wrap);
        });
    }
    function escapeHtml(raw) { return String(raw).replace(/[&<>"']/g, c => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c])); }
    function renderGps(gps) {
        if (!gps || !Number.isFinite(Number(gps.lat)) || !Number.isFinite(Number(gps.lon))) {
            setText('rtsInfoCoordinates', 'Tidak tersedia');
            setText('rtsInfoGpsTime', '–');
            setText('rtsMapStatus', 'Tidak ada koordinat GPS valid untuk equipment ini.');
            $('rtsMapsLink').hidden = true;
            if (state.marker && state.map) { state.map.removeLayer(state.marker); state.marker = null; }
            state.latlon = null;
            $('rtsMapPlaceholder').hidden = false;
            return;
        }
        const lat = Number(gps.lat), lon = Number(gps.lon);
        const latlon = [lat, lon];
        setText('rtsInfoCoordinates', lon.toFixed(6) + ' / ' + lat.toFixed(6));
        setText('rtsInfoGpsTime', timeString(gps.gps_time || gps.report_time));
        setText('rtsMapStatus', 'GPS timestamp: ' + timeString(gps.gps_time) +
            ' · Lat ' + lat.toFixed(6) + ', Lon ' + lon.toFixed(6));
        $('rtsMapsLink').href = 'https://www.google.com/maps?q=' + encodeURIComponent(lat + ',' + lon);
        $('rtsMapsLink').hidden = false;
        state.latlon = latlon;
        if (!window.L) {
            $('rtsMapPlaceholder').hidden = false;
            $('rtsMapPlaceholder').textContent = 'Connecting internet...';
            return;
        }
        $('rtsMapPlaceholder').hidden = true;
        if (!state.map) {
            state.map = L.map('rtsMap', { zoomControl:true }).setView(latlon, 15);
            const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19, attribution:'Tiles © Machine Health'
            });
            const street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19, attribution:'© OpenStreetMap contributors'
            });
            satellite.addTo(state.map);
            L.control.layers({ 'Satellite': satellite, 'Street Map': street }, null, { collapsed: true }).addTo(state.map);
            setTimeout(() => state.map.invalidateSize(), 100);
        }
        const icon = L.divIcon({ html: '<div class="rts-unit-marker">◆</div>', className:'', iconSize:[31,31], iconAnchor:[15,15] });
        if (!state.marker) state.marker = L.marker(latlon, { icon }).addTo(state.map);
        else state.marker.setLatLng(latlon);
        state.marker.bindPopup('<strong>' + escapeHtml(equipment.value) + '</strong><br>GPS: ' + escapeHtml(timeString(gps.gps_time || gps.report_time)));
        // Preserve zoom and pan unless this equipment moved significantly.
        if (!state.map.getBounds().contains(latlon)) state.map.panTo(latlon);
    }
    document.addEventListener('visibilitychange', () => { if (!document.hidden && state.running) poll(); });
    if (model.value && equipment.value && selected().length) apply();
    else status('Tidak ada unit aktif');
    window.addEventListener('beforeunload', () => stop(true));
})();
</script>

@include('layout.footer')
