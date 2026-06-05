<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\DocumentWorkflowService;
use App\Services\DocumentPatientService;
use App\Services\DocumentSendService;

class DocumentWorkflowController extends Controller
{
	protected const ERROR_MSG = "Sorry, something went wrong. Please try again.";
	protected $documentWorkflowService;
	protected $documentPatientService;

	public function __construct(
		DocumentWorkflowService $documentWorkflowService,
		DocumentPatientService $documentPatientService
	) {
		$this->middleware('auth', ['except' => ['markAsSignatureRequired']]);
		$this->documentWorkflowService = $documentWorkflowService;
		$this->documentPatientService = $documentPatientService;
	}

	public function temparyData($id){
		$this->documentWorkflowService->testingAccount($id);
	}
	public function markAsSignatureRequired(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'document_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->first(), 'status' => 0], 422);
		}

		try {
			$result = $this->documentWorkflowService->markAsSignatureRequired($request->document_id);
		
			if (isset($result['status'])) {
				return response()->json(['status' => 1, 'error_msg' => $result['error_msg']], 200);
			}
			return response()->json(['status' => 0, 'error_msg' => $result['error_msg']], 404);
		} catch (\Throwable $th) {
			return response()->json(['status' => 0, 'error_msg' => $result['error_msg']], 500);
		}
		
			
		
	}
}
