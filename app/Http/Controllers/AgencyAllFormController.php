<?php

namespace App\Http\Controllers;

use App\Helpers\AgencyAllForm;
use App\Services\AgencyAllFormService;
use App\Services\CommonEsignService;
use App\Services\DocumentSendService;
use App\Services\FormBuilderService;
use App\Services\PatientService;
use App\Services\DocumentSignerService;
use App\Services\TemplateService;
use App\Services\DynamicFormLogService;
use App\User;
use Illuminate\Http\Request;

class AgencyAllFormController extends Controller
{
    protected $AgencyAllFormService = '';
    protected $patientService = '';
    protected $commonEsignService = '';
    protected $FormBuilderService = '';
    protected $documentSignerService = '';
    protected $templateService = '';
    protected $documentSentReport = '';
    protected $dynamicFormLogService = '';

    function __construct(AgencyAllFormService $AgencyAllFormService, PatientService $patientService, CommonEsignService $commonEsignService, FormBuilderService $FormBuilderService, DocumentSignerService $documentSignerService, TemplateService $templateService, DocumentSendService $documentSentReport, DynamicFormLogService $dynamicFormLogService)
    {
        $this->AgencyAllFormService = $AgencyAllFormService;
        $this->patientService = $patientService;
        $this->commonEsignService = $commonEsignService;
        $this->FormBuilderService = $FormBuilderService;
        $this->documentSignerService = $documentSignerService;
        $this->templateService = $templateService;
        $this->documentSentReport = $documentSentReport;
        $this->dynamicFormLogService = $dynamicFormLogService;
    }

    public function getTemplatesData(Request $request)
    {
        $formId = $request->input('form_id');
        $templateId = $request->input('template_id');
        $agencyId = $request->input('agency_id');
        $agencyFormId = $request->input('id');
        $patient_id = $request->patient_id;
        $templateDetails = $this->AgencyAllFormService->getTemplate($templateId, $formId);
        $response = unserialize($templateDetails->response);

        $SubIntakeArray = [];
        if (isset($response) && $response != '') {
            $final_array[] = $templateDetails->docWidth;
            $data['docWidth'] = $templateDetails->docWidth;
            $data['sent_on'] = "Caregiver";

            foreach ($response as $val) {
                $final_array[] = $val;
               
                $max[] = $val['page'];
                $maxs = max($max);

                
                if (isset($val['placeHolder']) && $val['placeHolder'] != '') {
                    $val['placeHolder'] = str_replace('%22', '', $val['placeHolder']);
                }
                if ($val['temp1'] == 'caregiver') {
                    if ($val['temp3'] != '') {
                        $subresponse = $this->caregiverFieldsResponse($formId, $patient_id, $val['temp3'], $agencyId,$agencyFormId);
                        $val['text'] = $subresponse[$val['temp3']];
                    }
                } else {

                    $dynamicDropdownId = isset($val['dynamicDropdownId']) ? $val['dynamicDropdownId'] : "";
                    $dynamicDropdownIdVal = isset($val['dynamicDropdownIdVal']) ? $val['dynamicDropdownIdVal'] : "";

                    if ($dynamicDropdownId != "") {
                        $subresponse = $this->showOtherCheckBox($formId, $dynamicDropdownId, $patient_id, $request->id);
                        
                        if (isset($val['normalValue'])) {
                            if (in_array($val['normalValue'], $subresponse)) {
                                $val['checked'] = 1;
                            }
                        }
                    }else if ($dynamicDropdownIdVal != "") {
                        $subresponse = $this->showOtherRadio($formId, $dynamicDropdownIdVal, $patient_id, $request->id);
                        if (isset($val['normalValueRadio'])) {
							if (is_array($subresponse)) {
								if (in_array($val['normalValueRadio'], $subresponse)) {
									$val['checked'] = 1;
								}
							} else {
								if ($val['normalValueRadio'] == $subresponse) {
									$val['checked'] = 1;
								}
							}
						}
                    }
                }
                $SubIntakeArray[] = $val;
                $Signinsert[] = $val;
            }
        }
        // Insert form Log into Dynamic form log table
        $insertLog = [
            'type' => 'Download',
            'link' => url('/get-template-data'),
            'module' => 'Agency All Form',
            'module_id' => $patient_id
        ];
        $this->dynamicFormLogService->storeFormLog($insertLog);

        $this->commonEsignService->downloadPDF($SubIntakeArray, $templateId);

        return 1;
    }

