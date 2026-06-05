<?php

namespace App\Services;

use App\DocumentSentReport;
use App\Template;
use App\User;
use App\Model\Patient;
use App\Services\TemplateService;
use App\Services\DocumentSendService;
use App\Services\DocumentSignerService;
use App\Services\PatientService;
use App\Services\SmsService;
use App\Services\DocumentSendSmsLogService;
use App\Services\DynamicFormLogService;
use App\Services\DoctorService;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class StreamlinedEsignService
{
    protected $templateService;
    protected $documentSendService;
    protected $documentSignerService;
    protected $patientService;
    protected $smsService;
    protected $documentSendSmsLogService;
    protected $dynamicFormLogService;
    protected $doctorService;

    protected const SMS_ESIGN_LINK = 'esign/nye/';

    public function __construct(
        TemplateService $templateService,
        DocumentSendService $documentSendService,
        DocumentSignerService $documentSignerService,
        PatientService $patientService,
        SmsService $smsService,
        DocumentSendSmsLogService $documentSendSmsLogService,
        DynamicFormLogService $dynamicFormLogService,
        DoctorService $doctorService
    ) {
        $this->templateService = $templateService;
        $this->documentSendService = $documentSendService;
        $this->documentSignerService = $documentSignerService;
        $this->patientService = $patientService;
        $this->smsService = $smsService;
        $this->documentSendSmsLogService = $documentSendSmsLogService;
        $this->dynamicFormLogService = $dynamicFormLogService;
        $this->doctorService = $doctorService;
    }

    /**
     * Get available templates for the streamlined workflow.
     */
    public function getAvailableTemplates($agencyId)
    {
        $query = Template::select('id', 'template_name', 'response')
            ->where('active_status', 'Active')
            ->where('del_flag', 'N')
            ->where(function ($q) use ($agencyId) {
                $q->whereRaw('FIND_IN_SET(?, agency_id)', [$agencyId])
                  ->orWhereNull('agency_id');
            })
            ->where('lookup_fields', 'patient')
            ->where('custom_template', 1)
            ->orderBy('template_name', 'asc')
            ->get();

        $templateResponse = [];
        if (!empty($query[0])) {
            foreach ($query as $val) {
                $response = unserialize($val->response);
                if (!empty($response[0])) {
                    $templateResponse[] = [
                        'id' => $val->id,
                        'template_name' => $val->template_name,
                    ];
                }
            }
        }

        return $templateResponse;
    }

    /**
     * Create document records and send form in one click.
     * Returns ['groupId' => string, 'insertId' => int]
     */
    public function createAndSendForm($patientId, $templateId, $doctorId, $action)
    {
        $auth = auth()->user();
        $patient = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);
        $query = $this->documentSignerService->getDocumentSignerMasterListById($templateId);
        $sourceFile = $this->templateService->getDetailsById($templateId);

        $rand = uniqid();
        $insertId = 0;

        // Auto-populate patient demographics in template response
        $populatedResponse = $this->populatePatientDemographics($sourceFile->response, $patient);

        foreach ($query as $val) {
            $countArray = 'No';
            $pending = '';
            $sourceFiles = '';
            $userId = $patientId;
            $eidc = $patient->patient_code ?? $patientId;

            if (strtolower($val->name) == strtolower($query[0]->name)) {
                $pending = 'Pending';
                $sourceFiles = $sourceFile->upload_document;
                $userId = $patientId;
                if (strtolower($val->name) == 'officestaff') {
                    $eidc = $val->user_id;
                }
            }

            $dataArray = $this->buildSignerData($val, $auth, $eidc, $pending, $sourceFiles, $userId, $templateId, $patient, $rand, $populatedResponse, $doctorId, $action);

            if ($dataArray !== null) {
                $countArray = 'Yes';
            }

            if ($countArray == 'Yes') {
                $insertId = $this->documentSendService->save($dataArray);
            }
        }

        return ['groupId' => $rand, 'insertId' => $insertId];
    }

    /**
     * Build signer data array based on signer type.
     */
    private function buildSignerData($val, $auth, $eidc, $pending, $sourceFiles, $userId, $templateId, $patient, $rand, $populatedResponse, $doctorId, $action)
    {
        $signerName = strtolower($val->name);
        $validSigners = ['caregiver', 'officestaff', 'other', 'stampuser', 'patient', 'formfill', 'sign', 'stamp'];

        if (!in_array($signerName, $validSigners)) {
            return null;
        }

        $sentOnMap = [
            'caregiver' => 'caregiver',
            'officestaff' => 'OfficeStaff',
            'other' => 'other',
            'stampuser' => 'stampUser',
            'patient' => 'patient',
            'formfill' => 'formFill',
            'sign' => 'sign',
            'stamp' => 'stamp',
        ];

        $receiptName = $patient->first_name . ' ' . $patient->last_name;

        if ($signerName == 'officestaff') {
            $getUserDetails = User::where('id', $val->user_id)->first();
            $receiptName = $getUserDetails->first_name . ' ' . $getUserDetails->last_name;
            $eidc = $val->user_id;
        }

        return [
            'sender_name' => $auth->first_name . ' ' . $auth->last_name,
            'caregiver_code' => $eidc,
            'status' => $pending,
            'sender_id' => $auth->id,
            'receipt_name' => $receiptName,
            'templete_id' => $templateId,
            'type' => 'Patient',
            'sourceFile' => $sourceFiles,
            'main_intakeId' => $userId,
            'sent_on' => $sentOnMap[$signerName],
            'groupId' => $rand,
            'template_response' => $populatedResponse,
            'doctor_id' => $doctorId,
            'streamlined_action' => $action,
        ];
    }

    /**
     * Auto-notify the first pending signer via SMS and email.
     */
    public function autoNotifySigners($groupId)
    {
        $query = $this->documentSendService->getGroupPending($groupId);

        if (!isset($query->id)) {
            return false;
        }

        $link = URL::to(self::SMS_ESIGN_LINK) . '/' . $query->id . '?id=' . $groupId;

        // Determine contact info based on signer type
        $email = '';

        // Send email notification
        if (!empty($email)) {
            $this->sendAutoEmail($email, $link);
        }

        // Update record as auto-notified
        $this->documentSendService->update(
            [
                'auto_notified' => 1,
                'auto_notified_at' => date('Y-m-d H:i:s'),
                'email' => $email,
            ],
            ['id' => $query->id]
        );

        // Log the SMS send
        $this->documentSendSmsLogService->save([
            'document_id' => $query->id,
            'caregiver_id' => $query->main_intakeId,
            'message' => 'Auto-notification sent via streamlined workflow',
            'email' => $email,
           
        ]);

        // Log in dynamic form log
        $user = auth()->user();
        $message = ($user->first_name ?? 'System') . ' ' . ($user->last_name ?? '') . ' sent auto-notification via streamlined esign';
        $insertLog = [
            'type' => 'Streamlined Esign Auto-Notify',
            'link' => url('esign/streamlined/form-send'),
            'module' => 'Esign Section',
            'module_id' => $groupId,
            'new_response' => serialize(['email' => $email, 'link' => $link]),
            'message' => $message,
            'is_status' => 'Auto Notify',
        ];
        $this->dynamicFormLogService->storeFormLog($insertLog);

        return true;
    }

    /**
     * Send auto email notification with esign link.
     */
    private function sendAutoEmail($email, $link)
    {
        $emailData = ['link' => $link, 'notes' => 'Please complete the esign form.'];
        $message = Utility::getHtmlContent('email_template.email_esign_link_template', $emailData);

        try {
            Mail::mailer('second')->send([], [], function ($msg) use ($email, $message) {
                $msg->subject('Esign - Document Ready for Signature');
                $msg->to(strtolower($email));
                $msg->html($message);
            });
        } catch (\Throwable $th) {
            // Silently fail email sending
        }
    }
    
}
