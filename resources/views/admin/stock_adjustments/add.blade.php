@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
  <div class="container-fluid add-form-list">
    <div class="row">
      <div class="col-sm-12">
        <form method="POST" action="{{ isset($adjustment) ? route('stock_adjustments.update', $adjustment->id) : route('stock_adjustments.store') }}">
          @csrf
          @if(isset($adjustment)) @method('PUT') @endif

          <div class="card">
            <div class="card-header">
              <h4 class="card-title">{{ isset($adjustment) ? 'Edit Adjustment' : 'Add Adjustment' }}</h4>
            </div>
            <div class="card-body">

              <div class="form-group mb-3">
                <label for="product_id">Product <span class="text-danger">*</span></label>
                <select name="product_id" id="product_id" class="form-control" required>
                  <option value="">Select Product...</option>
                  @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ isset($adjustment) && $adjustment->product_id == $product->id ? 'selected' : '' }}>
                      {{ $product->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group mb-3">
                <label for="adjustment_type">Type <span class="text-danger">*</span></label>
                <select name="adjustment_type" id="adjustment_type" class="form-control" required>
                  <option value="">Select Type...</option>
                  <option value="damage" {{ isset($adjustment) && $adjustment->adjustment_type=='damage' ? 'selected':'' }}>Damage</option>
                  <option value="return" {{ isset($adjustment) && $adjustment->adjustment_type=='return' ? 'selected':'' }}>Return</option>
                  <option value="expiry" {{ isset($adjustment) && $adjustment->adjustment_type=='expiry' ? 'selected':'' }}>Expiry</option>
                </select>
              </div>

              <div class="form-group mb-3">
                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                <input type="number" name="quantity" id="quantity" class="form-control" min="1"
                       value="{{ old('quantity', $adjustment->quantity ?? '') }}" required>
              </div>

              <div class="form-group mb-3">
                <label for="reason">Reason</label>
                <textarea name="reason" id="reason" class="form-control" rows="3">{{ old('reason', $adjustment->reason ?? '') }}</textarea>
              </div>

            </div>
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-success">{{ isset($adjustment) ? 'Update' : 'Save' }}</button>
              <a href="{{ route('stock_adjustments.list') }}" class="btn btn-secondary">Cancel</a>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

@endsection