    function caregiverFieldsResponse($formId="", $id, $keys, $agencyId,$agencyFormId="")
    {
        $key = $keys;
        $user_id = $id;
        $explode  = explode('@', $key);

        $finalArray = array();
        if ($explode[0] == 'fm') {
            $caregiverDetails = AgencyAllForm::GetFormDetails($formId, $explode[1], $user_id,$agencyFormId);
        } else if ($explode[0] == 'dr') {
            $caregiverDetails = AgencyAllForm::GetDoctorDetails($formId, $explode[1], $user_id,$agencyFormId);
        } else if ($explode[0] == 'ag') {
            $caregiverDetails = AgencyAllForm::GetAgencyDetails($explode[1], $agencyId);
        } else {
            $caregiverDetails = $this->patientService->GetCaregiverFormDetails($explode[1], $user_id);
        }

        if ($explode[1] == 'dob' || $explode[1] == 'date_of_examination') {
            $date = "";
            if ($caregiverDetails != "") {
                $date = date('m/d/Y', strtotime($caregiverDetails));
            }
            $finalArray[$key] = $date;
        } else {
            $finalArray[$key] = $caregiverDetails ?? "";
        }


        return $finalArray;
    }

    public function storeAgencyForm(Request $request)
    {
        $formId = $request->input('f_id');
        $patient_id = $request->input('patient_id');
        $agency_id = $request->input('agency_id');
        $receipt_name = $request->input('receipt_name');
        $eid = $request->input('eid');
        $eidc = $request->input('eidc');
        $type = $request->input('type');

        $agencyForm  = $this->AgencyAllFormService->storeAgencyForm($request);
        $patientSubmitData = $this->FormBuilderService->getSubmitDataByAgencyIdPatientID($agency_id, $patient_id, $agencyForm->id);
        $patientSubmitDataGroupByFormId = [];
        foreach ($patientSubmitData as $patientSubmit) {
            $patientSubmitDataGroupByFormId[$patientSubmit->form_id][$patientSubmit->agency_id][$patientSubmit->patient_id][$patientSubmit->field_id] = $patientSubmit->value;
        }
        $patientSubmitData[$agencyForm->id] = $patientSubmitDataGroupByFormId;

        $doctorList = $this->FormBuilderService->getAllDoctor();

        $formData = [
            'receipt_name' => $receipt_name,
            'eid' => $eid,
            'eidc' => $eidc,
            'type' => $type,
        ];

        $getExistingData = $this->AgencyAllFormService->existAgencyForm($formId, $patient_id);

        // Insert form Log into Dynamic form log table
        $insertLog = [
            'type' => 'Add',
            'link' => url('/store-agency-form'),
            'module' => 'Agency All Form',
            'module_id' => $agencyForm->id,
            'new_response' => serialize($getExistingData),
        ];
        $this->dynamicFormLogService->storeFormLog($insertLog);

        if ($agencyForm) {
            return response()->json(['status' => true, 'msg' => 'Form Added successfully', 'data' => $agencyForm, 'doctorList' => $doctorList, 'patientSubmitData' => $patientSubmitData, 'formData' => $formData]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.']);
        }
    }

    public function showOtherCheckBox($formId, $fieldId, $patient_id,$id)
    {
        
        $query = AgencyAllForm::GetFormDetails($formId, $fieldId, $patient_id,$id);

        $data = unserialize($query);

        $final = [];
        if(!empty($data[0])){
            foreach ($data as $val) {
                if ($val != 'null') {
                    $final[] = $val;
                }
            }
        }
       
        return $final;
    }

    public function showOtherRadio($formId, $fieldId, $patient_id,$id)
    {
        $query = AgencyAllForm::GetFormDetails($formId, $fieldId, $patient_id,$id);

        return $query;
    }

    public function storeMoveToEsign(Request $request)
    {
        $auth = auth()->user();

        $query = $this->documentSignerService->getDocumentSignerMasterListById($request->template_id);
        $SourceFile = $this->templateService->getDetailsById($request->template_id);

        $rand = uniqid();
        $insertid = 0;
        $data_array = [];
        foreach ($query as $val) {
            $countArray = 'No';
            $pending = '';
            $SourceFiles = '';
            $user_id = $request->input('eid');
            $maicareg = '';
            $eidc = ($request->input('eidc') != null) ? $request->input('eidc') : $user_id;
            if (strtolower($val->name) == strtolower($query[0]->name)) {

                $pending = 'Pending';
                $SourceFiles = $SourceFile->upload_document;
                $user_id = $request->input('eid');
                if (strtolower($val->name) == 'officestaff') {
                    $eidc = $val->user_id;
                } else {
                    $eidc = $request->input('eidc');
                }

            }


            if (strtolower($val->name) == 'caregiver') {
                $countArray = 'Yes';
                $data_array = array(
                    'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
                    'caregiver_code' => $eidc,
                    'status' => $pending,
                    'sender_id' => $auth['id'],
                    'receipt_name' => $request->receipt_name,
                    'templete_id' => $request->template_id,

                    'type' => $request->type,
                    'sourceFile' => $SourceFiles,
                    'main_intakeId' => $user_id,
                    'sent_on' => 'caregiver',
                    'groupId' => $rand,
                    'template_response' => $SourceFile->response,
                    'agency_form_id' => $request->id ?? null,
                );
            }

            if (strtolower($val->name) == 'officestaff') {
                $getUserDetails = User::getDetailsById($val->user_id);
                $countArray = 'Yes';
                $data_array = array(
                    'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
                    'caregiver_code' => $val->user_id,
                    'status' => $pending,
                    'sender_id' => $auth['id'],
                    'receipt_name' => $getUserDetails->first_name . ' ' . $getUserDetails->last_name,
                    'templete_id' => $request->template_id,

                    'type' => $request->type,
                    'sent_on' => 'OfficeStaff',
                    'sourceFile' => $SourceFiles,
                    'main_intakeId' => $request->eid,
                    'groupId' => $rand,
                    'template_response' => $SourceFile->response,
                    'agency_form_id' => $request->id ?? null,
                );
            }

            if (strtolower($val->name) == 'other') {
                $countArray = 'Yes';
                $data_array = array(
                    'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
                    'caregiver_code' => $eidc,
                    'status' => $pending,
                    'sender_id' => $auth['id'],
                    'receipt_name' => $request->receipt_name,
                    'templete_id' => $request->template_id,

                    'type' => $request->type,
                    'sourceFile' => $SourceFiles,
                    'main_intakeId' => $user_id,
                    'sent_on' => 'other',
                    'groupId' => $rand,
                    'template_response' => $SourceFile->response,
                    'agency_form_id' => $request->id ?? null,
                );
            }
            if (strtolower($val->name) == 'stampuser') {
                $countArray = 'Yes';
                $data_array = array(
                    'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
                    'caregiver_code' => $eidc,
                    'status' => $pending,
                    'sender_id' => $auth['id'],
                    'receipt_name' => $request->receipt_name,
                    'templete_id' => $request->template_id,

                    'type' => $request->type,
                    'sourceFile' => $SourceFiles,
                    'main_intakeId' => $user_id,
                    'sent_on' => 'stampUser',
                    'groupId' => $rand,
                    'template_response' => $SourceFile->response,
                    'agency_form_id' => $request->id ?? null,
                );
            }

            if (strtolower($val->name) == 'patient') {
                $countArray = 'Yes';
                $data_array = array(
                    'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
                    'caregiver_code' => $eidc,
                    'status' => $pending,
                    'sender_id' => $auth['id'],
                    'receipt_name' => $request->receipt_name,
                    'templete_id' => $request->template_id,

                    'type' => $request->type,
                    'sourceFile' => $SourceFiles,
                    'main_intakeId' => $user_id,
                    'sent_on' => 'patient',
                    'groupId' => $rand,
                    'template_response' => $SourceFile->response,
                    'agency_form_id' => $request->id ?? null,
                );
            }

            if ($countArray == 'Yes') {

                $insertid = $this->documentSentReport->save($data_array);
            }
        }

        if ($insertid) {
            return response()->json(['status' => true, 'msg' => 'Move To Esign Successfully', 'data' => $insertid]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.']);
        }
    }
}
