@extends('layouts.frontend.app') {{-- Adjust this path if your main layout is different, e.g., 'layouts.app' or 'store.layouts.app' --}}

@section('frontend_content')
    @php
        use App\Models\Setting;
        $setting = Setting::first();
        $primaryColor = $setting->primary_color ?? '#0d6efd';
        $secondaryColor = $setting->secondary_color ?? '#6c757d';
    @endphp
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12" style="margin-top: 100px;">
                <div class="card shadow-sm border-0 rounded-lg p-4">
                    <div class="card-header bg-white border-bottom-0 text-center">
                        <h3 class="mb-0 text-dark">My Profile</h3>
                    </div>
                    <div class="card-body">

                        @if (session('status')) {{-- Changed from 'success' to 'status' to match controller --}}
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error')) {{-- Keep 'error' for general errors --}}
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('customer.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT') {{-- Use PUT method for updates --}}

                            {{-- Profile Image Section (Optional, if you have one) --}}
                            <div class="d-flex justify-content-center mb-4">
                                <img src="{{ asset('storage/user/user.webp') }}"
                                 class="img-fluid rounded-circle"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #ddd;"
                                 alt="User Profile">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name) }}">
                                    @error('last_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                                @error('email')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
                                @error('phone')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $customer->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $customer->country) }}">
                                    @error('country')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $customer->city) }}">
                                    @error('city')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="postcode" class="form-label">Postcode</label>
                                    <input type="text" class="form-control" id="postcode" name="postcode" value="{{ old('postcode', $customer->postcode) }}">
                                    @error('postcode')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Balance is display-only --}}
                            {{-- <div class="mb-4">
                                <label for="balance" class="form-label">Account Balance</label>
                                <input type="text" class="form-control" id="balance" value="{{ number_format($customer->balance ?? 0, 2) }} {{ $setting->currency_symbol ?? '$' }}" readonly disabled>
                            </div> --}}

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Update Profile</button>
                            </div>
                        </form>

                        <hr class="my-5">

                        {{-- Account Deletion Section --}}
                        {{-- <h4 class="mb-3 text-danger text-center">Delete Account</h4>
                        <p class="text-center text-muted">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>
                        <form action="{{ route('customer.profile.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label for="password_delete" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password_delete" name="password" required>
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger btn-lg">Delete Account</button>
                        </div>
                        </form> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection