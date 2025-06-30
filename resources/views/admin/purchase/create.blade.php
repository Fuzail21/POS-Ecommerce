@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            @php
                use App\Models\Setting;
                $setting = Setting::first();
                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
            @endphp
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

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
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

                                <div class="form-group col-md-6">
                                    <label>Warehouse</label>
                                    <select name="warehouse_id" class="form-control" required>
                                        <option value="">Select Warehouse</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
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
                                    <button type="button" class="btn text-white" style="background-color: {{ $secondaryColor }};" onclick="addProductRow()">+ Add Product</button>
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
                            <button type="submit" class="btn text-white" style="background-color: {{ $primaryColor }};" id="saveAndNewBtn">Save</button>
                            <a href="{{ route('purchases.list') }}" class="btn text-white" style="background-color: {{ $secondaryColor }};">Cancel</a>
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
                <td style="min-width: 500px;">
                    <div class="mb-2">
                        <select name="products[${productIndex}][id]" 
                                class="form-control main-product-select select2-ajax text-sm" 
                                data-row="${productIndex}" required>
                        </select>
                    </div>
                    <div>
                        <select name="products[${productIndex}][variant_id]" 
                                class="form-control variant-select" required>
                            <option value="">-- Select Variant --</option>
                        </select>
                    </div>
                </td>
                <td>
                    <input type="number" name="products[${productIndex}][quantity]" 
                           class="form-control quantity-input" min="1" required>
                </td>
                <td>
                    <input type="text" class="form-control unit-name-input" readonly>
                    <input type="hidden" name="products[${productIndex}][unit]" class="unit-id-input">
                </td>
                <td>
                    <input type="number" step="0.01" name="products[${productIndex}][unit_cost]" 
                           class="form-control price-input">
                </td>
                <td>
                    <input type="number" step="0.01" name="products[${productIndex}][subtotal]" 
                           class="form-control subtotal-input" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`;

        $('#product-rows').append(row);
        initSelect2Ajax(productIndex);
        attachEvents();
        productIndex++;
    }

    function initSelect2Ajax(index) {
        $(`select[name="products[${index}][id]"]`).select2({
            placeholder: "-- Select Product --",
            width: '100%',
            ajax: {
                url: '/api/search-products',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(p => ({
                            id: p.id,
                            text: p.name,
                            unit: p.unit,
                            unit_id: p.unit_id,
                            variants: p.variants
                        }))
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            const row = $(this).closest('tr');
            const selectedData = e.params.data;

            // Set unit name and ID
            row.find('.unit-name-input').val(selectedData.unit);
            row.find('.unit-id-input').val(selectedData.unit_id);

            // Populate variants
            const variantSelect = row.find('.variant-select');
            variantSelect.html('<option value="">-- Select Variant --</option>');
            selectedData.variants.forEach(v => {
                variantSelect.append(`<option value="${v.id}">${v.name}</option>`);
            });
        });
    }

    function attachEvents() {
        // Re-attach input and remove events
        $('.quantity-input, .price-input').off('input').on('input', function () {
            const row = $(this).closest('tr');
            updateRowSubtotal(row);
        });

        $('.remove-row').off('click').on('click', function () {
            $(this).closest('tr').remove();
            calculateTotals();
        });
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

        const paymentNow = parseFloat($('#payment_now').val()) || 0;
        const due = totalDue - paymentNow;

        $('#due').val(due.toFixed(2));
    }

    $(document).ready(function () {
        $('#payment_now').on('input', function () {
            calculateTotals();
        });

        addProductRow(); // Add one row by default
    });

    // Tab save handler
    let paymentTabShown = false;
    document.getElementById('saveAndNewBtn').addEventListener('click', function (e) {
        const paymentTabElement = document.querySelector('#paymentTab');
        const paymentTabPane = document.querySelector('#payment');

        if (!paymentTabShown || !paymentTabPane.classList.contains('active')) {
            e.preventDefault();
            const paymentTab = new bootstrap.Tab(paymentTabElement);
            paymentTab.show();
            paymentTabShown = true;
        } else {
            document.getElementById('productForm').submit();
        }
    });
</script>

@endsection
