<?php

use App\Http\Controllers\API\v1\PaymentController;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get(
    '/',
    function (Request $request) {
        // return 'x';
        if ($request->header('Content-Type') !== 'application/json') {
            return response()->json([
                'name' => 'Bad Request',
                'message' => 'Content-Type must be application/json',
                'code' => 400,
                'status' => 400
            ], 400);
        }


        //     1) Read the header
        // 2) Get "Authorization" header value
        // 3) Remove "Basic " from the Authorization value
        // 4) Decode Authorization value from base64 with iso-8859-1 to a string
        // 5) Split the string with colon ":" to get the value username and password.
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Basic ')) {

            // remove "Basic " from the Authorization value

            return response()->json([
                'name' => 'Unauthorized',
                'message' => 'Invalid or missing Authorization header',
                'code' => 0,
                'status' => 401
            ], 401);
        }

        //try check username and password from the header is valid or not
        $authHeader = substr($authHeader, 6);

        // decode Authorization value from base64 with iso-8859-1 to a string
        $credentials = base64_decode($authHeader);

        // split the string with colon ":" to get the value username and password
        $username = explode(':', $credentials)[0];
        $password = explode(':', $credentials)[1];

        // return [$username, $password];
        //check if username and password is valid or not
        $user =  Pengguna::where('nik', $username)->first();
        if ($user && $user->sandi !== md5($password)) {
            return response()->json([
                'name' => 'Unauthorized',
                'message' => 'Invalid username or password',
                'code' => 0,
                'status' => 401
            ], 401);
        }

        if (!$user) {
            return response()->json([
                'name' => 'Unauthorized',
                'message' => 'Invalid username or password',
                'code' => 0,
                'status' => 401
            ], 401);
        }
        // }



        $timestamp = $request->header('X-Timestamp');

        if (
            !$timestamp && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $timestamp) !== 1
        ) {
            return response()->json([
                'name' => 'Bad Request',
                'message' => 'Invalid X-Timestamp format. Use YYYY-MM-DD H24:II:SS',
                'code' => 0,
                'status' => 400
            ], 400);
        }

        $xSignature = $request->header('X-Signature');
        if (!$xSignature) {
            return response()->json([
                'name' => 'Unauthorized',
                'message' => 'X-Signature header is required',
                'code' => 0,
                'status' => 401
            ], 401);
        }

        // Extract username from Basic Auth
        // $username = $this->extractUsernameFromBasicAuth($authHeader)[0];
        // $password = $this->extractUsernameFromBasicAuth($authHeader)[1];

        // Validate signature (use env for secret)
        $kodePayment = $request->input('kode_pembayaran');
        $signatureData = "{$username}.{$timestamp}.{$kodePayment}";
        $expectedSignature = base64_encode(hash_hmac('sha256', $signatureData, $password, true));

        if (
            $xSignature !== $expectedSignature
        ) {
            return response()->json([
                'name' => 'Unauthorized',
                'message' => 'Invalid X-Signature',
                'code' => 0,
                'status' => 401
            ], 401);
        }


        return [$request->header('Content-Type'), $request->header('Authorization'), $request->header('X-Timestamp'), $request->header('X-Signature')];
    }

);
