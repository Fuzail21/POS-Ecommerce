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
                            <a href="{{ route('purchases.list') }}" class="btn btn-secondary btn-sm">
                                View Purchases
                            </a>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success mx-3 mt-3">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger mx-3 mt-3">{{ session('error') }}</div>
                        @endif

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Purchase Invoice</th>
                                            <th>Supplier</th>
                                            <th>Warehouse</th>
                                            <th>Return Date</th>
                                            <th>Items</th>
                                            <th>Total Returned</th>
                                            <th>Refunded</th>
                                            <th>Payment Method</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($returns as $return)
                                            <tr>
                                                <td>{{ $return->id }}</td>
                                                <td>
                                                    <a href="{{ route('purchases.invoice', $return->purchase_id) }}">
                                                        {{ $return->purchase->invoice_number ?? 'N/A' }}
                                                    </a>
                                                </td>
                                                <td>{{ $return->supplier->name ?? 'N/A' }}</td>
                                                <td>{{ $return->warehouse->name ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</td>
                                                <td>{{ $return->items_count }}</td>
                                                <td>{{ number_format($return->total_return_amount, 2) }}</td>
                                                <td>{{ number_format($return->refund_amount, 2) }}</td>
                                                <td>{{ ucfirst($return->payment_method ?? '-') }}</td>
                                                <td>
                                                    <a href="{{ route('purchase_returns.show', $return->id) }}"
                                                       class="btn btn-info btn-sm">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">No purchase returns found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $returns->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
