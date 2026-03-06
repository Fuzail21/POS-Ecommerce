@extends('layouts.frontend.app')

@section('frontend_content')

@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp

<div class="page-header-band">
    <div class="container text-center">
        <h1>Product Detail</h1>
        <nav>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('store.shop') }}">Shop</a></li>
                <li class="breadcrumb-item active">{{ Str::words($product->name, 5, '...') }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">

            {{-- Product Image --}}
            <div class="col-lg-5">
                <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden; aspect-ratio:1/1; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-sm);">
                    @if(!empty($product->product_img))
                        <img src="{{ asset('storage/'.$product->product_img) }}"
                             id="product_main_image"
                             style="width:100%; height:100%; object-fit:cover;"
                             alt="{{ $product->name }}">
                    @else
                        <div id="product_main_image" style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f1f5f9; gap:.75rem;">
                            <i class="fas fa-image" style="font-size:3.5rem; color:#cbd5e1;"></i>
                            <span style="font-size:.8rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.8px;">No Image</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Details --}}
            <div class="col-lg-7">
                <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:var(--clr-primary); background:rgba(37,99,235,.08); padding:4px 14px; border-radius:50px;">
                    {{ $product->category->name ?? 'General' }}
                </span>

                <h1 style="font-size:1.6rem; font-weight:800; color:var(--clr-dark); margin-top:.85rem; margin-bottom:.75rem; line-height:1.3;">
                    {{ $product->name }}
                </h1>

                {{-- Price --}}
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-weight:800; color:var(--clr-dark); margin-bottom:.25rem;">
                        <span id="display_final_price">
                            @if($product->has_discount)
                                <del style="font-size:1rem; color:#94a3b8; font-weight:400;">{{ $setting->currency_symbol ?? '' }} {{ number_format($product->actual_price, 2) }}</del>
                                <span style="color:var(--clr-primary); margin-left:.4rem;">{{ $setting->currency_symbol ?? '' }} {{ number_format($product->final_price, 2) }}</span>
                            @else
                                <span style="color:var(--clr-primary);">{{ $setting->currency_symbol ?? '' }} {{ number_format($product->actual_price ?? 0, 2) }}</span>
                            @endif
                        </span>
                        <span id="display_unit" style="font-size:.875rem; font-weight:500; color:var(--clr-muted);">/ {{ $product->displayUnit->name ?? 'unit' }}</span>
                    </h3>
                    @if($product->has_discount && $product->actual_price > 0)
                    @php $pct = round((($product->actual_price - $product->final_price) / $product->actual_price) * 100); @endphp
                    <span style="font-size:.75rem; font-weight:700; background:#fef2f2; color:#ef4444; padding:3px 10px; border-radius:50px;">-{{ $pct }}% OFF</span>
                    @endif
                </div>

                <p style="font-size:.9rem; color:var(--clr-muted); line-height:1.75; margin-bottom:1.75rem;">
                    {{ $product->description ?? 'No description available.' }}
                </p>

                {{-- Variant Selection --}}
                @if($product->has_variants && $product->variants->isNotEmpty())
                <div class="mb-3">
                    <label for="color-select" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">Color</label>
                    <select class="form-select" id="color-select" name="color" style="width:220px; border:1.5px solid var(--clr-border); border-radius:10px; font-size:.875rem; padding:10px 14px;">
                        <option value="">Select Color</option>
                        @foreach($colors as $color)
                            <option value="{{ $color }}">{{ $color }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="size-select" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">Size</label>
                    <select class="form-select" id="size-select" name="size" style="width:220px; border:1.5px solid var(--clr-border); border-radius:10px; font-size:.875rem; padding:10px 14px;">
                        <option value="">Select Size</option>
                    </select>
                </div>
                @endif

                {{-- Quantity Selector --}}
                <div class="mb-4">
                    <label style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.5rem;">Quantity</label>
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; border:1.5px solid var(--clr-border); border-radius:10px; overflow:hidden; background:#fff;">
                            <button type="button" id="btn_minus"
                                    style="width:40px; height:40px; border:none; background:transparent; cursor:pointer; font-size:.8rem; color:var(--clr-dark); display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="text" id="product_quantity" value="1" min="1"
                                   style="width:48px; height:40px; text-align:center; border:none; border-left:1px solid var(--clr-border); border-right:1px solid var(--clr-border); font-size:.9rem; font-weight:700; color:var(--clr-dark); outline:none;">
                            <button type="button" id="btn_plus"
                                    style="width:40px; height:40px; border:none; background:transparent; cursor:pointer; font-size:.8rem; color:var(--clr-dark); display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <span style="font-size:.8rem; color:var(--clr-muted);">
                            Available: <strong id="total_stock_display" style="color:var(--clr-dark);"></strong> {{ $product->baseUnit->name ?? 'units' }}
                        </span>
                    </div>
                </div>

                {{-- Add to Cart Form --}}
                <form action="{{ route('cart.add') }}" method="POST" id="add_to_cart_form" style="z-index:3; position:relative;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" id="cart_quantity_input" value="1">
                    <input type="hidden" name="stock" id="cart_stock_input" value="{{ $product->stock_quantity }}">
                    <input type="hidden" name="price" id="cart_price_input" value="{{ $product->has_discount ? $product->final_price : ($product->actual_price ?? 0) }}">
                    <input type="hidden" name="variant_id" id="cart_variant_id_input" value="">

                    <div class="d-flex gap-3 flex-wrap mb-4">
                        @if($product->in_stock)
                            <button type="submit" id="add_to_cart_button" class="btn-prim" style="padding:13px 28px; font-size:.9rem;">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button type="button" id="out_of_stock_button" class="btn-ghost" style="padding:13px 28px; font-size:.9rem; display:none;" disabled>
                                <i class="fas fa-ban"></i> Out of Stock
                            </button>
                        @else
                            <button type="submit" id="add_to_cart_button" class="btn-prim" style="padding:13px 28px; font-size:.9rem; display:none;">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button type="button" id="out_of_stock_button" class="btn-ghost" style="padding:13px 28px; font-size:.9rem;" disabled>
                                <i class="fas fa-ban"></i> Out of Stock
                            </button>
                        @endif
                        <a href="{{ route('store.shop') }}" class="btn-outline" style="padding:13px 24px; font-size:.9rem; text-decoration:none;">
                            <i class="fas fa-arrow-left"></i> Back to Shop
                        </a>
                    </div>
                </form>

                {{-- Trust Badges --}}
                <div style="border-top:1px solid var(--clr-border); padding-top:1.25rem; display:flex; gap:1.5rem; flex-wrap:wrap;">
                    <div style="font-size:.8rem; color:var(--clr-muted); display:flex; align-items:center; gap:.4rem;">
                        <i class="fas fa-shield-alt" style="color:#22c55e;"></i> Secure Payment
                    </div>
                    <div style="font-size:.8rem; color:var(--clr-muted); display:flex; align-items:center; gap:.4rem;">
                        <i class="fas fa-truck" style="color:var(--clr-primary);"></i> Fast Delivery
                    </div>
                    <div style="font-size:.8rem; color:var(--clr-muted); display:flex; align-items:center; gap:.4rem;">
                        <i class="fas fa-undo" style="color:#f59e0b;"></i> Easy Returns
                    </div>
                </div>
            </div>

            {{-- Description Panel --}}
            <div class="col-12">
                <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden;">
                    <div style="background:#f8fafc; border-bottom:1px solid var(--clr-border); padding:.875rem 1.5rem;">
                        <span style="font-size:.85rem; font-weight:700; color:var(--clr-dark);">
                            <i class="fas fa-align-left me-2" style="color:var(--clr-primary);"></i> Product Description
                        </span>
                    </div>
                    <div style="padding:1.5rem; font-size:.9rem; color:var(--clr-muted); line-height:1.85;">
                        {!! nl2br(e($product->description ?? 'No detailed description available.')) !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@section('frontend_js')
<script>
    // Prevent script from running multiple times if included more than once
    if (window.productPageScriptInitialized) {
        console.log('Product page script already initialized. Skipping re-initialization.');
    } else {
        window.productPageScriptInitialized = true;

        document.addEventListener('DOMContentLoaded', function () {
            const productQuantityInput = document.getElementById('product_quantity');
            const btnMinus = document.getElementById('btn_minus');
            const btnPlus = document.getElementById('btn_plus');
            const colorSelect = document.getElementById('color-select');
            const sizeSelect = document.getElementById('size-select');

            const displayFinalPrice = document.getElementById('display_final_price');
            const cartQuantityInput = document.getElementById('cart_quantity_input');
            const cartStockInput = document.getElementById('cart_stock_input');
            const cartPriceInput = document.getElementById('cart_price_input');
            const cartVariantIdInput = document.getElementById('cart_variant_id_input');
            const addToCartButton = document.getElementById('add_to_cart_button');
            const outOfStockButton = document.getElementById('out_of_stock_button');
            const totalStockDisplay = document.getElementById('total_stock_display');
            const productMainImage = document.getElementById('product_main_image');

            const productId = "{{ $product->id }}"; // Get product ID from PHP
            const baseActualPrice = parseFloat("{{ $product->actual_price ?? 0 }}");
            const baseFinalPrice = parseFloat("{{ $product->final_price ?? ($product->actual_price ?? 0) }}");
            const productHasDiscount = "{{ $product->has_discount }}" === "1";
            const currencySymbol = "{{ $setting->currency_symbol ?? '$' }}";
            const productHasVariants = "{{ $product->has_variants }}" === "1";

            let currentStock;
            let selectedVariantActualPrice;
            let selectedVariantFinalPrice;
            let selectedVariantHasDiscount;

            // Store initial product image for fallback
            const initialProductImage = productMainImage.src;

            // Store all variants data for client-side filtering of sizes
            const allVariantsClientData = @json($product->variants);

            // Create a map for quick lookup of available sizes by color
            const availableSizesByColor = {};
            allVariantsClientData.forEach(variant => {
                const color = variant.color || 'NULL'; // Use 'NULL' for variants without a color
                const size = variant.size || 'NULL';   // Use 'NULL' for variants without a size

                if (!availableSizesByColor[color]) {
                    availableSizesByColor[color] = new Set();
                }
                availableSizesByColor[color].add(size);
            });

            // Function to update price, stock, quantity input, and button states
            function updatePriceAndStock() {
                console.log('--- updatePriceAndStock called ---');
                console.log('currentStock:', currentStock);

                let displayedActualPrice = selectedVariantActualPrice;
                let displayedFinalPrice = selectedVariantFinalPrice;
                let displayedHasDiscount = selectedVariantHasDiscount;

                // Update displayed prices
                if (displayedHasDiscount) {
                    displayFinalPrice.innerHTML = `<del style="font-size:1rem; color:#94a3b8; font-weight:400;">${currencySymbol} ${displayedActualPrice.toFixed(2)}</del><span style="color:var(--clr-primary); margin-left:.4rem;">${currencySymbol} ${displayedFinalPrice.toFixed(2)}</span>`;
                } else {
                    displayFinalPrice.innerHTML = `<span style="color:var(--clr-primary);">${currencySymbol} ${displayedActualPrice.toFixed(2)}</span>`;
                }
                console.log('Displayed Price:', displayFinalPrice.innerHTML);

                // Update stock display
                cartStockInput.value = currentStock;
                totalStockDisplay.textContent = currentStock;
                console.log('Updated totalStockDisplay to:', currentStock);

                // Quantity input and button state logic
                let currentInputValue = parseInt(productQuantityInput.value);

                // Ensure quantity is within valid range (1 to currentStock)
                if (isNaN(currentInputValue) || currentInputValue < 1) {
                    currentInputValue = 1;
                }
                if (currentInputValue > currentStock) {
                    currentInputValue = currentStock;
                }
                productQuantityInput.value = currentInputValue;
                cartQuantityInput.value = currentInputValue; // Also update hidden cart quantity input

                // Disable/enable + and - buttons based on current quantity and stock
                if (currentInputValue >= currentStock) {
                    btnPlus.disabled = true;
                } else {
                    btnPlus.disabled = false;
                }

                if (currentInputValue <= 1) {
                    btnMinus.disabled = true;
                } else {
                    btnMinus.disabled = false;
                }

                // Also handle overall product in/out of stock state
                // Disable if out of stock OR if product has variants and no variant is selected
                if (currentStock <= 0 || (productHasVariants && !cartVariantIdInput.value)) {
                    addToCartButton.style.display = 'none';
                    outOfStockButton.style.display = 'block';
                    productQuantityInput.value = 0; // Reset quantity if out of stock or no variant selected
                    productQuantityInput.disabled = true;
                    btnMinus.disabled = true;
                    btnPlus.disabled = true;
                    console.log('Product is out of stock or no variant selected.');
                } else {
                    addToCartButton.style.display = 'block';
                    outOfStockButton.style.display = 'none';
                    productQuantityInput.disabled = false; // Re-enable if it was disabled due to 0 stock
                    // Re-evaluate plus/minus buttons based on currentInputValue and currentStock
                    if (currentInputValue >= currentStock) {
                        btnPlus.disabled = true;
                    } else {
                        btnPlus.disabled = false;
                    }
                    if (currentInputValue <= 1) {
                        btnMinus.disabled = true;
                    } else {
                        btnMinus.disabled = false;
                    }
                    console.log('Product is in stock.');
                }

                cartPriceInput.value = displayedFinalPrice.toFixed(2); // Always send the final (discounted) price to cart
                console.log('cartQuantityInput:', cartQuantityInput.value, 'cartPriceInput:', cartPriceInput.value);
                console.log('--- updatePriceAndStock finished ---');
            }

            // Function to populate size dropdown based on selected color
            function populateSizes(selectedColor) {
                // Clear current size options
                sizeSelect.innerHTML = '<option value="">Select Size</option>';

                // Get sizes available for the selected color
                const sizesForColor = availableSizesByColor[selectedColor || 'NULL']; // Use 'NULL' if no color selected

                if (sizesForColor) {
                    // Convert Set to Array, filter out 'NULL' if it's not a real size option, then sort
                    const sortedSizes = Array.from(sizesForColor)
                                            .filter(size => size !== 'NULL')
                                            .sort();

                    sortedSizes.forEach(size => {
                        const option = document.createElement('option');
                        option.value = size;
                        option.textContent = size;
                        sizeSelect.appendChild(option);
                    });

                    // If there's a 'NULL' size and no other sizes, add it back (for variants with only color, no size)
                    if (sizesForColor.has('NULL') && sortedSizes.length === 0) {
                        const option = document.createElement('option');
                        option.value = ''; // Represents no size selected for the variant
                        option.textContent = 'N/A'; // Or 'No Specific Size'
                        sizeSelect.appendChild(option);
                    }
                }
                // Reset size selection
                sizeSelect.value = '';
            }

            // Function to fetch variant details via AJAX
            async function fetchVariantDetails() {
                const selectedColor = colorSelect ? colorSelect.value : '';
                const selectedSize = sizeSelect ? sizeSelect.value : '';

                // If product has variants, and either color or size (or both) are selected
                if (productHasVariants && (selectedColor || selectedSize)) {
                    try {
                        const response = await fetch("{{ route('product.getVariant') }}", { // Ensure this route is defined in web.php
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_id: productId,
                                color: selectedColor || null, // Send null if empty string
                                size: selectedSize || null    // Send null if empty string
                            })
                        });

                        const data = await response.json();
                        console.log('AJAX response for variant:', data);

                        if (data.success && data.variant) {
                            const variant = data.variant;
                            cartVariantIdInput.value = variant.id;
                            currentStock = variant.stock_quantity;
                            selectedVariantActualPrice = parseFloat(variant.actual_price);
                            selectedVariantFinalPrice = parseFloat(variant.final_price);
                            selectedVariantHasDiscount = variant.has_discount;

                            // Update image if variant has a specific image, otherwise revert to main product image
                            if (variant.product_img) {
                                productMainImage.src = variant.product_img;
                            } else {
                                productMainImage.src = initialProductImage;
                            }
                        } else {
                            // Variant not found or out of stock for the combination
                            console.warn('Variant not found or out of stock:', data.message);
                            currentStock = 0; // Indicate out of stock for this combination
                            cartVariantIdInput.value = ''; // No valid variant ID
                            selectedVariantActualPrice = baseActualPrice; // Revert to base price display
                            selectedVariantFinalPrice = baseFinalPrice;
                            selectedVariantHasDiscount = productHasDiscount;
                            productMainImage.src = initialProductImage; // Revert to base image
                        }
                    } catch (error) {
                        console.error('Error fetching variant details:', error);
                        // Fallback to base product details on error
                        currentStock = parseInt("{{ $product->stock_quantity ?? 0 }}");
                        selectedVariantActualPrice = baseActualPrice;
                        selectedVariantFinalPrice = baseFinalPrice;
                        selectedVariantHasDiscount = productHasDiscount;
                        productMainImage.src = initialProductImage;
                        cartVariantIdInput.value = '';
                    } finally {
                        // Reset quantity to 1 when variant changes, then update
                        productQuantityInput.value = 1;
                        updatePriceAndStock();
                    }
                } else {
                    // No variant selected or product has no variants, revert to base product details
                    console.log('No variant selected or product has no variants. Reverting to base product details.');
                    currentStock = parseInt("{{ $product->stock_quantity ?? 0 }}");
                    selectedVariantActualPrice = baseActualPrice;
                    selectedVariantFinalPrice = baseFinalPrice;
                    selectedVariantHasDiscount = productHasDiscount;
                    productMainImage.src = initialProductImage;
                    cartVariantIdInput.value = ''; // No specific variant selected
                    productQuantityInput.value = 1; // Reset quantity
                    updatePriceAndStock();
                }
            }

            // Initial setup on page load
            if (productHasVariants) {
                // Populate sizes initially with all sizes
                populateSizes(''); // Pass empty string to show all sizes initially

                // Attach event listeners for color and size dropdowns
                if (colorSelect) {
                    colorSelect.addEventListener('change', function() {
                        populateSizes(this.value); // Update sizes based on selected color
                        fetchVariantDetails(); // Fetch variant details for the new combination
                    });
                }
                if (sizeSelect) {
                    sizeSelect.addEventListener('change', fetchVariantDetails); // Fetch variant details when size changes
                }
                // Initial fetch for variants based on default selections (or no selection)
                fetchVariantDetails();
            } else {
                // If no variants, just set initial stock and prices from base product
                currentStock = parseInt("{{ $product->stock_quantity ?? 0 }}");
                selectedVariantActualPrice = baseActualPrice;
                selectedVariantFinalPrice = baseFinalPrice;
                selectedVariantHasDiscount = productHasDiscount;
                updatePriceAndStock();
            }


            // Event listener for quantity minus button
            btnMinus.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                console.log('Minus button clicked.');
                let quantity = parseInt(productQuantityInput.value);
                if (quantity > 1) {
                    productQuantityInput.value = quantity - 1;
                    updatePriceAndStock();
                }
            });

            // Event listener for quantity plus button
            btnPlus.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                console.log('Plus button clicked.');
                let quantity = parseInt(productQuantityInput.value);
                if (quantity < currentStock) {
                    productQuantityInput.value = quantity + 1;
                    updatePriceAndStock();
                }
            });

            // Event listener for direct input of quantity
            productQuantityInput.addEventListener('change', function () {
                console.log('Quantity input changed.');
                updatePriceAndStock();
            });

            // Add to Cart button click handler (common for both variant/non-variant products)
            document.getElementById('add_to_cart_form').addEventListener('submit', function(event) {
                 // Prevent default form submission

                const variantId = cartVariantIdInput.value;
                const quantity = cartQuantityInput.value;
                const price = cartPriceInput.value; // The final calculated price

                if (!variantId) {
                    // Use a custom message box instead of alert()
                    // You would need to implement a modal or similar UI for this
                    console.error('Please select a valid product or variant.');
                    alert('Please select a valid product or variant.'); // Fallback for demonstration
                    return;
                }

                // Implement your AJAX call to add to cart
                fetch(this.action, { // Use the form's action attribute for the URL
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel CSRF token
                    },
                    body: JSON.stringify({
                        product_id: productId, // Main product ID
                        variant_id: variantId, // The ID of the selected variant (or main product if no variants)
                        quantity: quantity,
                        price: price // Pass the calculated price to the cart
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Use a custom message box instead of alert()
                        console.log('Product added to cart!');
                        alert('Product added to cart!'); // Fallback for demonstration
                        // Optionally update cart count in header, etc.
                    } else {
                        // Use a custom message box instead of alert()
                        console.error('Error adding to cart:', data.message);
                        alert('Error adding to cart: ' + data.message); // Fallback for demonstration
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Use a custom message box instead of alert()
                    // alert('An error occurred. Please try again.'); // Fallback for demonstration
                });
            });
        });
    }
</script>
@endsection
