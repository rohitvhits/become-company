<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DoctorPaperWorkController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\PatientCalenderController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\InsuranceMasterController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\FieldMasterController;
use App\Http\Controllers\FormSetupController;
use App\Http\Controllers\ApproveStampController;
use App\Http\Controllers\RatingMasterController;
use App\Http\Controllers\FormGroupController;
use App\Http\Controllers\InvoiceUploadController;
use App\Http\Controllers\FormReportController;
use App\Http\Controllers\EsignReportController;
use App\Http\Controllers\EventMasterController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\DisableDateController;
use App\Http\Controllers\ApiCallLogReportController;
use App\Http\Controllers\GroupNotificationController;
use App\Http\Controllers\FeedbackFormReportController;
use App\Http\Controllers\RateCardController;
use App\Http\Controllers\TeleHealthServiceController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\HubAuthenticationController;
use App\Http\Controllers\VNSQuestionController;
use App\Http\Controllers\VNSProcedureController;
use App\Http\Controllers\VNSProcedureResultController;
use App\Http\Controllers\VNSSocialHistoryController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\RedirectionEsignController;
use App\Http\Controllers\MergeAppointmentController;
use App\Http\Controllers\AppTokenController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LeadCoordinationReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//api v1 log-viewer

Route::get('/api/log-viewer/', function () {
    return redirect('/log-viewer');
});

// Health Check Endpoint - No middleware required
Route::get('/health-check', [HealthCheckController::class, 'index']);

Route::get('/clear', function () {
	Artisan::call('migrate', [
		'--path' => 'database/migrations/2024_10_03_024044_create_jobs_table.php',
		'--no-interaction' => true,
	]);
});
Route::get('/check-php-info', function () {
	phpinfo();
});
// Password form submission route
Route::post('/log-viewer-login', function (\Illuminate\Http\Request $request) {

	if ($request->input('password') === "MySecret123") {
		session(['logviewer_authenticated' => true]);
		return redirect('/log-viewer');
	}

	return back()->withErrors(['password' => 'Invalid password']);
})->name('logviewer.login');

Route::get('/', function () {

	return view('auth.login');
});
Route::get('077C79CE9E66E83AE35EBD91E7B7FD14.txt', function () {
	echo `124601E2D0F11398957F780B2033D956AD95936BE5B6B6E830B7E94941A0117C trust-provider.com cmcdtfqwiyxfj5`;
	die();
	return view('auth.login');
});
Route::get('.well-known/acme-challenge/077C79CE9E66E83AE35EBD91E7B7FD14', function () {
	echo `124601E2D0F11398957F780B2033D956AD95936BE5B6B6E830B7E94941A0117C trust-provider.com cmcdtfqwiyxfj5`;
	die();
	return view('auth.login');
});

Route::get('/ipchecker', [TestController::class, 'ipchecker']);
Route::get('.well-known/acme-challenge/DyJ_0Z9FP8o-654r25iU9YiiaB4eRcHwe-pdjtoBhr0', function () {
	echo `DyJ_0Z9FP8o-654r25iU9YiiaB4eRcHwe-pdjtoBhr0.1UzTG816WJp_pqVxuOPiaJXdPTiXCyX547SBYIXRt7w`;
	die();
	return view('auth.login');
});

Route::get('.well-known/pki-validation/1769FC43DD5DE1A985B8E9158A5C62F922.txt', function () {
	echo `07C1B42B9C647503ECA104211A5B29592DED24250F806E1CBD94AAACE118A58F
sectigo.com
770161849`;
	die();
	return view('auth.login');
});
Route::get('.well-known/pki-validation/CD5262E2428F3A27402946AF3D14F12D.txt', function () {
	echo `193211D94A3C5B94748C826FC1964282B60F20579558D87771B302F8D9192567
	comodoca.com
	b70af50a34b5e9a`;
	die();
	return view('auth.login');
});

Route::get('check-time-out', [\App\Http\Controllers\TestingPurposeController::class, 'checkTimeOut1']);

Route::prefix('auth')->group(function () {
    Auth::routes();
});

Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);

