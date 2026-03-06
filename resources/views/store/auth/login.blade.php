<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom styles for focus animation */
        .input-focus-animation:focus {
            transform: scale(1.01);
            border-color: #6366f1; /* Tailwind indigo-500 */
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl transform transition duration-500 hover:scale-105">

        <div class="mb-6 text-center">
            <h3 class="text-4xl font-extrabold text-gray-900 dark:text-white leading-tight">Welcome Back!</h3>
            <p class="mt-2 text-md text-gray-600 dark:text-gray-400">Log in to your account</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 rounded-md bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('customer.login') }}">
            @csrf
            
            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:outline-none text-gray-900 dark:text-white transition duration-200 input-focus-animation">
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:outline-none text-gray-900 dark:text-white transition duration-200 input-focus-animation">
                @error('password')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center mb-6">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
                <label for="remember_me" class="ml-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer"> Remember me </label>
            </div>

            <div class="flex items-center justify-end"> {{-- Changed to justify-end for button alignment --}}
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-md transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Login
                </button>
            </div>
            
            <div class="mt-6 text-center space-y-3">
                @if (Route::has('customer.password.request'))
                    <div>
                        <a class="text-sm text-indigo-600 hover:underline dark:text-indigo-400" href="{{ route('customer.password.request') }}">
                            Forgot your password?
                        </a>
                    </div>
                @endif
                <div>
                    <a href="{{ route('customer.register') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 hover:underline">
                        Don't have an account? Create one
                    </a>
                </div>
            </div>

        </form>
    </div>

</body>
</html>