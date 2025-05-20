@extends('layouts.app')

@section('css')

<style>
    #datatable_length,
    #datatable_info,
    #datatable_paginate {
        display: none !important;
    }
</style>

@endsection

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
                    <a href="{{ route('products.create') }}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Product</a>
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
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Actions</th>
                             </tr>
                          </thead>
                            <tbody>
                                @forelse($products as $index => $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if (!empty($product->product_img))
                                                <img src="{{ asset('storage/' . $product->product_img) }}" alt="Product Image" width="50" style="object-fit: cover;">
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td>{{ $product->brand ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center list-action">
                                                <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit" href="{{ route('products.edit', $product->id) }}">
                                                    <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                </a>
                                                <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" href="{{ route('products.destroy', $product->id) }}">
                                                    <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                </a>

                                                @if($product->has_variants == 1)
                                                    <a class="badge bg-info p-1" data-toggle="tooltip" data-placement="top" title="View Variants" href="{{ route('products.variants', $product->id) }}">
                                                        <i class="ri-eye-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                @else
                                                    
                                                @endif
                                                
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No products found.</td>
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
