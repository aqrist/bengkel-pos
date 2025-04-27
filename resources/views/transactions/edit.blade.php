@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Edit Transaksi #{{ $transaction->invoice_number }}</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row mt-4">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daftar Produk</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchProduct" placeholder="Cari produk...">
                        </div>
                        <div class="row" id="productList">
                            @foreach ($products as $product)
                                <div class="col-md-4 mb-3 product-item">
                                    <div class="card h-100"
                                        onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->selling_price }})">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $product->name }}</h6>
                                            <p class="card-text">Rp
                                                {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                            <p class="card-text"><small class="text-muted">Stok:
                                                    {{ $product->stock }}</small></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Detail Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('transactions.update', $transaction) }}" method="POST" id="editForm">
                            @csrf
                            @method('PUT')

                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartItems">
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label>Subtotal</label>
                                <input type="text" class="form-control" id="subtotal" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Biaya Jasa</label>
                                <input type="number" class="form-control" id="serviceFee" name="service_fee"
                                    value="{{ $transaction->service_fee }}" min="0">
                            </div>
                            <div class="mb-3">
                                <label>Diskon</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="discountAmount" name="discount_amount"
                                        value="{{ $transaction->discount_type == 'percentage' ? $transaction->discount_amount : ($transaction->discount_type == 'fixed' ? $transaction->discount_amount : 0) }}"
                                        min="0">
                                    <select class="form-select" id="discountType" name="discount_type"
                                        style="max-width: 120px;">
                                        <option value="fixed"
                                            {{ $transaction->discount_type == 'fixed' ? 'selected' : '' }}>Rp</option>
                                        <option value="percentage"
                                            {{ $transaction->discount_type == 'percentage' ? 'selected' : '' }}>%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Total</label>
                                <input type="text" class="form-control" id="total" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Metode Pembayaran</label>
                                <select class="form-select" id="paymentMethod" name="payment_method">
                                    <option value="cash" {{ $transaction->payment_method == 'cash' ? 'selected' : '' }}>
                                        Tunai</option>
                                    <option value="non-cash"
                                        {{ $transaction->payment_method == 'non-cash' ? 'selected' : '' }}>Non-Tunai
                                    </option>
                                </select>
                            </div>
                            <div id="productsInput"></div>
                            <button type="submit" class="btn btn-primary w-100">Update Transaksi</button>
                            <a href="{{ route('transactions.show', $transaction) }}"
                                class="btn btn-secondary w-100 mt-2">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let cart = [];

        // Load existing transaction items
        @foreach ($transaction->details as $detail)
            cart.push({
                id: {{ $detail->product_id }},
                name: "{{ $detail->product->name }}",
                price: {{ $detail->price }},
                quantity: {{ $detail->quantity }}
            });
        @endforeach

        // Update cart display immediately
        updateCart();

        function addToCart(id, name, price) {
            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    quantity: 1
                });
            }
            updateCart();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCart();
        }

        function updateQuantity(id, quantity) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.quantity = parseInt(quantity);
                if (item.quantity <= 0) {
                    removeFromCart(id);
                } else {
                    updateCart();
                }
            }
        }

        function updateCart() {
            const cartItems = document.getElementById('cartItems');
            const productsInput = document.getElementById('productsInput');
            cartItems.innerHTML = '';
            productsInput.innerHTML = '';

            let subtotal = 0;

            cart.forEach((item, index) => {
                const itemSubtotal = item.price * item.quantity;
                subtotal += itemSubtotal;

                cartItems.innerHTML += `
                <tr>
                    <td>${item.name}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm" 
                               value="${item.quantity}" min="1" 
                               onchange="updateQuantity(${item.id}, this.value)">
                    </td>
                    <td>Rp ${item.price.toLocaleString()}</td>
                    <td>Rp ${itemSubtotal.toLocaleString()}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;

                // Add hidden inputs for form submission
                productsInput.innerHTML += `
                <input type="hidden" name="products[${index}][id]" value="${item.id}">
                <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
            `;
            });

            document.getElementById('subtotal').value = 'Rp ' + subtotal.toLocaleString();
            calculateTotal();
        }

        function calculateTotal() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const serviceFee = parseFloat(document.getElementById('serviceFee').value) || 0;
            const discountAmount = parseFloat(document.getElementById('discountAmount').value) || 0;
            const discountType = document.getElementById('discountType').value;

            let discount = 0;
            if (discountType === 'percentage') {
                discount = (discountAmount / 100) * subtotal;
            } else {
                discount = discountAmount;
            }

            const total = subtotal + serviceFee - discount;
            document.getElementById('total').value = 'Rp ' + total.toLocaleString();
        }

        // Search product
        document.getElementById('searchProduct').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const productItems = document.querySelectorAll('.product-item');

            productItems.forEach(item => {
                const productName = item.querySelector('.card-title').textContent.toLowerCase();
                if (productName.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Update total when service fee or discount changes
        document.getElementById('serviceFee').addEventListener('input', calculateTotal);
        document.getElementById('discountAmount').addEventListener('input', calculateTotal);
        document.getElementById('discountType').addEventListener('change', calculateTotal);

        // Validate form before submission
        document.getElementById('editForm').addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Keranjang kosong! Tambahkan minimal satu produk.');
            }
        });
    </script>
@endpush
