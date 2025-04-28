@extends('layouts.app')

@section('content')

    @include('layouts.sidebar')

    <div class="content-page">
      <div class="container-fluid">
         <div class="row">

            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Purchase Items</h4>
                    </div>
                    <a href="{{ route('purchase.list') }}" class="btn btn-secondary">Back to Purchase List</a>
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

                  <div class="card-body">
                    <div class="table-responsive">
                        <div class="container">
                            <h2>Items for Purchase #{{ $purchase->id }}</h2>
                            <p><strong>Vendor:</strong> {{ $purchase->vendor->name ?? 'N/A' }}</p>
                            <p><strong>Total Amount:</strong> ${{ number_format($purchase->total_amount, 2) }}</p>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price (Each)</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->cost, 2) }}</td>
                                            <td>${{ number_format($item->quantity * $item->cost, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>
                  </div>
               </div>
            </div>

         </div>
      </div>
    </div>

@endsection
