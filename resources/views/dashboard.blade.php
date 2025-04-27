@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Dashboard</h1>

        <!-- Sales Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Penjualan Hari Ini</h5>
                        <h3 class="mb-0">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Penjualan Minggu Ini</h5>
                        <h3 class="mb-0">Rp {{ number_format($weekSales, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Penjualan Bulan Ini</h5>
                        <h3 class="mb-0">Rp {{ number_format($monthSales, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Low Stock -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Grafik Penjualan Bulan Ini</h5>
                    </div>
                    <div class="card-body">
                        <div style="min-height: 300px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Stok Menipis</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($lowStockProducts as $product)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-truncate me-2">{{ $product->name }}</span>
                                    <span class="badge bg-danger rounded-pill">{{ $product->stock }}</span>
                                </li>
                            @empty
                                <li class="list-group-item px-0">Semua stok aman</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaksi Terakhir</h5>
            </div>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTransactions as $transaction)
                                <tr onclick="window.location='{{ route('transactions.show', $transaction) }}'"
                                    style="cursor: pointer;">
                                    <td>{{ $transaction->invoice_number }}</td>
                                    <td class="d-none d-sm-table-cell">{{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $transaction->user->name }}</td>
                                    <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                    <td class="d-none d-sm-table-cell">{{ ucfirst($transaction->payment_method) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                maintainAspectRatio: false,
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
