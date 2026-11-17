<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VhmHistoricalController extends Controller
{
    public function index()
    {
        return view('workshop.vhm-historical');
    }

    public function data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vhc_id' => ['nullable', 'string', 'max:50'],
            'start' => ['nullable', 'date'],
            'stop' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $connection = DB::connection('sqlsrv');

        $equipmentIds = collect($connection->select(
            "SELECT DISTINCT VHC_ID
            FROM FOCUS_REPORTING.dbo.VW_TRK_LOG
            WHERE VHC_ID IS NOT NULL
            AND VHC_ID LIKE 'HD%'
            ORDER BY VHC_ID"
        ))->pluck('VHC_ID')->filter()->values();

        $defaultVhcId = $equipmentIds->contains('HD1115')
            ? 'HD1115'
            : $equipmentIds->first();

        $vhcId = strtoupper(trim((string) $request->input('vhc_id', $defaultVhcId ?? '')));

        if (!$equipmentIds->contains($vhcId)) {
            $vhcId = $defaultVhcId;
        }

        if (!$vhcId) {
            return response()->json([
                'success' => false,
                'message' => 'Equipment ID belum tersedia.',
            ], 404);
        }

        $stop = $request->filled('stop')
            ? Carbon::parse($request->input('stop'))
            : now();

        $start = $request->filled('start')
            ? Carbon::parse($request->input('start'))
            : $stop->copy()->subHours(2);

        if ($stop->lessThanOrEqualTo($start)) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu akhir harus lebih besar dari waktu awal.',
            ], 422);
        }

        if ($start->diffInHours($stop) > 168) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal rentang pencarian 7 hari.',
            ], 422);
        }

        $pdo = $connection->getPdo();

        if (defined('PDO::SQLSRV_ATTR_QUERY_TIMEOUT')) {
            $pdo->setAttribute(constant('PDO::SQLSRV_ATTR_QUERY_TIMEOUT'), 120);
        }

        $data = $connection->select(
            "SET NOCOUNT ON;
            SELECT TOP 1000 A.*
            FROM FOCUS_REPORTING.dbo.VW_TRK_LOG A
            WHERE A.OPR_REPORTTIME >= ?
              AND A.OPR_REPORTTIME < ?
              AND A.VHC_ID = ?
            ORDER BY A.OPR_REPORTTIME DESC
            OPTION (RECOMPILE);",
            [
                $start->format('Y-m-d H:i:s'),
                $stop->format('Y-m-d H:i:s'),
                $vhcId,
            ]
        );

        $records = collect($data);

        $excludedColumns = [
            'ID', 'VHC_ID', 'OPR_NRP', 'OPR_SHIFTNO', 'VSA_GROUPID',
            'VSA_STATUSID', 'VSA_ACTIVITYID', 'LOC_ID', 'LOC_TYPEID',
            'LOC_REGIONID', 'GPS_LAT', 'GPS_LON', 'GPS_ALT', 'GPS_DIR',
            'GPS_SPEED', 'OPR_SHIFTDATE', 'OPR_REPORTTIME'
        ];

        $numericColumns = [];

        if ($records->isNotEmpty()) {
            foreach (array_keys(get_object_vars($records->first())) as $column) {
                if (in_array(strtoupper($column), $excludedColumns, true)) {
                    continue;
                }

                $hasNumericValue = $records->contains(function ($row) use ($column) {
                    return isset($row->{$column}) && is_numeric($row->{$column});
                });

                if ($hasNumericValue) {
                    $numericColumns[] = $column;
                }
            }
        }

        $numericColumns = array_slice($numericColumns, 0, 12);
        $chartData = [];

        foreach ($records as $row) {
            foreach ($numericColumns as $column) {
                if (!isset($row->{$column}) || !is_numeric($row->{$column})) {
                    continue;
                }

                $chartData[] = [
                    'equipment' => $row->VHC_ID ?? $vhcId,
                    'time' => !empty($row->OPR_REPORTTIME)
                        ? Carbon::parse($row->OPR_REPORTTIME)->format('Y-m-d H:i:s')
                        : null,
                    'operator' => $row->OPR_NRP ?? '-',
                    'location' => $row->LOC_NAME ?? 'Unknown',
                    'value' => (float) $row->{$column},
                    'remark' => $column,
                    'max' => null,
                    'min' => null,
                ];
            }
        }

        $operators = $records->pluck('OPR_NRP')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'equipmentIds' => $equipmentIds,
            'vhcId' => $vhcId,
            'start' => $start->format('Y-m-d\TH:i'),
            'stop' => $stop->format('Y-m-d\TH:i'),
            'recordsCount' => $records->count(),
            'operators' => $operators,
            'parameters' => $numericColumns,
            'chartData' => $chartData,
        ]);
    }
}
