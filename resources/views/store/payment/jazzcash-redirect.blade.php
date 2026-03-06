<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to JazzCash...</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; align-items: center;
               justify-content: center; height: 100vh; background: #f0f4f8; margin: 0; }
        .card { background: #fff; padding: 40px; border-radius: 12px; text-align: center;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 400px; }
        .logo { color: #c8102e; font-size: 28px; font-weight: bold; margin-bottom: 16px; }
        p { color: #555; margin-bottom: 20px; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #c8102e;
                   border-radius: 50%; width: 40px; height: 40px;
                   animation: spin 0.8s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">JazzCash</div>
        <div class="spinner"></div>
        <p>Please wait while we redirect you to JazzCash payment page...</p>
    </div>

    <form id="paymentForm" action="{{ $endpoint }}" method="POST" style="display:none;">
        @foreach($payload as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('paymentForm').submit();
            }, 1500);
        });
    </script>
</body>
</html>
