@extends('layouts.frontend.app')

@section('frontend_content')

@php
    use App\Models\Setting;
    use Illuminate\Support\Facades\Auth;
    $setting = Setting::first();
    $primaryColor = $setting->primary_color ?? '#0d6efd';
    $secondaryColor = $setting->secondary_color ?? '#6c757d';
    // $user is already passed from the controller
@endphp

<div class="page-header-band">
    <div class="container text-center">
        <h1>Checkout</h1>
        <nav>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

@if(session('status'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif
@if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

<section class="py-5">
    <div class="container">
        <form action="{{ route('store.checkout.process') }}" method="POST" id="checkout-form">
            @csrf
            <input type="hidden" name="customer_id" value="{{ Auth::id() }}">
            <input type="hidden" name="branch_id" value="{{ \App\Models\Branch::first()->id ?? 1 }}">
            <input type="hidden" name="cart_data" id="cart_data_input">
            <input type="hidden" name="subtotal_before_discount" value="{{ $subtotal }}">
            <input type="hidden" name="coupon_code" value="{{ $couponCode }}">
            <input type="hidden" name="coupon_discount_amount" value="{{ $couponDiscount }}">
            <input type="hidden" name="shipping" value="0">
            <input type="hidden" name="total_payable" id="total_payable_input" value="{{ number_format($subtotalAfterCoupon, 2, '.', '') }}">
            <input type="hidden" name="amount_paid" id="amount_paid_input" value="{{ number_format($subtotalAfterCoupon, 2, '.', '') }}">
            <input type="hidden" name="balance_due" value="0">
            <input type="hidden" name="payment_method" id="payment_method_input" value="cash">

            <div class="row g-4">
                {{-- Billing Details --}}
                <div class="col-lg-6">
                    <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); padding:1.75rem;">
                        <h5 style="font-weight:700; margin-bottom:1.5rem; font-size:1rem; color:var(--clr-dark);">
                            <i class="fas fa-user-circle me-2" style="color:var(--clr-primary);"></i> Billing Details
                        </h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label" style="font-size:.78rem; font-weight:600; color:var(--clr-muted); text-transform:uppercase; letter-spacing:.5px;">First Name</label>
                                <input type="text" class="form-control field-input" value="{{ $user->name }}" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:.78rem; font-weight:600; color:var(--clr-muted); text-transform:uppercase; letter-spacing:.5px;">Last Name</label>
                                <input type="text" class="form-control field-input" value="{{ $user->last_name }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:.78rem; font-weight:600; color:var(--clr-muted); text-transform:uppercase; letter-spacing:.5px;">Address</label>
                                <input type="text" class="form-control field-input" value="{{ $user->address }}" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:.78rem; font-weight:600; color:var(--clr-muted); text-transform:uppercase; letter-spacing:.5px;">City</label>
                                <input type="text" class="form-control field-input" value="{{ $user->city }}" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:.78rem; font-weight:600; color:var(--clr-muted); text-transform:uppercase; letter-spacing:.5px;">Country</label>
                                <input type="text" class="form-control field-input" value="{{ $user->country }}" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:.78rem; font-weight:600; color:var(--clr-muted); text-transform:uppercase; letter-spacing:.5px;">Postcode</label>
                                <input type="text" class="form-control field-input" value="{{ $user->postcode }}" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:.78rem; font-weight:600; color:var(--clr-muted); text-transform:uppercase; letter-spacing:.5px;">Phone</label>
                                <input type="tel" class="form-control field-input" value="{{ $user->phone }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:.78rem; font-weight:600; color:var(--clr-muted); text-transform:uppercase; letter-spacing:.5px;">Email</label>
                                <input type="email" class="form-control field-input" value="{{ $user->email }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Summary & Payment --}}
                <div class="col-lg-6">
                    <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden;">
                        {{-- Items --}}
                        <div style="background:#f8fafc; border-bottom:1px solid var(--clr-border); padding:1rem 1.25rem;">
                            <h6 style="font-weight:700; font-size:.9rem; margin:0; color:var(--clr-dark);">
                                <i class="fas fa-shopping-bag me-2" style="color:var(--clr-primary);"></i> Your Items
                            </h6>
                        </div>
                        <div style="padding:1.25rem; max-height:320px; overflow-y:auto;">
                            @forelse($cart as $id => $details)
                            @php
                                $actualPrice   = $details['actual_price'] ?? $details['price'];
                                $finalPrice    = $details['price'];
                                $itemPrice     = $finalPrice * $details['quantity'];
                                $originalTotal = $actualPrice * $details['quantity'];
                            @endphp
                            <tr id="cart-item-row-{{ $id }}" class="cart-item-row" style="display:none;"></tr>
                            <div style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f1f5f9;">
                                <img src="{{ asset('storage/'.($details['variant_img'] ?? $details['image'])) }}"
                                     style="width:50px; height:50px; border-radius:8px; object-fit:cover; border:1px solid var(--clr-border);"
                                     alt="{{ $details['name'] }}">
                                <div style="flex:1;">
                                    <div style="font-size:.84rem; font-weight:600; color:var(--clr-dark);">
                                        {{ Str::words($details['name'], 5, '...') }}
                                        @if(!empty($details['variant_name'])) — {{ $details['variant_name'] }} @endif
                                    </div>
                                    <div style="font-size:.75rem; color:#94a3b8;">Qty: {{ $details['quantity'] }}</div>
                                </div>
                                <div style="text-align:right;">
                                    <div class="item-total" id="item-total-{{ $id }}" style="font-size:.875rem;">
                                        @if($actualPrice != $finalPrice)
                                            <small style="text-decoration:line-through; color:#94a3b8; font-size:.72rem; display:block;">
                                                {{ $setting->currency_symbol ?? '' }} {{ number_format($originalTotal, 2) }}
                                            </small>
                                        @endif
                                        <span class="fw-bold" style="color:var(--clr-dark);">
                                            {{ $setting->currency_symbol ?? '' }} {{ number_format($itemPrice, 2) }}
                                        </span>
                                    </div>
                                    {{-- Hidden but required by JS --}}
                                    <input type="hidden" class="quantity-input" id="quantity-{{ $id }}" value="{{ $details['quantity'] }}" data-max-stock="{{ $details['stock'] }}">
                                    <div id="stock-error-{{ $id }}" style="display:none;"></div>
                                    <div id="price-{{ $id }}" style="display:none;">
                                        <span class="fw-bold">{{ number_format($finalPrice, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p style="color:#94a3b8; font-size:.875rem; text-align:center; padding:1rem;">Cart is empty.</p>
                            @endforelse
                        </div>

                        {{-- Totals --}}
                        <div style="padding:1.25rem; border-top:1px solid var(--clr-border); background:#fafafa;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:.6rem;">
                                <span style="font-size:.875rem; color:var(--clr-muted);">Subtotal</span>
                                <span style="font-size:.875rem; font-weight:600;">{{ $setting->currency_symbol ?? '' }} {{ number_format($subtotal, 2) }}</span>
                            </div>
                            @if($couponDiscount > 0)
                            <div style="display:flex; justify-content:space-between; margin-bottom:.6rem;">
                                <span style="font-size:.875rem; color:var(--clr-muted);">
                                    Coupon Discount @if($couponCode) <span class="badge bg-success ms-1" style="font-size:.65rem;">{{ $couponCode }}</span> @endif
                                </span>
                                <span style="font-size:.875rem; font-weight:600; color:#22c55e;">−{{ $setting->currency_symbol ?? '' }} {{ number_format($couponDiscount, 2) }}</span>
                            </div>
                            @endif
                            <div style="display:flex; justify-content:space-between; padding-top:.75rem; border-top:2px solid var(--clr-border); margin-top:.5rem;">
                                <span style="font-weight:700; font-size:.95rem; color:var(--clr-dark);">Total</span>
                                <span id="grand-total" style="font-weight:800; font-size:1.05rem; color:var(--clr-primary);">
                                    {{ $setting->currency_symbol ?? '' }} {{ number_format($subtotalAfterCoupon, 2) }}
                                </span>
                            </div>
                        </div>

                        {{-- Payment --}}
                        <div style="padding:1.25rem; border-top:1px solid var(--clr-border);">
                            <p style="font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); margin-bottom:.75rem;">Payment Method</p>
                            <div style="background:var(--clr-light); border:1.5px solid var(--clr-border); border-radius:10px; padding:12px 16px; display:flex; align-items:center; gap:10px;">
                                <input type="radio" class="form-check-input" id="Delivery-1" name="payment_method_radio" value="cash" checked style="margin:0;">
                                <label for="Delivery-1" style="font-size:.875rem; font-weight:600; color:var(--clr-dark); cursor:pointer; margin:0;">
                                    <i class="fas fa-money-bill-wave me-2" style="color:#22c55e;"></i> Cash On Delivery
                                </label>
                            </div>
                        </div>

                        {{-- Place Order --}}
                        <div style="padding:1.25rem; border-top:1px solid var(--clr-border);">
                            <button type="submit" class="btn-prim w-100 justify-content-center" style="padding:14px; font-size:.95rem;">
                                <i class="fas fa-check-circle"></i> Place Order
                            </button>
                            <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                                <i class="fas fa-shield-alt" style="color:#94a3b8; font-size:.8rem;"></i>
                                <span style="font-size:.74rem; color:#94a3b8;">Secure & encrypted payment</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@section('frontend_js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutForm = document.getElementById('checkout-form');
        const cartDataInput = document.getElementById('cart_data_input');
        const totalPayableInput = document.getElementById('total_payable_input');
        const amountPaidInput = document.getElementById('amount_paid_input');
        const paymentMethodInputs = document.querySelectorAll('input[name="payment_method_radio"]');
        const paymentMethodHiddenInput = document.getElementById('payment_method_input');

        const cart = @json($cart ?? []); // Assuming $cart is passed to the view

        cartDataInput.value = JSON.stringify(cart);

        // Use the subtotalAfterCoupon passed from the controller
        const subtotalFinal = parseFloat("{{ number_format($subtotalAfterCoupon ?? 0, 2, '.', '') }}");

        totalPayableInput.value = subtotalFinal;
        amountPaidInput.value = subtotalFinal;

        paymentMethodInputs.forEach(radio => {
            radio.addEventListener('change', function() {
                paymentMethodHiddenInput.value = this.value;
            });
        });
    });
</script>
@endsection