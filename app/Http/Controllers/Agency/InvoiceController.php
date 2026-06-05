<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Model\Invoice;
use App\Model\InvoicePayment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Helpers\Utility;
use App\Services\LogsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('agency'); // Custom middleware to ensure user is agency
    }

    public function index(Request $request): View
    {
        // Update overdue invoices before fetching
        $this->updateOverdueInvoices();

        $agencyIds = Utility::getUserWiseAgency();
		$agencyIds[] = Auth::user()->agency_fk;
        $query = Invoice::whereIn('agency_id',$agencyIds)
                       ->with(['payments'])
                       ->whereIn('status',['sent','paid','overdue'])
                       ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $invoices = $query->paginate(15);

        // Statistics for current agency - use whereIn for multiple agencies
        $stats = [
            'total' => Invoice::whereIn('agency_id', $agencyIds)->count(),
            'pending' => Invoice::whereIn('agency_id', $agencyIds)->whereIn('status', ['sent', 'overdue'])->count(),
            'paid' => Invoice::whereIn('agency_id', $agencyIds)->where('status', 'paid')->count(),
            'overdue' => Invoice::whereIn('agency_id', $agencyIds)->where('status', '!=', 'paid')->where('due_date', '<', now())->count(),
            'total_amount_due' => Invoice::whereIn('agency_id', $agencyIds)->whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
            'total_paid_this_month' => Invoice::whereIn('agency_id', $agencyIds)
                                            ->where('status', 'paid')
                                            ->whereMonth('paid_at', now()->month)
                                            ->whereYear('paid_at', now()->year)
                                            ->sum('total_amount'),
        ];

        return view('agency.invoices.index', compact('invoices', 'stats'));
    }

    public function show(Invoice $invoice): View
    {
        // Ensure the invoice belongs to the current agency
        $agencyIds = Utility::getUserWiseAgency();
        $authorized = in_array($invoice->agency_id, $agencyIds) || $invoice->agency_id === Auth::user()->agency_fk;
        if (!$authorized) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $invoice->load(['agency','items', 'payments' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'notifications' => function ($query) {
            $query->orderBy('sent_at', 'desc');
        }]);

        return view('agency.invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        // Ensure the invoice belongs to the current agency
        $agencyIds = Utility::getUserWiseAgency();
        $authorized = in_array($invoice->agency_id, $agencyIds) || $invoice->agency_id === Auth::user()->agency_fk;
        if (!$authorized) {
            abort(403, 'Unauthorized access to invoice.');
        }

        try {
            // If PDF doesn't exist, generate one on the fly
            $pdfService = app(\App\Services\InvoicePdfService::class);
            $pdf = $pdfService->generateInvoicePdf($invoice);
            self::handleLogs('Download Invoice',url("invoices/{$invoice->id}/download"),'Agency Invoice',$invoice->id,NULL,NULL,'has downloaded invoice');

            $filename = $invoice->invoice_number . '.pdf';
            return response()->make($pdf->Output($filename, 'D'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download invoice: ' . $e->getMessage());
        }
    }

    public function payment(Invoice $invoice)
    {
        // Ensure the invoice belongs to the current agency
        $agencyIds = Utility::getUserWiseAgency();
        $authorized = in_array($invoice->agency_id, $agencyIds) || $invoice->agency_id === Auth::user()->agency_fk;
        if (!$authorized) {
            abort(403, 'Unauthorized access to invoice.');
        }

        // Check if invoice can be paid
        if ($invoice->status === 'paid') {
            return redirect()->route('agency.invoices.show', $invoice)->with('info', 'This invoice has already been paid.');
        }

        if ($invoice->status === 'draft') {
            return redirect()->route('agency.invoices.show', $invoice)->with('error', 'This invoice is not yet ready for payment.');
        }

        $invoice->load('agency');

        // Available payment methods
        $paymentMethods = [
            'stripe' => [
                'name' => 'Stripe',
                'description' => 'Pay securely with your credit or debit card',
                'enabled' => !empty(config('services.stripe.key')) && config('invoice.payment_gateways.stripe.enabled', true),
            ],
            'valor' => [
                'name' => 'Valor PayTech',
                'description' => 'Pay securely with Valor payment gateway',
                'enabled' => !empty(config('services.valor.client_token')) && config('invoice.payment_gateways.valor.enabled', true),
            ],
        ];
        return view('agency.invoices.payment', compact('invoice', 'paymentMethods'));
    }

    public function paymentHistory(Request $request): View|StreamedResponse
    {
        $agencyId = Auth::user()->agency_fk;

        $query = InvoicePayment::whereHas('invoice', function ($q) use ($agencyId) {
                                    $q->where('agency_id', $agencyId);
                                })
                                ->with(['invoice'])
                                ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Handle CSV export
        if ($request->filled('export') && $request->export === 'csv') {
            return $this->exportPaymentsCSV($query->get());
        }

        $payments = $query->paginate(15);

        $stats = [
            'total_payments' => InvoicePayment::whereHas('invoice', function ($q) use ($agencyId) {
                                    $q->where('agency_id', $agencyId);
                                })->where('status', 'completed')->count(),
            'total_amount_paid' => InvoicePayment::whereHas('invoice', function ($q) use ($agencyId) {
                                       $q->where('agency_id', $agencyId);
                                   })->where('status', 'completed')->sum('amount'),
            'payments_this_month' => InvoicePayment::whereHas('invoice', function ($q) use ($agencyId) {
                                         $q->where('agency_id', $agencyId);
                                     })
                                     ->where('status', 'completed')
                                     ->whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->count(),
            'amount_this_month' => InvoicePayment::whereHas('invoice', function ($q) use ($agencyId) {
                                       $q->where('agency_id', $agencyId);
                                   })
                                   ->where('status', 'completed')
                                   ->whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->sum('amount'),
        ];

        return view('agency.invoices.payment-history', compact('payments', 'stats'));
    }

    public function downloadReceipt(InvoicePayment $payment)
    {
        // Ensure the payment belongs to an invoice of the current agency
        $agencyIds = Utility::getUserWiseAgency();
        $authorized = in_array($payment->agency_id, $agencyIds) || $payment->agency_id === Auth::user()->agency_fk;
        if (!$authorized) {
            abort(403, 'Unauthorized access to Payment receipt.');
        }


        if ($payment->status !== 'completed') {
            return back()->with('error', 'Receipt is only available for completed payments.');
        }

        try {
            $pdfService = app(\App\Services\InvoicePdfService::class);
            $pdf = $pdfService->generatePaymentReceipt($payment->invoice, $payment);
            self::handleLogs('Download Payment Receipt',url("payments/{$payment->invoice->id}/receipt"),'Agency Invoice',$payment->invoice->id,NULL,NULL,'has downloaded Payment Receipt');

            $filename = 'receipt_' . $payment->invoice->invoice_number . '_' . $payment->id . '.pdf';
            return response()->make($pdf->Output($filename, 'D'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download receipt: ' . $e->getMessage());
        }
    }

    public function dashboard(): View
    {
        $agencyIds = Utility::getUserWiseAgency();
        $agencyIds[] = Auth::user()->agency_fk;

        // Recent invoices
        $recentInvoices = Invoice::whereIn('agency_id', $agencyIds)
                                ->with('payments')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();

        // Overdue invoices
        $overdueInvoices = Invoice::whereIn('agency_id', $agencyIds)
                                 ->where('status', '!=', 'paid')
                                 ->where('due_date', '<', now())
                                 ->orderBy('due_date', 'asc')
                                 ->limit(5)
                                 ->get();

        // Due soon invoices (next 7 days)
        $dueSoonInvoices = Invoice::whereIn('agency_id', $agencyIds)
                                 ->where('status', '!=', 'paid')
                                 ->whereBetween('due_date', [now(), now()->addDays(7)])
                                 ->orderBy('due_date', 'asc')
                                 ->limit(5)
                                 ->get();

        // Monthly statistics
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $month->format('M Y'),
                'invoices' => Invoice::whereIn('agency_id', $agencyIds)
                                   ->whereYear('created_at', $month->year)
                                   ->whereMonth('created_at', $month->month)
                                   ->count(),
                'amount' => Invoice::whereIn('agency_id', $agencyIds)
                                 ->whereYear('created_at', $month->year)
                                 ->whereMonth('created_at', $month->month)
                                 ->sum('total_amount'),
                'paid' => Invoice::whereIn('agency_id', $agencyIds)
                               ->where('status', 'paid')
                               ->whereYear('paid_at', $month->year)
                               ->whereMonth('paid_at', $month->month)
                               ->sum('total_amount'),
            ];
        }

        $stats = [
            'total_invoices' => Invoice::whereIn('agency_id', $agencyIds)->count(),
            'pending_invoices' => Invoice::whereIn('agency_id', $agencyIds)->whereIn('status', ['sent', 'overdue'])->count(),
            'paid_invoices' => Invoice::whereIn('agency_id', $agencyIds)->where('status', 'paid')->count(),
            'overdue_invoices' => Invoice::whereIn('agency_id', $agencyIds)->where('status', '!=', 'paid')->where('due_date', '<', now())->count(),
            'total_outstanding' => Invoice::whereIn('agency_id', $agencyIds)->whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
            'total_paid_ytd' => Invoice::whereIn('agency_id', $agencyIds)
                                     ->where('status', 'paid')
                                     ->whereYear('paid_at', now()->year)
                                     ->sum('total_amount'),
        ];

        return view('agency.invoices.dashboard', compact(
            'recentInvoices',
            'overdueInvoices',
            'dueSoonInvoices',
            'monthlyStats',
            'stats'
        ));
    }

    private function exportPaymentsCSV($payments) : StreamedResponse
    {
        $filename = 'payment-history-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Payment Date',
                'Invoice Number',
                'Invoice Title',
                'Amount',
                'Payment Method',
                'Transaction ID',
                'Status',
                'Created At'
            ]);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->invoice->invoice_number,
                    $payment->invoice->title ?? '',
                    number_format($payment->amount, 2),
                    $payment->payment_method_label,
                    $payment->transaction_id ?? '',
                    ucfirst($payment->status),
                    $payment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function handleLogs($type,$link,$module,$object_id,$new_response,$old_response,$message){
        $user = auth()->user();
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => $type,
            'link' => $link,
            'module' => $module,
            'object_id' => $object_id,
            'message' => $user->first_name . ' ' . $user->last_name . ' '.$message,
            'new_response' => serialize($new_response),
            'old_response' => serialize($old_response),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
    }

    /**
     * Update invoices that are past due date to overdue status
     */
    private function updateOverdueInvoices()
    {
        Invoice::where('status', '!=', 'paid')
               ->where('status', '!=', 'overdue')
               ->where('due_date', '<', now())
               ->update(['status' => 'overdue']);
    }
}