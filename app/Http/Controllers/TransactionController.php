<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

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

    public function edit(Transaction $transaction)
    {
        $transaction->load(['details.product']);
        $products = Product::where('stock', '>', 0)->get();
        return view('transactions.edit', compact('transaction', 'products'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'service_fee' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,non-cash'
        ]);

        DB::beginTransaction();
        try {
            // Kembalikan stok dari transaksi lama
            foreach ($transaction->details as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }

            // Hapus detail transaksi lama
            $transaction->details()->delete();

            $subtotal = 0;
            $items = [];

            // Proses produk baru
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi");
                }

                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->selling_price,
                    'subtotal' => $product->selling_price * $item['quantity']
                ];

                $subtotal += $product->selling_price * $item['quantity'];
            }

            // Hitung diskon
            $discountAmount = 0;
            if ($request->discount_type === 'percentage') {
                $discountAmount = ($request->discount_amount / 100) * $subtotal;
            } elseif ($request->discount_type === 'fixed') {
                $discountAmount = $request->discount_amount;
            }

            // Hitung total dengan biaya jasa
            $serviceFee = $request->service_fee;
            $total = $subtotal - $discountAmount + $serviceFee;

            // Update transaksi
            $transaction->update([
                'subtotal' => $subtotal,
                'discount_type' => $request->discount_type,
                'discount_amount' => $discountAmount,
                'service_fee' => $serviceFee,
                'total' => $total,
                'payment_method' => $request->payment_method
            ]);

            // Buat detail transaksi baru dan update stok
            foreach ($items as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);

                // Update stok produk
                $item['product']->decrement('stock', $item['quantity']);
            }

            DB::commit();
            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
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
