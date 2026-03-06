@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between align-items-center">
                    <div class="iq-header-title">
                        <h4 class="card-title">{{ $title }}</h4>
                    </div>
                    <a href="{{ route('stock.transfers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>

                <div class="iq-card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('stock.transfers.store') }}" method="POST">
                        @csrf

                        {{-- Product & Variant --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Product <span class="text-danger">*</span></label>
                                <select name="product_id" id="product_id" class="form-control" required>
                                    <option value="">— Select Product —</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-variants='@json($product->variants)'
                                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6" id="variant-group" style="display:none;">
                                <label>Variant</label>
                                <select name="variant_id" id="variant_id" class="form-control">
                                    <option value="">— No Variant —</option>
                                </select>
                            </div>
                        </div>

                        {{-- From Warehouse --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>From Warehouse <span class="text-danger">*</span>
                                    <small class="text-muted" id="from-hint">(select a product first)</small>
                                </label>
                                <select name="from_warehouse_id" id="from_warehouse_id" class="form-control" required disabled>
                                    <option value="">— Select Source —</option>
                                </select>
                                <small id="available-stock" class="text-info font-weight-bold mt-1 d-block"></small>
                            </div>

                            {{-- Destination Type Toggle --}}
                            <div class="form-group col-md-6">
                                <label>Transfer To <span class="text-danger">*</span></label>
                                <div class="d-flex mb-2">
                                    <div class="custom-control custom-radio mr-4">
                                        <input type="radio" id="dest_warehouse" name="dest_type" value="warehouse"
                                               class="custom-control-input" checked>
                                        <label class="custom-control-label" for="dest_warehouse">
                                            <i class="fas fa-warehouse mr-1"></i> Warehouse
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="dest_branch" name="dest_type" value="branch"
                                               class="custom-control-input">
                                        <label class="custom-control-label" for="dest_branch">
                                            <i class="fas fa-store mr-1"></i> Branch
                                        </label>
                                    </div>
                                </div>

                                {{-- Warehouse destination --}}
                                <div id="to-warehouse-group">
                                    <select name="to_warehouse_id" id="to_warehouse_id" class="form-control" required>
                                        <option value="">— Select Destination Warehouse —</option>
                                        @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}"
                                                    {{ old('to_warehouse_id') == $w->id ? 'selected' : '' }}>
                                                {{ $w->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Branch destination --}}
                                <div id="to-branch-group" style="display:none;">
                                    <select id="to_branch_id" class="form-control">
                                        <option value="">— Select Destination Branch —</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                    data-warehouse-id="{{ $branch->warehouse_id }}"
                                                    data-warehouse-name="{{ $branch->warehouse->name ?? 'N/A' }}">
                                                {{ $branch->name }}
                                                ({{ $branch->warehouse->name ?? 'No Warehouse' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted mt-1 d-block" id="branch-warehouse-hint"></small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Quantity to Transfer <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control"
                                   step="0.01" min="0.01"
                                   value="{{ old('quantity') }}" required
                                   placeholder="Enter quantity in base unit">
                            <small id="qty-hint" class="text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Optional transfer notes">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-exchange-alt mr-1"></i> Execute Transfer
                        </button>
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
(function () {
    const stockMap      = @json($stockMap);
    const allWarehouses = @json($warehouses->map(fn($w) => ['id' => $w->id, 'name' => $w->name]));

    const productSelect  = document.getElementById('product_id');
    const variantSelect  = document.getElementById('variant_id');
    const variantGroup   = document.getElementById('variant-group');
    const fromSelect     = document.getElementById('from_warehouse_id');
    const toWarehouseSelect = document.getElementById('to_warehouse_id');
    const toBranchSelect    = document.getElementById('to_branch_id');
    const fromHint       = document.getElementById('from-hint');
    const availableStock = document.getElementById('available-stock');
    const qtyInput       = document.getElementById('quantity');
    const qtyHint        = document.getElementById('qty-hint');
    const branchHint     = document.getElementById('branch-warehouse-hint');
    const toWarehouseGroup = document.getElementById('to-warehouse-group');
    const toBranchGroup    = document.getElementById('to-branch-group');
    const destRadios       = document.querySelectorAll('input[name="dest_type"]');

    // --- Destination type toggle ---
    destRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'branch') {
                toWarehouseGroup.style.display = 'none';
                toBranchGroup.style.display    = 'block';
                toWarehouseSelect.removeAttribute('required');
                toWarehouseSelect.value = '';
                setBranchWarehouse();
            } else {
                toWarehouseGroup.style.display = 'block';
                toBranchGroup.style.display    = 'none';
                toWarehouseSelect.setAttribute('required', 'required');
                branchHint.textContent = '';
                rebuildFromWarehouseWithSync();
            }
        });
    });

    // When branch is selected, resolve its warehouse and update From Warehouse sync
    toBranchSelect.addEventListener('change', function() {
        setBranchWarehouse();
        rebuildFromWarehouseWithSync();
    });

    function setBranchWarehouse() {
        const opt = toBranchSelect.options[toBranchSelect.selectedIndex];
        if (opt && opt.dataset.warehouseId) {
            const whId   = opt.dataset.warehouseId;
            const whName = opt.dataset.warehouseName;
            branchHint.textContent  = 'Stock will be moved to warehouse: ' + whName;
            // Set the hidden select so the same field name submits the correct warehouse id
            toWarehouseSelect.value = whId;
            syncFromWarehouse(parseInt(whId));
        } else {
            branchHint.textContent  = '';
            toWarehouseSelect.value = '';
            syncFromWarehouse(null);
        }
    }

    function getDestWarehouseId() {
        const destType = document.querySelector('input[name="dest_type"]:checked').value;
        if (destType === 'branch') {
            const opt = toBranchSelect.options[toBranchSelect.selectedIndex];
            return (opt && opt.dataset.warehouseId) ? parseInt(opt.dataset.warehouseId) : null;
        } else {
            return toWarehouseSelect.value ? parseInt(toWarehouseSelect.value) : null;
        }
    }

    // Rebuild "From Warehouse" options to only show warehouses with stock
    function rebuildFromWarehouse(productId, variantKey) {
        fromSelect.innerHTML = '<option value="">— Select Source —</option>';
        availableStock.textContent = '';
        qtyHint.textContent = '';
        qtyInput.removeAttribute('max');
        fromSelect.disabled = true;

        if (!productId) {
            fromHint.textContent = '(select a product first)';
            return;
        }

        const productStocks = stockMap[productId] || {};
        const stocks = productStocks[variantKey] || [];

        if (stocks.length === 0) {
            fromHint.textContent = '(no stock available for this selection)';
            return;
        }

        const currentDestId = getDestWarehouseId();

        stocks.forEach(function (s) {
            if (s.qty <= 0) return;
            const wh  = allWarehouses.find(w => w.id === s.warehouse_id);
            const opt = document.createElement('option');
            opt.value        = s.warehouse_id;
            opt.dataset.qty  = s.qty;
            opt.textContent  = (wh ? wh.name : 'Warehouse #' + s.warehouse_id)
                               + '  —  ' + s.qty.toFixed(2) + ' units available';
            if (currentDestId && currentDestId === s.warehouse_id) {
                opt.disabled = true;
            }
            fromSelect.appendChild(opt);
        });

        fromSelect.disabled = false;
        fromHint.textContent = '';
    }

    function rebuildFromWarehouseWithSync() {
        const productId  = productSelect.value;
        const variantKey = variantSelect.value ? parseInt(variantSelect.value) : 0;
        rebuildFromWarehouse(productId, variantKey);
    }

    // Disable the matching destId option in From Warehouse dropdown
    function syncFromWarehouse(destId) {
        Array.from(fromSelect.options).forEach(function (opt) {
            if (opt.value) {
                opt.disabled = destId && parseInt(opt.value) === destId;
            }
        });
        if (fromSelect.value && destId && parseInt(fromSelect.value) === destId) {
            fromSelect.value = '';
            availableStock.textContent = '';
            qtyInput.removeAttribute('max');
            qtyHint.textContent = '';
        }
    }

    // On product change
    productSelect.addEventListener('change', function () {
        const productId = this.value;
        const variants  = JSON.parse(this.options[this.selectedIndex].dataset.variants || '[]');

        variantSelect.innerHTML = '<option value="">— No Variant —</option>';
        variantGroup.style.display = 'none';

        if (variants.length > 0) {
            variants.forEach(function (v) {
                const opt = document.createElement('option');
                opt.value       = v.id;
                opt.textContent = v.variant_name;
                variantSelect.appendChild(opt);
            });
            variantGroup.style.display = 'block';
            fromSelect.innerHTML = '<option value="">— Select Source —</option>';
            fromSelect.disabled  = true;
            fromHint.textContent = '(select a variant first)';
        } else {
            rebuildFromWarehouse(productId, 0);
        }
    });

    // On variant change
    variantSelect.addEventListener('change', function () {
        const productId  = productSelect.value;
        const variantKey = this.value ? parseInt(this.value) : 0;
        rebuildFromWarehouse(productId, variantKey);
    });

    // On From Warehouse change: show available qty
    fromSelect.addEventListener('change', function () {
        const selectedOpt = this.options[this.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.qty) {
            const maxQty = parseFloat(selectedOpt.dataset.qty);
            availableStock.textContent = 'Available: ' + maxQty.toFixed(2) + ' units';
            qtyInput.max      = maxQty;
            qtyHint.textContent = 'Maximum transferable: ' + maxQty.toFixed(2);
        } else {
            availableStock.textContent = '';
            qtyInput.removeAttribute('max');
            qtyHint.textContent = '';
        }
    });

    // On To Warehouse change (warehouse mode): prevent same as From
    toWarehouseSelect.addEventListener('change', function () {
        const toId = this.value ? parseInt(this.value) : null;
        syncFromWarehouse(toId);
    });
})();
</script>
@endsection
