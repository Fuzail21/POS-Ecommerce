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

              <div class="form-group">
                <label>Business Name <span class="text-danger">*</span></label>
                <input type="text" name="business_name" class="form-control" required
                  value="{{ old('business_name', posSetting('business_name')) }}">
              </div>

              <div class="form-group">
                <label>Currency Symbol <span class="text-danger">*</span></label>
                <input type="text" name="currency_symbol" class="form-control" required
                  value="{{ old('currency_symbol', posSetting('currency_symbol', 'Rs')) }}">
              </div>

              <div class="form-group">
                <label>Currency Code <span class="text-danger">*</span></label>
                <input type="text" name="currency_code" class="form-control" required
                  value="{{ old('currency_code', posSetting('currency_code', 'PKR')) }}">
              </div>

              <div class="form-group">
                <label>Upload Logo</label>
                <input type="file" name="logo" class="form-control">
                @if(posSetting('logo'))
                  <img src="{{ asset(posSetting('logo')) }}" alt="Logo"
                       class="mt-2" style="max-height: 80px;">
                @endif
              </div>

              <div class="form-group">
                  <label for="primary_color">Primary Color</label>
                  <input type="color"
                         name="primary_color"
                         id="primary_color"
                         class="form-control"
                         value="{{ old('primary_color', $setting->primary_color ?? '#0d6efd') }}">
              </div>

              <div class="form-group">
                  <label for="secondary_color">Secondary Color</label>
                  <input type="color"
                         name="secondary_color"
                         id="secondary_color"
                         class="form-control"
                         value="{{ old('secondary_color', $setting->secondary_color ?? '#6c757d') }}">
              </div>



              <button type="submit" class="btn btn-success">Save Settings</button>
              {{-- <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a> --}}
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
