@extends('layouts.app')

@section('content')

    @include('layouts.sidebar')

    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h5>{{ isset($product) ? 'Edit' : 'Add' }} Product</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" enctype="multipart/form-data">
                                @csrf   

                                {{-- Product Name --}}
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control" required>
                                </div>

                                {{-- Category --}}
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Base Unit --}}
                                <div class="form-group">
                                    <label>Base Unit</label>
                                    <select name="base_unit_id" class="form-control" required>
                                        <option value="">Select Base Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ (old('base_unit_id', $product->base_unit_id ?? '') == $unit->id) ? 'selected' : '' }}>
                                                {{ $unit->base_unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- SKU --}}
                                <div class="form-group">
                                    <label>SKU</label>
                                    <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}">
                                </div>

                                {{-- Barcode --}}
                                <div class="form-group" id="main-barcode">
                                    <label>Barcode</label>
                                    <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode ?? '') }}">
                                </div>

                                {{-- Brand --}}
                                <div class="form-group">
                                    <label>Brand</label>
                                    <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand ?? '') }}">
                                </div>

                                {{-- Sale Price --}}
                                <div class="form-group">
                                    <label>Sale Price</label>
                                    <input type="number" name="sale_price" step="0.01" class="form-control" value="{{ old('sale_price', $product->sale_price ?? '') }}">
                                </div>

                                {{-- Has Variants --}}
                                <div class="form-group">
                                    <label for="has_variance">Has Variant</label>
                                    <select id="has_variance" name="has_variants" class="form-control" required>
                                        <option value="1" {{ old('has_variants', $product->has_variants ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('has_variants', $product->has_variants ?? '') == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                {{-- Variant Section --}}
                                <div id="variant-section" style="display: none;">
                                    <h5 class="mb-3">Product Variants</h5>
                                    <table class="table table-bordered" id="variant-table">
                                        <thead>
                                            <tr>
                                                <th>Variant Name</th>
                                                <th>SKU</th>
                                                <th>Barcode</th>
                                                <th>Sale Price</th>
                                                <th>Image</th>
                                                <th>
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="addVariantRow()">Add</button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($product->variants))
                                                @foreach($product->variants as $variant)
                                                    <tr>
                                                        <td><input type="text" name="variants[{{ $loop->index }}][variant_name]" value="{{ $variant->variant_name }}" class="form-control" required></td>
                                                        <td><input type="text" name="variants[{{ $loop->index }}][sku]" value="{{ $variant->sku }}" class="form-control" required></td>
                                                        <td><input type="text" name="variants[{{ $loop->index }}][barcode]" value="{{ $variant->barcode }}" class="form-control"></td>
                                                        <td><input type="number" step="0.01" name="variants[{{ $loop->index }}][sale_price]" value="{{ $variant->sale_price }}" class="form-control"></td>
                                                        <td>
                                                            <input type="file" name="variants[{{ $loop->index }}][product_img]" class="form-control-file">
                                                            @if(!empty($variant->product_img))
                                                                <img src="{{ asset('storage/' . $variant->product_img) }}" alt="Variant Image" class="mt-2" width="80">
                                                            @endif
                                                        </td>
                                                        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeVariantRow(this)">Remove</button></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Track Expiry --}}
                                <div class="form-group">
                                    <label>Track Expiry</label>
                                    <select name="track_expiry" class="form-control" required>
                                        <option value="1" {{ old('track_expiry', $product->track_expiry ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('track_expiry', $product->track_expiry ?? '') == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                {{-- Tax Rate --}}
                                <div class="form-group">
                                    <label>Tax Rate (%)</label>
                                    <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ old('tax_rate', $product->tax_rate ?? '') }}" required>
                                </div>

                                {{-- Product Image --}}
                                <div class="form-group">
                                    <label>Product Image</label>
                                    <input type="file" name="product_img" class="form-control-file" accept=".jpg, .jpeg, .png, .webp">
                                    @if(!empty($product->product_img))
                                        <img src="{{ asset('storage/' . $product->product_img) }}" alt="Product Image" class="mt-2" width="100">
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update' : 'Save' }}</button>
                                <a href="{{ route('products.list') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const hasVariance = document.getElementById('has_variance');
            const variantSection = document.getElementById('variant-section');
            const mainBarcode = document.getElementById('main-barcode');

            function toggleVariantSection() {
                if (hasVariance.value === '1') {
                    variantSection.style.display = 'block';
                    mainBarcode.style.display = 'none';
                } else {
                    variantSection.style.display = 'none';
                    mainBarcode.style.display = 'block';
                }
            }

            hasVariance.addEventListener('change', toggleVariantSection);
            toggleVariantSection(); // initial load
        });

        let variantIndex = {{ isset($product->variants) ? $product->variants->count() : 0 }};

        function addVariantRow() {
            const tableBody = document.querySelector('#variant-table tbody');
            const row = document.createElement('tr');

            row.innerHTML = `
                <td><input type="text" name="variants[${variantIndex}][variant_name]" class="form-control" required></td>
                <td><input type="text" name="variants[${variantIndex}][sku]" class="form-control" required></td>
                <td><input type="text" name="variants[${variantIndex}][barcode]" class="form-control"></td>
                <td><input type="number" step="0.01" name="variants[${variantIndex}][sale_price]" class="form-control"></td>
                <td>
                    <input type="file" name="variants[${variantIndex}][product_img]" class="form-control-file">
                    <div class="img-preview mt-2"></div>
                </td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeVariantRow(this)">Remove</button></td>
            `;

            tableBody.appendChild(row);
            variantIndex++;
        }


        function removeVariantRow(button) {
            button.closest('tr').remove();
        }
    </script>
@endsection
