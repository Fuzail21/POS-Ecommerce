<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
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
            <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white leading-tight">Reset Password</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Enter your new password below.</p>
        </div>

        <form method="POST" action="{{ route('customer.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                    class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-gray-900 dark:text-white transition duration-200 input-focus-animation">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-gray-900 dark:text-white transition duration-200 input-focus-animation">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-gray-900 dark:text-white transition duration-200 input-focus-animation">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-md transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Reset Password
            </button>

        </form>
    </div>

</body>
</html>
