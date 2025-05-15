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
                            <h4 class="card-title">Edit Store</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('store.update', $store->id) }}" method="post" data-toggle="validator">
                            @csrf
                            <div class="row">  
                                <div class="col-md-12">                      
                                    <div class="form-group">
                                        <label>Name *</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter Name" data-errors="Please Enter Name." value="{{ $store->name }}" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>    

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Location *</label>
                                        <input type="text" name="location" class="form-control" placeholder="Enter Location" data-errors="Please Enter Location." value="{{ $store->location }}" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div> 

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Contact Number *</label>
                                        <input type="text" name="contact" class="form-control" placeholder="Enter Contact Number" data-errors="Please Enter Contact Number." value="{{ $store->contact }}" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Manager *</label>
                                        <select name="manager_id" class="form-control" required>
                                            <option value="">Select Manager</option>
                                            @foreach($managers as $manager)
                                                <option value="{{ $manager->id }}" {{ $store->manager_id == $manager->id ? 'selected' : '' }}>
                                                    {{ $manager->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>                            

                            <button type="submit" class="btn btn-primary mr-2">Update Store</button>
                            <a href="{{ route('store.list') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
