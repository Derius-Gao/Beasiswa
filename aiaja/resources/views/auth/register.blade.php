@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<form method="POST" action="{{ route('register.post') }}" class="space-y-6">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Full Name
        </label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email Address
        </label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
            Phone Number
        </label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
        @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="gpa" class="block text-sm font-medium text-gray-700 mb-2">
                GPA
            </label>
            <input id="gpa" type="number" step="0.01" min="0" max="4" name="gpa" value="{{ old('gpa') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
            @error('gpa')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="major" class="block text-sm font-medium text-gray-700 mb-2">
                Major
            </label>
            <input id="major" type="text" name="major" value="{{ old('major') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
            @error('major')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="economic_status" class="block text-sm font-medium text-gray-700 mb-2">
            Economic Status
        </label>
        <select id="economic_status" name="economic_status"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
            <option value="">Select Economic Status</option>
            <option value="low_income" {{ old('economic_status') == 'low_income' ? 'selected' : '' }}>Low Income</option>
            <option value="middle_income" {{ old('economic_status') == 'middle_income' ? 'selected' : '' }}>Middle Income</option>
            <option value="high_income" {{ old('economic_status') == 'high_income' ? 'selected' : '' }}>High Income</option>
        </select>
        @error('economic_status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
            User Level
        </label>
        <select id="level" name="level" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
            <option value="">Select Level</option>
            <option value="superadmin" {{ old('level') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
            <option value="admin" {{ old('level') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="mahasiswa" {{ old('level') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
        </select>
        @error('level')
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

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
            Confirm Password
        </label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
        @error('password_confirmation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
        Create Account
    </button>

    <div class="text-center">
        <p class="text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                Sign in
            </a>
        </p>
    </div>
</form>
@endsection
