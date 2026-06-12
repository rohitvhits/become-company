<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EsignReportService;
use App\Services\LocationMasterService;
use App\Services\DocumentSendService;
use App\Services\DocumentSendSmsLogService;
use App\Services\SmsService;
use App\Services\DynamicFormLogService;
use App\Services\LogsService;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Agency;

use App\Services\MasterService;
use App\Services\PatientService;

class EsignReportController extends Controller
{

	protected $esignReportService;
	protected $masterService;
	protected $documentSendService;
	protected $documentSendSmsLogService;
	protected $smsService;
	protected $dynamicFormLogService;
	protected $patientService;
	protected const SMS_ESIGN_LINK = 'esign/nye/';
	protected const LOG_ESIGN_SMS_LINK = '/esign/esign-report/esign-bulk-send-sms';
	protected const MODULE_TYPE = 'Patient Appointment';

	public function __construct(
		EsignReportService $esignReportService,
		MasterService $masterService,
		DocumentSendService $documentSendService,
		DocumentSendSmsLogService $documentSendSmsLogService,
		SmsService $smsService,
		DynamicFormLogService $dynamicFormLogService
	) {
		$this->middleware('auth');
		$this->middleware('permission:esign-report-list', ['only' => ['index', 'ajaxList']]);

		$this->esignReportService = $esignReportService;
		$this->documentSendService = $documentSendService;
		$this->documentSendSmsLogService = $documentSendSmsLogService;
		$this->smsService = $smsService;
		$this->dynamicFormLogService = $dynamicFormLogService;
		$this->masterService = $masterService;
	}

