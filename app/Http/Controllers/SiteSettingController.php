<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\SiteSettingServices;
use App\Helpers\Utility;
use App\User;


class SiteSettingController extends BaseController
{
    protected $siteSettingService="";
    public function __construct(SiteSettingServices $siteSettingService)
    {
        $this->middleware('permission:site-setting', ['only' => ['index', 'update']]);
        $this->middleware('auth');
        $this->siteSettingService = $siteSettingService;
    }

    public function index(Request $request)
    {
       
        $data['menu'] = "user";
        $data['user']= auth()->user();
		
        $data['query'] = $this->siteSettingService->getDetails();
        $data['statusData'] = Utility::getPatientStatusData();
        $data['nybest_users'] = User::getNYBestUserData();
        return view("sitesetting/site_setting", $data);
    }
    public function save(Request $request)
    {
        $update = $this->siteSettingService->saveOrUpdate($request->all());
        if($update){
            if($request->id ==''){
                return response()->json(['success' => true, 'data' => $update, 'error_msg' => 'Site Setting added successfully.'], 200);
            }else{
                return response()->json(['success' => true, 'data' =>$update, 'error_msg' => 'Site Setting updated successfully.'], 200);
            }
        }else{
            return response()->json(['success' => false, 'data' => array(), 'error_msg' => 'Sorry, something went wrong. Please try again.'], 500);
        }
    }
}
