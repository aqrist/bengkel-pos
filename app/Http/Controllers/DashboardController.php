<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Transaction::whereDate('created_at', today())->sum('total');
        $weekSales = Transaction::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->sum('total');
        $monthSales = Transaction::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('total');

        $recentTransactions = Transaction::with('user')
            ->latest()
            ->take(10)
            ->get();

        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Data for chart
        $salesData = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy('date')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'weekSales',
            'monthSales',
            'recentTransactions',
            'lowStockProducts',
            'salesData'
        ));
    }
}
