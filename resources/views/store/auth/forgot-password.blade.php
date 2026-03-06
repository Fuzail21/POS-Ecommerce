<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .input-focus-animation:focus {
            transform: scale(1.01);
            border-color: #6366f1;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl">

        <div class="mb-6 text-center">
            <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white leading-tight">Forgot Password</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Enter your email and we'll send you a password reset link.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 rounded-md bg-green-100 text-green-700 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('customer.password.email') }}">
            @csrf

            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-gray-900 dark:text-white transition duration-200 input-focus-animation">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-md transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Send Reset Link
            </button>

            <div class="mt-5 text-center">
                <a href="{{ route('customer.login') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                    Back to Login
                </a>
            </div>

        </form>
    </div>

</body>
</html>
