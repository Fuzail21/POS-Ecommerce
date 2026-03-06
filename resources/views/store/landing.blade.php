@extends('layouts.frontend.app')

@section('frontend_content')
@php
    use App\Models\Setting;
    $setting = Setting::first();
    $primaryColor = $setting->primary_color ?? '#2563eb';
@endphp

{{-- ─── Hero ─── --}}
<section class="hero-wrap position-relative">
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="fas fa-bolt"></i> Premium Quality, Best Prices
                </div>
                <h1 class="hero-title mb-3">
                    Shop Smart,<br>
                    <span class="hero-accent">Live Better</span>
                </h1>
                <p class="hero-sub">
                    Discover thousands of products with unbeatable deals. Quality guaranteed and delivered to your door.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('store.shop') }}" class="btn-prim" style="padding:13px 28px; font-size:.95rem; text-decoration:none;">
                        Shop Now <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                    <a href="{{ route('store.shop') }}" class="btn-outline" style="padding:13px 28px; font-size:.95rem;">
                        Browse All
                    </a>
                </div>
                <div class="d-flex align-items-center gap-0 mt-4 flex-wrap">
                    <div class="text-center me-4">
                        <div class="hero-stat-val">{{ $products->count() }}+</div>
                        <div class="hero-stat-lbl">Products</div>
                    </div>
                    <div class="text-center hero-divider me-4">
                        <div class="hero-stat-val">100%</div>
                        <div class="hero-stat-lbl">Authentic</div>
                    </div>
                    <div class="text-center hero-divider">
                        <div class="hero-stat-val">Fast</div>
                        <div class="hero-stat-lbl">Delivery</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── Features ─── --}}
<section class="py-5" style="background:#f8fafc;">
    <div class="container">
        <div class="row g-4">
            @foreach([
                ['icon' => 'fas fa-truck',     'title' => 'Free Shipping',    'text' => 'On all orders, no minimum'],
                ['icon' => 'fas fa-shield-alt', 'title' => 'Secure Payment',  'text' => '100% safe transactions'],
                ['icon' => 'fas fa-undo',       'title' => 'Easy Returns',    'text' => '30-day hassle-free policy'],
                ['icon' => 'fas fa-headset',    'title' => '24/7 Support',    'text' => 'Always here to help you'],
            ] as $f)
            <div class="col-md-6 col-lg-3">
                <div class="feat-card h-100">
                    <div class="feat-icon"><i class="{{ $f['icon'] }}"></i></div>
                    <h6>{{ $f['title'] }}</h6>
                    <p>{{ $f['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── Latest Products ─── --}}
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
            <div>
                <h2 class="sec-title mb-1">Latest Products</h2>
                <p class="sec-sub mb-0">Fresh arrivals handpicked for you</p>
            </div>
            <a href="{{ route('store.shop') }}" class="btn-outline d-none d-md-inline-flex" style="text-decoration:none;">
                View All <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($products as $product)
            <div class="col-sm-6 col-lg-4 col-xl-3 d-flex">
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
                                <span class="p-unit">/ {{ $product->displayUnit->name ?? 'unit' }}</span>
                            </div>

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
                                            <input type="hidden" name="price" value="{{ $product->has_discount ? $product->final_price : ($product->actual_price ?? 0) }}">
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
            @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x mb-3 d-block" style="color:#cbd5e1;"></i>
                <p style="color:#94a3b8;">No products available yet. Check back soon!</p>
            </div>
            @endforelse
        </div>

        <div class="text-center mt-4 d-md-none">
            <a href="{{ route('store.shop') }}" class="btn-outline" style="text-decoration:none;">View All Products</a>
        </div>
    </div>
</section>

@endsection

@section('frontend_js')
<script>
    // Landing page JS placeholder — no product-specific JS needed here
</script>
@endsection
