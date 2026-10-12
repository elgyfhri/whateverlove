<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Halaman kembali dari Snap (callbacks.finish). Update status bila perlu,
     * lalu arahkan ke halaman sukses order.
     */
    public function returnPage(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 404);

        $service = app(MidtransService::class);
        $fromQuery = $request->query('transaction_status');

        try {
            if ($fromQuery) {
                $service->applyStatus($order, $request->query());
            } else {
                $service->syncFromMidtrans($order);
            }
        } catch (\Throwable $e) {
            Log::warning('Midtrans return sync failed', ['order' => $order->invoice_no, 'error' => $e->getMessage()]);
        }

        return redirect()->route('checkout.success', $order)->with('success', 'Status pembayaran diperbarui.');
    }

    /**
     * Cek status terbaru dari Midtrans (dipakai pemilik order di halaman sukses).
     */
    public function status(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 404);

        try {
            app(MidtransService::class)->syncFromMidtrans($order);
        } catch (\Throwable $e) {
            Log::warning('Midtrans status sync failed', ['order' => $order->invoice_no, 'error' => $e->getMessage()]);

            return back()->with('error', 'Tidak dapat terhubung ke gateway pembayaran. Coba lagi beberapa saat.');
        }

        return back()->with('status', 'Status pembayaran diperbarui.');
    }

    /**
     * Buat ulang transaksi Snap (batalkan dulu lalu bikin baru) supaya
     * pemilihan metode pembayaran di halaman Snap bersih lagi.
     */
    public function rechoose(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 404);

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order)
                ->with('error', 'Pembayaran sudah dikonfirmasi.');
        }

        $service = app(MidtransService::class);

        if (! $service->enabled()) {
            return redirect()->route('checkout.success', $order)->with('error', 'Gateway pembayaran belum aktif.');
        }

        try {
            $service->cancelTransaction($order->invoice_no);

            $snap = $service->createTransaction($service->payloadFor($order));

            $order->update([
                'payment_status' => 'pending',
                'payment_reference' => $snap['token'] ?? null,
                'payment_redirect_url' => $snap['redirect_url'] ?? null,
                'payment_raw' => json_encode($snap),
            ]);

            return redirect()->away($snap['redirect_url']);
        } catch (\Throwable $e) {
            Log::warning('Midtrans rechoose failed', ['order' => $order->invoice_no, 'error' => $e->getMessage()]);

            return redirect()->route('checkout.success', $order)
                ->with('error', 'Gagal membuat ulang transaksi pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Webhook notifikasi dari Midtrans (production). Update order berdasarkan payload.
     */
    public function notification(Request $request): JsonResponse
    {
        $service = app(MidtransService::class);
        $data = $request->json()->all();

        if (! $service->verifySignature($data)) {
            Log::warning('Midtrans notification rejected: invalid signature', [
                'order_id' => $data['order_id'] ?? null,
            ]);

            return response()->json(['ok' => false], '403');
        }

        $order = Order::where('invoice_no', $data['order_id'] ?? null)->first();

        if ($order && ($data['status_code'] ?? null) && ($data['transaction_status'] ?? null)) {
            $service->applyStatus($order, $data);

            return response()->json(['ok' => true]);
        }

        Log::warning('Midtrans notification ignored', ['data' => $data]);

        return response()->json(['ok' => false], 422);
    }
}
