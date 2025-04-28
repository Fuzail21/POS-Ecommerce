@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <div class="header-title">
              <h4 class="card-title">{{ isset($purchase) ? 'Edit Purchase Entry' : 'New Purchase Entry' }}</h4>
            </div>
          </div>

          <div class="card-body">
            <form action="{{ isset($purchase) ? route('purchase.update', $purchase->id) : route('purchase.store') }}" method="POST">
              @csrf
              @if(isset($purchase))
                @method('PUT') <!-- This ensures a PUT request for update -->
              @endif

              <div class="form-group">
                <label for="vendor_id">Select Vendor <span class="text-danger">*</span></label>
                <select name="vendor_id" id="vendor_id" class="form-control" required>
                  <option value="">-- Select Vendor --</option>
                  @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ isset($purchase) && $purchase->vendor_id == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                  @endforeach
                </select>
              </div>

              <hr class="my-4">

              <h5>Purchase Items</h5>

              <table class="table table-bordered" id="purchaseItemsTable">
                <thead class="thead-light">
                  <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Cost</th>
                    <th><button type="button" class="btn btn-sm btn-success" onclick="addRow()">+</button></th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($purchase))
                    @foreach($purchase->items as $index => $item)
                    <tr>
                      <td>
                        <select name="items[{{ $index }}][product_id]" class="form-control" required>
                          <option value="">-- Select Product --</option>
                          @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                          @endforeach
                        </select>
                      </td>
                      <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control qty" value="{{ $item->quantity }}" required></td>
                      <td><input type="number" name="items[{{ $index }}][cost]" step="0.01" class="form-control cost" value="{{ $item->cost }}" required></td>
                      <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">×</button></td>
                    </tr>
                    @endforeach
                  @else
                    <!-- Default row for adding a new purchase -->
                    <tr>
                      <td>
                        <select name="items[0][product_id]" class="form-control" required>
                          <option value="">-- Select Product --</option>
                          @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                          @endforeach
                        </select>
                      </td>
                      <td><input type="number" name="items[0][quantity]" class="form-control qty" required></td>
                      <td><input type="number" name="items[0][cost]" step="0.01" class="form-control cost" required></td>
                      <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">×</button></td>
                    </tr>
                  @endif
                </tbody>
              </table>

              <div class="form-group mt-4">
                <label for="total_amount">Total Amount</label>
                <input type="text" name="total_amount" id="total_amount" class="form-control" value="{{ isset($purchase) ? $purchase->total_amount : '' }}" readonly>
              </div>

              <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">{{ isset($purchase) ? 'Update Purchase' : 'Save Purchase' }}</button>
                <a href="{{ route('purchase.list') }}" class="btn btn-secondary">Cancel</a>

              </div>
            </form>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
    let rowCount = {{ isset($purchase) ? count($purchase->items) : 1 }};

    function addRow() {
        let table = document.getElementById('purchaseItemsTable').getElementsByTagName('tbody')[0];
        let newRow = table.insertRow();
        newRow.innerHTML = `
            <td>
                <select name="items[${rowCount}][product_id]" class="form-control" required>
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="items[${rowCount}][quantity]" class="form-control qty" required></td>
            <td><input type="number" name="items[${rowCount}][cost]" step="0.01" class="form-control cost" required></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">×</button></td>
        `;
        rowCount++;
        bindCalculationEvents();
        calculateTotal();
    }

    function removeRow(button) {
        let row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        const rows = document.querySelectorAll('#purchaseItemsTable tbody tr');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty')?.value || 0);
            const cost = parseFloat(row.querySelector('.cost')?.value || 0);
            total += qty * cost;
        });
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    function bindCalculationEvents() {
        document.querySelectorAll('.qty, .cost').forEach(input => {
            input.removeEventListener('input', calculateTotal); // avoid multiple binds
            input.addEventListener('input', calculateTotal);
        });
    }

    // Initial bind
    bindCalculationEvents();
</script>

@endsection
