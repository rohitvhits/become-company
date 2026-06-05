<?php

namespace App\Services;

use App\DocumentSignerMaster;
use App\Model\AgencyForm;
use App\Model\AgencyMaster;
use App\Template;

class AgencyAllFormService
{

	public function getTemplate($templateId, $formId)
	{
		$template = Template::where('id', $templateId)
			->where('custom_form_id', $formId)
			->where('del_flag', 'N')
			->first();

		return $template;
	}

	public function existAgencyForm($formId,$patient_id)
	{
		$agencyForm = AgencyForm::where('form_id', $formId)->where('patient_id', $patient_id)->first();

		return $agencyForm;
	}

	public  function storeAgencyForm($request)
	{
		$agencyForm  = AgencyForm::create([
			'form_id' => $request->f_id,
			'doctor_id' => $request->d_id,
			'agency_id' => $request->agency_id,
			'patient_id' => $request->patient_id,
			'created_by' => Auth()->user()->id,
		]);

		$agencyForm->load(['agencies:id,agency_name', 'forms:id,title,is_default,agency,form_type,sort_id', 'agencyMaster.fields', 'doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by','templateById:id,template_name,custom_form_id,del_flag,agency_id']);

		return $agencyForm;
	}

	public function getAgencyFormWithDoctors($agencyFormId)
    {
        return AgencyForm::with('doctors:id,full_name,email,gender,phone,remarks,license,address,city,state,zipcode,place_of_examination,date_of_examination,signature_upload,stamp_upload,specialty,registry_number,npi_number,is_signature_stamp_active')->where('id', $agencyFormId)->first();
    }

	public function getAgencyMasterData($customFormId)
    {
        return AgencyMaster::with('fields')->where('form_id', $customFormId)->get();
    }
}
