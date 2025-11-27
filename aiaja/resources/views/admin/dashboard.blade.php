@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">Manage the AIJA payment system</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-users">0</p>
                </div>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-transactions">0</p>
                </div>
            </div>
        </div>

        <!-- Suspicious Activities -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Suspicious Activities</p>
                    <p class="text-2xl font-bold text-gray-900" id="suspicious-activities">0</p>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-revenue">Rp 0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
            </div>
            <div class="p-6">
                <div id="recent-transactions" class="space-y-4">
                    <div class="text-center text-gray-500 py-8">
                        <p>Loading transactions...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Alerts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">System Alerts</h2>
            </div>
            <div class="p-6">
                <div id="system-alerts" class="space-y-4">
                    <div class="text-center text-gray-500 py-8">
                        <p>Loading alerts...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.bills.index') }}" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                <svg class="w-8 h-8 text-indigo-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="font-medium text-gray-900">Manage Bills</h3>
                <p class="text-sm text-gray-600">Add or edit invoices</p>
            </a>
            <a href="{{ route('admin.scholarships.index') }}" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="font-medium text-gray-900">Manage Scholarships</h3>
                <p class="text-sm text-gray-600">Add/edit scholarships</p>
            </a>

            <a href="{{ route('admin.reports') }}" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="font-medium text-gray-900">Generate Reports</h3>
                <p class="text-sm text-gray-600">Financial reports</p>
            </a>

            <a href="{{ route('admin.notifications.index') }}" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                <svg class="w-8 h-8 text-pink-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.868 12.683A17.925 17.925 0 0112 21c7.962 0 12-1.21 12-2.683m-12 2.683a17.925 17.925 0 01-7.132-8.317M12 21c4.411 0 8-4.03 8-9s-3.589-9-8-9-8 4.03-8 9a9.06 9.06 0 001.832 5.445"></path>
                </svg>
                <h3 class="font-medium text-gray-900">Notifications</h3>
                <p class="text-sm text-gray-600">Send alerts to users</p>
            </a>
            <a href="{{ route('admin.transactions') }}" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                <svg class="w-8 h-8 text-yellow-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="font-medium text-gray-900">Monitor Transactions</h3>
                <p class="text-sm text-gray-600">View all activities</p>
            </a>

            <button onclick="runAIScholarshipProcess()" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <h3 class="font-medium text-gray-900">Run AI Process</h3>
                <p class="text-sm text-gray-600">Update recommendations</p>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAdminStats();
    loadRecentTransactions();
    loadSystemAlerts();
});

function loadAdminStats() {
    // Load various stats - these would be real API calls
    fetch('/api/admin/stats', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('total-users').textContent = data.total_users || 0;
        document.getElementById('total-transactions').textContent = data.total_transactions || 0;
        document.getElementById('suspicious-activities').textContent = data.suspicious_activities || 0;
        document.getElementById('total-revenue').textContent = 'Rp ' + (data.total_revenue || 0).toLocaleString('id-ID');
    })
    .catch(error => {
        console.error('Error loading admin stats:', error);
    });
}

function loadRecentTransactions() {
    fetch('/api/admin/transactions/recent', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('recent-transactions');
        if (data.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-8">No recent transactions</p>';
            return;
        }

        container.innerHTML = data.slice(0, 5).map(transaction => `
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900">${transaction.user_name}</p>
                    <p class="text-sm text-gray-600">${transaction.type} - ${transaction.amount}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full ${
                        transaction.status === 'completed' ? 'bg-green-100 text-green-800' :
                        transaction.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-red-100 text-red-800'
                    }">
                        ${transaction.status}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">${new Date(transaction.created_at).toLocaleDateString()}</p>
                </div>
            </div>
        `).join('');
    })
    .catch(error => {
        console.error('Error loading transactions:', error);
        document.getElementById('recent-transactions').innerHTML = '<p class="text-gray-500 text-center py-8">Failed to load transactions</p>';
    });
}

function loadSystemAlerts() {
    fetch('/api/admin/alerts', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('system-alerts');
        if (data.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-8">No system alerts</p>';
            return;
        }

        container.innerHTML = data.map(alert => `
            <div class="p-4 border border-gray-200 rounded-lg ${
                alert.severity === 'high' ? 'border-red-200 bg-red-50' :
                alert.severity === 'medium' ? 'border-yellow-200 bg-yellow-50' :
                'border-blue-200 bg-blue-50'
            }">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 ${
                            alert.severity === 'high' ? 'text-red-600' :
                            alert.severity === 'medium' ? 'text-yellow-600' :
                            'text-blue-600'
                        }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-medium text-gray-900">${alert.title}</h4>
                        <p class="text-sm text-gray-600 mt-1">${alert.message}</p>
                        <p class="text-xs text-gray-500 mt-2">${new Date(alert.created_at).toLocaleString()}</p>
                    </div>
                </div>
            </div>
        `).join('');
    })
    .catch(error => {
        console.error('Error loading alerts:', error);
        document.getElementById('system-alerts').innerHTML = '<p class="text-gray-500 text-center py-8">Failed to load alerts</p>';
    });
}

function runAIScholarshipProcess() {
    if (!confirm('Are you sure you want to run the AI scholarship recommendation process? This may take some time.')) {
        return;
    }

    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>';
    button.disabled = true;

    fetch('/api/admin/ai/scholarship-process', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('AI scholarship process completed successfully!');
        } else {
            alert('Process failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error running AI process:', error);
        alert('Process failed. Please try again.');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function getToken() {
    return localStorage.getItem('api_token') || '';
}
</script>
@endpush
@endsection
