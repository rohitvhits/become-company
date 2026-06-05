<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FeedbackQuestionFormService;
use App\Services\FeedbackAnswerFormService;
use App\Services\AgencyWiseServiceService;
use Illuminate\Support\Facades\Cache;
use App\Services\LogsService;
use App\Agency;
use App\Master;
class FeedbackFormReportController extends Controller
{

	protected $feedbackQuestionFormService,$feedbackAnswerFormService,$AgencyWiseServiceService = "";

	public function __construct(FeedbackQuestionFormService $feedbackQuestionFormService, FeedbackAnswerFormService $feedbackAnswerFormService,AgencyWiseServiceService $AgencyWiseServiceService)
	{
		$this->middleware('auth');
		$this->middleware('permission:feedback-form-report-list', ['only' => ['index', 'ajaxList']]);

		$this->feedbackQuestionFormService = $feedbackQuestionFormService;
		$this->feedbackAnswerFormService = $feedbackAnswerFormService;
		$this->AgencyWiseServiceService = $AgencyWiseServiceService;
	}

	public function index()
	{
		$data['menu'] = "";
		$data['user'] = $auth = auth()->user();

		if (!$auth || $auth == null) {
			return redirect('login');
		}

		$angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$data['agencyList'] = $angecyList; 
        $agency_fk = '';
        $user = auth()->user();
		return view('feedbackFormReport/feedback_form_report_list', $data);
	}

	public function ajaxList(Request $request)
	{
		$data['query'] = $this->feedbackAnswerFormService->dataList($request->all());
		return view("feedbackFormReport/feedback_form_report_ajax_list", $data);
	}
}
