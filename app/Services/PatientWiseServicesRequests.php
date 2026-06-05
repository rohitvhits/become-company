<?php

namespace App\Services;

use App\Model\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Model\PatientWiseServiceRequest;
use App\Model\PatientWiseServiceEmail;

use App\Model\HubPatientWiseServiceRequest;
use App\Agency;
use Illuminate\Support\Facades\URL;
use App\Helpers\Utility;

class PatientWiseServicesRequests
{

	public  function save($data)
	{
		$auth = auth()->user();
		if (isset($data['flag']) && $data['flag'] == 1) {
			$data['created_date'] = $data['created_date'];
			$data['created_by'] = $data['created_by'];
		} else {
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $auth['id'];
		}
		// $data['created_date'] = date('Y-m-d H:i:s');
		// $data['created_by'] = $auth['id'];
		$data['del_flag'] = 'N';

		$insert = new PatientWiseServiceRequest($data);
		$insert->save();
		$insertId = $insert->id;

		return $insertId;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		if (isset($auth['id'])) {
			$data['updated_by'] = $auth['id'];
		}

		$update = PatientWiseServiceRequest::where($where)->update($data);

		return $update;
	}

	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = PatientWiseServiceRequest::where($where)->update($data);
		return $update;
	}

	public function patientWiseServices($patientId)
	{
		return PatientWiseServiceRequest::with('services')
			->where('patient_id', $patientId)
			->whereIn('status', ['Pending', 'Completed'])
			->get()
			->map(function ($request) {
				// Map service_id to service name
				$request->services = $request->services->mapWithKeys(function ($service) {
					return [$service->id => $service->name];
				});
				return $request;
			});
	}

	public function patientWiseServiceEmailSave($request)
	{
		$unitId = uniqid();
		$patientWiseServiceEmail = [
			'patient_id' => $request->patient_id,
			'email' => $request->email,
			'patient_wise_service_id' => $request->patient_wise_service_id,
			'del_flag' => 'N',
			'created_date' => date('Y-m-d H:i:s'),
			'created_by' => auth()->user()->id,
			'uniqid' => $unitId
		];

		$savedRecord = PatientWiseServiceEmail::create($patientWiseServiceEmail);

		/****get patient details */
		$getPatientDetails = Patient::find($request->patient_id);
		$getAgencyName = Agency::find($request->agency_id);

		$emails = isset($getAgencyName->notification_email) ? $getAgencyName->notification_email : "";
		$allemails = array();

		$username = auth()->user()->first_name . ' ' . auth()->user()->last_name;

		$agencyname = @$getAgencyName->name;
		$type = $getPatientDetails->type;
		$first_name = auth()->user()->first_name;
		$last_name = auth()->user()->last_name;
		$phone = auth()->user()->phone;
		$insert = $savedRecord->id;
		$messages = 'Hello NY Best Medical,<br>';
		$messages .= 'Below new record is added <br>';
		$messages .= 'Added By :' . $username . ' <br>';
		$messages .= 'Agency Name :' . $agencyname . ' <br>';
		$messages .= 'Details: <br>';
		$messages .= 'Portal Id: ' . $insert . '<br>';

		if ($type == 'Caregiver') {
			$messages .= 'Caregiver Name: ' . $first_name . ' ' . $last_name . '<br>';
		} else {
			$messages .= 'Patient Name: ' . $first_name . ' ' . $last_name . '<br>';
		}

		// dd($unitId);

		$url = URL::to('/') . '/ap-new/' . $unitId;
		dd($url);

		$messages .= "Dob: " . date('m/d/Y', strtotime($request->input('dob'))) . '<br>';
		$messages .= "Mobile No: " . $request->input('mobile') . '<br>';
		$messages .= "Phone No: " . $phone . '<br>';

		$messages .= 'Thank you!';

		$subject = "[" . $agencyname . "] NYBest Medical Care New record added";

		$email = $getPatientDetails->email;

		$emailData = array(
			'username' => $username,
			'agencyname' => $agencyname,
			'insert' => $insert,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'dob' => date('m/d/Y', strtotime($request->input('dob'))),
			'mobile' => $request->input('mobile'),
			'type' => $type,
		);
		$messages = Utility::getHtmlContent('email_content.create_new_record_service_mail', $emailData);

		try {
			$mail = Mail::mailers('second')->send([], [], function ($message) use ($email, $subject, $messages, $username) {
				$message->to($email, "Ny Best Medicals")->cc(auth()->user()->email)
					->subject($subject)->html($messages);
			});
		} catch (\Throwable $th) {
		}

		return $savedRecord;
	}

	public function savePatientTypeWiseServices($request)
	{
		dd($request->all());
		$addServiceIds = $request->input('service_id');

		$patientServiceLastId = $this->patientServicesRequest->save([
			'patient_id' => $request->input('patient_id')
		]);

		if (is_array($addServiceIds)) {
			foreach ($addServiceIds as $serviceId) {
				$patientWiseServiceRequest = [
					'patient_id' => $insert,
					'service_id' => $serviceId,
					'patient_service_request_id' => $patientServiceLastId,
				];

				$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
			}
		}
	}

	public function getPatientServices($request)
	{
		return PatientWiseServiceRequest::with(['services:id,name'])->where('patient_service_request_id', $request->id)->get();
	}

	public function getTotalAppointmentCountForService($agency_id, $location_id, $type, $from_date, $to_date)
	{
		$query = PatientWiseServiceRequest::with('patient')->where('del_flag', 'N');
		$query->whereHas('patient', function ($q) use ($type, $location_id, $agency_id) {
			if (!empty($type)) {
				$q->where('type', $type);
			}
			$q->whereNull('archived_at');
			if (!empty($location_id)) {
				$q->where('location_id', $location_id);
			}

			if (!empty($agency_id)) {
				$explode = explode(',', $agency_id);
				$q->whereIn('agency_id', $explode);
			}
		});
		$query->whereHas('patient', function ($q) {
			$q->whereNull('archived_at');
			$q->whereNotNull('id');
		});
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		return $query = $query->pluck('service_id');
	}

	public function getCaregiverCount($agencyIds, $type, $locationIds, $from_date, $to_date)
	{
		$query = PatientWiseServiceRequest::with('patient:id,type,location_id')->where('del_flag', 'N');
		$query->whereHas('patient', function ($q) use ($type, $locationIds, $agencyIds) {
			if ($type != '') {
				$q->where('type', $type);
			}
			$q->whereNull('archived_at');
			if ($locationIds != "") {
				$q->whereIn('location_id', $locationIds);
			}
			if ($agencyIds != "") {
				$q->whereIn('agency_id', $agencyIds);
			}
		});
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		return $query = $query->get();
	}

	public function patientWiseServicesData($patientId)
	{
		return PatientWiseServiceRequest::where('patient_id', $patientId)->where('del_flag', 'N')->get()->pluck('id');
	}

	public function getExistingPatientDataNew($patientId)
	{
		return PatientWiseServiceRequest::select('id', 'patient_id')->where('del_flag', 'N')->where('patient_id', $patientId)->get();
	}

	public function getExistingPatientServices($patientId)
	{
		return PatientWiseServiceRequest::select('id', 'patient_id', 'service_id')->where('del_flag', 'N')->where('patient_id', $patientId)->get()->pluck('service_id')->toArray();
	}

	public function getAgencytWiseServiceRequest($created_date = "", $agencyId = "", $type = "", $lastUpdatedDate = "")
	{

		$query = PatientWiseServiceRequest::join('patient_master as pm', 'patient_wise_service_requested.patient_id', '=', 'pm.id')
			->join('patient_service_requests as psr', 'patient_wise_service_requested.patient_service_request_id', '=', 'psr.id')
			->select('pm.agency_id', 'patient_wise_service_requested.service_id', DB::raw('COUNT(patient_wise_service_requested.id) as total_count'))
			->where('patient_wise_service_requested.del_flag', 'N')
			// ->where('psr.status', 'Completed')
			->when($created_date, function ($query) use ($created_date) {
				if ($created_date != '') {
					$date = explode('-', $created_date);
					if (count($date) > 0) {
						$from_date = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
						$to_date = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
					}
				}
				$query->whereBetween('psr.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			})
			->when($lastUpdatedDate, function ($query) use ($lastUpdatedDate) {
				if ($lastUpdatedDate != '') {
					$date = explode('-', $lastUpdatedDate);
					if (count($date) > 0) {
						$from_date = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
						$to_date = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
					}
				}
				$query->whereBetween('psr.last_status_update', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			});
		if ($agencyId != "") {
			$query->whereIn('pm.agency_id', $agencyId);
		} else {
			$agencyids = Utility::getUserWiseAgency();
			if (Auth()->user()->agency_fk != "") {
				$agencyids[] = Auth()->user()->agency_fk;
			}
			if (!empty($agencyids)) {
				$query->whereIn('pm.agency_id', $agencyids);
			}
		}
		$query = $query->when($type, function ($query) use ($type) {
			$query->where('pm.type', $type);
		})
			->whereHas('patient.agencyDetail', function ($q) {
				$q->where('agency.delete_flag', 'N');
			})
			->where('pm.deleted_flag', 'N')->where('patient_wise_service_requested.del_flag', 'N')
			->groupBy('pm.agency_id', 'patient_wise_service_requested.service_id')
			->orderBy('total_count', 'asc')
			->get();
		return $query;
	}

	public function getWeeklyMonthlyServices($created_date = "", $agencyId = "", $type = "", $lastUpdatedDate = "")
	{

		return	PatientWiseServiceRequest::join('master_table as m', 'patient_wise_service_requested.service_id', '=', 'm.id')->join('patient_master as pm', 'patient_wise_service_requested.patient_id', '=', 'pm.id')
			->join('patient_service_requests as psr', 'patient_wise_service_requested.patient_service_request_id', '=', 'psr.id')
			->where('patient_wise_service_requested.del_flag', 'N')
			->where('m.master_type_fk', 11)
			->select([
				DB::raw("DATE_FORMAT(DATE_SUB(psr.created_at, INTERVAL (WEEKDAY(psr.created_at)) DAY), '%m/%d/%Y') as week_start_date"),
				'm.name as service_name',
				DB::raw('COUNT(*) as count')
			])
			->when($created_date, function ($query) use ($created_date) {
				if ($created_date != '') {
					$date = explode('-', $created_date);
					if (count($date) > 0) {
						$from_date = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
						$to_date = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
					}
				}
				$query->whereBetween('psr.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			})
			->when($lastUpdatedDate, function ($query) use ($lastUpdatedDate) {
				if ($lastUpdatedDate != '') {
					$date = explode('-', $lastUpdatedDate);
					if (count($date) > 0) {
						$from_date = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
						$to_date = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
					}
				}
				$query->whereBetween('patient_service_requests.last_updated_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			})
			->when($agencyId, function ($query) use ($agencyId) {
				$query->whereIn('pm.agency_id', $agencyId);
			})
			->when($type, function ($query) use ($type) {
				$query->where('pm.type', $type);
			})
			->whereHas('patient.agencyDetail', function ($q) {
				$q->where('agency.delete_flag', 'N');
			})
			->groupBy('week_start_date', 'service_name')
			->orderBy('week_start_date')
			->get();
	}

	public function getServicesCount($created_date = "", $type = "", $lastUpdatedDate = "", $agency_fk = "", $agency_filter_type = "", $service_id = "", $service_filter_type = "", $assigned_to = "", $medication_list = "", $insurance_elg = "", $mdo_tag = "", $branch_id = "", $branch_filter_type = "")
	{
		$patientFilterSubquery = null;

		if (!empty($medication_list) || !empty($insurance_elg) || !empty($mdo_tag)) {
			$patientFilterSubquery = DB::table('patient_master')
				->select('id')
				->where('deleted_flag', 'N')
				->whereNull('archived_at')
				->when(!empty($medication_list), function ($query) use ($medication_list) {
					if ($medication_list == 'Yes') {
						$query->where('medication_count', '>=', 1);
					} else {
						$query->where('medication_count', '=', 0);
					}
				})
				->when(!empty($insurance_elg), function ($query) use ($insurance_elg) {
					if ($insurance_elg == 'Yes') {
						$query->where('insurance_elg_count', '>=', 1);
					} else {
						$query->where('insurance_elg_count', '=', 0);
					}
				})
				->when(!empty($mdo_tag), function ($query) use ($mdo_tag) {
					if ($mdo_tag == 'Yes') {
						$query->where('mdo_tag_count', '>=', 1);
					} else {
						$query->where('mdo_tag_count', '=', 0);
					}
				});
		}

		return	PatientWiseServiceRequest::leftjoin('master_table as m', 'patient_wise_service_requested.service_id', '=', 'm.id')->leftjoin('patient_master as pm', 'patient_wise_service_requested.patient_id', '=', 'pm.id')->leftjoin('patient_service_requests as ps', 'patient_wise_service_requested.patient_service_request_id', '=', 'ps.id')->where('ps.del_flag', 'N')
			->where('patient_wise_service_requested.del_flag', 'N')
			->where('m.master_type_fk', 11)
			->where('pm.deleted_flag', 'N')
			->where('pm.archived_at', null)
			->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
				$query->whereIn('patient_wise_service_requested.patient_id', $patientFilterSubquery);
			})
			->when($branch_id && !empty($branch_id), function ($query) use ($branch_id, $branch_filter_type) {
				if (isset($branch_filter_type) && $branch_filter_type == 'include') {
					$query->whereIn('pm.branch_id', $branch_id);
				} elseif (isset($branch_filter_type) && $branch_filter_type == 'exclude') {
					$query->where(function ($sub) use ($branch_id) {
						$sub->whereNotIn('pm.branch_id', $branch_id)
							->orWhere('pm.branch_id', 0)->orWhere('pm.branch_id', null);
					});
				}
			})
			->select([
				DB::raw("DATE_FORMAT(patient_wise_service_requested.created_date, '%m/%d/%Y') as service_date"),
				'm.name as service_name',
				DB::raw('COUNT(*) as count')
			])
			->when($created_date, function ($query) use ($created_date) {
				if ($created_date != '') {
					$date = explode('-', $created_date);
					if (count($date) > 0) {
						$from_date = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
						$to_date = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
					}
				}
				$query->whereBetween('ps.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			})
			->when($lastUpdatedDate, function ($query) use ($lastUpdatedDate) {
				$explode = explode('-', $lastUpdatedDate);
				$query->whereDate('ps.last_status_update', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('ps.last_status_update', '<=', date('Y-m-d', strtotime($explode[1])));
			})

			->when($type, function ($query) use ($type) {
				$query->where('pm.type', $type);
			})
			->when($agency_fk && !empty($agency_fk), function ($query) use ($agency_fk, $agency_filter_type) {
				if (isset($agency_filter_type) && $agency_filter_type == 'include') {
					$query->whereIn('pm.agency_id', $agency_fk);
				} elseif (isset($agency_filter_type) && $agency_filter_type == 'exclude') {
					$query->whereNotIn('pm.agency_id', $agency_fk);
				}
			})
			->when($service_id && !empty($service_id), function ($query) use ($service_id, $service_filter_type) {
				if (isset($service_filter_type) && $service_filter_type == 'include') {
					$query->whereIn('patient_wise_service_requested.service_id', $service_id);
				} elseif (isset($service_filter_type) && $service_filter_type == 'exclude') {
					$query->whereNotIn('patient_wise_service_requested.service_id', $service_id);
				}
			})
			->when($assigned_to && !empty($assigned_to), function ($query) use ($assigned_to) {
				$query->whereIn('pm.assign_user_id', $assigned_to);
			})
			->whereHas('patient.agencyDetail', function ($q) {
				$q->where('agency.delete_flag', 'N');
			})
			->groupBy('patient_wise_service_requested.service_id')
			->orderBy('count', 'desc')
			->get();
	}

	public  function hubSave($data)
	{
		$auth = auth()->user();
		if (isset($data['flag']) && $data['flag'] == 1) {
			$data['created_date'] = $data['created_date'];
			$data['created_by'] = $data['created_by'];
		} else {
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $auth['id'];
		}
		$data['del_flag'] = 'N';

		$insert = new HubPatientWiseServiceRequest($data);
		$insert->save();
		$insertId = $insert->id;

		return $insertId;
	}
}
