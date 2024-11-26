<?php

namespace App\Services;

use App\Models\Skpd;
use App\Models\Sspd;
use Illuminate\Support\Facades\DB;

class InquiryService
{
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
}
