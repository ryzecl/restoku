<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use App\Models\User;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * Create an order from Customer (MenuController)
     */
    public function createCustomerOrder(array $cart, $tableNumber, $paymentMethod, $note, array $userData)
    {
        return DB::transaction(function () use ($cart, $tableNumber, $paymentMethod, $note, $userData) {
            $totalAmount = 0;
            foreach ($cart as $item) {
                $totalAmount += $item['price'] * $item['qty'];
            }

            // Dapatkan atau buat user customer
            $user = User::firstOrCreate([
                'fullname' => $userData['fullname'],
                'phone' => $userData['phone'],
                'role_id' => 4
            ]);

            $order = Order::create([
                'order_code' => 'ORD-' . $tableNumber . '-' . time(),
                'user_id' => $user->id,
                'subtotal' => $totalAmount,
                'tax' => $totalAmount * 0.1,
                'grand_total' => $totalAmount + ($totalAmount * 0.1),
                'status' => OrderStatus::PENDING,
                'table_number' => $tableNumber,
                'payment_method' => $paymentMethod,
                'note' => $note,
            ]);

            foreach ($cart as $itemId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'] * $item['qty'],
                    'tax' => 0.1 * $item['price'] * $item['qty'],
                    'total_price' => $item['price'] * $item['qty'] + (0.1 * $item['price'] * $item['qty'])
                ]);
            }

            return $order;
        });
    }

    /**
     * Create an order from Cashier (PosController)
     */
    public function createKasirOrder(array $items, $tableNumber, $paymentMethod, $note, $userId)
    {
        return DB::transaction(function () use ($items, $tableNumber, $paymentMethod, $note, $userId) {
            $subtotal = 0;
            $cartItems = [];

            foreach ($items as $cartItem) {
                $item = Item::findOrFail($cartItem['id']);
                $itemTotal = $item->price * $cartItem['qty'];
                $subtotal += $itemTotal;

                $cartItems[] = [
                    'item' => $item,
                    'qty' => $cartItem['qty'],
                    'price' => $itemTotal,
                ];
            }

            $tax = $subtotal * 0.1;
            $grandTotal = $subtotal + $tax;

            $status = ($paymentMethod === 'tunai') ? OrderStatus::SETTLEMENT : OrderStatus::PENDING;

            $order = Order::create([
                'order_code' => 'POS-' . $tableNumber . '-' . time(),
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'status' => $status,
                'table_number' => $tableNumber,
                'payment_method' => $paymentMethod,
                'note' => $note,
            ]);

            foreach ($cartItems as $ci) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $ci['item']->id,
                    'quantity' => $ci['qty'],
                    'price' => $ci['price'],
                    'tax' => $ci['price'] * 0.1,
                    'total_price' => $ci['price'] + ($ci['price'] * 0.1),
                ]);
            }

            return $order;
        });
    }
}
