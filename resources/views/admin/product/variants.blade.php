@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                @php
                    use App\Models\Setting;
                    $setting = Setting::first();
                    $primaryColor = $setting->primary_color ?? '#0d6efd';
                    $secondaryColor = $setting->secondary_color ?? '#6c757d';
                @endphp
    
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">{{ $title }} - {{ $product->name }}</h4>
                        </div>
                        <a href="{{ route('products.list') }}" class="btn text-white" style="background-color: {{ $secondaryColor }};">
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
                                            <th>Barcode</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($product->variants as $index => $variant)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $variant->variant_name }}</td>
                                                <td>{{ $variant->sku }}</td>
                                                <td>
                                                    @if($variant->barcode)
                                                        <a href="#" data-toggle="modal" data-target="#barcodeModal{{ $variant->id }}" title="View Barcode">
                                                            <i class="ri-barcode-line" style="font-size: 1.4rem; color: #007bff;"></i>
                                                        </a>
                                                    @else
                                                        <em>No Barcode</em>
                                                    @endif
                                                </td>
                                                <td>{{ $variant->sale_price }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No variants found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{-- No pagination unless paginated manually --}}
                        </div>
                    </div>
                </div>
    
            </div>
        </div>
    </div>
    
    {{-- Barcode Modals --}}
    @foreach ($product->variants as $variant)
        @if($variant->barcode)
            <div class="modal fade" id="barcodeModal{{ $variant->id }}" tabindex="-1" aria-labelledby="barcodeModalLabel{{ $variant->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header justify-content-center position-relative">
                            <h5 class="modal-title text-center w-100" id="barcodeModalLabel{{ $variant->id }}">
                                Barcode - {{ $variant->variant_name }}
                            </h5>
                            <button type="button" class="close position-absolute" style="right: 15px;" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body text-center">
                            <div style="display: inline-block; max-width: 100%; overflow-x: auto;">
                                {!! (new \Milon\Barcode\DNS1D)->getBarcodeHTML($variant->barcode, 'C128', 2, 70) !!}
                            </div>
                            <div class="mt-2">{{ $variant->barcode }}</div>
                        </div>

                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection

@push('scripts')
<!-- Ensure Bootstrap JS is loaded -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush
