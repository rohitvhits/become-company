<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RobortController;


Route::group(['middleware' => 'auth'], function () {
    Route::controller(RobortController::class)
        ->prefix('remote')
        ->name('remote.')
        ->group(function () {
            Route::get('get-remote-details', 'getRemoteDetails');
            Route::post('send-remote-details', 'sendDetailsForRemote');
            Route::post('upload-remote-document','uploadRemoteDocument');
            Route::get('get-remote-patient-care-plan', 'patientRemoteCarePlan');
            Route::get('get-remote-patient-activity-log', 'patientRemoteActivityLog');

            Route::get('remote-list', 'index');
            Route::get('robort-ajax-list', 'robortAjaxList');
            Route::post('add-appointment-robort', 'saveRobortAppointment');
            Route::get('load-hha-dicipline', 'loadHHADicipline');
            Route::get('robort-patient-orn-trn', 'patientOrnTrn');
            Route::get('robort-patient-reading', 'patientReadingList');
            Route::get('robort-patient-medication', 'patientMedicationList');
            Route::get('demographic-detail', 'getDemoraphicDetails');
            Route::get('search-emmacare-employee', 'searchEmmacareEmployee');
            Route::post('update-remote-id','updateRemoteId');
            Route::get('remote-emp-data','remoteEmpData');
            Route::post('patient-document-send','patientDocumentSend');
            
        });
});