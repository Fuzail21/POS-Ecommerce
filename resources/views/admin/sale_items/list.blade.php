@extends('layouts.app')

@section('content')

    @include('layouts.sidebar')

    <div class="content-page">
      <div class="container-fluid">
         <div class="row">

            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Sales Items</h4>
                        {{-- <p></p> --}}
                    </div>
                    <a href="{{ route('sale.list') }}" class="btn btn-secondary">Back to Sales List</a>

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

                
                  <div class="card-body">
                    <div class="table-responsive">
                        <div class="container">
                            <h2>Items for Sale #{{ $sale->id }}</h2>
                            <p><strong>Customer:</strong> {{ $sale->customer->name ?? 'N/A' }}</p>
                            <p><strong>Total Amount:</strong> ${{ number_format($sale->total_amount, 2) }}</p>

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
                                    @foreach ($sale->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
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
