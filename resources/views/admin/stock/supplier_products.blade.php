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
                                <h5>Supplier Product</h5>
                            </div>
                        </div>
                            @php
                                use App\Models\Setting;
                                $setting = Setting::first();
                                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
                            @endphp

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        <div class="card-body">
                            <form method="GET" action="{{ route('reports.supplier_products') }}" class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label>Supplier</label>
                                    <select name="supplier_id" class="form-control" required>
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ $selectedSupplier == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Filter</label>
                                    <select name="filter" class="form-control">
                                        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>All Products</option>
                                        <option value="low_stock" {{ $filter == 'low_stock' ? 'selected' : '' }}>Low Stock Only</option>
                                    </select>
                                </div>

                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary" style="background-color: {{ $primaryColor }}; border-color: {{ $primaryColor }};">Filter</button>
                                </div>
                            </form>

                            @if($products->count())

                                <div class="row mb-3">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button onclick="printTable()" class="btn text-white" style="background-color: {{ $primaryColor }};">
                                            <i class="fas fa-print"></i> Print Report
                                        </button>
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Current Stock</th>
                                            <th>Low Stock Threshold</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            @php
                                                $baseConversionFactor = $product->baseUnit->conversion_factor ?? 1;
                                            @endphp

                                            @if($product->has_variants && $product->variants->count())
                                                @foreach($product->variants as $variant)
                                                    @php
                                                        $stock = $variant->inventoryStocks->sum('quantity_in_base_unit');
                                                        $actualStock = $baseConversionFactor > 0 ? ($stock / $baseConversionFactor) : 0;
                                                        $lowStockThreshold = $variant->low_stock ?? ($product->low_stock ?? 0);
                                                        $isLowStock = $actualStock <= $lowStockThreshold;
                                                    @endphp
                                                    <tr class="{{ $isLowStock ? 'table-warning' : '' }}">
                                                        <td>{{ $product->name }} ({{ $variant->variant_name }})</td>
                                                        <td>{{ $variant->sku }}</td>
                                                        <td>
                                                            {{ number_format($actualStock, 0) }} {{ $product->baseUnit->name ?? '' }}
                                                            @if ($variant->inventoryStocks->isEmpty())
                                                                <span class="text-danger">(No Record)</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($lowStockThreshold, 0) }}</td>
                                                        <td>
                                                            @if($isLowStock)
                                                                <span class="badge badge-danger">Low Stock</span>
                                                            @else
                                                                <span class="badge bg-success">OK</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                @php
                                                    $stock = $product->inventoryStocks->sum('quantity_in_base_unit');
                                                    $actualStock = $baseConversionFactor > 0 ? ($stock / $baseConversionFactor) : 0;
                                                    $lowStockThreshold = $product->low_stock ?? 0;
                                                    $isLowStock = $actualStock <= $lowStockThreshold;
                                                @endphp
                                                <tr class="{{ $isLowStock ? 'table-warning' : '' }}">
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>
                                                        {{ number_format($actualStock, 0) }} {{ $product->baseUnit->name ?? '' }}
                                                        @if ($product->inventoryStocks->isEmpty())
                                                            <span class="text-danger">(No Record)</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($lowStockThreshold, 0) }}</td>
                                                    <td>
                                                        @if($isLowStock)
                                                            <span class="badge badge-danger">Low Stock</span>
                                                        @else
                                                            <span class="badge bg-success">OK</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No products found for the selected supplier or filter criteria.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
  function printTable() {
        const printContents = document.querySelector('.table').outerHTML;
        const printWindow = window.open('', '', 'height=600,width=1000');
        printWindow.document.write('<html><head><title>Supplier Product Report</title>');
        printWindow.document.write('<style>table{width:100%;border-collapse:collapse} th, td{border:1px solid #ccc;padding:8px;text-align:left} th{background:#f2f2f2}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }
</script>


@endsection