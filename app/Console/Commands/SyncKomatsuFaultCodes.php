<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Sinkronisasi satu arah SQL Server -> PostgreSQL (tidak mengubah sumber).
 * Perintah:
 *   php artisan komatsu:sync-fault-codes --dry-run --limit=500
 *   php artisan komatsu:sync-fault-codes --full
 *   php artisan komatsu:sync-fault-codes
 *
 * Catatan: --full membaca ulang seluruh riwayat, dan upsert akan memperbarui
 * field di PostgreSQL berdasarkan data SQL Server. Gunakan di luar jam sibuk.
 */
class SyncKomatsuFaultCodes extends Command
{
    protected $signature = 'komatsu:sync-fault-codes
        {--dry-run : Baca sumber dan validasi tanpa menulis ke PostgreSQL}
        {--full : Baca ulang seluruh histori sejak ID awal}
        {--from-id= : Mulai dari ID SQL Server tertentu (eksklusif)}
        {--overlap=1000 : Jumlah ID terakhir untuk dicek ulang pada mode normal}
        {--batch=500 : Jumlah baris per batch}
        {--limit=0 : Maksimum baris dalam satu eksekusi (0 = tidak dibatasi)}';

    protected $description = 'Sinkronkan OPR_KOMATSU_ERROR_CODE dari SQL Server ke PostgreSQL machine_health';

    private const SOURCE_CONNECTION = 'sqlsrv';
    private const SOURCE_TABLE = 'DB_MACHINE_HEALTH.dbo.OPR_KOMATSU_ERROR_CODE';
    private const TARGET_CONNECTION = 'pgsql_machine_health';
    private const TARGET_TABLE = 'public.opr_komatsu_error_code';

    /** Kolom disamakan dengan struktur PostgreSQL (semua huruf kecil). */
    private const COLUMNS = [
        'id', 'sn', 'code', 'from_smr', 'from_time', 'to_smr', 'to_time',
        'c_gc_view_flag', 'count', 'error_message', 'created_at',
        'gps_lon', 'gps_lat', 'gps_alt', 'gps_speed', 'gps_dir',
        'loc_id', 'loc_typeid', 'loc_name', 'loc_regionid',
        'plm_timestamp', 'plm_lf', 'plm_rf', 'plm_lr', 'plm_rr',
        'plm_payload', 'plm_speed', 'plm_inc', 'plm_slp', 'plm_bfs',
        'plm_alt', 'plm_statusid', 'opr_nrp', 'vsa_groupid',
        'vsa_statusid', 'vsa_activityid', 'mtr_hourmeter', 'mtr_odometer',
        'opr_reporttime', 'opr_shiftdate', 'opr_shiftno', 'opr_name',
        'clc_enginestatus', 'clc_movingstatus', 'clc_payloadstatus',
        'mtr_speed', 'mtr_rpm', 'vhm_engoilpress', 'vhm_boostpress',
        'mtr_enginecoolanttemperature', 'mtr_engineoiltemperature',
        'mtr_transmissionoiltemperature', 'mtr_brakeoiltemperature',
        'mtr_ambientairtemperature', 'mtr_fuellevel', 'mtr_fuelrate',
        'vhm_engspeed', 'vhm_cooltemp', 'vhm_hydoiltemp', 'vhm_tmoiltemp',
        'vhm_tcoiltemp', 'vhm_fbrakeoilpress', 'vhm_rbrakeoilpress', 'name',
    ];

