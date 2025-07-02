<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Invoice</title>
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

    <h2 class="text-center">Purchase Invoice</h2>

    <!-- Supplier and Purchase Info -->
    <div class="section">
        <table>
            <tr>
                <td>
                    <div class="section-title">Supplier Information</div>
                    <p>
                        <strong>Name:</strong> {{ $purchase->supplier->name }}<br>
                        <strong>Email:</strong> {{ $purchase->supplier->email ?? '-' }}<br>
                        <strong>Phone:</strong> {{ $purchase->supplier->phone ?? '-' }}
                    </p>
                </td>
                <td class="text-right">
                    <div class="section-title">Purchase Details</div>
                    <p>
                        <strong>Invoice No:</strong> {{ $purchase->invoice_number }}<br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}<br>
                        <strong>Warehouse:</strong> {{ $purchase->warehouse->name ?? '-' }}
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
                        <td>{{ $item->quantity }} {{ $item->unit->name ?? '' }}</td>
                        <td>{{ number_format($item->unit_cost, 2) }}</td>
                        <td>{{ number_format($item->total_cost, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="section">
        <div class="section-title">Summary</div>
        <table>
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

    <!-- Footer -->
    <p class="text-center">Thank you for your purchase!</p>

</body>
</html>
