@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h3>Sale Invoice</h3>
        </div>

        <div class="card-body">
            <!-- Customer and Purchase Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <p>
                        <strong>Name:</strong> {{ $sale->customer->name }}<br>
                        <strong>Email:</strong> {{ $sale->customer->email ?? '-' }}<br>
                        <strong>Phone:</strong> {{ $sale->customer->phone ?? '-' }}<br>
                    </p>
                </div>
                <div class="col-md-6 text-right">
                    <h5>Customer Details</h5>
                    <p>
                        <strong>Invoice No:</strong> {{ $sale->invoice_number }}<br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}<br>
                        <strong>Branch:</strong> {{ $sale->branch->name ?? '-' }}
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
                            <th>Unit</th>
                            <th>Unit Cost</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->variant->variant_name ?? '-' }}</td>
                                <td>{{ number_format($item->quantity, 0) }}</td>
                                <td>{{ $item->unit->name }}</td> 
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($item->total_price, 2) }}</td>
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
                            <td>{{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Discount:</th>
                            <td>{{ number_format($sale->discount_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Tax:</th>
                            <td>{{ number_format($sale->tax_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th><strong>Grand Total:</strong></th>
                            <td><strong>{{ number_format($sale->final_amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Paid:</th>
                            <td>{{ number_format($sale->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Due:</th>
                            <td>{{ number_format($sale->due_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            {{-- <div class="text-center mt-5">
                <p>Thank you for your purchase!</p>
            </div> --}}
        </div>
    </div>
</div>

@endsection
