<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableToken;
use App\Enums\OrderStatus;
use App\Services\OrderService;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function scanTable($tableNumber)
    {
        $token = TableToken::where('table_number', $tableNumber)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if ($token) {
            Session::put('tableNumber', $tableNumber);
            Session::put('tableToken', $token->token);
            return redirect()->route('menu')->with('success', "Meja $tableNumber berhasil diakses!");
        }

        return redirect()->route('login')->with('error', "Meja $tableNumber belum dibuka oleh kasir. Silahkan panggil pelayan.");
    }

    public function index(Request $request)
    {
        // Akses menu dicek dari session yang di-set via scanTable
        $tableNumber = Session::get('tableNumber');
        $tokenString = Session::get('tableToken');

        if (!$tableNumber || !$tokenString) {
            return redirect()->route('login')->with('error', 'Akses menu harus melalui scan QR Code di meja yang sudah dibuka.');
        }

        $token = TableToken::findValidToken($tokenString);
        if (!$token || $token->table_number != $tableNumber) {
            Session::forget(['tableNumber', 'tableToken']);
            return redirect()->route('login')->with('error', 'Sesi pesanan Anda sudah berakhir. Silahkan scan QR ulang / lapor kasir.');
        }

        $items = Item::where('is_active', true)->orderBy('name', 'asc')->get();
        return view('customer.menu', ['items' => $items, 'tableNumber' => $tableNumber]);
    }

    // cart
    public function cart()
    {
        $cart = Session::get('cart');
        return view('customer.cart', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        $menuId = $request->input('id');
        $menu = Item::find($menuId);

        if (!$menu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan'
            ]);
        }

        $cart = Session::get('cart');

        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['qty']++;
        } else {
            $cart[$menu->id] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'image' => $menu->img,
                'qty' => 1
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil ditambahkan ke keranjang',
            'cart' => $cart
        ]);
    }

    public function updateCart(Request $request)
    {
        $itemId = $request->input('id');
        $newQty = $request->input('qty');

        if ($newQty <= 0) {
            return response()->json([
                'success' => 'false',
            ]);
        }

        $cart = Session::get('cart');

        if (isset($cart[$itemId])) {
            $cart[$itemId]['qty'] = $newQty;
            Session::put('cart', $cart);
            Session::flash('success', 'Berhasil memperbarui keranjang');

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function removeItemFromCart(Request $request)
    {
        $itemId = $request->input('id');
        $cart = Session::get('cart');


        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            Session::put('cart', $cart);
            Session::flash('success', 'Berhasil menghapus item dari keranjang');

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function clearCart()
    {
        Session::forget('cart');
        Session::flash('success', 'Berhasil mengosongkan keranjang');

        return redirect()->route('cart');
    }

    // checkout

    public function checkout()
    {
        $cart = Session::get('cart');

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong');
        }

        $tableNumber = Session::get('tableNumber');

        return view('customer.checkout', compact('cart', 'tableNumber'));
    }

    public function storeOrder(Request $request, OrderService $orderService)
    {
        $cart = Session::get('cart');
        $tableNumber = Session::get('tableNumber');
        $tokenString = Session::get('tableToken');

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong');
        }

        // Honeypot check (Anti-spam Layer 3)
        if ($request->filled('website')) {
            abort(403, 'Spam detected.');
        }

        // Validasi Token
        if (!$tableNumber || !$tokenString) {
            return redirect()->route('menu')->with('error', 'Akses tidak valid. Silahkan scan QR ulang.');
        }

        $token = TableToken::findValidToken($tokenString);
        if (!$token || $token->table_number != $tableNumber) {
            return redirect()->route('menu')->with('error', 'Sesi pesanan Anda sudah kadaluarsa. Silahkan minta QR baru ke kasir.');
        }

        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $order = $orderService->createCustomerOrder(
                $cart,
                $tableNumber,
                $request->payment_method,
                $request->note,
                $request->only(['fullname', 'phone'])
            );
        } catch (\Exception $e) {
            return redirect()->route('checkout')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        Session::forget('cart');

        if ($request->payment_method == 'tunai') {
            return redirect()->route('checkout.success', ['orderId' => $order->order_code])->with('success', 'Pesanan berhasil dibuat');
        } else {
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
                    'first_name' => $order->user->fullname ?? 'Guest',
                    'phone' => $order->user->phone,
                ],
                'payment_type' => 'qris',
            ];

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);

                return response()->json([
                    'status' => 'success',
                    'snap_token' => $snapToken,
                    'order_code' => $order->order_code,
                ]);
            } catch (\Exception $e) {
                return redirect()->route('checkout')->with('error', 'Gagal mendapatkan token pembayaran');
            }
        }
    }

    public function checkoutSuccess($orderId)
    {
        $order = Order::where('order_code', $orderId)->first();

        if (!$order) {
            return redirect()->route('menu')->with('error', 'Pesanan tidak ditemukan');
        }

        $orderItems = OrderItem::where('order_id', $order->id)->get();

        // if ($order->payment_method == 'qris') {
        //     $order->status = OrderStatus::SETTLEMENT;
        //     $order->save();
        // }

        return view('customer.success', compact('order', 'orderItems'));
    }
}
