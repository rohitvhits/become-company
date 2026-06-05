<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
use App\Services\InvoiceService;
use App\Services\PaymentLogServiceService;
use Illuminate\Support\Facades\Storage;
use App\Model\PaymentLogList;
use App\Services\LogsService;

class PaymentLogListingController extends Controller
{
    protected InvoiceService $invoiceService;
    protected PaymentLogServiceService $paymentLogServiceService;

    public function __construct(InvoiceService $invoiceService, PaymentLogServiceService $paymentLogServiceService)
    {
        $this->middleware('auth');
        $this->invoiceService = $invoiceService;
        $this->paymentLogServiceService = $paymentLogServiceService;
    }

    // Listing Page
    public function index(Request $request)
    {
        // Check if AJAX request
        if ($request->ajax()) {
            return $this->getRecords($request);
        }
        $data['user'] = auth()->user();

        return view('payment_log_listing.index', $data);
    }

    // Get Records via AJAX
    public function getRecords(Request $request)
    {
        $query = DB::table('payment_logs')
            ->leftJoin('patient_master', 'payment_logs.patient_id', '=', 'patient_master.id')
            ->leftJoin('agency', 'patient_master.agency_id', '=', 'agency.id')
            ->select(
                'payment_logs.*',
                'agency.agency_name',
                'patient_master.agency_id'
            );

        // Search filters
        if ($request->filled('search_name')) {
            $query->where('payment_logs.name', 'like', '%' . $request->search_name . '%');
        }

        if ($request->filled('search_agency')) {
            $query->where('patient_master.agency_id', $request->search_agency);
        }

        if ($request->filled('search_vendor')) {
            $query->where('payment_logs.vendor_name', 'like', '%' . $request->search_vendor . '%');
        }

        if ($request->filled('search_location')) {
            $query->where('payment_logs.location', 'like', '%' . $request->search_location . '%');
        }

        if ($request->filled('search_patient_id')) {
            $query->where('payment_logs.patient_id', 'like', '%' . $request->search_patient_id . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_logs.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_logs.created_at', '<=', $request->date_to);
        }

        if ($request->filled('search_status')) {
            $query->where('payment_logs.status', $request->search_status);
        }

        // Clone query for totals before pagination
        $totalsQuery = clone $query;

        // Get totals for all filtered records
        $totals = [
            'cash' => $totalsQuery->sum('payment_logs.cash'),
            'card' => $totalsQuery->sum('payment_logs.card'),
        ];

        // Paginate results
        $records = $query->whereNull('payment_logs.deleted_at')
            ->orderBy('payment_logs.created_at', 'desc')
            ->paginate(50);

        // Add total_billed_amount for records with bill status
        $recordsArray = $records->items();
        foreach ($recordsArray as $record) {
            if ($record->status === 'bill' && $record->invoice_id) {
                // Get sum of service amounts from payment_import_logs_service table
                $totalBilled = DB::table('payment_import_logs_service')
                    ->where('payment_log_id', $record->id)
                    ->where('delete_flag', 'N')
                    ->whereNull('deleted_at')
                    ->sum('total_amount');

                $record->total_billed_amount = $totalBilled;
            } else {
                $record->total_billed_amount = null;
            }
        }

        return response()->json([
            'success' => true,
            'records' => $recordsArray,
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
                'from' => $records->firstItem(),
                'to' => $records->lastItem(),
            ],
            'totals' => $totals,
            'filters' => $request->all()
        ]);
    }

