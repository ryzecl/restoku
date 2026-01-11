<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

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
            $order->status = 'settlement';
        } else {
            $order->status = 'cooked';
        }
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order berhasil di ' . $order->status);
    }

    public function cooked($id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'cooked'
        ]);
        return redirect()->route('orders.index')->with('success', 'Order berhasil di cooked');
    }
}
