<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sale Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f5fa; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .section-title { font-size: 18px; margin-bottom: 5px; }
        .section { margin-bottom: 20px; }
    </style>
</head>
<body>

    <h2 class="text-center">Sale Invoice</h2>

    <!-- Sale and Customer Info -->
    <div class="section">
        <table>
            <tr>
                <td>
                    <div class="section-title">Customer Information</div>
                    <p>
                        <strong>Name:</strong> {{ $sale->customer->name }}<br>
                        <strong>Email:</strong> {{ $sale->customer->email ?? '-' }}<br>
                        <strong>Phone:</strong> {{ $sale->customer->phone ?? '-' }}
                    </p>
                </td>
                <td class="text-right">
                    <div class="section-title">Sale Details</div>
                    <p>
                        <strong>Invoice No:</strong> {{ $sale->invoice_number }}<br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}<br>
                        <strong>Branch:</strong> {{ $sale->branch->name ?? '-' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <div class="section">
        <div class="section-title">Items</div>
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
                @foreach ($sale->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td>{{ $item->variant->variant_name ?? '-' }}</td>
                        <td>{{ $item->quantity }} {{ $item->unit->name ?? '' }}</td>
                        <td>{{ $sale->currency_symbol ?? 'PKR' }} {{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $sale->currency_symbol ?? 'PKR' }} {{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary Table -->
    <div class="section">
        <div class="section-title">Summary</div>
        <table>
            <tr>
                <th>Subtotal:</th>
                <td>{{ $sale->currency_symbol ?? 'PKR' }} {{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Discount:</th>
                <td>{{ $sale->currency_symbol ?? 'PKR' }} {{ number_format($sale->discount_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Tax:</th>
                <td>{{ $sale->currency_symbol ?? 'PKR' }} {{ number_format($sale->tax_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Shipping:</th>
                <td>{{ $sale->currency_symbol ?? 'PKR' }} {{ number_format($sale->shipping, 2) }}</td>
            </tr>
            <tr>
                <th><strong>Grand Total:</strong></th>
                <td><strong>{{ $sale->currency_symbol ?? 'PKR' }} {{ number_format($sale->final_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <th>Paid:</th>
                <td>{{ $sale->currency_symbol ?? 'PKR' }} {{ number_format($sale->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Due:</th>
                <td>{{ $sale->currency_symbol ?? 'PKR' }} {{ number_format($sale->due_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <p class="text-center">Thank you for your purchase!</p>

</body>
</html>
