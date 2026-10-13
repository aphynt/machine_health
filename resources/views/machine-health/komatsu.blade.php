@include('layout.head')
@include('layout.sidebar')

<style>
    .vhms-dashboard .card,
    .vhms-dashboard .card-header,
    .vhms-dashboard .card-body,
    .vhms-dashboard .footer .card {
        border-radius: 0 !important;
    }

    .vhms-dashboard .card {
        border: 1px solid #bfbfbf;
        box-shadow: none !important;
    }

    .vhms-dashboard .card-header {
        background: #ffffff;
        border-bottom: 1px solid #bfbfbf;
        padding: 6px 8px;
        min-height: 34px;
    }

    .vhms-dashboard .card-title {
        font-size: 13px;
        font-weight: 700;
        color: #2f5f9f;
        margin: 0;
        line-height: 1.2;
    }

    .vhms-dashboard .toolbar-icons {
        display: flex;
        gap: 8px;
        align-items: center;
        color: #7d7d7d;
        font-size: 14px;
    }

    .vhms-dashboard .toolbar-icons i,
    .vhms-dashboard .toolbar-icons button {
        cursor: pointer;
        border: 0;
        background: transparent;
        color: inherit;
        padding: 0;
    }

    .vhms-dashboard .panel-body {
        padding: 8px;
        background: #f6f6f6;
    }

    .vhms-dashboard .dashboard-top-title {
        font-size: 12px;
        font-weight: 700;
        color: #2f5f9f;
        margin: 10px 0 6px;
    }

    .vhms-dashboard .map-box,
    .vhms-dashboard .treemap-box,
    .vhms-dashboard .location-box,
    .vhms-dashboard .pie-box,
    .vhms-dashboard .trend-box {
        background: #efefef;
        position: relative;
        overflow: hidden;
    }

    .vhms-dashboard .map-box {
        height: 455px;
        border: 1px solid #c8c8c8;
    }

    .vhms-dashboard .legend-box {
        position: absolute;
        top: 16px;
        left: 14px;
        width: 82px;
        background: rgba(255, 255, 255, 0.88);
        padding: 10px 8px;
        z-index: 3;
    }

    .vhms-dashboard .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: #333;
        margin-bottom: 7px;
    }

    .vhms-dashboard .legend-color {
        width: 28px;
        height: 18px;
        display: inline-block;
    }

    .vhms-dashboard .map-svg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .vhms-dashboard .map-points {
        position: absolute;
        inset: 0;
        z-index: 2;
    }

    .vhms-dashboard .map-point {
        position: absolute;
        border-radius: 50%;
        background: #4b86c6;
        border: 1px solid rgba(255, 255, 255, .9);
        transform: translate(-50%, -50%);
        cursor: pointer;
        opacity: .95;
    }

    .vhms-dashboard .map-label {
        position: absolute;
        font-size: 10px;
        font-weight: 700;
        color: #2f2f2f;
        white-space: nowrap;
        z-index: 3;
        pointer-events: none;
    }

    .vhms-dashboard .treemap-box {
        height: 455px;
        padding: 0;
        overflow: auto;
        background: #fff;
    }

    .vhms-dashboard .treemap-list-row {
        display: grid;
        grid-template-columns: 42px 1fr 70px;
        gap: 10px;
        align-items: center;
        padding: 8px 10px;
        border-bottom: 1px solid #e5e5e5;
        font-size: 12px;
        cursor: pointer;
        background: #fff;
    }

    .vhms-dashboard .treemap-list-row:hover,
    .vhms-dashboard .treemap-list-row.active {
        background: #f1f7ff;
    }

    .vhms-dashboard .treemap-list-rank {
        font-weight: 700;
        color: #2f5f9f;
    }

    .vhms-dashboard .treemap-list-value {
        text-align: right;
        font-weight: 700;
    }

    .vhms-dashboard .trend-box {
        height: 355px;
        border: 1px solid #c8c8c8;
        padding: 8px 8px 2px;
        background: #f4f4f4;
    }

    .vhms-dashboard .location-box {
        height: 355px;
        border: 1px solid #c8c8c8;
        background: #f8f8f8;
        display: flex;
        flex-direction: column;
    }

    .vhms-dashboard .location-total {
        font-size: 18px;
        color: #222;
        padding: 10px 14px;
        border-bottom: 1px solid #bfbfbf;
        background: #fff;
    }

    .vhms-dashboard .location-list {
        flex: 1;
        overflow: auto;
    }

    .vhms-dashboard .location-item {
        display: grid;
        grid-template-columns: 80px 1fr;
        align-items: center;
        border-bottom: 1px solid #bfbfbf;
        background: #fff;
    }

    .vhms-dashboard .location-count {
        padding: 10px 14px;
        font-size: 14px;
        color: #222;
        align-self: start;
    }

    .vhms-dashboard .location-name {
        color: #111;
        text-align: center;
        padding: 10px;
    }

    .vhms-dashboard .pie-box {
        height: 355px;
        border: 1px solid #c8c8c8;
        background: #f9f9f9;
        padding: 12px;
    }

    .vhms-dashboard .pie-title {
        text-align: center;
        font-size: 18px;
        color: #6b7280;
    }

    .vhms-dashboard .tab-strip {
        display: flex;
        gap: 0;
        border-bottom: 1px solid #bfbfbf;
        margin-bottom: 8px;
    }

    .vhms-dashboard .tab-btn {
        border: 1px solid #bfbfbf;
        border-bottom: none;
        background: #fff;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
        color: #222;
        margin-right: 4px;
    }

    .vhms-dashboard .tab-btn.active {
        background: #f8f8f8;
    }

    .vhms-dashboard .tab-pane-custom {
        display: none;
    }

    .vhms-dashboard .tab-pane-custom.active {
        display: block;
    }

    .vhms-dashboard .top10-box {
        height: 818px;
        border: 1px solid #c8c8c8;
        background: #f7f7f7;
        padding: 10px;
        overflow: auto;
    }

    .vhms-dashboard .rank-list-row {
        display: grid;
        grid-template-columns: 52px 1fr 88px;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-bottom: 1px solid #e3e3e3;
        font-size: 12px;
        background: #fff;
    }

    .vhms-dashboard .rank-list-row:hover {
        background: #f7faff;
    }

    .vhms-dashboard .rank-no {
        font-weight: 700;
        color: #2f5f9f;
    }

    .vhms-dashboard .rank-name {
        color: #222;
        line-height: 1.25;
    }

    .vhms-dashboard .rank-value {
        text-align: right;
        font-weight: 700;
        color: #111;
    }

    @media (max-width: 1399px) {
        .vhms-dashboard .location-name {
            font-size: 18px;
        }
    }

