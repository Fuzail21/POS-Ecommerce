@extends('layouts.frontend.app')

    @section('frontend_css')
        <style>
            .pagination-wrapper {
                display: flex;
                justify-content: center;
            }

            .pagination {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap;
                gap: 6px;
                padding-left: 0;
                list-style: none;
            }

            .pagination .page-item {
                width: 40px;
                height: 40px;
                flex-shrink: 0;
            }

            .pagination .page-link {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
                height: 100%;
                padding: 0;
                border: 1px solid orange;
                border-radius: 0;
                color: #000;
            }

            .pagination .page-item.active .page-link {
                background-color: limegreen;
                border-color: limegreen;
                color: white;
                font-weight: bold;
            }

            .pagination .page-item:first-child .page-link {
                border-top-left-radius: 8px;
                border-bottom-left-radius: 8px;
            }

            .pagination .page-item:last-child .page-link {
                border-top-right-radius: 8px;
                border-bottom-right-radius: 8px;
            }

            .fruite-item {
                border: 1px solid #dee2e6; /* This is typically the color for border-secondary */
                border-radius: 0.25rem; /* Rounded corners, adjust as needed */
                overflow: hidden; /* Ensures rounded corners apply correctly to children */
                display: flex; /* Use flexbox to organize content vertically */
                flex-direction: column; /* Stack children vertically */
                height: 100%; /* Make sure the card takes full height of its container */
            }

            .fruite-item .p-4 {
                border-top: 0; /* Remove the top border if it was inherited or previously applied */
                flex-grow: 1; /* Allow the content area to grow and fill available space */
                display: flex; /* Use flexbox for content within p-4 */
                flex-direction: column; /* Stack content within p-4 vertically */
                justify-content: space-between; /* Distribute space between price/button and stock */
            }

            .product-card-img-container {
                height: 200px; /* Keep your fixed image height */
                overflow: hidden;
            }

            /* Ensure the image takes up 100% width and height of its container */
            .product-card-img-container img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
        </style>
    @endsection

    @section('frontend_content')
            @php
                use App\Models\Setting;

                $setting = Setting::first();
                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
            @endphp


        <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Shop</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item active text-white">Shop</li>
            </ol>
        </div>
        <!-- Single Page Header End -->


        <!-- Fruits Shop Start-->
        <div class="container-fluid fruite py-5">
            <div class="container py-5">
                {{-- <h1 class="mb-4">Fresh fruits shop</h1> --}}
                <div class="row g-4">
                    <div class="col-lg-12">
                        {{-- <div class="row g-4">
                            <div class="col-xl-3">
                                <div class="input-group w-100 mx-auto d-flex">
                                    <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                                    <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                                </div>
                            </div>
                            <div class="col-6"></div>
                            <div class="col-xl-3">
                                <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                                    <label for="fruits">Default Sorting:</label>
                                    <select id="fruits" name="fruitlist" class="border-0 form-select-sm bg-light me-3" form="fruitform">
                                        <option value="volvo">Nothing</option>
                                        <option value="saab">Popularity</option>
                                        <option value="opel">Organic</option>
                                        <option value="audi">Fantastic</option>
                                    </select>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row g-4">
                            <div class="col-lg-3">
                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <h4>Categories</h4>
                                            <ul class="list-unstyled fruite-categorie">
                                                @foreach ($categories as $category)
                                                    <li>
                                                        <div class="d-flex justify-content-between fruite-name">
                                                            <a href="{{ route('store.shop', ['category' => $category->id]) }}">
                                                                <i class="fas fa-apple-alt me-2"></i>{{ $category->name }}
                                                            </a>
                                                            <span>({{ $category->products_count }})</span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-12">
                                        <div class="mb-3">
                                            <h4 class="mb-2">Price</h4>
                                            <input type="range" class="form-range w-100" id="rangeInput" name="rangeInput" min="0" max="500" value="0" oninput="amount.value=rangeInput.value">
                                            <output id="amount" name="amount" min-velue="0" max-value="500" for="rangeInput">0</output>
                                        </div>
                                    </div> --}}
                                    
                                    {{-- <div class="col-lg-12">
                                        <h4 class="mb-3">Featured products</h4>
                                        <div class="d-flex align-items-center justify-content-start">
                                            <div class="rounded me-4" style="width: 100px; height: 100px;">
                                                <img src="img/featur-1.jpg" class="img-fluid rounded" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-2">Big Banana</h6>
                                                <div class="d-flex mb-2">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="d-flex mb-2">
                                                    <h5 class="fw-bold me-2">2.99 $</h5>
                                                    <h5 class="text-danger text-decoration-line-through">4.11 $</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-start">
                                            <div class="rounded me-4" style="width: 100px; height: 100px;">
                                                <img src="img/featur-2.jpg" class="img-fluid rounded" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-2">Big Banana</h6>
                                                <div class="d-flex mb-2">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="d-flex mb-2">
                                                    <h5 class="fw-bold me-2">2.99 $</h5>
                                                    <h5 class="text-danger text-decoration-line-through">4.11 $</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-start">
                                            <div class="rounded me-4" style="width: 100px; height: 100px;">
                                                <img src="img/featur-3.jpg" class="img-fluid rounded" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-2">Big Banana</h6>
                                                <div class="d-flex mb-2">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="d-flex mb-2">
                                                    <h5 class="fw-bold me-2">2.99 $</h5>
                                                    <h5 class="text-danger text-decoration-line-through">4.11 $</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center my-4">
                                            <a href="#" class="btn border border-secondary px-4 py-3 rounded-pill text-primary w-100">Vew More</a>
                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-lg-12">
                                        <div class="position-relative">
                                            <img src="img/banner-fruits.jpg" class="img-fluid w-100 rounded" alt="">
                                            <div class="position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%);">
                                                <h3 class="text-secondary fw-bold">Fresh <br> Fruits <br> Banner</h3>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="row g-4 justify-content-start">
                                    @forelse($products as $product)
                                        <div class="col-md-6 col-lg-6 col-xl-4">
                                            <div class="rounded position-relative fruite-item border border-secondary">
                                                <div class="fruite-img product-card-img-container rounded-top" style="height: 250px; overflow: hidden;">
                                                    {{-- Product Image --}}
                                                    @if (!empty($product->product_img))
                                                        <img src="{{ asset('storage/' . $product->product_img) }}" class="img-fluid w-100 h-100 rounded-top" style="object-fit: cover;" alt="{{ $product->name }}">
                                                    @else
                                                        <img src="{{ $placeholderProductImg ?? asset('build/assets/frontend/img/default-product.jpg') }}" class="img-fluid w-100 h-100 rounded-top" style="object-fit: cover;" alt="No Image">
                                                    @endif

                                                    {{-- SALE Badge --}}
                                                    @if ($product->has_discount && $product->actual_price > 0)
                                                        @php
                                                            $discountPercent = round((($product->actual_price - $product->final_price) / $product->actual_price) * 100);
                                                        @endphp
                                                        <div class="position-absolute top-0 end-0 m-2 px-2 py-1 bg-danger text-white fw-bold rounded">
                                                            -{{ $discountPercent }}%
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Category Label --}}
                                                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                                    {{ $product->category->name ?? 'Uncategorized' }}
                                                </div>

                                                <div class="p-4 rounded-bottom">
                                                    {{-- Title --}}
                                                    <h4 class="mb-2" style="font-size: 1.25rem; line-height: 1.4;">{{ Str::words($product->name, 8, '...') }}</h4>
                                                    <p>{{ Str::limit($product->description ?? 'No description available.', 70) }}</p>

                                                    {{-- 🟡 Price Section --}}
                                                    <div class="d-flex flex-column mb-2">
                                                        @if($product->has_discount && $product->final_price < $product->actual_price)
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="text-dark fs-5 fw-bold">
                                                                    {{ number_format($product->final_price, 2) }} {{ $setting->currency_symbol ?? '$' }}
                                                                </span>
                                                                <span class="text-danger text-decoration-line-through">
                                                                    {{ number_format($product->actual_price, 2) }} {{ $setting->currency_symbol ?? '$' }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="text-dark fs-5 fw-bold">
                                                                {{ number_format($product->actual_price, 2) }} {{ $setting->currency_symbol ?? '$' }}
                                                            </span>
                                                        @endif
                                                        <span class="text-muted" style="font-size: 0.9em;">
                                                            / {{ $product->baseUnit->name ?? 'unit' }}
                                                        </span>
                                                    </div>

                                                    {{-- Buttons --}}
                                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                                        @if($product->in_stock)
                                                            @if($product->has_variants)
                                                                <a href="{{ route('store.product', $product->id) }}" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                    <i class="fa fa-eye me-2 text-primary"></i> View Product
                                                                </a>
                                                            @else
                                                                <form action="{{ route('cart.add') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                                    <input type="hidden" name="quantity" value="1">
                                                                    <input type="hidden" name="stock" value="{{ $product->stock_quantity }}">
                                                                    <input id="final_price_{{ $product->id }}" type="hidden" name="price" value="{{ $product->has_discount ? $product->final_price : ($product->actual_price ?? 0) }}">
                                                                    <button type="submit" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @else
                                                            <button type="button" class="btn border border-secondary rounded-pill px-3 text-danger" disabled>
                                                                Out of Stock ({{ number_format($product->stock_quantity, 0) }} Left)
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <p class="mt-2 text-muted" style="font-size: 0.9em;">
                                                        Stock: {{ number_format($product->stock_quantity, 0) }} {{ $product->baseUnit->name ?? 'units' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-5">
                                            <h3>No products available at the moment.</h3>
                                            <p>Please check back later!</p>
                                        </div>
                                    @endforelse
                                    <div class="col-12">
                                        <div class="pagination-wrapper d-flex justify-content-center mt-5">
                                            {{ $products->links('pagination::bootstrap-5') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('frontend_js')
    
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let priceInput = document.getElementById('final_price_{{ $product->id }}');
                if (priceInput) {
                    priceInput.value = {{ $product->has_discount ? $product->final_price : ($product->actual_price ?? 0) }};
                }
            });
        </script>

    @endsection