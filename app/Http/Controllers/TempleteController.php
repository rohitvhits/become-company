<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Mail;
use URL;
use App\Helpers\EsignHelper;
use App\Template;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\DocumentType;
use App\DocumentSentReport;
use App\PDF;
use App\DocumentSignerMaster;
use App\User;
use App\TemplateLog;
use App\Services\LogsService;
use App\Services\DocumentSignerService;
use App\Services\DocumentSendService;
use App\Services\DynamicFormLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Agency;

use App\Model\AgencyMaster;
use App\Model\WriteDocument;
use App\Services\TemplateService;
use App\Services\AgencyAllFormService;
use App\Services\EsignReportService;
use App\Helpers\Utility;
use App\Services\PatientService;
use App\Model\signatureUpload;
use App\Services\DocumentPatientService;
use App\Services\WriteDocumentService;
use Carbon\Carbon;
use App\Services\DocumentUploadHistoryService;
use App\Services\CommonEsignService;
use Illuminate\Support\Facades\File;
use App\Services\AgencyMasterService;
use App\Services\AgencyService;
class TempleteController extends Controller
{
	protected $documentSignerService, $documentSendService, $dynamicFormLogService,$templateService,$agencyAllFormService,$esignReportService,$patientService,$documentPatientService,$writeDocumentService = "";
	protected $documentUploadHistoryService;
	protected $commonEsignService;
	protected $agencyMasterService;
	protected $agencyService;
	protected const DOCUSIGN_FOLDER = 'dosusinguploads/docusign';

	public function __construct(DocumentSignerService $documentSignerService, DocumentSendService $documentSendService, DynamicFormLogService $dynamicFormLogService,TemplateService $templateService,AgencyAllFormService $agencyAllFormService,EsignReportService $esignReportService,PatientService $patientService,DocumentPatientService $documentPatientService,WriteDocumentService $writeDocumentService,DocumentUploadHistoryService $documentUploadHistoryService,CommonEsignService $commonEsignService,AgencyMasterService $agencyMasterService,AgencyService $agencyService)
	{
		$this->middleware('permission:template-list|template-add|template-edit|template-delete|template-view|template-singer', ['only' => ['index', 'insert', 'document', 'insertReceiptSigner']]);
		$this->middleware('permission:template-list', ['only' => ['index']]);
		$this->middleware('permission:template-add', ['only' => ['add_template', 'insert']]);
		$this->middleware('permission:template-edit', ['only' => ['edit_template', 'update']]);
		$this->middleware('permission:template-delete', ['only' => ['delete']]);
		$this->middleware('permission:template-view', ['only' => ['document']]);
		$this->middleware('permission:template-singer', ['only' => ['DocumentSendByType', 'insertReceiptSigner']]);

		$this->middleware('auth', ['except' => ['upload_documentweb', 'docums', 'thankyou', 'upload_documentwebNew', 'getResponseCanvas','getResponseCanvasNew','getpdfbyTemplateid','getResponseCanvasNew1']]);
	
		$this->documentSignerService = $documentSignerService;
		$this->documentSendService = $documentSendService;
		$this->dynamicFormLogService = $dynamicFormLogService;
		$this->templateService = $templateService;
		$this->agencyAllFormService = $agencyAllFormService;
		$this->esignReportService = $esignReportService;
		$this->patientService = $patientService;
		$this->documentPatientService = $documentPatientService;
		$this->writeDocumentService = $writeDocumentService;
		$this->documentUploadHistoryService = $documentUploadHistoryService;
		$this->commonEsignService = $commonEsignService;
		$this->agencyMasterService = $agencyMasterService;
		$this->agencyService = $agencyService;
	}

	public function index(Request $request)
	{

		$data['user'] = $admin_login = auth()->user();
		$data['agencyList'] = Agency::getAllAgencyList();

		if ($admin_login['user_type_fk'] != 3 && $admin_login['user_type_fk'] != 4 && $admin_login['user_type_fk'] != 184) {
			abort(404);
		}
		
		return view('template.templete_list', $data);
	}

	public function template_report(Request $request)
	{

		$data['user'] = $admin_login = auth()->user();

		if ($admin_login['user_type_fk'] != 3 || $admin_login['user_type_fk'] == 184) {
			abort(404);
		}
		$data['document_list'] = DocumentType::where('del_flag', 'N')->get();
		return view('templete_report', $data);
	}

