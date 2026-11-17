<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaterpillarVimsLastUpdateController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::connection('machine_health')->select("
            SET NOCOUNT ON;
            EXEC DB_MACHINE_HEALTH.DBO.RPT_GET_LIST_CATERPILLAR_NOT_UPDATED_VHMS_20HOURS;
        ");
        $legend = DB::connection('machine_health')->select("SELECT 'x < 48' AS Value, 'White' AS ColorState UNION ALL SELECT '48 <= x < 168', 'Orange' UNION ALL SELECT '>= 168', 'Red'");
        return view('caterpillar.vims-last-update', compact('data', 'legend'));
    }
}
