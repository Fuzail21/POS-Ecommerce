@extends('layouts.app')
@section('content')
@include('layouts.sidebar')

<div class="content-page"><div class="container-fluid add-form-list">
  <h4>{{ isset($payment)?'Edit':'Add' }} Payment for Sale #{{ $sale->id ?? $payment->sale_id }}</h4>
  <form method="POST" action="{{ isset($payment)
      ? route('payments.update',$payment->id)
      : route('payments.store',$sale->id) }}">
    @csrf
    @if(isset($payment)) @method('PUT') @endif

    <div class="form-group">
      <label>Amount Paid</label>
      <input name="amount_paid" type="number" step="0.01" class="form-control"
             value="{{ old('amount_paid',$payment->amount_paid ?? '') }}">
    </div>

    <div class="form-group mt-3">
      <label>Payment Method</label>
      <select name="payment_method" class="form-control">
        @foreach(['cash','card','online'] as $m)
          <option value="{{ $m }}"
            {{ (old('payment_method',$payment->payment_method ?? '')==$m)?'selected':'' }}>
            {{ ucfirst($m) }}
          </option>
        @endforeach
      </select>
    </div>

    <button class="btn btn-success mt-4">{{ isset($payment)?'Update':'Save' }}</button>
    <a href="{{ route('payments.list',$sale->id ?? $payment->sale_id) }}" class="btn btn-secondary mt-4">Back</a>
  </form>
</div></div>
@endsection
