<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MachineHealthRankingController extends Controller
{
    public function index()
    {
        return view('workshop.machine-health-ranking');
    }

    public function data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => ['required', 'date'],
            'stop' => ['required', 'date', 'after_or_equal:start'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $start = Carbon::parse($request->input('start'))->startOfDay();
        $stop = Carbon::parse($request->input('stop'))->startOfDay();

        if ($start->diffInDays($stop) > 93) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal rentang tanggal 93 hari.',
            ], 422);
        }

        try {
            $connection = DB::connection('sqlsrv');

            if (defined('PDO::SQLSRV_ATTR_QUERY_TIMEOUT')) {
                $connection->getPdo()->setAttribute(
                    constant('PDO::SQLSRV_ATTR_QUERY_TIMEOUT'),
                    240
                );
            }

            $rows = $connection->select(
                "SET NOCOUNT ON;
                 EXEC DB_MACHINE_HEALTH.dbo.RPT_RESUME_VHC_SCORE_HEALTH_BAD
                    @START = ?,
                    @STOP = ?;",
                [
                    $start->format('Y-m-d'),
                    $stop->format('Y-m-d'),
                ]
            );

            return response()->json([
                'success' => true,
                'start' => $start->format('Y-m-d'),
                'stop' => $stop->format('Y-m-d'),
                'totalRows' => count($rows),
                'data' => $rows,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data Machine Health Ranking. Query membutuhkan waktu terlalu lama atau terjadi kesalahan pada database.',
                'detail' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
