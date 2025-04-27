<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('pos.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,non-cash'
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $items = [];

            // Calculate subtotal and prepare items
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

            // Calculate discount
            $discountAmount = 0;
            if ($request->discount_type === 'percentage') {
                $discountAmount = ($request->discount_amount / 100) * $subtotal;
            } elseif ($request->discount_type === 'fixed') {
                $discountAmount = $request->discount_amount;
            }

            $total = $subtotal - $discountAmount;

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::user()->id,
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'subtotal' => $subtotal,
                'discount_type' => $request->discount_type,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'payment_method' => $request->payment_method
            ]);

            // Create transaction details and update stock
            foreach ($items as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);

                // Update product stock
                $item['product']->decrement('stock', $item['quantity']);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'invoice' => $transaction->invoice_number,
                'transaction_id' => $transaction->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
