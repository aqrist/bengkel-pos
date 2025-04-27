@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
            <h1 class="h3 mb-3 mb-sm-0">Detail Transaksi</h1>
            <div class="btn-group">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
                <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('transactions.print', $transaction) }}" class="btn btn-primary" target="_blank">Cetak</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Invoice</dt>
                            <dd class="col-sm-8">{{ $transaction->invoice_number }}</dd>

                            <dt class="col-sm-4">Tanggal</dt>
                            <dd class="col-sm-8">{{ $transaction->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-4">Kasir</dt>
                            <dd class="col-sm-8">{{ $transaction->user->name }}</dd>

                            <dt class="col-sm-4">Pembayaran</dt>
                            <dd class="col-sm-8">{{ ucfirst($transaction->payment_method) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Total Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Subtotal</dt>
                            <dd class="col-sm-8">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</dd>

                            <dt class="col-sm-4">Biaya Jasa</dt>
                            <dd class="col-sm-8">Rp {{ number_format($transaction->service_fee, 0, ',', '.') }}</dd>

                            <dt class="col-sm-4">Diskon</dt>
                            <dd class="col-sm-8">
                                @if ($transaction->discount_type && $transaction->discount_amount > 0)
                                    @if ($transaction->discount_type == 'percentage')
                                        {{ $transaction->discount_amount }}%
                                    @else
                                        Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}
                                    @endif
                                @else
                                    -
                                @endif
                            </dd>

                            <dt class="col-sm-4">
                                <h5 class="mb-0">Total</h5>
                            </dt>
                            <dd class="col-sm-8">
                                <h5 class="mb-0">Rp {{ number_format($transaction->total, 0, ',', '.') }}</h5>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Detail Produk</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
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
