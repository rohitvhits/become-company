<?php



namespace App\Http\Middleware;



use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;



class VerifyCsrfToken extends Middleware

{

    /**

     * The URIs that should be excluded from CSRF verification.

     *

     * @var array

     */

    protected $except = [
		'bandwidth-callback',
        'sms/callback',
		'upload_documentweb',
		'esign/upload_documentwebNew',
		'document-Insert-View',
		'acceptInvitation',
		'getResponseCanvas',
		'sign/{id}',
		'ap/{id}',
		'patient/appointment-save',
		'thankyous',
		'expired',
		'location-schedule-search1',
		'patient/change-status',
		'patient/appointment-update',
		'active-report-list',
		'patient-document-Insert-View',
		'check-post-send-sms',
		'privacy-policy',
		'twillio-sms-status-callback',
		'send-emmacare-document',
		'send-emmacare-document-referral-id',
		'submit-rnpad',
		'task-health/webhook/critical-alert',
		'esign/docusign/submit-form',
        //
		'esign/document-workflow/streamlined'

    ];

}

