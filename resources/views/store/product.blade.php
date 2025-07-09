@extends('layouts.frontend.app')

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
                                            <del class="text-danger me-2">{{ $setting->currency_symbol }} {{ number_format($product->actual_price, 2) }}</del>
                                            <span>{{ $setting->currency_symbol }} {{ number_format($product->final_price, 2) }}</span>
                                        @else
                                            <span>{{ $setting->currency_symbol }} {{ number_format($product->actual_price ?? 0, 2) }}</span>
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
                                    <div class="mb-4">
                                        <label for="variant_select" class="form-label fw-bold">Select Variant:</label>
                                        <select class="form-select" id="variant_select" name="variant_id" style="width: 200px;">
                                            @foreach ($product->variants as $variant)
                                                <option
                                                    value="{{ $variant->id }}"
                                                    data-price-adjustment="{{ $variant->final_price - $product->actual_price }}" {{-- Calculate price adjustment from base product actual price --}}
                                                    data-stock="{{ $variant->stock_quantity }}"
                                                    data-variant-name="{{ $variant->variant_name }}"
                                                    data-variant-img="{{ !empty($variant->product_img) ? asset('storage/' . $variant->product_img) : ( !empty($product->product_img) ? asset('storage/' . $product->product_img) : asset('build/assets/frontend/img/default-product.jpg') ) }}"
                                                    @if(!$variant->in_stock) disabled @endif {{-- Use in_stock property for variant --}}
                                                >
                                                    {{ $variant->variant_name }} (Stock: {{ number_format($variant->stock_quantity, 0) }})
                                                    @if ($variant->has_discount)
                                                        ({{ $setting->currency_symbol }}{{ number_format($variant->final_price, 2) }})
                                                    @else
                                                        ({{ $setting->currency_symbol }}{{ number_format($variant->actual_price, 2) }})
                                                    @endif
                                                </option>
                                            @endforeach
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
            const variantSelect = document.getElementById('variant_select');
            const displayFinalPrice = document.getElementById('display_final_price');
            const cartQuantityInput = document.getElementById('cart_quantity_input');
            const cartStockInput = document.getElementById('cart_stock_input');
            const cartPriceInput = document.getElementById('cart_price_input');
            const cartVariantIdInput = document.getElementById('cart_variant_id_input');
            const addToCartButton = document.getElementById('add_to_cart_button');
            const outOfStockButton = document.getElementById('out_of_stock_button');
            const totalStockDisplay = document.getElementById('total_stock_display');
            const productMainImage = document.getElementById('product_main_image');

            const baseActualPrice = parseFloat("{{ $product->actual_price ?? 0 }}");
            const baseFinalPrice = parseFloat("{{ $product->final_price ?? ($product->actual_price ?? 0) }}");
            const productHasDiscount = "{{ $product->has_discount }}" === "1";
            const currencySymbol = "{{ $setting->currency_symbol ?? '$' }}";
            const productHasVariants = "{{ $product->has_variants }}" === "1";

            let currentStock;
            let selectedVariantPriceAdjustment = 0;
            let selectedVariantStock;
            let selectedVariantHasDiscount;
            let selectedVariantFinalPrice;
            let selectedVariantActualPrice;


            // Function to update price, stock, quantity input, and button states
            function updatePriceAndStock() {
                console.log('--- updatePriceAndStock called ---');
                console.log('currentStock:', currentStock);

                let displayedActualPrice = parseFloat(selectedVariantActualPrice);
                let displayedFinalPrice = parseFloat(selectedVariantFinalPrice);
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
                if (currentStock <= 0) {
                    addToCartButton.style.display = 'none';
                    outOfStockButton.style.display = 'block';
                    productQuantityInput.value = 0; // Reset quantity if out of stock
                    productQuantityInput.disabled = true;
                    btnMinus.disabled = true;
                    btnPlus.disabled = true;
                    console.log('Product is out of stock.');
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

            // Initialize based on whether variants exist and if a default is selected
            if (productHasVariants && variantSelect.options.length > 0) {
                console.log('Product has variants. Initializing variant selection.');
                let firstAvailableVariantOption = null;
                for (let i = 0; i < variantSelect.options.length; i++) {
                    if (!variantSelect.options[i].disabled) {
                        firstAvailableVariantOption = variantSelect.options[i];
                        break;
                    }
                }

                if (firstAvailableVariantOption) {
                    variantSelect.value = firstAvailableVariantOption.value; // Set the selected value
                    const initialSelectedVariantId = firstAvailableVariantOption.value;
                    const initialSelectedVariantStock = parseInt(firstAvailableVariantOption.dataset.stock);
                    const initialSelectedVariantImg = firstAvailableVariantOption.dataset.variantImg;

                    console.log('Initial selected variant:', firstAvailableVariantOption.dataset.variantName, 'ID:', initialSelectedVariantId, 'Stock:', initialSelectedVariantStock, 'Image:', initialSelectedVariantImg);

                    cartVariantIdInput.value = initialSelectedVariantId;
                    currentStock = initialSelectedVariantStock;
                    productMainImage.src = initialSelectedVariantImg;

                    const initialVariantData = @json($product->variants->keyBy('id'));
                    const initialVariant = initialVariantData[initialSelectedVariantId];

                    selectedVariantActualPrice = parseFloat(initialVariant.actual_price);
                    selectedVariantFinalPrice = parseFloat(initialVariant.final_price);
                    selectedVariantHasDiscount = initialVariant.has_discount;

                } else {
                    console.log('No available variants found. Setting stock to 0.');
                    currentStock = 0;
                    cartVariantIdInput.value = '';
                    selectedVariantActualPrice = baseActualPrice;
                    selectedVariantFinalPrice = baseFinalPrice;
                    selectedVariantHasDiscount = productHasDiscount;
                    productMainImage.src = "{{ !empty($product->product_img) ? asset('storage/' . $product->product_img) : asset('build/assets/frontend/img/default-product.jpg') }}";
                }
            } else {
                console.log('Product does not have variants or no variants available. Using base product stock.');
                currentStock = parseInt("{{ $product->stock_quantity ?? 0 }}");
                selectedVariantActualPrice = baseActualPrice;
                selectedVariantFinalPrice = baseFinalPrice;
                selectedVariantHasDiscount = productHasDiscount;
                productMainImage.src = "{{ !empty($product->product_img) ? asset('storage/' . $product->product_img) : asset('build/assets/frontend/img/default-product.jpg') }}";
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

            // Event listener for variant selection
            if (productHasVariants && variantSelect) {
                variantSelect.addEventListener('change', function () {
                    console.log('Variant selected. Value:', this.value);
                    const selectedOption = this.options[this.selectedIndex];
                    const selectedVariantId = selectedOption.value;
                    selectedVariantStock = parseInt(selectedOption.dataset.stock);
                    const newImageSrc = selectedOption.dataset.variantImg;
                    console.log('Selected variant stock from data-stock:', selectedVariantStock);
                    
                    const variantData = @json($product->variants->keyBy('id'));
                    const selectedVariant = variantData[selectedVariantId];
                    console.log('Selected variant object from PHP data:', selectedVariant);

                    selectedVariantActualPrice = parseFloat(selectedVariant.actual_price);
                    selectedVariantFinalPrice = parseFloat(selectedVariant.final_price);
                    selectedVariantHasDiscount = selectedVariant.has_discount;

                    cartVariantIdInput.value = selectedVariantId;
                    currentStock = selectedVariantStock;
                    productMainImage.src = newImageSrc;
                    console.log('currentStock updated to:', currentStock);

                    // Reset quantity to 1 when variant changes, then update
                    productQuantityInput.value = 1; 
                    updatePriceAndStock();
                });
            }

            // Initial call to set correct price, stock, and button display on page load
            updatePriceAndStock();
        });
    }
</script>
@endsection