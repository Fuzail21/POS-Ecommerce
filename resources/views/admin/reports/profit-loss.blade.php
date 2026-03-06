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
                    <form method="GET" action="{{ route('reports.profit-loss') }}" class="form-row align-items-end mb-4">
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
                            <button type="submit" class="btn btn-primary">Calculate</button>
                            <a href="{{ route('reports.profit-loss') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>

                    <p class="text-muted mb-4">
                        Period: <strong>{{ $start->format('d M Y') }}</strong> &ndash; <strong>{{ $end->format('d M Y') }}</strong>
                        @if(request('branch_id'))
                            &nbsp;&mdash; Branch filter applies to Sales &amp; Expenses only
                            <small class="d-block text-warning">(Purchases are warehouse-based and are shown for all warehouses)</small>
                        @endif
                    </p>

                    {{-- P&L Summary Cards --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white p-3 text-center">
                                <h6 class="text-uppercase">Total Revenue</h6>
                                <h4>{{ $setting->currency_symbol ?? '$' }} {{ number_format($totalRevenue, 2) }}</h4>
                                <small>From POS Sales</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white p-3 text-center">
                                <h6 class="text-uppercase">Cost of Goods</h6>
                                <h4>{{ $setting->currency_symbol ?? '$' }} {{ number_format($totalPurchases, 2) }}</h4>
                                <small>From Purchases</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white p-3 text-center">
                                <h6 class="text-uppercase">Operating Expenses</h6>
                                <h4>{{ $setting->currency_symbol ?? '$' }} {{ number_format($totalExpenses, 2) }}</h4>
                                <small>From Expenses</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} text-white p-3 text-center">
                                <h6 class="text-uppercase">Net Profit / Loss</h6>
                                <h4>{{ $setting->currency_symbol ?? '$' }} {{ number_format(abs($netProfit), 2) }}</h4>
                                <small>{{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- Detailed P&L Table --}}
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-right">Amount ({{ $setting->currency_symbol ?? '$' }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="font-weight-bold bg-light">
                                <td>Total Revenue (Sales)</td>
                                <td class="text-right text-success">{{ number_format($totalRevenue, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cost of Goods Sold (Purchases)</td>
                                <td class="text-right text-danger">- {{ number_format($totalPurchases, 2) }}</td>
                            </tr>
                            <tr class="font-weight-bold">
                                <td>Gross Profit</td>
                                <td class="text-right {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($grossProfit, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Operating Expenses</td>
                                <td class="text-right text-danger">- {{ number_format($totalExpenses, 2) }}</td>
                            </tr>
                            <tr class="font-weight-bold" style="background: {{ $netProfit >= 0 ? '#d4edda' : '#f8d7da' }}">
                                <td><strong>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</strong></td>
                                <td class="text-right {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ $setting->currency_symbol ?? '$' }} {{ number_format(abs($netProfit), 2) }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
