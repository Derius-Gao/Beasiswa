<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\BillController as AdminBillController;
use App\Http\Controllers\Admin\ScholarshipController as AdminScholarshipCrudController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [DashboardController::class, 'showLogin'])->name('login');
    Route::post('/login', [DashboardController::class, 'login'])->name('login.post');
    Route::get('/register', [DashboardController::class, 'showRegister'])->name('register');
    Route::post('/register', [DashboardController::class, 'register'])->name('register.post');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/bills', [DashboardController::class, 'bills'])->name('dashboard.bills');
    Route::post('/dashboard/bills/{bill}/pay', [DashboardController::class, 'payBill'])->name('dashboard.bills.pay');
    Route::get('/dashboard/scholarships', [DashboardController::class, 'scholarships'])->name('dashboard.scholarships');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    // Chatbot Route
    Route::get('/chatbot', [DashboardController::class, 'chatbot'])->name('chatbot');
    Route::post('/chatbot/message', \App\Http\Controllers\ChatbotWebController::class)->name('chatbot.message');
    // Logout Route
    Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');

    // Admin Routes (middleware would be added for admin role check)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/scholarships-overview', [AdminController::class, 'scholarships'])->name('scholarships.overview');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');

        Route::resource('bills', AdminBillController::class)->except(['show']);
        Route::resource('scholarships', AdminScholarshipCrudController::class)->except(['show']);
        Route::resource('notifications', AdminNotificationController::class)->only(['index', 'create', 'store', 'update', 'destroy']);
    });
});
