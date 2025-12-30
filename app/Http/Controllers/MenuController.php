<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Item;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $tableNumber = $request->query('meja');
        if ($tableNumber) {
            Session::put('tableNumber', $tableNumber);
        }
        $items = Item::where('is_active', true)->orderBy('name', 'asc')->get();
        return view('customer.menu', compact('items', 'tableNumber'));
    }
}