</style>

        <div class="page-content vhms-dashboard">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">

            <!-- LEFT: TAB -->
            <div class="tab-strip mb-0 d-flex flex-nowrap">
                <button class="tab-btn active" data-tab="tab-overview">
                    At A Glance VHMS Machine Health
                </button>
                <button class="tab-btn" data-tab="tab-equipment">
                    The TOP 10 Equipment with The Highest Warning
                </button>
            </div>

            <!-- RIGHT: FILTER -->
            <form method="GET" action="{{ url()->current() }}"
                class="d-flex align-items-center gap-2 flex-nowrap mb-0">

                <input type="date" name="start_date"
                    class="form-control form-control-sm"
                    style="width:140px;"
                    value="{{ $data['start_date'] ?? '' }}">

                <input type="date" name="end_date"
                    class="form-control form-control-sm"
                    style="width:140px;"
                    value="{{ $data['end_date'] ?? '' }}">

                <button type="submit" class="btn btn-primary btn-sm">Apply</button>

                <button type="button" class="btn btn-secondary btn-sm"
                    onclick="location.reload()" title="Reload">
                    <i class="iconoir-refresh"></i>
                </button>

            </form>

        </div>
        <div id="tab-overview" class="tab-pane-custom active">
            <div class="dashboard-top-title">At A Glance VHMS Machine Health</div>

            <div class="row g-2">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Map Distribution</h4>
                            <div class="toolbar-icons">
                                <button type="button" data-download-target="mapBox" title="Download PNG"><i
                                        class="iconoir-share-android"></i></button>


                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="map-box" id="mapBox">
                                <div class="legend-box" id="mapLegend"></div>
                                <svg class="map-svg" viewBox="0 0 100 100" preserveAspectRatio="none"></svg>
                                <div class="map-points" id="mapPoints"></div>
                                <div class="map-label" id="mapLabel1" style="display:none;"></div>
                                <div class="map-label" id="mapLabel2" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Treemap Distribution per Fault Code</h4>
                            <div class="toolbar-icons">
                                <select id="faultListLimit" class="form-select form-select-sm">
                                    <option value="10">Top 10</option>
                                    <option value="15" selected>Top 15</option>
                                    <option value="20">Top 20</option>
                                </select>
                            </div>
                        </div>
                        <div class="panel-body p-0">
                            <div class="treemap-box" id="treemapList"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mt-1">
                <div class="col-xl-9">
                    <div class="row g-2">
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Trend Per Day</h4>
                                    <div class="toolbar-icons">
                                        <button type="button" data-download-chart="trendChart" title="Download PNG"><i
                                                class="iconoir-share-android"></i></button>


                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="trend-box">
                                        <canvas id="trendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Location Name</h4>
                                    <div class="toolbar-icons">


                                    </div>
                                </div>
                                <div class="panel-body p-0">
                                    <div class="location-box">
                                        <div class="location-total" id="locationTotal">0</div>
                                        <div class="location-list" id="locationList"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Pie Chart Distribution Per Layer</h4>
                            <div class="toolbar-icons">
                                <button type="button" data-download-chart="pieChart" title="Download PNG"><i
                                        class="iconoir-share-android"></i></button>


                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="pie-box">
                                <div class="pie-title">Total Data Per Map Layer</div>

                                <div class="pie-chart-wrapper">
                                    <canvas id="pieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tab-equipment" class="tab-pane-custom">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">The TOP 10 Equipment with The Highest Warning</h4>
                    <div class="toolbar-icons">


                    </div>
                </div>
                <div class="panel-body">
                    <div class="top10-box">
                        <div class="row g-3">
                            <div class="col-12 col-xl-5">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Top Fault List</h4>
                                        <select id="equipmentFaultTopLimit" class="form-select form-select-sm"
                                            style="width:90px;">
                                            <option value="10">Top 10</option>
                                            <option value="15" selected>Top 15</option>
                                            <option value="20">Top 20</option>
                                        </select>
                                    </div>
                                    <div class="panel-body" style="padding:0; background:#fff;">
                                        <div id="equipmentFaultList"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-7">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Top Unit List</h4>
                                        <select id="equipmentUnitTopLimit" class="form-select form-select-sm"
                                            style="width:90px;">
                                            <option value="10">Top 10</option>
                                            <option value="15" selected>Top 15</option>
                                            <option value="20">Top 20</option>
                                        </select>
                                    </div>
                                    <div class="panel-body" style="padding:0; background:#fff;">
                                        <div id="equipmentUnitList"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rawLocationRows = @json($data['PERIODIC_OPERATOR_INDUCED_WITH_LOCATION'] ?? []);
        const rawEquipmentRows = @json($data['PERIODIC_OPERATOR_INDUCED_KOMATSU_PER_EQUIPMENTID'] ?? []);
        const rawMapRows = @json($data['mapRows'] ?? []);

        const safeNumber = (value) => {
            const num = Number(value);
            return Number.isFinite(num) ? num : 0;
        };

        const normalizeText = (value, fallback = 'UNKNOWN') => {
            if (value === null || value === undefined) return fallback;
            const text = String(value).trim();
            return text === '' ? fallback : text;
        };

        const formatK = (value) => {
            if (value >= 1000000) return (value / 1000000).toFixed(2) + 'M';
            if (value >= 1000) return (value / 1000).toFixed(2) + 'K';
            return String(value);
        };

        const parseGeoPoints = (geoText) => {
            if (!geoText || typeof geoText !== 'string') return [];
            const matches = geoText.match(/\[([^\]]+)\]/g) || [];
            return matches.map(pair => {
                const cleaned = pair.replace(/[\[\]]/g, '').split(',');
                return {
                    lon: Number(cleaned[0]),
                    lat: Number(cleaned[1])
                };
            }).filter(p => Number.isFinite(p.lon) && Number.isFinite(p.lat));
        };

        const locationRows = rawLocationRows.map(row => ({
            From_Date_Time: normalizeText(row.From_Date_Time, ''),
            Count: safeNumber(row.Count),
            Error_Message: normalizeText(row.Error_Message, 'UNKNOWN ERROR'),
            LOC_NAME: normalizeText(row.LOC_NAME, 'NO LOCATION'),
            LAYERNAME: normalizeText(row.LAYERNAME, 'UNMAPPED'),
            GPS_LON: row.GPS_LON === null || row.GPS_LON === '' ? null : Number(row.GPS_LON),
            GPS_LAT: row.GPS_LAT === null || row.GPS_LAT === '' ? null : Number(row.GPS_LAT),
            OPR_NRP: normalizeText(row.OPR_NRP, 'UNKNOWN OPERATOR'),
            VHC_ID: normalizeText(row.VHC_ID, 'UNKNOWN UNIT')
        }));

        const equipmentRows = rawEquipmentRows.map(row => ({
            Count: safeNumber(row.Count),
            Error_Message: normalizeText(row.Error_Message, 'UNKNOWN ERROR'),
            VHC_ID: normalizeText(row.VHC_ID, 'UNKNOWN UNIT')
        }));

        const mapObjects = rawMapRows.map(row => ({
            MAPOBJECTID: row.MAPOBJECTID,
            LAYERNAME: normalizeText(row.LAYERNAME, 'UNMAPPED'),
            MAPOBJECTNAME: normalizeText(row.MAPOBJECTNAME, ''),
            LONGITUDE: row.LONGITUDE === null || row.LONGITUDE === '' ? null : Number(row
                .LONGITUDE),
            LATITUDE: row.LATITUDE === null || row.LATITUDE === '' ? null : Number(row.LATITUDE),
            GEOPOINTS: parseGeoPoints(row.GEOPOINTS),
            TITLE: normalizeText(row.TITLE, '')
        }));

        const coordPool = [];
        locationRows.forEach(row => {
            const lon = Number(row.GPS_LON);
            const lat = Number(row.GPS_LAT);
            if (Number.isFinite(lon) && Number.isFinite(lat) && lon > 100 && lon < 130 && lat < 5 &&
                lat > -10) {
                coordPool.push({
                    lon,
                    lat
                });
            }
        });
        mapObjects.forEach(item => {
            if (Number.isFinite(item.LONGITUDE) && Number.isFinite(item.LATITUDE) && item.LONGITUDE >
                100 && item.LONGITUDE < 130 && item.LATITUDE < 5 && item.LATITUDE > -10) {
                coordPool.push({
                    lon: item.LONGITUDE,
                    lat: item.LATITUDE
                });
            }
            item.GEOPOINTS.forEach(p => {
                if (Number.isFinite(p.lon) && Number.isFinite(p.lat)) {
                    coordPool.push({
                        lon: p.lon,
                        lat: p.lat
                    });
                }
            });
        });

        const lonMin = Math.min(...coordPool.map(p => p.lon), 115.86);
        const lonMax = Math.max(...coordPool.map(p => p.lon), 115.91);
        const latMin = Math.min(...coordPool.map(p => p.lat), -1.871);
        const latMax = Math.max(...coordPool.map(p => p.lat), -1.844);

        const projectPoint = (lon, lat) => {
            const x = ((lon - lonMin) / ((lonMax - lonMin) || 1)) * 88 + 6;
            const y = (1 - ((lat - latMin) / ((latMax - latMin) || 1))) * 82 + 6;
            return {
                x: Number(x.toFixed(4)),
                y: Number(y.toFixed(4))
            };
        };

        const uniqueDates = [...new Set(locationRows.map(row => row.From_Date_Time).filter(Boolean))].sort((a,
            b) => new Date(a) - new Date(b));

        const overviewErrorTotals = {};
        locationRows.forEach(row => {
            overviewErrorTotals[row.Error_Message] = (overviewErrorTotals[row.Error_Message] || 0) + row
                .Count;
        });

        const equipmentErrorTotals = {};
        equipmentRows.forEach(row => {
            equipmentErrorTotals[row.Error_Message] = (equipmentErrorTotals[row.Error_Message] || 0) +
                row.Count;
        });

        const colorPalette = ['#6f80c5', '#9d6b80', '#b99455', '#c35050', '#c24f56', '#6b98a8', '#67a57f',
            '#9c8cc0', '#8a6f4b', '#6076a8', '#5f9e7c'
        ];

        const buildLegendScale = (points) => {
            const values = points.map(point => point.count).filter(value => Number.isFinite(value) &&
                value > 0).sort((a, b) => b - a);
            if (!values.length) return [{
                label: '0',
                color: '#4a87c8'
            }];

            const max = values[0];
            const min = values[values.length - 1];
            const legendColors = ['#d73e5a', '#cf4b66', '#c75772', '#be647e', '#a86d93', '#9074a6',
                '#7d79b5', '#687fc1', '#5684c8', '#4a87c8'
            ];
            return legendColors.map((color, index) => {
                const ratio = (legendColors.length - 1 - index) / Math.max(legendColors.length - 1,
                    1);
                const value = Math.round(min + ((max - min) * ratio));
                return {
                    label: formatK(value),
                    color
                };
            });
        };

        const treemapListEl = document.getElementById('treemapList');
        const mapLegend = document.getElementById('mapLegend');
        const mapSvg = document.querySelector('.map-svg');
        const mapPointsLayer = document.getElementById('mapPoints');
        const locationList = document.getElementById('locationList');
        const locationTotal = document.getElementById('locationTotal');
        const mapLabel1 = document.getElementById('mapLabel1');
        const mapLabel2 = document.getElementById('mapLabel2');
        const faultListLimitEl = document.getElementById('faultListLimit');
        const equipmentFaultListEl = document.getElementById('equipmentFaultList');
        const equipmentUnitListEl = document.getElementById('equipmentUnitList');
        const equipmentFaultTopLimitEl = document.getElementById('equipmentFaultTopLimit');
        const equipmentUnitTopLimitEl = document.getElementById('equipmentUnitTopLimit');

        const trendChart = new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Total Fault',
                    data: [],
                    backgroundColor: 'rgba(181,145,114,0.35)',
                    borderColor: 'rgba(181,145,114,1)',
                    borderWidth: 1.5,
                    barThickness: 18
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: '#d0d0d0'
                        },
                        ticks: {
                            color: '#555',
                            autoSkip: true,
                            maxRotation: 0,
                            callback: function (value, index) {
                                const raw = this.getLabelForValue(value);
                                if (!raw) return '';
                                if (index === 0 || index === this.getLabels().length - 1 || index %
                                    3 === 0) {
                                    const d = new Date(raw);
                                    if (!Number.isNaN(d.getTime())) {
                                        return `${d.getMonth() + 1}/${d.getDate()}/${d.getFullYear()}`;
                                    }
                                }
                                return '';
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Count (Sum)',
                            color: '#333'
                        },
                        grid: {
                            color: '#d0d0d0'
                        },
                        ticks: {
                            color: '#555'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        const pieChart = new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: ['#6aa786', '#9a7a64', '#b6bf57', '#d08bc6', '#6172a9',
                        '#86b6d8'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'left',
                        labels: {
                            boxWidth: 12,
                            color: '#666'
                        }
                    }
                }
            }
        });

        const mapStyleByLayer = {
            MAP_ROAD: {
                stroke: '#c9c9c9',
                width: 0.25,
                fill: 'none'
            }
        };

        const mapBaseObjects = mapObjects.filter(item => item.LAYERNAME === 'MAP_ROAD' && item.GEOPOINTS
            .length > 1);

        function renderBaseMap() {
            const baseShapes = mapBaseObjects.map(item => {
                const style = mapStyleByLayer[item.LAYERNAME] || {
                    stroke: '#c9c9c9',
                    width: 0.25,
                    fill: 'none'
                };
                const points = item.GEOPOINTS.map(p => {
                    const projected = projectPoint(p.lon, p.lat);
                    return `${projected.x},${projected.y}`;
                }).join(' ');
                return `<polyline points="${points}" fill="none" stroke="${style.stroke}" stroke-width="${style.width}" stroke-linejoin="round" stroke-linecap="round" />`;
            }).join('');
            mapSvg.innerHTML = baseShapes;
        }

        function aggregateOverview(filterKey = 'all') {
            const filteredRows = filterKey === 'all' ? locationRows : locationRows.filter(row => row
                .Error_Message === filterKey);
            const total = filteredRows.reduce((sum, row) => sum + row.Count, 0);

            const trendMap = {};
            uniqueDates.forEach(date => {
                trendMap[date] = 0;
            });
            filteredRows.forEach(row => {
                if (row.From_Date_Time) {
                    trendMap[row.From_Date_Time] = (trendMap[row.From_Date_Time] || 0) + row.Count;
                }
            });

            const locationMap = {};
            filteredRows.forEach(row => {
                locationMap[row.LOC_NAME] = (locationMap[row.LOC_NAME] || 0) + row.Count;
            });
            const locations = Object.entries(locationMap)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 10)
                .map(([name, count]) => ({
                    name,
                    count
                }));

            const layerMap = {};
            filteredRows.forEach(row => {
                layerMap[row.LAYERNAME] = (layerMap[row.LAYERNAME] || 0) + row.Count;
            });
            const layerTotal = Object.values(layerMap).reduce((a, b) => a + b, 0) || 1;
            const layers = Object.fromEntries(
                Object.entries(layerMap)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 6)
                .map(([name, count]) => [name, Number(((count / layerTotal) * 100).toFixed(2))])
            );

            const pointMap = {};
            filteredRows.forEach(row => {
                const key = row.LOC_NAME;
                if (!pointMap[key]) {
                    pointMap[key] = {
                        title: row.LOC_NAME,
                        total: 0,
                        lon: null,
                        lat: null
                    };
                }
                pointMap[key].total += row.Count;
                const lon = Number(row.GPS_LON);
                const lat = Number(row.GPS_LAT);
                if (Number.isFinite(lon) && Number.isFinite(lat) && lon > 100 && lon < 130 && lat < 5 &&
                    lat > -10) {
                    pointMap[key].lon = lon;
                    pointMap[key].lat = lat;
                }
            });

            Object.values(pointMap).forEach(item => {
                if ((!Number.isFinite(item.lon) || !Number.isFinite(item.lat)) && item.title) {
                    const normalizedTitle = item.title.replace(/^R(C|I)?/i, '').trim();
                    const mapMatch = mapObjects.find(obj => {
                        const objName = normalizeText(obj.MAPOBJECTNAME, '').replace(
                            /^R(C|I)?/i, '').trim();
                        const titleName = normalizeText(obj.TITLE, '').replace(/^R(C|I)?/i, '')
                            .trim();
                        return objName === normalizedTitle || titleName === normalizedTitle ||
                            obj.MAPOBJECTNAME === item.title || obj.TITLE === item.title;
                    });

                    if (mapMatch && Number.isFinite(mapMatch.LONGITUDE) && Number.isFinite(mapMatch
                            .LATITUDE) && mapMatch.LONGITUDE > 100 && mapMatch.LONGITUDE < 130 &&
                        mapMatch.LATITUDE < 5 && mapMatch.LATITUDE > -10) {
                        item.lon = mapMatch.LONGITUDE;
                        item.lat = mapMatch.LATITUDE;
                    } else if (mapMatch && mapMatch.GEOPOINTS.length) {
                        const centerLon = mapMatch.GEOPOINTS.reduce((sum, p) => sum + p.lon, 0) /
                            mapMatch.GEOPOINTS.length;
                        const centerLat = mapMatch.GEOPOINTS.reduce((sum, p) => sum + p.lat, 0) /
                            mapMatch.GEOPOINTS.length;
                        item.lon = centerLon;
                        item.lat = centerLat;
                    }
                }
            });

            const mapPoints = Object.values(pointMap)
                .filter(item => Number.isFinite(item.lon) && Number.isFinite(item.lat))
                .map(item => {
                    const projected = projectPoint(item.lon, item.lat);
                    return {
                        x: projected.x,
                        y: projected.y,
                        size: Math.max(6, Math.min(12, 4 + (Math.sqrt(item.total || 0) / 2))),
                        title: item.title,
                        count: item.total
                    };
                });

            return {
                total,
                trendLabels: uniqueDates,
                trendValues: uniqueDates.map(date => trendMap[date] || 0),
                locations,
                layers,
                mapPoints
            };
        }

        function renderLocations(items) {
            let html = '';
            items.forEach(item => {
                html += '<div class="location-item">';
                html += '<div class="location-count">' + item.count + '</div>';
                html += '<div class="location-name">' + item.name + '</div>';
                html += '</div>';
            });
            locationList.innerHTML = html;
        }

        function renderMap(points) {
            let html = '';
            points.forEach(point => {
                html += '<span class="map-point" title="' + point.title + ' (' + point.count +
                    ')" style="left:' + point.x + '%;top:' + point.y + '%;width:' + point.size +
                    'px;height:' + point.size + 'px;"></span>';
            });
            mapPointsLayer.innerHTML = html;
        }

        function renderLegend(items) {
            let html = '';
            items.forEach(item => {
                html += '<div class="legend-item">';
                html += '<span class="legend-color" style="background:' + item.color + '"></span>';
                html += '<span>' + item.label + '</span>';
                html += '</div>';
            });
            mapLegend.innerHTML = html;
        }

        function renderStaticLabels(locations, points) {
            const labels = [mapLabel1, mapLabel2];
            const targets = locations.slice(0, 2);
            labels.forEach((labelEl, idx) => {
                const target = targets[idx];
                if (!target) {
                    labelEl.style.display = 'none';
                    return;
                }
                const point = points.find(p => p.title === target.name);
                if (!point) {
                    labelEl.style.display = 'none';
                    return;
                }
                labelEl.textContent = target.name;
                labelEl.style.left = 'calc(' + point.x + '% + 8px)';
                labelEl.style.top = 'calc(' + point.y + '% - 6px)';
                labelEl.style.display = 'block';
            });
        }

        function renderFaultList(filterKey) {
            const limit = Number(faultListLimitEl.value || 15);
            const source = filterKey === 'all' ?
                Object.entries(overviewErrorTotals) :
                Object.entries(overviewErrorTotals).filter(([name]) => name === filterKey);
            const rows = source.sort((a, b) => b[1] - a[1]).slice(0, limit);

            let html = '';
            rows.forEach((item, index) => {
                const isActive = item[0] === filterKey && filterKey !== 'all';
                html += '<div class="treemap-list-row' + (isActive ? ' active' : '') +
                    '" data-fault-name="' + item[0].replace(/"/g, '&quot;') + '">';
                html += '<div class="treemap-list-rank">' + (index + 1) + '</div>';
                html += '<div>' + item[0] + '</div>';
                html += '<div class="treemap-list-value">' + formatK(item[1]) + '</div>';
                html += '</div>';
            });
            treemapListEl.innerHTML = html;

            treemapListEl.querySelectorAll('[data-fault-name]').forEach(el => {
                el.addEventListener('click', function () {
                    updateDashboard(this.getAttribute('data-fault-name'));
                });
            });
        }

        function renderRankList(container, rows) {
            let html = '';
            rows.forEach((item, index) => {
                html += '<div class="rank-list-row">';
                html += '<div class="rank-no">' + (index + 1) + '</div>';
                html += '<div class="rank-name">' + item[0] + '</div>';
                html += '<div class="rank-value">' + formatK(item[1]) + '</div>';
                html += '</div>';
            });
            container.innerHTML = html;
        }

        function updateEquipmentLists(filterKey) {
            const sourceRows = filterKey === 'all' ?
                equipmentRows :
                equipmentRows.filter(row => row.Error_Message === filterKey);

            const faultLimit = Number(equipmentFaultTopLimitEl.value || 15);
            const unitLimit = Number(equipmentUnitTopLimitEl.value || 15);

            const faultTotals = {};
            sourceRows.forEach(row => {
                faultTotals[row.Error_Message] = (faultTotals[row.Error_Message] || 0) + row.Count;
            });
            const topFaults = Object.entries(faultTotals).sort((a, b) => b[1] - a[1]).slice(0, faultLimit);

            const unitTotals = {};
            sourceRows.forEach(row => {
                unitTotals[row.VHC_ID] = (unitTotals[row.VHC_ID] || 0) + row.Count;
            });
            const topUnits = Object.entries(unitTotals).sort((a, b) => b[1] - a[1]).slice(0, unitLimit);

            renderRankList(equipmentFaultListEl, topFaults);
            renderRankList(equipmentUnitListEl, topUnits);
        }

        function downloadChart(chartInstance, fileName) {
            const link = document.createElement('a');
            link.href = chartInstance.toBase64Image('image/png', 1);
            link.download = fileName;
            link.click();
        }

        function downloadElementAsSvg(elementId, fileName) {
            const element = document.getElementById(elementId);
            if (!element) return;
            const svg = element.querySelector('svg');
            if (!svg) return;
            const serializer = new XMLSerializer();
            const source = serializer.serializeToString(svg);
            const blob = new Blob([source], {
                type: 'image/svg+xml;charset=utf-8'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = fileName;
            link.click();
            URL.revokeObjectURL(url);
        }

        function updateDashboard(filterKey) {
            const currentFilter = filterKey || 'all';
            const data = aggregateOverview(currentFilter);

            locationTotal.textContent = formatK(data.total);
            renderLocations(data.locations);
            renderMap(data.mapPoints);
            renderLegend(buildLegendScale(data.mapPoints));
            renderStaticLabels(data.locations, data.mapPoints);
            renderFaultList(currentFilter);

            trendChart.data.labels = data.trendLabels;
            trendChart.data.datasets[0].data = data.trendValues;
            trendChart.update();

            pieChart.data.labels = Object.keys(data.layers).map(key => key + ': ' + data.layers[key] + '%');
            pieChart.data.datasets[0].data = Object.values(data.layers);
            pieChart.update();

            updateEquipmentLists(currentFilter);
            window.__currentDashboardFilter = currentFilter;
        }

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove(
                    'active'));
                document.querySelectorAll('.tab-pane-custom').forEach(el => el.classList.remove(
                    'active'));
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });

        faultListLimitEl.addEventListener('change', function () {
            renderFaultList(window.__currentDashboardFilter || 'all');
        });

        equipmentFaultTopLimitEl.addEventListener('change', function () {
            updateEquipmentLists(window.__currentDashboardFilter || 'all');
        });

        equipmentUnitTopLimitEl.addEventListener('change', function () {
            updateEquipmentLists(window.__currentDashboardFilter || 'all');
        });

        document.querySelectorAll('[data-download-chart="trendChart"]').forEach(btn => {
            btn.addEventListener('click', function () {
                downloadChart(trendChart, 'trend-per-day.png');
            });
        });

        document.querySelectorAll('[data-download-chart="pieChart"]').forEach(btn => {
            btn.addEventListener('click', function () {
                downloadChart(pieChart, 'pie-layer-distribution.png');
            });
        });

        document.querySelectorAll('[data-download-target="mapBox"]').forEach(btn => {
            btn.addEventListener('click', function () {
                downloadElementAsSvg('mapBox', 'map-distribution.svg');
            });
        });

        renderBaseMap();
        updateDashboard('all');
    });

</script>

@include('layout.footer')
