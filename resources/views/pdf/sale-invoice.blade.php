<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sale Invoice #{{ $sale->invoice_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
    .header { background: #2c3e50; color: #fff; padding: 20px; margin-bottom: 20px; }
    .header h1 { font-size: 22px; margin-bottom: 4px; }
    .header p { font-size: 11px; opacity: 0.8; }
    .info-row { display: table; width: 100%; margin-bottom: 20px; }
    .info-col { display: table-cell; width: 50%; vertical-align: top; padding: 0 10px 0 0; }
    .info-col:last-child { padding: 0 0 0 10px; text-align: right; }
    .info-col h3 { font-size: 13px; border-bottom: 2px solid #2c3e50; padding-bottom: 4px; margin-bottom: 8px; }
    .info-col p { line-height: 1.8; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table th { background: #2c3e50; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
    table td { padding: 7px 10px; border-bottom: 1px solid #e0e0e0; font-size: 11px; }
    table tr:nth-child(even) td { background: #f9f9f9; }
    .totals { width: 50%; margin-left: auto; border-collapse: collapse; }
    .totals td { padding: 6px 10px; }
    .totals td:first-child { font-weight: bold; }
    .totals tr:last-child td { background: #2c3e50; color: #fff; font-weight: bold; font-size: 13px; }
    .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
</style>
</head>
<body>

<div class="header">
    <h1>{{ $setting->company_name ?? 'POS System' }}</h1>
    <p>SALE INVOICE</p>
</div>

<div class="info-row">
    <div class="info-col">
        <h3>Customer Information</h3>
        <p>
            <strong>Name:</strong> {{ $sale->customer->name ?? 'Walk-in' }}<br>
            <strong>Email:</strong> {{ $sale->customer->email ?? '-' }}<br>
            <strong>Phone:</strong> {{ $sale->customer->phone ?? '-' }}
        </p>
    </div>
    <div class="info-col">
        <h3>Invoice Details</h3>
        <p>
            <strong>Invoice No:</strong> {{ $sale->invoice_number }}<br>
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}<br>
            <strong>Branch:</strong> {{ $sale->branch->name ?? '-' }}<br>
            <strong>Status:</strong> {{ ucfirst($sale->status ?? 'N/A') }}
        </p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Variant</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sale->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->product->name ?? 'N/A' }}</td>
            <td>{{ $item->variant->variant_name ?? '-' }}</td>
            <td>{{ number_format($item->quantity, 0) }} {{ $item->unit->name ?? '' }}</td>
            <td>{{ $currencySymbol }} {{ number_format($item->unit_price, 2) }}</td>
            <td>{{ $currencySymbol }} {{ number_format($item->total_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr>
        <td>Subtotal:</td>
        <td>{{ $currencySymbol }} {{ number_format($sale->total_amount, 2) }}</td>
    </tr>
    @if($sale->discount_amount > 0)
    <tr>
        <td>
            @if(($sale->discount_type ?? 'fixed') === 'percentage')
                Discount (%):
            @else
                Discount:
            @endif
        </td>
        <td>- {{ $currencySymbol }} {{ number_format($sale->discount_amount, 2) }}</td>
    </tr>
    @endif
    @if($sale->tax_amount > 0)
    <tr>
        <td>Tax @if(($sale->tax_percentage ?? 0) > 0)({{ $sale->tax_percentage }}%)@endif:</td>
        <td>{{ $currencySymbol }} {{ number_format($sale->tax_amount, 2) }}</td>
    </tr>
    @endif
    @if($sale->shipping > 0)
    <tr>
        <td>Shipping:</td>
        <td>{{ $currencySymbol }} {{ number_format($sale->shipping, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td>Grand Total:</td>
        <td>{{ $currencySymbol }} {{ number_format($sale->final_amount, 2) }}</td>
    </tr>
</table>

<div class="footer">
    <p>Thank you for your business! &mdash; {{ $setting->company_name ?? 'POS System' }}</p>
</div>

</body>
</html>
