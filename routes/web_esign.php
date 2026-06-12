<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommonEsignController;
use App\Http\Controllers\TempleteController;
use App\Http\Controllers\EsignReportController;
use App\Http\Controllers\EsignImportController;
use App\Http\Controllers\DocumentWorkflowController;
use App\Http\Controllers\EsignPatientDashboardController;
Route::group(['middleware' => ['XSS']], function () {

    Route::group(['middleware' => 'auth'], function () {

        Route::controller(CommonEsignController::class)
            ->prefix('esign')
            ->name('esign.')
            ->group(function () {

                Route::post('template/docusign-sent', 'DocumentSend');
                Route::get('patient-wise-esign-list', 'esignPatientList');
                Route::get('allowcate-signer-request', 'singerAllowcateRequest');
                Route::get('patient-docusign-delete/{id}', 'caregiverDelete');
                Route::post('common/updateResponse', 'saveCommonResponse');
                Route::post('patient-send-sms-esign', 'caregiverSendSMS');
                Route::post('bulk-send-sms-esign', 'bulkCaregiverSendSMS');
                Route::get('preview-pdf-response', 'previewPdfPatient');
                Route::post('pdf/update-status', 'pdfUpdateStatus');
                Route::post('pdf/undo-status', 'pdfUndoStatus');
                Route::get('get-log-details', 'getLogDetails');
                Route::get('get-document-sent-report-data', 'getDocumentSentReportData');
                Route::get('load-doctor-list', 'loadDoctorList');
                Route::post('template/docusign-sent-new', 'DocumentSendNew');
                Route::get('preview-pdf-response-update', 'previewPdfupdate');
                Route::get('esign-history', 'showSMSEsignHistory');
                Route::get('delete-patient-wise-esign-list', 'deleteEsignPatientList');
                Route::post('/docusign/update-form', 'DocusignFormSubmitUpdate');
                Route::get('aws-pdf-generate-edit', 'getPdfForAwsEdit');
                Route::post('docusign/esign-signature-write-document', 'EsignSignatureWriteDocument');
                Route::post('docusign/upload-signature-write-document', 'uploadSignatureWriteDocument');
                Route::post('/docusign/submit-form-write-document', 'DocusignFormSubmitWriteDocoument');
                Route::post('/update-document-patient', 'updateDocumentPatient');
                Route::post('undo-esign-data', 'undoEsignData');
                Route::post('generate-patient-esign-link', 'generatePatientEsignLink');
                Route::post('update-signature-name', 'updateSignatureName');
                Route::post('get-qr-code-link', 'generateQrCodeLink');
                Route::get('/view-esign-log', 'viewEsignLog');
                Route::get('/view-esign-response-log', 'viewEsignResponseLog');
                Route::get('/edit-sign/{docId}', 'editSign');
                Route::post('docusign/record-wise-submit-form', 'updateEsignFormSubmit');
                Route::post('/all-response-file-submit', 'allUpdateResponse');
                Route::get('esign-pdf-details', 'uploadDocumentRegenerate');
                Route::get('show-pdf', 'getReturnFile');
                Route::post('esign-move-document', 'esignMoveDocumentStore');
                Route::get('write-show-pdf', 'getWriteReturnFile');
                Route::get('next-signer/{id}', 'redirectionNextSigner');
                Route::get('docusign/viewNew/{id}', 'viewDocusignNew');

            });

        // E-Sign Dashboard routes
        Route::controller(EsignPatientDashboardController::class)
            ->prefix('esign/esign-patient-dashboard')
            ->name('esign/esign-patient-dashboard.')
            ->group(function () {

                Route::get('/', 'index');
                Route::get('esign-dashboard-ajax-list', 'ajaxList');
                Route::post('default-assign-esign-user/store', 'storeDefaultAssignUser');
                Route::get('default-assign-esign-user/list', 'defaultAssignUserList');
                Route::post('default-assign-esign-user/delete', 'deleteDefaultAssignUser');

            });

        // Document Workflow routes
        Route::controller(TempleteController::class)
            ->prefix('esign')
            ->name('esign.')
            ->group(function () {

                Route::get('/load-esign-template', 'loadEsignTemplate');
                Route::get('/template/allowcate-signer', 'AllocateSigner');
                Route::get('/write-document', 'viewWriteDocument');
                Route::post('write_document_send', 'writeDocumentSend');
                Route::get('template/getpdfbyDocumentWriteid', 'getpdfbyDocumentWriteid');
                Route::post('regenerate-write-document', 'regenerateWriteDocument');
                Route::post('eraser-apply-to-pdf', 'eraserApplyToPdf');
                Route::get('template/fetch_eraser_pdf', 'fetchEraserPdf');
                Route::post('template/write-document-upload', 'writeDocumentUpload');

            });

        Route::controller(EsignImportController::class)
            ->prefix('esign')
            ->name('esign.')
            ->group(function () {
                Route::get('/esign-import', 'index');
                Route::post('/esign-import-store', 'store');
                Route::post('/esign-import-history', 'importHistory');
                Route::get('/esign-import-download/{id}', 'downloadFile');
                Route::get('/esign-import-sample-csv', 'sampleCSV');
                Route::post('/esign-import-mapping-data', 'mappingData');
                Route::post('/esign-import-confirm', 'confirmImport');
                Route::post('/esign-import-delete', 'deleteImport');
                Route::get('/esign-import-details/{id}', 'viewDetails');
                Route::post('/esign-import-details-ajax', 'detailsAjax');
                Route::post('/esign-import-error-detail', 'getErrorDetail');
                Route::get('/esign-import-templates', 'getTemplatesByType');
                Route::get('manual-sync-import',  'syncEsignImport');
            });
    });

    /********Without Auth */
    Route::controller(DocumentWorkflowController::class)
        ->prefix('esign/document-workflow')
        ->name('esign/document-workflow.')
        ->group(function () {

            Route::post('streamlined', 'markAsSignatureRequired')->name('signature-required');
            Route::get('streamlined/{id}', 'temparyData');
            Route::post('mark-approved', 'markAsApproved')->name('approved');
            Route::post('handle-signed-workflow', 'handleSignedWorkflow')->name('signed-workflow');

        });

    Route::controller(CommonEsignController::class)
        ->prefix('esign')
        ->name('esign.')
        ->group(function () {
            Route::get('docusign/view/{id}', 'ViewDocusign');
            Route::post('docusign/esign-signature', 'EsignSignature');
            Route::post('docusign/upload-signature', 'uploadSignature');
            Route::post('docusign/submit-form', 'docusignFormSubmit');
            Route::post('get-patient-signatures', 'getPatientSignatures');
            Route::post('delete-signature', 'deleteSignature');
            Route::get('thankyou-esign', 'thankyou');
            Route::get('nye/{id}', 'emailSignShow');
            Route::get('regenerate-pdf', 'pdfRegenerate');
            Route::get('aws-pdf-generate', 'getPdfForAws');

        });

    Route::controller(TempleteController::class)
        ->prefix('esign')
        ->name('esign.')
        ->group(function () {

            Route::get('template/esign-lookup-fields-new/{id}', 'getResponseCanvasNew');
            Route::get('template/esign-lookup-fields-new1/{id}', 'getResponseCanvasNew1');
            Route::post('upload_documentwebNew', 'upload_documentwebNew');
            Route::get('/template-signer-notification', 'getSignerNotification');
		    Route::post('/template-signer-notification-save', 'saveSignerNotification');        
	});

    Route::prefix('esign/esign-report')
        ->name('esign/esign-report.')
        ->group(function () {
            Route::resource('/', EsignReportController::class);
            Route::get('/ajax-list', [EsignReportController::class, 'ajaxList']);
            Route::get('/esign-report-export', [EsignReportController::class, 'reportExport']);
            Route::post('/esign-bulk-send-sms', [EsignReportController::class, 'bulkSendSMS']);
            Route::get('/search-nybest-all-user', [EsignReportController::class, 'searchNyBestAllUser']);
        });
});
