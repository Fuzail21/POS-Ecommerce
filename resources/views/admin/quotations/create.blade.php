@extends('layouts.app')

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
</style>
@endsection

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            @php
                use App\Models\Setting;
                $setting = Setting::first();
                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
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
                            
                            <div class="col-md-4 mb-3">
                                <label for="quotation_date">Date</label>
                                <input type="date" name="quotation_date" id="quotation_date" class="form-control" value="{{ now()->toDateString() }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="customer_id">Select Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="branch_id">Select Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        <h4>Search Products</h4>
                        <form id="product-search-form" style="display: flex; gap: 10px; align-items: center;">
                            <input type="text" name="search" id="search-input" class="form-control" placeholder="Search by name..." value="{{ request('search') }}" style="flex: 1;" autocomplete="off">
                            <button type="submit" class="btn text-white" style="background-color: {{ $primaryColor }};">Search</button>
                        </form>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <div class="row" id="product-list">
                            {{-- Product list loop is now directly embedded here --}}
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

                                        @if($product->variants->count())
                                            <select class="form-control mb-2 variant-selector mt-auto" data-product-id="{{ $product->id }}">
                                                <option disabled selected>Choose Variant</option>
                                                @foreach($product->variants as $variant)
                                                    <option
                                                        value="variant-{{ $variant->id }}"
                                                        data-name="{{ $product->name }} - {{ $variant->variant_name }}"
                                                        data-price="{{ $variant->sale_price }}"
                                                        data-stock="{{ $variant->stock_quantity }}"
                                                        data-unit-id="{{ $product->default_display_unit_id }}"
                                                        {{ !$variant->in_stock ? 'disabled' : '' }}>
                                                        {{ $variant->variant_name }} -  {{ $setting->currency_symbol }} {{ number_format($variant->sale_price, 2) }}
                                                        {{ !$variant->in_stock ? '(Out of Stock)' : '(Stock: '.$variant->stock_quantity.')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>
                                        @else
                                            <p class="mb-1">{{ $setting->currency_symbol }} {{ number_format($product->sale_price, 2) }}
                                                <br><small>(Stock: {{ $product->stock_quantity }})</small>
                                            </p>
                                            @if($product->in_stock)
                                                <button
                                                    class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart"
                                                    data-id="product-{{ $product->id }}"
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
                        <h4>Order Items</h4>
                    </div>
                    <div id="stock-error" class="alert alert-danger d-none" role="alert"></div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th style="min-width: 150px;">Product</th>
                                    <th style="min-width: 120px; text-align: center;">Qty</th>
                                    <th style="min-width: 130px; text-align: right;">Total</th>
                                    <th style="width: 40px;"></th>
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
                                <span style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;">{{ $setting->currency_symbol }} </span>
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
                            <button class="btn text-white" style="background-color: {{ $secondaryColor }};" id="reset-cart">Cancel</button>
                            <button class="btn text-white" style="background-color: {{ $primaryColor }};" data-toggle="modal" data-target="#checkoutModal" id="open-checkout" disabled>Next</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="checkout-form" method="POST" action="{{ route('quotations.store') }}">
                @csrf
                <input type="hidden" name="cart_data" id="cart_data">
                <input type="hidden" name="total_payable" id="total_payable">
                <input type="hidden" id="subtotal_hidden" name="subtotal">
                <input type="hidden" id="tax_hidden" name="tax">
                <input type="hidden" id="discount_hidden" name="discount">
                <input type="hidden" id="shipping_hidden" name="shipping">
                {{-- <input type="hidden" name="balance_due" id="balance_due_raw"> --}} {{-- REMOVED/COMMENTED OUT --}}
                <input type="hidden" name="customer_id" id="selected_customer_id">
                <input type="hidden" name="branch_id" id="selected_branch_id">
                <input type="hidden" name="quotation_date" id="selected_quotation_date">


                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Status</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        {{-- REMOVED/COMMENTED OUT PAYMENT METHOD FIELD --}}
                        {{--
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        --}}

                        {{-- REMOVED/COMMENTED OUT AMOUNT PAID FIELD --}}
                        {{--
                        <div class="form-group">
                            <label>Amount Paid</label>
                            <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" required>
                        </div>
                        --}}

                        {{-- REMOVED/COMMENTED OUT BALANCE / CHANGE FIELD --}}
                        {{--
                        <div class="form-group">
                            <label>Balance / Change</label>
                            <input type="text" id="balance_display" class="form-control" readonly>
                        </div>
                        --}}

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="sent">Sent</option> {{-- Changed value from 'cash' to 'sent' --}}
                                <option value="pending">Pending</option> {{-- Changed value from 'cash' to 'pending' --}}
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Note</label>
                            <textarea name="note" class="form-control" placeholder="Enter Note"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn text-white" style="background-color: {{ $primaryColor }};">Save</button>
                        <button type="button" class="btn text-white" style="background-color: {{ $secondaryColor }};" data-dismiss="modal">Back</button>
                    </div>
                </div>
            </form>
        </div>
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

        // Declare variables to hold real-time values
        let taxValue = 0;
        let discountValue = 0;
        let discountType = 'fixed';
        let shipping =  0;

        // Input listeners to update variables
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
                updateDiscountSymbol(); // Update symbol UI
                updateCartUI();
            });
        });

        // Elements
        const cartItemsEl = document.getElementById('cart-items');
        const subtotalEl = document.getElementById('subtotal');
        const discountEl = document.getElementById('discount');
        const shippingEl = document.getElementById('shipping');
        const taxEl = document.getElementById('tax');
        const totalEl = document.getElementById('total');
        const chargeBtn = document.getElementById('open-checkout');
        const resetBtn = document.getElementById('reset-cart');
        // const amountPaidInput = document.getElementById('amount_paid'); // REMOVED/COMMENTED OUT
        // const balanceDisplay = document.getElementById('balance_display'); // REMOVED/COMMENTED OUT
        const cartDataInput = document.getElementById('cart_data');
        const totalPayableInput = document.getElementById('total_payable');
        const customerSelect = document.getElementById('customer_id');
        const selectedCustomerInput = document.getElementById('selected_customer_id');
        const branchSelect = document.getElementById('branch_id');
        const selectedBranchInput = document.getElementById('selected_branch_id');
        const dateSelect = document.getElementById('quotation_date');
        const selectedDateInput = document.getElementById('selected_quotation_date');

        selectedCustomerInput.value = customerSelect.value;
        selectedBranchInput.value = branchSelect.value;
        selectedDateInput.value = dateSelect.value;

        customerSelect.addEventListener('change', () => {
            selectedCustomerInput.value = customerSelect.value;
        });

        branchSelect.addEventListener('change', () => {
            selectedBranchInput.value = branchSelect.value;
        });

        dateSelect.addEventListener('change', () => {
            selectedDateInput.value = dateSelect.value;
        });

        function updateCartUI() {
            cartItemsEl.innerHTML = '';
            let subtotal = 0;

            for (const id in cart) {
                const item = cart[id];
                const lineTotal = item.sale_price * item.qty;
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
                    <td>${currencySymbol} ${lineTotal.toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-danger btn-remove" data-id="${id}">&times;</button></td>
                `;
                cartItemsEl.appendChild(tr);
            }

            // Calculate discount
            const discount = discountType === 'percentage'
                ? (discountValue / 100) * subtotal
                : discountValue;

            // Calculate tax
            const tax = (subtotal - discount) * (taxValue / 100);

            // Final total
            const total = subtotal - discount + tax + shipping;

            // Update UI values
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

            // Serialize cart
            const structuredCart = {};
            for (const id in cart) {
                const [type, numericId] = id.split('-');
                structuredCart[id] = {
                    type: type,
                    id: parseInt(numericId),
                    ...cart[id]
                };
            }

            cartDataInput.value = JSON.stringify(structuredCart);
            chargeBtn.disabled = Object.keys(cart).length === 0;
        }

        function addToCart(id, name, price, unit_id, stock) {
            // If already in cart
            if (cart[id]) {
                cart[id].stock = stock; // Ensure stock is always updated in case it's missing or wrong

                if (cart[id].qty < cart[id].stock) {
                    cart[id].qty++;
                } else {
                    showStockError('Cannot add more. Stock limit reached.');
                    return;
                }
            } else {
                // Add for first time
                cart[id] = {
                    name,
                    sale_price: price,
                    unit_id,
                    qty: 1,
                    stock: stock
                };
            }
            updateCartUI();
        }

        // --- Event Listener Handlers (defined separately for re-attachment) ---
        function handleVariantSelection() {
            const card = this.closest('.product-item');
            const selected = this.options[this.selectedIndex];
            const id = selected.value;
            const name = selected.dataset.name;
            const price = parseFloat(selected.dataset.price);
            const unit_id = selected.dataset.unitId;
            const stock = parseInt(selected.dataset.stock); // Get stock for variant
            const addBtn = card.querySelector('.add-variant-to-cart');

            addBtn.disabled = !selected.value || isNaN(stock) || stock <= 0; // Disable if no variant selected or out of stock
            addBtn.dataset.id = id;
            addBtn.dataset.name = name;
            addBtn.dataset.price = price;
            addBtn.dataset.unitId = unit_id;
            addBtn.dataset.stock = stock; // Store stock on the button
        }

        function handleAddVariantToCart() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));
            const unit_id = this.getAttribute('data-unit-id');
            const stock = parseInt(this.getAttribute('data-stock')); // Retrieve stock

            const currentQty = cart[id]?.qty || 0;

            if (currentQty >= stock) {
                showStockError('Cannot add more. Stock limit reached.');
                return;
            }
            addToCart(id, name, price, unit_id, stock);
        }

        function handleAddSimpleToCart() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));
            const unit_id = this.getAttribute('data-unit-id');
            const stock = parseInt(this.getAttribute('data-stock'));

            const currentQty = cart[id]?.qty || 0;

            if (currentQty >= stock) {
                showStockError('Cannot add more. Stock limit reached.');
                return;
            }
            addToCart(id, name, price, unit_id, stock);
        }

        // --- Function to attach/re-attach event listeners to products ---
        function attachProductEventListeners() {
            // Variant selectors
            document.querySelectorAll('.variant-selector').forEach(selector => {
                selector.removeEventListener('change', handleVariantSelection); // Remove old to prevent duplicates
                selector.addEventListener('change', handleVariantSelection);
            });

            // Add variant to cart buttons
            document.querySelectorAll('.add-variant-to-cart').forEach(btn => {
                btn.removeEventListener('click', handleAddVariantToCart);
                btn.addEventListener('click', handleAddVariantToCart);
            });

            // Add simple product to cart buttons
            document.querySelectorAll('.add-simple-to-cart').forEach(btn => {
                btn.removeEventListener('click', handleAddSimpleToCart);
                btn.addEventListener('click', handleAddSimpleToCart);
            });
        }
        // --- End Event Listener Handlers ---


        cartItemsEl.addEventListener('click', e => {
            const btn = e.target;
            const id = btn.getAttribute('data-id');
            if (!id || !cart[id]) return; // Ensure element has data-id and item exists in cart

            if (btn.classList.contains('btn-increment')) {
                if (cart[id].qty < cart[id].stock) {
                    cart[id].qty++;
                } else {
                    showStockError('Cannot add more. Stock limit reached.');
                }
            } else if (btn.classList.contains('btn-decrement')) {
                cart[id].qty > 1 ? cart[id].qty-- : delete cart[id];
            } else if (btn.classList.contains('btn-remove')) {
                delete cart[id];
            }
            updateCartUI();
        });


        resetBtn.addEventListener('click', () => {
            Object.keys(cart).forEach(key => delete cart[key]);
            updateCartUI();
        });

        // REMOVED/COMMENTED OUT: amountPaidInput and balanceDisplay logic
        /*
        amountPaidInput.addEventListener('input', () => {
            const total = parseFloat(totalEl.textContent) || 0;
            const paid = parseFloat(amountPaidInput.value) || 0;
            const due = total - paid;
            balanceDisplay.value = (due >= 0 ? 'Due: ' : 'Change: ') + currencySymbol + ' ' + Math.abs(due).toFixed(2);
            document.getElementById('balance_due_raw').value = due.toFixed(2);
        });
        */

        // Use jQuery's on() for Bootstrap modal events, as they're jQuery-based
        $('#checkoutModal').on('show.bs.modal', () => {
            // REMOVED/COMMENTED OUT: Clear amountPaidInput and balanceDisplay on modal open
            // amountPaidInput.value = '';
            // balanceDisplay.value = '';
        });

        // --- AJAX Search Implementation ---
        const productSearchForm = document.getElementById('product-search-form');
        const searchInput = document.getElementById('search-input');
        const productListDiv = document.getElementById('product-list');

        productSearchForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const searchQuery = searchInput.value;

            // Show a loading indicator (optional)
            productListDiv.innerHTML = '<div class="col-12 text-center py-5">Loading products...</div>';

            fetch(`{{ route('quotations.create') }}?search=${encodeURIComponent(searchQuery)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Identify as an AJAX request
                }
            })
            .then(response => response.text()) // Get the HTML response
            .then(html => {
                // Update only the product list section with the HTML received directly from the controller
                productListDiv.innerHTML = html;

                // Re-attach event listeners to newly loaded products
                attachProductEventListeners();
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productListDiv.innerHTML = '<div class="col-12 text-center text-danger py-5">Error loading products. Please try again.</div>';
            });
        });
        // --- End AJAX Search Implementation ---

        function showStockError(message) {
            const errorDiv = document.getElementById('stock-error');
            errorDiv.textContent = message;
            errorDiv.classList.remove('d-none');

            // Auto-hide after 3 seconds
            setTimeout(() => {
                errorDiv.classList.add('d-none');
            }, 3000);
        }

        // Initial call to attach event listeners when the page first loads
        attachProductEventListeners();

        // **IMPORTANT:** If you are no longer handling "cash" or "card" as distinct payment methods
        // and you're always considering it as a "sale" without an explicit payment step
        // then you might want to review what 'status' values are appropriate for your backend.
        // I've changed the status options in the modal to 'sent' and 'pending' as examples,
        // assuming a "charge" implies a completed transaction unless explicitly stated otherwise.
    });
</script>
@endsection