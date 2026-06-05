<?php

use App\Http\Controllers\PaymentLogImportController;
use App\Http\Controllers\PaymentLogListingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment Log Module Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the Payment Log Module. These routes are loaded
| by the RouteServiceProvider within a group which contains the "web"
| middleware group.
|
*/

Route::prefix('account')->group(function () {

// Payment Log Import Routes
Route::prefix('payment-log-import')->middleware('auth')->group(function () {
    Route::get('/', [PaymentLogImportController::class, 'index'])->name('payment_log_import.index');
    Route::get('/download-sample', [PaymentLogImportController::class, 'downloadSample'])->name('payment_log_import.download_sample');
    Route::post('/upload', [PaymentLogImportController::class, 'uploadFile'])->name('payment_log_import.upload');
    Route::get('/mapping/{id}', [PaymentLogImportController::class, 'showMapping'])->name('payment_log_import.mapping');
    Route::post('/mapping/{id}', [PaymentLogImportController::class, 'processMapping'])->name('payment_log_import.process_mapping');
    Route::post('/confirm/{id}', [PaymentLogImportController::class, 'confirmImport'])->name('payment_log_import.confirm');
});

// Payment Log Listing Routes
Route::prefix('payment-log-listing')->middleware('auth')->group(function () {
    Route::get('/', [PaymentLogListingController::class, 'index'])->name('payment_log_listing.index');
    Route::get('/view/{id}', [PaymentLogListingController::class, 'view'])->name('payment_log_listing.view');
    Route::post('/verify/{id}', [PaymentLogListingController::class, 'verify'])->name('payment_log_listing.verify');
    Route::post('/bulk-verify', [PaymentLogListingController::class, 'bulkVerify'])->name('payment_log_listing.bulk_verify');
    Route::post('/bulk-generate-invoice', [PaymentLogListingController::class, 'bulkGenerateInvoice'])->name('payment_log_listing.bulk_generate_invoice');
    Route::post('/update-services/{id}', [PaymentLogListingController::class, 'updateServices'])->name('payment_log_listing.update_services');
    Route::post('/generate-bill/{id}', [PaymentLogListingController::class, 'generateBill'])->name('payment_log_listing.generate_bill');
    Route::delete('/delete/{id}', [PaymentLogListingController::class, 'delete'])->name('payment_log_listing.delete');
    Route::get('/export', [PaymentLogListingController::class, 'export'])->name('payment_log_listing.export');
});

}); // End of account prefix group
