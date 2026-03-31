<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Enums\OrderStatus;

class PaymentCallbackController extends Controller
{
    public function receive(Request $request)
    {
        // 1. Tangkap seluruh data yang dikirim Midtrans
        $payload = $request->all();

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';

        // Ambil server key dari file .env (opsi config aplikasi Anda)
        $serverKey = config('midtrans.server_key');

        // 2. Verifikasi Keamanan: Buat ulang signature dengan SHA512 dan cocokkan
        $hashed = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        if ($hashed == $signatureKey) {
            // Signature Tervalidasi (Aman dari aksi peretasan)

            // Cari pesanan berdasarkan order_code
            $order = Order::where('order_code', $orderId)->first();

            if (!$order) {
                return response()->json(['message' => 'Order tidak ditemukan'], 404);
            }

            // 3. Update status ke tabel Order berdasarkan respon dari Midtrans
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                $order->status = OrderStatus::SETTLEMENT;
            } elseif ($transactionStatus == 'pending') {
                // $order->status = OrderStatus::PENDING; // Jika ada enum penyesuaian untuk Pending
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                // Bisa diset ke OrderStatus::CANCEL / Batal jika pelanggan gagal bayar
                // $order->status = OrderStatus::CANCEL; 
            }

            $order->save();

            // Beri tahu Midtrans pesannya sudah kita terima dengan sukses
            return response()->json(['message' => 'Callback diterima dan status pesanan diperbarui']);
        }

        // Kalau signature tidak cocok, kembalikan error (Forbidden)
        return response()->json(['message' => 'Signature tidak valid'], 403);
    }
}
