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
                            <h4 class="card-title">{{ isset($expense) ? 'Edit' : 'Add' }} Expense</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($expense) ? route('expense.update', $expense->id) : route('expense.store') }}" method="POST" data-toggle="validator">
                            @csrf

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Store *</label>
                                        <select class="form-control mb-3" name="store_id" required>
                                            <option value="">Select Store...</option>
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}" {{ (isset($expense) && $expense->store_id == $store->id) ? 'selected' : '' }}>
                                                    {{ $store->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Expense Type *</label>
                                        <input type="text" name="expense_type" class="form-control" value="{{ old('expense_type', $expense->expense_type ?? '') }}" placeholder="Enter Expense Type" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Amount *</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount ?? '') }}" placeholder="Enter Amount" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">{{ isset($expense) ? 'Update' : 'Add' }} Expense</button>
                            <a href="{{ route('expense.list') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
