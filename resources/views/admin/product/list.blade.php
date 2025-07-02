@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            @php
                use App\Models\Setting;
                use Milon\Barcode\DNS1D;

                $setting = Setting::first();
                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray

                // Initialize barcode generator once outside the product loop for efficiency
                $barcodeGenerator = new DNS1D();
                $barcodeGenerator->setStorPath(storage_path('framework/barcodes'));

                // Define a placeholder for product image if not available
                $placeholderProductImg = 'https://placehold.co/50x50/cccccc/333333?text=No+Img';
            @endphp
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Product List</h4>
                    </div>
                    <a href="{{ route('products.create') }}" class="btn text-white add-list" style="background-color: {{ $primaryColor }};"><i class="las la-plus mr-3"></i>Add Product</a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Products</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead style="background-color: #F4F5FA;">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Img</th>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Barcode</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $index => $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>
                                                @if (!empty($product->product_img))
                                                    <img src="{{ asset('storage/' . $product->product_img) }}" alt="Product Image" width="50" style="object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <img src="{{ $placeholderProductImg }}" alt="No Image" width="50" style="object-fit: cover; border-radius: 5px;">
                                                @endif
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->sku }}</td>
                                            <td>
                                                @if($product->barcode)
                                                    <a href="#" data-toggle="modal" data-target="#barcodeModal{{ $product->id }}" title="View Barcode">
                                                        <i class="ri-barcode-line" style="font-size: 1.4rem; color: #007bff;"></i>
                                                    </a>
                                                @else
                                                    <em>No Barcode</em>
                                                @endif
                                            </td>
                                            <td>{{ $product->category->name ?? '-' }}</td>
                                            <td>{{ $product->brand ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    <a class="badge bg-success mr-2 p-1 rounded" data-toggle="tooltip" title="Edit" href="{{ route('products.edit', $product->id) }}">
                                                        <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                    <a class="badge bg-warning mr-2 p-1 rounded" data-toggle="tooltip" title="Delete" href="{{ route('products.destroy', $product->id) }}" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this product?')) { document.getElementById('delete-form-{{$product->id}}').submit(); }">
                                                        <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                    <form id="delete-form-{{$product->id}}" action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    @if ($product->has_variants === 1)
                                                        <a class="badge bg-info p-1 rounded" data-toggle="tooltip" title="View Variants" href="{{ route('products.variants', $product->id) }}">
                                                            <i class="ri-eye-line" style="font-size: 1.1rem;"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No products found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end mt-3">
                            {{ $products->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Barcode Modals --}}
@foreach ($products as $product)
    @if($product->barcode)
        @php
            // Generate barcode image for each product within the loop
            $productBarcodeImage = '';
            if ($product->barcode) {
                $productBarcodeImage = 'data:image/png;base64,' . $barcodeGenerator->getBarcodePNG($product->barcode, 'C128', 2, 70);
            }
        @endphp
        <div class="modal fade" id="barcodeModal{{ $product->id }}" tabindex="-1" aria-labelledby="barcodeModalLabel{{ $product->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header justify-content-center position-relative">
                        <h5 class="modal-title text-center w-100" id="barcodeModalLabel{{ $product->id }}">
                            Barcode - {{ $product->name }}
                        </h5>
                        <button type="button" class="close position-absolute" style="right: 15px;" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body text-center">
                        <div style="display: inline-block; max-width: 100%; overflow-x: auto;">
                            {{-- Display the barcode as an image --}}
                            <img src="{{ $productBarcodeImage }}" alt="Barcode for {{ $product->name }}" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
                        </div>
                        <div class="mt-2 text-lg font-semibold">{{ $product->barcode }}</div>

                        {{-- Print Button --}}
                        <button type="button" class="btn text-white mt-4 py-2 px-4 rounded"
                                style="background-color: {{ $secondaryColor }};"
                                onclick="printProductBarcode('{{ $product->barcode }}', '{{ $product->name }}', '{{ $productBarcodeImage }}')">
                            Print Barcode
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@push('scripts')
<!-- Ensure Bootstrap JS (if not already loaded in layout) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /**
     * Prints a product barcode in a new window with a card-like layout.
     * @param {string} barcodeValue - The actual barcode string (e.g., product SKU).
     * @param {string} productName - The name of the product.
     * @param {string} barcodeImageBase64 - The base64 encoded PNG image data of the barcode.
     * @param {string} primaryColor - The primary color for the card header.
     */
    function printProductBarcode(barcodeValue, productName, barcodeImageBase64) {
        const printContents = `
            <div class="card-print">
                <div class="card-header-print">
                    <div class="card-title-print">Product Barcode</div>
                </div>
                <div class="card-body-print">
                    <p><strong>Product:</strong> ${productName}</p>
                    <p><strong>Barcode ID:</strong> <span class="uppercase">${barcodeValue}</span></p>
                    <div class="barcode-img-container-print">
                        <img src="${barcodeImageBase64}" alt="Product Barcode">
                    </div>
                </div>
            </div>
        `;

        const styles = `
            <style>
                body {
                    font-family: 'Inter', sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: white;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                }
                .card-print {
                    width: 350px;
                    border-radius: 10px;
                    border: 1px solid #ccc;
                    background-color: #fff;
                    box-shadow: none; /* No shadow for print */
                    overflow: hidden;
                }
                .card-header-print {
                    color: white;
                    padding: 15px;
                    text-align: center;
                    font-size: 20px;
                    font-weight: bold;
                }
                .card-title-print {
                    margin-bottom: 5px;
                }
                .card-body-print {
                    padding: 20px;
                }
                .card-body-print p {
                    margin: 10px 0;
                    font-size: 15px;
                }
                .card-body-print p strong {
                    width: 90px;
                    display: inline-block;
                    color: #333;
                }
                .barcode-img-container-print {
                    margin-top: 20px;
                    text-align: center;
                }
                .barcode-img-container-print img {
                    max-width: 100%;
                    height: auto;
                    display: block;
                    margin: 0 auto;
                    background-color: white; /* Ensure white background for barcode */
                    padding: 5px 0;
                }
                /* Ensure no print-specific styles interfere with barcode */
            </style>
        `;

        const win = window.open('', '', 'height=600,width=500');
        win.document.write('<html><head><title>Print Product Barcode</title>');
        win.document.write('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">'); // Include font
        win.document.write(styles);
        win.document.write('</head><body>');
        win.document.write(printContents);
        win.document.write('</body></html>');
        win.document.close();
        win.focus();
        win.print();
        // win.close(); // Optionally close the window after print dialog is shown
    }
</script>
@endpush