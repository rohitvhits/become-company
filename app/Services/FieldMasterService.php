<?php

namespace App\Services;

use App\Model\AgencyMaster;
use App\Model\FieldMaster;
use App\Model\FormGroup;

class FieldMasterService
{

	public  function getFieldMaster()
	{
		$formFields = FieldMaster::whereNull('custom')->orderBy('id', 'asc')->paginate(50);
		return $formFields;
	}

	public  function storeFieldMaster($request, $agencyId,$formId)
	{
		$customValue = null;
		if (empty($agencyId) && $formId) {
			$customValue = "Custom";
		} else {
			$customValue = $agencyId ? "Agency" : null;
		}
	
		$fieldMaster  = FieldMaster::updateOrCreate(['id' => $request->id ?? null], [
			'label' => $request->label,
			'type' => $request->type,
			'size' => $request->size,
			'set_character_limit' => $request->set_character_limit ? $request->set_character_limit : null,
			'options' => $request->option ? json_encode($request->option) : null,
			// 'custom' => $agencyId ? "Agency" : null,
			'custom' => $customValue,
			'show_in_portal' => $request->has('show_in_portal') ? 1 : 0,
		]);
	
		return $fieldMaster;
		
	}

	public  function storeAgencyMaster($fieldMaster, $agencyId, $formId,$form_group)
	{
		$maxOrder = AgencyMaster::where('form_id', $formId)->max('sort_id');
		return AgencyMaster::create([
			'field_id' => $fieldMaster->id,
			'agency_id' => $agencyId,
			'form_id' => $formId,
			'sort_id' => $maxOrder + 1,
			'form_group_id' => $form_group,
		]);
	}

	public  function storeAgencyMasterWithoutFormId($fieldMaster, $agencyId)
	{
		return AgencyMaster::create([
			'field_id' => $fieldMaster->id,
			'agency_id' => $agencyId,
		]);
	}
	public  function storeAgencyMasterWithoutAgencyId($fieldMaster, $formId,$form_group)
	{
		return AgencyMaster::create([
			'field_id' => $fieldMaster->id,
			'form_id' => $formId,
			'form_group_id' => $form_group,
		]);
	}

	public function storeAgencyField($agencyId, $fieldIds, $formId,$form_group_id)
	{
		$maxOrder = AgencyMaster::where('agency_id', $agencyId);
		if ($formId == NULL) {
			$maxOrder = $maxOrder->whereNull('form_id');
		} else {
			$maxOrder = $maxOrder->where('form_id', $formId);
		}
		$maxOrder = $maxOrder->max('sort_id');
		$insertedId=[];
		foreach ($fieldIds as $fieldId) {

			$data = [
				'agency_id' => $agencyId,
				'field_id' => $fieldId,
				'form_id' => !empty($formId) ? $formId : null,
				'sort_id' => $maxOrder + 1,
				'form_group_id'=>!empty($form_group_id) ? $form_group_id : null

			];
			$agencyMaster = new AgencyMaster($data);
			$agencyMaster->save();
			$insertedId[] = $agencyMaster->id;
		}

		return AgencyMaster::whereIn('id',$insertedId)->with('fields:id,custom,label,type,size,options,set_character_limit,show_in_portal')->with('formGroup:id,form_id,title')->orderBy('sort_id', 'ASC')->get();
	}

	public function getFieldById($id)
	{
		$data = FieldMaster::find($id);

		return $data;
	}

	public function updateField($id, $data)
	{
		$field = FieldMaster::find($id);
		$field->update([
			'label' => $data['label'],
			'type' => $data['type'],
			'size' => $data['size'],
			'set_character_limit' => $data['set_character_limit'],
			'options' => isset($data['option']) ? json_encode($data['option']) : null,
			'show_in_portal' => isset($data['show_in_portal']) ? 1 : 0,
		]);
	}

	public function deleteField($id)
	{
		$isUsedInAgency = AgencyMaster::where('field_id', $id)->exists();

		if ($isUsedInAgency) {
			return false;
		}

		return FieldMaster::where('id', $id)->delete();
	}

	public function totalRecord()
	{
		return FieldMaster::count();
	}

	public function getFormGroupName($form_group){
		return FormGroup::where('id',$form_group)->first();
	}

	public  function getAgencyStatusData()
	{
		return FieldMaster::select('id','options')->where('label','Agency Status')->where('type','select')->get()->toArray();
	}
}
