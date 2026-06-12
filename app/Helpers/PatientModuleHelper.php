<?php

namespace App\Helpers;

class PatientModuleHelper
{

   public static function createColumnWiseFields(){
        return [
            'Portal Id'=>'id',
            'Agency Name'=>'agency_id',
            'Type'=>'type',
            'Discipline'=>'diciplin',
            'Patient Code'=>'patient_code',
            'Full Name'=>'full_name',
            'Phone'=>'mobile',
            'Gender'=>'gender',
            'Dob'=>'dob',
            'Location'=>'location_id',
            'Appointment Date'=>'appointment_date',
            'Appointment Start Time'=>'appoinment_time_id',
            'Service'=>'service_id',
            'Status'=>'status',
            'Notes'=>'remarks',
            'Booked Via'=>'appointment_mode',
            'Assign NyBest User'=>'assign_user_id',
            'Created Date'=>'created_date',
            'Created By'=>'created_by',
            'Due Date'=>'due_date',
            'FU Date'=>'fu_date',
            'Is Archive'=>'is_archive',
            'Completed date'=>'completed_date',
            'Follow Up Date'=>'follow_date',
            'Location / Branch'=>'location_branch',
            'Reason'=>'reason',
            'state'=>'state',
            'Training Date'=>'traning_due_date',
            'Training Status'=>'training_status',
            'Last Status Updated'=>'last_status_update',
            'Last Status Updated By'=>'last_status_update_by',
            'Referral Type'=>'referral_type',
            'Agency Rep'=>'agency_user_id',
            'Language'=>'language',
            'Clinician Code'=>'telehealth_nurse'
        ];
   }
}
