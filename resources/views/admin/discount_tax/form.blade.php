@extends('layouts.app')
@section('content')
@include('layouts.sidebar')

<div class="content-page">
  <div class="container-fluid add-form-list">
    <h4>Sales Tax & Discount for Sale #{{ $sale->id }}</h4>

    <form method="POST" action="{{ route('discount_taxes.update', $sale->id) }}">
      @csrf

      <div class="form-group">
        <label for="discount">Discount (%)</label>
        <input type="number" step="0.01" name="discount" class="form-control"
               value="{{ old('discount', $tax->discount) }}">
      </div>

      <div class="form-group mt-3">
        <label for="tax">Tax (%)</label>
        <input type="number" step="0.01" name="tax" class="form-control"
               value="{{ old('tax', $tax->tax) }}">
      </div>

      <button class="btn btn-primary mt-4">Save</button>
      <a href="{{ route('payments.list', $sale->id) }}" class="btn btn-secondary mt-4">Back</a>
    </form>
  </div>
</div>
@endsection
