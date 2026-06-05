<?php

namespace App\Http\Controllers;

use App\Http\Requests\RatingMasterRequest;
use App\Services\RatingMasterService;

class RatingMasterController extends Controller
{
    protected $ratingMasterService = '';
    function __construct(RatingMasterService $ratingMasterService)
    {
        $this->middleware('permission:rating-master-list|rating-master-create|rating-master-edit|rating-master-delete|rating-master-show', ['only' => ['index', 'store']]);
        $this->middleware('permission:rating-master-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:rating-master-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:rating-master-delete', ['only' => ['destroy']]);
        $this->middleware('permission:rating-master-show', ['only' => ['show']]);

        $this->middleware('auth');
        $this->ratingMasterService = $ratingMasterService;
    }

    public function index()
    {
        $ratingMaster = $this->ratingMasterService->getRatingMaster();

        return view('ratingMaster.index', compact('ratingMaster'));
    }

    public function create()
    {
        return view('ratingMaster._partial.create');
    }

    public function store(RatingMasterRequest $request)
    {
        $id = $request->id ?? null;

        $ratingMaster  = $this->ratingMasterService->storeRatingMaster($request);


        $idMsg = $id == null ? 'added' : 'updated';
        
        if ($ratingMaster) {
            return response()->json(['status' => true, 'msg' => 'Rating Master ' . $idMsg . ' successfully', 'data' => $ratingMaster]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.']);
        }
    }

    public function edit($id)
    {
        $rating = $this->ratingMasterService->getRatingById($id);
        return response()->json(['status' => true, 'msg' => 'Get Data', 'data' => $rating]);
    }

    public function destroy($id)
    {
        $this->ratingMasterService->deleteRating(array('id' => $id));
        
        $totalCount = $this->ratingMasterService->totalRecord();

        return response()->json(['status' => true, 'msg' => 'Rating Master successfully deleted', 'data' => $totalCount]);
    }
}
