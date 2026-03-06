@extends('layouts.app')

@section('content')

    @include('layouts.sidebar')

    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">{{ $title }}</h4>
                                <p class="mb-0 text-muted">
                                    Purchase: <strong>{{ $purchase->invoice_number }}</strong>
                                    &nbsp;|&nbsp; Supplier: <strong>{{ $purchase->supplier->name ?? 'N/A' }}</strong>
                                    &nbsp;|&nbsp; Warehouse: <strong>{{ $purchase->warehouse->name ?? 'N/A' }}</strong>
                                </p>
                            </div>
                            <a href="{{ route('purchase_returns.index') }}" class="btn btn-secondary btn-sm">
                                Back to Returns
                            </a>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger mx-3 mt-3">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger mx-3 mt-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('purchase_returns.store', $purchase->id) }}" method="POST" id="returnForm">
                                @csrf

                                {{-- Items Table --}}
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th>Originally Purchased</th>
                                                <th>Already Returned</th>
                                                <th>Max Returnable</th>
                                                <th>Unit Cost</th>
                                                <th>Return Qty</th>
                                                <th>Return Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchase->items as $item)
                                                @php
                                                    $key            = $item->product_id . '_' . ($item->variant_id ?? 0);
                                                    $alreadyRet     = $alreadyReturnedMap[$key] ?? 0;
                                                    $maxReturnable  = max(0, $item->quantity - $alreadyRet);
                                                    $productName    = $item->product->name ?? 'N/A';
                                                    if ($item->variant) {
                                                        $productName .= ' (' . $item->variant->variant_name . ')';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $productName }}</td>
                                                    <td>{{ $item->unit->name ?? '-' }}</td>
                                                    <td>{{ number_format($item->quantity, 2) }}</td>
                                                    <td>{{ number_format($alreadyRet, 2) }}</td>
                                                    <td>
                                                        @if ($maxReturnable <= 0)
                                                            <span class="badge bg-secondary">Fully Returned</span>
                                                        @else
                                                            {{ number_format($maxReturnable, 2) }}
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($item->unit_cost, 2) }}</td>
                                                    <td>
                                                        @if ($maxReturnable > 0)
                                                            <input type="number"
                                                                   name="return_items[{{ $item->id }}][quantity]"
                                                                   class="form-control form-control-sm qty-input"
                                                                   min="0" max="{{ $maxReturnable }}"
                                                                   step="0.01"
                                                                   value="0"
                                                                   data-unit-cost="{{ $item->unit_cost }}"
                                                                   style="width: 100px;">
                                                        @else
                                                            <input type="hidden"
                                                                   name="return_items[{{ $item->id }}][quantity]"
                                                                   value="0">
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="item-total">0.00</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="7" class="text-right font-weight-bold">Total Return Amount:</td>
                                                <td id="grandTotal" class="font-weight-bold">0.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="row">
                                    {{-- Return Reason --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="return_reason" class="form-label">Return Reason</label>
                                        <textarea name="return_reason" id="return_reason" rows="3"
                                                  class="form-control" maxlength="1000"
                                                  placeholder="e.g. Damaged goods, Wrong items, etc.">{{ old('return_reason') }}</textarea>
                                    </div>

                                    {{-- Refund Amount --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="refund_amount" class="form-label">
                                            Refund Amount <span class="text-danger">*</span>
                                            <small class="text-muted">(amount supplier refunds / credit given)</small>
                                        </label>
                                        <input type="number" name="refund_amount" id="refund_amount"
                                               step="0.01" min="0" class="form-control"
                                               value="{{ old('refund_amount', '0') }}" required>
                                    </div>

                                    {{-- Payment Method --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select name="payment_method" id="payment_method" class="form-control" required>
                                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                            <option value="credit_note" {{ old('payment_method') == 'credit_note' ? 'selected' : '' }}>Credit Note</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-2">
                                    <button type="submit" class="btn btn-danger" id="submitBtn" disabled>
                                        Process Return
                                    </button>
                                    <a href="{{ route('purchase_returns.index') }}" class="btn btn-light">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script>
    const qtyInputs  = document.querySelectorAll('.qty-input');
    const grandTotal = document.getElementById('grandTotal');
    const submitBtn  = document.getElementById('submitBtn');

    function recalculate() {
        let total = 0;
        qtyInputs.forEach(function (input) {
            const qty      = parseFloat(input.value) || 0;
            const unitCost = parseFloat(input.dataset.unitCost) || 0;
            const lineVal  = qty * unitCost;
            total += lineVal;

            // Update row total cell in same <tr>
            const row = input.closest('tr');
            row.querySelector('.item-total').textContent = lineVal.toFixed(2);
        });

        grandTotal.textContent = total.toFixed(2);

        // Auto-fill refund amount with calculated total
        document.getElementById('refund_amount').value = total.toFixed(2);

        // Enable submit only if at least one qty > 0
        const hasQty = Array.from(qtyInputs).some(function (i) {
            return parseFloat(i.value) > 0;
        });
        submitBtn.disabled = !hasQty;
    }

    qtyInputs.forEach(function (input) {
        input.addEventListener('input', recalculate);
        input.addEventListener('change', recalculate);
    });
</script>
@endsection
