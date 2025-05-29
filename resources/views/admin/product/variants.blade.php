@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">{{ $title }} - {{ $product->name }}</h4>
                    </div>
                    <a href="{{ route('products.list') }}" class="btn btn-secondary">
                        <i class="las la-arrow-left mr-2"></i>Back to Products
                    </a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Variants List</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead style="background-color: #F4F5FA;">
                                    <tr>
                                        <th>#</th>
                                        <th>Variant Name</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($product->variants as $index => $variant)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $variant->variant_name }}</td>
                                            <td>{{ $variant->sku }}</td>
                                            <td>{{ $variant->sale_price }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No variants found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- No pagination here unless variants are paginated --}}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
