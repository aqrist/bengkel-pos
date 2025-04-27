@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h1>Daftar Transaksi</h1>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->invoice_number }}</td>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($transaction->payment_method) }}</td>
                                    <td>
                                        <a href="{{ route('transactions.show', $transaction) }}"
                                            class="btn btn-sm btn-info">Detail</a>
                                        <a href="{{ route('transactions.invoice', $transaction) }}"
                                            class="btn btn-sm btn-primary">Invoice</a>
                                        <a href="{{ route('transactions.print', $transaction) }}"
                                            class="btn btn-sm btn-secondary" target="_blank">Cetak</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection
