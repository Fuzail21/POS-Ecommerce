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
                                    <h4 class="card-title">Add Product</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('product.store') }}" method="post" data-toggle="validator" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">  
                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                                <label>Name *</label>
                                                <input type="text" name="name" class="form-control" placeholder="Enter Name" data-errors="Please Enter Name." required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                               <label>Category *</label>
                                               <select class="form-control mb-3" name="category_id" required>
                                                  <option selected="">Select Category...</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                               </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                               <label>Company *</label>
                                               <select class="form-control mb-3" name="company_id" required>
                                                  <option selected="">Select Company...</option>
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                    @endforeach
                                               </select>
                                            </div>
                                        </div>


                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                               <label>Store *</label>
                                               <select class="form-control mb-3" name="store_id" required>
                                                  <option selected="">Select Store...</option>
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                                    @endforeach
                                               </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                                <label>Product Image</label>
                                                <input type="file" name="product_img" class="form-control" accept="image/*">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>


                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Purchase Price *</label>
                                                <input type="number" name="purchase_price" class="form-control" placeholder="Enter Purchase Price" data-errors="Please Enter Purchase Price" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>


                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Selling Price *</label>
                                                <input type="number" name="selling_price" class="form-control" placeholder="Enter Selling Price" data-errors="Please Enter Selling Price" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>


                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Stock Qty *</label>
                                                <input type="number" name="stock_quantity" class="form-control" placeholder="Enter Stock Qty" data-errors="Please Enter Stock Qty" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">           
                                            <div class="form-group">
                                                <label for="unit">Select Unit</label>
                                                <select name="unit" id="unit" class="form-control">
                                                    <option value="">Select a unit</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> 

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Expiry Date *</label>
                                                <input type="date" name="expiry_date" class="form-control" placeholder="Enter Expiry Date" data-errors="Please Enter Expiry Date" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div> 
                                    </div>                            
                                    <button type="submit" class="btn btn-primary mr-2">Add Product</button>
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


    