<?php

namespace App\Helpers;

use App\Agency;
use App\Model\AgencyForm;
use App\Model\Doctor;
use App\Model\Patient;
use App\Model\PatientCustomData;
use Illuminate\Support\Facades\DB;

class AgencyAllForm
{
	public function __construct() {}

	public static function GetFormDetails($formId="", $key, $id,$pkid="")
	{
	
		$query = PatientCustomData::with('fields')->where('field_id', $key)
			->where('form_id', $formId)
			->where('patient_id', $id)
			->where('agency_form_id',$pkid)
			->first();
		if (!$query) {
			return "";
		}

		$fieldType = $query->fields->type??"";

		if ($fieldType === 'date') {
			$dateValue = $query->value;
			return date('m/d/Y', strtotime($dateValue));
		}

		return $query->value;
	}

	public static function GetDoctorDetails($formId="", $key, $id,$agency_form_id="",$doctor_id="")
	{
		if($doctor_id == ''){
			$query = AgencyForm::with('doctors:id,full_name,email,gender,phone,license,address,city,state,zipcode,place_of_examination,date_of_examination,signature_upload,stamp_upload,specialty,registry_number,npi_number')->where('id',$agency_form_id)->where('form_id', $formId)->where('patient_id', $id)->first();

			return isset($query->doctors->$key) ? $query->doctors->$key : "";
		}else{
			$query = Doctor::where('id', $doctor_id)->first();
			return isset($query->$key) ? $query->$key : "";
		}
	

	}


	public static function GetAgencyDetails($key, $agencyId)
	{
		if ($key == 'full_address') {
			$keys = DB::raw('CONCAT_WS("", address1, " ", address2, " ", city, " ", state, " ", zip_code) as full_address');
		} else {
			$keys = $key;
		}
		$query = Agency::selectRaw($keys)
			->where('id', $agencyId)
			->first();

		return isset($query->$key) ? $query->$key : "";
	}
}
