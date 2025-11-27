<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate financial summary
     */
    public function generateFinancialSummary(\DateTime $startDate = null, \DateTime $endDate = null): array
    {
        $start = $startDate ?? now()->startOfMonth();
        $end = $endDate ?? now()->endOfMonth();

        $totalIncome = Payment::whereBetween('paid_at', [$start, $end])
            ->where('status', 'completed')
            ->sum('amount_paid');

        $totalOutstanding = Bill::where('due_date', '>=', $start)
            ->where('due_date', '<=', $end)
            ->where('status', 'pending')
            ->sum('amount');

        $scholarshipsGiven = Scholarship::whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $latePayments = Payment::whereBetween('paid_at', [$start, $end])
            ->whereColumn('paid_at', '>', 'bills.due_date')
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->count();

        return [
            'period' => $start->format('Y-m') . ' to ' . $end->format('Y-m'),
            'total_income' => $totalIncome,
            'total_outstanding' => $totalOutstanding,
            'scholarships_given' => $scholarshipsGiven,
            'late_payments_count' => $latePayments,
            'collection_rate' => $totalIncome > 0 ? (($totalIncome / ($totalIncome + $totalOutstanding)) * 100) : 0
        ];
    }

    /**
     * Generate payment trends data for charts
     */
    public function generatePaymentTrends(int $months = 12): array
    {
        $trends = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $start = $date->startOfMonth();
            $end = $date->endOfMonth();

            $paid = Payment::whereBetween('paid_at', [$start, $end])
                ->where('status', 'completed')
                ->sum('amount_paid');

            $overdue = Bill::where('due_date', '<=', $end)
                ->where('status', 'pending')
                ->sum('amount');

            $trends[] = [
                'month' => $date->format('M Y'),
                'paid_amount' => $paid,
                'overdue_amount' => $overdue,
                'late_payments' => Payment::whereBetween('paid_at', [$start, $end])
                    ->whereColumn('paid_at', '>', 'bills.due_date')
                    ->join('bills', 'payments.bill_id', '=', 'bills.id')
                    ->count()
            ];
        }

        return $trends;
    }

    /**
     * Export financial report to Excel
     */
    public function exportFinancialReportExcel(\DateTime $startDate = null, \DateTime $endDate = null): string
    {
        $summary = $this->generateFinancialSummary($startDate, $endDate);
        $trends = $this->generatePaymentTrends();

        return Excel::download(new class($summary, $trends) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $summary;
            private $trends;

            public function __construct($summary, $trends)
            {
                $this->summary = $summary;
                $this->trends = $trends;
            }

            public function array(): array
            {
                $data = [
                    ['Financial Summary Report'],
                    ['Period', $this->summary['period']],
                    ['Total Income', $this->summary['total_income']],
                    ['Total Outstanding', $this->summary['total_outstanding']],
                    ['Scholarships Given', $this->summary['scholarships_given']],
                    ['Late Payments Count', $this->summary['late_payments_count']],
                    ['Collection Rate (%)', $this->summary['collection_rate']],
                    [],
                    ['Payment Trends'],
                    ['Month', 'Paid Amount', 'Overdue Amount', 'Late Payments']
                ];

                foreach ($this->trends as $trend) {
                    $data[] = [
                        $trend['month'],
                        $trend['paid_amount'],
                        $trend['overdue_amount'],
                        $trend['late_payments']
                    ];
                }

                return $data;
            }
        }, 'financial_report_' . now()->format('Y_m_d') . '.xlsx');
    }

    /**
     * Export financial report to PDF
     */
    public function exportFinancialReportPDF(\DateTime $startDate = null, \DateTime $endDate = null): string
    {
        $summary = $this->generateFinancialSummary($startDate, $endDate);
        $trends = $this->generatePaymentTrends();

        $pdf = Pdf::loadView('reports.financial', compact('summary', 'trends'));

        return $pdf->download('financial_report_' . now()->format('Y_m_d') . '.pdf');
    }

    /**
     * Generate user payment history report
     */
    public function generateUserPaymentHistory(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) return [];

        $payments = Payment::where('user_id', $userId)
            ->with('bill')
            ->orderBy('paid_at', 'desc')
            ->get();

        $totalPaid = $payments->sum('amount_paid');
        $latePayments = $payments->filter(function ($payment) {
            return $payment->paid_at > $payment->bill->due_date;
        })->count();

        return [
            'user' => $user,
            'payments' => $payments,
            'total_paid' => $totalPaid,
            'late_payments' => $latePayments,
            'payment_score' => $payments->count() > 0 ? (($payments->count() - $latePayments) / $payments->count()) * 100 : 100
        ];
    }

    /**
     * Generate scholarship distribution report
     */
    public function generateScholarshipReport(): array
    {
        $scholarships = Scholarship::with('recommendations')->get();

        $totalAmount = $scholarships->sum('amount');
        $totalRecipients = ScholarshipRecommendation::where('is_notified', true)->count();
        $avgMatchScore = ScholarshipRecommendation::avg('match_score');

        $distribution = $scholarships->map(function ($scholarship) {
            return [
                'name' => $scholarship->name,
                'amount' => $scholarship->amount,
                'recipients' => $scholarship->recommendations->where('is_notified', true)->count(),
                'avg_score' => $scholarship->recommendations->avg('match_score')
            ];
        });

        return [
            'total_scholarships' => $scholarships->count(),
            'total_amount' => $totalAmount,
            'total_recipients' => $totalRecipients,
            'avg_match_score' => $avgMatchScore,
            'distribution' => $distribution
        ];
    }
}
