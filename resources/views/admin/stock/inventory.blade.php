@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
  <div class="container-fluid">
     <div class="row">

        <div class="col-lg-12 mb-4">
          <div class="card p-3 shadow-sm">
            <form method="GET" action="{{ route('stock.list') }}" class="row g-3 align-items-center">
        
              <div class="col-md-4">
                <label for="search_name" class="form-label">Product Name</label>
                <input 
                  type="text" 
                  name="search_name" 
                  id="search_name"
                  class="form-control" 
                  placeholder="Enter product name" 
                  value="{{ request('search_name') }}"
                >
              </div>
        
              {{-- <div class="col-md-3">
                <label for="search_sku" class="form-label">SKU</label>
                <input 
                  type="text" 
                  name="search_sku" 
                  id="search_sku"
                  class="form-control" 
                  placeholder="Enter SKU" 
                  value="{{ request('search_sku') }}"
                >
              </div> --}}
        
              <div class="col-md-4">
                <label for="search_category" class="form-label">Category</label>
                <select name="search_category" id="search_category" class="form-control">
                  <option value="">-- All Categories --</option>
                  @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('search_category') == $category->id ? 'selected' : '' }}>
                      {{ $category->name }}
                    </option>
                  @endforeach
                </select>
              </div>
        
              <div class="col-md-3">
                <label for="search_status" class="form-label">Status</label>
                <select name="search_status" id="search_status" class="form-control">
                  <option value="">-- All Status --</option>
                  <option value="low_stock" {{ request('search_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                  <option value="ok" {{ request('search_status') == 'ok' ? 'selected' : '' }}>OK</option>
                </select>
              </div>
        
              <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-primary mt-4">Filter</button>
              </div>
        
            </form>
          </div>
        </div>


        <div class="col-sm-12">
           <div class="card">

              @if(session('success'))
                  <div class="alert alert-success">{{ session('success') }}</div>
              @endif
              @if(session('error'))
                  <div class="alert alert-danger">{{ session('error') }}</div>
              @endif

              <div class="card-header">
                 <h4 class="card-title">Stock Summary</h4>
              </div>

              <div class="card-body">
                <div class="table-responsive">
                   <table class="table table-bordered table-hover">
                        <thead style="background-color: #F4F5FA;">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                {{-- <th>SKU</th> --}}
                                <th>Category</th>
                                {{-- <th>Branch</th> --}}
                                <th>Current Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $index => $product)
                                @php
                                    $conversion = $product->baseUnit->conversion_factor ?? 1;
                                @endphp

                                @if($product->has_variants && $product->variants->count())
                                    @foreach($product->variants as $variant)
                                        @php
                                            $baseQty = $variant->inventoryStock->quantity_in_base_unit ?? 0;
                                            $actualQty = $conversion > 0 ? ($baseQty / $conversion) : 0;
                                            $isLow = $actualQty <= 5;
                                        @endphp
                                        <tr class="{{ $isLow ? 'table-warning' : '' }}">
                                            <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                                            <td>{{ $product->name }} ({{ $variant->variant_name }})</td>
                                            {{-- <td>{{ $variant->sku }}</td> --}}
                                            <td>{{ $product->category->name ?? '-' }}</td>
                                            {{-- <td>{{ $product->branch->name ?? '-' }}</td> --}}
                                            <td>{{ number_format($actualQty, 0) }} {{ $product->baseUnit->name ?? '' }}</td>
                                            <td>
                                                @if($isLow)
                                                    <span class="badge badge-danger">Low Stock</span>
                                                @else
                                                    <span class="badge bg-success">OK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @php
                                        $baseQty = $product->inventoryStock ? $product->inventoryStock->sum('quantity_in_base_unit') : 0;
                                        $actualQty = $conversion > 0 ? ($baseQty / $conversion) : 0;
                                        $isLow = $actualQty <= 5;
                                    @endphp

                                    <tr class="{{ $isLow ? 'table-warning' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->name }}</td>
                                        {{-- <td>{{ $product->sku }}</td> --}}
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        {{-- <td>{{ $product->branch->name ?? '-' }}</td> --}}
                                        <td>{{ number_format($actualQty, 2) }} {{ $product->baseUnit->name ?? '' }}</td>
                                        <td>
                                            @if($isLow)
                                                <span class="badge badge-danger">Low Stock</span>
                                            @else
                                                <span class="badge bg-success">OK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                        </tbody>

                    </table>
                </div>

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
