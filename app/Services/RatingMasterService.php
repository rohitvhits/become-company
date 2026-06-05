<?php

namespace App\Services;

use App\Model\RatingMaster;

class RatingMasterService
{

	public  function getRatingMaster()
	{
		$rating = RatingMaster::orderBy('id', 'asc')->paginate(50);
		return $rating;
	}

	public  function storeRatingMaster($request)
	{
		$auth =auth()->user();
		$ratingMaster  = RatingMaster::updateOrCreate(['id' => $request->id ?? null], [
			'title' => $request->title,
			'type' => $request->type,
			'is_text' => $request->is_text ?? null,
			'created_by' => $auth['id'],
		]);

		return $ratingMaster;
	}

	public function getRatingById($id)
	{
		return RatingMaster::find($id);
	}

	public function deleteRating($id)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$update = RatingMaster::where($id)->update($data);
		return $update;
	}
	
	public function totalRecord()
	{
		return RatingMaster::count();
	}
}
