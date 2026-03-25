<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $items = Item::with('category')->where('is_active', true)->orderBy('name')->get();
        $categories = Category::orderBy('cat_name')->get();
        return view('admin.pos', compact('items', 'categories'));
    }

    public function store(Request $request, OrderService $orderService)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
            'table_number' => 'required|integer|min:1',
            'payment_method' => 'required|in:tunai,qris',
        ]);

        try {
            $order = $orderService->createKasirOrder(
                $request->items, 
                $request->table_number, 
                $request->payment_method, 
                $request->note, 
                Auth::id()
            );

            // ── QRIS: Generate Midtrans Snap Token ──
            if ($request->payment_method === 'qris') {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->order_code,
                        'gross_amount' => (int) $order->grand_total,
                    ],
                    'customer_details' => [
                        'first_name' => Auth::user()->fullname ?? 'Kasir',
                    ],
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);

                return response()->json([
                    'success' => true,
                    'message' => 'Silakan lakukan pembayaran QRIS',
                    'order_code' => $order->order_code,
                    'snap_token' => $snapToken,
                ]);
            }

            // ── TUNAI: langsung selesai ──
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'order_code' => $order->order_code,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order status to settlement after QRIS payment success.
     */
    public function updateStatus(Request $request, $orderCode)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();
        $order->status = OrderStatus::SETTLEMENT;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil! Pesanan telah di-settle.',
        ]);
    }
}

