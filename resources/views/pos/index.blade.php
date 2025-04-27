@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Point of Sale</h1>
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
                        <h5 class="card-title mb-0">Keranjang</h5>
                    </div>
                    <div class="card-body">
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
                            <label>Diskon</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="discountAmount" min="0"
                                    value="0">
                                <select class="form-select" id="discountType" style="max-width: 120px;">
                                    <option value="fixed">Rp</option>
                                    <option value="percentage">%</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Total</label>
                            <input type="text" class="form-control" id="total" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Metode Pembayaran</label>
                            <select class="form-select" id="paymentMethod">
                                <option value="cash">Tunai</option>
                                <option value="non-cash">Non-Tunai</option>
                            </select>
                        </div>
                        <button class="btn btn-primary w-100" onclick="processTransaction()">Proses Pembayaran</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let cart = [];

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
            cartItems.innerHTML = '';

            let subtotal = 0;

            cart.forEach(item => {
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
                        <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            });

            document.getElementById('subtotal').value = 'Rp ' + subtotal.toLocaleString();
            calculateTotal();
        }

        function calculateTotal() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const discountAmount = parseFloat(document.getElementById('discountAmount').value) || 0;
            const discountType = document.getElementById('discountType').value;

            let discount = 0;
            if (discountType === 'percentage') {
                discount = (discountAmount / 100) * subtotal;
            } else {
                discount = discountAmount;
            }

            const total = subtotal - discount;
            document.getElementById('total').value = 'Rp ' + total.toLocaleString();
        }

        function processTransaction() {
            if (cart.length === 0) {
                alert('Keranjang kosong!');
                return;
            }

            const products = cart.map(item => ({
                id: item.id,
                quantity: item.quantity
            }));

            const data = {
                products,
                discount_type: document.getElementById('discountType').value,
                discount_amount: parseFloat(document.getElementById('discountAmount').value) || 0,
                payment_method: document.getElementById('paymentMethod').value
            };

            fetch('{{ route('pos.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Tampilkan dialog konfirmasi cetak struk
                        if (confirm('Transaksi berhasil! Invoice: ' + data.invoice +
                                '\n\nApakah Anda ingin mencetak struk?')) {
                            // Buka window baru untuk cetak struk
                            window.open('/transactions/' + data.transaction_id + '/print', '_blank');
                        }

                        // Reset keranjang
                        cart = [];
                        updateCart();
                        document.getElementById('discountAmount').value = 0;
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan!');
                    console.error('Error:', error);
                });
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

        // Update total when discount changes
        document.getElementById('discountAmount').addEventListener('input', calculateTotal);
        document.getElementById('discountType').addEventListener('change', calculateTotal);
    </script>
@endpush
let cart = [];

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
cart =
