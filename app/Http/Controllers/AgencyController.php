<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Validator;

use App\Agency;
use App\User;
use App\Record;
use App\RecordNotes;
use App\Master;
use App\GenerateAgencyToken;
use Excel;
use App\Helpers\UserHelper;
use App\Helpers\GenerateAgencyTokenHelper;
use App\Helpers\AgencyWiseDomainHelper;
use App\AgencyCountry;
use App\AgencyIpAddress;
use App\Helpers\EncryptDecryptCodeHelper;
use App\Helpers\AgencyNotificationEmailHelper;
use App\Model\AgencyWiseNotifictionEmail;
use App\Model\AgencyWiseService;
use App\Model\AgencyWiseSms;
use App\Services\LogsService;
use App\Services\AgencyWiseServiceService;
use App\Services\AgencySkillService;
use App\Helpers\AlayacareHelper;
use App\Services\AlayacareClientService;
use App\Services\AlayacareService;
use App\Services\FormBuilderService;
use App\Helpers\HHACaregiversHelper;
use App\Helpers\HHAOfficeHelper;
use App\Model\HHACaregivers;
use App\Model\HHAPatient;
use App\Services\TokenwiseApiCallService;
use App\Model\FieldMaster;
use App\Model\AgencyMaster;
use Illuminate\Support\Facades\Storage;
use App\Services\AgencyWebHookService;
use App\Services\AgencyWiseSMSNotificationService;
use Illuminate\Support\Facades\Cache;
use App\Services\AgencyWiseDisabledService;
use App\Helpers\Utility;
use App\Services\UserCreatorEmailNotificationService;
use App\Services\AssignNyBestUserService;
use App\Services\HHAMDOService;
use App\Services\AgencyTaskHealthService;
use App\Services\AgencyWiseVistingClientService;
use App\Services\AgencyService;
use App\Services\AgencyPocDocumentTypeService;
use App\Services\AgencyOtherComplianceMedicalService;
use App\Model\AgencyOtherComplianceMedical;
use App\Services\AgencyNoteService;
use App\Services\UserService;
class AgencyController extends BaseController
{
    protected $AgencyWiseServiceService, $AgencySkillService, $alayacareClientService, $alayacareService, $tokenWiseApiCallService, $FormBuilderService, $agencyWebHookService, $agencyWiseSMSNotificationService,$agencyWiseDisabledService,$userCreatorEmailNotificationService,$assignNyBestUserService,$agencyTaskHealthService = '';
    protected $hhaMDOService="";
    protected $agencyWiseVistingClientService = "";
    protected $agencyService = "";
    protected $agencyPocDocumentTypeService = "";
    protected $agencyOtherComplianceMedicalService = "";
    protected $agencyNoteService = "";
    protected $userService;
    public function __construct(AgencyWiseServiceService $AgencyWiseServiceService, AgencySkillService $AgencySkillService, AlayacareClientService $alayacareClientService, AlayacareService $alayacareService, TokenwiseApiCallService $tokenWiseApiCallService, FormBuilderService $FormBuilderService, AgencyWebHookService $agencyWebHookService, AgencyWiseSMSNotificationService $agencyWiseSMSNotificationService,AgencyWiseDisabledService $agencyWiseDisabledService,UserCreatorEmailNotificationService $userCreatorEmailNotificationService, AssignNyBestUserService $assignNyBestUserService,HHAMDOService $hhaMDOService, AgencyTaskHealthService $agencyTaskHealthService,AgencyWiseVistingClientService $agencyWiseVistingClientService, AgencyService $agencyService, AgencyPocDocumentTypeService $agencyPocDocumentTypeService, AgencyOtherComplianceMedicalService $agencyOtherComplianceMedicalService, AgencyNoteService $agencyNoteService,UserService $userService)
    {
        $this->middleware('permission:agency-list|agency-add|agency-edit|agency-delete|agency-view', ['only' => ['index', 'save', 'view']]);
        $this->middleware('permission:agency-list', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:agency-add', ['only' => ['add', 'save']]);
        $this->middleware('permission:agency-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:agency-delete', ['only' => ['delete']]);
        $this->middleware('permission:agency-view', ['only' => ['view']]);
        $this->middleware('permission:update-alayacare-details', ['only' => ['agencyAlaycareDetailsSave']]);
        $this->middleware('permission:update-remote-details', ['only' => ['agencyRobortDetailsSave']]);
        $this->middleware('permission:update-alayacare-skill-details', ['only' => ['addAlayaAgencySkill']]);
        $this->middleware('permission:agency-notes-list', ['only' => ['getAgencyNotes']]);
        $this->middleware('permission:agency-notes-add', ['only' => ['addAgencyNote']]);
        $this->middleware('permission:agency-notes-toggle', ['only' => ['toggleAgencyNote']]);
        $this->middleware('permission:agency-notes-delete', ['only' => ['deleteAgencyNote']]);

        $this->middleware('auth');
        $this->AgencyWiseServiceService = $AgencyWiseServiceService;
        $this->AgencySkillService = $AgencySkillService;

        $this->alayacareClientService = $alayacareClientService;
        $this->alayacareService = $alayacareService;
        $this->tokenWiseApiCallService = $tokenWiseApiCallService;
        $this->FormBuilderService = $FormBuilderService;
        $this->agencyWebHookService = $agencyWebHookService;
        $this->agencyWiseSMSNotificationService = $agencyWiseSMSNotificationService;
        $this->agencyWiseDisabledService = $agencyWiseDisabledService;
        $this->userCreatorEmailNotificationService = $userCreatorEmailNotificationService;
        $this->assignNyBestUserService = $assignNyBestUserService;
        $this->hhaMDOService = $hhaMDOService;
        $this->agencyTaskHealthService = $agencyTaskHealthService;
        $this->agencyWiseVistingClientService = $agencyWiseVistingClientService;
        $this->agencyService = $agencyService;
        $this->agencyPocDocumentTypeService = $agencyPocDocumentTypeService;
        $this->agencyOtherComplianceMedicalService = $agencyOtherComplianceMedicalService;
        $this->agencyNoteService = $agencyNoteService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {

        $data['menu'] = "user";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }

        if ($user['agency_fk'] != "") {
            return abort(404);
        }

        $agency_name = $data['agency_name'] = $request->agency_name;
        $email = $data['email'] = $request->input('email');
        $phone = $data['phone'] = $request->input('phone');
        $city = $data['city'] = $request->input('city');
        $data['is_sms'] = $request->is_sms;

        $data['query'] = Agency::getData($agency_name, $email, $phone, $city, $request->is_sms);

        return view("agency/list", $data);
    }

    public function ajaxList(Request $request)
    {
        $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }

        $data['query'] = Agency::getData(
            $request->agency_name,
            $request->email,
            $request->phone,
            $request->city,
            $request->is_sms
        );

        return view('agency._partial.agency_ajax_list', $data);
    }

