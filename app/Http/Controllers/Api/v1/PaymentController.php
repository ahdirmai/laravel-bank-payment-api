<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentInquiryRequest;
use App\Models\Sspd;
use App\Services\InquiryService;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected $inquiryService;
    protected $paymentService;

    public function __construct(InquiryService $inquiryService, PaymentService $paymentService)
    {
        $this->inquiryService = $inquiryService;
        $this->paymentService = $paymentService;
    }

    public function inquiry(PaymentInquiryRequest $request)
    {
        
        $result = $this->inquiryService->handleInquiry($request->kode_pembayaran);
        if (!$result['success']) {
            if ($result['code'] == 500) {
                if (app()->environment('local')) {
                    return [
                        'name' => "Internal Server Error",
                        'status' => 500,
                        'message' => $result['message'],
                        'code' => 0
                    ];
                }
                return [
                    'name' => "Internal Server Error",
                    'status' => 500,
                    'message' => 'Terjadi kesalahan internal server.',
                    'code' => 0
                ];
            }
            return response()->json([
                'message' => $result['message'],
                'code' => $result['code']
            ], $result['code']);
        }

        return response()->json([
            'message' => 'Berhasil menemukan pembayaran.',
            'code' => 200,
            'data' => $result['data']
        ], 200);
    }

    public function payment(PaymentInquiryRequest $request)
    {
        return $result = $this->paymentService->processPayment($request->kode_pembayaran);

        if (!$result['success']) {
            if ($result['code'] == 500) {
                if (app()->environment('local')) {
                    return [
                        'name' => "Internal Server Error",
                        'status' => 500,
                        'message' => $result['message'],
                        'code' => 0
                    ];
                }
                return [
                    'name' => "Internal Server Error",
                    'status' => 500,
                    'message' => 'Terjadi kesalahan internal server.',
                    'code' => 0
                ];
            }

            return response()->json([
                'message' => $result['message'],
                'code' => $result['code']
            ], $result['code']);
        }

        return response()->json([
            'message' => 'Berhasil melunasi pembayaran.',
            'code' => 200,
            'data' => $result['data']
        ], 201);
    }
}
