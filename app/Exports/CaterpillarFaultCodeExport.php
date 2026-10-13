<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CaterpillarFaultCodeExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithColumnFormatting
{
    use Exportable;

    private Carbon $startDate;
    private Carbon $stopDate;
    private string $selectedError;

    public function __construct(
        Carbon $startDate,
        Carbon $stopDate,
        string $selectedError = ''
    ) {
        $this->startDate = $startDate;
        $this->stopDate = $stopDate;
        $this->selectedError = $selectedError;
    }

    public function query(): Builder
    {
        $query = DB::connection('sqlsrv')
            ->table('DB_MACHINE_HEALTH.dbo.VW_OPR_CAT_ERROR_CODE AS A')
            ->leftJoin(
                'FOCUS.dbo.MAP_OBJECT AS B',
                'A.LOC_NAME',
                '=',
                'B.MAPOBJECTNAME'
            )
            ->leftJoin(
                'FOCUS.dbo.PRS_PERSONAL AS D',
                'A.OPR_NRP',
                '=',
                'D.NRP'
            )
            ->leftJoin(
                'FOCUS.dbo.FLT_VSASTATUS AS E',
                'A.VSA_STATUSID',
                '=',
                'E.VSA_STATUSID'
            )
            ->leftJoin(
                'FOCUS.dbo.FLT_VSAACTIVITY AS F',
                function ($join) {
                    $join->on(
                        'A.VSA_ACTIVITYID',
                        '=',
                        'F.VSA_ACTIVITYID'
                    )->where('F.VHC_TYPEID', 2);
                }
            )
            ->select([
                'A.Model',
                'A.Name',
                'A.Fault_Code',
                'A.Date',
                'A.Fault_Date',
                'A.Hours_Value',
                'A.Odometer_Value',
                'A.Service_Meter_Unit_Odo',
                'A.Lifetime_Fuel_Consumed_Value',
                'A.Service_Meter_Unit',
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
                'A.VSA_GROUPID',
                'A.VSA_STATUSID',
                'E.VSA_STATUSDESC',
                'A.VSA_ACTIVITYID',
                'F.VSA_ACTIVITYDESC',
                'B.LAYERNAME',
            ])
            ->where(
                'A.Fault_Date',
                '>=',
                $this->startDate->format('Y-m-d H:i:s')
            )
            ->where(
                'A.Fault_Date',
                '<=',
                $this->stopDate->format('Y-m-d H:i:s')
            );

        if ($this->selectedError !== '') {
            $query->where(
                'A.Fault_Code',
                '=',
                $this->selectedError
            );
        }

        return $query
            ->orderByDesc('A.Fault_Date')
            ->orderBy('A.Fault_Code');
    }

    public function headings(): array
    {
        return [
            'Model',
            'Unit',
            'Fault Code',
            'Date',
            'Fault Date',
            'Hours Meter',
            'Odometer',
            'Odometer Unit',
            'Lifetime Fuel Consumed',
            'Service Meter Unit',
            'Source',
            'Type',
            'Occurrences',
            'Longitude',
            'Latitude',
            'GPS Speed',
            'Location',
            'PLM Speed',
            'NIK Operator',
            'Operator Name',
            'Severity Low',
            'Severity Medium',
            'Severity High',
            'VSA Group ID',
            'VSA Status ID',
            'VSA Status',
            'VSA Activity ID',
            'VSA Activity',
            'Layer Name',
        ];
    }

    public function map($row): array
    {
        return [
            $row->Model ?? '',
            $row->Name ?? '',
            $row->Fault_Code ?? '',
            $this->formatDate($row->Date ?? null),
            $this->formatDate($row->Fault_Date ?? null),
            $row->Hours_Value ?? 0,
            $row->Odometer_Value ?? 0,
            $row->Service_Meter_Unit_Odo ?? '',
            $row->Lifetime_Fuel_Consumed_Value ?? 0,
            $row->Service_Meter_Unit ?? '',
            $row->Source ?? '',
            $row->Type ?? '',
            $row->Occurrences ?? 0,
            $row->GPS_LON ?? '',
            $row->GPS_LAT ?? '',
            $row->GPS_SPEED ?? 0,
            $row->LOC_NAME ?? '',
            $row->PLM_SPEED ?? 0,
            $row->OPR_NRP ?? '',
            $row->PERSONALNAME ?? '',
            $row->Severity_Level1 ?? 0,
            $row->Severity_Level2 ?? 0,
            $row->Severity_Level3 ?? 0,
            $row->VSA_GROUPID ?? '',
            $row->VSA_STATUSID ?? '',
            $row->VSA_STATUSDESC ?? '',
            $row->VSA_ACTIVITYID ?? '',
            $row->VSA_ACTIVITYDESC ?? '',
            $row->LAYERNAME ?? '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0.00',
            'G' => '#,##0.00',
            'I' => '#,##0.00',
            'N' => '0.000000',
            'O' => '0.000000',
            'P' => '#,##0.00',
            'R' => '#,##0.00',
            'S' => NumberFormat::FORMAT_TEXT,
        ];
    }

    private function formatDate($value): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
