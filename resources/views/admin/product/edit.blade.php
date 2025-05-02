@extends('layouts.app')


    @section('content')


        @include('layouts.sidebar')



        <div class="content-page">
            <div class="container-fluid add-form-list">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Update Product</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('product.update', $product->id) }}" method="post" data-toggle="validator">
                                    @csrf
                                    <div class="row">  
                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                                <label>Name *</label>
                                                <input type="text" name="name" class="form-control" placeholder="Enter Name" value="{{ old('name', $product->name) }}" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                                <label>Category *</label>
                                                <select class="form-control mb-3" name="category_id" required>
                                                    <option value="">Select Category...</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                                <label>Company *</label>
                                                <select class="form-control mb-3" name="company_id" required>
                                                    <option value="">Select Company...</option>
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" {{ $product->company_id == $company->id ? 'selected' : '' }}>
                                                            {{ $company->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                                <label>Store *</label>
                                                <select class="form-control mb-3" name="store_id" required>
                                                    <option value="">Select Store...</option>
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}" {{ $product->store_id == $store->id ? 'selected' : '' }}>
                                                            {{ $store->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Purchase Price *</label>
                                                <input type="number" name="purchase_price" class="form-control" placeholder="Enter Purchase Price" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Selling Price *</label>
                                                <input type="number" name="selling_price" class="form-control" placeholder="Enter Selling Price" value="{{ old('selling_price', $product->selling_price) }}" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Stock Qty *</label>
                                                <input type="number" name="stock_quantity" class="form-control" placeholder="Enter Stock Qty" data-errors="Please Enter Stock Qty" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">           
                                            <div class="form-group">
                                                <label for="unit">Select Unit</label>
                                                <select name="unit" id="unit" class="form-control">
                                                    <option value="">Select a unit</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}" 
                                                            {{ (isset($category) && $category->unit == $unit->id) ? 'selected' : '' }}>
                                                            {{ $unit->name }} ({{ $unit->symbol }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Expiry Date *</label>
                                                <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $product->expiry_date) }}" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div> 
                                    </div>                            

                                    <button type="submit" class="btn btn-primary mr-2">Update Product</button>
                                    <a href="{{ route('product.list') }}" class="btn btn-secondary">Cancel</a>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page end  -->
            </div>
      </div>
        



    @endsection


    