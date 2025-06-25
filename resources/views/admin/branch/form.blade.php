@extends('layouts.app')
@section('content')
@include('layouts.sidebar')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            @php
                use App\Models\Setting;
                $setting = Setting::first();
                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
            @endphp
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>{{ isset($branch) ? 'Edit Branch' : 'Add Branch' }}</h4>
                    <a href="{{ route('branch.list') }}" class="btn text-white" style="background-color: {{ $secondaryColor }};">Back to List</a>
                </div>
                <div class="card">

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
                        <form action="{{ isset($branch) ? route('branch.update', $branch->id) : route('branch.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" value="{{ old('name', $branch->name ?? '') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" value="{{ old('location', $branch->location ?? '') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Contact</label>
                                <input type="text" name="contact" value="{{ old('contact', $branch->contact ?? '') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Warehouse</label>
                                <select name="warehouse_id" class="form-control" required>
                                    <option value="">-- Select Warehouse --</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ (old('warehouse_id', $branch->warehouse_id ?? '') == $warehouse->id) ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn text-white mt-3" style="background-color: {{ $primaryColor }};">{{ isset($branch) ? 'Update' : 'Create' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
