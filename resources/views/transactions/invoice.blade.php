@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mt-4">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3>INVOICE</h3>
                            <h4>Bengkel Blok Q</h4>
                            <p>Palem Asri II Blok Q nomor 19<br>Telp: 021-12345678</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <p class="mb-1"><strong>Invoice:</strong> {{ $transaction->invoice_number }}</p>
                                <p class="mb-1"><strong>Tanggal:</strong>
                                    {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-1"><strong>Kasir:</strong> {{ $transaction->user->name }}</p>
                                <p class="mb-1"><strong>Pembayaran:</strong> {{ ucfirst($transaction->payment_method) }}
                                </p>
                            </div>
                        </div>

                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->details as $detail)
                                    <tr>
                                        <td>{{ $detail->product->name }}</td>
                                        <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Biaya Jasa:</strong></td>
                                    <td class="text-end">Rp {{ number_format($transaction->service_fee, 0, ',', '.') }}</td>
                                </tr>
                                @if ($transaction->discount_amount > 0)
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Diskon:</strong></td>
                                        <td class="text-end">
                                            @if ($transaction->discount_type == 'percentage')
                                                {{ $transaction->discount_amount }}%
                                            @else
                                                Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>Rp
                                            {{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="text-center mt-4">
                            <p>Terima kasih atas kunjungan Anda</p>
                            <button class="btn btn-primary d-print-none" onclick="window.print()">Cetak Invoice</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style media="print">
        @page {
            size: A4;
            margin: 1cm;
        }

        .navbar,
        .sidebar,
        .d-print-none {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }
    </style>
@endpush
