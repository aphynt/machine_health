<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use InvalidArgumentException;
use Throwable;

class OperatorInducedController extends Controller
{
    public function index(): View
    {
        return view('operator-induced.index', [
            'defaultStart' => now()->subDay()->toDateString(),
            'defaultStop' => now()->toDateString(),
            'configured' => count($this->availableSources()) > 0,
            'configurationMessage' => count($this->availableSources()) > 0
                ? '' : 'Atur source Caterpillar atau Komatsu pada config/operator_induced.php.',
            'coverageMessage' => '',
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date_format:Y-m-d'],
            'stop_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'brand' => ['nullable', 'in:all,Caterpillar,Komatsu'],
            'page' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'pagination_only' => ['nullable', 'in:0,1'],
        ]);

        $start = Carbon::createFromFormat('!Y-m-d', $validated['start_date']);
        $stop = Carbon::createFromFormat('!Y-m-d', $validated['stop_date']);
        $maxDays = max(1, (int) config('operator_induced.max_range_days', 31));
        if ($start->diffInDays($stop) >= $maxDays) {
            return response()->json(['message' => 'Rentang maksimal ' . $maxDays . ' hari termasuk tanggal awal dan akhir.'], 422);
        }

        $brand = $validated['brand'] ?? 'all';
        if (!count($this->availableSources($brand))) {
            return response()->json(['message' => 'View event untuk merek yang dipilih belum dikonfigurasi.'], 422);
        }

        $startAt = $start->format('Y-m-d 00:00:00');
        $stopBefore = $stop->copy()->addDay()->format('Y-m-d 00:00:00');
        $pageSize = max(10, min(200, (int) config('operator_induced.page_size', 100)));
        $mapLimit = max(10, min(3000, (int) config('operator_induced.max_map_points', 1200)));
        $page = (int) ($validated['page'] ?? 1);

        try {
            $base = $this->baseQuery($startAt, $stopBefore, $brand);

            $records = (clone $base)
                ->select([
                    'Manufacturer', 'Model', 'Name', 'Fault_Code', 'Fault_Date',
                    'GPS_SPEED', 'VSA_STATUSID', 'OPR_NRP', 'PERSONALNAME',
                    'GPS_LON', 'GPS_LAT', 'LOC_NAME', 'Occurrences',
                ])
                ->orderByDesc('Fault_Date')->orderBy('Manufacturer')->orderBy('Name')->orderBy('Fault_Code')
                ->offset(($page - 1) * $pageSize)
                ->limit($pageSize + 1)->get();

            $hasMore = $records->count() > $pageSize;
            $resume = $records->take($pageSize)->map(function ($r) {
                return [
                    'brand' => $r->Manufacturer,
                    'model' => $r->Model,
                    'equipment' => $r->Name,
                    'fault' => $r->Fault_Code,
                    'date_time' => $this->formatDate($r->Fault_Date),
                    'speed' => is_numeric($r->GPS_SPEED) ? round((float) $r->GPS_SPEED, 2) : null,
                    'status' => $r->VSA_STATUSID,
                    'nik' => $r->OPR_NRP,
                    'fullname' => $r->PERSONALNAME,
                    'occurrences' => is_numeric($r->Occurrences) ? (int) $r->Occurrences : null,
                    'lon' => $this->coordinate($r->GPS_LON, -180, 180),
                    'lat' => $this->coordinate($r->GPS_LAT, -90, 90),
                    'location' => $r->LOC_NAME,
                ];
            })->values();

            if (($validated['pagination_only'] ?? '0') === '1') {
                $personal = $this->lookupPersonalNames($resume);
                return response()->json([
                    'resume' => $this->withPersonalNames($resume, $personal, 'fullname'),
                    'pagination' => ['page' => $page, 'per_page' => $pageSize, 'has_more' => $hasMore],
                ]);
            }

            $trend = (clone $base)
                ->selectRaw('CONVERT(date, [Fault_Date]) AS [date]')
                ->selectRaw('COUNT_BIG(*) AS [total]')
                ->groupByRaw('CONVERT(date, [Fault_Date])')
                ->orderByRaw('CONVERT(date, [Fault_Date])')
                ->get()->map(fn ($r) => [
                    'date' => $this->formatDate($r->date, 'Y-m-d'),
                    'total' => (int) $r->total,
                ])->values();

            $operatorRank = (clone $base)
                ->select('OPR_NRP')
                ->selectRaw('COUNT_BIG(*) AS [total]')
                ->groupBy('OPR_NRP')
                ->orderByRaw('COUNT_BIG(*) DESC')
                ->limit(50)->get()->map(fn ($r) => [
                    'nik' => $r->OPR_NRP,
                    'name' => null,
                    'total' => (int) $r->total,
                ])->values();

            $equipmentRank = (clone $base)
                ->select(['Manufacturer', 'Name', 'Model'])
                ->selectRaw('COUNT_BIG(*) AS [total]')
                ->groupBy('Manufacturer', 'Name', 'Model')
                ->orderByRaw('COUNT_BIG(*) DESC')
                ->limit(50)->get()->map(fn ($r) => [
                    'brand' => $r->Manufacturer,
                    'equipment' => $r->Name,
                    'model' => $r->Model,
                    'total' => (int) $r->total,
                ])->values();

            $map = (clone $base)
                ->whereNotNull('GPS_LON')->whereNotNull('GPS_LAT')
                ->whereBetween('GPS_LON', [-180, 180])->whereBetween('GPS_LAT', [-90, 90])
                ->where('GPS_LON', '<>', 0)->where('GPS_LAT', '<>', 0)
                ->select([
                    'Manufacturer', 'Name', 'Model', 'Fault_Code', 'Fault_Date',
                    'GPS_LON', 'GPS_LAT', 'OPR_NRP', 'PERSONALNAME', 'LOC_NAME',
                ])
                ->orderByDesc('Fault_Date')->limit($mapLimit)->get()
                ->map(fn ($r) => [
                    'brand' => $r->Manufacturer,
                    'equipment' => $r->Name,
                    'model' => $r->Model,
                    'fault' => $r->Fault_Code,
                    'date_time' => $this->formatDate($r->Fault_Date),
                    'lon' => (float) $r->GPS_LON,
                    'lat' => (float) $r->GPS_LAT,
                    'nik' => $r->OPR_NRP,
                    'fullname' => $r->PERSONALNAME,
                    'location' => $r->LOC_NAME,
                ])->values();

            $personal = $this->lookupPersonalNames($resume, $operatorRank, $map);
            $resume = $this->withPersonalNames($resume, $personal, 'fullname');
            $operatorRank = $this->withPersonalNames($operatorRank, $personal, 'name');
            $map = $this->withPersonalNames($map, $personal, 'fullname');

            return response()->json([
                'summary' => [
                    'total' => $trend->sum('total'),
                    'operator_count_shown' => $operatorRank->count(),
                    'equipment_count_shown' => $equipmentRank->count(),
                    'map_points_shown' => $map->count(),
                    'map_limit' => $mapLimit,
                ],
                'resume' => $resume,
                'pagination' => ['page' => $page, 'per_page' => $pageSize, 'has_more' => $hasMore],
                'trend' => $trend,
                'operators' => $operatorRank,
                'equipment' => $equipmentRank,
                'map' => $map,
                'meta' => [
                    'sources' => array_column($this->availableSources($brand), 'brand'),
                    'start_date' => $validated['start_date'],
                    'stop_date' => $validated['stop_date'],
                    'brand' => $brand,
                    'coverage_note' => 'Komatsu memakai SN sebagai identitas unit dan tidak menyediakan model pada view. Nama operator diambil dari FOCUS.dbo.PRS_PERSONAL berdasarkan NRP.',
                ],
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'message' => 'Gagal memuat data. Periksa view CAT/Komatsu, database tabel referensi, kolom kode, dan storage/logs/laravel.log.',
            ], 500);
        }
    }

