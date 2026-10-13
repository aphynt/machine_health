<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TPMSMachineHealthCaterpillarController extends Controller
{
    //
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));

        $PERIODIC_OPERATOR_CAT_INDUCED_WITH_LOCATION = DB::select("
            SET NOCOUNT ON;
            EXEC [dbo].[PERIODIC_OPERATOR_CAT_INDUCED_WITH_LOCATION]
                @DATE1 = ?,
                @DATE2 = ?
        ", [$startDate, $endDate]);

        $PERIODIC_OPERATOR_INDUCED_CAT_PER_EQUIPMENTID = DB::select("
            SET NOCOUNT ON;
            EXEC [dbo].[PERIODIC_OPERATOR_INDUCED_CAT_PER_EQUIPMENTID]
                @DATE1 = ?,
                @DATE2 = ?
        ", [$startDate, $endDate]);

        $mapRows = DB::table('FOCUS.dbo.MAP_OBJECT')
            ->select([
                'MAPOBJECTID',
                'LAYERNAME',
                'MAPOBJECTTYPE',
                'MAPOBJECTNAME',
                'LONGITUDE',
                'LATITUDE',
                'GEOPOINTS',
                'TITLE',
                'FIELD_STRING_0',
            ])
            ->get();

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'PERIODIC_OPERATOR_CAT_INDUCED_WITH_LOCATION' => $PERIODIC_OPERATOR_CAT_INDUCED_WITH_LOCATION,
            'PERIODIC_OPERATOR_INDUCED_CAT_PER_EQUIPMENTID' => $PERIODIC_OPERATOR_INDUCED_CAT_PER_EQUIPMENTID,
            'mapRows' => $mapRows,
        ];

        return view('machine-health.caterpillar', compact('data'));
    }
}
