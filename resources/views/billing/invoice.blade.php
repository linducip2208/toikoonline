<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Invoice {{ $order->code }}</title>
<style>body{font-family:sans-serif;font-size:12px;color:#111}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left}h1{font-size:20px}.right{text-align:right}.muted{color:#666}</style>
</head>
<body>
<h1>Invoice {{ $order->code }}</h1>
<p class="muted">Tanggal: {{ $order->created_at?->format('d M Y H:i') }} &nbsp;|&nbsp; Status bayar: {{ $order->payment_status }} &nbsp;|&nbsp; Status kirim: {{ $order->delivery_status }}</p>
<p>Pelanggan: {{ $order->user?->name }} ({{ $order->user?->email }})</p>
<table>
<thead><tr><th>Produk</th><th>Varian</th><th class="right">Harga</th><th class="right">Qty</th><th class="right">Subtotal</th></tr></thead>
<tbody>
@foreach($order->orderDetails as $d)
<tr>
<td>{{ $d->product?->name ?? 'Produk #'.$d->product_id }}</td>
<td>{{ $d->variation }}</td>
<td class="right">Rp {{ number_format($d->price, 0, ',', '.') }}</td>
<td class="right">{{ $d->quantity }}</td>
<td class="right">Rp {{ number_format($d->price * $d->quantity, 0, ',', '.') }}</td>
</tr>
@endforeach
</tbody>
</table>
<p class="right">Pajak: Rp {{ number_format($order->tax_amount, 0, ',', '.') }}<br>
Ongkir: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}<br>
Diskon kupon: Rp {{ number_format($order->coupon_discount, 0, ',', '.') }}<br>
<strong>Grand total: Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong></p>
</body>
</html>
