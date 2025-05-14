@extends('layouts.app')

@section('content')
    @include('layouts.sidebar')

    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <form method="POST" action="{{ isset($sale) ? route('sale.update', $sale->id) : route('sale.store') }}">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ isset($sale) ? 'Edit Sale' : 'Add Sale' }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Customer --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_id">Customer</label>
                                        <select class="form-control" name="customer_id" required>
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ isset($sale) && $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Total Amount --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="total_amount">Total Amount</label>
                                        <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" value="{{ $sale->total_amount ?? '' }}" readonly>
                                    </div>

                                    {{-- Discount --}}
                                    {{-- <div class="col-md-6 mb-3">
                                        <label for="discount">Discount</label>
                                        <input type="number" step="0.01" name="discount" class="form-control" value="{{ $sale->discount ?? 0 }}">
                                    </div> --}}

                                    {{-- Tax --}}
                                    {{-- <div class="col-md-6 mb-3">
                                        <label for="tax">Tax</label>
                                        <input type="number" step="0.01" name="tax" class="form-control" value="{{ $sale->tax ?? 0 }}">
                                    </div> --}}

                                    {{-- Discount & Tax Combo --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="discount_tax_id">Discount & Tax</label>
                                        <select class="form-control" name="discount_tax_id" id="discount_tax_id">
                                            <option value="">-- Select Discount/Tax --</option>
                                            @foreach ($discountTaxes as $dt)
                                                <option value="{{ $dt->id }}"
                                                    {{ isset($sale) && $sale->discount_tax_id == $dt->id ? 'selected' : '' }}>
                                                    {{ $dt->discount }}% Discount / {{ $dt->tax }}% Tax
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Payment Status --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="payment_status">Payment Status</label>
                                        <select class="form-control" name="payment_status" required>
                                            <option value="pending" {{ isset($sale) && $sale->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="paid" {{ isset($sale) && $sale->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Sale Items --}}
                                <hr>
                                <h5>Items</h5>
                                <div id="sale-items">
                                    @if(isset($sale))
                                        @foreach($sale->items as $item)
                                            <div class="row mb-3 sale-item-row">
                                                <div class="col-md-5">
                                                    <select class="form-control" name="products[]">
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" name="quantities[]" placeholder="Quantity" value="{{ $item->quantity }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" name="prices[]" placeholder="Price" value="{{ $item->price }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger remove-item">×</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row mb-3 sale-item-row">
                                            <div class="col-md-5">
                                                <select class="form-control" name="products[]">
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" name="quantities[]" placeholder="Quantity">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" name="prices[]" placeholder="Price">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger remove-item">×</button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary" id="add-item">+ Add Item</button>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-success">{{ isset($sale) ? 'Update Sale' : 'Add Sale' }}</button>
                                <a href="{{ route('sale.list') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS for dynamic items and total --}}
    <script>
        document.getElementById('add-item').addEventListener('click', function () {
            const row = document.querySelector('.sale-item-row');
            const clone = row.cloneNode(true);
            clone.querySelectorAll('input').forEach(input => input.value = '');
            row.parentNode.appendChild(clone);
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item')) {
                const rows = document.querySelectorAll('.sale-item-row');
                if (rows.length > 1) {
                    e.target.closest('.sale-item-row').remove();
                }
            }
        });

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.sale-item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('input[name="quantities[]"]')?.value || 0);
                const price = parseFloat(row.querySelector('input[name="prices[]"]')?.value || 0);
                total += qty * price;
            });
            document.getElementById('total_amount').value = total.toFixed(2);
        }

        document.addEventListener('input', function (e) {
            if (e.target.name === 'quantities[]' || e.target.name === 'prices[]') {
                calculateTotal();
            }
        });

        window.addEventListener('DOMContentLoaded', calculateTotal);
    </script>
@endsection
