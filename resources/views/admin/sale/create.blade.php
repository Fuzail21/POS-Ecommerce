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

            <!-- Product Search Panel -->
            <div class="col-md-7">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-5 mb-3">
                                <label for="customer_id">Select Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="branch_id">Select Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-3 text-right">
                                <button type="button" class="btn btn-outline-primary mt-4 w-100" data-toggle="modal" data-target="#addCustomerModal">
                                    + Customer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        <h4>Search Products</h4>
                        <form method="GET" action="{{ route('sales.create') }}" style="display: flex; gap: 10px; align-items: center;">
                            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}" style="flex: 1;" autocomplete="off">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>


                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <div class="row" id="product-list">
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
                                                        {{ $variant->variant_name }} - ${{ number_format($variant->sale_price, 2) }}
                                                        {{ !$variant->in_stock ? '(Out of Stock)' : '(Stock: '.$variant->stock_quantity.')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>
                                        @else
                                            <p class="mb-1">${{ number_format($product->sale_price, 2) }} 
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

            <!-- Cart Panel -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Shopping Cart</h4>
                    </div>
                    <div id="stock-error" class="alert alert-danger d-none" role="alert"></div>
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
                            <tbody id="cart-items"></tbody>
                        </table>

                        <div class="mt-3">
                            <p><strong>Subtotal:</strong> $<span id="subtotal">0.00</span></p>
                            <p><strong>Discount:</strong> $<span id="discount">0.00</span></p>
                            <p><strong>Tax (1%):</strong> $<span id="tax">0.00</span></p>
                            <hr>
                            <h5><strong>Total:</strong> $<span id="total">0.00</span></h5>
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
                <input type="hidden" id="subtotal_hidden" name="subtotal">
                <input type="hidden" id="tax_hidden" name="tax">
                <input type="hidden" id="discount_hidden" name="discount">
                <input type="hidden" name="balance_due" id="balance_due_raw">
                <input type="hidden" name="customer_id" id="selected_customer_id">
                <input type="hidden" name="branch_id" id="selected_branch_id">


                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Payment</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
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


<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cart = {};
    const taxRate = 0.01;
    const discountAmount = 0;

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
    const customerSelect = document.getElementById('customer_id');
    const selectedCustomerInput = document.getElementById('selected_customer_id');
    const branchSelect = document.getElementById('branch_id');
    const selectedBranchInput = document.getElementById('selected_branch_id');

    selectedCustomerInput.value = customerSelect.value; // set on load
    selectedBranchInput.value = branchSelect.value; // set on load


    customerSelect.addEventListener('change', () => {
        selectedCustomerInput.value = customerSelect.value;
    });

    branchSelect.addEventListener('change', () => {
        selectedBranchInput.value = branchSelect.value;
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
                <td>$${lineTotal.toFixed(2)}</td>
                <td><button class="btn btn-sm btn-danger btn-remove" data-id="${id}">&times;</button></td>
            `;
            cartItemsEl.appendChild(tr);
        }

        const discount = discountAmount;
        const tax = (subtotal - discount) * taxRate;
        const total = subtotal - discount + tax;

        subtotalEl.textContent = subtotal.toFixed(2);
        discountEl.textContent = discount.toFixed(2);
        taxEl.textContent = tax.toFixed(2);
        totalEl.textContent = total.toFixed(2);

        document.getElementById('subtotal_hidden').value = subtotal.toFixed(2);
        document.getElementById('discount_hidden').value = discount.toFixed(2);
        document.getElementById('tax_hidden').value = tax.toFixed(2);
        totalPayableInput.value = total.toFixed(2);
        const structuredCart = {};

        for (const id in cart) {
            const [type, numericId] = id.split('-');
            structuredCart[id] = {
                type: type, // "product" or "variant"
                id: parseInt(numericId),
                ...cart[id]
            };
        }

        cartDataInput.value = JSON.stringify(structuredCart);

        chargeBtn.disabled = Object.keys(cart).length === 0;
    }

    function addToCart(id, name, price, unit_id) {
        if (cart[id]) {
            cart[id].qty++;
        } else {
            cart[id] = {
                name,
                sale_price: price,
                unit_id,
                qty: 1
            };
        }
        updateCartUI();
    }

    document.querySelectorAll('.variant-selector').forEach(selector => {
        selector.addEventListener('change', function () {
            const card = this.closest('.product-item');
            const selected = this.options[this.selectedIndex];
            const id = selected.value;
            const name = selected.dataset.name;
            const price = parseFloat(selected.dataset.price);
            const unit_id = selected.dataset.unitId; // ✅ FIXED HERE
            const addBtn = card.querySelector('.add-variant-to-cart');
    
            addBtn.disabled = false;
            addBtn.dataset.id = id;
            addBtn.dataset.name = name;
            addBtn.dataset.price = price;
            addBtn.dataset.unitId = unit_id;
        });
    });


    document.querySelectorAll('.add-variant-to-cart').forEach(btn => {
        const card = $(this).closest('.card');
        const quantity = card.find('.variant-quantity').val();
        const selectedOption = card.find('.variant-selector option:selected');
        const stock = selectedOption.data('stock');

        if (parseInt(quantity) > stock) {
            alert('Requested quantity exceeds available stock.');
            return;
        }

        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const price = parseFloat(btn.getAttribute('data-price'));
            const unit_id = btn.getAttribute('data-unit-id');

            addToCart(id, name, price, unit_id);
        });
    });

    document.querySelectorAll('.add-simple-to-cart').forEach(btn => {
        const quantity = $(this).closest('.card').find('.simple-quantity').val();
        const maxStock = $(this).data('stock');

        if (parseInt(quantity) > maxStock) {
            alert('Requested quantity exceeds available stock.');
            return;
        }

        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const price = parseFloat(btn.getAttribute('data-price'));
            const unit_id = btn.getAttribute('data-unit-id');

            addToCart(id, name, price, unit_id);
        });
    });

    cartItemsEl.addEventListener('click', e => {
        const btn = e.target;
        const id = btn.getAttribute('data-id');
        if (!id) return;

        if (btn.classList.contains('btn-increment')) {
            cart[id].qty++;
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

    amountPaidInput.addEventListener('input', () => {
        const total = parseFloat(totalEl.textContent) || 0;
        const paid = parseFloat(amountPaidInput.value) || 0;
        const due = total - paid;
        balanceDisplay.value = (due >= 0 ? 'Due: $' : 'Change: $') + Math.abs(due).toFixed(2);
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

    function showStockError(message) {
    const errorDiv = document.getElementById('stock-error');
    errorDiv.textContent = message;
    errorDiv.classList.remove('d-none');

    // Auto-hide after 3 seconds
    setTimeout(() => {
        errorDiv.classList.add('d-none');
    }, 3000);
}


    
});
</script>
@endsection
