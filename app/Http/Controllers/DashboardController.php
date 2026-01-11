<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('grand_total');

        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->sum('grand_total');

        return view('admin.dashboard', compact('totalOrders', 'totalRevenue', 'todayOrders', 'todayRevenue'));
    }
}
