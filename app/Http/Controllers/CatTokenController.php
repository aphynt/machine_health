<?php

namespace App\Http\Controllers;

use App\Services\CatAuthService;
use App\Services\CatErrorCodeService;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CatTokenController extends Controller
{
    private const FAULT_URL = 'https://api.cat.com/catDigital/faultCodesHistory/v1/faultCodes/search';
    private const PARTY_NUMBER = '2969635690';
    private const LIMIT = 1000;

    public function index(
        CatAuthService $catAuthService,
        CatErrorCodeService $service
    ) {
        $left = Carbon::now('Asia/Makassar')
            ->subDays(4)
            ->startOfDay();

        $right = Carbon::now('Asia/Makassar')->endOfDay();

        return $this->syncFaultCodes(
            $catAuthService,
            $service,
            $left,
            $right
        );
    }

    public function store(
        Request $request,
        CatAuthService $catAuthService,
        CatErrorCodeService $service
    ) {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $left = !empty($validated['start_date'])
            ? Carbon::parse($validated['start_date'], 'Asia/Makassar')->startOfDay()
            : Carbon::now('Asia/Makassar')->subDays(4)->startOfDay();

        $right = !empty($validated['end_date'])
            ? Carbon::parse($validated['end_date'], 'Asia/Makassar')->endOfDay()
            : Carbon::now('Asia/Makassar')->endOfDay();

        return $this->syncFaultCodes(
            $catAuthService,
            $service,
            $left,
            $right
        );
    }

    public function preview(
        Request $request,
        CatAuthService $catAuthService,
        CatErrorCodeService $service
    ) {
        try {
            $days = (int) $request->get('days', 7);

            if ($days < 1) {
                $days = 1;
            }

            if ($days > 30) {
                $days = 30;
            }

            $left = Carbon::now('Asia/Makassar')
                ->subDays($days)
                ->startOfDay();

            $right = Carbon::now('Asia/Makassar')->endOfDay();

            $response = $this->requestFaultCodes(
                $catAuthService,
                $left,
                $right,
                null,
                self::LIMIT
            );

            if (!$response->successful()) {
                return $this->catErrorResponse($response, 1);
            }

            $data = $response->json();

            $faultCodes = $data['faultCodes']
                ?? $data['data']['faultCodes']
                ?? [];

            $preview = $service->previewFaultCodes($faultCodes);

            return response()->json([
                'success' => true,
                'range' => [
                    'left' => $this->catDate($left),
                    'right' => $this->catDate($right),
                ],
                'cat_total' => count($faultCodes),
                'preview_total' => count($preview),
                'sample' => array_slice($preview, 0, 10),
                'response_metadata' => $data['responseMetadata'] ?? null,
                'full_data' => $preview,
            ]);
        } catch (\Throwable $e) {
            Log::error('CAT Fault Code Preview Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat preview CAT Fault Code.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bootstrapToken(
        Request $request,
        CatAuthService $catAuthService
    ) {
        $validated = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        try {
            $catAuthService->saveInitialRefreshToken(
                $validated['refresh_token'],
                86400
            );

            return response()->json([
                'success' => true,
                'message' => 'CAT refresh token berhasil disimpan.',
            ]);
        } catch (\Throwable $e) {
            Log::error('CAT Bootstrap Token Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan refresh token CAT.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function syncFaultCodes(
        CatAuthService $catAuthService,
        CatErrorCodeService $service,
        Carbon $left,
        Carbon $right
    ) {
        $limit = self::LIMIT;
        $cursor = null;
        $totalFetched = 0;
        $totalInserted = 0;
        $totalSkipped = 0;
        $page = 0;
        $startedAt = microtime(true);

        try {
            do {
                $page++;

                $response = $this->requestFaultCodes(
                    $catAuthService,
                    $left,
                    $right,
                    $cursor,
                    $limit
                );

                if (!$response->successful()) {
                    return $this->catErrorResponse($response, $page);
                }

                $data = $response->json();

                $faultCodes = $data['faultCodes']
                    ?? $data['data']['faultCodes']
                    ?? [];

                $count = count($faultCodes);

                if ($count === 0) {
                    break;
                }

                $totalFetched += $count;

                $result = $service->syncFaultCodesInChunks($faultCodes, 100);

                $inserted = $result['inserted'] ?? 0;
                $skipped = $result['skipped'] ?? 0;

                $totalInserted += $inserted;
                $totalSkipped += $skipped;

                $cursor = data_get($data, 'responseMetadata.nextCursor');

                Log::info('CAT Fault Code Sync Page', [
                    'page' => $page,
                    'fetched' => $count,
                    'inserted' => $inserted,
                    'skipped' => $skipped,
                    'next_cursor' => !empty($cursor) ? 'AVAILABLE' : null,
                ]);

                usleep(300000);
            } while (!empty($cursor));
        } catch (\Throwable $e) {
            Log::error('CAT Fault Code Sync Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'page' => $page,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat sync CAT Fault Code.',
                'error' => $e->getMessage(),
                'page' => $page,
                'fetched_before_error' => $totalFetched,
                'inserted_before_error' => $totalInserted,
                'skipped_before_error' => $totalSkipped,
            ], 500);
        }

        $duration = round(microtime(true) - $startedAt, 2);

        return response()->json([
            'success' => true,
            'range' => [
                'left' => $this->catDate($left),
                'right' => $this->catDate($right),
            ],
            'pages' => $page,
            'fetched' => $totalFetched,
            'inserted' => $totalInserted,
            'skipped' => $totalSkipped,
            'duration_seconds' => $duration,
        ]);
    }

    private function requestFaultCodes(
        CatAuthService $catAuthService,
        Carbon $left,
        Carbon $right,
        ?string $cursor,
        int $limit
    ): Response {
        $accessToken = $catAuthService->getAccessToken();

        $response = $this->sendCatRequest(
            $accessToken,
            $left,
            $right,
            $cursor,
            $limit
        );

        if ($response->status() === 401 || $response->status() === 403) {
            Log::warning('CAT access token ditolak. Mencoba refresh token.', [
                'status' => $response->status(),
            ]);

            $accessToken = $catAuthService->getAccessToken(true);

            $response = $this->sendCatRequest(
                $accessToken,
                $left,
                $right,
                $cursor,
                $limit
            );
        }

        return $response;
    }

    private function sendCatRequest(
        string $accessToken,
        Carbon $left,
        Carbon $right,
        ?string $cursor,
        int $limit
    ): Response {
        $params = [
            'limit' => $limit,
            'orderBy' => 'ASC',
            'sortBy' => 'name',
            'partyNumber' => self::PARTY_NUMBER,
        ];

        if (!empty($cursor)) {
            $params['cursor'] = $cursor;
        }

        $query = http_build_query($params);

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
                self::FAULT_URL . '?' . $query,
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

    private function catErrorResponse(Response $response, int $page)
    {
        Log::error('CAT API Error', [
            'status' => $response->status(),
            'page' => $page,
            'response' => $response->body(),
        ]);

        return response()->json([
            'success' => false,
            'status' => $response->status(),
            'page' => $page,
            'message' => 'CAT API request gagal.',
            'error' => $response->json() ?? $response->body(),
        ], $response->status());
    }
}
