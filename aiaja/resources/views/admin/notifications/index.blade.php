@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600 mt-2">Review alerts sent to users</p>
        </div>
        <a href="{{ route('admin.notifications.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + New Notification
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Channel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($notifications as $notification)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $notification->user->name ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $notification->user->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($notification->type) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ strtoupper($notification->channel) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ optional($notification->sent_at)->diffForHumans() ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                {{ $notification->is_read ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $notification->is_read ? 'Read' : 'Unread' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <form action="{{ route('admin.notifications.update', $notification) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_read" value="{{ $notification->is_read ? 0 : 1 }}">
                                <button type="submit" class="text-blue-600 hover:text-blue-900">
                                    Mark as {{ $notification->is_read ? 'Unread' : 'Read' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Delete this notification log?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                            No notifications logged.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection


