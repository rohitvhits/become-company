<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\RobortHelper;
use App\Helpers\HHAPatientHelper;
use App\Agency;
use App\Model\Robort;

class RobortCronjobController extends Controller
{
    public  $paging;

    public function __construct()
    {
    }

    public function syncRemoteAgencyPatient()
    {
        // $this->syncRemoteSchedule();
        // die();

        $getAgency = Agency::where('delete_flag', 'N')->where('robort_status', '1')->whereNotNull('robort_grant_type')->whereNotNull('robort_user_name')->whereNotNull('robort_user_password')->get();

        if (!empty($getAgency[0])) {
            foreach ($getAgency as $agency) {
                $response = RobortHelper::getLogin($agency->robort_grant_type, $agency->robort_user_name, $agency->robort_user_password);
                if (isset($response['access_token'])) {
                    $this->getPatientList(1, $response['access_token'], $agency->id);
                }
            }
        }
    }

    public function getPatientList($page = 1, $token, $agencyId)
    {

        $getData = RobortHelper::getPatientList($page, $token);

        if (!empty($getData['items'])) {
            foreach ($getData['items'] as $key => $val) {

                $final = array(
                    'uuid' => $val['uuid'],
                    'patientId' => $val['patientId'],
                    'agency_id' => $agencyId,
                    'legacyId' => $val['legacyId'],
                    'firstName' => $val['firstName'],
                    'lastName' => $val['lastName'],
                    'dob' => $val['dob'],
                    'gender' => $val['gender'],
                    'status' => $val['enrolledProgramStatus'],
                    'created_date' => date('Y-m-d H:i:s'),
                    'externalId' => $val['externalId']
                );
                RobortHelper::insertData($final);
            }

            $page = $page + 1;

            $this->getPatientList($page, $token, $agencyId);
        }
    }

    public function syncRemoteSchedule()
    {
echo date('Y-m-d  H:i:s', strtotime('-1 day'));
        $getPatient = Robort::whereIn('status', [3, 4])->whereNotNull('externalId')->where(function ($query) {
            $query->where('last_schedule_sync_date', '<', date('Y-m-d  H:i:s', strtotime('-1 day', )))
                ->orWhereNull('last_schedule_sync_date');
        })->take(100)->get();

echo count($getPatient);
        foreach ($getPatient as $patient) {
            echo "id ".$patient->id . "<br/>";

           
            $this->getSchedule($patient->id);
          //  die();
        }
    }
    public function remoteRefreshEmployee(Request $request)
    {
        ini_set('max_execution_time', -1);
        $agency = Agency::where('id', $request->agency_id)->where('robort_status', '1')->first();
        $response = RobortHelper::getLogin($agency->robort_grant_type, $agency->robort_user_name, $agency->robort_user_password);

        if (isset($response['access_token'])) {
            $this->getPatientList(1, $response['access_token'], $agency->id);
            return response()->json(['error_msg' => 'Remote successfully sync',  'data' => array('')], 200);
        }
    }

    public function getSchedule($id)
    {
        $query = Robort::whereNotNull('externalId')->where('id', $id)->first();
       
        if (isset($query->id)) {
            $getDetails = HHAPatientHelper::getPatientIdByAdmissionId($query->externalId, $query->agency_id);

            $getAgencyDetails = Agency::where('id', $query->agency_id)->first();
            $response = RobortHelper::getLogin($getAgencyDetails->robort_grant_type, $getAgencyDetails->robort_user_name, $getAgencyDetails->robort_user_password);

            $saveResponse = RobortHelper::saveVisit($response['access_token'], $getDetails);
            //    Robort::whereNotNull('externalId')->where('id',$id)->first();
            $update = Robort::where('id', $id)->update(array('last_schedule_sync_date'=>date('Y-m-d H:i:s')));
          
        }


        return response()->json(['error_msg' => 'Remote successfully sync'], 200);
    }
}
