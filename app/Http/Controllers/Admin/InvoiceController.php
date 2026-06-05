<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Invoice;
use App\Model\InvoicePayment;
use App\Agency;
use App\Services\InvoiceService;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\LogsService;
use App\Helpers\Utility;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;
    protected InvoicePdfService $pdfService;

    public function __construct(InvoiceService $invoiceService, InvoicePdfService $pdfService)
    {
        $this->middleware('permission:manage-invoice', ['only' => ['index', 'create','store','edit','update', 'show','destroy','downloadReceipt', 'bulkAction', 'duplicate', 'download', 'markAsPaid', 'send']]);
        $this->invoiceService = $invoiceService;
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        // Update overdue invoices before fetching
        $this->updateOverdueInvoices();

        $query = Invoice::with(['agency', 'creator'])
                       ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

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
        $agencies = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();

        // Build base query for statistics with same filters
        $statsQuery = Invoice::query();

        // Apply same filters to stats
        if ($request->filled('agency_id')) {
            $statsQuery->where('agency_id', $request->agency_id);
        }

        if ($request->filled('date_from')) {
            $statsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $statsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Statistics based on filtered results
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'draft' => (clone $statsQuery)->where('status', 'draft')->count(),
            'sent' => (clone $statsQuery)->where('status', 'sent')->count(),
            'paid' => (clone $statsQuery)->where('status', 'paid')->count(),
            'overdue' => (clone $statsQuery)->where('status', 'overdue')->count(),
            'total_revenue' => (clone $statsQuery)->where('status', 'paid')->sum('total_amount'),
            'pending_amount' => (clone $statsQuery)->whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
        ];

        return view('admin.invoices.index', compact('invoices', 'agencies', 'stats'));
    }

    public function create(): View
    {
        $agencies = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
        return view('admin.invoices.create', compact('agencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:uploaded_pdf,quick,detailed',
            'agency_id' => 'required|exists:agency,id',
            'due_date' => 'required|date',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            // Quick invoice fields
            'quick_total_amount' => 'required_if:type,quick|nullable|numeric|min:0.01',
            'pdf_total_amount' => 'required_if:type,uploaded_pdf|nullable|numeric|min:0.01',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',

            // Uploaded PDF fields
            'pdf_file' => 'required_if:type,uploaded_pdf|nullable|file|mimes:pdf|max:10240',
            'invoice_number' => 'nullable|string|unique:invoices,invoice_number',

            // Detailed invoice fields
            'items' => 'required_if:type,detailed|nullable|array|min:1',
            'items.*.description' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|numeric|min:0.01',
            'items.*.unit_price' => 'required_with:items|numeric|min:0.01',
            'items.*.tax_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $status_inv = 'Draft';
        try {
            DB::beginTransaction();

            $invoice = null;

            switch ($request->type) {
                case 'uploaded_pdf':
                    $invoice = $this->invoiceService->createUploadedPdfInvoice($request);
                    break;
                case 'quick':
                    $invoice = $this->invoiceService->createQuickInvoice($request);
                    break;
                case 'detailed':
                    $invoice = $this->invoiceService->createDetailedInvoice($request);
                    break;
                default:
                    throw new \Exception('Invalid invoice type selected.');
            }

            if (!$invoice) {
                throw new \Exception('Failed to create invoice. Please try again.');
            }

            // Handle different actions
            $action = $request->get('action', 'save_draft');
            if ($action === 'save_and_send') {
                // Send email notification (this will also update status and sent_at)
                try {
                    $this->invoiceService->sendInvoice($invoice);
                    $status_inv = 'Sent';
                } catch (\Exception $e) {
                    // Log error but don't fail the creation
                    \Log::error('Failed to send invoice email: ' . $e->getMessage(), [
                        'invoice_id' => $invoice->id,
                        'agency_id' => $invoice->agency_id
                    ]);

                    // If sending fails, still mark as sent but with a warning message
                    $invoice->update(['status' => 'sent', 'sent_at' => now()]);
                }
            }

            DB::commit();

            // Check if sending failed (status is still draft after save_and_send)
            $sendFailed = ($action === 'save_and_send' && $invoice->fresh()->status === 'draft');
            $new_response = $request->all();
            unset($new_response['pdf_file']);
            self::handleLogs('Create Invoice',url('invoices/save'),'Admin Invoice',$invoice->id,$new_response,NULL,'has created new invoice with '.$status_inv.' Status');

            if ($sendFailed) {
                $message = 'Invoice created successfully, but failed to send email. Please check the agency email address and try sending manually.';
            } else {
                $message = $action === 'save_and_send' ?
                          'Invoice created and sent successfully!' :
                          'Invoice created successfully!';
            }

            return redirect()->route('admin.invoices.show', $invoice)
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Invoice creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Failed to create invoice: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['agency', 'creator', 'items', 'payments', 'notifications.invoice']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if (!$invoice->canBeEdited()) {
            return redirect()->route('admin.invoices.show', $invoice)
                           ->with('error', 'This invoice cannot be edited.');
        }

        $agencies = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();

        $invoice->load('items');

        return view('admin.invoices.edit', compact('invoice', 'agencies'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        if (!$invoice->canBeEdited()) {
            return redirect()->route('admin.invoices.show', $invoice)
                           ->with('error', 'This invoice cannot be edited.');
        }

        $validator = Validator::make($request->all(), [
            'agency_id' => 'required|exists:agency,id',
            'due_date' => 'required|date',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($invoice->type === 'quick') {
            $validator->addRules([
                'total_amount' => 'required|numeric|min:0.01',
            ]);
        }

        if ($invoice->type === 'detailed') {
            $validator->addRules([
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string|max:255',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0.01',
                'items.*.tax_percentage' => 'nullable|numeric|min:0|max:100',
                'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            ]);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            $this->invoiceService->updateInvoice($invoice, $request);
            DB::commit();
            $new_response = $request->all();
            self::handleLogs('Update Invoice',url('invoices/update'),'Admin Invoice',$invoice->id,$new_response,$invoice,'has updated invoice');
            return redirect()->route('admin.invoices.show', $invoice)
                           ->with('success', 'Invoice updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update invoice: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function destroy(Invoice $invoice)
    {
        self::handleLogs('Delete Invoice',url('invoices/delete'),'Admin Invoice',$invoice->id,NULL,$invoice,'has deleted invoice details');
        $invoice->delete();
        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully!',
            'redirect_url' => route('admin.invoices.index'),
        ]);
    }


    public function send(Request $request, Invoice $invoice): JsonResponse
    {
        try {
            $this->invoiceService->sendInvoice($invoice, $request->get('email'));
            self::handleLogs('Sent Invoice',url('invoices/save'),'Admin Invoice',$invoice->id,NULL,NULL,'has sent invoice details');
            return response()->json([
                'success' => true,
                'message' => 'Invoice sent successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function markAsPaid(Request $request, Invoice $invoice): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:0.01|max:' . $invoice->balance,
            'payment_method' => 'required|in:manual,stripe,razorpay,paypal',
            'transaction_id' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        // Add custom validation message for amount exceeding balance
        $validator->setCustomMessages([
            'amount.max' => 'Payment amount cannot exceed the outstanding balance of $' . number_format($invoice->balance, 2) . '.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->invoiceService->markAsPaid(
                $invoice,
                $request->get('amount', $invoice->total_amount),
                $request->get('payment_method', 'manual'),
                $request->get('transaction_id'),
                $request->get('notes')
            );
            self::handleLogs('Mark As Paid',url('invoices/save'),'Admin Invoice',$invoice->id,$request->all(),$invoice,'has marked as paid invoices details');
            return response()->json([
                'success' => true,
                'message' => 'Invoice marked as paid successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark invoice as paid: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(Invoice $invoice)
    {
        try {
            if ($invoice->pdf_path && Storage::exists($invoice->pdf_path)) {
                return Storage::download($invoice->pdf_path, $invoice->invoice_number . '.pdf');
            }

            // Generate PDF on the fly
            $pdf = $this->pdfService->generateInvoicePdf($invoice);
            self::handleLogs('Download Invoice',url("invoices/{$invoice->id}/download"),'Admin Invoice',$invoice->id,NULL,NULL,'has downloaded invoice');

            $filename = $invoice->invoice_number . '.pdf';
            return response()->make($pdf->Output($filename, 'D'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download invoice: ' . $e->getMessage());
        }
    }

    public function duplicate(Invoice $invoice): RedirectResponse
    {
        try {
            $newInvoice = $this->invoiceService->duplicateInvoice($invoice);

            return redirect()->route('admin.invoices.edit', $newInvoice)
                           ->with('success', 'Invoice duplicated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to duplicate invoice: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:send,mark_paid,delete',
            'invoice_ids' => 'required|array|min:1',
            'invoice_ids.*' => 'exists:invoices,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $type = $message="";
            $invoices = Invoice::whereIn('id', $request->invoice_ids)->get();
            $results = $this->invoiceService->bulkAction($invoices, $request->action);
            if(isset($request->action)){
                if($request->action == 'send'){
                    $type = 'Bulk: Agency Send';
                    $message = "has performed bulk send to agency action on invoice";
                }elseif($request->action == 'mark_paid'){
                    $type = 'Bulk: Mark Paid';
                    $message = "has performed bulk mark paid action on invoice";
                }elseif($request->action == 'delete'){
                    $type = 'Bulk: Delete Invoice';
                    $message = "has performed bulk delete action on invoice";
                }
                self::handleLogs($type,url('invoices/bulk-action'),'Admin Invoice','',NULL,$request->all(),$message);
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk action completed. {$results['success']} succeeded, {$results['failed']} failed.",
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadReceipt(InvoicePayment $payment)
    {
        if ($payment->status !== 'completed') {
            return back()->with('error', 'Receipt is only available for completed payments.');
        }
        try {
            $pdf = $this->pdfService->generatePaymentReceipt($payment->invoice, $payment);
            self::handleLogs('Generate Payment Receipt',url("payments/{$payment->invoice->id}/receipt"),'Admin Invoice',$payment->invoice->id,NULL,NULL,'has generated Payment Receipt');

            $filename = 'receipt_' . $payment->invoice->invoice_number . '_' . $payment->id . '.pdf';
            return response()->make($pdf->Output($filename, 'D'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download receipt: ' . $e->getMessage());
        }
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