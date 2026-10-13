<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CaterpillarFaultCodeController extends Controller
{
    private const PER_PAGE = 50;

    /**
     * ================================================================
     * MAIN PAGE
     * ================================================================
     */
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

        /**
         * ============================================================
         * ERROR MESSAGE
         * ============================================================
         *
         * Cache 30 menit supaya REF_WARNING_CODE_CAT
         * tidak dibaca terus setiap request.
         */
        $errorMessages = Cache::remember(
            'cat_fault_error_messages',
            now()->addMinutes(30),
            function () {
                return DB::connection('sqlsrv')
                    ->table('DB_MACHINE_HEALTH.dbo.REF_WARNING_CODE_CAT')
                    ->select('ERROR_MESSAGE')
                    ->whereNotNull('ERROR_MESSAGE')
                    ->where('ERROR_MESSAGE', '<>', '')
                    ->distinct()
                    ->orderBy('ERROR_MESSAGE')
                    ->pluck('ERROR_MESSAGE');
            }
        );

        /**
         * ============================================================
         * DEFAULT VARIABLE
         * ============================================================
         */
        $data = null;

        $totalRecords = 0;
        $uniqueFaults = 0;
        $uniqueUnits = 0;
        $uniqueOperators = 0;

        $severityLow = 0;
        $severityMedium = 0;
        $severityHigh = 0;

        $topFaults = collect();
        $topSources = collect();
        $topLocations = collect();
        $topUnits = collect();
        $typeSummary = collect();
        $dailyTrend = collect();

        $maxTopFault = 1;
        $maxSource = 1;
        $maxLocation = 1;
        $maxUnit = 1;
        $maxDaily = 1;

        /**
         * ============================================================
         * RAW DATA
         * ============================================================
         *
         * Query ini HANYA dijalankan jika tab RAW dibuka.
         */
        if ($activeTab === 'raw') {

            $data = $this->rawQuery(
                $startDate,
                $stopDate,
                $selectedError
            )
                ->orderByDesc('A.Fault_Date')
                ->orderBy('A.Name')
                ->orderBy('A.Fault_Code')
                ->simplePaginate(self::PER_PAGE)
                ->withQueryString();
        }

        /**
         * ============================================================
         * ANALYTICS
         * ============================================================
         *
         * Query analytics HANYA dijalankan ketika tab Analytics dibuka.
         */
        if ($activeTab === 'analytics') {

            $cacheKey = implode('_', [
                'cat_fault_analytics',
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
            $uniqueFaults = $analytics['uniqueFaults'];
            $uniqueUnits = $analytics['uniqueUnits'];
            $uniqueOperators = $analytics['uniqueOperators'];

            $severityLow = $analytics['severityLow'];
            $severityMedium = $analytics['severityMedium'];
            $severityHigh = $analytics['severityHigh'];

            $topFaults = $analytics['topFaults'];
            $topSources = $analytics['topSources'];
            $topLocations = $analytics['topLocations'];
            $topUnits = $analytics['topUnits'];
            $typeSummary = $analytics['typeSummary'];
            $dailyTrend = $analytics['dailyTrend'];

            $maxTopFault = max(
                (int) ($topFaults->max('total') ?? 0),
                1
            );

            $maxSource = max(
                (int) ($topSources->max('total') ?? 0),
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
            'caterpillar.fault-code-raw-data',
            compact(
                'data',

                'errorMessages',
                'selectedError',

                'startDate',
                'stopDate',

                'activeTab',

                'totalRecords',
                'uniqueFaults',
                'uniqueUnits',
                'uniqueOperators',

                'severityLow',
                'severityMedium',
                'severityHigh',

                'topFaults',
                'topSources',
                'topLocations',
                'topUnits',
                'typeSummary',
                'dailyTrend',

                'maxTopFault',
                'maxSource',
                'maxLocation',
                'maxUnit',
                'maxDaily'
            )
        );
    }

    /**
     * ================================================================
     * FAST EXPORT
     * ================================================================
     *
     * Format CSV:
     * - jauh lebih ringan dibanding XLSX
     * - langsung streaming ke browser
     * - tetap bisa dibuka Microsoft Excel
     * - tidak membuat jutaan object Cell PHPSpreadsheet
     */
    public function export(Request $request)
    {
        [$startDate, $stopDate] = $this->getDateRange($request);

        $selectedError = trim(
            (string) $request->get('error_message', '')
        );

        $fileName =
            'CAT_Fault_Code_' .
            $startDate->format('Ymd_His') .
            '_to_' .
            $stopDate->format('Ymd_His') .
            '.csv';

        /**
         * Hindari timeout PHP untuk export besar.
         */
        @set_time_limit(0);

        /**
         * Query logging tidak diperlukan untuk export.
         */
        DB::connection('sqlsrv')->disableQueryLog();

        return response()->streamDownload(
            function () use (
                $startDate,
                $stopDate,
                $selectedError
            ) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                /**
                 * BOM UTF-8.
                 *
                 * Supaya text ketika dibuka Excel tidak rusak.
                 */
                fwrite(
                    $handle,
                    "\xEF\xBB\xBF"
                );

                /**
                 * ====================================================
                 * HEADER
                 * ====================================================
                 */
                fputcsv(
                    $handle,
                    [
                        'Model',
                        'Unit',
                        'Fault Code',

                        'Fault Date',

                        'Hours Meter',
                        'Odometer',

                        'Source',
                        'Type',
                        'Occurrences',

                        'Longitude',
                        'Latitude',

                        'Location',
                        'Layer',

                        'GPS Speed',
                        'PLM Speed',

                        'NIK Operator',
                        'Operator Name',

                        'Severity Low',
                        'Severity Medium',
                        'Severity High',

                        'VSA Status',
                        'VSA Activity',
                    ],
                    ';'
                );

                /**
                 * ====================================================
                 * QUERY EXPORT
                 * ====================================================
                 */
                $query = $this->rawQuery(
                    $startDate,
                    $stopDate,
                    $selectedError
                )
                    ->orderBy('A.Fault_Date')
                    ->orderBy('A.Name')
                    ->orderBy('A.Fault_Code');

                /**
                 * lazy() = proses bertahap.
                 *
                 * Tidak semua data dimasukkan ke RAM.
                 */
                foreach ($query->lazy(2000) as $row) {

                    fputcsv(
                        $handle,
                        [
                            $row->Model ?? '',
                            $row->Name ?? '',
                            $row->Fault_Code ?? '',

                            $this->formatDate(
                                $row->Fault_Date ?? null
                            ),

                            $row->Hours_Value ?? '',
                            $row->Odometer_Value ?? '',

                            $row->Source ?? '',
                            $row->Type ?? '',
                            $row->Occurrences ?? '',

                            $row->GPS_LON ?? '',
                            $row->GPS_LAT ?? '',

                            $row->LOC_NAME ?? '',
                            $row->LAYERNAME ?? '',

                            $row->GPS_SPEED ?? '',
                            $row->PLM_SPEED ?? '',

                            /**
                             * NRP dibuat text supaya angka/nol
                             * di depan tidak berubah di Excel.
                             */
                            !empty($row->OPR_NRP)
                                ? '="' . $row->OPR_NRP . '"'
                                : '',

                            $row->PERSONALNAME ?? '',

                            $row->Severity_Level1 ?? 0,
                            $row->Severity_Level2 ?? 0,
                            $row->Severity_Level3 ?? 0,

                            $row->VSA_STATUSDESC ?? '',
                            $row->VSA_ACTIVITYDESC ?? '',
                        ],
                        ';'
                    );

                    /**
                     * Flush output secara berkala.
                     */
                    if (ob_get_level() > 0) {
                        @ob_flush();
                    }

                    flush();
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' . $fileName . '"',

                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate',

                'Pragma' =>
                    'no-cache',
            ]
        );
    }

    /**
     * ================================================================
     * RAW QUERY
     * ================================================================
     */
    private function rawQuery(
        Carbon $startDate,
        Carbon $stopDate,
        string $selectedError = ''
    ): Builder {

        $query = DB::connection('sqlsrv')
            ->table(
                'DB_MACHINE_HEALTH.dbo.VW_OPR_CAT_ERROR_CODE AS A'
            )

            /**
             * LOCATION
             */
            ->leftJoin(
                'FOCUS.dbo.MAP_OBJECT AS B',
                'A.LOC_NAME',
                '=',
                'B.MAPOBJECTNAME'
            )

            /**
             * OPERATOR
             */
            ->leftJoin(
                'FOCUS.dbo.PRS_PERSONAL AS D',
                'A.OPR_NRP',
                '=',
                'D.NRP'
            )

            /**
             * VSA STATUS
             */
            ->leftJoin(
                'FOCUS.dbo.FLT_VSASTATUS AS E',
                'A.VSA_STATUSID',
                '=',
                'E.VSA_STATUSID'
            )

            /**
             * VSA ACTIVITY
             */
            ->leftJoin(
                'FOCUS.dbo.FLT_VSAACTIVITY AS F',
                function ($join) {

                    $join->on(
                        'A.VSA_ACTIVITYID',
                        '=',
                        'F.VSA_ACTIVITYID'
                    );

                    $join->where(
                        'F.VHC_TYPEID',
                        '=',
                        2
                    );
                }
            )

            /**
             * Hanya field yang dipakai.
             */
            ->select([
                'A.Model',
                'A.Name',
                'A.Fault_Code',
                'A.Fault_Date',

                'A.Hours_Value',
                'A.Odometer_Value',

                'A.Source',
                'A.Type',
                'A.Occurrences',

                'A.GPS_LON',
                'A.GPS_LAT',
                'A.GPS_SPEED',

                'A.LOC_NAME',
                'A.PLM_SPEED',

                'A.OPR_NRP',

                'A.Severity_Level1',
                'A.Severity_Level2',
                'A.Severity_Level3',

                'D.PERSONALNAME',

                'E.VSA_STATUSDESC',
                'F.VSA_ACTIVITYDESC',

                'B.LAYERNAME',
            ])

            /**
             * START inclusive.
             */
            ->where(
                'A.Fault_Date',
                '>=',
                $startDate->format(
                    'Y-m-d H:i:s'
                )
            )

            /**
             * STOP exclusive.
             *
             * Contoh:
             * 23 Sep 07:00
             * sampai
             * 30 Sep 07:00
             *
             * Data tepat 30 Sep 07:00
             * masuk periode berikutnya.
             */
            ->where(
                'A.Fault_Date',
                '<',
                $stopDate->format(
                    'Y-m-d H:i:s'
                )
            );

        /**
         * FILTER ERROR MESSAGE
         */
        if ($selectedError !== '') {

            $query->where(
                'A.Fault_Code',
                '=',
                $selectedError
            );
        }

        return $query;
    }

    /**
     * ================================================================
     * ANALYTICS
     * ================================================================
     */
    private function getAnalytics(
        Carbon $startDate,
        Carbon $stopDate,
        string $selectedError = ''
    ): array {

        /**
         * ============================================================
         * SUMMARY
         * ============================================================
         *
         * Total + distinct + severity digabung menjadi 1 query.
         */
        $summary = $this->analyticsBaseQuery(
            $startDate,
            $stopDate,
            $selectedError
        )
            ->selectRaw("
                COUNT_BIG(*) AS totalRecords,

                COUNT(
                    DISTINCT
                    CASE
                        WHEN A.Fault_Code IS NOT NULL
                             AND LTRIM(RTRIM(A.Fault_Code)) <> ''
                        THEN A.Fault_Code
                    END
                ) AS uniqueFaults,

                COUNT(
                    DISTINCT
                    CASE
                        WHEN A.Name IS NOT NULL
                             AND LTRIM(RTRIM(A.Name)) <> ''
                        THEN A.Name
                    END
                ) AS uniqueUnits,

                COUNT(
                    DISTINCT
                    CASE
                        WHEN A.OPR_NRP IS NOT NULL
                             AND LTRIM(RTRIM(A.OPR_NRP)) <> ''
                        THEN A.OPR_NRP
                    END
                ) AS uniqueOperators,

                SUM(
                    COALESCE(
                        A.Severity_Level1,
                        0
                    )
                ) AS severityLow,

                SUM(
                    COALESCE(
                        A.Severity_Level2,
                        0
                    )
                ) AS severityMedium,

                SUM(
                    COALESCE(
                        A.Severity_Level3,
                        0
                    )
                ) AS severityHigh
            ")
            ->first();

        /**
         * ============================================================
         * TOP FAULT CODE
         * ============================================================
         */
        $topFaults = $this->analyticsBaseQuery(
            $startDate,
            $stopDate,
            $selectedError
        )
            ->selectRaw("
                A.Fault_Code AS label,
                COUNT_BIG(*) AS total
            ")
            ->whereNotNull('A.Fault_Code')
            ->where('A.Fault_Code', '<>', '')
            ->groupBy('A.Fault_Code')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        /**
         * ============================================================
         * TOP SOURCE
         * ============================================================
         */
        $topSources = $this->analyticsBaseQuery(
            $startDate,
            $stopDate,
            $selectedError
        )
            ->selectRaw("
                A.Source AS label,
                COUNT_BIG(*) AS total
            ")
            ->whereNotNull('A.Source')
            ->where('A.Source', '<>', '')
            ->groupBy('A.Source')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        /**
         * ============================================================
         * TOP UNIT
         * ============================================================
         */
        $topUnits = $this->analyticsBaseQuery(
            $startDate,
            $stopDate,
            $selectedError
        )
            ->selectRaw("
                A.Name AS label,
                COUNT_BIG(*) AS total
            ")
            ->whereNotNull('A.Name')
            ->where('A.Name', '<>', '')
            ->groupBy('A.Name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        /**
         * ============================================================
         * TOP LOCATION
         * ============================================================
         */
        $topLocations = $this->analyticsBaseQuery(
            $startDate,
            $stopDate,
            $selectedError
        )
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

        /**
         * ============================================================
         * TYPE SUMMARY
         * ============================================================
         */
        $typeSummary = $this->analyticsBaseQuery(
            $startDate,
            $stopDate,
            $selectedError
        )
            ->selectRaw("
                CASE
                    WHEN A.Type IS NULL
                         OR LTRIM(RTRIM(A.Type)) = ''
                    THEN 'Unknown'
                    ELSE A.Type
                END AS label,

                COUNT_BIG(*) AS total
            ")
            ->groupByRaw("
                CASE
                    WHEN A.Type IS NULL
                         OR LTRIM(RTRIM(A.Type)) = ''
                    THEN 'Unknown'
                    ELSE A.Type
                END
            ")
            ->orderByDesc('total')
            ->get();

        /**
         * ============================================================
         * DAILY TREND
         * ============================================================
         */
        $dailyTrend = $this->analyticsBaseQuery(
            $startDate,
            $stopDate,
            $selectedError
        )
            ->selectRaw("
                CAST(A.Fault_Date AS DATE) AS [date],
                COUNT_BIG(*) AS total
            ")
            ->groupByRaw(
                'CAST(A.Fault_Date AS DATE)'
            )
            ->orderByRaw(
                'CAST(A.Fault_Date AS DATE) ASC'
            )
            ->get();

        return [

            'totalRecords' =>
                (int) ($summary->totalRecords ?? 0),

            'uniqueFaults' =>
                (int) ($summary->uniqueFaults ?? 0),

            'uniqueUnits' =>
                (int) ($summary->uniqueUnits ?? 0),

            'uniqueOperators' =>
                (int) ($summary->uniqueOperators ?? 0),

            'severityLow' =>
                (int) ($summary->severityLow ?? 0),

            'severityMedium' =>
                (int) ($summary->severityMedium ?? 0),

            'severityHigh' =>
                (int) ($summary->severityHigh ?? 0),

            'topFaults' =>
                $topFaults,

            'topSources' =>
                $topSources,

            'topLocations' =>
                $topLocations,

            'topUnits' =>
                $topUnits,

            'typeSummary' =>
                $typeSummary,

            'dailyTrend' =>
                $dailyTrend,
        ];
    }

    /**
     * ================================================================
     * ANALYTICS BASE QUERY
     * ================================================================
     *
     * Tidak melakukan JOIN karena analytics menggunakan
     * field dari VW_OPR_CAT_ERROR_CODE saja.
     */
    private function analyticsBaseQuery(
        Carbon $startDate,
        Carbon $stopDate,
        string $selectedError = ''
    ): Builder {

        $query = DB::connection('sqlsrv')
            ->table(
                'DB_MACHINE_HEALTH.dbo.VW_OPR_CAT_ERROR_CODE AS A'
            )
            ->where(
                'A.Fault_Date',
                '>=',
                $startDate->format(
                    'Y-m-d H:i:s'
                )
            )
            ->where(
                'A.Fault_Date',
                '<',
                $stopDate->format(
                    'Y-m-d H:i:s'
                )
            );

        if ($selectedError !== '') {

            $query->where(
                'A.Fault_Code',
                '=',
                $selectedError
            );
        }

        return $query;
    }

    /**
     * ================================================================
     * DATE RANGE
     * ================================================================
     *
     * Jika hari ini 07 Oktober 2026:
     *
     * START = 23 September 2026 07:00
     * STOP  = 30 September 2026 07:00
     */
    private function getDateRange(
        Request $request
    ): array {

        $defaultStop = Carbon::today()
            ->subDays(7)
            ->setTime(7, 0, 0);

        $defaultStart = $defaultStop
            ->copy()
            ->subDays(7);

        try {

            $startDate = $request->filled(
                'start_date'
            )
                ? Carbon::parse(
                    $request->start_date
                )->setTime(
                    7,
                    0,
                    0
                )
                : $defaultStart;

            $stopDate = $request->filled(
                'stop_date'
            )
                ? Carbon::parse(
                    $request->stop_date
                )->setTime(
                    7,
                    0,
                    0
                )
                : $defaultStop;

        } catch (\Throwable $e) {

            $startDate = $defaultStart;
            $stopDate = $defaultStop;
        }

        /**
         * Kalau user membalik tanggal,
         * otomatis tukar.
         */
        if ($startDate->gt($stopDate)) {

            $temp = $startDate->copy();

            $startDate = $stopDate->copy();

            $stopDate = $temp;
        }

        return [
            $startDate,
            $stopDate
        ];
    }

    /**
     * ================================================================
     * FORMAT DATE
     * ================================================================
     */
    private function formatDate(
        $date
    ): string {

        if (empty($date)) {
            return '';
        }

        try {

            return Carbon::parse(
                $date
            )->format(
                'Y-m-d H:i:s'
            );

        } catch (\Throwable $e) {

            return (string) $date;
        }
    }
}
