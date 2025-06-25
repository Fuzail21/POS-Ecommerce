@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            @php
                use App\Models\Setting;
                $setting = Setting::first();
                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
            @endphp
            <div class="col-lg-12 mb-4">
                <div class="card p-4 shadow-sm w-100">
                    <h5 class="card-title mb-3">Filter Stock Ledger</h5>
                    <form method="GET" action="{{ route('stock.ledger') }}" class="row g-3 align-items-end">  
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Date From</label>
                            <input 
                                type="date" 
                                name="date_from" 
                                id="date_from"
                                class="form-control" 
                                value="{{ request('date_from') }}"
                            >
                        </div>
                
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Date To</label>
                            <input 
                                type="date" 
                                name="date_to" 
                                id="date_to"
                                class="form-control" 
                                value="{{ request('date_to') }}"
                            >
                        </div>
                
                        <div class="col-md-2">
                            <label for="product" class="form-label">Product</label>
                            <input 
                                type="text" 
                                name="product" 
                                id="product"
                                class="form-control" 
                                placeholder="Enter product" 
                                value="{{ request('product') }}"
                            >
                        </div>
                
                        {{-- <div class="col-md-2">
                            <label for="name" class="form-label">Name</label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name"
                                class="form-control" 
                                placeholder="Enter name" 
                                value="{{ request('name') }}"
                            >
                        </div> --}}
                
                        <div class="col-md-2">
                            <label for="ref_type" class="form-label">Ref Type</label>
                            <input 
                                type="text" 
                                name="ref_type" 
                                id="ref_type"
                                class="form-control" 
                                placeholder="Enter ref type" 
                                value="{{ request('ref_type') }}"
                            >
                        </div>
                
                        <div class="col-md-2">
                            <label for="warehouse" class="form-label">Warehouse</label>
                            <input 
                                type="text" 
                                name="warehouse" 
                                id="warehouse"
                                class="form-control" 
                                placeholder="Enter warehouse" 
                                value="{{ request('warehouse') }}"
                            >
                        </div>
                
                        <div class="col-md-2 d-grid mt-2">
                            <button type="submit" class="btn text-white" style="background-color: {{ $primaryColor }};">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">

                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="card-header">
                        <h4 class="card-title">Stock Ledger</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead style="background-color: #F4F5FA;">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Warehouse</th>
                                        <th>Ref Type</th>
                                        <th>Qty</th>
                                        <th>Unit Cost</th>
                                        <th>Direction</th>
                                        <th>Ref ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ledgers as $index => $ledger)
                                    <tr>
                                        <td>{{ $ledgers->firstItem() + $index }}</td>
                                        <td>{{ $ledger->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $ledger->product->name ?? '-' }}</td>
                                        <td>{{ $ledger->variant->variant_name ?? '-' }}</td>
                                        <td>{{ $ledger->warehouse->name ?? '-' }}</td>
                                        <td>{{ ucfirst($ledger->ref_type) }}</td>
                                        <td>
                                            @if (in_array($ledger->ref_type, ['purchase', 'sale']))
                                            {{ $ledger->ref_type === 'purchase' ? '+' : '-' }}
                                            {{ number_format($ledger->converted_qty, 0) }}
                                            {{ $ledger->unit_name }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                        <td>{{ number_format($ledger->unit_cost, 0) ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $ledger->direction === 'in' ? 'success' : 'danger' }}">
                                                {{ strtoupper($ledger->direction) }}
                                            </span>
                                        </td>
                                        <td>{{ $ledger->ref_id }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="11">No stock transactions found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $ledgers->appends(['type' => request('type')])->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
