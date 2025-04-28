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
                                    <h4 class="card-title">Update Customer</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('customer.update', $customer->id) }}" method="post" data-toggle="validator">
                                    @csrf
                                    <div class="row">  
                                        <div class="col-md-12">                      
                                            <div class="form-group">
                                                <label>Name *</label>
                                                <input type="text" name="name" class="form-control" placeholder="Enter Name" data-errors="Please Enter Name." required value="{{ $customer->name }}">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>    
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Contact Number *</label>
                                                <input type="number" name="contact" class="form-control" value="{{ $customer->contact }}" placeholder="Enter Contact No." data-errors="Please Enter Contact Number" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                               <label for="exampleFormControlTextarea1">Address *</label>
                                               <textarea class="form-control" name="address" id="exampleFormControlTextarea1" rows="3" required>{{ $customer->address }}</textarea>
                                            </div>
                                        </div> 
                                    </div>                            
                                    <button type="submit" class="btn btn-primary mr-2">Update Customer</button>
                                    <a href="{{ route('customer.list') }}" class="btn btn-secondary">Cancel</a>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page end  -->
            </div>
      </div>
        



    @endsection


    