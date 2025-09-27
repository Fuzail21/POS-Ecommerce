@extends('layouts.frontend.app') {{-- Assuming you have a layout --}}

@section('frontend_content')

@php
    use App\Models\Setting;
    $setting = Setting::first();
    $primaryColor = $setting->primary_color ?? '#0d6efd';
    $secondaryColor = $setting->secondary_color ?? '#6c757d';
@endphp


                <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Product Detail</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('store.shop') }}">Shop</a></li>
                <li class="breadcrumb-item active text-white">Product Detail</li>
            </ol>
        </div>
        <!-- Single Page Header End -->


        <!-- Single Product Start -->
        <div class="container-fluid py-5 mt-5">
            <div class="container py-5">
                <div class="row g-4 mb-5">
                    <div class="col-lg-12 col-xl-12">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="border rounded">
                                    <a href="#">
                                        @if (!empty($product->product_img))
                                            <img src="{{ asset('storage/' . $product->product_img) }}" class="img-fluid rounded" alt="{{ $product->name }}" id="product_main_image">
                                        @else
                                            <img src="{{ asset('build/assets/frontend/img/default-product.jpg') }}" class="img-fluid rounded" alt="No Image" id="product_main_image">
                                        @endif
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h4 class="fw-bold mb-3">{{ $product->name }}</h4>
                                <p class="mb-3">Category: {{ $product->category->name ?? 'Uncategorized' }}</p>

                                {{-- Price Display --}}
                                <h5 class="fw-bold mb-3">
                                    <span id="display_final_price">
                                        @if ($product->has_discount)
                                            <del class="text-danger me-2">{{ $setting->currency_symbol ?? '$' }} {{ number_format($product->actual_price, 2) }}</del>
                                            <span>{{ $setting->currency_symbol ?? '$' }} {{ number_format($product->final_price, 2) }}</span>
                                        @else
                                            <span>{{ $setting->currency_symbol ?? '$' }} {{ number_format($product->actual_price ?? 0, 2) }}</span>
                                        @endif
                                    </span>
                                    <span id="display_unit">/ {{ $product->displayUnit->name ?? 'unit' }}</span>
                                </h5>

                                <p class="mb-4">{{ $product->description ?? 'No description available.' }}</p>

                                {{-- Quantity Input --}}
                                <div class="input-group quantity mb-5" style="width: 100px;">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-minus rounded-circle bg-light border" id="btn_minus">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <input type="text" class="form-control form-control-sm text-center border-0" value="1" id="product_quantity" min="1">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-plus rounded-circle bg-light border" id="btn_plus">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Variant Selection (if product has variants) --}}
                                @if ($product->has_variants && $product->variants->isNotEmpty())
                                    {{-- Using separate color and size dropdowns as per your initial request --}}
                                    <div class="mb-4">
                                        <label for="color-select" class="form-label fw-bold">Select Color:</label>
                                        <select class="form-select" id="color-select" name="color" style="width: 200px;">
                                            <option value="">Select Color</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color }}">{{ $color }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label for="size-select" class="form-label fw-bold">Select Size:</label>
                                        <select class="form-select" id="size-select" name="size" style="width: 200px;">
                                            <option value="">Select Size</option>
                                            {{-- Options will be populated by JavaScript based on color selection --}}
                                        </select>
                                    </div>
                                @endif

                                {{-- Add to Cart Form / Out of Stock --}}
                                <form action="{{ route('cart.add') }}" method="POST" id="add_to_cart_form" style="z-index: 3; position: relative;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" id="cart_quantity_input" value="1">
                                    <input type="hidden" name="stock" id="cart_stock_input" value="{{ $product->stock_quantity }}">
                                    <input type="hidden" name="price" id="cart_price_input" value="{{ $product->has_discount ? $product->final_price : ($product->actual_price ?? 0) }}">
                                    <input type="hidden" name="variant_id" id="cart_variant_id_input" value=""> {{-- For selected variant ID --}}

                                    @if ($product->in_stock)
                                        <button type="submit" class="btn border border-secondary rounded-pill px-3 text-primary" id="add_to_cart_button">
                                            <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                        </button>
                                        <button type="button" class="btn border border-secondary rounded-pill px-3 text-muted" style="display: none;" disabled id="out_of_stock_button">
                                            <i class="fa fa-ban me-2"></i> Out of Stock
                                        </button>
                                    @else
                                        <button type="submit" class="btn border border-secondary rounded-pill px-3 text-primary" style="display: none;" id="add_to_cart_button">
                                            <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                        </button>
                                        <button type="button" class="btn border border-secondary rounded-pill px-3 text-muted" disabled id="out_of_stock_button">
                                            <i class="fa fa-ban me-2"></i> Out of Stock
                                        </button>
                                    @endif
                                </form>
                                <p class="mt-2 text-muted" style="font-size: 0.9em;">
                                    Available Stock: <span id="total_stock_display">
                                        {{-- Initial display will be set by JavaScript on DOMContentLoaded --}}
                                    </span> {{ $product->baseUnit->name ?? 'units' }}
                                </p>
                            </div>
                            <div class="col-lg-12">
                                <nav>
                                    <div class="nav nav-tabs mb-3">
                                        <button class="nav-link active border-white border-bottom-0" type="button" role="tab"
                                            id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                            aria-controls="nav-about" aria-selected="true">Description</button>
                                    </div>
                                </nav>
                                <div class="tab-content mb-5">
                                    <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                        {{-- Assuming product has a 'description' field --}}
                                        <p>{!! nl2br(e($product->description ?? 'No detailed description available.')) !!}</p>
                                        {{-- You can add more product attributes here if they exist --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Single Product End -->

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
                    displayFinalPrice.innerHTML = `<del class="text-danger me-2">${currencySymbol} ${displayedActualPrice.toFixed(2)}</del><span>${currencySymbol} ${displayedFinalPrice.toFixed(2)}</span>`;
                } else {
                    displayFinalPrice.innerHTML = `<span>${currencySymbol} ${displayedActualPrice.toFixed(2)}</span>`;
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
