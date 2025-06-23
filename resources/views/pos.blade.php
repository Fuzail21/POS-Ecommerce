@extends('standalone-template')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
    /* Navbar with proper alignment and padding */
    .navbar {
        display: flex;
        justify-content: space-between; /* space between logo/left and right controls */
        align-items: center;
        padding: 10px;
        background-color: #f8f9fa; /* Light grey background */
        border-bottom: 1px solid #ccc;
    }
    
    /* Right side controls (calculator + fullscreen button) */
    .right-controls {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* General button styling */
    .btn {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
    }
    
    /* Dropdown wrapper */
    .dropdown {
        position: relative;
        display: inline-block;
    }
    
    /* Dropdown (calculator) content */
    .dropdown-content {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        z-index: 999;
        background-color: white;
        border: 1px solid #ccc;
        padding: 10px;
        width: 250px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    /* Show dropdown on hover */
    .dropdown-content.show {
        display: block;
    }
    
    /* Calculator box */
    .calculator {
        width: 230px;
    }
    
    /* Display screen */
    .display {
        width: 100%;
        height: 40px;
        margin-bottom: 10px;
        text-align: right;
        padding: 5px;
        font-size: 18px;
        border: none;
        border-radius: 5px;
        background: #fff;
    }
    
    /* Grid for buttons */
    .buttons {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 5px;
    }
    
    /* Button inside calculator */
    .dropdown-content button {
        padding: 10px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        background: #4a90e2;
        color: white;
        cursor: pointer;
    }
    
    /* Hover state */
    .dropdown-content button:hover {
        background: #357abd;
    }
    
    /* Operator-specific styling */
    .operator {
        background: #d9534f;
    }
    
    .operator:hover {
        background: #c9302c;
    }

    .content-page {
        margin-left: 0 !important;
    }
    .wrapper {
        padding-left: 0 !important;
    }

</style>
{{-- @endsection --}}

@section('content')
{{-- @include('layouts.sidebar') --}}

    <div class="navbar">
        <div class="right-controls">

            @php
                use App\Models\Setting;
                $setting = \App\Models\Setting::first();
            @endphp


            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center" type="button" id="registerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-briefcase me-2"></i> Cash Register
                </button>
                <ul class="dropdown-menu" aria-labelledby="registerDropdown">
                    <li>
                        <a id="showRegisterDetailsBtn" class="dropdown-item">
                            <i class="fas fa-info-circle me-2"></i> Register Details
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" id="closeRegisterBtn">
                            <i class="fas fa-lock me-2"></i> Close Register
                        </a>
                    </li>
                </ul>
            </div>

            <div class="dropdown" id="calculatorDropdown">
                <button class="btn" id="toggleCalculator">Calculator</button>
                <div class="dropdown-content" id="calculatorContent">
                    <div class="calculator">
                        <input type="text" class="display" id="display" readonly>
                        <div class="buttons">
                            <button onclick="clearDisplay()">AC</button>
                            <button onclick="appendToDisplay('C')">C</button>
                            <button class="operator" onclick="appendToDisplay('/')">/</button>
                            <button class="operator" onclick="appendToDisplay('*')">×</button>
                            <button onclick="appendToDisplay('7')">7</button>
                            <button onclick="appendToDisplay('8')">8</button>
                            <button onclick="appendToDisplay('9')">9</button>
                            <button class="operator" onclick="appendToDisplay('-')">-</button>
                            <button onclick="appendToDisplay('4')">4</button>
                            <button onclick="appendToDisplay('5')">5</button>
                            <button onclick="appendToDisplay('6')">6</button>
                            <button class="operator" onclick="appendToDisplay('+')">+</button>
                            <button onclick="appendToDisplay('1')">1</button>
                            <button onclick="appendToDisplay('2')">2</button>
                            <button onclick="appendToDisplay('3')">3</button>
                            <button onclick="calculate()">=</button>
                            <button onclick="appendToDisplay('0')">0</button>
                            <button onclick="appendToDisplay('.')">.</button>
                        </div>
                    </div>
                </div>
            </div>


            <button class="btn" onclick="toggleFullScreen()">
                <i id="fullscreen-icon" class="fas fa-expand"></i>
            </button>
        </div>
    </div>



<div class="content-page">
    <div class="container-fluid">
        <div class="row">

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

            <!-- Product Search Panel -->
            <div class="col-md-7">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-5 mb-3">
                                <label for="customer_id">Select Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control select2" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="branch_id">Select Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control select2" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-2 mb-3 text-right">
                                <button type="button" class="btn btn-outline-primary mt-4 w-100" data-toggle="modal" data-target="#addCustomerModal">
                                    + Customer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        <h4>Search Products</h4>
                        <form method="GET" action="{{ route('sales.create') }}" style="display: flex; gap: 10px; align-items: center;">
                            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}" style="flex: 1;" autocomplete="off">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>


                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <div class="row" id="product-list">
                            @foreach($products as $product)
                                @php
                                    $isOutOfStock = !$product->in_stock && $product->variants->count() === 0;
                                @endphp
                                <div class="col-md-3 mb-2 product-item d-flex">
                                    <div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 {{ $isOutOfStock ? 'bg-light text-muted pointer-events-none opacity-50' : '' }}">
                                        @if (!empty($product->product_img))
                                            <img src="{{ asset('storage/' . $product->product_img) }}" alt="Product Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin: auto;">
                                        @else
                                            <div style="width: 100px; height: 100px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin: auto;">N/A</div>
                                        @endif

                                        <h6 class="mt-2 mb-1">{{ $product->name }}</h6>

                                        @if($product->variants->count())
                                            <select class="form-control mb-2 variant-selector mt-auto" data-product-id="{{ $product->id }}">
                                                <option disabled selected>Choose Variant</option>
                                                @foreach($product->variants as $variant)
                                                    <option 
                                                        value="variant-{{ $variant->id }}"
                                                        data-name="{{ $product->name }} - {{ $variant->variant_name }}"
                                                        data-price="{{ $variant->sale_price }}"
                                                        data-stock="{{ $variant->stock_quantity }}"
                                                        data-unit-id="{{ $product->default_display_unit_id }}"
                                                        {{ !$variant->in_stock ? 'disabled' : '' }}>
                                                        {{ $variant->variant_name }} - {{ $setting->currency_symbol }} {{ number_format($variant->sale_price, 2) }}
                                                        {{ !$variant->in_stock ? '(Out of Stock)' : '(Stock: '.$variant->stock_quantity.')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>
                                        @else
                                            <p class="mb-1">{{ $setting->currency_symbol }} {{ number_format($product->sale_price, 2) }} 
                                                <br><small>(Stock: {{ $product->stock_quantity }})</small>
                                            </p>
                                            @if($product->in_stock)
                                                <button 
                                                    class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart"
                                                    data-id="product-{{ $product->id }}"
                                                    data-name="{{ $product->name }}"
                                                    data-price="{{ $product->sale_price }}"
                                                    data-stock="{{ $product->stock_quantity }}"
                                                    data-unit-id="{{ $product->default_display_unit_id }}">
                                                    Add to Cart
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary w-100 mt-auto" disabled>Out of Stock</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Panel -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Shopping Cart</h4>
                    </div>
                    <div id="stock-error" class="alert alert-danger d-none" role="alert"></div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items"></tbody>
                        </table>

                    <div style="margin-top: 15px;">

                        <!-- Tax -->
                        <div style="margin-bottom: 10px;">
                            <label for="tax">Tax</label><br>
                            <div style="display: flex; align-items: center;">
                                <span style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;">%</span>
                                <input type="number" name="taxrate" id="taxrate" placeholder="Tax" autocomplete="off"
                                       style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-left: none; border-radius: 0 4px 4px 0;">
                            </div>
                        </div>

                        <!-- Discount Type -->
                        <div style="margin-top: 8px;">
                            <label>Discount Type:</label><br>
                            <label>
                                <input type="radio" name="discount_type" value="fixed" checked onclick="updateDiscountSymbol()"> Fixed
                            </label>
                            <label style="margin-left: 15px;">
                                <input type="radio" name="discount_type" value="percentage" onclick="updateDiscountSymbol()"> Percentage
                            </label>
                        </div>

                        <!-- Discount -->
                        <div style="margin-bottom: 10px;">
                            <label for="discount">Discount</label><br>
                            <div style="display: flex; align-items: center;">
                                <span id="discount-symbol" style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;"> {{ $setting->currency_symbol }} </span>
                                <input type="number" name="discountRate" id="discountRate" placeholder="Discount" autocomplete="off"
                                       style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-left: none; border-radius: 0 4px 4px 0;">
                            </div>
                        </div>

                        <!-- Shipping -->
                        <div style="margin-bottom: 10px;">
                            <label for="shipping">Shipping</label><br>
                            <div style="display: flex; align-items: center;">
                                <span style="padding: 6px 10px; background-color: #f1f1f1; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;"> {{ $setting->currency_symbol }} </span>
                                <input type="number" name="shippingRate" id="shippingRate" placeholder="Shipping" autocomplete="off"
                                       style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-left: none; border-radius: 0 4px 4px 0;">
                            </div>
                        </div>

                    </div>


                        <div class="mt-3">
                            <p><strong>Subtotal:</strong> {{ $setting->currency_symbol }} <span id="subtotal">0.00</span></p>
                            <p><strong>Discount:</strong> {{ $setting->currency_symbol }} <span id="discount">0.00</span></p>
                            <p><strong>Tax:</strong> {{ $setting->currency_symbol }} <span id="tax">0.00</span></p>
                            <p><strong>Shipping:</strong> {{ $setting->currency_symbol }} <span id="shipping">0.00</span></p>
                            <hr>
                            <h5><strong>Total:</strong> {{ $setting->currency_symbol }} <span id="total">0.00</span></h5>
                        </div>

                        <div class="mt-3 d-flex justify-content-between">
                            <button class="btn btn-danger" id="reset-cart">Cancel</button>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#checkoutModal" id="open-checkout" disabled>Charge</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="checkout-form" method="POST" action="{{ route('checkout.pos') }}">
                @csrf
                <input type="hidden" name="cart_data" id="cart_data">
                <input type="hidden" name="total_payable" id="total_payable">
                <input type="hidden" id="subtotal_hidden" name="subtotal">
                <input type="hidden" id="tax_hidden" name="tax">
                <input type="hidden" id="discount_hidden" name="discount">
                <input type="hidden" id="shipping_hidden" name="shipping">
                <input type="hidden" name="balance_due" id="balance_due_raw">
                <input type="hidden" name="customer_id" id="selected_customer_id">
                <input type="hidden" name="branch_id" id="selected_branch_id">


                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Payment</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount Paid</label>
                            <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Balance / Change</label>
                            <input type="text" id="balance_display" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Confirm Payment</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('customers.store') }}">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email (optional)</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Address (optional)</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save Customer</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>


@if(session('show_invoice') && session('sale_id'))
    @php
        $sale = \App\Models\Sale::with(['items.product', 'customer'])->find(session('sale_id'));
        $invoiceHtml = view('partials.invoice', compact('sale'))->render();
        session()->forget(['show_invoice', 'sale_id']);
    @endphp

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                title: 'Invoice Preview',
                html: {!! json_encode($invoiceHtml) !!},
                width: 600,
                showCancelButton: true,
                confirmButtonText: 'Print',
                cancelButtonText: 'Close',
                didOpen: () => {
                    // Optional styling tweaks or JS initialization
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const content = document.getElementById('invoice-content').innerHTML;
                    const printWindow = window.open('', '', 'width=800,height=600');
                    printWindow.document.write('<html><head><title>Invoice</title></head><body>' + content + '</body></html>');
                    printWindow.document.close();
                    printWindow.print();
                }
            });
        });
    </script>
