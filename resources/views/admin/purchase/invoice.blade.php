@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h3>Purchase Invoice</h3>
        </div>

        <div class="card-body">
            <!-- Supplier and Purchase Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Supplier Information</h5>
                    <p>
                        <strong>Name:</strong> {{ $purchase->supplier->name }}<br>
                        <strong>Email:</strong> {{ $purchase->supplier->email ?? '-' }}<br>
                        <strong>Phone:</strong> {{ $purchase->supplier->phone ?? '-' }}<br>
                    </p>
                </div>
                <div class="col-md-6 text-right">
                    <h5>Purchase Details</h5>
                    <p>
                        <strong>Invoice No:</strong> {{ $purchase->invoice_number }}<br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}<br>
                        <strong>Warehouse:</strong> {{ $purchase->warehouse->name ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead style="background-color: #F4F5FA;">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Variant</th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->variant->variant_name ?? '-' }}</td>
                                <td>{{ $item->quantity }} {{ $item->unit->name }}</td>
                                <td>{{ number_format($item->unit_cost, 2) }}</td>
                                <td>{{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="row mt-4">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Subtotal:</th>
                            <td>{{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Discount:</th>
                            <td>{{ number_format($purchase->discount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Tax:</th>
                            <td>{{ number_format($purchase->tax, 2) }}</td>
                        </tr>
                        <tr>
                            <th><strong>Grand Total:</strong></th>
                            <td><strong>{{ number_format($purchase->total_amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Paid:</th>
                            <td>{{ number_format($purchase->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Due:</th>
                            <td>{{ number_format($purchase->due_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-5">
                <p>Thank you for your purchase!</p>
            </div>

            <div class="text-right mb-3">
                <a href="{{ route('purchases.pdf', $purchase->id) }}" class="btn btn-danger" target="_blank">
                    <i class="fa fa-file-pdf"></i> Download PDF
                </a>
                <button onclick="window.print()" class="btn btn-secondary ml-2">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
