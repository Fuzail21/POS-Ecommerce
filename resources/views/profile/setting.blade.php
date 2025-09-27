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
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
          <div>
            <h4 class="mb-3">POS Settings</h4>
          </div>
          <a href="{{ route('dashboard') }}" class="btn text-white" style="background-color: {{ $secondaryColor }};">Back</a>
        </div>
      </div>

      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <form action="{{ route('settings.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
            
                <div class="row">
                    <!-- Business Name -->
                    <div class="form-group col-md-6">
                        <label>Business Name <span class="text-danger">*</span></label>
                        <input type="text" name="business_name" class="form-control" required
                               value="{{ old('business_name', posSetting('business_name')) }}">
                    </div>
            
                    <!-- Default Email -->
                    <div class="form-group col-md-6">
                        <label>Default Email</label>
                        <input type="email" name="default_email" class="form-control"
                               value="{{ old('default_email', posSetting('default_email')) }}">
                    </div>
            
                    <!-- Currency Symbol -->
                    <div class="form-group col-md-6">
                        <label>Currency Symbol <span class="text-danger">*</span></label>
                        <input type="text" name="currency_symbol" class="form-control" required
                               value="{{ old('currency_symbol', posSetting('currency_symbol', 'Rs')) }}">
                    </div>
            
                    <!-- Currency Code -->
                    <div class="form-group col-md-6">
                        <label>Currency Code <span class="text-danger">*</span></label>
                        <input type="text" name="currency_code" class="form-control" required
                               value="{{ old('currency_code', posSetting('currency_code', 'PKR')) }}">
                    </div>
            
                    <!-- Company Phone -->
                    <div class="form-group col-md-6">
                        <label>Company Phone</label>
                        <input type="text" name="company_phone" class="form-control"
                               value="{{ old('company_phone', posSetting('company_phone')) }}">
                    </div>
            
                    <!-- Footer -->
                    <div class="form-group col-md-6">
                        <label>Footer</label>
                        <input type="text" name="footer" class="form-control"
                               value="{{ old('footer', posSetting('footer')) }}">
                    </div>
            
                    <!-- Country -->
                    <div class="form-group col-md-6">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control"
                               value="{{ old('country', posSetting('country')) }}">
                    </div>
            
                    <!-- State -->
                    <div class="form-group col-md-6">
                        <label>State</label>
                        <input type="text" name="state" class="form-control"
                               value="{{ old('state', posSetting('state')) }}">
                    </div>
            
                    <!-- City -->
                    <div class="form-group col-md-6">
                        <label>City</label>
                        <input type="text" name="city" class="form-control"
                               value="{{ old('city', posSetting('city')) }}">
                    </div>
            
                    <!-- Postal Code -->
                    <div class="form-group col-md-6">
                        <label>Postal Code</label>
                        <input type="text" name="postal_code" class="form-control"
                               value="{{ old('postal_code', posSetting('postal_code')) }}">
                    </div>
            
                    <!-- Address -->
                    <div class="form-group col-md-12">
                        <label>Address</label>
                        <textarea name="address" rows="2" class="form-control">{{ old('address', posSetting('address')) }}</textarea>
                    </div>
            
                    <!-- Developed By -->
                    <div class="form-group col-md-6">
                        <label>Developed By</label>
                        <input type="text" name="developed_by" class="form-control"
                               value="{{ old('developed_by', posSetting('developed_by')) }}">
                    </div>
            
                    <!-- Logo -->
                    <div class="form-group col-md-6">
                        <label>Upload Logo <span class="text-muted" style="font-size: 12px;">(Only PNG, JPG, JPEG, WEBP – 50x50 px)</span></label>
                        <input type="file" name="logo" class="form-control" accept=".png,.jpg,.jpeg,.webp">
                        
                        @error('logo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    
                        @if(optional($setting)->logo_path)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo"
                                     style="width: 50px; height: 50px; border-radius: 50%; border: 1px solid black;">
                                <p class="text-muted mb-0" style="font-size: 12px;">Current Logo (50x50)</p>
                            </div>
                          @else
                            <p class="text-muted mb-0" style="font-size: 12px;">No logo uploaded.</p>
                          
                        @endif
                    </div>

            
                    <!-- Primary Color -->
                    <div class="form-group col-md-6">
                        <label for="primary_color">Primary Color</label>
                        <input type="color" name="primary_color" class="form-control"
                               value="{{ old('primary_color', $setting->primary_color ?? '#0d6efd') }}">
                    </div>
            
                    <!-- Secondary Color -->
                    <div class="form-group col-md-6">
                        <label for="secondary_color">Secondary Color</label>
                        <input type="color" name="secondary_color" class="form-control"
                               value="{{ old('secondary_color', $setting->secondary_color ?? '#6c757d') }}">
                    </div>
                </div>
            
                <button type="submit" class="btn text-white" style="background-color: {{ $primaryColor }};">Save Settings</button>
            </form>

          </div>
        </div>
      </div>

    {{-- Mail Setting --}}
      <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
          <div>
            <h4 class="mb-3">Mail Settings</h4>
          </div>
          {{-- <a href="{{ route('dashboard') }}" class="btn text-white" style="background-color: {{ $secondaryColor }};">Back</a> --}}
        </div>
      </div>

      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <form action="{{ route('mail-settings.save') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label>MAIL_MAILER:<span class="text-danger">*</span></label>
                        <input type="text" name="mail_mailer" class="form-control"
                               value="{{ old('mail_mailer', $mailSetting->mail_mailer ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>MAIL_HOST:<span class="text-danger">*</span></label>
                        <input type="text" name="mail_host" class="form-control"
                               value="{{ old('mail_host', $mailSetting->mail_host ?? '') }}" required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>MAIL_PORT:<span class="text-danger">*</span></label>
                        <input type="number" name="mail_port" class="form-control"
                               value="{{ old('mail_port', $mailSetting->mail_port ?? '') }}" required>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label>Sender Name:<span class="text-danger">*</span></label>
                        <input type="text" name="sender_name" class="form-control"
                               value="{{ old('sender_name', $mailSetting->sender_name ?? '') }}" required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>MAIL_USERNAME:<span class="text-danger">*</span></label>
                        <input type="text" name="mail_username" class="form-control"
                               value="{{ old('mail_username', $mailSetting->mail_username ?? '') }}" required>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label>MAIL_PASSWORD:<span class="text-danger">*</span></label>
                        <input type="text" name="mail_password" class="form-control"
                               value="{{ old('mail_password', $mailSetting->mail_password ?? '') }}" required>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>MAIL_ENCRYPTION:<span class="text-danger">*</span></label>
                        <input type="text" name="mail_encryption" class="form-control"
                               value="{{ old('mail_encryption', $mailSetting->mail_encryption ?? '') }}" required>
                    </div>

                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn text-white" style="background-color: {{ $primaryColor }};">Save</button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
      

    </div>
  </div>
</div>
@endsection