Route::group(['middleware' => ['XSS']], function () {
	Route::get('/login-xxyyyxyy', [\App\Http\Controllers\Auth\CustomeLoginController::class, 'otherAccess']);
	Route::get('sync-hha-caregiver', [\App\Http\Controllers\HHACaregiversController::class, 'getSyncCaregiverData']);
	Route::get('sync-hha-medical', [\App\Http\Controllers\HHACaregiversController::class, 'fetchAllUnsyncedMedical']);
	Route::get('term-condition', [\App\Http\Controllers\TermAndConditionController::class, 'index']);
	Route::get('privacy-policy', [\App\Http\Controllers\TermAndConditionController::class, 'privacyPolicy']);

	Route::get('check-documents', [\App\Http\Controllers\TestingPurposeController::class, 'testing']);
	Route::get('testing-hello123', [\App\Http\Controllers\TestingPurposeController::class, 'rolesCheckings']);
	Route::get('check-post-send-sms', [\App\Http\Controllers\TestingPurposeController::class, 'checkPostSendSMS']);
	Route::any('bandwidth-callback', [\App\Http\Controllers\OptInOutController::class, 'callback']);
	Route::any('bandwidth-test', [\App\Http\Controllers\OptInOutController::class, 'test']);

	Route::get('/invitation-accept/{id}', [\App\Http\Controllers\UserController::class, 'AcceptView']);
	Route::post('/acceptInvitation', [\App\Http\Controllers\UserController::class, 'AcceptInvivation']);
	Route::get('/active-account/{id}', [\App\Http\Controllers\UserController::class, 'activeView']);
	Route::get('/make-secure-password/{id}', [\App\Http\Controllers\UserController::class, 'makeSecurePassword']);
	Route::get('auth/verify-otp/{id}', [\App\Http\Controllers\UserController::class, 'verifyOtp']);
	Route::post('auth/verifyotp', [\App\Http\Controllers\UserController::class, 'checkOtp'])->name('verifyotp');
	Route::post('/check-passwords', [\App\Http\Controllers\UserController::class, 'checkPasswords']);

	Route::post('active-account', [\App\Http\Controllers\UserController::class, 'activeAccountUpdate']);
	Route::get('/link_expired', [\App\Http\Controllers\UserController::class, 'linkExpired']);
	Route::get('/check-cron-job', [\App\Http\Controllers\CronJobNewController::class, 'cronJobBookedStatusUpdateNoShow']);
	Route::get('/agency-wise-sync-caregiver', [\App\Http\Controllers\HHACaregiversController::class, 'agencyWiseSYNCCaregiver']);
	Route::get('/agency-wise-sync-with-caregiver', [\App\Http\Controllers\HHACaregiversController::class, 'agencyWiseSYNCCaregiverWIthAgency']);
	Route::get('/agency-wise-sync-caregiver-medical', [\App\Http\Controllers\HHACaregiversController::class, 'agencyWiseSYNCMedical']);
	Route::get('/agency-wise-sync-patient', [\App\Http\Controllers\HHAPatientController::class, 'agencyWiseSYNCPatient']);
	Route::get('/agency-wise-sync-patient-detail', [\App\Http\Controllers\HHAPatientController::class, 'updatePatientDemographics']);
	Route::get('/agency-wise-sync-caregiver-detail', [\App\Http\Controllers\HHACaregiversController::class, 'updateCaregiverDemographics']);
	Route::get('/last-modified', [\App\Http\Controllers\HHACaregiversController::class, 'caregiverModified']);
	Route::get('/agency-wise-sync-caregiver-other-compliance', [\App\Http\Controllers\HHACaregiversController::class, 'caregiverSyncOtherCompliance']);
	Route::get('/update-caregiver-dob', [\App\Http\Controllers\HHACaregiversController::class, 'updateCaregiverDOB']);
	Route::get('/last-patient-modified', [\App\Http\Controllers\HHAPatientController::class, 'patientModifiedPatientIds']);

	Route::get('check-sms-status', [\App\Http\Controllers\CronjobEventStatusUpdateController::class, 'updateSMSStatus']);
	Route::post('auth/resend-otp', [\App\Http\Controllers\UserController::class, 'resendOTP']);
	Route::post('auth/check-otp-valid', [\App\Http\Controllers\UserController::class, 'otpValid']);
	Route::any('twillio-sms-status-callback', [\App\Http\Controllers\CronjobBulkSMSCdpapController::class, 'smsCallBack']);
	Route::get('fetch-pending-sms-data', [\App\Http\Controllers\CronjobBulkSMSCdpapController::class, 'getALLSMSData']);
	Route::post('send-fetch-sms', [\App\Http\Controllers\CronjobBulkSMSCdpapController::class, 'getCustomSendData']);
	Route::post('send-emmacare-document',[\App\Http\Controllers\DownloadController::class,'sendEmmacareDocument']);
	Route::post('send-emmacare-document-referral-id',[\App\Http\Controllers\DownloadController::class,'sendEmmacareDocumentReferralId']);
	Route::prefix('make-an-appointment')->controller(\App\Http\Controllers\BulkViewAppointmentController::class)->group(function ($mkRoute) {
		$mkRoute->get('/', 'viewAnAppointment');
		$mkRoute->post('/save-make-appointment', 'saveAppointment');
	});

	Route::get('book', [\App\Http\Controllers\BulkViewAppointmentController::class, 'viewAnAppointment']);

	Route::get('appointment-thank-you', [\App\Http\Controllers\BulkViewAppointmentController::class, 'thankYou']);
	Route::group(['middleware' => 'auth'], function () {

		Route::resource('roles', RoleController::class);
		Route::get('/role-ajax', [RoleController::class, 'getRoleLogShowPage']);

		/*************************************Nybest Agency route *************************************/
		Route::get('/nybest-agency', [\App\Http\Controllers\AgencyHHAsetupController::class, 'index']);
		Route::post('/update-hha-setup', [\App\Http\Controllers\AgencyHHAsetupController::class, 'updateHHASetup']);
		Route::get('/agency-hha-tokan', [\App\Http\Controllers\AgencyHHAsetupController::class, 'getHHASetup']);
		Route::get('/agency-hha-enable-disable', [\App\Http\Controllers\AgencyHHAsetupController::class, 'enableDisableHHASetup']);
		Route::get('/nybest-agency/view/{id}', [\App\Http\Controllers\AgencyHHAsetupController::class, 'view']);
		Route::get('agency-wise-domain-list', [\App\Http\Controllers\AgencyController::class, 'agencyWiseDomain']);
		Route::get('notification-email-list', [\App\Http\Controllers\AgencyController::class, 'agencyWiseNotification']);
		Route::get('edit-email-notification', [\App\Http\Controllers\AgencyController::class, 'editEmailNotification']);
		Route::post('agency-wise-notification-email-save', [\App\Http\Controllers\AgencyController::class, 'saveNotifictionEmail']);
		Route::get('delete-notification-email', [\App\Http\Controllers\AgencyController::class, 'deleteNotificationEmail']);
		Route::post('agency-wise-domain-save', [\App\Http\Controllers\AgencyController::class, 'saveDomain']);
		Route::post('agency-domain-delete', [\App\Http\Controllers\AgencyController::class, 'domainDelete']);

		Route::post('nybest-agency/user-save', [\App\Http\Controllers\AgencyHHAsetupController::class, 'userSave']);
		Route::get('nybest-user-list', [\App\Http\Controllers\AgencyHHAsetupController::class, 'usertList']);

		/*************************************End Nybest Agency route *************************************/

		Route::post('update-expired-password', [\App\Http\Controllers\UserController::class, 'expiredChangeUpdate']);
		Route::post('/check-passwords', [\App\Http\Controllers\UserController::class, 'checkPasswords']);
		Route::get('/ajax-service', [\App\Http\Controllers\MasterController::class, 'AjaxService']);
		Route::get('/agency-ajax-service', [\App\Http\Controllers\MasterController::class, 'agencyAjaxService']);
		Route::get('/ajax-patient-requested-service', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'AjaxPatientRequestedService']);
		// change password
		Route::get('/change-password', [\App\Http\Controllers\UserController::class, 'changePassword']);
		Route::post('/check-old-password', [\App\Http\Controllers\UserController::class, 'checkOldPassword']);
		Route::post('/check-user-old-passwords', [\App\Http\Controllers\UserController::class, 'checkUserOldPasswords']);
		Route::post('/user-change-password', [\App\Http\Controllers\UserController::class, 'updatePassword']);
		Route::post('/user-two-factor-authentication', [\App\Http\Controllers\UserController::class, 'twoFactorEnable']);
		Route::post('/user-edit-two-factor-authentication', [\App\Http\Controllers\UserController::class, 'usertwoFactorEnable']);
		// change password

		Route::group(['middleware' => 'exmedSuperAdminAccess'], function () {

			Route::post('user-access', [\App\Http\Controllers\UserController::class, 'userAccess']);

			/*Master Module Start*/
			Route::get('/master-type-view', [\App\Http\Controllers\MasterController::class, 'master_type_view']);
			Route::get('/master', [\App\Http\Controllers\MasterController::class, 'index']);
			Route::delete('/delete-multiple-record', [\App\Http\Controllers\MasterController::class, 'deleteMultipleRecord'])->name('deleteMultipleRecord');

			Route::post('/add_master', [\App\Http\Controllers\MasterController::class, 'add']);
			Route::get('/delete_master', [\App\Http\Controllers\MasterController::class, 'delete']);
			Route::get('/edit_master', [\App\Http\Controllers\MasterController::class, 'edit']);
			Route::post('/update_master', [\App\Http\Controllers\MasterController::class, 'update']);
			Route::post('/agency-add', [\App\Http\Controllers\UserController::class, 'addMultipleAgency']);
			Route::get('/agency-remove/{id}', [\App\Http\Controllers\UserController::class, 'AgencyRemove']);
			/*Master Module End*/

			/*Agency Module Start*/
			Route::get('/agency', [\App\Http\Controllers\AgencyController::class, 'index']);
			Route::get('/agency/add', [\App\Http\Controllers\AgencyController::class, 'add']);
			Route::post('/agency/save', [\App\Http\Controllers\AgencyController::class, 'save']);
			Route::get('/agency/edit/{id}', [\App\Http\Controllers\AgencyController::class, 'edit']);
			Route::post('/agency/update/{id}', [\App\Http\Controllers\AgencyController::class, 'update']);
			Route::get('/agency/delete/{id}', [\App\Http\Controllers\AgencyController::class, 'delete']);
			Route::get('/agency-view/{id}', [\App\Http\Controllers\AgencyController::class, 'view']);
			Route::post('/search-agency', [\App\Http\Controllers\AgencyController::class, 'index']);
			Route::get('/agency-export', [\App\Http\Controllers\AgencyController::class, 'agencyExport']);
			Route::get('/agency-ajax-list', [\App\Http\Controllers\AgencyController::class, 'ajaxList']);
			Route::post('/agency-edit-table/', [\App\Http\Controllers\AgencyController::class, 'tableUpdate']);
			Route::post('/status-change-restrict-service-request-update', [\App\Http\Controllers\AgencyController::class, 'statusChangeRestrictServiceRequestUpdate']);
			Route::post('/agency/toggle-ai-call-logs', [\App\Http\Controllers\AgencyController::class, 'toggleAiCallLogs']);
			Route::get('/agency-notes', [\App\Http\Controllers\AgencyController::class, 'getAgencyNotes']);
			Route::get('/agency-notes-all', [\App\Http\Controllers\AgencyController::class, 'getAllAgencyNotes']);
			Route::post('/agency-note-add', [\App\Http\Controllers\AgencyController::class, 'addAgencyNote']);
			Route::post('/agency-note-delete', [\App\Http\Controllers\AgencyController::class, 'deleteAgencyNote']);
			Route::post('/agency-note-toggle', [\App\Http\Controllers\AgencyController::class, 'toggleAgencyNote']);
			Route::post('/search-rate/{id}', [\App\Http\Controllers\AgencyController::class, 'view']);
			Route::get('/agency/import/{id}', [\App\Http\Controllers\AgencyController::class, 'import']);
			Route::post('/agency/import/{id}', [\App\Http\Controllers\AgencyController::class, 'importSave']);
			Route::post('/agency/token-insert', [\App\Http\Controllers\AgencyController::class, 'generateToken']);
			Route::get('/agency-token-list', [\App\Http\Controllers\AgencyController::class, 'generateTokenList']);
			Route::post('/agency/smsstatus', [\App\Http\Controllers\AgencyController::class, 'agencySmsStatus'])->name('agencySmsStatus');
			Route::get('/agency/hhastatus', [\App\Http\Controllers\AgencyController::class, 'hhaStatus'])->name('hha-status');
			Route::post('/agency-logo-upload', [\App\Http\Controllers\AgencyController::class, 'agencyLogoUpload'])->name('agencyLogoUpload');
			Route::get('/agency-user-list', [\App\Http\Controllers\AgencyController::class, 'userList'])->name('agency-user-list');
			Route::get('/agency/user-export', [\App\Http\Controllers\AgencyController::class, 'agencyUserExportCsv'])->name('agencyUserExportCsv');
			Route::get('/agency/alaycare-status', [\App\Http\Controllers\AgencyController::class, 'agencyAlaycareStatus'])->name('agencyAlaycareStatus');
			Route::post('/agency/alaycare-details-save', [\App\Http\Controllers\AgencyController::class, 'agencyAlaycareDetailsSave'])->name('agencyAlaycareDetailsSave');
			Route::get('/alayacare-cronjob', [\App\Http\Controllers\AlayacareCronJobController::class, 'alayacareCronJob'])->name('alayacareCronJob');
			Route::post('/agency/agency-wise-service-save', [\App\Http\Controllers\AgencyController::class, 'agencyWiseServiceSave']);
			Route::get('/agency/agency-wise-service-ajax-list', [\App\Http\Controllers\AgencyController::class, 'ServiceAjaxList']);
			Route::get('delete-service', [\App\Http\Controllers\AgencyController::class, 'deleteService']);
			Route::get('edit-service', [\App\Http\Controllers\AgencyController::class, 'editService']);
			Route::get('/agency/agency-robort-status', [\App\Http\Controllers\AgencyController::class, 'agencyRobortStatus'])->name('agency-robort-status');
			Route::post('/agency/robort-details-save', [\App\Http\Controllers\AgencyController::class, 'agencyRobortDetailsSave'])->name('agency-robort-details-save');
			Route::get('/agency-alayacare-skill', [\App\Http\Controllers\AgencyController::class, 'alayacareSkill']);
			Route::post('/agency-add-skill', [\App\Http\Controllers\AgencyController::class, 'addAlayaAgencySkill']);
			// new nayan agency add users
			Route::get('/agency/adduser', [\App\Http\Controllers\AgencyUserController::class, 'add_page'])->name('adduser');

			Route::post('/agency/add_user', [\App\Http\Controllers\AgencyUserController::class, 'add'])->name('add_user');
			Route::post('/change-record-type', [\App\Http\Controllers\UserController::class, 'changeRecordType']);
			// new nayan agency add users

			Route::get('/agency-two-factor-enable-disable', [\App\Http\Controllers\AgencyController::class, 'agencyTwoFactor']);
			Route::get('/agency-password-expired-enable-disable', [\App\Http\Controllers\AgencyController::class, 'agencyPasswordExpired']);

			Route::get('/import/{id}', [\App\Http\Controllers\AgencyController::class, 'ExcelImport']);
			Route::post('/import_excel/{id}', [\App\Http\Controllers\AgencyController::class, 'subInsert']);
			Route::get('/site-setting', [\App\Http\Controllers\SiteSettingController::class, 'index']);
			Route::post('/site-setting/save', [\App\Http\Controllers\SiteSettingController::class, 'save']);

			Route::post('agency-wise-sms-save', [\App\Http\Controllers\AgencyController::class, 'agencyWiseSmsSave']);

			Route::post('/agency/toggle-portal-archive', [\App\Http\Controllers\AgencyController::class, 'togglePortalArchive']);
			Route::post('/agency/toggle-review', [\App\Http\Controllers\AgencyController::class, 'toggleReview']);
			Route::post('/agency/toggle-telehealth-send-sms', [\App\Http\Controllers\AgencyController::class, 'toggleTelehealthSendSms']);


			// country block
			Route::post('agency-country-save', [\App\Http\Controllers\AgencyController::class, 'countrySave']);
			Route::get('agency-wise-country-list', [\App\Http\Controllers\AgencyController::class, 'agencyWiseCountry']);
			// country block

			// ip address
			Route::post('agency-ip-address-save', [\App\Http\Controllers\AgencyController::class, 'ipAddressSave']);
			Route::get('agency-wise-ip-list', [\App\Http\Controllers\AgencyController::class, 'agencyWiseIpAddress']);
			Route::post('agency-ip-delete', [\App\Http\Controllers\AgencyController::class, 'ipAddressDelete']);
			Route::get('agency-ip-edit', [\App\Http\Controllers\AgencyController::class, 'ipAddressEdit']);
			Route::post('agency-ip-update', [\App\Http\Controllers\AgencyController::class, 'ipAddressUpdate']);
			Route::get('refresh-agency-employee', [\App\Http\Controllers\AlayacareCronJobController::class, 'refreshEmployee']);
			Route::get('sync-agency-employee/{id}', [\App\Http\Controllers\AlayacareCronJobController::class, 'getAllEmployeeDetails']);

			Route::get('sync-agency-visit', [\App\Http\Controllers\AgencyController::class, 'syncHHAVisit']);

			Route::get('refresh-agency-client', [\App\Http\Controllers\AlayacareCronJobController::class, 'refreshClient']);
			Route::get('sync-agency-client/{id}', [\App\Http\Controllers\AlayacareCronJobController::class, 'getAllClientDetails']);
			Route::get('refresh-agency-skill', [\App\Http\Controllers\AlayacareCronJobController::class, 'refreshSkill']);

			// ip address
			// country
			Route::resource('country', CountryController::class);
			Route::get('country/delete/{id}', [\App\Http\Controllers\CountryController::class, 'delete']);
			Route::get('country/edit/{id}', [\App\Http\Controllers\CountryController::class, 'edit']);
			Route::post('country/update/{id}', [\App\Http\Controllers\CountryController::class, 'update']);
			// country

			Route::get('hha-agency-office-list', [\App\Http\Controllers\AgencyController::class, 'hhaAgencyOfficeList']);
			Route::post('agency/hha-office-detail-save', [\App\Http\Controllers\AgencyController::class, 'hhaSaveOfficeDetail']);
			Route::post('/agency/hha-app-detail-update', [\App\Http\Controllers\AgencyController::class, 'hhaUpdateOfficeDetail']);
			Route::post('/agency/portalsmsstatus', [\App\Http\Controllers\AgencyController::class, 'portalAgencySMSStatus'])->name('portalAgencySMSStatus');
			Route::get('/load-portal-sms-list', [\App\Http\Controllers\AgencyController::class, 'agencyWisePortalList']);
			Route::get('/agency-hha-status', [\App\Http\Controllers\AgencyController::class, 'agencyHHAStatus']);
			Route::get('agency-sms-service-by-id', [\App\Http\Controllers\AgencyController::class, 'smsServiceById']);
			Route::post('disable-agency-wise-sms-service', [\App\Http\Controllers\AgencyController::class, 'disabledStatusUpdate']);
			Route::post('/update-document-email', [\App\Http\Controllers\AgencyController::class, 'updateDocumentEmail']);
			Route::post('/update-efax-no', [\App\Http\Controllers\AgencyController::class, 'updateEfaxNo']);
			Route::post('agency-user-block-unblock', [\App\Http\Controllers\AgencyController::class, 'agencyUserBlockUnblock']);

			Route::post('add-user-creator-email', [\App\Http\Controllers\AgencyController::class, 'addUserCreatorEmail']);
			Route::get('list-user-creator-email', [\App\Http\Controllers\AgencyController::class, 'listUserCreatorEmail']);
			Route::post('delete-user-creator-email', [\App\Http\Controllers\AgencyController::class, 'deleteUserCreatorEmail']);

			// Agency Delete with User Merge Routes
			Route::get('agency/get-active-agencies', [\App\Http\Controllers\AgencyController::class, 'getActiveAgencies']);
			Route::get('agency/get-users-by-agency', [\App\Http\Controllers\AgencyController::class, 'getUsersByAgency']);
			Route::post('agency/merge-users-and-delete', [\App\Http\Controllers\AgencyController::class, 'mergeUsersAndDeleteAgency']);

			Route::post('/agency/update-hha-md-details', [\App\Http\Controllers\AgencyController::class, 'updateHHAMdoOrderDetails']);
			Route::post('/agency/disabled-hha-md-details', [\App\Http\Controllers\AgencyController::class, 'disabledHHAMdoOrder']);
			Route::post('/agency/app-visting-detail-update', [\App\Http\Controllers\AgencyController::class, 'updateVisitingDetails']);
			Route::post('/agency/enabled-disabled-app-visting', [\App\Http\Controllers\AgencyController::class, 'enabledDisabledVisitingAid']);
			Route::post('/save-poc-document-type', [\App\Http\Controllers\AgencyController::class, 'saveAgencyPocDocumentType']);
			Route::post('/save-patient-assessment-document-type', [\App\Http\Controllers\AgencyController::class, 'savePatientAssessmentDocumentType']);
			Route::post('/save-cms485-document-type', [\App\Http\Controllers\AgencyController::class, 'saveCms485DocumentType']);
			Route::post('/save-emergency-kardex-document-type', [\App\Http\Controllers\AgencyController::class, 'saveEmergencyKardexDocumentType']);
			Route::post('/save-supervision-simple-document-type', [\App\Http\Controllers\AgencyController::class, 'saveSupervisionSimpleDocumentType']);
			Route::post('/save-patient-package-document-type', [\App\Http\Controllers\AgencyController::class, 'savePatientPackageDocumentType']);
			Route::get('/get-supervision-document-types', [\App\Http\Controllers\AgencyController::class, 'getSupervisionDocumentTypes']);
			Route::post('/save-supervision-document-type', [\App\Http\Controllers\AgencyController::class, 'saveSupervisionDocumentType']);
			Route::get('/get-other-compliance-types', [\App\Http\Controllers\AgencyController::class, 'getOtherComplianceTypes']);
			Route::post('/save-medical-id', [\App\Http\Controllers\AgencyController::class, 'saveMedicalId']);
			Route::get('/get-agency-other-compliance-medicals', [\App\Http\Controllers\AgencyController::class, 'getAgencyOtherComplianceMedicals']);
			Route::get('/get-agency-compliance-medical-results', [\App\Http\Controllers\AgencyController::class, 'getAgencyComplianceMedicalResults']);
			Route::get('/get-agency-medical-and-result', [\App\Http\Controllers\AgencyController::class, 'getAgencyMedicalAndResult']);
			Route::post('/save-agency-medical-and-result', [\App\Http\Controllers\AgencyController::class, 'saveAgencyMedicalAndResult']);
			
		});

		Route::group(['middleware' => 'exmedAgencyAdminAccess'], function () {
			/*User Module Start*/
			Route::get('/agency/user', [\App\Http\Controllers\AgencyUserController::class, 'index'])->name('agency-user');
			Route::get('/agency/user/add', [\App\Http\Controllers\AgencyUserController::class, 'add_page'])->name('agency-adduser');
			Route::post('/agency/user/add', [\App\Http\Controllers\AgencyUserController::class, 'add'])->name('agency-add_user');
			Route::get('/agency/user/edit', [\App\Http\Controllers\AgencyUserController::class, 'edit'])->name('agency-edituser');
			Route::post('/agency/user/edit', [\App\Http\Controllers\AgencyUserController::class, 'update'])->name('agency-update_user');
		});

		Route::get('/dashboard-graph', [\App\Http\Controllers\DashboardGraphController::class, 'dashboardGraph']);
		Route::get('/dashboard-graph-ajax', [\App\Http\Controllers\DashboardGraphController::class, 'dashboardGraphAjax']);
		Route::get('/dashboard-graph-agency', [\App\Http\Controllers\DashboardGraphController::class, 'dashboardGraphAgency']);

		Route::get('/document', [\App\Http\Controllers\TempleteController::class, 'document']);
		Route::get('/documents/sign', [\App\Http\Controllers\TempleteController::class, 'sign']);
		Route::get('template-duplicate', [\App\Http\Controllers\TempleteController::class, 'document_duplicate']);
		Route::get('template-delete', [\App\Http\Controllers\TempleteController::class, 'delete']);
		Route::get('getTypeByTemplate', [\App\Http\Controllers\TempleteController::class, 'getTypeByTemplate']);
		Route::get('template/getpdfbyTemplateid', [\App\Http\Controllers\TempleteController::class, 'getpdfbyTemplateid']);


		Route::get('/template/esign-lookup-fields/{id}', [\App\Http\Controllers\TempleteController::class, 'getResponseCanvas']);

		Route::get('/lookup/caregiver', [\App\Http\Controllers\TempleteController::class, 'caregiverLookUp']);

		Route::get('/template-agencies-list', [\App\Http\Controllers\TempleteController::class, 'loadAllAgenciesByTemplateId']);
		Route::get('patient-report', [\App\Http\Controllers\PatientReportController::class, 'index']);
		Route::get('patient-report/patient-report-export', [\App\Http\Controllers\PatientReportController::class, 'patientExport']);

		// Payment Type Report Routes
		Route::get('payment-type-report', [\App\Http\Controllers\PaymentTypeReportController::class, 'index']);
		Route::get('payment-type-report/ajax-list', [\App\Http\Controllers\PaymentTypeReportController::class, 'ajaxList']);
		Route::get('payment-type-report/ajax-counts', [\App\Http\Controllers\PaymentTypeReportController::class, 'ajaxCounts']);
		Route::get('payment-type-report/export', [\App\Http\Controllers\PaymentTypeReportController::class, 'export']);

		Route::get('/searchByEMCUserList', [\App\Http\Controllers\TempleteController::class, 'searchByEMCUserList']);

		Route::get('/searchByUserList', [\App\Http\Controllers\TempleteController::class, 'SearchUserList']);
		Route::get('/home', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
		Route::get('/assign-emc/AssignEMCExportCsv', [\App\Http\Controllers\DashboardController::class, 'AssignEMCExportCsv'])->name('AssignEMCExportCsv');
		Route::get('location', [\App\Http\Controllers\LocationMasterController::class, 'index'])->name('location');
		Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
		Route::get('/dashboard/dsd', [\App\Http\Controllers\DashboardController::class, 'doctorSMSDashboard'])->name('doctorSMSDashboard');
		Route::get('/dashboard-telehealth', [\App\Http\Controllers\DashboardController::class, 'getTelehealth'])->name('getTelehealth');
		Route::get('/dashboard-agency-record', [\App\Http\Controllers\DashboardController::class, 'agencyWiseRecordList'])->name('agencyWiseRecordList');
		Route::get('/dashboard/calander', [\App\Http\Controllers\DashboardCalanderController::class, 'index']);
		Route::get('/dashboard-calander', [\App\Http\Controllers\DashboardCalanderController::class, 'dashboard_calander']);

		Route::get('/dashboard/calendar-hospital-old', [\App\Http\Controllers\DashboardCalanderController::class, 'calenderHospital']);
		Route::get('/dashboard/dashboard-hosiptal-calander', [\App\Http\Controllers\DashboardCalanderController::class, 'calenderResponseHospital']);
		Route::post('/dashboard/getfollowupdates', [\App\Http\Controllers\DashboardCalanderController::class, 'calenderResponseHospital']);

		Route::post('/dashboard/generate-pdf', [\App\Http\Controllers\DashboardCalanderController::class, 'generatePdf']);
		Route::get('/latest-recent-notes', [\App\Http\Controllers\DashboardCalanderController::class, 'recentNotes']);
		/*User Module Start*/
		Route::get('/user', [\App\Http\Controllers\UserController::class, 'index'])->name('user');
		Route::get('/adduser', [\App\Http\Controllers\UserController::class, 'add_page'])->name('adduser');
		Route::post('/add_user', [\App\Http\Controllers\UserController::class, 'add'])->name('add_user');
		Route::get('/edituser', [\App\Http\Controllers\UserController::class, 'edit'])->name('edituser');
		Route::post('/update_user', [\App\Http\Controllers\UserController::class, 'update'])->name('update_user');
		Route::post('/agency-user-update', [\App\Http\Controllers\UserController::class, 'agencyUserUpdate'])->name('agency-user-update');
		Route::get('/delete_user', [\App\Http\Controllers\UserController::class, 'delete'])->name('delete');
		Route::any('/getUserType', [\App\Http\Controllers\UserController::class, 'userTypeByLoginType'])->name('delete');
		Route::get('/user-view/{id}', [\App\Http\Controllers\UserController::class, 'view']);
		Route::post('/search-user-list', [\App\Http\Controllers\UserController::class, 'index']);
		Route::get('/user-export', [\App\Http\Controllers\UserController::class, 'userExport']);
		Route::post('/user/update-password', [\App\Http\Controllers\UserController::class, 'update_password']);
		Route::get('/user-view-logs', [\App\Http\Controllers\UserController::class, 'userWiselogs']);
		Route::get('/user-view-login-logs', [\App\Http\Controllers\UserController::class, 'userWiseLoginLogs']);

		Route::get('/user-notification-email-list', [\App\Http\Controllers\UserController::class, 'userNotificationEmailList']);
		Route::post('user-notification-email-save', [\App\Http\Controllers\UserController::class, 'saveUSerNotifictionEmail']);
		Route::get('user-agency-list', [\App\Http\Controllers\UserController::class, 'getUserAgencyList']);
		Route::post('user-agency-save', [\App\Http\Controllers\UserController::class, 'userAgencySave']);
		Route::get('user-agency-edit', [\App\Http\Controllers\UserController::class, 'userAgencyEdit']);
		Route::get('user-agency-delete', [\App\Http\Controllers\UserController::class, 'userAgencyDelete']);

		Route::get('user-location-list', [\App\Http\Controllers\UserController::class, 'getUserLocationList']);
		Route::post('user-location-save', [\App\Http\Controllers\UserController::class, 'userLocationSave']);
		Route::get('user-location-edit', [\App\Http\Controllers\UserController::class, 'userLocationEdit']);
		Route::get('user-location-delete', [\App\Http\Controllers\UserController::class, 'userLocationDelete']);

		Route::get('/search-nybest-user', [\App\Http\Controllers\UserController::class, 'searchNyBestUser']);

		Route::get('/agency-view-logs', [\App\Http\Controllers\AgencyController::class, 'agencyrWiselogs']);
		Route::get('agency/search-users-by-agency', [\App\Http\Controllers\AgencyController::class, 'searchAgencyWiseUser']);
		/* userIp Address */
		Route::get('user-wise-ip-list', [\App\Http\Controllers\UserController::class, 'userWiseIpAddress']);
		Route::post('user-ip-address-save', [\App\Http\Controllers\UserController::class, 'userIpAddressSave']);
		Route::get('user-ip-edit', [\App\Http\Controllers\UserController::class, 'userIpAddressEdit']);
		Route::post('user-ip-update', [\App\Http\Controllers\UserController::class, 'userIpAddressUpdate']);
		Route::post('user-ip-delete', [\App\Http\Controllers\UserController::class, 'userIpAddressDelete']);
		Route::get('user-wise-domain-list', [\App\Http\Controllers\UserController::class, 'userWiseDomain']);
		Route::post('user-wise-domain-save', [\App\Http\Controllers\UserController::class, 'saveUserDomain']);
		Route::post('user-domain-delete', [\App\Http\Controllers\UserController::class, 'userDomainDelete']);

		Route::get('/send-invitation/{id}', [\App\Http\Controllers\UserController::class, 'send_invitation']);
		Route::get('/getUserListByAgencyId/{id}/{id1}', [\App\Http\Controllers\UserController::class, 'getUserListByAgencyId']);
		Route::get('/getUserListByEmcId/{id}', [\App\Http\Controllers\UserController::class, 'getUserListByEmcId']);
		Route::post('/updateagencyRecord', [\App\Http\Controllers\UserController::class, 'updateagencyRecord']);
		Route::post('/updateEmcRecord', [\App\Http\Controllers\UserController::class, 'updateEmcRecord']);
		Route::post('user-change-status', [\App\Http\Controllers\UserController::class, 'userChangeStatus']);
		Route::post('/chnagestatus', [\App\Http\Controllers\UserController::class, 'changeStatus']);
		Route::post('/exmedc-chnage-status', [\App\Http\Controllers\UserController::class, 'exmedcChangeStatus']);
		Route::post('/hospital-chnage-status', [\App\Http\Controllers\UserController::class, 'hospitalChangeStatus']);

		/*User Module End*/

		Route::get('expiring-medical', [\App\Http\Controllers\ExpiringMedicalController::class, 'index']);
		Route::get('expiring-appointment-ajax', [\App\Http\Controllers\ExpiringMedicalController::class, 'expiringAppointmentAjax']);
		Route::get('expiring-appointment-export', [\App\Http\Controllers\ExpiringMedicalController::class, 'exportCsv']);
		Route::get('update-last-work-date', [\App\Http\Controllers\ExpiringMedicalController::class, 'updateLastWorkDate']);
		/*Agency Module End*/


		/*language*/
		Route::get('lang/{locale}', [\App\Http\Controllers\HomeController::class, 'lang']);
		Route::get('support_error', [\App\Http\Controllers\HomeController::class, 'supportError']);

		/*end language*/


		/*Rates Module End*/

		/*Attachment Module Start*/

		Route::post('/get-county', [\App\Http\Controllers\PatientController::class, 'getCountyByZipCode']);

		/*End SMS Module */

		Route::get('template', [\App\Http\Controllers\TempleteController::class, 'index']);
		Route::get('template-add', [\App\Http\Controllers\TempleteController::class, 'add_template']);
		Route::post('insertTemplate', [\App\Http\Controllers\TempleteController::class, 'insert']);
		Route::get('template-edit', [\App\Http\Controllers\TempleteController::class, 'edit_template']);
		Route::post('updateTemplate', [\App\Http\Controllers\TempleteController::class, 'update']);
		Route::any('/template/uploadFiles', [\App\Http\Controllers\TempleteController::class, 'uploadFiles']);
		Route::get('template/details/{id}', [\App\Http\Controllers\TempleteController::class, 'view']);
		Route::post('SearchReponse', [\App\Http\Controllers\TempleteController::class, 'SearchReponse']);
		Route::post('documentInsert', [\App\Http\Controllers\TempleteController::class, 'document_insert']);
		Route::get('template-signer', [\App\Http\Controllers\TempleteController::class, 'DocumentSendByType']);
		Route::post('insertReceiptSigner', [\App\Http\Controllers\TempleteController::class, 'insertReceiptSigner']);
		Route::get('template-status', [\App\Http\Controllers\TempleteController::class, 'activeDeactive']);
		Route::get('/intakeResponse', [\App\Http\Controllers\TempleteController::class, 'intakeResponse']);
		Route::get('/nyBestResponse', [\App\Http\Controllers\TempleteController::class, 'nyBestResponse']);
		Route::get('/template/getsignerbyTemplateId', [\App\Http\Controllers\TempleteController::class, 'getsignerbyTemplateId']);
		Route::post('template_send', [\App\Http\Controllers\TempleteController::class, 'send']);


		Route::get('template-log/{id}', [\App\Http\Controllers\TempleteController::class, 'templateLogs']);
		Route::get('template-ajax', [\App\Http\Controllers\TempleteController::class, 'getTemplateLogPage']);

		Route::get('search', [\App\Http\Controllers\SearchController::class, 'index']);

		Route::get('/document-item', [\App\Http\Controllers\DocumentTypeMasterController::class, 'index']);
		Route::get('/document-item/document-add', [\App\Http\Controllers\DocumentTypeMasterController::class, 'add']);
		Route::post('/document-item/document-insert', [\App\Http\Controllers\DocumentTypeMasterController::class, 'save']);
		Route::get('/document-item/document-edit/{id}', [\App\Http\Controllers\DocumentTypeMasterController::class, 'edit']);
		Route::post('/document-item/document-update', [\App\Http\Controllers\DocumentTypeMasterController::class, 'update']);
		Route::get('/document-item/document-delete/{id}', [\App\Http\Controllers\DocumentTypeMasterController::class, 'documentDeleteByAgency']);
		Route::get('/document-item/document-export', [\App\Http\Controllers\DocumentTypeMasterController::class, 'documentexport']);

		Route::get('/agency-token', [\App\Http\Controllers\GenerateAgencyTokenController::class, 'index']);
		Route::get('/agency-token/token-add', [\App\Http\Controllers\GenerateAgencyTokenController::class, 'add']);
		Route::post('/agency-token/token-insert', [\App\Http\Controllers\GenerateAgencyTokenController::class, 'insert']);
		Route::get('/agency-token/token-delete/{id}', [\App\Http\Controllers\GenerateAgencyTokenController::class, 'delete']);
		Route::get('/checkGenereteAgencyToken', [\App\Http\Controllers\GenerateAgencyTokenController::class, 'checkGenereteAgencyToken']);

		Route::get('/agency-token/export', [\App\Http\Controllers\GenerateAgencyTokenController::class, 'export']);
		/*end  vishal d patel code 14-02-2020 */
		Route::get('/NotifMarkAsRead2', [\App\Http\Controllers\NotificationController::class, 'NotifMarkAsRead']);
		Route::get('/notification/unread-notification2', [\App\Http\Controllers\NotificationController::class, 'unreadNotificationByUser']);

		Route::get('/sms-template', [\App\Http\Controllers\SMSTemplateController::class, 'index']);
		Route::get('/sms-template/add', [\App\Http\Controllers\SMSTemplateController::class, 'add']);
		Route::get('/sms-template/edit/{id}', [\App\Http\Controllers\SMSTemplateController::class, 'edit']);
		Route::get('/sms-template/delete/{id}', [\App\Http\Controllers\SMSTemplateController::class, 'delete']);
		Route::post('/sms-template/insert', [\App\Http\Controllers\SMSTemplateController::class, 'save']);
		Route::post('/sms-template/update', [\App\Http\Controllers\SMSTemplateController::class, 'update']);
		/* Location master module */

		Route::get('/location/add', [\App\Http\Controllers\LocationMasterController::class, 'add']);
		Route::post('/location/save', [\App\Http\Controllers\LocationMasterController::class, 'save']);
		Route::get('/location/edit/{id}', [\App\Http\Controllers\LocationMasterController::class, 'edit']);
		Route::post('/location/update/{id}', [\App\Http\Controllers\LocationMasterController::class, 'update']);
		Route::get('/location/delete/{id}', [\App\Http\Controllers\LocationMasterController::class, 'delete']);

		Route::get('location/locationLog/{id}', [\App\Http\Controllers\LocationMasterController::class, 'locationLog']);
		Route::get('location/locationajax', [\App\Http\Controllers\LocationMasterController::class, 'getLocationLogPage']);

		Route::post('/location/save-block-dates', [\App\Http\Controllers\LocationMasterController::class, 'saveBlockDates']);
		Route::get('/location/get-block-dates', [\App\Http\Controllers\LocationMasterController::class, 'getBlockDates']);

		/*End Location master module */

		/*Agency mail log */
		Route::get('/agencies-mail-log', [\App\Http\Controllers\AgencyMailLogController::class, 'index']);
		Route::get('/agencies-mail-log/export', [\App\Http\Controllers\AgencyMailLogController::class, 'export']);
		/*End Agency mail log */
		/* Location Window master module */
		Route::get('/location-schedule/{id}', [\App\Http\Controllers\LocationScheduleController::class, 'index']);
		Route::get('/location-schedule/add/{id}', [\App\Http\Controllers\LocationScheduleController::class, 'add']);
		Route::post('/location-schedule/save', [\App\Http\Controllers\LocationScheduleController::class, 'save']);
		Route::get('/location-schedule/edit/{id}/{id1}', [\App\Http\Controllers\LocationScheduleController::class, 'edit']);
		Route::post('/location-schedule/update', [\App\Http\Controllers\LocationScheduleController::class, 'update']);
		Route::get('/location-schedule/delete/{id}/{id1}', [\App\Http\Controllers\LocationScheduleController::class, 'delete']);
		Route::get('/location/remaining-time-slot', [\App\Http\Controllers\LocationScheduleController::class, 'getSlotRemaining']);

		Route::get('/schedule-view-logs', [\App\Http\Controllers\LocationScheduleController::class, 'locationWiseScheduleLogs']);
		Route::get('/copy-schedule', [\App\Http\Controllers\LocationScheduleController::class, 'copySchedule']);

		/*End Location master module */

		Route::post('assign-emc-record/save', [\App\Http\Controllers\AssignEMCRecordController::class, 'save']);
		Route::get('assign-emc-record/checkAssignRecord', [\App\Http\Controllers\AssignEMCRecordController::class, 'checkAssignRecord']);
		Route::get('assign-emc/status-change/{id}', [\App\Http\Controllers\AssignEMCRecordController::class, 'statusChange']);
		Route::post('assign-emc/add-notes/{id}', [\App\Http\Controllers\AssignEMCRecordController::class, 'AddNotes']);
		Route::post('assign-emc/get-notes/{id}', [\App\Http\Controllers\AssignEMCRecordController::class, 'getNotes']);

		Route::get('reminder', [\App\Http\Controllers\RemainderController::class, 'index']);
		Route::get('reminder/add', [\App\Http\Controllers\RemainderController::class, 'add']);
		Route::post('reminder/save', [\App\Http\Controllers\RemainderController::class, 'save']);
		Route::get('remainder/remainder-ajax', [\App\Http\Controllers\RemainderController::class, 'AjaxRemainder']);
		Route::get('reminder/change-status', [\App\Http\Controllers\RemainderController::class, 'changeStatus']);
		Route::get('reminder/edit/{id}', [\App\Http\Controllers\RemainderController::class, 'edit']);
		Route::post('reminder/save', [\App\Http\Controllers\RemainderController::class, 'save']);
		Route::get('reminder/delete/{id}', [\App\Http\Controllers\RemainderController::class, 'delete']);

		Route::get('/pending-appoinment', [\App\Http\Controllers\PendingAppointmentController::class, 'index']);
		Route::get('/pending-appoinment-ajax-list', [\App\Http\Controllers\PendingAppointmentController::class, 'ajaxList']);

		Route::get('/upcomming-appoinment', [\App\Http\Controllers\UpcommingAppoinmentController::class, 'index']);
		Route::get('/upcomming-appoinment-ajax-list', [\App\Http\Controllers\UpcommingAppoinmentController::class, 'ajaxList']);


		/*Login Log Module Start*/
		Route::get('/user-login-log', [\App\Http\Controllers\LoginLogController::class, 'index'])->name('login-log');
		Route::get('/user-login-log-list', [\App\Http\Controllers\LoginLogController::class, 'loginLogList'])->name('login-log-list');
		Route::get('/user-login-log-export', [\App\Http\Controllers\LoginLogController::class, 'loginLogExport'])->name('login-log-export');


		Route::get('/user-all-log', [\App\Http\Controllers\MasterController::class, 'logView'])->name('user-all-log');
		Route::get('/user-all-log-list', [\App\Http\Controllers\MasterController::class, 'allLogList'])->name('all-log-list');
		Route::get('/user-all-log-export', [\App\Http\Controllers\MasterController::class, 'allLogExport'])->name('all-log-export');

		Route::post('insert-view-logs', [\App\Http\Controllers\MasterController::class, 'insertLogs'])->name('insert-view-logs');


		Route::get('/userlog', [\App\Http\Controllers\LogController::class, 'index']);
		Route::get('/user-log-list', [\App\Http\Controllers\LogController::class, 'allLogList']);

		//HHA Patinet list


		Route::get('/sync-hha-appointment-patient', [\App\Http\Controllers\HHAPatientController::class, 'syncPatientVisit']);
		Route::post('/hha-patient-vist-list', [\App\Http\Controllers\HHAPatientController::class, 'getAppointmentList']);
		Route::get('/hha-patient-document-type', [\App\Http\Controllers\HHAPatientController::class, 'getHHADocumentType']);
		Route::get('hha-patient-coordinator', [\App\Http\Controllers\HHAPatientController::class, 'HHAPatientCoordinator']);
		Route::get('link-to-hha-patient', [\App\Http\Controllers\HHAPatientController::class, 'linkHHAPatientList']);
		Route::get('get-patient-demographics', [\App\Http\Controllers\HHAPatientController::class, 'patientDemographicDetails']);


		Route::get('get-patient-authorization-info', [\App\Http\Controllers\HHAPatientController::class, 'GetPatientAuthorizationInfo']);

		Route::get('fetch-patient', [\App\Http\Controllers\HHAPatientController::class, 'fetchPatient']);
		Route::get('sync-agency-patient/{id}', [\App\Http\Controllers\HHAPatientController::class, 'syncPatient']);
		Route::get('/hha-patient/notes', [\App\Http\Controllers\HHAPatientController::class, 'syncHHAPatientNotes']);
		Route::get('/hha-patient/clinic', [\App\Http\Controllers\HHAPatientController::class, 'syncHHAPatientClinics']);
		Route::get('/hha-patient/search-patient-poc', [\App\Http\Controllers\HHAPatientController::class, 'getSearchPatientPOC']);
		Route::get('/search-hha-patient-code', [\App\Http\Controllers\HHAPatientController::class, 'searchPatientCode']);


		Route::post('/hha-add-patient-poc-deatils', [\App\Http\Controllers\HHAPatientController::class, 'addPatientPOCDetails']);
		Route::get('/hha-patient-poc-office-deatils', [\App\Http\Controllers\HHAPatientController::class, 'syncHHAPatientOffice']);
		Route::get('/hha-patient-poc-task-deatils', [\App\Http\Controllers\HHAPatientController::class, 'syncHHAPatientTask']);
		Route::get('/hha-patient-document-details', [\App\Http\Controllers\HHAPatientController::class, 'searchPatientDocument']);
		Route::get('/hha-patient-document-type-details', [\App\Http\Controllers\HHAPatientController::class, 'getPatientDocumentType']);
		Route::post('/save-hha-patient-document', [\App\Http\Controllers\HHAPatientController::class, 'savePatientDocument']);
		//end code

		//employee list alaycare
		// Route::get('/employee-list', [\App\Http\Controllers\AlaycareEmpController::class, 'getAlaycareEmpList']);
		// Route::get('/alaycare-employee-export', [\App\Http\Controllers\AlaycareEmpController::class, 'alaycareEmployeeExport']);
		// Route::get('/alaycare-employee-ajax-list', [\App\Http\Controllers\AlaycareEmpController::class, 'getAlaycareEmpListAjax']);
		// Route::post('/employee-add-appointment', [\App\Http\Controllers\AlaycareEmpController::class, 'empAddAppointment']);
		//end list

		//client list and details
		// Route::get('/client-list', [\App\Http\Controllers\AlayacareClientController::class, 'getAlaycareClientList']);
		// Route::get('/alaycare-client-export', [\App\Http\Controllers\AlayacareClientController::class, 'alaycareClientExport']);
		// Route::get('/alaycare-client-ajax-list', [\App\Http\Controllers\AlayacareClientController::class, 'getAlaycareClientListAjax']);
		// Route::post('/client-add-appointment', [\App\Http\Controllers\AlayacareClientController::class, 'clientAddAppointment']);


		//client list end

		// Route::get('/hha-appointment', [\App\Http\Controllers\HHAAppointmentController::class, 'index']);
		// Route::get('/hha-appointment-ajax', [\App\Http\Controllers\HHAAppointmentController::class, 'hhaAppoitmentAjax']);
		// Route::get('/hha-appointment-export', [\App\Http\Controllers\HHAAppointmentController::class, 'exportCsv']);

		Route::get('/get-branch-alaycare-ajax', [\App\Http\Controllers\AlayCareController::class, 'getBranchAlaycare']);
		Route::get('/get-group-by-branch-id', [\App\Http\Controllers\AlayCareController::class, 'getGroupByBranchId']);
		Route::get('/alaycare-employee-skill', [\App\Http\Controllers\AlaycareEmpController::class, 'alaycareEmployeeSkill']);
		Route::get('/alaycare-employee-scheduler', [\App\Http\Controllers\AlaycareEmpController::class, 'alaycareEmployeeSchedular']);
		Route::get('/alaycare-visit-details', [\App\Http\Controllers\AlaycareEmpController::class, 'alaycareVisitDetails']);
		Route::get('/alaycare-employee-notes', [\App\Http\Controllers\AlaycareEmpController::class, 'alaycareEmployeeNotes']);
		Route::get('/alaycare-employee-notes-type', [\App\Http\Controllers\AlaycareEmpController::class, 'alaycareEmployeeNotesType']);
		Route::post('/create-alaycare-employee-notes', [\App\Http\Controllers\AlaycareEmpController::class, 'createAlayaCareEmployeeNotes']);
		Route::post('/alaycare-employee-skill-update', [\App\Http\Controllers\AlaycareEmpController::class, 'alaycareEmployeeSkillUpdate']);
		Route::post('/alayacare-document-upload', [\App\Http\Controllers\AlaycareEmpController::class, 'alaycareDocumentUpload']);

		Route::get('/alayacare-upload-document-list', [\App\Http\Controllers\AlaycareEmpController::class, 'alaycareUploadDocumentList']);
		Route::get('/alayacare-skill-category', [\App\Http\Controllers\AlaycareEmpController::class, 'skillCategory']);
		Route::post('/alayacare-delete-skill', [\App\Http\Controllers\AlaycareEmpController::class, 'skillDelete']);
		Route::get('/alayacare-edit-skill', [\App\Http\Controllers\AlaycareEmpController::class, 'editSkill']);
		
		// Route::get('/alayacare-due-skill-list', [\App\Http\Controllers\AlaycareDueSkillController::class, 'index']);
		// Route::get('/due-skill-ajax-list', [\App\Http\Controllers\AlaycareDueSkillController::class, 'ajaxList']);
		// Route::get('/due-skill-export-csv', [\App\Http\Controllers\AlaycareDueSkillController::class, 'exportCSV']);
		// Route::post('/add-alayacare-patient-appointment', [\App\Http\Controllers\AlaycareDueSkillController::class, 'addAlayacarePatientAppointment']);
		Route::get('/download-attachment-file', [\App\Http\Controllers\AlaycareEmpController::class, 'downloadAttachment']);

		Route::post('/alayacare-post', [\App\Http\Controllers\AlayCareController::class, 'alayacarePost']);

		// Route::post('/add-appointment-patient', [\App\Http\Controllers\HHAAppointmentController::class, 'addAppoinmentPatient']);
		Route::get('/hha-document', [\App\Http\Controllers\PatientController::class, 'getHHADocumentByMedicalList']);
		Route::get('/hha-document-type', [\App\Http\Controllers\PatientController::class, 'getHHADocumentType']);
		Route::post('/send-hha-document', [\App\Http\Controllers\PatientController::class, 'sendHHADocument']);

		Route::post('patient/update-alaycare-id', [\App\Http\Controllers\PatientController::class, 'updateAlaycareId'])->name('updateAlaycareId');
		Route::get('alaycare-emp-data', [\App\Http\Controllers\PatientController::class, 'alaycareEmpData']);

		Route::get('/hha-caregiver-medical-results', [\App\Http\Controllers\PatientController::class, 'getCaregiverMedicalResults']);
		Route::get('/hha-complience-medical-results', [\App\Http\Controllers\PatientController::class, 'getCompienceMedicalResults']);

		Route::post('/update-inservice', [\App\Http\Controllers\PatientController::class, 'updateInservice']);
		Route::post('/update-inservice-two', [\App\Http\Controllers\PatientController::class, 'updateInserviceTwo']);
		Route::post('/update-training', [\App\Http\Controllers\PatientController::class, 'updateTraining']);
		Route::post('/patient/training-due-date', [\App\Http\Controllers\PatientController::class, 'updateTraningDueDate']);

		Route::post('/patient/updateEmergencyPhone', [\App\Http\Controllers\PatientController::class, 'updateEmergencyPhone']);
		Route::post('/patient/updateEmail', [\App\Http\Controllers\PatientController::class, 'updateEmail']);
		Route::post('/patient/updatePharmacy', [\App\Http\Controllers\PatientController::class, 'updatePharmacy']);
		Route::post('/patient/updateNoMedicationTaken', [\App\Http\Controllers\PatientController::class, 'updateNoMedicationTaken']);

		Route::post('/update-hha-document', [\App\Http\Controllers\PatientController::class, 'updateHHADocumentWithMedicalResult']);
		Route::post('/update-hha-document-patient', [\App\Http\Controllers\PatientController::class, 'updateHHADocumentPatient']);
		Route::post('/update-complience-document', [\App\Http\Controllers\PatientController::class, 'updateHHAcomplienceDocument']);
		Route::post('patient/document-upload', [\App\Http\Controllers\PatientController::class, 'DocumentUploadByPatient']);

		Route::post('insert-view-logs', [\App\Http\Controllers\MasterController::class, 'insertLogs'])->name('insert-view-logs');
		Route::get('doctor', [\App\Http\Controllers\DoctorController::class, 'index']);
		Route::get('doctor/add', [\App\Http\Controllers\DoctorController::class, 'add']);
		Route::post('doctor/save', [\App\Http\Controllers\DoctorController::class, 'save']);
		Route::get('doctor/edit/{id}', [\App\Http\Controllers\DoctorController::class, 'edit']);
		Route::get('doctor/delete/{id}', [\App\Http\Controllers\DoctorController::class, 'delete']);
		Route::get('doctor/doctor-export', [\App\Http\Controllers\DoctorController::class, 'agencyExport']);
		Route::post('doctor/update/{id}', [\App\Http\Controllers\DoctorController::class, 'update']);
		Route::get('doctor/log/{id}', [\App\Http\Controllers\DoctorController::class, 'logs']);
		Route::get('doctor/ajax', [\App\Http\Controllers\DoctorController::class, 'getDoctorLogPage']);
		Route::post('doctor/toggle-status', [\App\Http\Controllers\DoctorController::class, 'toggleDoctorStatus']);
		Route::post('doctor/toggle-signature-stamp-status', [\App\Http\Controllers\DoctorController::class, 'toggleSignatureStampStatus']);
		Route::get('appointment', [\App\Http\Controllers\PatientController::class, 'index']);
		Route::get('patient/add', [\App\Http\Controllers\PatientController::class, 'add']);
		Route::get('patient/send-booking-sms', [\App\Http\Controllers\PatientController::class, 'sendBookingSMS']);
		Route::post('patient/save', [\App\Http\Controllers\PatientController::class, 'save']);
		Route::get('patient/edit/{id}', [\App\Http\Controllers\PatientController::class, 'edit']);
		Route::get('patient/delete/{id}', [\App\Http\Controllers\PatientController::class, 'delete']);
		Route::get('patient/patient-export', [\App\Http\Controllers\PatientController::class, 'exportCsv']);
		Route::post('patient/update/{id}', [\App\Http\Controllers\PatientController::class, 'update']);
		Route::get('patient/view/{id}', [\App\Http\Controllers\PatientController::class, 'view']);
		Route::get('/appointment-view-logs', [\App\Http\Controllers\PatientController::class, 'appointmentWiselogs']);
		Route::post('/patient/get-notes/{id}', [\App\Http\Controllers\PatientController::class, 'getNotes']);
		Route::post('/patient/patient-notes/{id}', [\App\Http\Controllers\PatientController::class, 'SendNotes']);
		Route::post('/patient/patient-archive', [\App\Http\Controllers\PatientController::class, 'archive']);
		Route::get('archive-list', [\App\Http\Controllers\PatientController::class, 'PatientArchiveList']);
		Route::post('/patient/patient-unarchive', [\App\Http\Controllers\PatientController::class, 'unarchive']);
		Route::post('/patient/mark-reviewed', [\App\Http\Controllers\PatientController::class, 'markPatientReviewed']);
		Route::post('/patient/mark-unreviewed', [\App\Http\Controllers\PatientController::class, 'markPatientUnreviewed']);
		Route::post('/patient/due-date', [\App\Http\Controllers\PatientController::class, 'DueDateUpdate']);
		Route::get('patient/undo/{id}', [\App\Http\Controllers\PatientController::class, 'undo']);
		Route::post('patient/assign', [\App\Http\Controllers\PatientController::class, 'patientAssign'])->name('patientAssign');
		Route::get('/appointment-logs-view/{id}', [\App\Http\Controllers\PatientController::class, 'appointmentlogsView']);
		Route::get('/auto-complete-email', [\App\Http\Controllers\PatientController::class, 'autoCompleteEmail']);

		Route::get('appointment/status', [\App\Http\Controllers\PatientController::class, 'StatusWiseRecord']);
		Route::get('patient-refused', [\App\Http\Controllers\PatientController::class, 'StatusWiseRecord']);
		Route::post('patient/link-to-caregiver', [\App\Http\Controllers\PatientController::class, 'linkToCaregiver']);
		Route::post('patient/link-to-patient', [\App\Http\Controllers\PatientController::class, 'linkToPatient']);
		Route::post('patient/combine-appointment', [\App\Http\Controllers\MergeAppointmentController::class, 'mergeAppointment']);
		Route::post('patient/inservice-appointment', [\App\Http\Controllers\PatientController::class, 'inserviceAppointment']);
		Route::get('sms-logs-list/{id}', [\App\Http\Controllers\PatientController::class, 'smsLogs']);
		Route::post('patient-followup-date', [\App\Http\Controllers\PatientController::class, 'patientFollowupDate']);
		Route::post('patient-avaibility-followup-date', [\App\Http\Controllers\PatientController::class, 'patientAvaibilityFollowupDate']);

		Route::get('dpp/{id}', [\App\Http\Controllers\DownloadController::class, 'showImage']);
		Route::get('dpe/{id}', [\App\Http\Controllers\DownloadController::class, 'esignDocusign']);
		Route::get('drr/{id}', [\App\Http\Controllers\DownloadController::class, 'showAttachmentForRecord']);
		Route::get('dre/{id}', [\App\Http\Controllers\DownloadController::class, 'esignRecord']);
		Route::get('dpa/{id}', [\App\Http\Controllers\DownloadController::class, 'downloadAttachment']);
		Route::get('doctor-image-show-aws/{id}', [\App\Http\Controllers\DownloadController::class, 'doctorImages']);

		Route::post('patient/appointment-add', [\App\Http\Controllers\PatientController::class, 'AppAppointment']);
		Route::post('patient/appointment-schedule', [\App\Http\Controllers\PatientController::class, 'AppointmentSchedule']);
		
		Route::get('patient/approveStatus', [\App\Http\Controllers\PatientController::class, 'approveStatus']);
		Route::get('patient/statusUpdate/{id}', [\App\Http\Controllers\PatientController::class, 'statusUpdate']);
		Route::get('patient/services-list', [\App\Http\Controllers\PatientController::class, 'getServices']);
		Route::post('patient/document-send-patientId', [\App\Http\Controllers\PatientController::class, 'DocumentUploadByPatientId']);
		Route::get('patient/document/{docId}/ai-summary', [\App\Http\Controllers\PatientController::class, 'getDocumentAiSummary']);
		Route::post('patient/document/{docId}/save-ai-summary', [\App\Http\Controllers\PatientController::class, 'saveDocumentAiSummary']);
		Route::post('patient/document/{docId}/ai-analyse', [\App\Http\Controllers\PatientController::class, 'aiAnalyseByDocId']);
		Route::post('patient/ai-analyse-proxy', [\App\Http\Controllers\PatientController::class, 'aiAnalyseProxy']);

		Route::get('patient/send-sms/{id}', [\App\Http\Controllers\PatientController::class, 'SendSMSBYPending']);
		Route::get('patient/send-remainder-sms/{id}', [\App\Http\Controllers\PatientController::class, 'SendSMSBYBooked']);
		Route::get('patient/document-delete/{recordId}/{id}', [\App\Http\Controllers\PatientController::class, 'patientDocumentDelete']);
		Route::post('patient/reminder', [\App\Http\Controllers\PatientController::class, 'ReminderAppointment']);
		Route::get('patient/reminder-list/{id}', [\App\Http\Controllers\PatientController::class, 'ReminderAppointmentList']);
		Route::get('patient/garbase-colloction/{id}', [\App\Http\Controllers\PatientController::class, 'GarbaseCollection']);

		Route::get('patient/get-sms-text', [\App\Http\Controllers\TextMessageController::class, 'getMessageList']);
		Route::post('patient/text-message-notes', [\App\Http\Controllers\TextMessageController::class, 'smsTextMessage']);


		Route::post('patient/send-docusign', [\App\Http\Controllers\PatientController::class, 'sendDocusign']);
		Route::get('patient/send-docusign-list', [\App\Http\Controllers\PatientController::class, 'sendDocusignList']);
		Route::get('patient/signer-request', [\App\Http\Controllers\PatientController::class, 'sendDocusignRequest']);



		Route::get('/patient/change-status', [\App\Http\Controllers\PatientController::class, 'patientStatus']);
		Route::get('/thankyous', [\App\Http\Controllers\PatientController::class, 'thankyou']);

		Route::get('/expired', [\App\Http\Controllers\PatientController::class, 'expired']);
		Route::post('/patient/assign-nybest-user', [\App\Http\Controllers\PatientController::class, 'AssignNyBestUser']);
		Route::post('/patient/next-appoinment-date', [\App\Http\Controllers\PatientController::class, 'NextAppoinment']);
		Route::post('/patient/completed-date', [\App\Http\Controllers\PatientController::class, 'CompletedDate']);
		Route::post('/patient/telehealth-add', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'AddTelehealth']);
		Route::post('/patient/attachment-pdf', [\App\Http\Controllers\PatientController::class, 'AttachmentPDF']);
		Route::post('/patient/payment-type', [\App\Http\Controllers\PatientController::class, 'paymentTypeStatus']);
		Route::post('/patient/save-link-to-third-party', [\App\Http\Controllers\PatientController::class, 'saveThirdPartyLink']);
		Route::post('/patient/send-document-mail', [\App\Http\Controllers\PatientController::class, 'sendDocumentMail']);
		Route::post('/patient/mark-send-back-to-agency', [\App\Http\Controllers\PatientController::class, 'markSendBackToAgency']);
		Route::get('/patient-document-ajax-list', [\App\Http\Controllers\PatientController::class, 'ajaxDocumentList']);
		Route::get('/patient/view_old/{id}', [\App\Http\Controllers\PatientController::class, 'viewNewDesign']);

		Route::resource('doctor-paper-work', DoctorPaperWorkController::class);
		Route::get('doctor-paper-work-ajax', [\App\Http\Controllers\DoctorPaperWorkController::class, 'ajaxList']);
		Route::post('doctor-paper-work/update/{id}', [\App\Http\Controllers\DoctorPaperWorkController::class, 'update']);
		Route::get('doctor-paper-work-delete/{id}', [\App\Http\Controllers\DoctorPaperWorkController::class, 'delete']);
		Route::get('doctor-paper-work-response', [\App\Http\Controllers\DoctorPaperWorkController::class, 'EmcUserList']);
		Route::post('doctor-paper-work-notes', [\App\Http\Controllers\DoctorPaperWorkController::class, 'NotesUpdate']);
		Route::get('change-status-paper-work', [\App\Http\Controllers\DoctorPaperWorkController::class, 'changeStatus']);
		Route::get('doctor-paper-work-csv', [\App\Http\Controllers\DoctorPaperWorkController::class, 'exportCsv']);
		Route::get('doctor-paper-work/{id}', [\App\Http\Controllers\DoctorPaperWorkController::class, 'show']);

		/*************************Task Module ********************** */
		Route::resource('request-list', RequestController::class);

		/******************************************************************************/

		Route::resource('patient-calendar', PatientCalenderController::class);
		Route::get('patient-ajax-calender', [\App\Http\Controllers\PatientCalenderController::class, 'calenderAjax']);
		Route::get('patient-hha', [\App\Http\Controllers\PatientHHAMedicalController::class, 'index']);
		Route::get('patient-hha-ajax', [\App\Http\Controllers\PatientHHAMedicalController::class, 'ajaxList']);
		Route::post('add-patient-hha', [\App\Http\Controllers\PatientHHAMedicalController::class, 'addPatient']);

		Route::resource('language', LanguageController::class);
		Route::get('/language/log/ajax', [\App\Http\Controllers\LanguageController::class, 'getLogShowPage'])->name('languageLog');
		Route::get('/patient/sync', [\App\Http\Controllers\HHACaregiversController::class, 'syncVisit']);
		Route::post('/hha-caregiver-vist-list', [\App\Http\Controllers\HHACaregiversController::class, 'getAppoinmentList']);
		Route::get('/hha-caregiver/notes', [\App\Http\Controllers\HHACaregiversController::class, 'syncHHACaregiverNotes']);
		Route::post('/hha-caregiver/create-notes', [\App\Http\Controllers\HHACaregiversController::class, 'HHACaregiverCreateNotes']);
		Route::get('/hha-caregiver/subject', [\App\Http\Controllers\HHACaregiversController::class, 'syncHHACaregiverSubject']);
		Route::get('/hha-caregiver-medical', [\App\Http\Controllers\HHACaregiversController::class, 'syncHHACaregiverMedical']);
		Route::get('/hha-caregiver-medical-ajax', [\App\Http\Controllers\HHACaregiverMedicalController::class, 'ajaxList']);
		Route::get('/hha-other-compliance', [\App\Http\Controllers\HHACaregiversController::class, 'syncHHACaregiverOtherCompliance']);
		Route::get('/hha-caregiver-inservice', [\App\Http\Controllers\HHACaregiversController::class, 'syncHHACaregiverInService']);
		Route::get('link-to-hha-caregiver', [\App\Http\Controllers\HHACaregiversController::class, 'linktoHHACaregiver']);
		Route::get('search-hha-caregiver', [\App\Http\Controllers\HHACaregiversController::class, 'searchCaregiverCode']);
		Route::get('hha-caregiver-avaibility', [\App\Http\Controllers\HHACaregiversController::class, 'HHACaregiverAvaibility']);
		Route::get('caregiver-document-list', [\App\Http\Controllers\HHACaregiversController::class, 'HHACaregiverMedicalDetails']);
		Route::get('fetch-hha-caregiver', [\App\Http\Controllers\HHACaregiversController::class, 'fetchCaregiverDetails']);
		Route::get('/hha-caregiver-document-details', [\App\Http\Controllers\HHACaregiversController::class, 'searchCaregiverDocument']);
		Route::get('/hha-caregiver-document-type-details', [\App\Http\Controllers\HHACaregiversController::class, 'getCaregiverDocumentType']);
		Route::post('/save-hha-caregiver-document', [\App\Http\Controllers\HHACaregiversController::class, 'saveCaregiverDocument']);

		Route::get('/sync-hha-other-compliance/{id}', [\App\Http\Controllers\HHACaregiversController::class, 'syncHHACaregiverOtherComplianceWithAgencyId']);

		// Route::get('hha-other-compliances', [\App\Http\Controllers\HHAOtherComplianceController::class, 'index']);
		// Route::get('hha-other-compliance-ajax', [\App\Http\Controllers\HHAOtherComplianceController::class, 'hhaAppoitmentAjax']);
		// Route::post('add-hha-other-compliance', [\App\Http\Controllers\HHAOtherComplianceController::class, 'addOtherHHACompliance']);
		// Route::get('get-hha-other-compliance', [\App\Http\Controllers\HHAOtherComplianceController::class, 'getOtherCompliancebyCaregiverId']);
		// Route::get('hha-other-compliance-export', [\App\Http\Controllers\HHAOtherComplianceController::class, 'exportCsv']);

		Route::get('fetch-caregiver', [\App\Http\Controllers\HHACaregiversController::class, 'fetchCaregiver']);
		Route::get('sync-agency-caregiver/{id}', [\App\Http\Controllers\HHACaregiversController::class, 'getAllCaregiverDetails']);

		Route::get('fetch-hha-medical/{id}', [\App\Http\Controllers\HHACaregiversController::class, 'fetchHHAMedical']);
		Route::get('fetch-hha-medical-new/{id}', [\App\Http\Controllers\HHACaregiversController::class, 'fetchHHAMedicalNew']);

		Route::get('/refresh-agency-remote-employee', [\App\Http\Controllers\RobortCronjobController::class, 'remoteRefreshEmployee']);

		Route::get('/sync-robort-visit/{id}', [\App\Http\Controllers\RobortCronjobController::class, 'getSchedule']);
		Route::get('/hha-caregiver-detail', [\App\Http\Controllers\HHACaregiversController::class, 'caregiverDetails']);

		Route::get('agency-token-delete', [\App\Http\Controllers\AgencyController::class, 'deleteToken']);
		Route::get('token-api-call', [\App\Http\Controllers\AgencyController::class, 'getAllApicallUsingToken']);

		/*******************************Start Third Party Patient Master******************* */
		Route::get('third-party-patient', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'index']);
		Route::get('third-party-patient-ajax-list', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'ajaxList']);
		Route::post('add-appointment-third-patient', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'addAppointmentForThirdParty']);
		Route::get('check-existing-record', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'existingRecord']);
		Route::get('link-third-party-appointment', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'linkThirdPartyAppointment']);
		Route::get('link-to-third-party', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'searchThirdParty']);
		Route::post('third-party/advanced-search-third-party', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'advancedSearchThirdParty']);
		Route::post('third-party-document-upload', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'uploadDocumentThirdParty']);
		Route::get('third-party/third-party-pending-medical', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'getPendingMedical']);
		Route::get('third-party/third-party-pending-medical-result', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'getThirdPartyMedicalResult']);
		Route::post('third-party/save-visiting-third-party', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'saveVisitingAid']);
		Route::post('third-party/send-visiting-third-party-document', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'sendVisitingAidDocument']);

		Route::get('third-party/employee-pending-medical', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'getAllPendingMedical']);

		Route::resource('insurance-master', InsuranceMasterController::class);
		Route::resource('announcement', AnnouncementController::class);
		Route::get('announcement-ajax-list', [\App\Http\Controllers\AnnouncementController::class, 'ajaxList']);

		Route::get('directory', [\App\Http\Controllers\DirectoryController::class, 'index']);
		Route::get('ajax-directory-list', [\App\Http\Controllers\DirectoryController::class, 'ajaxList']);

		Route::get('/schedule-enabled-disabled', [\App\Http\Controllers\LocationScheduleController::class, 'scheduleEnabledDisabled']);

		Route::post('update-document-service', [\App\Http\Controllers\PatientController::class, 'updateDocumentService']);

		Route::get('/hha-patient/subject', [\App\Http\Controllers\HHAPatientController::class, 'syncHHAPatientSubject']);

		// Start Field Master
		Route::resource('field-master', FieldMasterController::class);
		// End Field Master

		//Start Agency Master
		Route::get('/agency-master-list', [\App\Http\Controllers\AgencyController::class, 'agencyMasterList'])->name('agency-master-list');
		Route::post('/update-agencymaster-order', [\App\Http\Controllers\AgencyController::class, 'updateAgencyMasterOrder'])->name('update-agencymaster-order');
		Route::post('/store-agency-master', [\App\Http\Controllers\FieldMasterController::class, 'storeAgencyField'])->name('store-agency-master');
		Route::post('agency-master-delete/{id}', [\App\Http\Controllers\AgencyController::class, 'destroyAgencyMaster']);
		//End Agency Master


		Route::post('/store-patient-custom-data', [\App\Http\Controllers\PatientController::class, 'savePatientCustomData']);
		Route::post('/save-patient-custom-data', [\App\Http\Controllers\PatientController::class, 'patientCustomDataSave']);
		// Start Form Setup
		Route::resource('form-setup', FormSetupController::class);
		Route::get('/form-setup-list', [\App\Http\Controllers\AgencyController::class, 'formSetupList'])->name('form-setup-list');
		Route::get('/field-master-list', [\App\Http\Controllers\AgencyController::class, 'fieldMasterList'])->name('field-master-list');
		Route::post('/store-template', [\App\Http\Controllers\FormSetupController::class, 'storeTemplate'])->name('store-template');
		Route::get('/get-templates', [\App\Http\Controllers\FormSetupController::class, 'getTemplates'])->name('get.templates');

		//Start Agency All Form
		Route::get('/get-template-data', [\App\Http\Controllers\AgencyAllFormController::class, 'getTemplatesData'])->name('get.templateData');
		Route::post('/store-agency-form', [\App\Http\Controllers\AgencyAllFormController::class, 'storeAgencyForm'])->name('store-agency-form');
		Route::post('/store-move-to-esign', [\App\Http\Controllers\AgencyAllFormController::class, 'storeMoveToEsign'])->name('store-move-to-esign');
		// Route::get('/get-agency-master-field', [\App\Http\Controllers\AgencyAllFormController::class,'getAgencyMasterField'])->name('get-agency-master-field');
		//End Agency All Form

		// End Form Setup

		Route::get('/get-template-data', [\App\Http\Controllers\AgencyAllFormController::class, 'getTemplatesData'])->name('get.templateData');

		Route::post('/store-agency-form', [\App\Http\Controllers\AgencyAllFormController::class, 'storeAgencyForm'])->name('store-agency-form');
		Route::get('get-form-by-checkbox', [\App\Http\Controllers\TempleteController::class, 'getFormByCheckbox'])->name('get-form-by-checkbox');
		Route::get('get-form-by-radio', [\App\Http\Controllers\TempleteController::class, 'getFormByRadio'])->name('get-form-by-radio');

		Route::get('notification-setting', [\App\Http\Controllers\NotificationSettingController::class, 'index'])->name('notification-setting.index');

		Route::get('service-wise-appointment-report', [\App\Http\Controllers\HamaspikAppointmentReportController::class, 'index']);
		Route::get('ajax-list', [\App\Http\Controllers\HamaspikAppointmentReportController::class, 'ajaxList']);
		Route::get('service-export-csv', [\App\Http\Controllers\HamaspikAppointmentReportController::class, 'exportCsv']);
		Route::get('service-export-csv-new', [\App\Http\Controllers\HamaspikAppointmentReportController::class, 'exportCsvServices']);
		Route::get('service-export-csv-document', [\App\Http\Controllers\HamaspikAppointmentReportController::class, 'exportCsvDocument']);

		Route::get('/get-patient-services', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'getPatientServices']);
		Route::get('/patient-wise-service-requested-list', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'serviceRequestedList']);
		Route::get('/patient-wise-services', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'getPatientWiseServices']);
		Route::post('/save-patient-type-wise-services', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'savePatientTypeWiseServices']);
		Route::post('/save-service-email', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'saveServiceEmail']);

		Route::get('/ajax-request-service', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'ajaxRequestService']);
		Route::get('/patient-wise-service-requested/view/{id}', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'serviceRequestedView']);
		Route::get('/patient-service-wise-list', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'serviceWiseList']);
		Route::post('/upload-document-request-service', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'uploadDocumentService']);
		Route::get('/checkAllStatus', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'checkAllStatus']);

		Route::get('search-patient', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'searchPatient']);
		Route::post('update-search-third-party-link', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'updateSearchThirdPartyLink']);
		Route::get('link-patient-services', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'linkPatientServices']);
		Route::post('update-patient-services', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'updateLinkPatientService']);
		Route::get('get-patient-details-by-id', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'patientDetailsGet']);
		Route::get('show-document-upload-list', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'showDocumentListLog']);

		Route::get('/agency-all-form-table-list', [\App\Http\Controllers\PatientController::class, 'agencyAllFormTableList']);
		Route::get('/agency-all-form-table/view/{id}', [\App\Http\Controllers\PatientController::class, 'agencyAllFormTableView']);

		Route::post('token-update', [\App\Http\Controllers\AgencyController::class, 'tokenUpdateName']);
		Route::post('/change-service-status', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'changeStatusPatientTypeWiseServices']);
		Route::get('/third-party-patient/third-party-patient-export', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'thirdPartyPatientExport']);

		Route::get('requested-service', [\App\Http\Controllers\RequestedServiceController::class, 'index']);
		Route::get('requested-service-ajax-list', [\App\Http\Controllers\RequestedServiceController::class, 'ajaxList']);

		Route::get('/dashboard/calendar-hospital-v2', [\App\Http\Controllers\DashboardAppointmentCalanderController::class, 'newCalendarDesign']);
		Route::get('/dashboard/get-new-appointment-data', [\App\Http\Controllers\DashboardAppointmentCalanderController::class, 'getNewAppointmentData']);
		Route::get('/new-calendar-hospital/appointment-details', [\App\Http\Controllers\DashboardAppointmentCalanderController::class, 'appointmentDetails']);
		Route::get('/new-calendar-hospital/get-monthly-appoitment-details', [\App\Http\Controllers\DashboardAppointmentCalanderController::class, 'getMonthlyAppointmentDetails']);
		//Route::get('/get-agencies', [\App\Http\Controllers\PatientController::class,'getAgencies']);

		// Start Approve Stamp
		Route::resource('stamp', ApproveStampController::class);
		Route::get('/approve-stamp-list', [\App\Http\Controllers\ApproveStampController::class, 'approveStampList']);
		Route::get('/approve-stamp-by-id', [\App\Http\Controllers\ApproveStampController::class, 'approveStampById']);
		Route::get('/stampstatus', [\App\Http\Controllers\ApproveStampController::class, 'stampStatus']);
		// End Approve Stamp
		// Start Rating Master
		Route::resource('rating-master', RatingMasterController::class);
		// End Rating Master

		// Start Form Group
		Route::resource('form-group', FormGroupController::class);
		Route::get('/form-group-list', [\App\Http\Controllers\FormGroupController::class, 'formGroupList']);
		Route::get('/get-form-groups', [\App\Http\Controllers\FormGroupController::class, 'getFormGroups']);
		Route::post('/update-formGroup-order', [\App\Http\Controllers\FormGroupController::class, 'updateFormGroupOrder'])->name('update-formGroup-order');
		// End Form Group

		/*********Dashboard New Design */
		Route::get('user-dashboard', [\App\Http\Controllers\UserDashboardController::class, 'index']);
		Route::get('total-patient-caregiver', [\App\Http\Controllers\UserDashboardController::class, 'totalCountForCaregiverPatientAgency']);
		Route::get('agency-wise-patient-caregiver-graph', [\App\Http\Controllers\UserDashboardController::class, 'agencyWisePatientCaregiverGraph']);
		Route::get('service-wise-graph', [\App\Http\Controllers\UserDashboardController::class, 'serviceWiseGraph']);
		Route::get('location-wise-patient-caregiver-graph', [\App\Http\Controllers\UserDashboardController::class, 'locationWiseGraph']);
		//Invoice Upload
		Route::resource('invoice', InvoiceUploadController::class);
		Route::get('/invoice-upload-ajax-list', [\App\Http\Controllers\InvoiceUploadController::class, 'ajaxInvoiceUploadList']);
		Route::post('invoice-save', [\App\Http\Controllers\InvoiceUploadController::class, 'invoiceSave']);
		Route::post('invoice/document-upload', [\App\Http\Controllers\InvoiceUploadController::class, 'invoiceDocumentUploadByPatient']);
		Route::get('invoice-upload-document/{id}', [\App\Http\Controllers\InvoiceUploadController::class, 'showDocument']);
		Route::get('today-appointment-data', [\App\Http\Controllers\UserDashboardController::class, 'getTodayAppointmentData']);
		Route::get('upcomming-appointment-data', [\App\Http\Controllers\UserDashboardController::class, 'getUpcommingAppointmentData']);
		Route::get('get-notes-data', [\App\Http\Controllers\UserDashboardController::class, 'getNotesData']);
		Route::get('status-wise-graph', [\App\Http\Controllers\UserDashboardController::class, 'statusWiseGraph']);

		Route::get('employee-dashboard', [\App\Http\Controllers\EmployeeDashboardController::class, 'index']);
		Route::get('employee-total-agency', [\App\Http\Controllers\EmployeeDashboardController::class, 'totalCountForAgency']);
		Route::get('employee-today-appointment-data', [\App\Http\Controllers\EmployeeDashboardController::class, 'getTodayAppointmentData']);
		Route::get('employee-upcomming-appointment-data', [\App\Http\Controllers\EmployeeDashboardController::class, 'getUpcommingAppointmentData']);
		Route::get('statistic-data', [\App\Http\Controllers\EmployeeDashboardController::class, 'getStatisticData']);
		Route::get('notes-data', [\App\Http\Controllers\EmployeeDashboardController::class, 'getNotesData']);
		Route::get('task-data', [\App\Http\Controllers\EmployeeDashboardController::class, 'getTaskData']);
		Route::get('esign-data', [\App\Http\Controllers\EmployeeDashboardController::class, 'getEsignData']);
		Route::get('load-agency-list', [\App\Http\Controllers\EmployeeDashboardController::class, 'userWiseAgencyShow']);

		/**************************Inflowcare Patient Logs******************* */
		Route::get('inflowcare-patient-logs', [\App\Http\Controllers\InflowcarePatientLogController::class, 'index']);
		Route::get('inflowcare-patient-logs-ajax', [\App\Http\Controllers\InflowcarePatientLogController::class, 'ajaxList']);
		Route::get('inflowcare-patient-logs-export-csv', [\App\Http\Controllers\InflowcarePatientLogController::class, 'exportCsv']);

		/**************************HHA POC Log******************* */
		Route::get('hha-audit-log', [\App\Http\Controllers\HhaAuditLogController::class, 'index']);
		Route::get('hha-audit-log-ajax', [\App\Http\Controllers\HhaAuditLogController::class, 'getLogs']);
		Route::get('hha-audit-log-detail/{id}', [\App\Http\Controllers\HhaAuditLogController::class, 'show']);

		/**************************DOcument Report******************* */
		Route::get('document-report', [\App\Http\Controllers\DocumentSectionReportController::class, 'index']);
		Route::get('document-ajax-list', [\App\Http\Controllers\DocumentSectionReportController::class, 'ajaxList']);
		Route::get('document-export-csv', [\App\Http\Controllers\DocumentSectionReportController::class, 'exportCsv']);
		Route::get('document-export-csv-new', [\App\Http\Controllers\DocumentSectionReportController::class, 'exportCsvNWithoutServices']);
		Route::get('document-export-csv-two', [\App\Http\Controllers\DocumentSectionReportController::class, 'exportCsvTwo']);

		Route::get('patient-add-new', [\App\Http\Controllers\CreatePatientController::class, 'add']);
		Route::get('create-new-patient', [\App\Http\Controllers\CreatePatientController::class, 'createNew']);
		Route::get('search-patient-details', [\App\Http\Controllers\CreatePatientController::class, 'searchPatientList']);
		Route::post('save-patient-details', [\App\Http\Controllers\CreatePatientController::class, 'savePatientDetails']);
		Route::post('update-remaining-patient-details', [\App\Http\Controllers\CreatePatientController::class, 'updateRemainingPatientDetails']);
		Route::get('/get-demo-graphic-details-data', [\App\Http\Controllers\CreatePatientController::class, 'getDemographicDeatailsData']);

		Route::get('/patient-service-requested', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'patientServiceRequestedList']);
		Route::get('/all-patient-service-requested-ajax-list', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'patientServiceRequestedAjaxList']);
		Route::get('/patient-service-requested-export', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'patientServiceRequestedExport']);

		Route::get('/hub-patient-service-requested', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'hubPatientServiceRequestedList']);
		Route::get('/hub-patient-service-requested-ajax-list', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'hubPatientServiceRequestedAjaxList']);
		Route::get('/hub-patient-service-requested-export', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'hubPatientServiceRequestedExport']);

		Route::get('/view-pdf-response', [\App\Http\Controllers\CreatePatientController::class, 'viewPdfPatient']);

		Route::get('agency-dashboard', [\App\Http\Controllers\AgencyDashboardController::class, 'index']);
		Route::get('total-agency', [\App\Http\Controllers\AgencyDashboardController::class, 'totalCountForAgency']);
		Route::get('agency-today-appointment-data', [\App\Http\Controllers\AgencyDashboardController::class, 'getTodayAppointmentData']);
		Route::get('agency-upcomming-appointment-data', [\App\Http\Controllers\AgencyDashboardController::class, 'getUpcommingAppointmentData']);
		Route::get('agency-statistic-data', [\App\Http\Controllers\AgencyDashboardController::class, 'getStatisticData']);
		Route::get('agency-notes-data', [\App\Http\Controllers\AgencyDashboardController::class, 'getNotesData']);
		Route::get('agency-notes-nybest-user-data', [\App\Http\Controllers\AgencyDashboardController::class, 'getNotesDataNyBestUser']);
		Route::get('agency-location-data', [\App\Http\Controllers\AgencyDashboardController::class, 'getLocationData']);
		Route::get('agency-announcement-data', [\App\Http\Controllers\AgencyDashboardController::class, 'getAnnouncementData']);

		Route::resource('form-report', FormReportController::class);
		Route::get('/form-report-ajax-list', [\App\Http\Controllers\FormReportController::class, 'esignReportAjaxList']);
		Route::get('/form-report-export', [\App\Http\Controllers\FormReportController::class, 'esignReportExport']);

		Route::get('/search-nybest-patient', [\App\Http\Controllers\FormReportController::class, 'searchNyBestPatient']);
		Route::get('/search-nybest-all-user', [\App\Http\Controllers\FormReportController::class, 'searchNyBestAllUser']);

		/**************************HHA Patient ********************************/
		Route::get('/hha-patient-changes-v2', [\App\Http\Controllers\HHAPatientController::class, 'getHHAPatientChangesV2']);
		Route::get('/hha-patient-authorization-changes-v2', [\App\Http\Controllers\HHAPatientController::class, 'getHHAPatientAuthorizationChangesV2']);

		/*Agency User Report Module Start*/
		Route::get('/agency-user-report', [\App\Http\Controllers\UserAgencyRerportController::class, 'index'])->name('agency-user-report');
		Route::get('/agency-user-report-ajax', [\App\Http\Controllers\UserAgencyRerportController::class, 'ajaxList']);
		Route::get('/agency-user-report-export', [\App\Http\Controllers\UserAgencyRerportController::class, 'userExport']);

		/*Agency Summary Module Start*/
		Route::get('/agency-summary', [\App\Http\Controllers\AgencySummaryController::class, 'index'])->name('agency-summary');
		Route::get('/agency-summary-ajax', [\App\Http\Controllers\AgencySummaryController::class, 'ajaxList']);
		Route::get('/agency-summary-export', [\App\Http\Controllers\AgencySummaryController::class, 'exportCSV']);
		/*Agency Summary Module End*/

		/* HHA Visit calender */
		Route::post('hha-caregiver-visit-list-data', [\App\Http\Controllers\HHACaregiversController::class, 'getCaregiverAppointmentData']);
		Route::post('hha-patient-visit-list-data', [\App\Http\Controllers\HHAPatientController::class, 'getPatientAppointmentData']);
		Route::get('employee-announcement-data', [\App\Http\Controllers\EmployeeDashboardController::class, 'getAnnouncementData']);

		Route::get('hha-agency-sync', [\App\Http\Controllers\HHAAgencySyncController::class, 'index']);
		Route::get('hha-sync-agency-ajax', [\App\Http\Controllers\HHAAgencySyncController::class, 'ajaxList']);
		Route::post('link-visiting-aids-services', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'linkVisitingAidService']);

		Route::get('download-agency-images', [\App\Http\Controllers\DownloadController::class, 'downloadAgencyImages']);

		//Start New Esign design
		Route::get('/template/esign-lookup-history-fields/{id}', [\App\Http\Controllers\TempleteController::class, 'getResponseCanvasHistory']);
		Route::get('/template-user-data', [\App\Http\Controllers\TempleteController::class, 'getFilteredTemplateUsers']);
		Route::get('/document-new', [\App\Http\Controllers\TempleteController::class, 'documentNew']);
		//End New Esign design

		// Start Event Master
		Route::resource('event-master', EventMasterController::class);
		Route::get('/event-master-list', [\App\Http\Controllers\EventMasterController::class, 'eventList']);
		Route::get('/event-master-by-id', [\App\Http\Controllers\EventMasterController::class, 'eventById']);
		Route::get('/get-active-event', [\App\Http\Controllers\EventMasterController::class, 'activeEvents']);
		Route::get('event-image-show-aws/{id}', [\App\Http\Controllers\DownloadController::class, 'eventImages']);
		Route::post('change-event-status', [\App\Http\Controllers\EventMasterController::class, 'changeStatus']);
		// End Event Master

		// Start Ebook Master
		Route::resource('ebook', EbookController::class);
		Route::get('/ebook-list', [\App\Http\Controllers\EbookController::class, 'ebookList']);
		Route::get('/ebook-by-id', [\App\Http\Controllers\EbookController::class, 'ebookById']);
		Route::get('ebook-show-aws/{id}', [\App\Http\Controllers\DownloadController::class, 'ebookVideo']);
		Route::get('ebook-view', [\App\Http\Controllers\EbookController::class, 'ebookView']);
		// End Ebook Master

		Route::get('get-document-of-patient', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'getDocumentData']);
		Route::get('get-document-third-party', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'getDocumentIdData']);
		Route::get('change-status-master-services', [\App\Http\Controllers\MasterController::class, 'changeStatusServices']);

		// Start Ebook Master
		Route::resource('disable-date', DisableDateController::class);
		Route::get('/disable-date-list', [\App\Http\Controllers\DisableDateController::class, 'disableDateList']);
		Route::get('/disable-date-by-id', [\App\Http\Controllers\DisableDateController::class, 'disableDateById']);
		// End Ebook Master

		Route::post('patient-language-update', [\App\Http\Controllers\PatientController::class, 'updatePatientLanguage']);
		Route::post('patient-mobile-update', [\App\Http\Controllers\PatientController::class, 'updatePatientMobile']);
		Route::post('patient-phone-update', [\App\Http\Controllers\PatientController::class, 'updatePatientPhone']);

		Route::resource('api-log-report', ApiCallLogReportController::class);
		Route::get('/api-log-report-ajax-list', [\App\Http\Controllers\ApiCallLogReportController::class, 'ajaxList']);
		Route::get('/api-log-report-export', [\App\Http\Controllers\ApiCallLogReportController::class, 'reportExport']);
		Route::get('/get-api-log-by-id', [\App\Http\Controllers\ApiCallLogReportController::class, 'apiLogById']);
		Route::get('/third-party-wise-data-show', [\App\Http\Controllers\ApiCallLogReportController::class, 'getAppointmentWiseDataShow']);

		Route::post('agency-wise-webhook-save', [\App\Http\Controllers\AgencyController::class, 'agencyWiseWebhookSave']);
		Route::get('load-agency-web-hook', [\App\Http\Controllers\AgencyController::class, 'loadAgencyWebHookList']);
		Route::get('edit-agency-wise-webhook', [\App\Http\Controllers\AgencyController::class, 'editAgencyWebHook']);
		Route::post('delete-agency-web-hook', [\App\Http\Controllers\AgencyController::class, 'deleteAgencyWebHook']);

		//Start Notification
		Route::get('user-notification', [\App\Http\Controllers\NotificationUserController::class, 'getAllUserWiseNotification']);
		Route::get('get-all-unread-user-notification', [\App\Http\Controllers\NotificationUserController::class, 'getAllUnreadUserWiseNotification']);
		Route::post('mark-as-read-notification', [\App\Http\Controllers\NotificationUserController::class, 'NotificationMarkAsRead']);
		Route::get('get-all-ajax-user-notification', [\App\Http\Controllers\NotificationUserController::class, 'notificationAjaxList']);
		Route::post('mark-as-read-count-notification', [\App\Http\Controllers\NotificationUserController::class, 'getAllUserCountWiseNotification']);
		//End notification

		//write document


		Route::get('flag-change-status', [\App\Http\Controllers\FlagAppointmentController::class, 'changeStatusFlag']);
		Route::get('flag-list', [\App\Http\Controllers\FlagAppointmentController::class, 'flagList']);
		Route::get('flag-appointment-ajax-list', [\App\Http\Controllers\FlagAppointmentController::class, 'flagAppointmentAjaxList']);
		Route::get('flag-doc-ajax-list', [\App\Http\Controllers\FlagAppointmentController::class, 'flagDocAjaxList']);
		Route::get('flag-task-ajax-list', [\App\Http\Controllers\FlagAppointmentController::class, 'flagTaskAjaxList']);
		Route::get('flag-notes-ajax-list', [\App\Http\Controllers\FlagAppointmentController::class, 'flagNotesAjaxList']);

		Route::get('location-search-list', [\App\Http\Controllers\LocationMasterController::class, 'searchLocation']);
		Route::get('location-search-ajax-list', [\App\Http\Controllers\LocationMasterController::class, 'searchLocationData']);

		Route::get('flag-change-document-status', [\App\Http\Controllers\FlagAppointmentController::class, 'changeDocStatusFlag']);
		Route::get('flag-change-notes-status', [\App\Http\Controllers\FlagAppointmentController::class, 'changeNotesStatusFlag']);
		Route::get('flag-change-task-status', [\App\Http\Controllers\FlagAppointmentController::class, 'changeTaskStatusFlag']);

		Route::get('third-party-report-list', [\App\Http\Controllers\ThirdPartyReportController::class, 'reportList']);
		Route::get('third-party-ajax-report-list', [\App\Http\Controllers\ThirdPartyReportController::class, 'reportAjaxList']);
		Route::get('third-party-patient-report-export', [\App\Http\Controllers\ThirdPartyReportController::class, 'thirdPartyReportExport']);
		Route::post('update-patient-dob', [\App\Http\Controllers\PatientController::class, 'updatePatientDob']);

		Route::get('pse-location', [\App\Http\Controllers\PSEController::class, 'index']);
		Route::get('/pse-location/add', [\App\Http\Controllers\PSEController::class, 'add']);
		Route::post('/pse-location/save', [\App\Http\Controllers\PSEController::class, 'save']);
		Route::get('/pse-location/edit/{id}', [\App\Http\Controllers\PSEController::class, 'edit']);
		Route::post('/pse-location/update/{id}', [\App\Http\Controllers\PSEController::class, 'update']);
		Route::get('/pse-location/delete/{id}', [\App\Http\Controllers\PSEController::class, 'delete']);

		Route::post('mark-flag-read', [\App\Http\Controllers\FlagAppointmentController::class, 'flagMarkAsRead']);
		/*********Esign Dashboard New Design */
		Route::get('esign-dashboard', [\App\Http\Controllers\EsignDashboardController::class, 'index']);
		Route::get('total-esign-data', [\App\Http\Controllers\EsignDashboardController::class, 'totalCountForEsign']);
		Route::get('esign-data', [\App\Http\Controllers\EsignDashboardController::class, 'esignData']);
		Route::get('get-status-wise-graph-data', [\App\Http\Controllers\EsignDashboardController::class, 'statusWiseGraphData']);
		Route::get('get-template-usage-graph-data', [\App\Http\Controllers\EsignDashboardController::class, 'templeteUseGraphData']);
		Route::get('get-review-esign-graph-data', [\App\Http\Controllers\EsignDashboardController::class, 'reviewEsignGraphData']);
		Route::get('get-created-esign-graph-data', [\App\Http\Controllers\EsignDashboardController::class, 'createEsignGraphData']);

		Route::post('notification-count', [\App\Http\Controllers\NotificationUserController::class, 'countNotificationOfUser']);
		//Write Document
		Route::get('dre-write-document/{id}', [\App\Http\Controllers\DownloadController::class, 'esignRecordWriteDocument']);

		//Esign new design v2
		Route::get('patient/view-v2/{id}', [\App\Http\Controllers\PatientController::class, 'viewV2']);

		Route::get('patient', [\App\Http\Controllers\DemographicController::class, 'index']);
		Route::get('patient-detail-list', [\App\Http\Controllers\DemographicController::class, 'ajaxList']);
		Route::get('export-csv', [\App\Http\Controllers\DemographicController::class, 'exportCsv']);

		// Task Dashboard
		Route::get('task-dashboard', [\App\Http\Controllers\TaskDashboardController::class, 'index']);
		Route::get('total-count-task-data', [\App\Http\Controllers\TaskDashboardController::class, 'getTotalCountData']);
		Route::get('task-priority-chart-data', [\App\Http\Controllers\TaskDashboardController::class, 'getPriorityTaskData']);
		Route::get('task-list-data', [\App\Http\Controllers\TaskDashboardController::class, 'getTaskData']);
		Route::get('patient-wise-task-data', [\App\Http\Controllers\TaskDashboardController::class, 'getPatientWiseTaskCount']);
		Route::get('assignee-wise-task-data', [\App\Http\Controllers\TaskDashboardController::class, 'getAssigneeWiseTaskCount']);

		// Start Group Notification Master
		Route::resource('group-notification', GroupNotificationController::class);
		Route::get('/group-notification-list', [\App\Http\Controllers\GroupNotificationController::class, 'groupNotificationList']);
		Route::get('/group-notification-by-id', [\App\Http\Controllers\GroupNotificationController::class, 'groupNotificationById']);
		Route::get('group-notification-service-data', [\App\Http\Controllers\GroupNotificationController::class, 'getServiceData']);
		// End Group Notification Master

		Route::get('/sync-template-data/{id}', [\App\Http\Controllers\TempleteController::class, 'syncTemplateData']);

		Route::get('appointment-dashboard', [\App\Http\Controllers\AppointmentDashboardController::class, 'index']);
		Route::get('status-appointment-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'statusAppointmentData']);
		Route::get('agency-wise-appointment-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'agencyAppointmentData']);
		Route::get('services-wise-appointment-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'servicesAppointmentData']);
		Route::get('location-wise-appointment-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'locationAppointmentData']);
		Route::get('user-wise-appointment-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'userAppointmentData']);

		Route::get('patient-monthly-chart-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'monthlyWisePatientChartData']);
		Route::get('agency-monthly-chart-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'monthlyWiseAgencyChartData']);
		Route::get('location-monthly-chart-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'monthlyWiseAgencyChartData']);
		Route::get('monthly-comparision-chart-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'monthlyWiseComparisionChartData']);

		Route::get('total-counts-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'getTotalounts']);
		Route::get('get-agency-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'getAgencyData']);
		Route::get('get-location-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'getLocationData']);
		Route::get('get-user-data', [\App\Http\Controllers\AppointmentDashboardController::class, 'getUserData']);
		Route::get('/search-notification-users', [\App\Http\Controllers\UserController::class, 'searchUserData']);

		Route::get('search-total-appointment', [\App\Http\Controllers\PatientController::class, 'getTotalAppointmentDetails']);

		/*******************************Start Arla Patient Master******************* */
		Route::get('arla-appointment', [\App\Http\Controllers\ArlaController::class, 'index']);
		Route::get('arla-ajax-list', [\App\Http\Controllers\ArlaController::class, 'ajaxList']);
		Route::post('add-appointment-arla-patient', [\App\Http\Controllers\ArlaController::class, 'addAppointmentForThirdParty']);
		Route::get('check-existing-record', [\App\Http\Controllers\ArlaController::class, 'existingRecord']);
		Route::get('link-arla-appointment', [\App\Http\Controllers\ArlaController::class, 'linkThirdPartyAppointment']);
		Route::get('link-to-arla', [\App\Http\Controllers\ArlaController::class, 'searchThirdParty']);
		Route::post('arla-document-upload', [\App\Http\Controllers\ArlaController::class, 'uploadDocumentThirdParty']);
		Route::get('/arla-appointment/arla-export', [\App\Http\Controllers\ArlaController::class, 'thirdPartyPatientExport']);

		Route::get('cms', [\App\Http\Controllers\CMSController::class, 'index']);
		Route::get('cms-edit', [\App\Http\Controllers\CMSController::class, 'edit']);
		Route::post('cms/update', [\App\Http\Controllers\CMSController::class, 'update']);

		Route::get('my-profile', [\App\Http\Controllers\MyProfileController::class, 'index']);
		Route::post('update-my-profile', [\App\Http\Controllers\MyProfileController::class, 'profileUpdate'])->middleware('throttle:5,1');
		Route::get('user-profile-image', [\App\Http\Controllers\MyProfileController::class, 'getUserProfileImage']);
		Route::get('send-cms-notification', [\App\Http\Controllers\CMSController::class, 'sendEmailNotification']);


		Route::get('enquiry', [\App\Http\Controllers\EnquiryFormController::class, 'index']);
		Route::get('enquiry-ajax-list', [\App\Http\Controllers\EnquiryFormController::class, 'ajaxList']);
		Route::get('enquiry/create', [\App\Http\Controllers\EnquiryFormController::class, 'add']);
		Route::post('enquiry/save', [\App\Http\Controllers\EnquiryFormController::class, 'save']);
		Route::post('enquiry-reply', [\App\Http\Controllers\EnquiryFormController::class, 'enquiryReply']);
		Route::get('view-enquiry-reply-log', [\App\Http\Controllers\EnquiryFormController::class, 'viewEnquiryReplyLog']);
		Route::post('change-enquiry-status', [\App\Http\Controllers\EnquiryFormController::class, 'changeEnquiryStatus']);

		// Start Event Master
		Route::get('announcements', [\App\Http\Controllers\CommunicationController::class, 'index']);
		Route::post('announcements-save', [\App\Http\Controllers\CommunicationController::class, 'save']);
		Route::post('announcements-update', [\App\Http\Controllers\CommunicationController::class, 'update']);
		Route::get('announcements-delete', [\App\Http\Controllers\CommunicationController::class, 'destory']);
		Route::get('/announcements-list', [\App\Http\Controllers\CommunicationController::class, 'eventList']);
		Route::get('/announcements-by-id', [\App\Http\Controllers\CommunicationController::class, 'eventById']);

		Route::get('announcements-image-show-aws/{id}', [\App\Http\Controllers\CommunicationController::class, 'eventImages']);

		Route::post('announcements-mail-all-user', [\App\Http\Controllers\CommunicationController::class, 'announcementsMailAllUsers']);
		// End Event Master
		Route::get('deleted_appointment_show/{id}', [\App\Http\Controllers\DeletedPatientController::class, 'view']);
		Route::get('delete-patient-document-ajax-list', [\App\Http\Controllers\DeletedPatientController::class, 'ajaxDocumentList']);
		Route::post('deleted-patient-get-notes/{id}', [\App\Http\Controllers\DeletedPatientController::class, 'getNotes']);
		Route::get('delete-sms-logs-list/{id}', [\App\Http\Controllers\DeletedPatientController::class, 'smsLogs']);
		Route::get('delete-patient/get-sms-text', [\App\Http\Controllers\TextMessageController::class, 'getDeletedMessageList']);
		Route::get('/delete-patient-wise-service-requested-list', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'deleteServiceRequestedList']);
		Route::get('/location-search-total-count', [\App\Http\Controllers\LocationScheduleController::class, 'totalCountByLocationIdAndDate']);
		Route::get('/location-search-total-time-count', [\App\Http\Controllers\LocationScheduleController::class, 'totalCountByLocationIdAndDateTime']);
		Route::post('user-page-detail-change', [\App\Http\Controllers\UserController::class, 'changePageViewStatus']);

		Route::post('save-basic-details', [\App\Http\Controllers\PatientController::class, 'saveBasicDetails']);
		Route::post('save-address-details', [\App\Http\Controllers\PatientController::class, 'saveAddressDetails']);
		Route::post('save-other-details', [\App\Http\Controllers\PatientController::class, 'saveOtherDetails']);
		Route::resource('feedback-form-report', FeedbackFormReportController::class);
		Route::get('/feedback-form-report-ajax-list', [\App\Http\Controllers\FeedbackFormReportController::class, 'ajaxList']);
		Route::get('/feedback-form-report-export', [\App\Http\Controllers\FeedbackFormReportController::class, 'reportExport']);
		Route::get('/get-feedback-form-by-id', [\App\Http\Controllers\FeedbackFormReportController::class, 'apiLogById']);

		/*********Start Visiting Aid New Design ************/
		Route::get('visiting-aid-dashboard', [\App\Http\Controllers\VisitingAidDashboardController::class, 'index']);
		Route::get('total-count-data', [\App\Http\Controllers\VisitingAidDashboardController::class, 'totalCountData']);
		Route::get('visting-list-ajax-data', [\App\Http\Controllers\VisitingAidDashboardController::class, 'visitingListData']);

		Route::get('visting-agency-wise-data', [\App\Http\Controllers\VisitingAidDashboardController::class, 'visitingAgencyWiseChartData']);
		Route::get('visting-count-data', [\App\Http\Controllers\VisitingAidDashboardController::class, 'visitingCountData']);
		Route::get('visting-service-status-wise-data', [\App\Http\Controllers\VisitingAidDashboardController::class, 'visitingServiceStatusWiseChartData']);
		Route::get('visting-services-wise-data', [\App\Http\Controllers\VisitingAidDashboardController::class, 'visitingServicesWiseChartData']);
		Route::get('visting-type-data', [\App\Http\Controllers\VisitingAidDashboardController::class, 'visitingTypeWiseChartData']);
		/*********End Visiting Aid New Design ************/

		Route::post('create-image-using-type', [\App\Http\Controllers\TempleteController::class, 'createImageUsingType']);
		/*********Start Analytics Dashboard New Design ************/
		Route::get('analytics-dashboard', [\App\Http\Controllers\AnalyticsDashboardController::class, 'index']);
		Route::get('current-inprogress', [\App\Http\Controllers\AnalyticsDashboardController::class, 'currentInprogressPatient']);
		Route::get('current-checkin', [\App\Http\Controllers\AnalyticsDashboardController::class, 'currentCheckInPatient']);
		Route::get('recently-updated-status', [\App\Http\Controllers\AnalyticsDashboardController::class, 'recentUpdateStatus']);
		Route::get('visiting-aid-patient', [\App\Http\Controllers\AnalyticsDashboardController::class, 'visitingAidPatient']);
		Route::get('visiting-aid-type', [\App\Http\Controllers\AnalyticsDashboardController::class, 'visitingAidType']);
		Route::get('recent-notes', [\App\Http\Controllers\AnalyticsDashboardController::class, 'recentNotes']);
		Route::get('visiting-due-date', [\App\Http\Controllers\AnalyticsDashboardController::class, 'visitingDueDate']);
		Route::get('location-status-data', [\App\Http\Controllers\AnalyticsDashboardController::class, 'locationWiseStatus']);
		Route::get('count-status-data', [\App\Http\Controllers\AnalyticsDashboardController::class, 'countStatusData']);
		Route::get('document-recent-data', [\App\Http\Controllers\AnalyticsDashboardController::class, 'documentRecentData']);
		Route::get('agency-status-data', [\App\Http\Controllers\AnalyticsDashboardController::class, 'agencyWiseStatus']);
		/*********End Analytics Dashboard New Design ************/
		Route::get('emmacare-referal', [\App\Http\Controllers\EmmacareReferalTableController::class, 'index']);
		Route::get('emmacare_referal_table/ajax-list', [\App\Http\Controllers\EmmacareReferalTableController::class, 'ajaxList']);
		Route::get('emmacare_referal_table/export-csv', [\App\Http\Controllers\EmmacareReferalTableController::class, 'exportCsv']);

		Route::get('temp-download-url', [\App\Http\Controllers\DocumentSectionReportController::class, 'showAWSServiceLink']);

		Route::get('patient_md_order_list', [\App\Http\Controllers\MDOrderController::class, 'patientMDOrderList']);
		Route::get('patient_md_order_document_list', [\App\Http\Controllers\MDOrderController::class, 'mdOrderDocumentList']);
		Route::post('save-patient-md-order', [\App\Http\Controllers\MDOrderController::class, 'saveMDOrder']);
		Route::get('edit-patient-md-order', [\App\Http\Controllers\MDOrderController::class, 'edit']);
		Route::post('update-patient-md-order', [\App\Http\Controllers\MDOrderController::class, 'updateMDOrder']);
		Route::post('delete-patient-md-order', [\App\Http\Controllers\MDOrderController::class, 'deleteMDOrder']);


		Route::get('md-order-report', [\App\Http\Controllers\MDOrderReportController::class, 'index']);
		Route::get('md-order-report/ajax-list', [\App\Http\Controllers\MDOrderReportController::class, 'ajaxList']);
		Route::get('md-order-report/export-csv', [\App\Http\Controllers\MDOrderReportController::class, 'exportCsv']);

		Route::get('alaycare-client-data', [\App\Http\Controllers\AlayacareClientController::class, 'alaycareClientData']);
		Route::post('patient/update-alaycare-client-id', [\App\Http\Controllers\PatientController::class, 'updateAlaycareClientId']);
		Route::get('/ajax-all-discipline', [\App\Http\Controllers\MasterController::class, 'AjaxAllDiscipline']);

		Route::get('/sync-dashboard', [\App\Http\Controllers\HHACaregiversController::class, 'dashboard']);
		Route::get('/patient/document_details_by_id', [\App\Http\Controllers\PatientController::class, 'documentDetailsById']);
		Route::get('/document-review-by-id', [\App\Http\Controllers\DocumentSectionReportController::class, 'documentReview']);
		Route::post('/update-document-review', [\App\Http\Controllers\DocumentSectionReportController::class, 'updateDocumentReview']);

		// Start Rate Card Master
		Route::resource('rate-card', RateCardController::class);
		Route::get('/rate-card-list', [\App\Http\Controllers\RateCardController::class, 'rateCardList']);
		Route::get('/rate-card-by-id', [\App\Http\Controllers\RateCardController::class, 'rateCardById']);
		Route::get('/agency-rate-card-list', [\App\Http\Controllers\RateCardController::class, 'agencyWiseData']);
		// End Rate Card Master

		Route::get('/payment-data', [\App\Http\Controllers\PaymentLogController::class, 'getPaymentData']);
		Route::get('/payment-export-data', [\App\Http\Controllers\PaymentLogController::class, 'exportPaymentData']);
		Route::get('/payment-pay-data/{id}', [\App\Http\Controllers\PaymentLogController::class, 'getPaymentDataById']);
		Route::post('/edit-payment-data', [\App\Http\Controllers\PaymentLogController::class, 'editPaymentData']);
		Route::post('/add-payment-data', [\App\Http\Controllers\PaymentLogController::class, 'addPaymentData']);
		Route::get('/genrate-payment-amount-details', [\App\Http\Controllers\PaymentLogController::class, 'genratePaymentDetails']);
		Route::get('/genrate-payment-history', [\App\Http\Controllers\PaymentLogController::class, 'genratePaymentHistroy']);
		Route::get('/get-services', [\App\Http\Controllers\PaymentLogController::class, 'getServices']);

		Route::get('payment-log-report', [\App\Http\Controllers\PaymentLogReportController::class, 'index']);
		Route::get('payment-log-report/ajax-list', [\App\Http\Controllers\PaymentLogReportController::class, 'ajaxList']);
		Route::get('payment-log-report/export-csv', [\App\Http\Controllers\PaymentLogReportController::class, 'exportCsv']);

		/*********Start Billing Dashboard New Design ************/
		Route::get('payment-dashboard', [\App\Http\Controllers\PaymentDashboardController::class, 'index']);
		Route::get('get-count-data', [\App\Http\Controllers\PaymentDashboardController::class, 'getCountData']);
		Route::get('location-wise-payment-data', [\App\Http\Controllers\PaymentDashboardController::class, 'locationWiseData']);
		Route::get('agency-wise-payment-data', [\App\Http\Controllers\PaymentDashboardController::class, 'agencyWiseData']);
		Route::get('service-wise-payment-data', [\App\Http\Controllers\PaymentDashboardController::class, 'servicesWiseData']);
		Route::get('payment-chart-data', [\App\Http\Controllers\PaymentDashboardController::class, 'paymentTypeWiseChartData']);
		Route::get('monthly-payment-chart', [\App\Http\Controllers\PaymentDashboardController::class, 'monthlyPaymentChartData']);
		/*********End Billing Dashboard New Design ************/


		Route::get('remaining-sync-caregiver-report', [\App\Http\Controllers\HHACaregiversController::class, 'hhaTrackerReport']);
		Route::get('ajax-sync-remaining-caregiver', [\App\Http\Controllers\HHACaregiversController::class, 'ajaxSyncRemainingCaregiver']);
		Route::post('send-document-arla', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'sendDocumentArlaPlatForm']);

		/* Start agency User Wise role*/
		Route::post('agency-user-wise-role', [\App\Http\Controllers\AgencySettingController::class, 'assignUserRole']);
		Route::get('agency-wise-user', [\App\Http\Controllers\AgencySettingController::class, 'agencyWiseUserList']);
		Route::get('agency-user-export', [\App\Http\Controllers\AgencySettingController::class, 'agencyWiseUserExport']);
		Route::get('agency-user-view/{id}', [\App\Http\Controllers\AgencySettingController::class, 'agencyUserView']);
		Route::get('agency-user-delete', [\App\Http\Controllers\AgencySettingController::class, 'agencyUserDelete']);
		Route::get('agency-user-details/{id}', [\App\Http\Controllers\AgencySettingController::class, 'getAgencyUserDetail']);
		Route::get('agency-user-notification-list', [\App\Http\Controllers\AgencySettingController::class, 'agencyUserWiseNotificationList']);
		Route::post('agency-email-notification-email-save', [\App\Http\Controllers\AgencySettingController::class, 'saveEmailNotification']);
		Route::get('agency-email-notification-email-delete', [\App\Http\Controllers\AgencySettingController::class, 'deleteNotificationEmailAgency']);
		Route::get('agency-setting', [\App\Http\Controllers\AgencySettingController::class, 'agencySetting']);

		/* End agency User Wise role*/

		/*************Start Document Pending Report */
		Route::get('pending-document-report', [\App\Http\Controllers\DocumentSectionReportController::class, 'pendingDocumentReport']);
		Route::get('pending-document-ajax-list', [\App\Http\Controllers\DocumentSectionReportController::class, 'pendingDocumentAjaxList']);
		Route::get('pending-document-export-csv', [\App\Http\Controllers\DocumentSectionReportController::class, 'exportCsv']);
		Route::get('pending-document-export-csv-new', [\App\Http\Controllers\DocumentSectionReportController::class, 'exportCsvNWithoutServices']);
		/*************End Document Pending Report */

		//Extract Pdf Text
		Route::get('/extract-text', [\App\Http\Controllers\PdfController::class, 'index']);
		Route::post('/extract-pdf-text', [\App\Http\Controllers\PdfController::class, 'extractText']);

		/*********Start Diagonosis Medical History ************/
		Route::get('patient/diagnosis', [\App\Http\Controllers\DiagnosisController::class, 'index']);
		Route::post('patient/diagnosis-predict', [\App\Http\Controllers\DiagnosisController::class, 'predict']);
		Route::post('patient/diagnosis-health-predict', [\App\Http\Controllers\DiagnosisController::class, 'predictDiagnosisHealth']);
		Route::post('patient/diagnosis-test-predict', [\App\Http\Controllers\DiagnosisController::class, 'predictDiagnosisHealthTest']);
		/*********End Diagonosis Dashboard New Design ************/

		Route::post('send-e-fax-document', [\App\Http\Controllers\PatientDocumentController::class, 'sendEFaxDocument']);

		Route::get('efax-report', [\App\Http\Controllers\EfaxReportController::class, 'index']);
		Route::get('efax-ajax-list', [\App\Http\Controllers\EfaxReportController::class, 'efexAjaxList']);
		Route::get('efax-export-csv', [\App\Http\Controllers\EfaxReportController::class, 'exportCsv']);
		Route::post('user-directory-status-change', [\App\Http\Controllers\UserController::class, 'changeDirectoryViewStatus']);

		Route::post('patient/diagnosis-clinical-notes', [\App\Http\Controllers\DiagnosisController::class, 'predictClinicalNotes']);
		Route::get('fetch-refused-status', [\App\Http\Controllers\PatientController::class, 'fetchRefusedStatus']);
		Route::post('send_document_caresphere', [\App\Http\Controllers\PatientDocumentController::class, 'sendDocumentCaresphere']);

		Route::get('search-alayacare-clients', [\App\Http\Controllers\AlayacareClientController::class, 'searchAlayacareClients']);
		Route::get('search-alayacare-employee', [\App\Http\Controllers\AlaycareEmpController::class, 'searchAlayacareEmployee']);
		Route::get('get-alayacare-emp-details', [\App\Http\Controllers\AlaycareEmpController::class, 'getAlayacareEmployeeDetail']);

		/* Telehealth Location Window master module */
		Route::get('/telehealth-location-schedule/{id}', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'index']);
		Route::get('/telehealth-schedule-ajax', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'telehealthLocationAjaxList']);
		Route::post('/telehealth-location-schedule/save', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'save']);
		Route::get('/telehealth-location-schedule/edit/{id}', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'edit']);
		Route::post('/telehealth-location-schedule/update', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'update']);
		Route::get('/telehealth-location-schedule/delete/{id}', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'delete']);
		Route::get('/telehealth-schedule-view-logs', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'locationWiseScheduleLogs']);
		Route::get('/schedule-status-change', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'scheduleEnabledDisabled']);
		Route::get('/manage-telehealth-location', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'manageTelehealthLocation']);
		Route::post('/get-location-schedules', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'getLocationSchedules']);
		Route::post('/telehealth-location-schedule-ajax', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'manageTelehealthLocationAjaxList']);
		Route::post('/save-selected-events', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'saveSelectedEvents']);
		Route::get('check-nurse-schedule/{locationId}/{nurseId}/{scheduleId}', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'checkNurseSchedule']);
		Route::post('update-nurse-schedule', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'updateNurseSchedule']);

		Route::get('search-alayacare-employee', [\App\Http\Controllers\AlaycareEmpController::class, 'searchAlayacareEmployee']);
		Route::get('search-alayacare-clients', [\App\Http\Controllers\AlayacareClientController::class, 'searchAlayacareClients']);

		Route::get('/telehealth-schedule-manage', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'teleHealthMange']);
		Route::post('/save-time-frame-hours', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'saveTimeFrameHours']);
		Route::get('/get-location-type-wise', [\App\Http\Controllers\TelehealthLocationScheduleController::class, 'getLocationTypeWise']);
		Route::post('check-nurse-schedule', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'checkNurseScheduleByDate']);
		Route::post('update-nurse-schedule-by-date', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'updateNurseScheduleByDate']);

		Route::get('get-tele-appointment-data', [\App\Http\Controllers\DashboardAppointmentCalanderController::class, 'getTeleAppointmentData']);
		Route::get('get-monthly-tele-appoitment-details', [\App\Http\Controllers\DashboardAppointmentCalanderController::class, 'getMonthlyTeleAppointmentDetails']);
		Route::post('patient/update-patient-notes', [\App\Http\Controllers\PatientController::class, 'updatePatientNote']);

		Route::resource('telehealth-services', TeleHealthServiceController::class);
		Route::get('agency-tele-service-list', [\App\Http\Controllers\TeleHealthServiceController::class, 'getTelehealthServiceList']);
		Route::get('agency-tele-service-by-id', [\App\Http\Controllers\TeleHealthServiceController::class, 'getTelehealthServiceListById']);

		Route::get('telehealth-book-report', [\App\Http\Controllers\TelehealthBookReportController::class, 'index']);
		Route::get('telehealth-book-report/ajax-list', [\App\Http\Controllers\TelehealthBookReportController::class, 'ajaxList']);
		Route::get('telehealth-book-report/export-csv', [\App\Http\Controllers\TelehealthBookReportController::class, 'exportCsv']);

		Route::get('get-service-of-doc-id', [\App\Http\Controllers\DocumentSectionReportController::class, 'getServicesOfDocs']);

		Route::post('save-bulk-assign-user', [\App\Http\Controllers\PatientController::class, 'saveBulkAssignUser']);

		Route::post('update-third-party-flag', [\App\Http\Controllers\ThirdPartyPatientMasterController::class, 'updateThirdPartyFlag']);
		Route::post('/save-location-schedule-slots', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'saveLocationScheduleSlots']);
		Route::post('/copy-location-schedule-slots', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'copyLocationScheduleSlots']);
		Route::get('/get-patient-telehealth-list', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'getPatientTelehealthList']);

		/* Hub Record start */
		Route::get('hub-record', [\App\Http\Controllers\HubRecordController::class, 'index']);
		Route::get('hub-record/ajax-list', [\App\Http\Controllers\HubRecordController::class, 'ajaxList']);
		Route::get('hub-record/csv-list', [\App\Http\Controllers\HubRecordController::class, 'exportToCsv']);
		Route::get('create-hub-record', [\App\Http\Controllers\HubRecordController::class, 'createHubRecord']);
		Route::post('hub-record/save', [\App\Http\Controllers\HubRecordController::class, 'save']);
		Route::get('hub-record/view/{id}', [\App\Http\Controllers\HubRecordController::class, 'view']);
		Route::post('hub-record/save-basic-details', [\App\Http\Controllers\HubRecordController::class, 'saveBasicDetails']);
		Route::post('hub-record/save-address-details', [\App\Http\Controllers\HubRecordController::class, 'saveAddressDetails']);
		Route::post('update-hub-mobile', [\App\Http\Controllers\HubRecordController::class, 'updateMobileNumber']);
		Route::post('update-hub-phone', [\App\Http\Controllers\HubRecordController::class, 'updatePhoneNumber']);
		Route::post('update-hub-langauge', [\App\Http\Controllers\HubRecordController::class, 'updateLanguage']);
		Route::post('save-hub-notes/{id}', [\App\Http\Controllers\HubRecordController::class, 'saveHubNotes']);
		Route::get('get-hub-notes/{id}', [\App\Http\Controllers\HubRecordController::class, 'hubRecordNotesData']);
		Route::get('get-hub-document/{id}', [\App\Http\Controllers\HubRecordController::class, 'hubRecordDocData']);
		Route::post('save-hub-document-data/{id}', [\App\Http\Controllers\HubRecordController::class, 'saveHubRecordDocData']);
		Route::post('delete-hub-document-data/{id}/{doc_id}', [\App\Http\Controllers\HubRecordController::class, 'hubDocumentDelete']);

		Route::get('hub-record/get-sms-text', [\App\Http\Controllers\HubRecordController::class, 'getMessageList']);
		Route::post('hub-record/text-message-notes', [\App\Http\Controllers\HubRecordController::class, 'smsTextMessage']);
		Route::get('hub-record/delete/{id}', [\App\Http\Controllers\HubRecordController::class, 'delete']);

		Route::get('view-hub-doc/{id}', [\App\Http\Controllers\HubRecordController::class, 'showImage']);
		Route::get('/hub-view-pdf-response', [\App\Http\Controllers\HubRecordController::class, 'viewPdfDoc']);

		Route::post('save-hub-nybest-data/{id}', [\App\Http\Controllers\HubRecordController::class, 'saveHubNybestData']);

		Route::get('/get-nybest-agency', [\App\Http\Controllers\HubRecordController::class, 'nyBestAgency']);
		Route::get('/hub-nybest-list', [\App\Http\Controllers\HubRecordController::class, 'nyBestList']);

		Route::post('update-hub-status', [\App\Http\Controllers\HubRecordController::class, 'updateStatus']);
		Route::post('update-bulk-hub-status', [\App\Http\Controllers\HubRecordController::class, 'updateBulkStatus']);

		// Clinical routes
		Route::get('get-clinical-html/{type}', [\App\Http\Controllers\HubRecordController::class, 'getClinicalHtml']);
		Route::post('save-clinical-pdf', [\App\Http\Controllers\HubRecordController::class, 'saveClinicalPdf']);
		Route::get('get-clinical-records/{id}', [\App\Http\Controllers\HubRecordController::class, 'getClinicalRecords']);
		Route::get('download-clinical-pdf/{id}', [\App\Http\Controllers\HubRecordController::class, 'downloadClinicalPdf']);
		Route::delete('delete-clinical-record/{id}', [\App\Http\Controllers\HubRecordController::class, 'deleteClinicalRecord']);
		Route::get('generate-clinical-pdf/{id}', [\App\Http\Controllers\HubRecordController::class, 'generatePdfDownload']);

		/* Hub Record end */
		Route::get('hub-flag-change-task-status', [\App\Http\Controllers\HubFlagController::class, 'changeTaskStatusFlag']);
		Route::get('hub-flag-change-status', [\App\Http\Controllers\HubFlagController::class, 'changeStatusFlag']);
		Route::get('hub-flag-list', [\App\Http\Controllers\HubFlagController::class, 'flagList']);
		Route::get('flag-hub-ajax-list', [\App\Http\Controllers\HubFlagController::class, 'flagAppointmentAjaxList']);
		Route::get('hub-flag-change-notes-status', [\App\Http\Controllers\HubFlagController::class, 'changeNotesStatusFlag']);
		Route::get('hub-flag-change-document-status', [\App\Http\Controllers\HubFlagController::class, 'changeDocStatusFlag']);
		Route::get('hub-flag-doc-ajax-list', [\App\Http\Controllers\HubFlagController::class, 'flagDocAjaxList']);
		Route::get('hub-flag-notes-ajax-list', [\App\Http\Controllers\HubFlagController::class, 'flagNotesAjaxList']);
		Route::post('hub-mark-flag-read', [\App\Http\Controllers\HubFlagController::class, 'flagMarkAsRead']);
		Route::get('hub-flag-task-ajax-list', [\App\Http\Controllers\HubFlagController::class, 'flagTaskAjaxList']);

		Route::resource('hub-record/task-record', App\Http\Controllers\HubTaskController::class);
		Route::get('hub-record/task-list', [\App\Http\Controllers\HubTaskController::class, 'HubTaskList']);
		Route::post('hub-record/task-add', [\App\Http\Controllers\HubTaskController::class, 'HubTaskAdd']);
		Route::get('hub-task-list-ajax', [\App\Http\Controllers\HubTaskController::class, 'TaskListAjax']);
		Route::get('hub-task/activity-log-list', [\App\Http\Controllers\HubTaskController::class, 'ActivityLogList']);
		Route::get('hub-task-comment-list', [\App\Http\Controllers\HubTaskController::class, 'taskCommentList']);
		Route::get('hub-task-assign-to-user', [\App\Http\Controllers\HubTaskController::class, 'taskAssignToUser']);
		Route::get('hub-record/task-time-log-list', [\App\Http\Controllers\HubTaskController::class, 'hubRecordTaskTimeLogList']);
		Route::get('hub-task-change-status', [\App\Http\Controllers\HubTaskController::class, 'changeStatus']);
		Route::post('hub-task-priority-update', [\App\Http\Controllers\HubTaskController::class, 'taskPriorityUpdate']);
		Route::post('hub-task-comment-save', [\App\Http\Controllers\HubTaskController::class, 'taskCommentSave']);
		Route::post('hub-task-due-date', [\App\Http\Controllers\HubTaskController::class, 'taskDueDateUpdate']);
		Route::post('hub-task-title-update', [\App\Http\Controllers\HubTaskController::class, 'taskTitleUpdate']);
		Route::post('hub-task-discription-update', [\App\Http\Controllers\HubTaskController::class, 'TaskDiscriptionUpdate']);
		Route::get('hub-record/task-clock-in-out', [\App\Http\Controllers\HubTaskController::class, 'PatientTaskClockInOut']);
		Route::get('hub-task-list-export', [\App\Http\Controllers\HubTaskController::class, 'export']);
		Route::post('hub-record/task-ajax-list', [\App\Http\Controllers\HubTaskController::class, 'taskListPageAjax']);
		// Hub Analytics Routes
		Route::get('hub-analytics', [\App\Http\Controllers\HubAnalyticsController::class, 'index']);
		Route::get('hub-analytics/refresh', [\App\Http\Controllers\HubAnalyticsController::class, 'refreshData']);
		Route::get('hub-analytics/export', [\App\Http\Controllers\HubAnalyticsController::class, 'export']);
		Route::get('hub-analytics/chart-data', [\App\Http\Controllers\HubAnalyticsController::class, 'getChartData']);

		Route::post('user-hub-status-change', [\App\Http\Controllers\UserController::class, 'changeHubStatus']);
		Route::post('user-hub-view-ssn', [\App\Http\Controllers\UserController::class, 'changeHubViewSSN']);

		Route::get('hub-record-csv', [\App\Http\Controllers\HubRecordController::class, 'hubRecordCsv']);
		Route::post('import-hub-record', [\App\Http\Controllers\HubRecordController::class, 'HubImports']);
		Route::get('hub-record/import-logs', [\App\Http\Controllers\HubRecordController::class, 'getImportLogs'])->name('hub-record.import-logs');
		Route::post('agency-hub-status-change', [\App\Http\Controllers\AgencyController::class, 'changeHubStatusUpdate']);

		Route::get('hub-get-basic-details', [\App\Http\Controllers\HubRecordController::class, 'getBasicDeatils']);
		Route::get('hub-record-view-logs', [\App\Http\Controllers\HubRecordController::class, 'hubRecordWiselogs']);
		Route::get('hub-record/send-dependent/{id}', [\App\Http\Controllers\HubRecordController::class, 'smsSendToDependent']);

		Route::controller(\App\Http\Controllers\HubRecordUtilizationController::class)->group(function () {
			Route::get('hub-utilization-report', 'hubUtilizationReport');
			Route::get('hub-uitlization', 'hubUitlization');
			Route::get('hub-uitlization/ajax-list', 'ajaxList');
			Route::get('hub-uitlization/import-logs', 'getImportLogs')->name('hub-record.import-logs');
			Route::post('import-hub-uitlization', 'hubImports');
			Route::get('hub-uitlization/csv-list', 'exportToCsv');
		});
		Route::get('hub-eligibility', [\App\Http\Controllers\HubRecordEligibilityController::class, 'hubRecordWiseEligibility']);

		/* Hub Record Report start */
		Route::get('hub-record-report', [\App\Http\Controllers\HubRecordReportController::class, 'index']);
		Route::get('hub-record-report/ajax-list', [\App\Http\Controllers\HubRecordReportController::class, 'ajaxList']);
		Route::get('hub-record-report/export-csv', [\App\Http\Controllers\HubRecordReportController::class, 'exportCsv']);
		/* Hub Record Report end */

		/* Hub Record Notes Report start */
		Route::get('hub-notes-report', [\App\Http\Controllers\HubRecordNotesReportController::class, 'index']);
		Route::get('hub-notes-report/ajax-list', [\App\Http\Controllers\HubRecordNotesReportController::class, 'ajaxList']);
		Route::get('hub-notes-report/export-csv', [\App\Http\Controllers\HubRecordNotesReportController::class, 'exportCsv']);
		/* Hub Record Report end */

		/* Hub Record Notes Report start */
		Route::get('hub-doc-report', [\App\Http\Controllers\HubRecordDocReportController::class, 'index']);
		Route::get('hub-doc-report/ajax-list', [\App\Http\Controllers\HubRecordDocReportController::class, 'ajaxList']);
		Route::get('hub-doc-report/export-csv', [\App\Http\Controllers\HubRecordDocReportController::class, 'exportCsv']);
		/* Hub Record Report end */
		Route::get('template-ajax-list', [\App\Http\Controllers\TempleteController::class, 'ajaxList']);

		Route::get('get-approved-user-doc', [\App\Http\Controllers\UserController::class, 'getPateintDocApprovedUser']);
		Route::get('document-dashboard', [\App\Http\Controllers\DocumentDashboardController::class, 'index']);
		Route::get('get-doc-count-data', [\App\Http\Controllers\DocumentDashboardController::class, 'getPateintDocData']);
		Route::get('get-doc-questions', [\App\Http\Controllers\DocumentDashboardController::class, 'getQuesionsData']);

		Route::get('update-document-name', [\App\Http\Controllers\DocumentSectionReportController::class, 'saveDocName']);
		Route::post('update-document-review-internal', [\App\Http\Controllers\DocumentSectionReportController::class, 'updateDocumentReviewInternal']);

		Route::post('bulk-appointments-delete', [\App\Http\Controllers\PatientController::class, 'bulkAppointmentDelete']);
		/* Audit Log Report start */
		Route::get('audit-log-report', [\App\Http\Controllers\AuditLogReportController::class, 'index']);
		Route::get('audit-log-report/ajax-list', [\App\Http\Controllers\AuditLogReportController::class, 'ajaxList']);
		/* Audit Log Report end */
		Route::get('get-audit-view-log', [\App\Http\Controllers\AuditLogReportController::class, 'getAuditviewLog']);

		Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index']);
		Route::get('reports-ajax-list', [\App\Http\Controllers\ReportController::class, 'ajaxList']);
		Route::post('patient-notes-delete', [\App\Http\Controllers\PatientController::class, 'noteDelete']);
		Route::post('unlink-hha-patient', [\App\Http\Controllers\PatientController::class, 'unlinkPatient']);
		Route::post('unlink-hha-caregiver', [\App\Http\Controllers\PatientController::class, 'unlinkCaregiver']);
		Route::get('get-document-internal-use-data', [\App\Http\Controllers\DocumentSectionReportController::class, 'getInternalUseData']);

		Route::get('check-hub-duplicate-data', [\App\Http\Controllers\HubRecordController::class, 'checkDuplicateRecord']);
		Route::get('get-hub-dependent-data', [\App\Http\Controllers\HubRecordController::class, 'getDependentData']);
		Route::post('hub-record/dependent-save', [\App\Http\Controllers\HubRecordController::class, 'saveHubDependentData']);
		Route::get('get-agency-other-data', [\App\Http\Controllers\HubRecordController::class, 'getAgencyOtherData']);
		Route::get('search-hub-record', [\App\Http\Controllers\HubRecordController::class, 'searchHubRecord']);
		Route::post('update-remaining-hub-details', [\App\Http\Controllers\HubRecordController::class, 'createHubDetails']);

		/*Hub Company Module Start*/
		Route::get('/hub-company', [\App\Http\Controllers\HubCompanyController::class, 'index']);
		Route::get('/hub-company/add', [\App\Http\Controllers\HubCompanyController::class, 'add']);
		Route::post('/hub-company/save', [\App\Http\Controllers\HubCompanyController::class, 'save']);
		Route::get('/hub-company/edit/{id}', [\App\Http\Controllers\HubCompanyController::class, 'edit']);
		Route::post('/hub-company/update/{id}', [\App\Http\Controllers\HubCompanyController::class, 'update']);
		Route::get('/hub-company/delete/{id}', [\App\Http\Controllers\HubCompanyController::class, 'delete']);
		Route::get('/hub-company-view/{id}', [\App\Http\Controllers\HubCompanyController::class, 'view']);
		Route::post('/hub-company/generate-token', [\App\Http\Controllers\HubCompanyController::class, 'hubGenerateToken']);
		Route::get('/hub-company/generate-token-list', [\App\Http\Controllers\HubCompanyController::class, 'hubGenerateTokenList']);
		Route::post('/update-agency-wise-hub-data', [\App\Http\Controllers\HubRecordController::class, 'updateAgencyWiseOtherDetails']);
		Route::post('update-referral-source', [\App\Http\Controllers\PatientController::class, 'updateReferralSource']);

		Route::controller(\App\Http\Controllers\ReferralSourceReportController::class)->group(function () {
			Route::get('referral-source-report', 'index');
			Route::get('referral-source-report-ajax', 'ajaxList');
			Route::get('referral-source-report-export', 'exportCsv');
		});

		// Route::controller(\App\Http\Controllers\ReferralsStatsAndAnalyticsController::class)->group(function () {
		// 	Route::get('referrals-weight', 'referralsWeight');
		// 	Route::get('service-count-ajax', 'serviceCountAjax');
		// 	Route::get('agency-count-ajax', 'agencyCountAjax');
		// 	Route::get('booking-count-ajax', 'bookingCountAjax');
		// 	Route::get('cancellations-count-ajax', 'cancellationsCountAjax');
		// 	Route::get('refusals-count-ajax', 'refusalsCountAjax');
		// 	Route::get('detailed-refusals-count-ajax', 'detailedRefusalsCountAjax');
		// 	Route::get('detailed-cancellations-count-ajax', 'detailedCancellationsCountAjax');
		// 	Route::get('unabletocontact-count-ajax', 'unabletocontactCountAjax');
		// 	Route::get('completed-count-ajax', 'completedCountAjax');
		// 	Route::get('status-count-ajax', 'statusCount');

		// 	Route::get('referrals-analytics-dashboard-report', 'referralsAnalyticsDashboard');
		// 	Route::get('/referrals-analytics-ajax', 'referralsAnalyticsAjaxList');

		// 	Route::get('weekly-monthly-states', 'weeklyMonthlyStates');
		// 	Route::get('/weekly-monthly-states-ajax', 'weeklyMonthlyStatesAjaxList');

		// 	// Route::get('detailed-refusals-report', 'detailedRefusals');
		// 	Route::get('/detailed-refusals-graph-ajax', 'graphAjax');
		// });

		// Daily Referral Email Automation Routes
		Route::controller(\App\Http\Controllers\DailyReferralEmailController::class)->group(function () {
			Route::get('daily-referral-email', 'index')->name('daily-referral-email.index');
			Route::post('daily-referral-email/preview', 'preview')->name('daily-referral-email.preview');
			Route::post('daily-referral-email/send', 'sendEmail')->name('daily-referral-email.send');
			Route::get('daily-referral-email/history', 'history')->name('daily-referral-email.history');
			Route::get('daily-referral-email/view/{id}', 'viewEmailLog')->name('daily-referral-email.view');
			Route::post('daily-referral-email/resend/{id}', 'resendEmail')->name('daily-referral-email.resend');

			// Scheduling routes
			Route::post('daily-referral-email/schedule', 'scheduleDaily')->name('daily-referral-email.schedule');
			Route::get('daily-referral-email/schedules', 'getSchedules')->name('daily-referral-email.schedules');
			Route::get('daily-referral-email/schedule/{id}', 'getSchedule')->name('daily-referral-email.schedule.show');
			Route::put('daily-referral-email/schedule/{id}', 'updateSchedule')->name('daily-referral-email.schedule.update');
			Route::delete('daily-referral-email/schedule/{id}', 'deleteSchedule')->name('daily-referral-email.schedule.delete');
			Route::post('daily-referral-email/schedule/{id}/toggle', 'toggleSchedule')->name('daily-referral-email.schedule.toggle');
			Route::post('daily-referral-email/schedule/{id}/test', 'testSchedule')->name('daily-referral-email.schedule.test');
		});

		Route::controller(\App\Http\Controllers\ResolutionController::class)->group(function () {
			Route::post('save-resolution-data', 'saveResolutionData');
			Route::get('get-resolution-data', 'getResolutionData');
		});

		Route::controller(\App\Http\Controllers\ResolutionLogReportController::class)->group(function () {
			Route::get('resolution-log-report', 'index');
			Route::get('resolution-log-report-ajax', 'ajaxList');
			Route::get('resolution-log-report-export', 'exportCsv');
		});

		// Deleted Patient Management Routes
		Route::controller(\App\Http\Controllers\DeletedPatientManagementController::class)->group(function () {
			Route::get('deleted-patient-management', 'index');
			Route::get('deleted-patient-ajax-list', 'ajaxList');
			Route::post('deleted-patient-reactivate', 'reactivatePatient');
		});

		Route::post('/update-nybest-user-data', [\App\Http\Controllers\AgencyController::class, 'assignNybestUserToAgency']);
		Route::get('/get-service-requested', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'resolutionAjaxServiceRequested']);
		Route::post('/save-pateint-service-requested', [\App\Http\Controllers\ResolutionController::class, 'resolutionSaveServiceRequested']);
		Route::get('dpp-text-message', [\App\Http\Controllers\DownloadController::class, 'downloadTextMessageImage']);

		// Agency Activity Feed
		Route::get('my-dashboard', [\App\Http\Controllers\MyDashboardController::class, 'index']);
		Route::get('get-activity-feed-data', [\App\Http\Controllers\MyDashboardController::class, 'getActivityFeedData']);
		Route::get('get-activity-feed-user-data', [\App\Http\Controllers\MyDashboardController::class, 'getActivityFeedUserData']);
		Route::get('get-last-status-not-updated-data', [\App\Http\Controllers\MyDashboardController::class, 'lastStatusUpdatedData']);

		Route::post('creator-email-noti-toggle', [\App\Http\Controllers\UserController::class, 'creatorEmailNotiToggle']);
		Route::post('user-telehealth-toggle', [\App\Http\Controllers\UserController::class, 'userTelehealthToggle']);
		Route::post('user-mdo-toggle', [\App\Http\Controllers\UserController::class, 'userMdoToggle']);
		Route::post('user-template-type-update', [\App\Http\Controllers\UserController::class, 'userTemplateTypeUpdate']);
		Route::prefix('service-master')->controller(\App\Http\Controllers\MasterServiceController::class)->group(function ($uRoute) {
			$uRoute->get('/', 'index');
			$uRoute->get('/ajax-list', 'ajaxList');
			$uRoute->post('/save', 'save');
			$uRoute->get('edit/{id}', 'edit');
			$uRoute->post('update', 'update');
			$uRoute->post('delete', 'delete');
			$uRoute->post('enabled-service', 'enabledService');
			$uRoute->post('enabled-nybest-user', 'enabledNyBestUser');
		});

		Route::prefix('bulk-sms-cdpap-caregiver')->controller(\App\Http\Controllers\BulkSMSCdpapCaregiverController::class)->group(function ($bRoute) {
			$bRoute->get('/', 'index');
			$bRoute->get('/ajax-list', 'ajaxList');
			$bRoute->post('save-bulk-sms', 'save');
			$bRoute->get('/view-detail/{id}', 'viewDetails');
			$bRoute->get('/view-ajax-list', 'viewDetailsAjaxList');
		});

		Route::prefix('date-wise-agency-access')->controller(\App\Http\Controllers\DateWiseAgencyAccessController::class)->group(function ($dtcRoute) {
			$dtcRoute->get('/load-date-wise-agency-access-list','agencyWiseDateAccessList');
			$dtcRoute->post('save-date-view-agency-view','save');
			$dtcRoute->get('edit-date-view-agency-view','edit');
			$dtcRoute->post('update-date-view-agency-view','update');
			$dtcRoute->post('delete-date-view-agency-view','delete');
			$dtcRoute->get('/load-date-wise-user-access-list','userWiseDateAccessList');
			$dtcRoute->post('save-date-view-user-view','saveUserDateAccess');
			$dtcRoute->get('edit-date-view-user-view','editUserDateAccess');
			$dtcRoute->post('update-date-view-user-view','updateUserDateAccess');
			$dtcRoute->post('delete-date-view-user-view','deleteUserDateAccess');
			$dtcRoute->post('check-existing-entries-user','checkExistingEntriesUser');
			$dtcRoute->post('set-permanent-restriction-user','setPermanentRestrictionUser');
			$dtcRoute->post('remove-permanent-restriction-user','removePermanentRestrictionUser');
		});
		Route::prefix('book-appointment-report')->controller(\App\Http\Controllers\BookAppointmentReportController::class)->group(function ($barRoute) {
			$barRoute->get('/', 'index');
			$barRoute->get('/ajax-list', 'ajaxList');
			$barRoute->get('/export-csv', 'exportCsv');
		});


		Route::get('get-hha-caregiver-medical-list', [\App\Http\Controllers\HHACaregiversController::class, 'getHHACaregiverMeddicalList']);
		Route::post('save-hha-caregiver-medical', [\App\Http\Controllers\HHACaregiversController::class, 'saveHHACaregiverMedical']);
		Route::get('get-caregiver-i9-abc-document', [\App\Http\Controllers\HHACaregiversController::class, 'getI9ABCDocument']);
		Route::post('update-caregiver-i9-requirement', [\App\Http\Controllers\HHACaregiversController::class, 'updateCaregiverI9Requirement']);
		Route::get('get-caregiver-i9-requirement-detail', [\App\Http\Controllers\HHACaregiversController::class, 'getCaregiverComplianceI9Details']);

		Route::post('patient/unmerge-appointment', [\App\Http\Controllers\PatientController::class, 'unMergeAppointment']);


		Route::post('agency-payment-report-url', [\App\Http\Controllers\AgencyController::class, 'changePaymentTypeReport']);
		Route::post('agency-reporting-tool-url', [\App\Http\Controllers\AgencyController::class, 'changeReportingToolStatus']);
		Route::get('custom-esign-html', [\App\Http\Controllers\CustomVNSEsignController::class, 'index']);
		Route::post('save_response_data_vns', [\App\Http\Controllers\CustomVNSEsignController::class, 'saveData']);
		Route::get('temp-regenerate-custom', [\App\Http\Controllers\CustomVNSEsignController::class, 'tempRegeneratePDF']);
		// ===================== VNS Procedure Routes =====================
		Route::controller(VNSProcedureController::class)
			->prefix('vns-procedure')
			->name('vns-procedure.')
			->group(function () {
				Route::get('data/list', 'getData')->name('data');
				Route::post('save', 'save')->name('save');
				Route::get('edit/{id}', 'edit')->name('edit');
				Route::post('update', 'updateAjax')->name('update');
				Route::get('export-csv', 'exportCSV')->name('export-csv');
				Route::resource('/', VNSProcedureController::class)->parameters(['' => 'vns_procedure']);
			});

		// ===================== VNS Question Routes =====================
		Route::controller(VNSQuestionController::class)
			->prefix('vns-question')
			->name('vns-question.')
			->group(function () {
				Route::get('data/list', 'getData')->name('data');
				Route::post('save', 'save')->name('save');
				Route::get('edit/{id}', 'edit')->name('edit');
				Route::post('update', 'updateAjax')->name('update');
				Route::get('export-csv', 'exportCSV')->name('export-csv');
				Route::resource('/', VNSQuestionController::class)->parameters(['' => 'vns_question']);
			});

		// ===================== VNS Procedure Result Routes =====================
		Route::controller(VNSProcedureResultController::class)
			->prefix('vns-procedure-result')
			->name('vns-procedure-result.')
			->group(function () {
				Route::get('data/list', 'getData')->name('data');
				Route::post('save', 'save')->name('save');
				Route::get('edit/{id}', 'edit')->name('edit');
				Route::post('update', 'updateAjax')->name('update');
				Route::get('export-csv', 'exportCSV')->name('export-csv');
				Route::get('by-procedure', 'byProcedure')->name('by-procedure');
				Route::resource('/', VNSProcedureResultController::class)->parameters(['' => 'vns_procedure_result']);
			});

		// ===================== VNS Social History Routes =====================
		Route::controller(VNSSocialHistoryController::class)
			->prefix('vns-social-history')
			->name('vns-social-history.')
			->group(function () {
				Route::get('data/list', 'getData')->name('data');
				Route::post('save', 'save')->name('save');
				Route::get('edit/{id}', 'edit')->name('edit');
				Route::post('update', 'updateAjax')->name('update');
				Route::get('export-csv', 'exportCSV')->name('export-csv');
				Route::get('by-template', 'byTemplate')->name('by-template');
				Route::resource('/', VNSSocialHistoryController::class)->parameters(['' => 'vns_social_history']);
			});

		// Patient Agency Merge Routes
		Route::controller(\App\Http\Controllers\AgencyMergeController::class)->prefix('patient-agency-merge')->name('patient-agency-merge.')->group(function(){
			Route::get('/index', [\App\Http\Controllers\AgencyMergeController::class, 'patientAgencyMergeIndex']);
			Route::get('/process-agency-merge', [\App\Http\Controllers\ProcessAgencyMergeController::class, 'process']);
			Route::post('/update', [\App\Http\Controllers\AgencyMergeController::class, 'patientAgencyMergeUpdate']);
			Route::get('/ajax-list', [\App\Http\Controllers\AgencyMergeController::class, 'patientAgencyMergeAjax']);
			Route::post('/sync', [\App\Http\Controllers\AgencyMergeController::class, 'syncAgencyMergeData']);
		});
		require __DIR__ . '/web_rnpad.php';
		Route::get('/ajax-service-with-json', [\App\Http\Controllers\MasterController::class, 'ajaxServiceWithJson']);

		Route::post('user-restrict', [\App\Http\Controllers\UserController::class, 'userRestrict']);

		Route::get('patient/merge-appointment-list', [\App\Http\Controllers\MergeAppointmentController::class, 'mergeAppointmentList']);
		Route::post('patient/new-unmerge-appointment', [\App\Http\Controllers\MergeAppointmentController::class, 'unMergeAppointment']);
		Route::get('patient/delete-merge-appointment-list', [\App\Http\Controllers\MergeAppointmentController::class, 'mergeDeletedAppointmentList']);

		Route::get('patient/appointment-list',[\App\Http\Controllers\PatientController::class, 'loadApointmentList']);
		// ZIP code
		Route::resource('setting/zipcode', App\Http\Controllers\ZipCodeController::class);
		Route::get('setting/zipcode-master/ajax-list', [\App\Http\Controllers\ZipCodeController::class,'ajaxlist']);
		Route::post('setting/zipcode-master/status-update', [\App\Http\Controllers\ZipCodeController::class,'changeStatus']);

		Route::get('get-third-party-data',[\App\Http\Controllers\ThirdPartyPatientMasterController::class,'getThirdPartyData']);
		Route::post('save-third-party-doc-data',[\App\Http\Controllers\ThirdPartyPatientMasterController::class,'saveThirdPartyDocumentData']);

		// Branch List (Master) Routes
		Route::controller(\App\Http\Controllers\BranchListController::class)
			->group(function () {
				Route::resource('branch-master', \App\Http\Controllers\BranchListController::class);
				Route::get('branch/ajax-list', 'ajaxList');
				Route::post('branch/status-update', 'changeStatus');
				Route::get('get-active-branches', 'activeBranchList');
				Route::post('patient/save-patient-branch', 'saveBranch');
				Route::get('get-branches', 'branchList');
			});

		// Branch List Link Routes
		Route::controller(\App\Http\Controllers\BranchListLinkController::class)
			->group(function () {
				Route::resource('branch-link', \App\Http\Controllers\BranchListLinkController::class);
				Route::get('branch-link-ajax/ajax-list', 'ajaxList');
				Route::get('branch-link-ajax/get-branches-by-agency-services', 'getBranchesByAgencyServices');
				Route::post('branch-link-ajax/change-mandatory', 'changeMandatoryOption');
				Route::get('branch-link-ajax/check-mandatory', 'checkMandatory');
			});

		Route::get('/search-all-users', [\App\Http\Controllers\UserController::class, 'searchAllUserData']);

		Route::get('/patient/send-document-inflowcare', [\App\Http\Controllers\PatientController::class, 'sendToInflowcare']);
	});


	Route::post('/patient/appointment-save', [\App\Http\Controllers\PatientController::class, 'AppointmentsSave']);

	Route::get('/thank-you', [\App\Http\Controllers\PatientController::class, 'nyThankyou']);
	Route::post('/patient/appointment-update', [\App\Http\Controllers\PatientController::class, 'AppointmentsUpdate']);


	Route::get('/location-schedule-search1', [\App\Http\Controllers\LocationScheduleController::class, 'SearchByLocationIdAndDate']);
	Route::get('/clear-cache', function () {

		Artisan::call('config:clear');
		Artisan::call('cache:clear');
		Artisan::call('route:clear');
		return "Cache is cleared";
	});

	Route::get('/test', [\App\Http\Controllers\TestController::class, 'indexb']);
	Route::get('/vishaltest', [\App\Http\Controllers\HHAMedicalDueController::class, 'index']);
	Route::get('/david-import-csv', [\App\Http\Controllers\HHAMedicalDueController::class, 'Importcsv']);
	Route::get('/hha-details', [\App\Http\Controllers\HHAMedicalDueController::class, 'getHHADetails']);
	Route::get('/hha-add-patient-details', [\App\Http\Controllers\HHAMedicalDueController::class, 'AddPatientDetails']);

	Route::get('formRecord', [\App\Http\Controllers\TestController::class, 'formRecord']);
	Route::post('test-record-form', [\App\Http\Controllers\TestController::class, 'updateformRecord']);
	Route::get('patient-sign/{id}/{id1}', [\App\Http\Controllers\PatientController::class, 'documentView']);
	Route::post('/patient-document-Insert-View', [\App\Http\Controllers\PatientController::class, 'documentInsertView']);


	Route::get('/task-outstanding-mail', [\App\Http\Controllers\CronjobController::class, 'sentMailOutstandingTask']);
	Route::get('/hha-caregiver-list', [\App\Http\Controllers\AgencyWiseHHACaregiverController::class, 'index']);
	Route::get('/hha-caregiver-ajax', [\App\Http\Controllers\AgencyWiseHHACaregiverController::class, 'ajaxList']);

	Route::get('/caregiver-list', [\App\Http\Controllers\HHACaregiversController::class, 'caregiverList']);
	Route::get('/caregiver-medical-list', [\App\Http\Controllers\HHACaregiversController::class, 'caregiverMedicalDetailsByCaregiverId']);
	Route::get('/caregiver-sync', [\App\Http\Controllers\HHACaregiversController::class, 'caregiverSync']);
	Route::get('/ap/{id}', [\App\Http\Controllers\PatientController::class, 'patientAppointments']);
	Route::get('/ajax-all-service', [\App\Http\Controllers\MasterController::class, 'AjaxAllService']);
	Route::get('/sync-caregiver/{id}', [\App\Http\Controllers\CronJobNewController::class, 'syncCaaregiver']);

	Route::get('/opt-in-out', [\App\Http\Controllers\OptInOutController::class, 'index']);
	Route::post('/opt-in-out-post', [\App\Http\Controllers\OptInOutController::class, 'store']);
	Route::get('/hha-patient-caregiver-details', [\App\Http\Controllers\PatientController::class, 'HHAPatientCaregiverDetails']);

	Route::get('hha-office', [\App\Http\Controllers\HHAOfficeController::class, 'syncOffice']);
	Route::get('hha-combile-update-code', [\App\Http\Controllers\HHAOfficeController::class, 'combineUpdateCode']);

	Route::get('hha-patient-contract', [\App\Http\Controllers\HHAPatientController::class, 'getPatientContract']);
	Route::get('hha-patient-discipline', [\App\Http\Controllers\HHAPatientController::class, 'getPatientDiscipline']);
	Route::get('hha-patient-prefrences', [\App\Http\Controllers\HHAPatientController::class, 'getPatientPrefrences']);
	Route::get('hha-patient-download-doc', [\App\Http\Controllers\HHAPatientController::class, 'getDownloadDocument']);
	Route::get('hha-caregiver-download-doc', [\App\Http\Controllers\HHACaregiversController::class, 'getDownloadDocument']);
	Route::get('hha-caregiver-prefrences', [\App\Http\Controllers\HHACaregiversController::class, 'getCaregiverPrefrences']);

	/*************Added */
	Route::get('docusign/viewNew/{id}', [\App\Http\Controllers\RedirectionEsignController::class, 'ViewDocusignNew']);
	Route::get('docusign/view/{id}', [\App\Http\Controllers\RedirectionEsignController::class, 'ViewDocusign']);
	Route::get('nye/{id}', [\App\Http\Controllers\RedirectionEsignController::class, 'emailSignShow']);
	Route::get('feedback-form', [\App\Http\Controllers\UserFeedbackFormController::class, 'feedbackForm']);
	Route::post('feedback-form-store', [\App\Http\Controllers\UserFeedbackFormController::class, 'feedbackFormStore']);
	Route::get('/feedback-thank-you', [\App\Http\Controllers\UserFeedbackFormController::class, 'feedbackThankyou']);
	Route::get('stmp/{id}', [\App\Http\Controllers\DownloadController::class, 'stampImages']);

	Route::get('/patient-edit-with-sms/{id}', [\App\Http\Controllers\PatientDemoGraphicController::class, 'patientEditWithSms']);
	Route::post('/patient-update-with-sms/{id}', [\App\Http\Controllers\PatientDemoGraphicController::class, 'patientUpdateWithSms']);
	Route::post('/get-countries', [\App\Http\Controllers\PatientDemoGraphicController::class, 'getCountyByZipCode']);
	Route::get('patient-details-search', [\App\Http\Controllers\PatientDemoGraphicController::class, 'search']);
	Route::get('patient-ajax-list-data', [\App\Http\Controllers\PatientDemoGraphicController::class, 'ajaxList']);
	Route::post('send-sms-patient-demographic', [\App\Http\Controllers\PatientDemoGraphicController::class, 'sendPatientDemogeraphicUpdate']);

	Route::get('demographic-link-expire', [\App\Http\Controllers\PatientDemoGraphicController::class, 'demographicLinkExpire']);
	Route::get('thank-you-demo', [\App\Http\Controllers\PatientDemoGraphicController::class, 'thankForDemo']);
	Route::get('patient-demographic-details/{id}', [\App\Http\Controllers\PatientDemoGraphicController::class, 'viewDetails']);
	Route::post('save-pdf-download', [\App\Http\Controllers\PatientDemoGraphicController::class, 'submitEsign']);
	Route::get('download-user-pdf', [\App\Http\Controllers\PatientDemoGraphicController::class, 'downloadPdf']);
	Route::get('save-dows', [\App\Http\Controllers\PatientDemoGraphicController::class, 'downs']);

	Route::get('load-term-condition', [\App\Http\Controllers\TermAndConditionController::class, 'loadTermAndCondition']);
	Route::post('save-term-condition', [\App\Http\Controllers\TermAndConditionController::class, 'saveTermCondition']);

	Route::get('review-feedback-form/{id}', [\App\Http\Controllers\ReviewFeedbackFormController::class, 'index']);
	Route::post('submit-feedback-form', [\App\Http\Controllers\ReviewFeedbackFormController::class, 'submitFeedback']);
	Route::get('last_status_update', [\App\Http\Controllers\TestingPurposeController::class, 'getListing']);
	Route::get('/tele-appointment/{id}', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'patientTeleAppointments']);
	Route::post('/get-patient-existing-appointment', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'getPatientExistingAppointment']);
	Route::post('/tele-appointment-update', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'teleAppointmentsUpdate']);
	Route::post('/get-time-slots', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'getTimeSlotsByLanguageAndDate']);
	Route::get('webhook-third-party', [\App\Http\Controllers\WebHookThirdPartyController::class, 'index']);
	Route::get('send-web-hook-data', [\App\Http\Controllers\WebHookThirdPartyController::class, 'sendWebHook']);
	Route::post('/get-telehealth-slots', [\App\Http\Controllers\TelehealthLocationScheduleEventController::class, 'getTelehealthSlots']);

	Route::post('/service-edit', [\App\Http\Controllers\PatientWiseServiceRequestController::class, 'editService']);
	Route::get('process-doc-medical-count',[\App\Http\Controllers\DocumentMedCountCronController::class, 'updateOldDocumentCounts']);

	// User Doc Approval Routes
	Route::resource('user-doc-approval', \App\Http\Controllers\UserDocApprovalController::class);
	Route::get('user-doc-approval-ajax-list', [\App\Http\Controllers\UserDocApprovalController::class, 'ajaxList']);

	// Announcement Master Routes (Admin)

	Route::resource('announcement-master', \App\Http\Controllers\AnnouncementMasterController::class);
	Route::get('announcement-master-ajax-list', [\App\Http\Controllers\AnnouncementMasterController::class, 'ajaxList']);
	Route::post('announcement-master-publish/{id}', [\App\Http\Controllers\AnnouncementMasterController::class, 'publish']);
	Route::delete('announcement-master-media/{id}', [\App\Http\Controllers\AnnouncementMasterController::class, 'deleteMedia']);
	Route::get('announcement-media-show/{id}', [\App\Http\Controllers\AnnouncementMasterController::class, 'showImage']);


	// Announcement User Routes (All Users)
	Route::get('announcement-list', [\App\Http\Controllers\AnnouncementUserController::class, 'announcementList']);
	Route::get('announcement-list-ajax', [\App\Http\Controllers\AnnouncementUserController::class, 'ajaxAnnouncementList']);
	Route::get('get-unread-announcements', [\App\Http\Controllers\AnnouncementUserController::class, 'getUnreadAnnouncements']);
	Route::get('get-unread-announcements-dropdown', [\App\Http\Controllers\AnnouncementUserController::class, 'getUnreadAnnouncementsDropdown']);
	Route::get('get-announcement-count', [\App\Http\Controllers\AnnouncementUserController::class, 'getUnreadCount']);
	Route::post('mark-announcement-as-read', [\App\Http\Controllers\AnnouncementUserController::class, 'markAsRead']);
	Route::get('get-unshown-announcements', [\App\Http\Controllers\AnnouncementUserController::class, 'getUnshownAnnouncements']);
	Route::post('mark-announcement-as-shown', [\App\Http\Controllers\AnnouncementUserController::class, 'markAsShown']);

	// App Token Routes
	Route::resource('app-tokens', AppTokenController::class);
	Route::get('/app-tokens-ajax-list', [AppTokenController::class, 'ajaxList']);
	Route::get('/app-tokens/{appToken}/json', [AppTokenController::class, 'json'])->name('app-tokens.json');

	// Lead Coordination Report Routes
	Route::get('/lead-coordination-report', [LeadCoordinationReportController::class, 'index'])->name('lead-coordination-report.index');
	Route::get('/lead-coordination-report/ajax-list', [LeadCoordinationReportController::class, 'ajaxList'])->name('lead-coordination-report.ajax-list');
	Route::get('/lead-coordination-report/export-csv', [LeadCoordinationReportController::class, 'exportCSV'])->name('lead-coordination-report.export-csv');

	Route::get('download-pdf',[\App\Http\Controllers\DownloadController::class,'sampleDownloadPDFFile']);

	Route::post('patient/assign-department',[DepartmentController::class,'savePortalAssignDept']);

	// Resolution SMS Template Routes
	Route::get('resolution-sms-template', [\App\Http\Controllers\ResolutionSmsTemplateController::class, 'index'])->name('resolution-sms-template.index');
	Route::post('resolution-sms-template/get-by-id', [\App\Http\Controllers\ResolutionSmsTemplateController::class, 'getById']);
	Route::post('resolution-sms-template/update', [\App\Http\Controllers\ResolutionSmsTemplateController::class, 'update']);
	Route::post('resolution-sms-template/bulk-update', [\App\Http\Controllers\ResolutionSmsTemplateController::class, 'bulkUpdate'])->name('resolution-sms-template.bulk-update');
	Route::post('patient-resolution-sms/send', [\App\Http\Controllers\ResolutionSmsTemplateController::class, 'sendSms'])->name('patient-resolution-sms.send');
	Route::post('patient-resolution-sms/resolve-message', [\App\Http\Controllers\ResolutionSmsTemplateController::class, 'resolveMessage'])->name('patient-resolution-sms.resolve-message');
});

