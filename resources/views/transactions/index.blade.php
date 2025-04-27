@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Daftar Transaksi</h1>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th class="d-none d-sm-table-cell">Tanggal</th>
                                <th class="d-none d-md-table-cell">Kasir</th>
                                <th>Total</th>
                                <th class="d-none d-sm-table-cell">Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>
                                        {{ $transaction->invoice_number }}
                                        <div class="d-sm-none small text-muted">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">{{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $transaction->user->name }}</td>
                                    <td>
                                        Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                        <div class="d-sm-none small text-muted">
                                            {{ ucfirst($transaction->payment_method) }}
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">{{ ucfirst($transaction->payment_method) }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('transactions.show', $transaction) }}">Detail</a>
                                                </li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('transactions.edit', $transaction) }}">Edit</a></li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('transactions.invoice', $transaction) }}">Invoice</a>
                                                </li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('transactions.print', $transaction) }}"
                                                        target="_blank">Cetak</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection
