<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ScholarshipController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\ChatbotController;
use App\Http\Controllers\API\AIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Payment routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('payments', PaymentController::class);
    Route::post('payments/{payment}/process', [PaymentController::class, 'processPayment']);
    Route::get('bills/my-bills', [PaymentController::class, 'myBills']);
    Route::post('bills/{bill}/pay', [PaymentController::class, 'payBill']);
});

// Scholarship routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('scholarships/recommendations', [ScholarshipController::class, 'recommendations']);
    Route::post('scholarships/{scholarship}/apply', [ScholarshipController::class, 'apply']);
});

// Report routes (admin only)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('reports/financial-summary', [ReportController::class, 'financialSummary']);
    Route::get('reports/payment-trends', [ReportController::class, 'paymentTrends']);
    Route::get('reports/export/excel', [ReportController::class, 'exportExcel']);
    Route::get('reports/export/pdf', [ReportController::class, 'exportPDF']);
    Route::get('reports/user/{user}/payment-history', [ReportController::class, 'userPaymentHistory']);
    Route::get('reports/scholarship-distribution', [ReportController::class, 'scholarshipReport']);
});

// Chatbot routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('chatbot/message', [ChatbotController::class, 'sendMessage']);
});
Route::post('chatbot/public', [ChatbotController::class, 'publicMessage']);

// Transaction monitoring (admin only)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('transactions/anomalies', [PaymentController::class, 'anomalies']);
    Route::post('transactions/{transaction}/flag', [PaymentController::class, 'flagTransaction']);
    Route::post('transactions/{transaction}/block', [PaymentController::class, 'blockTransaction']);
    Route::get('ai/health', [AIController::class, 'health']);
});
