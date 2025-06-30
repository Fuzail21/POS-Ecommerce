@extends('layouts.app')

@section('style')
<style>
    .detail-card {
        background-color: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .detail-card h5 {
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 25px;
        font-weight: 600;
        color: #333;
    }

    .detail-item {
        margin-bottom: 12px;
        font-size: 15px;
    }

    .detail-item strong {
        display: inline-block;
        min-width: 130px;
        color: #555;
    }

    .table-responsive {
        margin-top: 20px;
    }

    .table th {
        background-color: #f4f6f8;
        color: #333;
        font-weight: 600;
    }

    .table th,
    .table td {
        vertical-align: middle;
        font-size: 14px;
    }

    .summary-section {
        background-color: #fdfdfd;
        padding: 20px;
        border-radius: 8px;
        margin-top: 25px;
        border: 1px solid #ddd;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        color: #444;
    }

    .summary-item strong {
        font-size: 1.05rem;
    }

    .summary-total {
        font-size: 1.3rem;
        font-weight: bold;
        border-top: 1px solid #ccc;
        padding-top: 10px;
        margin-top: 15px;
        color: #000;
    }

    .badge-primary {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 13px;
    }

    @media (max-width: 767px) {
        .summary-section {
            font-size: 14px;
        }

        .detail-item strong {
            min-width: 110px;
        }

        .summary-total {
            font-size: 1.15rem;
        }
    }
</style>
@endsection

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <h4 class="mb-3">Quotation Details - #{{ $quotation?->id }}</h4>
                    <a href="{{ route('quotations.index') }}" class="btn btn-secondary add-list">
                        <i class="las la-arrow-left mr-2"></i>Back to Quotations
                    </a>
                </div>
            </div>

            @php
                use App\Models\Setting;
                $setting = Setting::first();
                $currencySymbol = $setting->currency_symbol ?? '$';
            @endphp

            <div class="col-md-12">
                <div class="detail-card">
                    <h5>Quotation Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($quotation?->quotation_date)->format('M d, Y') ?? 'N/A' }}
                            </div>
                            <div class="detail-item">
                                <strong>Customer:</strong> {{ $quotation?->customer?->name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <strong>Branch:</strong> {{ $quotation?->branch?->name ?? 'N/A' }}
                            </div>
                            <div class="detail-item">
                                <strong>Status:</strong> 
                                <span class="badge badge-primary">{{ ucfirst($quotation?->status ?? 'N/A') }}</span>
                            </div>
                        </div>
                    </div>
                    @if($quotation?->note)
                    <div class="detail-item mt-3">
                        <strong>Note:</strong> {{ $quotation->note }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-md-12 mt-4">
                <div class="detail-card">
                    <h5>Quotation Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
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
                                @forelse($quotation?->items ?? [] as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($item->product_display_name && $item->variant_display_name)
                                            {{ $item->product_display_name }} - {{ $item->variant_display_name }}
                                        @elseif($item->product_display_name)
                                            {{ $item->product_display_name }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">{{ $currencySymbol }} {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-right">{{ $currencySymbol }} {{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No items in this quotation.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="summary-section">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="text-left">Subtotal:</th>
                                            <td class="text-right">
                                                @php
                                                    $calculatedSubtotal = 0;
                                                    foreach ($quotation?->items ?? [] as $item) {
                                                        $calculatedSubtotal += ($item->unit_price * $item->quantity);
                                                    }
                                                @endphp
                                                {{ $currencySymbol }} {{ number_format($calculatedSubtotal, 2) }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th class="text-left">Discount:
                                            </th>
                                            <td class="text-right text-danger">- {{ $currencySymbol }} {{ number_format($quotation?->discount_percentage ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-left">Tax:</th>
                                            <td class="text-right text-success">+ {{ $currencySymbol }} {{ number_format($quotation?->order_tax_percentage ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-left">Shipping:</th>
                                            <td class="text-right text-success">+ {{ $currencySymbol }} {{ number_format($quotation?->shipping_cost ?? 0, 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <th class="text-left pt-3"><strong>Grand Total:</strong></th>
                                            <td class="text-right pt-3"><strong>{{ $currencySymbol }} {{ number_format($quotation?->grand_total ?? 0, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
