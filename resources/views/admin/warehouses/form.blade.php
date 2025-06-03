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
                                {{ isset($warehouse) ? 'Edit Warehouse' : 'Add Warehouse' }}
                            </h4>
                        </div>
                    </div>

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                    <div class="card-body">
                        <form 
                            action="{{ isset($warehouse) ? route('warehouse.update', $warehouse->id) : route('warehouse.store') }}" 
                            method="POST"
                            data-toggle="validator"
                        >
                            @csrf
                            @if(isset($warehouse))
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label>Warehouse Name *</label>
                                <input type="text" name="name" class="form-control" 
                                    placeholder="Enter warehouse name" 
                                    value="{{ old('name', $warehouse->name ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Location *</label>
                                <input type="text" name="location" class="form-control" 
                                    placeholder="Enter location" 
                                    value="{{ old('location', $warehouse->location ?? '') }}" required>
                            </div>

                            {{-- <div class="form-group">
                                <label>Capacity *</label>
                                <input type="text" name="capacity" class="form-control" 
                                    placeholder="Enter capacity" 
                                    value="{{ old('capacity', $warehouse->capacity ?? '') }}" required>
                            </div> --}}

                            {{-- <div class="form-group">
                                <label>Capacity Unit*</label>
                                <input type="text" name="capacity_unit" class="form-control" 
                                    placeholder="Enter capacity unit" 
                                    value="{{ old('capacity_unit', $warehouse->capacity_unit ?? '') }}" required>
                            </div> --}}

                            <button type="submit" class="btn btn-primary">
                                {{ isset($warehouse) ? 'Update Warehouse' : 'Add Warehouse' }}
                            </button>
                            <a href="{{ route('warehouse.list') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
