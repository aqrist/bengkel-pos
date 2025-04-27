<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'details.product'])
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'details.product']);
        return view('transactions.show', compact('transaction'));
    }

    public function invoice(Transaction $transaction)
    {
        $transaction->load(['user', 'details.product']);
        return view('transactions.invoice', compact('transaction'));
    }

    public function print(Transaction $transaction)
    {
        $transaction->load(['user', 'details.product']);
        return view('transactions.print', compact('transaction'));
    }
}
