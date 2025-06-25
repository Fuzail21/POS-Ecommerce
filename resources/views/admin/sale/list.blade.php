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
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Sales List</h4>
                    </div>
                    <a href="{{ route('sales.create') }}" class="btn text-white add-list" style="background-color: {{ $primaryColor }};">
                        <i class="las la-plus mr-3"></i>Add Sale
                    </a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Sales</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead style="background-color: #F4F5FA;">
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice No</th>
                                        <th>Supplier</th>
                                        <th>Branch</th>
                                        <th>Purchase Date</th>
                                        <th>Grand Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sales as $index => $sale)
                                        <tr>
                                            <td>{{ $sale->id }}</td>
                                            <td>{{ $sale->invoice_number }}</td>
                                            <td>{{ $sale->customer->name ?? '-' }}</td>
                                            <td>{{ $sale->branch->name ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                                            <td>{{ $setting->currency_symbol }} {{ number_format($sale->total_amount, 2) }}</td>
                                            <td>{{ $setting->currency_symbol }} {{ number_format($sale->paid_amount, 2) }}</td>
                                            <td class="{{ $sale->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $setting->currency_symbol }} {{ number_format($sale->due_amount, 2) }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="badge text-white mr-2 p-1 border-0" style="background-color: #FF723D;" data-toggle="tooltip" title="Delete">
                                                            <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                        </button>
                                                    </form>

                                                    <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Invoice" href="{{ route('sales.invoice', $sale->id) }}">
                                                        <i class="ri-file-list-3-line" style="font-size: 1.1rem;"></i>
                                                    </a>

                                                    <a class="badge bg-primary mr-2 p-1 {{ $sale->sales_returns_count > 0 ? 'pointer-events-none opacity-50' : '' }}"
                                                       data-toggle="tooltip"
                                                       data-placement="top"
                                                       title="{{ $sale->sales_returns_count > 0 ? 'Return already processed' : 'Create Sale Return' }}"
                                                       href="{{ $sale->sales_returns_count > 0 ? '#' : route('sale_return.create', $sale->id) }}">
                                                        <i class="ri-refresh-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No Sales found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end mt-3">
                            {{ $sales->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