    public function mapLayers(): JsonResponse
    {
        try {
            $source = $this->checkedIdentifier((string) config('operator_induced.map_source', 'FOCUS.dbo.MAP_OBJECT'));
            $connection = DB::connection((string) config('operator_induced.map_connection', 'sqlsrv'));
            $limit = max(100, min(8000, (int) config('operator_induced.map_object_limit', 3000)));
            $supported = [
                'MAP_ROAD', 'MAP_AREA', 'MAP_FRONT_BOUNDARY', 'MAP_DISPOSAL_BOUNDARY',
                'MAP_FRONT', 'MAP_DISPOSAL', 'MAP_CALLPOINT',
            ];
            $rows = $connection->table($source)
                ->select(['MAPOBJECTID', 'LAYERNAME', 'MAPOBJECTNAME', 'MAPOBJECTTYPE',
                          'LONGITUDE', 'LATITUDE', 'GEOPOINTS'])
                ->where('VISIBLE', 1)
                ->whereIn('LAYERNAME', $supported)
                ->orderByRaw("CASE WHEN [LAYERNAME] = 'MAP_ROAD' THEN 0 "
                    . "WHEN [LAYERNAME] IN ('MAP_AREA','MAP_FRONT_BOUNDARY','MAP_DISPOSAL_BOUNDARY') THEN 1 "
                    . "ELSE 2 END")
                ->orderBy('MAPOBJECTID')
                ->limit($limit + 1)
                ->get();

            $truncated = $rows->count() > $limit;
            $features = [];
            foreach ($rows->take($limit) as $row) {
                $layer = strtoupper(trim((string) $row->LAYERNAME));
                $feature = [
                    'id' => $row->MAPOBJECTID,
                    'name' => (string) $row->MAPOBJECTNAME,
                    'layer' => $layer,
                ];
                if (in_array($layer, ['MAP_ROAD', 'MAP_AREA', 'MAP_FRONT_BOUNDARY', 'MAP_DISPOSAL_BOUNDARY'], true)) {
                    $vertices = $this->parseGeoPoints((string) ($row->GEOPOINTS ?? ''));
                    $polygon = $layer !== 'MAP_ROAD';
                    if (count($vertices) < ($polygon ? 3 : 2)) {
                        continue;
                    }
                    $feature['geometry'] = $polygon ? 'polygon' : 'polyline';
                    $feature['coordinates'] = $vertices;
                } else {
                    $lat = $this->coordinate($row->LATITUDE, -90, 90);
                    $lon = $this->coordinate($row->LONGITUDE, -180, 180);
                    if ($lat === null || $lon === null) {
                        continue;
                    }
                    $feature['geometry'] = 'point';
                    $feature['lat'] = $lat;
                    $feature['lon'] = $lon;
                }
                $features[] = $feature;
            }
            return response()->json([
                'features' => $features,
                'meta' => [
                    'count' => count($features),
                    'truncated' => $truncated,
                    'limit' => $limit,
                    'source' => $source,
                    'coordinate_system' => 'WGS84 longitude / latitude (EPSG:4326)',
                ],
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'message' => 'Peta site tidak bisa dimuat. Periksa izin SELECT FOCUS.dbo.MAP_OBJECT dan pengaturan MAP connection pada config/operator_induced.php.',
            ], 500);
        }
    }

