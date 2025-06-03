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
                        <a class="nav-link active" data-toggle="tab" href="#add-product" role="tab">Products Purchase</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#payment" role="tab" id="paymentTab">Payment</a>
                    </li>
                </ul>

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                <form action="{{ route('purchases.store') }}" method="POST" id="productForm">
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
                                {{-- <div class="form-group col-md-6">
                                    <label>Invoice No</label>
                                    <input type="text" name="invoice_no" class="form-control" required>
                                </div> --}}
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
                                    <h4>Products Purchase</h4>
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
                                        <tbody id="product-rows"></tbody>
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
                                        <input type="text" name="subtotal" id="subtotal" class="form-control" readonly>
                                    </div>
                                    {{-- <div class="form-group col-md-4">
                                        <label>Discount</label>
                                        <input type="number" name="discount" id="discount" class="form-control" step="0.01" value="0">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Tax</label>
                                        <input type="number" name="tax" id="tax" class="form-control" step="0.01" value="0">
                                    </div> --}}
                                    {{-- <div class="form-group col-md-4">
                                        <label>Total Due</label>
                                        <input type="text" name="total_due" id="total_due" class="form-control" readonly>
                                    </div> --}}
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
                                        <label>Due</label>
                                        <input type="text" name="due" id="due" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary" id="saveAndNewBtn">Save</button>
                            <a href="{{ route('purchases.list') }}" class="btn btn-secondary">Cancel</a>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
{{-- data-price="${p.price}" --}}
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- Products as JSON -->
<script>
    const productsList = @json($productsMapped);
</script>

<!-- JavaScript -->
    <script>
        let productIndex = 0;

        function addProductRow() {
            const row = `
                <tr>
                    <td>
                        <select name="products[${productIndex}][id]" class="form-control main-product-select" required>
                            <option value="">-- Select Product --</option>
                            ${productsList.map((p, i) => 
                                `<option value="${p.id}" data-index="${i}" data-unit="${p.unit}" data-unit-id="${p.unit_id}">${p.name}</option>`
                            ).join('')}
                        </select>
                        <select name="products[${productIndex}][variant_id]" class="form-control variant-select mt-2" required>
                            <option value="">-- Select Variant --</option>
                        </select>
                    </td>
                    <td><input type="number" name="products[${productIndex}][quantity]" class="form-control quantity-input" min="1" required></td>
                    <td><input type="text" class="form-control unit-name-input" readonly>
                        <input type="hidden" name="products[${productIndex}][unit]" class="unit-id-input">
                    </td>
                    <td><input type="number" step="0.01" name="products[${productIndex}][unit_cost]" class="form-control price-input"></td>
                    <td><input type="number" step="0.01" name="products[${productIndex}][subtotal]" class="form-control subtotal-input" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                </tr>`;

            $('#product-rows').append(row);

            setTimeout(() => {
                $('.select2').select2({ width: '100%' });
            }, 100);

            attachEvents();
            productIndex++;
        }

        function attachEvents() {
            // Handle product select
            $('.main-product-select').off('change').on('change', function () {
                const row = $(this).closest('tr');
                const selectedOption = $(this).find(':selected');

                // Use real ID as value; use array index to get variants
                const productIndex = selectedOption.data('index');

                // Get base unit info
                const unitName = selectedOption.data('unit');
                const unitId = selectedOption.data('unit-id');

                row.find('.unit-name-input').val(unitName || '');
                row.find('.unit-id-input').val(unitId || '');

                const variants = productsList[productIndex]?.variants || [];

                const variantSelect = row.find('.variant-select');
                variantSelect.html('<option value="">-- Select Variant --</option>');

                variants.forEach(variant => {
                    variantSelect.append(`
                        <option value="${variant.id}">${variant.name}</option>
                    `);
                });

                // Handle quantity and price input changes
                $('.quantity-input, .price-input').off('input').on('input', function () {
                    const row = $(this).closest('tr');
                    updateRowSubtotal(row);
                });

                // Handle remove row
                $('.remove-row').off('click').on('click', function () {
                    $(this).closest('tr').remove();
                    calculateTotals(); // recalculate after row removal
                });

            })
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
        
            // Optional: Add discount and tax if needed
            const discount = parseFloat($('#discount').val()) || 0;
            const tax = parseFloat($('#tax').val()) || 0;
        
            const totalDue = subtotal - discount + tax;
        
            const paymentNow = parseFloat($('#payment_now').val()) || 0;
            const due = totalDue - paymentNow;
        
            // Update due field
            $('#due').val(due.toFixed(2));
        }

        $(document).ready(function () {
            $('#payment_now').on('input', function () {
                calculateTotals();
            });
        });

        $(document).ready(function () {
            $('.select2').select2({ width: '100%' });
            addProductRow(); // Add one default row on load
        });

        let paymentTabShown = false;

        document.getElementById('saveAndNewBtn').addEventListener('click', function (e) {
            const paymentTabElement = document.querySelector('#paymentTab');
            const paymentTabPane = document.querySelector('#payment');

            // Check if the payment tab is already visible
            if (!paymentTabShown || !paymentTabPane.classList.contains('active')) {
                e.preventDefault(); // Prevent submit the first time
                const paymentTab = new bootstrap.Tab(paymentTabElement);
                paymentTab.show();
                paymentTabShown = true;
            } else {
                // Proceed to submit the form on second click
                document.getElementById('productForm').submit();
            }
        });

    </script>
@endsection
