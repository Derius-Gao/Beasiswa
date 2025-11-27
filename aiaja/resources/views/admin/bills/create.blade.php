@extends('layouts.app')

@section('title', 'Create Bill')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create Bill</h1>
        <p class="text-gray-600 mt-2">Issue a new invoice for a student</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.bills.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                <select name="user_id" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" @selected(old('user_id') == $student->id)>
                            {{ $student->name }} {{ $student->student_id ? "({$student->student_id})" : '' }}
                        </option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <input type="text" name="type" value="{{ old('type') }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    @error('due_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @foreach (['unpaid' => 'Unpaid', 'overdue' => 'Overdue', 'paid' => 'Paid'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', 'unpaid') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Additional context">{{ old('description') }}</textarea>
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.bills.index') }}" class="px-4 py-2 border rounded-lg text-gray-700">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Bill
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


