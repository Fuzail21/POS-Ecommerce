@extends('layouts.frontend.app')

@section('frontend_css')
<style>
    .cart-table { border-collapse: separate; border-spacing: 0; width: 100%; }
    .cart-table thead th {
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .7px; color: #94a3b8;
        border-bottom: 1px solid var(--clr-border);
        padding: 12px 16px; background: #f8fafc;
    }
    .cart-table tbody td {
        vertical-align: middle; padding: 16px;
        border-bottom: 1px solid #f1f5f9; font-size: .875rem;
    }
    .cart-table tbody tr:last-child td { border-bottom: none; }
    .cart-prod-img {
        width: 64px; height: 64px; border-radius: 10px;
        object-fit: cover; border: 1px solid var(--clr-border);
    }
    .cart-prod-name { font-weight: 600; font-size: .875rem; color: var(--clr-dark); margin: 0; }
    .cart-remove-btn {
        width: 32px; height: 32px; border-radius: 8px;
        background: #fff1f2; border: 1px solid #fecdd3;
        color: #ef4444; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .2s; font-size: .8rem;
    }
    .cart-remove-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
    .summary-card {
        background: #fff; border: 1px solid var(--clr-border);
        border-radius: var(--radius-card); overflow: hidden;
        position: sticky; top: 80px;
    }
    .summary-header {
        background: #f8fafc; border-bottom: 1px solid var(--clr-border);
        padding: 1rem 1.25rem; font-weight: 700; font-size: .9rem; color: var(--clr-dark);
    }
    .summary-body { padding: 1.25rem; }
    .summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: .9rem; }
    .summary-label { font-size: .875rem; color: var(--clr-muted); }
    .summary-value { font-size: .875rem; font-weight: 600; color: var(--clr-dark); }
    .summary-total-row { border-top: 2px solid var(--clr-border); margin-top: 1rem; padding-top: 1rem; }
    .summary-total-label { font-weight: 700; font-size: .95rem; color: var(--clr-dark); }
    .summary-total-value { font-weight: 800; font-size: 1.1rem; color: var(--clr-primary); }
    .coupon-section { border: 1.5px dashed var(--clr-border); border-radius: 10px; padding: 1.1rem; }
    .empty-cart-icon { font-size: 4rem; color: #cbd5e1; }
</style>
@endsection

@section('frontend_content')
@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp

<div class="page-header-band">
    <div class="container text-center">
        <h1>Shopping Cart</h1>
        <nav>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item active">Cart</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        @php
            $subtotal = 0; $totalItems = 0;
            $couponDiscount    = session('coupon_discount', 0);
            $appliedCouponCode = session('coupon_code', null);
            $shippingCost      = 0.00;
        @endphp

        @if(empty($cart))
        <div class="text-center py-5">
            <div class="empty-cart-icon mb-3"><i class="fas fa-shopping-bag"></i></div>
            <h4 style="color:#475569;">Your cart is empty</h4>
            <p style="color:#94a3b8; font-size:.875rem;">Add some products to get started.</p>
            <a href="{{ route('store.shop') }}" class="btn-prim mt-2" style="text-decoration:none; display:inline-flex;">
                <i class="fas fa-store"></i> Start Shopping
            </a>
        </div>
        @else
        <div class="row g-4 align-items-start">

            <div class="col-lg-8">
                <div style="border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden; background:#fff;">
                    <div class="table-responsive">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Details</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Stock</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cart as $id => $details)
                                @php
                                    $actualPrice   = $details['actual_price'] ?? $details['price'];
                                    $finalPrice    = $details['price'];
                                    $itemPrice     = $finalPrice * $details['quantity'];
                                    $originalTotal = $actualPrice * $details['quantity'];
                                    $subtotal     += $itemPrice;
                                    $totalItems   += $details['quantity'];
                                @endphp
                                <tr id="cart-item-row-{{ $id }}" class="cart-item-row">
                                    <td>
                                        <img src="{{ asset('storage/'.($details['variant_img'] ?? $details['image'])) }}"
                                             class="cart-prod-img" alt="{{ $details['name'] }}">
                                    </td>
                                    <td>
                                        <p class="cart-prod-name">
                                            {{ $details['name'] }}
                                            @if(!empty($details['variant_name']))
                                                <span style="font-weight:400; color:#94a3b8;"> — {{ $details['variant_name'] }}</span>
                                            @endif
                                        </p>
                                        @if(!empty($details['variant_color']) || !empty($details['variant_size']))
                                        <p style="font-size:.75rem; color:#94a3b8; margin:2px 0 0;">
                                            @if(!empty($details['variant_color'])) Color: {{ $details['variant_color'] }} @endif
                                            @if(!empty($details['variant_size'])) &nbsp;Size: {{ $details['variant_size'] }} @endif
                                        </p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div style="display:flex; justify-content:center;">
                                            <div class="qty-display" style="width:48px;">
                                                <input type="text"
                                                       class="form-control form-control-sm text-center border-0 quantity-input bg-transparent p-0"
                                                       value="{{ $details['quantity'] }}"
                                                       id="quantity-{{ $id }}"
                                                       data-max-stock="{{ $details['stock'] }}"
                                                       style="font-weight:600; font-size:.9rem;">
                                            </div>
                                        </div>
                                        <div id="stock-error-{{ $id }}" class="text-danger mt-1" style="font-size:.75rem; display:none;"></div>
                                    </td>
                                    <td class="text-end">
                                        <div class="price-per-item" id="price-{{ $id }}">
                                            @if($actualPrice != $finalPrice)
                                                <small class="text-decoration-line-through d-block" style="color:#94a3b8; font-size:.75rem;">
                                                    {{ $setting->currency_symbol ?? '' }} {{ number_format($actualPrice, 2) }}
                                                </small>
                                            @endif
                                            <span class="fw-bold" style="color:var(--clr-dark);">
                                                {{ $setting->currency_symbol ?? '' }} {{ number_format($finalPrice, 2) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="item-total" id="item-total-{{ $id }}">
                                            @if($actualPrice != $finalPrice)
                                                <small class="text-decoration-line-through d-block" style="color:#94a3b8; font-size:.75rem;">
                                                    {{ $setting->currency_symbol ?? '' }} {{ number_format($originalTotal, 2) }}
                                                </small>
                                            @endif
                                            <span class="fw-bold" style="color:var(--clr-dark);">
                                                {{ $setting->currency_symbol ?? '' }} {{ number_format($itemPrice, 2) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="stock-display" id="stock-display-{{ $id }}"
                                              style="font-size:.75rem; color:#94a3b8;">
                                            {{ number_format($details['stock'], 0) }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('cart.remove') }}" method="POST" class="remove-from-cart-form">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="product_id" value="{{ $id }}">
                                            <button type="submit" class="cart-remove-btn" title="Remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr id="empty-cart-row">
                                    <td colspan="7" class="text-center py-5" style="color:#94a3b8;">Your cart is empty!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="coupon-section mt-4">
                    <p style="font-size:.8rem; font-weight:600; color:var(--clr-muted); margin-bottom:.75rem;">
                        <i class="fas fa-tag me-1"></i> Have a coupon code?
                    </p>
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <input type="text" class="field-input" id="coupon_code_input"
                               placeholder="Enter coupon code" value="{{ $appliedCouponCode }}"
                               style="max-width:220px;">
                        <button class="btn-prim" type="button" id="apply_coupon_btn">Apply</button>
                        @if($appliedCouponCode)
                            <button class="btn-outline" type="button" id="remove_coupon_btn"
                                    style="border-color:#ef4444; color:#ef4444;">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        @endif
                    </div>
                    <div id="coupon_message" class="mt-2" style="font-size:.82rem; display:none;"></div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('store.shop') }}" class="btn-outline" style="text-decoration:none;">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <div class="summary-header">Order Summary</div>
                    <div class="summary-body">
                        <div class="summary-row">
                            <span class="summary-label">Subtotal ({{ $totalItems }} items)</span>
                            <span class="summary-value" id="cart-subtotal">
                                {{ $setting->currency_symbol ?? '' }} {{ number_format($subtotal, 2) }}
                            </span>
                        </div>

                        @php $grandTotal = $subtotal - $couponDiscount + $shippingCost; @endphp

                        <div class="summary-row" id="coupon-discount-row"
                             style="{{ $couponDiscount > 0 ? '' : 'display:none;' }}">
                            <span class="summary-label">
                                Coupon Discount
                                @if($appliedCouponCode)
                                    <span class="badge bg-success ms-1" style="font-size:.65rem;">
                                        {{ $appliedCouponCode }}
                                        <span id="remove-coupon-tag" style="cursor:pointer;">&times;</span>
                                    </span>
                                @endif
                            </span>
                            <span class="summary-value text-success" id="coupon-discount-amount">
                                −{{ $setting->currency_symbol ?? '' }} {{ number_format($couponDiscount, 2) }}
                            </span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Shipping</span>
                            <span class="summary-value" style="color:#22c55e;">Free (<span id="shipping-cost">{{ number_format($shippingCost, 2) }}</span>)</span>
                        </div>

                        <div class="summary-row summary-total-row">
                            <span class="summary-total-label">Total</span>
                            <span class="summary-total-value" id="cart-grand-total">
                                {{ $setting->currency_symbol ?? '' }} {{ number_format($grandTotal, 2) }}
                            </span>
                        </div>

                        <div class="mt-4">
                            @if(Auth::guard('customer')->check())
                                @php $isCartEmpty = empty($cart); @endphp
                                <a href="{{ route('store.checkout') }}"
                                   class="btn-prim w-100 justify-content-center"
                                   style="text-decoration:none; padding:13px; font-size:.9rem;
                                          {{ $isCartEmpty ? 'opacity:.5; pointer-events:none;' : '' }}">
                                    <i class="fas fa-lock"></i> Proceed to Checkout
                                </a>
                            @else
                                <a href="{{ route('customer.login', ['redirect' => route('store.checkout')]) }}"
                                   class="btn-prim w-100 justify-content-center"
                                   style="text-decoration:none; padding:13px; font-size:.9rem;">
                                    <i class="fas fa-sign-in-alt"></i> Login to Checkout
                                </a>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                            <i class="fas fa-shield-alt" style="color:#94a3b8; font-size:.8rem;"></i>
                            <span style="font-size:.74rem; color:#94a3b8;">Secure & encrypted checkout</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @endif
    </div>
</section>
@endsection

@section('frontend_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const currencySymbol = "{{ $setting->currency_symbol ?? '' }}";

        document.querySelectorAll('.quantity-input').forEach(function(inp) {
            inp.setAttribute('readonly', true);
        });

        let currentSubtotal          = parseFloat("{{ number_format($subtotal, 2, '.', '') }}");
        let currentCouponDiscount    = parseFloat("{{ number_format($couponDiscount, 2, '.', '') }}");
        let currentShipping          = parseFloat("{{ number_format($shippingCost, 2, '.', '') }}");
        let currentAppliedCouponCode = "{{ $appliedCouponCode }}";

        function recalculateCartTotal() {
            let sub = 0;
            document.querySelectorAll('.item-total').forEach(function(el) {
                const bold = el.querySelector('.fw-bold');
                if (bold) sub += parseFloat(bold.textContent.replace(/[^0-9.]/g, '')) || 0;
            });
            currentSubtotal = sub;
            const ship = parseFloat(document.getElementById('shipping-cost').textContent) || 0;
            currentShipping = ship;
            let afterCoupon = currentSubtotal - currentCouponDiscount;
            if (afterCoupon < 0) afterCoupon = 0;
            const grand = (afterCoupon + currentShipping).toFixed(2);

            document.getElementById('cart-subtotal').textContent    = currencySymbol + ' ' + currentSubtotal.toFixed(2);
            document.getElementById('cart-grand-total').textContent = currencySymbol + ' ' + grand;
            document.getElementById('coupon-discount-amount').textContent = '−' + currencySymbol + ' ' + currentCouponDiscount.toFixed(2);

            const couponRow = document.getElementById('coupon-discount-row');
            const badge     = document.querySelector('#coupon-discount-row .badge');
            if (currentAppliedCouponCode && currentCouponDiscount > 0) {
                couponRow.style.display = 'flex';
                if (!badge) {
                    const label = couponRow.querySelector('.summary-label');
                    const b = document.createElement('span');
                    b.className = 'badge bg-success ms-1';
                    b.style.fontSize = '.65rem';
                    b.innerHTML = currentAppliedCouponCode + ' <span id="remove-coupon-tag" style="cursor:pointer;">&times;</span>';
                    label.appendChild(b);
                } else {
                    badge.innerHTML = currentAppliedCouponCode + ' <span id="remove-coupon-tag" style="cursor:pointer;">&times;</span>';
                }
            } else {
                couponRow.style.display = 'none';
                if (badge) badge.remove();
            }

            const tag = document.getElementById('remove-coupon-tag');
            if (tag) tag.addEventListener('click', removeCoupon);

            const removeBtn = document.getElementById('remove_coupon_btn');
            if (removeBtn) removeBtn.style.display = (currentAppliedCouponCode && currentCouponDiscount > 0) ? 'inline-flex' : 'none';
        }

        recalculateCartTotal();

        document.getElementById('apply_coupon_btn').addEventListener('click', function() {
            const code = document.getElementById('coupon_code_input').value;
            const msg  = document.getElementById('coupon_message');
            msg.style.display = 'none';
            if (!code) {
                msg.textContent = 'Please enter a coupon code.';
                msg.style.color = '#ef4444'; msg.style.display = 'block'; return;
            }
            fetch("{{ route('cart.apply_coupon') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ coupon_code: code, subtotal: currentSubtotal })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    currentCouponDiscount = parseFloat(data.discount_amount);
                    currentAppliedCouponCode = code;
                    msg.style.color = '#22c55e';
                } else {
                    currentCouponDiscount = 0; currentAppliedCouponCode = null;
                    msg.style.color = '#ef4444';
                }
                recalculateCartTotal();
                msg.textContent = data.message; msg.style.display = 'block';
            })
            .catch(function() {
                msg.textContent = 'An error occurred.'; msg.style.color = '#ef4444'; msg.style.display = 'block';
            });
        });

        function removeCoupon() {
            const msg = document.getElementById('coupon_message');
            msg.style.display = 'none';
            fetch("{{ route('cart.remove_coupon') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    currentCouponDiscount = 0; currentAppliedCouponCode = null;
                    document.getElementById('coupon_code_input').value = '';
                    msg.style.color = '#22c55e';
                } else { msg.style.color = '#ef4444'; }
                recalculateCartTotal();
                msg.textContent = data.message; msg.style.display = 'block';
            })
            .catch(function() { msg.textContent = 'An error occurred.'; msg.style.color = '#ef4444'; msg.style.display = 'block'; });
        }

        const removeBtn = document.getElementById('remove_coupon_btn');
        if (removeBtn) removeBtn.addEventListener('click', removeCoupon);
        const removeTag = document.getElementById('remove-coupon-tag');
        if (removeTag) removeTag.addEventListener('click', removeCoupon);
    });
</script>
@endsection
