@extends('layouts.frontend.app')

@section('frontend_css')
<style>
    .sidebar-box {
        background: #fff;
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-card);
        padding: 1.25rem;
    }
    .sidebar-label {
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .8px; color: #94a3b8; margin-bottom: .75rem;
    }
    .search-wrap .form-control {
        border: 1.5px solid var(--clr-border);
        border-radius: 10px 0 0 10px;
        padding: 12px 16px; font-size: .875rem;
    }
    .search-wrap .form-control:focus {
        border-color: var(--clr-primary);
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .search-wrap .btn-search {
        background: var(--clr-primary); color: #fff;
        border: none; border-radius: 0 10px 10px 0;
        padding: 12px 20px; font-size: .875rem; font-weight: 600;
    }
</style>
@endsection

@section('frontend_content')
@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp

{{-- Page Header --}}
<div class="page-header-band">
    <div class="container text-center">
        <h1>Shop</h1>
        <nav>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item active">Shop</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Shop Body --}}
<section class="py-5">
    <div class="container">

        {{-- Search --}}
        <form method="GET" action="{{ route('store.shop') }}" class="mb-4">
            @if($categoryId)
                <input type="hidden" name="category" value="{{ $categoryId }}">
            @endif
            <div class="row g-2 align-items-center">
                <div class="col search-wrap">
                    <div class="input-group">
                        <input type="search" name="q" class="form-control"
                               placeholder="Search products by name, SKU or barcode..."
                               value="{{ $search ?? '' }}">
                        <button type="submit" class="btn-search px-4">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                @if($search)
                <div class="col-auto d-flex align-items-center gap-2">
                    <a href="{{ route('store.shop', $categoryId ? ['category' => $categoryId] : []) }}"
                       class="btn-outline" style="padding:10px 16px; text-decoration:none; font-size:.82rem;">
                        <i class="fas fa-times"></i> Clear
                    </a>
                    <span style="font-size:.82rem; color:#94a3b8;">
                        {{ $products->total() }} results for &ldquo;{{ $search }}&rdquo;
                    </span>
                </div>
                @endif
            </div>
        </form>

        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="sidebar-box">
                    <div class="sidebar-label">Categories</div>
                    <a href="{{ route('store.shop') }}"
                       class="cat-link {{ !$categoryId ? 'active' : '' }}">
                        <span><i class="fas fa-th-large me-2" style="font-size:.8rem;"></i> All Products</span>
                        <span class="cat-count">All</span>
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('store.shop', ['category' => $cat->id]) }}"
                       class="cat-link {{ $categoryId == $cat->id ? 'active' : '' }}">
                        <span><i class="fas fa-tag me-2" style="font-size:.75rem;"></i> {{ $cat->name }}</span>
                        <span class="cat-count">{{ $cat->products_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Products --}}
            <div class="col-lg-9">
                @if($products->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x mb-3 d-block" style="color:#cbd5e1;"></i>
                        <h5 style="color:#475569;">No products found</h5>
                        <p style="color:#94a3b8; font-size:.875rem;">Try a different search term or browse all categories.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($products as $product)
                        <div class="col-sm-6 col-xl-4 d-flex">
                            <div class="p-card w-100">
                                <div class="p-card-img">
                                    <a href="{{ route('store.product', $product->id) }}"
                                       class="position-absolute w-100 h-100 top-0 start-0" style="z-index:1;"></a>

                                    @if(!empty($product->product_img))
                                        <img src="{{ asset('storage/'.$product->product_img) }}" alt="{{ $product->name }}">
                                    @else
                                        <div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f1f5f9; gap:.4rem;">
                                            <i class="fas fa-image" style="font-size:2rem; color:#cbd5e1;"></i>
                                            <span style="font-size:.72rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.5px;">No Image</span>
                                        </div>
                                    @endif

                                    <span class="p-card-cat">{{ $product->category->name ?? 'General' }}</span>

                                    @if($product->has_discount && $product->actual_price > 0)
                                        @php $pct = round((($product->actual_price - $product->final_price) / $product->actual_price) * 100); @endphp
                                        <span class="p-card-sale">-{{ $pct }}%</span>
                                    @endif

                                    @if(!$product->in_stock)
                                        <div class="p-card-oos">Out of Stock</div>
                                    @endif
                                </div>

                                <div class="p-card-body">
                                    <p class="p-card-title">{{ Str::words($product->name, 6, '...') }}</p>
                                    <p class="p-card-desc">{{ Str::limit($product->description ?? '', 58) }}</p>

                                    <div class="mt-auto">
                                        <div class="d-flex align-items-baseline gap-2 mb-3">
                                            @if($product->has_discount)
                                                <span class="p-price">{{ $setting->currency_symbol ?? 'Rs' }} {{ number_format($product->final_price, 0) }}</span>
                                                <span class="p-price-orig">{{ $setting->currency_symbol ?? 'Rs' }} {{ number_format($product->actual_price, 0) }}</span>
                                            @else
                                                <span class="p-price">{{ $setting->currency_symbol ?? 'Rs' }} {{ number_format($product->actual_price ?? 0, 0) }}</span>
                                            @endif
                                            <span class="p-unit">/ {{ $product->baseUnit->name ?? 'unit' }}</span>
                                        </div>
                                        <p style="font-size:.73rem; color:#94a3b8; margin-bottom:.5rem;">
                                            Stock: {{ number_format($product->stock_quantity, 0) }} {{ $product->baseUnit->name ?? 'units' }}
                                        </p>
                                        <div style="position:relative; z-index:3;">
                                            @if($product->in_stock)
                                                @if($product->has_variants)
                                                    <a href="{{ route('store.product', $product->id) }}" class="btn-outline w-100 justify-content-center" style="text-decoration:none;">
                                                        <i class="fas fa-eye"></i> View Options
                                                    </a>
                                                @else
                                                    <form action="{{ route('cart.add') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <input type="hidden" name="stock" value="{{ $product->stock_quantity }}">
                                                        <input id="final_price_{{ $product->id }}" type="hidden" name="price"
                                                               value="{{ $product->has_discount ? $product->final_price : ($product->actual_price ?? 0) }}">
                                                        <button type="submit" class="btn-prim w-100 justify-content-center">
                                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <button class="btn-ghost w-100 text-center" disabled style="cursor:not-allowed; opacity:.6;">
                                                    Out of Stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-5">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

@section('frontend_js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($products as $product)
        (function() {
            var el = document.getElementById('final_price_{{ $product->id }}');
            if (el) el.value = '{{ $product->has_discount ? $product->final_price : ($product->actual_price ?? 0) }}';
        })();
        @endforeach
    });
</script>
@endsection
