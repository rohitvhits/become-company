<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HHAMdoController;
use App\Http\Controllers\HHAOtherComplianceController;
use App\Http\Controllers\HhaMdoOrderReportLogReportController;
use App\Http\Controllers\HHAAppointmentController;
use App\Http\Controllers\HHACaregiversController;
use App\Http\Controllers\HHAPatientController;
use App\Http\Controllers\HHADueMedicalController;
use App\Http\Controllers\HHAMedicalsController;
use App\Http\Controllers\PocAutomateController;
use App\Http\Controllers\PocMappingController;


Route::group(['middleware' => 'auth'], function () {
    Route::controller(HHAMdoController::class)
        ->prefix('hha/hha-mdo')
        ->name('hha/hha-mdo.')
        ->group(function () {
            Route::get('mdo-document-list', 'mdoDocumentList');
            Route::get('download-md-order-document', 'downloadMDOrderDocument');
            Route::post('send-md-order-document','sendMDOrderDocument');
            Route::get('hha-mdo-patient-list', 'mdoPatientList');
            Route::get('ajax-hha-mdo-patient-list', 'ajaxList');
            Route::post('save-hha-mdo-patient', 'saveHHAMDOPatient');
        });

    Route::controller(HHAOtherComplianceController::class)
        ->prefix('hha/hha-other-compliance')
        ->name('hha/hha-other-compliance.')
        ->group(function () {
            Route::get('hha-complience-medical-results', 'getCompienceMedicalResults');
            Route::post('update-complience-document', 'updateHHAcomplienceDocument');
        });

    Route::controller(HhaMdoOrderReportLogReportController::class)
        ->prefix('hha/hha-mdo/mdo-report-log')
        ->name('hha/hha-mdo/mdo-report-log.')
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/ajax-list', 'ajaxList');
            Route::get('/export-csv', 'exportCsv');
            Route::get('/download/{id}', 'download');
            Route::get('/view-document-log', 'viewDocumentLog');
        });

    Route::controller(HHAAppointmentController::class)
        ->prefix('hha/hha-medical/')
        ->name('hha/hha-medical.')
        ->group(function () {
            Route::post('/refresh-sync', 'caregiverWiseSYNCMedical');
            Route::get('/', 'index');
            Route::get('/hha-appointment-ajax','hhaAppoitmentAjax');
		    Route::get('/hha-appointment-export','exportCsv');
            Route::post('/add-appointment-patient','addAppoinmentPatient');
        });

    Route::controller(HHACaregiversController::class)
        ->prefix('hha/hha-caregiver')
        ->name('hha/hha-caregiver.')
        ->group(function () {
            Route::get('/demographic-detail/{tabName}', 'getCaregiverTabDetails');
            Route::get('/calendar-visits', 'getCaregiverCalendarVisits');
        });

    Route::controller(HHAOtherComplianceController::class)
        ->prefix('hha/hha-other-compliances')
        ->name('hha/hha-other-compliances.')
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/hha-other-compliance-ajax', 'hhaAppoitmentAjax');
            Route::post('/add-hha-other-compliance', 'addOtherHHACompliance');
            Route::get('/get-hha-other-compliance', 'getOtherCompliancebyCaregiverId');
            Route::get('/hha-other-compliance-export', 'exportCsv');
            Route::get('/hha-other-complience','getHHAOtherComplienceData');
            Route::get('/caregiver-modal-medical-view-result','getMedicalResultByCaregiverId');
            Route::post('/update-other-medical-data-by-id','updateOtherMedicalData');
            Route::get('all-other-compliance-list', 'allOtherComplianceList');
            Route::post('/save-other-medical-data','saveOtherMedicalData');
        });

    Route::controller(HHAPatientController::class)
        ->prefix('hha/hha-patient')
        ->name('hha/hha-patient.')
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/hha-patient-ajax', 'ajaxList');
            Route::post('/add-hha-appointment-patient', 'addAppoinmentPatient');
            Route::get('/check-existing-patient-record', 'checkExistingPatientRecord');
            Route::post('/link-hha-patient-appointment', 'linkForHHAPatient');
            Route::get('hha-patient-export-csv','exportCsv');
            Route::get('/hha-update-patient-ajax','updateHomePhoneDetails');
            Route::get('hha-demographic-detail/{tabName}','getPatientTabDetails');
            Route::get('/calendar-visits', 'getPatientCalendarVisits');
            Route::get('/configuration-poc-task/{id}', 'configurationPOCTask');
            Route::get('/document-poc-type', 'syncDocumentType');
        });

    Route::controller(HHADueMedicalController::class)
        ->prefix('hha/due-medical-report')
        ->name('hha/due-medical-report.')
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/due-medical-report-ajax', 'ajaxList');
            Route::get('due-medical-report-export-csv','exportCsv');
            Route::post('/add-appointment-patient','addAppoinmentPatient');
        });

        Route::controller(HHAMedicalsController::class)
        ->prefix('hha/hha-caregiver-medicals')
        ->name('hha/hha-caregiver-medicals.')
        ->group(function () {
            Route::get('sync-agency-wise-medical', 'syncAgencyWiseMedical');
            Route::get('/', 'index');
            Route::get('/ajax-list', 'ajaxList')->name('ajax-list');
            Route::get('/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::get('/export-csv', 'exportCSV')->name('export-csv');
            Route::get('/get-offices-by-agency', 'getOfficesByAgency')->name('get-offices-by-agency');
            Route::get('/get-medicals-by-agency-office', 'getMedicalsByAgencyOffice');
            Route::post('/sync-medical', 'syncMedical');
        });

        Route::controller(PocAutomateController::class)
        ->prefix('hha/hha-patient')
        ->name('hha/hha-patient.')
        ->group(function () {
            Route::get('/map-task','index');
            Route::get('sync-auto-visit','syncAutoPopulateVisit');
            Route::post('show-hha-poc-document','showPOCFile');
            Route::post('send-hha-poc','sendHHAPoc');
        });

        // ── HHA Send Log Module ──
        Route::controller(\App\Http\Controllers\HHASendLogController::class)
            ->prefix('hha/hha-send-log')
            ->name('hha/hha-send-log.')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/ajax-list', 'ajaxList');
                Route::get('/view-detail', 'viewDetail');
            });

        // ── POC Mapping Module ──
        Route::controller(PocMappingController::class)
            ->prefix('hha/poc-mapping')
            ->name('hha/poc-mapping.')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/tasks-with-mappings', 'getTasksWithMappings');
                Route::post('/save-all', 'saveAll');
                Route::get('/sync-tasks', 'syncTasks');
            });


});
