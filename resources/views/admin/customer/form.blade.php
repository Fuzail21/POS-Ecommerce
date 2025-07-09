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
                                <label>First Name</label> {{-- Changed to First Name for clarity --}}
                                <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name ?? '') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label> {{-- New Field --}}
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $customer->last_name ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone ?? '') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email ?? '') }}">
                            </div>

                            {{-- Password fields as previously discussed --}}
                            <div class="form-group">
                                <label>Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" {{ isset($customer) ? '' : 'required' }}>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                @if(isset($customer))
                                    <small class="form-text text-muted">Leave blank to keep current password.</small>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" {{ isset($customer) ? '' : 'required' }}>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control">{{ old('address', $customer->address ?? '') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Country</label> {{-- New Field --}}
                                <input type="text" name="country" class="form-control" value="{{ old('country', $customer->country ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label>City</label> {{-- New Field --}}
                                <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label>Postcode</label> {{-- New Field --}}
                                <input type="text" name="postcode" class="form-control" value="{{ old('postcode', $customer->postcode ?? '') }}">
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

@section('js')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function (e) {
                // toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                // toggle the eye slash icon
                this.querySelector('i').classList.toggle('fa-eye-slash');
                this.querySelector('i').classList.toggle('fa-eye');
            });
        }

        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#password_confirmation');

        if (toggleConfirmPassword && confirmPassword) {
            toggleConfirmPassword.addEventListener('click', function (e) {
                // toggle the type attribute
                const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPassword.setAttribute('type', type);
                // toggle the eye slash icon
                this.querySelector('i').classList.toggle('fa-eye-slash');
                this.querySelector('i').classList.toggle('fa-eye');
            });
        }
    });
</script>
@endsection