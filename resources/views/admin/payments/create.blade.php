@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Add Payment</h4>
                    </div>

                        @php
                            use App\Models\Setting;
                            $setting = \App\Models\Setting::first();
                        @endphp

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

                    <div class="card-body">
                        <form action="{{ route('payments.store') }}" method="POST">
                            @csrf

                            {{-- Entity Type Dropdown --}}
                            <div class="form-group">
                                <label>Entity Type</label>
                                <select id="entityType" name="entity_type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="customer">Customer</option>
                                    <option value="supplier">Supplier</option>
                                </select>
                            </div>

                            <input type="hidden" name="ref_type" class="form-control" value="">

                            {{-- Entity Dropdown --}}
                            <div class="form-group d-none" id="entitySelectContainer">
                                <label id="entityLabel"></label>
                                <select id="entitySelect" name="entity_id" class="form-control" required>
                                    <option value="">Select</option>
                                </select>
                            </div>

                            {{-- Reference Dropdown --}}
                            <div class="form-group d-none" id="referenceContainer">
                                <label>Reference</label>
                                <select id="referenceSelect" name="ref_id" class="form-control" required>
                                    <option value="">Select Reference</option>
                                </select>
                            </div>

                            {{-- Amount --}}
                            <div class="form-group">
                                <label>Amount to Pay</label>
                                <input type="number" name="amount" class="form-control" step="0.01" required>
                            </div>

                            {{-- Payment Method --}}
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>

                            {{-- Note --}}
                            <div class="form-group">
                                <label>Note</label>
                                <textarea name="note" class="form-control" placeholder="Optional note..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Payment</button>
                            <a href="{{ route('payments.list') }}" class="btn btn-secondary">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const customers = @json($customers);
    const suppliers = @json($suppliers);
    const currencySymbol = @json($setting->currency_symbol);


    document.getElementById('entityType').addEventListener('change', function () {
        const type = this.value;
        const entitySelect = document.getElementById('entitySelect');
        const entityLabel = document.getElementById('entityLabel');
        const referenceSelect = document.getElementById('referenceSelect');

        // ✅ Set ref_type value based on selected entity type
        document.querySelector('input[name="ref_type"]').value = type === 'customer' ? 'sale' : 'purchase';

        entitySelect.innerHTML = '<option value="">Select</option>';
        referenceSelect.innerHTML = '<option value="">Select Reference</option>';

        if (type === 'customer') {
            entityLabel.innerText = "Select Customer";
            for (const customer_id in customers) {
                const name = customers[customer_id][0].customer.name;
                entitySelect.innerHTML += `<option value="${customer_id}">${name}</option>`;
            }
        } else if (type === 'supplier') {
            entityLabel.innerText = "Select Supplier";
            for (const supplier_id in suppliers) {
                const name = suppliers[supplier_id][0].supplier.name;
                entitySelect.innerHTML += `<option value="${supplier_id}">${name}</option>`;
            }
        }

        document.getElementById('entitySelectContainer').classList.remove('d-none');
        document.getElementById('referenceContainer').classList.add('d-none');
    });


    document.getElementById('entitySelect').addEventListener('change', function () {
        const entityId = this.value;
        const type = document.getElementById('entityType').value;
        const referenceSelect = document.getElementById('referenceSelect');

        referenceSelect.innerHTML = '<option value="">Select Reference</option>';
        let references = [];

        if (type === 'customer') {
            references = customers[entityId];
        } else if (type === 'supplier') {
            references = suppliers[entityId];
        }

        if (references) {
            references.forEach(ref => {
                const id = ref.id;
                const invoice = ref.invoice_number || 'N/A';
                const due = parseFloat(ref.due_amount).toFixed(2); // <-- FIXED here
                referenceSelect.innerHTML += `<option value="${id}">${id} - ${invoice} - ${currencySymbol} ${due}</option>`;
            });
            document.getElementById('referenceContainer').classList.remove('d-none');
        }
    });
</script>
@endsection
