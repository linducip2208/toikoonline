<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Packing Slip {{ $order->code }}</title>
<style>body{font-family:sans-serif;font-size:12px;color:#111}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left}h1{font-size:20px}.right{text-align:right}.muted{color:#666}.box{border:1px solid #999;padding:8px;margin:8px 0}</style>
</head>
<body>
<h1>Packing Slip {{ $order->code }}</h1>
<p class="muted">Kurir: {{ $order->courier }} &nbsp;|&nbsp; Resi: {{ $order->tracking_number }} &nbsp;|&nbsp; Metode: {{ $order->shipping_method }}</p>
<div class="box"><strong>Alamat kirim:</strong><br>{{ $order->shipping_address }}</div>
<table>
<thead><tr><th>#</th><th>Produk</th><th>Varian</th><th class="right">Qty</th><th>Check</th></tr></thead>
<tbody>
@foreach($order->orderDetails as $i => $d)
<tr>
<td>{{ $i + 1 }}</td>
<td>{{ $d->product?->name ?? 'Produk #'.$d->product_id }}</td>
<td>{{ $d->variation }}</td>
<td class="right">{{ $d->quantity }}</td>
<td style="width:60px"></td>
</tr>
@endforeach
</tbody>
</table>
@if($order->additional_info)<p><strong>Catatan:</strong> {{ $order->additional_info }}</p>@endif
</body>
</html>
