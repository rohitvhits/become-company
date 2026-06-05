<?php

namespace App\Http\Controllers;

use App\Helpers\LogsHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Services\ZipCodeService;

class ZipCodeController extends BaseController
{
    protected $zipCodeService;
    public function __construct(ZipCodeService $zipCodeService)
    {
        $this->middleware('auth');
        $this->middleware('permission:zipcode-list',['only' => ['index','ajaxList']]);
        $this->middleware('permission:zipcode-change-status',['only' => ['changeStatus']]);
        $this->zipCodeService = $zipCodeService;
    }

    /**
     * Display listing of Zip Code
     */
    public function index(Request $request)
    {
        $countyList = $this->zipCodeService->getAllCounty();
        return view('zipcode.index',compact('countyList'));
    }

    public function ajaxList(Request $request){
        $zipcode = $this->zipCodeService->getList($request->all(),$paginate = true);
        return view('zipcode.ajax_list', compact('zipcode'));
    }

    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $zipcode = $this->zipCodeService->getById($id);
        if (!$zipcode) {
            return response()->json([
                'status' => false,
                'error_msg' => 'Zip Code not found.'
            ], 404);
        }
        // Detach all users first
        $status = $request->status == 1 ? 0 : 1;
        $this->zipCodeService->update(['sms_status' => $status],['id' => $id]);
        $logData = array(
            'type' => 'Update Zip Code',
            'link' => url('setting/zipcode-master/status-update'),
            'module' => 'Zip Code',
            'object_id' => $id,
            'message' => auth()->user()->first_name .' '.auth()->user()->last_name . ' has updated sms status service',
            'old_response' => $zipcode,
            'new_response' => ['sms_status' => $status,'id'=>$id],
        );
        LogsHelper::handleLogs($logData);
        return response()->json([
            'status' => true,
            'error_msg' => 'Zip code status updated successfully.'
        ]);
    }
}
