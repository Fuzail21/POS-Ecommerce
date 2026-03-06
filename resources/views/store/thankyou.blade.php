@extends('layouts.frontend.app')

@section('frontend_content')

@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp

<div class="page-header-band">
    <div class="container text-center">
        <h1>Thank You!</h1>
        <nav>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item active">Thank You</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 text-center">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Success Icon --}}
                <div style="width:90px; height:90px; background:linear-gradient(135deg,#22c55e,#16a34a); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; box-shadow:0 8px 24px rgba(34,197,94,.3);">
                    <i class="fas fa-check" style="font-size:2.5rem; color:#fff;"></i>
                </div>

                <h2 style="font-weight:800; font-size:1.75rem; color:var(--clr-dark); margin-bottom:.5rem;">Order Placed Successfully!</h2>
                <p style="color:var(--clr-muted); font-size:.95rem; margin-bottom:2rem; line-height:1.7;">
                    Thank you for your purchase. We appreciate your business and will process your order shortly.
                </p>

                {{-- Order Details Card --}}
                <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); padding:1.75rem; margin-bottom:2rem; text-align:left;">
                    <h6 style="font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); margin-bottom:1rem;">
                        <i class="fas fa-receipt me-1"></i> Order Summary
                    </h6>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:.65rem 0; border-bottom:1px solid #f1f5f9;">
                        <span style="font-size:.875rem; color:var(--clr-muted);">Invoice Number</span>
                        <span style="font-size:.875rem; font-weight:700; color:var(--clr-dark);">{{ $invoiceNumber ?? 'N/A' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:.65rem 0;">
                        <span style="font-size:.875rem; color:var(--clr-muted);">Total Amount</span>
                        <span style="font-size:1.05rem; font-weight:800; color:var(--clr-primary);">
                            {{ $setting->currency_symbol ?? '' }} {{ number_format($totalAmount ?? 0, 2) }}
                        </span>
                    </div>
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:.65rem 1rem; margin-top:.75rem; display:flex; align-items:center; gap:.5rem;">
                        <i class="fas fa-envelope" style="color:#22c55e; font-size:.85rem;"></i>
                        <span style="font-size:.8rem; color:#166534;">A confirmation email has been sent with your order details.</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('store.orders') }}" class="btn-prim" style="text-decoration:none; padding:13px 28px; font-size:.9rem;">
                        <i class="fas fa-box me-1"></i> View My Orders
                    </a>
                    <a href="{{ route('store.landing') }}" class="btn-outline" style="padding:13px 28px; font-size:.9rem;">
                        <i class="fas fa-shopping-bag me-1"></i> Continue Shopping
                    </a>
                </div>

                {{-- Trust Badges --}}
                <div style="display:flex; gap:1.5rem; justify-content:center; flex-wrap:wrap; margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--clr-border);">
                    <div style="font-size:.78rem; color:#94a3b8; display:flex; align-items:center; gap:.4rem;">
                        <i class="fas fa-shield-alt" style="color:#22c55e;"></i> Secure Payment
                    </div>
                    <div style="font-size:.78rem; color:#94a3b8; display:flex; align-items:center; gap:.4rem;">
                        <i class="fas fa-truck" style="color:var(--clr-primary);"></i> Fast Delivery
                    </div>
                    <div style="font-size:.78rem; color:#94a3b8; display:flex; align-items:center; gap:.4rem;">
                        <i class="fas fa-headset" style="color:#f59e0b;"></i> 24/7 Support
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection

@section('frontend_js')
@endsection
