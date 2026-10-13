@include('layout.head')
@include('layout.sidebar')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzXf9tD/miZyo94VqJ4Pmy9h4k=" crossorigin="" />
<style>
    .oi-page{--oi-main:#315f9f;--oi-line:#dfe6ef;--oi-muted:#74869e;--oi-ink:#263d5a;--oi-bg:#f5f7fb;background:var(--oi-bg);min-height:calc(100vh - 62px);padding:18px;color:var(--oi-ink)}
    .oi-page *{box-sizing:border-box}
    .oi-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px}
    .oi-head h1{font-size:20px;font-weight:800;letter-spacing:-.35px;margin:0 0 3px}
    .oi-head p{font-size:12px;color:var(--oi-muted);margin:0}
    .oi-outline{display:inline-flex;gap:8px;align-items:center;text-decoration:none;color:#42648c;font-weight:700;border:1px solid var(--oi-line);background:#fff;border-radius:8px;padding:9px 12px;font-size:11px}
    .oi-panel{border:1px solid var(--oi-line);background:#fff;border-radius:5px;box-shadow:0 4px 16px rgba(26,47,80,.025);min-width:0;overflow:hidden}
    .oi-filters{padding:14px;margin-bottom:13px}
    .oi-section{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;color:#48627e;margin-bottom:11px}
    .oi-form{display:flex;align-items:end;gap:11px;flex-wrap:wrap}
    .oi-field{min-width:150px;flex:0 1 180px}
    .oi-field label{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.25px;color:#72839a;font-weight:800;margin-bottom:5px}
    .oi-input{border:1px solid #d9e3ef;background:#fff;color:#2e4766;border-radius:8px;height:37px;padding:7px 10px;font-size:12px;width:100%}
    .oi-input:focus{outline:none;border-color:#78aadd;box-shadow:0 0 0 3px rgba(57,108,171,.10)}
    .oi-btn{height:37px;padding:0 12px;display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid #d8e3f0;background:#fff;border-radius:8px;font-size:11px;color:#486481;font-weight:800;cursor:pointer;white-space:nowrap}
    .oi-btn:hover{background:#f1f6fc}
    .oi-btn.active{border-color:#86b3ec;background:#eaf4ff;color:#215e9e}
    .oi-btn.primary{background:#315f9f;color:#fff;border-color:#315f9f}
    .oi-btn.primary:hover{background:#284f89}
    .oi-btn:disabled{opacity:.48;cursor:not-allowed}
    .oi-toolbar{display:flex;gap:7px;align-items:center;flex-wrap:wrap;border-top:1px solid #edf0f5;padding-top:12px;margin-top:13px}
    .oi-toolbar-note{margin-left:auto;color:#8d9caf;font-size:10px}
    .oi-metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:13px}
    .oi-metric{display:flex;align-items:center;justify-content:space-between;border:1px solid var(--oi-line);border-radius:11px;padding:13px 15px;background:#fff;gap:8px}
    .oi-metric .tag{display:block;text-transform:uppercase;color:#7b8ca0;font-size:10px;font-weight:800;margin-bottom:5px}
    .oi-metric .num{font-size:25px;font-weight:850;letter-spacing:-.6px;line-height:1.05;color:#284765}
    .oi-metric i{font-size:22px;color:#8fb0d9}
    .oi-dashboard{display:grid;grid-template-columns:minmax(340px,41%) minmax(0,59%);gap:12px;margin-bottom:12px}
    .oi-bottom{display:grid;grid-template-columns:1.12fr .88fr .88fr;gap:12px}
    .oi-panel-head{display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;padding:12px 14px;border-bottom:1px solid #e8edf4;background:#fafcff}
    .oi-panel-head strong{font-size:12px;font-weight:800;color:#2f5077}
    .oi-panel-head small{font-size:10px;color:#8594a5}
    .oi-map,.oi-table-wrap{height:460px;position:relative}
    .oi-map{width:100%;min-width:0;background:#eaf0f6;isolation:isolate;overflow:hidden;touch-action:pan-x pan-y}
    #oiMap.leaflet-container{position:relative!important;overflow:hidden!important;outline:0;background:#e8eef4!important}
    #oiMap .leaflet-pane,#oiMap .leaflet-tile-pane,#oiMap .leaflet-overlay-pane,
    #oiMap .leaflet-marker-pane,#oiMap .leaflet-shadow-pane,#oiMap .leaflet-popup-pane,
    #oiMap .leaflet-tooltip-pane,#oiMap .leaflet-tile-container,#oiMap .leaflet-layer,
    #oiMap .leaflet-pane>svg,#oiMap .leaflet-pane>canvas,
    #oiMap img.leaflet-tile,#oiMap img.leaflet-marker-icon,#oiMap img.leaflet-marker-shadow{
        position:absolute!important;left:0;top:0;margin:0;padding:0;border:0;
    }
    #oiMap .leaflet-map-pane{z-index:auto}
    #oiMap .leaflet-tile-pane{z-index:200}
    #oiMap .leaflet-overlay-pane{z-index:400}
    #oiMap .leaflet-shadow-pane{z-index:500}
    #oiMap .leaflet-marker-pane{z-index:600}
    #oiMap .leaflet-tooltip-pane{z-index:650}
    #oiMap .leaflet-popup-pane{z-index:700}
    #oiMap img.leaflet-tile{
        width:256px!important;height:256px!important;max-width:none!important;max-height:none!important;
        min-width:256px!important;min-height:256px!important;object-fit:fill!important;display:block!important;
        box-shadow:none!important;border-radius:0!important;opacity:1;
    }
    #oiMap .leaflet-tile.leaflet-tile-loaded{visibility:visible}
    #oiMap .leaflet-pane>canvas{max-width:none!important;max-height:none!important;display:block!important}
    #oiMap .leaflet-pane>svg{max-width:none!important;max-height:none!important;overflow:visible}
    #oiMap .leaflet-interactive{pointer-events:auto}
    #oiMap .leaflet-control-container .leaflet-control-attribution{font-size:9px;background:rgba(255,255,255,.85);padding:2px 5px}
    .oi-layer-check{display:inline-flex;align-items:center;gap:5px;border:1px solid #d9e3ef;background:#fff;padding:7px 9px;border-radius:8px;color:#526a85;font-size:10px;font-weight:700;cursor:pointer;min-height:35px}
    .oi-layer-check input{accent-color:#315f9f;cursor:pointer}
    #oiMap .leaflet-pane.oi-site-pane{z-index:350!important;pointer-events:auto}
    .oi-map-status{position:absolute;top:12px;left:12px;z-index:1200;max-width:min(430px,calc(100% - 24px));background:rgba(255,251,235,.97);color:#854d0e;border:1px solid #f4d69b;border-radius:8px;padding:9px 11px;box-shadow:0 7px 20px rgba(0,0,0,.10);font-size:11px;line-height:1.5}
    .oi-map-status[hidden]{display:none}
    .oi-map-placeholder{position:absolute;z-index:9;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:9px;text-align:center;background:#f8fbff;color:#90a1b7;font-size:12px;padding:20px}
    .oi-map-placeholder i{font-size:26px;color:#9fb7d1}
    .oi-map-placeholder[hidden]{display:none}
    .oi-map-legend{position:absolute;bottom:13px;left:13px;z-index:500;display:flex;align-items:center;gap:7px;background:rgba(255,255,255,.95);border:1px solid #dfe7ef;border-radius:7px;padding:7px 10px;font-size:10px;color:#61788f;box-shadow:0 6px 16px rgba(20,48,80,.1)}
    .oi-dot{width:9px;height:9px;border-radius:100%;background:#d14b54;display:inline-block}
    .oi-table-wrap{overflow:auto}
    .oi-table{border-collapse:collapse;width:100%;font-size:10px;white-space:nowrap}
    .oi-table thead{position:sticky;top:0;z-index:2;background:#f1f5fb}
    .oi-table th{font-weight:800;color:#4b617d;text-align:left;padding:11px 9px;border-bottom:1px solid #dce5ee;cursor:default}
    .oi-table td{padding:9px 9px;border-bottom:1px solid #edf1f6;color:#4e617b}
    .oi-table tbody tr{cursor:pointer}
    .oi-table tbody tr:hover{background:#eef6ff}
    .oi-table tbody tr.selected{background:#e9f3fe}
    .oi-table .fault{color:#315f9f;font-weight:800;max-width:180px;overflow:hidden;text-overflow:ellipsis}
    .oi-table .unit{font-weight:800;color:#2a4666}
    .oi-table .empty{padding:28px;color:#8fa0b3;text-align:center;white-space:normal}
    .oi-pager{padding:9px 12px;display:flex;justify-content:space-between;align-items:center;gap:8px;border-top:1px solid #e6edf5;font-size:11px;color:#7185a0}
    .oi-mini{height:31px;padding:0 10px;font-size:10px}
    .oi-bottom .oi-panel{min-height:344px}
    .oi-chart-wrap{height:294px;padding:12px 13px;display:flex;align-items:stretch}
    .oi-chart-svg{height:100%;width:100%;display:block;overflow:visible}
    .oi-rank-wrap{max-height:292px;overflow:auto}
    .oi-rank{border-collapse:collapse;width:100%;font-size:10px}
    .oi-rank thead{position:sticky;top:0;background:#f1f5fb}
    .oi-rank th{padding:10px 9px;border-bottom:1px solid #dee6ef;font-size:10px;text-align:left;color:#61788e}
    .oi-rank td{padding:9px;border-bottom:1px solid #edf1f6;color:#546983}
    .oi-rank tbody tr{cursor:pointer}
    .oi-rank tbody tr:hover{background:#eef6ff}
    .oi-rank .right{text-align:right;font-weight:800;color:#2c5c94}
    .oi-badge{border-radius:100px;padding:4px 8px;color:#3064a2;background:#eaf3ff;font-size:9px;font-weight:800}
    .oi-alert{border:1px solid #f0c9a1;background:#fffbf4;color:#8b5d21;border-radius:10px;padding:12px 14px;font-size:12px;line-height:1.65;margin-bottom:12px}
    .oi-alert[hidden]{display:none}
    .oi-loading{display:none;align-items:center;gap:8px;color:#315f9f;font-size:11px;font-weight:700}
    .oi-loading.visible{display:inline-flex}
    .oi-notes{font-size:10px;color:#8b9aac;margin:12px 0 0;line-height:1.6}
    .oi-map-expanded{position:fixed!important;inset:15px!important;z-index:9999!important;border-radius:10px;box-shadow:0 20px 80px rgba(10,30,60,.35);background:white}
    .oi-map-expanded .oi-map{height:calc(100vh - 90px)}
    .oi-highlight{background:#e8f4ff!important}
    @media(max-width:1250px){.oi-dashboard{grid-template-columns:minmax(0,1fr) minmax(0,1fr)}.oi-bottom{grid-template-columns:1fr 1fr}.oi-bottom .oi-panel:first-child{grid-column:span 2}}
    @media(max-width:850px){.oi-page{padding:12px}.oi-dashboard,.oi-bottom{grid-template-columns:1fr}.oi-bottom .oi-panel:first-child{grid-column:auto}.oi-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.oi-map,.oi-table-wrap{height:360px}.oi-toolbar-note{margin-left:0;width:100%}}
    @media(max-width:500px){.oi-metrics{grid-template-columns:1fr 1fr}.oi-metric{padding:10px}.oi-metric .num{font-size:21px}.oi-map,.oi-table-wrap{height:320px}.oi-head h1{font-size:17px}.oi-field{flex:1 1 130px}.oi-form>.oi-btn{flex:1}}
</style>

<div class="page-content oi-page" id="oiPage">
    <header class="oi-head">
        <div>
            <h1>Operator Induced Distribution On Map</h1>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a class="oi-outline" href="{{ url('/realtime/series') }}"><i class="fi fi-rr-time-fast"></i> Realtime Series</a>
            <a class="oi-outline" href="{{ url('/periodic/series') }}"><i class="fi fi-rr-chart-histogram"></i> Periodic Series</a>
        </div>
    </header>

    <section class="oi-panel oi-filters">
        <div class="oi-section">Navigation &amp; Date Range</div>
        <form id="oiForm" class="oi-form">
            <div class="oi-field"><label for="oiStart">Start Date</label><input id="oiStart" class="oi-input" type="date" name="start_date" value="{{ $defaultStart }}" required></div>
            <div class="oi-field"><label for="oiStop">Stop Date</label><input id="oiStop" class="oi-input" type="date" name="stop_date" value="{{ $defaultStop }}" required></div>
            <div class="oi-field"><label for="oiBrand">Type Unit</label><select id="oiBrand" class="oi-input" name="brand"><option value="all">All (CAT + Komatsu)</option><option value="Caterpillar">Caterpillar</option><option value="Komatsu">Komatsu</option></select></div>
            <button class="oi-btn primary" type="submit"><i class="fi fi-rr-play"></i> View Data</button>
            <span class="oi-loading" id="oiLoading"><i class="fi fi-rr-refresh"></i> Loading...</span>
        </form>
        <div class="oi-toolbar">
            <button id="oiExtend" class="oi-btn" type="button"><i class="fi fi-rr-expand"></i> Extend Map</button>
            <button id="oiZoomIn" class="oi-btn" type="button"><i class="fi fi-rr-zoom-in"></i> Zoom In</button>
            <button id="oiZoomOut" class="oi-btn" type="button"><i class="fi fi-rr-zoom-out"></i> Zoom Out</button>
            <button id="oiDrag" class="oi-btn active" type="button"><i class="fi fi-rr-hand"></i> Drag</button>
            <button id="oiPlot" class="oi-btn active" type="button"><i class="fi fi-rr-map-marker"></i> Plotting</button>
            <button id="oiLayer" class="oi-btn" type="button"><i class="fi fi-rr-layers"></i> Layer: Site Map</button>
            <label class="oi-layer-check"><input type="checkbox" id="oiRoads" checked> Roads</label>
            <label class="oi-layer-check"><input type="checkbox" id="oiAreas" checked> Areas</label>
            <label class="oi-layer-check"><input type="checkbox" id="oiFronts" checked> Fronts / Disposal</label>
            <label class="oi-layer-check"><input type="checkbox" id="oiCallpoints"> Callpoints</label>
            <button id="oiCoord" class="oi-btn" type="button"><i class="fi fi-rr-globe"></i> Coord. System</button>
            <button id="oiReset" class="oi-btn" type="button"><i class="fi fi-rr-refresh"></i> Reset All</button>
            <span class="oi-toolbar-note" id="oiRangeInfo">Select dates to view operator induced records</span>
        </div>
    </section>

    <div class="oi-alert" id="oiAlert" @if($configured) hidden @endif>{{ $configurationMessage }}</div>

    @if (!empty($coverageMessage))
        <div class="oi-alert" style="background:#eff6ff;border-color:#bfdbfe;color:#1d4f8a">{{ $coverageMessage }}</div>
    @endif

    <div class="oi-metrics">
        <div class="oi-metric"><div><span class="tag">Total Induced</span><div class="num" id="oiTotal">—</div></div><i class="fi fi-rr-triangle-warning"></i></div>
        <div class="oi-metric"><div><span class="tag">Operators in Ranking</span><div class="num" id="oiOps">—</div></div><i class="fi fi-rr-users"></i></div>
        <div class="oi-metric"><div><span class="tag">Equipment in Ranking</span><div class="num" id="oiEquipment">—</div></div><i class="fi fi-rr-truck-side"></i></div>
        <div class="oi-metric"><div><span class="tag">Map Points</span><div class="num" id="oiPositions">—</div></div><i class="fi fi-rr-marker"></i></div>
    </div>

    <div class="oi-dashboard">
        <section class="oi-panel">
            <div class="oi-panel-head"><strong>Table View (Resume)</strong><small id="oiTableCount">0 records</small></div>
            <div class="oi-table-wrap">
                <table class="oi-table">
                    <thead><tr><th>No</th><th>Merek</th><th>Model</th><th>Equipment / SN</th><th>Fault Code</th><th>Date Time</th><th>Speed</th><th>Status ID</th><th>NIK</th><th>Fullname</th><th>Count*</th></tr></thead>
                    <tbody id="oiResumeRows"><tr><td class="empty" colspan="11">Klik View Data untuk memuat laporan.</td></tr></tbody>
                </table>
            </div>
            <div class="oi-pager"><span id="oiPagination">Page —</span><div style="display:flex;gap:6px"><button class="oi-btn oi-mini" type="button" id="oiPrev" disabled>Previous</button><button class="oi-btn oi-mini" type="button" id="oiNext" disabled>Next</button></div></div>
        </section>
        <section class="oi-panel" id="oiMapPanel">
            <div class="oi-panel-head"><strong>Site Map View</strong><small id="oiMapHint">Site · GPS WGS84</small></div>
            <div class="oi-map" id="oiMap"><div class="oi-map-placeholder" id="oiMapPlaceholder"><i class="fi fi-rr-map"></i><strong>Memuat geometri peta tambang</strong><span>Sumber: FOCUS.dbo.MAP_OBJECT.</span></div><div id="oiTileStatus" class="oi-map-status" role="status" hidden></div><div class="oi-map-legend"><i class="oi-dot"></i> CAT <i class="oi-dot" style="background:#f59e0b"></i> Komatsu <span id="oiMapCount">0 points</span> <span id="oiSiteCount" style="border-left:1px solid #d8e1ec;padding-left:7px">0 site features</span></div></div>
        </section>
    </div>

    <div class="oi-bottom">
        <section class="oi-panel"><div class="oi-panel-head"><strong>Trend Graphic Chart Per Day</strong><small>Induced events / day</small></div><div class="oi-chart-wrap" id="oiChartWrap"><svg class="oi-chart-svg" id="oiTrendChart" role="img" aria-label="Operator induced per day"></svg></div></section>
        <section class="oi-panel"><div class="oi-panel-head"><strong>Operator Ranking</strong><small>Top 50 operator</small></div><div class="oi-rank-wrap"><table class="oi-rank"><thead><tr><th>No</th><th>NIK</th><th>Fullname</th><th style="text-align:right">Total Induced</th></tr></thead><tbody id="oiOperatorRows"><tr><td colspan="4" class="oi-table empty">Belum ada data.</td></tr></tbody></table></div></section>
        <section class="oi-panel"><div class="oi-panel-head"><strong>Equipment Ranking</strong><small>Top 50 equipment</small></div><div class="oi-rank-wrap"><table class="oi-rank"><thead><tr><th>No</th><th>Merek</th><th>Equipment / SN</th><th>Model</th><th style="text-align:right">Total Induced</th></tr></thead><tbody id="oiEquipmentRows"><tr><td colspan="5" class="oi-table empty">Belum ada data.</td></tr></tbody></table></div></section>
    </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    'use strict';
    const API = @json(route('operator-induced.data'));
    const MAP_API = @json(route('operator-induced.map-layers'));
    const CONFIGURED = @json($configured);
    const el = id => document.getElementById(id);
    const state = { page:1, hasMore:false, latest:null, map:null, layer:null, markerLayer:null, markerRenderer:null, siteLayer:null, siteRenderer:null, siteFeatures:[], siteBounds:null, mapPoints:[], plotting:true, baseMode:'site', focus:null, lastQuery:null, controller:null, resizeObserver:null, resizeTimer:null };
    const number = n => Number(n ?? 0).toLocaleString('en-US');
    function showAlert(message) { el('oiAlert').textContent = message; el('oiAlert').hidden = !message; }
    function loading(on) { el('oiLoading').classList.toggle('visible',on); el('oiForm').querySelector('button[type=submit]').disabled = on; }
    function td(tr, value, className) { const cell = document.createElement('td'); cell.textContent = value === null || value === undefined || value === '' ? '—' : String(value); if(className) cell.className = className; tr.appendChild(cell); return cell; }
    function emptyRow(body, colspan, text) { body.replaceChildren(); const tr=document.createElement('tr'); const cell=document.createElement('td'); cell.colSpan=colspan;cell.className='empty';cell.textContent=text;tr.appendChild(cell);body.appendChild(tr); }
    function dateLabel(s) { if(!s) return '—'; const parts=String(s).split(' '); return parts[0] || s; }
    function setMetrics(summary) { el('oiTotal').textContent=number(summary.total);el('oiOps').textContent=number(summary.operator_count_shown);el('oiEquipment').textContent=number(summary.equipment_count_shown);el('oiPositions').textContent=number(summary.map_points_shown); }

    function setMapStatus(message='') {
        const status=el('oiTileStatus');
        status.textContent=message;
        status.hidden=!message;
    }
    function refreshMapSize() {
        if(!state.map) return;
        const root=el('oiMap');
        if(root.clientWidth<2 || root.clientHeight<2) return;
        state.map.invalidateSize({pan:false,debounceMoveend:true});
    }
    function createMap() {
        const placeholder=el('oiMapPlaceholder');
        if (typeof L === 'undefined') {
            placeholder.querySelector('strong').textContent='Leaflet belum tersedia';
            placeholder.querySelector('span').textContent='Hubungkan internet atau pasang Leaflet lokal pada public/vendor/leaflet.';
            return;
        }
        state.map=L.map('oiMap', {
            zoomControl:false,
            attributionControl:true,
            preferCanvas:true,
            zoomSnap:.5,
            worldCopyJump:true
        }).setView([-1.852,115.886],13);
        // Renderer standar Leaflet menggunakan overlayPane di atas tiles (z-index 400).
        // Pane khusus dengan pointer-events:none membuat marker tidak dapat diklik.
        state.map.createPane('oiSitePane');
        state.map.getPane('oiSitePane').classList.add('oi-site-pane');
        state.map.getPane('oiSitePane').style.zIndex='350';
        state.siteRenderer=L.canvas({pane:'oiSitePane',padding:.5});
        state.siteLayer=L.layerGroup().addTo(state.map);
        state.markerRenderer=L.canvas({padding:0.5});
        state.markerLayer=L.layerGroup().addTo(state.map);
        setBaseLayer();
        if(typeof ResizeObserver!=='undefined') {
            state.resizeObserver=new ResizeObserver(() => {
                clearTimeout(state.resizeTimer);
                state.resizeTimer=setTimeout(refreshMapSize,90);
            });
            state.resizeObserver.observe(el('oiMap'));
        }
        window.addEventListener('resize',refreshMapSize,{passive:true});
        state.map.whenReady(() => {
            requestAnimationFrame(() => requestAnimationFrame(refreshMapSize));
        });
        setTimeout(refreshMapSize,180);
        setTimeout(refreshMapSize,550);
    }
    function setBaseLayer() {
        if(!state.map)return;
        if(state.layer){state.layer.off();state.map.removeLayer(state.layer);state.layer=null;}
        const mode=state.baseMode;
        el('oiLayer').replaceChildren();
        const icon=document.createElement('i');icon.className='fi fi-rr-layers';
        el('oiLayer').append(icon,document.createTextNode(' Layer: '+(mode==='site'?'Site Map':mode==='satellite'?'Satellite':'Street')));
        if(mode==='site'){
            el('oiMap').style.background='#f1f5f9';
            setMapStatus('');
            refreshMapSize();
            return;
        }
        const satellite=mode==='satellite';
        const url=satellite
            ? 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
            : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        let failures=0;
        const layer=L.tileLayer(url,{
            attribution:satellite?'Imagery © Esri':'© OpenStreetMap contributors',
            maxZoom:19,tileSize:256,keepBuffer:2,updateWhenIdle:true
        });
        layer.on('tileerror',()=>{
            if(state.layer!==layer)return;
            failures++;
            if(failures>=3){
                state.baseMode='site';
                setBaseLayer();
                setMapStatus('Tile '+(satellite?'satelit':'peta jalan')+' gagal dimuat. Otomatis kembali ke Site Map dari FOCUS.dbo.MAP_OBJECT.');
            }
        });
        state.layer=layer.addTo(state.map);
        refreshMapSize();
    }
    function visibleSiteLayer(f){
        if(f.layer==='MAP_ROAD')return el('oiRoads').checked;
        if(['MAP_AREA','MAP_FRONT_BOUNDARY','MAP_DISPOSAL_BOUNDARY'].includes(f.layer)){
            return f.layer==='MAP_AREA'?el('oiAreas').checked:el('oiFronts').checked;
        }
        if(f.layer==='MAP_CALLPOINT')return el('oiCallpoints').checked;
        return el('oiFronts').checked;
    }
    function renderSiteMap(fit=false){
        if(!state.map||!state.siteLayer)return;
        state.siteLayer.clearLayers();
        const allCoords=[];
        let count=0;
        for(const f of state.siteFeatures){
            if(!visibleSiteLayer(f))continue;
            let layer=null;
            if(f.geometry==='polyline'&&Array.isArray(f.coordinates)){
                layer=L.polyline(f.coordinates,{renderer:state.siteRenderer,pane:'oiSitePane',color:'#596c80',weight:2.25,opacity:.88,interactive:true});
                allCoords.push(...f.coordinates);
            }else if(f.geometry==='polygon'&&Array.isArray(f.coordinates)){
                const front=f.layer==='MAP_FRONT_BOUNDARY',disposal=f.layer==='MAP_DISPOSAL_BOUNDARY';
                layer=L.polygon(f.coordinates,{
                    renderer:state.siteRenderer,pane:'oiSitePane',
                    color:front?'#dd7b26':disposal?'#7c56aa':'#15958a',
                    weight:front||disposal?1.8:1.4,
                    fillColor:front?'#f59e0b':disposal?'#9472c1':'#2dd4bf',
                    fillOpacity:(front||disposal)?0.09:0.055,interactive:true
                });
                allCoords.push(...f.coordinates);
            }else if(f.geometry==='point'){
                const front=f.layer==='MAP_FRONT',disposal=f.layer==='MAP_DISPOSAL';
                layer=L.circleMarker([f.lat,f.lon],{
                    renderer:state.siteRenderer,pane:'oiSitePane',radius:front||disposal?3.5:2.5,
                    color:front?'#b45309':disposal?'#6d28d9':'#64748b',weight:1.3,
                    fillColor:front?'#fbbf24':disposal?'#c4b5fd':'#fff',fillOpacity:.93
                });
                allCoords.push([f.lat,f.lon]);
            }
            if(!layer)continue;
            const label=document.createElement('div');
            const strong=document.createElement('strong');strong.textContent=f.name||'-';
            const sub=document.createElement('div');sub.textContent=f.layer;
            label.append(strong,sub);
            layer.bindPopup(label);
            layer.addTo(state.siteLayer);
            count++;
        }
        el('oiSiteCount').textContent=number(count)+' site features';
        if(allCoords.length)state.siteBounds=L.latLngBounds(allCoords);
        // Hanya autofit saat peta belum menampilkan titik fault.
        if(fit&&state.siteBounds?.isValid()&&!state.mapPoints.length){
            state.map.fitBounds(state.siteBounds.pad(.12),{maxZoom:15,animate:false});
        }
        refreshMapSize();
    }
    async function loadSiteMap(){
        if(!state.map)return;
        try{
            const response=await fetch(MAP_API,{headers:{'Accept':'application/json'},credentials:'same-origin'});
            const payload=await response.json();
            if(!response.ok)throw Error(payload.message||'Tidak dapat membaca layer peta.');
            state.siteFeatures=Array.isArray(payload.features)?payload.features:[];
            renderSiteMap(true);
            el('oiMapPlaceholder').hidden=true;
            if(payload.meta?.truncated){
                setMapStatus('Layer peta dibatasi '+number(payload.meta.limit)+' object. Naikkan map_object_limit bila diperlukan.');
            }else if(!state.siteFeatures.length){
                setMapStatus('Tidak ada geometri MAP_OBJECT dengan VISIBLE = 1 pada layer terpilih.');
            }
            if(state.mapPoints.length)plotMap(true);
        }catch(error){
            setMapStatus(error.message||'Gagal memuat geometri site map dari SQL Server.');
            el('oiMapPlaceholder').hidden=true;
        }
    }
    function mapPopup(p) {
        const root=document.createElement('div');root.style.fontSize='11px';root.style.lineHeight='1.8';
        const entries=[['Brand',p.brand],['Equipment / SN',p.equipment],['Fault',p.fault],['Date',p.date_time],['NIK',p.nik],['Operator',p.fullname],['Location',p.location]];
        entries.forEach(([k,v])=>{const line=document.createElement('div');const strong=document.createElement('b');strong.textContent=k+': ';line.append(strong,document.createTextNode(v||'—'));root.appendChild(line);});
        return root;
    }
    function plotMap(fit=false) {
        const valid=p=>Number.isFinite(+p.lat)&&Number.isFinite(+p.lon)
            && +p.lat>=-90 && +p.lat<=90 && +p.lon>=-180 && +p.lon<=180
            && +p.lat!==0 && +p.lon!==0;
        const points=state.mapPoints.filter(valid)
            .filter(p=>!state.focus||(state.focus.kind==='operator'?p.nik===state.focus.id:(p.brand+'|'+p.equipment)===state.focus.id));
        el('oiMapCount').textContent=number(points.length)+' points';
        if(!state.map || !state.markerLayer) return;
        state.markerLayer.clearLayers();
        if(state.plotting && state.mapPoints.length && !points.length){
            setMapStatus('Tidak ada koordinat GPS valid untuk filter ini. Periksa GPS_LAT dan GPS_LON pada record fault.');
        }
        if(!state.plotting) return;
        refreshMapSize();
        const bounds=[];
        points.forEach(p=>{
            const lat=+p.lat, lon=+p.lon;
            const point=L.circleMarker([lat,lon],{
                renderer:state.markerRenderer,
                radius:6,weight:1.5,color:'#fff',
                fillColor:p.brand==='Komatsu'?'#f59e0b':'#df4d5c',fillOpacity:.94
            });
            point.bindPopup(mapPopup(p));
            point.addTo(state.markerLayer);
            bounds.push([lat,lon]);
        });
        if(fit&&bounds.length===1){
            state.map.setView(bounds[0],15,{animate:false});
        }else if(fit&&bounds.length){
            // Pusat fit memakai mayoritas titik. Data GPS anomali tetap ditampilkan di peta,
            // tetapi tidak menyebabkan semua titik site terlihat sangat kecil.
            const sortedLat=points.map(p=>+p.lat).sort((a,b)=>a-b);
            const sortedLon=points.map(p=>+p.lon).sort((a,b)=>a-b);
            const middle=Math.floor(points.length/2);
            const midLat=sortedLat[middle],midLon=sortedLon[middle];
            const mainCluster=bounds.filter(p=>Math.abs(p[0]-midLat)<=.20&&Math.abs(p[1]-midLon)<=.20);
            const focused=mainCluster.length>=Math.ceil(bounds.length*.65)?mainCluster:bounds;
            state.map.fitBounds(L.latLngBounds(focused).pad(.12),{maxZoom:16,animate:false});
            requestAnimationFrame(refreshMapSize);
        }else if(fit){
            if(state.siteBounds?.isValid()) state.map.fitBounds(state.siteBounds.pad(.12),{maxZoom:15,animate:false});
            else state.map.setView([-1.852,115.886],13);
        }
    }
    function focusPosition(r) {
        if(!state.map||r.lat==null||r.lon==null||!Number.isFinite(+r.lat)||!Number.isFinite(+r.lon))return;
        state.focus=null;plotMap(false);
        state.map.setView([Number(r.lat),Number(r.lon)],Math.max(16,state.map.getZoom()));
        const target=L.circleMarker([Number(r.lat),Number(r.lon)],{
            renderer:state.markerRenderer,
            radius:10,color:'#315f9f',weight:3,fillColor:'#fff',fillOpacity:1
        });
        target.addTo(state.markerLayer).bindPopup(mapPopup({brand:r.brand,equipment:r.equipment,fault:r.fault,date_time:r.date_time,nik:r.nik,fullname:r.fullname,location:r.location})).openPopup();
        requestAnimationFrame(refreshMapSize);
    }
    function drawResume(items,pagination) {
        const body=el('oiResumeRows');body.replaceChildren();
        if(!items.length)emptyRow(body,11,'Tidak ada data pada rentang tanggal tersebut.');
        items.forEach((r,i)=>{
            const tr=document.createElement('tr');
            td(tr,(pagination.page-1)*pagination.per_page+i+1);
            td(tr,r.brand);td(tr,r.model);td(tr,r.equipment,'unit');td(tr,r.fault,'fault');td(tr,r.date_time);
            td(tr,r.speed);td(tr,r.status);td(tr,r.nik);td(tr,r.fullname);td(tr,r.occurrences);
            tr.title='Klik untuk melihat koordinat pada peta';
            tr.addEventListener('click',()=>{body.querySelectorAll('tr.selected').forEach(x=>x.classList.remove('selected'));tr.classList.add('selected');if(r.lat!==null&&r.lon!==null)focusPosition(r);else showAlert('Record ini tidak memiliki GPS valid.');});
            body.appendChild(tr);
        });
        state.page=pagination.page;state.hasMore=pagination.has_more;
        el('oiPrev').disabled=pagination.page<=1;
        el('oiNext').disabled=!pagination.has_more;
        el('oiPagination').textContent='Page '+pagination.page+' · '+pagination.per_page+' records/page';
        el('oiTableCount').textContent=number(items.length)+' records in current page';
    }
    function drawRanking(target,items,kind){
        const body=el(target);body.replaceChildren();
        if(!items.length){emptyRow(body,kind==='operator'?4:5,'Tidak ada data ranking.');return;}
        items.forEach((r,i)=>{
            const tr=document.createElement('tr');td(tr,i+1);
            if(kind==='operator'){td(tr,r.nik);td(tr,r.name);}
            else{td(tr,r.brand);td(tr,r.equipment);td(tr,r.model);}
            td(tr,number(r.total),'right');
            tr.title='Klik untuk melihat titik GPS terkait';
            tr.addEventListener('click',()=>{
                const id=kind==='operator'?r.nik:(r.brand+'|'+r.equipment);
                state.focus=(state.focus?.kind===kind && state.focus.id===id)?null:{kind,id};
                document.querySelectorAll('.oi-rank tbody tr').forEach(x=>x.classList.remove('oi-highlight'));
                if(state.focus)tr.classList.add('oi-highlight');
                plotMap(true);
                el('oiMapHint').textContent=state.focus?'Filter map: '+id:'Site · GPS WGS84';
            });
            body.appendChild(tr);
        });
    }
    function svgNode(tag,attrs={},text){const x=document.createElementNS('http://www.w3.org/2000/svg',tag);Object.entries(attrs).forEach(([k,v])=>x.setAttribute(k,String(v)));if(text!==undefined)x.textContent=String(text);return x;}
    function drawTrend(rows) {
        const svg=el('oiTrendChart');svg.replaceChildren();
        const width=680,height=265,margin={l:48,r:20,t:18,b:45},iw=width-margin.l-margin.r,ih=height-margin.t-margin.b;
        svg.setAttribute('viewBox',`0 0 ${width} ${height}`);
        if(!rows.length){svg.appendChild(svgNode('text',{x:width/2,y:height/2,'text-anchor':'middle',fill:'#94a3b8','font-size':13},'No trend data'));return;}
        const max=Math.max(1,...rows.map(x=>Number(x.total)||0));
        for(let i=0;i<=4;i++){
            const y=margin.t+ih*(1-i/4);
            svg.appendChild(svgNode('line',{x1:margin.l,y1:y,x2:width-margin.r,y2:y,stroke:'#e5eaf0','stroke-width':1}));
            svg.appendChild(svgNode('text',{x:margin.l-9,y:y+4,'text-anchor':'end',fill:'#8a9ab0','font-size':10},Math.round(max*i/4)));
        }
        const xx=i=>margin.l+(rows.length===1?iw/2:i*iw/(rows.length-1));
        const yy=v=>margin.t+ih*(1-Number(v)/max);
        const coords=rows.map((r,i)=>[xx(i),yy(r.total)]);
        const path=coords.map((p,i)=>(i?'L':'M')+p[0].toFixed(1)+','+p[1].toFixed(1)).join(' ');
        svg.appendChild(svgNode('path',{d:path,fill:'none',stroke:'#4b86cf','stroke-width':2.7,'stroke-linejoin':'round','stroke-linecap':'round'}));
        const step=Math.max(1,Math.ceil(rows.length/7));
        rows.forEach((r,i)=>{
            const [x,y]=coords[i];const dot=svgNode('circle',{cx:x,cy:y,r:4,fill:'#4b86cf',stroke:'#fff','stroke-width':1.5});
            const title=svgNode('title',{},r.date+': '+number(r.total));dot.appendChild(title);svg.appendChild(dot);
            if(i%step===0||i===rows.length-1)svg.appendChild(svgNode('text',{x,y:height-18,'text-anchor':'middle',fill:'#7c90a7','font-size':9},String(r.date).slice(5)));
        });
        svg.appendChild(svgNode('text',{x:18,y:height/2,transform:`rotate(-90 18 ${height/2})`,'text-anchor':'middle',fill:'#8295ab','font-size':10},'Total induced'));
    }
    async function fetchData(paginationOnly=false) {
        if(!CONFIGURED){showAlert(@json($configurationMessage));return;}
        const start=el('oiStart').value, stop=el('oiStop').value;
        if(!start||!stop||stop<start){showAlert('Tanggal Stop Date harus sama atau lebih besar dari Start Date.');return;}
        if(state.controller) state.controller.abort();
        state.controller=new AbortController();
        const params=new URLSearchParams({start_date:start,stop_date:stop,brand:el('oiBrand').value,page:String(state.page)});
        if(paginationOnly)params.set('pagination_only','1');
        loading(true);showAlert('');
        try{
            const response=await fetch(API+'?'+params.toString(),{headers:{'Accept':'application/json'},credentials:'same-origin',signal:state.controller.signal});
            const json=await response.json();
            if(!response.ok)throw new Error(json.message||'Gagal mengambil data server.');
            drawResume(json.resume||[],json.pagination);
            if(!paginationOnly){
                state.latest=json;state.mapPoints=json.map||[];state.focus=null;
                drawTrend(json.trend||[]);
                drawRanking('oiOperatorRows',json.operators||[],'operator');
                drawRanking('oiEquipmentRows',json.equipment||[],'equipment');
                setMetrics(json.summary||{});
                plotMap(true);
                setTimeout(refreshMapSize,160);
                el('oiRangeInfo').textContent='Data '+start+' s/d '+stop+' · '+(el('oiBrand').selectedOptions[0]?.text||'Semua');
                el('oiMapHint').textContent='Site MAP_OBJECT · GPS WGS84';
                if(state.siteFeatures.length)el('oiMapPlaceholder').hidden=true;
                else el('oiMapPlaceholder').hidden=!!state.map;
                if(!state.map)el('oiMapPlaceholder').querySelector('span').textContent='GPS sudah diproses: '+number(state.mapPoints.length)+' points. Untuk melihat peta, muat library Leaflet.';
            }
        }catch(err){if(err.name!=='AbortError')showAlert(err.message||'Unknown error');}
        finally{loading(false);}
    }
    el('oiForm').addEventListener('submit',e=>{e.preventDefault();state.page=1;fetchData(false);});
    el('oiPrev').addEventListener('click',()=>{if(state.page>1){state.page--;fetchData(true);}});
    el('oiNext').addEventListener('click',()=>{if(state.hasMore){state.page++;fetchData(true);}});
    el('oiZoomIn').addEventListener('click',()=>{if(state.map)state.map.zoomIn();});
    el('oiZoomOut').addEventListener('click',()=>{if(state.map)state.map.zoomOut();});
    el('oiLayer').addEventListener('click',()=>{state.baseMode=state.baseMode==='site'?'satellite':state.baseMode==='satellite'?'street':'site';setMapStatus('');setBaseLayer();});
    ['oiRoads','oiAreas','oiFronts','oiCallpoints'].forEach(id=>el(id).addEventListener('change',()=>renderSiteMap(false)));
    el('oiDrag').addEventListener('click',()=>{if(!state.map)return; if(state.map.dragging.enabled()){state.map.dragging.disable();el('oiDrag').classList.remove('active');}else{state.map.dragging.enable();el('oiDrag').classList.add('active');}});
    el('oiPlot').addEventListener('click',()=>{state.plotting=!state.plotting;el('oiPlot').classList.toggle('active',state.plotting);plotMap(false);});
    el('oiCoord').addEventListener('click',()=>showAlert('Koordinat GEOPOINTS dari MAP_OBJECT disimpan sebagai [longitude,latitude] WGS84 EPSG:4326. Saat diplot dengan Leaflet urutannya [latitude,longitude], dirender di Web Mercator EPSG:3857.'));
    el('oiExtend').addEventListener('click',()=>{el('oiMapPanel').classList.toggle('oi-map-expanded');el('oiExtend').classList.toggle('active');setTimeout(refreshMapSize,130);});
    el('oiReset').addEventListener('click',()=>{state.focus=null;document.querySelectorAll('.oi-rank tbody tr').forEach(x=>x.classList.remove('oi-highlight'));plotMap(true);showAlert('');});
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&el('oiMapPanel').classList.contains('oi-map-expanded')){el('oiMapPanel').classList.remove('oi-map-expanded');el('oiExtend').classList.remove('active');setTimeout(refreshMapSize,100);}});
    createMap();loadSiteMap();drawTrend([]);
    if(CONFIGURED)fetchData(false);
})();
</script>
@include('layout.footer')
