@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#add-product" role="tab">Add Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#payment" role="tab">Payment</a>
                    </li>
                </ul>

                <form action="{{ route('purchases.store') }}" method="POST">
                    @csrf
                    <div class="tab-content">

                        <!-- Card 1: Supplier & Purchase Info -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h4>Supplier & Purchase Info</h4>
                            </div>
                            <div class="card-body row">
                                <div class="form-group col-md-6">
                                    <label>Supplier</label>
                                    <select name="supplier_id" class="form-control" required>
                                        <option value="">Select Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Invoice No</label>
                                    <input type="text" name="invoice_no" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Purchase Date</label>
                                    <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Payment Status</label>
                                    <select name="payment_status" class="form-control" required>
                                        <option value="Unpaid">Unpaid</option>
                                        <option value="Paid">Paid</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Add Products -->
                        <div class="tab-pane fade show active" id="add-product" role="tabpanel">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4>Add Products</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Unit Price</th>
                                                <th>Subtotal</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="product-rows">
                                            <tr>
                                                <td>
                                                    <div class="w-100">
                                                        <select name="products[0][id]" class="form-control product-select select2">
                                                            @foreach($products as $product)
                                                                <option value="{{ $product->id }}"
                                                                    data-unit="{{ $product->unit }}"
                                                                    data-price="{{ $product->price }}">
                                                                    {{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td><input type="number" name="products[0][quantity]" class="form-control quantity-input" min="1" required></td>
                                                <td><input type="text" name="products[0][unit]" class="form-control unit-input"></td>
                                                <td><input type="number" step="0.01" name="products[0][price]" class="form-control price-input"></td>
                                                <td><input type="number" step="0.01" name="products[0][subtotal]" class="form-control subtotal-input" readonly></td>
                                                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-secondary" onclick="addProductRow()">+ Add Product</button>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Payment Summary -->
                        <div class="tab-pane fade" id="payment" role="tabpanel">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4>Summary & Payment</h4>
                                </div>
                                <div class="card-body row">
                                    <div class="form-group col-md-4">
                                        <label>Subtotal</label>
                                        <input type="text" name="total_amount" id="subtotal" class="form-control" readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Discount</label>
                                        <input type="number" name="discount" id="discount" class="form-control" step="0.01" value="0">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Tax</label>
                                        <input type="number" name="tax" id="tax" class="form-control" step="0.01" value="0">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Total Payable</label>
                                        <input type="text" name="total_due" id="total_due" class="form-control" readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Payment Mode</label>
                                        <select name="payment_mode" class="form-control">
                                            <option value="Cash">Cash</option>
                                            <option value="Card">Card</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Payment Now</label>
                                        <input type="number" name="payment_now" id="payment_now" class="form-control" step="0.01" value="0">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Remaining Payment</label>
                                        <input type="text" name="due_payment" id="due" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">Save & New</button>
                            <a href="{{ route('purchases.list') }}" class="btn btn-secondary">Cancel</a>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- JavaScript -->
<script>
let productIndex = 1;

function addProductRow() {
    const row = `
        <tr>
            <td>
                <div class="form-group mb-0 w-100">
                    <select name="products[${productIndex}][id]" class="form-control product-select select2" style="width: 100%;">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" data-price="{{ $product->price }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td><input type="number" name="products[${productIndex}][quantity]" class="form-control quantity-input" min="1" required></td>
            <td><input type="text" name="products[${productIndex}][unit]" class="form-control unit-input"></td>
            <td><input type="number" step="0.01" name="products[${productIndex}][price]" class="form-control price-input"></td>
            <td><input type="number" step="0.01" name="products[${productIndex}][subtotal]" class="form-control subtotal-input" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
        </tr>`;
    document.getElementById('product-rows').insertAdjacentHTML('beforeend', row);
    $('.select2').select2({ width: '100%' });
    productIndex++;
    attachEvents();
}

function attachEvents() {
    $('.product-select').off().on('change', function () {
        const selected = $(this).find('option:selected');
        const unit = selected.data('unit');
        const price = selected.data('price');
        const row = $(this).closest('tr');
        row.find('.unit-input').val(unit);
        row.find('.price-input').val(price);
        updateRowSubtotal(row);
    });

    $('.quantity-input, .price-input').off().on('input', function () {
        const row = $(this).closest('tr');
        updateRowSubtotal(row);
    });

    $('.remove-row').off().on('click', function () {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    $('#discount, #tax, #payment_now').off().on('input', calculateTotals);
}

function updateRowSubtotal(row) {
    const qty = parseFloat(row.find('.quantity-input').val()) || 0;
    const price = parseFloat(row.find('.price-input').val()) || 0;
    const subtotal = qty * price;
    row.find('.subtotal-input').val(subtotal.toFixed(2));
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    $('.subtotal-input').each(function () {
        subtotal += parseFloat($(this).val()) || 0;
    });
    $('#subtotal').val(subtotal.toFixed(2));

    const discount = parseFloat($('#discount').val()) || 0;
    const tax = parseFloat($('#tax').val()) || 0;
    const totalDue = subtotal - discount + tax;
    $('#total_due').val(totalDue.toFixed(2));

    const paymentNow = parseFloat($('#payment_now').val()) || 0;
    const due = totalDue - paymentNow;
    $('#due').val(due.toFixed(2));
}

$(document).ready(function () {
    $('.select2').select2({ width: '100%' });
    attachEvents();
});
</script>
@endsection
