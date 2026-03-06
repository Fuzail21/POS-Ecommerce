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
                                <h4 class="card-title">{{ $title }}</h4>
                            </div>
                            <a href="{{ route('stock.list') }}" class="btn btn-secondary btn-sm">
                                Back to Inventory
                            </a>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger mx-3 mt-3">{{ session('error') }}</div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success mx-3 mt-3">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger mx-3 mt-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('stock.adjustment.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    {{-- Product --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                        <select name="product_id" id="product_id" class="form-control" required>
                                            <option value="">-- Select Product --</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-has-variants="{{ $product->variants->isNotEmpty() ? '1' : '0' }}"
                                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                    @if ($product->sku) ({{ $product->sku }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Variant (shown only when product has variants) --}}
                                    <div class="col-md-6 mb-3" id="variant_wrapper" style="display:none;">
                                        <label for="variant_id" class="form-label">Variant</label>
                                        <select name="variant_id" id="variant_id" class="form-control">
                                            <option value="">-- Select Variant --</option>
                                        </select>
                                    </div>

                                    {{-- Warehouse --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="warehouse_id" class="form-label">Warehouse <span class="text-danger">*</span></label>
                                        <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                            <option value="">-- Select Warehouse --</option>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Quantity (positive = add, negative = remove) --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="quantity" class="form-label">
                                            Quantity Adjustment <span class="text-danger">*</span>
                                            <small class="text-muted">(positive to add stock, negative to remove)</small>
                                        </label>
                                        <input type="number" name="quantity" id="quantity" step="0.01"
                                               class="form-control" value="{{ old('quantity') }}"
                                               placeholder="e.g. 10 or -5" required>
                                    </div>

                                    {{-- Reason --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                        <textarea name="reason" id="reason" rows="3" maxlength="500"
                                                  class="form-control" placeholder="Enter reason for adjustment..." required>{{ old('reason') }}</textarea>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
                                    <a href="{{ route('stock.list') }}" class="btn btn-light">Cancel</a>
                                </div>
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
    // Product variants data map: productId → [{id, name}]
    const variantMap = {
        @foreach ($products as $product)
            @if ($product->variants->isNotEmpty())
                {{ $product->id }}: [
                    @foreach ($product->variants as $variant)
                        { id: {{ $variant->id }}, name: "{{ addslashes($variant->variant_name) }}" },
                    @endforeach
                ],
            @endif
        @endforeach
    };

    const productSelect   = document.getElementById('product_id');
    const variantWrapper  = document.getElementById('variant_wrapper');
    const variantSelect   = document.getElementById('variant_id');

    productSelect.addEventListener('change', function () {
        const productId = parseInt(this.value);
        const variants  = variantMap[productId] || [];

        // Reset variant select
        variantSelect.innerHTML = '<option value="">-- Select Variant --</option>';

        if (variants.length > 0) {
            variants.forEach(function (v) {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = v.name;
                variantSelect.appendChild(opt);
            });
            variantWrapper.style.display = 'block';
        } else {
            variantWrapper.style.display = 'none';
        }
    });

    // Restore variant state on page reload (validation error)
    @if (old('product_id') && old('variant_id'))
        (function () {
            productSelect.dispatchEvent(new Event('change'));
            // After options are rendered, set the old value
            setTimeout(function () {
                variantSelect.value = "{{ old('variant_id') }}";
            }, 50);
        })();
    @endif
</script>
@endsection