	public function index(Request $request)
	{
		$data['menu'] = "";
		$data['user'] = $auth = auth()->user();
		$data['auth'] = $auth;
		if (!$auth || $auth == null) {
			return redirect('login');
		}

		$data['templateList'] = $this->esignReportService->getAllTemplateList();
		$angecyList = Cache::get('patient_master_esign_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		
		$data['agencyList'] = $angecyList;
		
		$data['search_param'] = $request->all();

		$data['masterData'] = Cache::get('esign_patient_master_services', function () {
			return $this->masterService->getAllDataByMasterTypeFk(array(17, 26,35));
		}, 10);

		return view('esign_report/esign_report_list', $data);
	}

	public function ajaxList(Request $request)
	{
		$data['query'] = $this->esignReportService->dataListNew($request->all());
		
		if (!empty($data['query'][0])) {
			foreach ($data['query'] as $key => $val) {
				$totalSigner = $this->esignReportService->TotalSignerCount($val->groupId);
				$data['query'][$key]->signerRemaining = $totalSigner[0]->total ?? 0;

				$review_first_name = "";
				if(isset($val->reviewDetails->first_name) && $val->reviewDetails->first_name !=""){
					$review_first_name = $val->reviewDetails->first_name.' '.$val->reviewDetails->last_name;
				}

				$data['query'][$key]->review_first_name = $review_first_name;

				$data['query'][$key]->caregiverSignPending = $this->esignReportService->isCaregiverSignPending($val->groupId);

				$signerCounts = $this->esignReportService->getSignerCounts($val->groupId);
				$data['query'][$key]->completedSignerCount = $signerCounts['completed'];
				$data['query'][$key]->totalSignerCount = $signerCounts['total'];
			}
		}

		$data['esignView'] = auth()->user()->can('esign-view');
		$data['esignDelete'] = auth()->user()->can('esign-delete');
		$data['esignSendSms'] = auth()->user()->can('esign-send-sms');
		$data['esignViewLog'] = auth()->user()->can('esign-view-log');
		$data['esignPdfDownload'] = auth()->user()->can('esign-pdf-download');
		$data['esignMoveDocument'] = auth()->user()->can('esign-move-document');
		$data['esignRevert'] = auth()->user()->can('esign-revert');
		$data['esignReview'] = auth()->user()->can('esign-review');
		return view("esign_report/esign_report_ajax_list", $data);
	}

	public function reportExport(Request $request)
	{
		//ini_set('memory_limit', '4096M');

		$response = $this->esignReportService->dataListNew($request->all(),'export');
		$filename = 'esign_report_' . date("m-d-Y") . '.csv';

		$headers = [
			"Content-Type" => "text/csv",
			"Content-Disposition" => "attachment; filename={$filename}",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		];

		$columns = [
			'No',
			'Agency Name',
			'Patient Name',
			'Type',
			'Template Name',
			'Status',
			'Sender Name',
			'Completed Date',
			'Review Date',
			'Review By',
			'Created Date',
			'Created By'
		];

		$callback = function () use ($response, $columns) {
			$file = fopen('php://output', 'w');

			fputcsv($file, $columns);

			$cnt = 1;

			foreach ($response as $list) {
				$status = $list->status ?? 'N/A';
				$patient_name = isset($list->patient)
					? $list->patient->first_name . ' ' . $list->patient->last_name
					: 'N/A';
				
				$patient_type = isset($list->patient)
					? $list->patient->type
					: 'N/A';

				$templete_name = isset($list->templateDetails)
					? $list->templateDetails->template_name : 'N/A';
				$sender_name = isset($list->sender_name)
					? $list->sender_name : 'N/A';
				$completed_on = Utility::convertMDYTime($list->completed_on)?? null;
				$review_date = Utility::convertMDYTime($list->review_date) ?? null;
				$created_at = Utility::convertMDYTime($list->created_date) ?? null;
				$created_by_name = isset($list->userDetails)
					? $list->userDetails->first_name . ' ' . $list->userDetails->last_name
					: 'N/A';
				$agency_name = isset($list->patient->agencyDetail)
				? $list->patient->agencyDetail->agency_name
				: 'N/A';

				$review_first_name = isset($list->reviewDetails->first_name)
				? $list->reviewDetails->first_name
				: 'N/A';
				$review_last_name = "";
				if($review_first_name !="N/A"){
					$review_last_name = isset($list->reviewDetails->last_name)
					? $list->reviewDetails->last_name
					: 'N/A';
				}
				
				fputcsv($file, [
					$cnt++,
					$agency_name,
					$patient_name.'('.$list->patient->id.')',
					$patient_type,
					$templete_name,
					$status,
					$sender_name,
					$completed_on,
					$review_date,
					$review_first_name.' '.$review_last_name,
					$created_at,
					$created_by_name
				]);
			}

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
	}

	public function searchNyBestPatient(Request $request){
		$query = $this->esignReportService->searchNybestPatient($request->q);
		$final = [];
		foreach($query as $val){
			$temp = [];
			$temp['id'] = $val->id;
			$temp['name'] = $val->first_name .' '. $val->last_name;
			$final[] = $temp;
		}
		return json_encode($final);
	}

	public function searchNyBestAllUser(Request $request){
		$query = $this->esignReportService->searchNybestAllUser($request->q);
		$final = [];
		foreach($query as $val){
			$temp = [];
			$temp['id'] = $val->id;
			$temp['name'] = $val->first_name .' '. $val->last_name;
			$final[] = $temp;
		}
		return json_encode($final);
	}

	public function bulkSendSMS(Request $request)
	{

		$documents = $request->documents;

		if (empty($documents) ) {
			return response()->json(['results' => []], 400);
		}

		$results = [];
		foreach ($documents as $item) {
			if(isset($item['mobile']) && $item['mobile'] !=""){
				try {
					$query = $this->esignReportService->getGroupPendingDocument(
						$item['groupId'],
						$item['document_send_type'] ?? null
					);
				
					if (!isset($query->id)) {
						$results[] = [
							'groupId' => $item['groupId'],
							'portalId' => $item['portal_id'] ?? '',
							'portalName' => '',
							'templateName' => '',
							'mobile' => $item['mobile'] ?? '',
							'status' => 'failed',
							'error' => 'Document not found or already completed'
						];
						continue;
					}

					$docInfo = $this->esignReportService->getDocumentWithRelations($query);

					$link = $this->prepareLink($query->id, $query->groupId);
					$singleRequest = new Request($item);
					$singleRequest->newGroupId = $query->groupId;
					$emailsUpdate = '';
					$mobileUpdate = '';

					$smsResponse = $this->sendSMS($query->main_intakeId, $item['mobile'], $link, $request->message);
					$mobileUpdate = $item['mobile'];

					$this->updateLogsAndRecords($query->id, $item['portal_id'], $emailsUpdate, $mobileUpdate,$smsResponse,$request->message,$item['groupId']);
					$results[] = [
						'groupId' => $item['groupId'],
						'portalId' => $item['portal_id'],
						'portalName' => $docInfo['portalName'],
						'templateName' => $docInfo['templateName'],
						'mobile' => $mobileUpdate,
						'status' => 'success',
						'error' => 'Message sucecssfully send'
					];
				} catch (\Throwable $th) {
					$results[] = [
						'groupId' => $item['groupId'],
						'portalId' => $item['portal_id'] ?? '',
						'portalName' => '',
						'templateName' => '',
						'mobile' => $item['mobile'] ?? '',
						'status' => 'failed',
						'error' => $th->getMessage()
					];
				}
			}
		}

		return response()->json(['error_msg'=>'Bulk sms successfully send','data'=>$results],200);
	}

	private function prepareLink($docId, $groupId)
	{
		return URL::to(self::SMS_ESIGN_LINK) . '/' . $docId . '?id=' . $groupId;
	}

	private function sendSMS($intakeId, $mobile, $link, $notes)
	{
		$smsMessage = "Dear,\nPlease complete esign from below link.\n{$link}";
		if(isset($notes) && $notes !=""){
			$smsMessage = $notes. "\nPlease complete esign from below link.\n{$link}";
		}
		return $this->smsService->bulkEsignSmsDynamic($intakeId, $mobile, $smsMessage);
	}

	private function updateLogsAndRecords($docId, $portalId, $email, $mobile,$smsId,$message,$groupId)
	{
		$updatedData = [
			'document_id' => $docId,
			'caregiver_id' => $portalId,
			'message' => $message,
			'email' => $email,
			'mobile' => $mobile,
		];

		$this->documentSendSmsLogService->save($updatedData);
		$saveResponse = ['email' => $email, 'sms' => $mobile,'send_sms_mobile_no'=>$mobile,'bulk_send_sms_text'=>$message,'sms_id' => $smsId['smsId'],'sms_status' => $smsId['status']];
		$this->documentSendService->update($saveResponse, ['id' => $docId]);

		$user = auth()->user();
		$messages = $user->first_name . ' ' . $user->last_name . ' has sent a bulk e-sign message';
		$saveResponse['message'] = $message;
		$insertLog = [
			'type' => 'Bulk Send Esign SMS',
			'link' => url(self::LOG_ESIGN_SMS_LINK),
			'module' => self::MODULE_TYPE,
			'object_id' => $portalId,
			'message' => $messages,
			'new_response' => serialize($saveResponse),
			'ip' => Utility::getIP(),
		];
	
		LogsService::save($insertLog);

		$insertLog = [
			'type' => 'Send Esign SMS',
			'link' => url(self::LOG_ESIGN_SMS_LINK),
			'module' => 'Esign Section',
			'module_id' => $groupId,
			'new_response' => serialize($saveResponse),
			'message' => $message,
			'is_status' => 'Send SMS - Email'
		];
		$this->dynamicFormLogService->storeFormLog($insertLog);
	}
}
