@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">{{ $title }}</h4>
                    </div>
                </div>
                <div class="iq-card-body">

                    @include('admin.reports._nav')

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('reports.inventory-valuation') }}" class="form-row align-items-end mb-4">
                        <div class="col-md-4">
                            <label>Warehouse</label>
                            <select name="warehouse_id" class="form-control">
                                <option value="">All Warehouses</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ $warehouseId == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mt-2 mt-md-0">
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                        </div>
                        <div class="col-md-2 mt-2 mt-md-0">
                            <a href="{{ route('reports.inventory-valuation') }}" class="btn btn-secondary btn-block">Reset</a>
                        </div>
                    </form>

                    {{-- Summary Card --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-left-success shadow">
                                <div class="card-body py-3">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Inventory Value</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $setting->currency_symbol ?? '$' }} {{ number_format($grandTotal, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-info shadow">
                                <div class="card-body py-3">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total SKUs</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stocks->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Variant</th>
                                    <th>Category</th>
                                    <th>Warehouse</th>
                                    <th class="text-right">Qty (Base Unit)</th>
                                    <th class="text-right">Unit Cost</th>
                                    <th class="text-right">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $i => $stock)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        {{ $stock->product->name ?? 'N/A' }}
                                        @if($stock->product->sku)
                                            <br><small class="text-muted">{{ $stock->product->sku }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $stock->variant->variant_name ?? '—' }}</td>
                                    <td>{{ $stock->product->category->name ?? '—' }}</td>
                                    <td>{{ $stock->warehouse->name ?? '—' }}</td>
                                    <td class="text-right">{{ number_format($stock->quantity_in_base_unit, 2) }}</td>
                                    <td class="text-right">{{ $setting->currency_symbol ?? '$' }} {{ number_format($stock->unit_cost, 2) }}</td>
                                    <td class="text-right"><strong>{{ $setting->currency_symbol ?? '$' }} {{ number_format($stock->total_value, 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No stock records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($stocks->count() > 0)
                            <tfoot>
                                <tr class="table-dark">
                                    <th colspan="7" class="text-right">Grand Total</th>
                                    <th class="text-right">{{ $setting->currency_symbol ?? '$' }} {{ number_format($grandTotal, 2) }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
