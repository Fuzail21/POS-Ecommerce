@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
  <div class="container-fluid">
    <div class="row">

      <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
          <div>
            <h4 class="mb-3">{{ isset($expense) ? 'Edit Expense' : 'Add Expense' }}</h4>
          </div>
          <a href="{{ route('expense.list') }}" class="btn btn-secondary">Back to List</a>
        </div>
      </div>

      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <form action="{{ isset($expense) ? route('expense.update', $expense->id) : route('expense.store') }}" method="POST">
              @csrf
              @if(isset($expense))
                @method('PUT')
              @endif

              <div class="form-group">
                <label>Branch <span class="text-danger">*</span></label>
                <select name="branch_id" class="form-control" required>
                  <option value="">Select Branch</option>
                  @foreach($branches as $branch)
                    <option value="{{ $branch->id }}"
                      {{ isset($expense) && $expense->branch_id == $branch->id ? 'selected' : '' }}>
                      {{ $branch->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label>Category <span class="text-danger">*</span></label>
                <select name="category_id" class="form-control" required>
                  <option value="">Select Category</option>
                  @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                      {{ isset($expense) && $expense->category_id == $category->id ? 'selected' : '' }}>
                      {{ $category->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label>Amount <span class="text-danger">*</span></label>
                <input type="number" name="amount" step="0.01" class="form-control" required
                  value="{{ old('amount', $expense->amount ?? '') }}">
              </div>

              <div class="form-group">
                <label>Expense Date <span class="text-danger">*</span></label>
                <input type="date" name="expense_date" class="form-control" required
                  value="{{ old('expense_date', isset($expense) ? \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') : '') }}">
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $expense->description ?? '') }}</textarea>
              </div>

              <button type="submit" class="btn btn-success">
                {{ isset($expense) ? 'Update Expense' : 'Add Expense' }}
              </button>
              <a href="{{ route('expense.list') }}" class="btn btn-secondary">Cancel</a>
            </form>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