    private function parseGeoPoints(string $geometry): array
    {
        $pattern = '/\\[\\s*([+-]?(?:\\d+(?:\\.\\d*)?|\\.\\d+)(?:[eE][+-]?\\d+)?)\\s*,\\s*([+-]?(?:\\d+(?:\\.\\d*)?|\\.\\d+)(?:[eE][+-]?\\d+)?)\\s*\\]/';
        if (!preg_match_all($pattern, $geometry, $matches, PREG_SET_ORDER)) {
            return [];
        }
        $maxVertices = max(10, min(3000, (int) config('operator_induced.map_max_vertices', 800)));
        $vertices = [];
        foreach ($matches as $pair) {
            $lon = $this->coordinate($pair[1], -180, 180);
            $lat = $this->coordinate($pair[2], -90, 90);
            if ($lon === null || $lat === null) {
                continue;
            }
            $vertices[] = [$lat, $lon];
            if (count($vertices) >= $maxVertices) {
                break;
            }
        }
        return $vertices;
    }

    private function baseQuery(string $startAt, string $stopBefore, string $brand): Builder
    {
        $connection = DB::connection((string) config('operator_induced.connection', 'sqlsrv'));
        $union = null;

        foreach ($this->availableSources($brand) as $source) {
            $view = $this->checkedIdentifier($source['view']);
            $reference = $this->checkedIdentifier($source['reference']);
            $isCat = $source['brand'] === 'Caterpillar';

            $events = $connection->table($view . ' as F');
            if ($isCat) {
                $events->selectRaw(<<<'SQL'
                    CAST('Caterpillar' AS NVARCHAR(20)) AS [Manufacturer],
                    CONVERT(NVARCHAR(100), [F].[Model]) AS [Model],
                    CONVERT(NVARCHAR(100), [F].[Name]) AS [Name],
                    CONVERT(NVARCHAR(100), [F].[Fault_Code]) AS [Fault_Code],
                    [F].[Fault_Date] AS [Fault_Date],
                    [F].[GPS_SPEED] AS [GPS_SPEED],
                    CONVERT(NVARCHAR(50), [F].[VSA_STATUSID]) AS [VSA_STATUSID],
                    CONVERT(NVARCHAR(50), [F].[OPR_NRP]) AS [OPR_NRP],
                    CAST(NULL AS NVARCHAR(200)) AS [PERSONALNAME],
                    [F].[GPS_LON] AS [GPS_LON],
                    [F].[GPS_LAT] AS [GPS_LAT],
                    CONVERT(NVARCHAR(255), [F].[LOC_NAME]) AS [LOC_NAME],
                    TRY_CONVERT(BIGINT, [F].[Occurrences]) AS [Occurrences]
                SQL);
                $events->where('F.Fault_Date', '>=', $startAt)
                    ->where('F.Fault_Date', '<', $stopBefore);
                $faultColumn = 'Fault_Code';
            } else {
                $events->selectRaw(<<<'SQL'
                    CAST('Komatsu' AS NVARCHAR(20)) AS [Manufacturer],
                    CAST(NULL AS NVARCHAR(100)) AS [Model],
                    CONVERT(NVARCHAR(100), [F].[SN]) AS [Name],
                    CONVERT(NVARCHAR(500), [F].[Error_Message]) AS [Fault_Code],
                    COALESCE(
                        CASE WHEN TRY_CONVERT(DATETIME2, [F].[From_Date_Time]) >= '2000-01-01'
                            THEN TRY_CONVERT(DATETIME2, [F].[From_Date_Time]) END,
                        TRY_CONVERT(DATETIME2, [F].[OPR_REPORTTIME])
                    ) AS [Fault_Date],
                    [F].[GPS_SPEED] AS [GPS_SPEED],
                    CONVERT(NVARCHAR(50), [F].[VSA_STATUSID]) AS [VSA_STATUSID],
                    CONVERT(NVARCHAR(50), [F].[OPR_NRP]) AS [OPR_NRP],
                    CAST(NULL AS NVARCHAR(200)) AS [PERSONALNAME],
                    [F].[GPS_LON] AS [GPS_LON],
                    [F].[GPS_LAT] AS [GPS_LAT],
                    CONVERT(NVARCHAR(255), [F].[LOC_NAME]) AS [LOC_NAME],
                    TRY_CONVERT(BIGINT, [F].[Count]) AS [Occurrences]
                SQL);
                $events->where(function (Builder $q) use ($startAt, $stopBefore) {
                    $q->where(function (Builder $a) use ($startAt, $stopBefore) {
                        $a->where('F.From_Date_Time', '>=', $startAt)
                            ->where('F.From_Date_Time', '<', $stopBefore);
                    })->orWhere(function (Builder $b) use ($startAt, $stopBefore) {
                        $b->where(function (Builder $c) {
                            $c->whereNull('F.From_Date_Time')
                                ->orWhere('F.From_Date_Time', '<', '2000-01-01');
                        })->where('F.OPR_REPORTTIME', '>=', $startAt)
                          ->where('F.OPR_REPORTTIME', '<', $stopBefore);
                    });
                });
                $faultColumn = 'Code';
            }

            $events->whereExists(function (Builder $q) use ($reference, $faultColumn) {
                $q->selectRaw('1')->from($reference . ' as R')
                    ->whereRaw(
                        'LTRIM(RTRIM(CONVERT(NVARCHAR(100), [R].[CODE]))) COLLATE DATABASE_DEFAULT = '
                        . 'LTRIM(RTRIM(CONVERT(NVARCHAR(100), [F].[' . $faultColumn . ']))) COLLATE DATABASE_DEFAULT'
                    )
                    ->whereRaw(
                        'LTRIM(RTRIM(CONVERT(NVARCHAR(20), [R].[OPERATOR_INDUCED]))) = ?',
                        [(string) config('operator_induced.induced_value', '1')]
                    );
            });

            if ((bool) config('operator_induced.require_operator', true)) {
                $events->whereNotNull('F.OPR_NRP')
                    ->whereRaw("LTRIM(RTRIM(CONVERT(NVARCHAR(50), [F].[OPR_NRP]))) <> ''");
            }

            if ($union === null) {
                $union = $events;
            } else {
                $union->unionAll($events);
            }
        }

        if ($union === null) {
            throw new InvalidArgumentException('Belum ada sumber view fault yang dikonfigurasi.');
        }

        return $connection->query()->fromSub($union, 'Events');
    }

