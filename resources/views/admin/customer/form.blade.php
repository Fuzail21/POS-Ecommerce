@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            @php
                use App\Models\Setting;
                $setting = Setting::first();
                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
            @endphp
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>{{ isset($customer) ? 'Edit' : 'Add' }} Customer</h4>
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
                        <form action="{{ isset($customer) ? route('customers.update', $customer->id) : route('customers.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name ?? '') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone ?? '') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control">{{ old('address', $customer->address ?? '') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Balance</label>
                                <input type="number" name="balance" class="form-control" step="0.01" value="{{ old('balance', $customer->balance ?? '0') }}">
                            </div>

                            <button type="submit" class="btn text-white" style="background-color: {{ $primaryColor }};">{{ isset($customer) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('customers.list') }}" class="btn text-white" style="background-color: {{ $secondaryColor }};">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
