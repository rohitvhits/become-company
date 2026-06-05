<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;
use App\Master;
use App\Helpers\Utility;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Agency;
use App\Model\HubPatientServiceRequest;
use App\Model\HubPatient;

class CreateAppointmentService
{
    protected $patientService = "";
    protected $patientServiceRequest = "";
    protected $patientWiseServiceRequest = "";
    public function __construct(PatientService $patientService, PatientServicesRequest $patientServiceRequest, PatientWiseServicesRequests $patientWiseServiceRequest)
    {
        $this->patientService = $patientService;
        $this->patientServiceRequest = $patientServiceRequest;
        $this->patientWiseServiceRequest = $patientWiseServiceRequest;
    }

    public function saveAppointment($data)
    {
        $validator = Validator::make($data, [
            'first_name' => 'required',
            'type' => 'required',
            'last_name' => 'required',
            //'mobile' => 'required|numeric|digits_between:10,15',
            'service_id' => 'required',
            //'dob' => 'required',
            //'gender' => 'required',
            'referral_type' => 'required',
        ]);
        if ($validator->fails()) {

            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $patientDetails = $this->patientService->checkForThirdPartyExistingDataHub($data, $data['agency_id']);

            $serviceIds =  explode(',', $data['service_id']);
            $serviceIdArray = [];
            if (!empty($serviceIds[0])) {
                foreach ($serviceIds as $st) {
                    $details = Master::where('id', $st)->where('master_type_fk', 11)->where(DB::raw('LOWER(types)'), strtolower($data['type']))->first();
                    if (isset($details->id) && $details->id != "") {
                        $serviceIdArray[] = $st;
                    }
                }
            }
            $fuDate = NULL;
            if (isset($data['fu_date']) && $data['fu_date'] != '') {
                $fuDate = Utility::convertMdyToYmdUsingCarbon($data['fu_date']);
            }
            $dueDate = NULL;
            if (isset($data['due_date']) && $data['due_date'] != '') {
                $dueDate = Utility::convertMdyToYmdUsingCarbon($data['due_date']);
            }
            if (isset($patientDetails->id)) {
                $patientId = $patientDetails->id;
            } else {
                $age = NULL;
                if (isset($data['dob']) && $data['dob'] != '') {
                    $age = $data['dob'];
                }


                if (count($serviceIdArray) == 0) {
                    return response()->json(['error_msg' => "Sorry, we couldn’t locate the service you requested.", 'status' => 0, 'data' => array()], 422);
                }

                $ssn = "";
                if (isset($data['ssn'])) {
                    $ssn = str_replace('-', '', $data['ssn']);
                }

                $dataArray = array(
                    'first_name' => $data['first_name'],
                    'middle_name' =>  $data['middle_name'] ?? "",
                    'last_name' =>  $data['last_name'] ?? "",
                    'email' => $data['email'] ?? "",
                    'full_name' => $data['first_name'] . ' ' . $data['last_name'],
                    'type' =>  $data['type'] ?? "",
                    'dob' => $age,
                    'fu_date' => $fuDate,
                    'due_date' => $dueDate,
                    'phone' => $data['phone'] ?? "",
                    'mobile' => $data['mobile'] ?? "",
                    'agency_id' => $data['agency_id'],
                    'gender' => $data['gender'] ?? "",
                    'remarks' => $data['message'] ?? "",
                    'service_id' => implode(',', $serviceIdArray),
                    'patient_code' => $data['patient_code'] ?? "",
                    'diciplin' => $data['diciplin'] ?? "",
                    'language' => $data['language'] ?? "",
                    'address1' => $data['address1'] ?? "",
                    'address2' => $data['address2'] ?? "",
                    'state' => $data['state'] ?? "",
                    'city' => $data['city'] ?? "",
                    'zip_code' => $data['zipcode'] ?? "",
                    'county' => $data['country'] ?? "",
                    'payment_type' => $data['payment_type'] ?? "",
                    'platform_type' => $data['platform_type'] ?? "",
                    'platform_id' => $data['platform_id'] ?? "",
                    'created_date' => date('Y-m-d H:i:s'),
                    'partner_agency' => $data['partner_agency'] ?? "",
                    'agency_token_id' => $data['token_id'] ?? "",
                    'third_party_priority' => $data['priority'] ?? "",
                    'cin' => $data['cin'] ?? "",
                    'ssn' => $ssn,
                    'emergency_contact_name' => $data['emergency_contact_name'] ?? "",
                    'emergency_phone' => $data['emergency_phone'] ?? "",
                    'insurance_id' => isset($data['insurance_id']) ? $data['insurance_id'] : "",
                    'insurance_name' => isset($data['insurance_name']) ? $data['insurance_name'] : "",
                    'created_by' => $data['created_by'],
                    'created_date' => date('Y-m-d H:i:s'),
                    'hub_id' => $data['hub_id'] ?? "",
                    'company_id' => $data['company_id'] ?? ""
                );

                if (isset($data['insurance_name'])) {
                    if ($data['insurance_name'] == 'other') {
                        $dataArray['other_insurance_name'] = $data['other_insurance_name'] ?? "";
                    }
                }

                if (isset($data['gender']) && $data['gender'] == 'other') {
                    $dataArray['other_gender'] = $data['other_gender'] ?? "";
                }

                if (isset($data['service_start_date']) && $data['service_start_date'] != '') {
                    $dataArray['service_start_date'] = date('Y-m-d', strtotime($data['service_start_date']));
                }

                if (isset($data['third_party_callback_url'])) {
                    $dataArray['third_party_callback_url'] = $data['third_party_callback_url'] ?? "";
                }

                $savePatient = new HubPatient($dataArray);
                $savePatient->save();
                $patientId = $savePatient->id;
            }

            if ($patientId) {
                $data['serviceIdArray'] = $serviceIdArray;
                $data['fuDate'] = $fuDate;
                $data['dueDate'] = $dueDate;
                $this->savePatientWiseServiceRequested($patientId, $patientDetails, $data);
            }
            return response()->json(['error_msg' => "", 'status' => 1, 'data' => array('patient_id' => $patientId,'hub_id'=>$data['hub_id'])], 200);
        }
    }

