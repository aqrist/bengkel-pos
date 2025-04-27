<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk #{{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 58mm;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
        }

        .header p {
            margin: 2px 0;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
        }

        .item-table td {
            padding: 2px 0;
        }

        .item-name {
            margin-bottom: 2px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .total-section td {
            padding: 3px 0;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <h2>BENGKEL POS</h2>
        <p>Jl. Contoh Alamat No. 123</p>
        <p>Telp: 021-12345678</p>
    </div>

    <hr>

    <div>
        <p>No: {{ $transaction->invoice_number }}</p>
        <p>Tgl: {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
        <p>Kasir: {{ $transaction->user->name }}</p>
    </div>

    <hr>

    <table class="item-table">
        @foreach ($transaction->details as $detail)
            <tr>
                <td colspan="2">
                    <div class="item-name">{{ $detail->product->name }}</div>
                    <div class="item-details">
                        <span>{{ $detail->quantity }} x {{ number_format($detail->price, 0, ',', '.') }}</span>
                        <span>{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>

    <hr>

    <table class="total-section">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if ($transaction->discount_amount > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right">
                    @if ($transaction->discount_type == 'percentage')
                        {{ $transaction->discount_amount }}%
                    @else
                        {{ number_format($transaction->discount_amount, 0, ',', '.') }}
                    @endif
                </td>
            </tr>
        @endif
        <tr>
            <td><strong>Total</strong></td>
            <td class="text-right"><strong>{{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Pembayaran</td>
            <td class="text-right">{{ ucfirst($transaction->payment_method) }}</td>
        </tr>
    </table>

    <hr>

    <div class="footer">
        <p>Terima kasih</p>
        <p>Barang yang sudah dibeli</p>
        <p>tidak dapat dikembalikan</p>
    </div>
</body>

</html>
