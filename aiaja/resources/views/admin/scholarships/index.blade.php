@extends('layouts.app')

@section('title', 'Manage Scholarships')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Scholarships</h1>
            <p class="text-gray-600 mt-2">Create and manage scholarship programs</p>
        </div>
        <a href="{{ route('admin.scholarships.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + New Scholarship
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($scholarships as $scholarship)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $scholarship->name }}</div>
                            <div class="text-sm text-gray-500 line-clamp-1">{{ $scholarship->description }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $scholarship->provider }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">Rp {{ number_format($scholarship->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ \Illuminate\Support\Carbon::parse($scholarship->application_deadline)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                {{ $scholarship->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.scholarships.edit', $scholarship) }}" class="text-blue-600 hover:text-blue-900">
                                Edit
                            </a>
                            <form action="{{ route('admin.scholarships.destroy', $scholarship) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Delete this scholarship?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                            No scholarships found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $scholarships->links() }}
        </div>
    </div>
</div>
@endsection