	public function insert(Request $request)
	{
		$data['user'] = $admin_login = auth()->user();

		$validator = Validator::make($request->all(), [
			'template_name' => 'required',
			'document_type' => 'required'
		]);
		if ($validator->fails()) {
			return redirect("template-add")
				->withErrors($validator, 'template')
				->withInput();
		} else {
			
			$resouce = 'No';
			if (request('resource_tabe') == 'on') {
				$resouce = 'Yes';
			}
			
			$show_verify_by= "N";
			if(isset($request->show_verify_by) && $request->show_verify_by =='Y'){
				$show_verify_by = $request->show_verify_by;
			}

			$send_caregiver_email= "N";
			if(isset($request->send_caregiver_email) && $request->send_caregiver_email =='Y'){
				$send_caregiver_email = $request->send_caregiver_email;
			}
			
			$sub_data = array(
				"custom_form_id" => request("custom_form_id") ?? null,
				"template_name" => request("template_name"),
				"document_type" => request("document_type"),
				"upload_document" => request("attached_files"),
				"remark" => request("remark"),
				"lookup_fields" => request('lookup_field'),
				"created_date" => date('Y-m-d h:i:s'),
				"created_by" => $admin_login['id'],
				'esign' => $request->esign,
				'resouce_tab' => $resouce,
				'email_notification' => request('email_notification'),
				'checkbox_mark_flag'=>$request->checkbox_mark_flag,
				'show_verify_by'=>$show_verify_by,
				'send_caregiver_email'=>$send_caregiver_email,
				'resolution_update'=>$request->resolution_update??"N",
				'esign_workflow'=>$request->esign_workflow ?? 'normal',
				'template_type'=>$request->template_type
			);

			$custom_template= 1;
			if($request->custom_template ==1){
				$custom_template= 0;
			}
			$sub_data['custom_template'] =$custom_template;
			if(!empty(request('agency_id')[0])){
				$sub_data['agency_id'] =implode(',',request('agency_id'));
			}
		
			$insert = $this->templateService->save($sub_data);

			if ($insert) {
				
				$insertLog = [
					'type' => 'Add',
					'link' => url('/template-add'),
					'module' => 'Template',
					'object_id' => $insert,
					'message' => $admin_login->first_name . ' ' . $admin_login->last_name . ' has added Template',
					'new_response' => serialize($sub_data),
				];
				$this->logAction($insertLog);
				
				$getNewData = $this->templateService->getDetailsById($insert);

				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Add',
					'link' => url('/template'),
					'module' => 'Template',
					'module_id' => $getNewData->id,
					'new_response' => serialize($getNewData),
					'message'=>$admin_login->first_name . ' ' . $admin_login->last_name . ' has added Template',
				];
				$this->dynamicFormLogAction($insertLog);

				Session::flash('success', 'Template successfully inserted.');
				return redirect('template');
			} else {
				Session::flash('error', 'Sorry, something went wrong. Please try again.');
				return redirect('template-add');
			}
		}
	}

	public function add_template(Request $request)
	{
		$data['custom_form_id'] = $request->query('custom_form_id');
		$data['user'] = $admin_login = auth()->user();
		if (in_array($admin_login['user_type_fk'], array(3, 184))) {
			$document_list = DocumentType::where('del_flag', 'N');
			if ($admin_login->user_type_fk == 184) {
				$document_list->where('type', 'nybest');
			} else {
				$document_list->where('type', 'exmedc');
			}
			$document_list = $document_list->orderBy('name', 'asc')->get();
			$data['document_list'] = $document_list;
			$data['agency_list'] = Agency::getAllAgencyListWithoutAnyCondition();
			return view('template.templete_add', $data);
		} else {
			abort(404);
		}
	}

	public function edit_template(Request $request)
	{

		$data['user'] = $admin_login = auth()->user();

		if (in_array($admin_login['user_type_fk'], array(3, 184))) {

			$document_list = DocumentType::where('del_flag', 'N');
			if ($admin_login->user_type_fk == 184) {
				$document_list->where('type', 'nybest');
			} else {
				$document_list->where('type', 'exmedc');
			}
			$document_list = $document_list->orderBy('name', 'asc')->get();
			$data['document_list'] = $document_list;
			$data['edit_template'] = $this->templateService->getDetailsById(request('id'));
			$data['agency_list'] = Agency::getAllAgencyListWithoutAnyCondition();
			
			return view('template.templete_edit', $data);
		} else {
			abort(404);
		}
	}
	public function uploadFiles(Request $request)
	{
		$image = request('file');

		$destinationPath = public_path() . '/dosusinguploads/docusign/';

		$filename = date("Ymdhisa") . '.' . $image->getClientOriginalExtension();

		if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
			$destination = public_path('dosusinguploads/docusign');
			$image = $image->move($destination, $filename);
		} else {
			$image = Storage::disk('s3')->putFileAs('dosusinguploads/docusign', $image, $filename);
		}

		if ($image) {
			echo $filename;
		} else {
			echo false;
		}
	}

	public function document_insert()
	{

		$data['user'] = $admin_login = auth()->user();

		$sub_data = array(
			"name" => request("document_name"),
			"created_date" => date('Y-m-d h:i:s'),
			"created_by" => $admin_login['id']
		);
		$type = 'exmedc';
		if ($admin_login['user_type_fk'] == 184) {
			$type = 'nybest';
		}
		$sub_data['type'] = $type;

		$inser_id = new DocumentType($sub_data);
		$inser_id->save();
		$Insert = $inser_id->id;
		if ($Insert) {
			Session::flash('success', 'Document successfully inserted.');
			return redirect('template');
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
			return redirect('template');
		}
	}
	public function document_update()
	{

		$data['user'] = $admin_login = auth()->user();

		$sub_data = array(
			"name" => request("document_name")
		);
		$update = DocumentType::where('id', request("document_id"))->update($sub_data);

		Session::flash('success', 'Document successfully update.');
		return redirect('template');
	}

	public function document_delete()
	{

		$data['user'] = $admin_login = auth()->user();

		$sub_data = array(
			"del_flag" => 'Y'
		);
		$update = DocumentType::where('id', request("id"))->update($sub_data);

		Session::flash('success', 'Document successfully delete.');
		return redirect('template');
	}

	public function update(Request $request)
	{

		$data['user'] = $admin_login = auth()->user();

		$validator = Validator::make($request->all(), [
			'template_name' => 'required',
			'document_type' => 'required'
		]);
		if ($validator->fails()) {
			return redirect("template-edit?id=" . request('id'))
				->withErrors($validator, 'template')
				->withInput();
		} else {
			$oldResponse = $this->templateService->getDetailsById($request->id);
			$resouce = 'No';
			if (request('resource_tabe') == 'on') {
				$resouce = 'Yes';
			}

			$agencyId = null;
			if(!empty($request->agency_id[0])){
				$agencyId =implode(',',$request->agency_id);
			}
			
			$show_verify_by= "N";
			if(isset($request->show_verify_by) && $request->show_verify_by =='Y'){
				$show_verify_by = $request->show_verify_by;
			}

			$send_caregiver_email= "N";
			if(isset($request->send_caregiver_email) && $request->send_caregiver_email =='Y'){
				$send_caregiver_email = $request->send_caregiver_email;
			}

			$sub_data = array(
				"template_name" => request("template_name"),
				"document_type" => request("document_type"),
				"upload_document" => request("attached_files"),
				"remark" => request("remark"),
				"updated_date" => date('Y-m-d h:i:s'),
				"updated_by" => $admin_login['id'],
				'esign' => $request->esign,
				'resouce_tab' => $resouce,
				'email_notification' => request('email_notification'),
				'agency_id' => $agencyId,
				'checkbox_mark_flag'=>$request->checkbox_mark_flag,
				'show_verify_by'=>$show_verify_by,
				'send_caregiver_email'=>$send_caregiver_email,
				'resolution_update'=>$request->resolution_update,
				'esign_workflow'=>$request->esign_workflow ?? 'normal',
				'template_type'=>$request->template_type
			);

			$custom_template= 1;
			if($request->custom_template ==1){
				$custom_template= 0;
			}
			$sub_data['custom_template'] =$custom_template;
			$update = $this->templateService->update($sub_data,['id'=>request('id')]);
			if ($update) {
				$insertLog = [
					'type' => 'Update',
					'link' => url('/updateTemplate'),
					'module' => 'Template',
					'object_id' => request('id'),
					'message' => $admin_login->first_name . ' ' . $admin_login->last_name . ' has updated Template',
					'new_response' => serialize($sub_data),
					'old_response'=>serialize($oldResponse->toArray())
				];
				$this->logAction($insertLog);
				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Update',
					'link' => url('/updateTemplate'),
					'module' => 'Template',
					'module_id' => request('id'),
					'new_response' => serialize($sub_data),
					'old_response' => serialize($oldResponse->toArray())
				];
				$this->dynamicFormLogAction($insertLog);

				Session::flash('success', 'Template successfully updated.');
				return redirect('template');
			} else {
				Session::flash('error', 'Sorry, something went wrong. Please try again.');
				return redirect('template-edit?id=' . request('id'));
			}
		}
	}

	public function view(Request $request)
	{

		$data['user'] = $admin_login = auth()->user();
		if ($admin_login['user_type_fk'] != 3) {
			abort(404);
		}
		$id = request()->segment(3);
		$data['query'] = $this->templateService->getDetailsBySah1TemplateId($id);
		return view('template.template_receipt_new', $data);
	}

	public function CaregiverList()
	{
		$get_param  = request('q');
		$query = CaregiverDemographicsArchived::select('CaregiverCode as id', 'FirstName as name', 'LastName as lname', 'NotificationPreferencesEmail as email')->whereRaw('CONCAT(FirstName," ",LastName) LIKE "%' . $get_param . '%" or CaregiverCode LIKE "%' . $get_param . '%" ')->get();
		echo json_encode($query);
	}

	public function SearchReponse()
	{

		$caregiver_code = request('caregiver_code');
		$caregiver_name = request('caregiver_name');
		$caregiver_email = request('caregiver_email');
		$status_id = request('status_id');
		$ppd_expire_date = request('ppd_expire_date');
		$physical_date = request('physical_date');
		$between_ids = request('between_ids');
		$physical_date1 = request('physical_date1');
		$between_id = request('between_id');
		$ppd_expire_date1 = request('ppd_expire_date1');
		$temp_id = request('temp_id');
		$query = CaregiverDemographicsArchived::getResponseBySearch($caregiver_code, $caregiver_name, $caregiver_email, $status_id, $ppd_expire_date, $physical_date, $between_ids, $physical_date1, $between_id, $ppd_expire_date1);
		$temp_arraya = '';
		if (count($query) > 0) {

			foreach ($query as $val) {
				$fullname = ucfirst($val->FirstName . " " . $val->FirstName);
				$temp_arraya .= '<tr><input type="hidden" name="receipt_code[]" value="' . $val->CaregiverCode . '"><input type="hidden" name="receipt_name[]" value="' . $fullname . '"><td><input type="checkbox" name="chk[]" value="' . $val->CaregiverCode . '" class="caresid"></td><td>' . $val->CaregiverCode . '</td><td>' . $fullname . '</td><td>' . $val->NotificationPreferencesEmail . '</td><td>' . $val->Gender . '</td><td>' . $val->status . '</td><td>' . $val->ppd . '</td><td>' . $val->physical . '</td><td><a data-fancybox="" data-type="iframe" data-src="' . URL::to('/') . '/preview_details?VisitId=' . $temp_id . '" href="javascript:void(0)"> Preview</a></td></tr>';
			}
		} else {
			$temp_arraya .= '<tr><td colspan="9">No record available</td></tr>';
		}
		echo $temp_arraya;
		die();
	}



	public function delete()
	{

		$data['user'] = $admin_login = auth()->user();

		$id = request('id');

		$oldResponse = $this->templateService->getDetailsById($id);
		$sub_raay = array(
			'del_flag' => 'Y'
		);
		$update = $this->templateService->update($sub_raay,array('id'=>$id));

		if ($update) {
			$insertLog = [
				'type' => 'Delete',
				'link' => url('/template-delete'),
				'module' => 'Template',
				'object_id' => request('id'),
				'message' => $admin_login->first_name . ' ' . $admin_login->last_name . ' has deleted Template',
				'new_response' => serialize($sub_raay),
				'old_response'=>serialize($oldResponse->toArray()),
			];

			$this->logAction($insertLog);
			
			// Insert form Log into Dynamic form log table
			$dynamicFormResponseLog = [
				'type' => 'Delete',
				'link' => url('/template-delete'),
				'module' => 'Template',
				'module_id' => $id,
				'new_response' => serialize($sub_raay),
				'old_response' => serialize($oldResponse->toArray()),
				'message' => $admin_login->first_name . ' ' . $admin_login->last_name . ' has deleted Template',
			];
			$this->dynamicFormLogAction($dynamicFormResponseLog);

			Session::flash('success', 'Template successfully delete.');
			return redirect('template');
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
			return redirect('template');
		}
	}
	public function document()
	{
		$data['user'] = $admin_login = auth()->user();

		$id = request('id');

		$data['document'] = $this->templateService->getDetailsWithoutDelete($id);

		$data['matchingFormIds'] = $this->agencyMasterService->getAgencyMasterDetailsByFormId($data['document']->custom_form_id);
		
		$final_array = array();
		$countArray = array();
		$max = array();
		$testp = array();
		
		$data['savedWidth'] = $docWidth = $data['document']->docWidth;
		$response = unserialize($data['document']->response);
		$maxprice = '0';
		if (isset($response) && $response != '') {
			$final_array[] = $docWidth;
			foreach ($response as $val) {
				if (isset($val['obj']) && $val['obj'] != '') {
					$obj = $val['obj'];
				} else {
					$obj = '';
				}

				$final_array[] = $val;

				$countArray[] = $obj;
				$explode = explode('_', $val['id']);
				$max[] = $explode[1];
				$maxprice = max($max);
			}
		}
		$data['count'] = '';
		if ($maxprice != '') {
			$data['count'] = $maxprice;
		}

		$insertLog = [
			'type' => 'View',
			'link' => url('/document'),
			'module' => 'Template',
			'object_id' => $id,
			'message' => $admin_login->first_name . ' ' . $admin_login->last_name . ' has view Template',
			'new_response' => serialize($final_array),
		];

		$this->logAction($insertLog);

		$data['templateFields'] = json_encode($final_array, true);
		$data['template_id'] = $id;
		return view('docusign.new_template_document', $data);
	}

	public function send(Request $request)
	{

		$data['user'] = $admin_login = auth()->user();
		$obj = str_replace('"\"', '"', $_POST['actions']);
		$obj = str_replace('\""', '"', $obj);

		$document_key = request("document_key");
		$actions = request("actions");
		$docWidth = request("docWidth");
		$signing_key = request("signing_key");

		$documentDetails = json_decode($actions, true);
		
		$tesmps = serialize($documentDetails);
		$oldResponse = $this->templateService->getDetailsWithoutDelete($document_key);
		$this->templateService->update(array('response' => ($tesmps), 'docWidth' => $docWidth),array('id'=>$document_key));
		
		$dataResponse = array(
			'template_id' => $document_key,
			'user_id' => $admin_login['id'],
			'response' => $tesmps,
			'created_date' => date('Y-m-d h:i:s'),
			'created_by' => $admin_login['id'],
			'docWidth' => $docWidth,
			'old_response' => $oldResponse->response
		);
		$insertsa = new TemplateLog($dataResponse);
		$insertsa->save();
		$insert = $insertsa->id;
		echo 1;
	}

	public function getResponseCanvas($id)
	{

		$data['document'] =$this->templateService->getDetailsById($id);

		$response = '';
		if (isset($data['document']->response) && $data['document']->response != '') {
			$response = unserialize($data['document']->response);
		}

		echo json_encode($response);
	}

	/*Look up filed of intake */
	public function intakeResponse()
	{
		$selectedValue = request('values');
		$response = EsignHelper::RecordResponse($selectedValue);
		echo $response;
	}

	/*signer request for by template id */
	public function DocumentSendByType()
	{

		$data['user'] = $admin_login = auth()->user();
		$data['id'] = $id = request('id');
		$data['template_type'] = $templateDetails = $this->templateService->getDetailsWithoutDelete($id);

		$data['oldRecordById'] = DocumentSignerMaster::where('template_id', $id)->where('del_flag', 'N')->get();

		if (isset($data['oldRecordById']) && $data['oldRecordById'] != '') {
			foreach ($data['oldRecordById'] as $val) {
				if ($val->user_id != '') {
					$query = User::select('id as id', DB::raw('CONCAT(first_name," ",last_name) as name'))->where('id', $val->user_id)->first();
					$val->id = $query->id;
					$val->names = $query->name;
				}
			}
		}

		if (count($data['oldRecordById']) > 0) {

			return view('template.receiptSignEdit', $data);
		} else {

			return view('template.receiptSign', $data);
		}
	}
	/* end */
	public function insertReceiptSigner()
	{
		$data['user'] = $admin_login = auth()->user();
		$getTempalateData = Template::find(request("template_id"));
		$dropDown = request('dropDown');
		$deleteArray = array('del_flag' => 'Y');
		$getExistingData = DocumentSignerMaster::find(request('template_id'));
		DocumentSignerMaster::where('template_id', "=", request("template_id"))->update($deleteArray);
		foreach ($dropDown as $key => $val) {
			if (isset(request('search')[$key]) && request('search')[$key] != '' &&  $val == 'OfficeStaff') {
				$search = request('search')[$key];
			} else {
				$search = null;
			}
			$sub_data = array(
				"template_id" => request("template_id"),
				"name" => $val,
				'user_id' => $search,
				"created_date" => date('Y-m-d h:i:s'),
				"created_by" => $admin_login['id']
			);

			$inser_id = new DocumentSignerMaster($sub_data);
			$inser_id->save();
			$Insert = $inser_id->id;
		}

		if ($Insert) {

			$insertLog = [
				'type' => 'Signer',
				'link' => url('/insertReceiptSigner'),
				'module' => 'Template',
				'object_id' => request("template_id"),
				'message' => $getTempalateData->full_name . ' has signer Template',
				'new_response' => serialize($sub_data),
			];
			$this->logAction($insertLog);
			
			$getNewData = DocumentSignerMaster::find(request('template_id'));
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Signer',
				'link' => url('/insertReceiptSigner'),
				'module' => 'Template',
				'module_id' => request("template_id"),
				'new_response' => serialize($getNewData),
				'old_response' => serialize($getExistingData),
				'message' => $getTempalateData->full_name . ' has signer Template',
			];
			$this->dynamicFormLogAction($insertLog);
			
			Session::flash('success', 'Signer successfully inserted.');
			return redirect('template');
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
			return redirect('template');
		}
	}

	public function getTypeByTemplate()
	{
		$selectedValue = request('selected');

		$templateId = request('id');
		$getEsign =$this->templateService->getDetailsWithoutDelete($templateId);

		$query = DocumentSignerMaster::select('name', 'user_id', 'id')->where('del_flag', 'N')->where('template_id', $templateId)->get();

		$final_array = '<option value="">Select Signer</option>';
		$names = array('#ff0000', '#ff8080',  '#FFFF00', '#FF1493', '#dea5a4');
		$tempCounter = 0;

		if (isset($query) && $query != '') {
			foreach ($query as $key => $val) {

				if ($val->user_id != '') {
					$values = $val->user_id;
				} else {
					$values = $val->name;
				}

				$selected = '';
				if ($selectedValue == $val->name) {
					$selected = 'selected="selected"';
				}
				$name = $val->name;
				if ($val->name == 'OfficeStaff') {
					$name = "Admin";
				}
				$final_array .= '<option value="' . $val->name . '" data-style="' . $names[$tempCounter % count($names)] . '"  style="background-color:' . $names[$tempCounter % count($names)] . '" ' . $selected . '>' . $name . '</option>';
				$tempCounter++;
			}
		} else {

			$selected = '';
			if ($selectedValue == 'defualt') {
				$selected = 'selected="selected"';
			}
			$final_array .= '<option value="defualt" ' . $selected . ' data-style="' . $names[$tempCounter % count($names)] . '"  style="background-color:' . $names[$tempCounter % count($names)] . '" ' . $selected . '>Default</option>';
		}

		echo $final_array;
	}

	/*check signer by template id */
	public function checkSignerOnNot()
	{
		$template_id = request('template_id');
		$temp = 0;
		if ($template_id != '') {
			$query = DocumentSignerMaster::select('id')->where('template_id', $template_id)->get();

			if (!empty($query[0])) {
				$temp = 1;
			} else {
				$temp = 0;
			}
		}

		return $temp;
	}
	/*end check signer by template id */

	public function document_duplicate(Request $request)
	{

		$data['user'] = $admin_login = auth()->user();

		$tasks = Template::find(request("id"));
		$newTask = $tasks->replicate();
		$newTask->save();
		$Insert = $newTask->id;

		$this->templateService->update(array('created_date' => date('Y-m-d h:i:s'), 'created_by' => $admin_login['id']),array('id'=>$Insert,'del_flag'=>'N'));
		
		$insertLog = [
			'type' => 'Copy',
			'link' => url('/template-duplicate'),
			'module' => 'Template',
			'object_id' => request("id"),
			'message' => $tasks->full_name . ' has copy Template', // apply accesor from model for full name
			'new_response' => serialize($newTask),
		];
		$this->logAction($insertLog);

		if ($Insert) {
			$dropDown =  DocumentSignerMaster::where('template_id', "=", request("id"))->get();

			foreach ($dropDown as $key => $val) {

				$sub_data = array(
					"template_id" => $Insert,
					"name" => $val->name,
					'user_id' => $val->user_id,
					"created_date" => date('Y-m-d h:i:s'),
					"created_by" => $admin_login['id']
				);
				$inser_id = new DocumentSignerMaster($sub_data);
				$inser_id->save();
			}

			Session::flash('success', 'Document successfully create.');
			return redirect('template');
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
			return redirect('template');
		}
	}

	public function signatureloglist($id)
	{

		$query = DocumentSentReport::select('us.FirstName', 'us.LastName', 'us.EMAILADDRESS', 'us.PHONE', 'document_sent_report.*')->leftjoin('users as us', function ($join) {
			$join->on('us.USERID', '=', 'document_sent_report.caregiver_code');
		})
			->where('document_sent_report.groupId', $id)
			->where('document_sent_report.del_flag', 'N')
			->get();
		if (!empty($query)) {
			foreach ($query as $keys) {
				$keys->FirstName = ucfirst($keys->FirstName);
				$keys->LastName = ucfirst($keys->LastName);
				$status = '';
				if ($keys->status == 'Completed') {
					$status = $keys->status;
				}
				$keys->status = ucfirst($status);
				$completed = '';
				if ($keys->completed_on != '') {
					$completed  = date('m/d/Y h:i A', strtotime($keys->completed_on));
				}
				$keys->created_date = date('m/d/Y h:i A', strtotime($keys->created_date));
				$keys->completed_on = $completed;
			}
		}
		echo json_encode($query);
	}

	public function activeDeactive()
	{
		$admin_login = Auth::user();
		$templateId = request('id');

		$getExistingData = $this->templateService->getDetailsById(request('id'));
	
		if ($getExistingData->active_status == 'Active') {
			$status = 'Deactive';
		} else {
			$status = 'Active';
		}

		$update = $this->templateService->update(array('active_status' => $status),array('id'=>$templateId));
		if ($update) {

			$insertLog = [
				'type' => $status . ' Status',
				'link' => url('/template-status'),
				'module' => 'Template',
				'object_id' => $templateId,
				'message' => auth()->user()->first_name.' '.auth()->user()->last_name.' has updated status of Template', // apply accesor from model for full name
				'new_response' => serialize(array('active_status' => $status)),
				'old_response'=> serialize($getExistingData->toArray()),
			];
			$this->logAction($insertLog);
			
			$getNewData = $this->templateService->getDetailsById(request('id'));
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => $status,
				'link' => url('/template-status'),
				'module' => 'Template',
				'module_id' => $templateId,
				'new_response' => serialize($getNewData),
				'old_response' => serialize($getExistingData->toArray()),
				'message' => $query->full_name . ' has status Template',
			];
			$this->dynamicFormLogAction($insertLog);

			return response()->json(['status' => true, 'error_msg' => 'Status ' . $status . ' sucessfully'], 200);
		} else {
			return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
		}
	}

	public function getDownloadTemplate($id)
	{

		$query = DocumentSentReport::select('pdf_generate')->where('groupId', $id)->orderBy('id', 'desc')->where('status', 'Completed')->first();

		$file = public_path() . "/dosusinguploads/docusign/" . $query->pdf_generate;
		$file_name = basename($file);
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
		readfile($file);
		exit;

	}
	public function getpdfbyTemplateid()
	{

		$id = request('template_id');
		$query = $this->templateService->getDetailsWithoutDelete($id);

		$dir = public_path(). '/dosusinguploads/docusign/' . $query->upload_document;
		if (file_exists($dir)) {
			$dir = public_path() . '/dosusinguploads/docusign/' . $query->upload_document;
			$headers = [];
			return response()->download($dir, $query->upload_document, $headers);
		} else {
			return Storage::disk('s3')->download('/dosusinguploads/docusign/' . $query->upload_document);
			die();
		}
	}

	public function upload_documentweb(Request $request)
	{

		$file = $request->file('image');

		$ext = $file->getClientOriginalExtension();

		$newName = time() . uniqid() . ".png";
		$file_path = $file->getPathName();
		$image = Storage::disk('s3')->putFileAs('dosusinguploads/docusign', $file, $newName);
		$getFullUrl = Storage::disk('s3')->url('dosusinguploads/docusign/'.$image);
	
		echo $getFullUrl;
	}
	
	public function upload_documentwebNew(Request $request)
	{
		$text = $request->input('textbox');
		$fontsize = $request->input('fontsize');
		
		header('Content-Type: image/png');
		
	
		$font = public_path() . '/assets/fonts/' . $fontsize;
		$font_size = 70;
		
		// Get text bounding box to determine width & height
		$box = imagettfbbox($font_size, 0, $font, $text);
		$text_width = abs($box[2] - $box[0]);
		$text_height = abs($box[5] - $box[1]);
		
		// Add padding around the text
		$padding = 30;
		$image_width = $text_width + ($padding * 2);
		$image_height = $text_height + ($padding * 2);
		
		// Create a larger image to improve resolution
		$scale_factor = 2;
		$im = imagecreatetruecolor($image_width * $scale_factor, $image_height * $scale_factor);
		
		// Colors
		$white = imagecolorallocate($im, 255, 0, 0);
		$black = imagecolorallocate($im, 0, 0, 0);
		// You can change the text color by replacing $black with $grey if desired
		// $grey = imagecolorallocate($im, 128, 128, 128);
		$grey = imagecolorallocate($im, 0, 0, 0); // Red background
		$opacity = 0;
		$alpha = (int)(127 * (1 - $opacity));
		$transparent = imagecolorallocatealpha($im,255, 255	,255,$alpha);
		
		// Set background to transparent
		imagefill($im, 0, 0, $transparent);
		imagecolortransparent($im, $transparent);
		
		// Draw text on the scaled image
		$x = $padding * $scale_factor;
		$y = ($text_height + $padding) * $scale_factor;
		imagettftext($im, $font_size * $scale_factor, 0, $x, $y, $grey, $font, $text);
		
		// Resize the image back to normal size (anti-aliasing effect)
		$final_im = imagecreatetruecolor($image_width, $image_height);
		
		// Preserve transparency during resizing
		imagealphablending($final_im, false);
		imagesavealpha($final_im, true);
	
		imagefill($final_im, 0, 0, $transparent);
		
		imagecopyresampled($final_im, $im, 0, 0, 0, 0, $image_width, $image_height, imagesx($im), imagesy($im));
		
		// Save Image
		$ims = time() . uniqid() . ".png";
	
		// Handle Storage (S3 or Local)
		if (env('FILE_UPLOAD_PERMISSION') != 'development' && env('FILE_UPLOAD_PERMISSION') !="") {
			ob_start();
			imagepng($final_im, null, 9);
			$imageData = ob_get_clean();

			Storage::disk('s3')->put('dosusinguploads/docusign/' . $ims, $imageData);
			$path = 'dosusinguploads/docusign/'.$ims;
			$temporaryUrl = Storage::disk('s3')->temporaryUrl(
				$path,
				Carbon::now()->addMinutes(10)
			);
			return $temporaryUrl;
		
		} else {
			$imagesPng = public_path() . '/dosusinguploads/docusign/' . $ims;
			imagepng($final_im, $imagesPng, 9);
			
			imagedestroy($im);
			imagedestroy($final_im);
			return URL::to('/') . '/dosusinguploads/docusign/' . $ims;
		}
	}

	public function upload_documentwebNewOld(Request $request)
	{
		$text = $request->input('textbox');
		$fontsize = $request->input('fontsize');

		header('Content-Type: image/png');

		// Font Path
		$font = public_path() . '/assets/fonts/' . $fontsize;
		$font_size = 70; // Increase font size for better clarity

		// Get text bounding box to determine width & height
		$box = imagettfbbox($font_size, 0, $font, $text);
		$text_width = abs($box[2] - $box[0]); // Text width
		$text_height = abs($box[5] - $box[1]); // Text height

		// Add padding around the text
		$padding = 30;
		$image_width = $text_width + ($padding * 2);
		$image_height = $text_height + ($padding * 2);

		// Create a larger image to improve resolution
		$scale_factor = 2; // Scale factor for better quality
		$im = imagecreatetruecolor($image_width * $scale_factor, $image_height * $scale_factor);

		// Colors
		$white = imagecolorallocate($im, 255, 0, 0);
		$black = imagecolorallocate($im, 0, 0, 0);
		// $grey = imagecolorallocate($im, 128, 128, 128);
		$grey = imagecolorallocate($im, 0, 0, 0);

		$opacity = 0;
		$alpha = (int)(127 * (1 - $opacity));
		$transparent = imagecolorallocatealpha($im,255, 255	,255,$alpha);
	// Set background to transparent
	imagefill($im, 0, 0, $transparent);
	imagecolortransparent($im, $transparent);
		// Set background color
		//imagefilledrectangle($im, 0, 0, imagesx($im), imagesy($im), $white);

		// Draw text on the scaled image
		$x = $padding * $scale_factor;
		$y = ($text_height + $padding) * $scale_factor;
		imagettftext($im, $font_size * $scale_factor, 0, $x, $y, $grey, $font, $text);

		// Resize the image back to normal size (anti-aliasing effect)
		$final_im = imagecreatetruecolor($image_width, $image_height);
		imagecopyresampled($final_im, $im, 0, 0, 0, 0, $image_width, $image_height, imagesx($im), imagesy($im));

		// Save Image
		$ims = time() . uniqid() . ".png";
		$imagesPng = public_path() . '/dosusinguploads/docusign/' . $ims;
		imagepng($final_im, $imagesPng, 9); // Save with max quality (compression level 9)
		
		imagedestroy($im);
		imagedestroy($final_im);

		// Handle Storage (S3 or Local)
		if (env('FILE_UPLOAD_PERMISSION') != 'development') {
			$fileGetContain = file_get_contents($imagesPng);
			Storage::disk('s3')->put('dosusinguploads/docusign/' . $ims, $fileGetContain);
			
			return URL::to('/').'/dosusinguploads/docusign/'.$ims;
		} else {
			return URL::to('/') . '/dosusinguploads/docusign/' . $ims;
		}
	}

	public function getsignerbyTemplateId()
	{
		$data['user'] = $admin_login = auth()->user();
		$template_id = request('template_id');
		$query = DocumentSignerMaster::select('name', 'user_id', 'id')->where('del_flag', 'N')->where('template_id', $template_id)->get();
		echo json_encode($query);
	}

	public function docums()
	{
		$response = EsignHelper::tempregenerate();
		echo $response;
		die();
	}

	public function thankyou()
	{
		return view('docusign/thankyou');
	}
	/* search user by List */
	public function SearchUserList()
	{

		$data['user'] = $admin_login = auth()->user();
		$q = request('q');

		$query = User::searchUserTemplateModule($q);
		echo json_encode($query);
	}
	public function searchByEMCUserList()
	{

		$data['user'] = $admin_login = auth()->user();
		$q = request('q');

		$query = User::searchEMCUserTemplateModule($q);

		echo json_encode($query);
	}
	public function nyBestResponse(Request $request)
	{
		$selectedValue = $request->input('values');
		$response = EsignHelper::nyBestNewResponse($selectedValue);
		echo $response;
	}
	/* end */

	public function getTemplateLogPage(Request $request)
	{
		$id = request('id');
		$data['user'] = $authId = Auth::user();
		$data['logList'] = LogsService::getDatByAllLog($id, 'Template');

		return view("user_log_ajax_list", $data);
	}
	public function templateLogs($id)
	{
		$data['id'] = $id;
		return view('template/template_log_list', $data);
	}

	public function caregiverLookUp(Request $request)
	{
		$selectedValue = $request->input('value');
		$id = $request->template_id;

		$data = $this->templateService->getDetailsWithoutDelete($id);
		$agencyId=0;
		$agencyArray = [];
		if(!empty($data->agency_id)){
			$agencyArray = explode(',',$data->agency_id);

			if(count($agencyArray)==1){
			$agencyId =	$data->agency_id;
			}
		}
		
		$matchingFormIds = AgencyMaster::with('fields')->where('form_id', $data->custom_form_id)
			->when(!empty($agencyArray), function ($q) use ($agencyArray) {
				return $q->whereIn('agency_id', $agencyArray);
			})->when($agencyId>0, function ($q) use ($agencyId) {
				return $q->where('agency_id', $agencyId);
			})
		
		->get();
		
		$dynamicFields = [];

		foreach ($matchingFormIds as $form) {
			$label = "";
			if (isset($form->fields) && $form->fields != "") {
				if (isset($form->fields->type) && in_array($form->fields->type, ['information', 'radio', 'checkbox'])) {
					continue;
				}
				$label = $form->fields->label;
			}
			$dynamicFields[] = [
				'form_id' => $form->form_id,
				'field_id' => $form->field_id,
				'label' => $label,
			];
		}
		$response = EsignHelper::nyBestNewResponse($selectedValue, $dynamicFields);

		echo $response;
	}

	public function loadEsignTemplate(Request $request)
	{
		$type = strtolower($request->type);
		$templateType = $request->template_type ?? null;

		// Enforce user's template_type access for caregiver templates
		$userTemplateType = auth()->user()->template_type ?? 'All';
		if ($type == 'caregiver' && $userTemplateType != 'All') {
			$templateType = strtolower($userTemplateType);
		}

		$query = $this->templateService->loadEsignTemplateData($request->agency_id,$type,$templateType);
		
		$templateResponse = [];
		if(!empty($query[0])){
			foreach($query as $val){
				$response = unserialize($val->response);
				if(!empty($response[0])){
					$temp = [];
					$temp['id'] = $val->id;
					$temp['template_name'] = $val->template_name;
					$templateResponse[] =$temp;
				}
			}
		}
		
		return response()->json(['status' => true, 'data' => $templateResponse]);
	}

	public function AllocateSigner(Request $request)
	{
		$template_id = $request->input('template_id');
		$temp = 0;
		if ($template_id != '') {
			$query = $this->documentSignerService->getDocumentSignerMasterListById($template_id);

			if (!empty($query[0])) {
				$temp = 1;
			} else {
				$temp = 0;
			}
		}

		return $temp;
	}

	public function getResponseCanvasNew($id)
	{
		$data['document'] =  $this->documentSendService->getDetailsById($id);

		if($data['document']->agency_form_id != null){
			$agencyForm = $this->agencyAllFormService->getAgencyFormWithDoctors($data['document']->agency_form_id);

			$doctorSignature = null;
			if ($agencyForm && $agencyForm->doctors) {
				$doctorSignature =$this->getImagesForAwsServer($agencyForm->doctors->signature_upload,'signature');
			}
	
			$doctorStamp = null;
			if ($agencyForm && $agencyForm->doctors) {
				$doctorStamp =$this->getImagesForAwsServer($agencyForm->doctors->stamp_upload,'stamp');
			}
		}else{
			$doctorSignature = null;
			if ($data['document'] && $data['document']->doctors) {
				$doctorSignature =$this->getImagesForAwsServer($data['document']->doctors->signature_upload,'signature');
			}

			$doctorStamp = null;
			if ($data['document'] && $data['document']->doctors) {
				
				$doctorStamp =$this->getImagesForAwsServer($data['document']->doctors->stamp_upload,'stamp');
			}
		}

		$templDetails = $this->templateService->getDetailsById($data['document']->templete_id);

		$response = '';
		if (isset($templDetails->response) && $templDetails->response != '') {
			$response = unserialize($templDetails->response);
		}

		$response['doctor_signature'] = $doctorSignature;
		$response['doctor_stamp'] = $doctorStamp;
	
		echo json_encode($response);
	}

	public function getFormByCheckbox(Request $request)
	{
		$templateId = $request->template_id;
		$data = $this->templateService->getDetailsWithoutDelete($templateId);
	
		$matchingFormIds = AgencyMaster::with('fields')
			->where('form_id', $data->custom_form_id)
			->get();

		$options = $matchingFormIds->filter(function ($item) {
			return $item->fields->type === 'checkbox';
		})->map(function ($item) {
			return [
				'id' => $item->field_id,
				'label' => $item->fields->label,
				'type' => $item->fields->type,
				'options' => explode(',', $item->fields->options),
			];
		});
		return response()->json($options);
	}

	public function getFormByRadio(Request $request)
	{
		$templateId = $request->template_id;
		$data = $this->templateService->getDetailsWithoutDelete($templateId);

		$matchingFormIds = AgencyMaster::with('fields')
			->where('form_id', $data->custom_form_id)
			->get();

		$options = $matchingFormIds->filter(function ($item) {
			return $item->fields->type === 'radio';
		})->map(function ($item) {
			return [
				'id' => $item->field_id,
				'label' => $item->fields->label,
				'type' => $item->fields->type,
				'options' => explode(',', $item->fields->options),
			];
		});
		return response()->json($options);
	}

	public function getImagesForAwsServer($img,$type){
		$fileData = "";
		if ($type == 'stamp') {
			$fileUrl = url('dosusinguploads/docusign' . $img);
			$file = public_path('/dosusinguploads/docusign' . $img);

			if (file_exists($file)) {
				$fileData = $fileUrl;
			} else {
				$path = 'dosusinguploads/docusign/' . $img;
				$fileData = Storage::disk('s3')->temporaryUrl(
					$path,
					Carbon::now()->addMinutes(10)
				);
				
			}
		} else {
			$fileUrl = url('dosusinguploads/docusign' . $img);
			$file = public_path('dosusinguploads/docusign' . $img);

			if (file_exists($file)) {
				$fileData= $fileUrl;
			} else {
				$path = 'doctor-signature/' . $img;
				$fileData = Storage::disk('s3')->temporaryUrl(
					$path,
					Carbon::now()->addMinutes(10)
				);
			}
		}

		return $fileData;
	}

	public function getpdfbyDocumentWriteid(Request $request)
	{
	 
		$id = request('document_write_id');

		$query = $this->documentSendService->getWriteDataByUniqueId($id);
		$fileDetails = $query->old_file_upload;
		if($query->is_submit ==1){
			$fileDetails = $query->file_upload;
		}
	
		$dir = public_path(). '/patientWriteDocument/' . $fileDetails;
		$file = $fileDetails;
		if (file_exists($dir)) {
			$filePath = public_path('patientWriteDocument').'/'.$file;
			$headers = [];
			return response()->download($filePath, basename($file), $headers);
		} else {
			return Storage::disk('s3')->download('patientWriteDocument/' . $file);
			die();
		}
	}

	public function viewWriteDocument(Request $request)
	{
		$auth = auth()->user();
		$data['user'] = $admin_login = auth()->user();
		$data['login_id'] = $auth['id'] ?? '';
		
		$id = $request->id;
		
		if(isset($request->type) && $request->type !=""){
			$getDocumentExistingData = $this->documentPatientService->getDocumentDetailsById($id);
			$document = $this->documentSendService->getWriteDataByID($id,$request->type);

			if(isset($document->id)){
				$document->document_name = $getDocumentExistingData->document_name;
				$data['document'] = $document;
			}else{
				
				if(isset($getDocumentExistingData->id)){
					$saveData = [
						'document_patient_id'=>$getDocumentExistingData->id,
						'type'=>'Document',
						'file_upload'=>$getDocumentExistingData->attachment,
						'created_at'=>$getDocumentExistingData->created_date,
						'created_by'=>$getDocumentExistingData->created_by,
						'document_name'=>$getDocumentExistingData->document_name,
						'old_file_upload'=>$getDocumentExistingData->attachment,
					];

					$saveDocument = $this->writeDocumentService->save($saveData);
					if($saveDocument){
						if (env('FILE_UPLOAD_PERMISSION') != 'development') {

							$file = public_path('/patientdocument').'/'.$getDocumentExistingData->attachment;
							if(file_exists($file)){
								$fileGetContain = file_get_contents($file);
								Storage::disk('s3')->put('patientWriteDocument/'.$getDocumentExistingData->attachment, $fileGetContain);
							}else{
								$fileGetContain = Storage::disk('s3')->get('patientdocument/'.$getDocumentExistingData->attachment);
								Storage::disk('s3')->put('patientWriteDocument/'.$getDocumentExistingData->attachment, $fileGetContain);
							}

							
						}else{
							$file = public_path('/patientdocument').'/'.$getDocumentExistingData->attachment;
							if(file_exists($file)){
								$fileGetContain = file_get_contents($file);
								$destinationPath = public_path('patientWriteDocument/' . $getDocumentExistingData->attachment);
								file_put_contents($destinationPath,$fileGetContain);

							}
						}
						$data['document'] = $this->documentSendService->getWriteDataByID($id,$request->type);
					}
				}
			}

		}else{
			$data['document'] = $this->documentSendService->getWriteDataByID($id);
		}
		
		$response = [];
		if(isset($data['document']->response)){
			$response = unserialize($data['document']->response);
		}

		$data['Signinsert'] = json_encode($response, true);
		$data['docWidth'] = $docWidth = $data['document']->docWidth;
	
		$final_array = array();
		$countArray = array();
		$max = array();
		$testp = array();
		
		$data['savedWidth'] = $docWidth = $data['document']->docWidth;
		$maxprice = '0';
		if (isset($response) && $response != '') {
			$final_array[] = $docWidth;
			foreach ($response as $val) {
				if (isset($val['obj']) && $val['obj'] != '') {
					$obj = $val['obj'];
				} else {
					$obj = '';
				}

				$final_array[] = $val;

				$countArray[] = $obj;
				$explode = explode('_', $val['id']);
				$max[] = $explode[1];
				$maxprice = max($max);
			}
		}
		$data['count'] = '';
		if ($maxprice != '') {
			$data['count'] = $maxprice;
		}

		$data['templateFields'] = json_encode($final_array, true);
		$data['template_id'] = $id;
		
		$portalType ="";
		if(isset($data['document']->type) && $data['document']->type !=""){
			if($data['document']->type =='Document'){
				$getDocumentDetails =$this->documentPatientService->getDocumentDetailsById($data['document']->document_patient_id);
				$pid = $getDocumentDetails->patient_id??"";
			}
			if($data['document']->type =='Esign'){
				$getEsignDetails = $this->documentSendService->getDetailsById($data['document']->document_patient_id);
				$pid = $getEsignDetails->main_intakeId??"";
			}

			$patientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($pid);
			$portalType = $patientDetails->type??"";
		}

		$data['portalType'] = $portalType;
		return view('docusign.view_write_document', $data);
		
	}

	public function writeDocumentSend(Request $request)
	{

		$auth = auth()->user();
		$data['user'] = $admin_login = auth()->user();
		$obj = str_replace('"\"', '"', $_POST['actions']);
		$obj = str_replace('\""', '"', $obj);

		/*$obj = $_POST["actions"];*/
		$document_key = request("document_key");
		$actions = request("actions");
		$docWidth = request("docWidth");
		$signing_key = request("signing_key");

		$documentDetails = json_decode($actions, true);
		$final = [];
		foreach ($documentDetails as $act) {
			if($act['type'] =='image'){
				$folder = basename(dirname($act['image']));
				$folderFlag = 0;
				if(strtolower($folder) =='patientwritedocument'){
					$folderFlag = 1;
				}
				if(strtolower($folder) =='dosusinguploads/docusign'){
					$folderFlag = 2;
				}
				$finalName = basename($act['image']);
				$act['text'] = strtok($finalName, '?');
				$act['image'] = strtok($finalName, '?');
				$act['updatedSelectType'] = $folderFlag;
			}
	
			if($act['type'] =='stamp'){
				$finalName = basename($act['image']);
				$act['text'] = strtok($finalName, '?');
				$act['image'] = strtok($finalName, '?');
			}

			$final[] = $act;
		}

		$tesmps = serialize($final);
		
		$logResponse = [];
		if(count($documentDetails) >0){
			foreach($documentDetails as $val){
				$explode = explode('_',$val['id']);
				
				if($val['type'] =='image'){
					$finalName = basename($val['image']);
					$logResponse[$val['id']] = strtok($finalName, '?');
				}
				if($val['type'] =='text'){
					$logResponse[$val['id']] = $val['text'];
				}
				if($val['type'] =='stamp'){
					$finalName = basename($val['image']);
					$logResponse[$val['id']] = strtok($finalName, '?');
				}
				if($explode[0] =='datesigned'){
					$logResponse[$val['id']] = date('m/d/Y');
				}
			}
		}

		$writeDocumentData = $this->documentSendService->getWriteDataByIDWithoutCondition($document_key);
		
		$update = $this->documentSendService->updateDocumentResponse($document_key, $tesmps, $docWidth,$request->file_name,1);

		if(isset($writeDocumentData->type) && $writeDocumentData->type == 'Esign'){
			$oldResponse = $this->documentSendService->getDetailsByIdNew($document_key);
			

			$updateStatus =  $this->documentSendService->markDocumentAsCompleted($document_key,$writeDocumentData->file_upload);
			$documentRes = $this->documentSendService->getDetailsByIdNew($document_key);
			$newResponse = $documentRes->toArray();
			$newResponse['write_completed_by_name'] = $auth->full_name ?? '';
			// Insert form Log into Dynamic form log table
			$message = $newResponse['write_completed_by_name'].' has filled Esign document';

			$insertLog = [
				'type' => 'Update',
				'link' => url('/esign/write_document_send'),
				'module' => 'Esign Section',
				'module_id' => $documentRes->groupId,
				'new_response' => serialize($newResponse),
				'old_response' =>serialize($oldResponse),
				'is_status' => 'Completed',
				'message'=>$message,
				'esign_new_response'=>serialize($logResponse),
			];
			$this->dynamicFormLogAction($insertLog);
			
			$insertLog = [
				'type' => 'Filled Document',
				'link' => url('/esign/write_document_send'),
				'module' => 'Patient Appointment',
				'object_id' => $documentRes->main_intakeId,
				'message' =>$message,
				'new_response' => serialize($newResponse),
				'old_response' =>serialize($oldResponse)
			];
			$this->logAction($insertLog);
			
			// Get Group wise notification
			$signerAction = $this->getSignerAction($documentRes->sent_on);
			$patientId = $documentRes->main_intakeId ?? '';
			$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);
			$agencyFk = $getPatientDetails->agency_id ?? '';
			$userType = $getPatientDetails->type ?? '';
			$templeteName =  $writeDocumentData->document_name ?? '';
			$title = 'Esign '.'('.$signerAction.')';
			$msg = '<br><b>Document Name : </b>'.($templeteName).' ('.date('m/d/Y h:i A',strtotime($documentRes->created_date)).')';

			$this->notificationSend('Esign',$title,$msg,$patientId,$agencyFk,$userType,$userData=[],$serviceData=[]);
			
		}else{
			$getDocumentDetails = $this->documentPatientService->getDocumentDetailsById($writeDocumentData->document_patient_id);
			$message = $auth->full_name.' has filled Esign document';
			$insertLog = [
				'type' => 'Filled Document',
				'link' => url('/esign/write_document_send'),
				'module' => 'Patient Appointment',
				'object_id' => $getDocumentDetails->patient_id,
				'message' =>$message,
				'new_response' => serialize($logResponse)
			];

			$this->logAction($insertLog);
		}
		
		if(file_exists($request->converted)){
			unlink($request->converted);
		}

		if($request->demo_file !=""){
			if(file_exists($request->demo_file)){
				$directory = dirname($request->demo_file);
				// Delete directory if empty
				if (File::isDirectory($directory)) {
					File::deleteDirectory($directory);
				}
			}
		}
		echo 1;
	}

	public function regenerateWriteDocument(Request $request)
	{
		
		try {
			if(file_exists($request->converted_file)){
				$directory = dirname($request->converted_file);
				if (File::isDirectory($directory)) {
					File::deleteDirectory($directory);
				}
			}

			if(file_exists($request->demo_file)){
				$directory = dirname($request->demo_file);
				// Delete directory if empty
				if (File::isDirectory($directory)) {
					File::deleteDirectory($directory);
				}
			}
			$documentId = $request->input('document_id');
			$response = $this->commonEsignService->regeneratethepdfWriteDocument($documentId);
			
			return response()->json([
				'status' => true,
				'error_msg' => 'Document regenerated successfully.',
				'data' => $response
			],200);

		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'error_msg' => 'An error occurred while regenerating the document.'
			], 500);
		}
	}

	public function eraserApplyToPdf(Request $request)
	{
		try {
			$documentId = $request->input('document_id');
			$eraserAreas = $request->input('eraser_areas', []);

			if (empty($eraserAreas)) {
				return response()->json([
					'status' => false,
					'error_msg' => 'No eraser areas provided.'
				], 422);
			}

			
			$mainResponse = $this->documentSendService->getWriteDataByUniqueId($documentId);
			if (!$mainResponse) {
				return response()->json([
					'status' => false,
					'error_msg' => 'Document not found.'
				], 404);
			}

			$file = $mainResponse->old_file_upload;
			if($mainResponse->is_submit ==1){
				$file = $mainResponse->file_upload;
			}
				
			$inputPathLocal = public_path('/patientWriteDocument/' . $file);

			if (file_exists($inputPathLocal)) {
				$inputPath = $inputPathLocal;
			} else {
				if (env('FILE_UPLOAD_PERMISSION') != 'development') {
					$expiry = \Carbon\Carbon::now()->addMinutes(10);
					$path = 'patientWriteDocument/' . $file;
					$inputPath = \Storage::disk('s3')->temporaryUrl($path, $expiry);

					$filePath = public_path('/tempPDFGenerate/temp_server/ER-' . $documentId);
					if (!\File::exists($filePath)) {
						\File::makeDirectory($filePath, 0777, true, true);
					}
					$tmpFile = $filePath . '/' . uniqid() . '.pdf';
					file_put_contents($tmpFile, file_get_contents($inputPath));
					$inputPath = $tmpFile;
				} else {
					return response()->json([
						'status' => false,
						'error_msg' => 'Source PDF file not found.'
					], 404);
				}
			}

			$folder = 'ER-' . $documentId;
			$converted = $this->commonEsignService->commonGsPathPublic($folder, $inputPath);

			$pdf = new PDF(null, 'px');
			$pdf->SetAutoPageBreak(false, 0);
			$pdf->SetMargins(0, 0, 0);
			$pdf->SetHeaderMargin(0);
			$pdf->SetFooterMargin(0);

			$numPages = $pdf->setSourceFile($converted);

			$canvasWidth = floatval($request->input('canvas_width', 0));
			$canvasHeight = floatval($request->input('canvas_height', 0));

			foreach (range(1, $numPages) as $page) {
				$tplIdx = $pdf->importPage($page);
				$size = $pdf->getTemplateSize($tplIdx);

				$pdf->AddPage(
					$size['w'] > $size['h'] ? 'L' : 'P',
					array($size['w'], $size['h']),
					true
				);
				$pdf->useTemplate($tplIdx);

				$scaleX = ($canvasWidth > 0) ? $size['w'] / $canvasWidth : 1;
				$scaleY = ($canvasHeight > 0) ? $size['h'] / $canvasHeight : 1;

				foreach ($eraserAreas as $area) {
					if ((int) $area['page'] !== $page) {
						continue;
					}

					$x = floatval($area['x']) * $scaleX;
					$y = floatval($area['y']) * $scaleY;
					$w = floatval($area['width']) * $scaleX;
					$h = floatval($area['height']) * $scaleY;

					$pdf->SetFillColor(255, 255, 255);
					$pdf->Rect($x, $y, $w, $h, 'F');
				}
			}

			$outputDir = public_path('/tempPDFGenerate/temp_server/ER-' . $documentId);
			if (!\File::exists($outputDir)) {
				\File::makeDirectory($outputDir, 0777, true, true);
			}
			$outputName = 'erased_' . uniqid() . '.pdf';
			$outputPath = $outputDir . '/' . $outputName;
			$pdf->Output($outputPath, 'F');

			$newFileName = 'erased_' . time() . '_' . $file;
			$destinationPath = public_path('/patientWriteDocument/' . $newFileName);

			if (env('FILE_UPLOAD_PERMISSION') != 'development') {
				$pdfContent = file_get_contents($outputPath);
				\Storage::disk('s3')->put('patientWriteDocument/' . $newFileName, $pdfContent);
			} else {
				copy($outputPath, $destinationPath);
			}

			$this->writeDocumentService->update(['file_upload' => $newFileName],array('id'=>$documentId));
			
			$pdfUrl = (env('FILE_UPLOAD_PERMISSION') != 'development')
				? \Storage::disk('s3')->temporaryUrl('patientWriteDocument/' . $newFileName, \Carbon\Carbon::now()->addMinutes(10))
				: url('patientWriteDocument/' . $newFileName);

			if($outputPath !=""){
				$directory = dirname($outputPath);
				if (File::isDirectory($directory)) {
					File::deleteDirectory($directory);
				}
			}
			return response()->json([
				'status' => true,
				'error_msg' => 'Eraser applied successfully.',
				'data' => [
					'pdf_url' => $pdfUrl,
					'file_name' => $newFileName,
				]
			], 200);

		} catch (\Exception $e) {
			\Log::error('Eraser apply to PDF failed: ' . $e->getMessage());
			return response()->json([
				'status' => false,
				'error_msg' => 'An error occurred while applying eraser to PDF.'
			], 500);
		}
	}

	public function createImageUsingType(Request $request)
	{
	
		$text = $request->input('textbox');
		$fontsize = $request->input('fontsize');
		
		header('Content-Type: image/png');
		
	
		$font = public_path() . '/assets/fonts/' . $fontsize;
		$font_size = 70;
		
		// Get text bounding box to determine width & height
		$box = imagettfbbox($font_size, 0, $font, $text);
		$text_width = abs($box[2] - $box[0]);
		$text_height = abs($box[5] - $box[1]);
		
		// Add padding around the text
		$padding = 30;
		$image_width = $text_width + ($padding * 2);
		$image_height = $text_height + ($padding * 2);
		
		// Create a larger image to improve resolution
		$scale_factor = 2; 
		$im = imagecreatetruecolor($image_width * $scale_factor, $image_height * $scale_factor);
		
		// Colors
		$white = imagecolorallocate($im, 255, 0, 0); 
		$black = imagecolorallocate($im, 0, 0, 0);
		// You can change the text color by replacing $black with $grey if desired
		// $grey = imagecolorallocate($im, 128, 128, 128);
		$grey = imagecolorallocate($im, 0, 0, 0); // Red background
		$opacity = 0;
		$alpha = (int)(127 * (1 - $opacity));
		$transparent = imagecolorallocatealpha($im,255, 255	,255,$alpha);
		
		// Set background to transparent
		imagefill($im, 0, 0, $transparent);
		imagecolortransparent($im, $transparent);
		
		// Draw text on the scaled image
		$x = $padding * $scale_factor;
		$y = ($text_height + $padding) * $scale_factor;
		imagettftext($im, $font_size * $scale_factor, 0, $x, $y, $grey, $font, $text);
		
		// Resize the image back to normal size (anti-aliasing effect)
		$final_im = imagecreatetruecolor($image_width, $image_height);
		
		// Preserve transparency during resizing
		imagealphablending($final_im, false);
		imagesavealpha($final_im, true);
	
		imagefill($final_im, 0, 0, $transparent);
		
		imagecopyresampled($final_im, $im, 0, 0, 0, 0, $image_width, $image_height, imagesx($im), imagesy($im));
		
		// Save Image
		$ims = time() . uniqid() . ".png";
	
		// Handle Storage (S3 or Local)
		if (env('FILE_UPLOAD_PERMISSION') != 'development') {
		
			ob_start();
			imagepng($final_im, null, 9);
			$imageData = ob_get_clean();
			// $fileGetContain = file_get_contents($imagesPng);
			Storage::disk('s3')->put('patientWriteDocument/' . $ims, $imageData);
			// unlink($imagesPng);
			$data = Storage::disk('s3')->get('patientWriteDocument/' . $ims);
			
			$type = pathinfo($ims, PATHINFO_EXTENSION);
			return 'data:image/' . $type . ';base64,' . base64_encode($data);
		} else {
			
			$imagesPng = public_path() . '/patientWriteDocument/' . $ims;
			imagepng($final_im, $imagesPng, 9);
			
			imagedestroy($im);
			imagedestroy($final_im);
			return URL::to('/') . '/patientWriteDocument/' . $ims;
		}
	}
	
	public function upload_documentwebNew_write_documentOld(Request $request)
	{
		$text = $request->input('textbox');
		$fontsize = $request->input('fontsize');

		header('Content-Type: image/png');

		// Font Path
		$font = public_path() . '/assets/fonts/' . $fontsize;

		// Set the base font size and scale factor for better quality
		$font_size = 35; // Base font size
		$scale_factor = 2; // Increase resolution for clarity

		// Calculate text bounding box using scaled font size
		$box = imagettfbbox($font_size * $scale_factor, 0, $font, $text);
		$text_width = abs($box[2] - $box[0]);
		$text_height = abs($box[5] - $box[1]);

		// Define padding (scaled)
		$padding = 20 * $scale_factor;

		// Calculate scaled image dimensions to fit the full text on one line
		$image_width_scaled = $text_width + (2 * $padding);
		$image_height_scaled = $text_height + (2 * $padding);

		// Create a high resolution image
		$im = imagecreatetruecolor($image_width_scaled, $image_height_scaled);

		// Define colors
		$white = imagecolorallocate($im, 255, 255, 255);
		$black = imagecolorallocate($im, 0, 0, 0);
		// You can change the text color by replacing $black with $grey if desired
		$grey = imagecolorallocate($im, 128, 128, 128);

		// Fill the background with white
		imagefilledrectangle($im, 0, 0, $image_width_scaled, $image_height_scaled, $white);

		// Calculate text starting position (with padding)
		$x = $padding;
		$y = $padding + $text_height; // y is the baseline for the text

		// Draw the text on the high resolution image
		imagettftext($im, $font_size * $scale_factor, 0, $x, $y, $grey, $font, $text);

		// Create the final image by resampling to normal resolution
		$final_image_width = $image_width_scaled / $scale_factor;
		$final_image_height = $image_height_scaled / $scale_factor;
		$final_im = imagecreatetruecolor($final_image_width, $final_image_height);

		imagecopyresampled(
			$final_im,  // Destination image
			$im,        // Source image
			0, 0,       // Destination x, y
			0, 0,       // Source x, y
			$final_image_width, $final_image_height, // Destination width, height
			$image_width_scaled, $image_height_scaled  // Source width, height
		);

		// Save the final image to file
		$ims = time() . uniqid() . ".png";
		$imagesPng = public_path() . '/patientWriteDocument/' . $ims;
		imagepng($final_im, $imagesPng, 9); // Save with maximum quality

		// Clean up
		imagedestroy($im);
		imagedestroy($final_im);

		// Handle storage: Upload to S3 if not in development
		if (env('FILE_UPLOAD_PERMISSION') != 'development') {
			$fileGetContain = file_get_contents($imagesPng);
			Storage::disk('s3')->put('patientWriteDocument/' . $ims, $fileGetContain);
			return URL::to('/') . '/patientWriteDocument/' . $ims;
		} else {
			return URL::to('/') . '/patientWriteDocument/' . $ims;
		}
	}


	public function writeDocumentUpload(Request $request)
	{
		$auth = auth()->user();
		$user_id = $request->input('eidc');
		$pending = 'Pending';
		$rand = uniqid();

		$validator = Validator::make($request->all(), [
			'document_name' => 'required',
			'file_upload' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				            'error_msg' => $validator->errors()->all()[0],
				            'status' => false,
				        ], 422);
		} else {
			$imageWriteDocument ='';

			if ($request->file('file_upload') != '') {
				$priceImage = $request->file('file_upload');
				$content = file_get_contents($request->file('file_upload')->getRealPath());
				preg_match_all('/[A-Za-z0-9 ,.\'-]{20,}/', $content, $matches);
				$imageWriteDocument = $this->documentNormalPdfRegenerate($request->all());
			}
			
				$data = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $user_id,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => '',
					'type' => '',
					'sourceFile' => $imageWriteDocument,
					'main_intakeId' => $request->eid,
					'sent_on' => '',
					'groupId' => $rand,
					'template_response' => ''
				);

				$insert = $this->documentSendService->save($data);

			if ($insert) {
				$this->documentUploadHistoryService->save(
				[
					'document_type_flag'=>2,
					'id'=>$insert,
					'document_name'=>$request->document_name,
					'old_attachment'=>$imageWriteDocument,
					'attachment'=>$imageWriteDocument,
					'patient_id'=>$request->eid
				]);
				$documentRes = $this->documentSendService->getDetailsByIdNew($insert);
				unset($documentRes->userDetails);
				$newResponse = $documentRes->toArray();
				$newResponse['write_added_by_name'] = $auth->full_name ?? '';
				// Insert form Log into Dynamic form log table
				$message = auth()->user()->first_name.' '.auth()->user()->last_name.' has added a new Document via the eSign section.';
				$insertLog = [
					'type' => 'Added',
					'link' => url('/write-document-upload'),
					'module' => 'Esign Section',
					'module_id' => $documentRes->groupId,
					'new_response' => serialize($newResponse),
					'old_response' => '',
					'is_status' => 'Added',
					'message'=>$message,
				];
			
				$this->dynamicFormLogAction($insertLog);
				
				$insertLog = [
					'type' => 'Add New Esign Document',
					'link' => url('/write-document-upload'),
					'module' => 'Patient Appointment',
					'object_id' =>$request->eid,
					'message' =>$message,
					'new_response' => serialize($newResponse),
				];

				$this->logAction($insertLog);
				
				$this->saveWriteDocumentData('Esign', $insert, $imageWriteDocument,$request->document_name);

				return response()->json(['status'=>true,'error_msg'=>'Document  successfully uploaded'],200);
			} else {
				return response()->json(['status'=>true,'error_msg'=>'Sorry, something went wrong. Please try again'],500);
			}
		}
	}

	public function saveWriteDocumentData($type, $documentPatientId, $fileUpload,$documentName)
	{
		$data = [
			'document_name' => $documentName,
			'type' => $type,
			'document_patient_id' => $documentPatientId,
			'file_upload' => $fileUpload,
			'created_at'=>date('Y-m-d H:i:s'),
		];
		$this->documentSendService->saveWriteDocumentData($data);
	}

	public function getResponseCanvasHistory($id)
	{
		$template_log_id = request('id');

		$data['document'] = TemplateLog::where('template_id', $id)->where('id',$template_log_id)->where('del_flag', 'N')->first();
		
		$response = '';
		if (isset($data['document']->old_response) && $data['document']->old_response != '') {
			$response = unserialize($data['document']->old_response);
		}
		echo json_encode($response);
	}

	public function getFilteredTemplateUsers(Request $request)
	{
		$template_id = $request->template_id;
		$templateLogs = TemplateLog::where('template_id', $template_id)->get(); 
		$esignTemplateUsers = $this->esignReportService->esignTemplateUserList($template_id);
		$templateLogs = $templateLogs->sortBy('created_date')->values();
		$filteredTemplateUsers = [];

		foreach ($templateLogs as $index => $log) {
			$currentDate = $log->created_date;
			$nextDate = isset($templateLogs[$index + 1]) ? $templateLogs[$index + 1]->created_date : date('Y-m-d h:i:s');

				$filteredUsers = $esignTemplateUsers->filter(function ($user) use ($currentDate, $nextDate) {
					return $user['created_date'] >= $currentDate && $user['created_date'] <= $nextDate;
				});

				$filteredTemplateUsers[] = [
					'current_date' => $currentDate,
					'next_date' => $nextDate,
					'user_count' => $filteredUsers->count(),
					'users' => $filteredUsers,
				];
				
			}

		return response()->json(['filteredTemplateUsers' => $filteredTemplateUsers]);
	}

	public function documentNew()
	{
		$data['user'] = $admin_login = auth()->user();

		$id = request('id');

		$data['document'] = $this->templateService->getDetailsById($id);

		//new design
		$data['getTemplateLog'] = $this->templateService->getTemplateLogData($id);
		$data['esignTemplateUser'] = $this->esignReportService->esignTemplateUserList($id);

		$data['filteredCounts'] = [];
		$templateLogs = $data['getTemplateLog'];
		$esignTemplateUsers = $data['esignTemplateUser'];

		$templateLogs = $templateLogs->sortBy('created_date')->values();

		foreach ($templateLogs as $index => $log) {
			$currentDate = $log->created_date;
			$nextDate = isset($templateLogs[$index + 1]) ? $templateLogs[$index + 1]->created_date : date('Y-m-d h:i:s');

			$filteredUsers = $esignTemplateUsers->filter(function ($user) use ($currentDate, $nextDate) {
				return $user['created_date'] >= $currentDate && $user['created_date'] <= $nextDate;
			});
	
			$data['filteredTemplateUsers'][] = [
				'current_date' => $currentDate,
				'next_date' => $nextDate,
				'user_count' => $filteredUsers->count(),
				'users' => $filteredUsers,
			];
		}
		//new design
		$data['matchingFormIds'] = $this->agencyAllFormService->getAgencyMasterData($data['document']->custom_form_id);
		
		$final_array = array();
		$countArray = array();
		$max = array();
		$testp = array();
		
		$data['savedWidth'] = $docWidth = $data['document']->docWidth;
		$response = unserialize($data['document']->response);
		$maxprice = '0';
		if (isset($response) && $response != '') {
			$final_array[] = $docWidth;
			foreach ($response as $val) {
				if (isset($val['obj']) && $val['obj'] != '') {
					$obj = $val['obj'];
				} else {
					$obj = '';
				}

				$final_array[] = $val;

				$countArray[] = $obj;
				$explode = explode('_', $val['id']);
				$max[] = $explode[1];
				$maxprice = max($max);
			}
		}
		$data['count'] = '';
		if ($maxprice != '') {
			$data['count'] = $maxprice;
		}

		$insertLog = [
			'type' => 'View',
			'link' => url('/document'),
			'module' => 'Template',
			'object_id' => $id,
			'message' => $admin_login->first_name . ' ' . $admin_login->last_name . ' has view Template',
			'new_response' => serialize($final_array),
			
		];
		$this->logAction($insertLog);

		$data['templateFields'] = json_encode($final_array, true);
		$data['template_id'] = $id;
		
		return view('docusign.new_template_document_1', $data);
	}

	public function getResponseCanvasNew1($id)
	{
		$data['document'] =  $this->documentSendService->getDetailsById($id);

		if($data['document']->agency_form_id != null){
			$agencyForm = $this->agencyAllFormService->getAgencyFormWithDoctors($data['document']->agency_form_id);

			$doctorSignature = null;
			if ($agencyForm && $agencyForm->doctors && $agencyForm->doctors->is_signature_stamp_active == 1) {
				$doctorSignature =$this->getImagesForAwsServer($agencyForm->doctors->signature_upload,'signature');
			}
	
			$doctorStamp = null;
			if ($agencyForm && $agencyForm->doctors && $agencyForm->doctors->is_signature_stamp_active == 1) {
				$doctorStamp =$this->getImagesForAwsServer($agencyForm->doctors->stamp_upload,'stamp');
			}
		}else{
			$doctorSignature = null;
			if ($data['document'] && $data['document']->doctors && $data['document']->doctors->is_signature_stamp_active == 1) {
				$doctorSignature =$this->getImagesForAwsServerNew($data['document']->doctors->signature_upload,'signature');
			}

			$doctorStamp = null;
			if ($data['document'] && $data['document']->doctors && $data['document']->doctors->is_signature_stamp_active == 1) {
				
				$doctorStamp =$this->getImagesForAwsServerNew($data['document']->doctors->stamp_upload,'stamp');
			}
		}
		
		$templDetails = $this->templateService->getDetailsById($data['document']->templete_id);

		$response = '';
		if (isset($templDetails->response) && $templDetails->response != '') {
			$response = unserialize($templDetails->response);
		}

		$response['doctor_signature'] = $doctorSignature;
		$response['doctor_stamp'] = $doctorStamp;
	
		echo json_encode($response);
	}

	public function getImagesForAwsServerNew($img, $type)
	{
		if(empty($img)){
			return '';
		}
		if ($type == 'stamp') {
			$fileUrl = url(self::DOCUSIGN_FOLDER.'/' . $img);
			$file = public_path(self::DOCUSIGN_FOLDER.'/' .$img);

			if (file_exists($file)) {
				$fileData = $fileUrl;
			} else {
				$path = self::DOCUSIGN_FOLDER.'/' . $img;
				$fileData = Storage::disk('s3')->temporaryUrl(
					$path,
					Carbon::now()->addMinutes(10)
				);
			}
			return $fileData;
		} else {
			$fileUrl = url(self::DOCUSIGN_FOLDER.'/' . $img);
			$file = public_path(self::DOCUSIGN_FOLDER.'/' . $img);

			if (file_exists($file)) {
				$fileData = $fileUrl;
			} else {
				$path = self::DOCUSIGN_FOLDER.'/' . $img;
				$fileData = Storage::disk('s3')->temporaryUrl(
					$path,
					Carbon::now()->addMinutes(10)
				);
			}

			return $fileData;
		}
	}

	public function syncTemplateData($id)
    {
        try {
            $document = $this->templateService->getDetailsById($id);
            if (!$document) {
                return response()->json(['message' => 'Document not found'], 404);
            }

            $response = unserialize($document->response);
            $final = [];

            foreach ($response as $val) {
                $explode = explode('_', $val['id']);
                if ($explode[0] != "datesigned") {
                    $final[] = $val;
                }
            }

            $this->templateService->update(
                ['response' => serialize($final),'old_response'=>$document->response],
                ['id' => $id]
            );

            return response()->json(['message' => 'Data synchronized successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error synchronizing data', 'error' => $e->getMessage()], 500);
        }
    }

	public function notificationSend($type,$title,$msg,$patientId,$agencyFk,$userType,$userData=[],$serviceData=[]){
        $userData = Utility::getGroupUsersData($agencyFk,$userType,$type,$userData,$serviceData);
		$notificationData = array(
			'users' => $userData,
			'agency_fk' => $agencyFk ?? '',
			'record_id' => $patientId ?? '',
			'title' => $title,
			'msg' => $msg,
			'type' => $type
		);
		Utility::insertNotificationsType($notificationData);

    }

	public function getSignerAction($sentOn) {
		$actions = [
			'formFill'   => 'Form Fill Done',
			'sign'       => 'Signature Done',
			'stamp'      => 'Stamp Done',
			'other'      => 'Other User Sign Done',
			'caregiver'  => 'Caregiver Signature Done',
			'stampUser'  => 'Stamp User Done',
			'patient'    => 'Patient Signature Done',
			'OfficeStaff'=> 'Office Staff Signature Done',
		];
		return $actions[$sentOn] ?? 'Signature Done';
	}

	public function ajaxList(Request $request){
		$data['user'] = $admin_login = auth()->user();
		$id = request('id');
		$data['template_name'] = $template_name = $request->template_name;
		$data['lookup_fields'] = $lookup_fields = $request->lookup_fields;
		$data['agency_fk'] = $agency_fk = $request->agency_fk;
		$data['selected_agency_fk'] =$agency_fk;
		$data['status'] = $status = $request->status;
		$data['created_date'] = $created_date = $request->created_date;
		$data['updated_date'] = $updated_date = $request->updated_date;

		if(isset($agency_fk)){
			if(count($agency_fk) >0){
				$agency_fk = implode(',',$agency_fk);
			}
		}

		$data['templete_list'] = $this->templateService->templateList($id, $template_name, $lookup_fields, $status,$agency_fk, $created_date, $updated_date);
		
		$documenrs = DocumentType::where('del_flag', 'N');
		if ($admin_login->user_type_fk == 184) {
			$documenrs->where('type', 'nybest');
		} else {
			$documenrs->where('type', 'exmedc');
		}
		$documenrs = $documenrs->orderBy('name', 'asc')->get();
		$data['document_list'] = $documenrs;
		return view('template.ajax_list', $data);
	}

	public function loadAllAgenciesByTemplateId(Request $request){
		$query =$this->templateService->getDetailsById($request->id);
		$agencyIds = explode(',',$query->agency_id);
		
		$final = [];
		if(count($agencyIds) >0){
			foreach($agencyIds as $val){
				if($val !=""){
					$getAgencyDetails =$this->agencyService->getDetailsById($val);
					$temp = [];
					$temp['name'] =$getAgencyDetails->agency_name;
					$final[] = $temp;
				}
				
			}
		}

		return response()->json(['data'=>$final,'name'=>$query->template_name],200);
	}

	public function documentNormalPdfRegenerate($data){
		$priceImage = $data['file_upload'];
		$name = uniqid() . time() . '.' . $priceImage->getClientOriginalExtension();
		$writeDocumentDestination = public_path('patientWriteDocument');

		if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
			
			$priceImage->move($writeDocumentDestination, $name);
			$imageWriteDocument = $name;
	
		}else{
			Storage::disk('s3')->putFileAs('patientWriteDocument', $priceImage, $name);
			$imageWriteDocument = $name;
		}

		return $imageWriteDocument;
	}

	public function documentOtherPdfRegenerate($requestData){
		
		$imageData = $requestData['file_upload'];
		$imagick = new \Imagick();
		$imagick->setResolution(300, 300);
		$imagick->readImage($imageData->getRealPath());
	
		$path = public_path('/tempPDFGenerate');
		if (!file_exists($path)) {
			mkdir($path, 0755, true); // true = recursive
		}
		$pdf = new PDF(null, 'mm','legel');
	
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
	
		$pdf->SetMargins(0, 0, 0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetAutoPageBreak(false, 0);
	
		$a4Width = 210;
		$a4Height = 297;
		$dpi = 300;
	
		foreach ($imagick as $i => $page) {
			// Ensure image format is PNG
			$page->setImageFormat('png');
		
			// Flatten image onto white background to eliminate transparency
			$flattened = new \Imagick();
			$flattened->newImage($page->getImageWidth(), $page->getImageHeight(), 'white', 'png');
			$flattened->compositeImage($page, \Imagick::COMPOSITE_OVER, 0, 0);
		
			// Save to temp folder
			$uniqueId = uniqid();
			$tmpPath = $path . "/page_{$i}_{$uniqueId}.png";
			$flattened->writeImage($tmpPath);
		
			list($imgWidthPx, $imgHeightPx) = getimagesize($tmpPath);
		
			// Convert pixels to mm based on 300 DPI
			$imgWidthMm = ($imgWidthPx / $dpi) * 25.4;
			$imgHeightMm = ($imgHeightPx / $dpi) * 25.4;
		
			$imgRatio = $imgWidthMm / $imgHeightMm;
			$pageRatio = $a4Width / $a4Height;
		
			if ($imgRatio > $pageRatio) {
				$width = $a4Width;
				$height = $a4Width / $imgRatio;
			} else {
				$height = $a4Height;
				$width = $a4Height * $imgRatio;
			}
		
			$orientation = ($width > $height) ? 'L' : 'P';
			$pdf->AddPage($orientation);
		
			$x = ($a4Width - $width) / 2;
			$y = ($a4Height - $height) / 2;
		
			$pdf->Image($tmpPath, $x, $y, $width, $height, 'PNG', '', '', false, $dpi, '', false, false, 0, false, false, false);
			unlink($tmpPath);
			$flattened->clear();
			$flattened->destroy();
		}
		
		$imagick->clear();
		$imagick->destroy();
		$uniqueId = uniqid();
		$outputPath = public_path('/').'/tempPDFGenerate/generated_pdf_'.$uniqueId.'.pdf';
		$pdf->Output($outputPath, 'F');
	
		$name = uniqid() . time() . '.' . $imageData->getClientOriginalExtension();
		$destination1 = public_path('patientWriteDocument');
		
		$contain = file_get_contents($outputPath);
		if (env('FILE_UPLOAD_PERMISSION') == 'development') {
			
			file_put_contents($destination1 . '/' . $name, $contain);
			
			$image = $name;
		} else {
			Storage::disk('s3')->put('patientWriteDocument/' . $name, $contain);
			$image = $name;
		}
	
		unlink($outputPath);
		return $image;
	}

	public function getSignerNotification(Request $request){
		$template = $this->templateService->getDetailsById($request->id);
		if(!$template){
			return response()->json(['status' => false, 'error_msg' => 'Template not found'], 404);
		}
		$allocatedSigners = $this->documentSignerService->getallocatedSigners($request->id);
		$savedSignerTypes = [];
		if($template->template_signer_type){
			$savedSignerTypes = explode(',', $template->template_signer_type);
		}
		$signerLabels = [
			'Caregiver' => 'caregiver',
			'Patient' => 'patient',
			'OfficeStaff' => 'officestaff',
			'StampUser' => 'stampuser',
			'Other' => 'other',
			'FormFill' => 'formfill',
			'Sign' => 'sign',
			'Stamp' => 'stamp',
		];
		return response()->json([
			'status' => true,
			'allocated_signers' => $allocatedSigners,
			'signer_types' => $savedSignerTypes,
			'signer_labels' => $signerLabels,
		], 200);
	}

	public function saveSignerNotification(Request $request){
		$admin_login = auth()->user();
		$template = $this->templateService->getDetailsById($request->id);
		$oldData = $template;
		if(!$template){
			return response()->json(['status' => false, 'error_msg' => 'Template not found'], 404);
		}
		$signerTypes = $request->signer_types;
		$template->template_signer_type = !empty($signerTypes) ? implode(',', $signerTypes) : null;
		$template->updated_date = date('Y-m-d H:i:s');
		$template->updated_by = auth()->user()->id;
		$template->save();

		if ($template) {
			$insertLog = [
				'type' => 'Update',
				'link' => url('/esign/template-signer-notification-save'),
				'module' => 'Template',
				'object_id' => $request->id,
				'message' => $admin_login->first_name . ' ' . $admin_login->last_name . ' has updated Signer notification',
				'old_response' => serialize($oldData),
				'new_response' => serialize($template),
				
			];
			$this->logAction($insertLog);
		}
		return response()->json(['status' => true, 'error_msg' => 'Signer notification saved successfully'], 200);
	}

	protected function logAction($logAction){
		$insertLog = [
			'type' => $logAction['type'],
			'link' => $logAction['link'],
			'module' => $logAction['module'],
			'object_id' => $logAction['object_id'],
			'message' =>$logAction['message'],
			'new_response' => $logAction['new_response'],
			'ip' => Utility::getIP(),
		];

		if(isset($logAction['old_response'])){
			$insertLog['old_response'] = $logAction['old_response'];
		}

		LogsService::save($insertLog);
	}

	protected function dynamicFormLogAction($data){

		$insertLog = [
			'type' => $data['type'],
			'link' =>$data['link'],
			'module' => $data['module'],
			'module_id' => $data['module_id'],
			'new_response' => $data['new_response'],
			'ip'=> Utility::getIP(),
			'is_status'=>$data['is_status']??"",
			'message'=>$data['message']??"",
			'esign_new_response'=>$data['esign_new_response']??""
		];

		if(isset($data['old_response'])){
			$insertLog['old_response']= $data['old_response'];
		}

		$this->dynamicFormLogService->storeFormLog($insertLog);
	}

	public function fetchEraserPdf(Request $request){
		$id = request('document_write_id');

		$query = $this->documentSendService->getWriteDataByUniqueId($id);
		$file = $query->file_upload;
		$dir = public_path(). '/patientWriteDocument/' . $file;
		
		if (file_exists($dir)) {
			$filePath = public_path('patientWriteDocument').'/'.$file;
			$headers = [];
			return response()->download($filePath, basename($file), $headers);
		} else {
			return Storage::disk('s3')->download('patientWriteDocument/' . $file);
			die();
		}
	}
}