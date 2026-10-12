<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['user', 'items'])
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items']);

        return view('admin.orders.show', compact('order'));
    }

    public function print(Order $order): View
    {
        $order->load('items');

        return view('admin.orders.print', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,shipped,delivered,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:64'],
        ]);

        $order->status = $data['status'];

        if ($data['status'] === 'shipped') {
            $order->tracking_number = $data['tracking_number'] ?? null;
            $order->shipped_at = $order->shipped_at ?? now();
        }

        if ($data['status'] === 'delivered') {
            $order->tracking_number = $order->tracking_number ?? $data['tracking_number'] ?? null;
            $order->shipped_at = $order->shipped_at ?? now();
            $order->delivered_at = $order->delivered_at ?? now();
        }

        if ($data['status'] === 'pending' || $data['status'] === 'processing' || $data['status'] === 'cancelled') {
            $order->tracking_number = $data['status'] === 'processing' ? null : $order->tracking_number;
        }

        if ($data['status'] === 'cancelled') {
            $order->releaseStock();
        }

        $order->save();

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}