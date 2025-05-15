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
                                {{ isset($role) ? 'Edit Role' : 'Add Role' }}
                            </h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form 
                            action="{{ isset($role) ? route('role.update', $role->id) : route('role.store') }}" 
                            method="POST" 
                            data-toggle="validator"
                        >
                            @csrf
                            <div class="form-group">
                                <label>Role Name *</label>
                                <input type="text" name="name" class="form-control" 
                                    placeholder="Enter Role Name" 
                                    value="{{ old('name', $role->name ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" 
                                          placeholder="Enter Description">{{ old('description', $role->description ?? '') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ isset($role) ? 'Update Role' : 'Add Role' }}
                            </button>
                            <a href="{{ route('role.list') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
