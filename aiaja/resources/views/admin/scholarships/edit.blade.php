@extends('layouts.app')

@section('title', 'Edit Scholarship')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Scholarship</h1>
        <p class="text-gray-600 mt-2">Update scholarship details and criteria</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.scholarships.update', $scholarship) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name', $scholarship->name) }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Provider</label>
                <input type="text" name="provider" value="{{ old('provider', $scholarship->provider) }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>{{ old('description', $scholarship->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $scholarship->amount) }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application Deadline</label>
                    <input type="date" name="application_deadline"
                           value="{{ old('application_deadline', \Illuminate\Support\Carbon::parse($scholarship->application_deadline)->toDateString()) }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>

            @php
                $criteria = (array) $scholarship->criteria;
            @endphp
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Criteria (optional)</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="number" step="0.01" name="criteria[min_gpa]" placeholder="Minimum GPA"
                           value="{{ old('criteria.min_gpa', $criteria['min_gpa'] ?? '') }}"
                           class="border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <input type="text" name="criteria[economic_status]" placeholder="Economic Status"
                           value="{{ old('criteria.economic_status', $criteria['economic_status'] ?? '') }}"
                           class="border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <input type="text" name="criteria[major]" placeholder="Major"
                           value="{{ old('criteria.major', $criteria['major'] ?? '') }}"
                           class="border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" class="rounded text-blue-600"
                       @checked(old('is_active', $scholarship->is_active))>
                <span class="ml-2 text-sm text-gray-700">Active</span>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.scholarships.index') }}" class="px-4 py-2 border rounded-lg text-gray-700">Back</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Scholarship
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


