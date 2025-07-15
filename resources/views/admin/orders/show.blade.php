@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">{{ $title }}</h4>
                    </div>
                    <a href="{{ route('orders.index') }}" class="btn btn-primary">Back to Orders List</a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Order #{{ $order->invoice_number }} Details</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('sales.invoice', $order->id) }}" class="btn btn-sm btn-info" target="_blank">
                                <i class="ri-printer-line"></i> Print Invoice
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Order Information</h5>
                                <p><strong>Invoice Number:</strong> {{ $order->invoice_number }}</p>
                                <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->sale_date)->format('d M Y h:i A') }}</p>
                                <p><strong>Order Origin:</strong> {{ ucfirst($order->sale_origin ?? 'N/A') }}</p>
                                <p><strong>Branch:</strong> {{ $order->branch->name ?? 'N/A' }}</p>
                                <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                                <p><strong>Placed By:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                                <p>
                                    <strong>Status:</strong>
                                    @php
                                        $statusClass = '';
                                        switch($order->status) {
                                            case 'pending': $statusClass = 'badge bg-warning'; break;
                                            case 'confirmed': $statusClass = 'badge bg-primary'; break;
                                            case 'shipped': $statusClass = 'badge bg-info'; break;
                                            case 'delivered': $statusClass = 'badge bg-success'; break;
                                            case 'cancelled': $statusClass = 'badge bg-danger'; break;
                                            default: $statusClass = 'badge bg-secondary'; break;
                                        }
                                    @endphp
                                    <span class="{{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                </p> {{-- Display Status Here --}}
                            </div>
                            <div class="col-md-6">
                                <h5>Customer Information</h5>
                                <p><strong>Name:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                                <p><strong>Email:</strong> {{ $order->customer->email ?? 'N/A' }}</p>
                                <p><strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
                                <p><strong>Address:</strong> {{ $order->customer->address ?? 'N/A' }}, {{ $order->customer->city ?? '' }}, {{ $order->customer->country ?? '' }}</p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5>Order Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Unit</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td>{{ $item->variant->name ?? 'N/A' }}</td>
                                            <td>{{ $item->unit->name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $setting->currency_symbol }} {{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ $setting->currency_symbol }} {{ number_format($item->total_price, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No items found for this order.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <hr class="my-4">

                        <div class="row justify-content-end">
                            <div class="col-md-6">
                                <p class="text-right"><strong>Subtotal:</strong> {{ $setting->currency_symbol }} {{ number_format($order->total_amount, 2) }}</p>
                                @if($order->discount_amount > 0)
                                    <p class="text-right text-danger"><strong>Discount:</strong> -{{ $setting->currency_symbol }} {{ number_format($order->discount_amount, 2) }}</p>
                                @endif
                                <p class="text-right"><strong>Tax:</strong> {{ $setting->currency_symbol }} {{ number_format($order->tax_amount, 2) }}</p>
                                <p class="text-right"><strong>Shipping:</strong> {{ $setting->currency_symbol }} {{ number_format($order->shipping, 2) }}</p>
                                <h4 class="text-right"><strong>Grand Total:</strong> {{ $setting->currency_symbol }} {{ number_format($order->final_amount, 2) }}</h4>
                                <p class="text-right text-success"><strong>Amount Paid:</strong> {{ $setting->currency_symbol }} {{ number_format($order->paid_amount, 2) }}</p>
                                @if($order->due_amount > 0)
                                    <p class="text-right text-danger"><strong>Due Amount:</strong> {{ $setting->currency_symbol }} {{ number_format($order->due_amount, 2) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection