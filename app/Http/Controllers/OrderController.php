<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use App\Enums\OrderStatus;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all()->sortByDesc('created_at');

        return view('admin.order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        return view('admin.order.show', compact('order', 'orderItems'));
    }

    public function updateStatus($id)
    {
        $order = Order::findOrFail($id);

        if (Auth::user()->role->role_name == 'admin' || Auth::user()->role->role_name == 'cashier') {
            if ($order->status === OrderStatus::PENDING) {
                $order->status = OrderStatus::SETTLEMENT;
            } elseif ($order->status === OrderStatus::COOKED) {
                $order->status = OrderStatus::SERVED;
            }
        } else {
            if ($order->status === OrderStatus::SETTLEMENT) {
                $order->status = OrderStatus::COOKED;
            }
        }
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order berhasil di ' . $order->status->value);
    }

    public function cooked($id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => OrderStatus::COOKED
        ]);
        return redirect()->route('orders.index')->with('success', 'Order berhasil di cooked');
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->status === OrderStatus::PENDING) {
            $order->status = OrderStatus::CANCEL;
            $order->save();
            return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->route('orders.index')->with('error', 'Hanya pesanan yang masih pending yang bisa dibatalkan.');
    }
}
