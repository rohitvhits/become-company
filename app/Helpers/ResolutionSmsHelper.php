<?php

namespace App\Helpers;

use App\Model\Patient;
use App\Model\ResolutionSmsTemplate;

class ResolutionSmsHelper
{
    public static function statusWiseSmsSend($status, $patientId)
    {
        if (stripos($status, 'Unable To Contact') !== false) {
            $status = "Unable To Contact";
        }
        $patientName = "";
        $date = "";
        $mobile = "";
        $phone = "";

        if ($patientId != "") {
            $PatientDetails = Patient::with('agencyDetail')->select('id','agency_id','first_name', 'last_name', 'phone', 'telehealth_date_time', 'mobile')
                ->where('id', $patientId)
                ->where('deleted_flag', 'N')
                ->first();
            if (empty($PatientDetails->agencyDetail) || $PatientDetails->agencyDetail->is_telehealth_send_sms != 1) {
                return false;
            }

            if (!empty($PatientDetails)) {
                $patientName = $PatientDetails->first_name . ' ' . $PatientDetails->last_name;
                if (!empty($PatientDetails->telehealth_date_time)) {
                    $date = date('m/d/Y', strtotime($PatientDetails->telehealth_date_time));
                }
                $mobile = $PatientDetails->mobile;
                $phone  = $PatientDetails->phone;
            }
        }

        $template = ResolutionSmsTemplate::where('status', trim($status))->where('del_flag', 'N')->first();
        $msg = "";
        if ($template) {
            $msg = $template->message;
            $msg = str_replace('{patient_name}', $patientName, $msg);
            $msg = str_replace('{appointment_date}', $date, $msg);
        }

        return [
            'status'  => $status,
            'message' => $msg,
            'mobile'  => $mobile,
            'phone'   => $phone,
        ];
    }

    public static function getMDOServiceIds($service_ids){
        if(!((in_array('181', $service_ids) || in_array('1167', $service_ids)))){
            return true;
        }
        return false;
    }
}
