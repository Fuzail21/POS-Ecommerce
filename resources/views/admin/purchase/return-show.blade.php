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
                            <a href="{{ route('purchase_returns.index') }}" class="btn btn-secondary btn-sm">
                                Back to Returns
                            </a>
                        </div>

                        <div class="card-body">
                            {{-- Summary --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Return ID:</th>
                                            <td>#{{ $purchaseReturn->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Purchase Invoice:</th>
                                            <td>
                                                <a href="{{ route('purchases.invoice', $purchaseReturn->purchase_id) }}">
                                                    {{ $purchaseReturn->purchase->invoice_number ?? 'N/A' }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Supplier:</th>
                                            <td>{{ $purchaseReturn->supplier->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Warehouse:</th>
                                            <td>{{ $purchaseReturn->warehouse->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Return Date:</th>
                                            <td>{{ \Carbon\Carbon::parse($purchaseReturn->return_date)->format('d M Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Total Returned:</th>
                                            <td>{{ number_format($purchaseReturn->total_return_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Refund Amount:</th>
                                            <td>{{ number_format($purchaseReturn->refund_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payment Method:</th>
                                            <td>{{ ucfirst($purchaseReturn->payment_method ?? '-') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reason:</th>
                                            <td>{{ $purchaseReturn->return_reason ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Processed By:</th>
                                            <td>{{ $purchaseReturn->createdBy->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            {{-- Items Table --}}
                            <h5 class="mb-3">Returned Items</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Unit</th>
                                            <th>Qty Returned</th>
                                            <th>Unit Cost</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchaseReturn->items as $i => $item)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>
                                                    {{ $item->product->name ?? 'N/A' }}
                                                    @if ($item->variant)
                                                        ({{ $item->variant->variant_name }})
                                                    @endif
                                                </td>
                                                <td>{{ $item->unit->name ?? '-' }}</td>
                                                <td>{{ number_format($item->quantity, 2) }}</td>
                                                <td>{{ number_format($item->unit_cost, 2) }}</td>
                                                <td>{{ number_format($item->total_cost, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-right font-weight-bold">Total:</td>
                                            <td class="font-weight-bold">
                                                {{ number_format($purchaseReturn->total_return_amount, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
