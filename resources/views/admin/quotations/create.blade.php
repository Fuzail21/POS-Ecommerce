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
                                <input type="date" name="quotation_date" id="quotation_date" class="form-control" value="{{ old('quotation_date', $quotation->quotation_date ?? now()->toDateString()) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="customer_id">Select Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $quotation->customer_id ?? '') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="branch_id">Select Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $quotation->branch_id ?? '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
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
                            <div class="col-12 text-center text-muted py-5" id="branch-placeholder">
                                <p class="mb-0"><i class="fas fa-store fa-2x mb-2 d-block"></i>Select a branch above to see available products.</p>
                            </div>
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
                        <h4>Adjustments</h4>
                        <div style="margin-bottom: 10px;">
                            <label for="taxrate">Tax</label><br>
                            <div style="display: flex; align-items: center;">
                                <span style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;">%</span>
                                <input type="number" name="taxrate" id="taxrate" placeholder="Tax" autocomplete="off"
                                       style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-left: none; border-radius: 0 4px 4px 0;"
                                       value="{{ old('taxrate', $quotation->tax_percentage ?? 0) }}">
                            </div>
                        </div>


                        <div style="margin-top: 8px;">
                            <label>Discount Type:</label><br>
                            <label>
                                <input type="radio" name="discount_type" value="fixed" {{ (old('discount_type', $quotation->discount_type ?? 'fixed') == 'fixed') ? 'checked' : '' }} onclick="updateDiscountSymbol()"> Fixed
                            </label>
                            <label style="margin-left: 15px;">
                                <input type="radio" name="discount_type" value="percentage" {{ (old('discount_type', $quotation->discount_type ?? 'fixed') == 'percentage') ? 'checked' : '' }} onclick="updateDiscountSymbol()"> Percentage
                            </label>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label for="discountRate">Discount</label><br>
                            <div style="display: flex; align-items: center;">
                                <span id="discount-symbol" style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;"> {{ $setting->currency_symbol ?? '$' }} </span>
                                <input type="number" name="discountRate" id="discountRate" placeholder="Discount" autocomplete="off"
                                       style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-left: none; border-radius: 0 4px 4px 0;"
                                       value="{{ old('discountRate', $quotation->discount_percentage ?? 0) }}">
                            </div>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label for="shippingRate">Shipping</label><br>
                            <div style="display: flex; align-items: center;">
                                <span style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;">{{ $setting->currency_symbol ?? '$' }} </span>
                                <input type="number" name="shippingRate" id="shippingRate" placeholder="Shipping" autocomplete="off"
                                       style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-left: none; border-radius: 0 4px 4px 0;"
                                       value="{{ old('shippingRate', $quotation->shipping_cost ?? 0) }}">
                            </div>
                        </div>

                    </div>


                        <div class="mt-3">
                            <p><strong>Subtotal:</strong> {{ $setting->currency_symbol ?? '$' }} <span id="subtotal">0.00</span></p>
                            <p><strong>Discount:</strong> {{ $setting->currency_symbol ?? '$' }} <span id="discount">0.00</span></p>
                            <p><strong>Tax:</strong> {{ $setting->currency_symbol ?? '$' }} <span id="tax">0.00</span></p>
                            <p><strong>Shipping:</strong> {{ $setting->currency_symbol ?? '$' }} <span id="shipping">0.00</span></p>
                            <hr>
                            <h5><strong>Total:</strong> {{ $setting->currency_symbol ?? '$' }} <span id="total">0.00</span></h5>
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
            {{-- Form action changes based on whether it's create or edit --}}
            <form id="checkout-form" method="POST" action="{{ isset($quotation) ? route('quotations.update', $quotation->id) : route('quotations.store') }}">
                @csrf
                @if(isset($quotation))
                    @method('PUT') {{-- Use PUT method for updates --}}
                @endif
                <input type="hidden" name="cart_data" id="cart_data">
                <input type="hidden" name="total_payable" id="total_payable">
                <input type="hidden" id="subtotal_hidden" name="subtotal">
                <input type="hidden" id="tax_hidden" name="tax">
                <input type="hidden" id="taxrate_hidden" name="taxrate">
                <input type="hidden" id="discount_hidden" name="discount">
                <input type="hidden" id="shipping_hidden" name="shipping">
                <input type="hidden" name="customer_id" id="selected_customer_id">
                <input type="hidden" name="branch_id" id="selected_branch_id">
                <input type="hidden" name="quotation_date" id="selected_quotation_date">


                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Status</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="sent" {{ old('status', $quotation->status ?? '') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="pending" {{ old('status', $quotation->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Note</label>
                            <textarea name="note" class="form-control" placeholder="Enter Note">{{ old('note', $quotation->note ?? '') }}</textarea>
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
    const currencySymbol = @json($setting->currency_symbol ?? '$');
    const initialCartData = @json($initialCart ?? []); // Pass initial cart data for edit mode

    function updateDiscountSymbol() {
        const symbol = document.getElementById('discount-symbol');
        const selectedType = document.querySelector('input[name="discount_type"]:checked').value;
        symbol.textContent = selectedType === 'percentage' ? '%' : currencySymbol;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const cart = initialCartData; // Initialize cart with provided data for edit, or empty for create

        // Declare variables to hold real-time values
        let taxValue = parseFloat(document.getElementById('taxrate').value) || 0;
        let discountValue = parseFloat(document.getElementById('discountRate').value) || 0;
        let discountType = document.querySelector('input[name="discount_type"]:checked').value || 'fixed';
        let shipping =  parseFloat(document.getElementById('shippingRate').value) || 0;

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
        const cartDataInput = document.getElementById('cart_data');
        const totalPayableInput = document.getElementById('total_payable');
        const customerSelect = document.getElementById('customer_id');
        const selectedCustomerInput = document.getElementById('selected_customer_id');
        const branchSelect = document.getElementById('branch_id');
        const selectedBranchInput = document.getElementById('selected_branch_id');
        const dateSelect = document.getElementById('quotation_date');
        const selectedDateInput = document.getElementById('selected_quotation_date');


        // Set initial hidden input values based on selected dropdowns
        selectedCustomerInput.value = customerSelect.value;
        selectedBranchInput.value = branchSelect.value;
        selectedDateInput.value = dateSelect.value;

        customerSelect.addEventListener('change', () => {
            selectedCustomerInput.value = customerSelect.value;
        });

        dateSelect.addEventListener('change', () => {
            selectedDateInput.value = dateSelect.value;
        });

        function updateCartUI() {
            cartItemsEl.innerHTML = '';
            let subtotal = 0;

            for (const id in cart) {
                const item = cart[id];
                const lineTotal = item.actual_price * item.qty;
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
            document.getElementById('taxrate_hidden').value = taxValue.toFixed(2);

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
                    actual_price: price,
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
            // Also reset form fields to initial state (for create mode, this is default, for edit, it's the original quotation data)
            document.getElementById('quotation_date').value = "{{ isset($quotation) ? $quotation->quotation_date : now()->toDateString() }}";
            document.getElementById('customer_id').value = "{{ isset($quotation) ? $quotation->customer_id : '' }}";
            document.getElementById('branch_id').value = "{{ isset($quotation) ? $quotation->branch_id : '' }}";
            document.getElementById('taxrate').value = "{{ isset($quotation) ? $quotation->tax_amount : 0 }}";
            document.getElementById('discountRate').value = "{{ isset($quotation) ? $quotation->discount_amount : 0 }}";
            document.getElementById('shippingRate').value = "{{ isset($quotation) ? $quotation->shipping_cost : 0 }}";
            document.querySelector('input[name="discount_type"][value="{{ isset($quotation) ? $quotation->discount_type : 'fixed' }}"]').checked = true;

            // Re-initialize values after reset for calculation
            taxValue = parseFloat(document.getElementById('taxrate').value) || 0;
            discountValue = parseFloat(document.getElementById('discountRate').value) || 0;
            discountType = document.querySelector('input[name="discount_type"]:checked').value || 'fixed';
            shipping =  parseFloat(document.getElementById('shippingRate').value) || 0;

            updateDiscountSymbol(); // Update symbol for discount
            updateCartUI();
        });

        $('#checkoutModal').on('show.bs.modal', () => {
            // Any specific logic for modal on show
        });

        // --- AJAX Product Load (branch-aware) ---
        const productSearchForm = document.getElementById('product-search-form');
        const searchInput       = document.getElementById('search-input');
        const productListDiv    = document.getElementById('product-list');

        function loadProducts(branchId, searchQuery) {
            if (!branchId) {
                productListDiv.innerHTML = '<div class="col-12 text-center text-muted py-5"><p class="mb-0"><i class="fas fa-store fa-2x mb-2 d-block"></i>Select a branch above to see available products.</p></div>';
                return;
            }

            productListDiv.innerHTML = '<div class="col-12 text-center py-5">Loading products...</div>';

            let url = `{{ route('quotations.create') }}?branch_id=${encodeURIComponent(branchId)}`;
            if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    productListDiv.innerHTML = html;
                    attachProductEventListeners();
                })
                .catch(() => {
                    productListDiv.innerHTML = '<div class="col-12 text-center text-danger py-5">Error loading products. Please try again.</div>';
                });
        }

        // Auto-load products when branch changes
        branchSelect.addEventListener('change', function () {
            selectedBranchInput.value = this.value;
            searchInput.value = '';
            loadProducts(this.value, '');
        });

        // Search within selected branch
        productSearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const branchId = branchSelect.value;
            if (!branchId) {
                alert('Please select a branch first.');
                return;
            }
            loadProducts(branchId, searchInput.value);
        });
        // --- End AJAX Product Load ---

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
        // Initial call to update cart UI to display pre-filled items if in edit mode
        updateCartUI();
        updateDiscountSymbol(); // Set the correct discount symbol on load
    });
</script>
@endsection