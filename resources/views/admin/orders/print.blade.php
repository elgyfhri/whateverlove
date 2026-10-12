<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice {{ $order->invoice_no }} | WHATEVERLOVE</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Arial, sans-serif; color: #1a1c1b; background: #eee; padding: 32px 0; }
    .sheet { max-width: 780px; margin: 0 auto; background: #fff; padding: 48px 56px; border: 1px solid #ddd; }
    .toolbar { max-width: 780px; margin: 0 auto 16px; text-align: right; }
    .toolbar button { background: #000; color: #fff; border: 0; padding: 10px 22px; font-size: 13px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; cursor: pointer; border-radius: 6px; }
    .toolbar button:hover { opacity: .8; }

    .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 20px; }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand img { height: 48px; object-fit: contain; }
    .brand-name { font-size: 22px; font-weight: 800; letter-spacing: .05em; }
    .brand-sub { font-size: 10px; letter-spacing: .25em; text-transform: uppercase; color: #888; margin-top: 2px; }
    .invoice-label { text-align: right; font-size: 11px; letter-spacing: .25em; text-transform: uppercase; color: #888; }
    .invoice-no { font-size: 17px; font-weight: 800; letter-spacing: .04em; margin-top: 4px; }
    .invoice-date { font-size: 12px; color: #555; margin-top: 3px; }

    .meta { display: flex; gap: 40px; padding: 26px 0; border-bottom: 1px solid #e5e5e5; }
    .meta-col { flex: 1; }
    .meta h3 { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: #999; margin-bottom: 8px; }
    .meta p { font-size: 13px; line-height: 1.55; }
    .meta .name { font-weight: 700; font-size: 14px; }

    .badges { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .badge { font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: 4px 10px; border-radius: 999px; border: 1px solid #ddd; }
    .badge.paid { background: #e8f8eb; color: #157a37; border-color: #bfe6c9; }
    .badge.pending { background: #fff7e0; color: #9a6700; border-color: #f3dfa5; }
    .badge.failed, .badge.expired, .badge.cancelled { background: #fdecec; color: #b02a2a; border-color: #f2c4c4; }

    table.items { width: 100%; border-collapse: collapse; margin-top: 24px; }
    table.items th { text-align: left; font-size: 10px; letter-spacing: .18em; text-transform: uppercase; color: #999; padding: 10px 8px; border-bottom: 1px solid #000; }
    table.items td { font-size: 13px; padding: 12px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
    table.items .num { text-align: right; font-variant-numeric: tabular-nums; }
    table.items .price, table.items .amount { font-weight: 700; }
    .pname { font-weight: 700; }
    .pmeta { font-size: 11px; color: #888; margin-top: 2px; }

    .totals { margin-top: 20px; margin-left: auto; width: 300px; }
    .totals .row { display: flex; justify-content: space-between; font-size: 13px; padding: 5px 0; }
    .totals .row span:last-child { font-variant-numeric: tabular-nums; }
    .totals .row.sub span:last-child { font-weight: 700; }
    .totals .grand { border-top: 2px solid #000; margin-top: 8px; padding-top: 12px; font-weight: 800; font-size: 15px; }

    .foot { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e5e5e5; display: flex; justify-content: space-between; align-items: flex-end; }
    .foot .thanks { font-size: 12px; color: #777; line-height: 1.6; }
    .foot .sig { text-align: center; font-size: 11px; color: #555; }
    .foot .sig .line { border-top: 1px solid #444; width: 160px; margin: 34px 0 6px; }
    .generated { font-size: 10px; color: #aaa; margin-top: 24px; text-align: center; }

    @media print {
        body { background: #fff; padding: 0; }
        .sheet { border: 0; max-width: 100%; padding: 24px 8px; }
        .toolbar { display: none; }
    }
</style>
</head>
<body>
<div class="toolbar"><button onclick="window.print()">Cetak / Simpan PDF</button></div>

<div class="sheet">
    <div class="head">
        <div class="brand">
            <img src="{{ asset('assets/logo.png') }}" alt="WHATEVERLOVE">
            <div>
                <div class="brand-name">WHATEVERLOVE</div>
                <div class="brand-sub">Detail Pesanan</div>
            </div>
        </div>
        <div class="invoice-label">
            Invoice
            <div class="invoice-no">{{ $order->invoice_no }}</div>
            <div class="invoice-date">{{ $order->created_at->format('d F Y, H:i') }}</div>
            <div class="badges">
                <span class="badge {{ $order->payment_status }}">{{ $order->paymentStatusLabel() }}</span>
            </div>
        </div>
    </div>

    <div class="meta">
        <div class="meta-col">
            <h3>Penerima</h3>
            <p class="name">{{ $order->name }}</p>
            <p>{{ $order->phone }}</p>
            <p>{{ $order->email }}</p>
        </div>
        <div class="meta-col">
            <h3>Alamat Pengiriman</h3>
            <p>{{ $order->address }}</p>
            <p>{{ $order->district_name }}, {{ $order->city_name }}, {{ $order->province_name }}</p>
            @if($order->postal_code)<p>Kode Pos {{ $order->postal_code }}</p>@endif
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:60%">Item</th>
                <th class="num">Harga</th>
                <th class="num">Qty</th>
                <th class="num">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="pname">{{ $item->product_name }}</div>
                        <div class="pmeta">@if($item->size)Size {{ $item->size }} @endif</div>
                    </td>
                    <td class="num price">IDR {{ number_format($item->price, 0, '.', '.') }}</td>
                    <td class="num">{{ $item->qty }}</td>
                    <td class="num amount">IDR {{ number_format($item->price * $item->qty, 0, '.', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row"><span>Subtotal</span><span>IDR {{ number_format($order->subtotal, 0, '.', '.') }}</span></div>
        <div class="row sub"><span>Ongkos Kirim</span><span>IDR {{ number_format($order->shipping_cost, 0, '.', '.') }}</span></div>
        @if($order->shipping_courier)
            <div class="row"><span>Kurir</span><span>{{ strtoupper($order->shipping_courier) }} {{ $order->shipping_service }}</span></div>
            @if($order->shipping_estimate)
                <div class="row"><span>Estimasi</span><span>{{ $order->shippingEtaLabel() }}</span></div>
            @endif
        @endif
        @if($order->tracking_number)
            <div class="row"><span>No. Resi</span><span>{{ $order->tracking_number }}</span></div>
        @endif
        <div class="row grand"><span>TOTAL</span><span>IDR {{ number_format($order->total, 0, '.', '.') }}</span></div>
    </div>

    <div class="foot">
        <div class="thanks">
            Terima kasih telah berbelanja di WHATEVERLOVE.<br>
            Nomor resi akan dikirim setelah paket dikemas & dikirim.
        </div>
        <div class="sig">
            <div class="line"></div>
            Admin WHATEVERLOVE
        </div>
    </div>

    <div class="generated">Dicetak {{ now()->format('d F Y H:i') }} · WHATEVERLOVE</div>
</div>
</body>
</html>