@endif


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



<!-- JavaScript -->
<script>

        document.getElementById('showRegisterDetailsBtn').addEventListener('click', async () => {
            // Show a loading spinner while fetching data
            Swal.fire({
                title: 'Fetching Register Details...',
                html: '<div class="spinner-border animate-spin inline-block w-8 h-8 border-4 rounded-full text-blue-500" role="status"><span class="visually-hidden">Loading...</span></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // *** IMPORTANT: Replace '/pos/register-details' with your actual Laravel route ***
                const response = await fetch('/pos/register-details', {
                    method: 'GET', // Or 'POST' if your route is configured that way
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        // If you are using Laravel CSRF protection with AJAX, uncomment and set the token:
                        // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                });

                const data = await response.json();

                // Close the loading spinner
                Swal.close();

                if (response.ok && data.details) {
                    const { payment_type, total_sales, total_refund, total_payment, date } = data.details;

                    // Construct the HTML content for the SweetAlert popup

                    const sanitized = (value) => {
                        return value.replace(/^\s*\$\s*/, currencySymbol + ' ');
                    };
                    const popupHtml = `
                        <div class="space-y-4">
                            <!-- Payment Type & Amount Section -->
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-gray-700 text-sm md:text-base">
                                <div class="col-span-1 font-semibold text-gray-800">Payment Type</div>
                                <div class="col-span-1 font-semibold text-right text-gray-800">Amount</div>

                                ${Object.entries(payment_type).map(([type, amount]) => `
                                    <div class="col-span-1 py-2 px-3 bg-gray-50 rounded-lg shadow-sm">${type}:</div>
                                    <div class="col-span-1 py-2 px-3 bg-gray-50 rounded-lg shadow-sm text-right font-medium">${sanitized(amount)}</div>
                                `).join('')}
                            </div>

                            <!-- Totals Section -->
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-gray-700 text-sm md:text-base pt-4 border-t border-gray-200">
                                <div class="col-span-1 py-2 px-3 bg-gray-50 rounded-lg shadow-sm">Total Sales:</div>
                                <div class="col-span-1 py-2 px-3 bg-gray-50 rounded-lg shadow-sm text-right font-medium">${sanitized(total_sales)}</div>

                                <div class="col-span-1 py-2 px-3 bg-gray-50 rounded-lg shadow-sm">Total Refund:</div>
                                <div class="col-span-1 py-2 px-3 bg-gray-50 rounded-lg shadow-sm text-right font-medium">${sanitized(total_refund)}</div>

                                <div class="col-span-1 py-2 px-3 bg-gray-50 rounded-lg shadow-sm">Total Payment:</div>
                                <div class="col-span-1 py-2 px-3 bg-gray-50 rounded-lg shadow-sm text-right font-medium">${sanitized(total_payment)}</div>
                            </div>
                        </div>
                    `;

                    Swal.fire({
                        title: `Register Details - (${date})`,
                        html: popupHtml,
                        width: '700px', // Increased popup width
                        showCancelButton: true,
                        confirmButtonText: 'Print',
                        cancelButtonText: 'Close',
                        customClass: {
                            container: 'swal2-container',
                            popup: 'swal2-popup',
                            title: 'swal2-title text-lg font-semibold text-gray-800 mb-3', // Slightly smaller & bolder title
                            htmlContainer: 'swal2-html-container text-sm text-gray-700', // Smaller, cleaner text
                            confirmButton: 'swal2-confirm-button text-black font-bold py-2 px-5 rounded-lg focus:outline-none transition',
                            cancelButton: 'swal2-cancel-button text-black font-bold py-2 px-5 rounded-lg focus:outline-none ms-2 transition',
                            actions: 'swal2-actions flex justify-end w-full mt-4 gap-2'
                        },
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Logic for the "Print" button
                            // In a real application, you might trigger a print dialog or a server-side print action
                            console.log('Print button clicked!');
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            console.log('Popup closed');
                        }
                    });
                } else {
                     Swal.fire({
                        icon: 'info',
                        title: 'No Register Details',
                        text: data.message || 'Could not retrieve register details.',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'swal2-confirm-button bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline',
                            popup: 'swal2-popup',
                            title: 'swal2-title',
                            htmlContainer: 'swal2-html-container'
                        },
                        buttonsStyling: false,
                    });
                }
            } catch (error) {
                // Close the loading spinner on error
                Swal.close();
                console.error('Error fetching register details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while fetching register details. Please try again.',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal2-confirm-button bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline',
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        htmlContainer: 'swal2-html-container'
                    },
                    buttonsStyling: false,
                });
            }
        });

    //CLOSE Register    
    document.addEventListener('DOMContentLoaded', function () {
        const closeBtn = document.getElementById('closeRegisterBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
                e.preventDefault();
    
                Swal.fire({
                    title: 'Close Register?',
                    text: "This will finalize today's session.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, close it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('pos.closeRegister') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Closed',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true,
                                    didClose: () => {
                                        window.location.href = data.redirect;
                                    }
                                });
                            } else {
                                Swal.fire('Error', data.message || 'Something went wrong while closing the register.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Something went wrong while closing the register.', 'error');
                            console.error(error);
                        });
                    }
                });
            });
        }
    });

    const currencySymbol = @json($setting->currency_symbol);
    function updateDiscountSymbol() {
        var symbol = document.getElementById('discount-symbol');
        var selectedType = document.querySelector('input[name="discount_type"]:checked').value;
        symbol.textContent = selectedType === 'percentage' ? '%' : currencySymbol;
    }
    document.addEventListener('DOMContentLoaded', () => {
        const cart = {};

        // Declare variables to hold real-time values
        let taxValue = 0;
        let discountValue = 0;
        let discountType = 'fixed';
        let shipping =  0;

        // Input listeners to update variables
        document.getElementById('taxrate').addEventListener('input', function () {
            taxValue = parseFloat(this.value) || 0;
            updateCartUI();
        });

        document.getElementById('discountRate').addEventListener('input', function () {
            discountValue = parseFloat(this.value) || 0;
            updateCartUI();
        });

        document.getElementById('shippingRate').addEventListener('input', function () {
            shipping = parseFloat(this.value) || 0;
            updateCartUI();
        });

        document.querySelectorAll('input[name="discount_type"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                discountType = this.value;
                updateDiscountSymbol(); // Update symbol UI
                updateCartUI();
            });
        });

        // Elements
        const cartItemsEl = document.getElementById('cart-items');
        const subtotalEl = document.getElementById('subtotal');
        const discountEl = document.getElementById('discount');
        const shippingEl = document.getElementById('shipping');
        const taxEl = document.getElementById('tax');
        const totalEl = document.getElementById('total');
        const chargeBtn = document.getElementById('open-checkout');
        const resetBtn = document.getElementById('reset-cart');
        const amountPaidInput = document.getElementById('amount_paid');
        const balanceDisplay = document.getElementById('balance_display');
        const cartDataInput = document.getElementById('cart_data');
        const totalPayableInput = document.getElementById('total_payable');
        const customerSelect = document.getElementById('customer_id');
        const selectedCustomerInput = document.getElementById('selected_customer_id');
        const branchSelect = document.getElementById('branch_id');
        const selectedBranchInput = document.getElementById('selected_branch_id');

        selectedCustomerInput.value = customerSelect.value;
        selectedBranchInput.value = branchSelect.value;

        customerSelect.addEventListener('change', () => {
            selectedCustomerInput.value = customerSelect.value;
        });

        branchSelect.addEventListener('change', () => {
            selectedBranchInput.value = branchSelect.value;
        });

        function updateCartUI() {
            cartItemsEl.innerHTML = '';
            let subtotal = 0;

            for (const id in cart) {
                const item = cart[id];
                const lineTotal = item.sale_price * item.qty;
                subtotal += lineTotal;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.name}</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary btn-decrement" data-id="${id}">-</button>
                            </div>
                            <input type="text" class="form-control text-center" value="${item.qty}" readonly style="max-width: 50px;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-increment" data-id="${id}">+</button>
                            </div>
                        </div>
                    </td>
                    <td>${currencySymbol} ${lineTotal.toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-danger btn-remove" data-id="${id}">&times;</button></td>
                `;
                cartItemsEl.appendChild(tr);
            }

            // Calculate discount
            const discount = discountType === 'percentage'
                ? (discountValue / 100) * subtotal
                : discountValue;

            // Calculate tax
            const tax = (subtotal - discount) * (taxValue / 100);

            // Final total
            const total = subtotal - discount + tax + shipping;

            // Update UI values
            subtotalEl.textContent = subtotal.toFixed(2);
            discountEl.textContent = discount.toFixed(2);
            taxEl.textContent = tax.toFixed(2);
            shippingEl.textContent = shipping.toFixed(2); // ✅ Add this
            totalEl.textContent = total.toFixed(2);

            document.getElementById('subtotal_hidden').value = subtotal.toFixed(2);
            document.getElementById('discount_hidden').value = discount.toFixed(2);
            document.getElementById('tax_hidden').value = tax.toFixed(2);
            document.getElementById('shipping_hidden').value = shipping.toFixed(2);

            totalPayableInput.value = total.toFixed(2);

            // Serialize cart
            const structuredCart = {};
            for (const id in cart) {
                const [type, numericId] = id.split('-');
                structuredCart[id] = {
                    type: type,
                    id: parseInt(numericId),
                    ...cart[id]
                };
            }

            cartDataInput.value = JSON.stringify(structuredCart);
            chargeBtn.disabled = Object.keys(cart).length === 0;
        }

        function addToCart(id, name, price, unit_id, stock) {
            // If already in cart
            if (cart[id]) {
                // ✅ Ensure stock is always updated in case it's missing or wrong
                cart[id].stock = stock;

                if (cart[id].qty < cart[id].stock) {
                    cart[id].qty++;
                } else {
                    showStockError('Stock exceeds available quantity.');
                    return;
                }
            } else {
                // Add for first time
                cart[id] = {
                    name,
                    sale_price: price,
                    unit_id,
                    qty: 1,
                    stock: stock
                };
            }

            updateCartUI();
        }


        document.querySelectorAll('.variant-selector').forEach(selector => {
            selector.addEventListener('change', function () {
                const card = this.closest('.product-item');
                const selected = this.options[this.selectedIndex];
                const id = selected.value;
                const name = selected.dataset.name;
                const price = parseFloat(selected.dataset.price);
                const unit_id = selected.dataset.unitId; // ✅ FIXED HERE
                const addBtn = card.querySelector('.add-variant-to-cart');

                addBtn.disabled = false;
                addBtn.dataset.id = id;
                addBtn.dataset.name = name;
                addBtn.dataset.price = price;
                addBtn.dataset.unitId = unit_id;
            });
        });


        document.querySelectorAll('.add-variant-to-cart').forEach(btn => {
            btn.addEventListener('click', () => {
                const card = btn.closest('.card');
                const selectedOption = card.querySelector('.variant-selector option:checked');
                const stock = parseInt(selectedOption.dataset.stock);
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const price = parseFloat(btn.getAttribute('data-price'));
                const unit_id = btn.getAttribute('data-unit-id');

                // Ensure cart item exists and check quantity
                const currentQty = cart[id]?.qty || 0;

                if (currentQty >= stock) {
                    showStockError('Stock exceeds available quantity.');
                    return;
                }

                addToCart(id, name, price, unit_id, stock);
            });
        });



        document.querySelectorAll('.add-simple-to-cart').forEach(btn => {
            const quantity = $(this).closest('.card').find('.simple-quantity').val();
            const maxStock = $(this).data('stock');

            if (parseInt(quantity) > maxStock) {
                alert('Requested quantity exceeds available stock.');
                return;
            }

            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const price = parseFloat(btn.getAttribute('data-price'));
                const unit_id = btn.getAttribute('data-unit-id');
                const stock = parseInt(btn.getAttribute('data-stock')); // ✅ ADD THIS

                addToCart(id, name, price, unit_id, stock); // ✅ PASS STOCK
            });
        });

        cartItemsEl.addEventListener('click', e => {
            const btn = e.target;
            const id = btn.getAttribute('data-id');
            if (!id) return;

            if (btn.classList.contains('btn-increment')) {
                if (cart[id].qty < cart[id].stock) {
                    cart[id].qty++;
                } else {
                    showStockError('Stock exceeds available quantity.');
                }
            } else if (btn.classList.contains('btn-decrement')) {
                cart[id].qty > 1 ? cart[id].qty-- : delete cart[id];
            } else if (btn.classList.contains('btn-remove')) {
                delete cart[id];
            }
            updateCartUI();
        });


        resetBtn.addEventListener('click', () => {
            Object.keys(cart).forEach(key => delete cart[key]);
            updateCartUI();
        });

        amountPaidInput.addEventListener('input', () => {
            const total = parseFloat(totalEl.textContent) || 0;
            const paid = parseFloat(amountPaidInput.value) || 0;
            const due = total - paid;
            balanceDisplay.value = (due >= 0 ? 'Due: ' : 'Change: ') + currencySymbol + ' ' + Math.abs(due).toFixed(2);
            document.getElementById('balance_due_raw').value = due.toFixed(2);
        });

        $('#checkoutModal').on('show.bs.modal', () => {
            amountPaidInput.value = '';
            balanceDisplay.value = '';
        });

        document.getElementById('product-search').addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.getAttribute('data-name') || '';
                item.style.display = name.includes(query) ? '' : 'none';
            });
        });

        function showStockError(message) {
        const errorDiv = document.getElementById('stock-error');
        errorDiv.textContent = message;
        errorDiv.classList.remove('d-none');

        // Auto-hide after 3 seconds
        setTimeout(() => {
            errorDiv.classList.add('d-none');
        }, 3000);
    }
    });

    const toggleButton = document.getElementById('toggleCalculator');
    const dropdown = document.getElementById('calculatorContent');

    toggleButton.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
    });

    document.addEventListener('click', function (event) {
        const isClickInside = dropdown.contains(event.target) || toggleButton.contains(event.target);
        if (!isClickInside) {
            dropdown.classList.remove('show');
        }
    });

    // Calculator functions
    const display = document.getElementById('display');

    function appendToDisplay(value) {
        if (value === 'C') {
            display.value = display.value.slice(0, -1);
        } else {
            display.value += value;
        }
    }

    function clearDisplay() {
        display.value = '';
    }

    function calculate() {
        try {
            display.value = eval(display.value) || 0;
        } catch (e) {
            display.value = 'Error';
            setTimeout(clearDisplay, 1000);
        }
    }

    function toggleFullScreen() {
        const icon = document.getElementById('fullscreen-icon');
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            icon.classList.remove('fa-expand');
            icon.classList.add('fa-compress');
        } else {
            document.exitFullscreen();
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }
</script>
@endsection
