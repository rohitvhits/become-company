<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Model\signatureUpload;

class SignatureUploadService
{
	function getAllDetails($login_id)
	{
		$auth = auth()->user();
		if(isset($auth->id)){
			
			$query = signatureUpload::select('id','file_upload','user_id','type','signature_name')->whereNotNull('user_id')->where('user_id',$auth->id)->where(function ($q) {
				$q->where('type', '!=', 'stamp')
				  ->orWhereNull('type');
			})->get();
			
		}else{
			$query = [];
		}
		
		return $query;
	}

	function getAllDetailsTypeWise($login_id,$type)
	{

		$query = signatureUpload::select('id','file_upload','user_id','type','signature_name')->whereNotNull('user_id')->where('user_id',$login_id)->where('type',$type)->get();
		return $query;
	}

	public function findById($id)
	{
		return SignatureUpload::find($id);
	}
}