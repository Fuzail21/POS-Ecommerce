@extends('layouts.app')

@section('style')
    {{-- Add any specific styles for this page here --}}
@endendsection

@section('content')

@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Sales Return Details: RET-{{ $salesReturn->id }}</h4>
                    </div>
                    <a href="{{ route('sale_return.list') }}" class="btn btn-primary add-list">
                        <i class="ri-arrow-left-line mr-3"></i>Back to Sales Returns List
                    </a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Return Summary</h4>
                        </div>
                        <div class="card-action">
                            {{-- Optional action buttons, e.g., Print, Edit Return Reason --}}
                            {{-- <a href="#" class="btn btn-sm btn-outline-primary">Print Return Receipt</a> --}}
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Return ID:</strong> RET-{{ $salesReturn->id }}</p>
                                <p><strong>Return Date:</strong> {{ $salesReturn->return_date ? $salesReturn->return_date->format('d M Y H:i') : 'N/A' }}</p>
                                <p><strong>Customer:</strong> {{ $salesReturn->customer->name ?? 'N/A' }}</p>
                                <p><strong>Branch:</strong> {{ $salesReturn->branch->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Original Sale:</strong>
                                    @if($salesReturn->sale)
                                        <a href="{{ route('sales.invoice', $salesReturn->sale->id) }}" title="View Original Sale Invoice">
                                            {{ $salesReturn->sale->invoice_number }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                                <p><strong>Total Returned Amount:</strong> ${{ number_format($salesReturn->total_return_amount, 2) }}</p>
                                <p><strong>Refund Amount:</strong> ${{ number_format($salesReturn->refund_amount, 2) }}</p>
                                <p><strong>Payment Method:</strong> {{ ucfirst($salesReturn->payment_method) }}</p>
                            </div>
                        </div>
                        <hr>
                        <p><strong>Return Reason:</strong> {{ $salesReturn->return_reason ?? 'No reason provided.' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Returned Items</h4>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead style="background-color: #F4F5FA;">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Original Sale Price</th>
                                        <th>Returned Quantity</th>
                                        <th>Quantity in Base Unit</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesReturn->salesReturnItems as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($item->variant)
                                                    {{ $item->variant->product->name ?? 'N/A Product' }} - {{ $item->variant->variant_name ?? 'N/A Variant' }}
                                                @elseif($item->product)
                                                    {{ $item->product->name ?? 'N/A Product' }}
                                                @else
                                                    Unknown Product
                                                @endif
                                            </td>
                                            <td>${{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ number_format($item->quantity, 2) }} ({{ $item->unit->name ?? 'N/A Unit' }})</td>
                                            <td>{{ number_format($item->quantity, 0) }}</td>
                                            <td>${{ number_format($item->total_price, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No items found for this return.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div> {{-- End row --}}
    </div> {{-- End container-fluid --}}
</div> {{-- End content-page --}}

@endsection

@push('scripts')
    {{-- Any page-specific JavaScript can go here --}}
@endpush
