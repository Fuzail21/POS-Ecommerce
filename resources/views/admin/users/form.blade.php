@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">  
        @php
            use App\Models\Setting;
            $setting = Setting::first();
            $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
            $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
        @endphp
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

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

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
                                        <label for="branch_id">Branch</label>
                                        <select name="branch_id" id="branch_id" class="form-control">
                                            <option value="">-- Select Branch --</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
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

                            <button type="submit" class="btn text-white mr-2" style="background-color: {{ $primaryColor }};"> 
                                {{ isset($user) ? 'Update User' : 'Add User' }}
                            </button>
                            <a href="{{ route('user.list') }}" class="btn text-white" style="background-color: {{ $secondaryColor }};">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
