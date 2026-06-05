<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Record;
use App\User;
use App\Agency;
use App\Services\AssignEMCRecordService;
use App\Services\AssignEMCNotesRecordService;
use App\Services\DoctorPaperWorkService;
use Illuminate\Notifications\Notification;
use App\Notifications\MyFirstNotification;
use URL;
use Mail;

class AssignEMCRecordController extends BaseController
{

	public function __construct(AssignEMCRecordService $assignEmcRecord, AssignEMCNotesRecordService $assignEmcNotesRecord)
	{
		$this->middleware('auth');
		$this->assignEmcRecord = $assignEmcRecord;
		$this->assignEmcNotesRecord = $assignEmcNotesRecord;
	}

	public function save(Request $request)
	{
		$auth = auth()->user();
		$data_save = array(
			'record_id' => $request->input('emc_record_id'),
			'emc_id' => $request->input('emc_ems_pid'),
			'progress_notes' => $request->input('progress_notes_id'),
			'disability_questionaire' => $request->input('disability_questionaire'),
			'medical_report' => $request->input('medical_report'),


		);

		$insert = $this->assignEmcRecord->save($data_save);
		if ($insert) {
			$message = 'Hello ' . $request->input('emcusername') . ' new record ' . $request->input('emc_record_id') . ' has assigned to you';
			$details = [
				"greeting" => 'Exmedc',
				'actionText' => 'View Record',

				'body' => $message,
				'actionURL' => URL::to('/') . '/record/' . $request->input('emc_record_id'),
				'record_id' => $request->input('emc_record_id')
			];
			// $agencyd = User::find($request->input('emc_ems_pid'));

			// $agencyd->notify(new MyFirstNotification($details));

			/*$getDetails = User::where('id',$request->input('emc_ems_pid'))->first();
			$messages =  'Hello '.$request->input('emcusername').'<br>';
			$messages .= ' New record '.$request->input('emc_record_id').' has assigned to you by '.$auth['first_name'].' '.$auth['last_name'];
		
			$subject = "Assigned New Record";
			$allemails = isset($getDetails->email)?$getDetails->email:"";
			echo $mail= Mail::send( [],[], function ($message) use($allemails,$subject, $messages){
			  $message->to($allemails,"EMC Rep") 
				->subject($subject)->setBody( $messages, 'text/html');
				//  $message->bcc('hiten@virtualheight.com',"hiten");
				 
				
			});*/
			$getDoctorPaperWorkDetails = DoctorPaperWorkService::getDetailsByRecordId($request->input('emc_record_id'));
			if (isset($getDoctorPaperWorkDetails->id) && $getDoctorPaperWorkDetails->id != '') {

				DoctorPaperWorkService::update(array('rep_id' => $request->input('emc_ems_pid'), 'emc_user_id' => $request->input('emc_ems_pid')), array('id' => $getDoctorPaperWorkDetails->id));
			} else {
				$getRecordDetails = Record::getDetailsByRecordid($request->input('emc_record_id'));
				$getAgencyDetails = Agency::getDetailsByAgencyId($getRecordDetails->agency_fk);
				$data = array(
					'name' => $getRecordDetails->first_name . ' ' . $getRecordDetails->middle_name . ' ' . $getRecordDetails->last__name,
					'portal_id' => $request->input('emc_record_id'),
					'gender' => $getRecordDetails->gender,
					'dob' => $getRecordDetails->dob,
					'doctor_name' => "",
					'phone' => $getRecordDetails->phone,
					'fax' => "",
					'agency' => isset($getAgencyDetails->agency_name) ? $getAgencyDetails->agency_name : "",
					'rep' => $request->input('emc_ems_pid'),
					'notes_rep' => "",
					'medical_report' => $request->input('medical_report'),
					'progress_notes' => $request->input('progress_notes_id'),
					'date' => "",
					'fax_date' => "",
					'record_id' => $request->input('emc_record_id'),
					'emc_user_id' => $request->input('emc_ems_pid'),


				);

				DoctorPaperWorkService::save($data);
			}
			return 1;
		} else {
			return 0;
		}
	}

	public function statusChange(Request $request, $id)
	{
		$status = $request->input('status');
		$insert = $this->assignEmcRecord->update(array('status' => $status), array('id' => $id));
		if ($insert) {
			return 1;
		} else {
			return 0;
		}
	}
	public function AddNotes(Request $request, $id)
	{

		$insert = $this->assignEmcNotesRecord->save(array('notes' => $request->input('msg-box'), 'assign_id' => $id));
		return $insert;
	}

	public function getNotes($id)
	{
		$query = $this->assignEmcNotesRecord->getList($id);
		if (count($query) > 0) {
			foreach ($query as $ld) {
				$getUserDetails = User::select('first_name')->where('id', $ld->created_by)->where('delete_flag', 'N')->first();
				$ld->first_name = isset($getUserDetails->first_name) ? $getUserDetails->first_name : "";
			}
		}

		echo json_encode($query);
	}
}
