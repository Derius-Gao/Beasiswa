@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Welcome back, {{ $user->name }}!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $statCards = [
                [
                    'label' => 'Unpaid Bills',
                    'value' => $stats['unpaid_bills'],
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'color' => 'red'
                ],
                [
                    'label' => 'Total Paid',
                    'value' => 'Rp ' . number_format($stats['total_paid'], 0, ',', '.'),
                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
                    'color' => 'green'
                ],
                [
                    'label' => 'Scholarship Matches',
                    'value' => $stats['scholarship_matches'],
                    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Unread Notifications',
                    'value' => $stats['notifications'],
                    'icon' => 'M15 17h5l-5 5v-5zM4.868 12.683A17.925 17.925 0 0112 21c7.962 0 12-1.21 12-2.683m-12 2.683a17.925 17.925 0 01-7.132-8.317M12 21c4.411 0 8-4.03 8-9s-3.589-9-8-9-8 4.03-8 9a9.06 9.06 0 001.832 5.445',
                    'color' => 'yellow'
                ],
            ];
        @endphp

        @foreach ($statCards as $card)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-{{ $card['color'] }}-100">
                        <svg class="w-6 h-6 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"></path>
                    </svg>
                </div>
                <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
        </div>

    <!-- Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Recent Bills</h2>
                    <p class="text-sm text-gray-500">Latest five bills and their statuses</p>
                </div>
                <a href="{{ route('dashboard.bills') }}" class="text-sm text-blue-600 hover:text-blue-500">View all</a>
                </div>
            <div class="p-6 space-y-4">
                @forelse ($recentBills as $bill)
                    <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $bill->type }}</h3>
                            <p class="text-sm text-gray-600">
                                Due {{ \Illuminate\Support\Carbon::parse($bill->due_date)->translatedFormat('d M Y') }}
                            </p>
                </div>
                        <div class="text-right">
                            <p class="font-semibold">Rp {{ number_format($bill->amount, 0, ',', '.') }}</p>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($bill->status) }}
                            </span>
            </div>
        </div>
                @empty
                    <div class="text-center text-gray-500 py-6">
                        <p>No bills found.</p>
                </div>
                @endforelse
        </div>
    </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">AI Insights</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="border border-gray-100 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Payment Delay Risk</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $insights['delay_risk'] }}%</p>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $insights['delay_risk'] >= 70 ? 'High' : ($insights['delay_risk'] >= 40 ? 'Medium' : 'Low') }} risk based on your recent payment history.
                    </p>
        </div>
                <div class="text-sm text-gray-600">
                    <p>Past due bills and reminder engagement influence this score. Settle upcoming bills early to keep it low.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scholarship Recommendations -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
            <h2 class="text-lg font-semibold text-gray-900">Scholarship Recommendations</h2>
                <p class="text-sm text-gray-500">Generated from your profile & AI matching</p>
        </div>
            <a href="{{ route('dashboard.scholarships') }}" class="text-sm text-blue-600 hover:text-blue-500">Manage applications</a>
                </div>
        <div class="p-6 space-y-4">
            @forelse ($scholarshipRecommendations as $recommendation)
                <div class="p-4 border border-gray-100 rounded-lg flex items-center justify-between">
            <div>
                        <h3 class="font-medium text-gray-900">{{ $recommendation->scholarship->name }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ $recommendation->scholarship->provider }} •
                            Deadline {{ \Illuminate\Support\Carbon::parse($recommendation->scholarship->application_deadline)->translatedFormat('d M Y') }}
                        </p>
            </div>
            <div class="text-right">
                        <p class="font-semibold text-green-600">{{ number_format($recommendation->match_score, 0) }}% match</p>
                        <a href="{{ route('dashboard.scholarships') }}" class="mt-2 text-sm text-blue-600 hover:text-blue-500 inline-flex items-center">
                            Apply now
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
            </div>
        </div>
            @empty
                <div class="text-center text-gray-500 py-6">
                    <p>No scholarship recommendations yet. Update your academic profile to unlock personalized matches.</p>
            </div>
            @endforelse
            </div>
            </div>
        </div>
@endsection
