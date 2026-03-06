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
                    <form method="GET" action="{{ route('reports.sales') }}" class="form-row align-items-end mb-4">
                        <div class="col-md-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $start->toDateString()) }}">
                        </div>
                        <div class="col-md-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $end->toDateString()) }}">
                        </div>
                        <div class="col-md-3">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('reports.sales') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>

                    {{-- Summary --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white p-3 text-center">
                                <h5>Total Sales</h5>
                                <h3>{{ $setting->currency_symbol ?? '$' }} {{ number_format($total, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white p-3 text-center">
                                <h5>Total Invoices</h5>
                                <h3>{{ $sales->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white p-3 text-center">
                                <h5>Period</h5>
                                <h5>{{ $start->format('d M Y') }} &ndash; {{ $end->format('d M Y') }}</h5>
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Branch</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $i => $sale)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $sale->invoice_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                    <td>{{ $sale->branch->name ?? '-' }}</td>
                                    <td>{{ $setting->currency_symbol ?? '$' }} {{ number_format($sale->final_amount, 2) }}</td>
                                    <td><span class="badge badge-secondary">{{ ucfirst($sale->status ?? 'N/A') }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No sales found for the selected period.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($sales->count() > 0)
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="5" class="text-right">Grand Total:</td>
                                    <td>{{ $setting->currency_symbol ?? '$' }} {{ number_format($total, 2) }}</td>
                                    <td></td>
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
