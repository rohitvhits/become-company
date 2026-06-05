<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

use App\Services\InvoiceUploadService;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class InvoiceUploadController extends BaseController
{

	protected $invoiceUploadService  = "";
	protected $PatientService  = "";

	public function __construct(InvoiceUploadService $invoiceUploadService, PatientService $PatientService)
	{
		$this->invoiceUploadService = $invoiceUploadService;
		$this->PatientService = $PatientService;
	}


	function ajaxInvoiceUploadList(Request $request)
	{
		$response = $this->invoiceUploadService->getInvoiceTableList($request->patient_id, $request->agency_id);

		return response()->json(['status' => true, 'data' => $response]);
	}

	public function invoiceSave(Request $request)
	{

		$user = auth()->user();

		$validator = Validator::make($request->all(), [
			'request_service_id' => 'required',
			'document_service_id' => 'required',
			'attachment' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {

			if ($request->file('attachment') != '') {
				$attachment = '';
				$invoiceImage = $request->file('attachment');
				$name = uniqid() . time() . '.' . $invoiceImage->getClientOriginalExtension();

				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
					$destination = public_path('invoice-document');
					$invoiceImage->move($destination, $name);
				} else {
					$attachment = Storage::disk('s3')->putFileAs('invoice-document', $invoiceImage, $name);
				}
				$attachment = $name;
			}
			$data = array(
				'request_service_id' => $request->input('request_service_id'),
				'service_id' => $request->input('document_service_id'),
				'patient_id' => $request->input('patient_id'),
				'agency_id' => $request->input('agency_id'),
				'created_by' => $user->id,
			);
			if (isset($attachment) && !empty($attachment)) {
				$data['attachment'] = $attachment;
			}
			$insert = $this->invoiceUploadService->save($data);

			if ($insert) {
				return response()->json(['status' => true, 'error_msg' => 'Invoice added successfully'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	function destroy($id)
	{
		$update = $this->invoiceUploadService->SoftDelete(array('id' => $id));
		if ($update) {
			return response()->json(['status ' => "1", 'error_msg' => "Invoice successfully deleted.", 'data' => array()], 200);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	public function invoiceDocumentUploadByPatient(Request $request)
	{
		$user = auth()->user();

		$validator = Validator::make($request->all(), [
			'upload_invoice_document_id' => 'required',
			'images' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$image = '';

			if ($request->file('images') != '') {
				$priceImage = $request->file('images');
				$name = uniqid() . time() . '.' . $priceImage->getClientOriginalExtension();
				$destination = public_path('invoice-document');

				$image = $filepath = Storage::disk('s3')->putFileAs('invoice-document', $priceImage, $name);
				$image = $name;
			}

			if ($request->input('upload_invoice_document_id') != '') {
				$data = array(
					'attachment' => $image,
				);

				$insert = $this->invoiceUploadService->update($data, array('id' => $request->input('upload_invoice_document_id')));

				if ($insert) {
					Session::flash('success', 'Document  successfully uploaded.');
					return redirect()->back();
				} else {
					Session::flash('error', 'Sorry, something went wrong. Please try again.');
					return redirect()->back();
				}
			}
		}
	}

	public function showDocument($id)
	{
		$auth = auth()->user();

		$getDetails = $this->invoiceUploadService->getDetailsById($id);
		if (isset($getDetails->patient_id)) {
			$getPatientDetails = $this->PatientService->getDetailByIdNew($getDetails->patient_id);

			if (isset($getPatientDetails->agency_id)) {
				$file = public_path('/') . "/invoice-document/" . $getDetails->attachment;
				$headers = [];
				if (str_contains($getDetails->attachment, 'invoice-document')) {
					return   Storage::disk('s3')->download($getDetails->attachment);
					die();
				} else {
					return   Storage::disk('s3')->download('invoice-document/' . $getDetails->attachment);
					die();
				}

				return response()->download($file, $getDetails->attachment, $headers);
			} else {
				abort(404);
			}
		} else {
			abort(404);
		}
	}

	public function show($id) {}
}
