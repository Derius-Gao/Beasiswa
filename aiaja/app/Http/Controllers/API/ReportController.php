<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Get financial summary
     */
    public function financialSummary(Request $request)
    {
        $startDate = $request->has('start_date') ? new \DateTime($request->start_date) : null;
        $endDate = $request->has('end_date') ? new \DateTime($request->end_date) : null;

        $summary = $this->reportService->generateFinancialSummary($startDate, $endDate);

        return response()->json($summary);
    }

    /**
     * Get payment trends data
     */
    public function paymentTrends(Request $request)
    {
        $months = $request->get('months', 12);
        $trends = $this->reportService->generatePaymentTrends($months);

        return response()->json($trends);
    }

    /**
     * Export financial report as Excel
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->has('start_date') ? new \DateTime($request->start_date) : null;
        $endDate = $request->has('end_date') ? new \DateTime($request->end_date) : null;

        return $this->reportService->exportFinancialReportExcel($startDate, $endDate);
    }

    /**
     * Export financial report as PDF
     */
    public function exportPDF(Request $request)
    {
        $startDate = $request->has('start_date') ? new \DateTime($request->start_date) : null;
        $endDate = $request->has('end_date') ? new \DateTime($request->end_date) : null;

        return $this->reportService->exportFinancialReportPDF($startDate, $endDate);
    }

    /**
     * Get user payment history
     */
    public function userPaymentHistory(Request $request, $userId)
    {
        $history = $this->reportService->generateUserPaymentHistory($userId);

        return response()->json($history);
    }

    /**
     * Get scholarship distribution report
     */
    public function scholarshipReport(Request $request)
    {
        $report = $this->reportService->generateScholarshipReport();

        return response()->json($report);
    }
}
