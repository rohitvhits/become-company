<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Model\Invoice;
use App\Model\InvoicePayment;
use App\Helpers\Utility;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InvoiceReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('agency');
    }

    /**
     * Invoice Reports Dashboard for Agency
     */
    public function index(): View
    {
        $agencyIds = Utility::getUserWiseAgency();
        $agencyIds[] = Auth::user()->agency_fk;

        $data = [
            'reports' => [
                [
                    'title' => 'Invoice Summary Report',
                    'description' => 'Comprehensive overview of your agency invoices with totals and statistics',
                    'icon' => 'mdi-chart-line',
                    'color' => 'primary',
                    'route' => 'agency.reports.invoices.summary'
                ],
                [
                    'title' => 'Paid Invoices Report',
                    'description' => 'Your completed invoice payments with payment timeline and completion metrics',
                    'icon' => 'mdi-check-circle',
                    'color' => 'success',
                    'route' => 'agency.reports.invoices.paid'
                ],
                [
                    'title' => 'Outstanding Invoices Report',
                    'description' => 'Report of unpaid and overdue invoices with aging analysis',
                    'icon' => 'mdi-alert-circle',
                    'color' => 'warning',
                    'route' => 'agency.reports.invoices.outstanding'
                ],
                [
                    'title' => 'Revenue Report',
                    'description' => 'Your revenue trends with monthly and quarterly breakdown, growth metrics, and payment analysis',
                    'icon' => 'mdi-cash-multiple',
                    'color' => 'success',
                    'route' => 'agency.reports.invoices.revenue'
                ]
            ]
        ];

        return view('agency.reports.invoices.index', $data);
    }

    /**
     * Invoice Summary Report for Agency
     */
    public function summaryReport(Request $request): View|StreamedResponse
    {
        try {
            $agencyIds = Utility::getUserWiseAgency();
            $agencyIds[] = Auth::user()->agency_fk;
            $filters = $this->getFilters($request);

            // Base query with agency filter
            $baseQuery = Invoice::with(['agency', 'payments'])
                ->whereIn('agency_id', $agencyIds)
                ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
                ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
                ->when($filters['status'], fn($q) => $q->where('status', $filters['status']));

            // Get detailed invoices for both view and export
            $invoices = $baseQuery->orderBy('created_at', 'desc')->get();

            // Handle CSV export
            if ($request->get('export') === 'csv') {
                return $this->exportSummaryCSV(['invoices' => $invoices]);
            }

            // Summary statistics
            $stats = [
                'total_invoices' => $baseQuery->count(),
                'total_amount' => $baseQuery->sum('total_amount') ?? 0,
                'total_paid' => Invoice::whereIn('agency_id', $agencyIds)
                    ->where('status', 'paid')
                    ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
                    ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
                    ->sum('total_amount') ?? 0,
                'total_outstanding' => Invoice::whereIn('agency_id', $agencyIds)
                    ->whereIn('status', ['sent', 'overdue'])
                    ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
                    ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
                    ->sum('total_amount') ?? 0,
                'overdue_count' => Invoice::whereIn('agency_id', $agencyIds)
                    ->where('status', 'overdue')
                    ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
                    ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
                    ->count(),
                'overdue_amount' => Invoice::whereIn('agency_id', $agencyIds)
                    ->where('status', 'overdue')
                    ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
                    ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
                    ->sum('total_amount') ?? 0,
            ];

            // Status breakdown
            $statusBreakdown = $baseQuery->selectRaw('status, count(*) as count, sum(total_amount) as amount')
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            // Monthly breakdown
            $monthlyBreakdown = $baseQuery->selectRaw("
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    count(*) as count,
                    sum(total_amount) as amount,
                    sum(case when status = 'paid' then total_amount else 0 end) as paid_amount
                ")
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();

            $data = [
                'stats' => $stats,
                'statusBreakdown' => $statusBreakdown,
                'monthlyBreakdown' => $monthlyBreakdown,
                'invoices' => $invoices,
                'filters' => $filters,
                'title' => 'Invoice Summary Report'
            ];

            return view('agency.reports.invoices.summary', $data);

        } catch (\Exception $e) {
            \Log::error('Agency Invoice Summary Report Error: ' . $e->getMessage());

            return view('agency.reports.invoices.summary', [
                'stats' => [
                    'total_invoices' => 0,
                    'total_amount' => 0,
                    'total_paid' => 0,
                    'total_outstanding' => 0,
                    'overdue_count' => 0,
                    'overdue_amount' => 0,
                ],
                'statusBreakdown' => collect(),
                'monthlyBreakdown' => collect(),
                'invoices' => collect(),
                'filters' => $this->getFilters($request),
                'title' => 'Invoice Summary Report',
                'error' => 'An error occurred while generating the report. Please try again.'
            ]);
        }
    }

    /**
     * Paid Invoices Report for Agency
     */
    public function paidReport(Request $request): View|StreamedResponse
    {
        $agencyIds = Utility::getUserWiseAgency();
        $agencyIds[] = Auth::user()->agency_fk;
        $filters = $this->getFilters($request);

        // Base query for paid invoices
        $baseQuery = Invoice::with(['agency', 'payments'])
            ->whereIn('agency_id', $agencyIds)
            ->where('status', 'paid')
            ->when($filters['date_from'], fn($q) => $q->whereDate('paid_at', '>=', $filters['date_from']))
            ->when($filters['date_to'], fn($q) => $q->whereDate('paid_at', '<=', $filters['date_to']));

        // Get all paid invoices
        $invoices = $baseQuery->orderBy('paid_at', 'desc')->get();

        // Handle CSV export
        if ($request->get('export') === 'csv') {
            return $this->exportPaidCSV(['invoices' => $invoices]);
        }

        // Calculate payment completion metrics
        $avgPaymentDays = $invoices->whereNotNull('paid_at')->map(function($invoice) {
            return $invoice->created_at->diffInDays($invoice->paid_at);
        })->avg();

        // Analyze partial vs full payments
        $partialPayments = $invoices->filter(function($invoice) {
            return $invoice->payments->count() > 1;
        });

        $fullPayments = $invoices->filter(function($invoice) {
            return $invoice->payments->count() === 1;
        });

        $stats = [
            'total_paid_invoices' => $invoices->count(),
            'total_paid_amount' => $invoices->sum('total_amount'),
            'average_invoice_amount' => $invoices->avg('total_amount'),
            'average_payment_time' => round($avgPaymentDays, 1),
            'payment_completion_rate' => $baseQuery->count() > 0 ?
                round(($invoices->count() / $baseQuery->count()) * 100, 2) : 0,
            'partial_payments_count' => $partialPayments->count(),
            'full_payments_count' => $fullPayments->count(),
            'multi_transaction_percentage' => $invoices->count() > 0 ?
                round(($partialPayments->count() / $invoices->count()) * 100, 2) : 0,
        ];

        // Payment method breakdown from the invoices' payments
        $allPayments = $invoices->flatMap->payments->where('status', 'completed');
        $paymentMethodBreakdown = $allPayments->groupBy('payment_method')->map(function($payments, $method) {
            return (object)[
                'payment_method' => $method,
                'count' => $payments->count(),
                'amount' => $payments->sum('amount')
            ];
        })->values();

        // Daily payment trend
        $dailyTrend = $invoices->whereNotNull('paid_at')->groupBy(function($invoice) {
            return $invoice->paid_at->format('Y-m-d');
        })->map(function($invoices, $date) {
            return (object)[
                'date' => $date,
                'count' => $invoices->count(),
                'amount' => $invoices->sum('total_amount')
            ];
        })->sortByDesc('date')->take(30)->values();

        $data = [
            'stats' => $stats,
            'paymentMethodBreakdown' => $paymentMethodBreakdown,
            'dailyTrend' => $dailyTrend,
            'invoices' => $invoices,
            'filters' => $filters,
            'title' => 'Paid Invoices Report - Invoice Summary'
        ];

        return view('agency.reports.invoices.paid', $data);
    }

    /**
     * Outstanding Invoices Report for Agency
     */
    public function outstandingReport(Request $request): View|StreamedResponse
    {
        $agencyIds = Utility::getUserWiseAgency();
        $agencyIds[] = Auth::user()->agency_fk;
        $filters = $this->getFilters($request);

        $invoicesQuery = Invoice::with(['agency'])
            ->whereIn('agency_id', $agencyIds)
            ->whereIn('status', ['sent', 'overdue'])
            ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']));

        $stats = [
            'total_outstanding_invoices' => $invoicesQuery->count(),
            'total_outstanding_amount' => $invoicesQuery->sum('total_amount'),
            'overdue_invoices' => $invoicesQuery->where('status', 'overdue')->count(),
            'overdue_amount' => $invoicesQuery->where('status', 'overdue')->sum('total_amount'),
            'average_outstanding_amount' => $invoicesQuery->avg('total_amount'),
        ];

        // Aging analysis
        $invoicesagQuery = Invoice::with(['agency'])
            ->whereIn('agency_id', $agencyIds)
            ->whereIn('status', ['sent', 'overdue'])
            ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']));
        $agingAnalysis = [
            'current' => $invoicesagQuery->whereRaw('DATEDIFF(NOW(), due_date) <= 0')->sum('total_amount'),
            '1_30_days' => $invoicesagQuery->whereRaw('DATEDIFF(NOW(), due_date) BETWEEN 1 AND 30')->sum('total_amount'),
            '31_60_days' => $invoicesagQuery->whereRaw('DATEDIFF(NOW(), due_date) BETWEEN 31 AND 60')->sum('total_amount'),
            '61_90_days' => $invoicesagQuery->whereRaw('DATEDIFF(NOW(), due_date) BETWEEN 61 AND 90')->sum('total_amount'),
            'over_90_days' => $invoicesagQuery->whereRaw('DATEDIFF(NOW(), due_date) > 90')->sum('total_amount'),
        ];

        $invoicesmsQuery = Invoice::with(['agency'])
            ->whereIn('agency_id', $agencyIds)
            ->whereIn('status', ['sent', 'overdue'])
            ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']));
        $invoices = $invoicesmsQuery->orderBy('due_date', 'asc')->get();

        $data = [
            'stats' => $stats,
            'agingAnalysis' => $agingAnalysis,
            'invoices' => $invoices,
            'filters' => $filters,
            'title' => 'Outstanding Invoices Report'
        ];

        if ($request->get('export') === 'csv') {
            return $this->exportOutstandingCSV($data);
        }

        return view('agency.reports.invoices.outstanding', $data);
    }

    /**
     * Payment History Report for Agency
     */
    public function paymentReport(Request $request): View|StreamedResponse
    {
        $agencyIds = Utility::getUserWiseAgency();
        $agencyIds[] = Auth::user()->agency_fk;
        $filters = $this->getFilters($request);

        $paymentsQuery = InvoicePayment::with(['invoice.agency'])
            ->whereHas('invoice', function($q) use ($agencyIds) {
                $q->whereIn('agency_id', $agencyIds);
            })
            ->where('status', 'completed')
            ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->when($filters['payment_method'], fn($q) => $q->where('payment_method', $filters['payment_method']));

        // Get base data for stats
        $basePayments = $paymentsQuery->get();

        // Calculate total processing fees
        $totalProcessingFees = $basePayments->sum('processing_fee');

        // Failed payments analysis (move before stats)
        $failedPayments = InvoicePayment::with(['invoice.agency'])
            ->whereHas('invoice', function($q) use ($agencyIds) {
                $q->whereIn('agency_id', $agencyIds);
            })
            ->where('status', 'failed')
            ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->get();

        // Calculate refunds
        $refundedPayments = InvoicePayment::with(['invoice.agency'])
            ->whereHas('invoice', function($q) use ($agencyIds) {
                $q->whereIn('agency_id', $agencyIds);
            })
            ->where('status', 'refunded')
            ->when($filters['date_from'], fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'], fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->get();

        $stats = [
            'total_payments' => $basePayments->count(),
            'total_payment_amount' => $basePayments->sum('amount'),
            'average_payment_amount' => $basePayments->avg('amount'),
            'unique_invoices' => $basePayments->unique('invoice_id')->count(),
            'total_processing_fees' => $totalProcessingFees,
            'net_payment_amount' => $basePayments->sum('amount') - $totalProcessingFees,
            'refunded_count' => $refundedPayments->count(),
            'refunded_amount' => $refundedPayments->sum('amount'),
            'failed_payment_count' => $failedPayments->count(),
        ];

        // Payment method breakdown with fees
        $paymentMethodStats = $basePayments->groupBy('payment_method')->map(function($payments, $method) {
            return (object)[
                'payment_method' => $method,
                'count' => $payments->count(),
                'amount' => $payments->sum('amount'),
                'avg_amount' => $payments->avg('amount'),
                'total_fees' => $payments->sum('processing_fee'),
                'net_amount' => $payments->sum('amount') - $payments->sum('processing_fee')
            ];
        })->values();

        // Daily payment trend
        $dailyPayments = $basePayments->groupBy(function($payment) {
            return $payment->created_at->format('Y-m-d');
        })->map(function($payments, $date) {
            return (object)[
                'date' => $date,
                'count' => $payments->count(),
                'amount' => $payments->sum('amount')
            ];
        })->sortByDesc('date')->take(30)->values();

        $payments = $basePayments->sortByDesc('created_at');

        $data = [
            'stats' => $stats,
            'paymentMethodStats' => $paymentMethodStats,
            'dailyPayments' => $dailyPayments,
            'failedPayments' => $failedPayments,
            'refundedPayments' => $refundedPayments,
            'payments' => $payments,
            'filters' => $filters,
            'paymentMethods' => ['stripe', 'paypal', 'manual'],
            'title' => 'Payment Activity Report - Transaction Details'
        ];

        if ($request->get('export') === 'csv') {
            return $this->exportPaymentCSV($data);
        }

        return view('agency.reports.invoices.payments', $data);
    }

    /**
     * Get and validate filters from request
     */
    private function getFilters(Request $request): array
    {
        return [
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'status' => $request->get('status'),
            'payment_method' => $request->get('payment_method'),
        ];
    }

    /**
     * Export Summary Report as CSV
     */
    private function exportSummaryCSV($data): StreamedResponse
    {
        $filename = 'agency-invoice-summary-report-' . now()->format('Y-m-d') . '.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Write headers
            fputcsv($file, [
                'Invoice Number', 'Date Created', 'Due Date', 'Amount',
                'Status', 'Days Outstanding', 'Paid Date', 'Payment Method'
            ]);

            foreach ($data['invoices'] as $invoice) {
                $daysOutstanding = $invoice->status === 'paid' ? 0 :
                    Carbon::parse($invoice->due_date)->diffInDays(now(), false);

                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->created_at->format('Y-m-d'),
                    $invoice->due_date->format('Y-m-d'),
                    number_format($invoice->total_amount, 2),
                    ucfirst($invoice->status),
                    $daysOutstanding,
                    $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : '',
                    $invoice->payments->first()->payment_method ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Paid Invoices as CSV
     */
    private function exportPaidCSV($data): StreamedResponse
    {
        $filename = 'agency-paid-invoices-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Invoice Number', 'Amount', 'Created Date', 'Paid Date',
                'Payment Method', 'Transaction ID', 'Days to Pay'
            ]);

            foreach ($data['invoices'] as $invoice) {
                $payment = $invoice->payments->first();
                $daysToPay = $invoice->paid_at ?
                    Carbon::parse($invoice->created_at)->diffInDays($invoice->paid_at) : 0;

                fputcsv($file, [
                    $invoice->invoice_number,
                    number_format($invoice->total_amount, 2),
                    $invoice->created_at->format('Y-m-d'),
                    $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : '',
                    $payment->payment_method ?? '',
                    $payment->transaction_id ?? '',
                    $daysToPay
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Outstanding Invoices as CSV
     */
    private function exportOutstandingCSV($data): StreamedResponse
    {
        $filename = 'agency-outstanding-invoices-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Invoice Number', 'Amount', 'Created Date', 'Due Date',
                'Status', 'Days Overdue', 'Aging Category'
            ]);

            foreach ($data['invoices'] as $invoice) {
                $daysOverdue = Carbon::parse($invoice->due_date)->diffInDays(now(), false);
                $agingCategory = $this->getAgingCategory($daysOverdue);

                fputcsv($file, [
                    $invoice->invoice_number,
                    number_format($invoice->total_amount, 2),
                    $invoice->created_at->format('Y-m-d'),
                    $invoice->due_date->format('Y-m-d'),
                    ucfirst($invoice->status),
                    max(0, $daysOverdue),
                    $agingCategory
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Payment Report as CSV
     */
    private function exportPaymentCSV($data): StreamedResponse
    {
        $filename = 'agency-payment-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Payment Date', 'Invoice Number', 'Amount', 'Payment Method',
                'Transaction ID', 'Status', 'Processing Fee'
            ]);

            foreach ($data['payments'] as $payment) {
                fputcsv($file, [
                    $payment->created_at ? $payment->created_at->format('Y-m-d H:i:s') : '',
                    $payment->invoice->invoice_number,
                    number_format($payment->amount, 2),
                    ucfirst($payment->payment_method),
                    $payment->transaction_id ?? '',
                    ucfirst($payment->status),
                    number_format($payment->processing_fee ?? 0, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Revenue Report for Agency
     */
    public function revenueReport(Request $request): View|StreamedResponse
    {
        $agencyIds = Utility::getUserWiseAgency();
        $agencyIds[] = Auth::user()->agency_fk;
        $filters = $this->getFilters($request);

        // Get date range - default to current year
        $dateFrom = $filters['date_from'] ?? now()->startOfYear()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? now()->format('Y-m-d');

        // Base query for paid invoices (actual revenue)
        $paidInvoicesQuery = Invoice::with(['agency', 'payments'])
            ->whereIn('agency_id', $agencyIds)
            ->where('status', 'paid')
            ->whereDate('paid_at', '>=', $dateFrom)
            ->whereDate('paid_at', '<=', $dateTo);

        $paidInvoices = $paidInvoicesQuery->get();

        // Calculate total revenue metrics
        $totalRevenue = $paidInvoices->sum('total_amount');
        $totalInvoices = $paidInvoices->count();

        // Calculate processing fees from payments
        $allPayments = $paidInvoices->flatMap->payments->where('status', 'completed');
        $totalProcessingFees = $allPayments->sum('processing_fee');
        $netRevenue = $totalRevenue - $totalProcessingFees;

        // Calculate refunds
        $refundedPayments = InvoicePayment::with(['invoice'])
            ->whereHas('invoice', function($q) use ($agencyIds) {
                $q->whereIn('agency_id', $agencyIds);
            })
            ->where('status', 'refunded')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->get();

        $totalRefunds = $refundedPayments->sum('amount');

        $stats = [
            'total_revenue' => $totalRevenue,
            'net_revenue' => $netRevenue - $totalRefunds,
            'total_invoices' => $totalInvoices,
            'average_invoice_value' => $totalInvoices > 0 ? $totalRevenue / $totalInvoices : 0,
            'total_processing_fees' => $totalProcessingFees,
            'total_refunds' => $totalRefunds,
            'revenue_after_costs' => $netRevenue - $totalRefunds,
        ];

        // Monthly revenue breakdown
        $monthlyRevenue = $paidInvoices->groupBy(function($invoice) {
            return $invoice->paid_at->format('Y-m');
        })->map(function($invoices, $month) {
            return (object)[
                'month' => $month,
                'month_name' => \Carbon\Carbon::parse($month . '-01')->format('M Y'),
                'revenue' => $invoices->sum('total_amount'),
                'invoice_count' => $invoices->count(),
                'average_value' => $invoices->avg('total_amount'),
            ];
        })->sortBy('month')->values();

        // Revenue by payment method
        $paymentMethodRevenue = $allPayments->groupBy('payment_method')->map(function($payments, $method) {
            return (object)[
                'payment_method' => ucfirst($method),
                'revenue' => $payments->sum('amount'),
                'transaction_count' => $payments->count(),
                'processing_fees' => $payments->sum('processing_fee'),
                'net_revenue' => $payments->sum('amount') - $payments->sum('processing_fee'),
            ];
        })->sortByDesc('revenue')->values();

        // Year-over-year comparison
        $previousYearStart = \Carbon\Carbon::parse($dateFrom)->subYear()->format('Y-m-d');
        $previousYearEnd = \Carbon\Carbon::parse($dateTo)->subYear()->format('Y-m-d');

        $previousYearRevenue = Invoice::whereIn('agency_id', $agencyIds)
            ->where('status', 'paid')
            ->whereDate('paid_at', '>=', $previousYearStart)
            ->whereDate('paid_at', '<=', $previousYearEnd)
            ->sum('total_amount');

        $revenueGrowth = $previousYearRevenue > 0 ?
            (($totalRevenue - $previousYearRevenue) / $previousYearRevenue) * 100 : 0;

        // Daily revenue trend (last 30 days)
        $dailyRevenue = $paidInvoices->groupBy(function($invoice) {
            return $invoice->paid_at->format('Y-m-d');
        })->map(function($invoices, $date) {
            return (object)[
                'date' => $date,
                'revenue' => $invoices->sum('total_amount'),
                'invoice_count' => $invoices->count(),
            ];
        })->sortByDesc('date')->take(30)->values();

        // Quarterly breakdown with month ranges
        $quarterlyRevenue = $paidInvoices->groupBy(function($invoice) {
            return 'Q' . $invoice->paid_at->quarter . ' ' . $invoice->paid_at->year;
        })->map(function($invoices, $quarter) {
            // Extract quarter number and year
            preg_match('/Q(\d) (\d+)/', $quarter, $matches);
            $quarterNum = $matches[1] ?? 1;
            $year = $matches[2] ?? now()->year;

            // Define quarter month ranges
            $quarterMonths = [
                1 => 'Jan-Mar',
                2 => 'Apr-Jun',
                3 => 'Jul-Sep',
                4 => 'Oct-Dec'
            ];

            $quarterLabel = 'Q' . $quarterNum . ' ' . $year . ' (' . $quarterMonths[$quarterNum] . ')';

            return (object)[
                'quarter' => $quarter,
                'quarter_label' => $quarterLabel,
                'revenue' => $invoices->sum('total_amount'),
                'invoice_count' => $invoices->count(),
                'average_value' => $invoices->avg('total_amount'),
            ];
        })->values();

        $data = [
            'stats' => $stats,
            'monthlyRevenue' => $monthlyRevenue,
            'quarterlyRevenue' => $quarterlyRevenue,
            'paymentMethodRevenue' => $paymentMethodRevenue,
            'dailyRevenue' => $dailyRevenue,
            'revenueGrowth' => round($revenueGrowth, 2),
            'previousYearRevenue' => $previousYearRevenue,
            'filters' => array_merge($filters, ['date_from' => $dateFrom, 'date_to' => $dateTo]),
            'title' => 'Revenue Report'
        ];

        if ($request->get('export') === 'csv') {
            return $this->exportRevenueCSV($data);
        }

        return view('agency.reports.invoices.revenue', $data);
    }

    /**
     * Export Revenue Report as CSV
     */
    private function exportRevenueCSV($data): StreamedResponse
    {
        $filename = 'agency-revenue-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Summary section
            fputcsv($file, ['Revenue Report Summary']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Revenue', number_format($data['stats']['total_revenue'], 2)]);
            fputcsv($file, ['Net Revenue', number_format($data['stats']['net_revenue'], 2)]);
            fputcsv($file, ['Total Invoices', $data['stats']['total_invoices']]);
            fputcsv($file, ['Average Invoice Value', number_format($data['stats']['average_invoice_value'], 2)]);
            fputcsv($file, ['Total Processing Fees', number_format($data['stats']['total_processing_fees'], 2)]);
            fputcsv($file, ['Total Refunds', number_format($data['stats']['total_refunds'], 2)]);
            fputcsv($file, ['Revenue After Costs', number_format($data['stats']['revenue_after_costs'], 2)]);
            fputcsv($file, ['Revenue Growth %', number_format($data['revenueGrowth'], 2) . '%']);
            fputcsv($file, []);

            // Monthly revenue breakdown
            fputcsv($file, ['Monthly Revenue Breakdown']);
            fputcsv($file, ['Month', 'Revenue', 'Invoice Count', 'Average Value']);
            foreach ($data['monthlyRevenue'] as $month) {
                fputcsv($file, [
                    $month->month_name,
                    number_format($month->revenue, 2),
                    $month->invoice_count,
                    number_format($month->average_value, 2)
                ]);
            }
            fputcsv($file, []);

            // Quarterly breakdown
            fputcsv($file, ['Quarterly Revenue Breakdown']);
            fputcsv($file, ['Quarter', 'Revenue', 'Invoice Count', 'Average Value']);
            foreach ($data['quarterlyRevenue'] as $quarter) {
                fputcsv($file, [
                    $quarter->quarter_label,
                    number_format($quarter->revenue, 2),
                    $quarter->invoice_count,
                    number_format($quarter->average_value, 2)
                ]);
            }
            fputcsv($file, []);

            // Payment method breakdown
            fputcsv($file, ['Revenue by Payment Method']);
            fputcsv($file, ['Payment Method', 'Revenue', 'Transaction Count', 'Processing Fees', 'Net Revenue']);
            foreach ($data['paymentMethodRevenue'] as $method) {
                fputcsv($file, [
                    $method->payment_method,
                    number_format($method->revenue, 2),
                    $method->transaction_count,
                    number_format($method->processing_fees, 2),
                    number_format($method->net_revenue, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get aging category for outstanding invoices
     */
    private function getAgingCategory(int $daysOverdue): string
    {
        if ($daysOverdue <= 0) return 'Current';
        if ($daysOverdue <= 30) return '1-30 Days';
        if ($daysOverdue <= 60) return '31-60 Days';
        if ($daysOverdue <= 90) return '61-90 Days';
        return 'Over 90 Days';
    }
}