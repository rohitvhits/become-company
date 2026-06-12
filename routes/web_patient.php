<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PatientImportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\CallDetailController;

Route::group(['middleware' => ['XSS']], function () {
    Route::get('patient/{patient}/call-details', [CallDetailController::class, 'index'])->name('patient.call-details');
    Route::get('patient/{patient}/call-details/list', [CallDetailController::class, 'ajaxTable'])->name('patient.call-details.list');
    Route::get('patient/{patient}/call-details/ajax', [CallDetailController::class, 'ajaxList'])->name('patient.call-details.ajax');
    Route::get('patient/{patient}/call-details/messages', [CallDetailController::class, 'ajaxMessages'])->name('patient.call-details.messages');
    Route::get('call-details', [CallDetailController::class, 'index'])->name('call-details.index');
    Route::get('call-details/list', [CallDetailController::class, 'ajaxTable'])->name('call-details.list');
    Route::get('call-details/ajax', [CallDetailController::class, 'ajaxList'])->name('call-details.ajax');
    Route::get('call-details/recording', [CallDetailController::class, 'recordingUrl'])->name('call-details.recording');
    Route::post('patient/help-me-write', [\App\Http\Controllers\HelpMeWriteController::class, 'handle']);
	Route::post('patient/help-me-write/refine', [\App\Http\Controllers\HelpMeWriteController::class, 'refine']);

    Route::group(['middleware' => 'exmedSuperAdminAccess'], function () {
        Route::controller(PatientImportController::class)
            ->prefix('patient')
            ->name('patient.')
            ->group(function () {
                Route::get('import',  'index');
                Route::post('importdata',  'patientCsv');
                Route::post('patient-import',  'patientImports');
                Route::post('import-files-data',  'getImportFilesData');
                Route::get('view-import-data/{id}',  'viewImportDataByImportID');
                Route::post('view-import-data-ajax/{id}',  'getViewImportDataAjax');
                Route::get('view-import-data-show/{record_id}',  'getViewImportRecordDetails');
                Route::get('import-file-download/{record_id}',  'downloadFile');
                Route::get('sync-import',  'syncImport');
                Route::delete('delete-import/{id}',  'deleteImportHistory');
                Route::post('approve-import/{id}',  'approveImportHistory');
                Route::post('import-logs/{id}',  'getImportLogs');
                Route::post('/get-filtered-nurses', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'getFilteredNursesByTimeFrame']);
	            Route::post('/get-available-time-frames', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'getAvailableTimeFrames']);
        });

        Route::controller(\App\Http\Controllers\MergeAppointmentController::class)
            ->prefix('patient')
            ->name('patient.')
            ->group(function () {
                Route::get('sync-merge-appointment','syncMergeAppointments');
            });

        Route::controller(PatientController::class)
            ->prefix('patient')
            ->name('patient.')
            ->group(function () {
                Route::post('update-agency-user-rep',  'updateAgencyUserRep');
            });

    });
});
