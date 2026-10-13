<?php

namespace App\Console\Commands;

use App\Services\CatAuthService;
use App\Services\CatErrorCodeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncCatFaultCodes extends Command
{
    protected $signature = 'cat:sync-fault-codes';

    protected $description = 'Sync CAT Fault Codes 5 hari terakhir ke database';

    private const FAULT_URL = 'https://api.cat.com/catDigital/faultCodesHistory/v1/faultCodes/search';
    private const PARTY_NUMBER = '2969635690';
    private const LIMIT = 1000;

    public function handle(
        CatAuthService $catAuthService,
        CatErrorCodeService $service
    ) {
        set_time_limit(0);

        $left = Carbon::now('Asia/Makassar')
            ->subDays(5)
            ->startOfDay();

        $right = Carbon::now('Asia/Makassar')->endOfDay();

        $this->info('CAT Fault Code Sync');
        $this->info('Start : ' . $left->format('Y-m-d H:i:s'));
        $this->info('End   : ' . $right->format('Y-m-d H:i:s'));

        $cursor = null;
        $page = 0;
        $totalFetched = 0;
        $totalInserted = 0;
        $totalSkipped = 0;
        $totalInvalid = 0;
        $startedAt = microtime(true);

        try {
            do {
                $page++;

                $this->info("Request page {$page}...");

                $accessToken = $catAuthService->getAccessToken();

                $response = $this->sendCatRequest(
                    $accessToken,
                    $left,
                    $right,
                    $cursor
                );

                if ($response->status() === 401 || $response->status() === 403) {
                    $this->warn('Access token expired. Refreshing...');

                    $accessToken = $catAuthService->getAccessToken(true);

                    $response = $this->sendCatRequest(
                        $accessToken,
                        $left,
                        $right,
                        $cursor
                    );
                }

                if (!$response->successful()) {
                    $this->error('CAT API Error HTTP ' . $response->status());
                    $this->error($response->body());

                    Log::error('CAT Scheduled Sync API Error', [
                        'status' => $response->status(),
                        'page' => $page,
                        'response' => $response->body(),
                    ]);

                    return Command::FAILURE;
                }

                $data = $response->json();

                $faultCodes = $data['faultCodes']
                    ?? $data['data']['faultCodes']
                    ?? [];

                $count = count($faultCodes);

                if ($count === 0) {
                    $this->info('Tidak ada data pada page ini.');
                    break;
                }

                $totalFetched += $count;

                $result = $service->syncFaultCodesInChunks($faultCodes, 100);

                $inserted = $result['inserted'] ?? 0;
                $skipped = $result['skipped'] ?? 0;
                $invalid = $result['invalid'] ?? 0;

                $totalInserted += $inserted;
                $totalSkipped += $skipped;
                $totalInvalid += $invalid;

                $cursor = data_get($data, 'responseMetadata.nextCursor');

                $this->info(
                    "Page {$page}"
                    . " | Fetched={$count}"
                    . " | Insert={$inserted}"
                    . " | Skip={$skipped}"
                    . " | Invalid={$invalid}"
                );

                Log::info('CAT Scheduled Fault Code Sync', [
                    'page' => $page,
                    'fetched' => $count,
                    'inserted' => $inserted,
                    'skipped' => $skipped,
                    'invalid' => $invalid,
                    'has_next_cursor' => !empty($cursor),
                ]);

                usleep(300000);
            } while (!empty($cursor));
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            Log::error('CAT Scheduled Sync Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'page' => $page,
            ]);

            return Command::FAILURE;
        }

        $duration = round(microtime(true) - $startedAt, 2);

        $this->newLine();
        $this->info('======================================');
        $this->info('CAT FAULT CODE SYNC SELESAI');
        $this->info('======================================');
        $this->info('Range    : ' . $left->format('Y-m-d') . ' s/d ' . $right->format('Y-m-d'));
        $this->info('Pages    : ' . $page);
        $this->info('Fetched  : ' . $totalFetched);
        $this->info('Inserted : ' . $totalInserted);
        $this->info('Skipped  : ' . $totalSkipped);
        $this->info('Invalid  : ' . $totalInvalid);
        $this->info('Duration : ' . $duration . ' seconds');
        $this->info('======================================');

        Log::info('CAT Scheduled Sync Completed', [
            'start_date' => $left->format('Y-m-d H:i:s'),
            'end_date' => $right->format('Y-m-d H:i:s'),
            'pages' => $page,
            'fetched' => $totalFetched,
            'inserted' => $totalInserted,
            'skipped' => $totalSkipped,
            'invalid' => $totalInvalid,
            'duration_seconds' => $duration,
        ]);

        return Command::SUCCESS;
    }

    private function sendCatRequest(
        string $accessToken,
        Carbon $left,
        Carbon $right,
        ?string $cursor
    ): Response {
        $params = [
            'limit' => self::LIMIT,
            'orderBy' => 'ASC',
            'sortBy' => 'name',
            'partyNumber' => self::PARTY_NUMBER,
        ];

        if (!empty($cursor)) {
            $params['cursor'] = $cursor;
        }

        return Http::withToken($accessToken)
            ->acceptJson()
            ->asJson()
            ->timeout(120)
            ->connectTimeout(30)
            ->retry(2, 1000, throw: false)
            ->withHeaders([
                'Origin' => 'https://vl.cat.com',
                'Referer' => 'https://vl.cat.com/',
            ])
            ->post(
                self::FAULT_URL . '?' . http_build_query($params),
                [
                    'filters' => [
                        [
                            'type' => 'timeRange',
                            'propertyName' => 'startTime',
                            'range' => [
                                'left' => $this->catDate($left),
                                'right' => $this->catDate($right),
                            ],
                        ],
                    ],
                ]
            );
    }

    private function catDate(Carbon $date): string
    {
        return $date
            ->copy()
            ->timezone('Asia/Makassar')
            ->format('Y-m-d\TH:i:sP');
    }
}
