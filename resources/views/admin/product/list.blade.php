@extends('layouts.app')

@section('content')

    @include('layouts.sidebar')

    <div class="content-page">
      <div class="container-fluid">
         <div class="row">

            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Products List</h4>
                        {{-- <p></p> --}}

                    </div>
                    <a href="{{ route('product.create') }}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Product</a>
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
                        <h4 class="card-title">Products</h4>
                     </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                       <table id="datatable" class="table data-tables table-striped">
                          <thead>
                             <tr class="ligth">
                                <th>Id</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Company</th>
                                <th>Store</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                                <th>Stock Qty</th>
                                <th>Unit</th>
                                <th>Expiry Date</th>

                                <th colspan="2">Action</th>
                             </tr>
                          </thead>
                           <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td>{{ $product->company->name ?? 'N/A' }}</td>
                                        <td>{{ $product->store->name ?? 'N/A' }}</td>
                                        <td>{{ $product->purchase_price }}</td>
                                        <td>{{ $product->selling_price }}</td>
                                        <td>{{ $product->stock_quantity }}</td>
                                        <td>{{ $product->unitName->name ?? 'N/A' }} ({{ $product->unitName->symbol ?? '' }})</td>
                                        <td>{{ $product->expiry_date }}</td>
                                        <td>
                                            <div class="d-flex align-items-center list-action">
                                                <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit" href="{{ route('product.edit', $product->id) }}">
                                                    <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                </a>
                                                <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" href="{{ route('product.delete', $product->id) }}">
                                                    <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No records found.</td>
                                    </tr>
                                @endforelse

                           </tbody>

                       </table>
                    </div>

                    {{-- Pagination Links (outside table) --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
    </div>

@endsection
