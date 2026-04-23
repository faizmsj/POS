<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota {{ $sale->invoice }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 24px; }
        .receipt-shell { max-width: {{ $receiptSettings['paper_width'] === '58mm' ? '280px' : '360px' }}; margin: 0 auto; background: #fff; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
        .row { display: flex; justify-content: space-between; gap: 12px; }
        .muted { color: #6b7280; font-size: 12px; }
        .title { font-size: 18px; font-weight: 700; }
        .divider { border-top: 1px dashed #cbd5e1; margin: 14px 0; }
        .item { padding: 8px 0; }
        .total { font-weight: 700; font-size: 16px; }
        .actions { max-width: 360px; margin: 0 auto 16px; display: flex; gap: 8px; }
        .btn { border: 0; background: #0f172a; color: #fff; padding: 10px 14px; border-radius: 10px; cursor: pointer; font-weight: 600; }
        .btn.secondary { background: #e2e8f0; color: #0f172a; }
        @media print {
            body { background: #fff; padding: 0; }
            .actions { display: none; }
            .receipt-shell { box-shadow: none; margin: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button class="btn" onclick="window.print()">Cetak Nota</button>
        <button class="btn secondary" onclick="window.history.back()">Kembali</button>
    </div>

    <div class="receipt-shell">
        <div style="text-align:center;">
            @if ($receiptSettings['show_logo'])
                <div style="font-size:28px; font-weight:800; color:#2563eb;">P</div>
            @endif
            <div class="title">{{ $receiptSettings['store_name'] }}</div>
            <div class="muted">{{ $sale->branch?->name }}</div>
            <div class="muted">{{ optional($sale->created_at)->format('d M Y H:i') }}</div>
        </div>

        <div class="divider"></div>

        <div class="row muted"><span>Invoice</span><span>{{ $sale->invoice }}</span></div>
        <div class="row muted"><span>Pelanggan</span><span>{{ $sale->customer?->name ?? 'Umum' }}</span></div>

        <div class="divider"></div>

        @foreach ($sale->items as $item)
            <div class="item">
                <div style="font-weight:600;">{{ $item->product?->name ?? 'Produk' }}</div>
                <div class="row muted">
                    <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach

        <div class="divider"></div>

        <div class="row muted"><span>Subtotal</span><span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span></div>
        <div class="row muted"><span>Diskon</span><span>Rp {{ number_format($sale->discount, 0, ',', '.') }}</span></div>
        <div class="row muted"><span>Pajak</span><span>Rp {{ number_format($sale->tax, 0, ',', '.') }}</span></div>
        <div class="row total"><span>Total</span><span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span></div>
        <div class="row muted"><span>Bayar</span><span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span></div>

        <div class="divider"></div>
        <div class="muted" style="text-align:center;">{{ $receiptSettings['receipt_footer'] }}</div>
    </div>
</body>
</html>
