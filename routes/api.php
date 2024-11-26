<?php

use App\Http\Controllers\Api\v1\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('api.payment.headers')->group(function () {
    Route::post('/inquiry', [PaymentController::class, 'inquiry']);
    Route::post('/payment', [PaymentController::class, 'payment']);
});
