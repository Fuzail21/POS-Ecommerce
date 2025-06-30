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
    /* Styles specific to the create.blade.php layout provided */
    #cart-items {
        max-height: 350px; /* Adjust as needed for scrollable cart */
        overflow-y: auto;
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

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">All Products</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <input type="text" class="form-control" id="product-search" placeholder="Search products by name, SKU, or barcode...">
                        </div>
                        <div class="row" id="product-list">
                            {{-- Products will be loaded here via AJAX or on initial page load --}}
                            @foreach ($products as $product)
                                @php
                                    // Determine if the product card should be greyed out
                                    $isProductOutOfStock = !$product->in_stock;
                                    // Check if it has actual variants (not just the synthetic one)
                                    $hasActualVariants = $product->variants->where('variant_name', '!=', null)->isNotEmpty();
                                @endphp
                                <div class="col-md-3 mb-2 product-item d-flex">
                                    <div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 {{ $isProductOutOfStock && !$hasActualVariants ? 'bg-light text-muted pointer-events-none opacity-50' : '' }}">

                                        {{-- Product Image or Placeholder --}}
                                        @if (!empty($product->product_img))
                                            <img src="{{ asset('storage/' . $product->product_img) }}" alt="Product Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin: auto;">
                                        @else
                                            <div style="width: 100px; height: 100px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin: auto;">N/A</div>
                                        @endif

                                        <h6 class="mt-2 mb-1">{{ htmlspecialchars($product->name) }}</h6>

                                        @if ($hasActualVariants)
                                            {{-- Display dropdown for products with actual variants --}}
                                            <select class="form-control mb-2 variant-selector mt-auto" data-product-id="{{ $product->id }}">
                                                <option disabled selected>Choose Variant</option>
                                                @foreach ($product->variants->where('variant_name', '!=', null) as $variant)
                                                    @php
                                                        $disabled = !$variant->in_stock ? 'disabled' : '';
                                                        $stockText = !$variant->in_stock ? '(Out of Stock)' : '(Stock: ' . $variant->stock_quantity . ')';
                                                        $opacityClass = !$variant->in_stock ? 'opacity-50' : '';
                                                    @endphp
                                                    <option {{ $disabled }} value="variant-{{ $variant->id }}"
                                                            data-name="{{ htmlspecialchars($product->name . ' - ' . $variant->variant_name) }}"
                                                            data-price="{{ $variant->sale_price }}"
                                                            data-stock="{{ $variant->stock_quantity }}"
                                                            data-unit-id="{{ $product->default_display_unit_id }}"
                                                            class="{{ $opacityClass }}">
                                                        {{ htmlspecialchars($variant->variant_name) }} - {{ $setting->currency_symbol }} {{ number_format($variant->sale_price, 2) }} {{ $stockText }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>
                                        @else
                                            {{-- Display directly for simple products without actual variants --}}
                                            <p class="mb-1">{{ $setting->currency_symbol }} {{ number_format($product->sale_price, 2) }}
                                                <br><small>(Stock: {{ $product->stock_quantity }})</small></p>
                                            @if ($product->in_stock)
                                                <button class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart"
                                                        data-id="product-{{ $product->id }}"
                                                        data-name="{{ htmlspecialchars($product->name) }}"
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Order Items</h4>
                            <div id="stock-error" class="alert alert-danger d-none mt-2"></div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    {{-- Cart items will be loaded here by JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Subtotal: <span id="subtotal">{{ $setting->currency_symbol ?? '$' }} 0.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Discount:
                                    <div class="input-group input-group-sm w-50">
                                        <span class="input-group-text">{{ $setting->currency_symbol ?? '$' }}</span>
                                        <input type="number" step="0.01" min="0" class="form-control text-end" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $quotation->discount_amount ?? 0) }}">
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Tax:
                                    <div class="input-group input-group-sm w-50">
                                        <span class="input-group-text">{{ $setting->currency_symbol ?? '$' }}</span>
                                        <input type="number" step="0.01" min="0" class="form-control text-end" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', $quotation->tax_amount ?? 0) }}">
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Shipping:
                                    <div class="input-group input-group-sm w-50">
                                        <span class="input-group-text">{{ $setting->currency_symbol ?? '$' }}</span>
                                        <input type="number" step="0.01" min="0" class="form-control text-end" id="shipping_amount" name="shipping_amount" value="{{ old('shipping_amount', $quotation->shipping_amount ?? 0) }}">
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center font-weight-bold">
                                    Total: <span id="total-amount">{{ $setting->currency_symbol ?? '$' }} 0.00</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form id="checkout-form" action="{{ route('quotations.update', $quotation->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="quotation_items" id="quotation_items_input">
                            <input type="hidden" name="total_quantity" id="total_qty_input">
                            <input type="hidden" name="total_amount" id="final_total_amount_input">


                            <div class="form-group">
                                <label for="customer_id">Customer</label>
                                <select class="form-control" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $quotation->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="warehouse_id">Warehouse</label>
                                <select class="form-control" id="warehouse_id" name="warehouse_id" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ $quotation->warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="sent" {{ $quotation->status == 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="pending" {{ $quotation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $quotation->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $quotation->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="quotation_date">Quotation Date</label>
                                <input type="date" class="form-control" id="quotation_date" name="quotation_date" value="{{ old('quotation_date', $quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->format('Y-m-d') : date('Y-m-d')) }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Update Quotation</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let cart = {}; // Global cart object

        const productListDiv = document.getElementById('product-list');
        const searchInput = document.getElementById('product-search');
        const currencySymbol = "{{ $setting->currency_symbol ?? '$' }}";

        // --- Initialize Cart from Existing Quotation Items ---
        const initialQuotationItems = @json($quotation->items);
        initialQuotationItems.forEach(item => {
            const itemId = item.product_variant_id ? `variant-${item.product_variant_id}` : `product-${item.product_id}`;
            const itemName = item.product_variant_id ? `${item.product.name} - ${item.product_variant.variant_name}` : item.product.name;
            const itemPrice = parseFloat(item.unit_price);
            const itemQuantity = parseInt(item.quantity);
            const itemStock = item.product_variant_id ? (item.product_variant.inventory_stocks_sum_quantity_in_base_unit / (item.product_variant.product.base_unit.conversion_factor ?? 1)) : (item.product.inventory_stocks_sum_quantity_in_base_unit / (item.product.base_unit.conversion_factor ?? 1)); // Assuming these are available from load or re-fetch

            // Ensure base_unit and inventory_stocks are loaded for existing items for stock calculation
            // If not, you might need to adjust the PHP to load these relationships on quotation items' products/variants.
            const productBaseUnitConversion = item.product.base_unit ? item.product.base_unit.conversion_factor : 1;
            const variantProductBaseUnitConversion = (item.product_variant && item.product_variant.product && item.product_variant.product.base_unit) ? item.product_variant.product.base_unit.conversion_factor : 1;

            const calculatedStock = item.product_variant_id
                ? (item.product_variant.inventory_stocks ? item.product_variant.inventory_stocks.reduce((sum, stock) => sum + stock.quantity_in_base_unit, 0) / variantProductBaseUnitConversion : 0)
                : (item.product.inventory_stocks ? item.product.inventory_stocks.reduce((sum, stock) => sum + stock.quantity_in_base_unit, 0) / productBaseUnitConversion : 0);


            cart[itemId] = {
                id: itemId,
                name: itemName,
                price: itemPrice,
                quantity: itemQuantity,
                stock: calculatedStock, // Use calculated stock
                unitId: item.unit_id // Pass the unit ID
            };
        });

        // Function to render cart items and update totals
        function renderCart() {
            const cartItemsBody = document.getElementById('cart-items');
            cartItemsBody.innerHTML = ''; // Clear existing items

            let subtotal = 0;

            for (const itemId in cart) {
                const item = cart[itemId];
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                const row = `
                    <tr data-item-id="${item.id}">
                        <td>${item.name}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm item-quantity"
                                   data-item-id="${item.id}"
                                   value="${item.quantity}"
                                   min="1"
                                   max="${item.stock}" style="width: 80px;">
                        </td>
                        <td>${currencySymbol} ${item.price.toFixed(2)}</td>
                        <td class="item-total">${currencySymbol} ${itemTotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item" data-item-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                cartItemsBody.insertAdjacentHTML('beforeend', row);
            }

            // Update total calculations based on cart items and input fields
            const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const taxAmount = parseFloat(document.getElementById('tax_amount').value) || 0;
            const shippingAmount = parseFloat(document.getElementById('shipping_amount').value) || 0;

            document.getElementById('subtotal').textContent = `${currencySymbol} ${subtotal.toFixed(2)}`;
            const totalAmount = subtotal - discountAmount + taxAmount + shippingAmount;
            document.getElementById('total-amount').textContent = `${currencySymbol} ${totalAmount.toFixed(2)}`;

            // Attach event listeners for quantity changes and remove buttons
            attachCartEventListeners();
            updateHiddenInputs(); // Update hidden inputs for form submission
        }

        // --- Event Listeners for Cart Actions ---
        function attachCartEventListeners() {
            document.querySelectorAll('.item-quantity').forEach(input => {
                input.removeEventListener('change', handleQuantityChange); // Prevent duplicate listeners
                input.addEventListener('change', handleQuantityChange);
            });

            document.querySelectorAll('.remove-item').forEach(button => {
                button.removeEventListener('click', handleRemoveItem); // Prevent duplicate listeners
                button.addEventListener('click', handleRemoveItem);
            });

            document.getElementById('discount_amount').addEventListener('input', renderCart);
            document.getElementById('tax_amount').addEventListener('input', renderCart);
            document.getElementById('shipping_amount').addEventListener('input', renderCart);
        }

        function handleQuantityChange(event) {
            const itemId = event.target.dataset.itemId;
            let newQuantity = parseInt(event.target.value);
            if (isNaN(newQuantity) || newQuantity < 1) {
                newQuantity = 1;
                event.target.value = 1;
            }
            if (newQuantity > cart[itemId].stock) {
                newQuantity = cart[itemId].stock;
                event.target.value = cart[itemId].stock;
                showStockError(`Cannot add more than available stock for ${cart[itemId].name}. Available: ${cart[itemId].stock}`);
            } else {
                // Clear error if quantity is valid
                document.getElementById('stock-error').classList.add('d-none');
            }
            cart[itemId].quantity = newQuantity;
            renderCart();
        }

        function handleRemoveItem(event) {
            const itemId = event.target.closest('button').dataset.itemId;
            delete cart[itemId];
            renderCart();
        }

        // --- Attach Event Listeners to Product List (for adding to cart) ---
        function attachProductEventListeners() {
            document.querySelectorAll('.add-simple-to-cart').forEach(button => {
                button.removeEventListener('click', handleAddSimpleToCart); // Prevent duplicate listeners
                button.addEventListener('click', handleAddSimpleToCart);
            });

            document.querySelectorAll('.variant-selector').forEach(select => {
                select.removeEventListener('change', handleVariantSelectChange); // Prevent duplicate listeners
                select.addEventListener('change', handleVariantSelectChange);
            });

            document.querySelectorAll('.add-variant-to-cart').forEach(button => {
                button.removeEventListener('click', handleAddVariantToCart); // Prevent duplicate listeners
                button.addEventListener('click', handleAddVariantToCart);
            });
        }

        function handleAddSimpleToCart(event) {
            const button = event.target;
            const itemId = button.dataset.id;
            const itemName = button.dataset.name;
            const itemPrice = parseFloat(button.dataset.price);
            const itemStock = parseInt(button.dataset.stock);
            const itemUnitId = button.dataset.unitId;

            if (cart[itemId]) {
                if (cart[itemId].quantity < itemStock) {
                    cart[itemId].quantity++;
                } else {
                    showStockError(`Cannot add more of ${itemName}. Stock limit reached.`);
                }
            } else {
                cart[itemId] = {
                    id: itemId,
                    name: itemName,
                    price: itemPrice,
                    quantity: 1,
                    stock: itemStock,
                    unitId: itemUnitId
                };
            }
            renderCart();
        }

        function handleVariantSelectChange(event) {
            const select = event.target;
            const productId = select.dataset.productId;
            const addButton = select.closest('.card').querySelector('.add-variant-to-cart');

            if (select.value !== 'Choose Variant') {
                addButton.disabled = false;
                const selectedOption = select.options[select.selectedIndex];
                const itemStock = parseInt(selectedOption.dataset.stock);
                if (itemStock <= 0) {
                    addButton.disabled = true;
                    showStockError(`Selected variant is out of stock.`);
                } else {
                    document.getElementById('stock-error').classList.add('d-none');
                }
            } else {
                addButton.disabled = true;
            }
        }

        function handleAddVariantToCart(event) {
            const button = event.target;
            const select = button.closest('.card').querySelector('.variant-selector');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value === 'Choose Variant') {
                alert('Please select a variant first.');
                return;
            }

            const itemId = selectedOption.value;
            const itemName = selectedOption.dataset.name;
            const itemPrice = parseFloat(selectedOption.dataset.price);
            const itemStock = parseInt(selectedOption.dataset.stock);
            const itemUnitId = selectedOption.dataset.unitId;


            if (cart[itemId]) {
                if (cart[itemId].quantity < itemStock) {
                    cart[itemId].quantity++;
                } else {
                    showStockError(`Cannot add more of ${itemName}. Stock limit reached.`);
                }
            } else {
                cart[itemId] = {
                    id: itemId,
                    name: itemName,
                    price: itemPrice,
                    quantity: 1,
                    stock: itemStock,
                    unitId: itemUnitId
                };
            }
            renderCart();
            select.value = 'Choose Variant'; // Reset dropdown
            button.disabled = true; // Disable add button until new variant is selected
        }

        // --- AJAX Search Implementation for Products ---
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchValue = searchInput.value;
                fetch(`{{ route('quotations.edit', $quotation->id) }}?search=${searchValue}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    productListDiv.innerHTML = ''; // Clear current products
                    if (data.products.length === 0) {
                        productListDiv.innerHTML = '<div class="col-12 text-center text-muted py-5">No products found.</div>';
                    } else {
                        data.products.forEach(product => {
                            let productImgSrc = product.product_img ? `{{ asset('storage') }}/${product.product_img}` : 'https://placehold.co/100x100/f0f0f0/808080?text=N/A';
                            const hasActualVariants = product.variants.some(variant => variant.variant_name !== null);

                            let productCardHtml = `
                                <div class="col-md-3 mb-2 product-item d-flex">
                                    <div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 ${!product.in_stock && !hasActualVariants ? 'bg-light text-muted pointer-events-none opacity-50' : ''}">
                                        ${product.product_img ? `<img src="${productImgSrc}" alt="Product Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin: auto;">` : `<div style="width: 100px; height: 100px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin: auto;">N/A</div>`}
                                        <h6 class="mt-2 mb-1">${product.name}</h6>`;

                            if (hasActualVariants) {
                                productCardHtml += `<select class="form-control mb-2 variant-selector mt-auto" data-product-id="${product.id}">
                                                        <option disabled selected>Choose Variant</option>`;
                                product.variants.filter(v => v.variant_name !== null).forEach(variant => {
                                    const disabled = !variant.in_stock ? 'disabled' : '';
                                    const stockText = !variant.in_stock ? '(Out of Stock)' : `(Stock: ${variant.stock_quantity})`;
                                    const opacityClass = !variant.in_stock ? 'opacity-50' : '';
                                    productCardHtml += `<option ${disabled} value="variant-${variant.id}"
                                                                data-name="${product.name} - ${variant.variant_name}"
                                                                data-price="${variant.sale_price}"
                                                                data-stock="${variant.stock_quantity}"
                                                                data-unit-id="${product.default_display_unit_id}"
                                                                class="${opacityClass}">
                                                                ${variant.variant_name} - ${currencySymbol} ${variant.sale_price.toFixed(2)} ${stockText}
                                                            </option>`;
                                });
                                productCardHtml += `</select>
                                                    <button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>`;
                            } else {
                                productCardHtml += `<p class="mb-1">${currencySymbol} ${product.sale_price.toFixed(2)}
                                                    <br><small>(Stock: ${product.stock_quantity})</small></p>`;
                                if (product.in_stock) {
                                    productCardHtml += `<button class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart"
                                                                data-id="product-${product.id}"
                                                                data-name="${product.name}"
                                                                data-price="${product.sale_price}"
                                                                data-stock="${product.stock_quantity}"
                                                                data-unit-id="${product.default_display_unit_id}">
                                                                Add to Cart
                                                            </button>`;
                                } else {
                                    productCardHtml += `<button class="btn btn-sm btn-secondary w-100 mt-auto" disabled>Out of Stock</button>`;
                                }
                            }
                            productCardHtml += `</div></div>`;
                            productListDiv.insertAdjacentHTML('beforeend', productCardHtml); // Add to the DOM
                        });
                    }
                    attachProductEventListeners(); // Re-attach event listeners to newly loaded products
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                    productListDiv.innerHTML = '<div class="col-12 text-center text-danger py-5">Error loading products. Please try again.</div>';
                });
            }, 300); // 300ms debounce
        });
        // --- End AJAX Product Search ---

        function showStockError(message) {
            const errorDiv = document.getElementById('stock-error');
            errorDiv.textContent = message;
            errorDiv.classList.remove('d-none');

            setTimeout(() => {
                errorDiv.classList.add('d-none');
            }, 3000);
        }

        // Function to update hidden inputs for form submission
        function updateHiddenInputs() {
            const itemsInput = document.getElementById('quotation_items_input');
            const totalQtyInput = document.getElementById('total_qty_input');
            const finalTotalAmountInput = document.getElementById('final_total_amount_input');

            let totalQuantity = 0;
            const items = [];
            let totalAmount = 0;

            for (const itemId in cart) {
                const item = cart[itemId];
                totalQuantity += item.quantity;
                totalAmount += item.price * item.quantity; // Sum up for final_total_amount

                items.push({
                    product_id: itemId.startsWith('product-') ? itemId.split('-')[1] : null,
                    product_variant_id: itemId.startsWith('variant-') ? itemId.split('-')[1] : null,
                    quantity: item.quantity,
                    unit_price: item.price,
                    unit_id: item.unitId
                });
            }

            const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const taxAmount = parseFloat(document.getElementById('tax_amount').value) || 0;
            const shippingAmount = parseFloat(document.getElementById('shipping_amount').value) || 0;

            const calculatedFinalTotal = totalAmount - discountAmount + taxAmount + shippingAmount;

            itemsInput.value = JSON.stringify(items);
            totalQtyInput.value = totalQuantity;
            finalTotalAmountInput.value = calculatedFinalTotal.toFixed(2); // Store formatted total

            // Also update the displayed total amount on the page if not already handled by renderCart
            document.getElementById('total-amount').textContent = `${currencySymbol} ${calculatedFinalTotal.toFixed(2)}`;
        }


        // Initial calls on page load
        renderCart(); // Load existing items into the cart display
        attachProductEventListeners(); // Attach event listeners to initial products present on page load

        // Handle modal submission: ensure all hidden inputs are correctly populated right before submission
        document.getElementById('checkout-form').addEventListener('submit', function(event) {
            if (Object.keys(cart).length === 0) {
                alert('Please add at least one product to the quotation.');
                event.preventDefault(); // Prevent form submission
                return;
            }
            updateHiddenInputs(); // Ensure hidden inputs are up-to-date before submission
        });
    });
</script>
@endsection