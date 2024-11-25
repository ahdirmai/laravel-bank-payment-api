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
            DB::beginTransaction();

            return [
                'success' => true,
                'message' => 'Berhasil menemukan pembayaran.',
                'code' => 201,
                'data' => [
                    'status_transaksi' => $statusPayment,
                    'kode_pembayaran' => (string)$kodePembayaran,
                    'masa_pajak' => $payment->masapajak,
                    'besaran_pokok_pajak' => $payment->nilaipajak,
                    'usaha' => $payment->wajibPajak->namawpd,
                    'wajib_pajak' => $payment->wajibPajak->jenisw === 'badanUsaha' ? 'Wajib 1' : 'Wajib 2',
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