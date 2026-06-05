<?php

namespace App\Services;

use App\Model\NurseLanguage;

class NurseLanguageService
{

	public static function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new NurseLanguage($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}
	public static  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = NurseLanguage::where($where)->update($data);
		return $update;
	}
	public static function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = NurseLanguage::where($where)->update($data);
		return $update;
	}
}
