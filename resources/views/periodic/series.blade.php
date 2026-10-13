@include('layout.head')
@include('layout.sidebar')
<style>
    .pts-page{--pts-ink:#1a2e4d;--pts-sub:#73849b;--pts-bdr:#e0e7f0;--pts-main:#2866ab;--pts-soft:#eaf3ff;box-sizing:border-box;background:#f5f7fb;min-height:calc(100vh - 60px);padding:19px 20px 28px;color:var(--pts-ink)}
    .pts-page *{box-sizing:border-box}
    .pts-head{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:15px}
    .pts-head h1{font-size:21px;letter-spacing:-.45px;font-weight:800;margin:0 0 4px}
    .pts-head p{font-size:12px;color:var(--pts-sub);margin:0}
    .pts-links{display:flex;gap:8px;flex-wrap:wrap}
    .pts-link{font-size:11px;font-weight:750;color:#55708f;border:1px solid var(--pts-bdr);border-radius:8px;text-decoration:none;padding:9px 12px;background:#fff}
    .pts-link.active{background:#eaf3ff;color:#255ca3;border-color:#c9dff9}
    .pts-card{border:1px solid var(--pts-bdr);background:#fff;border-radius:5px;box-shadow:0 4px 13px rgba(25,45,79,.03)}
    .pts-bar{padding:15px;margin-bottom:14px}
    .pts-section{font-weight:800;color:#29476e;letter-spacing:.2px;font-size:11px;text-transform:uppercase;margin:0 0 12px}
    .pts-form{display:grid;grid-template-columns:minmax(125px,1fr) minmax(160px,1.25fr) minmax(200px,1.35fr) 105px minmax(220px,1.7fr) auto;gap:10px;align-items:end}
    .pts-field{min-width:0;position:relative}
    .pts-field label{display:block;color:#687b93;font-weight:750;font-size:10px;margin:0 0 6px;letter-spacing:.25px;text-transform:uppercase}
    .pts-input{display:block;width:100%;background:#fff;border:1px solid #d8e2ee;border-radius:8px;color:#2f4562;height:39px;padding:8px 11px;font-size:12px;min-width:0;outline:none}
    .pts-input:focus{border-color:#79aee9;box-shadow:0 0 0 3px rgba(40,102,171,.10)}
    .pts-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border-radius:8px;height:39px;padding:0 14px;border:1px solid #d7e2ef;background:#fff;font-size:12px;font-weight:800;color:#48627d;cursor:pointer;white-space:nowrap}
    .pts-btn:hover{background:#f0f5fd}
    .pts-btn.primary{background:#2866ab;color:white;border-color:#2866ab}
    .pts-btn.primary:hover{background:#1c528f}
    .pts-btn:disabled{opacity:.5;cursor:not-allowed}
    .pts-btn.toggle{width:100%;justify-content:space-between;text-align:left;font-weight:600;min-width:0}
    .pts-btn.toggle span:first-child{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .pts-parameter-panel{position:absolute;z-index:1000;top:calc(100% + 6px);left:0;right:0;min-width:300px;border:1px solid #dbe4ef;border-radius:10px;background:#fff;box-shadow:0 18px 40px rgba(28,56,98,.17);padding:10px}
    .pts-parameter-panel[hidden],.pts-alert[hidden]{display:none!important}
    .pts-parameter-list{max-height:260px;overflow-y:auto;margin:7px 0}
    .pts-param-row{display:flex;align-items:center;gap:8px;padding:7px;border-radius:6px;font-size:11px;color:#344b69;cursor:pointer}
    .pts-param-row:hover{background:#f2f6fd}
    .pts-param-row[hidden]{display:none!important}
    .pts-param-row span:last-child{margin-left:auto;color:#9aa8b8;font-size:9px;white-space:nowrap}
    .pts-param-row input{accent-color:#2866ab}
    .pts-parameter-foot,.pts-hint{color:#8a98aa;font-size:10px}
    .pts-meta{margin-top:13px;padding-top:12px;border-top:1px solid #edf1f6;color:#8091a7;font-size:11px;display:flex;flex-wrap:wrap;gap:13px}
    .pts-meta strong{color:#486380}
    .pts-alert{border:1px solid #f1c8c4;background:#fff2f0;color:#9d342d;border-radius:9px;padding:12px 14px;font-size:12px;margin-bottom:13px}
    .pts-tabs{display:flex;gap:6px;align-items:flex-end;flex-wrap:wrap;padding:11px 13px 0;background:#fff;border:1px solid var(--pts-bdr);border-radius:13px 13px 0 0;border-bottom:0}
    .pts-tab{border:1px solid #e1e9f3;border-bottom:0;border-radius:9px 9px 0 0;padding:11px 13px;background:#f2f5fa;color:#657990;font-size:12px;font-weight:750;cursor:pointer;display:flex;align-items:center;gap:7px;max-width:240px}
    .pts-tab span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .pts-tab.active{background:#fff;color:#2866ab;border-color:#bed6f4;box-shadow:inset 0 3px #2866ab}
    .pts-tab .pts-remove{width:18px;height:18px;background:transparent;border:0;border-radius:4px;color:#8ba0b8;font-weight:bold;cursor:pointer}
    .pts-tab .pts-remove:hover{background:#fdecec;color:#d33}
    .pts-chartcard{border-radius:0 0 13px 13px;padding:18px 19px 20px}
    .pts-chartheading{display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;align-items:flex-start}
    .pts-chartheading h2{font-size:18px;font-weight:800;margin:0 0 5px;color:#203957}
    .pts-chartheading p{font-size:11px;color:#8a9ab0;margin:0}
    .pts-charttools{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
    .pts-chip{font-size:10px;border:1px solid #dbe6f2;background:#f6f9fe;color:#5e789b;padding:6px 10px;border-radius:100px;font-weight:700}
    .pts-chip.selected{background:#e4f2ff;color:#2368b4}
    .pts-legend{display:flex;flex-wrap:wrap;gap:8px;margin-top:17px;margin-bottom:9px}
    .pts-legendbutton{display:flex;align-items:center;gap:7px;background:#fff;border:1px solid #e0e8f3;border-radius:8px;padding:8px 9px;font-size:11px;color:#405b77;cursor:pointer}
    .pts-legendbutton.muted{opacity:.40}
    .pts-swatch{display:inline-block;width:17px;height:3px;border-radius:3px}
    .pts-chartwrap{position:relative;width:100%;height:clamp(340px,48vh,550px);min-width:0;border:1px solid #edf1f6;border-radius:9px;background:linear-gradient(180deg,#ffffff 0%,#fcfdff 100%);overflow:hidden}
    .pts-svg{display:block;width:100%;height:100%;}
    .pts-empty{display:flex;align-items:center;justify-content:center;flex-direction:column;gap:9px;color:#9aacbe;text-align:center;height:100%;min-height:160px;font-size:12px;padding:20px}
    .pts-empty strong{color:#617c9b;font-size:13px}
    .pts-tooltip{position:absolute;z-index:3;pointer-events:none;max-width:275px;min-width:135px;border:1px solid #dce7f5;border-radius:8px;padding:9px 11px;box-shadow:0 10px 24px rgba(20,48,90,.13);background:rgba(255,255,255,.96);font-size:11px;color:#30465f;display:none}
    .pts-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:12px}
    .pts-stat{padding:12px;border:1px solid #e4eaf2;border-radius:10px;min-width:0;background:#fbfcff}
    .pts-stat-label{display:block;color:#8a9bb0;font-size:10px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-bottom:6px}
    .pts-stat-value{font-size:21px;font-weight:800;color:#264365}
    .pts-stat-code{display:block;margin-top:4px;color:#9baabd;font-size:9px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .pts-note{font-size:10px;line-height:1.7;color:#95a3b5;margin:13px 0 0}
    @media(max-width:1300px){.pts-form{grid-template-columns:repeat(3,minmax(0,1fr))}.pts-form>div:nth-child(5){grid-column:span 2}.pts-form>div:nth-child(6){align-self:end}}
    @media(max-width:800px){.pts-page{padding:14px 11px 23px}.pts-form{grid-template-columns:repeat(2,minmax(0,1fr))}.pts-form>div:nth-child(5){grid-column:span 2}.pts-form>div:nth-child(6){grid-column:span 2}.pts-form>div:nth-child(6) button{width:100%}.pts-parameter-panel{left:auto;right:0;width:min(85vw,380px)}.pts-chartcard{padding:13px 10px}.pts-tabs{padding:8px 7px 0;gap:4px}.pts-tab{padding:10px 9px;font-size:11px}.pts-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.pts-chartwrap{height:370px}.pts-chartheading h2{font-size:16px}}
    @media(max-width:450px){.pts-form{grid-template-columns:1fr}.pts-form>div:nth-child(5),.pts-form>div:nth-child(6){grid-column:1}.pts-tab{flex:1;min-width:70px;justify-content:center}}
</style>

<div class="page-content pts-page">
    <header class="pts-head">
        <div>
            <h1>Periodic Time Chart Series of Vehicles</h1>
        </div>
        <nav class="pts-links" aria-label="Machine Health navigation">
            <a class="pts-link" href="{{ url('/realtime/series') }}"><i class="fi fi-rr-time-fast me-1"></i> Realtime Series</a>
            <a class="pts-link active" href="{{ route('periodic-series.index') }}"><i class="fi fi-rr-chart-histogram me-1"></i> Periodic Series</a>
        </nav>
    </header>

    <section class="pts-card pts-bar">
        <p class="pts-section"><i class="fi fi-rr-settings-sliders me-1"></i> Navigation</p>
        <div class="pts-form">
            <div class="pts-field">
                <label for="ptsModel">Model</label>
                <select class="pts-input" id="ptsModel">
                    @forelse(array_keys($equipmentByModel) as $model)
                        <option value="{{ $model }}" {{ $defaultModel === $model ? 'selected' : '' }}>{{ $model }}</option>
                    @empty
                        <option value="">Tidak ada model</option>
                    @endforelse
                </select>
            </div>
            <div class="pts-field">
                <label for="ptsEquipment">Equipment · <span id="ptsSlotLabel">Vehicle 1</span></label>
                <select class="pts-input" id="ptsEquipment"></select>
            </div>
            <div class="pts-field">
                <label for="ptsDateTime">Date Time (mulai)</label>
                <input class="pts-input" id="ptsDateTime" type="datetime-local" step="60" value="{{ $defaultStart }}">
            </div>
            <div class="pts-field">
                <label for="ptsHours">Total Hour</label>
                <input class="pts-input" id="ptsHours" type="number" min="1" max="24" step="1" value="2">
            </div>
            <div class="pts-field" id="ptsParameterWrapper">
                <label for="ptsParameterToggle">Parameter · maks. 4</label>
                <button class="pts-btn toggle" type="button" id="ptsParameterToggle" aria-expanded="false">
                    <span id="ptsParameterSummary">Pilih parameter</span><span>⌄</span>
                </button>
                <div class="pts-parameter-panel" id="ptsParameterPanel" hidden>
                    <input type="search" id="ptsParameterSearch" class="pts-input" placeholder="Cari parameter VHM...">
                    <div class="pts-parameter-list">
                        @foreach($parameters as $code => $label)
                            <label class="pts-param-row" data-search="{{ strtolower($code . ' ' . $label) }}">
                                <input class="pts-param-check" type="checkbox" value="{{ $code }}" {{ in_array($code, $defaultParameters, true) ? 'checked' : '' }}>
                                <span>{{ $label }}</span><span>{{ $code }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="pts-parameter-foot">Pilih hingga 4 parameter untuk setiap equipment.</div>
                </div>
            </div>
            <div class="pts-field">
                <label>&nbsp;</label>
                <button class="pts-btn primary" id="ptsApply" type="button"><i class="fi fi-rr-check"></i> <span>Apply</span></button>
            </div>
        </div>
        <div class="pts-meta">
            <span><strong>Periode:</strong> <span id="ptsRange">—</span></span>
            <span><strong>Sampling:</strong> <span id="ptsSampling">—</span></span>
        </div>
    </section>

    <div id="ptsError" class="pts-alert" role="alert" hidden></div>

    <div class="pts-tabs" role="tablist" aria-label="Equipment tabs" id="ptsTabs"></div>
    <section class="pts-card pts-chartcard" aria-label="Historical machine health chart">
        <div class="pts-chartheading">
            <div>
                <h2 id="ptsChartTitle">Time Series Data</h2>
                <p id="ptsChartSubtitle">Pilih equipment dan tekan Apply untuk menampilkan data historis.</p>
            </div>
            <div class="pts-charttools">
                <span class="pts-chip" id="ptsRecordBadge">0 data</span>
                <button type="button" class="pts-btn" id="ptsResetLegend" title="Tampilkan semua parameter"><i class="fi fi-rr-refresh"></i> Show All</button>
            </div>
        </div>
        <div class="pts-legend" id="ptsLegend"></div>
        <div class="pts-chartwrap" id="ptsChartWrap">
            <div class="pts-empty"><strong>Select model, equipment, tanggal, total hour, ad parameter.</strong></div>
            <div class="pts-tooltip" id="ptsTooltip"></div>
        </div>
        <div class="pts-stats" id="ptsStats"></div>
        <p class="pts-note">Sumbu X = OPR_REPORTTIME, sumbu Y = nilai sensor asli (bukan normalisasi). Klik nama parameter di legenda untuk menyembunyikan/menampilkan kurva. Jika beberapa parameter memiliki rentang nilai sangat berbeda, pilih satu parameter agar skala terlihat jelas. Baris dengan VHM_TIMESTAMP tidak valid (mis. 1899-12-30) atau timestamp sensor yang berulang tidak diduplikasi.</p>
    </section>
</div>

<script>
(() => {
    'use strict';
    const dataUrl = @json(route('periodic-series.data'));
    const csrf = @json(csrf_token());
    const allEquipment = @json($equipmentByModel);
    const initialUnit = @json($defaultUnit);
    const colors = ['#2478c4', '#e28d36', '#21a184', '#a271cc'];
    const el = id => document.getElementById(id);
    const modelInput = el('ptsModel'), equipmentInput = el('ptsEquipment');
    const tabs = el('ptsTabs'), alertBox = el('ptsError');
    const toggler = el('ptsParameterToggle'), paramPanel = el('ptsParameterPanel');
    const paramCheckboxes = [...document.querySelectorAll('.pts-param-check')];
    const state = {
        slots: [initialUnit || '', '', '', ''],
        activeSlot: 0,
        response: null,
        hidden: new Set(),
        loading: false,
        latestRequest: 0,
    };
    const selectedParams = () => paramCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
    const text = (id, value) => { el(id).textContent = value; };
    const fmtNumber = value => Number(value).toLocaleString('en-US', { maximumFractionDigits: 3 });
    const fmtTime = raw => raw ? raw.replace('T',' ').replace(/\.\d+$/, '') : '—';
    const dateMs = raw => Date.parse(String(raw || '').replace(' ', 'T'));
    function showError(message) { alertBox.textContent = message; alertBox.hidden = false; }
    function clearError() { alertBox.textContent = ''; alertBox.hidden = true; }
    function updateParamTitle() {
        const values = selectedParams();
        text('ptsParameterSummary', values.length ? values.join(', ') : 'Pilih parameter');
    }
    function setEquipmentOptions() {
        const vehicles = allEquipment[modelInput.value] || [];
        equipmentInput.replaceChildren();
        const none = new Option('— Pilih equipment —', '');
        equipmentInput.add(none);
        for (const vehicle of vehicles) {
            const unavailable = state.slots.some((id, index) => index !== state.activeSlot && id === vehicle.id);
            const option = new Option(vehicle.id + (vehicle.active ? '' : ' (inactive)'), vehicle.id);
            option.disabled = unavailable;
            equipmentInput.add(option);
        }
        equipmentInput.value = state.slots[state.activeSlot] || '';
        text('ptsSlotLabel', 'Vehicle ' + (state.activeSlot + 1));
    }
    function drawTabs() {
        tabs.replaceChildren();
        for (let i = 0; i < MAX_UNITS; i++) {
            const tab = document.createElement('button');
            tab.type = 'button';
            tab.className = 'pts-tab' + (state.activeSlot === i ? ' active' : '');
            tab.setAttribute('role', 'tab');
            tab.setAttribute('aria-selected', String(state.activeSlot === i));
            const name = document.createElement('span');
            name.textContent = state.slots[i] || ('Vehicle ' + (i + 1));
            tab.appendChild(name);
            tab.addEventListener('click', () => {
                state.activeSlot = i;
                setEquipmentOptions();
                drawTabs();
                drawChart();
            });
            if (state.slots[i]) {
                const remove = document.createElement('span');
                remove.className = 'pts-remove';
                remove.title = 'Kosongkan Vehicle ' + (i + 1);
                remove.textContent = '×';
                remove.setAttribute('aria-label', 'Kosongkan Vehicle ' + (i + 1));
                remove.addEventListener('click', event => {
                    event.stopPropagation();
                    state.slots[i] = '';
                    setEquipmentOptions();
                    drawTabs();
                    drawChart();
                });
                tab.appendChild(remove);
            }
            tabs.appendChild(tab);
        }
    }
    modelInput.addEventListener('change', () => {
        state.slots = ['', '', '', ''];
        state.activeSlot = 0;
        state.response = null;
        setEquipmentOptions();
        drawTabs();
        drawChart();
    });
    equipmentInput.addEventListener('change', () => {
        state.slots[state.activeSlot] = equipmentInput.value;
        drawTabs();
        drawChart();
    });
    toggler.addEventListener('click', () => {
        paramPanel.hidden = !paramPanel.hidden;
        toggler.setAttribute('aria-expanded', String(!paramPanel.hidden));
    });
    document.addEventListener('click', event => {
        if (!el('ptsParameterWrapper').contains(event.target)) {
            paramPanel.hidden = true;
            toggler.setAttribute('aria-expanded', 'false');
        }
    });
    el('ptsParameterSearch').addEventListener('input', event => {
        const word = event.target.value.toLowerCase().trim();
        document.querySelectorAll('.pts-param-row').forEach(label => {
            label.hidden = !label.dataset.search.includes(word);
        });
    });
    paramCheckboxes.forEach(checkbox => checkbox.addEventListener('change', () => {
        if (selectedParams().length > 4) {
            checkbox.checked = false;
            showError('Maksimal 4 parameter sensor.');
        } else clearError();
        updateParamTitle();
    }));
    el('ptsResetLegend').addEventListener('click', () => {
        state.hidden.clear();
        drawChart();
    });

    async function apply() {
        if (state.loading) return;
        clearError();
        const units = state.slots.filter(Boolean);
        const params = selectedParams();
        const hours = Number(el('ptsHours').value);
        const start = el('ptsDateTime').value;
        if (!modelInput.value || units.length === 0) return showError('Pilih minimal 1 equipment pada tab Vehicle 1–4.');
        if (!params.length) return showError('Pilih minimal 1 parameter.');
        if (!start) return showError('Isi Date Time terlebih dahulu.');
        if (!Number.isInteger(hours) || hours < 1 || hours > 24) return showError('Total Hour harus antara 1 sampai 24.');
        state.loading = true;
        const requested = ++state.latestRequest;
        el('ptsApply').disabled = true;
        el('ptsApply').querySelector('span').textContent = 'Loading...';
        try {
            const res = await fetch(dataUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ model: modelInput.value, vehicles: units, parameters: params, start, hours }),
                cache: 'no-store',
                credentials: 'same-origin',
            });
            const data = await res.json();
            if (!res.ok) {
                const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
                throw new Error(firstError || data.message || `HTTP ${res.status}`);
            }
            if (requested !== state.latestRequest) return;
            state.response = data;
            state.hidden.clear();
            text('ptsRange', fmtTime(data.from) + ' s/d ' + fmtTime(data.until));
            text('ptsSampling', data.bucket_seconds + ' detik / bucket');
            drawChart();
        } catch (error) {
            showError('Gagal mengambil data: ' + error.message);
        } finally {
            state.loading = false;
            el('ptsApply').disabled = false;
            el('ptsApply').querySelector('span').textContent = 'Apply';
        }
    }
    el('ptsApply').addEventListener('click', apply);

    function emptyChart(title, description) {
        const wrap = el('ptsChartWrap');
        wrap.replaceChildren();
        const box = document.createElement('div');
        box.className = 'pts-empty';
        const strong = document.createElement('strong');
        strong.textContent = title;
        const caption = document.createElement('span');
        caption.textContent = description;
        box.append(strong, caption);
        wrap.appendChild(box);
        const tooltip = document.createElement('div');
        tooltip.id = 'ptsTooltip';
        tooltip.className = 'pts-tooltip';
        wrap.appendChild(tooltip);
    }
    function svgNode(tag, attributes = {}, content = null) {
        const node = document.createElementNS('http://www.w3.org/2000/svg', tag);
        Object.entries(attributes).forEach(([key, value]) => node.setAttribute(key, String(value)));
        if (content !== null) node.textContent = String(content);
        return node;
    }
    function drawChart() {
        const unit = state.slots[state.activeSlot];
        const response = state.response;
        const ready = !!unit && response && response.vehicles.includes(unit);
        text('ptsChartTitle', 'Time Series Data ' + (unit || ('Vehicle ' + (state.activeSlot + 1))));
        text('ptsChartSubtitle', ready ? 'Data historis · ' + fmtTime(response.from) + ' — ' + fmtTime(response.until)
            : 'Pilih equipment lalu tekan Apply untuk melihat data historis.');
        const legend = el('ptsLegend'), stats = el('ptsStats');
        legend.replaceChildren();
        stats.replaceChildren();
        if (!ready) {
            text('ptsRecordBadge', '0 data');
            emptyChart('Belum ada data untuk tab ini', unit ? 'Tekan Apply untuk membaca periode yang dipilih.' : 'Pilih unit pada dropdown Equipment.');
            return;
        }
        const rows = response.series[unit] || [];
        const keys = Object.keys(response.parameters);
        text('ptsRecordBadge', rows.length + ' record');
        for (let p = 0; p < keys.length; p++) {
            const key = keys[p];
            const color = colors[p % colors.length];
            const legendButton = document.createElement('button');
            legendButton.type = 'button';
            legendButton.className = 'pts-legendbutton' + (state.hidden.has(key) ? ' muted' : '');
            const stripe = document.createElement('i');
            stripe.className = 'pts-swatch';
            stripe.style.backgroundColor = color;
            const label = document.createElement('span');
            label.textContent = key;
            legendButton.append(stripe, label);
            legendButton.title = response.parameters[key];
            legendButton.addEventListener('click', () => {
                if (state.hidden.has(key)) state.hidden.delete(key); else state.hidden.add(key);
                drawChart();
            });
            legend.appendChild(legendButton);

            const vals = rows.map(row => row.values[key]).filter(value => typeof value === 'number' && Number.isFinite(value));
            const card = document.createElement('div');
            card.className = 'pts-stat';
            const name = document.createElement('span');
            name.className = 'pts-stat-label';
            name.textContent = response.parameters[key];
            const value = document.createElement('div');
            value.className = 'pts-stat-value';
            value.textContent = vals.length ? fmtNumber(vals[vals.length - 1]) : '—';
            const code = document.createElement('span');
            code.className = 'pts-stat-code';
            code.textContent = key + (vals.length ? (' · min ' + fmtNumber(Math.min(...vals)) + ' · max ' + fmtNumber(Math.max(...vals))) : ' · Tidak ada nilai');
            card.append(name, value, code);
            stats.appendChild(card);
        }
        if (!rows.length) {
            emptyChart('Tidak ada pembacaan VHM', 'Periode ini tidak berisi VHM_TIMESTAMP valid untuk equipment tersebut.');
            return;
        }
        const visible = keys.filter(key => !state.hidden.has(key));
        if (!visible.length) {
            emptyChart('Semua parameter disembunyikan', 'Klik legenda atau Show All untuk menampilkannya kembali.');
            return;
        }
        const numbers = rows.flatMap(row => visible.map(key => row.values[key]))
            .filter(value => typeof value === 'number' && Number.isFinite(value));
        if (!numbers.length) {
            emptyChart('Nilai sensor tidak tersedia', 'Parameter ini tidak berisi nilai numerik dalam periode terpilih.');
            return;
        }
        let minY = Math.min(...numbers), maxY = Math.max(...numbers);
        let space = maxY - minY;
        if (space === 0) space = Math.max(Math.abs(maxY) * .10, 1);
        minY -= space * .10;
        maxY += space * .10;
        const W = 1200, H = 520, left = 80, right = 30, top = 25, bottom = 72;
        const plotW = W - left - right, plotH = H - top - bottom;
        const startMs = dateMs(response.from), endMs = dateMs(response.until);
        const px = ms => left + ((ms - startMs) / Math.max(1, endMs - startMs)) * plotW;
        const py = value => top + ((maxY - value) / (maxY - minY)) * plotH;
        const svg = svgNode('svg', {viewBox:`0 0 ${W} ${H}`, preserveAspectRatio:'none', class:'pts-svg', role:'img', 'aria-label':'Grafik historis equipment ' + unit});
        // grid and axis labels
        for (let i = 0; i <= 6; i++) {
            const y = top + (i / 6) * plotH;
            svg.appendChild(svgNode('line', {x1:left, x2:W-right, y1:y, y2:y, stroke:'#e8edf4', 'stroke-width':1}));
            svg.appendChild(svgNode('text', {x:left-12, y:y+4, 'text-anchor':'end', fill:'#7d8ba0', 'font-size':13}, fmtNumber(maxY - (i / 6)*(maxY-minY))));
        }
        for (let i = 0; i <= 6; i++) {
            const x = left + (i / 6) * plotW;
            const timestamp = new Date(startMs + (endMs-startMs) * (i/6));
            const label = Number.isFinite(timestamp.getTime()) ? timestamp.toLocaleString('id-ID',{day:'2-digit', month:'2-digit',hour:'2-digit',minute:'2-digit'}) : '—';
            svg.appendChild(svgNode('line', {x1:x, x2:x, y1:top, y2:top+plotH, stroke:'#f4f6fa', 'stroke-width':1}));
            svg.appendChild(svgNode('text', {x:x, y:H-37, 'text-anchor':'middle', fill:'#8293a7', 'font-size':12}, label));
        }
        svg.appendChild(svgNode('text', {x:20, y:top+plotH/2, transform:`rotate(-90 20 ${top+plotH/2})`, 'text-anchor':'middle',fill:'#667c97','font-size':15}, 'Value'));
        svg.appendChild(svgNode('text', {x:left+plotW/2, y:H-9, 'text-anchor':'middle', fill:'#667c97', 'font-size':14}, 'Date / Time (OPR_REPORTTIME)'));
        const dots = [];
        for (let p = 0; p < keys.length; p++) {
            const key = keys[p];
            if (!visible.includes(key)) continue;
            let line = [];
            const segments = [];
            const color = colors[p % colors.length];
            for (const row of rows) {
                const val = row.values[key], at = dateMs(row.time);
                if (typeof val !== 'number' || !Number.isFinite(val) || !Number.isFinite(at)) {
                    if (line.length) segments.push(line);
                    line = [];
                    continue;
                }
                const point = { x: px(at), y: py(val), value: val, time: row.time, name: key, sensorTime: row.sensor_time, color };
                line.push(point);
                dots.push(point);
            }
            if (line.length) segments.push(line);
            for (const segment of segments) {
                const path = segment.map((pt, idx) => (idx === 0 ? 'M ' : ' L ') + pt.x.toFixed(2) + ' ' + pt.y.toFixed(2)).join('');
                svg.appendChild(svgNode('path', {d:path, stroke:color, fill:'none', 'stroke-width':2.6, 'stroke-linejoin':'round', 'stroke-linecap':'round'}));
                for (const point of segment) {
                    const dot = svgNode('circle', {cx:point.x,cy:point.y,r:3.1,fill:'#fff',stroke:color,'stroke-width':1.7});
                    dot.appendChild(svgNode('title',{},`${key}: ${fmtNumber(point.value)} · ${fmtTime(point.time)}`));
                    svg.appendChild(dot);
                }
            }
        }
        // Hoverable transparent overlay: nearest data point by Euclidean distance.
        const hit = svgNode('rect',{x:left,y:top,width:plotW,height:plotH,fill:'transparent','pointer-events':'all'});
        const tooltip = el('ptsTooltip');
        hit.addEventListener('mousemove', event => {
            if (!dots.length) return;
            const r = svg.getBoundingClientRect();
            const x = (event.clientX-r.left)/r.width*W;
            const y = (event.clientY-r.top)/r.height*H;
            let best = null, dist = Infinity;
            for (const pt of dots) {
                const dx = pt.x-x, dy = (pt.y-y)*1.3;
                const sq = dx*dx+dy*dy;
                if (sq < dist) { best = pt; dist = sq; }
            }
            if (!best) return;
            tooltip.replaceChildren();
            const heading = document.createElement('strong');
            heading.textContent = best.name + ': ' + fmtNumber(best.value);
            const time = document.createElement('div');
            time.textContent = 'Report: ' + fmtTime(best.time);
            const sensor = document.createElement('div');
            sensor.textContent = 'Sensor: ' + fmtTime(best.sensorTime);
            tooltip.append(heading,time,sensor);
            tooltip.style.display = 'block';
            tooltip.style.left = Math.max(8, Math.min(r.width-205, event.clientX-r.left+12)) + 'px';
            tooltip.style.top = Math.max(8, event.clientY-r.top-78) + 'px';
        });
        hit.addEventListener('mouseleave', () => { tooltip.style.display = 'none'; });
        svg.appendChild(hit);
        const wrap = el('ptsChartWrap');
        wrap.replaceChildren();
        wrap.appendChild(svg);
        wrap.appendChild(tooltip);
    }

    updateParamTitle();
    setEquipmentOptions();
    drawTabs();
    drawChart();
})();
</script>
@include('layout.footer')