Route::get('hub-authentication', function () {
	return view('hubRecord.login');
})->name('hub-authentication');

Route::controller(\App\Http\Controllers\HubAuthenticationController::class)->group(function () {
	Route::post('hub-authenticate', 'index')->name('hub-authenticate');
	Route::get('hub-otp-verification/{id}', 'hubOtpVerification')->name('hub-otp-verification');
	Route::post('hub-check-otp-valid', 'otpValid')->name('hub-check-otp-valid');
	Route::post('hub-verifyotp', 'verifyOtp')->name('hub-verifyotp');
	Route::get('hub-view-records/{id}', 'viewRecords')->name('hub-view-records');
	Route::get('hub-dependent-records/{id}', 'getDependentData');
	Route::post('hub-dependent-save', 'saveHubDependentData');
	Route::post('hub-dependent-update', 'updateHubDependentData');
});

Route::get('hub-record/add-dependent', [\App\Http\Controllers\HubRecordController::class, 'addDependent']);

require __DIR__ . '/web_hha.php';
require __DIR__ . '/web_emmacare.php';
// Invoice Module Routes
require __DIR__.'/web_payment_log.php';
require __DIR__.'/web_task.php';
require __DIR__.'/web_patient.php';
require __DIR__ . '/web_esign.php';
require __DIR__ . '/web_kiosk.php';
Route::get('imagick-temp-pdf', [\App\Http\Controllers\DownloadController::class, 'createImagicPdf']);
require __DIR__ . '/web_visiting_aid.php';
require __DIR__ . '/web_task_health.php';
require __DIR__ . '/web_agency_file_manager.php';
require __DIR__ . '/web_call_appointment.php';
require __DIR__ . '/web_alayacare.php';
