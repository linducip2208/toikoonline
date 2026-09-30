<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DeliveryHistory;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('orderDetails.product')
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderDetails.product', 'deliveryHistories', 'refundRequests');

        // Timeline live: gabung delivery_histories DB (sumber: admin / webhook Biteship)
        $trackingTimeline = $order->deliveryHistories->sortBy('created_at')->map(fn($h) => [
            'label' => $h->status ?? $h->delivery_status,
            'date' => $h->created_at?->format('d M Y H:i'),
            'note' => $h->note ?? '',
        ])->values();

        // Token Snap tersimpan (untuk tombol "Bayar Sekarang" saat masih unpaid)
        $snapToken = null;
        if ($order->payment_status === 'unpaid' && $order->payment_details) {
            $details = json_decode($order->payment_details, true);
            $snapToken = $details['token'] ?? null;
        }

        // Alamat normalisasi untuk Alpine (hindari logika di Blade)
        $saRaw = array_filter((array) (json_decode($order->shipping_address, true) ?? []), fn($v) => is_string($v));
        $shippingAddr = [
            'name' => $saRaw['name'] ?? '',
            'phone' => $saRaw['phone'] ?? '',
            'full' => trim(implode(', ', array_filter([$saRaw['address'] ?? '', $saRaw['city'] ?? '', $saRaw['postal_code'] ?? $saRaw['postal'] ?? '']))),
        ];

        // Payload Alpine dirakit di sini (Blade @json tidak tahan ekspresi
        // multi-baris bersarang — pemotongan argumen merusak compile).
        $statusLabel = $order->payment_status !== 'paid' ? 'Menunggu Pembayaran' : match ($order->delivery_status) {
            'delivered' => 'Selesai', 'cancelled' => 'Dibatalkan',
            'on_delivery', 'picked_up' => 'Dikirim', default => 'Diproses',
        };
        $orderPayload = [
            'code' => $order->code,
            'date' => $order->created_at->format('d M Y, H:i').' WIB',
            'status' => $statusLabel,
            'subtotal' => (int) $order->orderDetails->sum(fn($d) => $d->price * $d->quantity),
            'shippingCost' => (int) $order->shipping_cost,
            'discount' => (int) $order->coupon_discount,
            'total' => (int) $order->grand_total,
            'items' => $order->orderDetails->map(fn($d) => [
                'name' => $d->product->name ?? 'Produk',
                'variant' => $d->variation,
                'price' => (int) $d->price,
                'qty' => (int) $d->quantity,
                'image' => $d->product->thumbnail_img ? asset($d->product->thumbnail_img) : asset('marketing/products/placeholder.jpg'),
            ])->values()->all(),
            'shippingAddress' => $shippingAddr,
            'paymentInfo' => ['method' => $order->payment_type ?? '-', 'bankName' => null, 'accountNumber' => null],
        ];

        // Langkah timeline nyata dari riwayat (tanggal dicocokkan kata kunci).
        $hist = $trackingTimeline->map(fn($t) => ['label' => strtolower($t['label']), 'date' => $t['date']]);
        $findDate = function (array $keywords) use ($hist) {
            foreach ($hist as $h) {
                foreach ($keywords as $kw) {
                    if (str_contains($h['label'], $kw)) return $h['date'];
                }
            }
            return null;
        };
        $isPaid = $order->payment_status === 'paid';
        $stage = ['Menunggu Pembayaran' => 0, 'Diproses' => 2, 'Dikirim' => 3, 'Selesai' => 4, 'Dibatalkan' => -1][$statusLabel] ?? 0;
        $timelineSteps = [
            ['label' => 'Pesanan Dibuat', 'date' => $order->created_at->format('d M Y, H:i'), 'done' => true],
            ['label' => 'Pembayaran Dikonfirmasi', 'date' => $findDate(['bayar', 'paid', 'konfirmasi']), 'done' => $isPaid],
            ['label' => 'Diproses', 'date' => $findDate(['proses', 'kemas', 'confirmed']), 'done' => $isPaid && $stage >= 2],
            ['label' => 'Dikirim', 'date' => $findDate(['kirim', 'ambil', 'courier', 'resi']), 'done' => $stage >= 3],
            ['label' => 'Selesai', 'date' => $findDate(['terima', 'selesai', 'delivered']), 'done' => $stage >= 4],
        ];

        $view = view()->exists('customer.orders.show') ? 'customer.orders.show' : 'customer.order-detail';

        return view($view, compact('order', 'trackingTimeline', 'snapToken', 'shippingAddr', 'orderPayload', 'timelineSteps'));
    }

    /** Pelanggan konfirmasi paket diterima (hanya saat sedang dikirim). */
    public function receive(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if (! in_array($order->delivery_status, ['picked_up', 'on_delivery'], true)) {
            return back()->with('error', 'Pesanan belum dalam pengiriman.');
        }
        $order->update(['delivery_status' => 'delivered']);
        DeliveryHistory::create([
            'order_id' => $order->id,
            'delivery_status' => 'delivered',
            'status' => 'Paket diterima pelanggan',
        ]);

        return back()->with('success', 'Terima kasih! Pesanan ditandai selesai.');
    }

    /** Pelanggan mengajukan refund (dicatat, diproses admin). */
    public function refund(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $request->validate([
            'refund_reason' => 'required|string|max:1000',
            'refund_amount' => 'required|numeric|min:1000|max:'.$order->grand_total,
        ]);
        if ($order->payment_status !== 'paid') {
            return back()->with('error', 'Hanya pesanan lunas yang bisa diajukan refund.');
        }
        if (RefundRequest::where('order_id', $order->id)->where('refund_status', 'pending')->exists()) {
            return back()->with('error', 'Pengajuan refund sedang diproses admin.');
        }
        RefundRequest::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'refund_status' => 'pending',
            'refund_reason' => $request->refund_reason,
            'refund_amount' => $request->refund_amount,
        ]);

        return back()->with('success', 'Pengajuan refund terkirim, admin akan memprosesnya.');
    }
}
