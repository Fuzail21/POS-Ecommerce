@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
  <div class="container-fluid">
    <div class="row">

      <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
          <div>
            <h4 class="mb-3">Stock Adjustments List</h4>
          </div>
          <a href="{{ route('stock_adjustments.create') }}" class="btn btn-primary add-list">
            <i class="las la-plus mr-3"></i>Add Adjustment
          </a>
        </div>
      </div>

      <div class="col-sm-12">
        <div class="card">

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
              <h4 class="card-title">Stock Adjustments</h4>
            </div>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table id="datatable" class="table data-tables table-striped">
                <thead>
                  <tr class="ligth">
                    <th>Id</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Reason</th>
                    <th>Date</th>
                    <th colspan="2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($adjustments as $adj)
                    <tr>
                      <td>{{ $adj->id }}</td>
                      <td>{{ $adj->product->name ?? 'N/A' }}</td>
                      <td>{{ ucfirst($adj->adjustment_type) }}</td>
                      <td>{{ $adj->quantity }}</td>
                      <td>{{ Str::limit($adj->reason, 30) }}</td>
                      <td>{{ $adj->created_at->format('d M, Y') }}</td>
                      <td>
                        <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" title="Edit" href="{{ route('stock_adjustments.edit', $adj->id) }}">
                          <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                        </a>
                        {{-- <a class="badge bg-info mr-2 p-1" data-toggle="tooltip" title="View" href="#">
                          <i class="ri-eye-line" style="font-size: 1.1rem;"></i>
                        </a> --}}
                        <a class="badge bg-warning mr-2 p-1 border-0" data-toggle="tooltip" title="Delete" href="{{ route('stock_adjustments.destroy', $adj->id) }}">
                            <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="8" class="text-center">No records found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

@endsection