    private function lookupPersonalNames(...$groups): array
    {
        $nrps = collect($groups)
            ->flatMap(fn ($rows) => collect($rows)->pluck('nik'))
            ->filter(fn ($nik) => $nik !== null && trim((string) $nik) !== '')
            ->map(fn ($nik) => trim((string) $nik))
            ->unique()
            ->values();

        if ($nrps->isEmpty()) {
            return [];
        }

        $source = $this->checkedIdentifier(
            (string) config('operator_induced.operator_source', 'FOCUS.dbo.PRS_PERSONAL')
        );
        $connection = DB::connection(
            (string) config('operator_induced.operator_connection', 'sqlsrv')
        );
        $names = [];

        foreach ($nrps->chunk(400) as $chunk) {
            $rows = $connection->table($source)
                ->select(['NRP', 'PERSONALNAME'])
                ->whereIn('NRP', $chunk->all())
                ->orderByDesc('ACTIVE')
                ->orderByDesc('ID')
                ->get();

            foreach ($rows as $person) {
                $nik = trim((string) ($person->NRP ?? ''));
                $name = trim((string) ($person->PERSONALNAME ?? ''));
                if ($nik !== '' && $name !== '' && !array_key_exists($nik, $names)) {
                    $names[$nik] = $name;
                }
            }
        }

        return $names;
    }

