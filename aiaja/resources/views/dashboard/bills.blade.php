@extends('layouts.app')

@section('title', 'Bills & Payments')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Bills & Payments</h1>
        <p class="mt-2 text-gray-600">Manage your bills and payment history</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <p class="text-sm text-gray-500">Unpaid</p>
            <p class="text-3xl font-semibold text-gray-900 mt-2">{{ $summary['unpaid'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <p class="text-sm text-gray-500">Overdue</p>
            <p class="text-3xl font-semibold text-red-600 mt-2">{{ $summary['overdue'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <p class="text-sm text-gray-500">Paid</p>
            <p class="text-3xl font-semibold text-green-600 mt-2">{{ $summary['paid'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <p class="text-sm text-gray-500">Outstanding</p>
            <p class="text-3xl font-semibold text-gray-900 mt-2">
                Rp {{ number_format($summary['outstanding'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->has('payment'))
        <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200">
            {{ $errors->first('payment') }}
        </div>
    @endif

    <!-- Bills List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Your Bills</h2>
            <span class="text-sm text-gray-500">{{ $bills->count() }} records</span>
        </div>
        <div class="p-6 space-y-4">
            @forelse ($bills as $bill)
                <div class="p-4 border border-gray-100 rounded-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $bill->type }}</h3>
                            <p class="text-sm text-gray-600">Due {{ \Illuminate\Support\Carbon::parse($bill->due_date)->translatedFormat('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">
                                Rp {{ number_format($bill->amount, 0, ',', '.') }}
                            </p>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                @class([
                                    'bg-green-100 text-green-800' => $bill->status === 'paid',
                                    'bg-yellow-100 text-yellow-800' => $bill->status === 'overdue',
                                    'bg-red-100 text-red-800' => $bill->status === 'unpaid',
                                ])">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </div>
                    </div>
                    @if ($bill->payments->isNotEmpty())
                        <div class="mt-3 text-sm text-gray-600">
                            Last payment {{ $bill->payments->first()->paid_at->translatedFormat('d M Y') }}
                            • Rp {{ number_format($bill->payments->first()->amount_paid, 0, ',', '.') }}
                        </div>
                    @endif

                    @if ($bill->status !== 'paid')
                        <form action="{{ route('dashboard.bills.pay', $bill) }}" method="POST" class="bg-gray-50 rounded-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            @csrf
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Amount</label>
                                <input type="number" name="amount" step="0.01" value="{{ $bill->amount }}" required
                                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Method</label>
                                <select name="payment_method" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="e_wallet">E-Wallet</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Pay Now
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @empty
                <div class="text-center text-gray-500 py-10">
                    <p>No bills found. Once finance generates new invoices they will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
