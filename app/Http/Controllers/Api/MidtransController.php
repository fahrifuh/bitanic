<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FarmerTransaction;
use App\Models\Subscription;
use App\Models\Transaction;
use Heyharpreetsingh\FCM\Facades\FCMFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function paymentRedirect(Request $request){
        $transaction = Transaction::query()
            ->firstWhere('code', $request->order_id);

        if (!$transaction) {
            $transaction = FarmerTransaction::query()
                ->firstWhere('code', $request->order_id);
        }

        if (!$transaction) {
            $transaction = Subscription::query()
                ->firstWhere('code', $request->order_id);
        }

        if (!$transaction) {
            Log::error('Kode transaksi tidak ditemukan pada sistem');
            return response()->json([
                'message' => 'Kode transaksi tidak ditemukan pada sistem'
            ], 404);
        }

        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');

            $status = (object) \Midtrans\Transaction::status($request->order_id);

            if (
                $this->validateTransactionStatus($status->transaction_status) &&
                $transaction->status != $status->transaction_status
            ) {
                $message = null;
                $title = null;

                switch ($status->transaction_status) {
                    case 'settlement':
                        $message = "Produk kamu sudah dibayar. Harap siapkan produk untuk dikirim ke pelangganmu.";
                        $title = "Pesanan Telah dibayar";
                        break;
                    case 'cancel':
                        $message = "Pembelian produk kamu dibatalkan pelanggan.";
                        $title = "Pesanan Dibatalkan";
                        break;
                    case 'expire':
                        $message = "Pelanggan tidak membayar produk kamu, produk menjadi kadaluarsa.";
                        $title = "Pesanan Kadaluarsa";
                        break;
                    case 'failure':
                        $message = "Terjadi kegagalan dalam pembayaran pesanan.";
                        $title = "Pesanan Gagal";
                        break;

                    default:
                        # code...
                        break;
                }

                if ($message) {
                    // buat agar bisa mengirim ke 2 user.
                    FCMFacade::send([
                        "message" => [
                            "token" => "ctZP-eyHStCfH2Arm4KCo4:APA91bF_NCP2MNkQnpH38WS3QVZa05y2JM8iqhrJ2n_b7UbE4SC83EejpA3h9ry0NHGT27Ymc57XbwXHAr9ns2FWA2eZGpECMLgvg7VmdaRjR-YI5JQ5An4",
                            "notification" => [
                                "body" => $message,
                                "title" => $title
                            ]
                        ]
                    ]);
                }

                $transaction->status = $status->transaction_status;
                $transaction->save();
            }

            Log::info("Success Purchase");
            return response()->json([
                'message' => "Berhasil diupdate!"
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
            return response()->json($th->getMessage(), 500);
        }
    }

    private function validateTransactionStatus(String $transaction_status) : bool {
        return in_array($transaction_status, [
            'pending',
            'capture',
            'settlement',
            'deny',
            'cancel',
            'expire',
            'failure',
            // 'refund',
            // 'chargeback',
            // 'partial_refund',
            // 'partial_chargeback',
            'authorize',
        ]);
    }
}
