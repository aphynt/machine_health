<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PeriodicOperatorInducedController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }
        $data = DB::connection('machine_health')->select(
            'SET NOCOUNT ON; EXEC DB_MACHINE_HEALTH.DBO.RPT_GET_YTD_OPERATOR_INDUCED @YEAR = ?',
            [(string) $year]
        );
        return view('workshop.periodic-operator-induced', compact('data', 'year'));
    }
}
