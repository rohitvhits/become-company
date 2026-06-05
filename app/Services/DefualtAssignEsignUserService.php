<?php

namespace App\Services;

use App\Model\DefualtAssignEsignUser;
use Illuminate\Support\Facades\DB;

class DefualtAssignEsignUserService
{
	public static function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];

		$insert = new DefualtAssignEsignUser($data);
		$insert->save();

		return $insert->id;
	}

	public static function exists($userId)
	{
		
		return DefualtAssignEsignUser::where('user_id', $userId)
			->where('del_flag', 'N')->whereNull('deleted_at')
			->exists();
	}

	public static function getList()
	{
		return DefualtAssignEsignUser::select(
				'defualt_assign_esign_user.id',
				'defualt_assign_esign_user.user_id',
				'defualt_assign_esign_user.created_at',
				'users.first_name',
				'users.last_name',
				'users.email'
			)
			->leftJoin('users', 'users.id', '=', 'defualt_assign_esign_user.user_id')
			->where('defualt_assign_esign_user.del_flag', 'N')
			->orderBy('defualt_assign_esign_user.id', 'desc')
			->paginate(20);
	}

	public static function softDelete($id)
	{
		$auth = auth()->user();
		return DefualtAssignEsignUser::where('id', $id)->update([
			'deleted_at' => date('Y-m-d H:i:s'),
			'deleted_by' => $auth['id'],
			'del_flag' => 'Y',
		]);
	}
}
