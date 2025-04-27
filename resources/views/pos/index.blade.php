@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Point of Sale</h1>

        <!-- Mobile Cart Toggle Button -->
        <div class="d-lg-none mb-3">
            <button class="btn btn-primary w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                <i class="fas fa-shopping-cart me-2"></i>
                Lihat Keranjang (<span id="cartCount">0</span>)
            </button>
        </div>

        <div class="row">
            <!-- Products Section -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daftar Produk</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchProduct" placeholder="Cari produk...">
                        </div>
                        <div class="row g-2" id="productList">
                            @foreach ($products as $product)
                                <div class="col-6 col-md-4 product-item">
                                    <div class="card h-100"
                                        onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->selling_price }})">
                                        <div class="card-body p-2">
                                            <h6 class="card-title text-truncate mb-1">{{ $product->name }}</h6>
                                            <p class="card-text mb-1">Rp
                                                {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                            <small class="text-muted">Stok: {{ $product->stock }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Section - Desktop -->
            <div class="col-lg-5 d-none d-lg-block">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Keranjang</h5>
                    </div>
                    <div class="card-body">
                        @include('pos.cart-content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Offcanvas for Mobile -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Keranjang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @include('pos.cart-content')
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .product-item .card {
            cursor: pointer;
            transition: all 0.2s;
        }

        .product-item .card:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .offcanvas {
            width: 100% !important;
            max-width: 400px;
        }

        @media (max-width: 576px) {
            .offcanvas {
                max-width: 100%;
            }
        }
    </style>
@endpush

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

            // Show notification on mobile
            if (window.innerWidth < 992) {
                showNotification('Produk ditambahkan ke keranjang');
            }
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
            const cartItems = document.querySelectorAll('#cartItems');
            const cartCount = document.getElementById('cartCount');

            cartItems.forEach(element => {
                element.innerHTML = '';
            });

            let subtotal = 0;

            cart.forEach(item => {
                const itemSubtotal = item.price * item.quantity;
                subtotal += itemSubtotal;

                const rowHtml = `
                <tr>
                    <td>${item.name}</td>
                    <td style="width: 80px;">
                        <input type="number" class="form-control form-control-sm" 
                               value="${item.quantity}" min="1" 
                               onchange="updateQuantity(${item.id}, this.value)">
                    </td>
                    <td class="text-nowrap">Rp ${item.price.toLocaleString()}</td>
                    <td class="text-nowrap">Rp ${itemSubtotal.toLocaleString()}</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;

                cartItems.forEach(element => {
                    element.innerHTML += rowHtml;
                });
            });

            document.querySelectorAll('#subtotal').forEach(element => {
                element.value = 'Rp ' + subtotal.toLocaleString();
            });

            if (cartCount) {
                cartCount.textContent = cart.length;
            }

            calculateTotal();
        }

        function calculateTotal() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const serviceFeeElements = document.querySelectorAll('#serviceFee');
            const discountAmountElements = document.querySelectorAll('#discountAmount');
            const discountTypeElements = document.querySelectorAll('#discountType');

            const serviceFee = parseFloat(serviceFeeElements[0]?.value || 0);
            const discountAmount = parseFloat(discountAmountElements[0]?.value || 0);
            const discountType = discountTypeElements[0]?.value || 'fixed';

            let discount = 0;
            if (discountType === 'percentage') {
                discount = (discountAmount / 100) * subtotal;
            } else {
                discount = discountAmount;
            }

            const total = subtotal + serviceFee - discount;

            document.querySelectorAll('#total').forEach(element => {
                element.value = 'Rp ' + total.toLocaleString();
            });
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
                service_fee: parseFloat(document.querySelector('#serviceFee').value) || 0,
                discount_type: document.querySelector('#discountType').value,
                discount_amount: parseFloat(document.querySelector('#discountAmount').value) || 0,
                payment_method: document.querySelector('#paymentMethod').value
            };

            // Show loading state
            const submitButton = document.querySelector('button[onclick="processTransaction()"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            submitButton.disabled = true;

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
                        if (confirm('Transaksi berhasil! Invoice: ' + data.invoice +
                                '\n\nApakah Anda ingin mencetak struk?')) {
                            window.open('/transactions/' + data.transaction_id + '/print', '_blank');
                        }

                        cart = [];
                        updateCart();
                        document.querySelectorAll('#discountAmount').forEach(el => el.value = 0);
                        document.querySelectorAll('#serviceFee').forEach(el => el.value = 50000);

                        // Close offcanvas on mobile
                        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas'));
                        if (offcanvas) {
                            offcanvas.hide();
                        }
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan!');
                    console.error('Error:', error);
                })
                .finally(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
        }

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'position-fixed bottom-0 start-50 translate-middle-x mb-3 alert alert-success';
            notification.textContent = message;
            notification.style.zIndex = '9999';
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 2000);
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
        document.querySelectorAll('#serviceFee').forEach(element => {
            element.addEventListener('input', calculateTotal);
        });

        document.querySelectorAll('#discountAmount').forEach(element => {
            element.addEventListener('input', calculateTotal);
        });

        document.querySelectorAll('#discountType').forEach(element => {
            element.addEventListener('change', calculateTotal);
        });

        // Sync inputs between desktop and mobile
        document.querySelectorAll('#serviceFee, #discountAmount, #discountType, #paymentMethod').forEach(element => {
            element.addEventListener('change', function() {
                const id = this.id;
                const value = this.value;
                document.querySelectorAll(`#${id}`).forEach(el => {
                    if (el !== this) {
                        el.value = value;
                    }
                });
            });
        });
    </script>
@endpush
