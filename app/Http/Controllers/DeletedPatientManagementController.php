<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Model\Patient;
use App\Services\PatientV2Service;
use App\Services\LogsService;
use App\Helpers\Utility;
use App\Agency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DeletedPatientManagementController extends BaseController
{
    protected $patientV2Service;
    public function __construct(PatientV2Service $patientV2Service){
        $this->middleware('auth');
        $this->middleware('permission:reactivate-patient-list', ['only' => ['index','ajaxList']]);
        $this->middleware('permission:reactivate-patient', ['only' => ['reactivatePatient']]);
        $this->patientV2Service = $patientV2Service;
    }

    public function index(Request $request){
        $angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$data['agencyList'] = $angecyList;
        return view("deletedPatientManagement/deleted_patient_list",$data);
    }

    public function ajaxList(Request $request){
        $data['query'] = $this->patientV2Service->getDeletedPatientData($request->all());
        return view("deletedPatientManagement/deleted_patient_ajax_list", $data);
    }

    public function reactivatePatient(Request $request){
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patient_master,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error_msg' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $patient = Patient::find($request->patient_id);
            $oldResponse = $patient;
            if (!$patient) {
                return response()->json([
                    'status' => false,
                    'error_msg' => 'Patient not found',
                ], 404);
            }

            if ($patient->deleted_flag != 'Y') {
                return response()->json([
                    'status' => false,
                    'error_msg' => 'Patient is not deleted',
                ], 400);
            }

            // Reactivate patient
            $patient->deleted_flag = 'N';
            $patient->deleted_date = NULL;
            $patient->deleted_by = NULL;
            $patient->updated_date = date('Y-m-d H:i:s');
            $patient->updated_by = auth()->user()->id;
            $patient->save();

            $ipaddress = Utility::getIP();
                $insertLog = [
					'type' => 'Reactivated patient',
					'link' => url('/deleted-patient-reactivate'),
					'module' => 'Patient Appointment',
					'object_id' => $request->patient_id,
					'message' => auth()->user()->first_name.' '.auth()->user()->last_name.' has reactivated patient',
                    'ip' => $ipaddress,
					'new_response' => serialize($patient),
					'old_response' => serialize($oldResponse),
				];
				LogsService::save($insertLog);

            return response()->json([
                'status' => true,
                'error_msg' => 'Patient reactivated successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error_msg' => 'Error reactivating patient: ' . $e->getMessage(),
            ], 500);
        }
    }
}
