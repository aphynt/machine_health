<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CatAuthService
{
    private const ACCESS_CACHE_KEY =
        'cat_access_token';

    private const TOKEN_TABLE =
        'dbo.CAT_AUTH_TOKEN';

    /**
     * Ambil access token.
     *
     * false = gunakan cache jika masih aktif.
     * true  = paksa refresh token.
     */
    public function getAccessToken(
        bool $forceRefresh = false
    ): string {

        /*
         * Coba access token dari cache.
         */
        if (!$forceRefresh) {

            $cachedToken = Cache::get(
                self::ACCESS_CACHE_KEY
            );

            if (!empty($cachedToken)) {
                return $cachedToken;
            }
        }

        /*
         * Tidak ada access token /
         * dipaksa refresh.
         */
        return $this->refreshAccessToken();
    }

    /**
     * Tukar refresh_token menjadi access_token baru.
     */
    private function refreshAccessToken(): string
    {
        $tokenData = DB::connection('machine_health')
            ->table(self::TOKEN_TABLE)
            ->orderByDesc('ID')
            ->first();

        if (
            !$tokenData ||
            empty($tokenData->REFRESH_TOKEN)
        ) {
            throw new RuntimeException(
                'Refresh token CAT belum tersedia.'
            );
        }

        try {

            $refreshToken = trim(
                Crypt::decryptString(
                    $tokenData->REFRESH_TOKEN
                )
            );

        } catch (\Throwable $e) {

            throw new RuntimeException(
                'Refresh token CAT tidak dapat didekripsi.'
            );
        }

        if (empty($refreshToken)) {
            throw new RuntimeException(
                'Refresh token CAT kosong setelah didekripsi.'
            );
        }

        $tokenUrl =
            'https://signin.cat.com/' .
            '4f0f19d0-f44c-4a03-b8cb-ab327bd2b12b/' .
            'b2c_1a_p2_v1_signin_prod/' .
            'oauth2/v2.0/token';

        $clientId =
            '23f1628f-fa51-4612-ad8f-48931ed4b70f';

        $scope =
            'openid ' .
            'https://cwslogin.onmicrosoft.com/' .
            '4aebf00a-08b2-4d62-8625-57a79b68e28e/' .
            'user_impersonation ' .
            'profile ' .
            'offline_access';

        $response = Http::asForm()
            ->acceptJson()

            ->withHeaders([
                'Origin' =>
                    'https://vl.cat.com',

                'Referer' =>
                    'https://vl.cat.com/',
            ])

            ->timeout(30)
            ->connectTimeout(15)

            ->post(
                $tokenUrl,
                [
                    'client_id' =>
                        $clientId,

                    /*
                    * Tambahkan ini.
                    * Ini redirect_uri yang digunakan
                    * VisionLink saat memperoleh token.
                    */
                    'redirect_uri' =>
                        'https://vl.cat.com',

                    'scope' =>
                        $scope,

                    'refresh_token' =>
                        $refreshToken,

                    'grant_type' =>
                        'refresh_token',

                    'client_info' =>
                        '1',
                ]
            );

        if (!$response->successful()) {

            Log::error(
                'CAT refresh access token gagal',
                [
                    'status' =>
                        $response->status(),

                    /*
                    * Jangan pernah log refresh_token.
                    */
                    'response' =>
                        $response->body(),
                ]
            );

            throw new RuntimeException(
                'CAT token refresh gagal. HTTP '
                . $response->status()
                . ' - '
                . $response->body()
            );
        }

        $data = $response->json();

        $accessToken =
            $data['access_token'] ?? null;

        if (!$accessToken) {
            throw new RuntimeException(
                'CAT tidak mengembalikan access_token.'
            );
        }

        /*
        * Access token CAT = 3600 detik.
        */
        $expiresIn =
            (int) ($data['expires_in'] ?? 3600);

        /*
        * Cache sedikit lebih pendek:
        * 3600 - 120 = 3480 detik.
        */
        $cacheSeconds = max(
            60,
            $expiresIn - 120
        );

        Cache::put(
            self::ACCESS_CACHE_KEY,
            $accessToken,
            now()->addSeconds(
                $cacheSeconds
            )
        );

        /*
        * Sangat penting:
        * kalau CAT memberikan refresh_token BARU,
        * ganti token yang lama.
        */
        if (!empty($data['refresh_token'])) {

            $newRefreshToken =
                trim($data['refresh_token']);

            $refreshExpiresIn =
                (int) (
                    $data['refresh_token_expires_in']
                    ?? 86400
                );

            DB::connection('machine_health')
                ->table(self::TOKEN_TABLE)
                ->where(
                    'ID',
                    $tokenData->ID
                )
                ->update([
                    'REFRESH_TOKEN' =>
                        Crypt::encryptString(
                            $newRefreshToken
                        ),

                    'REFRESH_EXPIRES_AT' =>
                        now()->addSeconds(
                            $refreshExpiresIn
                        ),

                    'UPDATED_AT' =>
                        now(),
                ]);
        }

        Log::info(
            'CAT access token berhasil diperbarui',
            [
                'expires_in' =>
                    $expiresIn,

                'refresh_token_rotated' =>
                    !empty(
                        $data['refresh_token']
                    ),
            ]
        );

        return $accessToken;
    }

    /**
     * Bootstrap refresh token pertama kali.
     *
     * Dipakai SEKALI setelah login VisionLink.
     */
    public function saveInitialRefreshToken(
        string $refreshToken,
        int $expiresIn = 86400
    ): void {

        /*
        * Bersihkan spasi/newline hasil copy.
        */
        $refreshToken =
            trim($refreshToken);

        if (empty($refreshToken)) {
            throw new RuntimeException(
                'Refresh token kosong.'
            );
        }

        DB::connection('machine_health')
            ->table(self::TOKEN_TABLE)
            ->delete();

        DB::connection('machine_health')
            ->table(self::TOKEN_TABLE)
            ->insert([
                'REFRESH_TOKEN' =>
                    Crypt::encryptString(
                        $refreshToken
                    ),

                'REFRESH_EXPIRES_AT' =>
                    now()->addSeconds(
                        $expiresIn
                    ),

                'CREATED_AT' =>
                    now(),

                'UPDATED_AT' =>
                    now(),
            ]);

        Cache::forget(
            self::ACCESS_CACHE_KEY
        );
    }

    /**
     * Hapus access token cache.
     */
    public function clearAccessToken(): void
    {
        Cache::forget(
            self::ACCESS_CACHE_KEY
        );
    }
}
