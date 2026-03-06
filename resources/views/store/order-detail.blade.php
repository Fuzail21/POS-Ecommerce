@extends('layouts.frontend.app')

@section('frontend_content')

@php
    $currencySymbol = $setting->currency_symbol ?? '$';
    $statusMap = [
        'pending'    => ['bg' => '#fef9c3', 'color' => '#854d0e', 'icon' => 'fa-clock'],
        'confirmed'  => ['bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => 'fa-check-circle'],
        'processing' => ['bg' => '#e0f2fe', 'color' => '#0c4a6e', 'icon' => 'fa-cog'],
        'shipped'    => ['bg' => '#ede9fe', 'color' => '#5b21b6', 'icon' => 'fa-truck'],
        'completed'  => ['bg' => '#dcfce7', 'color' => '#166534', 'icon' => 'fa-check-double'],
        'cancelled'  => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'fa-times-circle'],
    ];
    $sm = $statusMap[$order->status] ?? ['bg' => '#f1f5f9', 'color' => '#64748b', 'icon' => 'fa-circle'];
    $steps = ['pending', 'confirmed', 'processing', 'shipped', 'completed'];
    $currentIndex = array_search($order->status, $steps);
@endphp

<div class="page-header-band">
    <div class="container text-center">
        <h1>Order Details</h1>
        <nav>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('store.orders') }}">My Orders</a></li>
                <li class="breadcrumb-item active">#{{ $order->invoice_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">

        {{-- Top bar --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h5 style="font-weight:800; color:var(--clr-dark); margin-bottom:.2rem;">
                    Invoice #{{ $order->invoice_number }}
                </h5>
                <span style="font-size:.82rem; color:var(--clr-muted);">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ \Carbon\Carbon::parse($order->sale_date)->format('d M Y, h:i A') }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span style="display:inline-flex; align-items:center; gap:.4rem; padding:6px 16px; border-radius:50px; font-size:.78rem; font-weight:700; background:{{ $sm['bg'] }}; color:{{ $sm['color'] }};">
                    <i class="fas {{ $sm['icon'] }}" style="font-size:.7rem;"></i>
                    {{ ucfirst($order->status) }}
                </span>
                <a href="{{ route('store.orders') }}" class="btn-outline" style="padding:8px 18px; font-size:.82rem; text-decoration:none;">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>

        {{-- Status Tracker --}}
        @if($order->status !== 'cancelled')
        <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); padding:1.75rem; margin-bottom:1.5rem;">
            <h6 style="font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); margin-bottom:1.5rem;">
                <i class="fas fa-map-marker-alt me-1"></i> Order Progress
            </h6>
            <div style="position:relative; display:flex; justify-content:space-between; align-items:flex-start;">
                {{-- Progress line --}}
                <div style="position:absolute; top:17px; left:5%; right:5%; height:2px; background:var(--clr-border); z-index:0;"></div>
                @if($currentIndex !== false && $currentIndex > 0)
                <div style="position:absolute; top:17px; left:5%; height:2px; background:var(--clr-primary); z-index:1; width:{{ min(($currentIndex / (count($steps)-1)) * 90, 90) }}%;"></div>
                @endif

                @foreach($steps as $i => $step)
                @php
                    $done = ($currentIndex !== false && $i <= $currentIndex);
                    $isCurrent = ($currentIndex !== false && $i === $currentIndex);
                    $stepIcons = ['fa-clock','fa-check-circle','fa-cog','fa-truck','fa-check-double'];
                @endphp
                <div style="text-align:center; position:relative; z-index:2; flex:1;">
                    <div style="width:36px; height:36px; border-radius:50%; margin:0 auto .6rem;
                                background:{{ $done ? 'var(--clr-primary)' : '#fff' }};
                                border:2px solid {{ $done ? 'var(--clr-primary)' : 'var(--clr-border)' }};
                                display:flex; align-items:center; justify-content:center;
                                box-shadow:{{ $isCurrent ? '0 0 0 4px rgba(37,99,235,.15)' : 'none' }};">
                        @if($done)
                            <i class="fas {{ $stepIcons[$i] }}" style="font-size:.72rem; color:#fff;"></i>
                        @else
                            <span style="font-size:.72rem; font-weight:700; color:#94a3b8;">{{ $i + 1 }}</span>
                        @endif
                    </div>
                    <div style="font-size:.72rem; font-weight:{{ $isCurrent ? '700' : '500' }}; color:{{ $done ? 'var(--clr-primary)' : '#94a3b8' }};">
                        {{ ucfirst($step) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div style="background:#fff1f2; border:1px solid #fecaca; border-radius:var(--radius-card); padding:1rem 1.5rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:.75rem;">
            <i class="fas fa-times-circle" style="color:#ef4444; font-size:1.25rem;"></i>
            <div>
                <div style="font-weight:700; color:#991b1b; font-size:.875rem;">Order Cancelled</div>
                <div style="font-size:.8rem; color:#b91c1c;">This order has been cancelled.</div>
            </div>
        </div>
        @endif

        <div class="row g-4">

            {{-- Order Items --}}
            <div class="col-lg-8">
                <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden;">
                    <div style="background:#f8fafc; border-bottom:1px solid var(--clr-border); padding:.875rem 1.25rem; display:flex; align-items:center; justify-content:space-between;">
                        <span style="font-size:.85rem; font-weight:700; color:var(--clr-dark);">
                            <i class="fas fa-shopping-bag me-2" style="color:var(--clr-primary);"></i>
                            Items Ordered <span style="font-size:.75rem; color:var(--clr-muted); font-weight:500;">({{ $order->items->count() }})</span>
                        </span>
                    </div>

                    @forelse($order->items as $item)
                    <div style="display:flex; align-items:center; gap:14px; padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9;">
                        {{-- Product image / placeholder --}}
                        <div style="width:56px; height:56px; border-radius:10px; background:#f1f5f9; border:1px solid var(--clr-border); flex-shrink:0; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                            @if(!empty($item->product->product_img))
                                <img src="{{ asset('storage/'.$item->product->product_img) }}"
                                     style="width:100%; height:100%; object-fit:cover;" alt="{{ $item->product->name ?? '' }}">
                            @else
                                <i class="fas fa-image" style="color:#cbd5e1; font-size:1.1rem;"></i>
                            @endif
                        </div>

                        <div style="flex:1; min-width:0;">
                            <div style="font-size:.875rem; font-weight:600; color:var(--clr-dark);">
                                {{ $item->product->name ?? 'N/A' }}
                            </div>
                            @if(!empty($item->variant->variant_name))
                            <div style="font-size:.75rem; color:var(--clr-muted); margin-top:2px;">
                                <i class="fas fa-tag me-1"></i>{{ $item->variant->variant_name }}
                            </div>
                            @endif
                        </div>

                        <div style="text-align:center; min-width:50px;">
                            <div style="font-size:.78rem; color:var(--clr-muted);">Qty</div>
                            <div style="font-size:.9rem; font-weight:700; color:var(--clr-dark);">{{ $item->quantity }}</div>
                        </div>

                        <div style="text-align:right; min-width:90px;">
                            <div style="font-size:.75rem; color:var(--clr-muted);">{{ $currencySymbol }} {{ number_format($item->unit_price, 2) }} each</div>
                            <div style="font-size:.95rem; font-weight:800; color:var(--clr-dark);">
                                {{ $currencySymbol }} {{ number_format($item->total_price, 2) }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="padding:2rem; text-align:center; color:var(--clr-muted); font-size:.875rem;">
                        No items found.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="col-lg-4">

                {{-- Payment & Info --}}
                <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden; margin-bottom:1rem;">
                    <div style="background:#f8fafc; border-bottom:1px solid var(--clr-border); padding:.875rem 1.25rem;">
                        <span style="font-size:.85rem; font-weight:700; color:var(--clr-dark);">
                            <i class="fas fa-receipt me-2" style="color:var(--clr-primary);"></i> Order Info
                        </span>
                    </div>
                    <div style="padding:1.1rem 1.25rem;">
                        <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid #f1f5f9;">
                            <span style="font-size:.82rem; color:var(--clr-muted);">Invoice #</span>
                            <span style="font-size:.82rem; font-weight:700; color:var(--clr-dark);">{{ $order->invoice_number }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid #f1f5f9;">
                            <span style="font-size:.82rem; color:var(--clr-muted);">Date</span>
                            <span style="font-size:.82rem; font-weight:600; color:var(--clr-dark);">{{ \Carbon\Carbon::parse($order->sale_date)->format('d M Y') }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:.5rem 0; border-bottom:1px solid #f1f5f9;">
                            <span style="font-size:.82rem; color:var(--clr-muted);">Status</span>
                            <span style="display:inline-flex; align-items:center; gap:.3rem; padding:3px 10px; border-radius:50px; font-size:.7rem; font-weight:700; background:{{ $sm['bg'] }}; color:{{ $sm['color'] }};">
                                <i class="fas {{ $sm['icon'] }}" style="font-size:.6rem;"></i> {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:.5rem 0;">
                            <span style="font-size:.82rem; color:var(--clr-muted);">Payment</span>
                            <span style="font-size:.82rem; font-weight:600; color:var(--clr-dark);">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Price Breakdown --}}
                <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden;">
                    <div style="background:#f8fafc; border-bottom:1px solid var(--clr-border); padding:.875rem 1.25rem;">
                        <span style="font-size:.85rem; font-weight:700; color:var(--clr-dark);">
                            <i class="fas fa-calculator me-2" style="color:var(--clr-primary);"></i> Price Summary
                        </span>
                    </div>
                    <div style="padding:1.1rem 1.25rem;">
                        <div style="display:flex; justify-content:space-between; padding:.45rem 0; border-bottom:1px solid #f1f5f9;">
                            <span style="font-size:.83rem; color:var(--clr-muted);">Subtotal</span>
                            <span style="font-size:.83rem; font-weight:600; color:var(--clr-dark);">{{ $currencySymbol }} {{ number_format($order->total_amount, 2) }}</span>
                        </div>

                        @if($order->discount_amount > 0)
                        <div style="display:flex; justify-content:space-between; padding:.45rem 0; border-bottom:1px solid #f1f5f9;">
                            <span style="font-size:.83rem; color:var(--clr-muted);">Discount</span>
                            <span style="font-size:.83rem; font-weight:600; color:#22c55e;">−{{ $currencySymbol }} {{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif

                        @if($order->tax_amount > 0)
                        <div style="display:flex; justify-content:space-between; padding:.45rem 0; border-bottom:1px solid #f1f5f9;">
                            <span style="font-size:.83rem; color:var(--clr-muted);">Tax</span>
                            <span style="font-size:.83rem; font-weight:600; color:var(--clr-dark);">{{ $currencySymbol }} {{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                        @endif

                        @if($order->shipping > 0)
                        <div style="display:flex; justify-content:space-between; padding:.45rem 0; border-bottom:1px solid #f1f5f9;">
                            <span style="font-size:.83rem; color:var(--clr-muted);">Shipping</span>
                            <span style="font-size:.83rem; font-weight:600; color:var(--clr-dark);">{{ $currencySymbol }} {{ number_format($order->shipping, 2) }}</span>
                        </div>
                        @endif

                        <div style="display:flex; justify-content:space-between; padding:.75rem 0 .25rem; margin-top:.25rem; border-top:2px solid var(--clr-border);">
                            <span style="font-size:.9rem; font-weight:700; color:var(--clr-dark);">Grand Total</span>
                            <span style="font-size:1.05rem; font-weight:800; color:var(--clr-primary);">{{ $currencySymbol }} {{ number_format($order->final_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

@endsection