    public function handle(): int
    {
        $batchSize = (int) $this->option('batch');
        $limit = (int) $this->option('limit');
        $overlap = (int) $this->option('overlap');
        $dryRun = (bool) $this->option('dry-run');
        $full = (bool) $this->option('full');
        $fromId = $this->option('from-id');

        if ($batchSize < 1 || $batchSize > 2000 || $limit < 0 || $overlap < 0 ||
            ($fromId !== null && (!ctype_digit((string) $fromId)))) {
            $this->error('Parameter tidak valid. Batch 1..2000, limit/overlap/from-id >= 0.');
            return self::FAILURE;
        }
        if ($full && $fromId !== null) {
            $this->error('Pilih salah satu dari --full atau --from-id.');
            return self::FAILURE;
        }

        try {
            $source = DB::connection(self::SOURCE_CONNECTION);
            $target = DB::connection(self::TARGET_CONNECTION);

            // Memastikan koneksi dan tabel dapat dibaca sebelum ada penulisan.
            $source->table(self::SOURCE_TABLE)->limit(1)->get();
            $target->table(self::TARGET_TABLE)->limit(1)->get();

            // Upsert dengan conflict target 'id' membutuhkan UNIQUE / PRIMARY KEY.
            $hasUniqueId = $target->selectOne(<<<'SQL'
SELECT EXISTS (
    SELECT 1
    FROM pg_index i
    JOIN pg_class t ON t.oid = i.indrelid
    JOIN pg_namespace n ON n.oid = t.relnamespace
    JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = i.indkey[0]
    WHERE n.nspname = 'public'
      AND t.relname = 'opr_komatsu_error_code'
      AND i.indisunique
      AND i.indnkeyatts = 1
      AND a.attname = 'id'
) AS ready
SQL
            );
            if (!$hasUniqueId || !$hasUniqueId->ready) {
                $this->error('Kolom id PostgreSQL harus memiliki PRIMARY KEY/UNIQUE tunggal. Tidak ada data ditulis.');
                return self::FAILURE;
            }

            if ($full) {
                $lastId = 0;
            } elseif ($fromId !== null) {
                $lastId = (int) $fromId;
            } else {
                $maxTargetId = (int) ($target->table(self::TARGET_TABLE)->max('id') ?? 0);
                $lastId = max(0, $maxTargetId - $overlap);
            }

            $this->info('Mulai setelah ID: ' . $lastId . ($dryRun ? ' [DRY RUN]' : ''));
            $total = 0;
            $batches = 0;
            $updateColumns = array_values(array_diff(self::COLUMNS, ['id']));

            while (true) {
                $take = $limit > 0 ? min($batchSize, $limit - $total) : $batchSize;
                if ($take <= 0) {
                    break;
                }

                $rows = $source->table(self::SOURCE_TABLE)
                    ->where('ID', '>', $lastId)
                    ->orderBy('ID')
                    ->limit($take)
                    ->get();

                if ($rows->isEmpty()) {
                    break;
                }

                $payload = [];
                foreach ($rows as $row) {
                    $normalized = array_change_key_case((array) $row, CASE_LOWER);
                    $mapped = [];
                    foreach (self::COLUMNS as $column) {
                        if (!array_key_exists($column, $normalized)) {
                            throw new \RuntimeException('Kolom sumber tidak ditemukan: ' . $column);
                        }
                        $value = $normalized[$column];
                        if ($value instanceof \DateTimeInterface) {
                            $value = $value->format($column === 'opr_shiftdate' ? 'Y-m-d' : 'Y-m-d H:i:s.u');
                        }
                        $mapped[$column] = $value;
                    }
                    $payload[] = $mapped;
                }

                // Semua kolom target berasal dari sumber; transaksi per batch.
                if (!$dryRun) {
                    $target->transaction(function () use ($target, $payload, $updateColumns): void {
                        $target->table(self::TARGET_TABLE)
                            ->upsert($payload, ['id'], $updateColumns);
                    });
                }

                $lastId = (int) end($payload)['id'];
                $total += count($payload);
                $batches++;
                $this->line("Batch {$batches}: " . count($payload) . " baris; ID terakhir {$lastId}");
            }

            $this->info("Selesai. " . ($dryRun ? 'Dibaca' : 'Di-upsert') . ": {$total} baris ({$batches} batch). Tidak ada data SQL Server yang diubah.");
            return self::SUCCESS;
        } catch (Throwable $e) {
            Log::error('Komatsu sync gagal', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            $this->error('Sinkronisasi gagal: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
