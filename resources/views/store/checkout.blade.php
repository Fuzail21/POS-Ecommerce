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
            <h1 class="text-center text-white display-6">Checkout</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item active text-white">Checkout</li>
            </ol>
        </div>
        <!-- Single Page Header End -->


        <!-- Checkout Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <h1 class="mb-4">Billing details</h1>
                <form action="#">
                    <div class="row g-5">
                        <div class="col-md-12 col-lg-6 col-xl-7">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">First Name<sup>*</sup></label>
                                        <input type="text" class="form-control" value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">Last Name<sup>*</sup></label>
                                        <input type="text" class="form-control" value="{{ $user->last_name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Address <sup>*</sup></label>
                                <input type="text" class="form-control" placeholder="House Number Street Name" value="{{ $user->address }}">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Town/City<sup>*</sup></label>
                                <input type="text" class="form-control" value="{{ $user->city }}">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Country<sup>*</sup></label>
                                <input type="text" class="form-control" value="{{ $user->country }}">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Postcode/Zip<sup>*</sup></label>
                                <input type="text" class="form-control" value="{{ $user->postcode }}">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Mobile<sup>*</sup></label>
                                <input type="tel" class="form-control" value="{{ $user->phone }}">
                            </div>
                            <div class="form-item mb-4">
                                <label class="form-label my-3">Email Address<sup>*</sup></label>
                                <input type="email" class="form-control" value="{{ $user->email }}">
                            </div>
                            {{-- <div class="form-check my-3">
                                <input type="checkbox" class="form-check-input" id="Account-1" name="Accounts" value="Accounts">
                                <label class="form-check-label" for="Account-1">Create an account?</label>
                            </div>
                            <hr>
                            <div class="form-check my-3">
                                <input class="form-check-input" type="checkbox" id="Address-1" name="Address" value="Address">
                                <label class="form-check-label" for="Address-1">Ship to a different address?</label>
                            </div> --}}
                            <div class="form-item">
                                <textarea name="text" class="form-control" spellcheck="false" cols="30" rows="11" placeholder="Oreder Notes (Optional)"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-5">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Products</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // These variables will calculate the subtotal and total items for the checkout summary
                                            $subtotal = 0;
                                            $totalItems = 0;
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
                                            <tr>
                                                <th scope="row">
                                                    <div class="d-flex align-items-center mt-2">
                                                        <img src="{{ asset('storage/' . $details['image']) }}" class="img-fluid rounded-circle" style="width: 70px; height: 70px; object-fit: cover;" alt="{{ $details['name'] }}">
                                                    </div>
                                                </th>
                                                <td class="py-5" >{{ Str::words($details['name'], 5, '...') }}</td>
                                                <td class="py-5" style="font-size: 15px; min-width: 100px;">
                                                    @if ($actualPrice != $finalPrice)
                                                        <small class="text-muted text-decoration-line-through me-1">{{ number_format($actualPrice, 2) }} {{ $setting->currency_symbol ?? '$' }}</small>
                                                    @endif
                                                    <span class="fw-bold text-dark">{{ number_format($finalPrice, 2) }} {{ $setting->currency_symbol ?? '$' }}</span>
                                                </td>
                                                <td class="py-5">{{ $details['quantity'] }}</td> {{-- Display quantity, not an editable input --}}
                                                <td class="py-5" style="font-size: 15px; min-width: 100px;">
                                                    @if ($actualPrice != $finalPrice)
                                                        <small class="text-muted text-decoration-line-through me-1">{{ number_format($originalTotal, 2) }} {{ $setting->currency_symbol ?? '$' }}</small>
                                                    @endif
                                                    <span class="fw-bold text-dark">{{ number_format($itemPrice, 2) }} {{ $setting->currency_symbol ?? '$' }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">Your cart is empty!</td>
                                            </tr>
                                        @endforelse

                                        {{-- Subtotal Row --}}
                                        <tr>
                                            <th scope="row"></th>
                                            <td class="py-5"></td>
                                            <td class="py-5"></td>
                                            <td class="py-5">
                                                <p class="mb-0 text-dark py-3">Subtotal</p>
                                            </td>
                                            <td class="py-5">
                                                <div class="py-3 border-bottom border-top">
                                                    <p class="mb-0 text-dark" style="min-width: 100px;">{{ number_format($subtotal, 2) }} {{ $setting->currency_symbol ?? '$' }}</p>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- Shipping Options (as provided in your example, without dynamic calculation here) --}}
                                        <tr>
                                            <th scope="row"></th> {{-- Empty header for alignment --}}
                                            <td class="py-5">
                                                <p class="mb-0 text-dark ">Shipping</p>
                                            </td>
                                            <td colspan="3" class="py-5"> {{-- colspans 3 columns --}}
                                                <div class="form-check text-start">
                                                    <input type="radio" class="form-check-input bg-primary border-0" id="Shipping-1" name="shipping_option" value="free_shipping" checked>
                                                    <label class="form-check-label" for="Shipping-1">Free Shipping</label>
                                                </div>
                                                {{-- The two options below have been removed --}}
                                            </td>
                                        </tr>
                                        {{-- Total Row (Note: this total does not dynamically include shipping fee calculation in this snippet) --}}
                                        <tr>
                                            <th scope="row"></th>
                                            <td class="py-5">
                                                <p class="mb-0 text-dark text-uppercase py-3">TOTAL</p>
                                            </td>
                                            <td class="py-5"></td>
                                            <td class="py-5"></td>
                                            <td class="py-5">
                                                <div class="py-3 border-bottom border-top">
                                                    {{-- You'll need JavaScript or a more complex Blade logic to update this based on selected shipping --}}
                                                    <p class="mb-0 text-dark" id="grand-total" style="min-width: 100px;">{{ number_format($subtotal, 2) }} {{ $setting->currency_symbol ?? '$' }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="checkbox" class="form-check-input bg-primary border-0" id="Transfer-1" name="Transfer" value="Transfer">
                                        <label class="form-check-label" for="Transfer-1">Direct Bank Transfer</label>
                                    </div>
                                    <p class="text-start text-dark">Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.</p>
                                </div>
                            </div> --}}
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="checkbox" class="form-check-input bg-primary border-0" id="Delivery-1" name="Delivery" value="Delivery">
                                        <label class="form-check-label" for="Delivery-1">Cash On Delivery</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                                <button type="button" class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">Place Order</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Checkout Page End -->


@section('frontend_js')

@endsection
