<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentInquiryRequest;
use App\Models\Skpd;
use App\Models\Sspd;
use App\Services\InquiryService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

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

        $result = $this->handleInquiry($request->kode_pembayaran);
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
        return $result = $this->processPayment($request->kode_pembayaran);

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

    public function handleInquiry(string $kodePembayaran)
    {

        $payment = Skpd::with('wajibPajak')->where('nosptpd', $kodePembayaran)->first();

        if (!$payment) {
            return [
                'success' => false,
                'message' => "Gagal menemukan pembayaran dengan kode pembayaran ({$kodePembayaran}).",
                'code' => 404
            ];
        }

        $statusPayment = Sspd::where('nosptpd', (string)$kodePembayaran)->exists() ? 'lunas' : 'menunggu';

        if ($statusPayment === 'lunas') {
            return [
                'success' => false,
                'message' => "Pembayaran dengan kode {$kodePembayaran} sudah lunas.",
                'code' => 422
            ];
        }

        try {
            return [
                'success' => true,
                'message' => 'Berhasil menemukan pembayaran.',
                'code' => 201,
                'data' => [
                    'status_transaksi' => $statusPayment,
                    'kode_pembayaran' => (string)$kodePembayaran,
                    'masa_pajak' => $payment->masapajak,
                    'besaran_pokok_pajak' => $payment->nilaipajak,
                    'usaha' => $payment->objectPajak->namaobjekpajak,
                    'wajib_pajak' => $payment->wajibPajak->namawpd,
                    'npwpd' => $payment->npwpd,
                ]
            ];
        } catch (\Throwable $th) {
            if (app()->environment('local')) {
                return [
                    'name' => "Internal Server Error",
                    'status' => 500,
                    'message' => $th->getMessage(),
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
    }

    public function processPayment(string $kodePembayaran)
    {
        $payment = Skpd::with('wajibPajak')->where('nosptpd', $kodePembayaran)->first();

        if (!$payment) {
            return [
                'success' => false,
                'message' => "Gagal menemukan pembayaran dengan kode pembayaran ({$kodePembayaran}).",
                'code' => 404
            ];
        }


        $statusPayment = Sspd::where('nosptpd', (string)$kodePembayaran)->exists() ? 'lunas' : 'menunggu';


        if ($statusPayment === 'lunas') {
            return [
                'success' => false,
                'message' => "Pembayaran dengan kode {$kodePembayaran} sudah lunas.",
                'code' => 422
            ];
        }


        try {

            DB::beginTransaction();

            $sspd = Sspd::create([
                'nosptpd' => (string)$kodePembayaran,
                'tglbayar' => now(),
                'jumlahbayar' => $payment->nilaipajak,
                'modebayar' => 'Bank',
                'kasir' => 'Bank NTT',
                'tglinput' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Berhasil melunasi pembayaran.',
                'code' => 201,
                'data' => [
                    'status_transaksi' => 'lunas',
                    'kode_pembayaran' => (string)$kodePembayaran,
                    'masa_pajak' => $payment->masapajak,
                    'besaran_pokok_pajak' => $payment->nilaipajak,
                    'usaha' => $payment->objectPajak->namaobjekpajak,
                    'wajib_pajak' => $payment->wajibPajak->namawpd,
                    'lunas_pada' => date('Y-m-d H:i:s', strtotime($sspd->tglbayar)),
                    'npwpd' => $payment->npwpd,
                ]
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            if (app()->environment('local')) {
                return [
                    'name' => "Internal Server Error",
                    'success' => false,
                    'message' => $th->getMessage(),
                    'code' => 500
                ];
            }
            return [
                'name' => "Internal Server Error",
                'success' => false,
                'message' => 'Terjadi kesalahan internal server.',
                'code' => 500
            ];
        }
    }
}
