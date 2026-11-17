<?php
namespace App\Exports;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
class KomatsuFaultCodeExport implements FromQuery, WithHeadings
{
    use Exportable;
    private Carbon $startDate;
    private Carbon $stopDate;
    private string $selectedError;

    public function __construct($startDate, $stopDate, ?string $selectedError = null)
    {
        $this->startDate = $startDate instanceof Carbon ? $startDate->copy() : Carbon::parse($startDate);
        $this->stopDate = $stopDate instanceof Carbon ? $stopDate->copy() : Carbon::parse($stopDate);
        $this->selectedError = $selectedError ?? '';
    }

    public function query(): Builder
    {
        $query = DB::connection('sqlsrv')
            ->table('DB_MACHINE_HEALTH.dbo.VW_OPR_KOMATSU_ERROR_CODE AS A')
            ->select('A.*')
            ->where('A.From_Date_Time', '>=', $this->startDate->format('Y-m-d H:i:s'))
            ->where('A.From_Date_Time', '<', $this->stopDate->format('Y-m-d H:i:s'));
        if ($this->selectedError !== '') {
            $query->where('A.Error_Message', '=', $this->selectedError);
        }
        return $query
            ->orderByDesc('A.From_Date_Time')
            ->orderBy('A.NAME')
            ->orderBy('A.Code');
    }

    public function headings(): array
    {
        return [
            'ID',
            'SN',
            'Code',
            'From SMR',
            'From Time',
            'From Epoch Time',
            'From Date Time',
            'To SMR',
            'To Time',
            'To Epoch Time',
            'To Date Time',
            'C-GC View Flag',
            'Count',
            'Error Message',
            'Created At',
            'GPS Longitude',
            'GPS Latitude',
            'GPS Altitude',
            'GPS Speed',
            'GPS Direction',
            'Location ID',
            'Location Type ID',
            'Location Name',
            'Location Region ID',
            'PLM Timestamp',
            'PLM LF',
            'PLM RF',
            'PLM LR',
            'PLM RR',
            'PLM Payload',
            'PLM Speed',
            'PLM Inclination',
            'PLM Slope',
            'PLM BFS',
            'PLM Altitude',
            'PLM Status ID',
            'Operator NRP',
            'VSA Group ID',
            'VSA Status ID',
            'VSA Activity ID',
            'Hour Meter',
            'Odometer',
            'Operator Report Time',
            'Operator Shift Date',
            'Operator Shift Number',
            'Operator Name',
            'Engine Status',
            'Moving Status',
            'Payload Status',
            'Meter Speed',
            'RPM',
            'Engine Oil Pressure',
            'Boost Pressure',
            'Engine Coolant Temperature',
            'Engine Oil Temperature',
            'Transmission Oil Temperature',
            'Brake Oil Temperature',
            'Ambient Air Temperature',
            'Fuel Level',
            'Fuel Rate',
            'Engine Speed',
            'Coolant Temperature',
            'Hydraulic Oil Temperature',
            'Transmission Oil Temperature VHM',
            'Torque Converter Oil Temperature',
            'Front Brake Oil Pressure',
            'Rear Brake Oil Pressure',
            'Equipment Name',
        ];
    }
}
