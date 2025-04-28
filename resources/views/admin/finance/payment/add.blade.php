@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ isset($payment) ? 'Edit' : 'Add' }} Payment</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($payment) ? route('payment.update', $payment->id) : route('payment.store') }}" method="POST" data-toggle="validator">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Loan *</label>
                                        <select class="form-control mb-3" name="loan_id" required>
                                            <option value="">Select Loan...</option>
                                            @foreach($loans as $loan)
                                                <option value="{{ $loan->id }}" {{ (isset($payment) && $payment->loan_id == $loan->id) ? 'selected' : '' }}>
                                                   Loan #{{ $loan->id }} - 
                                                    @if($loan->user_type == 'customer' && $loan->customer)
                                                        {{ $loan->customer->name }} (Customer)
                                                    @elseif($loan->user_type == 'vendor' && $loan->vendor)
                                                        {{ $loan->vendor->name }} (Vendor)
                                                    @else
                                                        No Name
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Amount Paid *</label>
                                        <input type="number" step="0.01" name="amount_paid" class="form-control" value="{{ old('amount_paid', $payment->amount_paid ?? '') }}" placeholder="Enter Paid Amount" required>
                                        @if ($errors->has('amount_paid'))
                                            <div class="alert alert-danger">{{ $errors->first('amount_paid') }}</div>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>Payment Date *</label>
                                        <input type="datetime-local" name="payment_date" class="form-control" 
                                            value="{{ old('payment_date', isset($payment) ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" 
                                            required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">{{ isset($payment) ? 'Update' : 'Add' }} Payment</button>
                            <a href="{{ route('payment.list') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
