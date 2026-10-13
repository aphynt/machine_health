<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeriodicSeriesController extends Controller
{
    private const MAX_VEHICLES = 4;
    private const MAX_PARAMETERS = 4;
    private const TARGET_POINTS_PER_VEHICLE = 200;

    // Only these trusted columns can be interpolated into the SQL query.
    private const PARAMETERS = [
        'VHM_ENGSPEED'           => 'Engine Speed',
        'VHM_ENGOILPRESS'        => 'Engine Oil Pressure',
        'VHM_ENGOILTEMP'         => 'Engine Oil Temperature',
        'VHM_COOLTEMP'           => 'Coolant Temperature',
        'VHM_AMBIENTAIRTEMP'     => 'Ambient Air Temperature',
        'VHM_VEHICLESPEED'       => 'Vehicle Speed',
        'VHM_FUELRATE01L'        => 'Fuel Rate',
        'VHM_FUELINJECTION'      => 'Fuel Injection',
        'VHM_BLOWBYPRESS'        => 'Blow-by Pressure',
        'VHM_BLOWBYPRESSPM'      => 'Blow-by Pressure PM',
        'VHM_EXHTEMPLBF'         => 'Exhaust Temp LBF',
        'VHM_EXHTEMPLBR'         => 'Exhaust Temp LBR',
        'VHM_EXHTEMPRBF'         => 'Exhaust Temp RBF',
        'VHM_EXHTEMPRBR'         => 'Exhaust Temp RBR',
        'VHM_EXHTEMPF'           => 'Exhaust Temp Front',
        'VHM_EXHTEMPR'           => 'Exhaust Temp Rear',
        'VHM_TMOILTEMP'          => 'TM Oil Temperature',
        'VHM_TMOUTSPEED'         => 'TM Output Speed',
        'VHM_TCOILTEMP'          => 'Torque Converter Oil Temp',
        'VHM_FBRAKEOILPRESS'     => 'Front Brake Oil Pressure',
        'VHM_RBRAKEOILPRESS'     => 'Rear Brake Oil Pressure',
        'VHM_ACCELPOS'           => 'Accelerator Position',
        'VHM_HOISTLEVERPOS'      => 'Hoist Lever Position',
        'VHM_SHIFTINDICATOR'     => 'Shift Indicator',
        'VHM_RETARDERFOILTEMP'   => 'Front Retarder Oil Temp',
        'VHM_RETARDERROILTEMP'   => 'Rear Retarder Oil Temp',
        'VHM_RETARDERPOS'        => 'Retarder Position',
        'VHM_FOOTBRAKEPOS'       => 'Foot Brake Position',
        'VHM_GRADIENTDEG'        => 'Gradient Degree',
        'VHM_LIVEWEIGHTTON'      => 'Live Weight',
        'VHM_PUMPFPRESSMPA'      => 'Front Pump Pressure',
        'VHM_PUMPRPRESSMPA'      => 'Rear Pump Pressure',
        'VHM_SWINGPRESSMPA'      => 'Swing Pressure',
        'VHM_GPUMP1PRESS'        => 'G-Pump 1 Pressure',
        'VHM_GPUMP2PRESS'        => 'G-Pump 2 Pressure',
        'VHM_HYDOILTEMP'         => 'Hydraulic Oil Temperature',
        'VHM_PTOTEMP'            => 'PTO Temperature',
        'VHM_BOOSTPRESS'         => 'Boost Pressure',
        'VHM_PUMP1FPRESS'        => 'Pump 1 Front Pressure',
        'VHM_PUMP1RPRESS'        => 'Pump 1 Rear Pressure',
        'VHM_PUMP2FPRESS'        => 'Pump 2 Front Pressure',
        'VHM_PUMP2RPRESS'        => 'Pump 2 Rear Pressure',
        'VHM_FANPUMPPRESS'       => 'Fan Pump Pressure',
        'VHM_FANPUMPFPRESS'      => 'Fan Pump Front Pressure',
        'VHM_FANPUMPRPRESS'      => 'Fan Pump Rear Pressure',
        'VHM_SUSPRESSFRP'        => 'Suspension Front Right',
        'VHM_SUSPRESSFLP'        => 'Suspension Front Left',
        'VHM_SUSPRESSRRP'        => 'Suspension Rear Right',
        'VHM_SUSPRESSRLP'        => 'Suspension Rear Left',
        'VHM_ECOON'              => 'ECO Mode',
    ];

    public function index(): View
    {
        $vehicles = $this->masterVehicles();
        $byModel = [];
        foreach ($vehicles as $vehicle) {
            $model = $vehicle['model'];
            $byModel[$model][] = [
                'id' => $vehicle['id'],
                'active' => $vehicle['active'],
            ];
        }

        ksort($byModel, SORT_NATURAL);
        $models = array_keys($byModel);
        $defaultModel = in_array('KOM785', $models, true) ? 'KOM785' : ($models[0] ?? '');
        $defaultUnit = collect($byModel[$defaultModel] ?? [])
            ->firstWhere('id', 'HD1115');
        $defaultUnit = $defaultUnit['id'] ?? ($byModel[$defaultModel][0]['id'] ?? '');

        return view('periodic.series', [
            'equipmentByModel' => $byModel,
            'defaultModel' => $defaultModel,
            'defaultUnit' => $defaultUnit,
            'parameters' => self::PARAMETERS,
            'defaultParameters' => ['VHM_BLOWBYPRESS', 'VHM_BOOSTPRESS', 'VHM_COOLTEMP'],
            'defaultStart' => now()->subHours(2)->startOfHour()->format('Y-m-d\TH:i'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $input = $request->validate([
            'model'        => ['required', 'string', 'max:60'],
            'vehicles'     => ['required', 'array', 'min:1', 'max:' . self::MAX_VEHICLES],
            'vehicles.*'   => ['required', 'string', 'max:60', 'distinct'],
            'parameters'   => ['required', 'array', 'min:1', 'max:' . self::MAX_PARAMETERS],
            'parameters.*' => ['required', 'string', 'max:80', 'distinct'],
            'start'        => ['required', 'date_format:Y-m-d\TH:i'],
            'hours'        => ['required', 'integer', 'between:1,24'],
        ]);

        $units = array_values($input['vehicles']);
        $params = array_values($input['parameters']);
        foreach ($params as $param) {
            if (!array_key_exists($param, self::PARAMETERS)) {
                return response()->json(['message' => 'Parameter tidak terdaftar: ' . $param], 422);
            }
        }

        // Verify units belong to the selected model; do not expose arbitrary unit IDs.
        $allowed = collect($this->masterVehicles())
            ->filter(fn ($item) => $item['model'] === $input['model'])
            ->pluck('id')->all();
        foreach ($units as $unit) {
            if (!in_array($unit, $allowed, true)) {
                return response()->json(['message' => "Equipment {$unit} tidak terdaftar pada model ini."], 422);
            }
        }

        $from = Carbon::createFromFormat('!Y-m-d\TH:i', $input['start']);
        $until = $from->copy()->addHours((int) $input['hours']);
        $periodSeconds = (int) $input['hours'] * 3600;
        $bucketSeconds = max(1, (int) ceil($periodSeconds / self::TARGET_POINTS_PER_VEHICLE));
        $slots = implode(', ', array_fill(0, count($units), '?'));
        $cols = implode(', ', array_map(fn ($p) => '[' . $p . ']', $params));
        $selectCols = '[VHC_ID], [OPR_REPORTTIME], [VHM_TIMESTAMP], ' . $cols;
        $fromTime = $from->format('Y-m-d H:i:s');
        $untilTime = $until->format('Y-m-d H:i:s');

        // Time filter and equipment filter are applied before ranking/sampling.
        // Latest row in each time bucket, per unit. Never SELECT * from the view.
        $sql = "WITH Sampled AS (
            SELECT {$selectCols},
                ROW_NUMBER() OVER (
                    PARTITION BY [VHC_ID], DATEDIFF(SECOND, ?, [OPR_REPORTTIME]) / ?
                    ORDER BY [OPR_REPORTTIME] DESC
                ) AS [row_num]
            FROM [FOCUS_REPORTING].[dbo].[VW_TRK_LOG]
            WHERE [VHC_ID] IN ({$slots})
              AND [OPR_REPORTTIME] >= ?
              AND [OPR_REPORTTIME] < ?
              AND [VHM_TIMESTAMP] >= '20000101'
        )
        SELECT {$selectCols} FROM Sampled WHERE [row_num] = 1
        ORDER BY [VHC_ID], [OPR_REPORTTIME] ASC";

        $rows = DB::connection('sqlsrv')->select($sql, array_merge(
            [$fromTime, $bucketSeconds], $units, [$fromTime, $untilTime]
        ));

        $results = [];
        $seen = [];
        foreach ($units as $unit) {
            $results[$unit] = [];
            $seen[$unit] = [];
        }

        foreach ($rows as $row) {
            $unit = (string) $row->VHC_ID;
            if (!array_key_exists($unit, $results)) continue;

            $event = $this->formatTime($row->VHM_TIMESTAMP);
            // Skip repeated/stale sensor snapshots and the 1899 sentinel.
            if (!$event || isset($seen[$unit][$event])) continue;
            $seen[$unit][$event] = true;

            $values = [];
            foreach ($params as $param) {
                $value = $row->{$param};
                $values[$param] = ($value !== null && is_numeric($value) && is_finite((float) $value))
                    ? (float) $value : null;
            }
            $results[$unit][] = [
                'time' => $this->formatTime($row->OPR_REPORTTIME),
                'sensor_time' => $event,
                'values' => $values,
            ];
        }

        return response()->json([
            'source' => 'FOCUS_REPORTING.dbo.VW_TRK_LOG',
            'model' => $input['model'],
            'vehicles' => $units,
            'parameters' => collect($params)->mapWithKeys(
                fn ($param) => [$param => self::PARAMETERS[$param]]
            )->all(),
            'from' => $from->format('Y-m-d\TH:i:s'),
            'until' => $until->format('Y-m-d\TH:i:s'),
            'bucket_seconds' => $bucketSeconds,
            'series' => $results,
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Lightweight vehicle dropdown source: master only, not the large reporting view. */
    private function masterVehicles(): array
    {
        return Cache::remember('periodic_series_vehicles_v1', 120, function () {
            $rows = DB::connection('sqlsrv')
                ->table('FOCUS.dbo.FLT_VEHICLE')
                ->select(['VHC_ID', 'EQU_GROUPID', 'EQU_TYPEID', 'VHC_ACTIVE'])
                ->whereNotNull('VHC_ID')
                ->orderBy('VHC_ID')
                ->get();

            $items = [];
            foreach ($rows as $row) {
                $model = trim((string) ($row->EQU_GROUPID ?: $row->EQU_TYPEID ?: ''));
                $id = trim((string) $row->VHC_ID);
                if ($model === '' || $id === '') continue;
                $items[] = [
                    'model' => $model,
                    'id' => $id,
                    'active' => (int) $row->VHC_ACTIVE === 1,
                ];
            }
            return $items;
        });
    }

    private function formatTime($value): ?string
    {
        if ($value === null || $value === '') return null;
        try {
            $date = Carbon::parse($value);
            return $date->year >= 2000 ? $date->format('Y-m-d\TH:i:s.v') : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }
}
