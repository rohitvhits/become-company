<?php

use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\InvoiceReportController;
use App\Http\Controllers\Agency\InvoiceController as AgencyInvoiceController;
use App\Http\Controllers\Agency\InvoiceReportController as AgencyInvoiceReportController;
use App\Http\Controllers\Agency\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Invoice Module Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the Invoice Module. These routes are loaded
| by the RouteServiceProvider within a group which contains the "web"
| middleware group.
|
*/

Route::prefix('account')->group(function () {

// Admin Invoice Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {

    // Invoice CRUD Routes
    Route::resource('invoices', AdminInvoiceController::class);

    // Additional Invoice Actions
    Route::post('invoices/{invoice}/send', [AdminInvoiceController::class, 'send'])
         ->name('invoices.send');

    Route::post('invoices/{invoice}/mark-paid', [AdminInvoiceController::class, 'markAsPaid'])
         ->name('invoices.mark-paid');

    Route::get('invoices/{invoice}/download', [AdminInvoiceController::class, 'download'])
         ->name('invoices.download');

    Route::post('invoices/{invoice}/duplicate', [AdminInvoiceController::class, 'duplicate'])
         ->name('invoices.duplicate');

    Route::post('invoices/bulk-action', [AdminInvoiceController::class, 'bulkAction'])
         ->name('invoices.bulk-action');

    Route::get('payments/{payment}/receipt', [AdminInvoiceController::class, 'downloadReceipt'])
         ->name('invoices.download-receipt');

    // Invoice Reports Routes
    Route::prefix('reports/invoices')->name('reports.invoices.')->group(function () {
        Route::get('/', [InvoiceReportController::class, 'index'])
             ->name('index');

        Route::get('/summary', [InvoiceReportController::class, 'summaryReport'])
             ->name('summary');

        Route::get('/paid', [InvoiceReportController::class, 'paidReport'])
             ->name('paid');

        Route::get('/outstanding', [InvoiceReportController::class, 'outstandingReport'])
             ->name('outstanding');

        Route::get('/payments', [InvoiceReportController::class, 'paymentReport'])
             ->name('payments');

        Route::get('/revenue', [InvoiceReportController::class, 'revenueReport'])
             ->name('revenue');
    });
});

// Agency Invoice Routes
Route::prefix('agency')->middleware(['auth', 'agency'])->name('agency.')->group(function () {

    // Invoice Dashboard
    Route::get('dashboard/invoices', [AgencyInvoiceController::class, 'dashboard'])
         ->name('invoices.dashboard');

    // Invoice Management
    Route::get('invoices', [AgencyInvoiceController::class, 'index'])
         ->name('invoices.index');

    Route::get('invoices/{invoice}', [AgencyInvoiceController::class, 'show'])
         ->name('invoices.show')
         ->middleware('invoice.access');

    Route::get('invoices/{invoice}/download', [AgencyInvoiceController::class, 'download'])
         ->name('invoices.download')
         ->middleware('invoice.access');

    // Payment Routes
    Route::get('invoices/{invoice}/payment', [AgencyInvoiceController::class, 'payment'])
         ->name('invoices.payment')
         ->middleware('invoice.access');

    Route::post('invoices/{invoice}/pay', [PaymentController::class, 'process'])
         ->name('invoices.pay')
         ->middleware('invoice.access');

    // Payment Intent/Order Creation for different gateways
    Route::post('invoices/{invoice}/create-stripe-intent', [PaymentController::class, 'createStripeIntent'])
         ->name('invoices.create-stripe-intent')
         ->middleware('invoice.access');


    // Payment History
    Route::get('payment-history', [AgencyInvoiceController::class, 'paymentHistory'])
         ->name('invoices.payment-history');

    Route::get('payments/{payment}/receipt', [AgencyInvoiceController::class, 'downloadReceipt'])
         ->name('invoices.download-receipt')
         ->middleware('payment.access');

    Route::get('payments/{payment}/status', [PaymentController::class, 'status'])
         ->name('payments.status')
         ->middleware('payment.access');

    // Invoice Reports Routes for Agency
    Route::prefix('reports/invoices')->name('reports.invoices.')->group(function () {
        Route::get('/', [AgencyInvoiceReportController::class, 'index'])
             ->name('index');

        Route::get('/summary', [AgencyInvoiceReportController::class, 'summaryReport'])
             ->name('summary');

        Route::get('/paid', [AgencyInvoiceReportController::class, 'paidReport'])
             ->name('paid');

        Route::get('/outstanding', [AgencyInvoiceReportController::class, 'outstandingReport'])
             ->name('outstanding');

        Route::get('/payments', [AgencyInvoiceReportController::class, 'paymentReport'])
             ->name('payments');

        Route::get('/revenue', [AgencyInvoiceReportController::class, 'revenueReport'])
             ->name('revenue');
    });
});

// Webhook Routes (no authentication required)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('payments/{provider}', [PaymentController::class, 'webhook'])
         ->name('payments')
         ->where('provider', 'stripe|paypal');
});

// API Routes for AJAX calls
Route::prefix('api')->middleware(['auth'])->name('api.')->group(function () {

    // Admin API Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('invoices/{invoice}/preview', function ($invoice) {
            // Return invoice preview data for modal
            $invoice = \App\Model\Invoice::with(['agency', 'items', 'payments'])->findOrFail($invoice);
            return response()->json($invoice);
        })->name('invoices.preview');

        Route::get('agencies/search', function (\Illuminate\Http\Request $request) {
            // Search agencies for invoice creation
            $agencies = \App\Agency::where('delete_flag', 'N')
            ->when($request->q, function ($query, $search) {
                $query->where('agency_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'agency_name as name', 'email']);

            return response()->json($agencies);
        })->name('agencies.search');
    });

    // Agency API Routes
    Route::middleware('agency')->prefix('agency')->name('agency.')->group(function () {
        Route::get('invoices/stats', function () {
            // Return dashboard statistics
            $agencyId = auth()->user()->agency_fk;
            return response()->json([
                'total_invoices' => \App\Model\Invoice::forAgency($agencyId)->count(),
                'pending_invoices' => \App\Model\Invoice::forAgency($agencyId)->whereIn('status', ['sent', 'overdue'])->count(),
                'paid_invoices' => \App\Model\Invoice::forAgency($agencyId)->where('status', 'paid')->count(),
                'overdue_invoices' => \App\Model\Invoice::forAgency($agencyId)->overdue()->count(),
                'total_outstanding' => \App\Model\Invoice::forAgency($agencyId)->whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
                'total_paid_ytd' => \App\Model\Invoice::forAgency($agencyId)
                                         ->where('status', 'paid')
                                         ->whereYear('paid_at', now()->year)
                                         ->sum('total_amount'),
            ]);
        })->name('invoices.stats');
    });
});

}); // End of account prefix group