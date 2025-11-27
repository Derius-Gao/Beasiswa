@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<form method="POST" action="{{ route('login.post') }}" class="space-y-6">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email Address
        </label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            Password
        </label>
        <input id="password" type="password" name="password" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
        @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @php
        $showCaptchaOnline = app()->environment('production');
    @endphp
    @if($showCaptchaOnline)
    <div id="captcha-online" class="mb-4">
        <div class="g-recaptcha" data-sitekey="6Le6KAssAAAAAMTnxios7DiaGGh89u6uD1U-WLxG"></div>
        @error('g-recaptcha-response')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    @else
    <div id="captcha-offline" class="mb-4">
        @php
            $a = rand(1, 10);
            $b = rand(1, 10);
            $op = rand(0, 1) ? '+' : '-';
            $question = $op == '+' ? "$a + $b" : "$a - $b";
            session(['captcha_answer' => $op == '+' ? $a + $b : $a - $b]);
        @endphp
        <label for="captcha_answer" class="block text-sm font-medium text-gray-700 mb-2">
            Captcha Offline: Hitung <b>{{ $question }}</b> = ?
        </label>
        <input id="captcha_answer" type="text" name="captcha_answer" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        @error('captcha_answer')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    @endif

    <div class="flex items-center justify-between">
        <label class="flex items-center">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <span class="ml-2 text-sm text-gray-600">Remember me</span>
        </label>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                Forgot password?
            </a>
        @endif
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
        Sign In
    </button>

    <div class="text-center">
        <p class="text-sm text-gray-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                Sign up
            </a>
        </p>
    </div>
</form>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection
