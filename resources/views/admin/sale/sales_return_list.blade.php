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
                        <h4 class="mb-3">{{ $title }}</h4> {{-- Uses the $title passed from controller --}}
                    </div>
                    <a href="{{ route('sales.list') }}" class="btn text-white add-list" style="background-color: {{ $primaryColor }};">
                        <i class="ri-arrow-left-line mr-3"></i>Back to Sales
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
                            <h4 class="card-title">Sales Returns</h4> {{-- Or more dynamic: {{ $title }} --}}
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead style="background-color: #F4F5FA;">
                                    <tr>
                                        <th>#</th>
                                        <th>Return ID</th>
                                        <th>Original Sale</th>
                                        <th>Customer</th>
                                        <th>Branch</th>
                                        <th>Return Date</th>
                                        <th>Total Returned Amount</th>
                                        <th>Refund Amount</th>
                                        <th>Payment Method</th>
                                        <th>Items Returned</th>
                                        <th>Returned By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesReturns as $salesReturn)
                                        <tr>
                                            {{-- Use $loop->iteration for sequential numbering --}}
                                            <td>{{ $loop->iteration + ($salesReturns->currentPage() - 1) * $salesReturns->perPage() }}</td>
                                            <td>RET-{{ $salesReturn->id }}</td>
                                            <td>
                                                @if ($salesReturn->sale)
                                                    <a href="{{ route('sales.invoice', $salesReturn->sale->id) }}" title="View Original Sale Invoice">
                                                        {{ $salesReturn->sale->invoice_number ?? 'N/A' }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $salesReturn->customer->name ?? 'N/A' }}</td>
                                            <td>{{ $salesReturn->branch->name ?? 'N/A' }}</td>
                                            {{-- Ensure 'return_date' is cast to datetime in SalesReturn model --}}
                                            <td>{{ $salesReturn->return_date ? $salesReturn->return_date->format('d M Y') : 'N/A' }}</td>
                                            <td>{{ $setting->currency_symbol ?? '$' }} {{ number_format($salesReturn->total_return_amount, 2) }}</td>
                                            <td>{{ $setting->currency_symbol ?? '$' }} {{ number_format($salesReturn->refund_amount, 2) }}</td>
                                            <td>{{ ucfirst($salesReturn->payment_method) }}</td>
                                            <td>{{ $salesReturn->sales_return_items_count }}</td> {{-- Count from withCount --}}
                                            <td>{{ $salesReturn->user->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    {{-- View Details Button --}}
                                                    <a href="{{ route('sale_return.show', $salesReturn->id) }}" class="badge bg-info mr-2 p-1" data-toggle="tooltip" data-placement="top" title="View Return Details">
                                                        <i class="ri-eye-line" style="font-size: 1.1rem;"></i>
                                                    </a>

                                                    {{-- Add other actions here if needed (e.g., Print, Delete) --}}
                                                    {{-- Example Delete Button (adjust route and logic as needed) --}}
                                                    {{-- <form action="{{ route('sale_return.destroy', $salesReturn->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this return? This action cannot be undone.')" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="badge bg-danger mr-2 p-1 border-0" data-toggle="tooltip" data-placement="top" title="Delete Return">
                                                            <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                        </button>
                                                    </form> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center">No sales returns found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end mt-3">
                            {{ $salesReturns->links('pagination::bootstrap-5') }}
                        </div>

                    </div> {{-- End card-body --}}
                </div> {{-- End card --}}
            </div> {{-- End col-sm-12 --}}

        </div> {{-- End row --}}
    </div> {{-- End container-fluid --}}
</div> {{-- End content-page --}}

@endsection
