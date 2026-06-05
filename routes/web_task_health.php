<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskHealthVisitController;
use App\Http\Controllers\SupervisionController;

Route::group(['middleware' => ['XSS']], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::controller(TaskHealthVisitController::class)
            ->prefix('task-health')
            ->name('task-health.')
            ->group(function () {
                Route::get('visit', 'index');
				Route::get('visit-ajax-list', 'ajaxList');
				Route::get('visit-agencies', 'getAgencies');
				Route::get('visit-detail/{taskId}', 'visitDetail');
				Route::get('visit-detail-json/{taskId}', 'visitDetailJson');
				Route::get('visit-detail-json-poc/{taskId}', 'visitDetailJsonWithPoc');
				Route::get('visit-check-master/{taskId}', 'checkExistingMasterRecord');
				Route::post('visit-create-master/{taskId}', 'createMasterFromVisit');
				Route::post('visit-create', 'createVisit');
				Route::post('visit-edit/{taskId}', 'editVisit');
				Route::delete('visit-cancel/{taskId}', 'cancelVisit');
				Route::get('visit-export-csv', 'exportCsv');
				Route::post('visit-doc-approve/{taskId}/{scheduledDocId}', 'approveDocument');
				Route::post('visit-doc-open-changes/{taskId}/{scheduledDocId}', 'openDocumentForChanges');
        });

		// ── Critical Alerts Module ──

		Route::controller(\App\Http\Controllers\TaskHealthCriticalAlertController::class)
            ->prefix('task-health')
            ->name('task-health.')
            ->group(function () {
                Route::get('critical-alerts', 'index')->name('task-health.critical-alerts.index');
				Route::get('critical-alerts-ajax-list', 'ajaxList')->name('task-health.critical-alerts.ajax-list');
				Route::post('critical-alerts/{id}/resolve', 'resolve')->name('task-health.critical-alerts.resolve');
				Route::get('critical-alerts-export-csv', 'exportCsv')->name('task-health.critical-alerts.export');
        });

		Route::resource('task-health-log', \App\Http\Controllers\TaskHealthLogController::class);
		Route::get('/task-health-log-ajax-list', [\App\Http\Controllers\TaskHealthLogController::class, 'ajaxList']);
		Route::get('/get-task-health-log-by-id', [\App\Http\Controllers\TaskHealthLogController::class, 'taskHealthLogById']);

		Route::get('task-health-cron-log', [\App\Http\Controllers\TaskHealthCronLogController::class, 'index']);
		Route::get('task-health-cron-log-ajax-list', [\App\Http\Controllers\TaskHealthCronLogController::class, 'ajaxList']);
		Route::get('get-task-health-cron-log-by-id', [\App\Http\Controllers\TaskHealthCronLogController::class, 'taskHealthCronLogById']);

		Route::get('patient-task-health-visits', [\App\Http\Controllers\TaskHealthMasterController::class,'patientPageVisits']);
		Route::get('patient-task-health-critical-alerts', [\App\Http\Controllers\TaskHealthMasterController::class,'patientCriticalAlerts']);
		Route::get('task-health/search-patients', [\App\Http\Controllers\TaskHealthMasterController::class,'searchPatients']);
		Route::post('task-health/link-patient', [\App\Http\Controllers\TaskHealthMasterController::class,'linkPatient']);
		Route::post('task-health/unlink-patient', [\App\Http\Controllers\TaskHealthMasterController::class,'unlinkPatient']);
		Route::get('task-health/{id}/detail', [\App\Http\Controllers\TaskHealthMasterController::class,'detail']);
		Route::get('task-health/{id}/patient-visits-ajax', [\App\Http\Controllers\TaskHealthMasterController::class,'patientVisitsAjax']);
		Route::get('task-health/by-task/{taskId}/hha-patient-preview', [\App\Http\Controllers\TaskHealthMasterController::class,'hhaPatientPreviewByTask']);
		Route::post('task-health/by-task/{taskId}/upload-doc-to-hha', [\App\Http\Controllers\TaskHealthMasterController::class,'uploadDocByTask']);
		Route::resource('task-health', \App\Http\Controllers\TaskHealthMasterController::class);
		Route::get('/task-health-ajax-list', [\App\Http\Controllers\TaskHealthMasterController::class, 'ajaxList']);
		Route::get('/get-task-health-master-by-id', [\App\Http\Controllers\TaskHealthMasterController::class, 'taskHealthMasterById']);
		Route::get('get-task-health-services', [\App\Http\Controllers\TaskHealthMasterController::class,'getTaskHealthService']);
		Route::post('send-task-health-document', [\App\Http\Controllers\TaskHealthMasterController::class,'sendToTaskHealth']);
		Route::post('status-change-task-health', [\App\Http\Controllers\AgencyController::class,'statusChangeTaskHealth']);
		Route::get('task-health-revert-search', [\App\Http\Controllers\TaskHealthMasterController::class,'revertPatientSearch']);
		Route::post('task-health-revert-patient', [\App\Http\Controllers\TaskHealthMasterController::class,'revertPatient']);
		Route::post('task-health-flag-update', [\App\Http\Controllers\TaskHealthMasterController::class,'updateFlag']);
		Route::post('task-health-flags-save',  [\App\Http\Controllers\TaskHealthMasterController::class,'saveFlags']);
		Route::get('task-health-export-csv',      [\App\Http\Controllers\TaskHealthMasterController::class,'exportCsv']);
		Route::get('task-health-flags-by-task-id', [\App\Http\Controllers\TaskHealthMasterController::class,'getFlagByTaskId']);
		Route::post('task-health-sync-critical-alerts', [\App\Http\Controllers\TaskHealthMasterController::class,'syncCriticalAlerts']);

		// ── Agency Settings Module ──
		Route::get('agency-task-health-setting', [\App\Http\Controllers\TaskHealthMasterController::class, 'agencySettingsIndex'])->name('agency-task-health-setting');
		Route::get('agency-task-health-setting-ajax-list', [\App\Http\Controllers\TaskHealthMasterController::class, 'agencySettingsAjaxList'])->name('agency-task-health-setting.ajax-list');
		Route::post('agency-task-health-setting-toggle-update', [\App\Http\Controllers\TaskHealthMasterController::class, 'agencySettingsToggleUpdate'])->name('agency-task-health-setting.toggle-update');
		Route::post('agency-task-health-setting-poc-notes', [\App\Http\Controllers\TaskHealthMasterController::class, 'savePocGroupNotes'])->name('agency-task-health-setting.poc-notes');
		Route::post('task-health-convert', [\App\Http\Controllers\TaskHealthMasterController::class,'convertTaskHealth']);
    });
});
// ── Supervision Module ──
Route::get('supervision', [SupervisionController::class, 'index']);
Route::post('task-health/webhook/critical-alert', [\App\Http\Controllers\TaskHealthMasterController::class, 'criticalAlert'])->middleware('api.logger');
