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
            @endphp

            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        {{-- Assuming $title and $product are passed from the controller --}}
                        <h4 class="mb-3">{{ $title ?? 'Product Variants' }} - {{ $product->name ?? 'Product' }}</h4>
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

{{-- Barcode Modals for Variants --}}
@foreach ($product->variants as $variant)
    @if($variant->barcode)
        @php
            // Generate barcode image for each variant within the loop
            $variantBarcodeImage = 'data:image/png;base64,' . $barcodeGenerator->getBarcodePNG($variant->barcode, 'C128', 2, 70);
        @endphp
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
                            {{-- Display the barcode as an image --}}
                            <img src="{{ $variantBarcodeImage }}" alt="Barcode for {{ $variant->variant_name }}" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
                        </div>
                        <div class="mt-2 text-lg font-semibold">{{ $variant->barcode }}</div>

                        {{-- Print Button --}}
                        <button type="button" class="btn text-white mt-4 py-2 px-4 rounded print-barcode-btn"
                                style="background-color: {{ $secondaryColor }};"
                                data-barcode="{{ $variant->barcode }}"
                                data-product-name="{{ $variant->variant_name }}"
                                data-barcode-image="{{ $variantBarcodeImage }}"
                                data-primary-color="{{ $primaryColor }}">
                            Print Barcode
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@section('js')
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
    function printProductBarcode(barcodeValue, productName, barcodeImageBase64, primaryColor) {
        const printContents = `
            <div class="card-print">
                <div class="card-header-print" style="background-color: ${primaryColor};">
                    <div class="card-title-print">Product Barcode</div>
                </div>
                <div class="card-body-print">
                    <p class="product-name-print"><strong>Variant:</strong> ${productName}</p>
                    <p class="barcode-id-print"><strong>Barcode ID:</strong> <span class="uppercase">${barcodeValue}</span></p>
                    <div class="barcode-img-container-print">
                        <img src="${barcodeImageBase64}" alt="Product Barcode">
                    </div>
                </div>
            </div>
        `;

        const styles = `
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

                body {
                    font-family: 'Inter', sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f0f2f5; /* Light gray background */
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                }
                .card-print {
                    width: 350px;
                    border-radius: 12px; /* More rounded corners */
                    border: none; /* Remove default border */
                    background-color: #fff;
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Stronger shadow */
                    overflow: hidden;
                    transition: transform 0.3s ease-in-out;
                }
                .card-print:hover {
                    transform: translateY(-5px); /* Slight lift on hover (though not interactive in print) */
                }
                .card-header-print {
                    color: white;
                    padding: 18px 20px; /* More padding */
                    text-align: center;
                    font-size: 22px; /* Slightly larger title */
                    font-weight: 700; /* Bolder font */
                    border-bottom: 1px solid rgba(255, 255, 255, 0.2); /* Subtle separator */
                    background: linear-gradient(135deg, ${primaryColor} 0%, ${primaryColor}EE 100%); /* Gradient effect */
                    position: relative;
                }
                .card-header-print::before {
                    content: ' ';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="%23FFFFFF" opacity="0.1"><circle cx="50" cy="50" r="40" /></svg>'); /* Subtle pattern */
                    background-repeat: repeat;
                    opacity: 0.1;
                }
                .card-title-print {
                    margin-bottom: 0; /* Remove default margin */
                    position: relative;
                    z-index: 1; /* Ensure text is above pattern */
                }
                .card-body-print {
                    padding: 25px; /* More padding */
                    text-align: left; /* Align text left */
                    color: #333;
                }
                .card-body-print p {
                    margin: 12px 0; /* Adjusted margin */
                    font-size: 16px; /* Slightly larger text */
                    line-height: 1.5;
                    display: flex; /* Use flex for alignment */
                    align-items: center;
                }
                .card-body-print p strong {
                    min-width: 100px; /* Ensure consistent width for labels */
                    display: inline-block;
                    color: #555;
                    font-weight: 600; /* Semi-bold label */
                    margin-right: 10px;
                }
                .barcode-id-print .uppercase {
                    font-weight: 700;
                    color: #000;
                    letter-spacing: 0.5px;
                }
                .barcode-img-container-print {
                    margin-top: 30px; /* More space above barcode */
                    text-align: center;
                    background-color: #f9f9f9; /* Light background for barcode area */
                    padding: 15px;
                    border-radius: 8px;
                    border: 1px dashed #ddd; /* Dashed border for visual separation */
                }
                .barcode-img-container-print img {
                    max-width: 90%; /* Slightly smaller barcode image */
                    height: auto;
                    display: block;
                    margin: 0 auto;
                    background-color: white; /* Ensure white background for barcode */
                    padding: 5px 0;
                }
                /* Print specific adjustments */
                @media print {
                    body {
                        background-color: white;
                        margin: 0;
                        padding: 0;
                    }
                    .card-print {
                        box-shadow: none;
                        border: 1px solid #eee; /* Light border for print */
                        margin: 20px auto; /* Center on print page */
                    }
                    .card-header-print {
                        background: ${primaryColor} !important; /* Ensure solid color for print */
                        -webkit-print-color-adjust: exact; /* For WebKit browsers */
                        color-adjust: exact; /* Standard */
                    }
                    .card-header-print::before {
                        content: none; /* Remove pattern for print */
                    }
                }
            </style>
        `;

        const win = window.open('', '', 'height=600,width=500');
        win.document.write('<html><head><title>Print Product Barcode</title>');
        win.document.write('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">'); // Include font
        win.document.write(styles);
        win.document.write('</head><body>');
        win.document.write(printContents);
        win.document.write('</body></html>');
        win.document.close();
        win.focus();
        win.print();
        // win.close(); // Optionally close the window after print dialog is shown
    }

    // Use event delegation to handle clicks on dynamically loaded modal buttons
    $(document).on('click', '.print-barcode-btn', function() {
        const barcodeValue = $(this).data('barcode');
        const productName = $(this).data('product-name');
        const barcodeImageBase64 = $(this).data('barcode-image');
        const primaryColor = $(this).data('primary-color');

        printProductBarcode(barcodeValue, productName, barcodeImageBase64, primaryColor);
    });
</script>
@endsection
