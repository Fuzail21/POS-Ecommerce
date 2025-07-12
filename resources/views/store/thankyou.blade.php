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
    <h1 class="text-center text-white display-6">Thank You!</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
        <li class="breadcrumb-item active text-white">Thank You</li>
    </ol>
</div>
<!-- Single Page Header End -->

<!-- Thank You Page Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4 text-primary">Your Order Has Been Placed Successfully!</h2>
                <p class="lead mb-4">Thank you for your purchase. We appreciate your business!</p>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm p-4 mb-5" style="border-radius: 15px;">
                    <h4 class="mb-3">Order Details</h4>
                    <p class="fs-5"><strong>Invoice Number:</strong> <span class="text-dark">{{ $invoiceNumber ?? 'N/A' }}</span></p>
                    <p class="fs-5"><strong>Total Amount:</strong> <span class="text-dark">{{ number_format($totalAmount ?? 0, 2) }} {{ $setting->currency_symbol ?? '$' }}</span></p>
                    <p class="text-muted">You will receive an email confirmation shortly with your order details.</p>
                </div>

                <a href="{{ route('store.landing') }}" class="btn border-secondary py-3 px-4 text-uppercase text-primary me-2" style="border-radius: 20px;">
                    Continue Shopping
                </a>
                {{-- You might add a link to order history here if available --}}
                {{-- <a href="{{ route('customer.orders') }}" class="btn border-primary py-3 px-4 text-uppercase text-white bg-primary" style="border-radius: 20px;">
                    View My Orders
                </a> --}}
            </div>
        </div>
    </div>
</div>
<!-- Thank You Page End -->

@endsection

@section('frontend_js')
    {{-- Add any specific JS for this page if needed --}}
@endsection