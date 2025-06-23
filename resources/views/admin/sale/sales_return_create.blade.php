@extends('layouts.app') {{-- Ensure this path is correct for your main layout file --}}

@section('style')
<style>
    #product-list .card {
        min-height: 300px;
    }
    .pointer-events-none {
        pointer-events: none;
    }
    .opacity-50 {
        opacity: 0.5;
    }
    /* Add a style for disabled dropdowns */
    .form-control[disabled] {
        background-color: #e9ecef;
        opacity: 1;
    }
</style>
@endsection

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            @php
                use App\Models\Setting;
                $setting = \App\Models\Setting::first();
            @endphp

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="col-md-7">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-5 mb-3">
                                <label for="customer_id">Select Customer</label>
                                {{-- Customer dropdown is disabled and pre-selected for sales return --}}
                                <select name="customer_id" id="customer_id" class="form-control" required disabled>
                                    <option value="{{ $sale->customer->id ?? '' }}" selected>{{ $sale->customer->name ?? 'N/A' }}</option>
                                </select>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="branch_id">Select Branch</label>
                                {{-- Branch dropdown is disabled and pre-selected for sales return --}}
                                <select name="branch_id" id="branch_id" class="form-control" required disabled>
                                    <option value="{{ $sale->branch->id ?? '' }}" selected>{{ $sale->branch->name ?? 'N/A' }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-3 text-right">
                                {{-- Add Customer button is hidden for sales return --}}
                                <button type="button" class="btn btn-outline-primary mt-4 w-100" style="display: none;">
                                    + Customer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Search Products</h4>
                        <form method="GET" action="{{ route('sale_return.create', $sale->id) }}" style="display: flex; gap: 10px; align-items: center;">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, SKU, barcode..." value="{{ request('search') }}" style="flex: 1;" autocomplete="off" id="product-search">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <div class="row" id="product-list">
                            {{-- Loop through the products passed from the controller --}}
                            @foreach($products as $product)
                                @php
                                    $isOutOfStock = !$product->in_stock && $product->variants->count() === 0;
                                @endphp
                                <div class="col-md-3 mb-2 product-item d-flex">
                                    <div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 {{ $isOutOfStock ? 'bg-light text-muted pointer-events-none opacity-50' : '' }}">
                                        @if (!empty($product->product_img))
                                            <img src="{{ asset('storage/' . $product->product_img) }}" alt="Product Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin: auto;">
                                        @else
                                            <div style="width: 100px; height: 100px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin: auto;">N/A</div>
                                        @endif

                                        <h6 class="mt-2 mb-1">{{ $product->name }}</h6>

                                        {{-- Conditional rendering for products with variants vs. simple products --}}
                                        @if($product->variants->count())
                                            {{-- Product has variants: show a dropdown to select a variant --}}
                                            <select class="form-control mb-2 variant-selector mt-auto" data-product-id="{{ $product->id }}">
                                                <option disabled selected>Choose Variant</option>
                                                @foreach($product->variants as $variant)
                                                    <option
                                                        value="variant-{{ $variant->id }}" {{-- Unique ID for variant items --}}
                                                        data-name="{{ $product->name }} - {{ $variant->variant_name }}" {{-- Combined name --}}
                                                        data-price="{{ $variant->sale_price }}"
                                                        data-stock="{{ $variant->stock_quantity }}"
                                                        data-unit-id="{{ $product->default_display_unit_id }}" {{-- Assuming unit from parent product --}}
                                                        {{ !$variant->in_stock ? 'disabled' : '' }}>
                                                        {{ $variant->variant_name }} - {{ $setting->currency_symbol }} {{ number_format($variant->sale_price, 2) }}
                                                        {{ !$variant->in_stock ? '(Out of Stock)' : '(Stock: '.$variant->stock_quantity.')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>
                                        @else
                                            {{-- Simple product (no variants): show direct price and add to cart button --}}
                                            <p class="mb-1">{{ $setting->currency_symbol }} {{ number_format($product->sale_price, 2) }}
                                                <br><small>(Stock: {{ $product->stock_quantity }})</small>
                                            </p>
                                            @if($product->in_stock)
                                                <button
                                                    class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart"
                                                    data-id="product-{{ $product->id }}" {{-- Unique ID for simple products --}}
                                                    data-name="{{ $product->name }}"
                                                    data-price="{{ $product->sale_price }}"
                                                    data-stock="{{ $product->stock_quantity }}"
                                                    data-unit-id="{{ $product->default_display_unit_id }}">
                                                    Add to Cart
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary w-100 mt-auto" disabled>Out of Stock</button>
                                            @endif
                                        @endif
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
                        <h4>Return Cart</h4>
                    </div>
                    <div id="stock-error" class="alert alert-danger d-none" role="alert"></div>
                    <div id="return-error" class="alert alert-warning d-none" role="alert"></div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty (Max Return)</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items"></tbody>
                        </table>

                    <div style="margin-top: 15px;">

                        <div style="margin-bottom: 10px;">
                            <label for="tax">Tax</label><br>
                            <div style="display: flex; align-items: center;">
                                <span style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;">%</span>
                                <input type="number" name="taxrate" id="taxrate" placeholder="Tax" autocomplete="off"
                                       style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-left: none; border-radius: 0 4px 4px 0;">
                            </div>
                        </div>

                        <div style="margin-top: 8px;">
                            <label>Discount Type:</label><br>
                            <label>
                                <input type="radio" name="discount_type" value="fixed" checked onclick="updateDiscountSymbol()"> Fixed
                            </label>
                            <label style="margin-left: 15px;">
                                <input type="radio" name="discount_type" value="percentage" onclick="updateDiscountSymbol()"> Percentage
                            </label>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label for="discount">Discount</label><br>
                            <div style="display: flex; align-items: center;">
                                <span id="discount-symbol" style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;"> {{ $setting->currency_symbol }} </span>
                                <input type="number" name="discountRate" id="discountRate" placeholder="Discount" autocomplete="off"
                                       style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-left: none; border-radius: 0 4px 4px 0;">
                            </div>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label for="shipping">Shipping</label><br>
                            <div style="display: flex; align-items: center;">
                                <span style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;"> {{ $setting->currency_symbol }} </span>
                                <input type="number" name="shippingRate" id="shippingRate" placeholder="Shipping" autocomplete="off"
                                       style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-left: none; border-radius: 0 4px 4px 0;">
                            </div>
                        </div>

                    </div>


                        <div class="mt-3">
                            <p><strong>Subtotal:</strong> {{ $setting->currency_symbol }} <span id="subtotal">0.00</span></p>
                            <p><strong>Discount:</strong> {{ $setting->currency_symbol }} <span id="discount">0.00</span></p>
                            <p><strong>Tax:</strong> {{ $setting->currency_symbol }} <span id="tax">0.00</span></p>
                            <p><strong>Shipping:</strong> {{ $setting->currency_symbol }} <span id="shipping">0.00</span></p>
                            <hr>
                            <h5><strong>Total:</strong> {{ $setting->currency_symbol }} <span id="total">0.00</span></h5>
                        </div>

                        <div class="mt-3 d-flex justify-content-between">
                            <button class="btn btn-danger" id="reset-cart">Cancel Return</button>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#checkoutModal" id="open-checkout" disabled>Process Return</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            {{-- Update the form action to your sales return process route --}}
            <form id="checkout-form" method="POST" action="{{ route('sale_return.store', $sale->id) }}">
                @csrf
                <input type="hidden" name="cart_data" id="cart_data">
                <input type="hidden" name="total_payable" id="total_payable">
                <input type="hidden" id="subtotal_hidden" name="subtotal">
                <input type="hidden" id="tax_hidden" name="tax">
                <input type="hidden" id="discount_hidden" name="discount">
                <input type="hidden" id="shipping_hidden" name="shipping">
                <input type="hidden" name="balance_due" id="balance_due_raw">
                <input type="hidden" name="customer_id" id="selected_customer_id" value="{{ $sale->customer_id }}">
                <input type="hidden" name="branch_id" id="selected_branch_id" value="{{ $sale->branch_id }}">


                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Return Payment</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Payment Method (Refund)</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mixed">Mixed</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount Refunded</label>
                            <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Balance / Change (Refunded)</label>
                            <input type="text" id="balance_display" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Confirm Return</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- The "Add Customer Modal" is not relevant for sales return, so you can remove or hide it --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('customers.store') }}">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email (optional)</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Address (optional)</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save Customer</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>


<script>
    const currencySymbol = @json($setting->currency_symbol);
    
    function updateDiscountSymbol() {
        const symbol = document.getElementById('discount-symbol');
        const selectedType = document.querySelector('input[name="discount_type"]:checked').value;
        symbol.textContent = selectedType === 'percentage' ? '%' : currencySymbol;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const cart = {};
        // Store original sale quantities for validation
        const originalSaleQuantities = {};

        let taxValue = 0;
        let discountValue = 0;
        let discountType = 'fixed';
        let shipping = 0;

        document.getElementById('taxrate').addEventListener('input', function () {
            taxValue = parseFloat(this.value) || 0;
            updateCartUI();
        });

        document.getElementById('discountRate').addEventListener('input', function () {
            discountValue = parseFloat(this.value) || 0;
            updateCartUI();
        });

        document.getElementById('shippingRate').addEventListener('input', function () {
            shipping = parseFloat(this.value) || 0;
            updateCartUI();
        });

        document.querySelectorAll('input[name="discount_type"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                discountType = this.value;
                updateDiscountSymbol();
                updateCartUI();
            });
        });

        const cartItemsEl = document.getElementById('cart-items');
        const subtotalEl = document.getElementById('subtotal');
        const discountEl = document.getElementById('discount');
        const shippingEl = document.getElementById('shipping');
        const taxEl = document.getElementById('tax');
        const totalEl = document.getElementById('total');
        const chargeBtn = document.getElementById('open-checkout');
        const resetBtn = document.getElementById('reset-cart');
        const amountPaidInput = document.getElementById('amount_paid');
        const balanceDisplay = document.getElementById('balance_display');
        const cartDataInput = document.getElementById('cart_data');
        const totalPayableInput = document.getElementById('total_payable');
        const returnErrorDiv = document.getElementById('return-error');


        // Function to pre-fill the cart with sale items
        function prefillCart(saleItems) {
            console.log("Debugging: Raw saleItems received from backend:", saleItems); // Debugging line

            saleItems.forEach(item => {
                let id, name, price, unit_id, purchased_qty;

                // CORRECTED: Use item.variant_id instead of item.product_variant_id
                if (item.product_id && !item.variant_id) { // Simple Product (product_id exists, but no variant_id)
                    id = `product-${item.product_id}`;
                    name = item.product.name;
                    price = item.product.sale_price;
                    unit_id = item.product.default_display_unit_id;
                    purchased_qty = item.quantity;
                    console.log(`Debugging: Processing Simple Product - ID: ${id}, Name: ${name}, Qty: ${purchased_qty}`); // Debugging line
                } else if (item.variant_id) { // Variant Product (variant_id exists)
                    id = `variant-${item.variant_id}`;
                    // Ensure item.variant.product and item.variant are available
                    name = `${item.variant?.product?.name || 'N/A Product'} - ${item.variant?.variant_name || 'N/A Variant'}`;
                    price = item.variant?.sale_price;
                    unit_id = item.variant?.product?.default_display_unit_id;
                    purchased_qty = item.quantity;
                    console.log(`Debugging: Processing Variant Product - ID: ${id}, Name: ${name}, Qty: ${purchased_qty}`); // Debugging line
                } else {
                    console.warn("Debugging: Item could not be identified as simple or variant:", item); // Debugging line
                    return; // Skip this item if it cannot be identified
                }

                if (id) {
                    // Check if the item already exists in the cart (shouldn't happen for distinct variants if IDs are unique)
                    if (cart[id]) {
                        console.warn(`Debugging: Duplicate item ID encountered in prefillCart: ${id}. This should not happen for distinct sale items.`); // Debugging line
                    }
                    cart[id] = {
                        name,
                        sale_price: price,
                        unit_id,
                        qty: 0, // Initialize quantity to 0 for return
                        purchased_qty: purchased_qty // Store original purchased quantity
                    };
                    originalSaleQuantities[id] = purchased_qty; // Store for validation
                }
            });
            console.log("Debugging: Cart object after prefill:", cart); // Debugging line
            updateCartUI();
        }

        // Call prefillCart with the data from your controller
        prefillCart(@json($saleItems));

        function updateCartUI() {
            cartItemsEl.innerHTML = '';
            let subtotal = 0;

            for (const id in cart) {
                const item = cart[id];
                // Only display items that were pre-filled from the sale, or explicitly added from search AND have purchased_qty > 0
                if (item.purchased_qty === undefined || item.purchased_qty === null) {
                    console.warn(`Debugging: Item ${id} has no purchased_qty. Skipping display for return.`, item); // Debugging line
                    continue; // Skip items not properly initialized for returns
                }

                const lineTotal = item.sale_price * item.qty; // Calculate based on return quantity
                subtotal += lineTotal;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.name}</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary btn-decrement" data-id="${id}" data-max-return="${item.purchased_qty}">-</button>
                            </div>
                            <input type="text" class="form-control text-center" value="${item.qty}" readonly style="max-width: 50px;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-increment" data-id="${id}" data-max-return="${item.purchased_qty}">+</button>
                            </div>
                        </div>
                        <small class="text-muted">(Max: ${item.purchased_qty})</small>
                    </td>
                    <td>${currencySymbol} ${lineTotal.toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-danger btn-remove" data-id="${id}">&times;</button></td>
                `;
                cartItemsEl.appendChild(tr);
            }

            const discount = discountType === 'percentage'
                ? (discountValue / 100) * subtotal
                : discountValue;

            const tax = (subtotal - discount) * (taxValue / 100);

            const total = subtotal - discount + tax + shipping;

            subtotalEl.textContent = subtotal.toFixed(2);
            discountEl.textContent = discount.toFixed(2);
            taxEl.textContent = tax.toFixed(2);
            shippingEl.textContent = shipping.toFixed(2);
            totalEl.textContent = total.toFixed(2);

            document.getElementById('subtotal_hidden').value = subtotal.toFixed(2);
            document.getElementById('discount_hidden').value = discount.toFixed(2);
            document.getElementById('tax_hidden').value = tax.toFixed(2);
            document.getElementById('shipping_hidden').value = shipping.toFixed(2);

            totalPayableInput.value = total.toFixed(2);

            const structuredCart = {};
            for (const id in cart) {
                const [type, numericId] = id.split('-');
                if (cart[id].qty > 0) { // Only include items with return quantity > 0
                    structuredCart[id] = {
                        type: type,
                        id: parseInt(numericId),
                        name: cart[id].name,
                        sale_price: cart[id].sale_price,
                        unit_id: cart[id].unit_id,
                        qty: cart[id].qty,
                        purchased_qty: cart[id].purchased_qty
                    };
                }
            }

            cartDataInput.value = JSON.stringify(structuredCart);
            chargeBtn.disabled = Object.keys(structuredCart).length === 0; // Disable if no items selected for return
        }

        function addToCart(id, name, price, unit_id, stock) {
            const purchasedQty = originalSaleQuantities[id] || 0; // This is the key for validation

            if (cart[id]) {
                if (cart[id].qty < purchasedQty) { // Validate against original purchased quantity
                    cart[id].qty++;
                    hideError(returnErrorDiv);
                } else {
                    showError(returnErrorDiv, `Cannot return more than the originally purchased quantity (${purchasedQty}) of "${name}".`);
                    return;
                }
            } else {
                // If the item is being added from the product search, and it *was not* part of the original sale,
                // or if it was, but we are adding it for the first time via the search (e.g., after removal),
                // we should still respect the purchased quantity.
                // If it's a completely new item not in the original sale, purchasedQty will be 0, preventing its addition.
                if (purchasedQty === 0) {
                     showError(returnErrorDiv, `This item was not part of the original sale or cannot be returned.`);
                     return;
                }
                cart[id] = {
                    name,
                    sale_price: price,
                    unit_id,
                    qty: 1,
                    stock: stock, // This 'stock' is the current inventory stock, not the return limit
                    purchased_qty: purchasedQty
                };
                if (cart[id].qty > purchasedQty) { // Double check immediately after adding
                     showError(returnErrorDiv, `Cannot return more than the originally purchased quantity (${purchasedQty}) of "${name}".`);
                     delete cart[id];
                     return;
                }
                hideError(returnErrorDiv);
            }
            updateCartUI();
        }

        document.querySelectorAll('.variant-selector').forEach(selector => {
            selector.addEventListener('change', function () {
                const card = this.closest('.product-item');
                const selected = this.options[this.selectedIndex];
                // Ensure selected and its dataset properties exist
                if (selected && selected.dataset) {
                    const id = selected.value; // e.g., "variant-123"
                    const name = selected.dataset.name;
                    const price = parseFloat(selected.dataset.price);
                    const unit_id = selected.dataset.unitId;
                    const addBtn = card.querySelector('.add-variant-to-cart');
                    const stock = parseInt(selected.dataset.stock);

                    addBtn.disabled = false;
                    addBtn.dataset.id = id;
                    addBtn.dataset.name = name;
                    addBtn.dataset.price = price;
                    addBtn.dataset.unitId = unit_id;
                    addBtn.dataset.stock = stock;
                }
            });
        });

        document.querySelectorAll('.add-variant-to-cart').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const price = parseFloat(btn.getAttribute('data-price'));
                const unit_id = btn.getAttribute('data-unit-id');
                const stock = parseInt(btn.getAttribute('data-stock'));

                addToCart(id, name, price, unit_id, stock);
            });
        });

        document.querySelectorAll('.add-simple-to-cart').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const price = parseFloat(btn.getAttribute('data-price'));
                const unit_id = btn.getAttribute('data-unit-id');
                const stock = parseInt(btn.getAttribute('data-stock'));

                addToCart(id, name, price, unit_id, stock);
            });
        });

        cartItemsEl.addEventListener('click', e => {
            const btn = e.target;
            const id = btn.getAttribute('data-id');
            if (!id) return;

            const item = cart[id];
            if (!item) {
                console.error("Debugging: Cart item not found for ID:", id); // Debugging line
                return;
            }

            const maxReturnQty = item.purchased_qty; // This is the key for validation

            if (btn.classList.contains('btn-increment')) {
                if (item.qty < maxReturnQty) {
                    item.qty++;
                    hideError(returnErrorDiv);
                } else {
                    showError(returnErrorDiv, `Cannot return more than ${maxReturnQty} of "${item.name}".`);
                }
            } else if (btn.classList.contains('btn-decrement')) {
                item.qty = Math.max(0, item.qty - 1); // Ensure quantity doesn't go below 0
                hideError(returnErrorDiv);
            } else if (btn.classList.contains('btn-remove')) {
                delete cart[id];
                hideError(returnErrorDiv);
            }
            updateCartUI();
        });


        resetBtn.addEventListener('click', () => {
            // Reset quantities to 0 for all pre-filled items
            for (const id in cart) {
                cart[id].qty = 0;
            }
            updateCartUI();
            hideError(returnErrorDiv);
        });

        amountPaidInput.addEventListener('input', () => {
            const total = parseFloat(totalEl.textContent) || 0;
            const paid = parseFloat(amountPaidInput.value) || 0;
            // For returns, `balance_due` will typically be positive (refund amount)
            const due = total - paid;
            balanceDisplay.value = (due >= 0 ? 'Due: ' : 'Change: ') + currencySymbol + ' ' + Math.abs(due).toFixed(2);
            document.getElementById('balance_due_raw').value = due.toFixed(2);
        });

        $('#checkoutModal').on('show.bs.modal', () => {
            amountPaidInput.value = '';
            balanceDisplay.value = '';
        });

        document.getElementById('product-search').addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.getAttribute('data-name') || '';
                item.style.display = name.includes(query) ? '' : 'none';
            });
        });


        function showError(element, message) {
            element.textContent = message;
            element.classList.remove('d-none');
            setTimeout(() => {
                element.classList.add('d-none');
            }, 3000);
        }

        function hideError(element) {
            element.classList.add('d-none');
        }

        // Initial update to show pre-filled cart
        updateCartUI();
    });
</script>
@endsection
