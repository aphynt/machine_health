<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RealtimeSeriesController extends Controller
{
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

    private const DEFAULT_PARAMETERS = [
        'VHM_BLOWBYPRESS',
        'VHM_BOOSTPRESS',
        'VHM_COOLTEMP',
    ];

    public function index(Request $request): View
    {
        $units = $this->activeUnits();
        $models = $units->pluck('model')->unique()->sort()->values()->all();

        $requestedModel = trim((string) $request->query('model', ''));
        $selectedModel = in_array($requestedModel, $models, true)
            ? $requestedModel : (in_array('KOM785', $models, true) ? 'KOM785' : ($models[0] ?? ''));

        $equipment = $units->where('model', $selectedModel)
            ->pluck('equipment')->unique()->sort()->values()->all();

        $requestedEquipment = trim((string) $request->query('equipment', ''));
        $selectedEquipment = in_array($requestedEquipment, $equipment, true)
            ? $requestedEquipment : (in_array('HD1115', $equipment, true) ? 'HD1115' : ($equipment[0] ?? ''));

        return view('realtime.series', [
            'models'            => $models,
            'equipment'         => $equipment,
            'selectedModel'     => $selectedModel,
            'selectedEquipment' => $selectedEquipment,
            'parameterOptions'  => self::PARAMETERS,
            'defaultParameters' => self::DEFAULT_PARAMETERS,
        ]);
    }

    public function equipment(Request $request): JsonResponse
    {
        $input = $request->validate(['model' => ['required', 'string', 'max:60']]);

        $equipment = $this->activeUnits()
            ->where('model', $input['model'])
            ->pluck('equipment')->unique()->sort()->values()->all();

        return response()->json(['equipment' => $equipment]);
    }

    public function data(Request $request): JsonResponse
    {
        $input = $request->validate([
            'model'        => ['required', 'string', 'max:60'],
            'equipment'    => ['required', 'string', 'max:60'],
            'parameters'   => ['required', 'array', 'min:1', 'max:4'],
            'parameters.*' => ['required', 'string', 'max:80'],
        ]);

        $parameters = array_values(array_unique($input['parameters']));
        foreach ($parameters as $parameter) {
            if (!array_key_exists($parameter, self::PARAMETERS)) {
                return response()->json(['message' => 'Parameter tidak diizinkan.'], 422);
            }
        }

        $columns = [
            'VHC_ID', 'VHC_ACTIVE', 'EQU_GROUPID', 'EQU_TYPEID',
            'OPR_REPORTTIME', 'OPR_SHIFTDATE', 'OPR_SHIFTNO',
            'OPR_NRP', 'OPR_NAME', 'VSA_GROUPID', 'VSA_STATUSID',
            'VSA_ACTIVITYID', 'LOC_NAME', 'ASG_LOADERID',
            'ASG_LOC_NAME', 'ASG_TO_LOC_NAME', 'GPS_LON',
            'GPS_LAT', 'GPS_TIMESTAMP', 'GPS_SPEED', 'VHM_TIMESTAMP',
            DB::raw('DATEDIFF(SECOND, OPR_REPORTTIME, GETDATE()) AS DATA_AGE_SECONDS'),
            DB::raw("CASE WHEN VHM_TIMESTAMP >= '20000101' THEN DATEDIFF(SECOND, VHM_TIMESTAMP, GETDATE()) ELSE NULL END AS VHM_AGE_SECONDS"),
        ];

        $row = DB::connection('sqlsrv')
            ->table('FOCUS.dbo.FLT_VEHICLE')
            ->select(array_merge($columns, $parameters))
            ->where('VHC_ID', $input['equipment'])
            ->first();

        $model = $row ? $this->modelName($row) : null;
        if (!$row || $model !== $input['model']) {
            return response()->json([
                'model'       => $input['model'],
                'equipment'   => $input['equipment'],
                'sample'      => null,
                'sensors'     => [],
                'latest'      => null,
                'gps'         => null,
            ])->header('Cache-Control', 'no-store');
        }

        $vhmTime = $this->dateString($row->VHM_TIMESTAMP);
        $values = [];
        $sensors = [];
        foreach ($parameters as $parameter) {
            $value = $this->sensorNumber($row->{$parameter});
            $values[$parameter] = $value;
            $sensors[$parameter] = [
                'label'        => self::PARAMETERS[$parameter],
                'value'        => $value,
                'measured_at'  => $vhmTime,
                'valid_time'   => $vhmTime !== null,
                'all_zero'     => $value === 0.0,
            ];
        }

        $gps = $this->validCoordinates($row->GPS_LAT, $row->GPS_LON)
            ? [
                'lat'         => (float) $row->GPS_LAT,
                'lon'         => (float) $row->GPS_LON,
                'gps_time'    => $this->dateString($row->GPS_TIMESTAMP),
                'report_time' => $this->dateString($row->OPR_REPORTTIME),
            ] : null;

        $from = trim((string) ($row->ASG_LOC_NAME ?? ''));
        $to = trim((string) ($row->ASG_TO_LOC_NAME ?? ''));

        return response()->json([
            'model'       => $model,
            'equipment'   => $row->VHC_ID,
            'sample'      => $vhmTime ? [
                'time'   => $vhmTime,
                'values' => $values,
            ] : null,
            'sensors'     => $sensors,
            'gps'         => $gps,
            'latest'      => [
                'report_time'      => $this->dateString($row->OPR_REPORTTIME),
                'data_age_seconds' => $row->DATA_AGE_SECONDS === null ? null : (int) $row->DATA_AGE_SECONDS,
                'vhm_time'        => $vhmTime,
                'vhm_age_seconds' => $row->VHM_AGE_SECONDS === null ? null : (int) $row->VHM_AGE_SECONDS,
                'vhm_valid'       => $vhmTime !== null,
                'active'          => (int) $row->VHC_ACTIVE === 1,
                'operator'        => $row->OPR_NAME,
                'nrp'             => $row->OPR_NRP,
                'location'        => $row->LOC_NAME,
                'shift_date'      => $this->dateOnly($row->OPR_SHIFTDATE),
                'shift_no'        => $row->OPR_SHIFTNO,
                'vsa_group'       => $row->VSA_GROUPID,
                'vsa_status'      => $row->VSA_STATUSID,
                'vsa_activity'    => $row->VSA_ACTIVITYID,
                'assignment'      => ($from || $to) ? (($from ?: '–') . ' → ' . ($to ?: '–')) : null,
                'loader_id'       => $row->ASG_LOADERID,
                'gps_speed'       => is_numeric($row->GPS_SPEED) ? (float) $row->GPS_SPEED : null,
            ],
        ])->header('Cache-Control', 'no-store');
    }

    private function activeUnits()
    {
        return Cache::store('file')->remember('realtime_series_active_flt_vehicle_v2', 120, function () {
            return DB::connection('sqlsrv')
                ->table('FOCUS.dbo.FLT_VEHICLE')
                ->select(['VHC_ID', 'EQU_GROUPID', 'EQU_TYPEID'])
                ->where('VHC_ACTIVE', 1)
                ->whereNotNull('VHC_ID')
                ->get()
                ->map(fn ($row) => [
                    'model'     => $this->modelName($row),
                    'equipment' => trim((string) $row->VHC_ID),
                ])
                ->filter(fn ($item) => $item['model'] !== '' && $item['equipment'] !== '')
                ->unique(fn ($item) => $item['model'] . '|' . $item['equipment'])
                ->values();
        });
    }

    private function modelName($row): string
    {
        $group = trim((string) ($row->EQU_GROUPID ?? ''));
        return $group !== '' ? $group : trim((string) ($row->EQU_TYPEID ?? ''));
    }

    private function sensorNumber($value): ?float
    {
        if (is_bool($value)) return $value ? 1.0 : 0.0;
        return is_numeric($value) ? (float) $value : null;
    }

    private function validCoordinates($lat, $lon): bool
    {
        return is_numeric($lat) && is_numeric($lon)
            && (float) $lat >= -90 && (float) $lat <= 90
            && (float) $lon >= -180 && (float) $lon <= 180
            && !((float) $lat === 0.0 && (float) $lon === 0.0);
    }

    private function dateString($value): ?string
    {
        if ($value === null || $value === '') return null;
        try {
            $date = Carbon::parse($value);
            return $date->year >= 2000 ? $date->format('Y-m-d H:i:s.v') : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function dateOnly($value): ?string
    {
        $date = $this->dateString($value);
        return $date ? substr($date, 0, 10) : null;
    }
}
