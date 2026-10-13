<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CatErrorCodeService
{
    private const CONNECTION = 'sqlsrv';
    private const TABLE = 'OPR_CAT_ERROR_CODE';

    public function previewFaultCodes(array $faultCodes): array
    {
        $result = [];

        foreach ($faultCodes as $item) {
            $result[] = $this->mapFaultCode($item);
        }

        return $result;
    }

    public function syncFaultCodesInChunks(array $faultCodes, int $chunkSize = 100): array
    {
        $inserted = 0;
        $skipped = 0;
        $invalid = 0;

        $chunks = array_chunk($faultCodes, $chunkSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                try {
                    $data = $this->mapFaultCode($item);

                    if (
                        empty($data['Asset_Serial_Number'])
                        || empty($data['Fault_Date'])
                    ) {
                        $invalid++;
                        continue;
                    }

                    $assetSerialNumber = trim((string) $data['Asset_Serial_Number']);
                    $faultDate = $data['Fault_Date'];
                    $faultCode = trim((string) ($data['Fault_Code'] ?? ''));
                    $source = trim((string) ($data['Source'] ?? ''));
                    $type = trim((string) ($data['Type'] ?? ''));
                    $cid = $this->normalizeCode($data['Fault_Code_Cid'] ?? 0);
                    $fmi = $this->normalizeCode($data['Fault_Code_Fmi'] ?? 0);
                    $eid = $this->normalizeCode($data['Fault_Code_Eid'] ?? 0);
                    $spn = $this->normalizeCode($data['Fault_Code_Spn'] ?? 0);

                    $query = DB::connection(self::CONNECTION)
                        ->table(self::TABLE)
                        ->where('Asset_Serial_Number', $assetSerialNumber)
                        ->where('Fault_Date', $faultDate)
                        ->whereRaw("ISNULL([Type], '') = ?", [$type])
                        ->whereRaw("ISNULL([Source], '') = ?", [$source])
                        ->whereRaw('ISNULL(Fault_Code_Cid, 0) = ?', [$cid])
                        ->whereRaw('ISNULL(Fault_Code_Fmi, 0) = ?', [$fmi])
                        ->whereRaw('ISNULL(Fault_Code_Eid, 0) = ?', [$eid])
                        ->whereRaw('ISNULL(Fault_Code_Spn, 0) = ?', [$spn]);

                    if (
                        (int) $cid === 0
                        && (int) $fmi === 0
                        && (int) $eid === 0
                        && (int) $spn === 0
                    ) {
                        $query->whereRaw("ISNULL(Fault_Code, '') = ?", [$faultCode]);
                    }

                    if ($query->exists()) {
                        $skipped++;
                        continue;
                    }

                    DB::connection(self::CONNECTION)
                        ->table(self::TABLE)
                        ->insert([
                            'Asset_Serial_Number' => $assetSerialNumber,
                            'Make' => $data['Make'],
                            'Model' => $data['Model'],
                            'Name' => $data['Name'],
                            'Fault_Code' => $faultCode,
                            'Fault_Code_Cid' => $cid,
                            'Fault_Code_Fmi' => $fmi,
                            'Fault_Code_Eid' => $eid,
                            'Fault_Code_Spn' => $spn,
                            'Severity' => $data['Severity'],
                            'Fault_Date' => $faultDate,
                            'Hours_Value' => $data['Hours_Value'],
                            'Service_Meter_Unit_Hours' => $data['Service_Meter_Unit_Hours'],
                            'Hours_Timestamp' => $data['Hours_Timestamp'],
                            'Odometer_Value' => $data['Odometer_Value'],
                            'Service_Meter_Unit_Odo' => $data['Service_Meter_Unit_Odo'],
                            'Odometer_Timestamp' => $data['Odometer_Timestamp'],
                            'Lifetime_Energy_Generated_Value' => $data['Lifetime_Energy_Generated_Value'],
                            'Service_Meter_Unit_Energy' => $data['Service_Meter_Unit_Energy'],
                            'Lifetime_Energy_Generated_Timestamp' => $data['Lifetime_Energy_Generated_Timestamp'],
                            'Lifetime_Fuel_Consumed_Value' => $data['Lifetime_Fuel_Consumed_Value'],
                            'Service_Meter_Unit' => $data['Service_Meter_Unit'],
                            'Lifetime_Fuel_Consumed_Timestamp' => $data['Lifetime_Fuel_Consumed_Timestamp'],
                            'Source' => $source,
                            'Type' => $type,
                            'Occurrences' => $data['Occurrences'],
                            'Fault_Location' => $data['Fault_Location'],
                            'CREATED_AT' => now(),
                            'Severity_Level1' => $data['Severity_Level1'],
                            'Severity_Level2' => $data['Severity_Level2'],
                            'Severity_Level3' => $data['Severity_Level3'],
                        ]);

                    $inserted++;
                } catch (\Throwable $e) {
                    Log::error('CAT Fault Code Insert Error', [
                        'message' => $e->getMessage(),
                        'serial' => data_get($item, 'assetMetadata.serialNumber'),
                        'fault_date' => data_get($item, 'startTime'),
                    ]);

                    $invalid++;
                }
            }
        }

        return [
            'inserted' => $inserted,
            'skipped' => $skipped,
            'invalid' => $invalid,
        ];
    }

    private function mapFaultCode(array $item): array
    {
        $asset = $item['assetMetadata'] ?? [];
        $faultCodeKey = $item['faultCodeKey'] ?? [];
        $severity = (int) data_get($item, 'severity.value', 0);
        $occurrences = (int) ($item['occurrenceCount'] ?? 0);

        $faultDate = $this->formatDate($item['startTime'] ?? null);
        $hoursTimestamp = $this->formatDate(
            data_get($asset, 'latestHoursReading.lastReportedTime')
        );
        $odometerTimestamp = $this->formatDate(
            data_get($asset, 'latestOdometerReading.lastReportedTime')
        );
        $energyTimestamp = $this->formatDate(
            data_get($asset, 'latestLifetimeEnergySourceConsumed.lastReportedTime')
        );
        $fuelTimestamp = $this->formatDate(
            data_get($asset, 'latestLifetimeFuelConsumed.lastReportedTime')
        );

        $faultLocation = data_get($item, 'location.address.streetAddress');
        $source = data_get($item, 'source.name', '');
        $type = data_get($item, 'faultType.name', '');

        $severityLevel1 = $severity === 1 ? $occurrences : 0;
        $severityLevel2 = $severity === 2 ? $occurrences : 0;
        $severityLevel3 = $severity === 3 ? $occurrences : 0;

        return [
            'Asset_Serial_Number' => trim((string) data_get($asset, 'serialNumber', '')),
            'Make' => data_get($asset, 'make', ''),
            'Model' => data_get($asset, 'model', ''),
            'Name' => data_get($asset, 'name', ''),
            'Fault_Code' => data_get($faultCodeKey, 'description', ''),
            'Fault_Code_Cid' => $this->normalizeCode(data_get($faultCodeKey, 'cid', 0)),
            'Fault_Code_Fmi' => $this->normalizeCode(data_get($faultCodeKey, 'fmi', 0)),
            'Fault_Code_Eid' => $this->normalizeCode(data_get($faultCodeKey, 'eid', 0)),
            'Fault_Code_Spn' => $this->normalizeCode(data_get($faultCodeKey, 'spn', 0)),
            'Severity' => $severity,
            'Fault_Date' => $faultDate,
            'Hours_Value' => data_get($asset, 'latestHoursReading.reading'),
            'Service_Meter_Unit_Hours' => data_get(
                $asset,
                'latestHoursReading.unitOfMeasure.value'
            ),
            'Hours_Timestamp' => $hoursTimestamp,
            'Odometer_Value' => data_get($asset, 'latestOdometerReading.reading'),
            'Service_Meter_Unit_Odo' => data_get(
                $asset,
                'latestOdometerReading.unitOfMeasure.value'
            ),
            'Odometer_Timestamp' => $odometerTimestamp,
            'Lifetime_Energy_Generated_Value' => data_get(
                $asset,
                'latestLifetimeEnergySourceConsumed.reading'
            ),
            'Service_Meter_Unit_Energy' => data_get(
                $asset,
                'latestLifetimeEnergySourceConsumed.unitOfMeasure.value'
            ),
            'Lifetime_Energy_Generated_Timestamp' => $energyTimestamp,
            'Lifetime_Fuel_Consumed_Value' => data_get(
                $asset,
                'latestLifetimeFuelConsumed.reading'
            ),
            'Service_Meter_Unit' => data_get(
                $asset,
                'latestLifetimeFuelConsumed.unitOfMeasure.value'
            ),
            'Lifetime_Fuel_Consumed_Timestamp' => $fuelTimestamp,
            'Source' => $source,
            'Type' => $type,
            'Occurrences' => $occurrences,
            'Fault_Location' => $faultLocation,
            'CREATED_AT' => now(),
            'Severity_Level1' => $severityLevel1,
            'Severity_Level2' => $severityLevel2,
            'Severity_Level3' => $severityLevel3,
        ];
    }

    private function normalizeCode(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (int) $value;
    }

    private function formatDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)
                ->timezone('Asia/Makassar')
                ->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
