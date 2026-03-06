@extends('layouts.frontend.app')

@section('frontend_content')

@php
    $currencySymbol = $setting->currency_symbol ?? '$';
@endphp

<div class="page-header-band">
    <div class="container text-center">
        <h1>My Orders</h1>
        <nav>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item active">My Orders</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Header row --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h4 style="font-weight:800; color:var(--clr-dark); margin-bottom:.2rem;">Order History</h4>
                <p style="font-size:.85rem; color:var(--clr-muted); margin:0;">Track and manage all your orders</p>
            </div>
            <a href="{{ route('store.shop') }}" class="btn-prim" style="text-decoration:none;">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
        </div>

        @if($orders->isEmpty())
            <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); padding:4rem 2rem; text-align:center;">
                <div style="width:72px; height:72px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;">
                    <i class="fas fa-box-open" style="font-size:1.75rem; color:#cbd5e1;"></i>
                </div>
                <h5 style="font-weight:700; color:var(--clr-dark); margin-bottom:.4rem;">No Orders Yet</h5>
                <p style="font-size:.875rem; color:var(--clr-muted); margin-bottom:1.5rem;">You haven't placed any orders yet. Start shopping to see them here.</p>
                <a href="{{ route('store.shop') }}" class="btn-prim" style="text-decoration:none;">
                    <i class="fas fa-arrow-right"></i> Start Shopping
                </a>
            </div>
        @else
            <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden;">
                {{-- Table header --}}
                <div style="background:#f8fafc; border-bottom:1px solid var(--clr-border); padding:.875rem 1.5rem; display:grid; grid-template-columns:1.5fr 1fr .75fr 1fr 1fr 1fr; gap:1rem; align-items:center;" class="d-none d-lg-grid">
                    <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--clr-muted);">Invoice #</span>
                    <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--clr-muted);">Date</span>
                    <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--clr-muted);">Items</span>
                    <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--clr-muted);">Total</span>
                    <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--clr-muted);">Status</span>
                    <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--clr-muted);">Action</span>
                </div>

                @foreach($orders as $order)
                @php
                    $statusMap = [
                        'pending'    => ['bg' => '#fef9c3', 'color' => '#854d0e', 'icon' => 'fa-clock'],
                        'confirmed'  => ['bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => 'fa-check-circle'],
                        'processing' => ['bg' => '#e0f2fe', 'color' => '#0c4a6e', 'icon' => 'fa-cog'],
                        'shipped'    => ['bg' => '#ede9fe', 'color' => '#5b21b6', 'icon' => 'fa-truck'],
                        'completed'  => ['bg' => '#dcfce7', 'color' => '#166534', 'icon' => 'fa-check-double'],
                        'cancelled'  => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'fa-times-circle'],
                    ];
                    $sm = $statusMap[$order->status] ?? ['bg' => '#f1f5f9', 'color' => '#64748b', 'icon' => 'fa-circle'];
                @endphp
                <div style="padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9; display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; transition:background .15s;"
                     onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <div style="flex:1.5; min-width:140px;">
                        <div style="font-size:.875rem; font-weight:700; color:var(--clr-dark);">{{ $order->invoice_number }}</div>
                        <div style="font-size:.72rem; color:var(--clr-muted);">Invoice</div>
                    </div>
                    <div style="flex:1; min-width:110px;">
                        <div style="font-size:.875rem; font-weight:500; color:var(--clr-dark);">
                            {{ \Carbon\Carbon::parse($order->sale_date)->format('d M Y') }}
                        </div>
                        <div style="font-size:.72rem; color:var(--clr-muted);">
                            {{ \Carbon\Carbon::parse($order->sale_date)->format('h:i A') }}
                        </div>
                    </div>
                    <div style="flex:.75; min-width:80px;">
                        <span style="font-size:.875rem; font-weight:600; color:var(--clr-dark);">{{ $order->items->count() }}</span>
                        <span style="font-size:.78rem; color:var(--clr-muted);"> item(s)</span>
                    </div>
                    <div style="flex:1; min-width:100px;">
                        <div style="font-size:.95rem; font-weight:800; color:var(--clr-primary);">
                            {{ $currencySymbol }} {{ number_format($order->final_amount, 2) }}
                        </div>
                    </div>
                    <div style="flex:1; min-width:110px;">
                        <span style="display:inline-flex; align-items:center; gap:.35rem; padding:4px 12px; border-radius:50px; font-size:.72rem; font-weight:700; background:{{ $sm['bg'] }}; color:{{ $sm['color'] }};">
                            <i class="fas {{ $sm['icon'] }}" style="font-size:.65rem;"></i>
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div style="flex:1; min-width:100px;">
                        <a href="{{ route('store.order.detail', $order->id) }}" class="btn-outline" style="padding:7px 16px; font-size:.78rem; text-decoration:none;">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </div>
                </div>
                @endforeach

                @if($orders->hasPages())
                <div style="padding:1rem 1.5rem; border-top:1px solid var(--clr-border); display:flex; justify-content:flex-end;">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        @endif

    </div>
</section>

@endsection
