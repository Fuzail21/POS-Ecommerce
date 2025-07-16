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

        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Checkout</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item active text-white">Checkout</li>
            </ol>
        </div>
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div style="padding-top: 3rem; padding-bottom: 3rem;"> {{-- Replaced container-fluid py-5 --}}
            <div style="padding-top: 3rem; padding-bottom: 3rem; max-width: 1600px; margin-left: auto; margin-right: auto; padding-left: 15px; padding-right: 15px;"> {{-- Replaced container py-5 --}}
                <h1 class="mb-4">Billing details</h1>
                <form action="{{ route('store.checkout.process') }}" method="POST" id="checkout-form">
                    @csrf

                    {{-- Hidden inputs for customer, cart data, and payment details --}}
                    <input type="hidden" name="customer_id" value="{{ Auth::id() }}">
                    <input type="hidden" name="branch_id" value="{{ \App\Models\Branch::first()->id ?? 1 }}">
                    <input type="hidden" name="cart_data" id="cart_data_input">
                    <input type="hidden" name="subtotal_before_discount" value="{{ $subtotal }}"> {{-- Original subtotal --}}
                    <input type="hidden" name="coupon_code" value="{{ $couponCode }}"> {{-- Applied coupon code --}}
                    <input type="hidden" name="coupon_discount_amount" value="{{ $couponDiscount }}"> {{-- Applied coupon discount amount --}}
                    <input type="hidden" name="shipping" value="0">
                    <input type="hidden" name="total_payable" id="total_payable_input" value="{{ number_format($subtotalAfterCoupon, 2, '.', '') }}"> {{-- Use subtotalAfterCoupon --}}
                    <input type="hidden" name="amount_paid" id="amount_paid_input" value="{{ number_format($subtotalAfterCoupon, 2, '.', '') }}">
                    <input type="hidden" name="balance_due" value="0">
                    <input type="hidden" name="payment_method" id="payment_method_input" value="Cash On Delivery">

                    <div class="row g-5">
                        <div class="col-md-12 col-lg-6 col-xl-7">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">First Name<sup>*</sup></label>
                                        <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">Last Name<sup>*</sup></label>
                                        <input type="text" class="form-control" value="{{ $user->last_name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Address <sup>*</sup></label>
                                <input type="text" class="form-control" placeholder="House Number Street Name" value="{{ $user->address }}" readonly>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Town/City<sup>*</sup></label>
                                <input type="text" class="form-control" value="{{ $user->city }}" readonly>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Country<sup>*</sup></label>
                                <input type="text" class="form-control" value="{{ $user->country }}" readonly>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Postcode/Zip<sup>*</sup></label>
                                <input type="text" class="form-control" value="{{ $user->postcode }}" readonly>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Mobile<sup>*</sup></label>
                                <input type="tel" class="form-control" value="{{ $user->phone }}" readonly>
                            </div>
                            <div class="form-item mb-4">
                                <label class="form-label my-3">Email Address<sup>*</sup></label>
                                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12 col-xl-5">
                            <div class="table-responsive" style="overflow: hidden;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Products</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Color</th>
                                            <th scope="col">Size</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // $subtotal is now passed from controller, no need to recalculate here
                                            // $cart is also passed from controller
                                        @endphp

                                        @forelse($cart as $id => $details)
                                            @php
                                                $actualPrice = $details['actual_price'] ?? $details['price'];
                                                $finalPrice = $details['price'];
                                                $itemPrice = $finalPrice * $details['quantity'];
                                                $originalTotal = $actualPrice * $details['quantity'];
                                            @endphp
                                            <tr id="cart-item-row-{{ $id }}" class="cart-item-row">
                                                <th scope="row">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('storage/' . ($details['variant_img'] ?? $details['image'])) }}"
                                                                class="img-fluid me-5 rounded-circle"
                                                                style="width: 90px; height: 90px; object-fit: cover;"
                                                                alt="{{ $details['name'] }}">
                                                    </div>
                                                </th>
                                                <td style="min-width: 150px;">
                                                    <p class="mb-0 mt-4" style="font-size:15px;">
                                                        {{ Str::words($details['name'], 5, '...') }}
                                                        @if (!empty($details['variant_name']))
                                                            - {{ $details['variant_name'] }}
                                                        @endif
                                                    </p>
                                                </td>
                                                <td >
                                                    <p class="mb-0 mt-4" style="font-size:15px;">
                                                        {{ $details['variant_color'] }}
                                                        @if (empty($details['variant_color']))
                                                            - 
                                                        @endif
                                                    </p>
                                                </td>
                                                <td >
                                                    <p class="mb-0 mt-4" style="font-size:15px;">
                                                        {{ $details['variant_size'] }}
                                                        @if (empty($details['variant_size']))
                                                            - 
                                                        @endif
                                                    </p>
                                                </td>

                                                <td style="min-width: 90px;">
                                                    <div class="mb-0 mt-4 price-per-item text-start" id="price-{{ $id }}">
                                                        @if ($actualPrice != $finalPrice)
                                                            <small class="text-muted text-decoration-line-through me-1" style="font-size:13px;">{{ number_format($actualPrice, 2) }} {{ $setting->currency_symbol ?? '$' }}</small>
                                                        @endif
                                                        <span class="fw-bold text-dark" style="font-size:13px;">{{ number_format($finalPrice, 2) }} {{ $setting->currency_symbol ?? '$' }}</span>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="input-group quantity mt-4" style="min-width: 50px;">
                                                        <input type="text" class="form-control form-control-sm text-center border-0 quantity-input"
                                                                    value="{{ $details['quantity'] }}"
                                                                    id="quantity-{{ $id }}"
                                                                    data-max-stock="{{ $details['stock'] }}"
                                                                    readonly>
                                                    </div>
                                                    <div id="stock-error-{{ $id }}" class="text-danger mt-1" style="font-size: 0.85em; display: none;"></div>
                                                </td>
                                                <td>
                                                    <div class="mb-0 mt-4 item-total text-start" id="item-total-{{ $id }}">
                                                        @if ($actualPrice != $finalPrice)
                                                            <small class="text-muted text-decoration-line-through me-1" style="font-size:13px;">{{ number_format($originalTotal, 2) }} {{ $setting->currency_symbol ?? '$' }}</small>
                                                        @endif
                                                        <span class="fw-bold text-dark" style="font-size:13px;">{{ number_format($itemPrice, 2) }} {{ $setting->currency_symbol ?? '$' }}</span>
                                                    </div>
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

                            <div class="col-md-12 col-lg-12">
                                <div class="rounded"> {{-- Added p-4 for internal padding around the total summary --}}
                                    <div class="d-flex justify-content-between py-4 border-bottom"> {{-- Added py-2 and border-bottom for clear separation --}}
                                        <h5 class="mb-0 me-4">Subtotal:</h5>
                                        <p class="mb-0 fw-bold">{{ number_format($subtotal, 2) }} {{ $setting->currency_symbol ?? '$' }}</p>
                                    </div>
                                    @if($couponDiscount > 0)
                                    <div class="d-flex justify-content-between py-4 border-bottom"> {{-- Added py-2 and border-bottom --}}
                                        <h5 class="mb-0 me-4">Coupon Discount:
                                            @if($couponCode)
                                                <span class="badge bg-success ms-2">{{ $couponCode }}</span>
                                            @endif
                                        </h5>
                                        <p class="mb-0 text-danger fw-bold">-{{ number_format($couponDiscount, 2) }} {{ $setting->currency_symbol ?? '$' }}</p>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between pt-3"> {{-- Use pt-3 for top padding and no bottom border --}}
                                        <h5 class="mb-0 ps-0 me-4 text-primary">Total:</h5> {{-- Removed ps-4, added text-primary --}}
                                        <p class="mb-0 pe-0 fw-bold text-primary" id="grand-total">{{ number_format($subtotalAfterCoupon, 2) }} {{ $setting->currency_symbol ?? '$' }}</p> {{-- Ensure this uses $subtotalAfterCoupon and shipping --}}
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="radio" class="form-check-input bg-primary border-0" id="Delivery-1" name="payment_method_radio" value="Cash On Delivery" checked>
                                        <label class="form-check-label" for="Delivery-1">Cash On Delivery</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                                <button type="submit" class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">Place Order</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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