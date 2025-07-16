@extends('layouts.frontend.app')

@section('frontend_content')

@php
    use App\Models\Setting;
    $setting = Setting::first();
    $primaryColor = $setting->primary_color ?? '#0d6efd';
    $secondaryColor = $setting->secondary_color ?? '#6c757d';
@endphp

<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Cart</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
        <li class="breadcrumb-item active text-white">Cart</li>
    </ol>
</div>

<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Products</th>
                        <th scope="col">Name</th>
                        <th scope="col">Color</th>
                        <th scope="col">Size</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Total</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subtotal = 0;
                        $totalItems = 0;
                        $couponDiscount = session('coupon_discount', 0); // Get applied coupon discount from session
                        $appliedCouponCode = session('coupon_code', null); // Get applied coupon code from session
                    @endphp

                    @forelse($cart as $id => $details)
                        @php
                            $actualPrice = $details['actual_price'] ?? $details['price'];
                            $finalPrice = $details['price'];
                            $itemPrice = $finalPrice * $details['quantity'];
                            $originalTotal = $actualPrice * $details['quantity'];
                            $subtotal += $itemPrice;
                            $totalItems += $details['quantity'];
                        @endphp
                        <tr id="cart-item-row-{{ $id }}" class="cart-item-row">
                            <th scope="row">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($details['variant_img'] ?? $details['image'])) }}"
                                         class="img-fluid me-5 rounded-circle"
                                         style="width: 80px; height: 80px; object-fit: cover;"
                                         alt="{{ $details['name'] }}">
                                </div>
                            </th>
                            <td>
                                <p class="mb-0 mt-4">
                                    {{ $details['name'] }}
                                    @if (!empty($details['variant_name']))
                                        - {{ $details['variant_name'] }}
                                    @endif
                                </p>
                            </td>
                            <td>
                                <p class="mb-0 mt-4">
                                    {{ $details['variant_color'] }}
                                    @if (empty($details['variant_color']))
                                        - 
                                    @endif
                                </p>
                            </td>
                            <td>
                                <p class="mb-0 mt-4">
                                    {{ $details['variant_size'] }}
                                    @if (empty($details['variant_size']))
                                        - 
                                    @endif
                                </p>
                            </td>
                            <td>
                                <div class="mb-0 mt-4 price-per-item text-start" id="price-{{ $id }}">
                                    @if ($actualPrice != $finalPrice)
                                        <small class="text-muted text-decoration-line-through me-1">{{ number_format($actualPrice, 2) }} {{ $setting->currency_symbol ?? '$' }}</small>
                                    @endif
                                    <span class="fw-bold text-dark">{{ number_format($finalPrice, 2) }} {{ $setting->currency_symbol ?? '$' }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="input-group quantity mt-4" style="width: 100px;">
                                    <input type="text" class="form-control form-control-sm text-center border-0 quantity-input"
                                           value="{{ $details['quantity'] }}"
                                           id="quantity-{{ $id }}"
                                           data-max-stock="{{ $details['stock'] }}">
                                </div>
                                <div id="stock-error-{{ $id }}" class="text-danger mt-1" style="font-size: 0.85em; display: none;"></div>
                            </td>
                            <td><p class="mb-0 mt-4 stock-display" id="stock-display-{{ $id }}">{{ number_format($details['stock'], 0) }}</p></td>
                            <td>
                                <div class="mb-0 mt-4 item-total text-start" id="item-total-{{ $id }}">
                                    @if ($actualPrice != $finalPrice)
                                        <small class="text-muted text-decoration-line-through me-1">{{ number_format($originalTotal, 2) }} {{ $setting->currency_symbol ?? '$' }}</small>
                                    @endif
                                    <span class="fw-bold text-dark">{{ number_format($itemPrice, 2) }} {{ $setting->currency_symbol ?? '$' }}</span>
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('cart.remove') }}" method="POST" class="remove-from-cart-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                    <button type="submit" class="btn btn-md rounded-circle bg-light border mt-4">
                                        <i class="fa fa-times text-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-cart-row">
                            <td colspan="7" class="text-center py-5">Your cart is empty!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5 d-flex align-items-center"> {{-- Added d-flex and align-items-center for better alignment --}}
            <input type="text" class="border-0 border-bottom rounded me-3 py-3 mb-4" id="coupon_code_input" placeholder="Coupon Code" value="{{ $appliedCouponCode }}">
            <button class="btn border-secondary rounded-pill px-4 py-3 text-primary" type="button" id="apply_coupon_btn">Apply Coupon</button>
            @if($appliedCouponCode)
                <button class="btn btn-danger rounded-pill px-3 py-2 ms-2 mb-4" type="button" id="remove_coupon_btn">Remove Coupon</button>
            @endif
        </div>
        <div id="coupon_message" class="text-danger mb-3" style="display:none;"></div>

        <div class="row g-4 justify-content-end">
            <div class="col-8"></div>
            <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                <div class="bg-light rounded">
                    <div class="p-4">
                        <h1 class="display-6 mb-4">Cart <span class="fw-normal">Total</span></h1>
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="mb-0 me-4">Subtotal:</h5>
                            <p class="mb-0" id="cart-subtotal">{{ number_format($subtotal, 2) }} {{ $setting->currency_symbol ?? '$' }}</p>
                        </div>

                        {{-- Coupon Discount Row --}}
                        <div class="d-flex justify-content-between mb-4" id="coupon-discount-row" style="{{ $couponDiscount > 0 ? '' : 'display:none;' }}">
                            <h5 class="mb-0 me-4">Coupon Discount:
                                @if($appliedCouponCode)
                                    <span class="badge bg-success ms-2">{{ $appliedCouponCode }} <span id="remove-coupon-tag" style="cursor:pointer;">&times;</span></span>
                                @endif
                            </h5>
                            <p class="mb-0 text-danger" id="coupon-discount-amount">-{{ number_format($couponDiscount, 2) }} {{ $setting->currency_symbol ?? '$' }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0 me-4">Shipping</h5>
                            @php $shippingCost = 0.00; @endphp
                            <p class="mb-0">Flat rate: <span id="shipping-cost">{{ number_format($shippingCost, 2) }}</span> {{ $setting->currency_symbol ?? '$' }}</p>
                        </div>
                        <p class="mb-0 text-end">Shipping to your address.</p>
                    </div>
                    @php $grandTotal = $subtotal - $couponDiscount + $shippingCost; @endphp
                    <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                        <h5 class="mb-0 ps-4 me-4">Total</h5>
                        <p class="mb-0 pe-4" id="cart-grand-total">{{ number_format($grandTotal, 2) }} {{ $setting->currency_symbol ?? '$' }}</p>
                    </div>
                    @if(Auth::guard('customer')->check())
                        @php
                            // Ensure $cart is defined and is an array/countable.
                            // Assuming $cart is passed to this view from your controller (e.g., CheckoutController@index)
                            $isCartEmpty = empty($cart);
                        @endphp

                        <a href="{{ route('store.checkout') }}"
                           class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4 {{ $isCartEmpty ? 'disabled' : '' }}"
                           type="button"
                           @if($isCartEmpty)
                               aria-disabled="true" {{-- For accessibility --}}
                               onclick="return false;" {{-- Prevent navigation for disabled link --}}
                           @endif
                        >
                            Proceed Checkout
                        </a>
                    @else
                        <a href="{{ route('customer.login', ['redirect' => route('store.checkout')]) }}" class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4" type="button">Login to Checkout</a>
                        {{-- You can also add a registration link here if desired --}}
                        {{-- <a href="{{ route('customer.register', ['redirect' => route('store.checkout')]) }}" class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4" type="button">Register and Checkout</a> --}}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('frontend_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const currencySymbol = "{{ $setting->currency_symbol ?? '$' }}";

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.setAttribute('readonly', true);
        });

        // Initial values from PHP (for page load)
        let currentSubtotal = parseFloat("{{ number_format($subtotal, 2, '.', '') }}");
        let currentCouponDiscount = parseFloat("{{ number_format($couponDiscount, 2, '.', '') }}");
        let currentShipping = parseFloat("{{ number_format($shippingCost, 2, '.', '') }}");
        let currentAppliedCouponCode = "{{ $appliedCouponCode }}";


        function recalculateCartTotal() {
            let subtotalFromItems = 0;
            document.querySelectorAll('.item-total').forEach(el => {
                const priceText = el.querySelector('.fw-bold')?.textContent || '0';
                subtotalFromItems += parseFloat(priceText.replace(/[^0-9.]/g, '')) || 0;
            });
            currentSubtotal = subtotalFromItems; // Update the global subtotal based on item totals

            const shipping = parseFloat(document.getElementById('shipping-cost').textContent) || 0;
            currentShipping = shipping; // Update current shipping

            let finalSubtotalAfterCoupon = currentSubtotal - currentCouponDiscount;
            if (finalSubtotalAfterCoupon < 0) finalSubtotalAfterCoupon = 0; // Prevent negative subtotal

            const grandTotal = (finalSubtotalAfterCoupon + currentShipping).toFixed(2);

            document.getElementById('cart-subtotal').textContent = `${currentSubtotal.toFixed(2)} ${currencySymbol}`;
            document.getElementById('cart-grand-total').textContent = `${grandTotal} ${currencySymbol}`;
            document.getElementById('coupon-discount-amount').textContent = `-${currentCouponDiscount.toFixed(2)} ${currencySymbol}`;

            // Update coupon tag display
            const couponTagElement = document.querySelector('#coupon-discount-row h5 .badge');
            if (currentAppliedCouponCode && currentCouponDiscount > 0) {
                document.getElementById('coupon-discount-row').style.display = 'flex';
                if (!couponTagElement) {
                    const h5 = document.querySelector('#coupon-discount-row h5');
                    const newBadge = document.createElement('span');
                    newBadge.className = 'badge bg-success ms-2';
                    newBadge.innerHTML = `${currentAppliedCouponCode} <span id="remove-coupon-tag" style="cursor:pointer;">&times;</span>`;
                    h5.appendChild(newBadge);
                } else {
                    couponTagElement.innerHTML = `${currentAppliedCouponCode} <span id="remove-coupon-tag" style="cursor:pointer;">&times;</span>`;
                }
            } else {
                document.getElementById('coupon-discount-row').style.display = 'none';
                if (couponTagElement) {
                    couponTagElement.remove();
                }
            }

            // Re-attach event listener for remove tag if it was just added
            if (document.getElementById('remove-coupon-tag')) {
                document.getElementById('remove-coupon-tag').addEventListener('click', removeCoupon);
            }

            // Toggle remove coupon button visibility
            const removeCouponBtn = document.getElementById('remove_coupon_btn');
            if (removeCouponBtn) {
                if (currentAppliedCouponCode && currentCouponDiscount > 0) {
                    removeCouponBtn.style.display = 'inline-block';
                } else {
                    removeCouponBtn.style.display = 'none';
                }
            }
        }

        recalculateCartTotal(); // Call initially on page load

        // Coupon Application Logic
        document.getElementById('apply_coupon_btn').addEventListener('click', function() {
            const couponCode = document.getElementById('coupon_code_input').value;
            const couponMessage = document.getElementById('coupon_message');
            couponMessage.style.display = 'none'; // Hide previous messages

            if (!couponCode) {
                couponMessage.textContent = "Please enter a coupon code.";
                couponMessage.style.display = 'block';
                return;
            }

            fetch("{{ route('cart.apply_coupon') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ coupon_code: couponCode, subtotal: currentSubtotal })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentCouponDiscount = parseFloat(data.discount_amount);
                    currentAppliedCouponCode = couponCode;
                    recalculateCartTotal();
                    couponMessage.textContent = data.message;
                    couponMessage.className = 'text-success mb-3'; // Change to success style
                    couponMessage.style.display = 'block';
                } else {
                    currentCouponDiscount = 0; // Reset discount on failure
                    currentAppliedCouponCode = null; // Clear applied code on failure
                    recalculateCartTotal();
                    couponMessage.textContent = data.message;
                    couponMessage.className = 'text-danger mb-3'; // Error style
                    couponMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error applying coupon:', error);
                couponMessage.textContent = "An error occurred while applying the coupon.";
                couponMessage.className = 'text-danger mb-3';
                couponMessage.style.display = 'block';
                currentCouponDiscount = 0; // Reset discount on error
                currentAppliedCouponCode = null;
                recalculateCartTotal();
            });
        });

        // Coupon Removal Logic
        function removeCoupon() {
            const couponMessage = document.getElementById('coupon_message');
            couponMessage.style.display = 'none'; // Hide previous messages

            fetch("{{ route('cart.remove_coupon') }}", {
                method: 'POST', // Use POST for removal as well for CSRF token
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({}) // No specific data needed for removal
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentCouponDiscount = 0;
                    currentAppliedCouponCode = null;
                    document.getElementById('coupon_code_input').value = ''; // Clear input field
                    recalculateCartTotal();
                    couponMessage.textContent = data.message;
                    couponMessage.className = 'text-success mb-3';
                    couponMessage.style.display = 'block';
                } else {
                    couponMessage.textContent = data.message;
                    couponMessage.className = 'text-danger mb-3';
                    couponMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error removing coupon:', error);
                couponMessage.textContent = "An error occurred while removing the coupon.";
                couponMessage.className = 'text-danger mb-3';
                couponMessage.style.display = 'block';
            });
        }

        // Attach event listener to the remove button next to the input field
        const removeCouponBtn = document.getElementById('remove_coupon_btn');
        if (removeCouponBtn) {
            removeCouponBtn.addEventListener('click', removeCoupon);
        }

        // Attach event listener to the remove tag in the summary (if it exists on initial load)
        if (document.getElementById('remove-coupon-tag')) {
            document.getElementById('remove-coupon-tag').addEventListener('click', removeCoupon);
        }

    });
</script>
@endsection