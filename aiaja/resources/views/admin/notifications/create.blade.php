@extends('layouts.app')

@section('title', 'Send Notification')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Send Notification</h1>
        <p class="text-gray-600 mt-2">Log and dispatch system alerts to users</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient</label>
                <select name="user_id" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select user</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <input type="text" name="type" value="{{ old('type', 'system') }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Channel</label>
                    <select name="channel" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                        @foreach (['app' => 'In-app', 'email' => 'Email', 'whatsapp' => 'WhatsApp', 'sms' => 'SMS'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('channel') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea name="message" rows="4"
                          class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                          required>{{ old('message') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Metadata (optional)</label>
                <textarea name="data" rows="3"
                          class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                          placeholder='{"bill_id":123}'>{{ old('data') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Use JSON format for structured payloads.</p>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 border rounded-lg text-gray-700">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Send Notification
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


