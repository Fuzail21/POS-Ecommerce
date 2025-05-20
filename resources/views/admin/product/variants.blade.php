@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

    <div class="content-page">

        <div class="container">
            <h4 class="mb-1">Variants for: <b>{{ $product->name }}</b></h4>

            @if($product->variants->count())
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Variant Name</th>
                            <th>SKU</th>
                            <th>Barcode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->variants as $index => $variant)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $variant->variant_name }}</td>
                                <td>{{ $variant->sku }}</td>
                                <td>{{ $variant->barcode ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No variants found for this product.</p>
            @endif

            <a href="{{ route('products.list') }}" class="btn btn-secondary mt-3">Back to Products</a>
        </div>
    </div>
@endsection
