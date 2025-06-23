            @php
                use App\Models\Setting;
                $setting = \App\Models\Setting::first();
            @endphp
<div id="invoice-content" class="font-mono text-sm text-gray-800 leading-tight">
    <div class="text-center mb-2">
        <h2 class="text-2xl font-extrabold tracking-wide text-gray-900">{{ $setting->business_name }} INVOICE</h2>
        <p class="text-xs text-gray-500">Invoice #: {{ $sale->invoice_number }}</p>
        <p class="text-xs">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y, h:i A') }}</p>
    </div>

    <hr class="border-t border-gray-400 my-2">

    <div class="space-y-1 mb-3">
        @foreach($sale->items as $item)
            <div class="flex justify-between">
                <div class="w-3/5 truncate">{{ $item->product->name ?? 'Item' }}</div>
                <div class="w-1/5 text-right">x{{ $item->quantity }}</div>
                <div class="w-1/5 text-right"> {{ $setting->currency_symbol }} {{ number_format($item->total_price, 2) }}</div>
            </div>
        @endforeach
    </div>

    <hr class="border-t border-dashed border-gray-300 my-2">

    <div class="space-y-1 text-sm">
        <div class="flex justify-between"><span>Subtotal:</span><span> {{ $setting->currency_symbol }} {{ number_format($sale->total_amount, 2) }}</span></div>
        <div class="flex justify-between"><span>Discount:</span><span> {{ $setting->currency_symbol }} {{ number_format($sale->discount_amount, 2) }}</span></div>
        <div class="flex justify-between"><span>Tax:</span><span> {{ $setting->currency_symbol }} {{ number_format($sale->tax_amount, 2) }}</span></div>
        <div class="flex justify-between"><span>Shipping:</span><span> {{ $setting->currency_symbol }} {{ number_format($sale->shipping, 2) }}</span></div>
        <hr class="border-t border-gray-300 my-1">
        <div class="flex justify-between font-bold text-base text-gray-900">
            <span>Grand Total:</span><span> {{ $setting->currency_symbol }} {{ number_format($sale->final_amount, 2) }}</span>
        </div>
    </div>

    <div class="text-center mt-4">
        <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $sale->invoice_number }}&code=Code128&dpi=96" alt="Barcode" class="mx-auto w-48 h-auto">
        <p class="text-[10px] mt-1 tracking-wide text-gray-600">{{ $sale->invoice_number }}</p>
    </div>
</div>

