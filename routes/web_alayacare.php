<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlayacareClientController;
use App\Http\Controllers\AlaycareEmpController;
use App\Http\Controllers\AlaycareDueSkillController;
use App\Http\Controllers\AlayacareCronLogController;
	
Route::group(['middleware' => 'auth'], function () {
    Route::controller(AlayacareClientController::class)
        ->prefix('alayacare/alayacare-client')
        ->name('alayacare/alayacare-client.')
        ->group(function () {
            
            Route::get('/client-list', 'getAlaycareClientList');
            Route::get('alaycare-client-export', 'alaycareClientExport');
            Route::get('alaycare-client-ajax-list', 'getAlaycareClientListAjax');
            Route::post('client-add-appointment', 'clientAddAppointment');
        });

    Route::controller(AlaycareEmpController::class)
        ->prefix('alayacare/alayacare-employee')
        ->name('alayacare/alayacare-employee.')
        ->group(function () {
            
            Route::get('/employee-list', 'getAlaycareEmpList');
            Route::get('alaycare-employee-export', 'alaycareEmployeeExport');
            Route::get('alaycare-employee-ajax-list', 'getAlaycareEmpListAjax');
            Route::post('employee-add-appointment', 'empAddAppointment');
            Route::post('/alayacare-document-upload-new','alaycareDocumentUploadNew');
            Route::get('/alaycare-employee-skill-list','getAlayacareSkillList');
            Route::get('/fetch-skill-details','fetchSkillDetails');
        });

    Route::controller(AlaycareDueSkillController::class)
        ->prefix('alayacare/alayacare-skill')
        ->name('alayacare/alayacare-skill.')
        ->group(function () {
            
            Route::get('/alayacare-due-skill-list', 'index');
            Route::get('due-skill-ajax-list', 'ajaxList');
            Route::get('due-skill-export-csv', 'exportCSV');
            Route::post('add-alayacare-patient-appointment', 'addAlayacarePatientAppointment');
        });

    // Alayacare Cron Log
    Route::get('alayacare/alayacare-cron-log', [AlayacareCronLogController::class, 'index']);
    Route::get('alayacare/alayacare-cron-log/list', [AlayacareCronLogController::class, 'getList']);
    Route::get('alayacare/alayacare-cron-log/view/{id}', [AlayacareCronLogController::class, 'view']);
});
