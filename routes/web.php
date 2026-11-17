<?php

use App\Http\Controllers\CaterpillarMessageRefController;
use App\Http\Controllers\CatTokenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KomatsuMessageRefController;
use App\Http\Controllers\KomatsuRefMHParameterController;
use App\Http\Controllers\KomatsuSNRefController;
use App\Http\Controllers\CaterpillarFaultCodeController;
use App\Http\Controllers\CaterpillarVimsLastUpdateController;
use App\Http\Controllers\KomatsuFaultCodeController;
use App\Http\Controllers\KomatsuVhmsLastUpdateController;
use App\Http\Controllers\MachineHealthRankingController;
use App\Http\Controllers\OperatorInducedController;
use App\Http\Controllers\PeriodicOperatorInducedController;
use App\Http\Controllers\PeriodicSeriesController;
use App\Http\Controllers\RealtimeSeriesController;
use App\Http\Controllers\TPMSMachineHealthCaterpillarController;
use App\Http\Controllers\VhmHistoricalController;
use App\Http\Controllers\VHMSMachineHealthKomatsuController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;


Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('/cat/fault-codes', [CatTokenController::class, 'index']);
Route::get('/cat/fault-codes/preview', [CatTokenController::class, 'preview']);
Route::post('/cat/auth/bootstrap', [CatTokenController::class, 'bootstrapToken'])->withoutMiddleware([ValidateCsrfToken::class]);
Route::post('/cat/fault-codes/store', [CatTokenController::class, 'store'])->withoutMiddleware([ValidateCsrfToken::class]);

Route::get('/machine-health/komatsu', [VHMSMachineHealthKomatsuController::class, 'index'])->name('dashboard.machine-health.komatsu');
Route::get('/machine-health/caterpillar', [TPMSMachineHealthCaterpillarController::class, 'index'])->name('dashboard.machine-health.caterpillar');


//Operator Induced
Route::get('/machine-health/operator-induced', [OperatorInducedController::class, 'index'])->name('operator-induced.index');
Route::get('/machine-health/operator-induced/data', [OperatorInducedController::class, 'data'])->name('operator-induced.data');
Route::get('/machine-health/operator-induced/map-layers', [OperatorInducedController::class, 'mapLayers'])->name('operator-induced.map-layers');


//Realtime Viewer
Route::get('/realtime/series', [RealtimeSeriesController::class, 'index'])->name('realtime-series.index');
Route::get('/realtime/series/equipment', [RealtimeSeriesController::class, 'equipment'])->name('realtime-series.equipment');
Route::get('/realtime/series/data', [RealtimeSeriesController::class, 'data'])->name('realtime-series.data');


//Periodic Viewer
Route::get('/periodic/series', [PeriodicSeriesController::class, 'index'])->name('periodic-series.index');
Route::post('/periodic/series/data', [PeriodicSeriesController::class, 'data'])->name('periodic-series.data');

//Periodic Operator Induced YTD Resume
Route::get('/workshop/periodic-operator-induced', [PeriodicOperatorInducedController::class, 'index'])->name('workshop.periodic-operator-induced');

//Trend Historical Data
Route::get('/workshop/vhm-historical', [VhmHistoricalController::class, 'index'])->name('workshop.vhm-historical');
Route::get('/api/workshop/vhm-historical', [VhmHistoricalController::class, 'data'])->name('workshop.vhm-historical.data');

//Worst Equipment Rank by FOCUS
Route::get('/workshop/machine-health-ranking', [MachineHealthRankingController::class, 'index'])->name('workshop.machine-health-ranking');
Route::get('/api/workshop/machine-health-ranking', [MachineHealthRankingController::class, 'data'])->name('workshop.machine-health-ranking.data');

//Komatsu Message Ref
Route::get('/komatsu/message-ref', [KomatsuMessageRefController::class, 'index'])->name('komatsu.message-ref');
Route::post('/komatsu/message-ref/store', [KomatsuMessageRefController::class, 'store'])->name('komatsu.message-ref.store');
Route::post('/komatsu/message-ref/update', [KomatsuMessageRefController::class, 'update'])->name('komatsu.message-ref.update');
Route::delete('/komatsu/message-ref/delete/{code}', [KomatsuMessageRefController::class, 'destroy'])->name('komatsu.message-ref.delete');

Route::get('/komatsu/sn-ref', [KomatsuSNRefController::class, 'index'])->name('komatsu.sn-ref');
Route::post('/komatsu/sn-ref/update', [KomatsuSNRefController::class, 'update'])->name('komatsu.sn-ref.update');

Route::get('/komatsu/ref-mh-parameter', [KomatsuRefMHParameterController::class, 'index'])->name('komatsu.ref-mh-parameter');
Route::post('/komatsu/ref-mh-parameter/update', [KomatsuRefMhParameterController::class, 'update'])->name('komatsu.ref-mh-parameter.update');

//Komatsu Fault Code Raw Data
Route::get('/komatsu/fault-code-raw-data', [KomatsuFaultCodeController::class, 'index'])->name('komatsu.fault-code.raw-data');
Route::get('/komatsu/fault-code-raw-data/export', [KomatsuFaultCodeController::class, 'export'])->name('komatsu.fault-code.raw-data.export');

//Komatsu VIMS Last Update 20 Hours
Route::get('/komatsu/vhms-last-update', [KomatsuVhmsLastUpdateController::class, 'index'])->name('komatsu.vhms-last-update');

//Caterpillar Message Ref
Route::get('/caterpillar/message-ref', [CaterpillarMessageRefController::class, 'index'])->name('caterpillar.message-ref');
Route::post('/caterpillar/message-ref/store', [CaterpillarMessageRefController::class, 'store'])->name('caterpillar.message-ref.store');
Route::post('/caterpillar/message-ref/update', [CaterpillarMessageRefController::class, 'update'])->name('caterpillar.message-ref.update');
Route::delete('/caterpillar/message-ref/delete/{code}', [CaterpillarMessageRefController::class, 'destroy'])->name('caterpillar.message-ref.delete');

//Caterpillar VIMS Last Update 20 Hours
Route::get('/caterpillar/vims-last-update', [CaterpillarVimsLastUpdateController::class, 'index'])->name('caterpillar.vims-last-update');

//Caterpillar Fault Code Raw Data
Route::get('/caterpillar/fault-code-raw-data', [CaterpillarFaultCodeController::class, 'index'])->name('caterpillar.fault-code.raw-data');
Route::get('/caterpillar/fault-code-raw-data/export', [CaterpillarFaultCodeController::class, 'export'])->name('caterpillar.fault-code.raw-data.export');


