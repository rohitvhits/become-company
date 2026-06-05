<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PendingMedicalsController;

Route::group(['middleware' => ['XSS']], function () {

    Route::group(['middleware' => 'auth'], function () {

        Route::controller(PendingMedicalsController::class)
            ->prefix('visiting-aid/pending-medicals')
            ->name('visiting-aid/pending-medicals.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('data/list', 'getData')->name('getData');
                Route::post('sync-medical', 'syncMedicalWithDetails')->name('syncMedicalWithDetails');
                Route::get('export-csv', 'exportCsv')->name('exportCsv');
            });

    });

});