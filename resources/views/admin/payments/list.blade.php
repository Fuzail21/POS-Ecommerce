@extends('layouts.app')
@section('content')
@include('layouts.sidebar')

<div class="content-page"><div class="container-fluid">
  <a href="{{ route('payments.create',$sale->id) }}" class="btn btn-primary mb-3">+ Add Payment</a>
  <h4>Payments for Sale #{{ $sale->id }}</h4>
  <table class="table">
    <thead><tr>
      <th>#</th><th>Amount</th><th>Method</th><th>Date</th><th>Actions</th>
    </tr></thead>
    <tbody>
      @foreach($payments as $p)
      <tr>
        <td>{{ $p->id }}</td>
        <td>{{ $p->amount_paid }}</td>
        <td>{{ ucfirst($p->payment_method) }}</td>
        <td>{{ $p->created_at->format('d M Y') }}</td>
        <td>
          <a href="{{ route('payments.edit',$p->id) }}" class="badge bg-success">✎</a>
          <form method="POST" action="{{ route('payments.destroy',$p->id) }}" class="d-inline">
            @csrf @method('DELETE')
            <button class="badge bg-warning" onclick="return confirm()">🗑</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div></div>
@endsection
