<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orderStats = [
            'total' => Order::count(),
            'pending' => Order::where('payment_status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'completed' => Order::where('status', 'delivered')->count(),
        ];

        $recentOrders = Order::with('items')->latest()->take(6)->get();

        return view('admin.dashboard', compact('user', 'orderStats', 'recentOrders'));
    }
}