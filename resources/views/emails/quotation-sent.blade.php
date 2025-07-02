{{-- resources/views/emails/quotation-sent.blade.php --}}

<h2>Dear {{ $quotation->customer->name }},</h2>

<p>Your quotation <strong>#{{ $quotation->quotation_number }}</strong> has been generated on {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('d M Y') }}
.</p>

<h4>Quotation Details:</h4>
<table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Variant</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($quotation->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td>{{ $item->productVariant->variant_name ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 2) }} {{ $quotation->currency_symbol ?? 'PKR' }}</td>
                <td>{{ number_format($item->sub_total, 2) }} {{ $quotation->currency_symbol ?? 'PKR' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>

<h4>Summary:</h4>
@php
    $currencySymbol = $quotation->currency_symbol ?? 'PKR';
    $calculatedSubtotal = 0;

    foreach ($quotation->items ?? [] as $item) {
        $calculatedSubtotal += ($item->unit_price * $item->quantity);
    }
@endphp

<p><strong>Subtotal:</strong> {{ $currencySymbol }} {{ number_format($calculatedSubtotal, 2) }}</p>
<p><strong>Discount:</strong> - {{ $currencySymbol }} {{ $quotation->discount_percentage }}</p>
<p><strong>Tax:</strong> + {{ $currencySymbol }} {{ $quotation->order_tax_percentage }}</p>
<p><strong>Shipping Cost:</strong> + {{ $currencySymbol }} {{ number_format($quotation->shipping_cost, 2) }}</p>
<hr>
<p><strong>Grand Total:</strong> {{ $currencySymbol }} {{ number_format($quotation->grand_total, 2) }}</p>

<br>

<p>Thank you for choosing us!</p>
