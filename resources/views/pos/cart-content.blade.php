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
    <label class="form-label">Subtotal</label>
    <input type="text" class="form-control" id="subtotal" readonly>
</div>
<div class="mb-3">
    <label class="form-label">Biaya Jasa</label>
    <input type="number" class="form-control" id="serviceFee" value="50000" min="0">
</div>
<div class="mb-3">
    <label class="form-label">Diskon</label>
    <div class="input-group">
        <input type="number" class="form-control" id="discountAmount" min="0" value="0">
        <select class="form-select" id="discountType" style="max-width: 120px;">
            <option value="fixed">Rp</option>
            <option value="percentage">%</option>
        </select>
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Total</label>
    <input type="text" class="form-control" id="total" readonly>
</div>
<div class="mb-3">
    <label class="form-label">Metode Pembayaran</label>
    <select class="form-select" id="paymentMethod">
        <option value="cash">Tunai</option>
        <option value="non-cash">Non-Tunai</option>
    </select>
</div>
<button class="btn btn-primary w-100" onclick="processTransaction()">Proses Pembayaran</button>
