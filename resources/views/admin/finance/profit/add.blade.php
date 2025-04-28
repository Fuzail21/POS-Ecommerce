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
                            <h4 class="card-title">{{ isset($profit) ? 'Edit' : 'Add' }} Profit</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ isset($profit) ? route('profit.update', $profit->id) : route('profit.store') }}" method="POST" data-toggle="validator">
                            @csrf

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Store *</label>
                                        <select class="form-control mb-3" name="store_id" required>
                                            <option value="">Select Store...</option>
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}" {{ (isset($profit) && $profit->store_id == $store->id) ? 'selected' : '' }}>
                                                    {{ $store->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Total Income *</label>
                                        <input type="number" step="0.01" name="total_income" id="total_income" class="form-control" value="{{ old('total_income', $profit->total_income ?? '') }}" placeholder="Enter Total Income" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Total Expense *</label>
                                        <input type="number" step="0.01" name="total_expense" id="total_expense" class="form-control" value="{{ old('total_expense', $profit->total_expense ?? '') }}" placeholder="Enter Total Expense" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Net Profit</label>
                                        <input type="text" id="net_profit" name="net_profit" class="form-control" value="{{ isset($profit) ? number_format($profit->net_profit, 2) : '' }}" placeholder="Net Profit" readonly>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">{{ isset($profit) ? 'Update' : 'Add' }} Profit</button>
                            <a href="{{ route('profit.list') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        function calculateNetProfit() {
            let income = parseFloat(document.getElementById('total_income').value) || 0;
            let expense = parseFloat(document.getElementById('total_expense').value) || 0;
            let netProfit = income - expense;
            document.getElementById('net_profit').value = netProfit.toFixed(2);
        }

        document.getElementById('total_income').addEventListener('input', calculateNetProfit);
        document.getElementById('total_expense').addEventListener('input', calculateNetProfit);

        // Optional: calculate immediately if editing existing profit
        calculateNetProfit();
    });
</script>

@endsection


