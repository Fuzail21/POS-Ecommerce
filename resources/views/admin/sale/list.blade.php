@extends('layouts.app')

@section('content')

    @include('layouts.sidebar')

    <div class="content-page">
      <div class="container-fluid">
         <div class="row">

            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Sales List</h4>
                        {{-- <p></p> --}}

                    </div>
                    <a href="{{ route('sale.create') }}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Sale</a>
                </div>
            </div>

            <div class="col-sm-12">
               <div class="card">

                  <!-- Flash Messages -->
                  @if(session('success'))
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                          {{ session('success') }}
                      </div>
                  @endif
                  @if(session('error'))
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          {{ session('error') }}
                      </div>
                  @endif

                  <div class="card-header d-flex justify-content-between">
                     <div class="header-title">
                        <h4 class="card-title">Sales</h4>
                     </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
    <thead>
        <tr class="ligth">
            <th>Id</th>
            <th>Customer</th>
            <th>Total Amount</th>
            <th>Discount</th>
            <th>Tax</th>
            <th>Total</th>
            <th>Payment Status</th>
            <th>User</th>
            <th>Date</th>
            <th colspan="2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sales as $sale)
            <tr>
                <td>{{ $sale->id }}</td>
                <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                <td>${{ number_format($sale->total_amount, 2) }}</td>

                {{-- Discount Display --}}
                <td>
                    @php
                        $discountPercent = $sale->discountTax->discount ?? 0;
                        $discountAmount = ($sale->total_amount * $discountPercent) / 100;
                    @endphp
                    {{ $discountPercent }}% (${{ number_format($discountAmount, 2) }})
                </td>

                {{-- Tax Display --}}
                <td>
                    @php
                        $amountAfterDiscount = $sale->total_amount - $discountAmount;
                        $taxPercent = $sale->discountTax->tax ?? 0;
                        $taxAmount = ($amountAfterDiscount * $taxPercent) / 100;
                    @endphp
                    {{ $taxPercent }}% (${{ number_format($taxAmount, 2) }})
                </td>

                <td>${{ number_format($sale->final_total, 2) }}</td>
                <td>
                    <span class="badge badge-{{ $sale->payment_status == 'paid' ? 'success' : 'warning' }}">
                        {{ ucfirst($sale->payment_status) }}
                    </span>
                </td>
                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                <td>{{ $sale->created_at->format('d M Y') }}</td>
                <td>
                    <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" title="Edit" href="{{ route('sale.edit', $sale->id) }}">
                        <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                    </a>
                    <a class="badge bg-info mr-2 p-1" data-toggle="tooltip" title="View Items" href="{{ route('sale.items', $sale->id) }}">
                        <i class="ri-eye-line" style="font-size: 1.1rem;"></i>
                    </a>
                    <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" href="{{ route('sale.delete', $sale->id) }}">
                        <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                    </a>
                </td>
                <td>
                  <!-- Add Payment Button -->
                  <a href="{{ route('payments.create', $sale->id) }}" class="btn btn-sm btn-primary">Add Payment</a>

                  <!-- Edit Discounts/Taxes Button -->
                  <a href="{{ route('discount_taxes.edit', $sale->id) }}" class="btn btn-sm btn-warning">Discounts & Taxes</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">No sales found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

                    </div>

                    {{-- Pagination Links (outside table) --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $sales->links('pagination::bootstrap-5') }}
                    </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
    </div>

@endsection
