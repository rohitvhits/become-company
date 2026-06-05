<?php

namespace App\Http\Controllers;

use App\Model\Feedback;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\FeedbackService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserFeedbackFormController extends BaseController
{

    protected $feedbackService = '';


    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }


    public function feedbackForm(Request $request)
    {
        $patientId = $request->patient_id;
        $patient_id = decrypt($patientId);
        
        $ratingMaster = $this->feedbackService->getRatingMaster();

        $organizedRatings = [];

        foreach ($ratingMaster as $rating) {
            $organizedRatings[$rating->type][] = [
                'title' => $rating->title,
                'type' => $rating->type,
                'is_text' => $rating->is_text,
            ];
        }
        return view("userFeedback.userFeedbackForm", compact('organizedRatings', 'patient_id'));
    }

    public function feedbackFormStore(Request $request)
    {
        $validated = $request->validate([
            'rating.*' => 'required',
            'remark.*' => 'nullable'
        ]);

        $feedbackData = [];
        $totalRatings = 0;
        $ratingCount = 0;

        if (!empty($request->input('rating'))) {
            foreach ($request->input('rating') as $key => $ratings) {
                foreach ($ratings as $rating) {
                    $feedbackData['rating'][$key][] = $rating;
                    $totalRatings += $rating;
                    $ratingCount++;
                }
            }
        }
        if (!empty($request->input('remark'))) {
            foreach ($request->input('remark') as $key => $remarks) {
                foreach ($remarks as $remark) {
                    $feedbackData[$key][] = $remark;
                }
            }
        }

        $averageRating = $ratingCount > 0 ? $totalRatings / $ratingCount : 0;

        $serializedData = serialize($request->all());

        $this->feedbackService->storeFeedback($request->patient_id, $serializedData, $averageRating);

        Session::flash('success', 'Feedback saved successfully');
        return redirect('/feedback-thank-you');
    }

    function feedbackThankyou()
    {
        return view('userFeedback.feedback-thankyou');
    }
}
