<?php

namespace App\Services;

use App\Agency;
use App\Model\AgencyMaster;
use App\Model\FormSetup;
use App\Template;

class FormSetupService
{

	public  function getFormSetup()
	{
		$formSetup = FormSetup::with('agencyValue:id,agency_name', 'getTemplateById:id,custom_form_id,template_name')->orderBy('id', 'asc')->paginate(50);
		return $formSetup;
	}

	public  function getAgency()
	{
		$agency = Agency::get();
		return $agency;
	}

	public  function getTemplate()
	{
		$template = Template::where('del_flag', 'N')->select(['id', 'template_name','custom_form_id','del_flag','agency_id'])->get();
		return $template;
	}

	public function updateTemplateId($formId, $templateId)
	{
		$template = Template::where('id', $templateId)->first();

		if ($template) {
			$existingTemplate = Template::where('custom_form_id', $formId)->first();

			if ($existingTemplate) {
				$existingTemplate->custom_form_id = null;
				$existingTemplate->save();
			}
			$template->custom_form_id = $formId;
			$template->save();

			return $template;
		} else {
			return false;
		}
	}

	public function getTemplateByFormId($templateId, $formId)
	{
		return Template::select(['id', 'template_name','custom_form_id','del_flag','agency_id'])
			->where('id', $templateId)->with('getFormName:id,title')
			->where('custom_form_id', '!=', '')
			->whereNotNull('custom_form_id')
			->where('custom_form_id','!=',$formId)
			->first();
	}

	public  function storeFieldMaster($request)
	{
		$fieldMaster  = FormSetup::updateOrCreate(['id' => $request->id ?? null], [
			'title' => $request->title,
			'is_default' => $request->is_default,
			'agency' => $request->agency,
			'form_type' => $request->form_type,

		]);
	
		if ($request->is_default == 0) {
			AgencyMaster::where('form_id', $fieldMaster->id)->update(['agency_id' => $request->agency]);
		}else{
			AgencyMaster::where('form_id', $fieldMaster->id)->update(['agency_id' => null]);
		}
	
		$fieldMaster->load('agencyValue:id,agency_name');

		return $fieldMaster;
	}

	public function getFieldById($id)
	{
		$data = FormSetup::find($id);

		return $data->load('agencyValue:id,agency_name');
	}

	public function deleteField($id)
	{
		return FormSetup::where('id', $id)->delete();
	}

	public function totalRecord()
	{
		return FormSetup::count();
	}

	public function updateTemplateCustomFormId($id)
	{
		return Template::where('custom_form_id', $id)
			->update(['custom_form_id' => NULL]);
	}
}
