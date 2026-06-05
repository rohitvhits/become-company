<?php

namespace App\Services;

use App\Model\FormGroup;

class FormGroupService
{

	public  function getData($form_id)
	{
		$data = FormGroup::where('form_id',$form_id)->orderBy('id', 'asc')->paginate(50);
		return $data;
	}
	public  function getFormGroupData($form_id)
	{
		$data = FormGroup::where('form_id',$form_id)->get();
		return $data;
	}

	public  function store($request)
	{
		$maxOrder = FormGroup::where('form_id', $request->form_id)->max('sort_id');
		$auth =auth()->user();
		$data  = FormGroup::updateOrCreate(['id' => $request->id ?? null], [
			'title' => $request->title,
			'form_id' => $request->form_id,
			'created_by' => $auth['id'],
			'sort_id' => $maxOrder + 1,
		]);
		return $data;
	}

	public function getDataById($id)
	{
		return FormGroup::find($id);
	}

	public function delete($id)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$update = FormGroup::where($id)->update($data);
		return $update;
	}
	
	public function totalRecord()
	{
		return FormGroup::count();
	}

	public  function storeFormGroupField($request)
	{
		$auth =auth()->user();
		$data  = FormGroup::updateOrCreate(['form_id' => $request->id ?? null], [
			'title' =>'Basic',
			'form_id' => $request->id,
			'sort_id' => '1',
			'created_by' => $auth['id'],
		]);

		return $data;
	}

	public function deleteFormGroup($id)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$update = FormGroup::where('form_id',$id)->update($data);
		return $update;
	}
}
