@extends('layouts.app')

@section('style')
<style>
    .invoice-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
    }
    .invoice-header {
        padding: 30px;
        border-bottom: 1px solid #eee;
    }
    .invoice-body {
        padding: 30px;
    }
    .invoice-footer {
        padding: 20px 30px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }
    .company-logo {
        max-height: 70px;
    }
    .invoice-title {
        font-size: 24px;
        color: #333;
        margin-bottom: 5px;
    }
    .invoice-subtitle {
        color: #666;
        margin-bottom: 0;
    }
    .invoice-details {
        margin-top: 20px;
    }
    .invoice-details-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .invoice-table {
        margin: 30px 0;
    }
    .invoice-table th {
        background: #f8f9fa;
        border-top: none !important;
    }
    .invoice-summary {
        border-top: 2px solid #eee;
        padding-top: 20px;
    }
    .invoice-summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .invoice-summary-row.total {
        font-size: 18px;
        font-weight: bold;
        color: #28a745;
    }
    .payment-status {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    .payment-status.paid {
        background: #d4edda;
        color: #155724;
    }
    .payment-status.partial {
        background: #fff3cd;
        color: #856404;
    }
    .payment-status.unpaid {
        background: #f8d7da;
        color: #721c24;
    }
    .action-buttons {
        margin-top: 20px;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .invoice-card {
            box-shadow: none;
            margin: 0;
        }
    }
</style>
@endsection

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <!-- Action Buttons -->
        <div class="row mb-3 no-print">
            <div class="col-12">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
                <a href="{{ route('sales.list') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Sales
                </a>
                @if($sale->due_amount > 0)
                    <a href="{{ route('payments.create', ['sale_id' => $sale->id]) }}" class="btn btn-success">
                        <i class="fas fa-money-bill"></i> Record Payment
                    </a>
                @endif
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="invoice-card">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        @if(config('app.logo'))
                            <img src="{{ asset(config('app.logo')) }}" alt="Company Logo" class="company-logo">
                        @else
                            <h2 class="mb-1">{{ config('app.name', 'POS System') }}</h2>
                        @endif
                        <p class="text-muted mb-0">{{ config('app.address', '123 Business Street') }}</p>
                        <p class="text-muted mb-0">{{ config('app.phone', '+1 234 567 890') }}</p>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <h1 class="invoice-title">INVOICE</h1>
                        <p class="invoice-subtitle">#{{ $sale->invoice_number }}</p>
                        <span class="payment-status {{ $sale->due_amount == 0 ? 'paid' : ($sale->paid_amount > 0 ? 'partial' : 'unpaid') }}">
                            {{ $sale->due_amount == 0 ? 'PAID' : ($sale->paid_amount > 0 ? 'PARTIALLY PAID' : 'UNPAID') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="invoice-body">
                <!-- Customer & Invoice Details -->
                <div class="row invoice-details">
                    <div class="col-md-6">
                        <h5 class="mb-3">Bill To</h5>
                        <h6>{{ $sale->customer->name }}</h6>
                        @if($sale->customer->address)
                            <p class="mb-1">{{ $sale->customer->address }}</p>
                        @endif
                        @if($sale->customer->phone)
                            <p class="mb-1">{{ $sale->customer->phone }}</p>
                        @endif
                        @if($sale->customer->email)
                            <p class="mb-1">{{ $sale->customer->email }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-right">
                        <h5 class="mb-3">Invoice Details</h5>
                        <div class="invoice-details-row">
                            <span>Invoice Date:</span>
                            <span>{{ $sale->sale_date->format('M d, Y') }}</span>
                        </div>
                        <div class="invoice-details-row">
                            <span>Payment Method:</span>
                            <span>{{ ucfirst($sale->payment_method) }}</span>
                        </div>
                        <div class="invoice-details-row">
                            <span>Created By:</span>
                            <span>{{ $sale->creator->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="invoice-table table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td class="text-center">{{ $item->quantity_in_base_unit }}</td>
                                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Invoice Summary -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6>Payment History</h6>
                            @if($sale->payments->count() > 0)
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Method</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                                <td class="text-right">${{ number_format($payment->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No payments recorded</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="invoice-summary">
                            <div class="invoice-summary-row">
                                <span>Subtotal:</span>
                                <span>${{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                            @if($sale->discount_amount > 0)
                                <div class="invoice-summary-row">
                                    <span>Discount:</span>
                                    <span>-${{ number_format($sale->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            @if($sale->tax_amount > 0)
                                <div class="invoice-summary-row">
                                    <span>Tax:</span>
                                    <span>${{ number_format($sale->tax_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="invoice-summary-row total">
                                <span>Total:</span>
                                <span>${{ number_format($sale->final_amount, 2) }}</span>
                            </div>
                            <div class="invoice-summary-row">
                                <span>Paid Amount:</span>
                                <span>${{ number_format($sale->paid_amount, 2) }}</span>
                            </div>
                            @if($sale->due_amount > 0)
                                <div class="invoice-summary-row">
                                    <span>Balance Due:</span>
                                    <span class="text-danger">${{ number_format($sale->due_amount, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Footer -->
            <div class="invoice-footer text-center">
                <p class="mb-0">Thank you for your business!</p>
                @if(config('app.terms'))
                    <small class="text-muted">{{ config('app.terms') }}</small>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 