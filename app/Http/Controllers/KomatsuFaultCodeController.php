<?php
namespace App\Http\Controllers;
use App\Exports\KomatsuFaultCodeExport;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class KomatsuFaultCodeController extends Controller
{
    private const PER_PAGE = 50;
    public function index(Request $request)
    {
        [$startDate, $stopDate] = $this->getDateRange($request);
        $selectedError = trim(
            (string) $request->get('error_message', '')
        );
        $activeTab = $request->get('tab', 'raw');
        if (!in_array($activeTab, ['raw', 'analytics'])) {
            $activeTab = 'raw';
        }
        $errorMessages = Cache::remember(
            'komatsu_fault_error_messages',
            now()->addMinutes(30),
            function () {
                return DB::connection('sqlsrv')
                    ->table('DB_MACHINE_HEALTH.dbo.VW_OPR_KOMATSU_ERROR_CODE')
                    ->whereNotNull('Error_Message')
                    ->where('Error_Message', '<>', '')
                    ->distinct()
                    ->orderBy('Error_Message')
                    ->pluck('Error_Message');
            }
        );
        $data = null;
        $totalRecords = 0;
        $totalOccurrences = 0;
        $uniqueFaults = 0;
        $uniqueUnits = 0;
        $uniqueOperators = 0;
        $topFaults = collect();
        $topErrors = collect();
        $topLocations = collect();
        $topUnits = collect();
        $dailyTrend = collect();
        $maxTopFault = 1;
        $maxError = 1;
        $maxLocation = 1;
        $maxUnit = 1;
        $maxDaily = 1;
        if ($activeTab === 'raw') {
            $data = $this->rawQuery(
                $startDate,
                $stopDate,
                $selectedError
            )
                ->orderByDesc('A.From_Date_Time')
                ->orderBy('A.NAME')
                ->orderBy('A.Code')
                ->simplePaginate(self::PER_PAGE)
                ->withQueryString();
        }
        if ($activeTab === 'analytics') {
            $cacheKey = implode('_', [
                'komatsu_fault_analytics',
                $startDate->format('YmdHis'),
                $stopDate->format('YmdHis'),
                md5($selectedError),
            ]);
            $analytics = Cache::remember(
                $cacheKey,
                now()->addMinutes(5),
                function () use (
                    $startDate,
                    $stopDate,
                    $selectedError
                ) {
                    return $this->getAnalytics(
                        $startDate,
                        $stopDate,
                        $selectedError
                    );
                }
            );
            $totalRecords = $analytics['totalRecords'];
            $totalOccurrences = $analytics['totalOccurrences'];
            $uniqueFaults = $analytics['uniqueFaults'];
            $uniqueUnits = $analytics['uniqueUnits'];
            $uniqueOperators = $analytics['uniqueOperators'];
            $topFaults = $analytics['topFaults'];
            $topErrors = $analytics['topErrors'];
            $topLocations = $analytics['topLocations'];
            $topUnits = $analytics['topUnits'];
            $dailyTrend = $analytics['dailyTrend'];
            $maxTopFault = max(
                (int) ($topFaults->max('total') ?? 0),
                1
            );
            $maxError = max(
                (int) ($topErrors->max('total') ?? 0),
                1
            );
            $maxLocation = max(
                (int) ($topLocations->max('total') ?? 0),
                1
            );
            $maxUnit = max(
                (int) ($topUnits->max('total') ?? 0),
                1
            );
            $maxDaily = max(
                (int) ($dailyTrend->max('total') ?? 0),
                1
            );
        }
        return view(
            'komatsu.fault-code-raw-data',
            compact(
                'data',
                'errorMessages',
                'selectedError',
                'startDate',
                'stopDate',
                'activeTab',
                'totalRecords',
                'totalOccurrences',
                'uniqueFaults',
                'uniqueUnits',
                'uniqueOperators',
                'topFaults',
                'topErrors',
                'topLocations',
                'topUnits',
                'dailyTrend',
                'maxTopFault',
                'maxError',
                'maxLocation',
                'maxUnit',
                'maxDaily'
            )
        );
    }

    public function export(Request $request)
    {
        [$startDate, $stopDate] = $this->getDateRange($request);
        $selectedError = trim((string) $request->input('error_message', ''));
        $fileName = 'komatsu-fault-code-' . now()->format('Ymd_His') . '.csv';
        return Excel::download(
            new KomatsuFaultCodeExport($startDate, $stopDate, $selectedError),
            $fileName,
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    private function rawQuery(
        Carbon $startDate,
        Carbon $stopDate,
        string $selectedError = ''
    ): Builder {
        $query = DB::connection('sqlsrv')
            ->table(
                'DB_MACHINE_HEALTH.dbo.VW_OPR_KOMATSU_ERROR_CODE AS A'
            )
            ->select([
                'A.ID',
                'A.SN',
                'A.Code',
                'A.From_SMR',
                'A.From_Time',
                'A.From_Epoch_Time',
                'A.From_Date_Time',
                'A.To_SMR',
                'A.To_Time',
                'A.To_Epoch_Time',
                'A.To_Date_Time',
                'A.C_GC_View_Flag',
                'A.Count',
                'A.Error_Message',
                'A.Created_At',
                'A.GPS_LON',
                'A.GPS_LAT',
                'A.GPS_ALT',
                'A.GPS_SPEED',
                'A.GPS_DIR',
                'A.LOC_ID',
                'A.LOC_TYPEID',
                'A.LOC_NAME',
                'A.LOC_REGIONID',
                'A.PLM_TIMESTAMP',
                'A.PLM_LF',
                'A.PLM_RF',
                'A.PLM_LR',
                'A.PLM_RR',
                'A.PLM_PAYLOAD',
                'A.PLM_SPEED',
                'A.PLM_INC',
                'A.PLM_SLP',
                'A.PLM_BFS',
                'A.PLM_ALT',
                'A.PLM_STATUSID',
                'A.OPR_NRP',
                'A.VSA_GROUPID',
                'A.VSA_STATUSID',
                'A.VSA_ACTIVITYID',
                'A.MTR_HOURMETER',
                'A.MTR_ODOMETER',
                'A.OPR_REPORTTIME',
                'A.OPR_SHIFTDATE',
                'A.OPR_SHIFTNO',
                'A.OPR_NAME',
                'A.CLC_ENGINESTATUS',
                'A.CLC_MOVINGSTATUS',
                'A.CLC_PAYLOADSTATUS',
                'A.MTR_SPEED',
                'A.MTR_RPM',
                'A.VHM_ENGOILPRESS',
                'A.VHM_BOOSTPRESS',
                'A.MTR_ENGINECOOLANTTEMPERATURE',
                'A.MTR_ENGINEOILTEMPERATURE',
                'A.MTR_TRANSMISSIONOILTEMPERATURE',
                'A.MTR_BRAKEOILTEMPERATURE',
                'A.MTR_AMBIENTAIRTEMPERATURE',
                'A.MTR_FUELLEVEL',
                'A.MTR_FUELRATE',
                'A.VHM_ENGSPEED',
                'A.VHM_COOLTEMP',
                'A.VHM_HYDOILTEMP',
                'A.VHM_TMOILTEMP',
                'A.VHM_TCOILTEMP',
                'A.VHM_FBRAKEOILPRESS',
                'A.VHM_RBRAKEOILPRESS',
                'A.NAME',
            ])
            ->where(
                'A.From_Date_Time',
                '>=',
                $startDate->format('Y-m-d H:i:s')
            )
            ->where(
                'A.From_Date_Time',
                '<',
                $stopDate->format('Y-m-d H:i:s')
            );
        if ($selectedError !== '') {
            $query->where(
                'A.Error_Message',
                '=',
                $selectedError
            );
        }
        return $query;
    }

    private function getAnalytics(
        Carbon $startDate,
        Carbon $stopDate,
        string $selectedError = ''
    ): array {
        $base = $this->analyticsBaseQuery(
            $startDate,
            $stopDate,
            $selectedError
        );
        $summary = (clone $base)
            ->selectRaw("
                COUNT_BIG(*) AS totalRecords,
                COALESCE(SUM(COALESCE(A.[Count], 0)), 0) AS totalOccurrences,
                COUNT(DISTINCT A.Code) AS uniqueFaults,
                COUNT(DISTINCT A.NAME) AS uniqueUnits,
                COUNT(DISTINCT A.OPR_NRP) AS uniqueOperators
            ")
            ->first();
        $topFaults = (clone $base)
            ->selectRaw("
                A.Code AS label,
                COUNT_BIG(*) AS total
            ")
            ->whereNotNull('A.Code')
            ->where('A.Code', '<>', '')
            ->groupBy('A.Code')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $topErrors = (clone $base)
            ->selectRaw("
                A.Error_Message AS label,
                COUNT_BIG(*) AS total
            ")
            ->whereNotNull('A.Error_Message')
            ->where('A.Error_Message', '<>', '')
            ->groupBy('A.Error_Message')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $topLocations = (clone $base)
            ->selectRaw("
                A.LOC_NAME AS label,
                COUNT_BIG(*) AS total
            ")
            ->whereNotNull('A.LOC_NAME')
            ->where('A.LOC_NAME', '<>', '')
            ->groupBy('A.LOC_NAME')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $topUnits = (clone $base)
            ->selectRaw("
                A.NAME AS label,
                COUNT_BIG(*) AS total
            ")
            ->whereNotNull('A.NAME')
            ->where('A.NAME', '<>', '')
            ->groupBy('A.NAME')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $dailyTrend = (clone $base)
            ->selectRaw("
                CAST(A.From_Date_Time AS DATE) AS [date],
                COUNT_BIG(*) AS total
            ")
            ->groupByRaw(
                'CAST(A.From_Date_Time AS DATE)'
            )
            ->orderByRaw(
                'CAST(A.From_Date_Time AS DATE) ASC'
            )
            ->get();
        return [
            'totalRecords' => (int) ($summary->totalRecords ?? 0),
            'totalOccurrences' => (int) ($summary->totalOccurrences ?? 0),
            'uniqueFaults' => (int) ($summary->uniqueFaults ?? 0),
            'uniqueUnits' => (int) ($summary->uniqueUnits ?? 0),
            'uniqueOperators' => (int) ($summary->uniqueOperators ?? 0),
            'topFaults' => $topFaults,
            'topErrors' => $topErrors,
            'topLocations' => $topLocations,
            'topUnits' => $topUnits,
            'dailyTrend' => $dailyTrend,
        ];
    }
    private function analyticsBaseQuery(
        Carbon $startDate,
        Carbon $stopDate,
        string $selectedError = ''
    ): Builder {
        $query = DB::connection('sqlsrv')
            ->table(
                'DB_MACHINE_HEALTH.dbo.VW_OPR_KOMATSU_ERROR_CODE AS A'
            )
            ->where(
                'A.From_Date_Time',
                '>=',
                $startDate->format('Y-m-d H:i:s')
            )
            ->where(
                'A.From_Date_Time',
                '<',
                $stopDate->format('Y-m-d H:i:s')
            );
        if ($selectedError !== '') {
            $query->where(
                'A.Error_Message',
                '=',
                $selectedError
            );
        }
        return $query;
    }
    private function getDateRange(Request $request): array
    {
        $defaultStop = Carbon::today()
            ->subDays(7)
            ->setTime(7, 0, 0);
        $defaultStart = $defaultStop
            ->copy()
            ->subDays(7);
        try {
            $startDate = $request->filled('start_date')
                ? Carbon::parse($request->start_date)->setTime(7, 0, 0)
                : $defaultStart;
            $stopDate = $request->filled('stop_date')
                ? Carbon::parse($request->stop_date)->setTime(7, 0, 0)
                : $defaultStop;
        } catch (\Throwable $e) {
            $startDate = $defaultStart;
            $stopDate = $defaultStop;
        }
        if ($startDate->gt($stopDate)) {
            [$startDate, $stopDate] = [
                $stopDate->copy(),
                $startDate->copy(),
            ];
        }
        return [$startDate, $stopDate];
    }
}
