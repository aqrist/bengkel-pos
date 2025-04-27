@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Dashboard</h1>
        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Penjualan Hari Ini</h5>
                        <h2>Rp {{ number_format($todaySales, 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Penjualan Minggu Ini</h5>
                        <h2>Rp {{ number_format($weekSales, 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Penjualan Bulan Ini</h5>
                        <h2>Rp {{ number_format($monthSales, 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Grafik Penjualan Bulan Ini</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Stok Menipis</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($lowStockProducts as $product)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $product->name }}
                                    <span class="badge bg-danger rounded-pill">{{ $product->stock }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Transaksi Terakhir</h5>
                    </div>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->invoice_number }}</td>
                                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $transaction->user->name }}</td>
                                            <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                            <td>{{ ucfirst($transaction->payment_method) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = @json($salesData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.map(item => item.date),
                datasets: [{
                    label: 'Penjualan',
                    data: salesData.map(item => item.total),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
