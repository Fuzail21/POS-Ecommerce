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
                            <h4 class="card-title">
                                {{ isset($user) ? 'Edit User' : 'Add User' }}
                            </h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form 
                            action="{{ isset($user) ? route('user.update', $user->id) : route('user.store') }}" 
                            method="POST" 
                            data-toggle="validator"
                        >
                            @csrf
                            <div class="row">  
                                <div class="col-md-12">                      
                                    <div class="form-group">
                                        <label>Name *</label>
                                        <input type="text" name="name" class="form-control" 
                                            placeholder="Enter Name" 
                                            value="{{ old('name', $user->name ?? '') }}" 
                                            data-errors="Please Enter Name." required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>    

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Email *</label>
                                        <input type="email" name="email" class="form-control" 
                                            placeholder="Enter Email" 
                                            value="{{ old('email', $user->email ?? '') }}" 
                                            data-errors="Please Enter Email." required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                                {{-- Password field --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>
                                            {{ isset($user) ? 'New Password (optional)' : 'Password *' }}
                                        </label>
                                        <input type="password" name="password" class="form-control" 
                                            placeholder="{{ isset($user) ? 'Enter New Password' : 'Enter Password' }}"
                                            {{ isset($user) ? '' : 'required' }}>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Role *</label>
                                        <select name="role_id" class="form-control" required>
                                            <option value="">Select Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>



                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Status *</label>
                                        <select name="status" class="form-control" required>
                                            <option value="">Select Status</option>
                                            <option value="Active" {{ old('status', $user->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ old('status', $user->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div> 
                            </div>                            

                            <button type="submit" class="btn btn-primary mr-2">
                                {{ isset($user) ? 'Update User' : 'Add User' }}
                            </button>
                            <a href="{{ route('user.list') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
