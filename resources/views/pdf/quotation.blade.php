<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quotation #{{ $quotation->quotation_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
    .header { background: #6c3483; color: #fff; padding: 20px; margin-bottom: 20px; }
    .header h1 { font-size: 22px; margin-bottom: 4px; }
    .header p { font-size: 11px; opacity: 0.8; }
    .info-row { display: table; width: 100%; margin-bottom: 20px; }
    .info-col { display: table-cell; width: 50%; vertical-align: top; padding: 0 10px 0 0; }
    .info-col:last-child { padding: 0 0 0 10px; text-align: right; }
    .info-col h3 { font-size: 13px; border-bottom: 2px solid #6c3483; padding-bottom: 4px; margin-bottom: 8px; }
    .info-col p { line-height: 1.8; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table th { background: #6c3483; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
    table td { padding: 7px 10px; border-bottom: 1px solid #e0e0e0; font-size: 11px; }
    table tr:nth-child(even) td { background: #f9f9f9; }
    .totals { width: 50%; margin-left: auto; border-collapse: collapse; }
    .totals td { padding: 6px 10px; }
    .totals td:first-child { font-weight: bold; }
    .totals tr:last-child td { background: #6c3483; color: #fff; font-weight: bold; font-size: 13px; }
    .note { background: #fef9e7; border-left: 4px solid #f39c12; padding: 10px 14px; margin-bottom: 20px; font-size: 11px; }
    .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
</style>
</head>
<body>

<div class="header">
    <h1>{{ $setting->company_name ?? 'POS System' }}</h1>
    <p>QUOTATION</p>
</div>

<div class="info-row">
    <div class="info-col">
        <h3>Customer Information</h3>
        <p>
            <strong>Name:</strong> {{ $quotation->customer->name ?? 'N/A' }}<br>
            <strong>Email:</strong> {{ $quotation->customer->email ?? '-' }}<br>
            <strong>Phone:</strong> {{ $quotation->customer->phone ?? '-' }}
        </p>
    </div>
    <div class="info-col">
        <h3>Quotation Details</h3>
        <p>
            <strong>Quotation No:</strong> {{ $quotation->quotation_number }}<br>
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('d M Y') }}<br>
            <strong>Status:</strong> {{ ucfirst($quotation->status) }}<br>
            <strong>Branch:</strong> {{ $quotation->branch->name ?? '-' }}
        </p>
    </div>
</div>

@if($quotation->note)
<div class="note">
    <strong>Note:</strong> {{ $quotation->note }}
</div>
@endif

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Variant</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Discount</th>
            <th>Tax</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($quotation->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->product->name ?? 'N/A' }}</td>
            <td>{{ $item->productVariant->variant_name ?? '-' }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $currencySymbol }} {{ number_format($item->unit_price, 2) }}</td>
            <td>{{ $currencySymbol }} {{ number_format($item->discount_amount, 2) }}</td>
            <td>{{ $currencySymbol }} {{ number_format($item->tax_amount, 2) }}</td>
            <td>{{ $currencySymbol }} {{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    @if($quotation->discount_percentage > 0)
    <tr>
        <td>
            @if(($quotation->discount_type ?? 'fixed') === 'percentage')
                Discount (%):
            @else
                Discount:
            @endif
        </td>
        <td>- {{ $currencySymbol }} {{ number_format($quotation->discount_percentage, 2) }}</td>
    </tr>
    @endif
    @if($quotation->order_tax_amount > 0)
    <tr>
        <td>Tax @if($quotation->tax_percentage > 0)({{ $quotation->tax_percentage }}%)@endif:</td>
        <td>+ {{ $currencySymbol }} {{ number_format($quotation->order_tax_amount, 2) }}</td>
    </tr>
    @endif
    @if($quotation->shipping_cost > 0)
    <tr>
        <td>Shipping:</td>
        <td>+ {{ $currencySymbol }} {{ number_format($quotation->shipping_cost, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td>Grand Total:</td>
        <td>{{ $currencySymbol }} {{ number_format($quotation->grand_total, 2) }}</td>
    </tr>
</table>

<div class="footer">
    <p>This is a quotation and not a final invoice. &mdash; {{ $setting->company_name ?? 'POS System' }}</p>
</div>

</body>
</html>
