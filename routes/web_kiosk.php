<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KioskAdminController;

Route::group(['middleware' => ['XSS']], function () {
    Route::group(['middleware' => 'auth'], function () {
        // Admin Routes
        Route::prefix('kiosk/admin')->group(function () {
            // Authenticated routes
            Route::middleware('auth')->group(function () {
                Route::get('/', [KioskAdminController::class, 'dashboard'])->name('admin.dashboard');
                Route::get('/dashboard', [KioskAdminController::class, 'dashboard'])->name('admin.dashboard.index');
                Route::get('/appointments', [KioskAdminController::class, 'appointments'])->name('admin.appointments');
                Route::get('/appointments-ajax-list', [KioskAdminController::class, 'appointmentsAjaxList'])->name('admin.appointments.ajax-list');
                Route::get('/appointments/{id}', [KioskAdminController::class, 'showAppointment'])->name('admin.appointments.show');
            });
        });
    });
});
