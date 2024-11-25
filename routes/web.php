<?php

use App\Http\Controllers\API\v1\PaymentController;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get(
    '/',
    function (Request $request) {
        return response()->json([
            'message' => 'Welcome to the API',
            'code' => 200
        ], 200);
    }

);
