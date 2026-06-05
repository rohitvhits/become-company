<?php

namespace App\Services;

use App\Model\Feedback;
use App\Model\RatingMaster;

class FeedbackService
{
    public  function getRatingMaster()
	{
		$rating = RatingMaster::get();
		return $rating;
	}

	public function storeFeedback($patientId, $serializedData, $averageRating)
    {
        $feedback = new Feedback();
        $feedback->patient_id = $patientId;
        $feedback->response = $serializedData;
        $feedback->average_rating = $averageRating;
        $feedback->save();
        
        return $feedback;
    }

}   