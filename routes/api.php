<?php

use App\Http\Controllers\Api\v1\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth');


Route::get('/test', function (Request $request) {
    $dataObjekPajak = \App\Models\DataObjekPajak::all();
    $jenisPajak = \App\Models\JenisPajak::all();
    $objekPajak = \App\Models\ObjekPajak::all();
    $skpd = \App\Models\Skpd::all();
    $sptpd = \App\Models\Sptpd::all();
    $wajibPajak = \App\Models\WajibPajak::all();
    return response()->json([
        'dataObjekPajak' => $dataObjekPajak,
        'jenisPajak' => $jenisPajak,
        'objekPajak' => $objekPajak,
        'skpd' => $skpd,
        'sptpd' => $sptpd,
        'wajibPajak' => $wajibPajak,
    ]);
});

Route::middleware('api.payment.headers')->group(function () {

    Route::post('/inquiry', [PaymentController::class, 'inquiry']);
    Route::post('/payment', [PaymentController::class, 'payment']);
});
