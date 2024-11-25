<?php

namespace App\Services;

use App\Models\Skpd;
use App\Models\Sspd;
use Illuminate\Support\Facades\DB;

class PaymentService
{
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

            // return 'x';
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
                    'usaha' => $payment->wajibPajak->namawpd,
                    'wajib_pajak' => $payment->wajibPajak->jenisw === 'badanUsaha' ? 'Wajib 1' : 'Wajib 2',
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
