<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>403 – Access Denied</title>
    <link rel="stylesheet" href="{{ asset('build/assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/css/backend.css?v=1.0.0') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/vendor/remixicon/fonts/remixicon.css') }}">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f4f6f9; }
        .error-card { text-align: center; padding: 3rem; max-width: 480px; }
        .error-icon { font-size: 5rem; color: #e74c3c; margin-bottom: 1rem; }
        .error-code { font-size: 6rem; font-weight: 700; color: #2c3e50; line-height: 1; }
        .error-message { font-size: 1.25rem; color: #555; margin: 1rem 0 2rem; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon"><i class="ri-shield-keyhole-line"></i></div>
        <div class="error-code">403</div>
        <h2 class="mt-2">Access Denied</h2>
        <p class="error-message">
            {{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}
        </p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
           class="btn btn-primary mr-2">Go Back</a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
    </div>
</body>
</html>
