@extends('layouts.frontend.app')

@section('frontend_content')

@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp

<div class="page-header-band">
    <div class="container text-center">
        <h1>My Profile</h1>
        <nav>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('store.landing') }}">Home</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">

        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">

            {{-- Left Sidebar --}}
            <div class="col-lg-3">
                {{-- Avatar card --}}
                <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); padding:1.75rem; text-align:center; margin-bottom:1rem;">
                    <div style="width:80px; height:80px; border-radius:50%; background:var(--clr-primary); color:#fff; font-size:1.75rem; font-weight:800; display:flex; align-items:center; justify-content:center; margin:0 auto .75rem; box-shadow:0 4px 16px rgba(37,99,235,.25);">
                        {{ strtoupper(substr($customer->name ?? 'U', 0, 1)) }}
                    </div>
                    <div style="font-weight:700; font-size:.95rem; color:var(--clr-dark);">{{ $customer->name }} {{ $customer->last_name }}</div>
                    <div style="font-size:.78rem; color:var(--clr-muted); margin-top:.2rem;">{{ $customer->email }}</div>
                    @if($customer->phone)
                    <div style="font-size:.78rem; color:var(--clr-muted); margin-top:.15rem;">
                        <i class="fas fa-phone me-1"></i>{{ $customer->phone }}
                    </div>
                    @endif
                </div>

                {{-- Quick links --}}
                <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden;">
                    <div style="padding:.75rem 1rem; background:#f8fafc; border-bottom:1px solid var(--clr-border);">
                        <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted);">Account</span>
                    </div>
                    <a href="{{ route('customer.profile.edit') }}"
                       style="display:flex; align-items:center; gap:.65rem; padding:.7rem 1rem; text-decoration:none; font-size:.855rem; font-weight:600; color:var(--clr-primary); background:rgba(37,99,235,.05); border-left:3px solid var(--clr-primary);">
                        <i class="fas fa-user-circle" style="width:14px;"></i> My Profile
                    </a>
                    <a href="{{ route('store.orders') }}"
                       style="display:flex; align-items:center; gap:.65rem; padding:.7rem 1rem; text-decoration:none; font-size:.855rem; font-weight:500; color:var(--clr-muted); border-left:3px solid transparent; transition:all .2s;"
                       onmouseover="this.style.color='var(--clr-primary)';this.style.background='rgba(37,99,235,.04)'"
                       onmouseout="this.style.color='var(--clr-muted)';this.style.background=''">
                        <i class="fas fa-box" style="width:14px;"></i> My Orders
                    </a>
                    <div style="border-top:1px solid var(--clr-border);">
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button type="submit"
                                    style="width:100%; display:flex; align-items:center; gap:.65rem; padding:.7rem 1rem; background:transparent; border:none; text-align:left; font-size:.855rem; font-weight:500; color:#ef4444; cursor:pointer; border-left:3px solid transparent;"
                                    onmouseover="this.style.background='#fff1f2'" onmouseout="this.style.background=''">
                                <i class="fas fa-sign-out-alt" style="width:14px;"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right: Profile Form --}}
            <div class="col-lg-9">
                <div style="background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius-card); overflow:hidden;">
                    <div style="background:#f8fafc; border-bottom:1px solid var(--clr-border); padding:1rem 1.5rem; display:flex; align-items:center; justify-content:space-between;">
                        <h6 style="font-weight:700; font-size:.9rem; color:var(--clr-dark); margin:0;">
                            <i class="fas fa-edit me-2" style="color:var(--clr-primary);"></i> Edit Profile
                        </h6>
                    </div>
                    <div style="padding:1.75rem;">
                        <form action="{{ route('customer.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Name row --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">First Name</label>
                                    <input type="text" class="field-input" name="name"
                                           value="{{ old('name', $customer->name) }}" required>
                                    @error('name')
                                        <div style="font-size:.78rem; color:#ef4444; margin-top:.3rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">Last Name</label>
                                    <input type="text" class="field-input" name="last_name"
                                           value="{{ old('last_name', $customer->last_name) }}">
                                    @error('last_name')
                                        <div style="font-size:.78rem; color:#ef4444; margin-top:.3rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">Email Address</label>
                                <input type="email" class="field-input" name="email"
                                       value="{{ old('email', $customer->email) }}" required>
                                @error('email')
                                    <div style="font-size:.78rem; color:#ef4444; margin-top:.3rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="mb-3">
                                <label style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">Phone Number</label>
                                <input type="text" class="field-input" name="phone"
                                       value="{{ old('phone', $customer->phone) }}">
                                @error('phone')
                                    <div style="font-size:.78rem; color:#ef4444; margin-top:.3rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="mb-3">
                                <label style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">Address</label>
                                <textarea class="field-input" name="address" rows="2" style="resize:none;">{{ old('address', $customer->address) }}</textarea>
                                @error('address')
                                    <div style="font-size:.78rem; color:#ef4444; margin-top:.3rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- City / Country / Postcode --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">City</label>
                                    <input type="text" class="field-input" name="city"
                                           value="{{ old('city', $customer->city) }}">
                                    @error('city')
                                        <div style="font-size:.78rem; color:#ef4444; margin-top:.3rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">Country</label>
                                    <input type="text" class="field-input" name="country"
                                           value="{{ old('country', $customer->country) }}">
                                    @error('country')
                                        <div style="font-size:.78rem; color:#ef4444; margin-top:.3rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--clr-muted); display:block; margin-bottom:.4rem;">Postcode</label>
                                    <input type="text" class="field-input" name="postcode"
                                           value="{{ old('postcode', $customer->postcode) }}">
                                    @error('postcode')
                                        <div style="font-size:.78rem; color:#ef4444; margin-top:.3rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn-prim" style="padding:12px 32px; font-size:.9rem;">
                                <i class="fas fa-save"></i> Save Changes
                            </button>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
