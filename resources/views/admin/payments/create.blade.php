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
                    <div class="card-body">
                        <form action="{{ route('payments.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="entity_type" value="{{ $entityType }}">
                            <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                            <input type="hidden" name="ref_type" value="{{ get_class($reference) }}">
                            <input type="hidden" name="ref_id" value="{{ $reference->id }}">

                            <div class="form-group">
                                <label>Entity Name</label>
                                <input type="text" class="form-control" value="{{ $entity->name }}" disabled>
                            </div>

                            <div class="form-group">
                                <label>Reference ({{ class_basename($reference) }})</label>
                                <input type="text" class="form-control" value="ID #{{ $reference->id }} | Total: ₹{{ number_format($reference->total, 2) }} | Due: ₹{{ number_format($reference->due, 2) }}" disabled>
                            </div>

                            <div class="form-group">
                                <label>Amount to Pay</label>
                                <input type="number" name="amount" class="form-control" step="0.01" max="{{ $reference->due }}" required>
                            </div>

                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Note</label>
                                <textarea name="note" class="form-control" placeholder="Optional note..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Payment</button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
