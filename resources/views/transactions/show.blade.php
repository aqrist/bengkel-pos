@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h1>Detail Transaksi #{{ $transaction->invoice_number }}</h1>
            <div>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
                <a href="{{ route('transactions.print', $transaction) }}" class="btn btn-primary" target="_blank">Cetak</a>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Invoice</th>
                                <td>{{ $transaction->invoice_number }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Kasir</th>
                                <td>{{ $transaction->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Pembayaran</th>
                                <td>{{ ucfirst($transaction->payment_method) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Total Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Subtotal</th>
                                <td>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Diskon</th>
                                <td>
                                    @if ($transaction->discount_type && $transaction->discount_amount > 0)
                                        @if ($transaction->discount_type == 'percentage')
                                            {{ $transaction->discount_amount }}%
                                        @else
                                            Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <h5>Total</h5>
                                </th>
                                <td>
                                    <h5>Rp {{ number_format($transaction->total, 0, ',', '.') }}</h5>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Detail Produk</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->details as $detail)
                                <tr>
                                    <td>{{ $detail->product->name }}</td>
                                    <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
