@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <!-- Product Search Panel (Left) -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h4>Search Products</h4>
                        <input id="product-search" type="text" class="form-control" placeholder="Search by name, SKU, or scan barcode...">
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <div class="row" id="product-list">
                            @foreach($products as $product)
                                <div class="col-md-6 mb-3 product-item" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}">
                                    <div class="card p-2">
                                        <h6>{{ $product->name }}</h6>
                                        <small>SKU: {{ $product->sku }}</small>
                                        <p>Price: ${{ number_format($product->sale_price, 2) }}</p>
                                        <button class="btn btn-sm btn-success w-100 add-to-cart" 
                                            data-id="{{ $product->id }}" 
                                            data-name="{{ $product->name }}" 
                                            data-price="{{ number_format($product->sale_price, 2, '.', '') }}">Add to Cart</button>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Panel (Right) -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Shopping Cart</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <!-- Cart rows added here by JS -->
                            </tbody>
                        </table>

                        <!-- Cart Summary -->
                        <div class="mt-4">
                            <p><strong>Subtotal:</strong> $<span id="subtotal">0.00</span></p>
                            <p><strong>Discount:</strong> $<span id="discount">0.00</span></p>
                            <p><strong>Tax (1%):</strong> $<span id="tax">0.00</span></p>
                            <hr>
                            <h5><strong>Total Payable:</strong> $<span id="total">0.00</span></h5>
                        </div>

                        <div class="mt-3 d-flex justify-content-between">
                            <button class="btn btn-danger" id="reset-cart">Cancel</button>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#checkoutModal" id="open-checkout" disabled>Charge</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="checkout-form" method="POST" action="{{ route('sales.checkout.process') }}">
                @csrf
                <input type="hidden" name="cart_data" id="cart_data">
                <input type="hidden" name="total_payable" id="total_payable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Payment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mixed">Mixed</option>
                                <option value="online">Online</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Amount Paid</label>
                            <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Balance / Change</label>
                            <input type="text" id="balance_display" class="form-control" readonly>
                            <input type="hidden" name="balance_due" id="balance_due_raw">
                            <input type="hidden" id="subtotal_hidden" name="subtotal">
                            <input type="hidden" id="tax_hidden" name="tax">
                            <input type="hidden" id="discount_hidden" name="discount">

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Confirm Payment</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
        
            // Add filter for product search input
            const productSearchInput = document.getElementById('product-search');
            productSearchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
        
                document.querySelectorAll('.product-item').forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    const sku = item.getAttribute('data-sku') || '';
        
                    if (name.includes(query) || sku.includes(query)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        
        
            const cart = {};
            const taxRate = 0.1; // 1% tax
            const discountAmount = 0; // for example purposes
        
            const cartItemsEl = document.getElementById('cart-items');
            const subtotalEl = document.getElementById('subtotal');
            const discountEl = document.getElementById('discount');
            const taxEl = document.getElementById('tax');
            const totalEl = document.getElementById('total');
            const chargeBtn = document.getElementById('open-checkout');
            const resetBtn = document.getElementById('reset-cart');
            const amountPaidInput = document.getElementById('amount_paid');
            const balanceDisplay = document.getElementById('balance_display');
            const cartDataInput = document.getElementById('cart_data');
            const totalPayableInput = document.getElementById('total_payable');
        
            function updateCartUI() {
                cartItemsEl.innerHTML = '';
        
                let subtotal = 0;
        
                for (const id in cart) {
                    const item = cart[id];
                    const lineTotal = (parseFloat(item.sale_price) || 0) * (parseInt(item.qty) || 0);
                    subtotal += lineTotal;
        
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${item.name}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary btn-decrement" data-id="${id}">-</button>
                                </div>
                                <input type="text" class="form-control text-center" value="${item.qty}" readonly style="max-width: 50px;">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-increment" data-id="${id}">+</button>
                                </div>
                            </div>
                        </td>
                        <td>$${lineTotal.toFixed(2)}</td>
                        <td><button class="btn btn-sm btn-danger btn-remove" data-id="${id}">&times;</button></td>
                    `;
                    cartItemsEl.appendChild(tr);
                }
        
                const discount = discountAmount;
                const tax = (subtotal - discount) * taxRate;
                const total = subtotal - discount + tax;
        
                subtotalEl.textContent = subtotal.toFixed(2);
                document.getElementById('subtotal_hidden').value = subtotal.toFixed(2);
        
                discountEl.textContent = discount.toFixed(2);
                document.getElementById('discount_hidden').value = discount.toFixed(2);
        
                taxEl.textContent = tax.toFixed(2);
                document.getElementById('tax_hidden').value = tax.toFixed(2);
        
                totalEl.textContent = total.toFixed(2);
        
                chargeBtn.disabled = Object.keys(cart).length === 0;
        
                // Save cart & total for checkout
                cartDataInput.value = JSON.stringify(cart);
                totalPayableInput.value = total.toFixed(2);
            }
        
            // Add to Cart button handler
            document.querySelectorAll('.add-to-cart').forEach(btn => {
                btn.addEventListener('click', e => {
                    const id = btn.getAttribute('data-id');
                    const name = btn.getAttribute('data-name');
                    const sale_price = parseFloat(btn.getAttribute('data-price')) || 0;
        
                    if (cart[id]) {
                        cart[id].qty++;
                    } else {
                        cart[id] = { name, sale_price, qty: 1 };
                    }
                    updateCartUI();
                });
            });
        
            // Delegate increment/decrement/remove buttons
            cartItemsEl.addEventListener('click', e => {
                const target = e.target;
                const id = target.getAttribute('data-id');
                if (!id) return;
        
                if (target.classList.contains('btn-increment')) {
                    cart[id].qty++;
                    updateCartUI();
                } else if (target.classList.contains('btn-decrement')) {
                    if (cart[id].qty > 1) {
                        cart[id].qty--;
                    } else {
                        delete cart[id];
                    }
                    updateCartUI();
                } else if (target.classList.contains('btn-remove')) {
                    delete cart[id];
                    updateCartUI();
                }
            });
        
            // Reset cart
            resetBtn.addEventListener('click', () => {
                for (const key in cart) delete cart[key];
                updateCartUI();
            });
        
            amountPaidInput.addEventListener('input', () => {
                const total = parseFloat(totalEl.textContent) || 0;
                const paid = parseFloat(amountPaidInput.value) || 0;
        
                const due = total - paid;
        
                // Always show Paid in the display field
                balanceDisplay.value = 'Due: $' + due.toFixed(2);
        
                // Send actual due value (positive if due, negative if overpaid)
                document.getElementById('balance_due_raw').value = due.toFixed(2);
            });
        
        
        
            // Reset balance display on modal show
            $('#checkoutModal').on('show.bs.modal', () => {
                amountPaidInput.value = '';
                balanceDisplay.value = '';
            });
        
        
        });
    
    </script>
@endsection
