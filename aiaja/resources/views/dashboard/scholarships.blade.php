@extends('layouts.app')

@section('title', 'Scholarships')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Scholarships</h1>
        <p class="mt-2 text-gray-600">AI-powered scholarship recommendations for you</p>
    </div>

    <!-- Recommendations Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Your Recommendations</h2>
            <span class="text-sm text-gray-500">{{ $recommendations->count() }} matches</span>
        </div>
        <div class="p-6 space-y-4">
            @forelse ($recommendations as $rec)
                <div class="border border-gray-100 rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $rec->scholarship->name }}</h3>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    {{ number_format($rec->match_score, 0) }}% match
                                </span>
                            </div>
                            <p class="text-gray-600 mb-3">{{ $rec->scholarship->description ?? 'No description available' }}</p>
                            <dl class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-700">Amount</dt>
                                    <dd class="text-gray-900">Rp {{ number_format($rec->scholarship->amount, 0, ',', '.') }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Deadline</dt>
                                    <dd class="text-gray-900">
                                        {{ \Illuminate\Support\Carbon::parse($rec->scholarship->application_deadline)->translatedFormat('d M Y') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <a href="#available" class="ml-4 px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    <p>No recommendations yet. Keep your academic profile updated to receive matches.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- All Available Scholarships -->
    <div id="available" class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">All Available Scholarships</h2>
            <span class="text-sm text-gray-500">{{ $availableScholarships->count() }} programs</span>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($availableScholarships as $scholarship)
                <div class="border border-gray-100 rounded-lg p-5 flex flex-col">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $scholarship->name }}</h3>
                    <p class="text-sm text-gray-600 mt-1 flex-1">
                        {{ \Illuminate\Support\Str::limit($scholarship->description, 120) }}
                    </p>
                    <dl class="mt-4 space-y-1 text-sm text-gray-700">
                        <div class="flex justify-between">
                            <dt>Amount</dt>
                            <dd class="text-gray-900">Rp {{ number_format($scholarship->amount, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Deadline</dt>
                            <dd class="text-gray-900">
                                {{ \Illuminate\Support\Carbon::parse($scholarship->application_deadline)->translatedFormat('d M Y') }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Provider</dt>
                            <dd class="text-gray-900">{{ $scholarship->provider }}</dd>
                        </div>
                    </dl>
                    <span class="mt-4 inline-flex items-center text-sm text-blue-600">
                        Integrated application coming soon
                    </span>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 py-10">
                    <p>No active scholarships available right now. Please check again later.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
