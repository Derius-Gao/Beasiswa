@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Profile</h1>
        <p class="mt-2 text-gray-600">Manage your account information</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <div class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ Auth::user()->name }}</h3>
                    <p class="text-gray-600">{{ Auth::user()->email }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ Auth::user()->role ?? 'student' }}</p>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Edit Profile</h2>
                </div>
                <form id="profile-form" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name
                            </label>
                            <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ Auth::user()->phone }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>

                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Birth Date
                            </label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ Auth::user()->birth_date }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>

                        <div>
                            <label for="gpa" class="block text-sm font-medium text-gray-700 mb-2">
                                GPA
                            </label>
                            <input type="number" id="gpa" name="gpa" value="{{ Auth::user()->gpa }}" step="0.01" min="0" max="4"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>

                        <div>
                            <label for="major" class="block text-sm font-medium text-gray-700 mb-2">
                                Major
                            </label>
                            <input type="text" id="major" name="major" value="{{ Auth::user()->major }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>

                        <div>
                            <label for="economic_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Economic Status
                            </label>
                            <select id="economic_status" name="economic_status"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                <option value="">Select Economic Status</option>
                                <option value="low_income" {{ Auth::user()->economic_status == 'low_income' ? 'selected' : '' }}>Low Income</option>
                                <option value="middle_income" {{ Auth::user()->economic_status == 'middle_income' ? 'selected' : '' }}>Middle Income</option>
                                <option value="high_income" {{ Auth::user()->economic_status == 'high_income' ? 'selected' : '' }}>High Income</option>
                            </select>
                        </div>

                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Student ID
                            </label>
                            <input type="text" id="student_id" name="student_id" value="{{ Auth::user()->student_id }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address
                        </label>
                        <textarea id="address" name="address" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">{{ Auth::user()->address }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="resetForm()"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Reset
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Section -->
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
            </div>
            <form id="password-form" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Current Password
                        </label>
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>

                    <div class="md:col-span-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile form submission
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updateProfile();
    });

    // Password form submission
    document.getElementById('password-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updatePassword();
    });
});

function updateProfile() {
    const formData = new FormData(document.getElementById('profile-form'));

    fetch('/api/user/profile', {
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!');
            // Reload page to show updated data
            window.location.reload();
        } else {
            alert('Update failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error updating profile:', error);
        alert('Update failed. Please try again.');
    });
}

function updatePassword() {
    const formData = new FormData(document.getElementById('password-form'));

    fetch('/api/user/password', {
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Password updated successfully!');
            document.getElementById('password-form').reset();
        } else {
            alert('Password update failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error updating password:', error);
        alert('Password update failed. Please try again.');
    });
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('profile-form').reset();
    }
}

function getToken() {
    return localStorage.getItem('api_token') || '';
}
</script>
@endpush
@endsection