    private function withPersonalNames($rows, array $names, string $field)
    {
        return $rows->map(function (array $row) use ($names, $field) {
            $nik = trim((string) ($row['nik'] ?? ''));
            $row[$field] = $names[$nik] ?? null;
            return $row;
        })->values();
    }

    private function availableSources(string $brand = 'all'): array
    {
        $sources = [];
        foreach ([
            ['brand' => 'Caterpillar', 'view' => 'cat_source', 'reference' => 'cat_reference'],
            ['brand' => 'Komatsu', 'view' => 'komatsu_source', 'reference' => 'komatsu_reference'],
        ] as $candidate) {
            if ($brand !== 'all' && $brand !== $candidate['brand']) {
                continue;
            }
            $view = trim((string) config('operator_induced.' . $candidate['view'], ''));
            $reference = trim((string) config('operator_induced.' . $candidate['reference'], ''));
            if ($view !== '' && $reference !== '') {
                $sources[] = ['brand' => $candidate['brand'], 'view' => $view, 'reference' => $reference];
            }
        }
        return $sources;
    }

    private function checkedIdentifier(string $identifier): string
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*){0,2}$/D', $identifier)) {
            throw new InvalidArgumentException('Nama source database tidak valid.');
        }
        return $identifier;
    }

    private function formatDate($value, string $format = 'Y-m-d H:i:s'): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Carbon::parse($value)->format($format);
        } catch (Throwable $e) {
            return null;
        }
    }

    private function coordinate($value, float $min, float $max): ?float
    {
        if (!is_numeric($value)) {
            return null;
        }
        $number = (float) $value;
        return ($number >= $min && $number <= $max && $number != 0.0) ? $number : null;
    }
}