    // Delete record (AJAX) - Soft Delete
    public function delete($id)
    {
         try {
            $paymentLog = PaymentLogList::findOrFail($id);
            $paymentLog->deleted_by = auth()->user()->id;
            $paymentLog->del_flag = 'Y';
            $paymentLog->save();
            $paymentLog->delete(); // Soft delete
            self::handleLogs('Delete Payment Log',url('payment-log-listing/delete/{$id}'),'Payment Log',$id,NULL,NULL,'has deleted Payment Log');
            return response()->json([
                'success' => true,
                'message' => 'Payment log record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete record: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export to Excel
    public function export(Request $request)
    {
        $query = DB::table('payment_logs')
            ->leftJoin('patient_master', 'payment_logs.patient_id', '=', 'patient_master.id')
            ->leftJoin('agency', 'patient_master.agency_id', '=', 'agency.id')
            ->select(
                'payment_logs.*',
                'agency.agency_name',
                'patient_master.agency_id'
            );

        // Apply same filters
        if ($request->filled('search_name')) {
            $query->where('payment_logs.name', 'like', '%' . $request->search_name . '%');
        }

        if ($request->filled('search_agency')) {
            $query->where('patient_master.agency_id', $request->search_agency);
        }

        if ($request->filled('search_vendor')) {
            $query->where('payment_logs.vendor_name', 'like', '%' . $request->search_vendor . '%');
        }

        if ($request->filled('search_location')) {
            $query->where('payment_logs.location', 'like', '%' . $request->search_location . '%');
        }

        if ($request->filled('search_patient_id')) {
            $query->where('payment_logs.patient_id', 'like', '%' . $request->search_patient_id . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_logs.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_logs.created_at', '<=', $request->date_to);
        }

        if ($request->filled('search_status')) {
            $query->where('payment_logs.status', $request->search_status);
        }

        $records = $query->orderBy('payment_logs.created_at', 'desc')->get();

        // Generate CSV
        $filename = 'payment_logs_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Name',
                'DOB',
                'Portal ID',
                'Agency',
                'Vendor Name',
                'Service Type',
                'Services',
                'PPD/Q',
                'Bill',
                'Insurance',
                'Location',
                'Status',
                'Initials',
                'Created At'
            ]);

            // Data
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->name,
                    $record->dob,
                    $record->patient_id,
                    $record->agency_name ?? 'N/A',
                    $record->vendor_name,
                    $record->service_type,
                    $record->services,
                    $record->ppd_q,
                    $record->bill,
                    $record->insurance,
                    $record->location,
                    $record->status,
                    $record->initials,
                    $record->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // View Payment Log Details (AJAX)
    public function view($id)
    {
        try {
            $paymentLog = DB::table('payment_logs')->where('id', $id)->first();

            if (!$paymentLog) {
                return response()->json(['success' => false, 'message' => 'Payment log not found'], 404);
            }

            // Get patient details if patient_id exists
            $patient = null;
            $documents = [];
            $patientInfo = [];

            if ($paymentLog->patient_id) {
                $patient = DB::table('patient_master')
                    ->select('id', 'first_name', 'last_name', 'middle_name', 'type', 'mobile', 'dob', 'gender')
                    ->where('id', $paymentLog->patient_id)
                    ->first();
                // Get patient documents with creator info
                $documents = DB::table('document_patient as dp')
                    ->leftJoin('users as u', 'dp.created_by', '=', 'u.id')
                    ->where('dp.patient_id', $paymentLog->patient_id)
                    ->select(
                        'dp.id',
                        'dp.document_name',
                        'dp.attachment',
                        'dp.created_date',
                        'dp.created_by',
                        DB::raw('CONCAT(COALESCE(u.first_name, ""), " ", COALESCE(u.last_name, "")) as created_by_name')
                    )
                    ->orderBy('dp.created_date', 'desc')
                    ->get();
                foreach($documents as $doc){
                    if (str_contains($doc->attachment, 'patientdocument')) {
                        if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
                            $url = url('patientdocument/' . $doc->attachment);
                        } else {
                            $url = Storage::disk('s3')->temporaryUrl($doc->attachment, now()->addMinutes(60));
                        }
                    } else {
                        if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
                            $url = url('patientdocument/' . $doc->attachment);
                        } else {
                            $url = Storage::disk('s3')->temporaryUrl('patientdocument/' . $doc->attachment, now()->addMinutes(60));
                        }
                    }

                    $doc->attachment  = $url;
                }
            }

            // Load services using the service class
            $servicesArray = [];
            $savedServices = $this->paymentLogServiceService->getServices($id);

            if ($savedServices->count() > 0) {
                // Use saved services from database
                foreach ($savedServices as $service) {
                    $servicesArray[] = [
                        'id' => $service->id,
                        'service_name' => $service->service_name,
                        'amount' => $service->total_amount ?? 0.00
                    ];
                }
            } else {
                // Fallback: Parse from comma-separated string if no saved services
                if ($paymentLog->services) {
                    $servicesList = [$paymentLog->services];

                    foreach ($servicesList as $service) {
                        $serviceName = trim($service);
                        if (!empty($serviceName)) {
                            $servicesArray[] = [
                                'service_name' => $serviceName,
                                'amount' => 0.00
                            ];
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'payment_log' => $paymentLog,
                'patient' => $patient,
                'patient_info' => $patientInfo,
                'documents' => $documents,
                'services' => $servicesArray,
                'location_info' => [
                    'location_name' => $paymentLog->location ?? '',
                ],
                'created_at' => date('m-d-Y h:i A', strtotime($paymentLog->created_at)),
                'created_by' => $paymentLog->initials ?? 'System'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Verify Single Payment Log (AJAX)
    public function verify($id)
    {
        try {
            $paymentLog = DB::table('payment_logs')->where('id', $id)->first();

            if (!$paymentLog) {
                return response()->json(['success' => false, 'message' => 'Payment log not found'], 404);
            }

            // Check if record already has 'bill' status
            if ($paymentLog->status === 'bill') {
                return response()->json([
                    'success' => false,
                    'message' => 'This record already has bill status and cannot be reverted to verified.'
                ], 400);
            }

            // Check if record is already verified
            if ($paymentLog->status === 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'This record is already verified.'
                ], 400);
            }

            // Update record to verified status
            DB::table('payment_logs')
                ->where('id', $id)
                ->update(['status' => 'verified', 'updated_at' => now()]);

            self::handleLogs('Verify Payment Log', url('payment-log-listing/verify/' . $id), 'Payment Log', $id, NULL, NULL, 'has verified Payment Log');

            return response()->json([
                'success' => true,
                'message' => 'Payment log verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Bulk Verify Payment Logs (AJAX)
    public function bulkVerify(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No records selected'], 400);
            }

            // Check which selected records have 'bill' status
            $billStatusRecords = DB::table('payment_logs')
                ->whereIn('id', $ids)
                ->where('status', '=', 'bill')
                ->pluck('id')
                ->toArray();

            // If any records have bill status, return error
            if (!empty($billStatusRecords)) {
                $count = count($billStatusRecords);
                return response()->json([
                    'success' => false,
                    'message' => $count . ' selected record(s) already have bill status and cannot be reverted to verified. Please unselect these records and try again.'
                ], 400);
            }

            // Update records that don't have 'bill' status
            $updated = DB::table('payment_logs')
                ->whereIn('id', $ids)
                ->where('status', '!=', 'bill')
                ->update(['status' => 'verified']);
            self::handleLogs('Delete Payment Log',url('payment-log-listing/bulk-verify'),'Payment Log','',NULL,serialize($request->all),'has bulk verified Payment Log');
            return response()->json([
                'success' => true,
                'message' => $updated . ' record(s) verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Update Services for Payment Log
    public function updateServices(Request $request, $id)
    {
        try {
            $paymentLog = PaymentLogList::findOrFail($id);
            $services = $request->input('services', []);

            if (empty($services)) {
                return response()->json(['success' => false, 'message' => 'No services provided'], 400);
            }

            // Use the service class to save services
            $this->paymentLogServiceService->saveServices($id, $services);

            self::handleLogs('Update Services', url('payment-log-listing/update-services/' . $id), 'Payment Log', $id, serialize($services), NULL, 'has updated services for Payment Log');

            return response()->json([
                'success' => true,
                'message' => 'Services updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Generate Bill (Invoice) for Verified Payment Log
    public function generateBill(Request $request, $id)
    {
        try {
            $paymentLog = DB::table('payment_logs')->where('id', $id)->first();

            if (!$paymentLog) {
                return response()->json(['success' => false, 'message' => 'Payment log not found'], 404);
            }

            if (!$paymentLog->patient_id) {
                return response()->json(['success' => false, 'message' => 'Patient ID is required to generate invoice'], 400);
            }

            // Get patient and agency details
            $patient = DB::table('patient_master')->where('id', $paymentLog->patient_id)->first();

            if (!$patient) {
                return response()->json(['success' => false, 'message' => 'Patient not found'], 404);
            }

            if (!$patient->agency_id) {
                return response()->json(['success' => false, 'message' => 'Patient must be associated with an agency'], 400);
            }

            if ($patient->archived_at != "") {
                return response()->json(['success' => false, 'message' => 'Invoice generation is not allowed because the patient is archived.'], 400);
            }

            if ($patient->merge_appointment_id != "" && $patient->deleted_flag == 'Y') {
                return response()->json(['success' => false, 'message' => 'This patient is merged. Invoice generation is not allowed.'], 400);
            }

            // Get services data from request or database
            $services = $request->input('services', []);

            if (empty($services)) {
                // Try to get from database using service class
                $savedServices = $this->paymentLogServiceService->getServices($id);

                if ($savedServices->count() > 0) {
                    foreach ($savedServices as $service) {
                        $services[] = [
                            'service_name' => $service->service_name,
                            'amount' => $service->total_amount
                        ];
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'No services found to generate invoice'], 400);
                }
            }

            if (empty($services)) {
                return response()->json(['success' => false, 'message' => 'No services found to generate invoice'], 400);
            }

            // Prepare invoice items in the format expected by InvoiceService
            $invoiceItems = [];
            foreach ($services as $service) {
                $amount = floatval($service['amount'] ?? 0);
                $invoiceItems[] = [
                    'description' => $service['service_name'],
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'tax_percentage' => 0,
                    'discount_percentage' => 0
                ];
            }

            // Create a new request object with invoice data
            $invoiceRequest = new Request([
                'agency_id' => $patient->agency_id,
                'type' => 'detailed',
                'title' => 'Payment Log Invoice - ' . $paymentLog->name,
                'description' => sprintf(
                    'Invoice for %s | Portal ID: %s | Service Type: %s | Location: %s',
                    $paymentLog->name,
                    $paymentLog->patient_id ?? 'N/A',
                    $paymentLog->service_type ?? 'N/A',
                    $paymentLog->location ?? 'N/A'
                ),
                'due_date' => now()->addDays(30)->format('Y-m-d'),
                'items' => $invoiceItems,
                'tax_percentage' => 0,
                'discount_percentage' => 0,
                'terms_conditions' => 'Payment is due within 30 days of invoice date.',
                'payment_log_id' => $paymentLog->id
            ]);

            // Create the invoice using InvoiceService
            $invoice = $this->invoiceService->createDetailedInvoice($invoiceRequest);

            // Update payment log status and link to invoice
            DB::table('payment_logs')
                ->where('id', $id)
                ->update([
                    'status' => 'bill',
                    'invoice_id' => $invoice->id,
                    'updated_at' => now()
                ]);
            self::handleLogs('Generate Invoice',url('payment-log-listing/bulk-verify'),'Payment Log','',NULL,serialize($request->all),'has generated invoice via Payment Log');
            return response()->json([
                'success' => true,
                'message' => 'Invoice generated successfully!',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number
            ]);
        } catch (\Exception $e) {
            \Log::error('Invoice generation failed: ' . $e->getMessage(), [
                'payment_log_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to generate invoice: ' . $e->getMessage()], 500);
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

    public function bulkGenerateInvoice(Request $request)
    {
        $ids = $request->ids ?? [];

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No payment log IDs provided.'
            ], 400);
        }

        $successList = [];
        $failedList = [];

        foreach ($ids as $id) {
            try {
                // Clone the original request and pass it to generateBill
                $response = $this->generateBill($request, $id);

                // Convert JsonResponse to array for processing
                $responseData = $response->getData(true);

                if (!empty($responseData['success']) && $responseData['success'] === true) {
                    $successList[] = [
                        'id' => $id,
                        'invoice_id' => $responseData['invoice_id'] ?? null,
                        'invoice_number' => $responseData['invoice_number'] ?? null,
                    ];
                } else {
                    $failedList[] = [
                        'id' => $id,
                        'message' => $responseData['message'] ?? 'Unknown error',
                    ];
                }

            } catch (\Exception $e) {
                \Log::error('Bulk invoice generation failed for ID: ' . $id, [
                    'error' => $e->getMessage(),
                ]);
                $failedList[] = [
                    'id' => $id,
                    'message' => $e->getMessage(),
                ];
            }
        }

        // Summary response
        return response()->json([
            'success' => true,
            'message' => sprintf(
                'Bulk invoice generation completed. Success: %d, Failed: %d',
                count($successList),
                count($failedList)
            ),
            'details' => [
                'success' => $successList,
                'failed' => $failedList
            ]
        ]);
    }

}
