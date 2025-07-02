<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
          <title>{{ $title ?? 'Default Site Title' }}</title>

          <!-- Favicon -->
            <link rel="stylesheet" href="{{ asset('build/assets/css/backend-plugin.min.css') }}">
            <link rel="stylesheet" href="{{ asset('build/assets/css/backend.css?v=1.0.0') }}">
            <link rel="shortcut icon" href="{{ asset('build/assets/images/favicon.ico') }}">
            <link rel="stylesheet" href="{{ asset('build/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
            <link rel="stylesheet" href="{{ asset('build/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
            <link rel="stylesheet" href="{{ asset('build/assets/vendor/remixicon/fonts/remixicon.css') }}">
            {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
            @yield('css')
    </head>

  <body class="">

        @php
            use App\Models\Setting;
            $setting = Setting::first();
            $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
            $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
        @endphp

    <div id="loading">
          <div id="loading-center">
          </div>
    </div>


    <div class="wrapper">

        @yield('content')


        <footer class="iq-footer">
            <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                                {{-- <li class="list-inline-item">{{ $setting->footer }}</li> --}}
                                <li class="list-inline-item">Developed by <a href="https://www.linkedin.com/in/fuzail-siddiqui-294238267/" target="_blank">{{ $setting->developed_by }}</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span class="mr-1"><script>document.write(new Date().getFullYear())</script>©</span> <a href="{{ route('dashboard') }}" class="">{{ $setting->business_name }}</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </footer>


    </div>

<script src="{{ asset('build/assets/js/backend-bundle.min.js') }}"></script>
    <script src="{{ asset('build/assets/js/table-treeview.js') }}"></script>
    <script src="{{ asset('build/assets/js/customizer.js') }}"></script>
    <script async src="{{ asset('build/assets/js/chart-custom.js') }}"></script>
    <script src="{{ asset('build/assets/js/app.js') }}"></script>        

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Ensure jQuery is loaded -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    @yield('js')

  </body>
</html>