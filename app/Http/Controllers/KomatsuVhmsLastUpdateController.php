<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KomatsuVhmsLastUpdateController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::connection('machine_health')->select("
            SET NOCOUNT ON;
            EXEC DB_MACHINE_HEALTH.DBO.RPT_GET_LIST_KOMATSU_NOT_UPDATED_VHMS_20HOURS;
        ");
        $legend = [
            (object) ['Value' => 'x < 48', 'ColorState' => 'White'],
            (object) ['Value' => '48 <= x < 168', 'ColorState' => 'Orange'],
            (object) ['Value' => '>= 168', 'ColorState' => 'Red'],
        ];
        return view('komatsu.vhms-last-update', compact('data', 'legend'));
    }
}
