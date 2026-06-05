<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\APIController;
use App\Http\Controllers\API\v2\APIController as APIV2Controller;
use App\Http\Controllers\API\v2\HubController;
use App\Http\Controllers\API\v2\APICoordinationController;
use App\Http\Controllers\API\v2\TaskHealthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'v1'], function () {

    Route::get('/patient-list', [APIController::class, 'patientlist']);
    Route::get('/patient-detail', [APIController::class, 'patientDetail']);
    Route::get('/language-list', [APIController::class, 'languageList']);
    Route::get('/discipline-list', [APIController::class, 'disciplineList']);
    Route::get('/payment-type-list', [APIController::class, 'paymentTypeList']);
    Route::post('/add-appointment', [APIController::class, 'addPatient']);
    Route::get('/document-list', [APIController::class, 'getDocumentList']);
    Route::get('/download-document', [APIController::class, 'downloadDocument']);
    Route::get('/service-list', [APIController::class, 'serviceList']);
    Route::get('/get-due-contact-document', [APIController::class, 'getDueContactDocument']);
    Route::get('/help', [APIController::class, 'help']);

});

Route::group(['prefix' => 'lead'], function () {
    Route::get('/patient-list', [APIV2Controller::class, 'patientlist']);
    Route::get('/patient-detail', [APIV2Controller::class, 'patientDetail']);

    Route::get('/language-list', [APIV2Controller::class, 'languageList']);
    Route::get('/discipline-list', [APIV2Controller::class, 'disciplineList']);
    Route::get('/payment-type-list', [APIV2Controller::class, 'paymentTypeList']);
    Route::post('/add-appointment', [APIV2Controller::class, 'addPatient']);
    Route::get('/document-list', [APIV2Controller::class, 'getDocumentList']);
    Route::get('/download-document', [APIV2Controller::class, 'downloadDocument']);
    Route::get('/service-list', [APIV2Controller::class, 'serviceList']);
    Route::get('/get-due-contact-document', [APIV2Controller::class, 'getDueContactDocument']);
    Route::get('/document-by-service', [APIV2Controller::class, 'documentsServiceList']);
    Route::post('/save-document', [APIV2Controller::class, 'saveDocument']);
    Route::post('/appointment-cancellation', [APIV2Controller::class, 'cancellationRequest']);
    Route::post('/emmacare_webhook', [APIV2Controller::class, 'emmacareWebhook']);
    Route::get('/insurance-list', [APIV2Controller::class, 'insuranceList']);
    Route::post('/update-service-date', [APIV2Controller::class, 'updateServiceStartDate']);
    Route::post('/save-lead-appointment', [APIV2Controller::class, 'addPatientNew']);

    /*  Task Health */
    Route::get('/get-all-agency-list', [TaskHealthController::class, 'getAllAgencyList']);
    Route::post('/save-task-health-appointment', [TaskHealthController::class, 'saveTaskHealthAppointment'])->middleware('api.logger');

    /* Start Hub Record */
    Route::get('/hub-record-list', [HubController::class, 'hubList']);
    Route::get('/hub-record-details', [HubController::class, 'hubRecordDetail']);
    Route::get('/hub-document-list', [HubController::class, 'hubDocList']);
    Route::get('/hub-notes-list', [HubController::class, 'hubNotesList']);
    Route::get('/hub-text-messages-list', [HubController::class, 'hubTextMessagesList']);
    Route::post('/hub-record-status-update', [HubController::class, 'statusUpdate']);
    Route::get('/hub-record-download-document', [HubController::class, 'hubDownloadDocument']);
    Route::get('/get-subject', [HubController::class, 'hubSubjectAPI']);
	Route::get('/patient-list-by-agency', [APIV2Controller::class, 'allPatientListByAgency']);
    Route::get('/all-document-list-by-agency', [APIV2Controller::class, 'getAllDocumentListbyAgency']);
    /* End Hub Record */
});

Route::group(['prefix' => 'call-request'], function () {

    Route::controller(\App\Http\Controllers\API\v3\CallRequestController::class)->group(function () {
        Route::get('/get-available-time-slots', 'getTimeSlotsByLanguageAndDate');
        Route::post('/add-call-appointment', 'AddCallAppointment');
        Route::get('/make-call', 'makeCall');
    });

});

Route::group(['prefix' => 'crm'], function () {
    Route::post('/save', [APICoordinationController::class, 'saveRecord']);
    /* End Hub Record */
});