    public function add()
    {
        $data['menu'] = "Add user";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }
        return view("agency/add", $data);
    }

    public function save(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'agency_name' => 'required',
            'email' => 'required|email|unique:agency,email',
            'phone' => 'required|numeric|digits_between:10,15',
            'address1' => 'required',
            'address2' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip_code' => 'required',

        ]);
        if ($validator->fails()) {
            return redirect("/agency/add")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {
            $agency_name = $request->input('agency_name');
            $email = $request->input('email');
            $phone = $request->input('phone');
            $address1 = $request->input('address1');
            $address2 = $request->input('address2');
            $state = $request->input('state');
            $city = $request->input('city');
            $zip_code = $request->input('zip_code');
            $county = $request->input('county');


            $data = array(
                'agency_name' => $agency_name,
                'email' => $email,
                'phone' => $phone,
                'address1' => $address1,
                'address2' => $address2,
                'state' => $state,
                'city' => $city,
                'zip_code' => $zip_code,
                'county' => $county,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user->id,
                'app_name' => $request->input('app_name'),
                'app_key' => $request->input('app_key'),
                'app_token' => $request->input('app_token'),
                'notification_email' => $request->input('notification_email'),
                'service_md_appointment' => 1,
                'client_name' => $request->client_name,
                'document_email_notification' => $request->document_email_notification,
                'efax_no' => $request->efax_no,
            );
            if ($request->input('nybest_email_notification') != '') {
                $data['nybest_email_notification'] = $request->input('nybest_email_notification');
            }

            $ins_test = new Agency($data);
            $ins_test->save();
            $insert = $ins_test->id;

            if ($insert) {

               // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Add',
                    'link' => url('/agency/save'),
                    'module' => 'Agency',
                    'object_id' => $ins_test->id,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added Agency',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                Session::flash('success', 'Agency successfully inserted');
                return redirect('/agency');
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/agency');
            }
        }
    }

    public function edit($id)
    {
        $data['menu'] = "user";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['id'] = $id;
        $data['agency'] = Agency::where("id", $id)->first();
        return view('agency/edit', $data);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data['id'] = $id;
        $validator = Validator::make($request->all(), [
            'agency_name' => 'required',
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect("/agency/edit/$id")
                ->withErrors($validator, 'edit_agency')
                ->withInput();
        } else {
            $agency_name = $request->input('agency_name');
            $email = $request->input('email');
            $phone = str_replace('-', '', $request->input('phone'));
            $address1 = $request->input('address1');
            $address2 = $request->input('address2');
            $state = $request->input('state');
            $city = $request->input('city');
            $zip_code = $request->input('zip_code');
            $county = $request->input('county');

            $data = array(
                'agency_name' => $agency_name,
                'email' => $email,
                'phone' => $phone,
                'address1' => $address1,
                'address2' => $address2,
                'state' => $state,
                'city' => $city,
                'zip_code' => $zip_code,
                'county' => $county,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user->id,
                'app_name' => $request->input('app_name'),
                'app_key' => $request->input('app_key'),
                'app_token' => $request->input('app_token'),
                'notification_email' => $request->input('notification_email'),
                'client_name' => $request->client_name,
                'document_email_notification' => $request->document_email_notification,
                'efax_no' => $request->efax_no,
            );
            $data['nybest_email_notification'] = $request->input('nybest_email_notification');
            if ($request->input('nybest_email_notification') != '') {
            }

            $update = Agency::where('id', $id)->update($data);

            if ($update) {
                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Update',
                    'link' => url('/agency/update'),
                    'module' => 'Agency',
                    'object_id' => $id,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has updated Agency',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
            }
            Session::flash('success', 'Agency successfully updated');
            return redirect('/agency-view/' . $id);
        }
    }
    public function view(Request $request, $id)
    {
        $data['menu'] = "Agency";
        $data['title'] = "Agency View";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['agency_id'] = $id;
        $data['id'] = $id;
        $data['agencyDetails'] = Agency::where('delete_flag', 'N')->where('id', $id)->first();
        $data['fieldMasterData'] = FieldMaster::whereNull('custom')->get();
        if ($data['agencyDetails'] != '') {
            /*Rate*/
            $data['generate_token_details'] = GenerateAgencyToken::getTokenByAgencyId($id);

            $data['agency_fk'] = $request->input('agency_fk');
            $data['flag'] = $flag = $request->input('flag');

            if ($flag == '') {
                $data['name'] = $request->input('name');
                $data['select_item'] = $request->input('item');
                $data['start_date'] = $request->input('start_date');
                $data['end_date'] = $request->input('end_date');
            }
            if ($flag != '') {
                $data['daterange'] = $request->input('daterange');

                $data['status'] = $request->input('status');
            }

            $data['agencyList'] = Agency::where('delete_flag', 'N')->get();
            /*End*/

            $data['agencyCount'] = [];

            $data['countryList'] = [];
            $selectedCountryIdArray = [];
            $selectedCountry = []; //AgencyCountry::agencyWiseCountry($id);
            foreach ($selectedCountry as $value) {
                $selectedCountryIdArray[] = $value->country_id;
            }
            $data['selectedCountry'] = $selectedCountryIdArray;
            $agencyWiseNotificationEmail =  Master::getAllDataByMasterTypeFk(array(24, 28));
            $statusUpdateArray = [];
            $generateUpdateArray = [];
            if (!empty($agencyWiseNotificationEmail[0])) {
                foreach ($agencyWiseNotificationEmail as $val) {
                    if ($val->master_type_fk == '28') {
                       $generateUpdateArray[] = $val;
                    }else{
                        $statusUpdateArray[] = $val;
                    }
                }
            }
            $data['agencyWiseNotificationEmail'] = $statusUpdateArray;
            $data['agencyGenerateUpdateArray'] = $generateUpdateArray;
            $data['encryptedId'] = EncryptDecryptCodeHelper::encryptData($id);

            $agency_skill = $this->AgencySkillService->getSkillByAgencyId($id);

            $data['agency_skill'] = $agency_skill;

            $totalClient = $this->alayacareClientService->totalSyncClientDetails($id);
            $data['totalClient'] = count($totalClient);

            $totalEmployee = $this->alayacareService->totalSyncEmployeeDetails($id);
            $data['totalEmployee'] = count($totalEmployee);

            $totalCaregiver = HHACaregivers::fetchCaregiverCount($id);

            $data['totalCaregiver'] = count($totalCaregiver);
            $data['totalPatient'] = count(HHAPatient::fetchPatientCount($id));
            //$data['office'] = HHAOfficeHelper::getAllOffice($id);
            $data['serviceList'] = Cache::get('patient_master_services', function ()  use ($user) {
                $agencyId = $user->agency_fk;
                $getAgencyWiseList = $this->AgencyWiseServiceService->getServiceNew($agencyId, "");
                if (!empty($getAgencyWiseList[0])) {
                    return  $getAgencyWiseList;
                } else {
                    return  Master::getServiceRequest(1)->whereIn('types', ['Caregiver', 'Patient']);
                }
            }, 10 * 60);

            $totalActive = [];
            $totalBlock = [];
            $totalInactive = [];
            $getAllUserList = UserHelper::getAgencyWiseUserList($id);

            if(count($getAllUserList) >0){
                foreach($getAllUserList as $val){
                    if($val->active =='active'){
                        $totalActive[] = $val;
                    }
                    if($val->active =='block'){
                        $totalBlock[] = $val;
                    }

                    if($val->active =='inactive'){
                        $totalInactive[] = $val;
                    }
                }

            }

            $data['totalActive'] = count($totalActive);
            $data['totalBlock'] = count($totalBlock);
            $data['totalInactive'] = count($totalInactive);
            $data['createdUser'] = User::getDetailsById($data['agencyDetails']->created_by);
            $data['updatedUser'] = User::getDetailsById($data['agencyDetails']->updated_by);
            $nybestUserData = $this->assignNyBestUserService->getAssignNybestUser($id);
		    $data['nybestUserData'] = $nybestUserData;

            $getHHAMDODetails = $this->hhaMDOService->getAllClientDetailsByAgencyId($id);
            $data['agencyDetails']->mdo_client_id = $getHHAMDODetails->client_id??"";
            $data['agencyDetails']->mdo_client_secret = $getHHAMDODetails->client_secret??"";
            $data['agencyDetails']->mdo_api_token = $getHHAMDODetails->api_token??"";
            $data['agencyDetails']->mdo_txtID = $getHHAMDODetails->txtID??"";
            $data['agencyDetails']->mdo_is_status = $getHHAMDODetails->is_status??"";
            $data['statusData'] = Utility::getPatientStatusData();
            $data['agencyDetails']->enable_task_health = Agency::getStatusOfTaskHealth($id);
            $data['agencyDetails']->enable_file_manager = Agency::getStatusOfFileManager($id);
            $data['visiting_client'] = $this->agencyWiseVistingClientService->getDetailsByAgencyId($id);
            $data['agencyDetails']->poc_document_type_name = $this->agencyPocDocumentTypeService->getDocumentNameById($data['agencyDetails']->poc_document_type_id,$id);
            $data['agencyDetails']->supervision_document_type_id = $data['agencyDetails']->supervision_document_type_id ?? null;
            $data['agencyDetails']->medical_id = $data['agencyDetails']->medical_id ?? null;
            return view('agency/view', $data);
        } else {
            return redirect('/agency');
        }
    }

    public function userList(Request $request)
    {
        $data['page'] = $request->page;
        $data['UsersList'] = UserHelper::getAgencyWiseUserList($request->id);

        return view('agency/userList', $data);
    }
    public function fieldMasterList(Request $request)
    {
        $agencyId = $request->agency_id;
        $page = $request->page;

        $formFields = $this->FormBuilderService->getFormFieldsForAgency($agencyId, "");
        $fieldMasterData = FieldMaster::whereNull('custom')->get();
        $data = [
            'agency_id' => $agencyId,
            'form_id' => "",
            'page' => $page,
            'formFields' => $formFields,
            'fieldMasterData' => $fieldMasterData,
        ];

        return view('agency/fieldList', $data);
    }

    public function updateAgencyMasterOrder(Request $request)
    {
        $sortOrder = $request->input('sortOrder');

        if (isset($sortOrder) && is_array($sortOrder)) {
            foreach ($sortOrder as $item) {
                $updateOrder = AgencyMaster::where('field_id', $item['id']);
                if (array_key_exists('agencyId', $item) && !array_key_exists('formID', $item)) {
                    $updateOrder = $updateOrder->where('agency_id', $item['agencyId'])->whereNull('form_id');
                } else if (!array_key_exists('agencyId', $item) && array_key_exists('formID', $item)) {
                    $updateOrder = $updateOrder->whereNull('agency_id')->where('form_id', $item['formID']);
                }
                if (array_key_exists('formID', $item) && !array_key_exists('agencyId', $item)) {
                    $updateOrder = $updateOrder->where('form_id', $item['formID'])->whereNull('agency_id');
                } else if (!array_key_exists('formID', $item) && array_key_exists('agencyId', $item)) {
                    $updateOrder = $updateOrder->whereNull('form_id')->where('agency_id', $item['agencyId']);
                }
                $updateOrder->update(['sort_id' => $item['order']]);
            }
        }

        return response()->json(['message' => 'Order updated successfully']);
    }

    public function agencyMasterList(Request $request)
    {
        $agencyId = $request->agency_id;
        $formId = $request->form_id;
        $page = $request->page;

        $formFields = $this->FormBuilderService->getFormFieldsForAgency($agencyId, $formId);
        $fieldMasterData = FieldMaster::whereNull('custom')->get();

        $data = [
            'agency_id' => $agencyId,
            'form_id' => $formId,
            'page' => $page,
            'formFields' => $formFields,
            'fieldMasterData' => $fieldMasterData,
        ];

        return view('agency/formList', $data);
    }

    public function formSetupList(Request $request)
    {
        $agencyId = $request->agency_id;
        $page = $request->page;

        $formSetupData = $this->FormBuilderService->getFormSetupForAgency($agencyId);
        $data = [
            'agency_id' => $agencyId,
            'page' => $page,
            'formSetupData' => $formSetupData,
        ];

        return view('agency/formSetupList', $data);
    }

    public function destroyAgencyMaster($id, Request $request)
    {
        $agencyId = $request->agency_id;
        $formId = $request->form_id ?? "";

        $this->FormBuilderService->deleteAgencyMasterAndField($agencyId, $id, $formId);

        return response()->json(['status' => true, 'msg' => 'Agency Master deleted successfully']);
    }
    public function agencyrWiselogs(Request $request)
    {
        $id = request('id');
        $data['user'] = $authId = auth()->user();
        $data['logList'] = LogsService::getDatByAllLog($id, 'Agency');

        return view("user_log_ajax_list", $data);
    }

    public function delete($id)
    {
        $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['id'] = $id;
        $delArr = array('delete_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id);
        $update = Agency::where('id', $id)->update($delArr);
        if ($update) {
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Delete',
                'link' => url('/agency/delete/' . $id),
                'module' => 'Agency',
                'object_id' => $id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has deleted Agency',
                'new_response' => serialize($delArr),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            Session::flash('success', 'Agency successfully deleted');
            return redirect('/agency');
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect('/agency');
        }
    }

    public function agencySmsStatus(Request $request)
    {

        $updateSmsStatus = Agency::where('id', $request['agency_id'])->update(['is_sms' => $request['is_sms']]);
        if ($updateSmsStatus) {
            $IsSms = $request['is_sms'] == 0 ? 'disabled' : 'enabled';
            return response()->json(['status' => true, 'error_msg' => 'Sms ' . $IsSms . ' sucessfully'], 200);
        }
    }

    public function hhaStatus(Request $request)
    {
        $updateSmsStatus = Agency::where('id', $request['agency_id'])->update(['enable_hha' => $request['enable_hha']]);
        $IsSms = $request['enable_hha'] == 0 ? 'disabled' : 'enabled';

        $details = Agency::select('enable_hha')->where('id', $request['agency_id'])->first();
        return response()->json(['status' => true, "data" => $details->enable_hha, 'error_msg' => 'HHA ' . $IsSms . ' sucessfully'], 200);
    }

    public function tableUpdate(Request $request)
    {
        //  print_r($_POST);die();

        $user = auth()->user();
        $columname = $request->input('column');
        $editval = $request->input('editval');
        $data['id'] = $id = $request->input('id');

        /*$validator = Validator::make($request->all(), [
           "$columname" => 'required',

        ]);
        if ($validator->fails()) {
            return redirect("/agency/edit/$id")
                ->withErrors($validator, 'edit_agency')
                ->withInput();
        } else { }*/

        $data = array(
            $columname => $editval,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $user->id
        );
        print_r($data);
        $update = Agency::where('id', $id)->update($data);
        if ($update) {
            echo    Session::flash('success', 'Agency successfully updated');
            // return redirect('/agency-view/'.$id);
        } else {

            echo Session::flash('error', 'Sorry, something went wrong. Please try again.');
            //return redirect('/agency-view/'.$id);
        }
    }
    public function agencyExport(Request $request)
    {


        $user = auth()->user();


        $agency_name = $data['agency_name'] = $request->input('agency_name');
        $email = $data['email'] = $request->input('email');
        $phone = $data['phone'] = $request->input('phone');
        $city = $data['city'] = $request->input('city');
        $is_sms = $data['is_sms'] = $request->input('is_sms');
        $users = Agency::getDataExport($agency_name, $email, $phone, $city, $is_sms);
        //echo "<pre>";print_r($users);die('ello');

        $filename = 'Agency' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        //$columns = array('Agency Name', 'Email', 'Phone', 'Address1', 'Address2', 'State', 'City', 'Zip Code', 'Billing Email', 'Bill Date', 'Monthly Bill');
        $columns = array('Agency Name', 'Email', 'Phone', 'City','Notification Email','NyBest Notification Email','Eanbled SMS');

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($users as $list) {
                $bill_date = '';
                if ($list->bill_date) {
                    $bill_date = date('m-d-Y', strtotime($list->bill_date));
                }

                $smsStatus = "No";
                if ($list->is_sms == 1) {
                    $smsStatus = "Yes";
                }
                fputcsv($file, array($list->agency_name, $list->email, $list->phone, $list->city, $list->notification_email, $list->nybest_email_notification, $smsStatus));
                //fputcsv($file, array($list->agency_name, $list->email, $list->phone, $list->address1, $list->address2, $list->state, $list->city, $list->zip_code, $list->billing_email, $bill_date, $list->monthly_bill));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
    public function import($id)
    {
        $data['id'] = $id;
        return view('agency.import', $data);
    }
    public function ExcelImport($id)
    {
        $data['id'] = $id;
        return view('agency.importExcel',  $data);
    }

    function subInsert(Request $request, $id)
    {

        $imgs = $request->file('import_excel');

        $arrays = Excel::toArray([], $imgs);

        //  $this->completeUnderCare($arrays);
        //$this->completeStatus($arrays);
        //die();
        $agency_fk = $id;
        $counter = 0;



        foreach ($arrays[0] as $row) {

            if ($counter != 0) {

                $patientNameArray = explode(' ', $row[2]);
                if (count($patientNameArray) > 1) {
                    $last_name = $patientNameArray[count($patientNameArray) - 1];;
                    $first_name = str_replace($last_name, '', $row[2]);
                } else {
                    $first_name = $row[2];
                    $last_name = "";
                }
                $patient_status = $row[12];
                $first_name = trim($first_name);
                $last_name = trim($last_name);




                $first_name = rtrim($first_name, ',');
                $last_name = rtrim($last_name, ',');
                $last_name = rtrim($last_name, '.');

                $recordExist = Record::where('agency_fk', $agency_fk)->where('first_name', $first_name)->where('last_name', $last_name)->first();
                if (!$recordExist) {





                    $emcRep = ucfirst($row[9]);
                    $emcRepId = NULL;
                    if ($emcRep != '') {
                        $emcRerpUser = User::where('first_name', $emcRep)->whereIn('user_type_fk', array(184, 4))->first();
                        if ($emcRerpUser) {
                            $emcRepId = $emcRerpUser->id;
                        } else {
                            $ins_test = new User(array("first_name" => $emcRep, 'user_type_fk' => '4', 'login_type_fk' => 1));
                            $ins_test->save();
                            $emcRepId = $ins_test->id;
                        }
                    }

                    $agencyRep = ucfirst($row[6]);
                    $agencyRepId = NULL;
                    if ($agencyRep != '') {
                        $agencyRepUser = User::where('first_name', $agencyRep)->whereIn('user_type_fk', array(5, 6))->where('agency_fk', $agency_fk)->first();
                        if ($agencyRepUser) {
                            $agencyRepId = $agencyRepUser->id;
                        } else {
                            $ins_test = new User(array("first_name" => $agencyRep, 'user_type_fk' => '6', 'login_type_fk' => 2, 'agency_fk' => $agency_fk));
                            $ins_test->save();
                            $agencyRepId = $ins_test->id;
                        }
                    }

                    $follow_date = "";
                    if (trim($row[10]) != "") {
                        $dateExplo = explode('/', $row[10]);

                        $follow_date = '2020-' . $dateExplo[0] . '-' . $dateExplo[1];
                    }





                    $data = array(
                        'emc_rep' => $emcRepId,
                        'agency_rep' => $agencyRepId,
                        'first_name' => trim($first_name),
                        'last_name' => trim($last_name),
                        'agency_fk' =>  $agency_fk,
                        'family_name1' => $row[4],
                        'relationship1' => $row[5],
                        'phone' => str_replace('-', '', str_replace('(', '', str_replace(')', '', $row[3]))),
                        'extra' => $row[1],
                        'ref_id' => $row[0],
                        'patient_status' => $row[12],
                        'created_at' => date('Y-m-d H:i:s')

                    );
                    if ($follow_date != "") {
                        $data['follow_date'] = $follow_date;
                    }


                    $ins_test = new Record($data);
                    $ins_test->save();
                    $insert = $ins_test->id;


                    //$insert = insertGetId($data);
                    if ($insert) {
                        $dataInserts = array();
                        if ($row[7] != "") {
                            $dataInserts[] = array('record_id' => $insert, 'message' => $row[7], 'type' => 'Agency', 'created_at' => date('Y-m-d H:i:s'), 'created_by' => 1);
                        }
                        if ($row[11] != "") {

                            $dataInserts[] = array('record_id' => $insert, 'message' => $row[11], 'type' => 'Agency', 'created_at' => date('Y-m-d H:i:s'), 'created_by' => 1);
                        }
                        if ($row[8] != "") {
                        }

                        RecordNotes::insert($dataInserts);
                        //$updae->save();
                    }
                    if ($counter < 3) {
                        echo "<pre>";
                        print_r($data);
                        print_r($dataInserts);
                        die();
                    }
                }
            }
            $counter++;
        }
    }
    public function completeUnderCare($arrays)
    {

        $counter = 0;
        $outputarray = array();

        foreach ($arrays[0] as $row) {
            if ($counter == 0) {
                //  $outputarray[]=$row;
                //    print_r($row);
                // die();
            }


            if ($counter != 0) {
                $first_name = trim($row[0]);
                $last_name = trim($row[1]);
                $surplus1 = str_replace(",", "", str_replace("$", "", $row[9]));
                $cin = $row[3];
                $patient_status = $row[4];
                $agency_fk = $row[5];
                $file_date = $row[7];
                $application_link = $row[8];
                $check_by = $row[12];
                $recent_month = $row[13];
                $county = $row[11];


                $UnsercardID = $row[12];
                $medicareitem = $row[14];
                $undercaractionword = $row[13];
                $gender = $row[16];

                $note = $row[20];
                echo "<br/> - " . $rexordId = $row[21];




                $recordsObj = Record::where("first_name", $first_name)->where('last_name', $last_name)->where('agency_fk', $agency_fk)->first();
                if (!$recordsObj) {
                    $first_name = trim($row[1]);
                    $last_name = trim($row[0]);


                    $recordsObj = Record::where("first_name", $first_name)->where('last_name', $last_name)->where('agency_fk', $agency_fk)->first();
                }
                if (!$recordsObj) {
                    $first_name = $row[0];
                    $last_name = $row[1];



                    $recordsObj = Record::whereRaw("(first_name like '%" . $first_name . "%' or first_name like '%" . $last_name . "%')")->where('agency_fk', $agency_fk)->first();
                }
                if ($rexordId != "") {
                    $recordsObj = Record::where('id', $rexordId)->first();
                }


                if ($recordsObj && $rexordId != "") {
                    echo " - done";

                    //    print_r($row);
                    if ($application_link != "") {
                        $$application_link = str_replace('=HYPERLINK("', "", $application_link);
                        $$application_link = str_replace('","Link")', "", $application_link);
                    }


                    $update = array(
                        "cin" => $cin,
                        "patient_status" => $patient_status,
                        "application_link" => $application_link,
                        "county" => $county

                    );
                    if ($surplus1 != "") {
                        $update['surplus1'] = $surplus1;
                    }


                    if ($file_date != "") {
                        $fileDateArray = explode("/", $file_date);
                        if (count($fileDateArray) > 2) {
                            $file_date = $fileDateArray[2] . '-' . $fileDateArray[0] . '-' . $fileDateArray[1];
                            $update['file_date'] = $file_date;
                        }
                    }
                    if ($recent_month != "") {
                        $recent_monthArray = explode("/", $recent_month);
                        if (count($recent_monthArray) > 2) {

                            $recent_month = $recent_monthArray[2] . '-' . $recent_monthArray[0] . '-' . $recent_monthArray[1];
                            $update['recent_month'] = $recent_month;
                        }
                    }
                    if ($UnsercardID != "") {
                        $update['undercare_action'] = $UnsercardID;
                    }
                    if ($medicareitem != "") {
                        $update['medicaid_issue'] = $medicareitem;
                    }
                    if ($undercaractionword != "") {
                        $update['unsercar_action_org'] = $undercaractionword;
                    }
                    if ($gender != "") {
                        $update['gender'] = $gender;
                    }

                    //  print_r($update); die();





                    $outputarray[] = $row;

                    Record::where('id', $recordsObj->id)->update($update);
                    if ($note != "") {

                        $dataInserts = array();
                        $dataInserts[] = array('record_id' => $recordsObj->id, 'message' => $note, 'type' => 'EMC', 'created_at' => date('Y-m-d H:i:s'), 'created_by' => 1);
                        //      	echo  $note; die();

                        RecordNotes::insert($dataInserts);
                    }
                } else {
                    //
                    //                  echo "<br> -  ".$rexordId;

                    //   print_r($row);
                }
            }
            $counter++;
        }
        //    print_r($outputarray);
        echo "<br/>count " . count($outputarray);
        echo "<br/>";
        $this->drawTable($outputarray);





        die("BHEEM2");
    }

    public function drawTable($data)
    {
        echo "<style>table, th, td {
  border: 1px solid black;
} table {
  border-collapse: collapse;
}</style> <table>";
        foreach ($data as $row) {
            echo "<tr>";
            foreach ($row as $column) {
                # code...
                echo "<td>" . $column . "</td>";
            }
            echo "</tr>";
            # code...
        }
        echo "</table>";
    }


    public function documentAddByAgency(Request $request)
    {
        $user = auth()->user();
        $agencyId = $request->input('id');
        $document_name = $request->input('document_name');
        $masterArray = array(
            'user_id' => $agencyId,
            'name' => $document_name,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $user->id,
            'master_type_fk' => 9
        );
        $insert = new Master($masterArray);
        $insert->save();
        $insertId = $insert->id;
        if ($insertId) {
            Session::flash('success', 'Document successfully inserted');
            return redirect()->back();
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect()->back();
        }
    }

    public function documentUpdateByAgency(Request $request)
    {
        $user = auth()->user();
        $docId = $request->input('id');
        $agencyid = $request->input('agencyid');
        $document_name = $request->input('document_name');
        $masterArray = array(
            'name' => $document_name,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $user->id
        );
        $insert = Master::where('id', $docId)->update($masterArray);
        Session::flash('success', 'Document successfully updated.');
        return redirect()->back();
    }
    public function documentDeleteByAgency(Request $request, $id, $agencyId)
    {
        $user = auth()->user();
        $docId = $id;
        $masterArray = array(
            'del_flag' => 'Y',
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $user->id
        );
        $insert = Master::where('id', $docId)->update($masterArray);
        Session::flash('success', 'Document successfully deleted.');
        return redirect()->back();
    }
    function documentexport($id)
    {
        $users =  Master::getDocumentListByAgencyId($id);
        //echo "<pre>";print_r($users);die('ello');
        $filename = 'Agency' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('Document Name');
        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($users as $list) {
                fputcsv($file, array($list->name));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    function generateToken(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'notes' => 'required',

        ]);
        if ($validator->fails()) {
            return redirect("/agency-view/" . $request->input('agency_id'))
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {

            $token = $this->random_string(50);
            $getMasterDetails = Master::getDetailsById($request->notes);
            $finalArray = [
                'agency_id' => $request->input('agency_id'),
                'token' => $token,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $user->id,
                'notes' => $getMasterDetails->name,
                'ip_block' => $request->ip_block,
                'notes_id' => $request->notes,
            ];


            $ins_test = GenerateAgencyTokenHelper::insert($finalArray);
            $message = 'Token successfully generated.';

            if ($ins_test) {
                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => "Generate Token",
                    'link' => url('/agency/token-insert'),
                    'module' => 'Agency',
                    'object_id' => $request->input('agency_id'),
                    'message' => $user->first_name . ' ' . $user->last_name . " has generate token",
                    'new_response' => serialize($finalArray),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                Session::flash('success', $message);
                return redirect('/agency-view/' . $request->input('agency_id'));
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/agency-view/' . $request->input('agency_id'));
            }
        }
    }
    function random_string($length)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }
    // add domain
    function agencyWiseDomain(Request $request)
    {
        $data['page'] = $request->input('page');
        $data['query'] = AgencyWiseDomainHelper::domainListByAgencyId($request->input('agency_id'));

        return view("agency/domain_ajax_list", $data);
    }

    function agencyWiseNotification(Request $request)
    {
        // dd($request);
        $data['page'] = $request->input('page');
        $query = AgencyNotificationEmailHelper::notificationEmailByAgencyId($request->input('agency_id'));
        foreach ($query as $val) {
            $service_name = "";
            if ($val->service_id != "") {
                $explode = explode(',', $val->service_id);

                $getDetails = Master::geServiceName($val->service_id);
                $finals = [];
                foreach ($getDetails->toArray() as $names) {
                    $finals[] = $names['name'];
                }

                $service_name = implode(',', $finals);
            }

            $val->service_name = $service_name;
        }
        $data['query'] = $query;
        return view("agency/notification_email_ajax_list", $data);
    }

    function saveNotifictionEmail(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'email' =>  'required|email',

        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0],  'data' => array()], 400);
        } else {

            $patients = '';

            if (!empty($request->patient[0])) {
                $patients = implode(',', $request->patient);
            }

            $caregivers = '';
            if (!empty($request->caregiver[0])) {
                $caregivers = implode(',', $request->caregiver);
            }

            $patientStatus = null;
            if (!empty($request->patient[0])) {
                if (in_array('Status Update', $request->patient)) {
                    if (!empty($request->patient_status[0])) {
                        $patientStatus = implode(',', $request->patient_status);
                    }
                }
            }

            $caregiverStatus = null;
            if (!empty($request->caregiver[0])) {
                if (in_array('Status Update', $request->caregiver)) {
                    if (!empty($request->caregiver_status[0])) {
                        $caregiverStatus = implode(',', $request->caregiver_status);
                    }
                }
            }

            $data = array(
                'email' => $request->email,
                'agency_id' => $request->agency_id,
                'patients' => $patients,
                'caregivers' => $caregivers,
                'patients_id' => $request->patient_id,
                'caregivers_id' => $request->caregivers_id,

                'created_by' => Auth()->user()->id,
                'updated_by' => Auth()->user()->id,
                'patient_status' => $patientStatus,
                'caregiver_status' => $caregiverStatus,
            );
            $data['service_id'] = "";
            if ($request->service_id != "") {
                $data['service_id'] = implode(',', $request->service_id);
            }
            $discipline_id = "";
            if ($request->discipline_id != "") {
                $discipline_id = implode(',', $request->discipline_id);
            }
            $data['discipline_id'] = $discipline_id;
            if ($request->id != "") {
                $save = AgencyWiseNotifictionEmail::where('id', $request->id)->update($data);
            } else {
                $save = AgencyWiseNotifictionEmail::insertGetId($data);
            }

            if ($request->id != "") {
                $msg = 'Notification Email successfully updated';
                return response()->json(['error_msg' => $msg,  'data' => array()], 200);
            } else {
                $msg = 'Notification Email successfully added';
                if ($save) {
                    return response()->json(['error_msg' => $msg,  'data' => array()], 200);
                } else {
                    return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
                }
            }
        }
    }

    function saveDomain(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'domain' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0],  'data' => array()], 400);
        } else {
            $agencyId = $request->input('agency_id');

            if ($request->input('id') == '') {

                $final_array = array(
                    'agency_id' => $agencyId,
                    'domain' => $request->input('domain')
                );
                $save = AgencyWiseDomainHelper::save($final_array);

                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => "Add Domain",
                    'link' => url('/agency/agency-wise-domain-save'),
                    'module' => 'Agency',
                    'object_id' => $agencyId,
                    'message' => $user->first_name . ' ' . $user->last_name . " has added Domain",
                    'new_response' => serialize($final_array),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
            } else {

                $final_array = array(
                    'domain' => $request->input('domain')
                );
                $save1 = AgencyWiseDomainHelper::update($final_array, array('id' => $request->input('id')));
                $save = 1;

                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => "Update Domain",
                    'link' => url('/agency/agency-wise-domain-save'),
                    'module' => 'Agency',
                    'object_id' => $agencyId,
                    'message' => $user->first_name . ' ' . $user->last_name . " has updated Domain",
                    'new_response' => serialize($final_array),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
            }
            if ($save) {

                if ($request->input('id') != '') {
                    $msg = 'Domain successfully updated';
                } else {
                    $totalAgency = AgencyWiseDomainHelper::totalDomainAgency($request->input('agency_id'));
                    $messages = 'Hello ,<br>';
                    $messages .= 'Below new domain is added <br>';
                    $messages .= 'Added By :' . $user['first_name'] . ' ' . $user['last_name'] . ' <br>';

                    $messages .= 'Details: <br>';
                    $messages .= 'Agency Id: ' . $request->input('agency_id') . '<br>';
                    $messages .= 'Agency Name: ' . $request->input('agency_name') . '<br>';
                    $messages .= 'New Domain Name: ' . $request->input('domain') . '<br>';

                    $messages .= 'Thank you!';
                    $subject = "Create a New Domain";
                    $email = 'info@nybestmedicals.com';

                    $msg = 'Domain successfully inserted';
                }
                return response()->json(['error_msg' => $msg,  'data' => array()], 200);
            } else {
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
            }
        }
    }
    function domainDelete(Request $request)
    {
        $user = auth()->user();
        $data = array('del_flag' => 'Y');
        $getaAgencyDomainDetailById = AgencyWiseDomainHelper::getDetailsById($request->input('id'));
        $update = AgencyWiseDomainHelper::SoftDelete($data, array('id' => $request->input('id')));
        // $ipaddress = request()->getClientIp();
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => "Delete Domain",
            'link' => url('/agency/agency-domain-delete'),
            'module' => 'Agency',
            'object_id' => $getaAgencyDomainDetailById->agency_id,
            'message' => $user->first_name . ' ' . $user->last_name . " has deleted Domain",
            'new_response' => serialize($data),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        if ($update) {
            return response()->json(['error_msg' => 'Domain successfully deleted',  'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.',  'data' => array()], 200);
        }
    }
    // add domain

    // country block
    public function countrySave(Request $request)
    {
        $user = auth()->user();
        $input['agency_id'] = $agencyId = request('agency_id');
        $checkid = request('checkid');
        $selectedValues = request('selectedValues');
        $selectedValuesArray = explode(',', $selectedValues);
        AgencyCountry::where('agency_id', $agencyId)->delete();
        for ($i = 0; $i < count($checkid); $i++) {
            $input['country_id'] = $checkid[$i];
            $input['country_name'] =  trim($selectedValuesArray[$i]);
            $input['created_at'] = now();
            $agencyCountry = AgencyCountry::create($input);
        }
        if ($agencyCountry) {
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => "Country Block",
                'link' => url('/agency/agency-country-save'),
                'module' => 'Agency',
                'object_id' => $agencyId,
                'message' => $user->first_name . ' ' . $user->last_name . " has blocked country",
                'new_response' => serialize($input),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['error_msg' => 'Successfully Allowed Country',  'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.',  'data' => array()], 200);
        }
    }
    public function agencyWiseCountry(Request $request)
    {
        $data['query'] = AgencyCountry::agencyWiseCountry($request->input('agency_id'));

        return view("agency/country_ajax_list", $data);
    }
    // country block

    // ip address
    public function ipAddressSave()
    {
        $user = auth()->user();
        $input['agency_id'] = $agencyId = request('agency_id');
        $input['type'] = request('type');
        $input['ip_address'] = request('ip_address');
        $input['created_at'] = now();
        $input['created_by'] = auth()->user()->id;

        $agencyIpAddress = AgencyIpAddress::create($input);

        if ($agencyIpAddress) {
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => "Add IP Address",
                'link' => url('/agency/agency-ip-address-save'),
                'module' => 'Agency',
                'object_id' => $agencyId,
                'message' => $user->first_name . ' ' . $user->last_name . " has added IP Address",
                'new_response' => serialize($input),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['error_msg' => 'Ip Address successfully inserted',  'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.',  'data' => array()], 200);
        }
    }
    public function agencyWiseIpAddress(Request $request)
    {
        $data['query'] = AgencyIpAddress::agencyWiseIpAddress($request->input('agency_id'));
        return view("agency/ip_ajax_list", $data);
    }
    public function ipAddressDelete(Request $request)
    {
        $update = AgencyIpAddress::SoftDelete(array('delflag' => 'Y'), array('id' => $request->input('id')));

        if ($update) {
            return response()->json(['error_msg' => 'IP Address successfully deleted',  'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.',  'data' => array()], 200);
        }
    }
    public function ipAddressEdit(Request $request)
    {

        $edit = AgencyIpAddress::editIpData($request->id);

        if ($edit) {
            return response()->json(['error_msg' => 'Success',  'data' => $edit], 200);
        } else {
            return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.',  'data' => array()], 200);
        }
    }
    public function ipAddressUpdate(Request $request)
    {
        $input['ip_address'] = $request->ip_address_edit;
        $input['type'] = $request->type_edit;
        $input['updated_at'] = now();
        $input['updated_by'] = auth()->user()->id;


        $updateIp = AgencyIpAddress::updateIpData($input, $request->id);

        if ($updateIp) {
            return response()->json(['error_msg' => 'IP Address successfully updated',  'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.',  'data' => array()], 200);
        }
    }
    // ip address

    function agencyTwoFactor(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
        }

        $data = array(
            'two_factor_auth' => $request->input('status'),
        );
        $update = Agency::where('id', $request->input('id'))->update($data);
        if ($update) {
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => "Two Factor Authentication",
                'link' => url('/agency/agency-two-factor-enable-disable'),
                'module' => 'Agency',
                'object_id' => $request->input('id'),
                'message' => $user->first_name . ' ' . $user->last_name . " has added Agency's User",
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            return response()->json(['error_msg' => "Agency Two Factor Authentication successfully updated", 'status' => 1, 'data' => array()], 200);
        }
        return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
    }

    function agencyPasswordExpired(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
        }
        $data = array(
            'password_expired' => $request->input('status'),
        );
        $update = Agency::where('id', $request->input('id'))->update($data);
        if ($update) {
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => "Password Expired",
                'link' => url('/agency/add_user'),
                'module' => 'Agency',
                'object_id' => $request->input('id'),
                'message' => $user->first_name . ' ' . $user->last_name . " has added Agency's User",
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            return response()->json(['error_msg' => "Agency Password Expired successfully updated", 'status' => 1, 'data' => array()], 200);
        }
        return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
    }

    function  agencyUserExportCsv(Request   $request)
    {
        $UsersList = UserHelper::getAgencyWiseUserExport($request->id);

        $filename = 'AgencyUser' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('Record ID', 'Agency Name',  'First Name', 'Last Name', 'Email', 'Phone', 'Ext','Permission Type','Department','Status','Is Admin', 'Login Type', 'User Type');


        $callback = function () use ($UsersList, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $ct = 1;
            foreach ($UsersList as $list) {

                $isAdmin = "No";
                if($list->role_access ==1){
                    $isAdmin = "Yes";
                }
                fputcsv($file, array($ct++, $list->agency_name, $list->first_name, $list->last_name, $list->email,$list->phone,$list->ext, $list->record_access,$list->department,$list->active, $isAdmin,$list->login_type_fk, $list->user_type_fk));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function agencyLogoUpload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'agency-image' => 'required|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the mime types and max size as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()[0]], 400);
        }

        if ($request->file('agency-image') != '') {
            $files = $request->file('agency-image');
            $name = time() . '.' . $files->getClientOriginalExtension();
            if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
                $destination = public_path('allupload');
                $files->move($destination, $name);
                $img = $name;
            } else {
                Storage::disk('s3')->putFileAs('agency-image', $files, $name);
                $img = $name;
            }

            $update = Agency::where('id', $request->agency_id)->update(['agency_logo' => $img]);

            return response()->json(['message' => 'Image uploaded successfully'], 200);
        }
    }

    public function agencyWiseSmsSave(Request $request)
    {

        $updateData = [
            'send_sms_eng' => $request->send_sms_eng,
            'send_sms_spanish' => $request->send_sms_spanish,
            'appointment_send_book_eng' => $request->appointment_send_book_eng,
            'appointment_send_book_spanish' => $request->appointment_send_book_spanish,
            'tele_send_sms_eng' => $request->tele_send_sms_eng,
            'tele_send_sms_spanish' => $request->tele_send_sms_spanish,
            'tele_remind_send_sms_spanish' => $request->tele_remind_send_sms_spanish,
            'tele_remind_send_sms_eng' => $request->tele_remind_send_sms_eng,
        ];

        $update = Agency::where('id', $request->agency_id)->update($updateData);
        $msg = 'Sms successfully updated';
        return response()->json(['error_msg' => $msg,  'data' => []], 200);
    }

    function editEmailNotification(Request $request)
    {

        $query = AgencyNotificationEmailHelper::getDetailsById($request->id);
        return response()->json(['message' => 'Success', 'data' => $query], 200);
    }

    function deleteNotificationEmail(Request $request)
    {
        $auth = auth()->user();
        $deleted = AgencyNotificationEmailHelper::SoftDelete(array('delete_flag' => 'Y'), array('id' => $request->id));
        if ($deleted) {
            return response()->json(['error_msg' => "Successfully deleted",  'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
        }
    }

    public function agencyAlaycareStatus(Request $request)
    {
        $updateSmsStatus = Agency::where('id', $request['agency_id'])->update(['alaycare_status' => $request['is_alaycare']]);
        $status = Agency::select('alaycare_status')->where('id', $request['agency_id'])->first();
        // $this->AgencySkillService->SoftDelete(array('del_flag'=>'Y'),array('agency_id'=>$request->agency_id));
        return response()->json(['status' => true, 'data' => $status->alaycare_status, 'error_msg' => 'Sucessfully Updated'], 200);
    }

    public function agencyAlaycareDetailsSave(Request $request)
    {
        $updateAlaycareDetails = Agency::where('id', $request['agency_id'])->update(['alaycare_username' => $request['alaycare_username'], 'alaycare_password' => $request['alaycare_password'], 'alayacare_url' => $request['alaycare_url']]);
        return response()->json(['status' => true, 'error_msg' => 'Successfully Updated'], 200);
    }

    public function agencyWiseServiceSave(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'type' => 'required',
            'agency_service' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $insertData = [
            'agency_id' => $request->agency_id,
            'type' => $request->type,
            'service_id' => $request->agency_service,
            'name' => $request->name,
        ];

        if ($request->m_id != "") {
            $insertData['updated_date'] = date('Y-m-d H:i:s');
            $insertData['updated_by'] = auth()->user()->id;
            $msg = "Service Successfully update";
        } else {
            $insertData['created_date'] = date('Y-m-d H:i:s');
            $insertData['created_by'] = auth()->user()->id;
            $msg = "Service Successfully added";
        }

        $condition = [
            'id' => $request->m_id,
        ];

        $agencyWiseServiceSave = AgencyWiseService::updateOrInsert($condition, $insertData);

        if ($agencyWiseServiceSave) {
            return response()->json(['error_msg' => $msg,  'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
        }
    }

    public function ServiceAjaxList(Request $request)
    {

        $query = $this->AgencyWiseServiceService->getService($request->agency_id, 'paginate');

        return view('agency.agency-wise-service-list', ['query' => $query]);
    }

    public function deleteService(Request $request)
    {

        $deleted = $this->AgencyWiseServiceService->SoftDelete(array('del_flag' => 'Y'), array('id' => $request->id));

        if ($deleted) {
            $editService = AgencyWiseService::find($request->id);
            $update = $this->agencyWiseDisabledService->softDelete(array('del_flag'=>"Y"),array('agency_id'=>$editService->agency_id,'service_id'=>$editService->service_id));
            return response()->json(['error_msg' => "Successfully deleted",  'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
        }
    }

    public function editService(Request $request)
    {
        $editService = AgencyWiseService::find($request->id);

        return response()->json(['error_msg' => "Successfully deleted",  'data' => $editService], 200);
    }

    public function agencyRobortStatus(Request $request)
    {
        $updateRobortStatus = Agency::where('id', $request['agency_id'])->update(['robort_status' => $request['is_robort']]);
        $IsRobort = $request['is_robort'] == 0 ? 'disabled' : 'enabled';
        return response()->json(['status' => true, 'data' => $request['is_robort'], 'error_msg' => 'Sucessfully Updated'], 200);
    }

    public function agencyRobortDetailsSave(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'robort_username' => 'required',
            'robort_password' => 'required',
            'robort_granttype' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        } else {
            $updateRobortDetails = Agency::where('id', $request['agency_id'])->update(['robort_grant_type' => $request['robort_granttype'], 'robort_user_name' => $request['robort_username'], 'robort_user_password' => $request['robort_password']]);
            return response()->json(['status' => true, 'error_msg' => 'SuccessFully Updated'], 200);
        }
    }

    public function alayacareSkill(Request $request)
    {
        $data['agencyDetails'] = Agency::where('delete_flag', 'N')->where('id', $request->agency_id)->where('alaycare_status', 1)->first();
        $finalArray = [];
        if (isset($data['agencyDetails']->alaycare_username)) {
            $query = AlayacareHelper::getAllAlayaCareSkillByAgencyDetails($request->page, $data['agencyDetails']->alaycare_username, $data['agencyDetails']->alaycare_password);
            $finalArray = $query;
        }

        return response()->json(['status' => true, 'error_msg' => 'Insert SuccessFully', 'data' => $finalArray], 200);
    }

    function addAlayaAgencySkill(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'skill' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $save = 0;
        
        $this->AgencySkillService->SoftDelete(array('del_flag' => 'Y'), array('agency_id' => $request->agency_id));
        foreach ($request->skill as $skill) {
            $dataArray = [
                'agency_id' => $request->agency_id,
                'skill_id' => $skill,
            ];
            $save = $this->AgencySkillService->save($dataArray);
        }

        if ($save) {
            $agency_skill = $this->AgencySkillService->getSkillByAgencyId($request->agency_id);
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Add Alayacare Skill',
                'link' => url('/agency-add-skill'),
                'module' => 'Agency',
                'object_id' => $request->agency_id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has added alayacare skill',
                'new_response' => serialize($request->except('_token')),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Skill successfully added', 'data' => $agency_skill], 200);
        } else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
        }
    }

    function syncHHAVisit(Request $request)
    {
        $query = HHACaregiversHelper::getHHAVisit($request->agency_id);
        $agencyDetails  = Agency::where("id", $request->agency_id)->first();
    }

    function generateTokenList(Request $request)
    {
        $data['page'] = $request->page;
        $data['token_list'] = GenerateAgencyToken::getAllGenerateToken($request->agency_id);
        return view('agency.agency_token_list', $data);
    }

    function deleteToken(Request $request)
    {
        $delete = GenerateAgencyToken::deleteTokenById($request->id);
        if ($delete) {
            return response()->json(['status' => true, 'error_msg' => 'Token successfully deleted', 'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
        }
    }

    function getAllApicallUsingToken(Request $request)
    {
        $data['page'] = $request->page;
        $data['token_list'] = $this->tokenWiseApiCallService->getAllList($request->id);
        return view('agency.api_token_list', $data);
    }


    function tokenUpdateName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        } else {
            $getMasterDetails = Master::getDetailsById($request->name);
            $name = $getMasterDetails->name;
            $ins_test = GenerateAgencyTokenHelper::update(array('notes' => $name, 'notes_id' => $request->name), array('id' => $request->id));
            return response()->json(['error_msg' => "Token Name successfully updated",  'data' => array('name' => $name)], 200);
        }
    }

    public function hhaSaveAppDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency_app_name' => 'required',
            'agency_app_token' => 'required',
            'agency_app_key' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        } else {
            $updateData = Agency::where('id', $request['agency_id'])->update(['enable_hha' => $request['enable_hha'], 'app_name' => $request['agency_app_name'], 'app_token' => $request['agency_app_token'], 'app_key' => $request['agency_app_key']]);
            return response()->json(['status' => true, "data" => $updateData, 'error_msg' => 'App data saved sucessfully.'], 200);
        }
    }

    public function hhaSaveOfficeDetail(Request $request)
    {
        $updateData = Agency::where('id', $request['agency_id'])->update(['office_id' => $request['office_id'], 'office_name' => $request['office_name']]);
        return response()->json(['status' => true, "data" => $updateData, 'error_msg' => 'Office data saved sucessfully.'], 200);
    }

    public function hhaUpdateOfficeDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency_edit_app_name' => 'required',
            'agency_edit_app_token' => 'required',
            'agency_edit_app_key' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        } else {
            $updateData = Agency::where('id', $request['agency_id'])->update(['enable_hha' => 1, 'app_name' => $request['agency_edit_app_name'], 'app_token' => $request['agency_edit_app_token'], 'app_key' => $request['agency_edit_app_key']]);
            return response()->json(['status' => true, "data" => $updateData, 'error_msg' => 'App data updated sucessfully.'], 200);
        }
    }

    public function hhaAgencyOfficeList(Request $request)
    {
        $office = HHAOfficeHelper::getAllOffice($request->agency_id);
        return response()->json(['status' => true, "data" => $office, 'error_msg' => 'App data updated sucessfully.'], 200);
    }

    public function agencyWiseWebhookSave(Request $request)
    {
        $auth = auth()->user();
        $rules = [
            'webhook' => 'required|string',
        ];

        if ($request->authentication_type == 'basic_auth') {
            $rules['username'] = 'required';
            $rules['password'] = 'required';
        }

        if ($request->authentication_type == 'bearer_token') {
            $rules['token'] = 'required';
        }

        if ($request->authentication_type == 'bearer_token') {
            $rules['token'] = 'required';
        }

        if ($request->authentication_type == 'api_key') {
            $rules['key'] = 'required';
            $rules['value'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }

        $username = $request->username;
        $password = $request->password;
        if ($request->authentication_type == 'api_key') {
            $username = $request->key;
            $password = $request->value;
        }

        $data = [
            'webhook_url' => $request->webhook,
            'agency_id' => $request->agency_id,
            'authentication_type' => $request->authentication_type,
            'user_name' => $username,
            'password' => $password,
            'token' => $request->token,
            'id' => $request->id,
            'notification_type' => "Service Status",
        ];

        if ($request->id != "") {
            $data['updated_date'] = date('Y-m-d H:i:s');
            $data['updated_by'] = $auth->id;
            $msg = "Webhook successfully updated";
        } else {
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['created_by'] = $auth->id;
            $msg = "Webhook successfully added";
        }
        $update = $this->agencyWebHookService->save($data);
        if ($update) {
            return response()->json(['error_msg' => $msg,  'data' => []], 200);
        } else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again",  'data' => []], 500);
        }
    }

    public function loadAgencyWebHookList(Request $request)
    {
        $data['page'] = $request->page;
        $data['query'] = $this->agencyWebHookService->list($request->agency_id);
        return view('agency._partial.agency_web_hook.agency_web_hook_list', $data);
    }

    public function editAgencyWebHook(Request $request)
    {
        $query = $this->agencyWebHookService->detailById($request->id);
        return response()->json(['error_msg' => "",  'data' => $query], 200);
    }

    public function deleteAgencyWebHook(Request $request)
    {
        $delete = $this->agencyWebHookService->softDelete(array('del_flag' => 'Y'), array('id' => $request->id));
        if ($delete) {
            return response()->json(['error_msg' => "Webhook successfully deleted",  'data' => []], 200);
        } else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again",  'data' => []], 500);
        }
    }

    public function portalAgencySMSStatus(Request $request)
    {

        $updateSmsStatus = Agency::where('id', $request['agency_id'])->update(['is_portal_sms' => $request['is_sms']]);

        $getDetails  = $this->agencyWiseSMSNotificationService->getDetailsByAgencyId($request->agency_id);

        $flag = 0;
        $smsCaregiver = "";
        $smsPatient = "";
        if (!empty($request->sms_notification_caregiver[0])) {
            $smsCaregiver = implode(',', $request->sms_notification_caregiver);
        }
        if (!empty($request->sms_notification_patient[0])) {
            $smsPatient = implode(',', $request->sms_notification_patient);
        }
        $save['caregiver_sms_notification'] = $smsCaregiver;
        $save['patient_sms_notification'] = $smsPatient;
        $save['agency_id'] = $request->agency_id;
        if (isset($getDetails->id)) {
            $flag = 1;
            $save['updated_at'] = date('Y-m-d H:i:s');
            $save['updated_by'] = auth()->user()->id;
        }
        $this->agencyWiseSMSNotificationService->saveUpdateOrCreate($save);

        $IsSms = $request['is_sms'] == 0 ? 'disabled' : 'enabled';
        return response()->json(['status' => true, 'error_msg' => 'Sms ' . $IsSms . ' sucessfully'], 200);
    }

    public function agencyWisePortalList(Request $request)
    {
        $getDetails  = $this->agencyWiseSMSNotificationService->getDetailsByAgencyId($request->id);
        $final = [];
        if (isset($getDetails->id)) {
            if ($getDetails->caregiver_sms_notification != "") {
                $final['caregiver'] = $getDetails->caregiver_sms_notification;
            }

            if ($getDetails->patient_sms_notification != "") {
                $final['patient'] = $getDetails->patient_sms_notification;
            }
        }
        return response()->json(['status' => true, 'error_msg' => '', 'data' => $final], 200);
    }

    public function agencyHHAStatus(Request $request)
    {
        $updateData = Agency::where('id', $request['id'])->update(['enable_hha' => $request['status']]);
        return response()->json(['status' => true, 'error_msg' => 'HHA status updated sucessfully'], 200);
    }

    public function smsServiceById(Request $request)
    {
        $page = $request->page;
        $getAgencyWiseList = $this->AgencyWiseServiceService->getServiceNewWithPaginate($request->id, "");

        if(count($getAgencyWiseList) >0){
            $query =$getAgencyWiseList;
        }else{
            $query = Master::getAllDataByMasterTypeFkWithPaginate(array(11));
        }

        $disabledServices = $this->agencyWiseDisabledService->getAgencyWiseDisabledSMSServiceList($request->id);
        $disabledServices = $disabledServices->toArray();
        return view('agency._partial.sms_service_setting.disabled_ajax_list', compact('query','page','disabledServices'));
    }

    public function disabledStatusUpdate(Request $request){
        $validator = Validator::make($request->all(), [
            'service_id' => 'required',
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $query = $this->agencyWiseDisabledService->getDetailsByServiceWithAgencyId($request->id,$request->service_id);
            if(isset($query->id)){
                $update = $this->agencyWiseDisabledService->softDelete(array('del_flag'=>"Y"),array('agency_id'=>$request->id,'service_id'=>$request->service_id));
                $status ="SMS Service successfully enabled";
            }else{
                $update = $this->agencyWiseDisabledService->save(array('agency_id'=>$request->id,'service_id'=>$request->service_id));
                $status ="SMS Service successfully disabled";
            }

            return response()->json(['status' => true, 'error_msg' =>$status], 200);
        }
    }

    public function updateDocumentEmail(Request $request){
        $user = auth()->user();
        $getAgencyDetails = Agency::where('id',$request->id)->first();
        $update = Agency::where('id',$request->id)->update(array('document_email_notification'=>$request->document_email_notification,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$user->id));


        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Update Document Email',
            'link' => url('/update-document-email'),
            'module' => 'Agency',
            'object_id' => $request->id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has update Document Email',
            'new_response' => serialize($request->all()),
            'old_response' => serialize($getAgencyDetails->toArray()),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        return response()->json(['status' => true, 'error_msg' =>"Document email updated successfully",'data'=>array('email'=>$request->document_email_notification)], 200);
    }


    public function updateEfaxNo(Request $request){
        $user = auth()->user();
        $getAgencyDetails = Agency::where('id',$request->id)->first();
        $update = Agency::where('id',$request->id)->update(array('efax_no'=>$request->efax_no,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$user->id));


        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Update Efaxno',
            'link' => url('/update-efax-no'),
            'module' => 'Agency',
            'object_id' => $request->id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has update Efax No',
            'new_response' => serialize($request->all()),
            'old_response' => serialize($getAgencyDetails->toArray()),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        return response()->json(['status' => true, 'error_msg' =>"eFax number was updated successfully",'data'=>array('efax_no'=>$request->efax_no)], 200);
    }


    public function changeHubStatusUpdate(Request $request){
        if(isset($request['agency_id'])){
            Agency::where('id', $request['agency_id'])->update(['show_hub' => $request['show_hub']]);
            $message = '';
            $link = url('agency/view/') . $request->agency_id;
            $user = auth()->user();
            $message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has updated show in Hub toggle button.';
            $module = 'User';
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Show In Hub toggle update',
                'link' => $link ?? '',
                'module' => $module,
                'object_id' => $request->agency_id,
                'message' => $message,
                'new_response' => serialize(array('show_hub' => $request['show_hub'])),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Hub status updated successfully.'], 200);
        }
        else{
			return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 200);
		}
    }


    public function agencyUserBlockUnblock(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'user_ids' => 'required',
        ], [
            'agency_id.required' => 'Please select an agency.',
            'user_ids.required' => 'Please select at least one user.',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $status = ['active','unblock'];
            if(count($request->user_ids) >0){
                foreach($request->user_ids as $uid){
                    $query = UserHelper::getUserDetails($uid);
                    $active ="active";
                    if(in_array($query->active,$status)){
                        $active ="block";
                    }else{
                        if($query->active =='block'){
                            $active ="active";
                        }
                    }

                    $userData = ['active'=>$active];
                    User::where('id',$uid)->update(array('active'=>$active,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$user->id));

                    $ipaddress = Utility::getIP();
                    $insertLog = [
                        'type' => 'Agency User Status Change',
                        'link' => url('/agency-user-block-unblock'),
                        'module' => 'User',
                        'object_id' => $uid,
                        'message' => $user->first_name . ' ' . $user->last_name . ' has change status',
                        'old_response' => serialize($query->toArray()),
                        'new_response' => serialize($userData),
                        'ip' => $ipaddress,
                    ];
                    LogsService::save($insertLog);

                }
            }

            return response()->json(['error_msg'=>'Status updated successfully'],200);
        }
    }

    public function addUserCreatorEmail(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'data' => 'required',
            'agency_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {


            $data =[
                'agency_id'=>$request->agency_id,
                'data'=>implode(',',$request->data),
            ];

            $save = $this->userCreatorEmailNotificationService->save($data);
            if($save){
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Creator User Email Notification ',
                    'link' => url('/add-user-creator-email'),
                    'module' => 'Agency',
                    'object_id' => $request->agency_id,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added the Creator User Email Notification',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
                return response()->json(['error_msg'=>'Creator User Email Notification added successfully'],200);

            }else{
                return response()->json(['error_msg'=>'Sorry, something went wrong. Please try again.'],500);
            }
        }
    }

    public function listUserCreatorEmail(Request $request){
        $data['user_creator_email_list'] = $this->userCreatorEmailNotificationService->listUserEmail($request->agency_id);
        return view('agency/_partial/user_creator_email_notification_list', $data);
    }

    public function deleteUserCreatorEmail(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'agency_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $delete = $this->userCreatorEmailNotificationService->softDelete(array('del_flag'=>'Y'),array('id'=>$request->id,'agency_id'=>$request->agency_id));
            if($delete){
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Delete Creator User Email Notification ',
                    'link' => url('/delete-user-creator-email'),
                    'module' => 'Agency',
                    'object_id' => $request->agency_id,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has deleted the Creator User Email Notification',

                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
                return response()->json(['error_msg'=>'Creator User Email Notification deleted successfully'],200);
            }else{

                return response()->json(['error_msg'=>'Sorry, something went wrong. Please try again'],500);
            }

        }
    }

    public function assignNybestUserToAgency(Request $request){
        $user = auth()->user();
		$validator = Validator::make($request->all(), [
			'agency_id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		}else{
			$nybest_user_id = explode(',', $request->nybest_user_id);
            $oldnybestRes = $this->assignNyBestUserService->getAssignNybestUserId($request->agency_id);
			$this->assignNyBestUserService->softDelete(['agency_id' => $request->agency_id]);
			foreach($nybest_user_id as $ny_user_id){
				$data[] = array(
					'nybest_user_id' => $ny_user_id,
					'agency_id' => $request->agency_id,
					'created_at' => date('Y-m-d H:i:s'),
					'created_by' => auth()->user()->id,
				);
			}
            $this->assignNyBestUserService->insert($data);
			$nybestUserData = $this->assignNyBestUserService->getAssignNybestUser($request->agency_id);
			$newnybestRes = $this->assignNyBestUserService->getAssignNybestUserId($request->agency_id);
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Update Assign Ny Best User ',
                'link' => url('/update-nybest-user-data'),
                'module' => 'Agency',
                'object_id' => $request->agency_id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated assigned Ny Best user',
                'old_response' => serialize($oldnybestRes),
                'new_response' => serialize($newnybestRes),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
			return response()->json(['error_msg' =>'NYBEST user assignment has been updated successfully.','data' => $nybestUserData]);
		}
	}

    public function changePaymentTypeReport(Request $request){
        if(isset($request['agency_id'])){
            $type = $request['view_payment_report'] == 1 ? 'activate' : 'deactivate';
            Agency::where('id', $request['agency_id'])->update(['view_payment_report' => $request['view_payment_report']]);
            $message = '';
            $link = url('agency/view/') . $request->agency_id;
            $user = auth()->user();
            $message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has '.$type.' payment report.';
            $module = 'User';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'View Payement Report Status',
                'link' => $link ?? '',
                'module' => $module,
                'object_id' => $request->agency_id,
                'message' => $message,
                'new_response' => serialize(array('view_payment_report' => $request['view_payment_report'])),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Payment report '.$type.' successfully.'], 200);
        }
        else{
			return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 200);
		}
    }

    /**
     * Get active agencies (excluding specified agency)
     * Used for agency deletion/merge functionality
     */
    public function getActiveAgencies(Request $request)
    {
        try {
            $excludeAgencyId = $request->input('exclude_agency_id');
            $agencies = Agency::select('id', 'agency_name')
                ->where('delete_flag', 'N')
                ->when($excludeAgencyId, function($query) use ($excludeAgencyId) {
                    return $query->where('id', '!=', $excludeAgencyId);
                })
                ->orderBy('agency_name', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'agencies' => $agencies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error loading agencies: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users by agency
     * Returns list of users associated with an agency
     */
    public function getUsersByAgency(Request $request)
    {
        try {
            $agencyId = $request->input('agency_id');

            if (!$agencyId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Agency ID is required'
                ], 400);
            }

            $users = User::select(
                    'users.id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                )
                ->where('users.agency_fk', $agencyId)
                ->where('users.delete_flag', 'N')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => trim($user->first_name . ' ' . $user->last_name),
                        'email' => $user->email,
                        'user_type' => $user->user_type
                    ];
                });

            return response()->json([
                'status' => 'success',
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error loading users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merge users and delete agency
     * Updates selected users' agency_fk to target agency and deletes source agency
     */
    public function mergeUsersAndDeleteAgency(Request $request)
    {
        try {
            $user = auth()->user();

            // Validate request
            $validator = Validator::make($request->all(), [
                'agency_id' => 'required|integer|exists:agency,id',
                'target_agency_id' => 'required|integer|exists:agency,id',
                'users' => 'required|array|min:1',
                'users.*.user_id' => 'required|integer|exists:users,id',
                'users.*.create_domain' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $agencyId = $request->input('agency_id');
            $targetAgencyId = $request->input('target_agency_id');
            $usersData = $request->input('users');

            // Check if source and target agencies are different
            if ($agencyId == $targetAgencyId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Source and target agencies cannot be the same'
                ], 400);
            }

            // Get agency details
            $sourceAgency = Agency::find($agencyId);
            $targetAgency = Agency::find($targetAgencyId);

            if (!$sourceAgency || !$targetAgency) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Agency not found'
                ], 404);
            }

            \DB::beginTransaction();

            $mergedUsersCount = 0;

            // Update users
            foreach ($usersData as $userData) {
                $userId = $userData['user_id'];
                $createDomain = $userData['create_domain'];

                $userToUpdate = User::find($userId);

                if ($userToUpdate && $userToUpdate->agency_fk == $agencyId) {
                    // Update agency_fk
                    $userToUpdate->agency_fk = $targetAgencyId;
                    $userToUpdate->updated_by = $user->id;
                    $userToUpdate->updated_at = now();
                    $userToUpdate->old_agency_id = $agencyId;
                    $userToUpdate->save();
                    $mergedUsersCount++;
                    $ipaddress = Utility::getIP();
                    $insertLog = [
                        'type' => 'Agency Merge & Delete',
                        'link' => url('/agency/view/' . $agencyId),
                        'module' => 'Agency',
                        'object_id' => $targetAgencyId,
                        'message' => auth()->user()->first_name.' '.auth()->user()->last_name . ' merged user from ' . $sourceAgency->agency_name . ' to ' . $targetAgency->agency_name . ' and deleted the source agency ',
                        'new_response' => serialize([
                                    'source_agency_id' => $agencyId,
                                    'target_agency_id' => $targetAgencyId,
                                    'merged_users_count' => $mergedUsersCount,
                                    'users_data' => $userToUpdate
                                ]),
                        'ip' => $ipaddress,
                    ];
                    LogsService::save($insertLog);
                    // Handle domain creation if requested
                    if ($createDomain) {
                        // You can add domain creation logic here
                        $email = $userToUpdate->email;
                        $domain = substr(strrchr($email, "@"), 1);
                        AgencyWiseDomainHelper::createDomain($targetAgencyId,$domain);
                    }
                }
            }

            // Delete the source agency (soft delete)
            $sourceAgency->delete_flag = 'Y';
            $sourceAgency->deleted_at = now();
            $sourceAgency->deleted_by = $user->id;
            $sourceAgency->save();

            \DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully merged ' . $mergedUsersCount . ' user(s) and deleted agency'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateHHAMdoOrderDetails(Request $request){
        $user = auth()->user();
		$validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'agency_hha_client_id' => 'required',
            'agency_hha_client_secret' => 'required',
            'agency_hha_app_key' => 'required',
            'agency_hha_client_txt_id' => 'required',
        ], [
            'agency_id.required' => 'The agency ID is required.',
            'agency_hha_client_id.required' => 'Client ID is required.',
            'agency_hha_client_secret.required' => 'Client secret is required.',
            'agency_hha_app_key.required' => 'App key is required.',
            'agency_hha_client_txt_id.required' => 'Client txt ID is required.',
        ]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		}else{
            $getDetails = $this->hhaMDOService->getAllClientDetailsByAgencyId($request->agency_id);
            $oldResponse = [];
            $data = [
                'client_id'=>$request->agency_hha_client_id,
                'client_secret'=>$request->agency_hha_client_secret,
                'api_token'=>$request->agency_hha_app_key,
                'txtID'=>$request->agency_hha_client_txt_id,

            ];
            if(isset($getDetails->id) && $getDetails->id !=""){
                $update = $this->hhaMDOService->update($data,['agency_id'=>$request->agency_id,'id'=>$getDetails->id]);
                $data['is_status'] = $getDetails->is_status;
                $oldResponse = $getDetails->toArray();
            }else{
                $data['agency_id']=$request->agency_id;
                $data['is_status'] =1;
                $update = $this->hhaMDOService->save($data);
            }

            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => "Update HHA MDO Credential",
                'link' => url('/agency/update-hha-md-details'),
                'module' => 'Agency',
                'object_id' => $request->input('agency_id'),
                'message' => $user->first_name . ' ' . $user->last_name . " has update hha mdo data",
                'new_response' => serialize($data),
                'old_response' => serialize($oldResponse),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            if ($update) {
                return response()->json(['error_msg' => "HHA Mdo detail successfully updated",  'data' =>$data], 200);
            } else {
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again",  'data' => []], 500);
            }
        }
    }

    public function disabledHHAMdoOrder(Request $request){
        $user = auth()->user();
        $getDetails = $this->hhaMDOService->getAllClientDetailsByAgencyId($request->id);
        if(isset($getDetails->id)){
            $this->hhaMDOService->update(['is_status'=>$request->status],['id'=>$getDetails->id]);
            $enabled = $request->status == 1 ? 'enabled' : 'disabled';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => "Change HHA MDO Credential",
                'link' => url('/agency/disabled-hha-md-details'),
                'module' => 'Agency',
                'object_id' => $request->id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has ' . $enabled . ' HHA MDO data.',
                'new_response' => serialize(['is_status' => $request->status]),
                'old_response' => serialize($getDetails->toArray()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['error_msg' => "HHA MDO detail ".$enabled." successfully updated.",  'data' =>[]], 200);
        }else{
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again",  'data' =>[]], 500);
        }

    }

    public function statusChangeTaskHealth(Request $request){
        if(isset($request['agency_id'])){
            $type = $request['enable_task_health'] == 1 ? 'activate' : 'deactivate';
            $this->agencyTaskHealthService->changeStatus($request['agency_id'],$request->enable_task_health);
            $message = '';
            $link = url('agency/view/') . $request->agency_id;
            $user = auth()->user();
            $message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has '.$type.' payment report.';
            $module = 'User';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => $type.' Task health Status',
                'link' => $link ?? '',
                'module' => $module,
                'object_id' => $request->agency_id,
                'message' => $message,
                'new_response' => serialize(array('enable_task_health' => $request['enable_task_health'])),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Task health api '.$type.' successfully.'], 200);
        }
        else{
			return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 200);
		}
    }

    public function statusChangeRestrictServiceRequestUpdate(Request $request){
        if(isset($request['agency_id'])){
            $type = $request['restrict_service_request_update'] == 1 ? 'enabled' : 'disabled';
            $this->agencyService->update(['restrict_service_request_update' => $request['restrict_service_request_update']], ['id' => $request['agency_id']]);
            $link = url('agency/view/') . $request->agency_id;
            $user = auth()->user();
            $message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has '.$type.' restrict service request update.';
            $module = 'User';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => $type.' Restrict Service Request Update',
                'link' => $link ?? '',
                'module' => $module,
                'object_id' => $request->agency_id,
                'message' => $message,
                'new_response' => serialize(array('restrict_service_request_update' => $request['restrict_service_request_update'])),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Restrict service request update '.$type.' successfully.'], 200);
        }
            return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 500);
    }

    public function toggleAiCallLogs(Request $request){
        if(isset($request['agency_id'])){
            $type = $request['ai_call_logs_enabled'] == 1 ? 'enabled' : 'disabled';
            $this->agencyService->update(['ai_call_logs_enabled' => $request['ai_call_logs_enabled']], ['id' => $request['agency_id']]);
            $link = url('agency/view/') . $request->agency_id;
            $user = auth()->user();
            $message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has '.$type.' AI Call Logs for agency.';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => $type.' AI Call Logs',
                'link' => $link ?? '',
                'module' => 'Agency',
                'object_id' => $request->agency_id,
                'message' => $message,
                'new_response' => serialize(array('ai_call_logs_enabled' => $request['ai_call_logs_enabled'])),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'AI Call Logs '.$type.' successfully.'], 200);
        }
        return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 500);
    }

    public function togglePortalArchive(Request $request){
        if(isset($request['agency_id'])){
            $type = $request['enable_portal_archive'] == 1 ? 'activate' : 'deactivate';
            $this->agencyService->update(['enable_portal_archive' => $request['enable_portal_archive']], ['id' => $request['agency_id']]);
            $link = url('agency/view/') . $request->agency_id;
            $user = auth()->user();
            $message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has '.$type.'d portal archive for agency.';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => $type.' Portal Archive',
                'link' => $link ?? '',
                'module' => 'Agency',
                'object_id' => $request->agency_id,
                'message' => $message,
                'new_response' => serialize(array('enable_portal_archive' => $request['enable_portal_archive'])),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Portal archive '.$type.'d successfully.'], 200);
        }
        else{
            return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 500);
        }
    }

    public function toggleReview(Request $request){
        if(isset($request['agency_id'])){
            $type = $request['enable_review'] == 1 ? 'activate' : 'deactivate';
            $this->agencyService->update(['enable_review' => $request['enable_review']], ['id' => $request['agency_id']]);
            $link = url('agency/view/') . $request->agency_id;
            $user = auth()->user();
            $message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has '.$type.'d review functionality for agency.';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type'         => $type . ' Review',
                'link'         => $link ?? '',
                'module'       => 'Agency',
                'object_id'    => $request->agency_id,
                'message'      => $message,
                'new_response' => serialize(['enable_review' => $request['enable_review'],'agency_id' => $request['agency_id']]),
                'ip'           => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Review functionality '.$type.'d successfully.'], 200);
        } else {
            return response()->json(['status' => false, 'error_msg' => 'Something went wrong.'], 500);
        }
    }

    public function statusChangeFileManager(Request $request){
        if(isset($request['agency_id'])){
            $type = $request['enable_file_manager'] == 1 ? 'activate' : 'deactivate';
            $this->agencyService->update(['enable_file_manager' => $request['enable_file_manager']], ['id' => $request['agency_id']]);
            $link = url('agency/view/') . $request->agency_id;
            $user = auth()->user();
            $message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has '.$type.'d file manager for agency.';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => $type.' File Manager',
                'link' => $link ?? '',
                'module' => 'Agency',
                'object_id' => $request->agency_id,
                'message' => $message,
                'new_response' => serialize(array('enable_file_manager' => $request['enable_file_manager'])),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'File manager '.$type.'d successfully.'], 200);
        }
        else{
            return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 500);
        }
    }

    public function toggleTelehealthSendSms(Request $request){
        if(isset($request['agency_id'])){
            $agency = $this->agencyService->getDetailsByAgencyId($request['agency_id']);
            if(!$agency){
                return response()->json(['status' => false, 'error_msg' => 'Agency not found.'], 404);
            }
            $newValue = $agency->is_telehealth_send_sms == 1 ? 0 : 1;
            $type = $newValue == 1 ? 'activate' : 'deactivate';
            $this->agencyService->update(['is_telehealth_send_sms' => $newValue], ['id' => $request['agency_id']]);
            $link = url('/agency/toggle-telehealth-send-sms');
            $user = auth()->user();
            $message = $user->first_name . ' ' . $user->last_name . ' has '.$type.'d telehealth send sms for agency.';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => $type.' Telehealth Send Sms',
                'link' => $link ?? '',
                'module' => 'Agency',
                'object_id' => $request->agency_id,
                'message' => $message,
                'old_response' => serialize($agency),
                'new_response' => serialize(array('is_telehealth_send_sms' => $newValue)),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Telehealth send sms '.$type.'d successfully.', 'new_value' => $newValue], 200);
        }
        else{
            return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 500);
        }
    }

    public function updateVisitingDetails(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'app_user_key' => 'required',
            'app_user_password' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        } else {
            $getDetails = $this->agencyWiseVistingClientService->getDetailsByAgencyId($request['agency_id']);
            $oldResponse = [];
            $finalData = [
                'app_user_key'=>$request->app_user_key,
                'app_user_password'=>$request->app_user_password
            ];
            if(isset($getDetails->id)){
                $this->agencyWiseVistingClientService->update($finalData,array('id'=>$getDetails->id,'agency_id'=>$request['agency_id']));
                $oldResponse = $getDetails->toArray();
            }else{
                $finalData['status'] = 1;
                $finalData['agency_id'] = $request['agency_id'];
                $this->agencyWiseVistingClientService->save($finalData);
            }
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Update Visiting Aid',
                'link' => url('/agency/app-visting-detail-update'),
                'module' => 'Agency',
                'object_id' =>$request['agency_id'],
                'message' => $user->first_name . ' ' . $user->last_name . ' has added Agency',
                'old_response' => serialize($oldResponse),
                'new_response' => serialize($request->all()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, "data" => $finalData, 'error_msg' => 'Visiting updated sucessfully.'], 200);
        }
    }

    public function enabledDisabledVisitingAid(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        } else {
            $getDetails = $this->agencyWiseVistingClientService->getDetailsByAgencyId($request['id']);
            if(isset($getDetails->id)){
                $status =1;
                if($getDetails->status ==1){
                    $status =0;
                }

                $this->agencyWiseVistingClientService->update(['status'=>$status],array('id'=>$getDetails->id,'agency_id'=>$request['id']));
                $updatedResponse = $this->agencyWiseVistingClientService->getDetailsByAgencyId($request['id']);
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Enabled Disabled Visiting Aid',
                    'link' => url('/agency/enabled-disabled-app-visting'),
                    'module' => 'Agency',
                    'object_id' => $request['id'],
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added Agency',
                    'old_response' => serialize($getDetails->toArray()),
                    'new_response' => serialize($updatedResponse->toArray()),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
                return response()->json(['status' => true, "data" =>['status'=>$status], 'error_msg' => 'Visiting updated sucessfully.'], 200);
            }else{
                return response()->json(['status' => false, "data" => [], 'error_msg' => 'Record does not found'], 404);
            }
        }
    }

    public function saveAgencyPocDocumentType(Request $request){
        $getAgencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if(isset($getAgencyDetails->id)){
            $oldDocTypeId = $getAgencyDetails->poc_document_type_id;
            $getAgencyDetails->poc_document_type_id   = $request->poc_document_type_id;
            $getAgencyDetails->poc_document_type_name = $request->poc_document_type_name;
            $getAgencyDetails->save();

            $user = auth()->user();
            LogsService::save([
                'type'         => 'Agency POC Document Type Update',
                'link'         => url('save-poc-document-type'),
                'module'       => 'Agency Task Health Setting',
                'object_id'    => $getAgencyDetails->id,
                'message'      => $user->first_name . ' ' . $user->last_name . ' updated POC document type for agency ' . $getAgencyDetails->id . ' (from ' . ($oldDocTypeId ?: 'none') . ' to ' . $request->poc_document_type_id . ')',
                'old_response' => serialize(['poc_document_type_id' => $oldDocTypeId]),
                'new_response' => serialize(['poc_document_type_id' => $request->poc_document_type_id]),
                'ip'           => Utility::getIP(),
            ]);

            return response()->json(['status'=>true,'error_msg'=>'Document type saved successfully'],200);
        }
        return response()->json(['status'=>false,'error_msg'=>'Agency not found'],500);
    }

    public function savePatientAssessmentDocumentType(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }
        $agencyDetails->patient_assessment_document_type_id   = $request->document_type_id;
        $agencyDetails->patient_assessment_document_type_name = $request->document_type_name;
        $agencyDetails->save();
        return response()->json(['status' => true, 'error_msg' => 'Patient Assessment document type saved successfully'], 200);
    }

    public function saveCms485DocumentType(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }
        $agencyDetails->cms_485_document_type_id   = $request->document_type_id;
        $agencyDetails->cms_485_document_type_name = $request->document_type_name;
        $agencyDetails->save();
        return response()->json(['status' => true, 'error_msg' => 'CMS 485 document type saved successfully'], 200);
    }

    public function saveEmergencyKardexDocumentType(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }
        $agencyDetails->emergency_kardex_document_type_id   = $request->document_type_id;
        $agencyDetails->emergency_kardex_document_type_name = $request->document_type_name;
        $agencyDetails->save();
        return response()->json(['status' => true, 'error_msg' => 'Emergency Kardex document type saved successfully'], 200);
    }

    public function saveSupervisionSimpleDocumentType(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }
        $agencyDetails->supervision_document_type_id   = $request->document_type_id;
        $agencyDetails->supervision_document_type_name = $request->document_type_name;
        $agencyDetails->save();
        return response()->json(['status' => true, 'error_msg' => 'Supervision document type saved successfully'], 200);
    }

    public function savePatientPackageDocumentType(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }
        $agencyDetails->patient_package_document_type_id   = $request->document_type_id;
        $agencyDetails->patient_package_document_type_name = $request->document_type_name;
        $agencyDetails->save();
        return response()->json(['status' => true, 'error_msg' => 'Patient Welcome Package document type saved successfully'], 200);
    }

    public function getSupervisionDocumentTypes(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }
        $docTypes = HHACaregiversHelper::getCaregiverDocumentType($agencyDetails->id);
        $data = [];
        if (!empty($docTypes)) {
            foreach ($docTypes as $doc) {
                $data[] = [
                    'document_id'   => $doc['id'],
                    'document_name' => $doc['name'],
                ];
            }
        }
        return response()->json([
            'status'    => true,
            'error_msg' => 'Success',
            'data'      => $data,
            'selected'  => $agencyDetails->supervision_document_type_id,
        ], 200);
    }

    public function saveSupervisionDocumentType(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }
        $oldDocTypeId   = $agencyDetails->supervision_document_type_id;
        $oldDocTypeName = $agencyDetails->supervision_document_type_name;
        $agencyDetails->supervision_document_type_id   = $request->supervision_document_type_id;
        $agencyDetails->supervision_document_type_name = $request->supervision_document_type_name;
        $agencyDetails->save();

        $user = auth()->user();
        LogsService::save([
            'type'         => 'Agency Supervision Document Type Update',
            'link'         => url('save-supervision-document-type'),
            'module'       => 'Agency Task Health Setting',
            'object_id'    => $agencyDetails->id,
            'message'      => $user->first_name . ' ' . $user->last_name . ' updated Supervision document type for agency ' . $agencyDetails->id . ' (from "' . ($oldDocTypeName ?: 'none') . '" to "' . $request->supervision_document_type_name . '")',
            'old_response' => serialize(['supervision_document_type_id' => $oldDocTypeId, 'supervision_document_type_name' => $oldDocTypeName]),
            'new_response' => serialize(['supervision_document_type_id' => $request->supervision_document_type_id, 'supervision_document_type_name' => $request->supervision_document_type_name]),
            'ip'           => Utility::getIP(),
        ]);

        return response()->json(['status' => true, 'error_msg' => 'Supervision document type saved successfully'], 200);
    }

    public function getOtherComplianceTypes(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);

        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }
        $caregiver = HHACaregivers::select('officeId')->where('agency_fk', $agencyDetails->id)->where('hha_delete_flag', 'N')->first();
        $officeId = $caregiver->officeId ?? 0;
        $complianceTypes = HHACaregiversHelper::getCaregiverOtherCompliance($agencyDetails->id, $officeId);
        $data = [];
        if (!empty($complianceTypes)) {
            foreach ($complianceTypes as $item) {
                $data[] = [
                    'id'   => $item['id'],
                    'name' => $item['name'],
                ];
            }
        }
        return response()->json([
            'status'    => true,
            'error_msg' => 'Success',
            'data'      => $data,
            'selected'  => $agencyDetails->medical_id,
        ], 200);
    }

    public function saveMedicalId(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }
        $agencyDetails->medical_id   = $request->medical_id;
        $agencyDetails->medical_name = $request->medical_name;
        $agencyDetails->save();
        return response()->json(['status' => true, 'error_msg' => 'Other compliance type saved successfully'], 200);
    }

    public function getAgencyOtherComplianceMedicals(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }

        $existing = $this->agencyOtherComplianceMedicalService->getByAgencyId($agencyDetails->id);
        $options  = $this->agencyOtherComplianceMedicalService->getHHAOptions($agencyDetails->id);

        return response()->json([
            'status'  => true,
            'data'    => $existing,
            'options' => $options,
        ], 200);
    }

    public function getAgencyComplianceMedicalResults(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }

        $medicaid_id = $request->input('medicaid_id', $agencyDetails->medical_id);
        $officeID    = $agencyDetails->office_id ?? 0;

        $data = $this->agencyOtherComplianceMedicalService->getMedicalResults($agencyDetails->id, $medicaid_id, $officeID);

        return response()->json([
            'status'   => true,
            'data'     => $data,
            'selected' => $agencyDetails->medical_result_id,
        ], 200);
    }

    public function getAgencyMedicalAndResult(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }

        $data = $this->agencyOtherComplianceMedicalService->getEditData($agencyDetails);

        return response()->json(['status' => true] + $data, 200);
    }

    public function saveAgencyMedicalAndResult(Request $request)
    {
        $agencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
        if (!isset($agencyDetails->id)) {
            return response()->json(['status' => false, 'error_msg' => 'Agency not found'], 500);
        }

        $saved = $this->agencyOtherComplianceMedicalService->saveBoth(
            $agencyDetails,
            $request->input('medicals', []),
            $request->input('medical_result_id'),
            $request->input('medical_result_name', '')
        );

        return response()->json(['status' => true, 'error_msg' => 'Saved successfully', 'data' => $saved], 200);
    }

    // ── Agency Notes ────────────────────────────────────────────────────────────

    // Active notes only — used by patient view & patient add page
    public function getAgencyNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
        }
        $notes = $this->agencyNoteService->getActiveByAgency($request->agency_id);
        return response()->json(['status' => true, 'data' => $notes], 200);
    }

    // All notes (active + inactive) — used by agency admin view tab
    public function getAllAgencyNotes(Request $request)
    {
        if (!auth()->user()->can('agency-notes-list')) {
            return response()->json(['status' => false, 'error_msg' => 'Unauthorized.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
        }
        $notes = $this->agencyNoteService->getByAgency($request->agency_id);
        return response()->json(['status' => true, 'data' => $notes], 200);
    }

    public function addAgencyNote(Request $request)
    {
        if (!auth()->user()->can('agency-notes-add')) {
            return response()->json(['status' => false, 'error_msg' => 'Unauthorized.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required|integer',
            'note'      => 'required|string|max:1000',
            'note_type' => 'required|in:info,warning,danger',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
        }
        $note = $this->agencyNoteService->addNote([
            'agency_id' => $request->agency_id,
            'note'      => $request->note,
            'note_type' => $request->note_type,
        ]);
        if($note){
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Add Agency Note',
                'link' => url('/agency/add-note'),
                'module' => 'Agency',
                'object_id' => $request->agency_id,
                'message' => auth()->user()->first_name.' '.auth()->user()->last_name . ' added a note for agency.',
                'new_response' => serialize($note),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
        return response()->json(['status' => true, 'error_msg' => 'Note added successfully.', 'data' => $note], 200);
            }
            return response()->json(['status' => false, 'error_msg' => 'Failed to add note. Please try again.'], 500);

    }

    public function toggleAgencyNote(Request $request)
    {
        if (!auth()->user()->can('agency-notes-toggle')) {
            return response()->json(['status' => false, 'error_msg' => 'Unauthorized.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'note_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
        }
        $note = $this->agencyNoteService->toggleActive($request->note_id);
        $msg = $note->is_active ? 'Note activated.' : 'Note deactivated.';
        if($note){
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Toggle Agency Note',
                'link' => url('/agency/toggle-note'),
                'module' => 'Agency',
                'object_id' => $note->agency_id,
                'message' => auth()->user()->first_name.' '.auth()->user()->last_name . ' has '.$msg,
                'new_response' => serialize($note),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
        return response()->json(['status' => true, 'error_msg' => $msg, 'is_active' => $note->is_active], 200);
        }
        return response()->json(['status' => false, 'error_msg' => 'Something went wrong. Please try again.'], 500);
    }

    public function deleteAgencyNote(Request $request)
    {
        if (!auth()->user()->can('agency-notes-delete')) {
            return response()->json(['status' => false, 'error_msg' => 'Unauthorized.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'note_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
        }
        $this->agencyNoteService->softDelete($request->note_id);
        return response()->json(['status' => true, 'error_msg' => 'Note deleted successfully.'], 200);
    }

    public function changeReportingToolStatus(Request $request)
    {
        if (isset($request['agency_id'])) {
            $type = $request['show_reporting_tool'] == 1 ? 'activated' : 'deactivated';
            Agency::where('id', $request['agency_id'])->update(['show_reporting_tool' => $request['show_reporting_tool']]);
            $user = auth()->user();
            $message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has ' . $type . ' the Reporting Tool.';
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type'         => 'Reporting Tool Status',
                'link'         => url('agency/view/') . $request->agency_id,
                'module'       => 'Agency',
                'object_id'    => $request->agency_id,
                'message'      => $message,
                'new_response' => serialize(['show_reporting_tool' => $request['show_reporting_tool']]),
                'ip'           => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Reporting Tool ' . $type . ' successfully.'], 200);
        }
        return response()->json(['status' => false, 'error_msg' => 'Something went wrong.'], 500);
    }

    public function searchAgencyWiseUser(Request $request){
      
       $query = $this->userService->fetchAgencyUserListByAgencyId($request->all());
       $final = [];
       if(!empty($query[0])){
        foreach($query as $val){
            $temp = [];
            $temp['id'] = $val->id;
            $temp['name'] = $val->first_name.' '.$val->last_name;
            $temp['agency_id'] = $val->agency_fk;
            $final[] = $temp;
        }
       }
       return json_encode($final);
    }
}
