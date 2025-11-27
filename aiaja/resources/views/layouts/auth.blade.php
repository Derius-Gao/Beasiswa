<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AIJA') }} - @yield('title', 'Login')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .auth-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="antialiased auth-bg min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-6">
        <div class="auth-card p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">AIJA</h1>
                <p class="text-gray-600">Sistem Pembayaran Online dengan AI</p>
            </div>

            @yield('content')

            <div class="mt-8 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} AIJA. All rights reserved.</p>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