    public function savePatientWiseServiceRequested($patientId, $patientDetails, $data)
    {

        $patientServiceCount = $this->patientServiceRequest->getServiceCountHubPatientId($patientId);

        if (count($patientServiceCount) == 0) {
            $services = explode(',', $patientDetails->service_id ?? '');
            if (!empty($services[0])) {
                $patientServiceLastId = $this->patientServiceRequest->hubSave([
                    'patient_id' => $patientDetails->id,
                    'follow_up_date' => $patientDetails->fu_date,
                    'due_date' => $patientDetails->due_date,
                    'status' => $patientDetails->status,
                    'created_at' => $patientDetails->created_date,
                    'created_by' => $data['created_by'],
                    'completed_date' => $patientDetails->completed_date,
                    'completed_by' => $patientDetails->completed_by,
                    'flag' => $data['flag'] ?? 0,
                    'from_api' => $data['from_api'] ?? 0,
                    'remarks' => $data['remarks'] ?? "",
                    'booking_date' => $data['booking_date'] ?? null,
                ]);
                foreach ($services as $serviceId) {
                    $patientWiseServiceRequest = [
                        'patient_id' => $patientDetails->id,
                        'service_id' => $serviceId,
                        'patient_service_request_id' => $patientServiceLastId,
                        'created_date' => $patientDetails->created_date,
                        'created_by' => $data['created_by'],
                    ];
                    $saveServices = $this->patientWiseServiceRequest->hubSave($patientWiseServiceRequest);
                }
            }
        }

        $patientServiceLastId = $this->patientServiceRequest->hubSave([
            'patient_id' => $patientId,
            'from_api' => $data['from_api'] ?? 0,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $data['created_by'],
            'follow_up_date' => $data['fuDate'],
            'due_date' => $data['dueDate'],
            'remarks' => $data['remarks'] ?? "",
            'booking_date' => $data['booking_date'] ?? null,
        ]);


        foreach ($data['serviceIdArray'] as $serviceId) {
            $patientWiseServiceRequest = [
                'patient_id' => $patientId,
                'service_id' => $serviceId,
                'patient_service_request_id' => $patientServiceLastId,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $data['created_by'],
            ];

            $saveServices = $this->patientWiseServiceRequest->hubSave($patientWiseServiceRequest);
        }
    }

    public function getAllAgencyList()
    {
        $agencyList = Agency::getAgencyList();
        return response()->json(['error_msg' => "", 'status' => 1, 'data' => $agencyList], 200);
    }

    public function fetchAppointmentsByHubAndCompanyId($hubId, $companyId)
    {
        return HubPatientServiceRequest::select(
            'hub_patient_service_requests.id',
            'hub_patient_master.id as pid',
            'hub_patient_master.full_name as f_name',
            'hub_patient_master.dob',
            'hub_patient_master.gender',
            'hub_patient_master.mobile',
            'hub_patient_master.phone',
            'hub_patient_master.language',
            'hub_patient_master.type',
            'hub_patient_master.patient_code',
            'hub_patient_master.created_date',
            'hub_patient_master.agency_id',
            'agency.agency_name',
            'hub_patient_service_requests.created_by',
            'hub_patient_service_requests.booking_date',
        )->leftJoin('hub_patient_master', function ($join) {
            $join->on('hub_patient_service_requests.patient_id', '=', 'hub_patient_master.id');
        })
            ->with('userDetails', 'patientServiceRequestRelationShip.requestService')

            ->orderBy('hub_patient_master.id', 'DESC')
            ->leftJoin('agency', function ($join) {
                $join->on('agency.id', '=', 'hub_patient_master.agency_id');
            })->whereHas('patientServiceRequestRelationShip', function ($q) {
                $q->where('service_id', '!=', '')->where('del_flag', 'N');
            })
            ->where('deleted_flag', 'N')
            ->when($hubId, function ($query, $hubId) {
                return $query->where('hub_id', $hubId);
            })
            ->when($companyId, function ($query, $companyId) {
                return $query->where('company_id', $companyId);
            })->orderBy('hub_patient_service_requests.id', 'DESC')
            ->paginate(50);
    }
}
